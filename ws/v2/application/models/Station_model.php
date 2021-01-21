<?php
defined('BASEPATH') OR exit('No direct script access allowed');


use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class Station_model extends CI_Model {

    private $read_db, $write_db;

    function __construct() {
        parent::__construct();
        $this->read_db = $this->load->database('read', true);
        $this->write_db = $this->load->database('write', true);
    }

    public function station_list($post = []) {

        if (isset($post["offset"]) && !empty($post["offset"])) {
            $offset = $post["offset"] + 20;
        } else {
            $offset = 0;
        }

        //category wise
        if (isset($post["category"]) && !empty($post["category"])) {
            $category = " and station_categories.station_category_type_id IN (" . $post["category"] . ")";
        } else {
            $category = "";
        }

        //date wise
        if (isset($post["date"]) && !empty($post["date"])) {
            $ddate = date('Y-m-d', strtotime($post["date"]));
            $sdate = " stations.status = 1 and DATE(stations.user_favorite_stations) = '" . $ddate . "'";
        } else {
            $sdate = "";
        }

        //country wise
        if (isset($post["country"]) && !empty($post["country"])) {
            $country_iso2 = $this->read_db->select("iso2")->where("country_id", trim($post["country"]))->get("countries")->row_array();

            if(!empty($country_iso2["iso2"])) {
                $country = "stations.status = 1 and stations.country_iso2 = " . $country_iso2["iso2"] . "";
            } else {
                $country = "stations.status = 1";
            }
        } else {
            $country = "stations.status = 1";
        }

        //from my favourites
        if (isset($post["my_favourite"]) && $post["my_favourite"] == 1) {
            $my_favourite = " and user_favorite_stations.user_id = " . $post["user_id"] . " and user_favorite_stations.status = 1";
        } else {
            $my_favourite = "";
        }

        //language wise
        if (isset($post["language"]) && !empty($post["language"])) {
            $language = " and station_languages.language_id IN (" . $post["language"] . ")";
        } else {
            $language = "";
        }

        //genres wise
        if (isset($post["genres"]) && !empty($post["genres"])) {
            $genres = " and station_genres.genre_id IN (" . $post["genres"] . ")";
        } else {
            $genres = "";
        }

        //friends recommended
        if (isset($post["is_friend_recommended"]) && !empty($post["is_friend_recommended"])) {
            $is_friend_recommended = $post["is_friend_recommended"];
        } else {
            $is_friend_recommended = "";
        }

        //is station live
        if (isset($post["is_live"]) && !empty($post["is_live"]) && $post["is_live"] == 1) {
            $is_live = $post["is_live"];
            $station_shows_on_air = $this->get_on_air_talkshow();
        } else {
            $is_live = "";
        }

        //video chat
        if (isset($post["video_chat"]) && !empty($post["video_chat"])) {
            $video_chat = $post["video_chat"];
        } else {
            $video_chat = "";
        }

        if ((isset($post["latitude"]) && !empty($post["latitude"])) && (isset($post["longitude"]) && !empty($post["longitude"]))) {

            $this->read_db->select("stations.station_id, get_distance_metres(stations.latitude, stations.longitude, '" . $post["latitude"] . "','" . $post["longitude"] . "') AS distance", false);
        }
        else {
            $this->read_db->select("stations.station_id", false);
        }

        if (!empty($category)) {
            $this->read_db->join("station_categories", "station_categories.station_id = stations.station_id " . $category . "");
        }

        if (!empty($my_favourite)) {
            $this->read_db->join("user_favorite_stations", "user_favorite_stations.station_id = stations.station_id " . $my_favourite . "");
        }

        if (!empty($language)) {
            $this->read_db->join("station_languages", "station_languages.station_id = stations.station_id " . $language . "");
        }

        if (!empty($genres)) {
            $this->read_db->join("station_genres", "station_genres.station_id = stations.station_id " . $genres . "");
        }

        if (!empty($is_live)) {
            if(!empty($station_shows_on_air)) {
                $this->read_db->where("stations.station_id IN (".$station_shows_on_air.")");
                $this->read_db->where($country);
                $this->read_db->group_by("stations.station_id");
                $this->read_db->order_by('stations.name', 'asc');
                $this->read_db->limit(LIMIT);
                $this->read_db->offset($offset);
                $fetch = $this->read_db->get('stations')->result_array();
            }
            else {
                $fetch = array();
            }
        }
        else {
            $this->read_db->where($country);
            $this->read_db->group_by("stations.station_id");
            $this->read_db->order_by('stations.name', 'asc');
            $this->read_db->limit(LIMIT);
            $this->read_db->offset($offset);
            $fetch = $this->read_db->get('stations')->result_array();
        }

        if (!empty($fetch)) {

            foreach ($fetch as $key => $value) {
                $value2 = array_map(function($val) {
                    if (is_null($val)) {
                        $val = "";
                    }
                    return $val;
                }, $value);
                $fetch[$key] = $value2;

                $getposts = $this->station_list_details($value2["station_id"], $post);

                //print_r($getposts); exit;
                $fetch[$key] = array_merge($fetch[$key], $getposts);
            }
            //echo "<pre>"; print_r($fetch); exit;
            return $fetch;
        }

        return false;
    }

    function station_list_details($station_id, $post) {

        $value = $this->read_db->select("stations.*, u.username ", false)
                        ->join('users u','u.user_id = stations.user_id','left')
                        ->where("station_id", $station_id)
                        ->get("stations")->row_array();

        if (!empty($value)) {
            $fetch = $value;

            //location
            $location = array(
                "type" => "Point",
                "coordinates" => array(
                    '0' => $value["latitude"],
                    '1' => $value["longitude"],
                )
            );

            $fetch["location"] = $location;
            $fetch["description"] = strip_tags(trim($value["blurb"]));
            $fetch["headline"] = strip_tags(trim($value["slogan"]));
            $fetch["address"] = strip_tags(trim($value["address"]));
            $fetch["tags"] = strip_tags(trim($value["tags"]));

            //get username
            if (!empty($value["user_id"])) {
                $user = $this->read_db->select("username")->where('user_id', $value["user_id"])->get("users")->row_array();
                if (!empty($user)) {
                    $fetch["username"] = $user["username"];
                } else {
                    $fetch["username"] = '';
                }
            } else {
                $fetch["username"] = '';
            }

            if (!empty($value["country_iso2"])) {
                //get country name
                $country = $this->read_db->select("*")
                                ->where('iso2', $value["country_iso2"])
                                ->get('countries')->row_array();

                if (!empty($country)) {
                    $fetch["country"] = $country["name"];
                    $fetch["countryCode"] = $country["iso2"];
                    //$country["flag"] = $_ENV['FLAG_PATH'].strtolower($country["iso2"]).'_medium.png';
                    $country["flag"] = $_ENV['S3_PATH'] . $country['image_flag'] . $country["image_flag_presigned"];
                    unset($country['image_flag']);
                    unset($country['image_flag_presigned']);
                    $fetch["nationality"] = $country;
                } else {
                    $fetch["flag"] = "";
                    $fetch["country"] = "";
                    $fetch["countryCode"] = "";
                    $fetch["nationality"] = new stdClass();
                }
            } else {
                $fetch["flag"] = "";
                $fetch["country"] = "";
                $fetch["countryCode"] = "";
                $fetch["nationality"] = new stdClass();
            }

            //categories
            $categories = $this->read_db->select("c.*")
                ->join('station_category_types c', 'c.station_category_type_id =  sc.station_category_type_id')
                ->where('sc.status', 1)
                ->where('sc.station_id', $value["station_id"])
                ->get('station_categories sc')
                ->result_array();

            $fetch["categories"] = $categories;

            //genres
            $genres = $this->read_db->select("g.*")
                    ->join('genres g', 'g.genre_id =  sg.genre_id')
                    ->where('sg.status', 1)
                    ->where('sg.station_id', $value["station_id"])
                    ->get('station_genres sg')
                    ->result_array();

            $fetch["genres"] = $genres;

            //languages
            $languages = $this->read_db->select("l.*")
                ->join('languages l', 'l.language_id = sl.language_id')
                ->where('sl.status', 1)
                ->where('sl.station_id', $value["station_id"])
                ->get('station_languages sl')
                ->result_array();

            foreach($languages as $k => $v1){
                $v = array_map(function($val) {
                    if(is_null($val)) {
                    $val = "";
                    }
                    return $val;
                }, $v1);
                $languages[$k] = $v;
            }

            $fetch["languages"] = $languages;

            //stream
            $streams = $this->read_db->select("*")
                    ->where('status', 1)
                    ->where('station_id', $value["station_id"])
                    ->get('station_streams')
                    ->result_array();

            $fetch["streams"] = $streams;

            //favourite
            $favourite = $this->read_db->select("*")
                    ->where('status', 1)
                    ->where('user_id', $post["user_id"])
                    ->where('station_id', $value["station_id"])
                    ->get('user_favorite_stations')
                    ->row_array();
            if (!empty($favourite)) {
                $fetch["favourite"] = "1";
            } else {
                $fetch["favourite"] = "0";
            }

            //station favourite count
            $favourite_count = $this->read_db->select("station_id")->where('status', 1)->where('station_id', $value["station_id"])->get('user_favorite_stations')->num_rows();
            $fetch["favourite_count"] = (string) $favourite_count;

            //subscribe
            $subscribe = $this->read_db->select("*")
                    ->where('user_id', $post["user_id"])
                    ->where('station_id', $value["station_id"])
                    ->get('user_subscribe_stations')
                    ->row_array();
            if (!empty($subscribe)) {
                $fetch["is_subscribe"] = "1";
            } else {
                $fetch["is_subscribe"] = "0";
            }

            //station subscriber count
            $subscriber_count = $this->read_db->select("station_id")->where('status', 1)->where('station_id', $value["station_id"])->get('user_subscribe_stations')->num_rows();
            $fetch["subscriber_count"] = (string) $subscriber_count;

            return $fetch;
        }
    }

    function get_on_air_talkshow() {
        $trending_talkshow_on_air = $this->read_db->select("talkshow.station_id")
            ->join("talkshow_schedule", "talkshow.talkshow_id = talkshow_schedule.talkshow_id and talkshow_schedule.schedule_date = CURDATE()")
            ->where("talkshow.status", 1)
            ->get("talkshow")->result_array();

        if(!empty($trending_talkshow_on_air)) {
            $station_shows_on_air = implode(",", array_unique(array_column($trending_talkshow_on_air, "station_id")));
        }
        else {
            $station_shows_on_air = "";
        }

        return $station_shows_on_air;
    }

    public function get_station_profile($post = []) {

        /*$fetch2 = $this->db->select("station_unique_id as _id, stations.*")->where('station_id', $post["station_id"])->get('stations')->row_array();*/
        $fetch2 = $this->read_db->select("station_id as _id, stations.*, u.username")
                            ->join('users u','u.user_id = stations.user_id','left')
                            ->where('station_id', $post["station_id"])->get('stations')->row_array();

        if (!empty($fetch2) && $fetch2["status"] == '1') {

            $fetch2["related"] = new stdClass();

            $fetch1 = array_map(function($val) {
                if (is_null($val)) {
                    $val = "";
                }
                return $val;
            }, $fetch2);

            $getposts = $this->station_list_details($post["station_id"], $post);
            $fetch = array_merge($fetch1, $getposts);

            //hosts
            $station_hosts = $this->read_db->select("*", false)
                            ->where('station_id', $post["station_id"])
                            ->where('status', 1)
                            ->limit(10)
                            ->get('station_hosts')->result_array();
            if (!empty($station_hosts)) {
                $hosts = array();
                foreach ($station_hosts as $k => $v) {
                    $host_dt = $this->user_model->get_user_by_id($v["host_id"]);

                    if(!empty($host_dt)) {
                        $response22 = $this->user_model->check_connection_user($post["user_id"], $v["host_id"]);
                        $host_dt = array_merge($host_dt, $response22);
                        array_push($hosts, $host_dt);
                    }
                }
                $fetch["hosts"] = $hosts;
            } else {
                $fetch["hosts"] = array();
            }

            $post["from_station"] = 1;

            //tagged user
            $fetch["is_tagged"] = $this->check_is_tagged($post["station_id"], $post["user_id"], 3);
            $fetch["is_seen_tagged"] = $this->check_is_tagged_seen($post["station_id"], $post["user_id"], 3);

            //share count
            $fetch["station_share_count"] = $this->station_share_count($post["station_id"]);

            //talkshows
            $fetch["talkshows"] = $this->station_wise_talkshow_list($post);

            //recent_played_tracks
            $fetch["recent_played_tracks"] = $this->recent_played_tracks($post);

            //Top 50
            $fetch["top_50"] = $this->get_top_50_songs($post);

            //most_liked_tracks
            $fetch["most_liked_tracks"] = $this->most_liked_tracks($post);

            //popular_artist
            $fetch["popular_artist"] = $this->popular_artist($post);

            //favourite_count
            $fetch["favourite_count"] = (string) $this->read_db->select("user_favorite_station_id")
                            ->where('status', 1)
                            ->where('station_id', $post["station_id"])
                            ->get('user_favorite_stations')
                            ->num_rows();

            //echo "<pre>"; print_r($fetch); exit;
            return $fetch;
        }
    else if (!empty($fetch2) && $fetch2["status"] == '0') {
        return 1;
    }
    else {
        return 2;
    }

    }


    function check_is_tagged($group_id, $user_id, $type) {return 0;
        $is_tagged = $this->read_db->select("*")
                        ->where("chat_type", $type)
                        ->where("group_id", $group_id)
                        ->where("user_id", $user_id)
                        ->where("to_user_id is not null")
                        ->where("is_tagged", 1)
                        ->where("is_seen_tagged", 0)
                        ->get("chat")->num_rows();
        if ($is_tagged == 0) {
            return '1';
        } else {
            return '0';
        }
    }

    function check_is_tagged_seen($group_id, $user_id, $type) { return 0;
        $is_tagged = $this->read_db->select("*")
                        ->where("chat_type", $type)
                        ->where("group_id", $group_id)
                        ->where("user_id", $user_id)
                        ->where("is_tagged", 1)
                        ->where("is_private_share", 0)
                        ->get("chat")->row_array();

        // print_r($is_tagged); exit;
        if (!empty($is_tagged)) {
            if ($is_tagged["is_seen_tagged"] == 0) {
                return '1';
            } else if ($is_tagged["is_seen_tagged"] == 1) {
                return '0';
            }
        } else {
            return '0';
        }
    }

    public function get_top_50_songs($post = []) { return array();
        $get_top_50_songs = array();
        $get_top_502_songs = array();
        $limit = 50;
        if (isset($post["from_station"]) && $post["from_station"] == '1') {
            $get_top_50 = $this->read_db->select("*, count(`track_name`) as top_50")
                    ->where("station_id", $post["station_id"])
                    ->group_by("track_name")
                    ->order_by("top_50", "desc")
                    ->limit(3)
                    ->get("songs_played")
                    ->result_array();
        } else {
            $get_top_50 = $this->read_db->select("*, count(`track_name`) as top_50")
                    ->where("station_id", $post["station_id"])
                    ->group_by("track_name")
                    ->order_by("top_50", "desc")
                    ->limit($limit)
                    ->get("songs_played")
                    ->result_array();
        }

//  print_r($get_top_50); exit;
        if (!empty($get_top_50)) {
            $flag = 0;

            if (count($get_top_50) > $limit) {
                $flag = 0;
            } else {
                $flag = 1;
            }

            foreach ($get_top_50 as $key => $value) {
                $track_details = $this->track_detail(trim($value["track_name"]), $post["user_id"]);
                if (!empty($track_details)) {
                    $get_top_50[$key] = $track_details;
                    array_push($get_top_50_songs, $track_details);
                }
            }

//      print_r($get_top_50_songs); exit;
            if (count($get_top_50_songs) > $limit) {
                $top_50_arr = array_slice($get_top_50_songs, 0, $limit);
            } else if (count($get_top_50_songs) == $limit) {
                $top_50_arr = $get_top_50_songs;
            } else if (count($get_top_50_songs) < $limit && $flag == 1) {
                $get_top_502 = $this->read_db->select("*, count(`track_name`) as top_50")
                        ->where("station_id", $post["station_id"])
                        ->group_by("track_name")
                        ->order_by("top_50", "desc")
                        ->limit($limit)
                        ->offset($limit)
                        ->get("songs_played")
                        ->result_array();

                if (!empty($get_top_502)) {
                    foreach ($get_top_502 as $key => $value) {
                        $track_details = $this->track_detail(trim($value["track_name"]), $post["user_id"]);
                        if (!empty($track_details)) {
                            $get_top_502[$key] = $track_details;
                            array_push($get_top_502_songs, $track_details);
                        }
                    }

                    $less = $limit - count($get_top_50_songs);
                    $next_arr = array_slice($get_top_502_songs, 0, $less);
                    $get_top_50_songs = array_merge($get_top_50_songs, $next_arr);

//          print_r($get_top_50_songs); exit;
                    $top_50_arr = $get_top_50_songs;
                } else {
                    $top_50_arr = $get_top_50_songs;
                }
            } else {
                $top_50_arr = $get_top_50_songs;
            }
            return $top_50_arr;
        } else {
            return $get_top_50_songs;
        }
    }

    function station_share_count($station_id) {
        $count = $this->read_db->select("*")
                        ->where("station_id", $station_id)
                        ->group_by("user_message_id")
                        ->get("user_share_stations")->num_rows();
        return $count;
    }



    public function recent_played_tracks($post = []) { return array();
        $recent_played_tracks = array();

        if (!isset($post['offset']) || !$post['offset']) {
            $post['offset'] = '0';
        }

        $counter = $post["offset"] + LIMIT;

        //if ($counter <= 500) {
            $recent_tracks = $this->read_db->select("track_name, songs_played_id")
                    ->where('station_id', $post["station_id"])
                    ->group_by("track_name")
                    ->order_by('songs_played_id', 'desc')
                    ->limit(LIMIT)
                    ->offset($post["offset"])
                    ->get('songs_played')
                    ->result_array();

            if (!empty($recent_tracks)) {
                foreach ($recent_tracks as $key => $value) {
                    $track_details = $this->track_detail(trim($value["track_name"]), $post["user_id"]);
                    if (!empty($track_details)) {
                        $recent_tracks[$key] = $track_details;
                        array_push($recent_played_tracks, $track_details);
                    }
                }
                return $recent_played_tracks;
            } else {
                return array();
            }
//        } else {
//            $counter = 0;
//            return array();
//        }
    }

    public function most_liked_tracks($post = []) { return array();
        $most_liked_tracks = array();

        if (isset($post["from_station"]) && $post["from_station"] == 1) {
            $liked_tracks = $this->read_db->select("track_name, count(track_name) as total")
                    ->where('station_id', $post["station_id"])
                    ->group_by('track_name')
                    ->limit(3)
                    ->get('songs_played')
                    ->result_array();
        } else {
            $liked_tracks = $this->read_db->select("track_name, count(track_name) as total")
                    ->where('station_id', $post["station_id"])
                    ->group_by('track_name')
                    ->limit(LIMIT)
                    ->get('songs_played')
                    ->result_array();
        }

        if (!empty($liked_tracks)) {
            foreach ($liked_tracks as $key => $value) {
                $track_details = $this->track_detail(trim($value["track_name"]), $post["user_id"]);
                if (!empty($track_details)) {
                    $liked_tracks[$key] = $track_details;
                    array_push($most_liked_tracks, $track_details);
                }
            }
            return $most_liked_tracks;
        } else {
            return $most_liked_tracks;
        }
    }

    public function popular_artist($post = []) { return array();
        $fetch = $this->read_db->select("artist_name, count(artist_id) as popular_count")
                        ->where("station_id", $post["station_id"])
                        ->group_by('artist_name')
                        ->order_by('popular_count', 'desc')
                        ->limit(10)
                        ->get('songs_played')->result_array();
        $artist = array();
        if (!empty($fetch)) {
            foreach ($fetch as $key => $value) {
                $response = $this->read_db->select("*")->where('artist_name', $value["artist_name"])->get('artists')->row_array();
                if (!empty($response)) {
                    unset($response["area"]);
                    unset($response["aliases"]);
                    unset($response["tags"]);
                    unset($response["begin_area"]);

                    if (empty($response["image"])) {
                        $response["image"] = "";
                    }

                    $fetch[$key] = $response;

                    $response2 = array_map(function($val) {
                        if (is_null($val)) {
                            $val = "";
                        }
                        return $val;
                    }, $response);

                    $fetch[$key] = $response2;

                    $fetch[$key]["popular_count"] = $value["popular_count"];

                    //is_favourite
                    $favourite = $this->read_db->select("*")
                                    ->where('artist_id', $response2["id"])
                                    ->get('user_favourite_artist')->row_array();
                    if (!empty($favourite)) {

                        $fetch[$key]["is_favourite"] = "1";
                    } else {
                        $fetch[$key]["is_favourite"] = "0";
                    }

                    array_push($artist, $fetch[$key]);
                }
            }
            //print_r($fetch);
            return $artist;
        }
    }

    public function update_station($post = [], $files = []) {
        //print_r($post); print_r($files); exit;
        $notification_array = array();
        $user_array = array();
        $user_id = $post["user_id"];
        unset($post["user_id"]);

        //get station's user id
        $station_user = $this->read_db->select("user_id")->where("station_id", $post["station_id"])->get("stations")->row_array();

        if (isset($post["username"])) {
            $userdata = $this->get_user_by_id($station_user["user_id"]);
            if ($userdata["username"] != $post["username"]) {
                $post["user_id"] = $station_user["user_id"];
                $chk = $this->check_username_id($post);
                if (!empty($chk) && $chk == 4) {
                    return [1];
                }
            }
            $user_array["username"] = $post["username"];
            unset($post["username"]);
        }

        if (isset($post["name"])) {
            $user_array["name"] = $post["name"];
        }

        if (isset($post["phone"])) {
            $user_array["phone"] = $post["phone"];
        }

        if (isset($post["description"])) {
            $user_array["description"] = $post["description"];
        }

        if (isset($post["country"])) {
            $user_array["nationality"] = $post["country"];
        }

        if (isset($post["band"]) && !empty($post["band"])) {
            $post["band"] = strtoupper($post["band"]);
        }

        //bucket info
    //$credentials = new Aws\Credentials\Credentials(PUBLISHED_KEY, SECRET_KEY);
    $credentials = new Aws\Credentials\Credentials($_ENV['S3_KEY'], $_ENV['S3_SECRET']);
    $s3 = new S3Client([
        'region' => 'us-east-2',
        'version' => 'latest',
        "credentials" => $credentials
    ]);

    $ext_arr2 = array('gif', 'jpg', 'png', 'jpeg');

        //station image
        if (isset($files["image"]["name"])) {
            if (!empty($files["image"]["name"])) {
                $ext = '.' . pathinfo($files['image']['name'], PATHINFO_EXTENSION);
                $ext1 = pathinfo($files['image']['name'], PATHINFO_EXTENSION);
                $filename = date('YmdHis') . rand() . $post["station_id"] . strtolower($ext);
                $filepath = $files['image']['tmp_name'];
                $mime = mime_content_type($filepath);

                if( in_array($ext1, $ext_arr2) ) {
                    $result = $s3->putObject(array(
                        'Bucket' => $_ENV['BUCKET_NAME'],
                        'Key' => 'stations/'.$filename,
                        'SourceFile' => $filepath,
                        'ACL' => 'public-read',
                        'StorageClass' => 'STANDARD',
                        'ContentType'=>$mime
                    ));

                    $result2 = $s3->putObject(array(
                        'Bucket' => $_ENV['BUCKET_NAME'],
                        'Key' => 'stations/thumbs/'.$filename,
                        'SourceFile' => $filepath,
                        'ACL' => 'public-read',
                        'StorageClass' => 'STANDARD',
                        'ContentType'=>$mime
                    ));

                    $post["image"] = $this->image_url($filename, '', 'stations');
                    $user_array["profile_image"] = $filename;
                    $user_array["is_s3"] = 1;
                }
                else {
                    return 'The filtype you are trying to upload is not allowed';
                }
            }
            else {
                $post["image"] = "";
                $user_array["profile_image"] = "";
                $user_array["is_s3"] = 0;
            }
        }

        //station cover image
        if (isset($files["station_cover_image"]["name"])) {
            if (!empty($files["station_cover_image"]["name"])) {
                $ext = '.' . pathinfo($files['station_cover_image']['name'], PATHINFO_EXTENSION);
                $ext1 = pathinfo($files['station_cover_image']['name'], PATHINFO_EXTENSION);
                $filename = date('YmdHis') . rand() . $post["station_id"] . strtolower($ext);
                $filepath = $files['station_cover_image']['tmp_name'];
                $mime = mime_content_type($filepath);

                if( in_array($ext1, $ext_arr2) ) {
                    $result = $s3->putObject(array(
                        'Bucket' => $_ENV['BUCKET_NAME'],
                        'Key' => 'stations/'.$filename,
                        'SourceFile' => $filepath,
                        'ACL' => 'public-read',
                        'StorageClass' => 'STANDARD',
                        'ContentType'=>$mime
                    ));

                    $result2 = $s3->putObject(array(
                        'Bucket' => $_ENV['BUCKET_NAME'],
                        'Key' => 'stations/thumbs/'.$filename,
                        'SourceFile' => $filepath,
                        'ACL' => 'public-read',
                        'StorageClass' => 'STANDARD',
                        'ContentType'=>$mime
                    ));

                    $post["station_cover_image"] = $this->image_url($filename, '', 'stations');
                    $user_array["cover_image"] = $filename;
                    $user_array["is_s3"] = 1;
                }
                else {
                    return 'The filtype you are trying to upload is not allowed';
                }
            } else {
                $post["station_cover_image"] = "";
                $user_array["cover_image"] = "";
                $user_array["is_s3"] = 0;
            }
        }

        //update genres
        if (isset($post["genres"]) && !empty($post["genres"])) {
            $check_genres = $this->read_db->select("*")->where("station_id", $post["station_id"])->get("station_genres")->result_array();
            if (!empty($check_genres)) {
                $this->read_db->where("station_id", $post["station_id"])->delete("station_genres");
            }
            $genres = explode(',', $post["genres"]);
            foreach ($genres as $g) {
                $in_arr1 = array(
                    'station_id' => $post["station_id"],
                    'genres_id' => $g,
                );
                $this->write_db->insert('station_genres', $in_arr1);
            }
            unset($post["genres"]);
        } else if (empty($post["genres"])) {
            unset($post["genres"]);
        }

        //update categories
        if (isset($post["categories"]) && !empty($post["categories"])) {
            $check_categories = $this->read_db->select("*")->where("station_id", $post["station_id"])->get("station_categories")->result_array();
            if (!empty($check_categories)) {
                $this->read_db->where("station_id", $post["station_id"])->delete("station_categories");
            }
            $categories = explode(',', $post["categories"]);
            foreach ($categories as $c) {
                $in_arr2 = array(
                    'station_id' => $post["station_id"],
                    'category_id' => $c,
                );
                $this->write_db->insert('station_categories', $in_arr2);
            }
            unset($post["categories"]);
        } else if (empty($post["categories"])) {
            unset($post["categories"]);
        }

        //update languages
        if (isset($post["languages"]) && !empty($post["languages"])) {
            $check_languages = $this->read_db->select("*")->where("station_id", $post["station_id"])->get("station_languages")->result_array();
            if (!empty($check_languages)) {
                $this->read_db->where("station_id", $post["station_id"])->delete("station_languages");
            }
            $languages = explode(',', $post["languages"]);
            foreach ($languages as $l) {
                $in_arr3 = array(
                    'station_id' => $post["station_id"],
                    'languages_id' => $l,
                );
                $this->write_db->insert('station_languages', $in_arr3);
            }
            unset($post["languages"]);
        } else if (empty($post["languages"])) {
            unset($post["languages"]);
        }

        //update streams
        if (isset($post["streams"]) && !empty($post["streams"])) {
            //check if any stream extist for stations
            $check_streams = $this->read_db->select("*")->where("station_id", $post["station_id"])->get("station_stream")->result_array();
            if (!empty($check_streams)) {
                $this->read_db->where("station_id", $post["station_id"])->delete("station_stream");
            }
            //explode stream urls
            $streams = explode(',', $post["streams"]);
            foreach ($streams as $st) {
                //check if exist in main stream table
                $streams_list = $this->read_db->select("stream_id, url")->where("url", $st)->get("streams")->row_array();
                if (!empty($streams_list)) {
                    $in_arr4 = array(
                        'station_id' => $post["station_id"],
                        'stream_id' => $streams_list["stream_id"],
                    );
                    $this->write_db->insert('station_stream', $in_arr4);
                } else {
                    $in_arr5 = array(
                        "url" => $st
                    );
                    $this->write_db->insert('streams', $in_arr5);
                    $stream_id = $this->write_db->insert_id();
                    $in_arr6 = array(
                        'station_id' => $post["station_id"],
                        'stream_id' => $stream_id,
                    );
                    $this->write_db->insert('station_stream', $in_arr6);
                }
            }
            unset($post["streams"]);
        } else if (empty($post["streams"])) {
            unset($post["streams"]);
        }

        //update hosts
        if (isset($post["hosts"]) && !empty($post["hosts"])) {
            $hosts = explode(',', $post["hosts"]);
            $check_hosts = $this->read_db->select("*")->where("station_id", $post["station_id"])->get("station_hosts")->result_array();
            if (!empty($check_hosts)) {

                foreach ($check_hosts as $key => $value) {
                    if (!in_array($value["host_id"], $hosts)) {
                        $this->read_db->where("station_host_id", $value["station_host_id"])->delete("station_hosts");
                    } else {
                        $ky = array_search($value["host_id"], $hosts);
                        unset($hosts[$ky]);
                    }
                }
            }

            foreach ($hosts as $ht) {
                $in_arr7 = array(
                    'station_id' => $post["station_id"],
                    'host_id' => $ht,
                );
                $check = $this->read_db->select("*")->where($in_arr7)->get("station_hosts")->row_array();
                $userdata = $this->get_user_by_id($ht);
                $stationdata = $this->get_station_detail_id($post["station_id"]);

                //insert in notification
                $insert_notify = array(
                    'to_user_id' => (string) $ht,
                    'contact_person_id' => (string) $post["station_id"],
                    'station_id' => (string) $post["station_id"],
                    'notification_types' => "17",
                    'message' => '@' . $userdata["username"] . ' - ' . $userdata["name"] . ', you have been invited as host at station ' . $stationdata["name"] . '',
                );

//                $this->m_api->create_notification($insert_notify);
//                $this->m_notify->send($insert_notify);

                array_push($notification_array, $insert_notify);


                if (empty($check)) {
                    //insert in station host table
                    $this->write_db->insert("station_hosts", $in_arr7);
                }
            }
            unset($post["hosts"]);
        } else if (empty($post["hosts"])) {
            unset($post["hosts"]);
        }


        if (!empty($user_array)) {
            //update station user details
           // $this->db->set($user_array)->where("user_id", $station_user["user_id"])->update("user");
        }

        //print_r($post);
        //update station details
        $update = $this->write_db->set($post)->where("station_id", $post["station_id"])->update("stations");
       // echo $this->db->last_query();exit;
        if ($update) {
            return [2, $notification_array];
        } else {
            return [3];
        }
    }

    public function get_station_comments($post = []) { return array();
        if (empty($post["offset"])) {
            $post["offset"] = 0;
        }

        $msgs = array();
        $where = "deleted_at IS NULL and chat_type = 3 and is_private_share = 0";
        $response = $this->read_db->select("*")
                        ->where("group_id", $post["station_id"])
                        ->where("(user_id = " . $post["user_id"] . " OR to_user_id = " . $post["user_id"] . ")")
                        ->where($where)
                        ->limit(LIMIT)
                        ->offset($post["offset"])
                        ->group_by("message_id")
                        ->order_by('sent_at', 'desc')
                        ->get('chat')->result_array();

        if (!empty($response)) {
            foreach ($response as $key => $value1) {

                $msg11 = str_replace(PHP_EOL, "@/@", $value1["message"]);
                $value1["message"] = json_decode('"' . $msg11 . '"');
                $value1["message"] = str_replace("@/@", PHP_EOL, $value1["message"]);

                $value = array_map(function($val) {
                    if (is_null($val)) {
                        $val = "";
                    }
                    return $val;
                }, $value1);
                $response[$key] = $value;

                $msg = $this->get_station_message($value["chat_id"]);
                if (!empty($msg)) {
                    array_push($msgs, $msg);
                }
            }
            return $msgs;
        } else {
            return 1;
        }
    }

    function get_unread_tagged_station_comments($post = []) { return array();
        $where = "chat_type = 3 and user_type = 3 and is_tagged = 1 and is_seen_tagged = 0 and is_private_share = 0";
        $response = $this->read_db->select("*")
                        ->where("group_id", $post["station_id"])
                        ->where("user_id = " . $post["user_id"] . "")
                        ->where($where)
                        ->group_by("message_id")
                        ->get('chat')->num_rows();
        return $response;
    }

    public function get_station_message($chat_id) {
        $where = "deleted_at IS NULL and chat_type = 3 and is_private_share = 0";
        $response = $this->read_db->select("*")->where($where)->where("chat_id", $chat_id)->get("chat")->row_array();
        if (!empty($response)) {
            $user_id = $response["user_id"];
            $to_user_id = $response["to_user_id"];

            if ($response["type"] == "received") {
                $user_id = $response["to_user_id"];
                $to_user_id = $response["user_id"];
            }

            $response["station_id"] = $response["group_id"];
            $songss = $this->get_station_details($response["group_id"]);

            if (!empty($songss)) {
                $response["station"] = $songss;
            } else {
                $response["station"] = new stdClass();
            }

            //print_r(json_decode($response["tagged_users"]), true); exit;
            if (is_null($response["tagged_users"])) {
                $response["tagged_users"] = array();
            } else if (empty($response["tagged_users"])) {
                $response["tagged_users"] = array();
            } else {
                //print_r($response["tagged_users"] );
                $response["tagged_users"] = json_decode($response["tagged_users"], true);
                //print_r($response["tagged_users"]);
                if (!empty($response["tagged_users"])) {
                    foreach ($response["tagged_users"] as $k1 => $v1) {
                        $usernm = $this->get_user_username($v1["user_id"]);
                        if (!empty($usernm)) {
                            $response["tagged_users"][$k1]["username"] = $usernm["username"];
                        }

                        $userss = $this->get_user_by_id($v1["user_id"]);
                        if (!empty($userss)) {
                            if($userss["is_s3"] == '1'){
                                $response["tagged_users"][$k1]["profile_image"] = $this->pic_url($userss["profile_image"]);
                                $response["tagged_users"][$k1]["profile_image_thumb"] = $this->pic_url($userss["profile_image"], 'thumb');
                            }
                            else {
                                $response["tagged_users"][$k1]["profile_image"] = $this->pic_url2($userss["profile_image"]);
                                $response["tagged_users"][$k1]["profile_image_thumb"] = $this->pic_url2($userss["profile_image"], 'thumb');
                            }
                        }
                    }
                } else {
                    $response["tagged_users"] = array();
                }
            }
            $user = $this->get_user_details_without_block($user_id, $to_user_id);
            if ($user) {
                $response["sender"] = $user;
            } else {
                $response["sender"] = $this->get_station_details($user_id);
            }

            $user2 = $this->get_user_details_without_block($to_user_id, $user_id);
            if ($user2) {
                $response["receiver"] = $user2;
            } else {
                $response["receiver"] = $this->get_station_details($to_user_id);
            }

            $response = array_map(function($val) {
                if (is_null($val)) {
                    $val = "";
                }
                return $val;
            }, $response);

            return $response;
        }
    }

    public function recent_played_station($post = []) {
        $station_array = array();
        $related = array();
        $stations = $this->read_db->select("*")
                ->where('user_id', $post["user_id"])
                //->group_by('station_id')
                ->order_by('updated_at', 'desc')
                ->limit(20)
                ->get("user_play_stations")
                ->result_array();
        foreach ($stations as $key => $value) {
            $station_data2 = $this->read_db->select("*")->where('station_id', $value["station_id"])->get('stations')->row_array();

            $station_data = array_map(function($val) {
                if (is_null($val)) {
                    $val = "";
                }
                return $val;
            }, $station_data2);

            if (!empty($station_data["country_iso2"])) {
                //get country name
                $country = $this->read_db->select('*')
                                ->where('iso2', $station_data["country_iso2"])
                                ->get('countries')->row_array();

                if (!empty($country)) {
                    //$country["flag"] = $_ENV['FLAG_PATH'].strtolower($country["iso2"]).'_medium.png';
                    $country["flag"] = $_ENV['S3_PATH'] . $country['image_flag'] . $country["image_flag_presigned"];
                }

                $station_data["nationality"] = $country;
            } else {
                $station_data["nationality"] = new stdClass();
            }

            array_push($station_array, $station_data);
        }

        $stations1 = $station_array;
        return $stations1;
    }

    public function listeners($post = []) { return array();
        $fetch = array();
        $response = $this->read_db->select("user_id, name, email, username, profile_image, description, permit_tag as permission_tag, is_admin_verified, case permit_tag when 1 then 'Public' when 2 then 'Private' end as permission_label, is_online, cover_image, is_s3", false)
                        ->where('current_station_id', $post["station_id"])
                        ->order_by("updated_date", "desc")
                        ->get('users')->result_array();
        //print_r($response);
        if (!empty($response)) {
            foreach ($response as $key => $value) {
                $value2 = array_map(function($val) {
                    if (is_null($val)) {
                        $val = "";
                    }
                    return $val;
                }, $value);
                $response[$key] = $value2;

                if (!empty($value2["description"])) {
                    $response[$key]["description"] = strip_tags($value2["description"]);
                }

                if (!empty($value["profile_image"])) {
                    if($value["is_s3"] == '1') {
                        $response[$key]["profile_image"] = $this->pic_url($value["profile_image"]);
                        $response[$key]["profile_image_thumb"] = $this->pic_url($value["profile_image"], 'thumb');
                    }
                    else {
                        $response[$key]["profile_image"] = $this->pic_url2($value["profile_image"]);
                        $response[$key]["profile_image_thumb"] = $this->pic_url2($value["profile_image"], 'thumb');
                    }
                } else {
                    $response[$key]["profile_image_thumb"] = "";
                }

                if (!empty($value["cover_image"])) {
                    if($value["is_s3"] == '1') {
                        $response[$key]["cover_image"] = $this->pic_url($value["cover_image"]);
                        $response[$key]["cover_image_thumb"] = $this->pic_url($value["cover_image"], 'thumb');
                    }
                    else {
                        $response[$key]["cover_image"] = $this->pic_url2($value["cover_image"]);
                        $response[$key]["cover_image_thumb"] = $this->pic_url2($value["cover_image"], 'thumb');
                    }
                } else {
                    $response[$key]["cover_image_thumb"] = "";
                }

                $response22 = $this->user_model->check_connection_user($post["user_id"], $value2["user_id"]);
                $response[$key] = array_merge($response[$key], $response22);
            }

            $fetch["user_data"] = array_values($response);
            return $fetch;
        }
    }

    function pic_url($pic, $thumb = '', $folder = '') {
        if ($thumb) {
            if ($pic) {
                return $_ENV['S3_PATH'] . 'thumbs/' . $pic;
            }
        } else {
            if ($pic) {
                return $_ENV['S3_PATH'] . $pic;
            }
        }
    }

    function total_listener($post = []) { return "";
        //total listeners
        $tresponse = $this->read_db->select("user_id")
                        ->where('current_station_id', $post["station_id"])
                        ->get('user')->num_rows();

        return (string) $tresponse;
    }

    public function make_fav_unfav_station($post = []) {
        $in_arr = array(
            'user_id' => $post["user_id"],
            'station_id' => $post["station_id"],
        );

        //check station exists
        $exists = $this->read_db->select("user_favorite_station_id")
                        ->where($in_arr)
                        ->get('user_favorite_stations')->row_array();

        if ($post["is_fav"] == 1) {
            $is_fav = 1;
        }
        else if ($post["is_fav"] == 0) {
            $is_fav = 0;
        }


        if (!empty($exists)) {
            if ($post["is_fav"] == 0) {
                $this->write_db->where($in_arr)->delete("user_favorite_stations");
                $ret[0] = 2;
            }
            else if ($post["is_fav"] == 1) {
                $this->write_db->set('updated_at', date('Y-m-d H:i:s'))
                        ->set('updated_by', $post["user_id"])
                        ->where($in_arr)
                        ->update("user_favorite_stations");
                $ret[0] = 1;
            }
        }
        else {
            if ($post["is_fav"] == 1) {
                $in_arr = array(
                    'user_id' => $post["user_id"],
                    'station_id' => $post["station_id"],
                    'status' => 1,
                );
                $this->write_db->insert('user_favorite_stations', $in_arr);
                $ret[0] = 1;
            }
            else if ($post["is_fav"] == 0) {
                $ret[0] = 3;
            }
        }

        $unread = $this->read_db->where('station_id', $post['station_id'])->get('user_favorite_stations')->num_rows();
        $ret[1] = (string) $unread;
        return $ret;
    }

    public function favourite_station_list($post = []) {
        $fetch1 = $this->read_db->select("station_id")->where('status', 1)->where('user_id', $post["user_id"])->get('user_favorite_stations')->result_array();
        if (!empty($fetch1)) {
            foreach ($fetch1 as $key1 => $value1) {
                $value = array_map(function($val) {
                    if (is_null($val)) {
                        $val = "";
                    }
                    return $val;
                }, $value1);

                $getposts = $this->station_list_details($value["station_id"], $post);
                $fetch1[$key1] = array_merge($fetch1[$key1], $getposts);
            }
            return $fetch1;
        }
        return false;
    }

    public function station_played($post = []) {

        $checkit = $this->read_db->select("user_play_stations_id, count_plays")
                        ->where('station_id', $post["station_id"])
                        ->where('user_id', $post["user_id"])
                        ->get("user_play_stations")->row_array();

        if (empty($checkit)) {
            if(isset($post['country_id']) && !empty($post['country_id'])) {
                $countryiso2 = $this->read_db->select("iso2")->where("country_id", $post['country_id'])->get("countries")->row_array();
                $post["country_iso2"] = $countryiso2["iso2"];
            }
            else {
                $post["country_iso2"] = null;
                $post["country_id"] = null;
            }

            if(isset($post['latitude']) && !empty($post['latitude'])) {
                $post["latitude"] = $post["latitude"];
            }
            else {
                $post["latitude"] = null;
            }

            if(isset($post['longitude']) && !empty($post['longitude'])) {
                $post["longitude"] = $post["longitude"];
            }
            else {
                $post["longitude"] = null;
            }

            //insert in played station
            $insert_array2 = array(
                'user_id' => $post["user_id"],
                'station_id' => $post["station_id"],
                'latitude' => $post["latitude"],
                'longitude' => $post["longitude"],
                'country_id' => $post["country_id"],
                'country_iso2' => $post["country_iso2"],
                'count_plays' => '1'
            );
            $insert2 = $this->write_db->insert('user_play_stations', $insert_array2);

        }
        else {
            $count = $checkit["count_plays"] + 1;
            $insert2 = $this->write_db->set("count_plays", $count)
                                ->set("updated_at", date('Y-m-d H:i:s'))
                                ->set("updated_by", $post["user_id"])
                                ->where('user_play_stations_id', $checkit["user_play_stations_id"])
                                ->update("user_play_stations");
        }

        if ($insert2) {
            return true;
        } else {
            return false;
        }
    }

    public function station_by_geners($post=[]){
        $data = array();
        if($post["type_tag"] == '0'){
            $stations = $this->read_db->select("sg.*, g.name")
                    ->join("genres g", "sg.genre_id = g.genre_id")
                    ->where('sg.status', 1)
                    ->group_by('sg.genre_id')
                    ->order_by('sg.genre_id', 'asc')
                    ->get("station_genres sg")->result_array();

            $limit = 6;
            $offset = 0;
        }
        else if($post["type_tag"] == '1'){

            if(!isset($post["genres_id"]) || empty($post["genres_id"])) {
                return 2;
            }

            else {
                $stations = $this->read_db->select("sg.*, g.name")
                        ->join("genres g", "sg.genre_id = g.genre_id")
                        ->where('sg.genre_id', $post["genres_id"])
                        ->where('sg.status', 1)
                        ->group_by('sg.genre_id')
                        ->order_by('sg.genre_id', 'asc')
                        ->get("station_genres sg")->result_array();

                $limit = 20;
                $offset = $post["offset"];
            }
        }

        if(!empty($stations)){
            foreach($stations as $key => $value){
                $new_array = array();
                $genre_station = $this->read_db->select("sg.*")
                        ->where('sg.genre_id', $value["genre_id"])
                        ->join("stations s", "s.station_id = sg.station_id and s.state = ".$post["state_id"])
                        ->limit($limit)
                        ->offset($offset)
                        ->get("station_genres sg")->result_array();

                if(!empty($genre_station)) {
                    $st_new_arr = array();
                    foreach($genre_station as $k => $val){
                        $st_detail = $this->get_station_detail_id2($val["station_id"]);
                        array_push($st_new_arr, $st_detail);
                    }
                    $new_array["genre_name"] = $value["genres_name"];
                    $new_array["genre_id"] = $value["genre_id"];
                    $new_array["genre_data"] = $st_new_arr;
                    array_push($data, $new_array);
                }
//                else {
//                    $new_array["genre_name"] = $value["genres_name"];
//                    $new_array["genres_id"] = $value["genres_id"];
//                    $new_array["genre_data"] = array();
//                }
//                print_r($new_array); exit;

            }

//            print_r($data); exit;
            return $data;
        }
        else {
            return 1;
        }
    }

    public function is_claim_station($post = []) { return 1;
        $exist = $this->read_db->select("station_id, is_claimed")->where('station_id', $post["station_id"])->get('stations')->row_array();
        if (!empty($exist)) {
            if ($exist["is_claimed"] == $post["is_claim"]) {
                if ($post["is_claim"] == 1) {
                    return 1;
                } else if ($post["is_claim"] == 0) {
                    return 5;
                }
            } else {
                $this->write_db->set('is_claimed', $post["is_claim"])->where('station_id', $post["station_id"])->update('stations');
                if ($post["is_claim"] == 1) {
                    return 2;
                } else if ($post["is_claim"] == 0) {
                    return 3;
                }
            }
        } else {
            return 4;
        }
    }

    public function make_sub_unsub_station($post = []) {
        $exists = $this->read_db->select("*")
                        ->where('station_id', $post['station_id'])
                        ->where('user_id', $post['user_id'])
                        ->get('user_subscribe_stations')->row_array();

        if ($post["is_subscribe"] == 1) {
            if (!empty($exists)) {
                return 1;
            } else {
                //check if station is subscribing itself
                $check = $this->read_db->select("user_id")
                                ->where('station_id', $post['station_id'])
                                ->get("stations")->row_array();

                if ($check["user_id"] == $post["user_id"]) {
                    return 5;
                } /*else if ($check["requested_user_id"] == $post["user_id"]) {
                    return 5;
                }*/ else {
                    $subscribe_array = array(
                        'station_id' => $post['station_id'],
                        'user_id' => $post['user_id'],
                    );
                    $this->write_db->insert('user_subscribe_stations', $subscribe_array);
                    return 2;
                }
            }
        } else if ($post["is_subscribe"] == 0) {
            if (!empty($exists)) {
                $subscribe_array = array(
                    'station_id' => $post['station_id'],
                    'user_id' => $post['user_id'],
                );

                $this->write_db->where($subscribe_array)->delete('user_subscribe_stations');
                return 3;
            } else {
                return 4;
            }
        }
    }

    public function station_subscriber_list($post = []) {
        $subscriber = array();
        //$where = "user_id NOT IN (" . $post["user_id"] . ")";
        $fetch = $this->read_db->select("user_id")
                        ->where('station_id', $post["station_id"])
                        //->where($where)
                        ->get('user_subscribe_stations')->result_array();
        //print_r($fetch); exit;
        if (!empty($fetch)) {

            //check if station is subscribing itself
            $check = $this->read_db->select("user_id")
                            ->where('station_id', $post['station_id'])
                            ->get("stations")->row_array();

            foreach ($fetch as $key => $value) {

               /* if ($check["requested_user_id"] == $value["user_id"]) {
                    unset($fetch[$key]);
                } else*/ if ($check["user_id"] == $value["user_id"]) {
                    unset($fetch[$key]);
                } else {
                    if (isset($post["chr"]) && !empty($post["chr"])) {
                        $where2 = "u.user_id = " . $value["user_id"] . " and name LIKE '%" . trim($post["chr"]) . "%'";
                    } else {
                        $where2 = "u.user_id = " . $value["user_id"] . "";
                    }
                    /*u.user_id, name, email, username, description, image_profile, permit_tag as permission_tag, is_admin_verified, case permit_tag when 1 then 'Public' when 2 then 'Private' end as permission_label, is_online, cover_image, is_s3*/

                    $response = $this->read_db->select("u.user_id, name, email, username, image_profile,is_live, blurb, image_cover", false)
                    ->join('users u', 'u.user_id = fans.user_id')
                                    ->where($where2)
                                    ->get('fans')->row_array();
                    if (!empty($response)) {
                        if (!empty($response["image_profile"])) {
                            $img = $response["image_profile"];
                            /*if($response["is_s3"] == '1') {
                                $response["image_profile"] = $this->pic_url($img);
                                $response["profile_image_thumb"] = $this->pic_url($img, 'thumb');
                            }
                            else {
                                $response["image_profile"] = $this->pic_url2($img);
                                $response["profile_image_thumb"] = $this->pic_url2($img, 'thumb');
                            }*/
                        } else {
                            $response["image_profile"] = "";
                            $response["profile_image_thumb"] = "";
                        }

                        if (!empty($response["image_cover"])) {
                            $img = $response["image_cover"];

                            if($response["is_s3"] == '1') {
                                $response["image_cover"] = $this->pic_url($img);
                                $response["cover_image_thumb"] = $this->pic_url($img, 'thumb');
                            }
                            else {
                                $response["image_cover"] = $this->pic_url2($img);
                                $response["cover_image_thumb"] = $this->pic_url2($img, 'thumb');
                            }
                        } else {
                            $response["image_cover"] = "";
                            $response["cover_image_thumb"] = "";
                        }

                        $response["image_profile"] = "";
                        $response["profile_image_thumb"] = "";
                        $response["image_cover"] = "";
                        $response["cover_image_thumb"] = "";

                        $response22 = $this->user_model->check_connection_user($post["user_id"], $value["user_id"]);
                        $response = array_merge($response, $response22);

                        array_push($subscriber, $response);
                    }
                }
            }
            //print_r($fetch);
            return $subscriber;
        }
    }

    public function share_station($post = []) { return true;
        $user_ids = explode(",", $post["ids"]);
        unset($post["ids"]);
        $userdata = $this->get_user_by_id($post["user_id"]);
        $stationdata = $this->get_station_detail_id2($post["station_id"]);
        //print_r($stationdata); exit;
        $msg = '@' . $userdata["username"] . ' - ' . $userdata["name"] . ', has shared ' . $stationdata["name"] . '';

        foreach ($user_ids as $ids) {
            $check = $this->read_db->select("*")
                            ->where("user_id", $ids)
                            ->where("station_id", $post["station_id"])
                            ->get("station_share")->row_array();

            if (!empty($post["comment"])) {
                $description = $post["comment"];
            } else {
                $description = $msg;
            }

            //insert in chat table
            $dat = substr(date('YmdHisu'), 0, -3);
            $ins_chat = array(
                'message_id' => $post["user_id"] . '-' . $dat,
                'offline_id' => '',
                'message' => $description,
                'document' => '',
                'document_thumb' => '',
                'location' => '',
                'msg_type' => 'text',
                'sent_at' => date('Y-m-d H:i:s'),
                'delivered_at' => null,
                'seen_at' => null,
                'deleted_at' => null,
                'edited_at' => null,
                'chat_type' => 3,
                'user_type' => 3,
                'tagged_users' => null,
                'song_name' => null,
                'artist_id' => null,
                'is_shared' => 1,
                'is_shared_type' => 5,
                "group_id" => $post["station_id"],
                "comment" => $description
            );

            $send_chat = array(
                'user_id' => $post["user_id"],
                'to_user_id' => $ids,
                'type' => 'sent',
            );

            $received_chat = array(
                'user_id' => $ids,
                'to_user_id' => $post["user_id"],
                'type' => 'received',
            );

            if (isset($post["is_prv_share"]) && $post["is_prv_share"] == '1') {
                $ins_chat = array_merge($ins_chat, array('is_private_share' => 1));
            }

//      print_r($ins_chat);

            $this->write_db->insert("chat", array_merge($ins_chat, $send_chat));
            $this->write_db->insert("chat", array_merge($ins_chat, $received_chat));

            $arr = array(
                "user_id" => $ids,
                "station_id" => $post["station_id"],
                "comment" => $description,
            );

            if (empty($check)) {
                //insert in share table
                $insert = $this->write_db->insert("station_share", $arr);

                if ($insert) {
                    //insert in notification
                    $insert_notify = array(
                        'to_user_id' => (string) $ids,
                        'contact_person_id' => (string) $post["station_id"],
                        'station_id' => (string) $post["station_id"],
                        'notification_types' => "23",
                        'message' => $msg,
                        "description" => $description,
                    );

                    $this->m_api->create_notification($insert_notify);
                    $this->m_notify->send($insert_notify);
                }
            } else {
                $this->write_db->set($arr)->where("station_id", $post["station_id"])->where("user_id", $ids)->update("station_share");

                //insert in notification
                $insert_notify1 = array(
                    'to_user_id' => (string) $ids,
                    'contact_person_id' => (string) $post["station_id"],
                    'station_id' => (string) $post["station_id"],
                    'notification_types' => "23",
                    'message' => $msg,
                    "description" => $description,
                );

                $this->m_api->create_notification($insert_notify1);
                $this->m_notify->send($insert_notify1);
            }
        }

        return true;
    }

    public function search_station($post = []) {

        if(isset($post["chr"]) && $post["chr"]!=""){
            $where2 = "name LIKE '" . $post["chr"] . "%'";
            $this->read_db->where($where2);
        }

        $station2 = $this->read_db->select("*", false)
                ->order_by('name', 'asc')
                ->get('stations')
                ->result_array();

        foreach ($station2 as $key => $value2) {
            $value = array_map(function($val) {
                if (is_null($val)) {
                    $val = "";
                }
                return $val;
            }, $value2);
        }
        return $station2;
    }

    public function station_wise_talkshow_list($post = []) {

        if (isset($post["person_id"]) && !empty($post["person_id"])) {
            $user_id = $post["person_id"];
        } else {
            $user_id = $post["user_id"];
        }

        if (!empty($post["search_date"])) {
            $where = "((DATE(on_air_date) = DATE_FORMAT('" . $post["search_date"] . "', '%Y-%m-%d') and show_schedules.schedule_date  = DATE_FORMAT('" . $post["search_date"] . "', '%Y-%m-%d')) OR show_schedules.schedule_date  = DATE_FORMAT('" . $post["search_date"] . "', '%Y-%m-%d'))";

            $fetch = $this->read_db->select("shows.show_id, stations.user_id", false)
                            ->join('stations', 'stations.station_id = shows.station_id')
                            ->join('show_schedules', 'show_schedules.show_id = shows.show_id')
                            ->where('shows.station_id', $post["station_id"])
                            ->where($where)
                            ->where("shows.status", 1)
                            ->group_by("shows.show_id")
                            ->get('shows')->result_array();

            if (!empty($fetch)) {
                foreach ($fetch as $key => $value2) {
                    $value = array_map(function($val) {
                        return $val === NULL ? "" : $val;
                    }, $value2);

                    $fetch[$key] = $value;

                    $fetch[$key] = $this->show_model->get_talkshow_details($value["talkshow_id"], $user_id, $post["search_date"]);
                    $fetch[$key]["station_user"] = $value["user_id"];
                }
                //print_r($fetch);
                return $fetch;
            } else {
                return array();
            }
        } else {
            $fetch = $this->read_db->select("shows.show_id, stations.user_id")
                            ->join('stations', 'stations.station_id = shows.station_id')
                            ->where('shows.station_id', $post["station_id"])
                            ->where("shows.status", 1)
                            ->get('shows')->result_array(); //echo $this->db->last_query();exit;
            if (!empty($fetch)) {
                foreach ($fetch as $key => $value2) {
                    $value = array_map(function($val) {
                        return $val === NULL ? "" : $val;
                    }, $value2);

                    $fetch[$key] = $value;

                    $fetch[$key] = $this->show_model->get_talkshow_details($value["show_id"], $user_id);
                    $fetch[$key]["station_user"] = $value["user_id"];
                }
                //print_r($fetch);
                return $fetch;
            } else {
                return array();
            }
        }
    }

    public function add_host_to_station($post = []) {
        
        $insert_host = array(
            'user_id' => $post["host_id"],
            'station_id' => $post["station_id"]
        );

        $check = $this->read_db->select("*")->where($insert_host)->get("station_hosts")->row_array();

        $userdata = $this->user_model->get_user_by_id($post["host_id"]);
        $stationdata = $this->get_station_profile($post);

        //insert in notification
        $insert_notify = array(
            'to_user_id' => $post["host_id"],
            'contact_person_id' => $post["station_id"],
            'station_id' => $post["station_id"],
            'notification_types' => "17",
            'message' => '@' . $userdata["username"] . ' - ' . $userdata["name"] . ', you have been invited as host at station ' . $stationdata["name"],
        );

        if (empty($check)) {
            //$this->notification_model->create_notification($insert_notify);
           // $this->notification_model->send($insert_notify);
            //insert in station host table
            $this->write_db->insert("station_hosts", $insert_host);
            return 1;
        } else if (!empty($check)) {
            if ($check["status"] == 2) {
                $this->write_db->set("status", 0)->where($insert_host)->update("station_hosts");
                //$this->notification_model->create_notification($insert_notify);
                return 1;
            } else if ($check["status"] == 1) {
                return 2;
            } else if ($check["status"] == 0) {
                //$this->notification_model->create_notification($insert_notify);
                return 1;
            }
        } else {
            return false;
        }
    }

    public function get_host_list($post = []) {
        $host_arr = array();
        /*
          //general user  array
          $fetch1 = $this->db->select("user_id, email, user_type_id")
          ->where('status', 1)
          ->where("user_type_id", 1)
          ->get('user')->result_array();

          //artist user array
          $fetch2 = $this->db->select("user_id, email, user_type_id")
          ->where('status', 1)
          ->where("user_type_id", 3)
          ->get('user')->result_array();

          //get Multidimensional associative array intersection
          $fetch3 = $this->recursive_array_intersect_key($fetch1, $fetch2);

          //merge both array
          $fetch4 = array_merge($fetch1, $fetch2);

          //find the difference between two multidimensional array
          $new_array = array_map('unserialize', array_diff(array_map('serialize', $fetch4), array_map('serialize', $fetch3)));

          //print_r($new_array); exit;
          if(!empty($new_array)){
          $arr = implode(",", array_column($new_array, "user_id"));
         *
         */

        if (isset($post["chr"]) && !empty($post["chr"])) {
            $user_data = $this->read_db->select("u.user_id, name, username, u.user_type_id, image_profile, u.email, blurb", false)
                    ->join('fans f', 'f.user_id = u.user_id')
                    ->where("u.name like '" . $post["chr"] . "%'")
                    ->where("u.user_id != " . $post["user_id"])
                    ->where('u.user_type_id IN (1, 3)')
                    ->where("u.status", 1)
                    ->where("u.username is not null")
                    ->limit($post['limit'])
                    ->offset($post['offset'])
                    ->order_by("u.username", "asc")
                    ->get('users u')
                    ->result_array();
        } else {
            $user_data = $this->read_db->select("u.user_id, name, username, u.user_type_id, image_profile, u.email, blurb", false)
                    ->join('fans f', 'f.user_id = u.user_id')
                    ->where("u.user_id != " . $post["user_id"])
                    ->where('u.user_type_id IN (1, 3)')
                    ->where("u.status", 1)
                    ->where("u.username is not null")
                    ->limit($post['limit'])
                    ->offset($post['offset'])
                    ->order_by("u.username", "asc")
                    ->get('users u')
                    ->result_array();
        }

        foreach ($user_data as $key => $value1) {

            $value = array_map(function($val) {
                return $val === NULL ? "" : $val;
            }, $value1);

            if (!empty($value["profile_image"])) {
                $img = $value["profile_image"];

                if($value["is_s3"] == '1'){
                    $user_data[$key]["profile_image"] = $this->pic_url($img);
                    $user_data[$key]["profile_image_thumb"] = $this->pic_url($img, 'thumb');
                }
                else {
                    $user_data[$key]["profile_image"] = $this->pic_url2($img);
                    $user_data[$key]["profile_image_thumb"] = $this->pic_url2($img, 'thumb');
                }
            } else {
                $user_data[$key]["profile_image"] = "";
                $user_data[$key]["profile_image_thumb"] = "";
            }

            $response22 = $this->user_model->check_connection_user($post["user_id"], $value["user_id"]);
            $user_data[$key] = array_merge($user_data[$key], $response22);

            if (!empty($host_arr)) {
                $host_arr = array_values(array_unique(array_column($host_arr, 'user_id')));
            }
            if (in_array($value["username"], $host_arr)) {
                unset($user_data[$key]);
            }
        }

        return array_values($user_data);
        //}
    }

    public function get_station_details($user_id) {
        $response = $this->read_db->select("station_id, name, image_profile, image_cover, slogan, is_live, freq_band")->where("station_id", $user_id)->get("stations")->row_array();
        if (!empty($response)) {
            if (empty($response["image_profile"])) {
                $response["image_profile"] = "";
            }

            $response["headline"] = strip_tags($response["headline"]);

            $response["i_hve_blocked"] = "0";
            $response["is_blocked_me"] = "0";
            //$response["last_seen"] = $response["date"];

            $response = array_map(function($val) {
                if (is_null($val)) {
                    $val = "";
                }
                return $val;
            }, $response);
            return $response;
        } else {
            return new stdClass();
        }
    }


    public function accept_reject_host_to_station($post = []) {
        $where = array(
            'user_id' => $post["user_id"],
            'station_id' => $post["station_id"]
        );

        $check = $this->read_db->select("*")->where($where)->get("station_hosts")->row_array(); 

        if (!empty($check)) {
            $userdata = $this->user_model->get_user_by_id($post["user_id"]);
            $stationdata = $this->get_station_details($post["station_id"]);

            if ($post["status"] == 2) {
                //insert in notification
               /* $insert_notify = array(
                    'to_user_id' => $stationdata["requested_user_id"],
                    'contact_person_id' => $post["user_id"],
                    'station_id' => $post["station_id"],
                    'notification_types' => "18",
                    'message' => '@' . $userdata["username"] . ' - ' . $userdata["name"] . ', has accepted your invite as host at station ' . $stationdata["name"],
                );
                $this->create_notification($insert_notify);

                $this->db->where("to_user_id", $post["user_id"])
                        ->where("contact_person_id", $post["station_id"])
                        ->where("station_id", $post["station_id"])
                        ->where("notification_type", "17")
                        ->delete('notification');

                $this->m_notify->send($insert_notify);*/
                $this->write_db->set("status", $post["status"])->where($where)->update("station_hosts");
                return 1;
            } else if ($post["status"] == 3) {
                //insert in notification
                /*$insert_notify = array(
                    'to_user_id' => $stationdata["requested_user_id"],
                    'contact_person_id' => $post["user_id"],
                    'station_id' => $post["station_id"],
                    'notification_types' => "19",
                    'message' => '@' . $userdata["username"] . ' - ' . $userdata["name"] . ', has rejected your invite as host at station ' . $stationdata["name"],
                );
                $this->create_notification($insert_notify);

                $this->db->where("to_user_id", $post["user_id"])
                        ->where("contact_person_id", $post["station_id"])
                        ->where("station_id", $post["station_id"])
                        ->where("notification_type", "17")
                        ->delete('notification');

                $this->m_notify->send($insert_notify);*/
                $this->write_db->where($where)->delete("station_hosts");
                return 2;
            }
        } else {
            return false;
        }
    }

    public function listener_common_connection($post = []) {

        $where = "connect_user_id = " . $post["user_id"] . " OR user_id = " . $post["user_id"] . "";
        $connections = $this->read_db->select("*")->where($where)->where('action', 1)->get('user_connections')->result_array();
        $fetch = array();

        if (!empty($connections)) {
            $send_user_arr = array_column($connections, 'user_id');
            $send_user_arr2 = array_column($connections, 'connect_user_id');
            $send_user_arr_list = implode(',', array_unique(array_merge($send_user_arr, $send_user_arr2)));

            $where2 = "u.user_id IN (" . $send_user_arr_list . ") and u.user_id != " . $post["user_id"] . "";
            $response = $this->read_db->select("u.user_id, name, email, username, image_profile, blurb, is_live, image_cover", false)
                            ->join('fans f','f.user_id = u.user_id')
                            ->where($where2)
                            //->where('current_station_id', $post["station_id"])
                            ->get('users u')->result_array();

            if (!empty($response)) {
                foreach ($response as $k => $value) {
                    $value2 = array_map(function($val) {
                        if (is_null($val)) {
                            $val = "";
                        }
                        return $val;
                    }, $value);
                    $response[$k] = $value2;

                    if (!empty($value2["description"])) {
                        $response[$k]["description"] = strip_tags($value2["description"]);
                    }

                    if (!empty($value["profile_image"])) {
                        if($value["is_s3"] == '1') {
                            $response[$k]["profile_image"] = $this->pic_url($value["profile_image"]);
                            $response[$k]["profile_image_thumb"] = $this->pic_url($value["profile_image"], 'thumb');
                        }
                        else {
                            $response[$k]["profile_image"] = $this->pic_url2($value["profile_image"]);
                            $response[$k]["profile_image_thumb"] = $this->pic_url2($value["profile_image"], 'thumb');
                        }
                    } else {
                        $response[$k]["profile_image_thumb"] = "";
                    }

                    if (!empty($value["cover_image"])) {
                        if($value["is_s3"] == '1') {
                            $response[$k]["cover_image"] = $this->pic_url($value["cover_image"]);
                            $response[$k]["cover_image_thumb"] = $this->pic_url($value["cover_image"], 'thumb');
                        }
                        else {
                            $response[$k]["cover_image"] = $this->pic_url2($value["cover_image"]);
                            $response[$k]["cover_image_thumb"] = $this->pic_url2($value["cover_image"], 'thumb');
                        }
                    } else {
                        $response[$k]["cover_image_thumb"] = "";
                    }

                    $response22 = $this->user_model->check_connection_user($post["user_id"], $value2["user_id"]);
                    $response[$k] = array_merge($response[$k], $response22);
                }

                $fetch["user_data"] = array_values($response);
                $fetch["total_listeners"] = (string) count($response);
                return $fetch;
            }
        }
    }

    public function recommended_stations($post = []) {
        if (isset($post["offset"]) && !empty($post["offset"])) {
            $offset = $post["offset"];
        } else {
            $offset = 0;
        }

        if (isset($post["chr"]) && !empty($post["chr"])) {
            $where2 = "status = 1 and name LIKE '%" . $post["chr"] . "%'";
        } else {
            $where2 = "status = 1";
        }

        $where22 = "(user_id = " . $post["user_id"] . " OR connect_user_id = " . $post["user_id"] . ")";
        $connection_list = $this->read_db->select("*")->where($where22)->where('action', 1)->get('user_connections')->result_array();

        if (!empty($connection_list)) {
            $friends_id_list = array();
            foreach ($connection_list as $key5 => $value5) {
                if ($value5["user_id"] == $post["user_id"]) {
                    $friend_id = $value5["connect_user_id"];
                } else if ($value5["connect_user_id"] == $post["user_id"]) {
                    $friend_id = $value5["user_id"];
                }
                array_push($friends_id_list, $friend_id);
            }

            $friends_list = implode(',', $friends_id_list);
            $where222 = "sp.user_id IN (" . $friends_list . ")";

            if(isset($post["country_id"])) {
                $friend_liked_station = $this->read_db->select("sp.station_id, count(sp.station_id) as liked_stations")
                                ->join("stations t", "t.station_id = sp.station_id and t.country = " . trim($post["country_id"]))
                                ->where($where222)
                                ->group_by("sp.station_id")
                                ->order_by("liked_stations", "desc")
                                ->limit(10)
                                ->get("user_favorite_stations sp")->result_array();

                $mostly_listen_station = $this->read_db->select("sp.station_id as station_id, count(sp.station_id) as liked_stations")
                       // ->join("stations t", "t.station_id = sp.station_id and t.country = " . trim($post["country_id"]))
                        ->join("stations t", "t.station_id = sp.station_id")
                        ->group_by("sp.station_id")
                        ->order_by("liked_stations", "desc")
                        ->limit(10)
                        ->get("user_play_stations sp")->result_array();
            }
            else {
                $friend_liked_station = $this->read_db->select("sp.station_id, count(sp.station_id) as liked_stations")
                            ->where($where222)
                            ->group_by("sp.station_id")
                            ->order_by("liked_stations", "desc")
                            ->limit(10)
                            ->get("user_favorite_stations sp")->result_array();

                $mostly_listen_station = array();
            }

            $stationids = array_merge($friend_liked_station, $mostly_listen_station);
            if (!empty($stationids)) {

                usort($stationids, function($a, $b) {
                    return $a['liked_stations'] < $b['liked_stations'];
                });

//                print_r($stationids); exit;
                $recommended_St = array();
                foreach ($stationids as $key => $value) {
                    $station_data = $this->read_db->select("station_id, image_profile, name, slogan, blurb, requested_user_id,  country_iso2", false)
                                    ->where('station_id', $value["station_id"])
                                    ->where($where2)
                                    ->get("stations")->row_array();

                    if (!empty($station_data)) {
                        if (!empty($station_data["headline"])) {
                            $station_data["headline"] = strip_tags($station_data["headline"]);
                        }

                        if (!empty($station_data["description"])) {
                            $station_data["description"] = strip_tags($station_data["description"]);
                        }

                        if (!empty($station_data["country"])) {
                            //get country name
                            $country = $this->read_db->select("country.*, timezone.zone")
                                            ->join("timezone", "timezone.country_code = country.code2l")
                                            ->where('country.id', $station_data["country"])
                                            ->group_by("country.code2l")
                                            ->get('country')->row_array();

                            if (!empty($country)) {
                                $station_data["country"] = $country["name"];
                                $station_data["countryCode"] = $country["code2l"];
                                //$country["flag"] = $country["flag_medium"];
                                $country["flag"] = $_ENV['S3_PATH'] . $country['image_flag'] . $country["image_flag_presigned"];
                                $station_data["nationality"] = $country;
                                $station_data["timezone"] = $country["zone"];
                            } else {
                                $station_data["timezone"] = "";
                                $station_data["country"] = "";
                                $station_data["countryCode"] = "";
                                $station_data["nationality"] = new stdClass();
                            }
                        }
                        else {
                            $station_data["timezone"] = "";
                            $station_data["country"] = "";
                            $station_data["countryCode"] = "";
                            $station_data["nationality"] = new stdClass();
                        }

                        if (!empty($station_data["image_profile"])) {
                            $img = $station_data["image_profile"];
                            $station_data["image_thumb"] = $this->image_url(basename($img), 'thumb', 'stations');
                        }

                        array_push($recommended_St, $station_data);
                    }
                }

                return $recommended_St;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

     public function report_station($post = []) {
        $check = $this->read_db->select("*")
                        ->where("request_user_id", $post["station_id"])
                        ->where("user_id", $post["user_id"])
                        ->where("user_request_reason_id", $post['user_request_reason_id'])
                        ->get("user_requests")->row_array();

        if (empty($check)) {
            $insert = $this->write_db->insert("user_requests", array(
                "request_user_id" => $post["station_id"],
                "user_id" => $post["user_id"],
                "user_request_reason_id" => $post['user_request_reason_id']
            ));

            if ($insert) {
                return true;
            } else {
                return false;
            }
        } else {
            $up_arr = array(
                "request_user_id" => $post["station_id"],
                "user_id" => $post["user_id"],
                "user_request_reason_id" => $post['user_request_reason_id']
            );

            $this->write_db->set($up_arr)->where("request_user_id", $post["station_id"])->where("user_id", $post["user_id"])->where("user_request_reason_id", $post['user_request_reason_id'])->update("user_requests");

            return true;
        }
    }

}

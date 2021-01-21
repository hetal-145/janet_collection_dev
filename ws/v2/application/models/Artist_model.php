<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class Artist_model extends CI_Model {

    private $read_db, $write_db;

    function __construct() {
        parent::__construct();

        $this->read_db = $this->load->database('read', true);
        $this->write_db = $this->load->database('write', true);
    }

    public function get_artist_list($post = []) {
        if (isset($post["chr"]) && !empty($post["chr"])) {
            $fetch = $this->read_db->select("*")
                            ->where('status', 1)
                            ->where("name like '" . $post["chr"] . "%'")
                            ->limit($post['limit'])
                            ->offset($post['offset'])
                            ->order_by("name", "asc")
                            ->get('artists')->result_array();
        } else {
            $fetch = $this->read_db->select("*")
                            ->where('status', 1)
                            ->limit($post['limit'])
                            ->offset($post['offset'])
                            ->order_by("name", "asc")
                            ->get('artists')->result_array();
        }

        if (!empty($fetch)) {
            foreach ($fetch as $key => $value) {
                $fetch[$key] = $this->artist_details_info($value["artist_id"]);
            }
            //print_r($fetch);
            return $fetch;
        }
    }

    public function artist_details_info($artist_id) {
        $data = $this->read_db->select("*")
                        ->where('status', 1)
                        ->where("artist_id", $artist_id)
                        ->get('artists')->row_array();
        if (!empty($data)) {
            $result = array_map(function($val) {
                if (is_null($val)) {
                    $val = "";
                }
                return $val;
            }, $data);

            $result["biography"] = strip_tags($result["biography"]);

            if (empty($result["image_profile"])) {
                $result["image_profile"] = "";
            }
            return $result;
        }
    }

    public function get_artist_profile($post = []) {
        $fetch2 = $this->read_db->select("artists.*")->where('artist_id', $post["artist_id"])->get('artists')->row_array();

        if (!empty($fetch2) && $fetch2["status"] == '1') {

            if (empty($fetch2["image"])) {
                $fetch2["image"] = "";
            }

            $fetch = array_map(function($val) {
                if (is_null($val)) {
                    $val = "";
                }
                return $val;
            }, $fetch2);

            //get username
            if (!empty($fetch["user_id"])) {
                $user = $this->read_db->select("*")
                                    ->join('fans f','f.user_id = u.user_id')
                                    ->where('u.user_id', $fetch["user_id"])
                                    ->get("users u")->row_array();
                $fetch["username"] = $user["username"];
                $fetch["dob"] = $user["dob"];
                $fetch["headlines"] = $user["blurb"];
                $fetch["phone"] = $user["phone"];
                $fetch["website"] = $user["website"];
                $fetch["city"] = $user["city_state"];
                $fetch["is_admin_verified"] = $user["is_admin_verified"];
            } else {
                $fetch["username"] = '';
            }

            if (!empty($fetch["area"])) {
                $fetch["area"] = json_decode($fetch["area"]);
            } else {
                $fetch["area"] = "";
            }

            if (!empty($fetch["aliases"])) {
                $fetch["aliases"] = json_decode($fetch["aliases"]);
            } else {
                $fetch["aliases"] = "";
            }

            if (!empty($fetch["tags"])) {
                $fetch["tags"] = json_decode($fetch["tags"]);
            } else {
                $fetch["tags"] = "";
            }

            if (!empty($fetch["begin_area"])) {
                $fetch["begin_area"] = json_decode($fetch["begin_area"]);
            } else {
                $fetch["begin_area"] = "";
            }

            $fetch["biography"] = strip_tags($fetch["biography"]);

            if (!empty($fetch["country"])) {
                //get country name
                $country = $this->read_db->select('*')
                                ->where('country_id', $fetch["country_id"])
                                ->get('countries')->row_array();

                $this->write_db->set("country_id", $country["country_id"])->where("artist_id", $post["artist_id"])->update("artists");

                if (!empty($country)) {
                    $country["flag"] = $_ENV['S3_PATH'] . $country['image_flag'] . $country["image_flag_presigned"];
                }

                $fetch["nationality"] = $country;
            } else if (!empty($fetch["country_id"])) {
                //get country name
                $country = $this->read_db->select('*')
                                ->where('country_id', $fetch["country_id"])
                                ->get('countries')->row_array();

                $this->write_db->set("country_id", $country["country_id"])->where("artist_id", $post["artist_id"])->update("artists");

                if (!empty($country)) {
                    $country["flag"] = $_ENV['S3_PATH'] . $country['image_flag'] . $country["image_flag_presigned"];
                }

                $fetch["nationality"] = $country;
            } else {
                $fetch["nationality"] = new stdClass();
            }

            //Artist User
            //get user details of artist
            $user_artist_details = $this->read_db->select("*")
                                            ->join('fans f','f.user_id = u.user_id')
                                            ->where("u.user_id", $fetch["user_id"])
                                            ->get("users u")->row_array();
            if (!empty($user_artist_details)) {
                unset($user_artist_details["password"]);
                unset($user_artist_details["password_updated"]);

                if (!empty($user_artist_details["image_profile"])) {
                    $img = $user_artist_details["image_profile"];
                    if($user_artist_details["is_s3"] == '1') {
                        $user_artist_details["image_profile"] = $img;
                        $user_artist_details["image_profile_thumb"] = $img;
                    }
                    else {
                        $user_artist_details["image_profile"] = $this->image_url2($img, '', 'upload/artists');
                        $user_artist_details["image_profile_thumb"] = $this->image_url2($img, 'thumb', 'upload/artists');
                    }
                } else {
                    $user_artist_details["image_profile"] = '';
                    $user_artist_details["image_profile_thumb"] = '';
                }

                if (!empty($user_artist_details["image_cover"])) {
                    $img1 = $user_artist_details["image_cover"];
                    if($user_artist_details["is_s3"] == '1') {
                        $user_artist_details["image_cover"] = $this->image_url($img1, '', 'artists');
                        $user_artist_details["image_cover_thumb"] = $this->image_url($img1, 'thumb', 'artists');
                    }
                    else{
                        $user_artist_details["image_cover"] = $this->image_url2($img1, '', 'upload/artists');
                        $user_artist_details["image_cover_thumb"] = $this->image_url2($img1, 'thumb', 'upload/artists');
                    }
                } else {
                    $user_artist_details["image_cover"] = '';
                    $user_artist_details["image_cover_thumb"] = '';
                }

                if (!empty($user_artist_details["nationality"])) {
                    //get country name
                    $country = $this->read_db->select('*')
                                    ->where('country_id', $user_artist_details["country_id"])
                                    ->get('countries')->row_array();

                    if (!empty($country)) {
                        $country["flag"] = $_ENV['S3_PATH'] . $country['image_flag'] . $country["image_flag_presigned"];
                    }

                    $user_artist_details["nationality"] = $country;
                } else {
                    $user_artist_details["nationality"] = new stdClass();
                }

                $user_artist_details2 = array_map(function($value) {
                    return $value === NULL ? "" : $value;
                }, $user_artist_details);

                $fetch["artist_user"] = $user_artist_details2;
            } else {
                $fetch["artist_user"] = new stdClass();
            }

            //genres
            $genres = $this->read_db->select("genres.*, genres.name , genres.genre_id as _id")
                    ->join('genres', 'genres.genre_id =  artist_genres.genre_id')
                    ->where('artist_genres.status', 1)
                    ->where('artist_genres.artist_id', $post["artist_id"])
                    ->get('artist_genres')
                    ->result_array();
            $fetch["genres"] = $genres;

            //favourite
            $favourite = $this->read_db->select("*")
                    ->where('status', 1)
                    ->where('user_id', $post["user_id"])
                    ->where('artist_id', $post["artist_id"])
                    ->get('user_favorite_artists')
                    ->row_array();
            //print_r($favourite);
            if (!empty($favourite)) {
                $fetch["favourite"] = "1";
            } else {
                $fetch["favourite"] = "0";
            }

            //subscribe
            $subscribe = $this->read_db->select("*")
                    ->where('user_id', $post["user_id"])
                    ->where('artist_id', $post["artist_id"])
                    ->get('user_subscribe_artists')
                    ->row_array();
            //print_r($subscribe);
            if (!empty($subscribe)) {
                $fetch["is_subscribe"] = "1";
            } else {
                $fetch["is_subscribe"] = "0";
            }

            //share count
            $fetch["artist_share_count"] = $this->artist_share_count($post["artist_id"]);

            //albums
            $albums = $this->artist_wise_album_list($post["artist_id"], $fetch["image"], $post["user_id"]);
            if (!empty($albums)) {
                $fetch["albums"] = $albums;
            } else {
                $fetch["albums"] = array();
            }

            //recent_played_tracks
            $recent_played_tracks = $this->artist_recent_played_tracks($fetch["name"], $post["user_id"]);
            if (!empty($recent_played_tracks)) {
                $fetch["recent_played_tracks"] = $recent_played_tracks;
            } else {
                $fetch["recent_played_tracks"] = array();
            }

            //video
            $video = $this->artist_wise_videos_list($post["user_id"], $post["artist_id"]);
            if (!empty($video)) {
                $fetch["videos"] = $video;
            } else {
                $fetch["videos"] = array();
            }

            //Top 10
            $top_10 = $this->artist_top_10_song($fetch["name"], $post["user_id"]);
            if (!empty($top_10)) {
                $fetch["top_10"] = $top_10;
            } else {
                $fetch["top_10"] = array();
            }

            //most trending
            $trending = "";
            if (!empty($trending)) {
                $fetch["trending"] = $trending;
            } else {
                $fetch["trending"] = array();
            }

            //upcoming appearance
            $talkshow = $this->artist_upcoming_apperance($post["artist_id"], $post["user_id"]);
            if (!empty($talkshow)) {
                $fetch["talkshow"] = $talkshow;
            } else {
                $fetch["talkshow"] = array();
            }

            //stations_played
            $stations_played = $this->artist_station_played($post["artist_id"], $post["user_id"]);
            if (!empty($stations_played)) {
                $fetch["stations_played"] = $stations_played;
            } else {
                $fetch["stations_played"] = array();
            }

            $fetch["favourite_count"] = (string) $this->read_db->select("user_favorite_artist_id")
                            ->where('status', 1)
                            ->where('artist_id', $post["artist_id"])
                            ->get('user_favorite_artists')
                            ->num_rows();

            return $fetch;
        }
		else if (!empty($fetch2) && $fetch2["status"] == '0') {
		    return 1;
		}
		else {
		    return 2;
		}
    }

     function artist_share_count($artist_id) {
        $count = $this->read_db->select("*")
                        ->where("artist_id", $artist_id)
                        //->group_by("user_id")
                        ->get("user_share_artists")->num_rows();
        return $count;
    }

     public function artist_wise_album_list($artist_id, $artist_image, $user_id) {
        $artist_albums = array();
        $tracks_albums = array();

        $fetch = $this->read_db->select("*")
                        ->where('artist_id', $artist_id)
                        ->limit(20)
                        ->get('albums')->result_array();
        if (!empty($fetch)) {

            foreach ($fetch as $key => $value) {
                $album2 = $this->read_db->select("*")->where("album_id", $value["album_id"])->get("albums")->row_array();

                if (!empty($album2)) {
                    $album = array_map(function($val) {
                        return $val === NULL ? "" : $val;
                    }, $album2);

                    $album["image"] = $artist_image;
                    array_push($artist_albums, $album);
                }
            }
            return $artist_albums;
        }
    }

    public function artist_recent_played_tracks($name, $user_id) { return array();

        $recent_played_tracks = array();
        $recent_tracks = $this->read_db->select("*")
                ->where("name like '%" . $name . "%'")
                ->group_by("track_name")
                ->order_by('songs_played_id', 'desc')
                ->limit(15)
                ->get('songs_played')
                ->result_array();

        if (!empty($recent_tracks)) {
            foreach ($recent_tracks as $key => $value) {
                $track_details = $this->track_detail($value["track_name"], $user_id);
                if (!empty($track_details)) {
                    $recent_tracks[$key] = $track_details;
                    array_push($recent_played_tracks, $track_details);
                }
            }
            return $recent_played_tracks;
        } else {
            return $recent_played_tracks;
        }
    }

    public function artist_wise_videos_list($user_id, $artist_id) {

        $fetch = $this->read_db->select("*")
                        ->where('artist_id', $artist_id)
                        ->limit(20)
                        ->get('artist_videos')->result_array();
        if (!empty($fetch)) {
            foreach ($fetch as $key => $value) {
                $get_data = array_map(function($val) {
                    return $val === NULL ? "" : $val;
                }, $value);

                $fetch[$key] = $get_data;

               /* if (!empty($get_data["thumbnails"])) {
                    $get_data["thumbnails"] = json_decode($get_data["thumbnails"]);
                } else {
                    $get_data["thumbnails"] = new stdClass();
                }

                $get_data["description"] = html_entity_decode($get_data["description"]);

                if (!empty($get_data["tags"])) {
                    $get_data["tags"] = json_decode($get_data["tags"]);
                } else {
                    $get_data["tags"] = new stdClass();
                }*/

                $fetch[$key] = $get_data;
            }
            //print_r($fetch);
            return $fetch;
        }
    }

    public function artist_top_10_song($artist_name, $user_id) { return array();
        $most_listened_song = array();
        $fetch = $this->read_db->select("*, count(track_name) as most_listen_count")
                        ->where('user_id', $user_id)
                        ->where("artist_name like '%" . $artist_name . "%'")
                        ->group_by('track_name')
                        ->order_by('most_listen_count', 'desc')
                        ->limit(10)
                        ->get('songs_played')->result_array();

        if (!empty($fetch)) {

            foreach ($fetch as $key => $value) {
                $track_details = $this->track_detail(trim($value["track_name"]), $user_id);
                if (!empty($track_details)) {
                    $fetch[$key] = array_merge($fetch[$key], $track_details);
                }
            }
            //echo "<pre>"; print_r($fetch); exit;
            return $fetch;
        }
    }

    public function artist_upcoming_apperance($artist_id, $user_id) {
        $artist_array = array();
        $artist1 = $this->read_db->select("*")
                        ->where('user_id', $artist_id)
                        ->where('status', 1)
                        ->where('schedule_date >= CURDATE()')
                        ->order_by('schedule_date', 'asc')
                        ->get('show_guests')->result_array();

        $artist2 = $this->read_db->select("*")
                        ->where('user_id', $artist_id)
                        ->where('status', 1)
                        //->where('date >= CURDATE()')
                        ->order_by('show_host_id', 'desc')
                        ->get('show_hosts')->result_array();

        $fetch = array_values(array_unique(array_merge(array_column($artist1, "show_id"), array_column($artist2, "show_id"))));

        if (!empty($fetch)) {
            $post["user_id"] = $user_id;

            foreach ($fetch as $value) {
                $post["show_id"] = $value;
                $tlkshow = $this->get_talkshow_by_id($post);
                if (!empty($tlkshow)) {
                    array_push($artist_array, $tlkshow);
                }
            }
            //echo "<pre>"; print_r($artist_array); exit;
            return $artist_array;
        }
    }


    public function artist_station_played($artist_id, $user_id) {
        $artist_array = array();

        $artist1 = $this->read_db->select("*")
                        ->where('user_id', $artist_id)
                        ->where('status', 1)
                        //->where('date >= CURDATE()')
                        ->limit(20)
                        ->order_by('station_host_id', 'desc')
                        ->get('station_hosts')->result_array();

        if (!empty($artist1)) {
            $post["user_id"] = $user_id;

            foreach ($artist1 as $value) {
                $post["station_id"] = $value["station_id"];
                $sttion = $this->get_station_profile($post);
                if (!empty($sttion)) {
                    array_push($artist_array, $sttion);
                }
            }
            //echo "<pre>"; print_r($artist_array); exit;
            return $artist_array;
        }
    }


    public function user_favourite_artist_list($post = []) {
        $array_fetch = array();
        $array_exitst = array();
        $fetch = $this->read_db->select("*")
                        ->where('user_id', $post["user_id"])
                        ->get('user_favorite_artists')->result_array();
//echo $this->read_db->last_query();exit;
        if (!empty($fetch)) {

            foreach ($fetch as $key => $value) {
                if (!in_array($value["artist_id"], $array_exitst)) {
                    array_push($array_exitst, $value["artist_id"]);

                    $artist_id = $value["artist_id"];

                    $response = $this->read_db->select("*")->where('artist_id', $artist_id)->get('artists')->row_array();
                    if (!empty($response)) {

                        unset($response["area"]);
                        unset($response["aliases"]);
                        unset($response["tags"]);
                        unset($response["begin_area"]);

                        if (empty($response["image"])) {
                            $response["image"] = "";
                        }

                        $response2 = array_map(function($val) {
                            if (is_null($val)) {
                                $val = "";
                            }
                            return $val;
                        }, $response);

                        $fetch[$key] = $response2;

                        //is_favourite
                        $favourite = $this->read_db->select("*")
                                        ->where('user_id', $post["user_id"])
                                        ->where('artist_id', $artist_id)
                                        ->get('user_favorite_artists')->row_array();
                        if (!empty($favourite)) {
                            $fetch[$key]["is_favourite"] = "1";
                        } else {
                            $fetch[$key]["is_favourite"] = "0";
                        }

                        array_push($array_fetch, $fetch[$key]);
                    }
                } else {
                    unset($fetch[$key]);
                }
            }
            //print_r($fetch);
            return $array_fetch;
        }
    }

    public function update_artist($post = [], $files = []) {
        //print_r($post); print_r($files); exit;
        $user_array = array();
        $user_id = $post["user_id"];
        unset($post["user_id"]);

        //get station's user id
        $station_user = $this->read_db->select("user_id")->where("artist_id", $post["artist_id"])->get("artists")->row_array();

        if (isset($post["username"])) {
            $userdata = $this->user_model->get_user_by_id($station_user["user_id"]);
            if ($userdata["username"] != $post["username"]) {
                $post["user_id"] = $station_user["user_id"];
                $chk = $this->check_username_id($post);
                if (!empty($chk) && $chk == 4) {
                    return 1;
                }
            }
            $user_array["username"] = $post["username"];
            unset($post["username"]);
        }

        if (isset($post["administrator"])) {
            $userdata = $this->user_model->get_user_by_id($station_user["user_id"]);
            if ($userdata["username"] != $post["administrator"]) {
                $post["user_id"] = $station_user["user_id"];
                $chk = $this->check_username_id($post);
                if (!empty($chk) && $chk == 4) {
                    return 1;
                }
            }
            $user_array["username"] = $post["administrator"];
            unset($post["administrator"]);
        }

        if (isset($post["name"])) {
            $user_array["name"] = $post["name"];
            unset($post["name"]);
        }

        if (isset($post["phone"])) {
            $user_array["phone"] = $post["phone"];
            unset($post["phone"]);
        }

        if (isset($post["website"])) {
            $user_array["website"] = $post["website"];
            unset($post["website"]);
        }

        if (isset($post["dob"])) {
            $user_array["dob"] = $post["dob"];
            unset($post["dob"]);
        }

        if (isset($post["description"])) {
            $user_array["description"] = $post["description"];
            unset($post["description"]);
        }

        if (isset($post["country"])) {
            $user_array["nationality"] = $post["country"];
            $post["country_id"] = $post["country"];
            unset($post["country"]);
        }

        if (isset($post["city"])) {
            $user_array["city_state"] = $post["city"];
            unset($post["city"]);
        }

        //bucket info
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
                $filename = date('YmdHis') . rand() . $post["artist_id"] . strtolower($ext);
                $filepath = $files['image']['tmp_name'];
                $mime = mime_content_type($filepath);

                if( in_array($ext1, $ext_arr2) ) {
                    $result = $s3->putObject(array(
                        'Bucket' => $_ENV['BUCKET_NAME'],
                        'Key' => 'artists/'.$filename,
                        'SourceFile' => $filepath,
                        'ACL' => 'public-read',
                        'StorageClass' => 'STANDARD',
                        'ContentType'=>$mime
                    ));

                    $result2 = $s3->putObject(array(
                        'Bucket' => $_ENV['BUCKET_NAME'],
                        'Key' => 'artists/thumbs/'.$filename,
                        'SourceFile' => $filepath,
                        'ACL' => 'public-read',
                        'StorageClass' => 'STANDARD',
                        'ContentType'=>$mime
                    ));

                    $post["image"] = $this->image_url($filename, '', 'artists');
                    $user_array["profile_image"] = $this->image_url($filename, '', 'artists');
                    $user_array["is_s3"] = 1;
                }
                else {
                    return 'The filtype you are trying to upload is not allowed';
                }
            } else {
                $post["image"] = "";
                $user_array["profile_image"] = "";
                $user_array["is_s3"] = 0;
            }
        }

        //station cover image
        if (isset($files["artist_cover_image"]["name"])) {
            if (!empty($files["artist_cover_image"]["name"])) {
                $ext = '.' . pathinfo($files['artist_cover_image']['name'], PATHINFO_EXTENSION);
                $ext1 = pathinfo($files['artist_cover_image']['name'], PATHINFO_EXTENSION);
                $filename = date('YmdHis') . rand() . $post["artist_id"] . strtolower($ext);
                $filepath = $files['artist_cover_image']['tmp_name'];
                $mime = mime_content_type($filepath);

                if( in_array($ext1, $ext_arr2) ) {
                    $result = $s3->putObject(array(
                        'Bucket' => $_ENV['BUCKET_NAME'],
                        'Key' => 'artists/'.$filename,
                        'SourceFile' => $filepath,
                        'ACL' => 'public-read',
                        'StorageClass' => 'STANDARD',
                        'ContentType'=>$mime
                    ));

                    $result2 = $s3->putObject(array(
                        'Bucket' => $_ENV['BUCKET_NAME'],
                        'Key' => 'artists/thumbs/'.$filename,
                        'SourceFile' => $filepath,
                        'ACL' => 'public-read',
                        'StorageClass' => 'STANDARD',
                        'ContentType'=>$mime
                    ));

                    $post["artist_cover_image"] = $this->image_url($filename, '', 'artists');
                    $user_array["cover_image"] = $filename;
                    $user_array["is_s3"] = 1;
                }
                else {
                    return 'The filtype you are trying to upload is not allowed';
                }
            } else {
                $post["artist_cover_image"] = "";
                $user_array["cover_image"] = "";
                $user_array["is_s3"] = 0;
            }
        }

        //update genres
        if (isset($post["genres"]) && !empty($post["genres"])) {
            $check_genres = $this->read_db->select("*")->where("artist_id", $post["artist_id"])->get("artist_genres")->result_array();
            if (!empty($check_genres)) {
                $this->write_db->where("artist_id", $post["artist_id"])->delete("artist_genres");
            }
            $genres = explode(',', $post["genres"]);
            foreach ($genres as $g) {
                $in_arr1 = array(
                    'artist_id' => $post["artist_id"],
                    'genres_id' => $g,
                );
                $this->write_db->insert('artist_genres', $in_arr1);
            }
            unset($post["genres"]);
        }
        else if(empty($post["genres"])){
            unset($post["genres"]);
        }

        //update station user details
        //$this->write_db->where("user_id", $station_user["user_id"])->update("users", $user_array);

        $post["name"] = $user_array["name"];

        //update artist details
        $update = $this->write_db->where("artist_id", $post["artist_id"])->update("artists", $post);
        if ($update) {
            return 2;
        } else {
            return false;
        }
    }


    public function report_artist($post = []) {
        $check = $this->read_db->select("*")
                        ->where("request_user_id", $post["artist_id"])
                        ->where("user_id", $post["user_id"])
                        ->where("user_request_reason_id", 4)
                        ->get("user_requests")->row_array();

        if (empty($check)) {
            $insert = $this->write_db->insert("user_requests", array(
                "request_user_id" => $post["artist_id"],
                "user_id" => $post["user_id"],
                "user_request_reason_id" => $post["user_request_reason_id"]
            ));

            if ($insert) {
                return true;
            } else {
                return false;
            }
        } else {
            $up_arr = array(
                "request_user_id" => $post["artist_id"],
                "user_id" => $post["user_id"],
                "user_request_reason_id" => $post["user_request_reason_id"]
            );
            $this->write_db->set($up_arr)->where("request_user_id", $post["artist_id"])->where("user_id", $post["user_id"])->where("user_request_reason_id", $post["user_request_reason_id"])->update("user_requests");

            return true;
        }
    }

    public function share_artist($post = []) { return true;
        $user_ids = explode(",", $post["ids"]);
        unset($post["ids"]);
        $userdata = $this->get_user_by_id($post["user_id"]);
        $artistdata = $this->get_artist_detail_id2($post["artist_id"]);
        $msg = '@' . $userdata["username"] . ' - ' . $userdata["name"] . ', has shared ' . $artistdata["artist_name"] . '';
        //print_r($artistdata); exit;

        foreach ($user_ids as $ids) {
            $check = $this->read_db->select("*")
                            ->where("user_id", $ids)
                            ->where("artist_id", $post["artist_id"])
                            ->get("artist_share")->row_array();

            if (!empty($post["comment"])) {
                $description = $post["comment"];
            } else {
                $description = $msg;
            }

            if (empty($check)) {
                $insert = $this->write_db->insert("artist_share", array(
                    "user_id" => $ids,
                    "artist_id" => $post["artist_id"],
                    "comment" => $description,
                ));

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
                    'chat_type' => 5,
                    'user_type' => 5,
                    'tagged_users' => null,
                    'song_name' => null,
                    'is_shared' => 1,
                    'is_shared_type' => 4,
                    "group_id" => $post["artist_id"],
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

                $this->write_db->insert("chat", array_merge($ins_chat, $send_chat));
                $this->write_db->insert("chat", array_merge($ins_chat, $received_chat));

                if ($insert) {
                    //insert in notification
                    $insert_notify = array(
                        'to_user_id' => (string) $ids,
                        'contact_person_id' => (string) $post["artist_id"],
                        'artist_id' => (string) $post["artist_id"],
                        'notification_types' => "26",
                        'message' => $msg,
                        "description" => $description,
                    );

                    $this->m_api->create_notification($insert_notify);
                    $this->m_notify->send($insert_notify);
                }
            } else {
                unset($post["is_prv_share"]);
                $this->write_db->set($post)->where("artist_id", $post["artist_id"])->where("user_id", $ids)->update("artist_share");

                //insert in notification
                $insert_notify1 = array(
                    'to_user_id' => (string) $ids,
                    'contact_person_id' => (string) $post["artist_id"],
                    'artist_id' => (string) $post["artist_id"],
                    'notification_types' => "26",
                    'message' => $msg,
                    "description" => $description,
                );

                $this->m_api->create_notification($insert_notify1);
                $this->m_notify->send($insert_notify1);
            }
        }

        return true;
    }

    public function make_sub_unsub_artist($post = []) {
        $exists = $this->read_db->select("*")
                        ->where('artist_id', $post['artist_id'])
                        ->where('user_id', $post['user_id'])
                        ->get('user_subscribe_artists')->row_array();

        if ($post["is_subscribe"] == 1) {
            if (!empty($exists)) {
                return 1;
            } else {
                //check if artists is subscribing itself
                $check = $this->read_db->select("user_id, parent_artist_id  ")
                                ->where('user_id', $post['artist_id'])
                                ->get("artists")->row_array();

                if ($check["user_id"] == $post["user_id"]) {
                    return 5;
                } else if ($check["parent_artist_id"] == $post["user_id"]) {
                    return 5;
                } else {
                    $subscribe_array = array(
                        'artist_id' => $post['artist_id'],
                        'user_id' => $post['user_id'],
                    );
                    $this->write_db->insert('user_subscribe_artists', $subscribe_array);
//                    $this->write_db->insert('user_favourite_artist', $subscribe_array);
                    return 2;
                }
            }
        } else if ($post["is_subscribe"] == 0) {
            if (!empty($exists)) {
                $subscribe_array = array(
                    'artist_id' => $post['artist_id'],
                    'user_id' => $post['user_id'],
                );

                $this->write_db->where($subscribe_array)->delete('user_subscribe_artists');
//                $this->write_db->where($subscribe_array)->delete('user_favourite_artist');
                return 3;
            } else {
                return 4;
            }
        }
    }


    public function artist_subscriber_list($post = []) {
        $subscriber = array();
        //$where = "user_id NOT IN (" . $post["user_id"] . ")";
        $fetch = $this->read_db->select("user_id")
                        ->where('artist_id', $post["artist_id"])
                        //->where($where)
                        ->get('user_subscribe_artists')->result_array();
//        print_r($fetch); exit;
        if (!empty($fetch)) {
            foreach ($fetch as $key => $value) {
                if (isset($post["chr"]) && !empty($post["chr"])) {
                    $where2 = "u.user_id = " . $value["user_id"] . " and f.name LIKE '%" . trim($post["chr"]) . "%'";
                } else {
                    $where2 = "u.user_id = " . $value["user_id"] . "";
                }

                $response = $this->read_db->select("u.user_id, name, email, username, blurb, image_profile, is_user_verified, image_cover", false)
                                ->join('fans f','f.user_id = u.user_id')
                                ->where($where2)
                                ->get('users u')->row_array();
                if (!empty($response)) {
                    if (!empty($response["image_profile"])) {
                        $img = $response["image_profile"];

                        if($response["is_s3"] == '1'){
                            $response["image_profile"] = $this->pic_url($img);
                            $response["image_profile_thumb"] = $this->pic_url($img, 'thumb');
                        }
                        else {
                            // $response["image_profile"] = $this->pic_url2($img);
                            // $response["image_profile_thumb"] = $this->pic_url2($img, 'thumb');
                             $response["image_profile"] = '';
                            $response["image_profile_thumb"] = '';
                        }
                    } else {
                        $response["image_profile"] = "";
                        $response["image_profile_thumb"] = "";
                    }

                    if (!empty($response["image_cover"])) {
                        $img = $response["cover_image"];

                        if($response["is_s3"] == '1'){
                            $response["image_cover"] = $this->pic_url($img);
                            $response["image_cover_thumb"] = $this->pic_url($img, 'thumb');
                        }
                        else {
                            // $response["image_cover"] = $this->pic_url2($img);
                            // $response["image_cover_thumb"] = $this->pic_url2($img, 'thumb');
                             $response["image_cover"] = '';
                            $response["image_cover_thumb"] = '';
                        }
                    } else {
                        $response["image_cover"] = "";
                        $response["image_cover_thumb"] = "";
                    }

                    $response22 = $this->user_model->check_connection_user($post["user_id"], $value["user_id"]);
                    $response = array_merge($response, $response22);
                    array_push($subscriber, $response);
                }
            }
            //print_r($fetch);
            return $subscriber;
        }
    }

    public function artist_subscriber_common_connection($post = []) {
        $where = "user_id NOT IN (" . $post["user_id"] . ")";
        $connection_list = $this->read_db->select("user_id")
                        ->where('artist_id', $post["artist_id"])
                        ->where($where)
                        ->get('user_subscribe_artists')->result_array();

        if (!empty($connection_list)) {
            $connect_merge = array_column($connection_list, 'user_id');

            //get common connection
            $logged_user_connection_list = $this->read_db->select("*")
                            ->where("(connect_user_id = " . $post["user_id"] . " OR user_id = " . $post["user_id"] . ")")
                            ->where('action', 1)
                            ->get('user_connections')->result_array();

            if (!empty($logged_user_connection_list)) {
                $connect_logged_user_list1 = array_column($logged_user_connection_list, 'connect_user_id');
                $connect_logged_user_list2 = array_column($logged_user_connection_list, 'user_id');
                $connect_logged_user_common = array_merge($connect_logged_user_list1, $connect_logged_user_list2);
                $result = array_intersect($connect_merge, $connect_logged_user_common);

                if (!empty($result)) {
                    $connect_logged_user_list = implode(',', array_unique($result));
                    $where2 = "user_id IN (" . $connect_logged_user_list . ") and user_id != " . $post["user_id"] . "";
                    $response = $this->read_db->select("u.user_id, name, username, user_type_id, email, image_profile, blurb, is_live", false)
                                    ->join('fans f','f.user_id = u.user_id')
                                    ->where($where2)
                                    ->limit(10)
                                    ->where('status', 1)
                                    ->get('users u')->result_array();

                    if (!empty($response)) {
                        foreach ($response as $k => $val) {
                            if (!empty($val["image_profile"])) {
                                if($val["is_s3"] == '1'){
                                    $response[$k]["image_profile"] = $this->pic_url($val["image_profile"]);
                                    $response[$k]["image_profile_thumb"] = $this->pic_url($val["image_profile"], 'thumb');
                                }
                                else {
                                    $response[$k]["image_profile"] = $this->pic_url2($val["image_profile"]);
                                    $response[$k]["image_profile_thumb"] = $this->pic_url2($val["image_profile"], 'thumb');
                                }
                            } else {
                                $response[$k]["image_profile_thumb"] = "";
                            }

                            $response22 = $this->user_model->check_connection_user($post["user_id"], $val["user_id"]);
                            $response[$k] = array_merge($response[$k], $response22);
                        }
                        return $response;
                    } else {
                        return array();
                    }
                } else {
                    return array();
                }
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public function make_fav_unfav_artist($post = []) {
        $exists = $this->read_db->select("*")
                        ->where('artist_id', $post['artist_id'])
                        ->where('user_id', $post['user_id'])
                        ->get('user_favorite_artists')->row_array();

        if ($post["is_fav"] == 1) {
            if (!empty($exists)) {
                return 1;
            } else {
                $subscribe_array = array(
                    'artist_id' => $post['artist_id'],
                    'user_id' => $post['user_id'],
                );
                $this->write_db->insert('user_favorite_artists', $subscribe_array);
                return 2;
            }
        } else if ($post["is_fav"] == 0) {
            if (!empty($exists)) {
                $subscribe_array = array(
                    'artist_id' => $post['artist_id'],
                    'user_id' => $post['user_id'],
                );
                $this->write_db->where($subscribe_array)->delete('user_favorite_artists');
                return 3;
            } else {
                return 4;
            }
        }
    }



}
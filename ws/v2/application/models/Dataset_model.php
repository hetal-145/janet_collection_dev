<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dataset_model extends CI_Model {

    private $read_db, $write_db;

	function __construct() {
        parent::__construct();
        $this->read_db = $this->load->database('read', true);
        $this->write_db = $this->load->database('write', true);
    }

    function send_mail($to, $subject, $msg, $bcc = '') {
        $ci = get_instance();
        $config = array();
        $tmp_arr = array();

        $config_data = $this->read_db->where_in('key', array('smtp_user', 'smtp_pass', 'smtp_host', 'smtp_port', 'support', 'smtp_user2'))->get('system_settings')->result_array();

        foreach ($config_data as $key => $row) {
            $tmp_arr[$row['key']] = $row['value'];
        }

        $config['smtp_user'] = $tmp_arr['smtp_user'];
        $config['smtp_pass'] = $tmp_arr['smtp_pass'];
        $config['smtp_host'] = $tmp_arr['smtp_host'];
        $config['smtp_port'] = $tmp_arr['smtp_port'];
        $config['support'] = $tmp_arr['support'];
        $config['support_mail'] = $tmp_arr['support'];

        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['crlf'] = "\r\n";
        $config['newline'] = "\r\n";

        // print_r($config);

        $ci->email->initialize($config);

        $ci->email->from($config['support_mail'], 'Wadio');
        $ci->email->to($to);
        //$this->email->reply_to($config['smtp_user'], 'Wadio');
        $ci->email->subject($subject);
        $ci->email->message($msg);
        $ci->email->send();

        // echo $this->email->print_debugger();
    }

    function get_category_list() {
        $genres = $this->read_db->select("*")
                ->where('status', 1)
                ->get('station_category_types')
                ->result_array();

        if (!empty($genres)) {
            foreach ($genres as $key => $value) {
                $value2 = array_map(function($val) {
                    if (is_null($val)) {
                        $val = "";
                    }
                    return $val;
                }, $value);

                $genres[$key] = $value2;
            }
            return $genres;
        }
    }

    function get_privacy_policy() {
        $get_privacy_policy = $this->read_db->select("value")
                ->where('key', 'privacy_policy')
                ->get('system_settings')
                ->row_array();

        if (!empty($get_privacy_policy)) {
            return $get_privacy_policy["value"];
        } else {
            return false;
        }
    }

    function get_term_condition() {
        $get_term_condition = $this->read_db->select('value')
                ->where('key', 'terms_and_conditions')
                ->get('system_settings')
                ->row_array();

        if (!empty($get_term_condition)) {
            return $get_term_condition["value"];
        } else {
            return false;
        }
    }

    function legal_notice() {
        $get_privacy_policy = $this->read_db->select("value")
                ->where('key', 'legal_notice')
                ->get('system_settings')
                ->row_array();

        if (!empty($get_privacy_policy)) {
            return $get_privacy_policy["value"];
        } else {
            return false;
        }
    }

    function get_countries($offset = 0, $longitude = "", $latitude = "", $chr = "") {
        if(!empty($chr)) {
            $this->read_db->where("name LIKE '" . $chr . "%'");
        }

        if($offset > 0) {
            $this->read_db->limit(LIMIT);
            $this->read_db->offset($offset);
        }
        $countries = $this->read_db->select("*", false)
                ->where("status", 1)
                ->order_by("name", "asc")
                ->get('countries')
                ->result_array();

        if(!empty($countries)) {
            foreach ($countries as $key => $value) {
                //$countries[$key]["flag"] = $_ENV['FLAG_PATH'].strtolower($value["iso2"]).'_medium.png';

                $countries[$key]["flag"] = $_ENV['S3_PATH'] . $value['image_flag'] . $value["image_flag_presigned"];

                if(!empty($latitude) && !empty($longitude)) {
                    //get distance
                    $distance = $this->read_db->select("get_distance_metres(" . $value["longitude"] . "," . $value["latitude"] . ", " . $longitude . "," . $latitude . ") as distance", FALSE)
                                    ->get('countries')->row_array();

                    $distance_in_km = (string) round(($distance["distance"] / 1000), 2);
                    $countries[$key]["distance"] = $distance_in_km;

                    //get stations
                    $stations = $this->read_db->select("count(station_id) as total_station")
                                    ->where("country_iso2", $value["iso2"])
                                    ->where("status", 1)
                                    ->limit(1)
                                    ->get("stations")->row_array();
                    $countries[$key]["total_stations"] = $stations["total_station"];
                }
            }
            return $countries;
        }
        else {
            return false;
        }
    }

    function get_country_by_iso2($iso2) {
        $countries = $this->read_db->select("*", false)
                ->where("status", 1)
                ->where("iso2", $iso2)
                ->get('countries')
                ->row_array();

        if(!empty($countries)) {
            $countries["flag"] = $_ENV['S3_PATH'] . $countries['image_flag'] . $countries["image_flag_presigned"];
            return $countries;
        }
        else {
            return false;
        }
    }

    function get_station_categoies() {
        $response = $this->read_db->select("*", false)
                ->where("status", 1)
                ->get('station_category_types')
                ->result_array();

        if (!empty($response)) {
            foreach ($response as $key => $value) {
                $value2 = array_map(function($val) {
                    if (is_null($val)) {
                        $val = "";
                    }
                    return $val;
                }, $value);

                $response[$key] = $value2;
            }
            return $response;
        }
        else {
            return false;
        }
    }

    function get_languages($offset = 0) {
        $response = $this->read_db->select("*", false)
                ->limit(LIMIT)
                ->offset($offset)
                ->get('languages')
                ->result_array();

        if (!empty($response)) {
            foreach ($response as $key => $value) {
                $value2 = array_map(function($val) {
                    if (is_null($val)) {
                        $val = "";
                    }
                    return $val;
                }, $value);

                $response[$key] = $value2;
            }
            return $response;
        }
        else {
            return false;
        }
    }

    function get_language_by_code($code) {
        $this->read_db->where("code", $code);
        $response = $this->read_db->select("*", false)
                ->get('languages')
                ->row_array();
        if (!empty($response)) {
            return $response;
        }
        else {
            return false;
        }
    }

    function get_genres($post) {

        if(isset($post['is_popular'])) {
            if($post['is_popular'] == 1){
               $this->read_db->where('is_popular', 1);
            }
            else if($post['is_popular'] == 0){
               $this->read_db->where('is_popular', 0);
            }

            $genres = $this->read_db->select("*", false)
                ->where("status", 1)
                //->where('is_qa_approved', 1)
                ->limit(LIMIT)
                ->offset($post['offset'])
                ->get('genres')
                ->result_array();
        }
        else if(!empty($post["country"])) {
            $genres = $this->read_db->select("g.*", false)
                ->join("station_genres sg", "g.genre_id = sg.genre_id")
                ->join("stations s", "s.station_id = sg.station_id")
                ->where("g.status = 1  and s.country_iso2 = '".$post["country"]."'")
                ->group_by("g.genre_id")
                ->get('genres g')
                ->result_array();
        }
        else if(!empty($post["latitude"]) && !empty($post["longitude"])) {
            $genres = $this->read_db->select("g.*, get_distance_metres(s.latitude, s.longitude, '".$post["latitude"]."','".$post["longitude"]."') AS distance", false)
                ->join("station_genres sg", "g.genre_id = sg.genre_id")
                ->join("stations s", "s.station_id = sg.station_id")
                ->where('g.status', 1)
                ->group_by("g.genre_id")
                ->order_by("distance", "asc")
                ->get('genres g')
                ->result_array();
        }
        else {
            $genres = $this->read_db->select("*", false)
                ->where("status", 1)
                //->where('is_qa_approved', 1)
                ->limit(LIMIT)
                ->offset($_GET['offset'])
                ->get('genres')
                ->result_array();
        }

        if (!empty($genres)) {
            foreach ($genres as $key => $value) {
                $value2 = array_map(function($val) {
                    if (is_null($val)) {
                        $val = "";
                    }
                    return $val;
                }, $value);

                $genres[$key] = $value2;

                if (!empty($value2["image"])) {
                    $genres[$key]["image"] = $_ENV['S3_PATH'] . 'genres/' . $value2["image"];
                    $genres[$key]["image_thumb"] = $_ENV['S3_PATH'] . 'genres/' . $value2["image"];
                } else {
                    $genres[$key]["image"] = "";
                    $genres[$key]["image_thumb"] = "";
                }
            }

            usort($genres, function($a, $b) {
                return strcasecmp($a["name"], $b["name"]);
            });
            return $genres;
        }
        else {
            return false;
        }
    }

    function get_genre_by_id($genre_id) {
        $this->read_db->where("genre_id", $genre_id);
        $response = $this->read_db->select("*", false)
                ->where("status", 1)
                //->where('is_qa_approved', 1)
                ->get('genres')
                ->row_array();

        if (!empty($response)) {
            if (!empty($response["image"])) {
                $img = $response["image"];
                $response["image"] = $_ENV['S3_PATH'] . 'genres/' . $img;
                $response["image_thumb"] = $_ENV['S3_PATH'] . 'genres/' . $img;
            }
            else {
                $response["image"] = "";
                $response["image_thumb"] = "";
            }
            return $response;
        }
        else {
            return false;
        }
    }

    function clear_recent_search($post = []){
        $response = $this->write_db->where("user_id", $post["user_id"])->delete("user_searches");

        if($response) {
            return true;
        }
        else {
            return false;
        }
    }

    function get_recent_search($post = []){
        $all_arr = array();
        $response = $this->read_db->where("user_id", $post["user_id"])
                    ->order_by("updated_at", "desc")
                    ->get("user_searches")->result_array();

        if(!empty($response)) {
            foreach($response as $key => $value) {
                if($value["type"] == '1') {
                    //search country
                    $countries = $this->read_db->select("country_id, name, iso2, latitude, longitude, image_flag, image_flag_presigned", false)
                        ->where("country_id IN (".$value["search_id"].")")
                        ->get("countries")->result_array();

                    if (!empty($countries)) {
                        foreach($countries as $k1 => $v1) {
                            $v1["flag"] = $_ENV['S3_PATH'] . $v1['image_flag'] . $v1["image_flag_presigned"];
                            $v1["tag"] = 1;
                            $v1["tag_name"] = 'Country';
                            array_push($all_arr, $v1);
                        }
                    }
                }
                else if($value["type"] == '2') {
                    //search station
                    $stations = $this->read_db->select("station_id, image_profile, image_profile_presigned, name, city, latitude, longitude", false)
                        ->where("station_id IN (".$value["search_id"].")")
                        ->where('status', 1)
                        ->get('stations')
                        ->result_array();

                    if (!empty($stations)) {
                        foreach($stations as $k2 => $v2) {
                            if(is_null($v2["city"])) {
                                $v2["city"] = "";
                            }

                            $v2["image"] = $_ENV['S3_PATH'] . $v2['image_profile'] . $v2["image_profile_presigned"];
                            $v2["tag"] = 2;
                            $v2["tag_name"] = 'Radio Station';
                            array_push($all_arr, $v2);
                        }
                    }
                }
                else if($value["type"] == '3') {
                    //search artist
                    $artists = $this->read_db->select("s.artist_id, s.image_profile, s.image_profile_presigned, s.name as artist_name, c.name as country_name", false)
                        ->join("countries c", "c.iso2 = s.country_iso2", "left")
                        ->where("s.artist_id IN (".$value["search_id"].")")
                        ->where('s.status', 1)
                        ->get('artists s')
                        ->result_array();

                    if (!empty($artists)) {
                        foreach($artists as $k3 => $v3) {
                            if(is_null($v3["country_name"])) {
                                $v3["country_name"] = "";
                            }

                            $v3["image"] = $_ENV['S3_PATH'] . $v3['image_profile'] . $v3["image_profile_presigned"];
                            $v3["tag"] = 3;
                            $v3["tag_name"] = 'Artist';
                            array_push($all_arr, $v3);
                        }
                    }
                }
                else if($value["type"] == '4') {
                    //search talkshow
                    $talkshow = $this->read_db->select("s.show_id, s.image_cover, s.name, s.image_cover_presigned, c.name as station_name", false)
                        ->join("stations c", "c.station_id = s.station_id", "left")
                        ->where("s.show_id IN (".$value["search_id"].")")
                        ->where('s.status', 1)
                        ->get('shows s')
                        ->result_array();

                    if (!empty($talkshow)) {
                        foreach($talkshow as $k4 => $v4) {
                            if(is_null($v4["station_name"])) {
                                $v4["station_name"] = "";
                            }

                            $v4["image"] = $_ENV['S3_PATH'] . $v4['image_cover'] . $v4["image_cover_presigned"];
                            $v4["tag"] = 4;
                            $v4["tag_name"] = 'Radio Show';
                            array_push($all_arr, $v4);
                        }
                    }
                }
                else if($value["type"] == '5') {
                    //search track
                    $track = $this->read_db->select("track_id, name as track_name", false)
                        ->where("track_id IN (".$value["search_id"].")")
                        ->get('tracks')
                        ->result_array();

                    if (!empty($track)) {
                        foreach($track as $k5 => $v5) {
                            // if(is_null($v5["artwork_url"])) {
                            //     $v5["artwork_url"] = "";
                            // }

                            // if(is_null($v5["artist_name"])) {
                            //     $v5["artist_name"] = "";
                            // }

                            $v5["tag"] = 5;
                            $v5["tag_name"] = 'Track';
                            array_push($all_arr, $v5);
                        }
                    }
                }
                else if($value["type"] == '6') {
                    $all_arr = array();
                    //search host
                    // $host = $this->read_db->select("s.id, s.image, s.artist_name, c.name as country_name", false)
                    //     ->join("artists s", "s.id = th.host_id")
                    //     ->join("country c", "c.code2l = s.country", "left")
                    //     ->where("s.id IN (".$value["serached_id"].")")
                    //     ->where('th.status', 1)
                    //     ->group_by("th.host_id")
                    //     ->get('show_hosts th')
                    //     ->result_array();

                    // if (!empty($host)) {
                    //     foreach($host as $k6 => $v6) {
                    //         if(is_null($v6["country_name"])) {
                    //             $v6["country_name"] = "";
                    //         }

                    //         if(is_null($v6["image"])) {
                    //             $v6["image"] = "";
                    //         }

                    //         $v6["tag"] = 6;
                    //         $v6["tag_name"] = 'Radio Host';
                    //         array_push($all_arr, $v6);
                    //     }
                    // }
                }
            }

            return $all_arr;
        }
        else {
            return $all_arr;
        }
    }

    function add_recent_search($post = []) {
        if(!is_numeric($post["searched_id"])){
            return 3;
        }

        $check = $this->read_db->select("*")
                    ->where("user_id", $post["user_id"])
                    ->where("type", $post["search_for"])
                    ->where("search_id", $post["searched_id"])
                    ->get("user_searches")->row_array();

        if(!empty($check)){
            $insert = $this->write_db->set("updated_at", date('Y-m-d H:i:s'))
                        ->set("updated_by", $post["user_id"])
                        ->where("user_id", $post["user_id"])
                        ->where("type", $post["search_for"])
                        ->where("search_id", $post["searched_id"])
                        ->update("user_searches");

            if($insert) {
                return 1;
            }
            else {
                return 2;
            }
        }
        else {
            $arr = array(
                'user_id' => $post["user_id"],
                'type' => $post["search_for"],
                'search_id' => $post["searched_id"]
            );

            $insert = $this->write_db->insert("user_searches", $arr);

            if($insert) {
                return 1;
            }
            else {
                return 2;
            }
        }
    }

    function search_all($chr) {
        //search by name and number
        $all_arr = array();

        if(!empty($chr)) {
            //search country
            $countries = $this->read_db->select("country_id, name, iso2, latitude, longitude, image_flag, image_flag_presigned", false)
                        ->where("name LIKE '".$chr."%'")
                        ->get("countries")->result_array();

            if (!empty($countries)) {
                foreach($countries as $k1 => $v1) {
                    $v1["flag"] = $_ENV['S3_PATH'] . $v1['image_flag'] . $v1["image_flag_presigned"];
                    $v1["tag"] = 1;
                    $v1["tag_name"] = 'Country';
                    array_push($all_arr, $v1);
                }
            }

            //search station
            $stations = $this->read_db->select("s.station_id, s.image_profile, s.image_profile_presigned, s.name, s.city, s.latitude, s.longitude", false)
                ->where("s.name LIKE '".$chr."%'")
                ->where('s.status', 1)
                ->limit(2)
                ->get('stations s')
                ->result_array();

            if (!empty($stations)) {
                foreach($stations as $k2 => $v2) {
                    if(is_null($v2["city"])) {
                        $v2["city"] = "";
                    }

                    $v2["image"] = $_ENV['S3_PATH'] . $v2['image_profile'] . $v2["image_profile_presigned"];
                    $v2["tag"] = 2;
                    $v2["tag_name"] = 'Radio Station';
                    array_push($all_arr, $v2);
                }
            }

            //search artist
            $artists = $this->read_db->select("s.artist_id, s.image_profile, s.image_profile_presigned, s.name as artist_name, c.name as country_name", false)
                ->join("countries c", "c.iso2 = s.country_iso2", "left")
                ->where("s.name LIKE '".$chr."%'")
                ->where('s.status', 1)
                ->limit(2)
                ->get('artists s')
                ->result_array();

            if (!empty($artists)) {
                foreach($artists as $k3 => $v3) {
                    if(is_null($v3["country_name"])) {
                        $v3["country_name"] = "";
                    }
                        
                    $v3["image"] = $_ENV['S3_PATH'] . $v3['image_profile'] . $v3["image_profile_presigned"];
                    $v3["tag"] = 3;
                    $v3["tag_name"] = 'Artist';
                    array_push($all_arr, $v3);
                }
            }

            //search talkshow
            $talkshow = $this->read_db->select("s.show_id, s.image_cover, s.image_cover_presigned, s.name, c.name as station_name", false)
                ->join("stations c", "c.station_id = s.station_id", "left")
                ->where("s.name LIKE '".$chr."%'")
                ->where('s.status', 1)
                ->limit(2)
                ->get('shows s')
                ->result_array();

            if (!empty($talkshow)) {
                foreach($talkshow as $k4 => $v4) {
                    if(is_null($v4["station_name"])) {
                        $v4["station_name"] = "";
                    }

                    $v4["tag"] = 4;
                    $v4["tag_name"] = 'Radio Show';
                    $v4["image"] = $_ENV['S3_PATH'] . $v4['image_cover'] . $v4["image_cover_presigned"];
                    array_push($all_arr, $v4);
                }
            }

            //search track
            $track = $this->read_db->select("track_id, name as track_name", false)
                ->where("name LIKE '".$chr."%'")
                ->limit(2)
                ->get('tracks')
                ->result_array();

            if (!empty($track)) {
                foreach($track as $k5 => $v5) {
                    // if(is_null($v5["artwork_url"])) {
                    //     $v5["artwork_url"] = "";
                    // }

                    // if(is_null($v5["artist_name"])) {
                    //     $v5["artist_name"] = "";
                    // }

                    $v5["tag"] = 5;
                    $v5["tag_name"] = 'Track';
                    array_push($all_arr, $v5);
                }
            }

            //search host
            // $host = $this->read_db->select("s.id, s.image, s.artist_name, c.name as country_name", false)
            //     ->join("artists s", "s.id = th.host_id")
            //     ->join("country c", "c.code2l = s.country", "left")
            //     ->where("s.artist_name LIKE '".$post["chr"]."%'")
            //     ->where('th.status', 1)
            //     ->group_by("th.host_id")
            //     ->limit(2)
            //     ->get('talkshow_hosts th')
            //     ->result_array();

            // if (!empty($host)) {
            // foreach($host as $k6 => $v6) {
            //     if(is_null($v6["country_name"])) {
            //     $v6["country_name"] = "";
            //     }

            //     if(is_null($v6["image"])) {
            //     $v6["image"] = "";
            //     }

            //     $v6["tag"] = 6;
            //     $v6["tag_name"] = 'Radio Host';
            //     array_push($all_arr, $v6);
            // }
            // }
        }

        return $all_arr;
    }

    function report_reason_list($post = []) {
        $response = $this->read_db->select("user_request_reason_id, type, reason")
                        ->where("type", $post["type"])
                        ->where("status", 1)
                        ->get("user_request_reasons")->result_array();

       
        return $response;
       
    }
}

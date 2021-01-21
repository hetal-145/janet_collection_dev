<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class Show_model extends CI_Model {

    private $read_db, $write_db;

    function __construct() {
        parent::__construct();
        $this->read_db = $this->load->database('read', true);
        $this->write_db = $this->load->database('write', true);
    }

    public function add_talkshow($post = [], $files = []) {
        $notification_array = array();
        //print_r($post); print_r($files); exit;
    //        $check_exists = $this->db->select("*")->where("title", $post["title"])->get("talkshow")->result_array();
    //        if(!empty($check_exists)){
    //            return 1;
    //        }
    //        else {

            //bucket info
    	$credentials = new Aws\Credentials\Credentials($_ENV['S3_KEY'], $_ENV['S3_SECRET']);
    	$s3 = new S3Client([
    	    'region' => 'us-east-2',
    	    'version' => 'latest',
    	    "credentials" => $credentials
    	]);

    	$ext_arr2 = array('gif', 'jpg', 'png', 'jpeg');
            //add image
            if (!empty($files["image_cover"]["name"])) {
                $ext = '.' . pathinfo($files['image_cover']['name'], PATHINFO_EXTENSION);
                $ext1 = pathinfo($files['image_cover']['name'], PATHINFO_EXTENSION);
                $filename = date('YmdHis') . rand() . strtolower($ext);
                $filepath = $files['image_cover']['tmp_name'];
                $mime = mime_content_type($filepath);

                if( in_array($ext1, $ext_arr2) ) {
                    $result = $s3->putObject(array(
                        'Bucket' => $_ENV['BUCKET_NAME'],
                        'Key' => 'talkshow/'.$filename,
                        'SourceFile' => $filepath,
                        'ACL' => 'public-read',
                        'StorageClass' => 'STANDARD',
                        'ContentType'=>$mime
                    ));

                    $result2 = $s3->putObject(array(
                        'Bucket' => $_ENV['BUCKET_NAME'],
                        'Key' => 'talkshow/thumbs/'.$filename,
                        'SourceFile' => $filepath,
                        'ACL' => 'public-read',
                        'StorageClass' => 'STANDARD',
                        'ContentType'=>$mime
                    ));

                    $post["image_cover"] = $filename;
                }
                else {
                    return 'The filtype you are trying to upload is not allowed';
                }
            } else {
                $post["image_cover"] = "";
            }

            if (isset($post["on_air_date"]) && !empty($post["on_air_date"])) {
                $on_air_date = $post["on_air_date"];
            } else {
                $on_air_date = null;
            }

            //add talkshow
            $arr1 = array(
                'name' => $post["name"],
                'image_cover' => $post["image_cover"],
                'blurb' => $post["headline"],
                'station_id' => $post["station_id"]
            );

            $insert = $this->write_db->insert("shows", $arr1);
            $talkshow_id = $this->write_db->insert_id();



            //add host to talk show
            if (isset($post["hosts"]) && !empty($post["hosts"])) {
                $hosts = explode(',', $post["hosts"]);
                foreach ($hosts as $ht) {
                    $arr2 = array(
                        'show_id' => $talkshow_id,
                        'user_id' => $ht
                    );

                    $check = $this->read_db->select("*")->where($arr2)->get("show_hosts")->row_array();

                    if (empty($check)) {
                       /* $userdata = $this->get_user_by_id($ht);
                        $stationdata = $this->get_talkshow_detail_id2($talkshow_id);
                        //insert in notification
                        $insert_notify = array(
                            'to_user_id' => (string) $ht,
                            'contact_person_id' => (string) $talkshow_id,
                            'talkshow_id' => (string) $talkshow_id,
                            'notification_types' => "20",
                            'message' => '@' . $userdata["username"] . ' - ' . $userdata["name"] . ', you have been invited as host at talkshow ' . $stationdata["title"],
                        );
                        array_push($notification_array, $insert_notify);*/

                        //insert in talkshow host table
                        $this->write_db->insert("show_hosts", $arr2);
                    }
                }
            }

            //add special guest to talk show
            //[{"special_guest_id":"1", "schedule_date":"2019-05-07"}, {"special_guest_id":"2", "schedule_date":"2019-05-07"}]
            if (isset($post["special_guest"]) && !empty($post["special_guest"])) {
                $special_guests = json_decode($post["special_guest"], true);
                foreach ($special_guests as $sg) {
                    $arr3 = array(
                        'show_id' => $talkshow_id,
                        'user_id' => $sg["special_guest_id"],
                        'schedule_date' => $sg["schedule_date"]
                    );

                    $check = $this->read_db->select("*")->where($arr3)->get("show_guests")->row_array();

                    if (empty($check)) {
                       /* $userdata = $this->get_user_by_id($sg["special_guest_id"]);
                        $stationdata = $this->get_talkshow_detail_id2($talkshow_id);

                        //insert in notification
                        $insert_notify = array(
                            'to_user_id' => (string) $sg["special_guest_id"],
                            'contact_person_id' => (string) $talkshow_id,
                            'talkshow_id' => (string) $talkshow_id,
                            'notification_types' => "21",
                            'schedule_date' => $sg["schedule_date"],
                            'message' => '@' . $userdata["username"] . ' - ' . $userdata["name"] . ', you have been invited as special guest at talkshow ' . $stationdata["title"],
                        );

                        array_push($notification_array, $insert_notify); */
                        //insert in talkshow guest table
                        $this->write_db->insert("show_guests", $arr3);
                    }
                }
            }

            //add schedule to talk show
            //[{"weekdays":"1", "start_time":"8:00", "end_time":"9:30", "period":"1", "schedule_date":"2019-05-07"}, {"weekdays":"2", "start_time":"8:00", "end_time":"9:30", "period":"2", "schedule_date":"2019-05-07"}]
            if (isset($post["schedule"]) && !empty($post["schedule"])) {
                $schedule = json_decode($post["schedule"], true);
                foreach ($schedule as $sch) {

                   $show_ids =  $this->read_db->select('show_id')->where('station_id', $post['station_id'])->get('shows')->result_array();
                   $show_ids_array = array();
                   foreach ($show_ids as $key => $id) {
                       array_push($show_ids_array, $id['show_id']);
                   }

                   if(!empty($show_ids_array)){
                        //check start time schedule overlapping
                        $check_overlapping = $this->read_db->select("*")
                                //->where("schedule_date", $sch["schedule_date"])
                                ->where("start_time", $sch["start_time"])
                                ->where("period", $sch["period"])
                                ->where_in("show_id", $show_ids_array)
                                ->where("status", 1)
                                ->get('show_schedules')
                                ->result_array();

                        if (!empty($check_overlapping)) {
                            return [4, $sch["schedule_date"]];
                        } else {
                            //check start time schedule overlapping
                            $check_overlapping2 = $this->read_db->select("*")
                                    //->where("schedule_date", $sch["schedule_date"])
                                    ->where("end_time", $sch["end_time"])
                                    ->where("period", $sch["period"])
                                    ->where_in("show_id", $show_ids_array)
                                    ->where("status", 1)
                                    ->get('show_schedules')
                                    ->result_array();

                            if (!empty($check_overlapping2)) {
                                return [5, $sch["schedule_date"]];
                            } else {
                                $arr4 = array(
                                    'show_id' => $talkshow_id,
                                    'weekday' => $sch["weekdays"],
                                    'start_time' => $sch["start_time"],
                                    'end_time' => $sch["end_time"],
                                    'period' => $sch["period"]
                                );
                                $this->write_db->insert("show_schedules", $arr4);
                            }
                        }
                   }else{
                         $arr4 = array(
                                    'show_id' => $talkshow_id,
                                    'weekday' => $sch["weekdays"],
                                    'start_time' => $sch["start_time"],
                                    'end_time' => $sch["end_time"],
                                    'period' => $sch["period"]
                                );
                                $this->write_db->insert("show_schedules", $arr4);
                   }
                }
            }



            if ($insert) {
                return [2, $notification_array];
            } else {
                return [3];
            }
        //}
    }

    public function edit_talkshow($post = [], $files = []) {
        //print_r($post); print_r($files); exit;
        unset($post["user_id"]);
        $check_exists = $this->read_db->select("*")->where("show_id", $post["show_id"])->get("shows")->row_array();
        if (empty($check_exists)) {
            return 1;
        } else {

            //bucket info
            $credentials = new Aws\Credentials\Credentials($_ENV['S3_KEY'], $_ENV['S3_SECRET']);
            $s3 = new S3Client([
                'region' => 'us-east-2',
                'version' => 'latest',
                "credentials" => $credentials
            ]);

            $ext_arr2 = array('gif', 'jpg', 'png', 'jpeg');

            //update image
            if (isset($files["image"]["name"])) {
                if (!empty($files["image"]["name"])) {
                    $ext = '.' . pathinfo($files['image']['name'], PATHINFO_EXTENSION);
                    $ext1 = pathinfo($files['image']['name'], PATHINFO_EXTENSION);
                    $filename = date('YmdHis') . rand() . strtolower($ext);
                    $filepath = $files['image']['tmp_name'];
                    $mime = mime_content_type($filepath);

                    if( in_array($ext1, $ext_arr2) ) {
                        $result = $s3->putObject(array(
                            'Bucket' => $_ENV['BUCKET_NAME'],
                            'Key' => 'talkshow/'.$filename,
                            'SourceFile' => $filepath,
                            'ACL' => 'public-read',
                            'StorageClass' => 'STANDARD',
                            'ContentType'=>$mime
                        ));

                        $result2 = $s3->putObject(array(
                            'Bucket' => $_ENV['BUCKET_NAME'],
                            'Key' => 'talkshow/thumbs/'.$filename,
                            'SourceFile' => $filepath,
                            'ACL' => 'public-read',
                            'StorageClass' => 'STANDARD',
                            'ContentType'=>$mime
                        ));
                        $post["is_s3"] = 1;
                        $post["image"] = $filename;
                    }
                    else {
                        return 'The filtype you are trying to upload is not allowed';
                    }
                } else {
                    $post["image"] = "";
                }
            }
            //add host to talk show
            if (isset($post["hosts"]) && !empty($post["hosts"])) {
                $hosts = explode(',', $post["hosts"]);
                $check_hosts = $this->read_db->select("*")->where("show_id", $post["show_id"])->get("show_hosts")->result_array();
                if (!empty($check_hosts)) {
                    $this->write_db->where("show_id", $post["show_id"])->delete("show_hosts");
                }
                foreach ($hosts as $ht) {
                    $arr2 = array(
                        'show_id' => $post["show_id"],
                        'user_id' => $ht
                    );
                    $this->write_db->insert("show_hosts", $arr2);
                }
                unset($post["hosts"]);
        } else {
                unset($post["hosts"]);
            }

            //add special guest to talk show
            //[{"special_guest_id":"1", "schedule_date":"2019-05-07"}, {"special_guest_id":"2", "schedule_date":"2019-05-07"}]
            if (isset($post["special_guest"]) && !empty($post["special_guest"])) {
                $check_guests = $this->read_db->select("*")->where("show_id", $post["show_id"])->get("show_guests")->result_array();
                if (!empty($check_guests)) {
                    $this->write_db->where("show_id", $post["show_id"])->delete("show_guests");
                }
                $special_guests = json_decode($post["special_guest"], true);
                foreach ($special_guests as $sg) {
                    $arr3 = array(
                        'show_id' => $post["show_id"],
                        'user_id' => $sg["special_guest_id"],
                        'schedule_date' => $sg["schedule_date"]
                    );
                    $this->write_db->insert("show_guests", $arr3);
                }
                unset($post["special_guest"]);
            }

            //add schedule to talk show
            //[{"weekdays":"1", "start_time":"8:00", "end_time":"9:30", "period":"1", "schedule_date":"2019-05-07"}, {"weekdays":"2", "start_time":"8:00", "end_time":"9:30", "period":"2", "schedule_date":"2019-05-07"}]
            if (isset($post["schedule"]) && !empty($post["schedule"])) {
                $check_schedule = $this->read_db->select("*")->where("show_id", $post["show_id"])->get("show_schedules")->result_array();
                if (!empty($check_schedule)) {
                    $this->write_db->where("show_id", $post["show_id"])->delete("show_schedules");
                }
                $schedule = json_decode($post["schedule"], true);
                $data = $this->read_db->select('station_id')->where('show_id', $post['show_id'])->get('shows')->row_array();
                foreach ($schedule as $sch) {

                    $show_ids =  $this->read_db->select('show_id')->where('station_id', $data['station_id'])->get('shows')->result_array();
                    $show_ids_array = array();
                    foreach ($show_ids as $key => $id) {
                       array_push($show_ids_array, $id['show_id']);
                    }
                    if(!empty($show_ids_array)){

                        //check start time schedule overlapping
                        $check_overlapping = $this->read_db->select("*")
                               // ->where("schedule_date", $sch["schedule_date"])
                                ->where("start_time", $sch["start_time"])
                                ->where("period", $sch["period"])
                                ->where("status", 1)
                                ->where_in("show_id", $show_ids_array)
                                ->get('show_schedules')
                                ->result_array();

                        if (!empty($check_overlapping)) {
                            return "4##" . $sch["schedule_date"];
                        } else {
                        //check start time schedule overlapping
                        $check_overlapping2 = $this->read_db->select("*")
                               // ->where("schedule_date", $sch["schedule_date"])
                                ->where("end_time", $sch["end_time"])
                                ->where("period", $sch["period"])
                                ->where("status", 1)
                                ->where_in("show_id", $show_ids_array)
                                ->get('show_schedules')
                                ->result_array();

                        if (!empty($check_overlapping2)) {
                            return "5##" . $sch["schedule_date"];
                        } else {
                            $arr4 = array(
                                'show_id' => $post["show_id"],
                                'weekday' => $sch["weekdays"],
                                'start_time' => $sch["start_time"],
                                'end_time' => $sch["end_time"],
                                'period' => $sch["period"]
                            );
                            $this->write_db->insert("show_schedules", $arr4);
                        }
                    }
                    }else{
                     $arr4 = array(
                                'show_id' => $talkshow_id,
                                'weekday' => $sch["weekdays"],
                                'start_time' => $sch["start_time"],
                                'end_time' => $sch["end_time"],
                                'period' => $sch["period"]
                            );
                            $this->write_db->insert("show_schedules", $arr4);
               }
                }
                unset($post["schedule"]);
            }

            //update talkshow
            $update = $this->write_db->set($post)->where("show_id", $post["show_id"])->update("shows");
            if ($update) {
                return 2;
            } else {
                return 3;
            }
        }
    }

    public function delete_talkshow($post = []) {
        $check_exists = $this->read_db->select("*")->where("show_id", $post["show_id"])->get("shows")->row_array();
        if (!empty($check_exists)) {
            //talkshow host
            $this->write_db->set("status", 0)->where("show_id", $post["show_id"])->update("show_hosts");
            //talkshow special_guest
            $this->write_db->set("status", 0)->where("show_id", $post["show_id"])->update("show_guests");
            //talkshow special_guest
            $this->write_db->set("status", 0)->where("show_id", $post["show_id"])->update("show_schedules");
            //talkshow
            $deleted = $this->write_db->set("status", 0)->where("show_id", $post["show_id"])->update("shows");

            //talkshow notification
            //$this->db->set("status", 0)->where("show_id", $post["show_id"])->update("notification");

            if ($deleted) {
                return 1;
            } else {
                return 2;
            }
        } else {
            return 3;
        }
    }

    public function get_talkshow_by_id($post = []) {
        if (isset($post["person_id"]) && !empty($post["person_id"])) {
            $user_id = $post["person_id"];
        } else {
            $user_id = $post["user_id"];
        }
        $talk_show_details = $this->get_talkshow_details($post["talkshow_id"], $post["user_id"]);
        if (!empty($talk_show_details)) {
            return $talk_show_details;
        }
    }

    public function get_talkshow_details($talkshow_id, $user_id) {
        $talkshow = $this->read_db->select("*")->where('show_id', $talkshow_id)->where('shows.status', 1)->get('shows')->row_array();
        
        if (!empty($talkshow)) {
            $talkshow = array_map(function($val) {
                return $val === NULL ? "" : $val;
            }, $talkshow);
            //talk show image
            if (!empty($talkshow["image"])) {
                $img = $talkshow["image"];
                if($talkshow["is_s3"] == '1') {
                    $talkshow["image"] = $this->image_url($img, '', 'talkshow');
                    $talkshow["image_thumb"] = $this->image_url($img, 'thumb', 'talkshow');
                }
                else {
                    $talkshow["image"] = $this->image_url2($img, '', 'upload/talkshow');
                    $talkshow["image_thumb"] = $this->image_url2($img, 'thumb', 'upload/talkshow');
                }
            } else {
                $talkshow["image"] = "";
                $talkshow["image_thumb"] = "";
            }

            //talkshow schedule
            $talkshow_schedule = $this->read_db->select("*, case weekday when 2 then 'Monday' when 3 then 'Tuesday' when 4 then 'Wednesday' when 5 then 'Thursday' when 6 then 'Friday' when 7 then 'Saturaday' when 1 then 'Sunday' end as weekday_name, case period when 1 then 'AM' when 2 then 'PM' end as time_period", false)
                            ->where('show_id', $talkshow_id)
                            ->where('show_schedules.status', 1)
                            ->get('show_schedules')->result_array();
            if (!empty($talkshow_schedule)) {
                $schedule = array();
                foreach ($talkshow_schedule as $k => $v) {
                    array_push($schedule, $v);
                }
                $talkshow["schedule"] = $schedule;
            }

            //talkshow hosts
            $talkshow_host = $this->read_db->select("*", false)
                            ->where('show_id', $talkshow_id)
                            ->where('status', 1)
                            ->get('show_hosts')->result_array();
            if (!empty($talkshow_host)) {
                $hosts = array();
                foreach ($talkshow_host as $k => $v) {
                    $host_dt = $this->get_user_by_id($v["user_id"]);
                    $host_dt["is_added"] = $this->check_if_host($v["user_id"], $talkshow_id);
                    array_push($hosts, $host_dt);
                }
                $talkshow["hosts"] = $hosts;
            } else {
                $talkshow["hosts"] = array();
            }

            //talkshow special guest
            $talkshow_guest = $this->read_db->select("*", false)
                            ->where('show_id', $talkshow_id)
                            ->where('status', 1)
                            ->get('show_guests')->result_array();
            if (!empty($talkshow_guest)) {
                $guests = array();
                ///create schedule date array
                $schedule_dt = array_values(array_unique(array_column($talkshow_guest, "schedule_date")));
                //print_r($schedule_dt); exit;
                for ($i = 0; $i < count($schedule_dt); $i++) {
                    $guest_list = array();
                    $list = array();
                    foreach ($talkshow_guest as $k => $v) {
                        if ($schedule_dt[$i] == $v["schedule_date"]) {
                            $guest_dt = $this->get_user_by_id($v["user_id"]);
                            if (!empty($guest_dt)) {
                                $guest_dt["is_added"] = $this->check_if_special_guest($v["user_id"], $talkshow_id);
                                $guest_dt["schedule_date"] = $v["schedule_date"];
                                array_push($guest_list, $guest_dt);
                            }
                        }
                    }
                    $list["schedule_date"] = $schedule_dt[$i];
                    $list["guest_list"] = $guest_list;
                    array_push($guests, $list);
                }

                $talkshow["guests"] = $guests;
            } else {
                $talkshow["guests"] = array();
            }

            return $talkshow;

            //get station requested user
            $station_req_user = $this->read_db->select("requested_user_id, country, state, city")->where("station_id", $talkshow["station_id"])->get("stations")->row_array();

            if (!empty($station_req_user["country"])) {
                //get country name
                $country = $this->read_db->select("country.*, timezone.zone")
                                ->join("timezone", "timezone.country_code = country.code2l")
                                ->where('country.id', $station_req_user["country"])
                                ->group_by("country.code2l")
                                ->get('country')->row_array();

                if (!empty($country)) {
                    $talkshow["country"] = $country["name"];
                    $talkshow["countryCode"] = $country["code2l"];
                    $country["flag"] = $_ENV['S3_PATH'] . $country['image_flag'] . $country["image_flag_presigned"];
                    $talkshow["nationality"] = $country;
                    $talkshow["timezone"] = $country["zone"];
                } else {
                    $talkshow["timezone"] = "";
                    $talkshow["country"] = "";
                    $talkshow["countryCode"] = "";
                    $talkshow["nationality"] = new stdClass();
                }
            } else {
                $talkshow["timezone"] = "";
                $talkshow["country"] = "";
                $talkshow["countryCode"] = "";
                $talkshow["nationality"] = new stdClass();
            }

            if (!empty($station_req_user["state"])) {
                //get state name
                $state = $this->read_db->select("name")
                                ->where('id', $station_req_user["state"])
                                ->get('states')->row_array();

                if (!empty($state)) {
                    $talkshow["state"] = $state["name"];
                } else {
                    $talkshow["state"] = "";
                }
            } else {
                $talkshow["state"] = "";
            }

            if (!empty($station_req_user["city"])) {
                //get city name
                $city = $this->read_db->select("name")
                                ->where('id', $station_req_user["city"])
                                ->get('cities')->row_array();

                if (!empty($city)) {
                    $talkshow["city"] = $city["name"];
                } else {
                    $talkshow["city"] = "";
                }
            } else {
                $talkshow["city"] = "";
            }
//            if(!empty($station_req_user["country"])) {
//                //get country name
//                $country = $this->db->select('*')
//                        ->where('id', $station_req_user["country"])
//                        ->get('country')->row_array();
//
//                if(!empty($country)){
//                    $country["flag"] = $country["flag_medium"];
//                }
//
//                $talkshow["nationality"] = $country;
//            }
//            else {
//                $talkshow["nationality"] = new stdClass();
//            }

            $talkshow['user_id'] = $station_req_user["requested_user_id"];
            $talkshow['talkshow_like_count'] = (string) $this->talkshow_like_count($talkshow_id);
            $talkshow['talkshow_share_count'] = $this->talkshow_share_count($talkshow_id);
            $talkshow['is_talkshow_liked'] = $this->is_talkshow_liked($talkshow_id, $user_id);
            $talkshow['is_talkshow_subscribe'] = $this->is_talkshow_subscribe($talkshow_id, $user_id);
            $talkshow['is_tagged'] = $this->check_is_tagged($talkshow_id, $user_id, 4);
            $talkshow["is_seen_tagged"] = $this->check_is_tagged_seen($talkshow_id, $user_id, 4);
            $talkshow['is_invited'] = $this->check_logged_user_host_guest($user_id, $talkshow_id);

            return $talkshow;
        }
    }

    public function check_if_host($host_id, $talkshow_id) {
        $check = $this->read_db->select("*")
                        ->where("user_id", $host_id)
                        ->where("show_id", $talkshow_id)
                        ->where("status", 1)
                        ->get("show_hosts")->row_array();

        if ($check) {
            return "1";
        } else {
            return "0";
        }
    }

    public function talkshow_share_count($talkshow_id) {
        $count = $this->read_db->select("*")
                        ->where("show_id", $talkshow_id)
                        //->group_by("user_id")
                        ->get("user_share_shows")->num_rows();
        return $count;
    }

    public function talkshow_like_count($talkshow_id) {
        $count = $this->read_db->select("*")
                        ->where("show_id", $talkshow_id)
                        ->get("user_favorite_shows")->num_rows();
        return $count;
    }

    public function is_talkshow_liked($talkshow_id, $user_id) {
        $is_liked = $this->read_db->select("*")
                        ->where("show_id", $talkshow_id)
                        ->where("user_id", $user_id)
                        ->get("user_favorite_shows")->num_rows();
        if ($is_liked == 0) {
            return '0';
        } else {
            return '1';
        }
    }

    public function like_unlike_talkshow($post = []) {
        $check_exists = $this->read_db->select("*")
                        ->where("user_id", $post["user_id"])
                        ->where("show_id", $post["talkshow_id"])
                        ->get('user_favorite_shows')->row_array();

        if ($post["like_unlike"] == 1) { 

            if (!empty($check_exists)) {
                return 1;
            } else {
                $arra = array(
                    'user_id' => $post["user_id"],
                    'show_id' => $post["talkshow_id"],
                );
                $insert = $this->write_db->insert('user_favorite_shows', $arra);

                if ($insert) {
                    return 2;
                } else {
                    return 3;
                }
            }
        } else if ($post["like_unlike"] == 0) {

            if (!empty($check_exists)) {
                $remove = $this->write_db->where("user_id", $post["user_id"])
                        ->where("show_id", $post["talkshow_id"])
                        ->delete('user_favorite_shows');

                if ($remove) {
                    return 4;
                } else {
                    return 5;
                }
            } else {
                return 6;
            }
        }
    }

    public function user_favourite_talkshow_list($post = []) {

        if (isset($post["person_id"]) && !empty($post["person_id"])) {
            $user_id = $post["person_id"];
        } else {
            $user_id = $post["user_id"];
        }

        $return_fetch = array();
        $fetch = $this->read_db->select("*")
                        ->where('user_id', $user_id)
                        ->get('user_favorite_shows')->result_array();

        //print_r($fetch);
        if (!empty($fetch)) {
            foreach ($fetch as $key => $value2) {
                $value = array_map(function($val) {
                    return $val === NULL ? "" : $val;
                }, $value2);

                $fetch[$key] = $value;
                /*$where = "schedule_date >= CURDATE()";
                $schedule = $this->db->select("*")->where("show_id", $value["show_id"])->where($where)->get("show_schedules")->result_array();*/
                 
                $schedule = $this->read_db->select("*")->where("show_id", $value["show_id"])->get("show_schedules")->result_array();
                if (!empty($schedule)) {
                    $fetch[$key] = $this->get_talkshow_details($value["show_id"], $user_id);
                } else {
                    $fetch[$key] = array();
                }
            }

            foreach ($fetch as $f) {
                if (!empty($f)) {
                    //print_r($f);
                    array_push($return_fetch, $f);
                }
            }
            return $return_fetch;
        }
    }

    public function make_sub_unsub_talkshow($post = []) {
        $exists = $this->read_db->select("*")
                        ->where('show_id', $post['talkshow_id'])
                        ->where('user_id', $post['user_id'])
                        ->get('user_subscribe_shows')->row_array();

        if ($post["is_subscribe"] == 1) {
            if (!empty($exists)) {
                return 1;
            } else {
                $subscribe_array = array(
                    'show_id' => $post['talkshow_id'],
                    'user_id' => $post['user_id'],
                );
                $this->write_db->insert('user_subscribe_shows', $subscribe_array);
                return 2;
            }
        } else if ($post["is_subscribe"] == 0) {
            if (!empty($exists)) {
                $subscribe_array = array(
                    'show_id' => $post['talkshow_id'],
                    'user_id' => $post['user_id'],
                );

                $this->write_db->where($subscribe_array)->delete('user_subscribe_shows');
                return 3;
            } else {
                return 4;
            }
        }
    }

    public function talkshow_subscriber_list($post = []) {
        $list = array();
        $fetch = $this->read_db->select("user_id")
                        ->where('show_id', $post["talkshow_id"])
                        ->get('user_subscribe_shows')->result_array();
        if (!empty($fetch)) {
            foreach ($fetch as $key => $value) {
                if (isset($post["chr"]) && !empty($post["chr"])) {
                    $response = $this->read_db->select("user_id, name, username, description, profile_image,  is_user_verified, is_live, image_cover", false)
                                    ->where('user_id', $value["user_id"])
                                    ->where('name LIKE "%' . $post["chr"] . '%"')
                                    ->get('user')->row_array();
                } else {
                    $response = $this->read_db->select("u.user_id, name, username, blurb, image_profile, is_user_verified, is_live", false)
                            ->join('fans f', 'f.user_id = u.user_id')
                            ->where('u.user_id', $value["user_id"])
                            ->get('users u')->row_array();
                }

                if (!empty($response)) {
                    if (!empty($response["image_profile"])) {
                        $img = $response["image_profile"];

                        if($response["is_s3"] == '1'){
                            $response["image_profile"] = $_ENV['S3_PATH'] . $img;
                            $response["profile_image_thumb"] = $_ENV['S3_PATH'] . $img;
                        }
                        else {
                            $response["image_profile"] = "";
                            $response["profile_image_thumb"] = "";
                        }
                    } else {
                        $response["image_profile"] = "";
                        $response["profile_image_thumb"] = "";
                    }
                    if (!empty($response["image_cover"])) {
                        $img = $response["cover_image"];

                        if($response["is_s3"] == '1'){
                            $response["image_cover"] = $_ENV['S3_PATH'] . $img;
                            $response["cover_image_thumb"] = $_ENV['S3_PATH'] . $img;
                        }
                        else {
                            $response["image_cover"] = $_ENV['S3_PATH'] . $img;
                            $response["cover_image_thumb"] = $_ENV['S3_PATH'] . $img;
                        }
                    } else {
                        $response["image_cover"] = "";
                        $response["cover_image_thumb"] = "";
                    }
                    //$fetch[$key] = $response;
                    $response22 = $this->user_model->check_connection_user($post["user_id"], $value["user_id"]);
                    $response = array_merge($response, $response22);
                    array_push($list, $response);
                }
            }
            return $list;
        }
    }

    public function add_host_to_talkshow($post = []) {
        $insert_host = array(
            'user_id' => $post["host_id"],
            'show_id' => $post["talkshow_id"]
        );

        $check = $this->read_db->select("*")->where($insert_host)->get("show_hosts")->row_array();

        $userdata = $this->user_model->get_user_by_id($post["host_id"]);
        $stationdata = $this->get_talkshow_details($post["talkshow_id"], $post['user_id']);

        //print_r($stationdata); exit;
        //insert in notification
        $insert_notify = array(
            'to_user_id' => $post["host_id"],
            'contact_person_id' => $post["talkshow_id"],
            'talkshow_id' => $post["talkshow_id"],
            'notification_types' => "20",
            'message' => '@' . $userdata["username"] . ' - ' . $userdata["name"] . ', you have been invited as host at talkshow ' . $stationdata["name"],
        );

        if (empty($check)) {
            //$this->notification_model->create_notification($insert_notify);
            //$this->notification_model->send($insert_notify);
            //insert in talkshow host table
            $this->write_db->insert("show_hosts", $insert_host);
            return 1;
        } else if (!empty($check)) {
            if ($check["status"] == 2) {
                $this->write_db->where($insert_host)->delete("show_hosts");
                //$this->notification_model->create_notification($insert_notify);
                //$this->notification_model->send($insert_notify);
                return 1;
            } else if ($check["status"] == 1) {
                return 2;
            } else if ($check["status"] == 0) {
               // $this->notification_model->create_notification($insert_notify);
                //$this->notification_model->send($insert_notify);
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

    public function add_guest_to_talkshow($post = []) {
        $insert_host = array(
            'user_id' => $post["guest_id"],
            'show_id' => $post["talkshow_id"],
            'schedule_date' => $post["schedule_date"]
        );

        $check = $this->read_db->select("*")->where($insert_host)->get("show_guests")->row_array();

        $userdata = $this->user_model->get_user_by_id($post["guest_id"]);
        $stationdata = $this->get_talkshow_details($post["talkshow_id"], $post['user_id']);

        //print_r($stationdata); exit;
        //insert in notification
        $insert_notify = array(
            'to_user_id' => $post["guest_id"],
            'contact_person_id' => $post["talkshow_id"],
            'talkshow_id' => $post["talkshow_id"],
            'notification_types' => "20",
            'message' => '@' . $userdata["username"] . ' - ' . $userdata["name"] . ', you have been invited as special guest at talkshow ' . $stationdata["name"],
        );

        if (empty($check)) {
           // $this->create_notification($insert_notify);
            //$this->m_notify->send($insert_notify);
            //insert in talkshow host table
            $this->write_db->insert("show_guests", $insert_host);
            return 1;
        } else if (!empty($check)) {
            if ($check["status"] == 2) {
                $this->write_db->set("status", 0)->where($insert_host)->update("show_guests");
                //$this->create_notification($insert_notify);
        $this->m_notify->send($insert_notify);
                return 1;
            } else if ($check["status"] == 1) {
                return 2;
            } else if ($check["status"] == 0) {
               // $this->create_notification($insert_notify);
                // $this->m_notify->send($insert_notify);
                return 1;
            }
        } else {
            return false;
        }
    }

    function get_user_by_id($user_id) {
        $user_data = $this->read_db->select("u.*, f.fan_id, f.name, f.gender, f.birthdate, f.demisedate, f.country_id, f.country_iso2, f.hometown, f.tags, f.blurb, f.image_profile, f.image_cover, f.is_live, up.is_profile_public, up.display_online_status, up.display_hometown, up.allow_direct_messages, up.allow_mentions", false)
                ->join("fans f", "u.user_id = f.user_id", "left")
                ->join("user_privacy_settings up", "u.user_id = up.user_id", "left")
                ->where('u.user_id', $user_id)
                ->get('users u')
                ->row_array();

        // print_r($user_data); exit;

        if (!empty($user_data)) {

            if (!empty($user_data["image_profile"])) {
                $img = $user_data["image_profile"];
                $user_data["profile_image"] = $_ENV['S3_PATH'] . $img;
                $user_data["profile_image_thumb"] = $_ENV['S3_PATH'] . $img; //$this->pic_url($img, 'thumb');
            } else {
                $user_data["profile_image_thumb"] = "";
            }

            if (!empty($user_data["image_cover"])) {
                $img1 = $user_data["image_cover"];
                $user_data["cover_image"] = $_ENV['S3_PATH'] . $img1;
                $user_data["cover_image_thumb"] = $_ENV['S3_PATH'] . $img1; //$this->pic_url($img1, 'thumb');
            } else {
                $user_data["cover_image_thumb"] = "";
            }

            if (!empty($user_data["country_id"])) {
                //get country name
                $country = $this->read_db->select('*')
                                ->where('country_id', $user_data["country_id"])
                                ->get('countries')->row_array();

                if (!empty($country)) {
                    $country["flag"] = $_ENV['S3_PATH'] . $country['image_flag'] . $country["image_flag_presigned"];
                }

                $user_data["nationality"] = $country;
            } else {
                $user_data["nationality"] = new stdClass();
            }

            unset($user_data["password"]);
            unset($user_data["password_temp"]);

            $user_data2 = array_map(function($value) {
                return $value === NULL ? "" : $value;
            }, $user_data);

            return $user_data2;
        }
    }

    public function accept_reject_guest_to_talkshow($post = []) {
        $where = array(
            'user_id' => $post["user_id"],
            'show_id' => $post["talkshow_id"],
            'schedule_date' => $post["schedule_date"]
        );

        $check = $this->read_db->select("*")->where($where)->get("show_guests")->row_array(); 

        if (!empty($check)) {
            $userdata = $this->get_user_by_id($post["user_id"]);
            $stationdata = $this->get_talkshow_details($post["talkshow_id"], $post['user_id']);

            if ($post["status"] == 1) {
                //insert in notification
                /*$insert_notify = array(
                    'to_user_id' => $stationdata["requested_user_id"],
                    'contact_person_id' => $post["user_id"],
                    'talkshow_id' => $post["talkshow_id"],
                    'notification_types' => "18",
                    'message' => '@' . $userdata["username"] . ' - ' . $userdata["name"] . ', has accepted your invite as special guest at talkshow ' . $stationdata["name"],
                );
                $this->create_notification($insert_notify);

                $this->db->where("to_user_id", $post["user_id"])
                        ->where("contact_person_id", $post["talkshow_id"])
                        ->where("talkshow_id", $post["talkshow_id"])
                        ->where("notification_type", "21")
                        ->delete('notification');

                $this->m_notify->send($insert_notify);*/
                $this->write_db->set("status", $post["status"])->where($where)->update("show_guests");
               /* $this->db->insert("talkshow_invite", array(
                    'user_id' => $post["user_id"],
                    'talkshow_id' => $post["talkshow_id"],
                    'type' => 2
                ));*/
                return 1;
            } else if ($post["status"] == 2) {
                //insert in notification
               /* $insert_notify = array(
                    'to_user_id' => $stationdata["requested_user_id"],
                    'contact_person_id' => $post["user_id"],
                    'talkshow_id' => $post["talkshow_id"],
                    'notification_types' => "19",
                    'message' => '@' . $userdata["username"] . ' - ' . $userdata["name"] . ', has rejected your invite as special guest at talkshow ' . $stationdata["title"],
                );
                $this->create_notification($insert_notify);

                $this->db->where("to_user_id", $post["user_id"])
                        ->where("contact_person_id", $post["talkshow_id"])
                        ->where("talkshow_id", $post["talkshow_id"])
                        ->where("notification_type", "21")
                        ->delete('notification');

                $this->m_notify->send($insert_notify);*/
                $this->write_db->where($where)->delete("show_guests");
                return 2;
            }
        } else {
            return false;
        }
    }

    public function check_if_special_guest($guest_id, $talkshow_id) {
        $check = $this->read_db->select("*")
                        ->where("user_id", $guest_id)
                        ->where("show_id", $talkshow_id)
                        ->where("status", 1)
                        ->get("show_guests")->row_array();

        if ($check) {
            return "1";
        } else {
            return '0';
        }
    }

    public function accept_reject_host_to_talkshow($post = []) {
        $where = array(
            'user_id' => $post["user_id"],
            'show_id' => $post["talkshow_id"]
        );

        $check = $this->read_db->select("*")->where($where)->get("show_hosts")->row_array();
        //echo $this->db->last_query();exit;
        if (!empty($check)) {
            $userdata = $this->get_user_by_id($post["user_id"]);
            $stationdata = $this->get_talkshow_details($post["talkshow_id"], $post['user_id']);


            if ($post["status"] == 2) { 
                //insert in notification
                /*$insert_notify = array(
                    'to_user_id' => $stationdata["requested_user_id"],
                    'contact_person_id' => $post["user_id"],
                    'talkshow_id' => $post["talkshow_id"],
                    'notification_types' => "18",
                    'message' => '@' . $userdata["username"] . ' - ' . $userdata["name"] . ', has accepted your invite as host at talkshow ' . $stationdata["title"],
                );
                $this->create_notification($insert_notify);

                $this->db->where("to_user_id", $post["user_id"])
                        ->where("contact_person_id", $post["talkshow_id"])
                        ->where("talkshow_id", $post["talkshow_id"])
                        ->where("notification_type", "20")
                        ->delete('notification');

                $this->m_notify->send($insert_notify);*/
                $this->write_db->set("status", $post["status"])->where($where)->update("show_hosts");
               /* $this->db->insert("talkshow_invite", array(
                    'user_id' => $post["user_id"],
                    'talkshow_id' => $post["talkshow_id"],
                    'type' => 1
                ));*/
                return 1;
            } else if ($post["status"] == 3) { 
                //insert in notification
                /*$insert_notify = array(
                    'to_user_id' => $stationdata["requested_user_id"],
                    'contact_person_id' => $post["user_id"],
                    'talkshow_id' => $post["talkshow_id"],
                    'notification_types' => "19",
                    'message' => '@' . $userdata["username"] . ' - ' . $userdata["name"] . ', has rejected your invite as host at talkshow ' . $stationdata["title"],
                );
                $this->create_notification($insert_notify);

                $this->db->where("to_user_id", $post["user_id"])
                        ->where("contact_person_id", $post["talkshow_id"])
                        ->where("talkshow_id", $post["talkshow_id"])
                        ->where("notification_type", "20")
                        ->delete('notification');

                $this->m_notify->send($insert_notify);*/
                $this->write_db->where($where)->delete("show_hosts");
                return 2;
            } 
        } else { 
            return false;
        }
    }

    public function share_talkshow($post = []) {
        $user_ids = explode(",", $post["ids"]);
        unset($post["ids"]);
        $userdata = $this->get_user_by_id($post["user_id"]);
        $talkshowdata = $this->get_talkshow_details($post["talkshow_id"], $post['user_id']);
        $msg = '@' . $userdata["username"] . ' - ' . $userdata["name"] . ', has shared ' . $talkshowdata["name"] . '';

        foreach ($user_ids as $ids) {
            $check = $this->read_db->select("*")
                            ->where("user_message_id", $ids)
                            ->where("show_id", $post["talkshow_id"])
                            ->get("user_share_shows")->row_array();

          

            //print_r($check); exit;

            if (empty($check)) {
                $insert = $this->write_db->insert("user_share_shows", array(
                    "user_message_id" => $ids,
                    "show_id" => $post["talkshow_id"]
                ));

                //insert in chat table
               /* $dat = substr(date('YmdHisu'), 0, -3);
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
                    'chat_type' => 4,
                    'user_type' => 4,
                    'tagged_users' => null,
                    'song_name' => null,
                    'is_shared' => 1,
                    'is_shared_type' => 3,
                    'group_id' => $post["talkshow_id"],
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

                $this->db->insert("chat", array_merge($ins_chat, $send_chat));
                $this->db->insert("chat", array_merge($ins_chat, $received_chat));*/

                if ($insert) {
                    //insert in notification
                   /* $insert_notify = array(
                        'to_user_id' => (string) $ids,
                        'contact_person_id' => (string) $post["talkshow_id"],
                        'talkshow_id' => (string) $post["talkshow_id"],
                        'notification_types' => "24",
                        'message' => $msg,
                        "description" => $description,
                    );

                    $this->m_api->create_notification($insert_notify);
                    $this->m_notify->send($insert_notify);*/
                }
            } else {
               // unset($post["is_prv_share"]);
                
                //$this->db->set('show_id', $post['talkshow_id'])->where("show_id", $post["talkshow_id"])->where("user_message_id", $ids)->update("user_share_shows");
                //insert in notification
               /* $insert_notify1 = array(
                    'to_user_id' => (string) $ids,
                    'contact_person_id' => (string) $post["talkshow_id"],
                    'talkshow_id' => (string) $post["talkshow_id"],
                    'notification_types' => "24",
                    'message' => $msg,
                    "description" => $description,
                );

                $this->m_api->create_notification($insert_notify1);
                $this->m_notify->send($insert_notify1);*/
            }
        }

        $talkshow_count = $this->talkshow_share_count($post["talkshow_id"]);

        return $talkshow_count;
    }

    public function talkshow_listeners($post = []) {
        $fetch = array();
        $response = $this->read_db->select("u.user_id, name, email, username, image_profile, blurb, is_user_verified, ", false)
                        ->join('fans f','f.user_id = u.user_id')
                        //->where('current_talkshow_id', $post["talkshow_id"])
                        //->where("user_id != " . $post["user_id"] . "")
                        ->get('users u')->result_array();
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
            }

            $fetch["user_data"] = $response;
            $fetch["total_listeners"] = (string) count($response);
            return $fetch;
        }
    }

    public function talkshow_listener_common_connection($post = []) {
        $where = "connect_user_id = " . $post["user_id"] . " OR user_id = " . $post["user_id"] . "";
        $connections = $this->read_db->select("*")->where($where)->where('action', 1)->get('user_connections')->result_array();
        $fetch = array();

        if (!empty($connections)) {
            $send_user_arr = array_column($connections, 'user_id');
            $send_user_arr2 = array_column($connections, 'connect_user_id');
            $send_user_arr_list = implode(',', array_unique(array_merge($send_user_arr, $send_user_arr2)));

            $where2 = "u.user_id IN (" . $send_user_arr_list . ") and u.user_id != " . $post["user_id"] . "";
            $response = $this->read_db->select("u.user_id, name, email, username, image_profile, blurb, is_user_verified, image_cover", false)
                            ->join('fans f','f.user_id = u.user_id')
                            ->where($where2)
                            //->where('current_talkshow_id', $post["talkshow_id"])
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

                    $response22 = $this->user_model->check_connection_user($post["user_id"], $value["user_id"]);
                    $response[$k] = array_merge($response[$k], $response22);
                }

                $fetch["user_data"] = $response;
                $fetch["total_listeners"] = (string) count($response);
                return $fetch;
            }
        }
    }


    public function recommended_talkshow($post = []) {
        if (isset($post["offset"]) && !empty($post["offset"])) {
            $offset = $post["offset"];
        } else {
            $offset = 0;
        }

        if (isset($post["chr"]) && !empty($post["chr"])) {
            $where2 = "shows.status = 1 and shows.title LIKE '%" . $post["chr"] . "%'";
        } else {
            $where2 = "shows.status = 1";
        }

        //get played stations
        $played_station = $this->read_db->select("*")
//                ->where("user_id", $post["user_id"])
                        ->group_by("station_id")
                        ->order_by("user_play_stations_id", "desc")
                        ->get("user_play_stations")->result_array();
        $station_ids = implode(",", array_column($played_station, "station_id"));

        /* Main Query */
        $st_where = "shows.station_id IN (" . $station_ids . ")";
        $fetch = $this->read_db->select("shows.show_id, stations.user_id")
                        ->join('stations', 'stations.station_id = shows.station_id')
                        ->where($st_where)
                        ->where($where2)
                        ->limit(LIMIT)
                        ->offset($offset)
                        ->get('shows')->result_array();
        if (!empty($fetch)) {
            foreach ($fetch as $key => $value2) {
                $value = array_map(function($val) {
                    return $val === NULL ? "" : $val;
                }, $value2);

                $fetch[$key] = $value;
                $fetch[$key] = $this->get_talkshow_details($value["show_id"], $post["user_id"]);
                $fetch[$key]["station_user"] = $value["user_id"];
            }
            //print_r($fetch);
            return $fetch;
        } else {
            return array();
        }
    }

}
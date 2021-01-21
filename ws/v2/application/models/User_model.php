<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    private $read_db, $write_db;

	function __construct() {
        parent::__construct();
        $this->load->model(basename(__DIR__)."/media_model");
        $this->read_db = $this->load->database('read', true);
        $this->write_db = $this->load->database('write', true);
    }

    function get_profile($post=[]) {
        $user_data = $this->user_details($post["user_id"], $post["user_id"]);
        return $user_data;
    }

    function user_details($user_id, $logged_user_id) {
        $user_data = $this->get_user_by_id($user_id);

        if(!empty($user_data)) {

            $post["user_id"] = $user_id;
            $post["logged_user_id"] = $logged_user_id;

            //favourite
            /*$favourite = $this->read_db->select("*")
                    ->where('status', 1)
                    ->where('user_id', $logged_user_id)
                    ->where('favourite_user_id', $user_id)
                    ->get('user_favourite_user')
                    ->row_array();
            //print_r($favourite);
            if (!empty($favourite)) {
                $user_data["favourite"] = "1";
            } else {
                $user_data["favourite"] = "0";
            }

            //favourite_count
            $user_data["favourite_count"] = (string) $this->read_db->select("user_favourite_user_id")
                            ->where('status', 1)
                            ->where('favourite_user_id', $user_id)
                            ->get('user_favourite_user')
                            ->num_rows();

            //get favourite stations
            $favourite_stations = $this->favourite_station_list($post);
            if (!empty($favourite_stations)) {
                $user_data["favourite_stations"] = $favourite_stations;
            } else {
                $user_data["favourite_stations"] = [];
            }

            //get favourite artist
            $favourite_artists = $this->user_favourite_artist_list($post);
            if (!empty($favourite_artists)) {
                $user_data["favourite_artists"] = $favourite_artists;
            } else {
                $user_data["favourite_artists"] = [];
            }

            //get favourite tracks
            $favourite_tracks = $this->user_favourite_track_list_with_logged_user_flag($post);
            if (!empty($favourite_tracks)) {
                $user_data["favourite_tracks"] = $favourite_tracks;
            } else {
                $user_data["favourite_tracks"] = [];
            }

            //get favourite talkshow
            $favourite_talkshows = $this->user_favourite_talkshow_list($post);
            if (!empty($favourite_talkshows)) {
                $user_data["favourite_talkshows"] = $favourite_talkshows;
            } else {
                $user_data["favourite_talkshows"] = [];
            }

            //get subscriber stations
            $subscriber_stations = $this->subscriber_station_list($post);
            if (!empty($subscriber_stations)) {
                $user_data["subscriber_stations"] = $subscriber_stations;
            } else {
                $user_data["subscriber_stations"] = [];
            }

            //get subscriber artist
            $subscriber_artists = $this->user_subscribed_artist_list($post);
            if (!empty($subscriber_artists)) {
                $user_data["subscriber_artists"] = $subscriber_artists;
            } else {
                $user_data["subscriber_artists"] = [];
            }

            //get subscriber talkshow
            $subscriber_talkshows = $this->user_subscribed_talkshow_list($post);
            if (!empty($subscriber_talkshows)) {
                $user_data["subscriber_talkshows"] = $subscriber_talkshows;
            } else {
                $user_data["subscriber_talkshows"] = [];
            }*/

            $user_data["favourite"] = "0";
            $user_data["favourite_stations"] = [];
            $user_data["favourite_artists"] = [];
            $user_data["favourite_tracks"] = [];
            $user_data["favourite_talkshows"] = [];
            $user_data["subscriber_stations"] = [];
            $user_data["subscriber_artists"] = [];
            $user_data["subscriber_talkshows"] = [];

            return $user_data;
        }
    }

    function update_profile($post = []) {
        // print_r($post); exit;
        $userdata = $this->get_user_by_id($post["user_id"]);
        // print_r($userdata); exit;

        if (isset($post["is_profile_image"]) && $post["is_profile_image"] == 1 && !empty($post["image_ext"])){
            $_POST['source_type'] = 'user';
            $_POST['source_id'] = $userdata["user_id"];

            if(!empty($userdata["image_profile"])) {
                $_POST['filename'] = $userdata["image_profile"];
                $_POST['extension'] = $post['image_ext'];
            }
            else {
                $_POST['extension'] = $post['image_ext'];
            }
            
            //get presigned ulr to upload image and POST method
            $pic_res = $this->media_model->index_post();
            print_r($pic_res); exit;
            $post['image_profile'] = $pic_res["filename"];
        }
        else {
            $post['image_profile'] = $userdata["image_profile"];
        }

        if (isset($post["is_cover_image"]) && $post["is_cover_image"] == 1 && !empty($post["cover_image_ext"])){
            $_POST['source_type'] = 'user';
            $_POST['source_id'] = $userdata["user_id"];

            if(!empty($userdata["image_cover"])) {
                $_POST['filename'] = $userdata["image_cover"];
                $_POST['extension'] = $post['cover_image_ext'];
            }
            else {
                $_POST['extension'] = $post['cover_image_ext'];
            }
            
            //get presigned ulr to upload image and POST method
            $pic_res = $this->media_model->index_post();
            print_r($pic_res); exit;
            $post['image_cover'] = $pic_res["filename"];
        }
        else {
            $post['image_cover'] = $userdata["image_cover"];
        }

            if (!empty($_FILES["cover_image"]['name']) && !empty($_FILES["cover_image"]['name'])) {
                $_POST['source_type'] = 'user';
                $_POST['source_id'] = $userdata["user_id"];
                $_POST['filename'] = $_FILES["cover_image"]['name'];
                $_FILES["file"] = $_FILES["cover_image"];

                $aObj = new Media();
                //get presigned ulr to upload image and POST method
                $pic_res = $aObj->index_post();

                //upload image with presigned url and PUT method
                $_POST["url"] = $pic_res["url"];
                $pic_res1 = $aObj->upload_image_post();
                unset($aObj);
                // print_r($pic_res); exit;
                $post['image_cover'] = $pic_res["filename"];
            }
            else {
                $post['image_cover'] = $userdata["image_cover"];
            }

        if (isset($post["username"]) && !empty($post["username"])) {
            if ($userdata["username"] != $post["username"]) {
                $chk = $this->check_username_id($post);
                if ($chk === 4) {
                    return 1;
                }
            }
        }

        if(isset($post['nationality']) && !empty($post['nationality'])) {
            $countryiso2 = $this->read_db->select("iso2")->where("country_id", $post['nationality'])->get("countries")->row_array();
            $post["country_iso2"] = $countryiso2["iso2"];
        }
        else {
            $post["country_iso2"] = null;
        }

        if(isset($post['dob']) && !empty($post['dob'])) {
            $post["dob"] = $post['dob'];
        }
        else {
            $post["dob"] = $userdata['birthdate'];
        }

        // insert in user fans table
        $check = $this->read_db->select('fan_id')->where('user_id', $post['user_id'])->get("fans")->row_array();

        $arry1 = array(
            "user_id" => $post['user_id'],
            "name" => (isset($post["name"]) && !empty($post["name"])) ? $post["name"] : $userdata["name"],
            "gender" => (isset($post["gender"]) && !empty($post["gender"])) ? $post["gender"] : $userdata["gender"],
            "birthdate" => $post['dob'],
            "country_id" => (isset($post["nationality"]) && !empty($post["nationality"])) ? $post["nationality"] : $userdata["country_id"],
            "country_iso2" => $post["country_iso2"],
            "hometown" => (isset($post["city_state"]) && !empty($post["city_state"])) ? $post["city_state"] : $userdata["hometown"],
            "blurb" => (isset($post["description"]) && !empty($post["description"])) ? $post["description"] : $userdata["blurb"],
            "image_profile" => $post['image_profile'],
            "image_cover" => $post['image_cover']
        );

        if(!empty($check)) {
            $this->write_db->set($arry1)->set("updated_by", $post['user_id'])->set("updated_at", date('Y-m-d H:i:s'))->where('fan_id', $check['fan_id'])->update("fans");
        }
        else {
            $this->write_db->insert("fans", $arry1);
        }

        // insert in user privacy_settings
        $check1 = $this->read_db->select('user_privacy_setting_id')->where('user_id', $post['user_id'])->get("user_privacy_settings")->row_array();

        $arry2 = array(
            "user_id" => $post['user_id'],
            "is_profile_public" => (isset($post["is_profile_public"])) ? $post["is_profile_public"] : $userdata["is_profile_public"],
        );

        if(!empty($check1)) {
            $this->write_db->set($arry2)->set("updated_by", $post['user_id'])->set("updated_at", date('Y-m-d H:i:s'))->where('user_privacy_setting_id', $check1['user_privacy_setting_id'])->update("user_privacy_settings");
        }
        else {
            $this->write_db->insert("user_privacy_settings", $arry2);
        }

        // insert in user notification
        $check2 = $this->read_db->select('user_notification_setting_id')->where('user_id', $post['user_id'])->get("user_notification_settings")->row_array();

        $arry3 = array(
            "user_id" => $post['user_id'],
            "is_push_notification" => '1',
            "is_artist_notification" => '1',
            "is_station_notification" => '1',
            "is_direct_message_notification" => '1',
            "is_tune_request_notification" => '1',
            "is_connection_request_notification" => '1',
            "is_post_comment_notification" => '1',
            "is_email_marketing_notification" => '1',
        );

        if(!empty($check2)) {
            $this->write_db->set($arry3)->set("updated_by", $post['user_id'])->set("updated_at", date('Y-m-d H:i:s'))->where('user_notification_setting_id', $check2['user_notification_setting_id'])->update("user_notification_settings");
        }
        else {
            $this->write_db->insert("user_notification_settings", $arry3);
        }

        //update in user
        $arry4 = array(
            "username" => (isset($post["username"]) && !empty($post["username"])) ? $post["username"] : $userdata["username"],
            "email" => $userdata['email'],
            "phone_code" => (isset($post['phone_code']) && !empty($post["phone_code"])) ? $post["phone_code"] : $userdata["phone_code"],
            "phone_number" => (isset($post['phone_number']) && !empty($post["phone_number"])) ? $post["phone_number"] : $userdata["phone_number"],
            "is_profile_updated" => 1,
            "last_profile_updated" => date('Y-m-d H:i:s'),
            "updated_by" => $post['user_id'],
            "updated_at" => date('Y-m-d H:i:s')
        );

        $updated = $this->write_db
                ->where('user_id', $post['user_id'])
                ->set($arry4)
                ->update('users');
        if ($updated) {
            return 2;
        }
    }

    function search_user($post = []) {        
        $user_arr = array();
        $list =array();
        $result = $this->read_db->select("u.user_id", false)
                ->join('fans f', 'f.user_id = u.user_id')
                ->where("u.user_id != " . $post['user_id'] . " AND u.status = 1 AND u.is_profile_updated = 1 AND (f.name LIKE '" . $post["chr"] . "%' OR u.username LIKE '" . $post["chr"] . "%')")
                ->where("u.user_type_id", 1)
                ->order_by('f.name', 'asc')
                ->get('users u')
                ->result_array();

        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $users = $this->get_user_by_id($value["user_id"]);
                $response = $this->check_connection_user($post['user_id'], $value['user_id']);
                if(!empty($response)) {
                    $users = array_merge($users, $response);
                }

                array_push($user_arr, $users);

            }
            return $user_arr;
        }
    }

    function suggestion_for_user($user_id) {
        $suggestions = array();
        $connections = $this->read_db->select("user_connection_id, user_id, connect_user_id, action")
                        ->where('action != 1')
                        ->where('user_id', $user_id)
                        ->get('user_connections')->result_array();

        foreach ($connections as $key => $conn) {
            $connections2 = $this->read_db->select("user_connection_id, user_id, connect_user_id, action")
                            ->where('user_id', $conn["connect_user_id"])
                            ->where('action', 1)
                            ->get('user_connections')->result_array();

            $connections = array_merge($connections, $connections2);
        }

        $connections3 = $this->read_db->select("user_connection_id, user_id, connect_user_id, action")
                        ->where('connect_user_id', $user_id)
                        ->where('action != 1')
                        ->get('user_connections')->result_array();

        if (!empty($connections3)) {
            foreach ($connections3 as $key1 => $conn1) {
                $connections4 = $this->read_db->select("user_connection_id, user_id, connect_user_id, action")
                                ->where('user_id', $conn1["user_id"])
                                ->where('action', 1)
                                ->get('user_connections')->result_array();
                $connections = array_merge($connections, $connections4);
            }
        }

        $contact_id = array();

        $unique_arr1 = $this->read_db->select("user_connection_id, user_id, connect_user_id, action")
                        ->where('action', 1)
                        ->where('user_id', $user_id)
                        ->or_where('connect_user_id', $user_id)
                        ->get('user_connections')->result_array();

        $arr1 = array_column($unique_arr1, 'user_id');
        $arr2 = array_column($unique_arr1, 'connect_user_id');
        $arr3 = array_unique(array_merge($arr1, $arr2));

        foreach ($connections as $ck => $cv) {
            if ($cv["connect_user_id"] != $user_id) {
                $uid = $cv["connect_user_id"];
            } else {
                $uid = $cv["user_id"];
            }

            if (array_search($uid, $contact_id) !== false) {
                unset($connections[$ck]);
            } else if (array_search($uid, $arr3) !== false) {
                unset($connections[$ck]);
            } else {
        //      print_r($contact_id);
                $contact_id[] = $uid;
                $user = $this->get_user_by_id($uid);
                if ($user["status"] == 1) {
                    $connections[$ck]["user_profile"] = $user;
                    if (empty($user)) {
                        unset($connections[$ck]);
                    }

                    if (!empty($user)) {
                        $response22 = $this->check_connection_user($user_id, $uid);
                        $user = array_merge($user, $response22);
                        array_push($suggestions, $user);
                    }
                }
            }
        }
        return array_values($suggestions);
    }

    function check_connection_user($user_id, $person_id) {
        $profile = array();
        $where = "user_id = " . $user_id . " AND connect_user_id = " . $person_id . "";
        $where1 = "user_id = " . $person_id . " AND connect_user_id = " . $user_id . "";
        $send_connection_list = $this->read_db->select("user_connection_id, user_id, connect_user_id, action")->where($where)->get('user_connections')->row_array();
        $accepted_connection_list = $this->read_db->select("user_connection_id, user_id, connect_user_id, action")->where($where1)->get('user_connections')->row_array();

        if (empty($send_connection_list)) {
            $profile["connection_type"] = "0";
            $profile["connection_status"] = "Connect";
        } else if ($send_connection_list["action"] == 0) {
            $profile["connection_type"] = "1";
            $profile["connection_status"] = "Pending";
        } else if ($send_connection_list["action"] == 1) {
            $profile["connection_type"] = "2";
            $profile["connection_status"] = "Connected";
        } else if ($send_connection_list["action"] == 2) {
            $profile["connection_type"] = "3";
            $profile["connection_status"] = "Decline";
        } else if ($send_connection_list["action"] == 3) {
            $profile["connection_type"] = "0";
            $profile["connection_status"] = "Connect";
        }

        if (empty($accepted_connection_list)) {

        } else if ($accepted_connection_list["action"] == 0) {
            $profile["connection_type"] = "4";
            $profile["connection_status"] = "No Action";
        } else if ($accepted_connection_list["action"] == 1) {
            $profile["connection_type"] = "5";
            $profile["connection_status"] = "Accepted";
        } else if ($accepted_connection_list["action"] == 2) {
            $profile["connection_type"] = "6";
            $profile["connection_status"] = "Rejected";
        } else if ($accepted_connection_list["action"] == 3) {
            $profile["connection_type"] = "0";
            $profile["connection_status"] = "Connect";
        }

        return $profile;
    }

    function person_profile($post = []) {
        $profile = $this->user_details($post['person_id'], $post['user_id']);

        if (!empty($profile)) {
            $response = $this->check_connection_user($post['user_id'], $post['person_id']);
            $profile = array_merge($profile, $response);

            return $profile;
        } else {
            return false;
        }
    }

    function get_contact_list($post = []) {
        $contact_list = [];

        if ($post['contact']) {
            $contact = explode(',', $post['contact']);
            foreach ($contact as $conact_id) {
                $single_contact = $this->read_db->select("user_id")
                                    ->where('email', $conact_id)
                                    ->where('user_id != '.$post["user_id"])
                                    ->get('users')->row_array();                

                if (!$single_contact) {
                    $single_contact['user_id'] = '';
                    $single_contact['email'] = $conact_id;
                    $single_contact['profile_image'] = '';
                    $single_contact['name'] = '';
                    $single_contact['username'] = '';
                    $single_contact['description'] = '';
                    $single_contact['is_invite'] = '1';
                    $single_contact['is_online'] = '0';
                    $single_contact['is_admin_verified'] = '0';
                } else {

                    $user_data = $this->get_user_by_id($single_contact['user_id']);

                    $single_contact['email'] = $conact_id;
                    $single_contact['phone'] = $user_data['phone_code'] . $user_data['phone_number'];
                    $single_contact['profile_image'] = $user_data['image_profile'];
                    $single_contact['user_id'] = $user_data['user_id'];
                    $single_contact['name'] = $user_data['name'];
                    $single_contact['username'] = $user_data['username'];
                    $single_contact['is_admin_verified'] = $user_data['is_user_verified'];
                    $single_contact['description'] = $user_data['blurb'];
                    $single_contact['is_invite'] = '0';
                    $single_contact['is_online'] = $user_data['display_online_status'];
                }
                $contact_list[] = $single_contact;
            }
            $contact_list = array_unique($contact_list, SORT_REGULAR);

            return array_values($contact_list);
        } else {

        }
    }

    function send_an_invite($post = []) {
        $CI =& get_instance();
        $CI->load->model(basename(__DIR__)."/notification_model");

        $where = "action IN (0,1,2)";
        $exists = $this->read_db->select("user_connections.*, user_blocked.user_block_id")
                        ->join("user_blocked", "user_blocked.user_id = user_connections.user_id and user_blocked.blocked_user_id = " . $post["contact_id"] . "")
                        ->where('user_connections.user_id', $post["user_id"])
                        ->where('user_connections.connect_user_id', $post["contact_id"])
                        ->get('user_connections')->row_array();

        if (empty($exists)) {
            $check = $this->read_db->select("user_connections.user_connection_id, user_connections.user_id, user_connections.connect_user_id, user_connections.action")
                            ->where('user_connections.user_id', $post["user_id"])
                            ->where('user_connections.connect_user_id', $post["contact_id"])
                            ->get('user_connections')->row_array();

            if (empty($check)) {
                //insert in connection
                $insert_arr = array(
                    'user_id' => $post["user_id"],
                    'connect_user_id' => $post["contact_id"],
                );
                $this->write_db->insert('user_connections', $insert_arr);

                //user data
                $userdata = $this->get_user_by_id($post["user_id"]);

                //insert in notification
                $insert_notify = array(
                    'recipient_user_id' => $post["contact_id"],
                    'user_id' => $post["user_id"],
                    'notification_type_id' => "1",
                    'message_title' => 'Send An Invite',
                    'message_body' => '@' . $userdata["username"] . ' would like to connect with you.',
                    'sent_at' => date('Y-m-d H:i:s')
                );

                $CI->notification_model->create_notification($insert_notify);
                // print_r($insert_notify);
                return $insert_notify;
            } 
            else if (!empty($check) && ($check["action"] == 2 || $check["action"] == 3 )) {
                $insert_arr = array(
                    'user_id' => $post["user_id"],
                    'connect_user_id' => $post["contact_id"],
                );
                $this->write_db->set('action', 0)->where($insert_arr)->update('user_connections');

                //user data
                $userdata = $this->get_user_by_id($post["user_id"]);

                //insert in notification
                $insert_notify = array(
                    'recipient_user_id' => $post["contact_id"],
                    'user_id' => $post["user_id"],
                    'notification_type_id' => "1",
                    'message_title' => 'Send An Invite',
                    'message_body' => '@' . $userdata["username"] . ' would like to connect with you.',
                    'sent_at' => date('Y-m-d H:i:s')
                );
                $CI->notification_model->create_notification($insert_notify);

                return $insert_notify;
            } else {
                return 2;
            }
        } else {
            return 1;
        }
    }

    function accept_an_invite($post = []) {
        $CI =& get_instance();
        $CI->load->model(basename(__DIR__)."/notification_model");

        $where = "(user_id = " . $post["invited_user_id"] . " AND connect_user_id = " . $post["user_id"] . ") OR (user_id = " . $post["user_id"] . " AND connect_user_id = " . $post["invited_user_id"] . ")";
        $exists = $this->read_db->select("*")->where($where)->get('user_connections')->row_array();
        //print_r($exists); exit;
        if (!empty($exists)) {
            //user data
            $userdata = $this->get_user_by_id($post["user_id"]);

            if ($post["type"] == 1) {
                $this->write_db->set('action', 1)
                        ->set('updated_at', date('Y-m-d H:i:s'))
                        ->set('updated_by', $post["user_id"])
                        ->where('user_id', $post["invited_user_id"])
                        ->where('connect_user_id', $post["user_id"])
                        ->update('user_connections');

                //insert in notification
                $insert_notify = array(
                    'recipient_user_id' => $post["invited_user_id"],
                    'user_id' => $post["user_id"],
                    'notification_type_id' => "2",
                    'message_title' => 'Accepted An Invite',
                    'message_body' => '@' . $userdata["username"] . ' has accepted your connection request.',
                    'sent_at' => date('Y-m-d H:i:s')
                );
                $CI->notification_model->create_notification($insert_notify);
                $insert_notify["type"] = 1;
                return $insert_notify;
            }
            else if ($post["type"] == 2) {
                $this->write_db->set('action', 2)
                        ->set('updated_at', date('Y-m-d H:i:s'))
                        ->set('updated_by', $post["user_id"])
                        ->where('user_id', $post["invited_user_id"])
                        ->where('connect_user_id', $post["user_id"])
                        ->update('user_connections');

                //insert in notification
                $insert_notify = array(
                    'recipient_user_id' => $post["invited_user_id"],
                    'user_id' => $post["user_id"],
                    'notification_type_id' => "3",
                    'message_title' => 'Reject An Invite',
                    'message_body' => '@' . $userdata["username"] . ' has rejected your connection request.',
                    'sent_at' => date('Y-m-d H:i:s')
                );
                $CI->notification_model->create_notification($insert_notify);
                $insert_notify["type"] = 2;
                return $insert_notify;
            }
            else if ($post["type"] == 3) {

                $where = "(user_id = " . $post["invited_user_id"] . " AND connect_user_id = " . $post["user_id"] . ") OR (user_id = " . $post["user_id"] . " AND connect_user_id = " . $post["invited_user_id"] . ")";
                $this->write_db->where($where)->delete('user_connections');

                //insert in notification
                $insert_notify = array(
                    'recipient_user_id' => $post["invited_user_id"],
                    'user_id' => $post["user_id"],
                    'notification_type_id' => "4",
                    'message_title' => 'Remove From Connection',
                    'message_body' => '@' . $userdata["username"] . ' has removed your connection.',
                    'sent_at' => date('Y-m-d H:i:s')
                );
                //$this->create_notification($insert_notify);
                $insert_notify["type"] = 3;
                return $insert_notify;
            }
        } else {
            return false;
        }
    }

    function unsend_a_request($post = []) {
        $where = "(user_id = " . $post["user_id"] . " AND connect_user_id = " . $post["invited_user_id"] . ")";
        $exists = $this->read_db->select("user_connection_id, user_id, connect_user_id, action")->where($where)->get('user_connections')->row_array();
        // print_r($exists); exit;
        if (!empty($exists)) {
            $this->write_db->where($where)
                    ->where('action', 0)
                    ->delete('user_connections');
            return true;
        } else {
            return false;
        }
    }

    function connection_list($post = []) {
        $return_arr = array();
        $common = array();
        $resp = array();
        $connect_merge1 = array();

        if (isset($post["chr"]) && !empty($post["chr"])) {
            $connection_list2 = $this->read_db->select("*")
                            ->where("((connect_user_id = " . $post["person_id"] . " AND user_id != " . $post["user_id"] . ") OR (connect_user_id != " . $post["user_id"] . " AND user_id = " . $post["person_id"] . "))")
                            ->where('action', 1)
                            ->get('user_connections')->result_array();

            if (!empty($connection_list2)) {
                $connect_user_list1 = array_column($connection_list2, 'connect_user_id');
                $connect_user_list2 = array_column($connection_list2, 'user_id');
                $connect_merge = array_merge($connect_user_list1, $connect_user_list2);
                $connect_user_list = implode(',', array_unique($connect_merge));

                $where = "user_id IN (" . $connect_user_list . ") and username like '" . $post["chr"] . "%'";
                $user_list = $this->read_db->select("user_id", false)->where($where)->get('user')->result_array();

                if (!empty($user_list)) {
                    $arr_user = array_column($user_list, "user_id");

                    // Search
                    if (!in_array($post["person_id"], $arr_user)) {

                    } else {
                        $pos = array_search($post["person_id"], $arr_user);

                        if ($pos != "" || $pos >= 0) {
                            // Remove from array
                            unset($arr_user[$pos]);
                        }
                    }

                    $users = implode(",", $arr_user);

                    if (!empty($users)) {
                        $connection_list = $this->read_db->select("*")
                                        ->where("((connect_user_id = " . $post["person_id"] . " AND user_id != " . $post["user_id"] . ") OR (connect_user_id != " . $post["user_id"] . " AND user_id = " . $post["person_id"] . "))")
                                        ->where("(connect_user_id IN (" . $users . ") OR user_id IN (" . $users . "))")
                                        ->where('action', 1)
                                        ->get('user_connections')->result_array();

                        if (!empty($connection_list)) {
                            foreach ($connection_list as $key => $value) {
                                if ($value["user_id"] == $post["person_id"]) {
                                    $person = $value["connect_user_id"];
                                } else if ($value["connect_user_id"] == $post["person_id"]) {
                                    $person = $value["user_id"];
                                }

                                if (!in_array($person, $connect_merge1)) {
                                    array_push($connect_merge1, $person);
                                    $cuser = $this->get_user_by_id($person);

                                    if (!empty($cuser)) {
                                        $response22 = $this->check_connection_user($post["user_id"], $person);
                                        $connection_list[$key]["connected_person"] = array_merge($cuser, $response22);
                                    } else {
                                        unset($connection_list[$key]);
                                    }
                                } else {
                                    unset($connection_list[$key]);
                                }
                            }
                        } else {
                            $connection_list = array();
                        }
                    } else {
                        $connection_list = array();
                    }
                } else {
                    $connection_list = array();
                }
            }
        }
        else {
            $connection_list = $this->read_db->select("*")
                            ->where("((connect_user_id = " . $post["person_id"] . " AND user_id != " . $post["user_id"] . ") OR (connect_user_id != " . $post["user_id"] . " AND user_id = " . $post["person_id"] . "))")
                            ->where('action', 1)
                            ->get('user_connections')->result_array();

            if (!empty($connection_list)) {
                $connect_user_list1 = array_column($connection_list, 'connect_user_id');
                $connect_user_list2 = array_column($connection_list, 'user_id');
                $connect_merge = array_merge($connect_user_list1, $connect_user_list2);
                $connect_user_list = implode(',', array_unique($connect_merge));

                foreach ($connection_list as $key => $value) {
                    if (($value["connect_user_id"] == $post["person_id"]) && ($value["user_id"] == $post["person_id"])) {
                        unset($connection_list[$key]);
                    } else {
                        if ($value["user_id"] == $post["person_id"]) {
                            $person = $value["connect_user_id"];
                        } else if ($value["connect_user_id"] == $post["person_id"]) {
                            $person = $value["user_id"];
                        }

                        if (!in_array($person, $connect_merge1)) {
                            array_push($connect_merge1, $person);
                            $cuser = $this->get_user_by_id($person);

                            if (!empty($cuser)) {
                                $response22 = $this->check_connection_user($post["user_id"], $person);
                                $connection_list[$key]["connected_person"] = array_merge($cuser, $response22);
                            } else {
                                unset($connection_list[$key]);
                            }
                        } else {
                            unset($connection_list[$key]);
                        }
                    }
                }
            }
        }

        $return_arr["connection_list"] = array_values($connection_list);

        if (!empty($connection_list)) {
            //get common connection
            $logged_user_connection_list = $this->read_db->select("*")
                            ->where("(connect_user_id = " . $post["user_id"] . " OR user_id = " . $post["user_id"] . ")")
                            ->where('action', 1)
                            ->get('user_connections')->result_array();

            if (!empty($logged_user_connection_list)) {
                $connect_logged_user_list1 = array_column($logged_user_connection_list, 'connect_user_id');
                $connect_logged_user_list2 = array_column($logged_user_connection_list, 'user_id');
                $connect_logged_user_common = array_merge($connect_logged_user_list1, $connect_logged_user_list2);

                //print_r($connect_logged_user_common); exit;

                $result = array_intersect($connect_merge, $connect_logged_user_common);
                //print_r($result); exit;
                if (!empty($result)) {
                    $connect_logged_user_list = implode(',', array_unique($result));

                    $response = $this->read_db->select("user_id")
                                    ->where("user_id IN (" . $connect_logged_user_list . ") and user_id != " . $post["user_id"] . " and user_id != " . $post["person_id"] . "")
                                    ->where('status', 1)
                                    ->get('users')->result_array();

                    if (!empty($response)) {
                        foreach ($response as $k => $val) {
                            $user_data = $this->get_user_by_id($val["user_id"]);

                            $response22 = $this->check_connection_user($post["user_id"], $val["user_id"]);
                            $user_data = array_merge($user_data, $response22);
                            if(!empty($user_data)) {
                                array_push($resp, $user_data);
                            }

                            $common[$k]["connected_person"] = $resp;
                        }

                        $return_arr["common_connections"] = $common;
                    } else {
                        $return_arr["common_connections"] = array();
                    }
                } else {
                    $return_arr["common_connections"] = array();
                }
            } else {
                $return_arr["common_connections"] = array();
            }

            return $return_arr;
        }
    }

    function common_connection_list($post = []) {

        //person connections
        $connection_list = $this->read_db->select("*")
                        ->where("((connect_user_id = " . $post["person_id"] . " AND user_id != " . $post["user_id"] . ") OR (connect_user_id != " . $post["user_id"] . " AND user_id = " . $post["person_id"] . "))")
                        ->where('action', 1)
                        ->get('user_connections')->result_array();

        if (!empty($connection_list)) {
            $host_arr = array();
            $connect_user_list1 = array_column($connection_list, 'connect_user_id');
            $connect_user_list2 = array_column($connection_list, 'user_id');
            $connect_merge = array_merge($connect_user_list1, $connect_user_list2);

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
                    $where2 = "user_id IN (" . $connect_logged_user_list . ") and user_id != " . $post["user_id"] . " and user_id != " . $post["person_id"] . "";
                    $response = $this->read_db->select("user_id")
                                    ->where($where2)
                                    ->where('status', 1)
                                    ->get('users')->result_array();

                    if (!empty($response)) {
                        $resp = array();
                        foreach ($response as $k => $val) {
                            $user_data = $this->get_user_by_id($val["user_id"]);

                            $response22 = $this->check_connection_user($post["user_id"], $val["user_id"]);
                            $user_data = array_merge($user_data, $response22);
                            array_push($resp, $user_data);
                        }
                        return $resp;
                    }
                }
            }
        }
    }

    function update_current_location_details($post = []) {
        $arr = array(
            'last_ipaddress' => (!empty($post["ipaddress"])) ? $post["ipaddress"] : null,
            'last_latitude' => (!empty($post["latitude"])) ? $post["latitude"] : null,
            'last_longitude' => (!empty($post["longitude"])) ? $post["longitude"] : null
        );

        return $this->write_db->set($arr)
                        ->where('user_id', $post['user_id'])
                        ->update('user_connectivity');
    }

    function check_username_id($post = []) {        
        if (isset($post["username"]) && !empty($post["username"])) {  
            // print_r($post);          
            $userdata = $this->get_user_by_id($post["user_id"]);
            // print_r($userdata); exit;
            if (!empty($userdata)) {
                if ($userdata["username"] != $post["username"]) {
                    $userdata1 = $this->read_db
                            ->where('username', $post['username'])
                            ->get('users')
                            ->row_array();
                    if ($userdata1) {
                        return 4;
                    } else {
                        return 3;
                    }
                } else {
                    return 2;
                }
            } else {
                return 1;
            }
        }
    }

    function get_user_by_id($user_id) {
        $user_data = $this->read_db->select("u.user_id, u.user_type_id, u.username, u.email, u.token, u.phone_code, u.phone_number, u.created_at, f.fan_id, f.name, f.gender, f.birthdate, f.demisedate, f.country_id, f.country_iso2, f.hometown, f.tags, f.blurb, f.image_profile, f.image_profile_presigned, f.image_cover, f.image_cover_presigned, f.is_live, up.is_profile_public, up.display_online_status, up.display_hometown, up.allow_direct_messages, up.allow_mentions", false)
                ->join("fans f", "u.user_id = f.user_id", "left")
                ->join("user_privacy_settings up", "u.user_id = up.user_id", "left")
                ->where('u.user_id', $user_id)
                ->get('users u')
                ->row_array();

        // print_r($user_data); exit;

        if (!empty($user_data)) {  

            // $media = new Media();
            if (!empty($user_data["image_profile"])) {    
                if (!empty($user_data["image_profile_presigned"])) { 
                    $presigned1 = explode("&", $user_data["image_profile_presigned"]);
                    $presigned2 = explode("=", $presigned1[0]);

                    if($presigned2[1] >= strtotime(date('Y-m-d H:i:s'))) {
                        $pic_res = $_ENV['S3_PATH'] . $user_data['image_profile'] . $user_data["image_profile_presigned"];
                    }
                    else {
                        $pic_res = $this->media_model->index_get($user_data["image_profile"]);
                        $presigned1 = explode("?", $pic_res);
                        $this->db->set("image_profile_presigned", '?'.$presigned1[1])->where('user_id', $user_id)->update("fans");
                    }
                }
                else {
                    $pic_res = $this->media_model->index_get($user_data["image_profile"]);
                    $presigned1 = explode("?", $pic_res);
                    $this->db->set("image_profile_presigned", '?'.$presigned1[1])->where('user_id', $user_id)->update("fans");
                    // print_r($presigned1); exit;
                }

                $user_data["profile_image"] = $pic_res;
                $user_data["profile_image_thumb"] = $pic_res;
            } else {
                $user_data["profile_image"] = "";
                $user_data["profile_image_thumb"] = "";

            }

            if (!empty($user_data["image_cover"])) {
                if (!empty($user_data["image_cover_presigned"])) { 
                    $presigned1 = explode("&", $user_data["image_cover_presigned"]);
                    $presigned2 = explode("=", $presigned1[0]);

                    if($presigned2[1] >= strtotime(date('Y-m-d H:i:s'))) {
                        $pic_res1 = $_ENV['S3_PATH'] . $user_data['image_cover'] . $user_data["image_cover_presigned"];
                    }
                    else {
                        $pic_res1 = $this->media_model->index_get($user_data["image_cover"]);
                        $presigned1 = explode("?", $pic_res1);
                        $this->db->set("image_cover_presigned", '?'.$presigned1[1])->where('user_id', $user_id)->update("fans");
                    }
                }
                else {
                    $pic_res1 = $this->media_model->index_get($user_data["image_cover"]);
                    $presigned1 = explode("?", $pic_res1);
                    $this->db->set("image_cover_presigned", '?'.$presigned1[1])->where('user_id', $user_id)->update("fans");
                    // print_r($presigned1); exit;
                }

                
                $user_data["cover_image"] = $pic_res1;
                $user_data["cover_image_thumb"] = $pic_res1;
            } else {
                $user_data["cover_image"] = "";
                $user_data["cover_image_thumb"] = "";
            }

            // unset($media);

            if (!empty($user_data["country_id"])) {
                //get country name
                $country = $this->read_db->select('country_id, name, name_official, iso2, latitude, longitude, image_flag, image_flag_presigned')
                                ->where('country_id', $user_data["country_id"])
                                ->get('countries')->row_array();

                if (!empty($country)) {
                    $country["flag"] = $_ENV['S3_PATH'] . $country['image_flag'] . $country["image_flag_presigned"];
                }

                $user_data["nationality"] = $country;
            } else {
                $user_data["nationality"] = new stdClass();
            }

            $user_data2 = array_map(function($value) {
                return $value === NULL ? "" : $value;
            }, $user_data);

            return $user_data2;
        }
    }

    function temporarily_disable_account($post = []) {
        $check = $this->read_db->select("user_id")
                        ->where("user_id", $post["user_id"])
                        ->get("users")->row_array();

        if (!empty($check)) {
            //user
            $this->write_db->set("status", $post["account_flag"])
                    ->set("updated_by", $post["user_id"])
                    ->set("updated_at", date('Y-m-d H:i:s'))
                    ->where("user_id", $post["user_id"])
                    ->update("users");

            if ($post["account_flag"] == "0") {
                return 1;
            } else if ($post["account_flag"] == "1") {
                return 2;
            }
        } else {
            return 3;
        }
    }

    function closed_account($post = []) {
        $check = $this->read_db->select("user_id")
                        ->where("user_id", $post["user_id"])
                        ->get("users")->row_array();

        if (!empty($check)) {
            //user
            $this->write_db->set("status", 3)
                    ->set("updated_by", $post["user_id"])
                    ->set("updated_at", date('Y-m-d H:i:s'))
                    ->where("user_id", $post["user_id"])
                    ->update("users");

            $this->write_db->set("status", 0)
                    ->set("updated_by", $post["user_id"])
                    ->set("updated_at", date('Y-m-d H:i:s'))
                    ->where("user_id", $post["user_id"])
                    ->update("fans");

            $this->write_db->set("status", 0)
                    ->set("updated_by", $post["user_id"])
                    ->set("updated_at", date('Y-m-d H:i:s'))
                    ->where("user_id", $post["user_id"])
                    ->update("user_notification_settings");

            return true;
        } else {
            return false;
        }
    }

     public function report_user($post = []) {
        $check = $this->read_db->select("*")
                        ->where("request_user_id", $post["reported_user_id"])
                        ->where("user_id", $post["user_id"])
                        ->where("user_request_reason_id", $post['user_request_reason_id'])
                        ->get("user_requests")->row_array();

        if (empty($check)) {
            $insert = $this->write_db->insert("user_requests", array(
                "request_user_id" => $post["reported_user_id"],
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
                "request_user_id" => $post["reported_user_id"],
                "user_id" => $post["user_id"],
                "user_request_reason_id" => $post['user_request_reason_id']
            );

            $this->write_db->set($up_arr)->where("request_user_id", $post["reported_user_id"])->where("user_id", $post["user_id"])->where("user_request_reason_id", $post['user_request_reason_id'])->update("user_requests");

            return true;
        }
    }
}

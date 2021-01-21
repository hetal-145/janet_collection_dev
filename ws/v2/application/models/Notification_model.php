<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification_model extends CI_Model {

	function __construct() {
        parent::__construct();
    }

    function create_notification($post = []) {
        $this->db->insert('notifications', $post);
        $notification['notification_id'] = $this->db->insert_id();
        return $notification;
    }

    function get_notification_list($post = []) {
        $list_station = $this->db
                ->where('recipient_user_id', $post['user_id'])
                ->where('deleted_at is null')
                //->where('notification_type_id NOT IN (11,14,15,16,22,23,24,25,26)')
                ->limit(LIMIT)
                ->offset($post["offset"])
                ->order_by('created_at', 'desc')
                ->get('notifications')
                ->result_array();

        if (!empty($list_station)) {
            $ntp1 = array(1,2,4,6,7,8); //user
            $ntp2 = array('3,9,10,15'); //stations
            $ntp3 = array('17'); //tracks
            $ntp4 = array('5,11,12,13,14,16,20'); //shows
            $ntp5 = array('18,19'); //artists

            foreach ($list_station as $key => $value2) {
                $value = array_map(function($val) {
                    if (is_null($val)) {
                        $val = "";
                    }
                    return $val;
                }, $value2);
                $list_station[$key] = $value;
                
                if (in_array($value["notification_type_id"], $ntp1)) {
                    if (!is_null($value["user_id"])) {
                        
                        $user111 = $this->user_model->get_user_by_id($value["user_id"]);
                        
                        if (!empty($user111)) {
                            $response22 = $this->user_model->check_connection_user($post["user_id"], $value["user_id"]);
                            $list_station[$key]["user"] = array_merge($user111, $response22);
                        } else {
                            $list_station[$key]["user"] = new stdClass();
                        }
                    } else {
                        $list_station[$key]["user"] = new stdClass();
                    }
                    $list_station[$key]["ntp"] = "1";
                }
                else if (in_array($value["notification_type_id"], $ntp2)) {
                    $list_station[$key]["station"] = new stdClass();
                    $list_station[$key]["ntp"] = "2";
                }
                else if (in_array($value["notification_type_id"], $ntp3)) {
                    $list_station[$key]["track"] = new stdClass();
                    $list_station[$key]["ntp"] = "3";
                }
                else if (in_array($value["notification_type_id"], $ntp4)) {
                    $list_station[$key]["show"] = new stdClass();
                    $list_station[$key]["ntp"] = "4";
                }
                else if (in_array($value["notification_type_id"], $ntp5)) {
                    $list_station[$key]["artist"] = new stdClass();
                    $list_station[$key]["ntp"] = "5";
                }
            }
            return $list_station;
        }
    }

    function get_unread_notification_list($post = []) {
        $list = $this->db->select("count(notification_id) as unread")
                ->where('recipient_user_id', $post['user_id'])
                ->where('read_at is null')
                ->where('notification_type_id NOT IN (11,14,15,16,22,23,24,25,26)')
                ->group_by('recipient_user_id')
                ->get('notifications')
                ->result_array();

        if (!empty($list)) {
            return $list[0]["unread"];
        } else {
            return 0;
        }
    }    

    function read_notification($post = []) {
        $update = $this->db->set('read_at', date('Y-m-d H:i:s'))
                           ->set('status', 2)
                           ->where('read_at is null')
                           ->where('recipient_user_id', $post["user_id"])
                           ->update('notifications');
        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    function single_read_notification($post = []) {
        $update = $this->db->set('read_at', date('Y-m-d H:i:s'))
                           ->set('status', 2)
                           ->where('read_at is null')
                           ->where('notification_id', $post["notification_id"])
                           ->update('notifications');
        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    function delete_notification($post = []) {
        if (isset($post["notification_id"]) && !empty($post["notification_id"])) {
            $where = "recipient_user_id = " . $post["user_id"] . " and notification_id = " . $post["notification_id"];
        } else {
            $where = "recipient_user_id = " . $post["user_id"];
        }
        $update = $this->db->set('deleted_at', date('Y-m-d H:i:s'))
                           ->set('status', 3)
                           ->where($where)
                           ->update('notifications');
        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    function send($push) {
        $user_id = $push['recipient_user_id'];
        $status = $this->get_user_notificatio($user_id);
        
        if(!empty($status)) {
            if($status["is_push_notification"] == 1) {          
                if($status["is_connection_request_notification"] == 1 && ($push["notification_type_id"] == "1" || $push["notification_type_id"] == "2" || $push["notification_type_id"] == "3" || $push["notification_type_id"] == "4")) {
                        $this->send_msg($push, $user_id);
                }
                else if($status["is_station_notification"] == 1 && ($push["notification_type_id"] == "5" || $push["notification_type_id"] == "6" || $push["notification_type_id"] == "7" || $push["notification_type_id"] == "8" || $push["notification_type_id"] == "9" || $push["notification_type_id"] == "10" || $push["notification_type_id"] == "12" || $push["notification_type_id"] == "13" || $push["notification_type_id"] == "17" || $push["notification_type_id"] == "18" || $push["notification_type_id"] == "19" || $push["notification_type_id"] == "20" || $push["notification_type_id"] == "21")) {
                        $this->send_msg($push, $user_id);
                }
                else if($status["is_artist_notification"] == 1) {
                        $this->send_msg($push, $user_id);
                }
                else if($status["is_direct_message_notification"] == 1 && ($push["notification_type_id"] == "11" || $push["notification_type_id"] == "22" || $push["notification_type_id"] == "23" || $push["notification_type_id"] == "24" || $push["notification_type_id"] == "25" || $push["notification_type_id"] == "26" || $push["notification_type_id"] == "27" || $push["notification_type_id"] == "28")) {
                        $this->send_msg($push, $user_id);
                }
                else if($status["is_tune_request_notification"] == 1 && ($push["notification_type_id"] == "14" || $push["notification_type_id"] == "15" || $push["notification_type_id"] == "16")) {
                        $this->send_msg($push, $user_id);
                }
            }   
        }
    }
    
    function send_msg($push, $user_id){
        $device_token = $this->get_all_device_token($user_id);
            
        if(!empty($device_token['device_type'])) {          
            if ($device_token['device_type'] == 'android') {
                $android = 0;
                if ($android == '0') {
                    $this->load->library('gcm');
                    $this->gcm->setMessage(strip_tags($push['message_body']));
                    $this->gcm->setTitle(strip_tags($push['message_title']));
                    $this->gcm->setData($push);
                    $this->gcm->setTtl(false);
                    $this->gcm->setGroup(false);
                }
                $this->gcm->addRecepient($device_token['device_token']);

                $this->gcm->send(); 
                $sttaus = $this->gcm->status;               
                // print_r($sttaus);
                // print_r($this->gcm->messagesStatuses);

                if($sttaus["error"] == 0) {
                    $badge = $this->db->select('notification_id')
                            ->where('recipient_user_id', $user_id)
                            ->where('read_at is null')
                            ->where('notification_type_id NOT IN (11,14,15,16,22,23,24,25,26)')
                            ->order_by('created_at','desc')
                            ->get('notifications')
                            ->row_array();

                    $this->db->set('status', 1)->where("notification_id", $badge["notification_id"])->update("notifications");
                }
                $this->gcm->clearRecepients();
            } 
            else if ($device_token['device_type'] == 'ios') {

                //Get Badge count
                $badge = $this->db->select('count(notification_id) as badge, notification_id')
                    ->where('recipient_user_id', $user_id)
                    ->where('read_at is null')
                    ->where('notification_type_id NOT IN (11,14,15,16,22,23,24,25,26)')
                    ->order_by('created_at','desc')
                    ->get('notifications')
                    ->row_array();

                $bage = (int)$badge["badge"];

                $this->load->library('apn');
                $this->apn->payloadMethod = 'enhance';
                $this->apn->connectToPush();
                $this->apn->setData($push);
                $send_result = $this->apn->sendMessage($device_token['device_token'], strip_tags($push['message_title']), strip_tags($push['message_body']), /* badge */ $bage, /* sound */ 'default');

                if($send_result){
                    $this->db->set('status', 1)->where("notification_id", $badge["notification_id"])->update("notifications");
                }
               
               // if ($send_result)
               //     echo 'sent success.';
               // else
               //     print_r($this->apn->error); 
                $this->apn->disconnectPush();
            }
        }
    }

    function get_all_device_token($user_id) {
        return $this->db->where('user_id', $user_id)->get('user_connectivity')->row_array();
    }   
    
    function get_user_notificatio($user_id) {
        return $this->db->select("*")->where('user_id', $user_id)->get('user_notification_settings')->row_array();
    }
}
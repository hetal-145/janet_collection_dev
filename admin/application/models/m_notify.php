<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class M_notify extends CI_Model {
      
    public function send($push) {
        //return false;
        $user_id = $push['to_user_id'];
        $all_device_token = $this->get_all_device_token($user_id);
        $android = '0';
        foreach ($all_device_token as $key => $device_token) {
           // print_r($device_token);
            
            if ($device_token['device_type'] == 'android') {
                $config = [
                    'user_type' => 'user'
                ];
                if ($android == '0') {
                    $this->load->library('gcm', $config);
                    $this->gcm->setMessage($push['message']);
                    $this->gcm->setData($push);
                    $this->gcm->setTtl(false);
                    $this->gcm->setGroup(false);
                }
                $this->gcm->addRecepient($device_token['device_token']);
                $android = '1';
            } else if ($device_token['device_type'] == 'ios') {
                $config = [
                    'user_type' => 'user'
                ];
                //Get Badge count
                $badge = $this->db->select('count(*) as badge')
                        ->where('to_user_id', $user_id)
                        ->where('is_read', 0)
                        ->order_by('date','desc')
                        ->get('notification')
                        ->row_array();
                
                //print_r($badge); exit;                
                $bage = (int)$badge["badge"];
                
                $this->load->library('apn', $config);
                $this->apn->payloadMethod = 'enhance';
                $this->apn->connectToPush();
                $this->apn->setData($push);
                $send_result = $this->apn->sendMessage($device_token['device_token'], $push['message'], /* badge */ $bage, /* sound */ 'default');
//                echo '<pre>';
//                if ($send_result)
//                    echo 'sent success.';
//                else
//                    print_r($this->apn->error);
                $this->apn->disconnectPush();
            }
        }
        if ($android == '1') {
            $this->gcm->send();

//            echo '<pre>';
//            print_r($this->gcm->status);
//            print_r($this->gcm->messagesStatuses);
        }
    }

    public function get_all_device_token($user_id) {
        return $this->db->where('user_id', $user_id)
                        ->where('status', '1')
                        ->get('device_token')->result_array();
    }
    
//    public function get_all_app_user(){
//        $all_app_user = $this->db->select('user_id')->where('status', '1')
//                        ->get('user')->result_array();
//        return $all_app_user;
//    }

}

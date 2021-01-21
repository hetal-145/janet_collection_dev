<?php

class M_notification extends CI_Model {

    public function add_notification($post = []){   
        $this->db->insert('notification', $post);
        return 'success';
    }
    
    public function update_notification($post = []){        
        $this->db->set($post)
                ->where('faq_id', $post["faq_id"])
                ->update('faq_mst');
        
        return 'success';        
    }
    
    public function get_notification($notification_id){
        $faq = $this->db->select("*")
                    ->where('notification_id', $notification_id)
                    ->get('notification')->row_array();
        
        if(!empty($faq)){
            return $faq;
        }
    }
    
    function send_notification($post=[]){
        //get user
        $get_users = $this->db->select("user_id")->where('status', 1)->get('user')->result_array();
        
        //get notification count
        $get_count = $this->db->select("notification_count")
                ->where('notification_type', 2)
                ->order_by('date', 'desc')
                ->limit(1)
                ->get('notification')->row_array();
        
        if(!empty($get_count)) {
            $notification_count = $get_count["notification_count"] + 1;
        }
        else {
            $notification_count = 1;
        }
        
        //add notification
        foreach($get_users as $user) {
            $insert_array = array(
                'to_user_id' => $user["user_id"],
                'notification_type' => 2,
                'notification_count' => $notification_count,
                'message' => $post["message"]
            );
            $this->db->insert("notification", $insert_array);            
            $this->push_notify($user["user_id"], $post["message"]);
        }
        
        return 'success';
        
    }
    
    function push_notify($user_id, $message){  
        $CI =& get_instance();
        $CI->load->model('m_notify');
        //print_r($_POST); exit;
        $push = array(
           'to_user_id' => $user_id,
           'message' => $message, 
        );
        
        //print_r($push);
        $this->m_notify->send($push);
    }

}

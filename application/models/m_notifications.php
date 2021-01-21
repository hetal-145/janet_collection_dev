<?php

class M_notifications extends CI_Model {
    public function get_notification_list($offset) {
        //echo $offset;
        $response = array();
        $user_id = $this->session->userdata("user_id");        
        $list = $this->db->select("notification.*, notification_type_mst.notification")
                ->join("notification_type_mst", "notification_type_mst.notification_type_id = notification.notification_type")
                ->where('notification.to_user_id', $user_id)
                ->limit(LIMIT)
                ->offset($offset)
                ->order_by('notification.date', 'desc')
                ->get('notification')
                ->result_array();
        if ($list) {
            $response["list"] = $list;
            $response["offset"] = $offset + LIMIT;
            return $response;
        }
        else {
            return 'error';
        }
    }
    
    public function get_total_notifications() {
        $user_id = $this->session->userdata("user_id");
        $total = $this->db->select("*")
                ->where('notification.to_user_id', $user_id)
                ->get('notification')
                ->num_rows();
        return $total;
    }
    
    public function get_unread_notifications() {
        $user_id = $this->session->userdata("user_id");
        $total = $this->db->select("*")
                ->where('notification.to_user_id', $user_id)
                ->where("is_read", 1)
                ->get('notification')
                ->num_rows();
        return $total;
    }
}

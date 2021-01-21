<?php

class M_setting extends CI_Model {

    function change_password($post = []) {

        $post['password'] = sha1($post['password']);
        $post['new_password'] = sha1($post['new_password']);

        $check_password = $this->db->select('*')
                ->where('password', $post['password'])
                ->where('seller_id', $post['user_id'])
                ->get('seller')
                ->row_array();
        if ($check_password) {
            $change_pass = $this->db->set('password', $post['new_password'])
                    ->where('seller_id', $post['user_id'])
                    ->update('seller');
            $resp = 'update';
        } else {
            $resp = 'not_match';
        }
        return $resp;
    }

    function get_setting_data() {
        return $this->db->select('*')
                        ->get('setting')
                        ->result_array();
    }

    public function update_settings($post =[]) {
        
        //print_r($post); exit;
        foreach ($post as $key => $value) {
            
            $exists = $this->db->select('*', false)                    
                    ->where('key', $key)
                    ->get('setting')->row_array();
            
            if($exists) {
                $this->db->set('value', $value)
                        ->where('key', $key)
                        ->update('setting');
            } else {
                $insert_arr = array(
                    'key' => $key,
                    'value' => $value
                );
                $this->db->insert('setting', $insert_arr);
            }
        }
        return true;
    }

}

<?php

class M_cookies extends CI_Model {

    public function get_cookies_data() {

        $tc = $this->db->select('value')
                ->where('key', 'cookies')
                ->get('setting')
                ->row_array();
        return $tc;
    }

    public function update_cookies_content($post = '') {
        $tc = $this->db->select('*')
                ->where('key', 'cookies')
                ->get('setting')
                ->row_array();
        $update_ary = array();
        if (!empty($post['cookies'])) {
            if(!empty($tc)) {
                $update_ary = [
                    'value' => $post['cookies'],
                    'key'=> 'cookies'
                ];

                $this->db->set($update_ary)
                    ->where('key', 'cookies')
                    ->update('setting');
            }
            else {
                $update_ary = [
                    'value' => $post['cookies'],
                    'key'=> 'cookies'
                ];

                $this->db->insert('setting', $update_ary);
            }
        }    
        return 'success';
    }

}

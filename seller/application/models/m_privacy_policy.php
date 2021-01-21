<?php

class M_privacy_policy extends CI_Model {

    public function get_privacy_policy() {

        $tc = $this->db->select('value')
                ->where('key', 'privacy_policy')
                ->get('setting')
                ->row_array();
        return $tc;
    }

    public function update_privacy_policy($post = '') {

        $update_ary = array();
        if (!empty($post['privacy_policy'])) {
            $update_ary = [
                'value' => $post['privacy_policy'],
                'key'=> 'privacy_policy'
            ];
        }
        $this->db->set($update_ary)
                ->where('key', 'privacy_policy')
                ->update('setting');

        return 'success';
    }

}

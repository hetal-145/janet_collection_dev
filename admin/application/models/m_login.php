<?php

class M_login extends CI_Model {

    public function check_login($post = []) {
        $password = sha1($post['password']);
        $email = $post['email'];
        $userdata = $this->db->select('*')
                        ->where('email', $email)
                        ->where('password', $password)
                        ->where('status', 1)
                        ->limit(1)
                        ->get('admin')->row_array();
        if ($userdata) {
            return $userdata;
        }
    }

    function check_session($index = '') {
        if ($index == 1) {
            if ($this->session->userdata('loged_in')) {
                redirect(base_url('home'));
            }
        } else {
            if (!$this->session->userdata('loged_in')) {
                redirect(base_url('login'));
            }
        }
    }

    public function user_details() {
        $user_id = $this->session->userdata('user_id');
        $user_info = $this->db->where('user_id', $user_id)
                ->get('users')
                ->row_array();
        return $user_info;
    }

}

<?php

class M_login extends CI_Model {

    public function check_login($post = []) {
        $password = sha1($post['password']);
        $contact_no = $post['contact_no'];
        $userdata = $this->db->select('*')
                        ->where('contact_no', $contact_no)
                        ->where('password', $password)
                        ->where('status', 1)
                        ->where('is_admin_verified', 1)
                        ->limit(1)
                        ->get('seller')->row_array();
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
    
    public function get_profile_data(){
        $user_id = $this->session->userdata('user_id');
        $response = $this->db->select("*")->where("seller_id", $user_id)->get('seller')->row_array();
        unset($response["password"]);
        return $response;
    }

}

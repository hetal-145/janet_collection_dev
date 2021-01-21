<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('m_login');
    }

    public function index() {
        $this->load->view('login');
    }

    public function user_login() {
        $post = $_POST;
        $userdata = $this->m_login->check_login($post);
        if ($userdata) {
	    if(!empty(trim($userdata["timezone"]))) {
		$timezone = trim($userdata["timezone"]);
		date_default_timezone_set($timezone);
	    }
	    else {
		$timezone = 'UTC';
		date_default_timezone_set($timezone);
	    }
	    
            $session = [
                'loged_in' => true,
                'user_id' => $userdata['seller_id'],
		'timezone' => $timezone
            ];
            $this->session->set_userdata($session);
            echo 1;
        } else {
            echo 0;
        }
    }
    
    public function logout() {
        $this->session->sess_destroy();
        redirect('/');
    }
}

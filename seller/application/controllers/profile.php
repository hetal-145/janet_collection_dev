<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Profile extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_otp');
        $this->load->model('m_login');
	$this->load->model('m_tools');
        $this->m_login->check_session();
    }

    public function index() {
        $data['code'] = $this->m_otp->country_code();
        $data['profile_details'] = $this->m_login->get_profile_data();
	$data["delivery_zone"] = $this->m_tools->get_delivery_zone();
	$data["tz_list"] = $this->m_tools->tz_list();
        $this->load->view('header', $data);
        $this->load->view('profile', $data);
        $this->load->view('footer');
    }
    
    public function update_profile(){
        $user_id = $this->session->userdata('user_id');
        $post = $_POST;
        //print_r($post); exit;
	if(!empty($post["timezone"])) {
	    $time = explode('-', $post["timezone"]);
	    unset($post["timezone"]);
	    $post["timezone_utc"] = $time[0];
	    $post["timezone"] = $time[1];
	}
	date_default_timezone_set(trim($post["timezone"]));
        $this->db->set($post)->where("seller_id", $user_id)->update('seller');
        echo 'success';
    }
    
    

}

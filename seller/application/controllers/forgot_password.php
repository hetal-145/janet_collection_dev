<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Forgot_password extends CI_Controller {

    function __construct() {
        parent::__construct();        
        $this->load->model('m_otp');
    }

    public function index() {
        $data["code"] = $this->m_otp->country_code();
        $this->load->view('forgot_password', $data);
    }

    public function get_password() {
        $post = $_POST;        
        //print_r($post); exit;    
        $exists = $this->m_otp->get_seller_by_mobile($post["contact_no"]);
	
        if(!empty($exists)) {
            $response = $this->m_otp->send_otp($post);
	    //print_r($response); exit;
            echo json_encode($response);
        }
	else {
	    $response = array(
                'status' => 'false',
            );
	    
	    echo json_encode($response);
        }
    }
    
    public function verify_otp() {
        $post = $_POST;        
        //print_r($post); exit;        
        $response = $this->m_otp->verify_otp($post);
        echo $response;
    }
}

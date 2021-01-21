<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Register extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('m_register');
	$this->load->model('m_tools');
        $this->load->model('m_otp');
    }

    public function index() {
        $data["country_code"] = $this->m_register->get_country_code();
	$data["delivery_zone"] = $this->m_tools->get_delivery_zone();
	$data["weekday"] = $this->m_tools->get_weekdays();
	$data["tz_list"] = $this->m_tools->tz_list();
        $this->load->view('register', $data);
    }
    
    public function save_register(){
        $post = $_POST;  
        $files = $_FILES;
	
	if(empty($files["verify_doc"]["name"][0])) {
	    echo "error";
	}
	else {	
	    $response = $this->m_register->save_register($post, $files);
	    echo $response;
	}
    }
    
    public function send_otp($post=[]) {   
        $post = $_POST;        
        //print_r($post); exit; 
            
        //check exists
        $exists = $this->db->select("country_code, contact_no")
                ->where('contact_no', $post['contact_no'])
                ->where('status', 1)
                ->get('seller')->row_array();
        //print_r($exists); exit; 
        if(empty($exists)) {                   
            $response = $this->m_otp->send_otp($post);
            echo json_encode($response);
        } 
        else {
            echo 'exists'; exit;
        }
    }
    
    public function verify_otp() {
        $post = $_POST;        
        //print_r($post); exit;        
        $response = $this->m_otp->verify_otp2($post);
        echo $response;
    }

}

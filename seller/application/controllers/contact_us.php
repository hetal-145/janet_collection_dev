<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Contact_us extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');

        $this->load->model('m_login');
        $this->load->model('m_tools');
        $this->m_login->check_session();
    }

    public function index() {
        $this->load->view('header');
        $this->load->view('contact_us');
        $this->load->view('footer');
    }
    
    public function save(){
        $user_id = $this->session->userdata('user_id');
        $post = $_POST;  
        $subject = "Inquiry By Seller";
        //print_r($post); exit;
        
        $insert_array = array(
            'user_id' => $user_id,
            'name' => $post["name"],
            'email' => $post["email"],
            'message' => $post["message"],
            'subject' => $subject,
            'type' => 2,
        );
        $insert = $this->db->insert('help_support', $insert_array);
        
        $config_data = $this->db->where('key', 'seller_email_address')->get('setting')->row_array();   
        //print_r($config_data["value"]); exit;
	$msg = $this->load->view('mail_tmp/header', $insert_array, true);
	$msg .= $this->load->view('mail_tmp/contact_us', $insert_array, true);
	$msg .= $this->load->view('mail_tmp/footer', $insert_array, true);
        $res = $this->m_tools->send_mail($config_data["value"], $subject, $msg);
//print_r($res); exit;
        if($insert) {
            echo 'success';
        }
        else {
            echo 'error';
        }
    }
}

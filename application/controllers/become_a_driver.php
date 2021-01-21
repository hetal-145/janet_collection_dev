<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Become_a_driver extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('m_tools');
        $this->load->model('m_driver');
    }

    public function index()
    {
        $this->load->view('header');
        $this->load->view('become_a_driver');
        $this->load->view('footer');
    }
    
    public function driver_signup()
    {
	$data["country_list"] = $this->m_tools->country_list();        
        $this->load->view('header');
        $this->load->view('driver_signup',$data);
        $this->load->view('footer');
    }
    
    public function add_driver()
    { 
        //print_r($_FILES); print_r($_POST); exit;        
        $response = $this->m_driver->add_driver($_POST, $_FILES);
        //print_r($response); exit;
        if(!empty($response)) {
            echo $response; exit;
        }
    }
    
    public function verify($md5_user_id) {
        $status = $this->m_driver->verify($md5_user_id);
        if ($status) {
            if ($status == '1') {
                echo '<h1><font color="green">Your Janet-Collection account verified.</h1>';
            } else if ($status == '2') {
                echo '<h1><font color="green">User already verified.</h1>';
            }
        } else {
            echo '<h1><font color="red">User not registered.</h1>';
        }
    }
}
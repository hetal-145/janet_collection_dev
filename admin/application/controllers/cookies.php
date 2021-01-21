<?php
if(!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cookies extends CI_Controller{
    
    function __construct(){
        parent::__construct();
        $this->load->model('m_cookies');
        $this->load->model('m_login');
        $this->m_login->check_session();
    }
    
    public function index(){
        $data['res'] =  $this->m_cookies->get_cookies_data();
        $this->load->view('header', $data);
        $this->load->view('cookies', $data);
        $this->load->view('footer');
    }
    
    public function add_cookies(){
        $post = $_POST;
        $res = $this->m_cookies->update_cookies_content($post);
        echo $res;
    }
}


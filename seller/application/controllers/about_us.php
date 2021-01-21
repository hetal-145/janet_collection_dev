<?php
if(!defined('BASEPATH'))
    exit('No direct script access allowed');

class About_us extends CI_Controller{
    
    function __construct(){
        parent::__construct();
        $this->load->model('m_about_us');
        $this->load->model('m_login');
        $this->m_login->check_session();
    }
    
    public function index(){
        $data['res'] =  $this->m_about_us->get_about_us();
        $this->load->view('header', $data);
        $this->load->view('about_us', $data);
        $this->load->view('footer');
    }
    
    public function add_about_us(){
        $post = $_POST;
        $res = $this->m_about_us->update_about_us($post);
        echo $res;
    }
    
    public function view_about_us() {
        $data['about_us_data'] = $this->m_about_us->get_about_us();

        $this->load->view('header', $data);
        $this->load->view('view_about_us', $data);
        $this->load->view('footer');
    }
}


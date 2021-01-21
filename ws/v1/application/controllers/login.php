<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login extends CI_Controller {

    function __construct() {
        parent::__construct();
//        $this->load->model('m_user');
//        $this->load->model('m_api');
        

        //$this->output->enable_profiler(TRUE);
    }

    public function index() {
//        $this->m_user->check_session(1);
//        $data = [];
//        $data['country'] = $this->get_country();
//        $this->load->view('header');
        $this->load->view('login');
//        $this->load->view('footer');
    }

   
}

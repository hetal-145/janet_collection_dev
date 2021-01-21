<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Privacy_policy extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('m_privacy_policy');
        $this->load->model('m_login');
        $this->m_login->check_session();
    }

    public function index() {
        $data['res'] = $this->m_privacy_policy->get_privacy_policy();
     
        $this->load->view('header', $data);
        $this->load->view('privacy_policy',$data);
        $this->load->view('footer');
    }

   public function add_privacy_policy() {

       $post= $_POST;
        $res = $this->m_privacy_policy->update_privacy_policy($post);
        echo $res;
    }
    
    public function view_privacy_policy() {
        $data['privacy_policy_data'] = $this->m_privacy_policy->get_privacy_policy();

        $this->load->view('header', $data);
        $this->load->view('view_privacy_policy', $data);
        $this->load->view('footer');
    }
}

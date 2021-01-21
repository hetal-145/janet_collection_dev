<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Setting extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_setting');
        $this->load->model('m_login');
        $this->m_login->check_session();
    }

    public function index() {
        $user_id = $this->session->userdata('user_id');
        $data['setting_data'] = $this->m_setting->get_setting_data();
        $this->load->view('header', $data);
        $this->load->view('setting', $data);
        $this->load->view('footer');
    }

    public function change_password() {
        $post = $_POST;
        $post['user_id'] = $this->session->userdata('user_id');
        $resp = $this->m_setting->change_password($post);
        if ($resp) {
            echo $resp;
        }
    }
    
    public function view_change_password() {
        $user_id = $this->session->userdata('user_id');
        $this->load->view('header');
        $this->load->view('change_password');
        $this->load->view('footer');
    }

    public function email_setting() {
        $data['setting_data'] = $this->m_setting->get_setting_data();
    
        $this->load->view('header', $data);
        $this->load->view('email_setting', $data);
        $this->load->view('footer');
    }    
   
    public function update_email_setting() {
        $post = $_POST;
        $update_email = $this->m_setting->update_email_setting($post);
        if ($update_email) {
            echo 'success';
        }
    }
    
    public function payment_setting() {
        $data['setting_data'] = $this->m_setting->get_setting_data();
    
        $this->load->view('header', $data);
        $this->load->view('payment_setting', $data);
        $this->load->view('footer');
    }
    
    public function stuart_setting() {
        $data['setting_data'] = $this->m_setting->get_setting_data();    
        $this->load->view('header', $data);
        $this->load->view('stuart_setting', $data);
        $this->load->view('footer');
    }
        
        
    public function update_settings() {
        $post = $_POST;
        $update_settings = $this->m_setting->update_settings($post);
        if ($update_settings) {
            echo 'success';
        }
    }
}

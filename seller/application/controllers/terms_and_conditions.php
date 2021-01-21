<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Terms_and_conditions extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('m_terms_and_conditions');
        $this->load->model('m_login');
        $this->m_login->check_session();
    }

    public function index() {
        $data['res'] = $this->m_terms_and_conditions->get_term_condition_data();
        $this->load->view('header', $data);
        $this->load->view('terms_and_conditions', $data);
        $this->load->view('footer');
    }

    public function add_term_condition_content() {
        $post = $_POST;
        $res = $this->m_terms_and_conditions->update_term_condition_content($post);
        echo $res;
    }
    
    public function view_term_condition() {
        $data['term_condition_data'] = $this->m_terms_and_conditions->get_term_condition_data();
        $this->load->view('header', $data);
        $this->load->view('view_terms_and_conditions', $data);
        $this->load->view('footer');
    }

}

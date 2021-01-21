<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Faq extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('m_faq');
        $this->load->model('m_login');
        $this->m_login->check_session();
    }

    public function index() {
        $data['res'] = $this->m_faq->get_faq_data();

        $this->load->view('header', $data);
        $this->load->view('faq', $data);
        $this->load->view('footer');
    }

    public function add_faq_content() {

        $post = $_POST;
        $res = $this->m_faq->update_faq_content($post);
        echo $res;
    }

}

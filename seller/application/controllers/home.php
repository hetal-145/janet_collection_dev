<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('m_home');
        $this->load->model('m_login');
        $this->m_login->check_session();
    }

    public function index() {
        $seller_id = $this->session->userdata("user_id");
        $data['get_total_products'] = $this->m_home->get_total_products($seller_id);
        $data['get_total_categories'] = $this->m_home->get_total_categories();
        $data['get_total_brands'] = $this->m_home->get_total_brands();
	$data['get_total_drivers'] = $this->m_home->get_total_drivers();
        $data['get_total_orders'] = $this->m_home->get_total_orders($seller_id);
        $data['get_total_new_orders'] = $this->m_home->get_total_new_orders($seller_id);
	$data['get_total_delivered_orders'] = $this->m_home->get_total_delivered_orders($seller_id);
	$data['get_total_cancelled_orders'] = $this->m_home->get_total_cancelled_orders($seller_id);
	$data['get_total_income'] = $this->m_home->get_total_income($seller_id);
	$data['get_total_amount_to_receive'] = $this->m_home->get_total_amount_to_receive($seller_id);
	$data['orders_not_completed'] = $this->m_home->orders_not_completed($seller_id);
	$data['orders_in_process'] = $this->m_home->orders_in_process($seller_id);
	$data['schedule_orders'] = $this->m_home->schedule_orders($seller_id);
	

        $this->load->view('header');
        $this->load->view('home',$data);
        $this->load->view('footer');
    }

   
    
}

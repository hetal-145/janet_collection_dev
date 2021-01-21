<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class My_orders extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('m_tools');
        $this->load->model('m_login');
        $this->load->model('m_home');
	$this->load->model('m_shipping');
	$this->load->model('m_gift_card');
        $this->load->model('m_orders');
        $this->m_login->check_session();
    }

    public function index()
    {
	$user_id = $this->session->userdata('user_id');  
        $data["delivered_orders"] = $this->m_orders->my_past_order($user_id, 0);
	$data["pending_orders"] = $this->m_orders->my_upcoming_order($user_id, 0);
        $data["top_picks"] = $this->m_home->get_top_pick_product_list();  
	$data["currency"] = $this->m_tools->get_currency();  
        $this->load->view('header');
        $this->load->view('my_orders', $data);
        $this->load->view('footer');
    }
    
    public function get_upcoming_order() {
	$post = $_POST;
	$user_id = $this->session->userdata('user_id');        
        $orders = $this->m_orders->my_upcoming_order($user_id, $post["offset"]);
        if($orders == 'error') {
            echo 'error'; exit;
        }
        else {
	    echo json_encode($orders); 
        }
    }
    
    public function get_past_order() {
	$post = $_POST;
	$user_id = $this->session->userdata('user_id');        
        $orders = $this->m_orders->my_past_order($user_id, $post["offset"]);
        if($orders == 'error') {
            echo 'error'; exit;
        }
        else {
            echo json_encode($orders); 
        }
    }
    
    public function get_order_details() {
	$post = $_POST;
	$user_id = $this->session->userdata('user_id');  
	$post["user_id"] = $user_id;
        $orders = $this->m_orders->order_details($post);
        if($orders == 'error') {
            echo 'error'; exit;
        }
        else {
            echo json_encode($orders); 
        }
    }
}
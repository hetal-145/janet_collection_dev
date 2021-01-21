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
        
        $data['total_users'] = $this->m_home->total_users();
	$data['total_products'] = $this->m_home->total_products();
        $data['total_sellers'] = $this->m_home->total_sellers();
        $data['total_drivers'] = $this->m_home->total_drivers();         
        $data['total_categories'] = $this->m_home->get_active_categories();
	$data['total_testimonials'] = $this->m_home->total_testimonials();
	$data['total_subcategories'] = $this->m_home->get_active_subcategories();
        $data['total_brands'] = $this->m_home->get_active_brands();
        $data['total_gift_cards_sent'] = $this->m_home->get_total_gift_cards_sent();
	$data['total_dz'] = $this->m_home->total_dz();
	$data['total_loyalty_products'] = $this->m_home->total_loyalty_products();
	$data['total_vip_products'] = $this->m_home->total_vip_products();
	$data['total_suppliers'] = $this->m_home->total_suppliers();
	$data['total_promocodes'] = $this->m_home->total_promocodes();
	
	$data['new_driver_request'] = $this->m_home->new_driver_request();
	$data['new_driver_request_vehicle'] = $this->m_home->new_driver_request_vehicle();
	$data['new_sellers'] = $this->m_home->new_sellers();
	$data['new_drivers'] = $this->m_home->new_drivers();
	
	$data['get_total_amount_to_receive'] = $this->m_home->get_total_amount_to_receive();
	$data['get_total_income'] = $this->m_home->get_total_income();
	$data['get_total_cancelled_orders'] = $this->m_home->get_total_cancelled_orders();
	$data['get_total_delivered_orders'] = $this->m_home->get_total_delivered_orders();
	$data['get_total_new_orders'] = $this->m_home->get_total_new_orders();
	$data['orders_not_completed'] = $this->m_home->orders_not_completed();
	$data['orders_in_process'] = $this->m_home->orders_in_process();
	$data['total_orders'] = $this->m_home->get_total_orders();
	
        
	$data['total_non_alcohol_products'] = $this->m_home->get_non_alcohol_products();        
	$data['total_alcohol_products'] = $this->m_home->get_alcohol_products();
        $data['total_alcohol_products_sold'] = $this->m_home->total_alcohol_products_sold();
	$data['total_non_alcohol_products_sold'] = $this->m_home->total_non_alcohol_products_sold();
        $data['total_alcohol_products_orders'] = $this->m_home->total_alcohol_products_orders();
        $data['user_signedup_this_week'] = $this->m_home->get_no_of_user_signedup_this_week();
        $data['users_online_day'] = $this->m_home->get_users_online_day();
	$data['drivers_online_day'] = $this->m_home->drivers_online_day();
        $data['users_online_week'] = $this->m_home->get_users_online_week();
        $data['users_online_month'] = $this->m_home->get_users_online_month();
        
        $this->load->view('header');
        $this->load->view('home',$data);
        $this->load->view('footer');
    }

    public function show_notifications(){
	$response = $this->m_home->show_notifications();
	//$this->db->set('is_notified', 1)->set('is_read', 1)->where('is_notified', 0)->update("website_notification");
	if(!empty($response)) {
	    echo json_encode($response);
	}
	else {
	    echo '';
	}
    }
    
}

/*<div class="alert alert-success alert-dismissable">
		    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
		    <?php echo $notify["message"]; ?>
		</div>*/

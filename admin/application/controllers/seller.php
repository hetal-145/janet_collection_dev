<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
        
class Seller extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');

        $this->load->model('m_login');
	$this->load->model('m_tools');
	$this->load->model('m_seller');
        $this->load->model('m_notify');
        $this->m_login->check_session();
    }

    public function index() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('seller');    
	$xcrud->change_type('status', 'select', '1', array('1' => 'Active', '0' => 'Inactive'));    
        $xcrud->change_type('gender', 'select', '1', array('1' => 'Male', '2' => 'Female'));
        $xcrud->subselect('verification_doc','{seller_id}');
        $xcrud->column_callback('verification_doc', 'verifiy_seller_document');
        $xcrud->columns('code, seller_name, company_name, email, contact_no, verification_doc, is_online, dzone_id, status', false);
        $xcrud->fields('code, seller_name, company_name, email, country_code, contact_no, address, gender, latitude, longitude, notification_status, is_online, dzone_id, account_number, routing_no, bank_name, has_connect_ac');
        $xcrud->search_columns('seller_name,company_name,email, contact_no', 'seller_name');     
        $xcrud->relation("dzone_id", "delivery_zone", "dzone_id", "city");
	$xcrud->change_type("is_online", "select", "", array("0" => "OFFLINE", "1" => "ONLINE"));
        $xcrud->change_type('notification_status', 'bool');
	$xcrud->change_type('has_connect_ac', 'bool');
        $xcrud->label(array(
            'seller_name' => 'Name',
            'company_name' => 'Company',
            'verification_doc' => 'Verified?',
	    'dzone_id' => 'Delivery Zone',
        ));	
        
        //Inactive Users
        $xcrud->create_action('inactive', 'inactive_seller');
        $xcrud->button('#', 'Inactive', 'glyphicon glyphicon-ban-circle', 'xcrud-action btn-danger', array(
            'data-task' => 'action',
            'data-action' => 'inactive',
            'data-primary' => '{seller_id}'), array(
            'status',
            '=',
            '1')
        );
        
        //Active Users
        $xcrud->create_action('active', 'active_seller');
        $xcrud->button('#', 'Active', 'glyphicon glyphicon-ok', 'xcrud-action btn-success', array(
            'data-task' => 'action',
            'data-action' => 'active',
            'data-primary' => '{seller_id}'), array(
            'status',
            '!=',
            '1'));
	
	//View Seller Documents
	$xcrud->button('seller/seller_doc?sid={seller_id}', 'View Seller Documents', 'glyphicon glyphicon-list-alt', 'btn-warning');   
	
	//View Seller Stripe details
//	$xcrud->button('seller/seller_details?sid={seller_id}', 'Seller Stripe Account', 'glyphicon glyphicon-home', 'btn-primary');   
//	
//	//Create Seller Stripe Account
//	$xcrud->button('#', 'Create Seller Stripe Account', 'glyphicon glyphicon-plus', 'btn-default create_stripe', 
//	array(
//	    'data-primary' => '{seller_id}'
//	), 
////	array(
////	    'routing_no',
////	    '!=',
////	    '',
////	),
//	array(
//	    'has_connect_ac',
//	    '=',
//	    '0',
//	)
//	);
        
        $xcrud->unset_edit();
        $xcrud->unset_remove();
        $xcrud->unset_add();
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('seller', $data);
        $this->load->view('footer');
    }
    
    public function view_history() {

        $xcrud = Xcrud::get_instance();
        $xcrud->table('seller');      
        $xcrud->change_type('gender', 'select', '1', array('1' => 'Male', '2' => 'Female'));
	$xcrud->relation("dzone_id", "delivery_zone", "dzone_id", "city");
	$xcrud->change_type("is_online", "select", "", array("0" => "OFFLINE", "1" => "ONLINE"));
        $xcrud->columns('code, seller_name, company_name, email, contact_no, is_online, dzone_id', false);
        $xcrud->search_columns('seller_name,company_name,email, contact_no', 'seller_name');   
        $xcrud->label(array(
            'seller_name' => 'Name',
            'company_name' => 'Company',
	    'dzone_id' => 'Delivery Zone',
        ));
		
	//View Dashboard
	$xcrud->button('seller/view_seller_details?sid={seller_id}', 'Dashboard', 'glyphicon glyphicon-list-alt', 'btn-warning');   
		        
        $xcrud->unset_edit();
        $xcrud->unset_remove();
        $xcrud->unset_add();
	$xcrud->unset_view();
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('seller', $data);
        $this->load->view('footer');
    }
    
    public function view_seller_details(){
	$seller_id = $_GET["sid"];
	$data["seller"] = $this->m_tools->get_seller_by_id($seller_id);
        $data['get_total_products'] = $this->m_seller->get_total_products($seller_id);
        $data['get_total_categories'] = $this->m_seller->get_total_categories();
        $data['get_total_brands'] = $this->m_seller->get_total_brands();
	$data['get_total_drivers'] = $this->m_seller->get_total_drivers();
        $data['get_total_orders'] = $this->m_seller->get_total_orders($seller_id);
        $data['get_total_new_orders'] = $this->m_seller->get_total_new_orders($seller_id);
	$data['get_total_delivered_orders'] = $this->m_seller->get_total_delivered_orders($seller_id);
	$data['get_total_cancelled_orders'] = $this->m_seller->get_total_cancelled_orders($seller_id);
	$data['get_total_income'] = $this->m_seller->get_total_income($seller_id);
	$data['get_total_amount_to_receive'] = $this->m_seller->get_total_amount_to_receive($seller_id);
	$data['orders_not_completed'] = $this->m_seller->orders_not_completed($seller_id);
	$data['orders_in_process'] = $this->m_seller->orders_in_process($seller_id);
        $data['schedule_orders'] = $this->m_seller->schedule_orders($seller_id);
	
        $this->load->view('header');
        $this->load->view('seller_dashboard', $data);
        $this->load->view('footer');
    }
    
    public function seller_stripe_details() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('seller');    
	$xcrud->change_type('gender', 'select', '1', array('1' => 'Male', '2' => 'Female'));
	$xcrud->relation("dzone_id", "delivery_zone", "dzone_id", "city");
	$xcrud->change_type("is_online", "select", "", array("0" => "OFFLINE", "1" => "ONLINE"));
        $xcrud->columns('code, seller_name, company_name, email, contact_no, is_online, dzone_id', false);
        $xcrud->search_columns('seller_name,company_name,email, contact_no', 'seller_name');   
        $xcrud->label(array(
            'seller_name' => 'Name',
            'company_name' => 'Company',
	    'dzone_id' => 'Delivery Zone',
        ));
	
	//View Seller Stripe details
	$xcrud->button('seller/seller_details?sid={seller_id}', 'Seller Stripe Account', 'glyphicon glyphicon-eye-open', 'btn-primary');   
	
	//Create Seller Stripe Account
	$xcrud->button('#', 'Create Seller Stripe Account', 'glyphicon glyphicon-plus', 'btn-success create_stripe', 
	array(
	    'data-primary' => '{seller_id}'
	), 
//	array(
//	    'routing_no',
//	    '!=',
//	    '',
//	),
	array(
	    'has_connect_ac',
	    '=',
	    '0',
	)
	);
        
        $xcrud->unset_edit();
        $xcrud->unset_remove();
        $xcrud->unset_add();
	$xcrud->unset_view();
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('seller', $data);
        $this->load->view('footer');
    }
    
    public function seller_trading_hours() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('seller');    
	$xcrud->relation("dzone_id", "delivery_zone", "dzone_id", "city");
	$xcrud->change_type('status', 'select', '1', array('1' => 'Active', '0' => 'Inactive'));  
        $xcrud->columns('code, seller_name, company_name, email, contact_no, dzone_id, status', false);
        $xcrud->search_columns('seller_name,company_name,email, contact_no', 'seller_name');   
        $xcrud->label(array(
            'seller_name' => 'Name',
            'company_name' => 'Company',
	    'dzone_id' => 'Delivery Zone',
        ));
	
	//View Seller trading hours
	$xcrud->button('seller/trading_hours?sid={seller_id}', 'Seller Trading Hours', 'glyphicon glyphicon-eye-open', 'btn-primary');   
        
        $xcrud->unset_edit();
        $xcrud->unset_remove();
        $xcrud->unset_add();
	$xcrud->unset_view();
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('seller', $data);
        $this->load->view('footer');
    }
    
    public function  trading_hours() {
	$xcrud = Xcrud::get_instance();
        $user_id = $_GET["sid"];
        $xcrud->table('trading_hours'); 
        $xcrud->where("seller_id", $user_id);
        $xcrud->pass_var("seller_id", $user_id);
	$xcrud->columns("start_time, end_time, weekday");
        $xcrud->fields("start_time, end_time, weekday");
	
        //$xcrud->search_columns("start_time, end_time, weekday", "weekday");  
        $xcrud->change_type("weekday", "select", "", array('1' => 'Sunday', '2' => 'Monday', '3' => 'Tuesday', '4' => 'Wednesday', '5' => 'Thursday', '6' => 'Friday', '7' => 'Saturday'));
        $xcrud->label(array(
            'start_time' => 'Store Start Time',
            'end_time' => 'Store End Time',
            'weekday' => 'Weekday',
        ));
	
	$xcrud->unset_edit();
	$xcrud->unset_search();
        $xcrud->unset_remove();
        $xcrud->unset_add();
	$xcrud->unset_view();
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('seller', $data);
        $this->load->view('footer');
    }


    public function seller_account(){
	//print_r($_POST); exit;
	$response = $this->m_seller->create_stripe_account($_POST["seller_id"]);
	//print_r($response); exit;
	echo json_encode($response); exit;
    } 
    
    public function seller_details(){
	$xcrud = Xcrud::get_instance();
        $xcrud->table('stripe_connect_accounts');  
	$xcrud->join("user_id", "seller", "seller_id");
        $xcrud->where('stripe_connect_accounts.user_id', $_GET["sid"]);
	$xcrud->where('stripe_connect_accounts.type', 1);
	$xcrud->change_type("stripe_connect_accounts.is_primary", "bool");	
        $xcrud->columns('seller.seller_name, seller.code, seller.account_number, seller.routing_no, stripe_connect_accounts.bank_name, stripe_connect_accounts.account_id, stripe_connect_accounts.is_primary', false);
        $xcrud->label(array(
            'doc_name' => 'Verification Document',
	    'seller_id' => 'Seller Name',
	    'is_primary' => 'Is Primary Account?',
        ));
        
        $xcrud->unset_add();
        $xcrud->unset_edit();       
        $xcrud->unset_remove();
        $xcrud->unset_view();
	$data['content'] = $xcrud->render();       
       
        $this->load->view('header', $data);
        $this->load->view('stripe_account', $data);
        $this->load->view('footer');
    }
    
    public function seller_doc() {
	$xcrud = Xcrud::get_instance();
        $xcrud->table('seller_verifications');
        $xcrud->where('seller_id', $_GET["sid"]);
	$xcrud->where('status', 1);
	$xcrud->relation("seller_id", "seller", "seller_id", "seller_name");
        $xcrud->columns('seller_id, doc_name', false);
	$xcrud->fields('seller_id, doc_name_view', false);
	$xcrud->set_var('s3_path', S3_PATH.'seller/');
        $xcrud->subselect('doc_name_view', '{doc_name}');
	$xcrud->column_callback('doc_name', 'list_img_function');
	$xcrud->column_callback('doc_name_view', 'view_img_function');
	
	$xcrud->label(array(
            'seller_id' => 'Name',
            'doc_name' => 'Document',
	    'doc_name_view' => 'Document',
        ));
	
	$xcrud->unset_edit();
        $xcrud->unset_remove();
        $xcrud->unset_add();
	//$xcrud->unset_view();
	
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('seller', $data);
        $this->load->view('footer');
    }
    
    public function admin_verified(){
        if( $_POST["tp_status"] == 1) {
            $push = array(
                'to_user_id' => $_POST["seller_id"],
                'notification_type' => 6,
                'message' => 'You have been verified by admin.', 
             );                
        }
        else if( $_POST["tp_status"] == 0) {
             $push = array(
                'to_user_id' => $_POST["seller_id"],
                'notification_type' => 6,
                'message' => 'You have been unverified by admin.', 
             );               
        }
        $update = $this->add_admin_verified($_POST, $push);
        if($update) {
            $this->m_notify->send($push);
        }
    }
    
    public function add_admin_verified($post = [], $push = []){
        $this->db->where(array(
            'seller_id' => $post["seller_id"],
            'status' => 1
        ));
        $this->db->set(array(
            'is_admin_verified' => $post["tp_status"],
            'date' => date('Y-m-d h:i:s'),
        ));
        $this->db->update('seller');  
        
        $push["is_seller"] = 1;
        
        $this->db->insert('notification', $push);
        return true;
    }
    
    public function push_notify(){   
        $push = array(
           'to_user_id' => $_POST["to_user_id"],
           'message' => 'You have been verified by admin.', 
        );
        $this->m_notify->send($push);
    }
}

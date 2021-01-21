<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//include('../vendor/worldpay/worldpay-lib-php/init.php');
//use Worldpay\Worldpay;
        
class Drivers extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_login');
	$this->load->model('m_tools');
        $this->load->model('m_user');
        $this->load->model('m_delivery_zone');
        $this->load->model('m_notify');
        $this->m_login->check_session();
    }

    public function index() {
        $xcrud = Xcrud::get_instance();
	//driver
        $xcrud->table('user');
        $xcrud->where('user_type', 2);
	$xcrud->order_by('member_since', "desc");
        $xcrud->change_type('status', 'select', '1', array('1' => 'Active', '0' => 'Inactive'));        
        $xcrud->change_type('verify_doc', 'bool');
        $xcrud->change_type('is_vip_club_member', 'bool');
        $xcrud->subselect('verify_doc','{user_id}');
        $xcrud->column_callback('verify_doc', 'verifiy_document');   
        $xcrud->column_callback('get_doc', 'show_verify_doc');
        $xcrud->subselect('ufirstname','{firstname}');
        $xcrud->subselect('ulastname','{lastname}');
        $xcrud->column_pattern('firstname','{firstname} {lastname}');
	$xcrud->relation("dzone_id", "delivery_zone", "dzone_id", "city");
	$xcrud->set_var('s3_path', S3_PATH.'driver/');
	$xcrud->column_callback('profile_image', 'list_img_function');
        $xcrud->columns('profile_image,firstname,email,mobileno,dzone_id,verify_doc, status', false);
	
        $xcrud->fields('profile_image, status, is_online, userno, ufirstname, ulastname, birthdate, email, country_code, mobileno, address, city, postalcode, wallet, dzone_id, member_since, has_connect_ac, account_number, bank_name, routing_no, name_of_card ', false, 'Personal Details', 'view');
	
        $xcrud->search_columns('firstname,email,mobileno', 'userno'); 
	$xcrud->change_type('has_connect_ac', 'bool');
	$xcrud->change_type('is_online', 'bool');
	
        $xcrud->label(array(
            'firstname' => 'Name',
            'verify_doc' => 'Verified?',
	    'dzone_id' => 'Delivery Zone',
	    'ufirstname' => 'Firstname',
	    'ulastname' => 'Lastname',
	    'routing_no' => 'Sort Code',
        ));
	
	// Verification Docs
	$docs = $xcrud->nested_table('Verification Docs','user_id','driver_docs','user_id');
	$docs->where("delete_status", 0);
        $docs->subselect('image_name_view', '{image_name}');
	$docs->column_callback('image_name', 'list_img_function');
	$docs->column_callback('image_name_view', 'view_img_function');
	$docs->fields('image_name_view', false, false, 'view');
        $docs->columns('image_name');
	$docs->set_var('s3_path', S3_PATH.'driver/');
        $docs->label(array(
            'image_name' => '',
	    'image_name_view' => '',
        ));
        $docs->hide_button('return');
	
	// Vehicle Details
	$vehicle = $xcrud->nested_table('Vehicle Details','user_id','vehicle_mst','driver_id');
	$vehicle->set_var('s3_path', S3_PATH.'driver/');
	$vehicle->column_callback('vehicle_ins_policy', 'list_pdf_function');
	
	$vehicle->fields('maker, model, registration_number, vehicle_info, vehicle_policy_number, vehicle_ins_policy, ins_certificate_no, status', false, 'Vehicle Details', 'view');
        $vehicle->columns('maker, model, registration_number, vehicle_info, vehicle_policy_number, vehicle_ins_policy, ins_certificate_no, status');
	$vehicle->change_type('status', 'select', '1', array('1' => 'Active', '0' => 'Inactive'));  
	$vehicle->set_var('s3_path', S3_PATH.'driver/');
        $vehicle->label(array(
            'ins_certificate_no' => 'Insurance Certificate',
	    'vehicle_ins_policy' => 'Vehicle Insurance Policy',
        ));
        $vehicle->hide_button('return');
	
	// Vehicle Images
	$vehicle_img = $vehicle->nested_table('Vehicle Images','vehicle_id','vehicle_images','vehicle_id');
	$vehicle_img->where("status", 1);
	$vehicle_img->subselect('image_name_view', '{image_name}');
	$vehicle_img->column_callback('image_name', 'list_img_function');
	$vehicle_img->column_callback('image_name_view', 'view_img_function');
	$vehicle_img->fields('image_name_view', false, 'Vehicle Images', 'view');
        $vehicle_img->columns('image_name');
	$vehicle_img->set_var('s3_path', S3_PATH.'driver/');
        $vehicle_img->label(array(
            'image_name' => '',
	    'image_name_view' => '',
        ));
        $vehicle_img->hide_button('return');
        
        $xcrud->unset_edit();
        $xcrud->unset_remove();
        $xcrud->unset_add();                
        
        //View Document
        $xcrud->button(S3_PATH.'verification_docs/{verification_doc}', 'View Document', 'glyphicon glyphicon-eye-open', 'btn-secondary', array(
            'target' => '_blank'
        ), array(
                'verification_doc', '!=', ''
            ));         
        
        //Inactive Users
        $xcrud->create_action('inactive', 'inactive_driver');
        $xcrud->button('#', 'Inactive', 'glyphicon glyphicon-ban-circle', 'xcrud-action btn-danger', array(
            'data-task' => 'action',
            'data-action' => 'inactive',
            'data-primary' => '{user_id}'), array(
            'status',
            '=',
            '1')
        );
        
        //Active Users
        $xcrud->create_action('active', 'active_driver');
        $xcrud->button('#', 'Active', 'glyphicon glyphicon-ok', 'xcrud-action btn-success', array(
            'data-task' => 'action',
            'data-action' => 'active',
            'data-primary' => '{user_id}'), array(
            'status',
            '!=',
            '1'));
        
//        $xcrud->button('drivers/add_delivery_zone?uid={user_id}', 'Add Delivery Zone', 'glyphicon glyphicon-map-marker', 'btn-warning');
//	
//	//View Stripe details
//	$xcrud->button('drivers/driver_details?sid={user_id}', 'Stripe Account', 'glyphicon glyphicon-home', 'btn-primary');  
	
//	//Create Stripe Account
//	$xcrud->button('#', 'Create Stripe Account', 'glyphicon glyphicon-plus', 'btn-default create_stripe', 
//	array(
//	    'data-primary' => '{user_id}'
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
        
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('drivers', $data);
        $this->load->view('footer');
    }
    
    public function driver_delivery_zone() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('user');
        $xcrud->where('user_type', 2); 
	$xcrud->relation("dzone_id", "delivery_zone", "dzone_id", "city");        
        $xcrud->columns('firstname, lastname, email,mobileno,dzone_id', false);        
        $xcrud->search_columns('firstname,lastname, email,mobileno, dzone_id', 'userno'); 
        $xcrud->label(array(
            'dzone_id' => 'Delivery Zone'
        ));
	
        $xcrud->unset_edit();
        $xcrud->unset_remove();
        $xcrud->unset_add();  
	$xcrud->unset_view();         
        	
	//View Stripe details
	$xcrud->button('drivers/add_delivery_zone?uid={user_id}', 'Add Delivery Zone', 'glyphicon glyphicon-map-marker', 'btn-warning');	
	        
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('drivers', $data);
        $this->load->view('footer');
    }
    
    public function driver_stripe_details() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('user');
        $xcrud->where('user_type', 2); 
        $xcrud->relation("dzone_id", "delivery_zone", "dzone_id", "city");        
        $xcrud->columns('firstname, lastname, email,mobileno,dzone_id', false);        
        $xcrud->search_columns('firstname,lastname, email,mobileno, dzone_id', 'userno'); 
        $xcrud->label(array(
            'dzone_id' => 'Delivery Zone'
        ));
        
        $xcrud->unset_edit();
        $xcrud->unset_remove();
        $xcrud->unset_add();  
	$xcrud->unset_view(); 
	
	//Create Stripe Account
	$xcrud->button('#', 'Create Stripe Account', 'glyphicon glyphicon-plus', 'btn-success create_stripe', 
	array(
	    'data-primary' => '{user_id}'
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
        
        	
	//View Stripe details
	$xcrud->button('drivers/driver_details?sid={user_id}', 'Stripe Account', 'glyphicon glyphicon-eye-open', 'btn-primary'); 	
	        
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('drivers', $data);
        $this->load->view('footer');
    }
    
    public function driver_details(){
	$xcrud = Xcrud::get_instance();
        $xcrud->table('stripe_connect_accounts');  
	$xcrud->join("user_id", "user", "user_id");
        $xcrud->where('stripe_connect_accounts.user_id', $_GET["sid"]);
	$xcrud->where('stripe_connect_accounts.type', 2);
	$xcrud->change_type("stripe_connect_accounts.is_primary", "bool");		
        $xcrud->columns('user.firstname, user.lastname, user.account_number, user.routing_no, stripe_connect_accounts.bank_name, stripe_connect_accounts.account_id, stripe_connect_accounts.is_primary', false);
        $xcrud->label(array(
            'doc_name' => 'Verification Document',
	    'account_id' => 'Account',
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
    
    public function driver_account(){
	//print_r($_POST); exit;
	$response = $this->m_user->create_driver_stripe_account($_POST["driver_id"]);
	//print_r($response); exit;
	echo json_encode($response); exit;
    } 
    
    public function add_zone(){
        $post = $_POST;
        $this->m_user->add_driver_delivery_zone($post);  
        echo "success";
    }
    
    public function add_delivery_zone() {
        //print_r($_GET); exit;
        $data["driver"] = $this->m_user->get_driver_details($_GET["uid"]);   
        $data["delivery_zones"] = $this->m_delivery_zone->get_delivery_zones();   
        $this->load->view('header', $data);
        $this->load->view('add_delivery_zone', $data);
        $this->load->view('footer');
    }
    
    public function admin_verified(){
        if( $_POST["tp_status"] == 1) {
            $push = array(
                'to_user_id' => $_POST["user_id"],
                'notification_type' => 5,
                'message' => 'Your age verification has been done by admin.', 
             );                
        }
        else if( $_POST["tp_status"] == 0) {
             $push = array(
                'to_user_id' => $_POST["user_id"],
                'notification_type' => 5,
                'message' => 'You have been unverified by admin.', 
             );               
        }
        $update = $this->m_user->add_admin_verified($_POST, $push);
        if($update) {
            $this->m_notify->send($push);
        }
    }
    
    public function view_history() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('user');
        $xcrud->where('user_type', 2); 
        $xcrud->relation("dzone_id", "delivery_zone", "dzone_id", "city");        
        $xcrud->columns('firstname, lastname, email,mobileno,dzone_id', false);        
        $xcrud->search_columns('firstname,lastname, email,mobileno, dzone_id', 'userno'); 
        $xcrud->label(array(
            'dzone_id' => 'Delivery Zone'
        ));
        
        $xcrud->unset_edit();
        $xcrud->unset_remove();
        $xcrud->unset_add();  
	$xcrud->unset_view(); 
        	
	//View details
	$xcrud->button('drivers/view_driver_details?sid={user_id}', 'Dashboard', 'glyphicon glyphicon-eye-open', 'btn-secondary'); 	
	        
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('drivers', $data);
        $this->load->view('footer');
    }
    
    public function view_driver_details(){
	$userid = $_GET["sid"];
	$data["driver"] = $this->m_tools->get_user_by_id($userid);
        $data["driver_total_orders"] = $this->m_user->driver_total_orders($userid);
	$data["driver_accepted_orders"] = $this->m_user->driver_accepted_orders($userid);
	$data["driver_rejected_orders"] = $this->m_user->driver_rejected_orders($userid);
	$data["get_total_income"] = $this->m_user->get_total_income($userid);
	$data["driver_total_delivered_orders"] = $this->m_user->driver_total_delivered_orders($userid);
       
        $this->load->view('header');
        $this->load->view('driver_dashboard', $data);
        $this->load->view('footer');
    }
}

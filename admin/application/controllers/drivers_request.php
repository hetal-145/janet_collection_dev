<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
        
class Drivers_request extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_login');
        $this->load->model('m_user');
        $this->load->model('m_delivery_zone');
        $this->load->model('m_notify');
        $this->m_login->check_session();
    }
    
    public function index() {}

    public function driver_request() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('driver_requests');
        $xcrud->order_by('date', "desc");
	$xcrud->set_var('s3_path', S3_PATH . 'driver/');
	$xcrud->subselect('image_view', '{image}');	
	$xcrud->column_callback('image', 'list_img_function');
	$xcrud->column_callback('image_view', 'view_img_function');
        $xcrud->relation("user_id", "user", "user_id", array("firstname", "lastname"));
        $xcrud->columns('user_id, firstname, lastname, birthdate, mobileno, email, image', false);
        $xcrud->fields('user_id, firstname, lastname, birthdate, mobileno, email, image_view', false);
        $xcrud->search_columns('user_id, firstname, lastname, birthdate, mobileno, email', 'user_id'); 
	
	$xcrud->label(array(
            'image' => 'Image',
	    'image_view' => 'Image'
        ));
        
        $xcrud->unset_edit();
        $xcrud->unset_remove();
        $xcrud->unset_add();    
	
        //Inactive Users
        $xcrud->create_action('inactive', 'inactive_user_request');
        $xcrud->button('#', 'Inactive', 'glyphicon glyphicon-ban-circle', 'xcrud-action btn-danger', array(
            'data-task' => 'action',
            'data-action' => 'inactive',
            'data-primary' => '{request_id}',
	    'data-user' => '{user_id}'), array(
            'status',
            '=',
            '1')
        );
        
        //Active Users
        $xcrud->create_action('active', 'active_user_request');
        $xcrud->button('#', 'Active', 'glyphicon glyphicon-ok', 'xcrud-action btn-success', array(
            'data-task' => 'action',
            'data-action' => 'active',
            'data-primary' => '{request_id}', 
	    'data-user' => '{user_id}'), array(
            'status',
            '!=',
            '1'));
        
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('driver_request', $data);
        $this->load->view('footer');
    }
    
    public function driver_vehicle_request() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('driver_vehicle_requests');	
        $xcrud->order_by('date', "desc");
	//$xcrud->set_var('s3_path', S3_PATH . 'driver/');	
        $xcrud->relation("user_id", "user", "user_id", array("firstname", "lastname"));
	$xcrud->relation("vehicle_id", "vehicle_mst", "vehicle_id", "registration_number");
	$xcrud->subselect('request_id_vehicle', '{request_id}');
	$xcrud->column_callback('request_id_vehicle', 'request_to_update_image');
        $xcrud->columns('user_id, car_name, vehicle_make, vehicle_reg_no, ins_policy_no, ins_certificate_no, request_id_vehicle', false);
        $xcrud->fields('user_id, car_name, vehicle_make, vehicle_reg_no, ins_policy_no, ins_certificate_no, request_id_vehicle', false, 'Vehicle Info', 'view');
	$xcrud->search_columns('user_id, car_name, vehicle_make, vehicle_reg_no, ins_policy_no, ins_certificate_no', 'user_id'); 

	$gallery = $xcrud->nested_table('Vehicle Images','request_id','driver_vehicle_image_request','request_id');
        $gallery->set_var('s3_path', S3_PATH.'driver/');
        $gallery->subselect('image_name_view', '{image_name}');
	$gallery->column_callback('image_name', 'list_img_function');
	$gallery->column_callback('image_name_view', 'view_img_function');
	$gallery->fields('image_name_view', false, false, 'view');
        $gallery->columns('image_name');
	$gallery->label(array(
            'image_name' => '',
	    'image_name_view' => '',
        ));
        $gallery->hide_button('return');	
        
        $xcrud->unset_edit();
        $xcrud->unset_remove();
        $xcrud->unset_add();    
	
        //Inactive Users
        $xcrud->create_action('inactive', 'inactive_user_vehicle_request');
        $xcrud->button('#', 'Inactive', 'glyphicon glyphicon-ban-circle', 'xcrud-action btn-danger', array(
            'data-task' => 'action',
            'data-action' => 'inactive',
            'data-primary' => '{request_id}'), array(
            'status',
            '=',
            '1')
        );
        
        //Active Users
        $xcrud->create_action('active', 'active_user_vehicle_request');
        $xcrud->button('#', 'Active', 'glyphicon glyphicon-ok', 'xcrud-action btn-success', array(
            'data-task' => 'action',
            'data-action' => 'active',
            'data-primary' => '{request_id}'), array(
            'status',
            '!=',
            '1'));
        
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('driver_request', $data);
        $this->load->view('footer');
    }
}

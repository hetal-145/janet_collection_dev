<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trading_hours extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_login');
        $this->load->model('m_tools');
	$this->load->model('m_trading_hours');
	
        $this->m_login->check_session();
    }

    public function index() {
        $xcrud = Xcrud::get_instance();
        $user_id = $this->session->userdata('user_id');
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
        
        $xcrud->create_action('inactive', 'inactive_trading_hours');        
        $xcrud->button('#', 'Inactive', 'glyphicon glyphicon-ban-circle', 'xcrud-action btn-danger', array(
            'data-task' => 'action',
            'data-action' => 'inactive',
            'data-primary' => '{thr_id}'), array(
            'status',
            '=',
            '1')
        );
        
        $xcrud->create_action('active', 'active_trading_hours');        
        $xcrud->button('#', 'Active', 'glyphicon glyphicon-ok', 'xcrud-action btn-success', array(
            'data-task' => 'action',
            'data-action' => 'active',
            'data-primary' => '{thr_id}'), array(
            'status',
            '!=',
            '1'));
	
	$xcrud->button('trading_hours/edit?tid={thr_id}', 'Edit', 'glyphicon glyphicon-edit', 'btn-warning');
        
        $xcrud->unset_remove();   
	$xcrud->unset_add();   
	$xcrud->unset_edit();   
        $xcrud->unset_view();   
	
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('trading_hours', $data);
        $this->load->view('footer');
    }
    
    public function add() {
        $data["weekday"] = $this->m_tools->get_weekdays();
        $this->load->view('header', $data);
        $this->load->view('add_hours', $data);
        $this->load->view('footer');
    }
    
    public function edit() {
        $data["weekday"] = $this->m_tools->get_weekdays();
        $data['trading_hours_details'] = $this->m_trading_hours->get_trading_hours($_GET["tid"]); 
        $this->load->view('header', $data);
        $this->load->view('edit_hours', $data);
        $this->load->view('footer');
    }
    
    public function save() {        
        $post = $_POST;
	//print_r($post); exit;
	
        $response = $this->m_trading_hours->add_trading_hours($post);
        if($response == 'exist'){
            echo "exist"; die;
        } else {
            echo "success"; die;
        }
    }
    
    public function update() {        
        $post = $_POST;
        $response = $this->m_trading_hours->update_trading_hours($post);
        if($response == 'notexist'){
            echo "notexist"; die;
        } else {
            echo "success"; die;
        }
    }
}

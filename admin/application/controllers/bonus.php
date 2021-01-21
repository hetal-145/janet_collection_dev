<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Bonus extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_login');
        $this->load->model('m_tools');
        $this->m_login->check_session();
    }

    public function index() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('bonus_list');           
        $xcrud->columns('bonus_type, bonus_amount, no_of_days, no_of_trips, no_of_deliveries, new_created_date, status'); 
        $xcrud->fields('bonus_type, bonus_amount, no_of_days, no_of_trips, no_of_deliveries'); 
        $xcrud->validation_required('bonus_type', 3);
	$xcrud->validation_required('bonus_amount', 1);
        $xcrud->search_columns('bonus_type, no_of_days', '');         
        $xcrud->change_type('status', 'select', '1', array('1' => 'Active', '0' => 'Expired'));
	$xcrud->create_action('active', 'active_bonus');
        $xcrud->create_action('inactive', 'inactive_bonus');
	$xcrud->label(array(
	    "new_created_date" => "Added On",
	    "bonus_type" => "Title",
	    "bonus_amount" => "Amount (in £)",
	    "no_of_days" => "Total Days",
	    "status" => "Status",
	));
        $xcrud->button('#', 'Inactive', 'glyphicon glyphicon-ban-circle', 'xcrud-action btn-danger', array(
            'data-task' => 'action',
            'data-action' => 'inactive',
            'data-primary' => '{bonus_id}'), array(
            'status',
            '=',
            '1')
        );
        $xcrud->button('#', 'Active', 'glyphicon glyphicon-ok', 'xcrud-action btn-success', array(
            'data-task' => 'action',
            'data-action' => 'active',
            'data-primary' => '{bonus_id}'), array(
            'status',
            '!=',
            '1'));
        
        $xcrud->unset_remove();
       
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('bonus', $data);
        $this->load->view('footer');
    }
}

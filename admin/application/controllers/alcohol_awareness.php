<?php
if(!defined('BASEPATH'))
    exit('No direct script access allowed');

class Alcohol_awareness extends CI_Controller{
    
    function __construct(){
        parent::__construct();
	include ('xcrud/xcrud.php');
        $this->load->model('m_alcohol_awareness');
        $this->load->model('m_login');
	$this->load->model('m_tools');
        $this->m_login->check_session();
    }
    
    public function index() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('alcohol_awareness');
	
	$xcrud->subselect('image_view', '{image}');
	$xcrud->set_var('s3_path', S3_PATH.'alcohol_awareness/');
	
	$xcrud->column_callback('image', 'list_img_function');	
	$xcrud->column_callback('image_view', 'view_img_function');
        $xcrud->columns("image, title, description, status");
        $xcrud->fields("image_view, title, description, date, update_date, status");        
        $xcrud->change_type("status", "select", "" ,array("1" => "Active", "0" => "Inactive"));
        $xcrud->label(array('image_view' => 'Image'));
	
        $xcrud->create_action('inactive', 'inactive_alcohol_awareness');        
        $xcrud->button('#', 'Inactive', 'glyphicon glyphicon-ban-circle', 'xcrud-action btn-danger', array(
            'data-task' => 'action',
            'data-action' => 'inactive',
            'data-primary' => '{aid}'), array(
            'status',
            '=',
            '1')
        );
	
	$xcrud->create_action('active', 'active_alcohol_awareness');
        $xcrud->button('#', 'Active', 'glyphicon glyphicon-ok', 'xcrud-action btn-success', array(
            'data-task' => 'action',
            'data-action' => 'active',
            'data-primary' => '{aid}'), array(
            'status',
            '!=',
            '1'));
	
	$xcrud->button('#', 'Edit', 'glyphicon glyphicon-edit', '', array(
            'data-toggle' => 'modal',
            'class' => 'btn btn-warning btn-sm edit_data',
            'data-target' => ".mdl_alcohol_awareness",
            'data-aid' => '{aid}'));
	
        $xcrud->unset_search();
        $xcrud->unset_remove();
	$xcrud->unset_add();
	$xcrud->unset_edit();
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('alcohol_awareness', $data);
        $this->load->view('footer');
    }
    
//    public function index(){
//        $data['res'] =  $this->m_alcohol_awareness->get_alcohol_awareness_data();
//        $this->load->view('header', $data);
//        $this->load->view('alcohol_awareness', $data);
//        $this->load->view('footer');
//    }
    
    public function add_alcohol_awareness(){
        $post = $_POST;
	$files = $_FILES;
        $res = $this->m_alcohol_awareness->update_alcohol_awareness_content($post, $files);
        if($res) {
	    echo $res; 
	}
    }
    
    public function get_alcohol_awareness(){
        $post = $_POST;
        $res = $this->m_alcohol_awareness->get_alcohol_awareness_data($post["aid"]);
	if($res) {
	    echo json_encode($res); 
	}
    }
}


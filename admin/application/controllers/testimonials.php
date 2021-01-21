<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Testimonials extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
	$this->load->model('m_testimonial');
        $this->load->model('m_login');
        $this->load->model('m_tools');
        $this->m_login->check_session();
    }
    
    public function index() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('testimonials');
	
	$xcrud->subselect('image_view', '{image}');
	$xcrud->set_var('s3_path', S3_PATH.'testimonial/');
	
	$xcrud->column_callback('image', 'list_img_function');	
	$xcrud->column_callback('image_view', 'view_img_function');
        $xcrud->columns("image, client_name, description, status");
        $xcrud->fields("image_view, client_name, description, date, update_date, status");        
        $xcrud->change_type("status", "select", "" ,array("1" => "Active", "0" => "Inactive"));
	$xcrud->label(array('image_view' => 'Image'));	
	
	$xcrud->create_action('active', 'active_testimonial');
        $xcrud->create_action('inactive', 'inactive_testimonial');
        
        $xcrud->button('#', 'Inactive', 'glyphicon glyphicon-ban-circle', 'xcrud-action btn-danger', array(
            'data-task' => 'action',
            'data-action' => 'inactive',
            'data-primary' => '{testimonials_id}'), array(
            'status',
            '=',
            '1')
        );
        $xcrud->button('#', 'Active', 'glyphicon glyphicon-ok', 'xcrud-action btn-success', array(
            'data-task' => 'action',
            'data-action' => 'active',
            'data-primary' => '{testimonials_id}'), array(
            'status',
            '!=',
            '1'));        
               
        $xcrud->button('#', 'Edit', 'glyphicon glyphicon-edit', '', array(
            'data-toggle' => 'modal',
            'class' => 'btn btn-warning btn-sm edit_data',
            'data-target' => ".mdl_testimonials",
            'data-testimonials_id' => '{testimonials_id}'));	
	
        $xcrud->unset_search();
        $xcrud->unset_remove();
	$xcrud->unset_add();
	$xcrud->unset_edit();
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('testimonials', $data);
        $this->load->view('footer');
    }
    
    public function add_testimonials(){
        $post = $_POST;
	$files = $_FILES;
        $res = $this->m_testimonial->update_testimonial_content($post, $files);
        if($res) {
	    echo $res; 
	}
    }
    
    public function get_testimonials(){
        $post = $_POST;
	$res = $this->m_testimonial->get_testimonial_data($post["testimonials_id"]);
	if($res) {
	    echo json_encode($res); 
	}
    }
}

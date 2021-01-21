<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Volume extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');

        $this->load->model('m_login');
        $this->load->model('m_volume');
        $this->load->model('m_tools');
        $this->m_login->check_session();
    }

    public function index() {

        $xcrud = Xcrud::get_instance();
        $xcrud->table('volume_mst');   
        $xcrud->validation_required('volumne_value', 1);
        
        //$xcrud->change_type('type', 'select', '', array('1' => 'Cl', '2' => 'mL', '3' => 'L'));
//        $xcrud->fk_relation('category_name', 'brand_id', 'brand_mst', 'brand_id', 'category_id','category_mst', 'category_id', 'category_name') ;  
//        $xcrud->relation('brand_id', 'brand_mst', 'brand_id', 'brand_name'); 
//        $xcrud->column_callback('volume_id', 'volume_type_callback');   
//        $xcrud->columns('category_name, brand_id, volume_id');
//        $xcrud->fields('category_name, brand_id, volume_id');
//        $xcrud->label(array( 'brand_id' => 'Brand', 'volume_id' => 'Volume', 'category_name' => 'Category' ));
//        $xcrud->search_columns('category_name, brand_id,volumne_value');
	
	$xcrud->fk_relation('category_name', 'brand_id', 'brand_category_allocation', 'brand_id', 'category_id','category_mst', 'category_id', 'category_name') ;
        $xcrud->relation('brand_id', 'brand_mst', 'brand_id', 'brand_name'); 
        $xcrud->column_callback('volume_id', 'volume_type_callback');   
        $xcrud->columns('category_name, brand_id, volume_id');
        $xcrud->fields('category_name, brand_id, volume_id');
        $xcrud->label(array( 'brand_id' => 'Brand', 'volume_id' => 'Volume', 'category_name' => 'Category / Sub category Name' ));
        $xcrud->search_columns('brand_id,volumne_value');
        
        $xcrud->button('#', 'Edit', 'glyphicon glyphicon-edit', '', array(
            'data-toggle' => 'modal',
            'class' => 'btn btn-warning btn-sm edit_data',
            'data-target' => ".mdl_volume",
            'data-primary' => '{volume_id}'));
        
        $xcrud->unset_add();
        $xcrud->unset_edit();
        $xcrud->unset_view();
        $xcrud->unset_remove();
        
        $data['brands'] = $this->m_tools->get_brand_list();
        $data['volumes'] = $this->m_tools->get_volume_type_list();
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('volume', $data);
        $this->load->view('footer');
    }
    
    public function save() {
        $post = $_POST;      
        
        $add_volume_data = $this->m_volume->add_volume($post);
        
        if($add_volume_data === 'exist1'){            
            echo 'exist'; die();
        }
        else if(!$add_volume_data)  {            
            echo 'error'; die();
        }
        else { 
            echo 'success'; die();
        }
    }
    
    public function get_volume() {
        $post = $_POST;
        $volume = $this->m_volume->get_volume_details($post);
        
        if ($volume) {
            echo json_encode($volume);
        }
    }

}

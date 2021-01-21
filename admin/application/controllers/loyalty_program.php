<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Loyalty_program extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_login');
        $this->m_login->check_session();
    }

    public function index() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('products'); 
        $xcrud->where('in_loyalty_club', 1);
        $xcrud->where('status', 1);
        $xcrud->relation('brand_id', 'brand_mst', 'brand_id', 'brand_name');
        $xcrud->relation('category_id', 'category_mst', 'category_id', 'category_name');
        $xcrud->relation('supplier_id', 'suppliers', 'supplier_id', 'supplier_name');
        $xcrud->set_var('s3_path', S3_PATH.'product/');
	$xcrud->column_callback('feature_img', 'list_img_function');
        $xcrud->columns('feature_img, category_id, brand_id, product_name, supplier_id, status', false);
	$xcrud->change_type('status', 'select', '', array('1' => 'Active', '0' => 'Inactive')); 
        
        $xcrud->search_columns('category_id,brand_id, product_name, supplier_id'); 
        
        $xcrud->unset_add();
        $xcrud->unset_edit();       
        $xcrud->unset_remove();
        $xcrud->unset_view();
        //$xcrud->unset_search();
        
        $xcrud->label(array(
            'feature_img' => 'Image',
            'product_name' => 'Product',
            'category_id' => 'Category / SubCategory',
            'brand_id' => 'Brand',
            'supplier_id' => 'Supplier',
        ));
        
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('product_in_loyalty', $data);
        $this->load->view('footer');
    }

    public function brand_in_loyalty() {
        
        $xcrud = Xcrud::get_instance();
        $xcrud->table('brand_mst'); 
        $xcrud->where('in_loyalty_club', 1);
        $xcrud->column_callback('category_id', 'category_callback'); 
	$xcrud->set_var('s3_path', S3_PATH.'brand/');
	$xcrud->column_callback('brand_logo', 'list_img_function');
        $xcrud->columns('category_id, brand_logo, brand_code, brand_name,status');                
        $xcrud->fields('category_id, brand_logo, brand_code, brand_name,status');               
        $xcrud->label(array(
            'category_id' => 'Category',
            'brand_logo' => 'Logo',
            'brand_code' => 'Code',
            'brand_name' => 'Brand',
        ));
        
        $xcrud->change_type('status', 'select', '', array('1' => 'Active', '0' => 'Inactive')); 
        
        $xcrud->unset_add();
        $xcrud->unset_edit();       
        $xcrud->unset_remove();
        $xcrud->unset_view();
        $xcrud->unset_search();
        
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('brand_in_loyalty', $data);
        $this->load->view('footer');
    }
    
    public function category_in_loyalty() {
        
        $xcrud = Xcrud::get_instance();
        $xcrud->table('category_mst'); 
        $xcrud->where('in_loyalty_club', 1);
	$xcrud->set_var('s3_path', S3_PATH.'category/');
	$xcrud->column_callback('category_img', 'list_img_function');
        $xcrud->column_callback('category_id', 'category_callback'); 
        $xcrud->columns('category_img, category_code, category_id,status');                
        $xcrud->fields('category_code, category_id, category_img,status'); 
        
        $xcrud->label(array(
            'parent_id' => 'Parent Category',
            'category_code' => 'Category Code',
            'category_id' => 'Category',
            'category_img' => 'Image',
        ));
        
        $xcrud->change_type('status', 'select', '', array('1' => 'Active', '0' => 'Inactive')); 
        
        $xcrud->unset_add();
        $xcrud->unset_edit();       
        $xcrud->unset_remove();
        $xcrud->unset_view();
        $xcrud->unset_search();
        
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('category_in_loyalty', $data);
        $this->load->view('footer');
    }

}

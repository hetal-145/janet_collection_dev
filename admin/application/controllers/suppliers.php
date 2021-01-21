<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Suppliers extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');

        $this->load->model('m_login');
        $this->load->model('m_suppliers');
        $this->load->model('m_tools');
        $this->m_login->check_session();
    }

    public function index() {

        $xcrud = Xcrud::get_instance();
        $xcrud->table('suppliers');   
        
        $xcrud->columns('supplier_code, supplier_name, supplier_email, supplier_mobileno');                
        $xcrud->fields('supplier_code, supplier_name, supplier_email, supplier_mobileno');                
        $xcrud->label(array(
            'supplier_code' => 'Supplier Code',
            'supplier_name' => 'Name',
            'supplier_email' => 'Email ID',
            'supplier_mobileno' => 'Mobile No'
        ));    
        $xcrud->search_columns('supplier_code,supplier_name, supplier_email', 'supplier_code');   
        
        
        $xcrud->button('#', 'Edit', 'glyphicon glyphicon-edit', '', array(
            'data-toggle' => 'modal',
            'class' => 'btn btn-warning btn-sm edit_data',
            'data-target' => ".mdl_supplier",
            'data-primary' => '{supplier_id}'));
        
        $xcrud->unset_add();
        $xcrud->unset_edit();
        $xcrud->unset_remove();
        
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('suppliers', $data);
        $this->load->view('footer');
    }
    
    public function save() {
        $post = $_POST;
        
        $add_supplier_data = $this->m_suppliers->add_suppliers($post);
        
        if($add_supplier_data === 'exist1'){            
            echo 'exist'; die();
        }
        else if(!$add_supplier_data)  {            
            echo 'error'; die();
        }
        else { 
            echo 'success'; die();
        }
    }
    
    public function get_suppliers() {
        $post = $_POST;
        $suppliers = $this->m_suppliers->get_supplier_details($post);
        if ($suppliers) {
            echo json_encode($suppliers);
        }
    }

}

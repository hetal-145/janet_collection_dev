<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Upload_documents extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');

        $this->load->model('m_login');
        $this->load->model('m_upload_documents');
        $this->load->model('m_tools');
        $this->m_login->check_session();
    }

    public function index() {
        $user_id = $this->session->userdata('user_id');
        $xcrud = Xcrud::get_instance();
        $xcrud->table('seller_verifications');  
        $xcrud->where('seller_id', $user_id);
	$xcrud->where('status', 1);
	$xcrud->relation("seller_id", "seller", "seller_id", "seller_name");
        $xcrud->columns('seller_id, doc_name', false);
	$xcrud->set_var('s3_path', S3_PATH.'seller/');
	$xcrud->column_callback('doc_name', 'list_img_function');
        
        
        $xcrud->label(array(
            'doc_name' => 'Verification Document',
	    'seller_id' => 'Seller Name',
        ));
        
        //$xcrud->button('property/view?pid={property_id}', 'View', 'glyphicon glyphicon-search', 'btn-info');
        $xcrud->button('upload_documents/edit?pid={seller_id}', 'Edit', 'glyphicon glyphicon-edit', 'btn-warning');
        
        $xcrud->unset_add();
        $xcrud->unset_edit();       
        $xcrud->unset_remove();
        $xcrud->unset_view();
        
        $data['content'] = $xcrud->render();       
        $this->load->view('header', $data);
        $this->load->view('upload_documents', $data);
        $this->load->view('footer');
    }
    
    public function edit(){ 
        $data = array();
        $data['documents'] = $this->m_upload_documents->get_documents($_GET["pid"]);
	$data['seller_name'] = $this->m_upload_documents->get_seller($_GET["pid"]);
	$data['seller_id'] = $_GET["pid"];
        $this->load->view('header', $data);
        $this->load->view('upload_documents_edit', $data);
        $this->load->view('footer');
    }
    
    public function save(){
        $post = $_POST;
        $files = $_FILES;
        $response = $this->m_upload_documents->save($post, $files);
        echo $response;
    }
    
    public function delete(){
        $post = $_POST;
        $response = $this->m_upload_documents->delete($post);
        echo $response;
    }
}

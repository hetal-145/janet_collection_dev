<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Faq_list extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');

        $this->load->model('m_faq');
        $this->load->model('m_login');
        $this->m_login->check_session();
    }

    public function index() {
        $xcrud = Xcrud::get_instance(); 
        $xcrud->table('seller_faq_mst');
        $xcrud->columns('faq_question, faq_answer');
        $xcrud->fields('faq_question, faq_answer');                
        $xcrud->unset_edit();
        $xcrud->unset_add();     
	$xcrud->unset_remove();
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('faq_list', $data);
        $this->load->view('footer');
    }
    
    public function index_1() {
        $xcrud = Xcrud::get_instance(); 
	$user_id = $this->session->userdata("user_id");
        $xcrud->table('seller_faq_mst');
	$xcrud->where("seller_id", $user_id);
        $xcrud->columns('faq_question, faq_answer');
        $xcrud->fields('faq_question, faq_answer');                
        $xcrud->unset_edit();
        $xcrud->unset_add();     
        $xcrud->button('faq_list/edit?fid={faq_id}', 'Edit', 'glyphicon glyphicon-edit', 'btn-warning');
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('faq_list', $data);
        $this->load->view('footer');
    }
    
    public function add() {
        $data = array();
        $this->load->view('header', $data);
        $this->load->view('faq_add', $data);
        $this->load->view('footer');
    }
    
    public function edit() {
        $data = array(); 
        $data['faq_details'] = $this->m_faq->get_faq($_GET["fid"]); 
        $this->load->view('header', $data);
        $this->load->view('faq_edit', $data);
        $this->load->view('footer');
    }
    
    public function save() {
        
        $post = $_POST;
        ///print_r($post); exit;
        
        if(isset($post["faq_id"]) && $post["faq_id"] != NULL) {
            $faq = $this->m_faq->update_faq($post);
        }
        else {
            $faq = $this->m_faq->add_faq($post);
        }
        if($faq == 'exist'){
            echo "exist";
            die;
        } else {
            echo "success";
            die;
        }
    }
}

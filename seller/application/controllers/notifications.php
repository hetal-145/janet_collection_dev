<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Notifications extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
	$this->load->model('m_tools');
	$this->load->model('m_login');
        $this->m_login->check_session();
    }

    public function index() {
        $xcrud = Xcrud::get_instance(); 
	$seller_id = $this->session->userdata("user_id");
        $xcrud->table('website_notification');
	$xcrud->where("seller_id", $seller_id);
	$xcrud->order_by("date", "desc");    
	$xcrud->relation("order_id", "orders", "order_id", "order_no");
	$xcrud->columns('order_id,message,date');
        $xcrud->unset_edit();
        $xcrud->unset_add();  
	$xcrud->unset_view();
	$xcrud->unset_remove();
	$this->read_all($seller_id);
       // $xcrud->button('faq_list/edit?fid={faq_id}', 'Edit', 'glyphicon glyphicon-edit', 'btn-warning');
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('notifications', $data);
        $this->load->view('footer');
    }
    
    public function read_all($seller_id) {
	$check_exist = $this->db->select("*")->where("seller_id", $seller_id)->where('is_read', 0)->get("website_notification")->result_array();
	if(!empty($check_exist)) {
	    $this->db->set("is_read", 1)->where("seller_id", $seller_id)->where('is_read', 0)->update("website_notification");
	}
    }
    
    

}

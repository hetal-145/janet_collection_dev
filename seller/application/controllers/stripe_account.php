<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Stripe_account extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_otp');
        $this->load->model('m_login');
	$this->load->model('m_tools');
        $this->m_login->check_session();
    }

    public function index() {
	$seller_id = $this->session->userdata('user_id');
	$xcrud = Xcrud::get_instance();
        $xcrud->table('stripe_connect_accounts');  
	$xcrud->join("user_id", "seller", "seller_id");
        $xcrud->where('stripe_connect_accounts.user_id', $seller_id);
	$xcrud->where('stripe_connect_accounts.type', 1);
        $xcrud->columns('seller.seller_name, seller.code, seller.account_number, seller.routing_no, stripe_connect_accounts.bank_name, stripe_connect_accounts.account_id', false);
        $xcrud->label(array(
            'doc_name' => 'Verification Document',
	    'seller_id' => 'Seller Name',
        ));
        
        $xcrud->unset_add();
        $xcrud->unset_edit();       
        $xcrud->unset_remove();
        $xcrud->unset_view();
	$data['content'] = $xcrud->render();       
       
        $this->load->view('header', $data);
        $this->load->view('stripe_account', $data);
        $this->load->view('footer');
    }
    
    public function update_profile(){
        $user_id = $this->session->userdata('user_id');
        $post = $_POST;
        //print_r($post); exit;
        $this->db->set($post)->where("seller_id", $user_id)->update('seller');
        echo 'success';
    }
    
    

}

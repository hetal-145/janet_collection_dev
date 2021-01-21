<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//include('../vendor/worldpay/worldpay-lib-php/init.php');
//use Worldpay\Worldpay;
        
class Users extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_login');
	$this->load->model('m_tools');
        $this->load->model('m_user');	
        $this->load->model('m_notify');
        $this->m_login->check_session();
    }

    public function index() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('user');
        $xcrud->where('user_type', 1);
        $xcrud->change_type('status', 'select', '1', array('1' => 'Active', '0' => 'Inactive'));        
        $xcrud->change_type('verify_doc', 'bool');
        $xcrud->change_type('is_vip_club_member', 'bool');
        $xcrud->subselect('verify_doc','{user_id}');
        $xcrud->column_callback('verify_doc', 'verifiy_document');   
        $xcrud->subselect('ufirstname','{firstname}');
        $xcrud->subselect('ulastname','{lastname}');
        $xcrud->column_pattern('firstname','{firstname} {lastname}');
	$xcrud->set_var('s3_path', S3_PATH.'');
	$xcrud->column_callback('profile_image', 'list_img_function');
		
//        $xcrud->columns('firstname,email,mobileno,loyalty_point,wallet,is_vip_club_member, verify_doc, status', false);
//        $xcrud->fields('profile_image, status, userno, ufirstname, ulastname, birthdate, email, country_code, mobileno, address, city, postalcode, wallet, dzone_id, member_since', false, 'Personal Details', 'view');
        
        $xcrud->columns('firstname,email,mobileno,wallet, verify_doc, status', false);
        $xcrud->fields('profile_image, status, userno, ufirstname, ulastname, birthdate, email, country_code, mobileno, address, city, postalcode, wallet, dzone_id, member_since', false, 'Personal Details', 'view');
	
        $xcrud->search_columns('firstname,email,mobileno', 'firstname'); 
        
        $xcrud->label(array(
            'firstname' => 'Name',
            'loyalty_point' => 'Loyalty Points',
            'is_vip_club_member' => 'VIP Member?',
            'verify_doc' => 'Verified?',
	    'ufirstname' => 'Firstname',
	    'ulastname' => 'Lastname',
	    'routing_no' => 'Sort Code',
	    'dzone_id' => 'Delivery Zone',
        ));
        
        $xcrud->unset_edit();
        $xcrud->unset_remove();
        $xcrud->unset_add();                
        
        //View Document
        $xcrud->button(S3_PATH.'verification_docs/{verification_doc}', 'View Document', 'glyphicon glyphicon-eye-open', 'btn-secondary', array(
            'target' => '_blank'
        ), array(
                'verification_doc', '!=', ''
            ));         
        
        //Inactive Users
        $xcrud->create_action('inactive', 'inactive_user');
        $xcrud->button('#', 'Inactive', 'glyphicon glyphicon-ban-circle', 'xcrud-action btn-danger', array(
            'data-task' => 'action',
            'data-action' => 'inactive',
            'data-primary' => '{user_id}'), array(
            'status',
            '=',
            '1')
        );
        
        //Active Users
        $xcrud->create_action('active', 'active_user');
        $xcrud->button('#', 'Active', 'glyphicon glyphicon-ok', 'xcrud-action btn-success', array(
            'data-task' => 'action',
            'data-action' => 'active',
            'data-primary' => '{user_id}'), array(
            'status',
            '!=',
            '1'));
        
//        //Past Orders
//        $xcrud->button('users/user_order_history?uid={user_id}', 'Past Orders', 'glyphicon glyphicon-list', 'btn-warning');
        
        //Delete Document
        $xcrud->create_action('delete', 'delete_user_doc');
        $xcrud->button('#', 'Delete Document', 'glyphicon glyphicon-trash', 'xcrud-action btn-primary', array(
            'data-task' => 'action',
            'data-action' => 'delete',
            'data-primary' => '{user_id}'), array(
                'doc_deleted', '=', '0'
            )); 
        
        //Refund Order
//        $xcrud->button('users/product_return_history?uid={user_id}', 'Refund Amount', 'glyphicon glyphicon-transfer', 'btn-primary');
        
        
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('users', $data);
        $this->load->view('footer');
    }
    
    public function uorder_history() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('user');
        $xcrud->where('user_type', 1);
        $xcrud->change_type('is_vip_club_member', 'bool');
        $xcrud->columns('firstname, lastname, email,mobileno,wallet', false);    
        $xcrud->search_columns('firstname, lastname, email,mobileno', 'firstname');         
        $xcrud->label(array(
            'firstname' => 'Name',
            'loyalty_point' => 'Loyalty Points',
            'is_vip_club_member' => 'VIP Member?',
        ));
        
        $xcrud->unset_edit();
        $xcrud->unset_remove();
        $xcrud->unset_add();                
        $xcrud->unset_view();   
                
        //Past Orders
        $xcrud->button('users/user_order_history?uid={user_id}', 'Past Orders', 'glyphicon glyphicon-list', 'btn-warning');
        
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('users', $data);
        $this->load->view('footer');
    }
    
    public function product_return() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('user');
        $xcrud->where('user_type', 1);
        $xcrud->change_type('is_vip_club_member', 'bool');
        $xcrud->columns('firstname, lastname, email,mobileno,wallet', false);    
        $xcrud->search_columns('firstname, lastname, email,mobileno', 'firstname');         
        $xcrud->label(array(
            'firstname' => 'Name',
            'loyalty_point' => 'Loyalty Points',
            'is_vip_club_member' => 'VIP Member?',
        ));
        
        $xcrud->unset_edit();
        $xcrud->unset_remove();
        $xcrud->unset_add();                
        $xcrud->unset_view();           
                       
        //Refund Order
        $xcrud->button('users/product_return_history?uid={user_id}', 'Refund Amount', 'glyphicon glyphicon-transfer', 'btn-primary');        
        
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('users', $data);
        $this->load->view('footer');
    }
    
    public function admin_verified(){
        if( $_POST["tp_status"] == 1) {
            $push = array(
                'to_user_id' => $_POST["user_id"],
                'notification_type' => 5,
                'message' => 'Your age verification has been done by admin.', 
             );                
        }
        else if( $_POST["tp_status"] == 0) {
             $push = array(
                'to_user_id' => $_POST["user_id"],
                'notification_type' => 5,
                'message' => 'You have been unverified by admin.', 
             );               
        }
        $update = $this->m_user->add_admin_verified($_POST, $push);
        if($update) {
            $this->m_notify->send($push);
        }
    }
    
    public function push_notify(){   
        //print_r($_POST); exit;
        $push = array(
           'to_user_id' => $_POST["to_user_id"],
           'message' => 'Your age verification has been done by admin.', 
        );
        $this->m_notify->send($push);
    }
    
    public function user_order_history() {
        //print_r($_GET); exit;
        $xcrud = Xcrud::get_instance();
        $xcrud->table('orders');
        $xcrud->where('user_id', $_GET["uid"]);
        $xcrud->order_by('order_date','desc');
        $xcrud->relation('user_id','user','user_id',array('firstname','lastname'));
        $xcrud->relation('shipping_id','shipping_mst','shipping_id',array('address'));
        $xcrud->columns('user_id,order_no, order_date, net_amount, order_status', false);
        $xcrud->fields('order_status', false, false, 'edit');
        $xcrud->fields('user_id,order_no, order_date, net_amount, order_status, shipping_id', false, false, 'view');
        $xcrud->label(array(
            'user_id' => 'Customer',
        ));  
        $xcrud->change_type('order_status', 'select', '1', array('4' => 'Delivered','3' => 'Shipped','2' => 'Ready To Ship', '1' => 'Order Placed'));
        $xcrud->sum('net_amount');
        $xcrud->search_columns('order_no', 'order_no');  
        $xcrud->unset_view();
        $xcrud->unset_edit();
        $xcrud->unset_add();
        $xcrud->unset_remove();
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('user_order_history', $data);
        $this->load->view('footer');
    }
    
    public function product_return_history() {
        //print_r($_GET); exit;
        $xcrud = Xcrud::get_instance();
        $xcrud->table('products_returned');
        $xcrud->where('user_id', $_GET["uid"]);
        $xcrud->order_by('date','desc');
        $xcrud->relation('user_id','user','user_id',array('firstname','lastname'));
        $xcrud->subselect('order_no','select order_no from orders where order_id={order_id}');
        $xcrud->subselect('order_date','select DATE_FORMAT(order_date, "%d-%M-%Y %H:%i:%s") as order_date from orders where order_id={order_id}');
        $xcrud->subselect('net_amount','select net_amount from orders where order_id={order_id}');
        $xcrud->columns('user_id,order_no,order_date,net_amount,amount_refunded', false);
        $xcrud->label(array(
            'user_id' => 'Customer',
            'order_no' => 'Order No',
            'order_date' => 'Order Date',
            'net_amount' => 'Net Amount',
        ));  
        
        //Refund Order
        //$xcrud->button('users/refund_order?oid={product_return_id}', 'Refund', 'glyphicon glyphicon-transfer', 'btn-success', '', array('amount_refunded','=','0'));
        
        $xcrud->search_columns('order_no', 'order_no');  
        $xcrud->unset_view();
        $xcrud->unset_edit();
        $xcrud->unset_add();
        $xcrud->unset_remove();
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('product_return_history', $data);
        $this->load->view('footer');
    }
    
    function refund_order(){
        //print_r($_GET); exit; 
        $return = $this->db->select("*")->where('product_return_id', trim($_GET["oid"]))->get('products_returned')->row_array();
        
        $product = $this->db->select("*")
                ->where('order_id', $return["order_id"])
                ->where('product_id', $return["product_id"])
                ->where('volume_id', $return["volume_id"])
                ->get('order_product')->row_array();
        
        $refund_amount = $product["net_total"];
        
        //update in order product table
        $this->db->set('is_refund', 1)
                ->where('order_id', $return["order_id"])
                ->where('product_id', $return["product_id"])
                ->where('volume_id', $return["volume_id"])
                ->update('order_product');
        
        //update in product refund table
        $set = array(
            'is_confirmed' => 1,
            'amount_refunded' => $refund_amount,
            'payment_status' => 'SUCCESS',
            'update_date' => date('Y-m-d H:i:s'),
            'payment_history' => '{status:true, payment:success, amount:'.$refund_amount.'}',
        );
        
        $this->db->set($set)
                ->where('order_id', $return["order_id"])
                ->where('user_id', $return["user_id"])
                ->where('product_id', $return["product_id"])
                ->where('volume_id', $return["volume_id"])
                ->update('products_returned');
        
        
        //order reutrn transaction
        $set1 = array(
            'order_id' => $return["order_id"],
            'amount' => $refund_amount,
            'payment_status' => 'SUCCESS',
            'transaction_id' => date('YmdHis').$return["order_id"].$return["user_id"],
            'payment_history' => '{status:true, payment:success, amount:'.$refund_amount.'}',
            'product_return_id' => trim($_GET["oid"])    
        );
        
        $this->db->insert('order_return_transaction',$set1);
        
        //add  amount in wallet
        $wallet = $this->db->select('wallet')->where('user_id', $return["user_id"])->get('user')->row_array();        
        $wallet_amt = $wallet["wallet"] + $refund_amount;        
        $this->db->set('wallet', $wallet_amt)->where('user_id', $return["user_id"])->update('user');
                
        redirect(base_url('users/product_return_history?uid='.$return["user_id"]), 'refresh');

    }
}

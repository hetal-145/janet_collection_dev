<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Order_history extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_login');
        $this->load->model('m_orders');
        $this->load->model('m_notify');
        $this->m_login->check_session();
    }

    public function index() {}
    
    public function push_notify(){   
        //print_r($_POST); exit;
        $push = array(
           'to_user_id' => $_POST["to_user_id"],
           'message' => 'Order Delivered', 
        );
        $this->m_notify->send($push);
    }    
    
    function all_orders(){
        $xcrud = Xcrud::get_instance();
        $xcrud->table('orders');
        $order_where = "orders.order_status IN (2,3,6,4) and DATE(order_date) < CURDATE()";
        $xcrud->where($order_where);
        $xcrud->order_by('order_date','desc');
        $xcrud->relation('user_id','user','user_id',array('firstname','lastname'));
        $xcrud->columns("user_id, order_no, order_date, net_amount, order_status");
        $xcrud->label(array(
            'user_id' => 'Customer',
        ));  
        $xcrud->change_type('order_status', 'select', '1', array('4' => 'Delivered','3' => 'Shipped','2' => 'Ready To Ship', '6' => 'Order Placed', '1' => 'Pending', '7' => 'Reject/Cancel the order'));
        $xcrud->change_type('delivered_date','date');
        $currency = $this->db->select("*")->where("key", "currency")->get("setting")->row_array();
        $xcrud->change_type('net_amount', 'price', '{net_amount}', array('prefix'=>$currency["value"]));
        $xcrud->search_columns('order_no', 'order_no');          
        $xcrud->unset_view();
        $xcrud->unset_edit();
        $xcrud->unset_add();
        $xcrud->unset_remove();
        $xcrud->sum("net_amount");
        $xcrud->button('order_history/order_details?oid={order_id}', 'View', 'glyphicon glyphicon-search', 'btn-success');                
        //View Details
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('new_orders', $data);
        $this->load->view('footer');
    }
        
    function cancelled_orders(){
        $xcrud = Xcrud::get_instance();
        $xcrud->table('orders');
        $xcrud->join('orders.order_id', 'order_product', 'order_id');
        $order_where = "orders.order_status IN (5,7) and DATE(order_date) < CURDATE()";
        $xcrud->where($order_where);
        $xcrud->order_by('order_date','desc');
        $xcrud->relation('user_id','user','user_id',array('firstname','lastname'));
        $xcrud->columns("user_id, order_no, order_date, order_product.net_total, order_status, order_cancellation_reason");
        $xcrud->label(array(
            'user_id' => 'Customer',
        ));  
        $xcrud->change_type('order_status', 'select', '1', array('5' => 'Cancelled by Users', '7' => 'Reject/Cancelled by Seller'));
        $xcrud->change_type('delivered_date','date');
        $currency = $this->db->select("*")->where("key", "currency")->get("setting")->row_array();
        $xcrud->change_type('order_product.net_total', 'price', '{order_product.net_total}', array('prefix'=>$currency["value"]));
        $xcrud->search_columns('order_no', 'order_no');          
        $xcrud->unset_view();
        $xcrud->unset_edit();
        $xcrud->unset_add();
        $xcrud->unset_remove();
        $xcrud->sum("order_product.net_total");
        $xcrud->button('order_history/order_details?oid={order_id}', 'View', 'glyphicon glyphicon-search', 'btn-success');                
        //View Details
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('cancelled_orders', $data);
        $this->load->view('footer');
    }
    
    function return_orders(){
        $xcrud = Xcrud::get_instance();
        $xcrud->table('products_returned');
        $xcrud->join('products_returned.product_id', 'products', 'product_id');
        $where = "products_returned.is_confirmed IN (1,2)";
        $xcrud->where($where);
        $xcrud->order_by('products_returned.date','desc');
        $xcrud->relation('user_id','user','user_id',array('firstname','lastname'));
        $xcrud->subselect('order_no','select order_no from orders where order_id={order_id}');
        $xcrud->subselect('order_date','select DATE_FORMAT(order_date, "%d-%M-%Y %H:%i:%s") as order_date from orders where order_id={order_id}');
        $xcrud->subselect('net_amount','select net_total from order_product where order_id={order_id} and product_id={product_id}');
        $xcrud->columns('user_id,order_no,order_date,products.product_name, net_amount,amount_refunded,is_confirmed', false);
        $xcrud->search_columns('order_no', 'order_no');          
        $xcrud->label(array(
            'user_id' => 'Customer',
            'order_no' => 'Order No',
            'order_date' => 'Order Date',
            'net_amount' => 'Net Amount',
            'is_confirmed' => 'Request Status',
        )); 
        $xcrud->change_type("is_confirmed", "select", "1", array("0" => "Pending", "1" => "Approved", "2" => "Decline"));
        $currency = $this->db->select("*")->where("key", "currency")->get("setting")->row_array();
        $xcrud->change_type('net_amount', 'price', '{net_amount}', array('prefix'=>$currency["value"]));
        $xcrud->sum("net_amount");
        $xcrud->change_type('amount_refunded', 'price', '{amount_refunded}', array('prefix'=>$currency["value"]));
        $xcrud->sum("amount_refunded");
        
        $xcrud->unset_view();
        $xcrud->unset_edit();
        $xcrud->unset_add();
        $xcrud->unset_remove();
        
        //View Details
        $xcrud->button('order_history/order_details?oid={order_id}', 'View', 'glyphicon glyphicon-search', 'btn-success');                
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('return_orders', $data);
        $this->load->view('footer');
    }
    
    function order_details(){
        $data = array();
        $data['order_details'] = $this->m_orders->get_order_details($_GET["oid"]); 
        $this->load->view('header', $data);
        $this->load->view('order_detail', $data);
        $this->load->view('footer');
    }
}
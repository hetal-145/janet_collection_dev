<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Order_Rc extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_login');
        $this->load->model('m_orders');
        $this->load->model('m_notify');
        $this->m_login->check_session();
    }

    function index() {}
   
    function return_orders(){
        $xcrud = Xcrud::get_instance();
        $user_id = $this->session->userdata('user_id');
        $xcrud->table('products_returned');        
        $where = "products_returned.is_confirmed = 0";
        $xcrud->where($where);
        $xcrud->order_by('products_returned.date','desc');
        $xcrud->relation('user_id','user','user_id',array('firstname','lastname'));
        $xcrud->subselect('order_no','select order_no from orders where order_id={order_id}');
        $xcrud->subselect('order_date','select DATE_FORMAT(order_date, "%d-%M-%Y %H:%i:%s") as order_date from orders where order_id={order_id}');
        $xcrud->subselect('net_amount','select net_total from order_product where order_id={order_id} and product_id={product_id}');
        $xcrud->columns('user_id,order_no,order_date,net_amount,amount_refunded,is_confirmed', false);
        $xcrud->search_columns('order_no', 'order_no');          
        $xcrud->label(array(
            'user_id' => 'Customer',
            'order_no' => 'Order No',
            'order_date' => 'Order Date',
            'net_amount' => 'Net Amount',
            'is_confirmed' => 'Request Status',
        )); 
        $xcrud->change_type("is_confirmed", "select", "1", array("0" => "Pending", "1" => "Approved", "2" => "Decline"));
        
        //Refund Order
        $xcrud->button('order_rc/refund_order?oid={product_return_id}', 'Refund / Accept', 'glyphicon glyphicon-transfer', 'btn-success', '', array('is_confirmed', '=', '0'));
        $xcrud->button('order_rc/decline_refund_order?oid={product_return_id}', 'Decline', 'glyphicon glyphicon-ban-circle', 'btn-danger', '', array('is_confirmed', '=', '0'));
        
        $xcrud->unset_view();
        $xcrud->unset_edit();
        $xcrud->unset_add();
        $xcrud->unset_remove();
        
        //View Details
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('return_orders', $data);
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
            'payment_status' => 'SUCCESS',
            'transaction_id' => date('YmdHis').$return["order_id"].$return["user_id"],
            'payment_history' => '{status:true, payment:success, amount:'.$refund_amount.'}',
            'payment_mode' => 4,
            'is_returned' => 1
        );        
        $this->db->insert('order_transaction',$set1);        
        //add  amount in wallet
        $wallet = $this->db->select('wallet')->where('user_id', $return["user_id"])->get('user')->row_array();        
        $wallet_amt = $wallet["wallet"] + $refund_amount;        
        $this->db->set('wallet', $wallet_amt)->where('user_id', $return["user_id"])->update('user');                
        redirect(base_url('order_rc/return_orders'), 'refresh');
    }
    
    function decline_refund_order(){
        //print_r($_GET); exit; 
        $return = $this->db->select("*")->where('product_return_id', trim($_GET["oid"]))->get('products_returned')->row_array();
        $update = $this->db->set("is_confirmed", 2)->where('product_return_id', trim($_GET["oid"]))->update('products_returned');
        $push = array(
            'to_user_id' => $return["user_id"],
            'notification_type' => 8,
            'message' => 'Sorry, you cannot return product as seller as declined your return product request.', 
        );        
        if($update){
            $this->db->insert("notification", $push);
            //print_r($push);
            //$this->m_notify->send($push);
            redirect(base_url('order_rc/return_orders'), 'refresh');
        }
    }
    
    /*function cancel_orders(){
        $xcrud = Xcrud::get_instance();
        $user_id = $this->session->userdata('user_id');
        $xcrud->table('order_canceled'); 
        $xcrud->join('order_canceled.order_id', 'orders', 'order_id');
        $xcrud->join('orders.order_id', 'order_product', 'order_id');
        $where = "orders.order_status IN (5,7) and order_product.seller_id = ".$user_id;
        $xcrud->where($where);
        $xcrud->order_by('date','desc');
        $xcrud->relation('user_id','user','user_id',array('firstname','lastname'));
        $xcrud->subselect('order_no','select order_no from orders where order_id={order_id}');
        $xcrud->subselect('order_date','select DATE_FORMAT(order_date, "%d-%M-%Y %H:%i:%s") as order_date from orders where order_id={order_id}');
        $xcrud->columns('user_id,order_no,reason,order_date,amount_refunded,is_confirmed', false);
        $xcrud->search_columns('order_no', 'order_no');          
        $xcrud->label(array(
            'user_id' => 'Customer',
            'order_no' => 'Order No',
            'order_date' => 'Order Date',
            'net_amount' => 'Net Amount',
            'is_confirmed' => 'Request Status',
        )); 
        $xcrud->change_type("is_confirmed", "select", "1", array("0" => "Pending", "1" => "Approved", "2" => "Decline"));
        
        //Refund Order
        //$xcrud->button('order_rc/accept_cancel_order?oid={order_canceled_id}', 'Accept', 'glyphicon glyphicon-ok', 'btn-success', '', array('is_confirmed', '=', '0'));
        //$xcrud->button('order_rc/decline_cancel_order?oid={order_canceled_id}', 'Decline', 'glyphicon glyphicon-ban-circle', 'btn-danger', '', array('is_confirmed', '=', '0'));
        
        $xcrud->unset_view();
        $xcrud->unset_edit();
        $xcrud->unset_add();
        $xcrud->unset_remove();
        
        //View Details
        $xcrud->button('order_rc/order_details?oid={order_id}', 'View', 'glyphicon glyphicon-search', 'btn-info');
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('cancelled_orders', $data);
        $this->load->view('footer');
    }
    
    function accept_cancel_order(){
        //print_r($_GET); exit; 
        $cancel_req = $this->db->select("*")->where('order_canceled_id', trim($_GET["oid"]))->get('order_canceled')->row_array();
        $order_details = $this->db->select("*")->where('order_id', $cancel_req["order_id"])->get('orders')->row_array();
       
        //update order cancel table
        $set = array(
            'is_confirmed' => 1,
            'amount_refunded' => $order_details["gross_amount"],
            'payment_status' => 'SUCCESS',
            'update_date' => date('Y-m-d H:i:s'),
            'payment_history' => '{status:true, payment:success, amount:'.$order_details["gross_amount"].'}',
        ); 
        //print_r($set); exit;
        $this->db->set($set)->where('order_canceled_id', trim($_GET["oid"]))->update('order_canceled');    
        
        //update orders table
        $set1 = array(
            'order_status' => 4,
            'order_cancellation_reason' => $cancel_req["reason"],
            'updated_date' => date('Y-m-d H:i:s'),
        ); 
        //print_r($set1); exit;
        $this->db->set($set1)->where('order_id', $cancel_req["order_id"])->update('orders');
        
        //insert in order transaction table
        $set_arr2 = array(
            'order_id' => $cancel_req["order_id"],
            'payment_status' => 'SUCCESS',
            'transaction_id' => date('YmdHis').$cancel_req["order_id"].$cancel_req["user_id"],
            'payment_history' => '{status:true, payment:success, amount:'.$order_details["gross_amount"].'}',
        ); 
        
        //print_r($set_arr2); exit;
        $this->db->insert('order_transaction',$set_arr2);  
                 
        //get user wallet balance
        $wallet = $this->db->select('wallet')->where('user_id', $cancel_req["user_id"])->get('user')->row_array();        
        //update wallet balance
        $wallet_amt = $wallet["wallet"] + $order_details["gross_amount"];        
        $this->db->set('wallet', $wallet_amt)->where('user_id', $cancel_req["user_id"])->update('user');  
        
        redirect(base_url('order_rc/cancel_orders'), 'refresh');
    }
    
    function decline_cancel_order(){
        //print_r($_GET); exit; 
        $return = $this->db->select("*")->where('order_canceled_id', trim($_GET["oid"]))->get('order_canceled')->row_array();
        $update = $this->db->set("is_confirmed", 2)->where('order_canceled_id', trim($_GET["oid"]))->update('order_canceled');
        $push = array(
            'to_user_id' => $return["user_id"],
            'notification_type' => 9,
            'message' => 'Sorry, you cannot cancel order as seller as declined your order cancellation request.', 
        );        
        if($update){
            $this->db->insert("notification", $push);
            //print_r($push);
            //$this->m_notify->send($push);
            redirect(base_url('order_rc/cancel_orders'), 'refresh');
        }
    }
    
    function order_details(){
        $data = array();
        $data['order_details'] = $this->m_orders->get_order_details($_GET["oid"]); 
        $data['order_cancellation_details'] = $this->m_orders->order_cancellation($_GET["oid"]); 
        $this->load->view('header', $data);
        $this->load->view('order_cancel_view', $data);
        $this->load->view('footer');
    }*/
}

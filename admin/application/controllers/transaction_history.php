<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Transaction_history extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_login');
        $this->load->model('m_transaction_history');
        $this->load->model('m_notify');
        $this->m_login->check_session();
    }

    function index() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('order_transaction');
        $xcrud->join('order_transaction.order_id', 'orders', 'order_id');        
        $xcrud->order_by('orders.order_date','desc');
        $xcrud->column_callback('order_transaction_id', 'transaction_amount');
        $xcrud->columns('transaction_id, orders.order_no, orders.order_date, order_transaction_id, payment_mode, payment_status', false);
        $xcrud->change_type('orders.order_status', 'select', '1', array('5' => 'Order Cancelled by User','4' => 'Delivered','3' => 'Shipped','2' => 'Ready To Ship', '6' => 'Order Placed', '1' => 'Pending', '7' => 'Reject/Cancel the order'));
        $xcrud->change_type('payment_mode', 'select', '1', array('1' => 'Card Payment','2' => 'COD','3' => 'Gift Card Payment','4' => 'Wallet'));
        $xcrud->change_type('orders.delivered_date','date');
        $xcrud->search_columns('transaction_id, orders.order_no', 'transaction_id');         
//        $currency = $this->db->select("*")->where("key", "currency")->get("setting")->row_array();
//        $xcrud->change_type('orders.net_amount', 'price', '{orders.net_amount}', array('prefix'=>$currency["value"]));
        $xcrud->label(array(
            'order_transaction_id' => 'Net Amount'
        ));
        $xcrud->unset_view();
        $xcrud->unset_edit();
        $xcrud->unset_add();
        $xcrud->unset_remove();
        $xcrud->button('transaction_history/transaction_view?tid={order_transaction_id}', 'View', 'glyphicon glyphicon-search', 'btn-success');
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('transaction_history', $data);
        $this->load->view('footer');
    }
    
    function transaction_view(){
        $data = array();
        $data['transaction_details'] = $this->m_transaction_history->get_order_transaction($_GET["tid"]); 
        $this->load->view('header', $data);
        $this->load->view('transaction_view', $data);
        $this->load->view('footer');
    }
}

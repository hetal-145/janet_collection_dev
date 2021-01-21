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
        $user_id = $this->session->userdata('user_id');
        $xcrud->query('select ot.order_transaction_id, ot.transaction_id, o.order_no, DATE_FORMAT(o.order_date, "%d %M, %Y") as order_date, case ot.payment_mode when 1 then "Card Payment" when 2 then "COD" when 3 then "Gift Card Payment" when 4 then "Wallet" end as payment_mode, ot.payment_status from orders o join order_product op on op.order_id = o.order_id and op.seller_id = '.$user_id.' join order_transaction ot on ot.order_id = o.order_id group by o.order_id, ot.transaction_id order by o.order_date desc ');
        $xcrud->button('transaction_history/transaction_view?tid={order_transaction_id}', 'View', 'glyphicon glyphicon-search', 'btn-success');
        $xcrud->unset_view();
        $xcrud->unset_edit();
        $xcrud->unset_add();
        $xcrud->unset_remove();
        
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('transaction_history', $data);
        $this->load->view('footer');
        
        /*$xcrud = Xcrud::get_instance();
        $user_id = $this->session->userdata('user_id');
        $xcrud->table('order_transaction');
        $xcrud->join('order_transaction.order_id', 'orders', 'order_id');   
        $xcrud->join('orders.order_id', 'order_product', 'order_id');
        $xcrud->join('order_product.product_id', 'products', 'product_id');
        $order_where = "products.seller_id = ".$user_id;
        $xcrud->where($order_where);
        $xcrud->order_by('orders.order_date','desc');
        $xcrud->columns('transaction_id, orders.order_no, orders.order_date, orders.order_status, payment_mode, payment_status, is_returned, is_cancelled', false);
        $xcrud->change_type('payment_mode', 'select', '1', array('1' => 'Card Payment','2' => 'COD','3' => 'Gift Card Payment','4' => 'Wallet'));
        $xcrud->change_type('orders.delivered_date','date');
        $xcrud->sum('orders.net_amount');
        $xcrud->search_columns('transaction_id, orders.order_no', 'transaction_id');         
        $currency = $this->db->select("*")->where("key", "currency")->get("setting")->row_array();
        $xcrud->change_type('orders.net_amount', 'price', '{orders.net_amount}', array('prefix'=>$currency["value"]));
        $xcrud->unset_view();
        $xcrud->unset_edit();
        $xcrud->unset_add();
        $xcrud->unset_remove();
        $xcrud->button('transaction_history/transaction_view?tid={order_transaction_id}', 'View', 'glyphicon glyphicon-search', 'btn-success');
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('transaction_history', $data);
        $this->load->view('footer');*/
    }
    
    function transaction_view(){
        $data = array();
        $data['transaction_details'] = $this->m_transaction_history->get_order_transaction($_GET["tid"]); 
        $this->load->view('header', $data);
        $this->load->view('transaction_view', $data);
        $this->load->view('footer');
    }
}

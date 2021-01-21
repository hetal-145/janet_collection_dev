<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_order_history extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_login');
        $this->m_login->check_session();
    }

    public function index() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('orders');
        $xcrud->relation('user_id','user','user_id',array('firstname','lastname'));
        $xcrud->relation('shipping_id','shipping_mst','shipping_id',array('address'));
        //$xcrud->modal('order_status');
        $xcrud->columns('user_id,order_no, order_date, net_amount, order_status', false);
        $xcrud->fields('order_status', false, false, 'edit');
        $xcrud->fields('user_id,order_no, order_date, net_amount, order_status, shipping_id', false, false, 'view');
        $xcrud->label(array(
            'user_id' => 'Customer',
        ));  
        $xcrud->change_type('order_status', 'select', '1', array('4' => 'Delivered','3' => 'Shipped','2' => 'Ready To Ship', '1' => 'Order Placed'));
        $xcrud->sum('net_amount');
        $xcrud->search_columns('order_no', 'order_no');   
         
         
        //$xcrud->unset_view();
        //$xcrud->unset_edit();
        $xcrud->unset_add();
        $xcrud->unset_remove();
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('user_order_history', $data);
        $this->load->view('footer');
    }
    
    
    

}

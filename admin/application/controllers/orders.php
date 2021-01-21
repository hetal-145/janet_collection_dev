<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Orders extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_login');
        $this->load->model('m_tools');
	$this->load->model('m_orders');
        $this->load->model('m_notify');
	$this->load->model('m_notifyd');
        $this->m_login->check_session();
    }

    function index() {}
   
    function new_orders(){
	error_reporting(0);
        $xcrud = Xcrud::get_instance();
	$xcrud->query("select o.order_id, o.order_no as tracking_no, case o.order_type when 1 then 'Standard' when 2 then 'Scheduled' when 3 then 'Pickup' end as order_type, DATE_FORMAT(o.order_date, '%d %M, %Y') as order_date, case o.order_status when 1 then 'Pending' when 2 then 'Accepted by seller' when 3 then 'Accepted by driver' when 4 then 'Delivered'  when 5 then 'Cancelled by user'  when 6 then 'Order Accepted'  when 7 then 'Reject/Cancel the order by seller'  when 8 then 'Cancel by driver'  when 9 then 'Picked up'  when 10 then 'Start delivery'  when 11 then 'End delivery'  when 12 then 'Pause' when 13 then 'Order not completed' end as order_status, concat(u.firstname,' ',u.lastname) as name from orders o "
                . "join order_product op on op.order_id = o.order_id "
		. "join user u on u.user_id = o.user_id "
		. "where o.payment_done = 1 and o.order_status IN (1,2,3,6) "
		. "group by o.order_id "
		. "order by o.order_date desc "
		);
        
        //View Details
        $xcrud->button('orders/order_details?oid={order_id}', 'View', 'glyphicon glyphicon-eye-open', 'btn-info');
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('new_orders', $data);
        $this->load->view('footer');
    }
    
    function delivered_orders(){
	error_reporting(0);
        $xcrud = Xcrud::get_instance();
	
	$xcrud->query("select o.order_id, o.order_no as tracking_no, case o.order_type when 1 then 'Standard' when 2 then 'Scheduled' when 3 then 'Pickup' end as order_type, DATE_FORMAT(o.order_date, '%d %M, %Y') as order_date, case o.order_status when 1 then 'Pending' when 2 then 'Accepted by seller' when 3 then 'Accepted by driver' when 4 then 'Delivered'  when 5 then 'Cancelled by user'  when 6 then 'Order Accepted'  when 7 then 'Reject/Cancel the order by seller'  when 8 then 'Cancel by driver'  when 9 then 'Picked up'  when 10 then 'Start delivery'  when 11 then 'End delivery'  when 12 then 'Pause' when 13 then 'Order not completed' end as order_status, concat(u.firstname,' ',u.lastname) as name from orders o "
                . "join order_product op on op.order_id = o.order_id "
		. "join user u on u.user_id = o.user_id "
		. "where o.payment_done = 1 and o.order_status IN (4) "
		. "group by o.order_id "
		. "order by o.order_date desc "
		);
        
        //View Details
        $xcrud->button('orders/order_details?oid={order_id}', 'View', 'glyphicon glyphicon-eye-open', 'btn-info');
        $data['content'] = $xcrud->render();	
        $this->load->view('header', $data);
        $this->load->view('delivered_orders', $data);
        $this->load->view('footer');
    }
    
    function cancelled_orders(){
	error_reporting(0);	
        $xcrud = Xcrud::get_instance();
	$xcrud->query("select o.order_id, o.order_no as tracking_no, case o.order_type when 1 then 'Standard' when 2 then 'Scheduled' when 3 then 'Pickup' end as order_type, DATE_FORMAT(o.order_date, '%d %M, %Y') as order_date, case o.order_status when 1 then 'Pending' when 2 then 'Accepted by seller' when 3 then 'Accepted by driver' when 4 then 'Delivered'  when 5 then 'Cancelled by user'  when 6 then 'Order Accepted'  when 7 then 'Reject/Cancel the order by seller'  when 8 then 'Cancel by driver'  when 9 then 'Picked up'  when 10 then 'Start delivery'  when 11 then 'End delivery'  when 12 then 'Pause' when 13 then 'Order not completed' end as order_status, concat(u.firstname,' ',u.lastname) as name from orders o "
                . "join order_product op on op.order_id = o.order_id "
		. "join user u on u.user_id = o.user_id "
		. "where o.payment_done = 1 and o.order_status IN (5,7) "
		. "group by o.order_id "
		. "order by o.order_date desc "
		);
        
        //View Details
        $xcrud->button('orders/order_details?oid={order_id}', 'View', 'glyphicon glyphicon-eye-open', 'btn-info');
        $data['content'] = $xcrud->render();
	
	$this->load->view('header', $data);
	$this->load->view('cancelled_orders', $data);
	$this->load->view('footer');
    }
    
    function user_cancelled_orders(){
        $xcrud = Xcrud::get_instance();
        $xcrud->query("select o.order_id, o.order_no as tracking_no, case o.order_type when 1 then 'Standard' when 2 then 'Scheduled' when 3 then 'Pickup' end as order_type, DATE_FORMAT(o.order_date, '%d %M, %Y') as order_date, case o.order_status when 1 then 'Pending' when 2 then 'Accepted by seller' when 3 then 'Accepted by driver' when 4 then 'Delivered'  when 5 then 'Cancelled by user'  when 6 then 'Order Accepted'  when 7 then 'Reject/Cancel the order by seller'  when 8 then 'Cancel by driver'  when 9 then 'Picked up'  when 10 then 'Start delivery'  when 11 then 'End delivery'  when 12 then 'Pause' when 13 then 'Order not completed' end as order_status, concat(u.firstname,' ',u.lastname) as name from orders o "
                . "join order_product op on op.order_id = o.order_id "
		. "join user u on u.user_id = o.user_id"
		. "where o.payment_done = 1 and o.order_status IN (5) "
		. "group by o.order_id "
		. "order by o.order_date desc "
		);
        
        //View Details
        $xcrud->button('orders/order_details?oid={order_id}', 'View', 'glyphicon glyphicon-eye-open', 'btn-info');
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('user_cancelled_orders', $data);
        $this->load->view('footer');
    }
    
    function order_details(){
        $data = array();
        $data['order_details'] = $this->m_orders->get_order_details($_GET["oid"]); 
        $this->load->view('header', $data);
        $this->load->view('order_view', $data);
        $this->load->view('footer');
    }
    
    function save_order_status(){
        $post = $_POST;
        $response = $this->m_orders->save_order_status($post);
        echo $response;
    }  
    
    function order_pickup(){
        $post = $_POST;
        $response = $this->m_orders->order_pickup($post);
        echo $response;
    }  
    
    function update_order_status(){
        $post = $_POST;        
        $response = $this->m_orders->update_order_status($post);
        echo $response;
    }  
    
    function orders_not_completed(){
	error_reporting(0);
        $xcrud = Xcrud::get_instance();
	
	$xcrud->query("select o.order_id, o.order_no, DATE_FORMAT(o.order_date, '%d %M, %Y') as order_date, case o.order_status when 1 then 'Pending' when 2 then 'Accepted by seller' when 3 then 'Accepted by driver' when 4 then 'Delivered'  when 5 then 'Cancelled by user'  when 6 then 'Order Accepted'  when 7 then 'Reject/Cancel the order by seller'  when 8 then 'Cancel by driver'  when 9 then 'Picked up'  when 10 then 'Start delivery'  when 11 then 'End delivery'  when 12 then 'Pause' when 13 then 'Order not completed' end as ostatus, o.order_status, concat(u.firstname,' ',u.lastname) as name from orders o "
                . "join order_product op on op.order_id = o.order_id "
		. "join user u on u.user_id = o.user_id "
		. "where o.payment_done = 1 and o.order_status IN (13) "
		. "group by o.order_id "
		. "order by o.order_date desc "
		);
        
        //View Details
        $xcrud->button('orders/order_details?oid={order_id}', 'View', 'glyphicon glyphicon-eye-open', 'btn-info');
        $data['content'] = $xcrud->render();	
        $this->load->view('header', $data);
        $this->load->view('orders_not_completed', $data);
        $this->load->view('footer');
    }
    
    function orders_in_process(){
	error_reporting(0);
        $xcrud = Xcrud::get_instance();
	
	$xcrud->query("select o.order_id, o.order_no, DATE_FORMAT(o.order_date, '%d %M, %Y') as order_date, case o.order_status when 1 then 'Pending' when 2 then 'Accepted by seller' when 3 then 'Accepted by driver' when 4 then 'Delivered'  when 5 then 'Cancelled by user'  when 6 then 'Order Accepted'  when 7 then 'Reject/Cancel the order by seller'  when 8 then 'Cancel by driver'  when 9 then 'Picked up'  when 10 then 'Start delivery'  when 11 then 'End delivery'  when 12 then 'Pause' when 13 then 'Order not completed' end as ostatus, o.order_status, concat(u.firstname,' ',u.lastname) as name from orders o "
                . "join order_product op on op.order_id = o.order_id "
		. "join user u on u.user_id = o.user_id "
		. "where o.payment_done = 1 and o.order_status IN (9,11) "
		. "group by o.order_id "
		. "order by o.order_date desc "
		);
        
        //View Details
        $xcrud->button('orders/order_details?oid={order_id}', 'View', 'glyphicon glyphicon-eye-open', 'btn-info');
        $data['content'] = $xcrud->render();	
        $this->load->view('header', $data);
        $this->load->view('orders_in_process', $data);
        $this->load->view('footer');
    }
}

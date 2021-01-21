<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cart extends CI_Controller {
    
    function __construct() {
        parent::__construct();
	$this->load->model('m_tools');
        $this->load->model('m_login');
	$this->load->model('m_shipping');
        $this->load->model('m_cart');
	$this->load->model('m_gift_card');
        $this->load->model('m_home');
	$this->load->model('m_orders');
        $this->m_login->check_session();
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id');  
        $user = $this->m_tools->get_user_by_id($user_id);  
	if (!empty($this->session->userdata('user_id'))) {
	    $arr = array(
		'latitude' => $user["latitude"],
		'longitude' => $user["longitude"]
	    );
	    $response = $this->m_tools->use_api('get_top_pick_products', $arr);
	}
	else {
	    $response = $this->m_tools->use_api3('get_top_pick_products');
	}
        $data["top_picks"] = $response["top_pick_product"]; 
	$data["get_gift_card"] = $this->m_gift_card->get_received_gift_card_list(0); 
        $data["user"] = $user; 
        $checkout = $this->m_tools->use_api('checkout');
	
	if($checkout["status"] == "true") {
	    $data["checkout"] = $checkout["order"];        
	}
	else {
	    $data["checkout"] = $checkout["status"];
	}
	
	$current_shipping_details = $this->m_tools->use_api('get_shipping_by_id', array('shipping_id' => $user["shipping_id"]));
	if($current_shipping_details["status"] == "true") {
	    $data["current_shipping_details"] = $current_shipping_details["shipping_detail"];
	}
	else {
	    $data["current_shipping_details"] = 0;
	}	
	
        $shipping_details = $this->m_tools->use_api('get_shipping_details');
	if($shipping_details["status"] == "true") {
	    $data["shipping_details"] = $shipping_details["sipping_details"]; 
	}
	else {
	    $data["shipping_details"] = "";
	}
	
	$schedule_list = $this->m_tools->use_api('get_schedule_list');
	$data["schedule_list"] = $schedule_list["schedule_list"];
	
	$this->m_tools->template('cart', $data);
//        $this->load->view('header');
//        $this->load->view('cart', $data);
//        $this->load->view('footer');
    }
    
    public function update_current_address() {
        $post = $_POST;
//        $user_id = $this->session->userdata('user_id');
//        $response = $this->m_shipping->add_shipping_to_account($post["shipping_id"], $user_id);
        $response = $this->m_tools->use_api('add_shipping_to_account', $post);
//	print_r($response); exit;	
	if($response["status"] == "true") {
	    echo 2;
	}
        else if($response["status"] == "false") {
	    echo 1;
	}
    }
    
    public function confirm_order() {
        exit;
        $post = $_POST;
	//print_r($post); exit;
	if(isset($post["is_repeat_order"]) && $post["is_repeat_order"] == "1") {
	    $post["order_type"] = 2;
	}
	else {
	    $post["order_type"] = 1;
	}
	
        $response = $this->m_cart->confirm_order($post);	
	echo json_encode($response);
    }
    
    public function add_shipping() {
        $post = $_POST;
        $response = $this->m_shipping->add_shipping($post);
        echo $response;
    }
    
    public function apply_promocode() {
        $post = $_POST;
        $response = $this->m_cart->check_promocode($post);
        echo $response;
    }
    
    public function delete_shipping_details() {
        $post = $_POST;
//        $response = $this->m_shipping->remove_shipping_details($post["shipping_id"]);
        $response = $this->m_tools->use_api('remove_shipping_details', $post);
//	print_r($response); exit;
	if($response["status"] == "true") {
	    echo 1;
	}
        else if($response["status"] == "false") {
	    echo 2;
	}
    }
    
    public function remove_product() {
        $post = $_POST;
//        $user_id = $this->session->userdata('user_id');   
//        $post["user_id"] = $user_id;
//        $response = $this->m_cart->remove_product($post);
//        echo $response;
        $response = $this->m_tools->use_api('remove_product', $post);
	if($response["status"] == "true") {
	    echo 1;
	}
        else if($response["status"] == "false") {
	    echo 2;
	}
    }
    
    public function do_payment() {
//        $older_data = $this->m_orders->get_order_by_id($_GET['order_id']);
//        $older_data['total_price'] = 100 * $older_data['net_amount'];	
//        $this->load->view('order_payment', $older_data);
	
	$config_data = $this->db->where_in('key', array('payment_mode', 'test_public_key', 'client_key', 'test_secret_key', 'service_key'))->get('setting')->result_array();
	
	//print_r($config_data); exit;

	if($config_data[2]["value"] == '1') {
	    $older_data["public_key"] = $config_data[3]["value"];
	    $secret_key = $config_data[4]["value"];
	}
	else if($config_data[2]["value"] == '2') {
	    $older_data["public_key"] = $config_data[1]["value"];
	    $secret_key = $config_data[0]["value"];
	}
	
	//get order details
	$order_product = $this->db->select("op.*, p.product_name, p.description, p.feature_img", false)
		->join("products p", "op.product_id = p.product_id")
		->where("op.order_id", $_GET['order_id'])
		->get("order_product op")->result_array();

	$items = array();
	$seller_add = array();
	$product_qty = array();
	
	foreach($order_product as $key => $value) {
	    if(!empty($value["description"])) {
		$decs = $value["description"];
	    }
	    else {
		$decs = "no description";
	    }
	    //check similar seller
	    if(!in_array($value["seller_id"],$seller_add)) {
		
		//check for qty more than 1
		if($value["qty"] <= 1){
		    
		    $single = array(
			'name' => $value["product_name"],
			'description' => $decs,
			'images' => [PRODUCT_S3_PATH.$value["feature_img"]],
			'amount' => ($value["price"] + $value["delivery_charge"])*100,
			'currency' => CURRENCY,
			'quantity' => $value["qty"],
		    );
		    
		    array_push($items, $single);
		}
		else if($value["qty"] > 1){		    
		    for($i=1; $i <= $value["qty"]; $i++) {
			if($i == 1){
			    $single = array(
				'name' => $value["product_name"],
				'description' => $decs,
				'images' => [PRODUCT_S3_PATH.$value["feature_img"]],
				'amount' => ($value["price"] + $value["delivery_charge"])*100,
				'currency' => CURRENCY,
				'quantity' => 1,
			    );
			    
			    array_push($items, $single);
			}
			else if($i > 1){ 
			    $single = array(
				'name' => $value["product_name"],
				'description' => $decs,
				'images' => [PRODUCT_S3_PATH.$value["feature_img"]],
				'amount' => $value["price"]*100,
				'currency' => CURRENCY,
				'quantity' => 1,
			    );
			    
			    array_push($items, $single);
			}
		    }
		}
		
		//$amount = $value["price"] + $value["delivery_charge"];
		
		array_push($seller_add, $value["seller_id"]);
	    }
	    else {
		$single = array(
		    'name' => $value["product_name"],
		    'description' => $decs,
		    'images' => [PRODUCT_S3_PATH.$value["feature_img"]],
		    'amount' => ($value["price"])*100,
		    'currency' => CURRENCY,
		    'quantity' => $value["qty"],
		);
		
		array_push($items, $single);
	    }
	}
		
	//echo "<pre>"; print_r($items); exit;

	try{ 
	    \Stripe\Stripe::setApiKey($secret_key);

	    $session = \Stripe\Checkout\Session::create([
		'payment_method_types' => ['card'],
		'line_items' => $items,
		'mode' => 'payment',
		'success_url' => base_url().'cart/save_transation?session_id={CHECKOUT_SESSION_ID}&order_id='.$_GET['order_id'],
		'cancel_url' => base_url()."cart/payment_failed",
	    ]);
	    
	    $older_data["session_id"] = $session->id;
	} 
	catch (Exception $e) {
	    $session = $e->getError();            
	}
	
	//echo "<pre>"; print_r($session); exit;
	
        $this->load->view('order_payment', $older_data);
    }
    
    public function save_transation(){
        $post = $_GET;
        $order = $this->m_cart->save_transation($post);
        if ($order == 1) {  
            
//            $get_order = $this->db->select("user_id")
//                ->where('order_id', $post["order_id"])
//                ->get('orders')->row_array();
//            
//            $push = array(
//                'to_user_id' => $get_order["user_id"],
//                'message' => 'Order Has Been Placed',   
//                'notification_type' => 1
//            );
//
//            $this->m_api->create_notification($push);  
//            $this->m_notify->send($push);
//            $this->m_api->notify_driver($get_order["user_id"], $post["order_id"]);
	    $user_id = $this->session->userdata('user_id');  
	    $data["delivered_orders"] = $this->m_orders->my_past_order($user_id, 0);
	    $data["pending_orders"] = $this->m_orders->my_upcoming_order($user_id, 0);
	    $data["top_picks"] = $this->m_home->get_top_pick_product_list();  
	    $data["currency"] = $this->m_tools->get_currency(); 
                    
            redirect(base_url()."cart/payment_success");
        } 
        else {
            redirect(base_url()."cart/payment_failed");
        }
    }
    
    public function payment_success() {        
        redirect(base_url()."my_orders");
//	$this->load->view('header');
//        $this->load->view('my_orders');
//        $this->load->view('footer');
    }
    
    public function payment_failed() {        
        $this->load->view('header');
        $this->load->view('fail');
        $this->load->view('footer');
    }
    
    public function update_bag() {
        $post = $_POST;
//        $user_id = $this->session->userdata('user_id');   
//        $post["user_id"] = $user_id;
//        //print_r($post); exit;
//        $response = $this->m_cart->update_bag($post);
//        echo $response;
	$response = $this->m_tools->use_api('update_bag', $post);
	echo json_encode($response);
    }
}
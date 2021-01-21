<?php

include('../vendor/autoload.php');

class M_seller extends CI_Model {
    
    public function schedule_orders($seller_id) {
        $orders = $this->db->select("orders.order_id")
		->join("order_product", "order_product.order_id = orders.order_id and order_product.seller_id = ".$seller_id)
		->where('orders.payment_done',1)
		->where('orders.is_repeat_order',1)
                ->group_by('orders.order_id')
                ->get('orders')
                ->result_array();
        return count($orders);
    }
    
    public function get_total_products($seller_id) {
	$products = $this->db->where('status',1)
		->where('seller_id',$seller_id)
                ->get('products')
                ->result_array();
        return count($products);
    }
    
    public function get_total_categories() {
        $categories = $this->db->where('status',1)
                ->get('category_mst')
                ->result_array();
        return count($categories);
    }
    
    public function get_total_drivers() {
        $drivers = $this->db->where('status',1)
		->where('user_type',2)
                ->get('user')
                ->result_array();
        return count($drivers);
    }
    
    public function get_total_brands() {
        $brands = $this->db->where('status',1)
                ->get('brand_mst')
                ->result_array();
        return count($brands);
    }
    
    public function get_total_orders($seller_id) {
        $orders = $this->db->select("orders.order_id")
		->join("order_product", "order_product.order_id = orders.order_id and order_product.seller_id = ".$seller_id)
		->where('orders.payment_done',1)
                ->group_by('orders.order_id')
                ->get('orders')
                ->result_array();
        return count($orders);
    }
    
    public function get_total_new_orders($seller_id) {
        $orders = $this->db->select("orders.order_id")
		->join("order_product", "order_product.order_id = orders.order_id and order_product.seller_id = ".$seller_id)
		->where('orders.payment_done',1)
		->where('orders.order_status IN (1,2,3,6)')
		->group_by('orders.order_id')
                ->get('orders')
                ->num_rows();
        return $orders;
    }
    
    public function get_total_delivered_orders($seller_id) {
        $orders = $this->db->select("orders.order_id")
		->join("order_product", "order_product.order_id = orders.order_id and order_product.seller_id = ".$seller_id)
		->where('orders.payment_done',1)
		->where('orders.order_status',4)
                ->group_by('orders.order_id')
                ->get('orders')
                ->result_array();
        return count($orders);
    }
    
    public function get_total_cancelled_orders($seller_id) {
        $orders = $this->db->select("orders.order_id")
		->join("order_product", "order_product.order_id = orders.order_id and order_product.seller_id = ".$seller_id)
		->where('orders.payment_done',1)
		->where('orders.order_status IN (5,7)')
                ->group_by('orders.order_id')
                ->get('orders')
                ->result_array();
        return count($orders);
    }
    
    public function get_total_income($seller_id) {
        $income = $this->db->select("orders.order_id, FORMAT(sum(order_product.net_total), 2) as total", false)
		->join("order_product", "order_product.order_id = orders.order_id and order_product.seller_id = ".$seller_id)
		->where('order_product.is_refund',0)
		->where('orders.payment_done',1)
		->where('orders.order_status', 4)
                ->get('orders')
                ->result_array();
	
	return $income[0]["total"];
    }
    
    public function get_total_amount_to_receive($seller_id) {
        $income = $this->db->select("orders.order_id, FORMAT(sum(order_product.net_total), 2) as total", false)
		->join("order_product", "order_product.order_id = orders.order_id and order_product.seller_id = ".$seller_id)
		->where('order_product.is_refund',0)
		->where('orders.payment_done',1)
		->where('orders.order_status IN (1,2,3,6,9,10,11,12)')
                ->get('orders')
                ->result_array();
	
	return $income[0]["total"];
    }
    
    public function orders_not_completed($seller_id) {
        $orders = $this->db->select("orders.order_id")
		->join("order_product", "order_product.order_id = orders.order_id and order_product.seller_id = ".$seller_id)
		->where('orders.payment_done',1)
		->where('orders.order_status IN (13)')
                ->group_by('orders.order_id')
                ->get('orders')
                ->result_array();
        return count($orders);
    }
    
    public function orders_in_process($seller_id) {
        $orders = $this->db->select("orders.order_id")
		->join("order_product", "order_product.order_id = orders.order_id and order_product.seller_id = ".$seller_id)
		->where('orders.payment_done',1)
		->where('orders.order_status IN (9,11)')
                ->group_by('orders.order_id')
                ->get('orders')
                ->result_array();
        return count($orders);
    }
    
    public function create_stripe_account($seller_id){ 
	
	$seller = $this->db->select("*")->where("seller_id", $seller_id)->get("seller")->row_array();
       
	$dob = explode("-",$seller["dob"]);
	$seller_name = explode(" ",$seller["seller_name"]);
	if(isset($seller_name[1])) { 
	    $lastnam = $seller_name[1]; 	    
	}
	else {
	    $lastnam = "";
	}
	
	$sort = str_replace("-", "", $seller["routing_no"]);
	if(!empty($seller["routing_no"]) || !empty($seller["account_number"])) {
	    $config_data = $this->db->where_in('key', array('client_key', 'service_key', 'payment_mode', 'test_public_key', 'test_secret_key', 'currency', 'currency_code', 'country'))->get('setting')->result_array();

	    foreach ($config_data as $key => $row) {
		$tmp_arr[$row['key']] = $row['value'];
	    }

	    if($tmp_arr['payment_mode'] == '1') {
		$secret_key = $tmp_arr['test_secret_key'];
	    }
	    else if($tmp_arr['payment_mode'] == '2') {
		$secret_key = $tmp_arr['service_key'];
	    }

	    $config['client_key'] = $tmp_arr['client_key'];
	    $config['service_key'] = $secret_key;
	    $config['currency'] = $tmp_arr['currency'];
	    $config['country'] = COUNTRY;
	    $config['currency_code'] = CURRENCY;

	    $account = array(
		"type" => "custom",
		"country" =>$config['country'],
		"email" => $seller["email"],
		"external_account"=>[
		    "object"=>"bank_account",
		    "country"=>$config['country'],
		    "currency"=> strtolower(CURRENCY),
		    "account_holder_name"=>$seller["seller_name"],
		    "account_holder_type"=>"individual",
		    "routing_number"=>$sort,
		    "account_number"=>$seller["account_number"]
		],
		"tos_acceptance"=>[
		    "date"=>time(),
		    'ip' => $_SERVER['REMOTE_ADDR']
		],
		"requested_capabilities"=>[
		    "transfers", "card_payments", "legacy_payments"
		],
		"business_type" => "individual",
		"individual" => [
		    "address" => [
			"city" => $seller["city"],
			"line1" => $seller["address"],
			"postal_code" => $seller["postalcode"]
		    ],
		    "dob" => [
			    "day" => $dob[2],
			    "month" => $dob[1],
			    "year" => $dob[0]
		    ],
		    "email" => $seller["email"],
		    "first_name" => $seller_name[0],
		    "last_name" => $lastnam,
		    "phone" => "+".$seller["country_code"].$seller["contact_no"]
		],
		"business_profile"=>[
		    "url" => "https://www.Janet-Collection.com",
		    "product_description" => "Seller Stripe Connect Account"
		],
//		"settings"=>[
//		    "payouts" => [
//			"schedule" => [
//			    "interval" => "manual",
//			]
//		    ]
//		],
		"metadata" => [
		    "description" => $seller["seller_name"] . " Stripe Connect Account (Seller)"
		],
	    );

	    try{   
		\Stripe\Stripe::setApiKey($config["service_key"]); //secret key
		\Stripe\Stripe::setApiVersion("2019-12-03");
		$account_obj = \Stripe\Account::create($account);  
		$account_obj = json_encode($account_obj);
		$account_obj = json_decode($account_obj, true);

		$connect_accont = [
		    'user_id' => $seller_id,
		    'account_id' => $account_obj['external_accounts']['data'][0]['account'],
		    'bank_account' => $account_obj['external_accounts']['data'][0]['id'],
		    'bank_name' => $account_obj['external_accounts']['data'][0]['bank_name'],
		    'account_holder_name' => $account_obj['external_accounts']['data'][0]['account_holder_name'],
		    'account_number' => $seller["account_number"],
		    'routing_number' => $sort,
		    'is_primary' => 1,
		    'sort_code' => $sort,
		    'public_key' => $config['client_key'],
		    'secret_key' => $config['service_key'],
		    'response' => json_encode($account_obj),
		    'type' => 1,
		    "email" => $seller["email"],
		];

		$this->db->insert('stripe_connect_accounts', $connect_accont);
		$this->db->set("has_connect_ac", 1)->where("seller_id", $seller_id)->update("seller");

		$res = array(
		    'status' => 1,
		    'message' => 'Account successfully created'
		);
	    } 
	    catch (Exception $e) {
		$account_obj = $e->getError(); 
		$account_obj = json_encode($account_obj);
		$account_obj = json_decode($account_obj, true);

		$res = array(
		    'status' => 0,
		    'message' => $account_obj["message"]
		);
	    }
	    return $res;
	    //print_r($account_obj); exit;
	}
	else {
	    return array(
		    'status' => 0,
		    'message' => "Please add your account number & routing details"
		);
	}
    }
}


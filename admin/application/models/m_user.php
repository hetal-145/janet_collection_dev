<?php
include('../vendor/autoload.php');
class M_user extends CI_Model {
    
    function driver_total_orders($id) {
	$result = $this->db->select("*")
		->where("driver_id", $id)
		->group_by("order_id")
		->get("order_driver")->num_rows();
	return $result;
    }
    
    function driver_accepted_orders($id) {
	$result = $this->db->select("*")
		->where("status", 1)
		->where("driver_id", $id)
		->get("order_driver")->num_rows();
	return $result;
    }
    
    function get_total_income($id) {
	$result = $this->db->select("*, SUM(amount) as total_amt")
		->where("status", 1)
		->where("user_id", $id)
		->get("driver_earnings")->row_array();
	return $result;
    }
    
    function driver_total_delivered_orders($id) {
	$result = $this->db->select("order_driver.*")
		->join("orders", "orders.order_id = order_driver.order_id")
		->where("orders.order_status", 4)
		->where("order_driver.status", 1)
		->where("order_driver.driver_id", $id)
		->get("order_driver")->num_rows();
	return $result;
    }
    
    function driver_rejected_orders($id) {
	$result = $this->db->select("*")
		->where("status", 2)
		->where("driver_id", $id)
		->get("order_driver")->num_rows();
	return $result;
    }
    
    public function add_driver_delivery_zone($post = []) {
       // print_r($post); exit;
        $this->db->set("dzone_id", $post["dzone_id"])->where("user_id", $post["user_id"])->update("user");
        return 1;
    }
    
    public function get_driver_details($driver_id) {
        $user_info = $this->db
                ->where('status',1)
                ->where('user_id', $driver_id)
                ->where('user_type',2)
                ->get('user')
                ->row_array();
        
        if(!empty($user_info)) {
            unset($user_info["password"]);
            unset($user_info["token"]);
            return $user_info;
        }
    }
    
    function add_admin_verified($post = [], $push = []){
        //print_r($post); exit;
        $this->db->where(array(
            'user_id' => $post["user_id"],
            'status' => 1
        ));
        $this->db->set(array(
            'is_admin_verified' => $post["tp_status"],
            'date' => date('Y-m-d h:i:s'),
        ));
        $this->db->update('user');  
        
        $this->db->insert('notification', $push);
        return true;
    }
    
    function get_order_details($order_id){
        $order = $this->db->select("*")->where('order_id', $order_id)->get('orders')->row_array();
        if(!empty($order)) {
            $order_product = $this->db->select("*")->where('order_id', $order_id)->get('order_product')->result_array();
            $order_transaction = $this->db->select("*")->where('order_id', $order_id)->get('order_transaction')->result_array();

            $order["products"] = $order_product;
            $order["transaction"] = $order_transaction;
        }
        return $order;      
        
    }
    
    public function create_driver_stripe_account($driver_id){ 
	
	$driver = $this->db->select("*")->where("user_type", 2)->where("user_id", $driver_id)->get("user")->row_array();
       
	$dob = explode("-",$driver["birthdate"]);
	
	$email = $driver["email"];

	if(isset($driver["routing_number"]) && !empty($driver["routing_number"])) {
	    $sort = $driver["routing_number"];
	}
	else {
	    $sort = str_replace("-", "", $driver["sort_code"]);
	}
		
	if(!empty($driver["routing_no"]) || !empty($driver["account_number"])) {
	   
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
		"email" => $email,
		"external_account"=>[
		    "object"=>"bank_account",
		    "country"=>$config['country'],
		    "currency"=>$config['currency_code'],
		    "account_holder_name"=>$driver["firstname"]." ".$driver["lastname"],
		    "account_holder_type"=>"individual",
		    "routing_number"=>$sort,
		    "account_number"=>$driver["account_number"]
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
			"city" => $driver["city"],
			"line1" => $driver["address"],
			"postal_code" => $driver["postalcode"]
		    ],
		    "dob" => [
			    "day" => $dob[2],
			    "month" => $dob[1],
			    "year" => $dob[0]
		    ],
		    "email" => $email,
		    "first_name" => $driver["firstname"],
		    "last_name" => $driver["lastname"],
		    "phone" => "+".$driver["country_code"].$driver["mobileno"]
		],
		"business_profile"=>[
		    "url" => "https://www.Janet-Collection.com",
		    "product_description" => "Driver Stripe Connect Account"
		],
		"settings"=>[
		    "payouts" => [
			"schedule" => [
			    "interval" => "manual",
			]
		    ]
		],
		"metadata" => [
		    "description" => $driver["firstname"]." ".$driver["lastname"] . " Stripe Connect Account (Driver)"
		],
	    );

	    try{   
		\Stripe\Stripe::setApiKey($config["service_key"]); //secret key

		$account_obj = \Stripe\Account::create($account);  
		$account_obj = json_encode($account_obj);
		$account_obj = json_decode($account_obj, true);

		$connect_accont = [
		    'user_id' => $driver_id,
		    'account_id' => $account_obj['external_accounts']['data'][0]['account'],
		    'bank_account' => $account_obj['external_accounts']['data'][0]['id'],
		    'bank_name' => $account_obj['external_accounts']['data'][0]['bank_name'],
		    'account_holder_name' => $account_obj['external_accounts']['data'][0]['account_holder_name'],
		    'account_number' => $driver["account_number"],
		    'routing_number' => $sort,
		    'public_key' => $config['client_key'],
		    'secret_key' => $config['service_key'],
		    'response' => json_encode($account_obj),
		    'type' => 2,		    
		    'is_primary' => 1,
		    'email' => $email
		];

		$this->db->insert('stripe_connect_accounts', $connect_accont);
		$this->db->set("has_connect_ac", 1)->where("user_type", 2)->where("user_id", $driver_id)->update("user");

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
	   // print_r($account_obj); exit;
	}
	else {
	    return array(
		    'status' => 0,
		    'message' => "Please add your account number & routing details"
		);
	}
    }
}

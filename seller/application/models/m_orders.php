<?php
include('../vendor/autoload.php');
class M_orders extends CI_Model { 
    
    function order_pickup($post=[]){
        //print_r($post); exit;
        $seller_id = $this->session->userdata('user_id');            
		
	//update the pickup status
	$this->db->set("is_picked_up", 1)
		->where("seller_id", $seller_id)
		->where('order_id', $post["order_id"])
		->update("order_product");

	$check_status = $this->db->select("order_product_id")
		->where("is_picked_up", 0)
		->where('order_id', $post["order_id"])
		->get("order_product")->result_array();

	//print_r($check_status); exit;

	if(empty($check_status)) {
	    
	    //get nearest driver in seller zone
	    $seller = $this->m_tools->get_seller($seller_id);
	    
	    $drivers = $this->db->select("*")
		    ->where('order_id', $post["order_id"])
		    ->where('status', 1)
		    ->get("order_driver")->row_array();
	    
	   // print_r($drivers); exit;

	    if(!empty($drivers)) {
		//send notification to driver
		$this->notify_driver_pickup($drivers["driver_id"], $post["order_id"], $seller["seller_name"]);
	    }
	}

	return 'success';        
    }
    
    function save_order_status_stuart($post=[]){
        //print_r($post); exit;
        $seller_id = $this->session->userdata('user_id');
        $arr = array(
            'order_status' => $post["order_status"],
            'order_cancellation_reason' => $post["order_cancellation_reason"]
        );
        
        $userdata = $this->db->select('u.user_id, u.mobileno as phone, u.wallet, u.email, u.firstname, u.lastname, s.address, z.zipcode as postalcode', false)
		->join("shipping_mst s", "s.shipping_id = u.shipping_id")
		->join("zipcode z", "z.zipcode_id = s.zipcode_id", "left")
                ->where('u.status', 1)
                ->where("u.user_id", $post["user_id"])
                ->get('user u')->row_array();
        
        $update = $this->db->set($arr)->where('order_id', $post["order_id"])->update("orders");
        if($update){
            
            if( $post["order_status"] == 2 ){	
		
		$order_det = $this->db->select("order_done_type, order_no, track_no, add_info, net_amount")->where('order_id', $post["order_id"])->get('orders')->row_array();
		
		//get nearest driver in seller zone
		$seller = $this->m_tools->get_seller($seller_id);
		
		if($order_det["order_done_type"] == '1') {
                    if($seller["delivery_by"] == '2') {
                        $contactno = '+'.$seller["country_code"].$seller["contact_no"];
                        $sellername = explode(" ", $seller["seller_name"]);
                        $fname = (!empty($sellername[0])) ? $sellername[0] : "";
                        $lname = (!empty($sellername[1])) ? $sellername[1] : "";

                        //validate pickup address
                        $job_arr = array(
                            "job" => array(
                                "assignment_code" => $order_det["order_no"],
                                "pickups" => array(
                                    array(
                                        "address" => $seller["address"]." ".$seller["postalcode"],
                                        "comment" => "Handle with care",
                                        "contact" => array(
                                            "firstname" => $fname,
                                            "lastname" => $lname,
                                            "phone" => $contactno,
                                            "email" => $seller["email"],
                                            "company" => $seller["company_name"]
                                        )
                                    ),
                                ),
                                "dropoffs" => array(
                                    array(
                                        "package_type" => "medium",
                                        "package_description" => $order_det["add_info"],
                                        "client_reference" => $order_det["track_no"],
                                        "address" => $userdata["address"]." ".$userdata["postalcode"],
                                        "comment" => "Handle with care",
                                        "contact" => array(
                                            "firstname" => $userdata["firstname"],
                                            "lastname" => $userdata["lastname"],
                                            "email" => $userdata["email"]
                                        )
                                    )
                                ),
                                "transport_type" => "motorbike"
                            )
                        );	

                        $response = $this->m_tools->curl_fun_post('jobs', $job_arr);	    
                        $resps = json_decode($response, true);

                        if(isset($resps["id"])) {
                            $ins_arr = array(
                                'job_id' => $resps["id"],
                                'seller_id' => $seller_id,
                                'status' => $resps["status"],
                                'created_on' => $resps["created_at"],
                                'package_type' => $resps["package_type"],
                                'transport_type' => $resps["transport_type"],
                                'assignment_code' => $resps["assignment_code"],
                                'distance' => $resps["distance"],
                                'duration' => $resps["duration"],			
                                'pricing' => json_encode($resps["pricing"]),
                                'delivery_id' => $resps["deliveries"][0]["id"]
                            );

                            $this->db->insert("order_job", $ins_arr);
                            $lastjobid = $this->db->insert_id();

                            $ins_arr2 = array(
                                'order_id' => $post["order_id"],
                                'user_id' => $post["user_id"],
                                'order_job_id' => $lastjobid,
                                'job_id' => $resps["id"],
                                'status' => $resps["status"],
                                'delivery_type' => 'stuart',
                                'delivery_id' => $resps["deliveries"][0]["id"],
                                'delivery_status' => $resps["deliveries"][0]["status"]
                            );

                            $this->db->insert("order_delivery", $ins_arr2);
                            $net_amount = $order_det["net_amount"] + $resps["pricing"]["price_tax_included"];

                            $ins_arr3 = array(
                                'delivery_charges' => $resps["pricing"]["price_tax_included"],
                                'net_amount' => $net_amount
                            );

                            $this->db->set($ins_arr3)
                                    ->where('order_id', $post["order_id"])
                                    ->where('user_id', $post["user_id"])
                                    ->update("orders");

                            $this->db->set('delivery_charge', $resps["pricing"]["price_tax_included"])
                                    ->where('order_id', $post["order_id"])
                                    ->update("order_product");

                            //send notification to user
                            $insert_array = array(
                                'to_user_id' => $post["user_id"],
                                'notification_type' => 12,
                                'is_seller' => 0,
                                'message' => date("Hi")." Your order has been accepted by '".$seller["seller_name"]."', ".$seller["postalcode"].""
                            );
                            $this->db->insert("notification", $insert_array);

                            //send notification
                            $this->m_notify->send($insert_array);
                            //send sms
                            $this->nexmo->send_password($userdata["phone"], $insert_array["message"]); 
                        }
                        else {
                            return 'error';
                        }	                    
                    }
                    else if($seller["delivery_by"] == '1') {
                        $res = $this->notify_driver_now($seller, $post["order_id"]);
                        if($res == 1) {
                            return 'not_available';
                        }
                    }
		}
		else {
		    return 'success';
		}	
            }
            else if( $post["order_status"] == 7 ){
                //refund amount of order in wallet
                $order_details = $this->db->select("gross_amount")->where("order_id", $post["order_id"])->get("orders")->row_array();
                $wallet = $userdata["wallet"] + $order_details["gross_amount"];
                
                //update wallet
                $payment_history = '{status:true, payment:success, amount:'.$order_details["gross_amount"].'}';
                $transaction_id = $post['user_id'].date('YmdHis');        
                $payment_status = 'SUCCESS';
                $wallet_arr = array(
                    'user_id' => $post["user_id"],
                    'order_id' => $post["order_id"],
                    'type' => 2,
                    'debit_credit_amount' => $order_details["gross_amount"],
                    'balance_amount' => $wallet,
                    'payment_status' => $payment_status,
                    'payment_history' => $payment_history,
                    'transaction_id' => $transaction_id,
                );            
                $this->db->insert('wallet_history', $wallet_arr);
                //update in user
                $this->db->set('wallet', $wallet)->where('user_id', $post["user_id"])->update('user');
                
                $insert_array = array(
                    'to_user_id' => $post["user_id"],
                    'notification_type' => 7,
                    'is_seller' => 0,
                    'message' => date("Hi")." Your has been rejected / cancelled by seller because ".$post["order_cancellation_reason"]." and amount has been refunded in your wallet."
                );
                $this->db->insert("notification", $insert_array);
               
                //send notification
                $this->m_notify->send($insert_array);
                //send sms
                $this->nexmo->send_password($userdata["phone"], $insert_array["message"]); 
            }
            
            return 'success';
        }
        else {
            return 'error';
        }
    }
    
    function save_order_status($post=[]){
        //print_r($post); exit;
        $seller_id = $this->session->userdata('user_id');
        $arr = array(
            'order_status' => $post["order_status"],
            'order_cancellation_reason' => $post["order_cancellation_reason"]
        );
        
        $userdata = $this->db->select('user_id, mobileno as phone, wallet', false)
                ->where('status', 1)
                ->where("user_id", $post["user_id"])
                ->get('user')->row_array();
        
        $update = $this->db->set($arr)->where('order_id', $post["order_id"])->update("orders");
        if($update){
            
            if( $post["order_status"] == 2 ){	
		
		$order_det = $this->db->select("order_done_type")->where('order_id', $post["order_id"])->get('orders')->row_array();
		
		//get nearest driver in seller zone
		$seller = $this->m_tools->get_seller($seller_id);
		
		if($order_det["order_done_type"] == '1') {		
		    $res = $this->notify_driver_now($seller, $post["order_id"]);
		    if($res == 1) {
			return 'not_available';
		    }
		}

		//send notification to user
		$insert_array = array(
		    'to_user_id' => $post["user_id"],
		    'notification_type' => 12,
		    'is_seller' => 0,
		    'message' => date("Hi")." Your order has been accepted by '".$seller["seller_name"]."', ".$seller["postalcode"].""
		);
		$this->db->insert("notification", $insert_array);

		//send notification
		$this->m_notify->send($insert_array);
		//send sms
		$this->nexmo->send_password($userdata["phone"], $insert_array["message"]); 
		
            }
            else if( $post["order_status"] == 7 ){
                //refund amount of order in wallet
                $order_details = $this->db->select("gross_amount")->where("order_id", $post["order_id"])->get("orders")->row_array();
                $wallet = $userdata["wallet"] + $order_details["gross_amount"];
                
                //update wallet
                $payment_history = '{status:true, payment:success, amount:'.$order_details["gross_amount"].'}';
                $transaction_id = $post['user_id'].date('YmdHis');        
                $payment_status = 'SUCCESS';
                $wallet_arr = array(
                    'user_id' => $post["user_id"],
                    'order_id' => $post["order_id"],
                    'type' => 2,
                    'debit_credit_amount' => $order_details["gross_amount"],
                    'balance_amount' => $wallet,
                    'payment_status' => $payment_status,
                    'payment_history' => $payment_history,
                    'transaction_id' => $transaction_id,
                );            
                $this->db->insert('wallet_history', $wallet_arr);
                //update in user
                $this->db->set('wallet', $wallet)->where('user_id', $post["user_id"])->update('user');
                
                $insert_array = array(
                    'to_user_id' => $post["user_id"],
                    'notification_type' => 7,
                    'is_seller' => 0,
                    'message' => date("Hi")." Your has been rejected / cancelled by seller because ".$post["order_cancellation_reason"]." and amount has been refunded in your wallet."
                );
                $this->db->insert("notification", $insert_array);
               
                //send notification
                $this->m_notify->send($insert_array);
                //send sms
                $this->nexmo->send_password($userdata["phone"], $insert_array["message"]); 
            }
            
            return 'success';
        }
        else {
            return 'error';
        }
    }
    
    function get_order_details($order_id){
        $user_id = $this->session->userdata('user_id');
        //order
        $orders = $this->db->select("*, case order_payment_type when 1 then 'Card payment' when 2 then 'Cash on delivery' when 3 then 'Gift card payment' when 4 then 'Wallet' end as order_payment, case order_status when 1 then 'Pending' when 2 then 'Accepted by seller' when 3 then 'Accepted by driver' when 4 then 'Delivered'  when 5 then 'Cancelled by user'  when 6 then 'Order Accepted'  when 7 then 'Reject/Cancel the order by seller'  when 8 then 'Cancel by driver'  when 9 then 'Picked up'  when 10 then 'Start delivery'  when 11 then 'End delivery'  when 12 then 'Pause' when 13 then 'Order not completed' end as ostatus, case order_type when 1 then 'Normal Order' when 2 then 'Repeat Order' end as otype, case order_done_type when 1 then 'Immediate Order' when 2 then 'Schedule Order' end as order_done_types, order_type, case is_pick_up when 1 then 'Pickup By Customer' when 0 then 'Pickup By Driver' end as pickup_type", false)
                ->where("order_id", $order_id)->get("orders")->row_array();
        
        //order products
        $order_products = $this->db->select("order_product.*, products.product_name")
                ->join('products', 'products.product_id = order_product.product_id')
                ->where("order_product.order_id", $order_id)
                ->where("order_product.seller_id", $user_id)
                ->get("order_product")
                ->result_array();
        
        //currency
        $currency = $this->db->select("*")->where('key', 'currency')->get('setting')->row_array();
        $orders["currency"] = $currency["value"];
        $orders["products"] = $order_products;     
        
//        if(in_array(1, array_column($order_products, 'allow_split_order'))){
//            $orders["delivery_type"] = "Split Delivery";
//        }
//        else {
//            $orders["delivery_type"] = "Normal Delivery";
//        }
        
        //get drivers
        $driver_list = $this->db->select("*")
                ->where("order_id", $order_id)
                ->where('status IN (1,3)')
                ->get('order_driver')->row_array();       
        
        if(!empty($driver_list)){        
            $driveruser = $this->db->select("userno, firstname, lastname, mobileno, birthdate, email, profile_image")
                ->where('user_id', $driver_list["driver_id"])
                ->where('user_type', 2)
                ->get('user')->row_array();
	    
	    if(!empty($driveruser["profile_image"])) {
		$driveruser["profile_image"] = S3_PATH."driver/".$driveruser["profile_image"];
	    }

            $orders["driver_details"] = $driveruser;        
        }
        
        //user details
        $user = $this->db->select("userno, firstname, lastname, mobileno, birthdate, email, profile_image")
                ->where('user_id', $orders["user_id"])
                ->get('user')->row_array();
        
        $orders["user"] = $user; 
	
	//shipping details
	$shipping = $this->db->select("s.name, s.contactno, s.address, z.zipcode, d.city, d.area_code")
		->join("zipcode z", "z.zipcode_id = s.zipcode_id")
		->join("delivery_zone d", "d.dzone_id = z.dzone_id")
                ->where('s.shipping_id', $orders["shipping_id"])
                ->get('shipping_mst s')->row_array();
        
        $orders["shipping"] = $shipping;

	//seller details
        $seller = $this->m_tools->get_seller($user_id);        
        $orders["seller"] = $seller; 
	
        return $orders;
    }
    
    function order_cancellation($order_id){
        //order
        $orders = $this->db->select("*, case is_confirmed when 0 then 'Pending' when 1 then 'Accepted' when 2 then 'Rejected' end as confirmation_status", false)
                ->where("order_id", $order_id)->get("order_canceled")->row_array();
        if(!empty($orders)){
            return $orders;
        }
    }
    
    function update_order_status($post=[]){
        //print_r($post); exit;
        if($post["is_flag"] == 1) {
            $this->db->set("picked_by_driver", 1)->set("order_status", 2)->where("order_id", $post["order_id"])->update("orders");
        }
    }
    
    function notify_driver_now($seller = [], $order_id){
	$driver_not_busy = array();
	$driver_busy = array();
	$driver_not_busy2 = array();
	$driver_busy2 = array();
	
	//find driver in zone
	$drivers = $this->db->select('od.order_driver_id, od.status, u.user_id, u.dzone_id, get_distance_metres(u.latitude, u.longitude, '.$seller["latitude"].' , '.$seller["longitude"].') as distance', false)
		->join("order_driver od", "od.driver_id = u.user_id")
		//->join("orders o", "od.order_id = o.order_id and o.order_status NOT IN (3, 6, 9, 10, 11, 12)")
		->join("driver_schedule ds", "u.user_id = ds.driver_id and ds.schedule_date = '".date('Y-m-d')."' AND TIME_FORMAT(ds.start_time, '%H:%i') <= '".date('H:i')."' AND TIME_FORMAT(ds.end_time, '%H:%i') > '".date('H:i')."' and ds.status = 1")
		->where("u.user_type", 2)
		->where("u.is_online", 1)
		->where("u.dzone_id", $seller["dzone_id"])
		->where('get_distance_metres(u.latitude, u.longitude, '.$seller["latitude"].' , '.$seller["longitude"].') is not null')
		->where("od.order_driver_id IN (select max(order_driver_id) as order_driver_id from order_driver group by driver_id)")
		->order_by("od.order_driver_id", "desc")
		->order_by("distance", "asc")		
//		->get("user u")->row_array();
		->get("user u")->result_array();
	
	//print_r($drivers); exit;	
	if(!empty($drivers)) {
	    foreach($drivers as $ckey => $cvalue) {
		//get driver who are free
		if($cvalue["status"] == 1) {
		    array_push($driver_busy, $cvalue["user_id"]);
		}
		else {
		    array_push($driver_not_busy, $cvalue["user_id"]);
		}
	    }
	    //print_r($driver_not_busy); exit;
	    if(!empty($driver_not_busy)) {
		$rest = $this->send_notification_driver($driver_not_busy[0], $order_id);
		return $rest;
	    }
	    else {
		goto nextquery;
	    }
	}
	else {
	    nextquery:
	    $mins = $this->m_tools->get_setting('notify_driver_time_in_mins');
	    $current_time = date("H:i", strtotime("+".$mins." minutes")); 
	    
	    //find driver in zone
	    $drivers2 = $this->db->select('od.order_driver_id, od.status, u.user_id, u.dzone_id, get_distance_metres(u.latitude, u.longitude, '.$seller["latitude"].' , '.$seller["longitude"].') as distance', false)
		    ->join("order_driver od", "od.driver_id = u.user_id")
		    //->join("orders o", "od.order_id = o.order_id and o.order_status NOT IN (3, 6, 9, 10, 11, 12)")
		    ->join("driver_schedule ds", "u.user_id = ds.driver_id and ds.schedule_date = '".date('Y-m-d')."' AND TIME_FORMAT(ds.start_time, '%H:%i') <= '".$current_time."' AND TIME_FORMAT(ds.end_time, '%H:%i') > '".$current_time."' and ds.status = 1")
		    ->where("u.user_type", 2)
		    ->where("u.is_online", 1)
		    ->where("u.dzone_id", $seller["dzone_id"])
		    ->where('get_distance_metres(u.latitude, u.longitude, '.$seller["latitude"].' , '.$seller["longitude"].') is not null')
		    ->where("od.order_driver_id IN (select max(order_driver_id) as order_driver_id from order_driver group by driver_id)")
		    ->order_by("od.order_driver_id", "desc")
		    ->order_by("distance", "asc")
		    ->get("user u")->result_array();

	    //print_r($drivers2); exit;
	    if(!empty($drivers2)) {
		foreach($drivers2 as $ckey => $cvalue) {
		    //get driver who are free
		    if($cvalue["status"] == 1) {
			array_push($driver_busy2, $cvalue["user_id"]);
		    }
		    else {
			array_push($driver_not_busy2, $cvalue["user_id"]);
		    }
		}
		//print_r($driver_not_busy2); exit;
		if(!empty($driver_not_busy2)) {
		    $rest = $this->send_notification_driver($driver_not_busy2[0], $order_id);
		    return $rest;
		}
	    }
	    else {
		return 1;
	    }
	}	
    }
    
    function send_notification_driver($driver_id, $order_id) {
	$user_id = $driver_id;

	//order details
	$get_order = $this->db->select("orders.order_id, orders.user_id, orders.order_no, orders.net_amount, orders.order_date, orders.shipping_id")
		->where("orders.order_id", $order_id)
		->get("orders")->row_array();

	//user details
	$get_ltlg = $this->db->select("user.user_id, user.latitude, user.longitude, user.firstname, user.lastname, user.mobileno")
		->where("user.user_id", $get_order["user_id"])
		->where("user.status", 1)
		->get("user")->row_array();  

	$post["user_id"] = $get_ltlg["user_id"];
	$post["shipping_id"] = $get_order["shipping_id"];
	$shipping_details = $this->m_tools->get_shipping_by_id_without_status($post);
	$address = $shipping_details["address"].', '.$shipping_details["zipcode"];

	//get seller from order products table
	$find_seller = $this->db->select("seller_id")
		->where("order_id", $order_id)
		 ->get("order_product")->result_array(); 

	//get unique seller
	$seller_ids = array_unique(array_column($find_seller, "seller_id"));   

	$seller_arr = array();

	foreach($seller_ids as $ids) {
	    //seller details
	    $seller = $this->m_tools->get_seller($ids);

	    $sellr = array(
		"id" => $seller["seller_id"],
		"name" => $seller["seller_name"],
		"mobileno" => $seller["contact_no"],
		"latitude" => $seller["latitude"],
		"longitude" => $seller["longitude"],
		"address" => $seller["address"],
		"company_name" => $seller["company_name"],
	    );
	    array_push($seller_arr, $sellr);
	}

	// insert driver for order       
	$ins1 = array(
	    'driver_id' => $user_id,
	    'order_id' => $order_id,
	);

	$check = $this->db->select("*")
		->where($ins1)->get("order_driver")->result_array();
	if(empty($check)) {
	    $this->db->insert("order_driver", $ins1);
	}

	$push1 = array(
	    'to_user_id' => $user_id,
	    'message' => date("Hi").' An Order Placed by '.$get_ltlg["firstname"].' '.$get_ltlg["lastname"],   
	    'notification_type' => 10,
	    'driver_id' => $user_id,
	    'order_id' => $order_id,
	    'customer_id' => $get_ltlg["user_id"],
	);

	$this->db->insert("notification", $push1);

	$push2 = array(
	    'order_no' => $get_order["order_no"],
	    'order_date' => $get_order["order_date"],
	    'net_amount' => $get_order["net_amount"],
	    'user' => array(
		'id' => $get_ltlg["user_id"],
		'name' => $get_ltlg["firstname"].' '.$get_ltlg["lastname"],
		'mobileno' => $get_ltlg["mobileno"],
		'address' => $address,
		'latitude' => $get_ltlg["latitude"],
		'longitude' => $get_ltlg["longitude"]
	    ),
	    'seller' => $seller_arr
	);

	$push = array_merge($push1, $push2);
	$this->m_notifyd->send($push); 
	return $push;
    }
    
    function notify_driver_pickup($user_id, $order_id, $seller){
	//order details
        $get_order = $this->db->select("orders.order_id, orders.user_id")
                ->where("orders.order_id", $order_id)
                ->get("orders")->row_array();        
        
        $push = array(
            'to_user_id' => $user_id,
            'message' => date("Hi").' Please pickup the order from '.$seller,
            'notification_type' => 19,
            'driver_id' => $user_id,
            'order_id' => $order_id,
	    'customer_id' => $get_order["user_id"],
        );

        $this->db->insert("notification", $push);
        $this->m_notifyd->send($push); 
        return $push;
    }
}

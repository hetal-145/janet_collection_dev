<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cron_model extends CI_Model {   
    
    function get_setting($key) {
	$setting = $this->db->select("*")
		->where("key", $key)
		->get("setting")->row_array();
	
	return $setting["value"];
    }
    
    //notifiy driver after one driver reject it
    function notify_new_driver($order_id, $driver_id) {	
	$driver_not_busy = array();
	$driver_busy = array();
	$driver_not_busy2 = array();
	$driver_busy2 = array();
	
	//get seller id to send notification
	$order_seller = $this->db->select("*")->where("order_id", $order_id)->get("order_product")->result_array();  
        
        //get nearest driver in seller zone
	$seller = $this->m_apid->get_seller($order_seller[0]["seller_id"]);
	//find driver in zone
	$drivers = $this->db->select('od.order_driver_id, od.status, u.user_id, u.dzone_id, get_distance_metres(u.latitude, u.longitude, '.$seller["latitude"].' , '.$seller["longitude"].') as distance', false)
		->join("order_driver od", "od.driver_id = u.user_id")
		->join("driver_schedule ds", "u.user_id = ds.driver_id and ds.schedule_date = '".date("Y-m-d")."' AND TIME_FORMAT(ds.start_time, '%H:%i') <= '".date("H:i")."' AND TIME_FORMAT(ds.end_time, '%H:%i') > '".date("H:i")."' and ds.status = 1")
		->where("u.user_type", 2)
		->where("u.user_id NOT IN (".$driver_id.")")
		->where("u.is_online", 1)
		->where("u.dzone_id", $seller["dzone_id"])
		->where('get_distance_metres(u.latitude, u.longitude, '.$seller["latitude"].' , '.$seller["longitude"].') is not null')
		->where("od.order_driver_id IN (select max(order_driver_id) as order_driver_id from order_driver group by driver_id)")
		->order_by("od.order_driver_id", "desc")
		->order_by("distance", "asc")		
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
	    $mins = $this->get_setting('notify_driver_time_in_mins');
	    $current_time = date("H:i", strtotime("+".$mins." minutes")); 
	    
	    //find driver in zone
	    $drivers2 = $this->db->select('od.order_driver_id, od.status, u.user_id, u.dzone_id, get_distance_metres(u.latitude, u.longitude, '.$seller["latitude"].' , '.$seller["longitude"].') as distance', false)
		    ->join("order_driver od", "od.driver_id = u.user_id")
		    //->join("orders o", "od.order_id = o.order_id and o.order_status NOT IN (3, 6, 9, 10, 11, 12)")
		    ->join("driver_schedule ds", "u.user_id = ds.driver_id and ds.schedule_date = '".date('Y-m-d')."' AND TIME_FORMAT(ds.start_time, '%H:%i') <= '".$current_time."' AND TIME_FORMAT(ds.end_time, '%H:%i') > '".$current_time."' and ds.status = 1")
		    ->where("u.user_type", 2)
		    ->where("u.is_online", 1)
		    ->where("u.user_id NOT IN (".$driver_id.")")
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
	$shipping_details = $this->m_api->get_shipping_by_id_without_status($post);
	$address = $shipping_details["address"].', '.$shipping_details["zipcode"];
	//print_r($address);

	//get seller from order products table
	$find_seller = $this->db->select("seller_id")
		->where("order_id", $order_id)
		 ->get("order_product")->result_array(); 

	//get unique seller
	$seller_ids = array_unique(array_column($find_seller, "seller_id"));   

	$seller_arr = array();

	foreach($seller_ids as $ids) {
	//seller details
	$seller = $this->m_apid->get_seller($ids);

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

	//print_r($push); exit;
	$this->m_notifyd->send($push); 
	return $push;
    }
    
    function notify_driver(){
	//date_default_timezone_set("Asia/Kolkata");
	$driver_not_busy = array();
	$driver_busy = array();
	$driver_not_busy2 = array();
	$driver_busy2 = array();
	
	$mins = $this->get_setting('notify_driver_time_in_mins');
	$driver_notify_time = date('H:i', strtotime("+".$mins." minutes"));
	//get orders schedule today
	$orders = $this->db->select("order_id")
		->where("to_be_delivered_date", date('Y-m-d'))
		->where("TIME_FORMAT(start_slot, '%H:%i') <= '".$driver_notify_time."'")
		->where("order_status IN (2)")
		->where("order_done_type", 2)
		->get("orders")->result_array();
	
	//echo "<pre>"; print_r($orders); exit;
	
	if(!empty($orders)) {
	    
	    foreach($orders as $key => $value) {
		//order id
		$order_id = $value["order_id"];
		
		//seller details
		$products = $this->db->select("seller_id")->where('order_id', $order_id)->get('order_product')->result_array();
		
		//get nearest driver in seller zone
		$seller = $this->m_apid->get_seller($products[0]["seller_id"]); 
		
		//find driver in zone
		$drivers = $this->db->select('od.order_driver_id, od.status, u.user_id, u.dzone_id, get_distance_metres(u.latitude, u.longitude, '.$seller["latitude"].' , '.$seller["longitude"].') as distance', false)
			->join("order_driver od", "u.user_id = od.driver_id")
			->join("driver_schedule ds", "u.user_id = ds.driver_id and ds.schedule_date = '".date("Y-m-d")."' AND TIME_FORMAT(ds.start_time, '%H:%i') <= '".date("H:i")."' AND TIME_FORMAT(ds.end_time, '%H:%i') > '".date("H:i")."' and ds.status = 1")
			->where("u.user_type", 2)
			->where("u.is_online", 1)
			->where("u.dzone_id", $seller["dzone_id"])
			->where('get_distance_metres(u.latitude, u.longitude, '.$seller["latitude"].' , '.$seller["longitude"].') is not null')
			->where("od.order_driver_id IN (select max(order_driver_id) as order_driver_id from order_driver group by driver_id)")
			->order_by("od.order_driver_id", "desc")
			->order_by("distance", "asc")
			->get("user u")->result_array();

		//print_r($drivers); 
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
		    
		    //find driver in zone
		    $drivers2 = $this->db->select('od.order_driver_id, od.status, u.user_id, u.dzone_id, get_distance_metres(u.latitude, u.longitude, '.$seller["latitude"].' , '.$seller["longitude"].') as distance', false)
			->join("order_driver od", "u.user_id = od.driver_id")
			->join("driver_schedule ds", "u.user_id = ds.driver_id and ds.schedule_date = '".date("Y-m-d")."' AND TIME_FORMAT(ds.start_time, '%H:%i') <= '".$driver_notify_time."' AND TIME_FORMAT(ds.end_time, '%H:%i') > '".$driver_notify_time."' and ds.status = 1")
			->where("u.user_type", 2)
			->where("u.is_online", 1)
			->where("u.dzone_id", $seller["dzone_id"])
			->where('get_distance_metres(u.latitude, u.longitude, '.$seller["latitude"].' , '.$seller["longitude"].') is not null')
			->where("od.order_driver_id IN (select max(order_driver_id) as order_driver_id from order_driver group by driver_id)")
			->order_by("od.order_driver_id", "desc")
			->order_by("distance", "asc")
			->get("user u")->result_array();

		    //print_r($drivers2); 
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
		}
	    }
	}
    }
    
//    function notify_driver(){
//	
//	$driver_notify_time = date('H:i', strtotime("-60 minutes"));
//	//get orders schedule today
//	$orders = $this->db->select("order_id")
//		->where("to_be_delivered_date", date('Y-m-d'))
//		->where("TIME_FORMAT(start_slot, '%H:%i') = '".$driver_notify_time."'")
//		->where("order_status IN (2)")
//		->where("order_done_type", 2)
//		->get("orders")->result_array();
//	
//	//echo "<pre>"; print_r($orders); exit;
//	
//	if(!empty($orders)) {
//	    
//	    foreach($orders as $key => $value) {
//		//order id
//		$order_id = $value["order_id"];
//		
//		//seller details
//		$sell = array();
//		$products = $this->db->select("seller_id")->where('order_id', $order_id)->get('order_product')->result_array();
//		
//		//get nearest driver in seller zone
//		$seller = $this->db->select("seller_id, seller_name, dzone_id, latitude, longitude, postalcode, timezone")->where("seller_id", $products[0]["seller_id"])->get("seller")->row_array();
//		
//		date_default_timezone_set(trim($seller["timezone"]));
//		
//		//find driver in zone
//		$drivers = $this->db->select('u.user_id, u.dzone_id, get_distance_metres(u.latitude, u.longitude, '.$seller["latitude"].' , '.$seller["longitude"].') as distance', false)
//			->join("order_driver od", "u.user_id = od.driver_id")
//			->join("orders o", "od.order_id = o.order_id and o.order_status NOT IN (3, 6, 9, 10, 11, 12)")
//			->join("driver_schedule ds", "u.user_id = ds.driver_id and ds.schedule_date = '".date("Y-m-d")."' AND TIME_FORMAT(ds.start_time, '%H:%i') <= '".date("H:i")."' AND TIME_FORMAT(ds.end_time, '%H:%i') > '".date("H:i")."' and ds.status = 1")
//			->where("u.user_type", 2)
//			->where("u.is_online", 1)
//			->where("u.dzone_id", $seller["dzone_id"])
//			->where("o.to_be_delivered_date", date("Y-m-d"))
//			->where('get_distance_metres(u.latitude, u.longitude, '.$seller["latitude"].' , '.$seller["longitude"].') is not null')
//			->group_by("u.user_id")
//			->order_by("distance, od.updated_date", "asc, desc")
//			->limit(1)
//			->get("user u")->row_array();
//	//		->get("user u")->result_array();
//
//		//print_r($drivers); exit;
//
//		if(!empty($drivers)) {
//		    $user_id = $drivers["user_id"];
//
//		    //order details
//		    $get_order = $this->db->select("orders.order_id, orders.user_id, orders.order_no, orders.net_amount, orders.order_date, orders.shipping_id")
//			    ->where("orders.order_id", $order_id)
//			    ->get("orders")->row_array();
//
//		    //user details
//		    $get_ltlg = $this->db->select("user.user_id, user.latitude, user.longitude, user.firstname, user.lastname, user.mobileno")
//			    ->where("user.user_id", $get_order["user_id"])
//			    ->where("user.status", 1)
//			    ->get("user")->row_array();  
//
//		    $post["user_id"] = $get_ltlg["user_id"];
//		    $post["shipping_id"] = $get_order["shipping_id"];
//		    $shipping_details = $this->m_tools->get_shipping_by_id_without_status($post);
//		    $address = $shipping_details["address"].', '.$shipping_details["zipcode"];
//		    
//		    //get seller from order products table
//		    $find_seller = $this->db->select("seller_id")
//			    ->where("order_id", $order_id)
//			     ->get("order_product")->result_array(); 
//
//		    //get unique seller
//		    $seller_ids = array_unique(array_column($find_seller, "seller_id"));   
//
//		    $seller_arr = array();
//
//		    foreach($seller_ids as $ids) {
//		    //seller details
//		    $seller = $this->db->select("seller.seller_id, seller.seller_name, seller.contact_no, seller.address, seller.company_name, seller.latitude, seller.longitude, seller.timezone")
//			    ->where("seller_id", $ids)
//			    ->get("seller")->row_array();   
//
//			$sellr = array(
//			    "id" => $seller["seller_id"],
//			    "name" => $seller["seller_name"],
//			    "mobileno" => $seller["contact_no"],
//			    "latitude" => $seller["latitude"],
//			    "longitude" => $seller["longitude"],
//			    "address" => $seller["address"],
//			    "company_name" => $seller["company_name"],
//			);
//			array_push($seller_arr, $sellr);
//		    }
//
//		    // insert driver for order       
//		    $ins1 = array(
//			'driver_id' => $user_id,
//			'order_id' => $order_id,
//		    );
//
//		    $check = $this->db->select("*")
//			    ->where($ins1)->get("order_driver")->result_array();
//		    if(empty($check)) {
//			$this->db->insert("order_driver", $ins1);
//		    }
//
//		    $push1 = array(
//			'to_user_id' => $user_id,
//			'message' => date("Hi").' An Order Placed by '.$get_ltlg["firstname"].' '.$get_ltlg["lastname"],   
//			'notification_type' => 10,
//			'driver_id' => $user_id,
//			'order_id' => $order_id,
//			'customer_id' => $get_ltlg["user_id"],
//		    );
//
//		    $this->db->insert("notification", $push1);
//
//		    $push2 = array(
//			'order_no' => $get_order["order_no"],
//			'order_date' => $get_order["order_date"],
//			'net_amount' => $get_order["net_amount"],
//			'user' => array(
//			    'id' => $get_ltlg["user_id"],
//			    'name' => $get_ltlg["firstname"].' '.$get_ltlg["lastname"],
//			    'mobileno' => $get_ltlg["mobileno"],
//			    'address' => $address,
//			    'latitude' => $get_ltlg["latitude"],
//			    'longitude' => $get_ltlg["longitude"]
//			),
//			'seller' => $seller_arr
//		    );
//
//		    $push = array_merge($push1, $push2);
//
//		    //print_r($push); exit;
//		    $this->m_notifyd->send($push);
//		    return $push;
//		}
//	    }
//	}
//    }
}

    

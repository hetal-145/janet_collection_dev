<?php

require 'vendor_aws/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class M_cart extends CI_Model { 
    
    //confirm order
    function confirm_order($post = []) {  
	//print_r($post); exit;
        $loyalty_net_amount = 0;
        //get product details
        $product_details = $this->cart_product_checkout_list($post); 
        $seller_add = array();
        $total_delivery_charge = 0;
        //get cart details
        $cart = $this->get_cart($post["user_id"]);
        
        if(!empty($cart) && !empty($product_details)){   
            
            foreach($product_details as $pd){
                //check quantity
                $check_qty = $this->check_quantity($pd["product_id"], $pd["volume_id"], $pd["qty"]);
                
                if(!$check_qty){
                    return 1;
                } 
                
                //$total_delivery_charge = $total_delivery_charge + $pd["delivery_charges"];
		if(!in_array($pd["seller_id"],$seller_add)) {                
		    $total_delivery_charge = $total_delivery_charge + $pd["delivery_charges"];
		    array_push($seller_add, $pd["seller_id"]);
		}
            }
	    
	    if(!isset($post["card_id"])) {
		$post["card_id"] = "";
	    }
	    
	    if(!isset($post["promocode_id"])) {
		$post["promocode_id"] = "";
	    }
	    
	    if(!isset($post["points"])) {
		$post["points"] = "";
	    }
	    
	    if(!isset($post["wallet"])) {
		$post["wallet"] = "";
	    }
	    
	    if(!isset($post["to_be_delivered_date"])) {
		$post["to_be_delivered_date"] = "";
	    }
	    
	    if(!isset($post["time_slot"])) {
		$post["time_slot"] = "";
	    }
	    
	    if(!isset($post["is_repeat_order"])) {
		$post["is_repeat_order"] = "0";
	    }
	    
	    if(!isset($post["online"])) {
		$post["online"] = "no";
	    }
            
            if(isset($post["send_as_gift"]) && $post["send_as_gift"] == "true"){
                $send_as_gift = 1;
            } else if(isset($post["send_as_gift"]) && $post["send_as_gift"] == "false"){
                $send_as_gift = 0;
            }
            else {
                $send_as_gift = 0;
		$post["send_as_gift"] = "false";
            }
            
            if(isset($post["is_pick_up"]) && $post["is_pick_up"] == '1'){
                $is_pick_up = $post["is_pick_up"];
            }
            else {
                $is_pick_up = 0;
		$post["is_pick_up"] = "0";
            }
	    
	    if(isset($post["add_info"]) && !empty($post["add_info"])){
                $add_info = $post["add_info"];
            }
            else {
                $add_info = '';
		$post["add_info"] = "";
            }

            $post["total_amount"] = $cart["total_amount"];  
            $post["delivery_charge"] = $total_delivery_charge;            
            //calculate price
            //$price = $this->calculate_price($post); 
	    $price = $cart["total_amount"] + $total_delivery_charge;
	    $price = number_format($price, 2);

            //order no 
            $order_no = rand(100, 999).date('ymdHi');
	    $track_no = rand(1000, 9999);
	    
            $where11 = "DATE(order_date) = CURDATE()";

            //check exists
            $exist = $this->db->select('*')
                        ->where('user_id', $post["user_id"])
                        ->where('shipping_id', $post["shipping_id"])
                        ->where('payment_done', 0)
                        ->where($where11)
                        ->get('orders')->row_array();

            if(empty($exist)){         
                //add new order
                $insert_array = array(
                    'user_id' => $post["user_id"],
                    'order_no' => $order_no,
		    'track_no' => $track_no,
                    'shipping_id' => $post["shipping_id"],
                    'order_date' => date('Y-m-d H:i:s'),
                    'order_status' => 1,
                    'send_as_gift' => $send_as_gift,
                    'gross_amount' => $cart["total_amount"],
                    'delivery_charges' => $total_delivery_charge,
                    'total_qty' => $cart["total_qty"],
                    'net_amount' => $post["net_amount"],
		    'order_type' => $post["order_type"],
                    'is_pick_up' => $is_pick_up,
		    'add_info' => $add_info,
		    'order_done_type' => $post["order_done_type"],
                );             

                $this->db->insert('orders', $insert_array);
                $last_id = $this->db->insert_id();  

                $post["order_id"] = $last_id; 
            }
            else {
                $post["order_id"] = $exist["order_id"]; 
            }
	    
	    //to check if any product is removed
	    $oproduct_exist = $this->db->select("*")
		    ->where('order_id', $post["order_id"])
                    ->get('order_product')->result_array();
	    
	    if(!empty($oproduct_exist)) {
		foreach($oproduct_exist as $pe) {
		    $exist_pd = $this->db->select('*')
                        ->where('cart_id', $cart["cart_id"])
                        ->where('product_id', $pe["product_id"])
                        ->where('volume_id', $pe["volume_id"])
                        ->get('cart_product')->row_array();
		    
		    if(empty($exist_pd)) {
			$deletepro = $this->db
			    ->where('order_id', $post["order_id"])
			    ->where('product_id', $pe["product_id"])
			    ->where('volume_id', $pe["volume_id"])
			    ->delete('order_product');   
		    }
		}
	    }

            //add order product details     
            foreach($product_details as $pd) {
                $exist_pd = $this->db->select('*')
                        ->where('order_id', $post["order_id"])
                        ->where('product_id', $pd["product_id"])
                        ->where('volume_id', $pd["volume_id"])
                        ->get('order_product')->row_array();
                
                if(empty($exist_pd)) {

                    $insert_array1 = array(
                        'order_id' => $post["order_id"],
                        'product_id' => $pd["product_id"],
                        'seller_id' => $pd["seller_id"],
                        'volume_id' => $pd["volume_id"],
                        'price' => $pd["price"],
                        'qty' => $pd["qty"],
                        'net_total' => $pd["total"],
			'delivery_charge' => $pd["delivery_charges"],
                        'allow_split_order' => $pd["allow_split_order"]
                    );

                    $check_points = $this->db->select("product_id, in_loyalty_club")
                                ->where('product_id', $pd["product_id"])
                                ->get('products')->row_array();

                    if($check_points["in_loyalty_club"] == 1){     
                        $loyalty_net_amount += $pd["total"];
                    }

                    $this->db->insert('order_product', $insert_array1);
                }
            }  
	    
	    //repeat order
	    if(isset($post["is_repeat_order"]) && $post["is_repeat_order"] == "1") {
		$this->db->set("is_repeat_order", 1)
                    ->where('order_id', $post["order_id"])
                    ->update("orders");
		
		$get_days = $this->db->select("total_days")
			->where("schedule_order_list_id", $post["repeat_order_on"])
			->where("status", 1)
			->get("schedule_order_list")->row_array();
		
		if(!empty($get_days)) {
		    $to_be_notified_on = date("Y-m-d H:i:s", strtotime('+'.$get_days["total_days"].' days'));
		}
		else {
		    $to_be_notified_on = null;
		}
		
		$insert_array2 = array(
		    "order_id" => $post["order_id"],
		    "user_id" => $post["user_id"],
		    "schedule_order_list_id" => $post["repeat_order_on"],
		    "to_be_notified_on" => $to_be_notified_on,
		);
		$this->db->insert("repeat_orders", $insert_array2);
	    }
	    
            if(!empty($post["time_slot"]) && !empty($post["to_be_delivered_date"])) {
		$slots = explode("-", $post["time_slot"]);
		
		//get sellers
		$sll = array_unique(array_column($product_details, 'seller_id'));
		
		//get trading hours
		$trading_hrs = $this->db->select("*")
			->where("seller_id", $sll[0])
			->where("start_time", trim($slots[0]))
			->where("end_slot", trim($slots[1]))
			->get("trading_hours")->row_array();
		
		$this->db->set("to_be_delivered_date", $post["to_be_delivered_date"])
			->set("to_be_delivered_date_utc", date("Y-m-d", strtotime($post["to_be_delivered_date"])))
			->set("start_slot", $trading_hrs["start_time_utc"])
			->set("end_slot", $trading_hrs["end_time_utc"])
			->where('order_id', $post["order_id"])
			->update("orders");
	    }
            
            //Payment by Wallet
            if((isset($post["wallet"]) && $post["wallet"] > 0) && (isset($post["online"]) && ($post["online"] === 'no' || $post["online"] == '')) && (isset($post["card_id"]) && $post["card_id"] == '')){
                ///echo "7"; exit;
                $wallet = $this->only_wallet_payment($post, $cart, $product_details);
                //print_r($wallet);
                $this->order_mail($post);
                if($wallet["msg"] == 8) {   
                    $this->add_web_notification($post);
		    $arr[0] = 8;
		    $arr[1] = $order_no;
                    return $arr;
                }
                else if($wallet["msg"] == 9) { 
                    return 9;
                }
            }  
            
            //Payment by wallet with gift card
            else if((isset($post["wallet"]) && $post["wallet"] > 0) && (isset($post["card_id"]) && $post["card_id"] != NULL) && (isset($post["online"]) && ($post["online"] === 'no' || $post["online"] == ''))){
                //echo "8"; exit;
                $wallet = $this->apply_wallet($post);
                //print_r($wallet);
                $post["net_amount"] = number_format($wallet["net_amount"],4);
                $post["amount_paid"] = $wallet["amount_paid"];
                //print_r($post);
                if($wallet["msg"] == 8) { 
                    $returnval = $this->use_gift_card($post, $cart, $product_details);

                    if($returnval == 8) {                        
                        $val = $this->complete_wallet_payment($post);
                        $this->order_mail($post);
                        if($val == 8) { 
			    $arr[0] = $returnval;
			    $arr[1] = $order_no;
			    return $arr;
                        }
                    } else {
                        return $returnval;
                    }
                }
                else { 
                    return 9;
                }
            }
            
            //Payment by wallet with payment gateway
            else if((isset($post["wallet"]) && $post["wallet"] > 0) && (isset($post["online"]) && $post["online"] === 'yes') && (isset($post["card_id"]) && $post["card_id"] == '')){
                //echo "9"; exit;
                $wallet = $this->apply_wallet($post);
                $post["net_amount"] = number_format($wallet["net_amount"],4);
                $post["amount_paid"] = $wallet["amount_paid"];

                if($wallet["msg"] == 8) { 
                    return $post;
                }
                else if($wallet["msg"] == 9) { 
                    return 9;
                };
            }  
            
	    //Payment by wallet with payment gateway and giftcard
            else if((isset($post["wallet"]) && $post["wallet"] > 0) && (isset($post["card_id"]) && $post["card_id"] != NULL) && isset($post["online"]) && $post["online"] === 'yes'){
                //echo "10"; exit;                    
                $wallet = $this->apply_wallet($post);
                $post["net_amount"] = number_format($wallet["net_amount"],4);
                $post["amount_paid"] = $wallet["amount_paid"];

                if($wallet["msg"] == 8) { 
                    $post["loyalty_net_amount"] = $post["net_amount"]; 

                    $returnval = $this->use_gift_card_with_gateway($post);
                    if($returnval == 8) {

                        $orders = $this->db->select('net_amount, amount_paid, order_id')->where('order_id', $post["order_id"])->get('orders')->row_array();
                        $post["net_amount"] = $orders["net_amount"];  
                        $post["amount_paid"] = $orders["amount_paid"];
                        return $post;
                    } else {
                        return $returnval;
                    }
                }
                else if($wallet["msg"] == 9) { 
                    return 9;
                };
            }
            
	    //Payment by Loyalty point with gift card
            else if((isset($post["points"]) && $post["points"] > 0) && (isset($post["card_id"]) && $post["card_id"] != NULL) && (isset($post["online"]) && ($post["online"] === 'no' || $post["online"] == ''))){
                //echo "1"; exit;
                $net_amount = $post["net_amount"] - $loyalty_net_amount;

                $loyalty_arr = $this->calculate_loyalty_point($post["user_id"], $post["points"], $loyalty_net_amount);
                //print_r($loyalty_arr);
                $post["net_amount"] = $net_amount + $loyalty_arr["net_amount"];

                $returnval = $this->use_gift_card($post, $cart, $product_details);

                if($returnval == 8) {

                    $loyalty_point = $this->db->select('user_id, loyalty_point')
                        ->where('user_id', $post["user_id"])
                        ->get('user')->row_array();

                    //print_r($loyalty_point); 

                    //left  point
                    $points = $loyalty_point["loyalty_point"] - $loyalty_arr["used_points"];
                    //echo $points.'<br>';
                    $this->db
                        ->set('loyalty_point', $points)
                        ->where('user_id', $post["user_id"])
                        ->update('user');

                    $this->db
                        ->set('loyalty_point', $loyalty_arr["used_points"])
                        ->where('order_id', $post["order_id"])
                        ->update('orders');

                    //send mail
                    $this->order_mail($post);

                    //print_r($returnval); exit;
                    $arr[0] = $returnval;
		    $arr[1] = $order_no;
		    return $arr;
                } else {
                    return $returnval;
                }
            }
            
	    //Payment by Loyalty point with payment gateway
            else if((isset($post["points"]) && $post["points"] > 0) && (isset($post["online"]) && $post["online"] === 'yes') && (isset($post["card_id"]) && $post["card_id"] == '')){
                //echo "2"; exit;

                $net_amount = $post["net_amount"] - $loyalty_net_amount;                    
                $loyalty_arr = $this->calculate_loyalty_point($post["user_id"], $post["points"], $loyalty_net_amount);
                //print_r($loyalty_arr);
                $post["net_amount"] = $net_amount + $loyalty_arr["net_amount"];
                $post["net_amount"] = number_format($post["net_amount"],4);
                $this->db
                    ->set('loyalty_point', $loyalty_arr["used_points"])
                    ->set('net_amount', $post["net_amount"])
                    ->where('order_id', $post["order_id"])
                    ->update('orders');

                return $post;
            }                
            
	    //Payment by Loyalty point with payment gateway and giftcard
            else if((isset($post["points"]) && $post["points"] > 0) && (isset($post["card_id"]) && $post["card_id"] != NULL) && isset($post["online"]) && $post["online"] === 'yes'){
                //echo "3"; exit;                    
                $net_amount = $post["net_amount"] - $loyalty_net_amount;                    
                $loyalty_arr = $this->calculate_loyalty_point($post["user_id"], $post["points"], $loyalty_net_amount);
                $post["net_amount"] = $net_amount + $loyalty_arr["net_amount"];
                $post["net_amount"] = number_format($post["net_amount"],4);
                $post["loyalty_net_amount"] = $post["net_amount"]; 

                $returnval = $this->use_gift_card_with_gateway($post);
                if($returnval == 8) {
                    return $post;
                } else {
                    return $returnval;
                }
            }
            
	    //Payment by both
            else if((isset($post["card_id"]) && $post["card_id"] != NULL) && isset($post["online"]) && $post["online"] === 'yes'){
                //echo "4"; exit;                    
                $post["loyalty_net_amount"] = $post["net_amount"];                      
                $returnval = $this->use_gift_card_with_gateway($post);
                if($returnval == 8) {
                    return $post;
                } else {
                    return $returnval;
                }
            }
            
	    //Payment by gift card
            else if((isset($post["card_id"]) && $post["card_id"] != NULL)){                    
                //echo "5"; exit;
                $returnval = $this->use_gift_card($post, $cart, $product_details);
                //send mail
                $this->order_mail($post);
                return $returnval;
            }                 
            
	    //Payment by payment gateway
            else if(isset($post["online"]) && $post["online"] === 'yes'){  
                //echo "6"; exit;
                return $post;                
            }
            else {
                return 10;
            }
                
        }
        else{
            return 5;
        }
    }
    
    function only_wallet_payment($post=[], $cart=[], $product_details=[]) {
        //print_r($post); exit;
        ////get userdata
        $userdata = $this->m_tools->get_user_by_id($post["user_id"]);
        $transaction_id = date('YmdHis').$post["order_id"].$post["user_id"];
        $payment_status = 'SUCCESS';
        //print_r($userdata); exit;
        if ($userdata["wallet"] == 0){
            $wallet_arr["msg"] = 9;
            return $wallet_arr;
        }
                
        if($post["net_amount"] <= $userdata["wallet"]){
            $balance = $userdata["wallet"] - $post["net_amount"]; 
            
            $payment_history = '{"status":"true", "payment":"success", "amount":"'.$post["net_amount"].'"}';
            
            $wallet_arr = array(
                'user_id' => $post["user_id"],
                'order_id' => $post["order_id"],
                'type' => 1,
                'debit_credit_amount' => $post["net_amount"],
                'balance_amount' => $balance,
                'payment_status' => $payment_status,
                'payment_history' => $payment_history,
                'transaction_id' => $transaction_id,
            );            
            $this->db->insert('wallet_history', $wallet_arr);
            //update in user
            $this->db->set('wallet', $balance)->where('user_id', $post["user_id"])->update('user');
            
            foreach($product_details as $pd) {
                $exist_pd = $this->db->select('*')
                        ->where('order_id', $post["order_id"])
                        ->where('product_id', $pd["product_id"])
                        ->where('volume_id', $pd["volume_id"])
                        ->get('order_product')->row_array();

                if(!empty($exist_pd)){ 

                    //get product details
                    $prd_qty = $this->db->select('units')
                            ->where('product_id', $pd["product_id"])
                            ->where('volume_id', $pd["volume_id"])
                            ->get('product_details')->row_array();

                    $new_qty = $prd_qty["units"] - $pd["qty"];  

                    //update quantity in product details               
                    $this->db->set('units', $new_qty)
                            ->where('product_id', $pd["product_id"])
                            ->where('volume_id', $pd["volume_id"])
                            ->update('product_details');
                }                
            }   

            //delete from cart
            $this->db->where('cart_id', $cart["cart_id"])->delete('cart');
            //delete from cart product
            $this->db->where('cart_id', $cart["cart_id"])->delete('cart_product');  

            //add loyalty points
            $this->add_loyalty_point($post["user_id"], $post["net_amount"]);
            
            if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){

                //Update Promocode History
                $ins_arr = array(
                    'user_id' => $post["user_id"],
                    'promocode_id' => $post["promocode_id"],
                );

                $this->db->insert('promocode_history', $ins_arr);

                $this->db->set('promocode_id', $post["promocode_id"])
                    ->where('order_id', $post["order_id"])
                    ->update('orders');
            }
                        
            //update in order
            $set = array(
                'updated_date' => date('Y-m-d H:i:s'),
                'order_payment_type' => 4,
                'payment_done' => 1,
                'wallet_amount' => $post["net_amount"],
                'net_amount' => $post["net_amount"],
            );
            
            $this->db->set($set)->where('order_id', $post["order_id"])->update('orders');
            
            //update order transaction
            $update_array_tran = array(
                'transaction_id' => $transaction_id,
                'payment_status' => $payment_status,
                'payment_history' => $payment_history,
                'payment_mode' => 4,
                'order_id' => $post["order_id"],
            );                   

            $this->db
                ->insert('order_transaction', $update_array_tran);            
            
            $wallet_arr["msg"] = 8;
            return $wallet_arr;
            
        } 
    }
    
    function add_loyalty_point($user_id, $price='') { 
        
        //get loyalty points
        $loyalty_points = $this->db->select("loyalty_point")
                ->where('user_id', $user_id)
                ->get('user')->row_array();
        
        //add loyalty points            
        $points = round($price) * 10;  
        
        $final_points = $loyalty_points["loyalty_point"] + $points;
        
        /*echo $points."<br>";
        echo $final_points."<br>";*/
        //Check Eligibility to be a member in VIP Club
        $vip_points = $this->db->where('key', 'vip_loyalty_points')->get('setting')->row_array();
        
        //check loyalty points is eligible
        if($final_points >= $vip_points["value"]){
            $is_vip_club_member = 1;
        } else {
            $is_vip_club_member = 0;
        }        
        //echo $final_points.'<br>';
        $this->db
            ->set('loyalty_point', $final_points)
            ->set('is_vip_club_member', $is_vip_club_member)
            ->where('user_id', $user_id)
            ->update('user');

        return true;
    }  
    
    //Cart
    function cart_product_checkout_list($post=[]) { 
        
        //get cart id
        $cart = $this->get_cart($post["user_id"]);
        
        if(!empty($cart)){        
            //Check Exists
            $get_cart_list = $this->db->select('cart_product.*, products.seller_id, products.product_name, products.feature_img, brand_mst.brand_name', false)
                    ->join('products', 'products.product_id = cart_product.product_id')
                    ->join('brand_mst', 'products.brand_id = brand_mst.brand_id', 'left')
                    ->where('cart_product.cart_id', $cart["cart_id"])
                    ->get('cart_product')->result_array(); 
	    
	    $userdata = $this->m_tools->get_user_by_id($post["user_id"]);
	    if(empty($userdata["latitude"]) || empty($userdata["longitude"])) {
		$shipping = $this->m_shipping->get_shipping_by_id($post["user_id"], $userdata["shipping_id"]);
		if(!empty($shipping)) {
		    $latitude = $shipping["latitude"];
		    $longitude = $shipping["longitude"];
		}
		else {
		    $latitude = "";
		    $longitude = "";
		}
	    }
	    else {
		$latitude = $userdata["latitude"];
		$longitude = $userdata["longitude"];
	    }
	    
            foreach($get_cart_list as $key => $value){
                
                if($value["seller_id"] > 0) {
                    if(!empty($latitude) || !empty($longitude)) {
                        //seller info
                        $seller = $this->db->select("seller_id, latitude, longitude, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender, get_distance_metres(latitude, longitude, ".$latitude." , ".$longitude.") as distance", false)
                                ->where('seller_id', $value['seller_id'])
                                ->where('status', 1)
                                ->where('is_admin_verified', 1)
                                ->get('seller')->row_array();
			
			$distance_in_miles = round(($seller["distance"] / 1609.34), 1);
                        
                        //get data for delivery charge calculation
                        $delivery_charge_details = $this->db->select("*")
				->where("FORMAT(miles, 1) = ".number_format($distance_in_miles, 1, '.', '')."")
				->get("delivery_charges")->row_array();
                        //print_r($delivery_charge_details); exit;
                        
                        //calculate details
                        if(!empty($delivery_charge_details)){
                            //$delivery_charge = $delivery_charge_details["base_rate"] + $delivery_charge_details["pay_driver_pickup"] + $delivery_charge_details["pay_driver_dropoff"];
                            $get_cart_list[$key]['delivery_charges'] = number_format($delivery_charge_details["base_rate"], 2);
                        }
                        else {
			    $settt = $this->get_delivery_charge();
                            $get_cart_list[$key]['delivery_charges'] = $settt["value"];
                        }
                    }
                    else {
                        //seller info
                        $seller = $this->db->select("seller_id, latitude, longitude, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender", false)
                                ->where('seller_id', $value['seller_id'])
                                ->where('status', 1)
                                ->where('is_admin_verified', 1)
                                ->get('seller')->row_array();
                        
                        $settt = $this->get_delivery_charge();
                        $get_cart_list[$key]['delivery_charges'] = $settt["value"];
                    }

                    $seller2 = array_map(function($val) {
                        if(is_null($val)) {
                            $val = "";
                        }
                        return $val;
                    }, $seller);
		    
		    //get open-close slot
		    $weekday = date('w', strtotime(date('Y-m-d'))); 
		    $seller_available = $this->db->select("*")
			    ->where("weekday", $weekday)
			    ->where("status", 1)
			    ->where("seller_id", $value['seller_id'])
			    ->get("trading_hours")->row_array();
		    
		    if(!empty($seller_available)) {
			$seller2["start_time"] = $seller_available["start_time"];
			$seller2["end_time"] = $seller_available["end_time"];

			if(strtotime($seller_available["end_time"]) > strtotime(date('H:i'))) {
			    $seller2["is_open"] = 1;
			}
			else {
			    $seller2["is_open"] = 0;
			}
		    }
		    else {
			$seller2["is_open"] = 0;
		    }

                    $get_cart_list[$key]['seller'] = $seller2;
                }
                else {
                    $get_cart_list[$key]['seller'] = array(
                        'seller_name' => "Admin"
                    );
		    
                    $settt = $this->get_delivery_charge();
                    $get_cart_list[$key]['delivery_charges'] = $settt["value"];
                }
                
                if($value['feature_img']){                
                    $get_cart_list[$key]['feature_img'] = $this->m_tools->image_url( $value['feature_img'],'', 'product');   
                    $get_cart_list[$key]['feature_img_thumb'] = $this->m_tools->image_url( $value['feature_img'],'thumb', 'product');     
                }
                else {
                    $get_cart_list[$key]['feature_img'] = '';  
                    $get_cart_list[$key]['feature_img_thumb'] = '';  
                }            
            }

            if(!empty($get_cart_list)) {
                return $get_cart_list;
            }
            else {
                return false;
            }   
        }
    }
    
    function checkout() { 
        $user_id = $this->session->userdata('user_id');
        $post["user_id"] = $user_id;
        $seller_add = array();
	$checkout = array();
        $total_delivery_charge = 0;
        $checkout["in_loyalty_club"] = false;
        $checkout["in_vip_club"] = false;
        //get product details
        $product_details = $this->cart_product_checkout_list($post); 
        $userdata = $this->m_tools->get_user_by_id($post["user_id"]);

        if(!empty($product_details)){
           // print_r($product_details); exit;
            foreach($product_details as $key => $pd){
                //check quantity
                $check_qty = $this->check_quantity($pd["product_id"], $pd["volume_id"], $pd["qty"]);
                
                if(!$check_qty){
                    return 1;
                } 
                
                $inloyalty = $this->check_in_loyalty_club($pd["product_id"]);
                $invip = $this->check_in_vip_club($pd["product_id"]); 
                
                if($inloyalty == true) {                   
                    if($pd["from_where"] == 2){
                        $checkout["in_loyalty_club"] = true;
                    }                
                }
                
                if($invip == true) { 
                    if($pd["from_where"] == 3){
                        $checkout["in_vip_club"] = true;
                    } 
                }
                
                //$total_delivery_charge = $total_delivery_charge + $pd["delivery_charges"];
		if(!in_array($pd["seller_id"],$seller_add)) {                
		    $total_delivery_charge = $total_delivery_charge + $pd["delivery_charges"];
		    array_push($seller_add, $pd["seller_id"]);
		}
            }            
            //get shipping details
            $post["shipping_id"] = $userdata["shipping_id"];            
            $shipping_details = $this->m_shipping->get_shipping_by_id($post["user_id"], $userdata["shipping_id"]); 
            $checkout["shipping"] = $shipping_details;            

            //get cart total amount
            $cart = $this->get_cart($post["user_id"]);
            //get payable amount
            $post["total_amount"] = $cart["total_amount"];
            $post["delivery_charge"] = $total_delivery_charge;
	    $amount_payable = $cart["total_amount"] + $total_delivery_charge;
            
            //get seller slots
            //print_r($product_details); exit;
            $sellers = implode(",", array_column($product_details, "seller_id"));
            $seller_slots = $this->get_seller_slot($sellers);
            $checkout["seller_slots"] = $seller_slots;
            //print_r($seller_slots); exit;
            
            //product details
            $checkout["currency"] = CURRENCY_CODE;
            $checkout["products"] = $product_details;
            $checkout["loyalty_point"] = $userdata["loyalty_point"];
            $checkout["wallet"] = number_format($userdata["wallet"], 2);
            $checkout["total_amount"] = number_format($cart["total_amount"], 2);
            $checkout["delivery_charge"] = number_format($total_delivery_charge, 2);
            $checkout["amount_payable"] = number_format($amount_payable, 2);

            if(!empty($checkout)) {
                return $checkout;
            }
            else {
                return false;
            }  
        }
        else {
            return false;
        }
    }
    
    function calculate_price($post=[]) {      
        
        $final_price = array();
        
        $delivery_charge = 0; $amount_payable = 0;
        
        //Check for delivery charges   
        $config_data = $this->db->where_in('key', array('amount_for_free_delivery', 'max_discount', 'delivery_charges'))->get('setting')->result_array();
        
        foreach ($config_data as $key => $row) {
            $tmp_arr[$row['key']] = $row['value'];
        }
        
        if($post["total_amount"] > $tmp_arr["amount_for_free_delivery"]){
            $delivery_charge = 0;
        } else {
            $delivery_charge = $tmp_arr["delivery_charges"];
        }
        
        if(!empty($post["delivery_charge"])){
            $amount_payable = $post["total_amount"] + $post["delivery_charge"];
            $final_price["delivery_charge"] = $post["delivery_charge"];
        }
        else {
            $amount_payable = $post["total_amount"];
            $final_price["delivery_charge"] = 0;
        }
        $final_price["amount_payable"] = number_format($amount_payable, 2);
        
        return $final_price;
    }
    
    function check_zone($seller_id, $user_latitude, $user_longitude) {
	//get seller
	$get_seller = $this->db->select('seller_id, latitude, longitude, get_distance_metres(latitude, longitude, '.$user_latitude.', '.$user_longitude.') as distance', false)
                        ->where('seller_id', $seller_id)
                        ->get('seller')->row_array(); 
	
	if(!empty($get_seller)) {
	    $distance = round(($get_seller["distance"] / 1609.34), 2);
                    
    	    $mile_limit = $this->m_tools->get_mile_limit();
	    //user within miles
	    if($distance <= $mile_limit && $distance >= 0){
		return 1;
	    }
	    else {
		return 2;
	    }
	}
    }
    
    function add_to_bag($post=[]){  
	//print_r($post); exit;
	
        $user_id = $this->session->userdata('user_id');
        $post["user_id"] = $user_id;
        $post["from_where"] = 1;
        
        $userdata = $this->m_tools->get_user_by_id($post["user_id"]);
        $seller_array = array();
                    
        if($userdata["is_admin_verified"] == 1) {
	    	    
	    //get seller
	    $get_seller = $this->db->select('seller_id', false)
		->where('product_id', $post["product_id"])
		->get('products')->row_array();         
		    
	    //check wheather product falls in zone?
	    $check_zone = $this->check_zone($get_seller["seller_id"], $userdata["latitude"], $userdata["longitude"]);	    
	    
	    if($check_zone == 1) {   
        
		$cart_id = $this->check_cart_exists($post["user_id"]);

		//Check Exists
		$exists = $this->db->select('*', false)
		    ->where('product_id', $post["product_id"])
		    ->where('volume_id', $post["volume_id"])
		    ->where('cart_id', $cart_id)
		    ->get('cart_product')->row_array();

		 // print_r($exists);

		if(empty($exists)) { 
		    $check_qty = $this->check_quantity($post["product_id"], $post["volume_id"], $post["qty"]);

		    if($check_qty){

			//get price from volume_mst
			$get_price = $this->db->select('*', false)
			    ->where('volume_id', $post["volume_id"])
			    ->where('product_id', $post["product_id"])
			    ->get('product_details')->row_array(); 

			$inloyalty = $this->check_in_loyalty_club($post["product_id"]);
			$invip = $this->check_in_vip_club($post["product_id"]); 

			if($inloyalty == true) {                          
			    if($post["from_where"] == 1){
				//calculate total
				$total = $post["qty"] * $get_price["normal_sell_price"];

				//Insert into cart product
				$insert_cart_product = array(
				    'cart_id' => $cart_id,
				    'product_id' => $post["product_id"],
				    'seller_id' => $get_seller["seller_id"],
				    'volume_id' => $post["volume_id"],
				    'price' => $get_price["normal_sell_price"],
				    'qty' => $post["qty"],
				    'total' => $total,
				    'from_where' => $post["from_where"],
				    'allow_split_order' => 0,
				);
			    }
			    else {
				//calculate total
				$total = $post["qty"] * $get_price["loyalty_club_sell_price"];

				//Insert into cart product
				$insert_cart_product = array(
				    'cart_id' => $cart_id,
				    'product_id' => $post["product_id"],
				    'seller_id' => $get_seller["seller_id"],
				    'volume_id' => $post["volume_id"],
				    'price' => $get_price["loyalty_club_sell_price"],
				    'qty' => $post["qty"],
				    'total' => $total,
				    'from_where' => $post["from_where"],
				    'allow_split_order' => 0,
				);
			    }
			}
			else if($invip == true) {   
			    if($post["from_where"] == 1){
				//calculate total
				$total = $post["qty"] * $get_price["normal_sell_price"];

				//Insert into cart product
				$insert_cart_product = array(
				    'cart_id' => $cart_id,
				    'product_id' => $post["product_id"],
				    'seller_id' => $get_seller["seller_id"],
				    'volume_id' => $post["volume_id"],
				    'price' => $get_price["normal_sell_price"],
				    'qty' => $post["qty"],
				    'total' => $total,
				    'from_where' => $post["from_where"],
				    'allow_split_order' => 0,
				);
			    }
			    else {
				//calculate total
				$total = $post["qty"] * $get_price["vip_club_sell_price"];

				//Insert into cart product
				$insert_cart_product = array(
				    'cart_id' => $cart_id,
				    'product_id' => $post["product_id"],
				    'seller_id' => $get_seller["seller_id"],
				    'volume_id' => $post["volume_id"],
				    'price' => $get_price["vip_club_sell_price"],
				    'qty' => $post["qty"],
				    'total' => $total,
				    'from_where' => $post["from_where"],
				    'allow_split_order' => 0,
				);
			    }
			}
			else if($get_price["normal_discount"] != 0 && $get_price["normal_sell_price"] != 0) {  
			    //calculate total
			    $total = $post["qty"] * $get_price["normal_sell_price"];

			    //Insert into cart product
			    $insert_cart_product = array(
				'cart_id' => $cart_id,
				'product_id' => $post["product_id"],
				'seller_id' => $get_seller["seller_id"],
				'volume_id' => $post["volume_id"],
				'price' => $get_price["normal_sell_price"],
				'qty' => $post["qty"],
				'total' => $total,
				'from_where' => $post["from_where"],
				'allow_split_order' => 0,
			    );
			}
			else {  
			    //calculate total
			    $total = $post["qty"] * $get_price["normal_sell_price"];

			    //Insert into cart product
			    $insert_cart_product = array(
				'cart_id' => $cart_id,
				'product_id' => $post["product_id"],
				'seller_id' => $get_seller["seller_id"],
				'volume_id' => $post["volume_id"],
				'price' => $get_price["normal_sell_price"],
				'qty' => $post["qty"],
				'total' => $total,
				'from_where' => $post["from_where"],
				'allow_split_order' => 0,
			    );
			}

			$insert_cp = $this->db->insert('cart_product', $insert_cart_product);
			$last_id = $this->db->insert_id();

			$exists2 = $this->db->select('seller_id', false)
			    ->where('cart_id', $cart_id)
			    ->get('cart_product')->result_array();

			$seller_array = array_column($exists2, "seller_id");
			$count = array_count_values($seller_array);
			//print_r($count);
			//echo count($exists2);
			if(count($exists2) > 1){

			    foreach($exists2 as $k => $v){   

				if(!empty($exists2) && $count[$v["seller_id"]] == 1){ 
				    $allow_split_order = 1;
				}
				else if(!empty($exists2) && $count[$v["seller_id"]] > 1){ 
				    $allow_split_order = 0;
				}   
				$this->db->set("allow_split_order", $allow_split_order)->where("cart_id", $cart_id)->update('cart_product');
			    }
			}

			$this->calculate_cart_total($cart_id, $post["user_id"]);
			if($insert_cp){
			    return 3;
			} 
			else {
			    return 4;
			}            
		    } 
		    else {
			return 2;
		    }
		} 
		else {
		    return 1;
		}
	    } 
	    else if($check_zone == 2) {
		return 6;
	    }
        }
        else {
            return 5;
        }
    }
    
    function remove_product($post=[]) {  
        
        //get cart id
        $cart = $this->get_cart($post["user_id"]);
        
        if(!empty($cart)){
        
            //Check Exists
            $exists = $this->db->select('*', false)
                    ->where('product_id', $post["product_id"])
                    ->where('cart_id', $cart["cart_id"])
                    ->where('volume_id', $post["volume_id"])
                    ->get('cart_product')->row_array();

            if(!empty($exists))
            {
                $this->calculate_sub_cart_total($cart["cart_id"], $post["product_id"], $post["volume_id"]);

                $deletepro = $this->db
                    ->where('product_id', $post["product_id"])
                    ->where('cart_id', $cart["cart_id"])
                    ->where('volume_id', $post["volume_id"])
                    ->delete('cart_product');            

                if($deletepro) {
                    return true;
                }
                else {
                    return false;
                } 
            }    
        }
    }
    
    function cart_product_list($post=[]) { 
        
        //get cart id
        $cart = $this->get_cart($post["user_id"]);
        
        if(!empty($cart)){        
            //Check Exists
            $get_cart_list = $this->db->select('cart_product.*, products.seller_id, products.product_name, products.feature_img, brand_mst.brand_name', false)
                    ->join('products', 'products.product_id = cart_product.product_id')
                    ->join('brand_mst', 'products.brand_id = brand_mst.brand_id', 'left')
                    ->where('cart_product.cart_id', $cart["cart_id"])
                    ->get('cart_product')->result_array();    

            foreach($get_cart_list as $key => $value){
                $post_arr = array('product_id' => $value["product_id"]);
                
                if($value["seller_id"] != 0) {
                    if(!empty($post["latitude"]) || !empty($post["longitude"])) {
                        //seller info
                        $seller = $this->db->select("seller_id, latitude, longitude, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender, get_distance_metres(latitude, longitude, ".$post['latitude']." , ".$post["longitude"].") as distance", false)
                                ->where('seller_id', $value['seller_id'])
                                ->where('status', 1)
                                ->where('is_admin_verified', 1)
                                ->get('seller')->row_array();
			
			$distance_in_miles = round(($seller["distance"] / 1609.34), 1);
                        
                        //get data for delivery charge calculation
                        $delivery_charge_details = $this->db->select("*")
				//->where("miles", number_format($distance_in_miles, 1, '.', ''))
				->where("FORMAT(miles, 1) = ".number_format($distance_in_miles, 1, '.', '')."")
				->get("delivery_charges")->row_array();
                        
                        //calculate details
                        if(!empty($delivery_charge_details)) {
                            //$delivery_charge = $delivery_charge_details["base_rate"] + $delivery_charge_details["pay_driver_pickup"] + $delivery_charge_details["pay_driver_dropoff"];
                            $get_cart_list[$key]['delivery_charges'] = number_format($delivery_charge_details["base_rate"], 2);
                        }
                        else {
                            $delivery_chg = $this->get_delivery_charge();
                            $get_cart_list[$key]['delivery_charges'] = $delivery_chg["value"];
                        }
                    }
                    else {
                        //seller info
                        $seller = $this->db->select("seller_id, latitude, longitude, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender", false)
                                ->where('seller_id', $value['seller_id'])
                                ->where('status', 1)
                                ->where('is_admin_verified', 1)
                                ->get('seller')->row_array();
                        
                        $delivery_chg = $this->get_delivery_charge();
                        $get_cart_list[$key]['delivery_charges'] = $delivery_chg["value"];
                    }
                    
                    $seller2 = array_map(function($val) {
                        if(is_null($val)) {
                            $val = "";
                        }
                        return $val;
                    }, $seller);

                    $get_cart_list[$key]['seller'] = $seller2;
                }
                else {
                    $get_cart_list[$key]['seller'] = array(
                        'seller_name' => "Admin"
                    );
                    
		    $delivery_chg = $this->get_delivery_charge();                        
                    $get_cart_list[$key]['delivery_charges'] = $delivery_chg["value"];
                }
                
                //get favourite
                $favourite = $this->db->select("user_id, product_id, status")
                        ->where('product_id', $value['product_id'])
                        ->where('user_id', $post['user_id'])
                        ->get('product_favourite')->row_array();
                
                
                if(!empty($favourite)){
                    $get_cart_list[$key]['is_favourite'] = $favourite["status"];
                }
                else {
                    $get_cart_list[$key]['is_favourite'] = 0;
                }
                
                
                //get current volume
                $volume = $this->m_tools->get_volume_by_id($value["volume_id"]);
                $get_cart_list[$key]['volume'] = $volume["volume"];
                //$get_cart_list[$key]['final_price'] = $value["price"] * $value["qty"];
                
                //get volume list by product
                //$get_cart_list[$key]['volume_list'] = $this->get_volume_list_by_product($post_arr);

                if($value['feature_img']){                
                    $get_cart_list[$key]['feature_img'] = $this->m_tools->image_url( $value['feature_img'],'', 'product');   
                    $get_cart_list[$key]['feature_img_thumb'] = $this->m_tools->image_url( $value['feature_img'],'thumb', 'product');     
                }
                else {
                    $get_cart_list[$key]['feature_img'] = '';  
                    $get_cart_list[$key]['feature_img_thumb'] = '';  
                }            
            }

            if(!empty($get_cart_list)) {
                return $get_cart_list;
            }
            else {
                return false;
            }   
        }
    }
    
    function update_bag($post=[]){         
        
        $cart = $this->get_cart($post["user_id"]);
        
        //Check Exists
        $exists = $this->db->select('*', false)
            ->where('product_id', $post["product_id"])
            ->where('volume_id', $post["volume_id"])
            ->where('cart_id', $cart["cart_id"])
            ->get('cart_product')->row_array();
        
        if(!empty($exists)){  
            //check quantity
            $check_qty = $this->check_quantity($post["product_id"], $post["volume_id"], $post["qty"]);
            
            if($check_qty){  
                $new_post = array(
                    'product_id' => $post["product_id"],
                    'cart_id' => $cart["cart_id"],
                    'volume_id' => $post["volume_id"],
                    'qty' => $post["qty"]
                );
                //print_r($new_post); exit;
                $update_qty = $this->update_product_quantity($new_post);

                $this->calculate_cart_total($cart["cart_id"], $post["user_id"]);

                if($update_qty){
                    return 1;
                } else {
                    return 4;
                }
            } else {
                return 2;
            }
        }
        else {
            return 3;
        }
    }
    
    function update_product_quantity($post=[]) { 
        
        /*$check_qty = $this->check_quantity($post["product_id"], $post["volume_id"], $post["qty"]);
        
        if($check_qty){*/
            //get current qty of product
            $new_qty = 0;
            $get_qty = $this->db->select("price, qty")
                ->where('product_id', $post["product_id"])
                ->where('cart_id', $post["cart_id"])
                ->where('volume_id', $post["volume_id"])
                ->get('cart_product')->row_array(); 

            $new_qty = $post["qty"];
            $total = $get_qty["price"] * $new_qty;

            $updateqty = $this->db
                ->set('qty', $new_qty)
                ->set('total', $total)
                ->where('product_id', $post["product_id"])
                ->where('cart_id', $post["cart_id"])
                ->where('volume_id', $post["volume_id"])
                ->update('cart_product');

            if($updateqty) {
                return true;
            }
            else {
                return false;
            } 
        /*} 
        else {
            return false;
        }*/
           
    }
    
    function calculate_cart_total($cart_id, $user_id){
        
        $total_price = 0; $total_qty = 0; $total_amount = 0;
        //get cart details
        $get_cart_details = $this->db->select("*")
                ->where('cart_id', $cart_id)
                ->where('user_id', $user_id)
                ->get('cart')->row_array();
        
        if($get_cart_details){
            //get cart product details
            $get_cart_product_details = $this->db->select("*")
                ->where('cart_id', $cart_id)
                //->where('user_id', $user_id)
                ->get('cart_product')->result_array();
	    
	    $userdata = $this->m_tools->get_user_by_id($user_id);
	    if(empty($userdata["latitude"]) || empty($userdata["longitude"])) {
		$shipping = $this->m_shipping->get_shipping_by_id($user_id, $userdata["shipping_id"]);
		if(!empty($shipping)) {
		    $latitude = $shipping["latitude"];
		    $longitude = $shipping["longitude"];
		}
		else {
		    $latitude = "";
		    $longitude = "";
		}
	    }
	    else {
		$latitude = $userdata["latitude"];
		$longitude = $userdata["longitude"];
	    }
            
            foreach($get_cart_product_details as $key => $value) {
		
		if($value["seller_id"] > 0) {
                    if(!empty($latitude) || !empty($longitude)) {
                        //seller info
                        $seller = $this->db->select("seller_id, latitude, longitude, get_distance_metres(latitude, longitude, ".$latitude." , ".$longitude.") as distance", false)
                                ->where('seller_id', $value['seller_id'])
                                ->where('status', 1)
                                ->where('is_admin_verified', 1)
                                ->get('seller')->row_array();
			$distance_in_miles = round(($seller["distance"] / 1609.34), 1);
                       
                        //get data for delivery charge calculation
                        $delivery_charge_details = $this->db->select("*")
				->where("FORMAT(miles, 1) = ".number_format($distance_in_miles, 1, '.', '')."")
				->get("delivery_charges")->row_array();
			                        
                        //calculate details
                        if(!empty($delivery_charge_details)){
                            //$delivery_charge = $delivery_charge_details["base_rate"] + $delivery_charge_details["pay_driver_pickup"] + $delivery_charge_details["pay_driver_dropoff"];
                            $delivery_charges = number_format($delivery_charge_details["base_rate"], 2);
                        }
                        else {
			    $settt = $this->get_delivery_charge(); 
                            $delivery_charges = $settt["value"];
                        }
                    }
		    else {
			$settt = $this->get_delivery_charge(); 
                        $delivery_charges = $settt["value"];
		    }
		}
                $total_qty = $total_qty + $value["qty"];
                $cal_amount = $value["price"] * $value["qty"];
                $total_amount = $total_amount + $cal_amount;

                $update_arr = array(
                    'total_qty' => $total_qty,
                    'total_amount' => $total_amount,
		    'delivery_charge' => $delivery_charges,
                );

                $this->db
                        ->set($update_arr)
                        ->where('cart_id', $cart_id)
                        ->update('cart');
            }
        }
    }
    
    function check_cart_exists($user_id){
        //Check cart exists
        $cart_exists = $this->db->select('*', false)
                ->where('user_id', $user_id)
                ->get('cart')->row_array();
        
        // print_r($cart_exists);
        
        if(empty($cart_exists))
        {
            // Insert into cart
            $insert_cart = array(
                'user_id' => $user_id,
            );

            $this->db->insert('cart', $insert_cart);
            $cart_id = $this->db->insert_id(); 
            return $cart_id;
        }
        else {
            return $cart_exists["cart_id"];
        }   
    }
    
    function check_quantity($product_id, $volume_id, $qty) {
        $details = $this->db->select("*")
            ->where('product_id', $product_id)
            ->where('volume_id', $volume_id)
            ->get('product_details')->row_array(); 
        
        if(!empty($details)){
            if($qty <= $details["units"]){
                return true;
            }        
            else if($qty > $details["units"]){
                return false;
            }            
        }
    }
    
    function check_in_loyalty_club($product_id) {
        $details = $this->db->select("product_id, in_loyalty_club")
            ->where('product_id', $product_id)
            ->get('products')->row_array(); 
        
        if(!empty($details)){
            if($details["in_loyalty_club"] == 1){
                return true;
            }        
            else if($details["in_loyalty_club"] == 0){
                return false;
            }            
        }
    }
    
    function check_in_vip_club($product_id) {
        $details = $this->db->select("product_id, in_vip_club")
            ->where('product_id', $product_id)
            ->get('products')->row_array(); 
        
        if(!empty($details)){
            if($details["in_vip_club"] == 1){
                return true;
            }        
            else if($details["in_vip_club"] == 0){
                return false;
            }            
        }
    }     
    
    function get_cart($user_id){
        //Check cart exists
        $cart = $this->db->select('*', false)
                ->where('user_id', $user_id)
                ->get('cart')->row_array();
        
        return $cart;
    } 
    
    function calculate_sub_cart_total($cart_id, $product_id, $volume_id){
        
        //get cart details
        $get_cart_details = $this->db->select("*")
                ->where('cart_id', $cart_id)
                ->get('cart')->row_array();
        
        if($get_cart_details){
            //get cart product details
            $get_cart_product_details = $this->db->select("*")
                ->where('cart_id', $cart_id)
                ->where('product_id', $product_id)
                ->where('volume_id', $volume_id)
                ->get('cart_product')->result_array();
            
            $total_qty = $get_cart_details["total_qty"];
            $total_amount = $get_cart_details["total_amount"];
            
            //print_r($get_cart_product_details);
            
            foreach($get_cart_product_details as $key => $value) {
                
                //$total_price = $total_price + $value["price"];
                $total_qty = $total_qty - ($value["qty"]);
                $cal_amount = $value["price"] * $value["qty"];
                $total_amount = $total_amount - $cal_amount;

                $update_arr = array(
                    //'total_price' => $total_price,
                    'total_qty' => $total_qty,
                    'total_amount' => $total_amount,
                );

                $this->db
                        ->set($update_arr)
                        ->where('cart_id', $cart_id)
                        ->update('cart');
            }
        }
    }
    
    function get_seller_slot($seller_ids) {
        $response = $this->db->select("*")
                ->where("seller_id IN (".$seller_ids.")")
                ->where("status", 1)
                ->get("trading_hours")->result_array();	
	
	if(!empty($response)) {
	    
            $date = date('Y-m-d'); //today date
            $final_array = array();
            $weekday_arr = array_column($response, "weekday");
            
            //get key of timeslot according to weekdays            
            for($i = 1; $i <= 7; $i++){
                $weekday = date('w', strtotime("+$i day", strtotime($date)))+1;
		
		if( in_array($weekday, $weekday_arr) ) {
                    $ans = array();
                    $ans["keys"] = array_keys($weekday_arr, $weekday);
                    $ans["day"] = date('l', strtotime("+$i day", strtotime($date)));
                    $ans["date"] = date('Y-m-d', strtotime("+$i day", strtotime($date)));
		    
		    $slotb = array();
		    $slot_arr = array();
                    $start_time = "";
                    $end_time = "";
		    //$duration = '180';  // split by 3 hours
		    $duration = '60';  // split by 1 hours
		    $add_mins  = $duration * 60;
		    
		    //get start and end time of slot
                    foreach($ans["keys"] as $keys => $values) {
			
                        array_push($slotb, $response[$values]);
			$start_time = $response[$values]["start_time"];
			$end_time = $response[$values]["end_time"];
			$seller_iid = $response[$values]["seller_id"];
			
			$array_of_time = array ();
			$starttime    = strtotime ($start_time); //change to strtotime
			$endtime      = strtotime ($end_time); //change to strtotime
			
			while ($starttime < $endtime) // loop between time
			{
			    $stime = date ("H:i", $starttime);
			    $starttime += $add_mins; // to check endtie=me
			    $etime = date ("H:i", $starttime);
			    $array_of_time[] = $stime.' - '.$etime;
			}
			$slot_arr = array_merge($slot_arr, $array_of_time);
                    }
		    
                    $final_array["seller_id"] = $seller_iid;
                    $final_array["day"] = date('l', strtotime("+$i day", strtotime($date)));
                    $final_array["date"] = date('Y-m-d', strtotime("+$i day", strtotime($date)));
                    $final_array["slots"] = array_unique($slot_arr);
		    sort($final_array["slots"]);
                }    
		
                $slota[] = $final_array;
            }
	    
	    return $slota;
        }  
        else {
	    return array();
	}
    }
    
    //Promocode
    function check_promocode($post=[]) {     
        //calculate amount
        $post["amount_payable"] = $post["gross_amount"];	
        $promocode = $this->apply_promocode($post);

        if(!$promocode) {
            $net_amount = $post["gross_amount"];     
            return $net_amount;
        }
        elseif (!empty($promocode) && $promocode === 'A') {
            return 3;
        }
        elseif (!empty($promocode) && $promocode === 'B') {
             return 2; 
        }
        elseif (!empty($promocode) && $promocode === 'C') {
             return 4; 
        }
        else {
           // $net_amount = $promocode;
            return $promocode;
        }
    }
    
    function apply_promocode($post=[]) { 
       // print_r($post); exit;
        $new_cart_total = 0;

        //check promocode
        $where = 'expiry_date >= now() and promocode = "'.$post['promocode'].'"';
        $promocode_details = $this->db->select('*')
                ->where($where)
                ->get('promocodes')->row_array();
        
        if(!empty($promocode_details)){
            
            //check wheather promocode is already used or not
            $promocode_used = $this->db->select('*')
                ->where('user_id', $post["user_id"])
                ->where('promocode_id', $promocode_details["promocode_id"])
                ->get('promocode_history')->row_array();
            
            if(empty($promocode_used)){

                //calculate            
                if($promocode_details["discount_type"] == 1){
                    $promocode_discount = ($post["amount_payable"] * $promocode_details["discount_amount"]) / 100;
                }
                elseif($promocode_details["discount_type"] == 2){
                    $promocode_discount = $promocode_details["discount_amount"];
                }
                
                if($post["amount_payable"] > $promocode_discount) {                    
                    //Update Cart total   
                    $new_cart_total = ($post["amount_payable"] - $promocode_discount);

                    //update order
                    $update_array = array(
                        'promocode_id' => $promocode_details["promocode_id"],
                    );
		    
		    $ret[0] = number_format($new_cart_total,2);
		    $ret[1] = $promocode_details["promocode_id"];

                    return json_encode($ret);
                }
                else {
                    return 'C';
                }
            }
            else {
                return 'B';
            }
        }
        else {
            return 'A';
        }
    }
    
    function order_mail($post = []){
        //print_r($post);
        //send mail
        $admin_data = $this->db->where('key', 'admin_email_address')->get('setting')->row_array();
        $order_details = $this->m_orders->order_details($post);
        $to = $admin_data['value'];
        $subject = 'A New Order Placed';
        $msg = $this->load->view('mail_tmp/header', $admin_data, true);
        $msg .= $this->load->view('mail_tmp/order_details', $order_details, true);
        $msg .= $this->load->view('mail_tmp/footer', $admin_data, true);
        $this->m_tools->send_mail($to, $subject, $msg);
    }
    
    function order_product_list($product_id, $user_id) {         
        
        $get_cart_list = $this->db->select('products.product_name, products.seller_id, products.feature_img, brand_mst.brand_name, products.no_of_return_days', false)                
                ->join('brand_mst', 'products.brand_id = brand_mst.brand_id', 'left')
                ->where('product_id', $product_id)
                ->get('products')->result_array();   

        if(!empty($get_cart_list)) {
            
            foreach($get_cart_list as $key => $value){            
                if($value["seller_id"] != 0) {
                    //seller info
                    $seller = $this->db->select("seller_id, seller_name, company_name, email, contact_no, address, latitude, longitude, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender", false)
                            ->where('seller_id', $value['seller_id'])
                            ->where('status', 1)
                            ->where('is_admin_verified', 1)
                            ->get('seller')->row_array();

                    $seller2 = array_map(function($val) {
                        if(is_null($val)) {
                            $val = "";
                        }
                        return $val;
                    }, $seller);

                    $get_cart_list[$key]['seller'] = $seller2;
                }
                else {
                    $get_cart_list[$key]['seller'] = array(
                        'seller_name' => "Admin"
                    );
                }

                if($value['feature_img']){                
                    $get_cart_list[$key]['feature_img'] = $this->m_tools->image_url( $value['feature_img'],'', 'product');   
                    $get_cart_list[$key]['feature_img_thumb'] = $this->m_tools->image_url( $value['feature_img'],'thumb', 'product');     
                }
                else {
                    $get_cart_list[$key]['feature_img'] = '';  
                    $get_cart_list[$key]['feature_img_thumb'] = '';  
                }    

                 //review tag
                $reviews = $this->db->select("*")
                        ->where('product_id', $product_id)
                        ->where('user_id', $user_id)
                        ->get('product_rating')->row_array();

                if(!empty($reviews)){
                    $get_cart_list[$key]['is_review'] = true;
                }
                else {
                    $get_cart_list[$key]['is_review'] = false; 
                }
            }
        
            return $get_cart_list;
        }
        else {
            return false;
        }  
    }
    
    function apply_gift_card($post=[]) {
        
        //get userdata
        $userdata = $this->m_tools->get_user_by_id($post["user_id"]);

        //check gift card
        $where = 'status = 1 and expiry_date > now() and card_id = "'.$post['card_id'].'" and receiver_email="'.$userdata["email"].'"';
        $gift_card_details = $this->db->select('*')
                ->where($where)
                ->get('gift_card')->row_array();
        
        if(!empty($gift_card_details)){
            
            $where = 'card_id = '.$post['card_id'].' AND balance_amount = 0';
            //check wheather amount left in your gift card or not
            $gift_card_used1 = $this->db->select('*')
                ->where($where)
                ->order_by('date', 'desc')
                ->limit(1)
                ->get('gift_card_history')->row_array();
            
            //print_r($post); 
        
           
            if(empty($gift_card_used1)){
                
                $where1 = 'card_id = '.$post['card_id'].'';
                //check wheather amount left in your gift card or not
                $gift_card_used = $this->db->select('*')
                    ->where($where1)
                    ->order_by('date', 'desc')
                    ->limit(1)
                    ->get('gift_card_history')->row_array();
                                
                if($post["net_amount"] <= $gift_card_used["balance_amount"]){
                    $amount_used = $post['net_amount'];
                    $balance_amount = $gift_card_used["balance_amount"] - $post['net_amount']; 
                }
                else {
                    $amount_used = $gift_card_used["balance_amount"];
                    $left_amount = $post['net_amount'] - $gift_card_used["balance_amount"];                    
                    $post['net_amount'] = $left_amount;                    
                    $balance_amount = $gift_card_used["balance_amount"] - $amount_used;
                }
                
                               
                $transaction_id = date('YmdHis').$post["card_id"].$post["user_id"];
                $payment_history = '{"status":"true", "payment":"success", "amount":"'.$amount_used.'"}';
                $payment_status = 'SUCCESS';
                
                
                //pay from gift card
                //deduct from gift card
                $insert_array = array(
                    'card_id' => $post['card_id'],
                    'user_id' => $post['user_id'],
                    'transaction_id' => $transaction_id,
                    'order_id' => $post['order_id'],
                    'payment_history' => $payment_history,
                    'payment_status' => $payment_status,
                    'used_amount' => $amount_used,
                    'balance_amount' => $balance_amount
                );
                
                $insert = $this->db->insert('gift_card_history', $insert_array);
                
                $insert_array["net_amount"] = $post['net_amount'];
                
                if($insert){
                    return $insert_array;
                }
                else {
                    return false;
                } 
            }
            else {
                return 3;
            }
        }
        else {
            return 4;
        }
    }
    
    function use_gift_card_with_gateway($post = []){
        $gift_card = $this->apply_gift_card_with_payment($post);
                    
        if(!$gift_card){                        
            //remove promocode if not used
            if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){
                $this->db
                    ->where('user_id', $post["user_id"])
                    ->where('promocode_id', $post["promocode_id"])
                    ->delete('promocode_history');  
            }

            return 2; 
        }
        elseif (!empty($gift_card) && $gift_card == 3) {                        
            //remove promocode if not used
            if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){
                $this->db
                    ->where('user_id', $post["user_id"])
                    ->where('promocode_id', $post["promocode_id"])
                    ->delete('promocode_history');  
            }

            return 3; 
        }
        elseif (!empty($gift_card) && $gift_card == 4) {                        
            //remove promocode if not used
            if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){
                $this->db
                    ->where('user_id', $post["user_id"])
                    ->where('promocode_id', $post["promocode_id"])
                    ->delete('promocode_history');  
            }

            return 4; 
        }
        else{
           // print_r($gift_card);
            if($gift_card["payment_status"] === 'SUCCESS'){ 
                
                $post["net_amount"] = $gift_card["net_amount"];

                //unset($post["promocode"]);
                if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){
                    //Update Promocode History
                    $ins_arr = array(
                        'user_id' => $post["user_id"],
                        'promocode_id' => $post["promocode_id"],
                    );

                    $this->db->insert('promocode_history', $ins_arr);

                    $this->db->set('promocode_id', $post["promocode_id"])
                        ->where('order_id', $post["order_id"])
                        ->update('orders');
                }
            }   
            
            if(isset($post["wallet"]) && $post["wallet"] > 0){
                $amount_paid = $post["amount_paid"] + $gift_card["temp_used_amount"];
            }
            else {
                $amount_paid = $gift_card["temp_used_amount"];
            }
            //echo $amount_paid;
            //update order
            $update_array = array(
                'gift_card_id' => $post["card_id"],
                'order_payment_type' => 3,
                'updated_date' => date('Y-m-d H:i:s'),
                'amount_paid' => $amount_paid,
                'net_amount' => $post["net_amount"],
            );

            $this->db->set($update_array)
                    ->where('order_id', $post["order_id"])
                    ->update('orders');

            //update order transaction
            $update_array_tran = array(
                'transaction_id' => $gift_card["transaction_id"],
                'payment_status' => $gift_card["payment_status"],
                'payment_history' => $gift_card["payment_history"],
                'payment_mode' => 3,
                'order_id' => $post["order_id"],
            );                   

            $this->db
                ->insert('order_transaction', $update_array_tran);
            $this->m_tools->add_web_notification($post);
            //$this->notify_driver($post["user_id"], $post["order_id"]);
            return 8;
        }
    }
    
    function use_gift_card($post = [], $cart = [], $product_details = []){
        $gift_card = $this->apply_gift_card($post);

        if(!$gift_card){

            //remove promocode if not used
            if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){
                $this->db
                    ->where('user_id', $post["user_id"])
                    ->where('promocode_id', $post["promocode_id"])
                    ->delete('promocode_history');  
            }

            return 2; 
        }
        elseif (!empty($gift_card) && $gift_card == 3) {

            //remove promocode if not used
            if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){
                $this->db
                    ->where('user_id', $post["user_id"])
                    ->where('promocode_id', $post["promocode_id"])
                    ->delete('promocode_history');  
            }

            return 3; 
        }
        elseif (!empty($gift_card) && $gift_card == 4) {

            //remove promocode if not used
            if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){
                $this->db
                    ->where('user_id', $post["user_id"])
                    ->where('promocode_id', $post["promocode_id"])
                    ->delete('promocode_history');  
            }

            return 4; 
        }
        else{

            if($gift_card["payment_status"] === 'SUCCESS'){                              

                if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){

                    //Update Promocode History
                    $ins_arr = array(
                        'user_id' => $post["user_id"],
                        'promocode_id' => $post["promocode_id"],
                    );

                    $this->db->insert('promocode_history', $ins_arr);

                    $this->db->set('promocode_id', $post["promocode_id"])
                        ->where('order_id', $post["order_id"])
                        ->update('orders');
                }

                foreach($product_details as $pd) {
                    $exist_pd = $this->db->select('*')
                            ->where('order_id', $post["order_id"])
                            ->where('product_id', $pd["product_id"])
                            ->where('volume_id', $pd["volume_id"])
                            ->get('order_product')->row_array();

                    if(!empty($exist_pd)){ 

                        //get product details
                        $prd_qty = $this->db->select('units')
                                ->where('product_id', $pd["product_id"])
                                ->where('volume_id', $pd["volume_id"])
                                ->get('product_details')->row_array();

                        $new_qty = $prd_qty["units"] - $pd["qty"];  

                        //update quantity in product details               
                        $this->db->set('units', $new_qty)
                                ->where('product_id', $pd["product_id"])
                                ->where('volume_id', $pd["volume_id"])
                                ->update('product_details');
                    }                
                }   
                
                //delete from cart
                $this->db->where('cart_id', $cart["cart_id"])->delete('cart');
                //delete from cart product
                $this->db->where('cart_id', $cart["cart_id"])->delete('cart_product');  
                
                
                //add loyalty points
                $this->add_loyalty_point($post["user_id"], $post["net_amount"]);
            }   
            
           // print_r($post);
            if(isset($post["wallet"]) && $post["wallet"] > 0) {
                $net_amount = $post["net_amount"] + $post["amount_paid"];
            }
            else {
                $net_amount = $post["net_amount"];
            }

            //update order
            $update_array = array(
                'gift_card_id' => $post["card_id"],
                'net_amount' => $net_amount,
                'order_payment_type' => 3,
                'updated_date' => date('Y-m-d H:i:s'),
                'payment_done' => 1,
            );

            $this->db->set($update_array)
                    ->where('order_id', $post["order_id"])
                    ->update('orders');

            //update order transaction
            $update_array_tran = array(
                'transaction_id' => $gift_card["transaction_id"],
                'payment_status' => $gift_card["payment_status"],
                'payment_history' => $gift_card["payment_history"],
                'payment_mode' => 3,
                'order_id' => $post["order_id"],
            );                   

            $this->db
                ->insert('order_transaction', $update_array_tran);
            $this->m_tools->add_web_notification($post);
            //$this->notify_driver($post["user_id"], $post["order_id"]);
            return 8;
        }
    }
    
    function save_transation($post=[]) {        
        //print_r($post); exit;
        $get_order = $this->db->select("*")
                ->where('order_id', $post["order_id"])
                ->get('orders')->row_array();
        
        $post["user_id"] = $get_order["user_id"];
	
	$config_data = $this->db->where_in('key', array('Janet-Collection_commission', 'seller_commission', 'payment_mode', 'test_public_key', 'client_key', 'test_secret_key', 'service_key'))->get('setting')->result_array();
	
	//print_r($config_data); exit;
	
	$Janet-Collection_commission = $config_data[1]['value'];
	$seller_commission = $config_data[0]['value'];

	if($config_data[4]["value"] == '1') {
	    $secret_key = $config_data[6]["value"];
	}
	else if($config_data[4]["value"] == '2') {
	    $secret_key = $config_data[2]["value"];
	}
	
	try{   
	    \Stripe\Stripe::setApiKey($secret_key); //secret key
	    //retireve checkout session details
	    $response1 = \Stripe\Checkout\Session::retrieve($post["session_id"]);      

	    //get payment intent details
	    $response2 = \Stripe\PaymentIntent::retrieve($response1->payment_intent);   

	    //get charge
	    $response = \Stripe\Charge::retrieve($response2->charges->data[0]->id);   
	   // echo "<pre>"; print_r($response); exit;

	} 
	catch (Exception $e) {
	    $response = $e->getError();            
	}
        
        /*$config_data = $this->db->where('key', 'service_key')->get('setting')->row_array();
        
        $token = $post['stripeToken'];
	//create charge
        try{   
            \Stripe\Stripe::setApiKey($config_data["value"]); //secret key

            $response = \Stripe\Charge::create(array(
                        "amount" => $post['amount'],
                        "currency" => CURRENCY,
                        "description" => "order product: ".$get_order["order_no"],
                        "capture" => TRUE,
                        "source" => $token
            ));                     
        } 
        catch (Exception $e) {
	    return $response = $e->getError();   
	}*/
        
        if(isset($response["status"]) && $response["status"] === 'succeeded'){  
            
            if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){
                                
                //Update Promocode History
                $ins_arr = array(
                    'user_id' => $get_order["user_id"],
                    'promocode_id' => $post["promocode_id"],
                );

                $this->db->insert('promocode_history', $ins_arr);

                $this->db->set('promocode_id', $post["promocode_id"])
                    ->where('order_id', $post["order_id"])
                    ->update('orders');
            }

            $product_details = $this->db->select("*")
                    ->where('order_id', $post["order_id"])
                    ->get('order_product')->result_array();

            foreach($product_details as $pd) {
                $exist_pd = $this->db->select('*')
                        ->where('order_id', $post["order_id"])
                        ->where('product_id', $pd["product_id"])
                        ->where('volume_id', $pd["volume_id"])
                        ->get('order_product')->row_array();

                if(!empty($exist_pd)){ 

                    //get product details
                    $prd_qty = $this->db->select('units')
                            ->where('product_id', $pd["product_id"])
                            ->where('volume_id', $pd["volume_id"])
                            ->get('product_details')->row_array();

                    $new_qty = $prd_qty["units"] - $pd["qty"];  

                    //update quantity in product details               
                    $this->db->set('units', $new_qty)
                            ->where('product_id', $pd["product_id"])
                            ->where('volume_id', $pd["volume_id"])
                            ->update('product_details');
                }                
            }    
            
            if($get_order["gift_card_id"] != 0) {
                //echo "1";
                $net_amount = $get_order["net_amount"] + $get_order["amount_paid"];
            }
            else if($get_order["wallet_amount"] > 0) {
               // echo "2"."<br>";
                $net_amount = $get_order["net_amount"] + $get_order["amount_paid"];
            }
            else {
                //echo "3";
                $net_amount = $get_order["net_amount"];
            }
            //echo $net_amount."<br>";
            //add loyalty points
            $this->add_loyalty_point($get_order["user_id"], $net_amount); 

            //update order
            $update_array = array(
                'order_payment_type' => 1,
                'net_amount' => $net_amount,
                'updated_date' => date('Y-m-d H:i:s'),
                'payment_done' => 1,
            ); 

            $this->db->set($update_array)
                    ->where('order_id', $get_order["order_id"])
                    ->update('orders');
            
            //get cart details
            $cart = $this->get_cart($get_order["user_id"]);
            
            //delete from cart
            $this->db->where('cart_id', $cart["cart_id"])->delete('cart');
            //delete from cart product
            $this->db->where('cart_id', $cart["cart_id"])->delete('cart_product'); 
            
            //Loyalty point
            if(isset($get_order["points"]) && $get_order["points"] > 0) {
                $loyalty_point = $this->db->select('user_id, loyalty_point')
                    ->where('user_id', $get_order["user_id"])
                    ->get('user')->row_array();
		
                //left  point
                $points = $loyalty_point["loyalty_point"] - $get_order["loyalty_point"];
                //echo $points.'<br>';
                $this->db
                    ->set('loyalty_point', $points)
                    ->where('user_id', $get_order["user_id"])
                    ->update('user');
            }

            //update order transaction
            $update_array_tran = array(
                'transaction_id' => $response["id"],
                'payment_status' => $response["status"],
                'payment_history' => json_encode($response),
                'payment_mode' => 1,
                'order_id' => $get_order["order_id"],
            );    
            
            $this->db->insert('order_transaction', $update_array_tran);    
	    
	    //transfer the amounts to seller
	    $get_sellers = $this->db->select("order_id, seller_id")
                ->where('order_id', $post["order_id"])
                ->get('order_product')->result_array();
	    
	    $seller_array = array_unique(array_column($get_sellers, "seller_id"));

	    foreach($seller_array as $sv) {
		//get stripe account for driver
		$get_seller_account = $this->db->select("*")
		    ->where('user_id', $sv)
		    ->where("status", 1)
		    ->where("is_primary", 1)
		    ->where("type", 1)
		    ->get('stripe_connect_accounts')->row_array();
		
		//print_r($get_seller_account); exit; 
		
		if(!empty($get_seller_account)) {
		    $samount = number_format(($get_order["gross_amount"] * $Janet-Collection_commission / 100), 2);
		    $seller_amt = number_format(($get_order["gross_amount"] - $samount), 2);
		    
		    //echo $get_order["gross_amount"]."<br>".$samount."<br>".$seller_amt; 
		    
		    try{   
			\Stripe\Stripe::setApiKey($secret_key); //secret key

			$response = \Stripe\Transfer::create(array(
			    "amount" => $seller_amt*100,
			    "currency" => CURRENCY,
			    "description" => "seller commission transfer to seller account",
			    "destination" => $get_seller_account["account_id"],
			    "metadata" => array(
				"account_number" => $get_seller_account["account_number"],
				"account_holder_name" => $get_seller_account["account_holder_name"],
				"bank_name" => $get_seller_account["bank_name"]
			    )
			));  
			
			//print_r($response); exit;
			$account_obj = json_encode($response);
			$account_obj = json_decode($account_obj, true);			

			//strip transfer history
			$ins_history2 = array(
			    'user_id' => $get_seller_account["user_id"],
			    'type' => 1,
			    'amount' => $seller_amt,
			    "destination" => $get_seller_account["account_id"],
			    "source_transaction" => $account_obj["id"],
			    'payment_status' => 'SUCCESS',
			    'payment_history' => json_encode($response),
			    'transaction_id' => $account_obj["id"]
			);

			$this->db->insert("stripe_transfer_transaction", $ins_history2);
		    } 
		    catch (Exception $e) {
			$response = $e->getError(); 
			$account_obj = json_encode($response);
			$account_obj = json_decode($account_obj, true);
		        //print_r($response); exit;
			//return $account_obj["message"];
		    }	
		    
		}		
	    }
            
            //get data from wallet history
            $check = $this->db->select("*")->where('user_id', $get_order["user_id"])->where('order_id', $post["order_id"])->get('wallet_history')->row_array();
            if(!empty($check)) {
                //update wallet details
                $this->complete_wallet_payment($get_order);
            }
            
            //get data from gift card history
            $check1 = $this->db->select("*")->where('card_id', $get_order["gift_card_id"])->where('user_id', $get_order["user_id"])->where('order_id', $post["order_id"])->get('gift_card_history')->row_array();
            //print_r($check1);
            if(!empty($check1)) {
                //update gift card details
                $update_gift_card = array(
                    'used_amount' => $check1["temp_used_amount"],
                    'balance_amount' => $check1["temp_balance_amount"],
                );
                
                $this->db->set($update_gift_card)
                        ->where('user_id', $get_order["user_id"])
                        ->where('card_id', $get_order["gift_card_id"])
                        ->where('order_id', $post["order_id"])
                        ->update('gift_card_history');
            }
            
            $this->order_mail($post);

            return true;

        } 
	else {

            //remove promocode if not used
            if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){
                $this->db
                    ->where('user_id', $get_order["user_id"])
                    ->where('promocode_id', $get_order["promocode_id"])
                    ->delete('promocode_history');  
            }
            //print_r($response); exit;
            //update order transaction
            $update_array_tran = array(
                'transaction_id' => $response->charge,
                'payment_history' => json_encode($response),
                'payment_status' => 'FAILED',
                'payment_mode' => 1,
                'order_id' => $get_order["order_id"],
            );                   

            $this->db->insert('order_transaction', $update_array_tran);
            
            //get data from wallet history
            $check = $this->db->select("*")->where('user_id', $get_order["user_id"])->where('order_id', $post["order_id"])->get('wallet_history')->row_array();
       
            if(!empty($check)) {
                $this->db
                    ->where('user_id', $get_order["user_id"])
                    ->where('order_id', $post["order_id"])
                    ->delete('wallet_history');  
            }
            
            //get data from history history
            $check1 = $this->db->select("*")->where('user_id', $get_order["user_id"])->where('order_id', $post["order_id"])->get('gift_card_history')->row_array();
       
            if(!empty($check1)) {
                $this->db
                    ->where('user_id', $get_order["user_id"])
                    ->where('card_id', $get_order["gift_card_id"])
                    ->where('order_id', $post["order_id"])
                    ->delete('gift_card_history');  
            }

            return false;
        }
    }
    
    function get_delivery_charge(){
	$charge = $this->db->select("value")->where("key", "delivery_charges")->get("setting")->row_array();
	return $charge;
    }
}

<?php

class M_orders extends CI_Model {
    
    function order_return() {
	$user_id = $this->session->userdata("user_id");
	$details = $this->db->select("pr.*, o.order_no, p.product_name")
            ->join("orders o", "o.order_id = pr.order_id")
	    ->join("products p", "p.product_id = pr.product_id")
            ->where('pr.user_id', $user_id)
	    ->where("pr.is_confirmed", 1)
            ->order_by('pr.update_date', 'desc')	    
            ->get('products_returned pr')->result_array(); 
	
	return $details;
    }
    
    public function my_order($user_id) {
        $details = $this->db->select("order_no, order_date, order_id, net_amount, total_qty")
            ->where('user_id', $user_id)
            ->order_by('order_date', 'desc')
            ->get('orders')->result_array();  
        
        if(!empty($details)) {
            foreach($details as $key => $value){
                
                $order_details = $this->db->select("*")
                    ->where('order_id', $value["order_id"])
                    ->get('order_product')->result_array(); 
                
               // print_r($order_details);
                
                foreach($order_details as $okey => $ovalue){                 
                    //get order product details
                    $product_details = $this->order_product_list($ovalue["product_id"], $user_id);
                    $volume_details = $this->m_tools->get_volume_by_id($ovalue["volume_id"]);
                    
                    if(!empty($product_details)){                    
                        $order_details[$okey]["product_name"] = $product_details[0]["product_name"];
                        $order_details[$okey]["brand_name"] = $product_details[0]["brand_name"];
                        $order_details[$okey]["feature_img"] = $product_details[0]["feature_img"];
                        $order_details[$okey]["feature_img_thumb"] = $product_details[0]["feature_img_thumb"];
                        $order_details[$okey]["no_of_return_days"] = $product_details[0]["no_of_return_days"];                        
                        $order_details[$okey]["is_review"] = $product_details[0]["is_review"];
                    }
                    
                    if(!empty($volume_details)){
                        $order_details[$okey]["volume"] = $volume_details["volume"];
                    }
                    
                    $details[$key]["products"] = $order_details;
               } 
            }            
        
            return $details;
        }
        else {
            return false;
        }            
    } 
    
    public function my_past_order($user_id, $offset) {
        $limit = 5;
        $where = "order_date <= now()";
        $details = $this->db->select("order_no, order_date, order_id, net_amount, total_qty")
            ->where('user_id', $user_id)
            ->where('order_status', 4)
	    ->limit($limit)
	    ->offset($offset)
            ->order_by('order_date', 'desc')
            ->get('orders')->result_array();  
        
        //print_r($details); exit;
        
        if(!empty($details)) {
            foreach($details as $key => $value){
                
                $order_details = $this->db->select("*")
                    ->where('order_id', $value["order_id"])
                    ->get('order_product')->result_array(); 
                
               // print_r($order_details);
                
                foreach($order_details as $okey => $ovalue){                 
                    //get order product details
                    $product_details = $this->order_product_list($ovalue["product_id"], $user_id);
                    $volume_details = $this->m_tools->get_volume_by_id($ovalue["volume_id"]);
                    
                    if(!empty($product_details)){                    
                        $order_details[$okey]["product_name"] = $product_details[0]["product_name"];
                        $order_details[$okey]["brand_name"] = $product_details[0]["brand_name"];
                        $order_details[$okey]["seller"] = $product_details[0]["seller"];
                        $order_details[$okey]["feature_img"] = $product_details[0]["feature_img"];
                        $order_details[$okey]["feature_img_thumb"] = $product_details[0]["feature_img_thumb"];
                        $order_details[$okey]["is_review"] = $product_details[0]["is_review"];
                    }
                    
                    if(!empty($volume_details)){
                        $order_details[$okey]["volume"] = $volume_details["volume"];
                    }
                    
                    $details[$key]["products"] = $order_details;
		    $details[$key]["order_date"] = date('d F, Y', strtotime($value["order_date"]));
                } 
            }
        
            $response["list"] = $details;
	    $response["offset"] = $offset + $limit;
        
            return $response;
        }
        else {
            return 'error';
        }            
    } 
    
    public function my_upcoming_order($user_id, $offset) {   
	$limit = 5;
        $where = "order_status IN (1, 2, 3, 6, 5, 7)";
        $details = $this->db->select("order_no, order_date, order_id, net_amount, total_qty")
            ->where('user_id', $user_id)
            ->where($where)
	    ->limit($limit)
	    ->offset($offset)
            ->order_by('order_date', 'desc')
            ->get('orders')->result_array();  
        
        //print_r($details); exit;
        
        if(!empty($details)) {
            foreach($details as $key => $value){
                
                $order_details = $this->db->select("*")
                    ->where('order_id', $value["order_id"])
                    ->get('order_product')->result_array(); 
                
               // print_r($order_details);
                
                foreach($order_details as $okey => $ovalue){                 
                    //get order product details
                    $product_details = $this->order_product_list($ovalue["product_id"], $user_id);
                    $volume_details = $this->m_tools->get_volume_by_id($ovalue["volume_id"]);
                    
                    if(!empty($product_details)){                    
                        $order_details[$okey]["product_name"] = $product_details[0]["product_name"];
                        $order_details[$okey]["brand_name"] = $product_details[0]["brand_name"];
                        $order_details[$okey]["seller"] = $product_details[0]["seller"];
                        $order_details[$okey]["feature_img"] = $product_details[0]["feature_img"];
                        $order_details[$okey]["feature_img_thumb"] = $product_details[0]["feature_img_thumb"];
                        $order_details[$okey]["is_review"] = $product_details[0]["is_review"];
                    }
                    
                    if(!empty($volume_details)){
                        $order_details[$okey]["volume"] = $volume_details["volume"];
                    }
                    
                    $details[$key]["products"] = $order_details;
		    $details[$key]["order_date"] = date('d F, Y', strtotime($value["order_date"]));
                } 
            }
	    $response["list"] = $details;
	    $response["offset"] = $offset + $limit;
        
            return $response;
        }
        else {
            return 'error';
        }            
    } 
    
    public function order_details($post=[]) {
        //print_r($post); exit;
        $details1 = $this->db->select("*")
            ->where('user_id', $post["user_id"])
            ->where('order_id', $post["order_id"])
            ->get('orders')->row_array();  
        
        //print_r($details1); exit;
        
        if(!empty($details1)) {
	    $details = array_map(function($val) {
		if(is_null($val)) {
		    $val = "";
		}
		return $val;
	    }, $details1);

	    //orde cancel request
	    $order_cancel_request = $this->db->select("*")
		    ->where('user_id', $post["user_id"])
		    ->where('order_id', $post["order_id"])
		    ->get('order_canceled')->row_array();

	    if(!empty($order_cancel_request)){
		if($order_cancel_request["is_confirmed"] == 0){
		    $details["cancel_request_status"] = "Cancel Request In-Progress";
		}
		else if($order_cancel_request["is_confirmed"] == 1){
		    $details["cancel_request_status"] = "Order Cancelled";
		}
		else if($order_cancel_request["is_confirmed"] == 2){
		    $details["cancel_request_status"] = "Order cannot be Cancelled";
		}
	    }

	    //promocode details
	    $order_promocode = $this->db->select("*")
		->where('promocode_id', $details["promocode_id"])
		->get('promocodes')->row_array(); 

	    if(!empty($order_promocode)) {
		$details["promocode"] = $order_promocode;
	    }

	    if( $details["order_status"] == 1 ){
		$details["orderStatus"] = "Pending";
	    }
	    elseif( $details["order_status"] == 2 ){
		$details["orderStatus"] = "Accepted By Seller";
	    }
	    elseif( $details["order_status"] == 3 ){
		$details["orderStatus"] = "Accepted By Driver";
	    }
	    elseif( $details["order_status"] == 4 ){
		$details["orderStatus"] = "Delivered";
	    }
	    elseif( $details["order_status"] == 5 ){
		$details["orderStatus"] = "Cancelled";
	    }
	    elseif( $details["order_status"] == 6 ){
		$details["orderStatus"] = "Order Placed";
	    }
	    elseif( $details["order_status"] == 7 ){
		$details["orderStatus"] = "Rejected By Seller";
	    }
	    elseif( $details["order_status"] == 8 ){
		$details["orderStatus"] = "Cancelled By Driver";
	    }
	    elseif( $details["order_status"] == 9 ){
		$details["orderStatus"] = "Picked Up";
	    }
	    elseif( $details["order_status"] == 10 ){
		$details["orderStatus"] = "Start Delivery";
	    }
	    elseif( $details["order_status"] == 11 ){
		$details["orderStatus"] = "End Delivery";
	    }
	    elseif( $details["order_status"] == 12 ){
		$details["orderStatus"] = "Pause";
	    }
	    elseif( $details["order_status"] == 13 ){
		$details["orderStatus"] = "Not Completed";
	    }

	    //gift card details
	    $post["card_id"] = $details["gift_card_id"];
	    $order_gift_card = $this->m_gift_card->gift_card_by_id($post);
	    
	    if(!empty($order_gift_card) || $order_gift_card) {
		$details["gift_card"] = $order_gift_card;
	    }
	    else {
		$details["gift_card"] = array();
	    }
	    
	    //get shipping details
	    $post["shipping_id"] = $details["shipping_id"];
	    $shipping_details = $this->m_shipping->get_all_shipping_by_id($post); 
	    
	    if(!empty($shipping_details) || $shipping_details) {
		$details["shipping_details"] = $shipping_details; 
	    }
	    else {
		$details["shipping_details"] = array();
	    }
	    
	    //order product details
	    $order_details = $this->db->select("*")
		->where('order_id', $details["order_id"])
		->get('order_product')->result_array(); 
	    
	    if(!empty($order_details)) {
		foreach($order_details as $okey => $ovalue){                 
		    //get order product details
		    $product_details = $this->order_product_list($ovalue["product_id"], $post["user_id"]);
		    $volume_details = $this->m_tools->get_volume_by_id($ovalue["volume_id"]);
		    //return policy
		    $return_policy = $this->get_product_return_policy($ovalue["product_id"]);   
		    $returned = $this->get_return_product_details($post["order_id"], $ovalue["product_id"], $ovalue["volume_id"]);

		    //print_r($returned); 
		    if(!empty($product_details)){                    
			$order_details[$okey]["product_name"] = $product_details[0]["product_name"];
			$order_details[$okey]["brand_name"] = $product_details[0]["brand_name"];
			$order_details[$okey]["seller"] = $product_details[0]["seller"];
			$order_details[$okey]["feature_img"] = $product_details[0]["feature_img"];
			$order_details[$okey]["feature_img_thumb"] = $product_details[0]["feature_img_thumb"];
			$order_details[$okey]["no_of_return_days"] = $product_details[0]["no_of_return_days"];
			$order_details[$okey]["is_review"] = $product_details[0]["is_review"];

			if(!empty($returned)){
			    if($returned["is_confirmed"] == 0) {
				$order_details[$okey]["return_flag"] = "Pending";
			    }
			    else if($returned["is_confirmed"] == 1) {
				$order_details[$okey]["return_flag"] = "Accepted";
			    }
			    else if($returned["is_confirmed"] == 2) {
				$order_details[$okey]["return_flag"] = "Rejected";
			    }
			    $order_details[$okey]["refund_amount"] = $returned["amount_refunded"];
			}
			else {
			    $order_details[$okey]["return_flag"] = "";
			}

			if($product_details[0]["no_of_return_days"] != 0) {
			    $order_details[$okey]["return_policy"] = $return_policy;
			}
			else {
			    $order_details[$okey]["return_policy"] = 'Cannot Return.';
			}

			//calculate allowed return days
			if($product_details[0]["no_of_return_days"] > 0 && !empty($return_policy) && $details["order_status"] == '4') {
			    $delivered_date = strtotime($details["delivered_date"]);
			    $return_due_date =  strtotime(' + '.$product_details[0]["no_of_return_days"].' day', $delivered_date);
			    $today = strtotime(date('Y-m-d'));

			    if($return_due_date > $today) {
				$order_details[$okey]["can_be_return"] = 1;
			    }
			    else {
				$order_details[$okey]["can_be_return"] = 0;
			    }
			}    
			else {
			    $order_details[$okey]["can_be_return"] = 0;
			}
		    }

		    if(!empty($volume_details)){
			$order_details[$okey]["volume"] = $volume_details["volume"];
		    }

		    $details["products"] = $order_details;
		} 
	    }
	    
	    //product delivery date
	    if($details["order_done_type"] == 1) {
		$details["delivery_date"] = $details["order_date"]; 
	    }
	    else if($details["order_done_type"] == 2) {
		$details["delivery_date"] = $details["to_be_delivered_date"]; 
	    }            
        
	   // print_r($details); exit;
            return $details;
        }
        else {
            return 'error';
        }            
    } 
    
    public function get_return_product_details($order_id, $product_id, $volume_id){
        $returned = $this->db->select("*")
                ->where('product_id', $product_id)
                ->where('volume_id', $volume_id)
                ->where('order_id', $order_id)
                ->get('products_returned')->row_array();
        if(!empty($returned)){
            return $returned;
        }
	else {
            return array();
        }
    }
    
    public function get_product_return_policy($product_id){        
        
        $this->db->select("*", FALSE)
            ->from('product_return_policy')
            ->where('product_id', $product_id);
        $return_policy = $this->db->get()
                ->row_array();   
        
        if (!empty($return_policy)) {
            return $return_policy;            
        }        
        else {
            return array();
        }
    }
    
    public function order_product_list($product_id, $user_id) {         
        
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
    
    function get_order_by_id($order_id) {
	$response = $this->db->select("*")->where("order_id", $order_id)->get("orders")->row_array();
	return $response;
    }
}

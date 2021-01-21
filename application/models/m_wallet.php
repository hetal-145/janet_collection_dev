<?php

class M_wallet extends CI_Model{
    
    public function wallet_balance($user_id) {        
        $balance = $this->db->select("user_id, wallet")
                        ->where("user_id", $user_id)
                        ->where("status", 1)
                        ->get("user")->row_array();
        return $balance["wallet"];
    }
    
    public function wallet_received($user_id){
        $response = $this->db->select("wallet_history.*, orders.order_no")
                        ->join("orders", "orders.order_id = wallet_history.order_id", "left")
                        ->where("wallet_history.user_id", $user_id)
                        ->where("wallet_history.type", 2)
                        ->get("wallet_history")->result_array();
        return $response;
    }
    
    public function order_return($user_id) {
        $where_his = "user_id = ".$user_id." and type = 2";

        //history of gift card
        $wallet_history = $this->db->select("*")
            ->where($where_his)
            ->order_by('date', 'desc')
            ->get('wallet_history')->result_array();            

	if(!empty($wallet_history)){

	    foreach($wallet_history as $key => $value){

		$order_details = $this->db->select("order_no, order_date, order_id, net_amount, wallet_amount, total_qty")
		    ->where('user_id', $user_id)
		    ->where('order_id', $value["order_id"])
		    ->order_by('order_date', 'desc')
		    ->get('orders')->row_array();

		//get order product details
		$product_details = $this->db->select("products.product_name, order_product.price, order_product.qty, order_product.net_total")
		    ->join('products', 'products.product_id = order_product.product_id')
		    ->where('order_product.order_id', $value["order_id"])
		    ->get('order_product')->result_array();

		foreach($product_details as $key1 => $value1){
		    $product_details[$key1]["order"] = $order_details;
		}                   

		$wallet_history[$key]["order"] = $product_details;
		$wallet_history[$key]["order_no"] = $order_details["order_no"];
	       // $wallet_history[$key]["order"] = $order_details;
		//$order_details["products"] = $product_details; 


		if($wallet_history[$key]["type"] == 1){
		    $wallet_history[$key]["transaction_type"] = "Debit";
		} 
		else if($wallet_history[$key]["type"] == 2){
		    $wallet_history[$key]["transaction_type"] = "Credit";
		}
	    }

	    return $wallet_history;
	}             
    }
}


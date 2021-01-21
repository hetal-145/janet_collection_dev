<?php

class M_transaction_history extends CI_Model {       
    
    function get_order_transaction($order_transaction_id){
        $currency = $this->db->select("*")->where("key", "currency")->get("setting")->row_array();
        
        //Order transaction
        $order_transaction = $this->db->select("*, case payment_mode when 1 then 'Card Payment' when 2 then 'COD' when 3 then 'Gift Card Payment' when 4 then 'Wallet' end as payment_method", false)
                ->where("order_transaction_id", $order_transaction_id)->get("order_transaction")->row_array();        
        
        if($order_transaction["payment_mode"] == 1){
            $transaction_amt = json_decode($order_transaction["payment_history"], true);       
            $order_transaction["transaction_amount"] = $currency["value"].($transaction_amt["amount"] / 100);
        }
        else {
            $transaction_amt = json_decode($order_transaction["payment_history"], true);        
            $order_transaction["transaction_amount"] = $currency["value"].$transaction_amt["amount"];
        }
        
        $order_transaction["currency"] = $currency["value"];
        //order details
        $orders = $this->db->select("*, case order_payment_type when 1 then 'Card Payment' when 2 then 'COD' when 3 then 'Gift Card Payment' when 4 then 'Wallet' end as order_payment_type, case order_status when '5' then 'Order Cancelled by User' when '4' then 'Delivered' when '3' then 'Shipped' when '2' then 'Ready To Ship' when '6' then 'Order Placed' when '1' then 'Pending' when '7' then 'Reject/Cancel the order' end as order_status1", false)->where("order_id", $order_transaction["order_id"])->get("orders")->row_array();
        $orders["net_amount"] = $currency["value"] . $orders["net_amount"];
        $orders["delivery_charges"] = $currency["value"] . $orders["delivery_charges"];
        //order products
        $order_products = $this->db->select("order_product.*, products.product_name, products.seller_id as pseller, seller.seller_name")
                ->join('products', 'products.product_id = order_product.product_id')
                ->join('seller', 'seller.seller_id = products.seller_id')
                ->where("order_product.order_id", $order_transaction["order_id"])
                ->get("order_product")
                ->result_array();
        
        $seller_array = array_unique(array_column($order_products, 'pseller'));
        $unique_seller_array = array();
        foreach ($seller_array as $value){            
            //seller
            $seller = $this->db->select('seller_id, seller_name, company_name, email, contact_no, address, latitude, longitude, case when gender=1 then "Male" when gender=2 then "Female" end as gender', false)        
                                 ->where('status', 1)
                                 ->where('is_admin_verified', 1)
                                 ->where('seller_id', $value)
                                 ->get('seller')->row_array();

            array_push($unique_seller_array, $seller);
        }
        
        $orders["seller"] = $unique_seller_array; 
        //currency
        $orders["currency"] = $currency["value"];
        $orders["products"] = $order_products;     
        
        if(in_array(1, array_column($order_products, 'allow_split_order'))){
            $orders["delivery_type"] = "Split Delivery";
        }
        else {
            $orders["delivery_type"] = "Normal Delivery";
        }
        
        //get drivers
        $driver_list = $this->db->select("*")
                ->where("order_id", $order_transaction["order_id"])
                ->where('status', 1)
                ->get('order_driver')->row_array();       
        
        if(!empty($driver_list)){        
            $driveruser = $this->db->select("userno, firstname, lastname, mobileno, birthdate, email")
                ->where('user_id', $driver_list["driver_id"])
                ->where('user_type', 2)
                ->get('user')->row_array();

            $order_transaction["driver_details"] = $driveruser;        
        }
        
        //user details
        $user = $this->db->select("userno, firstname, lastname, mobileno, birthdate, email")
                ->where('user_id', $orders["user_id"])
                ->get('user')->row_array();
        
        $order_transaction["user"] = $user;  
        
        $order_transaction["orders"] = $orders;
        //print_r($order); exit;
        return $order_transaction;
    }
}

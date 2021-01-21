<?php

class M_home extends CI_Model {
    
    public function show_notifications(){
	$response = $this->db->select("message, notification_type")
		->where("is_read", 0)
		->where("is_notified", 0)
		->where("notification_type IN (2,3,4,5)")
		//->where("user_id is not null")
		->get("website_notification")->result_array();
	
	if(!empty($response)){
	    return $response;
	}
    }
    
    public function total_alcohol_products_sold() {
        $qty = 0;
        $response = $this->db->select("order_product.order_id, order_product.product_id, order_product.qty")
                ->join('products', 'products.product_id = order_product.product_id')
                ->where('products.status',1)
                ->where('products.drink_type',1)
                ->get('order_product')
                ->result_array();
        
        foreach($response as $res){
            $qty = $qty + $res["qty"];
        }
        
        return $qty;
    }
    
    public function total_non_alcohol_products_sold() {
        $qty = 0;
        $response = $this->db->select("order_product.order_id, order_product.product_id, order_product.qty")
                ->join('products', 'products.product_id = order_product.product_id')
                ->where('products.status',1)
                ->where('products.drink_type',2)
                ->get('order_product')
                ->result_array();
        
        foreach($response as $res){
            $qty = $qty + $res["qty"];
        }
        
        return $qty;
    }
    
    public function total_alcohol_products_orders() {
        $where = "order_status IN (1,2,3,4)";
        $response = $this->db->select("order_product.order_id, order_product.product_id")
                ->join('order_product', 'order_product.order_id = orders.order_id')
                ->join('products', 'products.product_id = order_product.product_id')
                ->where($where)
                ->where('orders.payment_done',1)
                ->where('products.status',1)
                ->where('products.drink_type',1)
                ->get('orders')
                ->num_rows();
        
        return $response;
    }  
    
    public function get_no_of_user_signedup_this_week() {
        $day = date('l');
        if($day !== 'Sunday') {
            $sunday = date( 'Y-m-d', strtotime( 'previous sunday' ) );
        } else {
            $sunday = date( 'Y-m-d', strtotime( 'today' ) );
        }
        if($day !== 'Saturday') {
            $saturday = date( 'Y-m-d', strtotime( 'saturday this week' ) );
        } else {
            $saturday = date( 'Y-m-d', strtotime( 'today' ) );
        }
        //echo $sunday; echo "<br>"; echo $saturday;
        
        $where = "DATE(member_since) between '".$sunday."' AND '".$saturday."'";
        $users = $this->db->select('*')
                ->where($where)
                ->get('user')
                ->result_array();
        
        //print_r($users); exit;
        return count($users);
    }
    
    public function drivers_online_day() {
        $users = $this->db->select('user_id')
                ->where('user_type', 2)
		->where('is_online', 1)
		->where('status', 1)
                ->get('user')
                ->result_array();
        
        //print_r($users); exit;
        return count($users);
    }
    
    public function get_users_online_day() {
        $where = "DATE(login_time) = CURDATE()";
        $users = $this->db->select('*')
                ->where($where)
                ->get('logtbl')
                ->result_array();
        
        //print_r($users); exit;
        return count($users);
    }
    
    public function get_users_online_week() {
        $day = date('l');
        if($day !== 'Sunday') {
            $sunday = date( 'Y-m-d', strtotime( 'previous sunday' ) );
        } else {
            $sunday = date( 'Y-m-d', strtotime( 'today' ) );
        }
        if($day !== 'Saturday') {
            $saturday = date( 'Y-m-d', strtotime( 'saturday this week' ) );
        } else {
            $saturday = date( 'Y-m-d', strtotime( 'today' ) );
        }
        //echo $sunday; echo "<br>"; echo $saturday;
        
        $where = "DATE(login_time) between '".$sunday."' AND '".$saturday."'";
        $users = $this->db->select('*')
                ->where($where)
                ->get('logtbl')
                ->result_array();
        
        //print_r($users); exit;
        return count($users);
    }
    
    public function get_users_online_month() {
        $where = "MONTH(login_time) = MONTH(CURDATE()) AND YEAR(login_time) = YEAR(CURDATE())";
        $users = $this->db->select('*')
                ->where($where)
                ->get('logtbl')
                ->result_array();        
        //print_r($users); exit;
        return count($users);
    }
    
    public function get_alcohol_products() {
        $products = $this->db->where('status',1)->where('drink_type',1)
                ->get('products')
                ->result_array();
        return count($products);
    }
    
    public function get_non_alcohol_products() {
        $products = $this->db->where('status',1)->where('drink_type',2)
                ->get('products')
                ->result_array();
        return count($products);
    }
    
    public function get_active_categories() {
        $categories = $this->db->where('status',1)
                ->get('category_mst')
                ->result_array();
        return count($categories);
    }
    
    public function get_active_subcategories() {
        $categories = $this->db->where('status',1)
		->where('parent_id > 0')
                ->get('category_mst')
                ->result_array();
        return count($categories);
    }
    
    public function get_active_brands() {
        $brands = $this->db->where('status',1)
                ->get('brand_mst')
                ->result_array();
        return count($brands);
    }
    
    public function get_total_gift_cards_sent() {
        $gift_cards = $this->db
                ->get('gift_card')
                ->result_array();
        return count($gift_cards);
    }
    
    public function total_products() {
        $total_sellers = $this->db
                ->where('status',1)
                ->get('products')
                ->num_rows();
        return $total_sellers;
    }
    
    public function get_total_orders() {
        $orders = $this->db->select("orders.order_id")
		->where('orders.payment_done',1)
                ->get('orders')
                ->result_array();
        return count($orders);
    }
    
    public function total_testimonials() {
        $user_info = $this->db
                ->where('status',1)
                ->get('testimonials')
                ->num_rows();
        return $user_info;
    }
    
    public function total_users() {
        $user_info = $this->db
                ->where('status',1)
                ->where('user_type',1)
                ->get('user')
                ->num_rows();
        return $user_info;
    }
    
    public function total_sellers() {
        $total_sellers = $this->db
                ->where('status',1)
                ->where('is_admin_verified',1)
                ->get('seller')
                ->num_rows();
        return $total_sellers;
    }
    
    public function total_drivers() {
        $total_drivers = $this->db
                ->where('status',1)
                ->where('user_type',2)
                ->get('user')
                ->num_rows();
        return $total_drivers;
    }
    
    public function total_dz() {
        $total_sellers = $this->db
                ->where('status',1)
                ->get('delivery_zone')
                ->num_rows();
        return $total_sellers;
    }
    
    public function total_loyalty_products() {
        $total_sellers = $this->db
                ->where('status',1)
		->where('in_loyalty_club',1)
                ->get('products')
                ->num_rows();
        return $total_sellers;
    }
    
    public function total_vip_products() {
        $total_sellers = $this->db
                ->where('status',1)
		->where('in_vip_club',1)
                ->get('products')
                ->num_rows();
        return $total_sellers;
    }
    
    public function total_suppliers() {
        $total_sellers = $this->db
                ->where('status',1)
                ->get('suppliers')
                ->num_rows();
        return $total_sellers;
    }
    
    public function total_promocodes() {
        $total_sellers = $this->db
                ->where('status',1)
                ->get('promocodes')
                ->num_rows();
        return $total_sellers;
    }
    
    public function get_total_new_orders() {
        $orders = $this->db->select("orders.order_id")
		->where('orders.payment_done',1)
		->where('orders.order_status IN (1,2,3)')
                ->get('orders')
                ->result_array();
        return count($orders);
    }
    
    public function get_total_delivered_orders() {
        $orders = $this->db->select("orders.order_id")
		->where('orders.payment_done',1)
		->where('orders.order_status',4)
                ->get('orders')
                ->result_array();
        return count($orders);
    }
    
    public function get_total_cancelled_orders() {
        $orders = $this->db->select("orders.order_id")
		->where('orders.payment_done',1)
		->where('orders.order_status IN (5,7)')
                ->get('orders')
                ->result_array();
        return count($orders);
    }
    
    public function get_total_income() {
  //       $income = $this->db->select("orders.order_id, FORMAT(sum(order_product.net_total), 2) as total", false)
		// ->join("order_product", "order_product.order_id = orders.order_id")
		// ->where('order_product.is_refund',0)
		// ->where('orders.payment_done',1)
		// ->where('orders.order_status', 4)
  //               ->get('orders')
  //               ->result_array();
	
	   // return $income[0]["total"];
        return 0;
    }
    
    public function get_total_amount_to_receive() {
  //       $income = $this->db->select("orders.order_id, FORMAT(sum(order_product.net_total), 2) as total", false)
		// ->join("order_product", "order_product.order_id = orders.order_id")
		// ->where('order_product.is_refund',0)
		// ->where('orders.payment_done',1)
		// ->where('orders.order_status IN (1,2,3,6,9,10,11,12)')
  //               ->get('orders')
  //               ->result_array();
	    
	   // return $income[0]["total"];
        return 0;
    }
    
    public function orders_not_completed() {
        $orders = $this->db->select("orders.order_id")
		->where('orders.payment_done',1)
		->where('orders.order_status IN (13)')
                ->get('orders')
                ->result_array();
        return count($orders);
    }
    
    public function orders_in_process() {
        $orders = $this->db->select("orders.order_id")
		->where('orders.payment_done',1)
		->where('orders.order_status IN (9,11)')
                ->get('orders')
                ->result_array();
        return count($orders);
    }
    
    public function new_driver_request() {
        $response = $this->db->select("*")
		->where('status',0)
                ->get('driver_requests')
                ->num_rows();
        return $response;
    }
    
    public function new_driver_request_vehicle() {
        $response = $this->db->select("*")
		->where('status',0)
                ->get('driver_vehicle_requests')
                ->num_rows();
        return $response;
    }
    
    public function new_sellers() {
        $response = $this->db->select("*")
		->where('status',0)
		->where('is_admin_verified',0)
                ->get('seller')
                ->num_rows();
        return $response;
    }
    
    public function new_drivers() {
        $response = $this->db->select("*")
		->where('status',0)
		->where('is_admin_verified',0)
		->where('user_type',2)
                ->get('user')
                ->num_rows();
        return $response;
    }
    
}

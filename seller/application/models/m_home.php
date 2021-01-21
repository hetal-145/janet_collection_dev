<?php

class M_home extends CI_Model {    
    
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
}


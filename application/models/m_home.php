<?php

class M_home extends CI_Model {
    
    function get_faq_question_list(){
        $get_faq_questions = $this->db->select('faq_id, faq_question, faq_answer')->where("status", 1)
                ->get('faq_mst')->result_array();

        if (!empty($get_faq_questions)) {
            return $get_faq_questions;            
        }        
        else {
            return false;
        }
    }
    
    function get_seller_faq_question_list(){
        $get_faq_questions = $this->db->select('faq_id, faq_question, faq_answer')->where("status", 1)
                ->get('seller_faq_mst')->result_array();

        if (!empty($get_faq_questions)) {
            return $get_faq_questions;            
        }        
        else {
            return false;
        }
    }
    
    function licensed_retailers() {
	$response = $this->db->select("seller_id, seller_name, company_name")
		->where("status", 1)
		->where("is_admin_verified", 1)
		->get("seller")->result_array();
	
	if(!empty($response)) {
	    return $response;
	}
    }
    
    public function get_best_selling_products($dzone = "") {
	if(!empty($dzone)) {
	     //find nearest seller
            $nearest_seller = $this->db->select('seller_id, dzone_id', false)        
                                ->where('status', 1)
                                ->where('is_admin_verified', 1)
				->where('dzone_id', $dzone)
                                ->get('seller')->result_array();
	    
	    if(!empty($nearest_seller)) {               
                foreach($nearest_seller as $value1){
		    $top_pick = $this->db->select("order_product.product_id, count(order_product.product_id) as max_product, products.*, category_mst.category_name, brand_mst.brand_name", false)
				->join("products", "products.product_id = order_product.product_id and products.status=1")
				->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1')
				->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1')
				->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')  
				->where("products.seller_id", $value1["seller_id"])
				->group_by("order_product.product_id")
				->order_by("max_product", "desc")
				->limit(10)
				->get("order_product")->result_array();
                }
            }
	}
	else {
	    $top_pick = $this->db->select("order_product.product_id, count(order_product.product_id) as max_product, products.*, category_mst.category_name, brand_mst.brand_name", false)
				->join("products", "products.product_id = order_product.product_id and products.status=1")
				->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1')
				->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1')
				->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
				->group_by("order_product.product_id")
				->order_by("max_product", "desc")
				->limit(10)
				->get("order_product")->result_array();
	}

        if (!empty($top_pick)) {  
            $user_id = $this->session->userdata('user_id');

            //print_r($top_pick); exit;
            foreach($top_pick as $key => $value){  

                $top_pick[$key]['seller'] = $this->seller_details($value['seller_id']);

                //get favourite
                $top_pick[$key]['is_favourite'] = $this->product_favourite($value['product_id'], $user_id);

                //Get Product Volume
                $this->db->select("product_details.*, concat(volume_mst.volumne_value,' ',(select volume_type from volume_type where volume_type_id = volume_mst.type)) as volumes", false)
                        ->from("products")
                        ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
                        ->join('volume_mst', 'product_details.volume_id=volume_mst.volume_id and volume_mst.status=1')
                        ->where("products.product_id", $value['product_id'])
                        //->where("volume_mst.brand_id", $value['brand_id'])
                        ->where("products.status", 1)
                        ->order_by("products.date", "desc");
                $get_data_volume = $this->db->get()
                        ->result_array();

                if(!empty($get_data_volume)){ 

                    $top_pick[$key]["price"] = number_format($get_data_volume[0]["normal_sell_price"], 2);
		    $top_pick[$key]["actual_price"] = number_format($get_data_volume[0]["actual_price"], 2);
		    foreach($get_data_volume as $vkey => $vvalue){ 
			$get_data_volume[$vkey]["actual_price"] = $vvalue["actual_price"];
		    }  
		    
                    $top_pick[$key]['isvolume'] = true;
                } 
                else {
                    $top_pick[$key]['isvolume'] = false;
                }

                //Get Product Return policy
                $this->db->select("product_return_policy.*", false)
                        ->from("products")
                        ->join('product_return_policy', 'products.product_id = product_return_policy.product_id')
                        ->where("products.product_id", $value['product_id'])
                        ->where("products.have_return_policy", 1)
                        ->where("products.status", 1)
                        ->order_by("products.date", "desc");
                $get_data_policy = $this->db->get()
                            ->row_array();

                if(!empty($get_data_policy)){
                    $get_data_policy["description"] = html_entity_decode(strip_slashes(strip_tags($get_data_policy["description"], '<strong>')));
                    $get_data_policy["status"] = true;
                } else {
                    $get_data_policy["status"] = false;
                }

                $top_pick[$key]['return_policy'] = $get_data_policy;                
                $top_pick[$key]['volume'] = $get_data_volume;

                //feature image
                $imgs = $this->product_feature_img($value["feature_img"]);
                $top_pick[$key]['feature_img'] = $imgs[0];
                $top_pick[$key]['feature_img_thumb'] = $imgs[1];

            }        

            return $top_pick;            
        }        
        else {
            return false;
        }        
    }
    
    public function get_testimonials_list() {        
        $get_data = $this->db->select('*')
            ->where('status', 1)
            ->limit(6)
            ->get('testimonials')
            ->result_array();        
        foreach($get_data as $key => $value){   
            if($value['image']){                
                $get_data[$key]['image'] = $this->m_tools->image_url( $value['image'],'', 'testimonial'); 
            }
            else {
                $get_data[$key]['image'] = '';
            }            
        }        
        if(!empty($get_data)){
            return $get_data;
        }
    }
    
    public function get_top_pick_product_list($dzone = "") {  
	if(!empty($dzone)) {
	     //find nearest seller
            $nearest_seller = $this->db->select('seller_id, dzone_id', false)        
                                ->where('status', 1)
                                ->where('is_admin_verified', 1)
				->where('dzone_id', $dzone)
                                ->get('seller')->result_array();
	    
	    if(!empty($nearest_seller)) {               
                foreach($nearest_seller as $value1){
		    //top pick product list
		    $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", false)
			    ->from("products")
			    ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1')
			    ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1')
			    ->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
			    ->where("products.top_pick", 1)
			    ->where("products.status", 1)
			    ->where("products.seller_id", $value1["seller_id"])
			    ->group_by('products.product_id')
			    ->order_by("products.date", "desc")
			    ->limit(8);
		    $top_pick = $this->db->get()
			    ->result_array(); 
                }
            }
	}
	else {
	    //top pick product list
	    $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", false)
		    ->from("products")
		    ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1')
		    ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1')
		    ->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
		    ->where("products.top_pick", 1)
		    ->where("products.status", 1)
		    ->group_by('products.product_id')
		    ->order_by("products.date", "desc")
		    ->limit(8);
	    $top_pick = $this->db->get()
		    ->result_array();     
	}

        if (!empty($top_pick)) {            
            
            $user_id = $this->session->userdata('user_id');

            foreach($top_pick as $key => $value){  
                
                //seller details
                $top_pick[$key]["seller"] = $this->seller_details($value["seller_id"]);

                //product volumne
                $get_data_volume = $this->product_volumne($value["product_id"]);   
		$top_pick[$key]["price"] = number_format($get_data_volume[0]["normal_sell_price"], 2);
		$top_pick[$key]["actual_price"] = number_format($get_data_volume[0]["actual_price"], 2);
		foreach($get_data_volume as $vkey => $vvalue){ 
		    $get_data_volume[$vkey]["actual_price"] = $vvalue["actual_price"];
		}  
                
                //feature image
                $imgs = $this->product_feature_img($value["feature_img"]);
                $top_pick[$key]['feature_img'] = $imgs[0];
                $top_pick[$key]['feature_img_thumb'] = $imgs[1];
                
                if($this->session->userdata('loged_in')) { 
                    //get favourite
                    $top_pick[$key]['is_favourite'] = $this->product_favourite($value['product_id'], $user_id);
                }
                else {
                    $top_pick[$key]['is_favourite'] = 0;
                }
            }   
            return $top_pick;            
        }        
        else {
            return false;
        }
    }
    
    function seller_details($seller_id) {
        if($seller_id != 0) {
            //seller info
            $seller = $this->db->select("seller_id, seller_name", false)
                    ->where('seller_id', $seller_id)
                    ->where('status', 1)
                    ->where('is_admin_verified', 1)
                    ->get('seller')->row_array();
        }
        else {
            $seller = array(
                'seller_name' => "Admin"
            );
        }        
        return $seller;
    }
    
    function product_volumne($product_id) {
        //Get Product Volume
        $this->db->select("product_details.*", false)
                ->from("products")
                ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
                ->where("products.product_id", $product_id)
                ->where("products.status", 1)
                ->order_by("products.date", "desc");
        $get_data_volume = $this->db->get()
                ->result_array();

        if(!empty($get_data_volume)){ 
            return $get_data_volume;
        } 
    }
    
    function product_feature_img($feature_img) {
//        if($feature_img){
//            $arr[0] = $this->m_tools->image_url_product( $feature_img );    
//            $arr[1] = $this->m_tools->image_url_product( $feature_img ,'thumb');    
//        }
//        else {
//            $arr[0] = '';
//            $arr[1] = '';
//        }
	
	if(!empty($feature_img)){
	    $statss = $this->m_tools->get_http_response_code($this->m_tools->image_url_product($feature_img));
	    
	    if($statss == '200') {
		$arr[0] = $this->m_tools->image_url_product($feature_img);   
		$arr[1] = $this->m_tools->image_url_product($feature_img,'thumb'); 
	    }
	    else if($statss == '404') {
		// if image found
		$arr[0] = PLACEHOLDER;   
		$arr[1] = PLACEHOLDER;  
	    }
	    else {
		$arr[0] = PLACEHOLDER;
		$arr[1] = PLACEHOLDER;
	    }		    
	}
	else {
	    $arr[0] = PLACEHOLDER;
	    $arr[1] = PLACEHOLDER;
	}
		
        return $arr;
    }
    
    function product_favourite($product_id, $user_id) {
        $favourite = $this->db->select("user_id, product_id, status")
                ->where('product_id', $product_id)
                ->where('user_id', $user_id)
                ->get('product_favourite')->row_array();

        if(!empty($favourite)){
            return $favourite["status"];
        }
        else {
            return 0;
        }
    }
}

<?php

class M_loyalty_points extends CI_Model {
    
    public function loyalty_club_list($offset) {         
        
        $prod_list = $this->db->select("products.*, product_details.volume_id, category_mst.category_name, brand_mst.brand_name", false)
                ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1')
                ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1')
                ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1')
                ->where("products.status", 1)
                ->where("products.in_loyalty_club", 1)
                ->group_by('products.product_id')
		->limit(LIMIT)
		->offset($offset)
                ->order_by("products.date", "desc")
		->get("products")
                ->result_array(); 
        
        if(!empty($prod_list)){
        
            foreach($prod_list as $key => $value){  
                
                if($value["seller_id"] != 0) {
                    //seller info
                    $seller = $this->db->select("seller_id, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender", false)
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

                    $prod_list[$key]['seller'] = $seller2;
                }
                else {
                    $prod_list[$key]['seller'] = array(
                        'seller_name' => "Admin"
                    );
                }
                
                //Get Product Volume
                $this->db->select("product_details.*, concat(volume_mst.volumne_value,' ',(select volume_type from volume_type where volume_type_id = volume_mst.type)) as volumes", false)
                        ->from("products")
                        ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
                        ->join('volume_mst', 'product_details.volume_id=volume_mst.volume_id and volume_mst.status=1')
                        ->where("products.product_id", $value['product_id'])
                        ->where("products.status", 1)
                        ->order_by("products.date", "desc");
                $get_data_volume = $this->db->get()
                        ->result_array();

                //print_r($get_data_volume); exit;
                if(!empty($get_data_volume)){ 

                    $prod_list[$key]["price"] = number_format($get_data_volume[0]["normal_sell_price"], 2);
		    $prod_list[$key]["actual_price"] = number_format($get_data_volume[0]["actual_price"], 2);

                    foreach($get_data_volume as $vkey => $vvalue){ 
                        $get_data_volume[$vkey]["actual_price"] = $vvalue["actual_price"];
                    }                    
                    $prod_list[$key]['isvolume'] = true;
                }
                else {
                    $prod_list[$key]['isvolume'] = false;
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

                $prod_list[$key]['return_policy'] = $get_data_policy;
                $prod_list[$key]['volume'] = $get_data_volume;

                if($value['feature_img']){                
                    $prod_list[$key]['feature_img'] = $this->m_tools->image_url( $value['feature_img'],'', 'product');   
                    $prod_list[$key]['feature_img_thumb'] = $this->m_tools->image_url( $value['feature_img'],'thumb', 'product');     
                }
                else {
                    $prod_list[$key]['feature_img'] = '';  
                    $prod_list[$key]['feature_img_thumb'] = '';  
                }
		
		if($this->session->userdata('loged_in')) { 
                    //get favourite
                    $favourite = $this->db->select("user_id, product_id, status")
                            ->where('product_id', $value['product_id'])
                            ->where('user_id', $this->session->userdata('user_id'))
                            ->get('product_favourite')->row_array();

                    if(!empty($favourite)){
                        $prod_list[$key]['is_favourite'] = $favourite["status"];
                    }
                    else {
                        $prod_list[$key]['is_favourite'] = 0;
                    }
                }
                else {
                    $prod_list[$key]['is_favourite'] = 0;
                }

            }
        }
        
        if (!empty($prod_list)) {
            $count_prod = $this->db->select("products.*, product_details.volume_id, category_mst.category_name, brand_mst.brand_name", false)
		    ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1')
		    ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1')
		    ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1')
		    ->where("products.status", 1)
		    ->where("products.in_loyalty_club", 1)
		    ->group_by('products.product_id')
		    ->order_by("products.date", "desc")
		    ->get("products")
		    ->num_rows();
	    
	    $offset1 = LIMIT + $offset;
	    if($count_prod > $offset1) {
		$ret[0] = 1;
	    }
	    else {
		$ret[0] = 0;
	    }
	    $ret[1] = $offset1;
	    $ret[2] = $prod_list;
	    return $ret;       
        }        
        else {
            return false;
        }
    }

    public function vip_club_list($offset) {      
        
        $prod_list = $this->db->select("products.*, product_details.volume_id, category_mst.category_name, brand_mst.brand_name", false)
                ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1')
                ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1')
                ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1')
                ->where("products.status", 1)
                ->where("products.in_vip_club", 1)
                ->group_by('products.product_id')
                ->limit(LIMIT)
		->offset($offset)
                ->order_by("products.date", "desc")
		->get("products")
                ->result_array();  
        
        if(!empty($prod_list)){
        
            foreach($prod_list as $key => $value){  
                
                if($value["seller_id"] != 0) {
                    //seller info
                    $seller = $this->db->select("seller_id, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender", false)
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

                    $prod_list[$key]['seller'] = $seller2;
                }
                else {
                    $prod_list[$key]['seller'] = array(
                        'seller_name' => "Admin"
                    );
                }
                
                //Get Product Volume
                $this->db->select("product_details.*, concat(volume_mst.volumne_value,' ',(select volume_type from volume_type where volume_type_id = volume_mst.type)) as volumes", false)
                        ->from("products")
                        ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
                        ->join('volume_mst', 'product_details.volume_id=volume_mst.volume_id and volume_mst.status=1')
                        ->where("products.product_id", $value['product_id'])
                        ->where("products.status", 1)
                        ->order_by("products.date", "desc");
                $get_data_volume = $this->db->get()
                        ->result_array();

                //print_r($get_data_volume); exit;
                if(!empty($get_data_volume)){ 

                    $prod_list[$key]["price"] = number_format($get_data_volume[0]["normal_sell_price"], 2);
		    $prod_list[$key]["actual_price"] = number_format($get_data_volume[0]["actual_price"], 2);

                    foreach($get_data_volume as $vkey => $vvalue){ 
                        $get_data_volume[$vkey]["actual_price"] = $vvalue["actual_price"];
                    }                    
                    $prod_list[$key]['isvolume'] = true;
                }
                else {
                    $prod_list[$key]['isvolume'] = false;
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

                $prod_list[$key]['return_policy'] = $get_data_policy;
                $prod_list[$key]['volume'] = $get_data_volume;

                if($value['feature_img']){                
                    $prod_list[$key]['feature_img'] = $this->m_tools->image_url( $value['feature_img'],'', 'product');   
                    $prod_list[$key]['feature_img_thumb'] = $this->m_tools->image_url( $value['feature_img'],'thumb', 'product');     
                }
                else {
                    $prod_list[$key]['feature_img'] = '';  
                    $prod_list[$key]['feature_img_thumb'] = '';  
                }
		
		if($this->session->userdata('loged_in')) { 
                    //get favourite
                    $favourite = $this->db->select("user_id, product_id, status")
                            ->where('product_id', $value['product_id'])
                            ->where('user_id', $this->session->userdata('user_id'))
                            ->get('product_favourite')->row_array();

                    if(!empty($favourite)){
                        $prod_list[$key]['is_favourite'] = $favourite["status"];
                    }
                    else {
                        $prod_list[$key]['is_favourite'] = 0;
                    }
                }
                else {
                    $prod_list[$key]['is_favourite'] = 0;
                }

            }
        }
        
        if (!empty($prod_list)) {
            $count_prod = $this->db->select("products.*, product_details.volume_id, category_mst.category_name, brand_mst.brand_name", false)
		    ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1')
		    ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1')
		    ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1')
		    ->where("products.status", 1)
		    ->where("products.in_vip_club", 1)
		    ->group_by('products.product_id')
		    ->order_by("products.date", "desc")
		    ->get("products")
		    ->num_rows();
	    
	    $offset1 = LIMIT + $offset;
	    if($count_prod > $offset1) {
		$ret[0] = 1;
	    }
	    else {
		$ret[0] = 0;
	    }
	    $ret[1] = $offset1;
	    $ret[2] = $prod_list;
	    return $ret;          
        }        
        else {
            return false;
        }
    }
    
    public function get_loyalty_point() {       
        $user_id = $this->session->userdata("user_id");
        //get user by id
        $userdata = $this->m_tools->get_user_by_id($user_id);
        
        $vip_points = $this->db->where('key', 'vip_loyalty_points')->get('setting')->row_array();
        
        //check whether is member in vip club or not
        if($userdata["loyalty_point"] >= $vip_points["value"]){
            $is_member = 1;  
            $point_left = 0;
        } else {
            $is_member = 2;            
            $point_left = $vip_points["value"] - $userdata["loyalty_point"];
        }
        
        $return = array(
            'loyalty_point' => $userdata["loyalty_point"],
            'is_valid_member' => $is_member,
            'point_left' => $point_left
        );
        
        return $return;
    }
}

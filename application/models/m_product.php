<?php

class M_product extends CI_Model{
    
    function get_products_list($offset, $post = []){
        $product_array = array();
        $new_array = array();
        
        //with abv parameter
        if( (isset($post["abv_max"]) && $post["abv_max"] != NULL) && (isset($post["abv_min"]) && $post["abv_min"] != NULL) ) {
            //$abv = ' and products.abv_percent IN ('.$post["abv"].')';             
	    $abv = ' and products.abv_percent BETWEEN '.$post["abv_min"].' AND '.$post["abv_max"].'';   
        }
        else {
            $abv = '';                        
        }
        
        //with country parameter
        if(isset($post["country"]) && $post["country"] != NULL ) {
            $country = ' and products.country_id LIKE "'.$post["country"].'%"';             
        }
        else {
            $country = '';                        
        }
        
        //with category parameter
        if(isset($post["category"]) && $post["category"] != NULL ) {
            $categoryid = ' and products.category_id = '.$post["category"].'';             
        }
        else {
            $categoryid = '';                        
        }
        
        //with min & max amount parameter
        if( (isset($post["min_amt"]) && $post["min_amt"] != NULL) && (isset($post["max_amt"]) && $post["max_amt"] != NULL) ) {
            $price = ' and product_details.normal_sell_price BETWEEN '.$post["min_amt"].' AND '.$post["max_amt"].'';             
        }
        else {
            $price = '';                        
        }
        
        //with sub-category parameter
        if(isset($post["subcategory"]) && $post["subcategory"] != NULL ) {
            $categoryid = ' and products.category_id = '.$post["subcategory"].'';             
        }
        else {
            $categoryid = '';                        
        }
        
        //with brand parameter
        if(isset($post["brand"]) && $post["brand"] != NULL ) {
            $brandid = 'and (';
            $i=1;
            $br_arr = explode(',',$post["brand"]);
            
            foreach($br_arr as $value){
                $brandid .= ' products.brand_id = '.$value.''; 
                if($i < count($br_arr)){
                    $brandid .= ' or ';
                }
                $i++;
            }    
            $brandid .= ')';
        }
        else {
            $brandid = '';                        
        }
        
        //with volume parameter
        if( (isset($post["volume_max"]) && $post["volume_max"] != NULL) && (isset($post["volume_min"]) && $post["volume_min"] != NULL) && (isset($post["volume_type"]) && $post["volume_type"] != NULL) ) {
	    
	    if(isset($post["brand"]) && $post["brand"] != NULL ) {
		$brandids = ' AND brand_id IN ('.$post["brand"].')';  
	    }
	    else {
		$brandids = '';                        
	    }
	    
	    $where_volume_mst = 'volumne_value BETWEEN '.$post["volume_min"].' AND '.$post["volume_max"].' and type = '.$post["volume_type"].''.$brandids.'';
	    
	    $get_volume = $this->db->select("volume_id", false)
		    ->where($where_volume_mst)
		    ->get("volume_mst")->result_array();
	    
	    if(!empty($get_volume)) {	
		$get_volume_arr = array_column($get_volume, "volume_id");
		$volumes = implode(",", $get_volume_arr);
		
		$volumeid = ' and product_details.volume_id IN ('.$volumes.')';   
	    }
	    else {
		$volumeid = '';
	    }
        }
        else {
            $volumeid = '';                        
        }
        
        $prod_where = "products.status = 1 ".$abv." ".$country."";
        
        if(!empty($post["latitude"]) || !empty($post["longitude"])) {
	    $latitude = number_format($post["latitude"], 6);
	    $longitude = number_format($post["longitude"], 6);
	    
            //find nearest seller
            $nearest_seller = $this->db->select('seller_id, seller_name, company_name, email, contact_no, address, latitude, longitude, case when gender=1 then "Male" when gender=2 then "Female" end as gender, get_distance_metres(latitude, longitude, '.$latitude.' , '.$longitude.') as distance', false)        
                                ->where('status', 1)
                                ->where('is_admin_verified', 1)
				->order_by("distance", "asc")
                                ->get('seller')->result_array();
	    
            if(!empty($nearest_seller)) {
		$seller_array = array();
                foreach($nearest_seller as $value1){
                    //calculate distance
                    $distance = round(($value1["distance"] / 1609.34), 2);
                   
		    $mile_limit = $this->get_mile_limit();
                    
                    //seller within 10 km
                    if($distance <= $mile_limit and $distance >= 0){
			
			//Sort by 1 = Chepeast, 2 = Fastest, 3 = Best Match
			if(isset($post["sort_by"]) && $post["sort_by"] == 1 ) {
			    $sort_by = 'min_amount asc, ';
			}
			else if(isset($post["sort_by"]) && $post["sort_by"] == 2 ) {
			    $sort_by = 'distance asc, ';
			}
			else if(isset($post["sort_by"]) && $post["sort_by"] == 3 ) {
			    $sort_by = 'min_amount asc, distance asc, ';
			}
			else {
			    $sort_by = '';
			}
			
			if(isset($post["top_pick"]) && $post["top_pick"] == 1 ) {
			    $prod_order = ''.$sort_by.'products.top_pick desc, products.date desc';
			}
			else {
			    $prod_order = ''.$sort_by.'products.date desc';
			}
			
			//get seller
			array_push($seller_array, $value1["seller_id"]);
			
                    }
                }
		
		$seller_array1 = implode(",", $seller_array);
		$prod_where = "products.seller_id IN (".$seller_array1.")";
		
		$this->db->select("products.*, product_details.volume_id, category_mst.category_name, brand_mst.brand_name, COALESCE(MIN(product_details.actual_price), 0) as min_amount, get_distance_metres(latitude, longitude, ".$latitude." , ".$longitude.") as distance", false);
	    }
	    else {
		return 'error';
	    }
        }
        else {
	    //Sort by 1 = Chepeast, 2 = Fastest, 3 = Bestest
	    if(isset($post["sort_by"]) && $post["sort_by"] == 1 ) {
		$sort_by = 'min_amount asc, ';
	    }
	    else {
		$sort_by = '';
	    }
	
	    if(isset($post["top_pick"]) && $post["top_pick"] == 1 ) {
		$prod_order = 'products.top_pick desc, products.date desc';
	    }
	    else {
		$prod_order = ''.$sort_by.'products.date desc';
	    }
			
            $this->db->select("products.*, product_details.volume_id, category_mst.category_name, brand_mst.brand_name, COALESCE(MIN(product_details.actual_price), 0) as min_amount", false);
        }
	
	
	$prod_list = $this->db->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1 '.$price.' '.$volumeid.' ')
		->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1 '.$categoryid.'')
		->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1 '.$brandid.'')
		->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
		->where($prod_where)
		->limit(LIMIT)
		->offset($offset)
		->group_by('products.product_id')
		->order_by($prod_order)                                
		->get("products")->result_array();
	
	if(!empty($prod_list)){
	    foreach($prod_list as $key => $value){ 

		$msg = str_replace(PHP_EOL,"@/@", $prod_list[$key]["description"]);
		$prod_list[$key]["description"] = json_decode('"'.$msg.'"');
		$prod_list[$key]["description"] = str_replace("@/@",PHP_EOL, $prod_list[$key]["description"]);

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

		//get favourite
		if($this->session->userdata('loged_in')) {
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
		    $prod_list[$key]['feature_img'] = $this->m_tools->image_url_product( $value['feature_img']);   
		    $prod_list[$key]['feature_img_thumb'] = $this->m_tools->image_url_product( $value['feature_img'],'thumb');     
		}
		else {
		    $prod_list[$key]['feature_img'] = '';  
		    $prod_list[$key]['feature_img_thumb'] = '';  
		}

	    }
	}

	if (!empty($prod_list)) {
	    $count_prod = $this->db->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1 '.$price.' '.$volumeid.' ')
		->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1 '.$categoryid.'')
		->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1 '.$brandid.'')
		->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
		->where($prod_where)
		->group_by('products.product_id')
		->order_by($prod_order)                                
		->get("products")->num_rows();
	    
	    $product_name = array_column($prod_list, 'product_name');
	    array_multisort($product_name, SORT_ASC, $prod_list);
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
	    return 'error';
	}
    }
    
    public function get_product_by_id($product_id){
        $product = $this->db
                ->where('product_id', $product_id)
                ->get('products')
                ->row_array();
        
        if(!empty($product)){
	    
	    $msg = str_replace(PHP_EOL,"@/@", $product["description"]);
	    $product["description"] = json_decode('"'.$msg.'"');
	    $product["description"] = str_replace("@/@",PHP_EOL, $product["description"]);
	    		
            return $product;
        }
    }
    
    function similar_products($product_id, $offset, $limit){        
        if(isset($product_id) && $product_id != NULL){
            
            //current product        
            $current_product = $this->get_product_by_id($product_id);
            
            if($current_product){
                $where ="products.product_id != ".$product_id." and (products.category_id IN (".$current_product["category_id"].") OR products.brand_id IN (".$current_product["brand_id"].")) AND products.status=1";
                
		$prod_list = $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", false)
			->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1', 'left')
			->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1', 'left')
			->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
			->where($where)
			->group_by('products.product_id')
			->order_by("products.date", "desc")
			->limit($limit)
			->offset($offset)
			->get("products")
                        ->result_array();   

                if(!empty($prod_list)){
                    foreach($prod_list as $key => $value){   
			
			$msg = str_replace(PHP_EOL,"@/@", $prod_list[$key]["description"]);
			$prod_list[$key]["description"] = json_decode('"'.$msg.'"');
			$prod_list[$key]["description"] = str_replace("@/@",PHP_EOL, $prod_list[$key]["description"]);

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

                        if(!empty($get_data_volume)){ 

                            $prod_list[$key]["price"] = number_format($get_data_volume[0]["normal_sell_price"], 2);
			    $prod_list[$key]["actual_price"] = number_format($get_data_volume[0]["actual_price"], 2);
			    foreach($get_data_volume as $vkey => $vvalue){ 
				$get_data_volume[$vkey]["actual_price"] = $vvalue["actual_price"];
			    }   

                            $prod_list[$key]['isvolume'] = true;
                        }
                        else {
                            $prod_list[$key]["price"] = 0.00;
			    $prod_list[$key]["actual_price"] = 0.00;
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

                        // images
//                        if($value['feature_img']){                 
//                            $prod_list[$key]['feature_img'] = $this->m_tools->image_url_product( $value['feature_img']);   
//                            $prod_list[$key]['feature_img_thumb'] = $this->m_tools->image_url_product( $value['feature_img'],'thumb');     
//                        }
//                        else {
//                            $prod_list[$key]['feature_img'] = '';  
//                            $prod_list[$key]['feature_img_thumb'] = '';  
//                        }   
			if(!empty($value['feature_img'])){
			    $statss = $this->m_tools->get_http_response_code($this->m_tools->image_url_product( $value['feature_img']));		    
			    if($statss == '200') {
				$prod_list[$key]['feature_img'] = $this->m_tools->image_url_product( $value['feature_img']);   
				$prod_list[$key]['feature_img_thumb'] = $this->m_tools->image_url_product( $value['feature_img'],'thumb');  
			    }
			    else if($statss == '404') {
				// if image found
				$prod_list[$key]['feature_img'] = PLACEHOLDER;   
				$prod_list[$key]['feature_img_thumb'] = PLACEHOLDER;  
			    }
			    else {
				$prod_list[$key]['feature_img'] = PLACEHOLDER;
				$prod_list[$key]['feature_img_thumb'] = PLACEHOLDER;
			    }		    
			}
			else {
			    $prod_list[$key]['feature_img'] = PLACEHOLDER;
			    $prod_list[$key]['feature_img_thumb'] = PLACEHOLDER;
			}
                    }
                }

                if (!empty($prod_list)) {
		    $count_prod = $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", false)
			->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1', 'left')
			->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1', 'left')
			->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
			->where($where)
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
                    return 'error';
                }                
            }
            else {
                return 'error';
            }
        }        
        else {
            return 'error';
        }
    }
    
    function search_product_name($post=[]){  

	$product = $this->db->select("products.product_name")
		->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
		->where("products.status = 1 and products.product_name LIKE '%".$post["chr"]."%'")
		->order_by("products.product_name", "asc")
		->get("products")->result_array();  	
	$p = array_column($product, "product_name");
	
	$category = $this->db->select("category_mst.category_name")
		->where("category_mst.status = 1 and category_mst.category_name LIKE '%".$post["chr"]."%'")
		->order_by("category_mst.category_name", "asc")
		->get("category_mst")->result_array();  
	$c = array_column($category, "category_name");
	
	$brand = $this->db->select("brand_mst.brand_name")
		->where("brand_mst.status = 1 and brand_mst.brand_name LIKE '%".$post["chr"]."%'")
		->order_by("brand_mst.brand_name", "asc")
		->get("brand_mst")->result_array();  
	$b = array_column($brand, "brand_name");
	
	$prod_list = array_merge($p, $c, $b);       

	if (!empty($prod_list)) {
	    return $prod_list;            
	}        
	else {
	    return false;
	}
    }
    
    function search_product($post=[]){  
        if(isset($post["chr"]) && $post["chr"] != NULL ) {
	    $where = 'products.status = 1 AND (category_mst.category_name LIKE "%'.$post["chr"].'%" OR brand_mst.brand_name LIKE "%'.$post["chr"].'%" OR products.product_name LIKE "%'.$post["chr"].'%")';             
	}
	else {
	    $where = "products.status = 1 ";                       
	}

        $prod_list = $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", FALSE)
	    ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1', 'left')
	    ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1', 'left')
	    ->join('seller', 'products.seller_id=seller.seller_id')
            ->where($where)
	    ->limit(LIMIT)
	    ->offset($post["offset"])
	    ->order_by("products.product_name", "asc")
	    ->get('products')
	    ->result_array();  

	if (!empty($prod_list)) {
	    $count_prod = $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", FALSE)
		->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1', 'left')
		->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1', 'left')
		->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
		->where($where)
		->order_by("products.product_name", "asc")
		->get('products')
                ->num_rows();
	    
	    foreach($prod_list as $key => $value){   

		if($value["seller_id"] != 0) {
		    //seller info
		    $seller = $this->db->select("seller_id, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender", false)
			    ->where('seller_id', $value['seller_id'])
			    //->where('status', 1)
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

		//get favourite
		$favourite = $this->db->select("user_id, product_id, status")
			->where('product_id', $value['product_id'])
			->where('user_id', $post['user_id'])
			->get('product_favourite')->row_array();


		if(!empty($favourite)){
		    $prod_list[$key]['is_favourite'] = $favourite["status"];
		}
		else {
		    $prod_list[$key]['is_favourite'] = 0;
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

		if(!empty($get_data_volume)){            
		    $prod_list[$key]["price"] = number_format($get_data_volume[0]["normal_sell_price"], 2);
		    $prod_list[$key]["actual_price"] = number_format($get_data_volume[0]["actual_price"], 2);
		    
		    foreach($get_data_volume as $vkey => $vvalue){ 
			$get_data_volume[$vkey]["actual_price"] = $vvalue["actual_price"];
		    }  

		    $prod_list[$key]['isvolume'] = true;
		}
		else {
		    $prod_list[$key]["price"] = 0.00;
		    $prod_list[$key]["actual_price"] = 0.00;
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

		if(!empty($value['feature_img'])){
		    $statss = $this->m_tools->get_http_response_code($this->m_tools->image_url_product( $value['feature_img']));		    
		    if($statss == '200') {
			$prod_list[$key]['feature_img'] = $this->m_tools->image_url_product( $value['feature_img']);   
			$prod_list[$key]['feature_img_thumb'] = $this->m_tools->image_url_product( $value['feature_img'],'thumb');  
		    }
		    else if($statss == '404') {
			// if image found
			$prod_list[$key]['feature_img'] = PLACEHOLDER;   
			$prod_list[$key]['feature_img_thumb'] = PLACEHOLDER;  
		    }
		    else {
			$prod_list[$key]['feature_img'] = PLACEHOLDER;
			$prod_list[$key]['feature_img_thumb'] = PLACEHOLDER;
		    }		    
		}
		else {
		    $prod_list[$key]['feature_img'] = PLACEHOLDER;
		    $prod_list[$key]['feature_img_thumb'] = PLACEHOLDER;
		}

	    }
	    
	    $offset1 = LIMIT + $post["offset"];
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
	    return 'error';
	}
    }
 /*   
//    public function search_product($post=[]){ 
//       if(isset($post["chr"]) && $post["chr"] != NULL ) {
//            $where = 'products.status = 1 AND (category_mst.category_name LIKE "'.$post["chr"].'%" OR brand_mst.brand_name LIKE "'.$post["chr"].'%" OR products.product_name LIKE "'.$post["chr"].'%")';             
//        }
//        else {
//            $where = "products.status = 1 ";                       
//        }
//
//       $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", FALSE)
//            ->from('products')
//            ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1', 'left')
//            ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1', 'left')
//            ->where($where)
//            ->order_by("products.date", "desc");
//        $prod_list = $this->db->get()
//                ->result_array();  
//
//        if (!empty($prod_list)) {
//
//            foreach($prod_list as $key => $value){   
//		
//		$msg = str_replace(PHP_EOL,"@/@", $prod_list[$key]["description"]);
//		$prod_list[$key]["description"] = json_decode('"'.$msg.'"');
//		$prod_list[$key]["description"] = str_replace("@/@",PHP_EOL, $prod_list[$key]["description"]);
//
//                if($value["seller_id"] != 0) {
//                    //seller info
//                    $seller = $this->db->select("seller_id, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender", false)
//                            ->where('seller_id', $value['seller_id'])
//                            ->where('status', 1)
//                            ->where('is_admin_verified', 1)
//                            ->get('seller')->row_array();
//
//                    $seller2 = array_map(function($val) {
//                        if(is_null($val)) {
//                            $val = "";
//                        }
//                        return $val;
//                    }, $seller);
//
//                    $prod_list[$key]['seller'] = $seller2;
//                }
//                else {
//                    $prod_list[$key]['seller'] = array(
//                        'seller_name' => "Admin"
//                    );
//                }
//                
//                if($this->session->userdata('loged_in')) { 
//                    //get favourite
//                    $favourite = $this->db->select("user_id, product_id, status")
//                            ->where('product_id', $value['product_id'])
//                            ->where('user_id', $this->session->userdata('user_id'))
//                            ->get('product_favourite')->row_array();
//
//
//                    if(!empty($favourite)){
//                        $prod_list[$key]['is_favourite'] = $favourite["status"];
//                    }
//                    else {
//                        $prod_list[$key]['is_favourite'] = 0;
//                    }
//                }
//                else {
//                    $prod_list[$key]['is_favourite'] = 0;
//                }
//
//                //Get Product Volume
//                $this->db->select("product_details.*, concat(volume_mst.volumne_value,' ',(select volume_type from volume_type where volume_type_id = volume_mst.type))", false)
//                        ->from("products")
//                        ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
//                        ->join('volume_mst', 'product_details.volume_id=volume_mst.volume_id and volume_mst.status=1')
//                        ->where("products.product_id", $value['product_id'])
//                        ->where("products.status", 1)
//                        ->order_by("products.date", "desc");
//                $get_data_volume = $this->db->get()
//                        ->result_array();
//
//                if(!empty($get_data_volume)){            
//                    foreach($get_data_volume as $vkey => $vvalue){ 
//                        $get_data_volume[$vkey]["actual_price"] =  $vvalue["actual_price"];
//                    }
//
//                    //get price for volume
//                    $prod_list[$key]["price"] = $get_data_volume[0]["actual_price"];
//                    $prod_list[$key]['isvolume'] = true;
//                }
//                else {
//                    $prod_list[$key]['isvolume'] = false;
//                }
//
//                //Get Product Return policy
//                $this->db->select("product_return_policy.*", false)
//                        ->from("products")
//                        ->join('product_return_policy', 'products.product_id = product_return_policy.product_id')
//                        ->where("products.product_id", $value['product_id'])
//                        ->where("products.have_return_policy", 1)
//                        ->where("products.status", 1)
//                        ->order_by("products.date", "desc");
//                $get_data_policy = $this->db->get()
//                            ->row_array();
//
//                if(!empty($get_data_policy)){
//                    $get_data_policy["description"] = html_entity_decode(strip_slashes(strip_tags($get_data_policy["description"], '<strong>')));
//                    $get_data_policy["status"] = true;
//                } else {
//                    $get_data_policy["status"] = false;
//                }
//
//                $prod_list[$key]['return_policy'] = $get_data_policy;
//                $prod_list[$key]['volume'] = $get_data_volume;
//
//                if(!empty($value['feature_img'])){
//		    $statss = $this->m_tools->get_http_response_code($this->m_tools->image_url_product( $value['feature_img']));		    
//		    if($statss == '200') {
//			$prod_list[$key]['feature_img'] = $this->m_tools->image_url_product( $value['feature_img']);   
//			$prod_list[$key]['feature_img_thumb'] = $this->m_tools->image_url_product( $value['feature_img'],'thumb');  
//		    }
//		    else if($statss == '404') {
//			// if image found
//			$prod_list[$key]['feature_img'] = PLACEHOLDER;   
//			$prod_list[$key]['feature_img_thumb'] = PLACEHOLDER;  
//		    }
//		    else {
//			$prod_list[$key]['feature_img'] = PLACEHOLDER;
//			$prod_list[$key]['feature_img_thumb'] = PLACEHOLDER;
//		    }		    
//                }
//                else {
//                    $prod_list[$key]['feature_img'] = PLACEHOLDER;
//                    $prod_list[$key]['feature_img_thumb'] = PLACEHOLDER;
//                }
//
//            }
//
//            return $prod_list;            
//        }        
//        else {
//            return false;
//        }
//    }    
   */
    
    public function update_like_unlike($post=[]) {
        $user_id = $this->session->userdata("user_id");
        $is_fav = $post["fav_val"];
        
        $exists = $this->db->select("*")
                ->where('product_id', $post['product_id'])
                ->where('user_id', $user_id)
                ->get('product_favourite')->row_array();
        
        if(!empty($exists)) {
            $in_arr = array(
                'user_id' => $user_id,
                'product_id' => $post["product_id"],
                'status' => $is_fav,
            );
            $this->db->set($in_arr)
                ->where('product_id', $post['product_id'])
                ->where('user_id', $user_id)
                ->update('product_favourite');
        }
        else {
            $in_arr = array(
                'user_id' => $user_id,
                'product_id' => $post["product_id"],
                'status' => $is_fav,
            );
            $this->db->insert('product_favourite', $in_arr);
        }            
        
        if($is_fav == 1){
            return 1;
        }
        else if($is_fav == 0){ 
            return 2;
        }

    }
    
    public function get_product_details_by_id($product_id) {
        $pid = base64_decode($product_id);
        if(isset($pid) && $pid != NULL){
            //Get Product Details
            $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", false)
                    ->from("products")
                    ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1')
                    ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1')
                    ->where("products.status", 1)
                    ->where("products.product_id", $pid)
                    ->group_by('products.product_id')
                    ->order_by("products.date", "desc");
            $get_data = $this->db->get()
                    ->row_array();             
            
            if (!empty($get_data)) {
		
		$msg = str_replace(PHP_EOL,"@/@", $get_data["description"]);
		$get_data["description"] = json_decode('"'.$msg.'"');
		$get_data["description"] = str_replace("@/@",PHP_EOL, $get_data["description"]);
			
                if($get_data["seller_id"] != 0) {
                    //seller info
                    $seller = $this->db->select("seller_id, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender", false)
                            ->where('seller_id', $get_data['seller_id'])
                           // ->where('status', 1)
                            ->where('is_admin_verified', 1)
                            ->get('seller')->row_array();

                    $seller2 = array_map(function($val) {
                        if(is_null($val)) {
                            $val = "";
                        }
                        return $val;
                    }, $seller);

                    $get_data['seller'] = $seller2;
                }
                else {
                    $get_data['seller'] = array(
                        'seller_name' => "Admin"
                    );
                }
                
                //seller rating
                $post["seller_id"] = $get_data['seller_id'];
                $get_data['seller_rating'] = $this->get_seller_rating($post);
                
                //Get Product Volume
                $this->db->select("product_details.*, volume_mst.volumne_value, concat(volume_mst.volumne_value,' ',(select volume_type from volume_type where volume_type_id = volume_mst.type)) as volumes", false)
                        ->from("products")
                        ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
                        ->join('volume_mst', 'product_details.volume_id=volume_mst.volume_id and volume_mst.status=1')
                        ->where("products.product_id", $pid)
                        ->where("products.status", 1)
                        ->order_by("products.date", "desc");
                $get_data_volume = $this->db->get()
                        ->result_array();
		
		//print_r($get_data_volume); exit;
                
                if(!empty($get_data_volume)){                 
                    $get_data["price"] = number_format($get_data_volume[0]["normal_sell_price"], 2);
		    $get_data["actual_price"] = number_format($get_data_volume[0]["actual_price"], 2);

                    foreach($get_data_volume as $vkey => $vvalue){ 
                        $get_data_volume[$vkey]["actual_price"] =  $vvalue["actual_price"];
                    }
                    
                    $get_data['isvolume'] = true;
                }
                else {
		    $get_data["price"] = 0.00;
		    $get_data["actual_price"] = 0.00;
                    $get_data['isvolume'] = false;
                }

                //Get Product Return policy
                $this->db->select("product_return_policy.*", false)
                        ->from("products")
                        ->join('product_return_policy', 'products.product_id = product_return_policy.product_id')
                        ->where("products.product_id", $pid)
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
                
                //Get Product Gallery
                $this->db->select("product_images.image_name", false)
                        ->from("products")
                        ->join('product_images', 'products.product_id = product_images.product_id and product_images.status=1', 'left')
                        ->where("products.product_id", $pid)
                        ->where("products.status", 1)
                        ->order_by("products.date", "desc");
                $get_data_images = $this->db->get()
                        ->result_array();
                
                if(!empty($get_data_images)){  

                    foreach($get_data_images as $key => $images){            
                        //$get_data_images[$key]["image_name"] = $this->m_tools->image_url_product( $images["image_name"],'');
			if(!empty($images["image_name"])){
			    $statss = $this->m_tools->get_http_response_code($this->m_tools->image_url_product($images["image_name"]));		    
			    if($statss == '200') {
				$get_data_images[$key]['image_name'] = $this->m_tools->image_url_product($images["image_name"]);  		
			    }
			    else if($statss == '404') {
				// if image found
				$get_data_images[$key]['image_name'] = PLACEHOLDER;  
			    }
			    else {
				$get_data_images[$key]['image_name'] = PLACEHOLDER;
			    }		    
			}
			else {
			    $get_data_images[$key]['image_name'] = PLACEHOLDER;
			}
                    } 
                }
                
//                $userdata = $this->get_user_by_id($post["user_id"]);
//        
//                if($userdata["is_admin_verified"] == 1) {
//                    $get_data['is_admin_verified'] = true;
//                }
//                else {
//                    $get_data['is_admin_verified'] = false;
//                }
                
                $image_path = $get_data['feature_img'];
                $get_data['volume_id'] = $get_data_volume;
                $get_data['return_policy'] = $get_data_policy;
                $get_data['gallery_img'] = $get_data_images;
		
		if(!empty($image_path)){
		    $statss = $this->m_tools->get_http_response_code($this->m_tools->image_url_product($image_path));		    
		    if($statss == '200') {
			$get_data['feature_img'] = $this->m_tools->image_url_product($image_path);   
			$get_data['feature_img_thumb'] = $this->m_tools->image_url_product($image_path,'thumb');  
		    }
		    else if($statss == '404') {
			// if image found
			$get_data['feature_img'] = PLACEHOLDER;   
			$get_data['feature_img_thumb'] = PLACEHOLDER;  
		    }
		    else {
			$get_data['feature_img'] = PLACEHOLDER;
			$get_data['feature_img_thumb'] = PLACEHOLDER;
		    }		    
		}
		else {
		    $get_data['feature_img'] = PLACEHOLDER;
		    $get_data['feature_img_thumb'] = PLACEHOLDER;
		}
			
//                $get_data['feature_img'] = $this->m_tools->image_url_product( $image_path);  
//                $get_data['feature_img_thumb'] = $this->m_tools->image_url_product( $image_path,'thumb'); 

//                //get loyalty point of user
//                $userdata = $this->get_user_by_id($post["user_id"]);  
//                if($userdata["loyalty_point"] > 0) {
//                    $get_data['loyalty_eligible'] = true;
//                } else {
//                    $get_data['loyalty_eligible'] = false;
//                }
//                
//                //get points allowed to become
//                $vip_eligible_points = $this->db->select("*")->where('key', 'vip_loyalty_points')->get('setting')->row_array();
//                if($userdata["is_vip_club_member"] === 1 && $userdata["loyalty_point"] > $vip_eligible_points["value"]["vip_loyalty_points"]) {
//                    $get_data['vip_eligible'] = true;
//                } else {
//                    $get_data['vip_eligible'] = false;
//                }  
                
                //product rating/review
                $average_rating = $this->db->select("AVG(rating) as product_rating")
                        ->where('product_id', $pid)
                        ->get('product_rating')->row_array();
                
                if(!empty($average_rating)) {
                    $get_data['product_rating'] = number_format($average_rating["product_rating"],1);
                }
                else {
                    $get_data['product_rating'] = 0;
                }
                
                //top 2 review
                $reviews = $this->db->select("product_rating.*, concat(user.firstname,' ',user.lastname) as name, profile_image", false)
                        ->join('user', 'user.user_id = product_rating.user_id')
                        ->where('product_rating.product_id', $pid)
                        ->order_by('product_rating.rating', 'desc')
                        ->order_by('product_rating.date', 'desc')
                        ->limit(8)
                        ->get('product_rating')->result_array();
                
                if(!empty($reviews)){            
                    foreach($reviews as $key1 => $value1) {                        
                        
                        $value2 = array_map(function($val) {
                            if(is_null($val)) {
                                $val = "";
                            }
                            return $val;
                        }, $value1);
                        $reviews[$key1] = $value2;  
                        $reviews[$key1]["rating"] = number_format($value2["rating"],1);
                        if(!empty($value2["profile_image"])) {
                            $reviews[$key1]["profile_image"] = $this->m_tools->image_url( $value2["profile_image"],'', 'upload');
                        }
                        else {
                            $reviews[$key1]["profile_image"] = "";
                        }
                    }
                    
                    $get_data['product_review_list'] = $reviews;
                }
                else {
                    $get_data['product_review_list'] = [];
                }
                
                $count_review = $this->db->select("*")
                        ->where('product_id', $pid)
                        ->get('product_rating')->num_rows();
                
                $get_data['total_product_reviews'] = $count_review;
                
                //show review list
                if($count_review > 8){
                    $get_data['show_review_list'] = true;
                }
                else {
                    $get_data['show_review_list'] = false;
                }
                
                if($this->session->userdata('loged_in')) { 
                    //get favourite
                    $favourite = $this->db->select("user_id, product_id, status")
                            ->where('product_id', $pid)
                            ->where('user_id', $this->session->userdata('user_id'))
                            ->get('product_favourite')->row_array();

                    if(!empty($favourite)){
                        $get_data['is_favourite'] = $favourite["status"];
                    }
                    else {
                        $get_data['is_favourite'] = 0;
                    }
                }
                else {
                    $get_data['is_favourite'] = 0;
                }
                
                $get_data = array_map(function($val) {
                    if(is_null($val)) {
                        $val = "";
                    }
                    return $val;
                }, $get_data);
                
                return $get_data;
            } 

        }       
    }
    
    public function get_category_wise_product_list($cid, $offset) {        
        $category_id = base64_decode($cid);
	
        $check = $this->db->select("*")->where("status", 1)->where("category_id", $category_id)->get("category_mst")->row_array();
        
        if($check["parent_id"] == 0 || is_null($check["parent_id"])) {
            $cat_list = $this->db->select("category_id")->where("status", 1)->where("parent_id", $category_id)->get("category_mst")->result_array();
            $category_ids = implode(',', array_column($cat_list, 'category_id'));
        }
        else {
            $category_ids = $category_id;
        }
        
        $top_pick = $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", false)
                ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1 and category_mst.category_id IN ('.$category_ids.')')
                ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1')
                ->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
		->where("products.status", 1)
		->limit(LIMIT)
		->offset($offset)
                ->group_by('products.product_id')
                ->order_by("products.date", "desc")
		->get("products")
                ->result_array();
        
        if (!empty($top_pick)) {  
            foreach($top_pick as $key => $value) { 
		
		$msg = str_replace(PHP_EOL,"@/@", $top_pick[$key]["description"]);
		$top_pick[$key]["description"] = json_decode('"'.$msg.'"');
		$top_pick[$key]["description"] = str_replace("@/@",PHP_EOL, $top_pick[$key]["description"]);
			
                if($value["seller_id"] != 0) {
                    //seller info
                    $seller = $this->db->select("seller_id, seller_name", false)
                            ->where('seller_id', $value['seller_id'])
                            ->where('status', 1)
                            ->where('is_admin_verified', 1)
                            ->get('seller')->row_array();

                    $top_pick[$key]['seller'] = $seller;
                }
                else {
                    $top_pick[$key]['seller'] = array(
                        'seller_name' => "Admin"
                    );
                }
                //Get Product Volume
                $this->db->select("product_details.*", false)
                        ->from("products")
                        ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
                        ->where("products.product_id", $value['product_id'])
                        ->where("products.status", 1);
                $get_data_volume = $this->db->get()
                        ->result_array();

                if(!empty($get_data_volume)){                     
		    $top_pick[$key]["price"] = number_format($get_data_volume[0]["normal_sell_price"], 2);
		    $top_pick[$key]["actual_price"] = number_format($get_data_volume[0]["actual_price"], 2);
		    foreach($get_data_volume as $vkey => $vvalue){ 
			$get_data_volume[$vkey]["actual_price"] = $vvalue["actual_price"];
		    }  
                } 
		else {
		    $top_pick[$key]["price"] = 0.00;
		    $top_pick[$key]["actual_price"] = 0.00;
		}
                
                //feature image
//                if($value['feature_img']){
//                    $top_pick[$key]['feature_img'] = $this->m_tools->image_url_product( $value['feature_img']);    
//                    $top_pick[$key]['feature_img_thumb'] = $this->m_tools->image_url_product( $value['feature_img'] ,'thumb');    
//                }
//                else {
//                    $top_pick[$key]['feature_img'] = '';
//                    $top_pick[$key]['feature_img_thumb'] = '';
//                }
                
		if(!empty($value['feature_img'])){
		    $statss = $this->m_tools->get_http_response_code($this->m_tools->image_url_product($value['feature_img']));		    
		    if($statss == '200') {
			$top_pick[$key]['feature_img'] = $this->m_tools->image_url_product($value['feature_img']);   
			$top_pick[$key]['feature_img_thumb'] = $this->m_tools->image_url_product($value['feature_img'],'thumb');  
		    }
		    else if($statss == '404') {
			// if image found
			$top_pick[$key]['feature_img'] = PLACEHOLDER;   
			$top_pick[$key]['feature_img_thumb'] = PLACEHOLDER;  
		    }
		    else {
			$top_pick[$key]['feature_img'] = PLACEHOLDER;
			$top_pick[$key]['feature_img_thumb'] = PLACEHOLDER;
		    }		    
		}
		else {
		    $top_pick[$key]['feature_img'] = PLACEHOLDER;
		    $top_pick[$key]['feature_img_thumb'] = PLACEHOLDER;
		}
		
                if($this->session->userdata('loged_in')) { 
                    //get favourite
                    $favourite = $this->db->select("user_id, product_id, status")
                            ->where('product_id', $value['product_id'])
                            ->where('user_id', $this->session->userdata('user_id'))
                            ->get('product_favourite')->row_array();

                    if(!empty($favourite)){
                        $top_pick[$key]['is_favourite'] = $favourite["status"];
                    }
                    else {
                        $top_pick[$key]['is_favourite'] = 0;
                    }
                }
                else {
                    $top_pick[$key]['is_favourite'] = 0;
                }
            } 
	    
//	    $product_name = array_column($top_pick, 'products.product_name');
//	    array_multisort($product_name, SORT_ASC, $top_pick);
	    
	    $count_prod = $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", false)
                ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1 and category_mst.category_id IN ('.$category_ids.')')
                ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1')
                ->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
		->where("products.status", 1)
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
	    $ret[2] = $top_pick;
	    return $ret;            
        }        
        else {
            return 'error';
        }
    }  
    
    public function get_favourite_product_list($offset){
        $user_id = $this->session->userdata('user_id');
        $fv_prod = array();
        $favourites = $this->db->select("user_id, product_favourite.product_id")
                ->join('products', 'products.product_id = product_favourite.product_id')
                ->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
		->where('product_favourite.user_id', $user_id)
                ->where('product_favourite.status', 1)
                ->where('products.status', 1)
                ->limit(LIMIT)
		->offset($offset)
		->get('product_favourite')->result_array();
        
        if(!empty($favourites)) {
	    $count_prod = $this->db->select("user_id, product_favourite.product_id")
                ->join('products', 'products.product_id = product_favourite.product_id')
                ->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
		->where('product_favourite.user_id', $user_id)
                ->where('product_favourite.status', 1)
                ->where('products.status', 1)
                ->get('product_favourite')->num_rows();
		    
            foreach($favourites as $key => $value){
                $product = $this->get_product_details_by_id(base64_encode($value["product_id"]));
                //print_r($product);
                if(!empty($product)){
                    //$favourites[$key] = $product;
                    array_push($fv_prod, $product);
                }                
            }
	    
            //print_r($fv_prod); exit;
            $offset1 = LIMIT + $offset;
	    if($count_prod > $offset1) {
		$ret[0] = 1;
	    }
	    else {
		$ret[0] = 0;
	    }
	    $ret[1] = $offset1;
	    $ret[2] = $fv_prod;
	    return $ret;             
        }
        else {
            return false;
        }
    }
    
    public function get_seller_rating($post=[]){
        $average_rating = $this->db->select("AVG(rating) as seller_rating")
                        ->where('seller_id', $post['seller_id'])
                        ->get('seller_rating')->row_array();
        
        if(!empty($average_rating)) {
            return number_format($average_rating["seller_rating"],1);
        }
        else {
            return 0;
        }
    }    
}


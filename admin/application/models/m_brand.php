<?php

class M_brand extends CI_Model {
    
    function export_brands() {	
	$products = $this->db->select('c.category_code as "Category / Subcategory Code", sc.brand_code as "Brand Code", sc.brand_name as "Brand Name",  sc.brand_logo as "Brand Logo", sc.slider_img as "Brand Slider Image"', false)
		    ->join("brand_category_allocation bc", "bc.brand_id = sc.brand_id")
		    ->join("category_mst c", "c.category_id = bc.category_id")
		    ->order_by("sc.brand_name", "asc")
		    ->get('brand_mst sc')->result_array();
	
	//print_r($products); exit;
	//download in xls	
	$filename = "brand_" . date("Y_m_d_H_i") . ".csv";	
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=".$filename);
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"); 
	
	$df = fopen("php://output", 'w');
	$pkeys = array_keys(reset($products));
//	$pkeys = array_slice($pkeys, 1);
		
	fputcsv($df, $pkeys);
	foreach ($products as $row) {
//	    $row = array_slice($row, 1);
	    fputcsv($df, $row);	    
	}
	fclose($df);	
    }
    
    public function add_brand($post=[]){        
        //check wheather brand exists or not       
        if ($post['brand_id'] == '') {            
            $exists = $this->db->select('*')
                ->where('brand_name', $post["brand_name"])
                ->get('brand_mst')->row_array();            
        }
        
        if (!empty($exists)) {
            return 'exist';
        }
        else {
            //add brand details
            if($post["brand_logo"] != '' && $post["slider_img"] != '') {
                $insert_array = array(
                    'brand_name' => $post["brand_name"],
                    'in_loyalty_club' => $post["in_loyalty_club"],
                    'is_top_brand' => $post["is_top_brand"],
                    'brand_logo' => $post["brand_logo"],
                    'slider_img' => $post["slider_img"],
                );
            } 
            else if($post["brand_logo"] != '' && $post["slider_img"] == '') {
                $insert_array = array(
                    'brand_name' => $post["brand_name"],
                    'in_loyalty_club' => $post["in_loyalty_club"],
                    'is_top_brand' => $post["is_top_brand"],
                    'brand_logo' => $post["brand_logo"],
                );
            }
            else if($post["brand_logo"] == '' && $post["slider_img"] != '') {
                $insert_array = array(
                    'brand_name' => $post["brand_name"],
                    'in_loyalty_club' => $post["in_loyalty_club"],
                    'is_top_brand' => $post["is_top_brand"],
                    'slider_img' => $post["slider_img"],
                );
            } 
            else if($post["brand_logo"] == '' && $post["slider_img"] == '') {
                $insert_array = array(
                    'brand_name' => $post["brand_name"],
                    'in_loyalty_club' => $post["in_loyalty_club"],
                    'is_top_brand' => $post["is_top_brand"],
                );
            } 
            
            //print_r($insert_array); exit;
            $this->db->insert('brand_mst', $insert_array);
            $post['brand_id'] = $this->db->insert_id();
            
            //insert brand code    
            $this->db->set('brand_code', 'B'.$post['brand_id'])->where('brand_id', $post['brand_id'])->update('brand_mst');
            
            //insert volume
            $insert_volume = array(
                'brand_id' => $post["brand_id"],
                'volumne_value' => $post["volumne_value"],
                'type' => $post["type"]
            );
            
            $this->db->insert('volume_mst', $insert_volume);
            
            if(isset($post["sub_category_id"]) && $post["sub_category_id"] != '') {
                //Add sub category
                $subcategory = explode(',', $post["sub_category_id"]);
                
                foreach ($subcategory as $subcat) {
                    
                    //insert subcategory
                    $insert_category = array(
                        'brand_id' => $post["brand_id"],
                        'category_id' => $subcat,
                    );                    

                    $this->db->insert('brand_category_allocation', $insert_category);
                }
                
                //Add category
                $category = explode(',', $post["category_id"]);
                
                foreach ($category as $cat) {
                    
                    $check = $this->db->select("category_id, parent_id")
                        ->where('parent_id', $cat)
                        ->get('category_mst')->row_array();
                    
                    //print_r($check); 
                    
                    if(empty($check)){
                        //insert subcategory
                        $insert_category = array(
                            'brand_id' => $post["brand_id"],
                            'category_id' => $cat,
                        );
                        
                        $this->db->insert('brand_category_allocation', $insert_category);
                    }
                }
                
            }
            else if(isset($post["category_id"]) && $post["category_id"] != '') {
                $category = explode(',', $post["category_id"]);
                
                foreach ($category as $cat) {
                    //insert subcategory
                    $insert_category = array(
                        'brand_id' => $post["brand_id"],
                        'category_id' => $cat,
                    );

                    $this->db->insert('brand_category_allocation', $insert_category);
                }
            }
            
            echo 'success';
        }
    }
    
    public function update_brand($post=[]){
        //print_r($post); exit;
        
        //check wheather brand exists or not       
        $exists = $this->db->select('*')
            ->where('brand_id', $post["brand_id"])
            ->get('brand_mst')->row_array();            
                
        if (!empty($exists)) {            
        
            //add brand details
            if($post["brand_logo"] != '' && $post["slider_img"] != '') {
                $insert_array = array(
                    'brand_name' => $post["brand_name"],
                    'in_loyalty_club' => $post["in_loyalty_club"],
                    'is_top_brand' => $post["is_top_brand"],
                    'brand_logo' => $post["brand_logo"],
                    'slider_img' => $post["slider_img"],
                );
            } 
            else if($post["brand_logo"] != '' && $post["slider_img"] == '') {
                $insert_array = array(
                    'brand_name' => $post["brand_name"],
                    'in_loyalty_club' => $post["in_loyalty_club"],
                    'is_top_brand' => $post["is_top_brand"],
                    'brand_logo' => $post["brand_logo"],
                );
            }
            else if($post["brand_logo"] == '' && $post["slider_img"] != '') {
                $insert_array = array(
                    'brand_name' => $post["brand_name"],
                    'in_loyalty_club' => $post["in_loyalty_club"],
                    'is_top_brand' => $post["is_top_brand"],
                    'slider_img' => $post["slider_img"],
                );
            } 
            else if($post["brand_logo"] == '' && $post["slider_img"] == '') {
                $insert_array = array(
                    'brand_name' => $post["brand_name"],
                    'in_loyalty_club' => $post["in_loyalty_club"],
                    'is_top_brand' => $post["is_top_brand"],
                );
            } 
            
           // print_r($insert_array); exit;
            $this->db->set($insert_array)->where('brand_id', $post['brand_id'])->update('brand_mst');
            
            //delete category
            $this->db->where('brand_id', $post['brand_id'])->delete('brand_category_allocation');
            
            if(isset($post["sub_category_id"]) && $post["sub_category_id"] != '') {
                //Add sub category
                $subcategory = explode(',', $post["sub_category_id"]);
                
                foreach ($subcategory as $subcat) {
                    
                    //insert subcategory
                    $insert_category = array(
                        'brand_id' => $post["brand_id"],
                        'category_id' => $subcat,
                    );                    

                    $this->db->insert('brand_category_allocation', $insert_category);
                }
                
                //Add category
                $category = explode(',', $post["category_id"]);
                
                foreach ($category as $cat) {
                    
                    $check = $this->db->select("category_id, parent_id")
                        ->where('parent_id', $cat)
                        ->get('category_mst')->row_array();
                    
                    //print_r($check); 
                    
                    if(empty($check)){
                        //insert subcategory
                        $insert_category = array(
                            'brand_id' => $post["brand_id"],
                            'category_id' => $cat,
                        );
                        
                        $this->db->insert('brand_category_allocation', $insert_category);
                    }
                }
                
            }
            else if(isset($post["category_id"]) && $post["category_id"] != '') {
                $category = explode(',', $post["category_id"]);
                
                foreach ($category as $cat) {
                    //insert subcategory
                    $insert_category = array(
                        'brand_id' => $post["brand_id"],
                        'category_id' => $cat,
                    );

                    $this->db->insert('brand_category_allocation', $insert_category);
                }
            }
            
            echo 'success';
        }
    }
    
    public function get_brand_details($post = []) {
        $category_push = array();
        $sub_category_push = array();
        $sub_category_list_push = array();
        $category_list_push = array();
        
        $brand = $this->db->select("*")
                ->where('brand_id', $post["brand_id"])
                ->get('brand_mst')->row_array();
        
        $brand_category = $this->db->select("*")
                        ->where('brand_id', $post["brand_id"])
                        ->get('brand_category_allocation')->result_array();  
        
        //check whether it is subcategory or category
        foreach($brand_category as $category) {
            $check = $this->db->select("category_id, parent_id")
                    ->where('category_id', $category["category_id"])
                    ->get('category_mst')->row_array();
            
	    if(!empty($check)) {
		if($check["parent_id"] == 0) {
		    array_push($category_push, $check);
		}
		else {
		    array_push($sub_category_push, $check);
		    //array_push($category_push, $check);

		    //get category list
		    $cat_list = $this->db->select("category_id, parent_id")
			    ->where('category_id', $check["parent_id"])
			    ->get('category_mst')->row_array();

		    if( !in_array($cat_list, $category_push) ) {
			array_push($category_push, $cat_list);
		    }

		    //get sub category list
		    $sub_cat_list = $this->db->select("*")
			    ->where('parent_id', $check["parent_id"])
			    ->get('category_mst')->result_array();

		    if( !in_array($sub_cat_list, $sub_category_list_push) ) { 
			array_push($sub_category_list_push, $sub_cat_list); 
		    }
		}     
	    }
        }
        $brand["category"] = $category_push;
        $brand["sub_category"] = $sub_category_push;
        $brand["sub_category_list"] = $sub_category_list_push;
        //print_r($brand); exit;
        return $brand;
    }
    
    public function get_subcategory($post = []) {
        if(!empty($post['category_id'])) {
            $category = str_replace('on,','',$post['category_id']);
            $where = "parent_id IN (".$category.")";
            $brand = $this->db
                    ->where($where)
                    ->get('category_mst')
                    ->result_array();
            return $brand;
        }
        else {
            return '';
        }
    }
    
    function add_top_brand($post = []){
        
        $this->db->where(array(
            'brand_id' => $post["brand_id"],
            'status' => 1
        ));
        $this->db->set(array(
            'is_top_brand' => $post["tb_status"],
        ));
        $this->db->update('brand_mst');  
    }
    
}


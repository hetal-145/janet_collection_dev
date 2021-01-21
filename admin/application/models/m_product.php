<?php

class M_product extends CI_Model {
    
    function export_products() {	
	$products = $this->db->select('p.product_id as "PID ( DO NOT CHANGE IT )", p.product_name as "Product Name*", p.description as "Product Description*", pd.units as "Actual Stock Units*", pd.min_stock_limit as "Min Stock Alert Level*", pd.max_stock_limit as "Max Stock Alert Level*", s.code as "Seller Code*", sp.supplier_code as "Supplier Code*", pd.pack_size as "Pack Size*", v.volumne_value as Volume, (select volume_type from volume_type where volume_type_id = v.type) as "Volume Type*", b.brand_code as "Brand Code*", c.category_code as "Category Code*", p.drink_type as "Drink Type (1 = Alcoholic, 2 = Non – Alcoholic)*" , p.abv_percent "ABV Percentage*", p.alchol_units as "Alcohol Units*", p.no_of_return_days as "No of Days To Return*", pd.actual_price as "Actual Price (MRP)*", pd.normal_sell_price as "Sell Price*", pd.normal_discount as "Discount*", pd.loyalty_club_sell_price as "Loyalty Club Sell Price*", pd.loyalty_club_discount as "Loyalty Club Discount*", pd.vip_club_sell_price as "VIP Club Sell Price*", pd.vip_club_discount as "VIP Club Discount*", p.feature_img as "Product Image*", p.country_id as "Country*"', false)
		    ->join("product_details pd", "p.product_id = pd.product_id", "left")
		    ->join("seller s", "p.seller_id = s.seller_id", "left")
		    ->join("suppliers sp", "p.supplier_id = sp.supplier_id", "left")
		    ->join("volume_mst v", "v.volume_id = pd.volume_id", "left")
		    ->join("brand_mst b", "p.brand_id = b.brand_id", "left")
		    ->join("category_mst c", "p.category_id = c.category_id", "left")
		    ->where("p.status", 1)
		    ->order_by("p.product_name", "asc")
		    ->get('products p')->result_array(); 
	
	foreach($products as $k => $v) {	    
	    //get gallery images
	    $gimgs = $this->db->select("*")
		    ->where("product_id", $v["PID ( DO NOT CHANGE IT )"])
		    ->get("product_images")->result_array();
	    
	    if(!empty($gimgs)) {
		
		if(!empty($gimgs[0])) {
		    $products[$k]["Gallery Img1"] = $gimgs[0]["image_name"];
		}
		
		if(!empty($gimgs[1])) {
		    $products[$k]["Gallery Img2"] = $gimgs[1]["image_name"];
		}
		
		if(!empty($gimgs[2])) {
		    $products[$k]["Gallery Img3"] = $gimgs[2]["image_name"];
		}
		
		if(!empty($gimgs[3])) {
		    $products[$k]["Gallery Img4"] = $gimgs[3]["image_name"];
		}		
	    }
	}
	
//	echo "<pre>"; print_r($products); exit;
	//download in csv
	$filename = "products_" . date("Y_m_d_H_i") . ".csv";	
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=".$filename);
	header("Content-Type: application/csv; "); 
	
	$df = fopen("php://output", 'w');
	$pkeys = array_keys(reset($products));
//	$pkeys = array_slice($pkeys, 1);	
	$iarr = ["Gallery Img1", "Gallery Img2", "Gallery Img3", "Gallery Img4"];
	$pkeys = array_merge($pkeys, $iarr);
	fputcsv($df, $pkeys);
	foreach ($products as $row) {
//	    $row = array_slice($row, 1);
	    fputcsv($df, $row);	    
	}
	fclose($df);	
    }
    
    function export_products_archive() {	
	$products = $this->db->select('p.product_id as "PID ( DO NOT CHANGE IT )", p.product_name as "Product Name*", p.description as "Product Description*", pd.units as "Actual Stock Units*", pd.min_stock_limit as "Min Stock Alert Level*", pd.max_stock_limit as "Max Stock Alert Level*", s.code as "Seller Code*", sp.supplier_code as "Supplier Code*", pd.pack_size as "Pack Size*", v.volumne_value as Volume, (select volume_type from volume_type where volume_type_id = v.type) as "Volume Type*", b.brand_code as "Brand Code*", c.category_code as "Category Code*", p.drink_type as "Drink Type (1 = Alcoholic, 2 = Non – Alcoholic)*" , p.abv_percent "ABV Percentage*", p.alchol_units as "Alcohol Units*", p.no_of_return_days as "No of Days To Return*", pd.actual_price as "Actual Price (MRP)*", pd.normal_sell_price as "Sell Price*", pd.normal_discount as "Discount*", pd.loyalty_club_sell_price as "Loyalty Club Sell Price*", pd.loyalty_club_discount as "Loyalty Club Discount*", pd.vip_club_sell_price as "VIP Club Sell Price*", pd.vip_club_discount as "VIP Club Discount*", p.feature_img as "Product Image*", p.country_id as "Country*"', false)
		    ->join("product_details pd", "p.product_id = pd.product_id", "left")
		    ->join("seller s", "p.seller_id = s.seller_id", "left")
		    ->join("suppliers sp", "p.supplier_id = sp.supplier_id", "left")
		    ->join("volume_mst v", "v.volume_id = pd.volume_id", "left")
		    ->join("brand_mst b", "p.brand_id = b.brand_id", "left")
		    ->join("category_mst c", "p.category_id = c.category_id", "left")
		    ->where("p.status", 0)
		    ->order_by("p.product_name", "asc")
		    ->get('products p')->result_array(); 
	
	foreach($products as $k => $v) {	    
	    //get gallery images
	    $gimgs = $this->db->select("*")
		    ->where("product_id", $v["PID ( DO NOT CHANGE IT )"])
		    ->get("product_images")->result_array();
	    
	    if(!empty($gimgs)) {
		
		if(!empty($gimgs[0])) {
		    $products[$k]["Gallery Img1"] = $gimgs[0]["image_name"];
		}
		
		if(!empty($gimgs[1])) {
		    $products[$k]["Gallery Img2"] = $gimgs[1]["image_name"];
		}
		
		if(!empty($gimgs[2])) {
		    $products[$k]["Gallery Img3"] = $gimgs[2]["image_name"];
		}
		
		if(!empty($gimgs[3])) {
		    $products[$k]["Gallery Img4"] = $gimgs[3]["image_name"];
		}		
	    }
	}
	
//	echo "<pre>"; print_r($products); exit;
	//download in csv
	$filename = "products_" . date("Y_m_d_H_i") . ".csv";	
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=".$filename);
	header("Content-Type: application/csv; "); 
	
	$df = fopen("php://output", 'w');
	$pkeys = array_keys(reset($products));
//	$pkeys = array_slice($pkeys, 1);	
	$iarr = ["Gallery Img1", "Gallery Img2", "Gallery Img3", "Gallery Img4"];
	$pkeys = array_merge($pkeys, $iarr);
	fputcsv($df, $pkeys);
	foreach ($products as $row) {
//	    $row = array_slice($row, 1);
	    fputcsv($df, $row);	    
	}
	fclose($df);	
    }
    
    public function add_product($post=[]){    
        //print_r($post); exit;
        
        if($post["sub_category_id"] != 0){
            //unset($post["category_id"]);
            $post["category_id"] = $post["sub_category_id"];
        }
        
        $post["in_loyalty_club"] = 0;
        $post["in_vip_club"] = 0;
	
        if (array_key_exists('feature_img', $post)) {            
            $post_data = array(
                'country_id' => $post["country_id"],
                'category_id' => $post["category_id"],
                'brand_id' => $post["brand_id"],                
                'product_name' => $post["product_name"],
                'description' => $post["description"],
                'top_pick' => $post["top_pick"],
                'in_loyalty_club' => $post["in_loyalty_club"],
                'in_vip_club' => $post["in_vip_club"],
                'feature_img' => $post["feature_img"],
                'supplier_id' => $post["supplier_id"],
                'drink_type' => $post["drink_type"],
                'abv_percent' => $post["abv_percent"],
                'alchol_units' => $post["alchol_units"],
                'seller_id' => $post["seller_id"],
                'no_of_return_days' => $post["no_of_return_days"],
            );            
        } else {
            $post_data = array(
                'country_id' => $post["country_id"],
                'category_id' => $post["category_id"],
                'brand_id' => $post["brand_id"],                
                'product_name' => $post["product_name"],
                'description' => $post["description"],
                'top_pick' => $post["top_pick"],
                'in_loyalty_club' => $post["in_loyalty_club"],
                'in_vip_club' => $post["in_vip_club"],
                'supplier_id' => $post["supplier_id"],
                'drink_type' => $post["drink_type"],
                'abv_percent' => $post["abv_percent"],
                'alchol_units' => $post["alchol_units"],
                'seller_id' => $post["seller_id"],
                'no_of_return_days' => $post["no_of_return_days"],
            );    
        }

        //print_r($post_data); exit;

        if (isset($post['product_id']) && $post['product_id']) {
            //Update code
            $product_data = $this->db
                    ->set($post_data)
                    ->where('product_id', $post['product_id'])
                    ->update('products');
             return true;

        } else {  

            $exists = $this->db->select('product_id')
                ->where('product_name', $post["product_name"])
                ->where('category_id', $post["category_id"])
                ->where('brand_id', $post["brand_id"])
                ->get('products')->row_array();   

            if (!empty($exists)) {
                return $exists["product_id"];
            }
            else {
                //Insert code
                $product_data = $this->db
                    ->insert('products', $post_data);

                $last_id = $this->db->insert_id();

                if($product_data){
                    return $last_id;
                }
                else {
                    return false;
                }
            }  
        } 
    }   
    
    public function add_product_volume($post=[], $productid){              
        if($post["action"] == 'add'){

            for($i=1; $i<=$post["count_vol_div"]; $i++){

                if(!empty($post["actual_price_".$i.""])){
                    
                    if(isset($post["normal_discount_".$i.""]) && $post["normal_discount_".$i.""] != NULL){  
                        $normal_discount = $post["normal_discount_".$i.""];
                    }
                    else {
                        $normal_discount = 0;
                    }
                    //calculate normal sell price
                    $discount_amount = ($post["actual_price_".$i.""] * $normal_discount) / 100;
                    $normal_sell_price = $post["actual_price_".$i.""] - $discount_amount;
                    
                    $product_vol_data = array(
                        'product_id'        => $productid,
                        'volume_id'         => $post["volume_id_".$i.""],
                        'actual_price'      => $post["actual_price_".$i.""],
                        'normal_sell_price' => $normal_sell_price,
                        'normal_discount'   => $post["normal_discount_".$i.""],                       
                        'units'             => $post["units_".$i.""],
                        'pack_size'         => $post["pack_size_".$i.""],
                        'min_stock_limit'   => $post["min_stock_limit_".$i.""],
                        'max_stock_limit'   => $post["max_stock_limit_".$i.""],
                    );
                    
                    if(isset($post["loyalty_club_discount_".$i.""]) && $post["loyalty_club_discount_".$i.""] != NULL){  
                       
                        //calculate loyalty sell price
                        $lc_discount_amount = ($post["actual_price_".$i.""] * $post["loyalty_club_discount_".$i.""]) / 100;
                        $loyalty_club_sell_price = $post["actual_price_".$i.""] - $lc_discount_amount;
                        
                        $lc_array = array(
                            'loyalty_club_sell_price' => $loyalty_club_sell_price,
                            'loyalty_club_discount'   => $post["loyalty_club_discount_".$i.""],
                        );
                        $product_vol_data = array_merge($product_vol_data, $lc_array);
                    }
                    
                    if(isset($post["vip_club_discount_".$i.""]) && $post["vip_club_discount_".$i.""] != NULL){                    
                        //calculate vip sell price
                        $vip_discount_amount = ($post["actual_price_".$i.""] * $post["vip_club_discount_".$i.""]) / 100;
                        $vip_club_sell_price = $post["actual_price_".$i.""] - $vip_discount_amount;
                        
                        $vip_array = array(
                            'vip_club_sell_price' => $vip_club_sell_price,
                            'vip_club_discount'   => $post["vip_club_discount_".$i.""],  
                        );
                        $product_vol_data = array_merge($product_vol_data, $vip_array);
                    }
                    
                   // print_r($product_vol_data); exit;

                    //Insert code
                    $product_vol_insert = $this->db->insert('product_details', $product_vol_data);
                }   
            }
        }
        else if($post["action"] == 'edit'){
            //check exists
            $check_exists = $this->db->select('*')
                    ->where('status', '1')
                    ->where('product_id', $productid)
                    ->get('product_details')
                    ->result_array();

            if(!empty($check_exists)){

                $this->db
                    ->where('status',1)
                    ->where('product_id', $productid)
                    ->delete('product_details');
            }
                
            for($i=1; $i<=$post["count_vol_div"]; $i++){

                if(!empty($post["actual_price_".$i.""])){
                    
                    $product_vol_data = array(
                            'product_id'        => $productid,
                            'volume_id'         => $post["volume_id_".$i.""],
                            'actual_price'      => $post["actual_price_".$i.""],                              
                            'units'             => $post["units_".$i.""],
                            'pack_size'         => $post["pack_size_".$i.""],
                            'min_stock_limit'   => $post["min_stock_limit_".$i.""],
                            'max_stock_limit'   => $post["max_stock_limit_".$i.""],
                    );
                    
                    if(isset($post["normal_discount_".$i.""]) && $post["normal_discount_".$i.""] != NULL){
                        //calculate normal sell price
                        $discount_amount1 = ($post["actual_price_".$i.""] * $post["normal_discount_".$i.""]) / 100;
                        $normal_sell_price1 = $post["actual_price_".$i.""] - $discount_amount1;
                        
                        $nr_array = array(
                            'normal_sell_price' => $normal_sell_price1,
                            'normal_discount'   => $post["normal_discount_".$i.""],
                        );
                        
                        $product_vol_data = array_merge($product_vol_data, $nr_array);
                    }                    
                    
                    if(isset($post["in_loyalty_club"]) && $post["in_loyalty_club"] == 1 && isset($post["loyalty_club_discount_".$i.""]) && $post["loyalty_club_discount_".$i.""] != NULL){ 
                       // print_r($post);
                        //calculate loyalty sell price
                        $lc_discount_amount1 = ($post["actual_price_".$i.""] * $post["loyalty_club_discount_".$i.""]) / 100;
                        $loyalty_club_sell_price1 = $post["actual_price_".$i.""] - $lc_discount_amount1;
                        
                        $lc_array = array(
                            'loyalty_club_sell_price' => $loyalty_club_sell_price1,
                            'loyalty_club_discount'   => $post["loyalty_club_discount_".$i.""],
                        );
                        
                        $product_vol_data = array_merge($product_vol_data, $lc_array);
                    }
                    
                    if(isset($post["in_vip_club"]) && $post["in_vip_club"] == 1 && isset($post["vip_club_discount_".$i.""]) && $post["vip_club_discount_".$i.""] != NULL){                    
                        //calculate vip sell price
                        $vip_discount_amount1 = ($post["actual_price_".$i.""] * $post["vip_club_discount_".$i.""]) / 100;
                        $vip_club_sell_price1 = $post["actual_price_".$i.""] - $vip_discount_amount1;
                        
                        $vip_array = array(
                            'vip_club_sell_price' => $vip_club_sell_price1,
                            'vip_club_discount'   => $post["vip_club_discount_".$i.""],  
                        );
                        $product_vol_data = array_merge($product_vol_data, $vip_array);
                    }
                    
                   // print_r($product_vol_data); exit;

                    //Insert code
                    $product_vol_insert = $this->db->insert('product_details', $product_vol_data);
                }   
            }
        }
           
        return true;
    }  
    
    public function get_product_brand($category){
        $product_brand = $this->db->select('brand_id')
                ->where('category_id', $category)
                ->get('brand_category_allocation')
                ->result_array();
        
        $br_arr = array();
        foreach($product_brand as $brand){
            array_push($br_arr, $brand["brand_id"]);
        }
        $brandid = implode(',', $br_arr);
        
        if(!empty($product_brand)) {
            $where = "brand_id IN (".$brandid.")";

            $brand_data = $this->db->select('*')
                        ->where('status', 1)
                        ->where($where)
                        ->get('brand_mst')  
                        ->result_array();
        }
        
        if(!empty($brand_data)){
            return $brand_data;
        }
    }
    
    public function get_product_volume($brand){
        $product_volume = $this->db->select("volume_mst.volume_id, concat(volume_mst.volumne_value, volume_type.volume_type) as volumes", false)
                ->join('volume_type', 'volume_type.volume_type_id = volume_mst.type')
                ->where('volume_mst.status', '1')
                ->where('volume_mst.brand_id', $brand)
                ->get('volume_mst')
                ->result_array();
        
        if(!empty($product_volume)){
            return $product_volume;
        } else {
            return 0;
        }
    }
    
    function add_top_picks($post = []){
        //print_r($post); exit;
        $this->db->where(array(
            'product_id' => $post["product_id"],
            'status' => 1
        ));
        $this->db->set(array(
            'top_pick' => $post["tp_status"],
            'date' => date('Y-m-d h:i:s'),
        ));
        $this->db->update('products');  
    }
    
    public function get_product_details($post = []) {
        //print_r($post); exit;
        //Get Product Details
        $products = $this->db->select("products.*")
                ->where('products.product_id', $post['product_id'])
                ->get('products')
                ->row_array();
        
        //get category
        if(!empty($products)) {
            $category = $this->db->select("category_mst.category_id, category_mst.parent_id")
                    ->where('category_mst.category_id', $products["category_id"])
                    ->get('category_mst')->row_array();

            $products["parent_id"] = $category["parent_id"];

            //Get Product Images
            $this->db->select("product_images.image_name", false)
                            ->from("products")
                    ->join('product_images', 'products.product_id = product_images.product_id and product_images.status=1', 'left')
                    ->where("products.product_id", $post['product_id']);
            $product_images = $this->db->get()
                    ->result_array();

            foreach($product_images as $key => $images){            
                $product_images[$key] = $images["image_name"];
            } 

            $products["gallery_image"] = $product_images;

            //Get Product Volume
            $this->db->select("product_details.*", false)
                    ->from("products")
                    ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
                    ->where('products.product_id', $post['product_id']);
            $product_details = $this->db->get()
                    ->result_array();

            foreach($product_details as $key => $vols){          
                $rowval = $key + 1;
                $products["volume_id_".$rowval.""] = $vols["volume_id"];
                $products["actual_price_".$rowval.""] = $vols["actual_price"];
                $products["normal_sell_price_".$rowval.""] = $vols["normal_sell_price"];
                $products["normal_discount_".$rowval.""] = $vols["normal_discount"];            
                $products["loyalty_club_sell_price_".$rowval.""] = $vols["loyalty_club_sell_price"];
                $products["loyalty_club_discount_".$rowval.""] = $vols["loyalty_club_discount"];            
                $products["vip_club_sell_price_".$rowval.""] = $vols["vip_club_sell_price"];
                $products["vip_club_discount_".$rowval.""] = $vols["vip_club_discount"];            
                $products["units_".$rowval.""] = $vols["units"];
                $products["pack_size_".$rowval.""] = $vols["pack_size"];            
                $products["min_stock_limit_".$rowval.""] = $vols["min_stock_limit"];
                $products["max_stock_limit_".$rowval.""] = $vols["max_stock_limit"];
            } 

            $products["count_vol_div"] = count($product_details);
        }
        
        //echo "<pre>"; print_r($products); exit;
        
        return $products;
    }
    
    public function get_supplier_list(){
        $suppliers = $this->db->select('supplier_id, supplier_name')                
                ->get('suppliers')
                ->result_array();
        
        return $suppliers;
    }
    
    public function get_seller_list(){
        $seller = $this->db->select('*')   
                ->where('is_admin_verified', 1)
                ->where('status', 1)
                ->get('seller')
                ->result_array();
        
        if(!empty($seller)) {
            
            foreach($seller as $sel){
                unset($sel["password"]);
                unset($sel["verify_doc"]);
                unset($sel["verify_doc1"]);
                unset($sel["verify_doc2"]);
                unset($sel["verify_doc3"]);
            }
        
            return $seller;
        }  
    }
    
    function delete_product_img($post=[]) {
	$check = $this->db->select("pimg_id")->where("image_name like '%".$post["img_name"]."%'")->get("product_images")->row_array();
	
	if(!empty($check)) {
	    $this->db->set("status", 0)->where("image_name like '%".$post["img_name"]."%'")->update("product_images");
	    return 1;
	}
	else {
	    return 2;
	}
    }
}


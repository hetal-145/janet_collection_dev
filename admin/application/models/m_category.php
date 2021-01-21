<?php

class M_category extends CI_Model {
    
    function export_category() {	
	$products = $this->db->select('sc.category_id as "Category Id", sc.category_code as "Category Code", sc.category_name as "Category Name", case sc.status when 1 then "Active" when 0 then "Inactive" end as "Status"', false)
		    ->where("sc.parent_id", 0)
		    ->order_by("sc.category_name", "asc")
		    ->get('category_mst sc')->result_array(); 
	
//	foreach($products as $k => $v) {
//	    if(!empty($v["Category Image"])) {
//		$products[$k]["Category Image"] = S3_PATH.'category/'.$v["Category Image"];
//	    }
//	}
	
	//print_r($products); exit;
	//download in csv
	$filename = "category_" . date("Y-m-d") . ".csv";	
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=".$filename);
	header("Content-Type: application/csv; "); 
	
	$df = fopen("php://output", 'w');
	$pkeys = array_keys(reset($products));
	$pkeys = array_slice($pkeys, 1);
	
	//print_r($pkeys);
	fputcsv($df, $pkeys);
	foreach ($products as $row) {
	    $row = array_slice($row, 1);
	    //print_r($row);
	    fputcsv($df, $row);	    
	}
	fclose($df);	
	//echo "<pre>";	print_r($products); exit;
    }
    
    public function add_category($post=[]){ 
        
        if($post["in_loyalty_club"] == ''){
            $post["in_loyalty_club"] = 0;
        }
        
        //check wheather category exists or not       
        if ($post['category_id'] == '') {
            
            $exists = $this->db->select('*')
                    ->where('category_name', $post["category_name"])
                    ->get('category_mst')->row_array();            
        }
                
        if (!empty($exists)) {
            return 'exist1';
        }
        else {
            
            //print_r($post); exit;            
            
            if (isset($post['category_id']) && $post['category_id']) {
                
                unset($post['no_of_categories']);
                
                //Update code
                $category_data = $this->db
                        ->set($post)
                        ->where('category_id', $post['category_id'])
                        ->update('category_mst');
            } else {
                if(isset($post["category_img"]) && $post["category_img"] != NULL){
                    $insert_array = array(
                        'category_name' => $post["category_name"],
                        'in_loyalty_club' => $post["in_loyalty_club"],
                        'category_img' => $post["category_img"],
                    );
                } else {
                    $insert_array = array(
                        'category_name' => $post["category_name"],
                        'in_loyalty_club' => $post["in_loyalty_club"],
                    );
                }
                
                //Insert code
                $category_data = $this->db
                        ->insert('category_mst', $insert_array);
                $post['category_id'] = $this->db->insert_id();
                $this->db
                    ->set('category_code', 'C'.$post['category_id'])
                    ->where('category_id', $post['category_id'])
                    ->update('category_mst');
                
                
                //Add Sub category
                if(isset($post["want_to_add_checkbox"]) && $post["want_to_add_checkbox"] == "on") {
                    
                    for($i=1; $i<=$post["no_of_categories"]; $i++) {                        
                        
                        if(isset($post["sub_category_img_".$i]) && $post["sub_category_img_".$i] != NULL){
                            $insert_sub = array(
                                'category_name' => $post["sub_category_name_".$i],
                                'parent_id' => $post['category_id'],
                                'category_img' => $post["sub_category_img_".$i],
                            );
                        } else {
                            $insert_sub = array(
                                'category_name' => $post["sub_category_name_".$i],
                                'parent_id' => $post['category_id']
                            );
                        }
                        
                        $this->db->insert('category_mst', $insert_sub);
                        $last_sub_id = $this->db->insert_id();
                        $this->db
                            ->set('category_code', 'C'.$last_sub_id)
                            ->where('category_id', $last_sub_id)
                            ->update('category_mst');
                    }
                }
            }            
            
            if($post["in_loyalty_club"] == 1){
                //update brand
                $this->db->set('in_loyalty_club', 1)
                        ->where('category_id', $post['category_id'])
                        ->update('brand_mst');
                
                //update product
                $this->db->set('in_loyalty_club', 1)
                        ->where('category_id', $post['category_id'])
                        ->update('products');    
            }
            else if($post["in_loyalty_club"] == 0){
                //update brand
                $this->db->set('in_loyalty_club', 0)
                        ->where('category_id', $post['category_id'])
                        ->update('brand_mst');
                
                //update product
                $this->db->set('in_loyalty_club', 0)
                        ->where('category_id', $post['category_id'])
                        ->update('products');    
            }

            return true;
        }        
    }
    
    public function get_category_details($post = []) {
        $category = $this->db
                ->where('category_id', $post['category_id'])
                ->get('category_mst')
                ->row_array();
        return $category;
    }
    
}


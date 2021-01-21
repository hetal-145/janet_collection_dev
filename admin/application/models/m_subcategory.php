<?php

class M_subcategory extends CI_Model {
    
    function export_subcategory() {	
	$products = $this->db->select('sc.category_id as "Category Id", sc.category_code as "SubCategory Code", c.category_name as "Category", sc.category_name as "SubCategory", case sc.status when 1 then "Active" when 0 then "Inactive" end as "Status"', false)
		    ->join("category_mst c", "c.category_id = sc.parent_id")
		    ->order_by("sc.category_name", "asc")
		    ->get('category_mst sc')->result_array(); 
	
//	foreach($products as $k => $v) {
//	    if(!empty($v["Category Image"])) {
//		$products[$k]["Category Image"] = S3_PATH.'category/'.$v["Category Image"];
//	    }
//	}
	
	//print_r($products); exit;
	//download in csv
	$filename = "subcategory_" . date("Y-m-d") . ".csv";	
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
    
    public function add_subcategory($post=[]){ 
        
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
            
            if (isset($post['category_id']) && $post['category_id']) {
                //Update code
                $category_data = $this->db
                        ->set($post)
                        ->where('category_id', $post['category_id'])
                        ->update('category_mst');
            } else {
                //Insert code
                $category_data = $this->db
                        ->insert('category_mst', $post);
                $post['category_id'] = $this->db->insert_id();
                $supplier_data = $this->db
                        ->set('category_code', 'C'.$post['category_id'])
                        ->where('category_id', $post['category_id'])
                        ->update('category_mst');
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
    
    public function get_sub_category_details($post = []) {
        $category = $this->db
                ->where('category_id', $post['category_id'])
                ->get('category_mst')
                ->row_array();
        return $category;
    }
    
}


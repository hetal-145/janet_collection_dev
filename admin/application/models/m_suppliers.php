<?php

class M_suppliers extends CI_Model {
    
    public function add_suppliers($post=[]){            
       
        //check wheather suppliers exists or not       
        if ($post['supplier_id'] == '') {
            
            $exists = $this->db->select('*')
                    ->where('supplier_email', $post["supplier_email"])
                    ->where('supplier_mobileno', $post["supplier_mobileno"])
                    ->get('suppliers')->row_array();            
        }
        
        if (!empty($exists)) {
            return 'exist1';
        }
        else {
            
            if (isset($post['supplier_id']) && $post['supplier_id']) {
                //Update code
                $category_data = $this->db
                        ->set($post)
                        ->where('supplier_id', $post['supplier_id'])
                        ->update('suppliers');
            } else {
                //Insert code
                $category_data = $this->db
                        ->insert('suppliers', $post);
                
                $lastid = $this->db->insert_id();
                $supplier_data = $this->db
                        ->set('supplier_code', 'S'.$lastid)
                        ->where('supplier_id', $lastid)
                        ->update('suppliers');
            }

            return true;
        }        
    }
    
    public function get_supplier_details($post = []) {
        $suppliers = $this->db
                ->where('supplier_id', $post['supplier_id'])
                ->get('suppliers')
                ->row_array();
        return $suppliers;
    }    
}


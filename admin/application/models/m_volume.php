<?php

class M_volume extends CI_Model {
    
    public function add_volume($post=[]){            
       
        //check wheather volume exists or not in same brand       
        if ($post['volume_id'] == '') {
            
            $exists = $this->db->select('*')
                    ->where('volumne_value', $post["volumne_value"])
                    ->where('type', $post["type"])
                    ->where('brand_id', $post["brand_id"])
                    ->get('volume_mst')->row_array();            
        }
        
        if (!empty($exists)) {
            return 'exist1';
        }
        else {
            
            if (isset($post['volume_id']) && $post['volume_id']) {
                //Update code
                $category_data = $this->db
                        ->set($post)
                        ->where('volume_id', $post['volume_id'])
                        ->update('volume_mst');
            } else {
                //Insert code
                $category_data = $this->db
                        ->insert('volume_mst', $post);
            }

            return true;
        }        
    }
    
    public function get_volume_details($post = []) {
        $volume = $this->db
                ->where('volume_id', $post['volume_id'])
                ->get('volume_mst')
                ->row_array();
        return $volume;
    }
    
}


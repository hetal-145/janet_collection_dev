<?php

class M_faq extends CI_Model {

    public function add_faq($post = []){ 
	$post["seller_id"] = $this->session->userdata('user_id');
        $this->db->insert('seller_faq_mst', $post);
        return 'success';
    }
    
    public function update_faq($post = []){        
        $this->db->set($post)
                ->where('faq_id', $post["faq_id"])
                ->update('seller_faq_mst');
        
        return 'success';        
    }
    
    public function get_faq($faq_id){
        $faq = $this->db->select("*")
                    ->where('faq_id', $faq_id)
                    ->get('seller_faq_mst')->row_array();
        
        if(!empty($faq)){
            return $faq;
        }
    }

}

<?php

class M_about_us extends CI_Model{
    
    public function get_about_us(){
        $au = $this->db->select('value')
                ->where('key', 'about_us')
                ->get('setting')
                ->row_array();
        
        return $au;
    }
    
    public function update_about_us($post = ''){
        $update_ary = array();
        if(!empty($post["about_us"])){
            $update_ary=[
                'value' => $post["about_us"],
                'key' => 'about_us'
            ];
        }
        
        $this->db->set($update_ary)
                ->where('key', 'about_us')
                ->update('setting');
        
        return 'success';
    }
}


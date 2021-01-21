<?php

class M_shipping extends CI_Model { 
    
    public function get_all_shipping_by_id($post=[]) {
        $details = $this->db->select("*")
            ->where('user_id', $post["user_id"])
            ->where('shipping_id', $post["shipping_id"])
            ->get('shipping_mst')->row_array();  
        
        if(!empty($details)) {   
            
            if($details["zipcode_id"] != 0){        
                $zcode = $this->db->select("*")
                    ->where('zipcode_id', $details["zipcode_id"])
                    ->get('zipcode')->row_array(); 

                if(!empty($zcode)){
                    $details["zipcode"] = $zcode["zipcode"];
                    //$details["delivery_day"] = $zcode["delivery_day"];
                } else {
                    $details["zipcode"] = 'Service No Available At this destination.';
                }

                $userdata = $this->m_tools->get_user_by_id($post["user_id"]);

                if($userdata["shipping_id"] !=0 && $userdata["shipping_id"] == $details["shipping_id"]){            
                    $details["isaddress"] = true;
                }
                else {
                    $details["isaddress"] = false;
                }
            } 
            else {
                $details["zipcode"] = 'Service No Available At this destination.';
            }
            
            $details2 = array_map(function($value) {
                return $value === NULL ? "" : $value;
            }, $details);
            return $details2;
        }
        else {
            return false;
        }
    }
    
    public function add_shipping($post=[]) { 
	$user_id = $this->session->userdata("user_id");
        
        $check_zipcode = $this->db->select("*")
            ->where('zipcode', $post["zipcode"])
            ->get('zipcode')->row_array(); 
        
        if(!empty($check_zipcode)){    
            
            $ins_arr = array(
                'name' => $post["name"],
                'address' => $post["address"],
                'contactno' => $post["contactno"],
                'user_id' => $post["user_id"],
                'latitude' => $post["latitude"],
                'longitude' => $post["longitude"],
                'zipcode_id' => $check_zipcode["zipcode_id"],
            );
            $insert_shipping = $this->db->insert('shipping_mst', $ins_arr);           
	    $last_id = $this->db->insert_id();

            if($insert_shipping) {
		$user = $this->m_tools->get_user_by_id($user_id);
		if($user["shipping_id"] == '0') {		    
		    $this->db->set("shipping_id", $last_id)->where("user_id", $user_id)->update("user");
		}
                return 1;
            }
            else {
                return 2;
            } 
        } 
        else {
            return 3;
        } 
    }
    
    public function get_shipping_by_id($user_id, $shipping_id) {
        $details = $this->db->select("*")
            ->where('user_id', $user_id)
            ->where('shipping_id', $shipping_id)
            ->where("status", 1)
            ->get('shipping_mst')->row_array();         
        
        if(!empty($details)) {   
            
            if($details["zipcode_id"] != 0){        
                $zcode = $this->db->select("*")
                    ->where('zipcode_id', $details["zipcode_id"])
                    ->get('zipcode')->row_array(); 

                if(!empty($zcode)){
                    $details["zipcode"] = $zcode["zipcode"];
                    //$details["delivery_day"] = $zcode["delivery_day"];
                } else {
                    $details["zipcode"] = 'Service No Available At this destination.';
                }
                
                $userdata = $this->m_tools->get_user_by_id($user_id);
                
                if($userdata["shipping_id"] != '0' && $userdata["shipping_id"] == $details["shipping_id"]){            
                    $details["isaddress"] = 1;
                }
                else {
                    $details["isaddress"] = 0;
                }
            } 
            else {
                $details["zipcode"] = 'Service No Available At this destination.';
            }
            
            $details2 = array_map(function($value) {
                return $value === NULL ? "" : $value;
            }, $details);
            return $details2;
        }
        else {
            return 0;
        }
    }
    
    public function add_shipping_to_account($shipping_id, $user_id) {
        
        //check shipping details exists
        $check_exists = $this->db->select("*")
            ->where('shipping_id', $shipping_id)
            ->where("status", 1)
            ->get('shipping_mst')->row_array();
        
        if(!empty($check_exists)){        
            $check_zipcode = $this->db->select("*")
                ->where('zipcode_id', $check_exists["zipcode_id"])
                ->get('zipcode')->row_array(); 

            if(!empty($check_zipcode)){    

                $ins_arr = array(
                    'shipping_id' => $shipping_id,
                );
                $update = $this->db
                        ->set($ins_arr)
                        ->where('user_id', $user_id)
                        ->update('user');

                if($update) {
                    return $shipping_id;
                }
            } 
            else {
                return 2;
            } 
        } else {
            return 3;
        }           
    }
    
    public function remove_shipping_details($shipping_id) {  
        
        //Check Exists
        $exists = $this->db->select('*', false)
                ->where('shipping_id', $shipping_id)                
                ->get('shipping_mst')->row_array();
        
        if(!empty($exists))
        {            
            $deletepro = $this->db->set("status", 0)
                ->where('shipping_id', $shipping_id)
                ->update('shipping_mst');
            
            if($deletepro) {
                return $shipping_id;
            }
            else {
                return 'error';
            } 
        }     
    }
    
    public function get_shipping_details() {
        $user_id = $this->session->userdata('user_id');
        $post["user_id"] = $user_id;
        
        $details = $this->db->select("*")
            ->where('user_id', $post["user_id"])
            ->where("status", 1)
            ->get('shipping_mst')->result_array();          
        
        $userdata = $this->m_tools->get_user_by_id($post["user_id"]);
        
        foreach ($details as $key => $value){
            
            if(is_null($value["latitude"])){
                $details[$key]["latitude"] = "";
            }
            
            if(is_null($value["longitude"])){
                $details[$key]["longitude"] = "";
            }
            
            if($value["zipcode_id"] != 0){             
                $zcode = $this->db->select("*")
                    ->where('zipcode_id', $value["zipcode_id"])
                    ->get('zipcode')->row_array(); 

                if(!empty($zcode)){
                    $details[$key]["zipcode"] = $zcode["zipcode"];
                } else {
                    $details[$key]["zipcode"] = 'Service No Available At this destination.';
                }
            } 
            else {
                $details[$key]["zipcode"] = 'Service No Available At this destination.';
            }
            
            if($userdata["shipping_id"] !=0 && $userdata["shipping_id"] == $value["shipping_id"]){            
                $details[$key]["isaddress"] = 1;
            }
            else {
                $details[$key]["isaddress"] = 0;
            }
        }
        
        if(!empty($details)) {        
            return $details;
        }
        else {
            return false;
        }            
    }
}

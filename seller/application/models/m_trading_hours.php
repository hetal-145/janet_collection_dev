<?php

class M_trading_hours extends CI_Model {
    
    public function add_trading_hours($post = []){ 
	$post["seller_id"] = $this->session->userdata('user_id');
	$check = $this->db->select("thr_id, seller_id")
		->where('seller_id', $post["seller_id"])
		->where('weekday', $post["weekday"])
		->where("status", 1)
		->get("trading_hours")->row_array();
	
	if(empty($check)) {
	    $seller = $this->m_tools->get_seller($check["seller_id"]);
	    $post["timezone_utc"] = trim($seller["timezone_utc"]);
	    //get utc start time
	    $squery = $this->db->query("select CONVERT_TZ(CONCAT(CURRENT_DATE(),' ','".trim($post["start_time"])."'),'".trim($seller["timezone_utc"])."','+0:00') as start_time_utc");
	    $stime = $squery->row();
	    $post["start_time_utc"] = date("H:i:s", strtotime($stime->start_time_utc));
	    
	    //get utc end time
	    $equery = $this->db->query("select CONVERT_TZ(CONCAT(CURRENT_DATE(),' ','".trim($post["end_time"])."'),'".trim($seller["timezone_utc"])."','+0:00') as end_time_utc");
	    $etime = $equery->row();
	    $post["end_time_utc"] = date("H:i:s", strtotime($etime->end_time_utc));
		    
	    $this->db->insert('trading_hours', $post);
	    return 'success';
	}
	else {
	    return 'exist';
	}
    }
    
    public function update_trading_hours($post = []){        
        $check = $this->db->select("thr_id, seller_id")
		->where("thr_id", $post["thr_id"])
		->get("trading_hours")->row_array();
	
	if(!empty($check)) {
	    $seller = $this->m_tools->get_seller($check["seller_id"]);
	    $post["timezone_utc"] = trim($seller["timezone_utc"]);
	    //get utc start time
	    $squery = $this->db->query("select CONVERT_TZ(CONCAT(CURRENT_DATE(),' ','".trim($post["start_time"])."'),'".trim($seller["timezone_utc"])."','+0:00') as start_time_utc");
	    $stime = $squery->row();
	    $post["start_time_utc"] = date("H:i:s", strtotime($stime->start_time_utc));
	    
	    //get utc end time
	    $equery = $this->db->query("select CONVERT_TZ(CONCAT(CURRENT_DATE(),' ','".trim($post["end_time"])."'),'".trim($seller["timezone_utc"])."','+0:00') as end_time_utc");
	    $etime = $equery->row();
	    $post["end_time_utc"] = date("H:i:s", strtotime($etime->end_time_utc));
	    
	    $this->db->set($post)
		    ->where("thr_id", $post["thr_id"])
		    ->update('trading_hours');
	    return 'success';
	}
	else {
	    return 'notexist';
	}
    }
    
    public function get_trading_hours($thr_id){
        $response = $this->db->select("*")
                    ->where('thr_id', $thr_id)
		    ->where("status", 1)
                    ->get('trading_hours')->row_array();
        
        if(!empty($response)){
            return $response;
        }
    }

}

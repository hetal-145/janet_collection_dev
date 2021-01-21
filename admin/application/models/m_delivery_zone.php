<?php

class M_delivery_zone extends CI_Model {
    
    function export_delivery_charges(){
	$products = $this->db->select('m.miles as "Miles", m.base_rate as "Base Rate", m.pay_driver_pickup as "Driver Pickup Rate", m.pay_driver_dropoff as "Driver DropOff Rate"', false)
		    ->order_by("m.miles", "asc")
		    ->get('delivery_charges m')->result_array(); 
	
	//download in csv
	$filename = "delivery_charges_" . date("Y_m_d_H_i") . ".csv";	
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=".$filename);
	header("Content-Type: application/csv; "); 
	
	$df = fopen("php://output", 'w');
	$pkeys = array_keys(reset($products));
	fputcsv($df, $pkeys);
	foreach ($products as $row) {
	    fputcsv($df, $row);	    
	}
	fclose($df);	
    }
    
    function export_delivery_zone(){
	$products = $this->db->select('m.city as "City", m.area_code as "Area Code"', false)
		    ->order_by("m.area_code", "asc")
		    ->get('delivery_zone m')->result_array(); 
	
	//download in csv
	$filename = "delivery_zone_" . date("Y_m_d_H_i") . ".csv";	
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=".$filename);
	header("Content-Type: application/csv; "); 
	
	$df = fopen("php://output", 'w');
	$pkeys = array_keys(reset($products));
	fputcsv($df, $pkeys);
	foreach ($products as $row) {
	    fputcsv($df, $row);	    
	}
	fclose($df);	
    }
    
    function get_delivery_zones(){
        $response = $this->db->select("*")
               ->where("status", 1)
               ->get("delivery_zone")->result_array();
        if(!empty($response)) {
           return $response;
        }
    }
}


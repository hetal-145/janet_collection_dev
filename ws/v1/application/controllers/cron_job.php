<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cron_job extends CI_Controller {
     /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('input');
	$this->load->model('m_api');
	$this->load->model('m_apid');
        $this->load->model('m_notifyd');
	$this->load->model('m_notify'); 
	$this->load->model('cron_model'); 
	ini_set('display_errors', '1');
        //$this->output->set_header('Authorization: 272cee7490ddfdf72b9ce9a989efcdd0',true);
        if (isset($_REQUEST) && $_REQUEST) {
            log_message('error', $this->uri->uri_string() . ' /// request ---> ' . json_encode($_REQUEST));
        }
        if (isset($_FILES) && $_FILES) {
            log_message('error', $this->uri->uri_string() . ' /// files ---> ' . json_encode($_FILES));
        }
        $headers = $this->input->request_headers();

        if (isset($headers['Authorization']) && $headers['Authorization']) {
            log_message('error', $this->uri->uri_string() . ' /// header token ---> ' . json_encode($headers['Authorization']));
        }
    }
    
    /**
     * This function is used to send notificatiob to driver automatically
     * This function is called by cron job every hour
     */
    
    public function notify_driver(){
//	// is_cli_request() is provided by default input library of codeigniter
//        if($this->input->is_cli_request())
//        {            
//            echo "This script can only be accessed via the command line" . PHP_EOL;
//            return;
//        }	
	
	$response = $this->cron_model->notify_driver();
	
	if($response){
            $this->response = array(
                "status" => "true",
                "details" => $response
            );
            echo json_encode($this->response);
        }
        else {
            $this->response = array(
                "status" => "false",
                "response_msg" => "no data found"
            );
            echo json_encode($this->response);
        }
    }
    
    public function repeat_order(){
//	// is_cli_request() is provided by default input library of codeigniter
//        if($this->input->is_cli_request())
//        {            
//            echo "This script can only be accessed via the command line" . PHP_EOL;
//            return;
//        }
	
	$get_orders = $this->db->select("*")->where("status", 1)->where("DATE(to_be_notified_on)", date('Y-m-d'))->get("repeat_orders")->result_array();

	if(!empty($get_orders)) {	   
	    foreach($get_orders as $key => $value) {
		$tdate = date('Y-m-d');
		$hdate = date('H');
		$ndate = date('Y-m-d', strtotime($value["to_be_notified_on"]));
		$hndate = date('H', strtotime($value["to_be_notified_on"]));
		
		$today_date = strtotime($tdate) + ($hdate*60*60);
		$notified_date = strtotime($ndate) + ($hndate*60*60);
		
		if($today_date == $notified_date) {
		    $push = array(
			'to_user_id' => $value["user_id"],
			'order_id' => $value["order_id"],
			'message' => 'Do you want to repeat this order?',   
			'notification_type' => 24,
			'is_notified' => 1
		    );
		    
		    $this->m_api->create_notification($push);  
		    $this->m_notify->send($push);
		    
		    $this->db->set("is_notified", 1)
			    ->where("user_id", $value["user_id"])
			    ->where("order_id", $value["order_id"])
			    ->where("status", 1)
			    ->update("repeat_orders");
		}
	    }
	    
	    echo "send";
	}
        else {
            echo "not send";
        }
	
//	$response = $this->cron_model->repeat_order();
//	
//	if($response) {
//	    $this->response = array(
//                "status" => "true",
//                "response_msg" => "notification sent."
//            );
//            echo json_encode($this->response);
//	}
//        else {
//            $this->response = array(
//                "status" => "false",
//                "response_msg" => "no data found."
//            );
//            echo json_encode($this->response);
//        }	
    }
}


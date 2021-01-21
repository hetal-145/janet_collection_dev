<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class notifications extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('m_tools');
        $this->load->model('m_login');
        $this->load->model('m_notifications');
        $this->m_login->check_session();
    }

    public function index()
    {  	
	$response = $this->m_tools->use_api('get_notification_list');
//	print_r($response);
	if($response["status"] == "false") {
	    $data["notifications"] = array(); 
	    $data["offset"] = 0;
	    $data["flag"] = 0;
	    
	}
	else {
	    $data["notifications"] = $response["notification_list"]; 
	    $data["offset"] = $response["offset"];
	    $data["flag"] = $response["flag"];	    
	}
	$data["total_notifications"] = $this->m_notifications->get_total_notifications(); 
	$data["unread_notifications"] = $this->m_notifications->get_unread_notifications(); 
        $this->m_tools->template('notifications', $data);
    }
    
    public function get_notification_list() {
        $post = $_POST;
	$response = $this->m_tools->use_api('get_notification_list', $post);
        $data["notifications"] = $response["notification_list"]; 
	$data["offset"] = $response["offset"];
	$data["flag"] = $response["flag"];
        $data["total_notifications"] = $this->m_notifications->get_total_notifications(); 
        $data["unread_notifications"] = $this->m_notifications->get_unread_notifications(); 
	
        if($data["notifications"] == 'error') {
	    echo 'error'; exit;
	}
	else {
	    echo json_encode($data); exit;
	}
    }
}
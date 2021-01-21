<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
        
class Admin_bonus extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_login');
	$this->load->model('m_tools');
        $this->load->model('m_notify');
	$this->load->model('m_notifyd');
        $this->m_login->check_session();
    }

    public function index() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('driver_bonus');
        $xcrud->where('bonus_type', 2);
	$xcrud->order_by('date','desc');
	$xcrud->relation("user_id", "user", "user_id", array('firstname', 'lastname'));
	$xcrud->change_type('date','date');
	$xcrud->columns("user_id, amount, reason, date", false);
	$xcrud->fields("user_id, amount, reason, date");
	$xcrud->search_columns("user_id", "user_id");
	$xcrud->label(array(
            'user_id' => 'Driver Name',
            'amount' => 'Amount (in £)',
            'reason' => 'Reason',
	    'date' => 'Provided On'
        ));
	
        $xcrud->unset_edit();
        $xcrud->unset_remove();
        $xcrud->unset_add(); 
	
	$data['driver_list'] = $this->get_driver_list();
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('admin_bonus_list', $data);
        $this->load->view('footer');
    }
    
    public function get_driver_list() {
	$list = $this->db->select("user_id, concat(firstname,' ',lastname) as name", false)
		->where("status", 1)
		->where("is_admin_verified", 1)
		->where("user_type", 2)
		->get("user")->result_array();
	
	if(!empty($list)) {
	    return $list;
	}
    }
    
    public function add_bonus(){
	$post = $_POST;
	$drivers = explode(',', $post["user_id"]);
//	print_r($drivers); exit;
	$counter = 0;
	foreach($drivers as $driver){
	    $user = $this->m_tools->get_user_by_id($driver);
	    //update wallet amount
	    $wallet_amt = $user["wallet"] + $post["amount"];        
	    $this->db->set('wallet', $wallet_amt)->where('user_id', $driver)->update('user');
	    
	    //add in wallet history
	    $wallet_history = array(
		'user_id' => $driver,
		'type'=> 1,
		'debit_credit_amount' => $post["amount"],
		'balance_amount' => $wallet_amt,
		'note' => $post["reason"],
		'payment_status' => 'SUCCESS',
		'payment_history' => '{"status":"true", "payment":"success", "amount":'.$post["amount"].'}',
		'transaction_id' => date('YmdHis').$driver
	    );
	    $this->db->insert("wallet_history", $wallet_history);
	    
	    //Add in driver bonus
	    $driver_bonus = array(
		'user_id' => $driver,
		'bonus_type'=> 2,
		'amount' => $post["amount"],
		'reason' => $post["reason"]
	    );
	    $this->db->insert("driver_bonus", $driver_bonus);
	    
	    //send_notification
	    $push = array(
               'to_user_id' => $driver,
               'notification_type' => 26,
               'message' => 'Your have received a bonus of '. CURRENCY_CODE.$post["amount"].' from admin for reason '.$post["reason"].'"', 
		'driver_id' => $driver,
            );
	    
	    //insert in notification
	    $this->db->insert("notification", $push);
	    //send notification
	    $this->m_notifyd->send($push);
	    $counter++;
	}
	
	if(count($drivers) == $counter) {
	    echo 'success';
	}
	else {
	    echo 'fail';
	}
    }
}

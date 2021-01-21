<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gift_card extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('m_tools');
        $this->load->model('m_cart');
        $this->load->model('m_login');
        $this->load->model('m_gift_card');
        $this->m_login->check_session();
    }

    public function index()
    {
        $gift_cards = $this->m_gift_card->get_received_gift_card_list(0);	
	if($gift_cards == 'error') {
            $data["offset"] = 0;
	    $data["gift_card"] = "";
        }
	
	$data["offset"] = $gift_cards[1];
	$data["gift_card"] = $gift_cards[2];
	$this->load->view('header', $data);
	$this->load->view('gift_card', $data);
	$this->load->view('footer');	
    }
    
    public function get_gift_card() {
	$orders = $this->m_gift_card->get_received_gift_card_list($_POST["offset"]);
        if($orders == 'error') {
            echo 'error'; exit;
        }
        else {
	    $data["offset"] = $orders[1];
	    $data["gift_cards"] = $orders[2];
	    echo json_encode($data); 
        }
    }
    
    public function get_card_details() {
        $response = $this->m_gift_card->gift_card_details_by_id($_POST);
        if(!empty($response)) {
            echo json_encode($response); exit;
        }
    }
    
    public function redeem_gift_card() {
	//print_r($_POST); exit;
	$_POST["user_id"] = $this->session->userdata("user_id");
        $response = $this->m_gift_card->redeem_gift_card($_POST);
	//print_r($response); exit;
        if(!empty($response)) {
            echo $response; exit;
        }
    }
    
    public function send_gift_card() {
        //print_r($_POST); exit;
        $response = $this->m_gift_card->send_gift_card($_POST);
        //print_r($response); exit;
        if(!empty($response)) {
            echo json_encode($response); exit;
        }
    }
    
    public function do_gift_card_payment(){
	$older_data = $this->m_gift_card->get_giftcard_by_id($_GET['card_id']);
        $older_data['total_price'] = 100 * $older_data['amount'];	
        $this->load->view('gift_card_payment', $older_data);
    }
    
    public function save_gift_card_transation(){
        $post = $_POST;
	$order = $this->m_gift_card->save_gift_card_transation($post);
        
        $push = array(
            'to_user_id' => "".$order['receiver']."",
            'message' => 'You have received a gift card from '.$order['sender'].'',   
            'notification_type' => 4           
        );

//        $this->m_api->create_notification($push);             
//        $this->m_notify->send($push);
            
        if (!empty($order)) {
            redirect(base_url("gift_card/payment_success"));            
        } 
        else {
            redirect(base_url("gift_card/payment_failed"));
        }
    }
    
    public function payment_success() {        
        echo "success";
        redirect(base_url("gift_card"));      
    }
    
    public function payment_failed() {        
        $this->load->view('header');
        $this->load->view('fail');
        $this->load->view('footer');
    }
}
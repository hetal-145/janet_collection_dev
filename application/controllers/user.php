<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('m_tools');
        $this->load->model('m_login');
        $this->load->model('m_user');
        $this->m_login->check_session();
    }

    public function index() {}
    
    public function profile() {
        $post = $_POST;
        $files = $_FILES;
        //echo "<pre>"; print_r($post); print_r($files); exit;
        $response = $this->m_user->update_profile($post, $files);
        if($response === 1) {
            echo 1;
        }
        else if($response === 2) {
            echo 2;
        }
        else {
            echo $response;
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
        $this->load->view('gift_card_payment');
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
        echo "failed";
    }
}
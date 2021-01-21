<?php
include('vendor/autoload.php');

class M_gift_card extends CI_Model {
    
    function redeem_gift_card($post = []) {        
        //print_r($post); exit;
        $check = $this->db->select("*")
                ->where("redeem_code", $post["redeem_code"])
                ->get("gift_card")->row_array();

        if(!empty($check)) {
	    
	    if($check["status"] == 1 && $check["is_redeem"] == 1) {
		return 2;
	    }
	    else {            
		//get email
		$userdata = $this->m_tools->get_user_by_id($post["user_id"]);

		//update the status
		$this->db->set("is_redeem", 1)
			->set("gift_car_email", $check["receiver_email"])
			->set("receiver_email", $userdata["email"])			
			->where("redeem_code", $post["redeem_code"])		    
			->update("gift_card");


		$get_gift_card = $this->db->select("*")
			->where("card_id", $check["card_id"])
			->get("gift_card")->row_array();

		//To receiver
		$to = $get_gift_card['receiver_email'];
		$subject = 'Congratulation! A Gift Card for you.';
		$msg = $this->load->view('mail_tmp/header', $userdata, true);
		$msg .= $this->load->view('mail_tmp/gift_card_redeem', $get_gift_card, true);
		$msg .= $this->load->view('mail_tmp/footer', $userdata, true);
		$this->m_tools->send_mail($to, $subject, $msg);   

		return 3;
	    }
        }
        else {
            return 1;
        }
    }
    
    function gift_card_by_id($post=[]) {
        //get email of user
        $userdata = $this->m_tools->get_user_by_id($post["user_id"]);  
        
        $gift_card = $this->db->select("*")
            ->where('card_id', $post["card_id"])
	    ->where('is_redeem', 1)
            ->where('receiver_email', $userdata["email"])
            ->get('gift_card')->row_array();  
        
        //print_r($gift_card); exit;
        
        if(!empty($gift_card)) {
            
            //remaining amount
            $gift_card_balance = $this->db->select("*")
                ->where('card_id', $post["card_id"])
                ->order_by('date', 'desc')
                ->limit(1)
                ->get('gift_card_history')->row_array();
            
            if(!empty($gift_card_balance)){
                $gift_card["remaining_amount"] = $gift_card_balance["balance_amount"];
            }  
            
            $where_his = "order_id != 0 AND card_id = ".$post["card_id"]." AND order_id = ".$post["order_id"]."";
            
            //history of gift card
            $gift_card_history = $this->db->select("used_amount")
                ->where($where_his)
                ->order_by('date', 'desc')
                ->get('gift_card_history')->result_array();    
            
            $gift_card["used_amount"] = $gift_card_history[0]["used_amount"];
            
            return $gift_card;
        }
        else {
            return false;
        }            
    }
    
    function gift_card_details_by_id($post=[]) {
        $user_id = $this->session->userdata("user_id");
       //get email of user
        $userdata = $this->m_tools->get_user_by_id($user_id);  
        
        $gift_card = $this->db->select("*, DATE_FORMAT(expiry_date, '%d %M, %Y') as expiry_date", false)
            ->where('card_id', $post["card_id"])
            ->where('status', 1)
	    ->where('is_redeem', 1)
            ->where('receiver_email', $userdata["email"])
            ->get('gift_card')->row_array();  
                
        if(!empty($gift_card)) {
            
            $gift_card["amount"] = number_format($gift_card["amount"], 2);
            
            //remaining amount
            $gift_card_balance = $this->db->select("*", false)
                ->where('card_id', $post["card_id"])
                ->order_by('date', 'desc')
                ->limit(1)
                ->get('gift_card_history')->row_array();
            
            if(!empty($gift_card_balance)){
                $gift_card["remaining_amount"] = number_format($gift_card_balance["balance_amount"], 2);
            }  
            $where_his = "order_id != 0 AND card_id = ".$post["card_id"]."";
            
            //history of gift card
            $gift_card_history = $this->db->select("order_id")
                ->where($where_his)
                ->order_by('date', 'desc')
                ->get('gift_card_history')->result_array();            

            if(!empty($gift_card_history)){
                
                foreach($gift_card_history as $key => $value){                
                    $order_details = $this->db->select("order_no, DATE_FORMAT(order_date, '%d %M, %Y') AS order_date, order_id, FORMAT(net_amount, 2) as net_amount, total_qty", false)
                        ->where('user_id', $user_id)
                        ->where('order_id', $value["order_id"])
                        ->order_by('order_date', 'desc')
                        ->get('orders')->row_array();
                    
		    //get order product details
		    $product_details = $this->db->select("products.product_name")
			->join('products', 'products.product_id = order_product.product_id')
			->where('order_product.order_id', $value["order_id"])
			->get('order_product')->result_array();

		    $gift_card_history[$key]["products"] = $product_details;                    
                    $gift_card_history[$key]["order"] = $order_details;
                }
                
                $gift_card["history"] = $gift_card_history;
            }             
            $gift_card["currency"] = CURRENCY_CODE;
            
            //print_r($gift_card); exit;            
            return $gift_card;
        }
        else {
            return false;
        }            
    }
    
    public function save_gift_card_transation($post=[]) {        
        
        $get_gift_card = $this->db->select("*")
                ->where('card_id', $post["card_id"])
                ->get('gift_card')->row_array();
        //service key
        $config_data = $this->db->where('key', 'service_key')->get('setting')->row_array();
        
        $token = $post['stripeToken'];
     
        //get user data
        $userdata = $this->m_tools->get_user_by_id($get_gift_card["user_id"]);
        
        try{   
            \Stripe\Stripe::setApiKey($config_data["value"]); //secret key

            $response = \Stripe\Charge::create(array(
                        "amount" => $post['amount'],
                        "currency" => CURRENCY,
                        "description" => "purchased a gift card: ".$get_gift_card["code"],
                        "capture" => TRUE,
                        "source" => $token
            ));                     
        } 
        catch (Exception $e) {
	    $response = $e->getError();            
        }
	//print_r($response); exit;
        if(isset($response["status"]) && $response["status"] === 'succeeded'){  
            
            //update status
            $this->db->set('status', 1)->where('card_id', $post["card_id"])->update('gift_card');
            //Add history of gift card
            $ins_arr1 = array(            
                'card_id' => $post["card_id"],
                'user_id' => $get_gift_card["user_id"],  
                'balance_amount' => $get_gift_card["amount"],
                'transaction_id' => $response["id"],
                'used_amount' => 0,
                'payment_history' => json_encode($response),                   
                'payment_status' => $response["status"],
            );
            $insert1 = $this->db->insert('gift_card_history', $ins_arr1);
            
            if($insert1) {
                //To receiver
                $to = $get_gift_card['receiver_email'];
                $subject = 'Congratulation! A Gift Card for you.';
                $msg = $this->load->view('mail_tmp/header', $userdata, true);
                $msg .= $this->load->view('mail_tmp/gift_card', $get_gift_card, true);
                $msg .= $this->load->view('mail_tmp/footer', $userdata, true);
                $this->m_tools->send_mail($to, $subject, $msg);

                //To sender
                $to1 = $userdata['email'];
                $subject1 = 'You have purchase a Gift Card';
                $msg1 = $this->load->view('mail_tmp/header', $userdata, true);
                $msg1 .= $this->load->view('mail_tmp/gift_card_sent', $get_gift_card, true);
                $msg1 .= $this->load->view('mail_tmp/footer', $userdata, true);
                $this->m_tools->send_mail($to1, $subject1, $msg1);
            }
            
            //receiver user id
            $get_receiver = $this->m_tools->get_user_by_email($get_gift_card['receiver_email']);
            
            //sender name
            $get_sender = $this->m_tools->get_user_by_id($get_gift_card['user_id']);
            
            $user = array(
                'receiver' => $get_receiver["user_id"],
                'sender' => $get_sender["firstname"].' '.$get_sender["lastname"],
            );
            
            return $user;

        } else {
	    
	    if(empty($response->charge)) {
		$charge = date('YmdHis').$post["card_id"];
	    }
	    else {
		$charge = $response->charge;
	    }
            //update order transaction
            $update_array_tran = array(
                'transaction_id' => $charge,
                'payment_history' => json_encode($response),
                'payment_status' => 'FAILED',
                'card_id' => $post["card_id"],
            );                   

            $this->db->insert('gift_card_history', $update_array_tran);

            return false;
        }

    }
    
    public function send_gift_card($post=[]) {  
        $gift_code = $this->generate_random_code();        
        //Add a gift card
        $ins_arr = array(    
            'code' => 'GIFT'.$gift_code.$post["amount"],
            'expiry_date' => date('Y-m-d H:i:s', strtotime("+1 month", strtotime( date('Y-m-d H:i:s') ))),
            'amount' => $post["amount"],
            'sender_name' => $post["sender_name"],
            'receiver_name' => $post["name"],
            'receiver_email' => $post["email"],
            'user_id' => $this->session->userdata("user_id"),    
            'message' => $post["message"],
	    'redeem_code' => date('ymdHi'). rand(10, 99)
        );
        $this->db->insert('gift_card', $ins_arr);
        $post["card_id"] = $this->db->insert_id();
        return $post;
    }
    
    public function generate_random_code() {
        $rand = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $input_length = strlen($rand);
        $random_string = '';
        for($i = 0; $i < 5; $i++) {
            $random_character = $rand[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }  
        return $random_string;
    }        
    
    function get_received_gift_card_list($offset){
        $user_id = $this->session->userdata('user_id');
        $userdata = $this->m_tools->get_user_by_id($user_id);
	
        $gift_card = $this->db->select("*, DATE_FORMAT(expiry_date, '%d %M, %Y') as expiry_date", false)
            ->where('receiver_email', $userdata["email"])
            ->where('status', 1)
	    ->where('is_redeem', 1)
            ->order_by("date", "desc")
	    ->limit(LIMIT)
	    ->offset($offset)
            ->get('gift_card')->result_array();  
        
        if(!empty($gift_card)) {
            
            foreach($gift_card as $key => $value){
		//Content
                $gift_card_history = $this->db->select("*")
                    ->where('card_id', $value["card_id"])
                    ->order_by('date', 'desc')
                    ->limit(1)
                    ->get('gift_card_history')->row_array();
                
                if(!empty($gift_card_history)){
                    $gift_card[$key]["remaining_amount"] = CURRENCY_CODE.number_format($gift_card_history["balance_amount"], 2);
		    $gift_card[$key]["sender_name"] = ucwords($value["sender_name"]);
                }
                else {
                    $gift_card[$key]["remaining_amount"] = CURRENCY_CODE."0.00";
                }                
                $gift_card[$key]["currency"] = CURRENCY_CODE;
            }    
	    
	    $count_prod = $this->db->select("card_id")
		->where('receiver_email', $userdata["email"])
		->where('status', 1)
		->where('is_redeem', 1)
		->order_by("date", "desc")
		->get('gift_card')
                ->num_rows();
	    
	    $offset1 = LIMIT + $offset;
	    if($count_prod > $offset1) {
		$ret[0] = 1;
	    }
	    else {
		$ret[0] = 0;
	    }
	    $ret[1] = $offset1;
	    $ret[2] = $gift_card;
	    return $ret;  
        }
	else {
	    return 'error';
	}
    }
    
    function get_giftcard_by_id($gift_card) {
	$response = $this->db->select("*")->where("card_id", $gift_card)->get("gift_card")->row_array();
	return $response;
    }
}

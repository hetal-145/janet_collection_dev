<?php

class M_otp extends CI_Model {
    
    function generate_random_verification_code(){
        
        // generate random number
        $otp_no = rand(1000, 9999);  
        
        //api to send sms
        $sms_msg = "Your verification code is ".$otp_no;   
        
        $otp_array = array(
            'otp' => $otp_no,
            'sms_msg' => $sms_msg
        );
               
        return $otp_array;
    }
    
    public function get_seller_by_mobile($mobile_no){
        $userdata = $this->db->select("*")
                ->where('contact_no', $mobile_no)
                //->where('is_admin_verified', 1)
                ->where('status', 1)
                ->get('seller')->row_array();
        
        if(!empty($userdata)){
            return $userdata;
        }
    }
    
    public function country_code(){
        $list = $this->db->select("*")->get('country')->result_array();
        if(!empty($list)){
            return $list;
        }
    }

    public function send_otp($post = []) {        
        $contact_no = $post["country_code"].$post["contact_no"];        
        
        $otp_data = $this->generate_random_verification_code();            
        $check = $this->nexmo->sms($contact_no, $otp_data["sms_msg"]);            

//        $this->db->insert('delivery_receipt', array(
//            'message_id' => '1',
//            'phone' => $contact_no,
//            'otp' => $otp_data["otp"],
//            'delivery_status' => 'success'
//        ));            
//        $check = $this->db->insert_id();

        $this->db->set('type', 1)->where('delivery_receipt_id', $check)->update('delivery_receipt');

        //print_r($check); exit;

        if (!empty($otp_data) && !empty($check)) {

            $this->delivery_receipt($check, 'success', $contact_no);
            $response = array(
                'status' => 'true',
                'response_msg' => 'OTP is sent to your mobile no',
                'delivery_receipt_id' => $check,
                'otp' => $otp_data["otp"]
            );

            return $response;
        }
        else {
	    $response = array(
                'status' => 'false',
                'response_msg' => 'Contact no not exits',
            );
	    
	    return $response;
        }
    }
    
    public function delivery_receipt($delivery_receipt_id = '', $status = '', $phone = '') {
       
        if (!empty($delivery_receipt_id)) {
            $exist = $this->db
                    ->where('delivery_receipt_id', $delivery_receipt_id)
                    ->get('delivery_receipt')->row_array();
           
            if ($exist) {
                $res = $this->db
                        ->set('delivery_status', $status)
                        ->where('delivery_receipt_id', $delivery_receipt_id)
                        ->update('delivery_receipt');
            } else {
                $data = [
                    'delivery_receipt_id' => $post['delivery_receipt_id'],
                    'delivery_status' => $status,
                    'phone' => $phone
                ];
                
                $res = $this->db->insert('delivery_receipt', $data);
            }
                       
            if($res){
                return true;
            }
            else {
                return false;
            }
        }
    }   
   

    public function verify_otp($post=[]) {
        $verify_otp_data = $this->nexmo->confirm_otp($post["delivery_receipt_id"], $post["otp"]); 

        if($verify_otp_data){
            $res = $this->send_password($post);            
            if($res){
                return true;
            }
            else {
                return false;
            } 
        }
        else {
            return false;
        }
    }
    
    public function verify_otp2($post=[]) {
        $verify_otp_data = $this->nexmo->confirm_otp($post["delivery_receipt_id"], $post["otp"]); 

        if($verify_otp_data){
            return true;
        }
        else {
            return false;
        }
    }
    
    public function send_password($post=[]){
        //get contact no
        $phone = $this->db->select('phone')
                ->where('delivery_receipt_id', $post["delivery_receipt_id"])
                ->where('otp', $post["otp"])
                ->get('delivery_receipt')->row_array();

        //get user password
        $where = "concat(country_code,contact_no) = ".$phone["phone"]."";
        $userdata = $this->db->select('seller_id, password')
                ->where('status', 1)
                ->where($where)
                ->get('seller')->row_array();
        
        $new_password = substr(md5(rand()), 1, 8);
        
        $this->generate_random_password($userdata["seller_id"], $new_password);

        $password = "Your New Password is ".$new_password."";
        
        $check = $this->nexmo->send_password($phone["phone"], $password); 
        //$check = 'success';
        if ($check == 'success') {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function generate_random_password($user_id, $password) {
        $this->db->where(array(
            'seller_id' => $user_id,
            'status' => 1
        ));
        $this->db->set('password', sha1($password));
        if ($this->db->update('seller')) {
            return true;
        } else {
            return false;
        }
    }
}

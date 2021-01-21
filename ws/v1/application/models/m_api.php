<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


//include('../../vendor/worldpay/worldpay-lib-php/init.php');
//use Worldpay\Worldpay;
include('../../vendor/autoload.php');
require '../../vendor_aws/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class M_api extends CI_Model {

    public function send_mail($to, $subject, $msg, $bcc='') {
        $ci = get_instance();
        $config = array();
        $tmp_arr = array();

        $config_data = $this->db->where_in('key', array('smtp_user', 'smtp_pass', 'smtp_host', 'smtp_port'))->get('setting')->result_array();

        foreach ($config_data as $key => $row) {
            $tmp_arr[$row['key']] = $row['value'];
        }

        $config['smtp_user'] = $tmp_arr['smtp_user'];
        $config['smtp_pass'] = $tmp_arr['smtp_pass'];
        $config['smtp_host'] = $tmp_arr['smtp_host'];
        $config['smtp_port'] = $tmp_arr['smtp_port'];

        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['newline'] = "\r\n";

//print_r($config);

        $ci->email->initialize($config);

        $ci->email->from($config['smtp_user'], 'Drinxin');
        $ci->email->to($to);
        $this->email->reply_to($config['smtp_user'], 'Drinxin');
        $ci->email->subject($subject);

//create message with header and footer
//$data['msg'] = $msg;
// $msg = $this->load->view('mail_template', $data, true);
//echo $msg;
        $ci->email->message($msg);
        $ci->email->send();

// echo $this->email->print_debugger();
    }

    public function pic_url($pic, $thumb = '') {
        if ($thumb) {
            if ($pic) {
                return str_replace('/ws/v1', '', site_url('upload/thumb/' . $pic));
            }
        } else {
            if ($pic) {
                return str_replace('/ws/v1', '', site_url('upload/' . $pic));
            }
        }
        return '';
    }
    
    public function image_url($pic, $thumb = '', $folder = '') {
        
        if($folder != ''){
            if ($thumb) {
                if ($pic) {
                    return str_replace('/ws/v1', '', site_url($folder .'/thumbs/' . $pic)); 
                }
            } else {
                if ($pic) {
                    return str_replace('/ws/v1', '', site_url($folder .'/' . $pic)); 
                }
            }
        }
        else {
            if ($thumb) {
                if ($pic) {
                    return str_replace('/ws/v1', '', site_url('upload/thumb/' . $pic));
                }
            } else {
                if ($pic) {
                    return str_replace('/ws/v1', '', site_url('upload/' . $pic));
                }
            }
        }
    }
    
    public function s3_url($pic, $thumb = '', $folder = '') {
        
        if($folder != ''){
            if ($thumb) {
                if ($pic) {
                    return S3_PATH . $folder .'/thumbs/' . $pic; 
                }
            } else {
                if ($pic) {
                    return S3_PATH . $folder .'/' . $pic; 
                }
            }
        }
        else {
            if ($thumb) {
                if ($pic) {
                    return S3_PATH . '/thumb/' . $pic; 
                }
            } else {
                if ($pic) {
                    return S3_PATH . $pic; 
                }
            }
        }
    }
    
    public function image_url_product($pic, $thumb = '', $folder = '') {
        
        if($folder == ''){
            if ($thumb) {
                if ($pic) {
		    return PRODUCT_S3_PATH . $pic; 
		}
	    } else {
		if ($pic) {
		    return  PRODUCT_S3_PATH . $pic; 
		}
	    }
        }
        else if($folder != ''){
            if ($thumb) {
                if ($pic) {
		    return PRODUCT_S3_PATH.$folder.'/thumb/' . $pic; 
		}
	    } else {
		if ($pic) {
		    return PRODUCT_S3_PATH . $folder.'/'.$pic; 
		}
	    }
        }
    }

    public function notnull($ary = []) {
        return $this->filter_me($ary);
    }

    function filter_me(&$array) {
        foreach ($array as $key => $item) {
            if (!is_array($item) && $array [$key] == null) {
                $array [$key] = "";
            } else {
                is_array($item) && $array [$key] = $this->filter_me($item);
            }
        }
        return $array;
    }

    public function thumbCreate($img_uploadpath, $thumb_uploadpath, $source) {
        $fullPath = $img_uploadpath . $source;
        $thumbSize = 200;
        $thumbPath = $thumb_uploadpath;
        $thumbQuality = 99;

        $extension = pathinfo($img_uploadpath . $source, PATHINFO_EXTENSION);

        if ($extension == 'jpg' || $extension == 'jpeg')
            $full = imagecreatefromjpeg($fullPath);
        if ($extension == 'gif')
            $full = imagecreatefromgif($fullPath);
        if ($extension == 'png')
            $full = imagecreatefrompng($fullPath);


//$full = imagecreatefromjpeg($fullPath);
        $name = $source;

        $width = imagesx($full);
        $height = imagesy($full);

        /* work out the smaller version, setting the shortest
          side to the size of the thumb, constraining height/wight
         */

        if ($height > $width) {
            $divisor = $width / $thumbSize;
        } else {
            $divisor = $height / $thumbSize;
        }

        $resizedWidth = ceil($width / $divisor);
        $resizedHeight = ceil($height / $divisor);

        /* work out center point */
        $thumbx = floor(($resizedWidth - $thumbSize) / 2);
        $thumby = floor(($resizedHeight - $thumbSize) / 2);

        /* create the small smaller version, then crop it centrally
          to create the thumbnail */
        $resized = imagecreatetruecolor($resizedWidth, $resizedHeight);
        $thumb = imagecreatetruecolor($thumbSize, $thumbSize);
        imagecopyresized($resized, $full, 0, 0, 0, 0, $resizedWidth, $resizedHeight, $width, $height);
        imagecopyresized($thumb, $resized, 0, 0, $thumbx, $thumby, $thumbSize, $thumbSize, $thumbSize, $thumbSize);

        if ($extension == 'jpg' || $extension == 'jpeg')
            $status = imagejpeg($thumb, $thumbPath . $name, $thumbQuality);
        if ($extension == 'gif')
            $status = imagegif($thumb, $thumbPath . $name, $thumbQuality);
        if ($extension == 'png')
            $status = imagepng($thumb, $thumbPath . $name, 9);
    }

    /* @device
     * ios - check device token and update/insert
     * android - check device id and update/insert
     */

//$user_id, $device_type, $device_token, $device_id = '', $device_name = '', app_version = ''
    public function check_update_device_token($post = []) {
        
        $post['device_type'] = (isset($post['device_type']) && $post['device_type'] != null) ? $post['device_type'] : '';
        $post['device_token'] = (isset($post['device_token']) && $post['device_token'] != null) ? $post['device_token'] : '';
        $post['device_id'] = (isset($post['device_id']) && $post['device_id'] != null) ? $post['device_id'] : '';
        $post['device_name'] = (isset($post['device_name']) && $post['device_name'] != null) ? $post['device_name'] : '';
        $post['app_version'] = (isset($post['app_version']) && $post['app_version'] != null) ? $post['app_version'] : '';
        
        
        if ($post['device_type'] == '' || $post['device_token'] == '') {
            return false;
        } else if ($post['device_type'] == 'android' && $post['device_id'] == '') {
            return false;
        }
        
        //delete old device token
        $this->db->query("delete device_token from device_token inner join (select max(id) as lastId, user_id from device_token group by user_id having count(*) > 1) duplic on duplic.user_id = device_token.user_id where device_token.id < duplic.lastId and device_token.user_id = ".$post['user_id']."");
                
        if ($post['device_type'] == 'ios') {
//check device token exist or not
            $this->db->where(array(
                'device_token' => $post['device_token'],
                'status' => 1
            ));
            $row = $this->db->from('device_token')->get()->row_array();
            if ($row) {
//device token already exist update new user id and device
                $this->db->where(array(
                    'device_token' => $post['device_token'],
                    'status' => 1
                ));
                $this->db->set(array(
                    'user_id' => $post['user_id'],
                    'device_type' => $post['device_type'],
                    'date' => date('Y-m-d h:i:s'),
                ));
                $this->db->update('device_token');
            } else {
//device token not exist insert new token
                $this->db->insert('device_token', array(
                    'user_id' => $post['user_id'],
                    'device_token' => $post['device_token'],
                    'device_type' => $post['device_type'],
                    'device_name' => $post['device_name'],
                    'app_version' => $post['app_version'],
                    'date' => date('Y-m-d h:i:s'),
                    'status' => 1,
                ));
            }
        } else if ($post['device_type'] == 'android') {
//check device id exist or not
            $this->db->where(array(
                'device_id' => $post['device_id'],
                'status' => 1
            ));
            $row = $this->db->from('device_token')->get()->row_array();
            if ($row) {
//device token already exist update new user id and device
                $this->db->where(array(
                    'device_id' => $post['device_id'],
                    'status' => 1
                ));
                $this->db->set(array(
                    'user_id' => $post['user_id'],
                    'device_token' => $post['device_token'],
                    'device_type' => $post['device_type'],
                    'app_version' => $post['app_version'],
                    'device_name' => $post['device_name'],
                    'date' => date('Y-m-d h:i:s'),
                ));
                $this->db->update('device_token');
            } else {
//device token not exist insert new token
                $this->db->insert('device_token', array(
                    'user_id' => $post['user_id'],
                    'device_token' => $post['device_token'],
                    'device_type' => $post['device_type'],
                    'device_id' => $post['device_id'],
                    'device_name' => $post['device_name'],
                    'app_version' => $post['app_version'],
                    'date' => date('Y-m-d h:i:s'),
                    'status' => 1,
                ));
            }
        } else {
            return false;
        }
    }

    public function get_user_by_user_id_token($user_id, $token) {
        $this->db->where(array(
            'user_id' => $user_id,
            'status' => 1,
            'token' => $token,
        ));
        $userdata = $this->db->from('user')->get()->row_array();
//echo $this->db->last_query();
        if ($userdata) {
            return $userdata;
        } else {
            return false;
        }
    }

    public function get_user_by_email($email) {
        $user = $this->db
                        ->where('email', $email)
                        ->get('user')->row_array();
        return $user;
    }
    
    public function get_user_by_id($user_id) {
        $user = $this->db
                        ->where('user_id', $user_id)
                        ->get('user')->row_array();
        return $user;
    }

    public function update_password($post = []) {
        return $this->db->where('user_id', $post['user_id'])
                        ->set('password', sha1($post['password']))
                        ->set('password_updated', 1)
                        ->update('user');
    }

    public function signin($post = []) {
        
        $userdata = array();
        // for email
        $userdata_email = $this->db
            ->where('email', $post["signin_username"])
            ->where('password', sha1($post['password']))
	    ->where("user_type", 1)
	    ->where("status", 1)
            ->from('user')->get()->row_array();    
        
        if(!empty($userdata_email)){
            $userdata = $userdata_email;
        }
        else {
            // for mobileno
            $userdata_mobileno = $this->db
                ->where('mobileno', $post["signin_username"])
                ->where('password', sha1($post['password']))
		->where("user_type", 1)
		->where("status", 1)
                ->from('user')->get()->row_array(); 
        
            $userdata = $userdata_mobileno;
        }        
        
        return $userdata;
    }
    
    public function log_in($user_id){
        $check = $this->db->select("*")->where('user_id', $user_id)->get('logtbl')->row_array();
        
        if(empty($check)) {
            $insert_array = array(
                'user_id' => $user_id,
                'login_time' => date('Y-m-d H:i:s')
            );

            $this->db->insert('logtbl', $insert_array);
        }
        else {
            $this->db->set('login_time', date('Y-m-d H:i:s'))->where('user_id', $user_id)->update('logtbl');
        }
    }
    
    public function log_out($user_id){
        $check = $this->db->select("*")->where('user_id', $user_id)->get('logtbl')->row_array();
        
        if(!empty($check)) {
            $this->db->set('logout_time', date('Y-m-d H:i:s'))->where('user_id', $user_id)->update('logtbl');
        }
    }
    
    public function get_user_by_email_mobile($signin_username){
        $userdata = array();
        // for email
        $userdata_email = $this->db
            ->where('email', $signin_username)
	    ->where("user_type", 1)
	    ->where("status", 1)
            ->from('user')->get()->row_array();    
        
        if(!empty($userdata_email)){
            $userdata = $userdata_email;
        }
        else {
            // for mobileno
            $userdata_mobileno = $this->db
                ->where('mobileno', $signin_username)
                ->where("user_type", 1)
	    	->where("status", 1)
    		->from('user')->get()->row_array(); 
        
            $userdata = $userdata_mobileno;
        }
        
        return $userdata;
    }
    
    public function check_user_by_email_mobile($email = '', $mobileno=''){
        $where = "email = '".$email."' OR mobileno = ".$mobileno."";        
       
        $userdata = $this->db->select('*', false)
            ->where($where)
            ->get('user')->result_array();  
        
        if(!empty($userdata)) {
            return true;
        } else {
            return false;
        }
    }

    public function check_token($user_id = '') {
        $token = $this->db->where('user_id', $user_id)
                        ->get('user')->row_array();
        return $token;
    }

    public function update_login_token($user_id, $token) {
        $this->db->set('token', $token);
        $this->db->where('user_id', $user_id);
        if ($this->db->update('user')) {
            return true;
        } else {
            return false;
        }
    }

    public function check_profile_complition_and_get_screen_code($user_id) {
        $this->db->where('user_id', $user_id);
// $this->db->where('description !=', '');
        $this->db->where('email !=', '');
        $row = $this->db->from('user')->get()->row_array();
//echo $this->db->last_query();
        if ($row) {
            return '000';
        } else {
            return '222';
        }
    }

    public function verify($sha1_user_id) {
        $this->db->select('status');
        $this->db->where('sha1(user_id)', $sha1_user_id);
        $row = $this->db->get('user')->row();
        if ($row) {
//user found
            if ($row->status == '0') {
//not verified - do verification
                $this->db->where('sha1(user_id)', $sha1_user_id);
                $this->db->set('status', 1);
                //$this->db->set('email_verified', 1);
                $this->db->update('user');
                return '1';
            } else {
//verified -
                return '2';
            }
        } else {
//user not found
            return false;
        }
    }

    public function generate_random_password($user_id, $password) {
        $this->db->where(array(
            'user_id' => $user_id,
            'status' => 1
        ));
        $this->db->set('password', sha1($password));
        $this->db->set('password_updated', 0);
        if ($this->db->update('user')) {
            return true;
        } else {
            return false;
        }
    }

    public function update_user($post = []) {
        if (isset($post['password']) && $post['password']) {
            $post['password'] = sha1($post['password']);
        }
        $user_id = $post['user_id'];
        unset($post['user_id']);
        $this->db
                ->where('user_id', $user_id)
                ->update('user', $post);
        $post['user_id'] = $user_id;
        $userdata = $this->get_user_by_id($user_id);
        return $userdata;
    }

    public function signup($post = []) {
        if (isset($post['password']) && $post['password']) {
            $post['password'] = sha1($post['password']);
        }
        //$post['member_since'] = date('Y-m-d H:i:s');
        /*if (isset($post['status']) && $post['status']) {
            $post['status'] = '1';
        } else {
            $post['status'] = '0';
        }*/
        $post['status'] = '1';
        
        //ADD unique id        
        $post["userno"] = rand(0000000, 9999999);

        $this->db->insert('user', $post);
        $post['user_id'] = $this->db->insert_id();
        $userdata = $this->get_user_by_id($post['user_id']);
        return $userdata;
    }
    
    public function upload_verification_doc($post = [], $files = []) {
        
        //print_r($post); print_r($files); exit; 
	
	///bucket info
	$credentials = new Aws\Credentials\Credentials(PUBLISHED_KEY, SECRET_KEY);
	$s3 = new S3Client([        
	    'region' => 'eu-west-2',
	    'version' => '2006-03-01',
	    //'debug' => true,
	    "credentials" => $credentials
	]);
	
	if(isset($files["verify_doc"]) && !empty($files["verify_doc"])) {	    
	    $ext_arr2 = array('gif', 'jpg', 'png', 'jpeg');
	    
	    $ext = '.' . pathinfo($files['verify_doc']['name'], PATHINFO_EXTENSION);
	    $ext1 = pathinfo($files['verify_doc']['name'], PATHINFO_EXTENSION);
	    $filename = date('YmdHis') . rand() . $post["user_id"] . strtolower($ext);
	    $keyname = $filename;
	    $filepath = $files['verify_doc']['tmp_name'];
	    if( in_array($ext1, $ext_arr2) ) {
		$result = $s3->putObject(array(
		    'Bucket' => BUCKET_NAME,
		    'Key' => 'verification_docs/'.$keyname,
		    'SourceFile' => $filepath,
		    'ACL' => 'public-read',
		    'StorageClass' => 'STANDARD'
		));
		
		//update in user
		$update = $this->db->set('verification_doc', $filename)
			->where('user_id', $post["user_id"])
			->update('user');

		if($update) {
		    return 1;
		}
		else {
		    return 2;
		}        
	    }
	    else {
		return 'The filtype you are trying to upload is not allowed';
	    }
	}
        
//        $f = $files['verify_doc']['name'];
//        $ext = '.' . pathinfo($f, PATHINFO_EXTENSION);
//        $filenames = date('YmdHis') . rand() . $post["user_id"] . strtolower($ext);        
//
//        $config = [
//            'upload_path' => '../../upload/verification_docs/',
//            //'allowed_types' => 'jpg|png|jpeg|doc|docx|pdf|word',
//            'allowed_types' => 'jpg|png|jpeg',
//            'file_name' => $filenames
//        ];
//        $this->load->library('upload', $config);
//        $this->upload->initialize($config);
//        if ($this->upload->do_upload('verify_doc')) {
//            
//                
//        }
//        else {
//            return $this->upload->display_errors();
//        }    
    }
    
    public function generate_random_verification_code($mobileno){
        
        // generate random number
        $otp_no = rand(1000, 9999);  
        
        //api to send sms
        $sms_msg = "Your verification code is ".$otp_no;   
        
        /*$message = $client->message()->send([
            'to' => $mobileno,
            'from' => 'Drinxin.com',
            'text' => $sms_msg
        ]);  
        
        var_dump($message->getResponseData());*/
        
        /*$get_verifiy_code = $this->db->insert('delivery_receipt', array(
            'message_id' => '1',
            'phone' => $mobileno,
            'otp' => $otp_no,
            'delivery_status' => 'success'
        ));
        
        $otp_array = array(
            'delivery_receipt_id' => $this->db->insert_id(),
            'otp' => $otp_no,
            'sms_msg' => $sms_msg
        );  */   
        
         $otp_array = array(
            'otp' => $otp_no,
            'sms_msg' => $sms_msg
        );
               
        return $otp_array;
    }
    
    public function check_delivery_status($post=[]){
        $exist = $this->db
                ->where('delivery_receipt_id', $post['delivery_receipt_id'])
                ->where('delivery_status', 'delivered')
                ->get('delivery_receipt')->row_array();

        if ($exist) {
            return true;
        } else {
            return false;
        }
    }   
    
    public function verify_scode($post = []){      
        
        $verify = $this->db->where('otp' , $post["otp"])
                ->where('delivery_receipt_id' , $post["delivery_receipt_id"])
                ->get('delivery_receipt')->row_array();
        
        if ($verify) {
            return true;
        } else {
            return false;
        }                
    }

    public function check_social_id($post = []) {
        $userdata = $this->db
                        ->where('social_id',$post['social_id'])
			->where("user_type", 1)
			->where("status", 1)
                        ->get('user')->row_array();

        if ($userdata) {
            return $userdata;
        }        
    }

    public function delete_device_token($post = []) {
        return $this->db
                        ->where('user_id', $post['user_id'])
                        ->where('device_token', $post['device_token'])
                        ->delete('device_token');
    }

    public function get_profile($post = []) {
        $profile = $this->user_profile($post['profile_id']);
        
        if ($profile) {
            return $profile;
        }
        else {
            return false;
        }
    }

    public function user_profile($user_id = '') {
        $user_data = $this->db->select('*')
                ->where('u.user_id', $user_id)
                ->get('user u')
                ->row_array();
        
        if(!empty($user_data["verification_doc"]) && $user_data["is_admin_verified"] == 1) {
            $user_data["verification_doc"] = $this->s3_url( $user_data['verification_doc'],'', 'verification_docs'); 
            $doc_uploaded = true;
            $msg = "";
        } 
        else if(!empty($user_data["verification_doc"]) && $user_data["is_admin_verified"] == 0) {
            $user_data["verification_doc"] = $this->s3_url( $user_data['verification_doc'],'', 'verification_docs'); 
            $doc_uploaded = true;
            $msg = "";
        } 
        else if(empty($user_data["verification_doc"]) && $user_data["is_admin_verified"] == 1) {
            $doc_uploaded = true;
            $msg = "Your age has been verified by admin";
        }
        else {
           $doc_uploaded = false; 
           $msg = "";
        }
        
        
        $user_data['doc_uploaded'] = $doc_uploaded;
        $user_data['verify_msg'] = $msg;
        
        unset($user_data["password"]);
        unset($user_data["password_updated"]);
        unset($user_data["token"]);

        return $user_data;
    }

    public function update_profile($post = []) {
        $updated = $this->db
                ->where('user_id', $post['user_id'])
                ->set($post)
                ->update('user');
        if ($updated) {
            $userdata = $this->user_profile($post['user_id']);
            return $userdata;
        }
    }
    
    public function get_about_us(){
        $about_data = $this->db->select('value')
                ->where('key', 'about_us')
                ->get('setting')
                ->row_array();
        
        if(!empty($about_data)){
            return $about_data["value"];
        }    
        else {
            return false;
        }
    }
    
    public function get_privacy_policy(){
    $get_privacy_policy = $this->db->select("value")
                ->where('key', 'privacy_policy')
                ->get('setting')
                ->row_array();
        
        if(!empty($get_privacy_policy)){
            return $get_privacy_policy["value"];
        }
        else {
            return false;
        }
    }
    
    public function get_term_condition(){
        $get_term_condition = $this->db->select('value')
                ->where('key', 'terms_and_conditions')
                ->get('setting')
                ->row_array();
        
        if(!empty($get_term_condition)){
            return $get_term_condition["value"];
        }
        else {
            return false;
        }
    }
    
    public function get_faqs_list(){
        $get_faqs = $this->db->select('value')
                ->where('key', 'faq')
                ->get('setting')
                ->row_array();
        
        if(!empty($get_faqs)){
            return $get_faqs["value"];
        }
        else {
            return false;
        }
    }
    
    public function get_faq_question_list(){
        $get_faq_questions = $this->db->select('faq_id, faq_question')
                ->get('faq_mst')->result_array();

        if (!empty($get_faq_questions)) {
            return $get_faq_questions;            
        }        
        else {
            return false;
        }
    }
    
    public function get_faq_details($post = []) {
        $get_data = $this->db->select('faq_question, faq_answer')
                ->where('faq_id', $post['faq_id'])
                ->get('faq_mst')
                ->row_array();
        
        if(!empty($get_data)){
            return $get_data;
        }
        else {
            return false;
        }
    }
  
    //Product section
    
    public function get_brand_list($post=[]){
        $brands = array();
        if(!empty($post["category"]) && !isset($post["subcategory"])){
            $get_brand_category_list = $this->db->select('brand_id')
                    ->where('category_id', $post['category'])
                    ->get('brand_category_allocation')->result_array();
            
            $import = array();
            foreach($get_brand_category_list as $bcl){
                array_push($import, $bcl["brand_id"]);
            }
            $brand_ids = implode(',', $import);
            
	    if(!empty($brand_ids)) {
		if(isset($post["in_loyalty_club"]) && $post["in_loyalty_club"] != NULL) {
		    $where = "status = 1 and brand_id IN (".$brand_ids.") and in_loyalty_club = 1";
		}
		else {
		    $where = "status = 1 and brand_id IN (".$brand_ids.")";
		}
		
		$get_brand_list = $this->db->select('*')
                    ->where($where)
                    ->get('brand_mst')->result_array();
	    }
	    else {
		$get_brand_list = array();
	    }
	    
        }
        else if(!empty($post["category"]) && !empty($post["subcategory"])){
            $get_brand_category_list = $this->db->select('brand_id')
                    ->where('category_id', $post['subcategory'])
                    ->get('brand_category_allocation')->result_array();
            
            $import = array();
            foreach($get_brand_category_list as $bcl){
                array_push($import, $bcl["brand_id"]);
            }
            $brand_ids = implode(',', $import);
            
            if(!empty($brand_ids)) {
		if(isset($post["in_loyalty_club"]) && $post["in_loyalty_club"] != NULL) {
		    $where = "status = 1 and brand_id IN (".$brand_ids.") and in_loyalty_club = 1";
		}
		else {
		    $where = "status = 1 and brand_id IN (".$brand_ids.")";
		}
		$get_brand_list = $this->db->select('*')
			->where($where)
			->get('brand_mst')->result_array();
	    }
	    else {
		$get_brand_list = array();
	    }
        }
        else {            
            if(isset($post["in_loyalty_club"]) && $post["in_loyalty_club"] != NULL) {
		$where = "in_loyalty_club = 1 and status = 1";
            }
            else {
                $where = "status = 1";
            }
	    
	    $get_brand_list = $this->db->select('*')
                    ->where($where)
                    ->get('brand_mst')->result_array();
        }
	
	if (!empty($get_brand_list)) {
        
	    foreach($get_brand_list as $key => $value){   

		$check = $this->db->select("product_id")->where("brand_id", $value["brand_id"])->get("products")->result_array();
		if(!empty($check)) {            
		    if($value['brand_logo']){
			$get_brand_list[$key]['brand_logo'] = $this->s3_url( $value['brand_logo'],'', 'brand');    
			$get_brand_list[$key]['brand_logo_thumb'] = $this->s3_url( $value['brand_logo'] ,'thumb', 'brand');    
			$get_brand_list[$key]['slider_img'] = $this->s3_url( $value['slider_img'],'', 'brand');    
			$get_brand_list[$key]['slider_img_thumb'] = $this->s3_url( $value['slider_img'] ,'thumb', 'brand');
		    }
		    else {
			$get_brand_list[$key]['brand_logo'] = '';
			$get_brand_list[$key]['brand_logo_thumb'] = '';
			$get_brand_list[$key]['slider_img'] = '';
			$get_brand_list[$key]['slider_img_thumb'] = '';
		    }

		    array_push($brands, $get_brand_list[$key]);
		}

	    }
	    return $brands;		           
        }        
        else {
            return false;
        }
    }
    
    public function get_volume_list($post=[]){
	
	//with category parameter
        if(isset($post["category"]) && $post["category"] != NULL ) {
            $categoryid = ' and brand_category_allocation.category_id = '.$post["category"].'';             
        }
        else {
            $categoryid = '';                        
        }
        
	//with sub-category parameter
        if(isset($post["subcategory"]) && $post["subcategory"] != NULL ) {
            $categoryid = ' and brand_category_allocation.category_id = '.$post["subcategory"].'';             
        }
        else {
            $categoryid = '';                        
        }
	
	//get brands
	$get_brands = $this->db->select("brand_mst.brand_id")
		->join("brand_category_allocation", "brand_category_allocation.brand_id = brand_mst.brand_id ".$categoryid."")
		->where("brand_mst.status", 1)
		->get("brand_mst")->result_array();
		
	$gbrands = array_column($get_brands, "brand_id");	
	$brands = implode(',', $gbrands);
	//echo $brands; exit;	
	if(empty($post["category"]) && empty($post["subcategory"])) {
            $where = "volume_mst.status = 1";
        }
        else {
            $where = "volume_mst.brand_id IN (".$brands.") and volume_mst.status = 1";
        }     
	
	if($post["dtype"] == "1") {
	    $response = $this->db->select("volume_mst.type, volume_type.volume_type", false)	
		    ->join('volume_type', 'volume_type.volume_type_id = volume_mst.type')
		    ->where("volume_mst.status", 1)
		    ->where($where)
		    ->group_by("volume_mst.type")
		    ->get("volume_mst")
		    ->result_array();

	    if(!empty($response)) {
		return $response;
	    }
	    else {
		return 1;
	    }
	}
	else if($post["dtype"] == "2") {
	    if(empty($post["type"])) {
		return 2;
	    }
	    else {
	//        $this->db->select("GROUP_CONCAT(volume_mst.volume_id) as volume_id, concat(volume_mst.volumne_value,' ',volume_type.volume_type) as volume", FALSE)
	//                ->join('volume_type', 'volume_type.volume_type_id = volume_mst.type')
	//                ->where($where)
	//		->group_by('volume')
	//                ->order_by('volume');
	//        $get_volume_list = $this->db->get('volume_mst')->result_array();


		$list = array();
		$response = $this->db->select("COALESCE(MAX(volume_mst.volumne_value), 0) as max_volume, COALESCE(MIN(volume_mst.volumne_value), 0 ) as min_volume", false)	
			->join('volume_type', 'volume_type.volume_type_id = volume_mst.type')
			->where("volume_mst.status", 1)
			->where("volume_mst.type", $post["type"])
			->where($where)
			->order_by("volume_mst.volumne_value", "desc")
			->limit(1)
			->get("volume_mst")
			->row_array();

//		print_r($response);
//		exit;

		if(!empty($response)) {
		    $list["max_volume"] = $response["max_volume"];
		    $list["min_volume"] = $response["min_volume"];
		    return $list;
		}
		else {
		    return 1;
		}
	    }
	}
    }
    
    public function get_category_list($post = []) {
       
        if(isset($post["in_loyalty_club"]) && $post["in_loyalty_club"] != NULL) {
	    $where = "in_loyalty_club = 1 and status = 1";
        }
        else {
             $where = "status = 1";
        }
	
	$get_data = $this->db->select('*')
                ->where('parent_id', $post['parent_id'])
                ->where($where)
		->get('category_mst')
                ->result_array();
      
        if(!empty($get_data)){
	    foreach($get_data as $key => $value){   

		$get_subcat_data = $this->db->select('*')
		    ->where('parent_id', $value['category_id'])
		    ->where('status', 1)
		    ->get('category_mst')
		    ->result_array();

		if(!empty($get_subcat_data)){
		    $get_data[$key]["have_subcategory"] = true;
		} 
		else{
		    $get_data[$key]["have_subcategory"] = false;
		}
	    //print_r($get_subcat_data); 

		if($value['category_img']){                
		    $get_data[$key]['category_img'] = $this->s3_url( $value['category_img'],'', 'category');    
		    $get_data[$key]['category_img_thumb'] = $this->s3_url( $value['category_img'],'thumb', 'category');    
		}
		else {
		    $get_data[$key]['category_img'] = '';
		    $get_data[$key]['category_img_thumb'] = '';
		}            
	    }
	    return $get_data;
        }
    }
    
    public function get_sub_category($post = []) {
        
        if(isset($post["in_loyalty_club"]) && $post["in_loyalty_club"] != NULL) {
	    $where = "in_loyalty_club = 1 and status = 1";
        }
        else {
             $where = "status = 1";
        }
        
	$get_data = $this->db->select('*')
                ->where('parent_id', $post['category_id'])
                ->where($where)
		->get('category_mst')
                ->result_array();
	
        //print_r($get_data); exit;
        if(!empty($get_data)){
	    foreach($get_data as $key => $value){   

		if($value['category_img']){                
		    $get_data[$key]['category_img'] = $this->s3_url( $value['category_img'],'', 'category');    
		    $get_data[$key]['category_img_thumb'] = $this->s3_url( $value['category_img'],'thumb', 'category');    
		}
		else {
		    $get_data[$key]['category_img'] = '';
		    $get_data[$key]['category_img_thumb'] = '';
		}            
	    }
	    return $get_data;
        }
    }
    
    public function get_product_by_id($product_id){
        $product = $this->db
                ->where('product_id', $product_id)
                ->get('products')
                ->row_array();
        
        if(!empty($product)){
            return $product;
        }
    }
    
    public function get_volume_by_id($volume_id){
        $volume = $this->db->select("volume_mst.volume_id, concat(volume_mst.volumne_value,' ',volume_type.volume_type) as volume", FALSE)
                ->join('volume_type', 'volume_type.volume_type_id = volume_mst.type')
                ->where('volume_mst.volume_id', $volume_id)
                ->get('volume_mst')
                ->row_array();
        
        if(!empty($volume)){
            return $volume;
        }
    }
    
    function calculate_distance($user_latitude, $user_longitude, $seller_latitude, $seller_longitude) {
        //get distance
        $distance = $this->db->select("get_distance_metres(".$seller_latitude.", ".$seller_longitude.", ".$user_latitude.", ".$user_longitude.") as distance", FALSE)->get("seller")->result_array();
        print_r($distance);
        
        $distance_in_km = (string)round(($distance["distance"] / 1000), 2);
        return $distance_in_km;
    }
    
    public function get_mile_limit(){
        //get distance
        $mile_limit = $this->db->select("*")->where("key", "mile_limit")->get("setting")->row_array();
        return $mile_limit["value"];
    }
    
    public function get_top_pick_product_list($post=[]){
        $top_products = array();
        
	if(!empty($post["latitude"]) || !empty($post["longitude"])) {
            //find nearest seller
            $nearest_seller = $this->db->select('seller_id, seller_name, company_name, email, contact_no, address, latitude, longitude, case when gender=1 then "Male" when gender=2 then "Female" end as gender, get_distance_metres(latitude, longitude, '.$post["latitude"].' , '.$post["longitude"].') as distance', false)        
                                ->where('status', 1)
                                ->where('is_admin_verified', 1)
                                ->get('seller')->result_array();

            if(!empty($nearest_seller)) {
		
               // print_r($nearest_seller);
                foreach($nearest_seller as $value1){
                    //calculate distance
                    $distance = round(($value1["distance"] / 1609.34), 2);
		    
		    $mile_limit = $this->get_mile_limit();
		    
                    //seller within 10 km
                    if($distance <= $mile_limit and $distance >= 0){
			//echo " ".$value1["seller_id"];
                        //get otp picks
                        $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", false)
                                ->from("products")
                                ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1')
                                ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1')
                                ->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
				->where("products.top_pick", 1)
                                ->where("products.status", 1)
                                ->where("products.seller_id", $value1["seller_id"])
                                ->limit(20)
				->group_by('products.product_id')
                                ->order_by("products.updated_date", "desc");
                        $top_pick = $this->db->get()
                                ->result_array();
                        //print_r($top_pick); exit;
                        if (!empty($top_pick)) {         
                            //print_r($top_pick); exit;
                            foreach($top_pick as $key => $value){  
				
				$msg = str_replace(PHP_EOL,"@/@", $top_pick[$key]["description"]);
				$top_pick[$key]["description"] = json_decode('"'.$msg.'"');
				$top_pick[$key]["description"] = str_replace("@/@",PHP_EOL, $top_pick[$key]["description"]);

                                if($value1["seller_id"] != 0) {                                

                                    $seller2 = array_map(function($val) {
                                        if(is_null($val)) {
                                            $val = "";
                                        }
                                        return $val;
                                    }, $value1);

                                    $top_pick[$key]['seller'] = $seller2;
                                }
                                else {
                                    $top_pick[$key]['seller'] = array(
                                        'seller_name' => "Admin"
                                    );
                                }

                                //get favourite
                                $favourite = $this->db->select("user_id, product_id, status")
                                        ->where('product_id', $value['product_id'])
                                        ->where('user_id', $post['user_id'])
                                        ->get('product_favourite')->row_array();


                                if(!empty($favourite)){
                                    $top_pick[$key]['is_favourite'] = $favourite["status"];
                                }
                                else {
                                    $top_pick[$key]['is_favourite'] = 0;
                                }

                                //Get Product Volume
                                $this->db->select("product_details.*, concat(volume_mst.volumne_value,' ',(select volume_type from volume_type where volume_type_id = volume_mst.type)) as volumes", false)
                                        ->from("products")
                                        ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
                                        ->join('volume_mst', 'product_details.volume_id=volume_mst.volume_id and volume_mst.status=1')
                                        ->where("products.product_id", $value['product_id'])
                                        //->where("volume_mst.brand_id", $value['brand_id'])
                                        ->where("products.status", 1)
                                        ->order_by("products.date", "desc");
                                $get_data_volume = $this->db->get()
                                        ->result_array();

                                if(!empty($get_data_volume)){ 

                                    $top_pick[$key]["price"] = $get_data_volume[0]["actual_price"];

                                    foreach($get_data_volume as $vkey => $vvalue){ 
                                        $get_data_volume[$vkey]["actual_price"] = $vvalue["actual_price"];
                                    }
                                    $top_pick[$key]['isvolume'] = true;
                                } 
                                else {
                                    $top_pick[$key]['isvolume'] = false;
                                }

                                //Get Product Return policy
                                $this->db->select("product_return_policy.*", false)
                                        ->from("products")
                                        ->join('product_return_policy', 'products.product_id = product_return_policy.product_id')
                                        ->where("products.product_id", $value['product_id'])
                                        ->where("products.have_return_policy", 1)
                                        ->where("products.status", 1)
                                        ->order_by("products.date", "desc");
                                $get_data_policy = $this->db->get()
                                            ->row_array();

                                if(!empty($get_data_policy)){
                                    $get_data_policy["description"] = html_entity_decode(strip_slashes(strip_tags($get_data_policy["description"], '<strong>')));
                                    $get_data_policy["status"] = true;
                                } else {
                                    $get_data_policy["status"] = false;
                                }

                                $top_pick[$key]['return_policy'] = $get_data_policy;                
                                $top_pick[$key]['volume'] = $get_data_volume;

                                //feature image
                                if($value['feature_img']){

                                    $top_pick[$key]['feature_img'] = $this->image_url_product( $value['feature_img']);    
                                    $top_pick[$key]['feature_img_thumb'] = $this->image_url_product( $value['feature_img'] ,'thumb');    
                                }
                                else {
                                    $top_pick[$key]['feature_img'] = '';
                                    $top_pick[$key]['feature_img_thumb'] = '';
                                }
                                
                                array_push($top_products, $top_pick[$key]);
                            } 
                        }                          
                    }
                }
		
                if(!empty($top_products)) {
		    return $top_products;
                }
		else {
		    return false;
		}
            }
            else {
                return false;
            }
        }
        else {
            $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", false)
                    ->from("products")
                    ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1')
                    ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1')
                    ->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
		    ->where("products.top_pick", 1)
                    ->where("products.status", 1)
                    ->limit(20)
		    ->group_by('products.product_id')
                    ->order_by("products.updated_date", "desc");
            $top_pick = $this->db->get()
                    ->result_array();

            if (!empty($top_pick)) {            

                //print_r($top_pick); exit;
                foreach($top_pick as $key => $value){  
		    
		    $msg = str_replace(PHP_EOL,"@/@", $top_pick[$key]["description"]);
		    $top_pick[$key]["description"] = json_decode('"'.$msg.'"');
		    $top_pick[$key]["description"] = str_replace("@/@",PHP_EOL, $top_pick[$key]["description"]);

                    if($value["seller_id"] != 0) {
                        //seller info
                        $seller = $this->db->select("seller_id, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender", false)
                                ->where('seller_id', $value['seller_id'])
                                ->where('status', 1)
                                ->where('is_admin_verified', 1)
                                ->get('seller')->row_array();

                        $seller2 = array_map(function($val) {
                            if(is_null($val)) {
                                $val = "";
                            }
                            return $val;
                        }, $seller);

                        $top_pick[$key]['seller'] = $seller2;
                    }
                    else {
                        $top_pick[$key]['seller'] = array(
                            'seller_name' => "Admin"
                        );
                    }

                    //get favourite
                    $favourite = $this->db->select("user_id, product_id, status")
                            ->where('product_id', $value['product_id'])
                            ->where('user_id', $post['user_id'])
                            ->get('product_favourite')->row_array();


                    if(!empty($favourite)){
                        $top_pick[$key]['is_favourite'] = $favourite["status"];
                    }
                    else {
                        $top_pick[$key]['is_favourite'] = 0;
                    }

                    //Get Product Volume
                    $this->db->select("product_details.*, concat(volume_mst.volumne_value,' ',(select volume_type from volume_type where volume_type_id = volume_mst.type)) as volumes", false)
                            ->from("products")
                            ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
                            ->join('volume_mst', 'product_details.volume_id=volume_mst.volume_id and volume_mst.status=1')
                            ->where("products.product_id", $value['product_id'])
                            //->where("volume_mst.brand_id", $value['brand_id'])
                            ->where("products.status", 1)
                            ->order_by("products.date", "desc");
                    $get_data_volume = $this->db->get()
                            ->result_array();

                    if(!empty($get_data_volume)){ 

                        $top_pick[$key]["price"] = $get_data_volume[0]["actual_price"];

                        foreach($get_data_volume as $vkey => $vvalue){ 
                            $get_data_volume[$vkey]["actual_price"] = $vvalue["actual_price"];
                        }
                        $top_pick[$key]['isvolume'] = true;
                    } 
                    else {
                        $top_pick[$key]['isvolume'] = false;
                    }

                    //Get Product Return policy
                    $this->db->select("product_return_policy.*", false)
                            ->from("products")
                            ->join('product_return_policy', 'products.product_id = product_return_policy.product_id')
                            ->where("products.product_id", $value['product_id'])
                            ->where("products.have_return_policy", 1)
                            ->where("products.status", 1)
                            ->order_by("products.date", "desc");
                    $get_data_policy = $this->db->get()
                                ->row_array();

                    if(!empty($get_data_policy)){
                        $get_data_policy["description"] = html_entity_decode(strip_slashes(strip_tags($get_data_policy["description"], '<strong>')));
                        $get_data_policy["status"] = true;
                    } else {
                        $get_data_policy["status"] = false;
                    }

                    $top_pick[$key]['return_policy'] = $get_data_policy;                
                    $top_pick[$key]['volume'] = $get_data_volume;

                    //feature image
                    if($value['feature_img']){

                        $top_pick[$key]['feature_img'] = $this->image_url_product( $value['feature_img'] );    
                        $top_pick[$key]['feature_img_thumb'] = $this->image_url_product( $value['feature_img'] ,'thumb');    
                    }
                    else {
                        $top_pick[$key]['feature_img'] = '';
                        $top_pick[$key]['feature_img_thumb'] = '';
                    }

                }        

		return $top_pick;            
            }        
            else {
                return false;
            }
        }
    }
    
    public function get_product_details_by_id($post=[]){
        if(isset($post['product_id']) && $post['product_id'] != NULL){
            //Get Product Details
            $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", false)
                    ->from("products")
                    ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1')
                    ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1')
                    ->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
		    ->where("products.status", 1)
                    ->where("products.product_id", $post['product_id'])
                    ->group_by('products.product_id')
                    ->order_by("products.date", "desc");
            $get_data = $this->db->get()
                    ->row_array();             
            
            if (!empty($get_data)) {
		
		$msg = str_replace(PHP_EOL,"@/@", $get_data["description"]);
		$get_data["description"] = json_decode('"'.$msg.'"');
		$get_data["description"] = str_replace("@/@",PHP_EOL, $get_data["description"]);
		
                if($get_data["seller_id"] != 0) {
                    //seller info
                    $seller = $this->db->select("seller_id, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender", false)
                            ->where('seller_id', $get_data['seller_id'])
                            ->where('status', 1)
                            ->where('is_admin_verified', 1)
                            ->get('seller')->row_array();

                    $seller2 = array_map(function($val) {
                        if(is_null($val)) {
                            $val = "";
                        }
                        return $val;
                    }, $seller);
		    
		    //get open-close slot
		    $weekday = date('w', strtotime(date('Y-m-d')))+1; 
		    $seller_available = $this->db->select("*")
			    ->where("weekday", $weekday)
			    ->where("status", 1)
			    ->where("seller_id", $get_data['seller_id'])
			    ->get("trading_hours")->row_array();
		    
		    if(!empty($seller_available)) {	
			$seller2["start_time"] = $seller_available["start_time_utc"];
			$seller2["end_time"] = $seller_available["end_time_utc"];

			if(strtotime($seller_available["end_time_utc"]) > strtotime(date('H:i'))) {
			    $seller2["is_open"] = 1;
			}
			else {
			    $seller2["is_open"] = 0;
			}
		    }
		    else {
			$seller2["is_open"] = 0;
		    }

                    $get_data['seller'] = $seller2;
                }
                else {
                    $get_data['seller'] = array(
                        'seller_name' => "Admin"
                    );
                }
                
                //seller rating
                $post["seller_id"] = $get_data['seller_id'];
                $get_data['seller_rating'] = $this->get_seller_rating($post);
                
                //get favourite
                $favourite = $this->db->select("user_id, product_id, status")
                        ->where('product_id', $post['product_id'])
                        ->where('user_id', $post['user_id'])
                        ->get('product_favourite')->row_array();
                
                
                if(!empty($favourite)){
                    $get_data['is_favourite'] = $favourite["status"];
                }
                else {
                    $get_data['is_favourite'] = 0;
                }
                
                //Get Product Volume
                $this->db->select("product_details.*, concat(volume_mst.volumne_value,' ',(select volume_type from volume_type where volume_type_id = volume_mst.type)) as volumes", false)
                        ->from("products")
                        ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
                        ->join('volume_mst', 'product_details.volume_id=volume_mst.volume_id and volume_mst.status=1')
                        ->where("products.product_id", $post['product_id'])
                        ->where("products.status", 1)
                        ->order_by("products.date", "desc");
                $get_data_volume = $this->db->get()
                        ->result_array();
                
                if(!empty($get_data_volume)){                 
                    $get_data["price"] = $get_data_volume[0]["actual_price"];

                    foreach($get_data_volume as $vkey => $vvalue){ 
                        $get_data_volume[$vkey]["actual_price"] =  $vvalue["actual_price"];
                    }
                    
                    $get_data['isvolume'] = true;
                }
                else {
                    $get_data['isvolume'] = false;
                }

                //Get Product Return policy
                $this->db->select("product_return_policy.*", false)
                        ->from("products")
                        ->join('product_return_policy', 'products.product_id = product_return_policy.product_id')
                        ->where("products.product_id", $post['product_id'])
                        ->where("products.have_return_policy", 1)
                        ->where("products.status", 1)
                        ->order_by("products.date", "desc");
                $get_data_policy = $this->db->get()
                        ->row_array();    
                
                if(!empty($get_data_policy)){
                    $get_data_policy["description"] = html_entity_decode(strip_slashes(strip_tags($get_data_policy["description"], '<strong>')));
                    $get_data_policy["status"] = true;
                } else {
                    $get_data_policy["status"] = false;
                }
                
                //Get Product Gallery
                $get_data_images = $this->db->select("product_images.image_name", false)
                        ->join('product_images', 'products.product_id = product_images.product_id and product_images.status=1')
                        ->where("products.product_id", $post['product_id'])
                        ->where("products.status", 1)
                        ->order_by("products.date", "desc")
                        ->get("products")->result_array();
                                
                if(!empty($get_data_images)){ 
                    foreach($get_data_images as $key => $images){            
                        $get_data_images[$key]["image_name"] = $this->image_url_product( $images["image_name"]);
                    } 
                }                
                else {
                    $get_data_images[0]["image_name"] = $this->image_url_product( $get_data['feature_img']);
                }
                
                $userdata = $this->get_user_by_id($post["user_id"]);
        
                if($userdata["is_admin_verified"] == 1) {
                    $get_data['is_admin_verified'] = true;
                }
                else {
                    $get_data['is_admin_verified'] = false;
                }
                
                $image_path = $get_data['feature_img'];
                $get_data['volume_id'] = $get_data_volume;
                $get_data['return_policy'] = $get_data_policy;
                $get_data['gallery_img'] = $get_data_images;
                $get_data['feature_img'] = $this->image_url_product( $image_path);  
                $get_data['feature_img_thumb'] = $this->image_url_product( $image_path,'thumb'); 

                //get loyalty point of user
                $userdata = $this->get_user_by_id($post["user_id"]);  
                if($userdata["loyalty_point"] > 0) {
                    $get_data['loyalty_eligible'] = true;
                } else {
                    $get_data['loyalty_eligible'] = false;
                }
                
                //get points allowed to become
                $vip_eligible_points = $this->db->select("*")->where('key', 'vip_loyalty_points')->get('setting')->row_array();
                if($userdata["is_vip_club_member"] === 1 && $userdata["loyalty_point"] > $vip_eligible_points["value"]["vip_loyalty_points"]) {
                    $get_data['vip_eligible'] = true;
                } else {
                    $get_data['vip_eligible'] = false;
                }  
                
                //product rating/review
                $average_rating = $this->db->select("AVG(rating) as product_rating")
                        ->where('product_id', $post['product_id'])
                        ->get('product_rating')->row_array();
                
                if(!empty($average_rating)) {
                    $get_data['product_rating'] = number_format($average_rating["product_rating"],1);
                }
                else {
                    $get_data['product_rating'] = 0;
                }
                
                //top 2 review
                $reviews = $this->db->select("product_rating.*, concat(user.firstname,' ',user.lastname) as name", false)
                        ->join('user', 'user.user_id = product_rating.user_id')
                        ->where('product_rating.product_id', $post["product_id"])
                        ->order_by('product_rating.rating', 'desc')
                        ->order_by('product_rating.date', 'desc')
                        ->limit(2)
                        ->get('product_rating')->result_array();
                
                if(!empty($reviews)){            
                    foreach($reviews as $key1 => $value1) {                        
                        
                        $value2 = array_map(function($val) {
                            if(is_null($val)) {
                                $val = "";
                            }
                            return $val;
                        }, $value1);
                        $reviews[$key1] = $value2;  
                        $reviews[$key1]["rating"] = number_format($value2["rating"],1);
                    }
                    
                    $get_data['product_review_list'] = $reviews;
                }
                else {
                    $get_data['product_review_list'] = [];
                }
                
                $count_review = $this->db->select("*")
                        ->where('product_id', $post['product_id'])
                        ->get('product_rating')->num_rows();
                
                $get_data['total_product_reviews'] = $count_review;
                
                //show review list
                if($count_review > 2){
                    $get_data['show_review_list'] = true;
                }
                else {
                    $get_data['show_review_list'] = false;
                }
                
                $get_data = array_map(function($val) {
                    if(is_null($val)) {
                        $val = "";
                    }
                    return $val;
                }, $get_data);
                
                return $get_data;
            } 

        }        
    }     
    
    public function get_products_list($post = []){
        $product_array = array();
        $new_array = array();
        
        //with abv parameter
        if( (isset($post["abv_max"]) && $post["abv_max"] != NULL) && (isset($post["abv_min"]) && $post["abv_min"] != NULL) ) {
            //$abv = ' and products.abv_percent IN ('.$post["abv"].')';             
	    $abv = ' and products.abv_percent BETWEEN '.$post["abv_min"].' AND '.$post["abv_max"].'';   
        }
        else {
            $abv = '';                        
        }
        
        //with country parameter
        if(isset($post["country"]) && $post["country"] != NULL ) {
            $country = ' and products.country_id LIKE "'.$post["country"].'%"';             
        }
        else {
            $country = '';                        
        }
        
        //with category parameter
        if(isset($post["category"]) && $post["category"] != NULL ) {
            $categoryid = ' and products.category_id = '.$post["category"].'';             
        }
        else {
            $categoryid = '';                        
        }
        
        //with min & max amount parameter
        if( (isset($post["min_amt"]) && $post["min_amt"] != NULL) && (isset($post["max_amt"]) && $post["max_amt"] != NULL) ) {
            $price = ' and product_details.normal_sell_price BETWEEN '.$post["min_amt"].' AND '.$post["max_amt"].'';             
        }
        else {
            $price = '';                        
        }
        
        //with sub-category parameter
        if(isset($post["subcategory"]) && $post["subcategory"] != NULL ) {
            $categoryid = ' and products.category_id = '.$post["subcategory"].'';             
        }
        else {
            $categoryid = '';                        
        }
        
        //with brand parameter
        if(isset($post["brand"]) && $post["brand"] != NULL ) {
            $brandid = 'and (';
            $i=1;
            $br_arr = explode(',',$post["brand"]);
            
            foreach($br_arr as $value){
                $brandid .= ' products.brand_id = '.$value.''; 
                if($i < count($br_arr)){
                    $brandid .= ' or ';
                }
                $i++;
            }    
            $brandid .= ')';
        }
        else {
            $brandid = '';                        
        }
        
        //with volume parameter
        if( (isset($post["volume_max"]) && $post["volume_max"] != NULL) && (isset($post["volume_min"]) && $post["volume_min"] != NULL) && (isset($post["volume_type"]) && $post["volume_type"] != NULL) ) {
	    
	    if(isset($post["brand"]) && $post["brand"] != NULL ) {
		$brandids = ' AND brand_id IN ('.$post["brand"].')';  
	    }
	    else {
		$brandids = '';                        
	    }
	    
	    $where_volume_mst = 'volumne_value BETWEEN '.$post["volume_min"].' AND '.$post["volume_max"].' and type = '.$post["volume_type"].''.$brandids.'';
	    
	    $get_volume = $this->db->select("volume_id", false)
		    ->where($where_volume_mst)
		    ->get("volume_mst")->result_array();
	    
	    if(!empty($get_volume)) {	
		$get_volume_arr = array_column($get_volume, "volume_id");
		$volumes = implode(",", $get_volume_arr);
		
		$volumeid = ' and product_details.volume_id IN ('.$volumes.')';   
	    }
	    else {
		$volumeid = '';
	    }
        }
        else {
            $volumeid = '';                        
        }
        
        $prod_where = "products.status = 1 ".$abv." ".$country."";
        
        if(!empty($post["latitude"]) || !empty($post["longitude"])) {
	    $latitude = number_format($post["latitude"], 6);
	    $longitude = number_format($post["longitude"], 6);
	    
            //find nearest seller
            $nearest_seller = $this->db->select('seller_id, seller_name, company_name, email, contact_no, address, latitude, longitude, case when gender=1 then "Male" when gender=2 then "Female" end as gender, get_distance_metres(latitude, longitude, '.$latitude.' , '.$longitude.') as distance', false)        
                                ->where('status', 1)
                                ->where('is_admin_verified', 1)
				->order_by("distance", "asc")
                                ->get('seller')->result_array();
	    
            if(!empty($nearest_seller)) {
		$seller_array = array();
                foreach($nearest_seller as $value1){
                    //calculate distance
                    $distance = round(($value1["distance"] / 1609.34), 2);
                   
		    $mile_limit = $this->get_mile_limit();
                    
                    //seller within 10 km
                    if($distance <= $mile_limit and $distance >= 0){
			
			//Sort by 1 = Chepeast, 2 = Fastest, 3 = Best Match
			if(isset($post["sort_by"]) && $post["sort_by"] == 1 ) {
			    $sort_by = 'min_amount asc, ';
			}
			else if(isset($post["sort_by"]) && $post["sort_by"] == 2 ) {
			    $sort_by = 'distance asc, ';
			}
			else if(isset($post["sort_by"]) && $post["sort_by"] == 3 ) {
			    $sort_by = 'min_amount asc, distance asc, ';
			}
			else {
			    $sort_by = '';
			}
			
			if(isset($post["top_pick"]) && $post["top_pick"] == 1 ) {
			    $prod_order = ''.$sort_by.'products.top_pick desc, products.date desc';
			}
			else {
			    $prod_order = ''.$sort_by.'products.date desc';
			}
			
			//get seller
			array_push($seller_array, $value1["seller_id"]);
			
                    }
                }
		
		$seller_array1 = implode(",", $seller_array);
		$prod_where = "products.seller_id IN (".$seller_array1.")";
		
		$this->db->select("products.*, product_details.volume_id, category_mst.category_name, brand_mst.brand_name, COALESCE(MIN(product_details.actual_price), 0) as min_amount, get_distance_metres(latitude, longitude, ".$latitude." , ".$longitude.") as distance", false);
	    }
	    else {
		return false;
	    }
        }
        else {
	    //Sort by 1 = Chepeast, 2 = Fastest, 3 = Bestest
	    if(isset($post["sort_by"]) && $post["sort_by"] == 1 ) {
		$sort_by = 'min_amount asc, ';
	    }
	    else {
		$sort_by = '';
	    }
	
	    if(isset($post["top_pick"]) && $post["top_pick"] == 1 ) {
		$prod_order = 'products.top_pick desc, products.date desc';
	    }
	    else {
		$prod_order = ''.$sort_by.'products.date desc';
	    }
			
            $this->db->select("products.*, product_details.volume_id, category_mst.category_name, brand_mst.brand_name, COALESCE(MIN(product_details.actual_price), 0) as min_amount", false);
        }
	
	
	$prod_list = $this->db->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1 '.$price.' '.$volumeid.' ')
		->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1 '.$categoryid.'')
		->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1 '.$brandid.'')
		->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
		->where($prod_where)
		->where('products.status', 1)
		->limit(LIMIT)
		->offset($post["offset"])
		->group_by('products.product_id')
		->order_by($prod_order)                                
		->get("products")->result_array();
	
	if(!empty($prod_list)){
	    $str = $this->db->last_query();
	    $str = str_replace('LIMIT '.LIMIT, '', $str);
	    $str = str_replace('OFFSET '.$post["offset"], '', $str);
	    
	    foreach($prod_list as $key => $value){ 

		$msg = str_replace(PHP_EOL,"@/@", $prod_list[$key]["description"]);
		$prod_list[$key]["description"] = json_decode('"'.$msg.'"');
		$prod_list[$key]["description"] = str_replace("@/@",PHP_EOL, $prod_list[$key]["description"]);

		if($value["seller_id"] != 0) {
		    //seller info
		    $seller = $this->db->select("seller_id, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender", false)
			    ->where('seller_id', $value['seller_id'])
			    ->where('status', 1)
			    ->where('is_admin_verified', 1)
			    ->get('seller')->row_array();

		    $seller2 = array_map(function($val) {
			if(is_null($val)) {
			    $val = "";
			}
			return $val;
		    }, $seller);

		    $prod_list[$key]['seller'] = $seller2;
		}
		else {
		    $prod_list[$key]['seller'] = array(
			'seller_name' => "Admin"
		    );
		}

		//get favourite
		$favourite = $this->db->select("user_id, product_id, status")
			->where('product_id', $value['product_id'])
			->where('user_id', $post['user_id'])
			->get('product_favourite')->row_array();

		if(!empty($favourite)){
		    $prod_list[$key]['is_favourite'] = $favourite["status"];
		}
		else {
		    $prod_list[$key]['is_favourite'] = 0;
		}

		//Get Product Volume
		$this->db->select("product_details.*, concat(volume_mst.volumne_value,' ',(select volume_type from volume_type where volume_type_id = volume_mst.type)) as volumes", false)
			->from("products")
			->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
			->join('volume_mst', 'product_details.volume_id=volume_mst.volume_id and volume_mst.status=1')
			->where("products.product_id", $value['product_id'])
			->where("products.status", 1)
			->order_by("products.date", "desc");
		$get_data_volume = $this->db->get()
			->result_array();

		//print_r($get_data_volume); exit;
		if(!empty($get_data_volume)){ 

		    $prod_list[$key]["price"] = number_format($get_data_volume[0]["normal_sell_price"], 2);
		    $prod_list[$key]["actual_price"] = number_format($get_data_volume[0]["actual_price"], 2);

		    foreach($get_data_volume as $vkey => $vvalue){ 
			$get_data_volume[$vkey]["actual_price"] = $vvalue["actual_price"];
		    }                    
		    $prod_list[$key]['isvolume'] = true;
		}
		else {
		    $prod_list[$key]['isvolume'] = false;
		}

		//Get Product Return policy
		$this->db->select("product_return_policy.*", false)
			->from("products")
			->join('product_return_policy', 'products.product_id = product_return_policy.product_id')
			->where("products.product_id", $value['product_id'])
			->where("products.have_return_policy", 1)
			->where("products.status", 1)
			->order_by("products.date", "desc");
		$get_data_policy = $this->db->get()
			    ->row_array();

		if(!empty($get_data_policy)){
		    $get_data_policy["description"] = html_entity_decode(strip_slashes(strip_tags($get_data_policy["description"], '<strong>')));
		    $get_data_policy["status"] = true;
		} else {
		    $get_data_policy["status"] = false;
		}

		$prod_list[$key]['return_policy'] = $get_data_policy;
		$prod_list[$key]['volume'] = $get_data_volume;

		if($value['feature_img']){                
		    $prod_list[$key]['feature_img'] = $this->image_url_product( $value['feature_img']);   
		    $prod_list[$key]['feature_img_thumb'] = $this->image_url_product( $value['feature_img'],'thumb');     
		}
		else {
		    $prod_list[$key]['feature_img'] = '';  
		    $prod_list[$key]['feature_img_thumb'] = '';  
		}

	    }
	}

	if (!empty($prod_list)) {
	    $qry = $this->db->query($str);
	    $count_prod = count($qry->result());
	   	    
	    $product_name = array_column($prod_list, 'product_name');
	    array_multisort($product_name, SORT_ASC, $prod_list);
	    $offset = LIMIT + $post["offset"];
	    if($count_prod > $offset) {
		$ret[0] = 1;
	    }
	    else {
		$ret[0] = 0;
	    }
	    $ret[1] = $offset;
	    $ret[2] = $prod_list;
	    return $ret;
	}        
	else {
	    return false;
	}
    }
    
    public function get_similar_products($post = []){
        $product_array = array();
        if(isset($post['product_id']) && $post['product_id'] != NULL){
            
            //current product        
            $current_product = $this->get_product_by_id($post["product_id"]);
            
            if($current_product){
                $where ="products.product_id != ".$post["product_id"]." and (products.category_id IN (".$current_product["category_id"].") OR products.brand_id IN (".$current_product["brand_id"].")) AND products.status=1";
		if(!empty($post["latitude"]) || !empty($post["longitude"])) {
                    //find nearest seller
                    $nearest_seller = $this->db->select('seller.seller_id, products.product_id, get_distance_metres(latitude, longitude, '.$post["latitude"].' , '.$post["longitude"].') as distance', false)        
                                        ->join("seller", "products.seller_id = seller.seller_id")
                                        ->where($where)
                                        ->where('products.status', 1)
                                        ->get('products')->result_array();

                    if(!empty($nearest_seller)) {
			$seller_array = array();
                        foreach($nearest_seller as $value1){
                            //calculate distance
                            $distance = round(($value1["distance"] / 1609.34), 2);
                           
			    $mile_limit = $this->get_mile_limit();
                            //seller within 10 km
                            if($distance <= $mile_limit and $distance >= 0){
                               //get seller
				array_push($seller_array, $value1["seller_id"]);   
                            }
                        }
			
			$seller_array1 = implode(",", $seller_array);
			$where = "products.seller_id IN (".$seller_array1.")";
                    }
                    else {
                        return false;
                    }
                }
		
		$prod_list = $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", false)
			    ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1', 'left')
                            ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1', 'left')
                            ->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
			    ->where($where)
			    ->where('products.status', 1)
                            ->limit(LIMIT)
			    ->offset($post["offset"])
			    ->group_by('products.product_id')
                            ->order_by("products.date", "desc")
			    ->get("products")
                            ->result_array(); 
		
		if(!empty($prod_list)){

		    foreach($prod_list as $key => $value){   

			$msg = str_replace(PHP_EOL,"@/@", $prod_list[$key]["description"]);
			$prod_list[$key]["description"] = json_decode('"'.$msg.'"');
			$prod_list[$key]["description"] = str_replace("@/@",PHP_EOL, $prod_list[$key]["description"]);

			if($value["seller_id"] != 0) {
			    //seller info
			    $seller = $this->db->select("seller_id, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender", false)
				    ->where('seller_id', $value['seller_id'])
				    ->where('status', 1)
				    ->where('is_admin_verified', 1)
				    ->get('seller')->row_array();

			    $seller2 = array_map(function($val) {
				if(is_null($val)) {
				    $val = "";
				}
				return $val;
			    }, $seller);

			    $prod_list[$key]['seller'] = $seller2;
			}
			else {
			    $prod_list[$key]['seller'] = array(
				'seller_name' => "Admin"
			    );
			}

			//get favourite
			$favourite = $this->db->select("user_id, product_id, status")
				->where('product_id', $value['product_id'])
				->where('user_id', $post['user_id'])
				->get('product_favourite')->row_array();


			if(!empty($favourite)){
			    $prod_list[$key]['is_favourite'] = $favourite["status"];
			}
			else {
			    $prod_list[$key]['is_favourite'] = 0;
			}

			//Get Product Volume
			$this->db->select("product_details.*, concat(volume_mst.volumne_value,' ',(select volume_type from volume_type where volume_type_id = volume_mst.type)) as volumes", false)
				->from("products")
				->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
				->join('volume_mst', 'product_details.volume_id=volume_mst.volume_id and volume_mst.status=1')
				->where("products.product_id", $value['product_id'])
				->where("products.status", 1)
				->order_by("products.date", "desc");
			$get_data_volume = $this->db->get()
				->result_array();

			if(!empty($get_data_volume)){ 

			    $prod_list[$key]["price"] = number_format($get_data_volume[0]["normal_sell_price"], 2);

			    $prod_list[$key]["actual_price"] = number_format($get_data_volume[0]["actual_price"], 2);

			    foreach($get_data_volume as $vkey => $vvalue){ 
				$get_data_volume[$vkey]["actual_price"] = $vvalue["actual_price"];
			    }
			    $prod_list[$key]['isvolume'] = true;
			}
			else {
			    $prod_list[$key]['isvolume'] = false;
			}
			//Get Product Return policy
			$this->db->select("product_return_policy.*", false)
				->from("products")
				->join('product_return_policy', 'products.product_id = product_return_policy.product_id')
				->where("products.product_id", $value['product_id'])
				->where("products.have_return_policy", 1)
				->where("products.status", 1)
				->order_by("products.date", "desc");
			$get_data_policy = $this->db->get()
				    ->row_array();

			if(!empty($get_data_policy)){
			    $get_data_policy["description"] = html_entity_decode(strip_slashes(strip_tags($get_data_policy["description"], '<strong>')));
			    $get_data_policy["status"] = true;
			} else {
			    $get_data_policy["status"] = false;
			}

			$prod_list[$key]['return_policy'] = $get_data_policy;                        
			$prod_list[$key]['volume'] = $get_data_volume;

			// images
			if($value['feature_img']){                 
			    $prod_list[$key]['feature_img'] = $this->image_url_product( $value['feature_img']);   
			    $prod_list[$key]['feature_img_thumb'] = $this->image_url_product( $value['feature_img'],'thumb');     
			}
			else {
			    $prod_list[$key]['feature_img'] = '';  
			    $prod_list[$key]['feature_img_thumb'] = '';  
			}            
		    }
		}

		if (!empty($prod_list)) {			
		    $count_prod =  $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", false)
			    ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1', 'left')
                            ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1', 'left')
                            ->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
			    ->where($where)
			    ->where('products.status', 1)
			    ->group_by('products.product_id')
                            ->order_by("products.date", "desc")
			    ->get("products")->num_rows();

		    $offset = LIMIT + $post["offset"];
		    if($count_prod > $offset) {
			$ret[0] = 1;
		    }
		    else {
			$ret[0] = 0;
		    }
		    $ret[1] = $offset;
		    $ret[2] = $prod_list;
		    return $ret;       
		}        
		else {
		    return false;
		}
            }
            else {
                return false;
            }
        }        
        else {
            return false;
        }
    }
    
    public function get_top_brands($post=[]){
       
        $top_brands = $this->db->select("*", FALSE)            
            ->where("status", 1)
            ->where("is_top_brand", 1)
	    ->limit(LIMIT)
	    ->offset($post["offset"])
	    ->get('brand_mst')
            ->result_array();   
	
	if(!empty($top_brands)) {        
	    foreach($top_brands as $key => $value){    

		if($value['slider_img']){
		    $top_brands[$key]['brand_logo'] = $this->s3_url( $value['brand_logo'],'', 'brand');    
		    $top_brands[$key]['brand_logo_thumb'] = $this->s3_url( $value['brand_logo'] ,'thumb', 'brand');    
		    $top_brands[$key]['slider_img'] = $this->s3_url( $value['slider_img'],'', 'brand');    
		    $top_brands[$key]['slider_img_thumb'] = $this->s3_url( $value['slider_img'] ,'thumb', 'brand');    
		}
		else {
		    $top_brands[$key]['brand_logo'] = '';
		    $top_brands[$key]['brand_logo_thumb'] = '';
		    $top_brands[$key]['slider_img'] = '';
		    $top_brands[$key]['slider_img_thumb'] = '';
		}            
	    }
	    
	    $count_prod =  $this->db->select("*", FALSE)            
		    ->where("status", 1)
		    ->where("is_top_brand", 1)
		    ->get('brand_mst')
		    ->num_rows();
	    
	    $offset = LIMIT + $post["offset"];
	    if($count_prod > $offset) {
		$ret[0] = 1;
	    }
	    else {
		$ret[0] = 0;
	    }
	    $ret[1] = $offset;
	    $ret[2] = $top_brands;
	    return $ret;           
        }        
        else {
            return false;
        }
    }
    
    public function get_volume_list_by_product($post=[]){
        
        $this->db->select("product_details.volume_id, product_details.product_id, product_details.actual_price, CONCAT(volume_mst.volumne_value,' ',(select volume_type from volume_type where volume_type_id = volume_mst.type)) as volume", FALSE)
            ->from('product_details')
            ->join('volume_mst', 'product_details.volume_id=volume_mst.volume_id and volume_mst.status=1')
            ->where('product_details.status', 1)
            ->where('product_details.product_id', $post["product_id"]);
        $get_volume_list = $this->db->get()
                ->result_array();   

        if (!empty($get_volume_list)) {
            return $get_volume_list;            
        }        
        else {
            return false;
        }
    }
    
    public function get_product_search_list($post=[]){  
        $product_array = array();        
	if(isset($post["chr"]) && $post["chr"] != NULL ) {
	    $where = 'products.status = 1 AND (category_mst.category_name LIKE "%'.$post["chr"].'%" OR brand_mst.brand_name LIKE "%'.$post["chr"].'%" OR products.product_name LIKE "%'.$post["chr"].'%")';             
	}
	else {
	    $where = "products.status = 1 ";                       
	}

        $prod_list = $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", FALSE)
	    ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1', 'left')
	    ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1', 'left')
	    ->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
	    ->where($where)
	    ->limit(LIMIT)
	    ->offset($post["offset"])
	    ->order_by("products.date", "desc")
	    ->get('products')
	    ->result_array();  

	if (!empty($prod_list)) {

	    foreach($prod_list as $key => $value){   

		if($value["seller_id"] != 0) {
		    //seller info
		    $seller = $this->db->select("seller_id, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender", false)
			    ->where('seller_id', $value['seller_id'])
			    ->where('status', 1)
			    ->where('is_admin_verified', 1)
			    ->get('seller')->row_array();

		    $seller2 = array_map(function($val) {
			if(is_null($val)) {
			    $val = "";
			}
			return $val;
		    }, $seller);

		    $prod_list[$key]['seller'] = $seller2;
		}
		else {
		    $prod_list[$key]['seller'] = array(
			'seller_name' => "Admin"
		    );
		}

		//get favourite
		$favourite = $this->db->select("user_id, product_id, status")
			->where('product_id', $value['product_id'])
			->where('user_id', $post['user_id'])
			->get('product_favourite')->row_array();


		if(!empty($favourite)){
		    $prod_list[$key]['is_favourite'] = $favourite["status"];
		}
		else {
		    $prod_list[$key]['is_favourite'] = 0;
		}

		//Get Product Volume
		$this->db->select("product_details.*, concat(volume_mst.volumne_value,' ',(select volume_type from volume_type where volume_type_id = volume_mst.type)) as volumes", false)
			->from("products")
			->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
			->join('volume_mst', 'product_details.volume_id=volume_mst.volume_id and volume_mst.status=1')
			->where("products.product_id", $value['product_id'])
			->where("products.status", 1)
			->order_by("products.date", "desc");
		$get_data_volume = $this->db->get()
			->result_array();

		if(!empty($get_data_volume)){            
		    foreach($get_data_volume as $vkey => $vvalue){ 
			$get_data_volume[$vkey]["actual_price"] =  $vvalue["actual_price"];
		    }

		    //get price for volume
		    $prod_list[$key]["price"] = $get_data_volume[0]["actual_price"];
		    $prod_list[$key]['isvolume'] = true;
		}
		else {
		    $prod_list[$key]['isvolume'] = false;
		}

		//Get Product Return policy
		$this->db->select("product_return_policy.*", false)
			->from("products")
			->join('product_return_policy', 'products.product_id = product_return_policy.product_id')
			->where("products.product_id", $value['product_id'])
			->where("products.have_return_policy", 1)
			->where("products.status", 1)
			->order_by("products.date", "desc");
		$get_data_policy = $this->db->get()
			    ->row_array();

		if(!empty($get_data_policy)){
		    $get_data_policy["description"] = html_entity_decode(strip_slashes(strip_tags($get_data_policy["description"], '<strong>')));
		    $get_data_policy["status"] = true;
		} else {
		    $get_data_policy["status"] = false;
		}

		$prod_list[$key]['return_policy'] = $get_data_policy;
		$prod_list[$key]['volume'] = $get_data_volume;

		if($value['feature_img']){

		    $prod_list[$key]['feature_img'] = $this->image_url_product( $value['feature_img']);   
		    $prod_list[$key]['feature_img_thumb'] = $this->image_url_product( $value['feature_img'],'thumb');     
		}
		else {
		    $prod_list[$key]['feature_img'] = '';  
		    $prod_list[$key]['feature_img_thumb'] = '';  
		}

	    }

	    $count_prod = $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", FALSE)
	    ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1', 'left')
	    ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1', 'left')
	    ->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
	    ->where($where)
	    ->order_by("products.date", "desc")
	    ->get('products')
		    ->num_rows();
	    
	    $offset = LIMIT + $post["offset"];
	    if($count_prod > $offset) {
		$ret[0] = 1;
	    }
	    else {
		$ret[0] = 0;
	    }
	    $ret[1] = $offset;
	    $ret[2] = $prod_list;
	    return $ret;              
	}        
	else {
	    return false;
	}
    }
    
    public function get_product_return_policy($post=[]){        
        
        $this->db->select("*", FALSE)
            ->from('product_return_policy')
            ->where('product_id', $post["product_id"]);
        $return_policy = $this->db->get()
                ->row_array();   
        
        if (!empty($return_policy)) {
            return $return_policy;            
        }        
        else {
            return false;
        }
    }
    
    function check_zone($seller_id, $user_latitude, $user_longitude, $shipping_id) {
//	if($shipping_id > 0) {
//	    $shipping = $this->db->select("*")
//		    ->where("shipping_id", $shipping_id)
//		    ->get("shipping_mst")->row_array();
//	    
//	    if(!empty($shipping)) {	    
//		$latitude = $shipping["latitude"];
//		$longitude = $shipping["longitude"];
//	    }
//	    else {
//		$latitude = $user_latitude;
//		$longitude = $user_longitude;
//	    }
//	}
//	else {
	    $latitude = $user_latitude;
	    $longitude = $user_longitude;
//	}
	//get seller
	$get_seller = $this->db->select('seller_id, latitude, longitude, get_distance_metres(latitude, longitude, '.$latitude.', '.$longitude.') as distance', false)
                        ->where('seller_id', $seller_id)
			->where("status", 1)
                        ->get('seller')->row_array(); 
	
	if(!empty($get_seller)) {
	    $distance = round(($get_seller["distance"] / 1609.34), 2);
                    
    	    $mile_limit = $this->get_mile_limit();
	    //user within miles
	    if($distance <= $mile_limit && $distance >= 0){
		return 1;
	    }
	    else {
		return 2;
	    }
	}
    }
    
    //Cart
    public function add_to_bag($post=[]){  
        
        $userdata = $this->get_user_by_id($post["user_id"]);
        $seller_array = array();
                    
        if($userdata["is_admin_verified"] == 1) {
	    
	    //get seller
	    $get_seller = $this->db->select('seller_id', false)
		->where('product_id', $post["product_id"])
		->get('products')->row_array();
		    
	    //check wheather product falls in zone?
	    $check_zone = $this->check_zone($get_seller["seller_id"], $userdata["latitude"], $userdata["longitude"], $userdata["shipping_id"]);
	    
	    if($check_zone == 1) {        
		$cart_id = $this->check_cart_exists($post["user_id"]);

		//Check Exists
		$exists = $this->db->select('*', false)
		    ->where('product_id', $post["product_id"])
		    ->where('volume_id', $post["volume_id"])
		    ->where('cart_id', $cart_id)
		    ->get('cart_product')->row_array();

		if(empty($exists)) { 
		    $check_qty = $this->check_quantity($post["product_id"], $post["volume_id"], $post["qty"]);

		    if($check_qty){

			//get price from volume_mst
			$get_price = $this->db->select('*', false)
			    ->where('volume_id', $post["volume_id"])
			    ->where('product_id', $post["product_id"])
			    ->get('product_details')->row_array(); 

			$inloyalty = $this->check_in_loyalty_club($post["product_id"]);
			$invip = $this->check_in_vip_club($post["product_id"]); 

			if($inloyalty == true) {                          
			    if($post["from_where"] == 1){
				//calculate total
				$total = $post["qty"] * $get_price["normal_sell_price"];

				//Insert into cart product
				$insert_cart_product = array(
				    'cart_id' => $cart_id,
				    'product_id' => $post["product_id"],
				    'seller_id' => $get_seller["seller_id"],
				    'volume_id' => $post["volume_id"],
				    'price' => $get_price["normal_sell_price"],
				    'qty' => $post["qty"],
				    'total' => $total,
				    'from_where' => $post["from_where"],
				    'allow_split_order' => 0,
				);
			    }
			    else {
				//calculate total
				$total = $post["qty"] * $get_price["loyalty_club_sell_price"];

				//Insert into cart product
				$insert_cart_product = array(
				    'cart_id' => $cart_id,
				    'product_id' => $post["product_id"],
				    'seller_id' => $get_seller["seller_id"],
				    'volume_id' => $post["volume_id"],
				    'price' => $get_price["loyalty_club_sell_price"],
				    'qty' => $post["qty"],
				    'total' => $total,
				    'from_where' => $post["from_where"],
				    'allow_split_order' => 0,
				);
			    }
			}
			else if($invip == true) {   
			    if($post["from_where"] == 1){
				//calculate total
				$total = $post["qty"] * $get_price["normal_sell_price"];

				//Insert into cart product
				$insert_cart_product = array(
				    'cart_id' => $cart_id,
				    'product_id' => $post["product_id"],
				    'seller_id' => $get_seller["seller_id"],
				    'volume_id' => $post["volume_id"],
				    'price' => $get_price["normal_sell_price"],
				    'qty' => $post["qty"],
				    'total' => $total,
				    'from_where' => $post["from_where"],
				    'allow_split_order' => 0,
				);
			    }
			    else {
				//calculate total
				$total = $post["qty"] * $get_price["vip_club_sell_price"];

				//Insert into cart product
				$insert_cart_product = array(
				    'cart_id' => $cart_id,
				    'product_id' => $post["product_id"],
				    'seller_id' => $get_seller["seller_id"],
				    'volume_id' => $post["volume_id"],
				    'price' => $get_price["vip_club_sell_price"],
				    'qty' => $post["qty"],
				    'total' => $total,
				    'from_where' => $post["from_where"],
				    'allow_split_order' => 0,
				);
			    }
			}
			else if($get_price["normal_discount"] != 0 && $get_price["normal_sell_price"] != 0) {  
			    //calculate total
			    $total = $post["qty"] * $get_price["normal_sell_price"];

			    //Insert into cart product
			    $insert_cart_product = array(
				'cart_id' => $cart_id,
				'product_id' => $post["product_id"],
				'seller_id' => $get_seller["seller_id"],
				'volume_id' => $post["volume_id"],
				'price' => $get_price["normal_sell_price"],
				'qty' => $post["qty"],
				'total' => $total,
				'from_where' => $post["from_where"],
				'allow_split_order' => 0,
			    );
			}
			else {  
			    //calculate total
			    $total = $post["qty"] * $get_price["normal_sell_price"];

			    //Insert into cart product
			    $insert_cart_product = array(
				'cart_id' => $cart_id,
				'product_id' => $post["product_id"],
				'seller_id' => $get_seller["seller_id"],
				'volume_id' => $post["volume_id"],
				'price' => $get_price["normal_sell_price"],
				'qty' => $post["qty"],
				'total' => $total,
				'from_where' => $post["from_where"],
				'allow_split_order' => 0,
			    );
			}

			$insert_cp = $this->db->insert('cart_product', $insert_cart_product);
			$last_id = $this->db->insert_id();

			$exists2 = $this->db->select('seller_id', false)
			    ->where('cart_id', $cart_id)
			    ->get('cart_product')->result_array();

			$seller_array = array_column($exists2, "seller_id");
			$count = array_count_values($seller_array);
			//print_r($count);
			//echo count($exists2);
			if(count($exists2) > 1){

			    foreach($exists2 as $k => $v){   

				if(!empty($exists2) && $count[$v["seller_id"]] == 1){ 
				    $allow_split_order = 1;
				}
				else if(!empty($exists2) && $count[$v["seller_id"]] > 1){ 
				    $allow_split_order = 0;
				}   
				$this->db->set("allow_split_order", $allow_split_order)->where("cart_id", $cart_id)->update('cart_product');
			    }
			}

			$this->calculate_cart_total($cart_id, $post["user_id"]);

			if($insert_cp){
			    return 3;
			} 
			else {
			    return 4;
			}            
		    } 
		    else {
			return 2;
		    }
		} 
		else {
		    return 1;
		}
	    }
	    else if($check_zone == 2) {
		return 6;
	    }
        }
        else {
            return 5;
        }
    }
    
    public function update_bag($post=[]){         
        
        $cart = $this->get_cart($post["user_id"]);
        
        //Check Exists
        $exists = $this->db->select('*', false)
            ->where('product_id', $post["product_id"])
            ->where('volume_id', $post["volume_id"])
            ->where('cart_id', $cart["cart_id"])
            ->get('cart_product')->row_array();
        
        if(!empty($exists)){  
            //check quantity
            $check_qty = $this->check_quantity($post["product_id"], $post["volume_id"], $post["qty"]);
            
            if($check_qty){  
                $new_post = array(
                    'product_id' => $post["product_id"],
                    'cart_id' => $cart["cart_id"],
                    'volume_id' => $post["volume_id"],
                    'qty' => $post["qty"]
                );
                //print_r($new_post); exit;
                $update_qty = $this->update_product_quantity($new_post);

                $this->calculate_cart_total($cart["cart_id"], $post["user_id"]);

                if($update_qty){
                    return 1;
                } else {
                    return 4;
                }
            } else {
                return 2;
            }
        }
        else {
            return 3;
        }
    }
    
    public function check_cart_exists($user_id){
        //Check cart exists
        $cart_exists = $this->db->select('*', false)
                ->where('user_id', $user_id)
                ->get('cart')->row_array();
        
        // print_r($cart_exists);
        
        if(empty($cart_exists))
        {
            // Insert into cart
            $insert_cart = array(
                'user_id' => $user_id,
            );

            $this->db->insert('cart', $insert_cart);
            $cart_id = $this->db->insert_id(); 
            return $cart_id;
        }
        else {
            return $cart_exists["cart_id"];
        }   
    }
    
    public function get_cart($user_id){
        //Check cart exists
        $cart = $this->db->select('*', false)
                ->where('user_id', $user_id)
                ->get('cart')->row_array();
        
        return $cart;
    }
    
    public function remove_product($post=[]) {  
        
        //get cart id
        $cart = $this->get_cart($post["user_id"]);
        
        if(!empty($cart)){
        
            //Check Exists
            $exists = $this->db->select('*', false)
                    ->where('product_id', $post["product_id"])
                    ->where('cart_id', $cart["cart_id"])
                    ->where('volume_id', $post["volume_id"])
                    ->get('cart_product')->row_array();

            if(!empty($exists))
            {
                $this->calculate_sub_cart_total($cart["cart_id"], $post["product_id"], $post["volume_id"]);

                $deletepro = $this->db
                    ->where('product_id', $post["product_id"])
                    ->where('cart_id', $cart["cart_id"])
                    ->where('volume_id', $post["volume_id"])
                    ->delete('cart_product');            

                if($deletepro) {
                    return true;
                }
                else {
                    return false;
                } 
            }    
        }
    }
    
    public function cart_product_list($post=[]) { 
        
        //get cart id
        $cart = $this->get_cart($post["user_id"]);
        
        if(!empty($cart)){        
            //Check Exists
            $get_cart_list = $this->db->select('cart_product.*, products.seller_id, products.product_name, products.feature_img, brand_mst.brand_name', false)
                    ->join('products', 'products.product_id = cart_product.product_id')
                    ->join('brand_mst', 'products.brand_id = brand_mst.brand_id', 'left')
                    ->where('cart_product.cart_id', $cart["cart_id"])
		    ->get('cart_product')->result_array(); 
	    
	    if(!empty($get_cart_list)) {
		
		$userdata = $this->get_user_by_id($post["user_id"]);
		$post["shipping_id"] = $userdata["shipping_id"];
		$shipping = $this->get_shipping_by_id($post);
		if(!empty($post["latitude"]) || !empty($post["longitude"])) {
		    $latitude = $post["latitude"];
		    $longitude = $post["longitude"];
		}		
		else if(!empty($shipping)) {
		    $latitude = $shipping["latitude"];
		    $longitude = $shipping["longitude"];
		}
		else if(!empty($userdata["latitude"]) || !empty($userdata["longitude"])) {
		    $latitude = $userdata["latitude"];
		    $longitude = $userdata["longitude"];
		}
		else {
		    $latitude = "";
		    $longitude = "";
		}

		foreach($get_cart_list as $key => $value){
		    $post_arr = array('product_id' => $value["product_id"]);

		    if($value["seller_id"] != 0) {
			if(!empty($latitude) || !empty($longitude)) {
			    //seller info
			    $seller = $this->db->select("seller_id, latitude, longitude, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender, get_distance_metres(latitude, longitude, ".$latitude." , ".$longitude.") as distance", false)
				    ->where('seller_id', $value['seller_id'])
				    ->where('status', 1)
				    ->where('is_admin_verified', 1)
				    ->get('seller')->row_array();

			    $distance_in_miles = round(($seller["distance"] / 1609.34), 1);

			    //get data for delivery charge calculation
			    $delivery_charge_details = $this->db->select("*")
				    ->where("FORMAT(miles, 1) = ".number_format($distance_in_miles, 1, ".", "")."")
				    ->get("delivery_charges")->row_array();

			    //calculate details
			    if(!empty($delivery_charge_details)) {
				$get_cart_list[$key]['delivery_charges'] = number_format($delivery_charge_details["base_rate"], 2);                        
			    }
			    else {
				$get_cart_list[$key]['delivery_charges'] = $this->get_delivery_charge();
			    }
			}
			else {
			    //seller info
			    $seller = $this->db->select("seller_id, latitude, longitude, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender", false)
				    ->where('seller_id', $value['seller_id'])
				    ->where('status', 1)
				    ->where('is_admin_verified', 1)
				    ->get('seller')->row_array();

			    $get_cart_list[$key]['delivery_charges'] = $this->get_delivery_charge();
			}

			$seller2 = array_map(function($val) {
			    if(is_null($val)) {
				$val = "";
			    }
			    return $val;
			}, $seller);

			$get_cart_list[$key]['seller'] = $seller2;
		    
			//get favourite
			$favourite = $this->db->select("user_id, product_id, status")
				->where('product_id', $value['product_id'])
				->where('user_id', $post['user_id'])
				->get('product_favourite')->row_array();


			if(!empty($favourite)){
			    $get_cart_list[$key]['is_favourite'] = $favourite["status"];
			}
			else {
			    $get_cart_list[$key]['is_favourite'] = 0;
			}

			//get current volume
			$volume = $this->get_volume_by_id($value["volume_id"]);
			$get_cart_list[$key]['volume'] = $volume["volume"];

			//get volume list by product
			$get_cart_list[$key]['volume_list'] = $this->get_volume_list_by_product($post_arr);

			if($value['feature_img']){                
			    $get_cart_list[$key]['feature_img'] = $this->image_url_product( $value['feature_img']);   
			    $get_cart_list[$key]['feature_img_thumb'] = $this->image_url_product( $value['feature_img'],'thumb');     
			}
			else {
			    $get_cart_list[$key]['feature_img'] = '';  
			    $get_cart_list[$key]['feature_img_thumb'] = '';  
			}		    
		    }
		}
            
		return $get_cart_list;   
            }
            else {
                return false;
            }   
        }
    }
    
    public function cart_product_checkout_list($post=[]) { 
        
        //get cart id
        $cart = $this->get_cart($post["user_id"]);
        
        if(!empty($cart)){        
            //Check Exists
            $get_cart_list = $this->db->select('cart_product.*, products.seller_id, products.product_name, products.feature_img, brand_mst.brand_name', false)
                    ->join('products', 'products.product_id = cart_product.product_id')
                    ->join('brand_mst', 'products.brand_id = brand_mst.brand_id', 'left')
                    ->where('cart_product.cart_id', $cart["cart_id"])
                    ->get('cart_product')->result_array();    
	    
	    $userdata = $this->get_user_by_id($post["user_id"]);
	    $post["shipping_id"] = $userdata["shipping_id"];
	    $shipping = $this->get_shipping_by_id($post);
	    if(!empty($shipping)) {
		$latitude = $shipping["latitude"];
		$longitude = $shipping["longitude"];
	    }
	    else if(!empty($userdata["latitude"]) || !empty($userdata["longitude"])) {
		$latitude = $userdata["latitude"];
		$longitude = $userdata["longitude"];		
	    }
	    else {
		$latitude = "";
		$longitude = "";
	    }

            foreach($get_cart_list as $key => $value){
                
                if($value["seller_id"] != 0) {
                    if(!empty($latitude) || !empty($longitude)) {
                        //seller info
                        $seller = $this->db->select("seller_id, latitude, longitude, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender, get_distance_metres(latitude, longitude, ".$latitude." , ".$longitude.") as distance", false)
                                ->where('seller_id', $value['seller_id'])
                                ->where('status', 1)
                                ->where('is_admin_verified', 1)
                                ->get('seller')->row_array();

                        $distance_in_miles = round(($seller["distance"] / 1609.34), 1);
                        
                        //get data for delivery charge calculation
                        $delivery_charge_details = $this->db->select("*")
				->where("FORMAT(miles, 1) = ".number_format($distance_in_miles, 1, '.', '')."")
				->get("delivery_charges")->row_array();
                        //print_r($delivery_charge_details); exit;
                        
                        //calculate details
                        if(!empty($delivery_charge_details)){
                            //$delivery_charge = $delivery_charge_details["base_rate"] + $delivery_charge_details["pay_driver_pickup"] + $delivery_charge_details["pay_driver_dropoff"];
                            $get_cart_list[$key]['delivery_charges'] = number_format($delivery_charge_details["base_rate"], 2);
                        }
                        else {
			    $get_cart_list[$key]['delivery_charges'] = $this->get_delivery_charge();
                        }
                    }
                    else {
                        //seller info
                        $seller = $this->db->select("seller_id, latitude, longitude, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender", false)
                                ->where('seller_id', $value['seller_id'])
                                ->where('status', 1)
                                ->where('is_admin_verified', 1)
                                ->get('seller')->row_array();
                        
                        $get_cart_list[$key]['delivery_charges'] = $this->get_delivery_charge();
                    }

                    $seller2 = array_map(function($val) {
                        if(is_null($val)) {
                            $val = "";
                        }
                        return $val;
                    }, $seller);
		    
		    //get open-close slot
		    $weekday = date('w', strtotime(date('Y-m-d')))+1; 		    
		    
		    $seller_available = $this->db->select("*")
			    ->where("weekday", $weekday)
			    ->where("status", 1)
			    ->where("seller_id", $value['seller_id'])
			    ->get("trading_hours")->row_array();
		    
		    if(!empty($seller_available)) {
			$seller2["start_time"] = $seller_available["start_time_utc"];
			$seller2["end_time"] = $seller_available["end_time_utc"];

			if(strtotime($seller_available["end_time_utc"]) > strtotime(date('H:i'))) {
			    $seller2["is_open"] = 1;
			}
			else {
			    $seller2["is_open"] = 0;
			}
		    }
		    else {
			$seller2["is_open"] = 0;
		    }

                    $get_cart_list[$key]['seller'] = $seller2;                		
                
		    if($value['feature_img']){                
			$get_cart_list[$key]['feature_img'] = $this->image_url_product( $value['feature_img']);
			$get_cart_list[$key]['feature_img_thumb'] = $this->image_url_product( $value['feature_img'],'thumb');     
		    }
		    else {
			$get_cart_list[$key]['feature_img'] = '';  
			$get_cart_list[$key]['feature_img_thumb'] = '';  
		    } 
		}
            }

            if(!empty($get_cart_list)) {
                return $get_cart_list;
            }
            else {
                return false;
            }   
        }
    }
    
    public function update_product_quantity($post=[]) { 
        
        /*$check_qty = $this->check_quantity($post["product_id"], $post["volume_id"], $post["qty"]);
        
        if($check_qty){*/
            //get current qty of product
            $new_qty = 0;
            $get_qty = $this->db->select("price, qty")
                ->where('product_id', $post["product_id"])
                ->where('cart_id', $post["cart_id"])
                ->where('volume_id', $post["volume_id"])
                ->get('cart_product')->row_array(); 

            $new_qty = $post["qty"];
            $total = $get_qty["price"] * $new_qty;

            $updateqty = $this->db
                ->set('qty', $new_qty)
                ->set('total', $total)
                ->where('product_id', $post["product_id"])
                ->where('cart_id', $post["cart_id"])
                ->where('volume_id', $post["volume_id"])
                ->update('cart_product');

            if($updateqty) {
                return true;
            }
            else {
                return false;
            } 
        /*} 
        else {
            return false;
        }*/
           
    }
    
    public function calculate_cart_total($cart_id, $user_id){
        
        $total_price = 0; $total_qty = 0; $total_amount = 0;
        //get cart details
        $get_cart_details = $this->db->select("*")
                ->where('cart_id', $cart_id)
                ->where('user_id', $user_id)
                ->get('cart')->row_array();
        
        if($get_cart_details){
            //get cart product details
            $get_cart_product_details = $this->db->select("*")
                ->where('cart_id', $cart_id)
                //->where('user_id', $user_id)
                ->get('cart_product')->result_array();
	    
	    
	    $userdata = $this->get_user_by_id($user_id);
	    $post["shipping_id"] = $userdata["shipping_id"];
	    $post["user_id"] = $user_id;
	    $shipping = $this->get_shipping_by_id($post);
	    if(!empty($shipping)) {
		$latitude = $shipping["latitude"];
		$longitude = $shipping["longitude"];
	    }
	    else if(!empty($userdata["latitude"]) || !empty($userdata["longitude"])) {
		$latitude = $userdata["latitude"];
		$longitude = $userdata["longitude"];		
	    }
	    else {
		$latitude = "";
		$longitude = "";
	    }
            
            foreach($get_cart_product_details as $key => $value) {
		
		if($value["seller_id"] > 0) {
                    if(!empty($latitude) || !empty($longitude)) {
                        //seller info
                        $seller = $this->db->select("seller_id, latitude, longitude, get_distance_metres(latitude, longitude, ".$latitude." , ".$longitude.") as distance", false)
                                ->where('seller_id', $value['seller_id'])
                                ->where('status', 1)
                                ->where('is_admin_verified', 1)
                                ->get('seller')->row_array();
			$distance_in_miles = round(($seller["distance"] / 1609.34), 1);
                       
                        //get data for delivery charge calculation
                        $delivery_charge_details = $this->db->select("*")
				->where("FORMAT(miles, 1) = ".number_format($distance_in_miles, 1, '.', '')."")
				->get("delivery_charges")->row_array();
			                        
                        //calculate details
                        if(!empty($delivery_charge_details)){
                            //$delivery_charge = $delivery_charge_details["base_rate"] + $delivery_charge_details["pay_driver_pickup"] + $delivery_charge_details["pay_driver_dropoff"];
                            $delivery_charges = number_format($delivery_charge_details["base_rate"], 2);
                        }
                        else {
			    $delivery_charges = $this->get_delivery_charge();
                        }
                    }
		    else {
			$delivery_charges = $this->get_delivery_charge();
		    }
		
		    $total_qty = $total_qty + $value["qty"];
		    $cal_amount = $value["price"] * $value["qty"];
		    $total_amount = $total_amount + $cal_amount;

		    $update_arr = array(
			'total_qty' => $total_qty,
			'total_amount' => $total_amount,
			'delivery_charge' => $delivery_charges,
		    );

		    $this->db->set($update_arr)
			    ->where('cart_id', $cart_id)
			    ->update('cart');		
		}
            }
        }
    }
    
    public function calculate_sub_cart_total($cart_id, $product_id, $volume_id) {        
        //get cart details
        $get_cart_details = $this->db->select("*")
                ->where('cart_id', $cart_id)
                ->get('cart')->row_array();
        
        if($get_cart_details){
            //get cart product details
            $get_cart_product_details = $this->db->select("*")
                ->where('cart_id', $cart_id)
                ->where('product_id', $product_id)
                ->where('volume_id', $volume_id)
                ->get('cart_product')->result_array();
            
            $total_qty = $get_cart_details["total_qty"];
            $total_amount = $get_cart_details["total_amount"];
            
            foreach($get_cart_product_details as $key => $value) {
                
                //$total_price = $total_price + $value["price"];
                $total_qty = $total_qty - ($value["qty"]);
                $cal_amount = $value["price"] * $value["qty"];
                $total_amount = $total_amount - $cal_amount;

                $update_arr = array(
                    //'total_price' => $total_price,
                    'total_qty' => $total_qty,
                    'total_amount' => $total_amount,
                );

                $this->db->set($update_arr)
                        ->where('cart_id', $cart_id)
                        ->update('cart');
            }
        }
    }
    
    //Shipping
    public function add_shipping($post=[]) { 
        
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

            if($insert_shipping) {
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
    
    public function remove_shipping_details($post=[]) {  
        
        //Check Exists
        $exists = $this->db->select('*', false)
                ->where('shipping_id', $post["shipping_id"])                
                ->get('shipping_mst')->row_array();
        
        if(!empty($exists))
        {            
            $deletepro = $this->db->set("status", 0)
                ->where('shipping_id', $post["shipping_id"])
                ->update('shipping_mst');
            
            if($deletepro) {
                return true;
            }
            else {
                return false;
            } 
        }     
    }
    
    public function get_shipping_details($post=[]) {
        $details = $this->db->select("*")
            ->where('user_id', $post["user_id"])
            ->where("status", 1)
            ->get('shipping_mst')->result_array();          
        
        $userdata = $this->get_user_by_id($post["user_id"]);
        
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
                $details[$key]["isaddress"] = true;
            }
            else {
                $details[$key]["isaddress"] = false;
            }
        }
        
        if(!empty($details)) {        
            return $details;
        }
        else {
            return false;
        }            
    }
    
    public function get_shipping_by_id_without_status($post=[]) {
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

                $userdata = $this->get_user_by_id($post["user_id"]);

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
    
    public function get_shipping_by_id($post=[]) {
        $details = $this->db->select("*")
            ->where('user_id', $post["user_id"])
            ->where('shipping_id', $post["shipping_id"])
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

                $userdata = $this->get_user_by_id($post["user_id"]);

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

                $userdata = $this->get_user_by_id($post["user_id"]);

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
    
    public function add_shipping_to_account($post=[]) {
        //check shipping details exists
        $check_exists = $this->db->select("*")
            ->where('shipping_id', $post["shipping_id"])
            ->where("status", 1)
            ->get('shipping_mst')->row_array();
        
        if(!empty($check_exists)){        
            $check_zipcode = $this->db->select("*")
                ->where('zipcode_id', $check_exists["zipcode_id"])
                ->get('zipcode')->row_array(); 

            if(!empty($check_zipcode)){    

                $ins_arr = array(
                    'shipping_id' => $post["shipping_id"],
                );
                $update = $this->db
                        ->set($ins_arr)
                        ->where('user_id', $post["user_id"])
                        ->update('user');

                if($update) {
                    return 1;
                }
                else {
                    return 2;
                } 
            } 
            else {
                return 3;
            } 
        } else {
            return false;
        }           
    }
    
    public function update_shipping_details($post=[]) { 
        
        //check shipping details exists
        $check_exists = $this->db->select("*")
            ->where('shipping_id', $post["shipping_id"])
            ->where("status", 1)
            ->get('shipping_mst')->row_array();
        
        if(!empty($check_exists)){  
            
            if(!empty($post["zipcode"])) {
                $check_zipcode = $this->db->select("*")
                    ->where('zipcode', $post["zipcode"])
                    ->get('zipcode')->row_array(); 
                unset($post["zipcode"]);
                if(!empty($check_zipcode)){    
                    $post["zipcode_id"] = $check_zipcode["zipcode_id"];
                    $update = $this->db
                            ->set($post)
                            ->where('shipping_id', $post["shipping_id"])
                            ->update('shipping_mst');

                    if($update) {
                        $shipping_details = $this->get_shipping_by_id($post);
			return $shipping_details;
                    }
                    else {
                        return 2;
                    } 
                } 
                else {
                    return 3;
                } 
            }
            else {
                $update = $this->db
                        ->set($post)
                        ->where('shipping_id', $post["shipping_id"])
                        ->update('shipping_mst');

                if($update) {
		    $shipping_details = $this->get_shipping_by_id($post);
		    return $shipping_details;
		}
                else {
                    return 2;
                } 
            }
        } else {
            return 1;
        }
    }

    public function check_zipcode($post=[]) {
        $details = $this->db->select("*")
            ->where('zipcode', $post["zipcode"])
            ->get('zipcode')->row_array();         
        
        if(!empty($details)) {
            return true;
        }
        else {
            return false;
        }            
    }
    
    public function check_quantity($product_id, $volume_id, $qty) {
        $details = $this->db->select("*")
            ->where('product_id', $product_id)
            ->where('volume_id', $volume_id)
            ->get('product_details')->row_array(); 
        
        if(!empty($details)){
            if($qty <= $details["units"]){
                return true;
            }        
            else if($qty > $details["units"]){
                return false;
            }            
        }
    }
    
    public function check_in_loyalty_club($product_id) {
        $details = $this->db->select("product_id, in_loyalty_club")
            ->where('product_id', $product_id)
            ->get('products')->row_array(); 
        
        if(!empty($details)){
            if($details["in_loyalty_club"] == 1){
                return true;
            }        
            else if($details["in_loyalty_club"] == 0){
                return false;
            }            
        }
    }
    
    public function check_in_vip_club($product_id) {
        $details = $this->db->select("product_id, in_vip_club")
            ->where('product_id', $product_id)
            ->get('products')->row_array(); 
        
        if(!empty($details)){
            if($details["in_vip_club"] == 1){
                return true;
            }        
            else if($details["in_vip_club"] == 0){
                return false;
            }            
        }
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
    
    //Gift Card
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
            'user_id' => $post["user_id"],    
            'message' => $post["message"],
	    'redeem_code' => date('ymdHi'). rand(10, 99)
        );
        $this->db->insert('gift_card', $ins_arr);
        $post["card_id"] = $this->db->insert_id();
        return $post;
    }
    
    public function save_gift_card_transation($post=[]) {        
        //print_r($post); exit;        
        $get_gift_card = $this->db->select("*")
                ->where('card_id', $post["card_id"])
                ->get('gift_card')->row_array();
        //service key
        $config_data = $this->db->where('key', 'service_key')->get('setting')->row_array();
	$token = $post['stripeToken'];
        //get user data
        $userdata = $this->get_user_by_id($get_gift_card["user_id"]);
 
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
       // echo "<pre>"; print_r($response); exit;
        
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
		$this->m_api->send_mail($to, $subject, $msg);
                
                //To sender
                $to1 = $userdata['email'];
                $subject1 = 'You have purchase a Gift Card';
                $msg1 = $this->load->view('mail_tmp/header', $userdata, true);
                $msg1 .= $this->load->view('mail_tmp/gift_card_sent', $get_gift_card, true);
                $msg1 .= $this->load->view('mail_tmp/footer', $userdata, true);
                $this->m_api->send_mail($to1, $subject1, $msg1);
            }
            
            //receiver user id
            $get_receiver = $this->get_user_by_email($get_gift_card['receiver_email']);
            
            //sender name
            $get_sender = $this->get_user_by_id($get_gift_card['user_id']);
            
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
    
    public function apply_gift_card($post=[]) {
        
        //get userdata
        $userdata = $this->get_user_by_id($post["user_id"]);

        //check gift card
        $where = 'status = 1 and expiry_date > now() and card_id = "'.$post['card_id'].'" and receiver_email="'.$userdata["email"].'"';
        $gift_card_details = $this->db->select('*')
                ->where($where)
                ->get('gift_card')->row_array();
        
        if(!empty($gift_card_details)){
            
            $where = 'card_id = '.$post['card_id'].' AND balance_amount = 0';
            //check wheather amount left in your gift card or not
            $gift_card_used1 = $this->db->select('*')
                ->where($where)
                ->order_by('date', 'desc')
                ->limit(1)
                ->get('gift_card_history')->row_array();
            
            //print_r($post); 
        
           
            if(empty($gift_card_used1)){
                
                $where1 = 'card_id = '.$post['card_id'].'';
                //check wheather amount left in your gift card or not
                $gift_card_used = $this->db->select('*')
                    ->where($where1)
                    ->order_by('date', 'desc')
                    ->limit(1)
                    ->get('gift_card_history')->row_array();
                                
                if($post["net_amount"] <= $gift_card_used["balance_amount"]){
                    $amount_used = $post['net_amount'];
                    $balance_amount = $gift_card_used["balance_amount"] - $post['net_amount']; 
                }
                else {
                    $amount_used = $gift_card_used["balance_amount"];
                    $left_amount = $post['net_amount'] - $gift_card_used["balance_amount"];                    
                    $post['net_amount'] = $left_amount;                    
                    $balance_amount = $gift_card_used["balance_amount"] - $amount_used;
                }
                
                               
                $transaction_id = date('YmdHis').$post["card_id"].$post["user_id"];
                $payment_history = '{"status":"true", "payment":"success", "amount":"'.$amount_used.'"}';
                $payment_status = 'SUCCESS';
                
                
                //pay from gift card
                //deduct from gift card
                $insert_array = array(
                    'card_id' => $post['card_id'],
                    'user_id' => $post['user_id'],
                    'transaction_id' => $transaction_id,
                    'order_id' => $post['order_id'],
                    'payment_history' => $payment_history,
                    'payment_status' => $payment_status,
                    'used_amount' => $amount_used,
                    'balance_amount' => $balance_amount
                );
                
                $insert = $this->db->insert('gift_card_history', $insert_array);
                
                $insert_array["net_amount"] = $post['net_amount'];
                
                if($insert){
                    return $insert_array;
                }
                else {
                    return false;
                } 
            }
            else {
                return 3;
            }
        }
        else {
            return 4;
        }
    }
    
    public function gift_card_received($post=[]) {
        //get email of user
        $userdata = $this->get_user_by_id($post["user_id"]);       
        
        //get gift card
        $gift_card = $this->db->select("*")
            ->where('receiver_email', $userdata["email"])
            ->where('status', 1)
	    ->where('is_redeem', 1)
//	    ->where('expiry_date >= NOW()')
            ->limit(LIMIT)
	    ->offset($post["offset"])
	    ->get('gift_card')->result_array();  
        
        if(!empty($gift_card)) {
            
            foreach($gift_card as $key => $value){
                $gift_card_history = $this->db->select("*")
                    ->where('card_id', $value["card_id"])
                    ->order_by('date', 'desc')
                    ->limit(1)
                    ->get('gift_card_history')->row_array();
                
                if(!empty($gift_card_history)){
                    $gift_card[$key]["remaining_amount"] = $gift_card_history["balance_amount"];
                }
            }               
            
            $count_prod = $this->db->select("*")
		    ->where('receiver_email', $userdata["email"])
		    ->where('status', 1)
		    ->where('is_redeem', 1)
		    ->get('gift_card')
		    ->num_rows();

	    $offset = LIMIT + $post["offset"];
	    if($count_prod > $offset) {
		$ret[0] = 1;
	    }
	    else {
		$ret[0] = 0;
	    }
	    $ret[1] = $offset;
	    $ret[2] = $gift_card;
	    return $ret;  
        }
        else {
            return false;
        }            
    }
    
    public function gift_card_sent($post=[]) {              
        //get gift card
        $gift_card = $this->db->select("*")
            ->where('user_id', $post["user_id"])
            ->where('status', 1)
            ->limit(LIMIT)
	    ->offset($post["offset"])
	    ->get('gift_card')->result_array();  
        
        if(!empty($gift_card)) {
            
            foreach($gift_card as $key => $value){
                $gift_card_history = $this->db->select("*")
                    ->where('card_id', $value["card_id"])
                    ->order_by('date', 'desc')
                    ->limit(1)
                    ->get('gift_card_history')->row_array();
                
                if(!empty($gift_card_history)){
                    $gift_card[$key]["remaining_amount"] = $gift_card_history["balance_amount"];
                }
            }   
            
            $count_prod = $this->db->select("*")
		    ->where('user_id', $post["user_id"])
		    ->where('status', 1)
		    ->get('gift_card')
		    ->num_rows();

	    $offset = LIMIT + $post["offset"];
	    if($count_prod > $offset) {
		$ret[0] = 1;
	    }
	    else {
		$ret[0] = 0;
	    }
	    $ret[1] = $offset;
	    $ret[2] = $gift_card;
	    return $ret;  
        }
        else {
            return false;
        }            
    }
    
    public function gift_card_by_id($post=[]) {
        
        //get email of user
        $userdata = $this->get_user_by_id($post["user_id"]);  
        
        $gift_card = $this->db->select("*")
            ->where('card_id', $post["card_id"])
            ->where('status', 1)
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
    
    public function gift_card_details_by_id($post=[]) {
        
        //get email of user
        $userdata = $this->get_user_by_id($post["user_id"]);  
        
        $gift_card = $this->db->select("*")
            ->where('card_id', $post["card_id"])
            ->where('status', 1)
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
            
            //print_r($gift_card_balance); exit;
            
            $where_his = "order_id != 0 AND card_id = ".$post["card_id"]."";
            
            //history of gift card
            $gift_card_history = $this->db->select("order_id")
                ->where($where_his)
                ->order_by('date', 'desc')
                ->get('gift_card_history')->result_array();            

            if(!empty($gift_card_history)){
                
                foreach($gift_card_history as $key => $value){
                
                    $order_details = $this->db->select("order_no, order_date, order_id, net_amount, total_qty")
                        ->where('user_id', $post["user_id"])
                        ->where('order_id', $value["order_id"])
                        ->order_by('order_date', 'desc')
                        ->get('orders')->row_array();
                    
                    //print_r($order_details);
                    
                    //if($value["order_id"] != 0){
                    
                        //get order product details
                        $product_details = $this->db->select("products.product_name")
                            ->join('products', 'products.product_id = order_product.product_id')
                            ->where('order_product.order_id', $value["order_id"])
                            ->get('order_product')->result_array();

                        //print_r($product_details); 

                        $gift_card_history[$key]["products"] = $product_details;
                   // }
                    
                    $gift_card_history[$key]["order"] = $order_details;
                }
                
                $gift_card["history"] = $gift_card_history;
            } 
            
            //print_r($gift_card); exit;
            
            return $gift_card;
        }
        else {
            return false;
        }            
    }
    
    //Promocode
    public function check_promocode($post=[]) {     
        //calculate amount
        $post["amount_payable"] = $post["gross_amount"];
        $promocode = $this->apply_promocode($post);

        if(!$promocode) {
            $net_amount = $post["gross_amount"];     
            return $net_amount;
        }
        elseif (!empty($promocode) && $promocode === 'A') {
            return 3;
        }
        elseif (!empty($promocode) && $promocode === 'B') {
             return 2; 
        }
        elseif (!empty($promocode) && $promocode === 'C') {
             return 4; 
        }
        else {
            $net_amount = $promocode;
            return $net_amount;
        }
    }
    
    public function apply_promocode($post=[]) { 
        
        $new_cart_total = 0;

        //check promocode
        $where = 'expiry_date >= now() and promocode = "'.$post['promocode'].'"';
        $promocode_details = $this->db->select('*')
                ->where($where)
                ->get('promocodes')->row_array();
        
        if(!empty($promocode_details)){
            
            //check wheather promocode is already used or not
            $promocode_used = $this->db->select('*')
                ->where('user_id', $post["user_id"])
                ->where('promocode_id', $promocode_details["promocode_id"])
                ->get('promocode_history')->row_array();
            
            if(empty($promocode_used)){

                //calculate            
                if($promocode_details["discount_type"] == 1){
                    $promocode_discount = ($post["amount_payable"] * $promocode_details["discount_amount"]) / 100;
                }
                elseif($promocode_details["discount_type"] == 2){
                    $promocode_discount = $promocode_details["discount_amount"];
                }
                
                if($post["amount_payable"] > $promocode_discount) {                    
                    //Update Cart total   
                    $new_cart_total = ($post["amount_payable"] - $promocode_discount);

                    //update order
                    $update_array = array(
                        'promocode_id' => $promocode_details["promocode_id"],
                    );

                    return number_format($new_cart_total,4);
                }
                else {
                    return 'C';
                }
            }
            else {
                return 'B';
            }
        }
        else {
            return 'A';
        }
    }
    
    public function promocode_list($post=[]) {
        $where = "expiry_date >= now()";
        $details = $this->db->select("*")
            ->where($where)
            ->limit(LIMIT)
	    ->offset($post["offset"])
	    ->get('promocodes')->result_array();  
        
        if(!empty($details)) {
	    $count_prod = $this->db->select("*")
		    ->where($where)
		    ->get('promocodes')
		    ->num_rows();

	    $offset = LIMIT + $post["offset"];
	    if($count_prod > $offset) {
		$ret[0] = 1;
	    }
	    else {
		$ret[0] = 0;
	    }
	    $ret[1] = $offset;
	    $ret[2] = $details;
	    return $ret; 
        }
        else {
            return false;
        }            
    } 
    
    //Order
    public function my_order($post=[]) {
        $details = $this->db->select("order_no, order_date, order_id, net_amount, total_qty")
            ->where('user_id', $post["user_id"])
            ->order_by('order_date', 'desc')
            ->limit(LIMIT)
	    ->offset($post["offset"])
	    ->get('orders')->result_array();  
        
        if(!empty($details)) {
            foreach($details as $key => $value){
                
                $order_details = $this->db->select("*")
                    ->where('order_id', $value["order_id"])
                    ->get('order_product')->result_array(); 
                
               // print_r($order_details);
                
                foreach($order_details as $okey => $ovalue){                 
                    //get order product details
                    $product_details = $this->order_product_list($ovalue["product_id"], $post["user_id"]);
                    $volume_details = $this->get_volume_by_id($ovalue["volume_id"]);
                    
                    if(!empty($product_details)){                    
                        $order_details[$okey]["product_name"] = $product_details[0]["product_name"];
                        $order_details[$okey]["brand_name"] = $product_details[0]["brand_name"];
                        $order_details[$okey]["feature_img"] = $product_details[0]["feature_img"];
                        $order_details[$okey]["feature_img_thumb"] = $product_details[0]["feature_img_thumb"];
                        $order_details[$okey]["no_of_return_days"] = $product_details[0]["no_of_return_days"];                        
                        $order_details[$okey]["is_review"] = $product_details[0]["is_review"];
                    }
                    
                    if(!empty($volume_details)){
                        $order_details[$okey]["volume"] = $volume_details["volume"];
                    }
                    
                    $details[$key]["products"] = $order_details;
               } 
            }            
        
            $count_prod = $this->db->select("order_no, order_date, order_id, net_amount, total_qty")
		    ->where('user_id', $post["user_id"])
		    ->order_by('order_date', 'desc')
		    ->get('orders')
		    ->num_rows();

	    $offset = LIMIT + $post["offset"];
	    if($count_prod > $offset) {
		$ret[0] = 1;
	    }
	    else {
		$ret[0] = 0;
	    }
	    $ret[1] = $offset;
	    $ret[2] = $details;
	    return $ret; 
        }
        else {
            return false;
        }            
    } 
    
    public function my_past_order($post=[]) {
        
        $where = "order_date <= now()";
        $details = $this->db->select("order_no, order_date, order_id, net_amount, total_qty")
            ->where('user_id', $post["user_id"])
            ->where('order_status', 4)
            ->order_by('order_date', 'desc')
            ->limit(LIMIT)
	    ->offset($post["offset"])
	    ->get('orders')->result_array();  
        
        //print_r($details); exit;
        
        if(!empty($details)) {
            foreach($details as $key => $value){
                
                $order_details = $this->db->select("*")
                    ->where('order_id', $value["order_id"])
                    ->get('order_product')->result_array(); 
                
               // print_r($order_details);
                
                foreach($order_details as $okey => $ovalue){                 
                    //get order product details
                    $product_details = $this->order_product_list($ovalue["product_id"], $post["user_id"]);
                    $volume_details = $this->get_volume_by_id($ovalue["volume_id"]);
                    
                    if(!empty($product_details)){                    
                        $order_details[$okey]["product_name"] = $product_details[0]["product_name"];
                        $order_details[$okey]["brand_name"] = $product_details[0]["brand_name"];
                        $order_details[$okey]["seller"] = $product_details[0]["seller"];
                        $order_details[$okey]["feature_img"] = $product_details[0]["feature_img"];
                        $order_details[$okey]["feature_img_thumb"] = $product_details[0]["feature_img_thumb"];
                        $order_details[$okey]["is_review"] = $product_details[0]["is_review"];
                    }
                    
                    if(!empty($volume_details)){
                        $order_details[$okey]["volume"] = $volume_details["volume"];
                    }
                    
                    $details[$key]["products"] = $order_details;
                } 
            }
        
            $count_prod = $this->db->select("order_no, order_date, order_id, net_amount, total_qty")
		    ->where('user_id', $post["user_id"])
		    ->where('order_status', 4)
		    ->order_by('order_date', 'desc')
		    ->get('orders')
		    ->num_rows();

	    $offset = LIMIT + $post["offset"];
	    if($count_prod > $offset) {
		$ret[0] = 1;
	    }
	    else {
		$ret[0] = 0;
	    }
	    $ret[1] = $offset;
	    $ret[2] = $details;
	    return $ret;
        }
        else {
            return false;
        }            
    } 
    
    public function my_upcoming_order($post=[]) {
        
        $where = "order_status IN (1, 2, 3, 6, 5, 7)";
        $details = $this->db->select("order_no, order_date, order_id, net_amount, total_qty")
            ->where('user_id', $post["user_id"])
            ->where($where)
            ->order_by('order_date', 'desc')
            ->limit(LIMIT)
	    ->offset($post["offset"])
	    ->get('orders')->result_array();  
        
        //print_r($details); exit;
        
        if(!empty($details)) {
            foreach($details as $key => $value){
                
                $order_details = $this->db->select("*")
                    ->where('order_id', $value["order_id"])
                    ->get('order_product')->result_array(); 
                
               // print_r($order_details);
                
                foreach($order_details as $okey => $ovalue){                 
                    //get order product details
                    $product_details = $this->order_product_list($ovalue["product_id"], $post["user_id"]);
                    $volume_details = $this->get_volume_by_id($ovalue["volume_id"]);
                    
                    if(!empty($product_details)){                    
                        $order_details[$okey]["product_name"] = $product_details[0]["product_name"];
                        $order_details[$okey]["brand_name"] = $product_details[0]["brand_name"];
                        $order_details[$okey]["seller"] = $product_details[0]["seller"];
                        $order_details[$okey]["feature_img"] = $product_details[0]["feature_img"];
                        $order_details[$okey]["feature_img_thumb"] = $product_details[0]["feature_img_thumb"];
                        $order_details[$okey]["is_review"] = $product_details[0]["is_review"];
                    }
                    
                    if(!empty($volume_details)){
                        $order_details[$okey]["volume"] = $volume_details["volume"];
                    }
                    
                    $details[$key]["products"] = $order_details;
                } 
            }
        
            $count_prod = $this->db->select("order_no, order_date, order_id, net_amount, total_qty")
		    ->where('user_id', $post["user_id"])
		    ->where($where)
		    ->order_by('order_date', 'desc')
		    ->get('orders')
		    ->num_rows();

	    $offset = LIMIT + $post["offset"];
	    if($count_prod > $offset) {
		$ret[0] = 1;
	    }
	    else {
		$ret[0] = 0;
	    }
	    $ret[1] = $offset;
	    $ret[2] = $details;
	    return $ret;
        }
        else {
            return false;
        }            
    } 
    
    public function order_details($post=[]) {
        //print_r($post);
        $details = $this->db->select("*")
            ->where('user_id', $post["user_id"])
            ->where('order_id', $post["order_id"])
            ->get('orders')->row_array();  
        
        //print_r($details); exit;
        
        if(!empty($details)) {
                $details = array_map(function($val) {
                    if(is_null($val)) {
                        $val = "";
                    }
                    return $val;
                }, $details);
                
                //orde cancel request
                $order_cancel_request = $this->db->select("*")
                        ->where('user_id', $post["user_id"])
                        ->where('order_id', $post["order_id"])
                        ->get('order_canceled')->row_array();
                
                if(!empty($order_cancel_request)){
                    if($order_cancel_request["is_confirmed"] == 0){
                        $details["cancel_request_status"] = "Cancel Request In-Progress";
                    }
                    else if($order_cancel_request["is_confirmed"] == 1){
                        $details["cancel_request_status"] = "Order Cancelled";
                    }
                    else if($order_cancel_request["is_confirmed"] == 2){
                        $details["cancel_request_status"] = "Order cannot be Cancelled";
                    }
                }
                
                //promocode details
                $order_promocode = $this->db->select("*")
                    ->where('promocode_id', $details["promocode_id"])
                    ->get('promocodes')->row_array(); 
                
                if(!empty($order_promocode)) {
                    $details["promocode"] = $order_promocode;
                }
                
                if( $details["order_status"] == 1 ){
                    $details["orderStatus"] = "Pending";
                }
                elseif( $details["order_status"] == 2 ){
                    $details["orderStatus"] = "Accepted By Seller";
                }
                elseif( $details["order_status"] == 3 ){
                    $details["orderStatus"] = "Accepted By Driver";
                }
                elseif( $details["order_status"] == 4 ){
                    $details["orderStatus"] = "Delivered";
                }
                elseif( $details["order_status"] == 5 ){
                    $details["orderStatus"] = "Cancelled";
                }
                elseif( $details["order_status"] == 6 ){
                    $details["orderStatus"] = "Order Placed";
                }
                elseif( $details["order_status"] == 7 ){
                    $details["orderStatus"] = "Rejected By Seller";
                }
		elseif( $details["order_status"] == 8 ){
                    $details["orderStatus"] = "Cancelled By Driver";
                }
		elseif( $details["order_status"] == 9 ){
                    $details["orderStatus"] = "Picked Up";
                }
		elseif( $details["order_status"] == 10 ){
                    $details["orderStatus"] = "Start Delivery";
                }
		elseif( $details["order_status"] == 11 ){
                    $details["orderStatus"] = "End Delivery";
                }
		elseif( $details["order_status"] == 12 ){
                    $details["orderStatus"] = "Pause";
                }
		elseif( $details["order_status"] == 13 ){
                    $details["orderStatus"] = "Not Completed";
                }
                
                //gift card details
                $post["card_id"] = $details["gift_card_id"];
                $order_gift_card = $this->gift_card_by_id($post);
                
                if(!empty($order_gift_card)) {
                    $details["gift_card"] = $order_gift_card;
                }
                
                //get shipping details
                $post["shipping_id"] = $details["shipping_id"];
                $shipping_details = $this->get_all_shipping_by_id($post); 
                
                if(!empty($shipping_details)) {
                    $details["shipping_details"] = $shipping_details; 
                }
                
                //order product details
                $order_details = $this->db->select("*")
                    ->where('order_id', $details["order_id"])
                    ->get('order_product')->result_array(); 
                
                if(!empty($order_details)) {

                    foreach($order_details as $okey => $ovalue){                 
                        //get order product details
                        $product_details = $this->order_product_list($ovalue["product_id"], $post["user_id"]);
                        $volume_details = $this->get_volume_by_id($ovalue["volume_id"]);
                        //return policy
                        $return_policy = $this->get_product_return_policy($ovalue);   
                        $returned = $this->get_return_product_details($post["order_id"], $ovalue["product_id"], $ovalue["volume_id"]);
                        
                        //print_r($returned); 
                        if(!empty($product_details)){                    
                            $order_details[$okey]["product_name"] = $product_details[0]["product_name"];
                            $order_details[$okey]["brand_name"] = $product_details[0]["brand_name"];
                            $order_details[$okey]["seller"] = $product_details[0]["seller"];
                            $order_details[$okey]["feature_img"] = $product_details[0]["feature_img"];
                            $order_details[$okey]["feature_img_thumb"] = $product_details[0]["feature_img_thumb"];
                            $order_details[$okey]["no_of_return_days"] = $product_details[0]["no_of_return_days"];
                            $order_details[$okey]["is_review"] = $product_details[0]["is_review"];
                            
                            if(!empty($returned)){
                                if($returned["is_confirmed"] == 0) {
                                    $order_details[$okey]["return_flag"] = "Pending";
                                }
                                else if($returned["is_confirmed"] == 1) {
                                    $order_details[$okey]["return_flag"] = "Accepted";
                                }
                                else if($returned["is_confirmed"] == 2) {
                                    $order_details[$okey]["return_flag"] = "Rejected";
                                }
                                $order_details[$okey]["refund_amount"] = $returned["amount_refunded"];
                            }
                            else {
                                $order_details[$okey]["return_flag"] = "";
                            }
                            
                            if($product_details[0]["no_of_return_days"] != 0) {
                                $order_details[$okey]["return_policy"] = $return_policy;
                            }
                            else {
                                $order_details[$okey]["return_policy"] = 'Cannot Return.';
                            }
			    
			    //calculate allowed return days
			    if($product_details[0]["no_of_return_days"] > 0 && !empty($return_policy) && $details["order_status"] == '4') {
				$delivered_date = strtotime($details["delivered_date"]);
				$return_due_date =  strtotime(' + '.$product_details[0]["no_of_return_days"].' day', $delivered_date);
				$today = strtotime(date('Y-m-d'));
				
				if($return_due_date > $today) {
				    $order_details[$okey]["can_be_return"] = 1;
				}
				else {
				    $order_details[$okey]["can_be_return"] = 0;
				}
			    }    
			    else {
				$order_details[$okey]["can_be_return"] = 0;
			    }
                        }

                        if(!empty($volume_details)){
                            $order_details[$okey]["volume"] = $volume_details["volume"];
                        }

                        $details["products"] = $order_details;
                    } 
                }
            
            //calculate delivery date
            //$delivery_date = date("Y-m-d H:i:s", strtotime("+3 day", strtotime($details["order_date"])));

            //product delivery date
	    if($details["order_done_type"] == 1) {
		$details["delivery_date"] = $details["order_date"]; 
	    }
	    else if($details["order_done_type"] == 2) {
		$details["delivery_date"] = $details["to_be_delivered_date"]; 
	    }            
        
            return $details;
        }
        else {
            return false;
        }            
    } 
    
    public function get_return_product_details($order_id, $product_id, $volume_id){
        $returned = $this->db->select("*")
                ->where('product_id', $product_id)
                ->where('volume_id', $volume_id)
                ->where('order_id', $order_id)
                ->get('products_returned')->row_array();
        if(!empty($returned)){
            return $returned;
        }
    }
    
    public function order_product_list($product_id, $user_id) {         
        
        $get_cart_list = $this->db->select('products.product_name, products.seller_id, products.feature_img, brand_mst.brand_name, products.no_of_return_days', false)                
                ->join('brand_mst', 'products.brand_id = brand_mst.brand_id', 'left')
                ->where('product_id', $product_id)
                ->get('products')->result_array();   

        if(!empty($get_cart_list)) {
            
            foreach($get_cart_list as $key => $value){            
                if($value["seller_id"] != 0) {
                    //seller info
                    $seller = $this->db->select("seller_id, seller_name, company_name, email, contact_no, address, latitude, longitude, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender", false)
                            ->where('seller_id', $value['seller_id'])
                            //->where('status', 1)
                            //->where('is_admin_verified', 1)
                            ->get('seller')->row_array();

                    $seller2 = array_map(function($val) {
                        if(is_null($val)) {
                            $val = "";
                        }
                        return $val;
                    }, $seller);

                    $get_cart_list[$key]['seller'] = $seller2;
                }
                else {
                    $get_cart_list[$key]['seller'] = array(
                        'seller_name' => "Admin"
                    );
                }

                if($value['feature_img']){                
                    $get_cart_list[$key]['feature_img'] = $this->image_url_product( $value['feature_img']);   
                    $get_cart_list[$key]['feature_img_thumb'] = $this->image_url_product( $value['feature_img'],'thumb');     
                }
                else {
                    $get_cart_list[$key]['feature_img'] = '';  
                    $get_cart_list[$key]['feature_img_thumb'] = '';  
                }    

                 //review tag
                $reviews = $this->db->select("*")
                        ->where('product_id', $product_id)
                        ->where('user_id', $user_id)
                        ->get('product_rating')->row_array();

                if(!empty($reviews)){
                    $get_cart_list[$key]['is_review'] = true;
                }
                else {
                    $get_cart_list[$key]['is_review'] = false; 
                }
            }
        
            return $get_cart_list;
        }
        else {
            return false;
        }  
    }
    
    //Loyalty points
    public function add_loyalty_point($user_id, $price='') { 
        
        //get loyalty points
        $loyalty_points = $this->db->select("loyalty_point")
                ->where('user_id', $user_id)
                ->get('user')->row_array();
        
        //add loyalty points            
        $points = round($price) * 10;  
        
        $final_points = $loyalty_points["loyalty_point"] + $points;
        
        /*echo $points."<br>";
        echo $final_points."<br>";*/
        //Check Eligibility to be a member in VIP Club
        $vip_points = $this->db->where('key', 'vip_loyalty_points')->get('setting')->row_array();
        
        //check loyalty points is eligible
        if($final_points >= $vip_points["value"]){
            $is_vip_club_member = 1;
        } else {
            $is_vip_club_member = 0;
        }        
        //echo $final_points.'<br>';
        $this->db
            ->set('loyalty_point', $final_points)
            ->set('is_vip_club_member', $is_vip_club_member)
            ->where('user_id', $user_id)
            ->update('user');

        return true;
    }      
    
    public function wallet_details_by_id($post=[]) {
        
        $wallet = array();
        $wallet_arr = array();
        
        $userdata = $this->db->select('user_id, wallet')
                ->where('user_id',$post["user_id"])
                ->get('user')->row_array();   
        
        $wallet["remaining_amount"] = number_format($userdata["wallet"],2);
        
        $where_his = "user_id = ".$post["user_id"]." and type = 2";

        //history of gift card
        $wallet_history = $this->db->select("*")
            ->where($where_his)
            ->order_by('date', 'desc')
	    ->get('wallet_history')->result_array();            

            if(!empty($wallet_history)){
                
                foreach($wallet_history as $key => $value){
                
                    $order_details = $this->db->select("order_no, order_date, order_id, net_amount, wallet_amount, total_qty")
                        ->where('user_id', $post["user_id"])
                        ->where('order_id', $value["order_id"])
                        ->order_by('order_date', 'desc')
                        ->get('orders')->row_array();
                    
                    //get order product details
                    $product_details = $this->db->select("products.product_name, order_product.price, order_product.qty, order_product.net_total")
                        ->join('products', 'products.product_id = order_product.product_id')
                        ->where('order_product.order_id', $value["order_id"])
                        ->get('order_product')->result_array();

                    foreach($product_details as $key1 => $value1){
                        $product_details[$key1]["order"] = $order_details;
                    }                   
                    
                    $wallet_history[$key]["order"] = $product_details;
                   // $wallet_history[$key]["order"] = $order_details;
                    //$order_details["products"] = $product_details; 
                    
                    
                    if($wallet_history[$key]["type"] == 1){
                        $wallet_history[$key]["transaction_type"] = "Debit";
                    } 
                    else if($wallet_history[$key]["type"] == 2){
                        $wallet_history[$key]["transaction_type"] = "Credit";
                    }
                }
                
                $wallet["history"] = $wallet_history;
                
            }             
            //print_r($wallet); exit;            
            return $wallet;
                   
    }
        
    public function help_support($post = []) {         
        
        //get user by id
        $userdata = $this->get_user_by_id($post["user_id"]);
        
        $post["user_email"] = $userdata['email'];
        $post["name"] = $userdata['firstname'].' '.$userdata['lastname'];
        //insert into db
        $insert_array = array(
            'user_id' => $post["user_id"],
            'name' => $post["name"],
            'email' => $post["user_email"],
            'subject' => $post["subject"],
            'message' => $post["message"],
            'type' => 1,
        );
        
        $insert = $this->db->insert('help_support', $insert_array);
        
        $config_data = $this->db->where('key', 'admin_email_address')->get('setting')->row_array();
        
        $to = $config_data['value'];
        $subject = $post["subject"];
        $msg = $this->load->view('mail_tmp/header', $post, true);
        $msg .= $this->load->view('mail_tmp/help_support', $post, true);
        $msg .= $this->load->view('mail_tmp/footer', $post, true);
        $this->send_mail($to, $subject, $msg);

        if($insert) {
            return true;
        }
        else {
            return false;
        }  
    }

    public function get_loyalty_point($post = []) {         
        
        //get user by id
        $userdata = $this->get_user_by_id($post["user_id"]);
        
        $vip_points = $this->db->where('key', 'vip_loyalty_points')->get('setting')->row_array();
        
        //check whether is member in vip club or not
        if($userdata["loyalty_point"] >= $vip_points["value"]){
            $is_member = 'Yes';  
            $point_left = 0;
        } else {
            $is_member = 'No';            
            $point_left = $vip_points["value"] - $userdata["loyalty_point"];
        }
        
        $return = array(
            'loyalty_point' => $userdata["loyalty_point"],
            'is_valid_member' => $is_member,
            'point_left' => $point_left
        );
        
        return $return;
    }
    
    public function loyalty_club_list($post = []) {         
        
        //with category parameter
        if(isset($post["category"]) && $post["category"] != NULL ) {
            $categoryid = ' and products.category_id = '.$post["category"].'';             
        }
        else {
            $categoryid = '';                        
        }
        
        //with sub-category parameter
        if(isset($post["subcategory"]) && $post["subcategory"] != NULL ) {
            $categoryid = ' and products.category_id = '.$post["subcategory"].'';             
        }
        
        //with brand parameter
         if(isset($post["brand"]) && $post["brand"] != NULL ) {
            $brandid = 'and (';
            $i=1;
            $br_arr = explode(',',$post["brand"]);
            
            foreach($br_arr as $value){
                $brandid .= ' products.brand_id = '.$value.''; 
                if($i < count($br_arr)){
                    $brandid .= ' or ';
                }
                $i++;
            }    
            $brandid .= ')';
        }
        else {
            $brandid = '';                        
        }
        
        //with volume parameter
        if(isset($post["volume"]) && $post["volume"] != NULL ) {
            $volumeid = 'and (';
            $i=1;
            $br_arr = explode(',',$post["volume"]);
            
            foreach($br_arr as $value){
                $volumeid .= ' product_details.volume_id = '.$value.''; 
                if($i < count($br_arr)){
                    $volumeid .= ' or ';
                }
                $i++;
            }   
            $volumeid .= ')';
        }
        else {
            $volumeid = '';                        
        }
               
        $prod_list = $this->db->select("products.*, product_details.volume_id, category_mst.category_name, brand_mst.brand_name", false)
                ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1 '.$volumeid.'')
                ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1 '.$categoryid.'')
                ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1 '.$brandid.'')
                ->where("products.status", 1)
                ->where("products.in_loyalty_club", 1)
                ->group_by('products.product_id')
                ->limit(LIMIT)
		->offset($post["offset"])
		->order_by("products.date", "desc")
		->get("products")
                ->result_array();  
        
        if(!empty($prod_list)){
        
            foreach($prod_list as $key => $value){  
		
		$msg = str_replace(PHP_EOL,"@/@", $prod_list[$key]["description"]);
		$prod_list[$key]["description"] = json_decode('"'.$msg.'"');
		$prod_list[$key]["description"] = str_replace("@/@",PHP_EOL, $prod_list[$key]["description"]);
                
                //Get Product Volume
                $this->db->select("product_details.*, concat(volume_mst.volumne_value,' ',(select volume_type from volume_type where volume_type_id = volume_mst.type)) as volumes", false)
                        ->from("products")
                        ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
                        ->join('volume_mst', 'product_details.volume_id=volume_mst.volume_id and volume_mst.status=1')
                        ->where("products.product_id", $value['product_id'])
                        ->where("products.status", 1)
                        ->order_by("products.date", "desc");
                $get_data_volume = $this->db->get()
                        ->result_array();

                //print_r($get_data_volume); exit;
                if(!empty($get_data_volume)){ 

                    $prod_list[$key]["price"] = $get_data_volume[0]["actual_price"];

                    foreach($get_data_volume as $vkey => $vvalue){ 
                        $get_data_volume[$vkey]["actual_price"] = $vvalue["actual_price"];
                    }                    
                    $prod_list[$key]['isvolume'] = true;
                }
                else {
                    $prod_list[$key]['isvolume'] = false;
                }

                //Get Product Return policy
                $this->db->select("product_return_policy.*", false)
                        ->from("products")
                        ->join('product_return_policy', 'products.product_id = product_return_policy.product_id')
                        ->where("products.product_id", $value['product_id'])
                        ->where("products.have_return_policy", 1)
                        ->where("products.status", 1)
                        ->order_by("products.date", "desc");
                $get_data_policy = $this->db->get()
                            ->row_array();
                
                if(!empty($get_data_policy)){
                    $get_data_policy["description"] = html_entity_decode(strip_slashes(strip_tags($get_data_policy["description"], '<strong>')));
                    $get_data_policy["status"] = true;
                } else {
                    $get_data_policy["status"] = false;
                }

                $prod_list[$key]['return_policy'] = $get_data_policy;
                $prod_list[$key]['volume'] = $get_data_volume;

                if($value['feature_img']){                
                    $prod_list[$key]['feature_img'] = $this->image_url_product( $value['feature_img']);   
                    $prod_list[$key]['feature_img_thumb'] = $this->image_url_product( $value['feature_img'],'thumb');     
                }
                else {
                    $prod_list[$key]['feature_img'] = '';  
                    $prod_list[$key]['feature_img_thumb'] = '';  
                }

            }
        }
        
        if (!empty($prod_list)) {
            $count_prod = $this->db->select("products.*, product_details.volume_id, category_mst.category_name, brand_mst.brand_name", false)
		    ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1 '.$volumeid.'')
		    ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1 '.$categoryid.'')
		    ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1 '.$brandid.'')
		    ->where("products.status", 1)
		    ->where("products.in_loyalty_club", 1)
		    ->group_by('products.product_id')
		    ->order_by("products.date", "desc")
		    ->get("products")
		    ->num_rows();

	    $offset = LIMIT + $post["offset"];
	    if($count_prod > $offset) {
		$ret[0] = 1;
	    }
	    else {
		$ret[0] = 0;
	    }
	    $ret[1] = $offset;
	    $ret[2] = $prod_list;
	    return $ret;        
        }        
        else {
            return false;
        }
    }

    public function vip_club_list($post = []) {         
        
        //with category parameter
        if(isset($post["category"]) && $post["category"] != NULL ) {
            $categoryid = ' and products.category_id = '.$post["category"].'';             
        }
        else {
            $categoryid = '';                        
        }
        
        //with sub-category parameter
        if(isset($post["subcategory"]) && $post["subcategory"] != NULL ) {
            $categoryid = ' and products.category_id = '.$post["subcategory"].'';             
        }
        
        //with brand parameter
         if(isset($post["brand"]) && $post["brand"] != NULL ) {
            $brandid = 'and (';
            $i=1;
            $br_arr = explode(',',$post["brand"]);
            
            foreach($br_arr as $value){
                $brandid .= ' products.brand_id = '.$value.''; 
                if($i < count($br_arr)){
                    $brandid .= ' or ';
                }
                $i++;
            }    
            $brandid .= ')';
        }
        else {
            $brandid = '';                        
        }
        
        //with volume parameter
        if(isset($post["volume"]) && $post["volume"] != NULL ) {
            $volumeid = 'and (';
            $i=1;
            $br_arr = explode(',',$post["volume"]);
            
            foreach($br_arr as $value){
                $volumeid .= ' product_details.volume_id = '.$value.''; 
                if($i < count($br_arr)){
                    $volumeid .= ' or ';
                }
                $i++;
            }   
            $volumeid .= ')';
        }
        else {
            $volumeid = '';                        
        }
               
        $prod_list = $this->db->select("products.*, product_details.volume_id, category_mst.category_name, brand_mst.brand_name", false)
                ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1 '.$volumeid.'')
                ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1 '.$categoryid.'')
                ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1 '.$brandid.'')
                ->where("products.status", 1)
                ->where("products.in_vip_club", 1)
                ->group_by('products.product_id')
		->limit(LIMIT)
		->offset($post["offset"])
                ->order_by("products.date", "desc")
		->get("products")
                ->result_array();  
        
        if(!empty($prod_list)){
        
            foreach($prod_list as $key => $value){  
                
		$msg = str_replace(PHP_EOL,"@/@", $prod_list[$key]["description"]);
		$prod_list[$key]["description"] = json_decode('"'.$msg.'"');
		$prod_list[$key]["description"] = str_replace("@/@",PHP_EOL, $prod_list[$key]["description"]);
		    
                //Get Product Volume
                $this->db->select("product_details.*, concat(volume_mst.volumne_value,' ',(select volume_type from volume_type where volume_type_id = volume_mst.type)) as volumes", false)
                        ->from("products")
                        ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
                        ->join('volume_mst', 'product_details.volume_id=volume_mst.volume_id and volume_mst.status=1')
                        ->where("products.product_id", $value['product_id'])
                        ->where("products.status", 1)
                        ->order_by("products.date", "desc");
                $get_data_volume = $this->db->get()
                        ->result_array();

                //print_r($get_data_volume); exit;
                if(!empty($get_data_volume)){ 

                    $prod_list[$key]["price"] = $get_data_volume[0]["actual_price"];

                    foreach($get_data_volume as $vkey => $vvalue){ 
                        $get_data_volume[$vkey]["actual_price"] = $vvalue["actual_price"];
                    }                    
                    $prod_list[$key]['isvolume'] = true;
                }
                else {
                    $prod_list[$key]['isvolume'] = false;
                }

                //Get Product Return policy
                $this->db->select("product_return_policy.*", false)
                        ->from("products")
                        ->join('product_return_policy', 'products.product_id = product_return_policy.product_id')
                        ->where("products.product_id", $value['product_id'])
                        ->where("products.have_return_policy", 1)
                        ->where("products.status", 1)
                        ->order_by("products.date", "desc");
                $get_data_policy = $this->db->get()
                            ->row_array();
                
                if(!empty($get_data_policy)){
                    $get_data_policy["description"] = html_entity_decode(strip_slashes(strip_tags($get_data_policy["description"], '<strong>')));
                    $get_data_policy["status"] = true;
                } else {
                    $get_data_policy["status"] = false;
                }

                $prod_list[$key]['return_policy'] = $get_data_policy;
                $prod_list[$key]['volume'] = $get_data_volume;

                if($value['feature_img']){                
                    $prod_list[$key]['feature_img'] = $this->image_url_product( $value['feature_img']);   
                    $prod_list[$key]['feature_img_thumb'] = $this->image_url_product( $value['feature_img'],'thumb');     
                }
                else {
                    $prod_list[$key]['feature_img'] = '';  
                    $prod_list[$key]['feature_img_thumb'] = '';  
                }

            }
        }
        
        if (!empty($prod_list)) {
            $count_prod = $this->db->select("products.*, product_details.volume_id, category_mst.category_name, brand_mst.brand_name", false)
		    ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1 '.$volumeid.'')
		    ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1 '.$categoryid.'')
		    ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1 '.$brandid.'')
		    ->where("products.status", 1)
		    ->where("products.in_vip_club", 1)
		    ->group_by('products.product_id')
		    ->order_by("products.date", "desc")
		    ->get("products")
		    ->num_rows();

	    $offset = LIMIT + $post["offset"];
	    if($count_prod > $offset) {
		$ret[0] = 1;
	    }
	    else {
		$ret[0] = 0;
	    }
	    $ret[1] = $offset;
	    $ret[2] = $prod_list;
	    return $ret;                
        }        
        else {
            return false;
        }
    }
    
    //Product Return
    public function product_return_by_user($post = []){
        // order_id, product_id, user_id, reason
        
        //Check product is eligible to return
        $get_order = $this->db->select("*")
                ->where('order_id', $post["order_id"])
                ->where('user_id', $post["user_id"])
                ->get("orders")->row_array();
        
        if(isset($post["product_id"]) && $post["product_id"] != NULL) {
            $get_product = $this->db->select('no_of_return_days, seller_id')
                    ->where('product_id', $post["product_id"])
                    ->get("products")->row_array();
        }
        
        if($get_order["order_status"] == 4){
        //calculate no of days
        $delivered_date = strtotime($get_order["delivered_date"]);
            $now = strtotime(date('Y-m-d H:i:s'));
            //echo date('Y-m-d H:i:s');
            $difference = round(($now - $delivered_date) / (60 * 60 * 24));

            if($difference >= $get_product["no_of_return_days"]) {
                return 1;
            }
            else if($difference < $get_product["no_of_return_days"]) {
                $this->db->insert('products_returned', $post);
                $this->add_web_notification($post);
                return 2;
            }
        }
        else {
            return 3;
        }
    }
    
    public function get_similar_product_loyalty_vip($post = []){
        
        if(isset($post['product_id']) && $post['product_id'] != NULL){
            //current product        
            $current_product = $this->get_product_by_id($post["product_id"]);
            
            //print_r($current_product); exit;
            
            if($current_product){
                
                if(isset($post["in_club"]) && $post["in_club"] == 1) {
                    $where ="products.in_loyalty_club = 1 and products.product_id != ".$post["product_id"]." and (products.category_id IN (".$current_product["category_id"].") OR products.brand_id IN (".$current_product["brand_id"].")) AND products.status=1";
                }
                else if(isset($post["in_club"]) && $post["in_club"] == 2)  {
                    $where ="products.in_vip_club = 1 and products.product_id != ".$post["product_id"]." and (products.category_id IN (".$current_product["category_id"].") OR products.brand_id IN (".$current_product["brand_id"].")) AND products.status=1";
                }                

                $prod_list = $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", false)
                        ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1', 'left')
                        ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1', 'left')
                        ->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
			->where($where)
                        ->limit(LIMIT)
			->offset($post["offset"])
			->group_by('products.product_id')
                        ->order_by("products.date", "desc")
			->get("products")
                        ->result_array();   
                
                if(!empty($prod_list)){

                    foreach($prod_list as $key => $value){   
			
			$msg = str_replace(PHP_EOL,"@/@", $prod_list[$key]["description"]);
			$prod_list[$key]["description"] = json_decode('"'.$msg.'"');
			$prod_list[$key]["description"] = str_replace("@/@",PHP_EOL, $prod_list[$key]["description"]);
                        
                        //Get Product Volume
                        $this->db->select("product_details.*, concat(volume_mst.volumne_value,' ',(select volume_type from volume_type where volume_type_id = volume_mst.type)) as volumes", false)
                                ->from("products")
                                ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
                                ->join('volume_mst', 'product_details.volume_id=volume_mst.volume_id and volume_mst.status=1')
                                ->where("products.product_id", $value['product_id'])
                                ->where("products.status", 1)
                                ->order_by("products.date", "desc");
                        $get_data_volume = $this->db->get()
                                ->result_array();

                        if(!empty($get_data_volume)){ 

                            $prod_list[$key]["price"] = $get_data_volume[0]["actual_price"];

                            foreach($get_data_volume as $vkey => $vvalue){ 
                                $get_data_volume[$vkey]["actual_price"] = $vvalue["actual_price"];
                            }
                            $prod_list[$key]['isvolume'] = true;
                        }
                        else {
                            $prod_list[$key]['isvolume'] = false;
                        }
                        //Get Product Return policy
                        $this->db->select("product_return_policy.*", false)
                                ->from("products")
                                ->join('product_return_policy', 'products.product_id = product_return_policy.product_id')
                                ->where("products.product_id", $value['product_id'])
                                ->where("products.have_return_policy", 1)
                                ->where("products.status", 1)
                                ->order_by("products.date", "desc");
                        $get_data_policy = $this->db->get()
                                    ->row_array();
                        
                        if(!empty($get_data_policy)){
                            $get_data_policy["description"] = html_entity_decode(strip_slashes(strip_tags($get_data_policy["description"], '<strong>')));
                            $get_data_policy["status"] = true;
                        } else {
                            $get_data_policy["status"] = false;
                        }

                        $prod_list[$key]['return_policy'] = $get_data_policy;                        
                        $prod_list[$key]['volume'] = $get_data_volume;

                        // images
                        if($value['feature_img']){                 
                            $prod_list[$key]['feature_img'] = $this->image_url_product( $value['feature_img']);   
                            $prod_list[$key]['feature_img_thumb'] = $this->image_url_product( $value['feature_img'],'thumb');     
                        }
                        else {
                            $prod_list[$key]['feature_img'] = '';  
                            $prod_list[$key]['feature_img_thumb'] = '';  
                        }            
                    }
                }

                if (!empty($prod_list)) {
		    $count_prod = $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", false)
			    ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1', 'left')
			    ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1', 'left')
			    ->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
			    ->where($where)
			    ->group_by('products.product_id')
			    ->order_by("products.date", "desc")
			    ->get("products")
			    ->num_rows();

		    $offset = LIMIT + $post["offset"];
		    if($count_prod > $offset) {
			$ret[0] = 1;
		    }
		    else {
			$ret[0] = 0;
		    }
		    $ret[1] = $offset;
		    $ret[2] = $prod_list;
		    return $ret;         
                }        
                else {
                    return false;
                }
            }
            else {
                return false;
            }
        }        
        else {
            return false;
        }
    }

    public function create_notification($post = []) {
        $this->db->insert('notification', $post);
        $notification['notification_id'] = $this->db->insert_id();
        return $notification;
    }
    
    public function get_notification_list($post = []) {
	$this->db->set("is_read", 1)
		->where('to_user_id', $post['user_id'])
		->where("is_read", 0)
		->update("notification");
	
        $list = $this->db
                ->where('to_user_id', $post['user_id'])
		->where("notification_type NOT IN (25)")
                ->limit(LIMIT)
                ->offset($post['offset'])
                ->order_by('date', 'desc')
                ->get('notification')
                ->result_array();
        if ($list) {
            foreach($list as $key => $value1) {
                $value = array_map(function($val) {
                    if(is_null($val)) {
                        $val = "";
                    }
                    return $val;
                }, $value1);
		
		if($value["notification_type"] == 11) {
		    //order details
		    //echo $value["order_id"];
		    $get_order_driver = $this->db->select("*")
			    ->where("driver_id", $value["driver_id"])
			    ->where("status", 1)
			    ->get("order_driver")->row_array();
		    
		    //print_r($get_order_driver);
		    

		    if(!empty($get_order_driver)) {
			$get_ltlg = $this->user_info($get_order_driver["driver_id"]);
			$push_new["name"] = $get_ltlg["firstname"]." ".$get_ltlg["lastname"];
			$push_new["contact_no"] = trim($get_ltlg["mobileno"]);

			$value = array_merge($value, $push_new);
		    }
		    else {
			$push_new["name"] = "";
			$push_new["contact_no"] = "";
		    }
		}
		
		$list[$key] = $value;
		
		if(!empty($value["order_id"])) {
		    $order = $this->get_order_by_id($value["order_id"]);
		    if(!empty($order)) {
			$list[$key]["order_status"] = $order["order_status"];
		    }
		    else {
			$list[$key]["order_status"] = "";
		    }
		}
		else {
		    $list[$key]["order_status"] = "";
		}
            }
	    
	    $count_prod =  $this->db
                ->where('to_user_id', $post['user_id'])
		->where("notification_type NOT IN (25)")
                ->order_by('date', 'desc')
                ->get('notification')->num_rows();

	    $offset = LIMIT + $post["offset"];
	    if($count_prod > $offset) {
		$ret[0] = 1;
	    }
	    else {
		$ret[0] = 0;
	    }
	    $ret[1] = $offset;
	    $ret[2] = $list;
	    return $ret;
        }
    }
    
    public function order_mail($post = []){
        //print_r($post);
        //send mail
        $admin_data = $this->db->where('key', 'admin_email_address')->get('setting')->row_array();
        $order_details = $this->order_details($post);
        $to = $admin_data['value'];
        $subject = 'A New Order Placed';
        $msg = $this->load->view('mail_tmp/header', $admin_data, true);
        $msg .= $this->load->view('mail_tmp/order_details', $order_details, true);
        $msg .= $this->load->view('mail_tmp/footer', $admin_data, true);
        $this->m_api->send_mail($to, $subject, $msg);
    }
    
    public function make_fav_unfav_product($post = []){
        if($post["is_fav"] == 1){
            $is_fav = 1;
        }
        else if($post["is_fav"] == 0){
            $is_fav = 0;
        }
        
        $exists = $this->db->select("*")
                ->where('product_id', $post['product_id'])
                ->where('user_id', $post['user_id'])
                ->get('product_favourite')->row_array();
        
        if(!empty($exists)) {
            $in_arr = array(
                'user_id' => $post["user_id"],
                'product_id' => $post["product_id"],
                'status' => $is_fav,
            );

            $this->db->set($in_arr)
                ->where('product_id', $post['product_id'])
                ->where('user_id', $post['user_id'])
                ->update('product_favourite');
        }
        else {
            $in_arr = array(
                'user_id' => $post["user_id"],
                'product_id' => $post["product_id"],
                'status' => $is_fav,
            );

            $this->db->insert('product_favourite', $in_arr);
        }
            
        
        if($is_fav == 1){
            return true;
        }
        else if($is_fav == 0){ 
            return false;
        }
    }
    
    public function get_favourite_product_list($post = []){
        $fv_prod = array();
        $favourites = $this->db->select("user_id, product_favourite.product_id")
                ->join('products', 'products.product_id = product_favourite.product_id')
                ->where('product_favourite.user_id', $post["user_id"])
                ->where('product_favourite.status', 1)
                ->where('products.status', 1)
                ->limit(LIMIT)
		->offset($post["offset"])
		->get('product_favourite')->result_array();
        
        if(!empty($favourites)) {
            foreach($favourites as $key => $value){
                $product = $this->get_product_details_by_id($value);
                //print_r($product);
                if(!empty($product)){
                    //$favourites[$key] = $product;
                    array_push($fv_prod, $product);
                }                
            }
	    
	    $count_prod = $this->db->select("user_id, product_favourite.product_id")
		    ->join('products', 'products.product_id = product_favourite.product_id')
		    ->where('product_favourite.user_id', $post["user_id"])
		    ->where('product_favourite.status', 1)
		    ->where('products.status', 1)
		    ->get('product_favourite')
		    ->num_rows();

	    $offset = LIMIT + $post["offset"];
	    if($count_prod > $offset) {
		$ret[0] = 1;
	    }
	    else {
		$ret[0] = 0;
	    }
	    $ret[1] = $offset;
	    $ret[2] = $fv_prod;
	    return $ret;             
        }
        return false;
    }    
    
    public function brandwise_product_list($post = []){        
               
        $prod_list = $this->db->select("products.*, product_details.volume_id, category_mst.category_name, brand_mst.brand_name", false)
                ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1')
                ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1')
                ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1')
                ->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
		->where("products.status", 1)
                ->where("products.brand_id", $post["brand_id"])
                ->limit(LIMIT)
		->offset($post["offset"])
		->group_by('products.product_id')
                ->order_by("products.date", "desc")
		->get("products")
                ->result_array();  
        
        if(!empty($prod_list)){
        
            foreach($prod_list as $key => $value){ 
		
		$msg = str_replace(PHP_EOL,"@/@", $prod_list[$key]["description"]);
		$prod_list[$key]["description"] = json_decode('"'.$msg.'"');
		$prod_list[$key]["description"] = str_replace("@/@",PHP_EOL, $prod_list[$key]["description"]);
                
                if($value["seller_id"] != 0) {
                    //seller info
                    $seller = $this->db->select("seller_id, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender", false)
                            ->where('seller_id', $value['seller_id'])
                            ->where('status', 1)
                            ->where('is_admin_verified', 1)
                            ->get('seller')->row_array();

                    $seller2 = array_map(function($val) {
                        if(is_null($val)) {
                            $val = "";
                        }
                        return $val;
                    }, $seller);

                    $prod_list[$key]['seller'] = $seller2;
                }
                else {
                    $prod_list[$key]['seller'] = array(
                        'seller_name' => "Admin"
                    );
                }
                
                //get favourite
                $favourite = $this->db->select("user_id, product_id, status")
                        ->where('product_id', $value['product_id'])
                        ->where('user_id', $post['user_id'])
                        ->get('product_favourite')->row_array();
                
                
                if(!empty($favourite)){
                    $prod_list[$key]['is_favourite'] = $favourite["status"];
                }
                else {
                    $prod_list[$key]['is_favourite'] = 0;
                }
                
                //Get Product Volume
                $this->db->select("product_details.*, concat(volume_mst.volumne_value,' ',(select volume_type from volume_type where volume_type_id = volume_mst.type)) as volumes", false)
                        ->from("products")
                        ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
                        ->join('volume_mst', 'product_details.volume_id=volume_mst.volume_id and volume_mst.status=1')
                        ->where("products.product_id", $value['product_id'])
                        ->where("products.status", 1)
                        ->order_by("products.date", "desc");
                $get_data_volume = $this->db->get()
                        ->result_array();

                //print_r($get_data_volume); exit;
                if(!empty($get_data_volume)){ 

                    $prod_list[$key]["price"] = $get_data_volume[0]["actual_price"];

                    foreach($get_data_volume as $vkey => $vvalue){ 
                        $get_data_volume[$vkey]["actual_price"] = $vvalue["actual_price"];
                    }                    
                    $prod_list[$key]['isvolume'] = true;
                }
                else {
                    $prod_list[$key]['isvolume'] = false;
                }

                //Get Product Return policy
                $this->db->select("product_return_policy.*", false)
                        ->from("products")
                        ->join('product_return_policy', 'products.product_id = product_return_policy.product_id')
                        ->where("products.product_id", $value['product_id'])
                        ->where("products.have_return_policy", 1)
                        ->where("products.status", 1)
                        ->order_by("products.date", "desc");
                $get_data_policy = $this->db->get()
                            ->row_array();
                
                if(!empty($get_data_policy)){
                    $get_data_policy["description"] = html_entity_decode(strip_slashes(strip_tags($get_data_policy["description"], '<strong>')));
                    $get_data_policy["status"] = true;
                } else {
                    $get_data_policy["status"] = false;
                }

                $prod_list[$key]['return_policy'] = $get_data_policy;
                $prod_list[$key]['volume'] = $get_data_volume;

                if($value['feature_img']){                
                    $prod_list[$key]['feature_img'] = $this->image_url_product( $value['feature_img']);   
                    $prod_list[$key]['feature_img_thumb'] = $this->image_url_product( $value['feature_img'],'thumb');     
                }
                else {
                    $prod_list[$key]['feature_img'] = '';  
                    $prod_list[$key]['feature_img_thumb'] = '';  
                }

            }
        }
        
        if (!empty($prod_list)) {
            $count_prod = $this->db->select("products.*, product_details.volume_id, category_mst.category_name, brand_mst.brand_name", false)
		    ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1')
		    ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1')
		    ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1')
		    ->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
		    ->where("products.status", 1)
		    ->where("products.brand_id", $post["brand_id"])		    
		    ->group_by('products.product_id')
		    ->order_by("products.date", "desc")
		    ->get("products")
		    ->num_rows();

	    $offset = LIMIT + $post["offset"];
	    if($count_prod > $offset) {
		$ret[0] = 1;
	    }
	    else {
		$ret[0] = 0;
	    }
	    $ret[1] = $offset;
	    $ret[2] = $prod_list;
	    return $ret;            
        }        
        else {
            return false;
        }
    }
    
    public function search_brandwise_products($post=[]){        
       
        if(isset($post["chr"]) && $post["chr"] != NULL ) {
            $where = 'products.status = 1 AND products.product_name LIKE "%'.$post["chr"].'%"';             
        }
        else {
            $where = "products.status = 1 ";                       
        }
        
        $prod_list = $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", FALSE)
            ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1', 'left')
            ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1', 'left')
            ->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
	    ->where($where)
            ->where("products.brand_id", $post["brand_id"])
            ->limit(LIMIT)
	    ->offset($post["offset"])
	    ->order_by("products.date", "desc")
	    ->get('products')
            ->result_array();  
        
        if (!empty($prod_list)) {        
            foreach($prod_list as $key => $value){   
                
                if($value["seller_id"] != 0) {
                    //seller info
                    $seller = $this->db->select("seller_id, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender", false)
                            ->where('seller_id', $value['seller_id'])
                            ->where('status', 1)
                            ->where('is_admin_verified', 1)
                            ->get('seller')->row_array();

                    $seller2 = array_map(function($val) {
                        if(is_null($val)) {
                            $val = "";
                        }
                        return $val;
                    }, $seller);

                    $prod_list[$key]['seller'] = $seller2;
                }
                else {
                    $prod_list[$key]['seller'] = array(
                        'seller_name' => "Admin"
                    );
                }
                
                //get favourite
                $favourite = $this->db->select("user_id, product_id, status")
                        ->where('product_id', $value['product_id'])
                        ->where('user_id', $post['user_id'])
                        ->get('product_favourite')->row_array();                
                
                if(!empty($favourite)){
                    $prod_list[$key]['is_favourite'] = $favourite["status"];
                }
                else {
                    $prod_list[$key]['is_favourite'] = 0;
                }

                //Get Product Volume
                $this->db->select("product_details.*, concat(volume_mst.volumne_value,' ',(select volume_type from volume_type where volume_type_id = volume_mst.type)) as volumes", false)
                        ->from("products")
                        ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
                        ->join('volume_mst', 'product_details.volume_id=volume_mst.volume_id and volume_mst.status=1')
                        ->where("products.product_id", $value['product_id'])
                        ->where("products.status", 1)
                        ->order_by("products.date", "desc");
                $get_data_volume = $this->db->get()
                        ->result_array();

                if(!empty($get_data_volume)){            
                    foreach($get_data_volume as $vkey => $vvalue){ 
                        $get_data_volume[$vkey]["actual_price"] =  $vvalue["actual_price"];
                    }

                    //get price for volume
                    $prod_list[$key]["price"] = $get_data_volume[0]["actual_price"];
                    $prod_list[$key]['isvolume'] = true;
                }
                else {
                    $prod_list[$key]['isvolume'] = false;
                }

                //Get Product Return policy
                $this->db->select("product_return_policy.*", false)
                        ->from("products")
                        ->join('product_return_policy', 'products.product_id = product_return_policy.product_id')
                        ->where("products.product_id", $value['product_id'])
                        ->where("products.have_return_policy", 1)
                        ->where("products.status", 1)
                        ->order_by("products.date", "desc");
                $get_data_policy = $this->db->get()
                            ->row_array();
                
                if(!empty($get_data_policy)){
                    $get_data_policy["description"] = html_entity_decode(strip_slashes(strip_tags($get_data_policy["description"], '<strong>')));
                    $get_data_policy["status"] = true;
                } else {
                    $get_data_policy["status"] = false;
                }

                $prod_list[$key]['return_policy'] = $get_data_policy;
                $prod_list[$key]['volume'] = $get_data_volume;

                if($value['feature_img']){

                    $prod_list[$key]['feature_img'] = $this->image_url_product( $value['feature_img']);   
                    $prod_list[$key]['feature_img_thumb'] = $this->image_url_product( $value['feature_img'],'thumb');     
                }
                else {
                    $prod_list[$key]['feature_img'] = '';  
                    $prod_list[$key]['feature_img_thumb'] = '';  
                }
            }            
	    
            $count_prod = $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", FALSE)
		    ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1', 'left')
		    ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1', 'left')
		    ->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
		    ->where($where)
		    ->where("products.brand_id", $post["brand_id"])		    
		    ->order_by("products.date", "desc")
		    ->get('products')
		    ->num_rows();

	    $offset = LIMIT + $post["offset"];
	    if($count_prod > $offset) {
		$ret[0] = 1;
	    }
	    else {
		$ret[0] = 0;
	    }
	    $ret[1] = $offset;
	    $ret[2] = $prod_list;
	    return $ret;          
        }        
        else {
            return false;
        }
    }
    
    public function add_rate_review($post=[]){
        if(isset($post["review"]) && !empty($post["review"])){
            $review = $post["review"];
        }
        else {
            $review = '';
        }   
        $insert_array = array(
            'user_id' => $post["user_id"],
            'product_id' => $post["product_id"],
            'rating' => $post["rating"],
            'review' => $review,
        );        
        $insert = $this->db->insert('product_rating', $insert_array);
        if($insert){
            return true;
        }
        else {
            return false;
        }
    }
    
    public function edit_rate_review($post=[]){
        $exists = $this->db->select("*")
                ->where('product_rating_id', $post["review_id"])
                ->where('user_id', $post["user_id"])
                ->where('product_id', $post["product_id"])
                ->get('product_rating')->row_array();
        $review_id = $post["review_id"];
        unset($post["review_id"]);
        
        if(!empty($exists)){
            $update = $this->db->set($post)
                    ->where('product_rating_id', $review_id)
                    ->update('product_rating');
            
            if($update){
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
    
    public function delete_rate_review($post=[]){
        $exists = $this->db->select("*")
                ->where('product_rating_id', $post["review_id"])
                ->where('user_id', $post["user_id"])
                ->where('product_id', $post["product_id"])
                ->get('product_rating')->row_array();
        $review_id = $post["review_id"];
        unset($post["review_id"]);
        
        if(!empty($exists)){
            $delete = $this->db
                    ->where('product_rating_id', $review_id)
                    ->where('user_id', $post["user_id"])
                    ->where('product_id', $post["product_id"])
                    ->delete('product_rating');
            
            if($delete){
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
    
    public function review_list($post=[]){
        $list = $this->db->select("product_rating.*, concat(user.firstname,' ',user.lastname) as name", false)
                ->join('user', 'user.user_id = product_rating.user_id')
                ->where('product_rating.product_id', $post["product_id"])
                ->limit(LIMIT)
		->offset($post["offset"])
		->get('product_rating')->result_array();
	
        if(!empty($list)){            
            foreach($list as $key => $value) {
                $list[$key]["rating"] = number_format($value["rating"],1);
                $value2 = array_map(function($val) {
                    if(is_null($val)) {
                        $val = "";
                    }
                    return $val;
                }, $value);
                $list[$key] = $value2;     
                
                
            }
            
	    $count_prod = $this->db->select("product_rating.*, concat(user.firstname,' ',user.lastname) as name", false)
		    ->join('user', 'user.user_id = product_rating.user_id')
		    ->where('product_rating.product_id', $post["product_id"])		    
		    ->get('product_rating')
		    ->num_rows();

	    $offset = LIMIT + $post["offset"];
	    if($count_prod > $offset) {
		$ret[0] = 1;
	    }
	    else {
		$ret[0] = 0;
	    }
	    $ret[1] = $offset;
	    $ret[2] = $list;
	    return $ret;
        }
    }
    
    public function get_review_by_id($post=[]){
        $list = $this->db->select("*")
                ->where('product_id', $post["product_id"])
                ->where('user_id', $post["user_id"])
                ->get('product_rating')->row_array();
        if(!empty($list)){
            
            $list2 = array_map(function($val) {
                if(is_null($val)) {
                    $val = "";
                }
                return $val;
            }, $list);
                
            return $list2;
        }
    }
    
    public function add_seller_rating($post=[]){
        $insert_array = array(
            'user_id' => $post["user_id"],
            'seller_id' => $post["seller_id"],
            'rating' => $post["rating"],
            'product_id' => $post["product_id"]
        );        
        $insert = $this->db->insert('seller_rating', $insert_array);
        if($insert){
            return true;
        }
        else {
            return false;
        }
    }
    
    public function edit_seller_rating($post=[]){
        $exists = $this->db->select("*")
                ->where('seller_rating_id', $post["seller_rating_id"])
                ->where('user_id', $post["user_id"])
                ->where('product_id', $post["product_id"])
                ->where('seller_id', $post["seller_id"])
                ->get('seller_rating')->row_array();
                
        if(!empty($exists)){
            $update = $this->db->set($post)
                    ->where('seller_rating_id', $post["seller_rating_id"])
                    ->update('seller_rating');
            
            if($update){
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
    
    public function delete_seller_rating($post=[]){
        $exists = $this->db->select("*")
                ->where('seller_rating_id', $post["seller_rating_id"])
                ->where('user_id', $post["user_id"])
                ->where('seller_id', $post["seller_id"])
                ->where('product_id', $post["product_id"])
                ->get('seller_rating')->row_array();
       
        if(!empty($exists)){
            $delete = $this->db
                    ->where('seller_rating_id', $post["seller_rating_id"])
                    ->where('user_id', $post["user_id"])
                    ->where('seller_id', $post["seller_id"])
                    ->delete('seller_rating');
            
            if($delete){
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
    
    public function get_seller_rating($post=[]){
        $average_rating = $this->db->select("AVG(rating) as seller_rating")
                        ->where('seller_id', $post['seller_id'])
                        ->get('seller_rating')->row_array();
        
        if(!empty($average_rating)) {
            return number_format($average_rating["seller_rating"],1);
        }
        else {
            return 0;
        }
    }
    
    public function get_seller_rating_by_id($post=[]){
        $list = $this->db->select("*")
                ->where('seller_rating_id', $post["seller_rating_id"])
                ->where('user_id', $post["user_id"])
                ->get('seller_rating')->row_array();
        if(!empty($list)){
            return $list;
        }
    }
    
    public function update_user_location($post=[]){
        $check = $this->db->select("user_id")->where("user_id", $post["user_id"])->get("user")->row_array();
        if(!empty($check)){
            $update = $this->db->set("latitude", $post["latitude"])->set("longitude", $post["longitude"])->where("user_id", $post["user_id"])->update("user");
            if($update){
                return true;
            }
            else {
                return false;
            }
        }        
    }
    
    public function get_schedule_list($post = []){
        $response = $this->db->select("*")->where('status', 1)->get("schedule_order_list")->result_array();
        if(!empty($response)){
            return $response;
        }
    }
    
    public function order_cancel_request($post = []){        
        $where = "order_status IN (1,6)";
        //Check order is eligible to return
        $get_order = $this->db->select("*")
                ->where('order_id', $post["order_id"])
                ->where('user_id', $post["user_id"])
                ->where($where)
                ->get("orders")->row_array();
        
        if(!empty($get_order)){
            
            $order_details = $this->db->select("*")->where('order_id', $post["order_id"])->get('orders')->row_array();
       
            //update order cancel table
            $insert_array = array(
                'user_id' => $post["user_id"],
                'order_id' => $post["order_id"],
                'reason' => $post["reason"],
                'is_confirmed' => 1,
                'amount_refunded' => $order_details["gross_amount"],
                'payment_status' => 'SUCCESS',
                'update_date' => date('Y-m-d H:i:s'),
                'payment_history' => '{"status":"true", "payment":"success", "amount":'.$order_details["gross_amount"].'}',
            ); 
            //print_r($set); exit;
            $insert = $this->db->insert("order_canceled", $insert_array);   

            //update orders table
            $set1 = array(
                'order_status' => 5,
                'order_cancellation_reason' => $post["reason"],
                'updated_date' => date('Y-m-d H:i:s'),
            ); 
            //print_r($set1); exit;
            $this->db->set($set1)->where('order_id', $post["order_id"])->update('orders');

            //insert in order transaction table
            $set_arr2 = array(
                'order_id' => $post["order_id"],
                'payment_status' => 'SUCCESS',
                'transaction_id' => date('YmdHis').$post["order_id"].$post["user_id"],
                'payment_mode' => 4,
                'is_cancelled' => 1,
                'payment_history' => '{"status":"true", "payment":"success", "amount":'.$order_details["gross_amount"].'}',
            ); 

            //print_r($set_arr2); exit;
            $this->db->insert('order_transaction',$set_arr2);  

            //get user wallet balance
            $wallet = $this->db->select('wallet')->where('user_id', $post["user_id"])->get('user')->row_array();        
            //update wallet balance
            $wallet_amt = $wallet["wallet"] + $order_details["gross_amount"];        
	    
	    $wallet_arr = array(
                'user_id' => $post["user_id"],
                'order_id' => $post["order_id"],
                'type' => 2,
                'debit_credit_amount' => $order_details["net_amount"],
                'balance_amount' => $wallet_amt,
                'payment_status' => 'SUCCESS',
                'payment_history' => '{"status":"true", "payment":"success", "amount":'.$order_details["net_amount"].'}',
                'transaction_id' => date('YmdHis').$post["order_id"].$post["user_id"],
            );            
            $this->db->insert('wallet_history', $wallet_arr);
	    
            $this->db->set('wallet', $wallet_amt)->where('user_id', $post["user_id"])->update('user');  
        
            
            if($insert){
                return 1;
            }
            else {
                return 3;
            }
        }
        else {
            return 2;
        }
    }
    
    public function price_filter($post = []){
        $new_array = array();
        $price = array();
        $i = 0;
                
       /* if(!empty($post["latitude"]) || !empty($post["longitude"])) {
            //find nearest seller
            $nearest_seller = $this->db->select('seller_id, seller_name, company_name, email, contact_no, address, latitude, longitude, case when gender=1 then "Male" when gender=2 then "Female" end as gender, get_distance_metres(latitude, longitude, '.$post["latitude"].' , '.$post["longitude"].') as distance', false)        
                                ->where('status', 1)
                                ->where('is_admin_verified', 1)
                                ->get('seller')->result_array();

            if(!empty($nearest_seller)) {
                foreach($nearest_seller as $value1){
                    //calculate distance
                    $distance = round(($value1["distance"] / 1609.34), 2);
                    
                    $i++;
		    
		    $mile_limit = $this->get_mile_limit();
                    //seller within .. km
                    if($distance <= $mile_limit and $distance >= 0){  
                        //get min and max amount
                        $max_min_amt = $this->db->select("COALESCE(MAX(product_details.actual_price), 0) as max_amount, COALESCE(MIN(product_details.actual_price), 0) as min_amount", false)
                                ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1')
                                ->where("products.status", 1)
                                ->where("products.seller_id", $value1["seller_id"])
                                ->get("products")
                                ->row_array();  
                        
                        if(!empty($max_min_amt)){                            
                            $new_array["max_amount_".$i] = $max_min_amt["max_amount"];
                            $new_array["min_amount_".$i] = $max_min_amt["min_amount"];
                        }       
                        else {
                            return false;
                        }
                    }
                }
                
                $price["max_amount"] = max($new_array);
                $price["min_amount"] = min($new_array);
                return $price;
            }
            else {
                return false;
            }
        }
        else {*/
            $max_min_amt = $this->db->select("COALESCE(MAX(product_details.actual_price), 0) as max_amount, COALESCE(MIN(product_details.actual_price), 0) as min_amount", false)
                    ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1')
                    ->where("products.status", 1)
                    ->get("products")
                    ->row_array();  
            
            if (!empty($max_min_amt)) {
                $new_array["max_amount"] = $max_min_amt["max_amount"];
                $new_array["min_amount"] = $max_min_amt["min_amount"];
                return $new_array;            
            }        
            else {
                return false;
            }
        //}
    }
    
    public function get_brand_product_search_list($post=[]){  
        $product_array = array();
        if(!empty($post["latitude"]) || !empty($post["longitude"])) {
            //find nearest seller
            $nearest_seller = $this->db->select("seller_id, get_distance_metres(latitude, longitude, ".$post["latitude"].",".$post["longitude"].") as distance", false)        
                                ->where('status', 1)
                                ->where('is_admin_verified', 1)
                                ->get('seller')->result_array();

            if(!empty($nearest_seller)) {
                foreach($nearest_seller as $value1){
                    //calculate distance
                    $distance = round(($value1["distance"] / 1609.34), 2);
                    
		    $mile_limit = $this->get_mile_limit();
                    //seller within 10 km
                    if($distance <= $mile_limit and $distance >= 0){
                        if(isset($post["chr"]) && $post["chr"] != NULL ) {
                            $where = 'status = 1 AND seller_id = '.$value1["seller_id"].' AND (product_name LIKE "%'.$post["chr"].'%")';             
                        }
                        else {
                            $where = "status = 1 AND seller_id = ".$value1['seller_id']." ";                       
                        }

                        $data2 = $this->db->select("product_id as id, product_name as name", FALSE)
                            ->where($where)
                            ->order_by("date", "desc")
                            ->get('products')
                            ->result_array();  

                        if(!empty($data2)) {
                            foreach($data2 as $k2 => $v2) {
                                $data2[$k2]["search_type"] = "product";
                            }                            
                        }
                        
                        //Brands
                        $chr1 = 'status = 1 and brand_name LIKE "%'.$post["chr"].'%"';
                        $data1 = $this->db->select("brand_id as id, brand_name as name")
                                ->where($chr1)->get("brand_mst")->result_array();
                        if(!empty($data1)) {
                            foreach($data1 as $k1 => $v1) {
                                $data1[$k1]["search_type"] = "brand";
                            }
                        }
                        
                        //merge response
                        $response1 = array_merge($data2, $data1);
                        if(!empty($response1)) {
                            $response = array();              
                            foreach($response1 as $k3 => $v3) {
                                if(!in_array($v3["name"], array_column($response, 'name'))) {                 
                                    array_push($response, $v3);
                                }
                            }
                            //print_r($response);
                            array_push($product_array, $response);
                        } 
                    }
                }
                
                return $product_array;
            }
        }
        else {
            //Brands
            $chr1 = 'status = 1 and brand_name LIKE "%'.$post["chr"].'%"';
            $data1 = $this->db->select("brand_id as id, brand_name as name")
                    ->where($chr1)->get("brand_mst")->result_array();
            if(!empty($data1)) {
                foreach($data1 as $k1 => $v1) {
                    $data1[$k1]["search_type"] = "brand";
                }
            }        
            //Products
            $chr2 = 'status = 1 and product_name LIKE "%'.$post["chr"].'%"';
            $data2 = $this->db->select("product_id as id, product_name as name")
                    ->where($chr2)->get("products")->result_array();        
            if(!empty($data2)) {
                foreach($data2 as $k2 => $v2) {
                    $data2[$k2]["search_type"] = "product";
                }
            }
            //merge response
            $response1 = array_merge($data2, $data1);
            if(!empty($response1)) {
                $response = array();              
                foreach($response1 as $k3 => $v3) {
                    if(!in_array($v3["name"], array_column($response, 'name'))) {                 
                        array_push($response, $v3);
                    }
                }
                //print_r($response);
                return $response;   
            }     
        } 
    }
    
    public function get_abv_list($post = []) {	
	
//	$response = $this->db->select("abv_percent", false)
//		->where("status", 1)
//		->group_by("abv_percent")
//		->get("products")
//		->result_array();
	
        $list = array();
        $response = $this->db->select("COALESCE(MAX(abv_percent), 0) as max_abv_percent, COALESCE(MIN(abv_percent), 0) as min_abv_percent", false)
		->where("status", 1)
		->get("products")
		->row_array();
	
	//print_r($response);
	
        if(!empty($response)) {
            $list["max_abv_percent"] = $response["max_abv_percent"];
	    $list["min_abv_percent"] = $response["min_abv_percent"];
	    return $list;
        }
        else {
            return false;
        }
    }
    
    public function get_country_list($post = []) {
//        $response = $this->db->select("products.country_id, products.country_id as name")
//	       ->group_by("products.country_id")
//	       ->get("products")->result_array();
	
        $response = $this->db->select("country_id as id, name as country_id, name, code")
                ->get("country")->result_array();
	
        if(!empty($response)) {			
//	    foreach($response as $key => $value) {
//		if(is_null($value["country_id"])) {
//		    //echo $key;
//		    $k = $key;
//		    unset($response[$k]); 
//		}
//	    } 
//	    $resp = array_values($response);
//	    return $resp;
	    return $response;
        }
        else {
            return false;
        }
    }
    
    public function best_selling_products($post = []) {
        $top_products = array();
        if(!empty($post["latitude"]) || !empty($post["longitude"])) {
            //find nearest seller
            $nearest_seller = $this->db->select('seller_id, seller_name, company_name, email, contact_no, address, latitude, longitude, case when gender=1 then "Male" when gender=2 then "Female" end as gender, get_distance_metres(latitude, longitude, '.$post["latitude"].' , '.$post["longitude"].') as distance', false)        
                                ->where('status', 1)
                                ->where('is_admin_verified', 1)
                                ->get('seller')->result_array();

            if(!empty($nearest_seller)) {
               // print_r($nearest_seller);
                foreach($nearest_seller as $value1){
                    //calculate distance
                    $distance = round(($value1["distance"] / 1609.34), 2);
                    
		    $mile_limit = $this->get_mile_limit();
                    //seller within 10 km
                    if($distance <= $mile_limit and $distance >= 0){
                        
                        $top_pick = $this->db->select("order_product.product_id, count(order_product.product_id) as max_product, products.*, category_mst.category_name, brand_mst.brand_name", false)
                                ->join("products", "products.product_id = order_product.product_id and products.status=1")
                                ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1')
                                ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1')
                                ->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
				->where("order_product.seller_id", $value1["seller_id"])
                                ->group_by("order_product.product_id")
                                ->order_by("max_product", "desc")
                                ->limit(10)
                                ->get("order_product")->result_array();
                       
                        //print_r($top_pick); exit;
                        if (!empty($top_pick)) {         
                            //print_r($top_pick); exit;
                            foreach($top_pick as $key => $value){  

                                if($value1["seller_id"] != 0) {                                

                                    $seller2 = array_map(function($val) {
                                        if(is_null($val)) {
                                            $val = "";
                                        }
                                        return $val;
                                    }, $value1);

                                    $top_pick[$key]['seller'] = $seller2;
                                }
                                else {
                                    $top_pick[$key]['seller'] = array(
                                        'seller_name' => "Admin"
                                    );
                                }

                                //get favourite
                                $favourite = $this->db->select("user_id, product_id, status")
                                        ->where('product_id', $value['product_id'])
                                        ->where('user_id', $post['user_id'])
                                        ->get('product_favourite')->row_array();


                                if(!empty($favourite)){
                                    $top_pick[$key]['is_favourite'] = $favourite["status"];
                                }
                                else {
                                    $top_pick[$key]['is_favourite'] = 0;
                                }

                                //Get Product Volume
                                $this->db->select("product_details.*, concat(volume_mst.volumne_value,' ',(select volume_type from volume_type where volume_type_id = volume_mst.type)) as volumes", false)
                                        ->from("products")
                                        ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
                                        ->join('volume_mst', 'product_details.volume_id=volume_mst.volume_id and volume_mst.status=1')
                                        ->where("products.product_id", $value['product_id'])
                                        //->where("volume_mst.brand_id", $value['brand_id'])
                                        ->where("products.status", 1)
                                        ->order_by("products.date", "desc");
                                $get_data_volume = $this->db->get()
                                        ->result_array();

                                if(!empty($get_data_volume)){ 

                                    $top_pick[$key]["price"] = $get_data_volume[0]["actual_price"];

                                    foreach($get_data_volume as $vkey => $vvalue){ 
                                        $get_data_volume[$vkey]["actual_price"] = $vvalue["actual_price"];
                                    }
                                    $top_pick[$key]['isvolume'] = true;
                                } 
                                else {
                                    $top_pick[$key]['isvolume'] = false;
                                }

                                //Get Product Return policy
                                $this->db->select("product_return_policy.*", false)
                                        ->from("products")
                                        ->join('product_return_policy', 'products.product_id = product_return_policy.product_id')
                                        ->where("products.product_id", $value['product_id'])
                                        ->where("products.have_return_policy", 1)
                                        ->where("products.status", 1)
                                        ->order_by("products.date", "desc");
                                $get_data_policy = $this->db->get()
                                            ->row_array();

                                if(!empty($get_data_policy)){
                                    $get_data_policy["description"] = html_entity_decode(strip_slashes(strip_tags($get_data_policy["description"], '<strong>')));
                                    $get_data_policy["status"] = true;
                                } else {
                                    $get_data_policy["status"] = false;
                                }

                                $top_pick[$key]['return_policy'] = $get_data_policy;                
                                $top_pick[$key]['volume'] = $get_data_volume;

                                //feature image
                                if($value['feature_img']){

                                    $top_pick[$key]['feature_img'] = $this->image_url_product( $value['feature_img']);    
                                    $top_pick[$key]['feature_img_thumb'] = $this->image_url_product( $value['feature_img'] ,'thumb');    
                                }
                                else {
                                    $top_pick[$key]['feature_img'] = '';
                                    $top_pick[$key]['feature_img_thumb'] = '';
                                }
                                
                                array_push($top_products, $top_pick[$key]);
                            } 
                        }                          
                    }
                }
                if(!empty($top_products)) {
                    return $top_products;
                }
            }
            else {
                return false;
            }
        }
        else {
            $top_pick = $this->db->select("order_product.product_id, count(order_product.product_id) as max_product, products.*, category_mst.category_name, brand_mst.brand_name", false)
                                ->join("products", "products.product_id = order_product.product_id and products.status=1")
                                ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1')
                                ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1')
				->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
                                ->group_by("order_product.product_id")
                                ->order_by("max_product", "desc")
                                ->limit(10)
                                ->get("order_product")->result_array();

            if (!empty($top_pick)) {            

                //print_r($top_pick); exit;
                foreach($top_pick as $key => $value){  

                    if($value["seller_id"] != 0) {
                        //seller info
                        $seller = $this->db->select("seller_id, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender", false)
                                ->where('seller_id', $value['seller_id'])
                                ->where('status', 1)
                                ->where('is_admin_verified', 1)
                                ->get('seller')->row_array();

                        $seller2 = array_map(function($val) {
                            if(is_null($val)) {
                                $val = "";
                            }
                            return $val;
                        }, $seller);

                        $top_pick[$key]['seller'] = $seller2;
                    }
                    else {
                        $top_pick[$key]['seller'] = array(
                            'seller_name' => "Admin"
                        );
                    }

                    //get favourite
                    $favourite = $this->db->select("user_id, product_id, status")
                            ->where('product_id', $value['product_id'])
                            ->where('user_id', $post['user_id'])
                            ->get('product_favourite')->row_array();


                    if(!empty($favourite)){
                        $top_pick[$key]['is_favourite'] = $favourite["status"];
                    }
                    else {
                        $top_pick[$key]['is_favourite'] = 0;
                    }

                    //Get Product Volume
                    $this->db->select("product_details.*, concat(volume_mst.volumne_value,' ',(select volume_type from volume_type where volume_type_id = volume_mst.type)) as volumes", false)
                            ->from("products")
                            ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
                            ->join('volume_mst', 'product_details.volume_id=volume_mst.volume_id and volume_mst.status=1')
                            ->where("products.product_id", $value['product_id'])
                            //->where("volume_mst.brand_id", $value['brand_id'])
                            ->where("products.status", 1)
                            ->order_by("products.date", "desc");
                    $get_data_volume = $this->db->get()
                            ->result_array();

                    if(!empty($get_data_volume)){ 

                        $top_pick[$key]["price"] = $get_data_volume[0]["actual_price"];

                        foreach($get_data_volume as $vkey => $vvalue){ 
                            $get_data_volume[$vkey]["actual_price"] = $vvalue["actual_price"];
                        }
                        $top_pick[$key]['isvolume'] = true;
                    } 
                    else {
                        $top_pick[$key]['isvolume'] = false;
                    }

                    //Get Product Return policy
                    $this->db->select("product_return_policy.*", false)
                            ->from("products")
                            ->join('product_return_policy', 'products.product_id = product_return_policy.product_id')
                            ->where("products.product_id", $value['product_id'])
                            ->where("products.have_return_policy", 1)
                            ->where("products.status", 1)
                            ->order_by("products.date", "desc");
                    $get_data_policy = $this->db->get()
                                ->row_array();

                    if(!empty($get_data_policy)){
                        $get_data_policy["description"] = html_entity_decode(strip_slashes(strip_tags($get_data_policy["description"], '<strong>')));
                        $get_data_policy["status"] = true;
                    } else {
                        $get_data_policy["status"] = false;
                    }

                    $top_pick[$key]['return_policy'] = $get_data_policy;                
                    $top_pick[$key]['volume'] = $get_data_volume;

                    //feature image
                    if($value['feature_img']){

                        $top_pick[$key]['feature_img'] = $this->image_url_product( $value['feature_img']);    
                        $top_pick[$key]['feature_img_thumb'] = $this->image_url_product( $value['feature_img'] ,'thumb');    
                    }
                    else {
                        $top_pick[$key]['feature_img'] = '';
                        $top_pick[$key]['feature_img_thumb'] = '';
                    }

                }        

                return $top_pick;            
            }        
            else {
                return false;
            }
        }
    }
    
    public function suggested_products($post = []) {
        $top_products = array();
        
        $query = $this->db->query("select order_product.product_id from order_product join orders on orders.order_id = order_product.order_id and orders.user_id = 60 group by order_product.product_id UNION select product_favourite.product_id from product_favourite where product_favourite.status = 1 and product_favourite.user_id = 60 UNION select cart_product.product_id from cart_product join cart on cart.cart_id = cart_product.cart_id and cart.user_id = 60 where cart_product.status = 1 group by cart_product.product_id");
        $result = $query->result_array();
        $suggested_products = implode(",", array_column($result, "product_id"));
        //print_r($suggested_products); exit;     
       
        $this->db->select("products.*, category_mst.category_name, brand_mst.brand_name", false)
                ->from("products")
                ->join('category_mst', 'products.category_id=category_mst.category_id and category_mst.status=1')
                ->join('brand_mst', 'products.brand_id=brand_mst.brand_id and brand_mst.status=1')
		->join('seller', 'products.seller_id=seller.seller_id and seller.status=1')
                ->where("products.product_id IN (".$suggested_products.")")
                ->where("products.status", 1)
                ->group_by('products.product_id')
                ->order_by("products.date", "desc");
        $top_pick = $this->db->get()
                ->result_array();

        if (!empty($top_pick)) {            

            //print_r($top_pick); exit;
            foreach($top_pick as $key => $value){  
		
		$msg = str_replace(PHP_EOL,"@/@", $top_pick[$key]["description"]);
		$top_pick[$key]["description"] = json_decode('"'.$msg.'"');
		$top_pick[$key]["description"] = str_replace("@/@",PHP_EOL, $top_pick[$key]["description"]);

                if($value["seller_id"] != 0) {
                    //seller info
                    $seller = $this->db->select("seller_id, seller_name, company_name, email, contact_no, address, case when gender=1 then 'Male' when gender=2 then 'Female' end as gender", false)
                            ->where('seller_id', $value['seller_id'])
                            ->where('status', 1)
                            ->where('is_admin_verified', 1)
                            ->get('seller')->row_array();

                    $seller2 = array_map(function($val) {
                        if(is_null($val)) {
                            $val = "";
                        }
                        return $val;
                    }, $seller);

                    $top_pick[$key]['seller'] = $seller2;
                }
                else {
                    $top_pick[$key]['seller'] = array(
                        'seller_name' => "Admin"
                    );
                }

                //get favourite
                $favourite = $this->db->select("user_id, product_id, status")
                        ->where('product_id', $value['product_id'])
                        ->where('user_id', $post['user_id'])
                        ->get('product_favourite')->row_array();


                if(!empty($favourite)){
                    $top_pick[$key]['is_favourite'] = $favourite["status"];
                }
                else {
                    $top_pick[$key]['is_favourite'] = 0;
                }

                //Get Product Volume
                $this->db->select("product_details.*, concat(volume_mst.volumne_value,' ',(select volume_type from volume_type where volume_type_id = volume_mst.type)) as volumes", false)
                        ->from("products")
                        ->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1', 'left')
                        ->join('volume_mst', 'product_details.volume_id=volume_mst.volume_id and volume_mst.status=1')
                        ->where("products.product_id", $value['product_id'])
                        //->where("volume_mst.brand_id", $value['brand_id'])
                        ->where("products.status", 1)
                        ->order_by("products.date", "desc");
                $get_data_volume = $this->db->get()
                        ->result_array();

                if(!empty($get_data_volume)){ 

                    $top_pick[$key]["price"] = $get_data_volume[0]["actual_price"];

                    foreach($get_data_volume as $vkey => $vvalue){ 
                        $get_data_volume[$vkey]["actual_price"] = $vvalue["actual_price"];
                    }
                    $top_pick[$key]['isvolume'] = true;
                } 
                else {
                    $top_pick[$key]['isvolume'] = false;
                }

                //Get Product Return policy
                $this->db->select("product_return_policy.*", false)
                        ->from("products")
                        ->join('product_return_policy', 'products.product_id = product_return_policy.product_id')
                        ->where("products.product_id", $value['product_id'])
                        ->where("products.have_return_policy", 1)
                        ->where("products.status", 1)
                        ->order_by("products.date", "desc");
                $get_data_policy = $this->db->get()
                            ->row_array();

                if(!empty($get_data_policy)){
                    $get_data_policy["description"] = html_entity_decode(strip_slashes(strip_tags($get_data_policy["description"], '<strong>')));
                    $get_data_policy["status"] = true;
                } else {
                    $get_data_policy["status"] = false;
                }

                $top_pick[$key]['return_policy'] = $get_data_policy;                
                $top_pick[$key]['volume'] = $get_data_volume;

                //feature image
                if($value['feature_img']){

                    $top_pick[$key]['feature_img'] = $this->image_url_product( $value['feature_img']);    
                    $top_pick[$key]['feature_img_thumb'] = $this->image_url_product( $value['feature_img'] ,'thumb');    
                }
                else {
                    $top_pick[$key]['feature_img'] = '';
                    $top_pick[$key]['feature_img_thumb'] = '';
                }

            }        

            return $top_pick;            
        }        
        else {
            return false;
        }        
    }
    
    public function redeem_gift_card($post = []) {        
                
        $check = $this->db->select("*")
                ->where("redeem_code", $post["code"])
                ->get("gift_card")->row_array();
        
        if(!empty($check)) {
	
	    if($check["status"] == 1 && $check["is_redeem"] == 1) {
		return 2;
	    }
	    else {  
		$return = array();
            
		//get email
		$userdata = $this->get_user_by_id($post["user_id"]);

		//update the status
		$this->db->set("is_redeem", 1)
			->set("gift_car_email", $check["receiver_email"])
			->set("receiver_email", $userdata["email"])	
			->where("redeem_code", $post["code"])
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
		$this->m_api->send_mail($to, $subject, $msg);                       


		$return["amount"] = $check["amount"];
		$return["email"] = $check["receiver_email"];

		return $return;
	    
	    }
        }
        else {
            return 1;
        }
    }
    
    public function read_notification($post = []){
        $update = $this->db->set('is_read', 1)->where('is_read', 0)->where('to_user_id', $post["user_id"])->update('notification');
        if($update){
            return true;
        }
        else {
            return false;
        }
    }
    
    function get_order_by_id($order_id) {
	$response = $this->db->select("*")->where("order_id", $order_id)->get("orders")->row_array();
	return $response;
    }
    
    function get_gift_by_id($card_id) {
	$response = $this->db->select("*")->where("card_id", $card_id)->get("gift_card")->row_array();
	return $response;
    }
    
    function get_delivery_charge(){
	$charge = $this->db->select("value")->where("key", "delivery_charges")->get("setting")->row_array();
	return $charge["value"];
    }
    
    function add_web_notification($post=[]){    
        $sell = array();
        $products = $this->db->select("product_id")->where('order_id', $post["order_id"])->get('order_product')->result_array();
        foreach ($products as $p){
            $seller = $this->db->select("seller_id")->where('product_id', $p["product_id"])->get("products")->row_array();
            if(!in_array($seller["seller_id"], $sell)){
                array_push($sell, $seller["seller_id"]);
            }            
        }
	
	$order_no = $this->db->select("order_no")->where('order_id', $post["order_id"])->get('orders')->row_array();
        
        foreach($sell as $s){            
            //notification
            $insert_arr = array(
                'order_id' => $post["order_id"],
                'notification_type' => 1,
                'message' => 'Order placed at '.date("Hi").' on '.date("d-m-y").'. Order number '.$order_no["order_no"],
                'seller_id' => $s,
            );
            $this->db->insert('website_notification', $insert_arr);
            
            if($s != 0){
                $contact = $this->db->select("concat(country_code,' ',contact_no) as phone, email", false)->where("seller_id", $s)->get("seller")->row_array();
                
                //sms
                $this->nexmo->sms($contact['phone'], 'Order placed at '.date("Hi").' on '.date("d-m-y").'. Order number '.$order_no["order_no"]);
                
                //email
                $this->send_mail($contact["email"], "Drinxin - New Order", 'Order placed at '.date("Hi").' on '.date("d-m-y").'. Order number '.$order_no["order_no"]);
            }
        }
        return true;
    }    
       
    public function get_cart_count($post = []){
        //get cart
        $count_cart_items = $this->db->select("cart_product.cart_product_id")
                ->join('cart', 'cart.cart_id = cart_product.cart_id')
                ->where('cart.user_id', $post["user_id"])
                ->get('cart_product')->num_rows();
        
        if(!empty($count_cart_items)){
            return $count_cart_items;
        }
    }
    
    public function check_for_split_delivery($post=[]){
        $check = $this->db->select("seller_id")->where('product_id', $post["product_id"])->get('products')->row_array();
        
        if(!empty($check)){
            $cart_product_list = $this->cart_product_list($post);
            
            if(!empty($cart_product_list)) {
                $last_product = end($cart_product_list);
                if($last_product["seller_id"] == $check["seller_id"]){
                    return 2;
                }
                else {
                    return 3;
                }
            }
            else{
                return 1;
            }
        }
    }
    
    public function get_seller_slot($seller_ids) {
//	$date = date('Y-m-d'); //today date
//	$final_array = array();	
//	$slota = array();
//	$duration = '60';  // split by 1 hours
//	$add_mins  = $duration * 60;	
//	$total_seller = count(explode(",",$seller_ids));
//	
//	//get key of timeslot according to weekdays            
//	for($i = 1; $i <= 7; $i++){
//	    $slot_arr = array();
//	    $final_slot_array = array();
//	    $weekday = date('w', strtotime("+$i day", strtotime($date)))+1;
//	    	    
//	    $response = $this->db->select("*")
//                ->where("seller_id IN (".$seller_ids.")")
//		->where("weekday", $weekday)
//                ->where("status", 1)
//                ->get("trading_hours")->result_array();	
//
//	    if(!empty($response)) {
//		
//		foreach($response as $k => $v) {
//		    $array_of_time = array ();		
//		    $start_time = $v["start_time"];
//		    $end_time = $v["end_time"];
//		    $starttime = strtotime ($start_time); //change to strtotime
//		    $endtime = strtotime ($end_time); //change to strtotime
//
//		    while ($starttime < $endtime){ // loop between time			
//			$stime = date ("H:i", $starttime);
//			$starttime += $add_mins; // to check endtie=me
//			$etime = date ("H:i", $starttime);
//			$array_of_time[] = $stime.' - '.$etime;
//		    }
//		    $slot_arr = array_merge($slot_arr, $array_of_time);
//		}
//
//		$slot_arr_push = array_count_values($slot_arr);
//
//		foreach($slot_arr_push as $sk => $sv) {
//		    if($total_seller == $sv) {
//			$new_arr["slot"] = $sk;
//			$new_arr["flag"] = 1;		    
//		    }
//		    else {
//			$new_arr["slot"] = $sk;
//			$new_arr["flag"] = 0;
//		    }
//		    array_push($final_slot_array, $new_arr);
//		}
//
//		array_multisort(array_column($final_slot_array, "slot"), SORT_ASC, $final_slot_array);
//
//		$final_array["day"] = date('l', strtotime("+$i day", strtotime($date)));
//		$final_array["date"] = date('Y-m-d', strtotime("+$i day", strtotime($date)));
//		$final_array["slots"] = $final_slot_array;
//		$slota[] = $final_array;
//	    }
//	}
//	
//        return $slota;
	
        $response = $this->db->select("*")
                ->where("seller_id IN (".$seller_ids.")")
                ->where("status", 1)
                ->get("trading_hours")->result_array();	
	
	if(!empty($response)) {
	    
            $date = date('Y-m-d'); //today date
            $final_array = array();
            $weekday_arr = array_column($response, "weekday");
            
            //get key of timeslot according to weekdays            
            for($i = 1; $i <= 7; $i++){
                $weekday = date('w', strtotime("+$i day", strtotime($date)))+1;
		
		if( in_array($weekday, $weekday_arr) ) {
                    $ans = array();
                    $ans["keys"] = array_keys($weekday_arr, $weekday);
                    $ans["day"] = date('l', strtotime("+$i day", strtotime($date)));
                    $ans["date"] = date('Y-m-d', strtotime("+$i day", strtotime($date)));
		    
		    $slotb = array();
		    $slot_arr = array();
                    $start_time = "";
                    $end_time = "";
		    //$duration = '180';  // split by 3 hours
		    $duration = '60';  // split by 1 hours
		    $add_mins  = $duration * 60;
		    
		    //get start and end time of slot
                    foreach($ans["keys"] as $keys => $values) {
			
                        array_push($slotb, $response[$values]);
			$start_time = $response[$values]["start_time"];
			$end_time = $response[$values]["end_time"];
			
			$array_of_time = array ();
			$starttime    = strtotime ($start_time); //change to strtotime
			$endtime      = strtotime ($end_time); //change to strtotime
			
			while ($starttime < $endtime) // loop between time
			{
			    $stime = date ("H:i", $starttime);
			    $starttime += $add_mins; // to check endtie=me
			    $etime = date ("H:i", $starttime);
			    $array_of_time[] = $stime.' - '.$etime;
			}
			$slot_arr = array_merge($slot_arr, $array_of_time);
                    }
		    
                    $final_array["day"] = date('l', strtotime("+$i day", strtotime($date)));
                    $final_array["date"] = date('Y-m-d', strtotime("+$i day", strtotime($date)));
                    $final_array["slots"] = array_unique($slot_arr);
		    sort($final_array["slots"]);
                }    
		
                $slota[] = $final_array;
            }
	    
	    return $slota;
        }  
        else {
	    return array();
	}
    }
    
    //Calcualte Price for Order
    public function calculate_price($post=[]) {     
        $final_price = array();
        
        $delivery_charge = 0; $amount_payable = 0;
        
        //Check for delivery charges   
        $config_data = $this->db->where_in('key', array('amount_for_free_delivery', 'max_discount', 'delivery_charges'))->get('setting')->result_array();
        
        foreach ($config_data as $key => $row) {
            $tmp_arr[$row['key']] = $row['value'];
        }
        
        if($post["total_amount"] > $tmp_arr["amount_for_free_delivery"]){
            $delivery_charge = 0;
        } else {
            $delivery_charge = $tmp_arr["delivery_charges"];
        }
        
        if(!empty($post["delivery_charge"])){
            $amount_payable = $post["total_amount"] + $post["delivery_charge"];
            $final_price["delivery_charge"] = $post["delivery_charge"];
        }
        else {
            $amount_payable = $post["total_amount"];
            $final_price["delivery_charge"] = 0;
        }
        $final_price["amount_payable"] = number_format($amount_payable, 2);
        
        return $final_price;
    }
    
    public function calculate_loyalty_point($user_id, $loyaltypoints='', $amount) {         
        
        //get discount
        $config_data = $this->db->where('key', 'max_discount')->get('setting')->row_array();       
        $amount_discounted = ($amount * $config_data["value"])/100;
        
        //calculate point amount
        $loyalty_point = $this->db->select('user_id, loyalty_point')
            ->where('user_id', $user_id)
            ->get('user')->row_array();

        if($loyaltypoints <= $loyalty_point["loyalty_point"]){                
            $amount_can_be_used = $loyaltypoints / 100;            
            
            if($amount_discounted <= $amount_can_be_used) {
            
                //Apply discount on amount
                $net_amount = $amount - $amount_discounted;
                //update user loyalty point
                $points = $loyalty_point["loyalty_point"] - ($amount_discounted * 100);
            }
            else if($amount_discounted > $amount_can_be_used) {
                //Apply discount on amount
                $net_amount = $amount - $amount_can_be_used;
                //update user loyalty point
                $points = $loyalty_point["loyalty_point"] - ($amount_can_be_used * 100);
            }
            
            $return_arr = array(
                'net_amount' => $net_amount,
                'points' => $points,
                'used_points' => ($amount_discounted * 100)
            );
            
            return $return_arr;
        }
        else {
            return 'You have only '.$loyalty_point["loyalty_point"].' left';
        }        
    }
    
    //Checkout
    public function checkout($post = []) { 
	//get product details
	$product_details = $this->cart_product_checkout_list($post); 
        if(!empty($product_details)){
//	    $store = array_column($product_details, "seller");
//	    $store_open = array_column($store, "is_open");
////	    print_r($store_open);
//	   
//	    if(!in_array('0', $store_open)) {
		$seller_add = array();
		$checkout = array();
		$total_delivery_charge = 0;
		$checkout["in_loyalty_club"] = false;
		$checkout["in_vip_club"] = false;
		$userdata = $this->get_user_by_id($post["user_id"]);

		foreach($product_details as $key => $pd){
		    //print_r($pd);

		    //check quantity
		    $check_qty = $this->check_quantity($pd["product_id"], $pd["volume_id"], $pd["qty"]);

		    if(!$check_qty){
			return 1;
		    } 

		    $inloyalty = $this->check_in_loyalty_club($pd["product_id"]);
		    $invip = $this->check_in_vip_club($pd["product_id"]); 

		    if($inloyalty == true) {                   
			if($pd["from_where"] == 2){
			    $checkout["in_loyalty_club"] = true;
			}                
		    }

		    if($invip == true) { 
			if($pd["from_where"] == 3){
			    $checkout["in_vip_club"] = true;
			} 
		    }

		    if(!in_array($pd["seller_id"],$seller_add)) {                
			$total_delivery_charge = $total_delivery_charge + $pd["delivery_charges"];
			array_push($seller_add, $pd["seller_id"]);
		    }
		}  
		
		//get shipping details
		$post["shipping_id"] = $userdata["shipping_id"];            
		$shipping_details = $this->get_shipping_by_id($post); 
		$checkout["shipping"] = $shipping_details;            

		//get cart total amount
		$cart = $this->get_cart($post["user_id"]);
		$post["total_amount"] = $cart["total_amount"];
		$post["delivery_charge"] = $total_delivery_charge;
		$amount_payable = $cart["total_amount"] + $total_delivery_charge;

		//get seller slots
		$sellers = implode(",", array_column($product_details, "seller_id"));
		$seller_slots = $this->get_seller_slot($sellers);
		//print_r($seller_slots); exit;
		$checkout["seller_slots"] = $seller_slots;

		//product details
		$checkout["products"] = $product_details;
		$checkout["loyalty_point"] = $userdata["loyalty_point"];
		$checkout["wallet"] = number_format($userdata["wallet"], 2);
		$checkout["total_amount"] = number_format($cart["total_amount"], 2);
		$checkout["delivery_charge"] = number_format($total_delivery_charge, 2);
		$checkout["amount_payable"] = number_format($amount_payable, 2);

		if(!empty($checkout)) {
		    return $checkout;
		}
		else {
		    return 3;
		}  
//	    }
//	    else {
//		return 2;
//	    }
	}
	else {
            return 3;
        }
    }
    
    public function confirm_order($post = []) { 
	//get cart details
        $cart = $this->get_cart($post["user_id"]);
        //get product details
        $product_details = $this->cart_product_checkout_list($post); 
	
	if(!empty($product_details)){
	    $store = array_column($product_details, "seller");
	    $store_open = array_column($store, "is_open");
//	    print_r($store_open);
	    
	    if(!in_array('0', $store_open)) {}
	    else if(in_array('0', $store_open) && $post["order_done_type"] == '1'){
		return 6;
	    }
	}
	else{
            return 5;
        }
		
	$loyalty_net_amount = 0;
	$seller_add = array();
	$total_delivery_charge = 0;

	if(!empty($cart) && !empty($product_details)){   

	    foreach($product_details as $pd){

		//check quantity
		$check_qty = $this->check_quantity($pd["product_id"], $pd["volume_id"], $pd["qty"]);

		if(!$check_qty){
		    return 1;
		} 

		if(!in_array($pd["seller_id"],$seller_add)) {                
		    $total_delivery_charge = $total_delivery_charge + $pd["delivery_charges"];
		    array_push($seller_add, $pd["seller_id"]);
		}
	    }

	    if(isset($post["send_as_gift"]) && $post["send_as_gift"] == "true"){
		$send_as_gift = 1;
	    } else if(isset($post["send_as_gift"]) && $post["send_as_gift"] == "false"){
		$send_as_gift = 0;
	    }
	    else {
		$send_as_gift = 0;
	    }

	    if(isset($post["is_pick_up"]) && $post["is_pick_up"] == '1'){
		$is_pick_up = $post["is_pick_up"];
	    }
	    else {
		$is_pick_up = 0;
	    }

	    if(isset($post["add_info"]) && !empty($post["add_info"])){
		$add_info = $post["add_info"];
	    }
	    else {
		$add_info = '';
	    }

	    $post["total_amount"] = $cart["total_amount"];  
	    $post["delivery_charge"] = $total_delivery_charge;            
	    //calculate price
	    $price = $cart["total_amount"] + $total_delivery_charge;
	    $price = number_format($price, 2);

	    //order no 
	    $order_no = rand(100, 999).date('ymdHi');
	    $track_no = rand(1000, 9999);

	    $where11 = "DATE(order_date) = CURDATE()";

	    //check exists
	    $exist = $this->db->select('*')
			->where('user_id', $post["user_id"])
			->where('shipping_id', $post["shipping_id"])
			->where('payment_done', 0)
			->where($where11)
			->get('orders')->row_array();

	    if(empty($exist)){         
		//add new order
		$insert_array = array(
		    'user_id' => $post["user_id"],
		    'order_no' => $order_no,
		    'track_no' => $track_no,
		    'shipping_id' => $post["shipping_id"],
		    'order_date' => date('Y-m-d H:i:s'),
		    'order_status' => 1,
		    'send_as_gift' => $send_as_gift,
		    'gross_amount' => $cart["total_amount"],
		    'delivery_charges' => $total_delivery_charge,
		    'total_qty' => $cart["total_qty"],
		    'net_amount' => $post["net_amount"],
		    'order_type' => $post["order_type"],
		    'is_pick_up' => $is_pick_up,
		    'add_info' => $add_info,
		    'order_done_type' => $post["order_done_type"],
		);             

		$this->db->insert('orders', $insert_array);
		$last_id = $this->db->insert_id();  

		$post["order_id"] = $last_id; 
	    }
	    else {
		$post["order_id"] = $exist["order_id"]; 
	    }

	    //to check if any product is removed
	    $oproduct_exist = $this->db->select("*")
		    ->where('order_id', $post["order_id"])
		    ->get('order_product')->result_array();

	    if(!empty($oproduct_exist)) {
		foreach($oproduct_exist as $pe) {
		    $exist_pd = $this->db->select('*')
			->where('cart_id', $cart["cart_id"])
			->where('product_id', $pe["product_id"])
			->where('volume_id', $pe["volume_id"])
			->get('cart_product')->row_array();

		    if(empty($exist_pd)) {
			$deletepro = $this->db
			    ->where('order_id', $post["order_id"])
			    ->where('product_id', $pe["product_id"])
			    ->where('volume_id', $pe["volume_id"])
			    ->delete('order_product');   
		    }
		}
	    }

	    //add order product details     
	    foreach($product_details as $pd) {
		$exist_pd = $this->db->select('*')
			->where('order_id', $post["order_id"])
			->where('product_id', $pd["product_id"])
			->where('volume_id', $pd["volume_id"])
			->get('order_product')->row_array();

		if(empty($exist_pd)) {

		    $insert_array1 = array(
			'order_id' => $post["order_id"],
			'product_id' => $pd["product_id"],
			'seller_id' => $pd["seller_id"],
			'volume_id' => $pd["volume_id"],
			'price' => $pd["price"],
			'qty' => $pd["qty"],
			'net_total' => $pd["total"],
			'delivery_charge' => $pd["delivery_charges"],
			'allow_split_order' => $pd["allow_split_order"]
		    );

		    $check_points = $this->db->select("product_id, in_loyalty_club")
				->where('product_id', $pd["product_id"])
				->get('products')->row_array();

		    if($check_points["in_loyalty_club"] == 1){     
			$loyalty_net_amount += $pd["total"];
		    }

		    $this->db->insert('order_product', $insert_array1);
		}
	    }  

	    //repeat order
	    if(isset($post["is_repeat_order"]) && $post["is_repeat_order"] == "1") {
		$this->db->set("is_repeat_order", 1)
		    ->where('order_id', $post["order_id"])
		    ->update("orders");

		$get_days = $this->db->select("total_days")
			->where("schedule_order_list_id", $post["repeat_order_on"])
			->where("status", 1)
			->get("schedule_order_list")->row_array();

		if(!empty($get_days)) {
		    $to_be_notified_on = date("Y-m-d H:i:s", strtotime('+'.$get_days["total_days"].' days'));
		}
		else {
		    $to_be_notified_on = null;
		}

		$insert_array2 = array(
		    "order_id" => $post["order_id"],
		    "user_id" => $post["user_id"],
		    "schedule_order_list_id" => $post["repeat_order_on"],
		    "to_be_notified_on" => $to_be_notified_on,
		);
		$this->db->insert("repeat_orders", $insert_array2);
	    }

	    //update time slot & delivered date
	    if(!empty($post["time_slot"]) && !empty($post["to_be_delivered_date"])) {
		$slots = explode("-", $post["time_slot"]);

		//get sellers
		$sll = array_unique(array_column($product_details, 'seller_id'));

		//get trading hours
		$trading_hrs = $this->db->select("*")
			->where("seller_id", $sll[0])
			->where("start_time", trim($slots[0]))
			->where("end_time", trim($slots[1]))
			->get("trading_hours")->row_array();

		$this->db->set("to_be_delivered_date", $post["to_be_delivered_date"])
			->set("to_be_delivered_date_utc", date("Y-m-d", strtotime($post["to_be_delivered_date"])))
			->set("start_slot", trim($slots[0]))
			->set("end_slot", trim($slots[1]))
			->where('order_id', $post["order_id"])
			->update("orders");

//		$this->db->set("to_be_delivered_date", $post["to_be_delivered_date"])
//			->set("to_be_delivered_date_utc", date("Y-m-d", strtotime($post["to_be_delivered_date"])))
//			->set("start_slot", $trading_hrs["start_time_utc"])
//			->set("end_slot", $trading_hrs["end_time_utc"])
//			->where('order_id', $post["order_id"])
//			->update("orders");
	    }

	    //Payment by Wallet
	    if((isset($post["wallet"]) && $post["wallet"] > 0) && (isset($post["online"]) && ($post["online"] === 'no' || $post["online"] == '')) && (isset($post["card_id"]) && $post["card_id"] == '')){
		///echo "7"; exit;
		$wallet = $this->only_wallet_payment($post, $cart, $product_details);
		//print_r($wallet);
		$this->order_mail($post);
		if($wallet["msg"] == 8) {   
		    $this->add_web_notification($post);
		    $arr[0] = 8;
		    $arr[1] = $order_no;
		    return $arr;
		}
		else if($wallet["msg"] == 9) { 
		    return 9;
		}
	    }  

	    //Payment by wallet with gift card
	    else if((isset($post["wallet"]) && $post["wallet"] > 0) && (isset($post["card_id"]) && $post["card_id"] != NULL) && (isset($post["online"]) && ($post["online"] === 'no' || $post["online"] == ''))){
		//echo "8"; exit;
		$wallet = $this->apply_wallet($post);
		//print_r($wallet);
		$post["net_amount"] = number_format($wallet["net_amount"],4);
		$post["amount_paid"] = $wallet["amount_paid"];
		//print_r($post);
		if($wallet["msg"] == 8) { 
		    $returnval = $this->use_gift_card($post, $cart, $product_details);

		    if($returnval == 8) {                        
			$val = $this->complete_wallet_payment($post);
			$this->order_mail($post);
			if($val == 8) { 
			    $arr[0] = $returnval;
			    $arr[1] = $order_no;
			    return $arr;
			}
		    } else {
			return $returnval;
		    }
		}
		else { 
		    return 9;
		}
	    }

	    //Payment by wallet with payment gateway
	    else if((isset($post["wallet"]) && $post["wallet"] > 0) && (isset($post["online"]) && $post["online"] === 'yes') && (isset($post["card_id"]) && $post["card_id"] == '')){
		//echo "9"; exit;
		$wallet = $this->apply_wallet($post);
		$post["net_amount"] = number_format($wallet["net_amount"],4);
		$post["amount_paid"] = $wallet["amount_paid"];

		if($wallet["msg"] == 8) { 
		    return $post;
		}
		else if($wallet["msg"] == 9) { 
		    return 9;
		};
	    }  

	    //Payment by wallet with payment gateway and giftcard
	    else if((isset($post["wallet"]) && $post["wallet"] > 0) && (isset($post["card_id"]) && $post["card_id"] != NULL) && isset($post["online"]) && $post["online"] === 'yes'){
		//echo "10"; exit;                    
		$wallet = $this->apply_wallet($post);
		$post["net_amount"] = number_format($wallet["net_amount"],4);
		$post["amount_paid"] = $wallet["amount_paid"];

		if($wallet["msg"] == 8) { 
		    $post["loyalty_net_amount"] = $post["net_amount"]; 

		    $returnval = $this->use_gift_card_with_gateway($post);
		    if($returnval == 8) {

			$orders = $this->db->select('net_amount, amount_paid, order_id')->where('order_id', $post["order_id"])->get('orders')->row_array();
			$post["net_amount"] = $orders["net_amount"];  
			$post["amount_paid"] = $orders["amount_paid"];
			return $post;
		    } else {
			return $returnval;
		    }
		}
		else if($wallet["msg"] == 9) { 
		    return 9;
		};
	    }

	    //Payment by Loyalty point with gift card
	    else if((isset($post["points"]) && $post["points"] > 0) && (isset($post["card_id"]) && $post["card_id"] != NULL) && (isset($post["online"]) && ($post["online"] === 'no' || $post["online"] == ''))){
		//echo "1"; exit;
		$net_amount = $post["net_amount"] - $loyalty_net_amount;

		$loyalty_arr = $this->calculate_loyalty_point($post["user_id"], $post["points"], $loyalty_net_amount);
		//print_r($loyalty_arr);
		$post["net_amount"] = $net_amount + $loyalty_arr["net_amount"];

		$returnval = $this->use_gift_card($post, $cart, $product_details);

		if($returnval == 8) {

		    $loyalty_point = $this->db->select('user_id, loyalty_point')
			->where('user_id', $post["user_id"])
			->get('user')->row_array();

		    //print_r($loyalty_point); 

		    //left  point
		    $points = $loyalty_point["loyalty_point"] - $loyalty_arr["used_points"];
		    //echo $points.'<br>';
		    $this->db
			->set('loyalty_point', $points)
			->where('user_id', $post["user_id"])
			->update('user');

		    $this->db
			->set('loyalty_point', $loyalty_arr["used_points"])
			->where('order_id', $post["order_id"])
			->update('orders');

		    //send mail
		    $this->order_mail($post);

		    //print_r($returnval); exit;
		    $arr[0] = $returnval;
		    $arr[1] = $order_no;
		    return $arr;
		} else {
		    return $returnval;
		}
	    }

	    //Payment by Loyalty point with payment gateway
	    else if((isset($post["points"]) && $post["points"] > 0) && (isset($post["online"]) && $post["online"] === 'yes') && (isset($post["card_id"]) && $post["card_id"] == '')){
		//echo "2"; exit;

		$net_amount = $post["net_amount"] - $loyalty_net_amount;                    
		$loyalty_arr = $this->calculate_loyalty_point($post["user_id"], $post["points"], $loyalty_net_amount);
		//print_r($loyalty_arr);
		$post["net_amount"] = $net_amount + $loyalty_arr["net_amount"];
		$post["net_amount"] = number_format($post["net_amount"],4);
		$this->db
		    ->set('loyalty_point', $loyalty_arr["used_points"])
		    ->set('net_amount', $post["net_amount"])
		    ->where('order_id', $post["order_id"])
		    ->update('orders');

		return $post;
	    }                

	    //Payment by Loyalty point with payment gateway and giftcard
	    else if((isset($post["points"]) && $post["points"] > 0) && (isset($post["card_id"]) && $post["card_id"] != NULL) && isset($post["online"]) && $post["online"] === 'yes'){
		//echo "3"; exit;                    
		$net_amount = $post["net_amount"] - $loyalty_net_amount;                    
		$loyalty_arr = $this->calculate_loyalty_point($post["user_id"], $post["points"], $loyalty_net_amount);
		$post["net_amount"] = $net_amount + $loyalty_arr["net_amount"];
		$post["net_amount"] = number_format($post["net_amount"],4);
		$post["loyalty_net_amount"] = $post["net_amount"]; 

		$returnval = $this->use_gift_card_with_gateway($post);
		if($returnval == 8) {
		    return $post;
		} else {
		    return $returnval;
		}
	    }

	    //Payment by both
	    else if((isset($post["card_id"]) && $post["card_id"] != NULL) && isset($post["online"]) && $post["online"] === 'yes'){
		//echo "4"; exit;                    
		$post["loyalty_net_amount"] = $post["net_amount"];                      
		$returnval = $this->use_gift_card_with_gateway($post);
		if($returnval == 8) {
		    return $post;
		} else {
		    return $returnval;
		}
	    }

	    //Payment by gift card
	    else if((isset($post["card_id"]) && $post["card_id"] != NULL)){                    
		//echo "5"; exit;
		$returnval = $this->use_gift_card($post, $cart, $product_details);
		//send mail
		$this->order_mail($post);
		return $returnval;
	    }                 

	    //Payment by payment gateway
	    else if(isset($post["online"]) && $post["online"] === 'yes'){  
		//echo "6"; exit;
		return $post;                
	    }
	    else {
		return 10;
	    }

	}
	else{
	    return 5;
	}
	    
    }
    
    public function only_wallet_payment($post=[], $cart=[], $product_details=[]) {
        //print_r($post); exit;
        ////get userdata
        $userdata = $this->get_user_by_id($post["user_id"]);
        $transaction_id = date('YmdHis').$post["order_id"].$post["user_id"];
        $payment_status = 'SUCCESS';
        //print_r($userdata); exit;
        if ($userdata["wallet"] == 0){
            $wallet_arr["msg"] = 9;
            return $wallet_arr;
        }
                
        if($post["net_amount"] <= $userdata["wallet"]){
            $balance = $userdata["wallet"] - $post["net_amount"]; 
            
            $payment_history = '{"status":"true", "payment":"success", "amount":"'.$post["net_amount"].'"}';
            
            $wallet_arr = array(
                'user_id' => $post["user_id"],
                'order_id' => $post["order_id"],
                'type' => 1,
                'debit_credit_amount' => $post["net_amount"],
                'balance_amount' => $balance,
                'payment_status' => $payment_status,
                'payment_history' => $payment_history,
                'transaction_id' => $transaction_id,
            );            
            $this->db->insert('wallet_history', $wallet_arr);
            //update in user
            $this->db->set('wallet', $balance)->where('user_id', $post["user_id"])->update('user');
            
            foreach($product_details as $pd) {
                $exist_pd = $this->db->select('*')
                        ->where('order_id', $post["order_id"])
                        ->where('product_id', $pd["product_id"])
                        ->where('volume_id', $pd["volume_id"])
                        ->get('order_product')->row_array();

                if(!empty($exist_pd)){ 

                    //get product details
                    $prd_qty = $this->db->select('units')
                            ->where('product_id', $pd["product_id"])
                            ->where('volume_id', $pd["volume_id"])
                            ->get('product_details')->row_array();

                    $new_qty = $prd_qty["units"] - $pd["qty"];  

                    //update quantity in product details               
                    $this->db->set('units', $new_qty)
                            ->where('product_id', $pd["product_id"])
                            ->where('volume_id', $pd["volume_id"])
                            ->update('product_details');
                }                
            }   

            //delete from cart
            $this->db->where('cart_id', $cart["cart_id"])->delete('cart');
            //delete from cart product
            $this->db->where('cart_id', $cart["cart_id"])->delete('cart_product');  

            //add loyalty points
            $this->add_loyalty_point($post["user_id"], $post["net_amount"]);
            
            if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){

                //Update Promocode History
                $ins_arr = array(
                    'user_id' => $post["user_id"],
                    'promocode_id' => $post["promocode_id"],
                );

                $this->db->insert('promocode_history', $ins_arr);

                $this->db->set('promocode_id', $post["promocode_id"])
                    ->where('order_id', $post["order_id"])
                    ->update('orders');
            }
                        
            //update in order
            $set = array(
                'updated_date' => date('Y-m-d H:i:s'),
                'order_payment_type' => 4,
                'payment_done' => 1,
                'wallet_amount' => $post["net_amount"],
                'net_amount' => $post["net_amount"],
            );
            
            $this->db->set($set)->where('order_id', $post["order_id"])->update('orders');
            
            //update order transaction
            $update_array_tran = array(
                'transaction_id' => $transaction_id,
                'payment_status' => $payment_status,
                'payment_history' => $payment_history,
                'payment_mode' => 4,
                'order_id' => $post["order_id"],
            );                   

            $this->db
                ->insert('order_transaction', $update_array_tran);            
            
            $wallet_arr["msg"] = 8;
            return $wallet_arr;
            
        } 
    }

    public function apply_wallet($post=[]) {
        //print_r($post); exit;
        //get userdata
        $userdata = $this->get_user_by_id($post["user_id"]);
        $transaction_id = date('YmdHis').$post["order_id"].$post["user_id"];
        $payment_status = 'SUCCESS';
        
        //print_r($userdata);
        
        if($post["net_amount"] > $userdata["wallet"] && $userdata["wallet"] > 0) {
            $amount_used = $userdata["wallet"];
            $amount_left_to_pay = $post["net_amount"] - $userdata["wallet"];    
            $balance = $userdata["wallet"] - $amount_used;
            
            $payment_history = '{"status":"true", "payment":"success", "amount":"'.$amount_used.'"}';
            
            $wallet_arr = array(
                'user_id' => $post["user_id"],
                'order_id' => $post["order_id"],
                'type' => 1,
                'debit_credit_amount' => $amount_used,
                'balance_amount' => $balance,
                'payment_status' => $payment_status,
                'payment_history' => $payment_history,
                'transaction_id' => $transaction_id,
            );            
            $this->db->insert('wallet_history', $wallet_arr);
            //update in user
            //$this->db->set('wallet', $balance)->where('user_id', $post["user_id"])->update('user');
            //update in order
            $set = array(
                'updated_date' => date('Y-m-d H:i:s'),
                'order_payment_type' => 4,
                'wallet_amount' => $amount_used,
                'net_amount' => $amount_left_to_pay,
                'amount_paid' => $amount_used,
            );
            
            $this->db->set($set)->where('user_id', $post["user_id"])->where('order_id', $post["order_id"])->update('orders');
            
            //update order transaction
            $update_array_tran = array(
                'transaction_id' => $transaction_id,
                'payment_status' => $payment_status,
                'payment_history' => $payment_history,
                'payment_mode' => 4,
                'order_id' => $post["order_id"],
            );                   

            $this->db
                ->insert('order_transaction', $update_array_tran);
            
            $wallet_arr["net_amount"] = $amount_left_to_pay;
            $wallet_arr["amount_paid"] = $amount_used;
            $wallet_arr["msg"] = 8;
            
            return $wallet_arr;
        }
        else {
            return false;
        }
        
        
    }
    
    public function complete_wallet_payment($post=[]) {
       // print_r($post); 
        //get data from wallet history
        $wallet_history = $this->db->select("*")->where('user_id', $post["user_id"])->where('order_id', $post["order_id"])->get('wallet_history')->row_array();
        //print_r($wallet_history); 
        //update in user
        $this->db->set('wallet', $wallet_history["balance_amount"])->where('user_id', $post["user_id"])->update('user');
        
        //update in order
        $set = array(
            'updated_date' => date('Y-m-d H:i:s'),
            'order_payment_type' => 4,
            'wallet_amount' => $wallet_history["debit_credit_amount"],
        );
        $this->db->set($set)->where('user_id', $post["user_id"])->where('order_id', $post["order_id"])->update('orders');
        $this->add_web_notification($post);
        //$this->notify_driver($post["user_id"], $post["order_id"]);
        return 8;
    }   
       
    public function apply_gift_card_with_payment($post=[]) {
        
        //get userdata
        $userdata = $this->get_user_by_id($post["user_id"]);

        //check gift card
        $where = 'status = 1 and expiry_date > now() and card_id = "'.$post['card_id'].'" and receiver_email="'.$userdata["email"].'"';
        $gift_card_details = $this->db->select('*')
                ->where($where)
                ->get('gift_card')->row_array();
        
        if(!empty($gift_card_details)){
            
            $where = 'card_id = '.$post['card_id'].' AND balance_amount = 0';
            //check wheather amount left in your gift card or not
            $gift_card_used1 = $this->db->select('*')
                ->where($where)
                ->order_by('date', 'desc')
                ->limit(1)
                ->get('gift_card_history')->row_array();
            
            //print_r($gift_card_used); exit;
        
           
            if(empty($gift_card_used1)){
                
                $check1 = $this->db->select("*")->where('card_id', $post["card_id"])->where('user_id', $post["user_id"])->where('order_id', $post["order_id"])->get('gift_card_history')->row_array();
                
                if(empty($check1)){   
                
                    $where1 = 'card_id = '.$post['card_id'].'';
                    //check wheather amount left in your gift card or not
                    $gift_card_used = $this->db->select('*')
                        ->where($where1)
                        ->order_by('date', 'desc')
                        ->limit(1)
                        ->get('gift_card_history')->row_array();

                    if($post["net_amount"] <= $gift_card_used["balance_amount"]){
                        $amount_used = $post['net_amount'];
                        $balance_amount = $gift_card_used["balance_amount"] - $post['net_amount']; 
                    }
                    else {
                        $amount_used = $gift_card_used["balance_amount"];
                        $left_amount = $post['net_amount'] - $gift_card_used["balance_amount"];                    
                        $post['net_amount'] = $left_amount;                    
                        $balance_amount = $gift_card_used["balance_amount"] - $amount_used;
                    }

                    $transaction_id = date('YmdHis').$post["card_id"].$post["user_id"];
                    $payment_history = '{"status":"true", "payment":"success", "amount":"'.$amount_used.'"}';
                    $payment_status = 'SUCCESS';

                    //pay from gift card
                    //deduct from gift card
                    $insert_array = array(
                        'card_id' => $post['card_id'],
                        'user_id' => $post['user_id'],
                        'transaction_id' => $transaction_id,
                        'order_id' => $post['order_id'],
                        'payment_history' => $payment_history,
                        'payment_status' => $payment_status,
                        'used_amount' => 0,
                        'balance_amount' => $gift_card_used["balance_amount"],
                        'temp_used_amount' => $amount_used,
                        'temp_balance_amount' => $balance_amount
                    );             
                             
                    $insert = $this->db->insert('gift_card_history', $insert_array);
                }
                else {
                    
                    $insert_array = array(
                        'card_id' => $post['card_id'],
                        'user_id' => $post['user_id'],
                        'transaction_id' => $check1['transaction_id'],
                        'order_id' => $post['order_id'],
                        'payment_history' => $check1['payment_history'],
                        'payment_status' => $check1['payment_status'],
                        'used_amount' => $check1['used_amount'],
                        'balance_amount' => $check1["balance_amount"],
                        'temp_used_amount' => $check1['temp_used_amount'],
                        'temp_balance_amount' => $check1['temp_balance_amount']
                    );                     
                }
                
                $insert_array["net_amount"] = $post['net_amount'];
                
                return $insert_array;
            }
            else {
                return 3;
            }
        }
        else {
            return 4;
        }
    }
   
    public function use_gift_card_with_gateway($post = []){
        $gift_card = $this->apply_gift_card_with_payment($post);
                    
        if(!$gift_card){                        
            //remove promocode if not used
            if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){
                $this->db
                    ->where('user_id', $post["user_id"])
                    ->where('promocode_id', $post["promocode_id"])
                    ->delete('promocode_history');  
            }

            return 2; 
        }
        elseif (!empty($gift_card) && $gift_card == 3) {                        
            //remove promocode if not used
            if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){
                $this->db
                    ->where('user_id', $post["user_id"])
                    ->where('promocode_id', $post["promocode_id"])
                    ->delete('promocode_history');  
            }

            return 3; 
        }
        elseif (!empty($gift_card) && $gift_card == 4) {                        
            //remove promocode if not used
            if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){
                $this->db
                    ->where('user_id', $post["user_id"])
                    ->where('promocode_id', $post["promocode_id"])
                    ->delete('promocode_history');  
            }

            return 4; 
        }
        else{
           // print_r($gift_card);
            if($gift_card["payment_status"] === 'SUCCESS'){ 
                
                $post["net_amount"] = $gift_card["net_amount"];

                //unset($post["promocode"]);
                if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){
                    //Update Promocode History
                    $ins_arr = array(
                        'user_id' => $post["user_id"],
                        'promocode_id' => $post["promocode_id"],
                    );

                    $this->db->insert('promocode_history', $ins_arr);

                    $this->db->set('promocode_id', $post["promocode_id"])
                        ->where('order_id', $post["order_id"])
                        ->update('orders');
                }
            }   
            
            if(isset($post["wallet"]) && $post["wallet"] > 0){
                $amount_paid = $post["amount_paid"] + $gift_card["temp_used_amount"];
            }
            else {
                $amount_paid = $gift_card["temp_used_amount"];
            }
            //echo $amount_paid;
            //update order
            $update_array = array(
                'gift_card_id' => $post["card_id"],
                'order_payment_type' => 3,
                'updated_date' => date('Y-m-d H:i:s'),
                'amount_paid' => $amount_paid,
                'net_amount' => $post["net_amount"],
            );

            $this->db->set($update_array)
                    ->where('order_id', $post["order_id"])
                    ->update('orders');

            //update order transaction
            $update_array_tran = array(
                'transaction_id' => $gift_card["transaction_id"],
                'payment_status' => $gift_card["payment_status"],
                'payment_history' => $gift_card["payment_history"],
                'payment_mode' => 3,
                'order_id' => $post["order_id"],
            );                   

            $this->db
                ->insert('order_transaction', $update_array_tran);
            $this->add_web_notification($post);
            //$this->notify_driver($post["user_id"], $post["order_id"]);
            return 8;
        }
    }
    
    public function use_gift_card($post = [], $cart = [], $product_details = []){
        $gift_card = $this->apply_gift_card($post);

        if(!$gift_card){

            //remove promocode if not used
            if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){
                $this->db
                    ->where('user_id', $post["user_id"])
                    ->where('promocode_id', $post["promocode_id"])
                    ->delete('promocode_history');  
            }

            return 2; 
        }
        elseif (!empty($gift_card) && $gift_card == 3) {

            //remove promocode if not used
            if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){
                $this->db
                    ->where('user_id', $post["user_id"])
                    ->where('promocode_id', $post["promocode_id"])
                    ->delete('promocode_history');  
            }

            return 3; 
        }
        elseif (!empty($gift_card) && $gift_card == 4) {

            //remove promocode if not used
            if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){
                $this->db
                    ->where('user_id', $post["user_id"])
                    ->where('promocode_id', $post["promocode_id"])
                    ->delete('promocode_history');  
            }

            return 4; 
        }
        else{

            if($gift_card["payment_status"] === 'SUCCESS'){                              

                if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){

                    //Update Promocode History
                    $ins_arr = array(
                        'user_id' => $post["user_id"],
                        'promocode_id' => $post["promocode_id"],
                    );

                    $this->db->insert('promocode_history', $ins_arr);

                    $this->db->set('promocode_id', $post["promocode_id"])
                        ->where('order_id', $post["order_id"])
                        ->update('orders');
                }

                foreach($product_details as $pd) {
                    $exist_pd = $this->db->select('*')
                            ->where('order_id', $post["order_id"])
                            ->where('product_id', $pd["product_id"])
                            ->where('volume_id', $pd["volume_id"])
                            ->get('order_product')->row_array();

                    if(!empty($exist_pd)){ 

                        //get product details
                        $prd_qty = $this->db->select('units')
                                ->where('product_id', $pd["product_id"])
                                ->where('volume_id', $pd["volume_id"])
                                ->get('product_details')->row_array();

                        $new_qty = $prd_qty["units"] - $pd["qty"];  

                        //update quantity in product details               
                        $this->db->set('units', $new_qty)
                                ->where('product_id', $pd["product_id"])
                                ->where('volume_id', $pd["volume_id"])
                                ->update('product_details');
                    }                
                }   
                
                //delete from cart
                $this->db->where('cart_id', $cart["cart_id"])->delete('cart');
                //delete from cart product
                $this->db->where('cart_id', $cart["cart_id"])->delete('cart_product');  
                
                
                //add loyalty points
                $this->add_loyalty_point($post["user_id"], $post["net_amount"]);
            }   
            
           // print_r($post);
            if(isset($post["wallet"]) && $post["wallet"] > 0) {
                $net_amount = $post["net_amount"] + $post["amount_paid"];
            }
            else {
                $net_amount = $post["net_amount"];
            }

            //update order
            $update_array = array(
                'gift_card_id' => $post["card_id"],
                'net_amount' => $net_amount,
                'order_payment_type' => 3,
                'updated_date' => date('Y-m-d H:i:s'),
                'payment_done' => 1,
            );

            $this->db->set($update_array)
                    ->where('order_id', $post["order_id"])
                    ->update('orders');

            //update order transaction
            $update_array_tran = array(
                'transaction_id' => $gift_card["transaction_id"],
                'payment_status' => $gift_card["payment_status"],
                'payment_history' => $gift_card["payment_history"],
                'payment_mode' => 3,
                'order_id' => $post["order_id"],
            );                   

            $this->db
                ->insert('order_transaction', $update_array_tran);
            $this->add_web_notification($post);
            //$this->notify_driver($post["user_id"], $post["order_id"]);
	    $get_order = $this->db->select("user_id, order_no")
                ->where('order_id', $post["order_id"])
                ->get('orders')->row_array();
	    
            $arr[0] = 8;
	    $arr[1] = $get_order["order_no"];
	    return $arr;
        }
    }
    
    public function save_transation($post=[]) {        
        //print_r($post); exit;
        $get_order = $this->db->select("*")
                ->where('order_id', $post["order_id"])
                ->get('orders')->row_array();
        
        $post["user_id"] = $get_order["user_id"];
        
        $config_data = $this->db->where_in('key', array('drinxin_commission', 'seller_commission', 'payment_mode', 'test_public_key', 'client_key', 'test_secret_key', 'service_key'))->get('setting')->result_array();
	
	$drinxin_commission = $config_data[1]['value'];
	$seller_commission = $config_data[0]['value'];

	if($config_data[4]["value"] == '1') {
	    $secret_key = $config_data[6]["value"];
	}
	else if($config_data[4]["value"] == '2') {
	    $secret_key = $config_data[2]["value"];
	}
	
	try{   
	    \Stripe\Stripe::setApiKey($secret_key); //secret key
	    //retireve checkout session details
	    $response1 = \Stripe\Checkout\Session::retrieve($post["session_id"]);      

	    //get payment intent details
	    $response2 = \Stripe\PaymentIntent::retrieve($response1->payment_intent);   

	    //get charge
	    $response = \Stripe\Charge::retrieve($response2->charges->data[0]->id);   
	    //echo "<pre>"; print_r($response); exit;

	} 
	catch (Exception $e) {
	    $response = $e->getError();            
	}
        
//        $token = $post['stripeToken'];
// 
//        try{   
//            \Stripe\Stripe::setApiKey($config_data["value"]); //secret key
//
//            $response = \Stripe\Charge::create(array(
//                        "amount" => $post['amount'],
//                        "currency" => CURRENCY,
//                        "description" => "order product: ".$get_order["order_no"],
//                        "capture" => TRUE,
//                        "source" => $token
//            ));                     
//        } 
//        catch (Exception $e) {
//	    $response = $e->getError();            
//        }
        
        if(isset($response["status"]) && $response["status"] === 'succeeded'){  
            
            if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){
                                
                //Update Promocode History
                $ins_arr = array(
                    'user_id' => $get_order["user_id"],
                    'promocode_id' => $post["promocode_id"],
                );

                $this->db->insert('promocode_history', $ins_arr);

                $this->db->set('promocode_id', $post["promocode_id"])
                    ->where('order_id', $post["order_id"])
                    ->update('orders');
            }

            $product_details = $this->db->select("*")
                    ->where('order_id', $post["order_id"])
                    ->get('order_product')->result_array();

            foreach($product_details as $pd) {
                $exist_pd = $this->db->select('*')
                        ->where('order_id', $post["order_id"])
                        ->where('product_id', $pd["product_id"])
                        ->where('volume_id', $pd["volume_id"])
                        ->get('order_product')->row_array();

                if(!empty($exist_pd)){ 

                    //get product details
                    $prd_qty = $this->db->select('units')
                            ->where('product_id', $pd["product_id"])
                            ->where('volume_id', $pd["volume_id"])
                            ->get('product_details')->row_array();

                    $new_qty = $prd_qty["units"] - $pd["qty"];  

                    //update quantity in product details               
                    $this->db->set('units', $new_qty)
                            ->where('product_id', $pd["product_id"])
                            ->where('volume_id', $pd["volume_id"])
                            ->update('product_details');
                }                
            }    
            
            if($get_order["gift_card_id"] != 0) {
                $net_amount = $get_order["net_amount"] + $get_order["amount_paid"];
            }
            else if($get_order["wallet_amount"] > 0) {
                $net_amount = $get_order["net_amount"] + $get_order["amount_paid"];
            }
            else {
                $net_amount = $get_order["net_amount"];
            }
            
            //add loyalty points
            $this->add_loyalty_point($get_order["user_id"], $net_amount); 

            //update order
            $update_array = array(
                'order_payment_type' => 1,
                'net_amount' => $net_amount,
                'updated_date' => date('Y-m-d H:i:s'),
                'payment_done' => 1,
            ); 

            $this->db->set($update_array)
                    ->where('order_id', $get_order["order_id"])
                    ->update('orders');
            
            //get cart details
            $cart = $this->get_cart($get_order["user_id"]);
            
            //delete from cart
            $this->db->where('cart_id', $cart["cart_id"])->delete('cart');
            //delete from cart product
            $this->db->where('cart_id', $cart["cart_id"])->delete('cart_product'); 
            
            //Loyalty point
            if(isset($get_order["points"]) && $get_order["points"] > 0) {
                $loyalty_point = $this->db->select('user_id, loyalty_point')
                    ->where('user_id', $get_order["user_id"])
                    ->get('user')->row_array();

                //left  point
                $points = $loyalty_point["loyalty_point"] - $get_order["loyalty_point"];
                
                $this->db
                    ->set('loyalty_point', $points)
                    ->where('user_id', $get_order["user_id"])
                    ->update('user');
            }

            //update order transaction
            $update_array_tran = array(
                'transaction_id' => $response["id"],
                'payment_status' => $response["status"],
                'payment_history' => json_encode($response),
                'payment_mode' => 1,
                'order_id' => $get_order["order_id"],
            );    
            
            $this->db->insert('order_transaction', $update_array_tran);  
	    
	    //transfer the amounts to seller
	    $get_sellers = $this->db->select("order_id, seller_id")
                ->where('order_id', $post["order_id"])
                ->get('order_product')->result_array();
	    
	    $seller_array = array_unique(array_column($get_sellers, "seller_id"));

	    foreach($seller_array as $sv) {
		//get stripe account for driver
		$get_seller_account = $this->db->select("*")
		    ->where('user_id', $sv)
		    ->where("status", 1)
		    ->where("is_primary", 1)
		    ->where("type", 1)
		    ->get('stripe_connect_accounts')->row_array();
		
		if(!empty($get_seller_account)) {
		    $samount = number_format(($get_order["gross_amount"] * $drinxin_commission / 100), 2);
		    $seller_amt = number_format(($get_order["gross_amount"] - $samount), 2);
		    
		    try{   
			\Stripe\Stripe::setApiKey($secret_key); //secret key

			$response = \Stripe\Transfer::create(array(
			    "amount" => $seller_amt*100,
			    "currency" => CURRENCY,
			    "description" => "seller commission transfer to seller account",
			    "destination" => $get_seller_account["account_id"],
			    "metadata" => array(
				"account_number" => $get_seller_account["account_number"],
				"account_holder_name" => $get_seller_account["account_holder_name"],
				"bank_name" => $get_seller_account["bank_name"]
			    )
			));  
			
			$account_obj = json_encode($response);
			$account_obj = json_decode($account_obj, true);			

			//strip transfer history
			$ins_history2 = array(
			    'user_id' => $get_seller_account["user_id"],
			    'type' => 1,
			    'amount' => $seller_amt,
			    "destination" => $get_seller_account["account_id"],
			    "source_transaction" => $account_obj["id"],
			    'payment_status' => 'SUCCESS',
			    'payment_history' => json_encode($response),
			    'transaction_id' => $account_obj["id"]
			);

			$this->db->insert("stripe_transfer_transaction", $ins_history2);
		    } 
		    catch (Exception $e) {
			$response = $e->getError(); 
			$account_obj = json_encode($response);
			$account_obj = json_decode($account_obj, true);
		    }
		}		
	    }
            
            //get data from wallet history
            $check = $this->db->select("*")->where('user_id', $get_order["user_id"])->where('order_id', $post["order_id"])->get('wallet_history')->row_array();
            if(!empty($check)) {
                //update wallet details
                $this->complete_wallet_payment($get_order);
            }
            
            //get data from gift card history
            $check1 = $this->db->select("*")->where('card_id', $get_order["gift_card_id"])->where('user_id', $get_order["user_id"])->where('order_id', $post["order_id"])->get('gift_card_history')->row_array();
            
            if(!empty($check1)) {
                //update gift card details
                $update_gift_card = array(
                    'used_amount' => $check1["temp_used_amount"],
                    'balance_amount' => $check1["temp_balance_amount"],
                );
                
                $this->db->set($update_gift_card)
                        ->where('user_id', $get_order["user_id"])
                        ->where('card_id', $get_order["gift_card_id"])
                        ->where('order_id', $post["order_id"])
                        ->update('gift_card_history');
            }
            
            $this->order_mail($post);

            return true;

        } 
	else {
            //remove promocode if not used
            if(isset($post["promocode_id"]) && $post["promocode_id"] != NULL){
                $this->db
                    ->where('user_id', $get_order["user_id"])
                    ->where('promocode_id', $get_order["promocode_id"])
                    ->delete('promocode_history');  
            }
            
            //update order transaction
            $update_array_tran = array(
                'transaction_id' => $response->charge,
                'payment_history' => json_encode($response),
                'payment_status' => 'FAILED',
                'payment_mode' => 1,
                'order_id' => $get_order["order_id"],
            );                   

            $this->db->insert('order_transaction', $update_array_tran);
            
            //get data from wallet history
            $check = $this->db->select("*")->where('user_id', $get_order["user_id"])->where('order_id', $post["order_id"])->get('wallet_history')->row_array();
       
            if(!empty($check)) {
                $this->db
                    ->where('user_id', $get_order["user_id"])
                    ->where('order_id', $post["order_id"])
                    ->delete('wallet_history');  
            }
            
            //get data from history history
            $check1 = $this->db->select("*")->where('user_id', $get_order["user_id"])->where('order_id', $post["order_id"])->get('gift_card_history')->row_array();
       
            if(!empty($check1)) {
                $this->db
                    ->where('user_id', $get_order["user_id"])
                    ->where('card_id', $get_order["gift_card_id"])
                    ->where('order_id', $post["order_id"])
                    ->delete('gift_card_history');  
            }

            return false;
        }
    }
    
    public function save_offline_data($data = []) {
	$this->db->insert("offline_data", array(
	    "user_id" => $data["user_id"],
	    "method" => "new_msg",
	    "json" => $data["json"],
	));
    }
    
    public function send_a_msg($post = []) {
	$umsgid = $post["user_id"] . '-' . date('YmdHis');
	$insert_arr = array(
	    "message_id" => $umsgid,
	    "offline_id" => "",
	    "user_id" => $post["user_id"],
	    "group_id" => null,
	    "to_user_id" => $post["to_user_id"],
	    "type" => "sent",
	    "message" => $post["message"],
	    "msg_type" => "text",
	    "sent_at" => date('Y-m-d H:i:s'),
	    "seen_at" => null,
	    "delivered_at" => null,
	    "deleted_at" => null,
	    "edited_at" => null,
	    "order_id" => $post["order_id"],
	);
	
	$send_arr = array(
	   "user_id" => $post["user_id"],
	    "to_user_id" => $post["to_user_id"],
	    "type" => "sent",
	);
	
	$received_arr = array(
	   "user_id" => $post["to_user_id"],
	    "to_user_id" => $post["user_id"],
	    "type" => "received",
	);
	
	
	//insert send message
	$send_arr = array_merge($insert_arr, $send_arr);
	$this->db->insert("chat", $send_arr);
	
	//insert received message
	$received_arr = array_merge($insert_arr, $received_arr);
	$this->db->insert("chat", $received_arr);
	$last_receive_id = $this->db->insert_id();
	
	$push = array(
	    "to_user_id" => $post["to_user_id"],
	    "notification_type" => 25,
	    "message" => $post["message"],
	    "driver_id" => $post["to_user_id"],
	    "customer_id" => $post["user_id"],
	    "order_id" => $post["order_id"],
	);
	
	$this->save_offline_data(array(
	    "user_id" => $post["to_user_id"],
	    "json" => json_encode($received_arr)
	));
	
	$this->create_notification($push);
	
	//get customer name & contact no
	$get_ltlg = $this->user_info($post["to_user_id"]);
	$push["name"] = $get_ltlg["firstname"]." ".$get_ltlg["lastname"];
	$push["contact_no"] = trim($get_ltlg["mobileno"]);
	
	$this->m_notifyd->send($push);
	
	$resp = $this->db->select("*")
		->where("chat_id", $last_receive_id)
		->get("chat")->row_array();	
	
	return $resp;
    }
    
    public  function get_user_conversation_list($post = []){
        $toids = array();
        $conv = array();
        $counter = 0;
        $where = "chat.user_id = ".$post["user_id"]." and chat.deleted_at IS NULL";
        $chat_details = $this->db->select("chat.*, concat(user.firstname,' ',user.lastname) as name, user.email, user.userno, user.address, user.profile_image, user.mobileno, user.country_code", false)
                ->join('user', 'user.user_id = chat.to_user_id')
                ->where($where)
                ->order_by('chat.chat_id', 'desc')
                ->get('chat')->result_array();
        
        if(!empty($chat_details)){
            foreach($chat_details as $ckey => $cvalue1) {
                
                $cvalue = array_map(function($val) {
                    if(is_null($val)) {
                        $val = "";
                    }
                    return $val;
                }, $cvalue1);
                $chat_details[$ckey] = $cvalue;
                
                $status = "";
                if($status=="" || $status=="blocked_by_me"){
                    if($status=="blocked_by_me"){
                        $chat_details[$ckey]["blocked"] = "1";
                    }else{
                        $chat_details[$ckey]["blocked"] = "0";
                    }
                    
                    if(!empty($cvalue["profile_image"])) {
                        $chat_details[$ckey]["profile_image"] = $this->s3_url($cvalue["profile_image"]);
			$chat_details[$ckey]["profile_image_thumb"] = $this->s3_url($cvalue["profile_image"], 'thumb');
                    }
                    else {
                        $chat_details[$ckey]["profile_image"] = "";
			$chat_details[$ckey]["profile_image_thumb"] = "";
                    }
                    
                    $msg = str_replace(PHP_EOL,"@/@", $chat_details[$ckey]["message"]);
                    $chat_details[$ckey]["sender_message"] = json_decode('"'.$msg.'"');
                    $chat_details[$ckey]["sender_message"] = str_replace("@/@",PHP_EOL, $chat_details[$ckey]["sender_message"]);
                                        
                    $toid = $chat_details[$ckey]["to_user_id"];
                    if (in_array($toid, $toids) ) {
                        //exist //ignore
                    } else {
                        $type = "";
                        if($cvalue["type"] == "sent"){
                            $type = "received";
                        }
                        else if($cvalue["type"] == "received"){
                            $type = "sent";
                        }
                        
                        //get receiver msg
                        $receiver_msg = $this->db->select("message")
                                ->where("user_id", $post["user_id"])
                                ->where("to_user_id", $toid)
                                ->where("type", $type)
                                ->order_by('chat_id', 'desc')
                                ->get('chat')->result_array();
                        
                        if(!empty($receiver_msg)){
                            $msg = str_replace(PHP_EOL,"@/@", $receiver_msg[0]["message"]);
                            $chat_details[$ckey]["receiver_message"] = json_decode('"'.$msg.'"');
                            $chat_details[$ckey]["receiver_message"] = str_replace("@/@",PHP_EOL, $chat_details[$ckey]["receiver_message"]);
                        }
                        else {
                            $chat_details[$ckey]["receiver_message"] = ""; 
                        }
                        
                        $unread_msg = $this->get_unread_msg_count($post["user_id"], $cvalue["to_user_id"]);;
                        if($unread_msg == 0){
                            $chat_details[$ckey]["unread_msg"] = (string)$unread_msg;
                        }
                        else {
                            $counter++;
                            $chat_details[$ckey]["unread_msg"] = (string)$unread_msg;
                        }
                        
                        array_push($conv, $chat_details[$ckey]);                        
                    }
                    array_push($toids, $toid);
                }
            }            
        }
        
        $return_array = array(
            "list" => $conv,
            "total_unread_msg_count" => (string)$counter
        );
        //print_r($conv); exit;
        return $return_array;
    }
    
    public function get_unread_msg_count($user_id, $to_user_id){
        $where="seen_at IS NULL";
        $response = $this->db->select("*")
                ->where('user_id', $user_id)
                ->where('to_user_id', $to_user_id)
                ->where($where)->get('chat')->result_array();
        if(!empty($response)){
            return count($response);
        }
        else {
            return "0";
        }        
    }
    
    public function get_all_unread_msg_count($user_id){
        $where="seen_at IS NULL and deleted_at IS NULL";
        $response = $this->db->select("*")
                ->where('user_id', $user_id)
                ->where($where)->get('chat')->result_array();
        if(!empty($response)){
            return count($response);
        }
        else {
            return "0";
        }        
    }
    
    public function get_conversation_msg($post=[]){
        
        if(empty($post["offset"])){
           $post["offset"] = 0; 
        }
        
        $msgs = array();
        $where = "deleted_at IS NULL";
        $response = $this->db->select("*")
                ->where("user_id", $post["user_id"])
                ->where("to_user_id", $post["to_user_id"])
		->where("order_id", $post["order_id"])
                ->where($where)
                ->limit(20)
                ->offset($post["offset"])
                ->order_by('sent_at', 'desc')
                ->get('chat')->result_array();
        
        if(!empty($response)){
            foreach ($response as $key => $value1){
                
                $msg11 = str_replace(PHP_EOL,"@/@", $value1["message"]);
                $value1["message"] = json_decode('"'.$msg11.'"');
                $value1["message"] = str_replace("@/@",PHP_EOL, $value1["message"]);
                
                $value = array_map(function($val) {
                    if(is_null($val)) {
                        $val = "";
                    }
                    return $val;
                }, $value1);
                $response[$key] = $value;
                
                $msg = $this->get_message($value["chat_id"]);                
                if(!empty($msg)){
                    array_push($msgs, $msg);
                }
            } 
            return $msgs;
        }
        else {
            return 1;
        }        
    }
    
    public function get_blocked_status ($user_id='', $profile_id=''){
        $where = "(user_id=".$user_id." and blocked_user_id=".$profile_id.") OR (blocked_user_id=".$user_id." and user_id=".$profile_id.")";
        $fetch = $this->db->select("*", false)
                ->where($where)
                ->get('block_users')->result_array();

        if(!empty($fetch)){
            
            if (count($fetch) == "2") {
                return "blocked_each_other";
            } else if (count($fetch) == "0") {
                return "";
            } else {
                if ($fetch[0]["user_id"] == $user_id) {
                    return "blocked_by_me";
                } else {
                    return "blocked_by_you";
                }
            }
        }
    }
    
    public function get_message($chat_id){
        $response = $this->db->select("*")->where("chat_id", $chat_id)->get("chat")->row_array();
        if(!empty($response)){            
            $response = array_map(function($val) {
                if(is_null($val)) {
                    $val = "";
                }
                return $val;
            }, $response);
            
            return $response;
        }
    }
    
    public function get_user_details($user_id, $second_user){
        $response = $this->db->select("user_id, concat(firstname,' ',lastname) as name, userno, email, last_seen, address, profile_image, mobileno, country_code, date, is_admin_verified", false)->where("user_id", $user_id)->get("user")->row_array();
        if(!empty($response)){
            if(!empty($response["profile_image"])) {
                $img = $response["profile_image"];
		$response["profile_image"] = $this->s3_url($img);
		$response["profile_image_thumb"] = $this->s3_url($img, 'thumb');
            }
            else {
                $response["profile_image"] = "";
		$response["profile_image_thumb"] = "";
            }  
            if( is_null($response["last_seen"]) ) {
                $response["last_seen"] = "Online";            
            }
            else {
                $response["last_seen"] = $response["last_seen"];
            }
            $response = array_map(function($val) {
                if(is_null($val)) {
                    $val = "";
                }
                return $val;
            }, $response);            
            return $response;
        }    
    }
    
    public function repeat_order_response($post=[]) {
	//get repeat order details
	$repeat_details = $this->db->select("*")
		->where("order_id", $post["order_id"])
		->where("user_id", $post["user_id"])
		->where("status", 1)
		->get("repeat_orders")
		->row_array();	
	
	if(!empty($repeat_details)) {
	
	    $get_days = $this->db->select("total_days")
			    ->where("schedule_order_list_id", $repeat_details["schedule_order_list_id"])
			    ->where("status", 1)
			    ->get("schedule_order_list")->row_array();

	    if($post["type"] == '1') {
		//reschedule order 
		$to_be_notified_on = date("Y-m-d H:i:s", strtotime('+'.$get_days["total_days"].' days'));

		$this->db->set("to_be_notified_on", $to_be_notified_on)
			->set("is_notified", 0)
			->where("order_id", $post["order_id"])
			->where("user_id", $post["user_id"])
			->where("status", 1)
			->update("repeat_orders");

		$order_details = $this->db->select("*")
                    ->where('order_id', $post["order_id"])
                    ->get('order_product')->result_array(); 
		
		$out_of_stock = 0;
		
		foreach($order_details as $k => $v) {
		    $bag_arr = array(
			'user_id' => $post["user_id"],
			'from_where' => 1,
			'product_id' => $v["product_id"],
			'volume_id' => $v["volume_id"],
			'qty' => $v["qty"]
		    );
		    $reponse = $this->add_to_bag($bag_arr);
		    
		    if($reponse == '1') {
			$bag_arr2 = array(
			    'user_id' => $post["user_id"],
			    'product_id' => $v["product_id"],
			    'volume_id' => $v["volume_id"],
			    'qty' => $v["qty"]+1
			);
			$reponse2 = $this->update_bag($bag_arr2);
			
			if($reponse2 == '2') {
			    $out_of_stock++;
			}
		    }	
		    else if($reponse == '2') {
			$out_of_stock++;
		    }
		    
		    if($out_of_stock >= count($order_details)) {
			return 3;
		    }
		    else {
			return 4;
		    }
		}
		
		return $order_details;
	    }
	    else if($post["type"] == '0') {
		//reschedule order 
		$to_be_notified_on = date("Y-m-d H:i:s", strtotime('+'.$get_days["total_days"].' days'));

		$this->db->set("to_be_notified_on", $to_be_notified_on)
			->set("is_notified", 0)
			->where("order_id", $post["order_id"])
			->where("user_id", $post["user_id"])
			->where("status", 1)
			->update("repeat_orders");

		return 1;
	    }
	}
	else {
	    return 2;
	}
    }
    
    function user_info($user_id) {
	$get_ltlg = $this->db->select("user.user_id, user.firstname, user.lastname, user.mobileno, user.country_code")
		->where("user.user_id", $user_id)
		->where("user.status", 1)
		->get("user")->row_array();
	
	return $get_ltlg;
    }
    
    public function get_seller_slot2($seller_ids) {
	$date = date('Y-m-d'); //today date
	$final_array = array();	
	$duration = '60';  // split by 1 hours
	$add_mins  = $duration * 60;	
	$total_seller = count(explode(",",$seller_ids));
	
	//get key of timeslot according to weekdays            
	for($i = 1; $i <= 7; $i++){
	    $slot_arr = array();
	    $final_slot_array = array();
	    $weekday = date('w', strtotime("+$i day", strtotime($date)))+1;
	    	    
	    $response = $this->db->select("*")
                ->where("seller_id IN (".$seller_ids.")")
		->where("weekday", $weekday)
                ->where("status", 1)
                ->get("trading_hours")->result_array();		    
//	    print_r($response); 
	    
	    foreach($response as $k => $v) {
		$array_of_time = array ();		
		$start_time = $v["start_time"];
		$end_time = $v["end_time"];
		$starttime = strtotime ($start_time); //change to strtotime
		$endtime = strtotime ($end_time); //change to strtotime
		
		while ($starttime < $endtime){ // loop between time			
		    $stime = date ("H:i", $starttime);
		    $starttime += $add_mins; // to check endtie=me
		    $etime = date ("H:i", $starttime);
		    $array_of_time[] = $stime.' - '.$etime;
		}
		$slot_arr = array_merge($slot_arr, $array_of_time);
	    }
	    
	    $slot_arr_push = array_count_values($slot_arr);
//	    print_r($slot_arr_push);
	    foreach($slot_arr_push as $sk => $sv) {
		if($total_seller == $sv) {
		    $new_arr["slot"] = $sk;
		    $new_arr["flag"] = 1;		    
		}
		else {
		    $new_arr["slot"] = $sk;
		    $new_arr["flag"] = 0;
		}
		array_push($final_slot_array, $new_arr);
	    }
	    
	    array_multisort(array_column($final_slot_array, "slot"), SORT_ASC, $final_slot_array);
	    
//	    print_r($final_slot_array);
	    
	    $final_array["day"] = date('l', strtotime("+$i day", strtotime($date)));
	    $final_array["date"] = date('Y-m-d', strtotime("+$i day", strtotime($date)));
	    $final_array["slots"] = $final_slot_array;
	    $slota[] = $final_array;
	}
	
        return $slota;
    }
}

    

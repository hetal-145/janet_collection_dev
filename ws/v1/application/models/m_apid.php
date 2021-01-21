<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require '../../vendor_aws/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class M_apid extends CI_Model {

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
                        ->where("user_type", 2)
                        ->get('user')->row_array();
        return $user;
    }
    
    public function get_user_by_id($user_id) {
        $user = $this->db
                        ->where('user_id', $user_id)
                        ->where("user_type", 2)
                        ->get('user')->row_array();
        return $user;
    }

    public function update_password($post = []) {
	$check = $this->db->select("user_id")
		->where('user_id', $post['user_id'])
		->where('password', sha1($post['old_password']))
                ->where("user_type", 2)
		->get("user")->row_array();
	
	if(!empty($check)) {
	    $this->db->where('user_id', $post['user_id'])
                        ->where("user_type", 2)
                        ->set('password', sha1($post['password']))
                        ->set('password_updated', 1)
                        ->update('user');
	    
	    return 1;
	}
	else {
	    return 2;
	}
    }

    public function signin($post = []) {
        
        $userdata = array();
        // for email
        $userdata_email = $this->db
            ->where('email', $post["signin_username"])
            ->where('password', sha1($post['password']))
            ->where("user_type", 2) 
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
                ->where("user_type", 2)  
		->where("status", 1)
                ->from('user')->get()->row_array(); 
        
            $userdata = $userdata_mobileno;
        }     
        
        if(!empty($userdata)) {
            if(!empty($userdata["profile_image"])) {
                $img = $userdata["profile_image"];
                $userdata["profile_image"] = $this->s3_url($img);
                $userdata["profile_image_thumb"] = $this->s3_url($img, 'thumb');
            }

            $userdata["vehicle_details"] = $this->vehicle_details($userdata["user_id"]);
        }
        //print_r($userdata);
        
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
            ->where("user_type", 2)            
            ->from('user')->get()->row_array();    
        
        if(!empty($userdata_email)){
            $userdata = $userdata_email;
        }
        else {
            // for mobileno
            $userdata_mobileno = $this->db
                ->where('mobileno', $signin_username)
                ->where("user_type", 2)
                ->from('user')->get()->row_array(); 
        
            $userdata = $userdata_mobileno;
        }
        
        return $userdata;
    }
    
    public function check_user_by_email_mobile($email = '', $mobileno=''){
        $where = "email = '".$email."' OR mobileno = ".$mobileno."";        
       
        $userdata = $this->db->select('*', false)
            ->where($where)
            ->where("user_type", 2)                
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
                
                $this->db->where('sha1(user_id)', $sha1_user_id);
                $this->db->set('status', 1);
                //$this->db->set('email_verified', 1);
                $this->db->update('driver_docs');
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
                ->where("user_type", 2)
                ->update('user', $post);
        $post['user_id'] = $user_id;
        $userdata = $this->get_user_by_id($user_id);
        return $userdata;
    }  
    
    public function generate_random_verification_code($mobileno){
        
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

    public function delete_device_token($post = []) {
        return $this->db
                        ->where('user_id', $post['user_id'])
                        ->where('device_token', $post['device_token'])
                        ->delete('device_token');
    }

    public function get_profile($post = []) {
        $profile = $this->user_profile($post['profile_id']);
        //print_r($profile); exit;
        if ($profile) {   
            $profile = array_map(function($val) {
                if(is_null($val)) {
                    $val = "";
                }
                return $val;
            }, $profile);
            return $profile;
        }
        else {
            return false;
        }
    }

    public function user_profile($user_id = '') {
        $user_data = $this->db->select('*')
                ->where('user_id', $user_id)
                ->where("status", 1)
                ->get('user')
                ->row_array();
        
        if(!empty($user_data)) {  
            
            $user_data = array_map(function($val) {
                if(is_null($val)) {
                    $val = "";
                }
                return $val;
            }, $user_data);
            
            if(!empty($user_data["profile_image"])) {
                $img = $user_data["profile_image"];
                $user_data["profile_image"] = $this->s3_url($img, '', 'driver');
                $user_data["profile_image_thumb"] = $this->s3_url($img, 'thumb', 'driver');
            }
            
            $driver_docs = $this->db->select("*")->where("delete_status", 0)->where("status", 1)->where("user_id", $user_id)->get("driver_docs")->result_array();
            $user_data["total_driver_img"] = count($driver_docs);
                
            if(!empty($driver_docs)) {
                $user_data["driver_verify_doc_1"] = $this->s3_url($driver_docs[0]["image_name"], '', 'driver');
                    
                foreach($driver_docs as $key => $value) {
                    $driver_docs[$key] = $this->s3_url($value["image_name"], '', 'driver');
                }                
            }
            else {
                $user_data["driver_verify_doc_1"] = "";
            }
            $user_data["driver_docs"] = $driver_docs;
            
            unset($user_data["password"]);
            unset($user_data["password_updated"]);

            //vehicles details
            $user_data["vehicle_details"] = $this->vehicle_details($user_id);
        }

        return $user_data;
    }

    public function vehicle_details($driver_id = '') {
        $vehicle_mst = $this->db->select('*')
                ->where('driver_id', $driver_id)
                ->where("status", 1)
                ->get('vehicle_mst')
                ->row_array(); 
        
        if(!empty($vehicle_mst)) {        
            $vehicle_mst = array_map(function($val) {
                if(is_null($val)) {
                    $val = "";
                }
                return $val;
            }, $vehicle_mst);
            
            $vehicle_images = $this->db->select("*")->where("status", 1)->where("vehicle_id", $vehicle_mst["vehicle_id"])->get("vehicle_images")->result_array();
            $vehicle_mst["total_img"] = count($vehicle_images);
                
            if(!empty($vehicle_images)) {
                $vehicle_mst["vehicle_img_1"] = $this->s3_url($vehicle_images[0]["image_name"], '', 'driver');
                    
                foreach($vehicle_images as $key => $value) {
                    $vehicle_images[$key] = $this->s3_url($value["image_name"], '', 'driver');
                }
            }
            else {
                $vehicle_mst["vehicle_img_1"] = "";
            }
            $vehicle_mst["vehicle_images"] = $vehicle_images;
            
            
            if(!empty($vehicle_mst["vehicle_ins_policy"])) {
                $vehicle_mst["vehicle_ins_policy"] = $this->s3_url($vehicle_mst["vehicle_ins_policy"], '', 'driver');
            }
            return $vehicle_mst;
        }  
        else {
            return new stdClass();
        }
    }
    
    public function update_docs($post = [], $files = []) {
        //print_r($post); print_r($files); exit;
        $user_id = $post["user_id"];
        unset($post["user_id"]);   
	$ret_arr = array();
	
	///bucket info
	$credentials = new Aws\Credentials\Credentials(PUBLISHED_KEY, SECRET_KEY);
	$s3 = new S3Client([        
	    'region' => 'eu-west-2',
	    'version' => '2006-03-01',
	    //'debug' => true,
	    "credentials" => $credentials
	]);
        
        //update vehicle images        
        if(!empty($files["vehicle_img"]['name'])) {
	    $ext_arr = array('gif','jpg','png', 'jpeg');
	    
            $vehicle_id = $this->db->select("vehicle_id")->where("driver_id", $user_id)->where("status", 1)->get("vehicle_mst")->row_array();
	    
	    $c1 = 0;	    
	    foreach ($files['vehicle_img']['name'] as $key => $file) {
		$f = $files['vehicle_img']['name'][$key];
		$ext = '.' . pathinfo($f, PATHINFO_EXTENSION);
		$ext1 = pathinfo($f, PATHINFO_EXTENSION);
		if( in_array($ext1, $ext_arr) ) {
		    $filename = date('YmdHis') . rand() . $user_id . strtolower($ext);
		    $keyname = $filename;
		    $filepath = $files['vehicle_img']['tmp_name'][$key];
		    
		    $result = $s3->putObject(array(
			'Bucket' => BUCKET_NAME,
			'Key' => 'driver/'.$keyname,
			'SourceFile' => $filepath,
			'ACL' => 'public-read',
			'StorageClass' => 'STANDARD'
		    ));

		    $veh_arr = array(
			"vehicle_id" => $vehicle_id["vehicle_id"],
			"image_name" => $filename,
			"status" => 1
		    );                        
		    $this->db->insert("vehicle_images", $veh_arr);
		}
		else {
		    $c1++;
		}
	   }
	   
	   if($c1 > 0 && $c1 <= count($files['vehicle_img']['name'])) {
	       $ret_arr[0] = 1;
	       $ret_arr[1] = 'The filtype you are trying to upload is not allowed';
	       return $ret_arr;
	   }
	   
           unset($files["vehicle_img"]);
        }
        
        //update verification documents
        if(!empty($files["driver_verify_doc"]['name'])) {
	    $ext_arr1 = array('gif','jpg','png', 'jpeg', 'pdf');
	    
	    $c2 = 0;	    
	    foreach ($files['driver_verify_doc']['name'] as $key => $file) {
		$f = $files['driver_verify_doc']['name'][$key];
		$ext = '.' . pathinfo($f, PATHINFO_EXTENSION);
		$ext1 = pathinfo($f, PATHINFO_EXTENSION);
		if( in_array($ext1, $ext_arr1) ) {
		    $filename = date('YmdHis') . rand() . $user_id . strtolower($ext);
		    $keyname = $filename;
		    $filepath = $files['driver_verify_doc']['tmp_name'][$key];
		    
		    $result = $s3->putObject(array(
			'Bucket' => BUCKET_NAME,
			'Key' => 'driver/'.$keyname,
			'SourceFile' => $filepath,
			'ACL' => 'public-read',
			'StorageClass' => 'STANDARD'
		    ));

		    $dri_arr = array(
                        'user_id' => $user_id,
                        "image_name" => $filename,
                        "status" => 1
                    );                        
                    $this->db->insert("driver_docs", $dri_arr); 
		}
		else {
		    $c2++;
		}
	    }

	    if($c2 > 0 && $c2 <= count($files['driver_verify_doc']['name'])) {
		$ret_arr[0] = 1;
		$ret_arr[1] = 'The filtype you are trying to upload is not allowed';
		return $ret_arr;
	    }
  
            unset($files["driver_verify_doc"]);
        }
        
        //update insurance documents
        if(!empty($files["vehicle_ins_policy"]['name'])) {  	    
	    $ext_arr2 = array('pdf');
	    
	    $ext = '.' . pathinfo($files['vehicle_ins_policy']['name'], PATHINFO_EXTENSION);
	    $ext1 = pathinfo($files['vehicle_ins_policy']['name'], PATHINFO_EXTENSION);
	    $filename = date('YmdHis') . rand() . strtolower($ext);
	    $keyname = $filename;
	    $filepath = $files['vehicle_ins_policy']['tmp_name'];
	    if( in_array($ext1, $ext_arr2) ) {
		$result = $s3->putObject(array(
		    'Bucket' => BUCKET_NAME,
		    'Key' => 'driver/'.$keyname,
		    'SourceFile' => $filepath,
		    'ACL' => 'public-read',
		    'StorageClass' => 'STANDARD'
		));

		//print_r($result); 
		$post['vehicle_ins_policy'] = $filename;
	    }
	    else {
		$ret_arr[0] = 1;
		$ret_arr[1] = 'The filtype you are trying to upload is not allowed';
		return $ret_arr;
	    }
        }
        
        if(isset($post) && !empty($post)) {
            $this->db->set($post)->where('driver_id', $user_id)->update('vehicle_mst');
        }
        
        $userdata = $this->user_profile($user_id);
	
	$ret_arr[0] = 2;
	$ret_arr[1] = $userdata;
	return $ret_arr;
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
        $get_faq_questions = $this->db->select('faq_id, faq_question, faq_answer')
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
    
    public function feedback($post = []) {        
        //insert into db
        $insert_array = array(
            'user_id' => $post["user_id"],
            'name' => $post["name"],
            'mobileno' => $post["mobileno"],
            'email' => $post["email"],
            'subject' => $post["subject"],
            'message' => $post["message"],
            'type' => 3,
        );        
        $insert = $this->db->insert('help_support', $insert_array);
        
        $config_data = $this->db->where('key', 'admin_email_address')->get('setting')->row_array();        
        $to = $config_data['value'];
        $subject = $post["subject"];
        $msg = $this->load->view('mail_tmp/header', $post, true);
        $msg .= $this->load->view('mail_tmp/feedback', $post, true);
        $msg .= $this->load->view('mail_tmp/footer', $post, true);
        $this->send_mail($to, $subject, $msg);

        if($insert) {
            return true;
        }
        else {
            return false;
        }  
    }
    
    public function send_requests($post = [], $files = []) { 
	
	///bucket info
	$credentials = new Aws\Credentials\Credentials(PUBLISHED_KEY, SECRET_KEY);
	$s3 = new S3Client([        
	    'region' => 'eu-west-2',
	    'version' => '2006-03-01',
	    //'debug' => true,
	    "credentials" => $credentials
	]);
	
	if(isset($files["image"]) && !empty($files["image"])) {	    
	    $ext_arr2 = array('gif', 'jpg', 'png', 'jpeg');
	    
	    $ext = '.' . pathinfo($files['image']['name'], PATHINFO_EXTENSION);
	    $ext1 = pathinfo($files['image']['name'], PATHINFO_EXTENSION);
	    $filename = date('YmdHis') . rand() . strtolower($ext);
	    $keyname = $filename;
	    $filepath = $files['image']['tmp_name'];
	    if( in_array($ext1, $ext_arr2) ) {
		$result = $s3->putObject(array(
		    'Bucket' => BUCKET_NAME,
		    'Key' => 'driver/'.$keyname,
		    'SourceFile' => $filepath,
		    'ACL' => 'public-read',
		    'StorageClass' => 'STANDARD'
		));

		//print_r($result); 
		$post['image'] = $filename;
	    }
	    else {
		return 'The filtype you are trying to upload is not allowed';
	    }
	}
	
        //insert into db
        $insert = $this->db->insert('driver_requests', $post);
        $last_id = $this->db->insert_id();
        if($insert) {
	    $this->add_web_notification($post["user_id"], 2, "Driver request to update details", $last_id);
            return 1;
        }
        else {
            return 2;
        }  
    }
    
    public function send_vehicle_requests($post = [], $files = []) { 
	
//	if(isset($post["delete_image"]) && !empty($post["delete_image"])) {	    
//	    $this->db->set("status", 0)
//                        ->where("vehicle_image_id IN (".$post['delete_image'].")")
//                        ->update("vehicle_images");
//	    
//	    unset($post["delete_image"]);
//	}
//	
	//insert into db
        $insert = $this->db->insert('driver_vehicle_requests', $post);
	$last_id = $this->db->insert_id();
	
	///bucket info
	$credentials = new Aws\Credentials\Credentials(PUBLISHED_KEY, SECRET_KEY);
	$s3 = new S3Client([        
	    'region' => 'eu-west-2',
	    'version' => '2006-03-01',
	    //'debug' => true,
	    "credentials" => $credentials
	]);
	
	if(isset($files["images"]) && !empty($files["images"]["name"][0])) {
	    $ext_arr1 = array('gif','jpg','png', 'jpeg');
	    
	    $c2 = 0;	    
	    foreach ($files['images']['name'] as $key => $file) {
		$f = $files['images']['name'][$key];
		$ext = '.' . pathinfo($f, PATHINFO_EXTENSION);
		$ext1 = pathinfo($f, PATHINFO_EXTENSION);
		if( in_array($ext1, $ext_arr1) ) {
		    $filename = date('YmdHis') . rand() . $post["user_id"] . strtolower($ext);
		    $keyname = $filename;
		    $filepath = $files['images']['tmp_name'][$key];
		    
		    $result = $s3->putObject(array(
			'Bucket' => BUCKET_NAME,
			'Key' => 'driver/'.$keyname,
			'SourceFile' => $filepath,
			'ACL' => 'public-read',
			'StorageClass' => 'STANDARD'
		    ));

		    $arr = array(
                        'vehicle_id' => $post["vehicle_id"],
                        'image_name' => $filename,  
			'request_id' => $last_id,
                    );

                    $this->db->insert('driver_vehicle_image_request', $arr);
		}
		else {
		    $c2++;
		}
	    }

	    if($c2 > 0 && $c2 <= count($files['images']['name'])) {		
		return 'The filtype you are trying to upload is not allowed';
	    }
	}
        
        if($insert) {
	    $this->add_web_notification($post["user_id"], 5, "Driver request to update vehicle details", $last_id);
            return 1;
        }
        else {
            return 2;
        }  
    }
    
    public function update_user_location($post=[]){
        $check = $this->db->select("user_id")->where("user_id", $post["user_id"])->get("user")->row_array();
        if(!empty($check)){
	    //update location
            $update = $this->db->set("updated_date", date('Y-m-d H:i:s'))->set("latitude", $post["latitude"])->set("longitude", $post["longitude"])->where("user_id", $post["user_id"])->update("user");
	    
	    //check sessios added?
	    $check = $this->db->select("sch_id")
		    ->where("driver_id", $post["user_id"])
		    ->where("status", 1)
		    ->get("driver_schedule")->row_array();
	    
	    if(!empty($check)) {
		$ret[0] = 1;
		$ret[1] = 0;
		return $ret;
	    }
	    else {
		$ret[0] = 2;
		$ret[1] = 1;
		return $ret;
	    }
        }        
    }
    
    public function driver_online_status($post=[]) {
        $check = $this->db->select("user_id")->where("user_id", $post["user_id"])->get("user")->row_array();
        if(!empty($check)){
            $update = $this->db->set("is_online", $post["is_online"])->where("user_id", $post["user_id"])->update("user");
            if($update){
                if($post["is_online"] == 1) {
                    return 1;
                }
                else if($post["is_online"] == 0) {
                    return 2;
                }
                
            }
            else {
                return 3;
            }
        }  
        else {
            return 3;
        }
    }
    
    public function delete_image($post=[]) {
        if($post["type"] == 1) {            
            $delete = $this->db
                        ->where("image_name", basename($post["image_name"]))
                        ->where("user_id", $post["user_id"])
                        ->delete("driver_docs");
        }
        else if($post["type"] == 2) {
            $vehicle_id = $this->db->select("vehicle_id")->where("driver_id", $post["user_id"])->where("status", 1)->get("vehicle_mst")->row_array();
            
            $delete = $this->db
                        ->where("image_name", basename($post["image_name"]))
                        ->where("vehicle_id", $vehicle_id["vehicle_id"])
                        ->delete("vehicle_images");
        }
        else if($post["type"] == 3) {
            $check = $this->db->select("vehicle_id")
                        ->where("vehicle_ins_policy", basename($post["image_name"]))
                        ->where("driver_id", $post["user_id"])
                        ->get("vehicle_mst")->row_array();
            
            if(!empty($check)) {
                $delete = $this->db
                            ->set("vehicle_ins_policy", null)
                            ->where("driver_id", $post["user_id"])
                            ->update("vehicle_mst");
            }
            else {
                return false;
            }
            
        }
        
        if($delete) {
            return true;
        }
        else {
            return false;
        }        
    }
    
    public function get_notification_list($post = []) {
	$this->db->set("is_read", 1)
		->where('to_user_id', $post['user_id'])
		->where("is_read", 0)
		->update("notification");
	
        $list = $this->db
                ->where('to_user_id', $post['user_id'])
		->where("notification_type NOT IN (25)")		
                ->limit($post['limit'])
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
                
                $list[$key] = $value;
                
                if($value["notification_type"] == 10) {
                    //print_r($value);

                    //order details
                    $get_order = $this->db->select("orders.order_id, orders.order_no, orders.net_amount, orders.shipping_id")
                            ->where("orders.order_id", $value["order_id"])
                            ->get("orders")->row_array();
            
                    $post["user_id"] = $value["customer_id"];
		    
		    if(!empty($get_order)) {
			$post["shipping_id"] = $get_order["shipping_id"];
		    }
		    else {
			$post["shipping_id"] = 0;
		    }
                    
                    //user details
                    $get_ltlg = $this->db->select("user.user_id, user.latitude, user.longitude, user.firstname, user.lastname, user.mobileno")
                            ->where("user.user_id", $value["customer_id"])
                            ->where("user.status", 1)
                            ->get("user")->row_array(); 
                    
                    if(!empty($get_ltlg)) {
                        $shipping_details = $this->m_api->get_shipping_by_id_without_status($post); 

                        if(!empty($shipping_details)) {
                            $shipping_details['firstname'] = $get_ltlg["firstname"];
                            $shipping_details['lastname'] = $get_ltlg["lastname"];
                        }
                        else {
                            $shipping_details = array();
                            $shipping_details['firstname'] = "";
                            $shipping_details['lastname'] = "";
                        }
                    }
                    else {
                        $shipping_details = array();                            
                    }
		    
		    if(!empty($get_order)) {
			$push_new = array(
			    'order_id' => $value["order_id"],
			    'order_no' => $get_order["order_no"],
			    'net_amount' => $get_order["net_amount"],
			    'shipping_details' => $shipping_details
			);
		    }
		    else {
			$push_new = array(
			    'order_id' => $value["order_id"],
			    'order_no' => "",
			    'net_amount' => "",
			    'shipping_details' => $shipping_details
			);
		    }
                    
                    $value = array_merge($value, $push_new);
                    $list[$key] = $value;
                }
		else if($value["notification_type"] == 11) {
		    //order details
                    $get_order_driver = $this->db->select("*")
                            ->where("order_id", $value["order_id"])
			    ->where("status", 1)
                            ->get("order_driver")->row_array();
		    
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
		    $list[$key] = $value;
		}
		
                
            }
            return $list;
        }
    }
    
    public function create_notification($post = []) {
        $this->db->insert('notification', $post);
        $notification['notification_id'] = $this->db->insert_id();
        return $notification;
    }
    
    public function order_cancel_reason_list($post=[]) {
	$list = $this->db
                ->where('status', 1)
                ->limit($post['limit'])
                ->offset($post['offset'])
                ->get('cancel_order_reasons')
                ->result_array();
	
	if(!empty($list)) {	    
	    return $list;
	}
    }
    
    public function order_not_completed_reason_list($post=[]) {
	$list = $this->db
                ->where('status', 1)
                ->limit($post['limit'])
                ->offset($post['offset'])
                ->get('notcompleted_order_reasons')
                ->result_array();
	
	if(!empty($list)) {	    
	    return $list;
	}
    }
    
    function get_normal_user_by_id($user_id) {
	$user = $this->db
		    ->where('user_id', $user_id)
		    ->where("user_type", 1)
		    ->get('user')->row_array();
        return $user;
    }
    
    function get_order_by_id($order_id) {
	$order = $this->db->select("*")
		->where("order_id", $order_id)
		->get("orders")->row_array();
	return $order;
    }
    
    function get_order_driver_by_id($order_id) {
	$order = $this->db->select("*")
		->where("order_id", $order_id)
		->get("order_driver")->row_array();
	return $order;
    }
    
    public function accept_reject_order($post = []) {
        $where = array(
            'driver_id' => $post["user_id"],
            'order_id' => $post["order_id"]
        );  
        
        $check = $this->db->select("*")->where($where)->get("order_driver")->row_array();
        
        if(!empty($check)) {   	    
	    //when driver accept the order
            if($post["status"] == 1) {		                
		//delete the notification for request
                $this->db->where("to_user_id", $post["user_id"])
                        ->where("driver_id", $post["user_id"])
                        ->where("order_id", $post["order_id"])
                        ->where("notification_type", "10")
                        ->delete('notification');
		
		//get seller id to send notification
		$order_seller = $this->db->select("*")->where("order_id", $post["order_id"])->get("order_product")->result_array();  
		//get order
		$order = $this->get_order_by_id($post["order_id"]);
		
		//get driver
		$order_driver = $this->get_order_driver_by_id($post["order_id"]);

		//get driver
		$driver = $this->get_user_by_id($order_driver["driver_id"]);		
		
		//send notification to seller about acceptance
		$sellers = array_values(array_unique(array_column($order_seller, 'seller_id')));
		//print_r($sellers); exit;
		
		$seller = $this->get_seller($sellers[0]);
		
		$not_type = 11;
		$message = date("Hi")." A driver has been allocated to your order by Drixin, ".$driver["firstname"]." ".$driver["lastname"].", Driver Number ".$driver["mobileno"].".";
		$message1 = "Order has been accepted by ".$driver["firstname"]." ".$driver["lastname"].", Driver Number ".$driver["mobileno"].".";
				
		foreach ($sellers as $ids) {
		    
		    $push = array(
			'to_user_id' => $ids,
			'order_id' => $post["order_id"],
			'order_no' => $order["order_no"],
			'net_amount' => $order["net_amount"],
			'order_date' => $order["order_date"],
			'driver_name' => $driver["firstname"]." ".$driver["lastname"],
			'driver_mobileno' => $driver["mobileno"],
			'notification_type' => $not_type,
			'message' => $message1,
			'contact_no' => trim($driver["mobileno"])
		    );

		    $insert_array = array(
			'to_user_id' => $ids,
			'order_id' => $post["order_id"],
			'customer_id' => $ids,
			'driver_id' => $driver["user_id"],
			'is_seller' => 1,
			'notification_type' => $not_type,
			'message' => $message1,
		    );
		    
		    $insert_array2 = array(
			'seller_id' => $ids,
			'order_id' => $post["order_id"],
			'user_id' => $order["user_id"],
			'notification_type' => $not_type,
			'message' => $message1
		    );
		    
		    //Save & Send notification
		    $this->create_notification($insert_array); 
		    $this->db->insert('website_notification', $insert_array2);
		}
			
		//send notification to user with details		
		$push = array(
		    'to_user_id' => $order["user_id"],
		    'order_id' => $post["order_id"],
		    'order_no' => $order["order_no"],
		    'net_amount' => $order["net_amount"],
		    'order_date' => $order["order_date"],
		    'driver_name' => $driver["firstname"]." ".$driver["lastname"],
		    'driver_mobileno' => $driver["mobileno"],
		    'notification_type' => $not_type,
		    'message' => $message,
		    'contact_no' => trim($driver["mobileno"])
		);
		
		$insert_array = array(
		    'to_user_id' => $order["user_id"],
		    'order_id' => $post["order_id"],
		    'customer_id' => $order["user_id"],
		    'driver_id' => $driver["user_id"],			
		    'notification_type' => $not_type,
		    'message' => $message,
		);
		
		//Save & Send notification
		$this->create_notification($insert_array); 
		$this->m_notify->send($push);	
                
                $this->db->set("is_accepted", $post["status"])->where($where)->update("notification");
		$this->db->set("order_status", 3)->where("order_id", $post["order_id"])->update("orders");
                $this->db->set("status", 1)->where($where)->update("order_driver");
                return 1;
            }    
	    //when driver reject the order
            else if($post["status"] == 2) {  
		//delete the notification for request                
                $this->db->where("to_user_id", $post["user_id"])
                        ->where("driver_id", $post["user_id"])
                        ->where("order_id", $post["order_id"])
                        ->where("notification_type", "10")
                        ->delete('notification');
                
                //update 
                $this->db->set("is_accepted", $post["status"])->where($where)->update("notification");
                $this->db->set("status", 2)->where($where)->update("order_driver");
				
		// find new driver other than this driver
		$this->cron_model->notify_new_driver($post["order_id"], $post["user_id"]);		
		
                return 2;
            }
        }
        else {
            return false;
        }
    }
    
    public function update_order_status($post=[]){
	$order = $this->get_order_by_id($post["order_id"]);
	
	if(!empty($order)) {	    
	    //get driver
	    $order_driver = $this->get_order_driver_by_id($post["order_id"]);
	    
	    //get driver
	    $driver = $this->get_user_by_id($order_driver["driver_id"]);
	    
	    if($post["status"] == 9) {	    
		//update order status
		$this->db->set("order_status", 9)
			->where("order_id", $post["order_id"])
			->update("orders");
		
		$message = date("Hi")." Your order has been pickedup from store.";
		$not_type = 13;
		
		//send notification to user
		$push = array(
		    'to_user_id' => $order["user_id"],
		    'order_id' => $post["order_id"],
		    'order_no' => $order["order_no"],
		    'net_amount' => $order["net_amount"],
		    'order_date' => $order["order_date"],
		    'driver_name' => $driver["firstname"]." ".$driver["lastname"],
		    'driver_mobileno' => $driver["mobileno"],
		    'notification_type' => $not_type,
		    'message' => $message,
		);
		
		$insert_array = array(
		    'to_user_id' => $order["user_id"],
		    'order_id' => $post["order_id"],
		    'customer_id' => $order["user_id"],
		    'notification_type' => $not_type,
		    'message' => $message,
		);
	    }
	    else if($post["status"] == 10) {	    
		//update order status
		$this->db->set("order_status", 10)
			->where("order_id", $post["order_id"])
			->update("orders");
		
		$message = date("Hi")." Your order arriving - please have your ID ready";
		$not_type = 14;
		
		$push = array(
		    'to_user_id' => $order["user_id"],
		    'order_id' => $post["order_id"],
		    'order_no' => $order["order_no"],
		    'net_amount' => $order["net_amount"],
		    'order_date' => $order["order_date"],
		    'driver_name' => $driver["firstname"]." ".$driver["lastname"],
		    'driver_mobileno' => $driver["mobileno"],
		    'notification_type' => $not_type,
		    'message' => $message,
		);

		//send notification to user
		$insert_array = array(
		    'to_user_id' => $order["user_id"],
		    'order_id' => $post["order_id"],
		    'customer_id' => $order["user_id"],
		    'notification_type' => $not_type,
		    'message' => $message,
		);
	    }
	    else if($post["status"] == 11) {	    
		//update order status
		$this->db->set("order_status", 11)
			->set("delivered_date", date("Y-m-d H:i:s"))
			->where("order_id", $post["order_id"])
			->update("orders");
		
		$message = date("Hi")." Your driver has arrived - please have your ID ready";
		$not_type = 15;
		
		$push = array(
		    'to_user_id' => $order["user_id"],
		    'order_id' => $post["order_id"],
		    'order_no' => $order["order_no"],
		    'net_amount' => $order["net_amount"],
		    'order_date' => $order["order_date"],
		    'driver_name' => $driver["firstname"]." ".$driver["lastname"],
		    'driver_mobileno' => $driver["mobileno"],
		    'notification_type' => $not_type,
		    'message' => $message,
		);

		//send notification to user
		$insert_array = array(
		    'to_user_id' => $order["user_id"],
		    'order_id' => $post["order_id"],
		    'customer_id' => $order["user_id"],
		    'notification_type' => $not_type,
		    'message' => $message,
		);
	    }
	    else if($post["status"] == 12) {	    
		//update order status
		$this->db->set("order_status", 12)
			->where("order_id", $post["order_id"])
			->update("orders");
		
		$message = date("Hi")." Your driver is at halt.";
		$not_type = 16;
		
		$push = array(
		    'to_user_id' => $order["user_id"],
		    'order_id' => $post["order_id"],
		    'order_no' => $order["order_no"],
		    'net_amount' => $order["net_amount"],
		    'order_date' => $order["order_date"],
		    'driver_name' => $driver["firstname"]." ".$driver["lastname"],
		    'driver_mobileno' => $driver["mobileno"],
		    'notification_type' => $not_type,
		    'message' => $message,
		);

		//send notification to user
		$insert_array = array(
		    'to_user_id' => $order["user_id"],
		    'order_id' => $post["order_id"],
		    'customer_id' => $order["user_id"],
		    'notification_type' => $not_type,
		    'message' => $message,
		);
	    }
	    else if($post["status"] == 13) {

		$not_completion_reason = $this->db->select("ncreason_id, reason, other_reason")
			->where("ncreason_id", $post["reason"])
			->get("notcompleted_order_reasons")->row_array();
		
		//update order status
		$this->db->set("order_status", 13)
			->set("delivered_date", date("Y-m-d", strtotime($post["delivery_date"])))
			->set("delivered_time", date("H:i:s", strtotime($post["delivery_date"])))
			->set("no_completion_reason", $post["reason"])
			->set("not_completed_reason_other", $post["other_reason"])
			->where("order_id", $post["order_id"])
			->update("orders");
		
		$message = date("Hi")." Your order is not completed because ".$not_completion_reason["reason"];
		$not_type = 17;
		
		$push = array(
		    'to_user_id' => $order["user_id"],
		    'order_id' => $post["order_id"],
		    'order_no' => $order["order_no"],
		    'net_amount' => $order["net_amount"],
		    'order_date' => $order["order_date"],
		    'driver_name' => $driver["firstname"]." ".$driver["lastname"],
		    'driver_mobileno' => $driver["mobileno"],
		    'notification_type' => $not_type,
		    'message' => $message,
		);

		//send notification to user
		$insert_array = array(
		    'to_user_id' => $order["user_id"],
		    'order_id' => $post["order_id"],
		    'customer_id' => $order["user_id"],
		    'notification_type' => $not_type,
		    'message' => $message,
		);
		
		//save driver earnings
		$this->db->insert("driver_earnings", array(
		    "user_id" => $order_driver["driver_id"],
		    "order_id" => $post["order_id"],
		    "amount" => $order["delivery_charges"]
		));
		
		//save in wallet
		$wallet = $driver["wallet"] + $order["delivery_charges"];
		$this->db->set("wallet", $wallet)
			->where("user_id", $order_driver["driver_id"])
			->update("user");
		
		//save in wallet history
		$wallet_history = array(
		    'user_id' => $order_driver["driver_id"],
		    'type'=> 1,
		    'debit_credit_amount' => $order["delivery_charges"],
		    'balance_amount' => $wallet,
		    'note' => 'earnings',
		    'order_id' => $post["order_id"],
		    'payment_status' => 'SUCCESS',
		    'payment_history' => '{"status":"true", "payment":"success", "amount":'.$order["delivery_charges"].'}',
		    'transaction_id' => date('YmdHis').$post["order_id"].$order_driver["driver_id"]
		);
		$this->db->insert("wallet_history", $wallet_history);
		
		//update distance & time by driver
		$this->db->set("distance", $post["distance_traveled"])
			->set("duration", $post["duration_taken"])
			->where("order_id", $post["order_id"])
			->where("driver_id", $post["user_id"])
			->update("order_driver");
		
	    }
	    else if($post["status"] == 4) {	    
		//update order status
		$this->db->set("order_status", 4)
			->set("delivered_date", date("Y-m-d", strtotime($post["delivery_date"])))
			->set("delivered_time", date("H:i:s", strtotime($post["delivery_date"])))
			->where("order_id", $post["order_id"])
			->update("orders");
		
		$message = date("Hi")." ".date("d.m.y")." Your order number ".$order["order_no"]." was successfully delivered";
		$not_type = 3;
		
		$push = array(
		    'to_user_id' => $order["user_id"],
		    'order_id' => $post["order_id"],
		    'order_no' => $order["order_no"],
		    'net_amount' => $order["net_amount"],
		    'order_date' => $order["order_date"],
		    'driver_name' => $driver["firstname"]." ".$driver["lastname"],
		    'driver_mobileno' => $driver["mobileno"],
		    'notification_type' => $not_type,
		    'message' => $message,
		);

		//send notification to user
		$insert_array = array(
		    'to_user_id' => $order["user_id"],
		    'order_id' => $post["order_id"],
		    'customer_id' => $order["user_id"],
		    'notification_type' => $not_type,
		    'message' => $message,
		);
		
		//save driver earnings
		$this->db->insert("driver_earnings", array(
		    "user_id" => $order_driver["driver_id"],
		    "order_id" => $post["order_id"],
		    "amount" => $order["delivery_charges"]
		));
		
		//save in wallet
		$wallet = $driver["wallet"] + $order["delivery_charges"];
		$this->db->set("wallet", $wallet)
			->where("user_id", $order_driver["driver_id"])
			->update("user");
		
		//save in wallet history
		$wallet_history = array(
		    'user_id' => $order_driver["driver_id"],
		    'type'=> 1,
		    'debit_credit_amount' => $order["delivery_charges"],
		    'balance_amount' => $wallet,
		    'note' => 'earnings',
		    'order_id' => $post["order_id"],
		    'payment_status' => 'SUCCESS',
		    'payment_history' => '{"status":"true", "payment":"success", "amount":'.$order["delivery_charges"].'}',
		    'transaction_id' => date('YmdHis').$post["order_id"].$order_driver["driver_id"]
		);
		$this->db->insert("wallet_history", $wallet_history);
		
		//update distance & time by driver
		$this->db->set("distance", $post["distance_traveled"])
			->set("duration", $post["duration_taken"])
			->set("status", 3)
			->where("order_id", $post["order_id"])
			->where("driver_id", $post["user_id"])
			->update("order_driver");
	    }
	    
	    //Save & Send notification
	    $this->create_notification($insert_array); 
	    $this->m_notify->send($push);	
	    
	    return true;
	}
	else{
	    return false;
	}	
    }
    
    public function order_details($post=[]) {
	$response = array();
	$order = $this->db->select("*")
		->where("order_id", $post["order_id"])
		->get("orders")->row_array();
	
	if(!empty($order)) {
	    $response["order_id"] = $order["order_id"];
	    $response["order_date"] = $order["order_date"];
	    $response["order_no"] = $order["order_no"];
	    $response["net_amount"] = $order["net_amount"];
	    $response["order_status"] = $order["order_status"];
	    $response["delivered_date"] = $order["delivered_date"];
	    $response["delivered_time"] = $order["delivered_time"];
	    
	    //user address
	    $shipping_details = $this->db->select("*")
				    ->where('shipping_id', $order["shipping_id"])
				    ->get('shipping_mst')->row_array();
	    
	    $zcode = $this->db->select("*")
		->where('zipcode_id', $shipping_details["zipcode_id"])
		->get('zipcode')->row_array(); 

	    if(!empty($zcode)){
		$shipping_details["zipcode"] = $zcode["zipcode"];
	    } else {
		$shipping_details["zipcode"] = 'Service No Available At this destination.';
	    }
	    
	    $address = $shipping_details["address"].', '.$shipping_details["zipcode"];
	    
	    if(!empty($post["latitude"]) && !empty($post["longitude"])) {
		//user details
		$user = $this->db->select('*, get_distance_metres(latitude, longitude, '.$post["latitude"].' , '.$post["longitude"].') as distance', false)
			->where('user_id', $order["user_id"])
			->where("user_type", 1)
			->get('user')->row_array();
		//user distance
		$distance = round(($user["distance"] / 1609.34), 2);

		$response["user"] = array(
		    "id" => $user["user_id"],
		    "name" => $user["firstname"]." ".$user["lastname"],
		    "mobileno" => $user["mobileno"],
		    "latitude" => $user["latitude"],
		    "longitude" => $user["longitude"],
		    "address" => $address,
		    "distance" => $distance." miles",
		);	    

		//get seller from order products table
		$find_seller = $this->db->select("seller_id")
			->where("order_id", $order["order_id"])
			 ->get("order_product")->result_array(); 

		//get unique seller
		$seller_ids = array_unique(array_column($find_seller, "seller_id"));   
		$seller_arr = array();
		foreach($seller_ids as $ids) {
		    //seller details
		    $seller = $this->db->select('seller_id, seller_name, contact_no, address, company_name, latitude, longitude, get_distance_metres(latitude, longitude, '.$post["latitude"].' , '.$post["longitude"].') as distance', false)
			    ->where("seller_id", $ids)
			    ->get("seller")->row_array();
		    //seller distance
		    $seller_distance = round(($seller["distance"] / 1609.34), 2);

		    $sellr = array(
			"id" => $seller["seller_id"],
			"name" => $seller["seller_name"],
			"mobileno" => $seller["contact_no"],
			"latitude" => $seller["latitude"],
			"longitude" => $seller["longitude"],
			"address" => $seller["address"],
			"company_name" => $seller["company_name"],
			"distance" => $seller_distance." miles",
		    );
		    array_push($seller_arr, $sellr);		
		}

		$response["seller"] = $seller_arr;
	    }
	    else {
		//user details
		$user = $this->db->select('*', false)
			->where('user_id', $order["user_id"])
			->where("user_type", 1)
			->get('user')->row_array();
		//user distance
		$distance = 0;

		$response["user"] = array(
		    "id" => $user["user_id"],
		    "name" => $user["firstname"]." ".$user["lastname"],
		    "mobileno" => $user["mobileno"],
		    "latitude" => $user["latitude"],
		    "longitude" => $user["longitude"],
		    "address" => $address,
		    "distance" => $distance." miles",
		);	    

		//get seller from order products table
		$find_seller = $this->db->select("seller_id")
			->where("order_id", $order["order_id"])
			 ->get("order_product")->result_array(); 

		//get unique seller
		$seller_ids = array_unique(array_column($find_seller, "seller_id"));   
		$seller_arr = array();
		foreach($seller_ids as $ids) {
		    //seller details
		    $seller = $this->db->select('seller_id, seller_name, contact_no, address, company_name, latitude, longitude', false)
			    ->where("seller_id", $ids)
			    ->get("seller")->row_array();
		    //seller distance
		    $seller_distance = 0;

		    $sellr = array(
			"id" => $seller["seller_id"],
			"name" => $seller["seller_name"],
			"mobileno" => $seller["contact_no"],
			"latitude" => $seller["latitude"],
			"longitude" => $seller["longitude"],
			"address" => $seller["address"],
			"company_name" => $seller["company_name"],
			"distance" => $seller_distance." miles",
		    );
		    array_push($seller_arr, $sellr);		
		}

		$response["seller"] = $seller_arr;
	    }
	    
	    //print_r($response); exit;
	    
	    return $response;
	}
    }
    
    public function add_target($post = []) {
	
	if($post["target_type"] == '1') {
	    $this->db->where("DATE(target_start_date) = '".date('Y-m-d', strtotime($post["start_date"]))."'");
	}
	else {
	    $this->db->where("DATE(target_start_date) <= '".date('Y-m-d', strtotime($post["start_date"]))."' AND DATE(target_end_date) >= '".date('Y-m-d', strtotime($post["start_date"]))."'");
	    // $this->db->where("DATE(target_start_date) <= '".date('Y-m-d', strtotime($post["end_date"]))."' AND DATE(target_end_date) >= '".date('Y-m-d', strtotime($post["end_date"]))."'");
	}
	
	$check = $this->db->select("*")
		->where("target_type", $post["target_type"])
		->where("user_id", $post["user_id"])	
		->where("status", 1)
		->get("driver_target")->row_array();
	
	if(!empty($check)) {
	    return 1;
	}
	else {
	    if(isset($post["end_date"])) {
		$end_date = date('Y-m-d', strtotime($post["end_date"]));
	    }
	    else {
		$end_date = date('Y-m-d', strtotime($post["start_date"]));
	    }
	    
	    $ins_arr = array(
		"user_id" => $post["user_id"],
		"target_type" => $post["target_type"],
		"target_amount" => $post["target_amount"],
		"target_start_date" => $post["start_date"],
		"target_end_date" => $end_date,
	    );
	    $insert = $this->db->insert("driver_target", $ins_arr);
	    $last_id = $this->db->insert_id();
	    if($insert) {
		$data = $this->db->select("*")->where("target_id", $last_id)->get("driver_target")->row_array();	
		return $data;
	    }
	    else {
		return 3;
	    }
	}	
    }
    
    public function get_target($post = []) {
	$target = array();
	$empty_response = array(
	    "payments" => array(
		"amount" => "0",
		"percentage" => "0",
	    ),	    
	    "bonus" => array(
		"amount" => "0",
		"percentage" => "0",
	    ),	    
	    "remaining" => array(
		"amount" => "0",
		"percentage" => "0",
	    ),
	    "status" => "",
	    "target_amount" => "",
	    "target_end_date" => "",
	    "target_id" => "",
	    "target_start_date" => "",
	    "target_type" => "",
	    "user_id" => ""
	);
	
	//Daily		
	$daily = $this->db->select("*")
		->where("user_id", $post["user_id"])
		->where("target_type", 1)
        ->where("status", 1)
		->where("target_start_date", date('Y-m-d'))
		->limit(1)
		->order_by("date", "desc")
		->get("driver_target")->row_array();	
	
	if(!empty($daily)) {
	    $daily["payments"] = array(
		"amount" => "0",
		"percentage" => "0",
	    );
	    
	    $daily["bonus"] = array(
		"amount" => "0",
		"percentage" => "0",
	    );
	    
	    $daily["remaining"] = array(
		"amount" => $daily["target_amount"],
		"percentage" => "100",
	    );
	    
	    $target["daily"] = $daily;
	}
	else {	   
	    $target["daily"] = $empty_response;
	}
	
	//Weekly
	$weekly = $this->db->select("*")
		->where("user_id", $post["user_id"])
		->where("target_type", 2)
        ->where("status", 1)
		->where("( target_start_date <= '".date('Y-m-d')."' AND target_end_date >= '".date('Y-m-d')."')")
		->limit(1)
		->order_by("date", "desc")
		->get("driver_target")->row_array();	
	
	if(!empty($weekly)) {
	    $weekly["payments"] = array(
		"amount" => "0",
		"percentage" => "0",
	    );
	    
	    $weekly["bonus"] = array(
		"amount" => "0",
		"percentage" => "0",
	    );
	    
	    $weekly["remaining"] = array(
		"amount" => $weekly["target_amount"],
		"percentage" => "100",
	    );
	    
	    $target["weekly"] = $weekly;
	}
	else {	    
	    $target["weekly"] = $empty_response;
	}
	
	//Monthly
	$monthly = $this->db->select("*")
		->where("user_id", $post["user_id"])
		->where("target_type", 3)
        ->where("status", 1)
		->where("( target_start_date <= '".date('Y-m-d')."' AND target_end_date >= '".date('Y-m-d')."')")
		->limit(1)
		->order_by("date", "desc")
		->get("driver_target")->row_array();	
	
	if(!empty($monthly)) {
	    $monthly["payments"] = array(
		"amount" => "0",
		"percentage" => "0",
	    );
	    
	    $monthly["bonus"] = array(
		"amount" => "0",
		"percentage" => "0",
	    );
	    
	    $monthly["remaining"] = array(
		"amount" => $monthly["target_amount"],
		"percentage" => "100",
	    );
	    
	    $target["monthly"] = $monthly;
	}
	else {
	    $target["monthly"] = $empty_response;
	}
	
	return $target;
    }
    
    public function get_bookings($post = []) {
	$booking = array();
	if($post["type"] == 1) {
	    $obooking = $this->db->select("*")
			->where("driver_id", $post["user_id"])
			->where("DATE(date) < '".date('Y-m-d')."'")			
			->order_by("date", "desc")
			->limit($post["limit"])
			->offset($post["offset"])
			->get("order_driver")->result_array();
	    
	    if(!empty($obooking)) {
		foreach($obooking as $obook) {
		    $post["order_id"] = $obook["order_id"];
		    $data = $this->order_details($post);
		    if(!empty($data)) {
			array_push($booking, $data);
		    }
		}
	    }
	}
	else if($post["type"] == 2) {
	    $obooking = $this->db->select("*")
			->where("driver_id", $post["user_id"])
			->where("status IN (0,1)")
			->where("DATE(date) >= '".date('Y-m-d')."'")
			->order_by("date", "desc")
			->limit($post["limit"])
			->offset($post["offset"])
			->get("order_driver")->result_array();
	    
	    if(!empty($obooking)) {
		foreach($obooking as $obook) {
		    $post["order_id"] = $obook["order_id"];
		    $data = $this->order_details($post);
		    if(!empty($data)) {
			array_push($booking, $data);
		    }
		}
	    }
	}	
	else if($post["type"] == 3) {
	    $obooking = $this->db->select("*")
			->where("driver_id", $post["user_id"])
			->where("status = 2")
			->order_by("date", "desc")
			->limit($post["limit"])
			->offset($post["offset"])
			->get("order_driver")->result_array();
	    
	    if(!empty($obooking)) {
		foreach($obooking as $obook) {
		    $post["order_id"] = $obook["order_id"];
		    $data = $this->order_details($post);
		    if(!empty($data)) {
			array_push($booking, $data);
		    }
		}
	    }
	}
	
	return $booking;	
    }
    
    public function add_schedule_time($post = []) {	
	$counter = 0;
	$exist_arr = array();
	
	//get array of date
	$darr = $this->returnDates($post["start_date"], $post["end_date"]);
	foreach($darr as $date) {
	    $schedule_date = $date->format('Y-m-d');
	    $where = "(end_time > '".$post["start_time"]."' and start_time < '".$post["end_time"]."')";
	    
	    $check = $this->db->select("*", false)
		    ->where("driver_id", $post["user_id"])
		    ->where("schedule_date", $schedule_date)
		    ->where($where)
		    ->where("status", 1)
		    ->get("driver_schedule")->row_array();
		    
	    if(empty($check)) {
		$arr = array(
		    "driver_id" => $post["user_id"],
		    "schedule_date" => $schedule_date,
		    "start_time" => $post["start_time"],
		    "end_time" => $post["end_time"],
		);

		$this->db->insert("driver_schedule", $arr);
	    }
	    else {
		$counter++;
		array_push($exist_arr, date('d M,Y', strtotime($schedule_date)));
	    }
	}
	
	if($counter > 0) {
	    return implode(', ', $exist_arr);
	}
	else {
	    return 1;
	}
    }
    
    public function update_schedule_time($post = []) {
	$post["driver_id"] = $post["user_id"];
	unset($post["user_id"]);
	    
	$check = $this->db->select("sch_id")
		->where("sch_id", $post["sch_id"])
		->where("driver_id", $post["driver_id"])
		->get("driver_schedule")->row_array();
	
	if(!empty($check)) {
	    $this->db->set($post)
		    ->where("sch_id", $post["sch_id"])
		    ->where("driver_id", $post["driver_id"])
		    ->update("driver_schedule");
	    
	    $data = $this->get_schedule_by_id($post["sch_id"]);
	    return $data;
	}
	else {
	    return 3;
	}	
    }
    
    public function delete_schedule_time($post = []) {
	$check = $this->db->select("sch_id")
		->where("sch_id", $post["sch_id"])
		->where("driver_id", $post["user_id"])
		->get("driver_schedule")->row_array();
	
	if(!empty($check)) {
	    $this->db->set("status", 0)
		    ->where("sch_id", $post["sch_id"])
		    ->where("driver_id", $post["user_id"])
		    ->update("driver_schedule");
	    
	    return 1;
	}
	else {
	    return 3;
	}	
    }
    
    public function get_schedule_time($post = []) {
//	$where = "driver_id = ".$post["user_id"]." AND ((start_date BETWEEN '".$post["start_date"]."' AND '".$post["end_date"]."') OR (end_date BETWEEN '".$post["start_date"]."' AND '".$post["end_date"]."'))";
	
	$where = "driver_id = ".$post["user_id"]." AND schedule_date = '".$post["schedule_date"]."'";
	
	$check = $this->db->select("*")
		->where($where)
		->where("status", 1)
		->get("driver_schedule")->result_array();
	
	if(!empty($check)) {
	    return $check;
	}
	else {
	    return false;
	}	
    }
    
    public function get_schedule_by_id($sch_id) {
	$check = $this->db->select("*")
		->where("sch_id", $sch_id)
		->where("status", 1)
		->get("driver_schedule")->row_array();
	
	if(!empty($check)) {
	    return $check;
	}
	else {
	    return false;
	}	
    }
    
    function returnDates($fromdate, $todate) {
	$from = \DateTime::createFromFormat('Y-m-d', $fromdate);
	$to = \DateTime::createFromFormat('Y-m-d', $todate);
	return new \DatePeriod(
	    $from,
	    new \DateInterval('P1D'),
	    $to->modify('+1 day')
	);
    }
    
    public function bank_details($post=[]) {
	$details = $this->db->select("id, user_id, account_id, account_number, routing_number, bank_account, bank_name, account_holder_name, sort_code, card_color, is_primary, date, updated_date, email")
		->where("user_id", $post["user_id"])
		->where("type", "2")
		->where("status", "1")
		->order_by("id", "desc")
		->get("stripe_connect_accounts")->result_array();
	
	if(!empty($details)){
	    foreach ($details as $key => $values) {
		$value = array_map(function($val) {
		    if(is_null($val)) {
			$val = "";
		    }
		    return $val;
		}, $values);
		
		$details[$key] = $value;
		//$details[$key]["response"] = json_decode($value["response"]);
	    }
	    return $details;
	}
	else {
	    return false;
	}	
    }
    
    public function add_card($post = []) {
	//print_r($post); exit;
	
	$driver = $this->db->select("*")->where("user_type", 2)->where("user_id", $post["user_id"])->get("user")->row_array();
	if(empty($driver["birthdate"]) || $driver["birthdate"] == "0000-00-00") {
	    $res[0] = 3;
	    $res[1] = 'Please add your date of birth';
	    
	    return $res;
	}
	else if(empty($driver["city"]) || empty($driver["address"]) || empty($driver["postalcode"])) {
	    $res[0] = 4;
	    $res[1] = 'Please add your address with city & postal code.';
	    
	    return $res;
	}
	else if(empty($driver["country_code"]) || empty($driver["mobileno"])) {
	    $res[0] = 5;
	    $res[1] = 'Please add your contact number with country code.';
	    
	    return $res;
	}
	else if(empty($driver["firstname"]) || empty($driver["lastname"])) {
	    $res[0] =6;
	    $res[1] = 'Please update your firstname and lastname.';
	    
	    return $res;
	}
	
	$dob = explode("-",$driver["birthdate"]);
	
	if(isset($post["email"]) && !empty($post["email"])) {
	    $email = $post["email"];
	}
	else {
	    $email = $driver["email"];
	}
	
	if(isset($post["routing_number"]) && !empty($post["routing_number"])) {
	    $sort = $post["routing_number"];
	}
	else {
	    $sort = str_replace("-", "", $post["sort_code"]);
	}
	
	$config_data = $this->db->where_in('key', array('client_key', 'service_key', 'payment_mode', 'test_public_key', 'test_secret_key', 'currency', 'currency_code', 'country'))->get('setting')->result_array();

	foreach ($config_data as $key => $row) {
	    $tmp_arr[$row['key']] = $row['value'];
	}
	
	if($tmp_arr['payment_mode'] == '1') {
	    $secret_key = $tmp_arr['test_secret_key'];
	}
	else if($tmp_arr['payment_mode'] == '2') {
	    $secret_key = $tmp_arr['service_key'];
	}
	
	if($tmp_arr['payment_mode'] == '1') {
	    $client_key = $tmp_arr['test_public_key'];
	}
	else if($tmp_arr['payment_mode'] == '2') {
	    $client_key = $tmp_arr['client_key'];
	}

	$config['client_key'] = $client_key;
	$config['service_key'] = $secret_key;
	$config['currency'] = $tmp_arr['currency'];
	$config['country'] = COUNTRY;
	$config['currency_code'] = CURRENCY;
	
	$account = array(
	    "type" => "custom",
	    "country" =>$config['country'],
	    "email" => $email,
	    "external_account"=>[
		"object"=>"bank_account",
		"country"=>$config['country'],
		"currency"=> strtolower(CURRENCY),
		"account_holder_name"=>$post["account_holder_name"],
		"account_holder_type"=>"individual",
		"routing_number"=>$sort,
		"account_number"=>$post["account_number"]
	    ],
	    "tos_acceptance"=>[
		"date"=>time(),
		'ip' => $_SERVER['REMOTE_ADDR']
	    ],
	    "requested_capabilities"=>[
		"transfers", "card_payments", "legacy_payments"
	    ],
	    "business_type" => "individual",
	    "individual" => [
		"address" => [
		    "city" => $driver["city"],
		    "line1" => $driver["address"],
		    "postal_code" => $driver["postalcode"]
		],
		"dob" => [
			"day" => $dob[2],
			"month" => $dob[1],
			"year" => $dob[0]
		],
		"email" => $email,
		"first_name" => $driver["firstname"],
		"last_name" => $driver["lastname"],
		"phone" => "+".$driver["country_code"].$driver["mobileno"]
	    ],
	    "business_profile"=>[
		"url" => "https://www.drinxin.com",
		"product_description" => "Driver Stripe Connect Account"
	    ],
	    "settings"=>[
		"payouts" => [
		    "schedule" => [
			"interval" => "manual",
		    ]
		]
	    ],
	    "metadata" => [
		"description" => $post["account_holder_name"] . " Stripe Connect Account (Driver)"
	    ],
	);
	
	//print_r($account); exit;
	
	try{   
	    \Stripe\Stripe::setApiKey($config["service_key"]); //secret key
	    \Stripe\Stripe::setApiVersion("2019-12-03");
	    //create account
	    $account_obj = \Stripe\Account::create($account);  
	    $account_obj = json_encode($account_obj);
	    $account_obj = json_decode($account_obj, true);

	    $connect_accont = [
		'user_id' => $post["user_id"],
		'account_id' => $account_obj['external_accounts']['data'][0]['account'],
		'bank_account' => $account_obj['external_accounts']['data'][0]['id'],
		'bank_name' => $post["bank_name"],
		'account_holder_name' => $post["account_holder_name"],
		'account_number' => $post["account_number"],
		'routing_number' => $sort,
		'sort_code' => $post["sort_code"],
		'card_color' => $post["card_color"],
		'public_key' => $config['client_key'],
		'secret_key' => $config['service_key'],
		'response' => json_encode($account_obj),
		'type' => 2,		    
		'is_primary' => $post["is_primary"],
		"email" => $driver["email"],
	    ];

	    $this->db->insert('stripe_connect_accounts', $connect_accont);
	    $last_id = $this->db->insert_id();
	    
	    if($post["is_primary"] == 1) {
		//update account details in user table
		$this->db->set("has_connect_ac", 1)
			->set("account_number", $post["account_number"])
			->set("routing_no", $sort)
			->set("bank_name", $post["bank_name"])
			->where("user_type", 2)
			->where("user_id", $post["user_id"])->update("user");
		
		//update stripe connect table
		$this->db->set("is_primary", 0)
			->where("id NOT IN (".$last_id.")")
			->where("user_id", $post["user_id"])->update("stripe_connect_accounts");
	    }
	    else {
		$this->db->set("has_connect_ac", 1)
			->where("user_type", 2)
			->where("user_id", $post["user_id"])->update("user");
	    }

	    $res[0] = 1;
	    $res[1] = 'Account successfully created';
	} 
	catch (Exception $e) {
	    $account_obj = $e->getError(); 
	    $account_obj = json_encode($account_obj);
	    $account_obj = json_decode($account_obj, true);
	    
	    $res[0] = 2;
	    $res[1] = $account_obj["message"];
	}
	return $res;
       // print_r($account_obj); exit;
    }
    
    public function statistics($post = []) {
	$statistics = array();
	//get total accepted deliveries
	$accepted_deliveries = $this->db->select("order_driver_id")
		->where("status", 1)
		->where("driver_id", $post["user_id"])
		->get("order_driver")->num_rows();
	
	$statistics["accepted_deliveries"] = $accepted_deliveries;
	
	//get total rejected deliveries
	$rejected_deliveries = $this->db->select("order_driver_id")
		->where("status", 2)
		->where("driver_id", $post["user_id"])
		->get("order_driver")->num_rows();
	
	$statistics["rejected_deliveries"] = $rejected_deliveries;
	
	//get total distance travelled in miles
	$distance_traveled = $this->db->select("COALESCE(sum(distance), 0) as total_distance", false)
		->where("status", 1)
		->where("driver_id", $post["user_id"])
		->get("order_driver")->row_array();
		
	$statistics["distance_in_miles"] = $distance_traveled["total_distance"];
	
	//get total duration taken
	$duration = $this->db->select("COALESCE( SEC_TO_TIME( SUM( TIME_TO_SEC( duration ) ) ) , '00:00:00') as total_time", false)
		->where("status", 1)
		->where("driver_id", $post["user_id"])
		->get("order_driver")->row_array();
	
	//$time = explode(":", $duration["total_time"]);
	
	$statistics["duration"] = $duration["total_time"];
	
	return $statistics;	
    }
    
    public function accepted_deliveries($post = []) {
    	$deliveries = array();
    	//get total accepted deliveries
    	$accepted_deliveries = $this->db->select("order_driver_id")
    		->where("status", 1)
		->where("MONTH(updated_date)", $post["month"])
		->where("YEAR(updated_date)", $post["year"])
    		->where("driver_id", $post["user_id"])
    		->get("order_driver")->num_rows();
    	
    	$deliveries["total_deliveries"] = $accepted_deliveries;
    	
    	//total earned amount
    	$earned_amt = $this->db->select("COALESCE(sum(amount),0) as wallet", false)
    		->where("status", 1)
    		->where("MONTH(date)", $post["month"])
		->where("YEAR(date)", $post["year"])
		->where("user_id", $post["user_id"])
    		->get("driver_earnings")->row_array();
    	
    	$deliveries["amount_earned"] = number_format($earned_amt["wallet"], 2);
    	
    	//list of orders group by date
    	$list = $this->db->select("o.delivered_date, DATE_FORMAT(o.delivered_date, '%W, %b %d %Y') as date_of_delivery, count(o.order_id) as total_deliveries", false)
                ->join("order_driver od", "od.order_id = o.order_id AND od.driver_id = ".$post["user_id"])
                ->where("o.order_status", 4)
                ->where("MONTH(o.delivered_date)", $post["month"])            
                ->where("YEAR(o.delivered_date)", $post["year"])            
                ->group_by("o.delivered_date")
                ->order_by("o.delivered_date", "desc")
                ->get("orders o")->result_array();

        foreach($list as $k => $v) {
            $value = array_map(function($val) {
                if(is_null($val)) {
                    $val = "";
                }
                return $val;
            }, $v);

            $list[$k] = $value;
        }

        $deliveries["list"] = $list;
    	
    	return $deliveries;	
    }

    public function rejected_deliveries($post = []) {
        $deliveries = array();
        //get total rejected deliveries
        $accepted_deliveries = $this->db->select("order_driver_id")
            ->where("status", 2)
            ->where("driver_id", $post["user_id"])
            ->where("MONTH(updated_date)", $post["month"])
            ->where("YEAR(updated_date)", $post["year"])
            ->get("order_driver")->num_rows();
        
        $deliveries["total_deliveries"] = $accepted_deliveries;
        
        //total amount can be earned
        $earned_amt = $this->db->select("COALESCE(sum(o.delivery_charges), 0) as delivery_charges", false)
                ->join("orders o", "o.order_id = od.order_id")
                ->where("MONTH(od.updated_date)", $post["month"])
                ->where("YEAR(od.updated_date)", $post["user_id"])
                ->where("od.driver_id", $post["year"])
                ->get("order_driver od")->row_array();                
        
        $deliveries["amount_can_be_earned"] = number_format($earned_amt["delivery_charges"], 2);
        
        //list of orders group by date 
        $list = $this->db->select("DATE_FORMAT(updated_date, '%Y-%m-%d') as updated_date, DATE_FORMAT(updated_date, '%W, %b %d %Y') as date_of_rejection, count(order_id) as total_rejected", false)
                ->where("driver_id = ".$post["user_id"])
                ->where("status", 2)
                ->where("MONTH(updated_date)", $post["month"])            
                ->where("YEAR(updated_date)", $post["year"])            
                ->group_by("updated_date")
                ->order_by("updated_date", "desc")
                ->get("order_driver")->result_array();

        foreach($list as $k => $v) {
            $value = array_map(function($val) {
                if(is_null($val)) {
                    $val = "";
                }
                return $val;
            }, $v);

            $list[$k] = $value;
        }

        $deliveries["list"] = $list;
        
        return $deliveries; 
    }

    public function accept_delivery_detail($post = []) {     
        //amount earned 
        $amt_earned = $this->db->select("COALESCE(sum(amount),0) as total_amount", false)
                    ->where("user_id", $post["user_id"])
                    ->where("DATE(date)", $post["adate"])
                    ->get("driver_earnings")->row_array();
	
	//tips
	$amt_tips = $this->db->select("COALESCE(sum(tips),0) as total_tips", false)
                    ->where("user_id", $post["user_id"])
                    ->where("DATE(date)", $post["adate"])
                    ->get("driver_earnings")->row_array();

        $deliveries["amt_earned"] = $amt_earned["total_amount"];  
	$deliveries["amt_earned_by_trip"] = $amt_earned["total_amount"] - $amt_tips["total_tips"];  
	$deliveries["total_tips"] = $amt_tips["total_tips"]; 	
	
	//duration
	$total_duration = $this->db->select("COALESCE( SEC_TO_TIME( SUM( TIME_TO_SEC( duration ) ) ) , '00:00:00') as total_time", false)
                    ->where("driver_id", $post["user_id"])
		    ->where("status", 1)
                    ->where("DATE(updated_date)", $post["adate"])
                    ->get("order_driver")->row_array();
		
	$deliveries["total_time"] = $total_duration["total_time"]; 	

        //list of orders group by date
        $list = $this->db->select("o.order_id, o.order_no, o.track_no, o.user_id, o.shipping_id, TIME_FORMAT(o.delivered_time, '%h:%i %p') as delivered_time, od.amount, s.address, s.zipcode_id, s.name, s.contactno, z.zipcode", false)
                ->join("driver_earnings od", "od.order_id = o.order_id AND od.user_id = ".$post["user_id"])
                ->join("shipping_mst s", "s.shipping_id = o.shipping_id")
                ->join("zipcode z", "z.zipcode_id = s.zipcode_id")
                ->where("o.order_status", 4)
                ->where("o.delivered_date = '".$post['adate']."'")
                ->group_by("o.delivered_date")
                ->order_by("o.delivered_time", "desc")
                ->get("orders o")->result_array();

        foreach($list as $k => $v) {
            $value = array_map(function($val) {
                if(is_null($val)) {
                    $val = "";
                }
                return $val;
            }, $v);

            $list[$k] = $value;
        }

        $deliveries["list"] = $list;
        
        return $deliveries; 
    }

    public function rejected_delivery_detail($post = []) {     
        //amount earned 
        $amt_earned = $this->db->select("COALESCE(sum(o.delivery_charges), 0) as total_amount", false)
                ->join("order_driver od", "od.order_id = o.order_id AND od.driver_id = ".$post["user_id"]." AND DATE(od.updated_date) = '".$post["adate"]."'")
                ->get("orders o")->row_array();

        $deliveries["amt_earned"] = $amt_earned["total_amount"];    

        //list of orders group by date
        $list = $this->db->select("o.order_id, o.order_no, o.track_no, o.user_id, o.shipping_id, TIME_FORMAT(od.updated_date, '%h:%i %p') as delivered_time, o.delivery_charges as amount, od.driver_id, s.address, s.zipcode_id, s.name, s.contactno, z.zipcode", false)
                ->join("order_driver od", "od.order_id = o.order_id AND od.status=2 AND od.driver_id = ".$post["user_id"])
                ->join("shipping_mst s", "s.shipping_id = o.shipping_id")
                ->join("zipcode z", "z.zipcode_id = s.zipcode_id")
                ->where("DATE(od.updated_date) = '".$post['adate']."'")
                ->group_by("DATE(od.updated_date)")
                ->order_by("delivered_time", "desc")
                ->get("orders o")->result_array();

        foreach($list as $k => $v) {
            $value = array_map(function($val) {
                if(is_null($val)) {
                    $val = "";
                }
                return $val;
            }, $v);

            $list[$k] = $value;
        }

        $deliveries["list"] = $list;
        
        return $deliveries; 
    }

    public function earning($post=[]) {
        $earning = array();
        //last trip
        $last_trip1 = $this->db->select("*, COALESCE(tips, 0) as tips", false)
                ->where("user_id", $post["user_id"])
                ->order_by("date", "desc")
                ->limit(1)
                ->get("driver_earnings")->row_array();

        $last_trip = array_map(function($val) {
            if(is_null($val)) {
                $val = "";
            }
            return $val;
        }, $last_trip1);
	
	//get last trip distance & duration
	
	$details = $this->db->select("*")
                ->where("driver_id", $post["user_id"])
                ->where("order_id", $last_trip["order_id"])
                ->order_by("updated_date", "desc")
                ->limit(1)
                ->get("order_driver")->row_array();
	
	//$time = explode(":", $details["duration"]);
	
	$last_trip["distance"] = round($details["distance"]);
	$last_trip["duration"] = $details["duration"];

        $earning["last_trip"] = $last_trip;

        //history according to month
        $list = $this->db->select("COALESCE(sum(amount), 0) as amount", false)
            ->where("status", 1)
            ->where("MONTH(date)", $post["month"])
            ->where("YEAR(date)", $post["year"])
            ->where("user_id", $post["user_id"])
            ->get("driver_earnings")->row_array();
	
        $earning["month_earning_total"] = $list["amount"];
        $earning["month_trip_earning"] = $list["amount"];
        $earning["month_bonus"] = 0;
        $earning["month_tips"] = 0;

        //total delivery
        $total_del = $this->db->select("count(o.order_id) as total_deliveries", false)
                ->join("order_driver od", "od.order_id = o.order_id AND od.status = 1 AND od.driver_id = ".$post["user_id"])
                ->where("o.order_status", 4)
                ->where("MONTH(o.delivered_date)", $post["month"])  
                ->where("YEAR(o.delivered_date)", $post["year"])  
                ->order_by("o.delivered_date", "desc")
                ->get("orders o")->row_array();

        $earning["deliveried"] = (int)$total_del["total_deliveries"];

        //print_r($earning); exit;
        return $earning;
    }

    public function delete_account($post=[]) {
        $check = $this->db->select("*")
                ->where("user_id", $post["user_id"])
                ->where("status", 1)
                ->where("id", $post["main_id"])
                ->get("stripe_connect_accounts")->row_array();

        if(!empty($check)) {
            if($check["is_primary"] == 1) {
                return 2;
            }
            else {
                $this->db->set("status", 0)
                    ->where("user_id", $post["user_id"])
                    ->where("status", 1)
                    ->where("id", $post["main_id"])
                    ->update("stripe_connect_accounts");

                return 1;
            }
        }
        else {
            return 3;
        }
    }
    
    public function make_primary_account($post=[]) {
        $check = $this->db->select("*")
                ->where("user_id", $post["user_id"])
                ->where("status", 1)
                ->where("id", $post["main_id"])
                ->get("stripe_connect_accounts")->row_array();

        if(!empty($check)) {
            //remove other primary account
	    $this->db->set("is_primary", 0)
		->where("user_id", $post["user_id"])
		->where("status", 1)
		->update("stripe_connect_accounts");
	    
	    // make primary account
	    $this->db->set("is_primary", 1)
		->where("user_id", $post["user_id"])
		->where("status", 1)
		->where("id", $post["main_id"])
		->update("stripe_connect_accounts");

	    return 1;
        }
        else {
            return 2;
        }
    }
    
    public function view_requests($post=[]) {
	if($post["type"] == 1) {
	    $list = $this->db->select("*")
		->where("user_id", $post["user_id"])
		->order_by("update_date", "desc")
		->limit($post["limit"])
		->offset($post["offset"])
		->get("driver_requests")->result_array();
	    
	    if(!empty($list)) {
		foreach($list as $key => $values) {
		    $value = array_map(function($val) {
			if(is_null($val)) {
			    $val = "";
			}
			return $val;
		    }, $values);

		    $list[$key] = $value;

		    if(!empty($value["image"])) {
			$img = $value["image"];
			$list[$key]["image"] = $this->s3_url($img);
			//$list[$key]["profile_image_thumb"] = $this->s3_url($img, 'thumb');
		    }	
		    
		    $list[$key]["title"] = "Profile Update Request";
		}
		return $list;
		//print_r($list); exit;
	    }
	    else {
		return false;
	    }
	}	
	else if($post["type"] == 2) {
	    $list = $this->db->select("*")
		->where("user_id", $post["user_id"])
		->order_by("update_date", "desc")
		->limit($post["limit"])
		->offset($post["offset"])
		->get("driver_vehicle_requests")->result_array();
	    
	    if(!empty($list)) {
		foreach($list as $key => $values) {
		    $value = array_map(function($val) {
			if(is_null($val)) {
			    $val = "";
			}
			return $val;
		    }, $values);

		    $list[$key] = $value;
		    
		    //get vehicle image details
		    $list_img = $this->db->select("*")
			    ->where("request_id", $value["request_id"])
			    ->where("vehicle_id", $value["vehicle_id"])
			    ->get("driver_vehicle_image_request")->result_array();
		    
		    if(!empty($list_img)) {
			$vehicle_img = array();
			
			foreach($list_img as $key1 => $value1) {
			    array_push($vehicle_img, $this->s3_url($value1["image_name"], '', 'driver'));
			}
			$list[$key]["vehicle_images"] = $vehicle_img;
		    }
		    else {
			$list[$key]["vehicle_images"] = array();
		    }		    
		    
		    $list[$key]["title"] = "Vehicle Request";
		}
		return $list;
		//print_r($list); exit;
	    }
	    else {
		return false;
	    }
	}
    }
    
    public function wallet($post=[]) {
	$wallet = array();
	
	//amount
	$wallet_amount = $this->db->select("user_id, wallet")
		->where("user_id", $post["user_id"])
		->get("user")->row_array();
	
	$wallet["wallet_amount"] = $wallet_amount["wallet"];
	
	//withdrawn amount
	$details = $this->db->select("*")
		->where("user_id", $post["user_id"])
		->where("type", 2)
		->where("is_withdrawn", 1)
		->order_by("date", "desc")
		->get("wallet_history")->result_array();
	
	if(!empty($details)) {
	    //sum of amount withdrawn    
	    $amount = array_sum(array_column($details, 'debit_credit_amount'));
	    
	    foreach($details as $k => $v) {
		$details[$k]["payment_history"] = json_decode($v["payment_history"]);
	    }
	    
	    $wallet["amount_withdrawn"] = (float)$amount;
	    $wallet["history"] = $details;
	}
	else {
	    $wallet["amount_withdrawn"] = 0;	    
	    $wallet["history"] = array();
	}
	
	return $wallet;
    }
    
    public function withdraw_amount($post=[]) {
	//amount
	$wallet_amount = $this->db->select("user_id, wallet")
		->where("user_id", $post["user_id"])
		->get("user")->row_array();
	
	if(!empty($wallet_amount)) {	
	    if($wallet_amount["wallet"] > $post["amount"]) {
		
		//bank detail
		$bdt = $this->db->select("*")
			->where("user_id", $post["user_id"])
			->where("is_primary", 1)
			->get("stripe_connect_accounts")->row_array();
		
		//print_r($bdt); exit;
		if(!empty($bdt)) {
		//stripe code
		    $config_data = $this->db->where_in('key', array('client_key', 'service_key', 'payment_mode', 'test_public_key', 'test_secret_key'))->get('setting')->result_array();

		    foreach ($config_data as $key => $row) {
			$tmp_arr[$row['key']] = $row['value'];
		    }

		    if($tmp_arr['payment_mode'] == '1') {
			$secret_key = $tmp_arr['test_secret_key'];
		    }
		    else if($tmp_arr['payment_mode'] == '2') {
			$secret_key = $tmp_arr['service_key'];
		    }

		    try{   
			\Stripe\Stripe::setApiKey($secret_key); //secret key

			$response = \Stripe\Transfer::create(array(
			    "amount" => $post['amount']*100,
			    "currency" => CURRENCY,
			    "description" => "amount withdrawn from wallet",
			    "destination" => $bdt["account_id"],
			    "metadata" => array(
				"account_number" => $bdt["account_number"],
				"account_holder_name" => $bdt["account_holder_name"],
				"bank_name" => $bdt["bank_name"]
			    )
			));  

			$account_obj = json_encode($response);
			$account_obj = json_decode($account_obj, true);

			//insert in history
			$balance = $wallet_amount["wallet"] - $post["amount"];

			$ins_history = array(
			    'user_id' => $post["user_id"],
			    'type' => 2,
			    'is_withdrawn' => 1,
			    'debit_credit_amount' => $post["amount"],
			    'balance_amount' => $balance,
			    'note' => "Amount Withdrawn",
			    'payment_status' => 'SUCCESS',
			    'payment_history' => json_encode($response),
			    'transaction_id' => $account_obj["id"]
			);

			$this->db->insert("wallet_history", $ins_history);

			//update wallet amount
			$this->db->set("wallet", $balance)
			    ->where("user_id", $post["user_id"])
			    ->update("user");

			//strip transfer history
			$ins_history2 = array(
			    'user_id' => $post["user_id"],
			    'type' => 2,
			    'amount' => $post["amount"],
			    "destination" => $bdt["account_id"],
			    "source_transaction" => $account_obj["id"],
			    'payment_status' => 'SUCCESS',
			    'payment_history' => json_encode($response),
			    'transaction_id' => $account_obj["id"]
			);

			$this->db->insert("stripe_transfer_transaction", $ins_history2);

			return 1;
		    } 
		    catch (Exception $e) {
			$response = $e->getError(); 
			$account_obj = json_encode($response);
			$account_obj = json_decode($account_obj, true);
		       // print_r($response); exit;
			return $account_obj["message"];
		    }	
		}
		else {
		    return 'No account found.';
		}
	    }
	    else {
		return 2;
	    }
	}
	else {
	    return 3;
	}
    }
    
    public function schedule_list($post=[]) {
	$data = $this->db->select("*")
		->where("driver_id", $post["user_id"])
		->where("status", 1)
		->where("MONTH(schedule_date)", $post["month"])  
                ->where("YEAR(schedule_date)", $post["year"])  
		->get("driver_schedule")->result_array();
	
	if(!empty($data)) {
	    return $data;
	}                
    }
    
    public function view_request_by_id($post=[]) {
	if($post["type"] == 1) {
	    $lists = $this->db->select("*")
		->where("request_id", $post["request_id"])
		->get("driver_requests")->row_array();
	    
	    if(!empty($lists)) {		
		$list = array_map(function($val) {
		    if(is_null($val)) {
			$val = "";
		    }
		    return $val;
		}, $lists);

		if(!empty($list["image"])) {
		    $img = $list["image"];
		    $list["image"] = $this->s3_url($img, '', 'driver');
		    //$list[$key]["profile_image_thumb"] = $this->s3_url($img, 'thumb');
		}			    
		return $list;
	    }
	    else {
		return false;
	    }
	}	
	else if($post["type"] == 2) {
	    $lists = $this->db->select("*")
		->where("request_id", $post["request_id"])
		->get("driver_vehicle_requests")->row_array();
	    
	    if(!empty($lists)) {
		
		$list = array_map(function($val) {
		    if(is_null($val)) {
			$val = "";
		    }
		    return $val;
		}, $lists);

		//get vehicle image details
		$list_img = $this->db->select("*")
			->where("request_id", $list["request_id"])
			->where("vehicle_id", $list["vehicle_id"])
			->get("driver_vehicle_image_request")->result_array();

		if(!empty($list_img)) {
		    $vehicle_img = array();

		    foreach($list_img as $key1 => $value1) {
			array_push($vehicle_img, $this->s3_url($value1["image_name"], '', 'driver'));
		    }
		    $list["vehicle_images"] = $vehicle_img;
		}
		else {
		    $list["vehicle_images"] = array();
		}		    
		
		return $list;
	    }
	    else {
		return false;
	    }
	}
    }
    
    public function cancel_target($post=[]) {
	if($post["target_type"] == '1') {
	    $this->db->where("target_start_date", date('Y-m-d'));
	}
	else if($post["target_type"] == '2') {
	    $this->db->where("( target_start_date <= '".date('Y-m-d')."' AND target_end_date >= '".date('Y-m-d')."')");
	}
	else if($post["target_type"] == '3') {
	    $this->db->where("( target_start_date <= '".date('Y-m-d')."' AND target_end_date >= '".date('Y-m-d')."')");
	}	
	
	$check = $this->db->select("*")
		->where("user_id", $post["user_id"])
		->where("target_type", $post["target_type"])	
		->where("status", 1)
		->limit(1)
		->order_by("date", "desc")
		->get('driver_target')->row_array();
	
	if(!empty($check)) {
        $this->db->set("status", 0)
        ->where("target_id", $check["target_id"])
        ->update("driver_target");

	    return true;
	}
	else {
	    return false;
	}
    }
    
    public function add_web_notification($user_id, $notification_type, $message, $request_id){    
	$insert_arr = array(
	    'notification_type' => $notification_type,
	    'message' => $message,
	    'user_id' => $user_id,
	    'request_id' => $request_id
	);
	$this->db->insert('website_notification', $insert_arr);
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
	$last_send_id = $this->db->insert_id();
	
	//insert received message
	$received_arr = array_merge($insert_arr, $received_arr);
	$this->db->insert("chat", $received_arr);
	$last_receive_id = $this->db->insert_id();
	
	$push1 = array(
	    "to_user_id" => $post["to_user_id"],
	    "notification_type" => 25,
	    "message" => $post["message"],
	    "driver_id" => $post["user_id"],
	    "customer_id" => $post["to_user_id"],
	    "order_id" => $post["order_id"],
	);
	
	$this->save_offline_data(array(
	    "user_id" => $post["to_user_id"],
	    "json" => json_encode($received_arr)
	));
	
	$this->create_notification($push1);
	
	//get customer name & contact no
	$get_ltlg = $this->user_info($post["to_user_id"]);
	$push1["name"] = $get_ltlg["firstname"]." ".$get_ltlg["lastname"];
	$push1["contact_no"] = trim($get_ltlg["mobileno"]);
	
	//print_r($push1);
	
	$this->m_notify->send($push1);
	
	$resp = $this->db->select("*")
		->where("message_id", $umsgid)
		->where("type", "sent")
		->order_by('chat_id', 'desc')
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
                
                //$status = $this->get_blocked_status($post["user_id"], $cvalue["user_id"]);
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
                        //echo $cvalue["type"];
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
    
    public function get_message2($chat_id){
        $response = $this->db->select("*")->where("chat_id", $chat_id)->get("chat")->row_array();
        if(!empty($response)){
            $user_id = $response["user_id"];
            $to_user_id = $response["to_user_id"];

            if($response["type"] == "received"){
                $user_id = $response["to_user_id"];
                $to_user_id = $response["user_id"];
            }
            
//            $msg = str_replace(PHP_EOL,"@/@", $response["message"]);
//            $response["message"] = json_decode('"'.$msg.'"');
//            $response["message"] = str_replace("@/@",PHP_EOL, $response["message"]);
            
//            $status = $this->get_blocked_status($user_id, $to_user_id);
//            if($status=="" || $status=="blocked_by_me"){
//                if($status=="blocked_by_me"){
//                    $response["blocked"] = "1";
//                }else{
//                    $response["blocked"] = "0";
//                }
//            }
            
            $response["sender"] = $this->get_user_details($user_id, $to_user_id);
            $response["receiver"] = $this->get_user_details($to_user_id, $user_id);
            
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
            
//            $status = $this->get_blocked_status($user_id, $second_user);
//            if($status=="blocked_by_me"){
//                $response["i_hve_blocked"] = "1";
//                $response["is_blocked_me"] = "0";
//            }
//            else if($status=="blocked_by_you"){
//                $response["i_hve_blocked"] = "0";
//                $response["is_blocked_me"] = "1";
//            }
//            else if($status=="" ){               
//                $response["i_hve_blocked"] = "0";
//                $response["is_blocked_me"] = "0";               
//            }
//            else if($status=="blocked_each_other"){                
//                $response["i_hve_blocked"] = "1";
//                $response["is_blocked_me"] = "1";                
//            }            
            //$response["i_hve_blocked"] = "0";
            //$response["is_blocked_me"] = "0";
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
    
    function user_info($user_id) {
	$get_ltlg = $this->db->select("user.user_id, user.firstname, user.lastname, user.mobileno, user.country_code")
		->where("user.user_id", $user_id)
		->where("user.status", 1)
		->get("user")->row_array();
	
	return $get_ltlg;
    }
    
    function get_seller($seller_id) {
	$seller = $this->db->select("seller_id, seller_name, dzone_id, latitude, longitude, postalcode, timezone, company_name, contact_no, address")->where("seller_id", $seller_id)->get("seller")->row_array();
	date_default_timezone_set(trim($seller["timezone"]));
	return $seller;
    }
    
} 
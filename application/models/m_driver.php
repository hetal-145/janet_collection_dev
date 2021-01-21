<?php

require 'vendor_aws/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class M_driver extends CI_Model{
    
    public function user_profile($user_id = '') {
        $user_data = $this->db->select('*, concat(firstname," ",lastname) as name', false)
                ->where('u.user_id', $user_id)
                ->get('user u')
                ->row_array();
        
        if(!empty($user_data["verification_doc"]) && $user_data["is_admin_verified"] == 1) {
            $user_data["verification_doc"] = $this->m_tools->image_url( $user_data['verification_doc'],'', 'verification_docs'); 
            $doc_uploaded = true;
            $msg = "";
        } 
        else if(!empty($user_data["verification_doc"]) && $user_data["is_admin_verified"] == 0) {
            $user_data["verification_doc"] = $this->m_tools->image_url( $user_data['verification_doc'],'', 'verification_docs'); 
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
    
    public function add_driver($post = [], $files = []) {
        //print_r($post); print_r($files); exit;
        $this->load->model('m_tools');
        //remove blank values
        $docs = array_map('array_filter', $files["docs"]);
        $_FILES["docs"] = array_filter($docs);
        $vehicle_imgs = array_map('array_filter', $files["vehicle_imgs"]);
        $_FILES["vehicle_imgs"] = array_filter($vehicle_imgs);
	$_FILES["profile_image"] = $files["profile_image"];
        unset($files);
	
	///bucket info
	$credentials = new Aws\Credentials\Credentials(PUBLISHED_KEY, SECRET_KEY);
	$s3 = new S3Client([        
	    'region' => 'eu-west-2',
	    'version' => '2006-03-01',
	    //'debug' => true,
	    "credentials" => $credentials
	]);
                
        //check if driver exists
        $check = $this->db->select("email")
                ->where("email", $post["email"])
                ->where("user_type", 2)
                ->get("user")->row_array();
        
        if(empty($check)) {     
            $name = explode(" ", $post["name"]);
            
            if(isset($name[1]) && !empty($name[1])) {
                $firstname = $name[0];
                $lastname = $name[1];
            }
            else {
                $firstname = $name[0];
                $lastname = ""; 
            }

            $password = substr(md5(rand()), 1, 8);
	    
	    if(!empty($_FILES["profile_image"]['name'])) {
		$ext_arr = array('gif', 'jpg', 'png', 'jpeg');
		$ext = '.' . pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
		$ext1 = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
		$filename = date('YmdHis') . rand() . strtolower($ext);
		$keyname = $filename;
		$filepath = $_FILES['profile_image']['tmp_name'];
		if( in_array(strtolower($ext1), $ext_arr) ) {
		    $result = $s3->putObject(array(
			'Bucket' => BUCKET_NAME,
			'Key' => 'driver/'.$keyname,
			'SourceFile' => $filepath,
			'ACL' => 'public-read',
			'StorageClass' => 'STANDARD'
		    ));

		    $profile_image = $filename;
		}
		else {
		    return 'The filtype you are trying to upload is not allowed 1';
		}
	    }
	    else {
		$profile_image = "";
	    }
	    
	    $code = $this->m_tools->generate_alphanumeric_code();
	    $post["birthdate"] = str_replace('/', '-', $post["birthdate"]);
            //Insert driver
            $ins_post = array(
                "firstname" => $firstname,
                "lastname" => $lastname,
                "email" => $post["email"],
                "mobileno" => $post["mobileno"],
		"country_code" => $post["country_code"],
                "userno" => rand(0000000, 9999999),
                "user_type" => 2,
                "password" => sha1($password),
		"profile_image" => $profile_image,
		"driver_unique_code" => $code,
		"account_number" => $post["account_number"],
                "bank_name" => $post["bank_name"],
		"name_of_card" => $post["name_of_card"],
		"routing_no" => $post["routing_no"],
		"city" => $post["city"],
		"address" => $post["address"],
		"postalcode" => $post["postalcode"],
		"birthdate" => date('Y-m-d', strtotime($post["birthdate"]))
            );
            
            $this->db->insert("user", $ins_post);
            $last_id = $this->db->insert_id();
	    
	    if(!empty($post["refrence_code"])) {
		//add reference
		$this->db->insert("driver_by_invite", array(
		    "driver_id" => $last_id,
		    "code" => $post["refrence_code"]
		));
	    }
	    
            //upload driver documents            
	    if(!empty($_FILES["docs"]["name"])) {
		$ext_arr1 = array('gif','jpg','png', 'jpeg', 'pdf');

		$c2 = 0;	    
		foreach ($_FILES['docs']['name'] as $key => $file) {
		    $f = $_FILES['docs']['name'][$key];
		    $ext = '.' . pathinfo($f, PATHINFO_EXTENSION);
		    $ext1 = pathinfo($f, PATHINFO_EXTENSION);
		    //echo $ext1;
		    if( in_array(strtolower($ext1), $ext_arr1) ) {
			$filename = date('YmdHis') . rand() . strtolower($ext);
			$keyname = $filename;
			$filepath = $_FILES['docs']['tmp_name'][$key];

			$result = $s3->putObject(array(
			    'Bucket' => BUCKET_NAME,
			    'Key' => 'driver/'.$keyname,
			    'SourceFile' => $filepath,
			    'ACL' => 'public-read',
			    'StorageClass' => 'STANDARD'
			));

			$dri_arr = array(
			    'user_id' => $last_id,
			    "image_name" => $filename
			);                        
			$this->db->insert("driver_docs", $dri_arr); 
		    }
		    else {
			$c2++;
		    }
		}

		if($c2 > 0 && $c2 <= count($_FILES['docs']['name'])) {		
		    return 'The filtype you are trying to upload is not allowed 2';
		}
	    }

//	    if(!empty($_FILES["docs"]['name'])) {	
//                foreach ($_FILES["docs"]['name'] as $key => $file) {
//                   $f = $_FILES["docs"]['name'][$key];
//                   $ext = '.' . pathinfo($f, PATHINFO_EXTENSION);
//
//                   $filenames[] = date('YmdHis') . rand() . $last_id . strtolower($ext);
//                }
//                $config = [
//                    'upload_path' => 'upload/driver/',
//                    'allowed_types' => 'gif|jpg|png|jpeg|pdf',
//                    'file_name' => $filenames
//                ];
//                $this->load->library('upload', $config);
//                $this->upload->initialize($config);
//                
//                if ($this->upload->do_multi_upload("docs")) {
//                    ///insert image data
//                    $i = 1;
//                    foreach ($filenames as $file) {    
//                        $dri_arr = array(
//                            'user_id' => $last_id,
//                            "image_name" => $file
//                        );                        
//                        $this->db->insert("driver_docs", $dri_arr);  
//                    }
//                }
//                else {
//                    return $this->upload->display_errors();
//                }  
//            }
            
            //Insert vehicle details
            $ins_driver_post = array(
                "driver_id" => $last_id,
                "maker" => $post["maker"],
                "model" => $post["model"],
                "registration_number" => $post["registration_number"],
                "vehicle_info" => $post["vehicle_info"],
            );
            
            $this->db->insert("vehicle_mst", $ins_driver_post);
            $vehicle_id = $this->db->insert_id();
            
            //upload vehicle image
	    if(!empty($_FILES["vehicle_imgs"]["name"])) {
		$ext_arr1 = array('gif','jpg','png', 'jpeg');

		$c2 = 0;	    
		foreach ($_FILES['vehicle_imgs']['name'] as $key => $file) {
		    $f = $_FILES['vehicle_imgs']['name'][$key];
		    $ext = '.' . pathinfo($f, PATHINFO_EXTENSION);
		    $ext1 = pathinfo($f, PATHINFO_EXTENSION);
		    if( in_array(strtolower($ext1), $ext_arr1) ) {
			$filename = date('YmdHis') . rand() . strtolower($ext);
			$keyname = $filename;
			$filepath = $_FILES['vehicle_imgs']['tmp_name'][$key];

			$result = $s3->putObject(array(
			    'Bucket' => BUCKET_NAME,
			    'Key' => 'driver/'.$keyname,
			    'SourceFile' => $filepath,
			    'ACL' => 'public-read',
			    'StorageClass' => 'STANDARD'
			));

			$veh_arr = array(
                            "vehicle_id" => $vehicle_id,
                            "image_name" => $filename
                        );                        
                        $this->db->insert("vehicle_images", $veh_arr);    
		    }
		    else {
			$c2++;
		    }
		}

		if($c2 > 0 && $c2 <= count($_FILES['vehicle_imgs']['name'])) {		
		    return 'The filtype you are trying to upload is not allowed 3';
		}
	    }
	    
//            if(!empty($_FILES["vehicle_imgs"]['name'])) {
//                foreach ($_FILES["vehicle_imgs"]['name'] as $key => $file) {
//                    if(!empty($file)) {
//                        $f = $_FILES["vehicle_imgs"]['name'][$key];
//                        $ext = '.' . pathinfo($f, PATHINFO_EXTENSION);
//
//                        $filenames[] = date('YmdHis') . rand() . $last_id . strtolower($ext);
//                    }
//                }
//                $config = [
//                    'upload_path' => 'upload/driver/',
//                    'allowed_types' => 'gif|jpg|png|jpeg',
//                    'file_name' => $filenames
//                ];
//                $this->load->library('upload', $config);
//                $this->upload->initialize($config);
//                if ($this->upload->do_multi_upload("vehicle_imgs")) {
//                    ///insert image data
//                    foreach ($filenames as $file) { 
//                        $veh_arr = array(
//                            "vehicle_id" => $vehicle_id,
//                            "image_name" => $file
//                        );                        
//                        $this->db->insert("vehicle_images", $veh_arr);                                
//                    }
//                }
//                else {
//                    return $this->upload->display_errors();
//                }  
//            }    
            
            //send mail to driver
            $post["user_id"] = $last_id;
            $post["password"] = $password;
            $to = $post['email'];
            $subject = 'Welcome To Janet-Collection';
            $msg = $this->load->view('mail_tmp/header', $post, true);
            $msg .= $this->load->view('mail_tmp/welcome', $post, true);
            $msg .= $this->load->view('mail_tmp/footer', $post, true);
            $this->m_tools->send_mail($to, $subject, $msg);
	    
	    //send mail to administrator
	    $admin_email = $this->db->select("*")->where("key", "admin_email_address")->get("setting")->row_array();
	    $to1 = $admin_email['value'];
            $subject1 = 'A New Signup for Driver in Janet-Collection';
            $msg1 = $this->load->view('mail_tmp/header', $post, true);
            $msg1 .= $this->load->view('mail_tmp/verified', $post, true);
            $msg1 .= $this->load->view('mail_tmp/footer', $post, true);
            $this->m_tools->send_mail($to1, $subject1, $msg1);
            
            return 'success';
        }
        else {
            return 'exist';
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
}



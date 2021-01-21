<?php

//require 'vendor_google/autoload.php';

class M_login extends CI_Model {
    
    function signin_social_facebook($post=[]) {
	//print_r($post); exit;
	$id = base64_decode($post["encode_id"]);
	$fname = base64_decode($post["encode_firstname"]);
	$lname = base64_decode($post["encode_lastname"]);
	$email = base64_decode($post["encode_email"]);
	
//	print_r($id); 
//	print_r($fname); 
//	print_r($lname); 
//	print_r($email); exit;
	
	if(!empty($email)) {
	    $check = $this->db->select("user_id, firstname, lastname, email")
		    ->where("email", $email)
		    ->get("user")->row_array();
	    
	    if(!empty($check)) {
		if(empty($check["firstname"]) || empty($check["lastname"])) {
		    $firstname = $fname;
		    $lastname = $lname;
		}
		else {
		    $firstname = $check["firstname"];
		    $lastname = $check["lastname"];
		}
		
		$this->db->set("social_id", $id)
			->set("social_type", "facebook")
			->set("firstname", $firstname)
			->set("lastname", $lastname)
                        ->set("is_admin_verified", 1)
			->where("user_id", $check["user_id"])
			->update("user");
		
		$last_id = $check["user_id"];
	    }
	    else {
		$this->db->insert("user", array(
		    "social_id" => $id,
		    "social_type" => "facebook",
		    "firstname" => $fname,
		    "lastname" => $lname,
		    "email" => $email,
                    "is_admin_verified" => 1,
		    "status" => 1
		));
		
		$last_id = $this->db->insert_id();
	    }
	}
	else {
	    $this->db->insert("user", array(
		"social_id" => $id,
		"social_type" => "facebook",
		"firstname" => $fname,
		"lastname" => $lname,
		"is_admin_verified" => 1,
		"status" => 1
	    ));
	    
	    $last_id = $this->db->insert_id();
	}
	
	return $last_id;
    }
    
    function signin_social_google($post=[]) {
	//print_r($post); exit;
	$id = base64_decode($post["encode_id"]);
	$fname = base64_decode($post["encode_firstname"]);
	$lname = base64_decode($post["encode_lastname"]);
	$email = base64_decode($post["encode_email"]);
	
	if(!empty($email)) {
	    $check = $this->db->select("user_id, firstname, lastname, email")
		    ->where("email", $email)
		    ->get("user")->row_array();
	    
	    if(!empty($check)) {
		if(empty($check["firstname"]) || empty($check["lastname"])) {
		    $firstname = $fname;
		    $lastname = $lname;
		}
		else {
		    $firstname = $check["firstname"];
		    $lastname = $check["lastname"];
		}
		
		$this->db->set("social_id", $id)
			->set("social_type", "google")
			->set("firstname", $firstname)
			->set("lastname", $lastname)
                        ->set("is_admin_verified", 1)
			->where("user_id", $check["user_id"])
			->update("user");
		
		$last_id = $check["user_id"];
	    }
	    else {
		$this->db->insert("user", array(
		    "social_id" => $id,
		    "social_type" => "google",
		    "firstname" => $fname,
		    "lastname" => $lname,
		    "email" => $email,
		    "is_admin_verified" => 1,
		    "status" => 1
		));
		
		$last_id = $this->db->insert_id();
	    }
	}
	else {
	    $this->db->insert("user", array(
		"social_id" => $id,
		"social_type" => "google",
		"firstname" => $fname,
		"lastname" => $lname,
		"is_admin_verified" => 1,
		"status" => 1
	    ));
	    
	    $last_id = $this->db->insert_id();
	}
	
	return $last_id;
    }
    
    public function change_password($post = []) {

        $post['password'] = sha1($post['password']);
        $post['new_password'] = sha1($post['new_password']);

        $check_password = $this->db->select('*')
                ->where('password', $post['password'])
                ->where('user_id', $post['user_id'])
                ->get('user')
                ->row_array();
        if ($check_password) {
            $change_pass = $this->db->set('password', $post['new_password'])
                    ->where('user_id', $post['user_id'])
                    ->update('user');
            $resp = 'update';
        } else {
            $resp = 'not_match';
        }
        return $resp;
    }

    public function check_login($post = []) {
        $password = sha1($post['password']);        
        if(isset($post["email"]) && !empty($post["email"])) {
            $userdata_email = $this->db
                    ->where('email', $post["email"])
                    ->where('password', $password)
                    ->limit(1)
                    ->from('user')->get()->row_array();   
            
            if(!empty($userdata_email)){
                $userdata = $userdata_email;
            }
        }        
        else if(isset($post["mobileno"]) && !empty($post["mobileno"])) {
            // for mobileno
            $userdata_mobileno = $this->db
                ->where('mobileno', $post["mobileno"])
                ->where('password', $password)
                ->limit(1)
                ->from('user')->get()->row_array(); 
        
            $userdata = $userdata_mobileno;
        }        
        if (!empty($userdata)) {
            return $userdata;
        }
    }
    
    public function user_register($post = []) {
        //print_r($post); exit;
        $CI =& get_instance();
        $CI->load->model('m_tools');
        $post["token"] = md5(rand() . rand());
        $post['password'] = sha1($post['password']);
        $post["userno"] = rand(0000000, 9999999);        
        $birth_stamp = strtotime('+18 years', strtotime($post["birthdate"]));
        $current_stamp = strtotime('now');

        if($current_stamp < $birth_stamp) {
            return 6;
        }
        
        $userdata = $CI->m_tools->get_user_by_email($post["email"]);
        if(empty($userdata)) {        
            unset($post["cnf_password"]);
            $insert = $this->db->insert('user', $post);
            if($insert) {
                return 1;
            }
            else {
                return 5;
            }
        }
        else if(!empty($userdata)) {
            if($userdata["status"] == 1) {
                return 2;
            }
            else {
                if($userdata["is_admin_verified"] == 0) {
                    return 3;
                }
                else {                    
                    //$this->db->set($post)->where("user_id", $userdata["user_id"])->update("user");
                    return 4;
                }
            }
        }
    }

    function check_session($index = '') {
        if ($index == 1) {
            if ($this->session->userdata('loged_in')) {
                redirect(base_url('home'));
            }
        } else {
            if (!$this->session->userdata('loged_in')) {
                redirect(base_url('home'));
            }
        }
    }

    public function user_details($user_id) {
        $user_info = $this->db->select("user_id, email, password")
                ->where('user_id', $user_id)
                ->get('user')
                ->row_array();
        return $user_info;
    }
    
    public function forgot_password($post = []) {
        $userdata = $this->db->select("user_id")
                ->where('email', $post["contact_email"])
                ->limit(1)
                ->from('user')->get()->row_array();  
        
        if(!empty($userdata)) {
            $password = substr(md5(rand()), 1, 8);
            $this->db->where(array(
                'user_id' => $userdata["user_id"],
                'status' => 1
            ));
            $this->db->set('password', sha1($password));
            $this->db->set('password_updated', 0);
            $this->db->update('user');

            $return_arr = array(
                'user_id' => $userdata["user_id"],
                'email' => $post["contact_email"],
                'password' => $password
            );
            return $return_arr;
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
}

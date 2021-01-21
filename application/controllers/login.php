<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('m_login');
        $this->load->model('m_tools');
    }

    public function index() {
        //$this->load->view('login');
    }

    public function user_login() {
        $post = $_POST;
        $userdata = $this->m_login->check_login($post);
        if ($userdata) {
	    if (!empty($userdata["profile_image"])) {
		$statss = $this->m_tools->get_http_response_code(S3_PATH . $userdata["profile_image"]);		    
		if($statss == '200') {
		    $uimg = S3_PATH . $userdata["profile_image"];
		}
		else if($statss == '404') {
		    $uimg = base_url() . "assets/website/img/avtar.png";
		}

	    } else {
		$uimg = base_url() . "assets/website/img/avtar.png";
	    }
	    
	    if (!empty($userdata["firstname"]) && !empty($userdata["lastname"])) {
		$uname = $userdata['firstname'].' '.$userdata['lastname'];
	    } 
	    else  if (!empty($userdata["firstname"]) && empty($userdata["lastname"])) {
		$uname = $userdata['firstname'];
	    } 
	    else  if (empty($userdata["firstname"]) && !empty($userdata["lastname"])) {
		$uname = $userdata['lastname'];
	    } 
	    else {
		$uname = 'User';
	    }
			
            $session = [
                'loged_in' => true,
                'user_id' => $userdata['user_id'],
                'user_name' => $uname,
		'userno' => $userdata['userno'],
		'user_type' => $userdata['user_type'],
		'profile_image' => $uimg,
            ];
            $this->session->set_userdata($session);
            echo 1;
        } else {
            echo 0;
        }
    }
    
    public function forgot_password() {
        $arr["email"] = $_POST["contact_email"];
	$response = $this->m_tools->use_api3('forgot_password', $arr);
	echo json_encode($response);
    }
    
    public function user_register() {
        $post = $_POST;
	//print_r($post); exit;
	
	if(!empty($post["type"]) && $post["type"] == "1") {
	    $response = $this->m_login->signin_social_facebook($post);
	    $userdata = $this->m_tools->get_user_by_id($response);
	    if($userdata["is_admin_verified"] == '1') {
		$session = [
		    'loged_in' => true,
		    'user_id' => $userdata['user_id'],
		    'user_name' => $userdata['firstname'].' '.$userdata['lastname']
		];
		$this->session->set_userdata($session);
		echo 1;
	    }
	    else {
		echo 2;
	    }
	}
	else if(!empty($post["type"]) && $post["type"] == "2") {
	    $response = $this->m_login->signin_social_google($post);
	    $userdata = $this->m_tools->get_user_by_id($response);
	    if($userdata["is_admin_verified"] == '1') {
		$session = [
		    'loged_in' => true,
		    'user_id' => $userdata['user_id'],
		    'user_name' => $userdata['firstname'].' '.$userdata['lastname']
		];
		$this->session->set_userdata($session);
		echo 1;
	    }
	    else {
		echo 2;
	    }
	}
	else {
	    $response = $this->m_login->user_register($post);
	    if ($response === 1) {

		$userdata = $this->m_tools->get_user_by_email($post["email"]);
		$to = $userdata['email'];
		$userdata["password"] = $post['password'];
		$subject = 'Welcome To Janet-Collection';
		$msg = $this->load->view('mail_tmp/header', $userdata, true);
		$msg .= $this->load->view('mail_tmp/welcome', $userdata, true);
		$msg .= $this->load->view('mail_tmp/footer', $userdata, true);
		$this->m_tools->send_mail($to, $subject, $msg);
		echo 1;
	    } else {
		echo $response;
	    }
	}
    }
    
    public function change_password() {
	$arr["password"] = $_POST["cnf_password"];
        $response = $this->m_tools->use_api('change_password', $arr);
        echo json_encode($response);
    }
    
    public function logout() {
        $this->session->sess_destroy();
        redirect(base_url('home'), 'refresh');
    }
    
    public function verify($md5_user_id) {
        $status = $this->m_login->verify($md5_user_id);
        if ($status) {
            if ($status == '1') {
                echo '<h1><font color="green">Your Janet-Collection is account verified.</h1>';
            } else if ($status == '2') {
                echo '<h1><font color="green">User already verified.</h1>';
            }
        } else {
            echo '<h1><font color="red">User not registered.</h1>';
        }
    }
}

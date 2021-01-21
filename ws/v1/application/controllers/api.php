<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('m_api');
        $this->load->model('m_notify');
        $this->load->model('m_notifyd');
        $this->response = new stdClass();
        ini_set('display_errors', '1');
        //$this->output->set_header('Authorization: 272cee7490ddfdf72b9ce9a989efcdd0',true);
        if (isset($_REQUEST) && $_REQUEST) {
            log_message('error', $this->uri->uri_string() . ' /// request ---> ' . json_encode($_REQUEST));
        }
        if (isset($_FILES) && $_FILES) {
            log_message('error', $this->uri->uri_string() . ' /// files ---> ' . json_encode($_FILES));
        }
        $headers = $this->input->request_headers();

        if (isset($headers['Authorization']) && $headers['Authorization']) {
            log_message('error', $this->uri->uri_string() . ' /// header token ---> ' . json_encode($headers['Authorization']));
        }
    }

    public function index() {
        echo 'It\'s working...';
    }

    public function check_auth() {
        $headers = $this->input->request_headers();

        if (!isset($headers['Authorization']) || $headers['Authorization'] == null) {
            //header token not setchange_password

            $this->response = array(
                'status' => 'false',
                'response_msg' => 'User authentication failed. Token not set.',
            );
            echo json_encode($this->response);
            return false;
        } else {
            return $headers['Authorization'];
        }
    }

    public function check_auth_user_id() {
        if (!isset($_POST['user_id']) || $_POST['user_id'] == null) {
            //header token not set
            $this->response = array(
                'status' => 'false',
                'response_msg' => 'User authentication failed. User ID not set.',
            );
            echo json_encode($this->response);
            return false;
        } else {
            return $_POST['user_id'];
        }
    }

    public function validate_token($user_id, $token) {
        $userdata = $this->m_api->get_user_by_user_id_token($user_id, $token);
        if ($userdata) {
            return $userdata;
        } else {
            $this->response = array(
                'status' => 'false',
                'screen_code' => '1001',
                'response_msg' => 'User authentication failed. Token mismatch.',
            );
            echo json_encode($this->response);
            return false;
        }
    }

    public function auth() {
        $token = $this->check_auth();

        if ($token) {
            $user_id = $this->check_auth_user_id();

            if ($user_id) {
                $userdata = $this->validate_token($user_id, $token);
                if ($userdata) {
                    return $userdata;
                }
            }
        }
    }

    public function check_parameters($paras = array(), $msg = '') {
        $return = TRUE;
        $not_set = '';

        foreach ($paras as $para) {

            if (!isset($_POST[$para]) || $_POST[$para] == NULL) {
                $return = FALSE;
                if ($not_set != '') {
                    $not_set .= ', ';
                }
                $not_set .= $para;
            }
        }

        if (!$return) {
            log_message('error', 'Parameters not set. ---> ' . $not_set);
            if ($msg) {
                $this->response = array(
                    'status' => 'false',
                    'response_msg' => $msg
                        //'response_msg' => 'Parameters not set. ---> ' . $not_set,
                );
            } else {
                $this->response = array(
                    'status' => 'false',
                    'response_msg' => 'Please fill required fields.'
                        //'response_msg' => 'Parameters not set. ---> ' . $not_set,
                );
            }
            echo json_encode($this->response);
        }
        return $return;
    }

    public function display_system_error() {
        $this->response = array(
            'status' => 'false',
            'response_msg' => 'Server error. Something went wrong.',
        );
        echo json_encode($this->response);
        die;
    }

    public function signup() {
        header('Content-Type: application/json');
        $post = $_POST;
        $required = ['firstname', 'lastname', 'birthdate', 'mobileno', 'email', 'password'];

        if ($this->check_parameters($required)) {

            if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
                $this->response = array(
                    'status' => 'false',
                    'response_msg' => 'Invalid email address',
                );
                echo json_encode($this->response);
                exit;
            }
            $userdata = $this->m_api->get_user_by_email($post['email']);
            if ($userdata) {
                if ($userdata['status'] == '1') {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Email already exists'
                    );
                    echo json_encode($this->response);
                } else {

                    //print_r($userdata); exit;
                    $post['user_id'] = $userdata['user_id'];
                    $update_data = ['user_id', 'firstname', 'lastname', 'birthdate', 'mobileno', 'email', 'password'];
                    $userdata = $this->m_api->update_user(elements($update_data, $post));
                    // $this->m_api->signup_organization($post);
                    $this->m_api->log_in($userdata['user_id']);
                    if ($userdata['token'] != '') {
                        $token = $userdata['token'];
                    } else {
                        $token = md5(rand() . rand());
                    }
                    $this->m_api->update_login_token($post['user_id'], $token);
    //token is valid               
                    $this->m_api->check_update_device_token($post);

                    $screen_code = $this->m_api->check_profile_complition_and_get_screen_code($post['user_id']);
                    if ($userdata['password_updated'] == 0) {
                        //111 display update password screen
                        $screen_code = '111';
                    }
                    
                    $admin_data = $this->db->where('key', 'admin_email_address')->get('setting')->row_array();
                    $to = $admin_data['value'];
                    $subject = 'A New Signup in Drinxin';
                    $msg = $this->load->view('mail_tmp/header', $admin_data, true);
                    $msg .= $this->load->view('mail_tmp/age_verification_notice', $admin_data, true);
                    $msg .= $this->load->view('mail_tmp/footer', $admin_data, true);
                    $this->m_api->send_mail($to, $subject, $msg);
                    
                    if(!empty($userdata['verification_doc'])) {
                        $doc_uploaded = true;
                    } else {
                        $doc_uploaded = false;
                    }

                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Already registered.',
                        'screen_code' => '333',
                        'user_id' => $userdata['user_id'],
                        'firstname' => $userdata['firstname'],
                        'lastname' => $userdata['lastname'],
                        'token' => $token,
                        'email' => $userdata['email'],
                        'doc_uploaded' => $doc_uploaded,
                        'is_admin_verified' => $userdata['is_admin_verified'],
                    );
                    echo json_encode($this->response);
                }
            } else {

                $signup_data = ['firstname', 'lastname', 'birthdate', 'mobileno', 'email', 'password'];
                $signup_data = elements($signup_data, $post);
                $userdata = $this->m_api->signup($signup_data);
                if ($userdata) {
                    $post['user_id'] = $userdata['user_id'];
                    
                    $this->m_api->log_in($userdata['user_id']);
                    
                    //$this->m_api->generate_random_verification_code($userdata['user_id']);
                    if ($userdata['token'] != '') {
                        $token = $userdata['token'];
                    } else {
                        $token = md5(rand() . rand());
                    }
                    $this->m_api->update_login_token($post['user_id'], $token);
    //token is valid               
                    $this->m_api->check_update_device_token($post);


                    $admin_data = $this->db->where('key', 'admin_email_address')->get('setting')->row_array();
                    //print_r($admin_data); exit;
                    $to = $admin_data['value'];
                    $subject = 'A New Signup in Drinxin';
                    $msg = $this->load->view('mail_tmp/header', $admin_data, true);
                    $msg .= $this->load->view('mail_tmp/age_verification_notice', $admin_data, true);
                    $msg .= $this->load->view('mail_tmp/footer', $admin_data, true);
                    $this->m_api->send_mail($to, $subject, $msg);
                    
                    if(!empty($userdata['verification_doc'])) {
                        $doc_uploaded = true;
                    } else {
                        $doc_uploaded = false;
                    }

                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Signup successfull',
                        'screen_code' => '222',
                        'user_id' => $userdata['user_id'],
                        'firstname' => $userdata['firstname'],
                        'lastname' => $userdata['lastname'],
                        'token' => $token,
                        'email' => $userdata['email'],
                        'doc_uploaded' => $doc_uploaded,
                        'is_admin_verified' => $userdata['is_admin_verified'],
                    );

                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'System error. User registration failed.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function update_device_token() {
        //$user_id, $device_type, $device_token, $device_id = '', $device_name = '', app_version = ''
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'device_type', 'device_token', 'app_version'];
            if ($this->check_parameters($required)) {
//check and update device token
                $this->m_api->check_update_device_token($post);
                $this->response = array(
                    'status' => 'true',
                    'response_msg' => 'Device token updated.',
                );
                echo json_encode($this->response);
            }
        }
    }
    
    public function upload_verification_doc() {
       
        header('Content-Type: application/json');
        $post = $_POST;
        $files = $_FILES;
        $required = ['user_id'];

        if ($this->check_parameters($required)) {            
            $res = $this->m_api->upload_verification_doc($post, $files);
                       
            if(!empty($res) && $res === 1){
                $this->response = array(
                    'status' => 'true',
                    'response_msg' => 'Document Uploaded',
                );
                echo json_encode($this->response);
            }
            else if(!empty($res) && $res === 2){
                $this->response = array(
                    'status' => 'false',
                    'response_msg' => 'Document Not Uploaded',
                );
                echo json_encode($this->response);
            }
            else {
                $this->response = array(
                    'status' => 'false',
                    'response_msg' => strip_tags($res),
                );
                echo json_encode($this->response);
            }
        }
    }

    public function send_otp() {
        //param: mobile number 
        header('Content-Type: application/json');
        $post = $_POST;
        $required = ['mobileno'];

        if ($this->check_parameters($required)) {
            $verify_data = ['mobileno'];
            $verify_data = elements($verify_data, $post);       
            
            if(isset($post['email']) && $post['email'] != NULL){
                $email = $post["email"];
            } else {
                $email = '';
            }
	    
	    $mobileno = trim($verify_data['mobileno']);
	    //echo "hi".$mobileno; exit;
            //check exists
            $exists = $this->m_api->check_user_by_email_mobile($email, $mobileno);

            if(!$exists) {
                $otp_data = $this->m_api->generate_random_verification_code($mobileno);

                $check = $this->nexmo->sms($mobileno, $otp_data["sms_msg"]);
                //print_r($check); exit;
                //$check = $otp_data["delivery_receipt_id"];

                if (!empty($otp_data) && !empty($check)) {

                   // $this->m_api->delivery_receipt($check, 'success');

                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'OTP is sent to your mobile no',
                        'delivery_receipt_id' => $check,
                        'otp' => $otp_data["otp"]
                    );

                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'System error. OTP cannot be sent.',
                    );
                    echo json_encode($this->response);
                }
            } else {
                $this->response = array(
                    'status' => 'false',
                    'response_msg' => 'Email Id or Mobile no already exists',
                );
                echo json_encode($this->response);
            }
        }
    }
    
    public function delivery_receipt() {
       
        header('Content-Type: application/json');
        $post = $_GET;
        
        if (isset($post['messageId'])) {
            $exist = $this->db
                    ->where('message_id', $post['messageId'])
                    ->get('delivery_receipt')->row_array();
           
            if ($exist) {
                $res = $this->db
                        ->set('delivery_status', $post['status'])
                        ->where('message_id', $post['messageId'])
                        ->update('delivery_receipt');
            } else {
                $data = [
                    'message_id' => $post['messageId'],
                    'delivery_status' => $post['status'],
                    'phone' => $post['msisdn']
                ];
                
                $res = $this->db->insert('delivery_receipt', $data);
            }
                       
            if($res){
                $this->response = array(
                    'status' => 'true',
                    'response_msg' => 'OTP delivered',
                );
                echo json_encode($this->response);
            }
            else {
                $this->response = array(
                    'status' => 'false',
                    'response_msg' => 'OTP still not delivered',
                );
                echo json_encode($this->response);
            }
        }
    }
    
    public function check_delivery_status() {
       
        header('Content-Type: application/json');
        $post = $_POST;
        $required = ['delivery_receipt_id'];

        if ($this->check_parameters($required)) {            
            $res = $this->m_api->check_delivery_status($post);
                       
            if($res){
                $this->response = array(
                    'status' => 'true',
                    'response_msg' => 'OTP delivered',
                );
                echo json_encode($this->response);
            }
            else {
                $this->response = array(
                    'status' => 'false',
                    'response_msg' => 'OTP still not delivered',
                );
                echo json_encode($this->response);
            }
        }
    }
    
    public function make_call() {
        //param: mobile number 
        header('Content-Type: application/json');
        $post = $_POST;
        $required = ['mobileno'];

        if ($this->check_parameters($required)) {
            $verify_data = ['mobileno'];
            $verify_data = elements($verify_data, $post);            

            $otp_data = $this->m_api->generate_random_verification_code($verify_data['mobileno']);
            
            $check = $this->nexmo->call($verify_data['mobileno'], $otp_data["otp"]);
            //print_r($check); exit;
            //$check = $otp_data["delivery_receipt_id"];

            if (!empty($otp_data) && !empty($check)) {
                
                $this->response = array(
                    'status' => 'true',
                    'response_msg' => 'OTP is sent to your mobile no',
                    'delivery_receipt_id' => $check,
                    'otp' => $otp_data["otp"]
                );

                echo json_encode($this->response);
            } else {
                $this->response = array(
                    'status' => 'false',
                    'response_msg' => 'System error. OTP cannot be sent.',
                );
                echo json_encode($this->response);
            }
        }
    }

    public function verify_otp() {
        //otp_id, otp
        header('Content-Type: application/json');
        $post = $_POST;

        $required = ['delivery_receipt_id', 'otp'];

        if ($this->check_parameters($required)) {
            $verify_data = ['delivery_receipt_id', 'otp'];
            $verify_data = elements($verify_data, $post);

            $verify_otp_data = $this->m_api->verify_scode($verify_data);      
     
            if ($verify_otp_data) {
                $this->response = array(
                    'status' => 'true',
                    'response_msg' => 'Congratulation! You are successfully verified.',
                );

                echo json_encode($this->response);
            } else {
                $this->response = array(
                    'status' => 'false',
                    'response_msg' => 'Invalid verification code.',
                );
                echo json_encode($this->response);
            }
        }
    }
    
    public function delete_otp() {
        $time = date('Y-m-d H:i:s', strtotime('-15 minutes'));
        $res = $this->db
                ->where('date <=', $time)
                ->delete('delivery_receipt');
        
        if($res){
            $this->response = array(
                'status' => 'true',
            );

            echo json_encode($this->response);
        }
        else {
                $this->response = array(
                    'status' => 'false',
                );
                echo json_encode($this->response);
            }
    }    

    public function verify_signup_code() {

        header('Content-Type: application/json');
        $post = $_POST;
        $required = ['vcode', 'user_id'];

        if ($this->check_parameters($required)) {

            $verify_data = ['vcode', 'user_id'];
            $verify_data = elements($verify_data, $post);


            $userdata = $this->m_api->verify_scode($verify_data);

            if ($userdata) {

                $this->m_api->check_update_device_token($post);

                $this->response = array(
                    'status' => 'true',
                    'response_msg' => 'Verification successful',
                    'screen_code' => '222',
                    'user_id' => $userdata['user_id'],
                    'email' => $userdata['email'],
                );

                echo json_encode($this->response);
            } else {
                $this->response = array(
                    'status' => 'false',
                    'response_msg' => 'System error. User verification failed.',
                );
                echo json_encode($this->response);
            }
        }
    }

    public function verify($md5_user_id) {
        $status = $this->m_api->verify($md5_user_id);
        if ($status) {
            if ($status == '1') {
                echo '<h1><font color="green">Your Drinxin is account verified.</h1>';
            } else if ($status == '2') {
                echo '<h1><font color="green">User already verified.</h1>';
            }
        } else {
            echo '<h1><font color="red">User not registered.</h1>';
        }
    }

    public function signin() {
        header('Content-Type: application/json');
        $post = $_REQUEST;       

        if (isset($post['signin_username']) && $post['signin_username'] != null && isset($post['password']) && $post['password'] != null) {
            $userdata = $this->m_api->signin($post);
            
            if ($userdata) {
                
                $this->m_api->log_in($userdata['user_id']);
                
                $post['user_id'] = $userdata['user_id'];
                if ($userdata['token'] != '') {
                    $token = $userdata['token'];
                } else {
                    $token = md5(rand() . rand());
                }
                $this->m_api->update_login_token($post['user_id'], $token);
//token is valid               
                $this->m_api->check_update_device_token($post);

                $screen_code = $this->m_api->check_profile_complition_and_get_screen_code($post['user_id']);
                if ($userdata['password_updated'] == 0) {
//111 display update password screen
                    $screen_code = '111';
                }
                
                if(!empty($userdata['verification_doc'])) {
                    $doc_uploaded = true;
                } else {
                    $doc_uploaded = false;
                }
                
                $this->response = array(
                    'status' => 'true',
                    'response_msg' => 'Sign in Successful',
                    'token' => $token,
                    'screen_code' => $screen_code,
                    'user_id' => $userdata['user_id'],
                    'firstname' => $userdata['firstname'],
                    'lastname' => $userdata['lastname'],
                    'email' => $userdata['email'],
                    'doc_uploaded' => $doc_uploaded,
                    'is_admin_verified' => $userdata['is_admin_verified'],
                );
// $this->response = $this->response;
                echo json_encode($this->response);
            } else {
//invalid user
                $this->response = array(
                    'status' => 'false',
                    'response_msg' => 'Invalid email id / mobile no or password',
                );
                echo json_encode($this->response);
            }
        } else {
//enter username and password
            $this->response = array(
                'status' => 'false',
                'response_msg' => 'Please enter email/mobileno and password',
            );
            echo json_encode($this->response);
        }
    }

    public function check_social_id() {
        header('content-type:application/json');
        $post = $_POST;
        $required = ['social_id' ];
        if ($this->check_parameters($required)) {
            $userdata = $this->m_api->check_social_id($post);
            if ($userdata) {
                $post['user_id'] = $userdata['user_id'];
                if ($userdata['token'] != '') {
                    $token = $userdata['token'];
                } else {
                    $token = md5(rand() . rand());
                }
                $this->m_api->update_login_token($post['user_id'], $token);
                //token is valid
                $this->m_api->check_update_device_token($post);

                $screen_code = $this->m_api->check_profile_complition_and_get_screen_code($post['user_id']);

                $this->response = array(
                    'status' => 'true',
                    'response_msg' => 'Sign in Successful',
                    'token' => $token,
                    'screen_code' => $screen_code,
                    'user_id' => $userdata['user_id'],
                    'firstname' => $userdata['firstname'],
                    'lastname' => $userdata['lastname'],
                    'email' => $userdata['email'],
                );
                echo json_encode($this->response);
            } else {
                $this->response = array(
                    'status' => 'false',
                    'response_msg' => 'No data found'
                );
                echo json_encode($this->response);
            }
        }
    }

    public function signin_with_social() {
        header('content-type:application/json');
        $post = $_POST;
        $required = ['social_id', 'social_type'];
        if ($this->check_parameters($required)) {
            
            if(isset($post['email'])) {
                $userdata = $this->m_api->get_user_by_email_mobile($post['email']);
            } 
            else if(isset($post['mobileno'])) {
                $userdata = $this->m_api->get_user_by_email_mobile($post['mobileno']);
            }
            
            if ($userdata) {
                $post['user_id'] = $userdata['user_id'];
                if ($userdata['token'] != '') {
                    $post['token'] = $userdata['token'];
                } else {
                    $post['token'] = md5(rand() . rand());
                }
//token is valid
                $this->m_api->check_update_device_token($post);
               
                $update_data = elements(['user_id', 'token', 'social_id', 'social_type'], $post);
                $this->m_api->update_user($update_data);
                $screen_code = $this->m_api->check_profile_complition_and_get_screen_code($userdata['user_id']);
                
                $admin_data = $this->db->where('key', 'admin_email_address')->get('setting')->row_array();
                $to = $admin_data['value'];
                $subject = 'A New Signup in Drinxin';
                $msg = $this->load->view('mail_tmp/header', $admin_data, true);
                $msg .= $this->load->view('mail_tmp/age_verification_notice', $admin_data, true);
                $msg .= $this->load->view('mail_tmp/footer', $admin_data, true);
                $this->m_api->send_mail($to, $subject, $msg);

                $this->m_api->log_in($userdata['user_id']);
                
                $this->response = array(
                    'status' => 'true',
                    'response_msg' => 'Sign in Successful',
                    'token' => $post['token'],
                    'screen_code' => $screen_code,
                    'user_id' => $userdata['user_id'],
                    'firstname' => $userdata['firstname'],
                    'lastname' => $userdata['lastname'],
                    'email' => $userdata['email'],
                );
// $this->response = $this->response;
                echo json_encode($this->response);
                exit();
            } /*else {
                $this->response = array(
                    'status' => 'false',
                    'response_msg' => 'No data found. Please signup.'
                );
                echo json_encode($this->response);
            }*/            
            else {

                $post['token'] = md5(rand() . rand());
                $signup_data = elements(['firstname', 'lastname', 'birthdate', 'email', 'mobileno', 'social_id', 'social_type'], $post, '');

                $userdata = $this->m_api->signup($signup_data);
//check and update device token
                $post['user_id'] = $userdata['user_id'];
                
                $this->m_api->check_update_device_token($post);
                $this->m_api->update_login_token($post['user_id'], $post['token']);
                $screen_code = $this->m_api->check_profile_complition_and_get_screen_code($userdata['user_id']);
                
                $admin_data = $this->db->where('key', 'admin_email_address')->get('setting')->row_array();
                    $to = $admin_data['value'];
                    $subject = 'A New Signup in Drinxin';
                    $msg = $this->load->view('mail_tmp/header', $admin_data, true);
                    $msg .= $this->load->view('mail_tmp/age_verification_notice', $admin_data, true);
                    $msg .= $this->load->view('mail_tmp/footer', $admin_data, true);
                    $this->m_api->send_mail($to, $subject, $msg);

                $this->response = array(
                    'status' => 'true',
                    'response_msg' => 'Sign in Successful',
                    'token' => $post['token'],
                    'screen_code' => $screen_code,
                    'user_id' => $userdata['user_id'],
                    'firstname' => $userdata['firstname'],
                    'lastname' => $userdata['lastname'],
                    'email' => $userdata['email'],
                );
// $this->response = $this->response;
                echo json_encode($this->response);
                exit();
            }
        }
    }

    public function resend_verification_mail() {
        header('Content-Type: application/json');
        $post = $_POST;
        if (isset($post['email']) && $post['email'] != null) {
            $userdata = $this->m_api->get_user_by_email($post['email']);
            if ($userdata) {
                if ($userdata['status'] == 1 && $userdata['email_verified'] == 1) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'You have already verified your account, Please sign in',
                        'user_id' => $userdata['user_id'],
                        'email' => $userdata['email']
                    );
                    echo json_encode($this->response);
                } else {

                    $to = $userdata['email'];
                    $subject = 'Welcome to Drinxin';
                    $msg = $this->load->view('mail_tmp/header', $userdata, true);
                    $msg .= $this->load->view('mail_tmp/welcome', $userdata, true);
                    $msg .= $this->load->view('mail_tmp/footer', $userdata, true);
                    $this->m_api->send_mail($to, $subject, $msg);

                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Please check your email and verify account',
                        'screen_code' => '000'
                    );
                    echo json_encode($this->response); //JSON_PRETTY_PRINT for well formed
                }
            } else {
//System error cant able to generate email
                $this->response = array(
                    'status' => 'false',
                    'response_msg' => 'Email is not registered'
                );
                echo json_encode($this->response);
            }
        } else {
//enter email
            $this->response = array(
                'status' => 'false',
                'response_msg' => 'Please enter your email'
            );
            echo json_encode($this->response);
        }
    }

    public function forgot_password() {
        header('Content-Type: application/json');
        $post = $_POST;
        if (isset($post['email']) && $post['email'] != null) {
            $userdata = $this->m_api->get_user_by_email($post['email']);
            if ($userdata) {
                if ($userdata['status'] == 0) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'This email is not verified, Please verify your account',
                        'screen_code' => '333', //verification email
                    );
                    echo json_encode($this->response);
                } else {
                    $userdata['password'] = substr(md5(rand()), 1, 8);

                    if ($this->m_api->generate_random_password($userdata['user_id'], $userdata['password'])) {
//send email                        
                        $to = $userdata['email'];
                        $subject = 'Drinxin password recovery system.';
                        $msg = $this->load->view('mail_tmp/header', $userdata, true);
                        $msg .= $this->load->view('mail_tmp/forgot_password', $userdata, true);
                        $msg .= $this->load->view('mail_tmp/footer', $userdata, true);
                        $this->m_api->send_mail($to, $subject, $msg);
//email sent

                        $this->response = array(
                            'status' => 'true',
                            'response_msg' => '"Temporary password has been sent to ' . $post['email'] . ' use this password to sign in and set your new password',
                        );
                        echo json_encode($this->response);
                    } else {
//System error cant able to generate new password
                        $this->response = array(
                            'status' => 'false',
                            'response_msg' => 'System error. System can not able to generate new password.',
                        );
                        echo json_encode($this->response);
                    }
                }
            } else {
//Email id not registered
                $this->response = array(
                    'status' => 'false',
                    'response_msg' => 'Email is not registered',
                );
                echo json_encode($this->response);
            }
        } else {
//enter email
            $this->response = array(
                'status' => 'false',
                'response_msg' => 'Please enter email',
            );
            echo json_encode($this->response);
        }
    }

    public function change_password() {
        header('Content-Type: application/json');
        $post = $_POST;

        //print_r($post);
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'password'];
            if ($this->check_parameters($required)) {

                $post['user_id'] = $userdata['user_id'];
                $res = $this->m_api->update_password($post);
                if ($res) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Your password has been successfully updated',
                    );
                    echo json_encode($this->response);
                } else {
//system error password not updated
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'System error. Password not updated.',
                    );
                    echo json_encode($this->response);
                }
                /* } */
            }
        }
    }

    public function logout() {
        header('content-type:application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'device_token'];
            if ($this->check_parameters($required)) {
                $this->m_api->delete_device_token($post);
                $this->m_api->log_out($post["user_id"]);
                $this->response = array(
                    'status' => 'true',
                    'response_msg' => 'User logout successfully',
                );
                echo json_encode($this->response);
            }
        }
    }

    public function get_profile() {
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'profile_id'];
            if ($this->check_parameters($required)) {

                if ($post['user_id'] == $post['profile_id']) {
                    $user_data = $this->m_api->get_profile($post);
                } else {
                    $user_data = $this->m_api->get_profile($post);
                }


                if ($user_data) {

                    $this->response = array(
                        'status' => 'true',
                        'user_data' => $user_data
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'User data is not available',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }

    public function update_profile() {
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {

            $required = ['user_id'];
            if ($this->check_parameters($required)) {

                $user_data = $this->m_api->update_profile($post);
                if ($user_data) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Profile updated',
                        'user_data' => $user_data
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Please provide valid data for update profile.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }

    public function about_us() {
        $about_arr = $this->m_api->get_about_us();
        echo $about_arr;
    }

    public function privacy_policy() {
        $privacy_arr = $this->m_api->get_privacy_policy();
        echo $privacy_arr;
    }

    public function term_and_conditions() {
        $tmc_arr = $this->m_api->get_term_condition();
        echo $tmc_arr;
    }

    public function faqs() {   
        $faq_arr = $this->m_api->get_faqs_list();
        echo $faq_arr;
    }

    public function faq_question_list() {
        header('Content-Type: application/json');
        $faq_arr = $this->m_api->get_faq_question_list();
        //print_r($faq_arr); exit;

        $this->response = array(
            'status' => 'true',
            'questions' => $faq_arr
        );
        echo json_encode($this->response);
    }

    public function faq_detail() {
        header('Content-Type: application/json');
        $post = $_POST;

        $required = ['faq_id'];
        if ($this->check_parameters($required)) {

            $faq_data = $this->m_api->get_faq_details($post);

            if ($faq_data) {
                $this->response = array(
                    'status' => 'true',
                    'faq_data' => $faq_data
                );
                echo json_encode($this->response);
            } else {
                $this->response = array(
                    'status' => 'false',
                    'response_msg' => 'No data found',
                );
                echo json_encode($this->response);
            }
        }
    }
    
    public function get_brands() {
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {
		
		$brand_array = $this->m_api->get_brand_list($post);        

                if ($brand_array) {
                    $this->response = array(
                        'status' => 'true',
                        'brands' => $brand_array
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function get_volume() {
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id', 'dtype'];
            if ($this->check_parameters($required)) {      
		
		$volume_array = $this->m_api->get_volume_list($post);        

                if ($volume_array === 1) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                } 
		else if ($volume_array === 2) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Please type of volume',
                    );
                    echo json_encode($this->response);
                } 
		else {
		    $this->response = array(
                        'status' => 'true',
                        'volume' => $volume_array,			
                    );
                    echo json_encode($this->response);                    
                }
            }
        }
    }
    
    public function get_category() {
        header('Content-Type: application/json');       
        
        if(empty($_POST["parent_id"])){
            $post = $_POST;
            $post["parent_id"] = 0;            
        } else {
            $post = $_POST;
        }
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id'];
            if ($this->check_parameters($required)) {      
		
		$category_array = $this->m_api->get_category_list($post);

                if ($category_array) {
                    $this->response = array(
                        'status' => 'true',
                        'category' => $category_array
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }
        
    }
    
    public function get_sub_category() {
        header('Content-Type: application/json');  
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id', 'category_id'];
            if ($this->check_parameters($required)) {   
		
		$category_array = $this->m_api->get_sub_category($post);

                if ($category_array) {
                    $this->response = array(
                        'status' => 'true',
                        'category' => $category_array
                    );                    
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }
        
    }
    
    public function get_top_pick_products(){
        header('Content-Type: application/json');  
        $post=$_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id'];
            if ($this->check_parameters($required)) { 
		
                $top_pick_product = $this->m_api->get_top_pick_product_list($post);        

                if ($top_pick_product) {
                    $this->response = array(
                        'status' => 'true',
                        'top_pick_product' => $top_pick_product,
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function get_product_details() {
        header('Content-Type: application/json');       
        $post=$_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id', 'product_id'];
            if ($this->check_parameters($required)) {   
		
		$product_array = $this->m_api->get_product_details_by_id($post);

                if ($product_array) {
                    $this->response = array(
                        'status' => 'true',
                        'product' => $product_array,		
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }
        
    }
    
    public function products_list() {
        header('Content-Type: application/json');   
        $post=$_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id'];
            if ($this->check_parameters($required)) {  
		
		if (!isset($post['offset']) || !$post['offset']) {
		    $post['offset'] = '0';
		}
            
                $product_list = $this->m_api->get_products_list($post);

                if(!empty($product_list[2])){
		    $this->response = array(
			'status' => 'true',
			'product_list' => $product_list[2],			
			'offset' => $product_list[1],
			'flag' => $product_list[0]
		    );    
                    echo json_encode($this->response); 
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }
               
    }
    
    public function top_brands() {
        header('Content-Type: application/json');        
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id'];
            if ($this->check_parameters($required)) {
		
		if (!isset($post['offset']) || !$post['offset']) {
		    $post['offset'] = '0';
		}
        
                $top_brand_data = $this->m_api->get_top_brands($post);

                if ($top_brand_data[2]) {
                    $this->response = array(
                        'status' => 'true',
                        'top_brand' => $top_brand_data[2],				
			'offset' => $top_brand_data[1],
			'flag' => $top_brand_data[0]
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    );
                    echo json_encode($this->response);
                }
            }
        }        
    }
    
    public function similar_products() {
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id', 'product_id'];
            if ($this->check_parameters($required)) {
		
		if (!isset($post['offset']) || !$post['offset']) {
		    $post['offset'] = '0';
		}
        
                $similar_product_data = $this->m_api->get_similar_products($post);

                if ($similar_product_data[2]) {
                    $this->response = array(
                        'status' => 'true',
                        'similar_products' => $similar_product_data[2],			
			'offset' => $similar_product_data[1],
			'flag' => $similar_product_data[0]
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    );
                    echo json_encode($this->response);
                }  
            }
        }
    }
    
    public function volume_list_by_product(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id', 'product_id'];
            if ($this->check_parameters($required)) { 
        
                $volume_filter_data = $this->m_api->get_volume_list_by_product($post);

                if ($volume_filter_data) {
                    $this->response = array(
                        'status' => 'true',
                        'volume_list' => $volume_filter_data
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function search_product(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {     
		
		if (!isset($post['offset']) || !$post['offset']) {
		    $post['offset'] = '0';
		}
            
                $product_search_data = $this->m_api->get_product_search_list($post);

                if ($product_search_data[2]) {
                    $this->response = array(
                        'status' => 'true',
                        'search' => $product_search_data[2],
			'offset' => $product_search_data[1],
			'flag' => $product_search_data[0]
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function return_policy(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'product_id'];
            if ($this->check_parameters($required)) {                
            
                $product_return_policy = $this->m_api->get_product_return_policy($post);

                if ($product_return_policy) {
                    $this->response = array(
                        'status' => 'true',
                        'return_policy' => $product_return_policy
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    //Cart
    
    public function add_to_bag(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'product_id', 'volume_id', 'qty', 'from_where'];
            if ($this->check_parameters($required)) {                
            
                $cart_product = $this->m_api->add_to_bag($post);
                
               // print_r($cart_product); exit;

                if ($cart_product === 1) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Item Already Added in Cart.',
                    );
                    echo json_encode($this->response);
                }
                elseif ($cart_product === 2) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Quantity out of stock.',
                    );
                    echo json_encode($this->response);
                }
                elseif ($cart_product === 3) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Item added to cart.',
                    );
                    echo json_encode($this->response);
                }                
                elseif ($cart_product === 4) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    );
                    echo json_encode($this->response);
                }
                elseif ($cart_product === 5) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'You age is not verified.',
                    );
                    echo json_encode($this->response);
                }
		elseif ($cart_product === 6) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'The product selected falls outside your delivery zone.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function update_bag(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'product_id', 'volume_id', 'qty'];
            if ($this->check_parameters($required)) {                
            
                $cart_product = $this->m_api->update_bag($post);
                
               // print_r($cart_product); exit;

                if ($cart_product == 1) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Item qty updated.',
                    );
                    echo json_encode($this->response);
                } 
                else if ($cart_product == 2) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Quantity out of stock',
                    );
                    echo json_encode($this->response);
                }
                else if ($cart_product == 3) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    );
                    echo json_encode($this->response);
                }
                else if ($cart_product == 4) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Quantity not updated.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function remove_product(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'product_id', 'volume_id'];
            if ($this->check_parameters($required)) {                
            
                $remove_product = $this->m_api->remove_product($post);
                
               // print_r($cart_product); exit;

                if (!empty($remove_product)) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Product removed',
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'System failure. Product cannot be removed.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function cart_product_list(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {  
		
		$cart = $this->m_api->cart_product_list($post);

                if (!empty($cart)) {
                    $this->response = array(
                        'status' => 'true',                        
                        'cart_list' => $cart
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No product added in cart.',                        
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    //Shipping
    public function add_shipping(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'name', 'contactno', 'address', 'zipcode', 'latitude', 'longitude'];
            if ($this->check_parameters($required)) {                
            
                $add_shipping = $this->m_api->add_shipping($post);
                
               // print_r($cart_product); exit;

                if ($add_shipping == 1) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Shipping details added to account.',
                    );
                    echo json_encode($this->response);
                } 
                elseif ($add_shipping == 2) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Shipping details cannot be added to account.',
                    );
                    echo json_encode($this->response);
                }
                elseif ($add_shipping == 3) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Invalid zipcode',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function remove_shipping_details(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'shipping_id'];
            if ($this->check_parameters($required)) {                
            
                $remove_shipping_details = $this->m_api->remove_shipping_details($post);
                
                if (!empty($remove_shipping_details)) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Shipping details removed.',
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'System failure. Shipping details cannot be removed.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function get_shipping_details(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {                
            
                $get_shipping_details = $this->m_api->get_shipping_details($post);
                
                if (!empty($get_shipping_details)) {
                    $this->response = array(
                        'status' => 'true',
                        'sipping_details' => $get_shipping_details,
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No Data Found.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function get_shipping_by_id(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'shipping_id'];
            if ($this->check_parameters($required)) {                
            
                $get_shipping_by_id = $this->m_api->get_shipping_by_id($post);
                
                if (!empty($get_shipping_by_id)) {
                    $this->response = array(
                        'status' => 'true',
                        'shipping_detail' => $get_shipping_by_id,
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function add_shipping_to_account(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'shipping_id'];
            if ($this->check_parameters($required)) {                
            
                $get_shipping_by_id = $this->m_api->add_shipping_to_account($post);
                
                if (!empty($get_shipping_by_id)) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Shipping address added.',
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function update_shipping_details(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'shipping_id'];
            if ($this->check_parameters($required)) {                
            
                $add_shipping = $this->m_api->update_shipping_details($post);
                
                if ($add_shipping == 1) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    );
                    echo json_encode($this->response);
                } 
                elseif ($add_shipping == 2) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Shipping details cannot be updated.',
                    );
                    echo json_encode($this->response);
                }
                elseif ($add_shipping == 3) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Invalid zipcode',
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'true',
                        'details' => $add_shipping,
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function check_zipcode() {       
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'zipcode'];
            if ($this->check_parameters($required)) {                
            
                $check_zipcode = $this->m_api->check_zipcode($post);
                
                if (!empty($check_zipcode)) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Service Available.',
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Service Not Available.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    //Gift card
    public function gift_card_by_id() {       
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'card_id'];
            if ($this->check_parameters($required)) {                
            
                $gift_card = $this->m_api->gift_card_details_by_id($post);
                
                if (!empty($gift_card)) {
                    $this->response = array(
                        'status' => 'true',
                        'gift_card' => $gift_card,
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No Gift Card Found.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function send_gift_card() {       
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'name', 'email', 'amount', 'message', 'sender_name'];
            if ($this->check_parameters($required)) {                
            
                $gift_card = $this->m_api->send_gift_card($post);
                
                /*$push = array(
                    'to_user_id' => $post["user_id"],
                    'message' => 'A Gift Card is sent by you',   
                    'notification_type' => 4           
                );

                $this->m_api->create_notification($push);             
                $this->m_notify->send($push);*/
                
                if (!empty($gift_card)) { 
                    $this->response = array(
                        'status' => 'true',
                        'data' => $gift_card,
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No Gift Card Sent.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function do_gift_card_payment(){
	$older_data = $this->m_api->get_gift_by_id($_GET['card_id']);
        $older_data['total_price'] = 100 * $older_data['amount'];	
        $this->load->view('gift_card_payment', $older_data);
    }
    
    public function save_gift_card_transation(){
        $post = $_POST;
        $order = $this->m_api->save_gift_card_transation($post);
        
        $push = array(
            'to_user_id' => "".$order['receiver']."",
            'message' => 'You have received a gift card from '.$order['sender'].'',   
            'notification_type' => 4           
        );

        $this->m_api->create_notification($push);             
        $this->m_notify->send($push);
            
        if (!empty($order)) {
            redirect(site_url("api/payment_success"));            
        } 
        else {
            redirect(site_url("api/payment_failed"));
        }
    }
    
    public function apply_gift_card() {       
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'card_id'];
            if ($this->check_parameters($required)) {                
            
                $order = $this->m_api->apply_gift_card($post);
                
                if (!empty($order) && $order == 1) {                    
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Gift card code applied successfully.',
                    );
                    echo json_encode($this->response);
                } 
                elseif (!empty($order) && $order == 2) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Gift card code not applied successfully.',
                    );
                    echo json_encode($this->response);
                }
                elseif (!empty($order) && $order == 3) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No amount left in your gift card.',
                    );
                    echo json_encode($this->response);
                }
                elseif (!empty($order) && $order == 4) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Code already expired or You are not valid user to use this gift card.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function gift_card_received() {       
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {  
		
		if (!isset($post['offset']) || !$post['offset']) {
		    $post['offset'] = '0';
		}
            
                $gift_card = $this->m_api->gift_card_received($post);
                
                if (!empty($gift_card[2])) {
                    $this->response = array(
                        'status' => 'true',
                        'gift_card' => $gift_card[2],			
			'offset' => $gift_card[1],
			'flag' => $gift_card[0]
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No Gift Card Found.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function gift_card_sent() {       
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {    
		
		if (!isset($post['offset']) || !$post['offset']) {
		    $post['offset'] = '0';
		}
            
                $gift_card = $this->m_api->gift_card_sent($post);
                
                if (!empty($gift_card[2])) {
                    $this->response = array(
                        'status' => 'true',
                        'gift_card' => $gift_card[2],			
			'offset' => $gift_card[1],
			'flag' => $gift_card[0]
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No Gift Card Found.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    //Orders
    public function my_order() {       
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {   
		
		if (!isset($post['offset']) || !$post['offset']) {
		    $post['offset'] = '0';
		}
            
                $order = $this->m_api->my_order($post);
                
                if (!$order) {                    
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No Orders found',
                    );
                    echo json_encode($this->response);
                } 
                else {
                    
                    $this->response = array(
                        'status' => 'true',
                        'order' => $order[2],			
			'offset' => $order[1],
			'flag' => $order[0]
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function my_past_order() {       
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {  
		
		if (!isset($post['offset']) || !$post['offset']) {
		    $post['offset'] = '0';
		}
            
                $order = $this->m_api->my_past_order($post);
                
                if (!$order) {                    
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No Orders found',
                    );
                    echo json_encode($this->response);
                } 
                else {
                    
                    $this->response = array(
                        'status' => 'true',
                        'order' => $order[2],			
			'offset' => $order[1],
			'flag' => $order[0]
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function my_upcoming_order() {       
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {      
		
		if (!isset($post['offset']) || !$post['offset']) {
		    $post['offset'] = '0';
		}
            
                $order = $this->m_api->my_upcoming_order($post);
                
                if (!$order) {                    
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No Orders found',
                    );
                    echo json_encode($this->response);
                } 
                else {     
		    $this->response = array(
                        'status' => 'true',
                        'order' => $order[2],			
			'offset' => $order[1],
			'flag' => $order[0]
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function order_details() {       
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'order_id'];
            if ($this->check_parameters($required)) {                
            
                $order = $this->m_api->order_details($post);
                
                if (!$order) {                    
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No orders details found',
                    );
                    echo json_encode($this->response);
                } 
                else {                    
                    $this->response = array(
                        'status' => 'true',
                        'order' => $order,
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function help_support() {       
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'subject', 'message'];
            if ($this->check_parameters($required)) {                
            
                $order = $this->m_api->help_support($post);
                
                if (!$order) {                    
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Message sent not successfully.',
                    );
                    echo json_encode($this->response);
                } 
                else {                    
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Message sent successfully.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function checkout() {       
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {                
            
                $order = $this->m_api->checkout($post);
                
                if ($order === 3) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No Products Found.',
                    );
                    echo json_encode($this->response);                    
                }
                else if($order === 2){
                    $this->response = array(
                        'status' => 'false',
                        'order' => 'Seller is closed. Please select another seller.',
                    );
                    echo json_encode($this->response);
                }
		else if($order === 1){
                    $this->response = array(
                        'status' => 'false',
                        'order' => 'Quantity out of stock.',
                    );
                    echo json_encode($this->response);
                }
                else {
                    $this->response = array(
                        'status' => 'true',
                        'order' => $order,
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function promocode_list() {       
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {                
            
		if (!isset($post['offset']) || !$post['offset']) {
		    $post['offset'] = '0';
		}
		
                $order = $this->m_api->promocode_list($post);
                
                if ($order[2]) {                    
                    $this->response = array(
                        'status' => 'true',
                        'promocode' => $order[2],
			'offset' => $order[1],
			'flag' => $order[0]
                    );
                    echo json_encode($this->response);
                } 
                else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No promocode found.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function apply_promocode() {       
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'promocode', 'gross_amount'];
            if ($this->check_parameters($required)) {                
            
               $order = $this->m_api->check_promocode($post);                
                
               if (!empty($order) && $order === 2) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Code Already Used.',
                    );
                    echo json_encode($this->response);
                }
                elseif (!empty($order) && $order === 3) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Code Already Expired.',
                    );
                    echo json_encode($this->response);
                }
                 elseif (!empty($order) && $order === 4) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'You will not be able to use this promocode.',
                    );
                    echo json_encode($this->response);
                }
                elseif (!empty($order)) {                   
                    $this->response = array(
                        'status' => 'true',
                        'balance' => $order,
                    );
                    echo json_encode($this->response);
                } 
            }
        }
    }  
    
    public function check_quantity() {       
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'product_id', 'volume_id', 'qty'];
            if ($this->check_parameters($required)) {                
            
                $order = $this->m_api->check_quantity($post["product_id"], $post["volume_id"], $post["qty"]);
                
                if ($order) {                    
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'You can add quantity',
                    );
                    echo json_encode($this->response);
                } 
                else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Quantity out of stock',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function confirm_order() {       
        header('Content-Type: application/json');
        $post = $_POST;
        
        $this->response = array(
            'status' => 'false',
            'order' => 'You are not allowed to place order.',
        );
        echo json_encode($this->response); exit;
        
        $userdata = $this->auth();
        if ($userdata) {
            //$required = ['user_id', 'points', 'wallet', 'promocode', 'card_id', 'online', 'send_as_gift'];
            $required = ['user_id', 'shipping_id', 'net_amount'];
            if ($this->check_parameters($required)) {                
            
                $order = $this->m_api->confirm_order($post);
                //print_r($order); exit;
                
                if(!empty($order) && is_array($order) && isset($order[0]) && $order[0] === 8){  
                    
                    $push = array(
                        'to_user_id' => $post["user_id"],
                        'message' => 'Order placed at '.date("Hi").' on '.date("d-m-y").'. Order number '.$order[1],   
                        'notification_type' => 1
                    );

                    $this->m_api->create_notification($push);  
                    $this->m_notify->send($push);
                    
                    $this->response = array(
                        'status' => 'true',
                        'order' => 'Order placed at '.date("Hi").' on '.date("d-m-y").'. Order number '.$order[1],
                    );
                    echo json_encode($this->response);
                } 
                else if(!empty($order) && $order === 1){
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Quantity out of stock',
                    );
                    echo json_encode($this->response);
                }
                else if(!empty($order) && $order === 2){
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Gift card code not applied successfully.',
                    );
                    echo json_encode($this->response);
                }
                else if(!empty($order) && $order === 3){
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No amount left in your gift card.',
                    );
                    echo json_encode($this->response);
                }
                else if(!empty($order) && $order === 4){
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Code already expired or You are not valid user to use this gift card.',
                    );
                    echo json_encode($this->response);
                }
                else if(!empty($order) && $order === 5){
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No Order Found.',
                    );
                    echo json_encode($this->response);
                }
		else if(!empty($order) && $order === 6){
                    $this->response = array(
                        'status' => 'false',
                        'order' => 'Seller is closed. Please select another seller.',
                    );
                    echo json_encode($this->response);
                }
                else if(!empty($order) && $order === 9){
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'You do not have enough balance in your wallet to pay.',
                    );
                    echo json_encode($this->response);
                }
                else if(!empty($order) && is_array($order) && isset($order["online"]) && $order["online"] === 'yes') {
                    $this->response = array(
                        'status' => 'true',
                        'online' => 'true',
                        'order' => $order,
                    );
                    echo json_encode($this->response);
                }   
                else if(!empty($order) && $order === 10){
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Please select any one way of payment.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }    
       
    public function do_payment(){
        //$older_data = $this->m_api->get_order_by_id($_GET['order_id']);
        //$older_data['total_price'] = 100 * $older_data['net_amount'];	
	$seller_add = array();
	$config_data = $this->db->where_in('key', array('payment_mode', 'test_public_key', 'client_key', 'test_secret_key', 'service_key'))->get('setting')->result_array();
	
	//print_r($config_data); exit;

	if($config_data[2]["value"] == '1') {
	    $older_data["public_key"] = $config_data[3]["value"];
	    $secret_key = $config_data[4]["value"];
	}
	else if($config_data[2]["value"] == '2') {
	    $older_data["public_key"] = $config_data[1]["value"];
	    $secret_key = $config_data[0]["value"];
	}
	
	//get order details
	$order_product = $this->db->select("op.*, p.product_name, p.description, p.feature_img", false)
		->join("products p", "op.product_id = p.product_id")
		->where("op.order_id", $_GET['order_id'])
		->get("order_product op")->result_array();

	$items = array();

	foreach($order_product as $key => $value) {
	    if(empty($value["description"])) {
		$desc = $value["product_name"];
	    }
	    else {
		$desc = $value["description"];
	    }
	    
	    //check similar seller
	    if(!in_array($value["seller_id"],$seller_add)) {
		
		//check for qty more than 1
		if($value["qty"] <= 1){
		    
		    $single = array(
			'name' => $value["product_name"],
			'description' => $desc,
			'images' => [PRODUCT_S3_PATH.$value["feature_img"]],
			'amount' => ($value["price"] + $value["delivery_charge"])*100,
			'currency' => CURRENCY,
			'quantity' => $value["qty"],
		    );
		    
		    array_push($items, $single);
		}
		else if($value["qty"] > 1){		    
		    for($i=1; $i <= $value["qty"]; $i++) {
			if($i == 1){
			    $single = array(
				'name' => $value["product_name"],
				'description' => $desc,
				'images' => [PRODUCT_S3_PATH.$value["feature_img"]],
				'amount' => ($value["price"] + $value["delivery_charge"])*100,
				'currency' => CURRENCY,
				'quantity' => 1,
			    );
			    
			    array_push($items, $single);
			}
			else if($i > 1){ 
			    $single = array(
				'name' => $value["product_name"],
				'description' => $desc,
				'images' => [PRODUCT_S3_PATH.$value["feature_img"]],
				'amount' => $value["price"]*100,
				'currency' => CURRENCY,
				'quantity' => 1,
			    );
			    
			    array_push($items, $single);
			}
		    }
		}
		
		//$amount = $value["price"] + $value["delivery_charge"];
		
		array_push($seller_add, $value["seller_id"]);
	    }
	    else {
		$single = array(
		    'name' => $value["product_name"],
		    'description' => $desc,
		    'images' => [PRODUCT_S3_PATH.$value["feature_img"]],
		    'amount' => ($value["price"])*100,
		    'currency' => CURRENCY,
		    'quantity' => $value["qty"],
		);
		
		array_push($items, $single);
	    }
	    
//	    $single = array(
//		'name' => $value["product_name"],
//		'description' => $desc,
//		'images' => [PRODUCT_S3_PATH.$value["feature_img"]],
//		'amount' => $value["price"]*100,
//		'currency' => CURRENCY,
//		'quantity' => $value["qty"],
//	    );
//
//	    array_push($items, $single);
	}
	
//	print_r($items); exit;

	try{ 
	    \Stripe\Stripe::setApiKey($secret_key);

	    $session = \Stripe\Checkout\Session::create([
		'payment_method_types' => ['card'],
		'line_items' => $items,
		'mode' => 'payment',
		'success_url' => base_url().'api/save_transation?session_id={CHECKOUT_SESSION_ID}&order_id='.$_GET['order_id'],
		'cancel_url' => site_url("api/payment_failed"),
	    ]);
	    
	    $older_data["session_id"] = $session->id;
	} 
	catch (Exception $e) {
	    $session = $e->getError();            
	}
//	print_r($session); exit;
        $this->load->view('order_payment', $older_data);
    }
    
    public function save_transation(){
        $post = $_GET;
	//echo "<pre>";	print_r($post); exit;
        $order = $this->m_api->save_transation($post);
        if ($order == 1) {  
            
            $get_order = $this->db->select("user_id, order_no")
                ->where('order_id', $post["order_id"])
                ->get('orders')->row_array();
            
            $push = array(
                'to_user_id' => $get_order["user_id"],
                'message' => 'Order placed at '.date("Hi").' on '.date("d-m-y").'. Order number '.$get_order["order_no"],   
                'notification_type' => 1
            );

            $this->m_api->create_notification($push);  
            $this->m_notify->send($push);
                    
            redirect(site_url("api/payment_success"));
        } 
        else {
            redirect(site_url("api/payment_failed"));
        }
    }
    
    public function payment_success() {        
        echo "success";
    }
    
    public function payment_failed() {        
        echo "failed";
    }
    
    public function get_loyalty_point() {       
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {                
            
                $loyalty_point = $this->m_api->get_loyalty_point($post);
                
                if (!$loyalty_point) {                    
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    );
                    echo json_encode($this->response);
                } 
                else {                    
                    $this->response = array(
                        'status' => 'true',
                        'loyalty_point' => $loyalty_point,
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function loyalty_club_list() {       
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) { 
		
		if (!isset($post['offset']) || !$post['offset']) {
		    $post['offset'] = '0';
		}
            
                $loyalty_point = $this->m_api->loyalty_club_list($post);
                
                if (!$loyalty_point) {                    
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    );
                    echo json_encode($this->response);
                } 
                else {                    
                    $this->response = array(
                        'status' => 'true',
                        'loyalty_club_list' => $loyalty_point[2],			
			'offset' => $loyalty_point[1],
			'flag' => $loyalty_point[0]
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function vip_club_list() {       
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) { 
		
		if (!isset($post['offset']) || !$post['offset']) {
		    $post['offset'] = '0';
		}
            
                $vip_club_list = $this->m_api->vip_club_list($post);
                
                if (!$vip_club_list) {                    
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    );
                    echo json_encode($this->response);
                } 
                else {                    
                    $this->response = array(
                        'status' => 'true',
                        'vip_club_list' => $vip_club_list[2],			
			'offset' => $vip_club_list[1],
			'flag' => $vip_club_list[0]
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function similar_products_loyalty_vip() {
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id', 'product_id', 'in_club'];
            if ($this->check_parameters($required)) {
		
		if (!isset($post['offset']) || !$post['offset']) {
		    $post['offset'] = '0';
		}
        
                $similar_product_data = $this->m_api->get_similar_product_loyalty_vip($post);

                if ($similar_product_data[2]) {
                    $this->response = array(
                        'status' => 'true',
                        'similar_products' => $similar_product_data[2],			
			'offset' => $similar_product_data[1],
			'flag' => $similar_product_data[0]
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    );
                    echo json_encode($this->response);
                }  
            }
        }
    }
    
    public function product_return_by_user() {
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id', 'product_id', 'volume_id', 'order_id', 'reason'];
            if ($this->check_parameters($required)) {
        
                $similar_product_data = $this->m_api->product_return_by_user($post);

                if (!empty($similar_product_data) && $similar_product_data === 2) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'You product has been in queue. Please wait for the seller response.',
                    );
                    echo json_encode($this->response);
                } else if (!empty($similar_product_data) && $similar_product_data === 1) { 
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Sorry, you cannot return product as you have cross the limit days to return product.',
                    );
                    echo json_encode($this->response);
                } 
                else if (!empty($similar_product_data) && $similar_product_data === 3) { 
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Order is still not delivered.',
                    );
                    echo json_encode($this->response);
                } 
            }
        }
    }
    
    public function get_notification_list() {
        header('Content-Type: application/json');
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {
                $post = $_POST;

                if (!isset($post['offset']) || !$post['offset']) {
                    $post['offset'] = '0';
                }
		
                $notification_list = $this->m_api->get_notification_list($post);
		
                if ($notification_list[2]) {
                    $this->response = array(
                        'status' => 'true',
                        'notification_list' => $notification_list[2],
			'offset' => $notification_list[1],
			'flag' => $notification_list[0]
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No notifications.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function wallet_details_by_id() {
        header('Content-Type: application/json');       
        $post=$_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id'];
            if ($this->check_parameters($required)) {   
		
		$product_array = $this->m_api->wallet_details_by_id($post);

                if ($product_array) {
                    $this->response = array(
                        'status' => 'true',
                        'wallet' => $product_array,
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }
        
    }
    
    public function read_notification() {
        header('Content-Type: application/json');       
        $post=$_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id'];
            if ($this->check_parameters($required)) {                
            
                $product_array = $this->m_api->read_notification($post);

                if ($product_array) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Notification read',
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Notification cannot be read',
                    );
                    echo json_encode($this->response);
                }
            }
        }        
    }
    
    public function make_fav_unfav_product() {
        header('Content-Type: application/json');       
        $post=$_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id', 'product_id', 'is_fav'];
            if ($this->check_parameters($required)) {                
            
                $product_array = $this->m_api->make_fav_unfav_product($post);

                if ($product_array) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Product marked as favourite',
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Product marked as unfavourite',
                    );
                    echo json_encode($this->response);
                }
            }
        }        
    }
    
    public function get_favourite_product_list() {
        header('Content-Type: application/json');       
        $post=$_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id'];
            if ($this->check_parameters($required)) {   
		
		if (!isset($post['offset']) || !$post['offset']) {
		    $post['offset'] = '0';
		}
		
                $product_array = $this->m_api->get_favourite_product_list($post);

                if ($product_array[2]) {
                    $this->response = array(
                        'status' => 'true',
                        'favourite_products' => $product_array[2],			
			'offset' => $product_array[1],
			'flag' => $product_array[0]
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }        
    }
    
    public function get_cart_count() {
        header('Content-Type: application/json');       
        $post=$_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id'];
            if ($this->check_parameters($required)) {                
            
                $product_array = $this->m_api->get_cart_count($post);

                if ($product_array) {
                    $this->response = array(
                        'status' => 'true',
                        'cart_count' => $product_array,
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'cart is empty',
                    );
                    echo json_encode($this->response);
                }
            }
        }        
    }
    
    public function check_for_split_delivery() {
        header('Content-Type: application/json');       
        $post=$_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id', 'product_id'];
            if ($this->check_parameters($required)) {                
            
                $product_array = $this->m_api->check_for_split_delivery($post);

                if ($product_array === 1) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'cart is empty',
                    );
                    echo json_encode($this->response);
                }
                else if ($product_array === 2) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'seller is different',
                    );
                    echo json_encode($this->response);
                }
                else if ($product_array === 3) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'seller is same',
                    );
                    echo json_encode($this->response);
                }
            }
        }        
    }
    
    public function brandwise_product_list() {
        header('Content-Type: application/json');   
        $post=$_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id', 'brand_id'];
            if ($this->check_parameters($required)) {     
		
		if (!isset($post['offset']) || !$post['offset']) {
		    $post['offset'] = '0';
		}
            
                $product_list = $this->m_api->brandwise_product_list($post);

                if($product_list[2]){
                    $this->response = array(
                        'status' => 'true',
                        'product_list' => $product_list[2],			
			'offset' => $product_list[1],
			'flag' => $product_list[0]
                    );            
                    echo json_encode($this->response); 
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }               
    }
    
    public function search_brandwise_products(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'brand_id'];
            if ($this->check_parameters($required)) {      
		
		if (!isset($post['offset']) || !$post['offset']) {
		    $post['offset'] = '0';
		}
            
                $product_search_data = $this->m_api->search_brandwise_products($post);

                if ($product_search_data[2]) {
                    $this->response = array(
                        'status' => 'true',
                        'search' => $product_search_data[2],			
			'offset' => $product_search_data[1],
			'flag' => $product_search_data[0]
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function add_rate_review(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'product_id', 'rating'];
            if ($this->check_parameters($required)) {                
            
                $product = $this->m_api->add_rate_review($post);

                if ($product) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Rate/Review added',
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Rate/Review not added',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function edit_rate_review(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'review_id', 'product_id'];
            if ($this->check_parameters($required)) {                
            
                $product = $this->m_api->edit_rate_review($post);

                if ($product === 1) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Rate/Review updated',
                    );
                    echo json_encode($this->response);
                } 
                else if ($product === 2) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Rate/Review not updated',
                    );
                    echo json_encode($this->response);
                }
                else if ($product === 3) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function delete_rate_review(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'review_id', 'product_id'];
            if ($this->check_parameters($required)) {                
            
                $product = $this->m_api->delete_rate_review($post);

                if ($product === 1) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Rate/Review deleted',
                    );
                    echo json_encode($this->response);
                } 
                else if ($product === 2) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Rate/Review not deleted',
                    );
                    echo json_encode($this->response);
                }
                else if ($product === 3) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function review_list(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'product_id'];
            if ($this->check_parameters($required)) {  
		
		if (!isset($post['offset']) || !$post['offset']) {
		    $post['offset'] = '0';
		}
            
                $product = $this->m_api->review_list($post);

                if ($product[2]) {
                    $this->response = array(
                        'status' => 'true',
                        'review_list' => $product[2],			
			'offset' => $product[1],
			'flag' => $product[0]
                    );
                    echo json_encode($this->response);
                } 
                else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function get_review_by_id(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'product_id'];
            if ($this->check_parameters($required)) {                
            
                $product = $this->m_api->get_review_by_id($post);

                if ($product) {
                    $this->response = array(
                        'status' => 'true',
                        'review' => $product,
                    );
                    echo json_encode($this->response);
                } 
                else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function add_seller_rating(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'seller_id', 'rating', 'product_id'];
            if ($this->check_parameters($required)) {                
            
                $product = $this->m_api->add_seller_rating($post);

                if ($product) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Rating added',
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Rating not added',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function edit_seller_rating(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'seller_id', 'seller_rating_id', 'product_id'];
            if ($this->check_parameters($required)) {                
            
                $product = $this->m_api->edit_seller_rating($post);

                if ($product === 1) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Rating updated',
                    );
                    echo json_encode($this->response);
                } 
                else if ($product === 2) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Rating not updated',
                    );
                    echo json_encode($this->response);
                }
                else if ($product === 3) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function delete_seller_rating(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'seller_rating_id', 'seller_id', 'product_id'];
            if ($this->check_parameters($required)) {                
            
                $product = $this->m_api->delete_seller_rating($post);

                if ($product === 1) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Rating deleted',
                    );
                    echo json_encode($this->response);
                } 
                else if ($product === 2) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Rating not deleted',
                    );
                    echo json_encode($this->response);
                }
                else if ($product === 3) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function get_seller_rating(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'seller_id'];
            if ($this->check_parameters($required)) {                
            
                $product = $this->m_api->get_seller_rating($post);

                if ($product) {
                    $this->response = array(
                        'status' => 'true',
                        'final_rating' => $product,
                    );
                    echo json_encode($this->response);
                } 
                else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function get_seller_rating_by_id(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'seller_rating_id'];
            if ($this->check_parameters($required)) {                
            
                $product = $this->m_api->get_seller_rating_by_id($post);

                if ($product) {
                    $this->response = array(
                        'status' => 'true',
                        'review' => $product,
                    );
                    echo json_encode($this->response);
                } 
                else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function add_web_notification(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'order_id'];
            if ($this->check_parameters($required)) {   
                $order = $this->m_api->add_web_notification($post);                
                if (!$order) {                    
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'notification not added',
                    );
                    echo json_encode($this->response);
                } 
                else {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'notification added',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function update_user_location(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'latitude', 'longitude'];
            if ($this->check_parameters($required)) {   
                $order = $this->m_api->update_user_location($post);                
                if (!$order) {                    
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'location not updated',
                    );
                    echo json_encode($this->response);
                } 
                else {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'location updated',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function get_schedule_list(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {   
                $order = $this->m_api->get_schedule_list($post);                
                if ($order) {                    
                    $this->response = array(
                        'status' => 'true',
                        'schedule_list' => $order,
                    );
                    echo json_encode($this->response);
                } 
                else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'no data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function order_cancel_request() {
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id', 'order_id', 'reason'];
            if ($this->check_parameters($required)) {
        
                $data = $this->m_api->order_cancel_request($post);
                
                if ($data === 1) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Order Cancelled Successfully.',
                    );
                    echo json_encode($this->response);
                } 
                else if ($data === 2) { 
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Order is not eligible for cancellation process.',
                    );
                    echo json_encode($this->response);
                } 
                else if ($data === 3) { 
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Request cannot be placed due to some error.',
                    );
                    echo json_encode($this->response);
                } 
            }
        }
    }
    
    public function price_filter() {
        header('Content-Type: application/json');   
        $post=$_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id'];
            if ($this->check_parameters($required)) {                
            
                $product_list = $this->m_api->price_filter($post);

                if($product_list){
                    $this->response = array(
                        'status' => 'true',
                        'max_amount' => $product_list["max_amount"],
                        'min_amount' => $product_list["min_amount"]                        
                    );            
                    echo json_encode($this->response); 
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }               
    }
    
    public function get_brand_product_search_list(){
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'chr'];
            if ($this->check_parameters($required)) {                
            
                $product_search_data = $this->m_api->get_brand_product_search_list($post);

                if ($product_search_data) {
                    $this->response = array(
                        'status' => 'true',
                        'search' => $product_search_data
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function get_abv_list() {
        header('Content-Type: application/json');   
        $post=$_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id'];
            if ($this->check_parameters($required)) {     
		$data = $this->m_api->get_abv_list($post);

                if($data){
                    $this->response = array(
                        'status' => 'true',
                        'max_abv_percent' => $data["max_abv_percent"],
                        'min_abv_percent' => $data["min_abv_percent"] 		                    
                    );            
                    echo json_encode($this->response); 
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }               
    }
    
    public function get_country_list() {
        header('Content-Type: application/json');   
        $post=$_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id'];
            if ($this->check_parameters($required)) {     
		
		$data = $this->m_api->get_country_list($post);

                if($data){
                    $this->response = array(
                        'status' => 'true',
                        'country_list' => $data,			                      
                    );            
                    echo json_encode($this->response); 
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode($this->response);
                }
            }
        }               
    }
    
    public function best_selling_products(){
        header("Content-Type: application/json");
        $post = $_POST;
        
        $userdata = $this->auth();
        if($userdata) {
            
            $required = ['user_id'];
            if($this->check_parameters($required)) {
		
		$data = $this->m_api->best_selling_products($post);
                
                if($data){
                    $this->response = array(
                        "status" => "true",
                        "best_selling_products" => $data,		
                    );
                    echo json_encode($this->response);
                }
                else {
                    $this->response = array(
                        "status" => "false",
                        "response_msg" => "no data found"
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function suggested_products(){
        header("Content-Type: application/json");
        $post = $_POST;
        
        $userdata = $this->auth();
        if($userdata) {
            
            $required = ['user_id'];
            if($this->check_parameters($required)) {
		
		$data = $this->m_api->suggested_products($post);
                
                if($data){
                    $this->response = array(
                        "status" => "true",
                        "suggested_products" => $data,			
                    );
                    echo json_encode($this->response);
                }
                else {
                    $this->response = array(
                        "status" => "false",
                        "response_msg" => "no data found"
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function redeem_gift_card(){
        header("Content-Type: application/json");
        $post = $_POST;
        
        $userdata = $this->auth();
        if($userdata) {
            
            $required = ['user_id', 'code'];
            if($this->check_parameters($required)) {
                $data = $this->m_api->redeem_gift_card($post);
                
                if($data === 1){
                    $this->response = array(
                        "status" => "false",
                        "response_msg" => "no card found"
                    );
                    echo json_encode($this->response);
                }
                else if($data === 2){
                    $this->response = array(
                        "status" => "false",
                        "response_msg" => "you have already redeem this card"
                    );
                    echo json_encode($this->response);
                }
		else {
                    $this->response = array(
                        "status" => "true",
                        "gift_card" => $data
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function send_a_msg(){
        header("Content-Type: application/json");
        $post = $_POST;
        
        $userdata = $this->auth();
        if($userdata) {
            
            $required = ['user_id', 'to_user_id', 'message', 'order_id'];
            if($this->check_parameters($required)) {
                $data = $this->m_api->send_a_msg($post);
                
                if($data){
                    $this->response = array(
                        "status" => "true",
                        "data" => $data
                    );
                    echo json_encode($this->response);
		}
		else {
                    $this->response = array(
                        "status" => "false",
                        "response_msg" => "no data found"
                    );
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    public function get_user_conversation_list() {
        header('Content-Type: application/json');       
        $post=$_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id'];
            if ($this->check_parameters($required)) {                
            
                $user = $this->m_api->get_user_conversation_list($post);

                if ($user) {
                    $this->response = array(
                        'status' => 'true',
                        'conversation_list' => $user["list"],
                        'total_unread_msg_count' => $user["total_unread_msg_count"]
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data Found',
                    );
                    echo json_encode($this->response);
                }
            }
        }        
    }
    
    public function get_conversation_msg() {
        header('Content-Type: application/json');       
        $post=$_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id', 'to_user_id', 'offset', 'order_id'];
            if ($this->check_parameters($required)) {                
            
                $unread = $this->m_api->get_unread_msg_count($post["to_user_id"], $post["user_id"]); 
                $user = $this->m_api->get_conversation_msg($post);
               // $sender = $this->m_api->get_user_details($post["user_id"], $post["to_user_id"]);
                //$receiver = $this->m_api->get_user_details($post["to_user_id"], $post["user_id"]); 
               
                if ($user === 1) {
                    $this->response = array(
                        'status' => 'true',
                        'unread_msg' => (string)$unread,
                        'conversation_msgs' => [],
                        //'conversation_sender' => $sender,
                        //'conversation_receiver' => $receiver,
                    );
                    echo json_encode($this->response);
                }  
                else if ($user) {
                    $this->response = array(
                        'status' => 'true',
                        'unread_msg' => (string)$unread,
                        'conversation_msgs' => $user,
                        //'conversation_sender' => $sender,
                        //'conversation_receiver' => $receiver,
                    );
                    echo json_encode($this->response);
                } 
                else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data Found',
                    );
                    echo json_encode($this->response);
                }
            }
        }        
    }
    
    public function repeat_order_response() {
        header('Content-Type: application/json');       
        $post=$_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id', 'type', 'order_id'];
            if ($this->check_parameters($required)) {                
            
                $data = $this->m_api->repeat_order_response($post);

                if ($data === 1) {
		    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Your order has been reschedule on next date.',
                    );
                    echo json_encode($this->response);
                } 
		else if ($data === 2) {
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    );
                    echo json_encode($this->response);
                } 
		else if ($data === 3) {
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Quantity out of stock',
                    );
                    echo json_encode($this->response);
                } 
		else if ($data === 4) {
		    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Products added in cart.',
                    );
                    echo json_encode($this->response);
                } 
            }
        }        
    }
    
    public function notify_driver(){
        header("Content-Type: application/json");
        $post = $_POST; 
        
        $user_id = $post["user_id"];
	$order_id = $post["order_id"];
	
	//order details
        $get_order = $this->db->select("orders.order_id, orders.user_id, orders.order_no, orders.net_amount, orders.order_date, orders.shipping_id")
                ->where("orders.order_id", $order_id)
                ->get("orders")->row_array();
        
        //user details
        $get_ltlg = $this->db->select("user.user_id, user.latitude, user.longitude, user.firstname, user.lastname, user.mobileno")
                ->where("user.user_id", $get_order["user_id"])
                ->where("user.status", 1)
                ->get("user")->row_array();  
        
        $post["user_id"] = $get_ltlg["user_id"];
        $post["shipping_id"] = $get_order["shipping_id"];
        $shipping_details = $this->m_api->get_shipping_by_id_without_status($post);
        $address = $shipping_details["address"].', '.$shipping_details["zipcode"];
        //print_r($address);
        
        //get seller from order products table
        $find_seller = $this->db->select("seller_id")
                ->where("order_id", $order_id)
                 ->get("order_product")->result_array(); 
        
        //get unique seller
        $seller_ids = array_unique(array_column($find_seller, "seller_id"));   
        
	$seller_arr = array();
	
	foreach($seller_ids as $ids) {
        //seller details
        $seller = $this->db->select("seller.seller_id, seller.seller_name, seller.contact_no, seller.address, seller.company_name, seller.latitude, seller.longitude")
                ->where("seller_id", $ids)
                ->get("seller")->row_array();   
	
	    $sellr = array(
		"id" => $seller["seller_id"],
		"name" => $seller["seller_name"],
		"mobileno" => $seller["contact_no"],
		"latitude" => $seller["latitude"],
		"longitude" => $seller["longitude"],
		"address" => $seller["address"],
		"company_name" => $seller["company_name"],
	    );
	    array_push($seller_arr, $sellr);
	}
	
        // insert driver for order       
        $ins1 = array(
            'driver_id' => $user_id,
            'order_id' => $order_id,
        );
        
        $check = $this->db->select("*")
                ->where($ins1)->get("order_driver")->result_array();
        if(empty($check)) {
            $this->db->insert("order_driver", $ins1);
        }
            
        $push1 = array(
            'to_user_id' => $user_id,
            'message' => 'An Order Placed by '.$get_ltlg["firstname"].' '.$get_ltlg["lastname"],   
            'notification_type' => 10,
            'driver_id' => $user_id,
            'order_id' => $order_id,
            'customer_id' => $get_ltlg["user_id"],
        );

        $this->m_api->create_notification($push1);  

        $push2 = array(
            'order_no' => $get_order["order_no"],
            'order_date' => $get_order["order_date"],
            'net_amount' => $get_order["net_amount"],
            'user' => array(
                'id' => $get_ltlg["user_id"],
                'name' => $get_ltlg["firstname"].' '.$get_ltlg["lastname"],
                'mobileno' => $get_ltlg["mobileno"],
                'address' => $address,
                'latitude' => $get_ltlg["latitude"],
                'longitude' => $get_ltlg["longitude"]
            ),
            'seller' => $seller_arr
        );

        $push = array_merge($push1, $push2);

        //print_r($push); exit;
        $this->m_notifyd->send($push); 

        if($push){
            $this->response = array(
                "status" => "true",
                "details" => $push
            );
            echo json_encode($this->response);
        }
        else {
            $this->response = array(
                "status" => "false",
                "response_msg" => "no data found"
            );
            echo json_encode($this->response);
        }
    }
    
    public function repeat_order(){
        header("Content-Type: application/json");
        $post = $_POST; 
	
	$get_orders = $this->db->select("*")->where("user_id", $post["user_id"])->where("order_id", $post["order_id"])->where("status", 1)->get("repeat_orders")->result_array();

	if(!empty($get_orders)) {	   
	    foreach($get_orders as $key => $value) {
		
		$today_date = strtotime(date('Y-m-d H'));
		$notified_date = strtotime(date('Y-m-d H', strtotime($value["to_be_notified_on"])));
		
//		echo date('Y-m-d H');
//		echo " <br> ";
//		echo date('Y-m-d H', strtotime($value["to_be_notified_on"]));
		
		if($today_date == $notified_date) {
		    $push = array(
			'to_user_id' => $value["user_id"],
			'order_id' => $value["order_id"],
			'message' => 'Do you want to repeat this order?',   
			'notification_type' => 24,
			'is_notified' => 1
		    );
		    
		    $this->m_api->create_notification($push);  
		    $this->m_notify->send($push);
		    
		    $this->db->set("is_notified", 1)
			    ->where("user_id", $value["user_id"])
			    ->where("order_id", $value["order_id"])
			    ->where("status", 1)
			    ->update("repeat_orders");
		}
	    }
	    
	    $this->response = array(
                "status" => "true",
                "response_msg" => "notification sent."
            );
            echo json_encode($this->response);
	}
        else {
            $this->response = array(
                "status" => "false",
                "response_msg" => "notification not sent."
            );
            echo json_encode($this->response);
        }
    }
    
    public function get_seller_slot(){
	header("Content-Type: application/json");
	$push = $this->m_api->get_seller_slot2 ('8,9,13'); 

        if($push){
            $this->response = array(
                "status" => "true",
                "details" => $push
            );
            echo json_encode($this->response);
        }
        else {
            $this->response = array(
                "status" => "false",
                "response_msg" => "no data found"
            );
            echo json_encode($this->response);
        }
    }
}


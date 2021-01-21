<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Apid extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('m_apid');
        $this->load->model('m_notify');
        $this->load->model('m_notifyd');
	$this->load->model('cron_model');
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
    
    /**
    * @apiGroup API
    * @api {POST} https://drinxin.com/ws/v1/apid/ 1. Api Url
    * 
    */
    
    /**
    * @apiGroup API
    * @api {POST} {Notificationtypes} 2. Notification Type
    *
    * @apiParam 1  Order placed
    * @apiParam 2  Send by admin
    * @apiParam 3  Order delivered
    * @apiParam 4  Gift card sent
    * @apiParam 5  Age verified
    * @apiParam 6  Seller verification
    * @apiParam 7  Order rejected by seller
    * @apiParam 8  Product return request decline
    * @apiParam 9  Order cancellation request decline
    * @apiParam 10  Order Notification to Driver
    * @apiParam 11  Order accepted by driver
    * @apiParam 12  Order accepted by seller
    * @apiParam 13  Order picked up
    * @apiParam 14  Driver started delivery
    * @apiParam 15  Order arrived / Driver end delivery
    * @apiParam 16  Driver halted
    * @apiParam 17  Order not completed
    * @apiParam 18  Order cancel by driver
    * @apiParam 19  Notify driver to pickup order from seller
    * @apiParam 20  Profile Update Request Accepted
    * @apiParam 21  Profile Update Request Rejected
    * @apiParam 22  Vehicle Details Update Request Accepted
    * @apiParam 23  Vehicle Details Update Request Rejected
    * @apiParam 24  Repeat Order
    * @apiParam 25  Chat message
    */ 

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
            http_response_code(401);
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
            http_response_code(401);            
            echo json_encode($this->response);
            return false;
        } else {
            return $_POST['user_id'];
        }
    }

    public function validate_token($user_id, $token) {
        $userdata = $this->m_apid->get_user_by_user_id_token($user_id, $token);
        if ($userdata) {
            return $userdata;
        } else {
            $this->response = array(
                'status' => 'false',
                'screen_code' => '1001',
                'response_msg' => 'User authentication failed. Token mismatch.',
            );
            http_response_code(401);            
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
            http_response_code(400);
            echo json_encode($this->response);
        }
        return $return;
    }

    public function display_system_error() {
        $this->response = array(
            'status' => 'false',
            'response_msg' => 'Server error. Something went wrong.',
        );
        http_response_code(400);
        echo json_encode($this->response);
        die;
    }    
    
    /**
    * @apiGroup API
    * @api {post} update_device_token Update Device Token
    *
    * @apiParam {Number} user_id User ID
    * @apiParam {String} device_type Device Type
    * @apiParam {String} device_token Device Token
    * @apiParam {String} app_version App Version
    * @apiParam {String} [device_id] Device ID
    * @apiParam {String} [device_name] Device Name
    */   
    public function update_device_token() {
        //$user_id, $device_type, $device_token, $device_id = '', $device_name = '', app_version = ''
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'device_type', 'device_token', 'app_version'];
            if ($this->check_parameters($required)) {
//check and update device token
                $this->m_apid->check_update_device_token($post);
                $this->response = array(
                    'status' => 'true',
                    'response_msg' => 'Device token updated.',
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
            //check exists
            $exists = $this->m_apid->check_user_by_email_mobile($email, $verify_data['mobileno']);

            if(!$exists) {
                $otp_data = $this->m_apid->generate_random_verification_code($verify_data['mobileno']);

                $check = $this->nexmo->sms($verify_data['mobileno'], $otp_data["sms_msg"]);
                //print_r($check); exit;
                //$check = $otp_data["delivery_receipt_id"];

                if (!empty($otp_data) && !empty($check)) {

                   // $this->m_apid->delivery_receipt($check, 'success');

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
            $res = $this->m_apid->check_delivery_status($post);
                       
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

            $otp_data = $this->m_apid->generate_random_verification_code($verify_data['mobileno']);
            
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

            $verify_otp_data = $this->m_apid->verify_scode($verify_data);      
     
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


            $userdata = $this->m_apid->verify_scode($verify_data);

            if ($userdata) {

                $this->m_apid->check_update_device_token($post);

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
        $status = $this->m_apid->verify($md5_user_id);
        if ($status) {
            if ($status == '1') {
                echo '<h1><font color="green">Your drinxin account is verified.</h1>';
            } else if ($status == '2') {
                echo '<h1><font color="green">User already verified.</h1>';
            }
        } else {
            echo '<h1><font color="red">User not registered.</h1>';
        }
    }
    
    public function resend_verification_mail() {
        header('Content-Type: application/json');
        $post = $_POST;
        if (isset($post['email']) && $post['email'] != null) {
            $userdata = $this->m_apid->get_user_by_email($post['email']);
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
                    $this->m_apid->send_mail($to, $subject, $msg);

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
    
    /**
    * @apiGroup API
    * @api {post} signin Signin / Login for Driver
    *
    * @apiParam {String} signin_username Login Email Address / Mobile Number
    * @apiParam {String} password Login Password
    */   
    public function signin() {
        header('Content-Type: application/json');
        $post = $_REQUEST;       

        if (isset($post['signin_username']) && $post['signin_username'] != null && isset($post['password']) && $post['password'] != null) {
            $userdata = $this->m_apid->signin($post);
            //print_r($userdata); exit;
            if ($userdata) {
                
                $this->m_apid->log_in($userdata['user_id']);
                
                $post['user_id'] = $userdata['user_id'];
                if ($userdata['token'] != '') {
                    $token = $userdata['token'];
                } else {
                    $token = md5(rand() . rand());
                }
                $this->m_apid->update_login_token($post['user_id'], $token);
//token is valid               
                $this->m_apid->check_update_device_token($post);

                $screen_code = $this->m_apid->check_profile_complition_and_get_screen_code($post['user_id']);
                if ($userdata['password_updated'] == 0) {
//111 display update password screen
                    $screen_code = '111';
                }          
                                
                $this->response = array(
                    'status' => 'true',
                    'response_msg' => 'Sign in Successful',
                    'token' => $token,
                    'screen_code' => $screen_code,
                    'user_id' => $userdata['user_id'],
                    'firstname' => $userdata['firstname'],
                    'lastname' => $userdata['lastname'],
                    'profile_image' => (!is_null($userdata['profile_image']) ? $userdata['profile_image'] : "") ,
                    'email' => $userdata['email'],
                    'maker' => $userdata["vehicle_details"]['maker'],
                    'model' => $userdata["vehicle_details"]['model'],
                    'registration_number' => $userdata["vehicle_details"]['registration_number'],
                    'is_admin_verified' => $userdata['is_admin_verified'],
                    'is_online' => $userdata['is_online'],
                    'vehicle_img_1' => $userdata['vehicle_details']["vehicle_img_1"],
                );     
                        
// $this->response = $this->response;
                echo json_encode($this->response);
            } else {
//invalid user
                $this->response = array(
                    'status' => 'false',
                    'response_msg' => 'Invalid email id / mobile no or password',
                );
                http_response_code(400);
                echo json_encode($this->response);
            }
        } else {
//enter username and password
            $this->response = array(
                'status' => 'false',
                'response_msg' => 'Please enter email/mobileno and password',
            );
            http_response_code(401);
            echo json_encode($this->response);
        }
    }        
    
    /**
    * @apiGroup API
    * @api {post} forgot_password Forgot Password
    *
    * @apiParam {String} email Login Email Address
    */
    public function forgot_password() {
        header('Content-Type: application/json');
        $post = $_POST;
        if (isset($post['email']) && $post['email'] != null) {
            $userdata = $this->m_apid->get_user_by_email($post['email']);
            if ($userdata) {
                if ($userdata['status'] == 0) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'This email is not verified, Please verify your account',
                        'screen_code' => '333', //verification email
                    );
                    http_response_code(401);
                    echo json_encode($this->response);
                } else {
                    $userdata['password'] = substr(md5(rand()), 1, 8);

                    if ($this->m_apid->generate_random_password($userdata['user_id'], $userdata['password'])) {
//send email           
                        //echo $userdata['email'];
                        $to = $userdata['email'];
                        $subject = 'Drinxin password recovery system.';
                        $msg = $this->load->view('mail_tmp/header', $userdata, true);
                        $msg .= $this->load->view('mail_tmp/forgot_password', $userdata, true);
                        $msg .= $this->load->view('mail_tmp/footer', $userdata, true);
                        $this->m_apid->send_mail($to, $subject, $msg);
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
                        http_response_code(401);
                        echo json_encode($this->response);
                    }
                }
            } else {
//Email id not registered
                $this->response = array(
                    'status' => 'false',
                    'response_msg' => 'Email is not registered',
                );
                http_response_code(404);
                echo json_encode($this->response);
            }
        } else {
//enter email
            $this->response = array(
                'status' => 'false',
                'response_msg' => 'Please enter email',
            );
            http_response_code(401);
            echo json_encode($this->response);
        }
    }

    /**
    * @apiGroup API
    * @api {post} change_password Change Password
    *
    * @apiParam {Number} user_id User ID
    * @apiParam {String} old_password Old Password
    * @apiParam {String} password New Password
    */
    public function change_password() {
        header('Content-Type: application/json');
        $post = $_POST;

        //print_r($post);
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'old_password', 'password'];
            if ($this->check_parameters($required)) {

                $post['user_id'] = $userdata['user_id'];
                $res = $this->m_apid->update_password($post);
		
                if ($res === 1) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Your password has been successfully updated',
                    );
                    echo json_encode($this->response);
                } 
		else if ($res === 2) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Your current password is wrong.',
                    );
                    http_response_code(400);
                    echo json_encode($this->response);
                }
		else if ($res === 3) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'System error. Password not updated.',
                    );
                    http_response_code(400);
                    echo json_encode($this->response);
                }
            }
        }
    }

    /**
    * @apiGroup API
    * @api {post} logout Logout
    *
    * @apiParam {Number} user_id User ID
    * @apiParam {String} device_token Device Token  
    */
    public function logout() {
        header('content-type:application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {
                $this->m_apid->delete_device_token($post);
                $this->m_apid->log_out($post["user_id"]);
                $this->response = array(
                    'status' => 'true',
                    'response_msg' => 'User logout successfully',
                );
                echo json_encode($this->response);
            }
        }
    }

    /**
    * @apiGroup API
    * @api {post} get_profile Get Driver Profile
    *
    * @apiParam {String} user_id User ID
    * @apiParam {String} profile_id Profile ID of the person whose profile is displayed
    */  
    public function get_profile() {
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'profile_id'];
            if ($this->check_parameters($required)) {

                $user_data = $this->m_apid->get_profile($post);

                if ($user_data) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'User data',
                        'user_data' => $user_data
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'User data is not available',
                    );
                    http_response_code(400);                    
                    echo json_encode($this->response);
                }
            }
        }
    }    
    
    /**
    * @apiGroup API
    * @api {post} about_us About Us
    */
    public function about_us() {
        $about_arr = $this->m_apid->get_about_us();
        echo $about_arr;
    }
    
    /**
    * @apiGroup API
    * @api {post} privacy_policy Privacy Policy
    */
    public function privacy_policy() {
        $privacy_arr = $this->m_apid->get_privacy_policy();
        echo $privacy_arr;
    }

    /**
    * @apiGroup API
    * @api {post} term_and_conditions Terms & Conditions
    */
    public function term_and_conditions() {
        $tmc_arr = $this->m_apid->get_term_condition();
        echo $tmc_arr;
    }

    /**
    * @apiGroup API
    * @api {post} faq_question_list FAQ Question List
    */
    public function faq_question_list() {
        header('Content-Type: application/json');
        $faq_arr = $this->m_apid->get_faq_question_list();
        //print_r($faq_arr); exit;

        $this->response = array(
            'status' => 'true',
            'response_msg' => 'FAQ list',                        
            'questions' => $faq_arr
        );
        echo json_encode($this->response);
    }

    /**
    * @apiGroup API
    * @api {post} faq_detail FAQ Details
    *
    * @apiParam {Number} faq_id FAQ Id  
    */
    public function faq_detail() {
        header('Content-Type: application/json');
        $post = $_POST;

        $required = ['faq_id'];
        if ($this->check_parameters($required)) {

            $faq_data = $this->m_apid->get_faq_details($post);

            if ($faq_data) {
                $this->response = array(
                    'status' => 'true',
                    'response_msg' => 'FAQ details',                        
                    'faq_data' => $faq_data
                );
                echo json_encode($this->response);
            } else {
                $this->response = array(
                    'status' => 'false',
                    'response_msg' => 'No data found',
                );
                http_response_code(400);
                echo json_encode($this->response);
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} feedback Feedback
    *
    * @apiParam {Number} user_id User ID
    * @apiParam {String} name Person Name
    * @apiParam {String} email Person Email
    * @apiParam {String} mobileno Person Mobile Number
    * @apiParam {String} subject Subject
    * @apiParam {String} message Message
    */  
    public function feedback() {       
        header('Content-Type: application/json');
        $post = $_POST;
        
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'name', 'email', 'mobileno', 'subject', 'message'];
            if ($this->check_parameters($required)) {                
            
                $order = $this->m_apid->feedback($post);
                
                if (!$order) {                    
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Message sent not successfully.',
                    );
                    http_response_code(400);                
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
    
    /**
    * @apiGroup API
    * @api {post} send_requests Driver request changes in profile details
    *
    * @apiParam {Number} user_id User ID
    * @apiParam {String} [firstname] Request to change first name
    * @apiParam {String} [lastname] Request to change last name
    * @apiParam {String} [birthdate] Request to change date of birth (format: YYYY-MM-DD)
    * @apiParam {Number} [mobileno] Request to change mobile no
    * @apiParam {String} [email] Request to change email id
    * @apiParam {String} [image] Request to change profile image
    */  
    public function send_requests() {
        header('Content-Type: application/json');
        $post = $_POST;
	$files = $_FILES;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {

                $user_data = $this->m_apid->send_requests($post, $files);

                if ($user_data === 1) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Request send', 
                    );
                    echo json_encode($this->response);
                } 
		else if ($user_data === 2) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Request not send',
                    );
                    http_response_code(400);                    
                    echo json_encode($this->response);
                }
		else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => $user_data,
                    );
                    http_response_code(400);                    
                    echo json_encode($this->response);
                }
            }
        }
    }   
    
    /**
    * @apiGroup API
    * @api {post} send_vehicle_requests Driver request changes in vehicle details
    *
    * @apiParam {Number} user_id User ID
    * @apiParam {String} vehicle_id vehicle id whose change request is updated
    * @apiParam {String} [car_name] Request to change car name ( modal name )
    * @apiParam {String} [vehicle_reg_no] Request to change vehicle registration number
    * @apiParam {String} [vehicle_make] Request to change vechile maker name
    * @apiParam {String} [ins_policy_no] Request to change vehicle insurance policy number
    * @apiParam {String} [ins_certificate_no] Request to change vehicle insurance certificate number
    * @apiParam {String} [images] Request to change vehicle images (pass in array)
    * @apiParam {String} [delete_image] Request to delete vehicle images (pass ids 1,2,3)
    */  
    public function send_vehicle_requests() {
        header('Content-Type: application/json');
        $post = $_POST;
	$files = $_FILES;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'vehicle_id'];
            if ($this->check_parameters($required)) {

                $user_data = $this->m_apid->send_vehicle_requests($post, $files);

                if ($user_data === 1) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Request send', 
                    );
                    echo json_encode($this->response);
                } 
		else if ($user_data === 2) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Request not send',
                    );
                    http_response_code(400);                    
                    echo json_encode($this->response);
                }
		else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => $user_data,
                    );
                    http_response_code(400);                    
                    echo json_encode($this->response);
                }
            }
        }
    }   
    
    /**
    * @apiGroup API
    * @api {post} update_docs Update driver verification documents & vehicle information
    *
    * @apiParam {Number} user_id User ID
    * @apiParam {String} [vehicle_img] Vehicle image (max 2 images) (pass in array) (gif|jpg|png|jpeg)
    * @apiParam {String} [maker] Maker
    * @apiParam {String} [registration_number] Registration Number
    * @apiParam {String} [model] Model
    * @apiParam {Number} [vehicle_policy_number] Vehicle Policy Number
    * @apiParam {String} [driver_verify_doc] Driver verification documents (max 6) (pass in array) (gif|jpg|png|jpeg|pdf)
    * @apiParam {String} [vehicle_ins_policy] Driver's vehicle insurance policy (pdf)
    */ 
    public function update_docs() {
        header('Content-Type: application/json');
        $post = $_POST;
        $files = $_FILES;
        $userdata = $this->auth();
        if ($userdata) {

            $required = ['user_id'];
            if ($this->check_parameters($required)) {
                $user_data = $this->m_apid->update_docs($post, $files);
                //print_r($user_data);
                if ($user_data[0] === 2) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'Profile Updated',
                        'user_data' => $user_data[1]
                    );
                    echo json_encode($this->response);
                }
		else if ($user_data[0] === 1) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => $user_data[1],
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                }
		else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Profile not updated',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} update_user_location Update driver current location
    *
    * @apiParam {Number} user_id Users Id.
    * @apiParam {String} latitude Users current location latitude.
    * @apiParam {String} longitude Users current location longitude.
    */
    public function update_user_location(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'latitude', 'longitude'];
            if ($this->check_parameters($required)) {
                $data = $this->m_apid->update_user_location($post);
                
                if ($data[0] === 1) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'location updated',
			'session' => $data[1]
                    );
                    echo json_encode($this->response);
                } 
		else if ($data[0] === 2) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'location updated',
			'session' => $data[1]
                    );
                    echo json_encode($this->response);
                } 
                else if ($data[0] === 3) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'location not updated',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} driver_online_status Update driver status on/off
    *
    * @apiParam {Number} user_id Users Id.
    * @apiParam {Number} is_online On/off status - 0 = offline, 1 = online
    */
    public function driver_online_status(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'is_online'];
            if ($this->check_parameters($required)) {
                $data = $this->m_apid->driver_online_status($post);
                
                if ($data === 1) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'driver is online',
                    );
                    echo json_encode($this->response);
                } 
                else if ($data === 2) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'driver is offline',
                    );
                    echo json_encode($this->response);
                } 
                else if ($data === 3) {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'no data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} delete_image Delete driver / vehicle image
    *
    * @apiParam {Number} user_id Users Id.
    * @apiParam {Number} image_name Name of image to delete
    * @apiParam {Number} type Type of image - 1 = Driver / 2 = Vehicle / 3 = Insurance Policy
    */
    public function delete_image(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'image_name', 'type'];
            if ($this->check_parameters($required)) {
                $data = $this->m_apid->delete_image($post);
                
                if ($data) {
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'image deleted',
                    );
                    echo json_encode($this->response);
                } 
                else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'image not deleted',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} get_notification_list Notification list
    *
    * @apiParam {Number} user_id Users Id.
    * @apiParam {Number} offset Offset (default pass 0 )
    */
    public function get_notification_list() {

        header('Content-Type: application/json');

        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'offset'];
            if ($this->check_parameters($required)) {
                $post = $_POST;
//check and update device token
                $post['limit'] = '20';
                if (!isset($post['offset']) || !$post['offset']) {
                    $post['offset'] = '0';
                }
                $notification_list = $this->m_apid->get_notification_list($post);
                
                if ($notification_list) {
                    $this->response = array(
                        'status' => 'true',
                        'offset' => ($post['limit'] + $post['offset']),
                        'notification_list' => $notification_list
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No notifications.',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} get_notification Notification list
    *
    * @apiParam {Number} user_id Users Id.
    * @apiParam {Number} offset Offset (default pass 0 )
    */
    public function get_notification() {

        header('Content-Type: application/json');

        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'offset'];
            if ($this->check_parameters($required)) {
                $post = $_POST;
//check and update device token
                $post['limit'] = '20';
                if (!isset($post['offset']) || !$post['offset']) {
                    $post['offset'] = '0';
                }
                $notification_list = $this->m_apid->get_notification_list($post);
                
                if ($notification_list) {
                    $this->response = array(
                        'status' => 'true',
                        'offset' => ($post['limit'] + $post['offset']),
                        'notification_list' => $notification_list
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No notifications.',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} accept_reject_order Driver accept / reject order
    *
    * @apiParam {Number} user_id Users Id.
    * @apiParam {Number} status Type - 1 = accept, 2 = reject
    * @apiParam {Number} order_id Order id
    */
    public function accept_reject_order(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'status', 'order_id'];
            if ($this->check_parameters($required)) {
                $data = $this->m_apid->accept_reject_order($post);
                
                if (!empty($data)) {
                    if ($data === 1) {
                        $this->response = array(
                            'status' => 'true',
                            'response_msg' => 'Order accepted.',
                        );
                        echo json_encode($this->response);
                    } 
                    else if ($data === 2) {
                        $this->response = array(
                            'status' => 'true',
                            'response_msg' => 'Order Rejected.',
                        );
                        echo json_encode($this->response);
                    }
                } 
                else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} order_cancel_reason_list Order cancel reason list
    *
    * @apiParam {Number} user_id Users Id.
    * @apiParam {Number} [offset] Offset (default pass 0 )
    */
    public function order_cancel_reason_list() {

        header('Content-Type: application/json');

        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {
                $post = $_POST;
//check and update device token
                $post['limit'] = '20';
                if (!isset($post['offset']) || !$post['offset']) {
                    $post['offset'] = '0';
                }
                $data = $this->m_apid->order_cancel_reason_list($post);
                
                if ($data) {
                    $this->response = array(
                        'status' => 'true',
                        'offset' => ($post['limit'] + $post['offset']),
                        'order_cancel_reason_list' => $data
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} order_not_completed_reason_list Order not completed reason list
    *
    * @apiParam {Number} user_id Users Id.
    * @apiParam {Number} [offset] Offset (default pass 0 )
    */
    public function order_not_completed_reason_list() {

        header('Content-Type: application/json');

        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {
                $post = $_POST;
//check and update device token
                $post['limit'] = '20';
                if (!isset($post['offset']) || !$post['offset']) {
                    $post['offset'] = '0';
                }
                $data = $this->m_apid->order_not_completed_reason_list($post);
                
                if ($data) {
                    $this->response = array(
                        'status' => 'true',
                        'offset' => ($post['limit'] + $post['offset']),
                        'order_not_completed_reason_list' => $data
                    );
                    echo json_encode($this->response);
                } else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} update_order_status Driver update order status
    *
    * @apiParam {Number} user_id Users Id.
    * @apiParam {Number} status Type - 9 = pickup, 10 = start delivery, 11 = end delivery, 12 = pause, 13 = not completed, 4 = delivered
    * @apiParam {Number} order_id Order id
    * @apiParam {String} [reason] Order cancel / not completed reason
    * @apiParam {String} [other_reason] Reason for other for - not completed status
    * @apiParam {String} [delivery_date] Date or order delivered ( compulsory to added when order status is delivered) ( format: YYYY-MM-DD HH:ii:ss )
    * @apiParam {String} [distance_traveled] Total distance traveled by driver (in miles)
    * @apiParam {String} [duration_taken] Total time taken by driver (HH:ii:ss)
    */
    public function update_order_status(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'status', 'order_id'];
            if ($this->check_parameters($required)) {
                $data = $this->m_apid->update_order_status($post);
                
                if (!empty($data)) {                    
		    $this->response = array(
			'status' => 'true',
			'response_msg' => 'status updated',
		    );
		    echo json_encode($this->response);                     
                } 
                else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'no order found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} order_details Order details
    *
    * @apiParam {Number} user_id Users Id.
    * @apiParam {Number} order_id Order id
    * @apiParam {Number} latitude Driver Latitude
    * @apiParam {Number} longitude Driver Longitude
    */
    public function order_details(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'order_id'];
            if ($this->check_parameters($required)) {
                $data = $this->m_apid->order_details($post);
                
                if (!empty($data)) {                    
		    $this->response = array(
			'status' => 'true',
			'order_details' => $data,
		    );
		    echo json_encode($this->response);
                } 
                else {
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} add_target Add target for driver
    *
    * @apiParam {Number} user_id Users Id.
    * @apiParam {Number} target_type Type of target set (1 = Daily, 2 = Weekly, 3 = Monthly)
    * @apiParam {Number} target_amount Amount
    * @apiParam {Number} start_date Start date of target ( FORMAT - YYYY-MM-DD )
    * @apiParam {Number} [end_date] End date of target ( FORMAT - YYYY-MM-DD )
    */
    public function add_target(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'target_type', 'target_amount', 'start_date'];
            if ($this->check_parameters($required)) {
                $data = $this->m_apid->add_target($post);
                
                if ($data === 1) { 
		    if($post["target_type"] == 1) {
			$for = "Daily";
		    }
		    else if($post["target_type"] == 2) {
			$for = "Weekly";
		    }
		    else if($post["target_type"] == 3) {
			$for = "Monthly";
		    }
		    
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Your have already set a target for '.$for.'',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                }
                else if ($data === 2) {                    
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Target not set.',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
		else { 
                    $this->response = array(
                        'status' => 'true',
                        'details' => $data,
                    );                                       
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} get_target Get target set by driver
    *
    * @apiParam {Number} user_id Users Id.
    */
    public function get_target(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {		
                $data = $this->m_apid->get_target($post); 
		
                if (!empty($data)) { 
                    $this->response = array(
                        'status' => 'true',
                        'details' => $data,
                    );                                       
                    echo json_encode($this->response);
                }
                else {                    
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} get_bookings Get bookings accepted by driver
    *
    * @apiParam {Number} user_id Users Id.
    * @apiParam {Number} type Type of booking (1 = Past, 2 = Upcoming, 3 = Cancelled)
    * @apiParam {Number} offset Offset (pass 0 if no need to send offset)
    */
    public function get_bookings(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'type'];
            if ($this->check_parameters($required)) {
		
		$post['limit'] = '20';
		if (!isset($post['offset']) || !$post['offset']) {
		    $post['offset'] = '0';
		}
	
                $data = $this->m_apid->get_bookings($post); 
		
                if (!empty($data)) { 
                    $this->response = array(
                        'status' => 'true',
                        'bookings' => $data,
			'offset' => ($post['limit'] + $post['offset'])
                    );                                       
                    echo json_encode($this->response);
                }
                else {                    
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} add_schedule_time Add schedule working hours of driver
    *
    * @apiParam {Number} user_id Users Id.
    * @apiParam {String} start_date Schedule start date (YYYY-MM-DD)
    * @apiParam {String} end_date Schedule end date (YYYY-MM-DD) ( pass same date as start date if want to pass for single date)
    * @apiParam {String} start_time Schedule start working time (HH:ii:ss)
    * @apiParam {String} end_time Schedule end working time (HH:ii:ss)
    */
    public function add_schedule_time(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'start_date', 'end_date', 'start_time', 'end_time'];
            if ($this->check_parameters($required)) {		
                $data = $this->m_apid->add_schedule_time($post); 
		
                if ($data === 1) { 
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'added',
                    );                                       
                    echo json_encode($this->response);
                }
                else {                    
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Already scheduled on '.$data,
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} update_schedule_time Update schedule working hours of driver
    *
    * @apiParam {Number} user_id Users Id
    * @apiParam {Number} sch_id Schedule Id
    * @apiParam {String} [schedule_date] Schedule date (YYYY-MM-DD)
    * @apiParam {String} [start_time] Schedule start working time (HH:ii:ss)
    * @apiParam {String} [end_time] Schedule end working time (HH:ii:ss)
    */
    public function update_schedule_time(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'sch_id'];
            if ($this->check_parameters($required)) {		
                $data = $this->m_apid->update_schedule_time($post); 
		
                if ($data === 3) {               
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'no data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
		else if ($data) { 
                    $this->response = array(
                        'status' => 'true',
                        'details' => $data,
                    );                                       
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} get_schedule_time Get schedule working hours of driver listing
    *
    * @apiParam {Number} user_id Users Id
    * @apiParam {String} schedule_date Schedule date (YYYY-MM-DD)
    */
    public function get_schedule_time(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'schedule_date'];
            if ($this->check_parameters($required)) {		
                $data = $this->m_apid->get_schedule_time($post); 
		
                if (!empty($data)) { 
                    $this->response = array(
                        'status' => 'true',
                        'schedule_time' => $data,
                    );                                       
                    echo json_encode($this->response);
                }
		else {               
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'no data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} delete_schedule_time Delete schedule working hours of driver
    *
    * @apiParam {Number} user_id Users Id
    * @apiParam {Number} sch_id Schedule Id
    */
    public function delete_schedule_time(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'sch_id'];
            if ($this->check_parameters($required)) {		
                $data = $this->m_apid->delete_schedule_time($post); 
		
                if ($data === 1) { 
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'deleted',
                    );                                       
                    echo json_encode($this->response);
                }
                else if ($data === 2) {               
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'not delete',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
		else if ($data === 3) {               
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'no data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} bank_details Get bank details added from driver
    *
    * @apiParam {Number} user_id Users Id.
    */
    public function bank_details(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {		
                $data = $this->m_apid->bank_details($post); 
		
                if (!empty($data)) { 
                    $this->response = array(
                        'status' => 'true',
                        'details' => $data,
                    );                                       
                    echo json_encode($this->response);
                }
                else {                    
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} add_card Add new bank / card details
    *
    * @apiParam {Number} user_id User Id
    * @apiParam {String} [email] User Email ID
    * @apiParam {String} account_number User account number
    * @apiParam {String} [routing_number] User routing number
    * @apiParam {String} bank_name User bank name
    * @apiParam {String} account_holder_name Account holder name
    * @apiParam {String} sort_code Account sort code
    * @apiParam {String} card_color User Card color
    * @apiParam {Number} is_primary Whether user want to make this account primary or not? ( 1 = Yes, 0 = No )
    */
    public function add_card(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'account_number', 'is_primary'];
            if ($this->check_parameters($required)) {		
                $data = $this->m_apid->add_card($post); 
		
                if ($data[0] === 1) { 
                    $this->response = array(
                        'status' => 'true',
                        'details' => $data[1],
                    );                                       
                    echo json_encode($this->response);
                }
                else if ($data[0] === 2) {                     
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => $data[1],
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
		else if ($data[0] === 3) {                     
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => $data[1],
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
		else if ($data[0] === 4) {                     
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => $data[1],
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
		else if ($data[0] === 5) {                     
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => $data[1],
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
		else if ($data[0] === 6) {                     
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => $data[1],
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} statistics Get driver statistics till now
    *
    * @apiParam {Number} user_id Users Id.
    */
    public function statistics(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {		
                $data = $this->m_apid->statistics($post); 
		
                if (!empty($data)) { 
                    $this->response = array(
                        'status' => 'true',
                        'details' => $data,
                    );                                       
                    echo json_encode($this->response);
                }
                else {                    
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} accepted_deliveries Get total accepted deliveries by driver
    *
    * @apiParam {Number} user_id Users Id.
    * @apiParam {Number} month Month of deliveries in number( 11 )
    * @apiParam {Number} year Year of deliveries in number( 2019 )
    */
    public function accepted_deliveries(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'month', 'year'];
            if ($this->check_parameters($required)) {		
                $data = $this->m_apid->accepted_deliveries($post); 
		
                if (!empty($data)) { 
                    $this->response = array(
                        'status' => 'true',
                        'details' => $data,
                    );                                       
                    echo json_encode($this->response);
                }
                else {                    
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} accept_delivery_detail Get accepted delivery details by driver
    *
    * @apiParam {Number} user_id Users Id
    * @apiParam {String} adate Date of accepted delivery (Format: YYYY-MM-DD)
    */
    public function accept_delivery_detail(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'adate'];
            if ($this->check_parameters($required)) {		
                $data = $this->m_apid->accept_delivery_detail($post); 
		
                if (!empty($data)) { 
                    $this->response = array(
                        'status' => 'true',
                        'details' => $data,
                    );                                       
                    echo json_encode($this->response);
                }
                else {                    
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
            }
        }
    }

    /**
    * @apiGroup API
    * @api {post} rejected_deliveries Get total rejected deliveries by driver
    *
    * @apiParam {Number} user_id Users Id.
    * @apiParam {Number} month Month of deliveries in number( 11 )
    * @apiParam {Number} year Year of deliveries in number( 2019 )
    */
    public function rejected_deliveries(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'month', 'year'];
            if ($this->check_parameters($required)) {       
                $data = $this->m_apid->rejected_deliveries($post); 
        
                if (!empty($data)) { 
                    $this->response = array(
                        'status' => 'true',
                        'details' => $data,
                    );                                       
                    echo json_encode($this->response);
                }
                else {                    
            $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
            }
        }
    }

    /**
    * @apiGroup API
    * @api {post} rejected_delivery_detail Get rejected delivery details by driver
    *
    * @apiParam {Number} user_id Users Id
    * @apiParam {String} adate Date of rejected delivery (Format: YYYY-MM-DD)
    */
    public function rejected_delivery_detail(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'adate'];
            if ($this->check_parameters($required)) {       
                $data = $this->m_apid->rejected_delivery_detail($post); 
        
                if (!empty($data)) { 
                    $this->response = array(
                        'status' => 'true',
                        'details' => $data,
                    );                                       
                    echo json_encode($this->response);
                }
                else {                    
            $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
            }
        }
    }

    /**
    * @apiGroup API
    * @api {post} earning Get total earning details by driver
    *
    * @apiParam {Number} user_id Users Id
    * @apiParam {Number} month Month of deliveries in number( 11 )
    * @apiParam {Number} year Year of deliveries in number( 2019 )
    */
    public function earning(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'month', 'year'];
            if ($this->check_parameters($required)) {       
                $data = $this->m_apid->earning($post); 
        
                if (!empty($data)) { 
                    $this->response = array(
                        'status' => 'true',
                        'details' => $data,
                    );                                       
                    echo json_encode($this->response);
                }
                else {                    
            $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
            }
        }
    }

    /**
    * @apiGroup API
    * @api {post} delete_account Delete driver bank account
    *
    * @apiParam {Number} user_id Users Id
    * @apiParam {Number} main_id Main id of row for bank details
    */
    public function delete_account(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'main_id'];
            if ($this->check_parameters($required)) {       
                $data = $this->m_apid->delete_account($post); 
        
                if ($data === 1) { 
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'account deleted.',
                    );                                       
                    echo json_encode($this->response);
                }
                else if ($data === 2) {                    
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'this is a primary account, and cannot delete primary account.',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
                else if ($data === 3) {                    
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'no data found.',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                }
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} make_primary_account Make bank account primary for driver
    *
    * @apiParam {Number} user_id Users Id
    * @apiParam {Number} main_id Main id of row for bank details
    */
    public function make_primary_account(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'main_id'];
            if ($this->check_parameters($required)) {       
                $data = $this->m_apid->make_primary_account($post); 
        
                if ($data === 1) { 
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'account set has primary account.',
                    );                                       
                    echo json_encode($this->response);
                }
                else if ($data === 2) {                    
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'no data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} view_requests View request made by driver for profile & vehicle details
    *
    * @apiParam {Number} user_id Users Id
    * @apiParam {Number} type Type of request (1 = Profile details request, 2 = vehicle request)
    * @apiParam {Number} offset Offset (default pass 0 )
    */
    public function view_requests(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'type'];
            if ($this->check_parameters($required)) { 
		
		$post['limit'] = '20';
		if (!isset($post['offset']) || !$post['offset']) {
		    $post['offset'] = '0';
		}
		
                $data = $this->m_apid->view_requests($post); 
        
                if ($data) { 
                    $this->response = array(
                        'status' => 'true',
                        'response' => $data,
			'offset' => ($post['limit'] + $post['offset'])
                    );                                       
                    echo json_encode($this->response);
                }
                else {                    
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'no data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} wallet Get wallet details of driver
    *
    * @apiParam {Number} user_id Users Id
    */
    public function wallet(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id'];
            if ($this->check_parameters($required)) {       
                $data = $this->m_apid->wallet($post); 
        
                if (!empty($data)) { 
                    $this->response = array(
                        'status' => 'true',
                        'details' => $data,
                    );                                       
                    echo json_encode($this->response);
                }
                else {                    
            $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} withdraw_amount Withdraw amount from driver's wallet
    *
    * @apiParam {Number} user_id Users Id
    * @apiParam {Number} amount amount to be withdrawn
    */
    public function withdraw_amount(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'amount'];
            if ($this->check_parameters($required)) {       
                $data = $this->m_apid->withdraw_amount($post); 
        
                if ($data === 1) { 
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => $post["amount"].' amount withdrawn from your wallet.',
                    );                                       
                    echo json_encode($this->response);
                }
                else if ($data === 2) {                     
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'Your wallet balance is less than the amount you want to withdraw.',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
		else if ($data === 3) {                     
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
		else {                     
		    $this->response = array(
                        'status' => 'false',
                        'response_msg' => $data,
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} schedule_list Get all sessions of driver
    *
    * @apiParam {Number} user_id Users Id
    * @apiParam {Number} month Month of deliveries in number( 11 )
    * @apiParam {Number} year Year of deliveries in number( 2019 )
    */
    public function schedule_list(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'month', 'year'];
            if ($this->check_parameters($required)) {       
                $data = $this->m_apid->schedule_list($post); 
        
                if (!empty($data)) { 
                    $this->response = array(
                        'status' => 'true',
                        'details' => $data,
                    );                                       
                    echo json_encode($this->response);
                }
                else {                    
            $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} view_request_by_id View request made by driver by id
    *
    * @apiParam {Number} user_id Users Id
    * @apiParam {Number} type Type of request (1 = Profile details request, 2 = vehicle request)
    * @apiParam {Number} request_id Request id
    */
    public function view_request_by_id(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'type', 'request_id'];
            if ($this->check_parameters($required)) { 
		
		$data = $this->m_apid->view_request_by_id($post); 
        
                if ($data) { 
                    $this->response = array(
                        'status' => 'true',
                        'response' => $data,
                    );                                       
                    echo json_encode($this->response);
                }
                else {                    
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'no data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} cancel_target Cancel target added by driver
    *
    * @apiParam {Number} user_id Users Id
    * @apiParam {Number} target_type Type of target set (1 = Daily, 2 = Weekly, 3 = Monthly)
    */
    public function cancel_target(){
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'target_type'];
            if ($this->check_parameters($required)) { 
		
		$data = $this->m_apid->cancel_target($post); 
        
                if ($data) { 
                    $this->response = array(
                        'status' => 'true',
                        'response_msg' => 'deleted',
                    );                                       
                    echo json_encode($this->response);
                }
                else {                    
                    $this->response = array(
                        'status' => 'false',
                        'response_msg' => 'no data found',
                    );
                    http_response_code(400);                                        
                    echo json_encode($this->response);
                } 
            }
        }
    }
    
    /**
    * @apiGroup API
    * @api {post} send_a_msg Send message to user
    *
    * @apiParam {Number} user_id Users Id
    * @apiParam {Number} to_user_id  User of the person to whom message is to be sent
    * @apiParam {String} message  Message
    * @apiParam {Number} order_id Order Id
    */
    public function send_a_msg(){
        header("Content-Type: application/json");
        $post = $_POST;
        
        $userdata = $this->auth();
        if($userdata) {
            
            $required = ['user_id', 'to_user_id', 'message', 'order_id'];
            if($this->check_parameters($required)) {
		
                $data = $this->m_apid->send_a_msg($post);
                
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
		    http_response_code(400);           
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
            
                $user = $this->m_apid->get_user_conversation_list($post);

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
		    http_response_code(400);   
                }
            }
        }        
    }
    
    /**
    * @apiGroup API
    * @api {post} get_conversation_msg Get conversation messages between user & driver
    *
    * @apiParam {Number} user_id Users Id
    * @apiParam {Number} to_user_id  User of the person to whom message is to be sent
    * @apiParam {String} offset  
    * @apiParam {Number} order_id Order Id
    */
    public function get_conversation_msg() {
        header('Content-Type: application/json');       
        $post=$_POST;
        
        $userdata = $this->auth();
        if ($userdata) {      
            
            $required = ['user_id', 'to_user_id', 'offset', 'order_id'];
            if ($this->check_parameters($required)) {                
            
                $unread = $this->m_apid->get_unread_msg_count($post["to_user_id"], $post["user_id"]); 
                $user = $this->m_apid->get_conversation_msg($post);
                //$sender = $this->m_apid->get_user_details($post["user_id"], $post["to_user_id"]);
               // $receiver = $this->m_apid->get_user_details($post["to_user_id"], $post["user_id"]); 
               
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
		    http_response_code(400);      
                }
            }
        }        
    }
    
}
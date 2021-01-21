<?php error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(dirname(__FILE__).'/Authentication.php'); //include controller

class Login extends Authentication{

    /**
    * @apiGroup Login API
    * @api {POST} https://api.wadio.app/v1/auth/ 1. Api Url
    *
    * @apiParam 202 - Already registered, please check your email and verify account - signup - screencode (333)
    * @apiParam 203 - Email already exists - signup - screencode(777, 444)
    * @apiParam 209 - account is closed
    * @apiParam 206 - password reset after signin - screencode(111)
    * @apiParam 307 - Update profile (222)
    * @apiParam 401 - Authentication failed
    * @apiParam 404 - Not Found
    * @apiParam 400 - Bad Request
    * @apiParam 200 - OK
    */

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        //load model
        $this->load->model(basename(__DIR__)."/login_model");
        $this->load->model(basename(__DIR__)."/user_model");
        $this->load->model(basename(__DIR__)."/dataset_model");
        $this->load->model(basename(__DIR__)."/notification_model");

        // $read_db = $this->load->database('read', TRUE);
        // $write_db = $this->load->database('write', TRUE);
    }

    /**
    * @apiGroup Login API
    * @api {post} signup Sign Up
    *
    * @apiParam {String} email Email.
    * @apiParam {String} password Password.
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function signup_post() {
        header('Content-Type: application/json');
        $post = $_POST;
        $required = ['email', 'password'];

        $check_para = $this->check_parameters($required);

        if($check_para) {

            if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
                $this->response(['status' => 'false', 'response_msg' => 'Invalid email address'], 400);

                echo json_encode($this->response); exit;
            }
            $userdata = $this->login_model->get_nuser_by_email($post['email']);


            if ($userdata) {
                if ($userdata['status'] == '1') {
                    $this->response(['status' => 'false', 'response_msg' => 'Email already exists'], 203);

                    echo json_encode($this->response);
                }
                else {
                    $post['user_id'] = $userdata['user_id'];
                    if ($userdata['token'] != '') {
                        $token = $userdata['token'];
                    } else {
                        //$token = md5(rand() . rand());
                        $token = $this->generate_jwt_user_token($post['user_id']);
                    }

                    $this->login_model->update_login_token($post['user_id'], $token);
                    //token is valid
                    $this->login_model->check_update_device_token($post);

                    $screen_code = $this->login_model->check_profile_complition_and_get_screen_code($post['user_id']);

                    if ($userdata['password_temp'] == sha1($post['password']) && $userdata["is_password_reset"] == 1) {
                        $screen_code = '206';
                    }

                    $to = $userdata['email'];
                    $subject = 'Welcome to Wadio';
                    $msg = $this->load->view('mail_tmp/header', $userdata, true);
                    $msg .= $this->load->view('mail_tmp/welcome', $userdata, true);
                    $msg .= $this->load->view('mail_tmp/footer', $userdata, true);
                    $this->dataset_model->send_mail($to, $subject, $msg);

                    $res = array(
                        'status' => 'false',
                        'response_msg' => 'Already registered, please check your email and verify account'
                    );

                    $this->response($res, $screen_code);
                    echo json_encode($this->response);
                }
            }
            else {
                $signup_data = ['email', 'password'];
                $userdata = $this->login_model->signup($signup_data);
                if ($userdata) {
                    $post['user_id'] = $userdata['user_id'];

                    if ($userdata['token'] != '') {
                        $token = $userdata['token'];
                    } else {
                        // $token = md5(rand() . rand());
                        $token = $this->generate_jwt_user_token($post['user_id']);
                    }
                    $this->login_model->update_login_token($post['user_id'], $token);
                    //token is valid
                    $this->login_model->check_update_device_token($post);


                    $to = $userdata['email'];
                    $subject = 'Welcome to Wadio';
                    $msg = $this->load->view('mail_tmp/header', $userdata, true);
                    $msg .= $this->load->view('mail_tmp/welcome', $userdata, true);
                    $msg .= $this->load->view('mail_tmp/footer', $userdata, true);
                    $this->dataset_model->send_mail($to, $subject, $msg);

                    $res = array(
                        'status' => 'true',
                        'response_msg' => 'Registration successful, please check your email and verify account',
                        //'screen_code' => '222',
                        'user_id' => $userdata['user_id'],
                        'token' => $token,
                        'email' => $userdata['email'],
                    );

                    $this->response($res, 202);
                    echo json_encode($this->response);
                }
                else {
                    $this->response(['status' => 'false', 'response_msg' => 'User registration failed.'], 400);

                    echo json_encode($this->response);
                }
            }
        }
    }

    public function verify_get($md5_user_id) {
        $status = $this->login_model->verify($md5_user_id);
        if ($status) {
            if ($status == '1') {
                $data["msg"] = 'You have confirmed your email,<br />
          now you can open the app and log in with <br />your username and
          password.';
            } else if ($status == '2') {
                $data["msg"] = 'User already verified,<br />
          now you can open the app and log in with <br />your username and
          password.';
            }
        } else {
            $data["msg"] = 'User not registered.';
        }

        $this->load->view('mail_tmp/email_verify', $data);
        echo "";
    }

    /**
    * @apiGroup Login API
    * @api {post} signin Sign In
    *
    * @apiParam {String} email Email.
    * @apiParam {String} password Password.
    * @apiParam {String} [device_type] Device Type.
    * @apiParam {String} [device_token] Device Token.
    * @apiParam {String} [app_version] App version.
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function signin_post() {
        header('Content-Type: application/json');
        $post = $_POST;
        //device_token, device_id, device_name,device_type, email, password
        if (!empty($post['email']) && !empty($post['password'])) {
            $userdata = $this->login_model->signin($post);
            // print_r($userdata);
            if ($userdata && $userdata['status'] == '3') {
                $this->response([
                    'status' => 'true',
                    'response_msg' => 'This account is closed.'
                ], 209);

                echo json_encode($this->response);
            }
            else if ($userdata && $userdata['status'] == '2') {
                $this->response([
                    'status' => 'true',
                    'response_msg' => 'To continue you must activate your account.',
                    //'screen_code' => '333', //verification email
                ], 202);

                echo json_encode($this->response);
            }
            else if($userdata["is_password_reset"] == 1 && $userdata['password_temp'] != sha1($post['password']) && $userdata['password'] != sha1($post['password']) ) {

                $this->response([
                    'status' => 'false',
                    'response_msg' => 'Invalid Username or password',
                ], 400);

                echo json_encode($this->response);
            }
            else if($userdata["is_password_reset"] == 0 && $userdata['password'] != sha1($post['password'])) {

                $this->response([
                    'status' => 'false',
                    'response_msg' => 'Invalid Username or password',
                ], 400);

                echo json_encode($this->response);
            }
            else if ($userdata) {
                $post['user_id'] = $userdata['user_id'];
               // if ($userdata['token'] != '') {
               //     $token = $userdata['token'];
               // } else {
               //     $token = md5(rand() . rand());
               // }
                $token = $this->generate_jwt_user_token($post['user_id']);
                // $token = md5(rand() . rand());
                $this->login_model->update_login_token($post['user_id'], $token);
                $this->login_model->check_update_device_token($post);
                $this->login_model->update_login_details($post);

                $screen_code1 = $this->login_model->check_profile_complition_and_get_screen_code($post['user_id']);

                $screen_code = $this->login_model->check_password_update_and_reset($post['user_id'], $screen_code1, $post['password']);

                $this->response([
                    'status' => 'true',
                    'response_msg' => 'Sign in Successful',
                    'token' => $token,
                    //'screen_code' => $screen_code,
                    'user_id' => $userdata['user_id'],
                    'email' => $userdata['email'],
                    'user_type' => $userdata['user_type_id']
                ], $screen_code);

                echo json_encode($this->response);
            }
            else {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'Please enter email / username and password',
                ], 404);

                echo json_encode($this->response);
            }
        }
        else {
            $this->response([
                'status' => 'false',
                'response_msg' => 'Please enter email / username and password',
            ], 400);

            echo json_encode($this->response);
        }
    }

    /**
    * @apiGroup Login API
    * @api {post} resend_verification_mail Resend Verification Mail
    *
    * @apiParam {String} email Email.
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function resend_verification_mail_post() {
        header('Content-Type: application/json');
        $post = $_POST;
        if (isset($post['email']) && $post['email'] != null) {
            $userdata = $this->login_model->get_nuser_by_email($post['email']);
            if ($userdata) {
                if ($userdata['status'] == 1 && $userdata['is_email_verified'] == 1) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'You have already verified your account, Please sign in',
                        'user_id' => $userdata['user_id'],
                        'email' => $userdata['email']
                    ], 200);
                    echo json_encode($this->response);
                }
                else {
                    $to = $userdata['email'];
                    $subject = 'Welcome to Wadio';
                    $msg = $this->load->view('mail_tmp/header', $userdata, true);
                    $msg .= $this->load->view('mail_tmp/welcome', $userdata, true);
                    $msg .= $this->load->view('mail_tmp/footer', $userdata, true);
                    $this->dataset_model->send_mail($to, $subject, $msg);

                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Please check your email and verify account'
                    ], 202);
                    echo json_encode($this->response);
                }
            } else {
                //System error cant able to generate email
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'Email is not registered',
                ], 404);

                echo json_encode($this->response);
            }
        }
        else {
            //enter email
            $this->response([
                'status' => 'false',
                'response_msg' => 'Please enter your email',
            ], 400);

            echo json_encode($this->response);
        }
    }

    /**
    * @apiGroup Login API
    * @api {post} forgot_password Forgot password
    *
    * @apiParam {String} email Email.
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function forgot_password_post() {
        header('Content-Type: application/json');
        $post = $_POST;
        if (isset($post['email']) && $post['email'] != null) {
            $userdata = $this->login_model->get_user_by_email_username($post['email']);
            //print_r($userdata); exit;
            if ($userdata === 1) {
                //Email id not registered
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'Email is not registered',
                ], 404);
                echo json_encode($this->response);
            }
            else if ($userdata === 2) {
                //Username id not registered
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'Username is not registered'
                ], 404);
                echo json_encode($this->response);
            }
            else if (!empty($userdata[0]) && $userdata[0] === 3) {
                //Email id not registered
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'Username is invalid',
                    'user_type' => $userdata[1]
                ], 400);
                echo json_encode($this->response);
            }
            else if (!empty($userdata[0]) && $userdata[0] === 4) {
                //Username id not registered
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'You cannot reset password with username. Please try with email id.',
                    'user_type' => $userdata[1]
                ], 400);
                echo json_encode($this->response);
            }
            else {
                if ($userdata['status'] == 0) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'Please verify your account by instructions we sent to your email address:' . $post['email'],
                        'user_type' => $userdata["user_type_id"],
                        'email' => $userdata["email"],
                    ], 202);
                    echo json_encode($this->response);
                }
                else {
                    $userdata['password'] = substr(md5(rand()), 1, 8);
                    // $userdata['password'] = '123456';

                    if ($this->login_model->generate_random_password($userdata['user_id'], $userdata['password'])) {
                        //send email
                        $to = $userdata['email'];
                        $subject = 'Wadio password recovery system.';
                        $msg = $this->load->view('mail_tmp/header', $userdata, true);
                        $msg .= $this->load->view('mail_tmp/forgot_password', $userdata, true);
                        $msg .= $this->load->view('mail_tmp/footer', $userdata, true);
                        $this->dataset_model->send_mail($to, $subject, $msg);
                        //email sent
                        $this->response([
                            'status' => 'true',
                            'response_msg' => '"Temporary password has been sent to ' . $post['email'] . ' use this password to sign in and set your new password',
                            'user_type' => $userdata["user_type_id"],
                            'email' => $userdata["email"],
                        ], 206);

                        echo json_encode($this->response);
                    }
                    else {
                        //System error cant able to generate new password
                        $this->response([
                            'status' => 'false',
                            'response_msg' => 'System error. System can not able to generate new password.',
                        ], 500);

                        echo json_encode($this->response);
                    }
                }
            }
        }
        else {
            //enter email
            $this->response([
                'status' => 'false',
                'response_msg' => 'Please enter your email/username',
            ], 400);

            echo json_encode($this->response);
        }
    }

    /**
    * @apiGroup Login API
    * @api {post} update_device_token Update Device Token
    *
    * @apiParam {String} device_type Device Type.
    * @apiParam {String} device_token Device Token.
    * @apiParam {String} device_id Device ID.
    * @apiParam {String} [app_version] App version.
    * @apiParam {String} [device_name] Device Name.
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function update_device_token_post() {
        header('Content-Type: application/json');
        $post = $_POST;

        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {

            $required = ['device_type', 'device_token'];
            $check_para = $this->check_parameters($required);

            if($check_para) {
                $post["user_id"] = $userdata["user_id"];
                $this->login_model->check_update_device_token($post);
                $this->response([
                    'status' => 'true',
                    'response_msg' => 'Device token updated.',
                ], 200);

                echo json_encode($this->response);
            }
        }
    }

    /**
    * @apiGroup Login API
    * @api {post} allow_facial_login Allow permission for facial login
    *
    * @apiParam {Number} is_allow (0 = no, 1 = yes).
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function allow_facial_login_post() {
        header('Content-Type: application/json');
        $post = $_POST;

        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {

            $required = ['is_allow'];
            $check_para = $this->check_parameters($required);

            if($check_para) {
                $post["user_id"] = $userdata["user_id"];
                $data = $this->login_model->allow_facial_login($post);
                if ($data) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Facial login permission updated'
                    ], 200);
                    echo json_encode($this->response);
                }
                else {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'No data found'
                    ], 404);
                    echo json_encode($this->response);
                }
            }
        }
    }

    /**
    * @apiGroup Login API
    * @api {post} change_password Change Password
    *
    * @apiParam {String} password
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function change_password_post() {
        header('Content-Type: application/json');
        $post = $_POST;

        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {

            $required = ['password'];

            $check_para = $this->check_parameters($required);

            if($check_para) {
                $post["user_id"] = $userdata["user_id"];
                $data = $this->login_model->update_password($post);
                $users = $this->user_model->get_user_by_id($userdata["user_id"]);

                if ($data) {
                    if ($users["is_email_verified"] == 1 && $users["is_profile_updated"] == 1) {
                        $screen_code = '200';
                    }
                    else if ($users["is_profile_updated"] == 0) {
                        $screen_code = '307';
                    }
                    else if ($users["is_password_updated"] == 1) {
                        $screen_code = '206';
                    }
                    else {
                        $screen_code = '202';
                    }

                    $res = array(
                        'status' => 'true',
                        'response_msg' => 'Your password has been successfully updated',
                        'user_id' => $users['user_id'],
                        'token' => $users['token'],
                        'email' => $users['email'],
                    );

                    $this->response($res, $screen_code);
                    echo json_encode($this->response);
                }
                else {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'Current password is wrong'
                    ], 400);
                    echo json_encode($this->response);
                }
            }
        }
    }

    /**
    * @apiGroup Login API
    * @api {post} logout Logout
    *
    * @apiParam {String} [device_token]
    * @apiParam {String} [ipaddress] last ipaddress
    * @apiParam {String} [latitude] last latitude
    * @apiParam {String} [longitude] last longitude
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function logout_post() {
        header('content-type:application/json');
        $post = $_POST;

        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {
            // $required = ['device_token'];

            // $check_para = $this->check_parameters($required);

            // if($check_para) {
                $post["user_id"] = $userdata["user_id"];
                $data = $this->login_model->delete_device_token($post);
                if ($data) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'User logout successfully'
                    ], 200);
                    echo json_encode($this->response);
                }
                else {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'No data found'
                    ], 404);
                    echo json_encode($this->response);
                }
            // }
        }
    }

    /**
    * @apiGroup Login API
    * @api {post} check_email_registered Check Email Registered
    *
    * @apiParam {String} email
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function check_email_registered_post() {
        header('Content-Type: application/json');
        $post = $_POST;

        $required = ['email'];
        $check_para = $this->check_parameters($required);

        if($check_para) {
            $userdata = $this->login_model->check_email_id_signup($post);

            if ($userdata === 1) {
                $this->response([
                    'status' => 'true',
                    'response_msg' => 'Email id / username already exists.',
                ], 203);
                echo json_encode($this->response);
            } else if ($userdata === 2) {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'Email id / username not registered.',
                ], 200);
                echo json_encode($this->response);
            } else if ($userdata === 3) {
                $this->response([
                    'status' => 'true',
                    'response_msg' => 'Email id not validated.',
                ], 203);
                echo json_encode($this->response);
            } else if ($userdata === 4) {
                $this->response([
                    'status' => 'true',
                    'response_msg' => 'Email id / username not exists.',
                ], 200);
                echo json_encode($this->response);
            } else if ($userdata === 5) {
                $this->response([
                    'status' => 'true',
                    'response_msg' => 'Please verify your account we sent to your email address.',
                ], 202);
                echo json_encode($this->response);
            }
        }
    }

    /**
    * @apiGroup Login API
    * @api {post} check_username_registered Check Username Registered
    *
    * @apiParam {String} username
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function check_username_registered_post() {
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {
        //     $required = ['username'];
        //     $check_para = $this->check_parameters($required);

        //     if($check_para) {

                $post["user_id"] = $userdata["user_id"];
                $userdata1 = $this->user_model->check_username_id($post);

                if ($userdata1 === 1) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'Username do not exists.',
                    ], 200);
                    echo json_encode($this->response);
                } else if ($userdata1 === 2) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Username already taken by you.',
                    ], 200);
                    echo json_encode($this->response);
                } else if ($userdata1 === 4) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'Username already exists.',
                    ], 203);
                    echo json_encode($this->response);
                } else if ($userdata1 === 3) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Username not registered.',
                    ], 200);
                    echo json_encode($this->response);
                }
                else if ($userdata === 5) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Please verify your account we sent to your email address.',
                    ], 202);
                    echo json_encode($this->response);
                }
        //     }
        }
    }

    /**
    * @apiGroup Login API
    * @api {post} change_mobile_number Change Mobile Number
    *
    * @apiParam {String} [phone_code] Phone Code
    * @apiParam {String} phone_number Phone Number
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function change_mobile_number_post() {
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {
            $required = ['phone_number'];
            $check_para = $this->check_parameters($required);

            if($check_para) {
                $post["user_id"] = $userdata["user_id"];
                $data = $this->login_model->change_mobile_number($post);

                if ($data) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Mobile number updated'
                    ], 200);
                    echo json_encode($this->response);
                }
                else {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'No data found'
                    ], 404);
                    echo json_encode($this->response);
                }
            }
        }
    }

    /**
    * @apiGroup Login API
    * @api {post} check_email_verified Check Email Verified
    *
    * @apiParam {String} email Email Address
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function check_email_verified_post() {
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {
            $required = ['email'];
            $check_para = $this->check_parameters($required);

            if($check_para) {
                $post["user_id"] = $userdata["user_id"];
                $data = $this->login_model->check_email_id($post);

                if ($data) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Verified'
                    ], 200);
                    echo json_encode($this->response);
                }
                else {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'To continue you must activate your account.'
                    ], 202);
                    echo json_encode($this->response);
                }
            }
        }
    }

    public function test_notification_post() {
        $arr = array(
            'recipient_user_id' => $_POST["user_id"],
            'message' => '@Test Push Notifications',
            'notification_type_id' => 1
        );
        echo $this->notification_model->send($arr);
        exit;
    }

    public function test_email_post() {
        $to = $_POST["email"];
        $subject = "test";
        // $msg = "testing mail";
        $msg = $this->load->view('mail_tmp/header', $_POST, true);
        $msg .= $this->load->view('mail_tmp/welcome', $_POST, true);
        $msg .= $this->load->view('mail_tmp/footer', $_POST, true);
        $this->dataset_model->send_mail($to, $subject, $msg);
    }
}

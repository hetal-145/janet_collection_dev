<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(dirname(__FILE__).'/Authentication.php'); //include controller

class Users extends Authentication{

    /**
    * @apiGroup User API
    * @api {POST} https://api.wadio.app/v1/users/ 1. Api Url
    *
    */
    function __construct() {
        // Construct the parent class
        parent::__construct();
        //load model
        $this->load->model(basename(__DIR__)."/user_model");
        $this->load->model(basename(__DIR__)."/media_model");
        $this->load->model(basename(__DIR__)."/dataset_model");
        $this->load->model(basename(__DIR__)."/notification_model");
    }

    /**
    * @apiGroup User API
    * @api {get} index Get User Profile
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function index_get() {
    	header('Content-Type: application/json');
        $post = $_GET;
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {
            $post["user_id"] = $userdata["user_id"];
            $data = $this->user_model->get_profile($post);

            if ($data) {
                $this->response([
                    'status' => 'true',
                    'user_data' => $data,
                ], 200);
                echo json_encode($this->response);
            }
            else {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'User data not found'
                ], 404);
                echo json_encode($this->response);
            }
        }
    }

    /**
    * @apiGroup User API
    * @api {post} index Update User Profile
    *
    * @apiParam {String} [name] Name
    * @apiParam {String} [username] Username
    * @apiParam {String} [is_profile_image] Need to upload profile image ot not (1 / 0)
    * @apiParam {String} [is_cover_image] Need to upload cover image ot not (1 / 0)
    * @apiParam {String} [image_ext] Image ext to be uploaded
    * @apiParam {String} [cover_image_ext] Cover Image ext to be uploaded
    * @apiParam {String} [profile_image] Profile Image
    * @apiParam {String} [cover_image] Cover Image
    * @apiParam {String} [phone_code] Phone Code
    * @apiParam {String} [phone_number] Phone Number
    * @apiParam {String} [is_profile_public] Is profile public? (0 = no, 1 = yes)
    * @apiParam {String} [dob] Date of birth (Format: YYYY-MM-DD)
    * @apiParam {String} [gender] Gender (0 = female, 1 =male, 2 = other)
    * @apiParam {String} [nationality] Nationality
    * @apiParam {String} [city_state] City / State
    * @apiParam {String} [description] Description
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function index_post() {
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {
            $post["user_id"] = $userdata["user_id"];
            $user_data = $this->user_model->update_profile($post);

            if ($user_data === 1) {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'Username already exists',
                ], 400);
                echo json_encode($this->response);
            }
            else if ($user_data === 2) {
                $this->response([
                    'status' => 'true',
                    'response_msg' => 'Profile updated',
                    'user_data' => $this->user_model->get_profile($post)
                ], 200);
                echo json_encode($this->response);
            } else {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'Please provide valid data for update profile.',
                ], 400);
                echo json_encode($this->response);
            }
        }
    }

    /**
    * @apiGroup User API
    * @api {get} search/{chr} Searched User Profiles
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */

    /**
    * @apiGroup User API
    * @api {get} search Get Suggested User Profiles
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function search_get($chr = null) {
        header('Content-Type: application/json');
        
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {

            $post["user_id"] = $userdata["user_id"];

            if (empty($chr)) {
                $user = $this->user_model->suggestion_for_user($post["user_id"]);;
            }
            else {
                $post["chr"] = $chr;
                $user = $this->user_model->search_user($post);
            }
            //print_r($user);
            if ($user) {
                $this->response([
                    'status' => 'true',
                    'user_list' => $user,
                ], 200);
                echo json_encode($this->response);
            }
            else {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'No user found.',
                ], 404);
                echo json_encode($this->response);
            }
        }
    }

    /**
    * @apiGroup User API
    * @api {get} {person_id}/person_profile Get Another Person Profile
    *
    * @apiParam {Number} person_id Person ID
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function person_profile_get($person_id) {
        header('Content-Type: application/json');
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {
            $post["person_id"] = $person_id;
            $post["user_id"] = $userdata["user_id"];
            $user_data = $this->user_model->person_profile($post);
            if ($user_data) {

                $this->response([
                    'status' => 'true',
                    'user_data' => $user_data
                ], 200);
                echo json_encode($this->response);
            } else {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'User data not found',
                ], 404);
                echo json_encode($this->response);
            }
        }
    }

    /**
    * @apiGroup User API
    * @api {post} phone_book Add your contacts
    *
    * @apiParam {String} contact Email Ids of you contacts (comma (,) separated)
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function phone_book_post() {
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {
            $required = ['contact'];
            $check_para = $this->check_parameters($required);

            if($check_para) {

                $post["user_id"] = $userdata["user_id"];
                $user = $this->user_model->get_contact_list($post);
                //print_r($user); exit;
                if ($user) {
                    $this->response([
                        'status' => 'true',
                        'user_list' => $user,
                    ], 200);
                    echo json_encode($this->response);
                } else {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'No contact found.',
                    ], 404);
                    echo json_encode($this->response);
                }
            }
        }
    }

    /**
    * @apiGroup User API
    * @api {post} send_an_invite Send a connection request
    *
    * @apiParam {Number} contact_id Contact Person ID
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function send_an_invite_post() {
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {
            $required = ['contact_id'];
            $check_para = $this->check_parameters($required);

            if($check_para) {

                $post["user_id"] = $userdata["user_id"];
                $user = $this->user_model->send_an_invite($post);
                // print_r($user);
                if ($user === 1) {
                    $this->notification_model->send($user);

                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'You are blocked',
                    ], 400);
                    echo json_encode($this->response);
                } else if ($user === 2) {
                    // $this->notification_model->send($user);

                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'Connection Request Already Send.',
                    ], 400);
                    echo json_encode($this->response);
                } else {
                    $this->notification_model->send($user);

                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Connection Request Send.',
                    ], 200);
                    echo json_encode($this->response);
                }
            }
        }
    }

    /**
    * @apiGroup User API
    * @api {post} accept_an_invite Accept / Reject a connection request
    *
    * @apiParam {Number} invited_user_id Person ID who has invited you
    * @apiParam {Number} type ( 1 = Accepted, 2 = Rejected, 3 = Remove connected user )
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function accept_an_invite_post() {
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {
            $required = ['invited_user_id', 'type'];
            $check_para = $this->check_parameters($required);

            if($check_para) {

                $post["user_id"] = $userdata["user_id"];
                $user = $this->user_model->accept_an_invite($post);
                //print_r($user); exit;
                if (!empty($user) && $user["type"] === 3) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => $user["message_body"],
                    ], 200);
                    echo json_encode($this->response);
                } else if (!empty($user)) {
                    $this->notification_model->send($user);

                    $this->response([
                        'status' => 'true',
                        'response_msg' => $user["message_body"],
                    ], 200);
                    echo json_encode($this->response);
                } else {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'Error.',
                    ], 400);
                    echo json_encode($this->response);
                }
            }
        }
    }

    /**
    * @apiGroup User API
    * @api {delete} {invited_user_id}/unsend_a_request Unsend a connection request
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function unsend_a_request_delete($invited_user_id) {
        header('Content-Type: application/json');
        
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {
            $post["invited_user_id"] = $invited_user_id;
            $post["user_id"] = $userdata["user_id"];
            $user = $this->user_model->unsend_a_request($post);
            //print_r($user); exit;
            if (!empty($user)) {
                $this->response([
                    'status' => 'true',
                    'response_msg' => 'Unsend request',
                ], 200);
                echo json_encode($this->response);
            } else {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'Error.',
                ], 400);
                echo json_encode($this->response);
            }
        }
    }

    /**
    * @apiGroup User API
    * @api {get} {person_id}/connection_list Connection list
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function connection_list_get($person_id) {
        header('Content-Type: application/json');
        
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {           
            $post["person_id"] = $person_id;
            $post["user_id"] = $userdata["user_id"];
            $user = $this->user_model->connection_list($post);

            if ($user) {
                $this->response([
                    'status' => 'true',
                    'list' => $user["connection_list"],
                    'common_list' => $user["common_connections"],
                ], 200);
                echo json_encode($this->response);
            } else {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'No connections found.',
                ], 404);
                echo json_encode($this->response);
            }
        }
    }

    /**
    * @apiGroup User API
    * @api {post} update_current_location_details Update user current location
    *
    * @apiParam {String} [latitude] Last Latitude
    * @apiParam {String} [longitude] Last Longitude
    * @apiParam {String} [ipaddress] Last Ipaddress
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function update_current_location_details_post() {
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {
            $post["user_id"] = $userdata["user_id"];
            $user = $this->user_model->update_current_location_details($post);
            if ($user) {
                $this->response([
                    'status' => 'true',
                    'response_msg' => 'Location Details Updated.',
                ], 200);
                echo json_encode($this->response);
            } else {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'Location Details Not Updated.',
                ], 400);
                echo json_encode($this->response);
            }
        }
    }

    /**
    * @apiGroup User API
    * @api {put} / Temporarlily Disable Account
    *
    * @apiParam {String} account_flag // 1= enabled, 0 = disable
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function index_put() {
        header('Content-Type: application/json');
        parse_str(file_get_contents('php://input'), $_PUT);
        // print_r($_PUT); exit;
        $post = $_PUT;
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {            
            $post["user_id"] = $userdata["user_id"];
            $user = $this->user_model->temporarily_disable_account($post);
            //print_r($user); exit;
            if ($user === 1) {
                $this->response([
                    'status' => 'true',
                    'response_msg' => "Account Inactive",
                ], 200);
                echo json_encode($this->response);
            } 
            else if ($user === 2) {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'Account Active',
                ], 200);
                echo json_encode($this->response);
            }
            else if ($user === 3) {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'No data found.',
                ], 404);
                echo json_encode($this->response);
            }
        }
    }

    /**
    * @apiGroup User API
    * @api {delete} / Closed Account
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function index_delete() {
        header('Content-Type: application/json');
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {            
            $post["user_id"] = $userdata["user_id"];
            $user = $this->user_model->closed_account($post);
            //print_r($user); exit;
            if ($user) {
                $this->response([
                    'status' => 'true',
                    'response_msg' => "Account Closed",
                ], 200);
                echo json_encode($this->response);
            } 
            else {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'No data found.',
                ], 404);
                echo json_encode($this->response);
            }
        }
    }

    /**
    * @apiGroup User API
    * @api {post} report/{station_id}  Report User
    *
    * @apiParam {String} user_request_reason_id
    * @apiParam {String} reported_user_id
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function report_post() {
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
             $required = ['reported_user_id', 'user_request_reason_id'];
            if ($this->check_parameters($required)) {
                $post['user_id'] = $userdata['user_id'];
                $data = $this->user_model->report_user($post);
                if ($data) {
                    $this->response([
                            'status' => 'true',
                            'response_msg' => 'Reported Successfully'
                        ], 200);
                } else {
                    $this->response([
                            'status' => 'false',
                            'response_msg' => 'Report Not Successful',
                        ], 200);
                }
            }
        }
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Authentication extends RestController {

    function __construct() {
        // Construct the parent class
        parent::__construct();
        //load model
        $this->load->model(basename(__DIR__)."/authentication_model");
        ini_set('display_errors', '1');
        // print_r($_GET);
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
            log_message('error', $this->uri->uri_string() . ' /// get ---> ' . json_encode($_GET));
        }

        // if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'REQUEST') {
        //     log_message('error', $this->uri->uri_string() . ' /// request ---> ', json_encode($_REQUEST));
        // }

        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
            log_message('error', $this->uri->uri_string() . ' /// post ---> ' . json_encode($_POST));
        }

        if (isset($_FILES) && $_FILES) {
            log_message('error', $this->uri->uri_string() . ' /// files ---> ' . json_encode($_FILES));
        }

        $headers = $this->input->request_headers();

        if (isset($headers['Authorization']) && $headers['Authorization']) {
            log_message('error', $this->uri->uri_string() . ' /// header token ---> ' . json_encode($headers['Authorization']));
        }

        // if (isset($headers['Env']) && $headers['Env']) {
        //     log_message('error', $this->uri->uri_string() . ' /// env variable ---> ' . $headers['Env']);
        // }
    }

    public function auth() {
    	$response = $this->authentication_model->authenticate();

        if ($response === 1) {
            $this->response(['status' => 'false', 'response_msg' => 'User authentication failed. Token mismatch.'], 401);
            echo json_encode($this->response);
        }
        else if ($response === 2) {
            $this->response(['status' => 'false', 'response_msg' => 'User authentication failed. User ID not set.'], 401);
            echo json_encode($this->response);
        }
        else if ($response === 3) {
            $this->response(['status' => 'false', 'response_msg' => 'User authentication failed. Token not set.'], 401);
            echo json_encode($this->response);
        }
        else {
            return $response;
        }
    }

    public function check_parameters($paras = array(), $msg = '') {
        $response = $this->authentication_model->check_parameters($paras, $msg);

        if ($response === 1) {
            $this->response(['status' => 'false', 'response_msg' => $msg], 400);
            echo json_encode($this->response);
        }
        else if ($response === 2) {
            $this->response(['status' => 'false', 'response_msg' => 'Please fill required fields.'], 400);
            echo json_encode($this->response);
        }
        else {
            return $response;
        }
    }

    function notnull($ary = []) {
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

    function generate_jwt_user_token($user_id) {
        $response = $this->authentication_model->generate_jwt_user_token($user_id);
        return $response;
    }
}

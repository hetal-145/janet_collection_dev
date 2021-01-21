<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(dirname(__FILE__).'/Authentication.php'); //include controller

class Datasets extends Authentication{

    /**
    * @apiGroup Dataset API
    * @api {POST} https://api.wadio.app/v1/ 1. Api Url
    *
    */

    function __construct() {
        // Construct the parent class
        parent::__construct();
        //load model
        $this->load->model(basename(__DIR__)."/dataset_model");
    }

    public function qs_get() {
		$params = $_SERVER['QUERY_STRING'];
		$this->response(['status' => true, 'data' => $params]);
		http_response_code(200);
    }

    /**
    * @apiGroup Dataset API
    * @api {get} legal/pp Privacy Policy
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function privacy_policy_get() {
        header('Content-Type: application/json');
        $privacy_arr = $this->dataset_model->get_privacy_policy();
        $this->response(['status' => 'false', 'data' => $privacy_arr], 200);
        echo json_encode($this->response);
    }

    /**
    * @apiGroup Dataset API
    * @api {get} legal/toc Term & Condition
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function term_and_conditions_get() {
        header('Content-Type: application/json');
        $tmc_arr = $this->dataset_model->get_term_condition();
        $this->response(['status' => 'false', 'data' => $tmc_arr], 200);
        echo json_encode($this->response);
    }

    /**
    * @apiGroup Dataset API
    * @api {get} legal/notices Legal Notice
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function legal_notice_get() {
        header('Content-Type: application/json');
        $legalno_arr = $this->dataset_model->legal_notice();
        $this->response(['status' => 'false', 'data' => $legalno_arr], 200);
        echo json_encode($this->response);
    }

    /**
    * @apiGroup Dataset API
    * @api {get} legal Term & Condition & Privacy Policy
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function legal_get() {
        header('Content-Type: application/json');
        $tmc_arr = $this->dataset_model->get_term_condition();
        $privacy_arr = $this->dataset_model->get_privacy_policy();
        $arr = array(
            'termsconditon' => $tmc_arr,
            'privacypolicy' => $privacy_arr
        );
        $this->response(['status' => 'false', 'data' => $arr], 200);
        echo json_encode($this->response);
    }

    /**
    * @apiGroup Dataset API
    * @api {get} countries Countries List
    *
    * @apiParam {Number} [offset] Offset
    * @apiParam {String} [iso2] iso code of country (2 char)
    * @apiParam {String} [latitude] Latitude
    * @apiParam {String} [longitude] Longitude
    * @apiParam {String} [chr] character to search
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function countries_get() {
        header('Content-Type: application/json');
        $post = $_GET;

        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {

            if (!isset($_GET['offset']) || !$_GET['offset']) {
                $offset = '0';
            }
            else {
                $offset = $_GET['offset'];
            }

        	if(!empty($_GET['iso2'])) {
        		$data = $this->dataset_model->get_country_by_iso2($_GET['iso2']);
        		if(!$data) {
        			$this->response(['status' => 'false', 'response_msg' => 'No data found'], 404);
        		}
        		else {
		        	$this->response(['status' => 'true', 'data' => $data], 200);
		        }
		    }
		    else if(!empty($_GET['latitude']) && !empty($_GET['longitude']) && !empty($_GET['chr'])) {
		        $data = $this->dataset_model->get_countries($offset, $_GET['longitude'], $_GET['latitude'], $_GET['chr']);
        		if(!$data) {
                    $this->response(['status' => 'false', 'response_msg' => 'No data found'], 404);
                }
                else {
                    $this->response(['status' => 'true', 'data' => $data], 200);
                }
		    }
            else if(!empty($_GET['latitude']) && !empty($_GET['longitude'])) {
                $data = $this->dataset_model->get_countries($offset, $_GET['longitude'], $_GET['latitude']);
                if(!$data) {
                    $this->response(['status' => 'false', 'response_msg' => 'No data found'], 404);
                }
                else {
                    $this->response(['status' => 'true', 'data' => $data], 200);
                }
            }
            else if(!empty($_GET['chr'])) {
                $data = $this->dataset_model->get_countries($offset, "", "", $_GET['chr']);
                if(!$data) {
                    $this->response(['status' => 'false', 'response_msg' => 'No data found'], 404);
                }
                else {
                    $this->response(['status' => 'true', 'data' => $data], 200);
                }
            }
		    else{
		        $data = $this->dataset_model->get_countries($offset);
        		if(!$data) {
                    $this->response(['status' => 'false', 'response_msg' => 'No data found'], 404);
                }
                else {
                    $this->response(['status' => 'true', 'data' => $data], 200);
                }
		    }

		    echo json_encode($this->response);
        }
    }

    /**
    * @apiGroup Dataset API
    * @api {get} genres/{genred_id} Single Genres Details
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */

    /**
    * @apiGroup Dataset API
    * @api {get} genres Genres List
    *
    * @apiParam {Number} [offset] Offset
    * @apiParam {String} [is_popular] (1= yes , 0 = no)
    * @apiParam {String} [country] country ISO2 code (get_available_genres_list ex: IN for India)
    * @apiParam {String} [latitude] latitude (get_available_genres_list)
    * @apiParam {String} [longitude] longitude (get_available_genres_list)
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function genres_get($genred_id = null) {
        header('Content-Type: application/json');

        if (!isset($_GET['offset']) || !$_GET['offset']) {
            $_GET['offset'] = '0';
        }
        else {
            $offset = $_GET['offset'];
        }

        $post = $_GET;

        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {
            if(!empty($genred_id)) {
                $data = $this->dataset_model->get_genre_by_id($genred_id);
                if(!$data) {
                    $this->response(['status' => 'false', 'response_msg' => 'No data found'], 404);
                }
                else {
                    $this->response(['status' => 'true', 'data' => $data], 200);
                }
            }
            else{
                $data = $this->dataset_model->get_genres($post);
                if(!$data) {
                    $this->response(['status' => 'false', 'response_msg' => 'No data found'], 404);
                }
                else {
                    $this->response(['status' => 'true', 'data' => $data], 200);
                }
            }

            echo json_encode($this->response);
        }
    }

    /**
    * @apiGroup Dataset API
    * @api {get} languages/{code} Get Single Language Details
    *
    * @apiParam {Number} [offset] Offset
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */

    /**
    * @apiGroup Dataset API
    * @api {get} languages Languages List
    *
    * @apiParam {Number} [offset] Offset
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function languages_get($code = null) {
        header('Content-Type: application/json');
        $post = $_GET;

        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {

            if (!isset($_GET['offset']) || !$_GET['offset']) {
                $offset = '0';
            }
            else {
                $offset = $_GET['offset'];
            }

            if(!empty($code)) {
                $data = $this->dataset_model->get_language_by_code($code);
                if(!$data) {
                    $this->response(['status' => 'false', 'response_msg' => 'No data found'], 404);
                }
                else {
                    $this->response(['status' => 'true', 'data' => $data], 200);
                }
            }
            else{
                $data = $this->dataset_model->get_languages($offset);
                if(!$data) {
                    $this->response(['status' => 'false', 'response_msg' => 'No data found'], 404);
                }
                else {
                    $this->response(['status' => 'true', 'data' => $data], 200);
                }
            }

            echo json_encode($this->response);
        }
    }

    /**
    * @apiGroup Dataset API
    * @api {get} categories  Station Category List
    *
    * @apiParam {Number} [offset] Offset
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function categories_get($category_id = null) {
        header('Content-Type: application/json');
        
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {

            if (!isset($_GET['offset']) || !$_GET['offset']) {
                $offset = '0';
            }
            else {
                $offset = $_GET['offset'];
            }
            
            $data = $this->dataset_model->get_category_list($offset);
            if(!$data) {
                $this->response(['status' => 'false', 'response_msg' => 'No data found'], 404);
            }
            else {
                $this->response(['status' => 'true', 'data' => $data], 200);
            }

            echo json_encode($this->response);
        }
    }

    /**
    * @apiGroup Dataset API
    * @api {post} add_recent_search Add recent search
    *
    * @apiParam {String} search_for  (Pass 1 or 2…) (For info only: 1 : Country, 2 : Station, 3 : Artist, 4 : Talkshow, 5 : Track, 6 : Radio Host)
    * @apiParam {String} searched_id Source Id
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function add_recent_search_post() {
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {
            $required = ['search_for', 'searched_id'];
            $check_para = $this->check_parameters($required);

            if($check_para) {
                $post["user_id"] = $userdata["user_id"];
                $data = $this->dataset_model->add_recent_search($post);

                if ($data == 1) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Added in recent search list'
                    ], 200);
                    echo json_encode($this->response);
                }
                else if ($data == 2) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'No added in recent search list'
                    ], 400);
                    echo json_encode($this->response);
                }
                else if ($data == 3) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Please enter numeric value only'
                    ], 400);
                    echo json_encode($this->response);
                }
            }
        }
    }

    /**
    * @apiGroup Dataset API
    * @api {get} get_recent_search Get recent search
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function get_recent_search_get() {
        header('Content-Type: application/json');
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {

            if (!isset($_GET['offset']) || !$_GET['offset']) {
                $offset = '0';
            }
            else {
                $offset = $_GET['offset'];
            }
            
            $post["user_id"] = $userdata["user_id"];
            $data = $this->dataset_model->get_recent_search($post);

            if ($data) {
                $this->response([
                    'status' => 'true',
                    'recent_search_list' => $data,
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
    * @apiGroup Dataset API
    * @api {delete} clear_recent_search Clear recent search
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function clear_recent_search_delete() {
        header('Content-Type: application/json');
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {            
            $post["user_id"] = $userdata["user_id"];
            $data = $this->dataset_model->clear_recent_search($post);

            if ($data) {
                $this->response([
                    'status' => 'true',
                    'response_msg' => 'List clear',
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
    * @apiGroup Dataset API
    * @api {get} search_all/{chr} Search All
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function search_all_get($chr = null) {
        header('Content-Type: application/json');
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {  
            $data = $this->dataset_model->search_all($chr);

            if ($data == 1) {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'No data found.',
                ], 404);
                echo json_encode($this->response);
            }
            else {
                $this->response([
                    'status' => 'true',
                    'get_all' => $data
                ], 200);
                echo json_encode($this->response);
            }
        }
    }

    /**
    * @apiGroup Dataset API
    * @api {get} report_reasons Report Reasons
    ** @apiParam {String} type
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function report_reasons_get() {
        header('Content-Type: application/json');
        $post = $_GET;
        $_REQUEST = $_GET;

        $userdata = $this->auth();
        if(!empty($userdata["user_id"])) {  
            $required = ['type'];
            if ($this->check_parameters($required)) {
                $data = $this->dataset_model->report_reason_list($post);

                if ($data == 1) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    ], 404);
                    echo json_encode($this->response);
                }
                else {
                    $this->response([
                        'status' => 'true',
                        'get_all' => $data
                    ], 200);
                    echo json_encode($this->response);
                }
            }
        }
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(dirname(__FILE__) .'/Authentication.php'); //include controller
require_once(dirname(__FILE__) .'/Media.php'); //include controller


class Artists extends Authentication {

    /**
    * @apiGroup Artist API
    * @api {POST} https://api.wadio.app/v1/artists/ 1. Api Url
    *
    */
    function __construct() {
        // Construct the parent class
        parent::__construct();

        ini_set('display_errors','1');
        error_reporting(1);

        //load model
        $this->load->model(basename(__DIR__)."/user_model");
        $this->load->model(basename(__DIR__)."/artist_model");
        $this->load->model(basename(__DIR__)."/notification_model");
    }

     /**
    * @apiGroup Artist API
    * @api {get} /  Artist List
    * @apiParam {Number} [offset] offset
    * @apiParam {Number} [chr] 
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */

    /**
    * @apiGroup Artist API
    * @api {get} {artist_id}  Artist Detail
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function index_get($artist_id = null){
    	header('Content-Type: application/json');
        $post = $_POST;
        $post['artist_id'] = $artist_id;
        $userdata = $this->auth();
        if ($userdata) {
        	$post['user_id'] = $userdata['user_id'];
        	if($artist_id != null && (int)$artist_id > 0){
        		$data = $this->artist_model->get_artist_profile($post);
				if($data === 1) {
					$this->response([
	                    'status' => 'false',
	                    'response_msg' => 'Currently, artist is inactive.',
	                ], 200);
				}
				else if($data === 2) {
					$this->response([
	                    'status' => 'false',
	                    'response_msg' => 'No Data Found',
	                ], 200);
	            }
	            else {
	            	$this->response([
	                    'status' => 'true',
	                    'artist_profile' => $data,
	                ], 200);
	            }
        	}else{
        		$post['limit'] = LIMIT;
	            if (!isset($post['offset']) || !$post['offset']) {
	                $post['offset'] = '0';
	            }
	            $data = $this->artist_model->get_artist_list($post);
	            if ($data) {
	            	$this->response([
	                    'status' => 'true',
	                    'offset' => ($post['limit'] + $post['offset']),
	                    'artist_list' => $data
	                ], 200);
	            } else {
	            	$this->response([
	                    'status' => 'false',
	                    'response_msg' => 'No data found.',
	                ], 200);
	            }
        	}            
        }
    }


    /**
    * @apiGroup Artist API
    * @api {put} {station_id} Update Station Details
    *
    * @apiParam {File} image 
    * @apiParam {File} artist_cover_image 
    * @apiParam {String} username
    * @apiParam {String} name
    * @apiParam {String} biography 
    * @apiParam {String} blurb headline
    * @apiParam {String} website
    * @apiParam {String} email
    * @apiParam {String} phone
    * @apiParam {String} city 
    * @apiParam {String} country_id
    * @apiParam {String} country_iso2
    * @apiParam {String} hometown
    * @apiParam {String} tags
    * @apiParam {String} genres
    * @apiParam {String} birthdate
    * @apiParam {String} apple_music_url
    * @apiParam {String} youtube_channel_url
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function index_put($artist_id) {
        header('Content-Type: application/json');
        parse_str(file_get_contents('php://input'), $_PUT);
        $post = $_PUT;
        $post['artist_id'] = $artist_id;

        if (isset($_FILES)) {
            $files = $_FILES;
        } else {
            $files = [];
        }

        $userdata = $this->auth();
        if ($userdata) {
           	$post['user_id'] = $userdata['user_id'];
            $user_data = $this->artist_model->update_artist($post, $files);
            if ($user_data === 1) {
            	$this->response([
	                    'status' => 'false',
                    	'response_msg' => 'Username already exists',
	                ], 200);
            } else if ($user_data === 2) {
            	$this->response([
	                    'status' => 'true',
                    	'response_msg' => 'Artist Profile updated',
	                ], 200);
            } else {
            	$this->response([
	                    'status' => 'false',
                    	'response_msg' => 'Please provide valid data for update profile.',
	                ], 200);
            }
        }
    }

    /**
    * @apiGroup Artist API
    * @api {post} {artist_id}/report  Report Artist
    *
    * @apiParam {String} user_request_reason_id
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function report_post($artist_id) {
        header('Content-Type: application/json');
        $post = $_POST;
        $post['artist_id'] = $artist_id;
        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $data = $this->artist_model->report_artist($post);
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

    /**
    * @apiGroup Artist API
    * @api {post} {artist_id}/report  Share Artist
    *
    * @apiParam {String} [is_prv_share]
    * @apiParam {String} ids
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function shares_post($artist_id) {
        header('Content-Type: application/json');
        $post = $_POST;
        $post['artist_id'] = $artist_id;
        $userdata = $this->auth();
        if ($userdata) {

            $required = ['ids'];
            if ($this->check_parameters($required)) {
                $post['user_id'] = $userdata['user_id'];
                $data = $this->artist_model->share_artist($post);
                if ($data) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Artist Shared.'
                    ], 200);
                } else {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'Artist Not Shared.',
                    ], 200);
                }
            }
        }
    }

    /**
    * @apiGroup Artist API
    * @api {post} {artist_id}/subscribe  Subscribe Artist
    *
    * @apiParam {String} is_subscribe
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function subscribe_post($artist_id) {
        header('Content-Type: application/json');
        $post = $_POST;
        $post['artist_id'] = $artist_id;

        $userdata = $this->auth();
        if ($userdata) {

            $required = ['is_subscribe'];
            if ($this->check_parameters($required)) {
                $post['user_id'] = $userdata['user_id'];
                $station = $this->artist_model->make_sub_unsub_artist($post);

                if ($station === 1) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'Artist already subscribe',
                    ], 200);
                } else if ($station === 2) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Artist subscribe',
                    ], 200);
                } else if ($station === 3) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Artist unsubscribe',
                    ], 200);
                } else if ($station === 4) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'no data found',
                    ], 404);
                }
            }
        }
    }

    /**
    * @apiGroup Artist API
    * @api {get} {artist_id}/subscribers  Subscribers 
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function subscribers_get($artist_id) {
        header('Content-Type: application/json');
        $post = $_GET;
        $post['artist_id'] = $artist_id;

        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $station = $this->artist_model->artist_subscriber_list($post);
            $common_conn = $this->artist_model->artist_subscriber_common_connection($post);
            if ($station) {
                $this->response([
                        'status' => 'true',
                        'subscriber_list' => $station,
                        'common_connection_list' => $common_conn
                    ], 200);
            } else {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'No data Found',
                    ], 404);
            }
        }
    }


    /**
    * @apiGroup Artist API
    * @api {post} {artist_id}/favorites   Favorite / Unfavorite Artist
    *
    * @apiParam {Number} is_fav 0 / 1
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function favorites_post($artist_id = null){
        header('Content-Type: application/json');
        $post = $_POST;
        $post['artist_id'] = $artist_id;

        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $artist = $this->artist_model->make_fav_unfav_artist($post);
            if ($artist === 1) {
            $this->response([
                    'status' => 'false',
                    'response_msg' => 'artist already mark as favourite',
                ], 200);
            } else if ($artist === 2) {
                $this->response([
                    'status' => 'true',
                    'response_msg' => 'artist mark as favourite',
                ], 200);
            } else if ($artist === 3) {
                $this->response([
                    'status' => 'true',
                    'response_msg' => 'artist mark as unfavourite',
                ], 200);
            } else if ($artist === 4) {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'no data found',
                ], 404);
            }
        }
    }

    /**
    * @apiGroup Artist API
    * @api {get} favorites   Favorite Artist
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function favorites_get() {
        header('Content-Type: application/json');
        $post = $_GET;
        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $data = $this->artist_model->user_favourite_artist_list($post);
            if ($data) {
                $this->response([
                    'status' => 'true',
                    'user_favourite_artist_list' => $data,
                ], 200);
            } else {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'No data Found',
                ], 404);
            }
        }
    }


}

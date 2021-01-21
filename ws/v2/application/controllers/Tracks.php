<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(dirname(__FILE__).'/Authentication.php'); //include controller
require_once(dirname(__FILE__).'/Media.php'); //include controller

class Tracks extends Authentication{

	/**
    * @apiGroup Tracks API
    * @api {POST} https://api.wadio.app/v1/tracks/ 1. Api Url
    *
    */
    function __construct() {
        // Construct the parent class
        parent::__construct();
        //load model
        $this->load->model(basename(__DIR__)."/user_model");
        $this->load->model(basename(__DIR__)."/track_model");
        $this->load->model(basename(__DIR__)."/dataset_model");
        $this->load->model(basename(__DIR__)."/notification_model");        
    }


    /**
    * @apiGroup Tracks API
    * @api {post} / Current Song Playing on station
    *
    * @apiParam {Number} station_id Station ID 
    * @apiParam {Number} type ( 1 = Itunes, 2 = ACR, 3 = Wadio, 4 = Musicbrainz )
    * @apiParam {String} latitude Latitude
    * @apiParam {String} longitude Longitude
    * @apiParam {String} country_id Country ID
    * @apiParam {String} track_id Track ID
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */

    public function index_post() {
        header('Content-Type: application/json');
        $post = $_POST;
        //print_r($post); exit;
        $userdata = $this->auth();
        if ($userdata) {

            $required = ['user_id', 'station_id', 'track_id', 'type', 'latitude', 'longitude', 'country_id'];
            if ($this->check_parameters($required)) {
                $data = $this->m_api->song_playing($post);
                if ($data === 1) {
                    $this->response[] = array(
                        'status' => 'false',
                        'response_msg' => 'Song cannot be saved.'
                    );
                    echo json_encode(array('response' => $this->response));
                } else {
                    $this->response[] = array(
                        'status' => 'true',
                        'track_details' => $data,
                    );
                    echo json_encode(array('response' => $this->response));
                }
            }
        }
    } 

    public function index_get($track_id) {
        header('Content-Type: application/json');
        $post = $_GET;
        $post['track_id'] = $track_id;

        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $data = $this->track_model->get_track_details_by_id($post);
            if ($data) {
                $this->response([
                        'status' => 'true',
                        'track_details' => $data,
                    ], 200);
            } else {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    ], 404);
            }
        }
    }

    public function acr_callback_stream() {
        $stream_result = array();
        print_r($_POST); exit;
        if ($_POST["status"] == 1) {
            $stream_result['stream_id'] = $_POST['stream_id'];
            $stream_result['stream_url'] = $_POST['stream_url'];
            $stream_result['stream_data'] = json_decode($_POST["data"], true);
            $stream_result['stream_status'] = $_POST["status"];
         
            
            //add content in db
            $response = $this->m_api->acr_callback_stream($stream_result);
            
        } 
        else {
            $stream_result['stream_id'] = $_POST['stream_id'];
            $stream_result['stream_url'] = $_POST['stream_url'];
            $stream_result['stream_data'] = null;
            $stream_result['stream_status'] = $_POST["status"];
            //add content in db
            $this->m_api->acr_callback_stream($stream_result);
        }
    }

    public function favorites_post($track_id) {
        header('Content-Type: application/json');
        $post = $_POST;
        $post['track_id'] = $track_id;

        $userdata = $this->auth();
        if ($userdata) {
            $required = ['is_fav'];
            if ($this->check_parameters($required)) {
                $post['user_id'] = $userdata['user_id'];
                $station = $this->track_model->make_fav_unfav_track($post);

                if ($station === 1) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'track already mark as favourite',
                    ], 200);
                } else if ($station === 2) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'track mark as favourite',
                    ], 200);
                } else if ($station === 3) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'track mark as unfavourite',
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

    public function favorites_get() {
        header('Content-Type: application/json');
        $post = $_GET;
        $userdata = $this->auth();
        if ($userdata) {

            if (!isset($post['offset']) || !$post['offset']) {
                $post['offset'] = '0';
            }
            $post['user_id'] = $userdata['user_id'];
            $tracks = $this->track_model->user_favourite_track_list($post);

            if ($tracks) {
                $this->response([
                        'status' => 'true',
                        'user_favourite_track_list' => $tracks,
                        'offset' => (LIMIT + $post['offset']),
                    ], 200);
            } else {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'No data Found',
                    ], 404);
            }
        }
    }

    public function shares_post($track_id) { 
        header('Content-Type: application/json');
        $post = $_POST;
        $post['track_id'] = $track_id;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['ids'];
            if ($this->check_parameters($required)) {
                $post['user_id'] = $userdata['user_id'];
                $data = $this->track_model->share_track($post);
                if (!empty($data)) {
                    $this->response([
                        'status' => 'true',
                        'share_count' => $data
                    ], 200);
                } else {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'Track Not Shared.',
                    ], 200);
                }
            }
        }
    }
}

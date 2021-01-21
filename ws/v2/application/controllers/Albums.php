<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(dirname(__FILE__) .'/Authentication.php'); //include controller
require_once(dirname(__FILE__) .'/Media.php'); //include controller


class Albums extends Authentication {

    /**
    * @apiGroup Artist API
    * @api {POST} https://api.wadio.app/v1/artists/ 1. Api Url
    *
    */
    function __construct() {
        // Construct the parent class
        parent::__construct();

        error_reporting(0);

        //load model
        $this->load->model(basename(__DIR__)."/user_model");
        $this->load->model(basename(__DIR__)."/album_model");
        $this->load->model(basename(__DIR__)."/notification_model");
    }

     public function tracks_get($album_id) {
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $data = $this->album_model->get_tracks_by_album($post);
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

    public function get_album_track_by_track() {
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['user_id', 'track_id'];
            if ($this->check_parameters($required)) {

                $data = $this->m_api->get_album_track_by_track($post);
                if ($data) {
                    $this->response[] = array(
                        'status' => 'true',
                        'track_details' => $data,
                    );
                    echo json_encode(array('response' => $this->response));
                } else {
                    $this->response[] = array(
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    );
                    echo json_encode(array('response' => $this->response));
                }
            }
        }
    }

}

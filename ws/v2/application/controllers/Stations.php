<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(dirname(__FILE__) .'/Authentication.php'); //include controller
require_once(dirname(__FILE__) .'/Media.php'); //include controller

class Stations extends Authentication{

    /**
    * @apiGroup Station API
    * @api {POST} https://api.wadio.app/v1/stations/ 1. Api Url
    *
    */
    function __construct() {

        //ini_set('display_errors', '0');
        error_reporting("0");


        // Construct the parent class
        parent::__construct();

        //load model
        $this->load->model(basename(__DIR__)."/user_model");
        $this->load->model(basename(__DIR__)."/show_model");
        $this->load->model(basename(__DIR__)."/station_model");
        $this->load->model(basename(__DIR__)."/notification_model");

    }



    /**
    * @apiGroup Station API
    * @api {get} /  Station List
    * @apiParam {Number} [offset] offset
    * @apiParam {Number} [category]
    * @apiParam {Number} [date]
    * @apiParam {Number} [country]
    * @apiParam {Number} [my_favourite]
    * @apiParam {Number} [language]
    * @apiParam {Number} [genres]
    * @apiParam {Number} [is_friend_recommended]
    * @apiParam {Number} [is_live]
    * @apiParam {Number} [video_chat]
    * @apiParam {Number} [latitude]
    * @apiParam {Number} [longitude]
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */

     /**
    * @apiGroup Station API
    * @api {get} {station_id}  Station Profile
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function index_get($station_id = null) {
        header('Content-Type: application/json');
        $post = $_GET;
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {
            $post['user_id'] = $userdata['user_id'];
            if($station_id != null && (int)$station_id > 0){
                $post['station_id'] = $station_id;

                $station = $this->station_model->get_station_profile($post);

                if($station === 1) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'Currently, station is inactive.',
                    ], 200);
                }
                else if($station === 2) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'No Data Found',
                    ], 404);
                }
                else {
                    $this->response([
                        'status' => 'true',
                        'station_profile' => $station,
                    ], 200);
                }

            }
            else{

                if(!isset($post['offset']) || $post['offset'] == ""){
                    $post['offset'] = 0;
                }

                $station = $this->station_model->station_list($post);

                if ($station) {
                    $this->response([
                        'status' => 'true',
                        'station_list' => $station
                    ], 200);
                } else {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'No Stations Found'
                    ], 404);
                }

            }
        }
}


    /**
    * @apiGroup Station API
    * @api {put} {station_id} Update Station Details
    *
    * @apiParam {File} image Image
    * @apiParam {String} username Username
    * @apiParam {String} name Nmae
    * @apiParam {String} frequency Frequency
    * @apiParam {String} band Band
    * @apiParam {String} headline Headline
    * @apiParam {String} description Description
    * @apiParam {String} website Website
    * @apiParam {String} email Email
    * @apiParam {String} phone Phone
    * @apiParam {String} address Address
    * @apiParam {String} zipcode Zipcode
    * @apiParam {String} state State
    * @apiParam {String} city City
    * @apiParam {String} country Country
    * @apiParam {String} streams Streams
    * @apiParam {String} genres Genres
    * @apiParam {String} categories Categories
    * @apiParam {String} hosts Hosts
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function index_put($station_id = null){

        header('Content-Type: application/json');

        parse_str(file_get_contents('php://input'), $_PUT);
        $_PUT['station_id'] = $station_id;
        $post = $_PUT;
        $files = $_FILES;
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {

            $post['user_id'] = $userdata['user_id'];
            $user_data = $this->station_model->update_station($post, $files);
            if ($user_data[0] === 1) {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'Username already exists',
                    ], 200);
            } else if ($user_data[0] === 2) {
                $this->response([
                        'status' => 'true',
                        'response_msg' => 'Station Profile updated',
                    ], 200);
                foreach ($user_data[1] as $notify) {
                    $this->notification_model->create_notification($notify);
                    $this->m_notify->send($notify);
                }
            } else if ($user_data[0] === 3) {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'Please provide valid data for update profile.',
                    ], 200);
            } else {
                $this->response([
                        'status' => 'false',
                        'response_msg' => $user_data,
                    ], 200);
            }
        }
    }


    /**
    * @apiGroup Station API
    * @api {get} {station_id}/comments  Station Comments
    *
    * @apiParam {Number}  [offset] offset
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function comments_get($station_id = null){
        header('Content-Type: application/json');
        $_GET['station_id'] = $station_id;
        $post = $_GET;

        $userdata = $this->auth();
        if ($userdata) {

            $post['user_id'] = $userdata['user_id'];

            if(!isset($post['offset']) || $post['offset'] == ""){
                $post['offset'] = 0;
            }

            $user = $this->station_model->get_station_comments($post);
            $unread = $this->station_model->get_unread_tagged_station_comments($post);

            if ($user === 1) {
                $this->response([
                        'status' => 'true',
                        'conversation_msgs' => [],
                        'unread_tagged_msg_count' => (string) 0
                    ], 200);
            } else if ($user) {
                $this->response([
                        'status' => 'true',
                        'conversation_msgs' => $user,
                        'unread_tagged_msg_count' => (string) $unread
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
    * @apiGroup Station API
    * @api {get} {station_id}/played Stations Played
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function played_get($station_id = null){
        header('Content-Type: application/json');
        $_GET['station_id'] = $station_id;
        $post = $_GET;
        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $station = $this->station_model->recent_played_station($post);
            if ($station) {
                $this->response([
                        'status' => 'true',
                        'station_list' => $station
                    ], 200);
            } else {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'No stations found.',
                    ], 404);
            }
        }
    }


    /**
    * @apiGroup Station API
    * @api {get} {station_id}/top Station Top Songs
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function top_get($station_id = null){
        header('Content-Type: application/json');
        $_GET['station_id'] = $station_id;
        $post = $_GET;

        $userdata = $this->auth();
        if ($userdata) {
            $station = $this->station_model->get_top_50_songs($post);

            if ($station) {
                 $this->response([
                        'status' => 'true',
                        'top_50' => $station,
                    ], 200);
            } else {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'No Data Found',
                    ], 404);
            }
        }
    }

    /**
    * @apiGroup Station API
    * @api {get} {station_id}/listeners Station Listeners
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function listeners_get($station_id = null){
        header('Content-Type: application/json');
        $_GET['station_id'] = $station_id;
        $post = $_GET;
        $userdata = $this->auth();
        if ($userdata) {
            $station = $this->station_model->listeners($post);
            $total_listeners = $this->station_model->total_listener($post);

            if ($station) {
                $this->response([
                        'status' => 'true',
                        'total_listeners' => $total_listeners,
                        'listeners' => $station["user_data"]
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
    * @apiGroup Station API
    * @api {get} {station_id}/shows  Station Shows
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function shows_get($station_id = null){
        header('Content-Type: application/json');
        $_GET['station_id'] = $station_id;
        $post = $_GET;
        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $talkshow = $this->station_model->station_wise_talkshow_list($post);

            if ($talkshow) {
                $this->response([
                        'status' => 'true',
                        'station_wise_talkshow_list' => $talkshow,
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
    * @apiGroup Station API
    * @api {post} {station_id}/favorites  Make station Favourite / Unfavourite
    *
    * @apiParam {Number} is_fav 0 / 1
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function favorites_post($station_id = null){
        header('Content-Type: application/json');
        $_POST['station_id'] = $station_id;
        $post = $_POST;

        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $station = $this->station_model->make_fav_unfav_station($post);

            if ($station[0] === 1) {
                $this->response([
                        'status' => 'true',
                        'response_msg' => 'Station marked as favourite',
                        'favourite' => $post["is_fav"],
                        'station_id' => $post["station_id"],
                        'favourite_count' => $station[1]
                    ], 200);
            }
            else if ($station[0] === 2) {
                 $this->response([
                        'status' => 'true',
                        'response_msg' => 'Station marked as unfavourite',
                        'favourite' => $post["is_fav"],
                        'station_id' => $post["station_id"],
                        'favourite_count' => $station[1]
                    ], 200);
            }
            else if ($station[0] === 3) {
                $this->response([
                        'status' => 'true',
                        'response_msg' => 'No Station marked as favourite',
                        'favourite' => $post["is_fav"],
                        'station_id' => $post["station_id"],
                        'favourite_count' => $station[1]
                    ], 200);
            }
        }
    }

     /**
    * @apiGroup Station API
    * @api {get} favorites   Favourite Station List
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
            $station = $this->station_model->favourite_station_list($post);

            if ($station) {
                 $this->response([
                    'status' => 'true',
                    'stations' => $station
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
    * @apiGroup Station API
    * @api {post} {station_id}/save   Save Station
    *
    * @apiParam {String} country_id
    * @apiParam {String} latitude
    * @apiParam {String} longitude
    * @apiParam {String} country_iso2
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function save_post($station_id) {
        header('Content-Type: application/json');
        $_POST['station_id'] = $station_id;
        $post = $_POST;

        $userdata = $this->auth();
        if ($userdata) {

            $post['user_id'] = $userdata['user_id'];
            $station = $this->station_model->station_played($post);

            if ($station) {
                 $this->response([
                        'status' => 'true',
                        'response_msg' => 'Station saved.',
                    ], 200);
            } else {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'Station cannot be saved.',
                    ], 200);
            }
        }
    }


    /**
    * @apiGroup Station API
    * @api {get} recents  Recent played Station
    ** @apiParam {String} [chr]
    ** @apiParam {String} [latitude]
    ** @apiParam {String} [longitude]
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function recents_get() {
        header('Content-Type: application/json');
        $post = $_GET;
        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            //$required = ['user_id', 'chr', 'latitude', 'longitude'];
            $station = $this->station_model->recent_played_station($post);
            if ($station) {
                $this->response([
                    'status' => 'true',
                    'station_list' => $station
                ], 200);
            } else {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'No stations found.',
                ], 404);
            }
        }
    }


    /**
    * @apiGroup Station API
    * @api {get} genres Station By Geners
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    * @apiParam {Number} type_tag 0 / 1
    * @apiParam {Number} state_id State Id
    * @apiParam {Number} [offset]
    * @apiParam {Number} [genres_id]
    *
    */
    public function genres_get() {
        header('Content-Type: application/json');
        $post = $_GET;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['type_tag', 'state_id']; $_REQUEST = $_GET;
            if ($this->check_parameters($required)) {

                if (!isset($post['offset']) || !$post['offset']) {
                    $post['offset'] = '0';
                }

                $post['user_id'] = $userdata['user_id'];
                $station = $this->station_model->station_by_geners($post);
                if ($station === 1) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'No data found.',
                    ], 404);
                }
                else if ($station === 2) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'Please pass genres id',
                    ], 200);
                }
                else if ($station === 3) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'Please pass offset',
                    ], 200);
                }
                else {
                    $this->response([
                        'status' => 'true',
                        'station_list' => $station
                    ], 200);
                }
            }
        }
    }


    /*public function claim_post($station_id) {
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['is_claim'];
            if ($this->check_parameters($required)) {
                $station = $this->station_model->is_claim_station($post);
                if ($station === 1) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'Station already claimed',
                    ], 200);
                } else if ($station === 2) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Station claimed',
                    ], 200);
                } else if ($station === 3) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Station unclaimed',
                    ], 200);
                } else if ($station === 4) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    ], 200);
                } else if ($station === 5) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'Station already unclaimed',
                    ], 200);
                }
            }
        }
    }*/

     /**
    * @apiGroup Station API
    * @api {post} {station_id}/subscribe Station Subscribe
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    * @apiParam {Number} is_subscribe 0 / 1
    *
    */
    public function subscribe_post($station_id) {
        header('Content-Type: application/json');
        $_POST['station_id'] = $station_id;
        $post = $_POST;

        $userdata = $this->auth();
        if ($userdata) {

            $required = ['is_subscribe'];
            if ($this->check_parameters($required)) {
                $post['user_id'] = $userdata['user_id'];
                $station = $this->station_model->make_sub_unsub_station($post);

                if ($station === 1) {
                     $this->response([
                        'status' => 'false',
                        'response_msg' => 'Station already subscribe',
                        'is_subscribe' => $post["is_subscribe"],
                        'station_id' => $post["station_id"],
                    ], 200);
                } else if ($station === 2) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Station subscribe',
                        'is_subscribe' => $post["is_subscribe"],
                        'station_id' => $post["station_id"],
                    ], 200);
                } else if ($station === 3) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Station unsubscribe',
                        'is_subscribe' => $post["is_subscribe"],
                        'station_id' => $post["station_id"],
                    ], 200);
                } else if ($station === 4) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'no data found',
                        'is_subscribe' => $post["is_subscribe"],
                        'station_id' => $post["station_id"],
                    ], 200);
                } else if ($station === 5) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'cannot subscribe self',
                        'is_subscribe' => $post["is_subscribe"],
                        'station_id' => $post["station_id"],
                    ], 200);
                }
            }
        }
    }


   /**
    * @apiGroup Station API
    * @api {get} {station_id}/subscribers Subscriber List
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function subscribers_get($station_id) {
        header('Content-Type: application/json');
        $_GET['station_id'] = $station_id;
        $post = $_GET;
        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $station = $this->station_model->station_subscriber_list($post);
            if ($station) {
                $this->response([
                        'status' => 'true',
                        'subscriber_list' => $station,
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
    * @apiGroup Station API
    * @api {get} shares Share Station
    * @apiParam {String} ids
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
     public function shares_post($station_id){
        header('Content-Type: application/json');
        $_POST['station_id'] = $station_id;
        $post = $_POST;

        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $data = $this->station_model->share_station($post);

            if ($data) {
                $this->response([
                        'status' => 'true',
                        'response_msg' => 'Station Shared.'
                    ], 200);
            } else {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'Station Not Shared.',
                    ], 200);
            }
        }
    }

    /**
    * @apiGroup Station API
    * @api {get} search Search Station
    * @apiParam {String} chr
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function search_get() {
        header('Content-Type: application/json');
        $post = $_GET;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['chr'];
            $post['user_id'] = $userdata['user_id'];

            $station = $this->station_model->search_station($post);
            if ($station) {
                 $this->response([
                    'status' => 'true',
                    'station_list' => $station
                ], 200);
            } else {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'No stations found.',
                ], 404);
            }
        }
    }

    /**
    * @apiGroup Station API
    * @api {post} {station_id}/hosts Add host to station
    * @apiParam {String} host_id User Id
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function hosts_post($station_id) {
        header('Content-Type: application/json');
        $post = $_POST;
        $post['station_id'] = $station_id;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['host_id'];
            if ($this->check_parameters($required)) {
                $post['user_id'] = $userdata['user_id'];
                $data = $this->station_model->add_host_to_station($post);
                if (!empty($data)) {
                    if ($data === 1) {
                        $this->response([
                            'status' => 'true',
                            'response_msg' => 'Invite sent to host.',
                        ], 200);
                    } else if ($data === 2) {
                        $this->response([
                            'status' => 'false',
                            'response_msg' => 'Invite already accepted.',
                        ], 200);
                    }
                } else {
                    $this->response([
                            'status' => 'false',
                            'response_msg' => 'Invite not sent.',
                        ], 200);
                }
            }
        }
    }


    /**
    * @apiGroup Station API
    * @api {get} {station_id}/hosts Host list
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function hosts_get($station_id) {
        header('Content-Type: application/json');
        $post = $_GET;
        $userdata = $this->auth();
        if ($userdata) {

            $post['limit'] = LIMIT;
            if (!isset($_GET['offset']) || !$_GET['offset']) {
                $post['offset'] = '0';
            }
            else {
                $post['offset'] = $_GET['offset'];
            }
            $post['user_id'] = $userdata['user_id'];
            $data = $this->station_model->get_host_list($post);
            if ($data) {
                $this->response([
                    'status' => 'true',
                    'offset' => ($post['limit'] + $post['offset']),
                    'host_list' => $data
                ], 200);
            } else {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'No data found.',
                ], 404);
            }
        }
    }

    /**
    * @apiGroup Station API
    * @api {put} {station_id}/accept_host  Accept host to station
    *
    * @apiParam {String} status 1 : accept / 2 : reject
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function hosts_put($station_id) {
        header('Content-Type: application/json');
        parse_str(file_get_contents('php://input'), $_PUT);
        $post = $_PUT;
        $_REQUEST = $_PUT;
        $post['station_id'] = $station_id;
        $userdata = $this->auth();
        if ($userdata) { 
            $required = ['status'];
            if ($this->check_parameters($required)){
                $post['user_id'] = $userdata['user_id'];
                $data = $this->station_model->accept_reject_host_to_station($post);

                if ($data === 1) {
                    $this->response([
                            'status' => 'true',
                            'response_msg' => 'Invite Accepted.',
                        ], 200);
                } else if ($data === 2) {
                    $this->response([
                            'status' => 'true',
                            'response_msg' => 'Invite Rejected.',
                        ], 200);
                } else {
                    $this->response([
                            'status' => 'false',
                            'response_msg' => 'No Data Found.',
                        ], 404);
                }
            }
        }
    }

    /**
    * @apiGroup Station API
    * @api {post} {station_id}/reject_host  Reject host to station
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function reject_host_post($station_id) {
        header('Content-Type: application/json');
        $post = $_POST;
        $post['station_id'] = $station_id;
        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $post['status'] = 3;
            $data = $this->station_model->accept_reject_host_to_station($post);

            if ($data === 2) {
                $this->response([
                        'status' => 'true',
                        'response_msg' => 'Invite Rejected.',
                    ], 200);
            } else {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'No Data Found.',
                    ], 404);
            }
        }
    }

    /**
    * @apiGroup Station API
    * @api {get} {station_id}/popular_artists  Popular artists of station
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function popular_artists_get($station_id) {
        header('Content-Type: application/json');
        $post = $_GET;
        $post['station_id'] = $station_id;
        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $station = $this->station_model->popular_artist($post);
            if ($station) {
                 $this->response([
                        'status' => 'true',
                        'artist' => $station,
                    ], 200);
            } else {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'No Artist Found',
                    ], 404);
            }
        }
    }

    /**
    * @apiGroup Station API
    * @api {get} {station_id}/most_liked_tracks  Most Liked tracks
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
   public function most_liked_tracks_get($station_id) {
        header('Content-Type: application/json');
        $post = $_GET;
        $post['station_id'] = $station_id;
        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $track = $this->station_model->most_liked_tracks($post);
            if ($track) {
                $this->response([
                        'status' => 'true',
                        'track_list' => $track
                    ], 200);
            } else {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'No tracks found.',
                    ], 404);
            }
        }
    }


    /**
    * @apiGroup Station API
    * @api {get} {station_id}/listener_common_connections Listener Common Connections  
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function listener_common_connections_get($station_id){
        header('Content-Type: application/json');
        $post = $_GET;
        $post['station_id'] = $station_id;

        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $station = $this->station_model->listener_common_connection($post);

            if ($station) {
                 $this->response([
                        'status' => 'true',
                        'total_listeners' => $station["total_listeners"],
                        'listeners' => $station["user_data"]
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
    * @apiGroup Station API
    * @api {get} recommended Recommended Stations
    *
    * @apiParam {String} chr
    * @apiParam {String} [offset]
    * @apiParam {String} [country_id]
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function recommended_get() {

        header('Content-Type: application/json');
        $post = $_GET;

        $userdata = $this->auth();
        if ($userdata) {
           
            $post['user_id'] = $userdata['user_id'];
            $station = $this->station_model->recommended_stations($post);

            if ($station) {
                $this->response([
                        'status' => 'true',
                        'station_list' => $station,
                    ], 200);
            } else {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'No Stations Found',
                    ], 404);
            }
        }
    }

    
    /**
    * @apiGroup Station API
    * @api {get} {station_id}/recent_tracks Recently played tracks
    *
    * @apiParam {String} [offset]
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function recent_tracks_get($stations_id) {
        header('Content-Type: application/json');
        $post = $_GET;
        $userdata = $this->auth();
        if ($userdata) {
           
            if (!isset($post['offset']) || !$post['offset']) {
                $post['offset'] = '0';
            }

            $track = $this->station_model->recent_played_tracks($post);
            if ($track) {
                $this->response([
                        'status' => 'true',
                        'track_list' => $track,
                        'offset' => (LIMIT + $post['offset']),
                    ], 200);
            } else {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'No tracks found.',
                    ], 404);
            }
        }
    }

    /**
    * @apiGroup Station API
    * @api {post} {station_id}/report  Report Station
    *
    * @apiParam {String} user_request_reason_id
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function report_post($station_id) {
        header('Content-Type: application/json');
        $post = $_POST;
        $post['station_id'] = $station_id;
        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $data = $this->station_model->report_station($post);
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

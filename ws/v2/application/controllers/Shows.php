<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(dirname(__FILE__).'/Authentication.php'); //include controller
require_once(dirname(__FILE__).'/Media.php'); //include controller


class Shows extends Authentication{

    /**
    * @apiGroup Show API
    * @api {POST} https://api.wadio.app/v1/shows/ 1. Api Url
    *
    */
    function __construct() {
        // Construct the parent class
        parent::__construct();
        
        //load model
        $this->load->model(basename(__DIR__)."/user_model");
        $this->load->model(basename(__DIR__)."/show_model");
        $this->load->model(basename(__DIR__)."/notification_model");     
    }

    
     /**
    * @apiGroup Show API
    * @api {post} /   Create Show
    * @apiParam {Number} name
    * @apiParam {Number} blurb
    * @apiParam {Number} station_id
    * @apiParam {Number} [is_live]
    * @apiParam {Number} [image_cover]
    * @apiParam {Number} [special_guest]
    * @apiParam {Number} [hosts]
    * @apiParam {Number} [schedule]   
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function index_post(){
    	header('Content-Type: application/json');
        $post = $_POST;
        $files = $_FILES;
        $userdata = $this->auth();
        if ($userdata) {
            //'user_id', 'title', 'headline', 'on_air_date', 'station_id', 'is_live', 'image', 'special_guest', 'hosts', 'schedule'
            $required = ['name', 'blurb', 'station_id'];
            if ($this->check_parameters($required)) {
                $post["user_id"] = $userdata["user_id"];
                $talkshow = $this->show_model->add_talkshow($post, $files);
                if ($talkshow[0] === 1) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'talkshow exists',
                    ], 200);
                } else if ($talkshow[0] === 2) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'talkshow added',
                    ], 200);

                    foreach ($talkshow[1] as $notify) {
                        $this->show_model->create_notification($notify);
                        echo $this->m_notify->send($notify);
                    }
                } else if ($talkshow[0] === 3) {
                     $this->response([
                        'status' => 'false',
                        'response_msg' => 'talkshow not added',
                    ], 200);
                } else if ($talkshow[0] === 4) {
                    $this->response([
                         'status' => 'false',
                         'response_msg' => 'talkshow start time is overlapping with another talkshow in same station schedule on date ' . date('d M, Y', strtotime($talkshow[1])),
                    ], 200);
                } else if ($talkshow[0] === 5) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'talkshow end time is overlapping with another talkshow in same station schedule on date ' . date('d M, Y', strtotime($talkshow[1])),
                    ], 200);
                } else {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => $talkshow,
                    ], 200);
                }
                echo json_encode($this->response);
            }
        }
    }

    /**
    * @apiGroup Show API
    * @api {put} {show_id}  Update Show
    * @apiParam {Number} name
    * @apiParam {Number} blurb
    * @apiParam {Number} station_id
    * @apiParam {Number} [is_live]
    * @apiParam {Number} [image_cover]
    * @apiParam {Number} [special_guest]
    * @apiParam {Number} [hosts]
    * @apiParam {Number} [schedule]   
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function index_put($show_id) {
        header('Content-Type: application/json');
        parse_str(file_get_contents('php://input'), $_PUT);
        $_PUT['show_id'] = $show_id;
        $post = $_PUT;
        $files = $_FILES;
        $userdata = $this->auth();
        if ($userdata) {
            //'talkshow_id', 'user_id', 'title', 'headline', 'on_air_date', 'station_id', 'is_live', 'image', 'special_guest', 'host', 'schedule'
            $talkshow = $this->show_model->edit_talkshow($post, $files);

            if ($talkshow === 1) {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'no data found',
                    ], 404);
            } else if ($talkshow === 2) {
                $this->response([
                        'status' => 'true',
                        'response_msg' => 'talkshow updated',
                    ], 200);
            } else if ($talkshow === 3) {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'talkshow not updated',
                    ], 200);
            } else {
                $talkshow_arr = explode("##", $talkshow);
                //print_r($talkshow_arr);
                if ($talkshow_arr[0] == 4) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'talkshow start time is overlapping with another talkshow in same station schedule on date ' . date('d M, Y', strtotime($talkshow_arr[1])),
                    ], 200);
                } else if ($talkshow_arr[0] == 5) {
                     $this->response([
                        'status' => 'false',
                        'response_msg' => 'talkshow end time is overlapping with another talkshow in same station schedule on date ' . date('d M, Y', strtotime($talkshow_arr[1])),
                    ], 200);
                } else {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => $talkshow,
                    ], 200);
                }
            }
            echo json_encode($this->response);
        }
    }

    
    /**
    * @apiGroup Show API
    * @api {delete} {show_id}  Delete Show
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function index_delete($show_id){
        header('Content-Type: application/json');
        $_GET['show_id'] = $show_id;
        $post = $_GET;

        $userdata = $this->auth();
        if ($userdata) {

            $talkshow = $this->show_model->delete_talkshow($post);

            if ($talkshow === 1) {
                $this->response([
                        'status' => 'true',
                        'response_msg' => 'talkshow deleted',
                    ], 200);
            } else if ($talkshow === 2) {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'talkshow not deleted',
                    ], 200);
            } else if ($talkshow === 3) {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'no data found',
                    ], 404);
            }
            echo json_encode($this->response);
        }
    }

    /**
    * @apiGroup Show API
    * @api {get} {show_id}  Show Detail
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function index_get($show_id){
       
        header('Content-Type: application/json');
        $post = $_GET;
        $post['talkshow_id'] = $show_id;

        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $talkshow = $this->show_model->get_talkshow_by_id($post);

            if (!empty($talkshow)) {
                $this->response([
                        'status' => 'true',
                        'talkshow' => $talkshow,
                    ], 200);
            } else {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'no data found',
                    ], 404);
            }
        }
    }

    /**
    * @apiGroup Show API
    * @api {post} {show_id}/likes  Show Like / Dislike
    * @apiParam {String} like_unlike
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function likes_post($show_id) { 
        header('Content-Type: application/json');
        $_POST['talkshow_id'] = $show_id;
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['like_unlike'];
            if ($this->check_parameters($required)) {
                $post['user_id'] = $userdata['user_id'];
                $list = $this->show_model->like_unlike_talkshow($post);
                if ($list === 1) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => "Talkshow already liked.",
                        'talkshow_like_count' => $this->show_model->talkshow_like_count($post["talkshow_id"]),
                        'is_talkshow_liked' => $this->show_model->is_talkshow_liked($post["talkshow_id"], $post["user_id"]),
                    ], 200);
                } else if ($list === 2) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Talkshow liked.',
                        'talkshow_like_count' => $this->show_model->talkshow_like_count($post["talkshow_id"]),
                        'is_talkshow_liked' => $this->show_model->is_talkshow_liked($post["talkshow_id"], $post["user_id"]),
                    ], 200);
                } else if ($list === 3) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'Talkshow like failed.',
                        'talkshow_like_count' => $this->show_model->talkshow_like_count($post["talkshow_id"]),
                        'is_talkshow_liked' => $this->show_model->is_talkshow_liked($post["talkshow_id"], $post["user_id"]),
                    ], 200);
                } else if ($list === 4) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Talkshow unliked.',
                        'talkshow_like_count' => $this->show_model->talkshow_like_count($post["talkshow_id"]),
                        'is_talkshow_liked' => $this->show_model->is_talkshow_liked($post["talkshow_id"], $post["user_id"]),
                    ], 200);
                } else if ($list === 5) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'Talkshow unlike failed.',
                        'talkshow_like_count' => $this->show_model->talkshow_like_count($post["talkshow_id"]),
                        'is_talkshow_liked' => $this->show_model->is_talkshow_liked($post["talkshow_id"], $post["user_id"]),
                    ], 200);
                } else if ($list === 6) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'No likes found.',
                        'talkshow_like_count' => $this->show_model->talkshow_like_count($post["talkshow_id"]),
                        'is_talkshow_liked' => $this->show_model->is_talkshow_liked($post["talkshow_id"], $post["user_id"]),
                    ], 404);
                }
            }
        }
    }

    /**
    * @apiGroup Show API
    * @api {get} favorites  Liked Show List
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function favorites_get() {
        header('Content-Type: application/json');
        $post = $_POST;
        $userdata = $this->auth();
        if ($userdata) {
            //person_id (optional)
            $post['user_id'] = $userdata['user_id'];
            $talkshow = $this->show_model->user_favourite_talkshow_list($post);

            if ($talkshow) {
                $this->response([
                        'status' => 'true',
                        'user_favourite_talkshow_list' => $talkshow,
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
    * @apiGroup Show API
    * @api {post} {show_id}/subscribers  Subscribe / Unsubscribe Show
    * @apiParam {String} is_subscribe 0 / 1
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function subscribers_post($show_id) {
        header('Content-Type: application/json');
        $post = $_POST;
        $post['talkshow_id'] = $show_id;

        $userdata = $this->auth();
        if ($userdata) {

            $required = ['is_subscribe'];
            if ($this->check_parameters($required)) {
                $post['user_id'] = $userdata['user_id'];
                $station = $this->show_model->make_sub_unsub_talkshow($post);

                if ($station === 1) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'Talkshow already subscribe',
                    ], 200);
                } else if ($station === 2) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Talkshow subscribe',
                    ], 200);
                } else if ($station === 3) {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Talkshow unsubscribe',
                    ], 200);
                } else if ($station === 4) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    ], 404);
                }
            }
        }
    }

    /**
    * @apiGroup Show API
    * @api {get} {show_id}/subscribers  Subscriber List
    * @apiParam {String} is_subscribe 0 / 1
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function subscribers_get($show_id) {
        header('Content-Type: application/json');
        $post = $_GET;
        $post['talkshow_id'] = $show_id;
        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $data = $this->show_model->talkshow_subscriber_list($post);
            if ($data) {
                $this->response([
                    'status' => 'true',
                    'subscriber_list' => $data,
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
    * @apiGroup Show API
    * @api {post} {show_id}/hosts  Add host to show
    * @apiParam {String} host_id User Id
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function hosts_post($show_id) {
        header('Content-Type: application/json');
        $post = $_POST;
        $post['talkshow_id'] = $show_id;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['host_id'];
            if ($this->check_parameters($required)) {
                $post['user_id'] = $userdata['user_id'];
                $data = $this->show_model->add_host_to_talkshow($post);
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
    * @apiGroup Show API
    * @api {get} {show_id}/hosts  Host list
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
            $data = $this->show_model->get_host_list($post);
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
    * @apiGroup Show API
    * @api {post} {show_id}/guests  Add guest to show
    * @apiParam {String} guest_id User Id
    * @apiParam {String} schedule_date
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function guests_post($show_id) {
        
        header('Content-Type: application/json');
        $post = $_POST;
        $post['talkshow_id'] = $show_id;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['guest_id', 'schedule_date'];
            if ($this->check_parameters($required)) {
                $post['user_id'] = $userdata['user_id'];
                $data = $this->show_model->add_guest_to_talkshow($post);
                if (!empty($data)) {
                    if ($data === 1) {
                        $this->response([
                            'status' => 'true',
                            'response_msg' => 'Invite sent to guest.',
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
    * @apiGroup Show API
    * @api {put} {show_id}/guests  accept / reject guest to show
    * @apiParam {String} schedule_date
    * @apiParam {String} status 1 : accept / 2 : reject
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function guests_put($show_id) {
        header('Content-Type: application/json');
        parse_str(file_get_contents('php://input'), $_PUT);
        $post = $_PUT;
        $_REQUEST = $_PUT;
        $post['talkshow_id'] = $show_id;
        $userdata = $this->auth();
        if ($userdata) { 
            $required = ['schedule_date', 'status'];
            if ($this->check_parameters($required)){
                $post['user_id'] = $userdata['user_id'];
                $data = $this->show_model->accept_reject_guest_to_talkshow($post);
              
                if ($data === 1) {
                    $this->response([
                            'status' => 'true',
                            'response_msg' => 'Invite Accepted.',
                        ], 200);
                }else if ($data === 2) {
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
    * @apiGroup Show API
    * @api {put} {show_id}/hosts  Accept / Reject host to show
    *
    * @apiParam {String} status 1 : accept / 2 : reject
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function hosts_put($show_id) {
        header('Content-Type: application/json');
        parse_str(file_get_contents('php://input'), $_PUT);
        $post = $_PUT;
        $_REQUEST = $_PUT;
        $post['talkshow_id'] = $show_id;
        $userdata = $this->auth();
        if ($userdata) {
            $required = ['status'];
            if ($this->check_parameters($required)){
                $post['user_id'] = $userdata['user_id'];
                $data = $this->show_model->accept_reject_host_to_talkshow($post);
                if ($data === 1) {
                    $this->response([
                            'status' => 'true',
                            'response_msg' => 'Invite Accepted.',
                        ], 200);
                }else if ($data === 2) {
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
    * @apiGroup Show API
    * @api {post} {show_id}/shares  Share Show
    * @apiParam {String} ids
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function shares_post($show_id) {
        header('Content-Type: application/json');
        $post = $_POST;
        $post['talkshow_id'] = $show_id;

        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $data = $this->show_model->share_talkshow($post);

            if (!empty($data)) {
                $this->response([
                        'status' => 'true',
                        'share_count' => $data
                    ], 200);
            } else {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'Show Not Shared.',
                    ], 200);
            }
            
        }
    }

    
    /**
    * @apiGroup Show API
    * @api {post} {show_id}/listeners  Show listeners
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function listeners_get($show_id) {
        header('Content-Type: application/json');
        $post = $_GET;
        $post['talkshow_id'] = $show_id; 
        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $station = $this->show_model->talkshow_listeners($post);
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
    * @apiGroup Show API
    * @api {post} {show_id}/listener_common_connections  Show listeners common connections
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function listener_common_connections_get($show_id) {
        header('Content-Type: application/json');
        $post = $_GET;
        $post['talkshow_id'] = $show_id;

        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $station = $this->show_model->talkshow_listener_common_connection($post);

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
    * @apiGroup Show API
    * @api {post} recommended  Recommended Shows
    *
    * @apiParam {String} chr
    * @apiParam {String} [offset]
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function recommended_get() {
        header('Content-Type: application/json');
        $post = $_GET;

        $userdata = $this->auth();
        if ($userdata) {
            $post['user_id'] = $userdata['user_id'];
            $station = $this->show_model->recommended_talkshow($post);

            if ($station) {
                $this->response([
                        'status' => 'true',
                        'talkshow_list' => $station,
                    ], 200);
            } else {
                $this->response([
                        'status' => 'false',
                        'response_msg' => 'No data found',
                    ], 404);
            }
        }
    }


    //get_talkshow_comments
    //get_tagged_talkshow_comment_list


}

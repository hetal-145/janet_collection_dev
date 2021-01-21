<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(dirname(__FILE__).'/Authentication.php'); //include controller

class Notifications extends Authentication{

    /**
    * @apiGroup Notifications API
    * @api {POST} https://api.wadio.app/v1/notifications 1. Api Url
    *
    */

    function __construct() {
        // Construct the parent class
        parent::__construct();
        //load model
        $this->load->model(basename(__DIR__)."/notification_model");
        $this->load->model(basename(__DIR__)."/user_model");
    }

    /**
    * @apiGroup Notifications API
    * @api {get} / Get All Notifications List
    * @apiParam {Number} [offset] Offset
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function index_get() {
        header('Content-Type: application/json');

        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {

            if (!isset($_GET['offset']) || !$_GET['offset']) {
                $post['offset'] = '0';
            }
            else {
                $post['offset'] = $_GET['offset'];
            }

            $post["user_id"] = $userdata["user_id"];
            $user = $this->notification_model->get_notification_list($post);
            $unread = $this->notification_model->get_unread_notification_list($post);

            if ($user) {
                $this->response([
                    'status' => 'true',
                    'unread_notification' => $unread,
                    'notification_list' => $user,
                ], 200);
                echo json_encode($this->response);
            } else {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'No notifications found.',
                ], 404);
                echo json_encode($this->response);
            }
        }
    }

    /**
    * @apiGroup Notifications API
    * @api {put} / Read All Notifications
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */

    /**
    * @apiGroup Notifications API
    * @api {put} /{notification_id} Read Single Notification
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function index_put($notification_id = null) {
        header('Content-Type: application/json');   

        parse_str(file_get_contents('php://input'), $_PUT);
        //print_r($_PUT); echo $notification_id; exit;
        $post = $_PUT;
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {

            $post["user_id"] = $userdata["user_id"];

            if(!empty($notification_id)) {

                $post["notification_id"] = $notification_id;

                $data = $this->notification_model->single_read_notification($post);
                if(!$data) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'Notification cannot be read',
                    ], 400);
                }
                else {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Notification read',
                    ], 200);
                }
            }
            else{
                $data = $this->notification_model->read_notification($post);
                if(!$data) {
                    $this->response([
                        'status' => 'false',
                        'response_msg' => 'Notification cannot be read',
                    ], 400);
                }
                else {
                    $this->response([
                        'status' => 'true',
                        'response_msg' => 'Notification read',
                    ], 200);
                }
            }

            echo json_encode($this->response);
        }
    }

    /**
    * @apiGroup Notifications API
    * @api {delete} /{notification_id} Delete Single Notification
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */

    /**
    * @apiGroup Notifications API
    * @api {delete} / Delete All Notifications
    *
    * @apiHeader {String} Authorization Login Token (user_id included) (JWT)
    *
    */
    public function index_delete($notification_id = null) {
        header('Content-Type: application/json');
       
        $userdata = $this->auth();

        if(!empty($userdata["user_id"])) {

            $post["user_id"] = $userdata["user_id"];

            if(!empty($notification_id)) {
                $post["notification_id"] = $notification_id;
            }

            $data = $this->notification_model->delete_notification($post);

            if ($data) {
                $this->response([
                    'status' => 'true',
                    'response_msg' => 'Notification deleted',
                ], 200);
                echo json_encode($this->response);
            } else {
                $this->response([
                    'status' => 'false',
                    'response_msg' => 'Notification cannot be deleted',
                ], 400);
                echo json_encode($this->response);
            }
        }
    }
}

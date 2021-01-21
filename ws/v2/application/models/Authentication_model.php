<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// require '../vendor/autoload.php';
use \Firebase\JWT\JWT;

class Authentication_model extends CI_Model {

    private $serverKey = '47L6PH59Xn6EynjZ5sa7XEwLD3hM4Ru9DaW4m9G';
    private $algo = 'HS256';
    private $read_db, $write_db;

	function __construct() {
        parent::__construct();   
        $this->read_db = $this->load->database('read', true);
        $this->write_db = $this->load->database('write', true);     
    }

    function check_auth() {
        $headers = $this->input->request_headers();

        if (!isset($headers['Authorization']) || $headers['Authorization'] == null) {
            return false;
        } else {
            return $headers['Authorization'];
        }
    }

    function check_auth_user_id($token) {
        //decode 
        $jwtdata = (array) JWT::decode($token, $this->serverKey, array($this->algo));        
        $user_id = $jwtdata["user_id"];

        if (!isset($user_id) || $user_id == null) {
            return false;            
        } else {
            $userdata = $this->get_user_by_user_id_token($user_id);
            if ($userdata) {
                return $userdata;
            } else {
                return false;
            }
        }
    }

    function validate_token($user_id, $token) {
        //$userdata = $this->get_user_by_user_id_token($user_id, $token);
        $userdata = $this->get_user_by_user_id_token($user_id);
        if ($userdata) {
            return $userdata;
        } else {
            return false;
        }
    }

    function authenticate() {
        $token1 = $this->check_auth();

        if ($token1) {
            $token = str_replace("Bearer ", "", $token1);
        
            $userdata = $this->check_auth_user_id($token);

            //if ($user_id) {
                //$userdata = $this->validate_token($user_id, $token);
                if ($userdata) {
                    return $userdata;
                }
                else {
                    return 1;
                }
            // }
            // else {
            //     return 2;
            // }
        }
        else {
            return 3;
        }
    }

    //function get_user_by_user_id_token($user_id, $token) {
    function get_user_by_user_id_token($user_id) {
        $this->read_db->select("user_id, token")
            ->where(array(
                'user_id' => $user_id,
                //'status' => 1,
                //'token' => $token,
            ));
        $userdata = $this->read_db->from('users')->get()->row_array();

        if ($userdata) {
            return $userdata;
        } else {
            return false;
        }
    }

    function auth2() {
        $token = $this->check_auth();

        if ($token) {
            $user_id = $this->check_auth_user_id();

            if ($user_id) {
                $userdata = $this->validate_token($user_id, $token);
                if ($userdata) {
                    return $userdata;
                }
            }
        }
    }

    function check_parameters($paras, $msg) {
        $return = TRUE;
        $not_set = '';

        foreach ($paras as $para) {
            //if (!isset($_POST[$para]) || $_POST[$para] == NULL) {
            if (!isset($_REQUEST[$para]) || $_REQUEST[$para] == NULL) {
                $return = FALSE;
                if ($not_set != '') {
                    $not_set .= ', ';
                }
                $not_set .= $para;
            }
        }

        if (!$return) {
            log_message('error', 'Parameters not set. ---> ' . $not_set);
            if ($msg) {
                return 1;
            } else {
                return 2;
            }
        }
        return $return;
    }

    // function display_system_error() {
    //     http_response_code(500);
    //     $this->response[] = array(
    //         'status' => 'false',
    //         'response_msg' => 'Server error. Something went wrong.',
    //     );
    //     echo json_encode(array('response' => $this->response));
    //     die;
    // }

    function generate_jwt_user_token($user_id){
        $nextday = date('Y-m-d', strtotime("+1 year"));
        $nbf = strtotime(date('Y-m-d H:i:s'));
        $exp = strtotime(''.$nextday.' 00:00:01');

        // create a token
        $payloadArray = array();
        $payloadArray['user_id'] = $user_id;
        if (isset($nbf)) {$payloadArray['nbf'] = $nbf;}
        if (isset($exp)) {$payloadArray['exp'] = $exp;}
        $token = JWT::encode($payloadArray, $this->serverKey, $this->algo);
        return $token;
    }
}
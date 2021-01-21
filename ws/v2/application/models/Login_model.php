<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model {

    private $read_db, $write_db;
	
	function __construct() {
        parent::__construct();
        $this->read_db = $this->load->database('read', true);
        $this->write_db = $this->load->database('write', true);
    }

    function get_user_by_user_id_token($user_id, $token) {
        $this->read_db->where(array(
            'user_id' => $user_id,
            'status' => 1,
            'token' => $token,
        ));
        $userdata = $this->read_db->from('users')->get()->row_array();

        if ($userdata) {
            return $userdata;
        } else {
            return false;
        }
    }

    function verify($sha1_user_id) {
        $this->read_db->select('status');
        $this->read_db->where('sha1(user_id)', $sha1_user_id);
        $row = $this->read_db->get('users')->row();
        if ($row) {
            //user found
            if ($row->status == '2') {
                //not verified - do verification
                $this->write_db->where('sha1(user_id)', $sha1_user_id)
                        ->set('status', 1)
                        ->set('is_email_verified', 1)
                        ->set('last_email_verified', date('Y-m-d H:i:s')) 
                        ->update('users');
                return '1';
            } else {
                //verified -
                return '2';
            }
        } else {
            //user not found
            return false;
        }
    }

    function generate_username() {
        $alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

    function generate_hash() {
        $alphabet = "abcdefghijklmnopqrstuvwxyz0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 4; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

    function signup($post = []) { 
        if (isset($_POST['password']) && $_POST['password']) {
            $post['password'] = sha1($_POST['password']);
        }
        
        $post['username'] = "wu" . $this->generate_username();
        $post['status'] = '2';
        $post['email'] = $_POST['email'];
        
        unset($post[0]);unset($post[1]);

        $this->write_db->insert('users', $post);
        $post['user_id'] = $this->write_db->insert_id();
        $post['token'] = '';
        return $post;
        $userdata = $this->user_model->get_user_by_id($post['user_id']);
        return $userdata;
    }

    function get_user_by_email($email) {
        $user = $this->read_db
                        ->where('email', $email)
                        ->get('users')->row_array();
        return $user;
    }

    function get_nuser_by_email($email) {
        $user = $this->read_db
                        ->where('email', $email)
                        ->get('users')->row_array();
        return $user;
    }

    function get_user_by_email_mobile($signin_username) {
        $userdata = array();
        // for email
        $userdata_email = $this->read_db
                        ->where('email', $signin_username)
                        ->where("status", 1)
                        ->from('users')->get()->row_array();

        if (!empty($userdata_email)) {
            $userdata = $userdata_email;
        } else {
            // for mobileno
            $userdata_mobileno = $this->read_db
                            ->where('phone_number', $signin_username)
                            ->where("status", 1)
                            ->from('users')->get()->row_array();

            $userdata = $userdata_mobileno;
        }

        return $userdata;
    }

    function get_user_username($user_id) {
        $user = $this->read_db->select("user_id, username, is_user_verified")
                        ->where('user_id', $user_id)
                        ->get('users')->row_array();
        return $user;
    }    

    function signin($post = []) {

        $userdata = array();
        // for email
        $userdata_email = $this->read_db
                        ->where('email', $post["email"])
                        ->where('password', sha1($post['password']))
                        ->from('users')->get()->row_array();

        if (!empty($userdata_email)) {
            $userdata = $userdata_email;
        } else {
            // for username
            $userdata_username = $this->read_db
                            ->where('username', $post["email"])
                            ->where('password', sha1($post['password']))
                            ->from('users')->get()->row_array();

            $userdata = $userdata_username;
        }

        if (empty($userdata)) {
            $userdata_email = $this->read_db
                            ->where('email', $post["email"])
                            ->where('is_password_reset', 1)
                            ->where('password_temp', sha1($post['password']))
                            ->from('users')->get()->row_array();

            if (!empty($userdata_email)) {
                $userdata = $userdata_email;
            } else {
                // for username
                $userdata_username = $this->read_db
                                ->where('username', $post["email"])
                                ->where('is_password_reset', 1)
                                ->where('password_temp', sha1($post['password']))
                                ->from('users')->get()->row_array();

                $userdata = $userdata_username;
            }
            //print_r($userdata);
        }

        return $userdata;
    }

    function update_login_details($post) {
        $ins_arr = array(
            'user_id' => $post["user_id"],
            'last_login' => date('Y-m-d H:i:s'),
            'is_online' => 1,
            // 'last_ipaddress' => $post["ipaddress"],
            // 'last_latitude' => $post["latitude"],
            // 'last_longitude' => $post["longitude"]
        );

        $row = $this->read_db->where('user_id', $post['user_id'])->get('user_connectivity')->row_array();

        if(!empty($row)) {
            $this->write_db->set($ins_arr)->where('user_id', $post['user_id'])->update('user_connectivity');
        }
        else {
            $this->write_db->insert('user_connectivity', $ins_arr);
        }
        return true;
    }

    function generate_random_password($user_id, $password) {
        $this->write_db->where(array(
            'user_id' => $user_id,
            'status' => 1
        ));
        $this->write_db->set('password_temp', sha1($password));
        $this->write_db->set('is_password_updated', 0);
        $this->write_db->set('last_password_updated', date('Y-m-d H:i:s')); 
        $this->write_db->set('is_password_reset', 1);
        $this->write_db->set('last_password_reset', date('Y-m-d H:i:s')); 
        if ($this->write_db->update('users')) {
            return true;
        } else {
            return false;
        }
    }

    function allow_facial_login($post){
        $check = $this->read_db->select("user_id")->where("user_id", $post["user_id"])->get("users")->row_array();

        if(!empty($check)) {
            $this->write_db->set('allow_faceid_login', $post["is_allow"])->where("user_id", $post["user_id"])->update("users");
            return true;
        }
        else {
            return true;
        }
    }

    function update_password($post = []) {
        if (isset($post['current_password']) && !empty($post['current_password'])) {
            $check = $this->read_db->select('user_id')
                            ->where('user_id', $post['user_id'])
                            ->where('password', sha1($post['current_password']))
                            ->get('users')->row_array();

            if (!empty($check)) {
                return $this->write_db->where('user_id', $post['user_id'])
                                ->set('password', sha1($post['password']))
                                ->set('password_temp', null)
                                ->set('is_password_updated', 1)
                                ->set('last_password_updated', date('Y-m-d H:i:s'))
                                ->update('users');
            } else {
                return false;
            }
        } else {
            return $this->write_db->where('user_id', $post['user_id'])
                            ->set('password', sha1($post['password']))
                            ->set('password_temp', null)
                            ->set('is_password_updated', 1)
                            ->set('last_password_updated', date('Y-m-d H:i:s'))
                            ->update('users');
        }
    }

    function delete_device_token($post = []) {
        $arr = array(
            'is_online' => 0,
            'last_online' => date('Y-m-d H:i:s'),
            'last_logout' => date('Y-m-d H:i:s'),
            'device_token' => null,
            'device_id' => null,
            'device_name' => null,
            'device_type' => null,
            'last_ipaddress' => (!empty($post["ipaddress"])) ? $post["ipaddress"] : null,
            'last_latitude' => (!empty($post["latitude"])) ? $post["latitude"] : null,
            'last_longitude' => (!empty($post["longitude"])) ? $post["longitude"] : null
        );

        return $this->write_db->set($arr)
                        ->where('user_id', $post['user_id'])
                        ->where('device_token', $post['device_token'])
                        ->update('user_connectivity');
    }

    function check_email_id_signup($post = []) {
        // for email
        $userdata_email = $this->read_db
                        ->where('email', $post["email"])
                        ->from('users')->get()->row_array();

        if (!empty($userdata_email)) {
            if ($userdata_email["status"] == '1') {
                $userdata = $userdata_email;
            } else if ($userdata_email["status"] == '2') {
                return 5;
            } else if ($userdata_email["status"] == '3') {
                return 2;
            } else {
                return 3;
            }
        } else {
            // for username
            $userdata_username = $this->read_db
                            ->where('username', $post["email"])
                            ->from('users')->get()->row_array();

            if (!empty($userdata_username)) {
                if ($userdata_username["status"] == '1') {
                    $userdata = $userdata_username;
                } else if ($userdata_email["status"] == '2') {
                    return 5;
                } else if ($userdata_username["status"] == '3') {
                    return 2;
                } else {
                    return 3;
                }
            } else {
                return 2;
            }
        }

        if ($userdata) {
            return 1;
        } else {
            return 2;
        }
    }    

    function update_login_token($user_id, $token) {
        $this->write_db->set('token', $token);
        $this->write_db->where('user_id', $user_id);
        if ($this->write_db->update('users')) {
            return true;
        } else {
            return false;
        }
    }

    function check_profile_complition_and_get_screen_code($user_id) {
        $row = $this->read_db->where('user_id', $user_id)->get('users')->row_array();
        if ($row) {
            // print_r($row); exit;
            if ($row['is_profile_updated'] == '1') {
                return '200';
            } else {
                return '307';
            }
        } else {
            return '307';
        }
    }

    function check_password_update_and_reset($user_id, $screen_code, $password) {
        $row = $this->read_db->where('user_id', $user_id)->get('users')->row_array();
        if ($row) {
            // print_r($row); exit;
            if ($row['password'] == sha1($password) && $row["is_profile_updated"] == 1) {
                $screen_code1 = '200';
            }
            else if ($row['password'] == sha1($password) && $row["is_profile_updated"] == 0) {
                $screen_code1 = '307';
            }
            else if ($row['password_temp'] == sha1($password) && $row["is_password_reset"] == 1) {
                $screen_code1 = '206';
            } 

            if($screen_code == $screen_code1) {
                return $screen_code;
            }  
            else {
                return $screen_code1;
            }    
        } else {
            return '206';
        }
    }

    function check_update_device_token($post = []) {

        $post['device_type'] = (isset($post['device_type']) && $post['device_type'] != null) ? $post['device_type'] : '';
        $post['device_token'] = (isset($post['device_token']) && $post['device_token'] != null) ? $post['device_token'] : '';
        $post['device_id'] = (isset($post['device_id']) && $post['device_id'] != null) ? $post['device_id'] : '';
        $post['device_name'] = (isset($post['device_name']) && $post['device_name'] != null) ? $post['device_name'] : '';
        $post['app_version'] = (isset($post['app_version']) && $post['app_version'] != null) ? $post['app_version'] : '';

        if ($post['device_type'] == '' || $post['device_token'] == '') {
            return false;
        } else if ($post['device_type'] == 'android' && $post['device_id'] == '') {
            return false;
        }

        $this->read_db->where(array(
            'user_id' => $post['user_id']
        ));
        $row = $this->read_db->from('user_connectivity')->get()->row_array();

        
        if ($post['device_type'] == 'ios') {
            $arr = array(
                'user_id' => $post['user_id'],
                'device_token' => $post['device_token'],
                'device_type' => $post['device_type'],
                'device_name' => $post['device_name'],
                'app_version' => $post['app_version'],
                'created_at' => date('Y-m-d h:i:s'),
                'status' => 1,
            );

            if(!empty($row)) {                                
                $this->write_db->set($arr);
                $this->write_db->where(array(
                    'user_id' => $post['user_id']
                ));
                $this->write_db->update('user_connectivity');
            }
            else {
                $this->write_db->insert('user_connectivity', $arr);
            }
        }
        else if ($post['device_type'] == 'android') {
            $arr1 = array(
                'user_id' => $post['user_id'],
                'device_token' => $post['device_token'],
                'device_type' => $post['device_type'],
                'device_id' => $post['device_id'],
                'device_name' => $post['device_name'],
                'app_version' => $post['app_version'],
                'created_at' => date('Y-m-d h:i:s'),
                'status' => 1,
            );

            if(!empty($row)) {                                
                $this->write_db->set($arr1);
                $this->write_db->where(array(
                    'user_id' => $post['user_id']
                ));
                $this->write_db->update('user_connectivity');
            }
            else {
                $this->write_db->insert('user_connectivity', $arr1);
            }
        }
        else {
            return false;
        }
    }

    function get_user_by_email_username($email = '') {
        // for email
        $userdata_email = $this->read_db
                        ->where('email', $email)
                        ->from('users')->get()->row_array();

        if (!empty($userdata_email)) {

            if (!empty($userdata_email)) {
                return $userdata_email;
            } else {
                return 1;
            }
        } else {
            // for username
            $userdata_username = $this->read_db
                            ->where('username', $email)
                            ->from('users')->get()->row_array();

            if (!empty($userdata_username)) {
                if ($userdata_username["is_user_verified"] == 0) {
                    $check2 = $this->read_db->select("user_id")
                                    ->where("email", $userdata_username["email"])
                                    ->get("users")->result_array();

                    if (count($check2) > 1) {
                        $rt[0] = 3;
                        $rt[1] = $userdata_username["user_type_id"];
                        return $rt;
                    } else if (count($check2) == 1) {
                        return $userdata_username;
                    }
                } else {
                    $rt[0] = 4;
                    $rt[1] = $userdata_username["user_type_id"];
                    return $rt;
                }
            } else {
                return 2;
            }
        }
    }

    function change_mobile_number($post = []) {
        $check = $this->read_db->select("user_id")
            ->where("user_id", $post["user_id"])
            ->get("users");

        if(!empty($check)) {
            if(isset($post["phone_code"])) {
                $phone_code = $post["phone_code"];
            }
            else {
                $phone_code = "";
            }

            $ins_arr = array(
                "phone_number" => $post["phone_number"],
                "phone_code" => $phone_code
            );
            $this->write_db->set($ins_arr)->where("user_id", $post["user_id"])->update("users");

            return true;
        }
        else {
            return false;
        }
    }

    function check_email_id($post = []) {
        //print_r($post); exit;
        $userdata = $this->read_db
                ->where('email', $post['email'])
                ->where('status', 1)
                ->get('users')
                ->row_array();
        if ($userdata) {
            return $userdata;
        }
    }
}
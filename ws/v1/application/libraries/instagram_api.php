<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once APPPATH . '/third_party/Instagram-API-master/src/Instagram.php';

class Instagram_api extends Instagram {

    public function __construct($config=[]) {
        $username = $config['username'];
        $password = $config['password']; 
        $debug = $config['debug'];
        $cookie_path = $config['cookie_path'];
        parent::__construct($username, $password, $debug, $cookie_path);
    }

}
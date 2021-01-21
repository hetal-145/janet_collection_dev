<?php

/*
  |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
  || Apple Push Notification Configurations
  |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
 */


/*
  |--------------------------------------------------------------------------
  | APN Permission file
  |--------------------------------------------------------------------------
  |
  | Contains the certificate and private key, will end with .pem
  | Full server path to this file is required.
  |
 */
$config['PermissionFileDev'] = $_ENV['APN_API_KEY'];
$config['PermissionFileProd'] = $_ENV['APN_API_KEY'];



/*
  |--------------------------------------------------------------------------
  | APN Private Key's Passphrase
  |--------------------------------------------------------------------------
 */
$config['PassPhrase'] = $_ENV['APN_KEY_PWD'];

/*
  |--------------------------------------------------------------------------
  | APN Services
  |--------------------------------------------------------------------------
 */

//$config['Sandbox'] = true; //dev
//$config['Sandbox'] = false; //prod



/*
  |--------------------------------------------------------------------------
  | APN Permission file
  |--------------------------------------------------------------------------
  |
  | Contains the certificate and private key, will end with .pem
  | Full server path to this file is required.
  |
 */

$headers = getallheaders();

$config['Sandbox'] = false; //prod
if (isset($headers['Env']) && $headers['Env']) {
    if (strtolower($headers['Env']) == 'd') {
        $config['Sandbox'] = true; //dev
    } else {
        $config['Sandbox'] = false; //prod
    }
}

$config['PermissionFile'] = $config['PermissionFileProd'];
if ($config['Sandbox']) {
    $config['PermissionFile'] = $config['PermissionFileDev'];
}




$config['PushGatewaySandbox'] = 'ssl://gateway.sandbox.push.apple.com:2195';






$config['PushGateway'] = 'ssl://gateway.push.apple.com:2195';


$config['FeedbackGatewaySandbox'] = 'ssl://feedback.sandbox.push.apple.com:2196';
$config['FeedbackGateway'] = 'ssl://feedback.push.apple.com:2196';


/*
  |--------------------------------------------------------------------------
  | APN Connection Timeout
  |--------------------------------------------------------------------------
 */
$config['Timeout'] = 60;


/*
  |--------------------------------------------------------------------------
  | APN Notification Expiry (seconds)
  |--------------------------------------------------------------------------
  | default: 86400 - one day
 */
$config['Expiry'] = 86400;

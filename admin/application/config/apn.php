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
/*$config['PermissionFileDev'] = '/var/www/html/7alaki/development.pem';
$config['PermissionFileProd'] = '/var/www/html/7alaki/production.pem';*/

$config['PermissionFileDev'] = '/var/www/html/notification/Certificates_PreDrink_PushDist.pem';
$config['PermissionFileProd'] = '/var/www/html/notification/Certificates_PreDrink_PushDist.pem';

$config['PermissionFile_Driver_Dev'] = '/var/www/html/notification/dist_push_pdDriver.pem';
$config['PermissionFile_Driver_Prod'] = '/var/www/html/notification/dist_push_pdDriver.pem';

/*
  |--------------------------------------------------------------------------
  | APN Private Key's Passphrase
  |--------------------------------------------------------------------------
 */
$config['PassPhrase'] = 'Prismetric1';

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

$config['Sandbox'] = true; //prod
if (isset($headers['Env']) && $headers['Env']) {
    if (strtolower($headers['Env']) == 'd') {
        $config['Sandbox'] = true; //dev
    } else {
        $config['Sandbox'] = false; //prod
    }
}

$config['PermissionFile'] = $config['PermissionFileProd'];
$config['PermissionFile_Driver'] = $config['PermissionFile_Driver_Prod'];
if ($config['Sandbox']) {
    $config['PermissionFile'] = $config['PermissionFileDev'];
    $config['PermissionFile_Driver'] = $config['PermissionFile_Driver_Dev'];
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

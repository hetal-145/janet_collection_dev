<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
  |--------------------------------------------------------------------------
  | File and Directory Modes
  |--------------------------------------------------------------------------
  |
  | These prefs are used when checking and setting modes when working
  | with the file system.  The defaults are fine on servers with proper
  | security, but you may wish (or even need) to change the values in
  | certain environments (Apache running a separate process for each
  | user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
  | always be used to set the mode correctly.
  |
 */
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
  |--------------------------------------------------------------------------
  | File Stream Modes
  |--------------------------------------------------------------------------
  |
  | These modes are used when working with fopen()/popen()
  |
 */

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');
define('CURRENCY', 'GBP');
define('CURRENCY_CODE', '£');
define('COUNTRY', 'GB');
define('LIMIT', 20);
define('INQUIRY_EMAIL', 'shoutout@drinxin.com');
//define('INQUIRY_EMAIL', 'purvi@prismetric.com');

// S3 details
define('PRODUCT_S3_PATH', 'https://drinxin-media.s3.eu-west-2.amazonaws.com/product/');
define('S3_PATH', 'https://drinxin-media.s3.eu-west-2.amazonaws.com/');
define('BUCKET_NAME', 'drinxin-media');
define('PUBLISHED_KEY', 'AKIAIEFYRKMEV3AXPNEQ');
define('SECRET_KEY', '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');

/* End of file constants.php */
/* Location: ./application/config/constants.php */

define('APP_VERSION', 'v1');




// service use varibles

$date= date('Y-m-d H:i:s');
define('CREATED_AT', $date);

//NEXMO
define('SMS_USER', '447577998383');
define('SMS_PASSWORD', 'Diving12345');
define('SMS_SENDER', '447577998383');
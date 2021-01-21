<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');
define('INQUIRY_EMAIL', 'shoutout@Janet-Collection.com');
//define('INQUIRY_EMAIL', 'purvi@prismetric.com');
define('CURRENCY', 'GBP');
define('COUNTRY', 'GB');
define('CURRENCY_CODE', '£');
define('LIMIT', 20);


// S3 details
define('PRODUCT_S3_PATH', 'https://Janet-Collection-media.s3.eu-west-2.amazonaws.com/product/');
define('S3_PATH', 'https://Janet-Collection-media.s3.eu-west-2.amazonaws.com/');
define('BUCKET_NAME', 'Janet-Collection-media');
define('PUBLISHED_KEY', 'AKIAIEFYRKMEV3AXPNEQ');
define('SECRET_KEY', 'ZMxilc3jOlSkGuY+N8pNGJMJU/jtGY/YTdjhsJsL');
define('PLACEHOLDER', '../assets/website/placeholder.png');

//Social signin keys
define('GOOGLE_CLIENT_SECRET', 'LxURekcHGQ8ilQEnAYzOVyF1');
define('GOOGLE_CLIENT_ID', '950886894490-um9v0pr5mhnmpopqo2n9dloiqgj1k6lp.apps.googleusercontent.com');
define('GOOGLE_REDIRECT_URL', 'https://www.Janet-Collection.com/');

/* End of file constants.php */
/* Location: ./application/config/constants.php */
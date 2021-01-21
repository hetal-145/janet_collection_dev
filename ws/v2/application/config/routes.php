<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/


$version = 'v1/';

$route['default_controller'] = '';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

//dataset
$route['legal/pp']['get'] = 'datasets/privacy_policy';
$route['legal/toc']['get'] = 'datasets/term_and_conditions';
$route['legal/notices']['get'] = 'datasets/legal_notice';
$route['legal']['get'] = 'datasets/legal';
$route['countries']['get'] = 'datasets/countries';
$route['languages']['get'] = 'datasets/languages';
$route['languages/(:any)']['get'] = 'datasets/languages/$1';
$route['genres']['get'] = 'datasets/genres';
$route['genres/(:num)']['get'] = 'datasets/genres/$1';
$route['categories']['get'] = 'datasets/categories';
$route['add_recent_search']['post'] = 'datasets/add_recent_search';
$route['get_recent_search']['get'] = 'datasets/get_recent_search';
$route['clear_recent_search']['delete'] = 'datasets/clear_recent_search';
$route['search_all/(:any)']['get'] = 'datasets/search_all/$1';
$route['report_reasons']['get'] = 'datasets/report_reasons';



$route['media']['get'] = 'media'; // Request a S3 PresignedUrl to read a s3 file
$route['media']['post'] = 'media'; // Request a S3 PresignedUrl for upload to s3 from client

//notifications
$route['notifications']['put'] = 'notifications';
$route['notifications/(:num)']['put'] = 'notifications/$1';
$route['notifications']['delete'] = 'notifications';
$route['notifications/(:num)']['delete'] = 'notifications/$1';
$route['notifications']['get'] = 'notifications';

//login
$route['auth/signup']['post'] = 'login/signup';
$route['auth/verify']['get'] = 'login/verify';
$route['auth/signin']['post'] = 'login/signin';
$route['auth/resend_verification_mail']['post'] = 'login/resend_verification_mail';
$route['auth/forgot_password']['post'] = 'login/forgot_password';
$route['auth/update_device_token']['post'] = 'login/update_device_token';
$route['auth/allow_facial_login']['post'] = 'login/allow_facial_login';
$route['auth/change_password']['post'] = 'login/change_password';
$route['auth/logout']['post'] = 'login/logout';
$route['auth/check_email_registered']['post'] = 'login/check_email_registered';
$route['auth/check_username_registered']['post'] = 'login/check_username_registered';
$route['auth/change_mobile_number']['post'] = 'login/change_mobile_number';
$route['auth/check_email_verified']['post'] = 'login/check_email_verified';
$route['auth/test_email']['post'] = 'login/test_email';

//users
$route['users']['get'] = 'users/index';
$route['users']['post'] = 'users/index';
$route['users']['put'] = 'users/index';
$route['users']['delete'] = 'users/index';
$route['users/search']['get'] = 'users/search';
$route['users/search/(:any)']['get'] = 'users/search/$1';
$route['users/(:num)/person_profile']['get'] = 'users/person_profile/$1';
$route['users/(:num)/connection_list']['get'] = 'users/connection_list/$1';
$route['users/(:num)/unsend_a_request']['delete'] = 'users/unsend_a_request/$1';
$route['users/(:num)/report']['post'] = 'users/report/$1';
$route['users/phone_book']['post'] = 'users/phone_book';
$route['users/send_an_invite']['post'] = 'users/send_an_invite';
$route['users/accept_an_invite']['post'] = 'users/accept_an_invite';
$route['users/update_current_location_details']['post'] = 'users/update_current_location_details';

//Stations
$route['stations']['get'] = 'stations';
$route['stations/(:num)']['get'] = 'stations/$1';
$route['stations/(:num)']['put'] = 'stations/$1';
$route['stations/(:num)/comments']['get'] = 'stations/comments/$1';
$route['stations/(:num)/played']['get'] = 'stations/played/$1';
$route['stations/(:num)/top']['get'] = 'stations/top/$1';
$route['stations/(:num)/listeners']['get'] = 'stations/listeners/$1';
$route['stations/(:num)/shows']['get'] = 'stations/shows/$1';
$route['stations/(:num)/favorites']['post'] = 'stations/favorites/$1';
$route['stations/(:num)/favorites']['get'] = 'stations/favorites/$1';
$route['stations/(:num)/save']['post'] = 'stations/save/$1';
$route['stations/(:num)/recents']['get'] = 'stations/recents/$1';
$route['stations/(:num)/genres']['get'] = 'stations/genres/$1';
$route['stations/(:num)/shares']['post'] = 'stations/shares/$1';
$route['stations/(:num)/hosts']['post'] = 'stations/hosts/$1';
$route['stations/(:num)/hosts']['get'] = 'stations/hosts/$1';
$route['stations/(:num)/hosts']['put'] = 'stations/hosts/$1';
$route['stations/(:num)/popular_artists']['get'] = 'stations/popular_artists/$1';
$route['stations/(:num)/most_liked_tracks']['get'] = 'stations/most_liked_tracks/$1';
$route['stations/(:num)/listener_common_connections']['get'] = 'stations/listener_common_connections/$1';
$route['stations/(:num)/subscribe']['post'] = 'stations/subscribe/$1';
$route['stations/(:num)/subscribers']['get'] = 'stations/subscribers/$1';
$route['stations/(:num)/recommended']['get'] = 'stations/recommended/$1';
$route['stations/(:num)/recent_tracks']['get'] = 'stations/recent_tracks/$1';
$route['stations/(:num)/report']['post'] = 'stations/report/$1';


//Shows
$route['shows']['post'] = 'shows';
$route['shows/(:num)']['get'] = 'shows/$1';
$route['shows/(:num)']['put'] = 'shows/$1';
$route['shows/(:num)']['delete'] = 'shows/$1';
$route['shows/(:num)/likes']['post'] = 'shows/likes/$1';
$route['shows/(:num)/subscribers']['post'] = 'shows/subscribers/$1';
$route['shows/(:num)/subscribers']['get'] = 'shows/subscribers/$1';
$route['shows/favorites']['get'] = 'shows/favorites';
$route['shows/(:num)/hosts']['post'] = 'shows/hosts/$1';
$route['shows/(:num)/hosts']['put'] = 'shows/hosts/$1';
$route['shows/(:num)/hosts']['get'] = 'shows/hosts/$1';
$route['shows/(:num)/guests']['post'] = 'shows/guests/$1';
$route['shows/(:num)/guests']['put'] = 'shows/guests/$1';
$route['shows/(:num)/shares']['post'] = 'shows/shares/$1';
$route['shows/(:num)/listeners']['get'] = 'shows/listeners/$1';
$route['shows/(:num)/listener_common_connections']['get'] = 'shows/listener_common_connections/$1';
$route['shows/(:num)/recommended']['get'] = 'shows/recommended/$1';


//Artists
$route['artists']['get'] = 'artists';
$route['artists/(:num)']['get'] = 'artists/$1';
$route['artists/(:num)']['put'] = 'artists/$1';
$route['artists/(:num)/report']['post'] = 'artists/report/$1';
$route['artists/(:num)/shares']['post'] = 'artists/subscribe/$1';
$route['artists/(:num)/subscribe']['post'] = 'artists/subscribe/$1';
$route['artists/(:num)/subscribers']['get'] = 'artists/subscribers/$1';
$route['artists/(:num)/favorites']['post'] = 'artists/favorites/$1';
$route['artists/favorites']['get'] = 'artists/favorites';


//Album
$route['albums/(:num)/tracks']['get'] = 'albums/tracks/$1';

//Track
$route['tracks/(:num)']['get'] = 'tracks/$1';
$route['tracks/(:num)/favorites']['post'] = 'tracks/favorites/$1';
$route['tracks/favorites']['get'] = 'tracks/favorites';
$route['tracks/(:num)/shares']['post'] = 'tracks/shares/$1';

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include('../vendor/autoload.php');
require '../vendor_aws/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class Websetting extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('m_tools');
	$this->load->model('m_setting');
    }

    public function index() {}
    
    public function social_media() {
	$data['setting_data'] = $this->m_setting->get_setting_data();    
        $this->load->view('header', $data);
        $this->load->view('social_media', $data);
        $this->load->view('footer');
    }
    
    public function website_home()
    { 
	$data['setting_data'] = $this->m_setting->get_setting_data();    
//	print_r($data['setting_data']); exit;
        $this->load->view('header', $data);
        $this->load->view('website_home', $data);
        $this->load->view('footer');
    }
    
    public function save_home_content(){
	$post = $_POST;
	$files = $_FILES;
	$msg = '';
	
//	print_r($post); print_r($files); exit;
	
	//bucket info
	$credentials = new Aws\Credentials\Credentials(PUBLISHED_KEY, SECRET_KEY);
	$s3 = new S3Client([        
	    'region' => 'eu-west-2',
	    'version' => 'latest',
	    "credentials" => $credentials
	]);

	if(!empty($files["home_banner_video"]['name'])) {
	    $ext_arr2 = array('mp4', 'avi');
	    $ext = '.' . pathinfo($files['home_banner_video']['name'], PATHINFO_EXTENSION);
	    $ext1 = pathinfo($files['home_banner_video']['name'], PATHINFO_EXTENSION);
	    $filename = date('YmdHis') . rand() . strtolower($ext);
	    $keyname = $filename;
	    $filepath = $files['home_banner_video']['tmp_name'];
	    $mime = mime_content_type($filepath); 

	    if( in_array(strtolower($ext1), $ext_arr2) ) {
		$result = $s3->putObject(array(
		    'Bucket' => BUCKET_NAME,
		    'Key' => $keyname,
		    'SourceFile' => $filepath,
		    'ACL' => 'public-read',
		    'StorageClass' => 'STANDARD',
		    'ContentType'=>$mime
		));

		$this->db->set("value", S3_PATH . $filename)
			->where("key", "home_banner_video")
			->update("setting");
	    }
	    else {
		$msg .= '1. The filtype for video you are trying to upload is not allowed';
	    }
        }
	
	if(!empty($files["home_banner_image"]['name'])) {
	    $ext_arr2 = array('jpg', 'png', 'jpeg');
	    $ext = '.' . pathinfo($files['home_banner_image']['name'], PATHINFO_EXTENSION);
	    $ext1 = pathinfo($files['home_banner_image']['name'], PATHINFO_EXTENSION);
	    $filename = date('YmdHis') . rand() . strtolower($ext);
	    $keyname = $filename;
	    $filepath = $files['home_banner_image']['tmp_name'];
	    $mime = mime_content_type($filepath); 

	    if( in_array(strtolower($ext1), $ext_arr2) ) {
		$result = $s3->putObject(array(
		    'Bucket' => BUCKET_NAME,
		    'Key' => $keyname,
		    'SourceFile' => $filepath,
		    'ACL' => 'public-read',
		    'StorageClass' => 'STANDARD',
		    'ContentType'=>$mime
		));

		$this->db->set("value", S3_PATH . $filename)
			->where("key", "home_banner_image")
			->update("setting");
	    }
	    else {
		$msg .= '<br> 2. The filtype for image you are trying to upload is not allowed';
	    }
        }
	
	$update_settings = $this->m_setting->update_settings($post);
        if (empty($msg)) {
            echo 'success';
        }
	else {
	    echo $msg;
	}
    }
    
}
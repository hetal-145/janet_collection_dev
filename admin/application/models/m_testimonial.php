<?php

require '../vendor_aws/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class M_testimonial extends CI_Model {

    public function get_testimonial_data($testimonials_id) {
        $tc = $this->db->select('*')
                ->where('testimonials_id', $testimonials_id)		
                ->get('testimonials')
                ->row_array();
        return $tc;
    }

    public function update_testimonial_content($post = [], $files = []) {
	//print_r($post); print_r($files); exit;
	
	unset($post["action"]);
	
	///bucket info
	$credentials = new Aws\Credentials\Credentials(PUBLISHED_KEY, SECRET_KEY);
	$s3 = new S3Client([        
	    'region' => 'eu-west-2',
	    'version' => '2006-03-01',
	    //'debug' => true,
	    "credentials" => $credentials
	]);
	
	if (isset($files['image']['name']) && $files['image']['name']) {
	    $ext = '.' . pathinfo($files['image']['name'], PATHINFO_EXTENSION);
	    $filename = date('YmdHis') . rand() . strtolower($ext);
	    $keyname = $filename;
	    $filepath = $files['image']['tmp_name'];

	    $result = $s3->putObject(array(
		'Bucket' => BUCKET_NAME,
		'Key' => 'testimonial/'.$keyname,
		'SourceFile' => $filepath,
		'ACL' => 'public-read',
		'StorageClass' => 'STANDARD'
	    ));
	    
	    $result2 = $s3->putObject(array(
		'Bucket' => BUCKET_NAME,
		'Key' => 'testimonial/thumbs/'.$keyname,
		'SourceFile' => $filepath,
		'ACL' => 'public-read',
		'StorageClass' => 'STANDARD'
	    ));

	    $post['image'] = $filename;
	}
	
	if(!empty($post['testimonials_id'])){
	    $this->db->set($post)->where("testimonials_id", $post["testimonials_id"])->update("testimonials");
	}else {
	    unset($post["testimonials_id"]);
	    $this->db->insert("testimonials", $post);
	} 	
	
        return 'success';
    }

}

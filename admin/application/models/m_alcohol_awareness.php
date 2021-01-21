<?php

require '../vendor_aws/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class M_alcohol_awareness extends CI_Model {

    public function get_alcohol_awareness_data($aid) {
        $tc = $this->db->select('*')
                ->where('aid', $aid)		
                ->get('alcohol_awareness')
                ->row_array();
        return $tc;
    }

    public function update_alcohol_awareness_content($post = [], $files = []) {
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
		'Key' => 'alcohol_awareness/'.$keyname,
		'SourceFile' => $filepath,
		'ACL' => 'public-read',
		'StorageClass' => 'STANDARD'
	    ));
	    
	    $result2 = $s3->putObject(array(
		'Bucket' => BUCKET_NAME,
		'Key' => 'alcohol_awareness/thumbs/'.$keyname,
		'SourceFile' => $filepath,
		'ACL' => 'public-read',
		'StorageClass' => 'STANDARD'
	    ));

	    $post['image'] = $filename;	    
	}
	
	if(!empty($post['aid'])){
	    $this->db->set($post)->where("aid", $post["aid"])->update("alcohol_awareness");
	}else {
	    unset($post["aid"]);
	    $this->db->insert("alcohol_awareness", $post);
	} 	
	
        return 'success';
    }

}

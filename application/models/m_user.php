<?php
require 'vendor_aws/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class M_user extends CI_Model{
    
    public function user_profile($user_id = '') {
        $user_data = $this->db->select('*, concat(firstname," ",lastname) as name', false)
                ->where('u.user_id', $user_id)
                ->get('user u')
                ->row_array();
        
        if(!empty($user_data["verification_doc"]) && $user_data["is_admin_verified"] == 1) {
            $user_data["verification_doc"] = $this->m_tools->image_url( $user_data['verification_doc'],'', 'verification_docs'); 
            $doc_uploaded = true;
            $msg = "";
        } 
        else if(!empty($user_data["verification_doc"]) && $user_data["is_admin_verified"] == 0) {
            $user_data["verification_doc"] = $this->m_tools->image_url( $user_data['verification_doc'],'', 'verification_docs'); 
            $doc_uploaded = true;
            $msg = "";
        } 
        else if(empty($user_data["verification_doc"]) && $user_data["is_admin_verified"] == 1) {
            $doc_uploaded = true;
            $msg = "Your age has been verified by admin";
        }
        else {
           $doc_uploaded = false; 
           $msg = "";
        }
        
        $user_data['doc_uploaded'] = $doc_uploaded;
        $user_data['verify_msg'] = $msg;
        
        unset($user_data["password"]);
        unset($user_data["password_updated"]);
        unset($user_data["token"]);

        return $user_data;
    }
    
    public function update_profile($post = [], $files = []) {
	
	///bucket info
	$credentials = new Aws\Credentials\Credentials(PUBLISHED_KEY, SECRET_KEY);
	$s3 = new S3Client([        
	    'region' => 'eu-west-2',
	    'version' => '2006-03-01',
	    //'debug' => true,
	    "credentials" => $credentials
	]);
                
        if(!empty($files["usr_prof_img"]["name"])){
	    $ext = '.' . pathinfo($files['usr_prof_img']['name'], PATHINFO_EXTENSION);
	    $filename = date('YmdHis') . rand() . strtolower($ext);
	    $keyname = $filename;
	    $filepath = $files['usr_prof_img']['tmp_name'];

	    $result = $s3->putObject(array(
		'Bucket' => BUCKET_NAME,
		'Key' => $keyname,
		'SourceFile' => $filepath,
		'ACL' => 'public-read',
		'StorageClass' => 'STANDARD'
	    ));
	    
	    $post["profile_image"] = $filename;  
	    //$this->m_tools->thumbCreate(S3_PATH, S3_PATH.'thumbs/', $filename, 300);
        }
        
        $updated = $this->db
                ->where('user_id', $post['user_id'])
                ->set($post)
                ->update('user');
        if ($updated) {
            return 1;
        }
        else {
            return 2;
        }
    }
}


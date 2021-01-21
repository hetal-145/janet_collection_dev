<?php

require '../vendor_aws/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class M_upload_documents extends CI_Model {
    
    public function delete($post=[]) {
	//print_r($post); exit;
	$exists = $this->db->select('*')->where('status', 1)->get('seller_verifications')->result_array();
	if(!empty($exists)) {
	    $this->db->set("status", 0)->where("id", $post["id"])->update("seller_verifications");
	    return 'success';
	}
    }
    
    public function get_documents($seller_id) {
        $docs = $this->db->select("id, doc_name")
                ->where('seller_id', $seller_id)
		->where("status", 1)
                ->get('seller_verifications')->result_array();
        if(!empty($docs)){
            return $docs;
        }
    }
    
    public function get_seller($seller_id) {
        $docs = $this->db->select("seller_id, seller_name")
                ->where('seller_id', $seller_id)
		->where("status", 1)
                ->get('seller')->row_array();
        if(!empty($docs)){
            return $docs;
        }
    }
    
    public function save($post=[], $files=[]){
        //print_r($files); exit;
        $exists = $this->db->select('*')->where('seller_id', $post["seller_id"])->get('seller')->row_array();
        
        if(!empty($exists)) {   
	    
	    if (isset($files['doc_name']) ){
		
		///bucket info
		$credentials = new Aws\Credentials\Credentials(PUBLISHED_KEY, SECRET_KEY);
		$s3 = new S3Client([        
		    'region' => 'eu-west-2',
		    'version' => '2006-03-01',
		    //'debug' => true,
		    "credentials" => $credentials
		]);
		
		for($i=0; $i < count($files["doc_name"]["name"]); $i++) {
		    if( !empty($files["doc_name"]["name"][$i]) ) {
			$_FILES["verify_doc"]["name"] = $files["doc_name"]["name"][$i];
			$_FILES["verify_doc"]["type"] = $files["doc_name"]["type"][$i];
			$_FILES["verify_doc"]["tmp_name"] = $files["doc_name"]["tmp_name"][$i];
			$_FILES["verify_doc"]["error"] = $files["doc_name"]["error"][$i];
			$_FILES["verify_doc"]["size"] = $files["doc_name"]["size"][$i];
			
			$ext = '.' . pathinfo($_FILES['verify_doc']['name'], PATHINFO_EXTENSION);
			$filename = date('YmdHis') . rand() . strtolower($ext);
			$keyname = $filename;
			$filepath = $_FILES['verify_doc']['tmp_name'];

			$result = $s3->putObject(array(
			    'Bucket' => BUCKET_NAME,
			    'Key' => 'seller/'.$keyname,
			    'SourceFile' => $filepath,
			    'ACL' => 'public-read',
			    'StorageClass' => 'STANDARD'
			));
			
			 $inr = array(
			    'seller_id' => $post["seller_id"],
			    'doc_name' => $filename
			);			    

			$this->db->insert('seller_verifications', $inr);
			
			//print_r($_FILES); exit;
			
//			$ext = '.' . pathinfo($_FILES["verify_doc"]["name"], PATHINFO_EXTENSION);
//			$filename = date('YmdHis') . rand() . strtolower($ext);
//			$config = [
//			    'upload_path' => '../upload/seller',
//			    'allowed_types' => 'gif|jpg|png|jpeg',
//			    'file_name' => $filename
//			];
//			//print_r($config); exit;
//			$this->load->library('upload', $config);
//			$this->upload->initialize($config);
//			if ($this->upload->do_upload('verify_doc')) {
//			    $inr = array(
//				'seller_id' => $post["seller_id"],
//				'doc_name' => $filename
//			    );			    
//			    
//			    $this->db->insert('seller_verifications', $inr);
//			}   
		    }
		}
	    }
        
            /*if (isset($files['verify_doc']['name']) && $files['verify_doc']['name']) {
                $ext = '.' . pathinfo($files['verify_doc']['name'], PATHINFO_EXTENSION);
                $filename = date('YmdHis') . rand() . strtolower($ext);
                $config = [
                    'upload_path' => '../upload/seller',
                    'allowed_types' => 'gif|jpg|png|jpeg',
                    'file_name' => $filename
                ];
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload('verify_doc')) {
                    $post['verify_doc'] = $filename;
                }              
            }

            if (isset($files['verify_doc1']['name']) && $files['verify_doc1']['name']) {
                $ext = '.' . pathinfo($files['verify_doc1']['name'], PATHINFO_EXTENSION);
                $filename = date('YmdHis') . rand() . strtolower($ext);
                $config = [
                    'upload_path' => '../upload/seller',
                    'allowed_types' => 'gif|jpg|png|jpeg',
                    'file_name' => $filename
                ];
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload('verify_doc1')) {
                    $post['verify_doc1'] = $filename;
                }
            }

            if (isset($files['verify_doc2']['name']) && $files['verify_doc2']['name']) {
                $ext = '.' . pathinfo($files['verify_doc2']['name'], PATHINFO_EXTENSION);
                $filename = date('YmdHis') . rand() . strtolower($ext);
                $config = [
                    'upload_path' => '../upload/seller',
                    'allowed_types' => 'gif|jpg|png|jpeg',
                    'file_name' => $filename
                ];
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload('verify_doc2')) {
                    $post['verify_doc2'] = $filename;
                }
            }

            if (isset($files['verify_doc3']['name']) && $files['verify_doc3']['name']) {
                $ext = '.' . pathinfo($files['verify_doc3']['name'], PATHINFO_EXTENSION);
                $filename = date('YmdHis') . rand() . strtolower($ext);
                $config = [
                    'upload_path' => '../upload/seller',
                    'allowed_types' => 'gif|jpg|png|jpeg',
                    'file_name' => $filename
                ];
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload('verify_doc3')) {
                    $post['verify_doc3'] = $filename;
                }
            }*/
            
            return 'success';
        }
        else {
            return 'noexists';
        }
    }
}


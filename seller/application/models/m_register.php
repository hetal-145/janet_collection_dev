<?php

require '../vendor_aws/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class M_register extends CI_Model {
    
    public function get_country_code(){
        $list = $this->db->select("*")
		->where("code is not null")	
		->get('country')->result_array();
        if(!empty($list)){
            return $list;
        }
    }
    
    public function save_register($post=[], $files=[]){
        //print_r($post); exit;
        $exists = $this->db->select('email')->where('email', $post["email"])->get('seller')->row_array();
        if(!empty($exists)) {
            echo 'exists';
        }
        else {
	    
	    if(!empty($post["timezone"])) {
		$time = explode('-', $post["timezone"]);
		$timezone_utc = trim($time[0]);
		$timezone = trim($time[1]);
	    }
	    else {
		$timezone = "";
		$timezone_utc = "";
	    }
	    
	    if(!empty($post["latitude"])) {
		$latitude = number_format($post["latitude"], 6);
	    }
	    else {
		$latitude = "";
	    }
	    
	    if(!empty($post["longitude"])) {
		$longitude = number_format($post["longitude"], 6);
	    }
	    else {
		$longitude = "";
	    }
	    
	    $insert_array = array(
                'seller_name' => $post["seller_name"],
                'email' => $post["email"],
                'contact_no' => $post["contact_no"],
                'country_code' => $post["country_code"],
                'gender' => $post["gender"],
                'address' => $post["address"],
                'password' => sha1($post["password"]),
                'company_name' => $post["company_name"],
                'latitude' => $latitude,
                'longitude' => $longitude,
		'account_number' => $post["account_number"],
                'bank_name' => $post["bank_name"],
		'routing_no' => $post["routing_no"],
		'dzone_id' => $post["dzone_id"],
		'dob' => date('Y-m-d', strtotime($post["dob"])),
		'city' => $post["city"],
		'postalcode' => $post["postalcode"],
		'timezone' => $timezone,
		'timezone_utc' => $timezone_utc,
		'delivery_by' => $post["delivery_by"],
            );

            $this->db->insert('seller', $insert_array);
            $last_id = $this->db->insert_id();
            $this->db->set('code', 'SL'.$last_id)->where('seller_id', $last_id)->update('seller');  
	    
	    //add trading hours
	    for($i=0; $i<7; $i++) {
		//get utc start time
		$squery = $this->db->query("select CONVERT_TZ(CONCAT(CURRENT_DATE(),' ','".trim($post["start_time"][$i])."'),'".$timezone_utc."','+0:00') as start_time_utc");
		$stime = $squery->row();
		$start_time_utc = date("H:i:s", strtotime($stime->start_time_utc));

		//get utc end time
		$equery = $this->db->query("select CONVERT_TZ(CONCAT(CURRENT_DATE(),' ','".trim($post["end_time"][$i])."'),'".$timezone_utc."','+0:00') as end_time_utc");
		$etime = $equery->row();
		$end_time_utc = date("H:i:s", strtotime($etime->end_time_utc));
		
		
		$this->db->insert("trading_hours", array(
		    "seller_id" => $last_id,
		    "weekday" => $post["weekday"][$i],
		    "start_time" => $post["start_time"][$i],
		    "end_time" => $post["end_time"][$i],
		    "timezone_utc" => $timezone_utc,
		    "start_time_utc" => $start_time_utc,
		    "end_time_utc" => $end_time_utc
		));
	    }
	    
            if (isset($files['verify_doc']['name'][0])) {
		
		///bucket info
		$credentials = new Aws\Credentials\Credentials(PUBLISHED_KEY, SECRET_KEY);
		$s3 = new S3Client([        
		    'region' => 'eu-west-2',
		    'version' => '2006-03-01',
		    //'debug' => true,
		    "credentials" => $credentials
		]);
		
		foreach ($files['verify_doc']['name'] as $key => $file) {
		    $f = $files['verify_doc']['name'][$key];
		    $ext = '.' . pathinfo($f, PATHINFO_EXTENSION);		
		    $filename = date('YmdHis') . rand() . strtolower($ext);
		    $keyname = $filename;
		    $filepath = $files['verify_doc']['tmp_name'][$key];

		    $result = $s3->putObject(array(
			'Bucket' => BUCKET_NAME,
			'Key' => 'seller/'.$keyname,
			'SourceFile' => $filepath,
			'ACL' => 'public-read',
			'StorageClass' => 'STANDARD'
		    ));
		    
		    $data = [
                        'doc_name' => $file,
			'seller_id' => $last_id
                    ];
                    $this->db->insert('seller_verifications', $data);
	       }
	    }
            
            echo 'success';
        }
    }
}


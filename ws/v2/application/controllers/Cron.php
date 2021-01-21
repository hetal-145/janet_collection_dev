<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Aws\S3\S3Client;
use Aws\CloudFront\CloudFrontClient;
use Aws\Exception\AwsException;
use chriskacerguis\RestServer\RestController;

class Cron extends RestController{

    // private $key = $_ENV['S3_KEY'];
    // private $secret = $_ENV['S3_SECRET'];
    private $bucket = 'wadio-app-bucket';
    // private $endpoint = 'https://cdn.wadio.app';
    private $region = 'us-east-1';
    private $version = '2006-03-01';
    private $endpoint = 'cdn.wadio.app';
    private $read_db, $write_db;

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model(basename(__DIR__)."/media_model");
        $this->read_db = $this->load->database('read', true);
        $this->write_db = $this->load->database('write', true);
    }

    public function update_contry_images_post() {  

      $countries = $this->read_db->select("*", false)
                // ->where("country_id", 1)
                ->order_by("name", "asc")
                ->get('countries')
                ->result_array();

      // $url = 'https://cdn.wadio.app/flags/af_medium.png?Expires=1610092425&Signature=i7kUur02lMZJbC0V0FyE3TJ336g6ZGCZt989FTI1SWPcd-TWGesUjPBpxARR~7V4-UzWkLEzXIic~ifaMus6vwavlcdwpaLKrEuq7DROWk8uxmgZXNVXe9xQ7LkFKvTiGIc60qu-7ibnouI~RMYdbFq-YBpuRfRl93roJQSaD1X9Kd8I4mg2WqLqRuSxLOAZZY~EYLWGkXBdlOWEvT717mkwnQncup-1xjowGZFlZ0BrSe~VW7fmyvxjbI6iC0J-8JYQUaFJXDPjMmc5qDVA~isxRDIdlUL2wDehlmwlTjGGRBCsMT3IzAEMADtsNC732t0o7tC8l-IFfAgn0q--JA__&Key-Pair-Id=K1KBJHL5V973UY';

      // $arr = explode('?', $url);
      // echo $url;
      // print_r($arr);

      if(!empty($countries)) {
        foreach ($countries as $key => $value) {
          $path = 'flags/'.strtolower($value["iso2"]).'_medium.png';
          $signedUrlCannedPolicy = $this->media_model->get_presignedurl($path, 30);
          $arr1 = explode('?', $signedUrlCannedPolicy);

          if(empty($value["image_flag"])) {
            $arr = array(
              'image_flag' => $path,
              'image_flag_presigned' => '?'.$arr1[1]
            );
          }
          else {
            $arr = array(
              'image_flag_presigned' => '?'.$arr1[1]
            );
          }

          $this->write_db->set($arr)
                ->where("country_id", $value["country_id"])
                ->update("countries");
        }
      }

      $this->response(['status' => true, 'response_msg' => 'success'], 200);
    }
}

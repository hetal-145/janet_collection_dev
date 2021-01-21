<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Aws\S3\S3Client;
use Aws\CloudFront\CloudFrontClient;
use Aws\Exception\AwsException;

use chriskacerguis\RestServer\RestController;

class Media extends RestController{

    // private $key = $_ENV['S3_KEY'];
    // private $secret = $_ENV['S3_SECRET'];
    private $bucket = 'wadio-app-bucket';
    // private $endpoint = 'https://cdn.wadio.app';
    private $region = 'us-east-1';
    private $version = '2006-03-01';
    private $endpoint = 'cdn.wadio.app';


    function __construct()
    {
        // Construct the parent class
        parent::__construct();
    }

    public function index_get() {
      $cloudFront = new Aws\CloudFront\CloudFrontClient([
          'region'  => 'us-east-1',
          'version' => '2014-11-06'
      ]);

      // Setup parameter values for the resource
      $resourceKey = $_ENV['CLOUDFRONT_URL'] . $_GET['path']; // [GET] https://api.wadio.app/v1/media?path=lwqy-1604292854-station-1.jpg
      $expires = time() + 5 * 60;

      // Create a signed URL for the resource using the canned policy
      $signedUrlCannedPolicy = $cloudFront->getSignedUrl([
          'url'         => $resourceKey,
          'expires'     => $expires,
          'private_key' => $_ENV['CLOUDFRONT_PRIVATE_KEY_PATH'],
          'key_pair_id' => $_ENV['CLOUDFRONT_KEY_ID']
      ]);

      $this->response(['status' => true, 'url' => $signedUrlCannedPolicy], 200);

      $arr = ['status' => true, 'url' => $signedUrlCannedPolicy];
      return $arr;
    }

    public function index_post() {
      $credentials = new Aws\Credentials\Credentials($_ENV['S3_KEY'], $_ENV['S3_SECRET']);
      $s3Client = new Aws\S3\S3Client([
          // 'profile' => 'default',
          'region' => $this->region,
          'version' => $this->version,
          'credentials' => $credentials
      ]);

      // String of all alphanumeric character
      $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

      // Random string of length 4
      $hash = strtolower(substr(str_shuffle($str_result), 0, 4));
      $epochtime = time();
      $type = $_POST['source_type']; // station, user, artist, etc.
      $id = $_POST['source_id']; // source_id depending on type
      $ext = pathinfo($_POST['filename'], PATHINFO_EXTENSION);

      $filename = $hash.'-'.$epochtime.'-'.$type.'-'.$id.'.'.$ext;

      $cmd = $s3Client->getCommand('PutObject', [
        'Bucket' => $this->bucket,
        'Key' => $filename, // Filename
        // 'ContentType' =>'image/jpeg', // Content-Type of file which will be uploaded
        // 'ContentDisposition' => 'inline; filename='.$filename,
        // 'ResponseContentDisposition' => 'attachment;
      ]);

      // $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
      $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');

      $presignedUrl = (string)$request->getUri();

      // print_r($_FILES); print_r($_POST);
      // $this->upload_image($presignedUrl, $_FILES["file"]["name"]);

      $this->response(['status' => true, 'filename' => $filename, 'ext' => $ext, 'url' => $presignedUrl], 200);
    }

    //public function upload_image($presignedUrl, $filesname) {
    function upload_image_post() {
      // print_r($_FILES); print_r($_POST); exit;

      $presignedUrl = $_POST["url"];
      $filesname = $_FILES["file"]["name"];
      $file_path = $_FILES["file"]["tmp_name"];
      $payload = file_get_contents( $file_path );

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => $presignedUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => array(
          'Content-Type: '.$_FILES["file"]["type"],
          // "Accept: */*",
          // "Cache-Control: no-cache",
          // "Connection: keep-alive",
          // "Content-Length: ".$_FILES['file']['size'],
          // "Content-Type: multipart/form-data"
        ),
      ));

      $response = curl_exec($curl);

      curl_close($curl);
      // echo $response;
      $arr = ['status' => true, 'response' => $response];
      return $arr;
    }
}

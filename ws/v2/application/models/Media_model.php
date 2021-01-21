<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Media_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function index_get($path) {
      $cloudFront = new Aws\CloudFront\CloudFrontClient([
          'region'  => 'us-east-1',
          'version' => '2014-11-06'
      ]);

      // Setup parameter values for the resource
      $resourceKey = $_ENV['CLOUDFRONT_URL'] . $path; // [GET] https://api.wadio.app/v1/media?path=lwqy-1604292854-station-1.jpg
      // $expires = time() + 5 * 60;
      $expires = time() + 30 * 84600;

      // Create a signed URL for the resource using the canned policy
      $signedUrlCannedPolicy = $cloudFront->getSignedUrl([
          'url'         => $resourceKey,
          'expires'     => $expires,
          'private_key' => $_ENV['CLOUDFRONT_PRIVATE_KEY_PATH'],
          'key_pair_id' => $_ENV['CLOUDFRONT_KEY_ID']
      ]);

      return $signedUrlCannedPolicy;
    }

    function index_post() {
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

      if(empty($_POST['filename']) && !empty($_POST['extension'])) {
        $ext = pathinfo($_POST['filename'], PATHINFO_EXTENSION);
        $filename = $hash.'-'.$epochtime.'-'.$type.'-'.$id.'.'.$ext;
      }
      else if(!empty($_POST['filename']) && !empty($_POST['extension'])) {
        $ext = pathinfo($_POST['filename'], PATHINFO_EXTENSION);
        $_POST['filename'] = str_replace($ext, $_POST['extension'], $_POST['filename']);
        $filename = $_POST['filename'];
      }

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

      return $return = array('filename' => $filename, 'url' => $presignedUrl);
    }

    function get_presignedurl($path, $days) {
      $cloudFront = new Aws\CloudFront\CloudFrontClient([
          'region'  => 'us-east-1',
          'version' => '2014-11-06'
      ]);

      // Setup parameter values for the resource
      $resourceKey = $_ENV['CLOUDFRONT_URL'] . $path; // [GET] https://api.wadio.app/v1/media?path=lwqy-1604292854-station-1.jpg
      $expires = time() + $days * 84600;

      // Create a signed URL for the resource using the canned policy
      $signedUrlCannedPolicy = $cloudFront->getSignedUrl([
          'url'         => $resourceKey,
          'expires'     => $expires,
          'private_key' => $_ENV['CLOUDFRONT_PRIVATE_KEY_PATH'],
          'key_pair_id' => $_ENV['CLOUDFRONT_KEY_ID']
      ]);

      return $signedUrlCannedPolicy;
    }
}

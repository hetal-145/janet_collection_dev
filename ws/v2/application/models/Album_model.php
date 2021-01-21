<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class Album_model extends CI_Model {

    private $read_db, $write_db;

    function __construct() {
        parent::__construct();

        $this->read_db = $this->load->database('read', true);
        $this->write_db = $this->load->database('write', true);
    }

     public function get_tracks_by_album($post = []) {
        $album_id = $post["album_id"];
        $user_id = $post["user_id"];
        //get album and its tracks
        $albumdata = $this->read_db->select("*")->where("album_id", $album_id)->get("album_tracks")->result_array();
        if (!empty($albumdata)) {
            $track_album = array();
            foreach ($albumdata as $alb_data) {
                $album_track_data = $this->album_track_details($alb_data["track_id"], $user_id);
                if (!empty($album_track_data)) {
                    array_push($track_album, $album_track_data);
                }
            }
            return $track_album;
        } else {
            return array();
        }
    }

    public function album_track_details($track_id, $user_id) {
        $response1 = $this->db->select("track_id, name, duration_ms, label, composers, artwork_url, release_date, created_at")->where("track_id", $track_id)->get("tracks")->row_array();
        if (!empty($response1)) {
            $response = array_map(function($val) {
                if (is_null($val)) {
                    $val = "";
                }
                return $val;
            }, $response1);

            $track_name = $response["track_name"];

            //is_favourite
            $favourite = $this->db->select("user_favorite_track_id")
                            ->where('user_id', $user_id)
                            ->where('track_id', $response['track_id'])
                            ->get('user_favorite_tracks')->row_array();
            if (!empty($favourite)) {
                $response["is_favourite"] = "1";
            } else {
                $response["is_favourite"] = "0";
            }

            //user commented on track or not
            $comment = $this->db->select("chat_id, is_shared")
                            ->where("group_id", $track_id)
                            ->where("(user_id = '" . $user_id . "' OR to_user_id = '" . $user_id . "')")
                            ->where("chat_type", 2)
                            ->where("deleted_at is null")
                            ->group_by("message_id")
                            ->get("chat")->result_array();

            if (!empty($comment)) {
                $response["is_commented"] = "1";
            } else {
                $response["is_commented"] = "0";
            }

            //user shared
            $is_shared = $this->db->select("chat_id, is_shared")
                            ->where("group_id", $response1["id"])
                            ->where("(user_id = '" . $user_id . "' OR to_user_id = '" . $user_id . "')")
                            ->where("chat_type", 2)
                            ->where("is_shared", 1)
                            ->where("deleted_at is null")
                            ->group_by("message_id")
                            ->order_by("chat_id", "desc")
                            ->get("chat")->row_array();


            if (!empty($is_shared)) {
                $response["is_shared"] = "1";
            } else {
                $response["is_shared"] = "0";
            }

            $response["track_like_count"] = "0";//(string) $this->track_favourite_count($track_name);
            $response["track_share_count"] = "0";//(string) $this->track_share_count($track_id);
            $response["comment_count"] = "0";//(string) $this->song_comment_count($track_id);

            //tagged user
            $response["is_tagged"] = "0";// $this->check_is_tagged($track_id, $user_id, 2);
            $response["is_seen_tagged"] = "0";//$this->check_is_tagged_seen($track_id, $user_id, 2);
            return $response;
        }
    }



}
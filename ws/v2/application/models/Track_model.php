<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Track_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function make_fav_unfav_track($post = []) {

        $exists = $this->db->select("user_favorite_track_id")
                        ->where('track_id',$post['track_id'])
                        ->where('user_id',$post['user_id'])
                        ->get('user_favorite_tracks')->row_array();

        if ($post["is_fav"] == 1) {
            if (!empty($exists)) {
                return 1;
            } else {
                $subscribe_array = array(
                    'track_id' => $post['track_id'],
                    'user_id' => $post['user_id'],
                );
                $this->db->insert('user_favorite_tracks', $subscribe_array);
                return 2;
            }
        } else if ($post["is_fav"] == 0) {
            if (!empty($exists)) {
                $subscribe_array = array(
                    'track_id' => $post['track_id'],
                    'user_id' => $post['user_id'],
                );
                $this->db->where($subscribe_array)->delete('user_favorite_tracks');
                return 3;
            } else {
                return 4;
            }
        }
    }

    public function user_favourite_track_list($post = []) {
        $resp = array();

        if (!isset($post['offset']) || !$post['offset']) {
            $post['offset'] = '0';
        }

        $counter = $post["offset"] + LIMIT;

        $fetch = $this->db->select("track_id")
                        ->where('user_id', $post["user_id"])
                        ->limit(LIMIT)
                        ->order_by("created_at", "desc")
                        ->get('user_favorite_tracks')->result_array();

		//print_r($fetch); exit;

        if (!empty($fetch)) {
            foreach ($fetch as $key => $value) {
                $response1 = $this->track_detail($value['track_id'], $post["user_id"]);

                if (!empty($response1)) {
                    $fetch[$key] = array_merge($fetch[$key], $response1);
                    array_push($resp, $fetch[$key]);
                }
            }
            return $resp;
        }
    }

    function track_favourite_count($track_id) {
        $count = $this->db->select("user_favorite_track_id")
                        ->where("track_id", $track_id)
                        ->get("user_favorite_tracks")->num_rows();
        return $count;
    }

    function track_detail($track_id, $user_id) {
      

        $track_details2 = $this->db->select("*", false)
                        ->where('track_id', $track_id)
                        ->get('tracks')->row_array();

        if (!empty($track_details2)) {

            if (!empty($track_details2["previews"])) {
                $track_details2["previews"] = json_decode($track_details2["previews"]);
            }

            $track_details = array_map(function($val) {
                if (is_null($val)) {
                    $val = "";
                }
                return $val;
            }, $track_details2);

            if (!empty($track_details["artist_id"]) && $track_details["artist_id"] != 0) {
                $at_id = $this->db->select("artist_id, user_id, image_profile_presigned, biography")
                                ->where("artist_name = '" . $track_details["artist_name"] . "'")
                                ->get("artists")->row_array();

                if (!empty($at_id)) {
                    $track_details["at_id"] = $at_id["artist_id"];
                    $track_details["artist_image"] = $at_id["image_profile_presigned"];

                    if (!empty($at_id["biography"])) {
                        $track_details["biography"] = $at_id["biography"];
                    } else {
                        $track_details["biography"] = "";
                    }

                    if (!empty($at_id["user_id"])) {
                        $auser = $this->user_model->get_user_by_id($at_id["user_id"]);
                        if (!empty($auser)) {
                            $track_details["is_admin_verified"] = (string) $auser["is_admin_verified"];
                        } else {
                            $track_details["is_admin_verified"] = "0";
                        }
                    } else {
                        $track_details["is_admin_verified"] = "0";
                    }
                } else {
                    $track_details["at_id"] = "";
                    $track_details["artist_image"] = "";
                    $track_details["biography"] = "";
                    $track_details["is_admin_verified"] = "0";
                }
            }
	    else {
                $track_details["at_id"] = "";
                $track_details["artist_image"] = "";
                $track_details["artist_name"] = "";
                $track_details["biography"] = "";
                $track_details["is_admin_verified"] = "0";
            }

            if (!empty($track_details["country"])) {
                //get country name
                $country = $this->db->select('*')
                                ->where('code2l', $track_details["country"])
                                ->get('country')->row_array();

                $this->db->set("country_id", $country["id"])->where("id", $track_details["id"])->update("track_details");

                if (!empty($country)) {
                    $country["flag"] = $country["flag_medium"];
                }

                $track_details["nationality"] = $country;
            }
	    else if (!empty($track_details["country_id"])) {
                //get country name
                $country = $this->db->select('*')
                                ->where('id', $track_details["country_id"])
                                ->get('country')->row_array();

                $this->db->set("country", $country["code2l"])->where("id", $track_details["id"])->update("track_details");

                if (!empty($country)) {
                    $country["flag"] = $country["flag_medium"];
                }

                $track_details["nationality"] = $country;
            }
	    else {
                $track_details["nationality"] = new stdClass();
            }

//	    //get album and its tracks
//	    $albumdata1 = $this->db->select("*")->where("track_id", $track_details["id"])->get("track_albums")->row_array();
//	    if(!empty($albumdata1)) {
//		$albumdata2 = $this->db->select("*")->where("album_id", $albumdata1["album_id"])->get("track_albums")->result_array();
//		if(!empty($albumdata2)) {
//		    $track_album = array();
//		    foreach($albumdata2 as $alb_data) {
//			$album_track_data = $this->album_track_details($alb_data["track_id"], $user_id);
//			//$album_track_data = $this->db->select("*")->where("id", $alb_data["track_id"])->get("track_details")->row_array();
//			if(!empty($album_track_data)) {
//			    array_push($track_album, $album_track_data);
//			}
//		    }
//		    $track_details["track_album"] = $track_album;
//		}
//		else {
//		    $track_details["track_album"] = array();
//		}
//	    }
//	    else {
//		$track_details["track_album"] = array();
//	    }
            //most_listen_count
           /* $most_listen_count = $this->db->select("count(track_name) as most_listen_count")
                            ->where('user_id', $user_id)
                            ->where('track_name', $value["track_name"])
                            ->group_by('track_name')
                            ->order_by('most_listen_count', 'desc')
                            ->get('songs_played')->row_array();*/

            //print_r($most_listen_count);

            if (!empty($most_listen_count)) {
                $track_details["most_listen_count"] = $most_listen_count["most_listen_count"];
            } else {
                $track_details["most_listen_count"] = "0";
            }

            //is_favourite
            $favourite = $this->db->select("*")
                            ->where('user_id', $user_id)
                            ->where('track_id', $track_details["track_id"])
                            ->get('user_favorite_tracks')->row_array();
            if (!empty($favourite)) {
                $track_details["is_favourite"] = "1";
            } else {
                $track_details["is_favourite"] = "0";
            }

            $track_details["track_like_count"] = (string) $this->track_favourite_count($track_details["track_id"]);

           // $track_details["artwork_url"] = stripslashes($track_details["artwork_url"]);

            //user commented on track or not
           /* $comment = $this->db->select("chat_id")
                            ->where("group_id", $track_details["id"])
                            ->where("user_id", $user_id)
                            ->where("chat_type", 2)
			    ->where("is_shared", 0)
			    ->where("type", 'sent')
                            ->where("deleted_at is null")
                            ->group_by("message_id")
                            ->order_by('sent_at', 'desc')
                            ->get("chat")->result_array();*/

            if (!empty($comment)) {
                $track_details["is_commented"] = "1";
            } else {
                $track_details["is_commented"] = "0";
            }

           // $track_details["comment_count"] = (string) $this->song_comment_count($track_details["id"]);

            //user shared
           /* $is_shared = $this->db->select("chat_id, is_shared")
                            ->where("group_id", $track_details["id"])
                            ->where("(user_id = '" . $user_id . "' OR to_user_id = '" . $user_id . "')")
                            ->where("chat_type", 2)
                            ->where("is_shared", 1)
                            ->where("deleted_at is null")
                            ->group_by("message_id")
                            ->order_by("chat_id", "desc")
                            ->get("chat")->row_array();*/

			//print_r($is_shared);

            if (!empty($is_shared)) {
                $track_details["is_shared"] = "1";
            } else {
                $track_details["is_shared"] = "0";
            }

            //tagged user
            //$track_details["is_tagged"] = $this->check_is_tagged($track_details["id"], $user_id, 2);
            //$track_details["is_seen_tagged"] = $this->check_is_tagged_seen($track_details["id"], $user_id, 2);
            //$track_details["track_share_count"] = (string) $this->track_share_count($track_details["id"]);

            return $track_details;
        }
    }

    public function get_track_details_by_id($post = []) {

        $response = $this->db->select('track_id')
                        ->where('track_id', $post['track_id'])
                        ->get("tracks")->row_array();

        return  $this->track_detail($response['track_id'], $post["user_id"]);
    }

    function get_track_short_detail_by_id($track_id, $user_id) {
        $post["track_id"] = $track_id;
        $post["user_id"] = $user_id;
        $response = $this->get_track_details_by_id($post);
        if (!empty($response)) {
            return $response;
        }
    }

    function track_share_count($track_id) {
        $count = $this->db->select("user_share_track_id")
                        ->where("track_id", $track_id)
                        //->group_by("user_id")
                        ->get("user_share_tracks")->num_rows();
        return $count;
    }


    public function share_track($post = []) {
        $user_ids = explode(",", $post["ids"]);
        unset($post["ids"]);
        $userdata = $this->user_model->get_user_by_id($post["user_id"]);
        $trackdata = $this->get_track_short_detail_by_id($post["track_id"], $post["user_id"]);
        $msg = '@' . $userdata["username"] . ' - ' . $userdata["name"] . ', has shared ' . $trackdata["name"] . '';
		//print_r($user_ids); exit;

        foreach ($user_ids as $ids) {
            $check = $this->db->select("*")
                            ->where("user_message_id", $ids)
                            ->where("track_id", $post["track_id"])
                            ->get("user_share_tracks")->row_array();
                        

	    /*if (!empty($post["comment"])) {
                $description = $post["comment"];
            } else {
                $description = $msg;
            }*/

            if (empty($check)) {
                $insert = $this->db->insert("user_share_tracks", array(
                    "user_message_id" => $ids,
                    "track_id" => $post["track_id"],
                ));
            }
/*
                //insert in chat table
                $dat = substr(date('YmdHisu'), 0, -3);
                $ins_chat = array(
                    'message_id' => $post["user_id"] . '-' . $dat,
                    'offline_id' => '',
                    'message' => $description,
                    'document' => '',
                    'document_thumb' => '',
                    'location' => '',
                    'msg_type' => 'text',
                    'sent_at' => date('Y-m-d H:i:s'),
                    'delivered_at' => null,
                    'seen_at' => null,
                    'deleted_at' => null,
                    'edited_at' => null,
                    'chat_type' => 2,
                    'user_type' => 2,
                    'tagged_users' => null,
                    'song_name' => null,
                    'is_shared' => 1,
                    'is_shared_type' => 2,
                    'group_id' => $post["track_id"],
                    "comment" => $description
                );

                $send_chat = array(
                    'user_id' => $post["user_id"],
                    'to_user_id' => $ids,
                    'type' => 'sent',
                );

                $received_chat = array(
                    'user_id' => $ids,
                    'to_user_id' => $post["user_id"],
                    'type' => 'received',
                );

                if (isset($post["is_prv_share"]) && $post["is_prv_share"] == '1') {
                    $ins_chat = array_merge($ins_chat, array('is_private_share' => 1));
                }

                $this->db->insert("chat", array_merge($ins_chat, $send_chat));
                $this->db->insert("chat", array_merge($ins_chat, $received_chat));

                if ($insert) {
                    //insert in notification
                    $insert_notify = array(
                        'to_user_id' => (string) $ids,
                        'contact_person_id' => (string) $post["track_id"],
                        'track_id' => (string) $post["track_id"],
                        'notification_types' => "25",
                        'message' => $msg,
                        "description" => $description,
                    );

                    $this->m_api->create_notification($insert_notify);
                    $this->m_notify->send($insert_notify);
                }
            }
	    else {
                unset($post["is_prv_share"]);
                $this->db->set("comment", $description)->where("track_id", $post["track_id"])->where("user_id", $ids)->update("user_share_tracks");
                //insert in notification
                $insert_notify1 = array(
                    'to_user_id' => (string) $ids,
                    'contact_person_id' => (string) $post["track_id"],
                    'track_id' => (string) $post["track_id"],
                    'notification_types' => "25",
                    'message' => $msg,
                    "description" => $description,
                );

                $this->m_api->create_notification($insert_notify1);
                $this->m_notify->send($insert_notify1);
            }*/
        }

        $track_count = $this->track_share_count($post["track_id"]);

        return $track_count;
    }

}
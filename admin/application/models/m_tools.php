<?php

class M_tools extends CI_Model {
    
    function get_user_by_id($id) {
	$result = $this->db->select("*")->where("user_id", $id)->get("user")->row_array();
	unset($result["password"]);
	return $result;
    }
    
    function get_seller_by_id($id) {
	$result = $this->db->select("*")->where("seller_id", $id)->get("seller")->row_array();
	unset($result["password"]);
	return $result;
    }

    public function thumbCreate($img_uploadpath, $thumb_uploadpath, $source, $thumbSize = '') {
        $fullPath = $img_uploadpath . $source;

        if (!$thumbSize) {
            $thumbSize = 200;
        }
        $thumbPath = $thumb_uploadpath;
        $thumbQuality = 99;

        $extension = pathinfo($img_uploadpath . $source, PATHINFO_EXTENSION);

        if ($extension == 'jpg' || $extension == 'jpeg')
            $full = imagecreatefromjpeg($fullPath);
        if ($extension == 'gif')
            $full = imagecreatefromgif($fullPath);
        if ($extension == 'png')
            $full = imagecreatefrompng($fullPath);


//$full = imagecreatefromjpeg($fullPath);
        $name = $source;

        $width = imagesx($full);
        $height = imagesy($full);

        /* work out the smaller version, setting the shortest
          side to the size of the thumb, constraining height/wight
         */

        if ($height > $width) {
            $divisor = $width / $thumbSize;
        } else {
            $divisor = $height / $thumbSize;
        }

        $resizedWidth = ceil($width / $divisor);
        $resizedHeight = ceil($height / $divisor);

        /* work out center point */
        $thumbx = floor(($resizedWidth - $thumbSize) / 2);
        $thumby = floor(($resizedHeight - $thumbSize) / 2);

        /* create the small smaller version, then crop it centrally
          to create the thumbnail */
        $resized = imagecreatetruecolor($resizedWidth, $resizedHeight);
        $thumb = imagecreatetruecolor($thumbSize, $thumbSize);
        
       // print_r($full); print_r($thumb); print_r($resized); exit;
        imagecopyresized($resized, $full, 0, 0, 0, 0, $resizedWidth, $resizedHeight, $width, $height);
        imagecopyresized($thumb, $resized, 0, 0, $thumbx, $thumby, $thumbSize, $thumbSize, $thumbSize, $thumbSize);

        if ($extension == 'jpg' || $extension == 'jpeg')
            $status = imagejpeg($thumb, $thumbPath . $name, $thumbQuality);
        if ($extension == 'gif')
            $status = imagegif($thumb, $thumbPath . $name, $thumbQuality);
        if ($extension == 'png')
            $status = imagepng($thumb, $thumbPath . $name, 9);
    }

  

    public function send_user_register_mail($to, $subject, $msg) {
        $ci = get_instance();
        $config = array();
        
        $config_data = $this->db->where_in('key', array('smtp_user', 'smtp_pass', 'smtp_host', 'smtp_port'))->get('setting')->result_array();

        foreach ($config_data as $key => $row) {
            $tmp_arr[$row['key']] = $row['value'];
        }
        
        $config['smtp_user'] = $tmp_arr['smtp_user'];
        $config['smtp_pass'] = $tmp_arr['smtp_pass'];
        $config['smtp_host'] = $tmp_arr['smtp_host'];
        $config['smtp_port'] = $tmp_arr['smtp_port'];
        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['newline'] = "\r\n";
        $ci->email->initialize($config);
        $ci->email->from($config['smtp_user'], 'Janet-Collection');
        $ci->email->to($to);
        $this->email->reply_to($config['smtp_user'], 'Janet-Collection');
        $ci->email->subject($subject);
        $ci->email->message($msg);
        $ci->email->send();
// echo $this->email->print_debugger();
    }

    public function get_parent_category($post = []) {
        $where = "parent_category_id is   NULL";
        $category = $this->db
                ->where($where)
                ->get('category')
                ->result_array();
        return $category;
    }
    
    public function get_category_list(){
        $category_list = $this->db->query("SELECT c1.category_id, c1.category_name AS category FROM category_mst c1 WHERE c1.parent_id = 0 and c1.status=1 UNION select c2.category_id, CONCAT ( c1.category_name,' > ',c2.category_name ) AS category FROM category_mst as c1 JOIN category_mst as c2 ON c2.parent_id=c1.category_id AND c2.status=1 where c1.status=1 and c1.parent_id = 0 
UNION SELECT c3.category_id, CONCAT(c1.category_name,' > ',c2.category_name,' >',c3.category_name) AS category FROM category_mst c1 JOIN category_mst c2 ON c2.parent_id = c1.category_id and c2.status=1 JOIN category_mst c3 ON c3.parent_id = c2.category_id and c3.status=1 WHERE c1.parent_id = 0 and c1.status=1 UNION
SELECT c4.category_id, CONCAT(c1.category_name,' > ',c2.category_name,' >',c3.category_name,' >',c4.category_name) AS category FROM category_mst c1 JOIN category_mst c2 ON c2.parent_id = c1.category_id and c2.status=1 JOIN category_mst c3 ON c3.parent_id = c2.category_id and c3.status=1 JOIN category_mst c4 ON c4.parent_id = c3.category_id and c4.status=1 WHERE c1.parent_id = 0 and c1.status=1 UNION SELECT c5.category_id, CONCAT(c1.category_name,' > ',c2.category_name,' >',c3.category_name,' >',c4.category_name,' >',c5.category_name) AS category FROM category_mst c1 JOIN category_mst c2 ON c2.parent_id = c1.category_id and c2.status=1 JOIN category_mst c3 ON c3.parent_id = c2.category_id and c3.status=1 JOIN category_mst c4 ON c4.parent_id = c3.category_id and c4.status=1 JOIN category_mst c5 ON c5.parent_id = c4.category_id and c5.status=1 WHERE c1.parent_id = 0 and c1.status=1")
                ->result_array();
        
        return $category_list;        
    }
    
    public function get_category(){
        $category_list = $this->db->select("*")
                ->where('parent_id', 0)
                ->where('status', 1)
                ->get('category_mst')
                ->result_array();
        
        return $category_list;        
    }
    
    public function get_sub_category($category_id){
        $sub_category_list = $this->db->select("*")
                ->where('parent_id', $category_id)
                ->where('status', 1)
                ->get('category_mst')
                ->result_array();
        
        return $sub_category_list;    
    }
    
    public function get_country(){
        $country = $this->db->select("*")
                ->get('country')
                ->result_array();        
        return $country;    
    }
    
    
    public function get_brand_list(){
        $brand_list = $this->db->select('*')
                ->where('status', '1')
                ->order_by('brand_name', 'asc')
                ->get('brand_mst')
                ->result_array();
        
        if(!empty($brand_list)){
            return $brand_list;
        }
    }
    
    public function get_volume_list(){
        $get_volume_list = $this->db->select("volume_mst.brand_id, volume_mst.volume_id, concat(volume_mst.volumne_value, volume_type.volume_type) as volumes", false)
                                ->join('volume_type', 'volume_type.volume_type_id = volume_mst.type')
                                ->where('volume_mst.status', 1)
                                ->get('volume_mst')
                                ->result_array();
           
        if(!empty($get_volume_list)){
            return $get_volume_list;
        }
    }
    
    public function get_volume_type_list(){
        $get_volume_list = $this->db->select("volume_type.volume_type_id, volume_type.volume_type", false)
                                ->where('volume_type.status', 1)
                                ->get('volume_type')
                                ->result_array();
           
        if(!empty($get_volume_list)){
            return $get_volume_list;
        }
    }
    
    public function generate_random_code() {
        $rand = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        
        $input_length = strlen($rand);
        $random_string = '';
        for($i = 0; $i < 5; $i++) {
            $random_character = $rand[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }  

        return $random_string;
    }  
    
    public function pic_url($pic, $thumb = '', $folder = '') {
        if ($thumb) {
            if ($pic) {
                return str_replace('/admin', '', site_url($folder .'/thumbs/' . $pic));
            }
        } else {
            if ($pic) {
                return str_replace('/admin', '', site_url($folder .'' . $pic));
            }
        }
        return '';
    }

}

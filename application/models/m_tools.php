<?php

class M_tools extends CI_Model {
    
    function template($template_name, $vars = array(), $return = FALSE) {
	$user_id = $this->session->userdata("user_id");
	
	if(!$user_id) {
	    $this->session->sess_destroy();
	    redirect(base_url('login'), 'refresh');
	}
	
	$post["offset"] = 0;
	$post["user_id"] = $user_id;
	
	//settings
	$settings = $this->get_settings();
	$vars["facebook"] = $settings[23]["value"];
	$vars["instagram"] = $settings[24]["value"];
	$vars["twitter"] = $settings[25]["value"];
	$vars["linkedin"] = $settings[26]["value"];
	$vars["youtube"] = $settings[27]["value"];
	
        if($return):
	    $content  = $this->load->view('header', $vars, $return);
	    $content .= $this->load->view($template_name, $vars, $return);
	    $content .= $this->load->view('footer', $vars, $return);

	    return $content;
	else:
	    $this->load->view('header', $vars);
	    $this->load->view($template_name, $vars);
	    $this->load->view('footer', $vars);
	endif;
    }
    
    function generaltemplate($template_name, $vars = array(), $return = FALSE) {
	//settings
	$settings = $this->get_settings();
	$vars["facebook"] = $settings[23]["value"];
	$vars["instagram"] = $settings[24]["value"];
	$vars["twitter"] = $settings[25]["value"];
	$vars["linkedin"] = $settings[26]["value"];
	$vars["youtube"] = $settings[27]["value"];
	
        $this->load->view('header', $vars);
	$this->load->view($template_name, $vars);
	$this->load->view('footer', $vars);
    }
    
    function get_settings() {
	$data = $this->db->select("*")
                ->get('setting')->result_array();
	return $data;
    }
    
    function get_data_from_setting($key) {
	$data = $this->db->select("*")
		->where("key", $key)
                ->get('setting')->row_array();
	return $data;
    }
    
    function use_api2($method, $parameter = array(), $data_file = array()) {
	if (!empty($this->session->userdata('user_id'))) {
	    $user_id = $this->session->userdata("user_id");
	    $user = $this->get_user($user_id);
//	    print_r($user);
	    if(!empty($user["token"])) {
		$token = "authorization: ".$user["token"];
	    }
	    else {
		$token = "authorization: ".md5(rand() . rand());
	    }
	}
	else {
	    $user_id = "";
	    $token = "";
	}
	
	if(empty($user_id) && empty($parameter)) {
	    $parameters = array();
	}
	else if(empty($user_id) && !empty($parameter)) {
	    $parameters = $parameter;
	}
	else {
	    if(!empty($parameter)) {
		$parameters = array_merge($parameter, array("user_id" => $user_id));	
	    }
	    else {
		$parameters = array("user_id" => $user_id);	
	    }
	}
		
	$curl = curl_init();
	$url = "https://www.Janet-Collection.com/ws/v2/api/".$method;
//        $url = "http://localhost/predrinkdelivery/ws/v2/api/".$method;
	
	if(!empty($data_file)) {
	    $parameters = array_merge($parameters, $data_file);
	}
	
	curl_setopt_array($curl, array(
	    CURLOPT_URL => $url,
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => "",
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 30,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => "POST",
	    CURLOPT_POST => true,
	    CURLOPT_POSTFIELDS => $parameters,
	    CURLOPT_HTTPHEADER => array(
		$token,
		"cache-control: no-cache",
		"content-type: multipart/form-data",
	    ),
	));
	
	$resp = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);

	if ($err) {
	    return $err;
	} else {
	    $response = $this->get_data($resp);
	    return $response;
	}
    } 
    
    function use_api($method, $parameter = array()) {
	if (!empty($this->session->userdata('user_id'))) {
	    $user_id = $this->session->userdata("user_id");
	    $user = $this->get_user($user_id);
	    
	    if(!empty($user["token"])) {
		$token = "authorization: ".$user["token"];
	    }
	    else {
		$token = "authorization: ".md5(rand() . rand());
	    }
	}
	else {
	    $user_id = "";
	    $token = "";
	}
	
	if(empty($user_id) && empty($parameter)) {
	    $parameters = array();
	}
	else if(empty($user_id) && !empty($parameter)) {
	    $parameters = $parameter;
	}
	else {
	    if(!empty($parameter)) {
		$parameters = array_merge($parameter, array("user_id" => $user_id));	
	    }
	    else {
		$parameters = array("user_id" => $user_id);	
	    }
	}
	
	$curl = curl_init();
	$url = "https://www.Janet-Collection.com/ws/v2/api/".$method;
//        $url = "http://localhost/predrinkdelivery/ws/v2/api/".$method;
	
	curl_setopt_array($curl, array(
	    CURLOPT_URL => $url,
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => "",
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 30,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => "POST",
	    CURLOPT_POSTFIELDS => $parameters,
	    CURLOPT_HTTPHEADER => array(
		$token,
		"cache-control: no-cache",
	    ),
	));

	$resp = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);

       	if ($err) {
	    return $err;
	} else {
	    $response = $this->get_data($resp);
	    return $response;
	}
    } 
    
    function use_api3($method, $parameter = array()) {
	$user_id = 1;
	$user = $this->get_user($user_id);

	if(!empty($user["token"])) {
	    $token = "authorization: ".$user["token"];
	}
	else {
	    $token = "authorization: ".md5(rand() . rand());
	}
	
	if(empty($user_id) && empty($parameter)) {
	    $parameters = array();
	}
	else if(empty($user_id) && !empty($parameter)) {
	    $parameters = $parameter;
	}
	else {
	    if(!empty($parameter)) {
		$parameters = array_merge($parameter, array("user_id" => $user_id));	
	    }
	    else {
		$parameters = array("user_id" => $user_id);	
	    }
	}
	
	$curl = curl_init();
	$url = "https://www.Janet-Collection.com/ws/v2/api/".$method;
//        $url = "http://localhost/predrinkdelivery/ws/v2/api/".$method;
	
	curl_setopt_array($curl, array(
	    CURLOPT_URL => $url,
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => "",
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 30,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => "POST",
	    CURLOPT_POSTFIELDS => $parameters,
	    CURLOPT_HTTPHEADER => array(
		$token,
		"cache-control: no-cache",
	    ),
	));

	$resp = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);
        
	if ($err) {
	    return $err;
	} else {
	    $response = $this->get_data($resp);
	    return $response;
	}
    } 
    
    function get_data($response) {
	$response1 = json_decode($response, true);
	
	if($response1["status"] == false && $response1["screen_code"] == "1001") {
	    $this->session->sess_destroy();
	    redirect(base_url('login'), 'refresh');
	}
	else {
	    return $response1;
	}
    }
    
    function get_user($user_id) {
	$user = $this->db->select("user_id, firstname, lastname, concat(firstname,' ',lastname) as name, email, token, profile_image, wallet, status, latitude, longitude, shipping_id", false)
		->where("user_id", $user_id)
		->get("user")->row_array();
	
	if(!empty($user["profile_image"])) {
	    $user["profile_image"] = $this->s3_url($user["profile_image"]);
	}
	return $user;
    }
    
    function get_mile_limit(){
        //get distance
        $mile_limit = $this->db->select("*")->where("key", "mile_limit")->get("setting")->row_array();
        return $mile_limit["value"];
    }
    
    function get_http_response_code($domain1) {
	$headers = get_headers($domain1);
	return substr($headers[0], 9, 3);
    }
    
    function add_web_notification($post=[]){    
        $sell = array();
        $products = $this->db->select("product_id")->where('order_id', $post["order_id"])->get('order_product')->result_array();
        foreach ($products as $p){
            $seller = $this->db->select("seller_id")->where('product_id', $p["product_id"])->get("products")->row_array();
            if(!in_array($seller["seller_id"], $sell)){
                array_push($sell, $seller["seller_id"]);
            }            
        }
        
        foreach($sell as $s){            
            //notification
            $insert_arr = array(
                'order_id' => $post["order_id"],
                'notification_type' => 1,
                'message' => "A new order for you",
                'seller_id' => $s,
            );
            $this->db->insert('website_notification', $insert_arr);
            
            if($s != 0){
                $contact = $this->db->select("concat(country_code,' ',contact_no) as phone, email", false)->where("seller_id", $s)->get("seller")->row_array();
                
                //sms
                $this->nexmo->sms($contact['phone'], "A new order for you");
                
                //email
                $this->send_mail($contact["email"], "Janet-Collection - New Order", "A new order for you");
            }
        }
        return true;
    }
    
    function generate_alphanumeric_code() {
	$code =  substr(md5(rand()), 1, 10);
	return $code;
    }
    
    function get_zone_from_code($code) {
	$code_zone = $this->db->select("*")->where("zipcode", $code)->get("zipcode")->row_array();
	if(!empty($code_zone)) {
	    return $code_zone["dzone_id"];
	}
	else {
	    return '';
	}
    }
    
    function get_currency() {
	$currency = $this->db->select("*")->where("key", "currency")->get("setting")->row_array();
	return $currency;
    }
    
    function get_schedule_list(){
        $response = $this->db->select("*")->where('status', 1)->get("schedule_order_list")->result_array();
        if(!empty($response)){
            return $response;
        }
    }
    
    function get_return_product_details($order_id, $product_id, $volume_id){
        $returned = $this->db->select("*")
                ->where('product_id', $product_id)
                ->where('volume_id', $volume_id)
                ->where('order_id', $order_id)
                ->get('products_returned')->row_array();
        if(!empty($returned)){
            return $returned;
        }
    }
    
    function get_product_return_policy($product_id){        
        
        $this->db->select("*", FALSE)
            ->from('product_return_policy')
            ->where('product_id', $product_id);
        $return_policy = $this->db->get()
                ->row_array();   
        
        if (!empty($return_policy)) {
            return $return_policy;            
        }        
        else {
            return false;
        }
    }
    
    function get_volume_by_id($volume_id){
        $volume = $this->db->select("volume_mst.volume_id, concat(volume_mst.volumne_value,' ',volume_type.volume_type) as volume", FALSE)
                ->join('volume_type', 'volume_type.volume_type_id = volume_mst.type')
                ->where('volume_mst.volume_id', $volume_id)
                ->get('volume_mst')
                ->row_array();
        
        if(!empty($volume)){
            return $volume;
        }
    }
    
    function get_user_by_id($user_id) {
        $user = $this->db->select("*, concat(firstname,' ',lastname) as name", false)
                ->where('user_id', $user_id)
                ->get('user')->row_array();
        
        if(!empty($user)) {
            unset($user["password"]);
            return $user;
        }
    }
    
    public function get_cateogry_by_id($category_id) {
        $main_cat = $this->db->select("*")
                        ->where('category_id', $category_id)
                        ->get('category_mst')
                        ->row_array();

        return $main_cat; 
    }    
    
    public function get_subcategory_list($category_id) {
        $cid = base64_decode($category_id);
        
        $get_data = $this->db->select('*')
            ->where('parent_id', $cid)
            ->where('status', 1)
            ->get('category_mst')
            ->result_array();

        if(!empty($get_data)){
            foreach($get_data as $key => $value) {
                if($value['category_img']){                
                    $get_data[$key]['category_img'] = $this->image_url( $value['category_img'],'', 'category');    
                    $get_data[$key]['category_img_thumb'] = $this->image_url( $value['category_img'],'thumb', 'category');    
                }
                else {
                    $get_data[$key]['category_img'] = '';
                    $get_data[$key]['category_img_thumb'] = '';
                }   
            }      
            return $get_data;
        } 
        else{
            return false;
        }           
    }
    
    public function get_category_list($limit = '') {        
        
        if($limit == '') {
            $get_data = $this->db->select('*')
                ->where('parent_id', 0)
                ->where('status', 1)
                ->get('category_mst')
                ->result_array();
        }
        else {
            $get_data = $this->db->select('*')
                ->where('parent_id', 0)
                ->where('status', 1)
                ->limit($limit)
                ->get('category_mst')
                ->result_array();
        }
        
        if(!empty($get_data)) {        
            foreach($get_data as $key => $value){   

                $get_subcat_data = $this->db->select('*')
                    ->where('parent_id', $value['category_id'])
                    ->where('status', 1)
                    ->limit(2)
                    ->get('category_mst')
                    ->result_array();

                if(!empty($get_subcat_data)){
                    $get_data[$key]["have_subcategory"] = true;
                    $get_data[$key]["subcategory_list"] = $get_subcat_data;
                } 
                else{
                    $get_data[$key]["have_subcategory"] = false;
                    $get_data[$key]["subcategory_list"] = array();
                }
            //print_r($get_subcat_data); 

                if($value['category_img']){                
                    $get_data[$key]['category_img'] = $this->image_url( $value['category_img'],'', 'category');    
                    $get_data[$key]['category_img_thumb'] = $this->image_url( $value['category_img'],'thumb', 'category');    
                }
                else {
                    $get_data[$key]['category_img'] = '';
                    $get_data[$key]['category_img_thumb'] = '';
                }            
            }

            return $get_data;
        }
    }
    
    function image_url($pic, $thumb = '', $folder = '') {        
        if ($thumb) {
            if ($pic) {
                return S3_PATH . $folder .'/thumbs/' . $pic; 
            }
        } else {
            if ($pic) {
                return S3_PATH . $folder .'/' . $pic; 
            }
        }
    }
    
    function image_url_product($pic, $thumb = '', $folder='') {        
	if(empty($folder)) {
	    if ($thumb) {
		if ($pic) {
		    return 'https://Janet-Collection-media.s3.eu-west-2.amazonaws.com/product/thumb/' . $pic; 
		}
	    } else {
		if ($pic) {
		    return 'https://Janet-Collection-media.s3.eu-west-2.amazonaws.com/product/' . $pic; 
		}
	    }
	}
	else {
	    if ($thumb) {
		if ($pic) {
		    return 'https://Janet-Collection-media.s3.eu-west-2.amazonaws.com/product/'.$folder.'/thumb/' . $pic; 
		}
	    } else {
		if ($pic) {
		    return 'https://Janet-Collection-media.s3.eu-west-2.amazonaws.com/product/' . $folder.'/'.$pic; 
		}
	    }
	}
    }
    
    public function s3_url($pic, $thumb = '', $folder = '') {
        
        if($folder != ''){
            if ($thumb) {
                if ($pic) {
                    return S3_PATH . $folder .'/thumbs/' . $pic; 
                }
            } else {
                if ($pic) {
                    return S3_PATH . $folder .'/' . $pic; 
                }
            }
        }
        else {
            if ($thumb) {
                if ($pic) {
                    return S3_PATH . '/thumb/' . $pic; 
                }
            } else {
                if ($pic) {
                    return S3_PATH . $pic; 
                }
            }
        }
    }
    
    public function get_about_us(){
        $data = $this->db->select('value')
                ->where('key', 'about_us')
                ->get('setting')
                ->row_array();        
        return $data;
    }
    
    public function term_n_condition(){
        $data = $this->db->select('value')
                ->where('key', 'terms_and_conditions')
                ->get('setting')
                ->row_array();        
        return $data;
    }
    
    public function privacy_policy(){
        $data = $this->db->select('value')
                ->where('key', 'privacy_policy')
                ->get('setting')
                ->row_array();        
        return $data;
    }
    
    public function cookies(){
        $data = $this->db->select('value')
                ->where('key', 'cookies')
                ->get('setting')
                ->row_array();        
        return $data;
    }
    
    public function alcohol_awareness(){
        $data = $this->db->select('*')
                ->where('status', 1)
                ->get('alcohol_awareness')
                ->result_array();        
        return $data;
    }
    
    public function alcohol_awareness_detail($aid){
	$id = base64_decode($aid);
        $data = $this->db->select('*')
                ->where('aid', $id)
		->where('status', 1)
                ->get('alcohol_awareness')
                ->row_array();        
        return $data;
    }
    
    public function country_list(){
        $data = $this->db->select('*')
                ->get('country')
                ->result_array();        
        return $data;
    }
    
    public function save_contact($post = []) {
        //print_r($post); exit;
        $insert = $this->db->insert("contact_us", $post);
        if($insert) {
            return 1;
        }
        else {
            return 2;
        }
    }
    
    public function thumbCreate($img_uploadpath, $thumb_uploadpath, $source) {
        $fullPath = $img_uploadpath . $source;
        $thumbSize = 200;
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
        imagecopyresized($resized, $full, 0, 0, 0, 0, $resizedWidth, $resizedHeight, $width, $height);
        imagecopyresized($thumb, $resized, 0, 0, $thumbx, $thumby, $thumbSize, $thumbSize, $thumbSize, $thumbSize);

        if ($extension == 'jpg' || $extension == 'jpeg')
            $status = imagejpeg($thumb, $thumbPath . $name, $thumbQuality);
        if ($extension == 'gif')
            $status = imagegif($thumb, $thumbPath . $name, $thumbQuality);
        if ($extension == 'png')
            $status = imagepng($thumb, $thumbPath . $name, 9);
    }
    
    public function send_mail($to, $subject, $msg, $bcc='') {
        $ci = get_instance();
        $config = array();
        $tmp_arr = array();

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
    
    public function get_user_by_email($email) {
        $user = $this->db->select("*, concat(firstname,' ',lastname) as name", false)
                    ->where('email', $email)
                    ->get('user')->row_array();        
	unset($user["password"]);
        return $user;
    }
    
    function get_category($parent_id, $in_loyalty_club='') {
       
        if(isset($in_loyalty_club) && $in_loyalty_club != NULL) {
            $get_data = $this->db->select('*')
                ->where('parent_id', $parent_id)
                ->where('in_loyalty_club', 1)
                ->where('status', 1)
		->get('category_mst')
                ->result_array();
        }
        else {
            $get_data = $this->db->select('*')
                ->where('parent_id', $parent_id)
                ->where('status', 1)
		->get('category_mst')
                ->result_array();
        }
       // print_r($get_data); exit;
        
        foreach($get_data as $key => $value){   
            
            $get_subcat_data = $this->db->select('*')
                ->where('parent_id', $value['category_id'])
                ->where('status', 1)
                ->get('category_mst')
                ->result_array();
            
            if(!empty($get_subcat_data)){
                $get_data[$key]["have_subcategory"] = true;
            } 
            else{
                $get_data[$key]["have_subcategory"] = false;
            }  
        }
        
        if(!empty($get_data)){
            return $get_data;
        }
    }
    
     function get_sub_category($parent_id, $in_loyalty_club='') {
        
        if(isset($in_loyalty_club) && $in_loyalty_club != NULL) {
            $get_data = $this->db->select('*')
                ->where('parent_id', $parent_id)
                ->where('in_loyalty_club', 1)
                ->where('status', 1)
		->get('category_mst')
                ->result_array();
        }
        else {
            $get_data = $this->db->select('*')
                ->where('parent_id', $parent_id)
                ->where('status', 1)
		->get('category_mst')
                ->result_array();
        }
        
        if(!empty($get_data)){
            return $get_data;
        }
    }
    
    function price_filter(){
        $new_array = array();
	$max_min_amt = $this->db->select("COALESCE(MAX(product_details.actual_price), 0) as max_amount, COALESCE(MIN(product_details.actual_price), 0) as min_amount", false)
		->join('product_details', 'products.product_id = product_details.product_id and product_details.status=1')
		->where("products.status", 1)
		->get("products")
		->row_array();  

	if (!empty($max_min_amt)) {
	    $new_array["max_amount"] = $max_min_amt["max_amount"];
	    $new_array["min_amount"] = $max_min_amt["min_amount"];
	    return $new_array;            
	}        
	else {
	    return false;
	}
    }
    
    function get_abv_list() {		
        $list = array();
        $response = $this->db->select("COALESCE(MAX(abv_percent), 0) as max_abv_percent, COALESCE(MIN(abv_percent), 0) as min_abv_percent", false)
		->where("status", 1)
		->get("products")
		->row_array();
	
        if(!empty($response)) {
            $list["max_abv_percent"] = $response["max_abv_percent"];
	    $list["min_abv_percent"] = $response["min_abv_percent"];
	    return $list;
        }
        else {
            return false;
        }
    }
    
    function get_volume_list($dtype, $category='', $subcategory='', $type='', $brand=''){
	
	//with category parameter
        if(isset($category) && $category != NULL ) {
            $categoryid = ' and brand_category_allocation.category_id = '.$category.'';             
        }
        else {
            $categoryid = '';                        
        }
        
	//with sub-category parameter
        if(isset($subcategory) && $subcategory != NULL ) {
            $categoryid = ' and brand_category_allocation.category_id = '.$subcategory.'';             
        }
        else {
            $categoryid = '';                        
        }
	
	//with brand parameter
        if(isset($brand) && $brand != NULL ) {
            $brand_id = ' and brand_mst.brand_id IN ('.$brand.')';             
        }
        else {
            $brand_id = '';                        
        }
	
	//get brands
	$get_brands = $this->db->select("brand_mst.brand_id")
		->join("brand_category_allocation", "brand_category_allocation.brand_id = brand_mst.brand_id ".$categoryid."")
		->where("brand_mst.status", 1)
		->get("brand_mst")->result_array();
		
	$gbrands = array_column($get_brands, "brand_id");	
	$brands = implode(',', $gbrands);
	//echo $brands; exit;	
	if(empty($category) && empty($subcategory) && empty($brand)) {
            $where = "volume_mst.status = 1";
        }
	else if(!empty($brand)) {
            $where = "volume_mst.brand_id IN (".$brand.") and volume_mst.status = 1";
        } 
        else {
            $where = "volume_mst.brand_id IN (".$brands.") and volume_mst.status = 1";
        }     
	
	if($dtype == "1") {
	    $response = $this->db->select("volume_mst.type, volume_type.volume_type", false)	
		    ->join('volume_type', 'volume_type.volume_type_id = volume_mst.type')
		    ->where("volume_mst.status", 1)
		    ->where($where)
		    ->group_by("volume_mst.type")
		    ->get("volume_mst")
		    ->result_array();

	    if(!empty($response)) {
		return $response;
	    }
	    else {
		return false;
	    }
	}
	else if($dtype == "2") {
	    if(empty($type)) {
		return false;
	    }
	    else {

		$list = array();
		$response = $this->db->select("COALESCE(MAX(volume_mst.volumne_value), 0) as max_volume, COALESCE(MIN(volume_mst.volumne_value), 0 ) as min_volume", false)	
			->join('volume_type', 'volume_type.volume_type_id = volume_mst.type')
			->where("volume_mst.status", 1)
			->where("volume_mst.type", $type)
			->where($where)
			->order_by("volume_mst.volumne_value", "desc")
			->limit(1)
			->get("volume_mst")
			->row_array();

//		print_r($response);
//		exit;

		if(!empty($response)) {
		    $list["max_volume"] = $response["max_volume"];
		    $list["min_volume"] = $response["min_volume"];
		    return $list;
		}
		else {
		    return 1;
		}
	    }
	}
    }
    
    function get_brand_list($category='', $subcategory='', $in_loyalty_club=''){
        $brands = array();
	
        if(!empty($category) && empty($subcategory)){
            $get_brand_category_list = $this->db->select('brand_id')
                    ->where('category_id', $category)
                    ->get('brand_category_allocation')->result_array();
            
	    if(!empty($get_brand_category_list)) {
		$import = array();
		foreach($get_brand_category_list as $bcl){
		    array_push($import, $bcl["brand_id"]);
		}
		$brand_ids = implode(',', $import);

		if(isset($in_loyalty_club) && $in_loyalty_club != NULL) {
		    $where = "brand_id IN (".$brand_ids.") and in_loyalty_club = 1";
		}
		else {
		    $where = "brand_id IN (".$brand_ids.")";
		}
	    }
	    else {
		return false;
	    }
            
            $get_brand_list = $this->db->select('*')
                    ->where($where)
                    ->where('status', 1)
                    ->get('brand_mst')->result_array();
        }
        else if(!empty($category) && !empty($subcategory)){
            $get_brand_category_list = $this->db->select('brand_id')
                    ->where('category_id', $subcategory)
                    ->get('brand_category_allocation')->result_array();
            
	    if(!empty($get_brand_category_list)) {
		$import = array();
		foreach($get_brand_category_list as $bcl){
		    array_push($import, $bcl["brand_id"]);
		}
		$brand_ids = implode(',', $import);

		if(isset($in_loyalty_club) && $in_loyalty_club != NULL) {
		    $where = "brand_id IN (".$brand_ids.") and in_loyalty_club = 1";
		}
		else {
		    $where = "brand_id IN (".$brand_ids.")";
		}
	    }
            else {
		return false;
	    }
            $get_brand_list = $this->db->select('*')
                    ->where($where)
                    ->where('status', 1)
                    ->get('brand_mst')->result_array();
        }
        else {
            
            if(isset($in_loyalty_club) && $in_loyalty_club != NULL) {
                $get_brand_list = $this->db->select('*')
                    ->where('in_loyalty_club', 1)
                    ->where('status', 1)
                    ->get('brand_mst')->result_array();
            }
            else {
                $get_brand_list = $this->db->select('*')
                    ->where('status', 1)
                    ->get('brand_mst')->result_array();
            }
        }
        
        foreach($get_brand_list as $key => $value){   
            
            $check = $this->db->select("product_id")->where("brand_id", $value["brand_id"])->get("products")->result_array();
            if(!empty($check)) {            
                if($value['brand_logo']){
                    $get_brand_list[$key]['brand_logo'] = $this->image_url( $value['brand_logo'],'', 'brand');    
                    $get_brand_list[$key]['brand_logo_thumb'] = $this->image_url( $value['brand_logo'] ,'thumb', 'brand');    
                    $get_brand_list[$key]['slider_img'] = $this->image_url( $value['slider_img'],'', 'brand');    
                    $get_brand_list[$key]['slider_img_thumb'] = $this->image_url( $value['slider_img'] ,'thumb', 'brand');
                }
                else {
                    $get_brand_list[$key]['brand_logo'] = '';
                    $get_brand_list[$key]['brand_logo_thumb'] = '';
                    $get_brand_list[$key]['slider_img'] = '';
                    $get_brand_list[$key]['slider_img_thumb'] = '';
                }
                
                array_push($brands, $get_brand_list[$key]);
            }
            
        }

        if (!empty($brands)) {
            return $brands;            
        }        
        else {
            return false;
        }
    }
}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Products extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('m_tools');
	$this->load->model('m_shipping');
	$this->load->model('m_home');
        $this->load->model('m_product');
        $this->load->model('m_cart');
    }
    
    public function add_to_bag() 
    {
        $post = $_POST;
        $post["from_where"] = 1;
	$response = $this->m_tools->use_api('add_to_bag', $post);	
	echo json_encode($response);
//        $response = $this->m_cart->add_to_bag($post);
//        echo $response;
    }

    public function product_list()
    {
	//print_r($_POST); exit;
	if( isset($_POST["byajax"]) && $_POST["byajax"] == '1' ) {
	    
	    $cid = trim($_POST["cid"]);
	    $arr["offset"] = $_POST["offset"];
	    
	    //check category / sub category
	    $check_category = $this->m_tools->get_cateogry_by_id($cid);
	    
	    if($check_category["parent_id"] == 0) {
		$arr["category"] = $cid;
	    }
	    else {
		$arr["category"] = $check_category["parent_id"];
		$arr["subcategory"] = $cid;
	    }	    
	    
	    if (!empty($this->session->userdata('user_id'))) {
		$response = $this->m_tools->use_api('products_list', $arr);
	    }
	    else {
		$response = $this->m_tools->use_api3('products_list', $arr);
	    }
	
	    $data["products"] = $response["product_list"];
	    $data["offset"] = $response["offset"];
	    $data["flag"] = $response["flag"];
	    
	    if($data["products"] == 'error') {
		echo 'error'; exit;
	    }
	    else {
		echo json_encode($data); exit;
	    }
	}
	else {
	    $cid = base64_decode(urldecode($_GET["cid"]));
	    $arr["offset"] = 0;
	    
	    //check category / sub category
	    $check_category = $this->m_tools->get_cateogry_by_id($cid);
	    
	    if($check_category["parent_id"] == 0) {
		$arr["category"] = $cid;
	    }
	    else {
		$arr["category"] = $check_category["parent_id"];
		$arr["subcategory"] = $cid;
	    }	    
	    
	    if (!empty($this->session->userdata('user_id'))) {
		$response = $this->m_tools->use_api('products_list', $arr);
	    }
	    else {
		$response = $this->m_tools->use_api3('products_list', $arr);
	    }
	
	    $data["title"] = "Product List";
	    $data["cid"] = $cid;
	    $data["products"] = $response["product_list"];
	    $data["offset"] = $response["offset"];
	    $data["flag"] = $response["flag"];
	    $data["stype"] = 1;
	    $this->m_tools->generaltemplate('product_list', $data);
	}
    }
    
    public function product_detail()
    {
	$pid = base64_decode(urldecode($_GET["pid"]));
	$arr["offset"] = 0;
	$arr["product_id"] = $pid;
	if (!empty($this->session->userdata('user_id'))) {
	    $udata = $this->m_tools->get_user($this->session->userdata('user_id'));	    
	    $arr['latitude'] = $udata["latitude"];
	    $arr['longitude'] = $udata["longitude"];
	    
	    $response = $this->m_tools->use_api('get_product_details', $arr);
	    $response1 = $this->m_tools->use_api('similar_products', $arr);
	}
	else {
	    $response = $this->m_tools->use_api3('get_product_details', $arr);
	    $response1 = $this->m_tools->use_api3('similar_products', $arr);
	}
		    
        $data["product_details"] = $response["product"];
        $data["similar_products"] = $response1["similar_products"];
	
	$this->m_tools->generaltemplate('product_detail', $data);
    }
    
    public function like_unlike()
    {
        $arr["product_id"] = $_POST["product_id"];
	$arr["is_fav"] = $_POST["fav_val"];
        $response = $this->m_tools->use_api('make_fav_unfav_product', $arr);
	
        if($response === 1) {
            echo "1";
        }
        else {
            echo "2";            
        }
    }
    
    public function search_product()
    {
	if( isset($_POST["byajax"]) && $_POST["byajax"] == '1' ) {
//	    print_r($_POST); exit;
	    $arr["offset"] = $_POST["offset"];
	    $arr["chr"] = $_POST["chr"];
	    
	    if (!empty($this->session->userdata('user_id'))) {
		$udata = $this->m_tools->get_user($this->session->userdata('user_id'));	    
		$arr['latitude'] = $udata["latitude"];
		$arr['longitude'] = $udata["longitude"];
		$response = $this->m_tools->use_api('search_product', $arr);
	    }
	    else {
		$response = $this->m_tools->use_api3('search_product', $arr);
	    }
	    
	    $data["products"] = $response["search"];
	    $data["offset"] = $response["offset"];
	    $data["flag"] = $response["flag"];
	    
	    if($data["products"] == 'error') {
		echo 'error'; exit;
	    }
	    else {
		echo json_encode($data); exit;
	    }
	}
	else {
	    $chr = urldecode($_GET["chr"]);
	    $arr["offset"] = 0;
	    $arr["chr"] = urldecode($chr);
            
	    if (!empty($this->session->userdata('user_id'))) {
		$udata = $this->m_tools->get_user($this->session->userdata('user_id'));	    
		$arr['latitude'] = $udata["latitude"];
		$arr['longitude'] = $udata["longitude"];
		$response = $this->m_tools->use_api('search_product', $arr);
	    }
	    else {
		$response = $this->m_tools->use_api3('search_product', $arr);
	    }
            
            $data["title"] = "Searched Product List";
	    $data["chr"] = $chr;
	    $data["products"] = $response["search"];
	    $data["offset"] = $response["offset"];
	    $data["flag"] = $response["flag"];
	    $data["stype"] = 2;
	    $this->m_tools->generaltemplate('product_list', $data);
	}
    }
    
    public function search_product_name()
    {
        $post = $_POST;
        $response = $this->m_product->search_product_name($post);
	//print_r($response); exit;   
	if(!empty($response)) {
	    echo '<ul id="product_list">';
	    foreach($response as $p) {
		echo '<li><a href="'.base_url().'pps?chr='.urlencode($p).'">'.$p.'</a></li>';
	    }
	    echo '</ul>';
	}
	else {
	    echo '<ul id="product_list">';
	    echo '<li>No products</li>';
	    echo '</ul>';
	}
    }
    
    public function similar_drinks()
    {
	if( isset($_POST["byajax"]) && $_POST["byajax"] == '1' ) {
	    $pid = trim($_POST["pid"]);
	    $arr["offset"] = $_POST["offset"];
	    $arr["product_id"] = $pid;
	    
	    if (!empty($this->session->userdata('user_id'))) {
		$udata = $this->m_tools->get_user($this->session->userdata('user_id'));	    
		$arr['latitude'] = $udata["latitude"];
		$arr['longitude'] = $udata["longitude"];
		$response = $this->m_tools->use_api('similar_products', $arr);
	    }
	    else {
		$response = $this->m_tools->use_api3('similar_products', $arr);
	    }
	    
	    $data["products"] = $response["similar_products"];
	    $data["offset"] = $response["offset"];
	    $data["flag"] = $response["flag"];
	    
	    if($data["products"] == 'error') {
		echo 'error'; exit;
	    }
	    else {
		echo json_encode($data); exit;
	    }
	}
	else {
	    $pid = base64_decode(urldecode($_GET["pid"]));
	    $arr["offset"] = 0;
	    $arr["product_id"] = $pid;
	    
	    if (!empty($this->session->userdata('user_id'))) {
		$udata = $this->m_tools->get_user($this->session->userdata('user_id'));	    
		$arr['latitude'] = $udata["latitude"];
		$arr['longitude'] = $udata["longitude"];
		$response = $this->m_tools->use_api('similar_products', $arr);
	    }
	    else {
		$response = $this->m_tools->use_api3('similar_products', $arr);
	    }
	
	    $data["title"] = "Similar Products";
	    $data["pid"] = $pid;
	    $data["products"] = $response["similar_products"];
	    $data["offset"] = $response["offset"];
	    $data["flag"] = $response["flag"];
	    $data["stype"] = 3;
	    $this->m_tools->generaltemplate('product_list', $data);	    
	}
    }
    
    public function favourite_products()
    {
	if( isset($_POST["byajax"]) && $_POST["byajax"] == '1' ) {
	    $arr["offset"] = 0;
	    $response = $this->m_tools->use_api('get_favourite_product_list', $arr);	 
	    $data["products"] = $response["favourite_products"];
	    $data["offset"] = $response["offset"];
	    $data["flag"] = $response["flag"];
	    
	    if($data["products"] == 'error') {
		echo 'error'; exit;
	    }
	    else {
		echo json_encode($data); exit;
	    }
	}
	else {
	    $arr["offset"] = 0;
	    $response = $this->m_tools->use_api('get_favourite_product_list', $arr);	    
	    $data["title"] = "Favourite Product List";
	    $data["products"] = $response["favourite_products"];
	    $data["offset"] = $response["offset"];
	    $data["flag"] = $response["flag"];
	    $data["stype"] = 4;
	    $this->m_tools->template('product_list', $data);	 
	}
    }
    
    public function filtered_product()
    {
	$post = $_POST;
	session_start();
	
	if(!isset($post["brand"])) {
	    $post["brand"] = "";
	}
	
	if(!isset($post["country"])) {
	    $post["country"] = "";
	}
	    
	//print_r($post); exit;
	if( !isset($_POST["byajax"]) ) {
	    $_SESSION["store_array"] = $post;	
	}
	else if( isset($_POST["byajax"]) && $_POST["byajax"] == '1' ) {
	    $post = array_merge($post, $_SESSION["store_array"]);
	}
	//print_r($_SESSION); print_r($post); exit;
	 
	//filter array
	if (!$this->session->userdata('loged_in')) { 
	    $latitude = '';
	    $longitude = '';
	}
	else {
	    $user_id = $this->session->userdata('user_id');
	    $userdata = $this->m_tools->get_user_by_id($user_id);
	    if(!empty($userdata["latitude"]) && !empty($userdata["longitude"])) {
		$latitude = $userdata["latitude"];
		$longitude = $userdata["longitude"];
	    }
	    else {
		$latitude = '';
		$longitude = '';
	    }
	}	
	
	if(!empty($post["category"])) {
	    $category = $post["category"];
	}
	else {
	    $category = '';
	}
	
	if(!empty($post["subcategory"])) {
	    $subcategory = $post["subcategory"];
	}
	else {
	    $subcategory = '';
	}
	
	if(!empty($post["brand"])) {
	    $brand = implode(",", $post["brand"]);
	}
	else {
	    $brand = '';
	}
	
	if(!empty($post["volume_filter"])) {
	    $vf = explode(";", $post["volume_filter"]);
	    if(!empty($vf[0]) && !empty($vf[1])) {
		$volume_max = $vf[1];
		$volume_min = $vf[0];
	    }
	    else {
		$volume_max = '';
		$volume_min = '';
	    }
	}
	else {
	    $volume_max = '';
	    $volume_min = '';
	}
	
	if(!empty($post["volume_type"])) {
	    $volume_type = $post["volume_type"];
	}
	else {
	    $volume_type = '';
	}
	
	if(!empty($post["sort_by"])) {
	    $sort_by = $post["sort_by"];
	}
	else {
	    $sort_by = '';
	}
	
	if(!empty($post["country"])) {
	    $country = $post["country"];
	}
	else {
	    $country = '';
	}
	
	if(!empty($post["abv_filter"])) {
	    $af = explode(";", $post["abv_filter"]);
	    $abv_max = $af[1];
	    $abv_min = $af[0];
	}
	else {
	    $abv_max = '';
	    $abv_min = '';
	}
	
	if(!empty($post["price_filter"])) {
	    $pf = explode(";", $post["price_filter"]);
	    $max_amt = $pf[1];
	    $min_amt = $pf[0];
	}
	else {
	    $max_amt = '';
	    $min_amt = '';
	}
	
	$array = array(
	    "category" => $category,
	    "subcategory" => $subcategory,
	    "brand" => $brand,
	    "volume_max" => $volume_max,
	    "volume_min" => $volume_min,
	    "volume_type" => $volume_type,
	    "latitude" => $latitude,
	    "longitude" => $longitude,
	    "abv_max" => $abv_max,
	    "abv_min" => $abv_min,
	    "country" => $country,
	    "min_amt" => $min_amt,
	    "max_amt" => $max_amt,
	    "sort_by" => $sort_by,
	    "top_pick" => 0	    
	);
	//print_r($array); exit;
	
	if( isset($_POST["byajax"]) && $_POST["byajax"] == '1' ) {
	    $product_list = $this->m_product->get_products_list($_POST["offset"], $array);
	    $data["products"] = $product_list[2];
	    $data["offset"] = $product_list[1];
	    $data["flag"] = $product_list[0];
	    
	    if($data["products"] == 'error') {
		echo 'error'; exit;
	    }
	    else {
		echo json_encode($data); exit;
	    }
	}
	else {		
	    $product_list = $this->m_product->get_products_list(0, $array);
	    
	    if($product_list == 'error') {
		$data["title"] = "Product List";
		$data["products"] = array();
		$this->load->view('header');
		$this->load->view('product_list', $data);
		$this->load->view('footer');
	    }
	    else {
		//echo "<pre>"; print_r($product_list); exit;
		$data["title"] = "Product List";
		$data["products"] = $product_list[2];
		$data["offset"] = $product_list[1];
		$data["flag"] = $product_list[0];
		$data["stype"] = 5;
		$this->load->view('header');
		$this->load->view('product_list', $data);
		$this->load->view('footer');
	    }
	}
    }
}
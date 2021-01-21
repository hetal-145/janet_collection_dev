<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('m_tools');        
        $this->load->model('m_home');
    }

    public function index()
    {
	//print_r($_POST);
	$data["categories"] = $this->m_tools->get_category_list(10);
	$settings = $this->m_tools->get_settings();
	
	//if(empty($_POST)) {
	    if (!empty($this->session->userdata('user_id'))) {
		$udata = $this->m_tools->get_user($this->session->userdata('user_id'));
		$arr = array(
		    'latitude' => $udata["latitude"],
		    'longitude' => $udata["longitude"]
		);
		$response = $this->m_tools->use_api('get_top_pick_products', $arr);
		$response1 = $this->m_tools->use_api('best_selling_products', $arr);
	    }
	    else {
		$response = $this->m_tools->use_api3('get_top_pick_products');
		$response1 = $this->m_tools->use_api3('best_selling_products');
	    }

        if($response["status"] == "false") {
            $data["top_picks"] = array();    
        }
        else {
            $data["top_picks"] = $response["top_pick_product"];
        }

        if($response1["status"] == "false") {
            $data["best_selling_product"] = array();    
        }
        else {
            $data["best_selling_product"] = $response1["best_selling_products"];
        } 

//	    echo "<pre>"; print_r($response1); exit;
	    // $data["top_picks"] = $response["top_pick_product"];	    
	    // $data["best_selling_product"] = $response1["best_selling_products"];	 
//	}
//	else if(!empty($_POST)) {
//	    $dzone = $this->m_tools->get_zone_from_code($_POST["search"]);
//	    if(!empty($dzone)) {
//		$data["top_picks"] = $this->m_home->get_top_pick_product_list($dzone);
//		$data["best_selling_product"] = $this->m_home->get_best_selling_products($dzone);
//	    }
//	    else {
//		$data["top_picks"] = $this->m_home->get_top_pick_product_list();
//		$data["best_selling_product"] = $this->m_home->get_best_selling_products();
//	    }
//	}
//	print_r($settings); exit;
	$data["video"] = $settings[37]["value"];
	$data["vimage"] = $settings[38]["value"];
	$data["stat1"] = $settings[39]["value"];
	$data["stat2"] = $settings[40]["value"];
	$data["stat3"] = $settings[41]["value"];
	//echo "<pre>"; print_r($data); exit;		    
		
        $this->m_tools->generaltemplate('index', $data); 
    }
    
    public function page_no_found()
    {
        $this->load->view('header');
        $this->load->view('four_zero_four');
        $this->load->view('footer');
    }
    
    public function no_data(){
        $this->load->view('header');
        $this->load->view('no_data');
        $this->load->view('footer');
    }
    
    public function about_us(){
        $data['content'] =  $this->m_tools->get_about_us();
        $data['type'] = 'about_us';
        $this->load->view('header', $data);
        $this->load->view('common', $data);
        $this->load->view('footer');
    }
    
    public function privacy_policy() {
        $data['content'] = $this->m_tools->privacy_policy();  
        $data['type'] = 'privacy_policy';
        $this->load->view('header', $data);
        $this->load->view('common',$data);
        $this->load->view('footer');
    }
    
    public function terms_and_conditions() {
        $data['content'] = $this->m_tools->term_n_condition();
        $data['type'] = 'term_n_condition';
        $this->load->view('header', $data);
        $this->load->view('common', $data);
        $this->load->view('footer');
    }
    
    public function cookies() {
        $data['content'] = $this->m_tools->cookies();
        $data['type'] = 'cookies';
        $this->load->view('header', $data);
        $this->load->view('common', $data);
        $this->load->view('footer');
    }
    
    public function alcohol_awareness() {
        $data['alcohol_awareness'] = $this->m_tools->alcohol_awareness();
        $this->load->view('header', $data);
        $this->load->view('alcohol_awareness', $data);
        $this->load->view('footer');
    }
    
    public function alcohol_awareness_detail() {
        $data['alcohol_awareness'] = $this->m_tools->alcohol_awareness_detail($_GET["aid"]);
	$data['type'] = 'alcohol_awareness';
        $this->load->view('header', $data);
        $this->load->view('alcohol_awareness_detail', $data);
        $this->load->view('footer');
    }
    
    public function licensed_retailers() {
        $data['content'] = $this->m_home->licensed_retailers();
        $this->load->view('header', $data);
        $this->load->view('licensed_retailers', $data);
        $this->load->view('footer');
    }
    
    public function faq_list() {
        $data['content'] = $this->m_home->get_faq_question_list();
        $this->load->view('header', $data);
        $this->load->view('faq_list', $data);
        $this->load->view('footer');
    }
    
    public function seller_faq_list() {
        $data['content'] = $this->m_home->get_seller_faq_question_list();
        $this->load->view('header', $data);
        $this->load->view('faq_list', $data);
        $this->load->view('footer');
    }
    
    public function get_subcategory() {
	$post = $_POST;
	$subcategory = $this->m_tools->get_sub_category($post["category"]);
	echo json_encode($subcategory);
    }
    
    public function get_brand() {
	$post = $_POST;
	
	if(!empty($post["category"]) && empty($post["subcategory"])) {
	    $brand = $this->m_tools->get_brand_list($post["category"]);
	}
	else if(!empty($post["category"]) && !empty($post["subcategory"])) {
	    $brand = $this->m_tools->get_brand_list($post["category"], $post["subcategory"]);
	}
	
	echo json_encode($brand);
    }
    
    public function get_volume_type() {
	$post = $_POST;
	//print_r($post); exit;
	if(!empty($post["category"]) && empty($post["subcategory"])) {
	    $brand = $this->m_tools->get_volume_list(1, $post["category"]);
	}
	else if(!empty($post["category"]) && !empty($post["subcategory"])) {
	    $brand = $this->m_tools->get_volume_list(1, $post["category"], $post["subcategory"]);
	}
	else if( (!empty($post["category"]) && !empty($post["brands"])) || (!empty($post["category"]) && !empty($post["brands"]) && !empty($post["subcategory"])) ) {
	    $brand = $this->m_tools->get_volume_list(1, $post["category"], $post["subcategory"], '', $post["brands"]);
	}
	else if(!empty($post["brands"])) {
	    $brand = $this->m_tools->get_volume_list(1, '', '', '', $post["brands"]);
	}
	
	echo json_encode($brand);
    }
    
    public function get_volume() {
	$post = $_POST;
	//print_r($post); exit;
	if(!empty($post["category"]) && empty($post["subcategory"])) {
	    $brand = $this->m_tools->get_volume_list(2, $post["category"], '', $post["volume_type"] );
	}
	else if(!empty($post["category"]) && !empty($post["subcategory"])) {
	    $brand = $this->m_tools->get_volume_list(2, $post["category"], $post["subcategory"], $post["volume_type"]);
	}
	else if( (!empty($post["category"]) && !empty($post["brands"])) || (!empty($post["category"]) && !empty($post["brands"]) && !empty($post["subcategory"])) ) {
	    $brand = $this->m_tools->get_volume_list(2, $post["category"], $post["subcategory"], $post["volume_type"], $post["brands"]);
	}
	else if(!empty($post["brands"])) {
	    $brand = $this->m_tools->get_volume_list(2, '', '', $post["volume_type"], $post["brands"]);
	}
	
	echo json_encode($brand);
    }
}
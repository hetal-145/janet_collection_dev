<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Category extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('m_tools');
    }

    public function index()
    {  
	$cid = base64_decode(urldecode($_GET["cid"]));
	$arr["offset"] = 0;
	$arr["category_id"] = $cid;
	if (!empty($this->session->userdata('user_id'))) {
	    $response = $this->m_tools->use_api('get_sub_category', $arr);
	}
	else {
	    $response = $this->m_tools->use_api3('get_sub_category', $arr);
	}
	
	$data["categories"] = $response["category"];
        $response1 = $this->m_tools->get_cateogry_by_id($cid);
	$data["main_category"] = $response1["category_name"];
        $this->m_tools->generaltemplate('category', $data);
    }
    
    public function category_list()
    {     
	$arr["offset"] = 0;
	$arr["parent_id"] = 0;
	if (!empty($this->session->userdata('user_id'))) {
	    $response = $this->m_tools->use_api('get_category', $arr);
	}
	else {
	    $response = $this->m_tools->use_api3('get_category', $arr);
	
	}
        $data["categories"] = $response["category"];
        $this->m_tools->generaltemplate('category_list', $data);
    }
}
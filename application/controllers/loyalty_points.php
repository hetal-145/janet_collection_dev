<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Loyalty_points extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('m_tools');
        $this->load->model('m_login');
        $this->load->model('m_loyalty_points');
        $this->m_login->check_session();
    }

    public function index()
    {
        $data["points"] = $this->m_loyalty_points->get_loyalty_point();
        $this->load->view('header');
        $this->load->view('loyalty_points', $data);
        $this->load->view('footer');
    }
    
    public function loyality_club(){
	if( isset($_POST["byajax"]) && $_POST["byajax"] == '1' ) {
	    $product_list = $this->m_loyalty_points->loyalty_club_list($_POST["offset"]);
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
	    $product_list = $this->m_loyalty_points->loyalty_club_list($_GET["offset"]);
	    $data["title"] = "Loyality Club Product List";
	    $data["products"] = $product_list[2];
	    $data["offset"] = $product_list[1];
	    $data["flag"] = $product_list[0];
	    $data["stype"] = 6;
	    $this->load->view('header');
	    $this->load->view('product_list', $data);
	    $this->load->view('footer');
	}
    }
    
    public function vip_club(){
	if( isset($_POST["byajax"]) && $_POST["byajax"] == '1' ) {
	    $product_list = $this->m_loyalty_points->vip_club_list($_POST["offset"]);
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
	    $product_list = $this->m_loyalty_points->vip_club_list($_GET["offset"]);
	    $data["title"] = "VIP Club Product List";
	    $data["products"] = $product_list[2];
	    $data["offset"] = $product_list[1];
	    $data["flag"] = $product_list[0];
	    $data["stype"] = 7;
	    $this->load->view('header');
	    $this->load->view('product_list', $data);
	    $this->load->view('footer');
	}
    }
}
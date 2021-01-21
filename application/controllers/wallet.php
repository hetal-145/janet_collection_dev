<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wallet extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('m_tools');
        $this->load->model('m_login');
        $this->load->model('m_wallet');
	$this->load->model('m_orders');
        $this->m_login->check_session();
    }

    public function index()
    {
	$currency = $this->m_tools->get_data_from_setting("currency");
        $data["currency"] = $currency["value"];	
        $response = $this->m_tools->use_api('wallet_details_by_id');
	$data["wallet_balance"] = $response["wallet"]["remaining_amount"];
        $data["order_return"] = $this->m_orders->order_return();
        $this->m_tools->template('wallet', $data);
    }
}
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Return_policy extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');

        $this->load->model('m_login');
        $this->load->model('m_tools');
        $this->m_login->check_session();
    }

    public function index() {

        $xcrud = Xcrud::get_instance();
        $xcrud->table('product_return_policy');
        
        $xcrud->columns('product_id, description'); 
        $xcrud->fields('product_id, description'); 
        
        /* Get Products */
        $get_products = $this->db->select('products.*, category_mst.*, brand_mst.*')
                ->where('products.status', 1)
                ->join( 'category_mst', 'category_mst.category_id = products.category_id')
                ->join( 'brand_mst', 'brand_mst.brand_id = products.brand_id')
                ->get('products')->result_array();
        $arr_products = array();
        if(count($get_products) > 0){
            foreach ($get_products as $key => $value) {
                $arr_products[$value['product_id']] = $value['category_name'] . ' => ' . $value['brand_name'] . ' => ' . $value['product_name'];
            }
        }
        
        $xcrud->before_insert('check_exist_return_policy');
        $xcrud->after_insert('update_product_flag');
        
        $xcrud->label(array(
            'product_id' => 'Product',
            'description' => 'Description',
        ));
        
        $xcrud->unset_search();
        
        //echo "<pre>"; print_r($arr_products); exit;

        $xcrud->change_type('product_id', 'select', '1', $arr_products);
       
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('return_policy', $data);
        $this->load->view('footer');
    }

}

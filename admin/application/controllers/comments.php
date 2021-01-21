<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Comments extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');

        $this->load->model('m_login');
        $this->load->model('m_brand');
        $this->load->model('m_tools');
        $this->m_login->check_session();
    }

    public function index() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('product_rating'); 
        $xcrud->subselect('userno_id', '{user_id}');
        $xcrud->column_callback('userno_id', 'get_username');
        $xcrud->relation('user_id', 'user', 'user_id', 'userno');
        $xcrud->relation('product_id', 'products', 'product_id', 'product_name');
        $xcrud->columns('user_id, userno_id, product_id, rating, review', false);
        $xcrud->fields('user_id, userno_id, product_id, rating, review');
        $xcrud->search_columns('product_id, user_id, rating'); 
        $xcrud->column_pattern('rating', '{rating} / 5'); 
        $xcrud->label(array(
            'user_id' => 'Userno',
            'userno_id' => 'Name',
            'product_id' => 'Product Name',
            'rating' => 'Rating',
            'review' => 'Reviews',
        ));
        
        $xcrud->unset_add();
        $xcrud->unset_edit();
        //$xcrud->unset_view();
        //$xcrud->unset_remove();
        
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('comments', $data);
        $this->load->view('footer');
    }

}

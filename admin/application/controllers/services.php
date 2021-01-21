<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Services extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
//        $this->load->model('m_products');
        $this->load->model('m_login');
        $this->m_login->check_session();
    }

    public function index() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('products');
        $xcrud->where('post_type', 2);
        $xcrud->change_type('status', 'select', '1', array('1' => 'Active', '0' => 'Inactive'));
        $xcrud->join('product_id', 'product_description', 'product_id');
        $xcrud->join('product_id', 'product_videos', 'product_id');
        $xcrud->join('product_id', 'product_category', 'product_id');
        $xcrud->join('products.product_id', 'product_category', 'product_id');
        $xcrud->create_action('active', 'active_product');
        $xcrud->create_action('inactive', 'inactive_product');
        $xcrud->button('#', 'Inactive', 'glyphicon glyphicon-ban-circle', 'xcrud-action btn-danger', array(
            'data-task' => 'action',
            'data-action' => 'inactive',
            'data-primary' => '{product_id}'), array(
            'status',
            '=',
            '1')
        );
        $xcrud->button('#', 'Active', 'glyphicon glyphicon-ok', 'xcrud-action btn-success', array(
            'data-task' => 'action',
            'data-action' => 'active',
            'data-primary' => '{product_id}'), array(
            'status',
            '!=',
            '1'));

       
        $xcrud->modal('product_description.product_description');
        $xcrud->subselect('Posted By', 'select name FROM user WHERE user_id= {products.user_id}');
        $xcrud->subselect('Category', 'select category FROM category WHERE category_id= {product_category.category_id}');
        $xcrud->column_callback('product_videos.product_video_thumb', 'get_video');
        $xcrud->label('product_videos.product_video_thumb', 'Service Video');
        $xcrud->label('product_description.product_description', 'Service Description');
        $xcrud->columns('Posted By,title,Category,product_description.product_description,product_description.compensation,product_videos.product_video_thumb,status', false);
        $xcrud->unset_edit();
        $xcrud->unset_add();
        $xcrud->unset_view();
        $xcrud->unset_remove();
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('services', $data);
        $this->load->view('footer');
    }

}

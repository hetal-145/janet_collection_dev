<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Country_code extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');

        $this->load->model('m_login');
        $this->m_login->check_session();
    }

    public function index() {

        $xcrud = Xcrud::get_instance();
        $xcrud->table('country');        
        $xcrud->columns('name, code'); 
        $xcrud->fields('name, code'); 
        $xcrud->validation_required('name'); 
        $xcrud->search_columns('name, code', 'name'); 
        
        $xcrud->unset_view();
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('country_code', $data);
        $this->load->view('footer');
    }

}

<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class help_support extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');

        $this->load->model('m_login');
        $this->load->model('m_tools');
        $this->m_login->check_session();
    }

    public function index() {

        $xcrud = Xcrud::get_instance();
        $xcrud->table('help_support');
        $xcrud->where('type', 1);
        //$xcrud->subselect('name','SELECT concat(firstname," ",lastname) as name FROM user WHERE user_id={user_id}');
        $xcrud->columns('name,email,subject,message,date', false);
        
        $xcrud->label(array(
            'name' => 'User',
            'email' => 'Email',
            'subject' => 'Subject',
            'message' => 'Message',
            'date' => 'Received On',
        ));
        
        $xcrud->unset_add();
        $xcrud->unset_edit();
        $xcrud->unset_search();
       
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('help_support', $data);
        $this->load->view('footer');
    }
}

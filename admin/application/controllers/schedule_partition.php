<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Schedule_partition extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');

        $this->load->model('m_login');
        $this->load->model('m_tools');
        $this->m_login->check_session();
    }

    public function index() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('schedule_order_list');  
        $xcrud->columns('schedule_on_title');                
        $xcrud->fields('no_of_days, type');  
        $xcrud->change_type("type", "select", "1", array("1" => "Days", "2" => "Weeks", "3" => "Months"));
        $xcrud->after_insert('add_days'); 
        $xcrud->after_update('add_days'); 
        $xcrud->unset_search();
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('schedule_partition', $data);
        $this->load->view('footer');
    }
}

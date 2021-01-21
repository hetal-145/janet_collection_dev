<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Notification extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');

        $this->load->model('m_login');
        $this->load->model('m_tools');
        $this->load->model('m_notification');
        $this->m_login->check_session();
    }

    public function index() {
        error_reporting(E_ERROR | E_PARSE);        
        $xcrud = Xcrud::get_instance();
        $xcrud->query('SELECT message FROM notification WHERE notification_type = 2 group by notification_type, message order by date desc');
//        $xcrud->table('notification');  
//        $xcrud->where('notification_type', '2');
//        $xcrud->column_callback('notification_id', 'admin_notification_callback');
//        $xcrud->columns("notification_id");
//        $xcrud->label(array(
//            'notification_id' => 'Message',
//        ));
        
        $xcrud->unset_remove();
        $xcrud->unset_add();
        $xcrud->unset_edit();
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('notification', $data);
        $this->load->view('footer');
    }
    
    public function push_notify(){   
        //print_r($_POST); exit;
        $push = array(
           'to_user_id' => $_POST["to_user_id"],
           'message' => $_POST["message"], 
        );
        $this->m_notify->send($push);
    }
    
    public function add() {
        $data = array();
        $this->load->view('header', $data);
        $this->load->view('notification_add', $data);
        $this->load->view('footer');
    }
    
    public function save() {        
        $post = $_POST;
        $list = $this->m_notification->send_notification($post);            
        echo 'success'; exit;
    }
}

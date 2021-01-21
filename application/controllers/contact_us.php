<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contact_us extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->model('m_tools');
    }
    
    public function index(){
        $data["content"] = $this->m_tools->get_about_us();  
        $this->load->view('header');
        $this->load->view('contact_us', $data);
        $this->load->view('footer');
    }
    
    public function save(){  
        $post = $_POST;
        //print_r($post); exit;
        $response = $this->m_tools->save_contact($post);  
        if($response === 1) {
            $to = INQUIRY_EMAIL;
            $subject = trim($post["subject"]);
            $msg = $this->load->view('mail_tmp/header', $post, true);
            $msg .= $this->load->view('mail_tmp/contact_us', $post, true);
            $msg .= $this->load->view('mail_tmp/footer', $post, true);
            $this->m_tools->send_mail($to, $subject, $msg);
            echo 'success';
        }
        else if($response === 2) {
            echo 'error';
        }
    }
    
}
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Gift_card extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');

        $this->load->model('m_login');
        $this->load->model('m_tools');
        $this->m_login->check_session();
    }

    public function index() {

        $xcrud = Xcrud::get_instance();
        $xcrud->table('gift_card'); 
        
        $xcrud->change_type('banner_img', 'image', false, array(
            'width' => 720,
            'path' => '../../upload/gift_card',
            'thumbs' => array(
                array(
                    'height' => 100,
                    'width' => 100,
                    'crop' => true,
                    'marker' => '_th',
                    'folder' => 'thumbs'
                )
            )            
        ));
        
        $xcrud->columns('banner_img, code, expiry_date, card_amt');                
        $xcrud->fields('banner_img, code, description, expiry_date, card_amt'); 
        $xcrud->label(array(
            'banner_img' => 'Banner',
            'code' => 'Gift Card Code',
            'expiry_date' => 'Expiry Date',
            'description' => 'Description',
            'card_amt' => 'Amount'
        ));   
        
        //$xcrud->pass_var('expiry_date', date('Y-m-d H:i:s', strtotime("+1 month", strtotime( date('Y-m-d H:i:s') ))));
        $xcrud->pass_default('expiry_date', date('Y-m-d H:i:s', strtotime("+1 month", strtotime( date('Y-m-d H:i:s') ))));
                
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('gift_card', $data);
        $this->load->view('footer');
    }   

}

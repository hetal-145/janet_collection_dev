<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Promocode extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');

        $this->load->model('m_login');
        $this->load->model('m_tools');
        $this->m_login->check_session();
    }

    public function index() {

        $xcrud = Xcrud::get_instance();
        $xcrud->table('promocodes'); 
        $xcrud->validation_required('promocode, expiry_date, discount_amount');
        /*$xcrud->change_type('type', 'select', false, array(
            '1' => 'General',
            '2' => 'Product',
            '3' => 'Brand',
            '4' => 'Category',
        ));*/
        
       // $xcrud->subselect('product_id','SELECT product_name FROM products WHERE product_id={product_id}');
        
        $xcrud->change_type('type', 'select', false, array(
            '1' => 'General',
            '2' => 'Product',
        ));
        
        $xcrud->change_type('discount_type', 'select', false, array(
            '1' => 'Percentage (%)',
            '2' => 'Flat Amount',
        ));
        
        $xcrud->search_columns('promocode', 'promocode');  
        
        $xcrud->columns('promocode, type, product_id, expiry_date, discount_amount, discount_type');                
        $xcrud->fields('promocode, type, product_id, expiry_date, discount_amount, discount_type'); 
        $xcrud->label(array(
            'promocode' => 'Promocode',
            'type' => 'Code For',
            'product_id' => 'Product',
            'expiry_date' => 'Expiry Date',
            'discount_amount' => 'Discount',
            'discount_type' => 'Discount Type'
        ));
        
        $get_user_type = $this->db->select('*')->where('status', 1)->get('products')->result_array();
        $arr_user_type = array();
        if(count($get_user_type) > 0){
            $arr_user_type[''] = 'Select';
            foreach ($get_user_type as $key => $value) {
                $arr_user_type[$value['product_id']] = $value['product_name'];
            }
        }
        $xcrud->change_type('product_id', 'select', false, $arr_user_type);
        $xcrud->unset_remove();
        
        //$xcrud->pass_default('promocode', 'PROMO'.$this->m_tools->generate_random_code());
        $xcrud->pass_default('expiry_date', date('Y-m-d H:i:s', strtotime("+1 month", strtotime( date('Y-m-d H:i:s') ))));
        $xcrud->before_insert('check_date');
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('promocode', $data);
        $this->load->view('footer');
    }
    
    public function save() {
        $post = $_POST;  
        $files = $_FILES;  
        
        //upload files
        if (isset($files['category_img']['name']) && $files['category_img']['name']) {
           $ext = '.' . pathinfo($files['category_img']['name'], PATHINFO_EXTENSION);
           $filename = date('YmdHis') . rand() . strtolower($ext);
           $config = [
               'upload_path' => '../upload/category',
               'allowed_types' => 'gif|jpg|png|jpeg',
               'file_name' => $filename
           ];
           $this->load->library('upload', $config);
           $this->upload->initialize($config);
           if ($this->upload->do_upload('category_img')) {
               $post['category_img'] = $filename;
               $this->m_tools->thumbCreate('../upload/category/', '../upload/category/thumbs/', $filename, 300);
           }
       }

        $add_category_data = $this->m_category->add_category($post);
        
        //echo $add_models_data; 
        
        if($add_category_data === 'exist1'){            
            echo 'exist'; die();
        }
        else if(!$add_category_data)  {            
            echo 'error'; die();
        }
        else { 
            echo 'success'; die();
        }
    }
    
    public function get_category() {
        $post = $_POST;
        $category = $this->m_category->get_category_details($post);
        if ($category) {
            echo json_encode($category);
        }
    }

}

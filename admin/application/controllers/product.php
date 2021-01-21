<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require '../vendor_aws/autoload.php';
use Aws\S3\S3Client;
use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;

include('../vendor_spreadsheet/autoload.php');
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Product extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_login');
        $this->load->model('m_product');
        $this->load->model('m_tools');
        $this->m_login->check_session();
    }

    public function index() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('products');   
        $xcrud->where("status", 1);
        $xcrud->relation('brand_id', 'brand_mst', 'brand_id', 'brand_name');
        $xcrud->relation('category_id', 'category_mst', 'category_id', 'category_name');
        $xcrud->relation('supplier_id', 'suppliers', 'supplier_id', 'supplier_name');
        $xcrud->subselect('sellerid', '{seller_id}');
        $xcrud->relation('seller_id', 'seller', 'seller_id', 'seller_name');
	
	$xcrud->subselect('feature_img_view', '{feature_img}');
	$xcrud->subselect('price_product_id', '{product_id}');
	$xcrud->subselect('actual_product_id', '{product_id}');
	$xcrud->subselect('volume_product_id', '{product_id}');
	$xcrud->subselect('discount_product_id', '{product_id}');
	$xcrud->subselect('select_multi', '{product_id}');
	$xcrud->set_var('s3_path', S3_PATH.'product/');
	
        $xcrud->column_callback('sellerid', 'seller_label');
        $xcrud->column_callback('product_id', 'top_pick');	
	$xcrud->column_callback('select_multi', 'select_multiple');
        $xcrud->column_callback('feature_img', 'list_img_function');	
	$xcrud->column_callback('feature_img_view', 'view_img_function');
	$xcrud->column_callback('price_product_id', 'view_product_price');
	$xcrud->column_callback('actual_product_id', 'view_product_actual_price');
	$xcrud->column_callback('volume_product_id', 'view_product_volume');
	$xcrud->column_callback('discount_product_id', 'view_product_discount');
	//$xcrud->column_callback('category_id', 'category_callback');
	
        $xcrud->search_columns('category_id, brand_id, product_name, supplier_id, seller_id');                
//        $xcrud->columns('select_multi, feature_img, category_id, brand_id, product_name, supplier_id, sellerid, volume_product_id, actual_product_id, discount_product_id, price_product_id, product_id, in_loyalty_club, in_vip_club, status', false);
//	$xcrud->fields('feature_img_view, category_id, brand_id, product_name, supplier_id, sellerid, description, currency, top_pick, drink_type, abv_percent, alchol_units, country_id, have_return_policy, no_of_return_days, in_loyalty_club, in_vip_club', false, 'Product Info', 'view');        
        
        $xcrud->columns('select_multi, feature_img, category_id, brand_id, product_name, supplier_id, sellerid, volume_product_id, actual_product_id, discount_product_id, price_product_id, product_id, status', false);
	$xcrud->fields('feature_img_view, category_id, brand_id, product_name, supplier_id, sellerid, description, currency, top_pick, drink_type, abv_percent, alchol_units, country_id, have_return_policy, no_of_return_days', false, 'Product Info', 'view');
	
        $xcrud->change_type('product_id', 'bool');
//        $xcrud->change_type('in_loyalty_club', 'bool');
//        $xcrud->change_type('in_vip_club', 'bool');
	$xcrud->change_type('top_pick', 'bool');
	$xcrud->change_type('have_return_policy', 'bool');
	$xcrud->change_type('drink_type', 'select', '', array('1' => 'Alcoholic', '2' => 'Non-Alcoholic'));
	$xcrud->change_type('status', 'select', '', array('1' => 'Active', '0' => 'Inactive'));
	$xcrud->label(array(
            'feature_img' => 'Image',
	    'feature_img_view' => 'Image',
            'product_name' => 'Product',
            'category_id' => 'Category',
            'brand_id' => 'Brand',
            'supplier_id' => 'Supplier',
            'seller_id' => 'Seller',
            'sellerid' => 'Seller',
            'product_id' => 'Top Pick',
	    'country_id' => 'Country',
	    'actual_product_id' => 'Original Price',
	    'price_product_id' => 'Price',
	    'volume_product_id' => 'Volume',
	    'discount_product_id' => 'Discount',
	    'select_multi' => ""
        ));
	
	//Product Description
	$pdesc = $xcrud->nested_table('Product Details','product_id','product_details','product_id');
	$pdesc->column_callback('volume_id', 'view_volumne');	
        $pdesc->columns('volume_id, actual_price, units, min_stock_limit, max_stock_limit, pack_size', false);
//	$pdesc->fields('volume_id, actual_price, normal_discount, normal_sell_price, loyalty_club_discount, loyalty_club_sell_price, vip_club_discount, vip_club_sell_price, units, min_stock_limit, max_stock_limit, pack_size', false, false, 'view');
        
        $pdesc->fields('volume_id, actual_price, normal_discount, normal_sell_price, units, min_stock_limit, max_stock_limit, pack_size', false, false, 'view');
        $pdesc->hide_button('return');
	$pdesc->label(array(
            'volume_id' => 'Volume',
            'default_sell_price' => 'Default Price',  
            'min_sell_price' => 'Sell Price',            
            'min_stock_limit' => 'Min Stock Limit',            
            'max_stock_limit' => 'Max Stock Limit'
        ));
	
        //Gallery
        $gallery = $xcrud->nested_table('Gallery','product_id','product_images','product_id');
        $gallery->subselect('image_name_view', '{image_name}');
	$gallery->column_callback('image_name', 'list_img_function');
	$gallery->column_callback('image_name_view', 'view_img_function');
	$gallery->fields('image_name_view', false, false, 'view');
        $gallery->columns('image_name');
	$gallery->set_var('s3_path', S3_PATH.'product/');
        $gallery->label(array(
            'image_name' => '',
	    'image_name_view' => '',
        ));
        $gallery->hide_button('return');
        
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
               
        $xcrud->button('#', 'Edit', 'glyphicon glyphicon-edit', '', array(
            'data-toggle' => 'modal',
            'class' => 'btn btn-warning btn-sm edit_data',
            'data-target' => ".mdl_product",
            'data-primary' => '{product_id}'));
        
        $xcrud->unset_add();
        $xcrud->unset_edit();       
        $xcrud->unset_remove();
        
//        $xcrud->create_action('delete', 'remove_product');
//        $xcrud->button('#', 'Remove', 'glyphicon glyphicon-trash', 'xcrud-action btn-danger', array(
//            'data-task' => 'action',
//            'data-action' => 'delete',
//            'data-primary' => '{product_id}')
//        );
        
        $data['content'] = $xcrud->render();
        $data["categories"] = $this->m_tools->get_category();   
        $data["suppliers"] = $this->m_product->get_supplier_list();    
        $data["sellers"] = $this->m_product->get_seller_list(); 
        $data["countries"] = $this->m_tools->get_country();          
        $this->load->view('header', $data);
        $this->load->view('product', $data);
        $this->load->view('footer');
    }
    
    public function archive_product() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('products');   
        $xcrud->where("status", 0);
        $xcrud->relation('brand_id', 'brand_mst', 'brand_id', 'brand_name');
        $xcrud->relation('category_id', 'category_mst', 'category_id', 'category_name');
        $xcrud->relation('supplier_id', 'suppliers', 'supplier_id', 'supplier_name');
        $xcrud->subselect('sellerid', '{seller_id}');
        $xcrud->relation('seller_id', 'seller', 'seller_id', 'seller_name');
	
	$xcrud->subselect('feature_img_view', '{feature_img}');
	$xcrud->subselect('price_product_id', '{product_id}');
	$xcrud->subselect('actual_product_id', '{product_id}');
	$xcrud->subselect('volume_product_id', '{product_id}');
	$xcrud->subselect('discount_product_id', '{product_id}');
	$xcrud->subselect('select_multi', '{product_id}');
	$xcrud->set_var('s3_path', S3_PATH.'product/');
	
        $xcrud->column_callback('sellerid', 'seller_label');
        $xcrud->column_callback('product_id', 'top_pick');	
	$xcrud->column_callback('select_multi', 'select_multiple');
        $xcrud->column_callback('feature_img', 'list_img_function');	
	$xcrud->column_callback('feature_img_view', 'view_img_function');
	$xcrud->column_callback('price_product_id', 'view_product_price');
	$xcrud->column_callback('actual_product_id', 'view_product_actual_price');
	$xcrud->column_callback('volume_product_id', 'view_product_volume');
	$xcrud->column_callback('discount_product_id', 'view_product_discount');
	//$xcrud->column_callback('category_id', 'category_callback');
	
        $xcrud->search_columns('category_id, brand_id, product_name, supplier_id, seller_id');                
//        $xcrud->columns('select_multi, feature_img, category_id, brand_id, product_name, supplier_id, sellerid, volume_product_id, actual_product_id, discount_product_id, price_product_id, product_id, in_loyalty_club, in_vip_club, status', false);
//	$xcrud->fields('feature_img_view, category_id, brand_id, product_name, supplier_id, sellerid, description, currency, top_pick, drink_type, abv_percent, alchol_units, country_id, have_return_policy, no_of_return_days, in_loyalty_club, in_vip_club', false, 'Product Info', 'view');
        
        $xcrud->columns('select_multi, feature_img, category_id, brand_id, product_name, supplier_id, sellerid, volume_product_id, actual_product_id, discount_product_id, price_product_id, product_id, status', false);
	$xcrud->fields('feature_img_view, category_id, brand_id, product_name, supplier_id, sellerid, description, currency, top_pick, drink_type, abv_percent, alchol_units, country_id, have_return_policy, no_of_return_days', false, 'Product Info', 'view');
	
        $xcrud->change_type('product_id', 'bool');
//        $xcrud->change_type('in_loyalty_club', 'bool');
//        $xcrud->change_type('in_vip_club', 'bool');
	$xcrud->change_type('top_pick', 'bool');
	$xcrud->change_type('have_return_policy', 'bool');
	$xcrud->change_type('drink_type', 'select', '', array('1' => 'Alcoholic', '2' => 'Non-Alcoholic'));
	$xcrud->change_type('status', 'select', '', array('1' => 'Active', '0' => 'Inactive'));
	$xcrud->label(array(
            'feature_img' => 'Image',
	    'feature_img_view' => 'Image',
            'product_name' => 'Product',
            'category_id' => 'Category',
            'brand_id' => 'Brand',
            'supplier_id' => 'Supplier',
            'seller_id' => 'Seller',
            'sellerid' => 'Seller',
            'product_id' => 'Top Pick',
	    'country_id' => 'Country',
	    'actual_product_id' => 'Original Price',
	    'price_product_id' => 'Price',
	    'volume_product_id' => 'Volume',
	    'discount_product_id' => 'Discount',
	    'select_multi' => ""
        ));
	
	//Product Description
	$pdesc = $xcrud->nested_table('Product Details','product_id','product_details','product_id');
	$pdesc->column_callback('volume_id', 'view_volumne');	
        $pdesc->columns('volume_id, actual_price, units, min_stock_limit, max_stock_limit, pack_size', false);
//	$pdesc->fields('volume_id, actual_price, normal_discount, normal_sell_price, loyalty_club_discount, loyalty_club_sell_price, vip_club_discount, vip_club_sell_price, units, min_stock_limit, max_stock_limit, pack_size', false, false, 'view');
        
        $pdesc->fields('volume_id, actual_price, normal_discount, normal_sell_price, units, min_stock_limit, max_stock_limit, pack_size', false, false, 'view');
        $pdesc->hide_button('return');
	$pdesc->label(array(
            'volume_id' => 'Volume',
            'default_sell_price' => 'Default Price',  
            'min_sell_price' => 'Sell Price',            
            'min_stock_limit' => 'Min Stock Limit',            
            'max_stock_limit' => 'Max Stock Limit'
        ));
	
        //Gallery
        $gallery = $xcrud->nested_table('Gallery','product_id','product_images','product_id');
        $gallery->subselect('image_name_view', '{image_name}');
	$gallery->column_callback('image_name', 'list_img_function');
	$gallery->column_callback('image_name_view', 'view_img_function');
	$gallery->fields('image_name_view', false, false, 'view');
        $gallery->columns('image_name');
	$gallery->set_var('s3_path', S3_PATH.'product/');
        $gallery->label(array(
            'image_name' => '',
	    'image_name_view' => '',
        ));
        $gallery->hide_button('return');
        
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
               
        $xcrud->button('#', 'Edit', 'glyphicon glyphicon-edit', '', array(
            'data-toggle' => 'modal',
            'class' => 'btn btn-warning btn-sm edit_data',
            'data-target' => ".mdl_product",
            'data-primary' => '{product_id}'));
        
        $xcrud->unset_add();
        $xcrud->unset_edit();       
        $xcrud->unset_remove();
        
//        $xcrud->create_action('delete', 'remove_product');
//        $xcrud->button('#', 'Remove', 'glyphicon glyphicon-trash', 'xcrud-action btn-danger', array(
//            'data-task' => 'action',
//            'data-action' => 'delete',
//            'data-primary' => '{product_id}')
//        );
        
        $data['content'] = $xcrud->render();          
        $this->load->view('header', $data);
        $this->load->view('archive_product', $data);
        $this->load->view('footer');
    }
    
    public function update_price_all() {
	$count = count($_POST);	
	for($i=0; $i<$count; $i++) {	 
	    $params = array();	
	    parse_str($_POST[$i], $params);
	    $this->db->set($params)
		->where("product_detail_id", $params["product_detail_id"])
		->update("product_details");   
	}
	echo 1;
    }
    
    public function update_price() {
	$this->db->set($_POST)
		->where("product_detail_id", $_POST["product_detail_id"])
		->update("product_details");
	echo 1;
    }
    
    public function edit_all() {
	//print_r($_GET["pids"]); exit;
	$product_ids = base64_decode($_GET["pids"]);		
	$products = $this->db->select("pd.*, p.product_name, v.volumne_value", false)
		->join("products p", "p.product_id = pd.product_id")
		->join("volume_mst v", "v.volume_id = pd.volume_id", "left")
		->where("pd.product_id IN (".$product_ids.")")
		->get("product_details pd")->result_array();	
	
	$data["products"] = $products;
	$this->load->view('header');
        $this->load->view('edit_price', $data);
        $this->load->view('footer');
    }
    
    public function active_all(){
	$post = $_POST;
	$this->db->set("status", 1)->where("product_id IN (".$post['pids'].")")->update("products");
	$this->db->set("status", 1)->where("product_id IN (".$post['pids'].")")->update("product_details");
	$this->db->set("status", 1)->where("product_id IN (".$post['pids'].")")->update("product_images");
	echo 1;
    }
    
    public function deactive_all(){
	$post = $_POST;
	$this->db->set("status", 0)->where("product_id IN (".$post['pids'].")")->update("products");
	echo 1;
    }    

    public function get_product() {
        $post = $_POST;
        $product = $this->m_product->get_product_details($post);
        //print_r($product); exit;
        if ($product) {
            echo json_encode($product);
        }
    }
    
    public function get_brand() {
        $post = $_POST;
        $brand = $this->m_product->get_product_brand($post["category"]);  
        //print_r($brand); exit;
        if ($brand) {
            echo json_encode($brand);
        }
    }
    
    public function get_sub_category() {
        $post = $_POST;
        $brand = $this->m_tools->get_sub_category($post["category"]);  
        //print_r($brand); exit;
        if ($brand) {
            echo json_encode($brand);
        }
    }
    
    public function get_volume() {
        $post = $_POST;  
        //print_r($post); exit;
        $volume = $this->m_product->get_product_volume($post["brand"]);  
        
        if ($volume) {
            echo json_encode($volume);
        } else {
            return false;
        }
    }
    
    public function save() {                
        $post = $_POST;  
        $files = $_FILES;  
        //print_r($post); 
        
        if($post["count_vol_div"] > "0" ){ 
	    
	    ///bucket info
	    $credentials = new Aws\Credentials\Credentials(PUBLISHED_KEY, SECRET_KEY);
	    $s3 = new S3Client([        
		'region' => 'eu-west-2',
		'version' => '2006-03-01',
		//'debug' => true,
		"credentials" => $credentials
	    ]);
           
            //upload feature images
            if (isset($files['feature_img']['name']) && $files['feature_img']['name']) {
		$ext = '.' . pathinfo($files['feature_img']['name'], PATHINFO_EXTENSION);
		$filename = date('YmdHis') . rand() . strtolower($ext);
		$keyname = $filename;
		$filepath = $files['feature_img']['tmp_name'];

		$result = $s3->putObject(array(
		    'Bucket' => BUCKET_NAME,
		    'Key' => 'product/'.$keyname,
		    'SourceFile' => $filepath,
		    'ACL' => 'public-read',
		    'StorageClass' => 'STANDARD'
		));
		
		$result2 = $s3->putObject(array(
		    'Bucket' => BUCKET_NAME,
		    'Key' => 'product/thumbs/'.$keyname,
		    'SourceFile' => $filepath,
		    'ACL' => 'public-read',
		    'StorageClass' => 'STANDARD'
		));
		
		//print_r($result); 
		$post['feature_img'] = $filename;		
            }

            //Add product data 
            $last_inserted_id = $this->m_product->add_product($post);  

            if(!empty($post['product_id'])){
                $pro_id = $post['product_id'];
            }else {
                $pro_id = $last_inserted_id;
            } 

            if($post["count_vol_div"] > 0 ){        
                //Add product volume data 
                $volume_insert = $this->m_product->add_product_volume($post, $pro_id); 
            } else {
                echo 'error'; exit;
            }

            //Insert Gallery Image
            if (!empty($files['pgallery']['name'][0])) {
		foreach ($files['pgallery']['name'] as $key => $file) {
		    $f = $files['pgallery']['name'][$key];
		    $ext = '.' . pathinfo($f, PATHINFO_EXTENSION);		
		    $filename = date('YmdHis') . rand() . strtolower($ext);
		    $keyname = $filename;
		    $filepath = $files['pgallery']['tmp_name'][$key];

		    $result = $s3->putObject(array(
			'Bucket' => BUCKET_NAME,
			'Key' => 'product/'.$keyname,
			'SourceFile' => $filepath,
			'ACL' => 'public-read',
			'StorageClass' => 'STANDARD'
		    ));
		    
		    $result2 = $s3->putObject(array(
			'Bucket' => BUCKET_NAME,
			'Key' => 'product/thumbs/'.$keyname,
			'SourceFile' => $filepath,
			'ACL' => 'public-read',
			'StorageClass' => 'STANDARD'
		    ));
		    
		    $data = [
                        'image_name' => $filename,
                        'product_id' => $pro_id
                    ];
                    $this->db->insert('product_images', $data);
	        }    
            } 
            echo 'success';
        
        } 
        else {
            echo 'error'; exit;
        }
    }
    
    public function top_pick(){
        $post = $_POST;  
        $this->m_product->add_top_picks($post);
    }
    
    public function import_xls(){        
        //print_r($_FILES); exit;        
        if (isset($_FILES['import_file_nm']['name']) && $_FILES['import_file_nm']['name'] && ($_FILES['import_file_nm']['size'] > 0)) {
            $filename = $_FILES['import_file_nm']['tmp_name']; 
	    $inputFileType = 'Xls';
        
	    $reader = IOFactory::createReader($inputFileType);
	    $spreadsheet = $reader->load($filename);
	    
	    $sheetdata = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
	    $sheetdata2 = array_slice($sheetdata, 1);
	    
	    foreach($sheetdata2 as $key => $data) { 
		
//		echo "<pre>"; print_r($data); exit;
		$test_check1 = $this->db->select("seller_id")->where("code", trim($data[5]))->get("seller")->row_array();
		$test_check2 = $this->db->select("supplier_id")->where("supplier_code", trim($data[6]))->get("suppliers")->row_array();
		$test_check3 = $this->db->select("brand_id")->where("brand_code", trim($data[10]))->get("brand_mst")->row_array();
		$test_check4 = $this->db->select("category_id")->where("category_code", trim($data[11]))->get("category_mst")->row_array();
		
		$test_check5 = $this->db->select("brand_category_allocation_id")
			->where("category_id", str_ireplace('C', '', trim($data[11])))
			->where("brand_id", str_ireplace('B', '', trim($data[10])))
			->get("brand_category_allocation")->row_array();
		
		$ext = pathinfo(trim($data[23]), PATHINFO_EXTENSION);

		if(empty(trim($data[0]))){
		    echo '1'; exit;
		} 
//                        else if(empty(trim($data[1]))){
//			    $ret[0] = '2';
//			    $ret[1] = $data[0];
//			    echo json_encode($ret); exit;
//                        }
		else if(!empty(trim($data[2])) && !is_numeric(trim($data[2]))){
		    $ret[0] = '3';
		    $ret[1] = $data[0];
		    echo json_encode($ret); exit;
		}
		else if(empty(trim($data[3]))){
		    $ret[0] = '4';
		    $ret[1] = $data[0];
		    echo json_encode($ret); exit;
		}
		else if(empty(trim($data[4]))){
		    $ret[0] = '5';
		    $ret[1] = $data[0];
		    echo json_encode($ret); exit;
		}
		else if(empty(trim($data[5]))){
		    $ret[0] = '6';
		    $ret[1] = $data[0];
		    echo json_encode($ret); exit;
		}			
		else if(empty(trim($data[6]))){
		    $ret[0] = '7';
		    $ret[1] = $data[0];
		    echo json_encode($ret); exit;
		}
		else if(empty(trim($data[7]))){
		    $ret[0] = '8';
		    $ret[1] = $data[0];
		    echo json_encode($ret); exit;
		}
		else if(empty(trim($data[8]))){
		    $ret[0] = '9';
		    $ret[1] = $data[0];
		    echo json_encode($ret); exit;
		}
		else if(empty(trim($data[9]))){
		    $ret[0] = '10';
		    $ret[1] = $data[0];
		    echo json_encode($ret); exit;
		}
		else if(empty(trim($data[10]))){
		    $ret[0] = '11';
		    $ret[1] = $data[0];
		    echo json_encode($ret); exit;
		}
		else if(empty(trim($data[11]))){
		    $ret[0] = '12';
		    $ret[1] = $data[0];
		    echo json_encode($ret); exit;
		}
		else if(empty(trim($data[12]))){
		    $ret[0] = '13';
		    $ret[1] = $data[0];
		    echo json_encode($ret); exit;
		}
		else if(empty(trim($data[16]))){
		    $ret[0] = '14';
		    $ret[1] = $data[0];
		    echo json_encode($ret); exit;
		}
		else if(empty(trim($data[23]))){
		    $ret[0] = '15';
		    $ret[1] = $data[0];
		    echo json_encode($ret); exit;
		}
		elseif(!empty(trim($data[12])) && !is_numeric($data[12])){
		    $ret[0] = '16';
		    $ret[1] = $data[0];
		    echo json_encode($ret); exit;
		}
		else if(!empty(trim($data[23])) && empty($ext)){ 		    
		    $ret[0] = '17';
		    $ret[1] = $data[0];
		    echo json_encode($ret); exit;			    
		}
		else if(!empty(trim($data[11])) && empty($test_check4)){
		    $ret[0] = '18';
		    $ret[1] = $data[0];
		    echo json_encode($ret); exit;
		}
		else if(!empty(trim($data[10])) && empty($test_check3)){
		    $ret[0] = '19';
		    $ret[1] = $data[0];
		    echo json_encode($ret); exit;
		}
		else if(!empty(trim($data[5])) && empty($test_check1)){
		    $ret[0] = '20';
		    $ret[1] = $data[0];
		    echo json_encode($ret); exit;			    
		}
		else if(!empty(trim($data[6])) && empty($test_check2)){
		    $ret[0] = '21';
		    $ret[1] = $data[0];
		    echo json_encode($ret); exit;
		}
		else if(empty($test_check5)){
		    $ret[0] = '22';
		    $ret[1] = $data[0];
		    $ret[2] = $data[11];
		    echo json_encode($ret); exit;
		}
		else {   
		    $seller_id = str_ireplace('SL', '', trim($data[5]));

		    //check exists
		    $exists = $this->db->select('product_id, brand_id')
//				    ->where('product_id', $data[0])
			    ->where('product_name',trim($data[0]))
			    ->where('seller_id', $seller_id)
			    ->get('products')->row_array(); 
		    //print_r($exists); exit;

		    if(!empty($data[24])) {
			$count = trim($data[24]);
			if($count == 'USA' || $count == 'US' || $count == 'United States') {
			    $count = 'USA';
			}

			if($count == 'United Kingdom' || $count == 'UK') {
			    $count = 'UK';
			}

			$conid = $this->db->select("country_id")
				->where("name like '%".$count."%'")
				->get("country")->row_array();
			if(!empty($conid)) {
			    $country_id = $conid["country_id"];
			}
			else {
			    $this->db->insert("country", array(
				"name" => $count
			    ));
			    $country_id = $this->db->insert_id();
			}
		    }

		    $brand_id = str_ireplace('B', '', trim($data[10]));
		    $category_id = str_ireplace('C', '', trim($data[11]));
		    $supplier_id = str_ireplace('S', '', trim($data[6]));


		    $desc = str_replace(PHP_EOL,"@/@", $data[1]);
		    $desc1 = json_decode('"'.$desc.'"');
		    $desc1 = str_replace("@/@",PHP_EOL, $desc1);

		    if (empty($exists)) {
			//Insert Product
			$product_data = array(
			    'product_name' => trim($data[0]),
			    'description' => $desc1, 
			    'brand_id' => $brand_id,                        
			    'category_id' => $category_id,
			    'supplier_id' => $supplier_id,  
			    'drink_type' => trim($data[12]),    
			    'abv_percent' => trim($data[13]),
			    'alchol_units' => trim($data[14]),
			    'no_of_return_days' => trim($data[15]),
			    'seller_id' => $seller_id,
			    'feature_img' => trim($data[23]),
			    'country_id' => trim($data[24]),
			);

			$this->db->insert('products', $product_data);
			$proid = $this->db->insert_id();                        
		    } else {

			//Update Product
			$product_data1 = array(
			    'description' => $desc1, 
			    'drink_type' => trim($data[12]),    
			    'abv_percent' => trim($data[13]),
			    'alchol_units' => trim($data[14]),
			    'no_of_return_days' => trim($data[15]),
			    'seller_id' => $seller_id,
			    'feature_img' => trim($data[23]),
			    'country_id' => trim($data[24]),
			    'brand_id' => $brand_id,                        
			    'category_id' => $category_id,
			    'supplier_id' => $supplier_id,  
			);

			$this->db->set($product_data1)
				->where("product_id", $exists["product_id"])
				->update('products');

			$proid = $exists["product_id"];
		    }

		    $seller = $this->db->select("seller_id, code")->where("seller_id", $seller_id)->get("seller")->row_array();

		    if(!empty($data[25])) {
			//get gallery_imgs
			for($i=25; $i<=28; $i++) {				    
			    if(strpos($data[$i], "_") !== false) {
				$img = explode("_", $data[$i]);

				if($img[0] == $seller["code"]) {				    
				    //check exists
				    $gallexists = $this->db->select('pimg_id')
					->where('product_id', $proid) 
					->where('image_name', $data[$i]) 
					->get('product_images')->row_array(); 

				    if(empty($gallexists)) {
					$this->db->insert('product_images', array(
					    'product_id' => $proid,
					    'image_name' => $data[$i],
					));
				    }
				}
			    }
			}
		    }

		    //get volume
		    $volume = $this->db->select('volume_id')
			->where('brand_id', $brand_id)
			->where('volumne_value', trim($data[8]))
			->get('volume_mst')->row_array();


		    //get volume type
		    $where_vol = "volume_type LIKE '".trim($data[9])."'";
		    $volume_type = $this->db->select('*')
			    ->where($where_vol)
			->get('volume_type')->row_array();

		    if(empty($volume_type)){
			// Insert into volume master 
			$newvoldata1 = array(
			    'volume_type' => trim($data[9]),
			);

			$this->db->insert('volume_type', $newvoldata1);
			$type_value = $this->db->insert_id();   
		    } else {
			$type_value = $volume_type["volume_type_id"];
		    }

		    if(empty($volume)){
			// Insert into volume master 
			$newvoldata = array(
			    'brand_id' => $brand_id,
			    'volumne_value' => trim($data[8]),
			    'type' => $type_value
			);

			$this->db->insert('volume_mst', $newvoldata);
			$volid = $this->db->insert_id();                       

		    } else {
			$volid = $volume["volume_id"];
		    }

		    //check exists
		    $volexists = $this->db->select('volume_id')
			->where('volume_id', $volid)
			->where('product_id', $proid)                             
			->get('product_details')->row_array(); 

		    if(empty($volexists)) {
			//Insert Volume
			$product_vol_data = array(
			    'volume_id' => $volid,
			    'product_id' => $proid,   
			    'units' => trim($data[2]),
			    'min_stock_limit' => trim($data[3]),                        
			    'max_stock_limit' => trim($data[4]),  
			    'pack_size' => trim($data[7]),     
			    'actual_price' => trim($data[16]),
			    'normal_sell_price' => trim($data[17]),
			    'normal_discount' => trim($data[18]),
			    'loyalty_club_sell_price' => trim($data[19]),
			    'loyalty_club_discount' => trim($data[20]),
			    'vip_club_sell_price' => trim($data[21]),
			    'vip_club_discount' => trim($data[22])
			);

			$this->db->insert('product_details', $product_vol_data);  
		    }
		    else {
			//Insert Volume
			$product_vol_data1 = array(
			    'volume_id' => $volid,
			    'units' => trim($data[2]),
			    'min_stock_limit' => trim($data[3]),                        
			    'max_stock_limit' => trim($data[4]),  
			    'pack_size' => trim($data[7]),     
			    'actual_price' => trim($data[16]),
			    'normal_sell_price' => trim($data[17]),
			    'normal_discount' => trim($data[18]),
			    'loyalty_club_sell_price' => trim($data[19]),
			    'loyalty_club_discount' => trim($data[20]),
			    'vip_club_sell_price' => trim($data[21]),
			    'vip_club_discount' => trim($data[22])
			);

			$this->db->set($product_vol_data1)
				->where('volume_id', $volid)
				->where('product_id', $proid)  
				->update('product_details');  
		    }
		} 			
	    }
		
	    echo 'success';
        }
    }
    
    public function export_xls() {
//	$this->m_product->export_products();
	$helper = new Sample();
	if ($helper->isCli()) {
	    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
	    return;
	}
	// Create new Spreadsheet object
	$spreadsheet = new Spreadsheet();

	// Set document properties
	$spreadsheet->getProperties()->setCreator('Janet-Collection')
	    ->setLastModifiedBy('Janet-Collection')
	    ->setTitle('Product List')
	    ->setSubject('Product List')
	    ->setDescription('Product List')    
	    ->setCategory('Product List');
	
	$products = $this->db->select('p.product_id, p.product_name as "Product Name*", p.description as "Product Description*", pd.units as "Actual Stock Units*", pd.min_stock_limit as "Min Stock Alert Level*", pd.max_stock_limit as "Max Stock Alert Level*", s.code as "Seller Code*", sp.supplier_code as "Supplier Code*", pd.pack_size as "Pack Size*", v.volumne_value as Volume, (select volume_type from volume_type where volume_type_id = v.type) as "Volume Type*", b.brand_code as "Brand Code*", c.category_code as "Category Code*", p.drink_type as "Drink Type (1 = Alcoholic, 2 = Non – Alcoholic)*" , p.abv_percent "ABV Percentage*", p.alchol_units as "Alcohol Units*", p.no_of_return_days as "No of Days To Return*", pd.actual_price as "Actual Price (MRP)*", pd.normal_sell_price as "Sell Price*", pd.normal_discount as "Discount*", pd.loyalty_club_sell_price as "Loyalty Club Sell Price*", pd.loyalty_club_discount as "Loyalty Club Discount*", pd.vip_club_sell_price as "VIP Club Sell Price*", pd.vip_club_discount as "VIP Club Discount*", p.feature_img as "Product Image*", p.country_id as "Country*"', false)
		    ->join("product_details pd", "p.product_id = pd.product_id", "left")
		    ->join("seller s", "p.seller_id = s.seller_id", "left")
		    ->join("suppliers sp", "p.supplier_id = sp.supplier_id", "left")
		    ->join("volume_mst v", "v.volume_id = pd.volume_id", "left")
		    ->join("brand_mst b", "p.brand_id = b.brand_id", "left")
		    ->join("category_mst c", "p.category_id = c.category_id", "left")
		    ->where("p.status", 1)
		    ->order_by("p.product_name", "asc")
		    ->get('products p')->result_array();
	
	foreach($products as $k1 => $v) {	
	    $new_arr = array();
	    //get gallery images
	    $gimgs = $this->db->select("*")
		    ->where("product_id", $v["product_id"])
		    ->get("product_images")->result_array();
	    
	    if(!empty($gimgs)) {		
		if(!empty($gimgs[0])) {
		    $new_arr["Gallery Img1"] = $gimgs[0]["image_name"];
		}
		else {
		    $new_arr["Gallery Img1"] = "";
		}
		
		if(!empty($gimgs[1])) {
		    $new_arr["Gallery Img2"] = $gimgs[1]["image_name"];
		}
		else {
		    $new_arr["Gallery Img3"] = "";
		}
		
		if(!empty($gimgs[2])) {
		    $new_arr["Gallery Img3"] = $gimgs[2]["image_name"];
		}
		else {
		    $new_arr["Gallery Img3"] = "";
		}
		
		if(!empty($gimgs[3])) {
		    $new_arr["Gallery Img4"] = $gimgs[3]["image_name"];
		}
		else {
		    $new_arr["Gallery Img4"] = "";
		}
	    }
	    else {
		$new_arr["Gallery Img1"] = "";
		$new_arr["Gallery Img2"] = "";
		$new_arr["Gallery Img3"] = "";
		$new_arr["Gallery Img4"] = "";
	    }	    
	    $products[$k1] = array_merge($products[$k1], $new_arr);
	}	

	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);	
	$spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);	
	$spreadsheet->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);	
	$spreadsheet->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('AA')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('AB')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('AC')->setAutoSize(true);
	
	$columns = array_keys($products[0]);
	$columns = array_slice($columns, 1);
	$i=0;
	foreach($columns as $k => $rows) {
	    $i++;
	    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($i, 1, $rows);
	}
	
	$row = 2; // 1-based index
	foreach($products as $row_data) {
	    $row_data = array_slice($row_data, 1);
	    $col = 1;
	    foreach($row_data as $key=>$value) {
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
		$col++;
	    }
	    $row++;
	}
	
	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('Product List');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="products_' . date("Y_m_d_H_i") . '.xls"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	//header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
	header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header('Pragma: public'); // HTTP/1.0

	$writer = IOFactory::createWriter($spreadsheet, 'Xls');
	$writer->save('php://output');	

    }
    
    public function export_xls_archive() {
//	$this->m_product->export_products_archive();
	$helper = new Sample();
	if ($helper->isCli()) {
	    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
	    return;
	}
	// Create new Spreadsheet object
	$spreadsheet = new Spreadsheet();

	// Set document properties
	$spreadsheet->getProperties()->setCreator('Janet-Collection')
	    ->setLastModifiedBy('Janet-Collection')
	    ->setTitle('Product List')
	    ->setSubject('Product List')
	    ->setDescription('Product List')    
	    ->setCategory('Product List');
	
	$products = $this->db->select('p.product_id, p.product_name as "Product Name*", p.description as "Product Description*", pd.units as "Actual Stock Units*", pd.min_stock_limit as "Min Stock Alert Level*", pd.max_stock_limit as "Max Stock Alert Level*", s.code as "Seller Code*", sp.supplier_code as "Supplier Code*", pd.pack_size as "Pack Size*", v.volumne_value as Volume, (select volume_type from volume_type where volume_type_id = v.type) as "Volume Type*", b.brand_code as "Brand Code*", c.category_code as "Category Code*", p.drink_type as "Drink Type (1 = Alcoholic, 2 = Non – Alcoholic)*" , p.abv_percent "ABV Percentage*", p.alchol_units as "Alcohol Units*", p.no_of_return_days as "No of Days To Return*", pd.actual_price as "Actual Price (MRP)*", pd.normal_sell_price as "Sell Price*", pd.normal_discount as "Discount*", pd.loyalty_club_sell_price as "Loyalty Club Sell Price*", pd.loyalty_club_discount as "Loyalty Club Discount*", pd.vip_club_sell_price as "VIP Club Sell Price*", pd.vip_club_discount as "VIP Club Discount*", p.feature_img as "Product Image*", p.country_id as "Country*"', false)
		    ->join("product_details pd", "p.product_id = pd.product_id", "left")
		    ->join("seller s", "p.seller_id = s.seller_id", "left")
		    ->join("suppliers sp", "p.supplier_id = sp.supplier_id", "left")
		    ->join("volume_mst v", "v.volume_id = pd.volume_id", "left")
		    ->join("brand_mst b", "p.brand_id = b.brand_id", "left")
		    ->join("category_mst c", "p.category_id = c.category_id", "left")
		    ->where("p.status", 0)
		    ->order_by("p.product_name", "asc")
		    ->get('products p')->result_array(); 
	
	foreach($products as $k1 => $v) {	
	    $new_arr = array();
	    //get gallery images
	    $gimgs = $this->db->select("*")
		    ->where("product_id", $v["product_id"])
		    ->get("product_images")->result_array();
	    
	    if(!empty($gimgs)) {		
		if(!empty($gimgs[0])) {
		    $new_arr["Gallery Img1"] = $gimgs[0]["image_name"];
		}
		else {
		    $new_arr["Gallery Img1"] = "";
		}
		
		if(!empty($gimgs[1])) {
		    $new_arr["Gallery Img2"] = $gimgs[1]["image_name"];
		}
		else {
		    $new_arr["Gallery Img3"] = "";
		}
		
		if(!empty($gimgs[2])) {
		    $new_arr["Gallery Img3"] = $gimgs[2]["image_name"];
		}
		else {
		    $new_arr["Gallery Img3"] = "";
		}
		
		if(!empty($gimgs[3])) {
		    $new_arr["Gallery Img4"] = $gimgs[3]["image_name"];
		}
		else {
		    $new_arr["Gallery Img4"] = "";
		}
	    }
	    else {
		$new_arr["Gallery Img1"] = "";
		$new_arr["Gallery Img2"] = "";
		$new_arr["Gallery Img3"] = "";
		$new_arr["Gallery Img4"] = "";
	    }	    
	    $products[$k1] = array_merge($products[$k1], $new_arr);
	}	

	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);	
	$spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);	
	$spreadsheet->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);	
	$spreadsheet->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('AA')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('AB')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('AC')->setAutoSize(true);
	
	$columns = array_keys($products[0]);
	$columns = array_slice($columns, 1);
	$i=0;
	foreach($columns as $k => $rows) {
	    $i++;
	    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($i, 1, $rows);
	}
	
	$row = 2; // 1-based index
	foreach($products as $row_data) {
	    $row_data = array_slice($row_data, 1);
	    $col = 1;
	    foreach($row_data as $key=>$value) {
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
		$col++;
	    }
	    $row++;
	}
	
	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('Archive Product List');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="archive_products_' . date("Y_m_d_H_i") . '.xls"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	//header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
	header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header('Pragma: public'); // HTTP/1.0

	$writer = IOFactory::createWriter($spreadsheet, 'Xls');
	$writer->save('php://output');	
    }
    
    public function upload_bulk() {
	$files = $_FILES;
	//print_r($files); exit;
	
	$return = [];
	///bucket info
	$credentials = new Aws\Credentials\Credentials(PUBLISHED_KEY, SECRET_KEY);
	$s3 = new S3Client([        
	    'region' => 'eu-west-2',
	    'version' => '2006-03-01',
	    //'debug' => true,
	    "credentials" => $credentials
	]);
	    
	if (!empty($files['import_image_nm']['name'][0])) {	    
	    foreach ($files['import_image_nm']['name'] as $key => $file) {		
		if (isset($files['import_image_nm']['name'][$key]) && $files['import_image_nm']['name'][$key] != '') {
                    $f = $files['import_image_nm']['name'][$key];
		    $keyname = $f;
		    $filepath = $files['import_image_nm']['tmp_name'][$key];
		    
		    $return = $s3->putObject(array(
			'Bucket' => BUCKET_NAME,
			'Key' => 'product/'.$keyname,
			'SourceFile' => $filepath,
			'ACL' => 'public-read',
			'StorageClass' => 'STANDARD'
		    ));
		    
		    $return = $s3->putObject(array(
			'Bucket' => BUCKET_NAME,
			'Key' => 'product/thumbs/'.$keyname,
			'SourceFile' => $filepath,
			'ACL' => 'public-read',
			'StorageClass' => 'STANDARD'
		    ));
                    
                }		
	   }	
	} 
	echo 'success';
    }
    
    public function delete_product_img() {
        $post = $_POST;
        $product = $this->m_product->delete_product_img($post);
        echo $product; exit;
    }
}

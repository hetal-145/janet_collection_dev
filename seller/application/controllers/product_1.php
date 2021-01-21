<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require '../vendor_aws/autoload.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

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
        $user_id = $this->session->userdata('user_id');
        $xcrud = Xcrud::get_instance();
        $xcrud->table('products');   
        $xcrud->where('seller_id', $user_id);
        $xcrud->relation('brand_id', 'brand_mst', 'brand_id', 'brand_name');
        $xcrud->relation('category_id', 'category_mst', 'category_id', 'category_name');
        $xcrud->relation('supplier_id', 'suppliers', 'supplier_id', 'supplier_name');
        $xcrud->relation('seller_id', 'seller', 'seller_id', 'seller_name');
	
	$xcrud->subselect('feature_img_view', '{feature_img}');
	$xcrud->subselect('price_product_id', '{product_id}');
	$xcrud->subselect('actual_product_id', '{product_id}');
	$xcrud->subselect('volume_product_id', '{product_id}');
	$xcrud->subselect('discount_product_id', '{product_id}');
	$xcrud->subselect('select_multi', '{product_id}');
	$xcrud->set_var('s3_path', S3_PATH.'product/');
        
        //$xcrud->column_callback('product_id', 'top_pick');
	$xcrud->column_callback('select_multi', 'select_multiple');
        $xcrud->column_callback('feature_img', 'list_img_function');	
	$xcrud->column_callback('feature_img_view', 'view_img_function');
        //$xcrud->column_callback('category_id', 'category_callback');
	$xcrud->column_callback('price_product_id', 'view_product_price');
	$xcrud->column_callback('actual_product_id', 'view_product_actual_price');
	$xcrud->column_callback('volume_product_id', 'view_product_volume');
	$xcrud->column_callback('discount_product_id', 'view_product_discount');
	
        $xcrud->search_columns('category_id, brand_id, product_name');                
        $xcrud->columns('select_multi, feature_img, category_id, brand_id, product_name, supplier_id, seller_id, volume_product_id, actual_product_id, discount_product_id, price_product_id, in_loyalty_club, in_vip_club', false);
        $xcrud->fields('feature_img_view, category_id, brand_id, product_name, supplier_id, seller_id', false, 'Product Info', 'view');
        $xcrud->change_type('product_id', 'bool');
        $xcrud->change_type('in_loyalty_club', 'bool');
        $xcrud->change_type('in_vip_club', 'bool');
	$xcrud->label(array(
            'feature_img' => 'Image',
	    'feature_img_view' => 'Image',
            'product_name' => 'Product',
            'volume_id' => 'Volume',
            'default_sell_price' => 'Default Price',  
            'min_sell_price' => 'Sell Price',            
            'min_stock_limit' => 'Min Stock Limit',            
            'max_stock_limit' => 'Max Stock Limit',            
            'category_id' => 'Category / SubCategory',
            'brand_id' => 'Brand',
            'supplier_id' => 'Supplier',
            'seller_id' => 'Seller',
            'product_id' => 'Top Pick',
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
	$pdesc->fields('volume_id, actual_price, normal_discount, normal_sell_price, loyalty_club_discount, loyalty_club_sell_price, vip_club_discount, vip_club_sell_price, units, min_stock_limit, max_stock_limit, pack_size', false, false, 'view');
        $pdesc->hide_button('return');
	$pdesc->label(array(
            'volume_id' => 'Volume',
            'default_sell_price' => 'Default Price',  
            'min_sell_price' => 'Sell Price',            
            'min_stock_limit' => 'Min Stock Limit',            
            'max_stock_limit' => 'Max Stock Limit'
        ));
	
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
        
        $xcrud->create_action('inactive', 'inactive_product');        
        $xcrud->button('#', 'Inactive', 'glyphicon glyphicon-ban-circle', 'xcrud-action btn-danger', array(
            'data-task' => 'action',
            'data-action' => 'inactive',
            'data-primary' => '{product_id}'), array(
            'status',
            '=',
            '1')
        );
	
	$xcrud->create_action('active', 'active_product');
        $xcrud->button('#', 'Active', 'glyphicon glyphicon-ok', 'xcrud-action btn-success', array(
            'data-task' => 'action',
            'data-action' => 'active',
            'data-primary' => '{product_id}'), array(
            'status',
            '!=',
            '1'));       
        $xcrud->create_action('active', 'active_product');      
        $xcrud->button('#', 'Edit', 'glyphicon glyphicon-edit', '', array(
            'data-toggle' => 'modal',
            'class' => 'btn btn-warning btn-sm edit_data',
            'data-target' => ".mdl_product",
            'data-primary' => '{product_id}'));
        
        $xcrud->unset_add();
        $xcrud->unset_edit();       
        $xcrud->unset_remove();
        
        $data['content'] = $xcrud->render();
        $data["categories"] = $this->m_tools->get_category();   
        $data["suppliers"] = $this->m_product->get_supplier_list();   
        $data["sellers"] = $this->m_product->get_seller_list();  
        $data["countries"] = $this->m_tools->get_country(); 
        $this->load->view('header', $data);
        $this->load->view('product', $data);
        $this->load->view('footer');
    }
    
    public function update_price_all() {
//	print_r($_POST); exit;
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
//	print_r($_POST); exit;
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
        if ($product) {
            echo json_encode($product);
        }
    }
    
    public function get_brand() {
        $post = $_POST;
        $brand = $this->m_product->get_product_brand($post["category"]);  
        if ($brand) {
            echo json_encode($brand);
        }
    }
    
    public function get_sub_category() {
        $post = $_POST;
        $brand = $this->m_tools->get_sub_category($post["category"]); 
        if ($brand) {
            echo json_encode($brand);
        }
    }
    
    public function get_volume() {
        $post = $_POST;  
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
        
        if($post["count_vol_div"] > 0 ){ 
	    
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
        
        } else {
            echo 'error'; exit;
        }
    }
    
    public function top_pick(){
        $post = $_POST;  
        $this->m_product->add_top_picks($post);
    }
    
    public function brand() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('brand_mst'); 
        $xcrud->validation_required('brand_name', 2);
        
       $xcrud->subselect('category', "select c1.category_code  from brand_category_allocation bc join category_mst c1 ON c1.category_id=bc.category_id WHERE bc.brand_id={brand_id} group by brand_id");
       $xcrud->subselect('category_id', "select c1.category_name  from brand_category_allocation bc join category_mst c1 ON c1.category_id=bc.category_id WHERE bc.brand_id={brand_id} group by brand_id");
       
        $xcrud->subselect('top_brand_id','{brand_id}');
        $xcrud->column_callback('top_brand_id', 'is_top_brand'); 
        $xcrud->change_type('in_loyalty_club', 'bool');
	
	$xcrud->subselect('brand_logo_view', '{brand_logo}');
	$xcrud->subselect('slider_img_view', '{slider_img}');
	$xcrud->set_var('s3_path', S3_PATH.'brand/');
	
        $xcrud->column_callback('brand_logo', 'list_img_function');	
	$xcrud->column_callback('brand_logo_view', 'view_img_function');
	$xcrud->column_callback('slider_img', 'list_img_function');	
	$xcrud->column_callback('slider_img_view', 'view_img_function');
        
        $xcrud->search_columns('brand_code,category, brand_name');              
        $xcrud->columns('category_id, category, brand_logo, brand_code, brand_name, in_loyalty_club, top_brand_id');                
        $xcrud->fields('category_id, category, brand_logo_view, brand_code, brand_name, is_top_brand, slider_img_view, in_loyalty_club');               
        $xcrud->label(array(
            'category_id' => 'Category / Subcategory Name',
	    'category' => 'Category / Subcategory Code',
            'brand_logo' => 'Logo',
	    'brand_logo_view' => 'Logo',
            'brand_code' => 'Code',
            'brand_name' => 'Brand',
            'is_top_brand' => 'Top Brand',
            'slider_img' => 'Slider Image',
	    'slider_img_view' => 'Slider Image',
            'top_brand_id' => 'Top Brand?',
            'in_loyalty_club' => 'In Loyalty Club?',
        ));
        
        $xcrud->change_type('is_top_brand', 'bool');
        
        $xcrud->unset_add();
        $xcrud->unset_edit();
        $xcrud->unset_view();
        $xcrud->unset_remove();
        
        $data['categories'] = $this->m_tools->get_category();
        $data['volumes'] = $this->m_tools->get_volume_type_list();
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('brand', $data);
        $this->load->view('footer');
    }    
    
    public function category() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('category_mst'); 
        $table_where = "(parent_id=0 or parent_id=category_id)";
        $xcrud->where($table_where);
        
        $xcrud->column_callback('category_id', 'subcategory_callback'); 
	$xcrud->subselect('category_img_view', '{category_img}');
	$xcrud->set_var('s3_path', S3_PATH.'category/');
	$xcrud->column_callback('category_img', 'list_img_function');	
	$xcrud->column_callback('category_img_view', 'view_img_function');
        $xcrud->columns('category_img, category_code, category_name, category_id, in_loyalty_club'); 
        $xcrud->change_type('in_loyalty_club', 'bool');
        $xcrud->column_name('in_loyalty_club' , 'In Loyalty Club?');
        $xcrud->search_columns('category_code,category_name', 'category_name');  
        
        $xcrud->label(array(
            'category_img' => 'Image',
	    'category_img_view' => 'Image',
            'category_id' => 'Sub Category',
        ));
        
        $xcrud->unset_add();
        $xcrud->unset_edit();
        $xcrud->unset_view();
        $xcrud->unset_remove();
                    
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('category', $data);
        $this->load->view('footer');
    }    
    
    public function export_xls() {
	$user_id = $this->session->userdata('user_id');
	$this->m_product->get_products_by_seller($user_id);
    }
    
    public function import_xls(){        
        //print_r($_FILES); exit;
        $user_id = $this->session->userdata('user_id');
        if (isset($_FILES['import_file_nm']['name']) && $_FILES['import_file_nm']['name']) {
            $filename = $_FILES['import_file_nm']['tmp_name'];            
        } 
        
        if($_FILES['import_file_nm']['size'] > 0 ) {            
            $row = 0;
            if (($handle = fopen($filename, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
                    if($row > 0){
                        $num = count($data);  
			
			//print_r($data); exit;

                        if(empty($data[0])){
                            echo '1'; exit;
                        }                         
                        elseif(empty($data[2])){
                            echo '7'; exit;
                        }
                        elseif(empty($data[1])){
                            echo '8'; exit;
                        }
                        elseif(empty($data[3])){
                            echo '13'; exit;
                        }
                        else {    
                        
                            //check exists
                            $exists = $this->db->select('product_id, brand_id')
                                ->where('product_name like "%'.trim($data[0]).'%"')
				->where('seller_id', $user_id)
                                ->get('products')->row_array(); 
			    
			    if(!empty($exists)) {			
				$brand_id = $exists["brand_id"];		
				$proid = $exists["product_id"];
				
				//get volume
				$volume = $this->db->select('volume_id')
				    ->where('brand_id', $brand_id)
				    ->where('volumne_value', $data[1])
				    ->get('volume_mst')->row_array(); 
				
				if(!empty($volume)) {				
				    $volid = $volume["volume_id"]; 

				    //check exists
				    $volexists = $this->db->select('product_detail_id')
					->where('product_id', $proid)
					->where('volume_id', $volid)
					->get('product_details')->row_array(); 

				    $product_vol_data = array(
					'volume_id' => $volid,
					'product_id' => $proid,   
					'pack_size' => $data[2],     
					'actual_price' => $data[3],
					'normal_discount' => $data[5],
					'normal_sell_price' => $data[4],
					'loyalty_club_discount' => $data[7],
					'loyalty_club_sell_price' => $data[6],
					'vip_club_discount' => $data[9],
					'vip_club_sell_price' => $data[8]
				    );

				    if(!empty($volexists)) {
					$this->db->set($product_vol_data)
						->where('volume_id', $volid)
						->where('product_id', $proid)
						->where('product_detail_id', $volexists["product_detail_id"])
						->update('product_details');                         
				    }				
				}
			    } 
			}
                    }
                    $row++;
            }
            fclose($handle);
            echo 'success';
            
          } 
        }
    }
    
    public function upload_bulk() {
	$files = $_FILES;
	//print_r($files); exit;
	$user_id = $this->session->userdata('user_id');	
	
	//seller
	$seller = $this->db->select("seller_id, code")->where("seller_id", $user_id)->get("seller")->row_array();	
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
		if(strpos($files['import_image_nm']['name'][$key], "_") !== false) {
		    $img = explode("_", $files['import_image_nm']['name'][$key]);
		    
		    if($img[0] == $seller["code"]) {	
			$f = $files['import_image_nm']['name'][$key];
			$ext = '.' . pathinfo($f, PATHINFO_EXTENSION);		
			//$filename = $f;
			$keyname = $f;
			$filepath = $files['import_image_nm']['tmp_name'][$key];

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
		    }
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

    public function brand_export_xls() {
//	$this->m_product->export_brands();
	
	require_once '../vendor_spreadsheet/phpoffice/phpspreadsheet/src/Bootstrap.php';
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
	    ->setTitle('Brand List')
	    ->setSubject('Brand List')
	    ->setDescription('Brand List')    
	    ->setCategory('Brand List');
	
	$products = $this->db->select('c.category_code as "Category / Subcategory Code", sc.brand_code as "Brand Code", sc.brand_name as "Brand Name",  sc.brand_logo as "Brand Logo", sc.slider_img as "Brand Slider Image"', false)
		    ->join("brand_category_allocation bc", "bc.brand_id = sc.brand_id")
		    ->join("category_mst c", "c.category_id = bc.category_id")
		    ->order_by("sc.brand_name", "asc")
		    ->get('brand_mst sc')->result_array();
		
//		print_r($products); exit;

	$spreadsheet->getActiveSheet()->setCellValue('A1', 'Category / Subcategory Code');
	$spreadsheet->getActiveSheet()->setCellValue('B1', 'Brand Code');
	$spreadsheet->getActiveSheet()->setCellValue('C1', 'Brand Name');
	$spreadsheet->getActiveSheet()->setCellValue('D1', 'Brand Logo');
	$spreadsheet->getActiveSheet()->setCellValue('E1', 'Brand Slider Image');

	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	
//	echo "<pre>"; print_r($products); exit;
	
	$row = 2; // 1-based index
	$col = 1;
	foreach($products as $row_data) {
	    $col = 1;
	    foreach($row_data as $key=>$value) {
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
		$col++;
	    }
	    $row++;
	}

	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('Brand List');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="brand_' . date("Y_m_d_H_i") . '.xlsx"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	//header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
	header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header('Pragma: public'); // HTTP/1.0

	$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
	$writer->save('php://output');	
    }
    
    public function brand_upload_bulk() {
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
			'Key' => 'brand/'.$keyname,
			'SourceFile' => $filepath,
			'ACL' => 'public-read',
			'StorageClass' => 'STANDARD'
		    ));
		    
		    $return = $s3->putObject(array(
			'Bucket' => BUCKET_NAME,
			'Key' => 'brand/thumbs/'.$keyname,
			'SourceFile' => $filepath,
			'ACL' => 'public-read',
			'StorageClass' => 'STANDARD'
		    ));
                    
                }		
	   }	
	} 
	echo 'success';
    }
    
    public function brand_import_xls(){        
        //print_r($_FILES); exit;        
        if (isset($_FILES['import_file_nm']['name']) && $_FILES['import_file_nm']['name'] && ($_FILES['import_file_nm']['size'] > 0)) {
            $filename = $_FILES['import_file_nm']['tmp_name']; 
	    $inputFileType = 'Xlsx';
        
	    $reader = IOFactory::createReader($inputFileType);
	    $spreadsheet = $reader->load($filename);
	    
	    $sheetdata = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
	    $sheetdata2 = array_slice($sheetdata, 1);
	    
	    foreach($sheetdata2 as $key => $data) {	    
		$test_check4 = $this->db->select("category_id")->where("category_code", trim($data[0]))->get("category_mst")->row_array();
		$ext = pathinfo(trim($data[3]), PATHINFO_EXTENSION);

		if(empty(trim($data[0]))){
		    $ret[0] = '2';
		    $ret[1] = $data[2];
		    echo json_encode($ret); exit;
		} 
		else if(empty(trim($data[2]))){
		    $ret[0] = '4';
		    $ret[1] = $data[0];
		    echo json_encode($ret); exit;
		}
    //                        else if(empty(trim($data[3]))){
    //			    $ret[0] = '5';
    //			    $ret[1] = $data[2];
    //			    echo json_encode($ret); exit;
    //                        }
    //			else if(!empty(trim($data[3])) && empty($ext)){ 		    
    //			    $ret[0] = '6';
    //			    $ret[1] = $data[2];
    //			    echo json_encode($ret); exit;			    
    //                        }
		else if(!empty(trim($data[0])) && empty($test_check4)){
		    $ret[0] = '7';
		    $ret[1] = $data[2];
		    echo json_encode($ret); exit;
		}
		else {   
		    //check exists
		    if(empty(trim($data[1]))){
			$exists = $this->db->select('brand_id, brand_code')
			    ->where('brand_name like "%'.$data[2].'%"')
			    ->get('brand_mst')->row_array(); 
		    }
		    else {
			$exists = $this->db->select('brand_id, brand_code')
			    ->where('brand_code', $data[1])
			    ->get('brand_mst')->row_array(); 
		    }

		    $category_id = str_ireplace('C', '', trim($data[0]));

		    if (empty($exists)) {				
			//Insert Brand
			$product_data = array(
			    'brand_code' => trim($data[1]),                       
			    'brand_name' => trim($data[2]),
			    'brand_logo' => trim($data[3]),
			    'slider_img' => trim($data[4])
			);

			$this->db->insert('brand_mst', $product_data);
			$brandid = $this->db->insert_id(); 

			//Insert in brand category allocation
			$product_data2 = array(
			    'brand_id' => $brandid,
			    'category_id' => $category_id
			);

			$this->db->insert('brand_category_allocation', $product_data2);

    //				if(empty(trim($data[1]))){
    //				    $this->db->set("brand_code", 'B'.$brandid)
    //					->where("brand_id", $brandid)
    //					->update('brand_mst');	
    //				}

		    } 
		    else {
			if(empty(trim($data[1]))){
			    echo '3'; exit;
			}

			//Update Brand
			$product_data = array(
			    'brand_name' => trim($data[2]),                      
			    'brand_logo' => trim($data[3]),
			    'slider_img' => trim($data[4])
			);

			$this->db->set($product_data)
				->where("brand_id", $exists["brand_id"])
				->update('brand_mst');	

			$brandid = $exists["brand_id"];

			//Insert in brand category allocation
			$product_data2 = array(
			    'brand_id' => $brandid,
			    'category_id' => $category_id
			);

			//check exists
			$exists2 = $this->db->select('brand_category_allocation_id')
			    ->where($product_data2)
			    ->get('brand_category_allocation')->row_array();

			if(empty($exists2)) {
			    $this->db->insert('brand_category_allocation', $product_data2);
			}
			else {
			    $this->db->set($product_data2)
				->where($product_data2)
				->update('brand_category_allocation');	
			}
		    }
		} 
	    }
	    echo 'success';
	}
    }
}

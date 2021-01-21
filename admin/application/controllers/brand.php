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

class Brand extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_login');
        $this->load->model('m_brand');
        $this->load->model('m_tools');
        $this->m_login->check_session();
    }

    public function index() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('brand_mst'); 
	$xcrud->unset_sortable();
        $xcrud->subselect('category', "select GROUP_CONCAT(c1.category_code SEPARATOR ', ') as category_code from brand_category_allocation bc join category_mst c1 ON c1.category_id=bc.category_id WHERE bc.brand_id={brand_id} group by brand_id");
	$xcrud->subselect('category_id', "select GROUP_CONCAT(c1.category_name SEPARATOR ', ') as category_name from brand_category_allocation bc join category_mst c1 ON c1.category_id=bc.category_id WHERE bc.brand_id={brand_id} group by brand_id");
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
        
        $xcrud->create_action('active', 'active_brand');
        $xcrud->create_action('inactive', 'inactive_brand');
        
        $xcrud->button('#', 'Inactive', 'glyphicon glyphicon-ban-circle', 'xcrud-action btn-danger', array(
            'data-task' => 'action',
            'data-action' => 'inactive',
            'data-primary' => '{brand_id}'), array(
            'status',
            '=',
            '1')
        );
        $xcrud->button('#', 'Active', 'glyphicon glyphicon-ok', 'xcrud-action btn-success', array(
            'data-task' => 'action',
            'data-action' => 'active',
            'data-primary' => '{brand_id}'), array(
            'status',
            '!=',
            '1'));
        
        $xcrud->button('#', 'Edit', 'glyphicon glyphicon-edit', '', array(
            'data-toggle' => 'modal',
            'class' => 'btn btn-warning btn-sm edit_data',
            'data-target' => ".mdl_brand",
            'data-primary' => '{brand_id}'));
        
//        $xcrud->create_action('delete', 'remove_brand');
//        $xcrud->button('#', 'Remove', 'glyphicon glyphicon-trash', 'xcrud-action btn-danger', array(
//            'data-task' => 'action',
//            'data-action' => 'delete',
//            'data-primary' => '{brand_id}')
//        );
        
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
    
    public function save() {
        $post = $_POST;  
        $files = $_FILES;  
        //print_r($post); exit;
        
        if(isset($post["is_top_brand"]) && $post["is_top_brand"] == "on") {
            $post["is_top_brand"] = 1;
        } else {
            $post["is_top_brand"] = 0;
        }
        
        if(isset($post["in_loyalty_club"]) && $post["in_loyalty_club"] == 1) {
            $post["in_loyalty_club"] = 1;
        } else {
            $post["in_loyalty_club"] = 0;
        }
	
	///bucket info
	$credentials = new Aws\Credentials\Credentials(PUBLISHED_KEY, SECRET_KEY);
	$s3 = new S3Client([        
	    'region' => 'eu-west-2',
	    'version' => '2006-03-01',
	    //'debug' => true,
	    "credentials" => $credentials
	]);
        
        //upload brand logo
        if (isset($files['brand_logo']['name']) && $files['brand_logo']['name']) {
	    
	    $ext = '.' . pathinfo($files['brand_logo']['name'], PATHINFO_EXTENSION);
	    $filename = date('YmdHis') . rand() . strtolower($ext);
	    $keyname = $filename;
	    $filepath = $files['brand_logo']['tmp_name'];

	    $result = $s3->putObject(array(
		'Bucket' => BUCKET_NAME,
		'Key' => 'brand/'.$keyname,
		'SourceFile' => $filepath,
		'ACL' => 'public-read',
		'StorageClass' => 'STANDARD'
	    ));
	    
	    $result2 = $s3->putObject(array(
		'Bucket' => BUCKET_NAME,
		'Key' => 'brand/thumbs/'.$keyname,
		'SourceFile' => $filepath,
		'ACL' => 'public-read',
		'StorageClass' => 'STANDARD'
	    ));
	    
	    $post['brand_logo'] = $filename;
        }
        else {
            $post['brand_logo'] = '';
        }
       
        //upload slider image
        if (isset($files['slider_img']['name']) && $files['slider_img']['name']) {
	    $ext = '.' . pathinfo($files['slider_img']['name'], PATHINFO_EXTENSION);
	    $filename = date('YmdHis') . rand() . strtolower($ext);
	    $keyname = $filename;
	    $filepath = $files['slider_img']['tmp_name'];

	    $result = $s3->putObject(array(
		'Bucket' => BUCKET_NAME,
		'Key' => 'brand/'.$keyname,
		'SourceFile' => $filepath,
		'ACL' => 'public-read',
		'StorageClass' => 'STANDARD'
	    ));
	    
	    $result2 = $s3->putObject(array(
		'Bucket' => BUCKET_NAME,
		'Key' => 'brand/thumbs/'.$keyname,
		'SourceFile' => $filepath,
		'ACL' => 'public-read',
		'StorageClass' => 'STANDARD'
	    ));
	    
	    $post['slider_img'] = $filename;
        } 
        else {
            $post['slider_img'] = '';
        }
        
        //print_r($post); exit;

        if(isset($post["action"]) && $post["action"] == 'edit') {
            $add_brand_data = $this->m_brand->update_brand($post);
        } else if(isset($post["action"]) && $post["action"] == 'add') {
            $add_brand_data = $this->m_brand->add_brand($post);
        }        
        
        if($add_brand_data === 'exist'){            
            echo 'exist'; die();
        }
        else if($add_brand_data === 'error')  {            
            echo 'error'; die();
        }
        else if($add_brand_data === 'success') { 
            echo 'success'; die();
        }
    }
    
    public function get_brand() {
        $post = $_POST;
        $brand = $this->m_brand->get_brand_details($post);
        //print_r($brand); exit;
        if ($brand) {
            echo json_encode($brand);
        }
    }
    
    public function top_brands(){
        $post = $_POST;  
        $brand = $this->m_brand->add_top_brand($post);
        if ($brand) {
            echo json_encode($brand);
        }
    }
    
    public function get_subcategory() {       
        $post = $_POST;
        $brand = $this->m_brand->get_subcategory($post);
        if ($brand) {
            echo json_encode($brand);
        }
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
    
    public function export_xls() {
//	$this->m_brand->export_brands();
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
	
	$products = $this->db->select('c.category_code as "Category / Subcategory Code", sc.brand_code as "Brand Code", sc.brand_name as "Brand Name", sc.brand_logo as "Brand Logo", sc.slider_img as "Brand Slider Image"', false)
			    ->join("brand_category_allocation bc", "bc.brand_id = sc.brand_id")
			    ->join("category_mst c", "c.category_id = bc.category_id")
			    ->order_by("sc.brand_name", "asc")
			    ->get('brand_mst sc')->result_array();
		
//	print_r($products); exit;

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
	
	$row = 2; // 1-based index
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
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="brand_' . date("Y_m_d_H_i") . '.xls"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
	header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header('Pragma: public'); // HTTP/1.0

	$writer = IOFactory::createWriter($spreadsheet, 'Xls');
	$writer->save('php://output');	
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
//	    echo "<pre>"; print_r($data); exit;

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
		    //print_r($exists); exit;

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

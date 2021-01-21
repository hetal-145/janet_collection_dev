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

class Sub_category extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_login');
        $this->load->model('m_subcategory');
        $this->load->model('m_tools');
        $this->m_login->check_session();
    }

    public function index() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('category_mst');         
        $xcrud->change_type('in_loyalty_club', 'bool');
        $xcrud->join('parent_id', 'category_mst', 'category_id', 'c2');
	$xcrud->subselect('category_img_view', '{category_img}');
	$xcrud->set_var('s3_path', S3_PATH.'category/');
	$xcrud->column_callback('category_img', 'list_img_function');	
	$xcrud->column_callback('category_img_view', 'view_img_function');
        $xcrud->columns('category_img, category_code, c2.category_name, category_name, in_loyalty_club');                
        $xcrud->fields('category_code, category_name, category_img_view, in_loyalty_club'); 
        
        $xcrud->column_name('in_loyalty_club' , 'In Loyalty Club?');
        $xcrud->search_columns('category_code,c2.category_name, category_name');  
        
        $xcrud->label(array(
            'category_code' => 'Category Code',
            'c2.category_name' => 'Category',
            'category_name' => 'Sub Category',
            'category_img' => 'Image',
	    'category_img_view' => 'Image',
        ));
        
//        $xcrud->change_type('category_img', 'image', false, array(
//            'width' => 720,
//            'path' => '../../upload/category',
//            'thumbs' => array(
//                array(
//                    'height' => 100,
//                    'width' => 100,
//                    'crop' => true,
//                    'marker' => '_th',
//                    'folder' => 'thumbs'
//                )
//            )
//            
//        ));
        
        $xcrud->create_action('active', 'active_category');
        $xcrud->create_action('inactive', 'inactive_category');
        
        $xcrud->button('#', 'Inactive', 'glyphicon glyphicon-ban-circle', 'xcrud-action btn-danger', array(
            'data-task' => 'action',
            'data-action' => 'inactive',
            'data-primary' => '{category_id}'), array(
            'status',
            '=',
            '1')
        );
        $xcrud->button('#', 'Active', 'glyphicon glyphicon-ok', 'xcrud-action btn-success', array(
            'data-task' => 'action',
            'data-action' => 'active',
            'data-primary' => '{category_id}'), array(
            'status',
            '!=',
            '1'));
        
        $xcrud->button('#', 'Edit', 'glyphicon glyphicon-edit', '', array(
            'data-toggle' => 'modal',
            'class' => 'btn btn-warning btn-sm edit_data',
            'data-target' => ".mdl_sub_category",
            'data-primary' => '{category_id}'));
        
        $xcrud->unset_add();
        $xcrud->unset_edit();
        $xcrud->unset_view();
        $xcrud->unset_remove();
        //$xcrud->unset_search();
        
        $data["categories"] = $this->m_tools->get_category();             
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('sub_category', $data);
        $this->load->view('footer');
    }
    
    public function export_xls() {
//	$this->m_subcategory->export_subcategory();
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
	    ->setTitle('Sub Category List')
	    ->setSubject('Sub Category List')
	    ->setDescription('Sub Category List') 
	    ->setCategory('Sub Category List');
	
	$products = $this->db->select('sc.category_code as "SubCategory Code", c.category_name as "Category", sc.category_name as "SubCategory"', false)
		    ->join("category_mst c", "c.category_id = sc.parent_id")
		    ->order_by("sc.category_name", "asc")
		    ->get('category_mst sc')->result_array(); 
		
//	print_r($products); exit;

	$spreadsheet->getActiveSheet()->setCellValue('A1', 'SubCategory Code');
	$spreadsheet->getActiveSheet()->setCellValue('B1', 'Category');
	$spreadsheet->getActiveSheet()->setCellValue('C1', 'SubCategory');

	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	
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
	$spreadsheet->getActiveSheet()->setTitle('Sub Category List');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="subcategory_' . date("Y_m_d_H_i") . '.xls"');
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
    
    public function save() { 
        $post = $_POST;  
        $files = $_FILES; 
	
	///bucket info
	$credentials = new Aws\Credentials\Credentials(PUBLISHED_KEY, SECRET_KEY);
	$s3 = new S3Client([        
	    'region' => 'eu-west-2',
	    'version' => '2006-03-01',
	    //'debug' => true,
	    "credentials" => $credentials
	]);
        
        //upload files
        if (isset($files['category_img']['name']) && $files['category_img']['name']) {
           $ext = '.' . pathinfo($files['category_img']['name'], PATHINFO_EXTENSION);
	    $filename = date('YmdHis') . rand() . strtolower($ext);
	    $keyname = $filename;
	    $filepath = $files['category_img']['tmp_name'];

	    $result = $s3->putObject(array(
		'Bucket' => BUCKET_NAME,
		'Key' => 'category/'.$keyname,
		'SourceFile' => $filepath,
		'ACL' => 'public-read',
		'StorageClass' => 'STANDARD'
	    ));
	    
	    $result2 = $s3->putObject(array(
		'Bucket' => BUCKET_NAME,
		'Key' => 'category/thumbs/'.$keyname,
		'SourceFile' => $filepath,
		'ACL' => 'public-read',
		'StorageClass' => 'STANDARD'
	    ));
	    
	    $post['category_img'] = $filename;
       }

        $add_category_data = $this->m_subcategory->add_subcategory($post);
        
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
    
    public function get_subcategory() {
        $post = $_POST;
        $category = $this->m_subcategory->get_sub_category_details($post);
        if ($category) {
            echo json_encode($category);
        }
    }

}

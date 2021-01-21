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

class Category extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_login');
        $this->load->model('m_category');
        $this->load->model('m_tools');
        $this->m_login->check_session();
    }

    public function index() {
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
        //$xcrud->subselect('sub_category', "select GROUP_CONCAT(category_name) from category_mst WHERE parent_id={category_id} and status = 1 group by parent_id");
        //$xcrud->fields('category_code, category_name, category_img, in_loyalty_club'); 
        $xcrud->change_type('in_loyalty_club', 'bool');
        $xcrud->column_name('in_loyalty_club' , 'In Loyalty Club?');
        $xcrud->search_columns('category_code,category_name', 'category_name');  
        
        $xcrud->label(array(
            'category_img' => 'Image',
	    'category_img_view' => 'Image',
            'category_id' => 'Sub Category',
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
            'data-target' => ".mdl_category",
            'data-primary' => '{category_id}'));
        
        $xcrud->unset_add();
        $xcrud->unset_edit();
        $xcrud->unset_view();
        $xcrud->unset_remove();
        //$xcrud->unset_search();
                    
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('category', $data);
        $this->load->view('footer');
    }
    
    public function export_xls() {
//	$this->m_category->export_category();
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
	    ->setTitle('Category List')
	    ->setSubject('Category List')
	    ->setDescription('Category List') 
	    ->setCategory('Category List');
	
	$products = $this->db->select('sc.category_code as "Category Code", sc.category_name as "Category Name"', false)
		    ->where("sc.parent_id", 0)
		    ->order_by("sc.category_name", "asc")
		    ->get('category_mst sc')->result_array(); 
		
//	print_r($products); exit;

	$spreadsheet->getActiveSheet()->setCellValue('A1', 'Category Code');
	$spreadsheet->getActiveSheet()->setCellValue('B1', 'Category Name');

	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	
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
	$spreadsheet->getActiveSheet()->setTitle('Category List');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="category_' . date("Y_m_d_H_i") . '.xls"');
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
       
       //upload subcategory files
        if(isset($post["want_to_add_checkbox"]) && $post["want_to_add_checkbox"] == "on") {                    
            for($i=1; $i<=$post["no_of_categories"]; $i++) {
                if (!empty($files['sub_category_img_'.$i]['name'])) {
		    $ext = '.' . pathinfo($files['sub_category_img_'.$i]['name'], PATHINFO_EXTENSION);
		    $filename = date('YmdHis') . rand() . strtolower($ext);
		    $keyname = $filename;
		    $filepath = $files['sub_category_img_'.$i]['tmp_name'];

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
		    
		    $post['sub_category_img_'.$i] = $filename;
                } 
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

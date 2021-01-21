<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

include('../vendor_spreadsheet/autoload.php');
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Zipcode extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_login');
        $this->load->model('m_tools');
        $this->m_login->check_session();
    }

    public function index() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('zipcode');
        
        $xcrud->relation("dzone_id", "delivery_zone", "dzone_id", "area_code");
        $xcrud->columns('zipcode_id, zipcode, area, dzone_id'); 
        $xcrud->fields('zipcode, area, dzone_id'); 
        $xcrud->validation_required('zipcode', 1);
        
        $xcrud->before_insert('check_exist_zipcode');
        $xcrud->column_callback('zipcode_id', 'delete_zipcode');
        $xcrud->search_columns('zipcode, area', 'zipcode');  
        
        $xcrud->label(array(
            'zipcode' => 'Zip Code',
            'area' => 'Street / Area',
            'dzone_id' => 'Delivery Zone',
            'zipcode_id' => '#',
        ));
        
        $xcrud->change_type('status', 'select', '1', array('1' => 'Active', '0' => 'Inactive'));

        $xcrud->create_action('active', 'active_zipcode');
        $xcrud->create_action('inactive', 'inactive_zipcode');
        $xcrud->button('#', 'Inactive', 'glyphicon glyphicon-ban-circle', 'xcrud-action btn-info', array(
            'data-task' => 'action',
            'data-action' => 'inactive',
            'data-primary' => '{zipcode_id}'), array(
            'status',
            '=',
            '1')
        );
        $xcrud->button('#', 'Active', 'glyphicon glyphicon-ok', 'xcrud-action btn-success', array(
            'data-task' => 'action',
            'data-action' => 'active',
            'data-primary' => '{zipcode_id}'), array(
            'status',
            '!=',
            '1'));
        
        $xcrud->unset_view();
        $xcrud->unset_remove();
       
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('zipcode', $data);
        $this->load->view('footer');
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
	    
	    foreach($sheetdata2 as $key => $data){
	       // print_r($data); exit;

		//check exists
		$exists = $this->db->select('zipcode_id')
		    ->where('zipcode', $data[0])
		    ->get('zipcode')->row_array(); 

		if (empty($exists)) {

		    $dzone = $this->db->select("dzone_id")
			    ->where("area_code", trim($data[2]))
			    ->get("delivery_zone")->row_array();

		    if(!empty($dzone)) {
			$dzone_id = $dzone["dzone_id"];
		    }
		    else {
			$dzone_id = 1;
		    }

		    if(isset($data[1])) {
			//Insert Product
			$zipcode_data = array(
			    'zipcode' => $data[0],
			    'area' => $data[1], 
			    'dzone_id' => $dzone_id, 
			);
		    } 
		    else {
			//Insert Product
			$zipcode_data = array(
			    'zipcode' => $data[0],
			    'dzone_id' => $dzone_id,
			);
		    }

		    $zipcode_insert = $this->db
			    ->insert('zipcode', $zipcode_data);

		    $zipid = $this->db->insert_id();                        
		} 
		else {
		    $dzone = $this->db->select("dzone_id")
			    ->where("area_code", trim($data[2]))
			    ->get("delivery_zone")->row_array();

		    if(!empty($dzone)) {
			$dzone_id = $dzone["dzone_id"];
		    }
		    else {
			$dzone_id = 1;
		    }

		    $this->db->set('dzone_id', $dzone_id)
			    ->where('zipcode', $data[0])
			    ->update("zipcode");
		    
		    $zipid = $exists["zipcode_id"];
		}
	    }
            echo 'success';   
        }
    }
    
    public function delete_all(){
        $post = $_POST;
        //print_r($post); 
        $zipcode = implode(',', $post["zipcode"]);
        $where = 'zipcode_id IN ('.$zipcode.')';
        
        $del = $this->db->where($where)
                ->delete('zipcode');
        
        if($del){
            echo 'success'; exit;
        }
        else {
            echo 'error'; exit;
        }
        
    }
    
    public function export_xls() {
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
	    ->setTitle('Zipcode List')
	    ->setSubject('Zipcode List')
	    ->setDescription('Zipcode List') 
	    ->setCategory('Zipcode List');
	
	$products = $this->db->select('z.zipcode as "Zipcode", z.area as "Street / Area", d.area_code as "Delivery Zone ( pass code )"', false)
		    ->join("delivery_zone d", "z.dzone_id = d.dzone_id", "left")
		    ->where("z.status", 1)
		    ->get('zipcode z')->result_array();  
		
//	print_r($products); exit;

	$spreadsheet->getActiveSheet()->setCellValue('A1', 'Zipcode');
	$spreadsheet->getActiveSheet()->setCellValue('B1', 'Street / Area');
	$spreadsheet->getActiveSheet()->setCellValue('C1', 'Delivery Zone ( pass code )');

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
	$spreadsheet->getActiveSheet()->setTitle('Zipcode List');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="zipcodes_' . date("Y_m_d_H_i") . '.xls"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
	header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header('Pragma: public'); // HTTP/1.0

	$writer = IOFactory::createWriter($spreadsheet, 'Xls');
	$writer->save('php://output');
//	$zipcode = $this->db->select('z.zipcode as "Zipcode", z.area as "Street / Area", d.area_code as "Delivery Zone ( pass code )"', false)
//		    ->join("delivery_zone d", "z.dzone_id = d.dzone_id", "left")
//		    ->where("z.status", 1)
//		    ->get('zipcode z')->result_array(); 
//	
//	//download in csv
//	$filename = "zipcodes_" . date("Y-m-d") . ".csv";	
//	header("Content-Description: File Transfer");
//	header("Content-Disposition: attachment; filename=".$filename);
//	header("Content-Type: application/csv; "); 
//	
//	$df = fopen("php://output", 'w');
//	fputcsv($df, array_keys(reset($zipcode)));
//	foreach ($zipcode as $row) {
//	   fputcsv($df, $row);
//	}
//	fclose($df);	
    }

}

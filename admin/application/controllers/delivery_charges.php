<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

include('../vendor_spreadsheet/autoload.php');
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Delivery_charges extends CI_Controller {

    function __construct() {
        parent::__construct();
        include ('xcrud/xcrud.php');
        $this->load->model('m_login');
	$this->load->model('m_delivery_zone');
        $this->load->model('m_tools');
        $this->m_login->check_session();
    }

    public function index() {
        $xcrud = Xcrud::get_instance();
        $xcrud->table('delivery_charges');
	$xcrud->columns('miles, base_rate, pay_driver_pickup, pay_driver_dropoff');
	$xcrud->fields('miles, base_rate, pay_driver_pickup, pay_driver_dropoff');       
        $xcrud->search_columns('miles,base_rate,pay_driver_pickup,pay_driver_dropoff', 'miles');       
        $xcrud->unset_remove();
       
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('delivery_charges', $data);
        $this->load->view('footer');
    }
    
    public function export_xls() {
//	$this->m_delivery_zone->export_delivery_charges();
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
	    ->setTitle('Delivery Charges List')
	    ->setSubject('Delivery Charges List')
	    ->setDescription('Delivery Charges List') 
	    ->setCategory('Delivery Charges List');
	
	$products = $this->db->select('m.miles as "Miles", m.base_rate as "Base Rate", m.pay_driver_pickup as "Driver Pickup Rate", m.pay_driver_dropoff as "Driver DropOff Rate"', false)
		    ->order_by("m.miles", "asc")
		    ->get('delivery_charges m')->result_array(); 
		
//	print_r($products); exit;

	$spreadsheet->getActiveSheet()->setCellValue('A1', 'Miles');
	$spreadsheet->getActiveSheet()->setCellValue('B1', 'Base Rate');
	$spreadsheet->getActiveSheet()->setCellValue('C1', 'Driver Pickup Rate');
	$spreadsheet->getActiveSheet()->setCellValue('D1', 'Driver DropOff Rate');

	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	
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
	$spreadsheet->getActiveSheet()->setTitle('Delivery Charges List');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="delivery_charges_' . date("Y_m_d_H_i") . '.xls"');
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
    
    public function import_xls() {        
        if (isset($_FILES['import_file_nm']['name']) && $_FILES['import_file_nm']['name'] && ($_FILES['import_file_nm']['size'] > 0)) {
            $filename = $_FILES['import_file_nm']['tmp_name']; 
	    $inputFileType = 'Xls';
        
	    $reader = IOFactory::createReader($inputFileType);
	    $spreadsheet = $reader->load($filename);
	    
	    $sheetdata = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
	    $sheetdata2 = array_slice($sheetdata, 1);
	    
	    foreach($sheetdata2 as $key => $data){
//	    print_r($data); exit;

		if(trim($data[0]) == ""){
		    echo '1'; exit;
		} 
		else if(empty(trim($data[1]))){
		    echo '2'; exit;
		} 
		else if(empty(trim($data[2]))){
		    echo '3'; exit;
		} 
		else if(empty(trim($data[3]))){
		    echo '4'; exit;
		} 
		else {   
		    //check exists
		    $exists = $this->db->select('charge_id')
			->where('miles', $data[0])
			->get('delivery_charges')->row_array(); 
//                            print_r($exists); exit;

		    if (empty($exists)) {

			//Insert Charges
			$product_data = array(
			    'miles' => trim($data[0]),    
			    'base_rate' => trim($data[1]),
			    'pay_driver_pickup' => trim($data[2]),
			    'pay_driver_dropoff' => trim($data[3])
			);

			$this->db->insert('delivery_charges', $product_data);          
		    } 
		    else {	

			//Update Charges
			$product_data1 = array(
			    'miles' => trim($data[0]),    
			    'base_rate' => trim($data[1]),
			    'pay_driver_pickup' => trim($data[2]),
			    'pay_driver_dropoff' => trim($data[3])
			);

			$this->db->set($product_data1)
				->where("charge_id", $exists["charge_id"])
				->update('delivery_charges');
		    }
		} 			
	    }
	    echo 'success';
        }
    }

}

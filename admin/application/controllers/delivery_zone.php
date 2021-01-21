<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

include('../vendor_spreadsheet/autoload.php');
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Delivery_zone extends CI_Controller {

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
        $xcrud->table('delivery_zone');        
        $xcrud->subselect('select_multi', '{dzone_id}');
	$xcrud->column_callback('select_multi', 'select_multiple_delivery_zone');
        $xcrud->columns('select_multi, city, area_code'); 
        $xcrud->fields('city, area_code'); 
        $xcrud->search_columns('city, area_code', 'area_code');        
        $xcrud->before_insert('check_exist_zone');
        $xcrud->before_update('add_areacode_in_zone');        
	$xcrud->change_type('status', 'select', '1', array('1' => 'Active', '0' => 'Inactive'));
	
        $xcrud->label(array(
            'city' => 'City',
            'area_code' => 'Delivery Zone Area Code',
	    'select_multi' => ""
        ));        

        $xcrud->create_action('inactive', 'inactive_delivery_zone');
        $xcrud->button('#', 'Inactive', 'glyphicon glyphicon-ban-circle', 'xcrud-action btn-danger', array(
            'data-task' => 'action',
            'data-action' => 'inactive',
            'data-primary' => '{dzone_id}'), array(
            'status',
            '=',
            '1')
        );
        
        $xcrud->create_action('active', 'active_delivery_zone');        
        $xcrud->button('#', 'Active', 'glyphicon glyphicon-ok', 'xcrud-action btn-success', array(
            'data-task' => 'action',
            'data-action' => 'active',
            'data-primary' => '{dzone_id}'), array(
            'status',
            '!=',
            '1'));
        
        $xcrud->unset_view();
        $xcrud->unset_remove();
       
        $data['content'] = $xcrud->render();
        $this->load->view('header', $data);
        $this->load->view('delivery_zone', $data);
        $this->load->view('footer');
    }       
    
    public function active_all(){
	$post = $_POST;
	$this->db->set("status", 1)->where("dzone_id IN (".$post['pids'].")")->update("delivery_zone");
	echo 1;
    }
    
    public function deactive_all(){
	$post = $_POST;
	$this->db->set("status", 0)->where("dzone_id IN (".$post['pids'].")")->update("delivery_zone");
	echo 1;
    }    
    
    public function export_xls() {
//	$this->m_delivery_zone->export_delivery_zone();
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
	    ->setTitle('Delivery Zone List')
	    ->setSubject('Delivery Zone List')
	    ->setDescription('Delivery Zone List') 
	    ->setCategory('Delivery Zone List');
	
	$products = $this->db->select('m.city as "City", m.area_code as "Area Code"', false)
		    ->order_by("m.area_code", "asc")
		    ->get('delivery_zone m')->result_array(); 
		
//	print_r($products); exit;

	$spreadsheet->getActiveSheet()->setCellValue('A1', 'City');
	$spreadsheet->getActiveSheet()->setCellValue('B1', 'Area Code');

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
	$spreadsheet->getActiveSheet()->setTitle('Delivery Zone List');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="delivery_zone_' . date("Y_m_d_H_i") . '.xls"');
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
//		print_r($data); exit;

		if(empty(trim($data[0]))){
		    echo '1'; exit;
		} 
		else if(empty(trim($data[1]))){
		    echo '2'; exit;
		}
		else {   
		    //check exists
		    $exists = $this->db->select('dzone_id')
			->where('city', $data[0])
			->where('area_code', $data[1])
			->get('delivery_zone')->row_array(); 
//                            print_r($exists); exit;

		    if (empty($exists)) {

			//Insert Charges
			$product_data = array(
			    'city' => trim($data[0]),    
			    'area_code' => trim($data[1])
			);

			$this->db->insert('delivery_zone', $product_data);          
		    } 
		    else {	

			//Update Charges
			$product_data1 = array(
			    'city' => trim($data[0]),    
			    'area_code' => trim($data[1])
			);

			$this->db->set($product_data1)
				->where("dzone_id", $exists["dzone_id"])
				->update('delivery_zone');
		    }
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

}

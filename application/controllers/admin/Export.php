<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . '../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Export extends MY_Controller
{

	public function __construct()
    {
        parent::__construct();
        $this->load->model('Order_model');
    }

	public function transaction()
	{

		$spreadsheet = new Spreadsheet();

		$from = "A1";
		$to = "H1";
		$spreadsheet->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold( true );

		$spreadsheet->getSheet(0)->setCellValue("A1", "Sr.");
		$spreadsheet->getSheet(0)->setCellValue("B1", "Order ID");
		$spreadsheet->getSheet(0)->setCellValue("C1", "Name");
		$spreadsheet->getSheet(0)->setCellValue("D1", "Email");
		$spreadsheet->getSheet(0)->setCellValue("E1", "Amount");
		$spreadsheet->getSheet(0)->setCellValue("F1", "Payment Mode");
		$spreadsheet->getSheet(0)->setCellValue("G1", "Payment ID");
		$spreadsheet->getSheet(0)->setCellValue("H1", "Date");


		$row_data = $this->Order_model->get_transactions(array('transaction.`status`' => '1'));
        $excel_row = 2;
        $no = 1;

		$sheet = $spreadsheet->getActiveSheet();

		foreach ($row_data as $row) {
            $sheet->setCellValue('A'.$excel_row, $no++);
            $sheet->setCellValue('B'.$excel_row, $row->order_unique_id);
            $sheet->setCellValue('C'.$excel_row, $row->user_name);
            $sheet->setCellValue('D'.$excel_row, $row->email);
            $sheet->setCellValue('E'.$excel_row, $this->settings->app_currency_html_code . $row->payment_amt);
            $sheet->setCellValue('F'.$excel_row, strtoupper($row->gateway));
            $sheet->setCellValue('G'.$excel_row, $row->payment_id);
            $sheet->setCellValue('H'.$excel_row, date('d-m-Y h:i A', $row->date));
            $excel_row++;
        }
		
		$writer = new Xlsx($spreadsheet);

		$fileName = date('dmyhis').'_transaction';

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $fileName .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		
		$writer->save('php://output');
	}

	public function refund()
	{

		$this->load->model('Api_model');

		$spreadsheet = new Spreadsheet();

		$from = "A1";
		$to = "H1";
		$spreadsheet->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold( true );

		$spreadsheet->getSheet(0)->setCellValue("A1", "Sr.");
		$spreadsheet->getSheet(0)->setCellValue("B1", "Order ID");
		$spreadsheet->getSheet(0)->setCellValue("C1", "Product");
		$spreadsheet->getSheet(0)->setCellValue("D1", "Refund Amount");
		$spreadsheet->getSheet(0)->setCellValue("E1", "Reason");
		$spreadsheet->getSheet(0)->setCellValue("F1", "Status");
		$spreadsheet->getSheet(0)->setCellValue("G1", "Date");


		$row_data = $this->Api_model->get_refund_data();
        $excel_row = 2;
        $no = 1;

		$sheet = $spreadsheet->getActiveSheet();

		foreach ($row_data as $row) {

			switch ($row->request_status) {
                case '0':
                    $status = 'Pending';
                    break;
                case '2':
                    $status = 'Process';
                    break;
                case '1':
                    $status = 'Completed';
                    break;
                case '-1':
                    $status = 'Wating for claim';
                    break;

                default:
                    $status = 'Pending';
                    break;
            }

            $sheet->setCellValue('A'.$excel_row, $no++);
            $sheet->setCellValue('B'.$excel_row, $row->order_unique_id);
            $sheet->setCellValue('C'.$excel_row, $row->product_title);
            $sheet->setCellValue('D'.$excel_row, $this->settings->app_currency_html_code . $row->refund_pay_amt);
            $sheet->setCellValue('E'.$excel_row, $row->refund_reason);
            $sheet->setCellValue('F'.$excel_row, $status);
            $sheet->setCellValue('G'.$excel_row, date('d-m-Y h:i A', $row->last_updated));
            $excel_row++;
        }
		
		$writer = new Xlsx($spreadsheet);

		$fileName = date('dmyhis').'_refunds';

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $fileName .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		
		$writer->save('php://output');
	}
}
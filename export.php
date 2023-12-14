
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Export</title>
</head>


<?php
include_once "ses_set_s.php"; 
require_once("classes/PHPExcel.class.php");
$table    = $_POST['data'];

// save $table inside temporary file that will be deleted later
//$tmpfile = tempnam(sys_get_temp_dir(), 'html');
$baseDir = dirname(__FILE__);
$tmpfile = $baseDir.'/'.'test.html';

//echo $tmpfile ;

file_put_contents($tmpfile, $table);

// insert $table into $objPHPExcel's Active Sheet through $excelHTMLReader
$objPHPExcel     = new PHPExcel();
$excelHTMLReader = PHPExcel_IOFactory::createReader('HTML');
$excelHTMLReader->loadIntoExisting($tmpfile, $objPHPExcel);
//die();
$objPHPExcel->getActiveSheet()->setTitle('any name you want'); // Change sheet's title if you want

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
$filename = "DownloadReport_".$_SESSION["society_id"];
$border_style= array('borders' => array('right' => array('style' => 
PHPExcel_Style_Border::BORDER_THICK,'color' => array('argb' => '766f6e'),)));

$lastrow = $objPHPExcel->getActiveSheet()->getHighestRow(); $objPHPExcel->getActiveSheet();
$objPHPExcel->getActiveSheet()->getStyle('A1:A'.$lastrow) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1:F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('D1:D'.$lastrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1 );
$objPHPExcel->getActiveSheet()->getStyle('E1:E'.$lastrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle('F1:F'.$lastrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1 );
$objPHPExcel->getActiveSheet()->getStyle('A1:F100')->applyFromArray($border_style);

//header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
//header('Content-Disposition: attachment;filename="sample.xlsx"'); // specify the download file name
//header('Cache-Control: max-age=0');
//header('Cache-Control: max-age=1');

$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$writer->save('php://output');
$writer->save($baseDir.'/Export/'.$filename.'.xlsx');

header('Content-disposition: attachment; filename='.$filename.'.xlsx');
header("Content-Length: " . filesize($baseDir.'/Export/'.$filename.'.xlsx'));
header('Content-Type: application/octet-stream');

//header('Content-Type: application/zip');
ob_clean(); 
readfile($baseDir.'/Export/'.$filename.'.xlsx');

exit;
?>
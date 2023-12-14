<?php
/*
* PHP ZIP - Create a ZIP archive
*/

if(isset($_REQUEST['society_id']) &&  isset($_REQUEST['invoice_range_from']) && isset($_REQUEST['invoice_range_to']) && isset($_REQUEST['invoice_data']) )
{
	include_once ("classes/include/dbop.class.php");
	$dbConn = new dbop();
	
	include_once ("classes/include/fetch_data.php");
	$obj_fetch = new FetchData($dbConn);
	
	include_once("PDFMerger/PDFMerger.php");
	
	$societyDetails = $obj_fetch->GetSocietyDetails($_REQUEST['society_id']);
	
	$baseDir = dirname( dirname(__FILE__) );
	$range="Inv-".$_REQUEST['invoice_range_from']."-".$_REQUEST['invoice_range_to'];

	$dirName =  "Invoice_bills/" . $obj_fetch->objSocietyDetails->sSocietyCode ."/";
	
	$zipFile = $obj_fetch->objSocietyDetails->sSocietyCode ."-".$range.".zip";
	$combine_file = 'All-' . $obj_fetch->objSocietyDetails->sSocietyCode . "-".$range.".pdf";
	
	
		
	if (file_exists($dirName . $zipFile)) {
		try
		{
			unlink($dirName . $zipFile);
		}
		catch(Exception $exp)
		{
			//echo '<br/>Exception : ' . $exp;
		}
    }
	
	if (file_exists($dirName . $combine_file)) 
	{
		try
		{
			unlink($dirName . $combine_file);
		}
		catch(Exception $exp)
		{
			//echo '<br/>Exception : ' . $exp;
		}
    }
	

	$zip = new ZipArchive;
	$pdf = new PDFMerger;
	
	if ($zip->open($dirName . $zipFile,  ZipArchive::CREATE)) 
	{
		$dir = new DirectoryIterator(dirname($dirName . '*.pdf'));
		
		try
		{
			$invoice_data=json_decode($_REQUEST['invoice_data'],true);

			foreach ($invoice_data as $data ) {
				$specialChars = array('/','.', '*', '%', '&', ',', '(', ')', '"');
				$unitID = str_replace($specialChars,'', $data['unit_id']);
				$pdfFileName = 'Inv-'.$obj_fetch->objSocietyDetails->sSocietyCode."-".$unitID.'-'.$data['inv_no'].'.pdf';

				if(file_exists($dirName.$pdfFileName))
				{
					$zip->addFile($dirName.$pdfFileName, $pdfFileName);
					$pdf->addPDF($dirName.$pdfFileName, 'all');

				}
				else
				{
					$dirName.$pdfFileName;
				}


			}
			$pdf->merge('file', $dirName.$combine_file);
             
			
		}
		catch(Exception $exp)
		{
			// echo "<br/>Merge Exception : " . $exp;
		}
		$zip->addFile($dirName.$combine_file, $combine_file);		

		$zip->close();

		header('Content-disposition: attachment; filename=' . $zipFile);
		header("Content-Length: " . filesize($dirName.$zipFile));
		header('Content-Type: application/octet-stream');
		ob_clean(); 
		readfile($dirName.$zipFile);
		echo 'Archive created!';
	} 
	else 
	{
		echo 'Failed!';
	}
}
?>

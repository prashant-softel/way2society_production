<?php
/*
* PHP ZIP - Create a ZIP archive
*/

if(isset($_REQUEST['society_id']) && isset($_REQUEST['period_id']) && isset($_REQUEST['Download']) && isset($_REQUEST['BT']) && isset($_REQUEST['DownType']))
{
	include_once ("classes/include/dbop.class.php");
	$dbConn = new dbop();
	
	include_once ("classes/include/fetch_data.php");
	$obj_fetch = new FetchData($dbConn);
	
	include_once("PDFMerger/PDFMerger.php");
	
	$societyDetails = $obj_fetch->GetSocietyDetails($_REQUEST['society_id']);
	
	$baseDir = dirname( dirname(__FILE__) );
	$BillType = $_REQUEST['BT'];
	$dirName =  "maintenance_bills/" . $obj_fetch->objSocietyDetails->sSocietyCode . "/" . $obj_fetch->GetBillFor($_REQUEST["period_id"]) . "/";
	
	$zipFile = $obj_fetch->objSocietyDetails->sSocietyCode . "-" . str_replace(' ', '-', $obj_fetch->GetBillFor($_REQUEST["period_id"])) . "-".$BillType.".zip";
	$combine_file = 'All-' . $obj_fetch->objSocietyDetails->sSocietyCode . "-" . str_replace(' ', '-', $obj_fetch->GetBillFor($_REQUEST["period_id"])) . "-".$BillType.".pdf";
	
	$zipFile1 = $obj_fetch->objSocietyDetails->sSocietyCode . "-" . str_replace(' ', '-', $obj_fetch->GetBillFor($_REQUEST["period_id"])) . "-".$BillType."-members.zip";
	$combine_file1 = 'All-' . $obj_fetch->objSocietyDetails->sSocietyCode . "-" . str_replace(' ', '-', $obj_fetch->GetBillFor($_REQUEST["period_id"])) . "-".$BillType."-member.pdf";
	
	if($_REQUEST['DownType'] == 0)
	{	
		if(file_exists($dirName . $zipFile)) 
		{
			try
			{
				unlink($dirName . $zipFile);
			}
			catch(Exception $exp)
			{
				//echo '<br/>Exception : ' . $exp;
			}
		}
		
		if(file_exists($dirName . $combine_file)) 
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
	
		if($zip->open($dirName . $zipFile,  ZipArchive::CREATE)) 
		{
			$dir = new DirectoryIterator(dirname($dirName . '*.pdf'));
			try
			{
				$sqlUnit = "select unittbl.`unit_no` from `unit` as unittbl ORDER BY unittbl.`sort_order` ASC";
				$result = $dbConn->select($sqlUnit);
				for($iCnt = 0; $iCnt < sizeof($result); $iCnt++)
				{
					$specialChars = array('/','.', '*', '%', '&', ',', '(', ')', '"');
					$unitNo = str_replace($specialChars,'', $result[$iCnt]['unit_no']);

					$pdfFileName = 'bill-' . $obj_fetch->objSocietyDetails->sSocietyCode . "-" . $unitNo . '-' . $obj_fetch->GetBillFor($_REQUEST["period_id"]) . "-".$BillType.'.pdf';
					if(file_exists($dirName.$pdfFileName))
					{
						$zip->addFile($dirName.$pdfFileName, $pdfFileName);
						$pdf->addPDF($dirName.$pdfFileName, 'all');
					}
				}
				$pdf->merge('file', $dirName.$combine_file);
			}
			catch(Exception $exp)
			{
				//echo "<br/>Merge Exception : " . $exp;
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
	if($_REQUEST['DownType'] == 1)
	{
		//echo "inside ";
		if(file_exists($dirName . $zipFile1)) 
		{
			try
			{
				unlink($dirName . $zipFile1);
			}
			catch(Exception $exp)
			{
				//echo '<br/>Exception : ' . $exp;
			}
		}
		//echo "inside 1";
		if(file_exists($dirName . $combine_file1)) 
		{
			try
			{
				unlink($dirName . $combine_file1);
			}
			catch(Exception $exp)
			{
				//echo '<br/>Exception : ' . $exp;
			}
		}
	//echo "inside 2";
		$zip1 = new ZipArchive;
		$pdf1 = new PDFMerger;
		if($zip1->open($dirName . $zipFile1,  ZipArchive::CREATE)) 
		{
			
			$dir = new DirectoryIterator(dirname($dirName . '*.pdf'));
			try
			{
				$sqlUnit1 = "select unittbl.unit_no from `unit` as unittbl JOIN member_main as membertbl ON unittbl.unit_id = membertbl.unit where unittbl.`status` ='Y' and membertbl.`email` = '' order by sort_order asc";
				$result1 = $dbConn->select($sqlUnit1);
				
				for($iCnt1 = 0; $iCnt1 < sizeof($result1); $iCnt1++)
				{
					$specialChars = array('/','.', '*', '%', '&', ',', '(', ')', '"');
					$unitNo = str_replace($specialChars,'', $result1[$iCnt1]['unit_no']);

					$pdfFileName = 'bill-' . $obj_fetch->objSocietyDetails->sSocietyCode . "-" . $unitNo . '-' . $obj_fetch->GetBillFor($_REQUEST["period_id"]) . "-".$BillType.'.pdf';
			//	echo $pdfFileName;
					if(file_exists($dirName.$pdfFileName))
					{
						$zip1->addFile($dirName.$pdfFileName, $pdfFileName);
						$pdf1->addPDF($dirName.$pdfFileName, 'all');
					}
				}
				$pdf1->merge('file', $dirName.$combine_file1);
			}
			catch(Exception $exp)
			{
				//echo "<br/>Merge Exception : " . $exp;
			}
			$zip1->addFile($dirName.$combine_file1, $combine_file1);	
			$zip1->close();
			header('Content-disposition: attachment; filename=' . $zipFile1);
    		header("Content-Length: " . filesize($dirName.$zipFile1));
			header('Content-Type: application/octet-stream');
			ob_clean(); 
			readfile($dirName.$zipFile1);
 			echo 'Archive created!';
		}
		else
		{
			echo 'Failed!';
		}
	}
}
?>

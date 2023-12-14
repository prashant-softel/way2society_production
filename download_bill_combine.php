<?php
/*
* PHP ZIP - Create a ZIP archive
*/

if(isset($_REQUEST['society_id']) && isset($_REQUEST['period_id']) && isset($_REQUEST['Download']))
{
	include_once ("classes/include/dbop.class.php");
	$dbConn = new dbop();
	
	include_once ("classes/include/fetch_data.php");
	$obj_fetch = new FetchData($dbConn);
	
	include_once("PDFMerger/PDFMerger.php");
	
	echo $societyDetails = $obj_fetch->GetSocietyDetails($_REQUEST['society_id']);
	
	$baseDir = dirname( dirname(__FILE__) );
	$dirName =  "maintenance_bills/" . $obj_fetch->objSocietyDetails->sSocietyCode . "/" . $obj_fetch->GetBillFor($_REQUEST["period_id"]) . "/";
	
	$zipFile = $obj_fetch->objSocietyDetails->sSocietyCode . "-" . str_replace(' ', '-', $obj_fetch->GetBillFor($_REQUEST["period_id"])) . ".zip";
	
	$combine_file = 'All-' . $obj_fetch->objSocietyDetails->sSocietyCode . "-" . str_replace(' ', '-', $obj_fetch->GetBillFor($_REQUEST["period_id"])) . ".pdf";
		
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
		//print_r($dir);
		foreach ($dir as $fileinfo) 
		{
			if (!$fileinfo->isDot()) 
			{
				//echo $dirName.$fileinfo->getFilename();
				$pdf->addPDF($dirName.$fileinfo->getFilename(), 'all');
				$zip->addFile($dirName.$fileinfo->getFilename(), $fileinfo->getFilename());
				
				
			}
		}
		
		try
		{
			$pdf->merge('file', $dirName.$combine_file);
		}
		catch(Exception $exp)
		{
			//echo "<br/>Merge Exception : " . $exp;
		}
		$zip->addFile($dirName.$combine_file, $combine_file);		
		$zip->close();
		header('Content-disposition: attachment; filename=' . $zipFile);
    	//header('Content-type: application/zip');
		header("Content-Length: " . filesize($zipFile));
		header('Content-Type: application/octet-stream');
	
	    readfile($dirName . $zipFile);
		echo 'Archive created!';
	} 
	else 
	{
		echo 'Failed!';
	}
}
else
{
	echo "Invalid Parameters";
}

?>

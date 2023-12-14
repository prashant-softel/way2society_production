<?php 
include_once("../classes/include/dbop.class.php");
include_once("../classes/dbconst.class.php");
include_once("../classes/ContributionLedgerDetailed.class.php");
include_once("../classes/genbill.class.php");
$dbConn = new dbop();
$obj_ContributionLedgerDetailed = new ContributionLedgerDetailed($dbConn);
$obj_genbill = new genbill($dbConn);


if($_REQUEST["method"] == 'fetch')
{

	$finalArray = array();
	$societyID = $_REQUEST['societyID'];
	$billType = $_REQUEST['billType'];
	$unitIDArray = json_decode(str_replace('\\', '', $_REQUEST['unitIDArray']), true);
	$sqlBillcycle = "select `society_name` from `society` where `society_id` = '".$societyID."' " ;
	$resBillcycle = $dbConn->select($sqlBillcycle);
	
	echo "<div style='display:none;' id='societyname'><center><h1><font>"  .$resBillcycle[0]['society_name']. "</font></h1></center></div>";	
	echo "<div><center><font>BILL SUMMARY WITH BIFURCATION</font></center></div>";	
	$gandtotal_array =array();
	$finalArray1=array();	
	for($i = 0 ;$i < sizeof($unitIDArray); $i++)
	{
		//$tempArray = $obj_ContributionLedgerDetailed->startProcess($societyID,$unitIDArray[$i]);
		if($billType != 2)
		{
			$finalArray = $obj_ContributionLedgerDetailed->getCollection($societyID,$unitIDArray[$i],$billType);

			if($_REQUEST['ignore_zero'] == 'true')
			{
				$finalArray = $obj_genbill->unsetZeroKeysFromArray($finalArray , $obj_ContributionLedgerDetailed->checkZero);
			}
		
			if(sizeof($finalArray) > 0 )
			{
				$obj_ContributionLedgerDetailed->displayResults1($finalArray);
				//array_push($finalArray,$tempArray);
			}
			array_push($gandtotal_array,end($finalArray));
		}
		else
		{
			//$finalArray = $obj_ContributionLedgerDetailed->getCollection($societyID,$unitIDArray[$i],$billType);
			$finalArray = $obj_ContributionLedgerDetailed->getInvoiceCollection($societyID,$unitIDArray[$i]);
			$GrandTotal= 0;
			//var_dump($finalArray);
			
			
			if(sizeof($finalArray) > 0 )
			{
			
				$obj_ContributionLedgerDetailed->displayResults1($finalArray);
				
				
			}
			array_push($gandtotal_array,end($finalArray));
		}
	}
	$sumArray = array();
	foreach ($gandtotal_array as $k=>$subArray) 
	{
		foreach ($subArray as $id=>$value) 
		{
			$sumArray[$id]+=$value;
		}
	}
	$sumArray['Unit No'] = "&nbsp&nbsp&nbsp";
	$sumArray['Member Name'] = "GrandTotal";
	$sumArray['BillNumber'] = "&nbsp&nbsp&nbsp";
	$sumArray['Fin. Year'] = $_SESSION['year_description'];
	$sumArray['Bill For'] = "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
	array_push($finalArray1,$sumArray);
	//var_dump($sumArray);
	$obj_ContributionLedgerDetailed->displayResults1($finalArray1);
	
	
}

?>
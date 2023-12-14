<?php include_once("../classes/bill_period.class.php");
include_once("../classes/include/dbop.class.php");
include_once("../classes/dbconst.class.php");
	  $dbConn = new dbop();
$obj_bill_period = new bill_period($dbConn);


if(isset($_REQUEST['getdate']))
{
	$aryDate = $obj_bill_period->getPeriodStartAndEndDate($_REQUEST['period']);
	if($aryDate <> '')
	{
		echo getDisplayFormatDate($aryDate[0]['BeginingDate']) . "@@@" . getDisplayFormatDate($aryDate[0]['EndingDate']);
	}
}
else if(isset($_REQUEST['getperiod']))
{
	if(isset($_REQUEST['cycleID']))
	{
		
	$get_unit = $obj_bill_period->get_period($_REQUEST['cycleID'],$_REQUEST['billtype']);
	
	
	}
	else
	{
		
	$get_unit = $obj_bill_period->get_period(0,$_REQUEST['billtype']);
	}
}
else
{
	echo $_REQUEST["method"]."@@@";
	
	if($_REQUEST["method"]=="edit")
	{
		$select_type = $obj_bill_period->selecting();
	
		foreach($select_type as $k => $v)
		{
			foreach($v as $kk => $vv)
			{
				echo $vv."#";
			}
		}
	}
	if(isset($_REQUEST['method'])){
		if($_REQUEST["method"] =="fetchpreviousid")
		{
			$periodid = $_REQUEST['period_id'];
			$billtype = $_REQUEST['billtype'];
			//$year = $_REQUEST[''];
			$get_unit = $obj_bill_period->getpreviusid($periodid,$billtype); // fetching data from getpreviusid funtion from class file 
			echo $get_unit;
		}
	}
	if($_REQUEST["method"]=="delete")
	{
		$obj_bill_period->deleting();
		return "Data Deleted Successfully";
	}
}
?>
<?php 
include_once("../classes/parkingType.class.php");
include_once("../classes/include/dbop.class.php");
include_once("../classes/dbconst.class.php");
$dbConn = new dbop(); 
$dbConnRoot = new dbop(true); 
$obj_parkingType = new parkingType($dbConn,$dbConnRoot);
if(isset($_REQUEST['method']))	
{
	if($_REQUEST["method"] == "getParkingType")
	{
		$Id = $_REQUEST['Id'];
		$res = $obj_parkingType->getParkingDetails($Id);
		foreach($res as $k => $v)
		{
			foreach($v as $kk => $vv)
			{
				echo $vv."#";
			}
		}
	}
	if($_REQUEST["method"] == "deleteParkingType")
	{
		$Id = $_REQUEST['Id'];
		$res = $obj_parkingType->deleteParkingDetails($Id);
		echo $res;
	}
}
?>
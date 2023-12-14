<?php 
include_once("../classes/smsQuota.class.php");
include_once("../classes/include/dbop.class.php");
include_once("../classes/dbconst.class.php");
$dbConn = new dbop(); 
$dbConnRoot = new dbop(true); 
$obj_SMS = new smsQuota($dbConn,$dbConnRoot);
if(isset($_REQUEST['method']))	
{
	if($_REQUEST["method"] == "getSMSQuota")
	{
		$_SESSION['SMSSocietyId'] = $_POST['societyId'];
		echo "Soc Id in ajax:".$_POST['societyId'];
		//return "Data Deleted Successfully";
	}
	if($_REQUEST["method"] == "deleteSMSQuota")
	{
		$id = $_REQUEST['SMSQuotaId'];
		$res = $obj_SMS->deleteSMSQuota($id);
		echo $res;
	}
}
?>
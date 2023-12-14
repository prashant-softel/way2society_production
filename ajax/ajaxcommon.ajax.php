<?php 
include_once("../classes/include/dbop.class.php");
include_once("../classes/utility.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$obj_notice = new notice($dbConn,$dbConnRoot);
$obj_Utility = new utility($dbConn, $dbConnRoot);

if($_REQUEST["method"]  == "SMSTest")
{
	$TestMobileNo = $_REQUEST['TestMobileNo'];
	$MsgBody = $_REQUEST['SMSTemplate'];
	echo $obj_Utility->SendDemoSMS($TestMobileNo, $MsgBody);
}
?>
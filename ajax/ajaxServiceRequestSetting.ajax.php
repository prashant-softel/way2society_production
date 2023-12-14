<?php 
include_once("../classes/include/dbop.class.php");
include_once("../classes/serviceRequestSetting.class.php");
$m_dbConn = new dbop();
$obj_setting = new serviceRequestSetting($m_dbConn);
if($_REQUEST['method'] == "SubmitRenovationDefaults")
{
	$result = $obj_setting->updateRenovationDefaults($_REQUEST['categoryId'],$_REQUEST['approvalLevel'],$_REQUEST['header'],$_REQUEST['footer'],$_REQUEST['termsCondition'],$_REQUEST['thankYouNote'],$_REQUEST['workList']);
	echo $result;
}
if($_REQUEST['method'] == "SubmitTenantDefaults")
{
	$result = $obj_setting->updateTenantDefaults($_REQUEST['categoryId'],$_REQUEST['approvalLevel'],$_REQUEST['header'],$_REQUEST['footer'],$_REQUEST['thankYouNote']);
	echo $result;
}
if($_REQUEST['method'] == "SubmitAddressProofDefaults")
{
	echo "in ajax :".$_REQUEST['footer'];
	$result = $obj_setting->updateAddressProofDefaults($_REQUEST['categoryId'],$_REQUEST['approvalLevel'],$_REQUEST['header'],$_REQUEST['footer'],$_REQUEST['thankYouNote']);
	echo $result;
}
?>

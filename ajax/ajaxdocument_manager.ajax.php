<?php 
include_once("../classes/include/dbop.class.php");
include_once("../classes/document_manager.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$obj_docmanager = new document_manager($dbConn,$dbConnRoot);

//echo $_REQUEST["method"]."@@@";
ob_clean();
if($_REQUEST["method"] == "updateNomination")
{
	$nomination_id = $_REQUEST["nomination_id"];
	$check_id=$_REQUEST["check_id"];
	$nomi_id = $obj_docmanager->updateNomination($nomination_id,$check_id);
	ob_clean();
	echo json_encode($nomi_id);
}
if($_REQUEST["method"] == "checkExitingMember")
{
	$member_id = $_REQUEST["member_id"];
	$nomination_status = $_REQUEST["nomination_status"];
	$member_details = $obj_docmanager->checkExitingMember($nomination_status,$member_id);
	echo json_encode($member_details);
}
?>
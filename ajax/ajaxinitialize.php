<?php
include_once("../classes/include/dbop.class.php");
include_once("../classes/dbconst.class.php");

$dbConn = new dbop();
$dbConnRoot= new dbop(true);
echo $_REQUEST["method"]."@@@";
if($_REQUEST['method']=="Update")
{
	$sqlJoin=" Select maptbl.id, concat_ws(' - ', societytbl.society_name, maptbl.desc) as societyName,maptbl.society_id,societytbl.society_name  from mapping as maptbl JOIN society as societytbl ON maptbl.society_id = societytbl.society_id JOIN dbname as db ON db.society_id = societytbl.society_id WHERE maptbl.login_id = '" . $_SESSION['login_id'] . "' and societytbl.status = 'Y' and maptbl.status = 2 and maptbl.id='".$_REQUEST['Id']."' ";
	
	$resJoin=$dbConnRoot->select($sqlJoin);
	
	$sqlSoc="SELECT * FROM `society` where society_id='".$resJoin[0]['society_id']."' and society_name='".$resJoin[0]['society_name']."'";
	$resSoc	=$dbConnRoot->select($sqlSoc);
	
	
	 $sqlUpdate="update society set Last_use_society_timestamp =NOW() where society_id= '".$resSoc[0]['society_id']."' and society_code='".$resSoc[0]['society_code']."' and client_id ='".$resSoc[0]['client_id']."' and dbname ='".$resSoc[0]['dbname']."'";

	 $resUpdate	=$dbConnRoot->Update($sqlUpdate);
	
}

?>
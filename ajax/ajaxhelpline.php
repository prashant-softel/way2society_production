<?php 
	include_once("../classes/helpline.class.php");
	include_once("../classes/include/dbop.class.php");
	
	$dbConn = new dbop();
	$Objhelpline = new helpline($dbConn);
	
	echo $_REQUEST["method"]."@@@";

	if($_REQUEST["method"]=="delete")
	{
		
		$Objhelpline->deleting($_REQUEST['requestId']);
		return "Data Deleted Successfully";
	}
?>
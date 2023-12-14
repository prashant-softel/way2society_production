<?php 
include_once("../classes/addLien.class.php");
include_once("../classes/include/dbop.class.php");
include_once("../classes/dbconst.class.php");
include_once("../classes/doc.class.php");
$dbConn = new dbop(); 
$obj_addLien = new addLien($dbConn);
$obj_doc=new document($dbConn);
if(isset($_REQUEST['method']))	
{
	echo $_REQUEST["method"]."@@@";
	if($_REQUEST["method"]=="deleteDocument")
	{
		$select_type = $obj_addLien->deleteDocument($_REQUEST['docId']);
		echo $select_type;
	}
}
?>
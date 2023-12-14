<?php
include_once("../classes/dbconst.class.php");
include_once("../classes/checkpost_master.class.php");
include_once("../classes/include/dbop.class.php");

$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$smConn = new dbop(false,false,true,false);
$smConnRoot = new dbop(false,false,false,true);

$smreport = new SM_Report($dbConn,$dbConnRoot,$smConn,$smConnRoot);

echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"] == "edit" )
{
    $select_type = $smreport->selecting($_REQUEST['id']);
    //echo($select_type);
    echo json_encode($select_type);
}

if($_REQUEST["method"]=="delete")
{   	
	    $sql="DELETE FROM checkpost_master WHERE id='".$_REQUEST['id']."'";
		$res=$smConn->delete($sql);
        return "Data Deleted Successfully";
}
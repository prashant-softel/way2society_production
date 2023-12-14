<?php 
include_once("../classes/include/dbop.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
include_once "../classes/changelog.class.php";
$obj_changeLog = new changeLog($dbConn, $dbConnRoot);

//echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="fetchLog")
{
	$res = $obj_changeLog->getLogDetails($_REQUEST);
	echo  $res;
}

?>
<?php
include_once("../classes/include/dbop.class.php");
include_once("../classes/dbconst.class.php");
include_once("../classes/tasks.class.php");
//include_once("../addtask.php");

include_once("../classes/utility.class.php");

$dbConn = new dbop();
$obj_tasks = new task($dbConn);
$objUtility = new utility($dbConn);
if($_REQUEST['update'] == 'percent')
{
	$TaskID = $_REQUEST["TaskID"];
	$status = $_REQUEST["status"];
	$Percentage = $_REQUEST["Percentage"];
	$response = $obj_tasks->UpdateTask($TaskID, $status, $Percentage);
	echo $response;
}
else if($_REQUEST['update'] == 'due')
{
	$TaskID = $_REQUEST["TaskID"];
	$due_date = getDBFormatDate($_REQUEST["DueDate"]);
	$response = $obj_tasks->UpdateTask_due($TaskID, $due_date);	
	echo $response;
}


?>
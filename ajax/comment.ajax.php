<?php 
include_once("../classes/addComment.class.php");
include_once("../classes/include/dbop.class.php");
include_once("../classes/dbconst.class.php");
$dbConn = new dbop(); 
$dbConnRoot = new dbop(true); 
$obj_Comment = new addComment($dbConn,$dbConnRoot);
if(isset($_REQUEST['method']))	
{
	if($_REQUEST["method"] == "deleteComment")
	{
		$Id = $_REQUEST['Id'];
		$res = $obj_Comment->deleteComment($Id);
		echo $res;
	}
}
?>

<?php include_once("../classes/momGroup.class.php");	
	include_once("../classes/include/dbop.class.php");
	$dbConn = new dbop();
	$obj_momGrp=new momGroup($dbConn);
	
	//echo $_REQUEST["method"]."@@@";
	//echo $_REQUEST["method"];
	if($_REQUEST["method"]=="edit") 
	{
		//echo "in edit:";
		$select_type = $obj_momGrp->selecting();
		//echo $select_type;
		//print_r ("in momgroup.ajax:".$select_type);
		foreach($select_type as $k => $v)
		{
				echo $v."#";
		}
	}
	if($_REQUEST["method"]=="delete")
	{
		$gId=$_REQUEST['gId'];
		echo "Ajax:".$gId;
		$res=$obj_momGrp->deleting($gId);
		echo "<br>:".$res;
	}
?>
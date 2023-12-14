<?php	
include_once("../classes/include/dbop.class.php");
include_once("../classes/SM_report.class.php");
//include_once("classes/dbconst.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$smConn = new dbop(false,false,true,false);
$smConnRoot = new dbop(false,false,false,true);
$ObjSMReport = new SM_Report($dbConn,$dbConnRoot,$smConn,$smConnRoot);
$validator = $ObjSMReport->startProcess();
//echo "Validator :".$validator;
	///$actionPage = "";
	//if(isset($_REQUEST['vr']))
//	{			
		//$validator = $ObjSMReport->AddExpectedVistor($_REQUEST['vr'], $_POST['emailID'],$_POST['SREmailIDs']);
	//	$actionPage = "../viewrequest.php?rq=".$_REQUEST['vr'];
	//}
	
	
?>
<html>
<body>

<form name="Goback" method="post" action="<?php echo $ObjSMReport->actionPage; ?>">

	<?php

	if($validator=="Insert")
	{
	$ShowData="Record Added Successfully";
	}
	else if($validator=="Approve")
	{
	$ShowData="Record Updated Successfully";
	}
	else if($validator=="Denite")
	{
	$ShowData="Record Deleted Successfully";
	}
	else
	{
		foreach($_POST as $key=>$value)
		{
		echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
		}
		$ShowData=$validator;
	}
	?>

<input type="hidden" name="ShowData" value="<?php echo $ShowData; ?>">
<input type="hidden" name="ev">
</form>
<script>
	document.Goback.submit();
</script>
</body>
</html>
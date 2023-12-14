<?php	
		include_once("../classes/parkingType.class.php");
		include_once("../classes/include/dbop.class.php");
	 	$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$obj_parkingType=new parkingType($dbConn,$dbConnRoot);
		$validator = $obj_parkingType->startProcess();
?>
<html>
<body>
<form name="Goback" method="post" action="<?php echo $obj_parkingType->actionPage; ?>">

	<?php

	if($validator=="Insert")
	{
	$ShowData="Record Added Successfully";
	}
	else if($validator=="Update")
	{
	$ShowData="Record Updated Successfully";
	//echo $ShowData;
	}
	else if($validator=="Delete")
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
<input type="hidden" name="period_id" value="<?php echo $_REQUEST['period_id']; ?>"> 
<input type="hidden" name="bill_type" value="<?php echo $_REQUEST['bill_type']; ?>">    
</form>
<script>
	document.Goback.submit();
</script>
</body>
</html>

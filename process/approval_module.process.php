<?php 
include_once("../classes/approval_module.class.php");
include_once("../classes/include/dbop.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$obj_approval=new approval_module($dbConn, $dbConnRoot);
$validator = $obj_approval->startProcess();
	
echo "validator" .$validator;		
?>

<html>
<body>
<font color="#FF0000" size="+2">Please Wait...</font>

<form name="Goback" method="post" action="<?php echo $obj_approval_module->actionPage; ?>">

	<?php
	
	
	if($validator=="Insert")
	{
		
		$ShowData = "Record Added Successfully";
		
	}
	else if($validator=="Update")
	{
		$ShowData = "Record Updated Successfully";
	}
	else if($validator=="Delete")
	{
		$ShowData = "Record Deleted Successfully";
	}
	else
	{
		foreach($_POST as $key=>$value)
		{
			echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
		}
		$ShowData = $validator;
	}
	?>

<input type="hidden" name="ShowData" value="<?php echo $ShowData; ?>">
</form>

<script>
	document.Goback.submit();
</script>

</body>
</html>

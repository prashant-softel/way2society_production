<html>
<body>
<?php

    include_once("../classes/checkpost_master.class.php");
 	include_once("../classes/include/dbop.class.php");

    $dbConn = new dbop();
    $dbConnRoot = new dbop(true);
    $smConn = new dbop(false,false,true,false);
    $smConnRoot = new dbop(false,false,false,true);
    $smreport = new SM_Report($dbConn,$dbConnRoot,$smConn,$smConnRoot);
	$validator = $smreport->startProcess();
?>

<form name="Goback" method="post" action="<?php echo $smreport->actionPage; ?>">

	<?php

	if($validator=="Insert")
	{
	$ShowData="Record Added Successfully";
	}
	else if($validator=="Update")
	{
	$ShowData="Record Updated Successfully";
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
</form>
<script>
	document.Goback.submit();
</script>
</body>
</html>

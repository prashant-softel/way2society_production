<?php	include_once("../classes/soc_note1.class.php");
		include_once("../classes/include/dbop.class.php");
	  	$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$obj_society_notes=new soc_note1($dbConn, $dbConnRoot);
		$validator = $obj_society_notes->addNotes();
?>
<html>
<body>

<form name="Goback" method="post" action="<?php echo $obj_society_notes->actionPage; ?>">

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
<input type="hidden" name="nt">
</form>
<script>
	document.Goback.submit();
</script>
</body>
</html>
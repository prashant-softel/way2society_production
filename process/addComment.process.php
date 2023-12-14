<?php 
include_once("../classes/addComment.class.php");
include_once("../classes/include/dbop.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$obj_addComment = new addComment($dbConn, $dbConnRoot);
$validator = $obj_addComment->startProcess();
echo $validator;
//echo $obj_addComment->actionPage;
?>

<html>
<body>
<font color="#FF0000" size="+2">Please Wait...</font>

<form name="Goback" method="post" action="<?php echo $obj_addComment->actionPage; ?>">
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

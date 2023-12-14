<?php include_once "ses_set_as.php"; ?>
<?php
 // Turn off all error reporting
        error_reporting(0);
if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else
{
	include_once("includes/head_s.php");
}

include_once("classes/member_import.class.php");
$obj_member_import = new member_import($m_dbConn);
$obj_member_import=$obj_member_import->CSVMemberImport();
//print_r($obj_unit_import);
$_SESSION['society_id']=$_REQUEST['sid'];
//echo $_SESSION['society_id'];

?>

<html>
<head>

</head>



<center><font color="#43729F" size="+1"><b>Import Members</b></font></center>
<br>

<center>
<a href="unit.php?imp" style="color:#00F; text-decoration:none;"><b><u>Back to list</u></b></a>
</center>
<body>

<form name="" action="" method="post" enctype="multipart/form-data">
<center>
<table>
<tr>
<td><label for="file">Filename:</label></td>
<td><input type="file" name="file" id="file" /></td>
<BR/>
<BR/>

<tr>
<td><input type="submit" name="Import" value="Import" /></td>
</tr>
</tr>
</table>
</center>
</form>

<?php include_once "includes/foot.php"; ?>
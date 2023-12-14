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

include_once("classes/member_dues_import.class.php");
$obj_member_dues_import = new member_dues_import($m_dbConn);
$obj_member_import=$obj_member_dues_import->CSVMeberDuesImport();
//print_r($obj_unit_import);
?>

<html>
<head>

</head>



<center><font color="#43729F" size="+1"><b>Import Member's Dues</b></font></center>
<br>
<body>

<form name="" action="" method="post" enctype="multipart/form-data">
<center>
<table>
<tr><td>Enter Date:</td><td><input type="text" name="date"/></td></tr>
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
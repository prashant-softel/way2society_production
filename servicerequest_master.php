<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Service Request Category</title>
</head>

<?php include_once "ses_set_s.php"; ?>
<?php
if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else
{
	include_once("includes/head_s.php");
}
?>
<?php
include_once("classes/servicerequest_master.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$obj_category = new serviceRequest_Category($dbConn,$dbConnRoot);

?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >    
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsservicecategory_20190516.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {		
		document.getElementById('error').style.display = 'block';
        setTimeout('hide_error()',8000);	
    }
    function hide_error()
    {		
		document.getElementById('error').innerHTML = '';
        document.getElementById('error').style.display = 'none';
    }
	</script>
</head>
<body>
<center>
<br>
<div class="panel panel-info" id="panel" style="display:none">
<div class="panel-heading" id="pageheader">Service Request Category</div>        

<?php
$star = "<font color='#FF0000'>*</font>";
if(isset($_REQUEST['msg']))
{
	$msg = "Sorry !!! You can't delete it. ( Dependency )";
}
else if(isset($_REQUEST['msg1']))
{
	$msg = "Deleted Successfully.";
}
else{}
?>
<table align="center">
<tr>
<td>
<?php
echo "<br>";
echo $str1 = $obj_category->pgnation();

?>
</td>
</tr>
</table>
<br />
<div style="border-radius:20px; border:1px solid  #CCC; width:400px;">
<center>
<form name="service_category" id="service_category" method="post" action="process/servicerequest_master.process.php" onSubmit="return validate();">

<table align='center' >
<?php
if(isset($msg))
{
	if(isset($_POST['ShowData']))
	{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
	}
	else
	{
	?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $msg; ?></b></font></td></tr>
	<?php
	}
}
else
{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
}
?>


		<tr>
			<td>Category Name: </td>
			<td><input type="text" name="category" id="category"/></td>
		</tr>
        <tr>
        	<td>Assigned Member : </td>
            <td>
            	<select name="member" id="member" onChange="getEmailID(this.value,0);">
                	<?php echo $combo_member = $obj_category->combobox( "select mo.mem_other_family_id, CONCAT(CONCAT(u.unit_no,' - '), CONCAT(mo.other_name, IF(mo.coowner = 1, ' (Owner)', ' (Co-Owner)'))) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit JOIN mem_other_family as mo ON mm.member_id = mo.member_id where u.society_id = '" . DEFAULT_SOCIETY . "' and mm.ownership_status = 1 and mo.status = 'Y' and mo.coowner > 0 ORDER BY u.sort_order, mo.coowner", 0); ?>
                </select>
            </td>
        </tr>        
		<tr>
			<td>Assigned Member's Email id : </td>
			<td><input type="text" name="email" id="email" readonly style="background-color:#CCC;" /></td>
		</tr>
        <tr>
        	<td>Co - Assigned Member : </td>
            <td>
            	<select name="co-member" id="co-member" onChange="getEmailID(this.value,1);">
                	<?php echo $combo_member = $obj_category->combobox( "select mo.mem_other_family_id, CONCAT(CONCAT(u.unit_no,' - '), CONCAT(mo.other_name, IF(mo.coowner = 1, ' (Owner)', ' (Co-Owner)'))) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit JOIN mem_other_family as mo ON mm.member_id = mo.member_id where u.society_id = '" . DEFAULT_SOCIETY . "' and mm.ownership_status = 1 and mo.status = 'Y' and mo.coowner > 0 ORDER BY u.sort_order, mo.coowner", 0); ?>
                </select>
            </td>
        </tr>
        <tr>
			<td>Co - Assigned Member's Email id : </td>
			<td><input type="text" name="email1" id="email1" readonly style="background-color:#CCC;" /></td>
		</tr>
        
        <tr>
			<td>Email id [CC] : </td>
			<td><input type="text" name="email_cc" id="email_cc"  /></td>
		</tr>
        <tr>
        	<td>Contractor: </td>
            <td>
            	<select name="contractor" id="contractor">
                	<?php echo $obj_category->comboboxcontractor("SELECT l.login_id,l.name FROM `login` as l join mapping as m on m.login_id=l.login_id  where role='Contractor'", 0); ?>
                </select>
            </td>
        </tr>

        <tr>
        	<td>Visible in Service Request?</td>
            <td><input type="checkbox" id="check" name="check" /></td>
        </tr>
		<tr align="center"><td colspan="2" align="center"><br></td> </tr>
        <tr>
			<td colspan="2" align="center"><input type="hidden" name="id" id="id"><input type="submit" name="insert" id="insert" value="Insert" class="btn btn-primary"  style="padding: 6px 12px; color:#fff;background-color: #2e6da4;"></td>
		</tr>
        <tr align="center"><td colspan="2" align="center"><br></td> </tr>
</table>
</form>

</center>
</div>
<br>
</center>
</div>
<?php include_once "includes/foot.php"; ?>
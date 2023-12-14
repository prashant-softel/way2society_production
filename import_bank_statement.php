
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Import Ban Statement</title>
</head>


<?php if(!isset($_SESSION)){ session_start(); } ?>
<?php
	include_once("includes/head_s.php");
	include_once("classes/utility.class.php");
	include_once("classes/include/dbop.class.php");
	$m_dbConn = new dbop();
	$obj_utility = new utility($m_dbConn);
?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<script type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript" src="js/jquery_min.js"></script>
<script type="text/javascript" src="js/populateData.js"></script>
<script type="text/javascript">

$(document).ready(

	function(){
		$('input:submit').attr('disabled',true);
		$('input:file').change(
		function(){
			if ($(this).val())
			{
				$('input:submit').removeAttr('disabled'); 
			}
			else 
			{
				$('input:submit').attr('disabled',true);
			}
		});
});
    
</script>

<script language="javascript" type="text/javascript">
function go_error()
{
	setTimeout('hide_error()',10000);	
}
function hide_error()
{
	document.getElementById('error').style.display = 'none';
}

function doSubmitForm()
{
	if(document.getElementById('bank_id').value == 0 || document.getElementById('bank_id').value == '')
	{
		alert("Please Select Bank Name");
		return false;
	}
}
    
</script>
</head>

<body onLoad="go_error();">

<form name="import_bank_statement_preview" action="display_data.php" method="post" enctype="multipart/form-data" onSubmit="return doSubmitForm()" >


<!-- <form name="import_ledger" action="process/import_ledger.process_old.php" method="post" enctype="multipart/form-data" > -->

<center>
<br>
<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Import Bank Statement</div>
<div id="right_menu">
<table>

<?php
if(isset($_POST["ShowData"]))
{
	?>
    <tr height="30"><td colspan=5 style="text-align:center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
<?php } ?> 
<strong><div id="show" style="text-align:center; width:100%; color:#FF0000"><?php //echo $show_op; ?></div></strong>
<!--<tr height="50" align="center"><td>&nbsp;</td><th colspan="3" align="center"><table align="center"><tr height="25"><th bgcolor="#CCCCCC" width="180">For Society Admin Login</th></tr></table></th></tr>-->
<BR/>
<BR/>	

</tr>        
      <tr>
      <td>&nbsp;&nbsp;&nbsp;</td>
	  </tr>

<tr align="left">
    <td valign="middle"></td>
    <td>Select Bank </td>
    <td>&nbsp; : &nbsp;</td>
    <td>
           <select name="bank_id" id="bank_id"><?php echo $obj_utility->BankComboBox();?></select>
    </td>
</tr> 
</tr>        
      <tr>
      <td>&nbsp;&nbsp;&nbsp;</td>
	  </tr>
      
</tr>        
      <tr>
      <td>&nbsp;&nbsp;&nbsp;</td>
	  </tr>

<tr align="left">
    <td valign="middle"></td>
    <td>Browse File To Import</td>
    <td>&nbsp; : &nbsp;</td>
    <td id="browse">
            <input type="file" name="file[]" id="file" required multiple/>
    </td>
</tr>   
<tr>
	<td colspan="4">&nbsp;</td>
</tr>
<tr height="50" align="center">
    <td colspan="4" align="center">

    <input type="hidden" name="flag" id="flag" value="11">
    <input type="submit" name="Upload" id="Upload" value="Upload" disabled on class="btn btn-primary" /></td>
</tr>
</table>
    <span>Bank Statement Sample File : <a href="samplefile/BankStatement_Sample.csv" download>Click here to Download</a> </span>
</center>
</div>
</div>
    </form>
<?php include_once "includes/foot.php"; ?>
</body>
</html>
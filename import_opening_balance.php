
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Import Opening Balance</title>
</head>

<?php if(!isset($_SESSION)){ session_start(); } ?>
<?php
 	include_once("includes/head_s.php");
	

if(isset($_REQUEST['type']))
{
	//echo $_REQUEST['type'];
}

//echo "<pre>";print_r($_SESSION);
?>


<html>
<head>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<script type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/populateData.js"></script>
<script type="text/javascript">

$(document).ready(

	function(){
		$('input:submit').attr('disabled',true);
		$('input:file').change(
		function(){
			if($(this).val() != '')
			{
				if('<?php echo $_SESSION['is_year_freeze'] ?>' == 0)
				{
					var filename = $('input[type=file]').val().split('\\').pop();
					var fileExtension = ['csv', 'txt'];
					if($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) 
					{
						$('input:submit').attr('disabled',true);
						alert("Only formats are allowed : "+ fileExtension.join(', '));
					}
					else
					{
						$('input:submit').removeAttr('disabled');
					}
				}
				else
				{
					$('input:submit').attr('disabled',true);
				}
				
			}
			else 
			{
				alert('Please Select "FixedDeposit.csv" File');
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
			

</script>
</head>




<body onLoad="go_error();">


<form name="importfixeddepositform" action="process/import_opening_balance.process.php" method="post" enctype="multipart/form-data" />

<center>
<br>
<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Import Opening Balance</div>
       
<div id="right_menu">
<table>

<?php
if(isset($_POST["ShowData"]))
{
	?>
    <tr height="30"><td colspan=5 style="text-align:center"><font color="red" style="size:11px; "><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
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
			<td>Browse File To Import</td>
            <td>&nbsp; : &nbsp;</td>
			<td id="browse"><input type="file" name="upload_files[]" id="file"/><br/></td>
            
</tr>   

<tr align="center">
 <td colspan="4" align="center"><input type="submit" name="Import" value="Import"  disabled  class="btn btn-primary" style="width:100px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;color: #fff;background-color: #337ab7;border-color: #2e6da4;"/></td>
</tr>

<input type="hidden" name="sid" value="<?php echo $_SESSION['society_id'];?>">
</table><br/>
<div style="color:#FF0000">* File Name Should be ".csv/".txt(comma delimited file)"</div><br/>

<!--<span>Member Opening Balance Sample File : --><a href="samplefile/Member_Opening_Bal.csv"  class="btn btn-primary" style="width:200px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal; color: #fff; background-color: #337ab7; border-color: #2e6da4;" download> Download Sample </a><br/>
<br/>
</center>
</form>
</div>

</div>
<?php include_once "includes/foot.php"; ?>
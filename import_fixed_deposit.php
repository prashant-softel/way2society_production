
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Fixed Deposit</title>
</head>




<?php if(!isset($_SESSION)){ session_start(); } ?>
<?php 
	include_once("includes/head_s.php");
	include_once("classes/dbconst.class.php");


	if(isset($_REQUEST['Note']))
	{
		$flag = 10;
		$NoteType = 0;
		if($_REQUEST['Note'] == CREDIT_NOTE)
		{
			$Head = "Import Credit Note";
			$csv_file = "Credit_Note.csv";
			$text_file = "Credit_Note.txt";
			$NoteType = CREDIT_NOTE;
		}
		else if($_REQUEST['Note'] == DEBIT_NOTE)
		{
			$Head = "Import Debit Note";
			$csv_file = "Debit_Note.csv";
			$text_file = "Debit_Note.txt";
			$NoteType = DEBIT_NOTE;
		}
		
	}
	else
	{
		$Head = "Import Fixed Deposit ";
		$csv_file = "fd_import.csv";
		$text_file = "fd_import.txt";
		$flag = 13;
		$CreditNoteSet = 0;
		
	}
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
			if('<?php echo $_SESSION['is_year_freeze'] ?>' == 0)
			{
				if ($(this).val())
				{
					$('input:submit').removeAttr('disabled'); 
				}
				else 
				{
					$('input:submit').attr('disabled',true);
				}
			}
			else 
			{
				$('input:submit').attr('disabled',true);
			}
		});
});
    
$(document).ready(

	function(){
		
		document.getElementById('Download').disabled = false;
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

<form name="display_data" action="display_data.php" method="post" enctype="multipart/form-data" >

<center>
<br>
<div class="panel panel-info" id="panel" style="display:none">
<div class="panel-heading" id="pageheader"><?php echo $Head; ?></div>
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
			<td>Browse File To Import</td>
            <td>&nbsp; : &nbsp;</td>
			<td id="browse">
				    <input type="file" name="file[]" id="file" />
            </td>
</tr>   
<tr><td colspan="4">&nbsp;</td></tr>
<tr height="50" align="center">
 <td colspan="4" align="center">
 	<input type="hidden" name="flag" value="<?php echo $flag;?>">
    <input type="hidden" name="NoteType" value="<?php echo $NoteType;?>">
     <input type="submit" name="Upload" id="Upload" value="Import" disabled on class="btn btn-primary"  accept="application/msexcel"/></td>
</tr>
<input type="hidden" name="sid" value="<?php echo $_SESSION['society_id'];?>">
</table> 
	<!--<span style="color:red;">Date should be in YYYY-MM-DD format</span><br>-->
 <!--Note is set for Credit Note-->   

<!-- <div style="color:#FF0000">Please Download this template file and fill the data in appropriate column.</div> -->

<!--<span>Please Download this template file and fill the data in appropriate columns.--><a href="samplefile/Import_fd.csv" class="btn btn-primary" style="width:200px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal; color: #fff; background-color: #337ab7; border-color: #2e6da4;" download> Download Sample </a> <br/>

<!-- Download Sample Invoice File-->

</form>
<br>
<!-- <form id="downloadform" name="downloadform" action="process/download_invoice.process.php" method="post" style:"padding-bottom:50px;">
<input type="hidden" name="NoteType" value="<?php //echo $NoteType;?>">
<input type="submit" id="Download" name="Download" value="Download Template" class="btn btn-primary" style="width:200px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal; color: #fff; background-color: #337ab7; border-color: #2e6da4;"/>
<br>&nbsp; &nbsp;</form> -->
</center>

</div>

</center>
</div>
</div>
 </form>
<?php include_once "includes/foot.php"; ?>
</body>

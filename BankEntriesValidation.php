<?php 
include_once ("classes/include/dbop.class.php");
include_once("includes/head_s.php"); 
	if(!isset($_SESSION['society_id']))
	{
	?>
		<script>
            alert('Please Login.');
            window.location.href = 'logout.php?alog';
        </script>   
     <?php
	}
	
	include_once ("classes/BankEntriesValidation.class.php");
	$dbConn = new dbop();
	$obj_BankEntriesValidation=new BankEntriesValidation($dbConn);
	$SocietyName=$obj_BankEntriesValidation->getSocietyDetails($_SESSION['society_id']);
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>BANK REGISTER ENTRIES VALIDATION REPORT</title>
<script type="text/javascript" src="js/ajax.js"></script>
<script tytpe="text/javascript" src="js/ajax_new.js"></script>
<script tytpe="text/javascript" src="js/jquery-2.0.3.min.js"></script>
<script type="text/javascript">
function ajaxRunCleaner()
{
	//alert("ajaxRunCleaner");
	$.ajax({
				url: "ajax/ajaxBankEntriesValidation.php",
				method: "POST",
				data: {
				   cleanInvalidEntries:'YES',
				   method:"run"
				},
				success : function(data)
				{
					if(data == 'success')
					{
						alert("Invalid Records Clean Successfully.");
					}
					else
					{
						alert("Unable to create backup of database.");
					}
					location.reload(true);
				},
				fail: function()
				{
					alert("Failed");
				}
		 });		
	
}
</script>
<style>
.bankentries
{
	font-size:12px;
}
.banktbl ,.banktbl th ,.banktbl td
{
	border:1px solid black !important;	
	
}
.banktbl td,.banktbl th
{
	padding:5px;	
	
}  
</style>
</head>

<body >
<!--<center><h1><?php //echo $SocietyName;?></h1></center>-->
<div class="panel panel-info" id="panel" style="display:block">
<div class="panel-heading" id="pageheader">BANK REGISTER ENTRIES VALIDATION REPORT</div>
<!--<center><h2>BANK REGISTER ENTRIES VALIDATION REPORT</h2></center>-->
<br />
<form name="cleanerForm" method="post">
<input type="button" name="RunCleaner" class="btn btn-primary" value="Run Cleaner"  onclick="ajaxRunCleaner()" style="display:block;"/>
</form>
<center>
<table style="width:45%; border:1px solid black; background-color:transparent;">
	<tr> <td colspan="3"><br/> </td></tr>
    <tr>
        <td> &nbsp; Display Type : </td>            
        <td>
            <select name="classType" id="classType" onChange="ChangeClass();">				                                       	
                <option value="0"> All </option>
                <option value="1"> Valid Entries </option>
                <option value="2"> Errors </option>  
                <option value="3"> Warnings </option>               
           </select>
        </td>        
    </tr>        
    <tr> <td colspan="3"><br/> </td></tr>
</table>
</center>

<div class="bankentries">
 <font color='#FF0000'>  ** Scanned <label id="totalEntries"></label>  entries ,<label id="invalidEntries"></label> are invalid. ** </font>
<?php 
	
	$obj_BankEntriesValidation->FetchRecord();
		
?>
</div>
<script>
	function ChangeClass()
	{
		var val = document.getElementById('classType').value;
		var errorClassValues = document.getElementsByClassName('error');
		var warningClassValues = document.getElementsByClassName('warning');
		var validClassValues = document.getElementsByClassName('validEntry');
		document.getElementById("totalEntries").innerHTML = parseInt(validClassValues.length) + parseInt(errorClassValues.length);
		document.getElementById("invalidEntries").innerHTML = parseInt(errorClassValues.length);
		if(val == 1)
		{
			for (var i = 0; i < validClassValues.length; i ++) {
				validClassValues[i].style.display = 'block';
			}
			
			for (var i = 0; i < errorClassValues.length; i ++) {
				errorClassValues[i].style.display = 'none';
				
			}
			for (var i = 0; i < warningClassValues.length; i ++) {
				warningClassValues[i].style.display = 'none';
				
			}
			
		}
		else if(val == 2)
		{
			for (var i = 0; i < validClassValues.length; i ++) {
				validClassValues[i].style.display = 'none';
				
			}
			
			for (var i = 0; i < errorClassValues.length; i ++) {
				errorClassValues[i].style.display = 'block';
				
			}
			for (var i = 0; i < warningClassValues.length; i ++) {
				warningClassValues[i].style.display = 'none';
				
			}
		}
		else if(val == 3)
		{
			//alert(val);
			for (var i = 0; i < validClassValues.length; i ++) {
				validClassValues[i].style.display = 'none';
			}
			
			for (var i = 0; i < warningClassValues.length; i ++) {
				warningClassValues[i].style.display = 'block';
				
			}
			for (var i = 0; i < errorClassValues.length; i ++) {
				errorClassValues[i].style.display = 'none';
				
			}
		}
		else
		{
			for (var i = 0; i < validClassValues.length; i ++) {
				validClassValues[i].style.display = 'block';
			}
			
			for (var i = 0; i < errorClassValues.length; i ++) {
				errorClassValues[i].style.display = 'block';
			}
			for (var i = 0; i < warningClassValues.length; i ++) {
				warningClassValues[i].style.display = 'block';
			}
		}
	}
	ChangeClass();
</script>
</div>        
<?php include_once "includes/foot.php"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Fixed Asset Management</title>
</head>

<?php //include_once "ses_set_s.php"; ?>
<?php
	include_once("includes/head_s.php");
?>
<?php
include_once("classes/FA_Depreciation.class.php");
$obj_fa_dep = new fa_dep($m_dbConn);
$startdate = $obj_fa_dep->FetchDate($_SESSION['default_year']); 
include_once("classes/dbconst.class.php");

$ifset = $obj_fa_dep->checkIfDefaultSet();
if($ifset == 0)
{
	?>
    <script>
		alert('Please Set Default Value For Fixed Asset.');
		window.location.href = 'defaults.php';
	</script>
    <?php
}

?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <script type="text/javascript" src="js/populateData.js"></script>
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/account_subcategory.js"></script>
    <script language="javascript" type="application/javascript">
	minStartDate = '<?php echo getDisplayFormatDate($_SESSION['default_year_start_date']);?>';
   $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
			defaultDate: new Date(),
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true 
        })});
	function go_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
		});
        setTimeout('hide_error()',8000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }
	
	</script>
    <script>
	function addNew()
	{
		document.getElementById('new_entry').style.display = 'block';
		document.getElementById('btnAdd').style.display = 'none';
		document.getElementById("btnPrintAll").style.display = 'none';
		document.getElementById('brtag').style.display = "none";
	}
	
	function onCancel()
	{
		document.getElementById('new_entry').style.display = 'none';
		document.getElementById('btnAdd').style.display = 'block';		
	}
	
	function update_all_fa()
	{
		$.ajax({
		url : "ajax/ajaxFixedAssetDep.ajax.php",
		type : "POST",
		dataType : "json",
		data: {"method":"update_all"},
		success: function(data)
		{
			//alert(data);
			//var my_array = JSON.parse(data);
			var result = data.split('@@@');
			alert("Test2");
			//alert(result.count);

			alert(my_array[0]);
			alert(my_array[1]);
			for(var i = 0; i < my_array.length; i++)
			{
				alert(my_array[0][i]);
			}
		}
		});
	}
	
	var isSadmin = false; 
	<?php
		if($_SESSION['role'] && ($_SESSION['role']==ROLE_SUPER_ADMIN))
		{ ?>
		isSadmin = true;
	<?php } ?>
</script>
</head>

<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg'])){ ?>
<body onLoad="go_error();">
<?php } ?>

<body>
<br>
<center>
<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Fixed Asset Management</div>
        <br>

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
<center>
<!--<button type="button" class="btn btn-primary" onclick="window.location.href='ledger_details.php?imp'">Ledger Details</button>-->
<!--<button type="button" class="btn btn-primary" onClick="addNew();" id="btnAdd" >Add New Ledger</button>
<button type="button" class="btn btn-primary" onClick="window.open('multiple_ledger_print.php');"  id="btnPrintAll">Print Multiple Ledger</button>
<button type="button" class="btn btn-primary" onClick="window.open('account_category.php', '_blank')" id="">Manage Categories</button>
-->
<div id="brtag"><br/></div>
</center>
<!--<div id="new_entry" style="display:none;">
<form name="account_subcategory" id="account_subcategory" method="post" action="process/account_subcategory.process.php" onSubmit="return val();">
 

</form>
</div>-->
<form>
<table align="center">
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_fa_dep->pgnation();
echo "<br>";
?>
</td>
</tr>
</table>
<input type="button" id="update_all" name="update_all" value="Apply Depreciation To All Ledgers" onClick="update_all_fa();" class="btn btn-primary" />
</form>
<br>
</center>
</div>

<?php include_once "includes/foot.php"; ?>
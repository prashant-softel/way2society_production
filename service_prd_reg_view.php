<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - List of Service Provider</title>
</head>

<?php //include_once "ses_set_common.php"; ?>
<?php

/*if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else if(isset($_SESSION['sadmin']))
{*/
	include_once("includes/head_s.php");
	include_once("classes/dbconst.class.php");
/*}
else
{
	include_once("includes/header_m.php");
}
*/
include_once("classes/service_prd_reg.class.php");

$obj_service_prd_reg = new service_prd_reg($m_dbConn, $m_dbConnRoot);
?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/jsservice_prd_reg.js"></script>
</head>

<script language="javascript" type="application/javascript">
var aryServiceRegID=[];
function go_error()
{
	setTimeout('hide_error()',10000);	
}
function hide_error()
{
	document.getElementById('error').style.display = 'none';	
}
</script>

<!------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------->

	<!-- Add jQuery library -->
	

	<!-- Add mousewheel plugin (this is optional) -->
	<script type="text/javascript" src="lib/jquery.mousewheel-3.0.6.pack.js"></script>

	<!-- Add fancyBox main JS and CSS files -->
	<script type="text/javascript" src="source/jquery.fancybox.js?v=2.0.6"></script>
	<link rel="stylesheet" type="text/css" href="source/jquery.fancybox.css?v=2.0.6" media="screen" />

	<!-- Add Button helper (this is optional) -->
	<link rel="stylesheet" type="text/css" href="source/helpers/jquery.fancybox-buttons.css?v=1.0.2" />
	<script type="text/javascript" src="source/helpers/jquery.fancybox-buttons.js?v=1.0.2"></script>

	<!-- Add Thumbnail helper (this is optional) -->
	<link rel="stylesheet" type="text/css" href="source/helpers/jquery.fancybox-thumbs.css?v=1.0.2" />
	<script type="text/javascript" src="source/helpers/jquery.fancybox-thumbs.js?v=1.0.2"></script>

	<!-- Add Media helper (this is optional) -->
	<script type="text/javascript" src="source/helpers/jquery.fancybox-media.js?v=1.0.0"></script>
    
    <script type="text/javascript">
		$(document).ready(function() {			
		
		//Simple image gallery. Uses default settings
		$('.fancybox').fancybox();
		$('.fancybox1').fancybox();
	
		document.getElementById('example').style.width = '70%';

		//Button helper. Disable animations, hide close button, change title type and content
		$('.fancybox-buttons').fancybox({
		openEffect  : 'none',
		closeEffect : 'none',

		prevEffect : 'none',
		nextEffect : 'none',

		closeBtn  : false,

		helpers : {
			title : {
				type : 'inside'
			},
			buttons	: {}
		},

			afterLoad : function() {
				this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
			}
		});
		});
	</script>
	<style type="text/css">
		.fancybox-custom .fancybox-skin 
		{
			box-shadow: 0 0 50px #222;
		}
	</style>
<!------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------->

</head>

<?php if(isset($_GET['del']) || isset($_GET['add']) || isset($_GET['up'])){?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<br>
<div id="middle">
<div class="panel panel-info" id="panel" style="display:block;width: 76%;margin-left: 1%;">
        <div class="panel-heading" id="pageheader">List of Service Provider</div>
<br>
<center>

<button type="button" class="btn btn-primary" onclick="window.location.href='service_prd_reg.php?srm'">New Registration </button>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<!--<a href="service_prd_reg_view_other.php?srm" style="color:#00F; text-decoration:none;"><b><u>View others</u></b></a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->

<button type="button" class="btn btn-primary" onClick="window.location.href='service_prd_reg_search.php?srm'">Search</button>
 
<?php if($_SESSION['role'] && $_SESSION['is_year_freeze'] == 0 && ($_SESSION['role']==ROLE_SUPER_ADMIN|| $_SESSION['profile']['#srv_prd.php'] == '1'))
{?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<button type="button" class="btn btn-primary" value="Print Selected Id Cards" onClick="CheckServiceRequest(this)">Print Selected Id Cards</button>
<?php }?>
<?php if(isset($_REQUEST['search'])){?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="service_prd_reg_view.php?srm" style="color:#00F; text-decoration:none;"><b><u>View all records</u></b></a>
<?php }?>
</center>

<center>
<?php
if(isset($_GET['del']) || isset($_GET['add']) || isset($_GET['up'])){
?>
<br>
<table align="center" border="0">
<tr>
	<td valign="top" align="center"><font color="red"><?php if(isset($_GET['del'])){echo "<b id=error>Record deleted successfully</b>";}else if(isset($_GET['add'])){echo '<b id=error>Record added successfully</b>';}else if(isset($_GET['up'])){echo '<b id=error>Record updated successfully</b>';}else{} ?></font></td>
</tr>
</table>
<?php } ?>

<table align="center" border="0">
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_service_prd_reg->pgnation();
/*echo "<br>";
echo $str = $obj_service_prd_reg->display1($str1);
echo "<br>";
$str1 = $obj_service_prd_reg->pgnation();
echo "<br>";*/
?>
</td>
</tr>
</table>

</center>
</div>
<?php include_once "includes/foot.php"; ?>
<script> 
	//VoutherNumber.push("<?php// echo $data->sID.'@@@'.$data->sVoucherTypeID; ?>");
	//	
	//alert(aryServiceRegID);	
    function SelectAllPrintIDCard(objServiceID)
	{	
	//alert(objServiceID.checked);
		var table = $('#example').DataTable();
	table.page.len( -1 ).draw();

	for(var iCount = 0; iCount <  aryServiceRegID.length; iCount++)
	{
		//alert(aryUnit[iUnits].unit);
		document.getElementById('check_' + aryServiceRegID[iCount]).checked = objServiceID.checked;
		
		
		}
	}
		
	function CheckServiceRequest()
{
	var table = $('#example').DataTable();
	table.page.len( -1 ).draw();

	var aryServiceReqCheck=[];
	//alert(chkBox.Voucher+ ":" + chkBox.checked);
	for(var iCount = 0; iCount <  aryServiceRegID.length; iCount++)
	{
		var sKey = 'check_' + aryServiceRegID[iCount];
		//alert(sKey);
		var sVal = document.getElementById(sKey).checked;
		//alert(sVal);
		//sKey = 'amt_' + sKey;
		//sBtn = 'btn_' + unitID;
		
		if(sVal== true)
		{
			//document.getElementById(sKey).disabled = false;
			aryServiceReqCheck.push(aryServiceRegID[iCount]);
			//alert("test");
		}
		

		//alert(aryUnit[iUnits].unit);
		//document.getElementById('check_' + aryVoucherNumber[iVoucher]).checked = objVoucher.checked;
		
		
		}
		
		if(aryServiceReqCheck.length > 0)
		{
		//alert("hi");
			 window.open("all_printcert.php?serId=" + JSON.stringify(aryServiceReqCheck));
		}
		else{
					alert("Please select check box...");			
			}
		
}
    </script>
    

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Member Records Status</title>
</head>

<?php include_once "ses_set_as.php"; ?>
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
include_once("classes/mem_rem_data.class.php");
$obj_mem_rem_data = new mem_rem_data($m_dbConn);
?>
<script type="text/javascript" src="js/ajax.js"></script>
<script language="javascript" type="application/javascript">
function get_wing(society_id)
{
	document.getElementById('error_del').style.display = '';	
	document.getElementById('error_del').innerHTML = 'Wait... Fetching wing under this society';	
	remoteCall("ajax/get_wing.php","society_id="+society_id,"res_get_wing");		
}

function res_get_wing()
{
	var res = sResponse;
	
	document.getElementById('error_del').style.display = 'none';	
	
	var count = res.split('****');
	var pp = count[0].split('###');
	
	document.getElementById('wing_id').options.length = 0;
	var that = document.getElementById('society_id').value;
	
	for(var i=0;i<count[1];i++) 
	{		
		var kk = pp[i].split('#');
		var wing_id = kk[0];
		var wing = kk[1];
		document.getElementById('wing_id').options[i] = new Option(wing,wing_id);
	}
	document.getElementById('wing_id').options[i] = new Option('All','');
	document.getElementById('wing_id').value = '';
}

</script>
<body>

<br>
<center>
<div class="panel panel-info" id="panel" style="display:none">
<div class="panel-heading" id="pageheader">Member Records Status</div>
<center>

<table align="center" border="0">
<tr>
	<td valign="top" align="center"><font color="red"><b id="error_del"></b></font></td>
</tr>
</table>

<?php $rem_mem_form = $obj_mem_rem_data->rem_mem_form();?>


</center>
</div>
<?php include_once "includes/foot.php"; ?>

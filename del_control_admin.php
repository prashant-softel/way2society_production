<?php include_once "ses_set.php"; ?>
<?php include_once("includes/header.php");?>
<?php

include_once("classes/del_control_admin.class.php");
$obj_del_control_admin = new del_control_admin($m_dbConn);
$chk_delete_perm_admin = $obj_del_control_admin->chk_delete_perm_admin();
?>

<head>
	<script type="text/javascript" src="js/ajax.js"></script>
    <script language="javascript" type="application/javascript">
	function delete_perm_admin()
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = 'Wait...';	
		
		var del_control = document.getElementById('del_control_admin').checked;
		remoteCall("ajax/delete_perm_admin.php","del_control="+del_control,"res_delete_perm_admin");	
	}
	function res_delete_perm_admin()
	{
		var res = sResponse;
		if(res==1)
		{
			document.getElementById('error').style.display = 'none';	
			document.getElementById('del_control_admin').checked = true;
		}
		else
		{
			document.getElementById('error').style.display = 'none';	
			document.getElementById('del_control_admin').checked = false;
		}
	}
	</script>
</head>

<body>


<center><font color="#43729F" size="+1"><b>Admin Delete Permission</b></font></center>
<center>
<table align="center" border="0">
<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"></b></font></td></tr>
</table>

<table align='center'>
<tr>
    <td>Delete Permission</td>
    <td>&nbsp; : &nbsp;</td>
    <td>
    <?php
	if($chk_delete_perm_admin[0]['del_control_admin']==1)
	{
		$chk = 'checked';	
	}
	else
	{
		$chk = '';
	}
	?>
    <input type="checkbox" name="del_control_admin" id="del_control_admin" value="1" onClick="delete_perm_admin();" <?php echo $chk;?>>
    
    </td>
</tr> 
</table>

</center>
<?php include_once "includes/foot.php"; ?>

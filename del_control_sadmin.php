<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");?>
<?php

include_once("classes/del_control_sadmin.class.php");
$obj_del_control_sadmin = new del_control_sadmin($m_dbConn);
$chk_delete_perm_sadmin = $obj_del_control_sadmin->chk_delete_perm_sadmin();
?>

<head>
	<script type="text/javascript" src="js/ajax.js"></script>
    <script language="javascript" type="application/javascript">
	function delete_perm_sadmin()
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = 'Wait...';	
		
		var del_control = document.getElementById('del_control_sadmin').checked;
		remoteCall("ajax/delete_perm_sadmin.php","del_control="+del_control,"res_delete_perm_sadmin");	
	}
	function res_delete_perm_sadmin()
	{
		var res = sResponse;
		if(res==1)
		{
			document.getElementById('error').style.display = 'none';	
			document.getElementById('del_control_sadmin').checked = true;
		}
		else
		{
			document.getElementById('error').style.display = 'none';	
			document.getElementById('del_control_sadmin').checked = false;
		}
	}
	</script>
</head>

<body>

<center><font color="#43729F" size="+1"><b>Super Admin Delete Permission</b></font></center>

<center>
<table align="center">
<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"></b></font></td></tr>
</table>

<table align='center'>
<tr>
    <td>Delete Permission</td>
    <td>&nbsp; : &nbsp;</td>
    <td>
    <?php
	if($chk_delete_perm_sadmin[0]['del_control_sadmin']==1)
	{
		$chk = 'checked';	
	}
	else
	{
		$chk = '';
	}
	?>
    <input type="checkbox" name="del_control_sadmin" id="del_control_sadmin" value="1" onClick="delete_perm_sadmin();" <?php echo $chk;?>>
    
    </td>
</tr> 
</table>
<br><br><br><br><br><br>

</center>
<?php include_once "includes/foot.php"; ?>
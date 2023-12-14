<?php include_once("includes/head_s.php");

include_once("classes/dbconst.class.php");
include_once("classes/adduser.class.php");
include_once("classes/include/dbop.class.php");
$dbconn = new dbop();

$obj_addduser = new adduser($m_dbConnRoot,$dbconn);
$msg = '';

if(isset($_REQUEST['add']) && $_REQUEST['add'] != $_SESSION['random'])
{
	$_SESSION['random']	= $_REQUEST['add'];
	if($_REQUEST['role'] == ROLE_MEMBER && $_REQUEST['unit'] == 0)
	{
		$msg = 'Please select Unit for member';
	}
	else 
	{
		$code = $_REQUEST['add'];
		if($_REQUEST['role'] == ROLE_ADMIN || $_REQUEST['role'] == ROLE_CONTRACTOR || $_REQUEST['role'] == ROLE_SECURITY || $_REQUEST['role'] == ROLE_ACCOUNTANT || $_REQUEST['role'] == ROLE_MANAGER)
		{
			$emailID = $_REQUEST['email'];
		}
		else
		{
			$mailID = $obj_addduser->getEmail($_REQUEST['unit']);
			$emailID = $mailID[0]['email'];
		}
		
		$result = $obj_addduser->addUser($_REQUEST['role'], $_REQUEST['unit'], $_SESSION['society_id'], $code, $emailID);
		if($result > 0)
		{

			$msg = $_REQUEST['role'] . ' Added Successfully.<br>Account Access Code : ' . $result[0]['code'];
		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
<body>
	<center>
    <br />
		<div class="panel panel-info" id="panel" style="display:none">
<div class="panel-heading" id="pageheader">Add New User</div>
<br/>

         <button type="button" class="btn btn-primary" onClick="window.location.href = 'add_member_id.php'" >Back To List</button>
<br/>    	
<br/>    	
    	<div style="color:#FF0000;font-weight:bold;" id="msg"><?php echo $msg; ?></div>
		<form name="add_user"  method="post" action="">
			<input type="hidden" value="<?php echo getRandomUniqueCode(); ?>" name="add" />
			<table>
				<tr>
					<td>Role<?php echo $star;?></td><td>:&nbsp;</td>
					<td>
						<select name="role" id="role" onchange="onRoleChange(this.value);">
							<option value="<?php echo ROLE_MEMBER; ?>"><?php echo ROLE_MEMBER; ?></option>
							<option value="<?php echo ROLE_ADMIN; ?>"><?php echo ROLE_ADMIN; ?></option>
                            <option value="<?php echo ROLE_CONTRACTOR; ?>"><?php echo ROLE_CONTRACTOR; ?></option>
                            <option value="<?php echo ROLE_SECURITY; ?>"><?php echo ROLE_SECURITY; ?></option>
                             <option value="<?php echo ROLE_ACCOUNTANT; ?>"><?php echo ROLE_ACCOUNTANT; ?></option>
                            <option value="<?php echo ROLE_MANAGER; ?>"><?php echo ROLE_MANAGER; ?></option>
							
							<!--<option value="<?php //echo ROLE_ADMIN_MEMBER; ?>"><?php //echo ROLE_ADMIN_MEMBER; ?></option>-->
						</select>
					</td>
				</tr>
					<tr id="memUnit">
					<td>Unit<?php echo $star; ?></td><td>:&nbsp;</td>
					<td>
                     	<select name="unit" id="unit">
							<?php //echo $comboBox = $obj_addduser->combobox("select `unit_id`, `desc` from mapping where unit_id != 0 and society_id = '" . $_SESSION['society_id'] . "'", $_REQUEST['unit'], 'Please Select', 0); ?>
                            <?php  echo $comboBox = $obj_addduser->comboboxEx("select `unit_id`, `unit_no` from `unit` where unit_id != 0 and society_id = '" . $_SESSION['society_id'] . "'", $_REQUEST['unit'], 'Please Select', 0); ?>
						</select>
					</td>
				</tr>
                <tr>
					<td>Email <?php echo $star; ?></td><td>:&nbsp;</td>
					<td>
                     	<input type="text" id="email" name="email">
					</td>
				</tr>
					<script>
						onRoleChange('<?php echo $_REQUEST['role']; ?>');
						function onRoleChange(role)
						{
							document.getElementById('role').value = role;
							if(role == '<?php echo ROLE_MEMBER; ?>' || role == '')
							{
								document.getElementById('memUnit').style.display ='table-row';
								document.getElementById('unit').disabled = false;
								
							}
							else
							{
								document.getElementById('unit').value = 0;
								document.getElementById('unit').disabled = true;
								document.getElementById('memUnit').style.display='none';
							}
						}
					</script>
				<tr>
					<td colspan="3" align="center" style="padding:10px;"><input type="submit" value="Submit" style="color: #fff;background-color: #337ab7;border-color: #2e6da4;width:30%;height:20%;"/></td>
				</tr>
			</table>
    	</form>
    </center>
</body>
</html>
<?php include_once("includes/foot.php"); ?>
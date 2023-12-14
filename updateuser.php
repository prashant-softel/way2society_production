<?php include_once("includes/head_s.php");
include_once("classes/dbconst.class.php");
include_once("classes/adduser.class.php");
include_once("classes/add_member_id.class.php");
include_once("classes/initialize.class.php");


//print_r($map_details);

$msg = '';

if(isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'Update')
{
	$objUpdate = new adduser($m_dbConnRoot);
	$status = 1;
	if(isset($_REQUEST['restore']))
	{
		$status = $_REQUEST['restore'];
	}
	
	$result = $objUpdate->updateUserRole($_REQUEST['id'], $_REQUEST['role'], $status);
	
	$aryProfile = array();
	
	$aryProfile['PROFILE_GENERATE_BILL'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_GENERATE_BILL)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_GENERATE_BILL)] : 0;
	$aryProfile['PROFILE_CREATE_INVOICE'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_CREATE_INVOICE)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_CREATE_INVOICE)] : 0;
		
	$aryProfile['PROFILE_EDIT_BILL'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_EDIT_BILL)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_EDIT_BILL)] : 0;
	
	$aryProfile['PROFILE_CHEQUE_ENTRY'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_CHEQUE_ENTRY)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_CHEQUE_ENTRY)] : 0;
	
	$aryProfile['PROFILE_PAYMENTS'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_PAYMENTS)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_PAYMENTS)] : 0;
	
	$aryProfile['PROFILE_BANK_RECO'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_BANK_RECO)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_BANK_RECO)] : 0;
	
	$aryProfile['PROFILE_UPDATE_INTEREST'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_UPDATE_INTEREST)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_UPDATE_INTEREST)] : 0;
	
	$aryProfile['PROFILE_REVERSE_CHARGE'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_REVERSE_CHARGE)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_REVERSE_CHARGE)] : 0;
	
	$aryProfile['PROFILE_SEND_NOTIFICATION'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_SEND_NOTIFICATION)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_SEND_NOTIFICATION)] : 0;
	
	$aryProfile['PROFILE_MANAGE_MASTER'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_MANAGE_MASTER)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_MANAGE_MASTER)] : 0;

	$aryProfile['PROFILE_EDIT_MEMBER'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_EDIT_MEMBER)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_EDIT_MEMBER)] : 0;
	
	$aryProfile['PROFILE_SEND_NOTICE'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_SEND_NOTICE)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_SEND_NOTICE)] : 0;
	
	$aryProfile['PROFILE_SEND_EVENT'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_SEND_EVENT)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_SEND_EVENT)] : 0;
	
	$aryProfile['PROFILE_CREATE_ALBUM'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_CREATE_ALBUM)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_CREATE_ALBUM)] : 0;
	
	$aryProfile['PROFILE_CREATE_POLL'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_CREATE_POLL)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_CREATE_POLL)] : 0;
	
	$aryProfile['PROFILE_APPROVALS_LEASE'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_APPROVALS_LEASE)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_APPROVALS_LEASE)] : 0;
	
	$aryProfile['PROFILE_SERVICE_PROVIDER'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_SERVICE_PROVIDER)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_SERVICE_PROVIDER)] : 0;
	
	$aryProfile['PROFILE_PHOTO'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_PHOTO)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_PHOTO)] : 0;
	
	$aryProfile['PROFILE_MESSAGE'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_MESSAGE)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_MESSAGE)] : 0;
	
	$aryProfile['PROFILE_MANAGE_LIEN'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_MANAGE_LIEN)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_MANAGE_LIEN)] : 0;
		
	$aryProfile['PROFILE_USER_MANAGEMENT'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_USER_MANAGEMENT)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_USER_MANAGEMENT)] : 0;
	
	$aryProfile['PROFILE_CLASSIFIED'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_CLASSIFIED)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_CLASSIFIED)] : 0;
	$aryProfile['PROFILE_APPROVAL_OF_RENOVATION_REQUEST'] = (isset($_REQUEST['renovationApproval'])) ? $_REQUEST['renovationApproval'] : 0;
	
	$aryProfile['PROFILE_VERIFICATION_OF_RENOVATION_REQUEST'] = (isset($_REQUEST['renovationVerification'])) ? $_REQUEST['renovationVerification'] : 0;
	
	$aryProfile['PROFILE_APPROVAL_OF_NOC'] = (isset($_REQUEST['approvalNOC'])) ? $_REQUEST['approvalNOC'] : 0;
	
	$aryProfile['PROFILE_VENDOR_MANAGEMENT'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_VENDOR_MANAGEMENT)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_VENDOR_MANAGEMENT)] : 0;
	
	$resultProfile = $objUpdate->updateUserProfile($_REQUEST['id'], $aryProfile);
	
	if($result > 0)
	{
		//$map_details[0]['role'] = $_REQUEST['role'];
		$msg = 'Role Updated Successfully';
	}
}

$obj_initialize = new initialize($m_dbConnRoot);
$map_details = $obj_initialize->getMapDetails($_REQUEST['id']);
$profile_details = $obj_initialize->getProfile($map_details[0]['profile']);

$obj_member = new add_member_id($m_dbConn, $m_dbConnRoot);
$member_info = $obj_member->getMemberProfile($map_details[0]['unit_id']);
$memberDetails = $obj_member->getMemberInfo($map_details[0]['login_id']);

function getFileNameWithoutExt($fileName)
{
	return substr($fileName, 0, strrpos($fileName, '.'));
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
<body>


<script> 

function getRole(role)
{
	
	if(role=="Admin Member" || role=="Admin" || role == "Manager" || role=="Accountant"){
	document.getElementById("aceese").style.display='block';
	document.getElementById("aceese1").style.display='block';
	document.getElementById("aceese2").style.display='block';}
	else if(role=="Member"){
		document.getElementById("aceese").style.display='none';
		document.getElementById("aceese1").style.display='none';
		document.getElementById("aceese2").style.display='none';		
}}
</script>
	<br />
	<center>
	<div class="panel panel-info" id="panel" style="display:none">
	<div class="panel-heading" id="pageheader">Update User Role</div><br />
    	<?php if(isset($_REQUEST['cltID']) && $_REQUEST['cltID'] <> '')
				{ ?>
        <button type="button" class="btn btn-primary" onclick="window.location.href='client_details.php?client=<?php echo $_REQUEST['cltID']; ?>'">Back To Society List</button> 
        <?php 	}
		      else
			  	{  ?> 
        <button type="button" class="btn btn-primary" onclick="window.location.href='add_member_id.php'">Back To List</button>
        <?php 	} ?> <br /><br />
    	<div style="color:#FF0000;font-weight:bold;" id="msg"><?php echo $msg; ?></div>
		<form name="add_user"  method="post" action="">
			<input type="hidden" value="<?php echo $_REQUEST['id']; ?>" name="id" />
			<table id="example" class="display" cellspacing="0" style="width:60%;border:1px solid #CCC;">
				<tr>
					<td>Name<?php echo $star; ?></td><td>:&nbsp;</td>
					<td><?php if($map_details[0]['role'] == ROLE_ADMIN) { echo $memberDetails[0]['name']; }else{ echo $member_info[0]['owner_name']; } ?></td>
				</tr>
				<tr>
					<td>Description<?php echo $star; ?></td><td>:&nbsp;</td>
					<td><?php echo $map_details[0]['desc']; ?></td>
				</tr>
				<tr>
					<td>Code<?php echo $star; ?></td><td>:&nbsp;</td>
					<td><?php echo $map_details[0]['code']; ?></td>
				</tr>
				<tr>
					<td>Status<?php echo $star; ?></td><td>:&nbsp;</td>
					<td><?php echo ($map_details[0]['status'] == '1') ? 'Inactive' : (($map_details[0]['status'] == '2') ? 'Active' : 'Deleted'); ?></td>
				</tr>
				<?php if($map_details[0]['status'] == '3')
				{
					?>
						<tr>
							<td width="65px">Restore User<?php echo $star; ?></td><td>:&nbsp;</td>
							<td><input type="checkbox" name="restore" id="restore" value="2"/></td>
						</tr>
					<?php
				}
				else
				{
					?>
						<tr>
							<td width="60px">Deactivate User<?php echo $star; ?></td><td>:&nbsp;</td>
							<td><input type="checkbox" name="restore" id="restore" value="3"/></td>
						</tr>
					<?php
				}
				?>
				<tr>
					<td>Role<?php echo $star;?></td><td>:&nbsp;</td>
					<td>
						<select name="role" id="role" onchange="getRole(this.value)">
							<?php if($map_details[0]['role'] == ROLE_ADMIN)
							{
								?>
								<option value="<?php echo ROLE_ADMIN; ?>"><?php echo ROLE_ADMIN; ?></option>
								<?php
							}
							else
							{
								?>
								<option value="<?php echo ROLE_MEMBER; ?>"><?php echo ROLE_MEMBER; ?></option>
								<option value="<?php echo ROLE_ADMIN_MEMBER; ?>"><?php echo ROLE_ADMIN_MEMBER; ?></option>
                               					<option value="<?php echo ROLE_MANAGER; ?>"><?php echo ROLE_MANAGER; ?></option>
								<option value="<?php echo ROLE_ACCOUNTANT; ?>"><?php echo ROLE_ACCOUNTANT; ?></option>
								
								<?php
							}
							?>
						</select>
					</td>
				</tr>
               
				<tr>
					<td>Profile<?php echo $star;?></td><td>:&nbsp;</td>
					<td><table id="example" class="display" cellspacing="0" style="width:100%;"><tr> <td><div id="aceese"  style="display:none;">
                    
						<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_GENERATE_BILL); ?>" <?php if($profile_details[PROFILE_GENERATE_BILL] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Generate Bill</label><br />
                        
                        <!--Check box to give access to create invocie  -->
                        <label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_CREATE_INVOICE); ?>" <?php if($profile_details[PROFILE_CREATE_INVOICE] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Create Invoice</label><br />
                        
                        <label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_EDIT_BILL); ?>" <?php if($profile_details[PROFILE_EDIT_BILL] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Edit Bill</label><br />
						
						<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_CHEQUE_ENTRY); ?>" <?php if($profile_details[PROFILE_CHEQUE_ENTRY] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Deposit Cheques</label><br />
						
						<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_PAYMENTS); ?>" <?php if($profile_details[PROFILE_PAYMENTS] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Issue Cheques</label><br />
						
						<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_BANK_RECO); ?>" <?php if($profile_details[PROFILE_BANK_RECO] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Bank Reconciliation</label><br />
						
						<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_UPDATE_INTEREST); ?>" <?php if($profile_details[PROFILE_UPDATE_INTEREST] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Update Interest</label><br />
						
						<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_REVERSE_CHARGE); ?>" <?php if($profile_details[PROFILE_REVERSE_CHARGE] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Reverse Entry</label><br />
                        
						<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_SEND_NOTIFICATION); ?>" <?php if($profile_details[PROFILE_SEND_NOTIFICATION] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Send Notification</label><br />
                       
                        <label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_MANAGE_MASTER); ?>" <?php if($profile_details[PROFILE_MANAGE_MASTER] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Manage Masters</label><br />

                        <label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_EDIT_MEMBER); ?>" <?php if($profile_details[PROFILE_EDIT_MEMBER] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Edit Member</label>        
					
						<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_VENDOR_MANAGEMENT); ?>" 
						<?php if($profile_details[PROFILE_VENDOR_MANAGEMENT] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Vendor Management</label>
					</div>
						
					</div><br /> 
						
					</td>
                    
                    <td>
			
						
                     
                               
									
									
						
                       			<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_SEND_NOTICE); ?>" <?php if($profile_details[PROFILE_SEND_NOTICE] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Send Notice</label><br />
                        
                        <label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_SEND_EVENT); ?>" <?php if($profile_details[PROFILE_SEND_EVENT] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Send Event</label><br />
						
						<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_CREATE_ALBUM); ?>" <?php if($profile_details[PROFILE_CREATE_ALBUM] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Create Album</label><br />
						
						<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_CREATE_POLL); ?>" <?php if($profile_details[PROFILE_CREATE_POLL] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Create Poll</label><br />
                       
                        <div id="aceese1"  style="display:none;"><label ><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_MESSAGE); ?>" <?php if($profile_details[PROFILE_MESSAGE] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;SEND SMS </label><br />
                        
                        <label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_MANAGE_LIEN); ?>" <?php if($profile_details[PROFILE_MANAGE_LIEN] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Manage Lien</label><br />
                        
                        
                        <label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_USER_MANAGEMENT); ?>" <?php if($profile_details[PROFILE_USER_MANAGEMENT] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;User management</label></div>
                     
                        </td>
                       <td>
										<div id="aceese2"  style="display:none;"><label>
                <input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_APPROVALS_LEASE); ?>" <?php if($profile_details[PROFILE_APPROVALS_LEASE] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Approvals of Lease</label>
                
                			<label><input type="checkbox" value="1" name="renovationVerification" <?php if($profile_details['PROFILE_VERIFICATION_RENOVATION_REQUEST'] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Verification of Renovation Request</label><br />
                        
                        <label><input type="checkbox" value="1" name="renovationApproval" <?php if($profile_details['PROFILE_APPROVAL_RENOVATION_REQUEST'] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Approval of Renovation Request</label><br />
                        
						<label><input type="checkbox" value="1" name="approvalNOC" <?php if($profile_details['PROFILE_APPROVAL_NOC'] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Approval of NOC</label><br />
                </div>
						
						<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_SERVICE_PROVIDER); ?>" <?php if($profile_details[PROFILE_SERVICE_PROVIDER] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Approvals of Service Provider</label><br />
						
						<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_PHOTO); ?>" <?php if($profile_details[PROFILE_PHOTO] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Approvals of Photos</label><br />
                        
						<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_CLASSIFIED); ?>" <?php if($profile_details[PROFILE_CLASSIFIED] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Approval of classified</label><br />
                        
                                                
                        		                        
					</td>	
				</tr></table></td></tr>
				<script>
					var role=document.getElementById('role').value = "<?php echo $map_details[0]['role'] ?>"
					//alert(role);
					getRole(role);
				</script>
				<tr>
					<td colspan="7" align="center"  style="padding:10px;"><input type="submit" name="submit" value="Update" class="btn btn-primary" /></td>
				</tr>
			</table>
    	</form>
	</div>
    </center>
<?php include_once "includes/foot.php"; ?>

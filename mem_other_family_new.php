<?php include_once("includes/head_s.php");
include_once("classes/dbconst.class.php");
error_reporting(7);
include_once("classes/mem_other_family.class.php");
$obj_mem_other_family = new mem_other_family($m_dbConn);

$unit_details = $obj_mem_other_family->unit_details($_REQUEST['mem_id']);

$hasAccess = true;

if($_SESSION['role'] == ROLE_MEMBER && $_SESSION['unit_id'] <> $unit_details['unit_id'])
{
    $hasAccess = false;
}
else if($_SESSION['role'] == ROLE_ADMIN_MEMBER)
{
    if($_SESSION['profile'][PROFILE_EDIT_MEMBER] != 1 && $_SESSION['unit_id'] <> $unit_details['unit_id'])
    {
        $hasAccess = false;
    }
}

if($hasAccess == false)
{
	?>
		<script>
			window.location.href = 'Dashboard.php';
		</script>

	<?php
	exit();
}

$UnitBlock = $_SESSION["unit_blocked"];
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsmem_other_family.js"></script>
    <script language="javascript" type="application/javascript">
	 $(function () {
        $("[data-toggle='tooltip']").tooltip();
    });
	function go_error()
    {
        setTimeout('hide_error()',6000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }	
	
	//$( document ).ready(function() {
		 
		var isblocked = '<?php echo $UnitBlock ?>';
		//alert(isblocked);
		if(isblocked==1)
		{
			//alert("We are sorry,but your access has been blocked for this feature . Please contact your Managing Committee for resolution .");
			
			window.location.href='suspend.php';
			
			
		}
	//});
	</script>
    
    <!--<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
	<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
    <script type="text/javascript" src="javascript/jquery.clockpick.1.2.4.js"></script>
    <script type="text/javascript" src="javascript/ui.core.js"></script>
    <script type="text/javascript" src="javascript/ui.datepicker_bday.js"></script>-->
    <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
    <script type="text/javascript">
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "yy-mm-dd", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
			changeMonth: true,
    		changeYear: true,
    		yearRange: '-100:+0',
            buttonImageOnly: true,
            defaultDate: '01-01-1980'
        })});
        
		function next()
		{
			window.location.href = 'mem_vehicle_new.php.php?scm&tik_id=<?php echo time();?>&m'	
		}   
		function backk()
		{
			window.location.href = 'mem_other_family_new.php?scm&tik_id=<?php echo time();?>&m'	
		}
    </script>
</head>

<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<br>
<div class="panel panel-info" id="panel" style="display:none;margin-top:10px;margin-left:3.5%;width:70%">
    <div class="panel-heading" id="pageheader">Add Member Associated With Unit</div>

<br>
<button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;margin-left:10%" id="btnBack"><i class="fa  fa-arrow-left"></i></button>

<center>
<?php if(isset($_SESSION['role']) && $_SESSION['role']==ROLE_MEMBER){?>
<input type="button" class="btn btn-primary" onClick="window.location.href='view_member_profile.php?prf&id=<?php echo $_GET['mem_id'];?>'"  style="float:left;" value="Go to profile view">

<?php }else{ ?>
<input type="button" class="btn btn-primary" onClick="window.location.href='view_member_profile.php?scm&id=<?php echo $_GET['mem_id'];?>&tik_id=<?php echo time();?>&m'"  style="" value="Go to profile view">
<?php } ?>
<form name="mem_other_family" id="mem_other_family" method="post" action="process/mem_other_family.process.php" onSubmit="return val();">

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
		else
		{
			//$msg = '';	
		}
	?>
    <table align='center'>
		<?php
		if(isset($msg))
		{
			if(isset($_POST["ShowData"]))
			{
		?>
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
		<?php
			}
			else
			{
			?>
            	<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $msg; ?></b></font></td></tr>	   
            <?php		
			}
		}
		else
		{
		?>	
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
        <?php
		}
		?>


		<!--<tr>
        	<td valign="top"><?php //echo $star;?></td>
			<td>Member Name</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<?php
				//if(isset($_REQUEST['idd']))
                //{
				//$owner_name = $obj_mem_other_family->owner_name($_REQUEST['idd']);
				?>    
				<input type="hidden" name="member_id" value="<?php //echo $_REQUEST['idd'];?>">
				<?php					
				//}
				////else
				//{
					 
					//if(isset($_SESSION['admin']))
					//{
						//echo $_SESSION['owner_name'];
				?>
                		<input type="hidden" name="member_id" value="<?php //echo $_REQUEST['mem_id'];?>">    
                <?php
					//}
					//else
					//{
						
						//echo $_SESSION['owner_name'];
					?>
                    	<input type="hidden" name="member_id" value="<?php //echo $_REQUEST['mem_id'];?>">
                    <?php	
					//}
				//}
				?>
            </td>
		</tr>-->
	    
	    <tr>
		   	<td valign="middle"></td>
			<td>Unit No.</td>
            <td>&nbsp; : &nbsp;</td>
			<td><?php echo $unit_details['unit_no'];?></td>
		</tr>
	
		<tr><td colspan="4">&nbsp;</td></tr>
		
		<tr>
		   	<td valign="middle"><?php echo $star;?></td>
			<td>Name</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="other_name" id="other_name" /></td>
		</tr>
        
		<tr>
        	<td valign="middle"></td>
			<td>Relation With Primary Owner</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="relation" id="relation" /></td>
		</tr>


		<?php
			if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['profile'][PROFILE_EDIT_MEMBER] == 0 && $_SESSION['role'] != ROLE_MEMBER)
			{
				?>
					<input type="hidden" name="coowner" value="0">
				<?php
			}
			else
			{
				?>
					<tr>
			        	<td valign="middle"></td>
						<td>Ownership</td>
			            <td>&nbsp; : &nbsp;</td>
						<td>
			            	<select name="coowner" id="coowner">
			            		<option value="0">None</option>
			            		<?php if($_SESSION['role'] == ROLE_SUPER_ADMIN) { echo "<option value='1'>Owner</option>"; }?>
			            		<?php if($_SESSION['role'] == ROLE_SUPER_ADMIN) { echo "<option value='2'>Co-Owner</option>"; }?>								
								<?php if($_SESSION['role'] == ROLE_SUPER_ADMIN) {echo "<option value='3'>Associate member</option>";}?>
			            		<option value="4">Family</option>
			            		<option value="5">Friends</option>
			            	</select>
			        	</td>
					</tr>
				<?php
			}
		?>
		

		<tr><td colspan="4">&nbsp;</td></tr>

		<tr>
        	<td valign="middle"></td>
			<td>Mobile No.</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="other_mobile" id="other_mobile" /></td>
		</tr>

		<tr>
        	<td valign="middle"></td>
			<td>E-Mail</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="other_email" id="other_email" /></td>
		</tr>
        <!-- data-toggle='tooltip' data-original-title="Note:Account activation email will be sent to user. User will have to click on 'Activate' from email to create their own login."-->
		<tr>
			<td valign="middle"></td>
			<td>Create Login</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="checkbox"  name="chkCreateLogin" id="chkCreateLogin" value="1" /></td>
		</tr>
        <tr>
        	<td valign="middle"></td>
			<td>Send Communication E-Mails ?</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="checkbox" name="other_send_commu_emails" id="other_send_commu_emails" value="1" checked /></td>
		</tr>
		<tr>
        </tr>
		<tr>
			<td valign="middle"></td>
			<td>Publish Contact Information</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="checkbox" name="other_publish_contact" id="other_publish_contact" checked value="1" <?php /*if($_SESSION['role'] != ROLE_MEMBER && $_SESSION['role'] != ROLE_ADMIN_MEMBER) { echo 'onclick="return false;"';} */?> /></td>
		</tr>

		<tr><td colspan="4">&nbsp;</td></tr>

		 <tr>
        	<td valign="middle"></td>
			<td>Blood Group</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<select name="child_bg" id="child_bg">
					<?php echo $combo_child_bg=$obj_mem_other_family->combobox07("select bg_id,bg from bg where status='Y'",9); ?>
				</select>
        	</td>
		</tr>
        <tr>
        	<td valign="top"><?php //echo $star;?></td>
			<td>Date of Birth</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="other_dob" id="other_dob" class="basics" size="10" readonly style="width:100px;"/></td>
		</tr>

		<tr>
        	<td valign="top"><?php //echo $star;?></td>
			<td>Wedding Anniversary</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="other_wed" id="other_wed" class="basics" size="10" readonly style="width:100px;"/></td>
		</tr>
		
		<tr><td colspan="4">&nbsp;</td></tr>

        <tr>
        	<td valign="middle"></td>
			<td>Designation</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<select name="other_desg" id="other_desg">
					<?php echo $combo_other_desg=$obj_mem_other_family->combobox("select desg_id,desg from desg where status='Y'"); ?>
				</select>
        	</td>
		</tr>
        
		<tr>
        	<td valign="top"><?php //echo $star;?></td>
			<td>School/College/Company</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="ssc" id="ssc" /></td>
		</tr>
		
       


		<tr>
        	<td valign="middle"></td>
			<td>Professional Profile</td>
            <td>&nbsp; : &nbsp;</td>
			<td><textarea name="other_profile" id="other_profile" style="width: 100%;" rows="5" placeholder="Enter your profession profile here ...."></textarea>
        	</td>
		</tr>
        
        <tr>
			<td valign="middle"></td>
			<td>Publish Profile Information</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="checkbox" name="other_publish_profile" id="other_publish_profile" value="1" <?php if($_SESSION['role'] != ROLE_MEMBER && $_SESSION['role'] != ROLE_ADMIN_MEMBER) { echo 'onclick="return false;"';} ?> /></td>
		</tr>
		<tr><td colspan="4"><input type="hidden" id="txtUnitID" name="txtUnitID" value="<?php echo $unit_details['unit_id']; ?>"></td></tr>       
        <tr><td colspan="4">
        	<input type="hidden" value="<?php echo getRandomUniqueCode(); ?>" name="Code" id=="Code" />
        </td>
        </tr>
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td colspan="4" align="center">
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="mkm" id="mkm" value="<?php if(isset($_REQUEST['mkm'])){echo 'mkm';}?>">
            <input type="hidden" name="mrs" id="mrs" value="<?php if(isset($_REQUEST['mrs'])){echo 'mrs';}?>">
            <input type="hidden" name="member_id" value="<?php echo $_REQUEST['mem_id'];?>">
            <?php if(isset($_SESSION['admin'])){?>
            
            <?php if(!isset($_REQUEST['mkm']) && (!isset($_REQUEST['mrs']))){?>
            <input type="submit" name="insert" id="insert" value="Add More">
            &nbsp;&nbsp;
            <input type="button" value="Back" onClick="backk();"> 
            &nbsp;&nbsp;
            <input type="button" value="Next Form" onClick="next();"> 
            <?php }else{?>
            <input type="submit" class="btn btn-primary" name="insert" id="insert" value="Add" style="width:100px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal; background-color: #337ab7;color: #fff; border-color: #2e6da4;">
            <?php }?>
            
            <?php }else{?>
            <input type="submit" class="btn btn-primary" name="insert" id="insert" value="Add" style="width:100px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal; background-color: #337ab7;color: #fff; border-color: #2e6da4;">
            <?php }?>
            <input type="button" class="btn btn-primary" onClick="history.go(-1);" value="Cancel" style="width:100px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal; background-color: #337ab7;color: #fff; border-color: #2e6da4;" id="btnCancel">
            </td>
		</tr>

		<tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td colspan="4">
				<p>NOTE :<br/>1. <b>Publish Contact Information</b> will display your Mobile No. and E-Mail in the Blood Group Listing and Professional Listing in Directory.
				<br/>2. <b>Publish Profile Information</b> will display your Professional Profile under Professional Listing in the Directory.
				<br/>3. You can change these settings later from your <b>Profile View</b>.</p>
			</td>
		</tr>
</table>
</form>


<table align="center" style="display:none;">
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_mem_other_family->pgnation();
echo "<br>";
echo $str = $obj_mem_other_family->display1($str1);
echo "<br>";
$str1 = $obj_mem_other_family->pgnation();
echo "<br>";
?>
</td>
</tr>
</table>

</center>
</div>
<?php include_once "includes/foot.php"; ?>

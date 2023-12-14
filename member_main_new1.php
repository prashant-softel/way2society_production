<?php include_once "ses_set_mfb.php";
include_once "includes/test_out_head.php";

include_once("classes/member_main_new1.class.php");
$obj_member_main = new member_main_new1($m_dbConn);

include_once "fb/fbmain_new.php";

?>

	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsmember_main_new1.js"></script>
    <script type="text/javascript" src="js/validate.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',8000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }
	function go_error1()
    {
        setTimeout('hide_error1()',8000);	
    }
    function hide_error1()
    {
        document.getElementById('eu').style.display = 'none';	
    }
	
	function check_code(code)
	{
		if(code!="")
		{
			document.getElementById('error').style.display = '';	
			document.getElementById('error').innerHTML = 'Please wait...';	
		
			remoteCall("ajax/check_code.php","code="+code,"res_check_code");			
		}
	}
	function res_check_code()
	{
		var res = sResponse;
		
		document.getElementById('error').style.display = 'none';	
		
		if(res==0)
		{
			document.getElementById('error').style.display = '';	
			document.getElementById('error').innerHTML = 'This code is invalid';		
			document.getElementById('code').focus();
			
			go_error();
			return false;
		}	
		else
		{
			var code = document.getElementById('code').value;
			remoteCall("ajax/check_code_exist.php","code="+code+"&exist","res_check_code_exist");			
		}
	}
	
	function res_check_code_exist()
	{
		var res = sResponse;
		
		document.getElementById('error').style.display = 'none';		
		if(res==1)
		{
			document.getElementById('error').style.display = '';	
			document.getElementById('error').innerHTML = 'Already exist this code';		
			document.getElementById('code').value = "";
			document.getElementById('code').focus();
			
			go_error();
			return false;
		}
	}
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
            buttonImageOnly: true 
        })});
            
    </script>
</head>

<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<font color="#515151" size="+1"><b>Member Details</b></font>

<form name="member_main" id="member_main" method="post" action="process/member_main_new1.process.php" onSubmit="return val();">
<input type="hidden" name="fbid" value="<?php echo $_SESSION['fbid'];?>">

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
        
        <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Enter Code</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="code" id="code" maxlength="8" onBlur="check_code(this.value);"/></td>
		</tr>
        
		<tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Owner Name</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="owner_name" id="owner_name" value="<?php echo $_SESSION['name'];?>"/></td>
		</tr>
		
        <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Residence Phone No.</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="resd_no" id="resd_no" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)"/></td>
		</tr>
		
        <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Mobile No.</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="mob" id="mob" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" maxlength="10"/></td>
		</tr>
		
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Alternate Mobile No.</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="alt_mob" id="alt_mob" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" maxlength="10"/></td>
	
    	</tr>
		
        <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Office No.</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="off_no" id="off_no" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)"/></td>
		</tr>
		
        <tr align="left">
        	<td valign="top"><?php echo $star;?></td>
			<td valign="top">Office Address</td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><textarea name="off_add" id="off_add" rows="4" cols="28"></textarea></td>
		</tr>
		
        <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Designation</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<select name="desg" id="desg">
				<?php echo $combo_desg=$obj_member_main->combobox("select desg_id,desg from desg where status='Y' order by desg"); ?>
				</select>
        	</td>
		</tr>
		
        <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Email</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="email" id="email"  value="<?php echo $_SESSION['email'];?>" readonly/></td>
		</tr>
		
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Set Password</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="user_pass" id="user_pass" style="width:100px;"/></td>
		</tr>
        <tr><td colspan="3">&nbsp;</td><td><font color="#FF0000" style="font-size:11px;"><u>Note</u> : This password you can use only for <br> website login panel.Not for facebook login <br> panel if you are connected by fbconnect.</font></td></tr>
        
        <tr style="display:none;" id="eu">
        	<td></td>
        	<td colspan="3" align="left" height="40"><font color="#FF0000"><b id="err">This email or username already exist</b></font></td>
        </tr>
        
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Alternate Email id</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="alt_email" id="alt_email" /></td>
		</tr>
		
        <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Date of Birth</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="dob" id="dob"  class="basics" size="10" value="<?php echo $_SESSION['dob'];?>" readonly  style="width:80px;"/></td>
		</tr>
		
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Wedding Aniversary</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="wed_any" id="wed_any"  class="basics" size="10" readonly style="width:80px;"/></td>
		</tr>
		
        <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Blood Group</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<select name="bg" id="bg">
				<?php echo $combo_bg = $obj_member_main->combobox("select bg_id,`bg` from bg where status='Y'"); ?>
				</select>
            </td>
		</tr>
        
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td valign="top">Close Relative Name</td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><input type="text" name="eme_rel_name" id="eme_rel_name"/></td>
		</tr>
        
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td valign="top">Emergency Contact No. 1</td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><input type="text" name="eme_contact_1" id="eme_contact_1" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)"/></td>
		</tr>
        
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td valign="top">Emergency Contact No. 2</td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><input type="text" name="eme_contact_2" id="eme_contact_2" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)"/></td>
		</tr>
       
        <tr><td colspan="4">&nbsp;</td></tr>
        
		<tr>
			<td colspan="4" align="center">
            <input type="hidden" name="id" id="id">
            <input type="submit" name="insert" id="insert" value="Next">
            </td>
		</tr>
</table>
</form>

<?php include_once "includes/foot.php"; ?>														
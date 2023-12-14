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

include_once("classes/member_main.class.php");
$obj_member_main = new member_main($m_dbConn);
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsmember_main.js"></script>
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
	
	
	function get_unit(wing)
	{
		document.getElementById('error').style.display = '';	
		document.getElementById('error').innerHTML = 'Wait... Fetching unit under this wing';	
		remoteCall("ajax/get_unit.php","wing="+wing,"res_get_unit");		
	}
	
	function res_get_unit()
	{
		var res = sResponse;
		
		document.getElementById('error').style.display = 'none';	
		
		var count = res.split('****');
		var pp = count[0].split('###');
		
		document.getElementById('unit').options.length = 0;
		var that = document.getElementById('wing_id').value;
		
		if(count[1]!=0)
		{
			if(that>0) 
			{
				for (var i=0; i<count[1]; i++) 
				{		
					var kk = pp[i].split('#');
					var unit_no = kk[0];
					var unit_id = kk[1]
					document.getElementById('unit').options[i] = new Option(unit_id,unit_no);
				}
				document.getElementById('unit').options[i] = new Option('Please Select','');
				document.getElementById('unit').value = '';
			}
		}
		else
		{
			document.getElementById('unit').options[0] = new Option('Please Select','');
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
    
    <script language="javascript" type="application/javascript">
	function check_record(unit_id)
	{
		if(unit_id!="")
		{
			remoteCall("ajax/check_record.php","unit_id="+unit_id,"check_record_res");
		}
	}
	function check_record_res()
	{
		var res = sResponse;
		if(res==1)
		{
			document.getElementById('error').style.display = '';	
			document.getElementById("error").innerHTML = "Already added the record under this unit no.";
			document.getElementById('unit').value = '';
			go_error();
		}
		else
		{
			document.getElementById('error').style.display = '';	
			document.getElementById("error").innerHTML = "<font color='green'>You can add the record under this unit no.</font>";	
			
			go_error();
		}
		
	}
	
	function go_username(email)
	{
		document.getElementById('mem_user').value = email;
	}
	
	function check_username(email)
	{
		if(email=="")
		{
			document.getElementById('eu').style.display = '';	
			document.getElementById("err").innerHTML = "Please enter email id or username";
			document.getElementById("email").focus();
			
			go_error1();
			return false;		
		}
		else
		{
			var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
			
			if(reg.test(email)==false) 
			{
				document.getElementById('eu').style.display = '';
				document.getElementById('err').innerHTML = 'Invalid email id or username';	
				
				document.getElementById("email").value = '';
				document.getElementById("mem_user").value = '';
				
				document.getElementById('email').focus();
				
				go_error1();
				return false;	
			}
			else
			{
				remoteCall("ajax/check_username.php","email="+email,"check_username_res");
			}
		}
	}
	
	function check_username_res()
	{
		var res = sResponse;//alert(res);
		if(res==1)
		{
			document.getElementById('eu').style.display = '';
			document.getElementById("email").value = '';
			document.getElementById("mem_user").value = '';
			document.getElementById("email").focus();		
		}
	}
	</script>
</head>

<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>


<center><font color="#43729F" size="+1"><b>Member Main Form</b></font></center>
<br>
<center>
<a href="list_member.php?scm" style="color:#00F; text-decoration:none;"><b>Back to list</b></a>
</center>

<center>
<!--<form name="member_main" id="member_main" method="post" action="process/member_main.process.php" onSubmit="return val();">-->
<form name="member_main" id="member_main" method="post" action="process/member_main.process.php" >
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
			<td>Select Wing</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<select name="wing_id" id="wing_id" onChange="get_unit(this.value);">
				<?php echo $combo_unit=$obj_member_main->combobox("select wing_id,wing from wing where status='Y' and society_id='".$_SESSION['society_id']."' order by wing"); ?>
				</select>
        	</td>
		</tr>

		<tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Select Unit</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<select name="unit" id="unit" onChange="check_record(this.value);">
					<?php //echo $combo_unit=$obj_member_main->combobox("select unit_id,unit_no from unit where status='Y' order by unit_id"); ?>
                    <option value="">Please Select</option>
				</select>
        	</td>
		</tr>
        
		<tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Owner Name</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="owner_name" id="owner_name" /></td>
		</tr>
		
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Residence Phone No.</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="resd_no" id="resd_no" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)"/></td>
		</tr>
		
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
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
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Office No.</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="off_no" id="off_no" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)"/></td>
		</tr>
		
        <tr align="left">
        	<td valign="top"><?php //echo $star;?></td>
			<td valign="top">Office Address</td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><textarea name="off_add" id="off_add" rows="4" cols="28"></textarea></td>
		</tr>
		
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Designation</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<select name="desg" id="desg">
				<?php echo $combo_desg=$obj_member_main->combobox("select desg_id,desg from desg where status='Y' order by desg"); ?>
				</select>
        	</td>
		</tr>
		
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Email id or username</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="email" id="email" onKeyDown="go_username(this.value);" onBlur="check_username(this.value);"/></td>
		</tr>
		
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
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Date of Birth</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="dob" id="dob"  class="basics" size="10" readonly style="width:100px;"/></td>
		</tr>
		
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Wedding Aniversary</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="wed_any" id="wed_any"  class="basics" size="10" readonly style="width:100px;"/></td>
		</tr>
		
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
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
        
        <tr height="40"><td>&nbsp;</td><th colspan="3" align="center"><table><tr height="25"><th bgcolor="#CCCCCC" width="180">For Login</th></tr></table></th></tr>
        
        <tr align="left">
        	<td valign="top"><?php //echo $star;?></td>
			<td valign="top">Username / Email</td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><input type="text" name="mem_user" id="mem_user" readonly/></td>
		</tr>
        
        <tr align="left">
        	<td valign="top"><?php //echo $star;?></td>
			<td valign="top">Set Password</td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><input type="text" name="mem_pass" id="mem_pass"/></td>
		</tr>
        
		<tr><td colspan="4">&nbsp;</td></tr>
        
		<tr>
			<td colspan="4" align="center">
            <input type="hidden" name="id" id="id">
            <input type="submit" name="insert" id="insert" value="Insert">
            </td>
		</tr>
</table>
</form>

<br><br>


<table align="center" style="display:none;">
<tr>
<td>
<div style="overflow-y:hidden;overflow-x:scroll;width:900px; height:500px; border:solid #CCCCCC 1px;" align="center">
<?php
echo "<br>";
$str1 = $obj_member_main->pgnation1();
echo "<br>";
echo $str = $obj_member_main->display1($str1);
echo "<br>";
$str1 = $obj_member_main->pgnation1();
echo "<br>";
?>
</div>
</td>
</tr>
</table>

</center>
<?php include_once "includes/foot.php"; ?>

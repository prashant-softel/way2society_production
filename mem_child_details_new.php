<?php //include_once "ses_set_ma.php"; ?>
<?php

	include_once("includes/head_s.php");
include_once("classes/mem_child_details.class.php");
$obj_mem_child_details = new mem_child_details($m_dbConn);
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsmem_child_details.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',6000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
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
			changeMonth: true,
    		changeYear: true,
    		yearRange: '-150:+10',
			buttonImageOnly: true
			
        })});
            
		function next()
		{
			window.location.href = 'mem_other_family_new.php?scm&tik_id=<?php echo time();?>&m'	
		}
		function backk()
		{
			window.location.href = 'mem_spouse_details_new.php?scm&mkm&tik_id=<?php echo time();?>&m'	
		}
    </script>
</head>

<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<br>
<div class="panel panel-info" id="panel" style="display:none;margin-top:6%;margin-left:3.5%; border:none;width:70%">
        <div class="panel-heading" id="pageheader">Member Child Details</div>

<br>
<center>
<?php if(isset($_SESSION['role']) && $_SESSION['role']==ROLE_MEMBER){?>

<a href="view_member_profile.php?prf&id=<?php echo $_GET['mem_id'];?>" style="color:#00F; text-decoration:none;"><b>Go to profile view</b></a>
<?php }else{ ?>
<a href="view_member_profile_adm.php?scm&id=<?php echo $_GET['mem_id'];?>&tik_id=<?php echo time();?>&m" style="color:#00F; text-decoration:none;"><b>Go to profile view</b></a>
<?php } ?>
</center>

<center>
<form name="mem_child_details" id="mem_child_details" method="post" action="process/mem_child_details.process.php" onSubmit="return val();">
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


		<tr>
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Member Name</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
                <?php
				if(isset($_REQUEST['idd']))
                {
				$owner_name = $obj_mem_child_details->owner_name($_REQUEST['idd']);
				?>    
				<input type="hidden" name="member_id" value="<?php echo $_REQUEST['idd'];?>">
				<?php					
				}
				else
				{
					if(isset($_SESSION['admin']))
					{
						echo $_SESSION['owner_name'];
				?>
                		<input type="hidden" name="member_id" value="<?php echo $_SESSION['owner_id'];?>">    
                <?php
					}
					else
					{
						echo $_SESSION['owner_name'];
					?>
                    	<input type="hidden" name="member_id" value="<?php echo $_REQUEST['mem_id'];?>">
                    <?php	
					}
				}
				?>                
        	</td>
		</tr>
        
		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Child Name</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="child_name" id="child_name" /></td>
		</tr>
        
		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Occupation</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            <select name="child_desg" id="child_desg">
				<?php echo $combo_child_desg=$obj_mem_child_details->combobox("select desg_id,desg from desg where status='Y'"); ?>
			</select>
        	</td>
		</tr>
        
		<tr>
        	<td valign="top"><?php //echo $star;?></td>
			<td>Date Of Birth</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="child_dob" id="child_dob"  class="basics" size="10" readonly style="width:100px;"></td>
		</tr>
        
		<tr>
        	<td valign="top"><?php //echo $star;?></td>
			<td>School/College/Company</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="scc" id="scc" /></td>
		</tr>
        
		<tr>
        	<td valign="top"><?php //echo $star;?></td>
			<td>Standard/Division/Designaion</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="sdd" id="sdd" /></td>
		</tr>
        	
		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Blood Group</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<select name="child_bg" id="child_bg">
					<?php echo $combo_child_bg=$obj_mem_child_details->combobox("select bg_id,bg from bg where status='Y'"); ?>
				</select>
        	</td>
		</tr>
        
		<tr><td colspan="4">&nbsp;</td></tr>
        
		<tr>
			<td colspan="4" align="center">
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="mkm" id="mkm" value="<?php if(isset($_REQUEST['mkm'])){echo 'mkm';}?>">
            <input type="hidden" name="mrs" id="mrs" value="<?php if(isset($_REQUEST['mrs'])){echo 'mrs';}?>">
            
            <?php if(isset($_SESSION['admin'])){?>
            
			<?php if(!isset($_REQUEST['mkm']) && (!isset($_REQUEST['mrs']))){?>
            <input type="submit" name="insert" id="insert" value="Add More">
            &nbsp;&nbsp;
            <input type="button" value="Back" onClick="backk();"> 
            
            &nbsp;&nbsp;
            <input type="button" value="Next Form" onClick="next();"> 
            <?php }else{?>
            <input type="submit" name="insert" id="insert" value="Add">
            <?php }?>
            
            <?php }else{?>
            <input type="submit" name="insert" id="insert" value="Add">
            <?php }?>
            </td>
		</tr>
</table>
</form>


<table align="center" style="display:none;">
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_mem_child_details->pgnation();
echo "<br>";
echo $str = $obj_mem_child_details->display1($str1);
echo "<br>";
$str1 = $obj_mem_child_details->pgnation();
echo "<br>";
?>
</td>
</tr>
</table>

</center>
</div>
<?php include_once "includes/foot.php"; ?>

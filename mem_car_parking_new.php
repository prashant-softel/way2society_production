<?php //include_once "ses_set_ma.php"; ?>
<?php

	include_once("includes/head_s.php");


include_once("classes/mem_car_parking.class.php");
$obj_mem_car_parking = new mem_car_parking($m_dbConn);
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsmem_car_parking.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',6000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }
	
	function next()
	{
		window.location.href = 'mem_bike_parking_new.php?scm&tik_id=<?php echo time();?>&m'	
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
<div id="middle">
<div class="panel panel-info" id="panel" style="display:none;margin-top:6%;margin-left:3.5%; border:none;width:70%">
        <div class="panel-heading" id="pageheader">Member Car Details</div>


<br>
<center>
<?php if(isset($_SESSION['role']) && $_SESSION['role']==ROLE_MEMBER){?>
<a href="view_member_profile.php?prf&id=<?php echo $_GET['mem_id'];?>" style="color:#00F; text-decoration:none;"><b>Go to profile view</b></a>
<?php }else{ ?>
	<a href="view_member_profile_adm.php?scm&id=<?php echo $_SESSION['owner_id'];?>&tik_id=<?php echo time();?>&m" style="color:#00F; text-decoration:none;"><b>Go to profile view</b></a>
<?php } ?>
</center>

<center>
<form name="mem_car_parking" id="mem_car_parking" method="post" action="process/mem_car_parking.process.php" onSubmit="return val();">

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
				$owner_name = $obj_mem_car_parking->owner_name($_REQUEST['idd']);
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
			<td>Parking Slot</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="parking_slot" id="parking_slot" /></td>
		</tr>
        
		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Car Registration No.</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="car_reg_no" id="car_reg_no" /></td>
		</tr>
		
        <tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Car Owner</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="car_owner" id="car_owner" /></td>
		</tr>
        
		<tr>
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Car Model</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="car_model" id="car_model" /></td>
		</tr>
		
        <tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Car Make</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="car_make" id="car_make" /></td>
		</tr>
        
		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Car Colour</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="car_color" id="car_color" /></td>
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
$str1 = $obj_mem_car_parking->pgnation();
echo "<br>";
echo $str = $obj_mem_car_parking->display1($str1);
echo "<br>";
$str1 = $obj_mem_car_parking->pgnation();
echo "<br>";
?>
</td>
</tr>
</table>

</center>
</div>
<?php include_once "includes/foot.php"; ?>

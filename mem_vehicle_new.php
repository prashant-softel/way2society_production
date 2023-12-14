<?php include_once("includes/head_s.php");

include_once("classes/mem_car_parking.class.php");
$obj_mem_car_parking = new mem_car_parking($m_dbConn);

include_once("classes/mem_other_family.class.php");
$obj_mem_other_family = new mem_other_family($m_dbConn);

$unit_details = $obj_mem_other_family->unit_details($_REQUEST['mem_id']);

if($_SESSION['role'] == ROLE_MEMBER || $_SESSION['role'] == ROLE_ADMIN_MEMBER)
{
	if($_SESSION['unit_id'] <> $unit_details['unit_id'])
	{
		?>
			<script>
				window.location.href = 'Dashboard.php';
			</script>

		<?php
		exit();
	}
}
$UnitBlock = $_SESSION["unit_blocked"];
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
		window.location.href = 'mem_vehicle_new.php?scm&tik_id=<?php echo time();?>&m'	
	}  
	function backk()
	{
		window.location.href = 'mem_other_family_new.php?scm&tik_id=<?php echo time();?>&m'	
	}  
	
	$( document ).ready(function() {
		 
		var isblocked = '<?php echo $UnitBlock ?>';
		//alert(isblocked);
		if(isblocked==1)
		{
			alert("We are sorry,but your access has been blocked for this feature . Please contact your Managing Committee for resolution .");
			window.location.href='view_member_profile.php?prf&id=<?php echo $_SESSION['owner_id'];?>&idd=<?php echo time();?>';	
			
			
		}
	});
	
	</script>
</head>

<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<div id="middle">
<div class="panel panel-info" id="panel" style="display:none;margin-top:10px;margin-left:3.5%; border:none;width:70%">
        <div class="panel-heading" id="pageheader">Add Vehicle Details</div>
<br>
<button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;margin-left:10%" id="btnBack"><i class="fa  fa-arrow-left"></i></button>
<center>
<?php if(isset($_SESSION['role']) && $_SESSION['role']==ROLE_MEMBER){?>
<input type="button" class="btn btn-primary" onClick="window.location.href='view_member_profile.php?prf&id=<?php echo $_GET['mem_id'];?>'"  style="float:left;" value="Go to profile view">

<?php }else{ ?>
<input type="button" class="btn btn-primary" onClick="window.location.href='view_member_profile.php?scm&id=<?php echo $_GET['mem_id'];?>&tik_id=<?php echo time();?>&m'"  style="" value="Go to profile view">
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
		   	<td valign="middle"></td>
			<td>Unit No.</td>
            <td>&nbsp; : &nbsp;</td>
			<td><?php echo $unit_details['unit_no'];?></td>
		</tr>
	
		<tr><td colspan="4">&nbsp;</td></tr>
		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Vehicle Type</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
				<select name="vehicle_type" id="vehicle_type">
					<option value="2">Bike</option>
					<option value="4">Car</option>
				</select>
			</td>
		</tr>

		<?php
			if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN && $_SESSION['role'] != ROLE_MANAGER)
			{
				?>
					<input type="hidden" name="parking_slot" id="parking_slot" value="">
				<?php
			}
			else
			{
				?>
					<tr>
			        	<td valign="middle"></td>
						<td>Parking Slot No.</td>
			            <td>&nbsp; : &nbsp;</td>
						<td><input type="text" name="parking_slot" id="parking_slot" /></td>
					</tr>
				<?php
			}
		?>

		<?php
			if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN && $_SESSION['role'] != ROLE_MANAGER)
			{
				?>
					<input type="hidden" name="parking_sticker" id="parking_sticker" value="">
				<?php
			}
			else
			{
				?>
					<tr>
			        	<td valign="middle"></td>
						<td>Parking Sticker No.</td>
			            <td>&nbsp; : &nbsp;</td>
						<td><input type="text" name="parking_sticker" id="parking_sticker" /></td>
					</tr>
				<?php
			}
		?>
        <tr>
        	<td valign="middle"></td>
			<td>Parking Type:</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<select id="parkingType" name="parkingType">
                	<?php
						echo $obj_mem_car_parking->combobox07("Select `Id`,`ParkingType` from `parking_type` where Status = 'Y' AND IsVisible = '1'", "0");
                    ?>
                </select>
            </td>
		</tr>
		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Registration No.</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="car_reg_no" id="car_reg_no" /></td>
		</tr>
		
        <tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Owner Name</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="car_owner" id="car_owner" /></td>
		</tr>
        
        <tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Vehicle Make</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="car_make" id="car_make" /></td>
		</tr>

		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Vehicle Model</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="car_model" id="car_model" /></td>
		</tr>
		
		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Vehicle Colour</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="car_color" id="car_color" /></td>
		</tr>
		
        <tr><td colspan="4">&nbsp;</td></tr>
        
		<tr>
			<td colspan="4" align="center">
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="mkm" id="mkm" value="<?php if(isset($_REQUEST['mkm'])){echo 'mkm';}?>">
            <input type="hidden" name="mrs" id="mrs" value="<?php if(isset($_REQUEST['mrs'])){echo 'mrs';}?>">
            <input type="hidden" name="member_id" id="member_id" value="<?php echo $_GET['mem_id']; ?>">
            <input type="hidden" name="unit_no" id="unit_no" value="<?php echo $unit_details['unit_no']; ?>">
            
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

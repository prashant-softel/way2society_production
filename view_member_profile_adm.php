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
?>

<?php
include_once "classes/view_member_profile_adm.class.php" ;
$obj_view_member_profile_adm = new view_member_profile_adm($m_dbConn);

$show_member_main 		 = $obj_view_member_profile_adm->show_member_main();
$show_mem_spouse_details = $obj_view_member_profile_adm->show_mem_spouse_details();
$show_mem_child_details  = $obj_view_member_profile_adm->show_mem_child_details();
$show_mem_other_family   = $obj_view_member_profile_adm->show_mem_other_family();
$show_mem_car_parking    = $obj_view_member_profile_adm->show_mem_car_parking();
$show_mem_bike_parking   = $obj_view_member_profile_adm->show_mem_bike_parking();
$share_certificate_details = $obj_view_member_profile_adm->show_share_certificate_details();
$show_share_certificate = $obj_view_member_profile_adm->show_share_certificate();

$url = basename(basename($_SERVER['REQUEST_URI']));

$_SESSION['owner_name'] = $show_member_main[0]['owner_name'];
$_SESSION['owner_id'] = $_GET['id'];

//print_r($_SESSION);
?>

<style>
th
{
	color:#FFFFFF;
}
</style>

<body>
<br>
<div class="panel panel-info" id="panel" style="display:none;margin-top:6%;margin-left:3.5%; border:none;width:70%">
        <div class="panel-heading" id="pageheader">Profile View</div>

<br>
<center><button type="button" class="btn btn-primary" onClick="window.location.href='list_member.php?scm'">Back to list</button></center>
<br>

<center>
<table align="center"> <!-- class="profile_table" -->
<tr><td>

<table align="center" border="0">
<tr align="left">
	<td width="130"><b>Wing</b></td>
    <td width="10">:</td>
    <td width="250"><?php echo $show_member_main[0]['wing'];?></td>
    
    <td width="150"><b>Flat No.</b></td>
    <td width="10">:</td>
    <td><?php echo $show_member_main[0]['unit_no'];?></td>
</tr>

<tr align="left">
	<td><b>Owner Name</b></td>
    <td>:</td>
    <td align="left"><?php echo $show_member_main[0]['owner_name'];?></td>
    
    <td><b>Area</b></td>
    <td>:</td>
    <td><?php echo $show_member_main[0]['area']. ' Sq.Ft';?></td> 
</tr>

<tr align="left">
	<td><b>Residence Number</b></td>
    <td>:</td>
    <td><?php echo $show_member_main[0]['resd_no'];?></td>
    
    <td><b>Mobile No.</b></td>
    <td>:</td>
    <td><?php echo $show_member_main[0]['mob'];?></td>
</tr>

<tr align="left">
	<td><b>Office Number</b></td>
    <td>:</td>
    <td><?php echo $show_member_main[0]['off_no'];?></td>
    
    <td><b>Alternate Mobile No.</b></td>
    <td>:</td>
    <td><?php echo $show_member_main[0]['alt_mob'];?></td>
</tr>

<tr align="left">
	<td><b>Office Address</b></td>
    <td>:</td>
    <td><?php echo $show_member_main[0]['off_add'];?></td>
    
     <td><b>Intercom No.</b></td>
    <td>:</td>
    <td><?php echo $show_member_main[0]['intercom_no'];?></td>
</tr>

<tr align="left">
	<td><b>Designation</b></td>
    <td>:</td>
    <td colspan="5" align="left"><?php echo $show_member_main[0]['desg'];?></td>
</tr>

<tr align="left">
	<td><b>Email ID</b></td>
    <td>:</td>
    <td><?php echo $show_member_main[0]['email'];?></td>
	
    <td><b>Alternate Email ID</b></td>
    <td>:</td>
    <td><?php if($show_member_main[0]['alt_email']==''){echo 'Not Mentioned';}else{echo $show_member_main[0]['alt_email'];}?></td>
</tr>

<tr align="left">
	<td><b>Date of Birth</b></td>
    <td>:</td>
    <td><?php echo $show_member_main[0]['dob'];?></td>
    
    <td><b>Wedding Anniversary</b></td>
    <td>:</td>
    <td><?php if($show_member_main[0]['wed_any']==''){echo 'Not Mentioned';}else{echo $show_member_main[0]['wed_any'];}?></td>
</tr>

<tr align="left">
	<td><b>Blood Group</b></td>
    <td>:</td>
    <td><?php echo $show_member_main[0]['bg'];?></td>
    
    <td><b>Parking No</b></td>
    <td>:</td>
    <td><?php echo $show_member_main[0]['parking_no'];?></td>
</tr>

<?php if($show_share_certificate == 1)
{ ?>
<tr height="25" valign="bottom">
    <td colspan="6" align="left"><b>Share Certificate Details : </b></td>
</tr>

<tr>
	<td colspan="6">
    	<table border="0">
        <tr height="30" bgcolor="#999999">
            <th width="200">Share Certicate</th>
            <th width="250">From</th>
            <th width="180">To</th>
            <th width="250">Total</th>            
        </tr>    
        <?php
		if($share_certificate_details<>"")
		{
			foreach($share_certificate_details as $k => $v)
			{
			?>
			<tr height="25" bgcolor="#BDD8F4">
				<td align="center"><?php echo $share_certificate_details[$k]['share_certificate'];?></td>
				<td align="center"><?php echo $share_certificate_details[$k]['share_certificate_from'];?></td>
				<td align="center"><?php echo $share_certificate_details[$k]['share_certificate_to'];?></td>
				<td align="center"><?php if($share_certificate_details[$k]['share_certificate_from'] > 0 || $share_certificate_details[$k]['share_certificate_to'] > 0)
										{
											echo ($share_certificate_details[$k]['share_certificate_to'] - $share_certificate_details[$k]['share_certificate_from'] + 1) ;
										}else
										{echo '0';}?></td>            
			</tr>
			<?php
			}
		}
		else
		{
			?>
            <tr height="25"><td colspan="4" align="center"><font color="#FF0000"><b>Records has not inserted<!--  by admin --></b></font></td></tr>
            <?php	
		}
		?>
        </table>
    </td>
</tr>
<?php } ?>

<tr height="25" valign="bottom">
    <td colspan="6" align="left"><b>Spouse Details : 
	<?php if($_SESSION['is_year_freeze'] == 0 && !isset($_SESSION['sadmin'])){
    if($show_mem_spouse_details==""){ ?>
    &nbsp;&nbsp;<a href="mem_spouse_details_new.php?scm&mkm&tik_id=<?php echo time();?>&m&mem_id=<?php echo $show_member_main[0]['member_id'];?>" style="color:#00F; text-decoration:none;"><b>Add New</b></a>
	<?php }else{ ?>
    &nbsp;&nbsp;<a href="view_member_profile_adm_edit.php?scm&mkm&&id=<?php echo $_GET['id'];?>&tik_id=<?php echo time();?>&m" style="color:#00F; text-decoration:none;"><b>Edit here</b></a>
    <?php } ?>
	<?php }?>
    </b></td>
</tr>

<tr>
	<td colspan="6">
    	<table border="0">
        <tr height="30" bgcolor="#999999">
            <th width="120">Name</th>
            <th width="180">Designation</th>
            <th width="110">Date of Birth</th>
            <th width="180">Office Address</th>
            <th width="180">Office Number</th>
            <th width="120">Blood Group</th>
        </tr>    
        <?php
		if($show_mem_spouse_details<>"")
		{
		foreach($show_mem_spouse_details as $k => $v)
		{
		?>
        <tr height="25" bgcolor="#BDD8F4">
        	<td align="center"><?php echo $show_mem_spouse_details[$k]['spouse_name'];?></td>
            <td align="center"><?php echo $show_mem_spouse_details[$k]['desg'];?></td>
            <td align="center"><?php if($show_mem_spouse_details[$k]['spouse_dob']==''){echo 'Not Mentioned';}else{echo $show_mem_spouse_details[$k]['spouse_dob'];}?></td>
            <td align="center"><?php if($show_mem_spouse_details[$k]['spouse_off_add']==''){echo 'Not Mentioned';}else{echo $show_mem_spouse_details[$k]['spouse_off_add'];}?></td>
            <td align="center"><?php if($show_mem_spouse_details[$k]['spouse_off_no']==''){echo 'Not Mentioned';}else{echo $show_mem_spouse_details[$k]['spouse_off_no'];}?></td>
            <td align="center"><?php echo $show_mem_spouse_details[$k]['bg'];?></td>
        </tr>
        <?php
		}
		}
		else
		{
			?>
            <tr height="25"><td colspan="6" align="center"><font color="#FF0000"><b>Records has not inserted<!--  by admin --></b></font></td></tr>
            <?php	
		}
		?>
        </table>
    </td>
</tr>


<tr height="25" valign="bottom">
    <td colspan="6" align="left"><b>Child Details : <?php if(!isset($_SESSION['sadmin']) && $_SESSION['is_year_freeze'] == 0){?>&nbsp;&nbsp;<a href="mem_child_details_new.php?scm&mkm&tik_id=<?php echo time();?>&m&mem_id=<?php echo $show_member_main[0]['member_id'];?>" style="color:#00F; text-decoration:none;"><b>Add New</b></a><?php } ?> </b></td>
</tr>

<tr>
	<td colspan="6">
    	<table border="0">
        <tr height="30" bgcolor="#999999">
            <th width="120">Name</th>
            <th width="180">Designation</th>
            <th width="110">Date of Birth</th>
            <th width="180">School/College/Company</th>
            <th width="180">Standard/Division/Post</th>
            <th width="120">Blood Group</th>
        </tr> 
        <?php
		if($show_mem_child_details<>"")
		{
		foreach($show_mem_child_details as $k1 => $v1)
		{
		?>   
        <tr height="25" bgcolor="#BDD8F4">
        	<td align="center"><?php echo $show_mem_child_details[$k1]['child_name'];?></td>
            <td align="center"><?php echo $show_mem_child_details[$k1]['desg'];?></td>
            <td align="center"><?php if($show_mem_child_details[$k1]['child_dob']==''){echo 'Not Mentioned';}else{echo $show_mem_child_details[$k1]['child_dob'];}?></td>
            <td align="center"><?php if($show_mem_child_details[$k1]['scc']==''){echo 'Not Mentioned';}else{echo $show_mem_child_details[$k1]['scc'];}?></td>
            <td align="center"><?php if($show_mem_child_details[$k1]['sdd']==''){echo 'Not Mentioned';}else{echo $show_mem_child_details[$k1]['sdd'];}?></td>
            <td align="center"><?php echo $show_mem_child_details[$k1]['bg'];?></td>
        </tr>
        <?php
		}
		}
		else
		{
			?>
            <tr height="25"><td colspan="6" align="center"><font color="#FF0000"><b>Records has not inserted<!--  by admin --></b></font></td></tr>
            <?php	
		}
		?>
        </table>
    </td>
</tr>


<tr height="25" valign="bottom">
    <td colspan="6" align="left"><b>Details of other member staying at the flat&nbsp;: <?php if(!isset($_SESSION['sadmin']) && $_SESSION['is_year_freeze'] == 0){?>&nbsp;&nbsp;<a href="mem_other_family_new.php?scm&mkm&tik_id=<?php echo time();?>&m&mem_id=<?php echo $show_member_main[0]['member_id'];?>" style="color:#00F; text-decoration:none;"><b>Add New</b></a><?php } ?> </b></td>
</tr>

<tr>
	<td colspan="6">
    	<table border="0">
        <tr height="30" bgcolor="#999999">
            <th width="120">Name</th>
            <th width="180">Relation with flat owner</th>
            <th width="110">Date of Birth</th>
            <th width="180">Designation</th>
            <th width="180">School/College/Company</th>
            <th width="120">Blood Group</th>
        </tr>
        <?php
		if($show_mem_other_family<>"")
		{
		foreach($show_mem_other_family as $k2 => $v2)
		{
		?>     
        <tr height="25" bgcolor="#BDD8F4">
        	<td align="center"><?php echo $show_mem_other_family[$k2]['other_name'];?></td>
            <td align="center"><?php echo $show_mem_other_family[$k2]['relation'];?></td>
            <td align="center"><?php if($show_mem_other_family[$k2]['other_dob']==''){echo 'Not Mentioned';}else{echo $show_mem_other_family[$k2]['other_dob'];}?></td>
            <td align="center"><?php echo $show_mem_other_family[$k2]['desg'];?></td>
            <td align="center"><?php if($show_mem_other_family[$k2]['ssc']==''){echo 'Not Mentioned';}else{echo $show_mem_other_family[$k2]['ssc'];}?></td>
            <td align="center"><?php echo $show_mem_other_family[$k2]['bg'];?></td>
        </tr>
        <?php
		}
		}
		else
		{
			?>
            <tr height="25"><td colspan="6" align="center"><font color="#FF0000"><b>Records has not inserted<!--  by admin --></b></font></td></tr>
            <?php	
		}
		?>
        </table>
    </td>
</tr>

<tr height="25" valign="bottom">
    <td colspan="6" align="left"><b>Car Details : <?php if(!isset($_SESSION['sadmin']) && $_SESSION['is_year_freeze'] == 0){?>&nbsp;&nbsp;<a href="mem_vehicle_new.php?scm&mkm&tik_id=<?php echo time();?>&m&mem_id=<?php echo $show_member_main[0]['member_id'];?>" style="color:#00F; text-decoration:none;"><b>Add New</b></a><?php }?> </b></td>
</tr>

<tr>
	<td colspan="6">
    	<table border="0">
        <tr height="30" bgcolor="#999999">
            <th width="120">Car Owner</th>
            <th width="180">Car Registration No.</th>
            <th width="110">Parking Slot</th>
            <th width="180">Car Make</th>
            <th width="180">Car Model</th>
            <th width="120">Car Colour</th>
        </tr>
        <?php
		if($show_mem_car_parking<>"")
		{
		foreach($show_mem_car_parking as $k3 => $v3)
		{
		?>       
        <tr height="25" bgcolor="#BDD8F4">
        	<td align="center"><?php echo $show_mem_car_parking[$k3]['car_owner'];?></td>
            <td align="center"><?php echo $show_mem_car_parking[$k3]['car_reg_no'];?></td>
            <td align="center"><?php echo $show_mem_car_parking[$k3]['parking_slot'];?></td>
            <td align="center"><?php echo $show_mem_car_parking[$k3]['car_make'];?></td>
            <td align="center"><?php if($show_mem_car_parking[$k3]['car_model']==''){echo 'Not Mentioned';}else{echo $show_mem_car_parking[$k3]['car_model'];}?></td>
            <td align="center"><?php echo $show_mem_car_parking[$k3]['car_color'];?></td>
        </tr>
        <?php
		}
		}
		else
		{
			?>
            <tr height="25"><td colspan="6" align="center"><font color="#FF0000"><b>Records has not inserted<!--  by admin --></b></font></td></tr>
            <?php	
		}
		?>
        </table>
    </td>
</tr>


<tr height="25" valign="bottom">
    <td colspan="6" align="left"><b>Bike Details :  <?php if(!isset($_SESSION['sadmin']) && $_SESSION['is_year_freeze'] == 0){?>&nbsp;&nbsp;<a href="mem_vehicle_new.php?scm&mkm&tik_id=<?php echo time();?>&m&mem_id=<?php echo $show_member_main[0]['member_id'];?>" style="color:#00F; text-decoration:none;"><b>Add New</b></a><?php } ?> </b></td>
</tr>

<tr>
	<td colspan="6">
    	<table border="0">
        <tr height="30" bgcolor="#999999">
            <th width="120">Bike Owner</th>
            <th width="180">Bike Registration No.</th>
            <th width="110">Parking Slot</th>
            <th width="180">Bike Make</th>
            <th width="180">Bike Model</th>
            <th width="120">Bike Colour</th>
        </tr>    
        <?php
		if($show_mem_bike_parking<>"")
		{
		foreach($show_mem_bike_parking as $k4 => $v4)
		{
		?>       
        <tr height="25" bgcolor="#BDD8F4">
        	<td align="center"><?php echo $show_mem_bike_parking[$k4]['bike_owner'];?></td>
            <td align="center"><?php echo $show_mem_bike_parking[$k4]['bike_reg_no'];?></td>
            <td align="center"><?php echo $show_mem_bike_parking[$k4]['parking_slot'];?></td>
            <td align="center"><?php echo $show_mem_bike_parking[$k4]['bike_make'];?></td>
            <td align="center"><?php if($show_mem_bike_parking[$k4]['bike_model']==''){echo 'Not Mentioned';}else{echo $show_mem_bike_parking[$k4]['bike_model'];}?></td>
            <td align="center"><?php echo $show_mem_bike_parking[$k4]['bike_color'];?></td>
        </tr>
        <?php
		}
		}
		else
		{
			?>
            <tr height="25"><td colspan="6" align="center"><font color="#FF0000"><b>Records has not inserted<!--  by admin --></b></font></td></tr>
            <?php	
		}
		?>
        </table>
    </td>
</tr>

<tr height="25" valign="bottom" align="left">
	<td><b>Relative for emergency contact</b></td>
    <td>:</td>
    <td colspan="5" align="left"><?php if($show_member_main[0]['eme_rel_name']==''){echo 'Not Mentioned';}else{echo $show_member_main[0]['eme_rel_name'];}?></td>
</tr>

<tr align="left">
	<td><b>Emergency Contact No.</b></td>
    <td>:</td>
    <td><?php if($show_member_main[0]['eme_contact_1']==''){echo 'Not Mentioned';}else{echo $show_member_main[0]['eme_contact_1'];}?></td>
    
    <td><b>Alternate Emergency Contact No.</b></td>
    <td>:</td>
    <td><?php if($show_member_main[0]['eme_contact_2']==''){echo 'Not Mentioned';}else{echo $show_member_main[0]['eme_contact_2'];}?></td>
</tr>
</table>
<?php if(IsReadonlyPage() == false){?>
		<center><!--<a href="view_member_profile_adm_edit.php?scm&mkm&&id=<?php //echo $_GET['id'];?>&tik_id=<?php //echo time();?>&m" style="color:#00F; text-decoration:none; text-height:40px;"><b>Edit here</b></a>-->
<input type="button"   class="btn btn-primary" value="Edit"  style="width:100px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;"onClick="window.location.href='view_member_profile_adm_edit.php?scm&mkm&&id=<?php echo $_GET['id'];?>&tik_id=<?php echo time();?>&m'"></center>
<?php }?>
</td></tr></table>


</center>
</div>
<?php include_once "includes/foot.php"; ?>

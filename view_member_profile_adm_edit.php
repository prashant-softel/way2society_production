<?php //include_once "ses_set.php"; ?>
<?php include_once("includes/head_s.php");?>

<?php
include_once "classes/view_member_profile_adm_edit.class.php" ;
$obj_view_member_profile_adm_edit = new view_member_profile_adm_edit($m_dbConn);

$show_member_main 		 = $obj_view_member_profile_adm_edit->show_member_main();
$show_mem_spouse_details = $obj_view_member_profile_adm_edit->show_mem_spouse_details();
$show_mem_child_details  = $obj_view_member_profile_adm_edit->show_mem_child_details();
$show_mem_other_family   = $obj_view_member_profile_adm_edit->show_mem_other_family();
$show_mem_car_parking    = $obj_view_member_profile_adm_edit->show_mem_car_parking();
$show_mem_bike_parking   = $obj_view_member_profile_adm_edit->show_mem_bike_parking();
$share_certificate_details = $obj_view_member_profile_adm_edit->show_share_certificate_details();
$show_share_certificate = $obj_view_member_profile_adm_edit->show_share_certificate();

$url = basename(basename($_SERVER['REQUEST_URI']));

$_SESSION['owner_name'] = $show_member_main[0]['owner_name'];
$_SESSION['owner_id'] = $_GET['id'];


?>

<style>

#errorBox
{
	color:hsla(0,100%,50%,1);
	font-weight: bold;
	
	
}
th
{
	color:#FFFFFF;
}

</style>
<!--<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
<script type="text/javascript" src="javascript/jquery.clockpick.1.2.4.js"></script>
<script type="text/javascript" src="javascript/ui.core.js"></script>
<script type="text/javascript" src="javascript/ui.datepicker_bday.js"></script>-->
<script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
<script type="text/javascript" src="js/jsview_member_profile_mem_edit.js"></script>
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

function calcTotal(from, to, total) 
{
	total.value = 0;
	if(	to.value > 0 || from.value > 0)
	{	
		total.value = to.value - from.value + 1;
	}
}
	
</script>
<body>

<br>	
<div class="panel panel-info" id="panel" style="display:none;margin-top:6%;margin-left:3.5%; border:none;width:70%">
    <div class="panel-heading" id="pageheader">Update Profile</div>
    
<br>
<center><button type="button" class="btn btn-primary" onClick="window.location.href='list_member.php?scm'">Back to list</button></center>
<br>

<center>
<form method="post" name="memberform" action="process/view_member_profile_adm_edit.process.php" onSubmit="return validate();">
<?php $star = "<font color='#FF0000'>*&nbsp;</font>";?> 
<input type="hidden" name="id" value="<?php echo $_GET['id'];?>" />
<table align="center">
<div id="errorBox"></div></table>

<table align="center"> <!-- class="profile_table" -->
<tr><td>

<table align="center" border="0">
<tr>
	<td width="130"><b>Wing</b></td>
    <td width="10">:</td>
    <td width="250"><?php echo $show_member_main[0]['wing'];?></td>
    
    <td width="150"><b>Flat No.</b></td>
    <td width="10">:<input type="hidden" name="flat_no" id="flat_no" value="<?php echo $show_member_main[0]['unit_no'];?>"/></td>
    <td><?php echo $show_member_main[0]['unit_no'];?></td>
</tr>

<tr>
	<td><?php echo $star;?><b>Owner Name</b></td>
    <td>:</td>
    <td align="left">
    <input type="text" name="owner_name" id="owner_name" value="<?php echo $show_member_main[0]['owner_name'];?>" style="width:120px;"/>
    </td>
    
    <td><b>Area</b></td>
    <td>:</td>
    <td><?php echo $show_member_main[0]['area']. ' Sq.Ft';?></td> 
</tr>

<tr>
	<td><?php echo $star;?><b>Residence Number</b></td>
    <td>:</td>
    <td>
    <input type="text" name="resd_no" id="resd_no" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" value="<?php echo $show_member_main[0]['resd_no'];?>" style="width:120px;"/>
    </td>
    
    <td><?php echo $star;?><b>Mobile No.</b></td>
    <td>:</td>
    <td>
    <input type="text" name="mob" id="mob" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" maxlength="10" value="<?php echo $show_member_main[0]['mob'];?>" style="width:120px;"/>
    </td>
</tr>

<tr>
	<td><b>Office Number</b></td>
    <td>:</td>
    <td>
    <input type="text" name="off_no" id="off_no" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" value="<?php echo $show_member_main[0]['off_no'];?>" style="width:120px;"/>
    </td>
    
    <td><b>Alternate Mobile No.</b></td>
    <td>:</td>
    <td>
    <input type="text" name="alt_mob" id="alt_mob" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" maxlength="10" value="<?php echo $show_member_main[0]['alt_mob'];?>" style="width:120px;"/>
    </td>
</tr>

<tr>
	<td><b>Office Address</b></td>
    <td>:</td>
    <td>
    <input type="text" name="off_add" id="off_add" value="<?php echo $show_member_main[0]['off_add'];?>" style="width:120px;" size="104"/>
    </td>
    
    <td><b>Intercom No.</b></td>
    <td>:</td>
    <td>
    <input type="text" name="intercom_no" id="intercom_no" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" maxlength="10" value="<?php echo $show_member_main[0]['intercom_no'];?>" style="width:120px;"/>
    </td>
</tr>

<tr>
	<td><b>Designation</b></td>
    <td>:</td>
    <td colspan="5" align="left">
    <select name="desg" id="desg" style="width:142px;">
		<?php echo $combo_desg = $obj_view_member_profile_adm_edit->combobox11("select desg_id,desg from desg where status='Y' order by desg" , $show_member_main[0]['desg_id']); ?>
    </select>
    </td>
</tr>

<tr>
	<td><?php echo $star;?><b>Email ID</b></td>
    <td>:</td>
    <td>
    <input type="text" name="email" id="email" value="<?php echo $show_member_main[0]['email'];?>" style="width:120px;" size="30"/>
    </td>
	
    <td><b>Alternate Email ID</b></td>
    <td>:</td>
    <td>
    <input type="text" name="alt_email" id="alt_email" value="<?php echo $show_member_main[0]['alt_email'];?>" style="width:120px;" size="30"/>
    </td>
</tr>

<tr>
	<td><b>Date of Birth</b></td>
    <td>:</td>
    <td>
    <input type="text" name="dob" id="dob"  class="basics" size="10" value="<?php echo $show_member_main[0]['dob'];?>" style="width:70px;" readonly/>
    </td>
    
    <td><b>Wedding Anniversary</b></td>
    <td>:</td>
    <td>
    <input type="text" name="wed_any" id="wed_any"  class="basics" size="10" value="<?php echo $show_member_main[0]['wed_any'];?>" style="width:70px;" readonly/>
    </td>
</tr>

<tr>
	<td><b>Blood Group</b></td>
    <td>:</td>
    <td>
    <select name="bg" id="bg" style="width:142px;">
		<?php echo $combo_desg = $obj_view_member_profile_adm_edit->combobox11("select bg_id,`bg` from bg where status='Y'" , $show_member_main[0]['bg_id']); ?>
    </select>
    </td>
    
    <td><b>Parking No</b></td>
    <td>:</td>
    <td>
    <input type="text" name="parkingNo" id="parkingNo" value="<?php echo $show_member_main[0]['parking_no'];?>" style="width:120px;" size="30"/>
    </td>
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
				<td align="center">
            		<input type="text" name="share_certificate" id="share_certificate" value="<?php echo $share_certificate_details[$k]['share_certificate'];?>" style="width:120px;"/>
            	</td>                
                <td align="center">
                <input type="number" name="share_certificate_from" id="share_certificate_from" value="<?php echo $share_certificate_details[$k]['share_certificate_from'];?>" style="width:120px;" onChange="calcTotal(this.form.share_certificate_from, this.form.share_certificate_to,this.form.total);"/>
                </td>
                <td align="center">
                <input type="number" name="share_certificate_to" id="share_certificate_to" value="<?php echo $share_certificate_details[$k]['share_certificate_to'];?>" style="width:120px;" onChange="calcTotal(this.form.share_certificate_from, this.form.share_certificate_to,this.form.total);" />
                </td>
                <td align="center">
                <input type="number" name="total"  value="<?php if($share_certificate_details[$k]['share_certificate_from'] > 0 || $share_certificate_details[$k]['share_certificate_to'] > 0)
														{
															echo ($share_certificate_details[$k]['share_certificate_to'] - $share_certificate_details[$k]['share_certificate_from'] + 1) ;
														}else
														{echo '0';}?>" style="width:120px;" readonly />
                </td>
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
    <td colspan="6" align="left"><b>Spouse Details : &nbsp;&nbsp;<a href="mem_spouse_details_new.php?scm&mkm&tik_id=<?php echo time();?>&m" style="color:#00F; text-decoration:none;"><b><!--Add New--></b></a></b></td>
</tr>

<tr>
	<td colspan="6">
    	<table border="0">
        <tr height="30" bgcolor="#999999">
            <th width="130">Name</th>
            <th width="180">Designation</th>
            <th width="130">Date of Birth</th>
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
        <tr height="25">
        	<td align="center">
            <input type="text" name="spouse_name" id="spouse_name" value="<?php echo $show_mem_spouse_details[$k]['spouse_name'];?>" style="width:120px;"/>
            </td>
            <td align="center">
            <select name="spouse_desg" id="spouse_desg" style="width:112px;">
				<?php echo $combo_spouse_desg=$obj_view_member_profile_adm_edit->combobox11("select desg_id,desg from desg where status='Y'" , $show_mem_spouse_details[$k]['spouse_desg']); ?>
            </select>
            </td>
            <td align="center">
            <input type="text" name="spouse_dob" id="spouse_dob" class="basics" size="9" readonly value="<?php echo $show_mem_spouse_details[$k]['spouse_dob'];?>" style="width:70px;"/>
            </td>
            <td align="center">
            <input type="text" name="spouse_off_add" id="spouse_off_add" value="<?php echo $show_mem_spouse_details[$k]['spouse_off_add'];?>" style="width:120px;" size="25"/>
            </td>
            <td align="center">
            <input type="text" name="spouse_off_no" id="spouse_off_no" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" value="<?php echo $show_mem_spouse_details[$k]['spouse_off_no'];?>" style="width:120px;" size="14"/>
            </td>
            <td align="center">
            <select name="spouse_bg" id="spouse_bg" style="width:112px;">
				<?php echo $combo_desg = $obj_view_member_profile_adm_edit->combobox11("select bg_id,`bg` from bg where status='Y'" , $show_mem_spouse_details[$k]['bg_id']); ?>
            </select>
            </td>
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
    <td colspan="6" align="left"><b>Child Details : &nbsp;&nbsp;<a href="mem_child_details_new.php?scm&mkm&tik_id=<?php echo time();?>&m" style="color:#00F; text-decoration:none;"><b><!--Add New--></b></a></b></td>
</tr>

<tr>
	<td colspan="6">
    	<table border="0">
        <tr height="30" bgcolor="#999999">
            <th width="130">Name</th>
            <th width="180">Designation</th>
            <th width="130">Date of Birth</th>
            <th width="180">School/College/Company</th>
            <th width="180">Standard/Division/Post</th>
            <th width="120">Blood Group</th>
        </tr> 
        <?php
		if($show_mem_child_details<>"")
		{
			$ii0 = 1;
		foreach($show_mem_child_details as $k1 => $v1)
		{
		?>   
        <input type="hidden" name="mem_child_details_id<?php echo $ii0;?>" value="<?php echo $show_mem_child_details[$k1]['mem_child_details_id'];?>" />
        <tr height="25">
        	<td align="center">
            <input type="text" name="child_name<?php echo $ii0;?>" id="child_name" value="<?php echo $show_mem_child_details[$k1]['child_name'];?>" style="width:120px;"/>
            </td>
            <td align="center">
            <select name="child_desg<?php echo $ii0;?>" id="child_desg" style="width:112px;">
		<?php echo $combo_spouse_desg_c=$obj_view_member_profile_adm_edit->combobox11("select desg_id,desg from desg where status='Y'" , $show_mem_child_details[$k1]['desg_id']); ?>
            </select>
            </td>
            <td align="center">
            <input type="text" name="child_dob<?php echo $ii0;?>" id="child_dob" class="basics" size="9" readonly value="<?php echo $show_mem_child_details[$k1]['child_dob'];?>" style="width:70px;"/>
            </td>
            <td align="center">
            <input type="text" name="scc<?php echo $ii0;?>" id="scc" value="<?php echo $show_mem_child_details[$k1]['scc'];?>" style="width:120px;"/>
            </td>
            <td align="center">
            <input type="text" name="sdd<?php echo $ii0;?>" id="sdd" value="<?php echo $show_mem_child_details[$k1]['sdd'];?>" style="width:120px;"/>
            </td>
            <td align="center">
            <select name="child_bg<?php echo $ii0;?>" id="child_bg" style="width:112px;">
			<?php echo $combo_desg_child = $obj_view_member_profile_adm_edit->combobox11("select bg_id,`bg` from bg where status='Y'" , $show_mem_child_details[$k1]['bg_id']); ?>
            </select>
            </td>
        </tr>
        <?php
			$ii0++;
		}
		}
		else
		{
			?>
            <tr height="25"><td colspan="6" align="center"><font color="#FF0000"><b>Records has not inserted<!--  by admin --></b></font></td></tr>
            <?php	
		}
		?>
        <input type="hidden" name="tot_child" value="<?php echo $ii0-1;?>" />
        </table>
    </td>
</tr>


<tr height="25" valign="bottom">
    <td colspan="6" align="left"><b>Details of other member staying at the flat: &nbsp;&nbsp;<a href="mem_other_family_new.php?scm&mkm&tik_id=<?php echo time();?>&m" style="color:#00F; text-decoration:none;"><b><!--Add New--></b></a></b></td>
</tr>

<tr>
	<td colspan="6">
    	<table border="0">
        <tr height="30" bgcolor="#999999">
            <th width="130">Name</th>
            <th width="180">Relation with flat owner</th>
            <th width="130">Date of Birth</th>
            <th width="180">Designation</th>
            <th width="180">School/College/Company</th>
            <th width="120">Blood Group</th>
        </tr>
        <?php
		if($show_mem_other_family<>"")
		{
			$ii1 = 1;
		foreach($show_mem_other_family as $k2 => $v2)
		{
		?>     
        <input type="hidden" name="mem_other_family_id<?php echo $ii1;?>" value="<?php echo $show_mem_other_family[$k2]['mem_other_family_id'];?>" />
        <tr height="25">
        	<td align="center">
            <input type="text" name="other_name<?php echo $ii1;?>" id="other_name" value="<?php echo $show_mem_other_family[$k2]['other_name'];?>" style="width:120px;"/>
            </td>
            <td align="center">
            <input type="text" name="relation<?php echo $ii1;?>" id="relation" value="<?php echo $show_mem_other_family[$k2]['relation'];?>" style="width:120px;"/>
            </td>
            <td align="center">
            <input type="text" name="other_dob<?php echo $ii1;?>" id="other_dob" class="basics" size="9" readonly value="<?php echo $show_mem_other_family[$k2]['other_dob'];?>" style="width:70px;"/>
            </td>
            <td align="center">
            <select name="other_desg<?php echo $ii1;?>" id="other_desg" style="width:112px;">
		<?php echo $combo_spouse_desg_o=$obj_view_member_profile_adm_edit->combobox11("select desg_id,desg from desg where status='Y'" , $show_mem_other_family[$k2]['desg_id']); ?>
            </select>
            </td>
            <td align="center">
            <input type="text" name="ssc_other<?php echo $ii1;?>" id="ssc_other" value="<?php echo $show_mem_other_family[$k2]['ssc'];?>" style="width:120px;"/>
            </td>
            <td align="center">
            <select name="other_bg<?php echo $ii1;?>" id="other_bg" style="width:112px;">
			<?php echo $combo_desg_child = $obj_view_member_profile_adm_edit->combobox11("select bg_id,`bg` from bg where status='Y'" , $show_mem_other_family[$k2]['bg_id']); ?>
            </select>
            </td>
        </tr>
        <?php
			$ii1++;
		}
		}
		else
		{
			?>
            <tr height="25"><td colspan="6" align="center"><font color="#FF0000"><b>Records has not inserted<!--  by admin --></b></font></td></tr>
            <?php	
		}
		?>
        <input type="hidden" name="tot_other" value="<?php echo $ii1-1;?>" />
        </table>
    </td>
</tr>

<tr height="25" valign="bottom">
    <td colspan="6" align="left"><b>Car Details : &nbsp;&nbsp;<a href="mem_vehicle_new.php?scm&mkm&tik_id=<?php echo time();?>&m" style="color:#00F; text-decoration:none;"><b><!--Add New--></b></a></b></td>
</tr>

<tr>
	<td colspan="6">
    	<table border="0">
        <tr height="30" bgcolor="#999999">
            <th width="130">Car Owner</th>
            <th width="180">Car Registration No.</th>
            <th width="110">Parking Slot</th>
            <th width="180">Car Make</th>
            <th width="180">Car Model</th>
            <th width="120">Car Colour</th>
        </tr>
        <?php
		if($show_mem_car_parking<>"")
		{
			$ii2 = 1;
		foreach($show_mem_car_parking as $k3 => $v3)
		{
		?>       
        <input type="hidden" name="mem_car_parking_id<?php echo $ii2;?>" value="<?php echo $show_mem_car_parking[$k3]['mem_car_parking_id'];?>" />
        <tr height="25">
        	<td align="center">
            <input type="text" name="car_owner<?php echo $ii2;?>" id="car_owner" value="<?php echo $show_mem_car_parking[$k3]['car_owner'];?>" style="width:120px;"/>
            </td>
            <td align="center">
            <input type="text" name="car_reg_no<?php echo $ii2;?>" id="car_reg_no" value="<?php echo $show_mem_car_parking[$k3]['car_reg_no'];?>" style="width:120px;" />
            </td>
            <td align="center">
            <input type="text" name="parking_slot<?php echo $ii2;?>" id="parking_slot" value="<?php echo $show_mem_car_parking[$k3]['parking_slot'];?>" style="width:120px;" size="13" />
            </td>
            <td align="center">
            <input type="text" name="car_make<?php echo $ii2;?>" id="car_make" value="<?php echo $show_mem_car_parking[$k3]['car_make'];?>" style="width:120px;" />
            </td>
            <td align="center">
            <input type="text" name="car_model<?php echo $ii2;?>" id="car_model" value="<?php echo $show_mem_car_parking[$k3]['car_model'];?>" style="width:120px;" />
            </td>
            <td align="center">
            <input type="text" name="car_color<?php echo $ii2;?>" id="car_color" value="<?php echo $show_mem_car_parking[$k3]['car_color'];?>" style="width:120px;" />
            </td>
        </tr>
        <?php
			$ii2++;
		}
		}
		else
		{
			?>
            <tr height="25"><td colspan="6" align="center"><font color="#FF0000"><b>Records has not inserted<!--  by admin --></b></font></td></tr>
            <?php	
		}
		?>
        <input type="hidden" name="tot_car" value="<?php echo $ii2-1;?>" />
        </table>
    </td>
</tr>


<tr height="25" valign="bottom">
    <td colspan="6" align="left"><b>Bike Details : &nbsp;&nbsp;<a href="mem_vehicle_new.php?scm&mkm&tik_id=<?php echo time();?>&m" style="color:#00F; text-decoration:none;"><b><!--Add New--></b></a></b></td>
</tr>

<tr>
	<td colspan="6">
    	<table border="0">
        <tr height="30" bgcolor="#999999">
            <th width="130">Bike Owner</th>
            <th width="180">Bike Registration No.</th>
            <th width="110">Parking Slot</th>
            <th width="180">Bike Make</th>
            <th width="180">Bike Model</th>
            <th width="120">Bike Colour</th>
        </tr>    
        <?php
		if($show_mem_bike_parking<>"")
		{
			$ii3 = 1;
		foreach($show_mem_bike_parking as $k4 => $v4)
		{
		?> 
        <input type="hidden" name="mem_bike_parking_id<?php echo $ii3;?>" value="<?php echo $show_mem_bike_parking[$k4]['mem_bike_parking_id'];?>" />      
        <tr height="25">
            <td align="center">
            <input type="text" name="bike_owner<?php echo $ii3;?>" id="bike_owner" value="<?php echo $show_mem_bike_parking[$k4]['bike_owner'];?>" style="width:120px;"/>
            </td>
            <td align="center">
            <input type="text" name="bike_reg_no<?php echo $ii3;?>" id="bike_reg_no" value="<?php echo $show_mem_bike_parking[$k4]['bike_reg_no'];?>" style="width:120px;" />
            </td>
            <td align="center">
            <input type="text" name="bike_parking_slot<?php echo $ii3;?>" id="bike_parking_slot" value="<?php echo $show_mem_bike_parking[$k4]['parking_slot'];?>" style="width:120px;" size="13" />
            </td>
            <td align="center">
            <input type="text" name="bike_make<?php echo $ii3;?>" id="bike_make" value="<?php echo $show_mem_bike_parking[$k4]['bike_make'];?>" style="width:120px;" />
            </td>
            <td align="center">
            <input type="text" name="bike_model<?php echo $ii3;?>" id="bike_model" value="<?php echo $show_mem_bike_parking[$k4]['bike_model'];?>" style="width:120px;" />
            </td>
            <td align="center">
            <input type="text" name="bike_color<?php echo $ii3;?>" id="bike_color" value="<?php echo $show_mem_bike_parking[$k4]['bike_color'];?>" style="width:120px;" />
            </td>
        </tr>
        <?php
			$ii3++;
		}
		}
		else
		{
			?>
            <tr height="25"><td colspan="6" align="center"><font color="#FF0000"><b>Records has not inserted<!--  by admin --></b></font></td></tr>
            <?php	
		}
		?>
        <input type="hidden" name="tot_bike" value="<?php echo $ii3-1;?>" />
        </table>
    </td>
</tr>

<tr><td colspan="10">&nbsp;</td></tr>

<tr height="25" valign="bottom">
	<td><b>Relative for emergency contact</b></td>
    <td>:</td>
    <td colspan="5" align="left">
    <input type="text" name="eme_rel_name" id="eme_rel_name" value="<?php echo $show_member_main[0]['eme_rel_name'];?>" style="width:120px;"/>
    </td>
</tr>

<tr>
	<td><b>Emergency Contact No.</b></td>
    <td>:</td>
    <td>
    <input type="text" name="eme_contact_1" id="eme_contact_1" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" value="<?php echo $show_member_main[0]['eme_contact_1'];?>" style="width:120px;"/>
    </td>
    
    <td><b>Alternate Emergency Contact No.</b></td>
    <td>:</td>
    <td>
    <input type="text" name="eme_contact_2" id="eme_contact_2" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" value="<?php echo $show_member_main[0]['eme_contact_2'];?>" style="width:120px;"/>
    </td>
</tr>
</table>

</td></tr></table>
<br>
<center>
	<input type="submit" name="update" id="insert" value="Update" style="width:100px; height:30px;background-color:#337ab7;color:#FFF;font-family:'Times New Roman', Times, serif; font-style:normal;font-size:16px;border:none;" >
</center>

</form>
</center>
</div>
<?php include_once "includes/foot.php"; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Add New Unit</title>
</head>

<?php include_once("includes/head_s.php");
//include_once("includes/menu.php");
include_once ("check_default.php");
include_once("classes/unit.class.php");
include_once("classes/utility.class.php");
$obj_unit = new unit($m_dbConn, $m_dbConnRoot);
include_once("classes/bill_period.class.php");
$obj_bill_period=new bill_period($m_dbConn);
$obj_utility =new utility($m_dbConn);
$show_wings=$obj_unit->getallwing();
$last_iid = $obj_unit->getLastIID();
$IsGST = $obj_utility->IsGST();

//echo "Last IID: ".$last_iid;

$_SESSION['ssid'] = $_REQUEST['ssid'];
$_SESSION['wwid'] = $_REQUEST['wwid'];
$bIsCurrentYearAndCreationYrMatch = $obj_utility->IsCurrentYearAndCreationYrMatch();

if($_SESSION['role'] == ROLE_ADMIN_MEMBER && $_SESSION['profile'][PROFILE_EDIT_MEMBER] == 0)
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
	<link href="css/messagebox.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/ajax.js"></script>
     <script type="text/javascript" src="js/ajax_new.js"></script> 
	<script type="text/javascript" src="js/jsunit_08112018.js"></script>
    <script type="text/javascript" src="js/validate.js"></script>
    <script type="text/javascript" src="js/populateData.js"></script>
     <script type="text/javascript" src="js/status.js"></script> 
    <script language="javascript" type="application/javascript">
	
	function flatconfigdisable()
	{
		var data = document.getElementById("unit_presentation").value;
		if(data == 3 || data ==4 || data == 5)
		{
			document.getElementById("flat_configuration").disabled = true;
			document.getElementById("flattype").disabled = true;
			document.getElementById("flat_configuration").value = "";
			document.getElementById("flattype").value = "";
			

		}
		else if(data == 1 || data ==2 || data == 6 || data ==7)
		{
			document.getElementById("flat_configuration").disabled = false;
			document.getElementById("flattype").disabled = false;
		}
		}

		
	
	
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }
	
	//$( document ).ready(function() {
		 
		  <?php
		if(isset($_REQUEST['uid']))
		{  
			?>
		var isblocked = '<?php echo $UnitBlock ?>';
		if(isblocked==1)
		{
			//alert("We are sorry,but your access has been blocked for this feature . Please contact your Managing Committee for resolution .");
			window.location.href='suspend.php';
		}
    
	<?php 
		}
	?>
//});

function get_wing(society_id)
{
	document.getElementById('error').style.display = '';	
	document.getElementById('error').innerHTML = 'Wait... Fetching wing under this society';	
	remoteCall("ajax/get_wing.php","society_id="+society_id,"res_get_wing");		
}

function res_get_wing()
{
	var res = sResponse;//alert(res)
	
	document.getElementById('error').style.display = 'none';	
	
	var count = res.split('****');
	var pp = count[0].split('###');
	
	document.getElementById('wing_id').options.length = 0;
	var that = document.getElementById('society_id').value;

	for(var i=0;i<count[1];i++) 
	{		
		var kk = pp[i].split('#');
		var wing_id = kk[0];
		var wing = kk[1];
		document.getElementById('wing_id').options[i] = new Option(wing,wing_id);
	}
	document.getElementById('wing_id').options[i] = new Option('All','');
	document.getElementById('wing_id').value = '';
}
	
	function clear_unit(wing)
	{
		if(wing=='')
		{
			document.getElementById('unit_no').value = '';	
		}
	}
	</script>
    
    	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
	$(document).ready(function() {
		
	  		
      $('#Blocked').click(function(){
        var isChecked = $('#Blocked').is(':checked');
        
        if(isChecked){
			$('#active').show();
	  		$('#reason').show();
			}
        else{
			$('#active').hide();
		}
      });
	  
	 
	 
	  $('#insert').click(function(){
		   var r=$('#reason').val();
		   var isChecked = $('#Blocked').is(':checked');
			if(r.length<1 && isChecked){
					alert("Please Enter Reason to block");
					$('#unitForm').removeAttr('action');
			} else{
				$('#unitForm').attr('action',"process/unit.process.php");
			}
	  });
	   function a(){
		 var isChecked = $('#Blocked').is(':checked');
		 if(isChecked) {
			 $('#active').show();
		}
		else{
			$('#active').hide();
		}
        
    };
	a();
    });
$(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
			defaultDate: new Date(),
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true ,
			yearRange: "-10:+10",
			maxDate: '0'
        })});
</script>

<style>

#active{
 display:none;
 }
</style>
</head>

<?php if(isset($_REQUEST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>
<br>
<div id="middle">
<div class="panel panel-default">
<div class="panel-heading" id="pageheader" style="text-align:center; ">Add New Unit</div>
<center><br>
<!--<a href="unit_search.php"><input type="button" value="Search Units"></a>-->
<div style="padding-left: 15px;padding-bottom: 10px;"><button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;" id="btnBack"><i class="fa  fa-arrow-left"></i></button>
<!--<button type="button" class="btn btn-primary" onclick="window.location.href='unit_search.php'">Search Units</button>-->
<?php if(isset($_REQUEST['uid']) && $_REQUEST['uid'] <> "")
{?>
<button type="button" class="btn btn-primary" onClick="viewMemberStatus('<?php echo $_REQUEST['uid'];?>');"  style="height:35px;"><i class="fa  fa-history">&nbsp;Show Transfer History</i></button>
<button type="button"  id="transferOwnership"  class="btn btn-primary"   onClick="showOrHideOwnershipFields()" style="height:35px;"><i class="fa  fa-exchange">&nbsp;Transfer Ownership</i></button>
<?php }?>
</div>
</center>

<!--<center>
<a href="member_import.php?imp" style="color:#00F; text-decoration:none;"><b><u>Import Members</u></b></a>
</center>-->

<?php if(!isset($_REQUEST['ws'])){ $val ='';?>
<!--
<br>
<center>
<a href="society_view.php?imp" style="color:#00F; text-decoration:none;"><b>Add Unit</b></a>
</center>
-->
<?php }else{ $val =''; //'onSubmit="return val();"';
?>
<br>
<center>
<a href="wing.php?imp&ssid=<?php echo $_REQUEST['ssid'];?>&s&idd=<?php echo time();?>" style="color:#00F; text-decoration:none;"><b>Back</b></a>
</center>
<?php } ?>

<center>
<form name="unitForm" id="unitForm" method="post" action="process/unit.process.php" <?php echo $val;?> onSubmit=" return validateOwnershipTransfer();">
<input type="hidden" name="form_error"  id="form_error" value="<?php echo $_REQUEST["form_error"]; ?>" />
<input type="hidden" name="ssid" value="<?php echo $_GET['ssid'];?>">
<input type="hidden" name="wwid" value="<?php echo $_GET['wwid'];?>">
	<?php
		$star = "<font color='#FF0000'>*</font>";
		if(isset($_REQUEST['msg']))
		{
			$msg = "Sorry !!! You can't delete it. ( Dependency )";
		}
		else if(isset($_REQUEST['msg1']))
		{
			$msg = "Record Deleted Successfully.";
		}
		else
		{
			//$msg = '';	
		}
	?>
       <table align='center' style=" width:100%;">
		<?php
		if(isset($msg))
		{
			if(isset($_REQUEST["ShowData"]))
			{
		?>
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_REQUEST["ShowData"]; ?></b></font></td></tr>
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
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_REQUEST["ShowData"]; ?></b></font></td></tr>
        <?php
		}
		?>
        <tr>
        <td colspan="2">
        <table width="50%" style="font-size:12px; float:left;" id="PrintableTable">
        <tr align="left">
        <td><tr><td valign="middle"><?php echo $star;?></td>
			<td>Wing</td>
            <td>&nbsp;&nbsp; : &nbsp;&nbsp;</td>
			<td>
                <select name="wing_id" id="wing_id" style="width:142px;" onChange="clear_unit(this.value);" value="<?php echo $_REQUEST['wing_id'];?>"<?php if(/*$_SESSION['role'] != ROLE_SUPER_ADMIN*/$_SESSION['profile'][PROFILE_EDIT_MEMBER] == 1 && $_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1) { }else{echo 'disabled';} ?> >
					<?php echo $combo_wing = $obj_unit->combobox("select wing_id,wing from wing where status='Y' and society_id='".$_SESSION['society_id']."'",$wing_id); ?>
				</select>
            </td></tr></td>
         <td><tr></tr></td>
        	
		</tr>
        
         <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Unit Presentation</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<select name="unit_presentation" id="unit_presentation" onclick="flatconfigdisable()" >
					<?php echo $unit_presentation = $obj_unit->combobox("select `id`,`Description` from `unit_type`",$_REQUEST['unit_presentation'],false); ?>
                </select>
            </td>
		</tr>
        
        <?php if(isset($_SESSION['role']) && ($_SESSION['role']=='Super Admin' || $_SESSION['role']=='Admin' || $_SESSION['role']=='Admin Member' || $_SESSION['role']=='Manager' || $_SESSION['role']=='Accountant')){?>
		<tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Unit No. ( Flat No )</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="unit_no" id="unit_no" value="<?php echo $_REQUEST['unit_no'];?>" <?php if(/*$_SESSION['role'] != ROLE_SUPER_ADMIN*/$_SESSION['profile'][PROFILE_EDIT_MEMBER] == 1 && $_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1) { }else{echo 'disabled';} ?>/></td>
            <!-- onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)"  -->
		</tr>
		
        <tr>
        <td></td>
        <td  style="padding:10px;padding-left:0px">Block Unit</td>
        <td></td>
        <td style="padding:10px"><input type="checkbox" name="Blocked" id="Blocked" / ></td>
        </tr>
        
        <tr id="active" align="left">
        <td valign="middle"></td>
        <td>Reason</td>
        <td>&nbsp; : &nbsp;</td>
        <td><textarea cols="50" rows="5"  id="reason" name="reason" maxlength="50"></textarea></td>
        </tr>        
        
        <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Owner Name</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="owner_name" id="owner_name" value="<?php echo $_REQUEST['owner_name'];?>"/></td>
		</tr>
        
        
        <tr align="left" class="UnitFields">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Permanant Address</td>
            <td>&nbsp; : &nbsp;</td>
			<td><textarea cols="50" rows="5" name="permanant_add" id="permanant_add" value="<?php echo $_REQUEST['permanant_add'];?>"></textarea></td>
		</tr>
        
        <tr  align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Ownership Date</td>
              <td>&nbsp; : &nbsp;</td>
			<td>
            <?php if(isset($_REQUEST['uid']) && $_REQUEST['uid'] <> "")
			{?>
            <input type="text" name="ownership_date" id="ownership_date"  readonly  style="width:100px; background-color:#CCC;" value="<?php echo $_REQUEST['ownership_date'];?>"/>
            <?php }
			else{
			 ?>
               <input type="text" name="ownership_date" id="ownership_date"  class="basics"  readonly style="width:100px;" value="<?php echo $_REQUEST['ownership_date'];?>"/>
         <?php } ?> 
               
            </td>
		</tr>
        </table>
        <table align="left">
        <tr></tr><tr></tr>
         <tr align="left" class="ownership" style=" display:none;">
        	<td valign="middle"><?php echo $star;?></td>
			<td>New Owner Name</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="new_owner_name" id="new_owner_name" value="<?php echo $_REQUEST['new_owner_name'];?>" style="background-color:#FF0;"/></td>
		</tr>
        
       <tr  align="left"   class="ownership" style=" display:none;">
        	<td valign="middle"><?php echo $star;?></td>
			<td>New Ownership Date</td>
              <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="new_ownership_date" id="new_ownership_date" class="basics" readonly  style="width:100px; background-color:#FF0;"  value="<?php echo $_REQUEST['new_ownership_date'];?>"/></td>
		</tr>
        
        
        <tr align="left"   class="ownership" style=" display:none;">
        	<td valign="middle"></td>
            <td>Email ID</td>
            <td>&nbsp; : &nbsp;</td>
            <td><input type="text" name="email" id="email" value="<?php echo $_REQUEST['email'];?>"  size="30"  style="background-color:#FF0;"/></td>
         </tr>
         
         <tr align="left"   class="ownership" style=" display:none;">
        	<td valign="middle"></td>
          	<td>Mobile No</td>
            <td>&nbsp; : &nbsp;</td>
            <td> <input type="text" name="mob" id="mob" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" maxlength="10" value="<?php echo $_REQUEST['mob'];?>"  style="background-color:#FF0;"/></td>
        </tr>   
        
        <tr align="left" class="ownership" style="display:none">
        	<td valign="middle"></td>
            <td>IID</td>
            <td>&nbsp; : &nbsp;</td>
            <td><input type="text" name="iid" id="iid" style="background-color:#FF0" value="<?php echo $last_iid; ?>"/></td>
        </tr>   
        
        <tr align="left" class="ownership" style="display:none">
        	<td valign="middle"></td>
            <td>Reason</td>
            <td>&nbsp; : &nbsp;</td>
            <td><textarea id="transfer_reason" name="transfer_reason" style="background-color:#FF0" cols="50" rows="4" maxlength="100"></textarea></td>
        </tr>
        </td></tr>
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<!--<td>Society </td>
            <td>&nbsp; : &nbsp;</td>-->
			<td>
                <?php if(isset($_GET['ws'])){?>
                <input type="hidden" name="society_id" id="society_id" value="<?php echo $_SESSION['society_id'];?>">
				<?php //echo $_SESSION['society_name'];?>
                <?php }else{ 
				if($_REQUEST['society_id']<>""){$society_id = $_REQUEST['society_id'];}else if($_REQUEST['sid']<>""){$society_id = $_REQUEST['sid'];}
				?>
                <?php if(isset($_SESSION['admin'])){?>
                <input type="hidden" name="society_id" id="society_id" value="<?php echo $_SESSION['society_id'];?>"><?php //echo $_SESSION['society_name'];?>
                <?php }else{?>
                <select name="society_id" id="society_id" style="width:180px;display:none" onChange="get_wing(this.value);">
					<?php //echo $combo_society = $obj_unit->combobox("select society_id,concat_ws(' - ',society_name,landmark) from society where status='Y'",$society_id); ?>
                    <option><?php echo $_SESSION['society_id']; ?></option>
				</select>
                <?php }?>
                <?php }?>
            </td>
		</tr>
        </table>
       <table width="50%" style="font-size:12px; float:left;" id="PrintableTable">
		
        <!-- Transfer Unit -->
        <tr align="left" class="UnitFields">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Floor No.</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="floor_no" id="floor_no" value="<?php echo $_REQUEST['floor_no'];?>"/></td>
		</tr>
        
        <input type="hidden" name="unit_type" id="unit_type" value="0">
        
        <tr align="left" class="UnitFields">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Composition</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="composition" id="composition"  value="<?php echo $_REQUEST['composition'];?>"/></td>
		</tr>
        
        <tr align="left" class="UnitFields">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Area (Sq.Ft.)</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="area" id="area" value="<?php echo $_REQUEST['area'];?>"/></td>
		</tr>
        
        <tr align="left" class="UnitFields">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Carpet</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="carpet" id="carpet" value="<?php echo $_REQUEST['carpet'];?>"/></td>
		</tr>
        
		<tr align="left" class="UnitFields">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Flat Configuration</td>
            <td>&nbsp; : &nbsp;</td>

			<td><input type="text" style="width:35%;" name="flattype" id="flattype" /></td>
			<td ><select style="width:70%; position: relative;left: -120%;" name="flat_configuration" id ="flat_configuration">
			<option value="">Please Select</option>
        <option value="RK">RK</option>
        <option value="BHK">BHK</option>
      </select>


	 
		</td>
		</tr>
		
        <tr align="left" class="UnitFields">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Commercial</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="commercial" id="commercial" value="<?php echo $_REQUEST['commercial'];?>"/></td>
		</tr>
        
        <tr align="left" class="UnitFields">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Residential</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="residential" id="residential" value="<?php echo $_REQUEST['residential'];?>"/></td>
		</tr>
        
        <tr align="left" class="UnitFields">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Terrace</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="terrace" id="terrace" value="<?php echo $_REQUEST['terrace'];?>"/></td>
		</tr>
        
        <tr align="left" class="UnitFields">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Intercom Number</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="intercom_no" id="intercom_no" value="<?php echo $_REQUEST['intercom_no'];?>"/></td>
		</tr>
		  <tr align="left" class="UnitFields">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Resident Contact No</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="resident_no" id="resident_no" value="<?php echo $_REQUEST['resident_no'];?>"/></td>
		</tr>
        <!--
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Billing Cycle</td>
            <td>&nbsp; : &nbsp;</td>
			<td><select name="Cycle" id="Cycle" value='<?php //echo $_POST['Cycle']; ?>' onChange="jsGetCycles(this.value);" >
             	 <?php //echo $combo_state=$obj_bill_period->combobox("select ID,Description from billing_cycle_master",'0');?>   
           
                  </select>
           
                  </td>
		</tr>
        -->
        </table>
 
        <table align="center" style="width:100%">
		<tr class="UnitFields"><td colspan="4"><br /><br /></td></tr>
		<tr height="50" align="center"  class="UnitFields"><td>&nbsp;</td><th colspan="3" align="center"><table align="center"><tr height="25"><th bgcolor="#CCCCCC"  style="padding-top: 6px;"width="180">Particulars For Bill </th></tr></table></th></tr>
        <tr align="left" class="UnitFields">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Previous Year</td>
            <td>&nbsp; : &nbsp;</td>
			<td><select name="Year" id="Year" value='<?php echo $_POST['Year']; ?>' onChange="jsGetperiods(this.value, 0);" <?php if(/*$_SESSION['role'] != ROLE_SUPER_ADMIN*/$_SESSION['profile'][PROFILE_EDIT_MEMBER] == 1 && $_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1) { }else{echo 'disabled';} ?>> <!--disabled="disabled"-->
             	 <?php echo $combo_state = $obj_bill_period->combobox("select YearID,YearDescription from year ORDER BY YearID DESC", 0 ); ?>   
                  </select></td>
            <td></td>
		</tr>
        
        
        <script>
			function jsGetperiods(year_id, selectedIndex)
			{
				//var cycleID=document.getElementById().value;
				//alert(cycleID);
				populateDDListAndTrigger('select#Period', 'ajax/ajaxbill_period.php?getperiod&year=' + year_id + '&cycleID=0', 'period', 'periodFetched', false, selectedIndex);
			}
			function periodFetched()
			{
				
			}
		</script>
        
        
        <tr align="left" class="UnitFields">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Previous Year Ending Period</td>
            <td>&nbsp; : &nbsp;</td>
			<td><select name="Period" id="Period" value='<?php echo $_POST['Period']; ?>' <?php if(/*$_SESSION['role'] != ROLE_SUPER_ADMIN*/$_SESSION['profile'][PROFILE_EDIT_MEMBER] == 1 && $_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1) { }else{echo 'disabled';} ?>> <!--disabled="disabled"-->
             	 <?php //echo $combo_state = $obj_bill_period->combobox("select ID,Type from period where status='Y'",'0'); ?>   
                  </select></td>
            <td></td>
		</tr>
        <?php if($IsGST == 1){?>
        <tr align="left" class="UnitFields">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>GST No Exemption</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="checkbox" value="1" id="GSTNoExp" name="GSTNoExp" <?php if(/*$_SESSION['role'] != ROLE_SUPER_ADMIN*/$_SESSION['profile'][PROFILE_EDIT_MEMBER] == 1 && $_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1) { }else{echo 'disabled';} ?>></td>
            <td></td>
		</tr>
        <?php }?>
		<tr align="left" class="UnitFields">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Bill type</td>
            <td>&nbsp; : &nbsp;</td>
			<td>Maintenance Bill</td>
			<td>Supplimentary Bill</td>
		</tr>
		
		<tr align="left" class="UnitFields">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Opening Principle Balance</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input   type="text"   <?php  echo $bIsCurrentYearAndCreationYrMatch == false ?  'readonly  style="background-color:#CCC;"' : ''; ?>  name="bill_subtotal" id="bill_subtotal"  value="<?php echo $_REQUEST['bill_subtotal'];?>" <?php if(/*$_SESSION['role'] != ROLE_SUPER_ADMIN*/$_SESSION['profile'][PROFILE_EDIT_MEMBER] == 1 && $_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1) { }else{echo 'disabled';} ?> /></td>
			<td><input   type="text"   <?php  echo $bIsCurrentYearAndCreationYrMatch == false ?  'readonly  style="background-color:#CCC;"' : ''; ?>  name="supp_bill_subtotal" id="supp_bill_subtotal"  value="<?php echo $_REQUEST['supp_bill_subtotal'];?>" <?php if(/*$_SESSION['role'] != ROLE_SUPER_ADMIN*/$_SESSION['profile'][PROFILE_EDIT_MEMBER] == 1 && $_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1) { }else{echo 'disabled';} ?> /></td>
		</tr>
        
        <tr align="left" class="UnitFields">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Opening Interest Balance</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" <?php  echo $bIsCurrentYearAndCreationYrMatch == false ?   'readonly  style="background-color:#CCC;"' : ''; ?>  name="bill_interest" id="bill_interest" value="<?php echo $_REQUEST['bill_tax'];?>" <?php if(/*$_SESSION['role'] != ROLE_SUPER_ADMIN*/$_SESSION['profile'][PROFILE_EDIT_MEMBER] == 1 && $_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1) { }else{echo 'disabled';} ?>/></td>
			<td><input type="text" <?php  echo $bIsCurrentYearAndCreationYrMatch == false ?   'readonly  style="background-color:#CCC;"' : ''; ?>  name="supp_bill_interest" id="supp_bill_interest" value="<?php echo $_REQUEST['supp_bill_tax'];?>" <?php if(/*$_SESSION['role'] != ROLE_SUPER_ADMIN*/$_SESSION['profile'][PROFILE_EDIT_MEMBER] == 1 && $_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1) { }else{echo 'disabled';} ?>/></td>
		</tr>
        
        <tr align="left" class="UnitFields">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Previous Principle Balance</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" <?php  echo $bIsCurrentYearAndCreationYrMatch == false ?  'readonly  style="background-color:#CCC;"': ''; ?>  name="principle_balance" id="principle_balance" value="<?php echo $_REQUEST['principle_balance'];?>" <?php if(/*$_SESSION['role'] != ROLE_SUPER_ADMIN*/$_SESSION['profile'][PROFILE_EDIT_MEMBER] == 1 && $_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1) { }else{echo 'disabled';} ?>/></td>
			<td><input type="text" <?php  echo $bIsCurrentYearAndCreationYrMatch == false ?  'readonly  style="background-color:#CCC;"': ''; ?>  name="supp_principle_balance" id="supp_principle_balance" value="<?php echo $_REQUEST['supp_principle_balance'];?>" <?php if(/*$_SESSION['role'] != ROLE_SUPER_ADMIN*/$_SESSION['profile'][PROFILE_EDIT_MEMBER] == 1 && $_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1) { }else{echo 'disabled';} ?>/></td>
		</tr>
        
        <tr align="left" class="UnitFields">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Previous Interest Balance</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" <?php  echo $bIsCurrentYearAndCreationYrMatch == false ?  'readonly  style="background-color:#CCC;"' : ''; ?> name="interest_balance" id="interest_balance" value="<?php echo $_REQUEST['interest_balance'];?>" <?php if(/*$_SESSION['role'] != ROLE_SUPER_ADMIN*/$_SESSION['profile'][PROFILE_EDIT_MEMBER] == 1 && $_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1) { }else{echo 'disabled';} ?>/></td>
			<td><input type="text" <?php  echo $bIsCurrentYearAndCreationYrMatch == false ?  'readonly  style="background-color:#CCC;"' : ''; ?> name="supp_interest_balance" id="supp_interest_balance" value="<?php echo $_REQUEST['supp_interest_balance'];?>" <?php if(/*$_SESSION['role'] != ROLE_SUPER_ADMIN*/$_SESSION['profile'][PROFILE_EDIT_MEMBER] == 1 && $_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1) { }else{echo 'disabled';} ?>/></td>
		</tr>
        
       <?php if(isset($_GET['ssid'])){?>
        <!--<tr align="left" height="40" valign="bottom"><td></td><td colspan="3"><font style="font-size:11px; color:#F00;"><u>Note</u> : You can add multiple unit no.(Flat No.) with comma (,) separated.<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Eg. 1001,1002,1003,1004</font></td></tr>-->
        
        <?php }} ?>
    
        <tr><td colspan="4">&nbsp;</td></tr>
		
        <tr>
			<td colspan="4" align="center">
           		<input type="hidden" name="id" id="id">
				<input type="hidden" name="uid" id="uid" value="<?php echo $_REQUEST['uid'];?>">
                 <input type="hidden" name="mode" id="mode"   value="<?php echo $_REQUEST['mode'];?>" />
                 <input type="hidden" name="member_id" id="member_id" value="<?php echo $_REQUEST['member_id'];?>" />
              
            </td> 
		</tr>
        <tr>
           <td colspan="6" align="center"><input type="submit" name="insert" id="insert" value="Insert"  class="btn btn-primary"  style="color:#FFF;  box-shadow:none;border-radius: 5px; width:100px; height:30px;background-color: #337ab7;border-color: #2e6da4; "></td></tr>
</table>
</table>
</form>
<br>

<?php if(isset($_SESSION['admin'])){?>
<center>
<a href="unit_print.php?ssid=<?php echo $_GET['ssid'];?>&wwid=<?php echo $_GET['wwid'];?>&society_id=<?php if($_GET['society_id']<>""){echo $_GET['society_id'];}else{echo $_SESSION['society_id'];}?>&wing_id=<?php echo $_GET['wing_id'];?>&unit_no=<?php echo $_GET['unit_no'];?>&insert=<?php if($_GET['insert']<>""){echo $_GET['insert'];}else{echo 'Search';}?>&ShowData=&imp=" target="_blank"><img src="images/print.png" width="40" width="40" /></a>
</center>
<?php } ?>

<table align="center">
<tr>
<?php 
/*if($show_wings <> '')
{
	foreach($show_wings as $key=>$val)
	{
	?>

<a href="unit.php?wing_id=<?php echo $show_wings[$key]['wing_id']; ?>" id="wing_link"><?php echo 'Wing '.$show_wings[$key]['wing']; ?>&nbsp;&nbsp;</a>
	<?php }
}*/?>
<!--<a href="unit.php?wing_id=" id="wing_link">All Wings</a>-->
</tr>
<tr>
<td align="center">


<?php 
/*echo "<br>";
$str1 = $obj_unit->pgnation();
echo "<br>";
echo $str = $obj_unit->display1($str1);
echo "<br>";
$str1 = $obj_unit->pgnation();
echo "<br>";*/
?>
</td>
</tr>
</table>

</center>
</div>
</div>
<?php
	if(isset($_REQUEST['uid']) && $_REQUEST['uid'] <> '')
	{
		?>
			<script>
				getunit('edit-' + <?php echo $_REQUEST['uid']; ?>);
			</script>
		<?php
	}
	
if($IsGST == 1){?>
<input type="hidden" id="GstApply" name ="GstApply" value = "1">

<?php }
else{ ?>
<input type="hidden" id="GstApply" name ="GstApply" value = "0">

<?php } ?>
<div id="openDialogYesNo" class="modalDialog">
	<div>
		<div id="message_yesno">
		</div>
	</div>
</div>
<script>
if(document.getElementById('form_error').value == '1')
{
	showOrHideOwnershipFields(true);
}
</script>
<?php include_once "includes/foot.php"; ?>
<div id="openDialogOk" class="modalDialog" >
	<div>
		<div id="message_ok">
		</div>
	</div>
</div>
<?php
if(isset($_REQUEST["mtfr"]))
{?>
	<script>
			$(document).ready(function(){ $("#transferOwnership").click();});				
	</script>
<?php  } ?>    
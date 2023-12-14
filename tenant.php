<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - View Leave & License</title>
</head>

<?php
include_once("includes/head_s.php");
include_once ("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
include_once("classes/tenant.class.php");
include_once("classes/mem_other_family.class.php");
include_once("classes/unit.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
//nclude_once("classes/mem_other_family.class.php");
//$obj_mem_other_family = new mem_other_family($m_dbConn);
$obj_tenant = new tenant($dbConn);
$obj_mem_other_family = new mem_other_family($dbConn);


$unit_details = $obj_mem_other_family->unit_details($_REQUEST['mem_id']);
$society_dets = $obj_mem_other_family->get_society_details($_SESSION['society_id']);
$UnitBlock = $_SESSION["unit_blocked"];
$obj_unit = new unit($dbConn,$dbConnRoot);
//print_r($unit_details);
$verifyStatus = $obj_unit->checkVerificationAccess();
$approveStatus = $obj_unit->checkApprovalAccess();
if(isset($_REQUEST['edit']))
{
	if($_REQUEST['edit']<>"")
	{ 
		$details = $obj_tenant->getViewDetails($_REQUEST['edit']);
		//print_r($details);
		$image=$details[0]['img'];
		$document=$details[0]['Document'];
		
		//$imageUrl = "https://drive.google.com/file/d/".$image."/view";	
		$imageUrl = "images/noimage.png";	
		//print_r($image);
		//print_r($document);
		if($_SESSION['role'] == ROLE_MEMBER && $details[0]['active']==1 )
		{
			?>
			<script>
				window.location.href = 'Dashboard.php';
			</script>
			<?php
			exit();
		}
	}
}
if(isset($_REQUEST['view']))
{
	if($_REQUEST['view']<>"")
	{ 
		$details = $obj_tenant->getViewDetails($_REQUEST['view']);
		//print_r($details);
		$image=$details[0]['img'];
		$document=$details[0]['Document'];
		//$imageUrl = "https://drive.google.com/file/d/".$image."/view";
		
		$imageUrl = "images/noimage.png";
		//print_r($image);
		//print_r($document);
		if($_SESSION['role'] == ROLE_MEMBER && $details[0]['active']==1 )
		{
			//echo "hi";
			?>
			<script>
				window.location.href = 'Dashboard.php';
			</script>
			<?php
			exit();
		}
	}
}
if($_SESSION['role'] != ROLE_SUPER_ADMIN)
{
	$width = "77%";
}
else
{
	$width = "85%";
}
//echo $width;
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/tenant.js"></script>
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
    <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
    <script language="javascript" type="application/javascript">
		function getVehicleDetailsTable()
		{
			var role = "<?php echo $_SESSION['role'];?>";
			var sContent = "";
			sContent += "<tr align='center' id='vehicle_table_tr'><td width='15%'><b>&nbsp;&nbsp;Vehicle Type</b></td>";
			if(role != "Super Admin")
			{
				sContent += "<input type='hidden' name='parkingSlot_1' id = 'parkingSlot_1' value=''>";
				sContent += "<input type='hidden' name='parkingSticker_1' id='parkingSticker_1' value=''><input type='hidden' name='parkingType_1' id='parkingType_1' value='0'>";
			}
			else
			{
				sContent += "<td width='20%'><b>Parking Slot No.</b></td><td width='20%'><b>Parking Sticker No.</b></td><td width='15%'><b>Parking Type</b></td>";
			}
	        sContent += "<td width='15%'><b>Registration No.</b></td><td width='20%'><b>Vehicle Owner Name</b></td><td width='12%'><b>Vehicle Make</b></td><td width='12%'><b>Vehicle Model</b></td><td width='15%'><b>Vehicle Colour</b></td></tr>";
			sContent += "<tr align='center'><td align='center' id='vehicleType_td_1'><select name='vehicleType_1' id='vehicleType_1' ";
			if(role != "Super Admin")
			{
				sContent += "style='width:110px;'";
			}
			else
			{
				sContent += "style='width:80px'";
			}
			sContent +="><option value = ''>Please Select</option><option value='2'>Bike</option><option value='4'>Car</option></select></td>";
			if(role != "Super Admin")
			{
				sContent += "<input type='hidden' name='parkingSlot_1' id = 'parkingSlot_1' value=''>";
			}
			else
			{
				sContent += "<td id = 'parkingSlot_td_1'><input type='text' name='parkingSlot_1' id='parkingSlot_1' style='width:80px;'/></td>";
			}
			if(role != "Super Admin")
			{
				sContent += "<input type='hidden' name='parkingSticker_1' id = 'parkingSticker_1' value=''/><input type='hidden' name='parkingType_1' id='parkingType_1' value='0'>";
			}
			else
			{
				sContent += "<td id = 'parkingSticker_td_1'><input type='text' name='parkingSticker_1' id='parkingSticker_1' style='width:80px;'/></td><td id='parkingType_td_1'><select id='parkingType_1' name='parkingType_1' style='width:80px;'></select></td>";
			}
			sContent += "<td id='carRegNo_td_1'><input type='text' name='carRegNo_1' id='carRegNo_1' style='width:100px;'/></td><td id='carOwner_td_1'><input type='text' name='carOwner_1' id='carOwner_1' style='width:130px;'/></td><td id='carMake_td_1'><input type='text' name='carMake_1' id='carMake_1' style='width:90px;'/></td><td id = 'carModel_td_1'><input type='text' name='carModel_1' id='carModel_1' style='width:90px;'/></td><td id = 'carColor_td_1'><input type='text' name='carColor_1' id='carColor_1' style='width:90px;'/></td></tr>";
			sContent +="<tr><td id='addVehicle_button' style='padding-left:1%'><input id='btnAddVehicle' type='button' value='Add' onClick='addNewVehicle()'/></td></tr>";
			document.getElementById("vehicle_table").innerHTML = sContent;
			document.getElementById("addVehicleDetails").style.display = "none";
			document.getElementById("vehiclecount").value = 1;
			//document.getElementById("addVehicle_button").style.display = "table-cell";
		}
	function go_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
		});
        setTimeout('hide_error()',8000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }
	
	function for_print()
	{
		document.getElementById('print').style.display = "none";
 		document.getElementById("profile_img").style.height="120px";
		var html = document.getElementById('tenant').innerHTML;
		var print_div = document.getElementById('for_printing');
		print_div.innerHTML = html;
				
		var mywindow = window.open('', 'PRINT', 'height=600,width=800');

	    mywindow.document.write('<html><head><title></title>');
    	mywindow.document.write('</head><body>');
		mywindow.document.write(document.getElementById('head_for_printing').innerHTML);
    	mywindow.document.write(document.getElementById('for_printing').innerHTML);
	    mywindow.document.write('</body></html>');

    	mywindow.document.close(); // necessary for IE >= 10
	    mywindow.focus(); // necessary for IE >= 10*/

    	mywindow.print();
	    mywindow.close();

		document.getElementById('print').style.display = "block";
		document.getElementById("profile_img").style.height="15%";
		return false;
	}
		 
		var isblocked = '<?php echo $UnitBlock ?>';
		if(isblocked==1)
		{
			window.location.href='suspend.php';
		}
		function getFirstMemberName()
		{
			var FName = document.getElementById("t_name").value;
			var MName = document.getElementById("t_mname").value;
			var LName = document.getElementById("t_lname").value;
			document.getElementById("members_1").value = FName+" "+MName+" "+LName;
		}
		$(function()
    	{
			$.datepicker.setDefaults($.datepicker.regional['']);
			$(".basics").datepicker
			({ 
				dateFormat: "dd-mm-yy", 
				showOn: "both", 
				buttonImage: "images/calendar.gif", 
				buttonImageOnly: true 	
			})
		});
		$(function()
		{
			$.datepicker.setDefaults($.datepicker.regional['']);
			$(".basics_Dob").datepicker(datePickerOptions)
		});
		var datePickerOptions=
		{ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0',
            buttonImageOnly: true ,
            defaultDate: '01-01-1980'
        };
		$(function () 
		{
			
			$("#btnGet").bind("click", function () 
			{
        		var values = "";
        		$("input[name=members]").each(function ()
				{
            		values += $(this).val() + "\n";
        		});
        		//alert(values);
    		});
		});
		function GetDynamicTextBox(value) 
		{
    		return '<td id="members_td_'+FieldCount+'"><input name = "members_'+FieldCount+'" id = "members_'+FieldCount+'" type="text" value = "' + value + '"   style="width:140px;" /></td>&nbsp;<td id="relation_td_'+FieldCount+'"><input name = "relation_'+FieldCount+'" id = "relation_'+FieldCount+'" type="text" value = "' + value + '"  style="width:80px;"  /></td>&nbsp;&nbsp;'+'<td id="mem_dob_td_'+FieldCount+'"><input name = "mem_dob_'+FieldCount+'" id = "mem_dob_'+FieldCount+'"  class="basics_Dob" type="text" value = "' + value + '" size="10"   style="width:80px;" /></td><td id="contact_td_'+FieldCount+'">&nbsp;&nbsp;&nbsp;&nbsp;<input name = "contact_'+FieldCount+'" id = "contact_'+FieldCount+'" type="text" value = "' + value + '"  style="width:80px;"  /></td><td id="email_td_'+FieldCount+'"><input name = "email_'+FieldCount+'" id = "email_'+FieldCount+'" type="text" value = "' + value + '"  style="width:140px;"  />&nbsp;</td><td></td><td></td>';
			//class="dropdown"
		}
		function getTenant(str)
		{ //alert("hi");	
			var iden=new Array();
			iden=str.split("-");		
	//alert(iden[1]);
			if(iden[0]=="delete")
			{
				var conf = confirm("Are you sure , you want to delete it ???");
				if(conf==1)
				{			
					remoteCall("ajax/tenant.ajax.php","&method="+iden[0]+"&TenantId="+iden[1],"loadchanges");
				}
			}
			else
			{
				remoteCall("ajax/tenant.ajax.php","&method="+iden[0]+"&TenantId="+iden[1],"loadchanges");			
			}
		}
var FieldCount=1;
var MaxInputs=10;
function addNewMember()
{
	FieldCount = FieldCount + 1; 
	var sMemberContent = '<tr><td id="members_td_'+FieldCount+'"><input name = "members_'+FieldCount+'" id = "members_'+FieldCount+'" type="text" value = ""   style="width:140px;" /></td>&nbsp;<td id="relation_td_'+FieldCount+'"><input name = "relation_'+FieldCount+'" id = "relation_'+FieldCount+'" type="text" value = ""  style="width:80px;"  /></td>&nbsp;&nbsp;'+'<td id="mem_dob_td_'+FieldCount+'"><input name = "mem_dob_'+FieldCount+'" id = "mem_dob_'+FieldCount+'"  class="basics_Dob" type="text" value = "" size="10"   style="width:80px;" /></td><td id="contact_td_'+FieldCount+'">&nbsp;&nbsp;&nbsp;&nbsp;<input name = "contact_'+FieldCount+'" id = "contact_'+FieldCount+'" type="text" value = ""  style="width:80px;"  /></td><td id="email_td_'+FieldCount+'"><input name = "email_'+FieldCount+'" id = "email_'+FieldCount+'" type="text" value = ""  style="width:140px;"  />&nbsp;</td><td></td><td></td></tr>';
	if(FieldCount <= MaxInputs) //max file box allowed
    {
		
		$("#mem_table").append(sMemberContent);
		$(".basics_Dob").datepicker(datePickerOptions);
		
		document.getElementById('count').value=FieldCount;
	}
	else
	{
		alert ("Can't add more than 10 Members.")
	}
}
var vehicleCount=1;
function addNewVehicle()
{
	var role = "<?php echo $_SESSION['role'];?>";
	vehicleCount = vehicleCount + 1;
	var sContent = "<tr align='center'><td align='center' id='vehicleType_td_"+vehicleCount+"'><select name='vehicleType_"+vehicleCount+"' id='vehicleType_"+vehicleCount+"' ";
	if(role != "Super Admin")
	{
		sContent += "style='width:110px;'";
	}
	else
	{
		sContent += "style='width:80px'";
	}
	sContent +="><option value = ''>Please Select</option><option value='2'>Bike</option><option value='4'>Car</option></select></td>";
    if(role != "Super Admin")
	{
		sContent += "<input type='hidden' name='parkingSlot_"+vehicleCount+"' id = 'parkingSlot_"+vehicleCount+"' value=''>";
	}
	else
	{
				sContent += "<td id = 'parkingSlot_td_"+vehicleCount+"'><input type='text' name='parkingSlot_"+vehicleCount+"' id='parkingSlot_"+vehicleCount+"' style='width:80px;'/></td>";
	}
	if(role != "Super Admin")
	{
		sContent += "<input type='hidden' name='parkingSticker_"+vehicleCount+"' id = 'parkingSticker_"+vehicleCount+"' value=''/><input type='hidden' name='parkingType_"+vehicleCount+"' id='parkingType_"+vehicleCount+"' value='0'>";
	}
	else
	{
				sContent += "<td id = 'parkingSticker_td_"+vehicleCount+"'><input type='text' name='parkingSticker_"+vehicleCount+"' id='parkingSticker_"+vehicleCount+"' style='width:80px;'/></td><td id='parkingType_td_"+vehicleCount+"'><select id='parkingType_"+vehicleCount+"' name='parkingType_"+vehicleCount+"' style='width:80px;'></select></td>";
	}
	sContent += "<td id='carRegNo_td_"+vehicleCount+"'><input type='text' name='carRegNo_"+vehicleCount+"' id='carRegNo_"+vehicleCount+"' style='width:100px;'/></td><td id='carOwner_td_"+vehicleCount+"'><input type='text' name='carOwner_"+vehicleCount+"' id='carOwner_"+vehicleCount+"' style='width:130px;'/></td><td id='carMake_td_"+vehicleCount+"'><input type='text' name='carMake_"+vehicleCount+"' id='carMake_"+vehicleCount+"' style='width:90px;'/></td><td id = 'carModel_td_"+vehicleCount+"'><input type='text' name='carModel_"+vehicleCount+"' id='carModel_"+vehicleCount+"' style='width:90px;'/></td><td id = 'carColor_td_"+vehicleCount+"'><input type='text' name='carColor_"+vehicleCount+"' id='carColor_"+vehicleCount+"' style='width:90px;'/></td></tr>";	
	$("#vehicle_table").append(sContent);
	document.getElementById('vehiclecount').value=vehicleCount;
			document.getElementById("parkingType_"+vehicleCount).innerHTML = "<?php echo $obj_tenant->combobox07("Select `Id`,`ParkingType` from `parking_type` where Status = 'Y' AND IsVisible = '1'", "0");?>";
}

function getTotalMonth()
{
	var startDate = document.getElementById('start_date').value;
	var endDate = document.getElementById('end_date').value;	
	
	if(startDate != '' && endDate != '')
	{
		var firstDate = startDate.split('-');
		var secondDate = endDate.split('-');
		
		var year1 = parseInt(firstDate[2]);
		var year2 =parseInt(secondDate[2]);
		
		var month1 = parseInt(firstDate[1]);
		var month2 = parseInt(secondDate[1]);
		
		var monthDiff = (year2 - year1) * 12 + (month2 - month1)+1;
	
		document.getElementById('Lease_Period').value = monthDiff;		
	}
}

function updateEndDate()
{
	var startDate = document.getElementById('start_date').value;
	var totalMonth = parseInt(document.getElementById('Lease_Period').value);
	var firstDate = startDate.split('-');
	var year1 = parseInt(firstDate[2]);
	var month1 = parseInt(firstDate[1]) - 1;
	var date1 = parseInt(firstDate[0]);		
	
	var dt = new Date(year1,month1,date1);
	
	var endDate = dt.setMonth(dt.getMonth()+totalMonth); // It's return date in milisecond
	
	var endDate = new Date(endDate);
	
	var year2 = endDate.getFullYear();
	var month2 = endDate.getMonth()+1; // in js jan month start from 1 due to that we addded 1
	var date2 = endDate.getDate();
	
	if (date2 < 10) { 
		date2 = '0' + date2; 
	} 
	if (month2 < 10) { 
		month2 = '0' + month2; 
	}
	
	var updatedEndDate = date2+'-'+month2+'-'+year2;	
	document.getElementById('end_date').value = updatedEndDate;
}

function loadchanges()
{	
	var a		= sResponse.trim();			
	var arr1	= new Array();
	var arr2	= new Array();
	arr1		= a.split("@@@");
	arr2		= arr1[1].split("#");		
	
	var tenant_details = new Array();
	tenant_details = JSON.parse(arr1[1]);
	console.log(tenant_details[0]['carDetails']);	
	console.log(tenant_details[0]['bikeDetails']);
	//alert('test');		
	var role = "<?php echo $_SESSION['role'];?>";
	if(arr1[0] == "edit")
	{		
	//alert(tenant_details[0]['tenant_id']);
		//document.getElementById("tenant_id").value= tenant_details[0]['tenant_id'];
		document.getElementById("t_name").value= tenant_details[0]['tenant_name'];
		document.getElementById("t_mname").value= tenant_details[0]['tenant_MName'];
		document.getElementById("t_lname").value= tenant_details[0]['tenant_LName'];
		
		document.getElementById('start_date').value=tenant_details[0]['start_date'];
		//alert(bTerminate);
		
		if(bTerminate == false)
		{
			document.getElementById('end_date').value=tenant_details[0]['end_date'];
		}
		else
		{
			//document.getElementById('end_date').value=todayDate;
			$("#end_date").datepicker().datepicker("setDate", new Date());
		}
		document.getElementById('Lease_Period').value = tenant_details[0]['total_month'];
		//document.getElementById("dob").value=arr2[1];
		document.getElementById('agent').value=tenant_details[0]['agent_name'];
		document.getElementById('agent_no').value=tenant_details[0]['agent_no'];
		
		//document.getElementById('p_varification').checked = (arr2[7]==1) ? true : false;
		CKEDITOR.instances['note'].setData(tenant_details[0]['note']);
		
		var memberAry = new Array();
		memberAry = tenant_details[0]['members'];
		//alert( memberAry.length);
		for(var iCnt = 1; iCnt <= memberAry.length-1; iCnt++)
		{			
				//alert("add");
				//$("#btnAdd").trigger('click');
				addNewMember();
		}
		document.getElementById('members_1').value = tenant_details[0]['tenant_name']+" "+tenant_details[0]['tenant_MName']+" "+tenant_details[0]['tenant_LName'];
		document.getElementById('mem_dob_1').value = tenant_details[0]['dob'];
		document.getElementById('contact_1').value = tenant_details[0]['mobile_no'];
		document.getElementById('email_1').value = tenant_details[0]['email'];
		document.getElementById('relation_1').value = "self";
		for(var iCnt = 0; iCnt < memberAry.length; iCnt++)
		{
			document.getElementById('members_'+(iCnt+1)).value = memberAry[iCnt]['mem_name'];
			document.getElementById('relation_'+(iCnt+1)).value = memberAry[iCnt]['relation'];
			document.getElementById('mem_dob_'+(iCnt+1)).value = memberAry[iCnt]['mem_dob'];
			document.getElementById('contact_'+(iCnt+1)).value = memberAry[iCnt]['contact_no'];
			document.getElementById('email_'+(iCnt+1)).value = memberAry[iCnt]['email'];
		}
		/*if(memberAry[0]['send_act_email']  == "1")
		{
			document.getElementById('chkCreateLogin').checked = true;
		}
		if(memberAry[0]['send_commu_emails']  == "1")
		{
			document.getElementById('other_send_commu_emails').checked = true;
		}*/
		//---------------------------Vehicle Details-----------------------------------------------
		//alert (tenant_details[0]['vehicleCount']);
		//alert (tenant_details[0]['bikeDetails']);
		if(tenant_details[0]['vehicleCount'] != "0")
		{
			var carAry = new Array();
			carAry = tenant_details[0]['carDetails'];
			var vCnt = 0;
		
			for(vCnt = 1; vCnt <= tenant_details[0]['vehicleCount']-1; vCnt++)
			{			
				//alert("add");
				addNewVehicle();
			}
			if(carAry)
			{
				for(vCnt =  0; vCnt < carAry.length; vCnt++)
				{
					document.getElementById('vehicleType_'+(vCnt+1)).value = "4";
					if(role == "Super Admin")
					{
									document.getElementById('parkingSlot_'+(vCnt+1)).value = carAry[vCnt]['parking_slot'];
									document.getElementById('parkingSticker_'+(vCnt+1)).value = carAry[vCnt]['parking_sticker'];
						if(carAry[vCnt]['ParkingType'] == "0")
						{	
										document.getElementById('parkingType_'+(vCnt+1)).value = "1";
						}
						else
						{
										document.getElementById('parkingType_'+(vCnt+1)).value = carAry[vCnt]['ParkingType'];
									}
								}
							//alert ('carRegNo_'+(vCnt+1));
								document.getElementById('carRegNo_'+(vCnt+1)).value = carAry[vCnt]['car_reg_no'];
								document.getElementById('carOwner_'+(vCnt+1)).value = carAry[vCnt]['car_owner'];	
								document.getElementById('carMake_'+(vCnt+1)).value = carAry[vCnt]['car_make'];	
								document.getElementById('carModel_'+(vCnt+1)).value = carAry[vCnt]['car_model'];
								document.getElementById('carColor_'+(vCnt+1)).value = carAry[vCnt]['car_color'];	
						}
						vCnt = vCnt + 1;
					}
					else
					{
						vCnt = 1;
					}
			var bikeAry = new Array();
			bikeAry = tenant_details[0]['bikeDetails'];
				
				if(bikeAry != null)
				{
					for(var iCnt = 0; iCnt < bikeAry.length; iCnt++)
					{
					//alert(vCnt);
						document.getElementById('vehicleType_'+(vCnt)).value = "2";
						if(role == "Super Admin")
						{
							document.getElementById('parkingSlot_'+(iCnt+1)).value = bikeAry[iCnt]['parking_slot'];
							document.getElementById('parkingSticker_'+(iCnt+1)).value = bikeAry[iCnt]['parking_sticker'];
							if(bikeAry[iCnt]['ParkingType'] == "0")
							{	
								document.getElementById('parkingType_'+(iCnt+1)).value = "Not Specified";
							}
							else
							{
								document.getElementById('parkingType_'+(iCnt+1)).value = bikeAry[iCnt]['ParkingType'];
							}
						}
						document.getElementById('carRegNo_'+(vCnt)).value = bikeAry[iCnt]['bike_reg_no'];
						document.getElementById('carOwner_'+(vCnt)).value = bikeAry[iCnt]['bike_owner'];
						document.getElementById('carMake_'+(vCnt)).value = bikeAry[iCnt]['bike_make'];		
						document.getElementById('carModel_'+(vCnt)).value = bikeAry[iCnt]['bike_model'];
						document.getElementById('carColor_'+(vCnt)).value = bikeAry[iCnt]['bike_color'];	
						vCnt = vCnt + 1;
					}
						
				}
						document.getElementById("addVehicleDetails").style.display = "none";	
		}
		else
		{
		//	document.getElementById("vehicle_main_tr").style.display = "none";
			document.getElementById("vehicle_table").innerHTML = "No Records found";	
					document.getElementById("addVehicleDetails").style.display = "table-cell";	
		}
		//-------------------------------------------------Document details-----------------------------
		var DocumentAry = new Array();
		DocumentAry = tenant_details[0]['documents'];
		
		var docTable = "<table>";
		for(var iCnt = 0; iCnt < DocumentAry.length; iCnt++)
		{
			docTable += '<tr>';
			docTable += '<td><a href='+DocumentAry[iCnt]['documentLink']+' target=_blank>' + DocumentAry[iCnt]['Name'] + '</a></td>';
			
			if(tenant_details[0]['active'] == 0)
			{
				docTable += '<td><a style="color:red" href="#" onclick="del_Doc('+DocumentAry[iCnt]["doc_id"] + ',' +  tenant_details[0]["tenant_id"] + ')" ><img src="images/del.gif" border="0" alt="del" style="cursor:pointer;"></a></td>';	
			}
			docTable += '</tr>';
		}
		docTable += '<table>';
		document.getElementById('doc').innerHTML = docTable;
		//document.getElementById('doc_Id').style.display = "none";
				document.getElementById("profile_img").src = tenant_details[0]["img"];
				//profileHref
				document.getElementById("profileHref").href = tenant_details[0]["img"];
		document.getElementById("insert").value = "Update";																		
	}
	else if(arr1[0] == "view")
	{		
		//alert(tenant_details[0]['tenant_id']);
		//document.getElementById("tenant_id").value= tenant_details[0]['tenant_id'];
		//document.getElementById("t_name").value= tenant_details[0]['tenant_name'];
		//document.getElementById("t_name").readOnly = true;
		
		document.getElementById('data_table').style.border = "1px solid #cccccc";
		
		var t_name = tenant_details[0]['tenant_name'];
		document.getElementById('td_1').innerHTML = t_name;
		document.getElementById('td_n2').innerHTML = tenant_details[0]['tenant_MName'];
		document.getElementById('td_n3').innerHTML = tenant_details[0]['tenant_LName'];
		
		var start_date = tenant_details[0]['start_date'];
		document.getElementById('td_2').innerHTML = start_date;
		//document.getElementById('start_date').value=tenant_details[0]['start_date'];
		//document.getElementById('start_date').readOnly = true;
		//alert(bTerminate);
		
		if(bTerminate == false)
		{
			//document.getElementById('end_date').value=tenant_details[0]['end_date'];
			//document.getElementById('end_date').readOnly = true;
			var end_date = tenant_details[0]['end_date'];
			document.getElementById('td_3').innerHTML = end_date;
		}
		
		
		document.getElementById('td_10').innerHTML = tenant_details[0]['total_month'];
		
		var agent_name = tenant_details[0]['agent_name'];
		document.getElementById('td_4').innerHTML = agent_name;
		var agent_no = tenant_details[0]['agent_no'];
		document.getElementById('td_5').innerHTML = agent_no;
		
		var active = tenant_details[0]['active'];
		var p_varification = tenant_details[0]['p_varification'];
		var leaveAndLicenseAgreement = tenant_details[0]['leaveAndLicenseAgreement'];
		
		document.getElementById('verified').innerHTML = (active == 1) ? 'Yes' : 'No';
		document.getElementById('pVerified').innerHTML = (p_varification == 1) ? 'Yes' : 'No';
		document.getElementById('leaveAndLicenseAgreement').innerHTML = (leaveAndLicenseAgreement == 1) ? 'Yes' : 'No';
		
		
		document.getElementById('doc_Id').style.display = "none";
		document.getElementById('add_button').style.display = "none";
		
		//document.getElementById('p_varification').checked = (arr2[7]==1) ? true : false;
		//CKEDITOR.instances['note'].setData(tenant_details[0]['note']);
		//CKEDITOR.instances.editor1.readOnly( true );
		//CKEDITOR.instances.note.readOnly( true );
		document.getElementById('textarea').style.display = "none";
		document.getElementById('to_show_note').innerHTML = tenant_details[0]['note'];

		var memberAry = new Array();
		memberAry = tenant_details[0]['members'];
		//alert( memberAry.length);
		for(var iCnt = 1; iCnt <= memberAry.length-1; iCnt++)
		{			
			addNewMember();
		}
		
		document.getElementById('members_td_1').innerHTML = tenant_details[0]['tenant_name']+" "+tenant_details[0]['tenant_MName']+" "+tenant_details[0]['tenant_LName'];
		document.getElementById('mem_dob_td_1').innerHTML = tenant_details[0]['dob'];
		document.getElementById('contact_td_1').innerHTML = tenant_details[0]['mobile_no'];
		document.getElementById('email_td_1').innerHTML = tenant_details[0]['email'];
		document.getElementById('relation_td_1').innerHTML = "Self";
		for(var iCnt = 1; iCnt < memberAry.length; iCnt++)
		{
			document.getElementById('members_td_'+(iCnt+1)).innerHTML = memberAry[iCnt]['mem_name'];
			//document.getElementById('members_td_'+(iCnt+1)).style.border = '1px solid #cccccc';
			//document.getElementById('members_td_'+(iCnt+1)).style.borderColor = 'black';
			//document.getElementById('members_'+(iCnt+1)).readOnly = true;
			document.getElementById('relation_td_'+(iCnt+1)).innerHTML = memberAry[iCnt]['relation'];
			//document.getElementById('relation_td_'+(iCnt+1)).style.border = '1px solid #cccccc';
			//document.getElementById('relation_td_'+(iCnt+1)).style.borderColor = 'black';
			//document.getElementById('relation_'+(iCnt+1)).readOnly = true;
			document.getElementById('mem_dob_td_'+(iCnt+1)).innerHTML = memberAry[iCnt]['mem_dob'];
			//document.getElementById('mem_dob_td_'+(iCnt+1)).style.border = '1px solid #cccccc';
			//document.getElementById('mem_dob_td_'+(iCnt+1)).style.borderColor = 'black';
			//document.getElementById('mem_dob_'+(iCnt+1)).readOnly = true;
			document.getElementById('contact_td_'+(iCnt+1)).innerHTML = memberAry[iCnt]['contact_no'];
			//document.getElementById('contact_td_'+(iCnt+1)).style.border = '1px solid #cccccc';
			//document.getElementById('contact_td_'+(iCnt+1)).style.borderColor = 'black';
			//document.getElementById('contact_'+(iCnt+1)).readOnly = true;
			document.getElementById('email_td_'+(iCnt+1)).innerHTML = memberAry[iCnt]['email'];
			//document.getElementById('email_td_'+(iCnt+1)).style.border = '1px solid #cccccc';
			//document.getElementById('email_td_'+(iCnt+1)).style.borderColor = 'black';
			//document.getElementById('email_'+(iCnt+1)).readOnly = true;			
		}
		document.getElementById('mem_table').style.border = '1px solid #cccccc';
		//document.getElementById('mem_table').style.borderColor = 'black';
		
		document.getElementById('mem_table_tr').style.border = '1px solid #cccccc';
		//document.getElementById('mem_table_tr').style.borderColor = 'black';
		
		document.getElementById('chkCreateLogin').style.display = "none";
		document.getElementById('other_send_commu_emails').style.display = "none";
		
		document.getElementById('create_login').style.display = "none";
		document.getElementById('send_emails').style.display = "none";
		/*if(memberAry[0]['send_act_email']  == "1")
		{
			document.getElementById('chkCreateLogin').checked = true;
		}
		if(memberAry[0]['send_commu_emails']  == "1")
		{
			document.getElementById('other_send_commu_emails').checked = true;
		}*/
		//-------------------------------------------Vehicle Details---------------------------------------------
		//console.log(tenant_details[0]['carDetails']);
		if(tenant_details[0]['vehicleCount'] != 0)
		{
			var carAry = new Array();
			carAry = tenant_details[0]['carDetails'];
			var vCnt = 0;
			for(vCnt = 1; vCnt <= tenant_details[0]['vehicleCount']-1; vCnt++)
			{			
				//alert("add");
				addNewVehicle();
			}
			if(carAry)
			{
				for(vCnt =  0; vCnt < carAry.length; vCnt++)
				{
					document.getElementById('vehicleType_td_'+(vCnt+1)).innerHTML = "Car";
					if(role == "Super Admin")
					{
						document.getElementById('parkingSlot_td_'+(vCnt+1)).innerHTML = carAry[vCnt]['parking_slot'];
						document.getElementById('parkingSticker_td_'+(vCnt+1)).innerHTML = carAry[vCnt]['parking_sticker'];
						if(carAry[vCnt]['ParkingType'] == "0")
						{
							document.getElementById('parkingType_td_'+(vCnt+1)).innerHTML = "Not Specified";
						}
						else
						{
							document.getElementById('parkingType_td_'+(vCnt+1)).innerHTML = carAry[vCnt]['ParkingType'];
						}
					}
					document.getElementById('carRegNo_td_'+(vCnt+1)).innerHTML = carAry[vCnt]['car_reg_no'];
					document.getElementById('carOwner_td_'+(vCnt+1)).innerHTML = carAry[vCnt]['car_owner'];	
					document.getElementById('carMake_td_'+(vCnt+1)).innerHTML = carAry[vCnt]['car_make'];	
					document.getElementById('carModel_td_'+(vCnt+1)).innerHTML = carAry[vCnt]['car_model'];
					document.getElementById('carColor_td_'+(vCnt+1)).innerHTML = carAry[vCnt]['car_color'];	
				}
					//alert(tenant_details[0]['carDetails'].length);
					if(tenant_details[0]['carDetails'].length == 0)
					{
						vCnt = 1;
						//alert(tenant_details[0]['carDetails'].length);
					}
					else
					{
						vCnt = vCnt + 1;
				}
			}
			var bikeAry = new Array();
			bikeAry = tenant_details[0]['bikeDetails'];
			//console.log(tenant_details[0]['bikeDetails']);
			//console.log(carAry.length);
			/*var totalCnt = carAry.length+bikeAry.length;
			for(vCnt = carAry.length; vCnt < totalCnt; vCnt++)
			{			
				//alert("add");
				addNewVehicle();
			}*/
					if(tenant_details[0]['bikeDetails'] != null)
					{
						for(var iCnt = 0; iCnt < bikeAry.length; iCnt++)
						{
							//alert(vCnt);
							document.getElementById('vehicleType_td_'+(vCnt)).innerHTML = "Bike";
							if(role == "Super Admin")
							{
								document.getElementById('parkingSlot_td_'+(vCnt)).innerHTML = bikeAry[iCnt]['parking_slot'];
								document.getElementById('parkingSticker_td_'+(vCnt)).innerHTML = bikeAry[iCnt]['parking_sticker'];
								if(bikeAry[iCnt]['ParkingType'] == "0")
								{
									document.getElementById('parkingType_td_'+(vCnt)).innerHTML = "Not Specified";
								}
								else
								{
									document.getElementById('parkingType_td_'+(vCnt)).innerHTML = bikeAry[iCnt]['ParkingType'];
								}
							}
							document.getElementById('carRegNo_td_'+(vCnt)).innerHTML = bikeAry[iCnt]['bike_reg_no'];
							document.getElementById('carOwner_td_'+(vCnt)).innerHTML = bikeAry[iCnt]['bike_owner'];
							document.getElementById('carMake_td_'+(vCnt)).innerHTML = bikeAry[iCnt]['bike_make'];		
							document.getElementById('carModel_td_'+(vCnt)).innerHTML = bikeAry[iCnt]['bike_model'];
							document.getElementById('carColor_td_'+(vCnt)).innerHTML = bikeAry[iCnt]['bike_color'];	
							vCnt = vCnt + 1;
						}
					}
					document.getElementById('addVehicle_button').style.display = "none";
				}
				else
				{
					//document.getElementById("vehicle_main_tr").style.display = "";
					document.getElementById("vehicle_table").innerHTML = "No Records found.";
					document.getElementById("addVehicleDetails").style.display = "table-cell";
					
				}
				document.getElementById("addVehicleDetails").style.display = "none";
				document.getElementById('vehicle_table').style.border = '1px solid #cccccc';
				document.getElementById("profilePhotoSpan").style.display = "none";
				document.getElementById("profilePhoto").style.display = "none";
				document.getElementById("profile_img").src = tenant_details[0]["img"];
				document.getElementById("profileHref").href = tenant_details[0]["img"];
				
				//alert(DocumentAry.length);
				//console.log(DocumentAry);
				var DocumentAry = new Array();
				DocumentAry = tenant_details[0]['documents'];
				var docTable = "<table>";

				if(DocumentAry.length > 0)
				{
					for(var iCnt = 0; iCnt < DocumentAry.length; iCnt++)
					{
						docTable += "<tr>";
						docTable += "<td><a href='"+DocumentAry[iCnt]['documentLink']+"' target=_blank>"+(iCnt+1)+". "+ DocumentAry[iCnt]['Name'] + "</a></td><td><a href='#' onclick='del_Doc("+DocumentAry[iCnt]["doc_id"] + "," +  tenant_details[0]["tenant_id"] + ") '></a></td>";
						docTable += "</tr>";
					}
				}
				else
				{
					docTable += "<tr><td colspan='2'>No Documents attached.</td></tr>";
				}
				docTable += "<table>";
				//console.log(docTable);
				document.getElementById('doc').innerHTML = docTable;
				//document.getElementById("insert").value = "Update";
				document.getElementById('insert').style.display = "none";	
				document.getElementById('print').style.display = "block";
			}
			else if(arr1[0] == "delete")
			{			
				window.location.href ="show_tenant.php";
			}
			
		}
		
		
		
		var DocCount=2;
		<?php if(isset($_REQUEST['edit']))
		{ ?>
				var DocCount=1;
		<?php }?>
		var MaxInputs=10;
		$(function ()
		{
			$("#btnAddDoc").bind("click", function ()
			{
		//	alert("Add");
				if(FieldCount <= MaxInputs) //max file box allowed
                {
					DocCount++; 
					document.getElementById('doc_count').value=DocCount;
				}
	    		var div = $("<tr />");
        		div.html(GetDynamicFileBox(""));
        		$("#doc_Id").append(div);
			});
			$("#btnGet").bind("click", function ()
			{
        		var values = "";
        		$("input[name=upload]").each(function () {
            	values += $(this).val() + "\n";
        	});
        	//alert(values);
    	});
    	$("body").on("click", ".remove", function ()
		{
        	$(this).closest("div").remove();
    	});
		});
		function GetDynamicFileBox(value)
		{
			return '<td><input name = "doc_name_'+DocCount+'" id = "doc_name_'+DocCount+'" type="text" value = "' + value + '" /></td>'+'<td><input name = "userfile'+DocCount+'" id = "userfile'+DocCount+'" type="file" value = "' + value + '" /></td>'+
				'<!--<input type="button" value="Remove" class="remove" />-->'
		}
	</script>
    <style>
		img:hover 
		{
  			box-shadow: 0 0 2px 1px rgba(0, 140, 186, 0.5);
		}
	</style>
</head>
<body>


<div id="middle">
	<div class="panel panel-info" id="panel" style="display:block; margin-top:6%;width:<?php echo $width;?>;">
    	<?php 
		$tenantAction = "";
		$actionPage2 = "memberProfile";
		if(isset($_REQUEST['edit']))
	  	{
		?>
   			<div class="panel-heading" id="pageheader">Edit Leave & License</div>
      	<?php 
			if(isset($_REQUEST['ter']))
			{
				$tenantAction = "ter";
				if(isset($_REQUEST['sr']))
				{
					$actionPage2 = "serviceRequest";
				}
			}
			else
			{
				$tenantAction = "edit";
			}
		}
		else if(isset($_REQUEST['view']))
	  	{
		?>
      		<div class="panel-heading" id="pageheader">View Leave & License</div>
        <?php
			$tenantAction = "view";
	  	}
	  	else if(isset($_REQUEST['ter']))
	  	{
		?>
			<div class="panel-heading" id="pageheader">Terminate Lease</div>
		<?php  
			
		}
	  	else
		{
		?>
        	<div class="panel-heading" id="pageheader">Add Leave & License</div>
        <?php
			$tenantAction = "add";
			if(isset($_REQUEST['sr']))
			{
				$tenantResult = $obj_tenant->checkTenantStatus($_SESSION['serviceRequestDetails']['unit_no']);
				//var_dump($tenantResult);
				if($tenantResult['tenantStatus'] == 1)
				{
					//echo "in if";
					echo "<div style='text-align:center'><br/><font color='red'><span style='text-align:center'>Active tenant Exits to the selected Unit No. <a href='tenant.php?mem_id=".$tenantResult['memberId']."&tik_id=".time()."&edit=".$tenantResult['tenantId']."&ter&sr' target = '_self'>Click here</a> to terminate exisiting lease.</span></font><br></div>";
				?>
                <script>
					$( document ).ready(function()
					{
						document.getElementById("insert").style.display = "none";
					});
				</script>
				<?php
                }
				else
				{
					
				}
				$actionPage2 = "serviceRequest";
			}
        }
	?>
	<br>
		<button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;margin-left:10%" id="btnBack"><i class="fa  fa-arrow-left"></i></button>
		<center>
		<?php 
		if(isset($_SESSION['role']) && $_SESSION['role']==ROLE_MEMBER)
		{
		?>
			<input type="button" class="btn btn-primary" onClick="window.location.href='view_member_profile.php?prf&id=<?php echo $_GET['mem_id'];?>'"  style="float:right;margin-right:40%" value="Go to profile view">
		<?php
        }
		else
		{
		?>
			<input type="button" class="btn btn-primary" onClick="window.location.href='view_member_profile.php?scm&id=<?php echo $_GET['mem_id'];?>&tik_id=<?php echo time();?>&m'" style="float:right;margin-right:40%" value="Go to profile view">
<?php 
		} 
		?>
		</center>
		<br>
        <center>
		<?php if(isset($_REQUEST['ter']))
		{
		?>
			<p style="font-size:12px; color:red; font-weight:bold;padding-top:5%">Please update Lease end date to new date on which you want to terminate the lease.</p>
		<?php 
		}?>
		</center>
		<?php 
		if(isset($_POST['ShowData']) || isset($_REQUEST['msg']))
		{ 
		?>
			<body onLoad="go_error();">
		<?php 
		}
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
		}
		?>
		<form name="tenant" id="tenant" method="post" action="process/tenant.process.php" enctype="multipart/form-data"  onSubmit="return val();">
		<center>
		<br/>
		<br/>
		<table align='center' id="data_table" width="90%" style="border:0.5;">
		<input type = "hidden" name="tenantAction" id = "tenantAction" value = "<?php echo $tenantAction;?>"/>
        <input type = "hidden" name="actionPage2" id = "actionPage2" value = "<?php echo $actionPage2;?>"/>
		<?php
		
		if(isset($msg))
		{
			if(isset($_POST['ShowData']))
			{
			?>
				<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><span id="tenantError" style="display:none"></span><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?>			</b></font></td></tr>
			<?php
			}
			else
			{
			?>
				<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $msg; ?></b></font></td></tr>
			<?php
			}	
		}
		else
		{
		?>
			<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
		<?php
		}
		?>
        	<tr>
            	<td style="text-align:center;width: 200px;"><a target="_blank" href="images/noimage.png" id="profileHref"><img <?php 
				if(isset($_REQUEST['edit']) || isset($_REQUEST['view']))
				{ 
					if($image != "") 
					{?> 
                    	src = "<?php echo $imageUrl;?>" 
					<?php 
					}
					else
					{
					?> 
                    	src = "images/noimage.png" 
					<?php 
					}
				}
				else
				{
				?>
                	src = "images/noimage.png"
                <?php
				}
					?> id="profile_img" class="img-square" alt="img" style=" width:50%;height:15%;border: 1px solid #ddd;border-radius: 4px;padding: 5px;"></a>
                <br/>
                <br/><span style="text-align:left" id="profilePhotoSpan"><b>&nbsp;Upload Profile:</b></span><input type="file" accept="image/*" id="profilePhoto" name="profilePhoto" multiple/>
                </td>
                <td colspan="2" width="70%">
                	<table width="100%">
                	<tr>
        				<td style="text-align:right"></td>
						<td style="text-align:right"><?php echo $star;?>&nbsp;<b>First Name of Lessee</b></td>
            			<td style="text-align:left">&nbsp; : &nbsp;</td>
						<td style="text-align:left" id="td_1"><input type="text" name="t_name" id="t_name"  /></td>
					</tr>
        			<tr>
        				<td style="text-align:right"></td>
						<td style="text-align:right"><b>Middle Name of Lessee</b></td>
            			<td style="text-align:left">&nbsp; : &nbsp;</td>
						<td style="text-align:left" id="td_n2"><input type="text" name="t_mname" id="t_mname"/></td>
					</tr>
        			<tr >
        				<td style="text-align:right"></td>
						<td style="text-align:right"><?php echo $star;?>&nbsp;<b>Last Name of Lessee</b></td>
            			<td style="text-align:left">&nbsp; : &nbsp;</td>
						<td style="text-align:left" id="td_n3"><input type="text" name="t_lname" id="t_lname" onBlur="getFirstMemberName()"/></td>
					</tr>
      	  			<tr>
        				<td style="text-align:right"></td>
						<td style="text-align:right"><?php echo $star;?>&nbsp;<b>Lease Start Date</b></td>
            			<td style="text-align:left">&nbsp; : &nbsp;</td>
						<td style="text-align:left" id="td_2"><input type="text" name="start_date" id="start_date" class="basics" onChange="getTotalMonth();" size="10" readonly  style="width:80px;" /></td>
					</tr>
					<tr>
        				<td style="text-align:right"></td>
        				<td style="text-align:right"><?php echo $star;?>&nbsp;<b>Lease End Date</b></td>
            			<td style="text-align:left">&nbsp; : &nbsp;</td>
						<td style="text-align:left" id="td_3"><input type="text" name="end_date" id="end_date" class="basics" onChange="getTotalMonth();" size="10" readonly  style="width:80px;" /></td>
					</tr>
                    <tr>
        				<td style="text-align:right"></td>
        				<td style="text-align:right"><?php //echo $star;?>&nbsp;<b>Number of Month</b></td>
            			<td style="text-align:left">&nbsp; : &nbsp;</td>
						<td style="text-align:left" id="td_10"><input type="text" name="Lease_Period" id="Lease_Period" onChange="updateEndDate();" size="10"   style="width:80px;" /></td>
					</tr>
        			<tr  align="right">
        				<td style="text-align:right"><?php //echo $star;?></td>
						<td style="text-align:right"><b>Agent Name</b></td>
             			<td style="text-align:left">&nbsp; : &nbsp;</td>
						<td style="text-align:left" id="td_4"><input type="text" name="agent" id="agent" /></td>
					</tr>
					<tr  align="right">
        				<td style="text-align:right"><?php //echo $star;?></td>
						<td style="text-align:right"><b>Agent  Contact No (If applicable)</b></td>
             			<td style="text-align:left">&nbsp; : &nbsp;</td>
						<td style="text-align:left" id="td_5"><input type="text" name="agent_no" id="agent_no"  onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" size="30"  /></td>
					</tr>
        			<?php
	    			if($verifyStatus && $_REQUEST['action'] == "verify" || (!isset($_REQUEST['view']) && !isset($_REQUEST['edit']) && !isset($_REQUEST['approve']) && $_SESSION['role'] <> ROLE_MEMBER && $_SESSION['role'] <> ROLE_ADMIN_MEMBER))
	   				{
					?>
        			<tr  align="left" id = "verifyTr">
                    	<td style="text-align:right"><?php //echo $star;?></td>
						<td style="text-align:right"><b>Verified by society</b></td>
             			<td style="text-align:left">&nbsp; : &nbsp;</td>
						<td style="text-align:left" id="td_6"><input type="checkbox" name="verified" id="verified" value="1" ></td>
					</tr>
                    <tr  align="left" id = "pVerifyTr">
                    	<td style="text-align:right"><?php //echo $star;?></td>
						<td style="text-align:right"><b>Police Verification Submitted</b></td>
             			<td style="text-align:left">&nbsp; : &nbsp;</td>
						<td style="text-align:left" id="td_8"><input type="checkbox" name="pVerified" id="pVerified" value="1" ></td>
					</tr>
                     <tr  align="left" id = "leaveAndLicenseAgreementTr">
                    	<td style="text-align:right"><?php //echo $star;?></td>
						<td style="text-align:right"><b>Leave and License Agreement Submitted</b></td>
             			<td style="text-align:left">&nbsp; : &nbsp;</td>
						<td style="text-align:left" id="td_8"><input type="checkbox" name="leaveAndLicenseAgreement" id="leaveAndLicenseAgreement" value="1"></td>
					</tr>
					<?php 
					}
					
					if(isset($_REQUEST['view']) && $_REQUEST['view'] <> '')
					{ ?>
					
                    	<tr  align="left" id = "verifyTr">
                    	<td style="text-align:right"><?php //echo $star;?></td>
						<td style="text-align:right"><b>Verified by society</b></td>
             			<td style="text-align:left">&nbsp; : &nbsp;</td>
						<td style="text-align:left" id="td_6"><label name="verified" id="verified"></label></td>
					</tr>
                    <tr  align="left" id = "pVerifyTr">
                    	<td style="text-align:right"><?php //echo $star;?></td>
						<td style="text-align:right"><b>Police Verification Submitted</b></td>
             			<td style="text-align:left">&nbsp; : &nbsp;</td>
						<td style="text-align:left" id="td_8"><label name="pVerified" id="pVerified"></label></td>
					</tr>
                     <tr  align="left" id = "leaveAndLicenseAgreementTr">
                    	<td style="text-align:right"><?php //echo $star;?></td>
						<td style="text-align:right"><b>Leave and License Agreement Submitted</b></td>
             			<td style="text-align:left">&nbsp; : &nbsp;</td>
						<td style="text-align:left" id="td_8"><label name="leaveAndLicenseAgreement" id="leaveAndLicenseAgreement"></label></td>
					</tr>
                    <?php }
					
					if($approveStatus && $_REQUEST['action'] == "approve")
	   				{
					?>
        			<tr  align="left" id = "approveTr">
                    	<td style="text-align:right"><?php //echo $star;?></td>
						<td style="text-align:right"><b>Approved by society</b></td>
             			<td style="text-align:left">&nbsp; : &nbsp;</td>
						<td style="text-align:left" id="td_7"><input type="checkbox" name="approved" id="approved" value="1" ></td>
					</tr>
					<?php 
					}
					?>
                   </table>
                </td>
            </tr>
			<tr><td colspan="6"><hr></td></tr>
        	<tr  align="left">
        		<td style="text-align:left;"><b>Lease Documents</b></td>
				<td style="text-align:left"></td>
            	<td></td>
            </tr>
            <tr></tr>
            <tr align="left"><td colspan="4"><div id="doc" style="margin-left: 50px;font-weight: bold;text-transform: capitalize;"></div></td></tr>
            <tr align="left">
			<td colspan="6">
            <table id="doc_Id">
            <tr align="left">
            <td><b>Enter document name</b></td>
            <td><b>&nbsp;&nbsp;Select file to upload</b></td>
            </tr>
            <?php if(!isset($_REQUEST['edit']))
			{?>
            <tr align="left">
            <td><input type="text" id="doc_name_1" name="doc_name_1" placeholder="Police Verification Document"></td>
            <td align="left"><input type="file" name="userfile1" id="userfile1"/></td>
            </tr>
            <tr align="left">
            <td><input type="text" id="doc_name_2" name="doc_name_2" placeholder="Lease Aadhar ID" ></td>
            <td align="left"><input type="file" name="userfile2" id="userfile2"/></td>
             <td><input id="btnAddDoc" type="button" value="Add More" /></td><!--<td><div id="doc" style="margin-left: 73px;font-weight: bold;text-transform: capitalize;"></div></td>-->
            </tr>
            <!--<tr><td   valign="middle"><div id="FileContainer" >-->
            <input type="hidden" name="doc_count" id="doc_count" value="2">
            <?php }
			else
			{ ?>
                <tr align="left">
                <td><input type="text" id="doc_name_1" name="doc_name_1"></td>
                <td align="left"><input type="file" name="userfile1" id="userfile1"/></td>
                <td><input id="btnAddDoc" type="button" value="Add More" /></td><!--<td><div id="doc" style="margin-left: 73px;font-weight: bold;text-transform: capitalize;"></div></td>-->
                </tr>
                <!--<tr><td   valign="middle"><div id="FileContainer" >-->
                <input type="hidden" name="doc_count" id="doc_count" value="1">	
                
            <?php }?>
            <!--</div>-->
            <!--</td></tr>-->
            </table>
            </td>
		</tr>
       
    <tr><td colspan="6"><hr></td></tr>
    <tr  align="left">
        <td valign="left"><b>Lessee occupying the unit</b></td>
			<td</td>
            <td></td></tr>
            <tr align="left" >
			<td colspan="8">
            <table id="mem_table" style="margin-top:-10px;" width="100%"><tr align="left" id="mem_table_tr"><td width="20%"><b>&nbsp;&nbsp;Name</b></td>
            <td width="20%"><b>Relation</b></td><td width="20%"><b>Date Of Birth<br/>(DD-MM-YYYY)</b></td>
          	<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;<b><?php //echo $star;?>&nbsp;&nbsp;Contact No.</b></td>
            <td width="20%"><b><?php //echo $star;?>&nbsp;&nbsp;Email Address</b></td>
            <td id="create_login">Create Login</td>
            <td id="send_emails">Send E-Mails ?</td>
            </tr>
            <tr align="left">
            <td align="left" id="members_td_1"><input type="text" name="members_1" id="members_1" style="width:140px;" /></td>
            <td id="relation_td_1"><input type="text" name="relation_1" id="relation_1"  style="width:80px;" value = "Self" /></td>
            <td id="mem_dob_td_1"><input type="text" name="mem_dob_1" id="mem_dob_1"   class="basics_Dob" size="10" style="width:80px;" /></td>
            <td id="contact_td_1">&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="contact_1" id="contact_1"  style="width:80px;"  onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" size="30" /></td>
            <td id="email_td_1"><input type="text" name="email_1" id="email_1" style="width:140px;" /></td>            
			<td><input type="checkbox"  name="chkCreateLogin" id="chkCreateLogin" value="1" /></td>
			<td><input type="checkbox" name="other_send_commu_emails" id="other_send_commu_emails" value="1" /></td>
		</tr>
            <td id="add_button"><input id="btnAdd" type="button" value="Add" onClick="addNewMember()"/></td>
            </tr>
            <!--<tr><td   valign="left"><div id="TextBoxContainer" >-->
    <!--Textboxes will be added here -->
    <input type="hidden" name="count" id="count" value="1">
<!--</div></td></tr>-->
<br />
            
            </table>
            
        </td></tr>
  		<tr><td colspan="6"><hr></td></tr>
 		<tr  align="left" id = "vehicle_main_tr">
        	<td id="vehicleBtnTd" style="text-align:left"><b>Vehicle Details</b></td>
			<td></td>
            <td style="text-align:right"><input type="button" name="addVehicleDetails" id="addVehicleDetails" value="Add Vehicle Details" onClick="getVehicleDetailsTable()" style="display:none"></td>
        </tr>
      	<tr align="left" >
			<td colspan="8">
             <input type="hidden" name="vehiclecount" id="vehiclecount" value="1">
            <table id="vehicle_table" style="margin-top:-10px;" width="100%" >
            	<tr align="center" id="vehicle_table_tr">
                	<td width="15%"><b>&nbsp;&nbsp;Vehicle Type</b></td>
                    <?php
						if($_SESSION['role'] != ROLE_SUPER_ADMIN)
						{
						?>
							<input type="hidden" name="parkingSlot_1" id="parkingSlot_1" value="">
						<?php
						}
						else
						{
						?>
							<td width="20%"><b>Parking Slot No.</b></td>
			        
					<?php
						}
						if($_SESSION['role'] != ROLE_SUPER_ADMIN)
						{
						?>
							<input type="hidden" name="parkingSticker_1" id="parkingSticker_1" value="">
                            <input type="hidden" name="parkingType_1" id="parkingType_1" value="0">
						<?php
						}
						else
						{
						?>
							<td width="20%"><b>Parking Sticker No.</b></td>
                            <td width="15%"><b>Parking Type</b></td>
			            <?php
						}
            			?>
                        <td width="15%"><b>Registration No.</b></td>
          				<td width="20%"><b>Vehicle Owner Name</b></td>
            			<td width="12%"><b>Vehicle Make</b></td>
            			<td width="12%"><b>Vehicle Model</b></td>
                        <td width="15%"><b>Vehicle Colour</b></td>
            </tr>
           
            <!--<tr><td   valign="left"><div id="TextBoxContainer" >-->
    		<!--Textboxes will be added here -->
    		<tr align="center">
           		<td align="center" id="vehicleType_td_1">
            		<select name="vehicleType_1" id="vehicleType_1" <?php if($_SESSION['role'] != ROLE_SUPER_ADMIN) {?>style="width:110px;" <?php }else{?> style = "width:80px"<?php }?>>
						<option value = "">Please Select</option>
                    	<option value="2">Bike</option>
						<option value="4">Car</option>
					</select>
            	</td>
            	<?php
				if($_SESSION['role'] != ROLE_SUPER_ADMIN)
				{
				?>
				<?php
				}
				else
				{
				?>
					<td id = "parkingSlot_td_1"><input type="text" name="parkingSlot_1" id="parkingSlot_1" style="width:80px;" /></td>
				<?php
				}
				?>
				<?php
				if($_SESSION['role'] != ROLE_SUPER_ADMIN)
				{
				}
				else
				{
				?>
					<td id = "parkingSticker_td_1"><input type="text" name="parkingSticker_1" id="parkingSticker_1" style="width:80px;" /></td>
                    <td id="parkingType_td_1">
                	<select id="parkingType_1" name="parkingType_1" style="width:80px;">
                	<?php
						echo $obj_tenant->combobox07("Select `Id`,`ParkingType` from `parking_type` where Status = 'Y' AND IsVisible = '1'", "0");
                    ?>
                	</select>
                </td>
				<?php
				}
				?>
            	
           		<td id="carRegNo_td_1"><input type="text" name="carRegNo_1" id="carRegNo_1" style="width:100px;"/></td>
            	<td id="carOwner_td_1"><input type="text" name="carOwner_1" id="carOwner_1" style="width:130px;" /></td>
            	<td id="carMake_td_1"><input type="text" name="carMake_1" id="carMake_1" style="width:90px;" /></td>            
				<td id = "carModel_td_1"><input type="text" name="carModel_1" id="carModel_1" style="width:90px;"/></td>
				<td id = "carColor_td_1"><input type="text" name="carColor_1" id="carColor_1" style="width:80px;"/></td>
		</tr>
        <tr>
            <td id="addVehicle_button" style="padding-left:1%"><input id="btnAddVehicle" type="button" value="Add" onClick="addNewVehicle()"/></td>
     	</tr>
   
<!--</div></td></tr>-->
<br />
            
            </table>
            
        </td></tr>
 	<tr><td><br/></td></tr> 
<tr align="left">
    	<td valign="middle"><b>Note &nbsp;:&nbsp;</b></td>
    	<td style="text-align:left;"></td>
   		<td></td>
        <td></td>
    </tr>
    <tr>
    	<td id="to_show_note" colspan="3" style="padding-left:3%"></td>
    </tr>
    <tr><td colspan="4" id="textarea"><textarea name="note" id="note" rows="5" cols="50"></textarea></td></tr>
       	<script>			
			CKEDITOR.config.extraPlugins = 'justify';
			CKEDITOR.replace('note', {toolbar: [
         						{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        						{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
   								 ],
								 height: 100,
        						 width: 740,
								 uiColor: '#14B8C4'
								 });
		</script>
        
        <tr><td><input type="hidden" name="tenant_id" id="tenant_id" value=<?php  echo $_GET['edit']?>></td></tr>
         <tr><td><input type="hidden" name="mem_id" id="mem_id" value="<?php  echo $_GET['mem_id']?>"></td></tr>
         <tr><td><input type="hidden" name="unit_id" id="unit_id" value="<?php  echo $unit_details['unit_id']?>"></td></tr>
        <tr><td><input type="hidden" id="doc_id" name="doc_id" value="<?php echo $details[0]['doc_id']?>"></td></tr>
        <tr><td><input type="hidden" value="<?php echo getRandomUniqueCode(); ?>" name="Code" id=="Code" /></td></tr>
		<td colspan="4" align="center">
            <!--<input type="hidden" name="id" id="id">-->
            <input type="submit" name="insert" id="insert" value="Submit" class="btn btn-primary" style="color:#FFF; width:100px;background-color:#337ab7;" >
            <input type="button" name="print" id="print" value="Print" class="btn btn-primary" style="color:#FFF; width:100px;background-color:#337ab7; display:none" onClick="for_print();" >
            </td>
        
         <tr><td><br><br></td></tr>
</table>
<div id="head_for_printing" style="display:none"><center><table><tr><td style="text-align:center"><?php echo $society_dets[0]['society_name']; ?></td></tr><tr><td style="text-align:center"><?php echo $society_dets[0]['society_add']; ?></td></tr></table></center></div>
<div id="for_printing" style="display:none"></div>
</form>
</center>

<table align="center">
<tr>
<td>
<?php
/*echo "<br>";
$str1 = $obj_tenant->pgnation();
echo "<br>";
echo $str = $obj_tenant->display1($str1);
echo "<br>";
$str1 = $obj_tenant->pgnation();
echo "<br>";*/
?>
</td>
</tr>
</table>
</div>
</div>
</body>
</html>
<?php
	if(isset($_REQUEST['edit']) && $_REQUEST['edit'] <> '')
	{
		?>
			<script>
				getTenant('edit-' + <?php echo $_REQUEST['edit'];?>);				
			</script>
		<?php
	}
	
	if(isset($_REQUEST['deleteid']) && $_REQUEST['deleteid'] <> '')
	{
		?>
			<script>
				getTenant('delete-' + <?php echo $_REQUEST['deleteid'];?>);				
			</script>
		<?php
	}
	
	if(isset($_REQUEST['view']) && $_REQUEST['view'] <> '')
	{
		?>
        	<script>
				getTenant('view-' + <?php echo $_REQUEST['view']; ?> );
			</script>
        <?php
	}
?>

<?php
	if(isset($_REQUEST['ter']))
	{
		?>
		<script>
			bTerminate = true;
		</script>
		<?php
	}
?>

<?php include_once "includes/foot.php"; ?>
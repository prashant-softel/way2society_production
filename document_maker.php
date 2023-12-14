
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Document Maker</title>
</head>



<?php include_once "ses_set_as.php"; ?>
<?php
include_once("includes/head_s.php");
include_once ("classes/dbconst.class.php");
include_once("classes/utility.class.php");

include_once("classes/document_maker.class.php");
include_once("classes/renovationRequest.class.php");
include_once("classes/servicerequest.class.php");
$objRenovationRequest = new renovationRequest($m_dbConn,$m_dbConnRoot);
$obj_templates = new doc_templates($m_dbConn,$m_dbConnRoot);
$obj_serviceRequest = new servicerequest($m_dbConn);
$obj_Utility = new utility($m_dbConn,$m_dbConnRoot);
$listofWork = $obj_templates->getListOfWork();
$star = "<font color='#FF0000'>*</font>";
$verificationAccess = $objRenovationRequest->checkVerificationAccess();
$approvalAccess = $objRenovationRequest->checkApprovalAccess();
if(isset($_REQUEST['temp']))
{
	if($_REQUEST['temp'] == $_SESSION['ADDRESS_PROOF_ID'])
	{
		$memberDetails = $obj_templates->getpassDetail2($_REQUEST['unitId']);
	}
}
$memberId = $obj_serviceRequest->getMemberId($_REQUEST['unitId']);
//var_dump($memberDetails);
$memberAddress = $obj_serviceRequest->getMemberAddress($_REQUEST['unitId'],$_SESSION['society_id']);
//$this->obj_Utility = new utility($dbConn, $dbConnRoot);
$template = "";
if(isset($_REQUEST['temp']))
{
	$temp = $_REQUEST['temp'];
	//echo "temp : ".$temp;
	if($temp == $_SESSION['TENANT_REQUEST_ID'])
	{
		$tId = $_REQUEST['tId'];
		if($tId != "")
		{
			$template = $obj_templates->getTenantFinalNOC($tId , $_SESSION['role']);
		}
	}
	else if($temp == $_SESSION['RENOVATION_DOC_ID'])
	{
		//echo "<br>In if 2";
		$rId = $_REQUEST['rId'];
		//echo "<br>rId : ".$rId;
		if($rId != "")
		{
			$template = $obj_templates->getFinalTemplate($rId , $_SESSION['role']);
			//var_dump($template);
		}
	}
	else
	{
		$aId = $_REQUEST['aId'];
		$apId = $_REQUEST['apId'];
		if($aId != "")
		{
			$template = $obj_templates->getAddressProofFinalTemplate($aId,$apId, $_SESSION['role']);
		}
	}
	/*if($temp == $_SESSION['RENOVATION_DOC_ID'])
	{
		$tId = $_REQUEST['tId'];
		if($tId != "")
		{
			$template = $obj_templates->getTenantFinalNOC($tId , $_SESSION['role']);
		}
	}*/
}

?>

 

<html>
<head>

	<style>
		#prev_nominations
		{
			border:1px solid black;
			border-collapse:collapse;
			width:100%;
		}
		#prev_nominations th
		{
			border:1px solid black;
			text-align:center;
			vertical-align:middle;
		}
		#prev_nominations td
		{
			border:1px solid black;
			vertical-align:middle;
		}
		#prev_nominations tr:nth-child(even)
		{
			background-color: #f2f2f2;
		}
		.hover
		{
			cursor:pointer;
		}
		#panel_widget
		{
			display:none !important;
		}
		@media print 
		{
  			#for_printing_td
			{	
				border:1px solid black; !important;
				color:red; !important;
			}
			#for_printing_td tr, td
			{	
				border:1px solid black; !important;
			}
		}
	</style>
    
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsevents20190504.js"></script>
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
    <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
    <script language="javascript" type="application/javascript">
	//Vaishali's Code
	//Renovation request from service request page.
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
	function selectAllMember()
	{
		var checkStatus = document.getElementById("selectAllMem").checked;
		if(checkStatus)
		{
			$('#memberDetailsTable .chkMemberId').prop('checked', true);
		}
		else
		{
			$('#memberDetailsTable .chkMemberId').prop('checked', false);
		}
	}
	function displayUpdateBtn()
	{
		checkedStatus = 0;
		var action = "<?php if(isset($_REQUEST['action'])){echo $_REQUEST['action'];}else { echo "";}?>";
		if(action == "verify")
		{
			checkedStatus = document.getElementById("verify").checked;
		}
		if(action == "approve")
		{
			checkedStatus = document.getElementById("approve").checked;
		}
		//alert (checkedStatus)
		if(checkedStatus)
		{
			document.getElementById("updateRRBtn").style.display = "block";
			document.getElementById("create_button").style.display = "none";
		}
	}
	function updateRenovationRequest()
	{
		var requestType = "<?php if(isset($_REQUEST['rId'])){ echo "rr"; } else if(isset($_REQUEST['aId'])){ echo "ap"; }else { echo "";};?>";
		var rId = 0;
		var aId = 0;
		if(requestType == "rr")
		{
			rId = "<?php echo $_REQUEST['rId'];?>";
		}
		else
		{
			rId = "<?php echo $_REQUEST['aId'];?>";
			aId = "<?php echo $_REQUEST['apId']?>";
		}
		var action = "<?php if(isset($_REQUEST['action'])){echo $_REQUEST['action'];}else { echo "";}?>";
		//alert(action);
		$.ajax
		({
			url : "ajax/ajaxdocument_maker.ajax.php",
			type : "POST",
			data: {"method": "updateRenovationStatus", "renovationId":rId,"addressProofId":aId, "type":action, "requestType":requestType},
			success: function(data3)
			{
				data3 = data3.trim();
				if(data3 != "")
				{
					if(action == "verify")
					{
						document.getElementById("verifyBtn").style.display = "none";
						document.getElementById("verifyTd").innerHTML = "<b>Request Verified Successfully.</b>";
					}
					else
					{
						document.getElementById("approveBtn").style.display = "none";
						document.getElementById("verifyTd").innerHTML = "<b>Request Approved Successfully.</b>";
					}
					
					document.getElementById('create_button').style.display = "table-cell";
					document.getElementById('create_button').colSpan = "2";
				}
				else
				{
					alert("Can't Update details, try again later.")
				}
			}
		});
	}
	//End
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }
	function getDrawing(value)
	{
		//alert (value);
		if(value == "Painting" || value == "Grill" || value == "Carpentary")
		{
			document.getElementById("docSpan").innerHTML = "<b>Drawing</b>";
		}
		else
		{
			//alert ("in else");
			document.getElementById("docSpan").innerHTML = "<?php echo $star;?><b>Drawing</b>";
		}
	}
	var iLabourerCount = 2;
	function addLabourer()
	{
		var maxLabourer = document.getElementById("MaxNoOfLabourer").value;
		if(maxLabourer == "")
		{
			alert ("Please enter maximum labourer count");
			document.getElementById('MaxNoOfLabourer').focus();
		}
		else
		{
			if(iLabourerCount <= maxLabourer)
			{
				var sContent = "<tr><td style='text-align:left;'><input type='text' id='srNo_"+iLabourerCount+"' name='srNo[]' value='"+iLabourerCount+".' style='vertical-align:top;width:50%;size:10%'/></td><td width='5%' style='text-align:left'></td><td style='text-align:left;'><input type='text' id='labourerName_"+iLabourerCount+"' name='labourerName[]' value='' placeholder='Labourer Name' style='vertical-align:top;width:80%;size:10%'/></td></tr>";
				$("#labourerDetails > tbody").append(sContent);
			}
			else
			{
				alert("Cannot add more than "+maxLabourer+" Labourers. If you wants to add than change maximum labourer count.");
			}
			iLabourerCount = iLabourerCount + 1;
		}
	}
	function goToServiceRequest()
	{
		window.location.href = "servicerequest.php?type=createdme";
	}
	function goToRenovationReport()
	{
		var action =  "<?php echo $_REQUEST['action'];?>";
		var temp = "<?php echo $_REQUEST['temp'];?>";
		if(action == "verify")
		{
			if(temp == "<?php echo $_SESSION['RENOVATION_DOC_ID']?>")
			{
				window.location.href = "renovationRequest.php?type=pending";
			}
			else
			{
				window.location.href = "addressproofApproval.php?type=pending";
			}
		}
		else
		{
			if(temp == "<?php echo $_SESSION['RENOVATION_DOC_ID']?>")
			{
				window.location.href = "renovationRequest.php?type=verified";
			}
			else
			{
				window.location.href = "addressproofApproval.php?type=verified";
			}
		}
	}
	
	$( document ).ready(function()
	{
		var rId = "<?php if(isset($_REQUEST['rId'])){echo $_REQUEST['rId'];}else { echo "";}?>";//Renovation Request Id
		var tempType = "<?php if(isset($_REQUEST['temp'])){echo $_REQUEST['temp'];}else { echo "";}?>";
		var aId = "<?php if(isset($_REQUEST['aId'])){echo $_REQUEST['aId'];}else { echo "";}?>";//Address Proof Request Id
		var tId = "<?php if(isset($_REQUEST['tId'])){echo $_REQUEST['tId'];}else { echo "";}?>";//Tenant Request Id
		//alert (tempType);
		//alert (aId);
		var action = "<?php if(isset($_REQUEST['action'])){echo $_REQUEST['action'];}else { echo "";}?>";//action type verify or approve.
		//alert(action);
		var userRole = "<?php echo $_SESSION['role']?>";//Login user role.
		if(tempType == "<?php echo $_SESSION['RENOVATION_DOC_ID']?>" && rId != "")
		{
			//alert("userRole :"+userRole);
			var unitId="";
			if(userRole != "<?php echo ROLE_MEMBER;?>")
			{
				document.getElementById("main_tr_1").style.display = "none";
				document.getElementById("show_member").style.display = "none";
				unitId = "<?php echo $_REQUEST['unitId'];?>";
				//alert("unitId :"+unitId);
				document.getElementById("renovationUnitId").value = unitId;
			}
			else
			{
				document.getElementById('show_member').style.display = "block";
				document.getElementById("temp_type").value = 53;
				document.getElementById('temp_type').disabled = true;
				document.getElementById("member").innerHTML = "<?php echo $combo_unit = $obj_templates->combobox07("select u.unit_id, CONCAT(CONCAT(u.unit_no,' '), mm.owner_name) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.society_id = '" . $_SESSION['society_id'] . "' and ownership_status = 1 ORDER BY u.sort_order",$_SESSION['unit_id']);?>";
				unitId = document.getElementById("member").value;
				document.getElementById("renovationUnitId").value = unitId;
				document.getElementById('member').disabled = true;
			}
			document.getElementById('doc_temp').style.display = "table-row";
			document.getElementById('doc_temp_2').style.display = "table-row";
			document.getElementById('create_button').style.display = "table-cell";
			document.getElementById('create_button').colSpan = "2";
			document.getElementById('nomination_form').style.display="none";
			document.getElementById('electric_meter_noc').style.display = "none";
			document.getElementById('leave_and_license_noc').style.display = "none";
			document.getElementById('bank_noc').style.display = "none";
			document.getElementById('AGM').style.display = "none";
			document.getElementById('show_for_agm').style.display = "none";
			document.getElementById('show_for_ptc').style.display = "none";
			document.getElementById('notice_button').style.display = "none";
			document.getElementById('dom_pass').style.display = "none";
			document.getElementById("pageheader").innerHTML = "Renovation Request";
			if(action == "verify" || action == "approve")
			{
				document.getElementById("btnBackReport").style.display = "block";
			}
			else
			{
				document.getElementById("btnBack").style.display = "block";
			}
			CKEDITOR.instances['events_desc'].setData("<?php echo $template;?>");
			$.ajax
			({
				url : "ajax/ajaxdocument_maker.ajax.php",
				type : "POST",
				data: {"method": "fetch_renovation_document", "renovationId":rId},
				success: function(data2)
				{
					document.getElementById("main_tr_1").style.display = "none";
					document.getElementById("show_member").style.display = "none";
					document.getElementById("renovation_document").style.display = "table-row";
					document.getElementById("renovation_document_td").innerHTML = data2;
 				}
			});			
		}
		if(tempType == "<?php echo $_SESSION['RENOVATION_DOC_ID']?>" && rId == "")
		{
			if(userRole != "<?php echo ROLE_MEMBER;?>")
			{
				document.getElementById("main_tr_1").style.display = "none";
				document.getElementById("show_member").style.display = "none";
				unitId = "<?php echo $_REQUEST['unitId'];?>";
				document.getElementById("renovationUnitId").value = unitId;
				document.getElementById("renovation_noc").style.display = "block";
			
			}
			else
			{
				document.getElementById("temp_type").value = 53;
				document.getElementById('temp_type').disabled = true;
				unitId = "<?php echo $_SESSION['unit_id'];?>";
				document.getElementById("renovationUnitId").value = unitId;
				
			}
			document.getElementById("tempType").value = "<?php echo $_SESSION['RENOVATION_DOC_ID']?>";
			show_second();
			document.getElementById("pageheader").innerHTML = "Renovation Request";
		}
		//Address Proof request.
		if(tempType == "<?php echo $_SESSION['ADDRESS_PROOF_ID']?>" && aId != "")
		{
			var action = "<?php echo $_REQUEST['action'];?>";
			document.getElementById('doc_temp').style.display = "table-row";
			document.getElementById('doc_temp_2').style.display = "table-row";
			document.getElementById('create_button').style.display = "table-cell";
			document.getElementById('create_button').colSpan = "2";
			document.getElementById('nomination_form').style.display="none";
			document.getElementById('electric_meter_noc').style.display = "none";
			document.getElementById('leave_and_license_noc').style.display = "none";
			document.getElementById('bank_noc').style.display = "none";
			document.getElementById('AGM').style.display = "none";
			document.getElementById('show_for_agm').style.display = "none";
			document.getElementById('show_for_ptc').style.display = "none";
			document.getElementById('notice_button').style.display = "none";
			document.getElementById('dom_pass').style.display = "none";
			//document.getElementById("btnBack").style.display = "block";
			//main_tr_1
			document.getElementById('main_tr_1').style.display = "none";
			document.getElementById('renovation_document').style.display = "table-row";
			document.getElementById("pageheader").innerHTML = "Address Proof Request";
			CKEDITOR.instances['events_desc'].setData("<?php echo $template;?>");
			if(action == "verify" || action == "approve")
			{
				document.getElementById("btnBackReport").style.display = "block";
			}
			else
			{
				document.getElementById("btnBack").style.display = "block";
			}
		}
		/*if(tempType == "<?php echo $_SESSION['ADDRESS_PROOF_ID']?>" && aId == "")
		{
			document.getElementById("main_tr_1").style.display = "none";
			document.getElementById("show_member").style.display = "none";
			document.getElementById('nomination_form').style.display="none";
			document.getElementById('electric_meter_noc').style.display = "none";
			document.getElementById('leave_and_license_noc').style.display = "none";
			document.getElementById('bank_noc').style.display = "none";
			document.getElementById('doc_temp').style.display = "none";
			document.getElementById('doc_temp_2').style.display = "none";
			document.getElementById('create_button').style.display = "none";
			document.getElementById('AGM').style.display = "none";
			document.getElementById('show_for_agm').style.display = "none";
			document.getElementById('show_for_ptc').style.display = "none";
			document.getElementById('notice_button').style.display = "none";
			document.getElementById('dom_pass').style.display = "none";
			document.getElementById("addressProof_noc").style.display = "block";
			document.getElementById("tempType").value = "<?php echo $_SESSION['ADDRESS_PROOF_ID']?>";
			document.getElementById("pageheader").innerHTML = "Address Proof Request";
			if(userRole != "<?php echo ROLE_MEMBER;?>")
			{
				unitId = "<?php echo $_REQUEST['unitId'];?>";
				document.getElementById("addressUnitId").value = unitId;
			}
			else
			{
				unitId = "<?php echo $_SESSION['unit_id'];?>";
				document.getElementById("addressUnitId").value = unitId;
				
			}
		}*/
		if(tempType == "<?php echo $_SESSION['TENANT_REQUEST_ID']?>" && tId != "")
		{
			document.getElementById('doc_temp').style.display = "table-row";
			document.getElementById('doc_temp_2').style.display = "table-row";
			document.getElementById('create_button').style.display = "table-cell";
			document.getElementById('create_button').colSpan = "2";
			document.getElementById('nomination_form').style.display="none";
			document.getElementById('electric_meter_noc').style.display = "none";
			document.getElementById('leave_and_license_noc').style.display = "none";
			document.getElementById('bank_noc').style.display = "none";
			document.getElementById('AGM').style.display = "none";
			document.getElementById('show_for_agm').style.display = "none";
			document.getElementById('show_for_ptc').style.display = "none";
			document.getElementById('notice_button').style.display = "none";
			document.getElementById('dom_pass').style.display = "none";
			document.getElementById("btnBack").style.display = "block";
			document.getElementById('main_tr_1').style.display = "none";
			//main_tr_1
			CKEDITOR.instances['events_desc'].setData("<?php echo $template;?>");
		}
	});
	//End
	function show_second()
	{
		if(document.getElementById('temp_type').value == 27) //overdue payment
		{
			document.getElementById('property_tax_cert').style.display = "none";
			document.getElementById('electric_meter_noc').style.display = "none";
			document.getElementById('leave_and_license_noc').style.display = "none";
			document.getElementById('bank_noc').style.display = "none";
			document.getElementById('show_member').style.display = "block";
			document.getElementById('doc_temp').style.display = "none";
			document.getElementById('doc_temp_2').style.display = "none";
			document.getElementById('create_button').style.display = "none";
			document.getElementById('AGM').style.display = "none";
			document.getElementById('show_for_agm').style.display = "none";
			document.getElementById('show_for_ptc').style.display = "none";
			document.getElementById('notice_button').style.display = "none";
			document.getElementById('dom_pass').style.display = "none";
			document.getElementById("member").innerHTML = "<?php echo $combo_unit = $obj_templates->combobox_for_overdue_payment("select u.unit_id, CONCAT(CONCAT(u.unit_no,' '), mm.owner_name) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.society_id = '" . $_SESSION['society_id'] . "' and ownership_status = 1 ORDER BY u.sort_order","0");?>";
		}
		else if(document.getElementById('temp_type').value == 25) //agm notice
		{
			document.getElementById('property_tax_cert').style.display = "none";
			document.getElementById('electric_meter_noc').style.display = "none";
			document.getElementById('leave_and_license_noc').style.display = "none";
			document.getElementById('bank_noc').style.display = "none";
			document.getElementById('show_member').style.display = "none";
			document.getElementById('doc_temp').style.display = "none";
			document.getElementById('doc_temp_2').style.display = "none";
			document.getElementById('AGM').style.display = "block";
			document.getElementById('generate').hidden = false;	
			document.getElementById('create_button').style.display = "none";
			document.getElementById('show_for_agm').style.display = "none";
			document.getElementById('show_for_ptc').style.display = "none";
			document.getElementById('notice_button').style.display = "none";
			document.getElementById('dom_pass').style.display = "none";
		}	
		else if(document.getElementById('temp_type').value == 1) //associate form
		{
			document.getElementById('property_tax_cert').style.display = "none";
			document.getElementById('electric_meter_noc').style.display = "none";
			document.getElementById('leave_and_license_noc').style.display = "none";
			document.getElementById('bank_noc').style.display = "none";
			document.getElementById('show_member').style.display = "none";
			document.getElementById('doc_temp').style.display = "table-row";
			document.getElementById('doc_temp_2').style.display = "table-row";
			document.getElementById('create_button').style.display = "table-cell";
			document.getElementById('create_button').colSpan = "2";
			document.getElementById('AGM').style.display = "none";
			document.getElementById('show_for_agm').style.display = "none";
			document.getElementById('show_for_ptc').style.display = "none";
			document.getElementById('notice_button').style.display = "none";
			document.getElementById('dom_pass').style.display = "none";
			
			var temp_id = document.getElementById('temp_type').value;
			$.ajax({
			url : "ajax/ajaxdocument_maker.ajax.php",
			type : "POST",
			dataType : "json",
			data: {"method": "fetch_template_data", "template_id":temp_id},
			success: function(data)
			{
				CKEDITOR.instances['events_desc'].setData(data);
			}
			});
		}
		else if(document.getElementById('temp_type').value == 28) //debit(fine)
		{
			document.getElementById('property_tax_cert').style.display = "none";
			document.getElementById('electric_meter_noc').style.display = "none";
			document.getElementById('leave_and_license_noc').style.display = "none";
			document.getElementById('bank_noc').style.display = "none";
			document.getElementById('show_member').style.display = "block";
			document.getElementById('doc_temp').style.display = "none";
			document.getElementById('doc_temp_2').style.display = "none";
			document.getElementById('create_button').style.display = "none";
			document.getElementById('AGM').style.display = "none";
			document.getElementById('show_for_agm').style.display = "none";
			document.getElementById('show_for_ptc').style.display = "none";
			document.getElementById('notice_button').style.display = "none";
			document.getElementById('dom_pass').style.display = "none";
			document.getElementById("member").innerHTML = "<?php echo $combo_unit = $obj_templates->combobox07("select rs.ID, CONCAT(CONCAT(CONCAT(u.unit_no,' '), mm.owner_name),' - ',rs.Amount) AS 'unit_no' from `unit` AS u, `member_main` AS mm, `reversal_credits` AS rs where u.unit_id = mm.unit and u.unit_id = rs.UnitID and u.society_id = '".$_SESSION['society_id']."' and ownership_status = 1 and rs.ChargeType = 2 ORDER BY u.sort_order");?>";
		}
		else if(document.getElementById('temp_type').value == 29) //credit(refund)
		{
			document.getElementById('property_tax_cert').style.display = "none";
			document.getElementById('electric_meter_noc').style.display = "none";
			document.getElementById('leave_and_license_noc').style.display = "none";
			document.getElementById('bank_noc').style.display = "none";
			document.getElementById('show_member').style.display = "block";
			document.getElementById('doc_temp').style.display = "none";
			document.getElementById('doc_temp_2').style.display = "none";
			document.getElementById('create_button').style.display = "none";
			document.getElementById('AGM').style.display = "none";
			document.getElementById('show_for_agm').style.display = "none";
			document.getElementById('show_for_ptc').style.display = "none";
			document.getElementById('notice_button').style.display = "none";
			document.getElementById('dom_pass').style.display = "none";
			document.getElementById("member").innerHTML = "<?php echo $combo_unit = $obj_templates->combobox07("select rs.ID, CONCAT(CONCAT(CONCAT(u.unit_no,' '), mm.owner_name),' - ',rs.Amount) AS 'unit_no' from `unit` AS u, `member_main` AS mm, `reversal_credits` AS rs where u.unit_id = mm.unit and u.unit_id = rs.UnitID and u.society_id = '".$_SESSION['society_id']."' and ownership_status = 1 and rs.ChargeType = 1 ORDER BY u.sort_order");?>";
		}		
		else if(document.getElementById('temp_type').value == 49) //nomination form
		{
			document.getElementById('property_tax_cert').style.display = "none";
			document.getElementById('electric_meter_noc').style.display = "none";
			document.getElementById('leave_and_license_noc').style.display = "none";
			document.getElementById('bank_noc').style.display = "none";
			document.getElementById('show_member').style.display = "block";
			document.getElementById('doc_temp').style.display = "none";
			document.getElementById('doc_temp_2').style.display = "none";
			//document.getElementById('create_button').style.display = "table-cell";
			//document.getElementById('create_button').colSpan = "2";
			document.getElementById('AGM').style.display = "none";
			document.getElementById('show_for_agm').style.display = "none";
			document.getElementById('show_for_ptc').style.display = "none";
			document.getElementById('notice_button').style.display = "none";
			document.getElementById('dom_pass').style.display = "none";
			document.getElementById('renovation_noc').style.display="none";
			document.getElementById("member").innerHTML = "<?php echo $combo_unit = $obj_templates->combobox07("select u.unit_id, CONCAT(CONCAT(u.unit_no,' '), mm.owner_name) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.society_id = '" . $_SESSION['society_id'] . "' and ownership_status = 1 ORDER BY u.sort_order",$_SESSION['unit_id']); ?>";
			document.getElementById('member').disabled = true;
			$("#member").trigger("change");
			//ALTER TABLE `nomination_form` ADD `nomination_status` INT(11) NOT NULL COMMENT '0-Draft, 1-Submitted, 2-Approved' ;
		}
		else if(document.getElementById('temp_type').value == 33 || document.getElementById('temp_type').value == 31) //domicile and passport noc resp.
		{
			document.getElementById('property_tax_cert').style.display = "none";
			document.getElementById('electric_meter_noc').style.display = "none";
			document.getElementById('leave_and_license_noc').style.display = "none";
			document.getElementById('bank_noc').style.display = "none";
			document.getElementById('dom_pass').style.display = "block";
			document.getElementById('show_member').style.display = "none";
			document.getElementById('doc_temp').style.display = "none";
			document.getElementById('doc_temp_2').style.display = "none";
			document.getElementById('create_button').style.display = "none";
			document.getElementById('AGM').style.display = "none";
			document.getElementById('show_for_agm').style.display = "none";
			document.getElementById('show_for_ptc').style.display = "none";
			document.getElementById('notice_button').style.display = "none";
			document.getElementById('create_button').colSpan = "2";
			if(document.getElementById('temp_type').value == 31)
			{
				document.getElementById('msg_dom_pass').innerHTML = "<b>Please fill in the following details to generate Passport NOC:</b>"
			}
			else if(document.getElementById('temp_type').value == 33)
			{
				document.getElementById('msg_dom_pass').innerHTML = "<b>Please fill in the following details to generate Domicile NOC:</b>";
			}
		}
		else if(document.getElementById('temp_type').value == 34) //bank noc
		{
			document.getElementById('property_tax_cert').style.display = "none";
			document.getElementById('electric_meter_noc').style.display = "none";
			document.getElementById('leave_and_license_noc').style.display = "none";
			document.getElementById('bank_noc').style.display = "none";
			document.getElementById('dom_pass').style.display = "none";
			document.getElementById('show_member').style.display = "block";
			document.getElementById('doc_temp').style.display = "none";
			document.getElementById('doc_temp_2').style.display = "none";
			document.getElementById('create_button').style.display = "none";
			document.getElementById('AGM').style.display = "none";
			document.getElementById('show_for_agm').style.display = "none";
			document.getElementById('show_for_ptc').style.display = "none";
			document.getElementById('notice_button').style.display = "none";
			document.getElementById("member").innerHTML = "<?php echo $combo_unit = $obj_templates->combobox07("select u.unit_id, CONCAT(CONCAT(u.unit_no,' '), mm.owner_name) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.society_id = '" . $_SESSION['society_id'] . "' and ownership_status = 1 ORDER BY u.sort_order");?>";
		}
		else if(document.getElementById('temp_type').value == 35) //leave and license noc
		{
			document.getElementById('property_tax_cert').style.display = "none";
			document.getElementById('electric_meter_noc').style.display = "none";
			document.getElementById('leave_and_license_noc').style.display = "none";
			document.getElementById('bank_noc').style.display = "none";
			document.getElementById('dom_pass').style.display = "none";
			document.getElementById('show_member').style.display = "block";
			document.getElementById('doc_temp').style.display = "none";
			document.getElementById('doc_temp_2').style.display = "none";
			document.getElementById('create_button').style.display = "none";
			document.getElementById('AGM').style.display = "none";
			document.getElementById('show_for_agm').style.display = "none";
			document.getElementById('show_for_ptc').style.display = "none";
			document.getElementById('notice_button').style.display = "none";
			document.getElementById("member").innerHTML = "<?php echo $combo_unit = $obj_templates->combobox07("select u.unit_id, CONCAT(CONCAT(u.unit_no,' '), mm.owner_name) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.society_id = '" . $_SESSION['society_id'] . "' and ownership_status = 1 ORDER BY u.sort_order");?>";
		}
		else if(document.getElementById('temp_type').value == 36) //transfer of electric meter noc
		{
			document.getElementById('property_tax_cert').style.display = "none";
			document.getElementById('electric_meter_noc').style.display = "block";
			document.getElementById('leave_and_license_noc').style.display = "none";
			document.getElementById('bank_noc').style.display = "none";
			document.getElementById('dom_pass').style.display = "none";
			document.getElementById('show_member').style.display = "none";
			document.getElementById('doc_temp').style.display = "none";
			document.getElementById('doc_temp_2').style.display = "none";
			document.getElementById('create_button').style.display = "none";
			document.getElementById('AGM').style.display = "none";
			document.getElementById('show_for_agm').style.display = "none";
			document.getElementById('show_for_ptc').style.display = "none";
			document.getElementById('notice_button').style.display = "none";
		}
		else if(document.getElementById('temp_type').value == 37 || document.getElementById('temp_type').value == 38) //web access blocked and web access restored resp.
		{
			document.getElementById('property_tax_cert').style.display = "none";
			document.getElementById('electric_meter_noc').style.display = "none";
			document.getElementById('leave_and_license_noc').style.display = "none";
			document.getElementById('bank_noc').style.display = "none";
			document.getElementById('show_member').style.display = "block";
			document.getElementById('doc_temp').style.display = "none";
			document.getElementById('doc_temp_2').style.display = "none";
			document.getElementById('create_button').style.display = "none";
			document.getElementById('AGM').style.display = "none";
			document.getElementById('show_for_agm').style.display = "none";
			document.getElementById('show_for_ptc').style.display = "none";
			document.getElementById('notice_button').style.display = "none";
			document.getElementById('dom_pass').style.display = "none";
			if(document.getElementById('temp_type').value == 37)
			{
				document.getElementById("member").innerHTML = "<?php echo $combo_unit = $obj_templates->combobox07("select u.unit_id, CONCAT(CONCAT(u.unit_no,' - '), mm.owner_name) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.society_id = '" . $_SESSION['society_id'] . "' and ownership_status = 1 and u.block_unit = 1 ORDER BY u.sort_order","0"); ?>";
			}
			else if(document.getElementById('temp_type').value ==38)
			{
				document.getElementById("member").innerHTML = "<?php echo $combo_unit = $obj_templates->combobox07("select u.unit_id, CONCAT(CONCAT(u.unit_no,' - '), mm.owner_name) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.society_id = '" . $_SESSION['society_id'] . "' and ownership_status = 1 and u.block_unit = 0 ORDER BY u.sort_order","0"); ?>";
			}
		}
		else if(document.getElementById('temp_type').value == 51) //property tax certificate
		{
			//document.getElementById('society_cb_tr').style.display = "none";
			document.getElementById('electric_meter_noc').style.display = "none";
			document.getElementById('leave_and_license_noc').style.display = "none";
			document.getElementById('bank_noc').style.display = "none";
			document.getElementById('show_member').style.display = "block";
			document.getElementById('doc_temp').style.display = "none";
			document.getElementById('doc_temp_2').style.display = "none";
			document.getElementById('create_button').style.display = "none";
			document.getElementById('AGM').style.display = "none";
			document.getElementById('show_for_agm').style.display = "none";
			document.getElementById('show_for_ptc').style.display = "none";
			document.getElementById('notice_button').style.display = "none";
			document.getElementById('dom_pass').style.display = "none";
			document.getElementById("member").innerHTML = "<?php echo $combo_unit = $obj_templates->combobox07("select u.unit_id, CONCAT(CONCAT(u.unit_no,' '), mm.owner_name) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.society_id = '" . $_SESSION['society_id'] . "' and ownership_status = 1 ORDER BY u.sort_order","0");?>";
		}
		else if(document.getElementById('temp_type').value == 53) //renovation letter
		{
			document.getElementById('show_member').style.display = "block";
			document.getElementById("member").innerHTML = "<?php echo $combo_unit = $obj_templates->combobox07("select u.unit_id, CONCAT(CONCAT(u.unit_no,' '), mm.owner_name) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.society_id = '" . $_SESSION['society_id'] . "' and ownership_status = 1 ORDER BY u.sort_order",$_SESSION['unit_id']);?>";
			var unitId = document.getElementById("member").value;
			document.getElementById("unitId").value = unitId;
			document.getElementById('member').disabled = true;
			$("#member").trigger("change");
			document.getElementById('nomination_form').style.display="none";
			document.getElementById('electric_meter_noc').style.display = "none";
			document.getElementById('leave_and_license_noc').style.display = "none";
			document.getElementById('bank_noc').style.display = "none";
			document.getElementById('doc_temp').style.display = "none";
			document.getElementById('doc_temp_2').style.display = "none";
			document.getElementById('create_button').style.display = "none";
			document.getElementById('AGM').style.display = "none";
			document.getElementById('show_for_agm').style.display = "none";
			document.getElementById('show_for_ptc').style.display = "none";
			document.getElementById('notice_button').style.display = "none";
			document.getElementById('dom_pass').style.display = "none";
		}
	}
	
	function show_temp_and_fetch_data()
	{		
		if(document.getElementById('temp_type').value == 27) //overdue payment
		{	
			document.getElementById('doc_temp').style.display = "table-row";
			document.getElementById('doc_temp_2').style.display = "table-row";
			document.getElementById('create_button').style.display = "table-cell";
			document.getElementById('create_button').colSpan = "1";
			document.getElementById('show_for_agm').style.display = "none";
			document.getElementById('show_for_ptc').style.display = "none";
			document.getElementById('notice_button').style.display = "table-cell";
			
			var unit_id = document.getElementById('member').value;
			var temp_id = document.getElementById('temp_type').value;
  			$.ajax({
			url : "ajax/ajaxdocument_maker.ajax.php",
			type : "POST",
			dataType: "json",
			data: {"method": "fetch_template_data", "template_id":temp_id, "unit_id":unit_id} ,
			success : function(data)
			{
				//alert(data);
				document.getElementById('society_cb_tr').style.display = "table-row";
				CKEDITOR.instances['events_desc'].setData(data);
			}
			});
		}
		else if(document.getElementById('temp_type').value == 25) //agm notice
		{
			document.getElementById('AGM').style.display = "block";			
			
			var temp_id = document.getElementById('temp_type').value;
			var date_of_meeting = document.getElementById('meeting_date').value;
			var venue = document.getElementById('venue').value;
			var time_of_meeting = document.getElementById('hr').value + ":" + document.getElementById('mn').value + " " + document.getElementById('ampm').value;

			var time_of_meeting_temp = document.getElementById('hr').value + document.getElementById('mn').value;

			var last_agm_date = document.getElementById('last_date').value;
			
			if(document.getElementById('society_cb').checked == true)
			{
				var society_name = 1;				
			}
			else if(document.getElementById('society_cb').checked == false)
			{
				var society_name = 0;
			}
			
			if(date_of_meeting != "" && venue != "" && time_of_meeting_temp != "" && last_agm_date != "")
			{
				document.getElementById('doc_temp').style.display = "table-row";
				document.getElementById('doc_temp_2').style.display = "table-row";
				document.getElementById('create_button').style.display = "table-cell";
				document.getElementById('create_button').colSpan = "1";
				document.getElementById('notice_button').style.display = "table-cell";
				document.getElementById('show_for_agm').style.display = "table-row";
				document.getElementById('show_for_ptc').style.display = "none";
				
				$.ajax({
				url : "ajax/ajaxdocument_maker.ajax.php",
				type : "POST",
				dataType : "json",
				data: {"method": "fetch_template_data", "template_id":temp_id, "date_of_meeting":date_of_meeting, "venue":venue, "time_of_meeting":time_of_meeting, "last_agm_date":last_agm_date, "society_name":society_name},
				success: function(data)
				{
					//alert(data);
					CKEDITOR.instances['events_desc'].setData(data);
				}
				});
			}
			else
			{
				alert("Please fill all the * marked fields.");
			}
		}
		else if(document.getElementById('temp_type').value == 28 || document.getElementById('temp_type').value == 29) //debit(fine) and credit(refund) resp.
		{
			var temp_id = document.getElementById('temp_type').value;
			var fine_id = document.getElementById('member').value;
			document.getElementById('doc_temp').style.display = "table-row";
			document.getElementById('doc_temp_2').style.display = "table-row";
			document.getElementById('create_button').style.display = "table-cell";
			document.getElementById('create_button').colSpan = "1";
			document.getElementById('notice_button').style.display = "table-cell";

			$.ajax({
			url : "ajax/ajaxdocument_maker.ajax.php",
			type : "POST",
			dataType : "json",
			data: {"method": "fetch_template_data", "template_id":temp_id, "fine_rs_id":fine_id},
			success: function(data)
			{
				//alert(data);
				CKEDITOR.instances['events_desc'].setData(data);
			}
			});
		}
		else if(document.getElementById('temp_type').value == 31 || document.getElementById('temp_type').value == 33) //passport and domicile resp.
		{
			var template_id = document.getElementById('temp_type').value;
			var applier_name = document.getElementById('applier_name').value;
			var relation = document.getElementById('relation_with_owner').value;
			var owner_name = document.getElementById('owner_name').value;
			var address = document.getElementById('address').value;
			var start_date = document.getElementById('start_date').value;
			
			if(applier_name != "" && relation != "" && owner_name != "" && address != "" & start_date != "")
			{
				document.getElementById('doc_temp').style.display = "table-row";
				document.getElementById('doc_temp_2').style.display = "table-row";
				document.getElementById('create_button').style.display = "table-cell";
				document.getElementById('create_button').colSpan = "2";
		
				$.ajax({
				url : "ajax/ajaxdocument_maker.ajax.php",
				type : "POST",
				dataType : "json",
				data: {"method": "fetch_template_data", "template_id":template_id, "applier_name":applier_name, "relation":relation, "owner_name":owner_name, "address":address, "start_date":start_date},
				success: function(data)
				{
					CKEDITOR.instances['events_desc'].setData(data);
				}
				});
			}
			else
			{
				alert("Please fill all the * marked fields.");
			}
		}
		else if(document.getElementById('temp_type').value == 34) //bank noc
		{
			var unit_id = document.getElementById('member').value;
			//alert(unit_id);			
			$.ajax({
			url : "ajax/ajaxdocument_maker.ajax.php",
			type : "POST",
			dataType : "json",
			data: {"method": "fetch_unit_details_and_cc_dets", "unit_id":unit_id},
			success: function(data)
			{
				//alert(data[0]["unit"]);
				document.getElementById('flat_no').value = data[0]["unit_no"];
				document.getElementById('flat_owner_name').value = data[0]["primary_owner_name"];
				document.getElementById('cc_no').value = data[0]["cc_no"];
				document.getElementById('cc_date').value = data[0]["cc_date"];
				document.getElementById('bank_noc').style.display = "block";
			}
			});
		}
		else if(document.getElementById('temp_type').value == 35) //leave and license noc
		{
			var unit_id = document.getElementById('member').value;
			//alert(unit_id);			
			$.ajax({
			url : "ajax/ajaxdocument_maker.ajax.php",
			type : "POST",
			dataType : "json",
			data: {"method": "fetch_unit_details_and_cc_dets", "unit_id":unit_id},
			success: function(data)
			{
				//alert(data[0]["unit"]);
				document.getElementById('flat_no_lnl').value = data[0]["unit_no"];
				document.getElementById('flat_owner_name_lnl').value = data[0]["primary_owner_name"];
				document.getElementById('leave_and_license_noc').style.display = "block";
			}
			});
						
		}
		else if(document.getElementById('temp_type').value == 36) //transfer of electric meter noc
		{
			var template_id = document.getElementById('temp_type').value;
			var flat_owner_name = document.getElementById('flat_owner_name_em').value;
			var flat_no = document.getElementById('flat_no_em').value;
			var flat_owner_address = document.getElementById('owner_address').value;
			var current_em = document.getElementById('current_elec_name').value;
			var gender = ""
			if(document.getElementById('female_em').checked == true)
			{
				gender = "her";
			}
			else if(document.getElementById('male_em').checked == true)
			{
				gender = "his";
			}
			
			if(flat_owner_name != "" && flat_no != "" && owner_address != "" && gender != "" && current_em != "")
			{
				document.getElementById('doc_temp').style.display = "table-row";
				document.getElementById('doc_temp_2').style.display = "table-row";
				document.getElementById('create_button').style.display = "table-cell";
				document.getElementById('create_button').colSpan = "2";
				
				$.ajax({
				url : "ajax/ajaxdocument_maker.ajax.php",
				type : "POST",
				dataType : "json",
				data: {"method": "fetch_template_data", "template_id":template_id, "flat_owner_name":flat_owner_name, "flat_no":flat_no, "flat_owner_address":flat_owner_address, "gender":gender, "current_em":current_em},
				success: function(data)
				{
					CKEDITOR.instances['events_desc'].setData(data);
				}
				});
			}
			else
			{
				alert("Please fill all the * marked fields.");
			}
		}
		else if(document.getElementById('temp_type').value == 37 || document.getElementById('temp_type').value == 38) //web access blocked and Web access restored resp.
		{
			document.getElementById('doc_temp').style.display = "table-row";
			document.getElementById('doc_temp_2').style.display = "table-row";
			document.getElementById('create_button').style.display = "table-cell";
			//document.getElementById('create_button').colSpan = "1";
			document.getElementById('show_for_agm').style.display = "none";
			document.getElementById('show_for_ptc').style.display = "none";
			//document.getElementById('notice_button').style.display = "table-cell";
			document.getElementById('create_button').colSpan = "2";
			
			var unit_id = document.getElementById('member').value;
			var temp_id = document.getElementById('temp_type').value;
  			$.ajax({
			url : "ajax/ajaxdocument_maker.ajax.php",
			type : "POST",
			dataType: "json",
			data: {"method": "fetch_template_data", "template_id":temp_id, "unit_id":unit_id} ,
			success : function(data)
			{
				//alert(data);
				CKEDITOR.instances['events_desc'].setData(data);
			}
			});
		}
		else if(document.getElementById('temp_type').value == 49) //nomination form
		{			
			var unit_id = document.getElementById('member').value;
			
			$.ajax({
			url : "ajax/ajaxdocument_maker.ajax.php",
			type : "POST",
			dataType : "json",
			data: {"method": "get_all_nominations", "unit_id":unit_id},
			success: function(data)
			{
				if(data != "")
				{
					document.getElementById('prev_nomination_info').style.display = "block";
					document.getElementById('prev_nomination_info').innerHTML = data;
				}
				else
				{
					document.getElementById('nomination_form').style.display = "block";
					var unit_id = document.getElementById('member').value;
					//document.getElementById('nomination_form').style.display = "none";
					var nomination_id = 0;

					$.ajax({
					url : "ajax/ajaxdocument_maker.ajax.php",
					type : "POST",
					dataType : "json",
					data: {"method": "fetch_unit_details_for_nomination", "unit_id":unit_id, "nomination_id":nomination_id},
					success: function(data)
					{
						document.getElementById('nomination_form').style.display = "block";
						document.getElementById('flat_no_nomi').value = data[0]["unit_no"];
						document.getElementById('owner_name_nomi').value = data[0]["owner_name"];
						document.getElementById('sc_serial_no').value = data[0]["share_certificate"];
						document.getElementById('total_area').value = data[0]["area"];
						document.getElementById('start_no').value = data[0]["share_certificate_from"];
						document.getElementById('end_no').value = data[0]["share_certificate_to"];	
						document.getElementById('amt_per_share').value = data[0]["amt_per_share"];
						if(data[0]["share_certificate_from"] != 0 && data[0]["share_certificate_from"] != '' && data[0]["share_certificate_to"] != 0 && data[0]["share_certificate_to"] != '')
						{
							var no_of_shares = 0;
							no_of_shares = parseFloat(data[0]["share_certificate_to"]) - parseFloat(data[0]["share_certificate_from"]);
							no_of_shares = no_of_shares + 1;
							document.getElementById('no_of_shares').value = no_of_shares;
						}
						AddNewRow();
					}
					});
				}
			}
			});
		}
		else if(document.getElementById('temp_type').value == 51) //property tax certificate
		{
			document.getElementById('property_tax_cert').style.display = "block";
			document.getElementById('show_for_ptc').style.display = "none";
			//document.getElementById('society_cb_tr').style.display = "table-row";
		}
		else if(document.getElementById('temp_type').value == 53) //renovation letter
		{
			document.getElementById('renovation_noc').style.display = "block";
			//document.getElementById('renovation_text').value = "1. \n\n2. \n\n3. \n\n4. ";
			//get designated area thru ajax here
			$.ajax({
			url : "ajax/ajaxdocument_maker.ajax.php",
			type : "POST",
			dataType : "json",
			data: {"method": "fetch_garbage_area"},
			success: function(data)
			{
				//document.getElementById('garbage_area').value = data; <!--Vaishali--> 
			}
			});
		}
	}

	function show_temp()
	{
		if(document.getElementById('temp_type').value == 34) //bank noc
		{
			var template_id = document.getElementById('temp_type').value;
			var manager_name = document.getElementById('bank_manager_name').value;
			var bank_name = document.getElementById('bank_name').value;
			var bank_address = document.getElementById('bank_address').value;
			var flat_no = document.getElementById('flat_no').value;
			var flat_owner_name = document.getElementById('flat_owner_name').value;
			var flat_cost = document.getElementById('flat_cost').value;
			var cc_no = document.getElementById('cc_no').value;
			var cc_date = document.getElementById('cc_date').value;
			
			if(manager_name != "" && bank_name != "" && bank_address != "" && flat_no != "" && flat_owner_name != "" && flat_cost != "" && cc_no != "" && cc_date != "")
			{
				document.getElementById('doc_temp').style.display = "table-row";
				document.getElementById('doc_temp_2').style.display = "table-row";
				document.getElementById('create_button').style.display = "table-cell";
				document.getElementById('create_button').colSpan = "2";
				
				$.ajax({
				url : "ajax/ajaxdocument_maker.ajax.php",
				type : "POST",
				dataType : "json",
				data: {"method": "fetch_template_data", "template_id":template_id, "manager_name":manager_name, "bank_name":bank_name, "bank_address":bank_address, "flat_no":flat_no, "flat_owner_name":flat_owner_name, "flat_cost":flat_cost, "cc_no":cc_no, "cc_date":cc_date},
				success: function(data)
				{
					CKEDITOR.instances['events_desc'].setData(data);
				}
				});
			}
			else
			{
				alert("Please fill all the * marked fields.");
			}
		}
		else if(document.getElementById('temp_type').value == 35) //leave and license noc
		{
			var template_id = document.getElementById('temp_type').value;
			var flat_owner_name = document.getElementById('flat_owner_name_lnl').value;
			var flat_no = document.getElementById('flat_no_lnl').value;			
			var tenant_name = document.getElementById('tenant_name').value;
			var gender = "";
			if(document.getElementById('female').checked == true)
			{
				gender = "her";
			}
			else if(document.getElementById('male').checked == true)
			{
				gender = "his";
			}
			
			if(flat_owner_name != "" && flat_no != "" & tenant_name != "" && gender != "")
			{
				document.getElementById('doc_temp').style.display = "table-row";
				document.getElementById('doc_temp_2').style.display = "table-row";
				document.getElementById('create_button').style.display = "table-cell";
				document.getElementById('create_button').colSpan = "2";
				
				$.ajax({
				url : "ajax/ajaxdocument_maker.ajax.php",
				type : "POST",
				dataType : "json",
				data: {"method": "fetch_template_data", "template_id":template_id, "flat_owner_name":flat_owner_name, "flat_no":flat_no, "tenant_name":tenant_name, "gender":gender},
				success: function(data)
				{
					CKEDITOR.instances['events_desc'].setData(data);
				}
				});
			}
			else
			{
				alert("Please fill all the * marked fields.");
			}
		}
		else if(document.getElementById('temp_type').value == 49) //nomination form
		{
			var temp_id = document.getElementById('temp_type').value;
			var unit_id = document.getElementById('member').value;
			var nomi_owner_name = document.getElementById('owner_name_nomi').value;
			var nomi_flat_no = document.getElementById('flat_no_nomi').value;
			var area = document.getElementById('total_area').value;
			var sc_no = document.getElementById('sc_serial_no').value;
			var sc_date = document.getElementById('sc_issue_date').value;
			var amt_per_share = document.getElementById('amt_per_share').value;
			var no_of_shares = document.getElementById('no_of_shares').value;
			var start_no = document.getElementById('start_no').value;
			var end_no = document.getElementById('end_no').value;
			
			var witness_name_1 = document.getElementById('witness_name_1').value;
			var witness_address_1 = document.getElementById('witness_address_1').value;
			var witness_name_2 = document.getElementById('witness_name_2').value;
			var witness_address_2 = document.getElementById('witness_address_2').value;
			
			var triplicate = 0;
			if(document.getElementById('triplicate').checked == true)
			{
				triplicate = 1;
			}
			else
			{
				triplicate = 0;
			}
			
			var maxRows = document.getElementById('maxrows').value;
			//alert(maxRows);
			var nominees_info = [];
			//var indivi_nominee_info = [];
			
			for(var iRows = 1; iRows < maxRows; iRows++)
			{
				var nomi_name = document.getElementById('Nominee_Name' + iRows).value;
				var nomi_address = document.getElementById('Nominee_Address' + iRows).value;
				var relation = document.getElementById('Relation' + iRows).value;
				var share = document.getElementById('Share' + iRows).value;
				var dob = document.getElementById('DOB' + iRows).value;
				var guardian_name = document.getElementById('guardian_name' + iRows).value;
				
				var indivi_nominee_info = {"name" : nomi_name, "address" : nomi_address, "relation" : relation, "share" : share, "dob" : dob, "guardian_name" : guardian_name};
				
				nominees_info.push(indivi_nominee_info);
			}
			//alert(JSON.stringify(nominees_info));
			
			if(nomi_owner_name != '' && nomi_flat_no != '' && area != '' && sc_no != '' && sc_date != '' && amt_per_share != '' && no_of_shares != '' && start_no != '' && end_no != '' && witness_name_1 != '' && witness_address_1 != '' && witness_name_2 != '' && witness_address_2 != '')
			{
				document.getElementById('doc_temp').style.display = "table-row";
				document.getElementById('doc_temp_2').style.display = "table-row";
				document.getElementById('create_button').style.display = "table-cell";
				document.getElementById('create_button').colSpan = "2";
				document.getElementById('show_for_nomination').style.display = "table-row";
				
				$.ajax({
				url : "ajax/ajaxdocument_maker.ajax.php",
				type : "POST",
				dataType : "json",
				data: {"method": "fetch_template_data", "template_id":temp_id, "flat_owner_name":nomi_owner_name, "flat_no":nomi_flat_no, "area":area, "sc_no":sc_no, "sc_date":sc_date, "amt_per_share":amt_per_share, "no_of_shares":no_of_shares, "start_no":start_no, "end_no":end_no, "witness_name_1":witness_name_1, "witness_address_1":witness_address_1, "witness_name_2":witness_name_2, "witness_address_2":witness_address_2, "nominees_info":JSON.stringify(nominees_info), "is_triplicate":triplicate, "unit_id":unit_id},
				success: function(data)
				{
					document.getElementById('nomination_id').value = data[1];					
					alert("Your records are NOT submitted to society. For legal validity of nomination form you need to submit physical paper copy of signed nomination form in triplicate to the Society Management.");
					CKEDITOR.instances['events_desc'].setData(data[0]);
				}
				});		
			}
			else
			{
				alert("Please fill all the * marked fields.");
			}
		}
		else if(document.getElementById('temp_type').value == 51) //property tax certificate
		{
			var temp_id = document.getElementById('temp_type').value;
			var flat_id = document.getElementById('member').value;
			var ledger_id = document.getElementById('ledgers').value;
			var place = document.getElementById('place').value;
			if(document.getElementById('society_cb').checked == true)
			{
				var society_name = 1;
			}
			else if(document.getElementById('society_cb').checked == false)
			{
				var society_name = 0;
			}
			
			if(ledger_id != 0 && place != "")
			{
				document.getElementById('doc_temp').style.display = "table-row";
				document.getElementById('doc_temp_2').style.display = "table-row";
				document.getElementById('create_button').style.display = "table-cell";
				document.getElementById('create_button').colSpan = "2";
				document.getElementById('show_for_ptc').style.display = "table-row";
				
				$.ajax({
				url : "ajax/ajaxdocument_maker.ajax.php",
				type : "POST",
				dataType : "json",
				data: {"method": "fetch_template_data", "template_id":temp_id, "flat_id":flat_id, "ledger_id":ledger_id, "place":place, "society_name":society_name},
				success: function(data)
				{
					//alert(data);
					CKEDITOR.instances['events_desc'].setData(data);
				}
				});
			}
			else
			{
				alert("Please fill all the * marked fields.");
			}
		}
		else if(document.getElementById('temp_type').value == 53) //renovation letter
		{
			/*var flat_id = document.getElementById('member').value;
			var temp_id = document.getElementById('temp_type').value;
			var renovation_text = document.getElementById('renovation_text').value;
			var garbage_area = document.getElementById('garbage_area').value;
			
			var new_reno_text = renovation_text.replace(/\n/g,"<br>");
			
			var startDate = document.getElementById("startDate").value;
			var endDate =  document.getElementById("endDate").value;
			var workType = document.getElementsByName("workType[]").item(n);*/
			//var file = 
			/*document.getElementById('doc_temp').style.display = "table-row";
			document.getElementById('doc_temp_2').style.display = "table-row";
			document.getElementById('create_button').style.display = "table-cell";
			document.getElementById('create_button').colSpan = "2";
			var renovationId = "<?php if(isset($_REQUEST['RId'])){ echo $_REQUEST['RId'];}else{}?>";
			
			$.ajax({
			url : "ajax/ajaxdocument_maker.ajax.php",
			type : "POST",
			dataType : "json",
			data: {"method": "fetch_final_template", "renovationId":renovationId},
			success: function(data)
			{
				//alert(data);
				CKEDITOR.instances['events_desc'].setData(data);
			}
			});*/
		}
	}
	
	var original = "";
	var value = 1;
	
	function check_triplicate()
	{
		if(value % 2 != 0)
		{
			original = CKEDITOR.instances['events_desc'].getData();
		}
		
		if(document.getElementById('triplicate').checked == true)
		{	
			var duplicate = original.replace("ORIGINAL","DUPLICATE");
			var triplicate = original.replace("ORIGINAL","TRIPLICATE");				
			CKEDITOR.instances['events_desc'].setData(original + "<div style='page-break-after:always'><span style='display:none'>&nbsp;</span></div>" + duplicate + "<div style='page-break-after:always'><span style='display:none'>&nbsp;</span></div>" + triplicate);
		}
		else if(document.getElementById('triplicate').checked == false)
		{
			CKEDITOR.instances['events_desc'].setData(original);
		}
		value++;
	}
	
	function submit_nomination_form()
	{
		var nomination_id = document.getElementById('nomination_id').value;
		if(document.getElementById('submit_form').checked == true)
		{
			document.getElementById('triplicate').checked = true;
			check_triplicate();
			var status_flag = 1;
		}
		else if(document.getElementById('submit_form').checked == false)		
		{
			document.getElementById('triplicate').checked = false;
			check_triplicate();
			var status_flag = 0;
		}
		
		$.ajax({
		url : "ajax/ajaxdocument_maker.ajax.php",
		type : "POST",
		dataType : "json",
		data: {"method": "submit_form", "nomination_id":nomination_id, "status_flag":status_flag},
		success: function(data)
		{
			if(data == 1)
			{
				alert("Please print this triplicate and submit to society office. Your records are NOT submitted to society until you submit it in paper print out. For legal validity of nomination form you need to submit physical paper copy of signed nomination form in triplicate to the Society Management.");
			}
			else if(data == 0)
			{
				alert("Form saved as draft.");
			}
		}
		});
	}
	
	function for_print()
	{
		var html = CKEDITOR.instances.events_desc.getSnapshot();
		var print_div = document.getElementById('for_printing');
		print_div.innerHTML = html;
		console.log(html);	
		var mywindow = window.open('', 'PRINT', 'height=600,width=800');

	    mywindow.document.write('<html><head><title></title>');
    	mywindow.document.write('</head><body>');
    	mywindow.document.write(document.getElementById('for_printing').innerHTML);
	    mywindow.document.write('</body></html>');

    	mywindow.document.close(); // necessary for IE >= 10
	    mywindow.focus(); // necessary for IE >= 10*/

    	mywindow.print();
	    mywindow.close();
		
		return false;
	}
	
	function display_nominations(nomination_id, bReload)
	{
		//Apurva
		var page_action = "edit";
		document.cookie = 'TempType=' + document.getElementById('temp_type').value;
		document.cookie = 'NominationID=' + nomination_id;
		document.cookie = 'page_action=' + page_action;
		//alert(bReload);
		if(bReload == true)
		{
			location.reload();
		}
		var unit_id = document.getElementById('member').value;
		document.getElementById('nomination_form').style.display = "none";

		$.ajax({
		url : "ajax/ajaxdocument_maker.ajax.php",
		type : "POST",
		dataType : "json",
		data: {"method": "fetch_unit_details_for_nomination", "unit_id":unit_id, "nomination_id":nomination_id},
		success: function(data)
		{
			document.getElementById('nomination_form').style.display = "block";
			document.getElementById('nomination_id').value = nomination_id;
			document.getElementById('flat_no_nomi').value = data[0]["unit_no"];
			document.getElementById('owner_name_nomi').value = data[0]["owner_name"];
			document.getElementById('sc_serial_no').value = data[0]["share_certificate"];
			document.getElementById('total_area').value = data[0]["area"];
			document.getElementById('start_no').value = data[0]["share_certificate_from"];
			document.getElementById('end_no').value = data[0]["share_certificate_to"];	
			document.getElementById('amt_per_share').value = data[0]["amt_per_share"];
			if(data[0]["share_certificate_from"] != 0 && data[0]["share_certificate_from"] != '' && data[0]["share_certificate_to"] != 0 && data[0]["share_certificate_to"] != '')
			{
				var no_of_shares = 0;
				no_of_shares = parseFloat(data[0]["share_certificate_to"]) - parseFloat(data[0]["share_certificate_from"]);
				no_of_shares = no_of_shares + 1;
				document.getElementById('no_of_shares').value = no_of_shares;
			}
			AddNewRow();
			if(data[1].length > 0)
			{
				document.getElementById('witness_name_1').value = data[1][0]['witness_name_one'];
				document.getElementById('witness_address_1').value = data[1][0]['witness_add_one'];
				document.getElementById('witness_name_2').value = data[1][0]['witness_name_two'];
				document.getElementById('witness_address_2').value = data[1][0]['witness_add_two'];
					
				if(data[2].length > 0)
				{
					var Cnt = 1;
					for(var i = 0; i < data[2].length; i++)
					{							
						document.getElementById('Nominee_Name' + Cnt).value = data[2][i]['nominee_name'];
						document.getElementById('Nominee_Address' + Cnt).value = data[2][i]['nominee_address'];
						document.getElementById('Relation' + Cnt).value = data[2][i]['relation'];
						document.getElementById('Share' + Cnt).value = data[2][i]['percentage_share'];
						document.getElementById('DOB' + Cnt).value = data[2][i]['DOB'];
						if(data[2][i]['is_minor'] == 1)
						{
							document.getElementById('is_minor' + Cnt).checked = true;
							document.getElementById('guardian_name' + Cnt).readOnly = false;
							document.getElementById('guardian_name' + Cnt).style.backgroundColor = 'White';
							document.getElementById('guardian_name' + Cnt).value = data[2][i]['guardian_name'];
						}							
						if(data[2].length == Cnt)
						{
							//alert("End");
						}
						else
						{
							AddNewRow();
						}
						Cnt++;
					}						
				}
			}
		}
		});
	}
	
	function delete_nomination(nomination_id)
	{
		$.ajax({
		url : "ajax/ajaxdocument_maker.ajax.php",
		type : "POST",
		dataType : "json",
		data: {"method": "delete_nomination", "nomination_id":nomination_id},
		success: function(data)
		{
			var page_action = "delete";
			//alert("Temp_Type: " + document.getElementById('temp_type').value);
			document.cookie = 'TempType=' + document.getElementById('temp_type').value;
			document.cookie = 'page_action=' + page_action;
			location.reload();
		}
		});
	}
	
	function goto_notice()
	{
		var temp_id = document.getElementById('temp_type').value;
		var ckeditor_data = CKEDITOR.instances.events_desc.getSnapshot();
		
		$.ajax({
		url : "ajax/ajaxdocument_maker.ajax.php",
		type : "POST",
		dataType : "json",
		data: {"method": "fetch_subject", "temp_id":temp_id},
		success: function(data)
		{
			var subject = data;

			if(temp_id == 27) //overdue payment
			{
				var notice_to = document.getElementById('member').value;
				window.location = "addnotice.php?module=2&tempid=27&sub=" + subject + "&unitid=" + notice_to + "&date=&ckeditor=" + ckeditor_data;
			}
			else if(temp_id == 25) //agm
			{	
				var date = document.getElementById('meeting_date').value;
				window.location = "addnotice.php?module=2&tempid=25&sub=" + subject + "&unitid=0&date=" + date + "&ckeditor=" + ckeditor_data;
			}
			else if(temp_id == 28 || temp_id == 29) // fine and reverse charge resp.
			{
				var rc_id = document.getElementById('member').value;
			
				$.ajax({
				url : "ajax/ajaxdocument_maker.ajax.php",
				type : "POST",
				dataType : "json",
				data: {"method": "fetch_unit_id", "rc_id":rc_id},
				success: function(data)
				{
					//alert(data);
					var unit_id = data;
					if(temp_id == 28) //fine
					{
						window.location = "addnotice.php?module=2&tempid=28&sub=" + subject + "&unitid=" + unit_id + "&date=&ckeditor=" + ckeditor_data;
					}
					else if(temp_id == 29) //reverse charge
					{
						window.location = "addnotice.php?module=2&tempid=29&sub=" + subject + "&unitid=" + unit_id + "&date=&ckeditor=" + ckeditor_data;
					}
				}	
				});
			}
		}
		});
	}
	
	var iCounter = 1;
	function AddNewRow()
	{
		if(iCounter <= 5)
		{
			var sContent = "<tr id='NomineeRow" + iCounter+"'><td width='20%'><input type='text' id='Nominee_Name" + iCounter + "' name='Nominee_Name" + iCounter + "' style='width:150px;' /></td>";
				sContent += "<td width='30%'><textarea id='Nominee_Address" + iCounter + "' name='Nominee_Address" + iCounter + "' style='width:280px;' maxlength='85'></textarea></td>";				
				sContent += "<td width='10%'><input type='text' id='Relation" + iCounter + "' name='Relation" + iCounter + "' style='width:80px;' /></td>";			
				sContent += "<td width='5%'><input type='text' id='Share" + iCounter + "' name='Share" + iCounter + "' style='width:80px;' onBlur='check_total();' onkeypress='return IsNumeric(event);' ondrop='return false;' onpaste='return false;' /></td>";			
				sContent += "<td width='10%'><input type='text' id='DOB" + iCounter + "' name='DOB" + iCounter + "' class='basics' style='width:80px;' /></td>";		
				sContent += "<td width='5%'><input type='checkbox' id='is_minor" + iCounter + "' name='is_minor" + iCounter + "' onChange='if_minor();' /></td>";
				sContent += "<td width='20%' id='guardian_td'><input type='text' id='guardian_name" + iCounter + "' name='guardian_name" + iCounter + "' style='width:150px;background-color:#666' readonly /></td></tr>";			
				
				$("#nominee_info > tbody").append(sContent);
				
				setDatePicker('DOB' + iCounter);	
				iCounter = iCounter + 1;	
				document.getElementById('maxrows').value = iCounter;
			
		}
		else
		{
			<?php
			if(!isset($_COOKIE))
			{
			?>
				alert("Cannot add more than 5 nominees.");
			<?php
			}
			?>
		}
	}
	
	function check_total()
	{
		//alert('Coming here?');
		var maxrows = document.getElementById('maxrows').value;
		var total = 0;
		//alert(maxrows);
		for(var i = 1; i < maxrows; i++)
		{
			var value = document.getElementById('Share' + i).value;
			total += parseFloat(value);
		}
		//alert(total);
		if(total < 100)
		{
			document.getElementById("check_total").innerHTML = "";
			document.getElementById("check_total").innerHTML='<b>The total must be 100. The total is ' + total + '.</b>';
			document.getElementById("check_total").style.backgroundColor = '#F70D1A';
		}
		else if(total == 100)
		{
			document.getElementById("check_total").innerHTML = "";
		}
		else if(total > 100)
		{
			document.getElementById("check_total").innerHTML = "";
			document.getElementById("check_total").innerHTML='<b>The total cannot be greater than 100. The total is ' + total + '.</b>';
			document.getElementById("check_total").style.backgroundColor = '#F70D1A';
		}
	}
	
	function if_minor()
	{
		var maxrows = document.getElementById('maxrows').value;
		//alert(maxrows);
		for(var i = 1; i < maxrows; i++)
		{
			var check_box = document.getElementById('is_minor' + i);
			if(check_box.checked)
			{
				document.getElementById('guardian_name' + i).readOnly = false;
				document.getElementById('guardian_name' + i).style.backgroundColor = "#FFF";
			}
			else if(!check_box.checked)
			{
				document.getElementById('guardian_name' + i).readOnly = true;
				document.getElementById('guardian_name' + i).style.backgroundColor = "#666";
			}
		}	
	}
	
	var specialKeys = new Array();
    specialKeys.push(8); //Backspace
    function IsNumeric(e)
	{
		var keyCode = e.which ? e.which : e.keyCode
        var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) != -1);
        return ret;
    }
	
	function setDatePicker(fieldName)
	{
		$(function() {
			$('#' + fieldName).datepicker({
				dateFormat: "dd-mm-yy",
				//minDate: minGlobalCurrentYearStartDate,
				//maxDate: maxGlobalCurrentYearEndDate
				yearRange: '-100:',
				});
			});
	}
	</script>

    <!--<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
	<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
    <script type="text/javascript" src="javascript/jquery.clockpick.1.2.4.js"></script>
    <script type="text/javascript" src="javascript/ui.core.js"></script>
    <script type="text/javascript" src="javascript/ui.datepicker_event.js"></script>-->
    <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
    <script type="text/javascript">
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true, 
			yearRange: '-10:+10', // Range of years to display in drop-down,
        })});
		
	</script>
            
	<script type="text/javascript">
		$(document).ready(function() 
		{
			var temp_id= '';
			
			<?php 
			if(isset($_REQUEST['tempid']))
			{
			?>
				temp_id = '<?php echo $_REQUEST['tempid'];  ?>';
				if(temp_id == 25) //agm notice
				{
					document.getElementById('temp_type').options[1].selected = true;
					document.getElementById('AGM').style.display = "block";
				}
				else if(temp_id == 27) //overdue payment
				{
					document.getElementById('temp_type').options[2].selected = true;
					document.getElementById('show_member').style.display = "block";
					document.getElementById("member").innerHTML = "<?php echo $combo_unit = $obj_templates->combobox07("select u.unit_id, CONCAT(CONCAT(u.unit_no,' '), mm.owner_name) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.society_id = '" . $_SESSION['society_id'] . "' and ownership_status = 1 ORDER BY u.sort_order");?>";
				}
				else if(temp_id == 28) //debit(charge/fine)
				{
					document.getElementById('temp_type').options[3].selected = true;
					document.getElementById('show_member').style.display = "block";
					document.getElementById("member").innerHTML = "<?php echo $combo_unit = $obj_templates->combobox07("select rs.ID, CONCAT(CONCAT(CONCAT(u.unit_no,' '), mm.owner_name),' - ',rs.Amount) AS 'unit_no' from `unit` AS u, `member_main` AS mm, `reversal_credits` AS rs where u.unit_id = mm.unit and u.unit_id = rs.UnitID and u.society_id = '".$_SESSION['society_id']."' and ownership_status = 1 and rs.ChargeType = 2 ORDER BY u.sort_order");?>";
				}
				else if(temp_id == 29) //credit(refund)
				{
					document.getElementById('temp_type').options[4].selected = true;
					document.getElementById('show_member').style.display = "block";
					document.getElementById("member").innerHTML = "<?php echo $combo_unit = $obj_templates->combobox07("select rs.ID, CONCAT(CONCAT(CONCAT(u.unit_no,' '), mm.owner_name),' - ',rs.Amount) AS 'unit_no' from `unit` AS u, `member_main` AS mm, `reversal_credits` AS rs where u.unit_id = mm.unit and u.unit_id = rs.UnitID and u.society_id = '".$_SESSION['society_id']."' and ownership_status = 1 and rs.ChargeType = 1 ORDER BY u.sort_order");?>";
				}
				else if(temp_id == 37) //online access blocked
				{
					document.getElementById('temp_type').options[10].selected = true;
					document.getElementById('show_member').style.display = "block";
					document.getElementById("member").innerHTML = "<?php echo $combo_unit = $obj_templates->combobox07("select u.unit_id, CONCAT(CONCAT(u.unit_no,' - '), mm.owner_name) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.society_id = '" . $_SESSION['society_id'] . "' and ownership_status = 1 and u.block_unit = 1 ORDER BY u.sort_order","0"); ?>";
				}
				else if(temp_id == 38) //online access restored
				{
					document.getElementById('temp_type').options[11].selected = true;
					document.getElementById('show_member').style.display = "block";
					document.getElementById("member").innerHTML = "<?php echo $combo_unit = $obj_templates->combobox07("select u.unit_id, CONCAT(CONCAT(u.unit_no,' - '), mm.owner_name) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.society_id = '" . $_SESSION['society_id'] . "' and ownership_status = 1 and u.block_unit = 0 ORDER BY u.sort_order","0"); ?>";
				}
			<?php
			}
			?>			
			
			<?php
			if(isset($_COOKIE['TempType']) && $_COOKIE['TempType'] <> 0 && isset($_COOKIE['page_action']))
			{
				$action = $_COOKIE['page_action'];
				if($action == 'delete')
				{
					?>
						//alert(<?php echo $_COOKIE['TempType']; ?>);
						document.getElementById('temp_type').value = '<?php echo $_COOKIE['TempType']; ?>';
						$("#temp_type").trigger("change");
				
						document.cookie = 'TempType=0';
						document.cookie = 'page_action=0';
					<?php
				}
				else if($action == 'edit')
				{
					?>
						document.getElementById('temp_type').value = '<?php echo $_COOKIE['TempType']; ?>';
						var nomination_id = '<?php echo $_COOKIE['NominationID']; ?>';
						$("#temp_type").trigger("change");
						//$('#Edit_' + nomination_id).trigger("click");
						display_nominations(nomination_id, false);
				
						document.cookie = 'TempType=0';
						document.cookie = 'NominationID=0';
						document.cookie = 'page_action=0';

					<?php
				}
			}
			?>
			
		});
		function validate()
		{
			var result = true;
			var temp = "<?php echo $_REQUEST['temp'];?>";
			if(temp == "<?php echo $_SESSION['RENOVATION_DOC_ID']?>")
			{
				var startDate = document.getElementById('startDate').value;
				if(startDate == "")
				{
					alert ("Please provide Work Start Date.");
					document.getElementById('startDate').focus();
					result = false;
				}
				var endDate = document.getElementById('endDate').value;
				if(endDate == "")
				{
					alert ("Please provide Work End Date.");
					document.getElementById('endDate').focus();
					result = false;
				}
				var checkedValue = null; 
				var inputElements = document.getElementsByClassName('chkWorkType');
				var NoOfCheckedChkbox = 0;
				var userfile="",id="",i=0;
				for(i=0; inputElements[i]; i++)
				{
      				if(i!=0 && i%2 != 0)
					{
						//console.log("inputElements[i].checked : "+inputElements[i].checked);
						if(inputElements[i].checked)
						{
           					NoOfCheckedChkbox = NoOfCheckedChkbox + 1;
						
							id = "userfile_"+(i+1);
							//console.log("i : "+(i+1));
							userfile = $("#userfile_"+(i+1)).val();
							//console.log(userfile);
							if(userfile == "")
							{
								alert ("Please provide drawing file for "+inputElements[i].value+".");
								result = false;
							}
						}
       	 			}
				}
				var location = document.getElementById('location').value;
				//alert (location);
				if(location == "")
				{
					alert ("Please select location.");
					document.getElementById('location').focus();
					result = false;
				}
				var MaxNoOfLabourer = document.getElementById('MaxNoOfLabourer').value;
				if(MaxNoOfLabourer == "")
				{	
					alert ("Please provide Maximum numbr of labourer.");
					document.getElementById('location').focus();
					result = false;
				}
				var contractorAddress = document.getElementById('contractorAddress').value;
				if(contractorAddress == "")
				{
					alert ("Please provide Contractor Address.");	
					document.getElementById('contractorAddress').focus();
					result = false;
				}
				//var workDetails = document.getElementById('renovation_text').innerHTML;
				/*if(workDetails == "")
				{
					alert ("Please provide Work Details.");
					document.getElementById('renovation_text').focus();
					return false;
				}*/
				var contractorName = document.getElementById('contractorName').value;
				if(contractorName == "")
				{
					alert ("Please provide Contractor Name.");
					document.getElementById('contractorName').focus();
					result = false;	
				}
			}
			else if(temp == "<?php echo $_SESSION['ADDRESS_PROOF_ID'];?>")
			{
				//alert("in else");
				var stayingSince = document.getElementById("stayingSince").value;
				if(stayingSince == "")
				{
					alert("Please select staying since date.");
					document.getElementById('stayingSince').focus();
					result = false;
				}
				var purpose = document.getElementById("purpose").value;
				if(purpose == "")
				{
					alert("Please select purpose.");
					document.getElementById('purpose').focus();
					result = false;
				}
				var memberList = document.getElementsByClassName("chkMemberId");
				//console.log("MemberList :",memberList);
				//console.log("memberList :",memberList.length);
				var memberIdList = [];
				for(var i = 0;i < memberList.length; i++)
				{
					//alert(memberList[i].value);
					if(memberList[i].checked)
					{
						memberIdList.push(memerList[i].value);
					}
				}
				//console.log("memberIdList :",memberIdList);
				if(memberIdList.length == 0)
				{
					alert("Please select at least one member to apply for NOC.");
					result = false;
				}
			}
			return (result);
		}
    </script>
     
 <style>
 table, td
 {
	 text-align:center;
 }
 </style>
</head>

<body>
<div id="middle">

<br>
<div class="panel panel-info" id="panel" style="display:none;margin-top:10px;margin-left:3.5%;width:90%;">
<div class="panel-heading" id="pageheader">Document Maker</div>
<br>
<input type="hidden" name="unitId" id="unitId" value = "0"/>

<center>
<form name="templates" id="templates" method="POST" action="process/document_maker.process.php" enctype="multipart/form-data" onSubmit="return validate(); ">
	<input type="text" name="serviceRequestId" id="serviceRequestId" value="<?php if(isset($_REQUEST['srId'])){ echo $_REQUEST['srId'];}else{}?>" style="display:none;"/>
	<table width="100%">
    	<button type="button" class="btn btn-primary" onClick="goToServiceRequest()" style="float:right;margin-right:5%;display:none" id="btnBack" value="BackToRequest">Back to Service Request</button>
        <button type="button" class="btn btn-primary" onClick="goToRenovationReport()" style="float:right;margin-right:5%;display:none" id="btnBackReport" value="btnBackReport">Back to Report</button>
    	<tr id="main_tr_1"> 
        	<td width="50%" style="text-align:right;"><b> Select Template: </b></td>
            <td style="text-align:left" width="50%"><select id="temp_type" name="temp_type" onChange="show_second()" >
			    	<?php if($_SESSION['role'] != ROLE_ADMIN_MEMBER && $_SESSION['role'] != ROLE_MEMBER)
					{ 
						echo $combo_doc = $obj_templates->comboboxRoot("select id, template_name from document_templates where id != 1 and visible_to_member ='0'",'0');
					}
					else if($_SESSION['role'] == ROLE_ADMIN_MEMBER || $_SESSION['role'] == ROLE_MEMBER)
					{
						echo $combo_doc = $obj_templates->comboboxRoot("select id, template_name from document_templates where visible_to_member ='1'",'0');
					}
						?>
			    </select>
            </td>
        </tr>

		<tr>
        	<td colspan="2"><br></td>
        </tr>
        <tr id="main_tr_2">
			<td colspan="2">
				<div id="AGM" style="display:none;">
					<table width="100%" style="border:1px solid black;">
						<tr>
							<td><br></td>
						</tr>
                        <tr>
							<td colspan="4"><b>Please fill in the following details to generate AGM notice:</b></td>
						</tr>
						<tr>
							<td><br></td>
						</tr>
						<!--<tr>
							<td width="50%" style="text-align:right"><b>Date of the meeting:</b></td>
							<td style="text-align:left"><input type="text" name="meeting_date" id="meeting_date" class="basics" style="width:80px;"/></td>
						</tr>-->
                        
						<tr>
                        	<td width="25%" style="text-align:right"><b><font style="color:#F00">*</font>Date of the meeting:</b></td>
							<td style="text-align:left" width="25%"><input type="text" name="meeting_date" id="meeting_date" class="basics" style="width:80px;"/></td>
							<td style="text-align:right" width="25%"><b><font style="color:#F00">*</font>Time of the meeting:</b></td>
							<td style="text-align:left" width="25%">
								<select name="hr" id="hr" style="width:50px;">
									<option value="">HH</option>
										<?php for($i=1;$i<=12;$i++)
										{
											if(strlen($i)==1)
											{
												echo "<option value=0".$i.">0".$i."</option>";
											}
											else
											{	
												echo "<option value=".$i.">".$i."</option>";
											}
										}	
										?>
								</select>
           
								<select name="mn" id="mn" style="width:50px;">
									<option value="">MM</option>
										<?php for($ii=0;$ii<=59;$ii++)
										{
											if(strlen($ii)==1)
											{
												echo "<option value=0".$ii.">0".$ii."</option>";
											}
											else
											{
												echo "<option value=".$ii.">".$ii."</option>";
											}
										}
										?>
								</select>
            
								<select name="ampm" id="ampm" style="width:50px;">
									<option value="AM">AM</option>
									<option value="PM">PM</option>
								</select>
							</td>
						</tr>
						<tr>
							<td><br></td>
						</tr>
                        <tr>
							<td style="text-align:right"><b><font style="color:#F00">*</font>Venue of the meeting:</b></td>
							<td style="text-align:left"><textarea id="venue" name="venue" style="resize:none; width:180px" ></textarea></td>
                            <td style="text-align:right"><b><font style="color:#F00">*</font>Last AGM's date:</b></td>
							<td style="text-align:left"><input type="text" name="last_date" id="last_date" class="basics" style="width:80px;"/></td>
						</tr>
                        <tr>
							<td><br></td>
						</tr>
                        <tr>
                        	<td style="text-align:right" colspan="2"><input type="checkbox" id="society_cb" name="society_cb" /></td>
				            <td style="text-align:left" colspan="2"><b>Society Name as Letter Head?</b></td>
                        </tr>
                        <tr>
							<td><br></td>
						</tr>
						<tr>
							<td colspan="4"><center><input type="button" id="generate" name="generate" value="Generate" class="btn btn-primary" onClick="show_temp_and_fetch_data()" hidden="true"></center></td>
						</tr>
                        <tr>
							<td><br></td>
						</tr>
					</table>
				</div>
			</td>
        </tr>
          
        <!--<tr id="main_tr_10">
			<td colspan="2">
				<div id="show_for_overdue_payment" style="display:none">
					<table width="100%">
						<tr>
							<td style="text-align:right"><input type="checkbox" id="society_cb" name="society_cb" /></td>
				            <td style="text-align:left"><b>Society Name as Letter Head?</b></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>-->  
                        
		<tr id="main_tr_3">
			<td colspan="2" style="padding-left:40%">
				<div id="show_member" style="display:none;text-align:left;">
					<table <?php if($_SESSION['role'] == "Member"){?> width = "50%" <?php } else {?> width = "53%" <?php }?>>
						<tr>
							<td width="50%" style="text-align:right"><b>Select Member:</b></td>
							<td style="text-align:left">
								<select name="member" id="member" class="dropdown" onChange="show_temp_and_fetch_data()" >
									<?php /*?><?php echo $combo_unit = $obj_templates->combobox07("select u.unit_id, CONCAT(CONCAT(u.unit_no,' '), mm.owner_name) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.society_id = '" . $_SESSION['society_id'] . "' and ownership_status = 1 ORDER BY u.sort_order ");
									?><?php */?>
								</select>           	
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
        <tr>
			<td><br></td>
		</tr>
        <tr id="main_tr_4">
			<td colspan="2">
				<div id="dom_pass" style="display:none;">
					<table width="100%" style="border:1px solid black;">
						<tr>
							<td><br></td>
						</tr>
                        <tr>
							<td colspan="4" id="msg_dom_pass"></td>
						</tr>
						<tr>
							<td><br></td>
						</tr>						
						<tr>
                        	<td width="25%" style="text-align:right"><b><font style="color:#F00">*</font>Applier's Name:</b></td>
							<td style="text-align:left" width="25%"><input type="text" name="applier_name" id="applier_name" style="width:80px;" /></td>
							<td style="text-align:right" width="25%"><b><font style="color:#F00">*</font>Flat Owner's Name:</b></td>
							<td style="text-align:left" width="25%"><input type="text" name="owner_name" id="owner_name" style="width:80px;"/></td>
						</tr>
						<tr>
							<td><br></td>
						</tr>
                        <tr>
							<td style="text-align:right"><b><font style="color:#F00">*</font>Relation with Owner:</b></td>
							<td style="text-align:left"><input type="text" name="relation_with_owner" id="relation_with_owner" style="width:80px;"/></td>
                            <td style="text-align:right"><b><font style="color:#F00">*</font>Staying in society since:</b></td>
							<td style="text-align:left"><input type="text" name="start_date" id="start_date" class="basics" style="width:80px;"/></td>
						</tr>
                        <tr>
							<td><br></td>
						</tr>
                        <tr>
							<td colspan="2" style="text-align:right"><b><font style="color:#F00">*</font>Address:</b></td>
							<td colspan="2" style="text-align:left"><textarea id="address" name="address" ></textarea></td>           				</tr>
						<tr>
							<td><br></td>
						</tr>
                        <tr>
							<td colspan="4"><center><input type="button" id="generate_for_pass_dom" name="generate_for_pass_dom" value="Generate" class="btn btn-primary" onClick="show_temp_and_fetch_data()" ></center></td>
						</tr>
                        <tr>
							<td><br></td>
						</tr>
					</table>
				</div>
			</td>
        </tr>
        
        <tr id="main_tr_5">
			<td colspan="2">
				<div id="bank_noc" style="display:none;">
					<table width="100%" style="border:1px solid black;">
						<tr>
							<td><br></td>
						</tr>
                        <tr>
							<td colspan="6"><b>Please fill in the following details to generate Bank NOC:</b></td>
						</tr>
						<tr>
							<td><br></td>
						</tr>						
						<tr>
                        	<td width="15%" style="text-align:right"><b><font style="color:#F00">*</font>Bank Manager's Name:</b></td>
							<td style="text-align:left" width="15%"><input type="text" name="bank_manager_name" id="bank_manager_name" style="width:110px;"/></td>
							<td style="text-align:right" width="15%"><b><font style="color:#F00">*</font>Bank Name:</b></td>
							<td style="text-align:left" width="15%"><input type="text" name="bank_name" id="bank_name" style="width:110px;"/></td>
                            <td style="text-align:right" width="15%"><b><font style="color:#F00">*</font>Bank Address:</b></td>
							<td style="text-align:left" width="30%"><textarea id="bank_address" name="bank_address" style="width:120px" ></textarea></td>
						</tr>
						<tr>
							<td><br></td>
						</tr>
                        <tr>
							<td style="text-align:right"><b><font style="color:#F00">*</font>Flat No:</b></td>
							<td style="text-align:left"><input type="text" name="flat_no" id="flat_no" style="width:110px;"/></td>
                            <td style="text-align:right"><b><font style="color:#F00">*</font>Flat Owner Name:</b></td>
							<td style="text-align:left"><input type="text" name="flat_owner_name" id="flat_owner_name" style="width:110px;"/></td>
                            <td style="text-align:right"><b><font style="color:#F00">*</font>Flat Cost:</b></td>
							<td style="text-align:left"><input type="text" name="flat_cost" id="flat_cost" style="width:110px;"/></td>
						</tr>
                        <tr>
							<td><br><br></td>
						</tr>
                        <tr>
                        	<td style="text-align:right"><b><font style="color:#F00">*</font>Commencement Certificate No.:</b></td>
                            <td style="text-align:left"><input type="text" name="cc_no" id="cc_no" style="width:110px;" /></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td style="text-align:right"><b><font style="color:#F00">*</font>Commencement Certificate Date:</b></td>
                            <td style="text-align:left"><input type="text" name="cc_date" id="cc_date" style="width:80px;" class="basics" /></td>
                        </tr>
                        <tr>
							<td><br></td>
						</tr>
                        <tr>
							<td colspan="6"><center><input type="button" id="generate_for_bank_noc" name="generate_for_bank_noc" value="Generate" class="btn btn-primary" onClick="show_temp()" ></center></td>
						</tr>
                        <tr>
							<td><br></td>
						</tr>
					</table>
				</div>
			</td>
        </tr>        
        
        <tr id="main_tr_6">
			<td colspan="2">
				<div id="leave_and_license_noc" style="display:none;">
					<table width="100%" style="border:1px solid black;">
						<tr>
							<td><br></td>
						</tr>
                        <tr>
							<td colspan="4"><b>Please fill in the following details to generate Leave and License NOC:</b></td>
						</tr>
						<tr>
							<td><br></td>
						</tr>						
						<tr>
                        	<td width="25%" style="text-align:right"><b><font style="color:#F00">*</font>Flat Owner Name:</b></td>
							<td style="text-align:left" width="25%"><input type="text" name="flat_owner_name_lnl" id="flat_owner_name_lnl" style="width:110px;"/></td>
							<td style="text-align:right" width="25%"><b><font style="color:#F00">*</font>Gender:</b></td>
							<td style="text-align:left"><input type="radio" name="gender" id="female" value="Female" >Female</input>&nbsp; &nbsp;
                            <input type="radio" id="male" name="gender" value="Male" >Male</input>
                            </td>                            
						</tr>
						<tr>
							<td><br></td>
						</tr>
                        <tr>
							<td style="text-align:right"><b><font style="color:#F00">*</font>Flat No:</b></td>
							<td style="text-align:left"><input type="text" name="flat_no_lnl" id="flat_no_lnl" style="width:110px;"/></td>
                            <td style="text-align:right"><b><font style="color:#F00">*</font>Tenant Name:</b></td>
							<td style="text-align:left"><input type="text" name="tenant_name" id="tenant_name" style="width:110px;"/></td>                            
						</tr>
                        <tr>
							<td><br></td>
						</tr>                        
                        <tr>
							<td colspan="6"><center><input type="button" id="generate_for_lnl_noc" name="generate_for_lnl_noc" value="Generate" class="btn btn-primary" onClick="show_temp()" ></center></td>
						</tr>
                        <tr>
							<td><br></td>
						</tr>
					</table>
				</div>
			</td>
        </tr>
        <tr id="main_tr_7">
			<td colspan="2">
				<div id="electric_meter_noc" style="display:none;">
					<table width="100%" style="border:1px solid black;">
						<tr>
							<td><br></td>
						</tr>
                        <tr>
							<td colspan="4"><b>Please fill in the following details to generate Transfer of Electric Meter NOC:</b></td>
						</tr>
						<tr>
							<td><br></td>
						</tr>						
						<tr>
                        	<td width="25%" style="text-align:right"><b><font style="color:#F00">*</font>Flat Owner Name:</b></td>
							<td style="text-align:left" width="25%"><input type="text" name="flat_owner_name_em" id="flat_owner_name_em" style="width:110px;"/></td>
							<td style="text-align:right" width="25%"><b><font style="color:#F00">*</font>Gender:</b></td>
							<td style="text-align:left"><input type="radio" name="gender" id="female_em" value="Female" >Female</input>&nbsp; &nbsp;
                            <input type="radio" id="male_em" name="gender" value="Male" >Male</input>
                            </td>                            
						</tr>
						<tr>
							<td><br></td>
						</tr>
                        <tr>
							<td style="text-align:right"><b><font style="color:#F00">*</font>Flat No:</b></td>
							<td style="text-align:left"><input type="text" name="flat_no_em" id="flat_no_em" style="width:110px;"/></td>
                            <td style="text-align:right"><b><font style="color:#F00">*</font>Owner Address:</b></td>
							<td style="text-align:left"><textarea name="owner_address" id="owner_address" ></textarea></td>                            
						</tr>
                        <tr>
							<td><br></td>
						</tr>
                        <tr>
                        	<td style="text-align:right" colspan="2"><b><font style="color:#F00">*</font>Current Electric Meter Name:</b></td>
							<td style="text-align:left" colspan="2"><input type="text" name="current_elec_name" id="current_elec_name" style="width:110px;"/></td>
                        </tr>  
                        <tr>
							<td><br></td>
						</tr>                 
                        <tr>
							<td colspan="4"><center><input type="button" id="generate_for_em_noc" name="generate_for_em_noc" value="Generate" class="btn btn-primary" onClick="show_temp_and_fetch_data()" ></center></td>
						</tr>
                        <tr>
							<td><br></td>
						</tr>
					</table>
				</div>
			</td>
        </tr>
        
        <tr>
        	<td colspan="2">
            	<div id="prev_nomination_info" style="display:none" align="center" >
                </div>
            </td>
        </tr>
        
        <tr>
        	<td><br></td>
        </tr>
        
        <!--<tr style="display:none" id="show_button_for_nomination">
        	<td colspan="2"><input type="button" id="show_nomination" name="show_nomination" value="Show Nomination Form" class="btn btn-primary" onClick="display_nominations();" /></td>
        </tr>-->
        
        <tr id="main_tr_8">
			<td colspan="2">
				<div id="nomination_form" style="display:none;" >
					<table width="100%" style="border:1px solid black;">
						<tr>
							<td><br></td>
						</tr>
                        <tr>
							<td colspan="6"><b>Please fill in the following details to generate Nomination Form:</b></td>
						</tr>
						<tr>
							<td><input type="hidden" id="nomination_id" name="nomination_id" /><br></td>
						</tr>						
						<tr>
                        	<td width="15%" style="text-align:right"><b><font style="color:#F00">*</font>Owner Name:</b></td>
							<td style="text-align:left" width="25%"><input type="text" name="owner_name_nomi" id="owner_name_nomi" style="width:220px;"/></td>
							<td style="text-align:right" width="15%"><b><font style="color:#F00">*</font>Flat No.:</b></td>
							<td style="text-align:left" width="15%"><input type="text" name="flat_no_nomi" id="flat_no_nomi" style="width:110px;"/></td>
                            <td style="text-align:right" width="15%"><b><font style="color:#F00">*</font>Total Area:</b></td>
							<td style="text-align:left" width="15%"><input type="text" name="total_area" id="total_area" style="width:110px;"/></td>
						</tr>
						<tr>
							<td><br></td>
						</tr>
                        <tr>
							<td style="text-align:right"><b><font style="color:#F00">*</font>Share Certificate<br> Serial No.:</b></td>
							<td style="text-align:left"><input type="text" name="sc_serial_no" id="sc_serial_no" style="width:110px;"/></td>
                            <td style="text-align:right"><b><font style="color:#F00">*</font>Share Certificate<br> Issue Date:</b></td>
							<td style="text-align:left"><input type="text" name="sc_issue_date" id="sc_issue_date" style="width:90px;" class="basics"/></td>
                            <td style="text-align:right"><b><font style="color:#F00">*</font>Amount per Share:</b></td>
							<td style="text-align:left"><input type="text" name="amt_per_share" id="amt_per_share" style="width:110px;" onKeyPress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" /></td>
						</tr>
                        <tr>
							<td><br></td>
						</tr>
                        <tr>                        	
                            <td style="text-align:right"><b><font style="color:#F00">*</font>Start No.:</b></td>
                            <td style="text-align:left"><input type="text" name="start_no" id="start_no" style="width:110px;" onKeyPress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" /></td>
                            <td style="text-align:right"><b><font style="color:#F00">*</font>End No.:</b></td>
                            <td style="text-align:left"><input type="text" name="end_no" id="end_no" style="width:110px;" onKeyPress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" /></td>
                            <td style="text-align:right"><b><font style="color:#F00">*</font>No. of Shares:</b></td>
                            <td style="text-align:left"><input type="text" name="no_of_shares" id="no_of_shares" style="width:110px;" /></td>
                        </tr>                        
                        <tr>
                        	<td><br></td>
                        </tr>
                        
                        <tr id="inside_main_tr_8">
							<td colspan="6">
								<div id="nominee_information" >
									<table id="nominee_info" width="100%" >
                                    	<tr>
                                        	<td width="20%"><b><font style="color:#F00">*</font>Name of the Nominee</b></td>
                                            <td width="30%"><b><font style="color:#F00">*</font>Address of Nominee</b></td>
                                            <td width="10%"><b><font style="color:#F00">*</font>Relationship with Nominator</b></td>
                                            <td width="5%"><b><font style="color:#F00">*</font>Share of Nominee</b></td>
                                            <td width="10%"><b><font style="color:#F00">*</font>Date of Birth</b></td>
                                            <td width="5%"><b>Is Minor?</b></td>
                                            <td width="20%"><b>Guardian's Name</b></td>
                                        </tr>                                                                       
                                    </table>
                                </div>
                            </td>
                        </tr>
                        
                        <tr>
                        	<td><br></td>
                        </tr>
                        
                        <tr>
                        	<td colspan="6" id="check_total"></td>
                        </tr>
                        
                        <tr>
                        	<td colspan="6"><input type="button" id="addnewrow" name="addnewrow" value="Add New Nominee" onClick="AddNewRow()" /></td>
                        </tr>
                        
                        <tr>
                        	<td><br></td>
                        </tr>
                        
                        <tr>
                        	<td style="text-align:right"><b><font style="color:#F00">*</font>Name of 1st Witness:</b></td>
                            <td style="text-align:left" colspan="2"><input type="text" name="witness_name_1" id="witness_name_1" style="width:180px;" /></td>
                            <td style="text-align:right"><b><font style="color:#F00">*</font>Address:</b></td>
                            <td style="text-align:left" colspan="2"><textarea id="witness_address_1" name="witness_address_1" style="width:250px" ></textarea></td>
                        </tr>
                        
                        <tr>
                        	<td><br></td>
                        </tr>
                        
                        <tr>
                        	<td style="text-align:right"><b><font style="color:#F00">*</font>Name of 2nd Witness:</b></td>
                            <td style="text-align:left" colspan="2"><input type="text" name="witness_name_2" id="witness_name_2" style="width:180px;" /></td>
                            <td style="text-align:right"><b><font style="color:#F00">*</font>Address:</b></td>
                            <td style="text-align:left" colspan="2"><textarea id="witness_address_2" name="witness_address_2" style="width:250px" ></textarea></td>
                        </tr>
                        
                        <tr>
                        	<td><br></td>
                        </tr>
                        
                        <tr>
							<td colspan="6"><center><input type="button" id="generate_for_nomination" name="generate_for_nomination" value="Generate" class="btn btn-primary" onClick="show_temp()" ></center></td>
						</tr>
                        <tr>
							<td><br></td>
						</tr>
					</table>
				</div>
			</td>
        </tr>
        <tr id="main_tr_9">
			<td colspan="2">
				<div id="property_tax_cert" style="display:none;">
					<table width="100%" style="border:1px solid black;">
						<tr>
							<td><br></td>
						</tr>
                        <tr>
							<td colspan="4"><b>Please fill in the following details to generate Property Tax Certificate:</b></td>
						</tr>
						<tr>
							<td><br></td>
						</tr>						
						<tr>
                            <td style="text-align:right; width:25%"><font style="color:#F00">*</font>Select Ledger:</td>
                            <td style="text-align:left; width:25%"><select name="ledgers" id="ledgers" class="dropdown" >
									<?php echo $combo_unit = $obj_templates->combobox_for_ptc("SELECT `id`, `ledger_name` FROM `ledger` WHERE `show_in_bill` = '1' AND `society_id` = '".$_SESSION['society_id']."'");
									?>
								</select>
                            </td>
                            <td style="text-align:right; width:25%">Place:</td>
                            <td style="text-align:left; width:25%"><input type="text" name="place" id="place" /></td>
						</tr>
						<tr>
							<td><br></td>
						</tr>
                        <tr>
                        	<td style="text-align:right" colspan="2"><input type="checkbox" id="society_cb" name="society_cb" /></td>
				            <td style="text-align:left" colspan="2"><b>Society Name as Letter Head?</b></td>
                        </tr>
                        <tr>
                        	<td><br></td>
                        </tr>                                         
                        <tr>
							<td colspan="4"><center><input type="button" id="generate_for_ptc" name="generate_for_ptc" value="Generate" class="btn btn-primary" onClick="show_temp()" ></center></td>
						</tr>
                        <tr>
							<td><br></td>
						</tr>
					</table>
				</div>
			</td>
        </tr>
        <tr id="main_tr_10">
			<td colspan="2">
				<div id="renovation_noc" style="display:none;">
                	 <input type="hidden" id = "tempType" name = "tempType"/>
					<table width="100%" style="border:1px solid black;">
                    	<tr>
							<td><br></td>
						</tr>
                        <tr>
							<td colspan="4"><b>Please fill in the following details to generate Renovation Letter:</b></td>
						</tr>                        
						<tr>
							<td><br></td>
						</tr>
                        <!--Vaishali
                        <tr>
                   
                   	<!--<td colspan="2" style="text-align:right" style="display:none"><b>Designated area assigned by society to stack the garbage temporary&nbsp;:&nbsp;</b></td>
                   <td colspan="2" style="text-align:left"  style="display:none"><input type="text" id="garbage_area" name="garbage_area" /></td>
                    
                        </tr>
                        
						<tr>
							<td><br></td>
						</tr>
                         <!--Vaishali-->
                        <tr>
							<td colspan="4" style="text-align:center;vertical-align:middle">
                            	<input type="hidden" id = "renovationUnitId" name = "renovationUnitId"/>
                                <center>
                                <table width="70%">
                                	 <tr>
                            			<td style="text-align:right;width:15%;vertical-align:center;padding-top:1%"><?php echo $star;?>&nbsp;<b>Work Start Date &nbsp;:&nbsp;</b></td>
                            	
                            			<td width="85%" style="text-align:left;padding-top:1%">
                                			<input type="text" name="startDate" id="startDate" class="basics" style="width:20%"/>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $star;?>&nbsp;<b>Work End Date&nbsp;:&nbsp;</b>
                                    		<input type="text" name="endDate" id="endDate" class="basics" style = "width:20%"/> 

                                		</td>
                            		</tr>
                                	<tr>
                                 		<td width="85%" style="text-align:center;padding-top:1%;padding-left:5%"colspan = "2">
                                        	<center>
                                        	<table width="70%">
                                            	<tr>
                                                	<input type = "hidden" id = "sizeOfDoc" name = "sizeOfDoc" value = "<?php echo sizeof($listofWork);?>"/>
                                                	 <td style="text-align:left;"><?php echo $star;?>&nbsp;<b>Work Type</b></td>
                                                     <td width="5%"></td>
                                                     <td style="text-align:left;padding-left:15%"><b>Drawing/Design file</b></td><!--Vaishali-->
                                                </tr>
                                    			<?php for($i = 0;$i<sizeof($listofWork);$i++)
												{
												?>
                                                <tr>
                                                	<!--  <input type = "hidden" name = "doc_name_<?php //echo $i + 1;?>" id = "doc_name_<?php //echo $i + 1;?>" value = "<?php //echo $listofWork[$i]['work'];?>"/>-->
                                                	 <td style="text-align:left;"><input type = "checkbox" name = "workType[]" class="chkWorkType" id = "workType_<?php echo $i+1;?>" value="<?php echo $listofWork[$i]['work'];?>">&nbsp;&nbsp;<b><?php echo $listofWork[$i]['work'];?></b><br></td>
                                                     <td width="5%" style="text-align:right;padding-right:1%"><?php if($listofWork[$i]['drawingReq'] == "Yes"){ echo $star;}else{}?></td>
                                                     <td><input type="file" name="userfile[]" id="userfile_<?php echo ($i+1);?>" multiple></td>
                                                </tr>
												<?php
												}
												?>
                                                
                                           	</table>
                                            </center>
                                       	</td>
                                            
                                    		<!--<input type="text" id="contractorName" placeholder="Contractor Name" style="vertical-align:top;width:30%;"/>-->									</tr>
                                   	 <tr>
                                 		<td style="text-align:right;width:15%;vertical-align:center;padding-top:1%"><?php echo $star;?>&nbsp;<b>Work Location&nbsp;:&nbsp;</td>
                            			<td width="85%" style="text-align:left;padding-top:1%">
                                			<select id="location" name = "location" style="width:20%">
                                    			<option value="">Please Select</option>
                                                <option value="1">Inside</option>
                                                <option value="2">Outside</option>
                                            </select>
                                    	</td>
                              		</tr>
                                    <tr>
                                 		<td style="text-align:right;width:17%;vertical-align:center;padding-top:1%"><?php echo $star;?>&nbsp;<b>Contractor Name &nbsp;:&nbsp;</b></td>
                            			<td width="83%" style="text-align:left;padding-top:1%">
                                			<input type="text" id="contractorName" name="contractorName" placeholder="Contractor Name" style="vertical-align:top;width:30%;"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $star;?>&nbsp;<b>Contact No.&nbsp;:&nbsp;</b>
                                            <input type="text" id="contractorContact" name = "contractorContact" placeholder="Contractor's No." style="vertical-align:top;width:15%;size:10%"/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <?php echo $star;?>&nbsp;<b>Max Labourers&nbsp;:&nbsp;</b><input type="text" id="MaxNoOfLabourer" name = "MaxNoOfLabourer" placeholder="" style="vertical-align:top;width:10%;size:10%"/></td> 
                                            </td> 
                                    		<!--<input type="text" id="contractorName" placeholder="Contractor Name" style="vertical-align:top;width:30%;"/>-->
                              		</tr>
                                    <tr>
                                 		<td style="text-align:left;vertical-align:center;padding-top:1%;" colspan="4"><?php echo $star;?>&nbsp;<b>Contractor Address&nbsp;:&nbsp;</b>      			<textarea id = "contractorAddress" name = "contractorAddress" placeholder="Street No, Street Name, Location" style="width:496px;height:46px;" rows="3"></textarea></td> 
                              		</tr>
                                    <!--<tr>
                            			<td colspan="4" style="text-align:left;vertical-align:center;padding-top:1%;padding-left:3%"><b>List of condition&nbsp;:&nbsp;</b>
                            				<textarea id="renovation_text" name="renovation_text" style="width:496px;height:120px;" rows="8"></textarea>
                                        </td>
                        			</tr>-->
                                    <tr>
                                    	<td style="text-align:right;width:15%;vertical-align:center;padding-top:1%"><b>Labourer Details&nbsp;:&nbsp;</td>
                            			<td width="85%" style="text-align:left;padding-top:1%;padding-left:3%">
                                        	<table width="50%" id="labourerDetails">
                                            	<tr>
                                                	 <td style="text-align:left;width:25%"><b>Sr. No</b></td>
                                                     <td width="5%"></td>
                                                     <td style="text-align:left;padding-left:15%;width:70%"><b>Labourer Name</b></td>
                                                </tr>
                                                <tr>
                                                	 <td style="text-align:left;"><input type="text" id="srNo_1" name="srNo[]" value="1." style="vertical-align:top;width:50%;size:10%"/></td>
                                                     <td width="5%" style="text-align:left"></td>
                                                     <td><input type="text" id="labourerName_1" name="labourerName[]" value="" placeholder="Labourer Name" style="vertical-align:top;width:80%;size:10%"/>
                                                     <button type="button" class="btn btn-primary btn-xs" value="Add" onClick="addLabourer()" style="float: right; ">Add</button>
                                                     </td>
                                                </tr>
                                           	</table>
                                         </td>
                        			</tr>
                            	</table>
                             </center>
                            </td>
						</tr>
                       
                        <tr>
                            <td colspan="4" style="text-align:left;padding-left:16%;"><b>&nbsp;Work Details:&nbsp;</b>
                            <textarea id="renovation_text" name="renovation_text" cols="65" rows="6" style=""></textarea></td>
                        </tr>
                        <script>			
							CKEDITOR.config.extraPlugins = 'justify';
							CKEDITOR.replace('renovation_text', {toolbar: [
         						{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        						{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
   								 ],
								 height: 200,
        						 width: 700,
								 uiColor: '#14B8C4'
						});
						</script>						
						 <tr>
                        	<td><br></td>
                        </tr>  
                                                         
                        <tr>
							<td colspan="4"><center><input type="submit" id="generate_for_renovation" name="generate_for_renovation" value="Submit Request" class="btn btn-primary"></center></td>
						</tr>
                        <tr>
							<td><br></td>
						</tr>
					</table>
				</div>
			</td>
        </tr>
        			
        <tr>
        	<td colspan="2"><br><br></td>
        </tr>       
        <tr id="main_tr_11">
			<td colspan="2">
				<div id="addressProof_noc" style="display:none;">
					<table width="100%" style="border:1px solid black;">
                    	<tr>
							<td><br></td>
						</tr>
                        <tr>
							<td colspan="2"><b>Please fill in the following details to generate Address Proof NOC:</b></td>
						</tr>
						<tr>
							<td><br></td>
						</tr>
                        <tr>
							<td colspan="4" style="text-align:center;vertical-align:middle">
                            	<input type="hidden" id = "addressUnitId" name = "addressUnitId"/>
                                <!--<input type="hidden" id = "tempType" name = "tempType" value = "addressProof"/>-->
                                <center>
                                <table width="70%">
                                	<tr>
                        				<td style="text-align:right"><b>Owner Name&nbsp;:&nbsp;</b></td>
                            			<td style="text-align:left"><?php echo $memberDetails[0]['primary_owner_name'];?></td>
                        			</tr>
                                    <tr>
                                 		<td style="text-align:right;vertical-align:center;padding-top:1%;">&nbsp;<b>Owner Address&nbsp;:&nbsp;</b>      			
                                        </td>
                                        <td style="text-align:left;vertical-align:center;padding-top:1%;"><?php echo $memberAddress;?></td> 
                              		</tr>
                                    <tr>
										<td style="text-align:right;padding-top:1%"><?php echo $star;?>&nbsp;<b>Staying Since &nbsp;:&nbsp;</b><input type="text" name="stayingSince" id="stayingSince" class="basics_Dob" style="width:40%"/></td>
                            			<td style="padding-top:1%"><?php echo $star;?>&nbsp;<b>Purpose&nbsp;:&nbsp;</b>
                                            	<select id="purpose" name = "purpose" style="width:35%">
                                    				<option value="">Please Select</option>
                                                	<option value="1">Domicile Certificate NOC</option>
                                                	<option value="2">Passport NOC</option>
                                               	 	<option value="3">Address Proof NOC</option>
                                            	</select>
                                      	</td> 
                                 	</tr>
                                    <tr>
                                    	<td><br/></td>
                                    </tr>
                                    <tr>
                                    	<td colspan="2" style="text-align:left;padding-left:20%"><b>Select resident for whom you want to apply for NOC&nbsp;:&nbsp;</b></td>
                                    </tr>
                                	<tr>
                                 		<td width="85%" style="text-align:center;padding-top:1%;padding-left:20%;padding-right:20%"colspan = "2">
                                        	<center>
                                        	<table width="50%" class="table table-bordered table-hover table-striped" id="memberDetailsTable">
                                            	<tr>
                                                	<input type = "hidden" id = "noOfMember" name = "noOfMember" value = "<?php echo sizeof($memberDetails);?>"/>													
                                                     <td width="10%"><b>Select All</b><br/><input type = "checkbox" name = "selectAllMem" id = "selectAllMem" onChange="selectAllMember()"/></td>
                                                	 <td style="text-align:center;">&nbsp;<b>Member Name</b></td>
                                                     
                                                     <td style="text-align:center;"><b>Relation</b></td><!--Vaishali-->
                                                </tr>
                                    			<?php for($i = 0;$i<sizeof($memberDetails);$i++)
												{
												?>
                                                <tr>
                                                	<!--  <input type = "hidden" name = "doc_name_<?php //echo $i + 1;?>" id = "doc_name_<?php //echo $i + 1;?>" value = "<?php //echo $listofWork[$i]['work'];?>"/>-->
                                                    <td style="text-align:center;"><input type = "checkbox" name = "memberName[]" class="chkMemberId" id = "<?php echo $memberDetails[$i]['mem_other_family_id'];?>" value="<?php echo $memberDetails[$i]['mem_other_family_id'];?>"></td>
                                                	 <td style="text-align:center;">&nbsp;&nbsp;<?php echo $memberDetails[$i]['other_name'];?><br></td>
                                                	 <td style="text-align:center;"><?php echo $memberDetails[$i]['relation']?></td>
                                                </tr>
												<?php
												}
												?>
                                                
                                           	</table>
                                            </center>
                                       	</td>
                                     </tr>
                                   	 <tr style="padding-left:5%">
                                 		<td style="text-align:center;width:15%;vertical-align:center;padding-top:1%" colspan="2"><b>To add new member <a href="view_member_profile.php?prf&id=<?php echo $memberId;?>">click here</a></td>
                              		</tr>
                                    <tr style="margin-left:10%">
                                 		<td colspan="2" style="margin-left:10%">
                                        	<center>
											<table width="70%">
                                            	<tr style="padding-top:1%">
                                 					<td style="text-align:left;vertical-align:center;padding-top:1%;">&nbsp;<b>Note for Managing Committee&nbsp;:&nbsp;</b>      			
                                        			</td>
                                        			 
                              					</tr>
                                                <tr>
                                                	<td style="text-align:left;vertical-align:center;padding-top:1%;padding-left:5%"><textarea id="addressProof_note" name="addressProof_note" cols="80" rows="4"></textarea></td>
                                                </tr>
                                    		</table>
                                            </center>
                                    	</td>
                                 	</tr>
                            		<tr>
                        				<td><br></td>
                        			</tr>  
                                    <tr>
										<td colspan="4"><center><input type="submit" id="generate_for_addressProof" name="generate_for_addressProof" value="Submit Request" class="btn btn-primary"></center></td>
									</tr>
                        			<tr>
										<td><br></td>
									</tr>
								</table>
                        	</td>
                     	</tr>
                	</table>
				</div>
			</td>
        </tr>
        <tr style="display:none" id="show_for_agm">
        	<td colspan="2" style="color:#F00">*Please fill the issues in the curly braces below.</td>
        </tr>
        
        <tr style="display:none" id="show_for_ptc">
        	<td colspan="2" style="color:#F00">*Records shown below are for the default year set. Please change the default year for other records.</td>
        </tr>  
        
        <tr id="society_cb_tr" style="display:none;">
        	<td style="text-align:right" width="50%"><input type="checkbox" id="society_cb_add_remove" name="society_cb_add_remove" onChange="add_remove_society_header();" /></td>
            <td style="text-align:left"  width="50%"><b>Society Name as Letter Head?</b></td>
        </tr>
        
        <tr>
        	<td colspan="2"><br><br></td>
        </tr>                    
		<tr id = "renovation_document" style="display:none;border:1px solid #ddd;">
        	<td id = "renovation_document_td" style = "text-align:left;padding-left:5%">
            	
            </td>
            <td id = "verifyTd" style = "text-align:right;padding-right:5%">
            	<?php 
				if($_REQUEST['action'] == "verify")
				{
				?>
            		<input type="button" name="verifyBtn" id="verifyBtn" value="Verify Request" class="btn btn-primary" style="color:#FFF; width:150px; background-color:#337ab7;" onClick="updateRenovationRequest()">
                 
                <?php 	
				}
				else if($_REQUEST['action'] == "approve")
				{
				?>
                	<input type="button" name="approveBtn" id="approveBtn" value="Approve Request" class="btn btn-primary" style="color:#FFF; width:150px; background-color:#337ab7;" onClick="updateRenovationRequest()">
                <?php 	
				}				
				?>
                <br/>
            </td>
        </tr>
        <tr id="doc_temp" style="display:none;">
			<td colspan="2" width="100%"><b>Document: </b></td>
		</tr>
        
        <tr id="doc_temp_2" style="display:none;">
        	<td colspan="2" width="100%"><textarea name="events_desc" id="events_desc" rows="5" cols="50" style="text-align:center; margin-left:40%;" readonly></textarea></td>
        </tr>

        <script>
			//CKEDITOR.config.height = 100;
			//CKEDITOR.config.width = 500;
			CKEDITOR.config.extraPlugins = 'justify';
			CKEDITOR.replace('events_desc', {toolbar: [
         						{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        						{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] },
								{ name: 'insert', items: [ 'Table' ] },
								{ name: 'insert_2', items: [ 'PageBreak' ] }
   								 ],
								 height: "300",
        						 width: "100%",
								 uiColor: '#14B8C4'
								 });
		</script>
		
        <tr>
        	<td colspan="2"><br><br></td>
        </tr>
        
        <tr style="display:none" id="show_for_nomination">        	
            <td style="width:50%;display:none" ><b>Print form in Triplicate?</b><input type="checkbox" value="Print form in Triplicate?" id="triplicate" name="triplicate" onChange="check_triplicate();" />
            </td>
            <td style="width:50%" colspan="2"><b>Ready to submit the form in triplicate to Managing Committee</b><input type="checkbox" value="Submit the form?" id="submit_form" name="submit_form" onChange="submit_nomination_form();" />
            </td>
        </tr>
        
        <tr>
        	<td colspan="2"><br><br></td>
        </tr>
        
		<tr>
        	<td align="center" id="create_button" style="display:none">
            <input type="button" name="insert" id="insert" value="Print!" class="btn btn-primary" style="color:#FFF; width:100px; background-color:#337ab7;" onClick="for_print()" >
            </td>
            <td id="notice_button" align="center" style="display:none;" colspan="0" >
            <input type="button" name="to_notice" id="to_notice" value="Post as Notice" class="btn btn-primary" style="color:#FFF; width:130px; background-color:#337ab7; height:26px; vertical-align:middle; padding:0 !important" onClick="goto_notice()" >
            </td>
         </tr>
         
         <tr>
        	<td colspan="2"><br></td>
         </tr>
        
</table>

<div id="for_printing" style="display:none"></div>
<input type="hidden" name="maxrows" id="maxrows" />
</form>
</div>
</div>
</center>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>

<?php include_once "includes/foot.php"; ?>
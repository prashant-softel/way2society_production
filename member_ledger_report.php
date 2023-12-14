<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Member Ledger Report</title>
</head>
<?php 
include_once "ses_set_as.php"; 
include_once("classes/dbconst.class.php");
include "classes/include/fetch_data.php";
include_once "classes/utility.class.php";
$objFetchData = new FetchData($m_dbConn);
$m_objUtility = new utility($m_dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
$objFetchData->GetSocietyDetails($objFetchData->GetSocietyID($_SESSION["unit_id"]));
$BillingCycle=$objFetchData->objSocietyDetails->sSocietyBillingCycle;
//$CompareAmount = $m_objUtility->compareBillAmount($_REQUEST['uid']);

if(isset($_REQUEST['Cluster']))
{
	$DecodeUrl = urldecode($_REQUEST['Cluster']);
	$DecodeJSON = json_decode($DecodeUrl);
	$UnitArray = $DecodeJSON;
	$UnitArraystring = implode(",", $UnitArray);
}
if(!isset($_COOKIE["BillType"] ))
{
  
   $_COOKIE["BillType"] = 2;
}
?>

<?php
include_once "classes/unit_report.class.php";
$obj_view_unit_report = new unit_report($m_dbConn);

$AryUnitToDisplay = array();
if($_REQUEST["uid"] <> 0)
{
	array_push($AryUnitToDisplay, $_REQUEST["uid"]);
}
else
{
	if(!isset($_REQUEST['Cluster']))
	{
		$UnitArray = $obj_view_unit_report->getAllUnits();
	}
	$AryUnitToDisplay = $UnitArray;
}

if (isset($_REQUEST['from']) && isset($_REQUEST['to']) && !empty($_REQUEST['from']) && !empty($_REQUEST['to'])) {

	$startDate = $_REQUEST['from'];
	$endDate   = $_REQUEST['to'];
} else {

	$startDate = $_SESSION['default_year_start_date'];
	$endDate   = $_SESSION['default_year_end_date'];
}

$societyCreationDate = $m_objUtility->getSocietyCreatedOpeningDate();

$startDate = getDisplayFormatDate($startDate);
$endDate = getDisplayFormatDate($endDate);
$currentDate = date('d-m-Y');

$minDate = getDisplayFormatDate($societyCreationDate);
$maxDate = getDisplayFormatDate($m_objUtility->getMaxDate());

?>

<html>
<head>

<style>
/*@media print{@page {size: landscape}}*/
	table {
    	border-collapse: collapse;
		text-align:center;
	}
	table, th, td {
   		border: 0px solid black;
		text-align:center;
		padding-top:0px;
		padding-bottom:0px;
	}	
	
	@media print
	{    
		.no-print, .no-print *
		{
			display: none !important;
		}
	}
	td{vertical-align:middle}
.tooltip2 {
    position: relative;
    border-bottom: 1px dotted black;
	opacity: inherit;
}

.tooltip2 .tooltiptext {
    visibility: hidden;
    width: 155px;
    background-color: #fff;
    color: black;
    text-align: center;
    border-radius: 6px;
    padding: 5px 0;

    /* Position the tooltip */
    position: absolute;
    z-index: 1;
	border: 1px solid black;
    left: 14px;
    top: 22px;
}

.tooltip2:hover .tooltiptext {
    visibility: visible;
}
		.date-field {
			height: 30px;
			border-radius: 3px;
			width: 25%;
			text-align: center;
			border: 1px solid;
			margin: 1px;
		}

		.date-td{
			text-align: right;
		}

		.unit-controller {
			padding-left: 40%;
		}
</style>

	<link rel="stylesheet" type="text/css" href="bower_components/font-awesome/css/font-awesome.min.css">
    <script type="text/javascript" src="js/jsReportPdfNEmail20210611.js"></script>
    <!--<script type="text/javascript" src="js/ajax.js"></script>-->
	<script type="text/javascript" language="javascript" src="js/jquery-2.0.3.min.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script language="javascript" type="application/javascript">
	//alert("test");
	var unitstring;
	var jUnitArray;
	var jUnitIDNoArray = [];
	var CurrentUnit;
	var a; 
	var currentLocation = window.location.href;
	unitstring = "<?php echo "".$UnitArraystring.""; ?>";
		var onLoadStartDate = "<?=$startDate?>";
		var onLoadEndDate = "<?=$endDate?>";
	if (unitstring != null || unitstring != '')
	{
		jUnitArray = unitstring.split(',');
		CurrentUnit = "<?php echo $_REQUEST['uid']?>";
		a = jUnitArray.indexOf(CurrentUnit); 
		//alert("indexofval:" + a); 
	}
	
		var minDate = '<?=$minDate?>';
		var maxDate = '<?=$maxDate?>';
		$(function() {
			$.datepicker.setDefaults($.datepicker.regional['']);
			$(".basics").datepicker({
				dateFormat: "dd-mm-yy",
				showOn: "both",
				buttonImage: "images/calendar.gif",
				buttonImageOnly: true,
				minDate: minDate,
				maxDate: maxDate,
				changeMonth: true,
				changeYear: true,
			})
		});
	
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
	
	function ButtonStatus()
	{
		if(a == '0')
		{
			document.getElementById('prev').style.visibility = 'hidden';
		}
		else if(jUnitArray.length > 1)
		{
			document.getElementById('prev').style.visibility = 'visible';
		}
		
		if(a == jUnitArray.length - 1)
		{
			document.getElementById('next').style.visibility = 'hidden';	
		}
		else  if(jUnitArray.length > 1)
		{
			document.getElementById('next').style.visibility = 'visible';	
		}		
		
	}
	function Prev()
	{
		//window.location.href = 'member_ledger_report.php?&uid=' + jUnitArray[a - 1] ;
		if(CurrentUnit != 0)
		{
			if (unitstring != null || unitstring != '')
			{
				if(document.getElementById("chkRemember").checked ==  false)
				{
					document.cookie = 'BillType=2';
				}
				currentLocation = currentLocation.replace('uid='+jUnitArray[a], 'uid='+jUnitArray[a - 1]);
				window.location.href = currentLocation;
			}
		}
		else
		{
			ShowSingle();
		}
		//ButtonStatus();
	}
	
	function Next()
	{
		if(CurrentUnit != 0)
		{
			if (unitstring != null || unitstring != '')
			{
				if(document.getElementById("chkRemember").checked ==  false)
				{
					document.cookie = 'BillType=2';
				}
				currentLocation = currentLocation.replace('uid='+jUnitArray[a], 'uid='+jUnitArray[a + 1]);
				window.location.href = currentLocation;		
			}
		}
		else
		{
			ShowSingle();
		}
	}
	
	function ShowAll()
	{
		currentLocation = currentLocation.replace('uid='+jUnitArray[a], 'uid='+0);
		window.location.href = currentLocation;		
	}
	
	function ShowSingle()
	{
		currentLocation = currentLocation.replace('uid='+0, 'uid='+jUnitArray[0]);
		window.location.href = currentLocation;		
	}
		
	function PrintPage() 
	{
		var btnPrev;
		var btnNext;
		//Get the print button and put it into a variable
		if (unitstring != '')
		{
			btnPrev = document.getElementById("prev");
			btnNext = document.getElementById("next");
		}
		var btnPrint = document.getElementById("Print");
		var btnExportToPDf = document.getElementById("btnExportPdf");
		var btnSendMail = document.getElementById("btnSendMail");
		var btnShowAll = document.getElementById('ShowAll');
		if (unitstring != '')
		{
			btnPrev.style.visibility = 'hidden';
			btnNext.style.visibility = 'hidden';
		}
		btnPrint.style.visibility = 'hidden';
		btnExportToPDf.style.visibility = 'hidden';
		btnSendMail.style.visibility = 'hidden';
		btnShowAll.style.visibility = 'hidden';
		document.getElementById("table_selection").style.visibility = 'hidden';
		//document.getElementById("tooltiptext").style.visibility = 'hidden';
		//document.getElementsByClassName("tooltiptext").style.visibility='hidden';
		//document.getElementById("BillType").style.visibility = 'hidden';
		//document.getElementById("btnGo").style.visibility = 'hidden';
		
		//if(CurrentUnit != 0)
		{
			var btnExport = document.getElementById("btnExport");
			btnExport.style.visibility = 'hidden';
			
		}
		//Print the page content
        window.print();
        //Set the print button to 'visible' again 
		if (unitstring != '')
		{
			btnPrev.style.visibility = 'visible';
			btnNext.style.visibility = 'visible';
		}
		btnPrint.style.visibility = 'visible';
		//if(CurrentUnit != 0)
		{
			var btnExport = document.getElementById("btnExport");
			btnExport.style.visibility = 'visible';
			
		}
		btnExportToPDf.style.visibility = 'visible';
		btnSendMail.style.visibility = 'visible';
		btnShowAll.style.visibility = 'visible';
		document.getElementById("table_selection").style.visibility = 'visible';
		
    }
				
	window.onfocus = function() {
		var result = localStorage.getItem('refreshPage');	
		if(result != null && result > 0 )
		{	
			localStorage.setItem('refreshPage', "0");
			location.reload();
		}
	};
	function RefreshLedgerReport()
	{
			checkStartDate();
		document.cookie = 'BillType='+ document.getElementById("BillType").value ;
			var startDate = document.getElementById('from').value;
			var endDate = document.getElementById('to').value;
			
			if(currentLocation.includes('from') && currentLocation.includes('to')){
				currentLocation = currentLocation.replace(`&from=${onLoadStartDate}&to=${onLoadEndDate}`, `&from=${startDate}&to=${endDate}`);
			}
			else if (currentLocation.indexOf('?') > -1){
				currentLocation += `&from=${startDate}&to=${endDate}`;
			}else{
				currentLocation += `?from=${startDate}&to=${endDate}`;
			}
			window.location.href = currentLocation;
		}
	function ToggleCheckbox()
	{
		var RememberChecked = 0;
		if(document.getElementById("chkRemember").checked)
		{
			RememberChecked = 1;
		}
		document.cookie = 'RememberChecked='+ RememberChecked;
	}
		function checkStartDate(){
			let selectedDate = new Date($('#from').val());
			let minimumDate  = new Date(minDate);
			if(selectedDate.getTime() < minimumDate.getTime()){
				alert(`Date can't be less than ${minDate}`);
				$("#from").val(minDate);
			}
		}
		</script>
    
</head>
<body>
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
else{}
?>
<br>


	<div class="row">
		<span class="unit-controller">
			<?php if (sizeof($UnitArray) > 0 && $_REQUEST['uid'] <> 0) {
			?>
				<input type="button" name="prev" value="&laquo;" size="24px" id="prev" onClick="Prev();" style="width:54px;font-size:36px;" align="middle">
				<input type="button" name="next" value="&raquo;" size="24px" id="next" onClick="Next();" style="width:54px;font-size:36px">
			<?php
			}
			?>
		</span>
		<table style="float:right;" id="table_selection">
			<tr>
				<td class="date-td">
					<label for="from">Date Range:</label>
					<input type="text" name="from" id="from" class="date-field basics" placeholder="Start Date" value="<?= $startDate ?>">
					<input type="text" name="to" id="to" class="date-field basics" placeholder="End Date" value="<?= $endDate ?>">
				</td>
				<td>
					<select id="BillType" name="BillType" style="height:30px;">
						<option value="2" <?php if ($_COOKIE["BillType"] == "2") { ?>selected="selected" <?php } ?>>Combined</option>
						<option value="0" <?php if ($_COOKIE["BillType"] == "0") { ?>selected="selected" <?php } ?>>Maintenance Bills</option>
						<option value="1" <?php if ($_COOKIE["BillType"] == "1") { ?>selected="selected" <?php } ?>>Supplementary Bills</option>
						<option value="3" <?php if ($_COOKIE["BillType"] == "3") { ?>selected="selected" <?php } ?>>Invoice Bills</option>

						<!--New Drop Down Added for Invocie bill And We set the BillType Value as 3 in Cookie-->

					</select>
				</td>
				<td>
					<input type="button" name="btnGo" id="btnGo" value="Go" onClick="RefreshLedgerReport()" style="height:30px" />
				</td>
			</tr>
			<tr>
				<td></td>
				<td colspan="2" style="text-align: left;">
					<input type="checkbox" name="chkRemember" onClick="ToggleCheckbox()" id="chkRemember" <?php if ($_COOKIE["RememberChecked"] == "1") { ?> checked <?php } ?> style="height:10px;transform:scale(2, 2);margin:10px" title="Select checkbox to remember Selection of Bill Report Type for next unit" />Remember
				</td>

			</tr>
		</table>
		<br><br><br>
	</div>
	</div>
		<br>

<div id="Exportdiv" style="border: 1px solid #cccccc;;">
<?php 
$SelectedBillType = $_COOKIE["BillType"];
			for($iCnt = 0; $iCnt < sizeof($AryUnitToDisplay); $iCnt++)
			{
				
				$CompareAmount = $m_objUtility->FetchBillAmountGroupByPeriod($AryUnitToDisplay[$iCnt]);
				$show_due_details = array();
				if($iCnt == 2)
				{
					//break;
				}
				
			if (isset($startDate) && isset($endDate) && $startDate <> "" && $endDate <> "") {
				$show_owner_details = $obj_view_unit_report->show_owner_name($AryUnitToDisplay[$iCnt], getDBFormatDate($endDate));
				$get_details = $obj_view_unit_report->show_due_details($AryUnitToDisplay[$iCnt], getDBFormatDate($startDate), getDBFormatDate($endDate));
			} else {
				$show_owner_details = $obj_view_unit_report->show_owner_name($AryUnitToDisplay[$iCnt]);
				$get_details = $obj_view_unit_report->show_due_details($AryUnitToDisplay[$iCnt]);
			}
			// $show_due_details = $obj_view_unit_report->show_due_details($AryUnitToDisplay[$iCnt]);

			//$ReverseCredits = $obj_view_unit_report->GetReverseCredit($AryUnitToDisplay[$iCnt],$_SESSION['from_date'],$_SESSION['to_date']);
			if(isset($startDate) && !empty($startDate)){
				$date = getDBFormatDate($startDate);
			}
			else {
				$date = $m_objUtility->getCurrentYearBeginingDate($_SESSION['default_year']);
			}

			$resOpening = $m_objUtility->getOpeningBalance($AryUnitToDisplay[$iCnt], $date);

			$TotalOpeningBalance = $resOpening['Debit'] - $resOpening['Credit'];

			$res = $resOpening;

				$UnitID = $AryUnitToDisplay[$iCnt];
				$resOpeBalSplit = $m_objUtility->getInceptionOpeningBalanceSplit($UnitID);

				// echo "<pre>";
				// print_r($resOpeBalSplit);
				// echo "</pre>";
				//echo "<BR>Opening Bal split Arrary<BR>";
				$MaintBillOpeningBalance = $resOpeBalSplit[0]['TotalBillPayable'];
				$SuppBillOpeningBalance = $resOpeBalSplit[0]['supp_TotalBillPayable'];
				
				$InvoiceBillOpeningBalance = $resOpeBalSplit[0]['InvTotalBillPayable'];
				
				//Fetching Invoice Opening Balance

				$resMaintenanceWithoutOpBal = $m_objUtility->getOpeningBalance_ForBillType($AryUnitToDisplay[$iCnt], $date, Maintenance);
//				echo "<BR>New Maintenance WithoutOpBal Bill<BR>";
				// echo "<pre>";
				// print_r($resMaintenanceWithoutOpBal);
				// echo "</pre>";
				$resSupplemetaryWithoutOpBal = $m_objUtility->getOpeningBalance_ForBillType($AryUnitToDisplay[$iCnt], $date, Supplementry);
				
				$resInvoiceWithoutOpBal = $m_objUtility->getOpeningBalance_ForBillType($AryUnitToDisplay[$iCnt], $date, Invoice);
				
				// New Invoice WithoutOpbal 
				
				$ShowDebugTraces = 0;
				if($ShowDebugTraces)
				{
					echo "Opening bal for combine<BR>" ;
					print_r($resOpening);
					echo "<BR><BR>Current year Opening Balance :" . $TotalOpeningBalance ;
					echo "<BR>";
	
					echo "<BR>Opening Bal split Arrary<BR>";
					print_r($resOpeBalSplit);
					echo "<BR>MaintBillOpening Balance " . $MaintBillOpeningBalance;
					echo "<BR>SuppBillOpeningBalance " . $SuppBillOpeningBalance. "<BR>";
	
					echo "<BR>New Maintenance WithoutOpBal Bill<BR>";
					print_r($resMaintenanceWithoutOpBal);
	
					echo "<BR><BR>Supp Bill WithoutOpBal Arrary <BR>";
					print_r($resSupplemetaryWithoutOpBal);
				}


				if($res <> "")
				{
						$show_due_details[0] = array("Date" => $res['OpeningDate'] , "Particular" => $res['LedgerName'],"SubTotal"=>($res['BillSubTotal']),"IntrestOnArreas"=>($res['BillInterest']),"Debit" => ($res['OpeningType'] == TRANSACTION_DEBIT) ? $res['Total'] : 0 , "Credit" => ($res['OpeningType'] == TRANSACTION_CREDIT) ? $res['Total'] : 0 ,"PrincipalArrears"=>($res['PrincipalArrears']),"Intrest"=>($res['InterestArrears']),"VoucherID" => 0 ,"VoucherID" => 0 , "VoucherTypeID" => 0);
					
					if($get_details <> "")
					{
						for($i = 0 ; $i < sizeof($get_details); $i++)
						{
							$show_due_details[$i + 1] = $get_details[$i];
						}
				}
			}

			?>  
				
<div style="border: 1px solid #cccccc;;width:100%;" id="unit_<?php echo $AryUnitToDisplay[$iCnt] . '_' . $show_owner_details[0]['unit_no']; ?>">
		<script>
			jUnitIDNoArray.push("unit_<?php echo $AryUnitToDisplay[$iCnt] . '_' . $show_owner_details[0]['unit_no']; ?>");
		</script>
        <div id="bill_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:18px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
            <!--<div id="society_type" style="font-weight:bold; font-size:20px;">PREMISES CO-OPERATIVE SOCIETY LTD.</div>-->
            <div id="society_reg" style="font-size:14px;"><?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?></div>
            <div id="society_address"; style="font-size:14px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>
        </div>
        <div id="bill_subheader" style="text-align:center;">
            <div style="font-weight:bold; font-size:16px;" id="divTitle">Member Ledger Report 
			<?php if($SelectedBillType == 2 ) 
					{ 
						echo "(Combined - Maintenance and Supplementary Bills)";
					}
					else if($SelectedBillType == 0 ) 
					{
						echo "(Maintenance Bills Only)";
					}
					else if($SelectedBillType == 1 ) 
					{
						echo "(Supplementary Bills Only)";
					}
					else if($SelectedBillType == 3 ) 
					{
						echo "(Invoice Bills Only)";
						//Show bill type on header of member ledger Page
					}
					?>
            </div>
            <div style="font-weight; font-size:16px;">As on Date:<?php echo date("d.m.Y");?></div>
         </div>
  		
		<table style="width:100%;font-size:14px; border: 1px solid #cccccc;"  id="tableMemberDetils">
					<tr align="center"><td colspan="5"><font  size="+1"><b><a href="view_member_profile.php?scm&id=<?php echo $show_owner_details[0]['member_id'];?>&tik_id=<?php echo time();?>&m&view" target="_blank"><?php echo $show_owner_details[0]['owner_name'];?></a></b></font></td></tr>
					<tr>
						<td width="50"><b>Wing:</b><?php echo $show_owner_details[0]['wing'];?></td>
						<!--<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>-->
						
						<?php if($_REQUEST['uid'] == 0){ ?>
							<td width="100"><b>Unit No:</b><a href="#" style="color:#0000FF;" onClick="window.open('member_ledger_report.php?&uid=<?php echo $AryUnitToDisplay[$iCnt];?>','popup','type=fullWindow,fullscreen,scrollbars=yes'); return false;"><?php echo $show_owner_details[0]['unit_no'];?></a></td>
							
						<?php }
						else{ ?>
							<td width="100"><b>Unit No:</b><?php echo $show_owner_details[0]['unit_no'];?></td>
						<?php }?>
						
						
						<td><b>Residence No:</b><?php echo $show_owner_details[0]['resd_no'];?></td>
						<!--<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>-->
						<td><b>Mobile No:</b><?php echo $show_owner_details[0]['mob'];?></td>
						
						<!--<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>-->
						
						<td><b>Email ID:</b><?php echo $show_owner_details[0]['email'];?></td>
						
					</tr>
				</table>
 
				<table id="tableID" style="font-size:14px;width:100%;" >
               		<tr >
            			<th style="text-align:center;  border: 1px solid #cccccc;width:6%;">Date</th>
						<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;width:6%;">Voucher</th>
						<!--<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;">Voucher No</th>-->
						<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;width:5%;">ChequeNo<br>/Bill Number</th>
						<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;width:18%;">Particular</th>
                        <th style="text-align:center;  border: 1px solid #cccccc;border-left:none;width:7%;">Sub Total (Rs.)<br>( A )</th>
                        <?php 
                         if($_SESSION['apply_gst']==1)
                         {
                           echo '<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;width:8%;">GST<br>( B )</th>';	
                         }
                         else
                         {
                         	echo '<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;width:8%;">Adjustment Credit /Rebate<br>( B )</th>';
                         }
                        ?>
                        
                        <th style="text-align:center;  border: 1px solid #cccccc;border-left:none;width:8%;">Interest on Arrears (Rs.)<br>( C )</th>
						<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;width:8%;">Debit (Rs.)<br>( A+B+C )</th>
						<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;width:10%;">Credit (Rs.)</th>
                        <!--<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;width:8%;">Principal (Rs.)</th>
                        <th style="text-align:center;  border: 1px solid #cccccc;border-left:none;width:8%;">Interest (Rs.)</th>-->
						<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;width:25%;">Balance (Rs.)</th>
						<!--<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;">Status</th>
						<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;">Remark</th>-->
        		</tr>
       
				<?php
				if($show_due_details<>"")
				{
					
					$ChequeNumber=0;
					$BalanceAmt = 0;
					$TotalBalanceAmt = 0;
				
					$iCtr = 1;	
				foreach($show_due_details as $k => $v)
				{
					$counter =0;
					
					if($iCtr == 1)
					{
						//print_r($show_due_details);
					}
					$iCtr++;
					
					if($show_due_details[$k]['Credit'] <> 0)
					{							
						$show_particulars = $obj_view_unit_report->details2($_REQUEST["uid"],$show_due_details[$k]['VoucherID'],$show_due_details[$k]['VoucherTypeID'], $show_due_details[$k]['Debit'], $show_due_details[$k]['Credit'], "By");
					}
					else
					{
						$show_particulars = $obj_view_unit_report->details2($_REQUEST["uid"],$show_due_details[$k]['VoucherID'],$show_due_details[$k]['VoucherTypeID'], $show_due_details[$k]['Debit'], $show_due_details[$k]['Credit'], "To");
					}
					//fetching sale invoice table details

					///var_dump($show_particulars);
					
					$Sale_Invoice_ID = $show_particulars[0]['RefNo'];
					$GetAllInvoiceNumber = $obj_view_unit_report->getInvoiceNumbers($Sale_Invoice_ID);
						
					if($show_due_details[$k]['VoucherID']==0)
					{	
						//Opening balance calculations
					
						$show_particulars[0]['ledger_name']='Opening Balance';
					}
					//if($show_due_details[$k]['Debit'] > 0 || $show_due_details[$k]['Credit'] > 0)
					{
					//$BillNumber = $show_due_details[$k]['Debit'];															
					$DebitAmt = $show_due_details[$k]['Debit'];
					$CreditAmt = $show_due_details[$k]['Credit'];

					if($show_due_details[$k]['VoucherID']!=0)
					{
						//echo "Voucher:" . $show_due_details[$k]['VoucherID'];
						if( $SelectedBillType == 2)
						{
							
							$TotalBalanceAmt = $TotalBalanceAmt + $DebitAmt - $CreditAmt;
							//print_r($TotalBalanceAmt);
							$BalanceAmt = $BalanceAmt + $DebitAmt - $CreditAmt;
						}
						else if( $SelectedBillType == 0 )
						{
							//Maintenance selected
							
							$TotalBalanceAmt = $TotalBalanceAmt + $DebitAmt - $CreditAmt;
							//print_r($TotalBalanceAmt);
							if( $show_particulars[0]['BillType'] ==  1 || $show_particulars[0]['BillType'] == 2) 
							{
								//echo "<BR>Billtype 1 Skipped<BR>";
								continue;
							}
							/*else if($show_due_details[$k]['VoucherTypeID'] == "2" || $show_due_details[$k]['VoucherTypeID'] == "5")
							{
								//VoucherType 2 returned payment
								$BalanceAmt = $BalanceAmt + $DebitAmt - $CreditAmt;
							}*/
							else
							{
								$BalanceAmt = $BalanceAmt + $DebitAmt - $CreditAmt;
							}
						}
						else if($SelectedBillType == 1)
						{
							//Supplimentary selected
							$TotalBalanceAmt = $TotalBalanceAmt + $DebitAmt - $CreditAmt;
							//print_r($TotalBalanceAmt);
							if($show_particulars[0]['BillType']  ==  0 || $show_particulars[0]['BillType']  ==  2)
							{
								//echo "<BR>Billtype 0 etc Skipped<BR>";
								continue;
							}
							else if($show_due_details[$k]['VoucherTypeID'] == "5")
							{
								//echo "VoucherType 2 and 5 skipped<BR>" . $show_due_details[$k]['VoucherTypeID'] . "<BR>";
								continue;
							}
							else
							{
								$BalanceAmt = $BalanceAmt + $DebitAmt - $CreditAmt;
							}
						}					
					else if($SelectedBillType == 3)
						{	
							//Invoice selected
							$TotalBalanceAmt = $TotalBalanceAmt + $DebitAmt - $CreditAmt;
							//print_r($TotalBalanceAmt);
							if($show_particulars[0]['BillType']  ==  0 || $show_particulars[0]['BillType']  ==  1)
							{
					 
								//echo "<BR>Billtype 0 etc Skipped<BR>";
								continue;
							}
							else if($show_particulars[0]['BillType']  ==  2)
							{
								$BalanceAmt = $BalanceAmt + $DebitAmt - $CreditAmt;
							}
						}
					}	
					else
					{
						//Opening balance calculations
						if( $show_due_details[$k]['VoucherID']==0 && $SelectedBillType == "0" )
						{
							//echo "<BR>Maint bill Opening type :" . $resMaintenanceWithoutOpBal['OpeningType'];
							//var_dump($resMaintenanceWithoutOpBal);
							//var_dump($MaintBillOpeningBalance);
							$TotalBalanceAmt = $TotalBalanceAmt + $DebitAmt - $CreditAmt;
							$CreditAmt = 0;
							$DebitAmt = 0;
							if($resMaintenanceWithoutOpBal['OpeningType'] == "Credit")
							{
								//echo "<BR>Inside credit<BR>";
								 $CreditAmt = $resMaintenanceWithoutOpBal['Total'] - $MaintBillOpeningBalance;
								 if($CreditAmt < 0)
								 {
									 $DebitAmt = $CreditAmt * -1;
									 $CreditAmt = 0;
								 }
							}
							else
							{
								//echo "<BR>Inside debit<BR>";
								 $DebitAmt = $resMaintenanceWithoutOpBal['Total'] + $MaintBillOpeningBalance;
							}
							
							//$TotalBalanceAmt = $TotalBalanceAmt + $DebitAmt - $CreditAmt;
							$BalanceAmt = $BalanceAmt + $DebitAmt - $CreditAmt;
						}
						else if ($show_due_details[$k]['VoucherID']==0 && $SelectedBillType== "1")
						{
							//Supp bill 
							//echo "<BR>Maint bill Opening type :" . $resSupplemetaryWithoutOpBal['OpeningType'];
							$TotalBalanceAmt = $TotalBalanceAmt + $DebitAmt - $CreditAmt;
							$CreditAmt = 0;
							$DebitAmt = 0;
							if($resSupplemetaryWithoutOpBal['OpeningType'] == "Credit")
							{
								 //echo "<BR>Inside credit<BR>";
								 $CreditAmt = $resSupplemetaryWithoutOpBal['Total'] - $SuppBillOpeningBalance;
								 if($CreditAmt < 0)
								 {
									 $DebitAmt = $CreditAmt * -1;
									 $CreditAmt = 0;
								 }
							}
							else
							{
								 $DebitAmt = $resSupplemetaryWithoutOpBal['Total'] + $SuppBillOpeningBalance;
							}
							
							$BalanceAmt = $BalanceAmt + $DebitAmt - $CreditAmt;
						}
						else if ($show_due_details[$k]['VoucherID'] == 0 && $SelectedBillType== "3")
						{
								//**** Calculating Invoice Opening Balance
								
							    $TotalBalanceAmt = $TotalBalanceAmt + $DebitAmt - $CreditAmt;
								$CreditAmt = 0;
								$DebitAmt = 0;
								if($resInvoiceWithoutOpBal['OpeningType'] == "Credit")
								{
									$CreditAmt = $resInvoiceWithoutOpBal['Total'] - $InvoiceBillOpeningBalance; 
								 	if($CreditAmt < 0)
								 	{
									 $DebitAmt = $CreditAmt * -1;
									 $CreditAmt = 0;
									 }
								}
								 else
								 {
									 $DebitAmt = $resInvoiceWithoutOpBal['Total'] + $InvoiceBillOpeningBalance;
								 }
							   $BalanceAmt = $BalanceAmt + $DebitAmt - $CreditAmt;
							
						}
						else //$SelectedBillType== "2") Combine
						{
							$TotalBalanceAmt = $TotalBalanceAmt + $DebitAmt - $CreditAmt;
							$BalanceAmt = $BalanceAmt + $DebitAmt - $CreditAmt;							
						}
					}
					//print_r($DebitAmt);
					if ($show_particulars[0]['Return']==1)
					{
						$ParticularName = $show_particulars[0]['PayerBank'] ;
					}
					else
					{
						//echo '<br>Payment Particular';
						//echo '<br>Utility Result : '.$m_objUtility->getCategoryID($show_particulars[0]['ledger_name']);
						if($m_objUtility->getCategoryID($show_particulars[0]['ledger_name']) == DUE_FROM_MEMBERS)
						{
							$ParticularName = $show_particulars[0]['ledger_name']."-".$m_objUtility->getMemberName($show_particulars[0]['ledger_name']);	
						}
						else if($show_particulars[0]['RefTableID'] == TABLE_CHEQUE_DETAILS)
						{
							$ParticularName = $show_particulars[0]['ledger_name']." [".$m_objUtility->returnBillTypeString($show_particulars[0]['BillType'])." Bill ] ". "[".$show_particulars[0]['Status']."]";
						}
						else
						{
							
							$ParticularName = $show_particulars[0]['ledger_name'];							
						}
						
						//$ParticularName=$show_particulars[0]['ledger_name'];
					}
					if($show_particulars[0]['DepositGrp']==-2 && $show_due_details[$k]['VoucherTypeID'] <> 1  )
					{
						$ChequeNumber = "NEFT [TRXN:".$show_particulars[0]['ChequeNumber']."]";
					}
					else if($show_particulars[0]['Return']==1 )
					{
						$ChequeNumber = $show_particulars[0]['ChequeNumber'];	
					}
					else if($show_due_details[$k]['VoucherTypeID'] <> 1)
					{
						$ChequeNumber = $show_particulars[0]['ChequeNumber'];
					}
					
					if($show_particulars[0]['voucher_name']=="Sales Voucher")
					{
						$strBillType = "";
						
						if($show_particulars[0]["BillType"] == 0)
						{
							$strBillType = "Maintenance";
						}
						else
						{
							$strBillType = "Supplementary";
						}
						 
							
						$ParticularName= $strBillType." Bill [". $show_particulars[0]['billFor']. "]";
						$ChequeNumber = $show_particulars[0]['BillNumber'];
						
					}
					if($show_particulars[0]['Return']==1)
					{
						$filtered_Voucher_name="Return";
					}
					else
					{
						//This is to check whether is Invoice and set the voucher name because in database  Invoice are not set
						if($show_particulars[0]['voucher_name'] == 'Journal Voucher' && $GetAllInvoiceNumber[0]['InvSubTotal'] <> 0 && $DebitAmt <> 0)
						{
							$filtered_Voucher_name = 'Invoice';
						}
						else 
						{
							$filtered_Voucher_name = str_replace('Voucher', '', $show_particulars[0]['voucher_name']);
						}
						
					}
				?>
			   
				<tr>
					<td style="border: 1px solid #cccccc;text-align:center;"><?php echo getDisplayFormatDate($show_due_details[$k]['Date']);?></td>
					<td style="border: 1px solid #cccccc;border-left:none;text-align:center;"><?php echo $filtered_Voucher_name;?></td>
					<!--<td style="border: 1px solid #cccccc;border-left:none;text-align:center;width:10%; "><?php //echo $show_particulars[0]['VoucherNo'];?></td>-->
					<?php if($show_particulars[0]['voucher_name']=="Journal Voucher" && $GetAllInvoiceNumber[0]['InvSubTotal'] <> 0)
					{?>
					<td style="border: 1px solid #cccccc;border-left:none;text-align:center;font-size:12px; "><?php echo INVOICE_BILL.' - '.$GetAllInvoiceNumber[0]['Inv_Number'];?></td>	
					<?php } 
					else{?>
                    <td style="border: 1px solid #cccccc;border-left:none;text-align:center;font-size:12px; "><?php echo $ChequeNumber;
					
							if($show_particulars[0]['PayerBank'] <> "")
							{
								$bankdetails = "[". $show_particulars[0]['PayerBank'];
								if($show_particulars[0]['PayerChequeBranch'] <> "") 
								{ 
									$bankdetails .= ", ".$show_particulars[0]['PayerChequeBranch'] ;
								} 
								
								$bankdetails .= "]" ;	
							}
							//echo $bankdetails;?></td><?php }?>
					<td style="border: 1px solid #cccccc;border-left:none;text-align:left;"><?php echo $ParticularName;//$show_particulars[0]['ledger_name'];?></td>	
                    <?php if($show_particulars[0]['voucher_name'] == "Sales Voucher")
				 	{	
					?>
                    <td style="border: 1px solid #cccccc;border-left:none;text-align:right;"><?php echo number_format($show_due_details[$k]['BillSubTotal'],2)?></td>
                   <td style="border: 1px solid #cccccc;border-left:none;text-align:right;">
                   	<?php 
                   	if($_SESSION['apply_gst']==1)
                   	{   
                   		$total_gst=$show_due_details[$k]['CGST']+$show_due_details[$k]['SGST'];
                   		echo number_format($total_gst,2);
                   	}
                   	else
                   	{
                   		echo number_format($show_due_details[$k]['AdjustmentCredit'],2);
                   	}

                   	?></td>
                   <td style="border: 1px solid #cccccc;border-left:none;text-align:right;"><?php echo number_format($show_due_details[$k]['BillInterest'],2)?></td>
					<td style="border: 1px solid #cccccc;text-align:right;padding-right: 3px;">
                    <?php }
					else if($show_particulars[0]['voucher_name']=="Journal Voucher")
					{ 
						$invoice_gst = 0;
						if($_SESSION['apply_gst']==1)
                   		{
                   			$invoice_gst = $show_due_details[$k]['invoice_cgst'] +  $show_due_details[$k]['invoice_sgst'] + $show_due_details[$k]['invoice_round_off_amount'];
                   		}
						
						?>
                            
                   <td style="border: 1px solid #cccccc;border-left:none;text-align:right;"><?php echo number_format($GetAllInvoiceNumber[0]['InvSubTotal'],2);?></td>
                  <td style="border: 1px solid #cccccc;border-left:none;text-align:right;"><?php echo number_format($invoice_gst,2)?></td>
                   <td style="border: 1px solid #cccccc;border-left:none;text-align:right;">0.00<?php //echo number_format($show_due_details[$k]['BillInterest'],2)?></td>
					<td style="border: 1px solid #cccccc;text-align:right;padding-right: 3px;">
                    <?php }
					else {?>
						<td style="border: 1px solid #cccccc;border-left:none;text-align:right;padding-right: 3px;">0.00<?php //echo number_format($show_due_details[$k]['BillSubTotal'],2)?></td>
                     <td style="border: 1px solid #cccccc;border-left:none;text-align:right;padding-right: 3px;">0.00 <?php //echo $show_due_details[$k]['AdjustmentCredit']?></td>
                    <td style="border: 1px solid #cccccc;border-left:none;text-align:right;padding-right: 3px;">0.00<?php //echo $show_due_details[$k]['BillInterest'];?></td>
					<td style="border: 1px solid #cccccc;text-align:right;padding-right: 3px;">
						<?php }?>
					<?php
						
						$getInvoiceNumber=$_GET['inv_number'];	
									
						if($show_particulars[0]['voucher_name'] == 'Sales Voucher')
						{					
							echo "<a  href='Maintenance_bill.php?UnitID=".$AryUnitToDisplay[$iCnt]."&PeriodID=". $show_particulars[0]['PeriodID']."&BT=".$show_particulars[0]["BillType"]."' target='_blank'>".number_format($DebitAmt,2)."</a>";
						}
						else if($show_particulars[0]['voucher_name'] == 'Journal Voucher' && $GetAllInvoiceNumber[0]['InvSubTotal'] <> 0 && $DebitAmt <> 0)
						{
							 //This URL for Sale Invoice 
								echo "<a href='Invoice.php?UnitID=". $AryUnitToDisplay[$iCnt]."&id=".$Sale_Invoice_ID."&inv_number=".$GetAllInvoiceNumber[0]['Inv_Number']."' target='_blank'>".number_format($DebitAmt,2). "</a>";
							
						}
						else if($show_particulars[0]['voucher_name'] == 'Journal Voucher' && $GetAllInvoiceNumber[0]['InvSubTotal'] == 0 && $DebitAmt <> 0)
						{
								//This is for Vouchers
								echo "<a href='VoucherEdit.php?Vno=". $show_particulars[0]['VoucherNo']."' target='_blank'>".number_format($DebitAmt,2). "</a>";
						}
						else if($show_particulars[0]['VoucherType'] == VOUCHER_DEBIT_NOTE)
						{
							echo "<a href='Invoice.php?debitcredit_id=". $show_particulars[0]['RefNo']."&UnitID=".$_REQUEST['uid']."&NoteType=".$m_objUtility->GetNoteType($show_particulars[0]['RefNo'])."' target='_blank'>".number_format($DebitAmt,2). "</a>";
						}
						else if($show_particulars[0]['voucher_name'] == 'Payment Voucher')
						{
							echo "<a href='PaymentDetails.php?bankid=".$show_particulars[0]['BankID']."&LeafID=".$show_particulars[0]['ChqLeafID']."&CustomLeaf=".$show_particulars[0]['CustomLeaf']."&edt=".$show_particulars[0]['RefNo']."' target='_blank'>".number_format($DebitAmt,2). "</a>";	
						}
						else
						{
							echo number_format($DebitAmt,2);
						}
					?></td> 
                         
					<td style="border: 1px solid #cccccc;text-align:right;  padding-right: 3px;">
				   <?php if(number_format($CreditAmt,2) > 0 && ($show_particulars[0]['DepositGrp'] == -2 || $show_particulars[0]['DepositGrp'] == -3 || $show_particulars[0]['DepositGrp'] > 0) ) //for pay cash no need of edit link[pay cash = depositID:-1]
						{
							
							if($show_particulars[0]['DepositGrp'] > 0 || $show_particulars[0]['DepositGrp'] == -3) //for receive cash and deposit cheque edit
							{
								$url = "ChequeDetails.php?depositid=".$show_particulars[0]['DepositGrp']."&bankid=".$show_particulars[0]['id']."&edt=".$show_particulars[0]['RefNo'];
								//print_r($show_particulars[0]);						
							}
							else if($show_particulars[0]['DepositGrp'] == -2)// For Neft Transaction edit
							{
								$url = "NeftDetails.php?bankid=".$show_particulars[0]['id']."&edt=".$show_particulars[0]['RefNo'];
							}
							?>
                            
							<!-- <button type="button" id= "popup" style="border-radius:50px; width:15px; color:#009; vertical-align:middle;float: left;margin-left: 65px;" onClick="window.open('ReceiptDetails.php?extra&cycle=<?php echo $BillingCycle;?>&PeriodID=<?php echo  $show_particulars[0]['PeriodID'];?>&unit=<?php echo $AryUnitToDisplay[$iCnt] ?>', '_blank')"><i class="fa   fa-info-circle " style="margin-left: -6px;"></i> </button> -->
							
							<button type="button" id= "popup" style="border-radius:50px; width:15px; color:#009; vertical-align:middle;float: left;margin-left: 65px;" onClick="window.open('ReceiptDetails.php?extra&ChequeDetailsId=<?=$show_particulars[0]['RefNo']?>&unit=<?=$AryUnitToDisplay[$iCnt]?>', '_blank')"><i class="fa   fa-info-circle " style="margin-left: -6px;"></i> </button>
                            <a href="#" onClick="window.open('<?php echo $url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes'); "> <?php echo number_format($CreditAmt,2) ?> </a> 					                                                                           
						
						<?php
						}
						else
						{
							if($show_particulars[0]['voucher_name'] == 'Journal Voucher' && $CreditAmt <> 0)
							{
								echo "<a href='VoucherEdit.php?Vno=". $show_particulars[0]['VoucherNo']."' target='_blank'>".number_format($CreditAmt,2). "</a>";
							}
							else if($show_particulars[0]['VoucherType'] == VOUCHER_CREDIT_NOTE && $CreditAmt <> 0)
							{
								echo "<a href='Invoice.php?debitcredit_id=". $show_particulars[0]['RefNo']."&UnitID=".$_REQUEST['uid']."&NoteType=".$m_objUtility->GetNoteType($show_particulars[0]['RefNo'])."' target='_blank'>".number_format($CreditAmt,2). "</a>";
							}
							else
							{
								echo number_format($CreditAmt,2);
							}
						}
					?></td>
                     <!--<td style="border: 1px solid #cccccc;border-left:none;text-align:left;"><?php //echo $show_due_details[$k]['PrincipalArrears']?></td>   
                      <td style="border: 1px solid #cccccc;border-left:none;text-align:left;"><?php //echo $show_due_details[$k]['InterestArrears']?></td> -->  
                   
					<td style="border: 1px solid #cccccc;border-left:none;text-align:right;" class="tooltip2">
                   <?php 
					if($show_particulars[0]['voucher_name'] == "Sales Voucher")
				 	{	
						$TotalPayableAmount=$CompareAmount[$show_particulars[0]["PeriodID"]][$show_particulars[0]["BillType"]]['TotalBillPayable'];
						
						if(number_format($TotalPayableAmount, 2) != number_format($BalanceAmt, 2))
						{
							if($_COOKIE["BillType"] == "2")
							{?>
                            	<!--<span style="float: left;margin-left: 50%;">
								<i class="fa fa-warning" id="tooltip" style="color:red;font-size:1vw" >
                                <span class="tooltiptext">Ledger Balance <?php //echo "< ".$BalanceAmt." >";?><br> and Total Payable Amount  <?php //echo "< ".$TotalPayableAmount." >"?><br>in the bill do not match</span>
                                </i> 
                                </span>-->
						<?php }
							else if($_COOKIE["BillType"] == "0")
							{?>
                            	<span style="float: left;margin-left: 40%;" id="tool"  class="errorhint">
								<i class="fa fa-warning" id="tooltip" style="color:red;font-size:1vw" >
                                <span class="tooltiptext" id="tool1">Ledger Balance <?php echo "< ".$BalanceAmt." >";?><br> and Total Payable Amount  <?php echo "< ".$TotalPayableAmount." >"?><br>in the bill do not match</span>
                                </i> 
                                </span>
						<?php }
						else if($_COOKIE["BillType"] == "1")
							  {?>
                              	<span style="float: left;margin-left: 40%;" id="tool" class="errorhint">
								<i class="fa fa-warning" id="tooltip" style="color:red;font-size:1vw" >
                                <span class="tooltiptext" id="tool1">Ledger Balance <?php echo "< ".$BalanceAmt." >";?><br> and Total Payable Amount  <?php echo "< ".$TotalPayableAmount." >"?><br>in the bill do not match</span>
                                </i> 
                                </span>
						<?php }
						else if($_COOKIE["BillType"] == "3")
							  {?>
                              	<span style="float: left;margin-left: 40%;" id="tool" class="errorhint">
								<i class="fa fa-warning" id="tooltip" style="color:red;font-size:1vw" >
                                <span class="tooltiptext" id="tool1">Ledger Balance <?php echo "< ".$BalanceAmt." >";?><br> and Total Payable Amount  <?php echo "< ".$TotalPayableAmount." >"?><br>in the bill do not match</span>
                                </i> 
                                </span>
						<?php }
						}
					} ?>	
                   <?php echo number_format($BalanceAmt,2);?> </td>
				</tr>
				 <?php
				
					}
				}
					$BalanceAmtType = ' (Dr)';
					if($BalanceAmt < 0)
					{
						$BalanceAmtType = ' (Cr)';
					}

					$TotalBalanceAmtType = ' (Dr)';
					if($TotalBalanceAmt < 0)
					{
						$TotalBalanceAmtType = ' (Cr)';
					}

					?>
					<?php if($SelectedBillType != 2)
					{
						?>
						<tr>
							<td style="border: 1px solid #cccccc;text-align:left;background-color:#F5F3F3; border-right: none;" colspan="3">
							</td>
							<td style="border: 1px solid #cccccc;text-align:right;background-color:#F5F3F3; border-left: none;" colspan="6">
								<?php 
									if($SelectedBillType == 0 )
									{
										echo 'Total Maintenance Bill Dues (Rs.)';
									}
									else if($SelectedBillType == 1 )
									{
										echo 'Total Supplementary Bill Dues (Rs.)';
									}
									else if($SelectedBillType == 3)
									{
										echo 'Total Invoice Bill Dues (Rs.))';
										//Setting Footer headinng for sale invoice
									}
									else
									{
										echo 'Total Dues (Rs.)';
									}
								?>
							</td>
							<td style="border: 1px solid #cccccc;text-align:right;background-color:#F5F3F3;"><?php echo number_format(abs($BalanceAmt),2).$BalanceAmtType;?>
								
							</td>
						</tr>
						<?php
					}
					?>
					<tr>
						<td style="border: 1px solid #cccccc;text-align:left;background-color:#F5F3F3; border-right: none;" colspan="3">
						</td>
						<td style="border: 1px solid #cccccc;text-align:right;background-color:#F5F3F3; border-left: none;" colspan="6">
							Total Dues (Rs.)
						</td>
						<td style="border: 1px solid #cccccc;text-align:right;background-color:#F5F3F3;"><?php echo number_format(abs($TotalBalanceAmt),2).$TotalBalanceAmtType;?>
							
						</td>
					</tr>
					<?php
				}
				else
				{
					?>
					<!--<tr height="25"><td colspan="6" align="center"><font color="#FF0000"><b>Records Not Found....<!--  by admin --></b></font></td></tr>-->
					<?php	
				}
				?>
				   
		</table>
		
</div>
<div style='page-break-after:always;'>&nbsp;</div>
		<?php
			}
			?>
			
</div>
<!--<a href="#" onClick ="$('#tableID').tableExport({type:'excel',escape:'false'});">XLS</a>
<a href="#" onClick ="$('#tableID').tableExport({type:'csv',escape:'false'});">CSV</a>-->
<!--<input type="button" id="btnExport" value=" Export"  onClick ="$('#tableID').tableExport({type:'excel',escape:'false',tableName:'unitreport'});"/> -->
<br>
<!--<input  type="button" id="btnSendMail" value="Email"  style="width:80px; height:30px; float:right;" onClick="sendEmail(<?php echo $_REQUEST["uid"];?>,'<?php echo $show_owner_details[0]['email'];?>');"/> -->
<input  type="button" id="btnSendMail" value="Email"  style="width:80px; height:30px; float:right;" onClick="ShowTable()" />
<?php
 if($_SESSION['feature']['CLIENT_FEATURE_EXPORT_MODULE'] == 1)
 {
	 if($_REQUEST["uid"] <> 0)
	{
		?>
			<input  type="button" id="btnExport" value=" Export To Excel"  style="width:150px; height:30px; float:right;" />
			<input  type="button" id="btnExportPdf" value=" Export To Pdf"   onClick="ViewPDF('<?php echo $objFetchData->objSocietyDetails->sSocietyCode;?>' , '<?php echo $show_owner_details[0]['unit_no'];?>')" style="width:150px; height:30px; float:right;"/> 
		<?php
	}
	else
	{
		?>
			<input  type="button" id="btnExport" value=" Export To Excel"  style="width:150px; height:30px; float:right;"/>
			<input  type="button" id="btnExportPdf" value=" Export To Pdf"   onClick="ViewAllPDF('<?php echo $objFetchData->objSocietyDetails->sSocietyCode;?>' , '<?php echo $show_owner_details[0]['unit_no'];?>')" style="width:150px; height:30px; float:right;"/> 
		<?php
	}
	?>
	<INPUT TYPE="button" id="Print" onClick="PrintPage()" name="Print!" value="Print" style="width:100px; height:30px;float:right; "/>
<?php    	
}
 if($_REQUEST["uid"] <> 0)
{
	?>
	<input type="button" id="ShowAll" onClick="ShowAll()" name="Show_All_Unit" value="Show All Units" style="width:100px; height:30px;float:right; display:none; "/>	
	<?php
}
else
{
	?>
	<input type="button" id="ShowAll" onClick="ShowSingle()" name="Show_Single_Unit" value="Show Single Unit" style="width:120px; height:30px;float:right; display:none;"/>		
	<?php
}
?>
<a href="Invoice.php?uid=<?php echo $_REQUEST['uid'] ?>&add" target="_blank"><input  type="button" id="btnInvoice" value="Create Invoice"  style="width:80px; height:30px; float:left;" /></a>
<a href="Invoice.php?uid=<?php echo $_REQUEST['uid'] ?>&add_credit&NoteType=3" target="_blank"><input  type="button" id="btnCredit" value="Add Credit"  style="width:80px; height:30px; float:left;" /></a>
<a href="Invoice.php?uid=<?php echo $_REQUEST['uid'] ?>&add_debit&NoteType=4" target="_blank"><input  type="button" id="btnDebit" value="Add Debit"  style="width:80px; height:30px; float:left;" /></a>
<div id="status"></div>

<br>
<br>
<!--<form name="EmailForm" id="EmailForm"  method="post" onSubmit="sendEmail();">-->
<table style="border: 1px solid black; display:none; padding:50px; vertical-align:middle;" id="EmailTable"  class='no-print'>
<input type="hidden" name="UnitID"  id="UnitID" value="<?php echo $_REQUEST["uid"];?>"/>
<?php 
$specialChars = array('/','.', '*', '%', '&', ',', '(', ')', '"');
$unitNoForPdf = str_replace($specialChars,'',$show_owner_details[0]['unit_no']);
?>
<input type="hidden" name="UnitNoForPdf"  id="UnitNoForPdf" value="<?php echo $unitNoForPdf ;?>"/>
<input type="hidden" name="SocietyCode"  id="SocietyCode" value="<?php echo $objFetchData->objSocietyDetails->sSocietyCode;?>"/>
<input type="hidden" name="UnitNo"  id="UnitNo" value="<?php echo $show_owner_details[0]['unit_no'];?>"/>
<!--<input type="hidden" name="EmailID"  id="EmailID" value="<?php //echo $show_owner_details[0]['email'];?>"/>-->
<center>
<tr><td colspan="3">Please Fill Details For Sending Email</td></tr>
<tr><td colspan="3"><br/></td></tr>
<tr>
	<td valign="top" style="text-align:left;padding-left:10px">Email To</td>
 	<td>:</td>
  	<td>
		<?php if($_REQUEST["uid"] <> 0)
		{
			$emailIDList = $objFetchData->GetEmailIDToSendNotification($_REQUEST["uid"]);

			$ListOfEmailID = array();
			for($iCnt = 0; $iCnt < sizeof($emailIDList);  $iCnt++)
			{
				array_push($ListOfEmailID, $emailIDList[$iCnt]['to_email']);
			}
			
			?>
			<input type="text" name="EmailID"  id="EmailID" value="<?php echo implode(';', $ListOfEmailID) ?>" style="width:100%;"  onChange="ValidateEmail(this)" onBlur="ValidateEmail(this)"/></td>
			<?php
		}
		else
		{
			?>
				<input type="text" name="EmailID"  id="EmailID" value="<?php echo $objFetchData->objSocietyDetails->sSocietyEmail;?>" style="width:100%;"  onChange="ValidateEmail(this)" onBlur="ValidateEmail(this)"/></td>
			<?php
		}
		?>
</tr>
<tr>
	<td></td><td></td><td style="color:#999">seperated by ; (Semicolon) e.g.: sample1@example.com;sample2@example.com</td>
</tr>
<tr>
<td>
<br/>
</td>
</tr>

<tr>
	<td valign="top" style="text-align:left;padding-left:10px">CC To </td>
 	<td>:</td>
  	<td><input type="text" name="CC"  id="CC" value="<?php //echo $show_owner_details[0]['email'];?>" style="width:100%;"  onChange="ValidateEmail(this)"  onBlur="ValidateEmail(this)" /></td>
</tr>
<tr>
	<td></td><td></td><td style="color:#999">seperated by ; (Semicolon) e.g.: sample1@example.com;sample2@example.com</td>
</tr>
<tr>
<td>
<br/>
</td>
</tr>
<tr>
	<td valign="top" style="text-align:left;padding-left:10px">Email Subject</td>
 	<td>:</td>
  	<td><input type="text" name="SubjectHead" id="SubjectHead"  value="<?php echo 'Memeber Ledger Report For : ' . $show_owner_details[0]['unit_no']; ?>" style="width:100%;"  required/></td>
</tr>

<tr>
<td>
<br/>
</td>
</tr>
<tr>
    <td valign="top" style="text-align:left;padding-left:10px">Email Message</td>
    <td valign="top"> : </td>
    <td><textarea id="Message" style="width:400px;float:left" name="Message" cols="30" rows="10" type="text" required><?php echo 'Attached Memeber Ledger Report For Unit' . $show_owner_details[0]['unit_no'];?></textarea></td>
</tr>

<tr>
<td>
<br/>
</td>
</tr>
<tr>
	<td>Attached File:<a href="" target='_blank' id="EmailFile" name="EmailFile"><img src='images/pdficon.png' /></a></td>
</tr>
<tr><td colspan="3"><br/></td></tr>
<tr><td colspan="3"><input type="submit" id="SendEmail" name="Submit" value="Submit" onClick="sendEmail();"/></td></tr>
</center>
</table>
<!--</form>-->
</body>
<script>
if (unitstring != null || unitstring != '')
{
	ButtonStatus();
}
$("#btnExport").click(function(e) {
	
	$('.errorhint').remove();
	
	 var myBlob =  new Blob( [$("#Exportdiv").html()] , {type:'application/vnd.ms-excel'});
	 var url = window.URL.createObjectURL(myBlob);
	 
	 var a = document.createElement("a");
	 document.body.appendChild(a);
	 a.href = url;
	 a.download = "Memberledger.xls";
	 a.click();
	//adding some delay in removing the dynamically created link solved the problem in FireFox
	 setTimeout(function() {window.URL.revokeObjectURL(url);},0);
	
	//***Below commented code not working for large file so we using blob 
	//window.open('data:application/vnd.ms-excel,' + encodeURIComponent( $("#Exportdiv").html())); 
	//header('data:application/vnd.ms-excel,' + encodeURIComponent( $("#Exportdiv").html())); 
 	e.preventDefault();
 	location.reload();
  
});

</script>

</html>
 
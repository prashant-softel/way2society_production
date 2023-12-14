
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Invoice Generate</title>
</head>




 <?php 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
include "classes/include/fetch_data.php";
include "common/CommonMethods.php";
include_once("classes/dbconst.class.php");
include_once("classes/genbill.class.php");
include_once("classes/utility.class.php");
include_once("classes/changelog.class.php");
$currentUrl ="";
$BillType ="2";
$bTrace = 0;

//if($_SESSION['is_year_freeze'] <> 0)
	//{
	?>
	<script>
	//alert('Cannot create the invoice beacause year is frozen \n Please select unfreeze  year');
	//window.location.href = "home_s.php";
    </script>

<?php //}
if($_SESSION['default_ledger_round_off'] == 0)
{
?>
<script>
alert('Please first set Ledger Round Off in default setting');
window.location.href = "home_s.php";
</script>
<?php }

if(isset($_REQUEST['e']) && !isset($_REQUEST['UnitID']))
{
	//request file called from email link by member
	//$protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
	$protocol = 'https';
	$host     = $_SERVER['HTTP_HOST'];
	$script   = $_SERVER['SCRIPT_NAME'];
	$params   = $_SERVER['QUERY_STRING'];
	$referer  =  $_SERVER['HTTP_REFERER'];
	
	$currentUrl = $protocol . '://' . $host . $script . '?' . $params;
	
	if(!isset($_SESSION['login_id']))
	{
		header('Location: login.php?url='.$currentUrl);
	 }
	else
	{
		$currentUrl = str_replace('_**_','&',$currentUrl);
		$eVal =  str_replace('_**_','&',$_REQUEST['e']);
		$currentUrl = $protocol . '://' . $host . $script . '?' . $eVal;

		?>
			<script>window.location.href = "<?php echo $currentUrl?>";</script>
		<?php
	}
		
}
$m_objLog = new changeLog($dbConn, $dbConnRoot);
$obj_genbill = new genbill($dbConn,$dbConnRoot);
$objFetchData = new FetchData($dbConn);
$m_objUtility = new utility($dbConn,$dbConnRoot);
$m_objdbRoot = new  dbop(true);

$societyInfo = $m_objUtility->GetSocietyInformation($_SESSION['society_id']);
$CGSTRate = $societyInfo['cgst_tax_rate'];
$SGSTRate = $societyInfo['sgst_tax_rate'];
$DefaultGSTRate = $CGSTRate+$SGSTRate;
//$ClientDetails = $$m_objUtility->FetchClientDetails();
		
//This Note for prefetch from exiting bill register so they don't need to write Note every time

$NoteFromBillRegister = $objFetchData->GetNotes();
$FinalExitingNote = $NoteFromBillRegister[0]['Notes'];
$unitText = $objFetchData->getUnitPresentation($_REQUEST["UnitID"] );


					if(!isset($_REQUEST['NoteType']))
					{
						//**********Fetching Sale Invoice details for requested unitID and Invoice Bill Number
						//$VoucherCounter = $obj_genbill->getInvoiceNumberNext();
						
						$VoucherCounter = $m_objUtility->GetCounter(VOUCHER_INVOICE,0);
						$res02 = $obj_genbill->FetchSaleInvoice($_REQUEST['inv_number'],$_REQUEST['UnitID'],$_REQUEST['id']);
						
						$BillNoWithPrefix = PREFIX_INVOICE_BILL.'-'.$res02[0]["Inv_Number"];
						$FinalNumber = $res02[0]["Inv_Number"];
						$Finalpayable = $res02[0]["TotalPayable"];
						$FinalRegisterID = $res02[0]["ID"];
						$FinalUnitID = $res02[0]["UnitID"];
						$FinalDate = $res02[0]["Inv_Date"];
						$FinalSubTotal = $res02[0]["InvSubTotal"];
						$FinalCGST = $res02[0]["CGST"];
						$FinalSGST = $res02[0]["SGST"];
						$FinalLedgerRoundOff = $res02[0]["Ledger_round_off"];
						$FinalTotalPayable = $res02[0]["TotalPayable"];
						$FinalTaxableLedgers = $res02[0]["TaxableLedgers"];
						$FinalTaxRateOfLedgers = $res02[0]["TaxRate"];
						$FinalNote = $res02[0]["Note"];
					
					}
					else
					{
						//**********Fetching  details for requested unitID and Invoice Bill Number
						
						$VoucherCounter = $m_objUtility->GetCounter($VoucherType,0);
						$res02 = $obj_genbill->FetchDebitCreditDetails($_REQUEST['debitcredit_id']);
						$VoucherType = VOUCHER_CREDIT_NOTE;	
						$BillNoWithPrefix = PREFIX_CREDIT_NOTE.'-'.$res02[0]["Note_No"];
						
						if($_REQUEST['NoteType'] == DEBIT_NOTE)
						{
							$VoucherType = VOUCHER_DEBIT_NOTE;	
							$BillNoWithPrefix = PREFIX_DEBIT_NOTE."-".$res02[0]["Note_No"];
						}
						
						$Finalpayable = $res02[0]["TotalPayable"];
						$FinalRegisterID = $res02[0]["ID"];
						$FinalUnitID = $res02[0]["UnitID"];
						$FinalDate = $res02[0]["Date"];
						$FinalBillType = $res02[0]["BillType"];
						$FinalSubTotal = $res02[0]["Note_Sub_Total"];
						$FinalCGST = $res02[0]["CGST"];
						$FinalSGST = $res02[0]["SGST"];
						$FinalLedgerRoundOff = $res02[0]["Ledger_round_off"];
						$FinalTotalPayable = $res02[0]["TotalPayable"];
						$FinalTaxableLedgers = $res02[0]["TaxableLedgers"];
						$FinalNote = $res02[0]["Note"];
						$FinalNumber = $res02[0]["Note_No"];
						
						if($FinalBillType == Maintenance)
						{
							$BillTypeHead = '[ Maintenance Bill ]';
						}
						else if($FinalBillType == Supplementry)
						{
							$BillTypeHead = '[ Supplementry Bill ]';
						}
						else if($FinalBillType == Invoice)
						{
							$BillTypeHead = '[ Invoice Bill ]';
						}
						
					}
						$BillFont = 12;
						$taxableLedgerCount = 0;
						$GSTChargableLedgers=array();
						$TaxableLedgersArray = array();
						$TaxRateLedgersArray = array();
						
						//*****Storing Taxable ledger of sale Invoice in TaxableLedgersArray
						
						$TaxableLedgersArray = explode(',',$FinalTaxableLedgers);
						$TaxRateLedgersArray = json_decode($FinalTaxRateOfLedgers,true);	
						//var_dump($TaxRateLedgersArray);
					
					
					
$arUnits = array();
$data = array();
$sUnitID = "0";
		
		
		if(!isset($_REQUEST['add_credit']) && !isset($_REQUEST['add_debit']) && !isset($_REQUEST['debitcredit_id']))
		{
			//***Msg will appear when unit ID in Missing in Url 
			if((!isset($_REQUEST["add"]) && $_REQUEST["UnitID"]==''))
			{
				echo "<script>alert('Error ! There is no UnitID found to view a invoice bill');</script>";
				exit;	
			}
		
			else if(isset($_REQUEST['inv_number']) && $_REQUEST['inv_number'] <> $FinalNumber)
			{
				echo 'This Invoice Number Does Not Exits';
				exit;
			}
				
			//***Now we are Fetching details information from Voucher Table for Calculation

			if($_REQUEST["inv_number"]!= 0)
			{
				$data = $objFetchData->GetValuesFromSaleInvoice($FinalRegisterID);
				
				if($bTrace)
				{
					echo "size:".sizeof($data);
				}
				if(sizeof($data) == 0)
				{
					echo "<br><br>Invoice Not Generated For Unit : " . $_REQUEST['UnitID'] . " and Invoice Number : " . $_REQUEST["inv_number"];
					die();
				}
			}
		}
		else
		{
			//echo '<br> Access the register Data for Credit';
			if($_REQUEST["debitcredit_id"]!= 0)
			{
				$data = $objFetchData->GetValuesFromVoucherofDebitCredit($FinalRegisterID,$_REQUEST['NoteType']);
				if($bTrace)
				{
					echo "size:".sizeof($data);
				}
				if(sizeof($data) == 0)
				{
					echo "<br><br> Not Generated For Unit : " . $_REQUEST['UnitID'] . " and Invoice Number : " . $_REQUEST["inv_number"];
					die();
				}
			}
			
		}
		if($_REQUEST["UnitID"] <> "")
		{
			$sUnitID = $_REQUEST["UnitID"];
		}		
		
		
$BillDate = "";
$Id_Array= array();
$DueDate = "";
$BillNumber = "";
$BillNotes = "";
$total = "";
$BillFor_Msg = "";
$bTrace = 0;
if($bTrace)
{
	echo "1";
$BillFor = $objFetchData->GetBillFor($_REQUEST["PeriodID"]);
	echo "2";
}
//$BillFor_Bill = "Debit Note";
if($bTrace)
{
	echo "3";
}

//***Fetching Society Details of Society 
$objFetchData->GetSocietyDetails($_SESSION["society_id"]);
		
if($bTrace)
{
	echo "4";
}
if($sUnitID != "0")
{
	//*** fetching member details for show in invoice bill
	$objFetchData->GetMemberDetails($sUnitID);
}
if($bTrace)
{
	echo "5";
}
//$objectBillRegister = new CBillRegister($dbConn);

if($sUnitID != "0")
{
	$wing_areaDetails = $objFetchData->getWing_AreaDetails($_REQUEST["UnitID"]);
}
if($bTrace)
{
	echo "6";
}
$arSkipLedger = array(IGST_SERVICE_TAX,CGST_SERVICE_TAX,SGST_SERVICE_TAX,CESS_SERVICE_TAX, ROUND_OFF_LEDGER);

$iCounter = 0;
?>
<script> var HeaderAndAmount = new Array();</script>	 
<?php 
if($bTrace)
{
	echo "test8";
}

//***Storing all ledger id in Sequencelly in $ID_Array

if($bTrace)
{
	echo "test09";
}
			

$getSocietyInfo=$m_objUtility->GetSocietyInformation($_SESSION['society_id']);

$bApplyServiceTax = $getSocietyInfo['apply_service_tax'];
$bApplyGSTOnInterest = $getSocietyInfo['apply_GST_on_Interest'];

$iDateDiff = $m_objUtility->getDateDiff(getDBFormatDate($BillDate), GST_START_DATE);
if($iDateDiff < 0)
{
	//$bApplyServiceTax = 0;	
}

$AdjCredit = "0.00";
$InterestOnArrears = "0.00";
$PrinciplePreviousArrears = "0.00";
$IntrestOnPreviousarrears = "0.00";
$BillTax = "0.00";
$CGST = "0.00";
$SGST = "0.00";
$CESS = "0.00";
$sBillDetailNote = "0.00";
if($sUnitID != "0")
{
	$CGST = $FinalCGST;
	$SGST = $FinalSGST;
	$roundOffAmt = $FinalLedgerRoundOff;
}

//**** Setting Rowspan for invoice bill Layout

$value=3;
if(($bApplyServiceTax == 1 || $BillTax > 0 || $IGST > 0 || $CGST >0 || $SGST > 0 || $CESS  > 0))
{
if($bApplyServiceTax == 1 || ($CGST > 0 && $SGST > 0))
{
	$value+=2;
}
if($IGST > 0)
{
	$value++;
}
if($CESS > 0)
{
	$value++;
}

}
if($sUnitID != "0")
{
	//*** Society Details for show in invoice
	
	$showInBillDetails = $objFetchData->GetFieldsToShowInBill($_REQUEST["UnitID"]);
	$show_wing = $showInBillDetails[0]["show_wing"];
	$show_parking = $showInBillDetails[0]["show_parking"];
	$show_area = $showInBillDetails[0]["show_area"];
	$show_receipt = $showInBillDetails[0]["bill_method"];
	$show_shareCertificate = $showInBillDetails[0]["show_share"];
	$bill_footer = $showInBillDetails[0]['bill_footer'];
	$show_due_date = $showInBillDetails[0]["bill_due_date"];
	$show_floor = $showInBillDetails[0]["show_floor"];
	$specialChars = array('/','.', '*', '%', '&', ',', '(', ')', '"');
	$unitNoForPdf = str_replace($specialChars,'',$objFetchData->objMemeberDetails->sUnitNumber);
	$bDueDateNotMaxDate = false;
	$timestamp1 = strtotime(getDBFormatDate($DueDate));
	$timestamp2 = strtotime(PHP_MAX_DATE);
	if($timestamp1 <> $timestamp2)
	{
		$bDueDateNotMaxDate = true;
	}
	
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Maintanence Bill</title>
 <script type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="js/ajax_new.js"></script>
<script type="text/javascript" src="js/jsCommon_20190326.js"></script>

<script>SetAry_ExitingExCounter(<?php echo json_encode($VoucherCounter[0]['ExitingCounter']);?>)</script>  


<style>
	table {
    	border-collapse: collapse;
	}
	table, th, td {
   		border: 0px solid black;
		text-align:left;
		padding-top:0px;
		padding-bottom:0px;
	}
	
	.change-log{
		width: 90%;
	}



	.row-th, .row-td{

		border: 1px solid;
	}



	
</style>
<style type="text/css" class="init">
		.loader 
		{
			position: fixed;
			left: 0px;
			top: 0px;
			width: 100%;
			height: 100%;
			z-index: 9999;
			opacity:0.8;
			background: url('images/loader/page-loader.gif') 50% 50% no-repeat rgb(114,118,122);
		}
		textarea:focus, input:focus{
    outline: 0;
}
</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<!--<link rel="stylesheet" href="/resources/demos/style.css">-->
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="js/BillUpdate_20190409.js"></script>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<script>

				//Date picker of select date in specific Finacial year
				
				minStartDate = '<?php  echo getDisplayFormatDate($_SESSION['default_year_start_date']);?>';
				maxEndDate = '<?php  echo getDisplayFormatDate($_SESSION['default_year_end_date']);?>';
				
				<?php if(isset($_REQUEST['edt']) || isset($_REQUEST['debitcredit_id']))
				{?>
					InvoiceDate = '<?php echo getDisplayFormatDate($FinalDate); ?>';
				<?php }
				else{?>
					InvoiceDate = new Date();
				<?php }?>
				
                    $(function()
                    {
                        $.datepicker.setDefaults($.datepicker.regional['']);
                        $(".basics").datepicker({ 
                        dateFormat: "dd-mm-yy", 
                        showOn: "both", 
                        buttonImage: "images/calendar.gif", 
                        buttonImageOnly: true,
						changeMonth: true, 
    					changeYear: true,
						maxDate: maxEndDate,
						minDate: minStartDate	
                    }).datepicker("setDate", InvoiceDate)});
</script>
<script>
	
	function showLoader()
	{
		$(".loader").fadeIn("slow");
	}
	
	function hideLoader()
	{
		$(".loader").fadeOut("slow");
	}
	
	function hideLoaderFast()
	{
		$(".loader").fadeOut("fast");
	}
	
	document.write('<div class="loader"></div>');
	hideLoaderFast();
</script>
<script>
function setflag()
{
	document.getElementById("dwnflag").value = "1";
}


function ViewLog()
{
		window.open(document.getElementById("logurl").value, '_blank');
}
$(document).ready(function(){
 //checktaxable(1) 
});
/// Added new function checkbox checkedor not 
function checktaxable(count)
{
	//console.log("Inside Function.");
	//var test =$('#invoicetaxable2'+count).val($(this).is(':checked'));
	var TaxableChecked = $("#invoicetaxable"+count).is(":checked")
	if(TaxableChecked == true)
	{
		 $("#taxrate"+count).prop('disabled', false); //disable 
		 document.getElementById('taxrate'+count).style.display='inline-block';
		 document.getElementById('taxrateSet'+count).style.display='none';
		 document.getElementById('taxrate'+count).value='<?php echo $DefaultGSTRate ?>';
	}
	else
	{
		//console.log("Checkbox is unchecked.");
		document.getElementById('taxrate'+count).style.display='none';
		document.getElementById('taxrateSet'+count).style.display='inline-block';
		$("#taxrate"+count).prop('disabled', true); //disable 
	}
	
}

var NewRowCounter=0;

	function AddNewRow()
	{
		  NewRowCounter++;
		  <?php
	
			//Return only show in bill ledger 
						
		  		//$SELECT_Query = 'SELECT `id`, concat_ws(" - ",`ledger_name`,`id`) FROM `ledger` JOIN account_category ON ledger.categoryid = account_category.category_id where `id` NOT IN ('.ADJUSTMENT_CREDIT.','.IGST_SERVICE_TAX.','.CGST_SERVICE_TAX.",".SGST_SERVICE_TAX.','.CESS_SERVICE_TAX.','.ROUND_OFF_LEDGER.') and `show_in_bill` = 1 and `society_id` = '.$_SESSION['society_id'].'';
				$SELECT_Query = 'SELECT `id`,`ledger_name` FROM `ledger` JOIN account_category ON ledger.categoryid = account_category.category_id where `id` NOT IN ('.ADJUSTMENT_CREDIT.','.IGST_SERVICE_TAX.','.CGST_SERVICE_TAX.",".SGST_SERVICE_TAX.','.CESS_SERVICE_TAX.','.ROUND_OFF_LEDGER.') and `show_in_bill` = 1 and `society_id` = '.$_SESSION['society_id'].'';
	
		  ?>
			
          var newRow="<tr><td style='border:1px solid black;border-left:none;text-align:center;font-size:14px;'>"+NewRowCounter+"</td>";
		  
		  newRow += "<td colspan=3 style='border:1px solid black;'> <?php if($bApplyServiceTax == 1 && (isset($_REQUEST['add']) || isset($_REQUEST['edt']) || isset($_REQUEST['add_credit']) || isset($_REQUEST['add_debit']))){?>
		  <select onchange='IsTaxable(NewRowCounter)' name='particular"+NewRowCounter+"' id='particular"+NewRowCounter+"' style='width:30%;'><?php echo $Particular = $obj_genbill->comboboxEx($SELECT_Query);?></select><?php } else {?> <select name='particular"+NewRowCounter+"' id='particular"+NewRowCounter+"' style='width:30%;'><?php echo $Particular = $obj_genbill->comboboxEx($SELECT_Query);?></select><?php  }?></td>";
		  
			<?php if($bApplyServiceTax == 1 && (isset($_REQUEST['add']) || isset($_REQUEST['edt']) || isset($_REQUEST['add_credit']) || isset($_REQUEST['add_debit'])))
			{?>
		    newRow += "<td style='border:1px solid black;text-align:center;'><input type='checkbox' name='invoicetaxable"+NewRowCounter+"' id='invoicetaxable"+NewRowCounter+"'  onclick='checktaxable("+NewRowCounter+")' ;></td>";
			
			<?php //if(!isset($_REQUEST['NoteType']))
			//{?>
				newRow += "<td style='border:1px solid black;text-align:center;'>";
				newRow += "<select style='width: 50px; display:none' id='taxrate"+NewRowCounter+"' name='taxrate"+NewRowCounter+"' value=''><?php for($i = 0; $i < sizeof($GSTTAXRATES); $i++){
				$selected = $GSTTAXRATES[$i]["id"] == $DefaultGSTRate ? "selected" : ""; 
				echo "<option value='". $GSTTAXRATES[$i]["id"] ."' ".$selected.">" .$GSTTAXRATES[$i]["TaxRate"] . "</option>";} ?>'</select>";
				
				newRow += "<span id='taxrateSet"+NewRowCounter+"' style='display:block'>0</spna>";
				newRow +="</td>";
		<?php //}
			}
		?>
		  newRow +="<td align=right style='border:1px solid black;border-right:none;text-align:right;width:15%;font-size:14px;'> <input type='text' id='HeaderAmount"+NewRowCounter+"' name='HeaderAmount"+ NewRowCounter+"' value='0.00' style='text-align:right;background-color:#FFFF00;'  onBlur = 'extractNumber(this,2,true);' onKeyUp='extractNumber(this,2,true);' onKeyPress='return blockNonNumbers(this, event, true, true);' /></td></tr>";
		  
		  $("#mainTable").append(newRow);
		  <?php if($bApplyServiceTax == 1)
		  	{?>
		  		checktaxable(NewRowCounter);
			<?php }?>
		}
	
	function test()
	{
		document.getElementById('bill_address').style.left = "";
	}
	
	///******Code For Print Page 
	
	function PrintPage() 
	{
		//Get the print button and put it into a variable
		//alert("print called");
        //var btnEdit = document.getElementById("Edit");
		var btnPrint = document.getElementById("Print");
		var btnViewAsPDF = document.getElementById("viewbtn");
		var btnSendEmail;
		var btnDownloadPdf;
		var logContent = document.getElementById('logchanges');
		
		if(logContent != null)
		{
			logContent.style.visibility = 'hidden';
		}
		
		if (document.getElementById("send_email") != null)
		{
			btnSendEmail =document.getElementById("send_email");
		}
		
		if (document.getElementById("dwnbtn") != null)
		{
			btnDownloadPdf =document.getElementById("dwnbtn");
		}
		
		<?php if($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN){?>
		 var btnEdit = document.getElementById("Edit");
		 if (document.getElementById("Edit") != null)
		{
		 	btnEdit.style.visibility = 'hidden';
		}
		 <?php }?>
        //Set the print button visibility to 'hidden' 
        //btnEdit.style.visibility = 'hidden';
		btnPrint.style.visibility = 'hidden';
		btnViewAsPDF.style.visibility = 'hidden';
		if (document.getElementById("send_email") != null)
		{
			btnSendEmail.style.visibility = 'hidden';
		}
		
		if (document.getElementById("dwnbtn") != null)
		{
			btnDownloadPdf.style.visibility = 'hidden';
		}
		//Print the page content
        window.print();
        //Set the print button to 'visible' again 
        //[Delete this line if you want it to stay hidden after printing]
        //btnEdit.style.visibility = 'visible';
		<?php if($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN){?>
		 if (document.getElementById("Edit") != null)
		{
		 	btnEdit.style.visibility = 'visible';
		}
		 <?php }?>
		btnPrint.style.visibility = 'visible';
		btnViewAsPDF.style.visibility = 'visible';
		logContent.style.visibility = 'visible';
		if (document.getElementById("send_email") != null)
		{
			btnSendEmail.style.visibility = 'visible';
		}
		
		if (document.getElementById("dwnbtn") != null)
		{
			btnDownloadPdf.style.visibility = 'visible';
		}
	}
	
	//****View As PDF Function
	
	function ViewPDF(unitID,InvoiceNo)
	{	
		var sData = document.getElementById('bill_main').innerHTML;

		var bDownload = "0";
		var sHeader = '<html><head>';
		sHeader += '<style> ';
		sHeader += 'table {	border-collapse: collapse; } ';
		sHeader += 'table, th, td { border: 0px solid black; text-align: left; padding-top:0px; padding-bottom:0px; } ';
		sHeader += '</style>';	
		sHeader +=	'</head><body>';
		
		var sFooter =  '</body></html>';
		
		sData = sHeader + sData + sFooter;
	
		var sUnitNo = document.getElementById('owner_unit').innerHTML;
		
		var sFileName = "Inv-<?php echo $objFetchData->objSocietyDetails->sSocietyCode; ?>-" +  "<?php echo $unitNoForPdf; ?>" + "-<?php echo $FinalNumber; ?>" ;
		
		var sURL = "viewpdf.php";
		var obj = {"data":sData, "file":sFileName};
		
		if(document.getElementById("dwnflag").value == "1")
		{
			bDownload = "1";
		}
		$.ajax({
			url : "viewpdf.php",
			type : "POST",
			data: { "data":sData, 
					"filename":sFileName, 
					"society": "<?php echo $objFetchData->objSocietyDetails->sSocietyCode; ?>",
					"InvoiceNumber": "<?php echo $FinalNumber; ?>",
					"BT" : "<?php echo TABLE_SALESINVOICE; ?>","bDownload":bDownload} ,
			success : function(data)
			{	
				<?php if(!isset($_REQUEST['gen']))
				{
					?>
					if(bIsSendEmail == true)
					{
						 bIsSendEmail = false;
						 sendEmail(unitID, InvoiceNo, false,'<?php echo $objFetchData->objMemeberDetails->sEmail; ?>', '<?php echo TABLE_SALESINVOICE; ?>',true);
					}
					else
					{
						if(document.getElementById("dwnflag").value == "1")
						{
							var downLoadLink = "viewpdf.php?society=<?php echo $objFetchData->objSocietyDetails->sSocietyCode; ?>&period=<?php echo TABLE_SALESINVOICE; ?>&filename=" + sFileName +"&bDownload=" + bDownload;
							document.getElementById("dwnflag").value == "0";
							window.open(downLoadLink, '_blank');
						}
						else
						{
							window.open('Invoice_bills/<?php echo $objFetchData->objSocietyDetails->sSocietyCode; ?>/' + sFileName + '.pdf');
						}
					}
					<?php
				}
				else
				{
				?>
					//document.getElementById('msg').style.color = '#FF0000';
					document.getElementById('msg').innerHTML = '<font color="#009900"><a href="maintenance_bills/<?php echo $objFetchData->objSocietyDetails->sSocietyCode; ?>/<?php echo $BillFor; ?>/' + sFileName + '.pdf" target="blank"><img src="images/pdficon.png" /></a></font>';
					window.close();
				<?php
				}
				?>
			},
				
			fail: function()
			{
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});
	}

</script>
<script>
var unitID="";
var billdate="";
</script>
		
		
</head>
<body>
	<div id="msg" style="color:#FF0000;"></div>
    <center>
  

    <Form Name ="form1" Method ="POST" >

	<div align="center" style="alignment-adjust:middle; left:80px;">
		<?php 
		if(isset($_REQUEST['add']) || isset($_REQUEST['edt']) || isset($_REQUEST['add_credit']) || isset($_REQUEST['add_debit']))
		{ 
		?>
		<input type="hidden" id='DebitCreditID' name="DebitCreditID" value=<?php echo $obj_genbill->GetNextIDOfDebitCredit();?>>
        	<input type="hidden" id='CreditNoteType' name="CreditNoteType" value=<?php echo CREDIT_NOTE;?>>	
		
		<?php //***This Invoice Number Will Apear in URl and Then invoice bill of same number come	
		$FinalNumbers = $obj_genbill->GetInvoiceNumber($_REQUEST['UnitID']);
		
        ///$invoiceNumber = $invoiceNumber[0]['max(Inv_Number)']+1;*/
		if(isset($_REQUEST['edt']))
		{?>
        	<input type="hidden" id='VoucherArray' name="VoucherArray" value=<?php echo json_encode($FinalNumbers);?>>	
			
            <input type="hidden" id='IsInvoiceEdit' name="IsInvoiceEdit" <?php if(!isset($_REQUEST['NoteType'])){ ?>value="1" <?php } else {?>value="0" <?php }?>>	
           
            <input type="hidden" id='EditInvoiceNo' name="EditInvoiceNo" value="<?php echo $FinalNumber;?>">
            <input type="hidden" id='editableId' name="editableId" value="<?php echo $_REQUEST['debitcredit_id'];?>">		
            
            <input type="hidden" id='ExitingInvoiceUnitID' name="ExitingInvoiceUnitID" value="<?php echo $_REQUEST['UnitID'];?>">	
		<?php }
		else
		{?>
        	<input type="hidden" id='IsInvoiceEdit' name="IsInvoiceEdit" value="0">	
		<?php }
		 ?> 
        <!--<input type="hidden" id="invoiceNumber" name="invoiceNumber" value="<?php echo $VoucherCounter;?>">-->     

       <input type="hidden" id="invoiceNumber" name="invoiceNumber" value="<?php echo $VoucherCounter[0]['CurrentCounter'];?>">
        <input type="hidden" id='SocietyTaxable' name="SocietyTaxable" value="<?php echo $bApplyServiceTax; ?>">
		<input type="hidden" id='request_type' name="request_type" value="Invoice">
        
		<?php 	}
		
		if(isset($_REQUEST['add']))
		{?>
			
        <INPUT TYPE="button" id="add" onClick="jsBillUpdate(HeaderAndAmount,NewRowCounter)" value="Create Invoice" style="width:200px;height:30px;display: inline-block;font-size: 18px;font-style: normal;color: #fff;background-color: #337ab7;border-color: #2e6da4; border-radius: 4px;
    border: 0px;"/><br><br>
		<?php	}
		else if(isset($_REQUEST['add_credit']))
		{ ?>
                <INPUT TYPE="button" id="creditNote"  onClick ="AddCreditDebitNote(NewRowCounter,<?php echo CREDIT_NOTE;?>,'Invoice',0);" value="Create Credit Note" style="width:200px;height:30px;margin-bottom:10px;font-size:18px;font-style: normal;color: #fff;background-color: #337ab7;border-color: #2e6da4; border-radius: 4px;
    border: 0px" /><br>
		<?php }
		else if(isset($_REQUEST['add_debit']))
		{ ?>
                <INPUT TYPE="button" id="debitNote" onClick ="AddCreditDebitNote(NewRowCounter,<?php echo DEBIT_NOTE;?>,'Invoice',0);" value="Create Debit Note" style="width:200px;height:30px;margin-bottom:10px;font-size:18px;font-style: normal;color: #fff;background-color: #337ab7;border-color: #2e6da4; border-radius: 4px;
    border: 0pxfont-style: normal;color: #fff;background-color: #337ab7;border-color: #2e6da4; border-radius: 4px;
    border: 0px" /><br>
		<?php }
		
		if(!isset($_REQUEST['add']) && !isset($_REQUEST['edt'])&& !isset($_REQUEST['add_credit']) && !isset($_REQUEST['add_debit'])){?>
        <INPUT TYPE="button" id="Print" onclick="PrintPage()" name="Print!" value="Print!" width="300" style="width:60px;height:30px;margin-bottom:10px; font-size:18px;font-style: normal;color: #fff;background-color: #337ab7;border-color: #2e6da4; border-radius: 4px;border: 0px" />
        <?php 
		
		if((($_SESSION['role']==ROLE_SUPER_ADMIN) || ($_SESSION['profile'][PROFILE_CREATE_INVOICE] == 1)) && ($_SESSION['is_year_freeze'] == 0))
		{ 
		
			if(isset($_REQUEST['debitcredit_id'])) 
			{?>
           
        <INPUT TYPE="button" id="Edit" onclick="window.location.href='Invoice.php?UnitID='+ <?php echo $_REQUEST["UnitID"]?> + '&debitcredit_id='+ <?php echo $_REQUEST["debitcredit_id"]?> + '&NoteType='+<?php echo $_REQUEST["NoteType"]?>+'&edt'" name="Edit" value="Edit" width="300" style="width:60px;height:30px; font-size:18px;font-style: normal;color: #fff;background-color: #337ab7;border-color: #2e6da4; border-radius: 4px;
    border: 0px" />
 		<?php }else
			{ ?>
		<INPUT TYPE="button" id="Edit" onclick="window.location.href='Invoice.php?UnitID='+ <?php echo $_REQUEST["UnitID"]?> + '&inv_number='+ <?php echo $_REQUEST["inv_number"]?> +'&edt'" name="Edit" value="Edit" width="300" style="width:60px;height:30px; font-size:18px;font-style: normal;color: #fff;background-color: #337ab7;border-color: #2e6da4; border-radius: 4px;
    border: 0px" />
		<?php }
				 
		}
	}
		
		
        if(isset($_REQUEST['edt']) ){
        if(($_SESSION['role']==ROLE_SUPER_ADMIN ) || $_SESSION['profile'][PROFILE_CREATE_INVOICE] == 1)
		{
			if(isset($_REQUEST['NoteType']))
			{
				 ?>
				 <INPUT TYPE="button" id="Update" name="Update" value="Update" onClick ="AddCreditDebitNote(NewRowCounter,<?php echo $_REQUEST['NoteType'];?>,'Invoice',1);" width="300" style="left:550;width:100px;height:30px; font-size:20px;">		
		<?php 
			}else 
		{?>
        <INPUT TYPE="button" id="Update" name="Update" value="Update" onClick="jsBillUpdate(HeaderAndAmount,NewRowCounter)" width="300" style="left:550;width:100px;height:30px; font-size:20px;">
        <INPUT TYPE="hidden" id="txtInterestOnArrears" name="txtInterestOnArrears" value="<?php echo INTEREST_ON_PRINCIPLE_DUE ?>">
        
 		<?php }
			}
		}
        ?>
    </div>
    <div id="bill_main" style="width:90%;">
    <div style="border:1px solid black;">
        <div id="bill_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:16px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
            <!--<div id="society_type" style="font-weight:bold; font-size:20px;">PREMISES CO-OPERATIVE SOCIETY LTD.</div>-->
            <div id="society_reg" style="font-size:14px;"><?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?></div>
            <div id="society_address"; style="font-size:12px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>
            <?php if($objFetchData->objSocietyDetails->sSocietyGSTINNo <> '')
			{ ?>
            <div id="society_gstin"; style="font-size:12px;"><span>GSTIN No :&nbsp;&nbsp;</span><b><?php echo $objFetchData->objSocietyDetails->sSocietyGSTINNo; ?></b>							</div>
            <?php }?>
        </div>
        <div id="bill_subheader" style="text-align:center;">
            <div style="font-weight:bold; font-size:14px;"> <?php if(isset($_REQUEST['add']) || isset($_REQUEST['inv_number']))
																{
																	 $title = 'Invoice '; 
																}
																else if(isset($_REQUEST['NoteType']) && $_REQUEST['NoteType'] == CREDIT_NOTE)
																{
																	 $title = 'Credit Note'.$BillTypeHead;
																}
																else if(isset($_REQUEST['NoteType']) && $_REQUEST['NoteType'] == DEBIT_NOTE)
																{
																	 $title = 'Debit Note'.$BillTypeHead; 
																}
																echo $title
																?>
           </div>
        </div>
        <div id="bill_details" style="text-align:center;border-top:1px solid black;font-size:<?php echo $BillFont?>px;">
            <table style="width:100%;">
            	<tr>
                	
                    <?php if(isset($_REQUEST['add'])  || isset($_REQUEST['edt']) || isset($_REQUEST['add_credit']) || isset($_REQUEST['add_debit']))
					{?>
                    
                    <input type="hidden" id = "IsOutSider" value="">
                    <td style="width:13%;">Bill To :</td>
                      <td style="width:15%;/*display:table-row*/" id ='Unit'><select name="UnitID" id="UnitID"  style="width:250px;margin-top:5px;">
						<?php
						
						//value of outsider is sent here to fetch different value in dropdown   
						echo $combo_state = $obj_genbill->FetchUnitName($outsider = 0,$_REQUEST['uid']); ?>   
                           			
							</select></td>	
                       <?php if(isset($_REQUEST['add'])  || isset($_REQUEST['edt'])){?>     
                      <td style="width:15%; display:none;" id = 'Outside'><select name="Outsider" id="Outsider"  style="width:250px;margin-top:5px;">
						<?php  echo $combo_state = $obj_genbill->FetchUnitName($outsider = 1); ?>   
                        			
							</select></td>   
                           
					<?php }} 
                    else if(!isset($_REQUEST['edit']))
					{?>
                    <td style="width:18.5%;">Bill To :</td>
                    <td id='owner_name' style="font-weight:bold;"><?php echo $objFetchData->objMemeberDetails->sMemberName;?>
                   
                     <?php }
                     
                     if(isset($_REQUEST['add'])  || isset($_REQUEST['edt']) && (!isset($_REQUEST['NoteType'])))
					 {?>
                     <!--This only check when user want to select outsider member-->
                    <td style = "width:100%;padding-top:5px;" ><input type='checkbox' onclick ='SundryDebtorCheckbox()' name='OutSideServices' id='OutSideServices' value='1' ;><label>(Sundry Debtors Only)</label></td>
                    
               <?php } 
			   
			   	if(isset($_REQUEST['add_credit']) || isset($_REQUEST['add_debit']) || (isset($_REQUEST['edt']) && isset($_REQUEST['debitcredit_id'])))
				{?>
              
               <td align="left" style="width:10%; padding-left: 60px;">Bill Type :</td>
               <td style="width:10%"><input type="radio" name="BillType" id="MBilltype"  value="<?=Maintenance?>"> Mainteinance</td>
			   <td style="width:10%"><input type="radio" name="BillType" id="SBilltype" value="<?=Supplementry?>">Supplementry</td>
			   <td><input type="radio" name="BillType" id="IBilltype" value="<?=Invoice?>">Invoice</td>
               <?php }?>
			   <?php if(!isset($_REQUEST['add'])  && !isset($_REQUEST['edt']))
			   {
					if($objFetchData->objMemeberDetails->sMemberGstinNo <> "")
					{?>
						<td align="left" style="width:13%;">GSTIN No :</td>
						<td id='owner_gstin_no' style="font-weight:bold;width:13%;"><?php echo $objFetchData->objMemeberDetails->sMemberGstinNo; ?></td>
						<?php 
					}
			   }?>
						
              	</tr>
            </table>
            
            <table style="width:100%;">
                <tr>
                <td style= "width:74%">
                <?php if(!isset($_REQUEST['edt'])){?>
                <table style="width:124%;">
                <tr >
                  <td id='owner_unit' style="width: 20%;" ><?php echo $unitText;?> </td><td style="font-weight:bold; width:16%" colspan="2"><?php echo $objFetchData->objMemeberDetails->sUnitNumber; ?></td>
                
                <?php if($show_floor == false && $show_wing == false)
				{
					?>
                    <td>&nbsp;</td>
                    <?php
				}
				else
				{
					?>
                	<?php if($show_floor) { ?><td style="width: 8%;">Floor No :</td><td colspan="" width="4%"><?php echo $wing_areaDetails[0]['floor_no'] ; ?></td>
                	<?php if(!$show_wing) {
						?>
                        	<td>&nbsp;</td>
                        <?php
					}}?>
                 	<?php if($show_wing) { ?><td style="width:12%;">Wing :</td><td><?php echo $wing_areaDetails[0]['wing'] ?></td></tr>
                	<?php
				}
				?>
                
                 <?php }?> 
                 <?php //if($show_parking) { ?><!--<tr><td style="width: 16%;">Parking No :</td><td> <?php //echo "".$objFetchData->objMemeberDetails->sParkingNumber; ?></td></tr>-->
                <?php //}?>
                
                <?php 
				if($show_area || $show_parking)
				{
					if($show_area)
					{
						?>
						<?php if($show_area) {?><tr><td style="width: 16%;">Area :</td><td style="width:12%" colspan="2"><?php echo $wing_areaDetails[0]['area'] . ' Sq.Ft'; ?></td>
						<?php
						}
					
					} 
					?>
					<?php if($show_parking) { ?><td style="width: 16%;">Parking No :</td><td colspan="3"> <?php echo "".$objFetchData->objMemeberDetails->sParkingNumber; ?></td></tr>
					<?php }
				}
				else
				{?>
                	<tr><td>&nbsp;</td></tr>
                <?php } ?>
                
                <?php if($show_shareCertificate)
			{ ?>
            
                            	
                   	<tr><td id='shareCertificate' style="width:  20%;">Share Certificate No : </td><td style="width: 16%;"> <?php echo $objFetchData->GetShareCertificateNo($_REQUEST["UnitID"]); ?></td></tr>	
                    				                
               <?php }
			   else{ ?>
               <tr><td>&nbsp;</td></tr>
               <?php }?>
               </table>
              <?php }?>  	
            </td>
            <td style="width:26%">
            <table>
	            <tr>
	            	<td style="width:10%">Bill No:</td>
                    <?php if(isset($_REQUEST['edt']))
					{?> 
	                <td><input id='bill_no' style="width:50%;" value="<?php echo $FinalNumber;?>"></td>
                    <?php }
					else if(!isset($_REQUEST['add']))
					{ ?>
					<td><input id='bill_no' style="width:15%;border:none;padding-left:25px;" class=""  readonly ><?php echo $BillNoWithPrefix;?></td>	 
					<?php }?>
	            </tr>
				<tr>
                	<td style="width:10%">Date:</td>
                      <?php if(isset($_REQUEST['add']) || isset($_REQUEST['edt']) || isset($_REQUEST['add_credit']) || isset($_REQUEST['add_debit']))
					{ ?> 
                    
                    <td> <input type="text" name="bill_date" id="bill_date" value=""  class="basics" style="width:50%;text-align:left"  readonly /></td>
                     <?php }
					else if(!isset($_REQUEST['edt']) || isset($_REQUEST['debitcredit_id']))
					{  
					?>
                       <td> <input type="text" name="bill_date" id="bill_date" value="" style="width:15%;border:none;text-align:left;"  readonly /><?php echo getDisplayFormatDate($FinalDate);?></td>
                       <?php }?>
	            </tr>
	            <tr>
                		<td style="width:10%;<?php if(!$show_due_date) { echo 'visibility:hidden;';} ?>"></td>
	                   	<td id='bill_due' style="width:15%;<?php if(!$show_due_date) { echo 'visibility:hidden;';} ?>"><?php echo $DueDate ?></td>
	            </tr>
            </table>
            
            </td></tr></table>
            <?php 
            	if($bApplyServiceTax)
            	{	
            		?>
		            <table style="width: 100%;">
		            	<tr>
		            		<td style="width: 18.5%;">SAC :</td>
		            		<td style="width:55.5%;">999598</td>
		            		<td style="width: 14.5%">POS :</td>
		            		<td>MH (27)</td>
		            	</tr>
		            </table>
		            <?php
	        	}
	        ?>
           <?php  //}?>
        </div>
        <?php 	
		?>
        <div id="bill_charges">
        	<table  style="width:100%;font-size:14px;" id="mainTable">
                <tr>
                <th style="text-align:center; width:10%; border:1px solid black;border-left:none;font-size: <?php echo $BillFont?>px;">Sr. No.1</th>
                <th style="text-align:center; border:1px solid black;font-size: <?php echo $BillFont?>px;" colspan="3">Particulars of Charges</th> 
                <?php if($bApplyServiceTax == 1 && (isset($_REQUEST['add']) || isset($_REQUEST['edt']) || isset($_REQUEST['add_credit']) || isset($_REQUEST['add_debit'])))
				{ ?>
                <th style="text-align:center; border:1px solid black;font-size: <?php echo $BillFont?>px;" >Taxable</th>
               		<?php if(!isset($_REQUEST['NoteType']))
					{?>
                		<th style="text-align:center; border:1px solid black;font-size: <?php echo $BillFont?>px;" >GST Rate (%)</th>
                	<?php
					}
				 }?>
                <th style="text-align:center; width:20%; border:1px solid black;border-right:none;font-size:<?php echo $BillFont?>px;">Amount (Rs.)</th>
                </tr> 
                <?php if(isset($_REQUEST['add']) || isset($_REQUEST['add_credit']) || isset($_REQUEST['add_debit']))
				{?>
                <script>AddNewRow()</script>
                <?php }?>
                   </table>
                  <table width="100%">
                 <?php if(isset($_REQUEST['add']) ||  (isset($_REQUEST['edt']) && !isset($_REQUEST['debitcredit_id'])) /*|| isset($_REQUEST['add_credit']) || isset($_REQUEST['add_debit'])*/){?>
                <tr><td style='border-bottom:0.5px solid black;border-right:none;text-align:right;width:100%;font-size:14px;'> <!--<input type="button"  name="Submit" id="Submit" value="SUBMIT" onClick="SubmitBillRows();" style=" visibility:hidden;"/>--><input type="button"  name="ADD" id="ADD" value="ADD" onClick="AddNewRow();"/></td></tr>
           		<?php }?></table>
               
                <table width="100%">
                
                <?php
                	$counter = 1;
					$SubTotal = 0;
					$bNonGSTParticularsAdded = false;
					$bGSTParticularsAdded = false;

					$dNonGstLedgerTotal = 0;
					$dGstLedgerTotal = 0;
					$NonGstLedger = array();
					$GstLedger = array();
					$TaxRateCompareArray = array_column($TaxRateLedgersArray, 'Ledger');
					
					for($i = 0; $i<sizeof($data);$i++)
					{
						
						if(in_array($data[$i]['key1'],$TaxableLedgersArray))
						{
							 array_push($GstLedger , $data[$i]);
						}
						else
						{
							array_push($NonGstLedger , $data[$i]);	
						}
					}
					
					$data = array_merge($NonGstLedger,$GstLedger);
					for($i=0;$i<sizeof($data);$i++)
					{
					
						$index1 = false;
						$voucherID = $data[$i]['id'];
						$To_Ledger = $data[$i]['key1'];
						$Credit_amount = $data[$i]['Credit'];
						$ledger_name = $data[$i]['ledger_name']; 
						$LedgerDetails = $m_objUtility->GetLedgerDetails($To_Ledger);
						if(in_array($data[$i]["key1"], $TaxableLedgersArray))
						{	
							$LedgerDetails[$data[$i]["key1"]]['General']['taxable']=1;
							
						}
						else
						{
							$LedgerDetails[$data[$i]["key1"]]['General']['taxable']=0;
								
						}
						//echo "Tax sfsfs ::".sizeof($TaxRateLedgersArray);
						//echo "Taxable ::".$LedgerDetails[$data[$i]["key1"]]['General']['taxable'];
						if($LedgerDetails[$data[$i]["key1"]]['General']['taxable'] == 1 && sizeof($TaxRateLedgersArray) ==0)
						{
							$LedgerDetails[$data[$i]["key1"]]['General']['taxrate'] =$DefaultGSTRate;
						}
						else
						{
							if(in_array($data[$i]['key1'],$TaxRateCompareArray))
							{
								$index1 = array_search($data[$i]['key1'], $TaxRateCompareArray);
								if($index1 !== false)
								{
									$LedgerDetails[$data[$i]["key1"]]['General']['taxrate'] = $TaxRateLedgersArray[$index1]['TaxRate'];
								}
							}
							else
							{
								$LedgerDetails[$data[$i]["key1"]]['General']['taxrate'] = 0;
							}
						}
						if($bApplyServiceTax && $bNonGSTParticularsAdded == false && $LedgerDetails[$data[$i]["key1"]]['General']['taxable'] == 0)
						{
							$bNonGSTParticularsAdded = true;
							?>
							<?php 
							if(!isset($_REQUEST['edt']))
                			{
								
                			?>
								<tr>
				                	<td style="border:1px solid black;border-left:none;width:10%;text-align:center;font-size:<?php echo $BillFont?>px;font-weight: bold;">A</td>
				                	<td  colspan='5' style="border:1px solid black;text-align:left;font-size:<?php echo $BillFont?>px;padding-left:3px;font-weight: bold;">Non GST Charges</td>
				                	<td style="border:1px solid black;border-right:none;text-align:right;width:20%;font-size:<?php echo $BillFont?>px;"></td>
                                  
				                </tr>
				             <?php 
				            }?>
							<?php
						}

						if($bApplyServiceTax && $bGSTParticularsAdded == false && $LedgerDetails[$data[$i]["key1"]]['General']['taxable'] == 1)
						{
							$taxableLedgerCount++;
							if($bNonGSTParticularsAdded)
							{
								?>
								<?php 
								if(!isset($_REQUEST['edt']) && !isset($_REQUEST['add']))
                				{
                					?>
									<tr>
				                		<td style="border:1px solid black;border-left:none;width:10%;text-align:center;font-size:<?php echo $BillFont?>px;;font-weight: bold;"></td>
				                		<td colspan="5" style="border:1px solid black;text-align:right;font-size:<?php echo $BillFont?>px;padding-right:3px;font-weight: bold;">Sub Total of Non GST Charges - A</td>
				                		<td style="border:1px solid black;border-right:none;text-align:right;width:20%;font-size:<?php echo $BillFont?>px;font-weight: bold;"><?php echo number_format($dNonGstLedgerTotal, 2); ?></td>
				                	</tr>
				                	<?php
				                }
				                ?>
								<?php
							}

							$bGSTParticularsAdded = true;
							if(!isset($_REQUEST['edt']))
							{?>
								<tr>
				                	<td style="border:1px solid black;border-left:none;width:10%;text-align:center;font-size:<?php echo $BillFont?>px;font-weight: bold;">B</td>
                                     <?php if(!isset($_REQUEST['NoteType']))
									{?>
				                	<td colspan="4" style="border:1px solid black;text-align:left;font-size:<?php echo $BillFont?>px;padding-left:3px;font-weight: bold;">GST Charges</td>
                                   
                                    <td  style="border:1px solid black;text-align:right;font-size:<?php echo $BillFont?>px;padding-left:3px;font-weight: bold; width: 10%;">GST Rates&nbsp;</td>
                                    <?php }
									else
									{?>
										<td colspan="5" style="border:1px solid black;text-align:left;font-size:<?php echo $BillFont?>px;padding-left:3px;font-weight: bold;">GST Charges</td>
									<?php }
									?>
				                	<td style="border:1px solid black;border-right:none;text-align:right;width:20%;font-size:<?php echo $BillFont?>px;"></td>
				                </tr>
							<?php
							}
						} 
						
						if($data[$i]["key1"] != ADJUSTMENT_CREDIT)
						{
							
							if($data[$i]["Credit"] <> 0)
							{
								$bIsParticular = true;
								if($data[$i]["key1"] == INTEREST_ON_PRINCIPLE_DUE && $data[$i]["Credit"] >= 0)
								{

									$bIsParticular = true;
									$InterestOnArrears = $data[$i]["Credit"];
								}
								elseif(($data[$i]["key1"] == SERVICE_TAX && $data[$i]["Credit"] >= 0) || ($data[$i]["key1"] == IGST_SERVICE_TAX && $data[$i]["Credit"] >= 0 )|| ($data[$i]["key1"] == SGST_SERVICE_TAX && $data[$i]["Credit"] >= 0 ) ||( $data[$i]["key1"] == CGST_SERVICE_TAX && $data[$i]["Credit"] >= 0 )||( $data[$i]["key1"] == CESS_SERVICE_TAX && $data[$i]["Credit"] >= 0) || ( $data[$i]["key1"] == ROUND_OFF_LEDGER && $data[$i]["Credit"] <> 0))
								{
									$bIsParticular = false;
									$ServiceTax += $data[$i]["Credit"];
								}
								
								if($bIsParticular == true)
								{
									
									$ParticularLedger = $LedgerDetails[$data[$i]["key1"]]['General']['ledger_name'];
									$Rate =$LedgerDetails[$data[$i]["key1"]]['General']['taxrate']." % ";
									$TaxRateString = "";
									$PerticulorColspan = 5;
									if((!isset($_REQUEST['NoteType'])) && $Rate > 0 )
									{
										$TaxRateString = "<td  style='border:1px solid black;text-align:right;font-size:".$BillFont."px;padding-left:3px;width:10%'>".$Rate."</td>";
										$PerticulorColspan = 4;
										
									}
								
									//edit mode
									if(!isset($_REQUEST['edt']))
									{	
									
										echo "<tr><td style='border:1px solid black;border-left:none;width:10%;text-align:center;font-size:".$BillFont."px;'>".$counter."</td><td colspan=".$PerticulorColspan." style='border:1px solid black;text-align-left;font-size:".$BillFont."px;padding-left:3px;'>".strtoupper($ParticularLedger)."</td>".$TaxRateString."<td align=right style='border:1px solid black;border-right:none;text-align:right;width:20%;font-size:".$BillFont."px;'>". number_format($data[$i]["Credit"], 2) ."</td></tr>";
									}
									$SubTotal += $data[$i]["Credit"];

									if($LedgerDetails[$data[$i]["key1"]]['General']['taxable'] == 0)
									{
										$dNonGstLedgerTotal += $data[$i]["Credit"];
									}
									else
									{
										$dGstLedgerTotal += $data[$i]["Credit"];
									}
								}
							}
						}
						else
						{
							if($data[$i]["key1"] == INTEREST_ON_PRINCIPLE_DUE)
							{
								$InterestOnArrears = $data[$i]["Credit"];
							}
							else if($data[$i]["key1"] == ADJUSTMENT_CREDIT)
							{
								$AdjCredit = $data[$i]["Credit"];
							}
						}
						if($data[$i]["Credit"] <> 0 && $data[$i]["key1"] <> INTEREST_ON_PRINCIPLE_DUE && $data[$i]["key1"] <> ADJUSTMENT_CREDIT && $data[$i]["key1"] <> IGST_SERVICE_TAX && $data[$i]["key1"] <> CGST_SERVICE_TAX && $data[$i]["key1"] <> SGST_SERVICE_TAX && $data[$i]["key1"] <> CESS_SERVICE_TAX)
						{
							$counter++;
						}
						else if($data[$i]["key1"] == INTEREST_ON_PRINCIPLE_DUE && $data[$i]["Credit"] < 0)
						{
							$counter++;
						}
					}
					
					if($bApplyServiceTax && $taxableLedgerCount == 0)
					{
						?>
						<?php 
							if($_REQUEST['inv_number'] <> 0 && $_REQUEST['inv_number'] <> '')
                			{
                			?>
						<tr>
	                		<td style="border:1px solid black;width:10%;border-left:none;text-align:center;font-size:<?php echo $BillFont?>px;font-weight: bold;"></td>
	                		<td  colspan="5" style="border:1px solid black;text-align:right;font-size:<?php echo $BillFont?>px;padding-right:3px;font-weight: bold;">Sub Total of Non GST Charges - A1</td>
	                		<td style="border:1px solid black;border-right:none;text-align:right;width:20%;font-size:<?php echo $BillFont?>px;font-weight: bold;"><?php echo number_format($dNonGstLedgerTotal, 2); ?></td>
	                	</tr>
				        <tr>
		                	<td style="border:1px solid black;border-left;width:10%;:none;text-align:center;font-size:<?php echo $BillFont?>px;font-weight: bold;">B</td>
		                	<td colspan="5" style="border:1px solid black;text-align:left;font-size:<?php echo $BillFont?>px;padding-left:3px;font-weight: bold;">GST Charges</td>
		                	<td style="border:1px solid black;border-right:none;text-align:right;width:20%;font-size:<?php echo $BillFont?>px;"></td>
		                </tr>
		                <?php
						}
		                $bGSTParticularsAdded = true;
					}

					if($bApplyServiceTax && $bGSTParticularsAdded)
					{
						?>
						<?php 
							if(!isset($_REQUEST['add']) && !isset($_REQUEST['edt']) && !isset($_REQUEST['add_credit']) && !isset($_REQUEST['add_debit']))
                			{
                			?>
							<tr>
		                		<td style="border:1px solid black;width:10%;border-left:none;text-align:center;font-size:<?php echo $BillFont?>px;font-weight: bold;"></td>
		                		<td colspan="5" style="border:1px solid black;text-align:right;font-size:<?php echo $BillFont?>px;padding-right:3px;font-weight: bold;">Sub Total of GST Charges - B</td>
		                		<td style="border:1px solid black;border-right:none;text-align:right;width:20%;font-size:<?php echo $BillFont?>px;font-weight: bold;"><?php echo number_format($dGstLedgerTotal, 2); ?></td>
		                </tr>

						<?php
						}
					} ?>

               </table>
        
           <?php
		   		$BalanceAmout = 0;
				
				$BalanceAmout = $SubTotal + $AdjCredit  + $ServiceTax + $PrinciplePreviousArrears  /*+ $InterestOnArrears + $IntrestOnPreviousarrears + $IGST  + $CESS */;

			
			?>
           <table style="width:100%;font-size:14px;">
           	<tr>
                	<!--<td colspan="3" rowspan="<?php echo $IsSupplementaryBill ? "2" : "7" ?>" style="width:<?php echo $IsSupplementaryBill ? "60%" : "50%" ?>">E.& O.E.</td>-->
                    
                    <td colspan="3" rowspan="<?php echo ($bApplyServiceTax == 1 || $BillTax > 0 || $CGST >0 || $SGST >  0 ) ? $value : 3 ?>" style="width:50%;padding-left:3px;font-size: 12px;border-bottom:0.5px solid black">E.& O.E.</td>
                    <td style="width:20%;border:1px solid black;border-top:none;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">Sub Total <?php echo $bApplyServiceTax ? "(A + B)" : ""; ?></td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;border-top:none;font-size: <?php echo $BillFont?>px;"><?php echo number_format($SubTotal,2); ?></td>
                </tr> 
				<?php  

				if($bApplyServiceTax == 1 || $SGST > 0)
                    {?>
	                <tr>
                    <?php if(!isset($_REQUEST['NoteType']))
					{?>
	                	<td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">SGST <?php //echo $getSocietyInfo['sgst_tax_rate'] ?> on (B<?php echo ($bApplyGSTOnInterest ? " +  C" : "") ?>)</td>
                    <?php 
					   }
					   else
					   {?>
					   <td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">SGST @<?php echo $getSocietyInfo['sgst_tax_rate'] ?> on (B<?php echo ($bApplyGSTOnInterest ? " +  C" : "") ?>)</td>
					   <?php }?> 
	                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;font-size:<?php echo $BillFont?>px;"><?php echo number_format($SGST,2); ?></td>
	                </tr>
                    <?php }
					if($bApplyServiceTax == 1 || $CGST > 0 )
					{?>
                   <tr>
                    <?php if(!isset($_REQUEST['NoteType']))
					{?>
	                	<td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">CGST <?php //echo $getSocietyInfo['cgst_tax_rate'] ?> on (B<?php echo ($bApplyGSTOnInterest ? " +  C" : "") ?>)</td>
                     <?php 
					 }
					 else{?>
                     <td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">CGST @<?php echo $getSocietyInfo['cgst_tax_rate'] ?> on (B<?php echo ($bApplyGSTOnInterest ? " +  C" : "") ?>)</td>
                     <?php }?>   
	                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;font-size:<?php echo $BillFont?>px;"><?php echo number_format($CGST,2); ?></td>
	                </tr>
                    <?php }
					if($IGST > 0 )
					{?>
                   <tr>
	                	<td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">IGST @ <?php echo $getSocietyInfo['igst_tax_rate'] ?>% on (B<?php echo ($bApplyGSTOnInterest ? " +  C" : "") ?>)</td>
	                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;font-size:<?php echo $BillFont?>px;"><?php echo number_format($IGST,2); ?></td>
	                </tr>
                    <?php }
					if($CESS > 0 )
					{?>
                   <tr>
	                	<td style="width:30%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">CESS @ <?php echo $getSocietyInfo['cess_tax_rate'] ?>% on (B<?php echo ($bApplyGSTOnInterest ? " +  C" : "") ?>)</td>
	                    <td id="sub_total" style="text-align:right;width:20%;border:0.5px solid black;border-right:none;font-size:<?php echo $BillFont?>px;"><?php echo number_format($CESS,2); ?></td>
	                </tr>
                    <?php }


					?>
					<tr>
	                	<td style="width:30%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">Round-Off value of CGST & SGST</td>
	                    <td id="roundOffAmt" style="text-align:right;width:20%;border:0.5px solid black;border-right:none;font-size:<?php echo $BillFont?>px;"><?php echo number_format($roundOffAmt,2); ?></td>
	                </tr>
					<?php
				
		        //}
		        ?>
                
                <tr>
                	<td style="width:30%;border:0.5px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">Total Outstanding Amount</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;font-size: <?php echo $BillFont?>px;font-weight: bold;"><?php echo number_format(abs($BalanceAmout), 2);
					 if(isset($_REQUEST['NoteType']) && $_REQUEST['NoteType'] == CREDIT_NOTE)
					 {
						 echo ' Cr';
					 }
					 else
					 {
						if($BalanceAmout < 0)
						{
							echo ' Cr';
						}
						else
						{
							echo ' Dr';
						} 
					 } ?></td>
                </tr>
                <tr>
                	<td style="width:20%;border:1px solid black;border-right:none;border-left:none;padding-left: 3px;font-size: <?php echo $BillFont?>px;" colspan="6">
                    <?php
                    if($BalanceAmout <> "")
					{
						
					?>
                    	In Words : <?php  echo "Rupees ". convert_number_to_words(number_format($BalanceAmout,2)) . ' Only.';
					}
					?>
                        
                     </td>
                </tr>
	       </table>
         <input type="hidden" id="IsSupplementaryBill" name="IsSupplementaryBill" value="<?php echo $IsSupplementaryBill ?>"/>
        </div>
        
       <!-- <div id="bill_notes" style="text-align:left;font-size:<?php echo $BillFont?>px;margin-left:5px;">
        	Notes:<br>
       			<?php echo $BillNotes . " " . $sBillDetailNote; ?>  
                <textarea rows="4" cols="50"> </textarea>   
        </div>-->
        <div>
       <?php 
	   if($FinalNote == '0')
	   {
		   $FinalNote = "";
		}
	   
	   if(isset($_REQUEST['add']) ||  isset($_REQUEST['edt']) || isset($_REQUEST['add_credit']) ||  isset($_REQUEST['add_debit']))
	   {?>
       <table style="width:100%;font-size:14px;">
       <tr><td style="width:10%;float:center; border-right:0.5px solid black;" >Notes</td>
       <td style="border-top:1px solid black"><textarea rows="10" id="note" name="note" style="resize:none;width:99.5%;color:black;border-right-color:black;" placeholder="Please Write A Note...."><?php if(!isset($_REQUEST['NoteType'])) { echo strip_tags($FinalExitingNote);}?></textarea></td></tr>
       <script>
			CKEDITOR.replace('note', {toolbar: [
         						{ name: 'clipboard', items: ['Undo', 'Redo']},
        						{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
   								 ]});
		</script>
       </table>
       <?php }  
	   else if(!isset($_REQUEST['add'])){?>
        <table style="width:100%;font-size:14px;">
       <tr><td style="width:10%;float:center; border:1px solid black;" >Notes</td>
       <td style="border-top:1px solid black"><div id="bill_notes" style="text-align:left;font-size:<?php echo $BillFont?>px;margin-left:5px;"><?php echo $FinalNote;?></div></td></tr>
       </table>
       
       </div>
    	<?php }?>
        <div id="bill_message">
        </div>
        <?php
        	if($_SESSION['society_id'] <> 195)
        	{
        		?>
        		<div id="bill_sign" style="text-align:right;border-top:1px solid black;padding-right:10px;font-size:<?php echo $BillFont?>px;">
        	<?php echo $objFetchData->objSocietyDetails->sSocietyName; ?><br><br><br> <?php if($bill_footer <> "") { echo $bill_footer; } else { ?> Authorised Signatory <?php } ?>
        		</div>
        		<?php
        	}
        ?>
        <?php if($show_receipt == 1 && sizeof($receiptDetails) > 0)
			{ ?>
        <div id="bill_receipt" style="text-align:center;border-top:1px solid black;border-bottom:none;">        	
            <div id="society_name" style="font-weight:bold; font-size:14px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>           
            <div id="society_reg" style="font-size:10px;">
				<?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?>
            </div> 
            <div id="society_address"; style="font-size:10px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>                              
        	<div id="bill_subheader" style="text-align:center;font-weight:bold; font-size:14px;">
            	RECEIPT
            </div>
            <div id="bill_details" style="text-align:right;font-size:12px;">
            <table style="width:100%;">
            	<!--<tr>                	
                    <td style="width:10%">Date</td>
                    <td id='bill_date' style="width:15%;" colspan="3"><?php echo $BillDate ?></td>                    
              	</tr>-->  
              
            	<tr>
                	<td style="width:20%;" colspan="4">Received with thanks from  <?php echo $objFetchData->objMemeberDetails->sMemberName; ?></td>                    
              	</tr>
                <tr>
                	<td style="width:20%">Receipt Period:</td>
                    <td id='receipt_period' style="width:30%;"><?php  echo $StartDate . " to ". $EndDate ?></td>
                	<td style="width:40%; text-align:right;"><?php echo $unitText;?>:</td>
                    <td id='owner_unit' style="width:10%; text-align:center;"><?php echo $objFetchData->objMemeberDetails->sUnitNumber; ?></td>
                </tr>                                             
            </table>
        </div>
        <div id="bill_payment" style="width:100%;">
        	<table style="width:100%;font-size:<?php echo $BillFont?>px;">
                <tr>
                <th style="text-align:center; border:1px solid black;border-left:none;width:18%;">Receipt/Voucher Date</th>
                <th style="text-align:center; border:1px solid black;border-left:none;width:15%;">Cheque/NEFT Date</th>
                <th style="text-align:center; border:1px solid black;border-left:none;width:15%;">Cheque/NEFT No.</th>
                <th style="text-align:center; border:1px solid black;border-left:none;width:20%;">Payer Bank</th>
                <th style="text-align:center; border:1px solid black;border-left:none;width:15%;">Payer Branch</th>
                <th style="text-align:center; border:1px solid black;border-left:none;border-right:none;width:20%;">Amount</th>
                </tr>  
                <?php 
					//echo "Receipt Details";
					//echo sizeof($receiptDetails) ;
					$total = '';
					for($i=0; $i < sizeof($receiptDetails) ; $i++)
					{						
						$voucherDate = $receiptDetails[$i]['VoucherDate'];
						$amount = (float)$receiptDetails[$i]['Amount'];
						$payerBank = $receiptDetails[$i]['PayerBank'];
						$payerBranch = $receiptDetails[$i]['PayerChequeBranch'];
						$chequeDate = $receiptDetails[$i]['ChequeDate'];
						$chequeNo = $receiptDetails[$i]['ChequeNumber'];
						if($receiptDetails[$i]['IsReturn'] == 0)
						{
							$total += $amount;
						}
				?>
                <tr>
                	<!--<td style="text-align:center;border:1px solid black;border-left:none;"><?php //echo $i+1 ?> </td>-->         
                	<td style="text-align:center;border:1px solid black;border-left:none;"><?php echo getDisplayFormatDate($voucherDate) ?> </td>           
                    <td style="text-align:center;border:1px solid black;border-left:none;"><?php echo getDisplayFormatDate($chequeDate) ?> </td>
                    <td style="text-align:center;border:1px solid black;border-left:none;"><?php echo $chequeNo ?> </td>
                    <td style="text-align:center;border:1px solid black;border-left:none;"><?php echo $payerBank ?> </td>
                    <td style="text-align:center;border:1px solid black;border-left:none;"><?php echo $payerBranch ?> </td>
                    <td style="text-align:center;border:1px solid black;border-left:none;"><?php if($receiptDetails[$i]['IsReturn'] == 1){echo "Returned";}else{echo number_format($amount, 2);} ?> </td>
                </tr>                              
                <?php } 
				if($total <> '')
				{
				?>
                <tr>
                    <td colspan="5" style="text-align:right;"><?php echo "Total    :  " ?></td>
                    <td style="text-align:center;border:1px solid black;"><?php echo number_format($total, 2); ?> </td>

                </tr>
               
                <tr>
                	<td style="border:1px solid black;" colspan="6">
					In Words : <?php  echo "Rupees ". convert_number_to_words($total); if($total <> ''){ echo " Only"; }?>
					 </td>
                </tr>
          <?php } ?>      
           </table>
           </div>
        </div>
        <?php } ?>
        
        <?php
        	if($_SESSION['society_id'] <> 195)
        	{	
        		?>
		        <div id="bill_footer" style="text-align:left;border-top:1px solid black;padding-right:10px;border-top:none;">
		        	<table width="100%" style="font-size:<?php echo $BillFont?>px;">
		        
		        	<?php if($show_receipt == 1 && sizeof($receiptDetails) > 0)
						{ ?>
		            	<tr>
		            	<td style="text-align:left;width:50%;">( Subject to Realisation of Cheque ) </td>
		            	<td style="text-align:right;width:50%;"> <?php echo $objFetchData->objSocietyDetails->sSocietyName; ?> </td>
						</tr><?php } ?>
		            	<tr><td> <br><br> </td></tr>
		           	 	<tr>
		           		<td style="text-align:left;width:50%;"><?php  echo $Header?> </td>
		           		<td style="text-align:right;width:50%;"> <?php if($show_receipt == 1 && sizeof($receiptDetails) > 0)
					{ if($bill_footer <> "") { echo $bill_footer; } else {?> Authorised Signatory <?php } } ?></td>
		           		</tr>
		       		</table>
		        </div>
		        <?php
		    }
		    else
		    {
		    	?>
		    		<div id="bill_footer" style="text-align:left;border-top:1px solid black;padding-right:10px;border-top:none;">
		        	<table width="100%" style="font-size:<?php echo $BillFont?>px;">
		        
		        	<?php if($show_receipt == 1 && sizeof($receiptDetails) > 0)
						{ ?>
		            	<tr>
		            	<td style="text-align:left;width:50%;">( Subject to Realisation of Cheque ) </td>
		            	<td style="text-align:right;width:50%;"></td>
						</tr><?php } ?>
		       		</table>
		        </div>
		    	<?php
		    }
		?>
    </div>
    </div>
    </center>
    
    <input type="hidden" name="UnitID" id="UnitID" value="<?php echo  $_REQUEST['UnitID'];?>"/>
    <input type="hidden" name="PeriodID" id="PeriodID" value="<?php echo  $_REQUEST['PeriodID'];?>"/>
    <?php  if(isset($_REQUEST['add']))
		{?>
			
        <center><INPUT TYPE="button" id="add" onclick="jsBillUpdate(HeaderAndAmount,NewRowCounter)" value="Create Invoice" style="width:200px;height:30px; margin-top:10px;font-size:18px;font-style: normal;color: #fff;background-color: #337ab7;border-color: #2e6da4; border-radius: 4px; border: 0px" /></center><br>
		<?php	} ?>  
    </Form>
     <div style="margin-left:66px;margin-top:10px;">  
   <?php   if(!isset($_REQUEST['add']))
		{?>
    
   	 <input type="button" id="viewbtn" value="View As PDF"  onclick="ViewPDF('<?php echo $_REQUEST['UnitID']; ?>', '<?php echo $_REQUEST['inv_number'];?>');"/> 
     <input type="button" id="dwnbtn" value="Download PDF"  onclick="setflag();ViewPDF('<?php echo $_REQUEST['UnitID']; ?>', '<?php echo $_REQUEST['inv_number'];?>');"/> 
     <input type="hidden" name="dwnflag" id="dwnflag" value="<?php echo  $_REQUEST['dwnflag'];?>"/>
    
    <?php }?>
   <!-- <div><?php //echo "this for email".var_dump($objFetchData);?></div>-->
    <?php if($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['profile'][PROFILE_SEND_NOTIFICATION] == 1)
	{?>
    	<?php if(!isset($_REQUEST['add']))
		{
			//*** When user click the Send mail the Table_saleInvoice work and Invoice Number And unitid
		?>
            <input type="button" id="send_email" value="Send Email" onclick="sendEmail(<?php echo $_REQUEST['UnitID']; ?> , <?php echo $_REQUEST['inv_number']; ?>,true,'<?php echo $objFetchData->objMemeberDetails->sEmail; ?>','<?php echo TABLE_SALESINVOICE;?>', true);"  title="Email will be send to <?php echo $objFetchData->objMemeberDetails->sEmail; ?>" />
<?php   }?>
	 </div>
    <input type="button" id="viewlog" value=""  onclick="ViewLog();" style="background-color:#FFF;border:#FFFFFF"/>
    <?php if(!isset($_REQUEST['edt']))
		{?>
            <div id="status" style="color:#0033FF; font-weight:bold; visibility:hidden;"></div>
<?php }?>
        <br/>
		<center>
		<div id="FilterData" class="change-log" style="font-size:smaller;">
     <?php 
	//**Here we showing change history
	
	if(!isset($_REQUEST['add_credit']) && !isset($_REQUEST['add_debit']) && !isset($_REQUEST['add']) && !isset($_REQUEST['edt']))
	{
		if($_SESSION['role'] <>	ROLE_MEMBER)
		{
			if(!isset($_REQUEST['NoteType']))
			{
				$ShowChnagelog = $m_objLog->showFullLogDetail(TABLE_SALESINVOICE, $FinalRegisterID);
				
				echo $ShowChnagelog;	
			}
			else
			{
				$ShowChnagelog = $m_objLog->showFullLogDetail(TABLE_CREDIT_DEBIT_NOTE, $FinalRegisterID);
				echo $ShowChnagelog;
			}
			
		}	
	}
	
	?> 
    </div></center>
	<?php
	//$BillFor = $objFetchData->GetBillFor($_REQUEST['PeriodID']);
	$CurUnitNumber = $objFetchData->objMemeberDetails->sUnitNumber;
	//var_dump($BillFor); 
	$bill_dir = 'm_bills_log/' . $objFetchData->objSocietyDetails->sSocietyCode;
	if (!file_exists($bill_dir)) 
	{
		mkdir($bill_dir, 0777, true);
	}
	 $errorfile_name = $bill_dir.'/M_Bill_'.$_SESSION['society_id'].'_'. $CurUnitNumber .'_'.$BillFor.'_'.$BillNumber .'.html';
	$errorfile_name = str_replace(' ', '-',$errorfile_name);

	?>
    <input type="hidden" style="float:right" id="logurl" value="<?php  echo $errorfile_name; ?>" >
    <?php 
	}
	if($FinalBillType == Maintenance)
	{ ?>
		<script>document.getElementById('MBilltype').checked = true;</script>
	<?php }
	else if($FinalBillType == Supplementry)
	{ ?>
		<script>document.getElementById('SBilltype').checked = true;</script>
	<?php }
	else if($FinalBillType == Invoice)
	{ ?>
		<script>document.getElementById('IBilltype').checked = true;</script>
	<?php }
	?>
</body>
</html>
<?php
	
	//*********************  EDIT SECTION INVOICE ******************************
	
	$FinalFilterData = array();
	$Cnt = 0;
	//Invoice Date 
	$FinalDate = getDisplayFormatDate($FinalDate);
	//Fetch the ledger details
	$LedgerDetails = $m_objUtility->getParentOfLedger($FinalUnitID);
	$SundryDebtorsChkbox = false;
	if((int)$LedgerDetails['category'] <> DUE_FROM_MEMBERS)
	{
		// Ledger is due from member then set the SundryDebtorsChkbox
		$SundryDebtorsChkbox = true;
	}
	
	$otherDetails = array("SundryDebtorsChkbox" => $SundryDebtorsChkbox,"UnitID" => $FinalUnitID,"Note" => $FinalNote,);	
	
	//Array to skip the ledgers in bill			
	$SkipLedgers = array(IGST_SERVICE_TAX,CGST_SERVICE_TAX,SGST_SERVICE_TAX,CESS_SERVICE_TAX, ROUND_OFF_LEDGER);
	
	$TaxRateCompare = array_column($TaxRateLedgersArray, 'Ledger');
	for($i = 0 ; $i < sizeof($data) ; $i++)
	{
		$index = false;
		
		if(!in_array($data[$i]['key1'],$SkipLedgers))
		{	
			//Check the data is taxable or not
			if(in_array($data[$i]['key1'],$TaxableLedgersArray))
			{
				array_push($data[$i],$data[$i]['key1']);	
			}
			else
			{
				array_push($data[$i],0);	
			}
			if(in_array($data[$i]['key1'],$TaxRateCompare))
			{
				$index = array_search( $data[$i]['key1'], $TaxRateCompare);
				if($index !== false){
					$data[$i]['rate'] = $TaxRateLedgersArray[$index]['TaxRate'];
				}

			}
			else
			{
				//array_push($data[$i]['rate'],0);
				$data[$i]['rate'] = 0;
			}
			
			$FinalFilterData[$Cnt]['LedgerID'] = $data[$i]['key1'];
			$FinalFilterData[$Cnt]['Taxable'] = $data[$i][0];
			$FinalFilterData[$Cnt]['LedgerAmt'] = $data[$i]['Credit'];
			$FinalFilterData[$Cnt]['TaxRate'] = $data[$i]['rate'];
			
			$Cnt++;
		}
		
	}
	
	$LedgerDetails = json_encode($FinalFilterData);
	$UserDetails = json_encode($otherDetails);
	if(isset($_REQUEST['edt']))
	{ // setting edit mode for sale Invoice
		$NoteType =0;
		if(isset($_REQUEST['NoteType']))
		{
			$NoteType =$_REQUEST['NoteType'];
		}
	?>
       	<script>
			GetInvoiceDetails(<?php echo $LedgerDetails ;?>,<?php echo $UserDetails ;?>,<?php echo $NoteType ?>);
       </script>
        <?php
	}
	
?>

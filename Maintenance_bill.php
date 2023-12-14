<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Maintenance Bill</title>
</head>




<?php 
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include "classes/include/fetch_data.php";
include "common/CommonMethods.php";
include_once("classes/dbconst.class.php");
//include_once("classes/PaymentDetails.class.php");
include_once("classes/genbill.class.php");
include_once "classes/utility.class.php";
$currentUrl ="";

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

$obj_genbill = new genbill($dbConn);
$outstanding_result = $obj_genbill->selecting();

$objFetchData = new FetchData($dbConn);
$m_objUtility = new utility($dbConn);
$m_objdbRoot = new  dbop(true);

$bill_Template = $m_objUtility->GetBillTemplate($_REQUEST['PeriodID']);


	if(isset($_REQUEST['gen']))
	{
		$additionalParams .= '&gen';
	}
	if(isset($_REQUEST['edt']))
	{
		$additionalParams .= '&edt';
	}
	if(!isset($_REQUEST['edt']) && $bill_Template == MODERN_BILL_TEMPLATE)
	{
		
		//Show Sonata bills before April 2020 in older format	
		if(!(($_SESSION['society_id'] == 272) && ($_REQUEST['PeriodID'] <= 10)))
		{
			$bill_template_url = "Maintenance_bill1.php?UnitID=".$_REQUEST['UnitID']."&PeriodID=".$_REQUEST['PeriodID']."&BT=".$_REQUEST['BT'].$additionalParams ;
			header('Location:'.$bill_template_url);
		}
	}
	else if($bill_Template == CLASSIC_BILL_TEMPLATE)
	{
		
		$bill_template_url = "Maintenance_bill3.php?UnitID=".$_REQUEST['UnitID']."&PeriodID=".$_REQUEST['PeriodID']."&BT=".$_REQUEST['BT'].$additionalParams ;
			header('Location:'.$bill_template_url);
	}
	else if($bill_Template == CLASSIC_ADVANCE_BILL_TEMPLATE)
	{
		
		$bill_template_url = "Maintenance_bill2.php?UnitID=".$_REQUEST['UnitID']."&PeriodID=".$_REQUEST['PeriodID']."&BT=".$_REQUEST['BT'].$additionalParams ;
			header('Location:'.$bill_template_url);
	}
	

// Check Ledger Rounding Value is set or Not

$objFetchData->GetSocietyDetails($objFetchData->GetSocietyID($_REQUEST["UnitID"]));

if($_SESSION['default_ledger_round_off'] == 0 && $objFetchData->objSocietyDetails->sLedgerRoundOffSet)
{
?>
<script>
	alert('Please first set Ledger Round Off in default setting');
	window.location.href = "defaults.php";
</script>
<?php }



$ClientDetails = $m_objdbRoot->select("select * from `client` where `id` = '".$_SESSION['society_client_id']."'");
if(isset($ClientDetails) && $_SESSION['society_id'] <> 195)
{
	$Header = $ClientDetails[0]["bill_footer"];
}
else
{
	$Header = '';
}

$unitText = $objFetchData->getUnitPresentation($_REQUEST["UnitID"] );
$latestPeriod = $objFetchData->getLatestPeriodID($_REQUEST["UnitID"]);
/*if($_SESSION['society_client_id']  == 1)
{
	$Header = 'Accounts Maintained By "Pavitra Associates Pvt. Ltd."';
}
else if($_SESSION['society_client_id']  == 2)
{
	$Header = 'Accounts Maintained By "Developer."';
}
else if($_SESSION['society_client_id'] ==3)
{
	$Header = 'Accounts Maintained By "Shashank Society Service"'; 	
}
else if($_SESSION['society_client_id'] ==4)
{
	$Header = 'Accounts Maintained By "Sagar Mahashur"'; 	
}
else
{
	$Header = '';
}*/

if($_REQUEST["UnitID"] == "")
{
	echo "<script>alert('Error ! There are no UnitID passed to generate a bill');</script>";
	exit;
}
if($_REQUEST["UnitID"] == "" && $currentUrl == "")
{
	echo "<script>alert('Error ! There are no UnitID passed to generate a bill');</script>";
	exit;
}
$bBillType = 0;
if(isset($_REQUEST["BT"]))
{
	if($_REQUEST["BT"] == 1)
	{
		$bBillType = 1;
	}
	
}
if($bBillType == 1)
{
	$IsSupplementaryBill = 1;
}
else
{
	$IsSupplementaryBill = 0;
}
$BillDate = "";
$Id_Array= array();
$DueDate = "";
$BillNumber = "";
$BillNotes = "";
$total = "";
$BillFor_Msg = "";
$BillFor = $objFetchData->GetBillFor($_REQUEST["PeriodID"]);
$BillFor_Bill = "[".$m_objUtility->displayFormatBillFor($BillFor)."]";
//$tmpArray = explode(' ', $BillFor);
//$tmpArray[1] =  substr_replace($tmpArray[1], '', 2, -2);
//$BillFor = implode(" ",$tmpArray);*/
//$begin_endDate = $objFetchData->getBeginEndDate($_REQUEST["UnitID"], $_REQUEST["PeriodID"]);
$begin_endDate = $objFetchData->getBeginEndReceiptDate($_REQUEST["UnitID"], $_REQUEST["PeriodID"], $IsSupplementaryBill);
if($begin_endDate <> "")
{	
//Toshow recipt from 1st apil 2018 in all maintenace bills
if($_SESSION['society_id'] == 136)   // GARDENIA VASANT VALLEY RAGHUKUL CO-OP HOUSING SOCIETY LTD.
	{
		//$StartDate = getDisplayFormatDate('2018-04-01');
		$StartDate = getDisplayFormatDate('2019-04-01');     //add Fetch_data.php file in hardcoded in line no 592
	}
	else
	{
		$StartDate = getDisplayFormatDate($begin_endDate['BeginDate']);
	}
	//$StartDate = getDisplayFormatDate($begin_endDate['BeginDate']);
	$EndDate = getDisplayFormatDate($begin_endDate['EndDate']);									
}			
		
$objFetchData->GetSocietyDetails($objFetchData->GetSocietyID($_REQUEST["UnitID"]));
$objFetchData->GetMemberDetails($_REQUEST["UnitID"]);
$objectBillRegister = new CBillRegister($dbConn);
$data = array();
$wing_areaDetails = $objFetchData->getWing_AreaDetails($_REQUEST["UnitID"]);

$arSkipLedger = array(INTEREST_ON_PRINCIPLE_DUE,IGST_SERVICE_TAX,CGST_SERVICE_TAX,SGST_SERVICE_TAX,CESS_SERVICE_TAX,ROUND_OFF_LEDGER);
$BillRegisterData = $objFetchData->GetValuesFromBillRegister($_REQUEST["UnitID"], $_REQUEST["PeriodID"], $IsSupplementaryBill, $arSkipLedger);

if(sizeof($BillRegisterData) == 0)
{
	echo "<br><br>Bill Not Generated For Unit : " . $objFetchData->GetUnitNumber($_REQUEST['UnitID']) . " For Period : " . $BillFor;
	die();
}
else
{
	//setting Maintenance Bill read-unread flag
	$obj_genbill->setMaintenanceBillReadUnreadFlag($_REQUEST['UnitID'],$_REQUEST["PeriodID"],$_REQUEST['BT']);
}
//echo "skip:".$SkipLedger;
if($IsSupplementaryBill==1)
{
	
	$receiptDetails = $objFetchData->getReceiptDetailsEx($_REQUEST["UnitID"], $_REQUEST["PeriodID"], false, 0, true,$IsSupplementaryBill);
}
else
{
	$receiptDetails = $objFetchData->getReceiptDetailsEx($_REQUEST["UnitID"], $_REQUEST["PeriodID"], false, 0, true);
}
//$receiptDetails = $objFetchData->getReceiptDetailsEx($_REQUEST["UnitID"], $_REQUEST["PeriodID"], false, 0, true);
$iCounter = 0;
?>
<script> var HeaderAndAmount = new Array();</script>	 

<?php 
for($iVal = 0; $iVal < sizeof($BillRegisterData) ; $iVal++) 
{
	$BillDetails = $BillRegisterData[$iVal]["value"];
	
	$BillDate = getDisplayFormatDate($BillDetails->sBillDate);
	$DueDate = getDisplayFormatDate($BillDetails->sDueDate);
	$BillDisplayDueDate = getDisplayFormatDate($BillDetails->sBillDisplayDueDate);
	$BillNotes = $BillDetails->sNotes;
	$BillFor_Msg = $BillDetails->BillFor_Msg;
	$BillFont = $BillDetails->sFont;
	//$BillNumber = $BillDetails->sBillNumber;
	if($BillDetails->sHeader != INTEREST_ON_PRINCIPLE_DUE)
	{
		$HeaderAndAmount = array("key"=>$BillDetails->sHeader, "value"=> $BillDetails->sHeaderAmount, "voucher"=>$BillDetails->sVoucherID, "taxable" => $BillDetails->Taxable, "taxable_no_threshold" => $BillDetails->Taxable_no_threshold);
		array_push($data, $HeaderAndAmount);
	
		if($data[$iVal]["value"] <> '0.00' && $data[$iVal]["key"] <> ADJUSTMENT_CREDIT)
		{
			//echo "key:".$data[$iVal]["key"];
			if($data[$iVal]["key"] == INTEREST_ON_PRINCIPLE_DUE && $data[$iVal]["value"] > 0)
			{
				//echo "header:".$BillDetails->sHeader;
	    	?>
				<script> 
					//HeaderAndAmount.push("<?php //echo $BillDetails->sVoucherID . '@@@' . $BillDetails->sHeader . '@@@' . $BillDetails->Taxable ; ?>");
				</script>	 
			<?php 
			}
			else if($data[$iVal]["key"] <> INTEREST_ON_PRINCIPLE_DUE && $data[$iVal]["key"] <> ROUND_OFF_LEDGER &&  ($data[$iVal]["key"] <>  SERVICE_TAX && $data[$iVal]["key"] <> IGST_SERVICE_TAX &&  $data[$iVal]["key"] <> CGST_SERVICE_TAX && $data[$iVal]["key"] <> SGST_SERVICE_TAX &&  $data[$iVal]["key"] <> CESS_SERVICE_TAX ) )
			{
	    	?>
				<script> 
					HeaderAndAmount.push("<?php echo $BillDetails->sVoucherID . '@@@' . $BillDetails->sHeader . '@@@' . $BillDetails->Taxable . '@@@' . $BillDetails->Taxable_no_threshold; ?>");
				</script>	 
			<?php 
			}
		}
	}
	//print_r($data);
}

foreach($data as $key => $val)
{
	if($data[$key]['value'] <> 0)
	{
		array_push($Id_Array,$data[$key]['key']);
	}
	
}
$objFetchData->GetMemberDetails($_REQUEST["UnitID"],$BillDate);
$detail_values = $objFetchData->GetValuesFromBillDetails($_REQUEST["UnitID"],$_REQUEST["PeriodID"], $IsSupplementaryBill);				
//echo "PrevArrears";
//echo $detail_values[0]["PrevPrincipalArrears"];
//print_r($detail_values);
$getSocietyInfo=$m_objUtility->GetSocietyInformation($_SESSION['society_id']);

$bApplyServiceTax = $getSocietyInfo['apply_service_tax'];
$bApplyGSTOnInterest = $getSocietyInfo['apply_GST_on_Interest'];

if($_REQUEST['BT'] == 0){

	$ShowSuppBillBalanceInMaintBill = $getSocietyInfo['ShowSuppBillBalanceInMaintBill'];
}

$iDateDiff = $m_objUtility->getDateDiff(getDBFormatDate($BillDate), GST_START_DATE);
if($iDateDiff < 0)
{
	$bApplyServiceTax = 0;	
}

$BillNumber = $detail_values[0]["BillNumber"]; 				
$AdjCredit = $detail_values[0]["AdjustmentCredit"];
$InterestOnArrears = $detail_values[0]["BillInterest"];
$PrinciplePreviousArrears = $detail_values[0]["PrincipalArrears"];//$detail_values[0]["PrevPrincipalArrears"];
$IntrestOnPreviousarrears = $detail_values[0]["InterestArrears"];//$detail_values[0]["PrevInterestArrears"];
$BillTax = $detail_values[0]["BillTax"];
$IGST = $detail_values[0]["IGST"];
$CGST = $detail_values[0]["CGST"];
$SGST = $detail_values[0]["SGST"];
$CESS = $detail_values[0]["CESS"];
$LedgerRoundOff = $detail_values[0]["Ledger_round_off"];

$DetailNote = explode('::',$detail_values[0]["Note"]);
$sBillDetailNote = '';
foreach($DetailNote as $key => $value)
{
	$sBillDetailNote .= '<br>'.$value;
}
if($outstanding_result[0]['apply_Outstanding_amount'] == 0)
{			

	$value=8;
}
else
{
	$value=11;
}
if(($bApplyServiceTax == 1 || $BillTax > 0 || $IGST > 0 || $CGST >0 || $SGST > 0 || $CESS  > 0 || $ShowSuppBillBalanceInMaintBill == 1))
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
	if($ShowSuppBillBalanceInMaintBill == 1){

		$SuppBillBalanceDisplayText = $m_objUtility->SuppBillBalanceDisplayText($_REQUEST["PeriodID"]);

		if(empty($SuppBillBalanceDisplayText)){
			$SuppBillBalanceDisplayText = $getSocietyInfo['SuppBillBalanceDisplayText'];
		}

		$supplementDueDetail = $m_objUtility->getSupplementDues(getDBFormatDate($BillDate), $_REQUEST["UnitID"]);
		$supplementDue = $supplementDueDetail[0]['amount'];
		$value++;
	}
}
$showInBillDetails = $objFetchData->GetFieldsToShowInBill($_REQUEST["UnitID"]);
$show_wing = $showInBillDetails[0]["show_wing"];
$show_parking = $showInBillDetails[0]["show_parking"];
$show_area = $showInBillDetails[0]["show_area"];
$show_receipt = $showInBillDetails[0]["bill_method"];
$show_supply_receipt = $showInBillDetails[0]["show_reciept_on_supp"];
$show_shareCertificate = $showInBillDetails[0]["show_share"];
$bill_footer = $showInBillDetails[0]['bill_footer'];
$show_due_date = $showInBillDetails[0]["bill_due_date"];
$show_floor    = $showInBillDetails[0]["show_floor"];
$show_vertualAC = $showInBillDetails[0]["show_vertual_ac"];
$show_intercom = $showInBillDetails[0]["show_intercom"];
$specialChars = array('/','.', '*', '%', '&', ',', '(', ')', '"');
$unitNoForPdf = str_replace($specialChars,'',$objFetchData->objMemeberDetails->sUnitNumber);
$bDueDateNotMaxDate = false;
$timestamp1 = strtotime(getDBFormatDate($DueDate));
$timestamp2 = strtotime(PHP_MAX_DATE);
if($timestamp1 <> $timestamp2)
{
	$bDueDateNotMaxDate = true;
}
if($IsSupplementaryBill == "1" && $bDueDateNotMaxDate == false)
{
		$show_receipt = "0";
		$show_due_date = "0";
		$BillFor_Bill = "";
		if($BillFor_Msg != "")
		{
			$BillFor_Msg = "[" . $BillFor_Msg . "]";
		}
}
else if($IsSupplementaryBill == "1")
{
	$show_receipt = "0";
	$BillFor_Bill = "";	
	if($BillFor_Msg != "")
	{
		$BillFor_Msg = "[" . $BillFor_Msg . "]";
	}
}
if($BillFor_Msg != "")
{
	$BillFor_Bill .= $BillFor_Msg;
}
 //$getSocietyInfo=$m_objUtility->GetSocietyInformation($_SESSION['society_id']);
//$bApplyServiceTax = $getSocietyInfo['apply_service_tax'];
/*echo '<pre>';
print_r($m_objUtility->GetLedgerDetails(285));
echo '</pre>';*/

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Maintanence Bill</title>
 <script type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="js/ajax_new.js"></script>
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
	</style>
<script type="text/javascript" src="js/BillUpdate_20190409.js"></script>
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

var NewRowCounter=0;
	function AddNewRow()
	{
      
         // var item=$("#mainTable").find("tr").last();
		  NewRowCounter++;
		  //$("#mainTable").find("tr").last().prev().clone(true).insertAfter(item);
		  //document.getElementById('Submit').style.visibility='visible';
		  
		  <?php
		  	if(sizeof($Id_Array) > 0)
		  	{
		  		$SELECT_Query = 'SELECT `id`, concat_ws(" - ",`ledger_name`,`id`) FROM `ledger` where `id` NOT IN (' . implode(',', $Id_Array) . ','.ADJUSTMENT_CREDIT.','.IGST_SERVICE_TAX.','.CGST_SERVICE_TAX.",".SGST_SERVICE_TAX.','.CESS_SERVICE_TAX.','.ROUND_OFF_LEDGER.') and `show_in_bill` = 1 and `society_id` = '.$_SESSION['society_id'].'  and `supplementary_bill` = "'.$_REQUEST['BT'].'"';
		  	}
		  	else
		  	{
		  		$SELECT_Query = 'SELECT `id`, concat_ws(" - ",`ledger_name`,`id`) FROM `ledger` where `id` NOT IN ('.ADJUSTMENT_CREDIT.','.IGST_SERVICE_TAX.','.CGST_SERVICE_TAX.",".SGST_SERVICE_TAX.','.CESS_SERVICE_TAX.','.ROUND_OFF_LEDGER.') and `show_in_bill` = 1 and `society_id` = '.$_SESSION['society_id'].'  and `supplementary_bill` = "'.$_REQUEST['BT'].'"';
		  	}
		  ?>

          var newRow="<tr><td style='border:1px solid black;border-left:none;text-align:center;font-size:14px;'></td>";
		  newRow += "<td colspan=3 style='border:1px solid black;'><select name='particular"+NewRowCounter+"' id='particular"+NewRowCounter+"' style='width:30%;'><?php echo $Particular = $obj_genbill->comboboxEx($SELECT_Query);?></select></td>";
		  newRow +="<td align=right style='border:1px solid black;border-right:none;text-align:right;width:15%;font-size:14px;'> <input type='text' id='HeaderAmount"+NewRowCounter+"' name='HeaderAmount"+ NewRowCounter+"' value='0.00' style='text-align:right;background-color:#FFFF00;'  onBlur = 'extractNumber(this,2,true);' onKeyUp='extractNumber(this,2,true);' onKeyPress='return blockNonNumbers(this, event, true, true);' /></td></tr>";
		  
		  $("#mainTable").append(newRow);
		}
	
	function test()
	{
		document.getElementById('bill_address').style.left = "";
	}
	
	function PrintPage() 
	{
		//Get the print button and put it into a variable
		//alert("print called");
        //var btnEdit = document.getElementById("Edit");
		var btnPrint = document.getElementById("Print");
		var btnViewAsPDF = document.getElementById("viewbtn");
		var btnSendEmail;
		var btnDownloadPdf;
		var btnRegenerate;
		if (document.getElementById("send_email") != null)
		{
			btnSendEmail =document.getElementById("send_email");
		}
		
		if (document.getElementById("dwnbtn") != null)
		{
			btnDownloadPdf =document.getElementById("dwnbtn");
		}
		
		if (document.getElementById("Regenerate") != null)
		{
			btnRegenerate =document.getElementById("Regenerate");
		}
		
		
		<?php if($_SESSION['role']==ROLE_SUPER_ADMIN){?>
		 var btnEdit = document.getElementById("Edit");
		 if (document.getElementById("Edit") != null)
		{
		 	btnEdit.style.visibility = 'hidden';
		}
		 <?php }?>
        if (document.getElementById("Regenerate") != null)
		{
			btnRegenerate.style.visibility = 'hidden';;
		}
		
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
		<?php if($_SESSION['role']==ROLE_SUPER_ADMIN){?>
		 if (document.getElementById("Edit") != null)
		{
		 	btnEdit.style.visibility = 'visible';
		}
		 <?php }?>
		
		btnRegenerate.style.visibility = 'visible';;
		btnPrint.style.visibility = 'visible';
		btnViewAsPDF.style.visibility = 'visible';
		if (document.getElementById("send_email") != null)
		{
			btnSendEmail.style.visibility = 'visible';
		}
		
		if (document.getElementById("dwnbtn") != null)
		{
			btnDownloadPdf.style.visibility = 'visible';
		}
	}
	
	function ViewPDF(unitID,periodID)
	{
		var gencnt = 1;
		var sData = document.getElementById('bill_main').innerHTML;
		//var sData = document.body.innerHTML;
		var bDownload = "0";
		var sHeader = '<html><head>';
		sHeader += '<style> ';
		sHeader += 'table {	border-collapse: collapse; } ';
		sHeader += 'table, th, td { border: 0px solid black; text-align: left; padding-top:0px; padding-bottom:0px; } ';
		sHeader += '</style>';	
		sHeader +=	'</head><body>';
		
		var sFooter =  '</body></html>';
		
		sData = sHeader + sData + sFooter;
		
		//var sFileName = "bill_<?php //echo $_REQUEST['UnitID']; echo $_REQUEST['PeriodID']; ?>" ;
		var sUnitNo = document.getElementById('owner_unit').innerHTML;
		var sFileName = "bill-<?php echo $objFetchData->objSocietyDetails->sSocietyCode; ?>-" +  "<?php echo $unitNoForPdf; ?>" + "-<?php echo $BillFor; ?>-<?php echo $IsSupplementaryBill ?>" ;
		var sURL = "viewpdf.php";
		
		var obj = {"data":sData, "file":sFileName};
		
		//alert(sData);
		//remoteCall(sURL, obj, 'queryResult');
		
		//window.open('viewpdf.php?filename=' + sFileName + '&data=' + sData);
		
		//return;
		<?php if(isset($_REQUEST['gen']))
		{
			?>
			document.getElementById('bill_main').innerHTML = '';
			document.getElementById('Print').style.display = 'none';
			document.getElementById('viewbtn').style.display = 'none';
			document.getElementById('viewlog').style.display = 'none';
			if(document.getElementById('Edit') != null)
			{
				document.getElementById('Edit').style.display = 'none';
			}
			
			if(document.getElementById('dwnbtn') != null)
			{
				document.getElementById('dwnbtn').style.display = 'none';
			}
			
			document.getElementById('send_email').style.display = 'none';
			document.getElementById('viewlog').style.display = 'none';
			document.getElementById('msg').innerHTML = 'Exporting...';
			<?php
		}
		?>
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
					"period": "<?php echo $BillFor; ?>",
					"BT": "<?php echo $IsSupplementaryBill ?>","bDownload":bDownload} ,
			success : function(data)
			{	
				<?php if(!isset($_REQUEST['gen']))
				{
					?>
					if(bIsSendEmail == true)
					{
						 bIsSendEmail = false;
						 sendEmail(unitID,periodID,false,'<?php echo $objFetchData->objMemeberDetails->sEmail; ?>', '<?php echo $IsSupplementaryBill ?>');
					}
					else
					{
						if(document.getElementById("dwnflag").value == "1")
						{
							var downLoadLink = "viewpdf.php?society=<?php echo $objFetchData->objSocietyDetails->sSocietyCode; ?>&period=<?php echo $BillFor; ?>&filename=" + sFileName +"&bDownload=" + bDownload;
							document.getElementById("dwnflag").value == "0";
							window.open(downLoadLink, '_blank');
						}
						else
						{
							window.open('maintenance_bills/<?php echo $objFetchData->objSocietyDetails->sSocietyCode; ?>/<?php echo $BillFor; ?>/' + sFileName + '.pdf');
						}
					}
					<?php
				}
				else
				{
					//echo "BIll For ".$BillFor;
				?>
					//document.getElementById('msg').style.color = '#FF0000';
					//alert(sFileName);
					document.getElementById('msg').innerHTML = '<font color="#009900"><a href="maintenance_bills/<?php echo $objFetchData->objSocietyDetails->sSocietyCode; ?>/<?php echo $BillFor; ?>/' + sFileName + '.pdf" target="blank"><img src="images/pdficon.png" /></a></font>';
					var x = localStorage.getItem("genpdfcnt");
					console.log("x",x);
					x=  Number(x)+1;
					localStorage.setItem('genpdfcnt', x);
					
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
</head>
<body>
	<div id="msg" style="color:#FF0000;"></div>
    <center>
    <Form Name ="form1" Method ="POST" ACTION =<?php echo "Maintenance_bill_edit.php?UnitID=". $_REQUEST["UnitID"] ."&PeriodID=". $_REQUEST["PeriodID"] ."" ?> 				>
	<div align="center" style="alignment-adjust:middle; left:80px;">
    	<?php 
		if(isset($_REQUEST['edt']))
		{?>
		<input type="hidden" id='request_type' name="request_type" value="edt">
		<?php } ?>
		<?php if(!isset($_REQUEST['edt'])){?>
        <INPUT TYPE="button" id="Print" onClick="PrintPage()" name="Print" value="Print" width="300" style="width:60px;height:30px; font-size:20px" />
        <?php //if($_SESSION['role']==ROLE_SUPER_ADMIN || ($_SESSION['role']== ROLE_ADMIN && $_REQUEST["PeriodID"] == $latestPeriod))
		if(($_SESSION['role']==ROLE_SUPER_ADMIN &&  $_SESSION['is_year_freeze'] == 0) || ($_SESSION['profile'][PROFILE_EDIT_BILL] == 1 && $_REQUEST["PeriodID"] == $latestPeriod))
		{?>
        <INPUT TYPE="button" id="Edit" onClick="window.location.href='Maintenance_bill.php?UnitID='+ <?php echo $_REQUEST["UnitID"]?> + '&PeriodID='+ <?php echo $_REQUEST["PeriodID"]?> +'&BT='+ <?php echo $_REQUEST["BT"]?>+ '&edt'" name="Edit" value="Edit" width="300" style="width:60px;height:30px; font-size:20px" />
       	
        <INPUT TYPE="button" id="Regenerate" onClick="Regenerate_Bill(<?php echo $_REQUEST["UnitID"]?>,<?php echo $_REQUEST["PeriodID"]?>,<?php echo $_REQUEST["BT"]?>)" name="Regenerate" value="Regenerate" width="300" style="height:30px; font-size:20px" />
 		<?php }} 
		
		if(isset($_REQUEST['edt']) ){
        	if(($_SESSION['role']==ROLE_SUPER_ADMIN ) || ($_SESSION['profile'][PROFILE_EDIT_BILL] == 1 && $_REQUEST["PeriodID"] == $latestPeriod ))
		{?>
        <INPUT TYPE="button" id="Update" name="Update" value="Update" onClick="jsBillUpdate(HeaderAndAmount,NewRowCounter)" width="300" style="left:550;width:100px;height:30px; font-size:20px;">
        <INPUT TYPE="hidden" id="txtInterestOnArrears" name="txtInterestOnArrears" value="<?php echo INTEREST_ON_PRINCIPLE_DUE ?>">
        
 		<?php }
		else
		{?>
				<script>window.location.href='Maintenance_bill.php?UnitID='+ <?php echo $_REQUEST["UnitID"]?> + '&PeriodID='+ <?php echo $_REQUEST["PeriodID"]?>+'&BT='+<?php echo $IsSupplementaryBill;?></script>
		<?php }
		
		}?>
    </div>
    <div id="bill_main" style="width:90%;">
    <div style="border:1px solid black;">
       
        <table style="width:100%;">
        <tr>
        
        
        <?php 
		if($objFetchData->objSocietyDetails->sShowLogoInBill == 1)
		{?>
        <td>
        <div style="float:left;">
			<?php if($objFetchData->objSocietyDetails->sSocietyLogo  <> "")
			{?>
        	           <img src="<?php echo $objFetchData->objSocietyDetails->sSocietyLogo ?>" style="width:60px;margin-left: 10px;"></div>
			<?php 
			}
			else
			{?>
        	  		<img src="SocietyLogo/main/default.png" style="width:60px;margin-left: 10px;">
				<!--<img src="SocietyLogo/QRCode/RHG_TEST_society_QR.png" style="width:60px;margin-left: 10px;">-->
				</div>
			<?php
        	}?>
			  </td>
       
		<?php }?>
      
      	 <?php if($_SESSION['society_id'] == 448)
		 {?>	
          <td style="width:80%">
          <?php }
		  else{?>
          <td>
          <?php }?>
          <div id="bill_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:16px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
            <div id="society_reg" style="font-size:14px;"><?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?></div>
            <div id="society_address"; style="font-size:12px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?>
            <?php if($objFetchData->objSocietyDetails->sShowEmailAndPostalBillHeader == 1)
			{?>
				 <span style="font-size:12px;"><?php echo "&nbsp;" .$objFetchData->objSocietyDetails->sSocietyPinCode;
				 echo "&nbsp;Email&nbsp;:&nbsp;" .$objFetchData->objSocietyDetails->sSocietyEmail;?></span>	
			<?php }?>
            </div>
            <?php if($objFetchData->objSocietyDetails->sSocietyGSTINNo <> '')
			{ ?>
            <div id="society_gstin"; style="font-size:12px;"><span>GSTIN No :&nbsp;&nbsp;</span><b><?php echo $objFetchData->objSocietyDetails->sSocietyGSTINNo; ?></b></div>
            <?php }?>
        </div>
       
        
        <div id="bill_subheader" style="text-align:center;">
            <div style="font-weight:bold; font-size:14px;"> <?php echo $bBillType = $IsSupplementaryBill ?  "Supplementary" : "Maintenance "  ?> Bill <?php echo $BillFor_Bill; ?></div>
             </div>
             </td>
              <?php if($_SESSION['society_id'] == 448)
		 	{?>
             <td style="font-weight:bold; font-size:13px; width:10%"> <b>Flat No : <?php echo $objFetchData->objMemeberDetails->sUnitNumber; ?></b></td>
        	<?php }?>
        </tr>
        </table>
       
        <div id="bill_details" style="text-align:center;border-top:1px solid black;font-size:<?php echo $BillFont?>px;">
            <table style="width:100%;">
            	<tr>
                	<td style="width:15.8%;">Name :</td>
                    <td id='owner_name' style="font-weight:bold;"><?php echo $objFetchData->objMemeberDetails->sMemberName; ?></td>                    
              	<?php if($objFetchData->objMemeberDetails->sMemberGstinNo <> "")
				{?>
                	<td align="right" style="width:8.8%;">GSTIN No :</td>
                    <td id='owner_gstin_no' style="font-weight:bold;width:13%;"><?php echo $objFetchData->objMemeberDetails->sMemberGstinNo; ?></td>
                    <?php }?>                    
              	</tr>
            </table>
            <table style="width:100%;">
                <tr>
                <td style= "width:74%">
                <table style="width:124%;">
                <tr >
                  <td id='owner_unit' style="width: 16%;" ><?php echo $unitText;?> :</td><td style="font-weight:bold; width:12%" colspan="2"><?php echo $objFetchData->objMemeberDetails->sUnitNumber; ?></td>
                
                <?php if($show_floor == false && $show_wing == false)
				{
					?>
                    <td>&nbsp;</td>
                    <?php
				}
				else
				{
					?>
                	<?php if($show_floor) { ?><td style="width: 8%;">Floor No :</td><td colspan="2" width="4%"><?php echo $wing_areaDetails[0]['floor_no'] ; ?></td>
                	<?php if(!$show_wing) {
						?>
                        	<td>&nbsp;</td>
                        <?php
					}}?>
                 	<?php if($show_wing) { ?><td style="width: 6%;">Wing :</td><td><?php echo $wing_areaDetails[0]['wing'] ?></td></tr>
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
					{?>
						<?php if($show_area) {?><tr><td style="width: 16%;">Area :</td><td style="width:12%" colspan="2"><?php echo $wing_areaDetails[0]['area'];echo ($_SESSION['society_id'] == 261) ? ' Sq.Mtr' : ' Sq.Ft'; ?></td>
						<?php
						}
					
					} 
					?>
					<?php if($show_parking) { ?><td style="width: 10%;">Parking No :</td><td colspan="3"> <?php echo "".$objFetchData->objMemeberDetails->sParkingNumber; ?></td></tr>
					<?php }
				}
				else
				{?>
                	<tr><td>&nbsp;</td></tr>
                <?php } ?>
                
                
                 <?php 
				if($show_shareCertificate || $show_vertualAC)
				{
					if($show_shareCertificate)
					{?>
						<tr><td style="width: 16%;" id='shareCertificate'>Share Certificate No :</td><td style="width:12%" colspan="2"><?php echo $objFetchData->GetShareCertificateNo($_REQUEST["UnitID"]); ?></td>
						
					
					<?php } 
					?>
					<?php if($show_vertualAC) { ?><td style="width: 14%;">Virtual A/C No. :</td><td colspan="3"><b> <?php echo $wing_areaDetails[0]['virtual_acc_no'];?></b></td></tr>
					<?php }
				}
				else
				{?>
                	<!--<tr><td>&nbsp;</td></tr>-->
                <?php } ?>

                 <?php 
				if($show_intercom)
				{?>
					
						<tr><td style="width: 16%;" id='shareCertificate'>Intercom No. :</td><td style="width:12%" colspan="2"><b><?php echo  $wing_areaDetails[0]['intercom_no']; ?></b></td><tr>
						
					
					<?php } ?>
             
               </table>
                	
            </td>
            <td style="width:20%">
            <table>
	            <tr>
					<!-- add invoice no for FLYEDGE CO-OP PREMISES SOCIETY -->
	            	<td style="width:10%"> <?php echo (($_SESSION['society_id'] == 422)? 'Invoice No' : 'Bill No')?></td>
	                <td id='bill_no' style="width:15%;"><?php echo $BillNumber ?></td>
	            </tr>
				<tr>
	            	<td style="width:10%">Bill Date :</td>
	                <td id='bill_date' style="width:15%;"><?php echo $BillDate ?></td>
	            </tr>
	            <tr>
	            	<td style="width:10%;<?php if(!$show_due_date) { echo 'visibility:hidden;';} ?>">Due Date :</td>
	                   	<td id='bill_due' style="width:15%;<?php if(!$show_due_date) { echo 'visibility:hidden;';} ?>"><?php echo $BillDisplayDueDate ?></td>
	            </tr>
            </table>
            
            </td></tr></table>
            <?php 
            	if($bApplyServiceTax)
            	{	
            		?>
		            <table style="width: 100%;">
		            	<tr>
		            		<td style="width: 15.5%;">SAC :</td>
		            		<td style="width: 63.4%;">999598</td>
		            		<td style="width: 7.5%">POS :</td>
		            		<td>MH (27)</td>
		            	</tr>
		            </table>
		            <?php
	        	}
	        ?>
           <?php  //}?>
        </div>
        <div id="bill_charges">
        	<table  style="width:100%;font-size:14px;" id="mainTable">
                <tr>
                <th style="text-align:center; width:10%; border:1px solid black;border-left:none;font-size: <?php echo $BillFont?>px;">Sr. No.</th>
                <th style="text-align:center; border:1px solid black;font-size: <?php echo $BillFont?>px;" colspan="3">Particulars of Charges</th>
                <th style="text-align:center; width:20%; border:1px solid black;border-right:none;font-size:<?php echo $BillFont?>px;">Amount (Rs.)</th>
                </tr>
                
                <?php
                	$counter = 1;
					$SubTotal = 0;
					$bNonGSTParticularsAdded = false;
					$bGSTParticularsAdded = false;

					$dNonGstLedgerTotal = 0;
					$dGstLedgerTotal = 0;

					$taxableLedgerCount = 0;

					for($i=0; $i < sizeof($data) ;$i ++)
					{
						$LedgerDetails = $m_objUtility->GetLedgerDetails($data[$i]["key"]);
						
						if($bApplyServiceTax && $bNonGSTParticularsAdded == false && $LedgerDetails[$data[$i]["key"]]['General']['taxable'] == 0)
						{
							$bNonGSTParticularsAdded = true;
							?>
								<tr>
				                	<td style="border:1px solid black;border-left:none;text-align:center;font-size:<?php echo $BillFont?>px;font-weight: bold;">A</td>
				                	<td colspan="3" style="border:1px solid black;text-align:left;font-size:<?php echo $BillFont?>px;padding-left:3px;font-weight: bold;">Non GST Charges</td>
				                	<td style="border:1px solid black;border-right:none;text-align:right;width:15%;font-size:<?php echo $BillFont?>px;"></td>
				                </tr>
							<?php
						}

						if($bApplyServiceTax && $bGSTParticularsAdded == false && $LedgerDetails[$data[$i]["key"]]['General']['taxable'] == 1)
						{
							$taxableLedgerCount++;

							if($bNonGSTParticularsAdded)
							{
								?>
									<tr>
				                		<td style="border:1px solid black;border-left:none;text-align:center;font-size:<?php echo $BillFont?>px;;font-weight: bold;"></td>
				                		<td colspan="3" style="border:1px solid black;text-align:right;font-size:<?php echo $BillFont?>px;padding-right:3px;font-weight: bold;">Sub Total of Non GST Charges - A</td>
				                		<td style="border:1px solid black;border-right:none;text-align:right;width:15%;font-size:<?php echo $BillFont?>px;font-weight: bold;"><?php echo number_format($dNonGstLedgerTotal, 2); ?></td>
				                	</tr>
								<?php
							}

							$bGSTParticularsAdded = true;
							if($_SESSION['society_id'] == 418 && $IsSupplementaryBill == 1)
							{
							}
							else
							{
							?>
								<tr>
				                	<td style="border:1px solid black;border-left:none;text-align:center;font-size:<?php echo $BillFont?>px;font-weight: bold;">B</td>
				                	<td colspan="3" style="border:1px solid black;text-align:left;font-size:<?php echo $BillFont?>px;padding-left:3px;font-weight: bold;">GST Charges</td>
				                	<td style="border:1px solid black;border-right:none;text-align:right;width:15%;font-size:<?php echo $BillFont?>px;"></td>
				                </tr>
							<?php
							}
						}

						//if($data[$i]["key"] != INTEREST_ON_PRINCIPLE_DUE && $data[$i]["key"] != ADJUSTMENT_CREDIT)
						if($data[$i]["key"] != ADJUSTMENT_CREDIT)
						{
							if($data[$i]["value"] <> 0)
							{
								$bIsParticular = true;
								//echo "amount:".$data[$i]["value"];
								//echo "value:".$data[$i]["key"];
								if($data[$i]["key"] == INTEREST_ON_PRINCIPLE_DUE && $data[$i]["value"] >= 0)
								{
									$bIsParticular = false;
									$InterestOnArrears = $data[$i]["value"];
								}
								elseif(($data[$i]["key"] == SERVICE_TAX && $data[$i]["value"] >= 0) || ($data[$i]["key"] == IGST_SERVICE_TAX && $data[$i]["value"] >= 0 )|| ($data[$i]["key"] == SGST_SERVICE_TAX && $data[$i]["value"] >= 0 ) ||( $data[$i]["key"] == CGST_SERVICE_TAX && $data[$i]["value"] >= 0 )||( $data[$i]["key"] == CESS_SERVICE_TAX && $data[$i]["value"] >= 0)  || ( $data[$i]["key"] == ROUND_OFF_LEDGER && $data[$i]["value"] <> 0))
								{
									$bIsParticular = false;
									$ServiceTax += $data[$i]["value"];
								}
								
								if($bIsParticular == true)
								{
									
									$ParticularLedger = $LedgerDetails[$data[$i]["key"]]['General']['ledger_name'];

									/*if($bApplyServiceTax && $LedgerDetails[$data[$i]["key"]]['General']['taxable'] == 1)
									{
										$ParticularLedger .= ' (T)';
									}*/

									//edit mode
									if(isset($_REQUEST['edt']))
									{
									echo "<tr>";
									echo "<td style='border:1px solid black;border-left:none;text-align:center;font-size:14px;'>".$counter."</td>";
									echo "<td colspan=3 style='border:1px solid black;text-align-left;font-size:".$BillFont."px;'>". strtoupper($ParticularLedger) ."</td>";
									echo "<td align=right style='border:1px solid black;border-right:none;text-align:right;width:15%;font-size:".$BillFont."px;'><input type='text' name='' id='" . $data[$i]["voucher"] . "' value='". ($data[$i]["value"]) ."' style='text-align:right;background-color:#FFFF00;'  onBlur = 'extractNumber(this,2,true);' onKeyUp='extractNumber(this,2,true);' onKeyPress='return blockNonNumbers(this, event, true, true);' /></td>";
									echo "</tr>";
									}
									else
									{
										echo "<tr><td style='border:1px solid black;border-left:none;text-align:center;font-size:".$BillFont."px;'>".$counter."</td><td colspan=3 style='border:1px solid black;text-align-left;font-size:".$BillFont."px;padding-left:3px;'>". strtoupper($ParticularLedger) ."</td><td align=right style='border:1px solid black;border-right:none;text-align:right;width:15%;font-size:".$BillFont."px;'>". number_format($data[$i]["value"], 2) ."</td></tr>";
									}
									$SubTotal += $data[$i]["value"];

									if($LedgerDetails[$data[$i]["key"]]['General']['taxable'] == 0)
									{
										$dNonGstLedgerTotal += $data[$i]["value"];
									}
									else
									{
										$dGstLedgerTotal += $data[$i]["value"];
									}
								}
							}
						}
						else
						{
							if($data[$i]["key"] == INTEREST_ON_PRINCIPLE_DUE)
							{
								$InterestOnArrears = $data[$i]["value"];
							}
							else if($data[$i]["key"] == ADJUSTMENT_CREDIT)
							{
								$AdjCredit = $data[$i]["value"];
							}
						}
						if($data[$i]["value"] <> 0 && $data[$i]["key"] <> INTEREST_ON_PRINCIPLE_DUE && $data[$i]["key"] <> ADJUSTMENT_CREDIT && $data[$i]["key"] <> IGST_SERVICE_TAX && $data[$i]["key"] <> CGST_SERVICE_TAX && $data[$i]["key"] <> SGST_SERVICE_TAX && $data[$i]["key"] <> CESS_SERVICE_TAX && $data[$i]["key"] <> ROUND_OFF_LEDGER)
						{
							$counter++;
						}
						else if($data[$i]["key"] == INTEREST_ON_PRINCIPLE_DUE && $data[$i]["value"] < 0)
						{
							$counter++;
						}
					}
					
					if($bApplyServiceTax && $taxableLedgerCount == 0)
					{
						?>
						<tr>
	                		<td style="border:1px solid black;border-left:none;text-align:center;font-size:<?php echo $BillFont?>px;font-weight: bold;"></td>
	                		<td colspan="3" style="border:1px solid black;text-align:right;font-size:<?php echo $BillFont?>px;padding-right:3px;font-weight: bold;">Sub Total of Non GST Charges - A</td>
	                		<td style="border:1px solid black;border-right:none;text-align:right;width:15%;font-size:<?php echo $BillFont?>px;font-weight: bold;"><?php echo number_format($dNonGstLedgerTotal, 2); ?></td>
	                	</tr>
                        <?php if($_SESSION['society_id'] == 418 && $IsSupplementaryBill == 1)
							{
								
							}
							else
							{?>
								
				        <tr>
		                	<td style="border:1px solid black;border-left:none;text-align:center;font-size:<?php echo $BillFont?>px;font-weight: bold;">B</td>
		                	<td colspan="3" style="border:1px solid black;text-align:left;font-size:<?php echo $BillFont?>px;padding-left:3px;font-weight: bold;">GST Charges</td>
		                	<td style="border:1px solid black;border-right:none;text-align:right;width:15%;font-size:<?php echo $BillFont?>px;"></td>
		                </tr>
		                <?php
							}
		                $bGSTParticularsAdded = true;
					}

					if($bApplyServiceTax && $bGSTParticularsAdded)
					{
						?>
							<tr>
		                		<td style="border:1px solid black;border-left:none;text-align:center;font-size:<?php echo $BillFont?>px;font-weight: bold;"></td>
                                <?php if($_SESSION['society_id'] == 418 && $IsSupplementaryBill == 1)
							{?>
		                		<td colspan="3" style="border:1px solid black;text-align:right;font-size:<?php echo $BillFont?>px;padding-right:3px;font-weight: bold;">Sub Total of GST Charges </td>
                                <?php }
								else
								{?>
									<td colspan="3" style="border:1px solid black;text-align:right;font-size:<?php echo $BillFont?>px;padding-right:3px;font-weight: bold;">Sub Total of GST Charges - B</td>
								<?php }?>
		                		<td style="border:1px solid black;border-right:none;text-align:right;width:15%;font-size:<?php echo $BillFont?>px;font-weight: bold;"><?php echo number_format($dGstLedgerTotal, 2); ?></td>
		                </tr>
						<?php
					}

                if(isset($_REQUEST['edt'])){?>
                <tr><td style='border:1px solid black;border-left:none;text-align:center;font-size:<?php echo $BillFont?>px;'></td><td colspan=3 style='border:1px solid black;text-align-left;font-size:<?php echo $BillFont?>px;'></td><td align=right style='border:1px solid black;border-right:none;text-align:right;width:15%;font-size:14px;'> <!--<input type="button"  name="Submit" id="Submit" value="SUBMIT" onClick="SubmitBillRows();" style=" visibility:hidden;"/>--><input type="button"  name="ADD" id="ADD" value="ADD" onClick="AddNewRow();"/></td></tr>
           		<?php }?>
           </table>
           <?php
		   		$BalanceAmout = 0;
				/*if($IsSupplementaryBill)
				{
					$BalanceAmout = $SubTotal;
				}
				else
				{*/
					$BalanceAmout = $SubTotal + $AdjCredit + $InterestOnArrears + $ServiceTax + $PrinciplePreviousArrears + $IntrestOnPreviousarrears + $IGST + $SGST + $CGST + $CESS+$LedgerRoundOff;

				//}
			?>
           
           <table style="width:100%;font-size:14px;">

		   <?php if($outstanding_result[0]['apply_Outstanding_amount'] == 0)
				{
					$value1=8;
				}
				else
				{
					$value1=11;
				}
	
			?>
		   
          		<tr>
                	<!--<td colspan="3" rowspan="<?php echo $IsSupplementaryBill ? "2" : "7" ?>" style="width:<?php echo $IsSupplementaryBill ? "60%" : "50%" ?>">E.& O.E.</td>-->
					

					 <td colspan="3" rowspan="<?php echo ($bApplyServiceTax == 1 || $BillTax > 0 || $CGST >0 || $SGST >  0 ) ?  $value : $value1 ?>" style="width:50%;padding-left:3px;font-size: 12px; border-top: 1px solid black;">
                    <label>E.& O.E.</label><br/><br/><?php echo $sBillDetailNote;?>
                     </td>
                    	
                    <?php if($_SESSION['society_id'] == 418 && $IsSupplementaryBill == 1)
					{?>
                    <td style="width:20%;border:1px solid black; border-top: 1px solid black;;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">Sub Total </td>
                    <?php }
                    else
					{?>
                    <td style="width:20%;border:1px solid black; border-top: 1px solid black;;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">Sub Total <?php echo $bApplyServiceTax ? "(A + B)" : ""; ?></td>
                    <?php }?>
                    
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;border-top:none;font-size: <?php echo $BillFont?>px;border-top: 1px solid black;"><?php echo number_format($SubTotal,2); ?></td>
                </tr> 
				<?php 
				/*if(!$IsSupplementaryBill)
				{*/
					 if(isset($_REQUEST['edt'])){?>
                <tr>
                	<td style="width:20%;border:1px solid black;font-size: <?php echo $BillFont?>px;" colspan="2">Adjustment Credit/Rebate</td>
					<td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;"><input type="text" name="AdjustmentCredit" id="AdjustmentCredit" value="<?php echo number_format($AdjCredit,2); ?>" onBlur = "extractNumber(this,2,true);" onKeyUp="extractNumber(this,2,true);" onKeyPress="return blockNonNumbers(this, event, true, true);"  style="text-align:right;background-color:#FFFF00;" /></td>
                </tr>
				<?php }
				else { ?>
				<tr>
                	<td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">Adjustment Credit/Rebate</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;font-size:<?php echo $BillFont?>px;"><?php echo number_format($AdjCredit,2); ?></td>
                </tr>
				<?php } ?>
                <tr>
                	<td style="width:20%;border:1px solid black;padding-left:3px;font-size: <?php echo $BillFont?>px;" colspan="2">Previous Arrears</td>
                    <td id="sub_total" style="text-align:right;width:20%; border:none;"></td>
                </tr>
                <?php if(isset($_REQUEST['edt'])){?>
                <tr>
                	<td style="width:14%;border:1px solid black;text-align:right;">Principal&nbsp;</td>
                    <td id="sub_total" style="text-align:right;width:16%;border:1px solid black;">
						<?php if($_SESSION['role']==ROLE_SUPER_ADMIN){ ?>
                        <input type="text" name="PrinciplePreviousArrears" id="PrinciplePreviousArrears" value="<?php echo ($PrinciplePreviousArrears); ?>" onKeyUp="extractNumber(this,2,true);" onKeyPress="return blockNonNumbers(this, event, true, true);" style="text-align:right;background-color:#FFFF00;" /> 
                        <?php } else { ?>
                        <input type="text" name="PrinciplePreviousArrears" id="PrinciplePreviousArrears" value="<?php echo ($PrinciplePreviousArrears); ?>" style="text-align:right;" readonly />
                        <?php } ?>
                    </td>
                    <td style="border:none;"></td>
                </tr>
                <?php }else{?>
                 <tr>
                	<td style="width:14%;border:1px solid black;text-align:right;font-size: <?php echo $BillFont?>px;">Principal&nbsp;</td>
                    <td id="sub_total" style="text-align:right;width:16%;border:1px solid black;font-size:<?php echo $BillFont?>px;"><?php echo number_format($PrinciplePreviousArrears,2); ?></td>
                    <td style="border:none;"></td>
                </tr>
                <?php }
				if(isset($_REQUEST['edt'])){?>
                <tr> 
					<!-- add penalty for ROYAL COURT society  -->
					<td style="width:14%;border:1px solid black;text-align:right;font-size:<?php echo $BillFont?>px;"><?php echo (($_SESSION['society_id'] == 417)? 'Penalty' : 'Interest')?>&nbsp;</td>
                    <td id="sub_total" style="text-align:right;width:16%;border:1px solid black;">
						<?php if($_SESSION['role']==ROLE_SUPER_ADMIN){ ?>
                        <input type="text" name="" id="IntrestOnPreviousarrears" value="<?php echo ($IntrestOnPreviousarrears); ?>" onKeyUp="extractNumber(this,2,true);" onKeyPress="return blockNonNumbers(this, event, true, true);" style="text-align:right;background-color:#FFFF00;" />
                        <?php } else { ?>
                        <input type="text" name="" id="IntrestOnPreviousarrears" value="<?php echo ($IntrestOnPreviousarrears); ?>" style="text-align:right;" readonly />
                        <?php } ?>
                    </td>
                    <td style="text-align:right;width:20%;border:1px solid black;border-right:none;border-top:none;"><?php echo number_format($IntrestOnPreviousarrears + $PrinciplePreviousArrears,2); ?></td>
                </tr>
                <?php }else{?>
                <tr>
				<td style="width:14%;border:1px solid black;text-align:right;font-size:<?php echo $BillFont?>px;"><?php echo (($_SESSION['society_id'] == 417)? 'Penalty' : 'Interest' )?>&nbsp;</td>
                    <td id="sub_total" style="text-align:right;width:16%;border:1px solid black;font-size:<?php echo $BillFont?>px;"><?php echo number_format($IntrestOnPreviousarrears,2); ?></td>
                    <td style="text-align:right;width:20%;border:1px solid black;border-right:none;border-top:none;font-size:<?php echo $BillFont?>px;"><?php echo number_format($IntrestOnPreviousarrears + $PrinciplePreviousArrears,2); ?></td>
                </tr>
                <?php }
					//} // end of IsSupplemetary Bill?>

               <?php if(isset($_REQUEST['edt'])){?>
				<tr>
                	<td style="width:20%;border:1px solid black;font-size:<?php echo $BillFont?>px;" colspan="2"><?php echo (($_SESSION['society_id'] == 417)? 'Penalty' : 'Interest on Arrears')?></td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;"><input  type="text" name="<?php echo INTEREST_ON_PRINCIPLE_DUE; ?>" id="InterestOnPrincipleDue" value="<?php echo ($InterestOnArrears); ?>"  onBlur = "extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);"  style="text-align:right; background-color:#FFFF00;" /></td>
                </tr>
                <?php }
				else{ ?>
				<?php
					?>
					<tr>
                     <?php if($_SESSION['society_id'] == 417)
					{?>
                    <td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">Penalty </td>
                    <?php }
					
					else{?>
						<td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2"> Interest on Arrears <?php echo (($bApplyServiceTax && $bApplyGSTOnInterest) ? "(C)" : "");  ?></td>
					<?php }?>
		            	
		                <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;font-size:<?php echo $BillFont?>px;"><?php echo number_format($InterestOnArrears,2); ?></td>
		            </tr>
		        	<?php
		        }
		        //if($bApplyServiceTax == 1 || $BillTax > 0 )
				//{
					if($bApplyServiceTax == 1 || $SGST > 0)
                    {?>
	                <tr>
                     <?php if($_SESSION['society_id'] == 418 && $IsSupplementaryBill == 1)
					{?>
                    <td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">SGST @ <?php echo $getSocietyInfo['sgst_tax_rate'] ?>% </td>
                    <?php 
					}
					else{?>
						<td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">SGST @ <?php echo $getSocietyInfo['sgst_tax_rate'] ?>% on (B<?php echo ($bApplyGSTOnInterest ? " +  C" : "") ?>)</td>
						<?php 
					}?>
	                	
	                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;font-size:<?php echo $BillFont?>px;"><?php echo number_format($SGST,2); ?></td>
	                </tr>
                    <?php }
					if($bApplyServiceTax == 1 || $CGST > 0 )
					{?>
                   <tr>
                    <?php if($_SESSION['society_id'] == 418 && $IsSupplementaryBill == 1)
					{?>
                    <td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">CGST @ <?php echo $getSocietyInfo['cgst_tax_rate'] ?>%</td>
                    <?php 
					}
					else{?>
						<td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">CGST @ <?php echo $getSocietyInfo['cgst_tax_rate'] ?>% on (B<?php echo ($bApplyGSTOnInterest ? " +  C" : "") ?>)</td>
						<?php 
					}?>
	                	
	                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;font-size:<?php echo $BillFont?>px;"><?php echo number_format($CGST,2); ?></td>
	                </tr>
                    <?php }
					if($IGST > 0 )
					{?>
                   <tr>
                    <?php if($_SESSION['society_id'] == 418 && $IsSupplementaryBill == 1)
					{?>
                    <td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">IGST @ <?php echo $getSocietyInfo['igst_tax_rate'] ?>%</td>
                    <?php 
					}
					else{?>
						<td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">IGST @ <?php echo $getSocietyInfo['igst_tax_rate'] ?>% on (B<?php echo ($bApplyGSTOnInterest ? " +  C" : "") ?>)</td>
						<?php 
					}?>
	                	
	                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;font-size:<?php echo $BillFont?>px;"><?php echo number_format($IGST,2); ?></td>
	                </tr>
                    <?php }
					if($CESS > 0 )
					{?>
                   <tr>
                    <?php if($_SESSION['society_id'] == 418 && $IsSupplementaryBill == 1)
					{?>
                    <td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">CESS @ <?php echo $getSocietyInfo['cess_tax_rate'] ?>%</td>
                    <?php 
					}
					else{?>
						<td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">CESS @ <?php echo $getSocietyInfo['cess_tax_rate'] ?>% on (B<?php echo ($bApplyGSTOnInterest ? " +  C" : "") ?>)</td>
						<?php 
					}?>
	                	
	                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;font-size:<?php echo $BillFont?>px;"><?php echo number_format($CESS,2); ?></td>
	                </tr>
                    <?php }


					?>
					<tr>
	                	<td style="width:30%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">Round-Off value</td>
	                    <td id="roundOffAmt" style="text-align:right;width:20%;border:0.5px solid black;border-right:none;font-size:<?php echo $BillFont?>px;"><?php echo number_format($LedgerRoundOff,2); ?></td>
	                </tr>
	                <?php
		        //}
				
		        ?> 
				<?php if($outstanding_result[0]['apply_Outstanding_amount'] == 0)
				{
				?>
				<tr>
                	<td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">Total Outstanding Amount</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;font-size: <?php echo $BillFont?>px;font-weight: bold;"><?php echo number_format(abs($BalanceAmout), 2); if($BalanceAmout < 0){echo ' Cr';} else {echo ' Dr';} ?></td>
                </tr>
				<?php } 
				else {
				?>
				<tr>
				
                	<td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">
					<?php 
									if($IsSupplementaryBill == 0 )
									{
										echo 'Total Outstanding Maintenance (A)';
									}
									else if($IsSupplementaryBill == 1 )
									{
										echo 'Total Outstanding Supplementry (A)';
									}
									else if($IsSupplementaryBill == 3)
									{
										echo 'Total Outstanding Invoice (A)';
									}
								
								?>	
				</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;font-size: <?php echo $BillFont?>px;font-weight: bold;"><?php echo number_format(abs($BalanceAmout), 2); if($BalanceAmout < 0){echo ' Cr';} else {echo ' Dr';} ?></td>
                </tr>
              
                <tr>
                	<td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">
					<?php 
									if($IsSupplementaryBill == 0 )
									{
										echo 'Outstanding Supplementary Balance (B)';
										$OutstandingBillAmount=$Supple_Bill_Amout=$obj_genbill->OutstandingSupplementaryBillAmount($_REQUEST['PeriodID'],$_REQUEST['UnitID'],$BillDate,Supplementry);
									}
									else if($IsSupplementaryBill == 1 )
									{
										echo 'Outstanding Maintenance Balance (B)';
										$OutstandingBillAmount = $Supple_Bill_Amout=$obj_genbill->OutstandingSupplementaryBillAmount($_REQUEST['PeriodID'],$_REQUEST['UnitID'],$BillDate,Maintenance);
									}
								
								
								?>		
				</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;font-size: <?php echo $BillFont?>px;font-weight: bold;"><?php echo number_format(abs($OutstandingBillAmount), 2); if($Supple_Bill_Amout < 0){echo ' Cr';} else {echo ' Dr';} ?></td>
                </tr>
				<tr>
                	<td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">Outstanding Invoice Balance (C)</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;font-size: <?php echo $BillFont?>px;font-weight: bold;"><?php echo number_format(abs($Incoice_bill_Amout=$obj_genbill->OutstandingInvoiceBillAmount($BillDate,$_REQUEST['UnitID'])), 2); if($Incoice_bill_Amout < 0){echo ' Cr';} else {echo ' Dr';} ?></td>
                </tr>
				
				<tr>
				<?php 
				
				$total_outstanding_amt = $Supple_Bill_Amout+$BalanceAmout+$Incoice_bill_Amout ; ?>
                	<td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2">Total Outstanding Balance (A+B+C) </td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;font-size: <?php echo $BillFont?>px;font-weight: bold;"><?php echo number_format(abs($total_outstanding_amt), 2); if($total_outstanding_amt < 0){echo ' Cr';} else {echo ' Dr';} ?></td>
                </tr>
<?php } ?>
				<?php if($ShowSuppBillBalanceInMaintBill){ ?>
					<tr>
						<td style="width:20%;border:1px solid black;padding-left:3px;font-size:<?php echo $BillFont?>px;" colspan="2"><?=$SuppBillBalanceDisplayText?></td>
						<td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;font-size: <?php echo $BillFont?>px;font-weight: bold;"><?php echo number_format(abs($supplementDue), 2); if($supplementDue < 0){echo ' Cr';} else {echo ' Dr';} ?></td>
					</tr>
				<?php }?>
				
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
        <div id="bill_notes" style="text-align:left;font-size:<?php echo $BillFont?>px;margin-left:5px;">
        <table style="width:100%">
        	<tr>
            <?php 

		if($objFetchData->objSocietyDetails->sQRCode  <> "" && $objFetchData->objSocietyDetails->sShowQRCodeInBill==1)
		 	 {?>
        		<td style="width:83%">
        			Notes:<br>
       				<?php echo $BillNotes; ?>     
                </td>
                
        	<td >
         		<img src="<?php echo $objFetchData->objSocietyDetails->sQRCode ?>" style="width:100px;margin:10px;float:right"><br>
               <div style="float:right;text-align:center;font-size: 10px;"><b>Scan above QR code <br><span>to make the online payment</span></b></div>
        	</td>
             
        <?php }
		else
		{?>
			<td>
        			Notes:<br>
       				<?php echo $BillNotes; ?>     
                </td>
			<?php } ?>
        </tr>
        </table>
        	
        </div>
       
        <div id="bill_message">
        </div>
        <?php
        	if($_SESSION['society_id'] <> 195)
        	{
        		?>
        		<div id="bill_sign" style="text-align:right;border-top:1px solid black;padding-right:10px;font-size:<?php echo $BillFont?>px;">
        	<?php echo $objFetchData->objSocietyDetails->sSocietyName; ?><br> <?php if($bill_footer <> "") { echo $bill_footer; } else { ?><br><br> Authorised Signatory <?php } ?>
        		</div>
        		<?php
        	}
        ?>
        
        <?php
		//echo "Show Reciept".$show_receipt;
		//echo "size of ::".sizeof($receiptDetails);
		
		if($show_receipt == 1 && sizeof($receiptDetails) > 0)
		{
			
		?>
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
                		<th style="text-align:center; border:1px solid black;border-left:none;border-right:1px solid black;width:10%;">Bill Type</th>
                		<th style="text-align:center; border:1px solid black;border-left:none;width:10%;">Recpt Date</th>
                		<th style="text-align:center; border:1px solid black;border-left:none;width:10%;">Receipt No.</th>
                        <th style="text-align:center; border:1px solid black;border-left:none;width:10%;">Chk Date</th>
                        <th style="text-align:center; border:1px solid black;border-left:none;width:15%;">Cheque/NEFT No.</th>
                        <th style="text-align:center; border:1px solid black;border-left:none;width:15%;">Payer Bank</th>
                        <th style="text-align:center; border:1px solid black;border-left:none;width:15%;">Payer Branch</th>
                        <th style="text-align:center; border:1px solid black;border-left:none;border-right:none;width:25%;">Amount</th>  
                    </tr> 
                	<?php 
						//echo "Receipt Details";
						//echo sizeof($receiptDetails) ;
						$total = '';
						$CreditDebitNote = '';
						for($i=0; $i < sizeof($receiptDetails) ; $i++)
						{						
							$voucherDate = $receiptDetails[$i]['VoucherDate'];
							$receiptNo   = $receiptDetails[$i]['ExternalCounter'];
							$amount = (float)$receiptDetails[$i]['Amount'];
							$payerBank = $receiptDetails[$i]['PayerBank'];
							$payerBranch = $receiptDetails[$i]['PayerChequeBranch'];
							$chequeDate = $receiptDetails[$i]['Date'];
							$chequeNo = $receiptDetails[$i]['ChequeNumber'];
							$billtype=$receiptDetails[$i]['BillType'];
						
						if($receiptDetails[$i]['IsReturn'] == 0)
						{
							$total += $amount;
						}
						
						if($billtype == DEBIT_NOTE || $billtype == CREDIT_NOTE)
						{
							$receiptNote = strip_tags($receiptDetails[$i]['Note']);
							if($receiptNote <> '')
							{
							$CreditDebitNote .= 'Rs. '.$amount.' : ' . $receiptNote;							
							}
						}
						else if($billtype == Invoice || $billtype == Supplementry)
						{
							$receiptNote = strip_tags($receiptDetails[$i]['Comments']);
							if($receiptNote <> '')
							{
							$CreditDebitNote .= 'Rs. '.$amount.' : ' . $receiptNote;
							}
						}
						
				?>
                <tr>
                	<!--<td style="text-align:center;border:1px solid black;border-left:none;"><?php //echo $i+1 ?> </td>-->         
                  
		   <td style="text-align:center;border:1px solid black;border-left:none;border-right:1px solid black;"><?php echo $m_objUtility->returnBillTypeString($billtype);?></td>
					 
                    
                    <td style="text-align:center;border:1px solid black;border-left:none;"><?php echo getDisplayFormatDate($voucherDate) ?> </td>           
                    <td style="text-align:center;border:1px solid black;border-left:none;"><?php echo $receiptNo; ?> </td>
                    <td style="text-align:center;border:1px solid black;border-left:none;">
					<?php 
					if($_SESSION['society_id'] == 288) //Shree Marigold society
					{
						 echo '-';
					}
					else
					{
						echo getDisplayFormatDate($chequeDate);
					}
					?> </td>
                    <td style="text-align:center;border:1px solid black;border-left:none;"><?php echo $chequeNo ?> </td>
                    <td style="text-align:center;border:1px solid black;border-left:none;"><?php echo $payerBank ?> </td>
                    <td style="text-align:center;border:1px solid black;border-left:none;"><?php echo $payerBranch ?> </td>
                    <td style="text-align:center;border:1px solid black;border-left:none;border-right:none;"><?php if($receiptDetails[$i]['IsReturn'] == 1){echo "Returned";}else{echo number_format($amount, 2);} ?> </td>
                     
                </tr>                                                            
                <?php } 
				if($total <> '')
				{
				?>
               	<tr>
                	<td colspan="5" style="text-align:left;"><?php 
					if(!empty($CreditDebitNote))
					{
						echo $CreditDebitNote;
					}
					?></td>
                    <td colspan="2" style="text-align:right;"><?php echo "Total    :  " ?></td>
                    <td style="text-align:center;border:1px solid black;border-left:none;border-right:none;"><?php echo number_format($total, 2); ?> </td>

                </tr>
               
                <tr>
                	<td style="border:1px solid black;border-right:none;border-left:none;" colspan="8">
					In Words : <?php  echo "Rupees ". convert_number_to_words($total); if($total <> ''){ echo " Only"; }?>
					 </td>
                </tr>
          <?php } ?>      
           </table>
           </div>
          
        </div>
        <?php
        }
		if($show_receipt == 1 && sizeof($receiptDetails) == 0 && $_SESSION['client_id'] != 9)
		{?>
			<div id="bill_receipt" style="text-align:center;border-top:1px solid black;border-bottom:none;">        	
            <div id="society_name" style="font-weight:bold; font-size:14px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>           
            <div id="society_reg" style="font-size:10px;">
				<?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?>
            </div> 
            <div id="society_address"; style="font-size:10px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>            <div id="bill_subheader" style="text-align:center;font-weight:bold; font-size:14px;">
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
           <?php  if($_SESSION['society_id'] == 448)
			   {?>
           		<div id="bill_payment" style="width:100%;">
        		<table style="width:100%;font-size:<?php echo $BillFont?>px;">
                	<tr>
                		<th style="text-align:center; border:1px solid black;border-left:none;border-right:1px solid black;width:10%;">Bill Type</th>
                		<th style="text-align:center; border:1px solid black;border-left:none;width:10%;">Recpt Date</th>
                		<th style="text-align:center; border:1px solid black;border-left:none;width:10%;">Receipt No.</th>
                        <th style="text-align:center; border:1px solid black;border-left:none;width:10%;">Chk Date</th>
                        <th style="text-align:center; border:1px solid black;border-left:none;width:15%;">Cheque/NEFT No.</th>
                        <th style="text-align:center; border:1px solid black;border-left:none;width:15%;">Payer Bank</th>
                        <th style="text-align:center; border:1px solid black;border-left:none;width:15%;">Payer Branch</th>
                        <th style="text-align:center; border:1px solid black;border-left:none;border-right:none;width:25%;">Amount</th>  
                    </tr> 
                    <tr>
                			<!--<td style="text-align:center;border:1px solid black;border-left:none;"><?php //echo $i+1 ?> </td>-->         
                  			<td style="text-align:center;border:1px solid black;border-left:none;border-right:1px solid black;">&nbsp;</td>
					 		<td style="text-align:center;border:1px solid black;border-left:none;">&nbsp;</td>           
                    		<td style="text-align:center;border:1px solid black;border-left:none;">&nbsp;</td>
                    		<td style="text-align:center;border:1px solid black;border-left:none;">&nbsp;</td>
                            <td style="text-align:center;border:1px solid black;border-left:none;">&nbsp;</td>           
                    		<td style="text-align:center;border:1px solid black;border-left:none;">&nbsp;</td>
                    		<td style="text-align:center;border:1px solid black;border-left:none;">&nbsp;</td>
                            <td style="text-align:center;border:1px solid black;border-left:none;">&nbsp;</td>           
                    </tr>
                    <tr>
                    
                    <td colspan="7" style="text-align:right;border:1px solid black;border-left:none;"><?php echo "Total    :  " ?></td>
                    <td style="text-align:center;border:1px solid black;border-left:none;border-right:none;"> &nbsp;</td>

					</tr>
                    </table>
                    </div>
                    <?php }
					else
					{?>
						<div style="font-size: 14px;"><b>There is no receipt for this billing period</b></div>
						<?php }?>
			
		   <?php } ?>
		<?php 
	
		 ?>
        
       <?php 
			// Supplymentry Reciept
			foreach($receiptDetails as $key => $val){
				if($val['BillType'] == 1)
				{
					$newreceiptDetails[]= $val;
				}
			}
			unset($receiptDetails);
			$receiptDetails = $newreceiptDetails;
			
			if($show_supply_receipt == 1 && sizeof($receiptDetails) > 0 && $_SESSION['client_id'] != 9  && $bBillType = $IsSupplementaryBill)
			{
				
				?>
			<div id="bill_receipt" style="text-align:center;border-top:1px solid black;border-bottom:none;">        	
            <div id="society_name" style="font-weight:bold; font-size:14px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>           
            <div id="society_reg" style="font-size:10px;">
				<?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?>
            </div> 
            <div id="society_address"; style="font-size:10px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>            <div id="bill_subheader" style="text-align:center;font-weight:bold; font-size:14px;">
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
                		<th style="text-align:center; border:1px solid black;border-left:none;border-right:1px solid black;width:10%;">Bill Type</th>
                		<th style="text-align:center; border:1px solid black;border-left:none;width:10%;">Recpt Date</th>
                		<th style="text-align:center; border:1px solid black;border-left:none;width:10%;">Receipt No.</th>
                        <th style="text-align:center; border:1px solid black;border-left:none;width:10%;">Chk Date</th>
                        <th style="text-align:center; border:1px solid black;border-left:none;width:15%;">Cheque/NEFT No.</th>
                        <th style="text-align:center; border:1px solid black;border-left:none;width:15%;">Payer Bank</th>
                        <th style="text-align:center; border:1px solid black;border-left:none;width:15%;">Payer Branch</th>
                        <th style="text-align:center; border:1px solid black;border-left:none;border-right:none;width:25%;">Amount</th>  
                    </tr> 
                	<?php 
						//echo "Receipt Details";
						//echo sizeof($receiptDetails) ;
						$total = '';
						$CreditDebitNote = '';
						for($i=0; $i < sizeof($receiptDetails) ; $i++)
						{						
							$voucherDate = $receiptDetails[$i]['VoucherDate'];
							$receiptNo   = $receiptDetails[$i]['ExternalCounter'];
							$amount = (float)$receiptDetails[$i]['Amount'];
							$payerBank = $receiptDetails[$i]['PayerBank'];
							$payerBranch = $receiptDetails[$i]['PayerChequeBranch'];
							$chequeDate = $receiptDetails[$i]['Date'];
							$chequeNo = $receiptDetails[$i]['ChequeNumber'];
							$billtype=$receiptDetails[$i]['BillType'];
						
							if($receiptDetails[$i]['IsReturn'] == 0)
							{
								$total += $amount;
							}
							/*if($billtype == DEBIT_NOTE || $billtype == CREDIT_NOTE)
							{
								$receiptNote = strip_tags($receiptDetails[$i]['Note']);
								if($receiptNote <> '')
								{
									$CreditDebitNote .= 'Rs. '.$amount.' : ' . $receiptNote;							
								}
							}
							else if($billtype == Invoice || $billtype == Supplementry)
							{
								$receiptNote = strip_tags($receiptDetails[$i]['Comments']);
								if($receiptNote <> '')
								{
									$CreditDebitNote .= 'Rs. '.$amount.' : ' . $receiptNote;
								}
							}*/
							?>
                		<tr>
                			<!--<td style="text-align:center;border:1px solid black;border-left:none;"><?php //echo $i+1 ?> </td>-->         
                  			<td style="text-align:center;border:1px solid black;border-left:none;border-right:1px solid black;"><?php echo $m_objUtility->returnBillTypeString($billtype);?></td>
					 
                    		<td style="text-align:center;border:1px solid black;border-left:none;"><?php echo getDisplayFormatDate($voucherDate) ?> </td>           
                    		<td style="text-align:center;border:1px solid black;border-left:none;"><?php echo $receiptNo; ?> </td>
                    		<td style="text-align:center;border:1px solid black;border-left:none;">
							<?php 
							
								echo getDisplayFormatDate($chequeDate);
							
							?> </td>
                            <td style="text-align:center;border:1px solid black;border-left:none;"><?php echo $chequeNo ?> </td>
                            <td style="text-align:center;border:1px solid black;border-left:none;"><?php echo $payerBank ?> </td>
                            <td style="text-align:center;border:1px solid black;border-left:none;"><?php echo $payerBranch ?> </td>
 		                   <td style="text-align:center;border:1px solid black;border-left:none;border-right:none;"><?php if($receiptDetails[$i]['IsReturn'] == 1){echo "Returned";}else{echo number_format($amount, 2);} ?> </td>
                     
        	        </tr>                                                            
             <?php } 
				if($total <> '')
				{
				?>
               	<tr>
                	<td colspan="5" style="text-align:left;"><?php 
					if(!empty($CreditDebitNote))
					{
						echo $CreditDebitNote;
					}
					?></td>
                    <td colspan="2" style="text-align:right;"><?php echo "Total    :  " ?></td>
                    <td style="text-align:center;border:1px solid black;border-left:none;border-right:none;"><?php echo number_format($total, 2); ?> </td>

                </tr>
               
                <tr>
                	<td style="border:1px solid black;border-right:none;border-left:none;" colspan="8">
					In Words : <?php  echo "Rupees ". convert_number_to_words($total); if($total <> ''){ echo " Only"; }?>
					 </td>
                </tr>
          <?php }
		   ?>
		   
           </table>
           </div>
           
          
          <?php }
		  ?>
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
    </Form>
      <?php if(!isset($_REQUEST['edt']))
		{?>
   	 <input type="button" id="viewbtn" value="View As PDF"  onclick="ViewPDF();"/> 
     <input type="button" id="dwnbtn" value="Download PDF"  onclick="setflag();ViewPDF();"/> 
     <input type="hidden" name="dwnflag" id="dwnflag" value="<?php echo  $_REQUEST['dwnflag'];?>"/>
    <?php }?>
  
    <?php if($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['profile'][PROFILE_SEND_NOTIFICATION] == 1)
	{?>
    	<?php if(!isset($_REQUEST['edt']))
		{?>
            <input type="button" id="send_email" value="Send Email" onclick="sendEmail(<?php echo $_REQUEST['UnitID']; ?> , <?php echo $_REQUEST['PeriodID']; ?>,true,'<?php echo $objFetchData->objMemeberDetails->sEmail; ?>');"  title="Email will be send to <?php echo $objFetchData->objMemeberDetails->sEmail; ?>" />
<?php }?>
    <input type="button" id="viewlog" value=""  onclick="ViewLog();" style="background-color:#FFF;border:#FFFFFF"/>
    <?php if(!isset($_REQUEST['edt']))
		{?>
            <div id="status" style="color:#0033FF; font-weight:bold; visibility:hidden;"></div>
<?php }?>
    
    <?php
	$BillFor = $objFetchData->GetBillFor($_REQUEST['PeriodID']);
	$CurUnitNumber = $objFetchData->objMemeberDetails->sUnitNumber;
	//var_dump($BillFor); 
	$bill_dir = 'm_bills_log/' . $objFetchData->objSocietyDetails->sSocietyCode;
	if (!file_exists($bill_dir)) 
	{
		mkdir($bill_dir, 0777, true);
	}
	
	if(strpos($CurUnitNumber, '/') == true)
	{
		$CurUnitNumber = str_replace('/','-',$CurUnitNumber);
	}
	
	 $errorfile_name = $bill_dir.'/M_Bill_'.$_SESSION['society_id'].'_'. $CurUnitNumber .'_'.$BillFor.'_'.$BillNumber .'.html';
	$errorfile_name = str_replace(' ', '-',$errorfile_name);
	//echo $errorfile_name;
	//if(!file_exists($errorfile_name))
	//{
		//$errofile_name ="";
	//}
	?>
    <input type="hidden" style="float:right" id="logurl" value="<?php  echo $errorfile_name; ?>" >
    <?php 
	}
	?>
</body>
</html>
<?php
	if(isset($_REQUEST['gen']))
	{
		?>
        	<script>ViewPDF();</script>
        <?php
	}
?>
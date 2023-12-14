<?php 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include "classes/include/fetch_data.php";
include "common/CommonMethods.php";
include_once("classes/dbconst.class.php");
include_once("classes/ChequeDetails.class.php");
include_once("classes/utility.class.php");

$objFetchData = new FetchData($dbConn);
$objChequeData = new ChequeDetails($dbConn);
$objUtilityData = new utility($dbConn);

$UnitId=$_SESSION["unit_id"];
if(isset($_REQUEST['unit']))
{
	//echo"test";
	$UnitId=$_REQUEST['unit'];
}
	
$objFetchData->GetSocietyDetails($objFetchData->GetSocietyID($UnitId));

$m_objdbRoot = new  dbop(true);
$ClientDetails = $m_objdbRoot->select("select * from `client` where `id` = '".$_SESSION['society_client_id']."'");
//print_r($ClientDetails);
if(isset($ClientDetails))
{
	$Header = $ClientDetails[0]["bill_footer"];
}
else
{
	$Header = '';
}

/*****  Old Format *********
 
if(isset($_REQUEST["extra"]))
{
	$receiptDetails = $objFetchData->getReceiptDetailsEx($UnitId, $_REQUEST["PeriodID"],true,$_REQUEST["cycle"]);
}
else
{
	$receiptDetails = $objFetchData->getReceiptDetailsEx($UnitId, $_REQUEST["PeriodID"]);		
}
if($_REQUEST["cycle"] <> "")
{
	$PeriodID=$objFetchData->getNextPeriodID($_REQUEST["PeriodID"]);
}
else
{
	$PeriodID=$_REQUEST["PeriodID"];
}
$begin_endDate = $objFetchData->getBeginEndReceiptDate($UnitId, $PeriodID);
if($begin_endDate <> "")
{	
	$StartDate = getDisplayFormatDate($begin_endDate['BeginDate']);
	$EndDate = getDisplayFormatDate($begin_endDate['EndDate']);									
}

**/

/**  New Format **/

$receiptDetails = $objChequeData->selecting();
$StartDate = $receiptDetails[0]['VoucherDate'];

/* End */


$objFetchData->GetMemberDetails($UnitId ,getDBFormatDate($StartDate));
$showInBillDetails = $objFetchData->GetFieldsToShowInBill($UnitId);
$bill_footer = $showInBillDetails[0]['bill_footer'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Receipt Details</title>
<script>
function PrintPage() 
	{
		var btnPrint = document.getElementById("Print");
		btnPrint.style.visibility = 'hidden';
		window.print();
        btnPrint.style.visibility = 'visible';
		
    }
</script>
</head>

<body>
<div align="center" style="alignment-adjust:middle; left:80px;">
		<INPUT TYPE="button" id="Print" onClick="PrintPage()" name="Print!" value="Print!" width="300" style="width:60px;height:30px; font-size:20px">
</div>
<div id="bill_receipt" style="text-align:center;border:1px solid black;">        	
            <div id="society_name" style="font-weight:bold; font-size:14px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>           
            <div id="society_reg" style="font-size:10px;">
				<?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?>
            </div>  
             <div id="society_address"; style="font-size:10px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>                                                           
        	<div id="bill_subheader" style="text-align:center;font-weight:bold; font-size:15px;">
            	RECEIPT
            </div>
            <div id="bill_details" style="text-align:right;">
            <table style="width:100%;border-top:1px solid black;">  
            	<tr>
                	<td style="float:left;" colspan="2">Received with thanks from  <?php echo $objFetchData->objMemeberDetails->sMemberName; ?></td>                    
              	</tr>
			</table>
			<table style="width:100%;border-bottom:1px solid black;text-align:left;">  
                <tr>
					<?php
						$receiptPeriod = $StartDate . ' To ' . $EndDate;
						if($EndDate == '')
						{
							$receiptPeriod = 'From ' . $StartDate;
						}
					?>
					
                	<td id='receipt_period'>Receipt Period : <?php  echo $receiptPeriod; ?></td>
                	<td id='owner_unit'>Unit No:<?php echo $objFetchData->objMemeberDetails->sUnitNumber; ?></td>
                </tr>                                             
            </table>
        </div>
        <div id="bill_payment" style="width:100%;">
        	<table style="width:100%;">
                <tr>
                <th style="text-align:center; border:1px solid black;width:18%;">Receipt/Voucher Date</th>
                <th style="text-align:center; border:1px solid black;border-left:none;width:15%;">Cheque/NEFT Date</th>
                <th style="text-align:center; border:1px solid black;width:25%;">Deposited In Bank</th>
                <th style="text-align:center; border:1px solid black;border-left:none;width:15%;">Cheque/NEFT No.</th>
                <th style="text-align:center; border:1px solid black;border-left:none;width:15%;">Payer Bank</th>
                <th style="text-align:center; border:1px solid black;border-left:none;width:15%;">Payer Branch</th>
                <th style="text-align:center; border:1px solid black;border-left:none;width:20%;">Amount</th>
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
						$PayerChequeBranch = $receiptDetails[$i]['PayerChequeBranch'];
						if($PayerChequeBranch == "")
						{
							$PayerChequeBranch =" - ";
						}
						$chequeDate = $receiptDetails[$i]['ChequeDate'];
						$chequeNo = $receiptDetails[$i]['ChequeNumber'];
						$BankName = $objUtilityData->getLedgerName($receiptDetails[$i]['BankID']); // changes as per new format
						//$BankName = $receiptDetails[$i]['BankID']; // old code
						if($receiptDetails[$i]['IsReturn'] == 0)
						{
							$total += $amount;
						}
				?>
                <tr>
                	<!--<td style="text-align:center;border:1px solid black;border-top:none;"><?php //echo $i+1 ?></td> -->   
                    <td style="text-align:center;border:1px solid black;border-top:none;"><?php echo getDisplayFormatDate($voucherDate) ?></td>
                	<td style="text-align:center;border:1px solid black;border-left:none; border-top:none;"><?php echo getDisplayFormatDate($chequeDate) ?> </td>
                    <td style="text-align:center;border:1px solid black;border-left:none; border-top:none;"><?php echo $BankName ?> </td>
                    <td style="text-align:center;border:1px solid black;border-left:none; border-top:none;"><?php echo $chequeNo ?> </td>
                    <td style="text-align:center;border:1px solid black;border-left:none; border-top:none;"><?php echo $payerBank ?> </td>
                    <td style="text-align:center;border:1px solid black;border-left:none; border-top:none;"><?php echo $PayerChequeBranch ?> </td>
                    <td style="text-align:center;border:1px solid black;border-left:none; border-top:none;"><?php echo number_format($amount, 2); ?> </td>
                </tr>                              
                <?php } ?>
               <tr>
                	<td colspan="6" style="text-align:right;border:1px solid black;">Total : &nbsp;</td>
                    <td style="text-align:center;border:1px solid black;"><?php echo number_format($total, 2); ?> </td>

                </tr>
                <tr>
                	<td style="width:100%;border:1px solid black;text-align:left;" colspan="7"><?php
					if($total <> '')
					{
					?>
					In Words : <?php  echo "<b>Rupees ". convert_number_to_words($total); if($total <> ''){ echo " Only </b>"; }?>
					<?php
					}
					?>
                    </td>
                </tr>
           </table>
        </div>
        </div>
        <div id="bill_footer" style="text-align:left;border:1px solid black;padding-right:10px;border-top:none;">
        	<table width="100%" style="font-size:14px;">
            	<tr>
                    <td style="text-align:left;width:50%;">( Subject to Realisation of Cheque ) </td>
                    <td style="text-align:right;width:50%;"> <?php echo $objFetchData->objSocietyDetails->sSocietyName; ?> </td>
                </tr>
                <tr><td> <br><br> </td></tr>
                <tr>
                <td style="text-align:left;width:50%;"><?php  echo $Header?> </td>
           			<!--<td style="text-align:left;width:50%;">Accounts Maintained By "Pavitra Associates Pvt. Ltd."</td>-->
            		<td style="text-align:right;width:50%;"> <?php 
			 														if($bill_footer <> "") { echo $bill_footer; } else {?> Authorised Signatory <?php } 
															 ?> </td>
          		</tr>
            </table>
        </div>                 
</body>
</html>
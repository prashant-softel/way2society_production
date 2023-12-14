<?php 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include "classes/bankRecoReport.class.php";
include "classes/dbconst.class.php";
$objBankReco = new BankReco($dbConn);

if($_REQUEST["ledgerID"] == "")
{
	echo "<script>alert('Error ! Please pass LedgerID to generate statement');</script>";
	exit;
}

$objBankReco->GetSocietyDetails($_SESSION['society_id']);
$bankName = $objBankReco->getBankName($_REQUEST["ledgerID"]);
$chequeIssueDetails = $objBankReco->getChequeIssueDetails($_REQUEST['ledgerID']);
$chequeDepositDetails = $objBankReco->getChequeDepositDetails($_REQUEST['ledgerID']);
$reconciledBalance = $objBankReco->getReconciledBalance($_REQUEST["ledgerID"]);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Bank Reco Report</title>
<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="js/ajax_new.js"></script>
<style>
	table {
    	border-collapse: collapse;
	}
	table, th, td {
   		border: 0px solid black;
		text-align:left;
	}	
</style>		
	
</head>
<body>
    <center>    
    <div id="bill_main" style="width:90%;">
    <div>
        <div id="report_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:18px;"><?php echo $objBankReco->objSocietyDetails->sSocietyName; ?></div>    
            <div id="society_reg" style="font-size:14px;"><?php if($objBankReco->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objBankReco->objSocietyDetails->sSocietyRegNo; 
				}
				?></div>
            <div id="society_address"; style="font-size:14px;"><?php echo $objBankReco->objSocietyDetails->sSocietyAddress; ?></div>
        </div>
        <div id="financial_year" style="text-align:center;">
            <div style="font-weight:bold; font-size:16px;">Financial Year</div>
            <div style="text-align:right;"><?php echo date('d.m.Y'); ?> </div>
        </div>
        <div style="text-align:center;font-weight:bold; font-size:14px;border:1px solid black;border-left:none;border-right:none;border-bottom:none;">Reconciliation Statement for The <?php echo $bankName ;?>        
        </div>
        <div id="reco_report">
        	<table  style="width:100%;font-size:14px;">            
                <tr>
                    <th style="text-align:center; width:15%; border:1px solid black;border-left:none;">Voucher No.</th>
                    <th style="text-align:center; width:15%; border:1px solid black;">Voucher Date</th>
                    <th style="text-align:center; width:15%; border:1px solid black;">Cheque No.</th>
                    <th style="text-align:left; width:40%; border:1px solid black;">Particulars</th>
                    <th style="text-align:right; width:15%; border:1px solid black;border-right:none;">Amount</th>
                </tr>
                <tr>
                	<th colspan="4" style="text-align:center;">Balance as per Bank Book Rs.:</th>                    
                    <th style="text-align:right;"><label id="balance"> </label> </th>                    
                </tr>
                <tr>
          			<td colspan="5"><hr/></td>
    			</tr>
                <tr>
                	<td colspan="5" style="font-weight:bold;"> Add : Cheque issued but not presented to Bank </td>
                </tr>
                <?php 
					$paidTotal = 0;
					for($i = 0; $i < sizeof($chequeIssueDetails); $i++)
					{						
						$paidTotal += $chequeIssueDetails[$i]['Amount'];
						
				?>
                <tr style="">
                	<td style="text-align:center; width:15%; border:1px solid black;border-left:none;border-top:none;border-right:none;"> <?php echo $chequeIssueDetails[$i]['VoucherNo']; ?> </td>
                    <td style="text-align:center; width:15%; border:1px solid black;border-top:none;border-right:none;border-left:none;"> <?php echo getDisplayFormatDate($chequeIssueDetails[$i]['VoucherDate']); ?> </td>
                    <td style="text-align:center; width:15%; border:1px solid black;border-top:none;border-right:none;border-left:none;"> <?php echo $chequeIssueDetails[$i]['ChequeNo']; ?> </td>
                    <td style="text-align:left; width:40%; border:1px solid black;border-top:none;border-right:none;border-left:none;;"> <?php echo $chequeIssueDetails[$i]['Particulars'];  ?> </td>
                    <td style="text-align:right; width:15%; border:1px solid black;border-right:none;border-top:none;border-left:none;"><?php echo $chequeIssueDetails[$i]['Amount']; ?> </td>
                </tr>    
                
                <?php } ?>
                <tr>
                	<td colspan="3"> </td>
                    <th style="text-align:center;"> Total... </th>
                    <th style="text-align:right; "> <?php echo number_format($paidTotal, 2) ;?> </th>
                </tr>
                <tr>
                	<td colspan="5" style="font-weight:bold;"> Less : Cheque Deposited but not credited by Bank </td>
                </tr>
                 <?php 
					$receivedTotal = 0;
					for($i = 0; $i < sizeof($chequeDepositDetails); $i++)
					{						
						$receivedTotal += $chequeDepositDetails[$i]['Amount'];
						
				?>
                <tr style="">
                	<td style="text-align:center; width:15%; border:1px solid black;border-left:none;border-top:none;border-right:none;"> <?php echo $chequeDepositDetails[$i]['VoucherNo']; ?> </td>
                    <td style="text-align:center; width:15%; border:1px solid black;border-top:none;border-right:none;border-left:none;"> <?php echo getDisplayFormatDate($chequeDepositDetails[$i]['VoucherDate']); ?> </td>
                    <td style="text-align:center; width:15%; border:1px solid black;border-top:none;border-right:none;border-left:none;"> <?php echo $chequeDepositDetails[$i]['ChequeNo']; ?> </td>
                    <td style="text-align:left; width:40%; border:1px solid black;border-top:none;border-right:none;border-left:none;;"><?php echo $chequeDepositDetails[$i]['Particulars'];  ?></td>
                    <td style="text-align:right; width:15%; border:1px solid black;border-right:none;border-top:none;border-left:none;"><?php echo $chequeDepositDetails[$i]['Amount']; ?> </td>
                </tr>    
                
                <?php } ?>
                <tr>
                	<td colspan="3"> </td>
                    <th style="text-align:center;"> Total... </th>
                    <th style="text-align:right; "> <?php echo number_format($receivedTotal, 2) ;?> </th>
                </tr>
                <tr>
          			<td colspan="5"><hr/></td>
    			</tr>
                <tr>
                	<td colspan="3"> </td>
                    <th style="text-align:center;"> Balance as per Bank </th>
                    <th style="text-align:right; "> <?php echo number_format($reconciledBalance) ;?> </th>
                </tr>
                <?php $total = $reconciledBalance + $receivedTotal;
						$total -= $paidTotal; ?>
                        <script>
							document.getElementById("balance").innerHTML = '<?php echo $total ?>';
						</script>
            </table>
           
           
        </div>
        </div>  
        </div>        
    </center>   
    </body>
</html>


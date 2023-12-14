<?php 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include "classes/bankReco.class.php";
include_once("classes/dbconst.class.php");
include_once("classes/utility.class.php");
$objBankReco = new BankReco($dbConn);
$obj_utility = new utility($dbConn);

$sqlFetch="SELECT `society_creation_yearid` FROM `society` where society_id = '".$_SESSION['society_id']."'";
$res = $dbConn->select($sqlFetch);		
$startingYearEndDate = $obj_utility->getPreviousYearEndingDate($res[0]['society_creation_yearid']);
//$_POST["from_date"] = $startingYearEndDate;      // Comment issue in opening balance in twise
//echo "date:".$_POST["from_date"]; 
//print_r($startingYearEndDate);
//$date = $startingYearEndDate ;
//$date1 = str_replace('-', '/', $startingYearEndDate );
//$tomorrow = date('d-m-Y',strtotime($date1 . "+1 days"));
//$_POST["from_date"] = $tomorrow;

ini_set('memory_limit', '1024M');

if($_SESSION['society_id'] == 77 && $_POST['ledgerid'] == 882)
{
	try
	{
		$objBankReco->GetSocietyDetails($_SESSION['society_id']);
		$bankName = $objBankReco->getBankName($_POST['ledgerid']);
		$chequeIssueDetails = $objBankReco->getChequeIssueDetails($_POST['ledgerid'], $_POST["from_date"], $_POST['to_date']);
		$chequeDepositDetails = $objBankReco->getChequeDepositDetails($_POST['ledgerid'], $_POST["from_date"], $_POST['to_date']);
		$reconciledBalance = $objBankReco->getReconciledBalance($_POST['ledgerid'], $_POST['to_date']);
		$balanceBeforeDate = $objBankReco->getBalanceBeforeDate($_POST['ledgerid'], $_POST["from_date"],$_POST['to_date']);
	}
	catch(Exception $e)
	{
		echo 'Message: ' .$e->getMessage();
	}
}
else
{

$objBankReco->GetSocietyDetails($_SESSION['society_id']);
$bankName = $objBankReco->getBankName($_POST['ledgerid']);
$chequeIssueDetails = $objBankReco->getChequeIssueDetails($_POST['ledgerid'], $_POST["from_date"], $_POST['to_date']);
$chequeDepositDetails = $objBankReco->getChequeDepositDetails($_POST['ledgerid'], $_POST["from_date"], $_POST['to_date']);
$reconciledBalance = $objBankReco->getReconciledBalance($_POST['ledgerid'], $_POST['to_date']);
//$balanceBeforeDate = $objBankReco->getBalanceBeforeDate($_POST['ledgerid'], $_POST["from_date"],$_POST['to_date']);
}

$BankBalanceAmount = $objBankReco->getBankStatementBalance($_POST['to_date'], $_POST['ledgerid']);
$AmountDifference = $BankBalanceAmount -  $reconciledBalance;

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
	tr:hover {background-color: #f5f5f5}	
</style>		
	
   
</head>
<body>
<div id="mainDiv">
<?php include_once( "report_template.php" ); // get the contents, and echo it out.?>
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
   		<!--<div id="financial_year" style="text-align:center;">
            <div style="font-weight:bold; font-size:16px;">Financial Year</div>
            <div style="text-align:right;"><?php echo date('d.m.Y'); ?> </div>
        </div>-->
        <div id="bill_subheader" style="text-align:center;">
            <div style="font-weight:bold; font-size:16px;">Bank Reconciliation Report</div>
            <div style="font-weight; font-size:16px;">As On : <?php echo '   '.getDisplayFormatDate($_POST['to_date']).'      ';?></div>                                
        </div>
        <div style="text-align:center;font-weight:bold; font-size:14px;border:1px solid #cccccc;border-left:none;border-right:none;border-bottom:none; border-

top:none;">Reconciliation Statement for The <?php echo $bankName ;?>        
        </div>
        <div id="reco_report">
        	<table  style="width:100%;font-size:14px;">            
                <tr>
                    <th style="text-align:center; width:15%; border:1px solid #cccccc;border-left:none;">Voucher No.</th>
                    <th style="text-align:center; width:15%; border:1px solid #cccccc;">Voucher Date</th>
                    <?php //if($_SESSION['login_id'] == 4 || $_SESSION['login_id'] == 2216){ ?>
                    <th style="text-align:center; width:15%; border:1px solid #cccccc;">Reconcile Date</th>
                    <?php //}?>
                    <th style="text-align:center; width:15%; border:1px solid #cccccc;">Cheque No.</th>
                    <th style="text-align:left; width:40%; border:1px solid #cccccc;">Particulars</th>
                    <th style="text-align:right; width:15%; border:1px solid #cccccc;border-right:none;">Amount</th>
                </tr>
                <tr>
                	<th colspan="4" style="text-align:center;">Balance as per Bank Book Rs. upto <?php echo $_POST['to_date']; ?>:</th>                    
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
                	<td style="text-align:center; width:15%; border:1px solid #cccccc;border-left:none;border-top:none;border-right:none;"> <?php echo 

$chequeIssueDetails[$i]['VoucherNo']; ?> </td>
                    <td style="text-align:center; width:15%; border:1px solid #cccccc;border-top:none;border-right:none;border-left:none;"> <?php echo 

getDisplayFormatDate($chequeIssueDetails[$i]['VoucherDate']); ?> </td>

					 <?php //if($_SESSION['login_id'] == 4 || $_SESSION['login_id'] == 2216 || $_SESSION['login_id'] == 3429){ ?>
                    <td style="text-align:center; width:15%; border:1px solid #cccccc;border-top:none;border-right:none;border-left:none;"> <?php echo 

$chequeIssueDetails[$i]['ReconcileDate']; ?> </td>
                    
					<?php //}?>


                    <td style="text-align:center; width:15%; border:1px solid #cccccc;border-top:none;border-right:none;border-left:none;"> <?php echo 

$chequeIssueDetails[$i]['ChequeNo']; ?> </td>
                    <td style="text-align:left; width:40%; border:1px solid #cccccc;border-top:none;border-right:none;border-left:none;"> <?php echo 

$chequeIssueDetails[$i]['Particulars'];  ?> </td>
                    <td style="text-align:right; width:15%; border:1px solid #cccccc;border-right:none;border-top:none;border-left:none;"><?php echo number_format

($chequeIssueDetails[$i]['Amount'], 2); ?> </td>
                </tr>    
                
                <?php } ?>
                <tr>
                	<td colspan="3"> </td>
                    <th style="text-align:center;"> Total... </th>
                    <th style="text-align:right; "> <?php echo number_format($paidTotal,2) ;?> </th>
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
                	<td style="text-align:center; width:15%; border:1px solid #cccccc;border-left:none;border-top:none;border-right:none;"> <?php echo 

$chequeDepositDetails[$i]['VoucherNo']; ?> </td>
                    <td style="text-align:center; width:15%; border:1px solid #cccccc;border-top:none;border-right:none;border-left:none;"> <?php echo 

getDisplayFormatDate($chequeDepositDetails[$i]['VoucherDate']); ?> </td>

				 <?php //if($_SESSION['login_id'] == 4 || $_SESSION['login_id'] == 2216 || $_SESSION['login_id'] == 3429){ ?>
                   
                   <td style="text-align:center; width:15%; border:1px solid #cccccc;border-top:none;border-right:none;border-left:none;"> <?php echo 
                
 $chequeDepositDetails[$i]['ReconcileDate']; ?> </td>
                                    
                                    <?php //}?>

                    <td style="text-align:center; width:15%; border:1px solid #cccccc;border-top:none;border-right:none;border-left:none;"> <?php echo 

$chequeDepositDetails[$i]['ChequeNo']; ?> </td>
                    <td style="text-align:left; width:40%; border:1px solid #cccccc;border-top:none;border-right:none;border-left:none;"><?php echo 

$chequeDepositDetails[$i]['Particulars'];  ?></td>
                    <td style="text-align:right; width:15%; border:1px solid #cccccc;border-right:none;border-top:none;border-left:none;"><?php echo number_format

($chequeDepositDetails[$i]['Amount'],2); ?> </td>
                </tr>    
                
                <?php } ?>
                <tr>
                	<td colspan="3"> </td>
                    <th style="text-align:center;"> Total... </th>
                    <th style="text-align:right; "> <?php echo number_format($receivedTotal, 2);?> </th>
                </tr>
                <tr>
          			<td colspan="5" ><hr/></td>
    			</tr>
                <tr>
                	<td colspan="2"> </td>
                    <th colspan="2" style="text-align:center;"> Balance as per Bank Statement (Reconciled Balance)  </th>
                    <th style="text-align:right; "> <?php echo number_format($reconciledBalance, 2) ;?> </th>
                    
                </tr>
                <tr>
          			<td colspan="5" ><hr/></td>
    			</tr>    
                <?php 
				
				//if($_SESSION['login_id'] == 4 || $_SESSION['login_id'] == 2216 || $_SESSION['login_id'] == 3429)
				//{
					if($reconciledBalance <> $BankBalanceAmount) {?>
                    <tr>
                        <td colspan="2"> </td>
                        <td colspan="2" style="text-align:center;"> <b>Validation</b></br> Latest Balance as of <?=getDisplayFormatDate($_POST['to_date'])?> of uploaded actual bank statement.</br>
                            Difference in calculated reconciled balance and actual bank statement balance <?=number_format($AmountDifference, 2)?>. </td>
                        <th style="text-align:right; "> <?php echo number_format($BankBalanceAmount, 2) ;?> </th>
                    </tr>
                    <?php }	
				//}
				
                	    $total = $reconciledBalance + $receivedTotal;
						$total -= $paidTotal; 
                		//$total = $total + $balanceBeforeDate[0]['TotalReceived'] - $balanceBeforeDate[0]['TotalPaid'];
                 		?>
                        <script>
							document.getElementById("balance").innerHTML = '<?php echo number_format($total, 2); ?>';
						</script>
				<tr><td colspan="5"><br/></td></tr>                        
                <tr> 
                	<td colspan="5" style="text-align:center;"><input TYPE="button" VALUE="Show Bank Validation Report" onClick="window.open

('BankEntriesValidation.php','BankEntriesValidationpopup','type=width=700,height=600,scrollbars=yes');"  class="btn btn-primary"    style="box-shadow: none;"></td>     

  
				</tr>                    
            </table>
           
           
        </div>
        </div>  
        </div>        
    </center>  
    </div> 
    </body>
</html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - All Voucher Validation Report</title>
</head>

<?php include_once ("classes/include/dbop.class.php");
include_once("includes/head_s.php"); 
include_once("classes/dbconst.class.php");
include_once("classes/utility.class.php");
include_once("classes/voucherCounter.class.php");
$dbConn = new dbop();
$objCounter = new voucherCounter($dbConn);
$obj_utility = new utility($dbConn);
$IsSameCntApply = $obj_utility->IsSameCounterApply();

?>
<html>
	<head>
	<title>ALL VOUCHER VALIDATION REPORT</title>
	</head>
	<body>
    	<br><br>
    	<div style="border:1px solid black;padding-left:25px;border-radius:10px;background: ghostwhite;">
        	<center><h3>ALL VOUCHER VALIDATION REPORT</h3></center>	
        <br />
        <?php if($_SESSION['role'] == ROLE_SUPER_ADMIN){ ?>
        <button type="button"  style="border:none;margin:none;margin-left: 40%;" class="btn btn-primary" onClick="window.open('voucherCounter.php')">Manage Voucher Counter </button>
       <?php }?>
        <span><hr/></span>
        <left><br/><br /><span style = "font-size:20px;padding-bottom:10px;">JOURNAL VOUCHER VALIDATION REPORT</span><br/></left>     
<?php $JvErrorReport = $objCounter->CounterValidationReport(VOUCHER_JOURNAL,0);
	
		if(sizeof($JvErrorReport) <> 0)
		{?>
		 <left><br/><span style = "font-size:18px;padding-bottom:10px;">DUPLICATE VOUCHER</span><br /></left>			
	<?php	} 
		
		 $objCounter->displayErrorReport($JvErrorReport,VOUCHER_JOURNAL,'JOURNAL',0);?>	
		
        <left><br/><br /><span style = "font-size:20px;padding-bottom:10px;">INVOICE VOUCHER VALIDATION REPORT</span><br/></left>
       
<?php	$InvoiceErrorReport = $objCounter->CounterValidationReport(VOUCHER_INVOICE,0); 	
		
		if(sizeof($InvoiceErrorReport) <> 0)
		{
		?>
          <left><br/><span style = "font-size:18px;padding-bottom:10px;">DUPLICATE VOUCHER</span><br/></left>
		<?php
		}
		$objCounter->displayErrorReport($InvoiceErrorReport,VOUCHER_INVOICE,'Invoice',0);
	
		?>
		<left><br/><br /><span style = "font-size:20px;padding-bottom:10px;">CREDIT VOUCHER VALIDATION REPORT</span><br/></left>
       
		<?php	$InvoiceErrorReport = $objCounter->CounterValidationReport(VOUCHER_CREDIT_NOTE,0); 	
		
		if(sizeof($InvoiceErrorReport) <> 0)
		{
		?>
          <left><br/><span style = "font-size:18px;padding-bottom:10px;">DUPLICATE VOUCHER</span><br/></left>
		<?php
		}
		$objCounter->displayErrorReport($InvoiceErrorReport,VOUCHER_CREDIT_NOTE,'Credit',0);
		?>
		
		<left><br/><br /><span style = "font-size:20px;padding-bottom:10px;">Debit VOUCHER VALIDATION REPORT</span><br/></left>
       
		<?php	$InvoiceErrorReport = $objCounter->CounterValidationReport(VOUCHER_DEBIT_NOTE,0); 	
		
		if(sizeof($InvoiceErrorReport) <> 0)
		{
		?>
          <left><br/><span style = "font-size:18px;padding-bottom:10px;">DUPLICATE VOUCHER</span><br/></left>
		<?php
		}
		$objCounter->displayErrorReport($InvoiceErrorReport,VOUCHER_DEBIT_NOTE,'Debit',0);
		
	
	
	
	
	$CashLedgerDetails = $obj_utility->GetBankLedger($_SESSION['default_cash_account']);
	
	for($i = 0; $i < sizeof($CashLedgerDetails); $i++)
	{ ?>
		<left><br/><br /><span style = "font-size:20px;padding-bottom:10px;"><?php echo strtoupper($CashLedgerDetails[$i]['ledger_name']);?>  RECEIPT VOUCHER VALIDATION REPORT</span><br/></left>
        
        <?php
			$CashReceiptErrorReport = $objCounter->CounterValidationReport(VOUCHER_RECEIPT,$CashLedgerDetails[$i]['id']);
			
			$objCounter->displayErrorReport($CashReceiptErrorReport, VOUCHER_RECEIPT, $CashLedgerDetails[$i]['ledger_name'],$CashLedgerDetails[$i]['id'],false,true);	
		?>
        
        <left><br/><br /><span style = "font-size:20px;padding-bottom:10px;"><?php echo strtoupper($CashLedgerDetails[$i]['ledger_name']);?>  PAYMENT VOUCHER VALIDATION REPORT</span><br/></left>
        <?php  	
			
			$CashPaymentErrorReport = $objCounter->CounterValidationReport(VOUCHER_PAYMENT,$CashLedgerDetails[$i]['id']);
			
			$objCounter->displayErrorReport($CashPaymentErrorReport, VOUCHER_PAYMENT, $CashLedgerDetails[$i]['ledger_name'],$CashLedgerDetails[$i]['id'],false,true);
	}
	
	$BnkLedgerDetails = $obj_utility->GetBankLedger($_SESSION['default_bank_account']);
	
	for($i = 0 ; $i < sizeof($BnkLedgerDetails); $i++)
	{ ?>
		<left><br/><br /><span style = "font-size:20px;padding-bottom:10px;"><?php echo strtoupper($BnkLedgerDetails[$i]['ledger_name']);?>  RECEIPT VOUCHER VALIDATION REPORT</span><br/></left>
		
	<?php $BankReceiptErrorReport = $objCounter->CounterValidationReport(VOUCHER_RECEIPT,$BnkLedgerDetails[$i]['id']);

		$objCounter->displayErrorReport($BankReceiptErrorReport, VOUCHER_RECEIPT, $BnkLedgerDetails[$i]['ledger_name'],$BnkLedgerDetails[$i]['id'],$IsSameCntApply);?>
		
		<left><br/><br /><span style = "font-size:20px;padding-bottom:10px;"><?php echo strtoupper($BnkLedgerDetails[$i]['ledger_name']);?>  PAYMENT VOUCHER VALIDATION REPORT</span><br></left>
		
 <?php   $BankPaymentErrorReport = $objCounter->CounterValidationReport(VOUCHER_PAYMENT,$BnkLedgerDetails[$i]['id']);
 
		$objCounter->displayErrorReport($BankPaymentErrorReport, VOUCHER_PAYMENT, $BnkLedgerDetails[$i]['ledger_name'],$BnkLedgerDetails[$i]['id'],$IsSameCntApply);                   
		
	}
	
	if($IsSameCntApply == 1)
	{
		//	if($voucherType == VOUCHER_RECEIPT || $voucherType == VOUCHER_PAYMENT)
			{
					//Need to fetch all the bank missing counter
					$ReceiptResult = $objCounter->CounterValidationReport(VOUCHER_RECEIPT,0);
					$PaymentResult = $objCounter->CounterValidationReport(VOUCHER_PAYMENT,0);
					
					if(sizeof($ReceiptResult) > 0)
					{
						$Name = 'RECEIPT';
						//echo '<br>'
						$objCounter->showMissingCounter($ReceiptResult['Missing'],$Name,true);	
					}
					if(sizeof($PaymentResult) > 0)
					{
						$Name = 'PAYMENT';
						$objCounter->showMissingCounter($PaymentResult['Missing'],$Name,true);		
					}
				}
			}

?>
       <br/></div>
    	
	</body>
</html>
<?php include_once "includes/foot.php"; ?>
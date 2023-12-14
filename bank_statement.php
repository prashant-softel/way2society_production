<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Bank Statement</title>
</head>

<?php 
include_once "ses_set_as.php"; 
include_once("classes/dbconst.class.php");
?>

<?php
include_once "classes/bank_statement.class.php";
include_once("classes/utility.class.php");
include_once("classes/home_s.class.php"); 

if($_REQUEST["LedgerID"] == "")
{
	echo "<script>alert('Error ! Please pass LedgerID to generate statement');</script>";
	exit;
}

$obj_view_bank_statement = new bank_statement($m_dbConn);
$obj_Utility = new utility($m_dbConn);
$obj_AdminPanel = new CAdminPanel($m_dbConn);
$sHeader = $obj_Utility->getSocietyDetails();
$SocietyHeader=$sHeader;

$Society=str_replace('\r\n','',$SocietyHeader);

// Show Delete Option

$showDeleteBtn = false; // later we can add condition

if($_SESSION['role'] == ROLE_SUPER_ADMIN){
	
	$showDeleteBtn = true;
}

?>

<?php

 if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else
{
	include_once("includes/head_s.php");
}

if(isset($_GET['ssid'])){if($_GET['ssid']<>$_SESSION['society_id']){?><script>window.location.href = "logout.php";</script><?php }}


?>

<?php

if(isset($_POST['from_date']) && isset($_POST['to_date']))
{
	$from = $_POST['from_date'];
	$to   = $_POST['to_date'];
}
else
{
	$noOfMonth = 1; // current month start with 0	
	$Dates_arr = $obj_Utility->getSingleMonthDates($noOfMonth);
	$from  = getDisplayFormatDate($Dates_arr['from_date']);
	$to  = getDisplayFormatDate($Dates_arr['to_date']);

}


if($_POST['ledgerid'] == "")
{	
$bankName = $obj_view_bank_statement->getBankName($_REQUEST["LedgerID"]);
$details = $obj_view_bank_statement->getDetails($_REQUEST["LedgerID"], $from, $to);
$bankDetails = $obj_view_bank_statement->getBankDetails($_REQUEST["LedgerID"]);
$arParentDetails = $obj_Utility->getParentOfLedger($_REQUEST["LedgerID"]);
$bankID = $_REQUEST["LedgerID"];
$balanceBeforeDate = $obj_view_bank_statement->getBalanceBeforeDate($_REQUEST["LedgerID"], $from);
}
else
{
	$bankName = $obj_view_bank_statement->getBankName($_POST['ledgerid']);
	$details = $obj_view_bank_statement->getDetails($_POST['ledgerid'], $from, $to, $_POST['tran_type']);
	$bankDetails = $obj_view_bank_statement->getBankDetails($_POST['ledgerid']);
	$arParentDetails = $obj_Utility->getParentOfLedger($_POST['ledgerid']);	
	$bankID = $_POST['ledgerid'];
	$balanceBeforeDate = $obj_view_bank_statement->getBalanceBeforeDate($_POST["ledgerid"], $from);
}
$CategoryID = $arParentDetails['category'];	
$arBankDetails = $obj_AdminPanel->GetBankAccountAndBalance(); 
?>

<html>
<head>
	
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/account_subcategory_20190504.js?123"></script>
    <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
	<script language="javascript" type="application/javascript">
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
	
	function ValidateDate()
	{
		var fromDate = document.getElementById('from_date').value;
		var toDate = document.getElementById('to_date').value;		
		var isFromDateValid = jsdateValidator('from_date',fromDate,minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate);
		var isToDateValid = jsdateValidator('to_date',toDate,minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate);
		if(isFromDateValid == false || isToDateValid == false)
		{
			return false;	
		}
		return true;
	}
	
	 $(function()
		{
			$.datepicker.setDefaults($.datepicker.regional['']);
			$(".basics").datepicker({ 
			dateFormat: "dd-mm-yy", 
			showOn: "both", 
			buttonImage: "images/calendar.gif", 
			buttonImageOnly: true,
			minDate: minGlobalCurrentYearStartDate,
			maxDate: maxGlobalCurrentYearEndDate
		})});
	
	window.onfocus = function() {
		var result = localStorage.getItem('refreshPage');	
		//alert(result);
		if(result != null && result > 0 )
		{	
			localStorage.setItem('refreshPage', "0");
			location.reload();
		}
	};
	
	</script>
  
<style>
 @media print {
  /* style sheet for print goes here */
  .PrintClass
  {
		display:block;
   }
}
/*
@media print 
{
	
  a[href]:after { content: none !important; }
  img[src]:after { content: none !important; }
  html, body {
            margin: 0;
            padding: 0;
            background: #FFF; 
            font-size: 9.5pt;
          }
}*/
@media print {
  * { margin: 0 !important; padding: 0 !important;  }
 
  html, body, .page{
   
   alignment-adjust:central;
    background: #FFF; 
    font-size: 9.5pt;
	border-color:#FFFFFF !important;
	color:#fff !important;
	
  }

 
   a[href]:after { content: none !important; }
  img[src]:after { content: none !important; }
 
 
}
 </style>   
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
<div class="panel panel-info" id="panel" style="display:none">
	<?php 	 
	if($CategoryID == CASH_ACCOUNT) { ?>
    	<div class="panel-heading" id="pageheader">Cash & Bank Statement</div>
    <?php } else { ?>
    <div class="panel-heading" id="pageheader">Bank Statement - <?php echo $bankName[0]['ledger_name']; ?></div>
    <?php }  ?>
 </br>
<div align="center"> 
<form name="filter" id="filter" action="bank_statement.php?LedgerID=<?php echo $_REQUEST["LedgerID"]; ?>" method="post" onSubmit="return ValidateDate();">
	<table style="width:95%; border:1px solid black; background-color:transparent; ">
    <tr> <td colspan="3"><br/> </td></tr>
    	<tr>
        	<td> &nbsp; Please Select Bank : </td>            
        	<td>
        		<select name="ledgerid" id="ledgerid">				                    
					<?php                        
                    foreach($arBankDetails as $arData=>$arvalue)
                    {
                       $BankName = $obj_AdminPanel->GetLedgerNameFromID($arvalue["LedgerID"]);
                       $arParentDetails = $obj_Utility->getParentOfLedger($arvalue["LedgerID"]);						                                       
                    ?>    	
					<option value="<?php echo $arvalue["LedgerID"]; ?>"> <?php echo $BankName; ?> </option>
					<?php                                       
                    }?>
               </select>
   			</td>
            <td> From :</td>                      
			<td><input type="text" name="from_date" id="from_date"  class="basics" size="10" style="width:80px;" value = "<?php echo getDisplayFormatDate($from)?>"/></td>
            <td> To :</td>                     
			<td><input type="text" name="to_date" id="to_date"  class="basics" size="10" style="width:80px;" value="<?php echo getDisplayFormatDate($to);?>"/></td>
            <td> Transaction Type :</td>
            <td>
            	<select name="tran_type" id="tran_type" style="width:80px;">
                	<option value="0">All</option>
                    <option value="1">Withdrawals</option>
                    <option value="2">Deposits</option>
                </select>
           	</td>
            <td><input type="submit" name="submit" value="Submit" /> </td>
     	</tr>        
        <tr> <td colspan="3"><br/> </td></tr>
    </table>
</form>
</div>
<script>
<?php
if($_POST['ledgerid'] <> "")
{?>
	document.getElementById('ledgerid').value = "<?php echo $_POST['ledgerid'];?>";
<?php 
}
else 
{ ?>
	document.getElementById('ledgerid').value = "<?php echo $_REQUEST['LedgerID'];?>";
<?php 
} 
if(isset($_POST['from_date']) && isset($_POST['to_date']) )
{?>
	document.getElementById('from_date').value = "<?php echo $_POST['from_date']; ?>";
	document.getElementById('to_date').value = "<?php echo $_POST['to_date']; ?>";
	document.getElementById('tran_type').value = "<?php echo $_POST['tran_type'] ?>";
<?php }
else
{
	$_POST['from_date'] = $_SESSION['default_year_start_date'];	
	$_POST['to_date'] = $_SESSION['default_year_end_date']; 
} ?>
</script>
<br /> <br />   
<div style="width:100%; height:100px;">
<div style="float:left; width:35%; font-size:16px; font-weight:bold;">  
<?php echo $bankName[0]['ledger_name']; ?></br>
<?php echo $bankDetails[0]['BranchName']; ?></br>
<?php echo $bankDetails[0]['Address'] ;?>
</div>
<div style="float:right; width:55%;"> 
<table>
<tr style="height:30px; vertical-align:middle;">	
    <th style="text-align:center; background-color:#D9EDF7; width:12%;">Account Number </th>
    <th style="text-align:center; background-color:#D9EDF7; width:19%;">Statement Period </th>
    <th style="text-align:center; background-color:#D9EDF7; width:12%;">Opening Balance</th>
    <th style="text-align:center; background-color:#D9EDF7; width:12%;">Closing Balance</th>      
</tr>
<tr style="height:30px; vertical-align:middle;">	
    <td style="text-align:center;"><?php echo $bankDetails[0]['AcNumber'] ; ?></td>
    <td style="text-align:center;"><?php if($balanceBeforeDate > 0) { echo getDisplayFormatDate($from); } else { echo getDisplayFormatDate($details[0]['Date']);} ?> To <?php echo getDisplayFormatDate($to); ?></td>
    <td style="text-align:center;"><label id="opening_bal"> </label></td>
    <td style="text-align:center;"><label id="closing_bal"> </label></td>           
</tr>
</table>
</div>
</div>
		<hr />
		<div style="display:none" class="multiDeleteDiv text-center"><button type="button" id='multiDelete' name="multiDelete" class="btn btn-primary" onclick="deleteMultipleCheque();">Delete Selected Entries</button></div>
<center>
<table id="example" class="display" width="100%" >
<thead>
<tr style="height:35px;">
						<?php if ($showDeleteBtn) { ?>
							<th style="width:3%;text-align:center;"><input type="checkbox" class="chk_select_all" id="chk_all" width="25" height="20" />Select All</th>
						<?php } ?>
	<th style="width:5%;text-align:center;">Print</th>
	<th style="width:10%;text-align:center;">Date</th>
    <th style="width:20%;text-align:center;">Particulars</th>
						<th style="width:13%;text-align:center;">Note</th>
    <th style="width:5%;text-align:center;">Voucher Type</th>
    <th style="width:5%;text-align:center;">Voucher No</th>
    <th style="width:9%;text-align:center;">Ref.</th>
    <th style="width:12%;text-align:center;">Deposits</th>
    <th style="width:12%;text-align:center;">Withdrawals</th>
    <th style="width:12%;text-align:center;">Balance</th>
    <th style="width:12%;text-align:center;">Status</th>
</tr>
</thead>
<tbody>
	<?php		
		$balance = $balanceBeforeDate;
		$totalWithdrawals = 0;
		$totalDeposits = 0;
		$ledgerName = "";
		$chequeDetails="";
		$ledger_details="";
		$paidAmount = 0;
		$chequeNumber = "-";
		$voucherType = "-";
		$openingBalancePresent = 0;
		$reference = 0;
		
		//if($balance >= 0)
		//{?>
			<tr style="height:30px;">
							<?php if ($showDeleteBtn) { ?>
								<td style="width:5%;text-align:center;"><?php echo '-' ?></td>
							<?php } ?>
							<td style="width:5%;text-align:center;"><?php echo '-' ?></td>
                <td style="width:10%;text-align:center;"><?php echo getDisplayFormatDate($from);  ?></td>
                <td style="width:20%;text-align:left;"><?php echo 'Opening Balance'; ?></td>
                <td style="width:15%;text-align:left;"><?php echo '-'; ?></td>
                <td style="width:5%;text-align:center;"><?php echo '-' ?></td>
                 <td style="width:5%;text-align:center;"><?php echo '-' ?></td>
                <td style="width:9%;text-align:center;"><?php echo '-' ?></td>
		<td style="width:12%;text-align:right;"><?php echo number_format($balance, 2); ?></td>
		<td style="width:12%;text-align:right;"><?php echo '-' ?></td>							
            	<td style="width:12%;text-align:right;"><?php echo number_format($balance,2); ?></td>
               <td>&nbsp;</td>
                <!--<td style="width:12%;text-align:right;">
               
               </td>-->
       		</tr>
<?php	//}

		for($i = 0; $i < sizeof($details); $i++)
		{
			$chequeNumber = "-";	
			//echo "ID:".$details[$i]['VoucherID'];
			$voucherDetails = $obj_view_bank_statement->getRefTableName($details[$i]['VoucherID']);		
			//var_dump($voucherDetails);	
			$ExvoucherNo = $voucherDetails[0]['ExternalCounter'];	
			$voucherNo = $voucherDetails[0]['VoucherNo'];
			$RefTableName= $voucherDetails[0]['RefTableID'];
			$RefNo 		= $voucherDetails[0]['RefNo'];
			//echo "RefTableName:".$RefTableName;			
			$receivedAmount = $details[$i]['ReceivedAmount'];
			$isOpeningBalance = $details[$i]['Is_Opening_Balance'];
			//$ReconcileStatus=$details[$i]['ReconcileStatus'];
			//$Reconcile=$details[$i]['Reconcile'];
			$ledger_details = "";
			$paymentDtlUrl = "";
			$chqDtlUrl = "";
			if($_POST['ledgerid'] == "")
			{
				$bankID = $_REQUEST["LedgerID"];
			}
			else
			{
				$bankID = $_POST['ledgerid'];
			}
			if($isOpeningBalance == 1)
			{
				$balance += $receivedAmount;
				$totalDeposits += $receivedAmount;
				$ledgerName = "Opening Balance";
				$chequeDate = $details[$i]['Date'];
				$openingBalancePresent = 1;
				//$voucherType = "";
				//$chequeNumber = "";
				//$paidAmount = ""; ?>
                
         	<script>
				document.getElementById("opening_bal").innerHTML = '<?php echo number_format($balance,2); ?>';
			</script>
           <?php     
			}
			else
			{
				$ledgerName = "";
				$chkDetailID = $details[$i]['ChkDetailID'];
				$voucherID = $details[$i]['VoucherID'];
				$voucherTypeID = $details[$i]['VoucherTypeID'];			
				$Type = $obj_view_bank_statement->getVoucherType($voucherTypeID);
				$voucherType = $Type[0]['type'];
				$paidAmount = $details[$i]['PaidAmount'];
				$depositID = $details[$i]['DepositGrp'];
				$isPayment = 0;
				$isReceipt = 0;
				
				if($paidAmount == 0 && $receivedAmount == 0)
				{
					if($voucherTypeID == VOUCHER_PAYMENT)
					{
						$isPayment = 1;	
					}
					else if($voucherTypeID == VOUCHER_RECEIPT)
					{
						$isReceipt = 1;
					}
					else
					{
						continue;
					}
				}
				
				if($paidAmount > 0 || $isPayment == 1)
				{
					$totalWithdrawals += $paidAmount;
					$balance -= $paidAmount;
					if($RefTableName <> "")
					{
						if($RefTableName == TABLE_PAYMENT_DETAILS)
						{																							
							$chequeDetails = $obj_view_bank_statement->getPaymentDetails($chkDetailID,'paymentdetails');							
							if($voucherType == 'Contra')
							{								
								$bankID = $obj_view_bank_statement->getBankIDFromDID($chequeDetails[0]['ChqLeafID'], 'paymentdetails');
							}
							if($chequeDetails[0]['ChqLeafID'] == -1)
							{
								$paymentDtlUrl = "PaymentDetails.php?bankid=".$bankID."&LeafID=".$chequeDetails[0]['ChqLeafID']."&edt=".$chkDetailID;																	
							}
							else
							{
								$paymentDtlUrl = "PaymentDetails.php?bankid=".$bankID."&LeafID=".$chequeDetails[0]['ChqLeafID']."&CustomLeaf= ". $chequeDetails[0]['CustomLeaf']. "&edt=".$chkDetailID;																	
							}
							$ledger_details = $obj_view_bank_statement->getLedgerDetails($chkDetailID, 'paymentdetails', 'PaidTo', $voucherID);
							$chequeNumber = $chequeDetails[0]['ChequeNumber'];
							$comment = $chequeDetails[0]['Comments'];
							if($reference <> 0 && $reference == $chequeDetails[0]['Reference'])
							{
								$balance += $paidAmount;
								continue;									
							}
							if($chequeDetails[0]['IsMultipleEntry'] == 1)
							{
								$reference = $chequeDetails[0]['Reference'];
								$balance += $paidAmount;
								$ledgerName = $ledger_details[0]['ledger_name']. "(" . $ledger_details[0]['id'] . ")".'<br>';
								$multEntryDetails = $obj_view_bank_statement->getTotalAmountForMultEntry($chequeDetails[0]['Reference']);
								for($k = 1; $k < sizeof($multEntryDetails); $k++)
								{										
									$paidAmount += $multEntryDetails[$k]['Amount'];
									$ledger = $obj_view_bank_statement->getLedgerDetails($multEntryDetails[$k]['id'], 'paymentdetails', 'PaidTo', $voucherID);
									$ledgerName .= $ledger[0]['ledger_name']. "(" . $ledger[0]['id'] . ")".'<br>';
								}
								$balance -= $paidAmount;								
							}
						}
						else if($RefTableName == TABLE_CHEQUE_DETAILS)
						{
							if($voucherType == 'Contra')
							{								
								$bankID = $obj_view_bank_statement->getBankIDFromDID($depositID,'chequeentrydetails');
							}
																					
							if($depositID > 0)
							{
								$paymentDtlUrl = "ChequeDetails.php?depositid=".$depositID."&bankid=".$bankID."&edt=".$chkDetailID;	
							}
							else if($depositID == DEPOSIT_NEFT || $depositID == DEPOSIT_ONLINE)
							{
								$paymentDtlUrl = "NeftDetails.php?bankid=".$bankID."&edt=".$chkDetailID;	
							}
							else if($depositID == DEPOSIT_CASH)
							{
								$paymentDtlUrl = "ChequeDetails.php?depositid=".$depositID."&bankid=".$bankID."&edt=".$chkDetailID;	
							}							
							$chequeDetails = $obj_view_bank_statement->getPaymentDetails($chkDetailID,'chequeentrydetails');									
							$ledger_details = $obj_view_bank_statement->getLedgerDetails($chkDetailID, 'chequeentrydetails', 'PaidBy',$voucherID);
							$chequeNumber = $chequeDetails[0]['ChequeNumber'];
							$comment = $chequeDetails[0]['Comments'];
						}
					}
				}
				else
				{
					$paidAmount = 0;										
				}
				if($receivedAmount != 0 || $isReceipt == 1)
				{				
					$balance += $receivedAmount;	
					$totalDeposits += $receivedAmount;
					if($RefTableName <> "")
					{			
						if($RefTableName == TABLE_PAYMENT_DETAILS)
						{	
							$ColumnName='PaidTo';																					
																	
							$chequeDetails = $obj_view_bank_statement->getPaymentDetails($chkDetailID,'paymentdetails');	
							if($voucherType == 'Contra')
							{
								$ColumnName='PayerBank';
								$bankID = $obj_view_bank_statement->getBankIDFromDID($chequeDetails[0]['ChqLeafID'], 'paymentdetails');	
							}
							if($chequeDetails[0]['ChqLeafID'] == -1)
							{
								$chqDtlUrl = "PaymentDetails.php?bankid=".$bankID."&LeafID=".$chequeDetails[0]['ChqLeafID']."&edt=".$chkDetailID;																	
							}
							else
							{														
								$chqDtlUrl = "PaymentDetails.php?bankid=".$bankID."&LeafID=".$chequeDetails[0]['ChqLeafID']."&CustomLeaf= ". $chequeDetails[0]['CustomLeaf']."&edt=".$chkDetailID;																
							}
							$ledger_details = $obj_view_bank_statement->getLedgerDetails($chkDetailID, 'paymentdetails', $ColumnName, $voucherID);	
							$chequeNumber = $chequeDetails[0]['ChequeNumber'];	
							$comment = $chequeDetails[0]['Comments'];	
							
							if($reference <> 0 && $reference == $chequeDetails[0]['Reference'])
							{
								$balance -= $receivedAmount;
								continue;	
							}
							if($chequeDetails[0]['IsMultipleEntry'] == 1)
							{
								$reference = $chequeDetails[0]['Reference'];
								$balance -= $receivedAmount;
								$ledgerName = $ledger_details[0]['ledger_name']. "(" . $ledger_details[0]['id'] . ")".'<br>';
								$multEntryDetails = $obj_view_bank_statement->getTotalAmountForMultEntry($chequeDetails[0]['Reference']);																	
								for($j = 1; $j < sizeof($multEntryDetails); $j++)
								{										
									$receivedAmount += $multEntryDetails[$j]['Amount'];
									$ledger = $obj_view_bank_statement->getLedgerDetails($multEntryDetails[$j]['id'], 'paymentdetails', 'PaidTo', $voucherID);
									$ledgerName .= $ledger[0]['ledger_name']. "(" . $ledger[0]['id'] . ")".'<br>';
								}
								
								$balance += $receivedAmount;							
							}				
						}
						else if($RefTableName == TABLE_CHEQUE_DETAILS)
						{
							if($voucherType == 'Contra')
							{
								$bankID = $obj_view_bank_statement->getBankIDFromDID($depositID, 'chequeentrydetails');
							}
														
							if($depositID > 0)
							{								
								$chqDtlUrl = "ChequeDetails.php?depositid=".$depositID."&bankid=".$bankID."&edt=".$chkDetailID;	
							}
							else if($depositID == DEPOSIT_NEFT/* || $depositID == DEPOSIT_ONLINE*/) //enable online if wants to edit Online payments
							{
								$chqDtlUrl = "NeftDetails.php?bankid=".$bankID."&edt=".$chkDetailID;	
							}
							else if($depositID == DEPOSIT_CASH)
							{
								$chqDtlUrl = "ChequeDetails.php?depositid=".$depositID."&bankid=".$bankID."&edt=".$chkDetailID;	
							}
							$chequeDetails = $obj_view_bank_statement->getPaymentDetails($chkDetailID,'chequeentrydetails');									
							$ledger_details = $obj_view_bank_statement->getLedgerDetails($chkDetailID, 'chequeentrydetails', 'PaidBy',$voucherID);
							$chequeNumber = $chequeDetails[0]['ChequeNumber'];
							$comment = $chequeDetails[0]['Comments'];
						}
						else if($RefTableName == TABLE_FD_MASTER){

							$ledger_details = $obj_view_bank_statement->getLedgerDetails($RefNo, 'fd_master', 'LedgerID',$voucherID);
							
							$chequeDetails = $obj_view_bank_statement->getPaymentDetails($RefNo,'fd_master');

							$chequeNumber = $chequeDetails[0]['fdr_no'];
							$comment = $chequeDetails[0]['note'];

							$sqlcheck = "select  Count(*) as cnt  from `paymentdetails` where `PaidTo` = '".$ledger_details[0]['id']."' ";
							$data = $m_dbConn->select($sqlcheck);
			
							$sqlcheckBalance  = "select  Count(*) as cnt  from `assetregister` where `LedgerID` = '".$ledger_details[0]['id']."' and `Debit` <> 0";
							$dataBalance = $m_dbConn->select($sqlcheckBalance);
							
							$fdStatus = "";
							
							if(($data[0]['cnt'] > 0 || $dataBalance[0]['cnt'] > 0)  && $chequeDetails[0]['fd_close'] == 1)
							{
									$fdStatus = '<font color="#00F" ><b>Closed</b></font>';
							}
							else if(($data[0]['cnt'] > 0 || $dataBalance[0]['cnt'] > 0)   && $chequeDetails[0]['fd_renew'] == 1)
							{
									$fdStatus = '<font color="#00CC33" ><b>Renewed</b></font>';	
							}
							else if(($data[0]['cnt'] > 0 || $dataBalance[0]['cnt'] > 0) && $chequeDetails[0]['fd_close'] == 0  && $chequeDetails[0]['fd_renew'] == 0 )
							{
									$fdStatus = '<font color="#00CC33" ><b>Active</b></font>';
							}
							else if($data[0]['cnt'] == 0 && $dataBalance[0]['cnt'] == 0 && $chequeDetails[0]['fd_close'] == 0  && $chequeDetails[0]['fd_renew'] == 0)
							{
									$fdStatus = '<font color="#FF0000" ><b>Pending</b></font>';	
							}

							$chqDtlUrl = "UpdateFDInterest.php?edt=".$ledger_details[0]['id']."&fdreadonly=1&fd_id=".$RefNo."&status=".strip_tags($fdStatus);		
						}
					}
				}
				else
				{
					$receivedAmount = 0;										 
				}
				$chequeDate = $details[$i]['Date'];//$chequeDetails[0]['ChequeDate'];				
				//$chequeNumber = $chequeDetails[0]['ChequeNumber'];				
				//$comment = $chequeDetails[0]['Comments'];		
				if($CategoryID == CASH_ACCOUNT && $chequeNumber == -1) 
				{
					$chequeNumber = "Cash";
				}
				else if($voucherType == 'Contra' && $chequeNumber == "-1" && $CategoryID <> CASH_ACCOUNT && $ledger_details <> "")
				{
					$arParDetails = $obj_Utility->getParentOfLedger($ledger_details[0]['id']);
					$Category = $arParDetails['category'];	
					if($Category == CASH_ACCOUNT)
					{
						$chequeNumber = "Cash";	
					}
				}
			}
	?>
           <tr style="height:30px;">
							<?php
							if($showDeleteBtn){
							if ($RefTableName == TABLE_CHEQUE_DETAILS || $RefTableName == TABLE_PAYMENT_DETAILS) { ?>
								<td style="width:5%;text-align:center;"><input type="checkbox" class="chk_select" id="chk_<?= $chkDetailID ?>" value="<?= $chkDetailID ?>" data-table="<?= $RefTableName ?>" width="25" height="20" /></td>
							<?php } else { ?>

								<td style="width:5%;text-align:center;">-</td>

							<?php }
							}
							?>

           	<td style="width:5%;text-align:center;"><a href="print_voucher.php?&vno=<?php echo base64_encode($voucherNo); ?> &type=<?php echo base64_encode($voucherTypeID) ?>" target="_blank" ><img src="images/print.png" border="0" alt="Print" style="cursor:pointer;" width="25" height="10" /></a></td>
            <td style="width:10%;text-align:center;"><?php if($chequeDate <> 0){ echo getDisplayFormatDate($chequeDate); } ?></td>
            <td style="width:20%;text-align:left;">

			<a id="view-<?=$ledger_details[0]['id']?>" onclick="getaccount_subcategory(this.id);">

			<?php 
			//print_r($ledger_details);
            if($ledgerName <> "")
            {
                echo($ledgerName);
            }
            				
				if($ledger_details <> "")
				{
					if (isset($ledger_details[0]['member_id'])) {
						echo "<a href='view_member_profile.php?scm&id=".$ledger_details[0]['member_id']."&tik_id=".time()."&m&view' target='_blank'>".$ledger_details[0]['ledger_name']." </a>" ;

					}else {
						echo $ledger_details[0]['ledger_name'];

					}
				}				
				else
				{
					echo "-";	
				}
            
            ?></a></td>

             <td style="width:15%;text-align:left;"><?php if($comment == ""){echo "-";}else{echo $comment;} ?></td>
            <td style="width:5%;text-align:center;"><?php echo $voucherType ?></td>
              <td style="width:5%;text-align:center;"><?php echo $ExvoucherNo?></td>
            <td style="width:9%;text-align:center;"><?php echo $chequeNumber ?></td>
							<td style="width:12%;text-align:right;"><?php //echo "dID : ". $RefTableName;
																	if ($chqDtlUrl <> "" && ($receivedAmount > 0 || $isReceipt == 1)) { ?>
									<a href="#" onClick="window.open('<?php echo $chqDtlUrl; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes'); "> <?php echo number_format($receivedAmount, 2); ?> </a>
								<?php } else {
																		echo number_format($receivedAmount, 2);
																	}
								?>
							</td>
							<td style="width:12%;text-align:right;"><?php
																	if ($paymentDtlUrl <> "" && ($paidAmount > 0 || $isPayment == 1)) { ?>
									<a href="#" onClick="window.open('<?php echo $paymentDtlUrl; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes'); "> <?php echo number_format($paidAmount, 2); ?> </a>
								<?php } else {
																		echo number_format($paidAmount, 2);
																	} ?>
							</td>
            <td style="width:12%;text-align:right;"><?php echo number_format($balance,2); ?></td>
            
           <td align="center">
           <?php 
			   if($details[$i]['ReconcileStatus']==1 && $details[$i]['Reconcile']==1)
			   {
				   
				echo "<img src='images/clear.png' alt='Cleared' width='25' height='25'>";   
				}
			   else if($details[$i]['ReconcileStatus']==1 && $details[$i]['Return']==1)
			   {
				   echo "<img src='images/can.png' alt='Rejected' width='25' height='25'>";
			   }
			   ?>
           </td>
        </tr>
<?php  } ?>
 <script>
		document.getElementById("closing_bal").innerHTML = '<?php echo number_format($balance,2) ?>';
		<?php if($openingBalancePresent == 0) {	?>	
			document.getElementById("opening_bal").innerHTML = '<?php echo number_format($balanceBeforeDate,2); ?>';
		<?php } ?>
	</script>
<tr style="text-align:center;background-color:#D8DDF5;height:30px;">
						<?php if ($showDeleteBtn) { ?>
							<td></td>
						<?php } ?>
	 <td ></td>
     <td ></td>
    <td ></td>
    <td ></td>
    <td > **Totals** </td>
     <td ></td>
     <td ></td>
    <td style="display: none;"></td>
    <td style="display: none;"></td>
	<td style="display: none;"></td>
	<td style="display: none;"></td>
	<td style="text-align:right;"><?php echo number_format($totalDeposits, 2); ?> </td>
	<td style="text-align:right;"> <?php echo number_format($totalWithdrawals, 2); ?> </td>						
    <td style="text-align:right;"><?php echo number_format($balance, 2); ?> </td>
  
</tr>
</tbody>
</table>
</center>
		<?php //echo $sHeader?>
</div>

<?php include_once "includes/foot.php"; ?>
  
<script>

$(document).keyup(function(e) {    
    if (e.keyCode == 27) 
	{ 
		//escape key
		var sHeaders = document.getElementsByClassName('PrintClass'), i;
	
		for (i = 0; i < sHeaders.length; i += 1)
		{
			sHeaders[i].style.display = 'none';
			
		}
    }
});
printMessage = '<?php echo $sHeader?> ';
printMessage += '<center><font style="font-size:14px;" id="statement" class="PrintClass"><b>Bank Statement<br><?php echo $bankName[0]['ledger_name']; ?><br><?php if($balanceBeforeDate > 0) { echo getDisplayFormatDate($_SESSION['default_year_start_date']); } else { echo getDisplayFormatDate($details[0]['Date']);} ?> To <?php echo getDisplayFormatDate($details[sizeof($details) - 1]['Date']); ?></b></font></center>';
 
$(document).ready(function() {
	
	$('#example').dataTable(
 {
	"bDestroy": true
}).fnDestroy();

			//if(localStorage.getItem("client_id") != "" && localStorage.getItem("client_id") != 1)
			//{
					$('#example').dataTable( {
					dom: 'T<"clear">Blfrtip',
					columnDefs: [{
					orderable: false,
					targets: 0
				}],
					"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
						
					buttons: 
					[
						{
							extend: 'colvis',
							width:'inherit'/*,
							collectionLayout: 'fixed three-column'*/
						}
					],
					"oTableTools": 
					{
						"aButtons": 
						[
							{ "sExtends": "copy", "mColumns": "visible" },
							{ "sExtends": "csv", "mColumns": "visible" },
							{ "sExtends": "xls", "mColumns": "visible" },
							{ "sExtends": "pdf", "mColumns": "visible" },
							{ "sExtends": "print", "mColumns": "visible","sMessage": printMessage + " " }
						],
					 "sRowSelect": "multi"
				},
				aaSorting : [],
					
				fnInitComplete: function ( oSettings ) {
					//var otb = $(".DTTT_container")
					$(".DTTT_container").append($(".dt-button"));
				}
				
			} );	
		 
		});

		$(document).on('click', '.chk_select', function() {

			($('.chk_select:checked').length == 0) ? $('.multiDeleteDiv').hide(): $('.multiDeleteDiv').show();
		});

		$(document).on('click', '.chk_select_all', function() {

			if ($('.chk_select_all').is(':checked')) {
				$('.chk_select').prop('checked', true);
				$('.multiDeleteDiv').show();
			} else {
				$('.chk_select').prop('checked', false);
				$('.multiDeleteDiv').hide();
			}
		});

		function deleteMultipleCheque() {

			const CHEQUE_DETAILS_TABLE = 2;
			const PAYMENT_DETAILS_TABLE = 3;
			
			try {

				let chequeDetailIds = [];
				let paymentDetailIds = [];

				$('.chk_select:checked').each(function(i, obj) {


					let refTable = $(this).attr('data-table');

					if (refTable == PAYMENT_DETAILS_TABLE) {

						paymentDetailIds.push(obj.value);
					} else if (refTable == CHEQUE_DETAILS_TABLE) {

						chequeDetailIds.push(obj.value);
					}

				});

				if (chequeDetailIds.length == 0 && paymentDetailIds.length == 0) {

					alert('To please select checkbox to delete specific entry');
					return false;
				}


				let confirmResult = confirm('Are you sure you want to delete. This will be deleted for permanently!!');

				if (confirmResult) {

					$.ajax({
						url: 'ajax/ajaxBankStatement.php',
						type: 'POST',
						data: {
							"chequeDetailIds": chequeDetailIds,
							"paymentDetailIds": paymentDetailIds,
							"method": "deleteMultipleEntries"
						},
						success: function(data) {

							console.log(data);

							var a = data.trim();
							var arr1 = new Array();
							var arr2 = new Array();
							arr1 = a.split("@@@");
							arr2 = JSON.parse("[" + arr1[1] + "]");

							console.log('arr2', arr2);
							if (arr2[0].status == 'success') {

								alert(arr2[0].msg);
								window.location.reload();
							} else {
								throw arr2[0].msg;
							}
						}
					})
				}
			} catch (error) {
				alert(error);
			}



		}
	</script>
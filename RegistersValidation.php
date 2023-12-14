<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - All Register Entry Validation</title>
</head>

<?php include_once ("classes/include/dbop.class.php");
include_once("includes/head_s.php"); 
include_once("classes/dbconst.class.php");

	if(!isset($_SESSION['society_id']))
	{
	?>
		<script>
            alert('Please Login.');
            window.location.href = 'logout.php?alog';
        </script>   
     <?php
	}
	
	include_once ("classes/RegistersValidation.class.php");
	$dbConn = new dbop();
	$obj_register = new RegistersValidation($dbConn);
	$SocietyName = $obj_register->getSocietyName();	
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ALL REGISTER ENTRIES VALIDATION REPORT</title>

<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<link href="css/messagebox.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/ajax.js"></script>
<script tytpe="text/javascript" src="js/ajax_new.js"></script>
<script tytpe="text/javascript" src="js/jquery-2.0.3.min.js"></script>
<script src='js/jsViewLedgerDetails.js'></script>
<script type="text/javascript">
function ajaxRunCleaner()
{
	$.ajax({
				url: "ajax/ajaxRegistersValidation.php",
				method: "POST",
				data: {
				   cleanInvalidEntries:'YES',
				   method:"run"
				},
				success : function(data)
				{
					alert("Invalid Voucher Entry Deleted");
					window.location.reload(true);
				}
		 });		
	
}
function DeleteEntry(tblId,tblName)
{
	var conf = confirm("Are you sure , you want to delete it ???");
	if(conf==1)
	{
		$.ajax({
			url: "ajax/ajaxRegistersValidation.php",
			method: "POST",
			data: {
				 tableName:tblName,
				 tableId:tblId,
				 method:"Delete"
				},
			success : function(data)
			{
				alert("Corrupted Entry Deleted");
					window.location.reload(true);
			}
		});	
	}
	//location.reload();
 // return false;
}
</script>
<style>
.entries
{
	font-size:12px;
}
</style>
</head>

<body >
<div class="entries">
<!--<center><h1><?php //echo $SocietyName .'[ID:'.$_SESSION['society_id'].']';?></h1></center>-->
<center><h3>ALL REGISTER ENTRIES VALIDATION REPORT</h3></center>
<?php 
if($_SESSION['role'] == ROLE_SUPER_ADMIN)
{ ?>
<form name="cleanerForm" method="post">
	<input type="button" name="RunCleaner" value="Run Cleaner"  onclick="ajaxRunCleaner()"/>
</form>
<?php  } ?>
<?php
	$obj_register->ValidateVoucherTable();
	$obj_register->ValidateRegisterEntries('liabilityregister');
	$obj_register->ValidateRegisterEntries('assetregister');
	$obj_register->ValidateRegisterEntries('incomeregister');
	$obj_register->ValidateRegisterEntries('expenseregister');

?>

<center><h3>PAYMENT ENTRIES VALIDATION REPORT</h3></center>
<br />
<?php
	$totalCount = $dbConn->select("SELECT count(*) AS total FROM `paymentdetails`");
	$ledArr = $obj_register->getLedgerNameArray();
	$result = $dbConn->select("SELECT * FROM `paymentdetails` WHERE `ChequeNumber` = ''");	
	if(sizeof($result) > 0)
	{		
		$msg = "<font color='#d9534f' >";
	}
	else
	{
		$msg = "<font color='#259242' >";
	}
	echo $msg .= "<b>**INFORMATION** Scanned ".$totalCount[0]['total']." entries, ".sizeof($result)." is invalid.</b></font><br>";
	
	for($i = 0; $i < sizeof($result); $i++)
	{
		echo "<br>Bank : ". $ledArr[$result[$i]['PayerBank']];
		echo "<br>Cheque Date : ". getDisplayFormatDate($result[$i]['ChequeDate']);
		echo "<br>Voucher Date : ". getDisplayFormatDate($result[$i]['VoucherDate']);
		echo "<br>Amount : ". $result[$i]['Amount'];
		echo "<br>Paid To : ". $ledArr[$result[$i]['PaidTo']];
		echo "<br><font color='#FF0000' >**Error**Cheque number is empty.</font>";
		$url ='bank_statement.php?LedgerID='.$result[$i]['PayerBank'];
		echo $Url =	"&nbsp;&nbsp;<a href='' onClick=\"window.open('". $url ."','popup','type=fullWindow,fullscreen,scrollbars=yes');\"><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a><hr />";	
	}
?>

<center><h3>CHEQUE ENTRIES VALIDATION REPORT</h3></center>
<br />
<?php
	$totalCount = $dbConn->select("SELECT count(*) AS total FROM `chequeentrydetails`");
	$ledArr = $obj_register->getLedgerNameArray();
	
	$result1 = $dbConn->select("SELECT * FROM `chequeentrydetails` WHERE `ChequeNumber` = ''");	
	if(sizeof($result1) > 0)
	{		
		$msg = "<font color='#d9534f' >";
	}
	else
	{		
		$msg = "<font color='#259242' >";
	}
	echo $msg .= "<b>**INFORMATION** Scanned ".$totalCount[0]['total']." entries, ".sizeof($result1)." is invalid.</b></font><br>";
	for($i = 0; $i < sizeof($result1); $i++)
	{
		echo "<br>Bank : ". $ledArr[$result1[$i]['BankID']];
		echo "<br>Cheque Date : ". getDisplayFormatDate($result1[$i]['ChequeDate']);
		echo "<br>Voucher Date : ". getDisplayFormatDate($result1[$i]['VoucherDate']);
		echo "<br>Amount : ". $result1[$i]['Amount'];
		echo "<br>Paid BY : ". $ledArr[$result1[$i]['PaidBy']];
		echo "<br><font color='#FF0000' >**Error**Cheque number is empty.</font>";
		$url ='bank_statement.php?LedgerID='.$result1[$i]['BankID'];
		echo $Url =	"&nbsp;&nbsp;<a href='' onClick=\"window.open('". $url ."','popup','type=fullWindow,fullscreen,scrollbars=yes');\"><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a><hr />";	
	}
?>

<center><h3>JOURNAL ENTRIES VALIDATION REPORT</h3></center>
<br />
<?php
	$res = $dbConn->select("SELECT id,VoucherNo,sum(Credit) as Credit,sum(Debit) as Debit,abs(sum(Credit - Debit) )as total FROM `voucher` where `VoucherTypeID`= '".VOUCHER_JOURNAL."'  group by `VoucherNo` ORDER BY `total` ASC");
	$TotalRecord = sizeof($res);
	for($i = 0;$i < $TotalRecord; $i++) 
	{ 
		if($res[$i]['total'] == '0.00') 
		{ 
			unset($res[$i]); 
		}
		else
		{
			$url = $obj_register->CheckVoucherType($res[$i]['id'])	;
			if($url == "")
			{
					$url ='VoucherEdit.php?Vno='.$res[$i]['VoucherNo'];
			}
			echo "<br><font color='#FF0000' >**Error** Debit  <".$res[$i]['Debit']."> and Credit  <".$res[$i]['Credit']."> Amount not match  for VoucherNO:[".$res[$i]['VoucherNo']."]".".</font>";
			echo $Url =	"&nbsp;&nbsp;<a href='' onClick=\"window.open('". $url ."','popup','type=fullWindow,fullscreen,scrollbars=yes');\"><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a><br />";	
			
		} 
	}				
?>
<center><h3>CORRUPTED ENTRIES VALIDATION REPORT</h3></center>
<br />
<?php
$resBank = $dbConn->select("SELECT * FROM bankregister WHERE Is_Opening_Balance = 0 AND VoucherID NOT IN(SELECT id FROM `voucher`)");
	 $TotalCruptbank = sizeof($resBank);
	 //var_dump($resBank);
	 $Bankledger ="";
	if($TotalCruptbank > 0)
	{		
		$msg = "<font color='#d9534f' >";
	}
	else
	{		
		$msg = "<font color='#259242' >";
	}
	echo $msg .= "<b>**INFORMATION** Corrupted entries, ".$TotalCruptbank." in bank register.</b></font><br>";
	for($i = 0;$i < $TotalCruptbank; $i++) 
	{ 
		$Bankledger = $obj_register->getLedgerName($resBank[$i]['LedgerID']);
		echo "<br>Date : ". getDisplayFormatDate($resBank[$i]['Date']);
		echo "<br>Paid Amount : ". $resBank[$i]['PaidAmount'];
		echo "<br>Recieved Amount : ". $resBank[$i]['ReceivedAmount'];
		echo "<br>Ledger Name : ". $Bankledger;
		echo "<br><font color='#FF0000' >**Error**Voucher Does Not Exist.</font>";
		echo $Url =	"&nbsp;&nbsp;<a href='#' onClick=\"DeleteEntry(".$resBank[$i]['id'].",'bankregister');\"><img src='images/del.gif' border='0' alt='Delete' style='cursor:pointer;'/></a><hr />";
	}
	echo "<br>";
    // Asset Register Corrupted entries -----------
	$resAsset = $dbConn->select("SELECT * FROM assetregister WHERE Is_Opening_Balance = 0 AND VoucherID NOT IN(SELECT id FROM `voucher`)");
	$TotalCruptAsset = sizeof($resAsset);
	$Assetledger="";
	if($TotalCruptAsset > 0)
	{		
		$msg = "<font color='#d9534f' >";
	}
	else
	{		
		$msg = "<font color='#259242' >";
	}
	echo $msg .= "<b>**INFORMATION** Corrupted entries, ".$TotalCruptAsset." in Asset register.</b></font><br>";
	
	for($j = 0;$j < $TotalCruptAsset; $j++) 
	{ 
		$Assetledger = $obj_register->getLedgerName($resAsset[$j]['LedgerID']);
		echo "<br>Date : ". getDisplayFormatDate($resAsset[$j]['Date']);
		echo "<br>Debit Amount : ". $resAsset[$j]['Debit'];
		echo "<br>Credit Amount : ". $resAsset[$j]['Credit'];
		echo "<br>Ledger Name : ". $Assetledger;
		echo "<br><font color='#FF0000' >**Error**Voucher Does Not Exist.</font>";
		echo $Url =	"&nbsp;&nbsp;<a href='#' onClick=\"DeleteEntry(".$resAsset[$j]['id'].",'assetregister');\"><img src='images/del.gif' border='0' alt='Delete' style='cursor:pointer;'/></a><hr />";
	}
	echo "<br>";
	// Expense Register Corrupted entries -----------
	$resExpense = $dbConn->select("SELECT * FROM expenseregister WHERE Is_Opening_Balance = 0 AND VoucherID NOT IN(SELECT id FROM `voucher`)");
	$TotalCruptExpense = sizeof($resExpense);
	$Expenseledger="";
	if($TotalCruptExpense > 0)
	{		
		$msg = "<font color='#d9534f' >";
	}
	else
	{		
		$msg = "<font color='#259242' >";
	}
	echo $msg .= "<b>**INFORMATION** Corrupted entries, ".$TotalCruptExpense." in Expense register.</b></font><br>";
	
	for($k = 0;$k < $TotalCruptExpense; $k++) 
	{ 
		$Expenseledger = $obj_register->getLedgerName($resExpense[$k]['LedgerID']);
		echo "<br>Date : ". getDisplayFormatDate($resExpense[$k]['Date']);
		echo "<br>Debit Amount : ". $resExpense[$k]['Debit'];
		echo "<br>Credit Amount : ". $resExpense[$k]['Credit'];
		echo "<br>Ledger Name : ". $Expenseledger;
		echo "<br><font color='#FF0000' >**Error**Voucher Does Not Exist.</font>";
		echo $Url =	"&nbsp;&nbsp;<a href='#' onClick=\"DeleteEntry(".$resExpense[$k]['id'].",'expenseregister');\"><img src='images/del.gif' border='0' alt='Delete' style='cursor:pointer;'/></a><hr />";
	}
	echo "<br>";
	// Income Register Corrupted entries -----------
	$resIncome = $dbConn->select("SELECT * FROM incomeregister WHERE Is_Opening_Balance = 0 AND VoucherID NOT IN(SELECT id FROM `voucher`)");
	$TotalCruptIncome = sizeof($resIncome);
	$Incomeledger="";
	if($TotalCruptIncome > 0)
	{		
		$msg = "<font color='#d9534f' >";
	}
	else
	{		
		$msg = "<font color='#259242' >";
	}
	echo $msg .= "<b>**INFORMATION** Corrupted entries, ".$TotalCruptIncome." in Income register.</b></font><br>";
	
	for($l = 0;$l < $TotalCruptIncome; $l++) 
	{ 
		$Incomeledger = $obj_register->getLedgerName($resIncome[$l]['LedgerID']);
		echo "<br>Date : ". getDisplayFormatDate($resIncome[$l]['Date']);
		echo "<br>Debit Amount : ". $resIncome[$l]['Debit'];
		echo "<br>Credit Amount : ". $resIncome[$l]['Credit'];
		echo "<br>Ledger Name : ". $Incomeledger;
		echo "<br><font color='#FF0000' >**Error**Voucher Does Not Exist.</font>";
		echo $Url =	"&nbsp;&nbsp;<a href='#' onClick=\"DeleteEntry(".$resIncome[$l]['id'].",'incomeregister');\"><img src='images/del.gif' border='0' alt='Delete' style='cursor:pointer;'/></a><hr />";
	}
	echo "<br>";
	// Liability Register Corrupted entries -----------
	$resLiability = $dbConn->select("SELECT * FROM liabilityregister WHERE Is_Opening_Balance = 0 AND VoucherID NOT IN(SELECT id FROM `voucher`)");
	$TotalCruptLiability = sizeof($resLiability);
	$Liabilityledger="";
	if($TotalCruptLiability > 0)
	{		
		$msg = "<font color='#d9534f' >";
	}
	else
	{		
		$msg = "<font color='#259242' >";
	}
	echo $msg .= "<b>**INFORMATION** Corrupted entries, ".$TotalCruptLiability." in Liability register.</b></font><br>";
	
	for($m = 0;$m < $TotalCruptLiability; $m++) 
	{ 
		$Liabilityledger = $obj_register->getLedgerName($resLiability[$m]['LedgerID']);
		echo "<br>Date : ". getDisplayFormatDate($resLiability[$m]['Date']);
		echo "<br>Debit Amount : ". $resLiability[$m]['Debit'];
		echo "<br>Credit Amount : ". $resLiability[$m]['Credit'];
		echo "<br>Ledger Name : ". $Liabilityledger;
		echo "<br><font color='#FF0000' >**Error**Voucher Does Not Exist.</font>";
		echo $Url =	"&nbsp;&nbsp;<a href='#' onClick=\"DeleteEntry(".$resLiability[$m]['id'].",'liabilityregister');\"><img src='images/del.gif' border='0' alt='Delete' style='cursor:pointer;'/></a><hr />";
	}
	
?>
</div>
<div id="openDialogOk" class="modalDialog" >
	<div style="margin:2% auto; ">
		<div id="message_ok">
		</div>
	</div>
</div>

<?php include_once "includes/foot.php"; ?>
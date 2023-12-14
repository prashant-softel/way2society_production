<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
include_once("classes/home_s.class.php");
include_once("classes/expense_report.class.php");
include_once("classes/utility.class.php");
$obj_AdminPanel = new CAdminPanel($m_dbConn);
$obj_expense = new expense_report($m_dbConn);
$obj_utility=new utility($m_dbConn);

class CIncomeDetails
{
	function __construct()
	{
		
	}
}
?>
<html>
<body>
<center>
<br>
<div class="panel panel-info" id="panel" style="display:none">
<div class="panel-heading" id="pageheader">List of Expense Heads</div>
<table id="example" class="display" cellspacing="0" width="100%">
<thead>
<tr><th><u>Expense</u></th><th align="right"><u>Balance</u></th></tr>
</thead>
<tbody>
<?php
    $arBankDetails = $obj_AdminPanel->GetExpenseSummaryDetailed();
   //print_r($arBankDetails);
   $BalAmount=0;
   
   $aryFinal = array();
   //print_r($arBankDetails);
   foreach($arBankDetails as $arData=>$arvalue)
   {
	   	//echo 'LedgerId is'.$arvalue["LedgerID"];
	   	//$BankName = $obj_AdminPanel->GetLedgerNameFromID($arvalue["LedgerID"]);

	    //print_r($gid);
	   	//$ExpenseHead = $obj_AdminPanel->GetLedgerNameFromID($arvalue["ExpenseHead"]);
	   	//$ExpenseHead = $obj_expense->show_particulars_by($arvalue["LedgerID"], $arvalue["VoucherID"]);
		$ExpenseHead = $arvalue['ledger_name'];
		
	   	$BalAmount =$arvalue["debit"];
	   	$payments = $arvalue["credit"]; 
		
		if(array_key_exists($ExpenseHead, $aryFinal))
		{
			$amount = $aryFinal[$ExpenseHead];
			$amount = $amount + $payments - $BalAmount;
			$aryFinal[$ExpenseHead] = $amount;
		}
		else
		{
			$aryFinal[$ExpenseHead] = $payments - $BalAmount;
		}
		
		//print_r($aryFinal);
	   	/*//echo 'balance1'.$BalAmount;
	   	//$BalAmount =$BalAmount+$receipts;
	   	echo 'balance2 '.$BalAmount;
	   	echo '<br />';
	   	print_r($arBankDetails);*/
	   	//$get_ledger_id="select id from `ledger` where ledger_name='".$ExpenseHead."'";
		//$res00=$obj_AdminPanel->m_dbConn->select($get_ledger_id);
		//$_SESSION['lid']=$res00[0]['id'];
        
	   	//$Amount = $arvalue; 
	   	//echo "<tr><td>".$ExpenseHead."</td><td align=right><a href='expense_report.php?id=".$_SESSION['lid']." '>". number_format($BalAmount,2) ."</a></td><td></td></tr>";
   }
   
   if(sizeof($aryFinal) > 0)
   {
	   foreach($aryFinal as $key=>$value)
	   {
		   	$get_ledger_id="select id from `ledger` where ledger_name='".$key."'";
			$res00=$obj_AdminPanel->m_dbConn->select($get_ledger_id);
			
			$gid=$obj_utility->getParentOfLedger($res00[0]['id']);
					
		   	echo "<tr><td>" . $key . "</td><td align=right><a href='view_ledger_details.php?&lid=".$res00[0]['id']."&gid=".$gid['group']."&pg=".Expense." '>". number_format($value,2) ."</a></td></tr>";
	   }
	   
   }
   ?>
</tbody>
   </table>
   </div>
   </center>
   <?php include_once "includes/foot.php"; ?>
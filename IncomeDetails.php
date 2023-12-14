
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Income Details</title>
</head>




<?php include_once("includes/head_s.php");
include_once("classes/home_s.class.php");
include_once("classes/utility.class.php");
$obj_AdminPanel = new CAdminPanel($m_dbConn);
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
<div class="panel-heading" id="pageheader">List of Income Heads</div>
<table id="example" class="display" cellspacing="0" width="100%">
<thead>
<tr><th><u>Income</u></th><th align="right"><u>Balance</u></th></tr>
</thead>
<tbody>
<?php
    $arBankDetails = $obj_AdminPanel->GetIncomeSummaryDetailed();
   //print_r($arBankDetails);
   
   foreach($arBankDetails as $arData=>$arvalue)
   {
	   $BankName = $obj_AdminPanel->GetLedgerNameFromID($arvalue["LedgerID"]);
	   $receipts =$arvalue["debit"];
	   $payments = $arvalue["credit"]; 
	   $BalAmount = $payments - $receipts ;
		$get_ledger_id="select id from `ledger` where ledger_name='".$BankName."' AND `society_id` = '".$_SESSION['society_id']."'";
		$res00=$obj_AdminPanel->m_dbConn->select($get_ledger_id);
		$_SESSION['lid']=$res00[0]['id'];
		
		$gid=$obj_utility->getParentOfLedger($res00[0]['id']);
		//echo $get_ledger_id;
		//echo $_SESSION['lid'];
	   //$Amount = $arvalue; 
	   /*echo "<tr><td>".$BankName."</td><td align=right>". number_format($BalAmount,2) ."</td><td></td></tr>";
	   */
	   echo "<tr><td>".$BankName."</td><td align=right><a href='view_ledger_details.php?lid=".$_SESSION['lid']."&gid=".$gid['group']."&pg=".Income." '>". number_format($BalAmount,2) ."</a></td></tr>";
	   
   }
   ?>
</tbody>
   </table>
   </div>
  
   </center>
   <?php include_once "includes/foot.php"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - List of Liability</title>
</head>


<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
include_once("classes/home_s.class.php");
$obj_AdminPanel = new CAdminPanel($m_dbConn);

class CAssetSummary
{
	function __construct()
	{
		
	}
}
?>
<html>
<body>
<center>
<div class="panel panel-info" id="panel" style="display:none">
<div class="panel-heading" id="pageheader">List of Liability Heads</div>
<br>
<table id="example" class="display" cellspacing="0" width="100%">
<thead>
<!--<tr><th><u>Category</u></th><th><u>SubCategory</u></th><th><u>Ledger Name</u></th><th align="center"><u>Debits</u></th><th align="center"><u>Credits</u></th><th align="center"><u>Balance</u></th></tr>-->
<tr><th><u>Category</u></th><th><u>SubCategory</u></th><th align="center"><u>Debits</u></th><th align="center"><u>Credits</u></th><th align="center"><u>Balance</u></th></tr>
</thead>
<tbody>
<?php
$arBankDetails = $obj_AdminPanel->GetSummary(LIABILITY);
    //$arBankDetails = $obj_AdminPanel->GetLiabilitySummary(4);
   //print_r($arBankDetails);
   
   foreach($arBankDetails as $arData=>$arvalue)
   {
	   $category =$arvalue["CategoryID"];
	   $subcategory =$obj_AdminPanel->GetCategoryNameFromID($arvalue["SubCategoryID"]);
	   $subcategoryid =$arvalue["SubCategoryID"];
	   $BankName = $obj_AdminPanel->GetLedgerNameFromID($arvalue["LedgerID"]);
	   $receipts =$arvalue["debit"];
	   $payments = $arvalue["credit"]; 
	   $BalAmount =  $payments - $receipts;

	   //$Amount = $arvalue; 
	   if($receipts > 0 || $payments > 0)
	   {
	   //echo "<tr><td>".$category."</td><td><a href='ledger_details.php?gid=".$category."&cid=".$subcategoryid."&pg=".Liabilty." '>".$subcategory."</a></td><td><a href='view_ledger_details.php?lid=". $arvalue["LedgerID"]."&gid=".$category."&pg=".Liabilty."' style='color:#0000FF;'>".$BankName."</a></td><td align=right>". number_format($receipts,2) ."</td><td align=right>". number_format($payments,2) ."</td><td align=right><b>". number_format($BalAmount,2) ."</b></td></tr>";
	  	echo "<tr><td>".$category."</td><td><a href='ledger_details.php?gid=".$category."&cid=".$subcategoryid."'>".$subcategory."</a></td><td align=right>". number_format($receipts,2) ."</td><td align=right>". number_format($payments,2) ."</td><td align=right>". number_format($BalAmount,2) ."</td></tr>";
	   }
   }   
   ?>
   </tbody>
   </table>

   </div>
   </center>
<?php include_once "includes/foot.php"; ?>
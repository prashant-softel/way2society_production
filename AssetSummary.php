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
<br>
<div class="panel panel-info" id="panel" style="display:none">
<div class="panel-heading" id="pageheader">Assets Summary</div>
<table id="example" class="display" cellspacing="0" width="100%">
<thead>
<tr><th><u>Category</u></th><th><u>SubCategory</u></th><th align="center"><u>Debits</u></th><th align="center"><u>Credits</u></th><th align="center"><u>Balance</u></th></tr>
</thead>
<tbody>
<?php
    $arBankDetails = $obj_AdminPanel->GetSummary(ASSET);
	//$arBankDetails = $obj_AdminPanel->GetAssetSummary(4);
   //print_r($arBankDetails);
   
   foreach($arBankDetails as $arData=>$arvalue)
   {
	   $category =$arvalue["CategoryID"];
	   $subcategoryid =$arvalue["SubCategoryID"];
	   $subcategory =$obj_AdminPanel->GetCategoryNameFromID($arvalue["SubCategoryID"]);
//	   $BankName = $obj_AdminPanel->GetLedgerNameFromID($arvalue["LedgerID"]);
	   $receipts =$arvalue["debit"];
	   $payments = $arvalue["credit"]; 
	   $BalAmount = $receipts - $payments;

	   //$Amount = $arvalue; 
//	   echo "<tr><td>".$category."</td><td>".$subcategory."</td><td>".$BankName."</td><td align=right>". number_format($receipts,2) ."</td><td align=right>". number_format($payments,2) ."</td><td align=right>". number_format($BalAmount,2) ."</td><td></td></tr>";
	   echo "<tr><td>".$category."</td><td><a href='ledger_details.php?gid=".$category."&cid=".$subcategoryid."'>".$subcategory."</a></td><td align=right>". number_format($receipts,2) ."</td><td align=right>". number_format($payments,2) ."</td><td align=right>". number_format($BalAmount,2) ."</td></tr>";
   }
   ?>
   <?php //include_once "includes/foot.php"; ?>
   </tbody>
   </table>

   </div>
   </center>

<?php include_once "includes/foot.php"; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Generate Deposit Slip</title>
</head>




<?php include_once "ses_set_as.php"; 
?>
<?php
include_once "common/CommonMethods.php";
include_once "classes/GenerateDepositSlip.class.php";
include_once "classes/dbconst.class.php";
include_once "classes/utility.class.php";

include_once "classes/include/fetch_data.php";

$obj_view_bank_statement = new bank_statement($m_dbConn);
$objFetchData = new FetchData($m_dbConn);
$objUtility = new utility($m_dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);

?>
<?php
$from_date = $_REQUEST['from'];
$to_date = $_REQUEST['to'];
$SlipDetails = $obj_view_bank_statement->GetSlipDetails($_REQUEST["depositslipid"], $from_date, $to_date);
?>

<html>
<head>
	<style>table{}
    table, th, td {
   		border: 1px solid #cccccc;
	}	
    </style>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <script type="text/javascript" src="js/ajax.js"></script>
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
	</script>
</head>
<body>
<br>
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
<center>
<div id="mainDiv" style="width:80%;">
<?php include_once( "report_template.php" ); // get the contents, and echo it out.?>
<table id="Slip" width="90%" border="1px solid #cccccc" cellspacing="5" cellpadding="5" style="border-collapse:collapse; border:1px solid #cccccc;" align="center">
<tr><td colspan="7" align="left">
<h4 style="margin:0px;"><?php 
$LedgerID = $obj_view_bank_statement->getLedgerID($_REQUEST["depositslipid"]);
$bankName = $obj_view_bank_statement->getBankName($LedgerID);
?>
<center>
<font style="font-size:16px;"><b><?php echo $objFetchData->objSocietyDetails->sSocietyName;?></b></font><br>
<font style="font-size:14px;font-weight:100;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></font>
</center>
</h4>
</td></tr>
<tr><td colspan="7" style="text-align:center; border-bottom:none;"><center><font  style="font-size:16px;"><b>Bank Slip No. <?php echo ($obj_view_bank_statement->getDesc($_REQUEST["depositslipid"])[0]['id']) ?></b></font></center> </td></tr>
<tr>
    <td colspan="5" style="text-align:center; border-right:none; border-top:none;">
        <h4 style="margin:0px;" align="left">
        <?php
        echo $bankName[0]['ledger_name'] ?></b> A/C No : <b>
        <?php echo $obj_view_bank_statement->GetAccountNo($LedgerID) ?></b>&nbsp;<br/>Branch : <b><?php echo $obj_view_bank_statement->GetBankBranch($LedgerID) ?><?php $_REQUEST['bankid'] ?></b>
        </h4>
    </td> 
    <td colspan="2" style="text-align:center;border-left:none; border-top:none;" valign="top">
        <h4 style="margin:0px;" align="right">
        <?php $sql = "select curdate()as curdate";
                $res = $m_dbConn->select($sql);	
                echo "Date: ".getDisplayFormatDate( $res[0]['curdate'], "-");?>
<br/>
<?php	echo "Description : " .  ($obj_view_bank_statement->getDesc($_REQUEST["depositslipid"])[0]['desc']) ;

?>
        </h4>
    </td>
</tr>
<tr>
	<th style="width:3%;text-align:center;">Sr.</th>
    <th style="width:22%;text-align:center;">Unit No.</th>
    <th style="width:11%;text-align:center;">Cheque No.</th>
    <th style="width:11%;text-align:center;">Cheque Date</th>
    <th style="width:25%;text-align:center;">Drawee Bank</th>
    <th style="width:15%;text-align:center;">Branch</th>
    <th style="width:15%;text-align:center;">Amount</th>
    <th style="width:28%;text-align:center;display:none;" class="no-print">Comments</th>
</tr>
	<?php
		$TotalAmount = 0;
		//echo "count" .sizeof($SlipDetails);			
		for($i = 0; $i < sizeof($SlipDetails); $i++)
		{
			//echo "count" .$i;			
			$PaidByList = explode(",",$SlipDetails[$i]['PaidByList']);
			$UnitNo = "";
			if($PaidByList <> "")
			{	
				foreach ($PaidByList as $PaidBy)
				{	
					$UnitNo[] = $obj_view_bank_statement->getBankName($PaidBy)[0]['ledger_name'];
				}
			}
			$ChequeDate = $SlipDetails[$i]['ChequeDate'];
			$ChequeNo = $SlipDetails[$i]['ChequeNumber'];
			$Amount = $SlipDetails[$i]['Total_amt'];			
			//$voucherType = $obj_view_bank_statement->getVoucherType($voucherTypeID);
			$PayerBank = $SlipDetails[$i]['PayerBank'];
			$PayerBranch = $SlipDetails[$i]['PayerChequeBranch'];
			$comments = $SlipDetails[$i]['Comments'];			
					
			?>
		   <tr  cellspacing="2">
			<td style="width:2%;text-align:center;"><?php echo $i+1; ?></td>
			<td style="width:10%;text-align:center;"><?php echo implode(", ",$UnitNo) ?></td>
			<td style="width:10%;text-align:center;"><?php echo $ChequeNo ?><br></td>
			<td style="width:10%;text-align:center;"><?php echo getDisplayFormatDate($ChequeDate) ?></td>
			<td style="width:10%;text-align:center;"><?php echo $PayerBank ?></td>
			<td style="width:15%;text-align:center;"><?php echo $PayerBranch ?></td>
			<td style="width:15%;text-align:right;"><?php echo number_format($Amount,2) ?></td>
            <td style="width:28%;text-align:center;display:none;"  class="no-print"><?php echo $comments ?></td>
            <?php $TotalAmount = $TotalAmount + $Amount; ?>
		</tr>
		
		<?php  
		} 
		
		?>
        <!--<table border="1" style="width:100%"><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>-->
        <tr><td  colspan="5" align="left"><b><?php
		if($TotalAmount <> "")
		{
			 echo "Rupees ".$objUtility->convert_number_to_words($TotalAmount);  ?> Only
        <?php
        }
		?>
       </b> </td><td align="center"><b>Total (Rs.)</b></td><td align="right"><b><?php echo number_format($TotalAmount,2) ?></b></td></tr>
        <tr>
        <td colspan="4" style="border:none">
			<div id="bill_footer" style="text-align:left;border-top:0px solid black;padding-left::10px;">
        	<br><br>Cashier's Signature
        	</div>
		</td>
        <td colspan="3" style="border:none">
			<div id="bill_footer" style="text-align:right;border-top:0px solid black;padding-right:10px;">
        	<br><br>Authorised Signatory
        	</div>
		</td>
		</tr>
</table>
</div>
</center>
		
</body>
</html>
  
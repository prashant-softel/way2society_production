<?php
include_once("includes/head_s.php");
include_once ("check_default.php");
include_once("classes/changelog.class.php");
include_once("classes/utility.class.php");
include_once("classes/view_ledger_details.class.php");
include_once("classes/genbill.class.php");

$m_dbConnRoot = new dbop(true);
$obj_changeLog = new changeLog($m_dbConn, $m_dbConnRoot);
$obj_utility = new utility($m_dbConn, $m_dbConnRoot);
$obj_ledger_details = new view_ledger_details($m_dbConn);
$obj_bill = new genbill($m_dbConn);

$data = $obj_changeLog->showChangeLog($_REQUEST['vTable'], $_REQUEST['refNo'], true);
$voucherDetails = $obj_utility->getVoucherTypeAndVoucherID($_REQUEST['vTable'], $_REQUEST['refNo']);

?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
    <!-- <script type="text/javascript" src="js/jsChangeLog.js"></script> -->
	<script type="text/javascript" src="js/populateData.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }
	</script>
    
<style>
	#example_wrapper{
		overflow-x: auto;
	}

	.diffColor{
		background-color: yellow;
		color: red;
	}

	.note{
		text-align: left;
		padding: 10px;
		font-size: 12px;
		color: blue;
	}
	.dot {
	    height: 15px;
		width: 15px;
		background-color: #ff0;
		border-radius: 50%;
		display: inline-block;
	}
</style>    
</head>

<body>
<br>
<div id="middle">
<div class="panel panel-default">
<div class="panel-heading" id="pageheader">Change Log</div>
<center><br>
<br>

<?php 

	if(!empty($data)){

		if($data[count($data) - 1]['Changed_Mode'] <> DELETE){

	if($data[0]['ChangedTable'] == TABLE_JOURNAL_VOUCHER){

		$url = 'VoucherEdit.php?Vno='.$data[count($data)-1]['ChangedKey'];					   
	}
	else if($data[0]['ChangedTable'] == TABLE_BILLREGISTER){

		$billDetails = $obj_utility->getBillDetails($_REQUEST['refNo']);

		$url = 'Maintenance_bill.php?UnitID='.$billDetails[0]['UnitID'].'&PeriodID='.$billDetails[0]['PeriodID'].'&BT='.$billDetails[0]['BillType'];
	}
	else if($data[0]['ChangedTable'] == TABLE_SALESINVOICE){

		$invoiceDetail = $obj_bill->GetInvoiceDetail($data[0]['ChangedKey']);
		$url = 'Invoice.php?UnitID='.$invoiceDetail[0]['UnitID'].'&inv_number='.$invoiceDetail[0]['Inv_Number'].'';
	}
	else if($data[0]['ChangedTable'] == TABLE_CREDIT_DEBIT_NOTE){
		
		$creditDebitDetail = $obj_bill->FetchDebitCreditDetails($data[0]['ChangedKey']);
		$url = 'Invoice.php?debitcredit_id='.$creditDebitDetail[0]['ID'].'&UnitID='.$creditDebitDetail[0]['UnitID'].'&NoteType='.$creditDebitDetail[0]['Note_Type'].'';
	}
	else if($data[0]['ChangedTable'] == TABLE_TDSCHALLAN)
	{
		
		//$creditDebitDetail = $obj_bill->FetchDebitCreditDetails($data[0]['ChangedKey']);
		$url = 'print_challan.php?id='.$data[0]['ChangedKey'].'';
	}
	else{

			$url = $obj_ledger_details->generatUrl($voucherDetails[0]['id'], $voucherDetails[0]['VoucherTypeID']);					   
		} ?>
		<a href="<?=$url?>" target="_blank"><button  class="btn btn-primary">View Entry</button></a>
	<?php 
	
	}	
	?>
	
<?php }?>
<div class="note">
	<label><b>Note:</b> <span class="dot"></span><span> color represent changes done by user</span></label>
</div>

<div id="FilterData">   
<?php echo $obj_changeLog->showFullLogDetail($_REQUEST['vTable'], $_REQUEST['refNo']);?>
</div> 
   
</center>
</div>
</div>
<?php include_once "includes/foot.php"; ?>



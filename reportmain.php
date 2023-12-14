<?php include_once "ses_set_as.php"; ?>
<?php
	include_once("includes/head_s.php");
//}
include_once("header.php");
include_once("classes/dbconst.class.php");
include_once("classes/home_s.class.php");
$dbConnRoot =new dbop(true);
$dbConn = new dbop();
$obj_home = new CAdminPanel($dbConn,$dbConnRoot);
$result = $obj_home->GetSecurityDB($_SESSION['society_id']);

if(isset($_GET['ssid'])){if($_GET['ssid']<>$_SESSION['society_id']){?><script>window.location.href = "logout.php";</script><?php }}
$today_date = date('Y-m-d');
$last_month = date("Y-m-d", strtotime("-1 month"));

/*include_once("classes/bill_period.class.php");
$obj_bill_period = new bill_period($m_dbConn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$_SESSION['year'] = $_REQUEST['year'];
	$_SESSION['period'] = $_REQUEST['period'];
}
*/
//echo $_SESSION['default_year'].	$_SESSION['default_period'] . $_SESSION['default_interest_on_principle'] . $_SESSION['default_current_asset'] .
			//$_SESSION['default_bank_account'] . $_SESSION['default_due_from_member'] .	$_SESSION['society_id'];
?>
 
<html>
<head>
	<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="js/ajax_new.js"></script>
    <script language="javascript" type="application/javascript">
	
	function go_error()
    {
        setTimeout('hide_error()',3000);	
    }
	
    function hide_error()
    {
		document.getElementById('error').innerHTML = '';
        document.getElementById('error').style.display = 'none';	
    }
	
	

	</script>
    
    <style>
		#block_head{
			background:#337ab7;
			font-family:Verdana, Geneva, sans-serif;
			color:#FFFFFF;
			font-weight:bold;
			font-size:16px;
			padding:5px;
			width:100%;
			text-align:center;
		}
		#block_data{
			
			font-family:Verdana, Geneva, sans-serif;
			color:#000000;
			font-size:14px;
			text-align:center;
			width:100%;
			padding-top:2px;
			padding-bottom::2px;
			border:none;
		
		}
		#data_link{
			font-family:Verdana, Geneva, sans-serif;
			color:#337ab7;
			text-align:center;
			width:100%;
		
		
		}
	</style>
   
<title>Report</title>
</head>

<?php if(isset($_REQUEST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<br>
<div id="middle">
<div class="panel panel-info" id="panel" style="display:none">
    <div class="panel-heading" id="pageheader">Reports</div>

<?php if(!isset($_REQUEST['ws'])){ $val ='';?>
<!--
<br>
<center>
<a href="society_view.php?imp" style="color:#00F; text-decoration:none;"><b>Add Unit</b></a>
</center>
-->
<?php }else{ $val = 'onSubmit="return val();"';
?>
<br>
<center>
<a href="wing.php?imp&ssid=<?php echo $_REQUEST['ssid'];?>&s&idd=<?php echo time();?>" style="color:#00F; text-decoration:none;"><b>Back</b></a>
<?php } ?>
<br><br>

<table style="width:100%;">
<tr>
	<td style="width:50%;"><div id="block_head">Member Reports</div></td>
	<td style="width:50%;"><div id="block_head">Accounting Reports</div></td>
</tr>
<tr>
	<td>
        <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('dues_advance_frm_member_report.php?sid=<?php echo $_SESSION['society_id']; ?>','_blank')">Due-Advance From Member</button>
        
        <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('memberdues_regularreport.php','_blank')">Dues From Member Regular</button>
       	<button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('ContributionLedgerDetailed.php','_blank')">Member Bill Register</button>
        <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('bill_receipt_report.php', '_blank')">Member Receipt Report</button> 
        <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('multiple_ledger_print.php','_blank');">Multiple Ledger Report</button>
		<button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('member_ledger_report.php?uid=0', '_blank')">Member Ledger Report</button>
        <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('ownership_transferhistory_report.php', '_blank')">Ownership Transfer History Report</button>
        <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('ParkingDetails.php', '_blank')">Vehicle Parking Report</button>
        <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('VehicleParkingDetails.php', '_blank')">Vehicle Summary Report</button>
	<button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('show_tenant.php?TenantList=0', '_blank')">Leave & License Report</button>
       
    <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('lien.php?type=open', '_blank')">Lien / Mortgage Report</button>    
    
    <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('srreport.php', '_blank')">Service Request Report</button>    
    <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('renovationRequest.php?type=pending', '_blank')">Renovation Request Report</button> 
    <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('addressproofApproval.php?type=pending', '_blank')">Address Proof Request Report</button>    
       
    </td>  
    <td>
   		<button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('financial_reports.php','_blank')">Financial Reports</button>
        <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('expense_reports.php','_blank')">Expense Reports</button>
        <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('allexpense_reports.php','_blank')">Expense Reports Details</button>
        <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('BankAccountDetails.php','_blank')"> Bank Accounts </button>
       	<button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('common_period.php?cashflow','_blank')"> Cash Flow Report </button>
        <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('common_period.php?bankreco','_blank')"> Bank Reconciliation Report </button>
        <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('FixedDepositReport.php','_blank')"> Fixed Deposits</button>
        <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('FixedAssetMgmt.php','_blank')"> Fixed Assets</button>
        <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('ledger_voucher_detail.php','_blank')"> All Vouchers Detail</button>
         <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('ledger_voucher_report.php','_blank')"> All Vouchers Reports</button>
        <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('reverse_charges.php?&uid=0', '_blank')"> Reversal Charges Report</button>
         <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('TDS_Challans.php', '_blank')"> TDS Challan Report</button>
     
     <?php if($result[0]['security_dbname'] <> '')
	 {?>
    	<table style="width:100%;">
			<tr>
				<td style="width:50%;"><div id="block_head">Security Manager</div></td>
			</tr>
			<tr>
			
			<td> <button type="button"  style="border:none; font-weight: bold;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('security_reportmain.php', '_blank')">Security Manager Module</button></td>
			</tr>
       </table>
       <?php }?>
	</td>
  </tr>
</table>
<br>
<table style="width:100%;">
<!--<tr>
	<td  colspan="2"><div id="block_head">Register Reports</div></td>
</tr>-->
<tr>
	<td style="width:50%"><div id="block_head">Society Registers</div></td>
	<td style="width:50%"><div id="block_head">Member Registers</div></td>
</tr>
<tr>
	 <td>
    	<div id="block_data">
      	<button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('common_period.php?income','_blank')">Income Register</button>        
        </div>
        <div id="block_data">
      	<button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('common_period.php?expense','_blank')">Expense Register</button> 
        </div>
        <div id="block_data">
      	<button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('asset_report.php','_blank')">Asset Register</button>
        </div>
        <div id="block_data">
      	<button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('liability_report.php','_blank')">Liability Register</button>
        </div>
        <div id="block_data">
      	<button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('common_period.php?payment','_blank')">Payment Register</button>
        </div>
        <div id="block_data">
      	<button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('common_period.php?receipt','_blank')">Receipt Register</button>
        </div>
         <div id="block_data">
      	<button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('ChangeLog_New.php','_blank')">Change Log</button>
        </div>
		<div id="block_data">
      	<button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('common_period.php?sinkingfund','_blank')">Sinking Fund Report</button>
        </div>
		<div id="block_data">
      	<button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('common_period.php?investmentregister','_blank')">Investment Register Report</button>
        </div>
      </td>
   
   <td>
   		<div id="block_data">
        <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('i_register_UI.php','_blank')">I - Register</button>
        </div>
        <div id="block_data">
        <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('j_register.php','_blank')">J - Register</button>
        </div>
        <div id="block_data">
        <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('nomination_register.php','_blank')">Nomination - Register</button>
        </div>
        <div id="block_data">
        <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('share_register.php','_blank')">Share - Register</button>
        </div>    	
   </td>
   
</tr>
</table>
</center>
<br/>
</div>
</div>
<?php include_once "includes/foot.php"; ?>

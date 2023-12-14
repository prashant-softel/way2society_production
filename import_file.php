<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>

<title> W2S - Import Files </title>
</head>

<?php include_once("includes/head_s.php");
//include_once("header.php");

if(isset($_GET['ssid'])){if($_GET['ssid']<>$_SESSION['society_id']){?><script>window.location.href = "logout.php";</script><?php }}

include_once("classes/bill_period.class.php");
include_once("classes/dbconst.class.php");
$obj_bill_period = new bill_period($m_dbConn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$_SESSION['year'] = $_REQUEST['year'];
	$_SESSION['period'] = $_REQUEST['period'];
}

//echo $_SESSION['default_year'].	$_SESSION['default_period'] . $_SESSION['default_interest_on_principle'] . $_SESSION['default_current_asset'] .
			//$_SESSION['default_bank_account'] . $_SESSION['default_due_from_member'] .	$_SESSION['society_id'];
?>
 
<html>
<head>
	<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="js/ajax_new.js"></script>
    <script type="text/javascript" src="js/populateData.js"></script>
    <script type="text/javascript"></script>
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
	
	function get_period(year_id)
	{
		if(year_id == null)
		{
			populateDDListAndTrigger('select#period', 'ajax/get_unit.php?getperiod&year=' + document.getElementById('year').value, 'period', 'hide_error', false);
		}
		else
		{
			populateDDListAndTrigger('select#period', 'ajax/get_unit.php?getperiod&year=' + year_id, 'period', 'hide_error', false);
		}
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
			font-size:12px;
			text-align:center;
			width:100%;
			padding-top:2px;
			padding-bottom::2px;
			border:none;
		}
		#data_link{
			font-family:Verdana, Geneva, sans-serif;
			text-align:center;
			width:100%;
		}
	</style>
</head>

<?php if(isset($_REQUEST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<br>
<div id="middle">
<div class="panel panel-default">
<div class="panel-heading" id="pageheader">Import Files</div>


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
<!--<table style="width:100%;">
<tr>
	<td style="width:50%;"><div id="block_head">Import Data</div></td>
    <td style="width:50%;"><div id="block_head">Import Data</div></td>
	
    
</tr>

<tr>
	<td>
    	<div>
        <a href="#" id="data_link"></a><br>
      	<button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_payments_receipts.php?type=payment'">Import Payment Register</button>
        <button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_payments_receipts.php?type=receipts'">Import General Receipt Register</button>
        <?php //if($_SESSION['society_client_id'] <> 1 && $_SESSION['society_client_id'] <> 9)
		// {
			?>
        <button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_opening_balance.php'">Member Opening Balance</button>
            <?php
		 //}
		 ?>
         <?php //if($_SESSION['society_client_id'] == 2 || $_SESSION['society_client_id'] == 8)
		 //{
			?> 
         	<button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_fixed_deposit.php'">Import Fixed Deposits</button>
            <?php
		 //}
		 ?>
        <!--<a href="rev_import.php" id="data_link">Reverse Import</a><br><br>-->
        <!--<a href="#" id="data_link"></a><br>
        </div>
    </td>
    
</tr>
</table>-->

<table style="width:100%;">
<tr>
<td style="width:50%;"><div id="block_head">Member Data</div></td>
<td style="width:50%;"><div id="block_head">Accounting Data</div></td>
</tr>
<tr>
	<td>
   <!-- if($_SESSION['society_client_id'] <> 1 && $_SESSION['society_client_id'] <> 9 )-->
    	<?php if($_SESSION['society_client_id'] <> 1 && $_SESSION['society_client_id'] <> 9 )
		 {
		?>
        	<button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_opening_balance.php'">Member Opening Balance</button>
         <?php
		 }
		 ?>
          <?php //if($_SESSION['society_client_id'] == 2 || $_SESSION['society_client_id'] == 8)
		 {
		?> 
         	<button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_tariff.php?periodid=0'">Import Tariff</button>
         <?php
		 }
		 ?>
         
      
         <button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_member.php'">Import Members Data</button>
         
          <button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_ser_prd.php'">Import Service Provider Data</button>
         
          <button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_reverse_charge.php'">Import Reverse Charges/Fine</button>
          
         <button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_invoice.php?Note=<?php echo CREDIT_NOTE; ?>'">Import Credit Note</button>
         
          <button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_invoice.php?Note=<?php echo DEBIT_NOTE; ?>'">Import Debit Note</button>
         <button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_vehicleParking.php?Note'">Import Vehicle Data</button>

		 <button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_tenant_data.php?Note'">Import Tenant Data</button>
    </td>
    
    <td>
  	     <!--<a href="#" id="data_link"></a><br>-->
         
          	<button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_ledger.php'">Import Ledgers Data</button>  
            
         <button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_payments_receipts.php?type=receipts'">Import General Receipt </button>
         
      	<button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_payments_receipts.php?type=payment'">Import Payment</button>
        
        <button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_JV.php'">Import Journal Voucher</button>
            
      	<button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_invoice.php'">Import Invoice</button>
      
    	
         <?php //if($_SESSION['society_client_id'] == 2 || $_SESSION['society_client_id'] == 8) removed the condition by amit on prashant sir request
		 {
			?> 
         	<button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_fixed_deposit.php'">Import Fixed Deposits</button>
            <?php
		 }
		 ?>
		 <button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_share_certificate.php'">Import Share Certificate</button>
    </td>	
    
         
</tr>
<tr>
<td style="width:50%;"><div id="block_head">Tally Import</div></td>
<td style="width:50%;"></td>
</tr>
<td>
<button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_master_daybook.php'">Import Tally Master</button>	
 		
        <button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_daybook.php'">Import Tally DayBook</button>
        
        <?php if($_SESSION['login_id'] == 4 || $_SESSION['login_id'] == 2216 || $_SESSION['login_id'] == 3429 || $_SESSION['login_id'] == 2931 || $_SESSION['login_id'] == 8671){?>
        <button type="button"  style="border:none;" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.location.href='import_bank_statement.php'">Import Bank Statement</button>
    	<?php }?>
    
    </td>	
</tr>
</table>
</center>
</div>
</div>
<?php include_once "includes/foot.php"; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Bank Reconciliation</title>
</head>

<?php include_once("includes/head_s.php");
include_once "classes/bank_reconciliation.class.php";
include_once "classes/bank_statement.class.php";
include_once("classes/utility.class.php");
include_once("classes/dbconst.class.php");
error_reporting(1);

if($_REQUEST["ledgerID"] == "")
{
	echo "<script>alert('Error ! Please pass LedgerID to generate Report');</script>";
	exit;
}

$obj_bank_reconciliation = new bank_reconciliation($m_dbConn);
$obj_view_bank_statement = new bank_statement($m_dbConn);
$obj_Utility = new utility($m_dbConn);
//echo 'Penalty : '. PENALTY_TO_MEMBER;	
?>

<?php
if(isset($_POST['From']) && isset($_POST['To']))
{
	$from = $_POST['From'];
	$to   = $_POST['To'];
}
else
{
		
	$Dates_arr = $obj_Utility->getSingleMonthDates();
	if($Dates_arr['IsCurrentYear'] == true)
	{
		$from  = getDisplayFormatDate($Dates_arr['from_date']);
		$to  = getDisplayFormatDate($Dates_arr['to_date']);
	}
}

if(!empty($from) && !empty($to))
{
	$details = $obj_bank_reconciliation->getDetails($_REQUEST["ledgerID"], $_POST['dateType'], $_POST['voucherType'], $_POST['status'], $from,$to, $_POST['chequeNo'], $_POST['ledger']);		
}
$bankName = $obj_view_bank_statement->getBankName($_REQUEST["ledgerID"]);
$bankDetails = $obj_view_bank_statement->getBankDetails($_REQUEST["ledgerID"]);
$society_penalty = $obj_bank_reconciliation->getChqBounceCharge($_REQUEST["ledgerID"]);
$society_details = $obj_Utility->GetSocietyInformation($_SESSION['society_id']);
$reco_date_same_as_voucher = $society_details['reco_date_same_as_voucher'];

?>

<html>
<head>
	<script language="javascript">
		/*function Enable_BankPenalty(status)
		{			
			if (status) 
            $('#penalty').fadeIn('slow');
       		 else 
            $('#penalty').fadeOut('slow');			
		}*/
	</script>	
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <script type="text/javascript" src="js/jsreconciliation.js"></script>	
    <script type="text/javascript" src="js/validate.js"></script>	
    <script type="text/javascript">
	
	
	maxExtendedEndDate = '<?php  echo 	$obj_bank_reconciliation->getExtendedRecoEndDate($_SESSION['default_year_end_date'], +275);?>';
	 
	 var basic3DatePicker = { 
            dateFormat: "dd-mm-yy",
			showOn: "button", 
            buttonImage: "images/calendar.gif",
            buttonImageOnly: true,
			minDate: minGlobalCurrentYearStartDate,
			maxDate: maxExtendedEndDate,
			onSelect: function(id){
				isValidDate(this.id);
				isVoucherDateValid(this.id);
			},
			
        };
	 
	 
	 $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy",
			minDate: minGlobalCurrentYearStartDate,
			maxDate: maxGlobalCurrentYearEndDate,
			onClose: function (dateText, inst) {
                $(this).focus();
            }
        })});
		
		
		$(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics2").datepicker({ 
            dateFormat: "dd-mm-yy",
			showOn: "button", 
            buttonImage: "images/calendar.gif",
            buttonImageOnly: true,
			minDate: minGlobalCurrentYearStartDate,
			maxDate: 0,//maxExtendedEndDate,
			onSelect: function(id){
				isValidDate(this.id);
				isReconcileDateValid(this.id);
			},
			
        })});					
    </script>
    <script type="text/javascript" src="js/ajax.js"></script>
	<script language="javascript" type="text/javascript">
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
	
	function Enable(str,vDate)
	{		
		var arr1	= new Array();
		arr1		= str.split("-");
		
		if(arr1[0] == 'reconcile')
		{			
			if(document.getElementById('return'+arr1[1]).checked == true)
			{
				document.getElementById('return'+arr1[1]).checked = false;			
			}
		}
		else if(arr1[0] == 'return')
		{			
			if(document.getElementById('reconcile'+arr1[1]).checked == true)
			{				
				document.getElementById('reconcile'+arr1[1]).checked = false;
			}
		}
		
		if($('#cancel_date'+arr1[1]).val() == "")
		{
			$('#cancel_date'+arr1[1]).val(vDate);
		}
		
		if(document.getElementById('reconcile'+arr1[1]).checked == false && document.getElementById('return'+arr1[1]).checked == false)
		{
			$('#cancel_date'+arr1[1]).val("");
		}
	}
	
	function isVoucherDateValid(id){
		
		var idArr = id.split('_');
		var cnt = idArr[idArr.length-1];
		console.log('cnt',cnt, id);
		var recoDate = document.getElementById('cancel_date'+cnt).value;
		var voucherDate = document.getElementById('voucher_date_input_'+cnt).value;
		var chqDate = document.getElementById('cheque_date_input_'+cnt).value;		 
       
	    var d1 = voucherDate.substring(0, 2);
        var m1 = voucherDate.substring(3, 5);
        var y1 = voucherDate.substring(6, 10);
        var dateOne = new Date(y1, m1 - 1, d1);//Year, Month, Date
                
        var d2 = recoDate.substring(0, 2);
        var m2 = recoDate.substring(3, 5);
        var y2 = recoDate.substring(6, 10);
        var dateTwo = new Date(y2, m2 - 1, d2);//Year, Month, Date

		var d1 = chqDate.substring(0, 2);
        var m1 = chqDate.substring(3, 5);
        var y1 = chqDate.substring(6, 10);
        var dateThree = new Date(y1, m1 - 1, d1);//Year, Month, Date

		console.log('date one', dateOne, 'date two', dateTwo)
		if(dateTwo < dateOne && (id == 'voucher_date_input_'+cnt)){

			alert("Voucher Date must be less than Reconcile Date");
			document.getElementById(id).value = '';	
		}
		else if(id == 'voucher_date_input_'+cnt){

			$("#"+id).removeClass('error');
		}
	
		if(dateTwo < dateThree && (id == 'cheque_date_input_'+cnt)){
			alert("Cheque Date must be less than Reconcile Date");
			document.getElementById(id).value = '';	
		}
		else if(id == 'cheque_date_input_'+cnt){

			$("#"+id).removeClass('error');
		}		
	}

	function isReconcileDateValid(id, callApplyDate = true)
	{		
		var cnt = id.substring(11);		
		var voucherDate = document.getElementById('voucher_date'+cnt).innerHTML;
		var chqDate = document.getElementById('cheque_date'+cnt).innerHTML;
		var recoDate = document.getElementById(id).value;		 
       
	    var d1 = voucherDate.substring(0, 2);
        var m1 = voucherDate.substring(3, 5);
        var y1 = voucherDate.substring(6, 10);
        var dateOne = new Date(y1, m1 - 1, d1);//Year, Month, Date
                
        var d2 = recoDate.substring(0, 2);
        var m2 = recoDate.substring(3, 5);
        var y2 = recoDate.substring(6, 10);
        var dateTwo = new Date(y2, m2 - 1, d2);//Year, Month, Date
        	
		if( dateTwo < dateOne)
		{
			var userConfirmation = confirm("Reconcile Date must be greater than Voucher Date. Do you want to change the voucher date");	
			if(userConfirmation){

				document.getElementById('voucher_date'+cnt).innerHTML = '<input type="text" name="voucher_date_input'+cnt+'" id="voucher_date_input_'+cnt+'" class="error" value="'+voucherDate+'"  style="width:65px;" onchange="isValidDate(this.id);isVoucherDateValid(this.id);">';
				document.getElementById('cheque_date'+cnt).innerHTML = '<input type="text" name="cheque_date_input'+cnt+'" id="cheque_date_input_'+cnt+'" class="error" value="'+chqDate+'"  style="width:65px;"  onchange="isValidDate(this.id);isVoucherDateValid(this.id);">';
				$('#voucher_date_input_'+cnt).datepicker(basic3DatePicker);
				$('#cheque_date_input_'+cnt).datepicker(basic3DatePicker);
			}
			else{
				document.getElementById(id).value = '';	
			}
			
		}
		if(callApplyDate) {
			applyDate(cnt);
		}
	}
	function applyDate(id)
	{
		//alert (id);
		var i = 0;
		var type = "";
		if(document.getElementById('reconcile'+id).checked == true)
		{
			type = 'reconcile';
		}
		else if(document.getElementById('return'+id).checked == true)
		{
			type = 'return';
		}
		var checkboxId = new Array();
		var count = document.getElementById('count').value;
		if(type == 'reconcile')
		{			
			for(var j = parseInt(id);j < count; j++)
			{
				if(document.getElementById('reconcile'+j) != null && document.getElementById('reconcile'+j).checked == true)
				{
					checkboxId[i] = j;
					i = i + 1;
				}
			}
		}
		else if(type == 'return')
		{			
			for(j = parseInt(id); j < count ; j++)
			{
				if(document.getElementById('return'+j) != null && document.getElementById('return'+j).checked == true)
				{
					checkboxId[i] = j;
					i = i + 1;
				}
			}
		}
		//alert (checkboxId);	
		var name = "cancel_date";
		var date =  document.getElementById(name+id).value;
		//alert (date);
		if(confirm("Would you like to apply same date for all entries.") == true)
		{
			for (j = 0;j < checkboxId.length; j++)
			{
				if(document.getElementById(name+checkboxId[j]).value == "")
				{
					document.getElementById(name+checkboxId[j]).value = date;
					isReconcileDateValid(name+checkboxId[j], false);

				}
			}
		}
		else
		{
		}
	}
	function checkPenalty()
	{
		if(document.getElementById("bank_penalty").value == "0.00")
		{
			if(confirm("Bank Penalty Amount is 0.00. If you want to continue click OK otherwise CANCEL to abort transaction") == true)
			{
				return (ValidateForm(minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate));
			}
			else
			{
				return false;
			}
		}
	}
	function alertUser()
	{
		if(confirm("Would you like to update the value to database.")==true)
		{
			document.getElementById("userConfirmation").value = "0";
		}
		else
		{
			document.getElementById("userConfirmation").value = "1";
		}
	}
	</script>
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

// Lock buttons freez year 
$btnDisable = "";
if($_SESSION['is_year_freeze'] <> 0)
{
	$btnDisable = "disabled";
}
else
{
	$btnDisable = "";
}
?>
<br>
<div class="panel panel-info" id="panel" style="display:block">
    <div class="panel-heading" id="pageheader">Bank Reconciliation
    <?php if(($_SESSION['login_id'] == 4 || $_SESSION['login_id'] == 2216 || $_SESSION['login_id'] == 2727 || $_SESSION['login_id'] == 7369 || $_SESSION['login_id'] == 2735 || $_SESSION['login_id'] == 2931 || $_SESSION['login_id'] == 8671) && ($_SESSION['is_year_freeze'] == 0 )){?>
    <div class="btn btn-primary pull-left" style="color:white"><i class="fa fa-check" style="color:white;padding:margin-top: 3px;margin-right: 6px;"></i><a href="import_bank_statement_preview.php?LedgerID=<?php echo $_REQUEST['ledgerID'];?>" target="_blank" name="auto_reconcile" id="auto_reconcile" style="text-decoration: none;color: white;">Auto Reconcilation</a></div>
    <div class="btn btn-primary pull-right" style="color:white"><i class="fa fa-check" style="color:white;padding:margin-top: 3px;margin-right: 6px;"></i><a href="Actual_Bank_Statement.php?LedgerID=<?php echo $_REQUEST['ledgerID'];?>" target="_blank" name="auto_reconcile" id="auto_reconcile" style="text-decoration: none;color: white;">Actual Bank Statement</a></div>
    <?php }?>
    </div>
<br>
<center>
<form name="form_filter" id="form_filter" method="POST" action="bank_reconciliation.php?ledgerID=<?php echo $_REQUEST["ledgerID"]?>" onSubmit="return ValidateForm(minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate);">
<table width="90%" style="border:1px solid black; background-color:transparent; border-collapse:collapse;">
<tr> <td colspan="4"><br/> </td></tr>
<tr>
	<td style=" text-align:left;width:30%;"> &nbsp; Date &nbsp; 
    	<select name="dateType" id="dateType" style="width:150px;" tabindex="1" > <!--onChange="OnFormSubmit();" -->
        	<option value="0">Voucher Date </option>            
            <option value="1">Cheque Date </option>
            <option value="2">Clear Date </option>
        </select>
    </td>
    <td style="width:30%">
 &nbsp; From  <input type="text" name="From" id="From" style="width:75px;"  class="basics" tabindex="2" />   To   <input type="text" name="To" id="To" style="width:75px;" class="basics" tabindex="3" />
 	</td>      
    <td style="text-align:right;width:30%">Voucher Type &nbsp; 
    	<select name="voucherType" id="voucherType" style="width:120px;" tabindex="4">  <!--onChange="OnFormSubmit();" -->
        	<option value="">ALL </option>            
        	<?php echo $combo_type = $obj_bank_reconciliation->combobox("SELECT `id`,`type` FROM `vouchertype`",0); ?>        	
        </select>
    </td> 
    <td  style="text-align:right; width:10%"> </td>       
</tr>
<tr> <td colspan="4"><br/> </td></tr>
<tr>       
    <td style="text-align:left;width:30%"> &nbsp; Status 
    	<select name="status" id="status" style="width:150px;" tabindex="5"> <!--onChange="OnFormSubmit();" -->
        	<option value="0">ALL </option>            
            <option value="1">Cleared </option>
            <option value="2">Unclear </option>
            <option value="3">Rejected </option>
        </select>
    </td>
    <td style="width:30%">
	&nbsp; Ledger
    <?php $len = sizeof($details); ?>
    <input type="hidden" name="count" id="count" value="<?php echo $len;?>"/>
    <select name="ledger" id="ledger" style="width:150px;" tabindex="6"> <!--onChange="OnFormSubmit();" -->
    	<option value="">ALL </option>            
    	<?php
		for($i = 0 ; $i < sizeof($details); $i++)
			{ ?>
    		<option value="<?php echo $details[$i]['ledger_name']?>"> <?php echo $details[$i]['ledger_name']?> </option>                  
    	<?php } ?>
    </select>
    </td>    
    <td style="text-align:right;width:30%">Cheque Number/Amount
   		<input type="text" name="chequeNo" id="chequeNo" style="text-align:right; width:120px;" tabindex="7" />
    </td>  
    <td style="text-align:center; width:10%" style="vertical-align:central"><input type="submit" name="Go" id="Go" value="Go" style="width:50px;" tabindex="8"/> 	</td>     
</tr>
<tr> <td colspan="4"><br/> </td></tr>
</table>
</form>
<script>
function OnFormSubmit()
{	
//alert("test");	
		document.form_filter.submit();
} 
</script>
<script>
document.getElementById("ledger").value = "<?php echo $_POST['ledger'] ?>";
document.getElementById("voucherType").value = "<?php echo $_POST['voucherType'] ?>";
document.getElementById("status").value = "<?php echo $_POST['status'] ?>";
document.getElementById("chequeNo").value = "<?php echo $_POST['chequeNo']?>";
document.getElementById("From").value = "<?php echo $_POST['From']?>";
document.getElementById("To").value = "<?php echo $_POST['To']?>";
document.getElementById("dateType").value = "<?php echo $_POST['dateType'];?>";
</script>

<form name="bank_reconciliation" id="bank_reconciliation" method="post" action="process/bank_reconciliation.process.php">
<input type="hidden" name="ledgerID" id="ledgerID" value = '<?php echo $_REQUEST["ledgerID"] ?>'/> 
<table width="100%">
<tr> <td> <br /><br /> </td></tr>
<tr>
<td>&nbsp;</td>
	<th  style="width:20%;text-align:center;">Society Penalty Amount : &nbsp;</th>
    <td style="width:20%;text-align:left;margin-top: 1px;float: left;"><?php echo $society_penalty ?> </td>
    <input type="hidden" name="society_penalty" value='<?php echo $society_penalty ?>'  />
    <td style="width:15%;"></td>
    <th style="width:20%;text-align:center;">Bank Penalty Amount : &nbsp;</th>
    <input type="hidden" name="userConfirmation" id="userConfirmation" />
     <td style="width:20%;text-align:left;margin-top: 1px;float: left;"></td>
    <td><input type="text" name="bank_penalty" id="bank_penalty" style="text-align:center; margin-top: -7px;" value="<?php echo $details[1]['bank_penalty_amt'];?>" onChange="alertUser()"></td>
    <td  style="text-align:center;"><input type="submit" name="submit" id="submit" value="submit" style="margin-top: -7px;" class="btn btn-primary" onClick="return checkPenalty();"  <?php echo $btnDisable?>></td> 
</tr>
<tr> <td> <br /> </td></tr>
<?php 

if($Dates_arr['IsCurrentYear'] == false && !isset($_POST['From']) && !isset($_POST['To'])){ ?>
	<tr><td colspan="4" style="padding-left: 4%;"><b>NOTE : </b><span style="color:blue">Please select above options to get data as you required</span></td></tr>
<?php }?>

	
</table>
<br />

<style>

.error{
	color:red;
	border: 1px solid red;
}

td{vertical-align:middle}
.tooltip2 {
    position: relative;
    border-bottom: 1px dotted black;
	opacity: inherit;
}

.tooltip2 .tooltiptext {
    visibility: hidden;
    width: 155px;
    background-color: #fff;
    color: black;
    text-align: center;
    border-radius: 6px;
    padding: 5px 0;

    /* Position the tooltip */
    position: absolute;
    z-index: 1;
	border: 1px solid black;
    left: 10px;
    top: 26px;
}

.tooltip2:hover .tooltiptext {
    visibility: visible;
}
</style>
<style type="text/css">
 
  /*table.cruises { 
    font-family: verdana, arial, helvetica, sans-serif;
    font-size: 11px;
    cellspacing: 0; 
    border-collapse: collapse; 
    width: 535px;    
    }*/
  table.cruises td { 
    border-left: 1px solid #999; 
    border-top: 1px solid #999;  
    padding: 2px 4px;
    }
  table.cruises tr:first-child td {
    border-top: none;
  }
  table.cruises th { 
    border-left: 1px solid #999; 
    padding: 2px 4px;
    background: #6b6164;
    color: white;
    font-variant: small-caps;
    }
  table.cruises td { background: #eee; overflow: hidden; }
  
  div.scrollableContainer { 
    position: relative; 
    padding-top: 6em;
	width:100%;
    margin: 0px; 
	border: 1px solid #999;   
   }
  div.scrollingArea { 
    height: 600px; 
    overflow: auto; 
    }

  table.scrollable thead tr {
    left: -1px; top: 0;
    position: absolute;
    }

 

</style>
<!-- <table width="90%"> -->
<div class="scrollableContainer">
 <div class="scrollingArea">
<table style="text-align:center; width:100%;" class="table table-bordered table-hover table-striped " id="reconciliation-table">
<?php if(isset($_POST['ShowData']))
	{
?>
		<tr height='30'><td colspan='12' align='center'><font color='red' size='-1'><b id='error' style='display:block;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
	}?>
<thead style="left: -1px; top: 0;
    position: absolute;">    
<tr>
	<th style="width:8%;text-align:center;border:1px solid black;">Voucher Date</th>
    <th style="width:8%;text-align:center;border:1px solid black;">Cheque Date</th>
    <th style="width:20%;text-align:center;border:1px solid black;">Particulars</th>
    <th style="width:8%;text-align:center;border:1px solid black;">Voucher Type</th>
    <th style="width:26%;text-align:center;border:1px solid black;">Ref.</th>
    <th style="width:10%;text-align:center;border:1px solid black;">Withdrawals</th>
    <th style="width:10%;text-align:center;border:1px solid black;">Deposits</th>  
    <th style="width:3%;text-align:center;border:1px solid black;">Reconcile</th> 
    <th style="width:8%;text-align:center;border:1px solid black;">Note</th>  
    <th style="width:3%;text-align:center;border:1px solid black;">Return</th>     
    <th style="width:8%;text-align:center;border:1px solid black;">Clear/Return Date <?php echo "<font color='#FF0000'>(dd-mm-yyyy)";?></th>    
    <th style="width:8%;text-align:center;border:1px solid black;">Undo</th>    
</tr>
</thead>

<!--<tr> <td colspan="10"><br/> </td></tr>-->
<tbody>
	<tr> 
    <td colspan="12">        	  	             
        <input type="hidden" name="ledgerID" id="ledgerID" value = '<?php echo $_REQUEST["ledgerID"] ?>'  />
        <input type="hidden" name="ledger" id="ledger" value = '<?php echo $_POST['ledger'] ?>'  />
        <input type="hidden" name="status" id="status" value = '<?php echo $_POST['status'] ?>'  />
        <input type="hidden" name="voucher" id="voucher" value = '<?php echo $_POST['voucherType'] ?>'  />
        <input type="hidden" name="dateType" id="dateType" value = '<?php echo $_POST['dateType']?>'  />
		<input type="hidden" name="chequeNo" id="chequeNo" value = '<?php echo $_POST['chequeNo']?>'  />
        <input type="hidden" name="From" id="From" value = '<?php echo $_POST['From']?>'  />
        <input type="hidden" name="To" id="To" value = '<?php echo $_POST['To']?>'  />
    </td>
</tr>
	<?php		
		$ledgerName = "";
		//var_dump($details);
		for($i = 0; $i < sizeof($details); $i++)
		{						
			$receivedAmount = $details[$i]['ReceivedAmount'];
			$isOpeningBalance = $details[$i]['Is_Opening_Balance'];
			if($isOpeningBalance == 1)
			{				
				$ledgerName = "Opening Balance";
				$chequeDate = $details[$i]['Date'];
				$voucherType = "";
				$chequeNumber = "";
				$paidAmount = "";
			}
			else
			{
				$ledgerName = "";
				$chkDetailID = $details[$i]['ChkDetailID'];
				$LedgerUrl = $details[$i]['LedgerUrl'];
				//echo "<br>Ledger : ".$LedgerUrl;
				$AmountUrl = $details[$i]['AmountUrl'];
				$voucherID = $details[$i]['VoucherID'];
				$voucherTypeID = $details[$i]['VoucherTypeID'];			
				$Type = $obj_view_bank_statement->getVoucherType($voucherTypeID);
				$voucherType = $Type[0]['type'];
				$paidAmount = $details[$i]['PaidAmount'];
				if($paidAmount > 0)
				{	
					$receivedAmount = "";									
				}
				else
				{
					$paidAmount = "";										
				}
							
				$chequeDate = $details[$i]['Date'];
				if($details[$i]['DepositGrp']== -2)
				{
					$chequeNumber ="NEFT[TRXN:".$details[$i]['ChequeNumber']."]";
				}
				else
				{
					$chequeNumber = $details[$i]['ChequeNumber'];
				}
				//$chequeNumber = $details[$i]['ChequeNumber'];
				$comment = $details[$i]['Comment'];
			}
	?>
   <tr>
   <!--	<input type="hidden" name="chequeDetailID<?php echo $i ?>" id="chequeDetailID<?php //echo $i ?>" value = '<?php //echo $chkDetailID ; ?>'/> -->
	<td id="voucher_date<?php echo $i; ?>" style="width:12%;text-align:center;"><?php if($chequeDate <> 0){ echo getDisplayFormatDate($chequeDate); } ?></td>    
    <td id="cheque_date<?php echo $i; ?>" style="width:12%;text-align:center;"><?php if($details[$i]['Cheque Date'] <> 0)
	{echo getDisplayFormatDate($details[$i]['Cheque Date']) ;} ?> </td>  
    <td style="width:15%;text-align:center;" class="tooltip2" ><?php if($isOpeningBalance <> 1){?><a onClick="window.open('<?php echo $LedgerUrl; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes'); "><?php } 
	if($ledgerName <> "")
	{
		echo($ledgerName);
	}
	else
	{
		
		$MenberDetails = $obj_bank_reconciliation->getMemberName($details[$i]['id']);

		 echo $details[$i]['ledger_name'] . "(" . $details[$i]['id'] . ")";?>
         <?php
		 if($MenberDetails <> '')
		 {
         ?><span class="tooltiptext"> <?php echo $MenberDetails[0]['owner_name'] ?></span> 
         <?php 
		 }?><br>
		<?php
		if($comment <> "-")
		{ 		
		echo  "[".$comment. "]"; 
		}
	}
	?></a></td>	           
   <!-- <input type="hidden" name="cheque<?php //echo $i ?>" id="cheque<?php //echo $i ?>" value = '<?php //echo $chequeNumber  ?>'/>    -->
    <td style="width:8%;text-align:center;"><?php echo $voucherType ?></td>
    <td style="width:6%;text-align:center;"><?php echo $chequeNumber; ?></td>
    <td style="width:10%;text-align:center;"><?php if($isOpeningBalance <> 1){?><a href="<?php echo $AmountUrl;?>" target="_blank"><?php } echo number_format($paidAmount, 2); ?></a></td>
    <td style="width:10%;text-align:center;"><?php if($isOpeningBalance <> 1){?><a href="<?php echo $AmountUrl;?>" target="_blank"><?php } echo number_format($receivedAmount, 2);  ?></a></td>  
    <!--<input type="hidden" name="amount<?php //echo $i ?>" id="amount<?php //echo $i ?>" value = '<?php //echo $details[$i]['ReceivedAmount'] ?>'/>  -->
    
    <?php
		$status = $obj_bank_reconciliation->getReconcileStatus($details[$i]['ID']); 
		if( $status[0]['ReconcileStatus'] != 1)
			{?>            
    <td style="width:5%;text-align:center;"> <input type="checkbox" name="reconcile<?php echo $i ?>" id="reconcile<?php echo $i ?>" <?php if($reco_date_same_as_voucher == 1){?>onClick="Enable('reconcile-<?php echo $i;?>','<?php echo getDisplayFormatDate($chequeDate);?>')"<?php }?> value="1"/> </td>        	        
    
    <td style="width:8%;text-align:center;"> <input type="text" name="note<?php echo $i ?>" id="note" style="width:100px;" /> </td>  
      
    <td style="width:5%;text-align:center;" > <input type="checkbox" name="return<?php echo $i ?>" id="return<?php echo $i ?>" value="1" onClick=<?php if($reco_date_same_as_voucher == 1){ ?>"Enable('return-<?php echo $i;?>','<?php echo getDisplayFormatDate($chequeDate);?>')";<?php }?> <?php if($receivedAmount == "") { ?> disabled <?php } ?> /> </td>   		            
    
    <td><input type="text" name="cancel_date<?php echo $i ?>" id="cancel_date<?php echo $i ?>" style="width:65px;"  class="basics2"   onChange="isValidDate(this.id);isReconcileDateValid(this.id);" />
    	<br>
    	<br>
    	<input style="width:65px;"  type="hidden" name="act_bank_statement_id<?php echo $i ?>" id="act_bank_statement_id<?php echo $i ?>" /> 
    	<button type="button" style="height: 20px; width: 65px; border-radius: 5px;" id="reco_btn<?php echo $i ?>" owner_name="<?php echo $MenberDetails[0]['owner_name'] ?>" ledger_name="<?php echo $details[$i]['ledger_name'] ?>" onclick="OpenBankStatement('<?php echo $_REQUEST["ledgerID"] ?>','<?php echo getDisplayFormatDate($chequeDate); ?>','<?php echo $i ?>','<?php echo $chequeNumber; ?>','<?php echo number_format($paidAmount, 2) ?>','<?php echo number_format($receivedAmount, 2); ?>')" class="btn-primary">R</button>
    </td>
    <td> </td>
    <?php }else{
				 if($status[0]['Reconcile'] == 1)
		{ ?>
        <td colspan="3" style="text-align:center; color:#090;"><b> APPROVED </b> </td>      
        <td> <?php echo getDisplayFormatDate($details[$i]['ReconcileDate']); ?> </td>
        <td align="center"> <input type="checkbox" name="undo<?php echo $i ?>" id="undo<?php echo $i ?>" value="1" /></td>
        <?php }
			else
			{ ?>
            <td colspan="3" style="text-align:center; color:#FF0000;"> <b> REJECTED</b> </td>
            <td> <?php echo getDisplayFormatDate($details[$i]['ReconcileDate']); ?> </td>
            <td align="center"> <input type="checkbox" name="undo<?php echo $i ?>" id="undo<?php echo $i ?>" value="1" /></td>
            <?php  }} ?>
</tr>
<?php  } ?>
</tbody>
<!--<tr> <td> <br /> </td></tr>
<tr> <td> <br /> </td></tr>-->
<!-- <tr>
    <td>        
        <input type="hidden" name="myValue" value='<?php //echo $i ?>'  />
    </td>
</tr> -->
<tr> 
    <td colspan="12">    
    	<input type="hidden" name="myValue" value='<?php echo $i ?>'  />	     	             
        <!--<input type="hidden" name="ledgerID" id="ledgerID" value = '<?php //echo $_REQUEST["ledgerID"] ?>'  />
        <input type="hidden" name="ledger" id="ledger" value = '<?php //echo $_POST['ledger'] ?>'  />
        <input type="hidden" name="status" id="status" value = '<?php //echo $_POST['status'] ?>'  />
        <input type="hidden" name="voucher" id="voucher" value = '<?php //echo $_POST['voucherType'] ?>'  />
        <input type="hidden" name="dateType" id="dateType" value = '<?php //echo $_POST['dateType']?>'  />
		<input type="hidden" name="chequeNo" id="chequeNo" value = '<?php //echo $_POST['chequeNo']?>'  />
        <input type="hidden" name="From" id="From" value = '<?php //echo $_POST['From']?>'  />
        <input type="hidden" name="To" id="To" value = '<?php //echo $_POST['To']?>'  />-->
    </td>
</tr>
<tr>	
	<!--<td colspan="12" style="text-align:center;"><input type="submit" name="submit" id="submit" value="submit" style="background-color:#E8E8E8; width:80px; height:30px;"></td>  -->  
</tr>
</table>
</div>
</div>
</form>


</center>

</div>	
 <script type="text/javascript" src="js/bootstrap-modalmanager.js"></script>
<script type="text/javascript" src="js/bootstrap-modal.js"></script>
<div class="container">
  <!-- Modal -->
  <div class="modal fade" id="BankStatementModal" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" >
          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <center><h4 class="modal-title" style="color: #43729F;"><b>Actucal Bank Statement</b></h4></center>
          <table  id="table" class="display" width="100%" border="1" >
          	<tr>
          	<th style="width:20%;text-align:center;">Voucher Date</th>
          	<th style="width:20%;text-align:center;">Owner Name</th>
          	<th style="width:20%;text-align:center;">Ref.</th>
          	<th style="width:20%;text-align:center;"> Withdrawals</th>
          	<th style="width:20%;text-align:center;">Deposits</th>
          	</tr>
          	<tr>
          		<td style="width:20%;text-align:center;" id="voucher_date"></td>
          		<td style="width:20%;text-align:center;" id="owner_name"></td>
          		<td style="width:20%;text-align:center;" id="chequeNumber"></td>
          		<td style="width:20%;text-align:center;" id="withdrawal"></td>
          		<td style="width:20%;text-align:center;" id="deposit"></td>
          	</tr>
          </table>
        </div>
        <div class="modal-body" id="modal_body">
          
        </div>
        <div class="modal-footer">
        	<button type="button" class="btn btn-primary" name="act_bank_statement_modal" id="act_bank_statement_modal" data-dismiss="modal">Submit</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>	
<?php
if(IsReadonlyPage() == true)
{?>
<script>
	$("#reconciliation-table").css( 'pointer-events', 'none' );
</script>
<?php }?>
<?php include_once "includes/foot.php"; ?>
 <script type="text/javascript">
 	function OpenBankStatement(ledgerID,voucher_date,row_id,chequeNumber,withdrawal,deposit)
 	{
 	   var owner_name=$('#reco_btn'+row_id).attr('owner_name');
 	   var ledgerName=$('#reco_btn'+row_id).attr('ledger_name');
       $("#voucher_date").text(voucher_date);
       $("#chequeNumber").text(chequeNumber);
       $("#withdrawal").text(withdrawal);
       $("#deposit").text(deposit);
       if(owner_name!='')
       {
       $("#owner_name").text(owner_name);
       }
       else
       {
       $("#owner_name").text(ledgerName);

       }

      	$.ajax({
			url : "ajax/ajaxActualBankStatement.php",
			type : "POST",
			data : {"ledgerID" :ledgerID,'voucher_date':voucher_date ,"row_id":row_id,"chequeNumber":chequeNumber},
			success : function(data)
			{
			  $('#modal_body').html(data);
             $("#BankStatementModal").modal('show');

			},
				
			fail: function()
			{
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});	


 	}
 </script>
 <script type="text/javascript">
 	function redirectTransaction(act_bank_statement_id)
{
	$.ajax({
			url : "ajax/ajaxBankDetails.php",
			type : "POST",
			data: { "act_bank_statement_id":act_bank_statement_id,"method":"getReconcileDetails"} ,
			success : function(data)
			{	
				var a		= data.trim();
				var arr1	= new Array();
				var arr2	= new Array();
				arr1		= a.split("@@@");
				arr2 = JSON.parse("["+arr1[1]+"]");
				arr2 = arr2[0];
				if(arr2.length > 1)
				{
					var table = "<table style='width:100%'>";
					for(var i = 0; i < arr2.length; i++)
					{
						table += "<tr><td>"+arr2[i]['LedgerName']+"</td><td><a href="+arr2[i]['Url']+" target='_blank'>"+arr2[i]['Amount']+"</a></td></tr>";
					}
					table += "</table>"; 
					$('.formSubmitResponse').html(table);
					$('#myModal').modal();
				}
				else
				{
					window.open(arr2[0]['Url'],'_blank');
				}
			}
	});
}	
 </script>
<script type="text/javascript">
	 $('#act_bank_statement_modal').click(function(){
      var act_bank_statement_id = $("input[name='act_bank_statement']:checked").val();
      var act_bank_statement_date = $("input[name='act_bank_statement']:checked").attr('act_bank_statement_date');
      var row_id = $("input[name='act_bank_statement']:checked").attr('row_id');
      
       $("#cancel_date"+row_id).val(act_bank_statement_date);
       $("#act_bank_statement_id"+row_id).val(act_bank_statement_id);
       $("#return"+row_id).attr('checked',false);
       $("#reconcile"+row_id).attr('checked',true);
	   isReconcileDateValid("cancel_date"+row_id);
  
     });
</script>

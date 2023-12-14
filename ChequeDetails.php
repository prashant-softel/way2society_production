<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Cheque Details</title>
</head>



<?php 

include_once("includes/head_s.php");
include_once ("check_default.php");

include_once("classes/dbconst.class.php");
include_once("classes/ChequeDetails.class.php");
$obj_ChequeDetails = new ChequeDetails($m_dbConn);

//include_once("classes/genbill.class.php");
//$objGenBill = new genbill($m_dbConn);

include_once("classes/include/fetch_data.php");
$objFetchDetails = new FetchData($m_dbConn);
//include_once("classes/wing.class.php");
//$obj_wing = new wing($m_dbConn);
//include_once("classes/unit.class.php");
//$obj_wing = new unit($m_dbConn);
include_once("classes/utility.class.php");
$objUtility = new utility($m_dbConn);
$memberIDS = $objUtility->getMemberIDs($_SESSION['default_year_end_date']);	
$VoucherDate = '';
$status = $objUtility->getIsDateInRange( date("Y-m-d") , getDBFormatDate($_SESSION['default_year_start_date']) , getDBFormatDate($_SESSION['default_year_end_date'])); 
$CurrentYearBeginingDate = $objUtility->getCurrentYearBeginingDate($_SESSION['default_year']);


if($status)
{
	$VoucherDate = 	date("d-m-Y");	
}
else
{
	$VoucherDate = 	getDisplayFormatDate($_SESSION['default_year_end_date']);		
}
$Latest_Bill_Start_Date = $objFetchDetails->getlatestbillstartdate();
$newDate = date('Y-m-d', strtotime(getDBFormatDate($Latest_Bill_Start_Date) . ' -23 day'));
$Latest_Bill_Start_Date1 = getDisplayFormatDate($newDate);
$IsSameCntApply = $objUtility->IsSameCounterApply();
/*if($_REQUEST['depositid'] != -3)
{
	//** Here we check the  whether we have to use same counter or different for all banks
	if($IsSameCntApply == 1)
	{
		$Counter = $objUtility->GetCounter(VOUCHER_RECEIPT, 0);	
	}
	else
	{
		$Counter = $objUtility->GetCounter(VOUCHER_RECEIPT, $_REQUEST['bankid']);
	}
		
}
else
{
	// here fetching the counter for cash receipts
	$Counter = $objUtility->GetCounter(VOUCHER_CASHRECEIVE, $_REQUEST['bankid']);	
}*/

//** Here we check the  whether we have to use same counter or different for all banks
	if($IsSameCntApply == 1)
	{
		$Counter = $objUtility->GetCounter(VOUCHER_RECEIPT, 0);	
	}
	else
	{
		$Counter = $objUtility->GetCounter(VOUCHER_RECEIPT, $_REQUEST['bankid']);
	}


$depositid = "";
//echo $_REQUEST["select"];
//echo $_REQUEST["depositid"];
if($_REQUEST["depositid"] == "")
{
	$depositid = $_REQUEST["select"];
}
else
{
	$depositid = $_REQUEST["depositid"];
}
//echo $_REQUEST["bankname"];
//echo $depositid;
$_SESSION['ssid'] = $_REQUEST['ssid'];
$_SESSION['wwid'] = $_REQUEST['wwid'];
?>
 

<html>
<head>

	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
    <script type="text/javascript" src="js/ajax_new.js"></script>
	<script type="text/javascript" src="js/jsChequeDetails_20190705.js?rndstr=<%= getRandomStr() %>"></script>
    <script type="text/javascript" src="js/populateData.js"></script>
    <script type="text/javascript" src="js/validate.js"></script>
    <!--common js use to store common js function for the counter-->
    <script type="text/javascript" src="js/jsCommon_20190326.js"></script>
    <!--At below we store the all exiting counter so counter won't repeate-->
    <script>SetAry_ExitingExCounter(<?php echo json_encode($Counter[0]['ExitingCounter']);?>)</script>
    <script type="text/javascript">
    function sec_depo(dpval,counter)
	{
		var m=dpval;
		//alert(m);
		 $.ajax({
     		 		type: "POST",
     		 		url: "ajax/ajaxChequeDetails.php",
     			 	data: ({'LID' :m ,"method" : 'FetchSecDeposit'}),
      				success: function(data) 
					{
				 		var a		= data.trim();
						var arr2	= new Array();
						arr2		= a.split("@@@");
						//alert(arr2[1]);
						document.getElementById('sec_depfg'+counter).value=arr2[1];
						var k=document.getElementById('sec_depfg'+counter).value;
						if(k==1)
						{
							document.getElementById('sec_dep_lab'+counter).style.display="block";
							document.getElementById('sec_dep'+counter).style.display="block";
						}else
						{
							document.getElementById('sec_dep_lab'+counter).style.display="none";
							document.getElementById('sec_dep'+counter).style.display="none";
						}
						
            		}
       		});
		
		
	}
	
    </script>
    
	<script language="javascript" type="application/javascript">
	var iCounter = 1;
	var iTableCounter = 1; 
	var TDSReceivable = '<?php echo $_SESSION['default_tds_receivable']?>'; // Setting TDSReceivable to know whether TDS is set in default or not
	function PaidToChanged(PaidTo,mCounter)
	{
		var iMembersOnly = document.getElementById("MembersOnly").checked;
		var PaidToVal = document.getElementById(PaidTo.id).value;
		if((iMembersOnly == 1))
		{
			document.getElementById(PaidTo.id).innerHTML = "<?php echo $obj_ChequeDetails->comboboxEx("select led.id as id,concat_ws(' - ',led.ledger_name,mem.owner_name) as ledger_name from ledger as led JOIN unit as unittable on led.id=unittable.unit_id JOIN member_main as mem  on mem.unit=unittable.unit_id where receipt='1' and led.society_id=".$_SESSION['society_id']." and led.categoryid=".DUE_FROM_MEMBERS." and  mem.member_id IN (".$memberIDS.") ORDER BY unittable.sort_order ASC"); ?>";
		 //alert('done');
		 toggleSupplementaryCheckbox(mCounter);
		}
		else
		{
			document.getElementById(PaidTo.id).innerHTML = "<?php echo $obj_ChequeDetails->comboboxEx("select id,ledger_name from ledger where receipt='1' and categoryid !=".DUE_FROM_MEMBERS." and society_id=".$_SESSION['society_id']."  ORDER BY ledger_name ASC"); ?>";
			toggleSupplementaryCheckbox(mCounter);
		}
		
	}
	function myTestFunc(iCounter)
	{
		var m=document.getElementById('DrpDwnBillType'+iCounter).value;
		//alert(m);
		if(m==0||m==1||m==2)
		{
			document.getElementById('sec_dep_lab'+ iCounter).style.display="none";
			document.getElementById('sec_dep' + iCounter).style.display="none";
		}
		if(m==3)
		{
			document.getElementById('sec_dep_lab'+ iCounter).style.display="block";
			document.getElementById('sec_dep' + iCounter).style.display="block";
		}
	}
	function AddNewRow()
	{	
		var currentCounter =  <?php echo $Counter[0]['CurrentCounter']?>;
		var VoucherCounter = '';
		var tmp = 0;
		
		for(var iCnt = 1;iCnt <= 5;iCnt++)
		{
			if(tmp != 0)
			{
				VoucherRowCount = tmp+1;	
			}
			else
			{
				//This condition true for first row
				VoucherRowCount = parseInt(currentCounter);	
			}
						
			VoucherCounter = getExCounter(VoucherRowCount);
			tmp = VoucherCounter;
						
			<?php $sql = "select id,ledger_name from ledger where receipt='1' and society_id=".$_SESSION['society_id']." ORDER BY ledger_name ASC";  ?>
				
			var sContent = "<tr id='ChqRow" + iCounter+"'><td><input type='text' name='VoucherCounter" + iCounter+"' id='VoucherCounter" + iCounter+"' style='width:80px;' value="+VoucherCounter+" style='width:80px;' /></td>";
						
			sContent  += "<td id= 'PaidByBank"+iCounter+"'><select id='PaidBy" + iCounter+"' name='PaidBy" + iCounter+"' style='width:150px;' onfocus='PaidToChanged(this," + iCounter +")' onblur='PopulatePayerBank(this.value," + iCounter +")' onChange='sec_depo(this.value,"+ iCounter+")'> '<?php echo $PaidBy = $obj_ChequeDetails->comboboxEx($sql);?>'</select></td>";
	
			//***Drop down list for bill type
			sContent += "<td><select  name='DrpDwnBillType"+iCounter+"' id='DrpDwnBillType"+iCounter+"' onchange='myTestFunc(" + iCounter +")' style='width:120px;'><option value='0'>Maintenance bill</option><option value='1'>Supplementry bill</option><option value='2'>Invoice bill</option></select></td>";
			//sContent += "<td><input type='text' name='sec_dep' id='sec_dep'></td>"
			<?php if($_REQUEST['depositid'] != -3)
			{?>	
			sContent += "<td><input type='text' id='ChequeNumber" + iCounter+"' name='ChequeNumber" + iCounter+"' onBlur='extractNumber(this,0,false);' onKeyUp='extractNumber(this,0,false);' onKeyPress='return blockNonNumbers(this, event, false, false)' style='width:80px;'></td>";
			<?php } ?>
			
			
			sContent += "<td><input type='text' id='ChequeDate" + iCounter+"' name='ChequeDate" + iCounter+"' style='width:80px;'></td>";
			
			sContent += "<td><input type='text' id='Amount" + iCounter+"' name='Amount" + iCounter+"' onBlur='extractNumber(this,2,true);' onKeyUp='extractNumber(this,2,true);' onKeyPress='return blockNonNumbers(this, event, true, true)' style='width:90px;'></td>";
			
			<?php if($_SESSION['default_tds_receivable'] <> 0){?>
			sContent += "<td><input type='text' id='TDS_Amount" + iCounter+"' name='TDS_Amount" + iCounter+"' onBlur='extractNumber(this,2,true);' onKeyUp='extractNumber(this,2,true);' onKeyPress='return blockNonNumbers(this, event, true, true)' style='width:90px;'></td>";
			<?php }?>
			
			<?php if($_REQUEST['depositid'] != -3)
			{?>			
			sContent += "<td><input type='text' id='PayerBank" + iCounter+"' name='PayerBank" + iCounter+"' style='width:120px;'></td>";
			
			sContent += "<td><input type='text' id='PayerChequeBranch" + iCounter+"' name='PayerChequeBranch" + iCounter+"' style='width:120px;'></td>";
			<?php } ?>
			sContent += "<td><input type='text' id='Comments" + iCounter+"' name='Comments" + iCounter+"' style='width:160px;'></td></tr>";
			sContent += "<tr><td><br/></td></tr><tr><td><input type='hidden' name='sec_depfg" + iCounter+"' id='sec_depfg" + iCounter+"' style='width:25px'></td><td><label id='sec_dep_lab" + iCounter+"' name='sec_dep_lab" + iCounter+"' style='display:none'>Select Unit:</label></td><td><select id='sec_dep" + iCounter+"' name='sec_dep" + iCounter+"' style='width:100px;display:none' onblur='PopulatePayerBank(this.value," + iCounter +")'> <?php echo $obj_ChequeDetails->comboboxEx("select led.id as id,concat_ws(' - ',led.ledger_name,mem.owner_name) as ledger_name from ledger as led JOIN unit as unittable on led.id=unittable.unit_id JOIN member_main as mem  on mem.unit=unittable.unit_id where receipt='1' and led.society_id=".$_SESSION['society_id']." and led.categoryid=".DUE_FROM_MEMBERS." and  mem.member_id IN (".$memberIDS.") ORDER BY unittable.sort_order ASC"); ?></select></td></tr><tr><td><br/></td>"
			
			//Storing current counter in two field because if user change the current counter we show the confirmation alert  
			sContent += "<td><input type='hidden' name='Current_Counter" + iCounter+"' id='Current_Counter" + iCounter+"' value="+VoucherCounter+" style='width:80px;' /></td>";
			
			sContent += "<td><input type='hidden' name='OnPageLoadTimeVoucherNumber" + iCounter+"' id='OnPageLoadTimeVoucherNumber" + iCounter+"' value="+VoucherCounter+" style='width:80px;' /></td>";
			
			sContent +=	"<tr><td colspan='3'><label id='LedgerBalance" + iCounter+"' style='color:blue'></label></td></tr>";
		
			sContent += "<tr id='ChqRowLabel" + iCounter+"'><td colspan='3'><p id='label"+ iCounter +"' name='label"+ iCounter +"' style='color:#00FF00;padding-bottom: 10px;' readonly></p></td></tr>";			
			$("#table_details > tbody").append(sContent);
	
			setDatePicker('ChequeDate' + iCounter);	
			iCounter = iCounter + 1;
		}
		iTableCounter = iTableCounter + 1;
	document.getElementById('maxrows').value = iCounter;
	
	}

	function setDatePicker(fieldName)
	{
		//alert(fieldName);
		$(function() {
			$('#' + fieldName).datepicker({
				dateFormat: "dd-mm-yy",
				minDate: minGlobalCurrentYearStartDate,
				maxDate: maxGlobalCurrentYearEndDate
				});
			});
	}
	
	function go_error()
    {
		$(document).ready(function()
		{
			$("#error").show();
		});
        setTimeout('hide_error()',8000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			//$("#error").fadeOut("slow");
		});
		document.getElementById('error').innerHTML = '';
        document.getElementById('error').style.display = 'none';
    }
	
     $(function()
        {
			
	    <?php if($_SESSION['role'] == ROLE_MANAGER)
	   { ?>
		minGlobalCurrentYearStartDate = '<?php echo $Latest_Bill_Start_Date1;?>';
		console.log(minGlobalCurrentYearStartDate);		
	   <?php }?>
			
	    $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true,
			minDate: minGlobalCurrentYearStartDate,
			maxDate: maxGlobalCurrentYearEndDate
        })});
		
	$(function()
	{
		<?php if($_SESSION['role'] <> ROLE_SUPER_ADMIN)
		{ ?>
			for(var i = 1 ; i <= 5 ; i++)
			{
				document.getElementById('VoucherCounter'+i).disabled = true;
				document.getElementById('VoucherCounter'+i).style.background = "#d1d1d1";	
			}
			
		<?php } ?>
	});
	</script>
</head>
<?php if(isset($_REQUEST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<?php } ?>
<body>
<br>
<div class="panel panel-info" id="panel" style="display:none">
<?php if($_REQUEST['depositid'] == -3)
{?>
    <div class="panel-heading" id="pageheader">Cash Reciept</div>
<?php 
}
else
{?>
	 <div class="panel-heading" id="pageheader">Incoming Cheque</div>
<?php } ?> 
<div id="panel-inner-div">    
	<?php
		$sqlBankName = "select BankName from bank_master where BankID = '" . $_REQUEST['bankid'] . "'";
		$sqlResult = $m_dbConn->select($sqlBankName);
		echo '<center><h3>'.$sqlResult[0]['BankName'].'</h3></center>';
	?>
</font></h2>
<div style="float:right;margin-right: 15px;margin-top: -30px;"><button type="button" class="btn btn-primary" onClick="window.open('import_payments_receipts.php?type=receipts');" id="" >Import General Receipt</button><button type="button" class="btn btn-primary" style="margin:5px;" onClick="PrintDepositSlip()" >Print Deposit Slip</button></div>


<form name="depositslip" id="depositslip" method="post" action="GenerateDepositSlip.php" target="_blank">
	<input type="hidden" name="CanClose" id="CanClose" value="false" />
	<input type="hidden" name="depositslipid" id="depositslipid" value="<?php echo $_REQUEST["depositid"] ?>" />
	<input type="hidden" name="from" id="from" value="<?php echo $CurrentYearBeginingDate ?>" />
	<input type="hidden" name="to" id="to" value="<?php echo date("d-m-Y") ?>" />
</form>
<?php 
if(IsReadonlyPage() == false){?>
<div style="margin-left:5vw"><table><tr><td><input type="checkbox" id="MembersOnly" checked><b> Show Members Only</b></input></td></tr></table></div>

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
<table><tr><td><?php echo $star;?></td><td>Voucher Date
			<input type="text" name="VoucherDate" id="VoucherDate" class="basics" size="10" value="<?php //echo $VoucherDate; ?>" style="width:80px;" />
            </td></tr></table>

<form name="ChequeDetails" id="ChequeDetails" method="post" action="process/chequedetails.process.php">
<input type="hidden" name="maxrows" id="maxrows" />
<input type="hidden" name="DepositID" id="DepositID"  value="<?php echo $_REQUEST["depositid"];?>"/>
<input type="hidden" name="ChequeDetailid" id="ChequeDetailid" />
	<div class="scrollit">    
		<table id="table_details">
			<tbody>
            <?php if($_REQUEST['depositid'] == -3)
			{?>	
    			<tr><th style='text-align: center;'>Voucher Number</th><th>Paid By</th><th>Bill Type</th><th>Date<br>(DD-MM-YYYY)</th><th>Amount</th><?php if($_SESSION['default_tds_receivable'] <> 0){?><th>TDS Cut</th><?php }?><th>Comments</th><th></th></tr>
            <?php } else
			{?>
            <tr><th style='text-align: center;'>Voucher Number</th><th>Paid By</th><th>Bill Type</th><th>Cheque Number</th><th>Cheque Date<br>(DD-MM-YYYY)</th><th>Amount</th><?php if($_SESSION['default_tds_receivable'] <> 0){?><th>TDS Cut</th><?php }?><th>Payer Bank</th><th>Payer Cheque Branch</th><th>Comments</th><th></th></tr>
            <?php } ?>
    		</tbody>
    		</table>
    </div>

<?php if($_SESSION['is_year_freeze'] == 0)
{?>
	<input id="addnewrow" name="addnewrow"  type="button" value="Add 5 Rows" onClick="AddNewRow()"  class="btn btn-primary">
	<input type="button" onClick="SubmitChequeDetails(minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate)" value="Submit" id="Submit"  class="btn btn-primary">
<?php }
else
{?>
<input id="addnewrow" name="addnewrow"  type="button" value="Add 5 Rows" onClick="AddNewRow()"  class="btn btn-primary" disabled="disabled">
	<input type="button" onClick="SubmitChequeDetails(minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate)" value="Submit" id="Submit"  class="btn btn-primary" disabled="disabled">
	<?php }?>
<table align="center">
<?php
if(isset($msg))
{
	if(isset($_POST['ShowData']))
	{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
	}
	else
	{
	?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $msg; ?></b></font></td></tr>
	<?php
	}
}
else
{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
}
?>
        <tr>
			<!--<td><?php //echo $star;?>Bank</td>-->
			<td><input type="hidden" name="BankID" id="BankID"  value='<?php echo $_REQUEST["bankid"] ?>' size="10" readonly  style="width:80px;"/></td>
            <!--<td><input type="hidden" name="BankName" id="BankName" value='<?php //echo $_REQUEST["bankname"] ?>' size="10" readonly  style="width:80px;"/></td>-->
		</tr>
        
        <tr><td>
        <input type="hidden" name="DepositID" id="DepositID"  value="<?php echo $_REQUEST["depositid"] ?>" /></td></tr>
        <input type="hidden" name="edit" id="edit" value="<?php echo $_REQUEST['edt']; ?>" />
        <input type="hidden" name="login_id" id="login_id" value="<?php echo $_SESSION['login_id']; ?>" />
</table>
</form>
<script>
AddNewRow();
</script>
<?php }?>
</div> 
<center>
<table align="center">
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_ChequeDetails->pgnation($_REQUEST['depositid'],IsReadonlyPage());
/*echo "<br>";
echo $str = $obj_ChequeDetails->display1($str1);
echo "<br>";
$str1 = $obj_ChequeDetails->pgnation($_REQUEST['depositid']);
echo "<br>";*/
?>
</td>
</tr>
</table>
</center>
</div>
</div>

<?php include_once "includes/foot.php"; ?>
<?php
if(isset($_REQUEST['delete']) && isset($_REQUEST['edt']))
{
	 
?>
<script>
	delSetFlag = true;
	getChequeDetails('edit-' + <?php echo  $_REQUEST['edt'] ?>); 	
	 
</script>

<?php    
}
else if(isset($_REQUEST['edt']))
{ 
?>

<script>	
	getChequeDetails('edit-' + <?php echo  $_REQUEST['edt'] ?>); 
</script>

<?php    
}

if(isset($_REQUEST['report']))
	{ ?>
	<script>getChequeDetails('edit-'+<?php echo $_REQUEST['report'] ;?>)</script>;
<?php }

if(IsReadonlyPage() == true)
{?>
<script>
	$("#panel-inner-div").css( 'pointer-events', 'none' );
</script>
<?php }?>
<script type="text/javascript">
function PrintDepositSlip()
	{
		
	 document.depositslip.submit();
		
	}
</script>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - NEFT Transactions</title>
</head>

<?php
include_once "ses_set_as.php";
if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else
{
	include_once("includes/head_s.php");
}

include_once("classes/ChequeDetails.class.php");
$obj_ChequeDetails = new ChequeDetails($m_dbConn);

include_once("classes/genbill.class.php");
$objGenBill = new genbill($m_dbConn);

include_once("classes/include/fetch_data.php");
$objFetchDetails = new FetchData($m_dbConn);
include_once("classes/wing.class.php");
$obj_wing = new wing($m_dbConn);
include_once("classes/unit.class.php");
$obj_wing = new unit($m_dbConn);
include_once("classes/utility.class.php");
$objUtility = new utility($m_dbConn);

$VoucherDate = '';
$status = $objUtility->getIsDateInRange( date("Y-m-d") , getDBFormatDate($_SESSION['default_year_start_date']) , getDBFormatDate($_SESSION['default_year_end_date'])); 

$IsSameCntApply = $objUtility->IsSameCounterApply();
if($IsSameCntApply == 1)
{
	$Counter = $objUtility->GetCounter(VOUCHER_RECEIPT, 0);
}
else
{
	$Counter = $objUtility->GetCounter(VOUCHER_RECEIPT, $_REQUEST['bankid']);	
}



if($status)
{
	$VoucherDate = 	date("d-m-Y");	
}
else
{
	$VoucherDate = 	getDisplayFormatDate($_SESSION['default_year_end_date']);		
}

$Latest_Bill_Start_Date = $objFetchDetails->getlatestbillstartdate();

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
 

<html>
<head>

	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
   <!-- <link rel="stylesheet" type="text/css" media="all" href="jsDatePick_ltr.min.css" />-->
    <!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <link rel="stylesheet" href="/resources/demos/style.css">-->
<!--<script type="text/javascript" src="../jquery/jquery_min.js"></script>-->
<!--<script type="text/javascript" src="jsDatePick.min.1.3.js"></script>-->
	<script type="text/javascript" src="js/ajax.js"></script>
    <script type="text/javascript" src="js/ajax_new.js"></script>
	<script type="text/javascript" src="js/jsChequeDetails_20190705.js?rndstr=<%= getRandomStr() %>"></script>
    <!--<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
	<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
    <script type="text/javascript" src="javascript/jquery.clockpick.1.2.4.js"></script>
    <script type="text/javascript" src="javascript/ui.core.js"></script>
    <script type="text/javascript" src="javascript/ui.datepicker.js"></script>-->
    <script type="text/javascript" src="js/populateData.js"></script>
    <script type="text/javascript" src="js/validate.js"></script>
    <script type="text/javascript" src="js/jsCommon_20190326.js"></script>
	<script language="javascript" type="application/javascript">
	
    
    SetAry_ExitingExCounter(<?php echo json_encode($Counter[0]['ExitingCounter']);?>);
	var TDSReceivable = '<?php echo $_SESSION['default_tds_receivable']?>'; // Setting TDSReceivable to know whether TDS is set in default or not
    


	var iCounter = 1;
	var iTableCounter = 1;
	
	
	function PaidToChanged(PaidTo,mCounter)
	{
		var iMembersOnly = document.getElementById("MembersOnly").checked;
		var PaidToVal = document.getElementById(PaidTo.id).value;
		if((iMembersOnly == 1))
		{
			
		document.getElementById(PaidTo.id).innerHTML = "<?php echo $obj_ChequeDetails->comboboxEx("select led.id as id,concat_ws(' - ',led.ledger_name,mem.owner_name) as ledger_name from ledger as led JOIN unit as unittable on led.id=unittable.unit_id JOIN member_main as mem  on mem.unit=unittable.unit_id where receipt='1' and led.society_id=".$_SESSION['society_id']." and led.categoryid=".DUE_FROM_MEMBERS." and  mem.ownership_date <= '" .$_SESSION['default_year_end_date']. "' and mem.ownership_status=1 ORDER BY led.ledger_name ASC"); ?>";
		 //alert('done');
		  toggleSupplementaryCheckbox(mCounter);
		}
		else
		{
			document.getElementById(PaidTo.id).innerHTML = "<?php echo $obj_ChequeDetails->comboboxEx("select id,ledger_name from ledger where receipt='1' and categoryid !=".DUE_FROM_MEMBERS." and society_id=".$_SESSION['society_id']."  ORDER BY ledger_name ASC"); ?>";
			 toggleSupplementaryCheckbox(mCounter);
		}
		
	}
	
	function AddNewRow()
	{
		//$("#myTable > tbody").append("<tr><td><input type='text' id=" + iCount + " /></td><td><input type='text' /></td></tr>");
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
				VoucherRowCount = parseInt(currentCounter);	
			}
						
			VoucherCounter = getExCounter(VoucherRowCount);
			tmp = VoucherCounter;
			
			var sContent = "<tr id='NeftRow" + iCounter+"'><td><input type='text' id='VoucherCounter" + iCounter+"' name='VoucherCounter" + iCounter+"' value = "+VoucherCounter+" style='width:70px;'></td>";
			
			sContent += "<td><select style='width:150px;' id='PaidBy" + iCounter+"' name='PaidBy" + iCounter+"' onfocus='PaidToChanged(this," + iCounter +")' onblur='PopulatePayerBank(this.value," + iCounter +")'> <?php echo $PaidBy = $obj_ChequeDetails->comboboxEx("select id,ledger_name from ledger where receipt='1' and society_id=".$_SESSION['society_id']."  ORDER BY ledger_name ASC");?>'</select></td>";
			
			//***New Drop Down list for  Bill Type
			
			sContent += "<td><select  name='DrpDwnBillType"+iCounter+"' id='DrpDwnBillType"+iCounter+"' style='width:120px;'><option value='0'>Regular bill</option><option value='1'>Supplementry bill</option><option value='2'>Invoice bill</option></select></td>";
			
			sContent += "<td><input type='text' id='ChequeNumber" + iCounter+"' name='ChequeNumber" + iCounter+"' style='width:100px;'></td>";
			
			sContent += "<td><input type='text' id='ChequeDate" + iCounter+"' name='ChequeDate" + iCounter+"' style='width:70px;'></td>";
			
			sContent += "<td><input type='text' id='Amount" + iCounter+"' name='Amount" + iCounter+"' onBlur='extractNumber(this,2,true);' onKeyUp='extractNumber(this,2,true);' onKeyPress='return blockNonNumbers(this, event, true, true)' style='width:90px;'></td>";
			
			<?php if($_SESSION['default_tds_receivable'] <> 0){?>
			sContent += "<td><input type='text' id='TDS_Amount" + iCounter+"' name='TDS_Amount" + iCounter+"' onBlur='extractNumber(this,2,true);' onKeyUp='extractNumber(this,2,true);' onKeyPress='return blockNonNumbers(this, event, true, true)' style='width:90px;'></td>";
			<?php }?>
			
			sContent += "<td><input type='text' id='PayerBank" + iCounter+"' name='PayerBank" + iCounter+"' style='width:120px;'></td>";
			
			sContent += "<td><input type='text' id='PayerChequeBranch" + iCounter+"' name='PayerChequeBranch" + iCounter+"' style='width:120px;'></td>";
			
			sContent += "<td><input type='text' id='Comments" + iCounter+"' name='Comments" + iCounter+"' style='width:150px;'></td></tr>";
			
			sContent += "<td><input type='hidden' name='Current_Counter" + iCounter+"' id='Current_Counter" + iCounter+"' value="+currentCounter+" style='width:80px;' /></td>";
			
			sContent += "<td><input type='hidden' name='OnPageLoadTimeVoucherNumber" + iCounter+"' id='OnPageLoadTimeVoucherNumber" + iCounter+"' value="+VoucherCounter+" style='width:80px;' /></td>";
			
			sContent +=	"<tr><td colspan='3'><label id='LedgerBalance" + iCounter+"' style='color:blue'></label></td></tr>";
			
			sContent += "<td><input type='hidden' id='reconcileDate" + iCounter+"' name='reconcileDate" + iCounter+"' value='0'> </td>";
			
			sContent += "<td><input type='hidden' id='reconcileStatus" + iCounter+"' name='reconcileStatus" + iCounter+"' value='0'> </td>";
			
			sContent += "<td><input type='hidden' id='reconcile" + iCounter+"' name='reconcile" + iCounter+"' value='0'> </td>";
			
			sContent += "<td><input type='hidden' id='return" + iCounter+"' name='return" + iCounter+"' value='0'> </td>";
			
			
			sContent += "<tr id='NeftRowLabel" + iCounter+"'><td><p id='label"+ iCounter +"' name='label"+ iCounter +"' style='color:#00FF00;padding-bottom: 10px;' readonly></p></td></tr>";
			
			
			$("#table_details > tbody").append(sContent);
			
			setDatePicker('ChequeDate' + iCounter);	
			iCounter = iCounter + 1;
		}
		//iTableCounter = iTableCounter + 1;
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
				minGlobalCurrentYearStartDate = '<?php echo $Latest_Bill_Start_Date;?>';
						
			<?php }?>

            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true ,
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
    <div class="panel-heading" id="pageheader">NEFT Transactions</div>
    <?php 
if(IsReadonlyPage() == false){?>
<div id="panel-inner-div">
	<?php
		$sqlBankName = "select BankName from bank_master where BankID = '" . $_REQUEST['bankid'] . "'";
		$sqlResult = $m_dbConn->select($sqlBankName);
		echo '<center><h3>'.$sqlResult[0]['BankName'].'</h3></center>';
	?>
</font></h2>
<div style="float:right;margin-right: 15px;margin-top: -30px;"><button type="button" class="btn btn-primary" onClick="window.open('import_payments_receipts.php?type=receipts');" id="" >Import General Receipt</button></div>


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
<div style="margin-left:5vw"><table><tr><td><input type="checkbox" id="MembersOnly" checked ><b> Show Members Only</b></input></td><td></td></tr></table></div>

<center>
<table><tr><td><?php echo $star;?></td><td>Voucher Date
	<input type="text" name="VoucherDate" id="VoucherDate" class="basics" size="10" value=<?php echo $VoucherDate; ?> style="width:80px;" />
            </td></tr></table>

<form name="ChequeDetails" id="ChequeDetails" method="post" action="process/chequedetails.process.php">
<input type="hidden" name="maxrows" id="maxrows" />
</table>
<br>
<table id="table_details">
	<tbody>
    	<tr>
        	<th>Voucher No</th>
        	<th>Paid By</th>
            <th>Bill Type</th>
            <th>Transaction No</th>
            <th>Date</th>
            <th>Amount</th>
            <?php if($_SESSION['default_tds_receivable'] <> 0){?>
            <th>TDS Cut</th>
            <?php }?>
            <th>Payer Bank</th>
            <th>Payer Branch</th>
            <th>Comments</th>
        </tr>
    </tbody>
</table>
<input id="addnewrow" type="button" value="Add 5 Rows" onClick="AddNewRow()"  class="btn btn-primary" <?php echo $btnDisable?>>
<input type="button" onClick="SubmitNeftDetails(minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate)" value="Submit" id="Submit"  class="btn btn-primary" <?php echo $btnDisable?>>

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
AddNewRow();</script>
</div>
<?php } ?>
<table align="center">
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_ChequeDetails->pgnation_neft($_REQUEST['bankid'],IsReadonlyPage());
echo "<br>";
//echo $str = $obj_ChequeDetails->display1_neft($str1);
echo "<br>";
//$str1 = $obj_ChequeDetails->pgnation_neft($_REQUEST['bankid']);
echo "<br>";
?>
</td>
</tr>
</table>
</center>
</div>
</body>

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
{  ?>
<script>	
	getChequeDetails('edit-' + <?php echo  $_REQUEST['edt'] ?>); 
</script>
<?php    
}

if(IsReadonlyPage() == true)
{?>
<script>
	$("#panel-inner-div").css( 'pointer-events', 'none' );
</script>
<?php }?>

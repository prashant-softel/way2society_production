<?php 


include_once("datatools.php");

include_once("classes/include/dbop.class.php");

include_once("classes/utility.class.php");

include_once("classes/dbconst.class.php");

include_once("classes/PaymentDetails.class.php");

$dbConn = new dbop();
$obj_utility=new utility($dbConn);
$obj_PaymentDetails = new PaymentDetails($dbConn);
$PopupDetails=$obj_PaymentDetails->ShowPopupData($_REQUEST['paidto'],$_REQUEST['clrVNO']);

$InvoiceCounter = $obj_utility->GetCounter(VOUCHER_JOURNAL,0);
$fetchComment=$obj_PaymentDetails->fetchComment($_REQUEST['clrVNO']);
$paidTo=$obj_utility->getLedgerName($_REQUEST['paidto']);
$TDSAmountData=$obj_PaymentDetails->TDSPayble($_REQUEST['paidto']);
$IsRoundOffAmt= $obj_utility->GetSocietyInformation($_SESSION['society_id']);

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
<link href="cssss/layout.css" rel="stylesheet" type="text/css" />
<style>
.totalTable th 
{
text-align: left;
border: 1px solid grey; 
}
.totalTable td
{
border: 1px solid grey; 
}
.select_invoice
{	
padding: 0px !important; 
width: 130px;
font-size: 14px;
/*height: 25px;*/
}
.btn
{
color: #fff;
background-color: #337ab7;
border-color: #2e6da4;    
border-radius: 4px;font-size: 14px;
font-weight: 400;
line-height: 1.42857143;
text-align: center;
white-space: nowrap;
vertical-align: middle;
cursor: pointer;
}
}
</style>
<script type="text/javascript" src="js/jsPaymentDetails_20190706.js"></script>
<script type="text/javascript" src="js/jsCommon_20190326.js?123"></script>
<script type="text/javascript" src="js/validate.js"></script>
<script>
SetAry_ExitingExCounter(<?php echo json_encode($InvoiceCounter[0]['ExitingCounter']);?>);
</script>
<script type="text/javascript">
 function addtab(value)
{
		 document.getElementById('invoice_no_'+value).focus();
}
     
var datePickerOptions={ 
	dateFormat: "dd-mm-yy",
		minDate: minGlobalCurrentYearStartDate,
		maxDate: maxGlobalCurrentYearEndDate
	//defaultDate: '01-01-1980'
};
	 
var FieldCount=0;
var MaxInputs=20;
var bNewRow = false;
var inputSGST= "<?php echo $_SESSION['sgst_input']?>";
var inputCGST= "<?php echo $_SESSION['cgst_input']?>";
var InvoiceVoucherCounter = parseInt(getExCounter(<?php echo $InvoiceCounter[0]['CurrentCounter'];?>));
InvoiceVoucherCounter = (InvoiceVoucherCounter && InvoiceVoucherCounter != '')?parseInt(InvoiceVoucherCounter)-1:0;

var inputIGST= "<?php echo $_SESSION['igst_input']?>";
var RoundOffDfault ="<?php echo $_SESSION['default_ledger_round_off']?>";
var ISRoundOffSociety ="<?php echo $IsRoundOffAmt['IsRoundOffLedgerAmt']; ?>"

$(function () {
$("#btnAdd").bind("click", function () {
if(FieldCount <= MaxInputs) //max file box allowed
  {
   FieldCount++; 
   document.getElementById('count').value=FieldCount;
  }
	//alert(bNewRow);
   var div = $("<tr />");
	   div.html(GetDynamicTextBox("", bNewRow));
	   $("#mem_table").append(div);
	   $(".basics").datepicker(datePickerOptions);
});

    $("#btnGet").bind("click", function () {
      var values = "";
      $("input[name=members]").each(function () {
      values += $(this).val() + "\n";
     });
      alert(values);
   });
    	$("body").on("click", ".remove", function () {
        $(this).closest("div").remove();
   });
});
//}
function GetDynamicTextBox(value, bNewRow) {
	//alert(bNewRow);
	var trData='';
	 
	
if(bNewRow == false)
{
	trData+='<td style = "width:5%" id="td_sel_invoice" align="center"><input type="checkbox"  id="select_invoice_'+FieldCount+'" name="select_invoice_'+FieldCount+'"  onChange="AddValues(true, '+FieldCount+');"  style="width:47px;"><input type="hidden" id="Doc_statusID_'+FieldCount+'" name=id="Doc_statusID_'+FieldCount+'"></td>';
	
	trData+='<td style = "width:7%" id="td_invoice_external_voucher_no'+FieldCount+'" ><input name = "invoice_external_voucher_no_'+FieldCount+'" id = "invoice_external_voucher_no_'+FieldCount+'" type="text" value = "" style="width: 90px;" ></td>';
	
trData+='<td style = "width:7%" id="td_invoice_date'+FieldCount+'" ><input name = "invoice_date_'+FieldCount+'" id = "invoice_date_'+FieldCount+'" type="text" value = "' + value + '" onChange="addtab('+FieldCount+');" style="width: 90px;"></td>';
}
else
{
	InvoiceVoucherCounter++;

	trData+='<td style = "width:5%" id="td_sel_invoice" align="center"><input type="checkbox"  id="select_invoice_'+FieldCount+'" name="select_invoice_'+FieldCount+'"  onChange="AddValues(true, '+FieldCount+');" style="width:47px;" CHECKED><input type="hidden" id="Doc_statusID_'+FieldCount+'" name=id="Doc_statusID_'+FieldCount+'"></td>';
	
	trData+='<td style = "width:7%" id="td_invoice_external_voucher_no'+FieldCount+'" ><input type="text" name = "invoice_external_voucher_no_'+FieldCount+'" id = "invoice_external_voucher_no_'+FieldCount+'" value = "' + InvoiceVoucherCounter +'" style="width: 90px;"></td>';
	
	trData+='<td  style = "width:7%" id="td_invoice_date'+FieldCount+'" ><input name = "invoice_date_'+FieldCount+'" id = "invoice_date_'+FieldCount+'" type="text" value = "' + value + '" class="basics" onChange="addtab('+FieldCount+');" style="width: 90px;"></td>';
}

trData+='<td  style = "width:6%" id="td_invoice_no'+FieldCount+'" ><input name = "invoice_no_'+FieldCount+'" id = "invoice_no_'+FieldCount+'" type="text" value = "' + value +'"  style="width: 75px;"><input type="hidden" id="is_invoice'+FieldCount+'" value="1"></td>';
//trData+='<td id="voucher_no'+FieldCount+'" ><a href="#" id = "voucher_no_'+FieldCount+'" onClick="editpayment(this.value);" </a><td>';

//trData+='<td id="td_voucher_no'+FieldCount+'" ><input name = "voucher_no_'+FieldCount+'" id = "voucher_no_'+FieldCount+'" type="text" value = "' + value +'" onClick="editpayment(this.value);" style="width: 80px;border-top: none; border-bottom: none; border-left: none; border-right: none;font-weight: bold;text-align: center;cursor: pointer; color:blue;" readonly >';

<!--trData+='<td>&nbsp;&nbsp;</td>';
-->
if(bNewRow == false)
{
 trData+='<input name = "voucher_no_'+FieldCount+'" id = "voucher_no_'+FieldCount+'" type="hidden" value = "' + value +'" onClick="editpayment(this.value);" style="width: 80px;border-top: none; border-bottom: none; border-left: none; border-right: none;font-weight: bold;text-align: center;cursor: pointer; color:blue;" readonly >';	
 trData+='<input name = "InvoiceOnPageLoadTimeExtenalVoucherNumber'+FieldCount+'" id = "InvoiceOnPageLoadTimeExtenalVoucherNumber'+FieldCount+'" type="hidden" value="0" style="width: 80px;border-top: none; border-bottom: none; border-left: none; border-right: none;font-weight: bold;text-align: center;cursor: pointer; color:blue;" readonly >';	
 trData += '<input type="hidden" name = "New_Invoice_'+FieldCount+'" id = "New_Invoice_'+FieldCount+'" type="text" value = "0"></td>';
}
else
{
  trData +='<input name = "voucher_no_'+FieldCount+'" id = "voucher_no_'+FieldCount+'" type="hidden" value = "' + value +'" style="width: 80px;border-top: none; border-bottom: none; border-left: none; border-right: none;font-weight: bold;text-align: center;cursor: pointer; color:blue;" readonly >';	
  trData += '<input name = "invoice_external_current_voucher_no_'+FieldCount+'" id = "invoice_external_current_voucher_no_'+FieldCount+'" type="hidden" value = "' + InvoiceVoucherCounter +'"  style="width: 80px;border-top: none; border-bottom: none; border-left: none; border-right: none;font-weight: bold;text-align: center;cursor: pointer; color:blue;" readonly >';		
  trData += '<input type=\"hidden\" name = \"New_Invoice_'+FieldCount+'"\" id = \"New_Invoice_'+FieldCount+'"\" value = \"1\">';
}

//trData+="<td style = 'width:15%'><select id= \"Expence_by_"+ FieldCount +"\" name = \"Expence_by_"+ FieldCount + "\" style=\"width:190px; height:22px;\" onFocus='PaidToChanged(this);' > <?php //echo $ExpenseBy = $obj_PaymentDetails->combobox1("select `id`,concat_ws(' - ', ledgertable.ledger_name,categorytbl.category_name)  from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where ledgertable.categoryid NOT IN(".BANK_ACCOUNT.",".CASH_ACCOUNT.") and  ledgertable.society_id=".$_SESSION['society_id']." and categorytbl.group_id = 4 ORDER BY ledgertable.ledger_name ASC ",0);?></select></td>";
trData+="<td style = 'width:15%'><select id= \"Expence_by_"+ FieldCount +"\" name = \"Expence_by_"+ FieldCount + "\" style=\"width:190px; height:22px;\" onFocus='PaidToChanged(this);' onchange='checkLedger(this.value,"+FieldCount+");' > <?php echo $ExpenseBy = $obj_PaymentDetails->combobox1("select `id`,concat_ws(' - ', ledgertable.ledger_name,categorytbl.category_name)  from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where ledgertable.categoryid NOT IN(".BANK_ACCOUNT.",".CASH_ACCOUNT.") and  ledgertable.society_id=".$_SESSION['society_id']." and categorytbl.group_id =4 OR (categorytbl.group_id =2 and ledgertable.categoryid=".FIXED_ASSET.") ORDER BY ledgertable.ledger_name ASC ",0);?></select></td>";

<!--trData+='<td id="td_igst_amount'+FieldCount+'" ><input name = "IGST_amount_'+FieldCount+'" id = "IGST_amount_'+FieldCount+'" type="text" value = "' + value + '"  style="width:50px;"  onBlur="AddValues(true);" onKeyUp="extractNumber(this,2,true);"/></td>-->';
//if((inputSGST !=0 && inputCGST !=0) || (inputIGST !=0 && inputCESS !=0))
if((inputSGST !=0 && inputCGST !=0) || (inputIGST !=0 ))
{
	

trData+='<td style = "width:7%" ><input name = "gross_amount_'+FieldCount+'" id = "gross_amount_'+FieldCount+'" type="text" value = "' + value + '"  style="width:85px;" onBlur="AddValues(true);" onKeyUp="extractNumber(this,2,true);"/></td>';

//trData+='<td style = "width:4%" ><input name = "apply_IGST_'+FieldCount+'" id = "apply_IGST_'+FieldCount+'" type="checkbox" value = "' + value + '"  style="width:35px;" onChange="IGST_Check(true, '+FieldCount+');" /><input type="hidden" id="IGST_Flag_'+FieldCount+'" name="IGST_Flag_'+FieldCount+'" value="0"></td>';
trData+='<td  style = "width:4%" id="td_igst_amount'+FieldCount+'" ><input name = "IGST_amount_'+FieldCount+'" id = "IGST_amount_'+FieldCount+'" type="text" value = "' + value + '"  style="width:50px;"  onBlur="AddValues(true);" onKeyUp="extractNumber(this,2,true);" disabled/><input type="hidden" id="IGST_Flag_'+FieldCount+'" name="IGST_Flag_'+FieldCount+'" value="0"></td>';


trData+='<td  style = "width:4%" id="td_cgst_amount'+FieldCount+'" ><input name = "CGST_amount_'+FieldCount+'" id = "CGST_amount_'+FieldCount+'" type="text" value = "' + value + '"  style="width:50px;" onBlur="AddValues(true);" onKeyUp="extractNumber(this,2,true);"/></td>';

trData+='<td  style = "width:4%" id="td_sgst_amount'+FieldCount+'" ><input name = "SGST_amount_'+FieldCount+'" id = "SGST_amount_'+FieldCount+'" type="text" value = "' + value + '"  style="width:50px;"  onBlur="AddValues(true);" onKeyUp="extractNumber(this,2,true);" /></td>';


//trData+='<td  style = "width:4%" id="td_cess_amount'+FieldCount+'" ><input name = "CESS_amount_'+FieldCount+'" id = "CESS_amount_'+FieldCount+'" type="text" value = "' + value + '"  style="width:50px;"  onBlur="AddValues(true);" onKeyUp="extractNumber(this,2,true);" disabled /></td>';
}
else
{
trData+='<td style = "width:7%"><input name = "gross_amount_'+FieldCount+'" id = "gross_amount_'+FieldCount+'" type="text" value = "' + value + '"  style="width:85px; background-color: lightgray;" onBlur="AddValues(true);" onKeyUp="extractNumber(this,2,true);" readonly/></td>';	

//trData+='<td style = "width:4%" ><input name = "apply_IGST_'+FieldCount+'" id = "apply_IGST_'+FieldCount+'" type="checkbox" value = "' + value + '"  style="width:35px;" disabled/ ><input type="hidden" id="IGST_Flag_'+FieldCount+'" name="IGST_Flag_'+FieldCount+'" value="0"></td>';
trData+='<td  style = "width:4%" id="td_igst_amount'+FieldCount+'" ><input name = "IGST_amount_'+FieldCount+'" id = "IGST_amount_'+FieldCount+'" type="text" value = "' + value + '" style="width:50px;background-color: lightgray;" onBlur="AddValues(true);" onKeyUp="extractNumber(this,2,true);" onClick="Showmasg1(this);" readonly/><input type="hidden" id="IGST_Flag_'+FieldCount+'" name="IGST_Flag_'+FieldCount+'" value="0"></td>';
	
trData+='<td  style = "width:4%" id="td_cgst_amount'+FieldCount+'" ><input name = "CGST_amount_'+FieldCount+'" id = "CGST_amount_'+FieldCount+'" type="text" value = "' + value + '"  style="width:50px;background-color: lightgray;" onBlur="AddValues(true);" onKeyUp="extractNumber(this,2,true);" onClick="Showmasg(this);" readonly/></td>';

trData+='<td  style = "width:4%" id="td_sgst_amount'+FieldCount+'" ><input name = "SGST_amount_'+FieldCount+'" id = "SGST_amount_'+FieldCount+'" type="text" value = "' + value + '"  style="width:50px;background-color: lightgray;"  onBlur="AddValues(true);" onKeyUp="extractNumber(this,2,true);" onClick="Showmasg(this);"  readonly/></td>';
	


//trData+='<td  style = "width:4%" id="td_cess_amount'+FieldCount+'" ><input name = "CESS_amount_'+FieldCount+'" id = "CESS_amount_'+FieldCount+'" type="text" value = "' + value + '"  style="width:50px;background-color: lightgray;" onBlur="AddValues(true);" onKeyUp="extractNumber(this,2,true);" onClick="Showmasg1(this);" readonly/></td>';

}
<!--trData+='<td id="td_cess_amount'+FieldCount+'" ><input name = "CESS_amount_'+FieldCount+'" id = "CESS_amount_'+FieldCount+'" type="text" value = "' + value + '"  style="width:50px;"  onBlur="AddValues(true);"  onKeyUp="extractNumber(this,2,true);"/></td>-->';

//trData+='<td>&nbsp;&nbsp;</td>';


if(RoundOffDfault != 0 && ISRoundOffSociety == 1)
{
	
	trData+='<td style = "width:5%"><input name = "RoundOff_amount_'+FieldCount+'" id = "RoundOff_amount_'+FieldCount+'" type="text" value = "' + value + '"  style="width:60px;" onBlur="AddValues(true);" onKeyUp="extractNumber(this,2,true);" /></td>';
}
else
{
	trData+='<td style = "width:5%"><input name = "RoundOff_amount_'+FieldCount+'" id = "RoundOff_amount_'+FieldCount+'" type="text" value = "' + value + '"  style="width:60px;" onBlur="AddValues(true);" onKeyUp="extractNumber(this,2,true);" disabled/></td>';
}
trData+='<td style = "width:6%" id="td_inv_amount'+FieldCount+'"><input name = "invoice_amount_'+FieldCount+'" id = "invoice_amount_'+FieldCount+'" type="text" value = "'  + value +'"  style="width: 70px;" onBlur="AddValues(true);" onKeyUp="extractNumber(this,2,true);"></td>';

trData+='<td style = "width:5%"><input name = "TDS_amount_'+FieldCount+'" id = "TDS_amount_'+FieldCount+'" type="text" value = "' + value + '"  style="width:60px;" onBlur="AddValues(true);" onKeyUp="extractNumber(this,2,true);" /></td>';




trData+='<td style = "width:6%"><input name = "net_payable_amount_'+FieldCount+'" id = "net_payable_amount_'+FieldCount+'" type="text" value = "' + value + '"  style="width:80px;" onBlur="AddValues(true);"  /></td>';

trData+="<td style = 'width:12%'><select id= \"TDS_Payable_"+ FieldCount +"\" name = \"TDS_Payable_"+ FieldCount + "\" style=\"width: 148px;; height:22px;\" value="+value+"  > <?php echo $TDSPayable = $obj_PaymentDetails->combobox2("select `id`,concat_ws(' - ', ledgertable.ledger_name,'(',categorytbl.category_name,')')  from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where ledgertable.society_id=".$_SESSION['society_id']." and categorytbl.group_id = 1 ",TDS_PAYABLE);?></select></td>";

trData+="<td align = 'center' style = 'width:4%'><input type='checkbox'  id='delete_"+FieldCount+"' name='delete_"+FieldCount+"'style = 'display:none;' ></td>";

trData +="<input type ='hidden' name ='invoice_raised_voucher_no_"+FieldCount+"' id='invoice_raised_voucher_no_"+FieldCount+"'>"
    return trData;
}

function PaidToChanged(ExpenseBy,mCounter)
	{
		var iMembersOnly = document.getElementById("ExpenseOnly").checked;
		var PaidToVal = document.getElementById(ExpenseBy.id).value;
		if((iMembersOnly == 1))
		{
			//document.getElementById(ExpenseBy.id).innerHTML = "<?php //echo $ExpenseBy = $obj_PaymentDetails->combobox1("select `id`,concat_ws(' - ', ledgertable.ledger_name,'(',categorytbl.category_name,')')  from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where ledgertable.categoryid NOT IN(".BANK_ACCOUNT.",".CASH_ACCOUNT.") and  ledgertable.society_id=".$_SESSION['society_id']." and categorytbl.group_id = 4 ORDER BY ledgertable.ledger_name ASC ",0);?>";
			
			document.getElementById(ExpenseBy.id).innerHTML = "<?php echo $ExpenseBy = $obj_PaymentDetails->combobox1("select `id`,concat_ws(' - ', ledgertable.ledger_name,'(',categorytbl.category_name,')')  from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where ledgertable.categoryid NOT IN(".BANK_ACCOUNT.",".CASH_ACCOUNT.") and  ledgertable.society_id=".$_SESSION['society_id']." and categorytbl.group_id =4 OR (categorytbl.group_id =2 and ledgertable.categoryid=".FIXED_ASSET.") ORDER BY ledgertable.ledger_name ASC ",0);?>";
		 //alert('done');
		 //toggleSupplementaryCheckbox(mCounter);
		}
		else
		{
			document.getElementById(ExpenseBy.id).innerHTML = "<?php echo $ExpenseBy = $obj_PaymentDetails->combobox1("select `id`,concat_ws(' - ', ledgertable.ledger_name,'(',categorytbl.category_name,')')  from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where ledgertable.categoryid NOT IN(".BANK_ACCOUNT.",".CASH_ACCOUNT.") and  ledgertable.society_id=".$_SESSION['society_id']." ORDER BY ledgertable.ledger_name ASC ",0);?>";
			//toggleSupplementaryCheckbox(mCounter);
		}
		
	}
	
function checkLedger(val,cnt)
{
	if(inputIGST != 0)
	{
		$('#gross_amount_'+cnt).focus();
		$.ajax({
			url : "ajax/ajaxPaymentDetails.php",
			type : "POST",
			//datatype: "JSON",
			data : {"method":"Check","lId":val},
			success : function(data)
			{
			//console.log(data);	
				var arr = data.split("@@@");
				console.log(arr[1]);
				if(arr[1] == 0)
				{
					alert("Please Update Ledger GSTIN NO. Otherwise Amount Reflected on in-state vendor");
				}
				else if(arr[1] == 3)
				{
					alert("Please Update Society GSTIN NO. Otherwise Amount Reflected on in-state vendor");
				}
				else
				{
					if(arr[1] == 1)
					{
						//console.log('inside if','IGST_amount_'+cnt);
						document.getElementById('IGST_amount_'+cnt).disabled=true;
						document.getElementById('CGST_amount_'+cnt).disabled=false;
						document.getElementById('SGST_amount_'+cnt).disabled=false;
						document.getElementById('IGST_Flag_'+cnt).value = 0;
					}
					else
					{
						console.log('inside else');
						document.getElementById('IGST_amount_'+cnt).disabled=false;
						document.getElementById('CGST_amount_'+cnt).disabled=true;
						document.getElementById('SGST_amount_'+cnt).disabled=true;
						document.getElementById('IGST_Flag_'+cnt).value = 1;
						//document.getElementById('SGST_amount_'+FieldCount).disabled =false;
					}
				}
			}
		
	});
 }
}	
</script>

<script type="text/javascript">
$(function()
{
	$.datepicker.setDefaults($.datepicker.regional['']);
	$(".basics").datepicker({ 
	dateFormat: "dd-mm-yy", 
	showOn: "both", 
	buttonImage: "images/calendar.gif", 
	buttonImageOnly: true 
})});
$(function()
{
	$.datepicker.setDefaults($.datepicker.regional['']);
	$(".basics_Dob").datepicker(datePickerOptions)});

	
function setDatePicker(fieldName)
{
$(function() {
	$('#' + fieldName).datepicker({
		dateFormat: "dd-mm-yy",
		minDate: minGlobalCurrentYearStartDate,
		maxDate: maxGlobalCurrentYearEndDate
		});
	});
}

</script>
<style>
#table
{
overflow-y: auto; 
height:150px;
}
table
{
 width:100%;
}
</style>

</head>
<!--<body>-->
<body>
<div style="width:1300px" class="panel panel-info" id="panel">
<table style="border:1px solid black">
<tr><td>
<div style="width:100%; float:left">
<div style="width:75%;float: left;">
<p>Please select invoice for payment or create a new invoice. </p>
<?php if($_REQUEST['grpID'] == 4)
{?>
<p style="font-weight:bold; color:#F00;font-size: 14px;">Error : You are paying to expense ledger directly. In order to link expense invoice to the payment, you need to pay to liability ledger.</p>
<?php }?>
</div>
</div>
<input type="hidden" value="<?php echo $_REQUEST['LeafID']?>" id="LeafID">
<input type="hidden" value="<?php echo $_REQUEST['bankid']?>" id="bankid">
<input type="hidden" value="<?php echo $_REQUEST['paidto']?>" id="Paidto">
<input type="hidden" value="<?php echo $_REQUEST['chkNo']?>" id="cheque_no">
<input type="hidden" value="<?php echo $_REQUEST['clrVNO']?>" id="ClearVoucherNo">
<input type="hidden" value="<?php echo $_REQUEST['MofPay']?>" id="ModeOfPayment">
<input type="hidden" value="<?php echo $_REQUEST['recStatus']?>" id="recStatus">
<input type="hidden" value="<?php echo $_REQUEST['reDate']?>" id="reconcileDate">
<input type="hidden" value="<?php echo $_REQUEST['reconcile']?>" id="reconcile">
<input type="hidden" value="<?php echo $_REQUEST['grpID']?>" id="grpID">
<input type="hidden" value="<?php echo $_REQUEST['extPayVocher']?>" id="ExistPaymentVoucher">
<input type="hidden" value="<?php echo $_REQUEST['rowID']?>" id="RowID">
<input type="hidden" value="<?php echo $_REQUEST['currentExternalCounter']?>" id="currentCounter">
<input type="hidden" value="<?php echo $_REQUEST['externalCounter']?>" id="OnPageLoadTimeVoucherNumber">
<!--<input type="hidden" value="<?php //echo $_SESSION['sgst_input']?>" id="InputSGST">
<input type="hidden" value="<?php //echo $_REQUEST['cgst_input']?>" id="InputCGST">-->

<!--<input type="hidden" value="<?php //echo $_REQUEST['chkdt']?>" id="check_date">-->
<!--<input type="hidden" value="<?php //echo $_REQUEST['amt']?>" id="amount">-->


<table width="100%" style="border-bottom:1px solid black; border-top:1px solid black;">
<tr>
<td><b>Paid To :</b></td><td><b><?php echo $paidTo?></b></td>
<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
<?php 
if($TDSAmountData[0]['sum'] <> '')
{?>
<td><b>Pending TDS Amount :</b></td><td><b><?php echo $TDSAmountData[0]['sum']; ?></b></td>
<?php }?>
</tr>
<tr>
<?php if($_REQUEST['chkNo'] == -1)
{?>
<td>Cheque No :</td> <td><b>Cash</b></td>
<?php }
else
{?>
	<td>Cheque No :</td> <td><b><?php echo $_REQUEST['chkNo']?></b></td>
<?php }
?>
<td>Voucher No : 
	<input type='text' name='VoucherCounter' id='VoucherCounter' value="<?=$_REQUEST['externalCounter']?>"  style='width:80px;' />
</td>
<?php if( $_REQUEST['recStatus'] == 1)
{?>
<td>Cheque date :</td>
<td>
<input type="text" id="cheque_date" name="cheque_date" value="<?php echo  $_REQUEST['chkdt']?>" style='width:80px' readonly >
 </td>
 <td>Cheque Amount :</td><td ><input type="text" id="chequeamouunt" name="chequeamount" value="<?php echo  $_REQUEST['amt']?>" onBlur="AddValues(true);" style="width: 90px;" readonly><?php //echo  $_REQUEST['amt']?></td>
<?php 
}
else if($_REQUEST['chkNo'] == -1)
{?>
<td>Voucher date :</td>
<td>
<input type="text" id="cheque_date" name="cheque_date" value="<?php echo  $_REQUEST['chkdt']?>" style='width:80px' readonly >
 </td>
 <td>Cheque Amount :</td><td ><input type="text" id="chequeamouunt" name="chequeamount" value="<?php echo  $_REQUEST['amt']?>" onBlur="AddValues(true);" style="width: 90px;" readonly><?php //echo  $_REQUEST['amt']?></td>	
<?php }
else
{?>
<td>Cheque date :</td>
<td>
<input type="text" id="cheque_date" name="cheque_date" value="<?php echo  $_REQUEST['chkdt']?>" style='width:80px' 
>
</td>
<td>Cheque Amount :</td><td ><input type="text" id="chequeamouunt" name="chequeamount" value="<?php echo  $_REQUEST['amt']?>" onBlur="AddValues(true);" style="width: 90px;"><?php //echo  $_REQUEST['amt']?></td>
<?php }?>

<!--<td><img src="images/refresh.ico" width="20" height="20" alt="Refresh" title="Refresh" onClick="something()"></td>-->
</tr>
</table>
<?php  if( $_REQUEST['recStatus'] == 1)
{?>
<script>
$("#cheque_date").attr('readonly', 'readonly');
 //setDatePicker('cheque_date');

 </script>
 <?php }
 else{?>
 <script>

 setDatePicker('cheque_date');

 </script>
 <?php }?>
 <table id="table-top" width="100%">


<thead style="background-color: aqua;" >
<tr>
<th style="width:5%";>Select Invoice</th>
<th style="width:7%";>Voucher No</th>
<th style="width:7%";>Invoice Date</th>
<th style="width:6%";>Invoice No</th>
<th style="width:15%";>Expense For</th>
<th style="width:7%";>Gross Amount</th>
<!--<th style="width:4%";>Apply IGST</th>-->
<th style="width:4%";>IGST</th>
<th style="width:4%";>CGST</th>
<th style="width:4%";>SGST</th>

<!--<th style="width:4%";>CESS</th>-->
<th style="width:5%";>Round Off</th>
<th style="width:6%";>Invoice Amount</th>

<th style="width:5%";>TDS</th>

<th style="width:6%";>Net/Payable Amount</th>
<th style="width:12%";>TDS Ledger</th>
<th style="width:4%";>Delete</th>
</tr>
</thead>
</table>
 <div id="table" style="width:100%">
<table id="mem_table" >
</table>
</div>
<br>
<br>
<input type="hidden" name="count" id="count" value="1">
<div style="margin-top: -20px; height:20px;border-top: 1px solid black;">

<table align="right" style="width:99%;float: right;margin-right: 20px;margin-top: 6px;">
 <tr>
<!--<td style="text-align: left;">TDS Total </td>-->
<?php if($_REQUEST['grpID'] == 4)
{?>
<td>&nbsp;&nbsp;&nbsp;</td><td><input type="button" id="btnAdd" value="Add New Invoice"  class="btn" style="display:block; background-color: #337ab799;border-color:#eee; cursor: no-drop;" disabled></td>
 <?php }
 else
 {?>
<td>&nbsp;&nbsp;&nbsp;</td><td><input type="button" id="btnAdd" value="Add New Invoice"  class="btn" style="display:block;"></td>
<?php }?>
<td align="center"><input type="checkbox" id="ExpenseOnly" checked><b> Show Expense Only</b></input></td>
<td style="text-align: left; width:270px;"><b>Selected Invoice Total :</b> </td>
<td id="Invoicetotal" style="text-align: right;width: 100px;" ></td>
<td id="TDStotal" style="text-align: right;width: 100px;"></td>
						
</tr>
</table>
</div>
<br>

<div style="width:100%; float:left; border-top:1px solid #000;">
<!--<div style="float:left; width:30%;margin-top: 10px;margin-left: 10px;">
<!--<input type="button" class="btn" id="addNew" name="addNew" value="Add New Invoice" onClick="window.open('createvoucher.php')" style="display:none;">-->
<!--<input type="button" id="btnAdd" value="Add New Invoice"  class="btn" style="display:block;">
-->
<!--</div>-->
</div>


<script type="text/javascript">
 
 	var IsInvoiceEdit = '<?php echo $_REQUEST['IsInvoiceEdit'];?>';
    $(document).ready(function(){
		<?php for($i=0;$i<sizeof($PopupDetails);$i++)
		{
			?>
			$('#btnAdd').click();
			var TDSAMount=0;
			var CGSTValue= 0;
			var SGSTValue = 0;
			var IGSTValue= 0;
			var CESSValue = 0;
			var RoundOffValue = 0;
			var CGST_Amt ='<?php echo $PopupDetails[$i]['CGST_Amount']?>';
			var SGST_Amt = '<?php echo $PopupDetails[$i]['SGST_Amount']?>';
			var IGST_Amt = '<?php echo $PopupDetails[$i]['IGST_Amount']?>';
			var RoundOff_Amt = '<?php echo $PopupDetails[$i]['RoundOffAmount']?>';
			//var CESS_Amt=  '<?php //echo $PopupDetails[$i]['CESS_Amount']?>';
			var IGST_flg =0;
			<?php
		if($PopupDetails[$i]['InvoiceClearedVoucherNo'] <> '0')	
		{?>
          
	 	document.getElementById('select_invoice_'+FieldCount).checked=true;
		document.getElementById('delete_'+FieldCount).style.display = 'none';
		
		TDSAMount= document.getElementById('TDS_amount_'+FieldCount).value="<?php echo $PopupDetails[$i]['TDSAmount']?>";
		if(IsInvoiceEdit == 0) // IsInvoiceEdit 0 means View mode
		{
			document.getElementById('TDS_amount_'+FieldCount).readOnly=true;
		}
		<?php if($PopupDetails[$i]['tds_ledger'] <> '')
		{?>
		document.getElementById('TDS_Payable_'+FieldCount).value="<?php echo $PopupDetails[$i]['tds_ledger']?>"
		<?php 
		}
		?>
		
	   	<?php
		}else
		{ ?>
				document.getElementById('delete_'+FieldCount).style.display = 'block';
		<?php }?>
		document.getElementById('invoice_date_'+FieldCount).value="<?php  echo getDisplayFormatDate($PopupDetails[$i]['date'])?>";
		
		//document.getElementById('invoice_date_'+FieldCount).setAttribute('class','basics hasDatepicker');
		setDatePicker('invoice_date_'+FieldCount);
		
		if(IsInvoiceEdit == 0) 
		{
			document.getElementById('invoice_date_'+FieldCount).readOnly=true;
		}
	
		
	  	document.getElementById('invoice_no_'+FieldCount).value="<?php echo $PopupDetails[$i]['NewInvoiceNo']?>";
		if(IsInvoiceEdit == 0)
		{
			document.getElementById('invoice_no_'+FieldCount).readOnly=true;
			document.getElementById('invoice_no_'+ FieldCount).style.backgroundColor = 'lightgray';	
		}
		
		
		//real voucher number is changed to inner voucher number and externalCounter become voucher No
		document.getElementById('invoice_external_voucher_no_'+FieldCount).value="<?php echo $PopupDetails[$i]['ExternalCounter']?>";
		document.getElementById('InvoiceOnPageLoadTimeExtenalVoucherNumber'+FieldCount).value="<?php echo $PopupDetails[$i]['ExternalCounter']?>";
		document.getElementById('voucher_no_'+FieldCount).value="<?php echo $PopupDetails[$i]['InvoiceRaisedVoucherNo']?>";
		
		if(IsInvoiceEdit == 0)
		{
			document.getElementById('voucher_no_'+FieldCount).readOnly=true;
		}

		// if id is not expense only then uncheck the expense only checkbox and call Paid 	
		var GrpId = "<?php echo $PopupDetails[$i]['group_id']?>";
		if(GrpId != 4)	
		{
			document.getElementById('ExpenseOnly').checked=false;
			PaidToChanged(document.getElementById('Expence_by_'+FieldCount));
		}
		var exo = document.getElementById('Expence_by_'+FieldCount).value="<?php echo $PopupDetails[$i]['id']?>";
		
		if(IsInvoiceEdit == 0)
		{
			console.log("inside the InvoiceEdit");
			document.getElementById('Expence_by_'+FieldCount).readOnly=true;
		}
		//
		
		
		var InvAmountValue=document.getElementById('invoice_amount_'+FieldCount).value="<?php echo $PopupDetails[$i]['InvoiceChequeAmount']?>";
		
		if(IsInvoiceEdit == 0)
		{
			document.getElementById('invoice_amount_'+FieldCount).readOnly=true;
		}
		
		CGSTValue=document.getElementById('CGST_amount_'+FieldCount).value="<?php echo $PopupDetails[$i]['CGST_Amount']?>";
		if(IsInvoiceEdit == 0)
		{
			document.getElementById('CGST_amount_'+FieldCount).readOnly=true;
		}
		
		SGSTValue=document.getElementById('SGST_amount_'+FieldCount).value="<?php echo $PopupDetails[$i]['SGST_Amount']?>";
		if(IsInvoiceEdit == 0)
		{
			document.getElementById('SGST_amount_'+FieldCount).readOnly=true;
		}
		//   New Added COde 
		
		if(IsInvoiceEdit == 0)
		{
			document.getElementById('IGST_amount_'+FieldCount).readOnly=true;
		}
		IGSTValue=document.getElementById('IGST_amount_'+FieldCount).value="<?php echo $PopupDetails[$i]['IGST_Amount']?>";
		
		if(IsInvoiceEdit == 0)
		{
			document.getElementById('RoundOff_amount_'+FieldCount).readOnly=true;
		}
		RoundOffValue=document.getElementById('RoundOff_amount_'+FieldCount).value="<?php echo $PopupDetails[$i]['RoundOffAmount']?>";
		
		
		// Added New Condition 
		if(CGST_Amt != 0 && SGST_Amt != 0)
		{
			//console.log("CGST FLage"+FieldCount);
			IGST_flg = 0;
			document.getElementById('IGST_Flag_'+FieldCount).value= 0;
			//document.getElementById('apply_IGST_'+FieldCount).checked= false;
			document.getElementById('SGST_amount_'+FieldCount).disabled =false;
			document.getElementById('CGST_amount_'+FieldCount).disabled =false;
			document.getElementById('IGST_amount_'+FieldCount).disabled =true;
			//document.getElementById('CESS_amount_'+FieldCount).disabled =true;
			
		}
		//if(IGST_Amt != 0 && CESS_Amt != 0 )
		if(IGST_Amt != 0 )
		{
			IGST_flg=1;
		
			document.getElementById('IGST_Flag_'+FieldCount).value= 1;
			document.getElementById('IGST_amount_'+FieldCount).disabled =false;
			document.getElementById('SGST_amount_'+FieldCount).disabled =true;
			document.getElementById('CGST_amount_'+FieldCount).disabled =true;
		}
		
		
		
		
		document.getElementById('Expence_by_'+FieldCount).value="<?php echo $PopupDetails[$i]['id']?>";
		if(IsInvoiceEdit == 0)
		{
			document.getElementById('Expence_by_'+FieldCount).readOnly=true;
		}
		
		//
		
	    document.getElementById('Doc_statusID_'+FieldCount).value="<?php echo $PopupDetails[$i]['InvoiceStatusID']?>";
		
		
		var InvoiceAmountValue=InvAmountValue-TDSAMount;
		var GrossAmountValue=0;
		//console.log("ROundOff Amt",InvoiceAmountValue);
		if(inputSGST !=0 && inputCGST !=0 && IGST_flg ==0)
		{
			GrossAmountValue=InvAmountValue-CGSTValue-SGSTValue-RoundOff_Amt;
			document.getElementById('gross_amount_'+FieldCount).value=GrossAmountValue.toFixed(2);
			if(IsInvoiceEdit == 0)
			{
				document.getElementById('gross_amount_'+FieldCount).readOnly=true;
			}
			//
		}
		else if(inputIGST !=0 && IGST_flg ==1)
		{
			
			GrossAmountValue=InvAmountValue-IGSTValue-RoundOff_Amt;
			document.getElementById('gross_amount_'+FieldCount).value=GrossAmountValue.toFixed(2);
			if(IsInvoiceEdit == 0)
			{
				document.getElementById('gross_amount_'+FieldCount).readOnly=true;
			}
		}
		/*else if(RoundOff_Amt !=0)
		{
			console.log("ROundOff Amt",);
			GrossAmountValue=InvAmountValue-RoundOff_Amt;
			document.getElementById('gross_amount_'+FieldCount).value=GrossAmountValue.toFixed(2);
			if(IsInvoiceEdit == 0)
			{
				document.getElementById('gross_amount_'+FieldCount).readOnly=true;
			}
		}*/
		else
		{
			
			GrossAmountValue=0;
			document.getElementById('gross_amount_'+FieldCount).value=GrossAmountValue.toFixed(2);
			if(IsInvoiceEdit == 0)
			{
				document.getElementById('gross_amount_'+FieldCount).readOnly=true;
			}
			//
		}
		 document.getElementById("net_payable_amount_"+FieldCount).value =InvoiceAmountValue; 
		 if(IsInvoiceEdit == 0)
		{
			document.getElementById("net_payable_amount_"+FieldCount).readOnly=true;
		}
		
		document.getElementById('invoice_raised_voucher_no_'+FieldCount).value = '<?php echo $PopupDetails[$i]['InvoiceStatusID']?>';
		
<?php }?>
		
		if(IsInvoiceEdit == 1)
		{
			document.getElementById('sub').value = 'Update';	
		}
		

		
		
		bNewRow = true;
		setDatePicker('invoice_date_'+FieldCount);
		AddValues();
		//OpenWin = this.open('VoucherEdit.php?Vno=<?php //echo $PopupDetails[$i]['InvoiceRaisedVoucherNo']?>&pg=');
    });
	//function OnChecked()
	//{
	//	var iExpenseOnly = document.getElementById("ExpenseOnly").checked;
		//alert(iExpenseOnly);
	//	
	//}

 </script>


<br>
<div style="border-bottom:1px solid black;"></div>
<table style="width:100%;text-align:left;" class="totalTable">
						<tbody style="text-align: left;">
                         
						<tr>
                      
							<td rowspan="9" width="60%">
                            <table width="100%">
                         	<tr><td align="left" style="border: 0px solid grey;"><b style="padding-left: 6px;">Comments :</b></td></tr>
                            <tr>
                            <td colspan="5" align="center" style="border: 0px solid grey;">
                            <?php if(isset($_REQUEST['note']))
							{?>
                            <textarea id="Note" name="Note" rows="4" cols="85"><?php echo $_REQUEST['note'];?></textarea>
                            <?php
                            }
							else
							{?>
								<textarea id="Note" name="Note" rows="4" cols="85" ><?php echo $fetchComment[0]['Note'];?></textarea>
								<?php }
							?>
                            </td></tr></table>
							</td>
							<th>Amount Payable</th>
							<th id="mainTotal" style="text-align: right;width: 100px;"><lable id="total_amount">0</lable></th>
						</tr>
					<!--	<tr>
							<td style="text-align: left;">Invoice Total </td>
							<td id="Invoicetotal" style="text-align: right;width: 100px;" ></td>
						</tr>-->
                        <!--<tr>
							<td style="text-align: left;">IGST Total </td>
							<td style="text-align: right;width: 100px;"><lable id="tds_total">0</lable></td>
						</tr>

						<tr>
							<td style="text-align: left;">CGST Total </td>
							<td style="text-align: right;width: 100px;"><lable id="tds_total">0</lable></td>
						</tr>
                                                
                        <tr>
							<td style="text-align: left;">SGST Total </td>
							<td style="text-align: right;width: 100px;"><lable id="tds_total">0</lable></td>
						</tr>
                        
                        <tr>
							<td style="text-align: left;">CESS Total </td>
							<td style="text-align: right;width: 100px;"><lable id="tds_total">0</lable></td>
						</tr>
-->                       <!-- <tr>
							<td style="text-align: left;">TDS Total </td>
							<td id="TDStotal" style="text-align: right;width: 100px;"></td>
						</tr>-->

                        <tr>
							<td style="text-align: left;">Cheque Amount</td>
							<td id="cheque_amount" style="text-align: right;width: 100px;"></td>
						</tr>
						<tr>
							<td style="text-align: left;">Difference Total </td>
							<td id="diff_amount" style="text-align: right;width: 100px;"></td>
						</tr>
						
						<!--<tr>
							<th>Total GST</th>
							<th style="text-align: right;"><lable id="total_gst">0</lable></th>
						</tr>
						<tr>
							<th>Total Amount Payable</th>
							<th style="text-align: right;"><lable id="total_payable_amount">0</lable></th>
						</tr>-->
						</tbody>
					</table>
                    
                    
</td></tr>
<tr><td style="border-top:1px solid black;"><br></td></tr>

<tr><td colspan="4" align="center">
<?php if($_REQUEST['grpID'] == '4')
{?>
<input type="submit" value="Submit" id="sub" class="btn" onClick="SubmitEntry();" style="background-color: #337ab799;border-color:#eee; cursor: no-drop;" disabled>&nbsp;&nbsp;
<?php }
else 
    {?>
	<input type="submit" value="Submit" id="sub" class="btn" onClick="SubmitEntry();">
	
<?php }?>

<input type="submit" value="Cancel" class="btn" id="Cancle"  onclick="javascript:window.close()">

</td></tr>
<tr><td><br></td></tr>
</table>
</div>

</body>

<script>

var GroupID='<?php echo $_REQUEST['grpID']?>';
//alert(GroupID);
var Amount=document.getElementById('chequeamouunt').value;
var rowCounter=1;
  	function AddValues(addRowFlag, checkboxNo)
	{
		 var GroupID= document.getElementById("grpID").value;
		
	 Amount= document.getElementById('chequeamouunt').value;
		var Credit=0;
		var Debit=0;
		var TDS=0;
		var RoundOffAmt =0;
		if(Amount!="")
		{
			Debit += parseFloat(Amount);
		}
		if(checkboxNo != null)
		{
			if(document.getElementById("select_invoice_" + checkboxNo).checked == false)
			{
				alert("Note: Payment voucher would be de-linked from Invoice voucher. Invoice voucher would be retained but TDS (if any) voucher would be deleted.");
				document.getElementById('delete_'+checkboxNo).style.display = 'block';
				
			}
			else
			{
				document.getElementById('delete_'+checkboxNo).style.display = 'none';
			}
		}
		for(var i=1;i<=FieldCount;i++)
		{
		var invoiceTotal=0;
		var GrossTotal=0;
		var CGSTTotal=0;
		var SGSTTotal=0;
		var IGSTTotal=0;
		//var CESSTotal=0;
		var TDSTotal=0;
		var RoundOffTotal =0;
		var Checkbox = document.getElementById("select_invoice_" + i).checked;
		var inStateFlage = document.getElementById("IGST_Flag_" + i).value;
		
		if(Checkbox == true && GroupID == 4)
		{
			document.getElementById("sub").disabled = true;
	     	document.getElementById("sub").style='background-color: #337ab799';
		}
		else if(Checkbox == true && GroupID != 4)
		{
			 document.getElementById("sub").disabled = false;
			 document.getElementById("sub").style='background-color: #337ab7';
			 document.getElementById("sub").style='cursor: pointer';
			 document.getElementById("sub").style='border-color:#2e6da4';
		}
		else if((Checkbox == false &&  GroupID == 4 ) ||(Checkbox == false &&  GroupID != 4 ) )
		{
			 document.getElementById("sub").disabled = false;
			 document.getElementById("sub").style='background-color: #337ab7';
			 document.getElementById("sub").style='cursor: pointer';
			 document.getElementById("sub").style='border-color:#2e6da4';
			
			
		}
	
		
		if(document.getElementById("select_invoice_" + i).checked==true)
		{    
		  // condotion for if set default input cgst ledger.goting to if codition. and total of invoice amount.
		  if(document.getElementById("RoundOff_amount_"+i).value !="")
			{
				
				RoundOffAmt +=parseFloat(document.getElementById("RoundOff_amount_"+i).value);
				RoundOffTotal =parseFloat(document.getElementById("RoundOff_amount_"+i).value);
			}
			 const apply_IGST = document.getElementById("apply_IGST_" + i);
			if(inputSGST !=0 && inputCGST !=0 && inStateFlage == 0)
			{
				 if(document.getElementById('gross_amount_'+i).value !="")
				{
					GrossTotal=parseFloat(document.getElementById('gross_amount_'+i).value);
				}
				if(document.getElementById('CGST_amount_'+i).value !="")
				{
					CGSTTotal=parseFloat(document.getElementById('CGST_amount_'+i).value);
				}
				if(document.getElementById('SGST_amount_'+i).value!="")
				{
			 		SGSTTotal=parseFloat(document.getElementById('SGST_amount_'+i).value);
				}
				
			 invoiceTotal = GrossTotal +  CGSTTotal + SGSTTotal+RoundOffTotal ;
			}
			else if(inputIGST !=0  && inStateFlage == 1)
			{
				
				 if(document.getElementById('gross_amount_'+i).value !="")
				{
					GrossTotal=parseFloat(document.getElementById('gross_amount_'+i).value);
				}
				if(document.getElementById('IGST_amount_'+i).value !="")
				{
					IGSTTotal=parseFloat(document.getElementById('IGST_amount_'+i).value);
				}
				
				
				invoiceTotal = GrossTotal +  IGSTTotal+ RoundOffTotal ;
				
			}
			else
			{
				// if not set default input ledger switch in else condition check to invoice amount set net payable 
					//document.getElementById('gross_amount_'+i).value ;
					///document.getElementById('CGST_amount_'+i).value  ="0.00";
					//document.getElementById('SGST_amount_'+i).value ="0.00";
					
				 if(document.getElementById('invoice_amount_'+i).value !="")
				 {
					invoiceTotal =parseFloat(document.getElementById('invoice_amount_'+i).value);	
				 }
				 else
				 {
					invoiceTotal = 0;
				 }
				
			}
			
			if(invoiceTotal !="")
			{
				document.getElementById("invoice_amount_"+i).value =invoiceTotal.toFixed(2); 
			}
			if(document.getElementById("TDS_amount_" + i).value!="")
			{
				TDSTotal=parseFloat(document.getElementById("TDS_amount_" + i).value);
			}
			
			var PayableAmount=invoiceTotal-TDSTotal;
			
			document.getElementById("net_payable_amount_"+i).value =PayableAmount.toFixed(2); 
			
			
		 if(document.getElementById("invoice_amount_" + i).value!="")
		  {		
		  	Credit += parseFloat(document.getElementById("invoice_amount_" + i).value);
		  }
		 if(document.getElementById("TDS_amount_" + i).value!="")
		  {
			TDS += parseFloat(document.getElementById("TDS_amount_" + i).value);
		  }
		}
		
	   }
	  
	 // console.log(RoundOffAmt);
		//let RoundAmt = num.toFixed(2);
		var TotalAmount=Credit-TDS;
//		console.log("Credit ::"+Credit+ "TDS"+TDS);
		var DiffAmount=TotalAmount-Debit;
		var MainTotal=TotalAmount;
		//Credit = Credit+RoundOffAmt;
		document.getElementById("TDStotal").innerHTML='<b>' + TDS.toFixed(2) + '</b>';
		document.getElementById("Invoicetotal").innerHTML='<b>' + Credit.toFixed(2) + '</b>';
		document.getElementById("cheque_amount").innerHTML= '<b>' +Debit.toFixed(2) + '</b>';
		document.getElementById("diff_amount").innerHTML= '<b>' +DiffAmount.toFixed(2) + '</b>';
		document.getElementById("mainTotal").innerHTML= '<b>' +MainTotal.toFixed(2) + '</b>';
		if( Debit.toFixed(2)!=TotalAmount.toFixed(2) )
		{
			
			document.getElementById("diff_amount").style.backgroundColor = '#F70D1A';
			
			//alert("Note: Invoice will be unlinked and TDS will be deleted but Invoice won't be deleted");
			//if(checkForEmptyRowAlreadyExists(FieldCount) == true)
			{
			if(addRowFlag==true)
			 {
			   AddNewRow();
			   
			 }
			 //showbutton(false);
			
			}
		
			//document.getElementById('submit').style.backgroundColor = 'lightgray';
			return false;
		}
		else
		{
			
			document.getElementById("diff_amount").style.backgroundColor = '#7FE817';
			//document.getElementById('sub').disabled=false;
			//document.getElementById('maxrows').value=FieldCount;
			
			//showbutton(true);
			return true;
		}
		
	}
function Showmasg()
{
	alert("Please set the Input( CGST and SGST) on the defalt page!");	
}
function Showmasg1()
{
	alert("Please set the Input( IGST ) on the defalt page!");	
}
function editpayment(voucherNo)
{
 if(voucherNo != '')
 {
  	OpenWin = this.open('VoucherEdit.php?Vno='+voucherNo+'&pg=');
 }
}

</script>

</html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - TDS Reports</title>
</head>

<?php include_once("includes/head_s.php");
 include_once("classes/dbconst.class.php");
 include_once "classes/utility.class.php";
include_once("classes/FixedDeposit.class.php");
 ?>
<?php
include_once("classes/view_ledger_details.class.php");
//error_reporting(7);

$obj_ledger_details = new view_ledger_details($m_dbConn);
$m_objUtility = new utility($m_dbConn);
$sHeader = $m_objUtility->getSocietyDetails();

$obj_FixedDeposit = new FixedDeposit($m_dbConn);
$fdAccountArray = $obj_FixedDeposit->FetchFdCategories();
$bIsFdCategory = false;
//$ledgerName = $m_objUtility->getLedgerName($_REQUEST['lid']);




if(isset($_POST['st_date']) && isset($_POST['end_date']))
{
	
	$ledgerID = $_POST['lId'];
	$grpID = $_POST["grpid"];
	$st_Date = $_POST['st_date'];
	$endDate = $_POST['end_date'];
	$BankId  = $_POST['bankID'];
	//$first_date_find=strtotime(date("Y-m-d", strtotime($_POST['st_date'])));
	$first_date = getDBFormatDate($_POST['st_date']);
	$get_details = $obj_ledger_details->details($_POST["grpid"],$ledgerID, getDBFormatDate($_POST['st_date']),  getDBFormatDate($_POST['end_date']));

}

else if(isset($_REQUEST['ckdate']) )
{
	
	$first_date_find = strtotime(date("Y-m-d", strtotime($_REQUEST['ckdate'])) . ", first day of this month");
	$first_date = date("Y-m-d",$first_date_find);
	$last_date_find = strtotime(date("Y-m-d", strtotime($_REQUEST['ckdate'])) . ", last day of this month");
	$last_date = date("Y-m-d",$last_date_find);
	$endDate1 =strtotime(date("d-m-Y", strtotime($_REQUEST['ckdate'])) . ", last day of this month");
    $endDate = date("d-m-Y",$endDate1);
	
	$st_Date = $_REQUEST['ckdate'];
	$ledgerID =$_REQUEST['lid'];
	$grpID = $_REQUEST["gid"];
	$BankId = $_REQUEST['bankid'];
	$get_details = $obj_ledger_details->details($grpID,$ledgerID,$first_date , $last_date);
	
}
else if(isset($_REQUEST['stDate']) && isset($_REQUEST['endDate']))
{
	
	$ledgerID = $_REQUEST['lid'];
	$grpID = $_REQUEST["gid"];
	$st_Date = $_REQUEST['stDate'];
	$endDate = $_REQUEST['endDate'];
	$BankId = $_REQUEST['bankid'];
	//$first_date_find=strtotime(date("Y-m-d", strtotime($_POST['st_date'])));
	$first_date = getDBFormatDate($_REQUEST['stDate']);
	$get_details = $obj_ledger_details->details($grpID,$ledgerID, getDBFormatDate($_REQUEST['stDate']),  getDBFormatDate($_REQUEST['endDate']));
	//print_r($get_details);
}
else
{
	
	$ledgerID =$_REQUEST['lid'];
	$grpID = $_REQUEST["gid"];
	$BankId = $_REQUEST['bankid'];
	$get_details = $obj_ledger_details->details($grpID,$ledgerID, "", "");

}
//echo "Test".$ledgerID;
$ledgerName = $m_objUtility->getLedgerName($ledgerID);
//echo "Test1";
	$data = $get_details;	
	//var_dump($get_details);	
?>

<?php

 if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else
{
	include_once("includes/head_s.php");
}

?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<link href="css/messagebox.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/ajax.js"></script>      
    <script type="text/javascript" src="js/jsViewLedgerDetails.js"></script>      
	<script type="text/javascript" src="js/ajax_new.js"></script> 
	<script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
    <script type="text/javascript">
	minStartDate = '<?php  echo getDisplayFormatDate($_SESSION['default_year_start_date']);?>';
	maxEndDate = '<?php  echo getDisplayFormatDate($_SESSION['default_year_end_date']);?>';
	$(function()
	{
		$.datepicker.setDefaults($.datepicker.regional['']);
		$(".basics").datepicker({ 
		dateFormat: "dd-mm-yy", 
		showOn: "both", 
		buttonImage: "images/calendar.gif",
		buttonImageOnly: true ,
		minDate: minStartDate,
		maxDate: maxEndDate
	})});
	
	
	
    </script>
	
	
   
 <style>
 @media print {
  /* style sheet for print goes here */
  .PrintClass
  {
		display:block;
   }
}

 </style>   
</head>
<body>
<center>
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

<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">TDS Report </div>
        <br>
		<div style="padding-left: 15px;padding-bottom: 10px;"><button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;" id="btnBack"><i class="fa  fa-arrow-left"></i></button>
		 <center> 
         
		
<br>     
<!--<div style="width:100%;font-size: 14px;"><b><?php //echo $first_date ?> to <?php //echo $last_date ?></b></div>-->
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<table align='center' border="0" style="width:45%">
<tr>
<td style="line-height: 20px;"><b>From </b></td>
<td style="line-height: 20px;"><b>&nbsp; : &nbsp;</b></td>
<td><input type="text" name="st_date" id="st_date" class="basics" size="8" value="<?php echo $st_Date?>"  style="width:100px;" readonly/></td>
<td style="line-height: 20px;"><b>To </b></td>
<td style="line-height: 20px;"><b>&nbsp; : &nbsp;</b></td>
<td><input type="text" name="end_date" id="end_date" class="basics" size="8"  value="<?php echo $endDate?>" style="width:100px;" readonly/></td>
<td><input type="submit" class="btn btn-primary" style="padding: 2px 12px;" id="fetch" value="Fetch" onClick=""></td>
</tr>
</table>
<input type="hidden" id="grpid" name="grpid" value="<?php echo $grpID ?>">
<input type="hidden" id="lId" name="lId" value="<?php echo $ledgerID ?>">
<input type="hidden" id="bankID" name="bankID" value="<?php echo $BankId ?>">
</form>
<hr>
<?php echo $data[$k]['VoucherID'];?>
<div style="width:100%;">
<form method="post" action="Display_challan.php" target="_blank" onSubmit="return val();">
<center><div style="width:100%"><!--<button type="button" class="btn btn-primary"  disabled id="createChallan" style="background-color:#337ab77a" onClick="CreateChallan()">Create Challan</button>-->
<input type="submit" class="btn btn-primary"  disabled id="createChallan" style="background-color:#337ab77a" value="Create Challan" >
<button type="button" class="btn btn-primary"  id="createChallan"  onclick="location.href ='Challan_List.php'">Challan List</button>

 </div></center>

<br>

<input type="hidden" id="TDS_Payable_Id" name="TDS_Payable_Id" value="<?php echo $ledgerID?>" >

<table id="example" class="display" cellspacing="0" width="100%" >
	<thead>
        <tr>
        	<th >&nbsp;&nbsp;</th>
           	<th style="text-align: center;">Name of Deductee </th>
            <th style="text-align: center;">PAN of Deductee</th>
            <th style="text-align: center;">TDS Head/Section</th>
            <th style="text-align: center;">Date of Invoice/Payment</th>
            <th style="text-align: center;">Gross Amount</th>
            <th style="text-align: center;">TDS %</th>
            <th style="text-align: center;">TDS Deducted</th>
            <th style="text-align: center;">Challan Ref No.</th>
            <th style="text-align: center;">Challan Genretated</th>
            
        </tr>
    </thead>
  
   	<tbody>  
        <?php
		$BalanceAmt=0;
		$Tds_Amount=0;
		
		if($data<>"")
		{		$cnt =1;	
				foreach($data as $k => $v)
				{
					
					if(isset($data[$k]['id'])){
					
					/*$categoryid=$obj_ledger_details->obj_utility->getParentOfLedger($data[$k]['id']);
					
					
					if($categoryid['category']==BANK_ACCOUNT || $categoryid['category']== CASH_ACCOUNT )
					{ 
							$CreditAmt = $data[$k]['Debit'];
							$DebitAmt = $data[$k]['Credit'];	
							
							
								if($DebitAmt <> 0 )
								{
									$BalanceAmt += $DebitAmt;
								}
								
								if($CreditAmt <> 0)
								{
									$BalanceAmt -= $CreditAmt;
								}																	
							
					
					}
					else
					{*/
						$DebitAmt = $data[$k]['Debit'];
						$CreditAmt = $data[$k]['Credit'];
						
						$BalanceAmt = $BalanceAmt + $DebitAmt - $CreditAmt;
					//}
				?>
                <?php 
				
					
					$finalData =  $obj_ledger_details->get_voucher_details($data[$k]['VoucherTypeID'],$data[$k]['VoucherID'],$grpID,$ledgerID, "By");
					
					$categoryid=$obj_ledger_details->obj_utility->getParentOfLedger($finalData[0]['ledger_id']);
					
										if($categoryid['category']!=BANK_ACCOUNT && $categoryid['category']!= CASH_ACCOUNT ){
											
											
			
				$InvoiceData = $obj_ledger_details->GetInvoiceLedger($finalData[0]['VoucherNo']);
				//var_dump($finalData);
				?>
                <?php if($DebitAmt <> 0 && $CreditAmt ==0)
					{
						$Tds_Amount = $DebitAmt;
					}
					else if($CreditAmt <> 0 && $DebitAmt==0 )	
					{
						$Tds_Amount = $CreditAmt;
					}
					if($data[$k]['ChallanID'] == 0)
					{
						$checkboxDisbled = '';
					}
					else
					{
						$checkboxDisbled = 'disabled';
					}
					$natureOfTDS ="";
					 if($finalData[0]['Tds_Head']=='')
					 {
						 $natureOfTDS= "";
						}
					 else
					 {
						$natureOfTDS=  $finalData[0]['Tds_Head'];
					 }
					?>	
				<tr id="tr_<?php echo $data[$k]['VoucherID']; ?>">
                <td style="text-align:center" id='chk_<?php echo $k?>'><input type="checkbox" class="TDSCheckbox" id="check_Tds_<?php echo $k?>" value='<?php echo  $finalData[0]['ledger_id'] ?>'  onClick='SelectLeger(<?php echo $data[$k]['VoucherID']?>,<?php echo $Tds_Amount ?>,<?php echo $cnt ?>,"<?php echo $natureOfTDS?>","<?php echo $finalData[0]['ledger_name']?>","<?php echo $data[$k]['Date'] ?>",this);' <?php echo $checkboxDisbled ?>></td>
                	<td style="text-align:center">
					<?php
						
						//$Get_
						echo $finalData[0]['ledger_name'];
						//print_r($finalData);
						?>
                    </td>
                    <td style="text-align:center"><?php if($finalData[0]['ledger_pan']==''){echo '---';}else{echo $finalData[0]['ledger_pan'];}?></td>
					<td style="text-align:center"><?php if($finalData[0]['Tds_Head']==''){echo '---';}else{echo $finalData[0]['Tds_Head'];}?></td>
                    <td style="text-align:center"><?php echo getDisplayFormatDate($data[$k]['Date']);?></td>         
                    <td style="text-align:center"><?php echo $InvoiceData[0]['InvoiceChequeAmount']?><input type="hidden" id="bankID" name="bankID" value="<?php echo $InvoiceData[0]['BankID']?>"></td>
                     <td style="text-align:center;"><?php if($finalData[0]['TDS_Ded_rate']==''){echo '---';}else{echo $finalData[0]['TDS_Ded_rate'];}?></td>
                    
								
					<td style="text-align:center"><?php echo $Tds_Amount ?></td>
                    <td style="text-align:center"><?php echo $data[$k]['ChallanID'];?></td>
                    <td style="text-align:center"><?php if($data[$k]['ChallanID']==0){echo 'NO';}else{echo 'YES';}?></td>
					
                    
                	
                </tr>
				 <?php
				 $cnt++;
				}
				}
				}
				?>
<!--<script>document.getElementById('BalanceAmount').innerHTML = format(<?php echo $BalanceAmt?>,2)</script> 	-->
 <?php                
		}
		
		else
		{
			?>
            
            <?php	
		}
		?>
        
</tbody>
</table>
</div>
</center>
</div>
<input type="hidden" id="NatureOfTDS" name="NatureOfTDS" value="0" >
<input type="hidden" id="total_amount" name="total_amount" value="0">
<input type="hidden" id="tdsId" name="tdsId" value="<?php echo $ledgerID?>">
<input type="hidden" id="gid" name="gid" value="<?php echo $grpID?>">
<input type="hidden" id="fdate" name="fdate" value="<?php echo $first_date ?>" >
<input type="hidden" id="data_arr" name="data_arr" value="" >
<input type="hidden" id="from_date" name="from_date" value="<?php echo $st_Date?>">
<input type="hidden" id="to_date" name="to_date" value="<?php echo $endDate?>">
</div>	
</form>
<?php include_once "includes/foot.php"; ?>




<script>
var Data_arr = [];
var TotalAmount= 0;
function SelectLeger(vId,Amount,cnt,nature,LedgerName,VDate,elementObj)
{
	
	//var TotalAmount= 0;
	if($('#'+elementObj.id).is(':checked')){
		
		//document.getElementById("createChallan").disabled=false;
		//document.getElementById("createChallan").style.backgroundColor='#337ab7';
		 Data_arr.push({"VoucherID":vId,"VoucherLedgerID":elementObj.value,"Cnt": cnt,"NatureOfTDS":nature,"LedgerName":LedgerName,"VDate":VDate,"Amount":Amount});
		 
		TotalAmount = TotalAmount+Amount;
	}
	else
	{
		//document.getElementById("createChallan").style.backgroundColor='#337ab77a';
		//document.getElementById("createChallan").disabled=true;
		//removeAllInstances(Data_arr);
		//console.log({"VoucherID":vId,"VoucherLedgerID":elementObj.value});
		var val =({"VoucherID":vId,"VoucherLedgerID":elementObj.value,"Cnt": cnt,"NatureOfTDS":nature,"LedgerName":LedgerName,"VDate":VDate,"Amount":Amount});
		index = Data_arr.findIndex(x => x.Cnt ===cnt);

//console.log(index);
		//var index = _.findIndex(Data_arr, {"VoucherID":vId,"VoucherLedgerID":elementObj.value,"BankID":bankid})
		//var keyIndex = Data_arr.indexOf({"VoucherID":vId,"VoucherLedgerID":elementObj.value,"BankID":bankid});
		//indexof(Data_arr,val);
		//debugger;
		//console.log('key index', keyIndex);
		
		if(index > -1)
		{
			
			Data_arr.splice(index, 1);
		}
		
		}
		// console.log("pop data",Data_arr);
	//alert(checkedCount);
	document.getElementById('total_amount').value =TotalAmount;
	document.getElementById('data_arr').value =JSON.stringify(Data_arr);
	GetButtonAction();
}
function GetButtonAction()
{  
	var freezYear = '<?php echo $_SESSION['is_year_freeze']?>' ;
	if(freezYear == 0)
	{
		if(Data_arr.length > 0)
		{
			document.getElementById("createChallan").disabled=false;
			document.getElementById("createChallan").style.backgroundColor='#337ab7';
		}
		else
		{
			document.getElementById("createChallan").style.backgroundColor='#337ab77a';
			document.getElementById("createChallan").disabled=true;
		}
	}
	else
	{
		document.getElementById("createChallan").style.backgroundColor='#337ab77a';
		document.getElementById("createChallan").disabled=true;
	}
}
function indexof(arr, val) {
	
  var i;
  console.log(arr);
  console.log(val);
  while ((i = arr.indexOf(val)) != -1) {
    arr.splice(i, 1);
  }
  
 // console.log("Final",arr);
}


function val()
{
	//alert("test");
	console.log(Data_arr);
	var previousData='';
	var natureofTDSArr=[];
	for(var i =0; i < Data_arr.length; i++)
	{
		
		var natureofTDS = trim(Data_arr[i]['NatureOfTDS']);
		natureofTDSArr.push(natureofTDS);
		if(natureofTDS == '')
		{
			alert("Please update Nature of TDS from Leadger update page");
			window.open("vendor.php?type=open", '_blank');
			return false;
		}
}
var unique = natureofTDSArr.filter(onlyUnique);
console.log(unique);
console.log("lenght",unique.length);
if(unique.length > 1 )
{
	alert("Challan not create please select same TDS Head");
	return false;
}
document.getElementById('NatureOfTDS').value=unique[0];
function LTrim( value )
{
	var re = /\s*((\S+\s*)*)/;
	return value.replace(re, "$1");
}
function RTrim( value )
{
	var re = /((\s*\S+)*)\s*/;
	return value.replace(re, "$1");
}
function trim( value )
{
	return LTrim(RTrim(value));
}		
}
function onlyUnique(value, index, self) 
{
  return self.indexOf(value) === index;
}
</script>



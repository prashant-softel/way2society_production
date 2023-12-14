<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Ledger Report</title>
</head>

<?php include_once("includes/head_s.php");
 include_once("classes/dbconst.class.php");
 include_once "classes/utility.class.php";
include_once("classes/FixedDeposit.class.php");
 ?>
<?php
include_once("classes/view_ledger_details.class.php");
$m_dbConnRoot = new dbop(true);
$obj_ledger_details = new view_ledger_details($m_dbConn);
$m_objUtility = new utility($m_dbConn, $m_dbConnRoot);
$sHeader = $m_objUtility->getSocietyDetails();

$obj_FixedDeposit = new FixedDeposit($m_dbConn);
$fdAccountArray = $obj_FixedDeposit->FetchFdCategories();
$bIsFdCategory = false;
$showLogAllowedTable = array_keys($logModulesArr); // logModulesArr set in dbconst.class.php
$ledgerName = $m_objUtility->getLedgerName($_REQUEST['lid']);
$loginDetails = $m_objUtility->getSocietyAllLoginDetails(false,false, true);
// Lock buttons freez year 
$btnDisplay = "block";
if($_SESSION['is_year_freeze'] == 0)
{
	$btnDisplay = "block";
}
else
{
	$btnDisplay = "none";
}

$societyCreationDate = $m_objUtility->getSocietyCreatedOpeningDate();
$minDate = getDisplayFormatDate($societyCreationDate);
$maxDate = getDisplayFormatDate($m_objUtility->getMaxDate());

$from_date = getDBFormatDate($_SESSION['from_date']);
$to_date = getDBFormatDate($_SESSION['to_date']);
if(isset($_POST['from']) && isset($_POST['to']))
{
	$from_date = getDBFormatDate($_POST['from']);
	if($from_date < getDBFormatDate($minDate))
	{
		$from_date = getDBFormatDate($minDate);
		?>
			<script language="javascript" type="application/javascript">
			var message = "Date can't be less than <?php echo json_encode($minDate); ?> !!";
			alert(message);
			</script>
		<?php
	}
	$to_date = getDBFormatDate($_POST['to']);
}

else if(isset($_SESSION['from_date']) && isset($_SESSION['to_date']) && isset($_REQUEST['dt']))
{
	
	$from_date = getDBFormatDate($_SESSION['from_date']);
	$to_date = getDBFormatDate($_SESSION['to_date']);

	if(isset($_REQUEST['prev'])){
		
		$from_date = date('Y-m-d', strtotime($from_date.' -1 year'));
		$to_date = date('Y-m-d', strtotime($to_date.' -1 year'));
	}

	
}


$get_details = $obj_ledger_details->details($_REQUEST["gid"],$_REQUEST['lid'], $from_date, $to_date, true);

if($_REQUEST["gid"] == 1 || $_REQUEST["gid"] == 2)
{
	
	$arLedgerParentDetails = $m_objUtility->getParentOfLedger($_REQUEST['lid']);
	if(!(empty($arLedgerParentDetails)))
	{
		$LedgerGroupID = $arLedgerParentDetails['group'];
		$LedgerCategoryID = $arLedgerParentDetails['category'];
	}
	if(isset($fdAccountArray))
	{
		if (in_array($arLedgerParentDetails['category'], $fdAccountArray) && $arLedgerParentDetails['group'] == ASSET)
		{
			$bIsFdCategory = true;
		}
	}
	
	if($from_date <> "")
	{

		$res = $m_objUtility->getOpeningBalance($_REQUEST['lid'],$from_date);
		
		if($res <> "")
		{
			if($LedgerCategoryID == BANK_ACCOUNT || $LedgerCategoryID == CASH_ACCOUNT)
			{
				$data[0] = array("id" => $_REQUEST['lid'] , "Date" => $res['OpeningDate'] , "Particular" => $res['LedgerName'] , "Debit" => ($res['OpeningType'] == TRANSACTION_CREDIT) ? $res['Total'] : 0 , "Credit" => ($res['OpeningType'] == TRANSACTION_DEBIT) ? $res['Total'] : 0  , "VoucherID" => 0 , "VoucherTypeID" => 0 , "Is_Opening_Balance" => 1);
			}
			else
			{
				$data[0] = array("id" => $_REQUEST['lid'] , "Date" => $res['OpeningDate'] , "Particular" => $res['LedgerName'] , "Debit" => ($res['OpeningType'] == TRANSACTION_DEBIT) ? $res['Total'] : 0 , "Credit" => ($res['OpeningType'] == TRANSACTION_CREDIT) ? $res['Total'] : 0  , "VoucherID" => 0 , "VoucherTypeID" => 0 , "Is_Opening_Balance" => 1);		
			}
			if($get_details <> "")
			{
				for($i = 0 ; $i < sizeof($get_details); $i++)
				{
					$data[$i + 1] = $get_details[$i];
				}
			}
		}
		else
		{
			$data = $get_details;	
		}
	}
}
else
{
	$data = $get_details;		
}
$IsCreditor=$obj_ledger_details->IsCreditor($_REQUEST['lid']);
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

if(isset($_GET['ssid'])){if($_GET['ssid']<>$_SESSION['society_id']){?><script>window.location.href = "logout.php";</script><?php }}
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<link href="css/messagebox.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/ajax.js"></script>      
    <script type="text/javascript" src="js/jsViewLedgerDetails.js"></script>      
	<script type="text/javascript" src="js/ajax_new.js"></script> 
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
	//show();
	function show()
	{
	//alert("test 1");
	
	//window.location.search = jQuery.query.set("gid",vid);
	<?php if(isset($_GET['id'])){
	
		?> //=document.write(vid);
	//alert("test 2");
document.getElementById("show<?php echo $_GET['id'];?>").innerHTML="<?php echo $obj_ledger_details->details2($_REQUEST['lid'],$_GET['id'],$_GET['vtype']);?>";

	<?php }
	?>		
	}
	
	window.onfocus = function() {
		var result = localStorage.getItem('refreshPage');	
		//alert(result);
		if(result != null && result > 0 )
		{	
			localStorage.setItem('refreshPage', "0");
			location.reload();
		}
	};
		
	var minDate = '<?=$minDate?>';
	var maxDate = '<?=$maxDate?>';

	$(function() {
		$.datepicker.setDefaults($.datepicker.regional['']);
		$(".basics").datepicker({
			dateFormat: "dd-mm-yy",
			showOn: "both",
			buttonImage: "images/calendar.gif",
			buttonImageOnly: true,
			minDate: minDate,
			maxDate: maxDate,
			changeMonth: true,
			changeYear: true,
		})
	});
	</script>
 <style>
 @media print {
  /* style sheet for print goes here */
  .PrintClass
  {
		display:block;
   }
}

#example_wrapper{
		overflow-x: auto;
	}

.logDiv{

	width: 70%;
	overflow: overlay;
    border: 3px solid #2e6da4;
    margin: 20px 0px 10px 0px;
	display: none;
	border-radius: 5px;
}

.diffColor{
	color:red;
}

.date-field {
			height: 30px;
			border-radius: 3px;
			width: 25%;
			text-align: center;
			border: 1px solid;
			margin: 1px;
		}

		.date-td{
			text-align: right;
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
        <div class="panel-heading" id="pageheader">Ledger Report </div>
		<input type="hidden" name="show_log_btn" id="show_log_btn" value="0">
        <br>
		<div style="padding-left: 15px;padding-bottom: 10px;"><button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;" id="btnBack"><i class="fa  fa-arrow-left"></i></button>
		 <center>  <?php
		if($_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1){ 
			
			if($catid['category']!=BANK_ACCOUNT && $catid['category']!= CASH_ACCOUNT  && $_SESSION['is_year_freeze'] == 0 )
			{?>	 
        		<button type="button" class="btn btn-primary" onClick="ShowJV('<?php echo $_REQUEST['lid']; ?>');" style="">Create JV</button>
			<?php 
			 } ?>
        <button type="button" class="btn btn-primary" onClick="ShowBankAccountDetails();" style="">Payment/Receipt </button> </center></div>
        <?php }?>
        <center><font style="font-size:14px;"><b>
        <?php if($_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1){?>
        	<a href="ledger.php?edt=<?php echo $_REQUEST['lid']?>" ><?php echo  $ledgerName;/*$data[0]['Particular']; if($data[1]['owner_name'] <> ""){echo ' - ' .$data[1]['owner_name'] ;}*/?></a>
       <?php }
	   else{ 
		   echo $ledgerName;
		  } ?>
        </b></font></center>  
		<?php  
		if(isset($_SESSION['from_date']) && isset($_SESSION['to_date']) && isset($_REQUEST['dt']))
		{ ?>
		<h5 align="center">FROM <?php echo getDisplayFormatDate($from_date); ?> TO <?php echo getDisplayFormatDate($to_date);?></h5>
		<?php 
		} 
		
		$catid=$obj_ledger_details->obj_utility->getParentOfLedger($_REQUEST['lid']);		
		?>
		<form Name ="form1" Method ="POST">
		<table style="float:center;width: 50%;" id="table_selection">
			<tr>
				<td></td>
			</tr>
			<tr>
			<td></td>
			</tr>

			<tr>
				<td class="date-td"  valign="middle">
					<label for="from" style="color: #23527c;font-size:12px;line-height: 26px;" valign="middle">Date Range  &nbsp; &nbsp;: &nbsp;&nbsp;</label>
					<input type="text" name="from" id="from" class="date-field basics" style="width:25%" placeholder="Start Date" value="<?= getDisplayFormatDate($from_date) ?>">&nbsp;&nbsp;
					<input type="text" name="to" id="to" class="date-field basics" style="width:25%" placeholder="End Date" value="<?= getDisplayFormatDate($to_date) ?>">&nbsp;&nbsp;
				</td>
				<td>
					 <input type="submit" name="btnGo" id="btnGo" value="Submit" valign="middle" class="btn btn-primary" style="height:22px; font-size:12px; padding: 2px 12px; text-align:center;"/>
				</td>
			</tr>

			<tr>
				<td></td>
			</tr>

		</table>
	</form>
<br>     


<?php echo $data[$k]['VoucherID'];?>
<div style="width:100%;">
<table id="example" class="display" cellspacing="0" width="100%" >
	<thead>
        <tr>
        	 <th>Print</th>
        	<th>View</th>
            <?php if($_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1){?>
            <th >Edit</th>
           	<th >Delete</th>
           	<?php }?>
           	<th >Date</th>
            <th >Particular</th>
            <th>Voucher Type</th>
            <th >Voucher No</th>
            <th >Cheque/Bill Number</th>
            <th  class="sum">Debit</th>
            <th  class="sum">Credit</th>
            <th >Balance</th>
            
          <!-- <th style="display:none;"  class="showwhileprint">Note</th>-->
           <th>System No</th>
           <th>Note</th>
		   <th>Added/Edited By</th>
		   <th>Timestamp</th>
        </tr>
    </thead>
    <tfoot class="footerClass">
            <tr style="font-size:12px; ">
            	<?php if($_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1){?>
                <th style="text-align:center;" colspan="11"> Grand Total </th>
                <?php }
				else{ ?>
                <th style="text-align:center;" colspan="9"> Grand Total </th>                
				<?php }?>

                <th style="text-align:right;padding-right: 10px;"></th>
                <th style="text-align:right;padding-right: 10px;"></th>
                <th style="text-align:right;padding-right: 10px;" id="BalanceAmount"></th>
                <th style="text-align:right;"></th>
                <th style="text-align:right;"></th>
              </tr>
        </tfoot>
   	<tbody>  
        <?php
		$BalanceAmt=0;
		if($data<>"")
		{			
				foreach($data as $k => $v)
				{
					if(isset($data[$k]['id'])){
					//$categoryid=$obj_utility->getParentOfLedger($data[$k]['id']);
					$allowShowLogBtn = false;

					
					$loginID = (!empty($data[$k]['LoginID']) && $data[$k]['LoginID'] != 0)?$data[$k]['LoginID']:0;
					$TimeStamp = (!empty($data[$k]['Timestamp']) && strtotime($data[$k]['Timestamp']) != 0)?$data[$k]['Timestamp']:'--';
					
					if(in_array($data[$k]['RefTableID'], $showLogAllowedTable) || ($data[$k]['RefTableID'] == 0 && $data[$k]['VoucherTypeID'] == VOUCHER_JOURNAL)){

						$allowShowLogBtn = true;
					}

					$categoryid=$obj_ledger_details->obj_utility->getParentOfLedger($data[$k]['id']);
					
					$Is_Opening_Balance=$data[$k]['Is_Opening_Balance'];
					//echo 'CATEGORYID'.$categoryid['category'];
					if($categoryid['category']==BANK_ACCOUNT || $categoryid['category']== CASH_ACCOUNT )
					{ 
							$CreditAmt = $data[$k]['Debit'];
							$DebitAmt = $data[$k]['Credit'];	
							
							if($Is_Opening_Balance==1 && $CreditAmt <> 0)
							{
								$BalanceAmt -= $CreditAmt;
							}
							else if($Is_Opening_Balance==1 && $DebitAmt <> 0)
							{
								$BalanceAmt += $DebitAmt;
							}
							else
							{
								if($DebitAmt <> 0 )
								{
									$BalanceAmt += $DebitAmt;
								}
								
								if($CreditAmt <> 0)
								{
									$BalanceAmt -= $CreditAmt;
								}																	
							}
					
					}
					else
					{
						$DebitAmt = $data[$k]['Debit'];
						$CreditAmt = $data[$k]['Credit'];
						$BalanceAmt = $BalanceAmt + $CreditAmt - $DebitAmt;
						//$BalanceAmt = $BalanceAmt + $DebitAmt - $CreditAmt;
					}
				?>
				<tr id="tr_<?php echo $data[$k]['VoucherID']; ?>">
                <?php $voucher_details=$obj_ledger_details->get_voucher_details('',$data[$k]['VoucherID']);
		//var_dump($voucher_details);
                 if($data[$k]['VoucherTypeID']==VOUCHER_PAYMENT || $data[$k]['VoucherTypeID']==VOUCHER_RECEIPT || $data[$k]['VoucherTypeID']==VOUCHER_CONTRA || $data[$k]['VoucherTypeID'] == VOUCHER_JOURNAL || $data[$k]['VoucherTypeID'] == VOUCHER_CREDIT_NOTE || $data[$k]['VoucherTypeID'] == VOUCHER_DEBIT_NOTE)
                 {
					 if($data[$k]['VoucherTypeID'] == VOUCHER_CREDIT_NOTE || $data[$k]['VoucherTypeID'] == VOUCHER_DEBIT_NOTE || $voucher_details[0]['RefTableID'] == TABLE_SALESINVOICE) 
					 {
						$DebitOrCreditOrInvoiceDetails = $obj_ledger_details->getDebitCreditDetails($voucher_details[0]['RefNo'],$voucher_details[0]['RefTableID']);
						
						// pending check whether you can use data array login and timestamp
						$loginID = $DebitOrCreditOrInvoiceDetails[0]['LoginID'];
						$TimeStamp = $DebitOrCreditOrInvoiceDetails[0]['Timestamp'];
					
						if($voucher_details[0]['RefTableID'] == TABLE_SALESINVOICE)
						{
							$InvoicepageUrl = "Invoice.php?UnitID=".$DebitOrCreditOrInvoiceDetails[0]['UnitID']."&inv_number=".$DebitOrCreditOrInvoiceDetails[0]['Inv_Number'];
						}
						else
						{
							$InvoicepageUrl = "Invoice.php?debitcredit_id=".$voucher_details[0]['RefNo']."&UnitID=".$DebitOrCreditOrInvoiceDetails[0]['UnitID']."&NoteType=".$DebitOrCreditOrInvoiceDetails[0]['Note_Type'];
						}
							 
					?>
                    <td align='center' valign='top'><a href="<?php echo $InvoicepageUrl ;?>" target="_blank" ><img src="images/print.png" border='0' alt='Print' style='cursor:pointer;' width="25" height="10" /></a></td>      
                     <?php }else
					 {
						?>
                     
                <td align='center' valign='top'><a href="print_voucher.php?&vno=<?php echo base64_encode($voucher_details[0]['VoucherNo']);?>&type=<?php echo base64_encode($data[$k]['VoucherTypeID']);?>" target="_blank" ><img src="images/print.png" border='0' alt='Print' style='cursor:pointer;' width="25" height="10" /></a></td>
               <?php }
			   }
			   else{ ?>
               	<td align='center' valign='top'></td>
               <?php } ?>
                   <td id="show<?php echo $data[$k]['VoucherID'];?>">
					<?php //$show_details  = $obj_ledger_details->details2($_REQUEST['VoucherID'],$_REQUEST['lid']);
					//echo 'opening balance'.$data[$k]['Is_Opening_Balance'];
					if(($_REQUEST['gid']==1 || $_REQUEST['gid']==2) && $data[$k]['Is_Opening_Balance'] ==1)
					{
						//echo 'Opening Balance';
					
					}
					else 
					{
					?>
                    
					<!--<a href="view_ledger_details.php?lid=<?php //echo $_REQUEST['lid'];?>&gid=<?php  //echo $_REQUEST['gid'];?>&vtype=<?php //echo $data[$k]['VoucherTypeID'];?>&id=<?php //echo $data[$k]['VoucherID'];?>" style="color:#0000FF;"><img src='images/view.jpg' border='0' alt='view' style='cursor:pointer;' width="18" height="15"/></a>-->
					<div onClick="ViewVoucherDetail('<?php echo $_REQUEST['lid'];?>', '<?php echo $_REQUEST['gid'];?>', '<?php echo $data[$k]['VoucherTypeID'];?>', '<?php echo $data[$k]['VoucherID'];?>', '<?php echo $allowShowLogBtn;?>');" style="color:#0000FF;cursor: pointer;"><img src='images/view.jpg' border='0' alt='View' style='cursor:pointer;' width="18" height="15" /></div>
					<?php }?>
                    </td>
                <?php if($_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1){?>
                <td align='center' valign='top'>
					<?php
					if($data[$k]['VoucherTypeID'] == VOUCHER_CREDIT_NOTE || $data[$k]['VoucherTypeID'] == VOUCHER_DEBIT_NOTE || $voucher_details[0]['RefTableID'] == TABLE_SALESINVOICE)
					{
						//echo '<br>URL : '.$InvoicepageUrl;
						$Url = $InvoicepageUrl."&edt";
					?>
						
                        <a href="<?php echo $Url ;?>" target="_blank" > <img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;display:<?php echo $btnDisplay?>'/> </a>							
					
					<?php }
					
					 
                    elseif($data[$k]['VoucherTypeID']==VOUCHER_JOURNAL)
                    {	
						$Url = $obj_ledger_details->CheckVoucherType($data[$k]['VoucherID']);						
						if($Url <> '')
						{							
                        ?>
								<a href="" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes'); return false;"> <img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;display:<?php echo $btnDisplay?>'/> </a>
                        <?php } else{
							//echo "test";?>
							<a  id='edit' href="" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes'); return false;"><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;display:<?php echo $btnDisplay?>'/></a>
                    
                    <?php }
					}
                    else if($data[$k]['VoucherTypeID']==VOUCHER_PAYMENT || $data[$k]['VoucherTypeID']==VOUCHER_RECEIPT || $data[$k]['VoucherTypeID']==VOUCHER_CONTRA)
                    {
						
                       $Url = $obj_ledger_details->generatUrl($data[$k]['VoucherID'],$data[$k]['VoucherTypeID']);					   
                        //Url = "PaymentDetails.php?bankid=".$bankID."&LeafID=".$chequeDetails[0]['ChqLeafID']."&CustomLeaf= ". $chequeDetails[0]['CustomLeaf']. "&edt=".$chkDetailID;		
						//echo "url: ".$Url;
						
						if($Url == false)
						{ ?>
							<a onClick="showWarning('edit');"><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;display:<?php echo $btnDisplay?>'/></a>
						<?php }
						else
						{
                     ?>
                     	 <a href="" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes'); return false; "> <img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;display:<?php echo $btnDisplay?>'/> </a>
                    <?php }
					 }
					else if($data[$k]['Is_Opening_Balance'] == 1)
					{
						 if($bIsFdCategory == true)
						 {
							 $Url = "FixedDeposit.php?edt=".$_REQUEST['lid'];
						 }
						 else
						 {
						 	$Url = "ledger.php?edt=".$_REQUEST['lid'];
						 }
					?>
                    <a href="" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes');  return false;"> <img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;display:<?php echo $btnDisplay?>'/> </a>	
					<?php }
					?>
                </td>
                
                 <td align='center' valign='top'>
                <?php
				if($data[$k]['VoucherTypeID'] == VOUCHER_CREDIT_NOTE || $data[$k]['VoucherTypeID'] == VOUCHER_DEBIT_NOTE || $voucher_details[0]['RefTableID'] == TABLE_SALESINVOICE)
				{ 
					if($voucher_details[0]['RefTableID'] == TABLE_SALESINVOICE)
					{?>
						<img src='images/del.gif' border='0' alt='Edit' onClick="deleteInvoice('<?php echo $voucher_details[0]['RefNo'];?>','<?php echo $DebitOrCreditOrInvoiceDetails[0]['UnitID'];?>','<?php echo $DebitOrCreditOrInvoiceDetails[0]['Inv_Date'];?>')" style='cursor:pointer;display:<?php echo $btnDisplay?>'/>
					<?php }else
					{
				?>
					 <img src='images/del.gif' border='0' alt='Edit' onClick="deleteDebitorCredit('<?php echo $voucher_details[0]['RefNo'];?>','<?php echo $DebitOrCreditOrInvoiceDetails[0]['Note_Type'];?>')" style='cursor:pointer;display:<?php echo $btnDisplay?>'/>
				<?php }
				}
				else if($data[$k]['VoucherTypeID']==VOUCHER_JOURNAL)
				{ 
				
				 $ExistVoucher = $obj_ledger_details->InvoiceStatus($voucher_details[0]['VoucherNo']);
				$Url = $obj_ledger_details->CheckVoucherType($data[$k]['VoucherID']);
					
						if(($Url <> '') && ($ExistVoucher[0]['InvoiceClearedVoucherNo'] > 0 || $ExistVoucher[0]['TDSVoucherNo'] > 0  ))
						{
                        ?>
                        	<a href="" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes');  return false;"> <img src='images/del.gif' border='0' alt='Delete' style='cursor:pointer;display:<?php echo $btnDisplay?>'/> </a>
                        <?php } else {
							    ?>
                <a  id='<?php echo "delete-".$voucher_details[0]['VoucherNo']."-".$_REQUEST["gid"]."-".$_REQUEST['lid']?>'  onClick='deleteVoucher(this.id);'><img src='images/del.gif' border='0' alt='Edit' style='cursor:pointer;display:<?php echo $btnDisplay?>'/></a>
                
				<?php }
				}
				else if($data[$k]['VoucherTypeID']==VOUCHER_PAYMENT || $data[$k]['VoucherTypeID']==VOUCHER_RECEIPT || $data[$k]['VoucherTypeID']==VOUCHER_CONTRA)
				{
					
				   $Url = $obj_ledger_details->generatUrl($data[$k]['VoucherID'],$data[$k]['VoucherTypeID'],true);
				   
				   if($Url == FALSE)
				   {?>
					   <a onClick="showWarning('delete');"> <img src='images/del.gif' border='0' alt='Delete' style='cursor:pointer;display:<?php echo $btnDisplay?>'/> </a>
				  <?php }else
				   {
					   ?>
				 
				 		<a href="" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes');  return false;"> <img src='images/del.gif' border='0' alt='Delete' style='cursor:pointer;display:<?php echo $btnDisplay?>'/> </a>
               	 <?php
				   }
				   
				   
				 
				}?>
                </td>
                <?php }?>
					<td ><?php echo getDisplayFormatDate($data[$k]['Date']);?></td>
					<td>
					<?php
						if(($_REQUEST['gid']==1 || $_REQUEST['gid']==2) && $data[$k]['Is_Opening_Balance'] ==1)
						{
							echo 'Opening Balance';
						
						}
						else
						{
							echo $data[$k]['ParticularLedgerName'];
						}
					?>
                    </td>
					                    
                    <td ><?php $voucher_type =$obj_ledger_details->get_voucher_details($data[$k]['VoucherTypeID']);
								if($voucher_type=='' && $data[$k]['VoucherTypeID'] <> ""){echo '---';}else{echo $voucher_type;}?></td>
                                
                    <td ><?php 
						
						$BankID = $m_objUtility->getBankID($data[$k]['VoucherTypeID'], $data[$k]['RefNo']);
						$prefix = $m_objUtility->GetPreFix($data[$k]['VoucherTypeID'],$BankID);
						if(!empty($prefix))
						{
							echo $prefix.'-';
						}
						if($data[$k]['VoucherTypeID'] == VOUCHER_SALES)
						{
							echo $obj_ledger_details->getChequeNumber($data[$k]['VoucherID'],false);
						}
						else
						{
							echo $data[$k]['ExternalCounter'];
						} 
						
						//var_dump($data[$k]);
						
						?>
					</td>
                     <td style="text-align:center;">
                    <?php echo $obj_ledger_details->getChequeNumber($data[$k]['VoucherID']);
					//echo $ChequeNumber;?></td>
                    <td style="text-align:right;"><?php if($DebitAmt <> 0){echo number_format($DebitAmt, 2);}else{echo '';} ?></td>
					<td  style="text-align:right;"><?php if($CreditAmt <> 0){echo number_format($CreditAmt, 2);}else{echo '';} ?></td>
                    <td style="text-align:right;"><?php if($IsCreditor==true){echo number_format($BalanceAmt, 2);} else{echo number_format($BalanceAmt, 2);}?></td>
                	<td style="text-align:right;"><?php echo $voucher_details[0]['VoucherNo'];?></td>
                    <?php if($voucher_details[0]['Note']=='')
					{ ?>
                    
						<td ><?php echo '---'; ?></td>	
					<?php }
					else
					{
                    ?>
                    <td ><?php echo $voucher_details[0]['Note']; ?></td>
					<?php } ?>
					<td ><?php echo ($loginID <> 0)?$loginDetails[$loginID]:"--"; ?></td>
					<td ><?php echo $TimeStamp; ?></td>
                </tr>
				 <?php
				}
				}
				?>

<script>document.getElementById('BalanceAmount').innerHTML = (<?php echo $BalanceAmt?>).toFixed(2)</script> 	
 <?php                
		}
		
		else
		{
			?>
            <!--</tbody><tr height="25"><td colspan="9" align="center"><font color="#FF0000"><b>Records Not Found....</b></font></td></tr>-->
            <?php	
		}
		?>
        
</tbody>
</table>
</div>
</center>
<script>
show();</script>	

</div>	
<?php include_once "includes/foot.php"; ?>

<div id="openDialogOk" class="modalDialog" >
	<div style="margin:2% auto; ">
		<div id="message_ok">
		</div>
	</div>
</div>

<script>
if (window.opener) {
   // alert('inside a pop-up window or target=_blank window');
	document.getElementById('btnBack').style.visibility = 'hidden';
} 
else {
    document.getElementById('btnBack').style.visibility = 'visible';
}

</script>
<script src="js/jsCommon_20190326.js"></script> 
<script>

// Show Warning Message

function showWarning(text){

alert("Sorry, but you can't "+text+" this entry.\nThere is some technical issue related to this entry. \nPlease take a screenshot and send to the techsupport team. \n\n Thanks!!");
return false;

}


$(document).keyup(function(e) {    
    if (e.keyCode == 27) 
	{ 
		//escape key
		var sHeaders = document.getElementsByClassName('PrintClass'), i;
	
		for (i = 0; i < sHeaders.length; i += 1)
		{
			sHeaders[i].style.display = 'none';
		}
    }
});

$(document).ready(function() {
var gid='<?php echo $_REQUEST['gid']?>'	;
//alert(gid);
printMessage = '<?php echo $sHeader?> ';
printMessage += '<center><font style="font-size:14px;" id="ledger_name" class="PrintClass"><b><?php echo $data[0]['Particular']; if($data[1]['owner_name'] <> ""){echo ' - ' .$data[1]['owner_name'] ;}?></b></font></center>';
 $('#example').dataTable(
 {
	"bDestroy": true
}).fnDestroy();

var EditAccess = "<?php echo $_SESSION['profile'][PROFILE_MANAGE_MASTER]; ?>";
var nCol = [10];
if(EditAccess == 1)
{
	nCol = [12,14,15];
}

if(localStorage.getItem("client_id") != "" && localStorage.getItem("client_id") != 1)
{
		$('#example').dataTable( {
						dom: 'T<"clear">Blfrtip',
						"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
						buttons: 
						[
							{
								extend: 'colvis',
								width:'inherit'/*,
								collectionLayout: 'fixed three-column'*/
							}
						],
					 columnDefs: 
					 [
						{
							targets: nCol,
							visible: false
						}
					],
						"oTableTools": 
						{
							"aButtons": 
							[
								{ "sExtends": "copy", "mColumns": "visible" },
								{ "sExtends": "csv", "mColumns": "visible" },
								{ "sExtends": "xls", "mColumns": "visible" },
								{ "sExtends": "pdf", "mColumns": "visible"},
								{ "sExtends": "print", "mColumns": "visible","sMessage": printMessage + " "}
							],
						 "sRowSelect": "multi"
					},
					aaSorting : [],
						
					fnInitComplete: function ( oSettings, json ) {
						//var otb = $(".DTTT_container")
						//alert("fnInitComplete");
						$(".DTTT_container").append($(".dt-button"));
						
						//get sum of amount in column at footer by class name sum
						this.api().columns('.sum').every(function(){
						var column = this;
						var total = 0;
						var sum = column
							.data()
							.reduce(function (a, b) {
								if(a.length == 0)
								{
									a = '0.00';
								} 
								if(b.length == 0)
								{
									b = '0.00';
								}
								var val1 = parseFloat( String(a).replace(/,/g,'') ).toFixed(2);
								var val2 = parseFloat(String(b).replace(/,/g,'') ).toFixed(2);
								total = parseFloat(parseFloat(val1)+parseFloat(val2)).toFixed(2);
								return  total;
							});
							if(gid == 1 || gid == 2)
							{
								$(column.footer()).html(format(sum,2));
							}
					});
					
					}
					
				} );	
	}
	else
	{
			$('#example').dataTable( {
						/*dom: 'T<"clear">lfrtip',*/
						dom: 'T<"clear">Blfrtip',
						"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
						buttons: 
						[
							{
								extend: 'colvis',
								width:'inherit'/*,
								collectionLayout: 'fixed three-column'*/
							}
						],
					 columnDefs: 
					 [
						{
							targets: nCol,
							visible: false
						}
					],
						"oTableTools": 
						{
							"aButtons": 
							[
								{ "sExtends": "copy", "mColumns": "visible" },
								{ "sExtends": "csv", "mColumns": "visible" },
								{ "sExtends": "xls", "mColumns": "visible" },
								{ "sExtends": "pdf", "mColumns": "visible"},
								{ "sExtends": "print", "mColumns": "visible","sMessage": printMessage + " "}
							],
						 "sRowSelect": "multi"
					},
					aaSorting : [],
						
					fnInitComplete: function ( oSettings, json ) {
						//var otb = $(".DTTT_container")
						//alert("fnInitComplete");
						$(".DTTT_container").append($(".dt-button"));
						
						//get sum of amount in column at footer by class name sum
						this.api().columns('.sum').every(function(){
						var column = this;
						var total = 0;
						var sum = column
							.data()
							.reduce(function (a, b) {
								if(a.length == 0)
								{
									a = '0.00';
								} 
								if(b.length == 0)
								{
									b = '0.00';
								}
								var val1 = parseFloat( String(a).replace(/,/g,'') ).toFixed(2);
								var val2 = parseFloat(String(b).replace(/,/g,'') ).toFixed(2);
								total = parseFloat(parseFloat(val1)+parseFloat(val2)).toFixed(2);
								return  total;
							});
					$(column.footer()).html(format(sum,2));
					});
					
					}
					
				} );	
			
	}

} );		
</script>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - List of Maintenance Bills</title>
</head>

<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/MaintenanceBill_m.class.php");
include_once("classes/include/fetch_data.php");
include_once("classes/utility.class.php");

$obj_display_bills = new Display_Maintenance_Bill($m_dbConn);
$objFetchData = new FetchData($m_dbConn);
$objFetchData->GetSocietyDetails($objFetchData->GetSocietyID($_SESSION["unit_id"]));
$BillingCycle=$objFetchData->objSocietyDetails->sSocietyBillingCycle;
$billDetails = $obj_display_bills->fetchBillDetails($_SESSION["unit_id"]);
$LastBillPeriodID=$billDetails[sizeof($billDetails)-1]["PeriodID"];
$LastBillDate=$billDetails[sizeof($billDetails)-1]["BillDate"];
$getPaymentMetod= $obj_utility -> getPaymentOption();

/*
if($LastBillDate <> '')
{
	$reverseCharges=$obj_display_bills->objFetchData->getReverseChargesDetails($_SESSION["unit_id"], $LastBillDate);
}
*/
$obj_Utility = new utility($m_dbConn);

//print_r($_SESSION);

$balance = 0;
//$DueArray = $obj_Utility->getBillnReceiptsCollection($_SESSION['unit_id'],true,true,true,true,true,true,true

$DueArray = $obj_Utility->getBillnReceiptsCollection($_SESSION['unit_id'],true,true,true,false,true,true,true);
//var_dump($DueArray);

 if(sizeof($DueArray) > 0)
 {
	 $balanceAmt =0;
	 for($m = 0; $m < sizeof($DueArray); $m ++)
	 {
		 if($DueArray[$m]['mode'] == "Bill")
		 {
			if($obj_display_bills->getIsOpeningBill($DueArray[$m]['PeriodID']) == true)
			{
				continue;
			}								
		 	$balanceAmt = $balanceAmt + $DueArray[$m]['Amount'] + $DueArray[$m]['Ledger_round_off'];
		 }
		 else  if($DueArray[$m]['mode'] == "Receipt")
		 {
			 
			$balanceAmt = $balanceAmt - $DueArray[$m]['Amount'];
			 
			 
		}
		else if($DueArray[$m]['mode'] == "Payment")
		{
			$balanceAmt = $balanceAmt + $DueArray[$m]['Amount'];
		}
		else  if($DueArray[$m]['mode'] == "ReverseCharge")
		 {
		 	$balanceAmt = $balanceAmt - abs($DueArray[$m]['Amount']);
		 }
		 else  if($DueArray[$m]['mode'] == "CreditNote")
		 {
		 	$balanceAmt = $balanceAmt - abs($DueArray[$m]['Amount']);
		 }
		 else  if($DueArray[$m]['mode'] == "DebitNote")
		 {
		 	$balanceAmt = $balanceAmt + abs($DueArray[$m]['Amount']);
		 }
		  else  if($DueArray[$m]['mode'] == "Journal")
		 {
		 	$balanceAmt = $balanceAmt - abs($DueArray[$m]['Credit']);
		 	$balanceAmt = $balanceAmt + abs($DueArray[$m]['Debit']);
		 }
		  else  if($DueArray[$m]['mode'] == "Opening")
		 {
		 	$balanceAmt = $balanceAmt - abs($DueArray[$m]['Credit']);
		 	$balanceAmt = $balanceAmt + abs($DueArray[$m]['Debit']);
		 }
		 $DueArray[$m]['BalanceAmount'] = $balanceAmt;
	 }
 }
 ?>
<html>
<script type="text/javascript">
	function showPopup(chequeNo, payerBank, payerBranch)
 	{				
		alert("Cheque No : " + chequeNo + " of Payer Bank : " + payerBank + " and Payer Bank Branch : " + payerBranch + " is paid.");
 	}
function NEFT()
{
	<?php if($getPaymentMetod[0]['PaymentGateway'] == '1' && $getPaymentMetod[0]['PaymentGateway'] <> '')
	{?>
		window.location.href = <?php  echo "https://way2society.com/".$NEFTURL = "neft2.php?SID=".base64_encode( $_SESSION["society_id"])."&UID=".base64_encode($_SESSION['unit_id']); ?>
	<?php }
	else
	{?>
		window.location.href = <?php  echo "https://way2society.com/".$NEFTURL = "neft.php?SID=".base64_encode( $_SESSION["society_id"])."&UID=".base64_encode($_SESSION['unit_id']); ?>
	<?php }
	?>
}
</script>
<div id="page-wrapper" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
	<div class="row">
    <div class="col-lg-12">
		<div class="panel panel-default">
        <div class="panel-heading" style="font-size:20px">
            List of Maintenance Bills
        </div>
        <!-- /.panel-heading -->
        
        <div class="panel-body">

            <div class="col-lg-12" style="">
                <div class="well">

                    <table style="font-size:12px;text-align:center">
                    <tr><td rowspan="2" style="text-align:justify">You can make your bill payment from your Bank/NetBanking and record your NEFT/IMPS transaction details here which will be reflected in your ledger but it is subject to clearance/reconciliation.</td><td>
                    
                    <?php 
					if($_SESSION['apply_NEFT'] == 1)
					{
					if($getPaymentMetod[0]['PaymentGateway'] == '1' && $getPaymentMetod[0]['PaymentGateway'] <> '')
					{?>
						 <button type="button" style="float:right" class="btn btn-primary" onClick="window.open('<?php  echo "https://way2society.com/".$NEFTURL = "neft2.php?SID=".base64_encode( $_SESSION["society_id"])."&UID=".base64_encode($_SESSION['unit_id']); ?>')">Record NEFT/IMPS transaction</button>				
<?php }
else
{?>
	 <button type="button" style="float:right" class="btn btn-primary" onClick="window.open('<?php  echo "https://way2society.com/".$NEFTURL = "neft.php?SID=".base64_encode( $_SESSION["society_id"])."&UID=".base64_encode($_SESSION['unit_id']); ?>')">Record NEFT/IMPS transaction</button>
<?php }
					}
?>
                   </td></tr>
                    </table>
                    
                    
                </div>
            </div>
                <!-- /.col-lg-12 -->
        
            <div class="dataTable_wrapper" style="width:100%">
            <div class="dataTables_wrapper form-inline dt-bootstrap no-footer" id="dataTables-example_wrapper">
                <table aria-describedby="dataTables-example_info" role="grid"  style="width:100%" class="table table-striped table-bordered table-hover dataTable no-footer" id="example">
                    <thead>
                        <tr>
                        	<th style="width: 14px;">Sr No</th>
							 <th>Bill/Cheque Date</th>
                            <th>Bill/Voucher Number</th>
                            <th>Particulars</th>
                            <th>Check/Ref number</th>                            
                            <th>Due Date</th>                            
                            <th>Bill Amount (Rs.)</th>
                            <th>Received Amount (Rs.)</th>
                            <th>Payable Amount (Rs.) </th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php
							$count = 1;		 
//							echo "<BR>";
//print_r($DueArray);
//echo "<BR>";

							for($i = 0; $i < sizeof($DueArray); $i++)	
							{
								if($DueArray[$i]['mode'] == "Bill" && $obj_display_bills->getIsOpeningBill($DueArray[$i]['PeriodID']) == true)
								{
									continue;
									$BillText   = 'Opening Balance';										 
								}								
								
								?>
                            	<tr><td><?php echo $count++; ?></td>
                            	 <td><?php echo $DueArray[$i]['Date']?></td>
                                <td><?php echo $DueArray[$i]['VoucherNo']?></td>
                            <?php if($DueArray[$i]['mode'] == "Opening")
                            		{ ?>
                            			<td><?php echo"Opening Balance" ?></td>
                            			<td></td>
                            			<td></td>
                            			<td></td>
                            			<td><?php $DueArray[$i]['Amount'] ?></td>
                            	<?php	}                        
                               ?>
                               <?php if($DueArray[$i]['mode'] == "Bill") 
							   {
								   //echo "<BR><BR>";
								   //print_r($DueArray[$i]);
								   //echo "<BR><BR>";
								   $BillType = "Maintenance";
								   if($DueArray[$i]['BillType'] == 1)
								   {
									   $BillType = "Supplementary";
								   }
								  // $BillFor = $objFetchData->GetBillFor($_REQUEST["PeriodID"]);
									//$BillFor_Bill = "[".$m_objUtility->displayFormatBillFor($BillFor)."]";
								  $BillFor = $objFetchData->GetBillFor($DueArray[$i]['PeriodID']);
								 $BillText = $obj_Utility->displayFormatBillFor($BillFor)." [".$BillType."]";
									//$BillText  = $BillFor." [".$BillType."]";
								
									  if($obj_display_bills->getIsOpeningBill($DueArray[$i]['PeriodID']) == true)
									 {
										 $BillText   = 'Opening Balance 2';
										 
									}
								   // $BillFor = $objFetchData->GetBillFor(88);
								 	 $Amount=$DueArray[$i]['Amount']+$DueArray[$i]['Ledger_round_off'];
								   ?>
                               		 <td ><?php echo $BillText?></td>
                                		<td></td>       	
                                         <td><?php echo $DueArray[$i]['DueDate']?></td> 
                                     <td style="color:#00F;"  onClick="window.open('<?php echo $obj_display_bills->getBillPDFLink($_SESSION['unit_id'],  $BillFor , $DueArray[$i]["PeriodID"], $DueArray[$i]["BillType"])?>')"><?php echo number_format($Amount,2)?></td>
                                    <td><?php echo ""?></td>
                                <?php } 
								else  if($DueArray[$i]['mode'] == "Receipt") 
								{?>  
                                     <td><?php echo $DueArray[$i]['PayerBank']?></td>
                                    <td><?php echo $DueArray[$i]['ChequeNumber']?></td> 
                                     <td><?php echo ""?></td>                              
                                        <td><?php echo ""?></td>
                                        <td><?php echo number_format($DueArray[$i]['Amount'],2)?> &nbsp;&nbsp; <button type="button" id= "popup" style="border-radius:50px; width:15px; color:#009; vertical-align:middle;" onClick="window.open('ReceiptDetails.php?extra&ChequeDetailsId=<?=$DueArray[$i]['RefNo']?>&cycle=<?php echo $BillingCycle;?>&PeriodID=<?php echo  $DueArray[$i]['PeriodID'];?>', '_blank')"><i class="fa   fa-info-circle "></i> </button></td>
								 <?php }
								 else  if($DueArray[$i]['mode'] == "Payment") 
								 {?>  
									  <td><?php echo $DueArray[$i]['Comments']?></td>
									 <td><?php echo $DueArray[$i]['ChequeNumber'];?></td> 
									  <td><?php echo ""?></td>                              
										 <td><?php echo number_format($DueArray[$i]['Amount'],2)?></td>
										 <td><?php echo ""?> &nbsp;&nbsp; </td>
								  <?php }  
							else if($DueArray[$i]['mode'] == "ReverseCharge") 
								{ ?>  
                                     <td>[<?php echo $DueArray[$i]['PaidTo']?>]</td>
                                    <td>Reverse charges</td> 
                                     <td><?php echo ""?></td>                              
                                     <td><?php echo ""?></td>
                                    <td><?php echo number_format($DueArray[$i]['Amount'],2)?> </td>
					   <?php }
					   else if($DueArray[$i]['mode'] == "CreditNote") 
								{ ?>  
										<td>[<?php echo $DueArray[$i]['PaidTo']?>]</td>
										<td>Credit Note</>  
										<td><?php echo ""?></td>                              
										<td><?php echo ""?></td>
										<td><?php echo number_format($DueArray[$i]['Amount'],2)?></td>
										<!-- <td><a style="color:blue;text-decoration: none;" href="invoice.php?debitcredit_id='<?=$DueArray[$i]['ID']?>'&UnitID='<?=$DueArray[$i]['UnitID']?>'&NoteType='<?=CREDIT_NOTE?>'" target="_blank"><?php echo number_format($DueArray[$i]['Amount'],2)?> </td> -->
						<?php }	
						else if($DueArray[$i]['mode'] == "DebitNote") 
								{ ?>  
										<td>[<?php echo $DueArray[$i]['PaidTo']?>]</td>
										<td>Debit Note</>  
										<td><?php echo ""?></td>                              
										
										<td><?php echo number_format($DueArray[$i]['Amount'],2)?></td>
                                        <td><?php echo ""?></td>
										<!-- <td><a style="color:blue;text-decoration: none;" href="invoice.php?debitcredit_id='<?=$DueArray[$i]['ID']?>'&UnitID='<?=$DueArray[$i]['UnitID']?>'&NoteType='<?=DEBIT_NOTE?>'" target="_blank"><?php echo number_format($DueArray[$i]['Amount'],2)?> </td> -->
						<?php }	
                          	else if($DueArray[$i]['mode'] == "Journal") 
								{ ?>  
                                     <td>[<?php echo $DueArray[$i]['PaidTo']?>]</td>
                                    <td></td> 
                                     <td><?php echo ""?></td>
                            	    <?php
                            	    if($DueArray[$i]['Debit'] == 0.00)                           
                                    { ?>
                                     	<td><?php echo ""?></td>
                                     	<td><?php echo number_format($DueArray[$i]['Credit'],2)?> </td>
                                     	
                               <?php }
                                   else if($DueArray[$i]['Credit'] == 0.00)
                                   { ?>
                               			
                                     	<td><?php echo number_format($DueArray[$i]['Debit'],2)?> </td>
                                     	<td><?php echo ""?></td>
                                   <?php } ?>
                       <?php } ?>   
                                <td><?php echo number_format($DueArray[$i]['BalanceAmount'],2)?></td>
                          <?php }
                          ?>
                        
                    </tbody>
                </table>
            </div>
            
        </div>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
    </div>
</div>
</div>

<script>
$(document).ready(function() {
	$('#example').dataTable(
 {
	"bDestroy": true
}).fnDestroy();
/*var oTable = $('#dataTables-example').dataTable();
	oTable.fnDestroy();
$('#dataTables-example').DataTable( {
        "order": [[ 0, "desc" ]]
    } );*/
$('#example').dataTable( {
				dom: 'T<"clear">Blfrtip',
				"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
				buttons: 
				[
					{
						extend: 'colvis',
						width:'inherit',
						collectionLayout: 'fixed three-column'
					}
				],
				"aoColumnDefs": [{ "bVisible": false, "aTargets": [0] }],
				"oTableTools": 
				{
					"aButtons": 
					[
						{ "sExtends": "copy", "mColumns": "visible" },
						{ "sExtends": "csv", "mColumns": "visible" },
						{ "sExtends": "xls", "mColumns": "visible" },
						{ "sExtends": "pdf", "mColumns": "visible" },
						{ "sExtends": "print", "mColumns": "visible" }
					],
				 "sRowSelect": "multi"
			},
			aaSorting : [[ 0, "desc" ]],
				
			fnInitComplete: function ( oSettings ) {
				//var otb = $(".DTTT_container")
				$(".DTTT_container").append($(".dt-button"));
			}
			
		} );
});
</script>
<?php include_once "includes/foot.php"; ?>		
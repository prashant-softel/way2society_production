<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/MaintenanceBill_m.class.php");
include_once("classes/include/fetch_data.php");
$obj_display_bills = new Display_Maintenance_Bill($m_dbConn);
$objFetchData = new FetchData($m_dbConn);
$objFetchData->GetSocietyDetails($objFetchData->GetSocietyID($_SESSION["unit_id"]));
$BillingCycle=$objFetchData->objSocietyDetails->sSocietyBillingCycle;
$billDetails = $obj_display_bills->fetchBillDetails($_SESSION["unit_id"]);
$LastBillPeriodID=$billDetails[sizeof($billDetails)-1]["PeriodID"];
$LastBillDate=$billDetails[sizeof($billDetails)-1]["BillDate"];
$reverseCharges=$obj_display_bills->objFetchData->getReverseChargesDetails($_SESSION["unit_id"], $LastBillDate);
//print_r($reverseCharges);
$balance = 0;
//echo $LastBillPeriodID;
//print_r($billDetails);
//print_r($_SESSION);
?>

<script type="text/javascript">
	function showPopup(chequeNo, payerBank, payerBranch)
 	{				
		alert("Cheque No : " + chequeNo + " of Payer Bank : " + payerBank + " and Payer Bank Branch : " + payerBranch + " is paid.");
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
            <div class="dataTable_wrapper">
            <div class="dataTables_wrapper form-inline dt-bootstrap no-footer" id="dataTables-example_wrapper">
                <table aria-describedby="dataTables-example_info" role="grid" class="table table-striped table-bordered table-hover dataTable no-footer" id="example">
                    <thead>
                        <tr>
							<th style="width: 14px;">Sr No</th>
                            <th>Bill/Cheque Date</th>
                            <th>Bill/Voucher Number</th>
                            <th>Particulars</th>
                            <th>Cheque Number</th>                            
                            <th>Due Date</th>                            
                            <th>Bill Amount (Rs.)</th>
                            <th>Received Amount (Rs.)</th>
                            <th>Payable Amount (Rs.) </th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php 
							$count = 1;							
							//print_r($billDetails);
							$aryDisplay = array();
							for($i = 0; $i < sizeof($billDetails); $i++)
							{
								$chequeDetails = $obj_display_bills->objFetchData->getReceiptDetailsEx($_SESSION["unit_id"], $billDetails[$i]["PeriodID"]);
								//print_r($reverseCharges);
								
								
								for($j=0; $j < sizeof($chequeDetails) ; $j++)
								{	
									//echo "<br>chequeDetails";
									//print_r($chequeDetails[$j]);
									$voucherNo = $obj_display_bills->getVoucherNo($chequeDetails[$j]['ID']);
									//$count++;					
									$amount = (float)$chequeDetails[$j]['Amount'];
									$payerBank = $chequeDetails[$j]['PayerBank'];
									$payerChequeBranch = $chequeDetails[$j]['PayerChequeBranch'];								
									$chequeDate = $chequeDetails[$j]['ChequeDate'];
									$chequeNo = $chequeDetails[$j]['ChequeNumber']; 
									$balance -= $amount; 
									if($amount > 0)
									{
										?>
										 <tr>
											<td><?php echo $count++; ?></td>
                                            <td> <span style='display:none'> <?php echo $chequeDate;?></span> <?php echo getDisplayFormatDate($chequeDate);?> </td>
											<td> <?php echo $voucherNo; ?> </td>
											<td> <?php echo $payerBank; ?></td>	
                                            <td> <?php echo $chequeNo; ?> </td>										
											<td> <?php ?> </td> 
											<td> </td>                          
											<!--<td align="center" style="vertical-align:middle;"> <?php //echo number_format($amount, 2) ?> &nbsp;&nbsp; <button type="button" id= "popup" style="border-radius:50px; width:15px; color:#009; vertical-align:middle;" onclick="showPopup('<?php //echo $chequeNo ?>','<?php //echo $payerBank ?>', '<?php //echo $payerChequeBranch ?>');"><i class="fa   fa-info-circle "></i> </button> </td> -->
											<td align="center" style="vertical-align:middle;"> <?php echo number_format($amount, 2) ?> &nbsp;&nbsp; <button type="button" id= "popup" style="border-radius:50px; width:15px; color:#009; vertical-align:middle;" onclick="window.open('ReceiptDetails.php?PeriodID=<?php echo $billDetails[$i]["PeriodID"];?>', '_blank')"><i class="fa   fa-info-circle "></i> </button> </td> 
                                            <td> <?php echo number_format($balance, 2); ?> </td>
										 </tr>
										 <?php			
									}
								}
								
								$iTotalBillAmount = $billDetails[$i]['BillSubTotal'] + $billDetails[$i]['BillInterest'] + $billDetails[$i]['AdjustmentCredit'];
								$iTotalBillAmountPayable = $iTotalBillAmount + $billDetails[$i]['PrincipalArrears'] + $billDetails[$i]['InterestArrears'];
								$balance += $iTotalBillAmountPayable;
								
								?>
									<tr>
										<td><?php echo $count++; ?> </td>
                                        <td> <span style='display:none'> <?php echo $billDetails[$i]['BillDate'];?></span> <?php echo getDisplayFormatDate($billDetails[$i]['BillDate']);?> </td>
										<td> <?php echo $billDetails[$i]['BillNumber'];?> </td>
										<?php if($billDetails[$i]['BillNumber'] == '')
										{
											?>
											<td> <?php echo $billDetails[$i]['BillFor']; ?></td>
											<?php
										}
										else
										{
											?>
											<td> <?php echo 'Bill For '.$billDetails[$i]['BillFor']; ?></td>
											<?php
										}
										?>
                                        <td> </td>										
										<td> <?php echo getDisplayFormatDate($billDetails[$i]['DueDate']);?> </td>                            
										<td><a href="<?php echo $obj_display_bills->getBillPDFLink($_SESSION['unit_id'], $billDetails[$i]['BillFor'], $billDetails[$i]["PeriodID"])?>" target="_blank"><?php echo number_format($iTotalBillAmount, 2); ?></a></td>
										<td> </td>
										<td> <?php echo number_format($iTotalBillAmountPayable, 2); ?> </td>
									</tr>
								<?php 
								$balance = $iTotalBillAmountPayable;
							}
							//$balance = $iTotalBillAmountPayable;
							for($j=0; $j < sizeof($reverseCharges) ; $j++)
								{	
									$amount = (float)$reverseCharges[$j]['Amount'];
									$Date = $reverseCharges[$j]['Date'];
									
									if($Date >= $LastBillDate)
									{
										$balance -= abs($amount); 
									?>
										 <tr>
											<td><?php echo $count++; ?> </td>
                                            <td> <span style='display:none'> <?php echo $Date;?></span> <?php echo getDisplayFormatDate($Date);?> </td>
											<td></td>
											<td>[<?php echo $reverseCharges[$j]['To']; ?>]</td>	
                                            <td>Reverse charges</td>										
											<td> <?php ?> </td> 
											<td> </td>                          
											<td align="center" style="vertical-align:middle;"><?php echo number_format(abs($amount), 2); ?></td> 
											<td> <?php echo number_format($balance, 2); ?> </td>
										 </tr>
										 <?php
									}
								}
							$chequeDetailsExtra = $obj_display_bills->objFetchData->getReceiptDetailsEx($_SESSION["unit_id"], $LastBillPeriodID,true,$BillingCycle);
							for($j=0; $j < sizeof($chequeDetailsExtra) ; $j++)
								{	
									$voucherNo = $obj_display_bills->getVoucherNo($chequeDetailsExtra[$j]['ID']);
									$amount = (float)$chequeDetailsExtra[$j]['Amount'];
									$payerBank = $chequeDetailsExtra[$j]['PayerBank'];
									$payerChequeBranch = $chequeDetailsExtra[$j]['PayerChequeBranch'];								
									$chequeDate = $chequeDetailsExtra[$j]['ChequeDate'];
									$chequeNo = $chequeDetailsExtra[$j]['ChequeNumber']; 
									$balance -= abs($amount); 
									if($amount > 0)
									{
										 ?>
										 <tr>
											<td> <?php echo $count++; ?> </td>
                                            <td> <span style='display:none'> <?php echo $chequeDate;?></span> <?php echo getDisplayFormatDate($chequeDate);?> </td>
											<td> <?php echo $voucherNo; ?> </td>
											<td> <?php echo $payerBank; ?></td>	
                                            <td> <?php echo $chequeNo; ?> </td>										
											<td> <?php ?> </td> 
											<td> </td>                          
											<td align="center" style="vertical-align:middle;"> <?php echo number_format($amount, 2) ?> &nbsp;&nbsp; <button type="button" id= "popup" style="border-radius:50px; width:15px; color:#009; vertical-align:middle;" onclick="window.open('ReceiptDetails.php?extra&cycle=<?php echo $BillingCycle;?>&PeriodID=<?php echo $LastBillPeriodID;?>', '_blank')"><i class="fa   fa-info-circle "></i> </button> </td> 
											<td> <?php echo number_format($balance, 2); ?> </td>
										 </tr>
										 <?php			
									}
								}
								
								
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
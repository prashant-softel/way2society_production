<?php
include_once ("../classes/dbconst.class.php");
include_once("../classes/include/dbop.class.php"); 
include_once ("../classes/bank_statement.class.php");
$dbConn = new dbop();


$obj_view_bank_statement = new bank_statement($dbConn);

$Reco_Check_Day_Span=$obj_view_bank_statement->obj_Utility->getRecoCheckDaySpan();

$ledgerID=$_POST['ledgerID'];
$voucher_date=$_POST['voucher_date'];
$row_id=$_POST['row_id'];
$ref_chequeNumber=$_POST['chequeNumber'];

$from_date=date('Y-m-d', strtotime($voucher_date. ' - 7 days'));//7 
$to_date=date('Y-m-d', strtotime($voucher_date. ' + '.$Reco_Check_Day_Span.' days')); //15

$details = $obj_view_bank_statement->getActualBankDetails($ledgerID,$from_date,$to_date,$transType="");
?>
<table id="example" class="display" width="100%" >
<thead>
<tr style="height:35px;">
	<th style="width:10%;text-align:center;">Select</th>
	<th style="width:10%;text-align:center;">Date</th>
    <th style="width:15%;text-align:center;">Note</th>
    <th style="width:5%;text-align:center;">Voucher Type</th>
    <th style="width:9%;text-align:center;">Ref.</th>
    <th style="width:12%;text-align:center;">Withdrawals</th>
    <th style="width:12%;text-align:center;">Deposits</th>
    <th style="width:12%;text-align:center;">Balance</th>
    <th style="width:12%;text-align:center;">Status</th>
</tr>
</thead>
<tbody>
	<?php		
		$balance = $details[0]['Bank_Balance'] - $details[0]['Debit'] - $details[0]['Credit'];
		$totalWithdrawals = 0;
		$totalDeposits = 0;
		$ledgerName = "";
		$chequeDetails="";
		$ledger_details="";
		$paidAmount = 0;
		$chequeNumber = "-";
		$voucherType = "-";
		$openingBalancePresent = 0;
		$reference = 0;
		
		if($balance >= 0)
		{?>
			<tr style="height:30px;">
                <td style="width:10%;text-align:center;"></td>
                <td style="width:10%;text-align:center;"><?php echo getDisplayFormatDate($from_date);  ?></td>
                <td style="width:20%;text-align:left;"><?php echo 'Opening Balance'; ?></td>
                <td style="width:15%;text-align:left;"><?php echo '-'; ?></td>
                <td style="width:5%;text-align:center;"><?php echo '-' ?></td>
                <td style="width:9%;text-align:center;"><?php echo '-' ?></td>
                <td style="width:12%;text-align:right;"><?php echo '-'?></td>
                <td style="width:12%;text-align:right;"><?php echo number_format($balance,2) ;?></td>
               <td>&nbsp;</td>
                <!--<td style="width:12%;text-align:right;">
               
               </td>-->
       		</tr>
<?php	}
	
		for($i = 0; $i < sizeof($details); $i++)
		{
		
			
		 ?>

           <tr style="height:30px;">
            <td style="width:10%;text-align:center;"><input type="radio" name="act_bank_statement" value="<?php echo $details[$i]['Id']?>"  <?php echo ($ref_chequeNumber==$details[$i]['ChequeNo'])?'checked':''?> act_bank_statement_date="<?php echo getDisplayFormatDate($details[$i]['Date']) ?>" row_id="<?php echo $row_id; ?>"></td>
            <td style="width:10%;text-align:center;"><?php echo getDisplayFormatDate($details[$i]['Date']);?></td>
            <td style="width:20%;text-align:left;"><?php echo $details[$i]['Bank_Description'];
			if(!empty($details[$i]['Notes']))
			{
				echo "<br>".$details[$i]['Notes'];
			}
			?></td>
            <?php
            
			$VoucherType = '-';
			
			if($details[$i]['Debit'] > 0)
			{
				$totalWithdrawals += $details[$i]['Debit']; 
				$balance += $details[$i]['Debit'];
				$VoucherType = 'Payment';
			}
			else if($details[$i]['Credit'] > 0)
			{
				$totalDeposits += $details[$i]['Credit'];
				$balance += $details[$i]['Credit'];
				$VoucherType = 'Receipt';
			}?>
            
            <td style="width:5%;text-align:center;"><?php echo $VoucherType; ?></td>
            <td style="width:9%;text-align:center;"><?php echo $details[$i]['ChequeNo']; ?></td>
            
			<?php if($details[$i]['Reco_Status'] == 1)
			{ 
				if($details[$i]['Debit'] > 0)
				{ ?>
					
                    <td style="width:12%;text-align:right;"><a onclick="redirectTransaction(<?php echo $details[$i]['Id']?>);"><?php echo number_format($details[$i]['Debit'], 2); ?></a></td>	
                <?php }
				else
				{ ?>
					<td style="width:12%;text-align:right;"><?php echo number_format($details[$i]['Debit'], 2); ?></td>
				<?php }
				
				if($details[$i]['Credit'] > 0)
				{ ?>
					     <td style="width:12%;text-align:right;"><a onclick="redirectTransaction(<?php echo $details[$i]['Id']?>);"><?php echo number_format($details[$i]['Credit'], 2); ?></a></td>
				<?php }
				else
				{ ?>
					    <td style="width:12%;text-align:right;"><?php echo number_format($details[$i]['Credit'], 2); ?></td>					
				<?php }
				
				?>


			<?php }
			else
			{ ?>
				<td style="width:12%;text-align:right;"><?php echo number_format($details[$i]['Debit'], 2); ?></td>	
                <td style="width:12%;text-align:right;"><?php echo number_format($details[$i]['Credit'], 2); ?></td>
			<?php }
			?>
			

            <td style="width:12%;text-align:right;"><?php echo number_format($details[$i]['Bank_Balance'],2); ?></td>
            
           <td align="center">
           <?php 
			   if($details[$i]['Reco_Status']==1)
			   {

					echo "<img src='images/clear.png' alt='Cleared' width='25' height='25'>"; 
					$StatementCount = $obj_view_bank_statement->getStatementCount($details[$i]['Id']);
                   if($StatementCount[0]['statement_count'] >1 )
                   {
                   	echo '<span><i class="fa fa-star" aria-hidden="true" style="color:red"></i></span>';
                   }
				}
			   ?>
           </td>
        </tr>
<?php  } ?>

<tr style="text-align:center;background-color:#D8DDF5;height:30px;">
	 <td ></td>
    <td ></td>
    <td > **Totals** </td>
     <td ></td>
     <td ></td>
    <td style="display: none;"></td>
    <td style="display: none;"></td>
	<td style="display: none;"></td>
	<td style="display: none;"></td>
    <td style="text-align:right;"> <?php echo number_format($totalWithdrawals,2); ?> </td>
    <td style="text-align:right;"><?php echo number_format($totalDeposits,2) ; ?> </td>
    <td style="text-align:right;"><?php echo number_format($balance, 2); ?> </td>
  
</tr>
</tbody>
</table>
<script type="text/javascript">
	 function datatablenew(search_text)
	{
	       $('#example').dataTable(
			 {
				"bDestroy": true
			}).fnDestroy();

			//if(localStorage.getItem("client_id") != "" && localStorage.getItem("client_id") != 1)
			//{
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
					
				aaSorting : [],
                oSearch: {"sSearch":search_text},
					
				fnInitComplete: function ( oSettings ) {
					// $(".DTTT_container").append($(".dt-button"));
					$(".DTTT_container").css('display','none');
					$(".dt-button").css('display','none');

				}
				
			} );
		}	
		 
$(document).ready(function() {
	var ref_chequeNumber='<?php echo $ref_chequeNumber; ?>';
	datatablenew(ref_chequeNumber);
	
	});
</script>
<script type="text/javascript">
	$('#voucher_date,#chequeNumber,#withdrawal,#deposit').on( 'click', function () {
		var search_text =$(this).text();
	    datatablenew(search_text);

} );
</script>
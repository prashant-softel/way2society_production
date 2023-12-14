<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>W2S - TDS Deduction Reports</title>
<style>
	/*table {
    	border-collapse: collapse;
		border:1px solid #cccccc; 
		
	}*/
	th, td {
		border-collapse: collapse;
		border:1px solid #cccccc; 
		text-align:left;
	}	
	tr:hover {background-color: #f5f5f5}
	/*td{border:1px dotted black !important;}*/
</style>



</head>
<?php 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include "classes/include/fetch_data.php";
include_once("classes/dbconst.class.php");
include_once "classes/utility.class.php"; 
$m_objUtility = new utility($dbConn);
$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);

include_once("classes/view_ledger_details.class.php");
$obj_ledger_details = new view_ledger_details($dbConn);

//echo "<pre>";
//print_r($_SESSION);
//echo "</pre>";
$grpid=$obj_ledger_details->GetGroupID($_SESSION['default_tds_payable']);
$data = $obj_ledger_details->details($grpid,$_SESSION['default_tds_payable'], getDBFormatDate($_SESSION['from_date']),  getDBFormatDate($_SESSION['to_date']));
//echo "<pre>";
//print_r($data);
//echo "</pre>";

 ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body>
	<center>
    	<div id="mainDiv" style="width:80%;"><?php include_once( "report_template.php" ); ?>
   
   <div  id="originalDiv" style="border: 1px solid #cccccc; border-collapse:collapse; width:100%;" >
        <div id="bill_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:18px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
            <div id="society_reg" style="font-size:14px;"><?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?></div>
            <div id="society_address"; style="font-size:14px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>
        </div>
        <div id="bill_subheader" style="text-align:center;">
            <div style="font-weight:bold; font-size:16px;">Deduction Reports</div>
            <div style="font-weight; font-size:16px;">From <?php echo $_SESSION['from_date'];?> To <?php echo $_SESSION['to_date']; ?></div>            
        </div>
        
        <table  style="width:100%;font-size:14px;border-collapse: collapse;">
        	<tr>
            	<th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc; border-left:none; width: 6%;">Sr. No.</th>
                <th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;width: 25%; " >Name Of Deductee</th>
                <th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;width: 12%; ">PAN Of Deductee</th>
                <th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;width: 12%;   ">TDS Head/Section</th>
                <th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;width: 15%; " >Date Of Invoice/Payment</th>
                <th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;width: 12%; ">Gross Amount</th>
                <th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;width: 6%;   ">TDS %</th>
                <th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc; width: 12%;" >TDS Deducted</th>
            </tr>
            <?php 
				$BalanceAmt=0;
				$Tds_Amount=0;
				$SumOfGrossAmount=0;
				$SumOfTDSAmount=0;
				$month = '';
				$last_val = end(array_keys($data));
				if($data<>"")
				{		
					$cnt =1;	
					foreach($data as $k => $v)
					{
						if(isset($data[$k]['id']))
						{
							$categoryid=$obj_ledger_details->obj_utility->getParentOfLedger($data[$k]['id']);
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
							{
								$DebitAmt = $data[$k]['Debit'];
								$CreditAmt = $data[$k]['Credit'];
								$BalanceAmt = $BalanceAmt + $DebitAmt - $CreditAmt;
							}
							$finalData =  $obj_ledger_details->get_voucher_details($data[$k]['VoucherTypeID'],$data[$k]['VoucherID'],$grpid,$_SESSION['default_tds_payable'], "By");
							$InvoiceData = $obj_ledger_details->GetInvoiceLedger($finalData[0]['VoucherNo']);
							
							if($DebitAmt <> 0 && $CreditAmt ==0)
							{
								$Tds_Amount = $DebitAmt;
							}
							else if($CreditAmt <> 0 && $DebitAmt==0 )	
							{
								$Tds_Amount = $CreditAmt;
							}
							$SumOfGrossAmount = $SumOfGrossAmount+$InvoiceData[0]['InvoiceChequeAmount'];
							$SumOfTDSAmount = $SumOfTDSAmount+$Tds_Amount;
							// echo $data[$k]['Date'];
							// Convert the date to a DateTime object
							$dateTime = new DateTime($data[$k]['Date']);
							if(empty($month)){
								$month = $dateTime->format('M');
							}

							if($month === $dateTime->format('M')){
								$GrossAmountMonth = $GrossAmountMonth+$InvoiceData[0]['InvoiceChequeAmount'];
								$TDSAmountMonth = $TDSAmountMonth+$Tds_Amount;
								$monthTotal = 0;
								$currentMonth = $month;
								// echo $k;
							}else{
								$month = $dateTime->format('M');
								$SumOfGrossAmountMonth = $GrossAmountMonth;
								$SumOfTDSAmountMonth = $TDSAmountMonth;
								$GrossAmountMonth = 0;
								$TDSAmountMonth = 0;
								$GrossAmountMonth = $GrossAmountMonth+$InvoiceData[0]['InvoiceChequeAmount'];
								$TDSAmountMonth = $TDSAmountMonth+$Tds_Amount;
								$monthTotal = 1;
							}

							if($last_val === $k){
								$SumOfGrossAmountMonth = $GrossAmountMonth;
								$SumOfTDSAmountMonth = $TDSAmountMonth;
								$lastval = 1;
							}

							// echo "SumOfGrossAmountMonth : ".$SumOfGrossAmountMonth . "<br>";
							// echo "SumOfTDSAmountMonth : ".$SumOfTDSAmountMonth . "<br>";

							if($monthTotal){?>
								<tr>
									<td colspan="5" style="text-align:center;background-color: #D3D3D3;border-collapse: collapse;border:1px solid #cccccc;border-left:none; ">**<?php echo $currentMonth;?> Month Total **</td>
									<td style="text-align:center;background-color: #D3D3D3;border-collapse: collapse;border:1px solid #cccccc;border-left:none; " ><?php echo number_format($SumOfGrossAmountMonth,2)?></td>
									<td style="text-align:center;background-color: #D3D3D3;border-collapse: collapse;border:1px solid #cccccc;border-left:none; " >&nbsp;</td>
									<td style="text-align:center;background-color: #D3D3D3;border-collapse: collapse;border:1px solid #cccccc;border-left:none; " ><?php echo number_format($SumOfTDSAmountMonth,2)?></td>
								</tr>
							<?php
							}
						?>
            			<tr>
            				<td style="text-align:center;border-collapse: collapse;border:1px solid #cccccc; border-left:none;"><?php echo $cnt?></td>
                			<td style="text-align:center;border-collapse: collapse;border:1px solid #cccccc; " ><?php echo $finalData[0]['ledger_name'];?></td>
                			<td style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;"><?php if($finalData[0]['ledger_pan']==''){echo '---';}else{echo $finalData[0]['ledger_pan'];}?></td>
                			<td style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;  "><?php if($finalData[0]['Tds_Head']==''){echo '---';}else{echo $finalData[0]['Tds_Head'];}?></td>
                			<td style="text-align:center;border-collapse: collapse;border:1px solid #cccccc; " ><?php echo getDisplayFormatDate($data[$k]['Date']);?></td>
                			<td style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;"><?php echo $InvoiceData[0]['InvoiceChequeAmount']?></td>
                			<td style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;  "><?php if($finalData[0]['TDS_Ded_rate']=='' || $finalData[0]['TDS_Ded_rate']==0){echo '---';}else{echo $finalData[0]['TDS_Ded_rate'];}?></td>
                			<td style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;" ><?php echo $Tds_Amount ?></td>
            			</tr>
             	<?php 
			  		$cnt++;
					}
				}
				if($lastval){?>
		
						<tr>
							<td colspan="5" style="text-align:center;background-color: #D3D3D3;border-collapse: collapse;border:1px solid #cccccc;border-left:none; ">**<?php echo $currentMonth;?> Month Total **</td>
							<td style="text-align:center;background-color: #D3D3D3;border-collapse: collapse;border:1px solid #cccccc;border-left:none; " ><?php echo number_format($SumOfGrossAmountMonth,2)?></td>
							<td style="text-align:center;background-color: #D3D3D3;border-collapse: collapse;border:1px solid #cccccc;border-left:none; " >&nbsp;</td>
							<td style="text-align:center;background-color: #D3D3D3;border-collapse: collapse;border:1px solid #cccccc;border-left:none; " ><?php echo number_format($SumOfTDSAmountMonth,2)?></td>
						</tr>
		<?php } ?>
		<!-- Total Amount Calculation -->
						<!-- <tr>
							<td colspan="5" style="text-align:center;background-color: #D3D3D3;border-collapse: collapse;border:1px solid #cccccc;border-left:none; ">**Grand Total **</td>
							<td style="text-align:center;background-color: #D3D3D3;border-collapse: collapse;border:1px solid #cccccc;border-left:none; " ><?php echo number_format($SumOfGrossAmount,2)?></td>
							<td style="text-align:center;background-color: #D3D3D3;border-collapse: collapse;border:1px solid #cccccc;border-left:none; " >&nbsp;</td>
							<td style="text-align:center;background-color: #D3D3D3;border-collapse: collapse;border:1px solid #cccccc;border-left:none; " ><?php echo number_format($SumOfTDSAmount,2)?></td>
						</tr> -->
            
        </table>
        	<?php }
		?>
        </div>
   </div>
 
</body> 
</html>
<?php 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include_once "classes/dbconst.class.php";

include "classes/include/fetch_data.php";
$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
include_once "classes/income_register_report.class.php";
$obj_tax_report=new income_report($dbConn);
$paid_TDSDetails=$obj_tax_report->paid_TDSDetails(getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']));
//$tanNo = $obj_tax_report->getSocietyTanNo();
//echo "<pre>";
//print_r($paid_TDSDetails);
//echo "</pre>";
$ledgerDetails = $obj_tax_report->getLedgerDetails();
//echo "<pre>";
//print_r($ledgerDetails);
//echo "</pre>";
include_once "classes/view_ledger_details.class.php";
$objLedger = new view_ledger_details($dbConn);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TDS Report</title>
<style>
	table {
    	border-collapse: collapse;
	}
	table, th, td {
   		border:1px solid #cccccc;
		text-align:left;
	}	
@media print {

    @page {size: A4 landscape; }
}
</style>
<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
</head>

<body>
<center>
<div id="mainDiv" style="width:80%;">
		<?php include_once( "report_template.php" ); // get the contents, and echo it out.?>
        <script>
		document.getElementById('landscape').value='1';
		</script>
<div style="border:1px solid #cccccc;">
        <div id="bill_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:18px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
            <div id="society_reg" style="font-size:14px;"><?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?>
            </div>
            <div id="society_address"; style="font-size:14px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>
        </div>
        <div id="bill_subheader" style="text-align:center;">
            <div style="font-weight:bold; font-size:16px;">TDS Reports</div>
            	<div style = "font-weight; font-size:16px;">Tan No :&nbsp&nbsp<?php echo $objFetchData->objSocietyDetails->sSocietyTan_no?></div>
                        <div style="font-weight; font-size:16px;">FROM <?php echo getDisplayFormatDate($_SESSION['from_date']); ?> TO <?php echo getDisplayFormatDate($_SESSION['to_date']);?></div>
           
            
        </div>
        <?php
		?>
        <table  style="width:100%;font-size:14px;">
                <tr>
                <th style="text-align:center;  border:1px solid #cccccc; width:8%;"colspan="">Invoice Date</th>
                 <th style="text-align:center;  border:1px solid #cccccc; width:7%;"colspan="">Voucher No</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:18%;"colspan="">Vendor</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:11%;" colspan="">GSTIN No.</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:9%;" colspan="">PAN No.</th>
               
                <th style="text-align:center;  border:1px solid #cccccc; width:5%;" colspan="">TDS Type</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:6%;" colspan="">TDS Paid</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:6%;" colspan="">TDS Collected</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:5%;" colspan="">Note</th>
               </tr>
               
               <?php
			   $count=0;
			   $totalAmount=0;
			  
			    if($paid_TDSDetails <> '')
			   {
				  $tempLedgerID=0;
				  $tempDate=0;
				  $BalanceAmount=0;
				  //$Debit=0;
				  $FinalBalanceAmount=0;
				  $totalPaidTDS=0;
				  $totalCollectedTDS=0;
	
				for($i=0;$i<sizeof($paid_TDSDetails);$i++)
				{
					$resVoucherDetails = $objLedger->get_voucher_details($paid_TDSDetails[$i]['VoucherTypeID'],$paid_TDSDetails[$i]['VoucherID'],TDS_PAYABLE,$ledgerDetails['group_id'],"By");
					//echo "<pre>";
					//print_r($resVoucherDetails);
					//echo "</pre>";
					$totalPaidTDS += $paid_TDSDetails[$i]['Debit'];
					$totalCollectedTDS +=$paid_TDSDetails[$i]['Credit'];
					$natureOfPayment = $obj_tax_report->getNatureOfPayment($resVoucherDetails[0]['ledger_id']);
					$tdsType = '-';
					if($natureOfPayment[0]['TDS_NatureOfPayment'] <> "")
					{
						$tdsType = $natureOfPayment[0]['TDS_NatureOfPayment'];
					}
					echo "<tr>
					<td style='text-align:center;' colspan=''>&nbsp;".getDisplayFormatDate($paid_TDSDetails[$i]['Date'])."</td>
					<td style='text-align:center;' colspan=''>&nbsp;&nbsp;".$paid_TDSDetails[$i]['VoucherNo']."</td>
					
					<td style='text-align:center;' colspan=''>".$resVoucherDetails[0]['ledger_name']."</td>

					<td  style='border-left:none;text-align:center;' colspan=''>&nbsp;</td>
					
					<td  style='border-left:none;text-align:center;' colspan=''></td>
					
					<td colspan='' style='border-left:none;text-align:center;'>".$tdsType."&nbsp;</td>
					<td colspan='' style='border-left:none;text-align:center;'>".$paid_TDSDetails[$i]['Debit']."&nbsp;</td>
					<td colspan='' style='border-left:none;text-align:center;'>".$paid_TDSDetails[$i]['Credit']."&nbsp;</td>
					
					<td colspan='' style='border-left:none;text-align:center;'>".$resVoucherDetails[0]['Note']."&nbsp;</td>
					</tr>";
				}   
				   echo "<tr><td  style='text-align:center;background-color:#D2D2D2;' colspan=6><b>***Grand Total***</b></td>
				   <td style='text-align:center;background-color:#D2D2D2;' ><b>".number_format($totalPaidTDS,2)."</b>&nbsp;</td>
				   <td style='text-align:center;background-color:#D2D2D2;' ><b>".number_format($totalCollectedTDS,2)."</b></td>
					   <td style='text-align:center;background-color:#D2D2D2;' ></td>
				   </tr>";
				   
				}?>
</table>
</div>
</div>
</center>
</body>
</html>
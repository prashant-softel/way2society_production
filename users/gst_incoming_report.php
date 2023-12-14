<?php 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include_once "classes/dbconst.class.php";
include "classes/include/fetch_data.php";
$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
include_once "classes/list_member.class.php";
include_once "classes/income_register_report.class.php";
$obj_tax_report=new income_report($dbConn);

$obj_unit=new list_member($dbConn);
$LedgerList=$obj_unit->getAllUnits();

$SundryDebtorsList = $obj_unit->SundryDebtorsList();
foreach($SundryDebtorsList as $value)
{
  array_push($LedgerList,$value);	
}

$GST_Incoming_Reoprt = $obj_tax_report->getGSTIncomingDetails(getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']),$LedgerList);

/*echo "<pre>";
print_r($GST_Incoming_Reoprt);
echo "</pre>";
*/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Output GST Report</title>
<style>
	table {
    	border-collapse: collapse;
	}
	table, th, td {
   		border:1px solid #cccccc;
		text-align:left;
	}	
/*@media print {

    @page {size: A4 landscape; }
}*/
</style>
<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
</head>

<body>
<center>
<div id="mainDiv" style="width:90%;">
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
            <div style="font-weight:bold; font-size:16px;">Output GST Reports</div>
                        <div style="font-weight; font-size:16px;">FROM <?php echo getDisplayFormatDate($_SESSION['from_date']); ?> TO <?php echo getDisplayFormatDate($_SESSION['to_date']);?></div>
           
            
        </div>
        <table  style="width:100%;font-size:14px;">
                <tr>
                <th style="text-align:center;  border:1px solid #cccccc; width:7%;"colspan="">Bill Date</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:6%;"colspan="">Bill No</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:9%;"colspan="">Bill Type</th>
				<th style="text-align:center;  border:1px solid #cccccc; width:9%;"colspan="">Unit No/ID</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:20%;"colspan="">Member/Ledger Name</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:10%;" colspan="">GSTIN No.</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:7%;" colspan="">Bill CGST</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:7%;" colspan="">Bill SGST</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:10%;" colspan="">Taxable Amt<br>(B2B)</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:10%;" colspan="">Taxable Amt<br>(B2C)</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:10%;" colspan="">Total Bill Amt</th>
               </tr>
               
               <?php
			   $count=0;
			   $totalAmount=0;
			   $tmpUnit = $GST_Incoming_Reoprt[0]['UnitID'];
			   	
					for($iCount=0;$iCount<sizeof($GST_Incoming_Reoprt);$iCount++)
							{
								$UnitID = $GST_Incoming_Reoprt[$iCount]['UnitID'];
								
								if($iCount == 0 || $tmpUnit <> $UnitID)
								{
									$objFetchData->GetMemberDetails($UnitID);
									$tmpUnit = $GST_Incoming_Reoprt[$iCount]['UnitID'];	
								}
								
								$memberName=$objFetchData->objMemeberDetails->sMemberName;
								$unitNumber=$objFetchData->objMemeberDetails->sUnitNumber;
								$OwnerGSTIN=$objFetchData->objMemeberDetails->sMemberGstinNo;
								
							//$GST_Incoming_Reoprt=$obj_tax_report->getGSTIncomingDetails(getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']),$UnitID);
							
							$billType = $GST_Incoming_Reoprt[$iCount]['BillType'];
							$billNumber=$GST_Incoming_Reoprt[$iCount]['BillNumber'];
							
							// Add Prefix for SHREE SWAMI SAMARTH PRASANNA OSHIWARA EAST UNIT-14 CHS LTD 
							
							if($_SESSION['society_id'] == 284 && $billType <> 'Invoice')
							{
								$startyear = explode('-',getDisplayFormatDate($_SESSION['default_year_start_date']));
								$endyear = explode('-',getDisplayFormatDate($_SESSION['default_year_end_date']));
								$endyear = substr($endyear[2], -2);
								$billNumber = 'GR-'.sprintf('%05d', $billNumber).'/'.$startyear[2].'-'.$endyear;
							}
						
							if($billType == 'Invoice')
							{
								$billNumber = PREFIX_INVOICE_BILL.'-'.$billNumber;
							}
						
							
							//$InvoiceNumber = $GST_Incoming_Reoprt[$k]['Invoice_Number'];
							
							$month = $GST_Incoming_Reoprt[$iCount]['BillDate'];
							//$InvoiceDate = $GST_Incoming_Reoprt[$k]['Invoice_Date'];
							//$IGSTTotal=$GST_Incoming_Reoprt[$k]['TotalIGST'];
							$CGSTTotal=$GST_Incoming_Reoprt[$iCount]['CGST'];
							$SGSTTotal=$GST_Incoming_Reoprt[$iCount]['SGST'];
							
							//$CESSTotal=$GST_Incoming_Reoprt[$k]['TotalCESS'];
							//$InvoiceCGST=$GST_Incoming_Reoprt[$k]['Invoice_CGST'];
							//$InvoiceSGST=$GST_Incoming_Reoprt[$k]['Invoice_SGST'];
							// $TotalAmount = (float)$GST_Incoming_Reoprt[$iCount]['CGST']+(float)$GST_Incoming_Reoprt[$iCount]['SGST'];
							$TotalAmount = (float)$GST_Incoming_Reoprt[$iCount]['BillAmount'];
							//$Debit += (float)$paid_Invoice_details[$i]['Debit'];
							$BalanceAmount += $TotalAmount;
							$FinalBalanceAmount +=$TotalAmount;
							//$FinalCreditIGST +=(float)$GST_Incoming_Reoprt[$k]['TotalIGST'];
							$FinalCreditCGST +=(float)$GST_Incoming_Reoprt[$iCount]['CGST'];
							$FinalCreditSGST +=(float)$GST_Incoming_Reoprt[$iCount]['SGST'];
							
							
							
							
							//$FinalCreditCESS +=(float)$GST_Incoming_Reoprt[$k]['TotalCESS'];
							//$FinalCreditInvoiceCGST +=(float)$GST_Incoming_Reoprt[$k]['Invoice_CGST'];
							//$FinalCreditInvoiceSGST +=(float)$GST_Incoming_Reoprt[$k]['Invoice_SGST'];
				           $taxable_amt=($GST_Incoming_Reoprt[$iCount]['SGST']/0.09)+($GST_Incoming_Reoprt[$iCount]['CGST']/0.09);
				           if(!empty($OwnerGSTIN))
				           {
				           	$b2b_taxable=$taxable_amt;
				           	$FinalB2B_Taxable+=$taxable_amt;

				           	$b2c_taxable=0;
				           }
				           else
				           {
				           	$b2c_taxable=$taxable_amt;
				           	$FinalB2C_Taxable+=$taxable_amt;

				           	$b2b_taxable=0;
				           }
						
						
					echo "<tr>
					<td style='text-align:left;' colspan=''>&nbsp;".$month."</td>
					<td style='text-align:left;' colspan=''>&nbsp;".$billNumber."</td>					
					<td style='text-align:left;' colspan=''>&nbsp;".$billType."</td>
					<td style='text-align:left;'>&nbsp;&nbsp; ".$unitNumber."</td>
					<td style='text-align:left;' colspan=''>".$memberName."</td>
					<td  style='border-left:none;text-align:left;' colspan=''>&nbsp;&nbsp;".$OwnerGSTIN."</td>

					<td colspan='' style='border-left:none;text-align:right;'>".number_format($CGSTTotal,2)."&nbsp;</td>
					
					<td colspan='' style='border-left:none;text-align:right;'>".number_format($SGSTTotal,2)."&nbsp;</td>
					<td colspan='' style='border-left:none;text-align:right;'>".number_format($b2b_taxable,2)."&nbsp;</td>
					<td colspan='' style='border-left:none;text-align:right;'>".number_format($b2c_taxable,2)."&nbsp;</td>

					<td colspan='' style='border-left:none;text-align:right;'>".number_format($TotalAmount ,2)."&nbsp;</td>
					</tr>";
				  
				
			}
				   echo "<tr><td  style='text-align:center;background-color:#D2D2D2;' colspan=6><b>***Grand Total***</b></td>
				<!-- <td style='text-align:right;background-color:#D2D2D2;'  ><b>".number_format($FinalCreditIGST,2)."</b>&nbsp;</td>-->
				 <td style='text-align:right;background-color:#D2D2D2;' ><b>".number_format($FinalCreditCGST,2)."</b>&nbsp;</td>
				 <td style='text-align:right;background-color:#D2D2D2;' ><b>".number_format($FinalCreditSGST,2)."</b>&nbsp;</td> 
				 <td style='text-align:right;background-color:#D2D2D2;' ><b>".number_format($FinalB2B_Taxable,2)."</b>&nbsp;</td> 
				  <td style='text-align:right;background-color:#D2D2D2;' ><b>".number_format($FinalB2C_Taxable,2)."</b>&nbsp;</td> 
				 
				<td style='text-align:right;background-color:#D2D2D2;' ><b>".number_format($FinalBalanceAmount,2)."</b>&nbsp;</td>
				  
				   </tr>";
				   
					?>
</table>
</div>
</div>
</center>
</body>
</html>
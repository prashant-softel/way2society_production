<?php 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include_once "classes/dbconst.class.php";

include "classes/include/fetch_data.php";
$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);

include_once "classes/income_register_report.class.php";
$obj_income_report=new income_report($dbConn);
if($_POST['from_date'] <> '' && $_POST['to_date'] <> '' ) {

	$show_income_details=$obj_income_report->getIncomeDetails($_POST['from_date'],$_POST['to_date'],$_GET['sinkingfundid']);
	$queryString = ($_GET['sinkingfundid']) ? 'sinkingfund' : 'income';

	if($show_income_details == ''){?>	
		<script>
			window.location.href="common_period.php?<?=$queryString?>&fail";
		</script>	
	<?php
	}
}
else {
	//echo 'test';
	?>
<script>
	window.location.href="common_period.php?income&temp";
</script>	
	
	
<?php }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Income Register</title>
<style>
	table {
    	border-collapse: collapse;
	}
	table, th, td {
   		border: 0px solid black;
		text-align:left;
	}
	tr:hover {background-color: #f5f5f5}	
</style>
<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
</head>

<body>
<div id="mainDiv">
<?php include_once( "report_template.php" ); // get the contents, and echo it out.?>
<div style="border:1px solid #cccccc;">
        <div id="bill_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:18px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
            <!--<div id="society_type" style="font-weight:bold; font-size:20px;">PREMISES CO-OPERATIVE SOCIETY LTD.</div>-->
            <div id="society_reg" style="font-size:14px;"><?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?>
            </div>
            <div id="society_address"; style="font-size:14px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>
        </div>
        <div id="bill_subheader" style="text-align:center;">
            <div style="font-weight:bold; font-size:16px;">Income Register</div>
            <div style="font-weight; font-size:16px;">From<?php echo '   '.$_POST['from_date'].' ';?> To<?php echo '   '.$_POST['to_date'].'      ';?></div>
            <div style="font-weight; font-size:16px;">As on Date:<?php echo date("d.m.Y");?></div>
           
            
        </div>
        <table  style="width:100%;font-size:14px;" >
                <tr>
                <th style="text-align:center;  border:1px solid #cccccc; border-left:none;">Sr. No.</th>
                <th style="text-align:center;  width:10%; border:1px solid #cccccc;">Voucher Date</th>
                <th style="text-align:center;  width:10%; border:1px solid #cccccc;"" >Voucher No</th>
                <th style="text-align:center;  width:10%; border:1px solid #cccccc;"" >By</th>
                <th style="text-align:center;  border:1px solid #cccccc;"" colspan="3">Account Name</th>
                <th style="text-align:center;  border:1px solid #cccccc;"" colspan="3">Amount (In Rs.)</th>
                <th style="text-align:center;  width:20%; border:1px solid #cccccc; border-right:none;"" colspan="3">Remark</th>
                
                </tr>
               
               <?php
			   $count=0;
			   $totalAmount=0;
			   if($show_income_details <> '')
			   {
				   
				   //print_r($show_payment_details);
				   
				   
				foreach($show_income_details as $key=>$val)
				{
			
					$count++;
					$Amount= $show_income_details[$key]['Debit'] + $show_income_details[$key]['Credit'];
					$totalAmount = $totalAmount + $Amount;
					
				$particular_name=$obj_income_report->show_particulars($show_income_details[$key]['LedgerID'],$show_income_details[$key]['VoucherID']);
				
				echo "<tr><td style='border:1px solid #cccccc;text-align:center;border-left:none;'>".$count."</td><td style='border:1px solid #cccccc;text-align:center;'>".getDisplayFormatDate($show_income_details[$key]['Date'])."</td><td  style='border:1px solid #cccccc;border-left:none;text-align:center;'>".$show_income_details[$key]['VoucherNo']."</td><td  style='border:1px solid #cccccc;border-left:none;text-align:center;'>".$particular_name."</td><td colspan=3 style='border:1px solid #cccccc;border-left:none;'>".$show_income_details[$key]['TO']."</td><td colspan=3 style='border:1px solid #cccccc;border-left:none;text-align:center;'>".number_format($Amount,2)."</td><td colspan=3 style='border:1px solid #cccccc;border-left:none;text-align:center;border-right:none;'>".$show_income_details[$key]['Note']."</td></tr>";
				
					
					
					
				}   
				   echo "<tr><td colspan=7 style='border:1px solid #cccccc;text-align:center;border-left:none;border-bottom:none;background-color: #D3D3D3;'>***Total***</td><td colspan=3 style='border:1px solid #cccccc;text-align:center;border-bottom:none;background-color: #D3D3D3;'>".number_format($totalAmount,2)."</td></tr>";
				}
				else
				{
					
				 echo "<tr><td>No Records Available For Selected Period...</td></tr>";	
				}
				
			   
			   ?>
			
                
        </table>
</div>
</div>
<script>document.getElementById('btnExportPdf').style.display = "none";

</script>
</body>
</html>
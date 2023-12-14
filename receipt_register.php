<?php 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include_once "classes/include/dbop.class.php";
include_once "classes/dbconst.class.php";


include "classes/include/fetch_data.php";
$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);

include_once "classes/receipt_register.class.php";
$obj_receipt_report=new receipt_report($dbConn);
if($_POST['from_date'] <> '' && $_POST['to_date'] <> '' )
{
$show_receipt_details=$obj_receipt_report->getReceiptDetails($_POST['from_date'],$_POST['to_date']);
  if($show_receipt_details == '')
	{
	?>	
	<script>
		window.location.href="common_period.php?receipt&fail";
	</script>	
	
	<?php	
	}
}
else
{
	//echo 'test';
	?>
<script>
	window.location.href="common_period.php?receipt&temp";
</script>	
	
	
<?php }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Receipt Register</title>
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
<div style="border:1px solid #cccccc; ">
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
            <div style="font-weight:bold; font-size:16px;">Receipt Register</div>
            <div style="font-weight; font-size:16px;">From<?php echo '   '.getDisplayFormatDate($_POST['from_date']).' ';?> To<?php echo '   '.getDisplayFormatDate($_POST['to_date']).'      ';?></div>
            <div style="font-weight; font-size:16px;">As on Date:<?php echo date("d.m.Y");?></div>
           
            
        </div>
        <table  style="width:100%;font-size:14px;">
                <tr>
                <th style="text-align:center;  border:1px solid #cccccc; border-left:none;">Sr. No.</th>
                <th style="text-align:center;  width:10%; border:1px solid #cccccc;">Voucher Date</th>
                <th style="text-align:center;  width:10%; border:1px solid #cccccc;" >Receipt Voucher</th>
                <th style="text-align:center;  width:5%; border:1px solid #cccccc;">Unit No</th>
                <th style="text-align:center;   width:20%; border:1px solid #cccccc;" colspan="2">Member Name</th>
                <th style="text-align:center;  width:20%; border:1px solid #cccccc;" colspan="2">Payer Bank</th>
                <th style="text-align:center;  border:1px solid #cccccc;" colspan="2">Cheque No</th>
                <th style="text-align:center;  border:1px solid #cccccc;" colspan="3">Amount</th>
                <th style="text-align:center;  border:1px solid #cccccc;border-right:none;" colspan="3">Remark</th>
                
                </tr>
               
               <?php
			    $totalAmount=0;
				$count=0;
			   if($show_receipt_details <> '')
			   {
				   //print_r($show_payment_details);
				foreach($show_receipt_details as $key=>$val)
				{
					
					$totalAmount=$totalAmount +$show_receipt_details[$key]['Amount'];
					//$ledger_name=$obj_receipt_report->show_particulars($show_payment_details[$key]['BY'],$show_payment_details[$key]['id']);
					
					if($show_receipt_details[$key]['BY'] <> '')
					{
						$count++;
				echo "<tr><td style='border:1px solid #cccccc;text-align:center;border-left:none;'>".$count."</td><td style='border:1px solid #cccccc;text-align:center;'>". getDisplayFormatDate($show_receipt_details[$key]['Date'])."</td><td colspan=1 style='border:1px solid #cccccc;border-left:none;text-align:center;'>".$show_receipt_details[$key]['VoucherNo']."</td><td style='border:1px solid #cccccc;border-left:none;text-align:center;'>".$show_receipt_details[$key]['BY']."</td><td colspan=2 style='border:1px solid #cccccc;border-left:none;text-align:center;'>".$show_receipt_details[$key]['owner_name']."</td><td colspan=2 style='border:1px solid #cccccc;border-left:none;text-align:center;'>".$show_receipt_details[$key]['PayerBank']."</td><td colspan=2 style='border:1px solid #cccccc;border-left:none;text-align:center;'>".$show_receipt_details[$key]['ChequeNumber']."</td><td colspan=3 style='border:1px solid #cccccc;border-left:none;text-align:center;'>".number_format($show_receipt_details[$key]['Amount'],2)."</td><td colspan=3 style='border:1px solid #cccccc;border-left:none;text-align:left;border-right:none;'>".$show_receipt_details[$key]['Comments']."</td>";
					}
				}   
				   
				   echo "<tr><td colspan=10 style='border:1px solid #cccccc;text-align:center;background-color: #D3D3D3;border-left:none;'>***Total***</td><td colspan=3 style='border:1px solid #cccccc;text-align:center;background-color: #D3D3D3;'>".number_format($totalAmount,2)."</td>";
				   
				   
				   
				   
				   
				}
				else
				{
					
				 echo "<tr><td>No Records Available For Selected Period...</td></tr>";	
				}
				
			   
			   ?>
			
                
        </table>
</div>
</div>
</body>
</html>
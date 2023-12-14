
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Cash Details</title>
</head>


<?php
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include "classes/cash_flow.class.php";
include_once("classes/dbconst.class.php"); 
$objCashFlow = new CashFlow($dbConn);

//if($_POST['from_date'] == '' && $_POST['to_date'] == '' )
//{?>
<!--<script>
	window.location.href="common_period.php?cashflow&temp";
</script>-->
<?php	
//}

$objCashFlow->GetSocietyDetails($_SESSION['society_id']);
//$cashID = $objCashFlow->getCashID();
$cashOpeningBal = $objCashFlow->getOpeningBalance($_REQUEST["from_date"], $_REQUEST['ledgerid']);
$cashOpeningBalance = $cashOpeningBal[sizeof($cashOpeningBal)-1]['ReceivedAmount']-$cashOpeningBal[sizeof($cashOpeningBal)-1]['PaidAmount'];
$bankname = $objCashFlow->getBankName($_REQUEST['ledgerid']);
$cashReceivedDetails = $objCashFlow->getReceivedCashDetails($_REQUEST['ledgerid'], $_REQUEST["from_date"], $_REQUEST['to_date']);
$cashPaidDetails = $objCashFlow->getPaidDetails($_REQUEST['ledgerid'], $_REQUEST["from_date"], $_REQUEST['to_date']);
$totalReceivedAmount = 0;
$totalPaidAmount = 0;
$cashOpeningBal = $objCashFlow->getOpeningBalance($_REQUEST["from_date"], $_REQUEST['ledgerid']);
$cashOpeningBalance = $cashOpeningBal[0]['TReceivedAmount'] - $cashOpeningBal[0]['TPaidAmount'];
$prevYearOpeningBalance = $objCashFlow->getPrevYrOpeningBal($_REQUEST['ledgerid']);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cash Flow Report</title>
<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="js/ajax_new.js"></script>
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

	
</head>
<body>
<div id="mainDiv">
<?php include_once( "report_template.php" ); // get the contents, and echo it out.?>

    <center>    
    <div id="cash_flow" style="width:90%;">
        <div id="report_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:18px;"><?php echo $objCashFlow->objSocietyDetails->sSocietyName; ?></div>    
            <div id="society_reg" style="font-size:14px;"><?php if($objCashFlow->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objCashFlow->objSocietyDetails->sSocietyRegNo; 
				}
				?></div>
            <div id="society_address"; style="font-size:14px;"><?php echo $objCashFlow->objSocietyDetails->sSocietyAddress; ?></div>
        </div>
        <div id="financial_year" style="text-align:center;">            
            <div style="text-align:right;"><?php echo date('d.m.Y'); ?> </div>
        </div>
        <div style="text-align:center;font-weight:bold; font-size:16px;">Summarised Cash Flow Statement For <?php echo $bankname ; ?> </div>
        <div style="text-align:center;font-weight:bold; font-size:14px;"><?php echo getDisplayFormatDate($_REQUEST['from_date']); ?> to <?php echo getDisplayFormatDate($_REQUEST['to_date']); ?> </div>
        <br />
        <div id="cash_flow_report">
        	<table  style="width:80%;font-size:14px;" align="center" >                          
                <tr>                	
                	<th style="text-align:center; width:22%; border:1px solid #cccccc;border-left:none;"> Voucher Date </th>
                    <th style="text-align:left; width:15%; border:1px solid #cccccc;"> Particulars </th>
                    <th style="text-align:left; width:48%; border:1px solid #cccccc;"> Comments</th>
                    <th style="text-align:right; width:10%; border:1px solid #cccccc;border-right:none;" > Amount </th>     
                </tr>
                <tr><td><br /></td></tr>
                <tr>                	
                	<th colspan="3" style="text-align:left; width:35%;"> Previous Year Remaining Balance</th>  
                    <th style="text-align:right; width:15%;border:1px solid #cccccc;border-left:none;border-right:none;"><?php echo abs($prevYearOpeningBalance);
					if($prevYearOpeningBalance >= 0)
					{
						echo " Dr";
					}
					else
					{
						echo " Cr";	
					}	?> </th>
				</tr> 
                <tr><td><br /></td></tr>                        
                <tr>                	
                	<th colspan="3" style="text-align:left; width:35%;"> Opening Balance </th>  
                    <th style="text-align:right; width:15%;border:1px solid #cccccc;border-left:none;border-right:none;"><?php echo number_format(abs($cashOpeningBalance),2); 
					if(($cashOpeningBalance) >= 0)
					{
						echo " Dr";
					}
					else
					{
						echo " Cr";	
					}?> </th>                  
                </tr>
                <tr><td><br /></td></tr>
                <tr>
                	<td colspan="4" style="font-weight:bold;"> Cash Received : </td>
                </tr>
                <tr><td><br /></td></tr>
                <?php  
					for($i = 0; $i < sizeof($cashReceivedDetails); $i++)
					{
						$totalReceivedAmount += $cashReceivedDetails[$i]['Amount'];
				?>  
                <tr> 
                	<td style="text-align:center;"> <?php echo getDisplayFormatDate($cashReceivedDetails[$i]['VoucherDate']); ?> </td>
                    <td style="text-align:left;"> <?php echo $cashReceivedDetails[$i]['PaidBy']; ?> </td>
                    <td style="text-align:left;"> <?php if($cashReceivedDetails[$i]['Comments'] <> ""){echo "[".$cashReceivedDetails[$i]['Comments'] ."]"; }else{echo "-";}?> </td>
                    <td style="text-align:right;"> <?php echo number_format($cashReceivedDetails[$i]['Amount'],2); ?> </td>
                </tr>
                <?php } ?>             
                <tr>
                	<th colspan="3" style="width:35%;text-align:right;"> Total Received Amount : &nbsp; </th>
                    <th style="text-align:right; width:15%;border:1px solid #cccccc;border-left:none;border-right:none;"><?php echo number_format(abs($totalReceivedAmount),2);
					if(($totalReceivedAmount) >= 0)
					{
						echo " Dr";
					}
					else
					{
						echo " Cr";	
					}?> </th>
                </tr>  
                 <tr><td><br /></td></tr>
                <tr>
                	<td colspan="3" style="font-weight:bold;"> Cash Paid : </td>
                </tr>
                <tr><td><br /></td></tr>
                <?php  
					for($i = 0; $i < sizeof($cashPaidDetails); $i++)
					{
						$totalPaidAmount += $cashPaidDetails[$i]['Amount'];
				?>  
                <tr> 
                	<td style="text-align:center;"> <?php echo getDisplayFormatDate($cashPaidDetails[$i]['VoucherDate']); ?> </td>
                    <td style="text-align:left;"> <?php echo $cashPaidDetails[$i]['PaidTo']; ?> </td>
                     <td style="text-align:left;"> <?php if($cashPaidDetails[$i]['Comments'] <> ""){echo "[".$cashPaidDetails[$i]['Comments']."]";}else{echo "-";} ?> </td>
                    <td style="text-align:right;"> <?php echo number_format($cashPaidDetails[$i]['Amount'],2); ?> </td>
                </tr>
                <?php } ?>             
                <tr>
                	<th colspan="3" style="width:35%;text-align:right;"> Total Paid Amount : &nbsp; </th>
                    <th style="text-align:right; width:15%;border:1px solid #cccccc;border-left:none;border-right:none;"><?php echo number_format(abs($totalPaidAmount),2);
					if(($totalPaidAmount) > 0)
					{
						echo " Dr";
					}
					else
					{
						echo " Cr";	
					}?> </th> </tr>
                <tr><td><br /></td></tr>
                 <tr>
                	<th colspan="3" style="width:35%;text-align:right;"> Cash Balance : &nbsp; </th>
                    <th style="text-align:right; width:15%;border:1px solid #cccccc;border-left:none;border-right:none;"><?php 
					$balance = $prevYearOpeningBalance + $cashOpeningBalance + ($totalReceivedAmount - $totalPaidAmount);
					echo number_format(abs($balance),2);
					if(($balance) > 0)
					{
						echo " Dr";
					}
					else
					{
						echo " Cr";	
					}?> </th> </tr>      
            </table>                      
        </div>
        </div>
      </center> 
      </div>  
    </body>
</html>


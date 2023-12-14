<?php
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include "classes/cash_summary.class.php"; 
$objCashSummary = new CashSummary($dbConn);
include_once("classes/dbconst.class.php");

if($_POST['from_date'] == '' && $_POST['to_date'] == '' )
{?>
<script>
	window.location.href="common_period.php?cashflow&temp";
</script>
<?php	
}

$objCashSummary->GetSocietyDetails($_SESSION['society_id']);
//$cashID = $objCashSummary->getCashID();

$cashOpeningBal = $objCashSummary->getOpeningBalance($_POST["from_date"], $_POST['ledgerid']);
//$cashOpeningBalance = $cashOpeningBal[sizeof($cashOpeningBal)-1]['ReceivedAmount']-$cashOpeningBal[sizeof($cashOpeningBal)-1]['PaidAmount'];
$cashOpeningBalance = $cashOpeningBal[0]['TReceivedAmount'] - $cashOpeningBal[0]['TPaidAmount'];
$totalPaid = $objCashSummary->getTotalPaidAmount($_POST['ledgerid'], $_POST["from_date"], $_POST['to_date']);
$totalReceived = $objCashSummary->getTotalReceivedAmount($_POST['ledgerid'], $_POST["from_date"], $_POST['to_date']);
$bankname = $objCashSummary->getBankName($_POST['ledgerid']);
$prevYearOpeningBalance = $objCashSummary->getPrevYrOpeningBal($_POST['ledgerid'], $_POST["from_date"]);
$balance = $prevYearOpeningBalance + $cashOpeningBalance + ($totalReceived - $totalPaid);
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
</style>		
	
</head>
<body>
    <center>    
    <div id="cash_flow" style="width:90%;">
        <div id="report_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:18px;"><?php echo $objCashSummary->objSocietyDetails->sSocietyName; ?></div>    
            <div id="society_reg" style="font-size:14px;"><?php if($objCashSummary->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objCashSummary->objSocietyDetails->sSocietyRegNo; 
				}
				?></div>
            <div id="society_address"; style="font-size:14px;"><?php echo $objCashSummary->objSocietyDetails->sSocietyAddress; ?></div>
        </div>
        <div id="financial_year" style="text-align:center;">            
            <div style="text-align:right;"><?php echo date('d.m.Y'); ?> </div>
        </div>
        <div style="text-align:center;font-weight:bold; font-size:16px;">Summarised Cash Flow Statement For <?php echo $bankname ; ?> </div>
        <div style="text-align:center;font-weight:bold; font-size:14px;"><?php echo getDisplayFormatDate($_POST['from_date']); ?> to <?php echo getDisplayFormatDate($_POST['to_date']); ?> </div>
        <br />
        <div id="cash_flow_report">
        	<table  style="width:50%;font-size:14px;border:1px solid black;padding:100;" align="center" > 
            	<tr>                	
                	<th style="text-align:left; width:35%;"> Previous Year Remaining Balance</th>  
                    <th style="text-align:right; width:15%;border:1px solid black;border-left:none;border-right:none;"><?php echo abs($prevYearOpeningBalance);
					if($prevYearOpeningBalance >= 0)
					{
						echo " Dr";
					}
					else
					{
						echo " Cr";	
					}	?> </th>
				</tr>                         
                <tr>                	
                	<th style="text-align:left; width:35%;"> Opening Balance </th>  
                    <th style="text-align:right; width:15%;border:1px solid black;border-left:none;border-right:none;"><?php echo abs($cashOpeningBalance); 
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
                	<th style="text-align:left; width:35%;"> Receipts </th>
                    <th style="text-align:right; width:15%;"><?php //echo $totalReceived; ?> </th>
                </tr>
                <tr>
                	<td style="text-align:left; width:35%;">&nbsp;&nbsp; Cash Received : </td>
                    <td style="text-align:right; width:15%;border:1px solid black;border-left:none;border-right:none;"> <?php echo number_format($totalReceived,2); ?> </td>
                </tr>
               
                <tr><td><br /></td></tr>
                <tr>
                	<th style="text-align:left; width:35%;"> Payments </th>
                    <th style="text-align:right; width:15%;"><?php //echo $totalPaid; ?> </th>
                </tr>
                <tr>
                	<td style="text-align:left; width:35%;">&nbsp;&nbsp; Cash Paid: </td>
                    <td style="text-align:right; width:15%;border:1px solid black;border-left:none;border-right:none;"> <?php echo number_format($totalPaid,2); ?> </td>
                </tr>               
                <tr>
                	<th style="text-align:left; width:35%;"> Balance </th>
                    <th style="text-align:right; width:15%;border:1px solid black;border-left:none;border-right:none;"><?php echo abs($balance);
					if(($balance) >= 0)
					{
						echo " Dr";
					}
					else
					{
						echo " Cr";	
					}?> </th>
                </tr>             
            </table>
            <h3><a href='cash_flow_details.php?ledgerid=<?php echo $_POST['ledgerid'] ?>&from_date=<?php echo $_POST['from_date'] ?>&to_date=<?php echo $_POST['to_date'] ?>'> Cash_Flow_Details </a></h3>                      
        </div>
        </div>                  
    </center>   
    </body>
</html>


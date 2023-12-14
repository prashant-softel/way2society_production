<?php 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include_once "classes/dbconst.class.php";

include "classes/include/fetch_data.php";
$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);

include_once "classes/income_register_report.class.php";
$obj_income_report=new income_report($dbConn);
$show_income_details=$obj_income_report->getIncomeDetailsNew(getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Contribution Ledger</title>
<style>
	table {
    	border-collapse: collapse;
	}
	table, th, td {
   		border:1px solid #cccccc;
		text-align:left;
	}	
</style>
<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
</head>

<body>
<center>
<div id="mainDiv" style="width:80%;">
		<?php include_once( "report_template.php" ); // get the contents, and echo it out.?>
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
            <div style="font-weight:bold; font-size:16px;">Contribution Ledger</div>
                        <div style="font-weight; font-size:16px;">FROM <?php echo getDisplayFormatDate($_SESSION['from_date']); ?> TO <?php echo getDisplayFormatDate($_SESSION['to_date']);?></div>
           
            
        </div>
        <table  style="width:100%;font-size:14px;">
                <tr>
                <th style="text-align:center;  border:1px solid #cccccc"colspan="2">Month</th>
                <th style="text-align:center;  border:1px solid #cccccc"" colspan="3">Account Name</th>
                <th style="text-align:center;  border:1px solid #cccccc"" colspan="3">Debit Amount</th>
                <th style="text-align:center;  border:1px solid #cccccc"" colspan="3">Credit Amount</th>
                <th style="text-align:center;  border:1px solid #cccccc"" colspan="3">Balance Amount</th>
               </tr>
               
               <?php
			   $count=0;
			   $totalAmount=0;
			  
			    if($show_income_details <> '')
			   {
				   $tempLedgerID=0;
				  $tempDate=0;
				  $BalanceAmount=0;
				  $Credit=0;
				  $Debit=0;
				  $FinalBalanceAmount=0;
				  $FinalCredit=0;
				  $FinalDebit=0;
				   
	
				for($i=0;$i<=sizeof($show_income_details);$i++)
				{
					if($show_income_details[$i]['LedgerID'] <> $tempLedgerID)
					{
						
						if($tempLedgerID <> 0)
						{
							echo "<tr style='border:1px solid #cccccc;'><td style='text-align:center;' colspan='2'></td><td style='text-align:center;' colspan=3><b>Total</b></td><td style='text-align:right;' colspan=3><b>".number_format($Debit,2)."</b></td><td style='text-align:right;' colspan=3><b>".number_format($Credit,1)."</b></td><td style='text-align:right;'colspan=3><b>".number_format($BalanceAmount,2)."</b></td></tr>";
						}
						if($i == sizeof($show_income_details))
						{
							break;
						}
						echo "<tr><td style='text-align:center;' colspan='2'></td><td  style='border-left:none;text-align:center;font-size:16px;' colspan='3'><b>".$show_income_details[$i]['TO']."</b></td><td colspan=3 style='border-left:none;text-align:right;'></td><td colspan=3 style='border-left:none;text-align:right;'></td><td colspan=3 style='border-left:none;text-align:right;'></td></tr>";
						$tempLedgerID=$show_income_details[$i]['LedgerID'];
						$BalanceAmount=0;
						$Credit=0;
				  		$Debit=0;
					}
					
					if($tempLedgerID==$show_income_details[$i]['LedgerID'])
					{
						$month = strtotime(getDisplayFormatDate($show_income_details[$i]['Date']));
						
						$Credit += (float)$show_income_details[$i]['Credit'];
						$Debit += (float)$show_income_details[$i]['Debit'];
						$BalanceAmount += (float)$show_income_details[$i]['Credit'] - (float)$show_income_details[$i]['Debit'];
						$FinalBalanceAmount +=$BalanceAmount;
						$FinalCredit +=$Credit;
						$FinalDebit +=$Debit;
						
					}
					echo "<tr><td style='text-align:left;' colspan='2'>&nbsp;".date('F', $month)."</td><td  style='border-left:none;text-align:left;' colspan='3'></td><td colspan=3 style='border-left:none;text-align:right;'>".number_format($show_income_details[$i]['Debit'],2)."</td><td colspan=3 style='border-left:none;text-align:right;'>".number_format($show_income_details[$i]['Credit'],2)."</td><td colspan=3 style='border-left:none;text-align:right;'>".number_format($BalanceAmount,2)."</td></tr>";
				}   
				   echo "<tr><td  style='text-align:center;background-color:#D2D2D2;' colspan=5>***GrandTotal***</td><td style='text-align:right;background-color:#D2D2D2;'  colspan=3>".number_format($FinalDebit,2)."</td><td style='text-align:right;background-color:#D2D2D2;' colspan=3>".number_format($FinalCredit,2)."</td><td style='text-align:right;background-color:#D2D2D2;' colspan=3>".number_format($FinalBalanceAmount,2)."</td></tr>";
				   
				}?>
</table>
</div>
</div>
</center>
</body>
</html>
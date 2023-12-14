<?php 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include_once "classes/dbconst.class.php";
include "classes/include/fetch_data.php";
$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
include_once "classes/investment_register_report.class.php";
$obj_investment_report=new investment_report($dbConn);
if($_POST['from_date'] <> '' && $_POST['to_date'] <> '' ) {

	$show_investment_details=$obj_investment_report->get_investmentregister_details($_POST['from_date'],$_POST['to_date']);
if($show_investment_details == ''){?>	
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
	window.location.href="common_period.php?investmentregister";
</script>	
	
	
<?php }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Investment Register Report</title>
<style>
	table {
    	border-collapse: collapse;
	}
	table, th, td {
   		border: 0px solid black;
		text-align:left;
	}
	tr:hover {background-color: #f5f5f5}	

	@page {
    size: A4 landscape;
    size: 400mm 200mm;
	
}

@media print
{
tr    { page-break-inside:avoid; page-break-after:auto }
td    { page-break-inside:avoid; page-break-after:auto }
thead { display:table-header-group }
tfoot { display:table-footer-group }

}
</style>

  

<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
</head>

<body>
<div id="mainDiv" >
<?php include_once( "report_template.php" ); // get the contents, and echo it out.?>
<div  style="border:1px solid #cccccc; display:block; overflow:auto;">
        <div id="bill_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:18px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
            <!--<div id="society_type" style="font-weight:bold; font-size:20px;">PREMISES CO-OPERATIVE SOCIETY LTD.</div>-->
            <div id="society_reg" style="font-size:14px;"><?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{ 
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?>
            </div>
            <div id="society_address"; style="font-size:16px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>
        </div>
        <div id="bill_subheader" style="text-align:center;">
            <div style="font-weight:bold; font-size:16px;">Investment Register</div>
            <div style="font-weight; font-size:16px;">From<?php echo '   '.$_POST['from_date'].' ';?> To<?php echo '   '.$_POST['to_date'].'      ';?></div>
            <div style="font-weight; font-size:16px;">As on Date:<?php echo date("d.m.Y");?></div>
           
            
        </div>
		<?php
		$YearDate =$obj_investment_report->GetYearDateAndDesc($_SESSION['society_creation_yearid']);
					for($k=0; $k<sizeof($YearDate); $k++)
					{
						if( $accrud_amount[$k][0]['Date'] >= $YearDate[$k]['BegninigDate'] &&  $accrud_amount[$k][0]['Date'] <= $YearDate[$k]['EndingDate'] )
						{
							$AccInt = 'Acc.Interest.'.$YearDate[$k]['YearDesc'];
							if( $accrud_amount[$k][0]['By'] <> '')
							{
								$result2[$i][$AccInt]= $accrud_amount[$k][0]['Debit'];
							}
							else 
							{
								$result2[$i][$AccInt]=0;//$LinkedVoucher[0][$j]['Credit'];
								$result2[$i][$AccInt]='-'. $accrud_amount[$k][0]['Credit'];
								
							}
						 }
						 else
						 {
							$AccInt = 'Acc.Interest.'.$YearDate[$k]['YearDesc'];
							$result2[$i][$AccInt]=0; 
						 }
					 }	
					 ?>

        <table  id ="fit-to-page" style="width:100%;font-size:13px; " >
                <tr>
                <th style="text-align:center;  border:1px solid #cccccc; border-left:none;" rowspan ="2">Sr.No.</th>
                <th style="text-align:center;  width:10%; border:1px solid #cccccc;"rowspan ="2"> Date of investment </th>
				<th style="text-align:center;  width:10%; border:1px solid #cccccc;"rowspan ="2"> Details of Investments</th>
                <th style="text-align:center;  width:15%; border:1px solid #cccccc;" rowspan ="2"> Name of Institution/Bank  </th>
                <th style="text-align:center;  width:10%; border:1px solid #cccccc;"rowspan ="2" > Total Amount Invested  </th>
                <th style="text-align:center;  border:1px solid #cccccc;" colspan="3"rowspan ="2"> Rate of Dividend Interest</th>
				<?php
				$header = 'Accrued Interest per Annum
				(Period/Amount) Rs. &nbsp';
				$counter = 0;
				foreach($result2 as $row2)
					 {
						foreach(array_keys($row2) as $row_header){
							$counter++;
							
						}
					}
               ?>
			<?php   
			     echo '<th style="text-align:center;  width:60%;  border:1px solid #cccccc;"colspan='.$counter.'>'.$header.
				 '</th>';
			?>
				<th style="text-align:center;  width:10%; border:1px solid #cccccc;" colspan="3" rowspan ="2">&nbsp;Interest&nbsp; </th>
                <th style="text-align:center;  width:10%; border:1px solid #cccccc;" colspan="3" rowspan ="2">&nbsp;Date of Realization&nbsp; </th>
				<th style="text-align:center;  width:10%; border:1px solid #cccccc; " colspan="3" rowspan ="2"> &nbsp;T.D.S. Details (Rs)&nbsp;</th>
                <th style="text-align:center;  width:20%; border:1px solid #cccccc; " colspan="3" rowspan ="2">Amount Realized (Rs)</th>
				<th style="text-align:center;  width:10%; border:1px solid #cccccc; " colspan="3" rowspan ="2"> Remarks </th>
                </tr>
				<?php

		            foreach($result2 as $row2)
					 {
						echo '<tr>';
						foreach(array_keys($row2) as $row_header){
							echo '<th style="text-align:center;  width:10%; border:1px solid #cccccc;">'.$row_header.'</th>';
							
						}
						echo '</tr>';
					}
					
					?>
            
               <?php
			   	$count=0;
	
               if($show_investment_details <> '')
			   {
				   
				   //print_r($show_payment_details);
				foreach($show_investment_details as $key=>$val)
				{
                      
                    $accrud_amount = $obj_investment_report->GetAccrudAmount($show_investment_details[$key]['id'],$show_investment_details[$key]['accrued_interest_legder']);
					
					for($k=0; $k<sizeof($YearDate); $k++)
					{
						//$AccInt = 'Acc.Interest.'.$YearDate[$k]['YearDesc'];
						//$result[$i][$AccInt]=0;
						if( $accrud_amount[$k][0]['Date'] >= $YearDate[$k]['BegninigDate'] &&  $accrud_amount[$k][0]['Date'] <= $YearDate[$k]['EndingDate'] )
						{
							$AccInt = 'Acc.Interest.'.$YearDate[$k]['YearDesc'];
							if( $accrud_amount[$k][0]['By'] <> '')
							{
								$result2[$i][$AccInt]= $accrud_amount[$k][0]['Debit'];
							}
							else 
							{
								$result2[$i][$AccInt]=0;//$LinkedVoucher[0][$j]['Credit'];
								$result2[$i][$AccInt]='-'. $accrud_amount[$k][0]['Credit'];
							}
						 }
						 else
						 {
							$AccInt = 'Acc.Interest.'.$YearDate[$k]['YearDesc'];
							$result2[$i][$AccInt]=0; 
						 }
					 }	
                     $flag = false;
		             $skip = false;
				
				
					$TDSRecieved =$obj_investment_report->GetTDSReceivable($show_investment_details[$key]['id']);
				
					if($TDSRecieved == '')
					{
						$TDSRecieved = "0.00";
					}
					$count++;
					$bank_name= $show_investment_details[$key]['ledger_name'] ;
					$date_of_investment = $show_investment_details[$key]['deposit_date'] ;
                    $Total_amount_invested = $show_investment_details[$key]['principal_amt'];
					$rateof_dividendint = $show_investment_details[$key]['int_rate'];
					$interest = $show_investment_details[$key]['interest'];
					$details_of_investment = $show_investment_details[$key]['fdr_no'];
					$amount_realized = $show_investment_details[$key]['maturity_amt'];
					$date_realized = $show_investment_details[$key]['maturity_date'];
                    $interest_accrued = $show_investment_details[$key]['interest_accrued'];
				    $remark =  $show_investment_details[$key]['note'];
					
					
				echo"<tr><td style='border:1px solid #cccccc;text-align:center;border-left:none;'>".$count."</td>";
				echo "<td  style='border:1px solid #cccccc;text-align:center;border-left:none;'>".$date_of_investment."</td>";
				echo"<td style='border:1px solid #cccccc;border-left:none;text-align:center; '>".$details_of_investment."</td>";
				echo"<td  style='border:1px solid #cccccc;border-left:none;text-align:center;'>".$bank_name."</td>";
				echo"<td style='border:1px solid #cccccc;border-left:none;text-align:center; '> ".$Total_amount_invested."</td>";
				echo"<td colspan=3 style='border:1px solid #cccccc;border-left:none;text-align:center;'>".$rateof_dividendint."</td>";
				foreach($result2 as $row2)
					 {

						foreach(array_values($row2) as $row_data)
						{
							echo '<td style="border:1px solid #cccccc;text-align:center;border-left:none;">'.$row_data.'</th>';
						}
						
					
					}	 
					
					
					echo"<td colspan=3 style='border:1px solid #cccccc;border-left:none;text-align:center;'>".$interest."</td>
				    <td colspan=3 style='border:1px solid #cccccc;border-left:none;text-align:center;'>".$date_realized."</td>
					<td colspan=3 style='border:1px solid #cccccc;border-left:none;text-align:center;'>". $TDSRecieved."</td>
					<td colspan=3 style='border:1px solid #cccccc;border-left:none;text-align:center;'>".$amount_realized."</td>
					<td colspan=3 style='border:1px solid #cccccc;border-left:none;text-align:center;'>".$remark."</td>
			  </tr> " ;
				}   
				
				   
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
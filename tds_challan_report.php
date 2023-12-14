<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>W2S - TDS Challan Reports</title>
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
$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
include_once("classes/dbconst.class.php");
include_once "classes/utility.class.php";
include_once("classes/view_ledger_details.class.php");
$obj_ledger_details = new view_ledger_details($dbConn);
$result = $obj_ledger_details->getChallanList(getDBFormatDate($_SESSION['from_date']),  getDBFormatDate($_SESSION['to_date']));

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
            <div style="font-weight:bold; font-size:16px;">Challan Reports</div>
            <div style="font-weight; font-size:16px;">From 01-04-2020 To 28-10-2021</div>            
        </div>
        
        <table  style="width:100%;font-size:14px;border-collapse: collapse;">
        	<tr>
            	<th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc; border-left:none; width: 6%;">Sr. No.</th>
                <th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;width: 25%;">Date TDS Challan Paid in Bank</th>
                <th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;width: 12%;">Challan No</th>
                <th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;width: 12%;">Amount Of Challan</th>
                <th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;width: 15%; " >Bank Name</th>
                <th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;width: 12%; ">BSR Code</th>
                
            </tr>
            <?php 
			if($result <> '')
			{
				$cnt =1;
				$TotalChallanAmount = 0;	
				foreach($result as $k => $v)
				{
					$TotalChallanAmount = $TotalChallanAmount+$result[$k]['TotalAmount'];
					$GetBankName = $obj_ledger_details->GetBankName($result[$k]['BankId']);
				?>
					<tr>
            		<td style="text-align:center;border-collapse: collapse;border:1px solid #cccccc; border-left:none;"><?php echo $cnt ?></td>
                	<td style="text-align:center;border-collapse: collapse;border:1px solid #cccccc; " ><?php echo getDisplayFormatDate($result[$k]['Challan_date']); ?></td>
                	<td style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;"><?php if($result[$k]['ChallanNo']==''){echo '---';}else{echo $result[$k]['ChallanNo'];}?></td>
                	<td style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;  "><?php echo $result[$k]['TotalAmount']; ?></td>
                	<td style="text-align:center;border-collapse: collapse;border:1px solid #cccccc; " ><?php echo $GetBankName;?></td>
                	<td style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;"><?php if($result[$k]['BSR_Code']==''){echo '---';}else{echo $result[$k]['BSR_Code'];}?></td>
                
            </tr>
				
				<?php 
				$cnt++;
				}?>
			
            
             
            <!-- Total Amount Calculation -->
             <tr>
            	<td colspan="3" style="text-align:center;background-color: #D3D3D3;border-collapse: collapse;border:1px solid #cccccc;border-left:none; ">**Total **</td>
                <td style="text-align:center;background-color: #D3D3D3;border-collapse: collapse;border:1px solid #cccccc;border-left:none; " ><?php echo number_format($TotalChallanAmount,2)?></td>
                <td style="text-align:center;background-color: #D3D3D3;border-collapse: collapse;border:1px solid #cccccc;border-left:none; " >&nbsp;</td>
                <td style="text-align:center;background-color: #D3D3D3;border-collapse: collapse;border:1px solid #cccccc;border-left:none; " ></td>
            </tr>
            
        </table>
        <?php }
			?>
        </div>
   </div>
 
</body> 
</html>
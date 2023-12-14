<?php 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include "classes/include/fetch_data.php";
$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
include_once "classes/dbconst.class.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Share Register</title>
<style>
	table {
    	border-collapse: collapse;
	}
	table, th, td {
   		border: 1px solid #cccccc;
		text-align:left;
	}	
	.hover
	{
		cursor:pointer;
	}
</style>


</head>

<body>
<div id="mainDiv">

<?php 
$arrayUnit = array();
$sql01 = "select unit_id, share_certificate from unit";
$sql11 = $dbConn->select($sql01);
$k = 0;
for($z=0;$z<sizeof($sql11);$z++)
{
	if($sql11[$z]['share_certificate'] <> "")
	{
		$arrayUnit[$k] = $sql11[$z]['unit_id'];
		$k++;
	}
}
/*echo "<pre>";
print_r($arrayUnit);
echo "</pre>";
die();*/

?>

<?php include_once( "report_template.php" ); // get the contents, and echo it out.?>

<div style="border: 1px solid #cccccc;">

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
            <div style="font-weight:bold; font-size:22px;">SHARE REGISTER</div>
        </div>        
		
        <?php
		
		?>
        <table style="width:100%" border="1">
        	<tr>
            	<th><center>Serial No.</center></th>
                <th><center>Date of allotment of Share</center></th>
                <th><center>Cash Book Folio No.</center></th>
                <th><center>Share Certificate No.</center></th>
                <th><center>No. of Shares</center></th>
                <th><center>Value of Shares Rs.</center></th>
                <th><center>Name of the Member to whom Shares alloted</center></th>
                <th><center>Date of transfer/ refund</center></th>
                <th><center>Cash Book Journal Folio</center></th>
                <th><center>No. of Shares transferred or refunded</center></th>
                <th><center>Share Certificate Nos. transferred or refunded</center></th>
                <th><center>Share Value transferred or refunded Rs.</center></th>
                <th><center>Name of the Transferee or the person receiving refund</center></th>
                <th><center>Authority for transfer or refund</center></th>
                <th><center>Remarks</center></th> 
            </tr>
            
            <tr>
            <?php for($i = 1; $i <= 15; $i++)
			{ ?>
            	<th><center><?php echo $i ?></center></th>
            <?php } ?>

            </tr>
            
            <?php
				$toDisplayArray = $objFetchData->getDataForShare_register();
				/*echo "<pre>";
				print_r($toDisplayArray);
				echo "</pre>";*/
			?>

            <?php for($i=0; $i<sizeof($toDisplayArray); $i++)
				  {
					  //$toDisplayArray = $objFetchData->getDataForShare_register($arrayUnit[$i]);
			?>
            <tr>
            	<!--<td><center><input type="text" id="serial_no" name="serial_no" size="3" value="<?php echo ($i + 1); ?>" style="border:none; text-align:center;" readonly="readonly"/></center></td>
                
                <td width="25px"><center><input type="text" id="date_of_share" name="date_of_share" value="" readonly="readonly" style="text-align:center; border:none" size="7" /></center></td>
                
                <td><center><input type="text" id="folio_no" name="folio_no" value="" readonly="readonly" style="text-align:center; border:none" size="7" /></center></td>
                
                <td><center><input type="text" id="share_cert_no" name="share_cert_no" value="<?php echo $toDisplayArray[0]['share_certificate'];?>" readonly="readonly" style="border:none; text-align:center" size="12" /></center></td>                
                
                <td><center><input type="text" id="no_of_shares" name="no_of_shares" value="" readonly="readonly" style="border:none; text-align:center" size="7" /></center></td>
                
                <td><center><input type="text" id="value_of_shares" name="value_of_shares" value="" readonly="readonly" style="border:none; text-align:center" size="7"  /></center></td>
                
                <td><center><textarea id="name_of_mem" name="name_of_mem" readonly="readonly" cols="30" <?php if(strlen($toDisplayArray[0]['primary_owner_name']) < 70) { ?> rows="1" <?php } else if(strlen($toDisplayArray[0]['primary_owner_name']) > 70) { ?> rows="2" <?php } ?> style="text-align:center; resize:none; overflow:hidden; border:none"><?php echo $toDisplayArray[0]['primary_owner_name']; ?></textarea></center></td>
         
                <td><center><input type="text" id="date_of_trans_refund" name="date_of_trans_refund" readonly="readonly" style="border:none; text-align:center" size="7" /></center></td>
                
                <td><center><input type="text" id="cash_book_journal_folio" name="cash_book_journal_folio" readonly="readonly" style="border:none; text-align:center" size="7" /></center></td>

                <td><center><input type="text" id="no_of_shares_trans_refund" name="no_of_shares_trans_refund" readonly="readonly" style="border:none; text-align:center" size="7" /></center></td>

                <td><center><input type="text" id="share_cert_nos_trans_refund" name="share_cert_nos_trans_refund" readonly="readonly" style="border:none; text-align:center" size="7" /></center></td>

                <td><center><input type="text" id="share_value_trans_refund" name="share_value_trans_refund" readonly="readonly" style="border:none; text-align:center" size="7" /></center></td>

                <td><center><input type="text" id="name_of_transferee" name="name_of_transferee" readonly="readonly" style="border:none; text-align:center" size="7" /></center></td>

                <td><center><input type="text" id="authority" name="authority" readonly="readonly" style="border:none; text-align:center" size="7" /></center></td>

                <td><center><input type="text" id="remarks" name="remarks" readonly="readonly" style="border:none; text-align:center" size="7" /></center></td>-->
                        	
            	<td><center><?php echo $toDisplayArray[$i]['iid']; ?></center></td>
                
                <td><center><?php echo getDisplayFormatDate($toDisplayArray[$i]['ownership_date']); ?></center></td>
                
                <td><center></center></td>
                
                <td><center><?php echo $toDisplayArray[$i]['share_certificate']; ?></center></td>                
                
                <td><center><?php if($toDisplayArray[$i]['share_certificate'] != '') { echo (($toDisplayArray[$i]['share_certificate_to'] - $toDisplayArray[$i]['share_certificate_from']) + 1); } ?></center></td>
                
                <td><center><?php if($toDisplayArray[$i]['share_certificate'] != '') { echo $toDisplayArray[$i]['amt_per_share']; } ?></center></td>
                
                <td><center><a style="color:#00C" class="hover" onclick="window.open('view_member_profile.php?scm&id=<?php echo $toDisplayArray[$i]['member_id']; ?>&tik_id=<?php echo time();?>&m&view','_blank')"><?php echo $toDisplayArray[$i]['owner_name']; ?></a></center></td>
         
                <td><center><?php echo getDisplayFormatDate($toDisplayArray[$i]['second_ownership_date']); ?></center></td>
                
                <td><center></center></td>

                <td><center><?php if($toDisplayArray[$i]['share_certificate'] != '') { if($toDisplayArray[$i]['ownership_status'] == 0) { echo (($toDisplayArray[$i]['share_certificate_to'] - $toDisplayArray[$i]['share_certificate_from']) + 1); } } ?></center></td>

                <td><center><?php if($toDisplayArray[$i]['ownership_status'] == 0) { echo $toDisplayArray[$i]['share_certificate']; } ?></center></td>

                <td><center><?php if($toDisplayArray[$i]['share_certificate'] != '') { if($toDisplayArray[$i]['ownership_status'] == 0) { echo $toDisplayArray[$i]['amt_per_share']; } } ?></center></td>

                <td><center><?php echo $toDisplayArray[$i]['second_owner']; ?></center></td>

                <td><center></center></td>

                <td><center></center></td>
            
            </tr>        
  
  <?php 
}
?>

</table>
</div>
  </div>
</body>
</html>
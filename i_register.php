<?php 
error_reporting(7);
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include "classes/include/fetch_data.php";
$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
include_once "classes/dbconst.class.php";
include_once("classes/utility.class.php");
$obj_Utility =  new utility($dbConn);
$memberIDS = $obj_Utility->getMemberIDs($_SESSION['default_year_end_date']);	

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>I Register</title>
<style>
	table {
    	border-collapse: collapse;
	}
	table, th, td {
   		border: 1px solid #cccccc;
		text-align:left;
		font-weight:normal;
	}
	
	.for_page_break {
		page-break-after: always;
	}
</style>


</head>

<body>
<div id="mainDiv">


<?php
//$_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] = 1;

include_once("report_template.php"); // get the contents, and echo it out.

/*echo "<pre>";
print_r($_SESSION);
echo "</pre>";*/
?>
<?php
$toDisplayArray = $objFetchData->getDataForI_register($_REQUEST['iid']);
?>
<center>
<!--<input type="button" id="Edit" name="Edit" value="Edit" width="300" style="width:60px; height:30px; font-size:20px" onclick="window.location.href='i_register.php?edit&iid=<?php echo $_REQUEST['iid']; ?>'"  />-->

<input type="button" id="Edit" name="Edit" value="Edit" width="300" style="width:60px; height:30px; font-size:20px" onclick="window.open('view_member_profile.php?scm&id=<?php echo $toDisplayArray[0]['member_id']; ?>&tik_id=<?php echo time();?>&m&view','_blank')"  />
<?php 

if(isset($_REQUEST['edit']))
{
	?>
    <script>
	document.getElementById("Edit").hidden = true;
	</script>
    <input type="button" id="Update" name="Update" value="Update" width="300" style="left:550; width:100px; height:30px; font-size:20px" onclick="window.location.href='i_register.php?iid=<?php echo $_REQUEST['iid']; ?>'"  />
    <?php
}
?>
</center>
<?php
/*$arrayUnit = array();
//echo "in req: ".$_REQUEST['mem_id'];
if(!empty($_REQUEST['mem_id']) || $_REQUEST['mem_id'] == 0)
{
	if((isset($_REQUEST['edit']) || !is_array($_REQUEST['mem_id'])) && $_REQUEST['mem_id'] != 0)
	{
		$_REQUEST['mem_id'] = explode(",",$_REQUEST['mem_id']);	
	}
	//echo "req: ".$_REQUEST['mem_id'];
	if(sizeof($_REQUEST['mem_id']) == 1 && $_REQUEST['mem_id'][0] == 0)
	{
		$sql = "select unit.unit_id from `unit` JOIN `member_main` on unit.unit_id = member_main.unit where unit.society_id = '" . $_SESSION['society_id'] . "' and  member_main.member_id IN ($memberIDS) ORDER BY unit.sort_order ASC";
		$sql01 = $dbConn->select($sql);
		for($m=0;$m<sizeof($sql01);$m++)
		{
			$arrayUnit[] = $sql01[$m]['unit_id'];
		}
	}
	else
	{
		foreach($_REQUEST['mem_id'] as $check)
		{
			$arrayUnit[] = $check;
		}
	}
	/*echo "<pre>";
	print_r($arrayUnit);
	echo "</pre>";
	die();
}
*/
//for($i=0; $i<sizeof($arrayUnit); $i++)
{
?>
<?php 
$society_name = $objFetchData->objSocietyDetails->sSocietyName;
$society_address = $objFetchData->objSocietyDetails->sSocietyAddress;
?>
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
            <div style="font-weight:bold; font-size:22px;">FORM "I" [See Rule 32 and 65(1)]</div>
            <div style="font-weight:bold; font-size:20px;">REGISTER OF MEMBERS</div>
            <div style="font-weight:bold; font-size:16px;">[Section 38 (1) of the Maharashtra Co - operative Societies' Act, 1960]</div>
           	         
        </div>        
		
        
        <?php 
		?>
        <table style="width: 100%" border="1">
        <tr>
        	<th style="width: 15%; border-right: none">1. Serial Number:</th>
            <td style="border-left: none; width:35%;"><?php echo $toDisplayArray[0]['iid']; ?></td>
            <th style="width: 20%; border-right: none">2. Date of admission:</th>
            <td style="border-left: none; width:40%;"><?php echo getDisplayFormatDate($toDisplayArray[0]['ownership_date']); ?></td>
        </tr>
        
        <tr>
        	<th style="width: 25%; border-right: none" colspan="2">3. Date of Payment of entrance fees:</th>
            <td colspan="2" style="border-left: none"><?php ?></td>
        </tr>
        
        <tr>
        	<th style="border-right: none">4. Full Name:</th>
            <td style="border-left: none" colspan="3"><?php echo trim($toDisplayArray[0]['owner_name']); ?></td>
        </tr>
        
        <tr>
        	<th style="border-right: none" colspan="2">5. Address: </th>
            <td style="border-left: none" colspan="2"><?php echo trim($toDisplayArray[0]['alt_address']); ?></td>
        </tr>
        
        <tr>
        	<th colspan="2" style="border-right: none">Permanent: </th>
            <td colspan="2" style="border-left: none"><?php ?></td>
        </tr>
        
        <tr>
        	<th colspan="2" style="border-right: none">Residential: </th>
            <td colspan="2" style="border-left: none"><?php if($toDisplayArray[0]['alt_address'] == '') { echo $toDisplayArray[0]['unit_no'].',',$society_name.','.$society_address; } else { echo $toDisplayArray[0]['alt_address']; } ?></td>
        </tr>
        
        <tr>
        	<th style="border-right: none;">6. Occupation: </th>
            <td style="border-left: none;"><?php ?></td>
            <th style="border-right: none;">7. Age on the date of admission: </th>
            <td style="border-left: none"><?php ?></td>
        </tr>
        
        <tr>
        	<th colspan="2">8. Full Name and address of the person nominated by the member under section 30 (1):</th>
            <td colspan="2"><?php ?></td>
        </tr>
        
        <tr>
        	<th style="border-right: none">9. Date of nomination: </th>
            <td style="border-left: none"><?php ?></td>
            <th style="border-right: none">10. Date of cessation of membership:</th>
            <td style="border-left: none"><?php ?></td>
        </tr>
        
        <tr>
        	<th colspan="2" style="border-right: none">11. Reason for cessation: </th>
            <td colspan="2" style="border-left: none"><?php ?></td>
        </tr>
        
        <tr>
        	<th colspan="2" style="border-right: none">12. Remarks: </th>
            <td colspan="2" style="border-left:none"><?php ?></td>
        </tr>
        </table>
        <br /><br />
        <table style="width: 100%" border="1">
			<tr>
				<th rowspan="3"><center>Date</center></th>
				<th rowspan="3"><center>Cash Book folio</center></th>
				<th colspan="4"><center>PARTICULARS FOR SHARES HELD</center></th>
				<th rowspan="3"><center>Total Amount received</center></th>
				<th rowspan="3"><center>No. of Shares held</center></th>
			    <th rowspan="3"><center>Serial No. of Share certificate</center></th>
			</tr>
  
		    <tr>
			  	<th rowspan="2"><center>Application</center></th>
			    <th rowspan="2"><center>Allotment</center></th>
			    <th colspan="2"><center>Amount received on</center></th>
			</tr>
  
			<tr>
			  	<th><center>1st Call</center></th>
			    <th><center>2nd Call</center></th>
			</tr>
  
		    <tr style="height:20px">
			  	<td><?php if($toDisplayArray[0]['share_certificate'] != '') { echo getDisplayFormatDate($toDisplayArray[0]['ownership_date']); } ?></td>
			    <td></td>
			    <td></td>
			    <td></td>
			    <td></td>
			    <td></td>
			    <td><?php if($toDisplayArray[0]['share_certificate'] != '') { echo (($toDisplayArray[0]['no_of_shares']) * ($toDisplayArray[0]['amt_per_share'])); } ?></td>
			    <td><?php if($toDisplayArray[0]['share_certificate'] != '') { echo $toDisplayArray[0]['no_of_shares']; } ?></td>
			    <td><?php echo $toDisplayArray[0]['share_certificate']; ?></td>
			</tr>
            <?php 
			for($i = 0; $i < 5; $i++)
			{
				?>
                <tr style="height:20px">
				<td></td>
			    <td></td>
			    <td></td>
			    <td></td>
			    <td></td>
			    <td></td>
			    <td></td>
			    <td></td>
			    <td></td>
                </tr>
            	<?php
			}
			?>
		</table>
        <br /><br />
        <center><b>PARTICULARS OF SHARES TRANSFERRED OR SURRENDERED</b></center>
    <table border="1" style="width:100%">
    <tr>
      <th rowspan="2"><center>Date</center></th>
      <th rowspan="2"><center>Cash Book folio</center></th>
      <th rowspan="2"><center>Date</center></th>
      <th rowspan="2"><center>Cash Book folio or shares transfer register No.</center></th>
      <th><center>No. of Shares Certificate</center></th>
      <th rowspan="2"><center>No. of Shares transferred or refunded</center></th>
      <th colspan="2"><center>Balances</center></th>
      <th colspan="2" style="border-bottom: none"><center>Amount</center></th>
    </tr>

    <tr>
    	<th><center>SI. No. of Share Certificate</center></th>
        <th><center>No. of Shares held</center></th>
        <th><center>Serial No. of Share Certificate</center></th>
        <th style="border-top: none; border-right:none"><center>Rs.</center></th>
        <th style="border-top: none; border-left: none"><center>P.</center></th>
    </tr>
    
    <tr style="height:20px">
	  	<td></td>
	    <td></td>
	    <td></td>
	    <td></td>
	    <td><?php if($toDisplayArray[0]['ownership_status'] == 0) { echo $toDisplayArray[0]['share_certificate']; } ?></td>
	    <td><?php if($toDisplayArray[0]['ownership_status'] == 0) { echo $toDisplayArray[0]['no_of_shares']; } ?></td>
	    <td><?php if($toDisplayArray[0]['ownership_status'] == 0) { echo $toDisplayArray[0]['no_of_shares']; } ?></td>
	    <td><?php if($toDisplayArray[0]['ownership_status'] == 0) { echo $toDisplayArray[0]['share_certificate']; } ?></td>
	    <td><?php if($toDisplayArray[0]['ownership_status'] == 0) { echo (($toDisplayArray[0]['no_of_shares']) * ($toDisplayArray[0]['amt_per_share'])); } ?></td>
        <td></td>
	</tr>
    
    <?php
	for($i = 0; $i < 5; $i++)
	{
		?>
        <tr style="height:20px">
			<td></td>
		    <td></td>
	    	<td></td>
		    <td></td>
		    <td></td>
	    	<td></td>
		    <td></td>
		    <td></td>
	    	<td></td>
	        <td></td>
        </tr>
    <?php
	}
	?>
  </table>
  </div>
    <br /><br />
  <div class="for_page_break"></div>

  <?php 
}
  //echo "in req 2: ".$_REQUEST['mem_id'];
//$_REQUEST['mem_id'] = implode(",",$_REQUEST['mem_id']);
?>
  </div>
</body>
</html>
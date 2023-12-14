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
<title>Nomination Register</title>
<style>
	table {
    	border-collapse: collapse;
	}
	table, th, td {
   		border: 1px solid #cccccc;
		text-align:left;
	}	
</style>


</head>

<body>
<div id="mainDiv">


<?php include_once( "report_template.php" ); // get the contents, and echo it out.?>

<?php 
$arrayUnit = array();
$sql01 = "select unit_id, nomination from unit where nomination != 0";
$sql11 = $dbConn->select($sql01);
for($z=0;$z<sizeof($sql11);$z++)
{
	$arrayUnit[$z] = $sql11[$z]['unit_id'];
}

/*echo "<pre>";
print_r($arrayUnit);
echo "</pre>";
die();*/
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
            <div style="font-weight:bold; font-size:22px;">REGISTER OF NOMINATION</div>
        </div>        
		
        <?php
		
		?>
        <table style="width:100%" border="1">
        	<tr>
            	<th style="width:5%"><center>Serial No.</center></th>
                <th style="width:25%"><center>Name of the Member making Nomination</center></th>
                <th style="width:8%"><center>Date of Nomination</center></th>
                <th style="width:25%"><center>Name/s of Nominee/s &amp; Address/es of the Nominee/s</center></th>
                <th style="width:8%"><center>Date of the Managing Committee Meeting in which the Nomination was recorded</center></th>
                <th style="width:9%"><center>*Date of any subsequent revocation of Nomination</center></th>
                <th style="width:20%"><center>Remarks</center></th>
            </tr>
            
            <tr>
            <?php 
			for($i=1;$i<=7;$i++)
			{
			?>
            	<th><center><?php echo $i; ?></center></th>
            <?php
			}
			?>
            </tr>
            <?php for($i=0; $i<sizeof($arrayUnit); $i++)
				  {
					  $toDisplayArray = $objFetchData->getDataForNomination_register($arrayUnit[$i]);
			?>
            <tr>
            	<td><center><?php echo ($i + 1); ?></center></td>
                
                <td><center><?php echo $toDisplayArray[0]['primary_owner_name']; ?></center></td>
                
                <td><center></center></td>
                
                <td><center><?php echo $toDisplayArray[0]['nominee_name']; ?></center></td>
                
                <td><center></center></td>

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
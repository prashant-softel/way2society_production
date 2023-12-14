<?php 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include "classes/include/fetch_data.php";
include "common/CommonMethods.php";

$objFetchData = new FetchData($dbConn);

$Header = "Accounts Maintained By Pavitra Associates Pvt Ltd";
if($_REQUEST["UnitID"] == "")
{
	echo "<script>alert('Error ! There are no UnitID passed to generate a bill');</script>";
	exit;
}
$BillDate = "";
$DueDate = "";
$BillNumber = "";
$BillFor = $objFetchData->GetBillFor($_REQUEST["PeriodID"]);
$WingID = $objFetchData->GetWingID($_REQUEST["UnitID"]);
$SocietyID = $objFetchData->GetSocietyID($_REQUEST["UnitID"]);
$UnitID = $_REQUEST["UnitID"];

$objFetchData->GetSocietyDetails($objFetchData->GetSocietyID($_REQUEST["UnitID"]));
$objFetchData->GetMemberDetails($_REQUEST["UnitID"]);
$objectBillRegister = new CBillRegister($dbConn);
$data = array();

$BillRegisterData = $objFetchData->GetValuesFromBillRegister($_REQUEST["UnitID"], $_REQUEST["PeriodID"]);
$iCounter = 0;
for($iVal = 0; $iVal < sizeof($BillRegisterData) ; $iVal++) 
{
	 $BillDetails = $BillRegisterData[$iVal]["value"];
	 $BillDate = $BillDetails->sBillDate;
	 $DueDate = $BillDetails->sDueDate;
	 //$BillNumber = $BillDetails->sBillNumber;
	 $HeaderAndAmount = array("key"=>$BillDetails->sHeader, "value"=> $BillDetails->sHeaderAmount);
	 array_push($data, $HeaderAndAmount);
}

$detail_values = $objFetchData->GetValuesFromBillDetails($_REQUEST["UnitID"],$_REQUEST["PeriodID"]);								
$BillNumber = $detail_values[0]["BillNumber"]; 				
$AdjCredit = $detail_values[0]["AdjustmentCredit"];
$InterestOnArrears = $detail_values[0]["InterestArrears"];
$PrinciplePreviousArrears = $detail_values[0]["PrevPrincipalArrears"];
$IntrestOnPreviousarrears = $detail_values[0]["PrevInterestArrears"];
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Maintanence Bill</title>
<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="js/ajax_new.js"></script>
<!--<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
<script type="text/javascript" src="javascript/jquery.clockpick.1.2.4.js"></script>
<script type="text/javascript" src="javascript/ui.core.js"></script>
<script type="text/javascript" src="javascript/ui.datepicker.js"></script>-->
<script language="JavaScript" type="text/javascript" src="js/validate.js"></script>
<style>
	table {
    	border-collapse: collapse;
	}
	table, th, td {
   		border: 0px solid black;
		text-align:left;
	}	
</style>
<script>

	 $(function()
	{
		$.datepicker.setDefaults($.datepicker.regional['']);
		$(".basics").datepicker({ 
		dateFormat: "dd-mm-yy", 
		showOn: "both", 
		buttonImage: "images/calendar.gif", 
		buttonImageOnly: true 
	})});
	
	function test()
	{
		document.getElementById('bill_address').style.left = "";
	}
	
	function PrintPage() 
	{
		//Get the print button and put it into a variable
		//alert("print called");
        var btnEdit = document.getElementById("Update");
		var btnPrint = document.getElementById("Print");
        //Set the print button visibility to 'hidden' 
        btnEdit.style.visibility = 'hidden';
		btnPrint.style.visibility = 'hidden';
        //Print the page content
        window.print();
        //Set the print button to 'visible' again 
        //[Delete this line if you want it to stay hidden after printing]
        btnEdit.style.visibility = 'visible';
		btnPrint.style.visibility = 'visible';
    }
	
	function ViewPDF()
	{
		var sData = document.getElementById('bill_main').innerHTML;
		
		var sHeader = '<html><head>';
		sHeader += '<style> table {	border-collapse: collapse; } table, th, td { border: 0px solid black; text-align: left; } </style>';	
		sHeader +=	'</head><body>';
		
		var sFooter =  '</body></html>';
		
		sData = sHeader + sData + sFooter;
		
		var sFileName = "testbill";
		
		var sURL = "viewpdf.php";
		var obj = {"data":sData, "file":sFileName};
		//remoteCall(sURL, obj, 'queryResult');
		
		//window.open('viewpdf.php?filename=' + sFileName + '&data=' + sData);
		
		//return;
		
		$.ajax({
			url : "viewpdf.php",
			type : "POST",
			data: { "data":sData, "filename":sFileName} ,
			success : function(data)
			{	
				window.open('files/' + sFileName + '.pdf');
			},
				
			fail: function()
			{
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});
	}
</script>
</head>
<body>
    <center>
    <Form Name ="form1" Method ="POST" ACTION = "process/Maintenance_bill_edit.Process.php">

	<input type="hidden" name="wing_id" value=<?php echo $WingID; ?> />
	<input type="hidden" name="unit_id" value=<?php echo $UnitID; ?> />
	<input type="hidden" name="society_id" value=<?php echo $SocietyID; ?> />
	<input type="hidden" name="period_id" value="<?php echo $_REQUEST["PeriodID"] ?>"/>
    
	<div align="center" style="alignment-adjust:middle; left:80px;">
		<INPUT TYPE="submit" id="Update" name="Bill" value="Update" width="300" style="left:550;width:100px;height:30px; font-size:20px">
 	</div>
    <div id="bill_main" style="width:90%; border:1px solid black;">
        <div id="bill_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:24px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
            <!--<div id="society_type" style="font-weight:bold; font-size:20px;">PREMISES CO-OPERATIVE SOCIETY LTD.</div>-->
            <div id="society_reg" style="font-weight:bold; font-size:18px;"><?php echo $objFetchData->objSocietyDetails->sSocietyRegNo; ?></div>
            <div id="society_address" style="font-weight:bold; font-size:18px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>
        </div>
        <div id="bill_subheader" style="text-align:center;">
            <p style="font-weight:bold; font-size:16px;">Maintenance Bill</p>
        </div>
        <div id="bill_details" style="text-align:center;border:1px solid black;">
            <table style="width:100%;">
            	<tr>
                	<td style="width:15%;">Name</td>
                    <td id='owner_name' colspan="3">Mr. <?php echo $objFetchData->objMemeberDetails->sMemberName; ?></td>
              	</tr>
                <tr>
                	<td style="width:15%;">Unit No.</td>
                    <td id='owner_unit'><?php echo $objFetchData->objMemeberDetails->sUnitNumber; ?></td>
                    <td style="width:10%">Bill No.</td>
                    <td id='bill_no' style="width:10%;"><input type="text" name = "BillNumber" id="txtBillNo" value="<?php echo $BillNumber ?>" style="background-color:#FFFF99" size="10"/></td>
              	</tr>
                <tr>
                	<td colspan="4" id='bill_for' style="text-align:center;">Bill For January 2014</td>
                </tr>
                <tr>
                	<td style="width:15%;">Parking No.</td>
                    <td id='owner_parking'><?php echo "".$objFetchData->objMemeberDetails->sParkingNumber; ?></td>
                    <td style="width:10%">Bill Date</td>
                    <td id='bill_date' style="width:10%;"><input type="text" name="BillDate" id="txtBillDate" value="<?php echo $BillDate ?>" style="background-color:#FFFF99" class="basics" size="10"/></td>
              	</tr>  
                <tr>
                	<td style="width:15%;"></td>
                    <td></td>
                    <td style="width:10%">Due Date</td>
                    <td id='bill_due' style="width:10%;"><input type="text" id="DueDate"  name="DueDate" value="<?php echo $DueDate ?>" style="background-color:#FFFF99"  class="basics" size="10"/></td>
              	</tr>      
            </table>
        </div>
        <div id="bill_charges">
        	<table  style="width:100%;">
                <tr>
                <th style="text-align:center; width:10%; border:1px solid black;">Sr. No.</th>
                <th style="text-align:center; border:1px solid black;"" colspan="3">Particulars of Charges</th>
                <th style="text-align:center; width:20%; border:1px solid black;"">Amount (Rs.)</th>
                </tr>
                <?php
                	$counter = 1;
					$SubTotal = 0;
					for($i=0; $i < sizeof($data) ;$i ++)
					{
						echo "<tr><td style='border:1px solid black;text-align:center;'>".$counter."</td><td colspan=3 style='border:1px solid black;text-align-left;'>". $objFetchData->GetHeadingFromAccountHead($data[$i]["key"]) ."</td><td align=right style='border:1px solid black;text-align:right;width:15%;'><input type='text' name=txth" .$data[$i]["key"]. " value=" .$data[$i]["value"] . " style='background-color:#FFFF99;text-align:right;' size='14' /></td></tr>";
						$SubTotal += $data[$i]["value"];
						$counter++;
					}
				?>
           </table>
           
			<?php
            	$sHeaderIDs = "";
			  	for($i=0; $i <= sizeof($data) ;$i ++)
			  	{
				  	$key = $data[$i]["key"];
				  	if($key <> "")
				  	{
					  	if($sHeaderIDs <> "")
					  	{
							$sHeaderIDs = $sHeaderIDs  .",". $key;
					  	}
					  	else
					  	{
						  	$sHeaderIDs =  $key;
					  	}
				  	}
				}
			  ?>
			  <input type="hidden" name="Journals" value= <?php echo $sHeaderIDs ?> />
           
		   <?php
  				//$AdjCredit = 0;
  				//$InterestOnArrears = 0;
  				//$PrinciplePreviousArrears = 0;
  				//$IntrestOnPreviousarrears = 0;
  				$BalanceAmout = 0;
				$BalanceAmout = $SubTotal + $AdjCredit + $InterestOnArrears + $PrinciplePreviousArrears + $IntrestOnPreviousarrears;
			?>
           <table style="width:100%;">
          		<tr>
                	<td colspan="3" rowspan="7" style="width:50%;">E.& O.E.</td>
                    <td style="width:20%;border:1px solid black;" colspan="2">Sub Total</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;"><?php echo number_format($SubTotal,2); ?></td>
                </tr> 
                <tr>
                	<td style="width:20%;border:1px solid black;" colspan="2">Adjustment Credit/Rebate</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;"><input type="text" id="txtRebate" name="txtRebate" value='<?php echo number_format($AdjCredit,2); ?> ' style="background-color:#FFFF99; " size="14"/></td>
                </tr>
                <tr>
                	<td style="width:20%;border:1px solid black;" colspan="2">Interest on Arrears</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;"><?php echo number_format($InterestOnArrears,2); ?></td>
                </tr>
                <tr>
                	<td style="width:20%;border:1px solid black;"colspan="2">Previous Arrears</td>
                    <td id="sub_total" style="text-align:right;width:20%; border:none;"></td>
                </tr>
                <tr>
                	<td style="width:10%;border:1px solid black;">Principle</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;"><?php echo number_format($PrinciplePreviousArrears,2); ?></td>
                    <td style="border:none;"></td>
                </tr>
                <tr>
                	<td style="width:10%;border:1px solid black;">Interest</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;"><?php echo number_format($IntrestOnPreviousarrears,2); ?></td>
                    <td style="border:none;"></td>
                </tr>
                <tr>
                	<td style="width:20%;border:1px solid black;" colspan="2">Balance Amount</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;"><?php echo number_format($BalanceAmout, 2); ?></td>
                </tr>
                <tr>
                	<td style="width:20%;border:1px solid black;" colspan="6">
                    <?php
                    {
						?>
                     In Words : <?php  echo "Rupees ". convert_number_to_words($BalanceAmout) . ' Only.';
					}
					?>
                     </td>
                </tr>
	       </table>
           
        </div>
        <div id="bill_notes" style="text-align:left;">
            Notes:
            <ol style="list-style-type:decimal;">
                <li>Notes 1</li>
                <li>Notes 2</li>
            </ol>
        </div>
        <div id="bill_message">
        </div>
        <div id="bill_sign" style="text-align:right;border-top:1px solid black;border-bottom:1px solid black;padding-right:10px;">
        	<br><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?><br><br>Authorised Signature
        </div>
        <div id="bill_footer" style="text-align:left;border-top:1px solid black;border-bottom:1px solid black;padding-right:10px;">
        	<br><br>Accounts Maintained By Pavitra Associates Pvt Ltd
        </div>
    </div>
    </center>
    </Form>
    <input type="button" value="View As PDF"  onclick="ViewPDF();"/>
</body>
</html>
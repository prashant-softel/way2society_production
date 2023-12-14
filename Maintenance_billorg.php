<?php 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include "classes/include/fetch_data.php";
include "common/CommonMethods.php";
include_once("classes/dbconst.class.php");
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
$BillNotes = "";
$total = "";
$BillFor = $objFetchData->GetBillFor($_REQUEST["PeriodID"]);
$begin_endDate = $objFetchData->getBeginEndDate($_REQUEST["UnitID"], $_REQUEST["PeriodID"]);
if($begin_endDate <> "")
{	
	$StartDate = getDisplayFormatDate($begin_endDate[0]['BeginingDate']);
	$EndDate = getDisplayFormatDate($begin_endDate[0]['EndingDate']);									
}			
		
$objFetchData->GetSocietyDetails($objFetchData->GetSocietyID($_REQUEST["UnitID"]));
$objFetchData->GetMemberDetails($_REQUEST["UnitID"]);
$objectBillRegister = new CBillRegister($dbConn);
$data = array();
$wing_areaDetails = $objFetchData->getWing_AreaDetails($_REQUEST["UnitID"]);

$BillRegisterData = $objFetchData->GetValuesFromBillRegister($_REQUEST["UnitID"], $_REQUEST["PeriodID"]);

if(sizeof($BillRegisterData) == 0)
{
	echo "<br><br>Bill Not Generated For Unit : " . $objFetchData->GetUnitNumber($_REQUEST['UnitID']) . " For Period : " . $BillFor;
	die();
}

$receiptDetails = $objFetchData->getReceiptDetails($_REQUEST["UnitID"], $_REQUEST["PeriodID"], false, 0, true);
$iCounter = 0;
for($iVal = 0; $iVal < sizeof($BillRegisterData) ; $iVal++) 
{
	 $BillDetails = $BillRegisterData[$iVal]["value"];
	 $BillDate = getDisplayFormatDate($BillDetails->sBillDate);
	 $DueDate = getDisplayFormatDate($BillDetails->sDueDate);
	 $BillNotes = $BillDetails->sNotes;
	 //$BillNumber = $BillDetails->sBillNumber;
	 $HeaderAndAmount = array("key"=>$BillDetails->sHeader, "value"=> $BillDetails->sHeaderAmount);
	 array_push($data, $HeaderAndAmount);
}

$detail_values = $objFetchData->GetValuesFromBillDetails($_REQUEST["UnitID"],$_REQUEST["PeriodID"]);				
//echo "PrevArrears";
//echo $detail_values[0]["PrevPrincipalArrears"];
$BillNumber = $detail_values[0]["BillNumber"]; 				
$AdjCredit = $detail_values[0]["AdjustmentCredit"];
$InterestOnArrears = $detail_values[0]["InterestArrears"];
$PrinciplePreviousArrears = $detail_values[0]["PrincipalArrears"];//$detail_values[0]["PrevPrincipalArrears"];
$IntrestOnPreviousarrears = $detail_values[0]["InterestArrears"];//$detail_values[0]["PrevInterestArrears"];

$showInBillDetails = $objFetchData->GetFieldsToShowInBill($_REQUEST["UnitID"]);
$show_wing = $showInBillDetails[0]["show_wing"];
$show_parking = $showInBillDetails[0]["show_parking"];
$show_area = $showInBillDetails[0]["show_area"];
$show_receipt = $showInBillDetails[0]["bill_method"];
$show_shareCertificate = $showInBillDetails[0]["show_share"];
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Maintanence Bill</title>
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
<script>

	function test()
	{
		document.getElementById('bill_address').style.left = "";
	}
	
	function PrintPage() 
	{
		//Get the print button and put it into a variable
		//alert("print called");
        var btnEdit = document.getElementById("Edit");
		var btnPrint = document.getElementById("Print");
		var btnViewAsPDF = document.getElementById("viewbtn");
        //Set the print button visibility to 'hidden' 
        btnEdit.style.visibility = 'hidden';
		btnPrint.style.visibility = 'hidden';
		btnViewAsPDF.style.visibility = 'hidden';
        //Print the page content
        window.print();
        //Set the print button to 'visible' again 
        //[Delete this line if you want it to stay hidden after printing]
        btnEdit.style.visibility = 'visible';
		btnPrint.style.visibility = 'visible';
		btnViewAsPDF.style.visibility = 'visible';
    }
	
	function ViewPDF()
	{
		var sData = document.getElementById('bill_main').innerHTML;
		//var sData = document.body.innerHTML;
		
		var sHeader = '<html><head>';
		sHeader += '<style> table {	border-collapse: collapse; } table, th, td { border: 0px solid black; text-align: left; } </style>';	
		sHeader +=	'</head><body>';
		
		var sFooter =  '</body></html>';
		
		sData = sHeader + sData + sFooter;
		
		//var sFileName = "bill_<?php //echo $_REQUEST['UnitID']; echo $_REQUEST['PeriodID']; ?>" ;
		var sUnitNo = document.getElementById('owner_unit').innerHTML;
		var sFileName = "bill-<?php echo $objFetchData->objSocietyDetails->sSocietyCode; ?>-" + sUnitNo + "-<?php echo $BillFor; ?>" ;
		var sURL = "viewpdf.php";
		
		var obj = {"data":sData, "file":sFileName};
		
		//alert(sData);
		//remoteCall(sURL, obj, 'queryResult');
		
		//window.open('viewpdf.php?filename=' + sFileName + '&data=' + sData);
		
		//return;
		<?php if(isset($_REQUEST['gen']))
		{
			?>
			document.getElementById('bill_main').innerHTML = '';
			document.getElementById('Print').style.display = 'none';
			document.getElementById('viewbtn').style.display = 'none';
			document.getElementById('msg').innerHTML = 'Exporting...';
			<?php
		}
		?>
									
		$.ajax({
			url : "viewpdf.php",
			type : "POST",
			data: { "data":sData, 
					"filename":sFileName, 
					"society": "<?php echo $objFetchData->objSocietyDetails->sSocietyCode; ?>",
					"period": "<?php echo $BillFor; ?>"} ,
			success : function(data)
			{	
				<?php if(!isset($_REQUEST['gen']))
				{
					?>
					window.open('maintenance_bills/<?php echo $objFetchData->objSocietyDetails->sSocietyCode; ?>/<?php echo $BillFor; ?>/' + sFileName + '.pdf');
					<?php
				}
				else
				{
				?>
					//document.getElementById('msg').style.color = '#FF0000';
					document.getElementById('msg').innerHTML = '<font color="#009900"><a href="maintenance_bills/<?php echo $objFetchData->objSocietyDetails->sSocietyCode; ?>/<?php echo $BillFor; ?>/' + sFileName + '.pdf" target="blank"><img src="images/pdficon.png" /></a></font>';
					window.close();
				<?php
				}
				?>
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
	<div id="msg" style="color:#FF0000;"></div>
    <center>
    <Form Name ="form1" Method ="POST" ACTION =<?php echo "Maintenance_bill_edit.php?UnitID=". $_REQUEST["UnitID"] ."&PeriodID=". $_REQUEST["PeriodID"] ."" ?> 				>
	<div align="center" style="alignment-adjust:middle; left:80px;">
		<INPUT TYPE="button" id="Print" onClick="PrintPage()" name="Print!" value="Print!" width="300" style="width:60px;height:30px; font-size:20px">
 		<INPUT TYPE="submit" id="Edit" name="Bill" value="Edit" width="300" style="left:550;width:60px;height:30px; font-size:20px; display:none;">
 	</div>
    <div id="bill_main" style="width:90%;">
    <div style="border:1px solid black;">
        <div id="bill_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:18px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
            <!--<div id="society_type" style="font-weight:bold; font-size:20px;">PREMISES CO-OPERATIVE SOCIETY LTD.</div>-->
            <div id="society_reg" style="font-size:14px;"><?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?></div>
            <div id="society_address"; style="font-size:14px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>
        </div>
        <div id="bill_subheader" style="text-align:center;">
            <div style="font-weight:bold; font-size:16px;">Maintenance Bill [<?php echo $BillFor; ?>]</div>
        </div>
        <div id="bill_details" style="text-align:center;border-top:1px solid black;font-size:14px;">
            <table style="width:100%;">
            	<tr>
                	<td style="width:15%;">Name :</td>
                    <td id='owner_name'><?php echo $objFetchData->objMemeberDetails->sMemberName; ?></td>                    
              	</tr>
            </table>
            <table style="width:100%;">
                <tr>
                	<td style="width:15%;">Unit No :</td>
                    <td id='owner_unit'><?php echo $objFetchData->objMemeberDetails->sUnitNumber; ?></td>
                    <td style="width:10%">Bill No :</td>
                    <td id='bill_no' style="width:15%;"><?php echo $BillNumber ?></td>
              	</tr>
                <!--<tr>
                	<td colspan="4" id='bill_for' style="text-align:center;"><?php //echo 'Bill For ' .$BillFor?></td>
                </tr>-->
                <tr>
                	<td style="width:15%;<?php if(!$show_parking) { echo 'visibility:hidden;';} ?>">Parking No :</td>
                    <td id='owner_parking' style=" <?php if(!$show_parking) { echo 'visibility:hidden;';} ?>"><?php echo "".$objFetchData->objMemeberDetails->sParkingNumber; ?></td>
                    <td style="width:10%">Bill Date :</td>
                    <td id='bill_date' style="width:15%;"><?php echo $BillDate ?></td>
              	</tr>  
                <tr>
                	<td style="width:15%;<?php if(!$show_wing) { echo 'visibility:hidden;';} ?>">Wing :</td>
                   	<td style=" <?php if(!$show_wing) { echo 'visibility:hidden;';} ?>"><?php echo $wing_areaDetails[0]['wing'] ?></td>
                   	<td style="width:10%">Due Date :</td>
                   	<td id='bill_due' style="width:15%;"><?php echo $DueDate ?></td>
              	</tr> 
                <tr>
                 	<td style="width:15%;<?php if(!$show_area) { echo 'visibility:hidden;';} ?>">Area :</td>
                	<td id='area' style=" <?php if(!$show_area) { echo 'visibility:hidden;';} ?>"><?php echo $wing_areaDetails[0]['area'] . ' Sq.Ft'; ?></td>                    
                </tr>
            </table>
            <?php if($show_shareCertificate)
			{ ?>
            <table style="width:100%;">
                <tr>                	
                   	<td id='shareCertificate'>Share Certificate No :  <?php echo $objFetchData->GetShareCertificateNo($_REQUEST["UnitID"]); ?></td>					                
                </tr>    
            </table>
            <?php
			} ?>
        </div>
        <div id="bill_charges">
        	<table  style="width:100%;font-size:14px;">
                <tr>
                <th style="text-align:center; width:10%; border:1px solid black;border-left:none;">Sr. No.</th>
                <th style="text-align:center; border:1px solid black;"" colspan="3">Particulars of Charges</th>
                <th style="text-align:center; width:20%; border:1px solid black;border-right:none;">Amount (Rs.)</th>
                </tr>
                <?php
                	$counter = 1;
					$SubTotal = 0;
					for($i=0; $i < sizeof($data) ;$i ++)
					{
						if($data[$i]["key"] != INTEREST_ON_PRINCIPLE_DUE)
						{
							if($data[$i]["value"] <> 0)
							{
								echo "<tr><td style='border:1px solid black;border-left:none;text-align:center;font-size:14px;'>".$counter."</td><td colspan=3 style='border:1px solid black;text-align-left;font-size:12px;'>". strtoupper($objFetchData->GetHeadingFromAccountHead($data[$i]["key"])) ."</td><td align=right style='border:1px solid black;border-right:none;text-align:right;width:15%;font-size:14px;'>". number_format($data[$i]["value"], 2) ."</td></tr>";
								$SubTotal += $data[$i]["value"];
							}
						}
						else
						{
							$InterestOnArrears = $data[$i]["value"];
						}
						if($data[$i]["value"] <> 0)
						{
							$counter++;
						}
					}
				?>
           </table>
           <?php
		   		$BalanceAmout = 0;
				$BalanceAmout = $SubTotal + $AdjCredit + $InterestOnArrears + $PrinciplePreviousArrears + $IntrestOnPreviousarrears;
			?>
           <table style="width:100%;font-size:14px;">
          		<tr>
                	<td colspan="3" rowspan="7" style="width:50%;">E.& O.E.</td>
                    <td style="width:20%;border:1px solid black;border-top:none;" colspan="2">Sub Total</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;border-top:none;"><?php echo number_format($SubTotal,2); ?></td>
                </tr> 
                <tr>
                	<td style="width:20%;border:1px solid black;" colspan="2">Adjustment Credit/Rebate</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;"><?php echo number_format($AdjCredit,2); ?></td>
                </tr>
                <tr>
                	<td style="width:20%;border:1px solid black;" colspan="2">Interest on Arrears</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;"><?php echo number_format($InterestOnArrears,2); ?></td>
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
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;"><?php echo number_format(abs($BalanceAmout), 2); if($BalanceAmout < 0){echo ' Cr';} else {echo ' Dr';} ?></td>
                </tr>
                <tr>
                	<td style="width:20%;border:1px solid black;border-right:none;border-left:none;" colspan="6">
                    <?php
                    if($BalanceAmout <> "")
					{
						
					?>
                    	In Words : <?php  echo "Rupees ". convert_number_to_words(number_format($BalanceAmout,2)) . ' Only.';
					}
					?>
                        
                     </td>
                </tr>
	       </table>
           
        </div>
        <div id="bill_notes" style="text-align:left;font-size:12px;margin-left:5px;">
        	Notes:<br>
       			<?php echo $BillNotes; ?>     
        </div>
        <div id="bill_message">
        </div>
        <div id="bill_sign" style="text-align:right;border-top:1px solid black;padding-right:10px;font-size:12px;">
        	<?php echo $objFetchData->objSocietyDetails->sSocietyName; ?><br><br><br>Authorised Signatory
        </div>
        <?php if($show_receipt == 1 && sizeof($receiptDetails) > 0)
			{ ?>
        <div id="bill_receipt" style="text-align:center;border-top:1px solid black;border-bottom:none;">        	
            <div id="society_name" style="font-weight:bold; font-size:14px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>           
            <div id="society_reg" style="font-size:10px;">
				<?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?>
            </div>                               
        	<div id="bill_subheader" style="text-align:center;font-weight:bold; font-size:15px;">
            	RECEIPT
            </div>
            <div id="bill_details" style="text-align:right;">
            <table style="width:100%;">
            	<tr>                	
                    <td style="width:10%">Date</td>
                    <td id='bill_date' style="width:15%;" colspan="3"><?php echo $BillDate ?></td>                    
              	</tr>  
            	<tr>
                	<td style="width:20%;" colspan="4">Received with thanks from  <?php echo $objFetchData->objMemeberDetails->sMemberName; ?></td>                    
              	</tr>
                <tr>
                	<td style="width:20%">Receipt Period:</td>
                    <td id='receipt_period' style="width:30%;"><?php  echo $StartDate . " to ". $EndDate ?></td>
                	<td style="width:40%; text-align:right;">Unit No.</td>
                    <td id='owner_unit' style="width:10%; text-align:center;"><?php echo $objFetchData->objMemeberDetails->sUnitNumber; ?></td>
                </tr>                                             
            </table>
        </div>
        <div id="bill_payment" style="width:100%;">
        	<table style="width:100%;font-size:12px;">
                <tr>
                <th style="text-align:center; border:1px solid black;border-left:none;width:10%;">Receipt No.</th>
                <th style="text-align:center; border:1px solid black;border-left:none;width:15%;">Date</th>
                <th style="text-align:center; border:1px solid black;border-left:none;width:20%;">Cheque/NEFT No.</th>
                <th style="text-align:center; border:1px solid black;border-left:none;width:35%;">Bank & Branch</th>
                <th style="text-align:center; border:1px solid black;border-left:none;border-right:none;width:20%;">Amount</th>
                </tr>  
                <?php 
					//echo "Receipt Details";
					//echo sizeof($receiptDetails) ;
					$total = '';
					for($i=0; $i < sizeof($receiptDetails) ; $i++)
					{						
						$amount = (float)$receiptDetails[$i]['Amount'];
						$payerBank = $receiptDetails[$i]['PayerBank'];
						$chequeDate = $receiptDetails[$i]['ChequeDate'];
						$chequeNo = $receiptDetails[$i]['ChequeNumber'];
						$total += $amount;
				?>
                <tr>
                	<td style="text-align:center;border:1px solid black;border-left:none;"><?php echo $i+1 ?> </td>                    
                    <td style="text-align:center;border:1px solid black;border-left:none;"><?php echo getDisplayFormatDate($chequeDate) ?> </td>
                    <td style="text-align:center;border:1px solid black;border-left:none;"><?php echo $chequeNo ?> </td>
                    <td style="text-align:center;border:1px solid black;border-left:none;"><?php echo $payerBank ?> </td>
                    <td style="text-align:center;border:1px solid black;border-left:none;border-right:none;"><?php echo number_format($amount, 2); ?> </td>
                </tr>                              
                <?php } ?>
                <tr>
                	<td colspan="4" style="text-align:right;"><?php echo "Total    :  " ?></td>
                    <td style="text-align:center;border:1px solid black;border-left:none;border-right:none;"><?php echo number_format($total, 2); ?> </td>

                </tr>
                <tr>
                	<td style="border:1px solid black;border-right:none;border-left:none;" colspan="6"><?php 
					if($total <> '')
					{
					?>
					In Words : <?php  echo "Rupees ". convert_number_to_words($total); if($total <> ''){ echo " Only"; }?>
					<?php
					}
					?>
                    </td>
                </tr>
           </table>
           </div>
        </div>
        <?php } ?>
        
        <div id="bill_footer" style="text-align:left;border-top:1px solid black;padding-right:10px;border-top:none;">
        <table width="100%" style="font-size:12px;">
        
        <?php if($show_receipt == 1 && sizeof($receiptDetails) > 0)
			{ ?>
            <tr>
            <td style="text-align:left;width:50%;">( Subject to Realisation of Cheque ) </td>
            <td style="text-align:right;width:50%;"> <?php echo $objFetchData->objSocietyDetails->sSocietyName; ?> </td>
			</tr><?php } ?>
            <tr><td> <br><br> </td></tr>
            <tr>
           <td style="text-align:left;width:50%;">Accounts Maintained By "Pavitra Associates Pvt. Ltd." </td>
           <td style="text-align:right;width:50%;"> <?php if($show_receipt == 1 && sizeof($receiptDetails) > 0)
			{ ?> Authorised Signatory <?php } ?></td>
           </tr>
       </table>
        </div>
    </div>
    </div>
    </center>
    </Form>
    <input type="button" id="viewbtn" value="View As PDF"  onclick="ViewPDF();"/>
</body>
</html>
<?php
	if(isset($_REQUEST['gen']))
	{
		?>
        	<script>ViewPDF();</script>
        <?php
	}
?>
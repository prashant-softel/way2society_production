<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Create Challan</title>
</head>


<?php 
//include_once("datatools.php");
include_once("classes/include/dbop.class.php");
include_once("classes/utility.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/PaymentDetails.class.php");
include_once("classes/include/fetch_data.php");
include "common/CommonMethods.php";
include_once("classes/view_ledger_details.class.php");

$dbConn = new dbop();
$obj_utility=new utility($dbConn);
$obj_PaymentDetails = new PaymentDetails($dbConn);
$objFetchData = new FetchData($dbConn);

$GetAssesmentYear =$obj_PaymentDetails->GetAssesmentYear($_SESSION['year_description']); 
//print_r($GetAssesmentYear);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
$PaidToLedgerName =$obj_PaymentDetails->GetTDSLadgerName($_POST['tdsId']);  
//$data =json_encode($_POST['data_arr'],true);

$data_arr = $_POST['data_arr'];
$date1 = strtotime($_POST['fdate']);
$obj_ledger_details = new view_ledger_details($dbConn);
$GetBank = $obj_ledger_details->GetBankName($_POST['bankID']);

//Echo it
//echo $dmy;
//var_dump($data);

//$VoucherLedferID = $data_arr[0]['VoucherLedgerID'];
//$PaidToLedgerName =$obj_PaymentDetails->GetTDSLadgerName($VoucherLedferID);  
?>
<!DOCTYPE html>
<html><head>
		<meta charset="UTF-8">
			<title></title>
		<style>
.btn
{
color: #fff;
background-color: #337ab7;
border-color: #2e6da4;    
border-radius: 4px;font-size: 14px;
font-weight: 400;
line-height: 1.42857143;
text-align: center;
white-space: nowrap;
vertical-align: middle;
cursor: pointer;
}
 input {
    
    font: initial !important;
   
}
</style>	
<script type="text/javascript" src="js/display_challan.js"></script>
<script type="text/javascript" src="js/validate.js"></script>
<script src="jquery/jquery-1.11.3.min.js" language="JavaScript" type="text/javascript"></script>
<script src="jquery/jquery-ui.min.js" language="JavaScript" type="text/javascript"></script> 
<link href="jquery/jquery-ui.css" rel = "stylesheet">
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css">



<link href="csss/style.css" rel="stylesheet" type="text/css" />
<link href="cssss/layout.css" rel="stylesheet" type="text/css" />
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">


	
    
	
	
</head>

		<body>
			<center>
			<div style="width: 80%;text-align: center">

			<b>
				<u>Sheet Name</u> :- Challan ITNS 281
			</b>
			<hr>
            <input type="hidden" id="from_date" name="from_date" value="<?php echo $_POST['from_date']?>">
            <input type="hidden" id="to_date" name="to_date" value="<?php echo $_POST['to_date']?>">
            <input type="hidden" id="Paidto" name="Paidto" value="<?php echo $_POST['tdsId']?>">
            <input type="hidden" id="GroupID" name="GroupID" value="<?php echo $_POST['gid']?>">
         	 <input type="hidden" id="BankID" name="BankID" value="<?php echo $_POST['bankID']?>">
            <input type="hidden" id="url_date" name="url_date" value="<?php echo date("d-m-Y", $date1);?>">
            
            <input type="hidden" id="chequeamount" name="chequeamount" value="<?php echo $_POST['amt']?>">
            <input type="hidden" id="assessmentYear" name="assessmentYear" value="<?php echo $GetAssesmentYear[0]['AssesmentYear']?>">
            <input type="hidden" id="YearID" name="YearID" value="<?php echo $GetAssesmentYear[0]['YearID']?>">
            <input type="hidden" id="Nature_of_TDS" name="Nature_of_TDS" value="<?php echo $_POST['NatureOfTDS'] ?>">
            <input type="hidden" id="data_arr" name="data_arr" value = '<?php echo $data_arr?>'>
            <table class="" cellspacing=0 border=1 style="width: 100%;">
				<tbody>
						
						<tr style="height:18.8px;">
							
							<td style="font-family:Times New Roman;font-size:12px;color:#000000;border-left:1px solid;border-right:1px solid;border-top:1px solid;border-left-color:#000000;border-right-color:#000000;border-top-color:#000000;min-width:50px;text-align: left;" >
								* Important : Please see notes <br> overleaf before filling up the <br>challan
							</td>
							<td style="font-family:Times New Roman;text-align:center;font-size:15px;color:#000000;font-weight:bold;border-left:1px solid;border-right:1px solid;border-top:1px solid;border-left-color:#000000;border-right-color:#000000;border-top-color:#000000;min-width:50px" >
								T.D.S./TCS TAX CHALLAN
							</td>
							<td style="font-family:Times New Roman;font-size:12px;color:#000000;border-left:1px solid;border-right:1px solid;border-top:1px solid;border-left-color:#000000;border-right-color:#000000;border-top-color:#000000;min-width:50px;text-align: left" >
								Single Copy (to be sent to <br>the ZAO) 
							</td>
							
						</tr>
						<tr style="height:18.8px;">
							
							<td style="font-family:Times New Roman;text-align:center;font-size:15px;color:#000000;font-weight:bold;border-left:1px solid;border-top:1px solid;border-left-color:#000000;border-top-color:#000000;min-width:50px">
								CHALLAN NO./ <br>ITNS<br>281
							</td>
							<td style="font-family:Times New Roman;text-align:center;font-size:12px;color:#000000;font-weight:bold;border-left:1px solid;border-top:1px solid;border-left-color:#000000;border-top-color:#000000;min-width:50px" >
								<div style="width: 100%">
									Tax Applicable (Tick One)*
								</div>
								<div style="width: 100%">
									TAX DEDUCTED/COLLECTED AT SOURCE FROM
								</div>
                                <center>
								<div style="width: 90%;padding: 5px;">
								<div style="width: 30%;float: left;">
									(0020) COMPANY DEDUCTEES
								</div>
								<div style="width: 15%;float: left;text-align: left">
									<input type="Checkbox" name="comp_deductees_check" id="comp_deductees_check" onclick="camp_deduct()">
                                    <input type="hidden" name="comp_deductees" id="comp_deductees" value="0">
								</div>
								<div style="width: 10%;float: left;">
									&nbsp;
								</div>
								<div style="width: 30%;float: left;">
									(0021) NON-COMPANY DEDUCTEES
								</div>
								<div style="width: 15%;float: left;text-align: left">
									<input type="Checkbox" name="non_comp_deduct_check" id="non_comp_deduct_check" onclick="non_camp_deduct()">
                                    <input type="hidden" name="non_comp_deductees" id="non_comp_deductees" value="0">
								</div>
							</div>
                            </center>
							</td>
							
							<td style="min-width:50px;text-align: center;">
							Assessment Year<br>
                            
							<?php echo $GetAssesmentYear[0]['AssesmentYear']; ?>
							</td>
						</tr>
                        <tr><td colspan="3" style="border: none;"><br></td></tr>
						<tr style="height:18.8px;">
							
							<td style="border: none;border-left: 0px solid;font-size: 12px;padding-left: 10px;text-align: left;" >
								<b>Tax Deduction Account No. (T.A.N.)</b>
							</td>
                            <td  colspan="2" style="border: none;border-left: 0px solid;font-size: 12px;padding-left: 10px;text-align: left;" >
								<b> &nbsp;&nbsp;:&nbsp;&nbsp;<?php echo $objFetchData->objSocietyDetails->sSocietyTANNo; ?></b>
							</td>
							<!--<td  style="border: none">
								
							</td>-->
							
					
						</tr>
						<!--<tr style="height:18.8px;">
							
							
							<td  style="font-family:Times New Roman;font-size:12px;color:#000000;border-left:1px solid;border-top:1px solid;border-left-color:#000000;border-top-color:#000000;text-align: left;">
								<?php //echo $objFetchData->objSocietyDetails->sSocietyTANNo; ?>
							</td>
							<td  colspan="2" style="    border: 0px;" >
								
							</td>
					
						</tr>-->
						 <tr style="height:18.8px;">
							
							<td  style="font-family:Times New Roman;font-size:12px;border-left:1px solid;border-left-color:#000000;min-width:50px; text-align: left;border: none;padding-left: 10px;">
								<b>Full Name </b>
							</td>
                            <td  style="font-family:Times New Roman;font-size:12px;border-left:1px solid;border-left-color:#000000;min-width:50px; text-align: left;border: none;padding-left: 10px;" colspan="2">
								<b>&nbsp;&nbsp;:&nbsp;&nbsp;<?php echo $objFetchData->objSocietyDetails->sSocietyName; ?> </b>
							</td>
						</tr>
						<!--<tr style="height:18.8px;">
							
							<td  style="font-family:Times New Roman;font-size:12px;border-left:1px solid;border-left-color:#000000;text-align: left;" colspan=3>
								<?php //echo $objFetchData->objSocietyDetails->sSocietyName; ?>
							</td>
						</tr>-->
						
						<!--</tr>-->
						<tr style="height:18.8px;">
							
							<td  style="font-family:Times New Roman;font-size:12px;border-left:1px solid;border-left-color:#000000;min-width:50px; text-align: left;border: none;padding-left: 10px;">
								<b>Complete Address with City &amp; State</b>
							</td>
                            <td colspan="2"  style="font-family:Times New Roman;font-size:12px;border-left:1px solid;border-left-color:#000000;min-width:50px; text-align: left;border: none;padding-left: 10px;">
								<b>&nbsp;&nbsp;:&nbsp;&nbsp;<?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></b>
							</td>
						</tr>
						<!--<tr style="height:18.8px;">
							
							<td  colspan="3" style="font-family:Times New Roman;font-size:12px;color:#000000;border-left:1px solid;border-left-color:#000000;min-width:50px; text-align: left;">
								<?php //echo $objFetchData->objSocietyDetails->sSocietyAddress; ?>
							</td>
						</tr>-->
						<!--<tr style="height:18.8px;">
							
							<td  colspan="3" style="font-family:Times New Roman;font-size:9px;color:#000000;border-left:1px solid;border-left-color:#000000;min-width:50px; text-align: left;">
								
							</td>
						</tr>-->
						<tr style="height:18.8px;">
							
							<td style="font-family:Times New Roman;font-size:12px;border-left:1px solid;border-left-color:#000000;min-width:50px; text-align: left;border: none;padding-left: 10px;">
								<b>Tel. Number</b> 
							</td>
							<td  colspan="2" style="font-family:Times New Roman;font-size:12px;border-left:1px solid;border-left-color:#000000;min-width:50px; text-align: left;border: none;padding-left: 10px;">
								<b>&nbsp;&nbsp;:&nbsp;&nbsp;<?php echo $objFetchData->objSocietyDetails->sSocietyContactNo; ?></b>
							</td> 
							<!--<td style="font-family:Times New Roman;font-size:12px;color:#000000;min-width:50px">
								Pin :  <?php echo $objFetchData->objSocietyDetails->sSocietyPinCode; ?>
							</td> -->

						</tr>
						<tr style="height:18.8px;">
							
							<td colspan="3" style="font-family:Times New Roman;font-size:9px;color:#000000;border-left:1px solid;border-left-color:#000000;min-width:50px">
								<table style="width: 100%;border: 1px solid; border-collapse: collapse;" >
									<tr>
										<td style="border: 1px;font-size: 13px;"><b> Type of Payment</b> </td>
										<td style="font-size: 13px;text-align: right;padding-right: 40px;"><b> Code *  : <?php echo $_POST['NatureOfTDS'] //$PaidToLedgerName[0]['TDS_NatureOfPayment'] ?></b></td>
									</tr>
									<tr><td colspan="2" style="border-bottom: 1px solid black"></td></tr>
									<tr>
										<td style="font-size: 12px;width: 50%;"> (Tick One) </td>
										<td style="font-size: 12px;width: 50%;text-align: right;padding-right: 10px;">(Please see overleaf)</td>
									</tr>
									<tr>
										
										<td colspan="2" style="font-size: 12px;">
											<div style="width: 100%">
												<div style="width: 70%;float: left;text-align: right;">DS/TCS Payable by Taxpayer</div>
												<div style="width: 10%;float: left;text-align: right;">(200)</div>
												<div style="width: 20%;float: left;margin-top: 0px;"><input type="Checkbox" name="tds_taxpayer_check" id="tds_taxpayer_check" onclick="tds_tax_function()"><input type="hidden" name="tds_taxpayer" id="tds_taxpayer" value="0"></div>
											</div>
										</td>
									</tr>
									 <tr>
										<td colspan="2" style="font-size: 12px;">
											<div style="width: 100% ;" >
												<div style="width: 70%;float: left;text-align: right;">TDS/TCS Regular Assessment (Raised by I.T. Deptt.)</div>
												<div style="width: 10%;float: left;text-align: right;">(400)</div>
												<div style="width: 20%;float: left;margin-top: 0px;"><input type="Checkbox" name="tds_reg_assess_check" id="tds_reg_assess_check"  onclick="tds_reg_function()"><input type="hidden" name="tds_reg_assess" id="tds_reg_assess" value="0" ></div>
											</div>
											
										</td>
									</tr> 
                                    <tr><td colspan="2"><br></td></tr>
                                    <tr>
                                    <td colspan="2">
                                    	<table style="width:100%"> 
                                        <tr>
                                      <!--  <td style="font-family:Times New Roman;font-size:12px;color:#000000;font-weight:bold;text-align: left;padding-left: 10px;">Paid To &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;<?php //echo $PaidToLedgerName[0]['ledger_name'] ?></td>-->
                                        
                                       <!-- <td style="font-family:Times New Roman;font-size:12px;color:#000000;font-weight:bold;text-align: left;padding-left: 10px;visibility: hidden;">Cheque Number &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;<?php //echo $_POST['chkNo'] ?></td>-->
                                        
                                        <td style="font-family:Times New Roman;font-size:12px;color:#000000;font-weight:bold;text-align: left;padding-left: 10px;width: 25%;">Challan Date &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;<?php //echo date("Y-m-d");//$_POST['date'] ?> <input type="text" name="challan_date" id="challan_date" class="basics" size="8"  value="<?php echo date("d-m-Y") ?>" style="width:100px;" readonly/></td>
                                       <td style="text-align:left;font-family:Times New Roman;font-size:12px;color:#000000;font-weight:bold;"><?php echo $GetBank?></td>
                                        </tr>
                                        </table>
                                    </td>
                                    
                                    </tr>
									<tr>
										
										<td style="font-family:Times New Roman;font-size:13px;color:#000000;font-weight:bold;border-left:1px solid;border-top:1px solid;border-bottom:1px solid;border-left-color:#000000;border-top-color:#000000;border-bottom-color:#000000;min-width:50px;    text-align: left;    padding-left: 10px;" >
											DETAILS OF PAYMENTS 
									</td>
									<td style="font-family:Times New Roman;text-align:center;font-size:13px;border-top:1px solid;border-bottom:1px solid;border-top-color:#000000;border-bottom-color:#000000;min-width:50px;font-weight:bold;" >
										Amount (in Rs. Only)
									</td>
								</tr>
								<tr>
										<td style="font-family:Times New Roman;font-size:12px;color:#000000;font-weight:bold;border-left:1px solid;border-top:1px solid;border-bottom:1px solid;border-left-color:#000000;border-top-color:#000000;border-bottom-color:#000000;min-width:50px;    text-align: left;    padding-left: 10px;border-right: 1px solid;" >
											<nobr>Income Tax </nobr>
									</td>
									<td style="font-family:Times New Roman;text-align:center;font-size:12px;border-top:1px solid;border-bottom:1px solid;border-top-color:#000000;border-bottom-color:#000000;min-width:50px; text-align: right" >
										<input type="text" value="" id="income_tax" name="income_tax" style="text-align: right;" OnChange ="updateTotalAmt()">
									</td>
								</tr>
								<tr>
										<td style="font-family:Times New Roman;font-size:12px;color:#000000;font-weight:bold;border-left:1px solid;border-top:1px solid;border-bottom:1px solid;border-left-color:#000000;border-top-color:#000000;border-bottom-color:#000000;min-width:50px;    text-align: left;    padding-left: 10px;border-right: 1px solid;" >
											<nobr>Fee Under Sec 234E </nobr>
									</td>
									<td style="font-family:Times New Roman;text-align:center;font-size:12px;border-top:1px solid;border-bottom:1px solid;border-top-color:#000000;border-bottom-color:#000000;min-width:50px; text-align: right" >
										<input type="text" value="0" id="under_234e" name="under_234e" style="text-align: right;"  disabled>
									</td>
								</tr>
								<tr>
									<td style="font-family:Times New Roman;font-size:12px;color:#000000;font-weight:bold;border-left:1px solid;border-top:1px solid;border-bottom:1px solid;border-left-color:#000000;border-top-color:#000000;border-bottom-color:#000000;min-width:50px;    text-align: left;    padding-left: 10px;border-right: 1px solid;" >
											<nobr>Surcharge </nobr>
									</td>
									<td style="font-family:Times New Roman;text-align:center;font-size:12px;border-top:1px solid;border-bottom:1px solid;border-top-color:#000000;border-bottom-color:#000000;min-width:50px; text-align: right" >
									<input type="text" value="0" id="surcharge" name="surcharge" style="text-align: right;" disabled >	
									</td>
								</tr>
								<tr>
									<td style="font-family:Times New Roman;font-size:12px;color:#000000;font-weight:bold;border-left:1px solid;border-top:1px solid;border-bottom:1px solid;border-left-color:#000000;border-top-color:#000000;border-bottom-color:#000000;min-width:50px;    text-align: left;    padding-left: 10px;border-right: 1px solid;" >
											<nobr>Education Cess </nobr>
									</td>
									<td style="font-family:Times New Roman;text-align:center;font-size:12px;border-top:1px solid;border-bottom:1px solid;border-top-color:#000000;border-bottom-color:#000000;min-width:50px; text-align: right" >
										<input type="text" value="0" id="edu_cess" name="edu_cess" style="text-align: right;" disabled>
									</td>
								</tr>
								<tr>
									<td style="font-family:Times New Roman;font-size:12px;color:#000000;font-weight:bold;border-left:1px solid;border-top:1px solid;border-bottom:1px solid;border-left-color:#000000;border-top-color:#000000;border-bottom-color:#000000;min-width:50px;    text-align: left;    padding-left: 10px;border-right: 1px solid;" >
											<nobr>Interest </nobr>
									</td>
									<td style="font-family:Times New Roman;text-align:center;font-size:12px;border-top:1px solid;border-bottom:1px solid;border-top-color:#000000;border-bottom-color:#000000;min-width:50px; text-align: right" >
										<input type="text" value="0" id="intrest" name="intrest" style="text-align: right;" disabled>
									</td>
								</tr>
								<tr>
									<td style="font-family:Times New Roman;font-size:12px;color:#000000;font-weight:bold;border-left:1px solid;border-top:1px solid;border-bottom:1px solid;border-left-color:#000000;border-top-color:#000000;border-bottom-color:#000000;min-width:50px;    text-align: left;    padding-left: 10px;border-right: 1px solid;" >
											<nobr>Penalty </nobr>
									</td>
									<td style="font-family:Times New Roman;text-align:center;font-size:12px;border-top:1px solid;border-bottom:1px solid;border-top-color:#000000;border-bottom-color:#000000;min-width:50px; text-align: right" >
										<input type="text" value="0" id="penality" name="penality" style="text-align: right;" disabled >
									</td>
								</tr>
								</tr>
								<tr>
									<td style="font-family:Times New Roman;font-size:12px;color:#000000;font-weight:bold;border-left:1px solid;border-top:1px solid;border-bottom:1px solid;border-left-color:#000000;border-top-color:#000000;border-bottom-color:#000000;min-width:50px;    text-align: left;    padding-left: 10px;border-right: 1px solid;" >
											<nobr>Total </nobr>
									</td>
									<td style="font-family:Times New Roman;text-align:center;font-size:12px;border-top:1px solid;border-bottom:1px solid;border-top-color:#000000;border-bottom-color:#000000;min-width:50px; text-align: right" >
										<input type="text" value="<?php //echo number_format($_POST['amt'],2) ?>" id="total_amount" name="total_amount" style="text-align: right;">
									</td>
								</tr>
								<tr>
									<td style="font-family:Times New Roman;font-size:12px;color:#000000;font-weight:bold;border-left:1px solid;border-top:1px solid;border-bottom:1px solid;border-left-color:#000000;border-top-color:#000000;border-bottom-color:#000000;min-width:50px;    text-align: left;    padding-left: 10px;" colspan="2" >
									<!--<b>Total (in words) &nbsp;&nbsp;:&nbsp;&nbsp; <?php  echo "Rupees ". convert_number_to_words(number_format($_POST['amt'],2)) . ' Only.';?></b> -->
                                    <span id="TotalWordAmount"></span>
									</td>
									<!--<td style="font-family:Times New Roman;text-align:center;font-size:12px;border-top:1px solid;border-bottom:1px solid;border-top-color:#000000;border-bottom-color:#000000;min-width:50px; text-align: left" >
										<?php  //echo "Rupees ". convert_number_to_words(number_format($_POST['amt'],2)) . ' Only.';?>
									</td>-->
								</tr>
								</table>
                                
                               
			
			<tr><td colspan="3" style="border: none;"><br></td></tr>
			<tr><td colspan="3" style="border: none;">
            <input type="submit" value="Submit" id="sub" class="btn" onclick="SubmitEntry();">
            <input type="submit" value="Cancel" class="btn" id="Cancle"  onclick="javascript:window.close()">
            </td></tr>
            <tr><td colspan="3" style="border: none;"><br></td></tr>
				</table>
		</div>
	</center>
	<hr>
</body>
</html> 


<script type="text/javascript">
$(document).ready(function(){
  document.getElementById('income_tax').value='<?php echo number_format($_POST['total_amount'],2) ?>';
  document.getElementById('total_amount').value='<?php echo number_format($_POST['total_amount'],2) ?>';
  var Total_Amount_InWord =NumberRsInWOrd('<?php echo number_format($_POST['total_amount'],2) ?>');
  
  document.getElementById('TotalWordAmount').innerHTML='<b>Rupees : '+Total_Amount_InWord+' Only.</b>';
  
});
function camp_deduct() {
  var checkBox = document.getElementById("comp_deductees_check");

  if(checkBox.checked == true)
  {
	  document.getElementById("comp_deductees").value=1;
  }
  else 
  {
	  document.getElementById("comp_deductees").value=0;
  }
}
function non_camp_deduct()
{
	 var checkBox = document.getElementById("non_comp_deduct_check");

  if(checkBox.checked == true)
  {
	  document.getElementById("non_comp_deductees").value=1;
  }
  else 
  {
	  document.getElementById("non_comp_deductees").value=0;
  }
}
function tds_tax_function()
{
  var checkBox = document.getElementById("tds_taxpayer_check");
  if(checkBox.checked == true)
  {
	  document.getElementById("tds_taxpayer").value=1;
  }
  else 
  {
	  document.getElementById("tds_taxpayer").value=0;
  }
}
function tds_reg_function()
{
   var checkBox = document.getElementById("tds_reg_assess_check");
  if(checkBox.checked == true)
  {
	  document.getElementById("tds_reg_assess").value=1;
  }
  else 
  {
	  document.getElementById("tds_reg_assess").value=0;
  }
}

function updateTotalAmt()
{
	var Amount=document.getElementById('income_tax').value;
	document.getElementById('total_amount').value=Amount;
	var Total_Amount_InWord =NumberRsInWOrd(Amount);
	document.getElementById('TotalWordAmount').innerHTML='<b>Rupees : '+Total_Amount_InWord+' Only.</b>';
}

</script>
 
      
<script type="text/javascript">
	minStartDate = '<?php  echo getDisplayFormatDate($_SESSION['default_year_start_date']);?>';
	maxEndDate = '<?php  echo getDisplayFormatDate($_SESSION['default_year_end_date']);?>';
        $(function()
        {
           // $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true ,
			minDate: minStartDate,
          	maxDate: maxEndDate
        })});
		 /*$(function() {
            $( ".basics" ).datepicker();
         });*/
    </script>
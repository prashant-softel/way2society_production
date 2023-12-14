
<?php
	require_once('swift/swift_required.php');
	
// The message
$message = "Line 1\r\nLine 2\r\nLine 3";

// In case any of our lines are larger than 70 characters, we should use wordwrap()
//$message = wordwrap($message, 70, "\r\n");
$emailContent = 
//$transport = Swift_SmtpTransport::newInstance('103.50.162.146', 465, "ssl")
$transport = Swift_SmtpTransport::newInstance('103.50.162.146',587)
						->setUsername('no-reply14@way2society.com')
						->setSourceIp('0.0.0.0')
						->setPassword('society123') ; 
	
	$mailer = Swift_Mailer::newInstance($transport);
	$message = Swift_Message::newInstance();
	$emailContent = "test";
	//$message->setTo(array( 'dalvishreya106@gmail.com' => 'name'));
	$message->setTo(array("dalvishreya106@gmail.com" => "shreya"));
	$message->setSubject('Way2society Account Activation');
	//$message->setBody($emailContent);
	$message->setFrom(array('no-reply14@way2society.com' => 'no-reply'));
	$message->setContentType("text/html");	
	// Send the email
	// You can embed files from a URL if allow_url_fopen is on in php.ini
	$baseDir = dirname(__FILE__);
			
			$fileName =  $baseDir . "/images/bank_cash.png";
			echo $fileName;
//$message->attach(Swift_Attachment::fromPath($fileName)  
					//->setDisposition('inline'));
					$msg = '<body>
	<div id="msg" style="color:#FF0000;"></div>
    <center>
    <Form Name ="form1" Method ="POST" ACTION =Maintenance_bill_edit.php?UnitID=247&PeriodID=87 				>
	
    <div id="bill_main" style="width:90%;">
    <div style="border:1px solid black;">
        <div id="bill_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:16px;">RAHEJA HEIGHTS G CHS LTD TEST DEVELOPER 123</div>
            <!--<div id="society_type" style="font-weight:bold; font-size:20px;">PREMISES CO-OPERATIVE SOCIETY LTD.</div>-->
            <div id="society_reg" style="font-size:14px;">Registration No. MUM/WP/HSG/TC/15327/2012-13</div>
            <div id="society_address"; style="font-size:12px;">CTS NO 827 A / 1 C/1 A, OFF FILMCITY ROAD,MALAD EAST, MUMBAI 400 097</div>
        </div>
        <div id="bill_subheader" style="text-align:center;">
            <div style="font-weight:bold; font-size:14px;"> Maintenance  Bill [October 2016 -  December 2016]</div>
        </div>
        <div id="bill_details" style="text-align:center;border-top:1px solid black;font-size:12px;">
            <table style="width:100%;">
            	<tr>
                	<td style="width:15%;">Name :</td>
                    <td id="owner_name" style="font-weight:bold;">demo account</td>                    
              	</tr>
            </table>
            <table style="width:100%;">
                <tr>
                	<td style="width:15%;">Unit No :</td>
                    <td  style="font-weight:bold;">101</td>
                    <td style="width:10%">Bill No :</td>
                    <td  style="width:15%;">478</td>
              	</tr>
                <!--<tr>
                	<td colspan="4"  style="text-align:center;"></td>
                </tr>-->
                <tr>
                	<td style="width:15%;">Parking No :</td>
                    <td style=" "></td>
                    <td style="width:10%">Bill Date :</td>
                    <td  style="width:15%;">01-10-2016</td>
              	</tr>  
                <tr>
                	<td style="width:15%;visibility:hidden;">Wing :</td>
                   	<td style=" visibility:hidden;">G</td>
                   	<td style="width:10%;">Due Date :</td>
                   	<td  style="width:15%;">24-11-2016</td>
              	</tr>
               
										<tr>
                 			<td style="width:15%;">Area :</td>
                			<td style=" ">200 Sq.Ft</td>                    
                		</tr>
					            </table>
                    </div>
        <div id="bill_charges">
        	<table  style="width:100%;font-size:14px;" id="mainTable">
                <tr>
                <th style="text-align:center; width:10%; border:1px solid black;border-left:none;">Sr. No.</th>
                <th style="text-align:center; border:1px solid black;"" colspan="3">Particulars of Charges</th>
                <th style="text-align:center; width:20%; border:1px solid black;border-right:none;">Amount (Rs.)</th>
                </tr>
				<tr><td style="border:1px solid black;border-left:none;text-align:center;font-size:14px;">1</td><td colspan=3 style="border:1px solid black;text-align-left;font-size:12px;">PROPERTY TAX</td><td align=right style="border:1px solid black;border-right:none;text-align:right;width:15%;font-size:14px;">2,367.00</td></tr><tr><td style="border:1px solid black;border-left:none;text-align:center;font-size:14px;">2</td><td colspan=3 style="border:1px solid black;text-align-left;font-size:12px;">SINKING FUND</td><td align=right style="border:1px solid black;border-right:none;text-align:right;width:15%;font-size:14px;">1,350.00</td></tr></tr>           </table>
                      
                      </table>
                      
           <table style="width:100%;font-size:14px;">
          		<tr>
                	<td colspan="3" rowspan="7" style="width:50%">E.& O.E.</td>
                    <td style="width:20%;border:1px solid black;border-top:none;" colspan="2">Sub Total</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;border-top:none;">24,155.00</td>
                </tr> 
								<tr>
                	<td style="width:20%;border:1px solid black;" colspan="2">Adjustment Credit/Rebate</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;">0.00</td>
                </tr>
				                               <tr>
                	<td style="width:20%;border:1px solid black;" colspan="2">Interest on Arrears</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;">2,515.00</td>
                </tr>
                                <tr>
                	<td style="width:20%;border:1px solid black;"colspan="2">Previous Arrears</td>
                    <td id="sub_total" style="text-align:right;width:20%; border:none;"></td>
                </tr>
                                 <tr>
                	<td style="width:10%;border:1px solid black;">Principle</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;">47,910.00</td>
                    <td style="border:none;"></td>
                </tr>
                                <tr>
                	<td style="width:10%;border:1px solid black;">Interest</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;">1,254.00</td>
                    <td style="text-align:right;width:20%;border:1px solid black;border-right:none;border-top:none;">49,164.00</td>
                </tr>
                                <tr>
                	<td style="width:20%;border:1px solid black;" colspan="2">Balance Amount</td>
                    <td id="sub_total" style="text-align:right;width:20%;border:1px solid black;border-right:none;">75,834.00 Dr</td>
                </tr>
                <tr>
                	<td style="width:20%;border:1px solid black;border-right:none;border-left:none;" colspan="6">
                                        	In Words : Rupees Seventy-Five Thousand  Eight Hundred Thirty-Four Only.                        
                     </td>
                </tr>
	       </table>
         <input type="hidden" id="IsSupplementaryBill" name="IsSupplementaryBill" value="0"/>
        </div>
        <div id="bill_notes" style="text-align:left;font-size:12px;margin-left:5px;">
        	Notes:<br>
       			<div>1. Interest @ 18 % will be charged on arrears for whole 3 months if not paid on or before due date.</div>

<div>2. Cheque should be made in the name of &nbsp;&quot;Raheja Heights G CHS Ltd&quot;</div>

<div>3. NEFT/RTGS Details M/s Saraswat Co-Op Bank Ltd, Acc. Nu. Savings : &nbsp;249200100002390.NNP Branch, IFSC Code &nbsp;: SRCB0000249.</div>

<div>4. Please send a mail after making online payment.</div>

<div>5.&nbsp;Accounts maintained by &quot;Pavitra Associates Pvt Ltd&quot;. for queries pls mail rahejagwing@gmail.com/ lakshmi@pgsl.in</div>

<div>6.&quot; Property tax is as per actuals based on previous years BMC bill, any changes in this year charges will be charged accordingly.&quot;</div>
     
        </div>
        <div id="bill_message">
        </div>
        <div id="bill_sign" style="text-align:right;border-top:1px solid black;padding-right:10px;font-size:12px;">
        	RAHEJA HEIGHTS G CHS LTD TEST DEVELOPER 123<br><br><br>  Authorised Signatory         </div>
                
        <div id="bill_footer" style="text-align:left;border-top:1px solid black;padding-right:10px;border-top:none;">
        <table width="100%" style="font-size:12px;">
        
                    <tr><td> <br><br> </td></tr>
            <tr>
           <td style="text-align:left;width:50%;">Accounts Maintained By "Developer." </td>
           <td style="text-align:right;width:50%;"> </td>
           </tr>
       </table>
        </div>
    </div>
    </div>
    </center>
    <input type="hidden" name="UnitID" id="UnitID" value="247"/>
    <input type="hidden" name="PeriodID" id="PeriodID" value="87"/>
    </Form>
         	 <input type="button" id="viewbtn" value="View As PDF"  onclick="ViewPDF();"/> 
            	            <input type="button" id="send_email" value="Send Email" onclick="sendEmail(247 , 87,true,"demoaccount@way2society.com");"  title="Email will be send to demoaccount@way2society.com" />
    <input type="button" id="viewlog" value=""  onclick="ViewLog();" style="background-color:#FFF;border:#FFFFFF"/>
                <div id="status" style="color:#0033FF; font-weight:bold; visibility:hidden;"></div>
    
        <input type="hidden" style="float:right" id="logurl" value="m_bills_log/RHG_TEST/M_Bill_59_101_October-December-2016-17_478.html" >
    </body>
</html>';
	$message->setBody($msg);				
	$result = $mailer->send($message);	
	if($result >= 1)
	{		
		echo 'result : ' .$result;
	}
	else
	{
		echo 'Failed';
	}
									
				 
?>


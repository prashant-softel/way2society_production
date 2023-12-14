<?php 
include_once "classes/include/dbop.class.php";
$m_dbConn = new dbop();
$m_dbConnRoot = new dbop(true);
include_once("classes/dbconst.class.php");
include_once("classes/include/fetch_data.php");
include_once("classes/view_ledger_details.class.php");
include "common/CommonMethods.php";

/*if(isset($_REQUEST['e']) && !isset($_REQUEST['id']))
{
	
	//request file called from email link by member
	$protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
	$host     = $_SERVER['HTTP_HOST'];
	$script   = $_SERVER['SCRIPT_NAME'];
	$params   = $_SERVER['QUERY_STRING'];
	$referer  =  $_SERVER['HTTP_REFERER'];
	
	$currentUrl = $protocol . '://' . $host . $script . '?' . $params;
	
	if(!isset($_SESSION['login_id']))
	{
		header('Location: login.php?url='.$currentUrl);
	 }
	else
	{
		$currentUrl = str_replace('_**_','&',$currentUrl);
		$eVal =  str_replace('_**_','&',$_REQUEST['e']);
		$currentUrl = $protocol . '://' . $host . $script . '?' . $eVal;

		?>
			<script>window.location.href = "<?php echo $currentUrl?>";</script>
		<?php
	}
		
}*/
$obj_ledger_details = new view_ledger_details($m_dbConn);
$result = $obj_ledger_details->getChallanPrint($_REQUEST['id']);
$objFetchData = new FetchData($m_dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);	
//echo $result[0]['BankName'];
$AssessmentYear = $result[0]['AssessmentYear']; 
$Asses =explode('-',$AssessmentYear);
$start = $Asses[0];
$end   = $Asses[1];

$startYearNoArr = str_split($start);
$endYearNoArr   = str_split($end);
$SocietyTan     = $objFetchData->objSocietyDetails->sSocietyTANNo;
$SocietyTanArr = str_split($SocietyTan); 

$SocietyName    = $objFetchData->objSocietyDetails->sSocietyNameOfTDS;
$SocietyName =str_replace(' - ', '-', $SocietyName);
$SocietyNameArr = str_split($SocietyName); 
$SocietyAdd		= $objFetchData->objSocietyDetails->sSocietyAddress;
$SocietyAdd	 =str_replace(', ', ',', $SocietyAdd);
$SocietyAdd	 =str_replace('  ', ' ', $SocietyAdd);
$SocietyAdd	 =str_replace(' - ', '-', $SocietyAdd);

$SocietyAddArr = str_split($SocietyAdd); 

$SocietyContact = $objFetchData->objSocietyDetails->sSocietyContactNo;
$SocietyContactArr = str_split($SocietyContact); 

$SocietyPin = $objFetchData->objSocietyDetails->sSocietyPinCode;
$SocietyPinArr = str_split($SocietyPin); 
$code = str_split($result[0]['NatureOfTDS']);
//echo $SocietyPin; 
//print_r($result);

$company_deductee 		=	$result[0]['Company_Deductees'];
$non_company_deductee 	=	$result[0]['NonCompany_Deducteed'];
$payable_taxpayer 		=	$result[0]['Payable_Taxpayer'];
$regular_asses 			=	$result[0]['Reguler_Assessment']; 
$challan_no 			=	$result[0]['ChallanNo']; 
$intAmt = (int)$result[0]['TotalAmount'];
$totalamt =  strrev($intAmt);
$Unit ='';
$Tens='';
$Hundred='';
$Thousand='';
$Lac='';
$Carore='';

 
//$UnitAmt =convert_number_to_words(number_format($result[0]['TotalAmount']));
//echo $UnitAmt;
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Maintanence Bill</title>
 <script type="text/javascript" src="js/validate.js"></script>
<!--<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="js/ajax_new.js"></script>
<script type="text/javascript" src="js/jsCommon_20190326.js"></script>
<script>
/*function ViewPDF(ChallanNo)
{	
		var sData = document.getElementById('mainDiv').innerHTML;

		var bDownload = "0";
		var sHeader = '<html><head>';
		sHeader += '<style> ';
		sHeader += 'table {	border-collapse: collapse; } ';
		sHeader += 'table, th, td { border: 0px solid black; text-align: left; padding-top:0px; padding-bottom:0px; } ';
		sHeader += '</style>';	
		sHeader +=	'</head><body>';
		
		var sFooter =  '</body></html>';
		
		sData = sHeader + sData + sFooter;
	
		
		
		var sFileName = "TDSChallan-<?php echo $objFetchData->objSocietyDetails->sSocietyCode; ?>-" +ChallanNo ;
		console.log(sFileName);
		
		$.ajax({
			url : "viewpdf.php",
			type : "POST",
			//contentType: "application/json; charset=utf-8",
			data: { "data":sData, 
				     "filename":sFileName, 
					"society": "<?php echo $objFetchData->objSocietyDetails->sSocietyCode; ?>",
					"challanNumber": "<?php echo $_REQUEST['id']; ?>"},
					//"BT" : "<?php //echo TABLE_SALESINVOICE; ?>","bDownload":bDownload} ,
			success : function(data)
			{	
				console.log(data);
						//	window.open('TDSChallan/<?php //echo $objFetchData->objSocietyDetails->sSocietyCode; ?>/' + sFileName + '.pdf');
						
				
			},
				
			fail: function()
			{
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});
	}
*/</script>
<style>
		body,div,table,thead,tbody,tfoot,tr,th,td,p { font-family:"Times New Roman"; font-size:12px }
		a.comment-indicator:hover + comment { background:#ffd; position:absolute; display:block; border:1px solid black; padding:0.5em;  } 
		a.comment-indicator { background:red; display:inline-block; border:1px solid black; width:0.5em; height:0.5em;  } 
		comment { display:none;  } 
		.tdStyle
		{
			border-top: 1px solid #000000; 
			border-left: 1px solid #000000; 
			border-right: 1px solid #000000;
		}
		.tdStyle1
		{
			border-left: 1px solid #000000;
			 border-right: 1px solid #000000;
		}
		.tdStyle2
		{
			border-top: 1px solid #000000; 
			border-bottom: 1px solid #000000; 
			border-left: 1px solid #000000; 
			border-right: 1px solid #000000;
		}	
	</style>
	
</head>

<body>


<center>
<INPUT TYPE="button" id="Print" onClick="PrintPage()" name="Print!" value="Print!" width="300" style="width:60px;height:30px;margin-bottom:10px; font-size:20px" />

<div id="mainDiv"  style="width:70%" >
<div style="border:1px solid black;">
<table style="width:100%" align="center" cellspacing="0" border="0">
    <tr>
    <td class="tdStyle" colspan=10 align="left" valign=middle><font color="#000000">* Important : Please see notes <br>overleaf before filling up the <br>challan<br></font></td>
    <td class="tdStyle" colspan=17 align="center" valign=top><b><font size=3 color="#000000">T.D.S./TCS TAX CHALLAN <br></font></b></td>
	<td class="tdStyle" colspan=8 align="left" valign=top><font color="#000000">Single Copy (to be sent to <br> the ZAO) <br></font></td>
</tr>
<tr>
<td class="tdStyle" colspan=10 align="center" valign=middle><b><font color="#000000">CHALLAN NO./ <br>ITNS <br>281</font></b></td>
<td class="tdStyle" colspan=17 align="center" valign=top0><b><font color="#000000">Tax Applicable (Tick One)* <br>TAX DEDUCTED/COLLECTED AT SOURCE FROM
<table style="width:100%">
<tr><td align="center"><b><font color="#000000">(0020) COMPANY <br>DEDUCTEES</font></b></td>
	<td>
	<?php 
	if($company_deductee == 1)
	{?>
		<img src="images/checked.png">
    <?php 
	}
	else
	{?>
    	<img src="images/unchecked.png">
	<?php 	
	}?>    
    </td> 
	<td align="center"><b><font color="#000000">(0021) NON-COMPANY <br>DEDUCTEES</font></b></td>
	<td>
    <?php 
	if($non_company_deductee == 1)
	{?>
		<img src="images/checked.png">
    <?php 
	}
	else
	{?>
    	<img src="images/unchecked.png">
	<?php 	
	}?>  
    </td>
    </tr></table>
</font></b></td>
<td class="tdStyle" colspan=8 align="center" valign=middle>Assessment Year
	<table style="width: 90%;" align="right" cellspacing="0">
	<tr align="right">
		<td  class="tdStyle2"  align="center" valign=middle><?php echo $startYearNoArr[0];?></td>
		<td  class="tdStyle2" align="center" valign=middle><?php echo $startYearNoArr[1];?></td>
		<td class="tdStyle2" align="center" valign=middle><?php echo $startYearNoArr[2];?></td>
		<td class="tdStyle2" align="center" valign=middle><?php echo $startYearNoArr[3];?></td>
		<td align="left" valign=middle> -</td>
		<td class="tdStyle2" align="center" valign=middle><?php echo $endYearNoArr[0]?></td>
		<td class="tdStyle2" align="center" valign=middle><?php echo $endYearNoArr[1]?></td>
	</tr>
	</table>
</td>
</tr>

<tr>
	
<td style="border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" colspan=35 align="left"><font color="#000000">Tax Deduction Account No. (T.A.N.) </font></td>
</tr>
	<tr>
    
 <?php for($t=0 ; $t<10; $t++)
		{?>
    		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000;width: 25px;" align="center" valign=middle><?php echo $SocietyTanArr[$t]?></td>
	<?php }?>
  	 	<td style="border-right: 1px solid #000000" colspan=25 align="left" valign=middle><br></td>	
		
</tr>
<tr>
	<td style="border-left: 1px solid #000000; border-right: 1px solid #000000" colspan=35 align="left" valign=middle>Full Name </td>
</tr>
<tr>
<?php for($s=0; $s < 35; $s++ )
{?>
     <td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000;width: 25px;" align="center" valign=middle>
	 <?php 
	 if($SocietyNameArr[$s] == ''){ echo '&nbsp;&nbsp;'; }else{echo $SocietyNameArr[$s];}?></td>
	 <?php 
}?>
		
</tr>
<tr>
   	<td style="border-left: 1px solid #000000; border-right: 1px solid #000000" colspan=35 align="left" valign=middle>Complete Address with City &amp; State </td>
		
</tr>
<tr>
<?php for($sa=0; $sa <35 ;$sa++)
{?>
       <td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000;width: 25px;" align="center" valign=middle><?php echo $SocietyAddArr[$sa];?></td>
		<?php 
}?>
<tr>
 <?php for($sa1=36; $sa1 <71 ;$sa1++)
{?>
       <td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000;width: 25px;" align="center" valign=middle><?php echo $SocietyAddArr[$sa1];?></td>
<?php 
}?>
</tr>
<tr>
<td colspan="7" style="border-left: 1px solid #000000" align="left" valign=middle><font color="#000000">Tel. No. </font></td>
<?php for($c=0; $c<14; $c++)
{?>
   <td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000;width: 25px;" align="center" valign=middle><?php echo $SocietyContactArr[$c]?></td>
<?php 
}?>
		
<td style="border-right: 1px solid #000000" colspan=8 align="right" valign=middle>Pin&nbsp;   </td>
        <?php for($p=0; $p<6;$p++ )
		{?> 
		
        <td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000;width: 25px;" align="center" valign=middle><?php echo $SocietyPinArr[$p] ?></td>
		<?php
        }?>
		
		
	</tr>
    <tr>
		
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000" align="center" colspan="17"><b><font color="#000000">Type of Payment </font></b></td>
		
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000;border-right: 1px solid #000000" colspan=3 align="center"><b><font color="#000000">Code * </font></b></td>
        <?php for($t=0 ; $t<4; $t++)
		{?>
		<td style="border-top: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000;width: 25px;"  align="center" valign=middle><?php echo $code[$t]?></td>
		<?php }?>
	
		
		<td align="left" colspan="11" valign=middle style="border-top: 1px solid #000000; border-right: 1px solid #000000" ><br></td>
		
	</tr>
    <tr>
    <td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" colspan="24">
    <table style="width:100%">
    <tr>
    <td style="width:15%;">&nbsp;&nbsp;&nbsp;&nbsp;<br></td>
    <td  style="width:30%;"align="center"><font color="#000000" style="padding-left: 35px;">(Tick One)</font></td>
     <td style="width:30%;">&nbsp;&nbsp;&nbsp;&nbsp;<br></td>
    <td style="width:25%;" align="right">(Please see overleaf)</td>
    </tr>
   <tr style="height: 25px;">
    <td>&nbsp;&nbsp;&nbsp;&nbsp;<br></td>
    <td align="right" colspan="2"><font color="#000000">TDS/TCS Payable by Taxpayer</font> </td>
    <td>
   	<span style="padding-left: 25px; padding-right: 25px;"><font color="#000000">(200)</font></span>
   		<span>
        <?php 
		if($payable_taxpayer == 1)
		{?>
			<img src="images/checked.png">
    	<?php 
		}
		else
		{?>
    		<img src="images/unchecked.png">
		<?php 	
		}?>  
        </span>
        </td>
    </tr>
    <tr>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;<br></td>
    <td align="right" colspan="2"><font color="#000000">TTDS/TCS Regular Assessment (Raised by I.T. Deptt.)</font> </td>
   	<td><span style="padding-left: 25px; padding-right: 25px;"><font color="#000000">(200)</font></span>
   	<span>
    <?php 
		if($regular_asses == 1)
		{?>
			<img src="images/checked.png">
    	<?php 
		}
		else
		{?>
    		<img src="images/unchecked.png">
		<?php 	
		}?>  
    </span>
    </td>
    </tr>
    </table>
    </td>
    <td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000;padding-top: 16px;" >
    <table cellspacing="0">
    <tr>
    	<td colspan="8" style="padding-bottom:5px;"><b><font color="#000000">FOR USE IN RECEIVING BANK</font></b></td></tr>
    <tr>
    	<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><br></td>
    	<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><br></td>
   		<td align="center" valign=middle> -</td>
    	<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><br></td>
    	<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><br></td>
    	<td align="center" valign=middle> -</td>
    	<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><br></td>
    	<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><br></td>
      </tr>
    </table>
    </td>
   </tr>
   <tr>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=11 align="left"><b><font color="#000000">DETAILS OF PAYMENTS </font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-right: 1px solid #000000" colspan=13 align="center" valign=middle>Amount (in Rs. Only) </td>
       
        <td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000" >
        	<!--<table align="center" cellspacing="0" style="width:75%">-->
            <table align="center" cellspacing="0" style="width:180px">
            <tr>
            	<td align="left" >&nbsp;&nbsp;D&nbsp;&nbsp;&nbsp; D</td>
            	<td align="center">&nbsp;&nbsp;M&nbsp;&nbsp;&nbsp; M</td>
            	<td align="right">&nbsp;&nbsp;Y&nbsp;&nbsp;&nbsp; Y&nbsp;&nbsp;</td>
            </tr>
            </table>
        </td>
         </tr>
         <tr>
         	<td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=11 align="left"><font color="#000000">Income Tax</font></td>
         	<?php for($i=12; $i>=0; $i--)
			{
				if($i==0){$borderRight ='1px solid #000000';}else{$borderRight ='0px solid #000000';} ?>
            <td  align="center" style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: <?php echo $borderRight?>;width: 25px;"><?php echo $totalamt[$i]; ?></td>
         	<?php 
			}?>
            <td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000" ><br></td>
        </tr>
        <tr>
         	<td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=11 align="left"><font color="#000000">Fee Under Sec 234E</font></td>
            <?php for($i=0; $i<13; $i++)
			{
				if($i==12){$borderRight ='1px solid #000000';}else{$borderRight ='0px solid #000000';}
				?>
         	<td colspan="" style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: <?php echo $borderRight?>;width: 25px;"></td>
			<?php 
			
			}?>
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000" ><br></td>
        </tr>
        <tr>
         	<td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=11 align="left"><font color="#000000">Surcharge</font></td>
         	
            <?php for($i=0; $i<13; $i++)
			{
				if($i==12){$borderRight ='1px solid #000000';}else{$borderRight ='0px solid #000000';}
				?>
         	<td colspan="" style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: <?php echo $borderRight?>;width: 25px;"></td>
			<?php 
			
			}?>
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000" ><b><font color="#000000">SPACE FOR BANK SEAL </font></b></td>
        </tr>
        <tr>
         	<td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=11 align="left"><font color="#000000">Education Cess</font></td>
         	 <?php for($i=0; $i<13; $i++)
			{
				if($i==12){$borderRight ='1px solid #000000';}else{$borderRight ='0px solid #000000';}
				?>
         	<td colspan="" style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: <?php echo $borderRight?>;width: 25px;"></td>
			<?php 
			
			}?>
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000" ><br></td>
        </tr>
         <tr>
         	<td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=11 align="left"><font color="#000000">Interest</font></td>
         	 <?php for($i=0; $i<13; $i++)
			{
				if($i==12){$borderRight ='1px solid #000000';}else{$borderRight ='0px solid #000000';}
				?>
         			<td colspan="" style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: <?php echo $borderRight ?>;width: 25px;"></td>
			<?php 
			
			}?>
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000" ><br></td>
        </tr>
         <tr>
         	<td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=11 align="left"><font color="#000000">Penalty</font></td>
         	 <?php for($i=0; $i<13; $i++)
			{ 
				if($i==12){$borderRight ='1px solid #000000';}else{$borderRight ='0px solid #000000';}
				?>
         	<td colspan="" style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right:<?php echo $borderRight?>;width: 25px;"></td>
			<?php 
			
			}?>
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000" ><br></td>
        </tr>
         <tr>
         	<td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=11 align="left"><font color="#000000">Total</font></td>
         	<?php 
			//$unitCnt = 13 ;
			$AmtLength = strlen($totalamt);
			for($i=12; $i>=0; $i--)
			{
				if($i==0){$borderRight ='1px solid #000000';}else{$borderRight ='0px solid #000000';}
				if($i == 0 && $totalamt[$i] <> '')
				{
					//echo "Inside If <br> ".$totalamt[$i];
					$Unit = strtoupper(convert_number_to_words($totalamt[$i]));
				}
				else if($i == 1 && $totalamt[$i] <> '' )
				{
					//echo "Inside elseIf <br> ";
					$Tens =  strtoupper(convert_number_to_words($totalamt[$i]));
				}
				else if($i == 2 && $totalamt[$i] <> '')
				{
					//echo "Inside elseIf 1 <br>";
					$Hundred =  strtoupper(convert_number_to_words($totalamt[$i]));
				}
				else if($i == 3  || $i == 4)
				{
					
					if($AmtLength == 4 && $i == 3)
					{
						
						$Thousand =strtoupper(convert_number_to_words($totalamt[$i]));  
					}
					else if($AmtLength >= 5 && $i == 3)
					{
						
						$Thousand =strtoupper(convert_number_to_words((int)$totalamt[4].$totalamt[3]));  
					}
				}
				else if($i == 5 || $i == 6) 
				{
					//var_dump($AmtLength, $i, $totalamt[5].$totalamt[6]);
					if($AmtLength == 6 && $i == 5)
					{
						$Lac = strtoupper(convert_number_to_words($totalamt[$i])); 
					}
					else if($AmtLength >= 7 && $i == 6)
					{
						$Lac =strtoupper(convert_number_to_words((int)$totalamt[6].$totalamt[5])); 
					}
					// echo "lacs".$Lac;
				}
				else if($i == 7 ||$i == 8) 
				{
					if($AmtLength == 8 && $i == 7)
					{
						$Carore =strtoupper(convert_number_to_words($totalamt[$i]));  
					}
					else if($AmtLength >= 9 && $i == 8)
					{
						$Lac =strtoupper(convert_number_to_words((int)$totalamt[8].$totalamt[7])); 
					}
					
				}
				//echo "<br>AMount ".$totalamt[$i];
				?>
            <td  align="center" style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: <?php echo $borderRight ?>;width: 25px;"><?php echo $totalamt[$i]; ?></td>
         	<?php
			//echo "<br> CNt". $unitCnt;
			//$unitCnt--;
			
			}
			//echo "Unit :".$Unit;
			//echo "Ten :".$Tens;
			//echo "Hundred :".$Hundred;
			?>
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000" ><br></td>
        </tr>
        <tr>
         	<td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=11 align="left"><font color="#000000">Total (in words)</font></td>
         	<td colspan="13" style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000">&nbsp;<?php  echo "Rupees ". convert_number_to_words(number_format($result[0]['TotalAmount'],0)) . ' Only.'?></td>
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000" ><br></td>
        </tr>
        
        <tr>
         	<td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=4 align="center"><font color="#000000">CRORES</font></td>
            <td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=4 align="center"><font color="#000000">LACS</font></td>
            <td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=4 align="center"><font color="#000000">THOUSANDS</font></td>
            <td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=4 align="center"><font color="#000000">HUNDREDS</font></td>
            <td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=4 align="center"><font color="#000000">TENS</font></td>
            <td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000;border-right: 1px solid #000000" colspan=4 align="center"><font color="#000000">UNITS</font></td>
         	
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000" ><br></td>
        </tr> 
        <tr>
         	<td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=4 align="center"><font color="#000000"><br></font></td>
            <td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=4 align="center"><font color="#000000"><?php echo $Lac?></font></td>
            <td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=4 align="center"><font color="#000000"><?php echo $Thousand?></font></td>
            <td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=4 align="center"><font color="#000000"><?php echo $Hundred;?></font></td>
            <td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=4 align="center"><font color="#000000"><?php echo $Tens;?></font></td>
            <td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000;border-right: 1px solid #000000" colspan=4 align="center"><font color="#000000"><?php echo $Unit;?></font></td>
         	
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000" ><br></td>
        </tr>
        <tr>
        	<td colspan="11" style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" align="left"><font color="#000000">&nbsp;&nbsp;&nbsp;Paid in Cash/Debit to  A/c /Cheque No.</font></td>
            
          
            <td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=5 align="center"><font color="#000000"><br></font></td>
            <td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" colspan=3 align="center"><font color="#000000">Dated</font></td>
            <td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000;border-right: 1px solid #000000" colspan=5 align="center"><font color="#000000"><?php echo getDisplayFormatDate($result[0]['Challan_date']); ?></font></td>
         	
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000" ><br></td>
        </tr> 
        <tr>
        	<td colspan="11" style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000" align="left"><font color="#000000">&nbsp;&nbsp;&nbsp;Drawn on </font></td>
             <td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000;border-right: 1px solid #000000" colspan=13 align="center"><font color="#000000">&nbsp;<?php echo $result[0]['BankName'] ?>,&nbsp;<?php echo $result[0]['BranchName']?></font></td>
         	
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000" ><br></td>
        </tr> 
        <tr>
        	<td colspan="14" style=" border-left: 1px solid #000000" align="left"><font color="#000000"><br> </font></td>
             <td style="  border-right: 1px solid #000000" colspan=10 align="center"><font color="#000000">(Name of the Bank and Branch)</font></td>
         	
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000" ></td>
        </tr> 
        <tr>
        	<td colspan="5" style=" border-left: 1px solid #000000" align="left"><font color="#000000">&nbsp;&nbsp;&nbsp;Date: </font></td>
             <td style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;border-left: 1px solid #000000; border-top: 1px solid #000000;" colspan=6 align="center"><font color="#000000"><?php echo getDisplayFormatDate($result[0]['Challan_date']); ?></font></td>
             <td style="  border-right: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;border-top: 1px solid;" colspan=13 align="left"><font color="#000000"></font></td>
         	
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000" ><br></td>
        </tr> 
         <tr>
        	<td colspan="14" style=" border-left: 1px solid #000000" align="left"><font color="#000000"><br> </font></td>
             <td style="  border-right: 1px solid #000000" colspan=10 align="center"><font color="#000000">Signature of person making payment</font></td>
         	
         	<td align="left" colspan="11" valign=middle style=" border-right: 1px solid #000000;" >Rs.</td>
        </tr> 
         <tr>
        	<td colspan="24" style=" border-left: 1px solid #000000;border-top: 1px solid #000000;border-right: 1px solid #000000;" align="center"><font color="#000000"><b>Taxpayers Counterfoil </b>(To be filled up by tax payer) </font></td>
         	
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000;border-top: 1px solid #000000" ><b><font color="#000000">SPACE FOR BANK SEAL </font></b></td>
        </tr>
         <tr>
        	<td colspan="4" style=" border-left: 1px solid #000000" align="left"><font color="#000000">&nbsp;&nbsp;&nbsp;TAN</font></td>
             <?php for($t=0 ; $t<10; $t++)
			{?>
    			<td colspan="2" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><?php echo $SocietyTanArr[$t]?></td>
		<?php }?>
            <!-- <td style=" border-right: 1px solid #000000;border-left: 1px solid #000000; border-top: 1px solid #000000;border-bottom: 1px solid #000000;" colspan=18 align="center"><font color="#000000"></font></td>-->
         	
         	<td align="left" colspan="11" valign=middle style=" border-right: 1px solid #000000;" ><br></td>
        </tr> 
        <tr>
        	<td colspan="4" style=" border-left: 1px solid #000000" align="left"><font color="#000000">&nbsp;&nbsp;&nbsp;Received from</font></td>
             <td style=" border-right: 1px solid #000000;border-left: 1px solid #000000;border-bottom: 1px solid #000000;" colspan=20 align="left"><font color="#000000">&nbsp;&nbsp;<?php echo $SocietyName ?></font></td>
         	
         	<td align="left" colspan="11" valign=middle style=" border-right: 1px solid #000000;" ><br></td>
        </tr> 
         <tr>
        	<td colspan="24" style=" border-left: 1px solid #000000;border-right: 1px solid #000000;" align="center"><font color="#000000;">(Name)</font></td>
             <?php 
			 if($challan_no <> '')
			 {?>
				<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000;" >Challan No.  - &nbsp;&nbsp;<?php echo $challan_no?></td>
			<?php
			 }
			 else
			 {?>
				<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000;" > <br></td> 
			 <?php 
			 }
			 ?>
         	
        </tr>
         <tr>
        	<td colspan="11" style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000;border-top: 1px solid #000000;" align="left"><font color="#000000">&nbsp;&nbsp;&nbsp;Cash/ Debit to A/c /Cheque No.</font></td>
            
          
            <td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000;border-top: 1px solid #000000;" colspan=5 align="center"><font color="#000000"><br></font></td>
            <td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000;border-top: 1px solid #000000;" colspan=3 align="center"><font color="#000000">For Rs.</font></td>
            <td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000;border-right: 1px solid #000000;border-top: 1px solid #000000;" colspan=5 align="right"><font color="#000000"><?php echo $result[0]['TotalAmount']?><br></font></td>
         	
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000" ><br></td>
        </tr>
        
        <tr>
        	<td colspan="5" style=" border-left: 1px solid #000000;" align="left"><font color="#000000">&nbsp;&nbsp;&nbsp;Rs. (in words)</font></td>
            
          
            <td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000;border-right: 1px solid #000000" colspan=19 align="left"><font color="#000000">&nbsp;<?php  echo "Rupees ". convert_number_to_words(number_format($result[0]['TotalAmount'],0)) . ' Only.'?></font></td>
          
         	
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000" ><br></td>
        </tr> 
         <tr>
        	<td colspan="5" style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000;" align="left"><font color="#000000">&nbsp;&nbsp;&nbsp;Drawn on</font></td>
            
          
            <td style=" border-bottom: 1px solid #000000; border-left: 1px solid #000000;border-right: 1px solid #000000" colspan=19 align="left"><font color="#000000">&nbsp;<?php echo $result[0]['BankName'] ?>,&nbsp;<?php echo $result[0]['BranchName']?></font></td>
          
         	
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000" ><br></td>
        </tr>  
           <tr>
        	<td colspan="24" style=" border-left: 1px solid #000000;border-right: 1px solid #000000;" align="center"><font color="#000000">(Name of the Bank and Branch) </font></td>
         	
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000;" ><br></td>
        </tr>
        <tr>
        	<td colspan="24" style=" border-left: 1px solid #000000;border-right: 1px solid #000000;" align="center"><font color="#000000">Company/Non-Company Deductees </font></td>
         	
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000;" ><br></td>
        </tr>
        <tr>
        	<td colspan="24" style=" border-left: 1px solid #000000;border-right: 1px solid #000000;" align="center"><font color="#000000">on account of Tax Deducted at Source (TDS)/Tax Collected at Source (TCS) from____(Fill up Code) <?php echo $result[0]['NatureOfTDS'] ?></font></td>
         	
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000;" ><br></td>
        </tr>
         <tr>
        	<td colspan="24" style=" border-left: 1px solid #000000;border-right: 1px solid #000000;" align="center"><font color="#000000">(Strike out whichever is not applicable) </font></td>
         	
         	<td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000;" ><br></td>
        </tr>
         <tr>
        	<td colspan="8" style=" border-left: 1px solid #000000;" align="center"><font color="#000000">for the Assessment Year </font></td>
            <td  class="tdStyle2"  align="center" valign=middle><?php echo $startYearNoArr[0];?></td>
			<td  class="tdStyle2" align="center" valign=middle><?php echo $startYearNoArr[1];?></td>
			<td class="tdStyle2" align="center" valign=middle><?php echo $startYearNoArr[2];?></td>
			<td class="tdStyle2" align="center" valign=middle><?php echo $startYearNoArr[3];?></td>
			<td align="left" valign=middle> -</td>
			<td class="tdStyle2" align="center" valign=middle><?php echo $endYearNoArr[0]?></td>
			<td class="tdStyle2" align="center" valign=middle><?php echo $endYearNoArr[1]?></td>
           
             <td colspan="9" style="border-right: 1px solid #000000;" align="center"><font color="#000000"><br> </font></td>
         	
         	<td align="left" colspan="11" valign=middle style=" border-right: 1px solid #000000;" >Rs.</td>
        </tr>
        <tr>
        <td align="center" colspan="24" valign=middle style=" border-left: 1px solid #000000;border-bottom: 1px solid #000000; border-right: 1px solid #000000;" ><br></td>
        <td align="center" colspan="11" valign=middle style=" border-right: 1px solid #000000;border-bottom: 1px solid #000000;" ><br></td>
        </tr>
</table>
</div>
</div>
<!--<br><br>
 <input type="button" id="viewbtn" value="View As PDF"  onclick="ViewPDF('<?php echo $_REQUEST['id'];?>');"/> -->
 <!--<input type="button" id="dwnbtn" value="Download PDF"  onclick="setflag();ViewPDF('<?php echo $_REQUEST['UnitID']; ?>', '<?php echo $_REQUEST['inv_number'];?>');"/> -->
</center>


	
<!--<br clear=left>-->
<!-- ************************************************************************** -->
</body>

</html>
<script>
function PrintPage() 
	{
		document.getElementById('mainDiv').style.width='90%';
		var btnPrint = document.getElementById("Print");
		btnPrint.style.visibility = 'hidden';
		window.print();
		document.getElementById('mainDiv').style.width='70%';
       	btnPrint.style.visibility = 'visible';
		
	}
	

	
</script>
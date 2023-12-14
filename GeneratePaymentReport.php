<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Cheque Leaf Book Report</title>
</head>
<?php 
include_once "ses_set_as.php"; 
?>

<?php
include_once "common/CommonMethods.php";
include_once "classes/GeneratePaymentReport.class.php";
include_once "classes/dbconst.class.php";


$obj_view_bank_statement = new Payment_Report($m_dbConn);

//$show_owner_details=$obj_view_unit_report->show_owner_name($_REQUEST["uid"]);


//$show_due_details=$obj_view_unit_report->show_due_details($_REQUEST["uid"]);
?>

<?php

 if(isset($_SESSION['admin']))
{
	//include_once("includes/header.php");
}
else
{
	//include_once("includes/head_s.php");
}

if(isset($_GET['ssid'])){if($_GET['ssid']<>$_SESSION['society_id']){?><script>window.location.href = "logout.php";</script><?php }}


?>

<?php
//echo $_REQUEST['CanClose']  ;

//$details = $obj_view_bank_statement->getDetails($_REQUEST["LedgerID"]);
//$bankDetails = $obj_view_bank_statement->getBankDetails($_REQUEST["LedgerID"]);
//echo "test2";
$AllLeaf = $obj_view_bank_statement->GetLeafDetails($_REQUEST["leafid"]);

//echo $SlipDetails[0]['PayerBank'];
//print_r($SlipDetails);
?>

<html>
<head>
	<style>table{}</style>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <script type="text/javascript" src="js/ajax.js"></script>
	<script language="javascript" type="application/javascript">
	function printTable()
	{
	  var divToPrint=document.getElementById('Slip');
	  newWin= window.open("");
	  newWin.document.write(divToPrint.outerHTML);
	  newWin.print();
	  newWin.close();
	  //alert('test');
	  <?php
	  /*print_r($_REQUEST['CanClose']);  
	  if($_REQUEST['CanClose'] == 1)
	  {
		  $obj_view_bank_statement->CloseDepositSlip($_REQUEST['depositid']); 
	  }*/
	  ?>
	  //alert('test');
	}

	function go_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
		});
        setTimeout('hide_error()',8000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }
	</script>
</head>
<body>
<br><br>
<?php
$star = "<font color='#FF0000'>*</font>";
if(isset($_REQUEST['msg']))
{
	$msg = "Sorry !!! You can't delete it. ( Dependency )";
}
else if(isset($_REQUEST['msg1']))
{
	$msg = "Deleted Successfully.";
}
else{}
?>
<center>
<table id="Slip" width="90%" border="1px" cellspacing="5" cellpadding="5" style="border-collapse:collapse;" align="center">
<tr><td colspan="6" style="text-align:center"><center><font color="#43729F" size="+1"><b>CHEQUE LEAF BOOK REPORT</b></font></center></td></tr>
<tr><td colspan="6" style="text-align:right">
<br>
<?php $sql = "select curdate()as curdate";
		//$res = $this->m_dbConn->select($sql);	
		$res = $m_dbConn->select($sql);	
		echo "Date: ".getDisplayFormatDate( $res[0]['curdate'], "-");
?>
</td></tr>
<tr>
<td colspan="6"><div id="IssuedInfo"></div></td>
</tr>
<tr>
<td colspan="6"><div id="Info"></div></td>
</tr>
<tr><td colspan="6" align="left">
<h3><?php
$CONSTSTRING = "<b>Cheques Not Issued : </b>";
$ISSUED = "<b>Cheques Issued : </b>";
$ChequesNotIssued = $CONSTSTRING;
$ChequesIssued = $ISSUED; 
$BankName = $obj_view_bank_statement->getLedgerName($AllLeaf[0]['BankID']);
$Bank =  $BankName[0]["ledger_name"];
echo $Bank. " A/C No : ".$obj_view_bank_statement->GetAccountNo($AllLeaf[0]['BankID']);
echo " Branch : ". $obj_view_bank_statement->GetBankBranch($AllLeaf[0]['BankID']);
?></h3>
</td>
</tr>

<tr><th align="center" colspan="6"><b>ISSUED CHEQUE DETAILS:</b></th></tr>
<tr>
	<th style="width:5%;text-align:center;">Sr.</th>
    <th style="width:10%;text-align:center;">ChequeNo.</th>
    <th style="width:30%;text-align:center;">Paid To</th>
    <th style="width:15%;text-align:center;">Cheque Date</th>
    <th style="width:15%;text-align:center;">Amount</th>
    <th style="width:25%;text-align:center;">Comments</th>
</tr>
	<?php
		$TotalAmount = 0;
		
		for($i = 0; $i < sizeof($AllLeaf); $i++)
		{
			$aryID = array();

			//print_r($AllLeaf[$i]);
			$aryID = $obj_view_bank_statement->GetChequeEntryDetailsIDForLeaf($AllLeaf[$i]['id'], $AllLeaf[$i]['CustomLeaf']);

			$StartChequeNumber = $AllLeaf[$i]['StartCheque'];
			$EndChequeNumber = $AllLeaf[$i]['EndCheque'];

			$aryChqNumber = array();
			for($iChqNum = $StartChequeNumber; $iChqNum <= $EndChequeNumber; $iChqNum++)
			{
				array_push($aryChqNumber, $iChqNum);
			}

			for($iChqCount = 0; $iChqCount < sizeof($aryID); $iChqCount++)
			{
				$SlipDetails = $obj_view_bank_statement->GetChqLeafDetailsByID($aryID[$iChqCount]);
				//print_r($SlipDetails);
				if(isset($SlipDetails))
				{
					for($i = 0; $i < sizeof($SlipDetails); $i++)
					{
						$PaidBy = $SlipDetails[$i]['PaidTo'];
						$UnitNo = "";
						if($PaidBy <> "")
						{
							$UnitNo = $obj_view_bank_statement->getLedgerName($PaidBy);
						}
						$ChequeDate = getDisplayFormatDate($SlipDetails[$i]['ChequeDate']);
						$ChequeNo = $SlipDetails[$i]['ChequeNumber'];
						$Amount = $SlipDetails[$i]['Amount'];
						$comments = $SlipDetails[$i]['Comments'];		

						$aryChqNumber = array_flip($aryChqNumber);
						unset($aryChqNumber[$ChequeNo]);
						$aryChqNumber = array_flip($aryChqNumber);	
							
						?>
				   		<tr cellspacing="2">
							<td style="width:5%;text-align:center;"><?php echo ($iChqCount + 1) ?></td>
							<td style="width:10%;text-align:center;"><?php echo $ChequeNo ?><br>
							<td style="width:30%;text-align:center;"><?php echo $UnitNo[0]['ledger_name'] ?></td>
							<td style="width:15%;text-align:center;"><?php echo $ChequeDate ?></td>
							<td style="width:15%;text-align:right;"><?php echo number_format($Amount,2) ?></td>
							<td style="width:25%;text-align:center;"><?php echo $comments ?></td>
							<?php $TotalAmount = $TotalAmount + $Amount; ?>
                    	</tr>
                    
                    	<?php  
                    }

					if($ChequesIssued == $ISSUED)
					{
						$ChequesIssued .=  strval($ChequeNo);
					}
					else
					{
						$ChequesIssued .= ", " . strval($ChequeNo);
					}
				}
				/*else
				{
					//echo "cheque not issued ".$iChqCount."";
					if($ChequesNotIssued == $CONSTSTRING)
					{
						$ChequesNotIssued .=  strval($iChqCount);
					}
					else
					{
						$ChequesNotIssued .= ", " . strval($iChqCount);
					}
				}*/
			}

			if($AllLeaf[$i]['CustomLeaf'] == 0)
			{
				$ChequesNotIssued .= implode(', ', $aryChqNumber);
			}
		}
		?>
        
        <!--<table border="1" style="width:100%"><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>-->
        <tr><td  colspan="3" align="left"><b><?php 
		if($TotalAmount <> "")
		{
			 echo "Rupees ".convert_number_to_words($TotalAmount);  ?> Only
             <?php
		}
		?>
        </td><td align="center"><b>Total (Rs.):</b></td><td align="right"><b><?php echo number_format($TotalAmount,2) ?></b></td><td></td></tr>
        <!--<tr>
        <td colspan="4" style="border:none">
			<div id="bill_footer" style="text-align:left;border-top:0px solid black;padding-left::10px;">
        	<br><br>Cashier's Signature
        	</div>
		</td>
        <td colspan="4" style="border:none">
			<div id="bill_footer" style="text-align:right;border-top:0px solid black;padding-right:10px;">
        	<br><br>Authorised Signatory
        	</div>
		</td>-->
		</tr>
</table>
<?php if($ChequesIssued != $ISSUED)
{
	?>
<script>document.getElementById("IssuedInfo").innerHTML = '<?php echo $ChequesIssued ?>'</script>
<?php 
}
else
{
?>
<script>document.getElementById("IssuedInfo").innerHTML = '<?php echo $ChequesIssued = $ChequesIssued . "---" ?>'</script>
<?php
}
?>


<?php if($ChequesNotIssued != $CONSTSTRING)
{
?>
  
<script>document.getElementById("Info").innerHTML = '<?php echo $ChequesNotIssued ?>'</script>
<?php 
}
else
{
?>
<script>document.getElementById("Info").innerHTML = '<?php echo $ChequesNotIssued = $ChequesNotIssued . "---" ?>'</script>
<?php
}
?>

</br>
<button value="Print" id="Print" onClick="printTable()" style="width:100px;height:50px" ><b>Print</b></button>
</center>
		
</body>
</html>
  
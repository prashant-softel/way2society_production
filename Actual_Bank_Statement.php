<?php 
include_once "ses_set_as.php"; 
include_once("classes/dbconst.class.php");

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

?>

<?php
include_once "classes/bank_statement.class.php";
include_once("classes/utility.class.php");
include_once("classes/home_s.class.php"); 

if($_REQUEST["LedgerID"] == "")
{
	echo "<script>alert('Error ! Please pass LedgerID to generate statement');</script>";
	exit;
}

$obj_view_bank_statement = new bank_statement($m_dbConn);
$obj_Utility = new utility($m_dbConn);
$obj_AdminPanel = new CAdminPanel($m_dbConn);
$sHeader = $obj_Utility->getSocietyDetails();
$SocietyHeader=$sHeader;

$Society=str_replace('\r\n','',$SocietyHeader);

?>

<?php

 if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else
{
	include_once("includes/head_s.php");
}

if(isset($_GET['ssid'])){if($_GET['ssid']<>$_SESSION['society_id']){?><script>window.location.href = "logout.php";</script><?php }}


?>

<?php
if($_POST['ledgerid'] == "")
{	
$from_month=date('Y-m-d', strtotime(date('Y-m-01')." -1 month"));
$to_month=date('t-m-Y');
$bankName = $obj_view_bank_statement->getBankName($_REQUEST["LedgerID"]);
$details = $obj_view_bank_statement->getActualBankDetails($_REQUEST["LedgerID"],$from_month,$to_month);
$bankDetails = $obj_view_bank_statement->getBankDetails($_REQUEST["LedgerID"]);
$arParentDetails = $obj_Utility->getParentOfLedger($_REQUEST["LedgerID"]);
$bankID = $_REQUEST["LedgerID"];
}
else
{
	$bankName = $obj_view_bank_statement->getBankName($_POST['ledgerid']);
	$details = $obj_view_bank_statement->getActualBankDetails($_POST['ledgerid'], $_POST['from_date'], $_POST['to_date'], $_POST['tran_type']);
	$bankDetails = $obj_view_bank_statement->getBankDetails($_POST['ledgerid']);
	$arParentDetails = $obj_Utility->getParentOfLedger($_POST['ledgerid']);	
	$bankID = $_POST['ledgerid'];
}
$CategoryID = $arParentDetails['category'];	
$arBankDetails = $obj_AdminPanel->GetBankAccountAndBalance(); 

//var_dump($arBankDetails);
?>

<html>
<head>
	
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <script type="text/javascript" src="js/ajax.js"></script>
    <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
	<script language="javascript" type="application/javascript">
	

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
	
	function ValidateDate()
	{
		var fromDate = document.getElementById('from_date').value;
		var toDate = document.getElementById('to_date').value;		
		var isFromDateValid = jsdateValidator('from_date',fromDate,minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate);
		var isToDateValid = jsdateValidator('to_date',toDate,minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate);
		if(isFromDateValid == false || isToDateValid == false)
		{
			return false;	
		}
		return true;
	}
	
	
	
	window.onfocus = function() {
		var result = localStorage.getItem('refreshPage');	
		//alert(result);
		if(result != null && result > 0 )
		{	
			localStorage.setItem('refreshPage', "0");
			location.reload();
		}
	};
	
	</script>
  
<style>
 @media print {
  /* style sheet for print goes here */
  .PrintClass
  {
		display:block;
   }
}
/*
@media print 
{
	
  a[href]:after { content: none !important; }
  img[src]:after { content: none !important; }
  html, body {
            margin: 0;
            padding: 0;
            background: #FFF; 
            font-size: 9.5pt;
          }
}*/
@media print {
  * { margin: 0 !important; padding: 0 !important;  }
 
  html, body, .page{
   
   alignment-adjust:central;
    background: #FFF; 
    font-size: 9.5pt;
	border-color:#FFFFFF !important;
	color:#fff !important;
	
  }

 
   a[href]:after { content: none !important; }
  img[src]:after { content: none !important; }
 
 
}
 </style>   
</head>
<body>
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
<br>
<div class="panel panel-info" id="panel" style="display:block">
	<?php 	 
	if($CategoryID == CASH_ACCOUNT) { ?>
    	<div class="panel-heading" id="pageheader">Cash & Bank Statement</div>
    <?php } else { ?>
    <div class="panel-heading" id="pageheader">Actual Bank Statement - <?php echo $bankName[0]['ledger_name']; ?></div>
    <?php }  ?>
 </br>
<div align="center"> 
<form name="filter" id="filter" action="Actual_Bank_Statement.php?LedgerID=<?php echo $_REQUEST["LedgerID"]; ?>" method="post" onSubmit="return ValidateDate();">
	<table style="width:95%; border:1px solid black; background-color:transparent; ">
    <tr> <td colspan="3"><br/> </td></tr>
    	<tr>
        	<td> &nbsp; Please Select Bank : </td>            
        	<td>
        		<select name="ledgerid" id="ledgerid">				                    
					<?php                        
                    foreach($arBankDetails as $arData=>$arvalue)
                    {
                       $BankName = $obj_AdminPanel->GetLedgerNameFromID($arvalue["LedgerID"]);						                                       
                    ?>    	
					<option value="<?php echo $arvalue["LedgerID"]; ?>"> <?php echo $BankName; ?> </option>
					<?php                                       
                    }?>
               </select>
   			</td>
            <td> From :</td>                      
			<td><input type="text" name="from_date" id="from_date"  class="basics" size="10" style="width:80px;" value = "<?php echo getDisplayFormatDate(date('Y-m-d', strtotime(date('Y-m-01')." -1 month")))?>"/></td>
            <td> To :</td>                     
			<td><input type="text" name="to_date" id="to_date"  class="basics" size="10" style="width:80px;" value="<?php echo getDisplayFormatDate(date('t-m-Y'));?>"/></td>
            <td> Transaction Type :</td>
            <td>
            	<select name="tran_type" id="tran_type" style="width:80px;">
                	<option value="0">All</option>
                    <option value="1">Withdrawals</option>
                    <option value="2">Deposits</option>
                </select>
           	</td>
            <td><input type="submit" name="submit" value="Submit" /> </td>
     	</tr>        
        <tr> <td colspan="3"><br/> </td></tr>
    </table>
</form>

</div>
<script>
<?php

if($_POST['ledgerid'] <> "")
{?>
	document.getElementById('ledgerid').value = "<?php echo $_POST['ledgerid'];?>";
<?php 
}
else 
{ ?>
	document.getElementById('ledgerid').value = "<?php echo $_REQUEST['LedgerID'];?>";
<?php 
} 
if(isset($_POST['from_date']) && isset($_POST['to_date']) )
{?>
	document.getElementById('from_date').value = "<?php echo $_POST['from_date']; ?>";
	document.getElementById('to_date').value = "<?php echo $_POST['to_date']; ?>";
	document.getElementById('tran_type').value = "<?php echo $_POST['tran_type'] ?>";
<?php }
else
{
	$_POST['from_date'] =$from_month;// $_SESSION['default_year_start_date'];	
	$_POST['to_date'] = $to_month;//$_SESSION['default_year_end_date']; 

} ?>
</script>
<br /> <br />   
<div style="width:100%; height:100px;">
<div style="float:left; width:35%; font-size:16px; font-weight:bold;">  
<?php echo $bankName[0]['ledger_name']; ?></br>
<?php echo $bankDetails[0]['BranchName']; ?></br>
<?php echo $bankDetails[0]['Address'] ;?>
</div>
<div style="float:right; width:55%;"> 
<table>
<tr style="height:30px; vertical-align:middle;">	
    <th style="text-align:center; background-color:#D9EDF7; width:12%;">Account Number </th>
    <th style="text-align:center; background-color:#D9EDF7; width:19%;">Statement Period </th>
    <th style="text-align:center; background-color:#D9EDF7; width:12%;">Opening Balance</th>
    <th style="text-align:center; background-color:#D9EDF7; width:12%;">Closing Balance</th>      
</tr>
<tr style="height:30px; vertical-align:middle;">	
    <td style="text-align:center;"><?php echo $bankDetails[0]['AcNumber'] ; ?></td>
    <td style="text-align:center;"><?php if($balanceBeforeDate > 0) { echo getDisplayFormatDate($_SESSION['default_year_start_date']); } else { echo getDisplayFormatDate($details[0]['Date']);} ?> To <?php echo getDisplayFormatDate($details[sizeof($details) - 1]['Date']); ?></td>
    <td style="text-align:center;"><label id="opening_bal"> </label></td>
    <td style="text-align:center;"><label id="closing_bal"> </label></td>           
</tr>
</table>
</div>
</div>

<center>
<table id="example" class="display" width="100%" >
<thead>
<tr style="height:35px;">
	<th style="width:10%;text-align:center;">Date</th>
    <th style="width:15%;text-align:center;">Note</th>
    <th style="width:5%;text-align:center;">Voucher Type</th>
    <th style="width:9%;text-align:center;">Ref.</th>
    <th style="width:12%;text-align:center;">Withdrawals</th>
    <th style="width:12%;text-align:center;">Deposits</th>
    <th style="width:12%;text-align:center;">Balance</th>
    <th style="width:12%;text-align:center;">Status</th>
</tr>
</thead>
<tbody>
	<?php		
		$balance = $details[0]['Bank_Balance'] - $details[0]['Debit'] - $details[0]['Credit'];
		$totalWithdrawals = 0;
		$totalDeposits = 0;
		$ledgerName = "";
		$chequeDetails="";
		$ledger_details="";
		$paidAmount = 0;
		$chequeNumber = "-";
		$voucherType = "-";
		$openingBalancePresent = 0;
		$reference = 0;
		
		if($balance >= 0)
		{?>
			<tr style="height:30px;">
                <td style="width:10%;text-align:center;"><?php echo getDisplayFormatDate($_POST['from_date']);  ?></td>
                <td style="width:20%;text-align:left;"><?php echo 'Opening Balance'; ?></td>
                <td style="width:15%;text-align:left;"><?php echo '-'; ?></td>
                <td style="width:5%;text-align:center;"><?php echo '-' ?></td>
                <td style="width:9%;text-align:center;"><?php echo '-' ?></td>
                <td style="width:12%;text-align:right;"><?php echo '-'?></td>
                <td style="width:12%;text-align:right;"><?php echo number_format($balance,2) ;?></td>
               <td>&nbsp;</td>
                <!--<td style="width:12%;text-align:right;">
               
               </td>-->
       		</tr>
<?php	}
	
		for($i = 0; $i < sizeof($details); $i++)
		{ ?>
           <tr style="height:30px;">
            <td style="width:10%;text-align:center;"><?php echo getDisplayFormatDate($details[$i]['Date']);?></td>
            <td style="width:20%;text-align:left;"><?php echo $details[$i]['Bank_Description'];
			if(!empty($details[$i]['Notes']))
			{
				echo "<br>".$details[$i]['Notes'];
			}
			?></td>
            <?php
            
			$VoucherType = '-';
			
			if($details[$i]['Debit'] > 0)
			{
				$totalWithdrawals += $details[$i]['Debit']; 
				$balance += $details[$i]['Debit'];
				$VoucherType = 'Payment';
			}
			else if($details[$i]['Credit'] > 0)
			{
				$totalDeposits += $details[$i]['Credit'];
				$balance += $details[$i]['Credit'];
				$VoucherType = 'Receipt';
			}?>
            
            <td style="width:5%;text-align:center;"><?php echo $VoucherType; ?></td>
            <td style="width:9%;text-align:center;"><?php echo $details[$i]['ChequeNo']; ?></td>
            
			<?php if($details[$i]['Reco_Status'] == 1)
			{ 
				if($details[$i]['Debit'] > 0)
				{ ?>
					
                    <td style="width:12%;text-align:right;"><a onclick="redirectTransaction(<?php echo $details[$i]['Id']?>);"><?php echo number_format($details[$i]['Debit'], 2); ?></a></td>	
                <?php }
				else
				{ ?>
					<td style="width:12%;text-align:right;"><?php echo number_format($details[$i]['Debit'], 2); ?></td>
				<?php }
				
				if($details[$i]['Credit'] > 0)
				{ ?>
					     <td style="width:12%;text-align:right;"><a onclick="redirectTransaction(<?php echo $details[$i]['Id']?>);"><?php echo number_format($details[$i]['Credit'], 2); ?></a></td>
				<?php }
				else
				{ ?>
					    <td style="width:12%;text-align:right;"><?php echo number_format($details[$i]['Credit'], 2); ?></td>					
				<?php }
				
				?>


			<?php }
			else
			{ ?>
				<td style="width:12%;text-align:right;"><?php echo number_format($details[$i]['Debit'], 2); ?></td>	
                <td style="width:12%;text-align:right;"><?php echo number_format($details[$i]['Credit'], 2); ?></td>
			<?php }
			?>
			

            <td style="width:12%;text-align:right;"><?php echo number_format($details[$i]['Bank_Balance'],2); ?></td>
            
           <td align="center">
           <?php 
			   if($details[$i]['Reco_Status']==1)
			   {
					echo "<img src='images/clear.png' alt='Cleared' width='25' height='25'>";  
					$StatementCount = $obj_view_bank_statement->getStatementCount($details[$i]['Id']);
					if($StatementCount[0]['statement_count'] >1 )
					{
						echo '<span><i class="fa fa-star" aria-hidden="true" style="color:red"></i></span>';
					} 
				}
			   ?>
           </td>
        </tr>
<?php  } ?>
 <script>
		document.getElementById("closing_bal").innerHTML = '<?php echo number_format($balance,2) ?>';
		<?php if($openingBalancePresent == 0) {	?>	
			document.getElementById("opening_bal").innerHTML = '<?php echo number_format($balanceBeforeDate,2); ?>';
		<?php } ?>
	</script>
<tr style="text-align:center;background-color:#D8DDF5;height:30px;">
	 <td ></td>
    <td ></td>
    <td > **Totals** </td>
     <td ></td>
     <td ></td>
    <td style="display: none;"></td>
    <td style="display: none;"></td>
	<td style="display: none;"></td>
	<td style="display: none;"></td>
    <td style="text-align:right;"> <?php echo number_format($totalWithdrawals,2); ?> </td>
    <td style="text-align:right;"><?php echo number_format($totalDeposits,2) ; ?> </td>
    <td style="text-align:right;"><?php echo number_format($balance, 2); ?> </td>
  
</tr>
</tbody>
</table>
</center>
		<?php //echo $sHeader?>
</div>

<script type="text/javascript" src="js/bootstrap-modalmanager.js"></script>
<script type="text/javascript" src="js/bootstrap-modal.js"></script>
  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content" style="border-radius: 15px;">
        <div class="modal-header" style="background-color: #d9edf7;min-height: 0px;padding: 0px;border-top-left-radius: 10px;border-top-right-radius: 10px;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" style="margin: 0px;padding: 13px">This is a split entry</h4>
        </div>
        <div class="modal-body">
           <div class="formSubmitResponse" align="left" style="font-size: 15px;font-weight: 400;"></div>
        </div>
        <div class="modal-footer">
			<a data-dismiss="modal" data-dismiss="modal"><span class="btn btn-primary">Done</span></a>
        </div>
      </div>
      
    </div>
  </div>
<!-- <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button>   -->
</div> 

<?php include_once "includes/foot.php"; ?>
  
<script>

$(document).keyup(function(e) {    
    if (e.keyCode == 27) 
	{ 
		//escape key
		var sHeaders = document.getElementsByClassName('PrintClass'), i;
	
		for (i = 0; i < sHeaders.length; i += 1)
		{
			sHeaders[i].style.display = 'none';
			
		}
    }
});
printMessage = '<?php echo $sHeader?> ';
printMessage += '<center><font style="font-size:14px;" id="statement" class="PrintClass"><b>Bank Statement<br><?php echo $bankName[0]['ledger_name']; ?><br><?php if($balanceBeforeDate > 0) { echo getDisplayFormatDate($_SESSION['default_year_start_date']); } else { echo getDisplayFormatDate($details[0]['Date']);} ?> To <?php echo getDisplayFormatDate($details[sizeof($details) - 1]['Date']); ?></b></font></center>';
 
$(document).ready(function() {
	
	$('#example').dataTable(
 {
	"bDestroy": true
}).fnDestroy();

			//if(localStorage.getItem("client_id") != "" && localStorage.getItem("client_id") != 1)
			//{
					$('#example').dataTable( {
					dom: 'T<"clear">Blfrtip',
					"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
					buttons: 
					[
						{
							extend: 'colvis',
							width:'inherit'/*,
							collectionLayout: 'fixed three-column'*/
						}
					],
					"oTableTools": 
					{
						"aButtons": 
						[
							{ "sExtends": "copy", "mColumns": "visible" },
							{ "sExtends": "csv", "mColumns": "visible" },
							{ "sExtends": "xls", "mColumns": "visible" },
							{ "sExtends": "pdf", "mColumns": "visible" },
							{ "sExtends": "print", "mColumns": "visible","sMessage": printMessage + " " }
						],
					 "sRowSelect": "multi"
				},
				aaSorting : [],
					
				fnInitComplete: function ( oSettings ) {
					$(".DTTT_container").append($(".dt-button"));
				}
				
			} );	
		 
		});

 $(function()
		{
			$.datepicker.setDefaults($.datepicker.regional['']);
			$(".basics").datepicker({ 
			dateFormat: "dd-mm-yy", 
			showOn: "both", 
			buttonImage: "images/calendar.gif", 
			buttonImageOnly: true,
			minDate: minGlobalCurrentYearStartDate,
			maxDate: maxGlobalCurrentYearEndDate
		})});
		
function redirectTransaction(act_bank_statement_id)
{
	$.ajax({
			url : "ajax/ajaxBankDetails.php",
			type : "POST",
			data: { "act_bank_statement_id":act_bank_statement_id,"method":"getReconcileDetails"} ,
			success : function(data)
			{	
				var a		= data.trim();
				var arr1	= new Array();
				var arr2	= new Array();
				arr1		= a.split("@@@");
				arr2 = JSON.parse("["+arr1[1]+"]");
				arr2 = arr2[0];
				if(arr2.length > 1)
				{
					var table = "<table style='width:100%'>";
					for(var i = 0; i < arr2.length; i++)
					{
						table += "<tr><td>"+arr2[i]['LedgerName']+"</td><td><a href="+arr2[i]['Url']+" target='_blank'>"+arr2[i]['Amount']+"</a></td></tr>";
					}
					table += "</table>"; 
					$('.formSubmitResponse').html(table);
					$('#myModal').modal();
				}
				else
				{
					window.open(arr2[0]['Url'],'_blank');
				}
			}
	});
}		
		
			
</script>




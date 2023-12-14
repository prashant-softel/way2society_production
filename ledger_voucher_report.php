<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Ledger Voucher Report </title>


</head>
<?php 
include_once("includes/head_s.php");

include_once("classes/dbconst.class.php");
include_once "classes/utility.class.php";
include_once("classes/FixedDeposit.class.php");
 
include_once("classes/view_ledger_details.class.php");
$obj_ledger_details = new view_ledger_details($m_dbConn);
$m_objUtility = new utility($m_dbConn);
$sHeader = $m_objUtility->getSocietyDetails();
if($_SESSION['default_year_end_date'] >= date('Y-m-01'))
{
	$from = date('Y-m-01');
	$lastDay = date('Y-m-t');
	if(isset($_POST['from_date']) && !empty($_POST['from_date'])) {
		$from = date("Y-m-d",strtotime(getDBFormatDate($_POST['from_date'])));
	} 
	if(isset($_POST['to_date']) && !empty($_POST['to_date'])) {
		$lastDay=date('Y-m-d',strtotime(getDBFormatDate($_POST['to_date'])));
	}
}
else
{
	$to = $_SESSION['default_year_end_date'];
	$lastDay=date('Y-m-d',strtotime($to));
	$from = date("Y-m-01",strtotime($lastDay));
	
	if(isset($_POST['from_date']) && !empty($_POST['from_date'])) {
		$from = date("Y-m-d",strtotime(getDBFormatDate($_POST['from_date'])));
	} 
	if(isset($_POST['to_date']) && !empty($_POST['to_date'])) {
		$lastDay=date('Y-m-d',strtotime(getDBFormatDate($_POST['to_date'])));
	}
}
$LedgerDetail=$obj_ledger_details->AllLegerDetail('',$from ,$lastDay);
$data=$LedgerDetail;

?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<link href="css/messagebox.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/ajax.js"></script>      
    <script type="text/javascript" src="js/jsViewLedgerDetails.js"></script>      
	<script type="text/javascript" src="js/ajax_new.js"></script> 
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
	
		window.onfocus = function() {
		var result = localStorage.getItem('refreshPage');	
		if(result != null && result > 0 )
		{	
			localStorage.setItem('refreshPage', "0");
			location.reload();
		}
	};
	</script>
	<script>
function Export_xls()
{	
	window.open('data:application/vnd.ms-excel,' + encodeURIComponent( $("#showTable").html()));
}
function Export_csv()
{
	var csv = [];
	var rows = document.querySelectorAll("#reports tr");
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        for (var j = 0; j < cols.length; j++) 
            row.push(cols[j].innerText);
        csv.push(row.join(","));        
    }

    // Download CSV file
    downloadCSV(csv.join("\n"), "download.csv");
	
}
function downloadCSV(csv, filename) {
    var csvFile;
    var downloadLink;

    // CSV file
    csvFile = new Blob([csv], {type: "text/csv"});
	// Download link
    downloadLink = document.createElement("a");
	// File name
    downloadLink.download = filename;
	// Create a link to the file
    downloadLink.href = window.URL.createObjectURL(csvFile);
	// Hide download link
    downloadLink.style.display = "none";
	// Add the link to DOM
    document.body.appendChild(downloadLink);
	// Click download link
    downloadLink.click();
}
</script>
<style>
@media print
{    
	.no-print, .no-print *
	{
		display: none !important;
	}
	
	 div.tr, div.td , div.th 
	 {
		page-break-inside: avoid;
	}
</style>
</head>
<body>
<center>
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
$categoryid=$obj_ledger_details->obj_utility->getParentOfLedger($data[0]['id']);
?>
<br><br>
<div class="panel panel-info" id="panel" style="display:none">
	<div class="panel-heading" id="pageheader">Voucher Reports </div>
    <br>
    <div style="padding-left: 15px;padding-bottom: 10px;">
    	<button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;" id="btnBack"><i class="fa  fa-arrow-left"></i></button>
		<center> 
        <br><br>
        <p>&nbsp;</p>  
        <div align="center"> 
			<table style="width:80%; border:1px solid black; background-color:transparent; ">
    			<tr> <td colspan="3"><br/> </td></tr>
    			<tr>
        			<td  style="width:20%"> &nbsp;Select Voucher Type : </td>            
        			<td style="width:28%;">
        				<select name="voucherTypeID" id="voucherTypeID" >	
               			<!--<option value="all"> All </option>-->	                    
             		 	<?php echo $Groups = $obj_ledger_details->combobox1("SELECT `id`,`desc` FROM `vouchertype` where id IN (".VOUCHER_PAYMENT.",".VOUCHER_RECEIPT.",".VOUCHER_JOURNAL.")", $_REQUEST['voucherTypeID']); ?>   						</select>
   					</td>
            		<td> From : </td>                      
					<td><input type="text" name="from_date" id="from_date"  class="basics" size="10" style="width:80px;" value = "<?php  echo getDisplayFormatDate($from)?>"/></td>
            		<td> To :</td>                     
					<td><input type="text" name="to_date" id="to_date"  class="basics" size="10" style="width:80px;" value="<?php echo getDisplayFormatDate($lastDay);?>"/></td>
            		<td></td><td></td>
               		<td><input type="button" name="submit" value="Submit" onClick ="GetReport()" class="btn btn-primary"/> </td>
     			</tr>        
        		<tr> <td colspan="3"><br/> </td></tr>
    		</table>
			<br>     
			<br>
			<br>
			<div id="export_btn" style="width:100%;display: none ">
				<input type="button" id="export" name="export" value="Export To Excel" onclick="Export_xls()" class="btn btn-primary"  />
                <input type="button" id="exportCSV" name="exportCSV" value="Export To CSV" onclick="Export_csv()" class="btn btn-primary" />
			</div>
            <br>
            <div style="max-height: 700px; overflow: scroll;overflow-x: hidden;">
				<div id='showTable' style="font-weight:lighter;"></div>
             </div>
		</div>
	</center>
</div>
</div>
<script src="js/jsCommon_20190326.js"></script>
</div>
<?php include_once "includes/foot.php"; ?>
<div id="openDialogOk" class="modalDialog" >
	<div style="margin:2% auto; ">
		<div id="message_ok">
		</div>
	</div>
</div>




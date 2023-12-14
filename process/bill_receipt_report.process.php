<?php	include_once("../classes/bill_receipt_report.class.php");
		include_once("../classes/include/dbop.class.php");
	 	$dbConn = new dbop();
		$obj_unit=new bill_receipt_report($dbConn);
		$wing_id = $_POST['wing_id'];
		$year_id = $_POST['year_id'];
		$period_id = $_POST['period_id'];
		$bill_type = $_POST['bill_method'];
		
		//$validator = $obj_unit->startProcess();
?>
<html>
<body onload="document.forms['voucher'].submit()">
<form name="Goback" method="POST" action="<?php echo $obj_unit->actionPage; ?>">

<input type="hidden" name="ShowData" value="<?php //echo $ShowData; ?>">
<input type="hidden" name="imp">

<input type="hidden" name="wing_id" id="wing_id" value="<?php echo $wing_id; ?>">
<input type="hidden" name="year_id" id="year_id" value="<?php echo $year_id;?>">
<input type="hidden" name="period_id" id="period_id" value="<?php echo $period_id;?>">
<input type="hidden" name="bill_method" id="bill_method" value="<?php echo $bill_type;?>">

</form>
<script>
	document.Goback.submit();
</script>
</body>
</html>

<!-- for reference -->
<?php

/*print_r($_REQUEST);
$arUnits = array();
foreach($_POST as $key => $value)
{
    if(substr($key, 0,8) == "checkbox")
    {
        //echo "val:".$value;
        array_push($arUnits, $value);
        //inserttag($value, $x);
    }
}
$PeriodID = $_REQUEST["period_id"];
$YearID = $_REQUEST["year_id"];
$BillNoteType = $_REQUEST["bill_voucher_type"];
$BillVoucherDate = $_REQUEST["voucher_date"];
$Action = $_REQUEST["view"];
if($Action == "Proceed")
{
	$sRequestURL = "../Voucher.php"; 
}
else
{
	$sRequestURL = "../genBillVoucher.php"; 	
}
print_r($arUnits);
echo "action:".$sRequestURL;
//die();
*/

?>
<!--<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body  onload="document.forms['voucher'].submit()">

<form method="POST" action="<?php //echo $sRequestURL; ?>" onload="" id="voucher">
	<input type="text" name="period_id" id="period_id" value="<?php //echo $PeriodID;?>">
	<input type="text" name="year_id" id="year_id" value="<?php //echo $YearID;?>">
	<input type="text" name="BillType" id="BillType" value="0">
	<input type="text" name="VoucherDate" id="VoucherDate" value="<?php //echo $BillVoucherDate;?>">
	<input type="text" name="Units" id="Units" value="<?php //echo implode($arUnits, ",")?>">
	<input type="text" name="BillNoteType" id="BillNoteType" value="<?php //echo $BillNoteType;?>">
	<input type="text" name="edt" id="edt" value="<?php //echo implode($arUnits, ",")?>">
</form>
</body>
</html>-->
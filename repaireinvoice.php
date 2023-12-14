<?php //include_once("includes/head_s.php");
include_once ("check_default.php");

include_once("classes/include/dbop.class.php");
include_once("classes/repaireGST_Invoice.class.php");
$dbConn = new dbop();
$obj_updateInvoice = new  updateInterest($dbConn)
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
<?php
if($_REQUEST['Rvoucher'] == 1)
{ 
	$RepaireVoucher = $obj_updateInvoice->RepairVouchers(1);
}
else
{
	$RepaireVoucher = $obj_updateInvoice->RepairVouchers(0);
}
?>
</body>
</html>
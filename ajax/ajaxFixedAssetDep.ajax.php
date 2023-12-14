
<?php 
include_once("../classes/include/dbop.class.php");
include_once("../classes/FA_Depreciation.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$obj_fa_dep = new fa_dep($dbConn);

//echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"] == "update_all")
{
	$update_all = $obj_fa_dep->update_all();
	
	echo json_encode($update_all);
}

if($_REQUEST["method"] == "fetch_opening_balance")
{
	$led_id = $_REQUEST["led_id"];
	$dep_type = $_REQUEST["dep_type"];
	$get_opening_bal = $obj_fa_dep->getOpeningBalance($led_id,$dep_type);
	
	echo json_encode($get_opening_bal);
}

if($_REQUEST["method"] == "create_jv")
{
	$to_ledger_id = $_REQUEST["to_ledger_id"];
	$by_ledger_id = $_REQUEST["by_ledger_id"];
	$dep_amt = $_REQUEST["dep_amt"];
	$opening_bal = $_REQUEST["opening_bal"];
	$closing_bal = $_REQUEST["closing_bal"];
	$dep_per = $_REQUEST["dep_per"];
	$purchase_date = $_REQUEST["purchase_date"];
	$purchase_amt = $_REQUEST["purchase_amt"];
	$depreciation_type = $_REQUEST["depreciation_type"];
	
	$create_voucher = $obj_fa_dep->createDepreciationVoucher($to_ledger_id,$by_ledger_id,$dep_amt,$opening_bal,$closing_bal,$dep_per,$purchase_date,$purchase_amt,$depreciation_type);
	
	echo json_encode($create_voucher);
}

if($_REQUEST["method"] == "show_jv_table")
{
	$led_id = $_REQUEST["led_id"];
	
	$create_table = $obj_fa_dep->create_table($led_id);
	ob_clean();
	echo json_encode($create_table);
}
if($_REQUEST["method"] == "deleteAssetVoucher")
{
	$VoucherNo = $_REQUEST["VoucherNo"];
	$RefNo = $_REQUEST["RefNo"];
	
	echo $deleteAssetVoucher = $obj_fa_dep->deleteAssetVoucher($VoucherNo,$RefNo);
}

?>
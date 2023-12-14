<?php 
include_once("../classes/dbconst.class.php");
include_once("../classes/include/dbop.class.php");
include_once('../classes/ChequeDetails.class.php');

$m_dbConn = new dbop();
$objChequeEntryDetails = new ChequeDetails($m_dbConn);


if($_REQUEST["method"]=="delete")
{
    //echo "inside function";
	$objChequeEntryDetails->deletingBatch($_REQUEST['Id']);
	return "Data Deleted Successfully";
}

?>
<?php 
include_once "../classes/include/dbop.class.php"; 
include_once("../classes/dbconst.class.php");
 include_once "../classes/utility.class.php";
$m_dbConn = new dbop();
?>
<?php
include_once("../classes/view_ledger_details.class.php");
$obj_ledger_details = new view_ledger_details($m_dbConn);
echo $_REQUEST["method"]."@@@";
	if($_REQUEST['method']=="Fetch")
	{
		$gId = $_REQUEST['gId'];
		$memId = $obj_ledger_details->getGroupMembers($gId);
		
		echo json_encode($memId);
	}
	
	if($_REQUEST['method']=="Fetchdata")
	{
		$gId = $_REQUEST['gId'];
		$memId = $obj_ledger_details->getGroupMembers1($gId);
			
		echo $memId;
	}
?>

<?php include_once("../classes/BankDetails.class.php");
include_once("../classes/import_bank_statement.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_BankDetails = new BankDetails($dbConn);
$obj_import_bank_statement = new Import_Bank_Statement();

if(isset($_REQUEST['getbalance']))
{
	$balance = $obj_BankDetails->getOpeningBalance($_REQUEST['ledger']);
}
else
{
	echo $_REQUEST["method"]."@@@";
	
	if($_REQUEST["method"]=="edit")
	{
		$select_type = $obj_BankDetails->selecting();
	
		foreach($select_type as $k => $v)
		{
			echo json_encode($v); 
			
			/*foreach($v as $kk => $vv)
			{
				echo $vv."#";
			}*/
		}
	}
	
	if($_REQUEST["method"]=="delete")
	{
		$obj_BankDetails->deleting();
		return "Data Deleted Successfully";
	}
	
	if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'ReconcileBankRegister')
	{
		$Flag = $_REQUEST['flag'];
		$BankID = $_REQUEST['bankid'];
		$Data = json_decode($_REQUEST['data'],true);
		/*if($Flag == AMOUNT_MATCH)
		{
			
		}*/
		$RadioSelection = json_decode($_REQUEST['RadioSelection'],true);
		
		$Result = $obj_import_bank_statement->ReconcilationProcess($Flag,$BankID,$Data,$RadioSelection);
		echo trim($Result);
		
	}
	if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'getReconcileDetails')
	{
		$Statement_id = $_REQUEST['act_bank_statement_id'];
		$Result = $obj_import_bank_statement->getReconcileDetails($Statement_id);
		echo json_encode($Result);
	}

}






?>
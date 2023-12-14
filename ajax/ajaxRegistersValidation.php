<?php 	include_once("../classes/RegistersValidation.class.php");
		include_once("../classes/include/dbop.class.php");
			
		$dbConn = new dbop();
		
		$obj_register = new RegistersValidation($dbConn);
		if(isset($_POST["method"]) && $_POST["method"] == "run" && $_POST["cleanInvalidEntries"] == "YES")
		{
			$obj_register->ValidateRegisterEntries('liabilityregister');
			$obj_register->ValidateRegisterEntries('assetregister');
			$obj_register->ValidateRegisterEntries('incomeregister');
			$obj_register->ValidateRegisterEntries('expenseregister');
			echo "success";
		}
		if(isset($_POST["method"]) && $_POST["method"] == "Delete" )
		{
			$response = 0;
			
			if($_POST["tableName"] == "bankregister")
			{
				$response = $obj_register->DeleteCorruptedBankRegister($_POST["tableId"],$_POST["tableName"]);
			}
			else
			{
				$response = $obj_register->DeleteCorruptedRegister($_POST["tableId"],$_POST["tableName"]);
			}
			echo "success";
		}
?>
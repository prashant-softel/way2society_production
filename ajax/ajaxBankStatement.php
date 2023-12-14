<?php 
include_once("../classes/bank_statement.class.php");
include_once("../classes/include/dbop.class.php");
$dbConn = new dbop();
$obj_bank_statement = new bank_statement($dbConn);

echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"] == "deleteMultipleEntries")
{
	$result = $obj_bank_statement->deleteMultipleEntries($_REQUEST);
	echo json_encode($result).'@@@';
}
else if($_REQUEST["method"] == "importBatchDelete")
{
	try {
		
		if(!empty($_REQUEST['batch_id'])){

			if($_REQUEST['voucherType'] == VOUCHER_RECEIPT){

				$selectQry = "SELECT ID FROM chequeentrydetails WHERE Import_Batch_Id = '".$_REQUEST['batch_id']."'";
				$data = $dbConn->select($selectQry);
				$chequeIDs = array_column($data, 'ID');
				if(!empty($chequeIDs) && count($chequeIDs) <> 0){
				
					$paymentIDs = $obj_bank_statement->deleteMultipleChequeEntryDetails($chequeIDs);
				
					if(!empty($paymentIDs)){
			
						$obj_bank_statement->deleteMultiplePaymentDetails($paymentIDs);
					}

					$updateBatchTable = "UPDATE import_batch set `Status` = 0 WHERE Id = '".$_REQUEST['batch_id']."'";
					$dbConn->update($updateBatchTable);

					echo json_encode(array('status'=>'success', 'msg'=>'Entries are deleted!!')).'@@@';
				}
			}
			else if($_REQUEST['voucherType'] == VOUCHER_PAYMENT){
				
				$selectQry = "SELECT ID FROM paymentdetails WHERE Import_Batch_Id = '".$_REQUEST['batch_id']."'";
				$data = $dbConn->select($selectQry);
				$paymentIDs = array_column($data, 'ID');
				if(!empty($paymentIDs) && count($paymentIDs) <> 0){
				
					$result = $obj_bank_statement->deleteMultiplePaymentDetails($paymentIDs);
				
					$updateBatchTable = "UPDATE import_batch set `Status` = 0 WHERE Id = '".$_REQUEST['batch_id']."'";
					$dbConn->update($updateBatchTable);

					echo json_encode(array('status'=>'success', 'msg'=>'Entries are deleted!!')).'@@@';
				}
			}

						
		}

	} catch (Exception $e) {
		
		echo json_encode(array('status'=>'failed', 'msg'=>'ERROR : '.$e->getMessage())).'@@@';
	}
}

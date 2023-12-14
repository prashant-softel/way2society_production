<?php
include_once("dbconst.class.php");
include_once("utility.class.php");
include_once("ChequeDetails.class.php");
include_once("PaymentDetails.class.php");
class bank_statement
{
	public $m_dbConn;
	public $obj_Utility;
	public $obj_chequeDetails;
	public $obj_paymentDetails;

	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->obj_Utility = new utility($dbConn);
		$this->obj_chequeDetails = new ChequeDetails($dbConn);
		$this->obj_paymentDetails = new PaymentDetails($dbConn);
	}
	
	function getBankDetails($ledgerID)
	{
		$bank_details_query = 'SELECT `BranchName`,`Address`,`AcNumber` FROM `bank_master` where `BankID` = '.$ledgerID;
		//echo $bank_details_query;		
		$bank_details = $this->m_dbConn->select($bank_details_query);
		return $bank_details;
	}
	
	function getDetails($ledgerID, $from, $to, $transType)
	{			
		//$detailsquery = 'SELECT banktable.PaidAmount,banktable.ReceivedAmount,chequw_detailstable.ChequeDate,chequw_detailstable.ChequeNumber FROM `bankregister` as `banktable` join `chequeentrydetails` as `chequw_detailstable` on banktable.ChkDetailID = chequw_detailstable.ID where banktable.LedgerID = '.$ledgerID;
		$detailsquery = "SELECT `PaidAmount`,`ReceivedAmount`,`ChkDetailID`, `VoucherID`,`VoucherTypeID`,`Is_Opening_Balance`,`Date`,`DepositGrp` ,`ReconcileStatus`,`Reconcile`,`Return` FROM `bankregister` where `LedgerID` = '".$ledgerID."'";	
		if($from <> "")
		{
			$detailsquery .= " AND `Date` >= '".getDBFormatDate($from)."'";  
		}
		if($to <> "")
		{
			$detailsquery .= " AND `Date` <= '".getDBFormatDate($to)."'";
		}
		
		if($from == "" && $to == "")
		{
			$detailsquery .= " AND `Date` BETWEEN  '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."' ";  
		}
		
		if($transType == 1)
		{
			$detailsquery .= " AND `PaidAmount` > 0";	
		}
		else if($transType == 2)
		{
			$detailsquery .= " AND `ReceivedAmount` > 0";
		}
		
		$detailsquery .= ' order by `Date`';						
		
		$result = $this->m_dbConn->select($detailsquery);		 
		return $result;
	}
	
	function getActualBankDetails($ledgerID, $from, $to, $transType)
	{
		$detailsquery = "SELECT * FROM `actualbankstmt` where `BankID` = '".$ledgerID."'";	
		if($from <> "")
		{
			$detailsquery .= " AND `Date` >= '".getDBFormatDate($from)."'";  
		}
		if($to <> "")
		{
			$detailsquery .= " AND `Date` <= '".getDBFormatDate($to)."'";
		}
		
		if($from == "" && $to == "")
		{
			$detailsquery .= " AND `Date` BETWEEN  '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."' ";  
		}
		
		if($transType == 1)
		{
			$detailsquery .= " AND `Debit` > 0";	
		}
		else if($transType == 2)
		{
			$detailsquery .= " AND `Credit` > 0";
		}
		
		//echo "<br>Query : ".$detailsquery;
		$detailsquery .= ' order by `Date`';						
		
		$result = $this->m_dbConn->select($detailsquery);		 
		return $result;
	}
	
	
	function getPaymentDetails($chkDetailID, $tableName)
	{		
		if($chkDetailID <> "")
		{	
			if($tableName == 'paymentdetails')
			{
				$chequeDetails_query = 'SELECT `ChequeDate`,`ChequeNumber`, `Comments`, `ChqLeafID`,`IsMultipleEntry`,`Reference` FROM ' . $tableName.' WHERE `id` = ' .$chkDetailID;	
			}
			else if($tableName == 'fd_master')
			{
				$chequeDetails_query = 'SELECT `deposit_date`,`fdr_no`, `note`, `fd_close`, `fd_renew` FROM ' . $tableName.' WHERE `id` = ' .$chkDetailID;
			}
			else
			{
				$chequeDetails_query = 'SELECT `ChequeDate`,`ChequeNumber`, `Comments` FROM ' . $tableName.' WHERE `id` = ' .$chkDetailID;
			}
			$res = $this->m_dbConn->select($chequeDetails_query);
			
			if($tableName == 'paymentdetails')
			{
				$customLeafQuery = "SELECT `CustomLeaf` FROM `chequeleafbook` WHERE `id` = ".$res[0]['ChqLeafID'];				
				$result = $this->m_dbConn->select($customLeafQuery);
				
				$res[0]['CustomLeaf'] = $result[0]['CustomLeaf'];
			}
			
		}		
		return $res;		
	}
	
	function getVoucherType($voucherTypeID)
	{
		$sql = 'SELECT `type` FROM `vouchertype` where `id` = ' .$voucherTypeID;
		$result = $this->m_dbConn->select($sql);
		return $result; 
	}
	
	function getVoucherDetails($voucherID, $perticular)
	{
		$sql = 'SELECT ledger_table.id,ledger_table.ledger_name FROM `voucher` as `vouchertable` join `ledger` as `ledger_table` on ledger_table.id = vouchertable.'.$perticular . ' and vouchertable.id = ' . $voucherID;			
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	function getBankName($ledgerID)
	{
		$sql = 'SELECT `ledger_name` FROM `ledger` where `id` = '.$ledgerID;
		$bankname = $this->m_dbConn->select($sql);
		return $bankname; 	
	}
	
	function getRefTableName($voucherID)
	{
		$sql = "SELECT `RefTableID`, `VoucherNo`, `RefNo`,`ExternalCounter` FROM `voucher` where `id`='".$voucherID."' ";
		$TableName = $this->m_dbConn->select($sql);
		return $TableName;
		//return $TableName[0]['RefTableID']; 	
	}
	
	function getLedgerDetails($chkDetailID, $tableName, $columnName, $voucherID)
	{
		if($chkDetailID == 0)
		{
			$sql = 'SELECT `Note` FROM `voucher` where `id` = '.$voucherID;
			$res = $this->m_dbConn->select($sql); 	
			$result[0]['ledger_name'] = $res[0]['Note'];
		}
		else
		{								
			$sql = "SELECT ledger_table.id,ledger_table.ledger_name FROM `" . $tableName ."` as `datatable` join `ledger` as `ledger_table` on ledger_table.id = datatable.".$columnName . " where datatable.id = '" . $chkDetailID . "'";
			
			if($tableName == 'chequeentrydetails')
			{
				$sql = "SELECT ledger_table.id,ledger_table.ledger_name FROM `" . $tableName ."` as `datatable` join `ledger` as `ledger_table` on ledger_table.id = datatable.".$columnName . " where datatable.ID = '" . $chkDetailID . "'";
			}		
			$result = $this->m_dbConn->select($sql);
			$arParentDetails = $this->obj_Utility->getParentOfLedger($result[0]['id']);
			if(!(empty($arParentDetails)))
			{			
				$categoryID = $arParentDetails['category'];
				$result[0]['categoryID'] = $categoryID;
				$result[0]['groupID'] = $arParentDetails['group'];
				
				if($categoryID == DUE_FROM_MEMBERS)
				{
					$sqlQuery = "SELECT `owner_name`,member_id FROM `member_main` WHERE `unit` = '".$result[0]['id']."'";
					$memberName = $this->m_dbConn->select($sqlQuery);
					if(sizeof($memberName) > 0)
					{
						$result[0]['ledger_name'] .= " - ".$memberName[0]['owner_name'];
						$result[0]['member_id']	 = $memberName[0]['member_id'];
					}
				}			
			}		
		}
		return $result;
	}
	
	//get bankID when depositID or chqleafID is passed 
	function getBankIDFromDID($ID, $table)
	{		
		if($table == 'paymentdetails')
		{
			$sql = "SELECT `BankID` FROM `chequeleafbook` WHERE `id` = ".$ID;			
			$res = $this->m_dbConn->select($sql);
			return $res[0]['BankID'];	
		}
		else
		{
			$sql = "SELECT `bankid` FROM `depositgroup` WHERE `id` = ".$ID;			
			$res = $this->m_dbConn->select($sql);
			return $res[0]['bankid'];	
		}
				
	}
	
	function getBalanceBeforeDate($ledgerID, $from)
	{
		$total = 0;
		if($from <> "")
		{
			$sql = "SELECT SUM(`PaidAmount`) 'TotalPaid', SUM(`ReceivedAmount`) 'TotalReceived' FROM `bankregister` WHERE `LedgerID` = '".$ledgerID."'";		
			$sql .= " AND `Date` < '".getDBFormatDate($from)."'";	
			$balanceBeforeDate = $this->m_dbConn->select($sql);
			$total = $balanceBeforeDate[0]['TotalReceived'] - $balanceBeforeDate[0]['TotalPaid'];
		}
		else
		{
			$openingBalance = $this->obj_Utility->getOpeningBalance(	$ledgerID, $_SESSION['default_year_start_date']);
			$total = $openingBalance['Credit']-$openingBalance['Debit'];
		}
		return $total;
	}
	
	function getTotalAmountForMultEntry($ref)
	{
		$sqlQuery = "SELECT `id`,`Amount` FROM `paymentdetails` WHERE `Reference` = '".$ref."'";
		$amount = $this->m_dbConn->select($sqlQuery);		
		return $amount;
	}

	function getStatementCount($statement_id)
	{
		$sql = "SELECT count(`id`) as statement_count FROM `bankregister` where `ReconcileStatus`=1 and `statement_id` = '".$statement_id."' ";
		$result = $this->m_dbConn->select($sql);
		return $result; 
	}
	
		public function deleteMultipleEntries($params)
	{

		try {

			extract($params);

			$this->m_dbConn->begin_transaction();

			if (count($chequeDetailIds) == 0 && count($paymentDetailIds) == 0) {
				
				throw new Exception("Please select checkboxes to delete cheque entries!!", 1);
			}

			if(!isset($chequeDetailIds)){

				$chequeDetailIds = array();
			}

			if(!isset($paymentDetailIds)){

				$paymentDetailIds = array();
			}
			

			// ******************* Delete ChequeEntryDetails starts ********************//

			if(count($chequeDetailIds) <> 0){
				$result = $this->deleteMultipleChequeEntryDetails($chequeDetailIds);

				foreach ($result as $paymentID) {

					array_push($paymentDetailIds, $paymentID);
				}
			}

			// **************** ChequeDetails deletion end here!!***************//

			// ******************* Delete PaymentDetails starts ********************//

			if (count($paymentDetailIds) <> 0) {

				$result = $this->deleteMultiplePaymentDetails($paymentDetailIds);
			}

			//$this->m_dbConn->commit();
			return array('status' => 'success', 'msg' => 'All the selected entries deleted!!');
		} catch (Exception $e) {

			$this->m_dbConn->rollback();
			return array('status' => 'failed', 'msg' => 'Sorry something went wrong!!' . $e->getMessage());
		}
	}


	public function deleteMultipleChequeEntryDetails($chequeDetailIds)
	{

		try {

			$paymentDetailIds = array();

				$this->obj_chequeDetails->actionType = DELETE;

				$chequeIds = implode(',', $chequeDetailIds);

				$getPreviousDetailsQuery = "SELECT * FROM chequeentrydetails WHERE ID IN($chequeIds)";

				$resultPreviouDetails = $this->m_dbConn->select($getPreviousDetailsQuery);

				if (count($resultPreviouDetails) == 0) {

					throw new Exception("Data is not avaiable to delete in system", 1);
				}

				foreach ($resultPreviouDetails as $detail) {

					extract($detail);

					// If cheque is return then need to delete payment entry as well

					$PaymentDetails = $this->obj_chequeDetails->deleteReturnChequeEntry($PaidBy, $ID); // If any cheque is return then delete from reverse credit & payment table.

					if(!empty($PaymentDetails)){

						array_push($paymentDetailIds, $PaymentDetails[0]['ChkDetailID']);
					}

					$result = $this->obj_chequeDetails->DeletePreviousRecord($PaidBy, $PayerBank, $PayerChequeBranch, $ID);
				}

			return $paymentDetailIds;
		} catch (Exception $e) {

			return array('status' => 'failed', 'msg' => 'Sorry something went wrong!!' . $e->getMessage());
		}
	}

	public function deleteMultiplePaymentDetails($paymentDetailIds)
	{

		try {


				$this->obj_paymentDetails->actionType = DELETE;

				$paymentIds = implode(',', $paymentDetailIds);

				$paymentDetails = $this->m_dbConn->select("select * from `paymentdetails` where id IN($paymentIds) ");

				if (count($paymentDetails) == 0) {

					throw new Exception("Data is not avaiable to delete in system", 1);
				}

				foreach ($paymentDetails as $detail) {
					
					extract($detail);

					$MultipleEntryData = array();

					if ($detail['Reference'] <> 0) {

						$MultipleEntryData = $this->m_dbConn->select("SELECT * FROM `paymentdetails` WHERE `Reference` = '" . $detail['Reference'] . "'");
					}
					if (sizeof($MultipleEntryData) > 0) {
						$prevRef = 0;
						for ($i = 0; $i < sizeof($MultipleEntryData); $i++) {
							
							$this->obj_paymentDetails->deletePaymentDetails(
								$MultipleEntryData[$i]['ChequeDate'],
								$MultipleEntryData[$i]['ChequeNumber'],
								$MultipleEntryData[$i]['VoucherDate'],
								$MultipleEntryData[$i]['Amount'],
								$MultipleEntryData[$i]['PaidTo'],
								$MultipleEntryData[$i]['ExpenseBy'],
								$MultipleEntryData[$i]['PayerBank'],
								$MultipleEntryData[$i]['ChqLeafID'],
								$MultipleEntryData[$i]['Comments'],
								$MultipleEntryData[$i]['InvoiceDate'],
								$MultipleEntryData[$i]['TDSAmount'],
								$MultipleEntryData[$i]["id"],
								false,
								$MultipleEntryData[$i]['Reference'],
								$prevRef
							);
							$prevRef = $MultipleEntryData[$i]['Reference'];
						}
					} else {

					$this->obj_paymentDetails->deletePaymentDetails($detail['ChequeDate'], $detail['ChequeNumber'], $detail['VoucherDate'], $detail['Amount'], $detail['PaidTo'], $detail['ExpenseBy'], $detail['PayerBank'], $detail['ChqLeafID'], $detail['Comments'], $detail['InvoiceDate'], $detail['TDSAmount'], $detail["id"]);
				}
			}
		} catch (Exception $e) {
			return array('status' => 'failed', 'msg' => 'Sorry something went wrong!!' . $e->getMessage());
		}
	}
}
?> 
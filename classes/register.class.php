<?php
	include_once "dbconst.class.php";
	
	class regiser
	{
		private $m_dbConn;
		private $ShowDebugTrace;
		function __construct($dbConn)
		{
			$this->m_dbConn = $dbConn;
			$this->ShowDebugTrace = 0;
		}
		
		
		public function UpdateRegister($ledgerID, $voucherID, $transactionType, $amount,$date="")
		{
			
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Inside UpdateRegister";
			}
			$aryParent = $this->getLedgerParent($ledgerID);
			$groupID = $aryParent['group'];
			$categoryID = $aryParent['category'];
			
			return $this->UpdateRegister_Ex($groupID, $categoryID, $ledgerID, $voucherID, $transactionType, $amount, $date);
		}
		
		public function UpdateRegister_Ex($groupID, $categoryID, $ledgerID, $voucherID, $transactionType, $amount,$date)
		{	
			//if there is already Debit amount and now setting Credit amount, Debit amount needs to be set to 0 and vice versa.
			$transactionType2 = TRANSACTION_CREDIT;
			if(strcmp($transactionType, TRANSACTION_CREDIT) == 0)
			{
				$transactionType2 = TRANSACTION_DEBIT;
			}
			else if(strcmp($transactionType, TRANSACTION_DEBIT) == 0)
			{
				$transactionType2 = TRANSACTION_CREDIT;
			}
			else
			{
				throw "Invalid transaction type " . $transactionType;
			}
			
//			$UpdateString = "SET `" . $transactionType . "` = '" . $amount . "',  `" . $transactionType2 . "` = '0'  where `VoucherID` = '" . $voucherID ."' and `LedgerID` ='" . $ledgerID . "' ";
			
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Inside UpdateRegister_Ex";
				echo "<BR>GroupID :". $groupID . " Category :" . $categoryID . " LedgerID : " . $ledgerID . " VoucherID :" . $voucherID . "   transactionType :" . $transactionType . "   Amount :" . $amount . "   date :" . $date . "<BR>";
			}
			
			$updateDate = "";
			if(!empty($date)){

				$updateDate = ", `Date` = '".getDBFormatDate($date)."'";
			}

			if($groupID == ASSET) 	
			{
				$sqlquery = "select * from assetregister where `VoucherID` = '" . $voucherID ."' and `LedgerID` ='" . $ledgerID . "'";
				$sqlResult1 = $this->m_dbConn->select($sqlquery);
				if($sqlResult1 != '')
				{
					 $sqlUpdate = "UPDATE `assetregister` SET `" . $transactionType . "` = '" . $amount . "', `" . $transactionType2 . "` = '0' $updateDate  where `VoucherID` = '" . $voucherID ."' and `LedgerID` ='" . $ledgerID . "' ";
					$sqlResult = $this->m_dbConn->update($sqlUpdate);
				}
				//If update fails then fresh insert in libalilityregister
				else
				{
		 			$sqlResult = $this->SetAssetRegister2($date, $groupID, $categoryID, $ledgerID, $voucherID, 0, $transactionType, $amount,1);
				}
			}
			else if($groupID == LIABILITY)
			{
				$sqlquery = "select * from liabilityregister where `VoucherID` = '" . $voucherID ."' and `LedgerID` ='" . $ledgerID . "'";
				$sqlResult1 = $this->m_dbConn->select($sqlquery);
				if($sqlResult1 != '')
				{
					$sqlUpdate = "UPDATE `liabilityregister` SET `" . $transactionType . "` = '" . $amount . "',  `" . $transactionType2 . "` = '0' $updateDate  where `VoucherID` = '" . $voucherID ."' and `LedgerID` ='" . $ledgerID . "' ";
					if($sqlUpdate <> "")
					{
						$sqlResult = $this->m_dbConn->update($sqlUpdate);
					}
				}
				else
				{
					$sqlResult = $this->SetLiabilityRegister2($date, $groupID, $categoryID, $ledgerID, $voucherID, 0, $transactionType, $amount,1);
		
				}
	
			}
			else if($groupID == EXPENSE)
			{
				$sqlquery = "select * from expenseregister where `VoucherID` = '" . $voucherID ."' and `LedgerID` ='" . $ledgerID . "'";
				$sqlResult3 = $this->m_dbConn->select($sqlquery);
				if($sqlResult3 != '')
				{
					$sqlUpdate = "UPDATE `expenseregister` SET `" . $transactionType . "` = '" . $amount ."',  `" . $transactionType2 . "` = '0' $updateDate  where `VoucherID`= '" . $voucherID ."' and `LedgerID` ='" . $ledgerID . "' ";
					if($sqlUpdate <> "")
					{
						$sqlResult = $this->m_dbConn->update($sqlUpdate);
					}
				}
				else
				{
					$sqlResult = $this->SetExpenseRegister($ledgerID, $date, $voucherID, 0, $transactionType, $amount, 0, 1);
				}
			}
			else if($groupID == INCOME)
			{
				$sqlquery = "select * from incomeregister where `VoucherID` = '" . $voucherID ."' and `LedgerID` ='" . $ledgerID . "'";
				$sqlResult4 = $this->m_dbConn->select($sqlquery);
				if($sqlResult4 != '')
				{
					$sqlUpdate = "UPDATE `incomeregister` SET `" . $transactionType . "` = '" . $amount ."' ,  `" . $transactionType2 . "` = '0' $updateDate where `VoucherID`= '" . $voucherID ."' and `LedgerID` ='" . $ledgerID . "' ";	
					if($sqlUpdate <> "")
					{
						$sqlResult = $this->m_dbConn->update($sqlUpdate);
					}
				}
				else
				{
					$sqlResult = $this->SetIncomeRegister($ledgerID, $date, $voucherID, 0, $transactionType, $amount, 1);
				}
			}
			else
			{
				echo "<BR>Invalid groupID : $groupID.<BR>";	
			}

			
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Update Query: " . $sqlUpdate . "<BR>";
				echo "Result: " . $sqlResult . "<BR>";
			}
			return $sqlResult;
		}

		public function SetRegister($date, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance)
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "Inside SetRegister";
			}
			$aryParent = $this->getLedgerParent($ledgerID);
			$groupID = $aryParent['group'];
			$categoryID = $aryParent['category'];
			if($this->ShowDebugTrace == 1)
			{
				print_r($aryParent );
				echo "<BR>GroupID :". $groupID . "  LedgerID : " . $ledgerID . " VoucherID :" . $voucherID . "   Amount :" . $amount . "<BR>";
			}
			if($groupID == ASSET) 	
			{
		 		$this->SetAssetRegister2($date, $groupID , $categoryID, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance);
			}
			else if($groupID == LIABILITY)
			{
		 		$this->SetLiabilityRegister2($date, $groupID , $categoryID, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance);
			}
			else if($groupID == EXPENSE)
			{
		 		$this->SetExpenseRegister($ledgerID, $date, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance);
			}
			else if($groupID == INCOME)
			{
		 		$this->SetIncomeRegister($ledgerID, $date, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance);
			}
			else
			{
				echo "<BR>Invalid $groupID<BR>";	
			}

		}
		public function SetIncomeRegister($ledgerID, $date, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance = 0)
		{
			if($this->ShowDebugTrace == 1)
			{	
				echo "<BR>In SetIncome. Ledger:". $ledgerID . " VoucherID :" . $voucherID . "   Amount :" . $amount . "<BR>";
			}
			
			
			$sqlInsert = "INSERT INTO `incomeregister`(`LedgerID`, `Date`, `VoucherID`, `VoucherTypeID`, `" . $transactionType . "`, Is_Opening_Balance) VALUES ('" . $ledgerID . "', '" . getDBFormatDate($date) . "', '" . $voucherID .  "', '" . $voucherTypeID . "', '" . $amount . "','".$isOpeningBalance."')";	
			$sqlResult = $this->m_dbConn->insert($sqlInsert);
			if($this->ShowDebugTrace == 1)
			{	
				echo "<BR>Result Inserted ID: " . $sqlResult . "<BR>";
			}
			return $sqlResult;
		}
		public function SetExpenseRegister($ledgerID, $date, $voucherID, $voucherTypeID, $transactionType, $amount, $ExpenseHead = 0, $isOpeningBalance = 0)
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>In SetExpense  LedgerID:". $ledgerID . " VoucherID :" . $voucherID . "   Amount :" . $amount . "<BR>";
			}
			
			$sqlInsert = "INSERT INTO `expenseregister`(`LedgerID`, `Date`, `VoucherID`, `VoucherTypeID`, `" . $transactionType . "`,`Is_Opening_Balance`,`ExpenseHead`) VALUES ('" . $ledgerID . "', '" . getDBFormatDate($date) . "', '" . $voucherID .  "', '" . $voucherTypeID . "', '" . $amount . "', '".$isOpeningBalance."','".$ExpenseHead."')";
			$sqlResult = $this->m_dbConn->insert($sqlInsert);
			
			if($this->ShowDebugTrace == 1)
			{	
				echo "<BR>Result Inserted ID: " . $sqlResult . "<BR>";
			}
			return $sqlResult;
		}
			
		public function SetAssetRegister($date, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance)
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>In SetAssetRegister Ledger:". $ledgerID . " VoucherID :" . $voucherID . "   Amount :" . $amount . "<BR>";
			}
			$aryParent = $this->getLedgerParent($ledgerID);
		
			$groupID = $aryParent['group'];
			$categoryID = $aryParent['category'];
		 	return $this->SetAssetRegister2($date, $groupID, $categoryID, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance);
		}

		public function SetAssetRegister2($date, $groupID, $categoryID, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance)
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Inside SetAssetRegister2<BR>";
			}
			$sqlInsert = "INSERT INTO `assetregister`(`Date`, `CategoryID`, `SubCategoryID`, `LedgerID`, `VoucherID`, `VoucherTypeID`, `" . $transactionType . "`, `Is_Opening_Balance`) VALUES ('" . getDBFormatDate($date) . "', '" . $groupID . "', '" . $categoryID . "', '" . $ledgerID . "', '" . $voucherID . "',  '" . $voucherTypeID . "', '" . $amount . "', '" . $isOpeningBalance . "')";
			$sqlResult = $this->m_dbConn->insert($sqlInsert);
			if($this->ShowDebugTrace == 1)
			{	
				echo "<BR>Result Inserted ID: " . $sqlResult . "<BR>";
			}
			
			return $sqlResult;
		}
		
		public function SetLiabilityRegister($date, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance)
		{
			if($this->ShowDebugTrace == 1)
			{
					echo "<BR>Liability:". $ledgerID . " VoucherID :" . $voucherID . "   Amount :" . $amount . "<BR>";
			}
			$aryParent = $this->getLedgerParent($ledgerID);
		
			$groupID = $aryParent['group'];
			$categoryID = $aryParent['category'];
			
		 	$this->SetLiabilityRegister2($date, $groupID, $categoryID, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance);
		}
		
		public function SetLiabilityRegister2($date, $groupID, $categoryID, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance)
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Inside SetLiabilityRegister2";
			}
			$sqlInsert = "INSERT INTO `liabilityregister`(`Date`, `CategoryID`, `SubCategoryID`, `LedgerID`, `VoucherID`, `VoucherTypeID`, `" . $transactionType . "`, `Is_Opening_Balance`) VALUES ('" . getDBFormatDate($date) . "', '" . $groupID . "', '" . $categoryID . "', '" . $ledgerID . "', '" . $voucherID . "',  '" . $voucherTypeID . "', '" . $amount . "', '" . $isOpeningBalance . "')";
		
			$sqlResult = $this->m_dbConn->insert($sqlInsert);
			
			if($this->ShowDebugTrace == 1)
			{	
				echo "<br>" . $sqlInsert ;
				echo "<BR>Result Inserted ID: " . $sqlResult . "<BR>";
			}			
			return $sqlResult;
		}
		
		
		public function SetBankRegister($date, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $depositGroup, $chequeDetailID, $isOpeningBalance = 0, $chequeDate = 0, $ref = 0, $reconcileDate = 0, $reconcileStatus = 0, $reconcile = 0, $return = 0)
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Bank Register:". $ledgerID . " VoucherID :" . $voucherID . "   Amount :" . $amount . "<BR>";
			}
			
			$sqlInsert = "INSERT INTO `bankregister`(`Date`, `LedgerID`, `VoucherID`, `VoucherTypeID`, `" . $transactionType . "`, `DepositGrp`, `ChkDetailID`, `Is_Opening_Balance`, `Cheque Date`, `Ref`, `Reconcile Date`, `ReconcileStatus`, `Reconcile`, `Return`) VALUES ('" . getDBFormatDate($date) . "', '" . $ledgerID . "', '" . $voucherID .  "', '" . $voucherTypeID . "', '" . $amount . "', '" . $depositGroup . "', '" . $chequeDetailID . "', '" . $isOpeningBalance . "', '".getDBFormatDate($chequeDate)."', '" . $ref . "', '".getDBFormatDate($reconcileDate)."', '".$reconcileStatus."', '".$reconcile."', '".$return."')";
			$sqlResult = $this->m_dbConn->insert($sqlInsert);
			
			return $sqlResult;
		}
	
	public function UpdateBankRegister($date, $ledgerID,$transactionType, $amount)
		{
			$this->ShowDebugTrace =1 ;
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Bank Register:". $ledgerID . " transactionType :" . $transactionType . "   Amount :" . $amount . "<BR>";
			}
			$transactionString = "";
			if($transactionType == TRANSACTION_RECEIVED_AMOUNT)
			{
				$transactionString = "PaidAmount = '0', ReceivedAmount = ".$amount;
			}
			else
			{
				$transactionString = "ReceivedAmount = '0', PaidAmount = ".$amount;
			}
			$sqlquery = "select * from bankregister where `VoucherID` = '" . $voucherID ."' and `LedgerID` ='" . $ledgerID . "'";
				$sqlResult1 = $this->m_dbConn->update($sqlquery);
				if($sqlResult1 > 0)
				{
			$sqlUpdate = "update `bankregister` set ".$transactionString." where `LedgerID` = '" . $ledgerID . "' and Is_Opening_Balance = '1'";
			$sqlResult = $this->m_dbConn->update($sqlUpdate);
				}
			else
			{
			$result = $this -> SetBankRegister($date, $ledgerID, 0, 0, $transactionType, $amount, 0, 0, 1);
			}
			
			return $sqlResult;
		}
	
	
		public function getLedgerParent($ledgerID)
		{
			//echo "<BR><BR>Inside getLedgerParent<BR><BR>";
			$sqlSelect = "select categorytbl.group_id, ledgertbl.categoryid from ledger As ledgertbl JOIN account_category As categorytbl ON ledgertbl.categoryid = categorytbl.category_id where ledgertbl.id = '" . $ledgerID . "'";
			$result = $this->m_dbConn->select($sqlSelect);
			
			$aryParent = array();
			$aryParent['group'] = $result[0]['group_id'];
			$aryParent['category'] = $result[0]['categoryid'];
			
			return $aryParent;
		}
	}
?>
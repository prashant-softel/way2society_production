<?php
include_once("voucher.class.php");
include_once("dbconst.class.php");
include_once("latestcount.class.php");
include_once("utility.class.php");
include_once("bank_statement.class.php");
include_once("register.class.php");
include_once("view_ledger_details.class.php");
include_once("ChequeDetails.class.php");
include_once("PaymentDetails.class.php");
include_once("changelog.class.php");

class bank_reconciliation
{	
	public $actionPage = "../bank_reconciliation.php";	
	public $m_dbConn;
	public $obj_view_bank_statement;
	public $prevCheque;
	public $obj_Utility;
	public $obj_ledger_details;
	public $obj_cheque_details;
	public $obj_payment_details;
	public $obj_log;
	public $obj_register;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;	
		$this->obj_view_bank_statement = new bank_statement($this->m_dbConn);	
		$this->prevCheque = "";
		$this->obj_Utility = new utility($this->m_dbConn);
		$this->obj_ledger_details = new view_ledger_details($this->m_dbConn);
		$this->obj_cheque_details = new ChequeDetails($this->m_dbConn);
		$this->obj_payment_details = new PaymentDetails($this->m_dbConn);
		$this->obj_log = new changeLog($this->m_dbConn);
		$this->obj_register = new regiser($this->m_dbConn);
	}
	
	function startProcess()
	{				
		if($_POST['userConfirmation']=="0")
		{
			$sql = "select `bank_penalty_amt` from `society` where society_id = ".$_SESSION['society_id'];
			$bankPenalty = $this->m_dbConn->select($sql);
			$changeLogDes = "Updating Bank Penalty Amount as bank_penalty_amt : ".$bankPenalty[0]['bank_penalty_amt'];
			$sqlChangedLog = "Insert into `change_log` (`ChangedLogDec`,`ChangedBy`,`ChangedTable`,`ChangedKey`) values ('".$changeLogDes."','".$_SESSION['login_id']."','society','".$_SESSION['society_id']."')";
			$result = $this->m_dbConn->insert($sqlChangedLog);
			if($result > 0)
			{
				$sqlSociety = "Update `society` set `bank_penalty_amt` = '".$_POST['bank_penalty']."' where `society_id` = '".$_SESSION['society_id']."'";
				$res = $this->m_dbConn->update($sqlSociety);
			}
		}
		try
		{
			$obj_voucher = new voucher($this->m_dbConn);
			$obj_LatestCount = new latestCount($this->m_dbConn);
			$obj_Utility = new utility($this->m_dbConn);
			$obj_register = new regiser($this->m_dbConn);
			//echo "reqclass:".$_REQUEST["ledgerID"];
			$details = $this->getDetails($_POST["ledgerID"], $_POST['dateType'], $_POST['voucher'], $_POST['status'], $_POST['From'],$_POST['To'], $_POST['chequeNo'], $_POST['ledger']);												
			$iSrNo = 1;					
			if(isset($_POST))
			{			
				if(PENALTY_TO_MEMBER == 0)
				{
					return "Please set default PENALTY_TO_MEMBER Ledger in Defaults";
				}
								
				$flag = 0;								
				for($i = 0; $i < sizeof($details); $i++)
				{														
					$this->m_dbConn->begin_transaction();																				
					$status = $this->getReconcileStatus($details[$i]['ID']);//$this->m_dbConn->select($selectQuery);				
					
					if($status[0]['ReconcileStatus'] != 1)
					{							
						if($_POST['return'.$i] > 0)
						{												
							if($_POST['bank_penalty'] == "")
							{
								return "Please enter Bank Penalty Amount";									
							}
							
							if($_POST['cancel_date'.$i] == 0)
							{
								//return "Please select clear/return date";	
								$flag = 1;
								continue;
							}

							// check if voucher/cheque date is modified. If yes then validate with reconcile date. voucher/cheque date must be less than reconcile date
						
							if(!empty($_POST['voucher_date_input'+$i]) && !empty($_POST['cheque_date_input'+$i])){

								if(strtotime($_POST['voucher_date_input'+$i]) > strtotime($_POST['cancel_date'.$i]) || strtotime($_POST['cheque_date_input'+$i]) > strtotime($_POST['cancel_date'.$i])){
									
									$flag = 1;
									continue;
								}
							}
							
							$yearDesc = $this->GetYearDesc($_SESSION['default_year']);
							$selectQuery = 'SELECT `id` FROM `chequeleafbook` WHERE `IsReturnChequeLeaf` = 1 AND `BankID` = "'.$_POST["ledgerID"].'" AND `LeafCreatedYearID` = "'.$_SESSION['default_year'].'"';
							
							$result = $this->m_dbConn->select($selectQuery);
							
							if($result == "")
							{								
								$insert_query="insert into chequeleafbook (`LeafName`,`BankID`,`Comment`,`CustomLeaf`,`IsReturnChequeLeaf`, `status`, `LeafCreatedYearID`) values ('ReverseChequeLeafBook-".$yearDesc."','".$_POST["ledgerID"]."','Leaf Created for bounce cheque entries : ".$yearDesc.".',1, 1, 'Y', '".$_SESSION['default_year']."')";
								$chqleafID = $this->m_dbConn->insert($insert_query);
							}
							else
							{
								$chqleafID = $result[0]['id'];	
							}

							$BillType = $this->getBillType($details[$i]['ChkDetailID']);
																					
							$comment = 'Reverse entry for Cheque #'.$details[$i]['ChequeNumber'].' from '.$details[$i]['ledger_name'].'.';
							$insert_query="insert into paymentdetails(`ChequeDate`, `ChequeNumber`, `Amount`, `PaidTo`, `EnteredBy`, `PayerBank`, `Comments`, `VoucherDate`, `ChqLeafID`, `ExpenseBy`) values ('".getDBFormatDate($_POST['cancel_date'.$i])."', '".$details[$i]['ChequeNumber']."', '".$_POST['bank_penalty']."', '".BANK_CHARGES."', '".$_SESSION['login_id']."', '".$_POST['ledgerID']."', '".$comment."','".getDBFormatDate($_POST['cancel_date'.$i])."','".$chqleafID."', '0')";												
							$data = $this->m_dbConn->insert($insert_query);
							
							$insert_query="insert into paymentdetails(`ChequeDate`, `ChequeNumber`, `Amount`, `PaidTo`, `EnteredBy`, `PayerBank`, `Comments`, `VoucherDate`, `ChqLeafID`, `ExpenseBy`, `Chktbl_ID`, `Bill_Type`) values ('".getDBFormatDate($_POST['cancel_date'.$i])."', '".$details[$i]['ChequeNumber']."', '".$details[$i]['ReceivedAmount']."', '".$details[$i]['id']."', '".$_SESSION['login_id']."', '".$_POST['ledgerID']."', '".$comment."','".getDBFormatDate($_POST['cancel_date'.$i])."','".$chqleafID."', '0', '".$details[$i]['ChkDetailID']."', '".$BillType."')";						
							$data1 = $this->m_dbConn->insert($insert_query);
											
							$note = 'Cheque #'.$details[$i]['ChequeNumber'].' from '.$details[$i]['ledger_name'].' is rejected. ['.$_POST['note'.$i].']';
							$amount = $details[$i]['ReceivedAmount'] + $_POST['bank_penalty'];	
							//$total_penalty = $_POST['bank_penalty'] + $_POST['society_penalty'];
							$iVoucherCounter = $obj_LatestCount->getLatestVoucherNo($_SESSION['society_id']);								
							$voucherID_I = $obj_voucher->SetVoucherDetails(getDBFormatDate($_POST['cancel_date'.$i]),$data, TABLE_PAYMENT_DETAILS, $iVoucherCounter,$iSrNo, VOUCHER_PAYMENT,$_POST['ledgerID'],TRANSACTION_DEBIT,$_POST['bank_penalty'], $note);																	
							$voucherID_II = $obj_voucher->SetVoucherDetails(getDBFormatDate($_POST['cancel_date'.$i]),$data, TABLE_PAYMENT_DETAILS, $iVoucherCounter,$iSrNo+1, VOUCHER_PAYMENT,BANK_CHARGES,TRANSACTION_CREDIT,$_POST['bank_penalty'], $note);
							
							//$society_amount = $_POST['amount'.$i] + $_POST['society_penalty'];
							$iVoucherCounter = $obj_LatestCount->getLatestVoucherNo($_SESSION['society_id']);
							$voucherID_III = $obj_voucher->SetVoucherDetails(getDBFormatDate($_POST['cancel_date'.$i]),$data1,TABLE_PAYMENT_DETAILS,$iVoucherCounter,$iSrNo,VOUCHER_PAYMENT,$_POST['ledgerID'],TRANSACTION_DEBIT,$details[$i]['ReceivedAmount'],$note);				
							$voucherID_IV = $obj_voucher->SetVoucherDetails(getDBFormatDate($_POST['cancel_date'.$i]),$data1,TABLE_PAYMENT_DETAILS, $iVoucherCounter,$iSrNo+1, VOUCHER_PAYMENT,$details[$i]['id'],TRANSACTION_CREDIT,$details[$i]['ReceivedAmount'], $note);						
		
							$obj_register->SetBankRegister($_POST['cancel_date'.$i],$_POST['ledgerID'],$voucherID_III,VOUCHER_PAYMENT, TRANSACTION_PAID_AMOUNT,$details[$i]['ReceivedAmount'],$chqleafID,$data1,0,$details[$i]['Cheque Date'], $details[$i]['ID']);						
							
							$obj_register->SetBankRegister($_POST['cancel_date'.$i],$_POST['ledgerID'],$voucherID_I,VOUCHER_PAYMENT,TRANSACTION_PAID_AMOUNT,$_POST['bank_penalty'],$chqleafID,$data,0,$details[$i]['Cheque Date'],$details[$i]['ID']);						
							
							//$obj_register->SetIncomeRegister(PENALTY_TO_MEMBER, $_POST['cancel_date'.$i], 0, "", TRANSACTION_CREDIT, $_POST['society_penalty']);
													
							$obj_register->SetExpenseRegister(BANK_CHARGES, $_POST['cancel_date'.$i], $voucherID_II, VOUCHER_PAYMENT, TRANSACTION_DEBIT, $_POST['bank_penalty'], 0); 																		
							
							$arParentDetails = $obj_Utility->getParentOfLedger($details[$i]['id']);
														
							if(!(empty($arParentDetails)))
							{
								$ExpenseByGroupID = $arParentDetails['group'];
								$PaidToCategoryID = $arParentDetails['category'];	
								
								if($ExpenseByGroupID==LIABILITY)
								{																						
									$obj_register->SetLiabilityRegister(getDBFormatDate($_POST['cancel_date'.$i]),$details[$i]['id'],$voucherID_IV,VOUCHER_PAYMENT, TRANSACTION_DEBIT, $details[$i]['ReceivedAmount'], 0);													
								}								
								else if($ExpenseByGroupID==ASSET)
								{
									if($PaidToCategoryID == BANK_ACCOUNT || $PaidToCategoryID == CASH_ACCOUNT)						
									{
											$obj_register->SetBankRegister(getDBFormatDate($_POST['cancel_date'.$i]), $details[$i]['id'], $voucherID_IV, VOUCHER_PAYMENT, TRANSACTION_RECEIVED_AMOUNT, $details[$i]['ReceivedAmount'], $chqleafID, $data1, 0, getDBFormatDate($details[$i]['Cheque Date']), $details[$i]['ID']);
									}
									else
									{
										$obj_register->SetAssetRegister(getDBFormatDate($_POST['cancel_date'.$i]), $details[$i]['id'], $voucherID_IV, VOUCHER_PAYMENT, TRANSACTION_DEBIT, $details[$i]['ReceivedAmount'], 0);				
									}
								}								
								else if($ExpenseByGroupID==INCOME)
								{													
									$obj_register->SetIncomeRegister($details[$i]['id'], getDBFormatDate($_POST['cancel_date'.$i]), $voucherID_IV, VOUCHER_PAYMENT, TRANSACTION_DEBIT, $details[$i]['ReceivedAmount']);
								}								
								else if($ExpenseByGroupID==EXPENSE)
								{																		 
									$obj_register->SetExpenseRegister($details[$i]['id'],getDBFormatDate($_POST['cancel_date'.$i]), $voucherID_IV,VOUCHER_PAYMENT, TRANSACTION_DEBIT,$details[$i]['ReceivedAmount'],0);												
								}																					
							}																												
							
							$reversal_credits = 'INSERT INTO `reversal_credits`(`Date`, `VoucherID`, `UnitID`, `Amount`, `LedgerID` , `ChargeType`) VALUES ("'.getDBFormatDate($_POST['cancel_date'.$i]).'",0,"'.$details[$i]['id'].'","'.$_POST['society_penalty'].'","'.PENALTY_TO_MEMBER.'", "'.FINE.'")	';
							$this->m_dbConn->insert($reversal_credits);	
							
							if($details[$i]['ledger_name'] != "Opening Balance")
							{						
							$update_chequeDetail = 'UPDATE `chequeentrydetails` SET `IsReturn`= 1 WHERE `ID`= '.$details[$i]['ChkDetailID']	;
							$this->m_dbConn->update($update_chequeDetail);									
							}
							$updateQuery = 'UPDATE `bankregister` SET `Reconcile Date` = "'.getDBFormatDate($_POST['cancel_date'.$i]).'", `ReconcileStatus`= 1, `Return` = 1 ,`statement_id`="'.$_POST['act_bank_statement_id'.$i].'" WHERE `id` = '.$details[$i]['ID'];								
							$this->m_dbConn->update($updateQuery);

							$UpdateBankStatement = "UPDATE `actualbankstmt` SET `Reco_Status` = 1, `Reco_By` = '".$_SESSION['login_id']."' WHERE Id = '".$_POST['act_bank_statement_id'.$i]."'";
					        $StatementResult = $this->m_dbConn->update($UpdateBankStatement);

						}
						elseif($_POST['reconcile'.$i] > 0)
						{
							$isMultEntry = false;
							if($_POST['cancel_date'.$i] == 0)
							{
								//return "Please select clear/return date";	
								$flag = 1;
								continue;
							}

							// check if voucher/cheque date is modified. If yes then validate with reconcile date. voucher/cheque date must be less than reconcile date
						
							if(!empty($_POST['voucher_date_input'+$i]) && !empty($_POST['cheque_date_input'+$i])){

								if(strtotime($_POST['voucher_date_input'+$i]) > strtotime($_POST['cancel_date'.$i]) || strtotime($_POST['cheque_date_input'+$i]) > strtotime($_POST['cancel_date'.$i])){
									
									$flag = 1;
									continue;
								}
							}
														
							$multEntries = $this->GetMultEntryArray($details[$i]['PaidAmount'],$details[$i]['ChkDetailID']);							
							if(sizeof($multEntries) > 0)
							{									
								for($cnt = 0; $cnt < sizeof($multEntries); $cnt++)
								{
									$updateQuery1 = 'UPDATE `bankregister` SET `Reconcile Date` = "'.getDBFormatDate($_POST['cancel_date'.$i]).'", `ReconcileStatus`= 1, `Reconcile` = 1,`statement_id`="'.$_POST['act_bank_statement_id'.$i].'" WHERE `ChkDetailID` = "'.$multEntries[$cnt]['id'].'" AND `PaidAmount` > 0';																				
						           $this->m_dbConn->update($updateQuery1);

						           $UpdateBankStatement = "UPDATE `actualbankstmt` SET `Reco_Status` = 1, `Reco_By` = '".$_SESSION['login_id']."' WHERE Id = '".$_POST['act_bank_statement_id'.$i]."'";
					               $StatementResult = $this->m_dbConn->update($UpdateBankStatement);
								}
							}
							else
							{																													
							$updateQuery = 'UPDATE `bankregister` SET `Reconcile Date` = "'.getDBFormatDate($_POST['cancel_date'.$i]).'", `ReconcileStatus`= 1, `Reconcile` = 1 ,`statement_id`="'.$_POST['act_bank_statement_id'.$i].'" WHERE `id` = '.$details[$i]['ID'];																	
						    $this->m_dbConn->update($updateQuery);	

						    $UpdateBankStatement = "UPDATE `actualbankstmt` SET `Reco_Status` = 1, `Reco_By` = '".$_SESSION['login_id']."' WHERE Id = '".$_POST['act_bank_statement_id'.$i]."'";
					        $StatementResult = $this->m_dbConn->update($UpdateBankStatement);
					        
							}
						}	
						// Check User want to change voucher Date

						if(!empty($_POST['voucher_date_input'.$i]) && !empty($_POST['cheque_date_input'.$i])){

							
							$newVoucherDate = $_POST['voucher_date_input'.$i];
							$newChequeDate = $_POST['cheque_date_input'.$i];
							
							// this function update the date
							$this->updateNewDate($newVoucherDate, $newChequeDate, $details[$i]['ChkDetailID'],$details[$i]['VoucherID'], $details[$i]['RefTableID']);

						}													
					}		
					else
					{
						//echo "undo : ".$_POST['undo'.$i];	
						if($_POST['undo'.$i] > 0)
						{
							$multEntries = $this->GetMultEntryArray($details[$i]['PaidAmount'],$details[$i]['ChkDetailID']);							
							if(sizeof($multEntries) > 0)
							{									
								for($cnt = 0; $cnt < sizeof($multEntries); $cnt++)
								{
									$undoQuery1 = 'UPDATE `bankregister` SET `Reconcile Date` = "0", `ReconcileStatus`= 0, `Reconcile` = 0, `Return` = 0, `statement_id` = 0 WHERE `ChkDetailID` = "'.$multEntries[$cnt]['id'].'" AND `PaidAmount` > 0';										
									$this->m_dbConn->update($undoQuery1);
								}
								if($details[$i]['statement_id'] <> '0' && !empty($details[$i]['statement_id']))
								{
									$undoBankStatementQuery = "UPDATE `actualbankstmt` set `Reco_Status` = 0, `Reco_By` = 0 WHERE Id = '".$details[$i]['statement_id']."'";
									$this->m_dbConn->update($undoBankStatementQuery);	
								}
							}
							else
							{
								$undoQuery = 'UPDATE `bankregister` SET `Reconcile Date` = "0", `ReconcileStatus`= 0, `Reconcile` = 0,`Return` = 0, `statement_id` = 0 WHERE `id` = '.$details[$i]['ID'];										
								$this->m_dbConn->update($undoQuery);
								
								if($details[$i]['VoucherTypeID'] == VOUCHER_RECEIPT) // Receipt Entry
								{
									$undoCheckDetailQry = "UPDATE chequeentrydetails SET IsReturn = 0 WHERE ID = '".$details[$i]['ChkDetailID']."'";
									$this->m_dbConn->update($undoCheckDetailQry);
								}
								
								if($details[$i]['statement_id'] <> '0' && !empty($details[$i]['statement_id']))
								{
									$undoBankStatementQuery = "UPDATE `actualbankstmt` set `Reco_Status` = 0, `Reco_By` = 0 WHERE Id = '".$details[$i]['statement_id']."'";
									$this->m_dbConn->update($undoBankStatementQuery);	
								}
							}
						}
					}
					$this->m_dbConn->commit();
				}  
				if($flag == 1)
				{
					return "Please select clear/return date";
				}
			}
		}
		catch(Exception $exp)
		{
			$this->m_dbConn->rollback();
			return $exp;
		}
	}
	
	public function updateVoucherAndRegisterDate($VoucherDate, $ChequeDate, $voucherNo){

		try {
			
			// Before we update dates in table we need basic details from voucher table and also we check whether data exits in voucher table or not
			$query = "SELECT id as voucherID, `By`, `To`, `Debit`, `Credit`, `RefTableID`, `Note`, `RefNo`, `ExternalCounter` FROM voucher WHERE VoucherNo = '$voucherNo'";
			$result = $this->m_dbConn->select($query);
			

			if(!empty($result)){ // If data exits we update voucher table

				$updateVoucherQry = "UPDATE voucher SET `Date` = '$VoucherDate' WHERE VoucherNo = '$voucherNo'";
				$updateVoucher = $this->m_dbConn->update($updateVoucherQry);

				if($result[0]['RefTableID'] == 0 || $result[0]['RefTableID'] == TABLE_FD_MASTER){ // we are not logging changes for these in its parent function because they don't have base table

					$dataArr = array();
					$dataArr['Date'] = getDisplayFormatDate($VoucherDate);
					$dataArr['Voucher No'] = $result[0]['ExternalCounter'];
					$total = 0;
				}
				

					foreach ($result as $key => $row) { // iterate through each entry in voucher table to update register table
						
						extract($row);
						$transactionType = '';
						if(!empty($To) && $To != 0 && empty($By)){ // set the data as per By and To 

						$transactionType = ($RefTableID == TABLE_CHEQUE_DETAILS || $RefTableID == TABLE_PAYMENT_DETAILS) ? TRANSACTION_DEBIT : TRANSACTION_CREDIT;
						$ledgerID = $To;
						$amount = $Credit;

							if($result[0]['RefTableID'] == 0 || $result[0]['RefTableID'] == TABLE_FD_MASTER){
								
								$LedgerName = $this->obj_Utility->getLedgerName($ledgerID);
								$dataArr['To Ledger'][$LedgerName] = number_format($amount, 2);
							}	
							
						}
						else if(empty($To) && !empty($By) && $By != 0){ // set the data as per By and To 
							
						$transactionType = ($RefTableID == TABLE_CHEQUE_DETAILS || $RefTableID == TABLE_PAYMENT_DETAILS) ? TRANSACTION_CREDIT : TRANSACTION_DEBIT;
							$ledgerID = $By;
							$amount = $Debit;

							if($result[0]['RefTableID'] == 0 || $result[0]['RefTableID'] == TABLE_FD_MASTER){

								$LedgerName = $this->obj_Utility->getLedgerName($ledgerID);
								$dataArr['By Ledger'][$LedgerName] = number_format($amount, 2);
								$total += $amount;
							}
						}
						
						$aryParent = $this->obj_register->getLedgerParent($ledgerID); // this function return the ledger group and its parent information

						$groupID = $aryParent['group'];
						$categoryID = $aryParent['category'];
						
						if($groupID == ASSET && ($categoryID == BANK_ACCOUNT || $categoryID == CASH_ACCOUNT)){ // If  ledger belongs to bank then update the bank register date

							$updateBankRegisterDateQry = "UPDATE bankregister SET `Date` = '".$VoucherDate."', `Cheque Date` = '".$ChequeDate."' WHERE VoucherID = '$voucherID'";
							$this->m_dbConn->update($updateBankRegisterDateQry);
						}
						else{

							// else this function check ledger parent details and update date as per ledger group
							$this->obj_register->UpdateRegister($ledgerID, $voucherID, $transactionType, $amount, $VoucherDate);
						}
					}

					if($result[0]['RefTableID'] == 0 || $result[0]['RefTableID'] == TABLE_FD_MASTER){ // preparing log msg for FD and JV

						$dataArr['Amount'] = $total;

						$is_invoice = false;
						$changeTableID = $result[0]['RefTableID'];
						$changelogKey = $result[0]['RefNo'];
						
						if($result[0]['RefTableID'] == 0){

							$changeTableID = TABLE_JOURNAL_VOUCHER;
							$changelogKey = $voucherNo;

							$selectQry = "SELECT InvoiceStatusID, NewInvoiceNo, CGST_Amount, SGST_Amount FROM invoicestatus WHERE InvoiceRaisedVoucherNo = '$voucherNo'";
							$invoiceResult = $this->m_dbConn->select($selectQry);
							if(!empty($invoiceResult)){

								$is_invoice = true;
								$NewInvoiceNo = $invoiceResult[0]['NewInvoiceNo'];
								$CGST_Amount = $invoiceResult[0]['CGST_Amount'];
								$SGST_Amount = $invoiceResult[0]['SGST_Amount'];
							}	
						}
				
						if($is_invoice){

							$dataArr['Invoice'] = 'YES';
							$dataArr['Invoice No.'] = $NewInvoiceNo;
							$dataArr['CGST Amount'] = $CGST_Amount;
							$dataArr['SGST Amount'] = $SGST_Amount;
						}
						else{

							$dataArr['Invoice'] = 'No';
							$dataArr['Invoice No.'] = '-';
							$dataArr['CGST Amount'] = 0;
							$dataArr['SGST Amount'] = 0;
						}
					}

					$dataArr['Note'] = $this->m_dbConn->escapeString($result[0]['Note']);

					$logArr = json_encode($dataArr);

					$previousLogID = 0;
					if(!empty($changelogKey) && $changelogKey != 0){

						$checkPreviousLogQry = "SELECT ChangeLogID FROM change_log WHERE ChangedKey = '$changelogKey' AND ChangedTable = '".$changeTableID."' ORDER BY ChangeLogID DESC LIMIT 1";
					
						$previousLogDetails = $this->m_dbConn->select($checkPreviousLogQry);

						$previousLogID = $previousLogDetails[0]['ChangeLogID'];
					}
					
					$this->obj_log->setLog($logArr, $_SESSION['login_id'], $changeTableID, $changelogKey, EDIT, $previousLogID);
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}

	}

	public function updateNewDate($VoucherDate, $ChequeDate, $rowID, $voucherID, $tableID){

		try {
			
			// converting date to db format 
			$VoucherDate = getDBFormatDate($VoucherDate);
			$ChequeDate = getDBFormatDate($ChequeDate);

			if($tableID == TABLE_CHEQUE_DETAILS && !empty($rowID) && $rowID != 0){ // if its receipt then we have to update cheque entry table as well

				// first we will check whether data exits or not
				$getChequeDetail = "SELECT c.*, v.VoucherNo FROM chequeentrydetails as c JOIN voucher as v ON c.ID = v.RefNo WHERE c.ID = '$rowID' and v.id = '$voucherID'";
				$ChequeDetail = $this->m_dbConn->select($getChequeDetail);


				if(!empty($ChequeDetail)){ // if data exits then update the respective tables

					// update the cheque entry table
					$updateChequeEntry = "UPDATE chequeentrydetails SET VoucherDate = '$VoucherDate', ChequeDate = '$ChequeDate' WHERE ID = $rowID";
					$updateCheque = $this->m_dbConn->update($updateChequeEntry);
						
					$voucherNo = $ChequeDetail[0]['VoucherNo'];
					
					// once base table updated then update voucher and register table as well
					$this->updateVoucherAndRegisterDate($VoucherDate, $ChequeDate, $voucherNo);
				}


				// Once we updated the dates. we have to keep trace those changes for that we log changes
				// change log start here
				$PaidByName = $this->obj_Utility->getLedgerName($ChequeDetail[0]['PaidBy']);
				$BankName = $this->obj_Utility->getLedgerName($ChequeDetail[0]['BankID']);
				$BillTypeName = $this->obj_Utility->returnBillTypeString($ChequeDetail[0]['BillType']);
				$DepositName = $this->obj_Utility->getDepositName($ChequeDetail[0]['DepositID']);
				
				$dataArr = array('Voucher Date'=> $VoucherDate, 'Cheque Date'=>$ChequeDate, 'Cheque Number'=>$ChequeDetail[0]['ChequeNumber'], 'Amount'=> $ChequeDetail[0]['Amount'], 'TDS Amount'=> $ChequeDetail[0]['TDS_Amount'], 'Paid By'=>$PaidByName, 'Bank'=> $BankName, 'Payer Bank'=>$ChequeDetail[0]['PayerBank'], 'Payer Cheque Branch'=>$ChequeDetail[0]['PayerChequeBranch'], 'Deposit Name'=>$DepositName, 'Comments'=>$ChequeDetail[0]['Comments'], 'Bill Type'=>$BillTypeName);

				$checkPreviousLogQry = "SELECT ChangeLogID FROM change_log WHERE ChangedKey = '$rowID' AND ChangedTable = '".TABLE_CHEQUE_DETAILS."'";
				
				$previousLogDetails = $this->m_dbConn->select($checkPreviousLogQry);

				$previousLogID = $previousLogDetails[0]['ChangeLogID'];

				$previousLogDesc = json_encode($dataArr);

				$this->obj_log->setLog($previousLogDesc, $_SESSION['login_id'], TABLE_CHEQUE_DETAILS, $rowID, EDIT, $previousLogID);

				//change log end
			}
			else if($tableID == TABLE_PAYMENT_DETAILS && !empty($rowID) && $rowID != 0){// if its payment then we have to update payment entry table as well

				// check payment data exits or not
				$getPaymentDetail = "SELECT p.*, v.VoucherNo FROM paymentdetails as p JOIN voucher as v ON p.id = v.RefNo WHERE p.id = '$rowID' and v.id = '$voucherID'";
				$PaymentDetail = $this->m_dbConn->select($getPaymentDetail);

				// if data exits then update the its base/root table which is paymentdetails table
				if(!empty($PaymentDetail)){

					// updating dates in table
					$updatePaymentEntry = "UPDATE paymentdetails SET VoucherDate = '$VoucherDate', ChequeDate = '$ChequeDate' WHERE id = $rowID";
					$updatePayment = $this->m_dbConn->update($updatePaymentEntry);
					
					$voucherNo = $PaymentDetail[0]['VoucherNo'];
					
					// now update the date in voucher and register table as well
					$this->updateVoucherAndRegisterDate($VoucherDate, $ChequeDate, $voucherNo);
				}
				
				// Once we updated the dates. we have to keep trace those changes for that we log changes
				// change log start here
				$PaidToName = $this->obj_Utility->getLedgerName($PaymentDetail[0]['PaidTo']);
				$BankName = $this->obj_Utility->getLedgerName($PaymentDetail[0]['PayerBank']);

				$LeafName = $this->obj_Utility->getLeftName($PaymentDetail[0]['ChqLeafID']);

				$dataArr = array('Cheque Date'=>$ChequeDate, 'Cheque Number'=>$PaymentDetail[0]['ChequeNumber'], 'Amount'=> $PaymentDetail[0]['Amount'], 'Paid To'=>$PaidToName, 'Bank'=> $BankName, 'Invoice Date'=>getDisplayFormatDate($PaymentDetail[0]['InvoiceDate']), 'cheque Leaf'=>$LeafName, 'Comments'=>$PaymentDetail[0]['Comments']);
				$logArr = json_encode($dataArr);

				$previousLogID = 0;
				$requestStatus = ADD;

				if(!empty($rowID)){

					$requestStatus = EDIT;
					$checkPreviousLogQry = "SELECT ChangeLogID FROM change_log WHERE ChangedKey = '$rowID' AND ChangedTable = '".TABLE_PAYMENT_DETAILS."'";
				
					$previousLogDetails = $this->m_dbConn->select($checkPreviousLogQry);

					$previousLogID = $previousLogDetails[0]['ChangeLogID'];

				}

				$this->obj_log->setLog($logArr, $_SESSION['login_id'], TABLE_PAYMENT_DETAILS, $rowID, $requestStatus, $previousLogID);
				//change log end
			}
			else if($tableID == TABLE_FD_MASTER || ($tableID == 0)){ // 0 represent the journal voucher 

				// In Bank register table there was 2 more voucher type present. FD and JV so we have to manage those also
				// For these voucher type we are considering any root/base table
				// check data exits or not
				$getVoucherDetail = "SELECT VoucherNo FROM voucher where id = '$voucherID'";
				$voucherDetail = $this->m_dbConn->select($getVoucherDetail);

				if(!empty($voucherDetail)){ // if data exits then update voucher and register table

					$voucherNo = $voucherDetail[0]['VoucherNo'];
					$this->updateVoucherAndRegisterDate($VoucherDate, $ChequeDate, $voucherNo);
				}
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}


	function getBillType($chequeID){

		$sql = "SELECT BillType FROM chequeentrydetails WHERE ID = '".$chequeID."'";
		$result = $this->m_dbConn->select($sql);
		return $result[0]['BillType'];
	}

	function getExtendedRecoEndDate($sEndDate, $Offset )
	{
		//$Offset = "60";
		$newDate =  $this->obj_Utility->GetDateByOffset_dmy($sEndDate,  $Offset );
		return $newDate;
	}

	function getChqBounceCharge($ledgerID)
	{
		$sql = 'SELECT `chq_bounce_charge` FROM `society` WHERE `society_id` = (SELECT `society_id` FROM `ledger` WHERE `id` = '.$ledgerID.')';
		$res = $this->m_dbConn->select($sql);
		return $res[0]["chq_bounce_charge"];	
	}
	
	function getLedgers($DepositGrp, $ChkDetailID, $voucherID, $ledger)
	{								
		$ledgerName = array();		
		if($DepositGrp == -1)
		{				
			$ledgerQuery = 'SELECT ledgertable.id,payment.id as "TableID",ledgertable.ledger_name, payment.ChequeNumber FROM `ledger` AS `ledgertable` JOIN `paymentdetails` AS `payment` ON ledgertable.id = payment.PaidTo WHERE payment.PaidTo = ( SELECT `PaidTo` FROM `paymentdetails` WHERE `ID` = '.$ChkDetailID.')'; 
		}
		else
		{				
			$ledgerQuery = 'SELECT ledgertable.id,chequedetails.ID as "TableID",ledgertable.ledger_name, chequedetails.ChequeNumber FROM `ledger` as `ledgertable` JOIN `chequeentrydetails` as `chequedetails` on ledgertable.id = chequedetails.PaidBy where chequedetails.PaidBy = (SELECT `PaidBy` from `chequeentrydetails` where `ID` = '.$ChkDetailID.')'; 
		}	
		if($ledger <> "")
		{
			$ledgerQuery .= ' AND ledgertable.ledger_name = "'.$ledger.'"' ;	
		}		
		$result = $this->m_dbConn->select($ledgerQuery);
		
		if($ledger == "")
		{	
			if($ChkDetailID == 0)
			{
				$sql = 'SELECT `Note` FROM `voucher` where `id` = '.$voucherID;
				$res = $this->m_dbConn->select($sql); 	
				$result[0]['ledger_name'] = $res[0]['Note'];
			}
		}
		return $result;
	}
	
	public function combobox($query, $id, $defaultString = '', $defaultValue = '')
	{
		if($defaultString <> '')
		{		
			$str.="<option value='" . $defaultValue . "'>" . $defaultString . "</option>";
		}
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($id==$v)
						{
							$sel = 'selected';	
						}
						else
						{
							$sel = '';
						}
						
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
					}
					else
					{
						$str.=$v."</OPTION>";
					}
					$i++;
				}
			}
		}
			return $str;
	}

	function getPaymentDetails($chkDetailID, $tableName, $ChequeNo)
	{						
		if($chkDetailID <> "")
		{		
			$chequeDetails_query = 'SELECT `ChequeNumber`, `Comments` FROM ' . $tableName.' WHERE `id` = ' .$chkDetailID;	
			if($ChequeNo <> "")
			{
				$chequeDetails_query .= ' AND `ChequeNumber` LIKE "'.$ChequeNo.'%"';
			}			
			$res = $this->m_dbConn->select($chequeDetails_query);
			
			if($ChequeNo == "")
			{	
				if($chkDetailID == 0)
				{					
					$res[0]['ChequeNumber'] = '-';
					$res[0]['Comments'] = '-';
				}
			}
		}		
		return $res;		
	}
	
	function getDetails($ledgerID, $dateType, $voucherType, $status, $from, $to, $chequeNo, $ledgerName)
	{					
		$displayDetails = array();
		//echo "details:".$ledgerID;		
		$detailsquery = "SELECT br.`id`,br.`Date`,br.`PaidAmount`,br.`ReceivedAmount`,br.`ChkDetailID`, br.`VoucherID`,br.`VoucherTypeID`,br.`Is_Opening_Balance`,br.`DepositGrp`,br.`Reconcile Date`, br.`Cheque Date`, br.`statement_id`, s.`bank_penalty_amt`, v.RefTableID, v.ExternalCounter FROM `society` as s,`bankregister` as br JOIN voucher as v ON v.id = br.VoucherID where `LedgerID` = '".$ledgerID."' AND s.`society_id` = '".$_SESSION['society_id']."'"; 
		if($dateType == 0)
		{			
			if($from <> "")
			{			
				$detailsquery .= ' AND br.`Date` >= "'.getDBFormatDate($from).'"';				
			}
			if($to <> "")
			{
				$detailsquery .= ' AND br.`Date` <= "'.getDBFormatDate($to).'"';	
			}
			
			if($from == "" && $to == "")
			{
				$detailsquery .= " AND br.`Date` BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";  
			}
		}
		else if($dateType == 2)
		{			
			$detailsquery .= ' AND br.`Reconcile Date` != 0';
			if($from <> "")
			{			
				$detailsquery .= ' AND br.`Reconcile Date` >= "'.getDBFormatDate($from).'"';				
			}
			if($to <> "")
			{
				$detailsquery .= ' AND br.`Reconcile Date` <= "'.getDBFormatDate($to).'"';	
			}
			if($from == "" && $to == "")
			{
				$detailsquery .= " AND br.`Reconcile Date` BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."' ";  
			}
		}
		else if($dateType == 1)
		{			
			$detailsquery .= ' AND br.`Cheque Date` != 0';
			if($from <> "")
			{			
				$detailsquery .= ' AND br.`Cheque Date` >= "'.getDBFormatDate($from).'"';				
			}
			if($to <> "")
			{
				$detailsquery .= ' AND br.`Cheque Date` <= "'.getDBFormatDate($to).'"';	
			}
			if($from == "" && $to == "")
			{
				$detailsquery .= " AND br.`Cheque Date` BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."' ";  
			}
		}
		if($voucherType <> "")
		{
			$detailsquery .= ' AND br.`VoucherTypeID` =  "'.$voucherType.'"';	
		}
		if($status <> "")
		{
			if($status == 1)
			{
				$detailsquery .= ' AND br.`Reconcile` = 1';	
			}
			elseif($status == 2)
			{
				$detailsquery .= ' AND br.`Reconcile` = 0';
			}
			elseif($status == 3)
			{
				$detailsquery .= ' AND br.`Return` = 1';
			}
		}
		else
		{
			$detailsquery .= ' AND br.`Reconcile` = 0';
		}		
		
		if($dateType == 0)
		{
			$detailsquery .= ' ORDER BY br.`Date` ';
		}
		else if($dateType == 1)
		{
			$detailsquery .= ' ORDER BY br.`Cheque Date` ';
		}
		else if($dateType == 2)
		{
			$detailsquery .= ' ORDER BY br.`Reconcile Date` ';
		}	
		$detailsquery .= ',br.`id`';	
	
		//echo "<br>Query : ".$detailsquery;
		$result = $this->m_dbConn->select($detailsquery);				
					
		$paymentdtl_chqno = array();
		$paymentdtl_comments = array();
		$paymentdtl_ledgers = array();
		$paymentdtl_ledgerID = array();
		$paymentdtl_IsMultEntry = array();
		$paymentdtl_Ref = array();
		$sql = 'SELECT payment.id as paymentID, payment.ChequeNumber, payment.Comments, ledger.id, ledger.ledger_name, payment.IsMultipleEntry, payment.Reference FROM `paymentdetails` as `payment` JOIN `ledger` on ledger.id = payment.PaidTo'; // WHERE payment.PayerBank = '.$ledgerID;		 
		if($chequeNo <> "")
		{ 
			$sql .= ' AND ( payment.ChequeNumber LIKE "'.$chequeNo.'%" OR payment.Amount LIKE "'.$chequeNo.'%" )';
		}
		if($ledgerName <> "")
		{
			$sql .= ' AND ledger.ledger_name = "'.$ledgerName.'"';
		}							
		$res = $this->m_dbConn->select($sql);
		
		for($i = 0; $i < sizeof($res); $i++)
		{
			$paymentdtl_chqno[$res[$i]['paymentID']] = $res[$i]['ChequeNumber'];
			$paymentdtl_comments[$res[$i]['paymentID']] = $res[$i]['Comments'];
			$paymentdtl_ledgers[$res[$i]['paymentID']] = $res[$i]['ledger_name'];
			$paymentdtl_ledgerID[$res[$i]['paymentID']] = $res[$i]['id'];
			$paymentdtl_IsMultEntry[$res[$i]['paymentID']] = $res[$i]['IsMultipleEntry'];
			$paymentdtl_Ref[$res[$i]['paymentID']] = $res[$i]['Reference'];
		}		
		
		$chequeentrydtl_chqno = array();
		$chequeentrydtl_comments = array();
		$chequeentrydtl_ledgers = array();
		$chequeentrydtl_ledgerID = array();
		$sql = 'SELECT chqentrydtls.ID as chqentrydtlID, chqentrydtls.ChequeNumber, chqentrydtls.Comments, ledger.id, ledger.ledger_name FROM `chequeentrydetails` as `chqentrydtls` JOIN `ledger` ON ledger.id = chqentrydtls.PaidBy'; //WHERE chqentrydtls.BankID = '.$ledgerID;					
		if($chequeNo <> "")
		{			
			$sql .= ' AND ( chqentrydtls.ChequeNumber LIKE "'.$chequeNo.'%" OR chqentrydtls.Amount LIKE "'.$chequeNo.'%" )';
		}
		if($ledgerName <> "")
		{
			$sql .= ' AND ledger.ledger_name = "'.$ledgerName.'"';
		}		
		$res1 = $this->m_dbConn->select($sql);
			//echo "test 4";	
		for($i = 0; $i < sizeof($res1); $i++)
		{
			$chequeentrydtl_chqno[$res1[$i]['chqentrydtlID']] = $res1[$i]['ChequeNumber'];
			$chequeentrydtl_comments[$res1[$i]['chqentrydtlID']] = $res1[$i]['Comments'];
			$chequeentrydtl_ledgers[$res1[$i]['chqentrydtlID']] = $res1[$i]['ledger_name'];
			$chequeentrydtl_ledgerID[$res1[$i]['chqentrydtlID']] = $res1[$i]['id'];
		}				
		for($i = 0; $i < sizeof($result); $i++)
		{			
			$chequeDetails = array();
			$ledgers = array(); 	
			$tableQuery = "SELECT * FROM `voucher` WHERE `id` = '".$result[$i]['VoucherID']."'";	
			$res = $this->m_dbConn->select($tableQuery); 			
			if($result[$i]['ChkDetailID'] == 0)
			{					
				$chequeDetails[0]['ChequeNumber'] = '-';
				$chequeDetails[0]['Comments'] = '-';
				//$sql = 'SELECT `Note` FROM `voucher` where `id` = '.$result[$i]['VoucherID'];
				//$res = $this->m_dbConn->select($sql); 	
				$ledgers[0]['ledger_name'] = $res[0]['Note'];
			}							
			//else if($result[$i]['PaidAmount'] > 0)
			else if($res[0]['RefTableID'] == 3)
			{																		
				$chequeDetails[0]['ChequeNumber'] = $paymentdtl_chqno[$result[$i]['ChkDetailID']];			
				$chequeDetails[0]['Comments'] = $paymentdtl_comments[$result[$i]['ChkDetailID']];
				if(	$result[$i]['VoucherTypeID'] == 6)
				{
					$sqlLedger = "SELECT ledger_table.id,ledger_table.ledger_name FROM `paymentdetails` as `datatable` join `ledger` as `ledger_table` on ledger_table.id = datatable.PayerBank where datatable.id = ". $result[$i]['ChkDetailID'];
					$ledger = $this->m_dbConn->select($sqlLedger);
					$ledgers[0]['ledger_name'] = $ledger[0]['ledger_name'];
					$ledgers[0]['id'] = $ledger[0]['id'];
				}
				else
				{
					$ledgers[0]['ledger_name'] = $paymentdtl_ledgers[$result[$i]['ChkDetailID']];
					$ledgers[0]['id'] = $paymentdtl_ledgerID[$result[$i]['ChkDetailID']];
				}
				$chequeDetails[0]['IsMultEntry'] = $paymentdtl_IsMultEntry[$result[$i]['ChkDetailID']];	
				$chequeDetails[0]['Ref'] = $paymentdtl_Ref[	$result[$i]['ChkDetailID']];		
			}
			else if($res[0]['RefTableID'] == 2)
			{									
				$chequeDetails[0]['ChequeNumber'] = $chequeentrydtl_chqno[$result[$i]['ChkDetailID']];			
				$chequeDetails[0]['Comments'] = $chequeentrydtl_comments[$result[$i]['ChkDetailID']];				
				$ledgers[0]['ledger_name'] = $chequeentrydtl_ledgers[$result[$i]['ChkDetailID']];
				$ledgers[0]['id'] = $chequeentrydtl_ledgerID[$result[$i]['ChkDetailID']];
				$chequeDetails[0]['IsMultEntry'] = 0;
				$chequeDetails[0]['Ref'] = 0;
			}							 
			if($result[$i]['Is_Opening_Balance'] == 1 && ($ledgerName == "" || $ledgerName == "Opening Balance") && ($chequeNo == "" || $chequeDetails[0]['ChequeNumber'] == "-"))	
			{								
				if($chequeNo <> "")
				{										
					if(strpos( $result[$i]['ReceivedAmount'], $chequeNo) === 0)
					{						 
						$ledgers[0]['ledger_name'] = "Opening Balance";
					}																					
				}
				else
				{
					$ledgers[0]['ledger_name'] = "Opening Balance";
				}
			}
			
			if($ledgers[0]['ledger_name'] <> "" && $chequeDetails[0]['ChequeNumber'] <> "") //	
			{				
				if($this->prevCheque > 0 && $this->prevCheque == $chequeDetails[0]['Ref'])
				{					
					continue;
				}				
				
				$LedgerDetails = $this->obj_Utility->getParentOfLedger($ledgers[0]['id']);
				$LedgerUrl = "view_ledger_details.php?lid=".$ledgers[0]['id']."&gid=".$LedgerDetails['group']."";

				$AmountUrl = $this->obj_ledger_details->generatUrl($result[$i]['VoucherID'],$result[$i]['VoucherTypeID']);
				
				$details = array();																																									
				$details['ID'] = $result[$i]['id'];
				$details['Date'] = $result[$i]['Date'];
				$details['PaidAmount'] = $result[$i]['PaidAmount'];
				$details['ReceivedAmount'] = $result[$i]['ReceivedAmount'];
				$details['ChkDetailID'] =  $result[$i]['ChkDetailID'];
				$details['statement_id'] = $result[$i]['statement_id'];
				$details['VoucherID'] =  $result[$i]['VoucherID'];
				$details['VoucherTypeID'] =  $result[$i]['VoucherTypeID'];
				$details['Is_Opening_Balance'] =  $result[$i]['Is_Opening_Balance'];
				$details['ReconcileDate'] = $result[$i]['Reconcile Date'];
				$details['ChequeNumber'] = $chequeDetails[0]['ChequeNumber'];
				$details['Comment'] = $chequeDetails[0]['Comments'];
				$details['Cheque Date'] = $result[$i]['Cheque Date'];								
				$details['ledger_name'] = $ledgers[0]['ledger_name'];
				$details['DepositGrp'] = $result[$i]['DepositGrp'];
				$details['id'] = $ledgers[0]['id'];
				$details['bank_penalty_amt'] = $result[0]['bank_penalty_amt'];
				$details['LedgerUrl'] = $LedgerUrl;
				$details['AmountUrl'] = $AmountUrl;
				$details['RefTableID'] = $result[$i]['RefTableID'];
				
				if($chequeDetails[0]['IsMultEntry'] == 1)
				{																				
					$this->prevCheque = $chequeDetails[0]['Ref'];
					$sqlQuery = "SELECT `id`,`Amount` FROM `paymentdetails` WHERE `Reference` = '".$chequeDetails[0]['Ref']."'";
					$amount = $this->m_dbConn->select($sqlQuery);
					$total = 0;
					$ledgerN = '';	
					for($k = 0; $k < sizeof($amount); $k++)
					{
						$total += $amount[$k]['Amount'];					
						$ledgerN .= $paymentdtl_ledgers[$amount[$k]['id']] . "<br />";	
						$ledgerIDForMult .= $paymentdtl_ledgerID[$amount[$k]['id']] . "<br />";					
					}
					$details['ledger_name'] = $ledgerN;
					$details['PaidAmount'] = $total;
				}				
				array_push($displayDetails, $details);																										
			}						
		}
		$sql23 = 'SELECT `id` as ID,`Date`,`PaidAmount`,`ReceivedAmount`,`ChkDetailID`, `VoucherID`,`VoucherTypeID`,`Is_Opening_Balance`,`DepositGrp`,`Reconcile Date`, `Cheque Date` FROM `bankregister` where `LedgerID` = "'.$ledgerID.'"  AND `Is_Opening_Balance` = 1 ';														
		$result23 = $this->m_dbConn->select($sql23);
		
		//converting array of array to single array		
		
		if($result23 <> "")
		{
			$flatten = array();
			array_walk_recursive($result23, function($value,$key) use(&$flatten) {
        		if($key == 'Date')
				{
					$value = $_SESSION['default_year_start_date'];			
				}
				$flatten[$key] = $value;
   		 	});
			
			//append opening balance array to start of $result array
			if(count($displayDetails) == 0)
			{
				array_push($displayDetails, $flatten);
			}
			else
			{
				array_unshift($displayDetails ,$flatten);
			}
		}	
		return $displayDetails;
	}
	
	function getReconcileStatus($bankRegID)
	{				
		$selectQuery = "SELECT `Reconcile`,`ReconcileStatus` FROM `bankregister` WHERE `id` = '".$bankRegID."' ";		
		$status = $this->m_dbConn->select($selectQuery);		
		return $status;
	}
	
	public function GetYearDesc($YearID)
	{
		$SqlVal = $this->m_dbConn->select("SELECT `YearDescription` FROM `year` where `YearID`=". $YearID);
		return $SqlVal[0]['YearDescription'];
	}
	
	public function GetMultEntryArray($PaidAmount, $ChkDetailID)
	{
		$multEntries = array();
		if($PaidAmount > 0)
		{
			$sql = 'SELECT * FROM `paymentdetails` WHERE `id` = "'.$ChkDetailID.'"';
			$res = $this->m_dbConn->select($sql);
			
			if($res[0]['IsMultipleEntry'] == 1)
			{				
				$sql1 = 'SELECT * FROM `paymentdetails` WHERE `ChequeNumber` = "'.$res[0]['ChequeNumber'].'"';	
				$multEntries = $this->m_dbConn->select($sql1);
			}								
		}
		return $multEntries;	
	}
	
	function getMemberName($ledgerID)
	{
		$legerSql="SELECT `ledger_name`,`categoryid`,`id` FROM `ledger`where id='".$ledgerID."'";
		$category = $this->m_dbConn->select($legerSql);
		if($category <> '')
		{
			for($i=0;$i<sizeof($category);$i++)
			{
				if($category[$i]['categoryid']==DUE_FROM_MEMBERS)
				{
					$selectQuery="SELECT mm.`owner_name`,mm.`unit`,u.unit_id,u.unit_no,l.id,l.ledger_name FROM `member_main` as mm join unit as u on u.unit_id=mm.unit join ledger as l on l.id=u.unit_id where l.id='".$category[$i]['id']."' and mm.`ownership_status`='1'";
					$memberName = $this->m_dbConn->select($selectQuery);
				}
			}
		}
		return $memberName;
		
	}
}
?> 
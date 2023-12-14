<?php if(!isset($_SESSION)){ session_start(); }

	include_once("classes/include/dbop.class.php");
	include_once("classes/dbconst.class.php");

	//Select the payment entries
	
	$startDBNo = 0;
	$endDBNo = 120;

	$iConvertedCount = 0;
	for($iDBCnt = $startDBNo; $iDBCnt <= $endDBNo; $iDBCnt++)
	{
		$dbname = 'hostmjbt_society' . $iDBCnt;

		echo '<br/>DBName : ' . $dbname;

		$dbConn = new dbop(false, $dbname);
		$dbConnRoot = new dbop(true);

		$sqlSelectPayment = "Select * from `paymentdetails` where `ExpenseBy` > 0";// and `VoucherDate` < '2016-04-01'";// and id in (29,30,31,32)";

		$resultSelectPayment = $dbConn->select($sqlSelectPayment);

		echo '<br>No Of Entries for conversion : ' . sizeof($resultSelectPayment);
		
		if($resultSelectPayment <> '')
		{
			//Pending :  select the tds ledger id from the defaults
			$sqlSelectTDSLedger = "Select `APP_DEFAULT_TDS_PAYABLE` from `appdefault`";
			$resultSelectTDSLedger = $dbConn->select($sqlSelectTDSLedger);

			$tds_ledger = $resultSelectTDSLedger[0]['APP_DEFAULT_TDS_PAYABLE'];
			echo '<br/>TDS Ledger ID : ' . $tds_ledger;

			for($iPaymentCnt = 0; $iPaymentCnt < sizeof($resultSelectPayment); $iPaymentCnt++)
			{
				echo '<br/>Converting ChequeNumber : ' . $resultSelectPayment[$iPaymentCnt]['ChequeNumber'];

				$payment_id = $resultSelectPayment[$iPaymentCnt]['id'];
				$paid_to = $resultSelectPayment[$iPaymentCnt]['PaidTo'];
				$invoice_date =  $resultSelectPayment[$iPaymentCnt]['InvoiceDate'];
				$invoice_amount = $resultSelectPayment[$iPaymentCnt]['InvoiceAmount'];
				$tds_amount = $resultSelectPayment[$iPaymentCnt]['TDSAmount'];
				$invoice_voucher_id = $resultSelectPayment[$iPaymentCnt]['VoucherID'];
				$is_multiple_entry = $resultSelectPayment[$iPaymentCnt]['IsMultipleEntry'];
				$ref_no = $resultSelectPayment[$iPaymentCnt]['Reference'];

				echo '<pre> Cheque Details : ';
				print_r($resultSelectPayment[$iPaymentCnt]);
				echo '</pre>';

				$bDeletePaymentEntry = false;

				if($ref_no > 0 && $is_multiple_entry == 0)
				{
					$bDeletePaymentEntry = true;
					//Get the payment voucher no
					$sqlSelectPaymentVoucherNo = "Select `VoucherNo` from `voucher` where `RefNo` = '" . $ref_no . "' and `RefTableID` = '" . TABLE_PAYMENT_DETAILS . "'";
				}
				else
				{
					$sqlSelectPaymentVoucherNo = "Select `VoucherNo` from `voucher` where `RefNo` = '" . $payment_id . "' and `RefTableID` = '" . TABLE_PAYMENT_DETAILS . "'";
				}

				$resultSelectPaymentVoucherNo = $dbConn->select($sqlSelectPaymentVoucherNo);

				$payment_voucher_no = $resultSelectPaymentVoucherNo[0]['VoucherNo'];

				//Get the invoice voucher details
				$sqlSelectInvoiceVoucherDetails = "Select * from `voucher` where `VoucherNo` = (Select `VoucherNo` from `voucher` where `id` = '" . $invoice_voucher_id . "')";

				$resultSelectInvoiceVoucherDetails = $dbConn->select($sqlSelectInvoiceVoucherDetails);

				echo '<pre> Existing Invoice Details : ';
				print_r($resultSelectInvoiceVoucherDetails);
				echo '</pre>';

				if($resultSelectInvoiceVoucherDetails <> '')
				{
					echo '<br>Conversion Started .... ';

					//die();

					$tds_voucher_no = 0;
					$voucher_no = 0;
					$voucher_debit_amount = 0;

					for($iInvoiceRowCnt = 0; $iInvoiceRowCnt < sizeof($resultSelectInvoiceVoucherDetails); $iInvoiceRowCnt++)
					{
						$voucher_id = $resultSelectInvoiceVoucherDetails[$iInvoiceRowCnt]['id'];
						$voucher_date = $resultSelectInvoiceVoucherDetails[$iInvoiceRowCnt]['Date'];
						$voucher_no = $resultSelectInvoiceVoucherDetails[$iInvoiceRowCnt]['VoucherNo'];
						$ledger_by = $resultSelectInvoiceVoucherDetails[$iInvoiceRowCnt]['By'];
						$ledger_to = $resultSelectInvoiceVoucherDetails[$iInvoiceRowCnt]['To'];
						$amount_debit = $resultSelectInvoiceVoucherDetails[$iInvoiceRowCnt]['Debit'];
						$amount_credit = $resultSelectInvoiceVoucherDetails[$iInvoiceRowCnt]['Credit'];

						if($ledger_by <> '')
						{
							$voucher_debit_amount = $amount_debit;
							continue;
						}
						else if($ledger_to <> '')
						{
							$sqlSelectLedgerGroup = "SELECT c.group_id, l.categoryid, l.id FROM `ledger` as l JOIN account_category as c on l.categoryid = c.category_id where l.id = '" . $ledger_to . "'";

							$resultSelectLedgerGroup = $dbConn->select($sqlSelectLedgerGroup);

							//Paid To entry
							if($ledger_to == $paid_to)
							{
								if($voucher_id <> '' && $voucher_id > 0)
								{
									//Update Credit Entry in voucher
									$sqlUpdateCreditAmountOfPaidTo = "Update `voucher` SET `Credit` = '" . $voucher_debit_amount . " ' WHERE id = '" . $voucher_id . "'";

									$resultUpdateCreditAmountOfPaidTo = $dbConn->update($sqlUpdateCreditAmountOfPaidTo);

									if($resultSelectLedgerGroup[0]['group_id'] == LIABILITY)
									{
										//Update credit liability entry in register
										$sqlUpdateCreditAmountInRegister = "Update `liabilityregister` SET `Credit` = '" . $voucher_debit_amount . " ' WHERE VoucherID = '" . $voucher_id . "'";

										$resultUpdateCreditAmountInRegister = $dbConn->update($sqlUpdateCreditAmountInRegister);
									}
									else if($resultSelectLedgerGroup[0]['group_id'] == ASSET)
									{
										//Update credit asset entry in register
										 $sqlUpdateCreditAmountInRegister = "Update `assetregister` SET `Credit` = '" . $voucher_debit_amount . " ' WHERE VoucherID = '" . $voucher_id . "'";

										$resultUpdateCreditAmountInRegister = $dbConn->update($sqlUpdateCreditAmountInRegister);
									}
									else if($resultSelectLedgerGroup[0]['group_id'] == INCOME)
									{
										//Update credit income entry in register
										$sqlUpdateCreditAmountInRegister = "Update `incomeregister` SET `Credit` = '" . $voucher_debit_amount . " ' WHERE VoucherID = '" . $voucher_id . "'";

										$resultUpdateCreditAmountInRegister = $dbConn->update($sqlUpdateCreditAmountInRegister);
									}
									else if($resultSelectLedgerGroup[0]['group_id'] == EXPENSE)
									{	
										//Update credit expense entry in register
										$sqlUpdateCreditAmountInRegister = "Update `expenseregister` SET `Credit` = '" . $voucher_debit_amount . " ' WHERE VoucherID = '" . $voucher_id . "'";

										$resultUpdateCreditAmountInRegister = $dbConn->update($sqlUpdateCreditAmountInRegister);
									}
								}
							}
							else if($ledger_to == $tds_ledger)
							{
								//delete the voucher row and TDS entry in register
								$sqlDeleteTDSInRegister = "Delete from `liabilityregister` WHERE `VoucherID` = '" . $voucher_id . "'";

								$resultDeleteTDSInRegister = $dbConn->delete($sqlDeleteTDSInRegister);

								$sqlDeleteTDSInRegister = "Delete from `voucher` WHERE `id` = '" . $voucher_id . "'";

								$resultDeleteTDSInRegister = $dbConn->delete($sqlDeleteTDSInRegister);

								echo '<br/>Latest Voucher No : ' . $iLatestVoucherNo = getLatestVoucherNo($dbConn);

								$tds_voucher_no = $iLatestVoucherNo;

								//pass a new tds voucher and register entry
								$sqlInsertTDSVoucherBy = "INSERT INTO `voucher`(`Date`, `RefNo`, `RefTableID`, `VoucherNo`, `SrNo`, `VoucherTypeID`, `By`, `Debit`,`Note`) VALUES ('" . $invoice_date . "', '0', '0', '" .  $iLatestVoucherNo .  "', '1', '" . VOUCHER_JOURNAL . "' , '" . $paid_to .  "', '" . $tds_amount . "', '" . $dbConn->escapeString($resultSelectInvoiceVoucherDetails[0]['Note']) ."')";

								$resultInsertTDSVoucherBy = $dbConn->insert($sqlInsertTDSVoucherBy);

								 $sqlSelectLedgerGroup = "SELECT c.group_id, l.categoryid, l.id FROM `ledger` as l JOIN account_category as c on l.categoryid = c.category_id where l.id = '" . $paid_to . "'";

								$resultSelectLedgerGroup = $dbConn->select($sqlSelectLedgerGroup);

								$table_name = 'liabilityregister';

								if($resultSelectLedgerGroup[0]['group_id'] == ASSET)
								{
									$table_name = 'assetregister';								
								}
								else if($resultSelectLedgerGroup[0]['group_id'] == INCOME)
								{
									$table_name = 'incomeregister';								
								}
								else if($resultSelectLedgerGroup[0]['group_id'] == EXPENSE)
								{
									$table_name = 'expenseregister';								
								}

								echo '<br/>Table Name For TDS By : ' . $table_name;

								if($resultSelectLedgerGroup[0]['group_id'] == EXPENSE || $resultSelectLedgerGroup[0]['group_id'] == INCOME)
								{
									echo '<br/>' . $sqlInsertTDSVoucherBy = "INSERT INTO `" . $table_name . "` (`Date`, `LedgerID`, `VoucherID`, `VoucherTypeID`, `Debit`) VALUES ('" . $invoice_date . "', '" . $paid_to . "', '" . $resultInsertTDSVoucherBy . "',  '" . VOUCHER_JOURNAL . "', '" . $tds_amount . "')";
								}
								else
								{
									echo '<br/>' . $sqlInsertTDSVoucherBy = "INSERT INTO `" . $table_name . "` (`Date`, `CategoryID`, `SubCategoryID`, `LedgerID`, `VoucherID`, `VoucherTypeID`, `Debit`) VALUES ('" . $invoice_date . "', '" . $resultSelectLedgerGroup[0]['group_id'] . "', '" . $resultSelectLedgerGroup[0]['categoryid'] . "', '" . $paid_to . "', '" . $resultInsertTDSVoucherBy . "',  '" . VOUCHER_JOURNAL . "', '" . $tds_amount . "')";
								}

								$resultInsertTDSVoucherBy = $dbConn->insert($sqlInsertTDSVoucherBy);

								echo '<br/>' . $sqlInsertTDSVoucherTo = "INSERT INTO `voucher`(`Date`, `RefNo`, `RefTableID`, `VoucherNo`, `SrNo`, `VoucherTypeID`, `To`, `Credit`, `Note`) VALUES ('" . $invoice_date . "', '0', '0', '" .  $iLatestVoucherNo .  "', '2', '" . VOUCHER_JOURNAL . "' , '" . $tds_ledger .  "', '" . $tds_amount . "', '" . $dbConn->escapeString($resultSelectInvoiceVoucherDetails[0]['Note']) ."')";

								$resultInsertTDSVoucherTo = $dbConn->insert($sqlInsertTDSVoucherTo);

								$sqlSelectLedgerGroup = "SELECT c.group_id, l.categoryid, l.id FROM `ledger` as l JOIN account_category as c on l.categoryid = c.category_id where l.id = '" . $tds_ledger . "'";

								$resultSelectLedgerGroup = $dbConn->select($sqlSelectLedgerGroup);

								echo '<br/>' . $sqlInsertTDSVoucherTo = "INSERT INTO `liabilityregister` (`Date`, `CategoryID`, `SubCategoryID`, `LedgerID`, `VoucherID`, `VoucherTypeID`, `Credit`) VALUES ('" . $invoice_date . "', '" . $resultSelectLedgerGroup[0]['group_id'] . "', '" . $resultSelectLedgerGroup[0]['categoryid'] . "', '" . $tds_ledger . "', '" . $resultInsertTDSVoucherTo . "',  '" . VOUCHER_JOURNAL . "', '" . $tds_amount . "')";

								$resultInsertTDSVoucherTo = $dbConn->insert($sqlInsertTDSVoucherTo);

							}
						}
	 				}

	 				//Create entry in IncomeStatus table
	 				$sqlInsertInvoiceStatus = "INSERT INTO `invoicestatus`(`NewInvoiceNo`, `InvoiceChequeAmount`, `InvoiceRaisedVoucherNo`, `InvoiceClearedVoucherNo`, `TDSVoucherNo`, `AmountReceivable`, `AmountReceived`, `TDSAmount`, `is_invoice`) VALUES ('0', '" . $invoice_amount . "', '" . $voucher_no . "', '" . $payment_voucher_no . "', '" . $tds_voucher_no . "', '" . $invoice_amount . "', '" . $invoice_amount . "', '" . $tds_amount . "', 1)";

	 				$resultInsertInvoiceStatus = $dbConn->insert($sqlInsertInvoiceStatus);

	 				if($bDeletePaymentEntry == true)
	 				{
	 					echo '<br>Deleting Payment entry for id : ' . $payment_id;

	 					//Delete payment entry
	 					$sqlDeletePaymentEntry = "DELETE from `paymentdetails` WHERE id = '" . $payment_id . "'";
	 					$resultDeletePaymentEntry = $dbConn->delete($sqlDeletePaymentEntry);
	 				}
	 				else
	 				{
	 					//Update Payment entry
	 					$sqlUpdatePaymentEntry = "UPDATE `paymentdetails` SET `ExpenseBy` = '0', `InvoiceDate` = '0000-00-00', `InvoiceAmount` = '0', `TDSAmount` = '0', `VoucherID` = '0', `VoucherTypeID` = '0', `IsMultipleEntry` = '0', `Reference` = '0' WHERE id = '" . $payment_id . "'";
	 					$resultUpdatePaymentEntry = $dbConn->update($sqlUpdatePaymentEntry);
	 				}

	 				$iConvertedCount++;
	 				echo '<br>Conversion Complete .... ';
				}
			}
		}

		echo '<br/>Entries Converted : ' . $iConvertedCount;
	}

	function getResult($mMysqli, $sqlQuery)
	{
		$result = $mMysqli->query($sqlQuery);						
		if($result)
		{
			$count = 0;
			while($row = $result->fetch_array(MYSQL_ASSOC))
			{
				$data[$count] = $row;
				$count++;
			}											
		}	
		return $data;	
	}

	function getLatestVoucherNo($dbConn)
	{
		$sqlSelect = "Select voucher_no from counter";
		
		$sqlResult = $dbConn->select($sqlSelect);
		
		$sqlCounter = $sqlResult[0]['voucher_no'];
		
		$sqlUpdate = "UPDATE `counter` SET `voucher_no`='"  . ($sqlCounter + 1) . "'";

		$sqlResult = $dbConn->update($sqlUpdate);
		
		return $sqlCounter;
	} 

?>
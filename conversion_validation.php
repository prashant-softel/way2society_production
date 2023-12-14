<?php if(!isset($_SESSION)){ session_start(); }

	include_once("classes/include/dbop.class.php");
	include_once("classes/dbconst.class.php");

	//Select the payment entries
	
	$startDBNo = 7;
	$endDBNo = 7;

	$iConvertedCount = 0;
	for($iDBCnt = $startDBNo; $iDBCnt <= $endDBNo; $iDBCnt++)
	{
		$dbname = 'hostmjbt_society' . $iDBCnt;

		echo '<br/>DBName : ' . $dbname;

		$dbConn = new dbop(false, $dbname);
		$dbConnRoot = new dbop(true);

		$sqlSelectPayment = "Select * from `paymentdetails_org` where `ExpenseBy` > 0 and `VoucherID` > 0";// and `VoucherDate` < '2016-04-01'";// and id in (29,30,31,32)";

		$resultSelectPayment = $dbConn->select($sqlSelectPayment);

		echo '<br>No Of Entries for conversion : ' . sizeof($resultSelectPayment);
		
		if($resultSelectPayment <> '')
		{
			$sqlSelectTDSLedger = "Select `APP_DEFAULT_TDS_PAYABLE` from `appdefault`";
			$resultSelectTDSLedger = $dbConn->select($sqlSelectTDSLedger);

			$tds_ledger = $resultSelectTDSLedger[0]['APP_DEFAULT_TDS_PAYABLE'];
			echo '<br/>TDS Ledger ID : ' . $tds_ledger;

			for($iPaymentCnt = 0; $iPaymentCnt < sizeof($resultSelectPayment); $iPaymentCnt++)
			{
				echo '<br/><br/>Validating ChequeNumber : ' . $resultSelectPayment[$iPaymentCnt]['ChequeNumber'];

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

				if($is_multiple_entry == 1)
				{
					echo '<br/>First Entry of Multiple Entry Cheque';
				}
				else if($ref_no > 0 && $is_multiple_entry == 0)
				{
					echo '<br/>Multiple Entry Cheque';	
				}

				$bDeletePaymentEntry = false;

				//Check if voucher exist
				$sqlSelectPaymentVoucherNo = "Select `VoucherNo` from `voucher` where `RefNo` = '" . $payment_id . "' and `RefTableID` = '" . TABLE_PAYMENT_DETAILS . "'";

				if($ref_no > 0 && $is_multiple_entry == 0)
				{
					$sqlSelectPaymentVoucherNo = "Select `VoucherNo` from `voucher` where `RefNo` = '" . $ref_no . "' and `RefTableID` = '" . TABLE_PAYMENT_DETAILS . "'";					
				}
				
				$resultSelectPaymentVoucherNo = $dbConn->select($sqlSelectPaymentVoucherNo);

				echo '<br/>Payment Voucher Existence Check : ';

				if($resultSelectPaymentVoucherNo <> '')
				{
					echo 'Success';

					$payment_voucher_no = $resultSelectPaymentVoucherNo[0]['VoucherNo'];

					//Check invoice voucher exist
					$sqlSelectInvoiceVoucherDetails = "Select * from `voucher` where `VoucherNo` = (Select `VoucherNo` from `voucher` where `id` = '" . $invoice_voucher_id . "')";

					$resultSelectInvoiceVoucherDetails = $dbConn->select($sqlSelectInvoiceVoucherDetails);

					$invoice_voucher_no = $resultSelectInvoiceVoucherDetails[0]['VoucherNo'];

					echo '<br/>Invoice Voucher Existance Check : ';

					if($resultSelectInvoiceVoucherDetails <> '')
					{
						echo 'Success';
					}
					else
					{
						echo 'Failed';
					}

					echo '<br/>Checking Entry exist in InvoiceStatus : ';

					$sqlSelectEntryFromInvoiceStatus = "Select * from invoicestatus where `InvoiceRaisedVoucherNo` =	" . $invoice_voucher_no . " and InvoiceClearedVoucherNo = " . $payment_voucher_no;

					$resultSelectEntryFromInvoiceStatus = $dbConn->select($sqlSelectEntryFromInvoiceStatus);

					if($resultSelectEntryFromInvoiceStatus <> '')
					{
						echo 'Success';	

						if($tds_amount > 0)
						{
							echo '<br/>Checking TDS Voucher exist in InvoiceStatus : ';

							if($resultSelectEntryFromInvoiceStatus[0]['TDSVoucherNo'])
							{
								echo 'Success';
							}
							else
							{
								echo 'Failed';
							}
						}
					}
					else
					{
						echo 'Failed';
					}


				}
				else
				{
					echo 'Failed';
				}

				if($ref_no > 0 && $is_multiple_entry == 0)
				{
					echo '<br>Checking Multiple Entry deleted : '; 

					//Payment entry must be deleted
					$sqlSelectIfPaymentDeleted = "Select * from paymentdetails where id = " . $payment_id;
					$resultSelectIfPaymentDeleted = $dbConn->select($sqlSelectIfPaymentDeleted);

					if($resultSelectIfPaymentDeleted == '')
					{
						echo 'Success';
					}
					else
					{
						echo 'Failed';
					}
				}
				else
				{
					$sqlSelectPaymentVoucherNo = "Select `VoucherNo` from `voucher` where `RefNo` = '" . $payment_id . "' and `RefTableID` = '" . TABLE_PAYMENT_DETAILS . "'";
				}

				continue;
				
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
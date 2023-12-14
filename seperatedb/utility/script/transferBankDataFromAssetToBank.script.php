<?php

include_once("../../../classes/defaults.class.php");
include_once ("../../../classes/include/dbop.class.php");
include_once("../../../classes/dbconst.class.php");
include_once("../../../classes/BalanceSheet.class.php");
include_once("../../../classes/utility.class.php");

session_start(); 
$parent=0;
$bSuccess = true;

if(isset($bSuccess))
{
	try
	{
		$startNo = (int)$_REQUEST['start'];
		$dbPrefix = "hostmjbt_society";
		$dbName = $dbPrefix . $startNo;
		$body = '';
		$m_dbConn = new dbop(false,$dbName);
		$obj_utility = new utility($m_dbConn);
		
		if($m_dbConn->isConnected == false)
		{
			echo ' .....Connection Failed';	
		}
		else
		{
			
			//Select Bank and cash category ledgers
			
			$query = "SELECT concat(APP_DEFAULT_CASH_ACCOUNT,',', APP_DEFAULT_BANK_ACCOUNT) as Catid FROM appdefault";
			
			$result = $m_dbConn->select($query);
			
			$catId = $result[0]['Catid'];
			
			
			$query1 = 'SELECT * FROM assetregister WHERE LedgerID in(SELECT id FROM ledger WHERE categoryid in ('.$catId.'))';
			$list = $m_dbConn->select($query1);
			$voucherIds = array_column($list,'VoucherID');
			$voucherIdstring = implode(',',$voucherIds);
			
			if($_REQUEST['method'] == "Fetch"){
			
				$body .= "<html><head><style>body { font: normal medium/1.4 sans-serif;}table {border-collapse: collapse;width: 100%;}th, td {padding: 0.25rem;text-align: left;border: 1px solid #ccc;}tbody tr:nth-child(odd) {background: #eee;} label{color:black}</style></head>";
				$body .= "<div align='center' style='width:100%;font-weight: bold;'><div><label>Bank Entries in Asset Table</label></div>";
				$body .= "<table  align='center' style='border-collapse: collapse;border:1px solid black;bgcolor:gray;color:black'>";
				$body .= "<thead><tr>";
				$body .= "<th>Sr No.</th>";
				$body .= "<th>Date</th>";
				$body .= "<th>Voucher Id</th>";
				$body .= "<th>Ledger ID</th>";
				$body .= "<th>Debit</th>";
				$body .= "<th>Credit</th>";
				$body .= "</tr></thead><tbody>";
				
				
				
				$counter = 1;
				foreach($list as $row){
					extract($row);
					$body .="<tr>";
					$body .= "<td>".$counter."</td>";
					$body .= "<td>".$Date."</td>";
					$body .= "<td>".$VoucherID."</td>";
					$body .= "<td>".$LedgerID."</td>";
					$body .= "<td>".$Debit."</td>";
					$body .= "<th>".$Credit."</th>";
					$body .="</tr>";
					$counter++;
				}
				$body .="</tbody></table></html>";
				echo "<center><button type='button' name='ExportToExcel' id='ExportToExcel' onClick='ExportToExcel();' style='font-size: 25px;'>Export To Excel</button><center><br>";
				echo "<center><button type='button' name='transferAmount' id='UpdateAmount' onClick='UpdateAmount();' style='font-size: 25px;'>Transfer Amount</button><center><br>";
				echo  $body;
			}
			else if($_REQUEST['method'] == "Update"){
			
				try{
					$m_dbConn->begin_transaction();
					$cnt = 0;
					$idsToDelete = array();
					
					
					if($deleteResult !== false){
						
						foreach($list as $row)
						{
							extract($row);
							$isOpeningBalance = 0;
							
							$amount = $Credit;
							if($Debit != '0'){
								$amount = $Debit;
							}
							
							$subQuery = "";
							$updateJV = false;
							
							if($VoucherTypeID == VOUCHER_PAYMENT){
								
								$transactionType = 'ReceivedAmount';
								$subQuery = "SELECT id as ID, ChqLeafID as DepositID FROM `paymentdetails` where id IN(SELECT RefNO FROM voucher WHERE id = '$VoucherID')";
							}
							else if($VoucherTypeID == VOUCHER_RECEIPT){
								
								$transactionType = 'PaidAmount';
								$subQuery = "SELECT ID, DepositID FROM `chequeentrydetails` where ID IN(SELECT RefNO FROM voucher WHERE id = '$VoucherID')";
							}
							else if($VoucherTypeID == VOUCHER_JOURNAL){
								$updateJV = true;
							}
							
							$subResult = array();
							if(!empty($subQuery)){
								
								$subResult = $m_dbConn->select($subQuery);
								
								if(!empty($subResult)){
									
									$depositGroup = $subResult[0]['DepositID'];
									$chequeDetailID = $subResult[0]['ID'];
									$idsToDelete[] = $VoucherID;
									$sqlInsert = "INSERT INTO `bankregister`(`Date`, `LedgerID`, `VoucherID`, `VoucherTypeID`, `" . $transactionType . "`, `DepositGrp`, `ChkDetailID`, `Is_Opening_Balance`, `Cheque Date`, `Reconcile Date`, `ReconcileStatus`, `Reconcile`, `Return`) VALUES ('" . $Date . "', '" . $LedgerID . "', '" . $VoucherID .  "', '" . $VoucherTypeID . "', '" . $amount . "', '" . $depositGroup . "', '" . $chequeDetailID . "', '" . $isOpeningBalance . "', '".$Date."', '0000-00-00', '0', '0', '0')";
							$sqlResult = $m_dbConn->insert($sqlInsert) or print("Issue");
							
								}
							}
							else{
								
								if($updateJV == true){
									
									
									$sqlInsert = "INSERT INTO `bankregister`(`Date`, `LedgerID`, `VoucherID`, `VoucherTypeID`, `ReceivedAmount`, `Is_Opening_Balance`) VALUES ('" . $Date . "', '" . $LedgerID . "', '" . $VoucherID .  "', '" . $VoucherTypeID . "', '" . $amount . "', '0')";
							$sqlResult = $m_dbConn->insert($sqlInsert) or print("Issue");
							$idsToDelete[] = $VoucherID;	
								
								}
							}
						}
						$cnt = count($idsToDelete);
						$idsString = implode(",",$idsToDelete);
						$query1 = 'DELETE FROM assetregister WHERE VoucherID in('.$idsString.')'; // only delete those entries which are grt updated
						$deleteResult = $m_dbConn->delete($query1);
					}
					else{
					
						throw new Exception("unable to delete"); 
					
					}
					$m_dbConn->commit();
					echo "<br><br><br><br><b><span style='color:#5600ff'>TOTAL NO. Of ROW UPDATED IS ".$cnt."</b></span>";
				}
				catch(Exception $e){
					$m_dbConn->rollback();
					echo "ERROR: ".$e->getMessage();
				}
			}
		}
		
	}
	catch(Exception $exp)
	{
		echo $exp;
	}
	
}

	
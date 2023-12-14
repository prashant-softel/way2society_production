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
			$query = 'SELECT sub.id, sub.By, sub.VoucherNo, sub.RefTableID, sub.vouchertypeid, sub.credit, sub.debit, sub.Date,sub.diff FROM (SELECT id, VoucherNo as voucherno, `By`, Date, RefTableID,VoucherTypeID as vouchertypeid, sum(Debit) as debit, sum(Credit) as credit, sum(Debit) - sum(Credit) as diff FROM `voucher` group by VoucherNo ) as sub where sub.diff <> 0';
			$VoucherList = $m_dbConn->select($query);
			
			if($_REQUEST['method'] == "Fetch"){
			
				$body .= "<html><head><style>body { font: normal medium/1.4 sans-serif;}table {border-collapse: collapse;width: 100%;}th, td {padding: 0.25rem;text-align: left;border: 1px solid #ccc;}tbody tr:nth-child(odd) {background: #eee;} label{color:black}</style></head>";
				$body .= "<div align='center' style='width:100%;font-weight: bold;'><div><label>Voucher Debit Total MisMatch Entries</label></div>";
				$body .= "<table  align='center' style='border-collapse: collapse;border:1px solid black;bgcolor:gray;color:black'>";
				$body .= "<thead><tr>";
				$body .= "<th>Sr No.</th>";
				$body .= "<th>Date</th>";
				$body .= "<th>Voucher Id</th>";
				$body .= "<th>Voucher No.</th>";
				$body .= "<th>Voucher Type</th>";
				$body .= "<th>BY Ledger Id</th>";
				$body .= "<th>Amount Differece</th>";
				$body .= "</tr></thead><tbody>";
				
				
				
				$counter = 1;
				foreach($VoucherList as $row){
					extract($row);
					$body .="<tr>";
					$body .= "<td>".$counter."</td>";
					$body .= "<td>".$Date."</td>";
					$body .= "<td>".$id."</td>";
					$body .= "<td>".$VoucherNo."</td>";
					$body .= "<td>".$vouchertypeid."</td>";
					$body .= "<th>".$By."</th>";
					$body .= "<td>".$diff."</td>";
					$body .="</tr>";
					$counter++;
				}
				$body .="</tbody></table></html>";
				echo "<center><button type='button' name='ExportToExcel' id='ExportToExcel' onClick='ExportToExcel();' style='font-size: 25px;'>Export To Excel</button><center><br>";
				echo "<center><button type='button' name='UpdateAmount' id='UpdateAmount' onClick='UpdateAmount();' style='font-size: 25px;'>Update Amount</button><center><br>";
				echo  $body;
			}
			else if($_REQUEST['method'] == "Update"){
			
				try{
					$m_dbConn->begin_transaction();
					$cnt = 0;
					foreach($VoucherList as $row){
						extract($row);
						if($vouchertypeid == VOUCHER_SALES && !empty($By) && $By !== 0 && !empty($id) && $id !== 0){
							
							$updateVoucher = "UPDATE voucher SET Debit = '".$credit."' WHERE id = '".$id."'";
							$updateResult = $m_dbConn->update($updateVoucher);
							$ledgerDetails = $obj_utility->getParentOfLedger($By); // for Sale Voucher `By` ledger always be in asset Register
							
							if($ledgerDetails['group'] == ASSET){
								
								$query =  "UPDATE assetregister SET Debit = '".$credit."' WHERE VoucherID = '".$id."' AND LedgerID = '".$By."'";
								$result =  $m_dbConn->update($query);
								if($result !== FALSE){
									$cnt++;
								}
							}
							echo "<BR><BR> <span style='color:green'> <b>Success</b> : Voucher Id ".$id." previous amt : ".$debit." updated to ".$credit."</span>";
						}
						else if((($vouchertypeid == VOUCHER_DEBIT_NOTE) || ($vouchertypeid == VOUCHER_JOURNAL && $RefTableID == TABLE_SALESINVOICE)) && $By !== 0 && !empty($id) && $id !== 0){
								
								if($diff == 1 || $diff == -1){
									
									
									$updateVoucher = "UPDATE voucher SET Debit = '".$credit."' WHERE id = '".$id."'";
									$updateResult = $m_dbConn->update($updateVoucher);
									$ledgerDetails = $obj_utility->getParentOfLedger($By); // for Sale Voucher `By` ledger always be in asset Register
									
									if($ledgerDetails['group'] == ASSET)
									{
										$query =  "UPDATE assetregister SET Debit = '".$credit."' WHERE VoucherID = '".$id."' AND LedgerID = '".$By."'";
										$result =  $m_dbConn->update($query);
										if($result !== FALSE){
											$cnt++;
										}				
									}
								}
						}
						else if($vouchertypeid == VOUCHER_CREDIT_NOTE){
							
							if($diff == 1 || $diff == -1){
								
								$getVoucherQuery = "SELECT id as voucherId, `To` FROM voucher WHERE VoucherNo = '".$VoucherNo."' and `To` != ''";
								$voucherDetails = $m_dbConn->select($getVoucherQuery);
								$voucherId = $voucherDetails[0]['voucherId'];
								$toLedger = $voucherDetails[0]['To'];
								$toLedger;
								$ledgerDetails = $obj_utility->getParentOfLedger($toLedger);
						
								$updateVoucher = "UPDATE voucher SET Credit = '".$debit."' WHERE id = '".$voucherId."'";
								$updateResult = $m_dbConn->update($updateVoucher);
								
								if($ledgerDetails['group'] == ASSET)
								{
									$query =  "UPDATE assetregister SET Credit = '".$debit."' WHERE VoucherID = '".$voucherId."' AND LedgerID = '".$toLedger."'";
									$result =  $m_dbConn->update($query);
									if($result !== FALSE){
										$cnt++;
									}				
								}
							}
						}
						else{
						
							echo "<BR><BR> <span style='color:#ff0000'> <b>No Operation</b> : Code haven't implemented to update amount for voucher Type  ".$vouchertypeid." or blank by Ledger.</span>";
						}
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

	
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
	
		$m_dbConn = new dbop(false,$dbName);
		
		if($m_dbConn->isConnected == false)
		{
			echo ' .....Connection Failed';	
		}
		else
		{
			
			$query = 'SELECT sub.count, sub.VoucherNo FROM (SELECT count(`By`) as count, VoucherNo from voucher where `By` != "" group by voucherNo order by count) as sub where sub.count > 1';
			$duplicateVoucherList = $m_dbConn->select($query);
			
			$getMaxVoucherQuery = "SELECT max(VoucherNo) as voucherNo FROM `voucher`";
			$maxVoucherNo = $m_dbConn->select($getMaxVoucherQuery);
			$nextVoucher = $maxVoucherNo[0]['voucherNo'];
			
			
			$noRowAffected = 0;
			foreach($duplicateVoucherList as $row){
			
				$voucherNo = $row['VoucherNo'];
				$subCnt = 0;
				$subQuery = "SELECT id, SrNo FROM voucher WHERE VoucherNo = '".$voucherNo."'" ;
				$subResult = $m_dbConn->select($subQuery);
				
				foreach($subResult as $subrow){
					
					if($subrow['SrNo'] == 1){
						$subCnt++;
						if($subCnt > 1){
							$nextVoucher++;
						}
					}
					if($subCnt > 1){
						$updateQuery = "UPDATE voucher SET VoucherNo = '".$nextVoucher."' WHERE id = '".$subrow['id']."'";
						$m_dbConn->update($updateQuery);
						$noRowAffected++;
					}
				}
			}
			
			$updateCounterQuery = "Update `counter` set voucher_no = '".($nextVoucher+1)."'";
			$m_dbConn->update($updateCounterQuery);
		}
		
		echo "</BR></BR> ".$noRowAffected." rows updated.";
	}
	catch(Exception $exp)
	{
		echo $exp;
	}
}

	
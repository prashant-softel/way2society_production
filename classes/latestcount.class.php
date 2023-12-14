<?php
	
		include_once("changelog.class.php");
		include_once("include/dbop.class.php");
		include_once("email_format.class.php");
		
		class latestCount
		{
			public $m_dbConn;
			public $m_objLog;
			private $Conn; //Making new connection to insert log if voucher counter not found.
			
			function __construct($dbConn)
			{
				$this->Conn = new dbop();
				$this->m_dbConn = $dbConn;
				$this->m_objLog = new changeLog($this->Conn);
				
			}
			
			function getLatestVoucherNo($society_id, $bAutoIncrement = true/*Sets next voucher id to DB */, $periodID = '')
			{
				
				$sqlSelect = "Select voucher_no from counter where society_id='" . $society_id . "'";
				
				$sqlResult = $this->m_dbConn->select($sqlSelect);
				
				$sqlCounter = $sqlResult[0]['voucher_no'];
				
				if(empty($sqlCounter) || $sqlCounter == 0)
				{
					 $this->m_objLog->setLog("Voucher Number Not Found during Trasncation", $_SESSION['login_id'], 'VOUCHER', '--');
					 die();
				}
				
				// Check Voucher No is not already exits in voucher Table
				if($this->checkVoucherNoIsNotExits($sqlCounter))
				{
					if($bAutoIncrement == true)
					{
						$this->updateLatestVoucherNo($society_id, ($sqlCounter + 1));
					}
					return $sqlCounter;
				}
				else
				{
					sendDuplicateVoucherNoNotification($sqlCounter);
					$vQuery = "SELECT MAX(voucherNo) as maxVoucher FROM voucher";
					$result = $this->m_dbConn->select($vQuery);
					$maxVoucher = $result[0]['maxVoucher'];
					if($bAutoIncrement == true)
					{
						$this->updateLatestVoucherNo($society_id, ($maxVoucher + 2));
					}
					return $maxVoucher+1;
				}
			}
			
			function checkVoucherNoIsNotExits($sqlCounter){

				$vQuery = "SELECT * FROM voucher WHERE VoucherNo = '".$sqlCounter."'";
				$vResult = $this->m_dbConn->select($vQuery);
				if(empty($vResult)){
					return true;
				}
				else{
					return false;
				}
			} 
			
			function getLatestBillNo($society_id, $bAutoIncrement = true/*Sets next bill no to DB */, $periodID = '')
			{
				$sqlSelect = "Select bill_no from counter where society_id='" . $society_id . "'";
				$sqlResult = $this->m_dbConn->select($sqlSelect);
				
				$sqlCounter = $sqlResult[0]['bill_no'];
				
				if($bAutoIncrement == true)
				{
					$this->updateLatestBillNo($society_id, ($sqlCounter + 1));
				}
				
				return $sqlCounter;
			}
			
			function getLatestReceiptNo($periodID = '', $societyID = '')
			{
				return 1;
			}
			
			function updateLatestVoucherNo($society_id, $nextVoucherNo, $periodID = '')
			{
				$sqlUpdate = "UPDATE `counter` SET `voucher_no`='"  . $nextVoucherNo . "' WHERE society_id='" . $society_id . "'";
				$sqlResult = $this->m_dbConn->update($sqlUpdate);
			}
			
			function updateLatestBillNo($society_id, $nextBillNo, $periodID = '')
			{
				$sqlUpdate = "UPDATE `counter` SET `bill_no`='"  . $nextBillNo . "' WHERE society_id='" . $society_id . "'";
				$sqlResult = $this->m_dbConn->update($sqlUpdate);			
			}
			
			function updateLatestReceiptNo($periodID = '', $societyID = '')
			{
				
			}
			
			function getLatestRequestNo($society_id)
			{
				$sqlSelect = "select MAX(`request_no`) as max from `service_request` where `society_id`='" .$society_id."'" ;	
				$sqlResult = $this->m_dbConn->select($sqlSelect);	
				//$length = sizeof($sqlResult);
				//echo "length: ".$length;
				$sqlCounter = $sqlResult[0]['max'];			
				
				
				//echo "count: ".$sqlCounter;
					$sqlCounter = $sqlCounter + 1;
					//$sqlUpdate = "UPDATE `counter` SET `request_no`='"  . $sqlCounter . "' WHERE society_id='" . $society_id . "'";
					//echo $sqlUpdate;
					//$sqlResult = $this->m_dbConn->update($sqlUpdate);								
				//echo "count2: ".$sqlCounter;
				return $sqlCounter;
			}
		}
	
	
?>
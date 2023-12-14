<?php
	include('config_script.php');
	
	
	error_reporting(0);	

	function GetResult($result)
	{
		$count = 0;
		while($row = $result->fetch_array(MYSQL_ASSOC))
		{
			$data[$count] = $row;
			$count++;
		}
		return $data;
	}
	
	try
	{	
		$hostname = DB_HOST;
		$username =DB_USER;
		$password = DB_PASSWORD;
		$dbPrefix = 'hostmjbt_society';
		$Societybody = '';
		//$Start = 148;
		//$end = 148;	
		//$societiesDatabaseNumber = array(1,7,10);
		$societiesDatabaseNumber = array(1,7,10,19,32,38,50,53,60,69,76,84,118,125,129,132,136,145,148,153);
		//var_dump($societiesDatabaseNumber);
		
		for($iCount = 0/*$Start*/; $iCount <= /*$end*/ sizeof($societiesDatabaseNumber); $iCount++)
		{
			$dbName = $dbPrefix . $societiesDatabaseNumber[$iCount];
			//$dbName = $dbPrefix . $iCount;
			echo '<br/><br/>Connecting DB : ' . $dbName;
			
			
			$mMysqli = mysqli_connect($hostname, $username, $password, $dbName);
			if(!$mMysqli)
			{
					echo '<br/>Connection Failed';	
			}
			else
			{
				echo '<br/>Connected';
				
				$getGSTLedgerIDSQL = "Select APP_DEFAULT_CGST,APP_DEFAULT_SGST from appdefault";
				$GSTLedgerID = mysqli_query($mMysqli, $getGSTLedgerIDSQL);
				$GSTLedgerID = GetResult($GSTLedgerID);
				
				$CGSTLedgerID = $GSTLedgerID[0]['APP_DEFAULT_CGST'];
				$SGSTLedgerID = $GSTLedgerID[0]['APP_DEFAULT_SGST'];
				
				
				
				$FetchInvalidEntryRef  = "Select RefNo from voucher group by RefNo having sum(Credit) != SUM(Debit)";
				$result = mysqli_query($mMysqli, $FetchInvalidEntryRef);	
				if($result == true)
				{
					$data = GetResult($result);
					for($i = 0; $i < sizeof($data); $i++)
					{
						if($data[$i]['RefNo'] <> 0 && $data[$i]['RefNo'] <> '')
						{
							$GSTDetailsFromBillDetailsTable = "Select CGST,SGST from billdetails where id ='".$data[$i]['RefNo']."'";	
							$GSTresult = mysqli_query($mMysqli, $GSTDetailsFromBillDetailsTable);
							$GSTDetails = GetResult($GSTresult);
							
							$CGSTUpdate = mysqli_query($mMysqli, "Update voucher set Credit = '".$GSTDetails[0]['CGST']."' where `To` = '".$CGSTLedgerID."' and RefNo = '".$data[$i]['RefNo']."'");
							$SGSTUpdate = mysqli_query($mMysqli, "Update voucher set Credit = '".$GSTDetails[0]['SGST']."' where `To` = '".$SGSTLedgerID."' and RefNo = '".$data[$i]['RefNo']."'");
							/*
							echo "<br>Update voucher set Credit = '".$GSTDetails[0]['CGST']."' where `To` = '".$CGSTLedgerID."'and RefNo = '".$data[$i]['RefNo']."'";
							echo "<br>Update voucher set Credit = '".$GSTDetails[0]['SGST']."' where `To` = '".$SGSTLedgerID."' and RefNo = '".$data[$i]['RefNo']."'";
							echo "<br>Select id from voucher where RefNo = '".$data[$i]['RefNo']."' and `To` in ('".$CGSTLedgerID."','".$SGSTLedgerID."')";*/
							
							
							$LiabilityRegister = mysqli_query($mMysqli, "Select id from voucher where RefNo = '".$data[$i]['RefNo']."' and `To` in ('".$CGSTLedgerID."','".$SGSTLedgerID."')");
							$LiabilityRegisterIDs = GetResult($LiabilityRegister);
						
							for($j = 0 ; $j < sizeof($LiabilityRegisterIDs); $j++)
							{
								echo "<br><br>update liabilityregister set `Credit` = '".$GSTDetails[0]['CGST']."' where VoucherID = '".$LiabilityRegisterIDs[$j]['id']."'";
							 	$UpdateLiabilityRegister =  mysqli_query($mMysqli, "update liabilityregister set `Credit` = '".$GSTDetails[0]['CGST']."' where VoucherID = '".$LiabilityRegisterIDs[$j]['id']."'");	
							}
						}
						
					}
					$DeleteVoucher =  mysqli_query($mMysqli, "Delete From voucher Where `Date` = '0000-00-00' and `Credit` > 0");
						
					if($DeleteVoucher == true)
					{
						echo '<br>Invalid Dated record deleted successfully from voucher Table';
					}
					else
					{
						continue;
					}
					$DeleteliabilityregisterRecord =  mysqli_query($mMysqli, "Delete From liabilityregister Where `Date` = '0000-00-00' and `Credit` > 0");	
					if($DeleteVoucher == true)
					{
						echo '<br>Invalid Dated record deleted successfully from Liability Table';
					}
				}	
			}
		}
		mysqli_close($mMysqli);
		echo '<br/>Connection Closed';
	}
	catch(Exception $exp)
	{
		echo $exp;
	}
		
	function SendBillReminderSMS($dbName, $societyID, $socoetyName, $societyCode, $periodID, $dueDate)
	{	
		$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$objUtility  = new utility($dbConn,$dbConnRoot);
				
	}
	
	
	
	
?>
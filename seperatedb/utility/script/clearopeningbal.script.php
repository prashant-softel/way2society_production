<?php
	include('config_script.php');
	error_reporting(0);	
	if(isset($_REQUEST['start']) && isset($_REQUEST['end']) &&
		$_REQUEST['start'] <> '' && $_REQUEST['end'] <> '')
	{
		$dbhost = DB_HOST;
		$dbuser = DB_USER;
		$dbpass = DB_PASSWORD;
		$dbprefix = 'hostmjbt_society';
		$startNo = (int)$_REQUEST['start'];
		$endNo = (int)$_REQUEST['end'];
		for($iCount = $startNo; $iCount <= $endNo; $iCount++)
			{
				$dbname = $dbprefix . $iCount;
				echo '<br/><br/>Connecting DB : ' . $dbname;
				$mMysqli = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);
				if(!$mMysqli)
				{
					echo '<br/>Connection Failed';
				}
				else
				{
					$ledgerid = "";
					$count = 0;
					echo '<br/>Connected';
					$ledgerdetails = getResult($mMysqli, "SELECT * from ledger");
					$totalledgers = count($ledgerdetails);
					
					$default_due_from_member = getResult($mMysqli, "SELECT `APP_DEFAULT_DUE_FROM_MEMBERS` FROM `appdefault`");
					 for($i = 0 ;$i < sizeof($ledgerdetails); $i++)
					 {
						 if($ledgerdetails[$i]['categoryid'] <> $default_due_from_member[0]['APP_DEFAULT_DUE_FROM_MEMBERS'])
						 {
							 $ledgerid .= $ledgerdetails[$i]['id'] . ",";
							 $count = $count + 1;
							 
						 }
					 }
					 
					 $ledgerid = rtrim($ledgerid,',');
					 $query = "UPDATE `ledger` SET `opening_balance`= '0' where `id` in( ".$ledgerid .")";			
					 $mMysqli->query($query);
					 $query1 = "UPDATE `liabilityregister` SET `Debit` = '0' , `Credit` = '0' where `LedgerID` in(".$ledgerid.") and `Is_Opening_Balance` = 1";
					 $mMysqli->query($query1);
					 $query2 = "UPDATE `assetregister` SET `Debit` = '0' , `Credit` = '0' where `LedgerID` in(".$ledgerid.") and `Is_Opening_Balance` = 1";
					 $mMysqli->query($query2);
					 $query3 = "UPDATE `bankregister` set `ReceivedAmount` = '0' , `PaidAmount` = '0'  where `LedgerID` in(".$ledgerid.") and `Is_Opening_Balance` = 1";
					 $mMysqli->query($query3);
					 $dueledgercount = $totalledgers - $count;
				echo '<br>Opening Balances Cleared for '.$count.' ledgers from '.$totalledgers.' ledgers as rest '.$dueledgercount.' ledgers belong to dues from member category...';
				}
			}
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
			
?>
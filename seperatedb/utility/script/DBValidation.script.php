<?php
	include('config_script.php');
	error_reporting(0);	
		
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
	
	function bankRegReturnChequeVal($mMysqli)
	{
		$result = getResult($mMysqli ,"SELECT * FROM `bankregister` WHERE `Return` = 1");
		for($i = 0; $i < sizeof($result); $i++)
		{
			echo "<br /> <font color='#FF0000'> INFORMATION : </font> BankRegister Entry [ID :".$result[$i]['id']."] is a bounce cheque Entry.";
		}
	}
	
	function bankRegDateVal($mMysqli)
	{
		$result = getResult($mMysqli,"SELECT bankregister.id, ledger.ledger_name, bankregister.Date FROM `bankregister` JOIN `ledger` ON bankregister.LedgerID = ledger.id WHERE bankregister.Date = '0000-00-00' OR bankregister.Date < '2015-04-01'");	
		for($i = 0; $i < sizeof($result); $i++)
		{			
			if($result[$i]['Date'] <> '0000-00-00')
			{				
				echo "<br /> <font color='#FF0000'> ERROR : </font> Invalid Date [ID :".$result[$i]['id']." LEDGER : ".$result[$i]['ledger_name']."]";
			}
			else
			{				
				echo "<br /> <font color='#FF0000'> ERROR : </font> BankRegister Entry [ID :".$result[$i]['id']." LEDGER : ".$result[$i]['ledger_name']."] has empty Voucher Date.";
			}
		}
	}
	
	try
	{				
		$mMysqli = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, "hostmjbt_societydb");
		if(!$mMysqli)
		{
			echo '<br/>Connection Failed';
		}
		else
		{
			echo '<br/>Connected';
			
			date_default_timezone_set('Asia/Kolkata');
			$data = getResult($mMysqli, "SELECT * FROM `society` WHERE `status` = 'Y' AND `client_id` = 1 ");				
			
			for($iCount = 0; $iCount < sizeof($data); $iCount++)
			{
				$dbName = $data[$iCount]['dbname'];				
				echo '<br/><br/>Connecting DB : ' . $dbName;								
		 
				$mMysqli1 = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, $dbName);
				if(!$mMysqli1)
				{
					echo '<br/>Connection Failed';
				}
				else
				{
					echo '<br/>Connected';																
																																																												
					bankRegReturnChequeVal($mMysqli1);
					bankRegDateVal($mMysqli1);																											
					mysqli_close($mMysqli1);
					echo '<br/>Connection Closed';								
				}
			}
						
			mysqli_close($mMysqli);
			echo '<br/>Connection Closed';
		}
		
	}
	catch(Exception $exp)
	{
		echo $exp;
	}													
	
?>
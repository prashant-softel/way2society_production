<?php
	include('config_script.php');
	error_reporting(0);	
	if(isset($_REQUEST['start']) && isset($_REQUEST['end']) && isset($_REQUEST['query']) &&
		$_REQUEST['start'] <> '' && $_REQUEST['end'] <> '' && $_REQUEST['query'] <> '')
	{
		try
		{
			$hostname = DB_HOST;
			$username =DB_USER;
			$password = DB_PASSWORD;
			$dbPrefix = 'hostmjbt_society';
			
			
			$startNo = (int)$_REQUEST['start'];
			$endNo = (int)$_REQUEST['end'];
			
			$query = str_replace('\\', '', $_REQUEST['query']);
			
			echo '<br/><br/>Verifying DB ' . $dbPrefix . $startNo . ' to ' . $dbPrefix . $endNo . ' with query : ' . $query;
			
			for($iCount = $startNo; $iCount <= $endNo; $iCount++)
			{
				$dbName = $dbPrefix . $iCount;
				echo '<br/><br/>Connecting DB : ' . $dbName;
				 
				$mMysqli = mysqli_connect($hostname, $username, $password, $dbName);
				if(!$mMysqli)
				{
					echo '<br/>Connection Failed';
				}
				else
				{
					echo '<br/>Connected';
					$result = mysqli_query($mMysqli, $query);
					$resultAry = mysqli_fetch_assoc($result);
					echo '<br/><br/>Result Size : ' . sizeof($resultAry) . '<br/>';
					var_dump($resultAry);
					/*if($result)
					{
						echo '<br/>Updated';
					}
					else
					{
						echo '<br/>Update Failed';
					}*/
					mysqli_close($mMysqli);
					echo '<br/>Connection Closed<br/>';
				}
			}
		}
		catch(Exception $exp)
		{
			echo $exp;
		}
	}
	else
	{
		echo 'Missing Parameters';
	}
	
?>
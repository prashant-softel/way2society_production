<?php
	include('config_script.php');
	error_reporting(0);	
	//if(isset($_REQUEST['start']) && isset($_REQUEST['end']) && isset($_REQUEST['query']) &&
		//$_REQUEST['start'] <> '' && $_REQUEST['end'] <> '' && $_REQUEST['query'] <> '')
	{
		try
		{
			$hostname = DB_HOST;
			$username =DB_USER;
			$password = DB_PASSWORD;
			$dbPrefix = 'hostmjbt_society';
	
			
			$startNo = 1;//(int)$_REQUEST['start'];
			$endNo = 170;//(int)$_REQUEST['end'];
			
			//$query = str_replace('\\', '', $_REQUEST['query']);
			
			echo '<br/><br/>Updating DB ' . $dbPrefix . $startNo . ' to ' . $dbPrefix . $endNo;
			
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

					$query = 'SELECT ID, unitID from `servicerequest_category`';

					$result = mysqli_query($mMysqli, $query);
					if($result)
					{
						$count = 0;
						while($row = $result->fetch_array(MYSQL_ASSOC))
						{
							if ($row['unitID'] > 0) 
							{
								echo '<br/><br/><br/>Unit ID : ' . $row['unitID'];
								echo $sqlSelect = 'SELECT mo.mem_other_family_id from `mem_other_family` as mo JOIN member_main as mm on mm.member_id = mo.member_id WHERE mm.`unit` = "' . $row['unitID'] . '" and mm.`ownership_status` = 1 and mo.`status` = "Y" and mo.`coowner` = 1';
								$member_result = mysqli_query($mMysqli, $sqlSelect);

								while($rowMember = $member_result->fetch_array(MYSQL_ASSOC))
								{
									$sqlUpdate = "Update `servicerequest_category` SET `member_id` = '" . $rowMember['mem_other_family_id']  . "' WHERE `id` = '" . $row['ID'] . "'";

									mysqli_query($mMysqli, $sqlUpdate);
								}
							}
						}											
					}	
					else
					{
						echo '<br/>Failed';
					}
					mysqli_close($mMysqli);
					echo '<br/>Connection Closed';
				}
			}
		}
		catch(Exception $exp)
		{
			echo $exp;
		}
	}
	/*else
	{
		echo 'Missing Parameters';
	}*/
	
?>

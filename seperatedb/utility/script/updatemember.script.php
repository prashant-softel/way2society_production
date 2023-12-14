<?php
	include('config_script.php');
	error_reporting(0);	
	die();//before running further , enter correct startno and end no and then uncomment die() in this line
	//if(isset($_REQUEST['start']) && isset($_REQUEST['end']) && isset($_REQUEST['query']) &&
		//$_REQUEST['start'] <> '' && $_REQUEST['end'] <> '' && $_REQUEST['query'] <> '')
	{
		try
		{
			$hostname = DB_HOST;
			$username =DB_USER;
			$password = DB_PASSWORD;
			$dbPrefix = 'hostmjbt_society';
	
			
			$startNo = 15;//(int)$_REQUEST['start'];
			$endNo = 15;//(int)$_REQUEST['end'];
			
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

					$query = 'SELECT member_id, owner_name, mob, email from `member_main`';

					$result = mysqli_query($mMysqli, $query);
					if($result)
					{
						$count = 0;
						while($row = $result->fetch_array(MYSQL_ASSOC))
						{
							//$data[$count] = $row;
							//$count++;
							echo '<br/><br/><br/>Member ID : ' . $row['member_id'] . ' Owner Name : ' . $row['owner_name'];
							//if (strpos($row['owner_name'], 'AND') !== false) 
							//{
								$sqlDelete = 'DELETE FROM `mem_other_family` WHERE `member_id` = "' . $row['member_id'] . '" and `coowner` > 0';
								mysqli_query($mMysqli, $sqlDelete);
								
	    						$owner = str_replace('&', ',', $row['owner_name']);
								$owner = str_replace('/', ',', $owner);
								$owner = str_replace(' AND ', ',', $owner);
								$owner_coll = explode(',', $owner);
								echo '<br/>Primary : ' . trim($owner_coll[0]) . '<br/>Others : ' . (sizeof($owner_coll) - 1);

								echo '<br/>' . $updateSql = "Update `member_main` SET `primary_owner_name` = '" . trim($owner_coll[0]) . "' WHERE `member_id` = '" . $row['member_id'] . "'";
								mysqli_query($mMysqli, $updateSql);

								for($i = 0; $i < sizeof($owner_coll); $i++)
								{
									echo '<br/>' . $owner_coll[$i];
									if($i == 0)
									{
										echo '<br/>' . $insertSql = "INSERT INTO `mem_other_family` (`member_id`, `other_name`, `coowner`, `relation`, `other_mobile`, `other_email`) VALUES ('" . $row['member_id'] . "', '" . trim($owner_coll[$i]) . "', '1', 'Self', '" . $row['mob'] . "', '" . $row['email'] . "')";
									}
									else
									{
										echo '<br/>' . $insertSql = "INSERT INTO `mem_other_family` (`member_id`, `other_name`, `coowner`) VALUES ('" . $row['member_id'] . "', '" . trim($owner_coll[$i]) . "', '2')";
									}

									mysqli_query($mMysqli, $insertSql);
								}
							//}
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
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
			
			$IsSelectQuery = false;
			$IsRecordExits = false;
			$NumberOfSociety = 0;
			$body = '';
			$query = trim($query,' ');
			$value = stripos($query,"SELECT");
			
			if(stripos($query,"SELECT") === 0)
			{
				$IsSelectQuery = true;
				echo '<br/><br/>Fetching Data ... ' . $dbPrefix . $startNo . ' to ' . $dbPrefix . $endNo . ' with query : ' . $query;
				echo '<br/><br/>';
			}
			else
			{
				echo '<br/><br/>Updating DB ' . $dbPrefix . $startNo . ' to ' . $dbPrefix . $endNo . ' with query : ' . $query;	
			}
			
			for($iCount = $startNo; $iCount <= $endNo; $iCount++)
			{
				$dbName = $dbPrefix . $iCount;
				if($IsSelectQuery == false)
				{
					echo '<br/><br/>Connecting DB : ' . $dbName;
				}
				
				$mMysqli = mysqli_connect($hostname, $username, $password, $dbName);
				if(!$mMysqli)
				{
					if($IsSelectQuery == false)
					{
						echo '<br/>Connection Failed';	
					}
					
				}
				else
				{
					if($IsSelectQuery == false)
					{
						echo '<br/>Connected';	
					}
					
					$result = mysqli_query($mMysqli, $query);
					if($result == true)
					{
						if($IsSelectQuery == true)
						{
							$data = GetResult($result);
							
							$getSocietyDetailsSql = "Select society_id, society_name from society";
							$SocietyDetails = mysqli_query($mMysqli, $getSocietyDetailsSql);
							$SocietyDetails = GetResult($SocietyDetails);
							
							if($NumberOfSociety <> 0)
							{
								$body .= "<br><br><br>";  
							}
							$body .= "<html><head>
									<style>
									  body { font: normal medium/1.4 sans-serif;}table {border-collapse: collapse;width: 95%;}th, td {padding: 0.25rem;text-align: left;border: 1px solid #ccc;}tbody tr:nth-child(odd) {background: #eee;} label{color:black}</style></head>";
							
							$body .= "<div align='center' style='width:95%;font-weight: bold;'><div><label>Society ID : </label><label>".$SocietyDetails[0]['society_id']."</label></div>
									 <div><label>Society Name : </label><label>".$SocietyDetails[0]['society_name']."</label></div>
									 <div><label>DBName : </label><label>".$dbName."</label></div></div>";
							
							if(count($data) <> 0)
							{
								$NumberOfSociety += 1;
								$IsRecordExits = true;
								
								$Header = array_keys($data[0]);

								$body .= "<div><label>Total Record : </label><label>".count($data)."</label></div>";
											
								$body .= "<table  align='center' style='border-collapse: collapse;border:1px solid black;bgcolor:gray;color:black'>";
								$counter = 1;
								for($i = 0 ; $i< count($data); $i++)
								{
									if($i == 0)
									{
										$body .= "<thead><tr>";
										$Header = array_keys($data[0]);
										$body .= "<th>Sr No.</th>";
										for($j = 0;$j < count($Header); $j++)
										{
											$body .= "<th>".strtoupper($Header[$j])."</th>";				
										}
										$body .= "</tr></thead><tbody>";
									}
									
									$body .="<tr>";
									$body .= "<td>".$counter."</td>";
									for($k = 0;$k < count($data[$i]); $k++)
									{
										$index = $Header[$k];
										$body .= "<td>".strtoupper($data[$i][$index])."</td>";				
									}
									$body .="</tr>";
									$counter++;				
								}
								$body .="</tbody></table></html>";
							}
							else
							{
								$body .="<br><br><label align='center' style='width:95%;font-weight: bold;'>Data Not Exits..</label>";	
							}
						}	
					}
					else
					{
						$body .= "<br><label>Problem in your sql Query !!!</label>";
					}
					
					if($IsSelectQuery == false)
					{
						if($result)
						{
							echo '<br/>Updated';
						}
						else
						{
							echo '<br/>Update Failed';
						}	
					}
					else
					{
						$body .="<br><center><label align='center' style='width:95%;font-weight: bold;'>Query Execution Complete.</label><center>";
					}
					
					mysqli_close($mMysqli);
					$body .="<br/>Connection Closed";
				}
			}
			if($IsSelectQuery == true)
			{
				echo "<center><button type='button' name='ExportToExcel' id='ExportToExcel' onClick='ExportToExcel();' style='font-size: 25px;'>Export To Excel</button><center><br>";	
				echo "<center><label>Record Found In " .$NumberOfSociety." Society</label></center>";
				echo $body;	
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
?>
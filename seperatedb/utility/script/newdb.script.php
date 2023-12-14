<?php
	include('config_script.php');
	error_reporting(0);	
	if(isset($_REQUEST['start']) && isset($_REQUEST['end']) &&
		$_REQUEST['start'] <> '' && $_REQUEST['end'] <> '')
	{
		try
		{
			$dbhost = DB_HOST;
			$dbuser = DB_USER;
			$dbpass =DB_PASSWORD;
			$dbprefix = DB_DATABASE;
			
			
			$startNo = (int)$_REQUEST['start'];
			$endNo = (int)$_REQUEST['end'];
			
			$backup_dir_db = 'db/' . date("Ymd");
			if (!file_exists($backup_dir_db)) {
				mkdir($backup_dir_db, 0777, true);
			}
			
			//echo '<br/><br/>Processing DB ' . $dbprefix . $startNo . ' to ' . $dbprefix . $endNo;
			
			$refDBName = $dbprefix . '0';
			echo '<br/><br/>Connecting DB : ' . $refDBName . ' to create sql file';
			$backup_file = 'refdb.sql.gz';
			$command = "mysqldump --opt -h $dbhost -u $dbuser -p$dbpass $refDBName | gzip  > " . $backup_file;
			
			system($command, $retval);
			echo '<br/> Result : ' . $retval;			
			
			echo '<br/><br/>Processing DB ' . $dbprefix . $startNo . ' to ' . $dbprefix . $endNo;
			
			for($iCount = $startNo; $iCount <= $endNo; $iCount++)
			{
				$dbname = $dbprefix . $iCount;
				
				$mMysqli = mysqli_connect($dbhost, $dbuser, $dbpass, 'hostmjbt_societydb');
				if(!$mMysqli)
				{
					echo '<br/>Failed connection to Root DB';
					break;
				}
				else
				{
					echo '<br/>Connected';
					$sqlQuery = 'Select count(*) as cnt from `dbname` where `dbname` = "' . $dbname . '"';
					//echo $sqlQuery;
					$result = mysqli_query($mMysqli, $sqlQuery);
					
					if($result)
					{
						$count = 0;
						while($row = $result->fetch_array(MYSQL_NUM))
						{
							$count = $row[0];
						}
						
						if($count > 0)
						{
							echo '<br/>DB entry for ' . $dbname . ' already exist. Unable to continue initializing db.';
							continue;
						}
						else
						{
							$result = mysqli_query($mMysqli, 'INSERT INTO `dbname`(`dbname`) VALUES ("' . $dbname . '")');
							mysqli_close($mMysqli);
							
							echo '<br/><br/>Connecting DB : ' . $dbname;
							//$backup_file = $backup_dir_db . '/' . $dbname . '_' . date("Y-m-d_H-i-s") . '.sql.gz';
							
							//echo $command = 'mysql -h' .$dbhost .' -u' .$dbuser .' -p' .$dbpass .' ' .$dbname .' | gunzip < ' .$backup_file;
							echo $command = 'gunzip < ' . $backup_file . ' | mysql -h' .$dbhost .' -u' .$dbuser .' -p' .$dbpass .' ' .$dbname;
							exec($command,$output=array(),$worked);
							
							switch($worked)
							{
								case 0:
									echo 'Import file <b>' .$backup_file .'</b> successfully imported to database <b>' .$mysqlDatabaseName .'</b>';
									break;
								case 1:
									echo 'There was an error during import.';
								break;		
							}			
						}
					}
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
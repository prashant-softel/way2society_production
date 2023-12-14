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
			$dbprefix = 'hostmjbt_society';
			
			
			$startNo = (int)$_REQUEST['start'];
			$endNo = (int)$_REQUEST['end'];
			
			//$backup_dir_db = 'db/' . date("Ymd");
			//$backup_dir_db = '/var/www/html/seperatedb/utility/script/db/manual/' . date("Ymd");
			//C:\wamp\www\beta_aws_test_master\seperatedb\utility\script
			$backup_dir_db = 'db/db_restore_20200404';

			echo '<br/><br/>Processing DB ' . $dbprefix . $startNo . ' to ' . $dbprefix . $endNo;
			
			// Create connection
			$conn = new mysqli($dbhost, $dbuser, $dbpass);
			//$conn = new mysqli('localhost', 'root', '');
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}
			
			
			for($iCount = $startNo; $iCount <= $endNo; $iCount++)
			{
				$dbname = $dbprefix . $iCount;
				echo '<br/><br/>Check DB Exit : ' . $dbname;
				
				$sql = "SELECT SCHEMA_NAME
						  FROM INFORMATION_SCHEMA.SCHEMATA
						 WHERE SCHEMA_NAME = '".$dbname."'";
				
				$result = $conn->query($sql);	
				if(mysqli_num_rows($result) == 0)
				{
					// Create database
					$sql = "CREATE DATABASE ".$dbname;
					if ($conn->query($sql) === TRUE) {
						echo " </br></br>Database created successfully";
					} else {
						echo "</br></br> Error creating database: " . $conn->error;
					}		
				}	
				else
				{
					$SkipDB[] = $iCount; // Store Database which is already exits
				}	 
			}
			
			$conn->close();
			
			$test = 0;
			
			if($test == 1)
			{
				$dbname = 'hostmjbt_society59';
				$backup_file = $backup_dir_db . '/' . $dbname . '.sql.gz';
				$command =  "gunzip < $backup_file | mysql -u $dbuser -p$dbpass $dbname";
				system($command, $retval);
				echo '<br/> Result : ' . $retval;
			}
			else
			{
				for($iCount = $startNo; $iCount <= $endNo; $iCount++)
				{
					$dbname = $dbprefix . $iCount;
					echo '<br/><br/>Connecting DB : ' . $dbname;
					
					if(!in_array($iCount,$SkipDB))
					{
						$backup_file = $backup_dir_db . '/' . $dbname . '.sql.gz';
						//$command = "mysql -u $dbuser -p$dbpass $dbname < ".$backup_file;
						$command =  "gunzip < $backup_file | mysql -u $dbuser -p$dbpass $dbname";
						system($command, $retval);
						echo '<br/> Result : ' . $retval;		
					}
					else
					{
						echo "<br/> Database already exits";
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
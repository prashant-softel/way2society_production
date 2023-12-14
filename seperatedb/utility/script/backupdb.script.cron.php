<?php

echo 'start backup';
include('config_script.php');
	
	error_reporting(0);	
	
	$iStart = 0;
	$iEnd = 240;
	$bSuccess = true;

	
	if($bSuccess)
	{
		try
		{
			$dbhost = DB_HOST;
			$dbuser = DB_USER;
			$dbpass =DB_PASSWORD;
			$dbprefix = 'hostmjbt_society';
		
			
			$startNo = (int)$iStart;
			$endNo = (int)$iEnd;
			
			$backup_dir_db = '/var/www/html/seperatedb/utility/script/db/' . date("Ymd");
			if (!file_exists($backup_dir_db)) {
				mkdir($backup_dir_db, 0777, true);
			}
		
			$conn = new mysqli($dbhost, $dbuser, $dbpass,'hostmjbt_societydb'); // connecting to root database
			
			if($conn->connect_error)
			{
				echo "<br /> Failed to connect database ".$conn->connect_error;
			}
			else
			{
				$query  = "SELECT dbname FROM `dbname` ORDER BY id DESC LIMIT 1"; // get max db Count 
				$result = $conn->query($query);
				$result = mysqli_fetch_assoc($result);
				$arr = explode('hostmjbt_society',$result['dbname']);
				$endNo = $arr[1];
			}
			
			echo '<br/><br/>Processing DB ' . $dbprefix . $startNo . ' to ' . $dbprefix . $endNo;
			
			for($iCount = $startNo; $iCount <= $endNo; $iCount++)
			{
				$dbname = $dbprefix . $iCount;
				echo '<br/><br/>Connecting DB : ' . $dbname;
				$backup_file = $backup_dir_db . '/' . $dbname . '_' . date("Y-m-d_H-i-s") . '.sql.gz';
				echo "<br>".$command = "mysqldump --opt -h $dbhost -u $dbuser -p$dbpass $dbname | gzip  > " . $backup_file;
			
				system($command, $retval);
				echo '<br/> Result : ' . $retval;	
			}
			
			/****** BACKUP ROOT DATABASE ********/
			$dbname = 'hostmjbt_societydb';
			echo '<br/><br/>Connecting DB : ' . $dbname;
			$backup_file = $backup_dir_db . '/' . $dbname . '_' . date("Y-m-d_H-i-s") . '.sql.gz';
			echo "<br>".$command = "mysqldump --opt -h $dbhost -u $dbuser -p$dbpass $dbname -R | gzip  > " . $backup_file;
			
			system($command, $retval);
			echo '<br/> Result : ' . $retval;	
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
date_default_timezone_set('Asia/Kolkata');
echo "<br> Current Cron Execution time is " . date("h:i:sa");
	
?>

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
			$backup_dir_db = '/var/www/html/seperatedb/utility/script/db/manual/' . date("Ymd");
			if (!file_exists($backup_dir_db)) {
				mkdir($backup_dir_db, 0777, true);
			}
			
			echo '<br/><br/>Processing DB ' . $dbprefix . $startNo . ' to ' . $dbprefix . $endNo;
			
			for($iCount = $startNo; $iCount <= $endNo; $iCount++)
			{
				$dbname = $dbprefix . $iCount;
				echo '<br/><br/>Connecting DB : ' . $dbname;
				$backup_file = $backup_dir_db . '/' . $dbname . '_' . date("Y-m-d_H-i-s") . '.sql.gz';
				$command = "mysqldump --opt -h $dbhost -u $dbuser -p$dbpass $dbname | gzip  > " . $backup_file;
				
			
				system($command, $retval);
				echo '<br/> Result : ' . $retval;	
			}
			
			if(isset($_REQUEST['backuproot']) && $_REQUEST['backuproot'] == 1)
			{
				/****** BACKUP ROOT DATABASE ********/
				$dbname = 'hostmjbt_societydb';
				echo '<br/><br/>Connecting DB : ' . $dbname;
				$backup_file = $backup_dir_db . '/' . $dbname . '_' . date("Y-m-d_H-i-s") . '.sql.gz';
				$command = "mysqldump --opt -h $dbhost -u $dbuser -p$dbpass $dbname -R | gzip  > " . $backup_file;
				
			
				system($command, $retval);
				echo '<br/> Result : ' . $retval;		
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
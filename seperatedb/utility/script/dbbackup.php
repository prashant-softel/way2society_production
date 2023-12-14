<?php
	include('config_script.php');
	/**************************************Database Backup********************************/
	try
	{
		$hostname = DB_HOST;
		$username =DB_USER;
		$password = DB_PASSWORD;
		$dbPrefix = DB_DATABASE;
		
		
		$backup_dir_db = 'db/' . date("Ymd");
		if (!file_exists($backup_dir_db)) {
			mkdir($backup_dir_db, 0777, true);
		}
		
		for($iCount = 1; $iCount <= 1; $iCount++)
		{
			echo $dbname = $dbPrefix . $iCount;
			echo $backup_file = $backup_dir_db . '/' . $dbname . '_' . date("Y-m-d_H-i-s") . '.sql.gz';
			
			$command = "mysqldump --opt -h $hostname -u $username -$password $dbname | gzip  > " . $backup_file;
		
			system($command, $retval);
			echo '<br/> Result : ' . $retval;	
		}
	}
	catch(Exception $exp)
	{
		echo $exp;
	}
	
	/************************************************************************************/
?>
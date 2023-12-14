<?php
	
	/**************************************Database Backup********************************/
	
	$dbhost = 'localhost';
	$dbuser = 'hostmjbt_society';
	$dbpass = 'society123';
	$dbnameprefix = 'hostmjbt_society';
	
	$backup_dir_db = 'db/' . date("Ymd");
	if (!file_exists($backup_dir_db)) {
  		mkdir($backup_dir_db, 0777, true);
	}
	
	for($iCnt = 1; $iCnt <= 40; $iCnt++)
	{
		$dbname = $dbnameprefix . $iCnt;
		$backup_file = $backup_dir_db . '/' . $dbname . '_' . date("Y-m-d_H-i-s") . '.sql.gz';
		$command = "mysqldump --opt -h $dbhost -u $dbuser -p$dbpass $dbname | gzip  > " . $backup_file;
	
		system($command, $retval);
		echo '<br/> Result : ' . $retval;	
	}
	
	/************************************************************************************/
?>
<?php
	include('seperatedb/utility/script/config_script.php');
	error_reporting(0);	
		try
		{
			$dbhost = DB_HOST;
			$dbuser = DB_USER;
			$dbpass = DB_PASSWORD;
			$dbprefix = 'hostmjbt_societydb';

			$backup_dir_db = '/var/www/html/backup_db/' . date("Ymd");
			if (!file_exists($backup_dir_db)) {
				mkdir($backup_dir_db, 0777, true);
			}

				$dbname = $_SESSION['dbname'];

				 $exact_file = 'backup_db/' . date("Ymd") . '/' . $dbname . '_' . date("Y-m-d_H-i-s") . '.sql.gz';
				 $backup_file = $backup_dir_db . '/' . $dbname . '_' . date("Y-m-d_H-i-s") . '.sql.gz';
				$command = "mysqldump --opt -h $dbhost -u $dbuser -p$dbpass $dbname | gzip  > " . $backup_file;
			
				system($command, $retval);
				$connection = mysqli_connect($dbhost,$dbuser,$dbpass,$dbprefix);
				$insert=mysqli_query($connection,"INSERT INTO `hostmjbt_societydb`.`societydb_maintain` (`society_id`, `dbname`,  `description`, `path`, `created_at`) VALUES ( ".$_SESSION['society_id'].",'".$dbname."','".$_POST['description']."', '".$exact_file."', '".date('Y-m-d H-i-s')."')");
				
			
		}
		catch(Exception $exp)
		{
			// echo $exp;
		}

	
?>

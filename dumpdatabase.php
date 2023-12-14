<?php
include('seperatedb/utility/script/config_script.php');
	$dbhost = DB_HOST;
	$dbuser = DB_USER;
	$dbpass = DB_PASSWORD;
	$dbprefix = $_SESSION['dbname'];

	$ADMINdbhost = DB_HOST;
	$ADMINdbuser = DB_USER;
	$ADMINdbpass = DB_PASSWORD;
	$ADMINdbprefix = 'hostmjbt_societydb';
	$connectionAdmin = new mysqli($ADMINdbhost, $ADMINdbuser, $ADMINdbpass, $ADMINdbprefix);
	$connection = new mysqli($dbhost, $dbuser, $dbpass, $dbprefix);
	  $sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA LIKE '".$dbprefix."'";
	  // echo $sql;
	  $result = $connection->query($sql);
	  $tables = $result->fetch_all(MYSQLI_ASSOC);
	  foreach($tables as $table)
	  {
	   // echo $table['TABLE_NAME'];echo "<br>";
	      $sql = "TRUNCATE TABLE `".$table['TABLE_NAME']."`";
	      
	      $result = $connection->query($sql);
	  }
	  $SELECT="SELECT * FROM societydb_maintain WHERE society_id=".$_SESSION['society_id']." ORDER BY ID DESC LIMIT 1";
	  
	  $record = $connectionAdmin->query($SELECT);
	  $recordINFO = $record->fetch_all(MYSQLI_ASSOC);

		if (isset($recordINFO[0])) {
			$backup_dir_db = '/var/www/html/';
			$backup_file=$backup_dir_db.$recordINFO[0]['path'];
    		
		    $command =  "gunzip < $backup_file | mysql -u $dbuser -p$dbpass $dbprefix";
		    system($command, $retval);
		    }
    
		

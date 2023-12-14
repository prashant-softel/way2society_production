<?php
error_reporting(E_ALL);
	include_once("../../classes/include/check_session.php");
include('script/config_script.php');
	try
	{
		if($_SERVER['HTTP_HOST']=="localhost")
		{
			$hostname = DB_HOST;
			$username = DB_USER;
			$password = DB_PASSWORD;
			$dbname = DB_DATABASE;
		}
		else
		{
			$hostname = DB_HOST_ROOT;
			$username = DB_USER_ROOT;
			$password = DB_PASSWORD_ROOT;
			$dbname = DB_DATABASE_ROOT;
		}
	
	$mMysqli = mysqli_connect($hostname, $username, $password, $dbname);
	$query = "SELECT db.dbname, db.locked, soc.society_id, soc.society_code, soc.society_name, soc.status, soc.timestamp, clnt.client_name FROM dbname as db LEFT JOIN society as soc ON db.dbname = soc.dbname LEFT JOIN client as clnt ON soc.client_id = clnt.id";
	
	$result = mysqli_query($mMysqli, $query);
	$count = 0;
	while($row = $result->fetch_array(MYSQL_ASSOC))
	{	
		$data[$count] = $row;
		$count++;
	}
	mysqli_close($mMysqli);
	}
	catch(Exception $exp)
	{
		echo $exp;
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Way2Society - Update Tables</title>
<script type="text/javascript" language="javascript" src="js/jquery.js"></script>
</head>
<body>
	<h3>Society and related DB</h3>
	<div>
		<div id = 'total_db' style="color:#0000FF;font-weight:bold;">Total DB Count : </div>
		<!--<div id = 'total_society_active'>Active Society Count : </div>
		<div id = 'total_society_inactive'>Inactive Society Count : </div>-->
		<div id = 'db_in_use' style="color:#F00;font-weight:bold;">DB In Use : </div>
		<div id = 'db_available' style="color:#006600;font-weight:bold;">DB Available For Import : </div>
	</div>
	<br />
	<table border="2px" style="width:100%;text-align:center;">
		<thead>
			<th>Society ID</th>
			<th>Society Code</th>
			<th>Society Name</th>
			<th>Client Name</th>
			<th>DB Name</th>
			<th>Society Status</th>
			<th>DB Status</th>
			<th>Timestamp</th>
		</thead>
		<tbody>
			<?php
			try
			{
				$iDBCount = array();
				$iActiveSocCount = array();
				$iInActiveSocCount = array();
				$iDBInUse = array();
				$iDBAvailable = array();
				for($iCnt = 0 ; $iCnt < sizeof($data) ; $iCnt++)
				{
					$iDBCount++;
					?>
						<tr>
							<td><?php echo $data[$iCnt]['society_id']; ?></td>
							<td><?php echo $data[$iCnt]['society_code']; ?></td>
							<td><?php echo $data[$iCnt]['society_name']; ?></td>
							<td><?php echo $data[$iCnt]['client_name']; ?></td>
							<td><?php echo $data[$iCnt]['dbname']; $iDBCount[$data[$iCnt]['dbname']] = '1';?></td>
							<td><?php if($data[$iCnt]['status'] == 'Y'){echo '<font color="#009900"><b>ACTIVE</b></font>'; $iActiveSocCount[$data[$iCnt]['dbname']] = '1';}else if($data[$iCnt]['status'] == 'N'){echo '<font color="#FF0000"><b>INACTIVE</b></font>'; $iInActiveSocCount[$data[$iCnt]['dbname']] = '1';}; ?></td>
							<td><?php if($data[$iCnt]['locked'] == ''){echo '<font color="#009900"><b>AVAILABLE</b></font>'; $iDBAvailable[$data[$iCnt]['dbname']] = '1';}else{echo '<font color="#FF0000"><b>IN USE</b></font>'; $iDBInUse[$data[$iCnt]['dbname']] = '1';}; ?></td>
							<td><?php echo $data[$iCnt]['timestamp']; ?></td>
					<?php
				}
			}
			catch(Exception $exp)
			{
				echo $exp;
			}
			?>
		</tbody>
	</table>
	<script>
		document.getElementById('total_db').innerHTML += '<?php echo sizeof($iDBCount); ?>';
		//document.getElementById('total_society_active').innerHTML += '<?php echo sizeof($iActiveSocCount); ?>';
		//document.getElementById('total_society_inactive').innerHTML += '<?php echo sizeof($iInActiveSocCount); ?>';
		document.getElementById('db_in_use').innerHTML += '<?php echo sizeof($iDBInUse); ?>';
		document.getElementById('db_available').innerHTML += '<?php echo sizeof($iDBAvailable); ?>';
	</script>
</body>
</html>

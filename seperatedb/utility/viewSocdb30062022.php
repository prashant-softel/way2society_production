<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
	include_once("../../classes/include/check_session.php");
include_once("../../classes/initialize.class.php");
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
			$hostname = DB_HOST;
			$username = DB_USER_ROOT;
			$password = DB_PASSWORD_ROOT;
			$dbname = DB_DATABASE_ROOT;
		}
	
	$mMysqli = mysqli_connect($hostname, $username, $password, $dbname);
	$query = "SELECT db.dbname, db.locked, soc.society_id, soc.society_code, soc.society_name, soc.status, soc.timestamp, clnt.client_name,soc.Last_use_society_timestamp FROM dbname as db LEFT JOIN society as soc ON db.dbname = soc.dbname LEFT JOIN client as clnt ON soc.client_id = clnt.id";
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
	$obj_initialize = new initialize($m_dbConnRoot);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Way2Society - Update Tables</title>
<script type="text/javascript" language="javascript" src="js/jquery.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
	<h3>Society and related DB</h3>
	<div style="width: 20%;float: left;">
		<div id = 'total_db' style="color:#0000FF;font-weight:bold;">Total DB Count : </div>
		
		<div id = 'db_in_use' style="color:#F00;font-weight:bold;">DB In Use : </div>
		<div id = 'db_available' style="color:#006600;font-weight:bold;">DB Available For Import : </div>
	</div>
	<div>
		<div id='total_society_active' style="color:#009900;font-weight:bold;">Active Society Count : </div>
		<div id='total_society_inactive' style="color:#F00;font-weight:bold;">Inactive Society Count : </div>
		<div id='total_society_inactive' style="color:#F00;font-weight:bold;">&nbsp; </div>
	</div>
	<div>
		<table align='center' style="margin-top:-100px;">
			<tr align="left">
				<td valign="middle"></td>
				<td>Client Name</td>
				<td>&nbsp; : &nbsp;</td>
				<td>
					<select name="client_id" id="client_id" style="width:142px;" onChange="get_client(this.value);">
						<?php echo $mapList = $obj_initialize->filtercombobox("select distinct id,client_name as ClientName from client order by client_name asc", 0); ?>

					</select>
				</td>
			</tr>
		</table>
	</div>
	<br />
	<table border="2px" style="width:100%;text-align:center;" id="AllTable">
		<thead>
			<th>Society ID</th>
			<th>Society Code</th>
			<th>Society Name</th>
			<th>Client Name</th>
			<th>DB Name</th>
			<th>Society Status</th>
			<th>DB Status</th>
			<th>Timestamp</th>
			<th>Last Use Society TimeStamp </th>
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
						<td id="society_id"><?php echo $data[$iCnt]['society_id']; ?></td>
						<td id="society_code"><?php echo $data[$iCnt]['society_code']; ?></td>
						<td id="society_name"><?php echo $data[$iCnt]['society_name']; ?></td>
						<td id="client_name"><?php echo $data[$iCnt]['client_name']; ?></td>
						<td id="dbname"><?php echo $data[$iCnt]['dbname'];
										$iDBCount[$data[$iCnt]['dbname']] = '1'; ?></td>
						<td id="status"><?php if ($data[$iCnt]['status'] == 'Y') {
											echo '<font color="#009900"><b>ACTIVE</b></font>';
											$iActiveSocCount[$data[$iCnt]['dbname']] = '1';
										} else if ($data[$iCnt]['status'] == 'N') {
											echo '<font color="#FF0000"><b>INACTIVE</b></font>';
											$iInActiveSocCount[$data[$iCnt]['dbname']] = '1';
										}; ?></td>
						<td id="locked"><?php if ($data[$iCnt]['locked'] == '') {
											echo '<font color="#009900"><b>AVAILABLE</b></font>';
											$iDBAvailable[$data[$iCnt]['dbname']] = '1';
										} else {
											echo '<font color="#FF0000"><b>IN USE</b></font>';
											$iDBInUse[$data[$iCnt]['dbname']] = '1';
										}; ?></td>
						<td id="timestamp"><?php echo $data[$iCnt]['timestamp']; ?></td>
						<td id="Last_use_society_timestamp"><?php echo $data[$iCnt]['Last_use_society_timestamp'] ?></td>
					</tr>
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
		$(document).ready(function() {
			document.getElementById('total_db').innerHTML += '<?php echo sizeof($iDBCount); ?>';
			document.getElementById('total_society_active').innerHTML += '<?php echo sizeof($iActiveSocCount); ?>';
			document.getElementById('total_society_inactive').innerHTML += '<?php echo sizeof($iInActiveSocCount); ?>';
			document.getElementById('db_in_use').innerHTML += '<?php echo sizeof($iDBInUse); ?>';
			document.getElementById('db_available').innerHTML += '<?php echo sizeof($iDBAvailable); ?>';

		});

		function get_client(clientId) {
			var ClientId = clientId;
			$.ajax({
				url: '../../ajax/ajaxviewsocdb.php',
				type: 'post',
				data: {
					'Id': ClientId,
					'method': 'Get_client'
				},
				success: function(data) {
					var arr1 = new Array();
					var arr2 = new Array();
					var arr = new Array();
					var arr4 = new Array();
					var arr5 = new Array();
					arr1 = data.split("@@@");
					arr2 = JSON.parse("[" + arr1[1] + "]");
					var count = 0;
					var tbl = "<table border='2px' style='width:100%;text-align:center;' >";
					tbl += "<thead>";
					tbl += "<th>Society ID</th><th>Society Code</th><th>Society Name</th><th>Client Name</th><th>DB Name</th><th>Society Status</th><th>DB Status</th><th>Timestamp</th><th>Last Use Society TimeStamp </th>";
					tbl += "</thead>";
					tbl += "<tbody>";
					var iDBCount = [];
					var iActiveSocCount = [];
					var iInActiveSocCount = [];
					var iDBInUse = [];
					var iDBAvailable = [];

					if (arr2[0] == null) {
						tbl += "<tr><td colspan='9'><p style='color:#F00;font-weight:bold; text-align:center; margin-top:20px;'> &nbsp;&nbsp; Data is not avaliable</p></td></tr>";
					} else {
						for (var i = 0; i < arr2[0].length; i++) {
							tbl += "<tr><td>" + arr2[0][i].society_id + "</td>";
							tbl += "<td>" + arr2[0][i].society_code + "</td>";
							tbl += "<td>" + arr2[0][i].society_name + "</td>";
							tbl += "<td>" + arr2[0][i].client_name + "</td>";
							tbl += "<td>" + arr2[0][i].dbname + "</td>";
							iDBCount.push(arr2[0][i].dbname = '1');

							if (arr2[0][i].status == 'Y') {
								Status = '<?php echo '<font color="#009900"><b>ACTIVE</b></font>'; ?>';
								iActiveSocCount.push(arr2[0][i].dbname);

							} else {
								Status = '<?php echo '<font color="#FF0000"><b>INACTIVE</b></font>'; ?>';
								iInActiveSocCount.push(arr2[0][i].dbname);
							}

							tbl += "<td>" + Status + "</td>";

							if (arr2[0][i].locked == '') {
								Locked = '<?php echo '<font color="#009900"><b>AVAILABLE</b></font>'; ?>';
								iDBAvailable.push(arr2[0][i].dbname);
							} else {
								Locked = '<?php echo '<font color="#FF0000"><b>IN USE</b></font>'; ?>';
								iDBInUse.push(arr2[0][i].dbname);
							}

							tbl += "<td>" + Locked + "</td>";
							tbl += "<td>" + arr2[0][i].timestamp + "</td>";
							tbl += "<td>" + arr2[0][i].Last_use_society_timestamp + "</td></tr>";
						}

					}
					tbl += "</tbody>";
					tbl += "</table>";

					document.getElementById('AllTable').innerHTML = tbl;
					document.getElementById('total_db').innerHTML = "Total DB Count : " + iDBCount.length;
					document.getElementById('total_society_active').innerHTML = "Active Society Count : " + iActiveSocCount.length;
					document.getElementById('total_society_inactive').innerHTML = "Inactive Society Count : " + iInActiveSocCount.length;
					document.getElementById('db_in_use').innerHTML = "DB In Use : " + iDBInUse.length;
					document.getElementById('db_available').innerHTML = "DB Available For Import : " + iDBAvailable.length;

				}
			});
		}
	</script>
</body>
</html>

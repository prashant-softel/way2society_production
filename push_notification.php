<?php if(!isset($_SESSION)){ session_start(); }
	
	include_once("classes/include/dbop.class.php");
	include_once("classes/dbconst.class.php");

	$dbConnRoot = new dbop(true);

	$sqlSelectDeviceDetails = "SELECT d1.*, l.name, l.member_id from device_details d1 LEFT JOIN device_details d2 ON (d1.login_id = d2.login_id AND d1.id < d2.id ) JOIN login as l on l.login_id = d1.login_id WHERE d2.id IS NULL";

	$resultSelectDeviceDetails = $dbConnRoot->select($sqlSelectDeviceDetails);

 ?>
MAPID : <input type="text" id="map" style="width: 200px;" /><br/><br/>
TITLE : <input type="text" id="title" style="width: 200px;" /><br/><br/>
MESSAGE : <input type="text" id="message" style="width: 400px;" /><br/><br/>
BillPeriodID : <input type="text" id="period" style="width: 400px;" /><br/><br/>
BillType (0/1): <input type="text" id="type" value="0" style="width: 400px;" /><br/><br/>
NoticeID : <input type="text" id="notice" style="width: 400px;" /><br/><br/>

<style type="text/css">
	table, td, th {
		border: 1px solid black;
	}
</style>

<table style="width: 80%;border: 1px solid black;border-collapse: collapse;">
	<thead>
		<th>Send Notification</th>
		<th>Name</th>
		<th>Email</th>
		<th>Device ID</th>
	</thead>
	<tbody>
		
		<?php
			for($iCount = 0; $iCount < sizeof($resultSelectDeviceDetails); $iCount++)
			{
				?>
				<tr>
					<td><button onclick="SendBill('<?php echo $resultSelectDeviceDetails[$iCount]['device_id']; ?>');">Send Bill</button></td>
					<td><button onclick="SendNotice('<?php echo $resultSelectDeviceDetails[$iCount]['device_id']; ?>');">Send Notice</button></td>
					<td><?php echo $resultSelectDeviceDetails[$iCount]['name']; ?></td>
					<td><?php echo $resultSelectDeviceDetails[$iCount]['member_id']; ?></td>
					<td><span style="width:150px;word-wrap:break-word;"><?php echo $resultSelectDeviceDetails[$iCount]['device_id']; ?></span></td>
					
				</tr>
				<?php
			}
		?>
	</tbody>
</table>
<script type="text/javascript" language="javascript" src="media/js/jquery.js"></script>
<script type="text/javascript">

	function SendBill(deviceID)
	{	
		var sMapID = document.getElementById('map').value.trim();
		var sTitle = document.getElementById('title').value.trim();
		var sMessage = document.getElementById('message').value.trim();
		var sPeriod = document.getElementById('period').value.trim();
		var sType = document.getElementById('type').value.trim();

		if(sMapID.length == 0 || sTitle.length == 0 || sMessage.length == 0 || sPeriod.length == 0 || sType.length == 0)
		{
			alert("Enter Map, Title, Message, Period and Type");
			return false;
		}

		window.location.href = "push_notification_send.php?map=" + sMapID + "&title=" + sTitle + "&message=" + sMessage + "&device=" + deviceID + "&period=" + sPeriod + "&type=" + sType;
	}

	function SendNotice(deviceID)
	{
		var sMapID = document.getElementById('map').value.trim();
		var sTitle = document.getElementById('title').value.trim();
		var sMessage = document.getElementById('message').value.trim();
		var sNoticeID = document.getElementById('notice').value.trim();

		if(sMapID.length == 0 || sTitle.length == 0 || sMessage.length == 0 || sNoticeID.length == 0)
		{
			alert("Enter Map, Title, Message, NoticeID");
			return false;
		}

		window.location.href = "push_notification_send.php?map=" + sMapID + "&title=" + sTitle + "&message=" + sMessage + "&device=" + deviceID + "&notice=" + sNoticeID;
	}
</script>
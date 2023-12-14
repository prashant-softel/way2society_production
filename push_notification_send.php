<?php
	
	error_reporting(E_ALL);
	
	$URL = "http://way2society.com:8080/Way2Society/Notification?map_id=" . $_REQUEST['map'] . "&title=" . $_REQUEST['title'] . "&message=" . $_REQUEST['message'] . "&deviceID=" . $_REQUEST['device'];

	if(isset($_REQUEST['notice']))
	{
		$URL .= "&page_ref=2&page_name=ViewnoticePage&details=".$_REQUEST['notice'];	
	}
	else
	{
		$URL .= "&page_ref=1&page_name=ViewbillPage&details=".$_REQUEST['period']."/".$_REQUEST['type'];	
	}
	
	echo 'URL : ' . $URL;
	try
	{
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
		//curl_setopt($ch, CURLOPT_POSTFIELDS, "$Template");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);	

		echo 'Notification sent successfully<br/>';			
	}
	catch(Exception $ex)
	{
		echo 'Unable to send notification<br/>';
	}
	

?>

<a href="push_notification.php"> Go Back </a>
	
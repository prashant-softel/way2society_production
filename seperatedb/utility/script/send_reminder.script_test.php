<?php
	include('config_script.php');
	error_reporting(0);	
	if($_SERVER['HTTP_HOST']=="localhost")
	{		
		define("HOSTNAME", DB_HOST);
		define("USERNAME", DB_USER);
		define("PASSWORD", DB_PASSWORD); 			
	}
	else
	{		
		define("HOSTNAME", DB_HOST);
		define("USERNAME", DB_USER);
		define("PASSWORD", DB_PASSWORD); 	
	}
	
	function getResult($mMysqli, $sqlQuery)
	{
		$result = $mMysqli->query($sqlQuery);						
		if($result)
		{
			$count = 0;
			while($row = $result->fetch_array(MYSQL_ASSOC))
			{
				$data[$count] = $row;
				$count++;
			}											
		}	
		return $data;	
	}		
	
	try
	{				
		$mMysqli = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, "hostmjbt_societydb");
		if(!$mMysqli)
		{
			//echo '<br/>Connection Failed';
		}
		else
		{
			//echo '<br/>Connected';
			
			date_default_timezone_set('Asia/Kolkata');
			$data = getResult($mMysqli, "SELECT * FROM `society` WHERE `status` = 'Y' AND `send_reminder_sms` = 1 ");				
			
			for($iCount = 0; $iCount < sizeof($data); $iCount++)
			{				
				$dueDateDetails = getResult($mMysqli, "SELECT `ReminderType`, `PeriodID`, `EventDate`, `ID` FROM `remindersms` WHERE `EventReminderDate` = '".date('Y-m-d')."' AND `society_id` = '".$data[$iCount]['society_id']."'"); 				
				if(sizeof($dueDateDetails) > 0)
				{
					for($i = 0; $i < sizeof($dueDateDetails); $i++)
					{
						switch($dueDateDetails[$i]['ReminderType'])
						{
							case 1 :
								SendBillReminderSMS($data[$iCount]['dbname'], $data[$iCount]['society_id'], $data[$iCount]['society_name'], $data[$iCount]['society_code'], $dueDateDetails[$i]['PeriodID'], $dueDateDetails[$i]['EventDate']);						
								break;
								
							case 2 :
								//send event reminder sms
								break;																		
						}
						
						$cronjob_dateTime = date('Y-m-d h:i:s ');	
						$mMysqli->query("UPDATE `remindersms` SET `CronJobTimestamp`= '".$cronjob_dateTime."' WHERE `ID`='".$dueDateDetails[$i]['ID']."'");																																
					}
				}
			}
						
			mysqli_close($mMysqli);
			//echo '<br/>Connection Closed';
		}
		
	}
	catch(Exception $exp)
	{
		echo $exp;
	}
		
	function SendBillReminderSMS($dbName, $societyID, $socoetyName, $societyCode, $periodID, $dueDate)
	{	
		$Logfile=fopen("SendReminderSMS.html", "a");	
		
		$msg = "<center><b><font color='#003399' >  DATE : </b>".date('Y-m-d')."</font></center> <br /> ";
		fwrite($Logfile,$msg);		
		
		date_default_timezone_set('Asia/Kolkata');
		
		//echo '<br/><br/>Connecting DB : ' . $dbName;								
		 
		$mMysqli1 = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, $dbName);
		if(!$mMysqli1)
		{
			//echo '<br/>Connection Failed';
		}
		else
		{
			//echo '<br/>Connected';																
																													
			$smsDetails = getResult($mMysqli1, "SELECT `sms_start_text`,`sms_end_text` FROM `society` WHERE `society_id` = '".$societyID."'");																									
									
			$units = getResult($mMysqli1, "SELECT u.id, u.unit_no, mm.mob FROM `unit` AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit WHERE u.society_id = '".$societyID."'");						
			
			$msg = "<b>DBNAME : </b>". $dbName ."<br /><b> SOCIETY : </b>".$socoetyName."<br /><b> START TIME : </b>".date('Y-m-d h:i:s ')."<br /><br />";
			fwrite($Logfile,$msg);
			$dueDate = date("d-m-Y", strtotime($dueDate));
			
			for($i = 0; $i < sizeof($units); $i++)			
			{
				if($units[$i]['mob'] <> "")
				{				
					$units[$i]['mob'] = '9820040095';
					$mailBody = $smsDetails[0]['sms_start_text']. ',Reminder : Kindly pay your dues on or before ' . $dueDate . ', of Unit No. '. $units[$i]['unit_no'] .', kindly ignore if paid, '.$societyCode.' '.$smsDetails[0]['sms_end_text'];				    
					$sendSMS = "http://sms.surewingroup.info/SendSMS/sendmsg.php?uname=pavitr&pass=d$9Zx$0I&send=PAVITR&dest=" . $units[$i]['mob'] . "&msg=" . $mailBody;	
					
					//echo $sendSMS;
					//$response = file_get_contents($sendSMS);					
		
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $sendSMS);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					$response = curl_exec($ch);
					curl_close($ch);
					//echo $response;
					
					$current_dateTime = date('Y-m-d h:i:s ');																													
					$res = $mMysqli1->query("INSERT INTO `notification`(`UnitID`, `PeriodID`, `SentSMSReminderDate`) VALUES ('".$units[$i]['id']."', '".$periodID."','".$current_dateTime."')");	
					
					sleep(1);
				}
				else
				{
					$msg = "<b>** ERROR ** </b>Unit - '".$units[$i]['unit_no']."' : Invalid Mobile Number. <br /><br />";
					fwrite($Logfile,$msg);
				}
			}
			$msg = "<b> END TIME : </b>".date('Y-m-d h:i:s ')."<br /><hr />";
			fwrite($Logfile,$msg);
																						
			mysqli_close($mMysqli1);
			//echo '<br/>Connection Closed';								
		}
		return true;		
	}
	
	
?>
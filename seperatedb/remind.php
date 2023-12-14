<?php
	echo "31";
	//error_reporting(E_ALL);
	echo ( __DIR__ );
	$AppPath = preg_replace("!${_SERVER['SCRIPT_NAME']}$!", '', $_SERVER['SCRIPT_FILENAME']);
	
	//error_reporting(E_ALL);
	include_once($AppPath.'/classes/include/dbop.class.php');
	$dbConn = new dbop();
	include(( __DIR__ ).'/config_script.php');
	include_once($AppPath.'/classes/utility.class.php');
	echo "b";
	include_once($AppPath.'/classes/email_format.class.php');
	echo "d";
	
	echo "e";
	//error_reporting(0);	

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
		echo "d";	
		return $data;	
	}		
	
	try
	{				
		$mMysqli = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, "hostmjbt_societydb");
		//var_dump($mMysqli);
		echo "e";
		if(!$mMysqli)
		{
			echo '<br/>Connection Failed';
		}
		else
		{
			echo '<br/>Connected';
			
			date_default_timezone_set('Asia/Kolkata');
			$data = getResult($mMysqli, "SELECT * FROM `society` WHERE `status` = 'Y'  ");		
			//print_r($data);
			for($iCount = 0; $iCount < sizeof($data); $iCount++)
			{				
				$dueDateDetails = getResult($mMysqli, "SELECT `ReminderType`, `PeriodID`, `EventDate`, `ID` FROM `remindersms` WHERE `EventReminderDate` = '".date('Y-m-d')."' AND `society_id` = '".$data[$iCount]['society_id']."'  AND `CronJobTimestamp` = '0000-00-00 00:00:00' "); 				
				if(sizeof($dueDateDetails) > 0)
				{
					for($i = 0; $i < sizeof($dueDateDetails); $i++)
					{
						switch($dueDateDetails[$i]['ReminderType'])
						{
							case 1 :
								if($data[$iCount]['send_reminder_sms'] == 1)
								{
										echo "dabname:" .$data[$iCount]['dbname'];
										SendBillReminderSMS($data[$iCount]['dbname'], $data[$iCount]['society_id'], $data[$iCount]['society_name'], $data[$iCount]['society_code'], $dueDateDetails[$i]['PeriodID'], $dueDateDetails[$i]['EventDate']);
								}
								break;
								
							case 2 :
								//send event reminder sms
								break;		
							case 3 :
								//send fd  maturity reminder email
								SendFDMaturityReminderEmail($data[$iCount]['dbname'], $data[$iCount]['society_id'], $data[$iCount]['society_name'], $data[$iCount]['society_code']
																				, $dueDateDetails[$i]['PeriodID'], $dueDateDetails[$i]['EventDate']); 
								break;			
																								
						}
						
						$cronjob_dateTime = date('Y-m-d h:i:s ');	
						$mMysqli->query("UPDATE `remindersms` SET `CronJobTimestamp`= '".$cronjob_dateTime."' WHERE `ID`='".$dueDateDetails[$i]['ID']."'");																																
					}
				}
			}
						
			mysqli_close($mMysqli);
			echo '<br/>Connection Closed';
		}
		
	}
	catch(Exception $exp)
	{
		echo $exp;
	}
		
	function SendBillReminderSMS($dbName, $societyID, $socoetyName, $societyCode, $periodID, $dueDate)
	{	
		$objUtility  = new utility($dbConn);
		$Logfile=fopen("SendReminderSMS.html", "a");	
		
		$msg = "<center><b><font color='#003399' >  DATE : </b>".date('Y-m-d')."</font></center> <br /> ";
		fwrite($Logfile,$msg);		
		
		date_default_timezone_set('Asia/Kolkata');
		
		echo '<br/><br/>Connecting DB : ' . $dbName;								
		 
		$mMysqli1 = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, $dbName);
		if(!$mMysqli1)
		{
			echo '<br/>Connection Failed';
		}
		else
		{
			echo '<br/>Connected';																
																													
			$smsDetails = getResult($mMysqli1, "SELECT `sms_start_text`,`sms_end_text` FROM `society` WHERE `society_id` = '".$societyID."'");																									
									
			$units = getResult($mMysqli1, "SELECT u.id, u.unit_no, mm.mob,mm.alt_mob,u.unit_id FROM `unit` AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit WHERE u.society_id = '".$societyID."'");						
			
			$msg = "<b>DBNAME : </b>". $dbName ."<br /><b> SOCIETY : </b>".$socoetyName."<br /><b> START TIME : </b>".date('Y-m-d h:i:s ')."<br /><br />";
			fwrite($Logfile,$msg);
			$dueDate = date("d-m-Y", strtotime($dueDate));
			$smsText = $smsDetails[0]['sms_start_text']. ',Reminder : Kindly pay your dues on or before ' . $dueDate . ', of Unit No. unitfield, kindly ignore if paid, '.$societyCode.' '.$smsDetails[0]['sms_end_text'];		
			for($i = 0; $i < sizeof($units); $i++)			
			{
				if($units[$i]['mob'] <> "")
				{				
					$mailBody = $smsDetails[0]['sms_start_text']. ',Reminder : Kindly pay your dues on or before ' . $dueDate . ', of Unit No. '. $units[$i]['unit_no'] .', kindly ignore if paid, '.$societyCode.' '.$smsDetails[0]['sms_end_text'];				    
					///////$sendSMS = "http://sms.surewingroup.info/SendSMS/sendmsg.php?uname=pavitr&pass=d$9Zx$0I&send=PAVITR&dest=" . $units[$i]['mob'] . "&msg=" . $mailBody;	
					//new $sendSMS = "http://sms.transaction.surewingroup.info/submitsms.jsp?user=waysoc&key=7009e8caf1XX&mobile=".$units[$i]['mob']."&message=".$mailBody."&senderid=waysoc&accusage=1";														
							
					/*$sendSMS1 = str_replace(" ", '%20', $sendSMS);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $sendSMS1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					
					
					curl_setopt($ch, CURLOPT_SSLVERSION,3);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); 
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); 
					curl_setopt($ch, CURLOPT_CAINFO, getcwd() . "/cacert.pem");
					
					$response = curl_exec($ch);
					curl_close($ch);
					echo $response;*/
					
					$ResultAry =  $objUtility->SendSMS($units[$i]['mob'],$mailBody);
					
					$msg = "<b>** INFORMATION ** </b>Unit - '".$units[$i]['unit_no']."' : Message Sent['".$mailBody."']. <br /><br />";
					fwrite($Logfile,$msg);
											
	?>				
				<!--	<iframe src="<?php //echo $sendSMS; ?>" style="border:0px solid #0F0;width:100px;height:100px;"></iframe> -->
	<?php			$current_dateTime = date('Y-m-d h:i:s ');		
						//fwrite($Logfile,"b4 notification query");																											
						//$res = $mMysqli1->query("INSERT INTO `notification`(`UnitID`, `PeriodID`, `SentSMSReminderDate`,`SMSSentReport`) VALUES ('".$units[$i]['unit_id']."', '".$periodID."','".$current_dateTime."','".$ResultAry."')");	
						$res = mysqli_query($mMysqli1, "INSERT INTO `notification`(`UnitID`, `PeriodID`, `SentSMSReminderDate`,`SMSSentReport`) VALUES ('".$units[$i]['unit_id']."', '".$periodID."','".$current_dateTime."','".$ResultAry."')");
						//fwrite($Logfile,"after notification query".$res);
						fwrite($Logfile,$msg);
						if($i == 0)
						{
							//fwrite($Logfile,"b4 rsmsdetails query");
							//$res2 = $mMysqli1->query("INSERT INTO `rsmsdetails`(`PeriodID`, `sms`,`sms_type`) VALUES ('".$periodID."','".$smsText."','2')");
							$res2 = mysqli_query($mMysqli1, "INSERT INTO `rsmsdetails`(`PeriodID`, `sms`,`sms_type`,`timestamp`) VALUES ('".$periodID."','".$smsText."','2','".date('Y-m-d h:i:s ')."')");
							//fwrite($Logfile,"after rsmsdetails query".$res2);
						}
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
			echo '<br/>Connection Closed';								
		}
		return true;		
	}
	
	
	function SendFDMaturityReminderEmail($dbName, $societyID, $socoetyName, $societyCode, $periodID, $dueDate)
	{
			$Logfile=fopen("SendReminderEmail.html", "a");	
		
			$msg = "<center><b><font color='#003399' >  DATE : </b>".date('d-m-Y')."</font></center> <br /> ";
			fwrite($Logfile,$msg);		
			
			date_default_timezone_set('Asia/Kolkata');
			
			echo '<br/><br/>Connecting DB : ' . $dbName;								
			 
			$mMysqli1 = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, $dbName);
			if(!$mMysqli1)
			{
				echo '<br/>Connection Failed';
			}
			else
			{
				echo '<br/>Connected';
				$msg = "<b> START TIME : </b>".date('d-m-Y h:i:s ')."<br />";
				fwrite($Logfile,$msg);
				
				$data = getResult($mMysqli1, "SELECT * FROM `society` WHERE `status` = 'Y'");		

				
				if($data[0]['email']  <> "")
				{
					$res = getResult($mMysqli1, "SELECT * FROM `fd_master` WHERE `LedgerID` = '".$periodID."' ");		
					$bankName = getResult($mMysqli1, "SELECT `ledger_name` FROM `ledger` WHERE `id` = '".$res[0]['BankID']."' ");		
					if($res <> "")
					{
						$result =  sendFDEmail($name,$dueDate ,$data[0]['email'],$res,$bankName[0]['ledger_name'],$socoetyName);
						
						if($result == 'Success')
						{
							$msg = "<ul type='disc'><li>INFORMATION : Society Name - '".$socoetyName."[ ".$societyID." ] : Email Sent [ ".$data[0]['email']." ]. </li></ul>";
							fwrite($Logfile,$msg);		
						}
						else
						{
							$msg = "<ul type='disc'><li><font style='background-color:#F88'>ERROR : Society Name - '".$socoetyName."[ ".$societyID." ] : Email ID [ ".$data[0]['email']." ] Unable To Send Email. </font></li></ul>";
								fwrite($Logfile,$msg);		
						}
					}
				}
				else
				{
					$msg = "<ul type='disc'><li><font style='background-color:#F88'>ERROR : Society Name - '".$socoetyName."[ ".$societyID." ] : Email ID [ ".$data[0]['email']." ] Blank Or Invalid.</font></li></ul>";
					fwrite($Logfile,$msg);		
				}
				$msg = "<b> END TIME : </b>".date('d-m-Y h:i:s ')."<br /><hr />";
				fwrite($Logfile,$msg);
																							
				mysqli_close($mMysqli1);
				echo '<br/>Connection Closed';
						
			}
		
		return true;
	}
	
	
	
	
?>

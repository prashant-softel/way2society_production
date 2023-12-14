<?php
	//echo "231";

	//error_reporting(E_ALL);
	//echo ( __DIR__ );
	$AppPath = preg_replace("!${_SERVER['SCRIPT_NAME']}$!", '', $_SERVER['SCRIPT_FILENAME']);
	
	//error_reporting(E_ALL);
	include_once($AppPath.'/classes/include/dbop.class.php');

	

	include(( __DIR__ ).'/config_script.php');
	include_once($AppPath.'/classes/utility.class.php');
	//echo "b";
	include_once($AppPath.'/classes/email_format.class.php');
	include_once($AppPath.'/classes/create_poll.class.php');
	include_once($AppPath.'/classes/android.class.php');
	include_once($AppPath.'/classes/include/fetch_data.php');
	include_once($AppPath.'/classes/process_email_queue.class.php');
	//include_once($AppPath.'/classes/email.class.php');
	//echo "d";
	
	error_reporting(0);	

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
		$mMysqli = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, "hostmjbt_societydb");
		//var_dump($mMysqli);
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
				$dueDateDetails = getResult($mMysqli, "SELECT `ReminderType`, `PeriodID`, `EventDate`, `ID`,`send_type` FROM `remindersms` WHERE `EventReminderDate` = '".date('Y-m-d')."' AND `society_id` = '".$data[$iCount]['society_id']."'  AND `CronJobTimestamp` = '0000-00-00 00:00:00' "); 				
				if(sizeof($dueDateDetails) > 0)
				{
					for($i = 0; $i < sizeof($dueDateDetails); $i++)
					{

						switch($dueDateDetails[$i]['ReminderType'])
						{
							case 1 :
								if($data[$iCount]['send_reminder_sms'] == 1 && $dueDateDetails[$i]['send_type'] <> 1)
								{
										//echo "dabname:" .$data[$iCount]['dbname'];
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
							case 4 : //Send Reminder EMail 
							if($data[$iCount]['send_reminder_email'] == 1 && $dueDateDetails[$i]['send_type'] <> 0)
								{
									SendBillReminderEMAIL($data[$iCount]['dbname'], $data[$iCount]['society_id'], $data[$iCount]['society_name'], $data[$iCount]['society_code'], $dueDateDetails[$i]['PeriodID'], $dueDateDetails[$i]['EventDate']);
								}
								break;
								
							case 0:		//custom reminder
							//echo $data[$iCount]['dbname'];
								//send user defined customer sms,email,notification
							 $custom_rem = getResult($mMysqli, "SELECT * FROM `customer_reminder` WHERE `status` = 'Y' and `id`= '".$dueDateDetails[$i]['rem_id']."' ");		
							 //var_dump($custom_rem);
							 	$society_id=$data[$iCount]['society_id'];
								$cust_id=$custom_rem[0]['id'];
								$cust_title=$custom_rem[0]['title'];
								$cust_desc=$custom_rem[0]['description'];
								$cust_sms=$custom_rem[0]['SMS'];
								$cust_email=$custom_rem[0]['EMAIL'];
								$cust_mob=$custom_rem[0]['MOBILE_NOTIFY'];
								$cust_freq=$custom_rem[0]['frequency'];
								$cust_rem_date=$custom_rem[0]['reminder_date'];
								$cust_rem_before=$custom_rem[0]['reminder_before'];
								$main_id=$dueDateDetails[$i]['ID'];
								$rem_type=1;
								$status='Y';
								$cust_grpid=json_decode($custom_rem[0]['group_id']);
								for($m=0;$m<sizeof($cust_grpid);$m++)
								{
									 $targetgrp=$cust_grpid[$m];
									
										if($cust_sms==1)
										{
											//var_dump($mMysqli);

											SendUserReminderSMS($cust_title, $targetgrp,$cust_rem_before,$cust_rem_date,$cust_freq,$data[$iCount]['society_id'], $data[$iCount]['dbname'],$mMysqli);
										
										}
										if($cust_email==1)
										{
										//echo "email".$data[$iCount]['society_id'];
										 SendUserReminderEmail($cust_title,$cust_desc,$targetgrp,$cust_rem_before,$cust_rem_date,$cust_freq,$data[$iCount]['society_id'], $data[$iCount]['dbname'],$main_id,$mMysqli);
									
										}
										if($cust_mob==1)
										{
											SendMobileNotify($cust_title,$targetgrp,$cust_rem_before,$cust_rem_date,$cust_freq,$data[$iCount]['society_id'], $data[$iCount]['dbname']);
										}
									
														
								}
																								
						}
						if($data[$iCount]['send_reminder_sms'] == 1)
						{
							$cronjob_dateTime = date('Y-m-d h:i:s ');	
							if($dueDateDetails[$i]['send_type'] <> 1)
							{
								$mMysqli->query("UPDATE `remindersms` SET `CronJobTimestamp`= '".$cronjob_dateTime."' WHERE `ID`='".$dueDateDetails[$i]['ID']."'");
							}
							else if($dueDateDetails[$i]['send_type'] <> 0)
							{
								$mMysqli->query("UPDATE `remindersms` SET `CronJobTimestamp`= '".$cronjob_dateTime."' WHERE `ID`='".$dueDateDetails[$i]['ID']."'");
							}
							echo "<br>reminder sms process completed successfully.";																	
							
							$select = getResult($mMysqli, "SELECT * FROM `remindersms` WHERE `rem_status` = 'Y' and `ID`='".$dueDateDetails[$i]['ID']."'");		
							$cust_reminder_date=$select[0]['EventDate'];
							$cust_create_date=$select[0]['EventReminderDate'];
							$cust_rem_id=$select[0]['rem_id'];
							$cust_rem_type=$select[0]['rem_type'];
							$cust_rem_status=$select[0]['rem_status'];
							$cust_rem_send_before=$select[0]['rem_before'];
							
							$status="Y";
							if($cust_rem_id<>0)
							{
								$sel_user_rem=getResult($mMysqli,"SELECT * from customer_reminder where id='".$cust_rem_id."' and status='".$status."' and reminder_before='".$cust_rem_send_before."'");
								$frequency=$sel_user_rem[0]['frequency'];
								if($frequency==2)
								{
									$date=date_create($cust_reminder_date);
									date_add($date,date_interval_create_from_date_string('1 days'));
									$rem_date=date_format($date,"Y-m-d");
									
									
									$cust_send_before=date_create($cust_rem_send_before);
									date_add($cust_send_before,date_interval_create_from_date_string('1 days'));
									$cust_day_before=date_format($cust_send_before,"Y-m-d");
									
									$res = mysqli_query($mMysqli,"insert into `remindersms`(society_id,rem_id,rem_type,EventDate,rem_status,rem_before,EventReminderDate) values('".$data[$iCount]['society_id']."','".$cust_rem_id."','".$cust_rem_type."','".$rem_date."','".$cust_rem_status."','".$cust_day_before."','".getDBFormatDate(date('Y-m-d'))."')");
									echo "Inserted Sucessfully";
									
									
									$resk=getResult($mMysqli, "SELECT * FROM `remindersms` WHERE `rem_status` = 'Y' and EventDate='".$rem_date."' and rem_before='".$cust_day_before."' and EventReminderDate='".getDBFormatDate(date('Y-m-d'))."' and CronJobTimestamp='0000-00-00 00:00:00'");
									echo $res_id=$resk[0]['rem_id'];
									
									$mMysqli->query("UPDATE `customer_reminder` SET `reminder_date`= '".$rem_date."', `reminder_before`='".$cust_day_before."' WHERE `id`='".$res_id."'");
									echo "Update";
									
									
									echo "<br>cust_day_before:".$cust_day_before;
									echo "<br>rem_date:".$rem_date;
									
									
										
								}
								else if($frequency==3)
								{
									$date=date_create($cust_reminder_date);
									date_add($date,date_interval_create_from_date_string('7 days'));
									$rem_date=date_format($date,"Y-m-d");
									echo "Rem date:" .$rem_date;
									
									$cust_send_before=date_create($cust_rem_send_before);
									date_add($cust_send_before,date_interval_create_from_date_string('7 days'));
									echo "<br>cust_day_before:".$cust_day_before=date_format($cust_send_before,"Y-m-d");
									
									$res = mysqli_query($mMysqli,"insert into `remindersms`(society_id,rem_id,rem_type,EventDate,rem_status,rem_before,EventReminderDate) values('".$data[$iCount]['society_id']."','".$cust_rem_id."','".$cust_rem_type."','".$rem_date."','".$cust_rem_status."','".$cust_day_before."','".getDBFormatDate(date('Y-m-d'))."')");
									echo "Inserted Sucessfully";
									
									
									$resk=getResult($mMysqli, "SELECT * FROM `remindersms` WHERE `rem_status` = 'Y' and EventDate='".$rem_date."' and rem_before='".$cust_day_before."' and EventReminderDate='".getDBFormatDate(date('Y-m-d'))."' and CronJobTimestamp='0000-00-00 00:00:00'");
									echo $res_id=$resk[0]['rem_id'];
									
									$mMysqli->query("UPDATE `customer_reminder` SET `reminder_date`= '".$rem_date."', `reminder_before`='".$cust_day_before."' WHERE `id`='".$res_id."'");
									echo "Update";
									
									
									
									echo "<br>cust_day_before:".$cust_day_before;
									echo "<br>rem_date:".$rem_date;
									
								}
								else if($frequency==4)
								{
									$date=date_create($cust_reminder_date);
									date_add($date,date_interval_create_from_date_string('1 month'));
									$rem_date=date_format($date,"Y-m-d");
									echo "Rem date:" .$rem_date;
									
									$cust_send_before=date_create($cust_rem_send_before);
									date_add($cust_send_before,date_interval_create_from_date_string('1 month'));
									$cust_day_before=date_format($cust_send_before,"Y-m-d");
									
									$res = mysqli_query($mMysqli,"insert into `remindersms`(society_id,rem_id,rem_type,EventDate,rem_status,rem_before,EventReminderDate) values('".$data[$iCount]['society_id']."','".$cust_rem_id."','".$cust_rem_type."','".$rem_date."','".$cust_rem_status."','".$cust_day_before."','".getDBFormatDate(date('Y-m-d'))."')");
									echo "Inserted Sucessfully";
									
									$resk=getResult($mMysqli, "SELECT * FROM `remindersms` WHERE `rem_status` = 'Y' and EventDate='".$rem_date."' and rem_before='".$cust_day_before."' and EventReminderDate='".getDBFormatDate(date('Y-m-d'))."' and CronJobTimestamp='0000-00-00 00:00:00'");
									echo $res_id=$resk[0]['rem_id'];
									
									$mMysqli->query("UPDATE `customer_reminder` SET `reminder_date`= '".$rem_date."', `reminder_before`='".$cust_day_before."' WHERE `id`='".$res_id."'");
									echo "Update";
									
									echo "<br>cust_day_before:".$cust_day_before;
									echo "<br>rem_date:".$rem_date;
								}
								else if($frequency==5)
								{
									$date=date_create($cust_reminder_date);
									date_add($date,date_interval_create_from_date_string('3 month'));
									$rem_date=date_format($date,"Y-m-d");
									echo "Rem date:" .$rem_date;
									
									$cust_send_before=date_create($cust_rem_send_before);
									date_add($cust_send_before,date_interval_create_from_date_string('3 month'));
									$cust_day_before=date_format($cust_send_before,"Y-m-d");
									
									$res = mysqli_query($mMysqli,"insert into `remindersms`(society_id,rem_id,rem_type,EventDate,rem_status,rem_before,EventReminderDate) values('".$data[$iCount]['society_id']."','".$cust_rem_id."','".$cust_rem_type."','".$rem_date."','".$cust_rem_status."','".$cust_day_before."','".getDBFormatDate(date('Y-m-d'))."')");
									echo "Inserted Sucessfully";
									
									
									$resk=getResult($mMysqli, "SELECT * FROM `remindersms` WHERE `rem_status` = 'Y' and EventDate='".$rem_date."' and rem_before='".$cust_day_before."' and EventReminderDate='".getDBFormatDate(date('Y-m-d'))."' and CronJobTimestamp='0000-00-00 00:00:00'");
									echo $res_id=$resk[0]['rem_id'];
									
									$mMysqli->query("UPDATE `customer_reminder` SET `reminder_date`= '".$rem_date."', `reminder_before`='".$cust_day_before."' WHERE `id`='".$res_id."'");
									echo "Update";
									
									
									echo "<br>cust_day_before:".$cust_day_before;
									echo "<br>rem_date:".$rem_date;
										

								}
								else if($frequency==6)
								{
									$date=date_create($cust_reminder_date);
									date_add($date,date_interval_create_from_date_string('6 month'));
									$rem_date=date_format($date,"Y-m-d");
									echo "Rem date:" .$rem_date;
									
									$cust_send_before=date_create($cust_rem_send_before);
									date_add($cust_send_before,date_interval_create_from_date_string('6 month'));
									$cust_day_before=date_format($cust_send_before,"Y-m-d");
									
									$res = mysqli_query($mMysqli,"insert into `remindersms`(society_id,rem_id,rem_type,EventDate,rem_status,rem_before,EventReminderDate) values('".$data[$iCount]['society_id']."','".$cust_rem_id."','".$cust_rem_type."','".$rem_date."','".$cust_rem_status."','".$cust_day_before."','".getDBFormatDate(date('Y-m-d'))."')");
								echo "Inserted Sucessfully";
								
								
									$resk=getResult($mMysqli, "SELECT * FROM `remindersms` WHERE `rem_status` = 'Y' and EventDate='".$rem_date."' and rem_before='".$cust_day_before."' and EventReminderDate='".getDBFormatDate(date('Y-m-d'))."' and CronJobTimestamp='0000-00-00 00:00:00'");
									echo $res_id=$resk[0]['rem_id'];
									
									$mMysqli->query("UPDATE `customer_reminder` SET `reminder_date`= '".$rem_date."', `reminder_before`='".$cust_day_before."' WHERE `id`='".$res_id."'");
									echo "Update";
									
								
								echo "<br>cust_day_before:".$cust_day_before;
									echo "<br>rem_date:".$rem_date;

								}
								else if($frequency==7)
								{
									$date=date_create($cust_reminder_date);
									date_add($date,date_interval_create_from_date_string('1 year'));
									$rem_date=date_format($date,"Y-m-d");
									echo "Rem date:" .$rem_date;
									
									$cust_send_before=date_create($cust_rem_send_before);
									date_add($cust_send_before,date_interval_create_from_date_string('1 year'));
									$cust_day_before=date_format($cust_send_before,"Y-m-d");
									
									$res = mysqli_query($mMysqli,"insert into `remindersms`(society_id,rem_id,rem_type,EventDate,rem_status,rem_before,EventReminderDate) values('".$data[$iCount]['society_id']."','".$cust_rem_id."','".$cust_rem_type."','".$rem_date."','".$cust_rem_status."','".$cust_day_before."','".getDBFormatDate(date('Y-m-d'))."')");
									echo "Inserted Sucessfully";
									
									
									$resk=getResult($mMysqli, "SELECT * FROM `remindersms` WHERE `rem_status` = 'Y' and EventDate='".$rem_date."' and rem_before='".$cust_day_before."' and EventReminderDate='".getDBFormatDate(date('Y-m-d'))."' and CronJobTimestamp='0000-00-00 00:00:00'");
									echo $res_id=$resk[0]['rem_id'];
									
									$mMysqli->query("UPDATE `customer_reminder` SET `reminder_date`= '".$rem_date."', `reminder_before`='".$cust_day_before."' WHERE `id`='".$res_id."'");
									echo "Update";
									
									
									echo "<br>cust_day_before:".$cust_day_before;
									echo "<br>rem_date:".$rem_date;

								
								}
								
							}
																							
						}															
					}
				}
				else
				{ 
					echo "inside else";
				}

			}
			
			SendPollReminderEmail($mMysqli);
			SendTenantReminderEmail($mMysqli);
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
		$dbConn = new dbop(false, $dbName);
		$dbConnRoot = new dbop(true);
		$objUtility  = new utility($dbConn,$dbConnRoot);
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
			$smsText = $smsDetails[0]['sms_start_text']. ',Reminder : Kindly pay your dues on or before ' . $dueDate . ', of Unit No. unitfield, kindly ignore if paid,  '.$smsDetails[0]['sms_end_text'];		
			for($i = 0; $i < sizeof($units); $i++)			
			{
				
				if(($units[$i]['mob'] <> "") && ($units[$i]['mob'] <> "0"))
				{
					$AmountDue= $objUtility->getDueAmountTillDate($units[$i]['unit_id']);
					if($AmountDue > 0 )
					{
						echo " <br>mobile no:".$units[$i]['mob'] . " amount due:".$AmountDue;
					 
						$mailBody = $smsDetails[0]['sms_start_text']. ',Reminder : Kindly pay your dues on or before ' . $dueDate . ', of Unit No. '. $units[$i]['unit_no'] .', kindly ignore if paid,  '.$smsDetails[0]['sms_end_text'];				    
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
						//echo "text:".$mailBody;
						$mMysqliRoot = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, "hostmjbt_societydb");
						$queryclient = "SELECT `client_id` FROM  `society` WHERE  `dbname` ='".$dbName."' ";
						$resultclient = getResult($mMysqliRoot,$queryclient);
						$client_id = 0;
						$template_type ='0';
						if(sizeof($resultclient) > 0)
						{
							$client_id = $resultclient[0]['client_id'];
						}
						
						$ResultAry =  $objUtility->SendSMS($units[$i]['mob'],$mailBody,$client_id,$societyID,$template_type);
						//die();

						
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
				}
				else
				{
					$msg = "<b>** ERROR ** </b>Unit - '".$units[$i]['unit_no']."' : Invalid Mobile Number " . $units[$i]['mob'] .". <br /><br />";
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
		 function SendUserReminderSMS($cust_title, $target_grpid,$cust_rem_date,$cust_rem_before,$frequency,$societyid, $dbname,$mMysqli)
 	{
		//echo '<br> Mem_id:'.$mem_id.'</br>';

		//echo '<br>Unit_id:'.$memunit.'</br>';
		//echo '<br>Number:'.$memnum.'</br>';
		$Logfile=fopen("SendReminderSMS.html", "a");	
		$msg = "<center><b><font color='#003399' >  DATE : </b>".date('Y-m-d')."</font></center> <br /> ";
		fwrite($Logfile,$msg);		
		date_default_timezone_set('Asia/Kolkata');
		$mMysqli1 = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, $dbname);
			if(!$mMysqli1)
			{
				echo '<br/>Connection Failed';
			}
			else
			{
				echo "Connected";
				$dbConn = new dbop(false, $dbname);
				$dbConnRoot = new dbop(true);
				$obj_utility  = new utility($dbConn,$dbConnRoot);
				$objFetchData = new FetchData($dbConn);

				$status="Y";
				$mem_grp=getResult($mMysqli1,"SELECT MemberId FROM membergroup_members WHERE GroupId='".$target_grpid."' and Status='".$status."'");			var_dump($mem_grp);
				for($n=0;$n<sizeof($mem_grp);$n++)
				{
						$mem_id=$mem_grp[$n]['MemberId'];
	   					$smsDetails = getResult($mMysqli1,"SELECT `sms_start_text`,`sms_end_text` FROM `society` WHERE `society_id` ='".$societyid."'");
						$units=getResult($mMysqli1,"SELECT u.id, u.unit_no, mm.mob,mm.alt_mob, mm.member_id,u.unit_id FROM `unit` AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit WHERE mm.member_id ='".$mem_id."'");
						$msg = "<b>DBNAME : </b>". $dbname ."<br /><b> SOCIETY : </b>".$smsDetails[0]['society_name']."<br /><b> START TIME : </b>".date('Y-m-d h:i:s ')."<br /><br />";
						fwrite($Logfile,$msg);
						$dueDate = date("d-m-Y", strtotime($date));
						$unitno=$units[0]['unit_no'];
						 $memnum=$units[0]['mob'];
			
						if($memnum <> '' && $memnum <> 0)
						{
				//echo  "<br>Mobile No.:".$memnum;
							$mailBody =' Reminder ' .$cust_title.' on ' . $cust_rem_date . ' of Unit No. '. $unitno .' Thanks and Regards';	
							//$mailBody =' Reminder &#44; &#xA;' .$cust_title.' on ' . $dueDate . ' of Unit No. '. $unitno .'&#xA;Thanks &amp; Regards&#46;  ';		
	

				//echo "<br>dbname:".$dbname;
				
							$clientDetails = getResult($mMysqli,"SELECT `client_id` FROM  `society` WHERE  `dbname` ='".$dbname."' ");
				
							if(sizeof($clientDetails) > 0)
							{
								  $clientID = $clientDetails[0]['client_id'];
						//echo "<br>Client_id:" .$clientID;

							}
			
							$response =  $obj_utility->SendSMS($memnum, $mailBody, $clientID,$societyid);
					
					
							$msg = "<b>** INFORMATION ** </b>Unit - '".$unitno."' : Message Sent['".$mailBody."']. <br /><br />";
							fwrite($Logfile,$msg);
					
							$current_dateTime = date('Y-m-d h:i:s ');
							    
					}
					else
					{
							$msg = "<b>** ERROR ** </b>Unit - '".$unitno."' : Invalid Mobile Number. <br /><br />";
							fwrite($Logfile,$msg);
					}
				}
			
				$msg = "<b> END TIME : </b>".date('Y-m-d h:i:s ')."<br /><hr />";
				fwrite($Logfile,$msg);
			}
				return true;

	}
	
	
	
	
	function SendMobileNotify($cust_title,$target_grpid,$cust_rem_date,$cust_rem_before,$frequency,$societyid, $dbname)
	{
		$Logfile=fopen("SendUserReminderNotify.html", "a");	
		$msg = "<center><b><font color='#003399' >  DATE : </b>".date('Y-m-d')."</font></center> <br /> ";
		fwrite($Logfile,$msg);		
		date_default_timezone_set('Asia/Kolkata');
		$mMysqli1 = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, $dbname);
			if(!$mMysqli1)
			{
				echo '<br/>Connection Failed';
			}
			else
			{
				echo "Connected";
				$dbConn = new dbop(false, $dbName);
				$dbConnRoot = new dbop(true);
				$objUtility  = new utility($dbConn,$dbConnRoot);
				$objFetchData = new FetchData($dbConn);
				$status="Y";
				$mem_grp=getResult($mMysqli1,"SELECT MemberId FROM membergroup_members WHERE GroupId='".$target_grpid."' and Status='".$status."'");			var_dump($mem_grp);
				for($n=0;$n<sizeof($mem_grp);$n++)
				{
						$mem_id=$mem_grp[$n]['MemberId'];
				

				$sql=getResult($mMysqli1,"select mem_other_family_id from mem_other_family where member_id='".$mem_id."'");
				for($i=0;$i<sizeof($sql);$i++)
				{
					$mem_other_family_id=$sql[$i]['mem_other_family_id'];
		

					$RemTitle='Society Reminder';
					$RemMessage=$cust_title;
					$Remdate=$cust_rem_date;
					$emailIDList = $objFetchData->GetEmailIDToSendNotification(0);
					for($k=0;$k<sizeof($emailIDList);$k++)
					{
						if($mem_other_family_id==$emailIDList[$k]['id']){

						if($emailIDList[$k]['to_email'] <> "")
						{
							$unitID = $emailIDList[$k]['unit'];
							$objAndroid = new android($emailIDList[$k]['to_email'], $societyid, $unitID);
							$sendMobile=$objAndroid->sendCustRemNotification($RemTitle,$RemMessage,$Remdate,$mem_other_family_id);
						}
				
					}
				}
		}
	}
	}
	$msg = "<b> END TIME : </b>".date('Y-m-d h:i:s ')."<br /><hr />";
		fwrite($Logfile,$msg);
		return true;
		
	}
	
	
	
	
	
	 function SendUserReminderEmail($cust_title,$cust_desc,$target_grpid,$cust_rem_date,$cust_rem_before,$frequency,$societyid, $dbname,$main_id,$mMysqli)
	{
			$dbConn = new dbop(false, $dbname);
			$dbConnRoot = new dbop(true);
			$obj_utility  = new utility($dbConn,$dbConnRoot);
			$objFetchData = new FetchData($dbConn);

			$Logfile=fopen("SendReminderEmail.html", "a");	
		
			$msg = "<center><b><font color='#003399' >  DATE : </b>".date('d-m-Y')."</font></center> <br /> ";
			fwrite($Logfile,$msg);		
			
			date_default_timezone_set('Asia/Kolkata');
			
			echo '<br/><br/>Connecting DB : ' . $dbname;								
			 
			$mMysqli1 = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, $dbname);
			if(!$mMysqli1)
			{
				echo '<br/>Connection Failed';
			}
			else
			{
				echo '<br/>Connected dsdfs';
				$msg = "<b> START TIME : </b>".date('d-m-Y h:i:s ')."<br />";
				fwrite($Logfile,$msg);
				if($societyid <> "")
				{
					$objFetchData->GetSocietyDetails($societyid);	
				}
				else
				{
					$objFetchData->GetSocietyDetails($_SESSION['society_id']);
				}
				$status="Y";
				$mem_grp=getResult($mMysqli1,"SELECT MemberId FROM membergroup_members WHERE GroupId='".$target_grpid."' and Status='".$status."'");			var_dump($mem_grp);
				for($n=0;$n<sizeof($mem_grp);$n++)
				{
						$mem_id=$mem_grp[$n]['MemberId'];
						$mailsubject=$cust_title;
						$check1=$obj_utility->GetEmailHeader();
						$check2=$obj_utility->GetEmailFooter();
						$mailBody=$check1."<br>".$cust_desc." on Date: '".$cust_rem_date."'<br>".$check2;
						$display=array();
						$bccUnitsArray=array();
						$units=getResult($mMysqli1,"SELECT u.mem_other_family_id, u.member_id, u.other_email,mm.member_id, mm.unit FROM `mem_other_family` AS u JOIN `member_main` AS mm ON u.member_id = mm.member_id WHERE mm.member_id ='".$mem_id."'");
						for($i=0;$i<sizeof($units);$i++)
						{
							$mem_other_family_id=$units[$i]['mem_other_family_id'];
							$memunit=$units[$i]['unit'];
							$emailIDList = $objFetchData->GetEmailIDToSendNotification(0);
							for($k=0;$k<sizeof($emailIDList);$k++)
							{
								if($mem_other_family_id==$emailIDList[$k]['id'])
								{
				
									if($emailIDList[$k]['to_email'] <> "")
									{
										$display[$emailIDList[$k]['to_email']] = $emailIDList[$k]['to_name'];
										$bccUnitsArray[$k] = $emailIDList[$k]['unit'];
									}

								}

							}
						if(sizeof($display)==0)
						{
								echo "<br>Email ID Missing</br>";
								//return;
								exit();

						}
			
						$societyEmail="";
					if($objFetchData->objSocietyDetails->sSocietyEmail <> "")
					{
						 $societyEmail=$objFetchData->objSocietyDetails->sSocietyEmail;
					}
					else
					{
						$societyEmail="techsupport@way2society.com";
					}
					try
					{
						echo "1";
						$EmailIdToUse=$obj_utility->GetEmailIDToUse(true,1,"",$memunit,0,$dbname,$societyid,$mem_other_family_id,$bccUnitsArray);
						echo "2";
						echo "<br>EMailID:" .$EMailID = $EmailIdToUse['email'];
						echo "<br>Password:" . $Password = $EmailIdToUse['password'];
						 

						if($EmailIdToUse['status']==0)
						{
							//$transport = Swift_SmtpTransport::newInstance('103.50.162.146',25)
							//$transport = Swift_SmtpTransport::newInstance('smtp.gmail.com',25)
								//->setUsername($EMailID)
								//->setSourceIp('0.0.0.0')
								//->setPassword($Password) ; 
							$AWS_Config = CommanEmailConfig();
				 			$transport = Swift_SmtpTransport::newInstance($AWS_Config[0]['Endpoint'],$AWS_Config[0]['Port'] , $AWS_Config[0]['Security'])
				 					 ->setUsername($AWS_Config[0]['Username'])
				  					 ->setPassword($AWS_Config[0]['Password']);		
						
						    $message = Swift_Message::newInstance();
				
							if($objFetchData->objSocietyDetails->sSocietyEmail <> "")
							{
								$message->setTo(array(
					  		    $societyEmail => $societyName
								));
							}
				//print_r($display);
								$message->setBcc($display);
							    $message->setReplyTo(array(
				   				$societyEmail => $societyName
							)); 
							$message->setSubject($mailsubject);
							$message->setBody($mailBody);
							$message->setFrom("no-reply@way2society.com", $objFetchData->objSocietyDetails->sSocietyName);					
							$message->setContentType("text/html");		
					
							$mailer=Swift_Mailer::newInstance($transport);
							$result=$mailer->send($message);
							$result=1;
							if($result==1)
							{
								echo "<br>sucess</br>";
								//echo "Id:".$main_id;
								$cronjob_dateTime = date('Y-m-d h:i:s ');	
								$mMysqli->query("UPDATE `remindersms` SET `CronJobTimestamp`= '".$cronjob_dateTime."' WHERE `ID`='".$main_id."'");
								//echo "UPDATE `remindersms` SET `CronJobTimestamp`= '".$cronjob_dateTime."' WHERE `ID`='".$main_id."'";
								echo "<br>reminder email process completed successfully.";
						
							
							$select = getResult($mMysqli, "SELECT * FROM `remindersms` WHERE `rem_status` = 'Y' and `ID`='".$main_id."'");		
							$cust_reminder_date=$select[0]['EventDate'];
							$cust_create_date=$select[0]['EventReminderDate'];
							$cust_rem_id=$select[0]['rem_id'];
							$cust_rem_type=$select[0]['rem_type'];
							$cust_rem_status=$select[0]['rem_status'];
							$cust_rem_send_before=$select[0]['rem_before'];
							
							$status="Y";
							if($cust_rem_id<>0)
							{
								$sel_user_rem=getResult($mMysqli,"SELECT * from customer_reminder where id='".$cust_rem_id."' and status='".$status."' and reminder_before='".$cust_rem_send_before."'");
								$frequency=$sel_user_rem[0]['frequency'];
								if($frequency==2)
								{
									$date=date_create($cust_reminder_date);
									date_add($date,date_interval_create_from_date_string('1 days'));
									$rem_date=date_format($date,"Y-m-d");
									
									
									$cust_send_before=date_create($cust_rem_send_before);
									date_add($cust_send_before,date_interval_create_from_date_string('1 days'));
									$cust_day_before=date_format($cust_send_before,"Y-m-d");
									
									$res = mysqli_query($mMysqli,"insert into `remindersms`(society_id,rem_id,rem_type,EventDate,rem_status,rem_before,EventReminderDate) values('".$societyid."','".$cust_rem_id."','".$cust_rem_type."','".$rem_date."','".$cust_rem_status."','".$cust_day_before."','".getDBFormatDate(date('Y-m-d'))."')");
									echo "Inserted Sucessfully";
									
									
									$resk=getResult($mMysqli, "SELECT * FROM `remindersms` WHERE `rem_status` = 'Y' and EventDate='".$rem_date."' and rem_before='".$cust_day_before."' and EventReminderDate='".getDBFormatDate(date('Y-m-d'))."' and CronJobTimestamp='0000-00-00 00:00:00'");
									echo $res_id=$resk[0]['rem_id'];
									
									$mMysqli->query("UPDATE `customer_reminder` SET `reminder_date`= '".$rem_date."', `reminder_before`='".$cust_day_before."' WHERE `id`='".$res_id."'");
									echo "Update";
									
									
									echo "<br>cust_day_before:".$cust_day_before;
									echo "<br>rem_date:".$rem_date;
									
									
										
								}
								else if($frequency==3)
								{
									$date=date_create($cust_reminder_date);
									date_add($date,date_interval_create_from_date_string('7 days'));
									$rem_date=date_format($date,"Y-m-d");
									echo "Rem date:" .$rem_date;
									
									$cust_send_before=date_create($cust_rem_send_before);
									date_add($cust_send_before,date_interval_create_from_date_string('7 days'));
									echo "<br>cust_day_before:".$cust_day_before=date_format($cust_send_before,"Y-m-d");
									
									$res = mysqli_query($mMysqli,"insert into `remindersms`(society_id,rem_id,rem_type,EventDate,rem_status,rem_before,EventReminderDate) values('".$societyid."','".$cust_rem_id."','".$cust_rem_type."','".$rem_date."','".$cust_rem_status."','".$cust_day_before."','".getDBFormatDate(date('Y-m-d'))."')");
									echo "Inserted Sucessfully";
									
									
									$resk=getResult($mMysqli, "SELECT * FROM `remindersms` WHERE `rem_status` = 'Y' and EventDate='".$rem_date."' and rem_before='".$cust_day_before."' and EventReminderDate='".getDBFormatDate(date('Y-m-d'))."' and CronJobTimestamp='0000-00-00 00:00:00'");
									echo $res_id=$resk[0]['rem_id'];
									
									$mMysqli->query("UPDATE `customer_reminder` SET `reminder_date`= '".$rem_date."', `reminder_before`='".$cust_day_before."' WHERE `id`='".$res_id."'");
									echo "Update";
									
									
									
									echo "<br>cust_day_before:".$cust_day_before;
									echo "<br>rem_date:".$rem_date;
									
								}
								else if($frequency==4)
								{
									$date=date_create($cust_reminder_date);
									date_add($date,date_interval_create_from_date_string('1 month'));
									$rem_date=date_format($date,"Y-m-d");
									echo "Rem date:" .$rem_date;
									
									$cust_send_before=date_create($cust_rem_send_before);
									date_add($cust_send_before,date_interval_create_from_date_string('1 month'));
									$cust_day_before=date_format($cust_send_before,"Y-m-d");
									
									$res = mysqli_query($mMysqli,"insert into `remindersms`(society_id,rem_id,rem_type,EventDate,rem_status,rem_before,EventReminderDate) values('".$societyid."','".$cust_rem_id."','".$cust_rem_type."','".$rem_date."','".$cust_rem_status."','".$cust_day_before."','".getDBFormatDate(date('Y-m-d'))."')");
									echo "Inserted Sucessfully";
									
									$resk=getResult($mMysqli, "SELECT * FROM `remindersms` WHERE `rem_status` = 'Y' and EventDate='".$rem_date."' and rem_before='".$cust_day_before."' and EventReminderDate='".getDBFormatDate(date('Y-m-d'))."' and CronJobTimestamp='0000-00-00 00:00:00'");
									echo $res_id=$resk[0]['rem_id'];
									
									$mMysqli->query("UPDATE `customer_reminder` SET `reminder_date`= '".$rem_date."', `reminder_before`='".$cust_day_before."' WHERE `id`='".$res_id."'");
									echo "Update";
									
									echo "<br>cust_day_before:".$cust_day_before;
									echo "<br>rem_date:".$rem_date;
								}
								else if($frequency==5)
								{
									$date=date_create($cust_reminder_date);
									date_add($date,date_interval_create_from_date_string('3 month'));
									$rem_date=date_format($date,"Y-m-d");
									echo "Rem date:" .$rem_date;
									
									$cust_send_before=date_create($cust_rem_send_before);
									date_add($cust_send_before,date_interval_create_from_date_string('3 month'));
									$cust_day_before=date_format($cust_send_before,"Y-m-d");
									
									$res = mysqli_query($mMysqli,"insert into `remindersms`(society_id,rem_id,rem_type,EventDate,rem_status,rem_before,EventReminderDate) values('".$societyid."','".$cust_rem_id."','".$cust_rem_type."','".$rem_date."','".$cust_rem_status."','".$cust_day_before."','".getDBFormatDate(date('Y-m-d'))."')");
									echo "Inserted Sucessfully";
									
									
									$resk=getResult($mMysqli, "SELECT * FROM `remindersms` WHERE `rem_status` = 'Y' and EventDate='".$rem_date."' and rem_before='".$cust_day_before."' and EventReminderDate='".getDBFormatDate(date('Y-m-d'))."' and CronJobTimestamp='0000-00-00 00:00:00'");
									echo $res_id=$resk[0]['rem_id'];
									
									$mMysqli->query("UPDATE `customer_reminder` SET `reminder_date`= '".$rem_date."', `reminder_before`='".$cust_day_before."' WHERE `id`='".$res_id."'");
									echo "Update";
									
									
									echo "<br>cust_day_before:".$cust_day_before;
									echo "<br>rem_date:".$rem_date;
										

								}
								else if($frequency==6)
								{
									$date=date_create($cust_reminder_date);
									date_add($date,date_interval_create_from_date_string('6 month'));
									$rem_date=date_format($date,"Y-m-d");
									echo "Rem date:" .$rem_date;
									
									$cust_send_before=date_create($cust_rem_send_before);
									date_add($cust_send_before,date_interval_create_from_date_string('6 month'));
									$cust_day_before=date_format($cust_send_before,"Y-m-d");
									
									$res = mysqli_query($mMysqli,"insert into `remindersms`(society_id,rem_id,rem_type,EventDate,rem_status,rem_before,EventReminderDate) values('".$societyid."','".$cust_rem_id."','".$cust_rem_type."','".$rem_date."','".$cust_rem_status."','".$cust_day_before."','".getDBFormatDate(date('Y-m-d'))."')");
								echo "Inserted Sucessfully";
								
								
									$resk=getResult($mMysqli, "SELECT * FROM `remindersms` WHERE `rem_status` = 'Y' and EventDate='".$rem_date."' and rem_before='".$cust_day_before."' and EventReminderDate='".getDBFormatDate(date('Y-m-d'))."' and CronJobTimestamp='0000-00-00 00:00:00'");
									echo $res_id=$resk[0]['rem_id'];
									
									$mMysqli->query("UPDATE `customer_reminder` SET `reminder_date`= '".$rem_date."', `reminder_before`='".$cust_day_before."' WHERE `id`='".$res_id."'");
									echo "Update";
									
								
								echo "<br>cust_day_before:".$cust_day_before;
									echo "<br>rem_date:".$rem_date;

								}
								else if($frequency==7)
								{
									$date=date_create($cust_reminder_date);
									date_add($date,date_interval_create_from_date_string('1 year'));
									$rem_date=date_format($date,"Y-m-d");
									echo "Rem date:" .$rem_date;
									
									$cust_send_before=date_create($cust_rem_send_before);
									date_add($cust_send_before,date_interval_create_from_date_string('1 year'));
									$cust_day_before=date_format($cust_send_before,"Y-m-d");
									
									$res = mysqli_query($mMysqli,"insert into `remindersms`(society_id,rem_id,rem_type,EventDate,rem_status,rem_before,EventReminderDate) values('".$societyid."','".$cust_rem_id."','".$cust_rem_type."','".$rem_date."','".$cust_rem_status."','".$cust_day_before."','".getDBFormatDate(date('Y-m-d'))."')");
									echo "Inserted Sucessfully";
									
									
									$resk=getResult($mMysqli, "SELECT * FROM `remindersms` WHERE `rem_status` = 'Y' and EventDate='".$rem_date."' and rem_before='".$cust_day_before."' and EventReminderDate='".getDBFormatDate(date('Y-m-d'))."' and CronJobTimestamp='0000-00-00 00:00:00'");
									echo $res_id=$resk[0]['rem_id'];
									
									$mMysqli->query("UPDATE `customer_reminder` SET `reminder_date`= '".$rem_date."', `reminder_before`='".$cust_day_before."' WHERE `id`='".$res_id."'");
									echo "Update";
									
									
									echo "<br>cust_day_before:".$cust_day_before;
									echo "<br>rem_date:".$rem_date;

								
								}
								
							}
								
							}
							else
							{
								echo "<br>Failed</br>";
							}
				}
					else
		  			{
							echo '<br>'.$EMailIDToUse['msg'].'</br>';
		  			}
			}
			catch(Exception $exp)
			{
				echo "<br>Error occurred in sending email.</br>";
				echo "<br>".$exp;
			}
			

			}
			}/*echo '<br/>Connected';
				echo "".$mem_id;
				$msg = "<b> START TIME : </b>".date('d-m-Y h:i:s ')."<br />";
				fwrite($Logfile,$msg);
				
				if($societyid <> "")
				{
					$objFetchData->GetSocietyDetails($societyid);	
				}
				else
				{
					$objFetchData->GetSocietyDetails($_SESSION['society_id']);
				}
				$mailsubject=$cust_title;
				$check1=$obj_utility->GetEmailHeader();
				$check2=$obj_utility->GetEmailFooter();
				echo $mailBody=$check1."<br>".$cust_desc."</br>".$check2;
				$display=array();
				$bccUnitsArray=array();
				$units=getResult($mMysqli1,"SELECT u.mem_other_family_id, u.member_id, u.other_email,mm.member_id, mm.unit FROM `mem_other_family` AS u JOIN `member_main` AS mm ON u.member_id = mm.member_id WHERE mm.member_id ='".$mem_id."'");
				for($i=0;$i<sizeof($units);$i++)
				{
					$mem_other_family_id=$units[$i]['mem_other_family_id'];
					$memunit=$units[$i]['unit'];
					$emailIDList = $objFetchData->GetEmailIDToSendNotification(0);
					for($k=0;$k<sizeof($emailIDList);$k++)
					{
						if($mem_other_family_id==$emailIDList[$k]['id']){
				
							if($emailIDList[$k]['to_email'] <> "")
							{
								$display[$emailIDList[$k]['to_email']] = $emailIDList[$k]['to_name'];
								$bccUnitsArray[$k] = $emailIDList[$k]['unit'];
							}

				}

			}
			if(sizeof($display)==0)
			{
				echo "<br>Email ID Missing</br>";
				//return;
				//exit();

			}
			
			$societyEmail="";
			if($objFetchData->objSocietyDetails->sSocietyEmail <> "")
			{
				 $societyEmail=$objFetchData->objSocietyDetails->sSocietyEmail;
			}
			else
			{
				$societyEmail="techsupport@way2society.com";
			}
			try
			{
				$EmailIdToUse=$obj_utility->GetEmailIDToUse(true,1,"",$memunit,0,$dbname,$society_id,$mem_other_family_id,$bccUnitsArray);
				 $EMailID = $EmailIdToUse['email'];
				 $Password = $EmailIdToUse['password'];

				if($EmailIdToUse['status']==0)
				{
					$transport = Swift_SmtpTransport::newInstance('103.50.162.146',25)
						->setUsername($EMailID)
						->setSourceIp('0.0.0.0')
						->setPassword($Password) ; 
								
						
				$message = Swift_Message::newInstance();
				
				if($objFetchData->objSocietyDetails->sSocietyEmail <> "")
				{
					$message->setTo(array(
					   $societyEmail => $societyName
					));
				}
				//print_r($display);
				$message->setBcc($display);
				 $message->setReplyTo(array(
				   $societyEmail => $societyName
				)); 
				$message->setSubject($mailsubject);
				$message->setBody($mailBody);
				$message->setFrom("no-reply@way2society.com", $objFetchData->objSocietyDetails->sSocietyName);					
				$message->setContentType("text/html");		
					
				$mailer=Swift_Mailer::newInstance($transport);
				$result=$mailer->send($message);
				$result=1;
				if($result==1)
				{
					echo "<br>sucess</br>";
				}
				else
				{
					echo "<br>Failed</br>";
				}
			}
			else
		  	{
				echo '<br>'.$EMailIDToUse['msg'].'</br>';
		  	}
				
				
				
			}
			catch(Exception $exp)
			{
				echo "<br>Error occurred in sending email.</br>";
			}
			


		}
		
	
			*/
			$msg = "<b> END TIME : </b>".date('Y-m-d h:i:s ')."<br /><hr />";
		fwrite($Logfile,$msg);
			
	}
	return true;

		
	}
	
	
	
	
	
	
	 function SendPollReminderEmail($mMysqli)
	{
		
		$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
			//echo 'Test';
			
			//$pollDetails = getResult($mMysqli,"SELECT a.poll_id ,a.question,a.group_id,a.start_date,a.end_date, a.status,b.group_id,b.society_id,c.society_id,c.dbname FROM soc_group as b JOIN poll_question as a ON b.group_id = a.group_id Join society as c on b.society_id=c.society_id WHERE  a.start_date = '".date('Y-m-d')."'  AND a.send_notification = '0000-00-00 00:00:00'");
			  $pollDetails = getResult($mMysqli,"SELECT a.poll_id ,a.question,a.group_id,a.start_date,a.end_date, a.status FROM poll_question as a WHERE a.start_date = '".date('Y-m-d')."'  AND a.send_notification = '0000-00-00 00:00:00'");
			
		//print_r($pollDetails);
			
			for($iCnt = 0 ; $iCnt < sizeof($pollDetails); $iCnt++)
			{ 
				$obj_poll = new create_poll($dbConnRoot, $dbConn);
				$question = $pollDetails[$iCnt]['question'];
			 	$startDate = $pollDetails[$iCnt]['start_date'];
			 	 $pollId = $pollDetails[$iCnt]['poll_id'];
			    $polls=$obj_poll->sendPollEmail($question, $startDate, $endDate, $pollId, $catEmail = '',false);
				
				date_default_timezone_set('Asia/Kolkata');
				$send_notification = date('Y-m-d h:i:s ');	
				
				$mMysqli->query("UPDATE `poll_question` SET `send_notification`= '".$send_notification."' WHERE `poll_id`='".$pollId."'");		
						
				//print_r($mMysqli);
				
			}
			return "update";
	}
	function SendTenantReminderEmail($mMysqli)
	{
		
		date_default_timezone_set('Asia/Kolkata');
		$next_runDate = date('Y-m-d', strtotime(' +1 day'));
		$tenantRem = getResult($mMysqli,"SELECT * FROM `tenant_reminder` WHERE next_run = '".date('Y-m-d')."' ");
		for($iCnt = 0 ; $iCnt< sizeof($tenantRem); $iCnt++)
		{
			$SocietyID	= $tenantRem[$iCnt]['soicety_id'];
			$Last_Run 	= $tenantRem[$iCnt]['last_run'];
			$Next_Run 	=$tenantRem[$iCnt]['next_run'];
				
			$societyData = getResult($mMysqli,"SELECT * FROM `society` WHERE society_id ='".$SocietyID."' AND software_subscription IN (0,2) AND  status= 'Y'");
			if($societyData <> '')
			{
				$dbname = $societyData[0]['dbname'];
				$societyName = $societyData[0]['society_name'];
				$mMysqli1 = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, $dbname );
			 	if(!$mMysqli1)
				{
					echo '<br/>Connection Failed';
					$mMysqli->query("UPDATE `tenant_reminder` SET `next_run`= '".$next_runDate."',`last_run`='".$Next_Run."',`timestamp`=NOW() WHERE `soicety_id`='".$SocietyID."'");
				}
				else
				{
					echo '<br/>Connected';
					$dbConn = new dbop(false, $dbname);
					$dbConnRoot = new dbop(true);
					$obj_utility  = new utility($dbConn,$dbConnRoot);
					// Get EMail Header ANd Footer 
					$header=$obj_utility->GetEmailHeader();
					$footer=$obj_utility->GetEmailFooter();
					 
					$data = getResult($mMysqli1,"select *,u.unit_no, w.wing from `tenant_module` as t join unit as u on u.unit_id=t.unit_id join wing as w on u.wing_id=w.wing_id where t.end_date >= DATE(now()) and t.end_date <= DATE_ADD(DATE(now()), INTERVAL 1 Month) and t.status='Y'  order by t.tenant_id desc");
						
					if($data <> '')
					{
						for($i=0 ;$i<sizeof($data);$i++)
						{
							$memberMailSubject = "Reminder Rent Agreement Expire";
							
							$date1 =$data[$i]["end_date"];
							$date2 =date("Y-m-d");
							$Interval ='day';
							$DayDiff = getResult($mMysqli1,"SELECT TIMESTAMPDIFF(".$Interval.",'" . $date2 . "','" . $date1 . "') AS DiffDate");
							$Days=$DayDiff[0]['DiffDate'];
							$mailBody='';
							if($Days == 30 || $Days == 0)
							{
								$TenantName =$data[$i]["tenant_name"].' '.$data[$i]["tenant_MName"].' '.$data[$i]["tenant_LName"];
				    			$TenantEmail =$data[$i]["email"];
									
								$mailBody .= '<b> Dear '.$TenantName.'</b>,'.'<br/><br/>';
								if($Days == 0)
								{
									$mailBody .= 'Your Rent Agreement Expire On Today.<br> Please Renew your Agreement early, otherwise terminate agreements.';
								}
								else
								{
									$mailBody .= 'Your Rent Agreement Expire On '.$data[$i]["end_date"].'.<br>Please Renew your Agreement before expire date, otherwise terminate agreements.';
								}
								
								//echo $emailContent;
								if($TenantEmail <> '')
								{
									try
									{
										//$AWS_Config = CommanEmailConfig();
										//print_r($AWS_Config);
										/*$SMTP_Username = "AKIAWORPNMPGX76CCAPQ";
										$SMTP_Password = "BOwueG82ahzTYrSgK5igS9qChzA6KKF35obJEEvXTrGe";
										$SMTP_endpoint = "email-smtp.ap-south-1.amazonaws.com";
										$SMTP_Port = 587;
										$SMTP_Security = "tls";
										$transport = Swift_SmtpTransport::newInstance($SMTP_endpoint,$SMTP_Port , SMTP_Security)
				        							->setUsername($SMTP_Username)
				        							->setPassword($SMTP_Password);*/
										$AWS_Config = CommanEmailConfig();
										$transport = Swift_SmtpTransport::newInstance($AWS_Config[0]['Endpoint'],$AWS_Config[0]['Port'] , $AWS_Config[0]['Security'])
				        							->setUsername($AWS_Config[0]['Username'])
				        							->setPassword($AWS_Config[0]['Password']);			
													
										$emailContent =$header.$mailBody . $footer;
				 						$message = Swift_Message::newInstance();
										$message->setTo(array(
													$TenantEmail => $TenantName
											));	 
										$message->setSubject($memberMailSubject);
										$message->setBody($emailContent);
										$message->setFrom('no-reply@way2society.com',"Way2Society");
										$message->setContentType("text/html");										 
										// Send the email				
										$mailer = Swift_Mailer::newInstance($transport);
										$resultEmailSend = $mailer->send($message);											
										if($resultEmailSend > 0)
										{								
											echo "Success";	
											if($Days == 0)
											{
												$mMysqli1->query("UPDATE `tenant_module` SET `RemTodayExpire`= '1',`RemToday_timestamp`=NOW() WHERE `tenant_id`='".$date1 =$data[$i]["tenant_id"]."'");
											}
											else
											{
												$mMysqli1->query("UPDATE `tenant_module` SET `RemEmail_30days`= '1',`Rem_30_timestamp`=NOW() WHERE `tenant_id`='".$date1 =$data[$i]["tenant_id"]."'");
											}
										}
										else
										{
											echo "Field";
				   						}
												
										$mMysqli->query("UPDATE `tenant_reminder` SET `next_run`= '".$next_runDate."',`last_run`='".$Next_Run."',`timestamp`=NOW() WHERE `soicety_id`='".$SocietyID."'");
											
									}
									catch(exception $e)
									{
										echo "<br>".$e;
									}
									
								}
								
							}
							
								
						}
						$mMysqli->query("UPDATE `tenant_reminder` SET `next_run`= '".$next_runDate."',`timestamp`=NOW() WHERE `soicety_id`='".$SocietyID."'");
					
					}
				}
			}
		}
	}
	
function CommanEmailConfig()
{
	$response = array();
	//$SMTP_Username = "AKIAWORPNMPGX76CCAPQ";
	//$SMTP_Password = "BOwueG82ahzTYrSgK5igS9qChzA6KKF35obJEEvXTrGe";
	$SMTP_Username = "AKIAWORPNMPGVXHVQM4C";
	$SMTP_Password = "BAe+7wu2ry9dP8zO7irJKjdBCtObWbOYAZV71nK56Ymn";
	$SMTP_endpoint = "email-smtp.ap-south-1.amazonaws.com";
	$SMTP_Port = 587;
	$SMTP_Security = "tls";
	array_push($response,array("Username"=>$SMTP_Username, "Password"=> $SMTP_Password, "Endpoint"=>$SMTP_endpoint, "Port"=>$SMTP_Port, "Security"=>$SMTP_Security));
	
	
	return $response; 
}
	
?>

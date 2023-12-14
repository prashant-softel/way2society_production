<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Process Message Queue</title>
</head>
<?php

/*include_once("notice.class.php");	
include_once("events.class.php");
include_once("utility.class.php");
*/			
	//print_r($dbConn);
	
	//if($_SESSION['login_id'] == 4){ // Prashant sir
	
	/*$file  = 'log/process_queue_emails_log_'.rand().'_'.date('Y-m-d H:i:s').'.txt';
	$fileOpen = fopen($file,'a');
	fwrite($fileOpen, 'process_email_queue called');
	*/
	
	include_once ("include/dbop.class.php");
	include_once ("email.class.php");
	$QueueProcessLimit = 5;
	//exit();
	$dbConnRoot = new dbop(true);
	
	echo "<BR>Inside queue process " . $_REQUEST["status"];
	if($_REQUEST["status"] == 1)
	{
		echo "<BR/><BR/>";
		
		echo $sqlBillQueuereport = "SELECT count(*) as count, dbname  FROM `emailqueue` WHERE `Status` = 0 group by dbname";

		$Queue_Report = $dbConnRoot->select($sqlBillQueuereport);

		var_dump($Queue_Report);
		
	}
	
	
	$sqlBillQueueFetch = "select * from `emailqueue` where `Status`=0 and `PeriodID`<>0 and `ModuleTypeID`=0 ORDER BY Priority DESC LIMIT " . $QueueProcessLimit ;
	$ids = $dbConnRoot->select($sqlBillQueueFetch);
	if(sizeof($ids) > 0)
	{
		echo "<br/>Sending ".sizeof($ids) . " emails.<br/>";
	}
	else
	{ 
		echo "<br/>No email in queue to send.<br/>";
		//fwrite($fileOpen, '<br/>No email in queue to send.<br/>');
	}

	for($QueueCount = 0; $QueueCount < sizeof($ids); $QueueCount++)
	{
		$DBName = $ids[$QueueCount]["dbName"];
		$PeriodID = $ids[$QueueCount]["PeriodID"];
		$SocietyID = $ids[$QueueCount]["SocietyID"];
		$UnitID = $ids[$QueueCount]["UnitID"];
		$_REQUEST['BT'] = $ids[$QueueCount]["Mode"];
		$Invoice_Number = "";
		$ResultAry  = array();

		$dbConn = new dbop(false,$DBName);
		$CronJobProcess = 1;
		SendEMail($dbConn, $dbConnRoot, $CronJobProcess, $DBName, $SocietyID, $UnitID, $PeriodID,$Invoice_Number,$ids[$QueueCount]["id"],$ResultAry,$_REQUEST['BT']);

		// SendEMail2($dbConn, $dbConnRoot, $CronJobProcess, $DBName, $SocietyID, $UnitID, $PeriodID, $ids[$QueueCount]["id"]);
	}
	//fclose($fileOpen);
	//}
	/*$NoticeEmails = $dbConnRoot->select("select DISTINCT `SourceTableID` from `emailqueue` where `Status`=0 and `PeriodID`=0  and `ModuleTypeID`=1");
	//echo "Notices:<br/>";
	if(sizeof($NoticeEmails) > 0)
	{
		echo "<br/>Sending ".sizeof($NoticeEmails) . " Notices.<br/>";
	}
	else
	{ 
		echo "<br/>No Notice in queue to send.<br/>";
	}
	//print_r($NoticeEmails);
	foreach($NoticeEmails as $Notices)
	{
		$srcTableID = $dbConnRoot->select("select * from `emailqueue` where `SourceTableID`='".$Notices["SourceTableID"]."' and `Status`=0 and `ModuleTypeID`=1");
		$arBCCEmailIDs = array();
		$iBCCCounter = 0;
		$Subject = "";
		$desc = "";
		$arBCCEmailIDs = "";
		$FileName = "";
		$NoticeID = "";
		$objNotice = "";
		foreach($srcTableID as $srcTable)
		{
			$dbConn = new dbop(false, $srcTable["dbName"]);
			if(isset($dbConn))
			{
				$objNotice = new notice($dbConn, $dbConnRoot, $srcTable["SocietyID"]);
					
			//echo "ct:".print_r($srcTable);
			//echo "dbname:".$srcTable["dbName"];
			//echo "UnitID:".$srcTable["UnitID"];
			if($srcTable["UnitID"] > 0)
			{
				
					//$ret =$dbConn->select("select * from `paymentdetails`");
					//print_r($ret);
					//echo "<br/>connected".$srcTable["SocietyID"];
					$memDetails = $objNotice->objFetchData->GetMemberDetails($srcTable["UnitID"]);
					if(isset($objNotice->objFetchData->objMemeberDetails->sEmail))
					{
					$arBCCEmailIDs[$iBCCCounter] = $srcTable["UnitID"];
					//echo "emailID.".$arBCCEmailIDs[$iBCCCounter];
					$iBCCCounter++;
					
					//print_r($memDetails[0]);
					$NoticeDetails = $dbConn->select("select * from `notices` where id='".$srcTable["SourceTableID"]."'");
					//print_r($NoticeDetails);
					$Subject = $NoticeDetails[0]["subject"];
					//echo "<br/>subject:".$Subject;
					$desc = $NoticeDetails[0]["description"];
					//echo "Email:".$memDetails["emailid"];
					$FileName = $NoticeDetails[0]["note"];
					$Type = $NoticeDetails[0]["notice_type_id"];
					$CreationDate = $NoticeDetails[0]["creation_date"];
					$PostDate = $NoticeDetails[0]["post_date"];
					$ExpiryDate = $NoticeDetails[0]["exp_date"];
					$IsNotify = $NoticeDetails[0]["isNotify"];
					//echo "Subject<".$Subject."> Desc:<".$desc."> fileName:<".$FileName."> srcTableID:<".$srcTable["SourceTableID"]."> Note:<".$Note."> DBName <".$srcTable["dbName"]."> Type:<".$Type.">CreationDate<".$CreationDate.">PostDate:<".$PostDate.">";
					}
									}
			}
		}
		//echo "<br/>1bcc size:<".sizeof($arBCCEmailIDs) ."><br/>";
		$loopCounter =  0;
		foreach($arBCCEmailIDs as $arIDs)
		{
			//echo "ids:".$arIDs ."<br/>";
			$loopCounter++;
		}
		//echo "<br/>change<br/>";
		if($loopCounter > 0)
		{
			//echo "ready to send";
			//echo "srcSocID:".$srcTable["SocietyID"];
			//echo "srcDBName:".$srcTable["dbName"];
			  
			$objNotice->sendEmail($Subject, $desc, $arBCCEmailIDs, $FileName, $NoticeID, 0, $srcTable["SocietyID"], $srcTable["dbName"], 1, $Notices["SourceTableID"]);
		}
		//echo "<br/>end<br/>";

	}
	$EventEmails = $dbConnRoot->select("select DISTINCT `SourceTableID` from `emailqueue` where `Status`=0 and `PeriodID`=0  and `ModuleTypeID`=2");
	//echo "Events:";
	if(sizeof($EventEmails) > 0)
	{
		echo "<br/>Sending ".sizeof($EventEmails) . " Events.<br/>";
	}
	else
	{ 
		echo "<br/>No events in queue to send.<br/>";
	}
	//print_r($EventEmails);
	foreach($EventEmails as $Event)
	{
		$query =  "select * from `emailqueue` where `SourceTableID`='".$Event["SourceTableID"]."' and `Status`=0 and `ModuleTypeID`=2";
		//echo $query;
		$srcTableID = $dbConnRoot->select($query);
		$arBCCEmailIDs = array();
		$iBCCCounter = 0;
		$Subject = "";
		$desc = "";
		$arBCCEmailIDs = "";
		$FileName = "";
		$EventID = "";
		$objEvent = "";
		//echo "<br/>test<br/>";
		foreach($srcTableID as $srcTable)
		{
			//echo "<br/>test2<br/>";
			$dbConn = new dbop(false, $srcTable["dbName"]);
			if(isset($dbConn))
			{
				//echo "<br/>test3<br/>";
				$objEvent = new events($dbConn, $dbConnRoot, $srcTable["SocietyID"]);
				//echo "<br/>test4<br/>";	
			//echo "ct:".print_r($srcTable);
			//echo "dbname:".$srcTable["dbName"];
			//echo "UnitID:".$srcTable["UnitID"];
			if($srcTable["UnitID"] > 0)
			{
				
					//$ret =$dbConn->select("select * from `paymentdetails`");
					//print_r($ret);
					//echo "<br/>connected".$srcTable["SocietyID"];
					$memDetails = $objEvent->objFetchData->GetMemberDetails($srcTable["UnitID"]);
					//echo "<br/>test5<br/>";
					if(isset($objEvent->objFetchData->objMemeberDetails->sEmail))
					{
					$arBCCEmailIDs[$iBCCCounter] = $srcTable["UnitID"];
					//echo "emailID.".$arBCCEmailIDs[$iBCCCounter];
					$iBCCCounter++;
					$EventID  = $srcTable["SourceTableID"];
					//print_r($memDetails[0]);
					$EventDetails = $dbConnRoot->select("select * from `events` where `events_id`='".$srcTable["SourceTableID"]."'");
					//print_r($NoticeDetails);
					/*$Subject = $EventDetails[0]["events_title"];
					//echo "<br/>subject:".$Subject;
					$desc = $EventDetails[0]["events"];
					//echo "Email:".$memDetails["emailid"];
					$FileName = $EventDetails[0]["Uploaded_file"];
					$Type = $EventDetails[0]["event_type"];
					$CreationDate = $EventDetails[0]["creation_date"];
					$PostDate = $EventDetails[0]["post_date"];
					$ExpiryDate = $EventDetails[0]["exp_date"];
					$IsNotify = $EventDetails[0]["isNotify"];*/
					//echo "Subject<".$Subject."> Desc:<".$desc."> fileName:<".$FileName."> srcTableID:<".$srcTable["SourceTableID"]."> Note:<".$Note."> DBName <".$srcTable["dbName"]."> Type:<".$Type.">CreationDate<".$CreationDate.">PostDate:<".$PostDate.">";
				/*	}
									}
			}
		}
		//echo "<br/>Events bcc size:<".sizeof($arBCCEmailIDs) ."><br/>";
		$loopCounter =  0;
		foreach($arBCCEmailIDs as $arIDs)
		{
			//echo "ids:".$arIDs ."<br/>";
			$loopCounter++;
		}
		//echo "<br/>change<br/>";
		if($loopCounter > 0)
		{
			//echo "ready to send";
			//echo "srcSocID:".$srcTable["SocietyID"];
			//echo "srcDBName:".$srcTable["dbName"];
			  
			$objEvent->SendEventInEmail(1, $srcTable["dbName"], $srcTable["SocietyID"], $EventID);
		}
		//echo "<br/>end<br/>";

	}
	$hostname = 'localhost';
	$username = 'root';
	$password = '';
	$dbPrefix = 'hostmjbt_society';
	
	$hostname = DB_HOST;
	$username = DB_USER;
	$password = DB_PASSWORD;
	$dbPrefix = 'hostmjbt_society';
	
	$startNo = 1;
	$endNo = 100;
	
	
	//echo '<br/><br/>Updating DB ' . $dbPrefix . $startNo . ' to ' . $dbPrefix . $endNo   ;
			
			for($iCount = $startNo; $iCount <= $endNo; $iCount++)
			{
				try
				{
					$dbName = $dbPrefix . $iCount;
					//echo '<br/><br/>Connecting DB : ' . $dbName;
					 
					$mMysqli = mysqli_connect($hostname, $username, $password, $dbName);
					if(!$mMysqli)
					{
						echo '<br/>Connection Failed';
					}
					else
					{
						//if($dbName == "hostmjbt_society86")
						//{ 
							//echo '<br/>Connected';
							$dbConn = new dbop(false,$dbName);
							$objUtility  = new utility($dbConn);
							$query = "select * from `generalsms_log` WHERE `DeliveryStatus`=''";
							
							$result = $dbConn->select($query);
							if($result)
							{
								foreach($result as $Record)
								{ 
										$SentReport = $Record['SentReport'];
										$arSentReport = explode(',',$SentReport);
												$arDeliveryReport = "";
												if($Record['DeliveryReport'] <> "")
												{
													$DeliveryReport = $Record['DeliveryReport'];
													$arDeliveryReport = explode(',',$DeliveryReport);
												}
												//echo '<br/>ID '.$Record['ID'].' MSGID ' .$arSentReport[2]. ' DeliveryReport '.$Record['DeliveryReport'] ;
												if((isset($arSentReport[2]) && $arSentReport[2] <> "" && $arSentReport[2] <> "0") && ( ($arDeliveryReport <> "" && sizeof($arDeliveryReport) > 0 && $arDeliveryReport[4] <> "Delivered") || $arDeliveryReport == "" ) )
												{
													$response = $objUtility->GetSMSDeliveryReport($arSentReport[2]);
													$status = explode(',',$response);
													$UpdateQuery = "UPDATE `generalsms_log` SET `DeliveryReport`='".$response."',`DeliveryStatus`='".$status[4]."' WHERE `ID`='".$Record['ID']."'";
													echo "<br/>".$UpdateQuery;
													$dbConn->update($UpdateQuery);
												}
								}
							}
							else
							{
								echo '<br/>Update Failed';
							}
							//------------------------------------------------------------------------------------------------
							//									Notification SMS
							//------------------------------------------------------------------------------------------------
							$NFT_Query = "select * from `notification` WHERE `DeliveryStatus`=''";
							
							$NFT_Result = $dbConn->select($NFT_Query);
							if($NFT_Result)
							{
								foreach($NFT_Result as $NFT_Record)
								{ 
										$SentReport = $NFT_Record['SMSSentReport'];
										if($SentReport <> "")
										{
											$arSentReport = explode(',',$SentReport);
											
											$arnDeliveryReport ="";
											if($NFT_Record['DeliveryReport'] <> "")
											{
												$nDeliveryReport = $NFT_Record['DeliveryReport'];
												$arnDeliveryReport = explode(',',$nDeliveryReport);
											}
											
											//echo '<br/>ID '.$NFT_Record['ID'].' MSGID ' .$arSentReport[2]. ' DeliveryReport '.$NFT_Record['DeliveryReport'] ;
											if((isset($arSentReport[2]) && $arSentReport[2] <> "" && $arSentReport[2] <> "0")&& ( ($arnDeliveryReport <> "" && sizeof($arnDeliveryReport) > 0 && $arnDeliveryReport[4] <> "Delivered") || $arnDeliveryReport == "" ) )
											{
												$response = $objUtility->GetSMSDeliveryReport($arSentReport[2]);
												$status = explode(',',$response);
												$UpdateQuery = "UPDATE `notification` SET `DeliveryReport`='".$response."',`DeliveryStatus`='".$status[4]."' WHERE `ID`='".$NFT_Record['ID']."'";
												echo "<br/>".$UpdateQuery;
												$dbConn->update($UpdateQuery);
											}
										}
								}
							}
							else
							{
								//echo '<br/>Update Failed';
							}
							mysqli_close($mMysqli);
							//echo '<br/>Connection Closed';
						//}
						//else
						//{
						//	echo "</br>skipped db ".$dbName;
						//}
					}
				}
				catch(Exception $ex)
				{
					echo "Exception:".$ex;
				}
			}*/
	?>
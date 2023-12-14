<?php 
	if(isset($_REQUEST['SentEmailManually']) && $_REQUEST['SentEmailManually'] == 1)
	{
		include_once ("dbconst.class.php");
		include_once ("include/dbop.class.php");
		$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$societyID = $_SESSION['society_id'];
		$DBName = $_SESSION['dbname'];
		$UnitID = $_REQUEST['unit'];
		$UnitsCollection = $_REQUEST['unitsArray'];
		$PeriodID = $_REQUEST['period'];
		$Invoice_Number = $_REQUEST['Invoice_Number'];
		$InvoiceDate = "";
		$Units = "";
		if($_REQUEST['BT'] == TABLE_SALESINVOICE)	
		{
			$PeriodID = 0;	
		}
		else
		{
			$PeriodID = $_REQUEST['period'];
		}
		if(isset($_REQUEST['unitsArray']))
		{
			$Units = json_decode($UnitsCollection);	
		}
		else
		{
			$Units   = array();
			$Units[0] = $UnitID;
		}
		$ResultAry  = array();
		foreach($Units as $UnitNumber)
		{
			if($UnitNumber <> "")
			{
				SendEMail($dbConn, $dbConnRoot, 0, $DBName, $societyID, $UnitNumber, $PeriodID, $Invoice_Number, "-1", $ResultAry,$_REQUEST['BT']);
			}
		}
		echo json_encode($ResultAry);
		
	}
	else if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'Send_invitaion_to_all_member') // requested to send invitation for all the society member
	{
		echo Send_invitaion_to_all_member(); // calling method to send emaail
	}
	//----------------- Added Function -----------------------//
	if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'SendRemEMail')
	{
		//echo "INside Rem Email";
		include_once ("dbconst.class.php");
		include_once ("include/dbop.class.php");
		$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		
		$DBName = $_SESSION['dbname'];
		$SocietyID = $_SESSION['society_id'];
		$UnitsCollection = $_REQUEST['unitsArray'];
		$PeriodID = $_REQUEST['period'];
		$res = $dbConn->select("SELECT society_code,society_name FROM `society`");
		//echo "SELECT DueDate FROM `billregister` where PeriodID ='".$PeriodID."' AND BillType='".$_REQUEST['BT']."'";
		$res1 = $dbConn->select("SELECT DueDate FROM `billregister` where PeriodID ='".$PeriodID."' AND BillType='".$_REQUEST['BT']."'");
		
		$SocietyCode= $res[0]['society_code']; 
		$SocietyName= $res[0]['society_name']; 
		$dueDate= $res1[0]['DueDate']; 
		//print_r($res);
		$Units = json_decode($UnitsCollection);
		$ResultAry  = array();	
		foreach($Units as $UnitNumber)
		{
			if($UnitNumber <> "")
			{
				SendBillReminderEMAIL($DBName, $SocietyID, $SocietyName, $SocietyCode, $PeriodID, $dueDate,$UnitNumber,$ResultAry);
				//SendEMail($dbConn, $dbConnRoot, 0, $DBName, $societyID, $UnitNumber, $PeriodID, $Invoice_Number, "-1", $ResultAry,$_REQUEST['BT']);
			}
		}
		echo json_encode($ResultAry);
		//$response = SendBillReminderEMAIL($DBName, $SocietyID, $SocietyName, $SocietyCode, $PeriodID, $dueDate,$UnitsCollection);
	}
	
	
	function getTransport($obj_Utility)
	{
		$debug_trace = 0;
		
		if($debug_trace == 1)
		{
			echo "<br>Access get Transport";
		}
		
		$EMailIDToUse = $obj_Utility->GetEmailIDToUse(false, 0, 0, 0, 0, 0, $_SESSION['society_id'], 0, 0);
		
		$EMailID = "";
		$Password = "";
				
		if(isset($EMailIDToUse) && $EMailIDToUse['status'] == 0)
		{
				$EMailID = $EMailIDToUse['email'];
				$Password = $EMailIDToUse['password'];
		}
		
		if($debug_trace == 1)
		{
			echo "<br>EMailID = ".$EMailID;
			echo "<br>Password = ".$Password;
		}
		
		

		//These settings are for AWS TLS. 		
		//$SMTP_Username = "AKIAWORPNMPGX76CCAPQ";
		//$SMTP_Password = "BOwueG82ahzTYrSgK5igS9qChzA6KKF35obJEEvXTrGe";
		//$SMTP_endpoint = "email-smtp.ap-south-1.amazonaws.com";
		//$SMTP_Port = 587;
		//$SMTP_Security = "tls";
		$AWS_Config = CommanEmailConfig();
		 $transport = Swift_SmtpTransport::newInstance($AWS_Config[0]['Endpoint'],$AWS_Config[0]['Port'] , $AWS_Config[0]['Security'],$AWS_Config[0]['Name'])
				  ->setUsername($AWS_Config[0]['Username'])
				  ->setPassword($AWS_Config[0]['Password']);
//		$transport = Swift_SmtpTransport::newInstance('103.50.162.146',587)
		//$transport = Swift_SmtpTransport::newInstance($SMTP_endpoint, $SMTP_Port, $SMTP_Security)
		//->setUsername($SMTP_Username)
		//->setSourceIp('0.0.0.0')
		//->setPassword($SMTP_Password);
		return $mailer = Swift_Mailer::newInstance($transport);
		
	}
	
	
	
	function Send_invitaion_to_all_member()
	{
		//error_reporting(0);
		include_once ("dbconst.class.php");
		include_once ("include/fetch_data.php");
		include_once ("include/dbop.class.php");
		include_once ("utility.class.php");
		include_once ("initialize.class.php");
		include_once('email_format.class.php');
		
		
		$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		
		$objFetchData = new FetchData($dbConn);
		$obj_Utility = new utility($dbConn, $dbConnRoot);
		$obj_initialize = new initialize($dbConnRoot);
		
		
		$getTransport = 1;
		$debug_trace = 0;
		
		if($debug_trace == 1)
		{
			echo "<br>Inside the Send_invitaion_to_all_member.  <br> Fetching all member email Id";
		}
		
		$emailIDList = $objFetchData->GetEmailIDToSendNotification(0); // passing 0 to get all members email IDs
		
		$max_execution_time = 5 * count($emailIDList); 
		
		ini_set('max_execution_time', $max_execution_time); 
		
		
		$Invition_Sent_unit_Lists = array();  
		$function_count = 1;
		
		if($debug_trace == 1)
		{
			echo "<pre>";
			print_r($emailIDList);
			echo "</pre>";
		}
		
		$mailer = getTransport($obj_Utility);
		
		$mailer->registerPlugin(new Swift_Plugins_AntiFloodPlugin(49));
		
		$mailer->registerPlugin(new Swift_Plugins_AntiFloodPlugin(49, 30));
			
		$memberMailSubject = "Invitation to Activate Your Way2Society Account";
		
		$cnt = 0;
		$success = 0; 
		
		
		
		for($i = 0 ; $i < count($emailIDList) ; $i++)
		{
			if($debug_trace == 1)
			{
				echo "<br>iteration start";					
			}
			$mailToEmail = $emailIDList[$i]['to_email'];
			$unitID = $emailIDList[$i]['unit'];
			$mailToName = $emailIDList[$i]['to_name'];
			$result = 0;
			$arPaidToParentDetails = $obj_Utility->getParentOfLedger($unitID);
			
			if(!(empty($arPaidToParentDetails)))
			{
				$unitNo = $arPaidToParentDetails['ledger_name'];
			}
			
			//if(!in_array($mailToEmail,$Invition_Sent_EmailID_Lists)) // Checking whether email already not sent to this email id. 
			//{
				$loginExist = $dbConnRoot->select("SELECT mp.id,log.login_id FROM `mapping` as mp JOIN login as log ON log.login_id = mp.login_id WHERE log.member_id = '".$mailToEmail."' AND mp.society_id = '".$_SESSION['society_id']."' AND mp.unit_id  = '".$unitID."' " );
				
				if(sizeof($loginExist) == 0)		
				{
					$mapping_code = '';			
					$mappingDetails = $dbConnRoot->select("SELECT * FROM `mapping` WHERE `society_id` = '".$_SESSION['society_id']."' AND `unit_id` = '".$unitID."' AND `status` = 1");
					
					if(sizeof($mappingDetails) == 0 || in_array($unitID,$Invition_Sent_unit_Lists)) // In array one unit can have mutliple member so need to create new code
					{
						//$sAccountActivationCode = getRandomUniqueCode();
						$sCode = getRandomUniqueCode();
					
						if($mailToEmail <> '')
						{
							$sAccountActivationCode = substr(($sCode),0, 4);
							$sActivCode=$sAccountActivationCode;
							
							$sAccountActivationCode=$mailToEmail . $sActivCode;									
							$codeType=1;
						}
						else
						{
							$sAccountActivationCode=$sCode;
							$codeType=0;
						}
						
						if($debug_trace == 1)
						{
							echo "<pre>";
							print_r($arPaidToParentDetails);
							echo "</pre>";
						}
						
						if(!(empty($arPaidToParentDetails)))
						{
							$unitNo = $arPaidToParentDetails['ledger_name'];
						}
						
						 $insert_mapping = "INSERT INTO `mapping`(`society_id`, `unit_id`, `desc`, `code`,`code_type`, `role`, `created_by`, `view`,`status`) VALUES ('" . $_SESSION['society_id'] . "', '" . $unitID . "', '" . $unitNo . "', '" . $sAccountActivationCode . "','".$codeType."', '" . ROLE_MEMBER . "', '" . $_SESSION['login_id'] . "', 'MEMBER','1')";
						 $result_mapping = $dbConnRoot->insert($insert_mapping);
						  
						 $mapping_code = $sAccountActivationCode; 
					}
					 else
					 {
						$mapping_code = $mappingDetails[0]['code'];
					 }
				 }
				 
				 //send activation email
				 if($bSendSMS == 1)
				 {
					 $sSMSBody = "Your Mobile App activation code is " . $mapping_code;
					 
				 }
				 if($SendEmail)
				 {
				 $mailBody = $obj_initialize->generateActivationEmailTemplate(true,$mailToEmail,$mailToName,$mapping_code);
			     
				 if($debug_trace == 1)
				 {
					 	echo "<br>mailToEmail".$mailToEmail;
						echo "<br>unitID".$unitID;
						echo "<br>mailToName".$mailToName;
				 }
				 
				 $Invalid = false; 
				 if(isValidEmailID($dbConn->escapeString($mailToEmail)) == true)
				 {
					if($debug_trace == 1)
					{
						echo "<br>\n valid Email ID ".$mailToEmail;
					} 
					try
					  {
							$emailContent = GetEmailHeader() . $mailBody . GetEmailFooter() ;
							
							$message = Swift_Message::newInstance();
							$message->setTo(array($mailToEmail => $mailToName));
						 
							$message->setSubject($memberMailSubject);
							$message->setBody($emailContent);
							$message->setFrom(array('no-reply@way2society.com' => 'way2society'));
							// $message->setCc(array('techsupport@way2society.com' => 'Tech Support'));
							// $message->setCc(array('cs@way2society.com' => 'way2society'));
							$message->setContentType("text/html");	
							// Send the email
							$result = $mailer->send($message);
						}
					  catch(exception $e)
					  {
						  echo "error occured in send function".$e->getMessage();
						  echo "<br>".$e;
					 }				 
				 }
				 else
				 {
					$Invalid = true; 
				 }
				 
				 
				 if($debug_trace == 1)
				 {
					 echo "<br>Emails Response : ".$result;
				 }
				 
				 
				array_push($Invition_Sent_unit_Lists,$unitID); 
				
				if($result >= 1)
				{
					$success++;
					//echo "<br>Success : ".$success;
					
					if(($success % 49) == 0)
					{
						$function_count++;
						$mailer = getTransport($obj_Utility);
					}
					$status = "Success"; //Pending Need to store log's of email send 
				}
				else
				{
					if(empty($mailToEmail))
					{
						$status = "Email ID Not Avaiable";
					}
					else if($Invalid == true)
					{
						$status = "Invalid Email ID";
					}
					else
					{
						$status = "Failed";	
					}
				}
				
				$ResultAry[$cnt]['unit_no'] = $unitNo;;
				$ResultAry[$cnt]['Name'] = $mailToName;
				$ResultAry[$cnt]['email'] = $mailToEmail;
				$ResultAry[$cnt]['status'] = $status;
				$cnt++;
				
				if($debug_trace == 1)
				{
					echo "<br>iteration complete";					
				}
			}
		
		}
		
		return "@@@".json_encode($ResultAry)."@@@";
	}
	
	function SendEMail($dbConn, $dbConnRoot, $CronJobProcess, $DBName, $societyID, $unitID, $periodID, $Invoice_Number, $QueueID, &$ResultAry,$Supplemenary_bills = 0,$sEmail,$IsEmailSubscribe)
	{
		$debug_trace = 0;
		try
		{
			if($CronJobProcess == 1)
			{
				$debug_trace = 1;
			}
			if($debug_trace == 1)
			{
				echo "<BR><BR>Processing request : CronJobProcess: $CronJobProcess, $DBName, Societyid: $societyID, UnitID: $unitID, PeriodID; $periodID, QueueID : $QueueID";
			}

		
			
		include_once ("include/fetch_data.php");
		$obj_fetch = new FetchData($dbConn);
		include_once("utility.class.php");
		$obj_Utility = new utility($dbConn, $dbConnRoot);
		if($CronJobProcess == 1){
			require_once('/var/www/html/swift/swift_required.php');
			//require_once('C:\wamp\www\W2S\swift\swift_required.php');
		}
		else{
			require_once('../swift/swift_required.php');
		}
		

		
							
		$InvoiceDate = $obj_fetch->getInvoiceBillDate($unitID,$Invoice_Number);
		if(isset($unitID) && isset($periodID))
		{
			$EncodeSocID = base64_encode($societyID);
			$EncodeUnitID = base64_encode($unitID);
			$EncodePeriodID = base64_encode($periodID);
			$EncodeDbName = base64_encode($DBName);
			
			$url = "<a href='http://way2society.com/neft.php?SID=".$EncodeSocID."&UID=".$EncodeUnitID."'>Notify Society about NEFT Payment</a>";
			//$url = "<a href='http://localhost/way2society.com/neft.php?SID=".$EncodeSocID."'>View</a>";
			//-$mailBody .= $url;
			
			$memberDetails = $obj_fetch->GetMemberDetails($unitID);
			
			$societyDetails = $obj_fetch->GetSocietyDetails($obj_fetch->GetSocietyID($unitID));
			$mailToEmail = trim($obj_fetch->objMemeberDetails->sEmail);
			$OtherMembers = $obj_fetch->objMemeberDetails->arListOfMembers;
			$OtherTenants = $obj_fetch->objMemeberDetails->arListOfTenants;

			if(sizeof($OtherTenants) > 0)
			{
				array_push($OtherMembers, $OtherTenants[0]);
			}
			
			if(sizeof($OtherMembers) == 0)
			{
				$ResultAry[$unitID] = "Subscribe to society email checkbox is not set";
				return;
			}

			foreach($OtherMembers as $OtherMemberEmails)
			{
				$mailToEmail = trim($OtherMemberEmails[1]);
				//echo $mailToEmail;
				//die();
				if($mailToEmail == '')
				{
					$ResultAry[$unitID] = "Email ID Missing";
					return;
				}
				else if(filter_var($mailToEmail, FILTER_VALIDATE_EMAIL) == false)
				{
					$ResultAry[$unitID] = "Incorrect Email ID  ".$mailToEmail." ";
					return;
				}
				
				$socContactNo = $obj_fetch->objSocietyDetails->sSocietyEmailContactNo;
				$socAddress = $obj_fetch->objSocietyDetails->sSocietyAddress;
				$showSocietyAddressInEmail = $obj_fetch->objSocietyDetails->bSocietyAddressInEmail;
				$mailToName = $OtherMemberEmails[0];
				$newUserUrl = "";
				$isNewAccountCreate = false;
			
			
				$loginExist = $dbConnRoot->select("SELECT mp.id,log.login_id FROM `mapping` as mp JOIN login as log ON log.login_id = mp.login_id WHERE log.member_id = '".$mailToEmail."' AND mp.society_id = '".$_SESSION['society_id']."' AND mp.unit_id  = '".$unitID."' " );
				
				$sqlClientDetails = "SELECT * FROM `client` WHERE `id` = '".$_SESSION['society_client_id']."'";
				//$ResultAry[$unitID] = $sqlClientDetails;
				$ClientDetails = $dbConnRoot->select($sqlClientDetails);
				$EmailFooter=$ClientDetails[0]['email_footer'];
				if($showSocietyAddressInEmail==1)
				{
					$EmailFooter =$socAddress;
				}
				if(isset($ClientDetails))
				{
					//echo "invalid client details";
				}
				
				$encryptedEmail = $obj_Utility->encryptData($mailToEmail);	
				if(sizeof($loginExist) == 0)		
				{
					if($debug_trace == 1)
					{
						echo "<BR>1.0. Login doesnt exist";
					}
					$iSocietyId =  $obj_fetch->GetSocietyID($unitID);
					$mapping_code = '';			
					$mappingDetails = $dbConnRoot->select("SELECT * FROM `mapping` WHERE `society_id` = '".$iSocietyId."' AND `unit_id` = '".$unitID."' AND `status` = 1");
					if(sizeof($mappingDetails) == 0)
					{
						//$sAccountActivationCode = getRandomUniqueCode();
						$sCode = getRandomUniqueCode();
						if($mailToEmail <> '')
						{
						 	$sAccountActivationCode=substr(($sCode),0, 4);
							$sActivCode=$sAccountActivationCode;
							//echo "acive for mail:" .$sActivCode;
							$sAccountActivationCode=$mailToEmail . $sActivCode;									
							//echo "sAccountActivationCode:".$sAccountActivationCode;
						}
						else
						{
							$sAccountActivationCode=$sCode;
						}		
						if($debug_trace == 1)
						{
							echo "<BR>2.1. mapping doesnt exist. Creating new code " . $sAccountActivationCode;
						}

						$arPaidToParentDetails = $obj_Utility->getParentOfLedger($unitID);
						if(!(empty($arPaidToParentDetails)))
						{
							$unitNo = $arPaidToParentDetails['ledger_name'];
						}
						if($mailToEmail <> '')
						{
						 $codeType=1;	
						}
						else
						{
							$codeType=0;
						}
						$insert_mapping = "INSERT INTO `mapping`(`society_id`, `unit_id`, `desc`, `code`,`code_type`, `role`, `created_by`, `view`,`status`) VALUES ('" . $iSocietyId . "', '" . $unitID . "', '" . $unitNo . "', '" . $sAccountActivationCode . "','".$codeType."', '" . ROLE_MEMBER . "', '" . $_SESSION['login_id'] . "', 'MEMBER','1')";
						$result_mapping = $dbConnRoot->insert($insert_mapping);

						$mapping_code = $sAccountActivationCode;
					}
					else
					{
						$mapping_code = $mappingDetails[0]['code'];
						$sActivCode = substr($mapping_code,-4);
						if($debug_trace == 1)
						{
							echo "<BR>3.1.mapping " . $mappingDetails[0]['id'] . " mapped to login id " . $mappingDetails[0]['login_id'] . " with mapping_code  " . $mapping_code;					
						}
					}
					//{		
						//$encryptedEmail = $obj_Utility->encryptData($mailToEmail);					
						//$newUserUrl = "http://way2society.com/newuser.php?reg&u=".$mailToEmail."&n=".$mailToName."&c=".$mappingDetails[0]['code']."&tkn=".$encryptedEmail;
						//$newUserUrl = "http://way2society.com/newuser.php?reg&u=".$mailToEmail."&n=".$mailToName."&tkn=".$encryptedEmail;
						$newUserUrl = "https://way2society.com/newuser.php?reg&u=".$mailToEmail."&n=".$mailToName."&tkn=".$encryptedEmail."&c=".$mapping_code;
						//$newUserUrl = "http://localhost/beta_aws master_new/newuser.php?reg&u=".$mailToEmail."&n=".$mailToName."&tkn=".$encryptedEmail."&c=".$mapping_code;
						$onclickURL = $newUserUrl.'&URL=https://way2society.com/Dashboard.php?View=MEMBER';
						//$onclickURL = $newUserUrl.'&URL=http://localhost/beta_aws master_new/Dashboard.php?View=MEMBER';
					
						//	$userURL = $newUserUrl.'&url=http://localhost/beta_aws master_new/voucher.php?UnitID='.$unitID.'_**_inv_number='.$Invoice_Number;
						
						
						if($_REQUEST['BT'] == TABLE_SALESINVOICE)
						{
							$userURL = $newUserUrl.'&url=https://way2society.com/Invoice.php?e=1_**_UnitID='.$unitID.'_**_inv_number='.$Invoice_Number;
						}
						else
						{
							$userURL = $newUserUrl.'&url=https://way2society.com/Maintenance_bill.php?e=1_**_UnitID='.$unitID.'_**_PeriodID='.$periodID.'_**_BT='.$Supplemenary_bills;
						}
						
						
					//}
				}
				else
				{
					if($debug_trace == 1)
					{
						echo "<BR>1.1. Login exist " . $loginExist[0]['login_id'];
						//var_dump($loginExist);
						//echo "<BR><BR>";
					}
					$iSocietyId =  $obj_fetch->GetSocietyID($unitID);
					$mappingDetails = $dbConnRoot->select("SELECT * FROM `mapping` WHERE `society_id` = '".$iSocietyId."' AND `unit_id` = '".$unitID."' AND `login_id` = '".$loginExist[0]['login_id']."' ");
						
							
					if(sizeof($mappingDetails) > 0)
					{	
						if($debug_trace == 1)
						{
							echo "<BR>4.1mapping " . $mappingDetails[0]['id'] . " mapped to login id " . $mappingDetails[0]['login_id'] . " with mapping_code  " . $mappingDetails[0]['code'];					

						}
						$onclickURL = "https://way2society.com/Dashboard.php?View=MEMBER";
			
						//$userURL = $newUserUrl.'&url=http://way2society.com/voucher.php?UnitID='.$unitID.'_**_inv_number='.$Invoice_Number.'&u='.$encryptedEmail;
						if($_REQUEST['BT'] == TABLE_SALESINVOICE)
						{
							$userURL ='https://way2society.com/Invoice.php?e=1_**_UnitID='.$unitID.'_**_inv_number='.$Invoice_Number.'&u='.$encryptedEmail;
						}
						else
						{
						$userURL ='https://way2society.com/Maintenance_bill.php?e=1_**_UnitID='.$unitID.'_**_PeriodID='.$periodID.'_**_BT='.$Supplemenary_bills.'&u='.$encryptedEmail;		
						}
							
					}
					else
					{
						if($debug_trace == 1)
						{
							echo "<BR>1.1. login id not mapped";
						}							
						$mappingDetails = $dbConnRoot->select("SELECT * FROM `mapping` WHERE `society_id` = '".$iSocietyId."' AND `unit_id` = '".$unitID."' AND `status` = '1' ");		
						if(sizeof($mappingDetails) > 0)
						{	
							if($debug_trace == 1)
							{
								echo "<BR>4.1. mapping exist but not mapped to login id";
								var_dump($mappingDetails);
							}
							$sAccountActivationCode = $mappingDetails[0]['code'];
							$onclickURL = 'https://way2society.com/login.php?mCode='.$sAccountActivationCode.'&u='.$encryptedEmail;
						
								//$userURL ='http://way2society.com/login.php?mCode='.$sAccountActivationCode.'&u='.$encryptedEmail .'&url=voucher.php?UnitID='.$unitID.'_**_inv_number='.$Invoice_Number;
							if($_REQUEST['BT']== TABLE_SALESINVOICE)
							{
								$userURL ='https://way2society.com/login.php?mCode='.$sAccountActivationCode.'&u='.$encryptedEmail .'&url=Invoice.php?e=1_**_UnitID='.$unitID.'_**_inv_number='.$Invoice_Number;
							}
							else
							{	
								$userURL ='https://way2society.com/login.php?mCode='.$sAccountActivationCode.'&u='.$encryptedEmail .'&url=Maintenance_bill.php?e=1_**_UnitID='.$unitID.'_**_PeriodID='.$periodID.'_**_BT='.$Supplemenary_bills;
							}
									
								//	echo 'user<br>'.$userURL.'</br>';	
						}
						else
						{
							//Mapping does not exist.
							$sCode = getRandomUniqueCode();
							if($mailToEmail <> '')
							{
								$sAccountActivationCode=substr(($sCode),0, 4);
								$sActivCode=$sAccountActivationCode;
								//echo "acive for mail:" .$sActivCode;
								$sAccountActivationCode=$mailToEmail . $sActivCode;									
								//echo "sAccountActivationCode:".$sAccountActivationCode;
							}
							else
							{
								$sAccountActivationCode=$sCode;
							}									
								$arPaidToParentDetails = $obj_Utility->getParentOfLedger($unitID);
							if(!(empty($arPaidToParentDetails)))
							{
								$unitNo = $arPaidToParentDetails['ledger_name'];
							}
							if($mailToEmail <> '')
							{
								$codeType=1;	
							}
							else
							{
								$codeType=0;
							}
							$insert_mapping = "INSERT INTO `mapping`(`society_id`, `unit_id`, `desc`, `code`,`code_type`, `role`, `created_by`, `view`,`status`) VALUES ('" . $iSocietyId . "', '" . $unitID . "', '" . $unitNo . "', '" . $sAccountActivationCode . "','".$codeType."', '" . ROLE_MEMBER . "', '" . $_SESSION['login_id'] . "', 'MEMBER','1')";
							$result_mapping = $dbConnRoot->insert($insert_mapping);
							if($debug_trace == 1)
							{
								echo "<BR>4.. new mapping $result_mapping created for login id " . $unitID;
								//var_dump($mappingDetails);
							}
							
							$onclickURL = 'https://way2society.com/login.php?mCode='.$sAccountActivationCode.'&u='.$encryptedEmail;
							if($_REQUEST['BT']== TABLE_SALESINVOICE)
							{
								$userURL ='https://way2society.com/login.php?mCode='.$sAccountActivationCode.'&u='.$encryptedEmail .'&url=Invoice.php?e=1_**_UnitID='.$unitID.'_**_inv_number='.$Invoice_Number;
							}
							else
							{
							$userURL ='https://way2society.com/login.php?mCode='.$sAccountActivationCode.'&u='.$encryptedEmail .'&url=Maintenance_bill.php?e=1_**_UnitID='.$unitID.'_**_PeriodID='.$periodID.'_**_BT='.$Supplemenary_bills;	
							}	
						}
					}
				
				}
				
				if($newUserUrl <> "")
				{		
					$buttonValue = "My Account";	
					$isNewAccountCreate = true;
					$sText = "Please click below button to check your account balance, bills and previous payments. ";
					//$sText = "Your email id <".$mailToEmail."> is not registered to way2society.com yet. You can register now by clicking on ".$buttonValue.".";
					$neftURL = $newUserUrl.'&URL=https://way2society.com/Dashboard.php?View=MEMBER';
					$newUser = 1;	
				}
				else
				{	
					$buttonValue = "My Account";
					$sText = "Please click below button to check your account balance, bills and previous payments.";	
					$neftURL = 	'https://way2society.com/neft.php?SID='.$EncodeSocID.'&UID='.$EncodeUnitID;
					$newUser = 0;		
				}
				
				$sBillTypeText  = '';
				if($_REQUEST['BT'] == Maintenance)
				{
					//$sBillTypeText = 'Revised Maintenance ';
					$sBillTypeText = 'Maintenance ';
				}
				else if($_REQUEST['BT'] == Supplementry)
				{
					$sBillTypeText = 'Supplementary ';
				}
				else if($_REQUEST['BT'] == TABLE_SALESINVOICE)
				{
					$sBillTypeText = 'Invoice';
					//echo 'sale<BR>'.$sBillTypeText;
				}
				if($_REQUEST['BT'] ==TABLE_SALESINVOICE)
				{
					$mailSubject = $sBillTypeText . ' Bill For : ' .getDisplayFormatDate($InvoiceDate[0]['Inv_Date']);
				}
				else 
				{
					//$mailSubject = $sBillTypeText . ' Bill For : ' . $obj_fetch->GetBillFor($periodID) . ' (Please Ignore the previous mail)' ;
					$mailSubject = $sBillTypeText . ' Bill For : ' . $obj_fetch->GetBillFor($periodID);	
				}//'Maintainance Bill For March';
				$mailBody ="";
				// $mailBody = 'Attached Maintainance Bill For ' . $obj_fetch->GetBillFor($_REQUEST["period"]) .'<br />';
				//Dear '.$mailToName. ',<br /><br />Please find attached  '.$billText.'  bill for ' . $obj_fetch->GetBillFor($periodID) .'.<br />
				/*$billText = "Maintenance"	;
				if($Supplemenary_bills == '1')
				{
					$billText = "Supplementary";
				}*/
				$societyEmailID = $obj_fetch->objSocietyDetails->sSocietyEmail;
				 if($societyEmailID == '')
				 {
					 $societyEmail = "techsupport@way2society.com";
				 }
				if($obj_fetch->objSocietyDetails->sSocietySendBillAsLink == 1)
				 {
					
					if($_REQUEST['BT'] == TABLE_SALESINVOICE)
					{

					
					$mailBodyHeader= " Dear ".$mailToName.",<br /><br />".$sBillTypeText."  bill of &nbsp;".$obj_fetch->objSocietyDetails->sSocietyName." &nbsp;for " . getDisplayFormatDate($InvoiceDate[0]['Inv_Date']).' has been generated.<br />';							
					}
					else
					{
					$mailBodyHeader= " Dear ".$mailToName.",<br /><br />".$sBillTypeText."  bill of  &nbsp;".$obj_fetch->objSocietyDetails->sSocietyName."  &nbsp;for " . $obj_fetch->GetBillFor($periodID).' has been generated.<br />';
					}
				 }
				 else
				 {
					
					 $mailBodyHeader= " Dear ".$mailToName.",<br /><br />Please find attached ".$sBillTypeText."  bill of  &nbsp;".$obj_fetch->objSocietyDetails->sSocietyName. " &nbsp; for " . $obj_fetch->GetBillFor($periodID).'.<br />';
				 }
				
				$mailBody .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
							<html xmlns="http://www.w3.org/1999/xhtml">
							 <head>
							  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />  
							  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
							</head>
							<body style="margin: 0; padding: 0;">					 
								<table align="center" border="1" bordercolor="#CCCCCC" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;">
								   <tr>
									 <td align="center" bgcolor="#D9EDF7" style="padding: 30px 0 20px 0;border-bottom:none;">
									   <img src="http://way2society.com/images/logo.png" alt="Way2Society.com"  style="display: block;" />
									  <br />
									  <i><font color="#43729F" size="4"><b> Way2Society.com - Housing Society Social & Accounting Software </b></font></i>
									 </td>
								   </tr>
								   <tr>
									 <td bgcolor="#ffffff" style="padding-top:20px; padding-bottom:20px; padding-left:10px; padding-right:10px;border-top:none;border-bottom:none;" >
									   <table width="100%">
									   
										<tr><td>'.$ClientDetails[0]['email_header'].'</td></tr>
										<tr><td>'.$mailBodyHeader.'<br />';
									//if($isNewAccountCreate != true)
									//{
										if($obj_fetch->objSocietyDetails->sSocietySendBillAsLink == "1")
									{
										
										if($obj_fetch->objSocietyDetails->sAccounting_Only == "0")
										{
										if($newUserUrl <> "")
											{
												if($_REQUEST['BT']== TABLE_SALESINVOICE)
												{
												$mailBody .= '<tr>
												<td>Please click below button to activate your way2society.com account and to check '.$sBillTypeText.'  Bill for ' .getDisplayFormatDate($InvoiceDate[0]['Inv_Date']).'</td>									
											</tr>';
												}
												else
													{
													$mailBody .= '<tr>
													<td>Please click below button to activate your way2society.com account and to check '.$sBillTypeText.'  Bill for ' . $obj_fetch->GetBillFor($periodID).'</td></tr>';
													}
											
											$mailBody .= '<tr><td>Your username is your email id :  '.$mailToEmail.' and you can set password of your choice.</td></tr>
											<tr><td><br /></td></tr>';
												}
										else
										{
											if($_REQUEST['BT']== TABLE_SALESINVOICE)
											{
												$mailBody .= '<tr>
												<td>Please click below button to check '.$sBillTypeText.'  Bill for ' . getDisplayFormatDate($InvoiceDate[0]['Inv_Date']).'</td></tr><tr><td><br/></td></tr>';
											}
											else
											{
												$mailBody .= '<tr>
												<td>Please click below button to check '.$sBillTypeText.'  Bill for ' . $obj_fetch->GetBillFor($periodID).'</td>																		                                                </tr>
												<tr><td><br/></td></tr>';
											}
										}
									  }
										else
										{
											if($_REQUEST['BT']== TABLE_SALESINVOICE)
											{
												$mailBody .= '<tr>
												<td>Please find below attachment to check '.$sBillTypeText.'  Bill for ' . getDisplayFormatDate($InvoiceDate[0]['Inv_Date']).'</td></tr><tr><td><br/></td></tr>';
											}
											else
											{
												$mailBody .= '<tr>
												<td>Please find below attachment to check '.$sBillTypeText.'  Bill for ' . $obj_fetch->GetBillFor($periodID).'</td><tr>
												<tr><td><br/></td></tr>';
											}
										}
											//if($newUserUrl <> "")
											//{
										//$mailBody .='<br /><br /><tr  align="center"><td colspan="3">
                                       /*<table style="height:50px;"><tr style="background-color:#f8f8f8; height:40px;">
                                       <td align="center" style="width:160px;"><span style="font-size:16px">Access Code :</span></td>
                                       <td align="center" style="background-color:rgb(217, 237, 247);width: 200px;height: 40px;"><span style="font-size:16px">'.$sActivCode.'</span></td>
                                       </tr></table></td></tr><tr><td><br></td></tr>';*/	
											//}
										if($obj_fetch->objSocietyDetails->sAccounting_Only == "0")
                                         {
											if($newUserUrl <> "")
											{
												$mailBody .= '<tr>
												<td align="center">
													<table width="250px" cellspacing="0" cellpadding="0" border="0">
														<tbody>
															<tr>
																<td valign="middle" bgcolor="#337AB7" height="40" align="center" style="cursor:pointer;">
																	<a href="'.$userURL.'"  style="cursor:pointer;text-decoration: none;">
																		<div style="cursor:pointer;text-decoration: none;" onClick=\'window.open("//'.$userURL.'");\' >
																			<font  style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif;">';
																			if($_REQUEST['BT'] == TABLE_SALESINVOICE)
																			{
																				$mailBody .= '<b>Activate + View Invoice</b>';
																			}
																			else
																			{
																				$mailBody .= '<b>Activate + View Bill</b>';
																			}
																			$mailBody .= '</font>
																		</div>	
																	</a>
															  </td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>';
											}
											else
											{
											$mailBody .= '<tr>
												<td align="center">
													<table width="250px" cellspacing="0" cellpadding="0" border="0">
														<tbody>
															<tr>
																<td valign="middle" bgcolor="#337AB7" height="40" align="center" style="cursor:pointer;">
																	<a href="'.$userURL.'"  style="cursor:pointer;text-decoration: none;">
																		<div style="cursor:pointer;text-decoration: none;" onClick=\'window.open("//'.$userURL.'");\' >
																			<font  style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif;">';
																			if($_REQUEST['BT'] == TABLE_SALESINVOICE)
																			{
																				$mailBody .= '<b>View Invoice</b>';
																			}
																			else
																			{
																				$mailBody .= '<b>View Bill</b>';
																			}
																			$mailBody .= '</font>
																		</div>
																	</a>
															  </td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>';
											}
										}
											

									
							if($newUserUrl <> "" && $obj_fetch->objSocietyDetails->sAccounting_Only == "0" )
											{
											$mailBody .='<tr><td><br></td></tr>
										<tr><td colspan="3">If you already have a Way2Society.com user login id and want to link this unit with it, then please login to your Way2Society account and click on the Link "Have A New Code To Link Another Society/Flat ?" and enter this activation code. </td></tr>  
										<tr><td><br></td></tr>
										<tr  align="center"><td colspan="3">
                                       <table style="height:50px;"><tr style="background-color:#f8f8f8; height:50px;">
                                       <td align="center" style="width:160px;height: 45px;"><span style="font-size:16px">Web Access Code :</span></td>
                                       <td align="center" style="background-color:rgb(217, 237, 247);width: 340px;height: 45px;"><span style="font-size:16px">'.$mailToEmail.''.$sActivCode.'</span></td>
                                       </tr></table>
									   </td></tr><tr><td><br></td></tr><tr><td>Alternativaly, You can install mobile App "Way2Society" from Google Play Store or Apple App Store. Click on "New Account Activation" link at bottom of login screen and use following Activation Code when prompted during installation.</td></tr><tr><td>Your username is your email id :  '.$mailToEmail.' and you can set password of your choice. </td></tr>
									   <tr><td><br></td></tr><tr  align="center"><td colspan="3">
                                       		<table style="height:50px;"><tr style="background-color:#f8f8f8; height:40px;">
                                      		<td align="center" style="width:160px;"><span style="font-size:16px">Mobile Access Code :</span></td>
                                       <td align="center" style="background-color:rgb(217, 237, 247);width: 200px;height: 40px;"><span style="font-size:16px">'.$sActivCode.'</span></td><td>&nbsp;&nbsp;&nbsp;</td><td><a rel="nofollow" target="_blank" href="https://play.google.com/store/apps/details?id=com.ionicframework.way2society869487&amp;rdid=com.ionicframework.way2society869487">
										<img src="http://way2society.com/images/app.png" width="120" height="50" style="" class="yiv1843970569ycb7204091656"></a></td>
                                       </tr></table></td></tr><tr><td><br></td></tr>';
											}
									}
							  if( $obj_fetch->objSocietyDetails->sAccounting_Only == "0" )
							  {

										$mailBody .= '<tr><td>If you have made NEFT payment, please click below button to enter NEFT transaction details. </td>                   
										</tr>
										<tr><td><br /></td></tr>
										<tr>
											<td align="center">
												<table width="250px" cellspacing="0" cellpadding="0" border="0">
													<tbody>
														<tr>
															<td valign="middle" bgcolor="#337AB7" height="40" align="center">
																<a target="_blank" style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif" href="'.$neftURL.'">Enter NEFT transaction details</a>
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
										<tr><td><br /></td></tr>';
										
									$mailBody .= '<tr>
											<td>'.$sText.'</td>									
										</tr>
										<tr><td><br /></td></tr>
										<tr>
											<td align="center">
												<table width="200px" cellspacing="0" cellpadding="0" border="0">
													<tbody>
														<tr>
															<td valign="middle" bgcolor="#337AB7" height="40" align="center" style="cursor:pointer;">
																<a href="'.$onclickURL.'"  style="cursor:pointer;text-decoration: none;">
																	<div style="cursor:pointer;text-decoration: none;" onClick=\'window.open("//'.$onclickURL.'");\' >
																		<font  style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif;"><b>'.$buttonValue.'</b></font>
																	</div>
																</a>
	                                                      </td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
										<tr><td><br /></td></tr>
										<tr><td align="center">
                                            <table style="background-color:#f8f8f8; padding-top:10px; padding-bottom:10px;" width="100%">
                                            <tr ><td align="center"><span style="font-size:14px;font-weight:bold">NEFT Payment</span>
                                            </td>
                                            <td align="center"><span style="font-size:14px;font-weight:bold">Check Maintenance Bill</span>
                                            </td></tr>
                                            <tr><td  align="center">
											<video poster="../images/images.png" width="100%" height="50%" controls="controls">
											<a href="https://www.youtube.com/watch?v=-KNLJ44TB8Q" >
											<img src="http://way2society.com/images/NEFT.jpg" width="250px" height="140px" alt="image instead of video" />
											</a>
											</video>
											</td>
                                            <td  align="center">
											<video poster="../images/images.png" width="100%" height="50%" controls="controls">
											<a href="https://www.youtube.com/watch?v=38RXe5k-fFM&t=4s" >
										<img src="http://way2society.com/images/VIEW_BILLS.jpg" width="250px" height="140px" alt="image instead of video" />
											</a>
											</video>
											</td></tr>
                                            </table></td></tr>
										<tr><td><br /></td></tr>';
										if($newUserUrl <> "")
										{
										$mailBody .='<tr><td>If you are a new user, we will take you through a simple process to create your account. </td></tr>';
										}
										$mailBody .='<tr><td><br /></td></tr>
										<tr><td>'.$EmailFooter.'</td></tr>
										<tr><td>Email : '.$societyEmailID .'</td></tr>';
										if($_SESSION['society_client_id'] == "1" || $_SESSION['society_client_id'] == "9")
										{
											 $mailBody .= '<tr><td>Contact No : '. $socContactNo .'</td></tr>';	
										}
										else if(strlen($socContactNo) > 0)
										{
											 $mailBody .= '<tr><td>Contact No : '. $socContactNo .'</td></tr>';	
										}
									}


										$mailBody .= '<tr>
										<td><br /></td>
										</tr>
										<tr>
										<td style="color:#808080;font-size:10px" >
										Note: This is an automated email. Sometimes spam filters block automated emails. If you do not find the email in your inbox, please check your spam filter or bulk email folder. Mark this email as "Not Spam" to get further emails in inbox.
										</td>
										</tr>
									   </table>
									 </td>
								   </tr>
								   <tr>
									 <td bgcolor="#CCCCCC" style="padding: 2px 20px 2px 20px;border-top:none;">
									   <table cellpadding="0" cellspacing="0" width="100%">           
										 <td >             
											<a href="https://way2society.com/" target="_blank"><i>Way2Society</i></a>              
										 </td>
										 <td align="center"  style="padding: 0px 50px 0px 1px;">
								 		<table>
                                 		<tr>
                                 		<td><a href="https://play.google.com/store/apps/details?id=com.ionicframework.way2society869487&amp;rdid=com.ionicframework.way2society869487" target="_blank">
										<img src="http://way2society.com/images/app.png" width="120" height="50" style="style=" top:10px;"></a></td>
										<td><a href="https://itunes.apple.com/in/app/way2society/id1389751648?mt=8" target="_blank">
										<img src="http://way2society.com/images/ios.png" width="120" height="50" style="style=" top:10px;"></a></td></tr>				
										</table>
                                	 </td>
										 <td align="right">
										  <table border="0" cellpadding="0" cellspacing="0">
										   <tr>
											<td>
												<a href="https://twitter.com/way2society" target="_blank"><img src="http://way2society.com/images/icon2.jpg" alt=""></a>                  
											</td>
											<td style="font-size: 0; line-height: 0;" width="20">&nbsp;&nbsp;</td>
											<td>
												<a href="https://www.facebook.com/way2soc" target="_blank"><img src="http://way2society.com/images/icon1.jpg" alt=""></a>                 
											</td>
										   </tr>
										  </table>
										 </td>             
									   </table>
									 </td>
								   </tr>
								 </table>   
							</body>
							</html>';
								
				
			$unitNo = $obj_fetch->GetUnitNumber($unitID);
			$specialChars = array('/','.', '*', '%', '&', ',', '(', ')', '"');
			$unitNo = str_replace($specialChars,'',$unitNo);
			
			//$baseDir = dirname( dirname(__FILE__) );
			$baseDir =( __DIR__ );
			$baseDir = substr($baseDir, 0,strrpos($baseDir, '/') );
			
			$fileName =  $baseDir . "/maintenance_bills/" . $obj_fetch->objSocietyDetails->sSocietyCode . "/" . $obj_fetch->GetBillFor($periodID) . "/bill-" . $obj_fetch->objSocietyDetails->sSocietyCode . '-' . $unitNo . "-" . $obj_fetch->GetBillFor($periodID) . '-'.$Supplemenary_bills.'.pdf';
			//$fileName =  $baseDir . "\\maintenance_bills\\DVT\\April-2016-17\\bill-DVT-101-April-2016-17-0.pdf";
			
			if(!file_exists($fileName) && $obj_fetch->objSocietyDetails->sSocietySendBillAsLink == 0)
			{
				//echo "<BR>Bill PDF " . $fileName . " does not exist.";
				$ResultAry[$unitID] = "Bill PDF " . $fileName . " does not exist.";
				//return;
			}
			else
			{
				if($debug_trace == 1)
				{
					echo "<BR>1.Attaching Bill PDF " . $fileName;
				}
			}
			$EMailIDToUse = $obj_Utility->GetEmailIDToUse(true, 0, $periodID, $unitID, $CronJobProcess, $DBName, $societyID, 0, 0);

			/*echo '<pre>';
			print_r($EMailIDToUse);
			echo '</pre>';*/
/*

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
 
$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
    //Server settings
    $mail->SMTPDebug = 2;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = '103.50.162.146';                   // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'test@way2society.com';              // SMTP username
    $mail->Password = 'test123';                           // SMTP password
    //$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to
 
    //Recipients
    $mail->setFrom('accounts@way2society.com', 'W2S-Accts');          //This is the email your form sends From
    $mail->addAddress('prashant@way2society.com', 'Mail Test send'); // Add a recipient address
    //$mail->addAddress('contact@example.com');               // Name is optional
    //$mail->addReplyTo('info@example.com', 'Information');
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');
 
    //Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
 
    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Test Email';
    $mail->Body    = 'The test email worked!!';
    //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
 
    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}
*/

			if($EMailIDToUse['status'] == 0)
			{
				$EMailID = $EMailIDToUse['email'];
				$Password = $EMailIDToUse['password'];
				//$EMailID = "test@way2society.com";
				//$Password = "test123";
				
				// Create the mail transport configuration
				//$transport = Swift_SmtpTransport::newInstance('103.50.162.146',25, "ssl")
				//$transport = Swift_SmtpTransport::newInstance('103.50.162.146',25)



/*				$transport = Swift_SmtpTransport::newInstance('103.50.162.146', 587)
					  ->setUsername($EMailID)
					  ->setSourceIp('0.0.0.0')
					  ->setPassword($Password) ;	 
					  */
				//These settings are for AWS TLS. 		
				//$SMTP_Username = "AKIAWORPNMPGX76CCAPQ";
				//$SMTP_Password = "BOwueG82ahzTYrSgK5igS9qChzA6KKF35obJEEvXTrGe";
				//$SMTP_endpoint = "email-smtp.ap-south-1.amazonaws.com";
				//$SMTP_Port = 587;
				//$SMTP_Security = "tls";

				// send bill notification through gmail account for FLYEDGE CO-OP PREMISES SOCIETY LTD 
				if(($_SESSION['society_id'] == 422))
				{
					$AWS_Config = GmailCommanEmailConfig();

				}
				else
				{
					$AWS_Config = CommanEmailConfig();
				}
				//print_r($AWS_Config );
				 $transport = Swift_SmtpTransport::newInstance($AWS_Config[0]['Endpoint'],$AWS_Config[0]['Port'] , $AWS_Config[0]['Security'],$AWS_Config[0]['Name'])
				  ->setUsername($AWS_Config[0]['Username'])
				  ->setPassword($AWS_Config[0]['Password']);
			//	$transport = Swift_SmtpTransport::newInstance($SMTP_endpoint, $SMTP_Port, $SMTP_Security)
				//  ->setUsername($SMTP_Username)
				 // ->setPassword($SMTP_Password);	 
				 
				// Create the message
				$message = Swift_Message::newInstance();
				$message->setTo(array(
				  $mailToEmail => $mailToName
				 ));
				 
				 $societyEmail = $obj_fetch->objSocietyDetails->sSocietyEmail;
				 if($societyEmail == '')
				 {
					$societyEmail = "techsupport@way2society.com";
				 }
				 
				 $societyName = $obj_fetch->objSocietyDetails->sSocietyName;
						
				 $society_CCEmail = $obj_fetch->objSocietyDetails->sSocietyCC_Email;
				 if($society_CCEmail <> "")
				 {
					// $message->setCc(array($society_CCEmail => $societyName));
					$message->setReplyTo(array($societyEmail => $societyName,$society_CCEmail => $societyName));
				 }
				 else
				 {		
					 $message->setReplyTo(array(
					   $societyEmail => $societyName
					));
				 }
				 
				$message->setSubject($mailSubject);
				$message->setBody($mailBody);
				$message->setFrom("no-reply@way2society.com", $obj_fetch->objSocietyDetails->sSocietyName);
				
				$message->setContentType("text/html");	
				if($obj_fetch->objSocietyDetails->sSocietySendBillAsLink == 0)
				{
					if(file_exists($fileName))
					{
						if($debug_trace == 1)
						{
							echo "<BR>Attaching_ : " . $fileName; 
						}
						$message->attach(Swift_Attachment::fromPath($fileName));
					}
					else
					{
						if($debug_trace == 1)
						{
							echo "<BR>Attachment doesnt exist : " . $fileName ; 
						}
					}
				}
				// Attach circular file with email
				if(($obj_fetch->objSocietyDetails->sSocietyCode == "371") && ($periodID == 23)) //paras
				{
					$Attachment_fileName =  $baseDir . "/maintenance_bills/Doc/ActivationOfNewAccount.pdf";
					
					
					if(!file_exists($Attachment_fileName))
					{
						$ResultAry[$unitID] = "Activation Help file does not exist.";
						if($debug_trace == 1)
						{
							echo "<BR>Attachment doesnt exist : " . $Attachment_fileName ; 
		
						}
						//return;
					}
					else
					{
						if($debug_trace == 1)
						{
							echo "<BR>2.Attaching_ : " . $Attachment_fileName ; 
		
						}
						//echo "<BR>Attaching";
						$message->attach(Swift_Attachment::fromPath($Attachment_fileName));
					}										
				}
				
				// Attach circular file with email
				if(($obj_fetch->objSocietyDetails->sSocietyCode == "333") && ($periodID == 5)) //Manavsthal
				{
					$Attachment_fileName =  $baseDir . "/maintenance_bills/" . $obj_fetch->objSocietyDetails->sSocietyCode . "/Notices/" . 'Notice20200711.pdf';
					
					
					if(!file_exists($Attachment_fileName))
					{
						$ResultAry[$unitID] = "Notice file does not exist.";
						if($debug_trace == 1)
						{
							echo "<BR>Attachment doesnt exist : " . $Attachment_fileName ; 
		
						}
						//return;
					}
					else
					{
						if($debug_trace == 1)
						{
							echo "<BR>2.Attaching_ : " . $Attachment_fileName ; 
		
						}
						//echo "<BR>Attaching";
						$message->attach(Swift_Attachment::fromPath($Attachment_fileName));
					}										

					//File No 2
					$Attachment_fileName2 =  $baseDir . "/maintenance_bills/" . $obj_fetch->objSocietyDetails->sSocietyCode . "/Notices/" . 'ClarificationNotice.pdf';
					
					
					if(!file_exists($Attachment_fileName2))
					{
						$ResultAry[$unitID] = "Notice file does not exist.";
						if($debug_trace == 1)
						{
							echo "<BR>Attachment doesnt exist : " . $Attachment_fileName2 ; 
		
						}
						//return;
					}
					else
					{
						if($debug_trace == 1)
						{
							echo "<BR>3.Attaching : " . $Attachment_fileName2 ; 
		
						}
						//echo "<BR>Attaching";
						$message->attach(Swift_Attachment::fromPath($Attachment_fileName2));
					}			
												
					//File No 3
					$Attachment_fileName3 =  $baseDir . "/maintenance_bills/" . $obj_fetch->objSocietyDetails->sSocietyCode . "/Notices/" . 'VisitorDeclaration.pdf';
					
					
					if(!file_exists($Attachment_fileName3))
					{
						$ResultAry[$unitID] = "Notice file does not exist.";
						if($debug_trace == 1)
						{
							echo "<BR>Attachment doesnt exist : " . $Attachment_fileName3; 
		
						}
						//return;
					}
					else
					{
						if($debug_trace == 1)
						{
							echo "<BR>4.Attaching : " . $Attachment_fileName3; 
		
						}
						//echo "<BR>Attaching";
						$message->attach(Swift_Attachment::fromPath($Attachment_fileName3));
					}																				
				}

				$mailer = Swift_Mailer::newInstance($transport);
				$result = $mailer->send($message);
				if($debug_trace == 1)
				{
					echo "<BR>Send Result " . $result;
				}
				if($result >= 1)
				{
					date_default_timezone_set('Asia/Kolkata');	
					$current_dateTime = date('Y-m-d H:i:s ');
					if($CronJobProcess)
					{
						$sql = "INSERT INTO `notification`(`UnitID`, `PeriodID`, `SentBillEmailDate`, `SentBy`, `Mem_Other_ID`) VALUES ('" . $unitID . "','" . $periodID . "','" . $current_dateTime . "','-1', '" . $OtherMemberEmails[2] . "')";
					}
					else
					{
						$sql = "INSERT INTO `notification`(`UnitID`, `PeriodID`, `SentBillEmailDate`, `SentBy`, `Mem_Other_ID`) VALUES ('" . $unitID . "','" . $periodID . "','" . $current_dateTime . "','" . $_SESSION['login_id'] . "', '" . $OtherMemberEmails[2] . "')";
					}
					$obj_fetch->m_dbConn->insert($sql);
					$ResultAry[$unitID] = "Success";
					
				}
				else
				{
					$ResultAry[$unitID] = "Failed";
					
				}
				if($CronJobProcess)
				{
					$sqlUpdate = "Update `emailqueue` set `Status`=1 WHERE `id` = '".$QueueID."'"; 
					$dbConnRoot->update($sqlUpdate);
				}
			}
			else
			{
				$ResultAry[$unitID] = $EMailIDToUse['msg'];
			}
			//**
		}
		
	}
		
	}
	catch(Exception $exp)
	{
		if($debug_trace == 1)
		{
			echo $exp->getMessage();
		}
		$EmailSourceModule = 0;
		$SQL_query_existCheck = "select * from `emailqueue` where `dbName`='".$DBName."' and `PeriodID`='".$periodID."' and `SocietyID`='".$societyID."' and `UnitID`='".$unitID."' and `Status`=0 and `ModuleTypeID`='".$EmailSourceModule."'";
		$SQL_query_existCheckRes = $dbConnRoot->select($SQL_query_existCheck);
		//echo "Already exist count:".sizeof($SQL_query_existCheckRes);
		if(sizeof($SQL_query_existCheckRes) == 0)
		{
			$queue_query = "insert into `emailqueue`(`dbName`,`PeriodID`, `SocietyID`, `UnitID`, `ModuleTypeID`,`Mode`) values ('".$DBName."','".$periodID."','".$societyID."','".$unitID."','".$EmailSourceModule."','".$_REQUEST['BT']."')";
			//echo "<br/>".$queue_query;
			$dbConnRoot->insert($queue_query);
			//var_dump($exp);
			$ResultAry[$unitID] = "Message added into email queue. ECode : " . $exp->getCode();
			//return;
		}
		else{
			$ResultAry[$unitID] = "Already in email queue. ECode : " . $exp->getCode();  //$exp->getMessage() ;
		}
	}

}

/******************************************* */
/******************************************* */
function getResult($mMysqli1, $sqlQuery)
	{
		$result = $mMysqli1->query($sqlQuery);						
		if($result)
		{
			$count = 0;
			while($row = $result->fetch_array(MYSQL_ASSOC))
			{
				$data[$count] = $row;
				$count++;
			}											
		}
		//echo "d";	
		return $data;	
	}		


	function SendBillReminderEMAIL($dbName, $societyID, $societyName, $societyCode, $periodID, $dueDate, $unitID=0,&$ResultAry)
	{
		include_once ("dbconst.class.php");
		include_once ("include/fetch_data.php");
		include_once ("include/dbop.class.php");
		include_once ("utility.class.php");
		include_once ("initialize.class.php");
		include_once ("email_format.class.php");
		//require_once ("swift/swift_required.php");

		
		$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		
		//$objFetchData = new FetchData($dbConn);
		$obj_Utility = new utility($dbConn, $dbConnRoot);
		//$obj_initialize = new initialize($dbConnRoot);
		$obj_fetch = new FetchData($dbConn);
		date_default_timezone_set('Asia/Kolkata');
		
		//echo '<br/><br/>Connecting DB : ' . $dbName;								
		$mMysqli1 = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, $dbName);
		
		if(!$mMysqli1)
		{
			//echo '<br/>Connection Failed';
		}
		else
		{
			//echo '<br/>Connected';	

		if($unitID == 0)
		{	
			$Emailunits = getResult($mMysqli1,"SELECT u.id as id, u.unit_no,mm.email,u.unit_id as unit_id,mm.unit ,u.society_id as society_id, mm.owner_name as owner_name,SUM(a.Debit) as Debit , SUM(a.Credit) as Credit, (SUM(Debit) - SUM(Credit)) as Total FROM `unit` AS u JOIN `member_main` AS mm JOIN `assetregister` as a ON unit_id = mm.unit WHERE unit_id = a.LedgerID AND u.society_id ='".$societyID."' and mm.ownership_status= 1 group by unit_id");	
		}
		else
		{
				$Emailunits = getResult($mMysqli1,"SELECT u.id as id, u.unit_no,mm.email,u.unit_id as unit_id,mm.unit ,u.society_id as society_id, mm.owner_name as owner_name,SUM(a.Debit) as Debit , SUM(a.Credit) as Credit, (SUM(Debit) - SUM(Credit)) as Total FROM `unit` AS u JOIN `member_main` AS mm JOIN `assetregister` as a ON unit_id = mm.unit WHERE unit_id = a.LedgerID AND u.society_id ='".$societyID."' and mm.unit='".$unitID."' and mm.ownership_status= 1 group by unit_id");	
		}
			$msgEmail = "<b>DBNAME : </b>". $dbName ."<br /><b> SOCIETY : </b>".$societyName."<br /><b> START TIME : </b>".date('Y-m-d h:i:s ')."<br /><br />";
		
			$GetBillDate = getResult($mMysqli1,"SELECT BillDate FROM `billregister` where PeriodID ='".$periodID."'");
			
			$billDate = date("d-m-Y", strtotime($GetBillDate[0]['BillDate']));
			$dueDate = date("d-m-Y", strtotime($dueDate));
			$EMailIDToUse = $obj_Utility->GetEmailIDToUse(false,0,0,0,0,'',$societyID,0,0);
			

			$SocietyEmailDetails = getResult($mMysqli1, "SELECT `email` FROM `society` WHERE `society_id` = '".$societyID."'");
			//print_r($SocietyEmailDetails);   //society email id

			$mailToEmail1 =array();
			//echo sizeof($Emailunits);

			$memberMailSubject = "Reminder to pay due amount";

			/*************Society Email id array*************** */
			$mailToName = $MemberName;
			//echo  $mailToName;
			$Email = getResult($mMysqli1, "SELECT mm.email FROM `unit` AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit WHERE u.society_id = '".$societyID."'");

			
			//print_r($arrayemail);
			/************************* */

			for($i = 0; $i < sizeof($Emailunits); $i++)			
			{
				$mailToEmail = $Emailunits[$i]['email'];
				$mailToName = $Emailunits[$i]['owner_name'];
				$unitID = $Emailunits[$i]['unit_id'];
				$unitno = $Emailunits[$i]['unit_no'];
				//$societyDetails = $obj_fetch->GetSocietyDetails($obj_fetch->GetSocietyID($unitID));	
				
				//echo $mailToName;
				if($Emailunits[$i]['email'] <> "")
				{				
					
					$mailBody = '<b> Dear '.' '.$Emailunits[$i]['owner_name'].'</b>,'.'<br/><br/>'. 'Your bill for amount Rs.'.'<b>' . $Emailunits[$i]['Total'] .'</b>'. ' dated '.'<b>' . $billDate .'</b>'. ' is due for payment.'.'<br/>Kindly pay your bill before the due date of <b>'.$dueDate.'</b>'.' to avoid interest charges. <br/>Please ignore this mail, if already paid.<br/><br/>'.'Best Regards ,'.'<br/>'.'Managing Committee <br/>'.$societyName.'<br/><br/>';	
					  //print_r($mailBody);

					$msgEmail = "<b>** INFORMATION ** </b>Unit - '".$Emailunits[$i]['unit_no']."' : Message Sent... <br /><br />";
					//print_r($msgEmail);
					fwrite($Logfile,$msg);					
				}
				else
				{
					$msg = "<b>** ERROR ** </b>Unit - '".$Emailunits[$i]['unit_no']."' : Invalid Email. <br /><br />";
					fwrite($Logfile,$msg);
				}
				
				   		 						
			//}
			//echo $mailToEmail;
			
			$socAddress = $obj_fetch->objSocietyDetails->sSocietyAddress;
			
			/******************/
			$Supplemenary_bills = 0;
			$baseDir = dirname( dirname(__FILE__) );
			
		$specialChars = array('/','.', '*', '%', '&', ',', '(', ')', '"');
			$unitno = str_replace($specialChars,'',$unitno);
	
	    $fileName =  $baseDir . "/maintenance_bills/" . $societyCode . "/" . $obj_fetch->GetBillFor($periodID) . "/bill-" . $societyCode . '-' . $unitno . "-" . $obj_fetch->GetBillFor($periodID) . '-'.$Supplemenary_bills.'.pdf';
		
			
			
			try
			{
			   if($EMailIDToUse['status'] == 0)
			   {
					$EMailID = $EMailIDToUse['email'];
					$Password = $EMailIDToUse['password'];		
				   
				  // $transport = Swift_SmtpTransport::newInstance('103.50.162.146',587)
					// ->setUsername($EMailID)
					// ->setSourceIp('0.0.0.0')
					// ->setPassword($Password);
				  $AWS_Config = CommanEmailConfig();
				  $transport = Swift_SmtpTransport::newInstance($AWS_Config[0]['Endpoint'],$AWS_Config[0]['Port'] , $AWS_Config[0]['Security'],$AWS_Config[0]['Name'])
				        ->setUsername($AWS_Config[0]['Username'])
				        ->setPassword($AWS_Config[0]['Password']);
						
					 $emailContent = GetEmailHeader() . $mailBody . GetEmailFooter() ;
					
					 $message = Swift_Message::newInstance();
					 $message->setTo(array($mailToEmail => $mailToName));
				  
					 $message->setSubject($memberMailSubject);
					 $message->setBody($emailContent);
					 //echo "file name ::".$fileName;
					//if($obj_fetch->objSocietyDetails->sSocietySendBillAsLink == 0)
					//{
						//echo "<br>File Path :".$fileName;
						$message->attach(Swift_Attachment::fromPath($fileName));
					//}
					 $message->setFrom(array('no-reply@way2society.com' => 'way2society'));
					//  $message->setCc(array('techsupport@way2society.com' => 'Tech Support'));
					//  $message->setCc(array('cs@way2society.com' => 'way2society'));
					 $message->setContentType("text/html");	
					
							
					$mailer = Swift_Mailer::newInstance($transport);
					$result = $mailer->send($message);	
					//echo $result;										
					if($result > 0)
					{								
						$ResultAry[$unitID] = "Success";
						//echo "Success";	
					}
					else
					{
						$ResultAry[$unitID] = "Field";
						//echo "Field";
					 }
					}
				}
				catch(exception $e)
				{
					//echo "error occured in send function".$e->getMessage();
					//echo "<br>".$e;
				}
			}					 
		}
		$msg = "<b> END TIME : </b>".date('Y-m-d h:i:s ')."<br /><hr />";
		fwrite($Logfile,$msg);
				
			//	return $ResultAry[$unitID];
			}	


/****************************** */

/* 
SELECT u.id as id, u.unit_no,mm.email,u.unit_id,mm.unit ,u.society_id as society_id, mm.owner_name as owner_name,SUM(a.Debit) as Debit , SUM(a.Credit) as Credit, (SUM(Debit) - SUM(Credit)) as Total FROM `unit` AS u JOIN `member_main` AS mm JOIN `assetregister` as a ON u.unit_id = mm.unit WHERE u.unit_id = a.LedgerID AND u.society_id = 59 group by unit_id


SELECT * FROM `loginemailids` WHERE `client_id`=2
 lastused =1
 EmailSentCounter=1
  

notice.class.php
utility.class.php
notification.php
*/

function CommanEmailConfig()
{
	$response = array();
	//$SMTP_Username = "AKIAWORPNMPGX76CCAPQ";
	//$SMTP_Password = "BOwueG82ahzTYrSgK5igS9qChzA6KKF35obJEEvXTrGe";
	$SMTP_Name = "amazonses.com";
	$SMTP_Username = "AKIAWORPNMPGVXHVQM4C";
	$SMTP_Password = "BAe+7wu2ry9dP8zO7irJKjdBCtObWbOYAZV71nK56Ymn";
	$SMTP_endpoint = "email-smtp.ap-south-1.amazonaws.com";
	$SMTP_Port = 587;
	$SMTP_Security = "tls";
	array_push($response,array("Username"=>$SMTP_Username, "Password"=> $SMTP_Password, "Endpoint"=>$SMTP_endpoint, "Port"=>$SMTP_Port, "Security"=>$SMTP_Security,"Name"=>$SMTP_Name));
	return $response; 
}
function GmailCommanEmailConfig()
{
	$response = array();
	$SMTP_Name = "";
	$SMTP_Username = "flyedge.cpsl@gmail.com";
	$SMTP_Password = "fmfcrjpggnthpnul";
	$SMTP_endpoint = "smtp.gmail.com";
	$SMTP_Port = 587;
	$SMTP_Security = "tls";
	array_push($response,array("Username"=>$SMTP_Username, "Password"=> $SMTP_Password, "Endpoint"=>$SMTP_endpoint, "Port"=>$SMTP_Port, "Security"=>$SMTP_Security,"Name"=>$SMTP_Name));
	return $response; 
}
?>

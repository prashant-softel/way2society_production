<?php
	//error_reporting(7);
	include_once "dbconst.class.php";	
	include_once "adduser.class.php";
	include_once "initialize.class.php";
	include_once "changelog.class.php";
	include_once("../GDrive.php");

	class activation
	{
		private $m_dbConn;
		public $m_dbConnRoot;
		public $obj_addduser;
		public $obj_initialize;
		//public $obj_utility;
		public $m_bShowTrace;
		function __construct($dbConn, $dbConnRoot = "")
		{
			//echo "ctor";
			$this->m_bShowTrace = 0;
			$this->m_dbConn = $dbConn;
			$this->m_dbConnRoot = $dbConnRoot;
			$this->obj_addduser = new adduser($this->m_dbConnRoot,$this->m_dbConn);
			$this->obj_initialize = new initialize($this->m_dbConnRoot);
			//$this->obj_utility = new utility($this->m_dbConn, $this->m_dbConnRoot);
		}
		
		function AddMappingAndSendActivationEmail($role, $unit_id, $society_id, $code, $NewUserEmailID, $name)
		{
			//echo "trace:";
			$result = $this->obj_addduser->addUser($role, $unit_id, $society_id, $code);
						
				//		echo "trace2".$result ;
			if($result > 0)
			{
				$ActivationStatus = $this->obj_initialize->sendNewUserActivationEmail( $NewUserEmailID, $name,$code, "1", $society_id);
				//echo "ActivationStatus:".$ActivationStatus;
				return $ActivationStatus;
			}
		}
	}
	class utility
	{
		private $m_dbConn;
		public $m_dbConnRoot;
		public $m_changelog;
		
		function __construct($dbConn, $dbConnRoot = "")
		{
			//echo "ctor";
			$this->m_dbConn = $dbConn;
			$this->m_dbConnRoot = $dbConnRoot;
			$this->m_changelog = new changeLog($this->m_dbConn);
			//$this->obj_fetch = new FetchData($this->m_dbConn);
		}
		

		function SuppBillBalanceDisplayText($PeriodID){

			$qry = "SELECT SuppBillBalanceDisplayText FROM billregister WHERE PeriodID = '$PeriodID' order by id DESC LIMIT 1";
			$res = $this->m_dbConn->select($qry);
			return $res[0]['SuppBillBalanceDisplayText'];
		}
	
		public function getSupplementDues($date, $unitID)
		{
			$sql = "SELECT * FROM (select membertbl.owner_name as member,membertbl.member_id as member_id, unittbl.unit_no as unit,(assettbl.Debit),(assettbl.Credit), assettbl.LedgerID as LedgerID, societytbl.society_id, assettbl.Date, assettbl.VoucherTypeID, unittbl.sort_order,unittbl.unit_id, wingtbl.wing from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN `billdetails` as billdet on vchrtbl.RefNo=billdet.ID JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id JOIN `society` as societytbl on unittbl.society_id=societytbl.society_id where vchrtbl.RefTableID='".TABLE_BILLREGISTER."' and billdet.BillType = ".Supplementry; 
		
			$sql .= " UNION ALL select membertbl.owner_name as member,membertbl.member_id as member_id, unittbl.unit_no as unit,(assettbl.Debit),(assettbl.Credit), assettbl.LedgerID as LedgerID, societytbl.society_id, assettbl.Date, assettbl.VoucherTypeID, unittbl.sort_order,unittbl.unit_id, wingtbl.wing from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN `credit_debit_note` as CrDrNote ON vchrtbl.RefNo = CrDrNote.ID JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id JOIN `society` as societytbl on unittbl.society_id=societytbl.society_id where vchrtbl.RefTableID='".TABLE_CREDIT_DEBIT_NOTE."' and CrDrNote.BillType = ". Supplementry; 
			
			$sql .= " UNION ALL select membertbl.owner_name as member,membertbl.member_id as member_id, unittbl.unit_no as unit,(assettbl.Debit),(assettbl.Credit), assettbl.LedgerID as LedgerID, societytbl.society_id, assettbl.Date, assettbl.VoucherTypeID, unittbl.sort_order,unittbl.unit_id, wingtbl.wing from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN `chequeentrydetails` as chqdet on (vchrtbl.RefNo=chqdet.ID) JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id JOIN `society` as societytbl on unittbl.society_id=societytbl.society_id where vchrtbl.RefTableID='".TABLE_CHEQUE_DETAILS."' and chqdet.BillType=". Supplementry;
	
			$sql .= ") A where A.unit_id IN (" . $unitID . ") and A.society_id='".$_SESSION['society_id']."' and A.Date <= '".getDBFormatDate($date)."'";
	
			$sql .= " ORDER BY A.sort_order, A.Date";
		
			$res = $this->m_dbConn->select($sql);
		
			$finalArray = array();
	
			for($iCount = 0; $iCount < sizeof($res); $iCount++)
			{
				$amount = 0;
				$amount = $finalArray[$res[$iCount]['LedgerID']]['amount'] + $res[$iCount]['Debit'] - $res[$iCount]['Credit'];
				
				$finalArray[$res[$iCount]['LedgerID']] = $res[$iCount];
				$finalArray[$res[$iCount]['LedgerID']]['amount'] = $amount;
			}
	
			$res = array();
			foreach($finalArray as $k => $v)
			{
				array_push($res, $v);
			}
	
			if(sizeof($res) > 0)
			{
				for($i = 0;$i <= sizeof($res)-1;$i++)
				{
					
					$temp = $this->getInceptionOpeningBalanceSplit($res[$i]['unit_id']);
					$Temp_Total = $temp[0]['supp_TotalBillPayable'];
					
					if(sizeof($temp) > 0)
					{
						if($temp['OpeningType'] == TRANSACTION_CREDIT)
						{
						$res[$i]['amount'] = $res[$i]['amount'] - $Temp_Total ;
						}
						else
						{
							$res[$i]['amount'] = $res[$i]['amount'] + $Temp_Total ;
						}
					}
				}
			}
			return $res;
		}

		function FetchClientDetails()
		{
		
			 $Sql = "select * from `client` where `id` = '".$_SESSION['society_client_id']."'" ;
		 	$ClientDetails->$this->m_dbConnRoot->select($Sql);
		 
			if(isset($ClientDetails) && $_SESSION['society_id'] <> 195)
			{
				$Header = $ClientDetails[0]["bill_footer"];
			}
			else
			{
				$Header = '';
			}	
			return $Header;
		}

		function reverseVoucherEntry($data)
		{
			$NewArray = array(); 
			for($i = 0 ; $i < count($data); $i++)
			{
				$NewArray[$i] = $data[$i];
				$NewArray[$i]['By'] = $data[$i]['To'];	
				$NewArray[$i]['To'] = $data[$i]['By'];	
				$NewArray[$i]['Debit'] = $data[$i]['Credit'];	
				$NewArray[$i]['Credit'] = $data[$i]['Debit'];	
			}
			return $NewArray;
			//var_dump($data);
		}
		
		function GetPrevUsedEmailID()
		{
			$EMailIDs = $this->m_dbConnRoot->select("select * from `loginemailids` where LastUsed = 1");
			//print_r($EMailIDs);
			return ($EMailIDs);
		}
		function GetEmailIDDetails($EmailID, &$id, &$MaxLimit, &$EmailIDSentCounter, &$UsedTimeStamp)
		{
			$id = $EmailID['id'];
			$MaxLimit = $EmailID['MaxLimit'];
			$EmailIDSentCounter = $EmailID['EmailSentCounter'];
			$UsedTimeStamp = $EmailID['LastUsedTimeStamp'];
		}
		
		function GetNoteType($ID)
		{
			$Result = $this->m_dbConn->select("SELECT Note_Type FROM credit_debit_note WHERE ID = '".$ID."'");
			return $Result[0]['Note_Type'];
		}
		
		public function sendVehicleAddEmail($vehicle_id, $member_id, $unit_no, $vehicleType)
		{
			
			 
			 try{
				 
			 echo "<br>Test1";
			 $Vehicle_Name = "";
			 
			 $memberDetailsQuery = "SELECT primary_owner_name, email FROM `member_main` WHERE member_id = '".$member_id."'";
			 $memberDetails = $this->m_dbConn->select($memberDetailsQuery);
			 $MemberName = $memberDetails[0]['primary_owner_name'];
			 $EMailIDToUse = $this->GetEmailIDToUse(false,0,0,0,0,$_SESSION['dbname'],$_SESSION['society_id'],0,0);
			 
			 var_dump($EMailIDToUse);
			 
			 if($vehicleType == VEHICLE_CAR)
			 {
				$Vehicle_Name = "car";
				$Vehicle_query = "SELECT * FROM mem_car_parking where mem_car_parking_id = '".$vehicle_id."'";	 
			 }
			 else
			 {
				 $Vehicle_Name = "bike";
				 $Vehicle_query = "SELECT * FROM mem_bike_parking where mem_bike_parking_id = '".$vehicle_id."'";
			}
			
			$VehicleDetails = $this->m_dbConn->select($Vehicle_query);
			
			$owner = $VehicleDetails[0][$Vehicle_Name.'_owner'];
			$reg_no = $VehicleDetails[0][$Vehicle_Name.'_reg_no'];
			$parking_slot = $VehicleDetails[0]['parking_slot'];
			$parking_type = $VehicleDetails[0]['ParkingType'];
			$parking_sticker = $VehicleDetails[0]['parking_sticker'];
			$make = $VehicleDetails[0][$Vehicle_Name.'_make'];
			$model = $VehicleDetails[0][$Vehicle_Name.'_model'];
			$color = $VehicleDetails[0][$Vehicle_Name.'_color'];
			
			$parkingTypeQuery = "SELECT ParkingType FROM parking_type WHERE id = '".$parking_type."'";
			$parkingTypeDetails = $this->m_dbConn->select($parkingTypeQuery);
			$parkingTypeName = $parkingTypeDetails[0]['ParkingType'];	 
			 
			 
			 
			 $VehicleDetailTable = "<table style='border: 1px solid black;border-collapse: collapse'>
								<tr><td style='border: 1px solid black'>".$Vehicle_Name." Owner</td>
									<td style='border: 1px solid black'>".$owner."</td>
								</tr>
								<tr><td style='border: 1px solid black'>".$Vehicle_Name." Reg_no</td>
									<td style='border: 1px solid black'>".$reg_no."</td>
								</tr>
								<tr><td style='border: 1px solid black'>Parking Slot</td>
									<td style='border: 1px solid black'>".$parking_slot."</td>
								</tr>
								<tr><td style='border: 1px solid black'>Parking Type</td>
									<td style='border: 1px solid black'>".$parkingTypeName."</td>
								</tr>
								<tr><td style='border: 1px solid black'>Parking Sticker</td>
									<td style='border: 1px solid black'>".$parking_sticker."</td>
								</tr>
								<tr><td style='border: 1px solid black'>".$Vehicle_Name." Make</td>
									<td style='border: 1px solid black'>".$make."</td>
								</tr>
								<tr><td style='border: 1px solid black'>".$Vehicle_Name." Model</td>
									<td style='border: 1px solid black'>".$model."</td>
								</tr>
								<tr><td style='border: 1px solid black'>".$Vehicle_Name." Color</td>
									<td style='border: 1px solid black'>".$color."</td>
								</tr>
							</table>";
			 
			 
			 $subject = $mailBody = "New ".$Vehicle_Name." added in unit no ".$unit_no." at ".date("d-m-Y H:i:s");
			 $mailBody .= "<br><br>";
			 $mailBody .= $VehicleDetailTable;
			 
			 $mailToEmail = $memberDetails[0]['email'];
			 $mailToName = $MemberName;
			 $societyDetails = $this->GetSocietyCode($_SESSION['society_id']);
			 $society_name = $societyDetails[0]['society_name'];
			 $society_email = $societyDetails[0]['email'];
			 
			 if(isValidEmailID($this->m_dbConn->escapeString($mailToEmail)) == true)
			 {
				if($this->m_bShowTrace == 1)
				{
					echo "<br>\n valid Email ID ".$mailToEmail;
				} 
				try
				  {
					  if($EMailIDToUse['status'] == 0)
						{
							$EMailID = $EMailIDToUse['email'];
							$Password = $EMailIDToUse['password'];
							
							include_once('email_format.class.php');
							require_once('../swift/swift_required.php');
							include_once('email.class.php');
							//$transport = Swift_SmtpTransport::newInstance('103.50.162.146',587)
		
							 // ->setUsername($EMailID)
							  //->setSourceIp('0.0.0.0')
							 // ->setPassword($Password) ;
							//AWS Config
							$AWS_Config = CommanEmailConfig();
				 			$transport = Swift_SmtpTransport::newInstance($AWS_Config[0]['Endpoint'],$AWS_Config[0]['Port'] , $AWS_Config[0]['Security'])
				 					 ->setUsername($AWS_Config[0]['Username'])
				  					 ->setPassword($AWS_Config[0]['Password']);	
									  
							$emailContent = GetEmailHeader() . $mailBody . GetEmailFooter() ;
							
							$message = Swift_Message::newInstance($transport);
							
							$message->setTo(array($mailToEmail => $mailToName));
						 
							$message->setSubject($subject);
							$message->setBody($emailContent);
							$message->setFrom(array($EMailID => 'way2society'));
							$message->setCc(array($society_email => $society_name,'cs@way2society.com' => 'way2society'));
							$message->setContentType("text/html");	
						
							$mailer = Swift_Mailer::newInstance($transport);
							$result = $mailer->send($message);
						}
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
		  }
		  catch(EXCEPTION $e)
		  {
			  echo $e->getMessage();
		  }
			
		}
		
		
		function GetEmailIDToUse($bSendEMailInQueue, $EmailSourceModule ,$PeriodID = "", $UnitID = "", $CronJobProcess = "", $DBName = "", $SocietyID = "", $iNoticeID = "", $bccUnitsArray)
		{
			//echo "bSendEMailInQueue:<".$bSendEMailInQueue .">EmailSourceModule:<".$EmailSourceModule."> PeriodID:<".$PeriodID .">UnitID <".$UnitID ."> CronJobProcess <".$CronJobProcess ."> DBName:<".$DBName .">SocietyID <". $SocietyID ."> iNoticeID<" .$iNoticeID ."> bccUnitsArray <";
			$ResultAry = array();
			error_reporting(0);
			$ResultAry['status'] = 0;
			$ResultAry['msg'] = 0;
			$ResultAry['email'] = '';
			$ResultAry['password'] = '';
		
			$ClientID = 0;

			if($SocietyID != "" && $SocietyID != 0)
			{
				$ClientQuery = $this->m_dbConnRoot->select("select client_id from `society` where society_id = '" . $SocietyID . "'");
				$ClientID = $ClientQuery[0]['client_id'];
			}
			
			$ListOfEmailIDs = $this->m_dbConnRoot->select("select * from `loginemailids` where client_id = '" . $ClientID . "'");
			$iEmailCount = sizeof($ListOfEmailIDs);
			
			//echo 'Email Count : ' . $iEmailCount;
			$sql_query = "update `loginemailids` set `EmailSentCounter`=0 where `LastUsedTimeStamp` < TIMESTAMPADD( HOUR , -1, NOW( )+ INTERVAL 5 HOUR + INTERVAL 30 MINUTE)";
			//$sql_query = "update `loginemailids` set `EmailSentCounter`=0 where `LastUsedTimeStamp` < TIMESTAMPADD( HOUR , -1, NOW( ))";
			$this->m_dbConnRoot->update($sql_query);
			
			//$EMailDetails = array();
			if($iEmailCount > 0)
			{
				//echo 'getting email ids';
				//$PrevEmailID = $this->GetPrevUsedEmailID();
				//$iNextID = $PrevEmailID[0]['id'];
				
				
				for($iEmailIDCounter = 0; $iEmailIDCounter < $iEmailCount; $iEmailIDCounter++ )
				{
					//echo "iCounter:".$iEmailIDCounter;
					$LastUsedFlag = $ListOfEmailIDs[$iEmailIDCounter]["LastUsed"];
					if($LastUsedFlag == "1")
					{ 
						//echo "<br/>lastUsedFlag at index:<".$iEmailIDCounter.">";
						$arNextID = $ListOfEmailIDs[$iEmailIDCounter];
						//print_r($arNextID);
						$PrevID = "";
						$MaxLimit = "";
						$PrevCounter = "";
						$PrevTimeStamp = "";
						$this->GetEmailIDDetails($arNextID, $PrevID, $MaxLimit, $PrevCounter, $PrevTimeStamp);
						//echo "<br/>fetched details : id<".$PrevID .">,Limit<".$MaxLimit.">,Counter<".$PrevCounter.">,Last used Timestamp<".$PrevTimeStamp .">";
	
						$iMaxLimit = (int)$MaxLimit;
						$iPrevCounter = (int)$PrevCounter;
						$iPrevID = (int)$PrevID;
						$iEMailCount = (int)$iEmailCount;
						
						//echo "<br/>iEMailCount:<". $iEMailCount .">";
						$sCurrentTimeStamp = getCurrentTimeStamp();
						//echo "<br/>sCurrentTimeStamp:".$sCurrentTimeStamp['DateTime'];
						$iDiffInMins = $this->getTimeDiff($PrevTimeStamp, $sCurrentTimeStamp['DateTime'], HOUR) ;
						//echo "maxlimit <".$iMaxLimit .">PrevCounter:<".$iPrevCounter.">";
						if($iPrevCounter < $iMaxLimit)
						{
							$NextCounter = $iPrevCounter + 1;
							
							$sql_query = "update `loginemailids` SET LastUsed=1,LastUsedTimeStamp='".$sCurrentTimeStamp['DateTime']."',EmailSentCounter='".$NextCounter."'  where id='". $iPrevID ."'";
							$this->m_dbConnRoot->update($sql_query);
							$RetVal = $this->m_dbConnRoot->select("select * from `loginemailids` where id='".$iPrevID."'");
							$ResultAry['email'] = $RetVal[0]['EmailID'];
							$ResultAry['password'] = $RetVal[0]['Password'];
						}
						else
						{
							 if($iDiffInMins > 0)
							 {
								$NextCounter = 1;
								$sql_query = "update `loginemailids` SET LastUsed=1,LastUsedTimeStamp='".$sCurrentTimeStamp['DateTime']."',EmailSentCounter='".$NextCounter."'  where id='". $iPrevID ."'";
								$this->m_dbConnRoot->update($sql_query);
								$RetVal = $this->m_dbConnRoot->select("select * from `loginemailids` where id='".$iPrevID."'");
								$ResultAry['email'] = $RetVal[0]['EmailID'];
								$ResultAry['password'] = $RetVal[0]['Password'];
							 }
							 else
							 {
								$Query = "select id,EmailSentCounter from `loginemailids` where EmailSentCounter<>$MaxLimit and client_id = '" . $ClientID . "'"; 
								
								$NextAvailableID = $this->m_dbConnRoot->select($Query);
								//echo 'next available id:';
								//print_r($NextAvailableID);
								if(sizeof($NextAvailableID) > 0)
								{
									$iNextID = (int)$NextAvailableID[0]["id"];
									
									$NextCounter2 = (int)$NextAvailableID[0]["EmailSentCounter"];
									$NextCounter2 = $NextCounter2 + 1;
									$sql_query = "update `loginemailids` SET LastUsed=1,LastUsedTimeStamp='".$sCurrentTimeStamp['DateTime']."',EmailSentCounter='".$NextCounter2."'  where id='". $iNextID ."'";
									$this->m_dbConnRoot->update($sql_query);
									$NextCounter = $iPrevCounter + 1;
									$sql_query = "update `loginemailids` SET LastUsed=0,LastUsedTimeStamp='".$sCurrentTimeStamp['DateTime']."',EmailSentCounter='".$iPrevCounter."'  where id='". $iPrevID ."'";
									$this->m_dbConnRoot->update($sql_query);
									$RetVal = $this->m_dbConnRoot->select("select * from `loginemailids` where id='".$iNextID."'");
									$ResultAry['email'] = $RetVal[0]['EmailID'];
									$ResultAry['password'] = $RetVal[0]['Password'];
								}
								else
								{
									if($CronJobProcess)
									{
										//$ResultAry['msg'] = "All the email servers are busy at the moment. Email will be send shortly.";
										$ResultAry['status'] = 1;
									}
									else
									{
										//echo "<br/>SendEMailInQueue".$bSendEMailInQueue."<br/>";
										if($bSendEMailInQueue)
										{
											$ResultAry['status'] = 2;
											switch($EmailSourceModule)
											{
												case "0":
														//echo "M-Bill Mode";
														//$ResultAry['msg'] = "All the email servers are busy at the moment. Please try sending emails after sometime.";
														$SQL_query_existCheck = "select * from `emailqueue` where `dbName`='".$DBName."' and `PeriodID`='".$PeriodID."' and `SocietyID`='".$SocietyID."' and `UnitID`='".$UnitID."' and `Status`=0 and `ModuleTypeID`='".$EmailSourceModule."'";
														$SQL_query_existCheckRes = $this->m_dbConnRoot->select($SQL_query_existCheck);
														//echo "Already exist count:".sizeof($SQL_query_existCheckRes);
														if(sizeof($SQL_query_existCheckRes) == 0)
														{
															$queue_query = "insert into `emailqueue`(`dbName`,`PeriodID`, `SocietyID`, `UnitID`, `ModuleTypeID`) values ('".$DBName."','".$PeriodID."','".$SocietyID."','".$UnitID."','".$EmailSourceModule."')";
															//echo "<br/>".$queue_query;
															$this->m_dbConnRoot->insert($queue_query);
														}
														break;
												case "1":
												case "2":
												case "3":
														//echo "Notice Mode";
														//print_r($bccUnitsArray);
														foreach($bccUnitsArray as $aryBCCEmailUnit)
														{ 
															
															//echo "aryBCCEmailUnit:<".$aryBCCEmailUnit.">";
															$SQL_query_existCheck = "select * from `emailqueue` where `dbName`='".$DBName."' and `SocietyID`='".$SocietyID."' and `UnitID`='".$aryBCCEmailUnit."' and `SourceTableID`='".$iNoticeID."' and `Status`=0 and `ModuleTypeID`='".$EmailSourceModule."'";
															$SQL_query_existCheckRes = $this->m_dbConnRoot->select($SQL_query_existCheck);
															//echo "<br/>".$SQL_query_existCheck;
															//echo "Already exist count:".sizeof($SQL_query_existCheckRes);
															if(sizeof($SQL_query_existCheckRes) == 0)
															{
																$queue_query = "insert into `emailqueue`(`dbName`,`SourceTableID`, `SocietyID`, `UnitID`, `ModuleTypeID`) values ('".$DBName."','".$iNoticeID."','".$SocietyID."','".$aryBCCEmailUnit."','".$EmailSourceModule."')";
																//echo "<br/>".$queue_query;
																$this->m_dbConnRoot->insert($queue_query);
															}
															else
															{
																$ResultAry['msg'] = "Email already in Queue.";
																$ResultAry['status'] = 3;
															}
														}
														break;
												default:
														//echo "None";
											}
										}
										
									}
										//echo "All the email servers are busy at the moment. Please try sending emails after sometime.";
									if($ResultAry['status'] == 3)
									{
										$ResultAry['msg'] = "Email already in Queue.";
									}
									else
									{
										$ResultAry['msg'] = "All the email servers are busy at the moment. Please try sending emails after sometime.";
									}
								}
							}
							
						}
						break;
					}
				}
			}
			else
			{
				$ResultAry['status'] = 1;
				$ResultAry['msg'] = 'No EMail ID available to send email';
			}
			
			if($ResultAry['status'] == 0)
			{
				date_default_timezone_set('Asia/Kolkata');	
				$current_dateTime = date('Y-m-d H:i:s ');
				
				$sqlInsertLog = "INSERT INTO `loginemail_utilized_log`(`client_id`, `society_id`, `email_id`, `timestamp`) VALUES ('" . $ClientID . "', '" . $SocietyID . "', '" . $ResultAry['email'] . "', '" . $current_dateTime . "')";
				$this->m_dbConnRoot->insert($sqlInsertLog);
			}

			return $ResultAry;
		}
		
		public function bill_template()
		{
			$query = "SELECT bill_template FROM society";
			$result = $this->m_dbConn->select($query);
			return $result[0]['bill_template'];
		}
		
		public function ReturnInColumn($LedgerDetails)
		{
			$InColumn = '(';
			
			for($i = 0 ; $i < sizeof($LedgerDetails); $i++)
			{
				if($i == sizeof($LedgerDetails)-1)
				{
					$InColumn .= $LedgerDetails[$i]['id'];
				}
				else
				{
					$InColumn .= $LedgerDetails[$i]['id'].',';	
				}
				
			}
			$InColumn .= ')';
			
			return $InColumn;
		}	
		
		
		public function BankComboBox()
		{
			//echo "test1";
			$id=0;
			$str.="<option value=''>Please Select</option>";
			
			$query = "SELECT id,ledger_name FROM `ledger` where categoryid = '".$_SESSION['default_bank_account']."'";
			
			$data = $this->m_dbConn->select($query);
		
			//echo "<script>alert('test2')<//script>";
			if(!is_null($data))
			{
				$vowels = array('/', '*', '%', '&', ',', '(', ')', '"');
				foreach($data as $key => $value)
				{
					$i=0;
					foreach($value as $k => $v)
					{
						if($i==0)
						{
							if($id==$v)
							{
								$sel = 'selected';
							}
							else
							{
								$sel = '';
							}
							
							$str.="<OPTION VALUE=".$v.' '.$sel.">";
						}
						else
						{
							//$str.=$v."</OPTION>";
							$str.= str_replace($vowels, ' ', $v)."</OPTION>";
						}
						//echo "<script>alert('".$str."')<//script>";
						$i++;
					}
				}
			}
			return $str;
		}
				
		public function GetBankLedger($bankLedgerID)
		{
			$FetchBankDetails = "Select id, ledger_name from ledger where categoryid = '".$bankLedgerID."'";
			$ResultFetchBankDetails = $this->m_dbConn->select($FetchBankDetails);
			return $ResultFetchBankDetails	; 
		}
		
		public function GetDB_Schema_Ver()
		{
			$SocietyTable = $this->m_dbConn->select("SELECT DB_Schema_Ver FROM society WHERE society_id = '".$_SESSION['society_id']."'");
			return $SocietyTable[0]['DB_Schema_Ver'];	
		}
		
		public function SwapVoucherTableByAndToEntry()
		{
			$this->m_dbConn->update("UPDATE `voucher` SET `By` = `To`, `To`=@temp WHERE (@temp:=`By`) IS NOT NULL and VoucherTypeID in ('".VOUCHER_PAYMENT."','".VOUCHER_RECEIPT."')");

			$this->m_dbConn->update("UPDATE `voucher` SET `Debit` = `Credit`, `Credit`=@temp WHERE (@temp:=`Debit`) IS NOT NULL and VoucherTypeID in ('".VOUCHER_PAYMENT."','".VOUCHER_RECEIPT."')") ;
		}
		
		public function UpdateTableForCounter()
		{
			
			$table_vouchercounter= $this->m_dbConn->select('SHOW TABLES LIKE "%vouchercounter%"');
			
			if(empty($table_vouchercounter))
			{
			  	$this->m_dbConn->insert("CREATE TABLE IF NOT EXISTS `vouchercounter` (`id` int(11) NOT NULL AUTO_INCREMENT,`YearID` int(11) NOT NULL DEFAULT '0',`VoucherType` int(11) NOT NULL DEFAULT '0',`LedgerID` int(11) NOT NULL DEFAULT '0',`StartCounter` int(11) NOT NULL DEFAULT '1',`CurrentCounter` int(11) NOT NULL DEFAULT '1',PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");
				
			}
			$table_credit_debit_note= $this->m_dbConn->select('SHOW TABLES LIKE "%credit_debit_note%"');
			
			if(empty($table_vouchercounter))
			{
				$this->m_dbConn->insert("CREATE TABLE IF NOT EXISTS `credit_debit_note` (`ID` int(11) NOT NULL AUTO_INCREMENT,`UnitID` int(11) NOT NULL,`Date` date NOT NULL,`Note_No` int(11) NOT NULL,`Note_Sub_Total` double(50,2) NOT NULL,
  										`CGST` double(50,2) NOT NULL,`SGST` double(50,2) NOT NULL,`TotalPayable` double(50,2) NOT NULL,`Note` varchar(250) NOT NULL,`YearID` int(11) NOT NULL,`BillType` int(11) DEFAULT NULL,
										`Note_Type` int(11) NOT NULL,`LatestChangeID` int(11) NOT NULL,`CreatedTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,`CreatedBy_LoginID` int(11) NOT NULL,
 										`LastModified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',`TaxableLedgers` varchar(11) NOT NULL DEFAULT '0',PRIMARY KEY (`ID`)) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");
				
			}
			
			$Table_Col_ExternalCounter = $this->m_dbConn->select("SHOW COLUMNS FROM `voucher` LIKE 'ExternalCounter'");
			
			if(empty($Table_Col_ExternalCounter))
			{
				$this->m_dbConn->insert("ALTER TABLE `voucher` ADD `ExternalCounter` INT(11) NOT NULL DEFAULT '0' AFTER `VoucherNo`");	
			}
			$Table_Col_APP_DEFAULT_SINGLE_COUNTER = $this->m_dbConn->select("SHOW COLUMNS FROM `appdefault` LIKE 'APP_DEFAULT_SINGLE_COUNTER'");
			
			if(empty($Table_Col_APP_DEFAULT_SINGLE_COUNTER))
			{
				$this->m_dbConn->insert("ALTER TABLE `appdefault` ADD `APP_DEFAULT_SINGLE_COUNTER` INT(11) NOT NULL DEFAULT '0' AFTER `APP_DEFAULT_CESS`");
			}
		}
		
		public function Delete_VoucherandRegister_table($VoucherNo,$VoucherType)
		{
			$GetVoucherDetails = "Select * From voucher where VoucherNo = '".$VoucherNo."'"; 
			$VoucherDetails = $this->m_dbConn->select($GetVoucherDetails);
			$msg = '';
			
			for( $i=0;  $i<=sizeof($VoucherDetails); $i++)
			{
				if($VoucherDetails[$i]['id'] <> '')
				{
					if($this->ShowDebugTrace == 1)
					{
						echo '<BR>If Voucher ID is not Null then Check Current Voucher Belong to Which Register';
					}
					$GetVoucherGroup = array();
					if($VoucherDetails[$i]['By'] <> 0 &&  $VoucherDetails[$i]['By'] <> '')
					{
						$GetVoucherGroup = $this->getParentOfLedger($VoucherDetails[$i]['By']);
						
						$msg .= "Ledger : ".$VoucherDetails[$i]['By']." : Amt ".$VoucherDetails[$i]['Debit']." debit ::";
						//echo "<br>Msg".$msg;
						
					}
					else if($VoucherDetails[$i]['To'] <> 0 &&  $VoucherDetails[$i]['To'] <> '')
					{
						 $GetVoucherGroup = $this->getParentOfLedger($VoucherDetails[$i]['To']);
						 $msg .= " Ledger : ".$VoucherDetails[$i]['To']." : Amt ".$VoucherDetails[$i]['Credit']." Credit ";
						 //echo "<br>Msg".$msg;
					}
					
					if($this->ShowDebugTrace == 1)
					{
						echo '<BR>After Identify the Register Entry will deleted to respective register';
					}
				//	echo 'Group ID'.var_dump($GetVoucherGroup['group']);
					
					if($GetVoucherGroup['group'] == INCOME)
					{
						if($this->ShowDebugTrace == 1)
						{
							echo '<BR>delete VoucherEntry in income register';
						}
					//delete VoucherEntry in income register
					
						$deleteIncomeVoucher="DELETE FROM incomeregister WHERE VoucherID='".$VoucherDetails[$i]['id']."' and VoucherTypeID='".$VoucherType."'";
						$deleteIncomeVoucherResult=$this->m_dbConn->delete($deleteIncomeVoucher);
					}
						
					if($GetVoucherGroup['group'] == ASSET)
					{
						if($this->ShowDebugTrace == 1)
						{
							echo '<br>delete VoucherEntry in Assest register';
						}
					
						$deleteAssetVoucher="DELETE FROM assetregister WHERE VoucherID='".$VoucherDetails[$i]['id']."' and VoucherTypeID='".$VoucherType."'";
						$deleteAssetVoucherResult=$this->m_dbConn->delete($deleteAssetVoucher);
					}
						
					if($GetVoucherGroup['group'] == LIABILITY)
					{
						if($this->ShowDebugTrace == 1)
						{
							echo '<BR>delete VoucherEntry in Liability register';
						}
						
						$deleteliabilityVoucher="DELETE FROM liabilityregister WHERE VoucherID='".$VoucherDetails[$i]['id']."' and VoucherTypeID='".$VoucherType."'";
						$deleteliabilityVoucherResult=$this->m_dbConn->delete($deleteliabilityVoucher);	
					}
						
					if($GetVoucherGroup['group'] == EXPENSE)
					{
						if($this->ShowDebugTrace == 1)
						{	
							echo 'delete VoucherEntry in Expense register';
						}
					
						$deleteliabilityVoucher="DELETE FROM expenseregister WHERE VoucherID='".$VoucherDetails[$i]['id']."' and VoucherTypeID='".$VoucherType."'";
						$deleteliabilityVoucherResult=$this->m_dbConn->delete($deleteliabilityVoucher);	
					}	
				}
			}
			
			$voucherdeleteQuery = "DELETE FROM voucher WHERE VoucherNo='".$VoucherNo."'"; //For Every transaction voucher Number is unique
			$voucherdeleteQueryResult=$this->m_dbConn->delete($voucherdeleteQuery);
			
			$msg .= ' Voucher Number '.$VoucherNo.' Deleted';
			
			$iLatestChangeID = $this->m_changelog->setLog($msg, $_SESSION['login_id'], 'VOUCHER', 'Delected Voucher Number:'.$VoucherNo);
			
			return $voucherdeleteQueryResult;
		}
		
		public function GetPreFix($VoucherType,$LedgerID = 0)
		{

			$Prefix = "";
			if($VoucherType == VOUCHER_PAYMENT || $VoucherType == VOUCHER_RECEIPT || $VoucherType == VOUCHER_CONTRA)
			{
				if($LedgerID <> 0 && !empty($LedgerID))
				{
					$SelectBankPrefixQuery = "SELECT Bank_PreFix FROM `bank_master` WHERE BANKID = '".$LedgerID."'"; 
					$SelectBankPrefix = $this->m_dbConn->select($SelectBankPrefixQuery);
					if(!empty($SelectBankPrefix))
					{
						$Prefix = $SelectBankPrefix[0]['Bank_PreFix'];
					}
				}
			}
			else if($VoucherType == VOUCHER_SALES)
			{
				$Prefix = PREFIX_SALE_VOUCHER;
			}
			else if($VoucherType == VOUCHER_JOURNAL)
			{
				$Prefix = PREFIX_JOURNAL_VOUCHER;
			}
			else if($VoucherType == VOUCHER_INVOICE)
			{
				$Prefix = PREFIX_INVOICE_BILL;
			}
			else if($VoucherType == VOUCHER_CREDIT_NOTE)
			{
				$Prefix = PREFIX_CREDIT_NOTE;
			}
			else if($VoucherType == VOUCHER_DEBIT_NOTE)
			{
				$Prefix = PREFIX_DEBIT_NOTE;
			}
			
			return $Prefix;
			
		}
		
		public function getBankID($VoucherType, $TableID)
		{
			$BankID = 0;
			if($VoucherType == VOUCHER_RECEIPT)
			{
				$Select_Bank_Query = "SELECT BankID From chequeentrydetails WHERE ID ='".$TableID."'"; 
				$Bank_Result = $this->m_dbConn->select($Select_Bank_Query);
				$BankID = $Bank_Result[0]['BankID'];
				$result[$i]['BankID'] = $BankID;
			}
			else if($VoucherType == VOUCHER_PAYMENT || $VoucherType == VOUCHER_CONTRA)
			{
				$Select_Bank_Query = "SELECT PayerBank From paymentdetails WHERE id ='".$TableID."'"; 
				$Bank_Result = $this->m_dbConn->select($Select_Bank_Query);
				$BankID = $Bank_Result[0]['PayerBank'];
			}
			return $BankID;
		}
		
				
		
		
		public function GetCounter($VoucherType, $LedgerID = 0, $IsExitingCounterRequire = true)
		{
			$Result =array(); 
			//here we get all the exiting counter in voucher table for requested voucher type	
			if($IsExitingCounterRequire == true)
			{
				$ExitingCounter = $this->CheckVoucherCounterExit($VoucherType,$LedgerID,false,false);	
			}
			
			//First we check whether requested Counter exits or not in the table 
			$CheckTableISEmpty = "Select * from vouchercounter where YearID = '".$_SESSION['default_year']."' AND VoucherType = '".$VoucherType."' AND LedgerID = '".$LedgerID."'";
			$ResultCheckTableISEmpty = $this->m_dbConn->select($CheckTableISEmpty);
			$VoucherCounterTable = sizeof($ResultCheckTableISEmpty);
			//If counter not find in the table it return 1 as default values
			if(sizeof($ResultCheckTableISEmpty) == 0 || sizeof($ResultCheckTableISEmpty) == '')
			{
				$Result[0]['StartCounter'] = 1;
				$Result[0]['CurrentCounter'] = 1;
				
				return $Result;
			}
			//IF Counter exits then return respected counter
			else if(sizeof($ResultCheckTableISEmpty) <> 0 || sizeof($ResultCheckTableISEmpty) <> '')
			{		
				$FetchVoucherCounter = "Select StartCounter, CurrentCounter from vouchercounter where VoucherType = '".$VoucherType."' AND YearID = '".$_SESSION['default_year']."' AND LedgerID = '".$LedgerID."' ";
				$ResultFetchCounter  = $this->m_dbConn->select($FetchVoucherCounter);

				$Result[0]['StartCounter'] = $ResultFetchCounter[0]['StartCounter'];
				$Result[0]['CurrentCounter'] = $ResultFetchCounter[0]['CurrentCounter'];
				
				if($IsRequestfromCounterNumbering == false)
				{
					$Result[0]['ExitingCounter'] = $ExitingCounter;	
				}
				
				return $Result;
			}
		}
		
		public function CheckVoucherCounterExit($ExVoucherType, $ledgerID = 0, $IsRequestfromCounterNumberingOrReport, $returnOnlyVoucherNumber, $FromDate, $EndDate)
		{
			//***Declaring the assigning the default value to the variable 
			$VoucherTypeID = 0;
			$refTable = 0;
			$byCol = 0;
			$toCol = 0;
			$AmountCol = '';
			$requireColumn = '';
			$CounterExits = array();
			
			
			//**** Checking the VoucherType and according to assign the values
			if($ExVoucherType == VOUCHER_PAYMENT || $ExVoucherType == VOUCHER_CASHPAY)
			{
				$refTable = TABLE_PAYMENT_DETAILS;
				$VoucherTypeID = VOUCHER_PAYMENT;
				$byCol = $ledgerID;	
				$AmountCol = 'Debit';
			}
			else if($ExVoucherType == VOUCHER_RECEIPT || $ExVoucherType == VOUCHER_CASHRECEIVE)
			{
				$refTable = TABLE_CHEQUE_DETAILS;
				$VoucherTypeID = VOUCHER_RECEIPT;
				$toCol = $ledgerID;
				$AmountCol = 'Credit';
			}
			else if($ExVoucherType == VOUCHER_JOURNAL)
			{
				$refTable = 0;
				$VoucherTypeID = VOUCHER_JOURNAL;
				$AmountCol = 'Debit';
			}
			else if($ExVoucherType == VOUCHER_INVOICE)
			{
				$refTable = TABLE_SALESINVOICE;
				$VoucherTypeID = VOUCHER_JOURNAL;
				$AmountCol = 'Debit';
			}
			else if($ExVoucherType == VOUCHER_CREDIT_NOTE || $ExVoucherType == VOUCHER_DEBIT_NOTE)
			{
				$refTable = TABLE_CREDIT_DEBIT_NOTE;
				$VoucherTypeID = $ExVoucherType;
				
				$AmountCol = 'Debit';
				
				if($ExVoucherType == VOUCHER_CREDIT_NOTE)
				{
					$AmountCol = 'Credit';	
				}
				
			}
			
			
			if($IsRequestfromCounterNumberingOrReport == true)
			{
				//***Because for update and renumber the counter only we require voucherNumber so other details are useless
				if($returnOnlyVoucherNumber == true)
				{
					$requireColumn = '`VoucherNo`,`RefNo`';		
				}
				//***In report we require more data to show 
				else
				{
					$requireColumn = '`VoucherNo`, `ExternalCounter`, `Date`, sum('.$AmountCol.') as Amount, `RefNo`';
				}	
			}
			
			if($IsRequestfromCounterNumberingOrReport == true)
			{
				$queryVoucherData = "SELECT ".$requireColumn." FROM voucher WHERE RefTableID = '".$refTable."' AND VoucherTypeID = '".$VoucherTypeID."' AND (Date between '".getDBFormatDate($FromDate)."' AND '".getDBFormatDate($EndDate)."')";
			}
			else
			{
				 $queryVoucherData = "SELECT `ExternalCounter` FROM voucher WHERE RefTableID = '".$refTable."' AND VoucherTypeID = '".$VoucherTypeID."' AND (Date between '".getDBFormatDate($_SESSION['from_date'])."' AND '".getDBFormatDate($_SESSION['to_date'])."')";				
			}

			if($ExVoucherType <> VOUCHER_JOURNAL && $ExVoucherType <> VOUCHER_CREDIT_NOTE && $ExVoucherType <> VOUCHER_DEBIT_NOTE)
			{
				//$CashLedgerDetails = $this->GetBankLedger($_SESSION['default_cash_account']);
				$BnkLedgerDetails = $this->GetBankLedger($_SESSION['default_bank_account']);
					
				$InColumnBank = $this->ReturnInColumn($BnkLedgerDetails);
				
				if($ExVoucherType == VOUCHER_RECEIPT && $ledgerID === 0)
				{	
					$queryVoucherData .= " AND `To` IN".$InColumnBank;		
				}
				else if($ExVoucherType == VOUCHER_PAYMENT && $ledgerID === 0)
				{
					$queryVoucherData .= " AND `By` IN".$InColumnBank;
				}
				else
				{
					if($toCol == 0 && ($byCol <> 0 || $byCol <> 0))
					{
						// If user request for payemnt the ledgerid of the bank store in by columns 
						$queryVoucherData .= " AND `By` = '".$byCol."'";
					}
					else if($byCol == 0 && ($toCol <> 0 || $toCol <> 0))
					{
						// If user request for Reciepts the ledgerid of the bank store in by To
						$queryVoucherData .= " AND `To` = '".$toCol."'";
					}	
				}		
			}
			$queryVoucherData .= " group by VoucherNo";
			
			//echo 'Q :<br>'.$queryVoucherData;
			$CounterResult = $this->m_dbConn->select($queryVoucherData);
			
			if($IsRequestfromCounterNumberingOrReport == true)
			{
				return $CounterResult; 
			}
			else
			{
				for($i = 0; $i < sizeof($CounterResult); $i++)
				{
					array_push($CounterExits,$CounterResult[$i]['ExternalCounter']);
				}
				return $CounterExits; 		
			}
	
		}
	
		public function UpdateExVCounter($voucherType, $ExVoucherNo, $LedgerID = 0)
		{
			$NextCounter = $ExVoucherNo + 1;
			$updateVoucherCounter = "UPDATE `vouchercounter` SET CurrentCounter = '".$NextCounter."' where VoucherType = '".$voucherType."' AND YearID = '".$_SESSION['default_year']."' AND LedgerID = '".$LedgerID."'";
			$Result = $this->m_dbConn->update($updateVoucherCounter);	
			
		}
		 
		public function IsSameCounterApply()
		{
			$Result = $this->m_dbConn->select("SELECT APP_DEFAULT_SINGLE_COUNTER from appdefault");
			return $Result[0]['APP_DEFAULT_SINGLE_COUNTER'];
		}
		
		public function GetSocietyCode()
		{
			return $this->m_dbConn->select("SELECT * from society where society_id = '".$_SESSION['society_id']."'");
		}
		public function getParentOfCategory($categoryID)
		{
			$sqlSelect = "select categorytbl.group_id,grouptbl.groupname from account_category As categorytbl JOIN `group` As grouptbl where categorytbl.category_id = '" . $categoryID . "' and grouptbl.id=categorytbl.group_id ";
			$result = $this->m_dbConn->select($sqlSelect);
			
			$aryParent = array();
			$aryParent['group'] = $result[0]['group_id'];
			$aryParent['groupname'] = $result[0]['groupname'];
			//$aryParent['category'] = $result[0]['categoryid'];
			
			return $aryParent;
		}

	  public function getGroupID($GroupName)
	  {
		$retGroupID = -1;
		if(strcasecmp($GroupName, "LIABILITY")==0)
		{
			$retGroupID = LIABILITY;
		}
		else if(strcasecmp($GroupName, "ASSET")==0)
		{
			$retGroupID = ASSET;
		}
		else if(strcasecmp($GroupName, "INCOME")==0)
		{
			$retGroupID = INCOME;
		}
		else if(strcasecmp($GroupName, "EXPENSE")==0)
		{
			$retGroupID = EXPENSE;
		}
		return $retGroupID;		  
	  }
	  
	  public function getGroupName($GroupID)
	  {
		  $GroupName = "";
		  switch($GroupID)
		  {
			  case LIABILITY:
			  	$GroupName = "LIABILITY";
				break;
			  case ASSET:
			  	$GroupName = "ASSET";
				break;
			  case INCOME:
			  	$GroupName = "INCOME";
				break;
			  case EXPENSE:
			  	$GroupName = "EXPENSE";
				break;
		  }
		  return $GroupName;		  
	  }

		public function getLedgerID($LedgerName)
		{
			$LedgerID = 0;
			$sql="select `id` from `ledger` where `ledger_name`='".$LedgerName."' ";
			$result = $this->m_dbConn->select($sql);
			
			if(!empty($result) && !empty($result[0]['id']))
			{
				$LedgerID = $result[0]['id'];
			}
					
			return $LedgerID;	
			
		}

		public function GetCategoryDetails($category, $subcategory, $groupname,$createNew = 0)
		{	
			$this->m_bShowTrace = 1;	  
		  if($this->m_bShowTrace == 1)
		  {
			  echo "<br>GetCategoryDetails : SubCategory < ".$subcategory . " >     Category :< " .$category . " >    Group : < " . $groupname  . " >      Create new flag :" .$createNew ;
		  }
		  $category_id_array = array();
		
		//		  $query2="select id from `group` where groupname='".$groupname."'";
		//		  $data2=$this->m_dbConn->select($query2);
		//		  $group_id = $data2[0]['id'];
		$category_id = -1;
		$parentcategory_id = -1;
		
		$group_id = $this->getGroupID($groupname);
		if($group_id <= 0)
		{
			//echo "<BR>Invalid groupID" . $group_id;
			$category_id_array['group_id'] = $group_id;
			$category_id_array['parentcategory_id'] = $parentcategory_id;
			$category_id_array['category_id'] = $category_id;
			
			return $category_id_array;
		}
		$category_query = "select category_id, parentcategory_id, group_id from account_category where category_name='".$category."' and group_id = '".$group_id."'";
		
		$category_query_res = $this->m_dbConn->select($category_query );
		$primary_category_id = 1;		  
		if($category_query_res == '')
		{
		  if($this->m_bShowTrace == 1)
		  {
			  echo "<br>Category :" .$category . " not found in db<BR>";
		  }
			
		  if($createNew == 1)
		  {
			  //create category as primary category
			  $query4="insert into `account_category`(category_name, parentcategory_id, group_id) values('$category', '$primary_category_id', '$group_id')";
			  $parentcategory_id = $primary_category_id;
			  $category_id = $this->m_dbConn->insert($query4);
		
				if($category_id > 0)
				{
				  if($this->m_bShowTrace == 1)
				  {
					  echo "<BR>Created new Category : " . $category . " with CategoryID :" . $category_id . " under Primary category";					  
				  }
				}
				else
				{
					  //Error in creating subcategory
					$category_id = -1;
					$parentcategory_id = -1;
					  if($this->m_bShowTrace == 1)
					  {
						  echo "<BR>Error creating new SubCategory : " . $subcategory;
						  
					  }
				}
			  }
			  else
			  {
				$category_id = -1;
				$parentcategory_id = -1;
			  }
		  }
		  else
		  {
			  //Category found in database
			  $parentcategory_id = $category_query_res[0]['parentcategory_id'];
			  $category_id = $category_query_res[0]['category_id'];
			  if($this->m_bShowTrace == 1)
			  {
				  echo "<BR>Found existing Category : " . $category . " ID : " . $category_id . " and parentcategory_id " . $parentcategory_id;
				  
			  }
		  }
		
		
		if($category_id > 0)
		{
			//if category is found
			//Now find sub category in database
		  if($subcategory <> '')
		  {
			  $sub_category_query = "select category_id, parentcategory_id, group_id from account_category where category_name='".$subcategory."' and parentcategory_id = '".$category_id."'";
		
			  $sub_category_res = $this->m_dbConn->select($sub_category_query);
			  if($sub_category_res == '')
			  {
				  if($createNew == 1)
				  {
					  $SubCategoryInsertQuery="insert into `account_category` (category_name, parentcategory_id, group_id) values ('$subcategory', '$category_id', '$group_id')";
					  $SubCategoryID = $this->m_dbConn->insert($SubCategoryInsertQuery);
					  if($SubCategoryID>0)
					  {
						$parentcategory_id = $category_id;
						$category_id = $SubCategoryID;
						  if($this->m_bShowTrace == 1)
						  {
							  echo "<BR>Created new SubCategory : " . $subcategory . " with ID :" . $category_id;
							  
						  }
					  }
					  else
					  {
						  //Error in creating subcategory
						$category_id = -1;
						$parentcategory_id = -1;
						  if($this->m_bShowTrace == 1)
						  {
							  echo "<BR>Error creating new SubCategory : " . $subcategory;
							  
						  }
					  }
				  }
				  else
				  {
					  //User does not want to create new if does not exist
						$category_id = -1;
						$parentcategory_id = -1;
				  }
			  }
			  else
			  {
				  //Category found in database
				  $parentcategory_id = $sub_category_res[0]['parentcategory_id'];
				  $category_id = $sub_category_res[0]['category_id'];
				  if($this->m_bShowTrace == 1)
				  {
					  echo "<BR>Found existing SubCategory : " . $subcategory . "  ID : " . $category_id . " and under Category " . $category;
					  
				  }
			  }
		  }
		  else
		  {
			  //There is no subcategory mentioned. So category id is returned
			  if($this->m_bShowTrace == 1)
			  {
				  echo "<BR>No SubCategory : " . $subcategory . " mentioned returning Category " . $category;
				  
			  }
		  }
		}
		else
		{
			//Category of SubCategory not found
			$category_id = -1;
		}
		  $category_id_array['group_id'] = $group_id;
		  $category_id_array['parentcategory_id'] = $parentcategory_id;
		  $category_id_array['category_id'] = $category_id;
		  return $category_id_array;	
		}  
		
		
		public function GetCategory_ID($category, $subcategory, $groupname,$createNew = 0)
		{
			$category_id_array = $this->GetCategoryDetails($category, $subcategory, $groupname,$createNew = 0);
			if($this->m_bShowTrace == 1)
			{
				var_dump($category_id_array);
			}
			return $category_id_array['category_id'];
		}

		//This will return the 31 March of prev financial year
		public function get_begining_date_minus_one($id)
		{
			$sql = "select `BeginingDate`- INTERVAL 1 DAY  as BeginingDate from `year` where  YearID= '".$_SESSION['default_year']."'";
			$data = $this->m_dbConn->select($sql);
			return $data[0]['BeginingDate'];
		}
	
		function getDateTime()
		{
			$dateTime = new DateTime();
			$dateTimeNow = $dateTime->format('Y-m-d H:i:s');
			return $dateTimeNow;
		}
		public function ExistsUnitNo()
		{
			$result=$this->m_dbConn->select("select unit_no from unit");
			return $result;	
		}
		
		public function GetMemberID($OwnerName,$unitID)
		{
			$result=$this->m_dbConn->select("SELECT member_id from member_main where owner_name='".$OwnerName."' and unit='".$unitID."'");
			return $result[0]['member_id'];	
		}
		
		public function GetMemberIDNew($unitID)
		{
			$result=$this->m_dbConn->select("SELECT member_id from member_main where ownership_status='1' and unit='".$unitID."'");
			return $result[0]['member_id'];	
		}
		public function ExistsMemberID()
		{
			echo "In Function";
			$result=$this->m_dbConn->select("SELECT member_id FROM member_main WHERE EXISTS (SELECT * FROM nomination_form WHERE member_main.member_id = nomination_form.member_id and ownership_status=1)");
			echo $result;
			return $result;
		
			
		}
		public function GetOtherMemberID($OwnerName,$unitID)
		{
			$result=$this->m_dbConn->select("SELECT mm.member_id,mof.mem_other_family_id from mem_other_family as mof join member_main as mm on mof.member_id = mm.member_id where ownership_status=1 and other_name='".$OwnerName."' and mm.unit='".$unitID."'");
			return $result;	
		}
	
		
		
		public function getParentOfLedger($ledgerID)
		{
			$sqlSelect = "select categorytbl.group_id, categorytbl.category_name, ledgertbl.categoryid,ledgertbl.ledger_name from ledger As ledgertbl JOIN account_category As categorytbl ON ledgertbl.categoryid = categorytbl.category_id where ledgertbl.id = '" . $ledgerID . "'";
			$result = $this->m_dbConn->select($sqlSelect);
			$aryParent = array();
			$aryParent['group'] = $result[0]['group_id'];
			$aryParent['group_name'] = $this->getGroupName($result[0]['group_id']);
			$aryParent['category'] = $result[0]['categoryid'];
			$aryParent['category_name'] = $result[0]['category_name'];
			$aryParent['ledger_name'] = $result[0]['ledger_name'];
			//print_r($aryParent);			
			//return json_encode($aryParent);
			return $aryParent;
		}
		public function getParentOfLedgerGroup($ledgerID)
		{
			//echo $ledgerID;
			$sqlSelect = "select categorytbl.group_id, categorytbl.category_name, ledgertbl.categoryid,ledgertbl.ledger_name from ledger As ledgertbl JOIN account_category As categorytbl ON ledgertbl.categoryid = categorytbl.category_id where ledgertbl.id = '" . $ledgerID . "'";
			$result = $this->m_dbConn->select($sqlSelect);
			$sqlGroup="select `groupname` from `group` where `id`='".$result[0]['group_id']."' ";
			$resultGroupName = $this->m_dbConn->select($sqlGroup);
			$aryParent = array();
			$aryParent['group'] = $result[0]['group_id'];
			//$aryParent['group_name'] = $resultGroupName[0]['groupname'];
			//$aryParent['category'] = $result[0]['categoryid'];
			//$aryParent['category_name'] = $result[0]['category_name'];
			//$aryParent['ledger_name'] = $result[0]['ledger_name'];
			//print_r($aryParent);			
			//return json_encode($aryParent);
			return $aryParent;
		}
		
		public function getSingleMonthDates($noOfMonth = 0) // 0 means current months
		{
			$currentYear  = date('Y');
			$currentMonth = date('m');
			
			$startYearArr = explode('-',$_SESSION['default_year_start_date']);
			$endYearArr = explode('-',$_SESSION['default_year_end_date']);
			
			$startYear = $startYearArr[0];
			$startMonth = $startYearArr[1];
			
			$endYear = $endYearArr[0];
			$endMonth = $endYearArr[1];
			
			$from = "";
			$to   = "";
			$IsCurrentYear = true;
			
			if(($currentYear == $startYear && $currentMonth >= $startMonth) || ($currentYear == $endYear && $currentMonth <= $endMonth))
			{
				$currentMonth = $currentMonth - $noOfMonth;
				$from = date('Y-m-01');
				$to   = date('Y-m-t');

				$from = date('Y-m-d', strtotime(' -'.$noOfMonth.' months', strtotime($from)));
			}
			else
			{
				$from = $endYear.'-03-01';
				$to   = $_SESSION['default_year_end_date'];

				$from = date('Y-m-d', strtotime(' -'.$noOfMonth.' months', strtotime($from)));
				$IsCurrentYear = false;
			}
			
			return array("from_date"=>$from,"to_date"=>$to,"IsCurrentYear"=>$IsCurrentYear);
		}
		
		
		public function getDateDiff($date1, $date2, $Interval = "day")
		{
			//$sql = "SELECT DATEDIFF($Interval,'" . $date1 . "','" . $date2 . "') AS DiffDate";
			$sql = "SELECT TIMESTAMPDIFF(".$Interval.",'" . $date2 . "','" . $date1 . "') AS DiffDate";
			//echo $sql;
			$result = $this->m_dbConn->select($sql);
			return $result[0]['DiffDate'];
		}
					
		public function getDateDiffForPeriod($yyyymmdd1,$yyyymmdd2)
		{
			$date1 = new DateTime($yyyymmdd1);
			$date2 = new DateTime($yyyymmdd2);
			$diff = $date1->diff($date2);
			
			//echo "difference " . $diff->y . " years, " . $diff->m." months, ".$diff->d." days ";
			return $diff;
		}

		public function getTimeDiff($date1, $date2, $Interval = "DAY")
		{
			$sql = "SELECT TIMESTAMPDIFF(".$Interval.",'" . $date1 . "','" . $date2 . "') AS DiffDate";
			//echo $sql;
			$result = $this->m_dbConnRoot->select($sql);
			//var_dump($result);
			return $result[0]['DiffDate'];
		}
		
		public function getIsDateInRange($dateToCheck, $date1, $date2)
		{
			$ts_dateToCheck = strtotime($dateToCheck);
			$ts_date1 = strtotime($date1);
			$ts_date2 = strtotime($date2);
			
			//echo "********getIsDateInRange : " . $dateToCheck . ":" . $ts_dateToCheck . ":" . $date1 . ":" . $ts_date1 . ":"  . $date2 . ":" . $ts_date2 . "********";
			
			return (($ts_dateToCheck >= $ts_date1) && ($ts_dateToCheck <= $ts_date2));	
		}
		
		public function getMemberName($unitNo)
		{
			$MemberSql = "SELECT owner_name FROM member_main as mm JOIN unit as u ON mm.unit = u.unit_id where u.unit_no ='".$unitNo."' and mm.ownership_status = 1";
			$MemberResult = $this->m_dbConn->select($MemberSql);
			return $MemberResult[0]['owner_name'];
		}
		
		public function getCategoryID($ledgerName)
		{
			$ledgerDetails = $this->m_dbConn->select("SELECT categoryid FROM ledger where ledger_name = '".$ledgerName."'");
			return $ledgerDetails[0]['categoryid'];
		} 
		
		public function getLedgerName($LedgerID)
		{
			$sql="select ledger_name from `ledger` where id=".$LedgerID." ";
			$result = $this->m_dbConn->select($sql);	
			$ledger = $result[0]['ledger_name'];
			$arParentDetails = $this->getParentOfLedger($LedgerID);
			if(!(empty($arParentDetails)))
			{			
				$categoryID = $arParentDetails['category'];
				if($categoryID == DUE_FROM_MEMBERS)
				{
					$sqlQuery = "SELECT `owner_name` FROM `member_main` WHERE `unit` = '".$LedgerID."' and  `ownership_status` = 1";
					$memberName = $this->m_dbConn->select($sqlQuery);
					if(sizeof($memberName) > 0)
					{
						$ledger .= " - ".$memberName[0]['owner_name'];	
					}
				}			
			}		
			return $ledger;	
		}
		
		
		
		public function logGeneratorOLD($errorfile,$line_no,$errormsg)
		{
							
			if($line_no=='')
			{
				$msgFormat=$errormsg."\r\n";
				fwrite($errorfile,$msgFormat);	
			}
			else if($line_no=='start')
			{
				$msgFormat=$errormsg."\r\n";
				fwrite($errorfile,$msgFormat);
			}
			else if($line_no=='End')
			{
				$msgFormat=$errormsg."\r\n\n";
				fwrite($errorfile,$msgFormat);
			}
			else
			{
				$msgFormat="ErrorLog:[Line No:" .$line_no."]".$errormsg."\r\n";
				fwrite($errorfile,$msgFormat);
			}
			
		}
		
		
		
		public function logGenerator($errorfile,$line_no,$errormsg,$logType="")
		{
							
			if($line_no=='')
			{
				$msgFormat=$errormsg."\r\n";
				fwrite($errorfile,$msgFormat);	
			}
			else if($line_no=='start')
			{
				$msgFormat=$errormsg."\r\n";
				fwrite($errorfile,$msgFormat);
			}
			else if($line_no=='End')
			{
				$msgFormat=$errormsg."\r\n\n";
				fwrite($errorfile,$msgFormat);
			}
			else
			{
				if($logType=="E")
				{
					$msgFormat="\r\n";
					$msgFormat="<ul type='disc'><li><font style='background-color:#F88'> Error:[CSV Line No:" .$line_no."] ".$errormsg."</font></li></ul>";
					fwrite($errorfile,$msgFormat);
				}
				else if($logType=="W")
				{
					$msgFormat="<ul type='disc'><li><font style='background-color:#FF9;'>Warning:[CSV Line No:" .$line_no."] ".$errormsg."</font></li></ul>";
					fwrite($errorfile,$msgFormat);
				}
				else if($logType=="I")
				{
					$msgFormat="<ul type='disc'><li>Information:[CSV Line No:" .$line_no."] ".$errormsg."</li></ul>";
					fwrite($errorfile,$msgFormat);
				}
				else
				{
					$msgFormat="ErrorLog:[Line No:" .$line_no."]".$errormsg."\r\n";
					fwrite($errorfile,$msgFormat);
				}
			}
			
		}
		
	
		
		
	public function isNumeric($Numeric)
	{
		$bResult = true;
		 if (!preg_match('/^[0-9]*$/', $Numeric))
		
		{
			$bResult = false;
		}
		return $bResult;
	}
	
		public function check_in_range($start_date, $end_date, $date_from_user)
	{
		// Convert to timestamp
		$start_ts = strtotime($start_date);
		$end_ts = strtotime($end_date);
		$user_ts = strtotime($date_from_user);

		// Check that user date is between start & end
		return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
	}
		
		public function getCurrentYearBeginingDate($YearID)
		{
			$getPerod="select `BeginingDate` from `year` where `YearID`= ".$YearID." ";
			$period = $this->m_dbConn->select($getPerod);
			$from =	$period[0]['BeginingDate'];
			return $from;
		}
		
		public function encryptData($input)
		{
			$output = '';
			$key = (string)bin2hex('ajshdj9wieuroweurkscne98rw84fjkdnfiwfndsf4nf94hfinw4hr94heirh9fn');
			$output = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $input, MCRYPT_MODE_CBC, md5(md5($key))));
			$output = rtrim(strtr(base64_encode($output), '+/', '-_'), '='); 
			return $output;
		}
		
		public function dateFormat($date)//yyyy-mm-dd
		{
			if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
			public function validateDate($date)//dd-mm-yyyy
			{
			if (preg_match("/^(\d{2})-(\d{2})-(\d{4})$/",$date))
			{
    			return true;
			}
			else 
			{
    			return false;
			}
		}
		
		public function isVariable($variable)
		{
			$result = true;
			if(!preg_match('/^[a-zA-Z]+$/',$variable))
			{
				$result= false;	
			}	
			return $result;
		}
	
	
	
	public function isValidEmailID($email)
	{
		$bResult = true;
		if(!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i',$email))
		{
			$bResult = false;
		}
		return $bResult;
	
	}
		
		public function decryptData($input)
		{
			$output = '';
			$key = (string)bin2hex('ajshdj9wieuroweurkscne98rw84fjkdnfiwfndsf4nf94hfinw4hr94heirh9fn');
			$input = base64_decode(str_pad(strtr($input, '-_', '+/'), strlen($input) % 4, '=', STR_PAD_RIGHT)); 
			$output = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($input), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
			return $output;
		}
		
		/* Check Balancesheet sorting flag*/

		public function balancesheetSortingIsTrue()
		{
			$query = "SELECT balancesheet_sorting FROM society WHERE society_id = '".$_SESSION['society_id']."'";
			$result = $this->m_dbConn->select($query);
			return $result[0]['balancesheet_sorting'];
		}


		/*Get Supplementary Bill Opening Balance for Member Leder Reports*/
		//NOTE : JV are not considered in this balance
		public function getOpeningBalance_ForBillType($LedgerID, $date, $BillType = 1)
		{

			$openingBalance = array("Credit" => 0 ,"Debit" => 0 ,"Total" => 0, "OpeningType" => 0 ,"OpeningDate" => $date);
			
			$sqlBillCrDr = "select sum(assettbl.Debit) as Debit, sum(assettbl.Credit) as Credit, assettbl.LedgerID as LedgerID, unittbl.unit_id from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN `credit_debit_note` as CrDrNote ON vchrtbl.RefNo = CrDrNote.ID WHERE  CrDrNote.BillType = '" . $BillType . "' and unittbl.unit_id = '" . $LedgerID . "' and assettbl.Date < '" . $date . "' and vchrtbl.RefTableID = '".TABLE_CREDIT_DEBIT_NOTE."'";

			if($BillType == 0 || $BillType == 1)
			{
			 	$sqlBill = "select sum(assettbl.Debit) as Debit, sum(assettbl.Credit) as Credit, assettbl.LedgerID as LedgerID, unittbl.unit_id from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN `billdetails` as billdet on vchrtbl.RefNo=billdet.ID where vchrtbl.RefTableID='1' AND billdet.BillType = '" . $BillType . "' and unittbl.unit_id = '" . $LedgerID . "' and assettbl.Date < '" . $date . "'";
			}
			 else if($BillType == 2)
			 {
				  $sqlBill = "select sum(assettbl.Debit) as Debit, sum(assettbl.Credit) as Credit, assettbl.LedgerID as LedgerID, unittbl.unit_id from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN `sale_invoice` as saleInv on vchrtbl.RefNo=saleInv.ID where vchrtbl.RefTableID='".TABLE_SALESINVOICE."'  AND unittbl.unit_id = '" . $LedgerID . "' and assettbl.Date < '" . $date . "'";
			}

			$resultBill = $this->m_dbConn->select($sqlBill);
			
			$openingBalance['Credit'] = $resultBill[0]['Credit'];
			$openingBalance['Debit'] = $resultBill[0]['Debit'];
			
			//This will check Is there any Debit or Credit note Applied or Not and Calculated it amount
			if($sqlBillCrDr <> '')
			{
				$resultBillCrDr = $this->m_dbConn->select($sqlBillCrDr);
				if(!empty($resultBillCrDr))
				{
					//Adding amount for Opening Balance
					$openingBalance['Credit'] += $resultBillCrDr[0]['Credit'];
					$openingBalance['Debit'] += $resultBillCrDr[0]['Debit'];		
				}
			}
	
			$sSqlReceipt = "select sum(assettbl.Debit) as Debit, sum(assettbl.Credit) as Credit, assettbl.LedgerID as LedgerID, unittbl.unit_id from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN `chequeentrydetails` as chqdet on vchrtbl.RefNo=chqdet.ID where vchrtbl.RefTableID='2' AND chqdet.BillType = '" . $BillType . "' and unittbl.unit_id = '" . $LedgerID . "' and assettbl.Date < '" . $date . "'";

			$resultReceipt = $this->m_dbConn->select($sSqlReceipt);
/*
echo "<BR>sSqlReceipt :" . $sSqlReceipt . "<BR>";
print_r($resultReceipt);
echo "<BR>";
*/
			$openingBalance['Credit'] += $resultReceipt[0]['Credit'];
			$openingBalance['Debit'] += $resultReceipt[0]['Debit'];

//Get returned cheques
			
			// $sSqlReceipt = "SELECT sum(c.Amount) as Amount FROM `chequeentrydetails` as c JOIN voucher as v ON v.RefNo = c.ID JOIN `assetregister` as a  ON v.id = a.VoucherID where c.isreturn = 1 and c.paidby = '" . $LedgerID . "'  and c.BillType = '" . $BillType . "' and c.VoucherDate < '" . $date . "'";
			
			// $resultReceipt = $this->m_dbConn->select($sSqlReceipt);

			// $openingBalance['Debit'] = $openingBalance['Debit'] + $resultReceipt[0]['Amount'];
			
			//
			//$sSqlPayment = "SELECT sum(p.Amount) as Amount FROM `paymentdetails` as p JOIN voucher as v ON v.RefNo = p.id JOIN `assetregister` as a  ON v.id = a.VoucherID where v.RefTableID='". TABLE_PAYMENT_DETAILS ."' and p.paidTo = '" . $LedgerID . "'  and p.Bill_Type = '" . $BillType . "' and p.VoucherDate < '" . $date . "'";	
			
			//Check all return receipt. basically if receipt is return then it will be in both tables chequeentrydetail and paymentdetail table 
			$sSqlPaymentReverse = "select sum(assettbl.Debit) - sum(assettbl.Credit) as Amount, p.BillType, p.ChkDetailID as base_id from `assetregister` as assettbl JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN (SELECT b2.ChkDetailID, cheque.BillType FROM `chequeentrydetails` as cheque JOIN bankregister as b1 ON cheque.ID = b1.ChkDetailID JOIN bankregister as b2 ON (b1.ID = b2.Ref AND b1.ReceivedAmount = b2.PaidAmount)  WHERE b1.`Return` = 1 and b1.VoucherTypeID = '".VOUCHER_RECEIPT."' and cheque.PaidBy in('".$LedgerID."') group by b1.ChkDetailID order by b2.ChkDetailID) as p ON p.ChkDetailID = vchrtbl.refNo where vchrtbl.RefTableID='".TABLE_PAYMENT_DETAILS."' and assettbl.Date < '" . $date . "' group by p.ChkDetailID";
			$resultPaymentReverse = $this->m_dbConn->select($sSqlPaymentReverse);
		
			// its not a reverse entry.. This is made directly through payment to member
			$sSqlDirectPayment = "select sum(assettbl.Debit) - sum(assettbl.Credit) as Amount, payment.Bill_Type as BillType, payment.id as base_id from `assetregister` as assettbl JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN paymentdetails as payment ON (vchrtbl.RefNo = payment.id AND payment.id NOT IN(SELECT b2.ChkDetailID FROM `chequeentrydetails` as cheque JOIN bankregister as b1 ON cheque.ID = b1.ChkDetailID JOIN bankregister as b2 ON (b1.ID = b2.Ref AND b1.ReceivedAmount = b2.PaidAmount)  WHERE b1.`Return` = 1 and b1.VoucherTypeID = '".VOUCHER_RECEIPT."' and cheque.PaidBy in('".$LedgerID."')  group by b1.ChkDetailID order by b2.ChkDetailID)) where vchrtbl.RefTableID='".TABLE_PAYMENT_DETAILS."'  and payment.PaidTo IN ('".$LedgerID."') and payment.Bill_Type = '".$BillType."' and assettbl.Date < '" . $date . "' group by payment.id";

			$resultDirectPayment = $this->m_dbConn->select($sSqlDirectPayment);
			/* Earlier Bill Type Column was not in payment details table. Hence While we add new column we give detault value as 0 which is Maintaince
			   If any invoice and supplemetry bill get rejected before adding bill type column then opening balance was not comming right for maintaince bill type 
			   So We have addded below code to check bill type from check entry detail table and If that entry is not present then only it will be consider as maintaince.	
			*/
			//*******Code to manage reverse and direct payment*****//
			$PaymentIDs = array();
			foreach ($resultPaymentReverse as $res) {
				
				if(in_array($res['base_id'], $PaymentIDs) || $BillType != $res['BillType']){
					
					continue;
				}
				
				if(!in_array($res['base_id'], $PaymentIDs)){

					$openingBalance['Debit'] = $openingBalance['Debit'] + $res['Amount'];
					array_push($PaymentIDs, $res['base_id']);
				}
			}

			foreach ($resultDirectPayment as $res) {
				
				if(in_array($res['base_id'], $PaymentIDs) || $BillType != $res['BillType']){
					
					continue;
				}
				
				if(!in_array($res['base_id'], $PaymentIDs)){

					$openingBalance['Debit'] = $openingBalance['Debit'] + $res['Amount'];
					array_push($PaymentIDs, $res['base_id']);
				}
			}
			//*******Amit*****//
			
			$openingBalance['Total'] = ($openingBalance['Debit'] - $openingBalance['Credit']);

			if($openingBalance['Total'] < 0)
			{
				$openingBalance['OpeningType'] = TRANSACTION_CREDIT;
				$openingBalance['Total'] = abs($openingBalance['Total']);			
			}
			else
			{
				$openingBalance['OpeningType'] = TRANSACTION_DEBIT;	
			}
/*
echo "<BR>openingBalance :" . $openingBalance . "<BR>";
print_r($openingBalance);
echo "<BR>";
*/
			return $openingBalance;
		}

	public function getOpeningBalancePeriodID()
	{
			$sqlFetch="SELECT `society_creation_yearid` FROM `society` where society_id = '".$_SESSION['society_id']."'";
			$res = $this->m_dbConn->select($sqlFetch);
			
			$currentYear = $res[0]['society_creation_yearid'];
			$sql = "Select  ID from period  where YearID = '" . ($currentYear - 1) . "' and IsYearEnd = 1 ORDER BY  ID ASC";
		
			$result = $this->m_dbConn->select($sql);
			
			$PeriodID = $result[0]['ID'];
			return $PeriodID; 
	}
	
	public function getInceptionOpeningBalanceSplit($UnitID)
	{
			//Get opening balance periodID to fetch Maint and Supp opening balance when soc created
			$OpBalPeriodID = $this->getOpeningBalancePeriodID();
			
			$sqlbill = "select bill.PeriodID, bill.PrincipalArrears, bill.InterestArrears, bill.BillSubTotal, bill.BillInterest, bill.TotalBillPayable, prd.YearID from billdetails as bill JOIN period as prd ON bill.PeriodID = prd.ID where BillType = 0 and UnitID = '" . $UnitID . "' and PeriodID = '" . $OpBalPeriodID . "'";

			$resultbill = $this->m_dbConn->select($sqlbill);
			//$var[0]['year'] = $result[0]['YearID'];
			$var[0]['year'] = $currentYear - 1;
			$var[0]['period'] = $OpBalPeriodID;
			$var[0]['principle'] = $resultbill[0]['PrincipalArrears'];
			$var[0]['interest'] = $resultbill[0]['InterestArrears'];
			$var[0]['billsubtotal'] = $resultbill[0]['BillSubTotal'];
			$var[0]['billinterest'] = $resultbill[0]['BillInterest'];
			$var[0]['TotalBillPayable'] = $resultbill[0]['TotalBillPayable'];

			$sqlbill = "select bill.PeriodID, bill.PrincipalArrears, bill.InterestArrears, bill.BillSubTotal, bill.BillInterest, bill.TotalBillPayable, prd.YearID from billdetails as bill JOIN period as prd ON bill.PeriodID = prd.ID where BillType = 1 and UnitID = '" . $UnitID . "' and PeriodID = '" . $OpBalPeriodID . "'";
			
			$resultbill = $this->m_dbConn->select($sqlbill);
/*			echo  "<BR>";
			print_r($resultbill );
			echo  "<BR>";
*/			
			if($resultbill <> '')
			{
				//$var[0]['year'] = $result[0]['YearID'];
				$var[0]['supp_principle'] = $resultbill[0]['PrincipalArrears'];
				$var[0]['supp_interest'] = $resultbill[0]['InterestArrears'];
				$var[0]['supp_billsubtotal'] = $resultbill[0]['BillSubTotal'];
				$var[0]['supp_billinterest'] = $resultbill[0]['BillInterest'];
				$var[0]['supp_TotalBillPayable'] = $resultbill[0]['TotalBillPayable'];
			}
			else
			{
				//$var[0]['year'] = $result[0]['YearID'];
				$var[0]['supp_principle'] = 0;
				$var[0]['supp_interest'] = 0;
				$var[0]['supp_billsubtotal'] = 0;
				$var[0]['supp_billinterest'] = 0;
				$var[0]['supp_TotalBillPayable'] = 0;
			}
			
			//*** Fetching opening Balance for sale invoice
			
			$sqlFetch="SELECT `society_creation_yearid` FROM `society` where society_id = '".$_SESSION['society_id']."'";
			$reSqlFetch = $this->m_dbConn->select($sqlFetch);
			
			$currentYear = $reSqlFetch[0]['society_creation_yearid'];
			$Rsql = "Select  EndingDate from period  where YearID = '" . ($currentYear - 1) . "' and IsYearEnd = 1 ORDER BY  ID ASC";
		
			$resultRsql = $this->m_dbConn->select($Rsql);
			//var_dump($resultRsql);
			$InvoiceDate = $resultRsql[0]['EndingDate'];
			//echo "Date". $InviceDate;
		    $sqlbill = "Select TotalPayable from sale_invoice  where  UnitID = '" . $UnitID . "' AND `Inv_Date` < '".$InvoiceDate."'";
		
			$resultbill = $this->m_dbConn->select($sqlbill);

			if($resultbill <> '')
			{
			$var[0]['InvTotalBillPayable'] = $resultbill[0]['TotalPayable'];
			}
			else
			{
			$var[0]['InvTotalBillPayable'] = 0;
			}
			return $var;
	}

	public function getLedgerTotal($ledgerID, $group, $startDate = "", $endDate = "") {
		$result = array('OpeningDate'=> $_SESSION['default_year_start_date']);
		if(empty($startDate) || empty($endDate)) {
			$startDate = date('Y-m-d', strtotime($_SESSION['default_year_start_date'].' -1 year'));
			$endDate   = date('Y-m-d', strtotime($_SESSION['default_year_end_date'].' -1 year'));
		}

		if($group == INCOME) {
			$query = "select SUM(Credit) - sum(Debit) as total from incomeregister";
		}
		else if($group == EXPENSE) {
			$query = "select SUM(Credit) - sum(Debit) as total from expenseregister";
		}
		
		if(!empty($query)) {
			$query .= " WHERE LedgerID = {$ledgerID} AND Date BETWEEN '{$startDate}' AND '{$endDate}'";
		}
		$registerResult  = $this->m_dbConn->select($query);
		$result['Total'] = abs($registerResult[0]['total']);
		
		if($registerResult[0]['total'] < 0) {
			$result['OpeningType'] = TRANSACTION_CREDIT;
		}
		else{
			$result['OpeningType'] = TRANSACTION_DEBIT;
		}
		return $result;
	}
		public function getOpeningBalance($LedgerID,$date)
		{
		
			$openingBalance = array("LedgerName" => "","Credit" => 0 ,"Debit" => 0 ,"Total" => 0,"OpeningType" => 0 ,"OpeningDate" => $date);
			
			$arParentDetails = $this->getParentOfLedger($LedgerID);
			if(!(empty($arParentDetails)))
			{
				$LedgerGroupID = $arParentDetails['group'];
				$LedgerCategoryID = $arParentDetails['category'];
				
				if($LedgerGroupID == LIABILITY)
				{
					 $sqlLiability = "SELECT SUM(Credit) as Credit,
									SUM(Debit) as Debit,(SUM(Credit) - SUM(Debit)) as Total
									FROM `liabilityregister` where LedgerID = '".$LedgerID."' and  Date < '".getDBFormatDate($date)."' ";
					$result = $this->m_dbConn->select($sqlLiability);
					$openingBalance['Credit'] = $result[0]['Credit'];
					$openingBalance['Debit'] = $result[0]['Debit'];
					$openingBalance['Total'] = $result[0]['Total'];
					
					
					if($openingBalance['Total'] < 0)
					{
						$openingBalance['OpeningType'] = TRANSACTION_DEBIT;	
						$openingBalance['Total'] = abs($openingBalance['Total']);	
					}
					else
					{
						$openingBalance['OpeningType'] = TRANSACTION_CREDIT;		
					}
					
				}
				else if($LedgerGroupID == ASSET && ($LedgerCategoryID == BANK_ACCOUNT || $LedgerCategoryID == CASH_ACCOUNT))
				{
					 $sqlBank = "SELECT SUM(ReceivedAmount) as Credit,
								SUM(PaidAmount) as Debit,(SUM(ReceivedAmount) - SUM(PaidAmount)) as Total 
								FROM `bankregister` where LedgerID = '".$LedgerID."' and  Date < '".getDBFormatDate($date)."' ";								
					$result = $this->m_dbConn->select($sqlBank);
					$openingBalance['Credit'] = $result[0]['Credit'];
					$openingBalance['Debit'] = $result[0]['Debit'];
					$openingBalance['Total'] = $result[0]['Total'];
					
					if($openingBalance['Total'] < 0)
					{
						$openingBalance['OpeningType'] = TRANSACTION_CREDIT;
						$openingBalance['Total'] = abs($openingBalance['Total']);			
					}
					else
					{
						$openingBalance['OpeningType'] = TRANSACTION_DEBIT;	
					}

						
					
				}
				else if($LedgerGroupID == ASSET)
				{
					$sqlAsset = "SELECT SUM(Credit) as Credit,
								SUM(Debit) as Debit,(SUM(Debit) - SUM(Credit)) as Total 
								FROM `assetregister` where LedgerID  = '".$LedgerID."' and  Date < '".getDBFormatDate($date)."' ";
					$result = $this->m_dbConn->select($sqlAsset);
					$openingBalance['Credit'] = $result[0]['Credit'];
					$openingBalance['Debit'] = $result[0]['Debit'];
					$openingBalance['Total'] = $result[0]['Total'];
					
					if($openingBalance['Total'] < 0)
					{
						
						$openingBalance['OpeningType'] = TRANSACTION_CREDIT;
						$openingBalance['Total'] = abs($openingBalance['Total']);			
					}
					else
					{
						$openingBalance['OpeningType'] = TRANSACTION_DEBIT;	
						
					}
						
					
				}
				else if($LedgerGroupID == INCOME)
				{
					$sqlIncome = "SELECT SUM(Credit) as Credit,
								SUM(Debit) as Debit,(SUM(Credit) - SUM(Debit)) as Total 
								FROM `incomeregister` where LedgerID  = '".$LedgerID."' and  Date < '".getDBFormatDate($date)."' ";
					$result = $this->m_dbConn->select($sqlIncome);
					$openingBalance['Credit'] = $result[0]['Credit'];
					$openingBalance['Debit'] = $result[0]['Debit'];
					$openingBalance['Total'] = $result[0]['Total'];
					
					if($openingBalance['Total'] < 0)
					{
						$openingBalance['OpeningType'] = TRANSACTION_DEBIT;
						$openingBalance['Total'] = abs($openingBalance['Total']);			
					}
					else
					{
						$openingBalance['OpeningType'] = TRANSACTION_CREDIT;
						
					}
					
				}
				else if($LedgerGroupID == EXPENSE)
				{
					$sqlExpense = "SELECT SUM(Credit) as Credit,
								SUM(Debit) as Debit,(SUM(Debit) - SUM(Credit)) as Total 
								FROM `expenseregister` where LedgerID  = '".$LedgerID."' and  Date < '".getDBFormatDate($date)."' ";
					$result = $this->m_dbConn->select($sqlExpense);
					$openingBalance['Credit'] = $result[0]['Credit'];
					$openingBalance['Debit'] = $result[0]['Debit'];
					$openingBalance['Total'] = $result[0]['Total'];
					
					if($openingBalance['Total'] < 0)
					{
						$openingBalance['OpeningType'] = TRANSACTION_CREDIT;
						$openingBalance['Total'] = abs($openingBalance['Total']);
									
					}
					else
					{
						$openingBalance['OpeningType'] = TRANSACTION_DEBIT;	
					}
				}
				
			}
			if($result <> "")
			{
				$sql = "select l.`ledger_name`, acc.category_name   from `ledger`  as l JOIN account_category as acc ON l.categoryid = acc.category_id where l.`id` = '".$LedgerID."'";
				$res = $this->m_dbConn->select($sql);
				if($res <> "")
				{
					$openingBalance['LedgerName'] = $res[0]['ledger_name'];	
					$openingBalance['Ledger_Category'] = $res[0]['category_name'];		
				}	
			}
				//print_r($openingBalance);		
			return $openingBalance;
			
		}
		
		
		
		public function getOpeningBalanceOfCategory($CategoryID,$date , $isGroupCall = false)
		{
			$openingBalance = array("CategoryName" => "","Credit" => 0 ,"Debit" => 0 ,"Total" => 0,"OpeningType" => 0);
			
			if($isGroupCall == false)
			{
				$arParentDetails = $this->getParentOfCategory($CategoryID);
			}
			if(!(empty($arParentDetails)) || $isGroupCall == true)
			{
				$CategoryGroupID = $arParentDetails['group'];
				if($isGroupCall == true)
				{
					$CategoryGroupID = $CategoryID;		
				}
								
				if($CategoryGroupID == LIABILITY)
				{
					$sqlLiability = "SELECT SUM(Credit) as Credit,
									SUM(Debit) as Debit,(SUM(Credit) - SUM(Debit)) as Total ,CategoryID,SubCategoryID
									FROM `liabilityregister` ";
					if($isGroupCall == false)
					{				
						$sqlLiability .= "  where SubCategoryID = '".$CategoryID."'  ";	
					}
					else
					{
						$sqlLiability .= "  where CategoryID = '".$CategoryID."'  ";			
					}
					
					$sqlLiability .= "  and  Date < '".getDBFormatDate($date)."' ";	
					$result = $this->m_dbConn->select($sqlLiability);
					$openingBalance['Credit'] = $result[0]['Credit'];
					$openingBalance['Debit'] = $result[0]['Debit'];
					$openingBalance['Total'] = $result[0]['Total'];
					
					
					if($openingBalance['Total'] < 0)
					{
						$openingBalance['OpeningType'] = TRANSACTION_DEBIT;	
						$openingBalance['Total'] = abs($openingBalance['Total']);	
					}
					else
					{
						$openingBalance['OpeningType'] = TRANSACTION_CREDIT;		
					}
					
				}
				else if($CategoryGroupID == ASSET && ($CategoryID == BANK_ACCOUNT || $CategoryID == CASH_ACCOUNT))
				{
					//do nothing not required now
				}
				else if($CategoryGroupID == ASSET)
				{
					$sqlAsset = "SELECT SUM(Credit) as Credit,
								SUM(Debit) as Debit,(SUM(Debit) - SUM(Credit)) as Total,CategoryID,SubCategoryID 
								FROM `assetregister`  ";
								
					if($isGroupCall == false)
					{				
						$sqlAsset .= "  where SubCategoryID = '".$CategoryID."'  ";	
					}
					else
					{
						$sqlAsset .= "  where CategoryID = '".$CategoryID."'  ";			
					}
					
					$sqlAsset .= "  and  Date < '".getDBFormatDate($date)."' ";	
								
					$result = $this->m_dbConn->select($sqlAsset);
					$openingBalance['Credit'] = $result[0]['Credit'];
					$openingBalance['Debit'] = $result[0]['Debit'];
					$openingBalance['Total'] = $result[0]['Total'];
					
					if($openingBalance['Total'] < 0)
					{
						$openingBalance['OpeningType'] = TRANSACTION_CREDIT;
						$openingBalance['Total'] = abs($openingBalance['Total']);			
					}
					else
					{
						$openingBalance['OpeningType'] = TRANSACTION_DEBIT;	
					}
				}
				else if($CategoryGroupID == INCOME)
				{
					$sqlIncome = "SELECT SUM(Credit) as Credit,
								SUM(Debit) as Debit,(SUM(Credit) - SUM(Debit)) as Total 
								FROM `incomeregister`  ";
								
					$sqlIncome .= "  where  Date < '".getDBFormatDate($date)."' ";	
					
					
					$result = $this->m_dbConn->select($sqlIncome);
					$openingBalance['Credit'] = $result[0]['Credit'];
					$openingBalance['Debit'] = $result[0]['Debit'];
					$openingBalance['Total'] = $result[0]['Total'];
					
				}
				else if($CategoryGroupID == EXPENSE)
				{
					$sqlExpense = "SELECT SUM(Credit) as Credit,
								SUM(Debit) as Debit,(SUM(Debit) - SUM(Credit)) as Total
								FROM `expenseregister`  ";
								
					$sqlExpense .= "  where  Date < '".getDBFormatDate($date)."' ";	
					
					$result = $this->m_dbConn->select($sqlExpense);
					$openingBalance['Credit'] = $result[0]['Credit'];
					$openingBalance['Debit'] = $result[0]['Debit'];
					$openingBalance['Total'] = $result[0]['Total'];
					
				}
			}
									
			return $openingBalance;
			
		}
		
		
		public function getPreviousYearEndingDate($CurrentYearID)
		{
			//$prevYearID = $CurrentYearID ;
			$prevYearID = $CurrentYearID - 1;
		  $getPerod = "select yeartbl.EndingDate from `period` as periodtbl JOIN `society` as societytbl on periodtbl.Billing_cycle = societytbl.bill_cycle  JOIN `year` as yeartbl on yeartbl.YearID = periodtbl.YearID where societytbl.society_id = ".$_SESSION['society_id']." and  yeartbl.YearID = ".$prevYearID." ";
			
			$period = $this->m_dbConn->select($getPerod);
			$EndingDate =	$period[0]['EndingDate'];
			//echo "End Date :".$EndingDate;
			return $EndingDate;
		}
		
		public function getBeginningAndEndingDate($YearID)
		{
			//$prevYearID = $CurrentYearID - 1;
			$getPerod = "select yeartbl.BeginingDate,yeartbl.EndingDate from `society` as societytbl  JOIN `year` as yeartbl where societytbl.society_id = ".$_SESSION['society_id']." and  yeartbl.YearID = ".$YearID." ";
			$period = $this->m_dbConn->select($getPerod);
			$EndingDate =	$period[0];
			return $EndingDate;
		}
		
		public function GetDateByOffset($myDate, $Offset)
		{
			$datetime1 = new DateTime($myDate);
			$newDate = $datetime1->modify($Offset . ' day');
			return $newDate->format('Y-m-d');	
		}
		
		
		public function getDueAmount($unitID)
		{
			$sql = "SELECT SUM(`Debit`) as Debit , SUM(`Credit`) as Credit, (SUM(Debit) - SUM(Credit)) as Total FROM `assetregister` WHERE `LedgerID` = '".$unitID."'  ";
			if(isset($_SESSION['default_year_start_date']) && $_SESSION['default_year_start_date'] <> 0  && isset($_SESSION['default_year_end_date']) && $_SESSION['default_year_end_date'] <> 0)
			{
				//$sql .= "  and Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";	
				$sql .= "  and Date <= '".getDBFormatDate($_SESSION['default_year_end_date'])."'";									
			}
			$sql .= " GROUP BY LedgerID ";	
			
			
			$details = $this->m_dbConn->select($sql);
			//$details[0]['Total'] = 0;
			/*$OpeningBalance = $this->getOpeningBalance($unitID , getDBFormatDate($_SESSION['default_year_start_date']));
				
			if($OpeningBalance <> "")
			{
				if($OpeningBalance['OpeningType'] == TRANSACTION_CREDIT)
				{
					$details[0]['Credit'] = $details[0]['Credit'] + $OpeningBalance['Credit']; 		
				}
				else if($OpeningBalance['OpeningType'] == TRANSACTION_DEBIT)
				{
					$details[0]['Debit'] = $details[0]['Debit'] + $OpeningBalance['Debit']; 		
				}
				$details[0]['Total'] =  $details[0]['Debit'] - $details[0]['Credit'];
			}*/
			return $details[0]['Total'];	
		}
		function generateRandomString($length = 10) 
		{
		    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		    $charactersLength = strlen($characters);
		    $randomString = '';
		    for ($i = 0; $i < $length; $i++) {
		        $randomString .= $characters[rand(0, $charactersLength - 1)];
		    }
		    return $randomString;
		}
		public function getDueAmountTillDate($unitID)
		{
			$sql = "SELECT SUM(`Debit`) as Debit , SUM(`Credit`) as Credit, (SUM(Debit) - SUM(Credit)) as Total FROM `assetregister` WHERE `LedgerID` = '".$unitID."'  ";
			
			$sql .= " GROUP BY LedgerID ";	
			
			//echo "<br>due qry:".$sql;

			//$sql = "SELECT * FROM  `commitee` ";		
			//print_r($this->m_dbConn);
			$details = $this->m_dbConn->select($sql);
			//print_r($details);
			//$details[0]['Total'] = 0;
			/*$OpeningBalance = $this->getOpeningBalance($unitID , getDBFormatDate($_SESSION['default_year_start_date']));
				
			if($OpeningBalance <> "")
			{
				if($OpeningBalance['OpeningType'] == TRANSACTION_CREDIT)
				{
					$details[0]['Credit'] = $details[0]['Credit'] + $OpeningBalance['Credit']; 		
				}
				else if($OpeningBalance['OpeningType'] == TRANSACTION_DEBIT)
				{
					$details[0]['Debit'] = $details[0]['Debit'] + $OpeningBalance['Debit']; 		
				}
				$details[0]['Total'] =  $details[0]['Debit'] - $details[0]['Credit'];
			}*/
			//echo "due:".$details[0]['Total'];
			return $details[0]['Total'];	
		}
		public function getDueAmountByBillType($UnitID,$BillType)
		{
			$sql = "SELECT TotalBillPayable FROM `billdetails` WHERE `UnitID` = '".$UnitID."' and `BillType`='".$BillType."' order by ID desc limit 0,1 ";
			
			//echo "<br>due qry:".$sql;

			//$sql = "SELECT * FROM  `commitee` ";		
			//print_r($this->m_dbConn);
			$details = $this->m_dbConn->select($sql);
			//print_r($details);
			//$details[0]['Total'] = 0;
			/*$OpeningBalance = $this->getOpeningBalance($unitID , getDBFormatDate($_SESSION['default_year_start_date']));
				
			if($OpeningBalance <> "")
			{
				if($OpeningBalance['OpeningType'] == TRANSACTION_CREDIT)
				{
					$details[0]['Credit'] = $details[0]['Credit'] + $OpeningBalance['Credit']; 		
				}
				else if($OpeningBalance['OpeningType'] == TRANSACTION_DEBIT)
				{
					$details[0]['Debit'] = $details[0]['Debit'] + $OpeningBalance['Debit']; 		
				}
				$details[0]['Total'] =  $details[0]['Debit'] - $details[0]['Credit'];
			}*/
			//echo "due:".$details[0]['Total'];
			return $details[0]['TotalBillPayable'];	
		}
		public function getDueAmountTillDateNew($unitID, $BillType = 0)
		{
			//$sql = "SELECT SUM(`Debit`) as Debit , SUM(`Credit`) as Credit, (SUM(Debit) - SUM(Credit)) as Total FROM `assetregister` WHERE `LedgerID` = '".$unitID."'  ";
			
			$sql .= " GROUP BY LedgerID ";	
			
			$sql = "SELECT * FROM `assetregister` WHERE `LedgerID` = '".$unitID."'  ";
			$sqlOpeningBal = "SELECT opening_balance,Supplementary_bill FROM `ledger` WHERE `ID` = '".$unitID."'";
			$resLedger = $this->m_dbConn->select($sqlOpeningBal);
			$sOpeningBalAmount = $resLedger[0]["opening_balance"];
			$sOpeningBillType = $resLedger[0]["Supplementary_bill"];
			//$sql .= " GROUP BY LedgerID ";	
			$bTrace = false;
			if($bTrace)
			{
				echo "<br>due qry:".$sql;
			}
			//$sql = "SELECT * FROM  `commitee` ";		
			//print_r($this->m_dbConn);
			$details = $this->m_dbConn->select($sql);
			if($bTrace)
			{
				echo "size:".sizeof($details);
			}
			$sTotalCredit = 0;
			$sTotalDebit = 0;
			$sTotalChqCredit = 0;
			$sTotalChqDebit = 0;
			$sTotalChqReturned = 0;
			for($iCnt = 0; $iCnt < sizeof($details); $iCnt++)
			{
				$sVoucherID = $details[$iCnt]["VoucherID"];
				if($bTrace)
				{
					echo "<br>voucherID:".$sVoucherID;
				}
				$sqlVoucher = "SELECT * FROM `voucher` WHERE `ID` = '".$sVoucherID."'  ";
				$VoucherDetails = $this->m_dbConn->select($sqlVoucher);
				$sRefNo = $VoucherDetails[0]["RefNo"];
				$sRefTableID = $VoucherDetails[0]["RefTableID"];
				if($bTrace)
				{
					echo "<br>ref:".$sRefTableID;
				}
				if($sRefTableID == 1)
				{
					//"select SUM(`Debit`) as Debit , SUM(`Credit`) as Credit, (SUM(Debit) - SUM(Credit)) as Total FROM `vocher` WHERE `LedgerID` = '".$unitID."' where voucher.RefTableID=1 JOIN billregister on billregister.id=voucher.RefNo";
					$sqlBill = "select `Debit` as Debit, VoucherNo FROM `voucher` JOIN billdetails on billdetails.id=voucher.RefNo WHERE `By` = '".$unitID."' and voucher.RefTableID=1 and billdetails.billtype='".$BillType."' and voucher.id='".$sVoucherID."'";
					if($bTrace)
					{
						echo "<br>sqlbill:".$sqlBill;
					}
					$VoucherBillDetails = $this->m_dbConn->select($sqlBill);
					
					for($iVoucherCnt = 0; $iVoucherCnt < sizeof($VoucherBillDetails); $iVoucherCnt++)
					{
						$VoucherNo = $VoucherBillDetails[$iVoucherCnt]["VoucherNo"];
						$Debit = $VoucherBillDetails[$iVoucherCnt]["Debit"];
						if($bTrace)
						{
							echo "<br/>voucherNo:".$VoucherNo;
						}
						$sVoucherNoQry = "select SUM(`Credit`) as Credit from voucher where VoucherNo='".$VoucherNo."'";
						$VoucherNoDetails = $this->m_dbConn->select($sVoucherNoQry);
						if($bTrace)
						{
							echo "<br>credit:".$VoucherNoDetails[0]["Credit"];
						}
						$sCredit = $VoucherNoDetails[0]["Credit"];
						$sTotalCredit += $sCredit;
						$sTotalDebit += $Debit;
					}
					
				}
				else if($sRefTableID == "2")
				{
					//echo "refno:2";
					$sqlBill = "select `Debit` as Debit, VoucherNo FROM `voucher` JOIN chequeentrydetails as chq on chq.id=voucher.RefNo WHERE `By` = '".$unitID."' and voucher.RefTableID=2 and chq.billtype='".$BillType."' and voucher.id='".$sVoucherID."'";
					if($bTrace)
					{
						echo "<br>sqlchq:".$sqlBill;
					}
					$VoucherBillDetails2 = $this->m_dbConn->select($sqlBill);
					
					for($iVoucherCnt = 0; $iVoucherCnt < sizeof($VoucherBillDetails2); $iVoucherCnt++)
					{
						$VoucherNo = $VoucherBillDetails2[$iVoucherCnt]["VoucherNo"];
						$Debit = $VoucherBillDetails2[$iVoucherCnt]["Debit"];
						if($bTrace)
						{
							echo "<br/>voucherNo:".$VoucherNo;
						}
						$sVoucherNoQry = "select SUM(`Credit`) as Credit from voucher where VoucherNo='".$VoucherNo."'";
						$VoucherNoDetails2 = $this->m_dbConn->select($sVoucherNoQry);
						if($bTrace)
						{
							echo "<br>credit:".$VoucherNoDetails2[0]["Credit"];
						}
						$sCredit = $VoucherNoDetails2[0]["Credit"];
						$sTotalChqCredit += $sCredit;
						$sTotalChqDebit += $Debit;
					}
				}
				else if($sRefTableID == "3")
				{
					if($bTrace)
					{
						echo "refno:3";
					}
					$sqlVoucher = "SELECT * FROM `voucher` join paymentdetails on voucher.refno = paymentdetails.id   where reftableid in (3) and `to`='".$unitID."' and voucher.id='".$sVoucherID."'"; 
					$VoucherDetails3 = $this->m_dbConn->select($sqlVoucher);
					//print_r($VoucherDetails3);
					for($iVoucherCntr = 0; $iVoucherCntr < sizeof($VoucherDetails3); $iVoucherCntr++)
					{
						$VoucherNo = $VoucherDetails3[$iVoucherCntr]["VoucherNo"];
						$Credit = $VoucherDetails3[$iVoucherCntr]["Credit"];
						$sChequeNumber = $VoucherDetails3[$iVoucherCntr]["ChequeNumber"];
						if($bTrace)
						{
							echo "<br/>voucherNo:".$VoucherNo;
						}
						$sVoucherNoQry = "select `Amount` from chequeentrydetails where ChequeNumber='".$sChequeNumber."' and BillType='".$BillType."'";
						$resVoucherDetails3 = $this->m_dbConn->select($sVoucherNoQry);
						//print_r($resVoucherDetails3);
						if($bTrace)
						{
							echo "<br>credit:".$resVoucherDetails3[0]["Amount"];
						}
						$sTotalChqReturned += $resVoucherDetails3[0]["Amount"];
						
					}
					//$sTotalChqReturned = $sAmount;
					//echo "t:".$sTotalChqReturned;
					//
				}
				else if($sRefTableID == "4")
				{
					if($bTrace)
					{
						echo "refno:4";
					}
				}
				else if($sRefTableID == "5")
				{
					if($bTrace)
					{
						echo "refno:5";
					}
				}
				else if($sRefTableID == "6")
				{
					if($bTrace)
					{
						echo "refno:6";
					}
				}

			}
			if($bTrace)
			{
				echo "returned:".$sTotalChqReturned;
			}
			$sBal = $sTotalDebit - $sTotalCredit;
			//$sBal = $sTotalChqDebit - $sTotalChqCredit;
			if($sOpeningBillType == $BillType)
			{
				$sTotalDebit += $sOpeningBalAmount;	
			}
			$sBalDue = $sTotalDebit - $sTotalChqDebit + $sTotalChqReturned;
			if($bTrace)
			{
				echo "<br>debit:".$sTotalDebit. " credit :".$sTotalCredit." bal: " . $sBal;
				echo "<br>debit:".$sTotalChqDebit. " credit :".$sTotalChqCredit." bal: " . $sBalDue;
			}
					//print_r($details);
			//$details[0]['Total'] = 0;
			/*$OpeningBalance = $this->getOpeningBalance($unitID , getDBFormatDate($_SESSION['default_year_start_date']));
				
			if($OpeningBalance <> "")
			{
				if($OpeningBalance['OpeningType'] == TRANSACTION_CREDIT)
				{
					$details[0]['Credit'] = $details[0]['Credit'] + $OpeningBalance['Credit']; 		
				}
				else if($OpeningBalance['OpeningType'] == TRANSACTION_DEBIT)
				{
					$details[0]['Debit'] = $details[0]['Debit'] + $OpeningBalance['Debit']; 		
				}
				$details[0]['Total'] =  $details[0]['Debit'] - $details[0]['Credit'];
			}*/
			//echo "due:".$details[0]['Total'];
			return $sBalDue;	
		}
		public function displayFormatBillFor($BillFor)
		{
			$tmpString = '';
			$monthsArrayI = array('April','May','June','July','August','September','October','November','December');
			$monthsArrayII = array('January','February','March');
			
			$tmpArray = explode(' ', $BillFor);
			$StartMonth =  $tmpArray[0];
			//var_dump($temArray);
			//explode period description
			$tmpArray2 = explode('-', $tmpArray[0]);
			
			$StartMonth =  $tmpArray2[0];
			$EndMonth =  $tmpArray2[1];
			
			$tmpArray3 = explode('-', $tmpArray[1]);
			
			//explode year description
			$tmpArray3[1] =  substr_replace($tmpArray[1], '', 2, -2);
			$tmpArray3[0] =  substr_replace($tmpArray[1], '', 4, strlen($tmpArray[1]));
			

			if(in_array($StartMonth,$monthsArrayI) && in_array($EndMonth,$monthsArrayII))
			{
			//This condition only apply for half yearly bill
				$tmpArray3[0] = $tmpArray3[1] - 1 ;
			}
			for($i = 0 ;$i < sizeof($tmpArray2); $i++)
			{
				if($tmpString <> "")
				{
					$tmpString .= " - ";		
				}
				if (in_array($tmpArray2[$i], $monthsArrayI, true)) 
				{
					//echo "found I\n";
					if($tmpString == "")
					{
						$tmpString .= $tmpArray2[$i] ." ". $tmpArray3[0];	
					}
					else
					{
						$tmpString .= " ".$tmpArray2[$i] ." ". $tmpArray3[0];
					}
				}
				else if (in_array($tmpArray2[$i], $monthsArrayII, true))  
				{
					//echo "found II\n";
					if($tmpString == "")
					{
						$tmpString .= $tmpArray2[$i] ." ". $tmpArray3[1];	
					}
					else
					{
						$tmpString .= " ".$tmpArray2[$i] ." ". $tmpArray3[1];
					}
				}
			}
			
			return $tmpString;
		}
		
		public function getPeriodBeginAndEndDate($PeriodID)
		{
			$aryDate = array();
			
			$sql = "select BeginingDate, EndingDate	from period where ID = '" . $PeriodID . "'";
			$res = $this->m_dbConn->select($sql);
			if($res <> '')
			{
				$aryDate['BeginDate'] = $res[0]['BeginingDate'];
				$aryDate['EndDate'] = $res[0]['EndingDate'];
			}
			
			return $aryDate;
		}
		
		public function convert_number_to_words($number) 
		{
			$number = str_replace(',', '', $number);
		   	$hyphen      = '-';
			$conjunction = ' ';
			$separator   = '  ';
			$negative    = 'negative ';
			$decimal     = ' and ';
			$dictionary  = array(
				0                   => 'Zero',
				1                   => 'One',
				2                   => 'Two',
				3                   => 'Three',
				4                   => 'Four',
				5                   => 'Five',
				6                   => 'Six',
				7                   => 'Seven',
				8                   => 'Eight',
				9                   => 'Nine',
				10                  => 'Ten',
				11                  => 'Eleven',
				12                  => 'Twelve',
				13                  => 'Thirteen',
				14                  => 'Fourteen',
				15                  => 'Fifteen',
				16                  => 'Sixteen',
				17                  => 'Seventeen',
				18                  => 'Eighteen',
				19                  => 'Nineteen',
				20                  => 'Twenty',
				30                  => 'Thirty',
				40                  => 'Fourty',
				50                  => 'Fifty',
				60                  => 'Sixty',
				70                  => 'Seventy',
				80                  => 'Eighty',
				90                  => 'Ninety',
				100                 => 'Hundred',
				1000                => 'Thousand',
				100000             => 'Lakh',
	        	10000000          => 'Crore'
			);
		   
			if (!is_numeric($number)) {
				return false;
			}
		   
			if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
				// overflow
				trigger_error(
					'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
					E_USER_WARNING
				);
				return false;
			}
		
			if ($number < 0) {
				//return $negative . convert_number_to_words(abs($number));
				return  $this->convert_number_to_words(abs($number)) . ' Credit';
			}
		   
			$string = $fraction = null;
		   
			if (strpos($number, '.') !== false) {
				list($number, $fraction) = explode('.', $number);
			}
		   
			switch (true) {
				case $number < 21:
					$string = $dictionary[$number];
					break;
				case $number < 100:
					$tens   = ((int) ($number / 10)) * 10;
					$units  = $number % 10;
					$string = $dictionary[$tens];
					if ($units) {
						$string .= $hyphen . $dictionary[$units];
					}
					break;
				case $number < 1000:
					$hundreds  = $number / 100;
					$remainder = $number % 100;
					$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
					if ($remainder) {
						$string .= $conjunction . $this->convert_number_to_words($remainder);
					}
					break;
				  case $number < 100000:
					$thousands   = ((int) ($number / 1000));
					$remainder = $number % 1000;
		
					$thousands = $this->convert_number_to_words($thousands);
		
					$string .= $thousands . ' ' . $dictionary[1000];
					if ($remainder) {
						$string .= $separator .$this->convert_number_to_words($remainder);
					}
					break;
				case $number < 10000000:
					$lakhs   = ((int) ($number / 100000));
					$remainder = $number % 100000;
		
					$lakhs = $this->convert_number_to_words($lakhs);
		
					$string = $lakhs . ' ' . $dictionary[100000];
					if ($remainder) {
						$string .= $separator .$this->convert_number_to_words($remainder);
					}
					break;
				case $number < 1000000000:
					$crores   = ((int) ($number / 10000000));
					$remainder = $number % 10000000;
		
					$crores =$this->convert_number_to_words($crores);
		
					$string = $crores . ' ' . $dictionary[10000000];
					if ($remainder) {
						$string .= $separator . $this->convert_number_to_words($remainder);
					}
					break;	
				default:
					$baseUnit = pow(1000, floor(log($number, 1000)));
					$numBaseUnits = (int) ($number / $baseUnit);
					$remainder = $number % $baseUnit;
					$string = $this->convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
					if ($remainder) {
						$string .= $remainder < 100 ? $conjunction : $separator;
						$string .= $this->convert_number_to_words($remainder);
					}
					break;
			}
		   
			if (null !== $fraction && is_numeric($fraction)) 
			{
				
				$words = array();
				if((int)$fraction == 0)
				{
					//$string .= " Zero";
				}
				else
				{
					$string .= $decimal;
					/*foreach (str_split((string) $fraction) as $number) 
					{
						$words[] = $dictionary[$number];
					}*/
					
					$words[]=  $this->convert_number_to_words((int)$fraction);
					$string .= implode(' ', $words);
					$string .= " Paise ";
				}
			}
		   
			return $string;
		}
		
		
		public function GetUnitID($UnitNo)
		{
			$result = $this->m_dbConn->select("SELECT unit_id from unit as u JOIN ledger as l On u.unit_id = l.id WHERE u.unit_no = '".$UnitNo."'");
			return $result[0]['unit_id'];
		}
		
		public function getLedgerIDForBillType()
		{
			$keys = array(0);
			$sql="select id from ledger where categoryid =".DUE_FROM_MEMBERS."  ORDER BY ledger_name ASC";
			$res = $this->m_dbConn->select($sql);
			$result = array_column($res, 'id');
			return $result;			
		}

		public  function getMemberIDs($date)
		{
			$keys = array(0);
			
			//$sql = "SELECT member_id, owner_name, ownership_date FROM ( SELECT member_id, unit, owner_name, ownership_date FROM member_main where ownership_date <= '".$date."' ORDER BY ownership_date DESC ) AS t1 GROUP BY unit";
			//$res = $this->m_dbConn->select($sql);	
			
			$res = $this->getUnitData(0,$date);
			if(sizeof($res) > 0)
			{
				/*for($i = 0;$i < sizeof($res);$i++)
				{
					array_push($keys,$res[$i]['member_id']);	
				}	*/
				foreach($res as  $k => $v)
				{
					array_push($keys,$res[$k]['member_id']);		
				}
			}
			$strKeys = implode(',', $keys);
			return $strKeys;
		}
		
		public  function getUnitData($uid = 0,$date)
		{
			$data = array();
			
			$sqlII = "SELECT member_id, unit, owner_name, ownership_date FROM member_main where ownership_date <= '".$date."' ";
			if($uid > 0)
			{
				$sqlII .= " and   unit = '".$uid."'  ";	
			}
			$sqlII .= "  ORDER BY ownership_date DESC";
			
			$sql = "SELECT member_id, owner_name, ownership_date,unit FROM (".$sqlII.") AS t1 ";
			if($uid > 0)
			{
				$sql .= "where  unit = '".$uid."'  ";	
			}
			$sql .= "  GROUP BY unit";
			$res = $this->m_dbConn->select($sql);	
			
			
			if(sizeof($res) > 0)
			{
				for($i = 0;$i < sizeof($res);$i++)
				{
					$data[$res[$i]['unit']]['member_id'] =$res[$i]['member_id'] ;
					$data[$res[$i]['unit']]['owner_name'] =$res[$i]['owner_name'] ;
					$data[$res[$i]['unit']]['ownership_date'] =$res[$i]['ownership_date'] ;
				}
			}
			return $data;
		}
		
		public function getSocietyAddress()
		{
			$sHeader = "";
			$sql = "SELECT society_add from  `society`";
			$res = $this->m_dbConn->select($sql);
			return  $res; 
		}
		
		public function GetmemberDetailunit($UnitID)
		{
			//echo "SELECT mm.member_id, mm.email, u.unit_no, mm.mob FROM `unit` AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit WHERE u.society_id = '".$_SESSION['society_id']."' AND u.unit_id = '".$UnitID."'";
			return $MemberID = $this->m_dbConn->select("SELECT mm.member_id,mm.primary_owner_name, mm.email, u.unit_no,u.unit_id, mm.mob FROM `unit` AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit WHERE u.society_id = '".$_SESSION['society_id']."' AND mm.`status` = 'Y'  and mm.`ownership_status` = '1' and u.unit_id = '".$UnitID."'");
		}
		public function GetmemberDetail($UnitID)
		{
			//echo "SELECT mm.member_id, mm.email, u.unit_no, mm.mob FROM `unit` AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit WHERE u.society_id = '".$_SESSION['society_id']."' AND u.unit_id = '".$UnitID."'";
			return $MemberID = $this->m_dbConn->select("SELECT mm.member_id,mm.primary_owner_name, mm.email, u.unit_no, mm.mob FROM `unit` AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit WHERE u.society_id = '".$_SESSION['society_id']."' AND u.unit_id = '".$UnitID."'");
		}
		public function getSocietyDetails()
		{
			$sHeader = "";
			$sql = "SELECT * from  `society` where `society_id` = '".$_SESSION['society_id']."' ";
			$res = $this->m_dbConn->select($sql);
			$sHeader .= '<center><div style="text-align:center;"  class="PrintClass"><br><br> <div  style="font-weight:bold; font-size:18px;" class="PrintClass">'.$res[0]['society_name'] .'</div>';
		   
		    if($res[0]['registration_no'] <> "")
		    {
				$sHeader .= '<div style="font-size:14px;"  class="PrintClass">'.$res[0]['registration_no'] .'</div>';
		    }
			$sHeader .= '<div style="font-size:14px;"  class="PrintClass">'.$res[0]['society_add'].'</div></div><br></center>';
			return  addcslashes($sHeader,"\\\'\"\n\r"); 
		}
		
		public function IsGST()
		{		
			$periodQuery = 'SELECT `apply_service_tax` FROM `society` WHERE `society_id` = '.$_SESSION['society_id'];
			$res = $this->m_dbConn->select($periodQuery);
			if($res <> '')
			{
				$IsShowGST = $res[0]['apply_service_tax'];
			}
			
			return $IsShowGST ;
		}
	
		
		public function getSMSTemplateString($MobileNumber, $SMSBody,$client_id=0,$societyid=0)
		{
			$sql_query = "select `sms_userid`,`sms_key`,`sms_senderid` from `client` where `id` = '" . $client_id. "'";
			$sms_data = $this->m_dbConnRoot->select($sql_query);
			if(!empty($societyid)){
				$Society_sms_details_query = "SELECT sms_senderid FROM society WHERE society_id = '".$societyid."'";
			}else
			{
				$Society_sms_details_query = "SELECT sms_senderid FROM society WHERE society_id = '".$_SESSION['society_id']."'";
			}
			$Society_sms_details = $this->m_dbConnRoot->select($Society_sms_details_query);
			
			if(!empty($Society_sms_details[0]['sms_senderid']))
			{
				$sms_data[0]['sms_senderid'] = $Society_sms_details[0]['sms_senderid']; 
			}
			
			
				$smsTemplate ='<?xml version="1.0"?>
								<parent>
								<child>
								<user>'.$sms_data[0]['sms_userid'].'</user>
								<key>'.$sms_data[0]['sms_key'].'</key>
								<mobile>+91'.$MobileNumber.'</mobile>
								<message>'.$SMSBody.'</message>
								<senderid>'.$sms_data[0]['sms_senderid'].'</senderid>
								<accusage>1</accusage>
								
								</child>						
								</parent>';
				
			return $smsTemplate;					
		}
		public function SendDemoSMS($MobileNo, $MsgBody)
		{
			$clientDetails = $this->m_dbConnRoot->select("SELECT `client_id` FROM  `society` WHERE  `dbname` ='".$_SESSION['dbname']."' ");
			if(sizeof($clientDetails) > 0)
				{
					$clientID = $clientDetails[0]['client_id'];
				}		
				//**---Calling SMS function for utility---***
				$response =  $this->SendSMS($MobileNo, $MsgBody, $clientID);
				return $response;
						
		}
		
		public function SendSMS($mobileNumber, $SMSBody,$client_id = 0,$societyid=0,$template_type=0)
		{
			// Need to make move below query out side of the function
			
			$SMSBody = substr($SMSBody,0,158); // Restrict the sms body to 158 character
			$SMSBody = str_replace('&',' and ',$SMSBody);
			
			$sql_query = "select `sms_userid`,`sms_key`,`sms_domain`,sms_senderid, sms_vendor from `client` where `id` = '" . $client_id. "'";
			$sms_data = $this->m_dbConnRoot->select($sql_query);
			if(!empty($societyid) && $societyid !== 0){
				$Society_sms_details_query = "SELECT sms_senderid FROM society WHERE society_id = '".$societyid."'";
			}else
			{
				$Society_sms_details_query = "SELECT sms_senderid FROM society WHERE society_id = '".$_SESSION['society_id']."'";
			}
			$Society_sms_details = $this->m_dbConnRoot->select($Society_sms_details_query);
			if(!empty($Society_sms_details[0]['sms_senderid']))
			{
				$sms_data[0]['sms_senderid'] = $Society_sms_details[0]['sms_senderid']; 
			}
			$Template = $this->getSMSTemplateString($mobileNumber, $SMSBody,$client_id,$societyid);
			$xml = simplexml_load_string($Template);
			$User = "";
			$Key = "";
			
			
			// Prepare the url to call API as per different domains
			
			$vendor = $sms_data[0]['sms_vendor'];
			//echo $vendor;
			$response = "";
			
			if($vendor == 1)// surewingroup Vendor
			{
				$URL = $sms_data[0]['sms_domain']."submitsms.jsp?";
				$User = $xml->child->user;
				$Key = $xml->child->key;
				
				if($Template <> "" && $User <> "" && $Key <> "")
				{
					$ch = curl_init($URL);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
					curl_setopt($ch, CURLOPT_POSTFIELDS, "$Template");
				}
				else
				{
					$response = "Invalid or empty credentials User: ".$xml->child->user." key : ".$xml->child->key;
				}
			}
			else if($vendor == 2)// 360marketings Vendor
			{
				
				$SMSBody = str_replace(' ','%20',$SMSBody);
				
				//echo $sms_data[0]['sms_domain'];
				$domain = trim($sms_data[0]['sms_domain']);
//echo $domain."data";

				 $URL =$domain."?user=".$sms_data[0]['sms_key']."&password=".$sms_data[0]['sms_key']."&senderid=".$sms_data[0]['sms_senderid']."&channel=TRANS&DCS=0&flashsms=0&number=".$mobileNumber."&text=".$SMSBody."&route=".$sms_data[0]['sms_userid'];
			
				//die();

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $URL);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$response = curl_exec($ch);
				curl_close($ch);
				
				$response = json_decode($response,true);
				//print_r($response);
				$response = '@@@,'.strtolower($response['ErrorMessage']);
			}
			else if($vendor == 3)// kutility Vendor
			{
				if ($template_type == 0)
				{
					$template_id = BILL_REMINDER_TEMP_ID;
				}
				elseif ($template_type == 1)
				{
                    			$template_id = BILL_NOTIFICATION_TEMP_ID;
				}
				 $SMSBody = str_replace(' ','%20',$SMSBody);
				//echo $URL = $sms_data[0]['sms_domain']."?key=".$sms_data[0]['sms_key']."&routeid=".$sms_data[0]['sms_userid']."&type=text&contacts=".$mobileNumber."&senderid=".$sms_data[0]['sms_senderid']."&msg=".$SMSBody."&fl=0&gwid=2";
				
				$URL =$sms_data[0]['sms_domain']."?key=".$sms_data[0]['sms_key']."&campaign=11414&routeid=".$sms_data[0]['sms_userid']."&type=text&contacts=".$mobileNumber."&senderid=".$sms_data[0]['sms_senderid']."&msg=".$SMSBody."&template_id=".$template_id;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $URL);
				
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$response = curl_exec($ch);
				curl_close($ch);
				
				$err = explode(':',$response);
				if(empty($err[1]))
				{
					$response = '@@@,success';
				}
				else
				{
					$response = '@@@,'.$err[1];	
				}
			}
			
			$sql_query2="update `client` set `sms_counter`= (`sms_counter` + 1) where `id`='".$client_id. "'";
			$this->m_dbConnRoot->update($sql_query2);

			return $response;
			
		}
	
		public function GetSMSDeliveryReport($MessageID,$client_id=0)
		{
			if($client_id <> 0)
			{
				$sql_query = "select `sms_userid`,`sms_key`,`sms_domain` from `client` where `id` = '" .$client_id. "'";
				$sms_data = $this->m_dbConnRoot->select($sql_query);
				
			
				$data	= '<?xml version="1.0"?>
							<parent>
							<child>
							<user>'.$sms_data[0]['sms_userid'].'</user>
							<key>'.$sms_data[0]['sms_key'].'</key>
							<messageid>'.$MessageID.'</messageid>
							</child>	
							</parent>';
				
				$xml = simplexml_load_string($data);
				$response = "";
			
				if($data <> "" && $xml->child->user <> "" && $xml->child->key <> "")
				{			
					$url = $sms_data[0]['sms_domain'].'getreport.jsp?userid='.$xml->child->user.'&key='.$xml->child->key.'&messageid='.$MessageID.'';
					$ch = curl_init($url);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$response = curl_exec($ch);
					curl_close($ch);
				}
				else
				{
					$response = "Invalid or empty credentials User: ".$xml->child->user." key : ".$xml->child->key;
				}
			}
			return $response;
		}
	
		public function getYearIDFromDates($date1,$date2) // date forma must be tdd-mm-yy
		{
			$sql = "SELECT * FROM `year` where (`BeginingDate` BETWEEN '".$date1."' AND '".$date2."') OR (`EndingDate` BETWEEN '".$date1."' AND '".$date2."')";
			$res = $this->m_dbConn->select($sql);
			
			if(sizeof($res) > 0 )
			{
				//fetch previous year start and end date
				$sqlPrev = "SELECT `BeginingDate`,`EndingDate` FROM `year` where `YearID` = '".$res[0]['PrevYearID']."' ";
				$resPrev = $this->m_dbConn->select($sqlPrev);
				if(sizeof($resPrev) > 0 )
				{
					$res[0]['BeginingDatePrevYear'] = $resPrev[0]['BeginingDate'];
					$res[0]['EndingDatePrevYear'] = $resPrev[0]['EndingDate'];
				}
				
			}
			return $res;	
		}
	
	
		public function IsCurrentYearAndCreationYrMatch()
		{
			if(($_SESSION['society_creation_yearid'] <> $_SESSION['default_year']) || $_SESSION['is_year_freeze'] == 1)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		
		public function getBillnReceiptsCollection($iUnitId = 0,$IsSupplementary,$IsRegularBill,$IsFetchReceipts,$bFetchReverseCharges = false, $bFetchJournal = true, $bFetchOpening= true, $bFetchCreditNote = true,$bFetchDebitNote = true)
		{
				$BillType = 0;
				$finalArray = array();
				$LastBillDate="0000-00-00";
				//echo $iUnitId;
				if($bFetchOpening = true)
				{ 
					$sql10= "select DATE_FORMAT(Date,'%d-%m-%Y') as Date, Date as sdate,VoucherID,Debit,Credit from assetregister where LedgerID = '".$iUnitId."' AND Is_Opening_Balance=1";
					$openres = $this->m_dbConn->select($sql10);
					//$OpBalPeriodID = $this->getOpeningBalancePeriodID();
					//echo "<BR> opening balnce period id (" . $OpBalPeriodID . ")<BR>";
					$openres[0]['PeriodID']=2; //why period id = 2 hardcoded?? its actually 1
					$openres[0]['mode'] = "Opening";
					$openres[0]['Amount'] = -$openres[0]['Credit'] + $openres[0]['debit'];
					array_push($finalArray, $openres[0]);
					/*echo "<pre>";
					print_r($finalArray);
					echo "</pre>";*/
				}
				if($IsRegularBill == true)		
				{	

					$sSqlBills =  "Select  bill.BillNumber as VoucherNo,DATE_FORMAT(billreg.BillDate,'%d-%m-%Y') as Date,DATE_FORMAT(billreg.DueDate,'%d-%m-%Y') as DueDate,DATE_FORMAT(billreg.BillDate,'%Y-%m-%d') as sdate,bill.UnitID as UnitID,(bill.`BillSubTotal` + bill.`AdjustmentCredit`  + bill.`BillInterest`+ bill.`IGST`+ bill.`CGST`+ bill.`SGST`+ bill.`CESS`) as Amount,bill.BillType,bill.PeriodID,Ledger_round_off from billdetails as bill JOIN period as period ON bill.PeriodID = period.id JOIN year as yr ON yr.YearID=period.YearID JOIN billregister as billreg ON bill.PeriodID = billreg.PeriodID and bill.BillType = billreg.BillType where bill.UnitID= '" . $iUnitId  . "' AND bill.BillNumber <> 0 ";
					
					$sSqlBills .= " and bill.BillType =0 ";
					$sSqlBills .= " group by bill.PeriodID";
					
					$resBills = $this->m_dbConn->select($sSqlBills);	
					if(sizeof($resBills) > 0)
					{
						for($bill = 0;$bill < sizeof($resBills);$bill ++)
						{
							$resBills[$bill] = array(" " => "") + $resBills[$bill];
							$resBills[$bill]["ChequeNumber"] = 'Bill';
							//$resBills[$bill][" "] = "";
							$resBills[$bill]["mode"] = 'Bill';
							//$finalArray[$bill] = $resBills[$bill];
							array_push($finalArray,$resBills[$bill]);
						}
						
						$LastBillDate=$resBills[sizeof($resBills)-1]["Date"];
					}
				}
				
				
				if($IsSupplementary == true)		
				{	
				
					$sSqlBills =  "Select  bill.BillNumber as VoucherNo,DATE_FORMAT(billreg.BillDate,'%d-%m-%Y') as Date,DATE_FORMAT(billreg.DueDate,'%d-%m-%Y') as DueDate,DATE_FORMAT(billreg.BillDate,'%Y-%m-%d') as sdate,bill.UnitID as UnitID,(bill.`BillSubTotal` + bill.`AdjustmentCredit`  + bill.`BillInterest`+ bill.`IGST`+ bill.`CGST`+ bill.`SGST`+ bill.`CESS`) as Amount,bill.BillType,bill.PeriodID,Ledger_round_off from billdetails as bill JOIN period as period ON bill.PeriodID = period.id JOIN year as yr ON yr.YearID=period.YearID JOIN billregister as billreg ON bill.PeriodID = billreg.PeriodID and bill.BillType = billreg.BillType where bill.UnitID= '" . $iUnitId . "'  ";
					
					$sSqlBills .= " and bill.BillType =1 ";
					$sSqlBills .= " group by bill.PeriodID";
					
					$resBillsSupp = $this->m_dbConn->select($sSqlBills);	
					
					if(sizeof($resBillsSupp) > 0)
					{
						for($bill = 0;$bill < sizeof($resBillsSupp);$bill ++)
						{
							$resBillsSupp[$bill] = array(" " => "") + $resBillsSupp[$bill];
							$resBillsSupp[$bill]["ChequeNumber"] = 'Bill';
							//$resBillsSupp[$bill][" "] = "";
							$resBillsSupp[$bill]["mode"] = 'Bill';
							//$finalArray[$bill] = $resBillsSupp[$bill];
							array_push($finalArray,$resBillsSupp[$bill]);
						}
						$LastBillDate=$resBillsSupp[sizeof($resBillsSupp)-1]["Date"];
					}
					
				}
				$LastBillDate = getDBFormatDate($LastBillDate);

				// Fetch Payment

				$sSqlPayments =  "select vch.VoucherNo as VoucherNo,DATE_FORMAT(ChequeDate,'%d-%m-%Y') as Date,DATE_FORMAT(ChequeDate,'%Y-%m-%d') as sdate,PaidTo as UnitID,Amount,ChequeNumber,Comments, periodtbl.ID as PeriodID from paymentdetails JOIN `voucher` as vch on paymentdetails.ID = vch.RefNo  JOIN `period` as periodtbl on paymentdetails.ChequeDate >= periodtbl.BeginingDate and  paymentdetails.ChequeDate <= periodtbl.EndingDate where PaidTo='" .$iUnitId."'";
					
				$sSqlPayments .= "  and  vch.RefTableID = ".TABLE_PAYMENT_DETAILS." group by  vch.VoucherNo order by VoucherDate DESC";
					
				$resPayments = $this->m_dbConn->select($sSqlPayments);
					
				if(sizeof($resPayments) > 0)
				{
					for($Payment = 0;$Payment < sizeof($resPayments);$Payment ++)
					{
						$resPayments[$Payment] = array(" " => "") + $resPayments[$Payment];
						$resPayments[$Payment]['mode'] = 'Payment';
						array_push($finalArray,$resPayments[$Payment]);
					}
				}




				//fetch receipts
				if($IsFetchReceipts == true)
				{
					$sSqlReceipts =  "select vch.RefNo, vch.VoucherNo as VoucherNo,DATE_FORMAT(ChequeDate,'%d-%m-%Y') as Date,DATE_FORMAT(ChequeDate,'%Y-%m-%d') as sdate,PaidBy as UnitID,Amount,BillType,ChequeNumber,PayerBank, IsReturn, periodtbl.ID as PeriodID from chequeentrydetails JOIN `voucher` as vch on chequeentrydetails.ID = vch.RefNo  JOIN `period` as periodtbl on chequeentrydetails.voucherdate >= periodtbl.BeginingDate and  chequeentrydetails.voucherdate <= periodtbl.EndingDate where PaidBy='" .$iUnitId. "' ";
					
					
					if($IsSupplementary ==false || $IsRegularBill == false)
					{
						if($IsRegularBill == true)
						{
							$sSqlReceipts .= " and BillType = 0 ";
						}
						else if($IsSupplementary == true)
						{
							$sSqlReceipts .= " and BillType =1 ";
						}
					}
					$sSqlReceipts .= "  and  vch.RefTableID = ".TABLE_CHEQUE_DETAILS." group by  vch.VoucherNo order by VoucherDate DESC";
					
					$resReceipts = $this->m_dbConn->select($sSqlReceipts);
					if(sizeof($resReceipts) > 0)
					{
						for($Receipt = 0;$Receipt < sizeof($resReceipts);$Receipt ++)
						{
							$resReceipts[$Receipt] = array(" " => "") + $resReceipts[$Receipt];
							//$resReceipts[$Receipt][" "] = "";
							$resReceipts[$Receipt]['mode'] = 'Receipt';
							$resReceipts[$Receipt]['IsReturn'] = $resReceipts[$Receipt]['IsReturn'];
							//$finalArray[sizeof($finalArray) -1] = $resReceipts[$Receipt];
							array_push($finalArray,$resReceipts[$Receipt]);
							//array_push($comArray,$resReceipts[$Receipt]);
						}
					}
				}
				
				
				if($bFetchReverseCharges == true)
				{
					$ledgername_array=array();
					
					//echo $sql = "SELECT *,max(PeriodID) as PeriodID FROM `billdetails` where  `UnitID` = '" . $iUnitId."'";
					//$res =$this->m_dbConn->select($sql);
					
					$sqlCheck = "select DATE_FORMAT(Date,'%d-%m-%Y') as Date,DATE_FORMAT(Date,'%Y-%m-%d') as sdate,Amount,LedgerID from `reversal_credits` where Date >= '".$LastBillDate. "' AND `UnitID` = '" . $iUnitId."' ";
					//echo "reverse :".$sqlCheck;
					$resultCheck = $this->m_dbConn->select($sqlCheck);
					//echo gettype($resultCheck);
					if(isset($resultCheck))
					{
						$get_ledger_name="select id,ledger_name from `ledger`";
						$result02=$this->m_dbConn->select($get_ledger_name);
					
						for($i = 0; $i < sizeof($result02); $i++)
						{
							$ledgername_array[$result02[$i]['id']]=$result02[$i]['ledger_name'];
					
						}
						for($i = 0; $i < sizeof($resultCheck); $i++)
						{
							$resultCheck[$i]['mode'] = 'ReverseCharge';
							$resultCheck[$i]['PaidTo'] = $ledgername_array[$resultCheck[$i]['LedgerID']];
							array_push($finalArray,$resultCheck[$i]);
						}	
					}
				}

				if($bFetchCreditNote == true)
				{
					$ledgername_array=array();
					
					$sqlCheck = "select DATE_FORMAT(Date,'%d-%m-%Y') as Date,DATE_FORMAT(Date,'%Y-%m-%d') as sdate,TotalPayable as Amount, UnitID, CONCAT('".PREFIX_CREDIT_NOTE."','-',Note_No) as VoucherNo, ID from `credit_debit_note` where `UnitID` = '" . $iUnitId."' AND `Note_type` = '".CREDIT_NOTE."' ";
					
					$resultCheck = $this->m_dbConn->select($sqlCheck);
					
					if(isset($resultCheck))
					{
						$get_ledger_name="select id,ledger_name from `ledger`";
						$result02=$this->m_dbConn->select($get_ledger_name);
					
						for($i = 0; $i < sizeof($result02); $i++)
						{
							$ledgername_array[$result02[$i]['id']]=$result02[$i]['ledger_name'];
					
						}
						for($i = 0; $i < sizeof($resultCheck); $i++)
						{
							$resultCheck[$i]['mode'] = 'CreditNote';
							
							$resultCheck[$i]['PaidTo'] = $ledgername_array[$resultCheck[$i]['UnitID']];
							array_push($finalArray,$resultCheck[$i]);
						}	
					}
				}
				if($bFetchDebitNote == true)
				{
					$ledgername_array=array();
					
					$sqlCheck = "select DATE_FORMAT(Date,'%d-%m-%Y') as Date,DATE_FORMAT(Date,'%Y-%m-%d') as sdate,TotalPayable as Amount, UnitID, CONCAT('".PREFIX_DEBIT_NOTE."','-',Note_No) as VoucherNo, ID from `credit_debit_note` where `UnitID` = '" . $iUnitId."' AND `Note_type` = '".DEBIT_NOTE."' ";
					
					$resultCheck = $this->m_dbConn->select($sqlCheck);
					
					if(isset($resultCheck))
					{
						$get_ledger_name="select id,ledger_name from `ledger`";
						$result02=$this->m_dbConn->select($get_ledger_name);
					
						for($i = 0; $i < sizeof($result02); $i++)
						{
							$ledgername_array[$result02[$i]['id']]=$result02[$i]['ledger_name'];
					
						}
						for($i = 0; $i < sizeof($resultCheck); $i++)
						{
							$resultCheck[$i]['mode'] = 'DebitNote';
							
							$resultCheck[$i]['PaidTo'] = $ledgername_array[$resultCheck[$i]['UnitID']];
							array_push($finalArray,$resultCheck[$i]);
						}	
					}
				}

				if($bFetchJournal == true)
				{
					$ledgername_array=array();
					
					//echo $sql = "SELECT *,max(PeriodID) as PeriodID FROM `billdetails` where  `UnitID` = '" . $iUnitId."'";
					//$res =$this->m_dbConn->select($sql);
					
					$sqlCheck = "select DATE_FORMAT(Date,'%d-%m-%Y') as Date,DATE_FORMAT(Date,'%Y-%m-%d') as sdate,Credit,Debit,LedgerID,VoucherID from `assetregister` where `VoucherTypeID`=5 AND `LedgerID` = '" . $iUnitId."' ";
					$resultCheck = $this->m_dbConn->select($sqlCheck);
					//echo gettype($resultCheck);
					if(isset($resultCheck))
					{
						$vid=$resultCheck[0]['VoucherID']; 
						$sql1 = "select `desc` from `vouchertype` where id=5";		
						$data1 = $this->m_dbConn->select($sql1);
						
						$voucher = $data1[0]['desc'];	
						$sql2 = "select RefNo,RefTableID,VoucherNo from `voucher` where id='".$vid."' ";
						//echo $sql2;
						$data2 = $this->m_dbConn->select($sql2);
						if(isset($data2))
						{
							$RefNo = $data2[0]['RefNo'];
							$RefTableID = $data2[0]['RefTableID'];
							$VoucherNo = $data2[0]['VoucherNo']; 
							if($voucher == "Journal Voucher")
							{
								$get_ledger_name= "select ledgertbl.id,`ledger_name`,vouchertbl.Note,vouchertbl.RefNo,vouchertbl.VoucherNo,vouchertbl.By as 'To' from `voucher` as vouchertbl JOIN `ledger` as ledgertbl on vouchertbl.By=ledgertbl.id where vouchertbl.RefNo='".$RefNo."' AND vouchertbl.RefTableID='".$RefTableID."' and vouchertbl.VoucherNo='".$VoucherNo."'";
								$result02=$this->m_dbConn->select($get_ledger_name);
							}
							if(isset($result02))
							{
								for($i = 0; $i < sizeof($result02); $i++)
								{
									$ledgername_array[$result02[$i]['id']]=$result02[$i]['ledger_name'];
					
								}
								for($i = 0; $i < sizeof($resultCheck); $i++)
								{
									$resultCheck[$i]['mode'] = 'Journal';
									$resultCheck[$i]['PaidTo'] = $ledgername_array[$result02[$i]['id']];
									$resultCheck[$i]['VoucherNo']=$voucher;
									array_push($finalArray,$resultCheck[$i]);
									//array_push($comArray,$resultCheck[$i]);
								}
							}
						}	
					}		
				}
			
				
			// Below code will do sorting on voucher number if there is more than 1 records present with same date
			
			// Code Start
			
			$finalArray  = $this->sortArray($finalArray,"sdate");
			$cnt = 0;
			$tempArray[$finalArray[0]['Date']][$cnt] = $finalArray[0];	
			$prevDate = $finalArray[0]['Date'];
			for ($i=1; $i < count($finalArray); $i++) { 
				
				if($prevDate == $finalArray[$i]['Date'])
				{
					$cnt++;	
					$tempArray[$finalArray[$i]['Date']][$cnt] = $finalArray[$i];
				}
				else
				{
					$cnt = 0;
					$tempArray[$finalArray[$i]['Date']][$cnt] = $finalArray[$i]; 
					$prevDate = $finalArray[$i]['Date'];
				}
			}
			

			
			$sortedArray = array();
			foreach ($tempArray as $value) {
				
				if(count($value) > 1)
				{
					$base = array_column($value,'VoucherNo');
					krsort(arsort($base));
					$base_keys = array_keys($base);
					krsort($base_keys);

					foreach ($base_keys as $data) {

						array_push($sortedArray, $value[$data]);
						
					}
				}
				else
				{
					array_push($sortedArray, $value[0]);
				}  
			}

		//Code End	

			return $sortedArray;
			
		}
		function compare($iUnitId = 0, $IsSupplementary, $IsRegularBill)
		{
			$comArray=array();
			$sql10= "select Date,Debit,Credit from assetregister where LedgerID = '".$iUnitId."' AND Is_Opening_Balance=1";
					$openres = $this->m_dbConn->select($sql10);
					$openres[0]['PeriodID']=2;
					$openres[0]['mode'] = "Opening";
					$openres[0]['Amount'] = $openres[0]['Debit']+$openres[0]['Credit']; 
					
					array_push($comArray, $openres[0]);
			

			$sql7 ="select bill.UnitID as UnitID,(bill.`BillSubTotal` + bill.`AdjustmentCredit`  + bill.`BillInterest`+ bill.`IGST`+ bill.`CGST`+ bill.`SGST`+ bill.`CESS`) as Amount,bill.BillType,bill.PeriodID,billreg.BillDate as Date from billdetails as bill JOIN period as period ON bill.PeriodID = period.id JOIN year as yr ON yr.YearID=period.YearID JOIN billregister as billreg ON bill.PeriodID = billreg.PeriodID where bill.UnitID= '" . $iUnitId  . "' AND bill.BillNumber <> 0 ";
					
				
				$sql7 .= " group by bill.PeriodID"; 
			$block = $this->m_dbConn->select($sql7);
			
			$balanceAmt=0;
			if(sizeof($block) > 0)
			{
				for($i=0;$i < sizeof($block);$i++)
				{
					$block[$i] = array(" " => "") + $block[$i];
					$block[$i]['mode'] = 'Bill';
					//$balanceAmt=$balanceAmt+$block[$i]['Amount'];
					array_push($comArray, $block[$i]);
				}
			}
			

			$sql8 =  "select PaidBy as UnitID,Amount,BillType,ChequeDate as Date,periodtbl.ID as PeriodID from chequeentrydetails JOIN `voucher` as vch on chequeentrydetails.ID = vch.RefNo  JOIN `period` as periodtbl on chequeentrydetails.voucherdate >= periodtbl.BeginingDate and  chequeentrydetails.voucherdate <= periodtbl.EndingDate where PaidBy='" .$iUnitId. "' ";
					
					
					if($IsSupplementary ==false || $IsRegularBill == false)
					{
						if($IsRegularBill == true)
						{
							$sql8 .= " and BillType = 0 ";
						}
						else if($IsSupplementary == true)
						{
							$sql8 .= " and BillType =1 ";
						}
					}
					$sql8 .= "  and  vch.RefTableID = ".TABLE_CHEQUE_DETAILS." group by  vch.VoucherNo order by VoucherDate ASC";
					
			
				$block1 = $this->m_dbConn->select($sql8);
				//print_r($block1);
			if(sizeof($block1) > 0)
			{
				for($i=0;$i < sizeof($block1);$i++)
				{
					$block1[$i] = array(" " => "") + $block1[$i];
					$block1[$i]['mode'] = 'Receipt';
					//$balanceAmt=$balanceAmt+$block[$i]['Amount'];
					array_push($comArray, $block1[$i]);
				}
			}
			$checkArr = array();
			$checkArr[0] = $comArray[0]['Amount'];
			$comArray = $this->sortArray($comArray,"Date");
			for($i=1;$i < sizeof($comArray);$i++)
			{
				if($comArray[$i]['mode']=='Bill')
				{
					$balanceAmt=$balanceAmt+$comArray[$i]['Amount'];
					$checkArr[$i]=$balanceAmt;
				}

				elseif($comArray[$i]['mode']=='Receipt')
				{
					$balanceAmt=$balanceAmt - $comArray[$i]['Amount'];
					$checkArr[$i]=$balanceAmt;
				}
			}
			
			$sql9 = "select Debit,Credit,VoucherID,Date from assetregister where LedgerID = '" . $iUnitId."' ";
			$block2 = $this->m_dbConn->select($sql9);
			$block2 = $this->sortArray($block2,"Date");
			$blockArr = array();
			for($i=0;$i < sizeof($block2);$i++)
			{
				$totalAmt=$totalAmt + $block2[$i]['Debit'] - $block2[$i]['Credit'];
				$blockArr[$i]=$totalAmt;
				
			}

			$p=array();
			for($i=0;$i<sizeof($blockArr);$i++)
			{
				if($checkArr[$i]!=$blockArr[$i])
				{
					$p[$i]=1;

				}
				else
					$p[$i]=0;

			}
			return $p;
		}
		
		//function compareBillAmount($iUnitId, $BillType)
		function FetchBillAmountGroupByPeriod($iUnitId)
		{
			
			 $sql = "Select UnitID, PeriodID, SUM(BillSubTotal + AdjustmentCredit + BillTax + IGST + CGST + SGST + CESS + BillInterest) as CurrentBillAmountCalculated, CurrentBillAmount, SUM(CurrentBillAmount + PrincipalArrears +InterestArrears) as TotalBillPayableCalculated, TotalBillPayable, BillType from `billdetails` WHERE UnitID = '" . $iUnitId."' group by PeriodID, BillType";
	
			$result = $this->m_dbConn->select($sql);
			
			$resultArray = array();
			for($i = 0; $i < sizeof($result); $i++)
			{
				$resultArray[$result[$i]['PeriodID']][$result[$i]['BillType']]['TotalBillPayable']= $result[$i]['TotalBillPayable'];
				$resultArray[$result[$i]['PeriodID']][$result[$i]['BillType']]['TotalBillPayableCalculated'] = $result[$i]['TotalBillPayableCalculated'];
				$resultArray[$result[$i]['PeriodID']][$result[$i]['BillType']]['CurrentBillAmount'] = $result[$i]['CurrentBillAmount'];
				$resultArray[$result[$i]['PeriodID']][$result[$i]['BillType']]['CurrentBillAmountCalculated'] = $result[$i]['CurrentBillAmountCalculated'];
			}
			
			return $resultArray;
		}
		
		/*function compareBillAmountForAdmin($iUnitId)
		{
			
			//echo $sql = "Select UnitID, PeriodID,  TotalBillPayable, BillType from `billdetails` WHERE UnitID = " . $iUnitId;
		$sql = "Select UnitID, PeriodID, SUM(BillSubTotal + AdjustmentCredit + BillTax + IGST + CGST + SGST + CESS + BillInterest) as CurrentBillAmountCalculated, CurrentBillAmount, SUM(CurrentBillAmount + PrincipalArrears +InterestArrears) as TotalBillPayableCalculated, TotalBillPayable, BillType from `billdetails` WHERE UnitID = '" . $iUnitId."' group by PeriodID, BillType";
			
			
			$result = $this->m_dbConn->select($sql);
			
			$resultArray = array();
			for($i = 0; $i < sizeof($result); $i++)
			{
				$resultArray[$result[$i]['PeriodID']][$result[$i]['BillType']]['TotalBillPayable']= $result[$i]['TotalBillPayable'];
				//$resultArray[$result[$i]['PeriodID']][$result[$i]['BillType']]['TotalBillPayable']= $result[$i]['TotalBillPayable'];
				//$resultArray[$result[$i]['PeriodID']][$result[$i]['BillType']]['TotalBillPayableCalculated'] = $result[$i]['TotalBillPayableCalculated'];
				//$resultArray[$result[$i]['PeriodID']][$result[$i]['BillType']]['CurrentBillAmount'] = $result[$i]['CurrentBillAmount'];
				//$resultArray[$result[$i]['PeriodID']][$result[$i]['BillType']]['CurrentBillAmountCalculated'] = $result[$i]['CurrentBillAmountCalculated'];
			}
			//print_r($resultArray);
			return $resultArray;
		}
		*/
		function compareBill($iUnitId)
		{	
			
			
			$sql13 = "select ID,(BillSubTotal + BillInterest + PrincipalArrears + InterestArrears) as Amount,BillType,PeriodID from billdetails where UnitId ='".$iUnitId."'AND BillNumber <> 0 AND BillType = 0 ORDER by PeriodID ASC";
			$regbill = $this->m_dbConn->select($sql13);
			
			$sql14 = "select ID,(BillSubTotal+BillInterest+PrincipalArrears+InterestArrears) as Amount,BillType,PeriodID from billdetails where UnitId ='".$iUnitId."' AND BillType = 1";
			$supbill = $this->m_dbConn->select($sql14);//todo: order by periodid;
			
			$sql15 = "select a.Date,a.Debit,b.RefNo as ID from assetregister as a join Voucher as b on a.VoucherID= b.id where a.LedgerId ='".$iUnitId."' AND a.VoucherTypeID = 1 ORDER by Date"; //todo : use VoucherId; via imp;
			$p=array();
			$j=0;
			$k=0;
			$sales = $this->m_dbConn->select($sql15);
			$pay=0;
			$reg_pay=0;
			$sup_pay=0;
		
			for($i=0;$i<sizeof($sales);$i++)
			{
				if ($sales[$i]['ID']==$regbill[$j]['ID'])
				{
					
					if($pay+$sales[$i]['Debit'] == $regbill[$j]['Amount']+$sup_pay)
					{
						$p[$i]=0;// todo:convert into multi dimensional and use period;
						$pay=$pay+$sales[$i]['Debit'];
						$reg_pay=$reg_pay + $sales[$i]['Debit'];
						
					}
					else
					{
						$p[$i]=1;
					}
					$j=$j+1;

				}
				else if($sales[$i]['ID']==$supbill[$k]['ID'])
				{	
					if($pay+$sales[$i]['Debit'] == $supbill[$k]['Amount']+$reg_pay)
					{
						$p[$i]=0;
						$pay=$pay + $sales[$i]['Debit'];
						$sup_pay=$sup_pay+ $sales[$i]['Debit'];
					
					}
					else
					{
						$p[$i]=1;
					}
					$k=$k+1;
				}
			}

			
			return $p;
		}
		
		function sortArray($array,$inputKey)
		{
			foreach($array as $key=>$value){
				$arr_Ref[$key] = $array[$key][$inputKey];
				}
			array_multisort($arr_Ref, SORT_ASC,$array);	
			return $array;	
		}
		
		function GetEmailHeader()
		{
			$mailText = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
								   <table width="100%">';
			return $mailText;							  	
		}
	
		function GetEmailFooter()
		{
			$mailText = '<tr><td><br /></td></tr>
									<tr>
										<td font="colr:#999999;">Thank You,<br>way2society.com</td>
									</tr>
								   </table>
								 </td>
							   </tr>
							   <tr>
								 <td bgcolor="#CCCCCC" style="padding: 20px 20px 20px 20px;border-top:none;">
								   <table cellpadding="0" cellspacing="0" width="100%">           
									 <td >             
										<a href="http://way2society.com/" target="_blank"><i>Way2Society</i></a>              
									 </td>
									 <td align="center"  style="padding: 0px 50px 0px 1px;">
								 		<table>
                                 		<tr>
                                 		<td><a href="https://play.google.com/store/apps/details?id=com.ionicframework.way2society869487&amp;rdid=com.ionicframework.way2society869487" target="_blank">
										<img src="http://way2society.com/images/app.png" width="120" height="50" style="style=" top:10px;"></a></td></tr>				
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
			return $mailText;					
		}
		
		function GetLedgerDetails($LedgerID = 0)
		{

			$LedgerDetails = array();

			if($LedgerID == 0)
			{
				$sql = "SELECT * from `ledger`";		
			}
			else
			{
				$sql = "SELECT * from `ledger` WHERE `id` = '" . $LedgerID . "'";  
			}

			$result = $this->m_dbConn->select($sql);

			for($iCnt = 0; $iCnt < sizeof($result); $iCnt++)
			{
				$LedgerDetails[$result[$iCnt]['id']]['General'] = $result[$iCnt];
			}

			return $LedgerDetails;
		}

		function GetMemberPersonalDetails($UnitID)
		{
			$sqlQry  =  "select `email`,`mob` from `member_main` where `unit` = '".$UnitID."' AND `ownership_status` = 1";
			return $this->m_dbConn->select($sqlQry);
		}
		function GetUnitNo($UnitID)
		{
			//SELECT u.unit_no, m.primary_owner_name FROM `member_main` m, `unit` u where u.unit_id=m.unit and m.unit=16
			$sqlQry1 = "select u.`unit_no`, m.`primary_owner_name` from `member_main` m, `unit` u where u.`unit_id` = m.`unit` and m.`unit`='".$UnitID."' and m.ownership_status = 1";
			return $this->m_dbConn->select($sqlQry1);
		}
		function GetUnitDetailsByMobileNo($MobileNumber)
		{
			//SELECT u.unit_no, m.primary_owner_name FROM `member_main` m, `unit` u where u.unit_id=m.unit and m.unit=16
			$sqlQry1 = "select u.`unit_id`, u.`unit_no`, m.`primary_owner_name`,m.mob as mobile_no from `member_main` m, `unit` u where u.`unit_id` = m.`unit` and m.`mob`='".$MobileNumber."'";
			//$sqlQry1 = "SELECT mem_other_family.mem_other_family_id as id, mem_other_family.other_email as to_email, mem_other_family.other_mobile as mobile_no, mem_other_family.other_name as to_name, IF(mem_other_family.mem_other_family_id > 0, mem_other_family.coowner, 0) as type, unit.unit_id as unit FROM `mem_other_family` JOIN `member_main` on mem_other_family.member_id = member_main.member_id JOIN `unit` on unit.unit_id = member_main.unit where mem_other_family.send_commu_emails = 1  and mem_other_family.other_mobile='".$MobileNumber."' and member_main.member_id IN (SELECT member_main.`member_id` FROM (select `member_id` from `member_main` where `ownership_date` <= NOW() ORDER BY `ownership_date` desc) as member_id Group BY unit) UNION select tmem.tmember_id as id, tmem.email as to_email,tmem.contact_no as mobile_no, tmem.mem_name as to_name, IF(tmem.tmember_id > 0, 10, 0) as type, tmod.unit_id as unit from tenant_member as tmem JOIN tenant_module as tmod ON tmem.tenant_id = tmod.tenant_id where tmod.end_date > NOW() AND tmem.send_commu_emails=1";
			//echo $sqlQry1;
			return $this->m_dbConn->select($sqlQry1);
		}
		function IsUnitExist($UnitID)
		{
			$sqlQry1 = "select unit_id,`unit_no` from unit where `unit_id`='".$UnitID."'";
			
			return $this->m_dbConn->select($sqlQry1);	
		}
		function GetUnitNoForDD()
		{
			$sqlQry2 = "select u.`unit_no`, m.`primary_owner_name` from `member_main` m, `unit` u where u.`unit_id` = m.`unit`";
			return $this->m_dbConn->select($sqlQry2);
		}

		function GetLoginDetails()
		{
            $LoginID = $_SESSION["login_id"];
			$sqlQry  =  "select * from `login` where `login_id` = '".$LoginID."' AND `status` = 'Y'";
            //echo $sqlQry;
			return $this->m_dbConnRoot->select($sqlQry);
		}
		function GetSocietyInformation($SocietyID)
		{
		    $sql = "Select `apply_service_tax`, `service_tax_threshold`, `igst_tax_rate`,`gstin_no`,`cgst_tax_rate`, `sgst_tax_rate`, `cess_tax_rate`, `society_name`, `apply_GST_on_Interest`, `apply_GST_above_Threshold` ,`int_rate`,`int_method`,`rebate_method`,`int_tri_amt`,`rebate_method`,`rebate`,`bill_cycle`, `apply_receipt_to_principal_first`, `balancesheet_template`, `reco_date_same_as_voucher`, `IsRoundOffLedgerAmt`, `ShowSuppBillBalanceInMaintBill`, `SuppBillBalanceDisplayText`,`gstin_no`,`Auth_Share_Capital_Text`,`Auth_Share_Capital_Amount` from society where `society_id` = '" . $SocietyID . "'";
			$result = $this->m_dbConn->select($sql);
			return $result[0];
		}
		function GetPaymentGatewayDetails($SocietyID)
        {
          $sql = "Select `PaymentGateway`, `PGSalt`, `PGKey` from society where `society_id` = '" . $SocietyID . "'";
            $result = $this->m_dbConn->select($sql);
            return $result[0];
        }
        function GetPaymentGatewayBankID()
        {
        	$result = $this->m_dbConn->select("select `PGBeneficiaryBank` from society");
        	//echo $result[0]['PGBeneficiaryBank'];
        	return $result[0]['PGBeneficiaryBank'];
        }
        function GetPaymentGatewayTransactionStatus($TransactionID)
        {
        	$result = $this->m_dbConn->select("select * from `paymentgatewaytransactions` where TranxID='".$TransactionID."'");
        	//echo $result[0]['PGBeneficiaryBank'];
        	return $result[0]['Status'];
        }
		function GetGDriveDetails()
		{
			$sqlSelect = "select GDrive_W2S_ID,GDrive_Credentials,GDrive_UserID from `society`";
			//echo $sqlSelect;
			//print_r($m_dbConn);
			$res = $this->m_dbConn->select($sqlSelect);
			return $res;
		}
		function SetGDriveCredentails($credentials)
		{
			$sqlUpdate = "update society set GDrive_Credentials='".$credentials."'";
			$res = $this->m_dbConn->update($sqlUpdate);
			//echo "updated id:".$res;
		}
		function GetUnitDesc($UnitID)
		{

			$sqlUnit = "select unit_no from `unit` where unit_id='".$UnitID."'";
			$resUnit = $this->m_dbConn->select($sqlUnit);
			//print_r($resUnit);
			return $resUnit;
		}
		function GetDocTypeByID($sDocID)
		{
			$sqlDocName = "select * from `document_type` where ID='".$sDocID."'";
			//echo "sz:".$sqlDocName;
			$resDocName = $this->m_dbConn->select($sqlDocName);
			//echo "sz:".$sizeof($resDocName);
			return $resDocName;	
		}
		public function GetMyLoginDetails($LoginID = 0)
		{
			if($LoginID == 0)
			{
				$LoginID = $_SESSION["login_id"];					
			}
			$sqllogin = "select name, member_id from login where login_id='".$LoginID."'";
			$resLogin = $this->m_dbConnRoot->select($sqllogin);
			
			return $resLogin;		
		}
		
		//***Here we Format Change log to user readable format
		public function FormatLogDescription($Description)
		{
			//**converting JSON Object into array
			$Log = json_decode($Description , true);
			//*** Declating empty variable to store Change log 
			$LogMsg = "";
			//*** It check entry first entry made in ledger loop so we append <br> tag for next line
			$EntryFoundInLedger = false;
			//** storing all the key in the prious ledegr
			$IndexSearchLedgerIDPrevious = array();
			
			
			//*** If Changes found in Bill Number then it will append on LogMsg
			if($Log["BillNumber"]["ExitingBillNumber"] <> $Log["BillNumber"]["OnUpdateBillNumber"])
			{
				//***adding msg to LogMsg
				$LogMsg .= "Bill Number changed from ".$Log["BillNumber"]["ExitingBillNumber"]." to ".$Log["BillNumber"]["OnUpdateBillNumber"];	
			}
			
			//*** If Changes found in unit then it will append on LogMsg
			if($Log["Unit"]["PreviousUnit"] <> $Log["Unit"]["NewUnit"])
			{
				//***We have unitID so Need to fetch unit number so user can understand which unit or flat changed
				$PreUnitNo = $this->GetmemberDetail($Log["Unit"]["PreviousUnit"]);
				$NewUnitNo = $this->GetmemberDetail($Log["Unit"]["NewUnit"]);
				//***adding msg to LogMsg
				$LogMsg .= "Unit changed from ".$PreUnitNo[0]["unit_no"]." to ".$NewUnitNo[0]["unit_no"];	
			}
			
			//*** If changes found in Bill Date then it will append in LogMsg
			if($Log["Date"]["PreviousDate"] <> $Log["Date"]["NewUnitDate"])
			{
				//**Here verifying $logMSg append above msg or not if yes then adding "and"  to join date sentence
				if($LogMsg <> "")
				{
					$LogMsg .= " and "; 
				}
				//**appending date changes in LogMSG
				$LogMsg .= "Date changed from ".getDisplayFormatDate($Log["Date"]["PreviousDate"]). " to ".getDisplayFormatDate($Log["Date"]["NewUnitDate"]);
			}
				
			for($i = 0; $i < sizeof($Log["PriviousLedgers"]); $i++)
			{
				//** Push the LedgerID in New Array so we use search in array function
				array_push($IndexSearchLedgerIDPrevious,$Log["PriviousLedgers"][$i]["LedgerID"]);
			}
			
			//**Now here if LogMsg is not empty then we add break for add ledger details 
			if($LogMsg <> "")
			{
				$LogMsg .= "<br>";
			}
			
			//**Now we compare the change done in updated ledgers with old one
			for($i = 0; $i < sizeof($Log["UpdatedLedgers"]); $i++)
			{	
				//**It add break on every single entry found in ledger
				if($EntryFoundInLedger == true)
				{
					$LogMsg .= "<br>";	
				}
				
				//** Now here we check whether updated ledger present in previous entry
				$IndexOfLedger = array_search($Log["UpdatedLedgers"][$i]["LedgerID"],$IndexSearchLedgerIDPrevious);
				
				//**Getting ledger related data	
				$LedgerDetails = $this->getParentOfLedger($Log["UpdatedLedgers"][$i]["LedgerID"]);	
				
				//** If No Index found from array_search so we assume it's new ledger
				if($IndexOfLedger == "" && $IndexOfLedger !== 0)
				{
					$EntryFoundInLedger = true;
					$LogMsg .= $LedgerDetails["ledger_name"]." new ledger added amount is ".$Log["UpdatedLedgers"][$i]["Amount"].".";
				}
				else
				{
					//**Unset means we removing array element from IndexSearchLedgerIDPrevious which ledger we found so at the end we know if any entry is remaining in this array that is removed from updated ledgers
					unset($IndexSearchLedgerIDPrevious[$IndexOfLedger]);
								
					//*** If entry found in array_search then check whether it's amount chnage or not
					if($Log["UpdatedLedgers"][$i]["Amount"] <> $Log["PriviousLedgers"][$IndexOfLedger]["Amount"])
					{
						$EntryFoundInLedger = true;
						$LogMsg .= $LedgerDetails["ledger_name"]." Amount ".$Log["PriviousLedgers"][$IndexOfLedger]["Amount"]." Changed to ".$Log["UpdatedLedgers"][$i]["Amount"];							
					}	
				}
			}
			
			//**Here we check after unset the array any entry is remain or not
			if(count($IndexSearchLedgerIDPrevious) > 0)
			{
				//** so we put loop on it and append in log as these entry is removed
				for($j = 0 ; $j < count($IndexSearchLedgerIDPrevious); $j++)
				{
					$LedgerDetails = $this->getParentOfLedger($Log["PriviousLedgers"][$j]["LedgerID"]);
					
					$LogMsg .= $LedgerDetails["ledger_name"]." ledger removed amount was ".$Log["PriviousLedgers"][$j]["Amount"].".";
				}
			}
			
			//*** If Changes found in Bill Total then it will append on LogMsg
			if($Log["BillTotal"]["PrevBillTotal"] <> $Log["BillTotal"]["OnUpdateBillTotal"])
			{
				//***adding msg to LogMsg
				$LogMsg .= "Bill Total changed from ".$Log["BillTotal"]["PrevBillTotal"]." to ".$Log["BillTotal"]["OnUpdateBillTotal"];	
			}
			
			//***CheckBillType
			if($Log["BillType"]["Previous"] <> $Log["BillType"]["Current"])
			{
				//***adding msg to LogMsg
				$LogMsg .= "Bill Type changed from ".$this->returnBillTypeString($Log["BillType"]["Previous"])." to ".$this->returnBillTypeString($Log["BillType"]["Current"])."";	
			}
			
			return $LogMsg ;
		}
		
		public function returnBillTypeString($BillType)
		{
			if($BillType == Maintenance)
			{
				return "Maintenance";
			}
			else if($BillType == Supplementry)
			{
				return "Supplementry";
			}
			else if($BillType == Invoice)
			{
				return "Invoice";
			}
			else if($BillType == CREDIT_NOTE)
			{
				return "Credit Note";
			}
			else if($BillType == DEBIT_NOTE)
			{
				return "Debit Note";
			}
		}
		//*****This function return time and date in properformat by taking timestamp
		public function DateAndTimeConversion($TimeStamp)
		{
			//** Declaring array to store end result
			$Result = array();
			//*** Assign default value to as AM to TimeFormat
			$TimeFormat = 'AM';
			//*** Now we we sepate the date and time from timestamp
			$DateAry = explode('-',$TimeStamp); 
			$TempAry = explode(' ',$DateAry[2]);
			
			//** Here we assigning the date value
			$Year = $DateAry[0];
			$Month = $DateAry[1];
			$date = $TempAry[0];
			
			//** Here Time value
			$Time = explode(':', $TempAry[1]);
			$Hour = $Time[0];
			$Minute = $Time[1];			
			$Second = $Time[2];
			
			//** Checking the Hour is greater than 12 so we change the Timeformat from AM to PM and reassign the Hour value according to that
			if($Hour > 12)
			{
				$TimeFormat = 'PM';
				$Hour = $Hour - 12 ;  
			}
			
			//** Finally we setting our end result Date and Time with timeformat
			$Result[0]['Date'] = $date."-".$Month."-".$Year;
			$Result[0]['Time'] = $Hour.':'.$Minute.':'.$Second.' '.$TimeFormat;
			
			//** returning result back to the Requested function
			return $Result;
		}
		
		public function comboboxEx($query, $bShowPleaseSelect = true)
		{
			$id=0;
			//echo "<script>alert('test')<script>";
			if($bShowPleaseSelect)
			{
				$str.="<option value=''>Please Select</option>";
			}
			$data = $this->m_dbConn->select($query);
			//echo "<script>alert('test2')<//script>";
			if(!is_null($data))
			{
				$vowels = array('/', '*', '%', '&', ',', '(', ')', '"');
				foreach($data as $key => $value)
				{
					$i=0;
					foreach($value as $k => $v)
					{
						if($i==0)
						{
							if($id==$v)
							{
								$sel = 'selected';
							}
							else
							{
								$sel = '';
							}
							
							$str.="<OPTION VALUE=".$v.' '.$sel.">";
						}
						else
						{
							//$str.=$v."</OPTION>";
							$str.= str_replace($vowels, ' ', $v)."</OPTION>";
						}
						//echo "<script>alert('".$str."')<//script>";
						$i++;
					}
				}
			}
			//return $str;
			//print_r( $str);
			//echo "<script>alert('test')<//script>";
			return $str;
		}
		public function GetListMobileAppUsers()
		{
			$sql = "select m.unit_id,m.desc from device_details as dd JOIN login as lg on  dd.login_id = lg.login_id JOIN mapping as m on lg.login_id = m.login_id where m.society_id = '".$_SESSION['society_id']."'  and dd.device_id !='' group by m.`unit_id` order by m.`society_id`";
			//echo $sql;
			$data = $this->m_dbConnRoot->select($sql);
			//echo "<script>alert('test2')<//script>";
			//print_r($data);
			return $data;
		}

		public function GetBlockedUsersDesc()
		{
			$sql = "select block_desc from unit where unit_id='".$_SESSION["unit_id"]."'";
			
			$data = $this->m_dbConn->select($sql);
			
			return $data;
		}
		public function GetDuesFromMemberLedger()
		{
			$sql = "select APP_DEFAULT_DUE_FROM_MEMBERS from appdefault";
			
			$data = $this->m_dbConn->select($sql);
			
			return $data;
		}
		function UploadAttachment($arFILES, $doc_type_id,$PostDate, $DestFolderName, $FileIndexCount = "", $bTenantModule = false, $UnitNo = "")
		{
			echo "Upload attalcment";
			$docGDriveID = "";
			$arResponse =  array();
			try
			{
				//echo "start";
				//die();
				$fileTempName = $arFILES['userfile'.$FileIndexCount]['tmp_name'];  
				$fileSize = $arFILES['userfile'.$FileIndexCount]['size'];
				$resSociety = $this->GetGDriveDetails();
				$sGDrive_W2S_ID = $resSociety["0"]["GDrive_W2S_ID"];
				if($sGDrive_W2S_ID != "")
				{
					$fileName = basename($arFILES['userfile'.$FileIndexCount]['name']);
				}
				else
				{
					$fileName = time().'_'.basename($arFILES['userfile'.$FileIndexCount]['name']);	
				}
				if($_SERVER['HTTP_HOST']=="localhost")
				{		
					$uploaddir = $_SERVER['DOCUMENT_ROOT']."/beta_aws_12/".$DestFolderName;			   
				}
				else
				{
					$uploaddir = $_SERVER['DOCUMENT_ROOT']."/".$DestFolderName;			   
				}
				$uploadfile = $uploaddir ."/". $fileName;	
				
				if($this->m_bShowTrace)
				{
					echo $uploadfile;	
				}
				
				//die();
				if($this->m_bShowTrace)
				{
					echo "uploading to gdrive:".$uploadfile . " doctype:".$doc_type_id;	
				}
				$mimeType = $arFILES['userfile'.$FileIndexCount]['type'];
				//$documentName = time() . "_" . $arFILES['userfile']['name'] ;
				//$noticeFileName = $documentName;

				//$sqlDocName = "select doc_type from `document_type` where ID='".$doc_type_id."'";
				if($doc_type_id != "Events")
				{
					$resDocName = $this->GetDocTypeByID($doc_type_id);
					$NoticeAlias = $resDocName[0]["doc_type"];
				}
				else
				{
					$NoticeAlias = "Events";
				}

				if($this->m_bShowTrace)
				{
					echo "doctype:".$NoticeAlias;
					echo "doc_type_id:".$doc_type_id;
				}
				//echo "doctype:".$NoticeAlias = $resDocName[0]["doc_type"];
				//echo "doc_type_id:".$doc_type_id;
				//die();
				//$str = "Lease//".$start;
				$bAddDate = false;
				if($bTenantModule)
				{
					if($UnitNo != "")
					{
						if($bAddDate)
						{
							$folderName = $UnitNo . "//". $NoticeAlias . "//".$PostDate; 
						}
						else
						{
							$folderName = $UnitNo . "//". $NoticeAlias;	
						}
					}
					else
					{
						if($bAddDate)
						{
							$folderName = $NoticeAlias . "//".$PostDate; 
						}
						else
						{
							$folderName = $NoticeAlias;
						}
					}
				}
				else
				{
					if($bAddDate)
					{
						$folderName = $NoticeAlias . "//".$PostDate; 
					}
					else
					{
						$folderName = $NoticeAlias;	
					}
				}
				if($this->m_bShowTrace)
				{
					echo "path:".$folderName;
				}
				//$sql = "select GDrive_W2S_ID from `society`";
				//$res = $this->m_dbConn->select($sql);
				//print_r($res);
				//$rootid = $res[0]['GDrive_W2S_ID'];
				
				//echo "GD:".$sGDrive_W2S_ID;
				$sStatus = "";
				$sMode = "";
				$sFile = "";
				//$sUploadingFileName = $fileName;
				
				if($sGDrive_W2S_ID != "")
				{
					$ObjGDrive = new GDrive($this->m_dbConn, "0", $sGDrive_W2S_ID, 0);
					
					//echo "filename:".$noticeFileName ." mime:". $mimeType ." tmpname:". $arFILES['userfile'.$FileIndexCount]['tmp_name'] ." folderName:". $folderName;
					//$mimeType = 'application/vnd.google-apps.file';
					$UploadedFiles = $ObjGDrive->UploadFiles($fileName , $fileName, $mimeType, $arFILES['userfile'.$FileIndexCount]['tmp_name'], $folderName, $folderName, "", "", $sGDrive_W2S_ID, "0");
					
					$sStatus = "1";
					$sMode = "2";
					$sFile = $UploadedFiles["response"]["id"];
					//echo "uploaded:".$sFile;

				}
				else
				{
					if(move_uploaded_file($arFILES['userfile'.$FileIndexCount]['tmp_name'], $uploadfile))
					{
						//$_POST['note'] = $fileName;
						
						$sStatus = "1";
						$sMode = "1";
						$sFile = $fileName;
						$arResponse["note"] = $fileName;
					}
					else
					{
						echo "Error uploading file - check destination is writeable.";
						$sStatus = "0";
						$sMode = "0";
						$sFile = "";
						$arResponse["note"] = "";
						//return "";
					}
				}

				if($bTenantModule)
				{
					$arResponse["doc_name"] = $fileName;
				}
				$arResponse["status"] = $sStatus;
				$arResponse["mode"] = $sMode;
				$arResponse["response"] = $sFile;
				$arResponse["FileName"] = $fileName;
				//die();
				if($this->m_bShowTrace)
				{
					echo "<br>uploadfile:";
					echo "<pre>";
					print_r($UploadedFiles);

					echo "</pre>";
				}
				//$_POST["note"] = $noticeFileName;
				/*if($UploadedFiles["status"] == 1)
				{
					$docGDriveID = $UploadedFiles["response"]["id"];
					echo "file uploaded successfully to gdrive.";
				}
				else
				{
					//$docGDriveID = $UploadedFiles["status"][0][""];
					$docGDriveID = "Error while uploading document.";
				}*/
			}
			catch(Exception $exp)
			 {
				echo "Error occured in uploading document. Details are:".$exp->getMessage();
				die();
			 }
			return $arResponse;
		}
		function IsAPITokenValid($sClientID, $sUnitID, $sToken)
		{
			if($sUnitID <> 0)
			{
			
			 $sqlSelect = "select status,id from api_tokens where `ClientID`='".$sClientID."' and `UniqueID`='".$sUnitID."' and Token='".$sToken."' order by id desc";
			}
			else
			{
			$sqlSelect = "select status,id,UniqueID from api_tokens where `ClientID`='".$sClientID."' and Token='".$sToken."' order by id desc";
			}
			
			$bTrace = 0;
			if($bTrace)
			{
				echo "sql:".$sqlSelect;
			}
			if($bTrace)
			{
				echo "api status:".$sqlSelect;		
			}
			$res = $this->m_dbConn->select($sqlSelect);
			//print_r($res);
			$status = "0";
			$token_id = "";
			$arResponse = array();
			if($bTrace)
			{
				print_r($res);
			}
			if(sizeof($res) > 0)
			{
				$status = $res[0]["status"];
				$token_id = $res[0]["id"];
				$UnitID = $res[0]["UniqueID"];
			}

			$arResponse["status"] = $status;
			$arResponse["id"] = $token_id;
			$arResponse["unit_id"] = $UnitID;
			if($bTrace)
			{
				echo "api status:".$status;		
			}
			return $arResponse;
			//$sStatus = $res["status"];
			
			//echo "api status:".$sStatus;		
		}
		function UploadAttachmentAndroid($arFILES, $doc_type_id,$PostDate, $DestFolderName, $FileIndexCount = "", $bTenantModule = false, $UnitNo = "")
		{
			$docGDriveID = "";
			$arResponse =  array();
			try
			{
				//echo "start";
				//die();
				$errorfile_name = 'image_upload_errorlog_'.date("d.m.Y").'.html';
				//$this->errorLog = $this->errorfile_name;
				$errorfile = fopen($errorfile_name, "a");
				
				$fileTempName = $arFILES['file'.$FileIndexCount]['tmp_name'];  
				$fileSize = $arFILES['file'.$FileIndexCount]['size'];
				
				$errormsg = "get drive details";
				$msgFormat=$errormsg."\r\n";
				fwrite($errorfile,$msgFormat);
				
				$resSociety = $this->GetGDriveDetails();
				$sGDrive_W2S_ID = $resSociety["0"]["GDrive_W2S_ID"];
				if($sGDrive_W2S_ID != "")
				{
					$fileName = basename($arFILES['file'.$FileIndexCount]['name']);
				}
				else
				{
					$fileName = time().'_'.basename($arFILES['file'.$FileIndexCount]['name']);	
				}
				if($_SERVER['HTTP_HOST']=="localhost")
				{		
					$uploaddir = $_SERVER['DOCUMENT_ROOT']."/beta_aws_12/".$DestFolderName;			   
				}
				else
				{
					$uploaddir = $_SERVER['DOCUMENT_ROOT']."/".$DestFolderName;			   
				}
				$uploadfile = $uploaddir ."/". $fileName;	
				
				if($this->m_bShowTrace)
				{
					echo $uploadfile;	
				}
				
				//die();
				if($this->m_bShowTrace)
				{
					echo "uploading to gdrive:".$uploadfile . " doctype:".$doc_type_id;	
				}
				$mimeType = $arFILES['file'.$FileIndexCount]['type'];
				//$documentName = time() . "_" . $arFILES['userfile']['name'] ;
				//$noticeFileName = $documentName;

				//$sqlDocName = "select doc_type from `document_type` where ID='".$doc_type_id."'";
				$resDocName = $this->GetDocTypeByID($doc_type_id);
				$NoticeAlias = $resDocName[0]["doc_type"];
				$NoticeAlias = "FINE";
				if($this->m_bShowTrace)
				{
					echo "doctype:".$NoticeAlias;
					echo "doc_type_id:".$doc_type_id;
				}
				$errormsg = "type:".$NoticeAlias;
				$msgFormat=$errormsg."\r\n";
				fwrite($errorfile,$msgFormat);
				
				//echo "doctype:".$NoticeAlias = $resDocName[0]["doc_type"];
				//echo "doc_type_id:".$doc_type_id;
				//die();
				//$str = "Lease//".$start;
				$bAddDate = false;
				if($bTenantModule)
				{
					if($UnitNo != "")
					{
						if($bAddDate)
						{
							$folderName = $UnitNo . "//". $NoticeAlias . "//".$PostDate; 
						}
						else
						{
							$folderName = $UnitNo . "//". $NoticeAlias;	
						}
					}
					else
					{
						if($bAddDate)
						{
							$folderName = $NoticeAlias . "//".$PostDate; 
						}
						else
						{
							$folderName = $NoticeAlias;
						}
					}
				}
				else
				{
					if($bAddDate)
					{
						$folderName = $NoticeAlias . "//".$PostDate; 
					}
					else
					{
						$folderName = $NoticeAlias;	
					}
				}
				if($this->m_bShowTrace)
				{
					echo "path:".$folderName;
				}
				//$sql = "select GDrive_W2S_ID from `society`";
				//$res = $this->m_dbConn->select($sql);
				//print_r($res);
				//$rootid = $res[0]['GDrive_W2S_ID'];
				$errormsg = "gdrive ctor".$sGDrive_W2S_ID;
				$msgFormat=$errormsg."\r\n";
				fwrite($errorfile,$msgFormat);
				
				//echo "GD:".$sGDrive_W2S_ID;
				$sStatus = "";
				$sMode = "";
				$sFile = "";
				//$sUploadingFileName = $fileName;
				$sGDrive_W2S_ID = "";
				if($sGDrive_W2S_ID != "")
				{
					$ObjGDrive = new GDrive($this->m_dbConn, "0", $sGDrive_W2S_ID, 0);
					
					//echo "filename:".$noticeFileName ." mime:". $mimeType ." tmpname:". $arFILES['userfile'.$FileIndexCount]['tmp_name'] ." folderName:". $folderName;
					//$mimeType = 'application/vnd.google-apps.file';

					$errormsg = "ready to image upload through gdrive";
					$msgFormat=$errormsg."\r\n";
					fwrite($errorfile,$msgFormat);
				
					$UploadedFiles = $ObjGDrive->UploadFiles($fileName , $fileName, $mimeType, $arFILES['file'.$FileIndexCount]['tmp_name'], $folderName, $folderName, "", "", $sGDrive_W2S_ID, "0");
					
					$sStatus = "1";
					$sMode = "2";
					$sFile = $UploadedFiles["response"]["id"];
					//echo "uploaded:".$sFile;

				}
				else
				{

					$errormsg = "ready to image upload from non-gdrive";
					$msgFormat=$errormsg."\r\n";
					fwrite($errorfile,$msgFormat);

					if(move_uploaded_file($arFILES['file'.$FileIndexCount]['tmp_name'], $uploadfile))
					{
						//$_POST['note'] = $fileName;
						
						$sStatus = "1";
						$sMode = "1";
						$sFile = $fileName;
						$arResponse["note"] = $fileName;
					}
					else
					{
						echo "Error uploading file - check destination is writeable.";
						$sStatus = "0";
						$sMode = "0";
						$sFile = "";
						$arResponse["note"] = "";
						//return "";
					}
				}

				if($bTenantModule)
				{
					$arResponse["doc_name"] = $fileName;
				}
				$arResponse["status"] = $sStatus;
				$arResponse["mode"] = $sMode;
				$arResponse["response"] = $sFile;
				$arResponse["FileName"] = $fileName;
				//die();
				if($this->m_bShowTrace)
				{
					echo "<br>uploadfile:";
					echo "<pre>";
					print_r($UploadedFiles);

					echo "</pre>";
				}
				//$_POST["note"] = $noticeFileName;
				/*if($UploadedFiles["status"] == 1)
				{
					$docGDriveID = $UploadedFiles["response"]["id"];
					echo "file uploaded successfully to gdrive.";
				}
				else
				{
					//$docGDriveID = $UploadedFiles["status"][0][""];
					$docGDriveID = "Error while uploading document.";
				}*/
			}
			catch(Exception $exp)
			 {
				echo "Error occured in uploading document. Details are:".$exp->getMessage();
				die();
			 }
			return $arResponse;
		}
			function checkAccess()
			{
			if($_SESSION['role']=="Super Admin")
			{
				return 0;
			}
			else
			{
				return 1;
			}
			}
		
		public function getAppDefaultProperty($property_name)
		{
			$sql1 = "SELECT * FROM `appdefault_new` WHERE `Property` = '".$property_name."'";
			$sql1_res = $this->m_dbConn->select($sql1);
			
			return $sql1_res;
		}
		
		public function setAppDefaultProperty($property_name, $value)
		{
			$sql1 = "INSERT INTO `appdefault_new`(`Property`,`Value`,`LoginID`) VALUES('".$property_name."','".$value."','".$_SESSION['login_id']."')";
			$sql1_res = $this->m_dbConn->insert($sql1);
			
			return $sql1_res;
		}
		
		public function updateAppDefaultProperty($property_name, $value)
		{
			$timestamp = getCurrentTimeStamp();
			$sql1 = "UPDATE `appdefault_new` SET `Value` = '".$value."', `LoginID` = '".$_SESSION['login_id']."', `timestamp` = '".$timestamp['DateTime']."' WHERE `Property` = '".$property_name."'";
			$sql1_res = $this->m_dbConn->update($sql1);
			
			return $sql1_res;
		}
				public function ComboboxWithDefaultSelect($query,$id)
		{
		//$str.="<option value=''>All</option>";
		$str.="<option value='0'>Please Select</option>";
		$data = $this->m_dbConn->select($query);
		//print_r($data);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($id==$v)
						{
							$sel = 'selected';	
						}
						else
						{
							$sel = '';
						}
				
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
					}
					else
					{
						$str.=$v."</OPTION>";
					}
					$i++;
				}
			}	
		}
		return $str;
	}

	function getRoundToNextRs($amount)
	{
		$roundAmount = 0;
		//$roundAmount = round($amount * 2)/2; //(Eg. 1 to 1.24 = 1, 1.25 to 1.74 = 1.5, 1.75 to 1.99 = 2)
		$roundAmount = round($amount+0.49); //(Eg. 1 to 1.49 = 1, 1.50 to 1.99 = 2)
		return $roundAmount;
	}

	function getRoundValue2($amount)
	{
		$roundAmount = 0;
		//$roundAmount = round($amount * 2)/2; //(Eg. 1 to 1.24 = 1, 1.25 to 1.74 = 1.5, 1.75 to 1.99 = 2)
		$roundAmount = round($amount); //(Eg. 1 to 1.49 = 1, 1.50 to 1.99 = 2)
		return $roundAmount;
	}

	function getTruncatedValue2($val, $f="0")
	{
		if(($p = strpos($val, '.')) !== false) {
			$val = floatval(substr($val, 0, $p + 1 + $f));
		}
		//echo "<BR>val " . $val;
		return $val;
	}
	//To get Category Name of bank and cash account as per society 
	  public function getcategory_bankcash()
	  {
		  $querycategory = "select LOWER(category_name) as category_name from account_category where category_id in(".$_SESSION['default_cash_account'].",".$_SESSION['default_bank_account'].")";
		  $result =  $this->m_dbConn->select($querycategory);
		  //var_dump($result);
		  $bank_cash_array = array_column($result, 'category_name');
		  return $bank_cash_array;
	 }
	public function GetDateByOffset2($myDate, $Offset)
	{
		//echo '<br/>myDate : ' . $myDate;
		//echo '<br/>Offset : ' . $Offset;
		$datetime1 = new DateTime($myDate);
		$newDate = $datetime1->modify($Offset . ' day');
//		echo '<br/>Offetdate : ' . $newDate->format('y-m-d');

		return $newDate->format('Y-m-d');	
	}

	//Required for calendar control
	public function GetDateByOffset_dmy($myDate, $Offset)
	{
		$datetime1 = new DateTime($myDate);
		$newDate = $datetime1->modify($Offset . ' day');
		return $newDate->format('d-m-Y');	
	}

function getPaymentOption()
{
	$sqlQuery= "Select PaymentGateway, Paytm_Link from society where society_id ='".$_SESSION['society_id']."'";
	$data = $this->m_dbConn->select($sqlQuery);
	return $data;
}

	function safe_json_encode($value, $options = 0, $depth = 512, $utfErrorFlag = false) {
		$encoded = json_encode($value, $options, $depth);
		switch (json_last_error()) {
			case JSON_ERROR_NONE:
				return $encoded;
			case JSON_ERROR_DEPTH:
				return 'Maximum stack depth exceeded'; // or trigger_error() or throw new Exception()
			case JSON_ERROR_STATE_MISMATCH:
				return 'Underflow or the modes mismatch'; // or trigger_error() or throw new Exception()
			case JSON_ERROR_CTRL_CHAR:
				return 'Unexpected control character found';
			case JSON_ERROR_SYNTAX:
				return 'Syntax error, malformed JSON'; // or trigger_error() or throw new Exception()
			case JSON_ERROR_UTF8:
				$clean = $this->utf8ize($value);
				if ($utfErrorFlag) {
					return 'UTF8 encoding error'; // or trigger_error() or throw new Exception()
				}
				return $this->safe_json_encode($clean, $options, $depth, true);
			default:
				return 'Unknown error'; // or trigger_error() or throw new Exception()

		}
	}

	function utf8ize($mixed) {
		if (is_array($mixed)) {
			foreach ($mixed as $key => $value) {
				$mixed[$key] = $this->utf8ize($value);
			}
		} else if (is_string ($mixed)) {
			return utf8_encode($mixed);
		}
		return $mixed;
	}

	 public function CheckActivationCodeExist($unit_id,$society_id,$userEmail)
		{
			$sql = "SELECT code FROM `mapping` where unit_id = '".$unit_id."' and society_id = '".$society_id."' and status = 1 and code LIKE '$userEmail%'  ORDER BY `id` DESC  ";
			$data = $this->m_dbConnRoot->select($sql);
			$code= substr(($data[0]['code']),-4);
			return $code;
		}
	public function getRecoCheckDaySpan()
	{
			$sqlFetch="SELECT `Reco_Check_Day_Span` FROM `society` where society_id = '".$_SESSION['society_id']."'";
			$res = $this->m_dbConn->select($sqlFetch);

			
			return $res[0]['Reco_Check_Day_Span']; 
	}

	public function getSocietyAllLoginDetails($showRole = false, $excludeMembers = false, $getAllLogin = false)
	{
		if($getAllLogin){

			$sql = "SELECT l.login_id, l.name, m.role from login AS l JOIN mapping as m ON l.login_id = m.login_id ";
		}
		else{

			$sql2 = "SELECT l.login_id, l.name, m.role from login AS l JOIN mapping as m ON l.login_id = m.login_id WHERE m.society_id = '".$_SESSION['society_id']."'";
		}
		
		
		if($excludeMembers){

			$sql .= " WHERE role != '".ROLE_MEMBER."' group by l.login_id";
			$sql2 .= " AND role != '".ROLE_MEMBER."'";
		}

		if($getAllLogin){

			$data = $this->m_dbConnRoot->select($sql);
		}
		else{

			$data = $this->m_dbConnRoot->select($sql2);
		}

		$result = array();
		foreach ($data as $memberData) {
			
			$result[$memberData['login_id']] = ($showRole) ? $memberData['name'].' - '.$memberData['role'] : $memberData['name'];
			
		}
		return $result;
	}

	public function getSocietyCreatedOpeningDate(){

		$qry = "SELECT BeginingDate FROM period as p JOIN society as s ON p.YearID = s.society_creation_yearid order by p.ID limit 1";
		$result = $this->m_dbConn->select($qry);
		return $result[0]['BeginingDate'];

	}

	public function getDepositName($depositID){

		if($depositID == DEPOSIT_NEFT){

			$depositGroupName = 'NEFT';
		}
		else if($depositID == DEPOSIT_CASH){

			$depositGroupName = 'Cash';
		}
		else if($depositID == DEPOSIT_ONLINE){
			
			$depositGroupName = 'Online';
		}
		else{

			$qry = "SELECT `desc` FROM `depositgroup` where id = '$depositID'";
			$result = $this->m_dbConn->select($qry);
			$depositGroupName = $result[0]['desc'];
		}
		return $depositGroupName;
	}

	public function getLeftName($LeafID){

		$qry = "SELECT LeafName FROM `chequeleafbook` where id = '$LeafID'";
		$result = $this->m_dbConn->select($qry);
		return $result[0]['LeafName'];
	}

	public function getVoucherTypeAndVoucherID($refTable, $refNo){

		$qry = "SELECT id, VoucherTypeID, VoucherNo FROM voucher where RefTableID = '$refTable' AND RefNo = '$refNo'";
		$result = $this->m_dbConn->select($qry);
		return $result;

	}

	public function getBillDetails($billRefID){

		if(!empty($billRefID)){

			$qry = "SELECT UnitID, PeriodID, BillType FROM billdetails WHERE ID = '$billRefID'";
			return $result = $this->m_dbConn->select($qry);
		}
		
	}
	
		// get the Latest Bill Date

	public function getLastBillDate(){

		$query = "SELECT BeginingDate FROM `period` WHERE ID IN(SELECT MAX(PeriodID) FROM billregister)";
		$result =  $this->m_dbConn->select($query);
		return $result[0]['BeginingDate'];
	}

	public function getMaxDate(){

		$query = "SELECT MAx(EndingDate) as maxDate FROM `year` where `status` = 'Y'";
		$result =  $this->m_dbConn->select($query);
		return $result[0]['maxDate'];
	}
	public function GetBillTemplate($periodID){

		$query = "SELECT gen_bill_template FROM `billregister` where `PeriodID` = '".$periodID."'";
		$result =  $this->m_dbConn->select($query);
		return $result[0]['gen_bill_template'];
	}

	public function getendYearBalance($LedgerID)
		{
			//echo "Ledger ID: " .$LedgerID;
			$from_date = $_SESSION['default_year_start_date'];
			$to_date = $_SESSION['default_year_end_date'];
			$endOfYearBalance = array("LedgerName" => "","Credit" => 0 ,"Debit" => 0 ,"Total" => 0,"OpeningDate" => $date);
			
			$arParentDetails = $this->getParentOfLedger($LedgerID);
			if(!(empty($arParentDetails)))
			{
				$LedgerGroupID = $arParentDetails['group'];
				$LedgerCategoryID = $arParentDetails['category'];
				
				if($LedgerGroupID == LIABILITY)
				{
					$sqlLiability = "SELECT SUM(Credit) as Credit,
									SUM(Debit) as Debit,(SUM(Credit) - SUM(Debit)) as Total
									FROM `liabilityregister` where LedgerID  = '".$LedgerID."' and  Date between '".$from_date."' and '".$to_date."' ";
					$result = $this->m_dbConn->select($sqlLiability);
					$endOfYearBalance['Credit'] = $result[0]['Credit'];
					$endOfYearBalance['Debit'] = $result[0]['Debit'];
					$endOfYearBalance['Total'] = $result[0]['Total'];
					
					$endOfYearBalance['Total'] = abs($endOfYearBalance['Total']);	
				}
				else if($LedgerGroupID == ASSET && ($LedgerCategoryID == BANK_ACCOUNT || $LedgerCategoryID == CASH_ACCOUNT))
				{
					 $sqlBank = "SELECT SUM(ReceivedAmount) as Credit,
								SUM(PaidAmount) as Debit,(SUM(ReceivedAmount) - SUM(PaidAmount)) as Total 
								FROM `bankregister` where LedgerID = '".$LedgerID."' and  Date between '".$from_date."' and '".$to_date."' ";								
					$result = $this->m_dbConn->select($sqlBank);
					$endOfYearBalance['Credit'] = $result[0]['Credit'];
					$endOfYearBalance['Debit'] = $result[0]['Debit'];
					$endOfYearBalance['Total'] = $result[0]['Total'];
					
					$endOfYearBalance['Total'] = abs($endOfYearBalance['Total']);	
				}
				else if($LedgerGroupID == ASSET)
				{
					$sqlAsset = "SELECT SUM(Credit) as Credit,
								SUM(Debit) as Debit,(SUM(Credit) - SUM(Debit)) as Total 
								FROM `assetregister` where LedgerID  = '".$LedgerID."' and  Date between '".$from_date."' and '".$to_date."' ";
					$result = $this->m_dbConn->select($sqlAsset);
					$endOfYearBalance['Credit'] = $result[0]['Credit'];
					$endOfYearBalance['Debit'] = $result[0]['Debit'];
					$endOfYearBalance['Total'] = $result[0]['Total'];
					
					$endOfYearBalance['Total'] = abs($endOfYearBalance['Total']);				
				}
				else if($LedgerGroupID == INCOME)
				{
					$sqlIncome = "SELECT SUM(Credit)-SUM(Debit) as Total 
								FROM `incomeregister` where LedgerID  = '".$LedgerID."' and  Date between '".$from_date."' and '".$to_date."' ";
					$result = $this->m_dbConn->select($sqlIncome);
					$endOfYearBalance['Credit'] = $result[0]['Credit'];
					$endOfYearBalance['Debit'] = $result[0]['Debit'];
					$endOfYearBalance['Total'] = $result[0]['Total'];

					$endOfYearBalance['Total'] = abs($endOfYearBalance['Total']);			
				}
				else if($LedgerGroupID == EXPENSE)
				{
					$sqlExpense = "SELECT SUM(Credit) as Credit,
								SUM(Debit) as Debit,(SUM(Credit) - SUM(Debit)) as Total 
								FROM `expenseregister` where LedgerID  = '".$LedgerID."' and  Date between '".$from_date."' and '".$to_date."' ";
					$result = $this->m_dbConn->select($sqlExpense);
					$endOfYearBalance['Credit'] = $result[0]['Credit'];
					$endOfYearBalance['Debit'] = $result[0]['Debit'];
					$endOfYearBalance['Total'] = $result[0]['Total'];
					
					$endOfYearBalance['Total'] = abs($endOfYearBalance['Total']);
				}
				
			}
			if($result <> "")
			{
				$sql = "select l.`ledger_name`, acc.category_name   from `ledger`  as l JOIN account_category as acc ON l.categoryid = acc.category_id where l.`id` = '".$LedgerID."'";
				$res = $this->m_dbConn->select($sql);
				if($res <> "")
				{
					$endOfYearBalance['LedgerName'] = $res[0]['ledger_name'];	
					$endOfYearBalance['Ledger_Category'] = $res[0]['category_name'];		
				}	
			}
				//print_r($closingBalance);		
			return $endOfYearBalance;
			
		}
}

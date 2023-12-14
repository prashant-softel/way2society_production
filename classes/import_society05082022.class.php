






<?php

include_once("dbconst.class.php");
include_once("changelog.class.php");
//include_once("society_import.class.php");
//include_once("wing_import.class.php");
//include_once("ledger_import.class.php");
//include_once("unit_import.class.php");
//include_once("member_import.class.php");
//include_once("tarrif_import.class.php");
include_once("billdetails_import.class.php");
include_once("include/display_table.class.php");
include_once("utility.class.php");
include_once("bill_period.class.php");
include_once("register.class.php");
include_once("include/fetch_data.php");


class import 
{
	public $ErrorLogFile;
	public $m_dbConnRoot;
	public $m_dbConn;
	private $obj_default;
	public $obj_utility;
	public $actionPage = "../import_file.php";
	public $obj_billperiod;
	
	function __construct($m_dbConnRoot, $dbConn)
	{
		$this->m_dbConnRoot = $m_dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->obj_default = new defaults($this->m_dbConn);
		$this->obj_utility= new utility($this->m_dbConn);
		$this->obj_billperiod = new bill_period($this->m_dbConn);
		$this->obj_register = new regiser($this->m_dbConn);
		$this->obj_fetch = new FetchData($this->m_dbConn);

		$a = $this->obj_fetch->GetSocietyDetails($_SESSION['society_id']);
	}
	
	public function ImportData()
	{
		//ErrorFile
		$Foldername = $this->obj_fetch->objSocietyDetails->sSocietyCode;

		if (!file_exists('../logs/import_log/'.$Foldername)) 
		{
			mkdir('../logs/import_log/'.$Foldername, 0777, true);
		}

		$errofile_name='../logs/import_log/'.$Foldername.'/import_errorlog_'.date("d.m.Y").'_'.rand().'.html';
		$this->ErrorLogFile=$errofile_name;
		$errorfile=fopen($errofile_name, "a");
			
		if(isset($_POST["submit"]))
		{
			if(isset($_SESSION['Cycle']) && isset($_SESSION['eperiod']) && isset($_SESSION['Year']) && isset($_SESSION['society_name']))
			{
					$society_name=$_SESSION['society_name'];
					$PeriodName = '';		
					$IsPeriodsAdded = 'No';	
					$PeriodStatus = '';
		
					$FetchPeriod = $this->m_dbConn->select("select count(YearID) as count from `period` where `Billing_cycle`='".$_SESSION['Cycle']."' and `YearID`= '".$_SESSION['Year']."'");
											
					if($FetchPeriod[0]['count'] == 0)
					{ 
					
						$months = getMonths($_SESSION['Cycle']);
						
						$PrevYear =  $_SESSION['Year'] - 1;
						$sqlFetchData = $this->m_dbConn->select("SELECT * FROM `year`  where  `YearID`= '".$PrevYear."'");
						
						$begin_date = $this->obj_billperiod->getBeginDate(end($months),$sqlFetchData[0]['YearDescription']);
						$end_date = $this->obj_billperiod->getEndDate(end($months),$sqlFetchData[0]['YearDescription']); 
												
						$insert_query="insert into period(`Billing_cycle`,`Type`,`YearID`,`PrevPeriodID`,`IsYearEnd`,`BeginingDate`,`EndingDate` )
												 values(".$_SESSION['Cycle'].",'".end($months)."',".$PrevYear.",'0', '1','".$begin_date ."','".$end_date."')";
						$prevPeriod = $this->m_dbConn->insert($insert_query);	
						
						$this->obj_billperiod->setPeriod($months ,$_SESSION['Cycle'],$_SESSION['Year']);
						$IsPeriodsAdded = 'Yes';
						
						
					}
					else
					{
						$IsPeriodsAdded = 'No';	
						$PeriodStatus =  'Unable to generate  periods for  selected year because period already exists';
					}		
					
					if($IsPeriodsAdded == 'No')
					{
						$this->actionPage="../import_society.php";
						return $PeriodStatus;		
					}
					else
					{
						$sqlFetchData = $this->m_dbConn->select("select *  from `period` where `Billing_cycle`='".$_SESSION['Cycle']."' and `YearID`= '".$_SESSION['Year']."'");		
						$_SESSION['Period'] = $sqlFetchData[0]['ID'];
						if($_SESSION['Period']  == "")
						{
							$this->actionPage="../import_society.php";
							return $PeriodStatus;		
						}
					}
					
					$billingcycle="select `Description` from `billing_cycle_master` where `ID`='".$_SESSION['Cycle']."'";
					$resCycle=$this->m_dbConn->select($billingcycle);
					$getyear="SELECT `YearDescription` FROM `year` where `YearID`='".$_SESSION['Year']."' ";
					$resgetyear=$this->m_dbConn->select($getyear);
					$getperiod="SELECT `Type` FROM `period` where `ID`='".$_SESSION['Period']."'";
					$resgetperiod=$this->m_dbConn->select($getperiod);
					$int_method="";
					$rebate_method="";
					if($_SESSION['int_method']==1)
					{
						$int_method="INTEREST_METHOD_DELAY_DUE";
					}
					elseif($_SESSION['int_method']==2)
					{
						$int_method="INTEREST_METHOD_FULL_MONTH";
					}
					else
					{
						$int_method="INTEREST_METHOD_FULL_CYCLE";	
					}
					
					
					if($_SESSION['rebate_method']==1)
					{
						$rebate_method="REBATE_METHOD_NONE";
					}
					elseif($_SESSION['rebate_method']==2)
					{
						$rebate_method="REBATE_METHOD_FLAT";
					}
					else
					{
						$rebate_method="REBATE_METHOD_WAIVE";
					}
					
						
						$search_exists="select count(*) as cnt from `society` where society_name='".$society_name."' ";
						$res00=$this->m_dbConnRoot->select($search_exists);
						if($res00[0]['cnt'] > 0)
						{ 
							$errormsg="Society &lt;".$society_name."&gt;  already exist";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							return "Exist";
							break;
						}
						else if($res00[0]['cnt']==0 && $society_name <> "")
						{
							
							$insert_society_root = "INSERT INTO `society`(`society_name`, `dbname`,`client_id`) VALUES ('$society_name','" . $_SESSION['dbname'] . "','".$_SESSION['client_id']."')";
							
							
							
							$_SESSION[$result_society_id] = $this->m_dbConnRoot->insert($insert_society_root);
							
							$isImportSuccess = true;
							
							$timestamp = DateTime::createFromFormat('U.u', microtime(true));
							$uniqueTime = $timestamp->format("m-d-Y H:i:s.u");
		
							$update_dbname = "UPDATE dbname SET society_id = '". $_SESSION[$result_society_id ].  "' ,locked = '" . $uniqueTime . "' WHERE dbname = '" . $_SESSION['dbname'] . "'";
							$result_dbname = $this->m_dbConnRoot->update($update_dbname);
							
							$insert_mapping = "INSERT INTO `mapping`(`login_id`, `society_id`, `desc`, `role`, `profile`, `created_by`, `status`, `view`) VALUES ('" . $_SESSION['login_id'] . "', '" . $_SESSION[$result_society_id]. "', '" . ROLE_SUPER_ADMIN . "', '" . ROLE_SUPER_ADMIN . "', '" . PROFILE_SUPER_ADMIN_ID . "', '" . $_SESSION['login_id'] . "', 2, 'ADMIN')";
							
							$result_mapping = $this->m_dbConnRoot->insert($insert_mapping);
							
							$sqlUpdate = "UPDATE `login` SET `current_mapping`='" . $result_mapping . "' WHERE login_id = '" . $_SESSION['login_id'] . "'";
							$resultUpdate = $this->m_dbConnRoot->update($sqlUpdate);						
							
							
							$_SESSION['current_mapping'] = $result_mapping;
							
							$_SESSION[$society_code]=$_SESSION[$result_society_id];
							$update_society_code="UPDATE `society` SET `society_code` = '".$_SESSION[$society_code]."' WHERE society_id='".$_SESSION[$result_society_id]."'";
							$result_society_code=$this->m_dbConnRoot->update($update_society_code);
							
							if($_SESSION['client_id'] > 0)
							{
								$sqlSelectSadmin = "select login_id from login where client_id = '" . $_SESSION['client_id'] . "' and authority = 'self'";
								$resultSelectSadmin = $this->m_dbConnRoot->select($sqlSelectSadmin);
								
								for($sadminCnt = 0 ; $sadminCnt < sizeof($resultSelectSadmin) ; $sadminCnt++)
								{
									if($resultSelectSadmin[$sadminCnt]['login_id'] <> $_SESSION['login_id'])
									{
										$insert_mapping_sadmin = "INSERT INTO `mapping`(`login_id`, `society_id`, `desc`, `role`, `profile`, `created_by`, `status`, `view`) VALUES ('" . $resultSelectSadmin[$sadminCnt]['login_id'] . "', '" . $_SESSION[$result_society_id] . "', '" . ROLE_SUPER_ADMIN . "', '" . ROLE_SUPER_ADMIN . "', '" . PROFILE_SUPER_ADMIN_ID . "', '" . $_SESSION['login_id'] . "', 2, 'ADMIN')";
							
										$result_mapping_sadmin = $this->m_dbConnRoot->insert($insert_mapping_sadmin);
									}
								}
							}
								
							$insert_mapping = "INSERT INTO `mapping`(`society_id`, `desc`, `code`, `role`, `profile`, `created_by`, `view`) VALUES ('" . $_SESSION[$result_society_id] . "', '" . ROLE_ADMIN . "', '" . getRandomUniqueCode() . "', '" . ROLE_ADMIN. "', '" . PROFILE_ADMIN_ID . "', '" . $_SESSION['login_id'] . "', 'ADMIN')";
							
							$result_mapping = $this->m_dbConnRoot->insert($insert_mapping);
							
							$prevPeriod = $this->getPrevPeriod($_SESSION['Period']);
							
							$insert_society="insert into society(society_id, society_code,society_name,society_add,bill_cycle,int_rate,int_method,rebate_method,rebate,chq_bounce_charge,bank_penalty_amt,bill_method,M_PeriodID,society_creation_yearid) values('" . $_SESSION[$result_society_id] . "', '".$_SESSION[$society_code]."', '$society_name', '$society_add', '".$_SESSION['Cycle']."','".$_SESSION['int_rate']."','".$_SESSION['int_method']."','".$_SESSION['rebate_method']."','".$_SESSION['rebate']."','".$_SESSION['chq_bounce_charge']."','".$_SESSION['chq_bounce_charge']."','".BILL_FORMAT_WITH_RECEIPT."','". $prevPeriod."','".$_SESSION['Year']."')";
							$data=$this->m_dbConn->insert($insert_society);
	
							
							$_SESSION['society_id']	= $data;
							
							$sqlDefault = "INSERT INTO `appdefault`(`APP_DEFAULT_SOCIETY`, `changed_by`) VALUES ('" . $data . "', '" . $_SESSION['login_id'] . "')";
							$resultDefault = $this->m_dbConn->insert($sqlDefault);
							 
							$sqlDefault = "INSERT INTO `counter`(`society_id`) VALUES ('" . $data . "')";
							$resultDefault = $this->m_dbConn->insert($sqlDefault);
							
							$importHistory="INSERT INTO `import_history` (`society_id`,`society_flag`) VALUES (".$_SESSION[$result_society_id] .",'1')" ;
							$resultHistory=$this->m_dbConn->insert($importHistory);
							
						}//else if
					
					
					$errormsg1='<html><table border=1px solid black><tr>';
					$errormsg1.='<td colspan="2">Import Society Data Form Fields:'.'</td></tr>';
					$errormsg1 .='<tr><td>E Society Name</td><td>'.$_SESSION['society_name'].'</td></tr>';
					$errormsg1.='<tr><td>Billing Cycle</td><td>'.$resCycle[0]['Description'].'</td></tr>';
					$errormsg1 .='<tr><td>E Society Period</td><td>'.$_SESSION['eperiod'].'</td></tr>';
					$errormsg1 .='<tr><td>Data Import Into Year</td><td>'.$resgetyear[0]['YearDescription'].'</td></tr>';
					$errormsg1 .='<tr><td>Data Import Into Period</td><td>'.$resgetperiod[0]['Type'].'</td></tr>';
					$errormsg1 .='<tr><td>Bill Interest Rate</td><td>'.$_SESSION['int_rate'].'</td></tr>';
					$errormsg1 .='<tr><td>Bill Interest Method</td><td>'.$int_method.'</td></tr>';
					$errormsg1 .='<tr><td>Bill Rebate Method</td><td>'.$rebate_method.'</td></tr>';
					$errormsg1 .='<tr><td>Bill Rebate Amount</td><td>'.$_SESSION['rebate'].'</td></tr>';
					$errormsg1 .='<tr><td>Cheque Bounce Charges</td><td>'.$_SESSION['chq_bounce_charge'].'</td></tr>';
					$errormsg1 .='<tr><td>Periods Added For  '.$resgetyear[0]['YearDescription'].' </td><td>'.$IsPeriodsAdded.'</td></tr>';
					$errormsg1.='</table><br><br></html>';
					$errormsg=$errormsg1;
					$this->obj_utility->logGenerator($errorfile,'',$errormsg);
					
			
			 
		
		
			}//ifisset
	
		}//ifimport
	}//function
	function getDateTime()
	{
		$dateTime = new DateTime();
		$dateTimeNow = $dateTime->format('Y-m-d H:i:s');
		return $dateTimeNow;
	}
	
	function getPrevPeriod($curPeriod)
	{
		$sql = "Select PrevPeriodID from period where ID = '" . $curPeriod . "'";
		$result = $this->m_dbConn->select($sql);
		
		return $result[0]['PrevPeriodID'];
	}

	public function UploadData($fileName,$checkbox_indexes,$fileData,$validate)
	{  
		//ErrorLogFile
		
		if($validate!='')
		{		
			$Foldername = $this->obj_fetch->objSocietyDetails->sSocietyCode;

			if (!file_exists('../logs/import_log/'.$Foldername)) 
			{
				mkdir('../logs/import_log/'.$Foldername, 0777, true);
			}
			
			$validatefile_name='../logs/import_log/'.$Foldername.'/validate_errorlog_'.date("d.m.Y").'_'.rand().'.html';
			$this->ErrorLogFile=$validatefile_name;
			$validatefile=fopen($validatefile_name, "a");
			$societyData="<h3>Society Name : ".$_SESSION['society_name']."</h3>";
			$this->obj_utility->logGenerator($validatefile,'',$societyData,'I');
			$validatemsgfile="[Validating Society File Data]";
			$this->obj_utility->logGenerator($validatefile,'start',$validatemsgfile);
		}
		else
		{
			$errorfile=fopen($this->ErrorLogFile, "a");
			$errormsgfile="[Importing File Data]";
			$this->obj_utility->logGenerator($errorfile,'start',$errormsgfile);
		}
				
		$society_code=$_SESSION[$society_code];
		$society_name=$_SESSION['society_name'];
		
		$isImportSuccess = false;
		//Import History
		$sql00="select wing_flag,unit_flag,member_flag from `import_history` where society_id='".$_SESSION['society_id']."'";
		$res01=$this->m_dbConn->select($sql00);
		
		if($res01[0]['wing_flag']==0 && $res01[0]['unit_flag']==0 && $res01[0]['member_flag']==0 || $validate!='')
		{
		
			foreach  ($fileData as $row)
			{
				//var_dump($row);
				
				if($row[0] || $row[1] || $row[2]<> '')
				{
						$rowCount++;
						if($rowCount == 1)//Header
						{
							for($i=0;$i<sizeof($row);$i++)
							{
							$row[$i]=trim($row[$i],'');
							}

							$WCodeIndex = array_search (WCode,$row);
							$WNameIndex=array_search(WName,$row);
							$FCodeIndex=array_search(FCode,$row);
							$FloorNoIndex=array_search(FloorNo,$row);
							$UnitTypeIndex=array_search(UnitType,$row);
							$FlatAreaIndex=array_search(FlatArea,$row);
							$OwnerIndex=array_search(Owner,$row);
							$ExtnLedgerNameIndex=array_search(ExtnLedgerName,$row);
							$EMailIndex=array_search(EMail,$row);
							$MobileNoIndex=array_search(MobileNo,$row);
							$OccupationIndex=array_search(Occupation,$row);
							$DateOfBirthIndex=array_search(DateOfBirth,$row);
							$AnnivarsaryDateIndex=array_search(AnnivarsaryDate,$row);
							$GenderIndex=array_search(Gender,$row);
							$BloodGroupIndex=array_search(BloodGroup,$row);
							$OwnerAddressIndex=array_search(OwnerAddress,$row);
							$BikeParkingNoIndex=array_search(BikeParkingNo,$row);
							$CarParkingNoIndex=array_search(CarParkingNo,$row);
							$GSTINNOIndex=array_search(GSTINNO,$row);
							$ResPhoneIndex=array_search(ResPhone,$row);
							$OffPhoneIndex=array_search(OffPhone,$row);
							$EMail1Index=array_search(Email1,$row);
							$EmergencyPersonNameIndex=array_search(EmergencyPersonName,$row);
							$EmergencyMobileNoIndex=array_search(EmergencyMobileNo,$row);
							$EmergencyTelephoneNoIndex=array_search(EmergencyTelephoneNo,$row);
							$FlatConfigIndex=array_search(FlatConfiguration,$row);
							$AssociateMemberNameIndex=array_search(AssociateMemberName,$row,true);
							$VirtualACIndex=array_search(VirtualAC,$row,true);

							if($WCodeIndex === false || $WNameIndex === false || $FCodeIndex === false || $OwnerIndex === false)
							{
								$errormsg = "One or Many mandatory headers (WCode, WName, FCode, Owner) are missing";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								return $errormsg;
							}
						}
					 	else
					   	{   
							if($rowCount==2)
							{
								continue;	
							}
						
							$Failed;
							$wing_code='';
							$wing_name='';
							$unit_no='';
							$unit_type='';
							$floor_no ='';
							$area='';
							$owner_name='';
							$extn_ledger_name='';
							$email='';
							$Mobile='';
							$mob='';
							$desg='';
							$dob='';
							$wed_any='';
							$gender='';
							$blood_group='';
							$parking_no='';
							$bike_parking_no='';
							$car_parking_no='';
							$gstin_no='';
							$resd_no='';	
							$off_no='';
							$alt_email='';
							$eme_rel_name='';	
							$eme_contact_1='';	
							$eme_contact_2='';
							$flat_configuration='';
							$associatemembername='';
							$virtualac='';
							$ErrorMsg='';	
							$successData='';//all the fields
							$errorData='';//optional fields
							$successMsg='';//mandatory fields
							$errormsg='';//mandatory fields error
				
							for($i=0;$i<sizeof($row);$i++)
							{
								//Saving Values In Variables
								$wingExist=false;
								
								if(in_array($i,$checkbox_indexes))
								{
									if($WCodeIndex===$i)
									{
										$wingcode='';
										$wingcode=$row[$WCodeIndex];
									
										if($wingcode == '')
										{
											$ErrorMsg .="<br>Wing Code Not Provided in line :".$rowCount;
										}
										else
										{
											$wing_code=$wingcode;
										}
										$successData .="Wing Code : &lt;".$wingcode."&gt;";
										continue;
									}
								
									if($WNameIndex == $i)
									{
										$wingname='';
										$wingname=$row[$WNameIndex];
										
										if($wingname == '')
										{
											$ErrorMsg .="<br>Wing Name Not Provided in line :".$rowCount;
										}
										else
										{
											$wing_name=$wingname;
										}
										$successData .="Wing Name : &lt;".$wingname."&gt";
										continue;
									}
							
									if($FCodeIndex == $i)
									{
										$unitNo='';
										$unitNo=$row[$FCodeIndex];
										
										if($unitNo == '')
										{
											$ErrorMsg .="<br>FCode Not Provided of Wing : &lt;'".$wing_name."'&gt; in line :".$rowCount;
										}
										else
										{
											$unit_no=$unitNo;
										}
										$successData .="FCode : &lt;".$unitNo."&gt;";
										continue;
									}
							
									if($UnitTypeIndex == $i)
									{
										$unitType='';
										$unitType=$row[$UnitTypeIndex];
										
										if($unitType == '')
										{
											//$errorData .="<br>Unit Type Not Provided in line :".$rowCount;
										}
										else
										{
											$Valid=is_numeric($unitType);
											if($Valid)
											{
											
												if($unitType >= 1 && $unitType <= 7 )
												{
													$unit_type=$unitType;
												}
												else
												{
													$errorData .="<br>Unit Type Not in Range(1 to 7) : &lt;".$unitType."&gt;";
												}
											}
											else
											{
													$errorData .= "<br>Invalid Format of Unit Type : &lt;".$unitType."&gt;";										
											}
											
										}
										$successData .="Unit Type : &lt;".$unitType."&gt;";
										continue;
									}
							
									if($FloorNoIndex == $i)
									{
										$floorNo='';
										$floorNo=$row[$FloorNoIndex];
										
										if($floorNo == '')
										{
										//	$errorData="<br>Floor No Not Provided in line :".$rowCount;
										}
										else
										{
											$Valid=is_numeric($floorNo);
											if($Valid)
											{
												$floor_no=$floorNo;
											}
											else
											{
													$errorData .= "<br>Invalid Floor No Format : &lt;".$floorNo."&gt;";
											}
											
										}
										$successData .="Floor No : &lt;".$floorNo."&gt;";
										continue;
									}
									
							
									if($FlatAreaIndex == $i)
									{
										$flatArea='';
										$flatArea=$row[$FlatAreaIndex];
										
										if($flatArea == '')
										{
										//	$errorData .="<br>Flat Area Not Provided in line :".$rowCount;
										}
										else
										{
											$Valid=is_numeric($flatArea);
											if($Valid)
											{
												$area=$flatArea;
											}
											else
											{
													$area='';
													$errorData .= "<br>Invalid Flat Area Format &lt;".$flatArea."&gt;";
											}
											
										}
										$successData .="Flat Area : &lt;".$flatArea."&gt;";
										continue;
									}


									if($FlatConfigIndex == $i)
									{
										$flatconfig='';
										$flatconfig =$row[$FlatConfigIndex];
										
										if($flatconfig == '')
										{
											$ErrorMsg .="<br>flat configuration Not Provided in line :".$rowCount;
										}
										else
										{
								
												$flat_configuration = $flatconfig;
										}
											
										
										   $successData .="Flat configuration : &lt;".$flatconfig."&gt;";
										   continue;
									}

								
							
									if($OwnerIndex==$i)
									{
										if($this->m_dbConn->escapeString($row[$OwnerIndex]) == '')
										{
											
											$ErrorMsg .= "<br>Owner Name Not Provided For FCode : ".$unit_no." Of Wing Name : ".$wing_name;
										}
										else
										{
											$owner_name= $this->m_dbConn->escapeString($row[$OwnerIndex]);
										}
										$successData .="Owner Name : &lt;".$row[$OwnerIndex]."&gt;";
										continue;
									}
									
									if($ExtnLedgerNameIndex == $i)
									{
										$extnLedgerName='';
										$extnLedgerName=$row[$ExtnLedgerNameIndex];
										
										if($extnLedgerName == '')
										{
											//$errorData .="<br>ExtnLedgerName Not Provided";
										}
										else
										{
											$extn_ledger_name=$extnLedgerName;
										}
										$successData .="Extn LedgerName : &lt;".$row[$ExtnLedgerNameIndex]."&gt;";
										continue;
									}
									
									if($EMailIndex == $i)
									{
										$Email='';
										 $Email=trim($row[$EMailIndex]);
										
										if($Email == '')
										{
											$email='';
										//	$errorData .="<br>Email ID Not Provided For FCode :".$unitNo." in line :".$rowCount;
										}
										else
										{
											$Valid=$this->obj_utility->isValidEmailID($Email);
											if($Valid)
											{
												$email=$Email;
											}
											else
											{
												$errorData .= "<br>Invalid Email ID Format &lt;".$Email."&gt;";
											}
											
										}
										$successData .="Email ID :&lt;".$Email."&gt;";
										continue;
									}
									
									if($MobileNoIndex == $i)
									{
										$mobile='';
										$mobile=$row[$MobileNoIndex];
										
										if($mobile == '')
										{
											//$errorData .="<br>Mobile Number Not Provided For FCode :".$unitNo;
										}
										else
										{
											$Valid=is_numeric($mobile);
											if($Valid)
											{
												$mob=$mobile;
											}
											else
											{
												$errorData .= "<br>Invalid Mobile No Format &lt;".$mobile."&gt; Of FCode : ".$unitNo;
											}
											
										}
										$successData .="Mobile No : &lt;".$mobile."&gt;";	
										continue;
									}
							
									if($OccupationIndex == $i)
									{
										$occupation='';
										$occupation=$row[$OccupationIndex];
										
										if($occupation == '')
										{
											//$errorData .="<br>Occupation Not Provided For FCode : ".$unitNo;
										}
										else
										{
											$desg=$occupation;
										}
										$successData .="Occupation : &lt;".$occupation."&gt;";
										continue;
									}
							
									if($DateOfBirthIndex == $i)
									{
										$DOB='';
										$DOB=$row[$DateOfBirthIndex];
										
										if($DOB == '')
										{
											//$errorData .="<br>Date Of Birth Not Provided For FCode : ".$unitNo;
										}
										else
										{
											if($this->obj_utility->dateFormat($DOB))// yyyy-mm-dd
											{
												$dob=$DOB;
											}
											elseif($this->obj_utility->validateDate($DOB))//dd-mm-yyyy
											{
												$DOB=getDBFormatDate($DOB);
												if($DOB!='00-00-0000')
												{
													$dob=$DOB;
												}
											}
											
											else
											{
												 $errorData .="<br>Invalid Date Format &lt;".$DOB."&gt;Date Should be in either DD-MM-YYYY or YYYY-MM-DD Format ";
											}
										}
										$successData .="Date Of Birth : &lt;".$row[$DateOfBirthIndex]."&gt;";
										continue;
									} 
									
									if($AnnivarsaryDateIndex == $i)
									{
										$annDate='';
										$annDate=$row[$AnnivarsaryDateIndex];
										
										if($annDate == '')
										{
											//$errorData .="<br>Anniversary Date Not Provided For FCode : ".$unitNo;
										}
										else
										{
											if($this->obj_utility->dateFormat($annDate))// yyyy-mm-dd
											{
												$wed_any=$annDate;
											}
											elseif($this->obj_utility->validateDate($annDate))//dd-mm-yyyy
											{
												$annDate=getDBFormatDate($annDate);
												if($annDate!='00-00-0000')
												{
													$wed_any=$annDate;
												}
											}
											else
											{
												$errorData .="Invalid Anniversary Date Format &lt;".$annDate."&gt; Date Should be in either DD-MM-YYYY or YYYY-MM-DD Format";
											}
										}
										$successData .="Anniversary Date : &lt;".$annDate."&gt;";
										continue;
									}
									
									if($GenderIndex == $i)
									{
										$Gender='';
										$Gender=$row[$GenderIndex];
										
										if($Gender == '')
										{
											//$errorData .="<br>Gender Not Provided For FCode: ".$unitNo;
										}
										else
										{
											$gender=$Gender;
										}
										$successData .="Gender : &lt;".$Gender."&gt;";
										continue;
									}
									
									if($BloodGroupIndex == $i)
									{
										$BloodGroup='';
										$BloodGroup=$row[$BloodGroupIndex];
										
										if($BloodGroup == '')
										{
											//$errorData .="<br>Blood Group Not Provided For FCode : ".$unitNo;
										}
										else
										{
											$blood_group=$BloodGroup;
										}
										$successData .="Blood Group : &lt;".$BloodGroup."&gt;";
										continue;
									}
									
									if($OwnerAddressIndex == $i)
									{
										$ownerAdd='';
										$ownerAdd=$row[$OwnerAddressIndex];
										
										if($ownerAdd == '')
										{
											//$errorData .="<br>Owner Address Not Provided For FCode : ".$unitNo;
										}
										else
										{
											$parking_no=$ownerAdd;
										}
										$successData .="Owner Address : &lt;".$ownerAdd."&gt;";
										continue;
									}
									
									if($BikeParkingNoIndex == $i)
									{
										$bikeparking='';
										$bikeparking=$row[$BikeParkingNoIndex];
										
										if($bikeparking == '')
										{
											//$errorData .="<br>Bike Parking No Not Provided For FCode : ".$unitNo;
										}
										else
										{
											$bike_parking_no=$bikeparking;
										}
										$successData .="Bike Parking Number : &lt;".$bikeparking."&gt;";
										continue;
									}
									
									if($CarParkingNoIndex == $i)
									{
										$carparking='';
										$carparking=$row[$CarParkingNoIndex];
										
										if($carparking == '')
										{
											//$errorData .="<br>Car Parking Number Not Provided For FCode : ".$unitNo;
										}
										else
										{
											$car_parking_no=$carparking;
										}
										$successData .="Car Parking No : &lt;".$carparking."&gt;";
										continue;
									}
									
									if($GSTINNOIndex == $i)
									{
										$gstinno='';
										$gstinno=$row[$GSTINNOIndex];
										
										if($gstinno == '')
										{
											//$errorData .="<br>GSTINNO Not Provided For FCode : ".$unitNo;
										}
										else
										{
											$gstin_no=$gstinno;
										}
										$successData .="GSTINNO : &lt;".$gstinno."&gt;";
										continue;
									}
									
									if($ResPhoneIndex == $i)
									{
										$ResPhone='';
										$ResPhone=$row[$ResPhoneIndex];
										
										if($ResPhone == '')
										{
											//$errorData .="<br>Residential Number Not Provided for FCode : ".$unitNo;
										}
										else
										{
												$resd_no=$ResPhone;
											
										}
										$successData .="Residential Phone No : &lt;".$ResPhone."&gt;";
										continue;
									}
									
									if($OffPhoneIndex == $i)
									{
										$OffPhone='';
										$OffPhone=$row[$OffPhoneIndex];
										
										if($ResPhone == '')
										{
											//$errorData .="<br>Office Number Not Provided For FCode : ".$unitNo;
										}
										else
										{
											$Valid=is_numeric($OffPhone);
											if($Valid)
											{
												$off_no	= $OffPhone;
											}
											else
											{
													$errorData .= "<br>Invalid Office No Format &lt;".$OffPhone."&gt;Of Owner : ".$owner_name;
											}
										}
										$successData .="Office Phone : &lt;".$off_no."&gt;";
										continue;
									}
									
									if($EMail1Index == $i)
									{
										$AltEmail='';
										$AltEmail=$row[$EMail1Index];
										
										if($AltEmail == '')
										{
											//$errorData .="<br>Alternate Email ID Not Provided For FCode :".$unitNo;
										}
										else
										{
											$Valid=$this->obj_utility->isValidEmailID($AltEmail);
											if($Valid)
											{
													$alt_email=$AltEmail;
											}
											else
											{
													$errorData .= "<br>Invalid Alternate Email ID Format &lt;".$AltEmail."&gt;";
											}
											
										}
										$successData .="Alternate Email : &lt;".$AltEmail."&gt;";	
										continue;
									}
									
									
									if($EmergencyPersonNameIndex == $i)
									{
										$EmergencyPerson='';
										$EmergencyPerson=$row[$EmergencyPersonNameIndex];
										
										if($EmergencyPerson == '')
										{
											//$errorData .="<br>Emergency Person Name Not Provided For FCode : ".$unitNo;
										}
										else
										{
											$eme_rel_name=$EmergencyPerson;
										}
										$successData .="Emergency Person Name : &lt;".$EmergencyPerson."&gt;";
										continue;
									}
									
									if($EmergencyMobileNoIndex == $i)
									{
										$EmergencyPhone='';
										$EmergencyPhone=$row[$EmergencyMobileNoIndex];
										
										if($EmergencyPhone == '')
										{
											$eme_contact_1='';
											//$errorData .="<br>Emergency Mobile Not Provided For FCode : ".$unitNo;
										}
										else
										{
											$Valid=is_numeric($EmergencyPhone);
											if($Valid)
											{
												$eme_contact_1	= $EmergencyPhone;
											}
											else
											{
												$eme_contact_1='';
												$errorData .= "<br>Invalid Emergency Mobile Format &lt;".$EmergencyPhone."&gt; Of Owner : ".$owner_name;
											}
										}
										$successData .="Emergency Mobile No : &lt;".$EmergencyPhone."&gt;";
										continue;
									}	
									
									if($EmergencyTelephoneNoIndex == $i)
									{
										$EmergencyTele='';
										$EmergencyTele=$row[$EmergencyTelephoneNoIndex];
										
										if($EmergencyTele == '')
										{
											$eme_contact_2='';
											//$errorData .="<br>Emergency Telephone Number Not Provided For FCode : ".$unitNo;
										}
										else
										{
											$eme_contact_2	= $EmergencyTele;
											
										}
										$successData .="Emergency Telephone Number : &lt;".$EmergencyTele."&gt;";
										continue;
									}

									
									if($VirtualACIndex == $i)
									{
										$virtualacno='';
										$virtualacno =$row[$VirtualACIndex];
										
										if($virtualacno == '')
										{
											$ErrorMsg .="<br>flat configuration Not Provided in line :".$rowCount;
										}
										else
										{
								
											$virtualac = $virtualacno;
										}
											
										
										   $successData .="virtual account number : &lt;".$virtualacno."&gt;";
										   continue;
									}

									
									if($AssociateMemberNameIndex == $i)
									{
										$associatemember='';
										$associatemember =$row[$AssociateMemberNameIndex];
										
										if($associatemember == '')
										{
											$ErrorMsg .="<br>associate member Not Provided in line :".$rowCount;
										}
										else
										{
								
											$associatemembername = $associatemember;
										}
											
										
										   $successData .="associate member : &lt;".$associatemember."&gt;";
										   continue;
									}


								}//if in_array
							}//for row i=1
						
							if($validate=='')
							{
								if($wing_code != '' && $unit_no!= '' && $owner_name != '' && $wing_name !='')
							{	
								$get_society_id="select society_id from society where society_code='".$society_code."'";
								$data2=$this->m_dbConn->select($get_society_id);
								if($data2=='')
								{
									$errormsg=" Society Code &lt;".$society_code."&gt;  not found in society table for unit: &lt;".$unit_no."&gt; and wing: &lt;".$wing_code."&gt;";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"W");
								}
								$society_id=$data2[0]['society_id'];
								//If Wing Exists
								$search_exists="select count(*) as cnt from `wing` where wing='".$wing_name."' and 								society_id='".$_SESSION['society_id']."'";
								$res00=$this->m_dbConn->select($search_exists);
								if($res00[0]['cnt'] > 0)//Updating Import History for Wing
								{
									$wingExist = true;
									$update_import_history="update `import_history` set wing_flag=1 where society_id='".$_SESSION['society_id']."'";
									$res123=$this->m_dbConn->update($update_import_history);
								}
								
								else
								{	
									$insert_society="insert into wing(society_id,wing) values('".$_SESSION['society_id']."','$wing_name')";
									$data=$this->m_dbConn->insert($insert_society);
									$isImportSuccessWing = true;
									
								if($isImportSuccessWing)
								{
									$successMsg .= "Wing Details Imported : WCode: ".$wing_code." Wing Name: ".$wing_name."<br>";
								}
								else
								{
									$errorMsg .= "Wing Details Not Imported : WCode: ".$wing_code." Wing Name: ".$wing_name."<br>";
								}
							
							}
							//getting Wing ID for Unit Update
							$search_wing_code="select wing_id from wing where wing ='".$wing_name."' and society_id='".$_SESSION['society_id']."'";
							$data3=$this->m_dbConn->select($search_wing_code);
							
							if($data3=='')
							{
								$errormsg="wing id not found for wing code: &lt;".$wing_code."&gt; and society &lt;".$society_code."&gt; ";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"W");
							}
								$wing_id=$data3[0]['wing_id'];
							
							
							$LedgerName=$unit_no;
							$chk_ledger_presence="select `unit_id` from `unit` where `unit_no`='".$LedgerName."' and society_id='".$_SESSION['society_id']."' and `wing_id`='" . $wing_id . "'";
							$ledger_exists=$this->m_dbConn->select($chk_ledger_presence);
							
							
							$chk_mapping_presence="select Count(*)  as cnt from `mapping` where `unit_id`='".$unit_id."' and society_id='".$_SESSION['society_id']."'";
							$mapping_exists=$this->m_dbConnRoot->select($chk_mapping_presence);
							
							$SortOrderID =$SortOrderID + 100;
							
							//if Ledger , Wing & Society Exists, Inserting Unit Values
							if(sizeof($ledger_exists) <> 0 && ($society_id <> "" || $society_id <> 0) && ($wing_id <> "" || $wing_id <> 0))
							{
								$errormsg="Ledger name &lt;" .$LedgerName."&gt; already exists in ledger table with ledger_id as &lt;" .$ledger_exists[0]['id']."&gt;";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");	
								$unit_id=$ledger_exists[0]['id'];
								$insert_unit="insert into unit(unit_id,society_id,wing_id,unit_no,floor_no,unit_type,area,sort_order,flat_configuration,virtual_acc_no) values('$unit_id','$society_id','$wing_id','$unit_no','$floor_no','$unit_type','$area',".$SortOrderID.",'$flat_configuration','$virtualac')";
								$data=$this->m_dbConn->insert($insert_unit);
								$isImportSuccessUnit = true;
								
								
								if($mapping_exists[0]['cnt'] == 0)
								{
									$insert_mapping = "INSERT INTO `mapping`(`society_id`, `unit_id`, `desc`, `code`, `role`, `created_by`, `view`) VALUES ('" . $society_id . "', '" . $unit_id . "', '" . $unit_no . "', '" . getRandomUniqueCode() . "', '" . ROLE_MEMBER . "', '" . $_SESSION['login_id'] . "', 'MEMBER')";
									$result_mapping = $this->m_dbConnRoot->insert($insert_mapping);
								}
							}
							//If only society and Wing Exists then inserting Ledger and Unit Values
							
							else if(($society_id <> "" || $society_id <> 0) && ($wing_id <> "" || $wing_id <> 0))
							{
								$unitLedgerName = $unit_no;
								$Date = $this->get_date($_SESSION['Period']);
								//Inserting In Ledger
								$sqlInsert = "INSERT INTO `ledger`(`society_id`,`categoryid`,`ledger_name`,`opening_type`,`opening_date`,`payment`,`receipt`) VALUES ('$society_id',4, '" . $unitLedgerName . "','2','".getDBFormatDate($Date)."','1','1')";	
								$data4=$this->m_dbConn->insert($sqlInsert);
								$insertAsset = $this->obj_register->SetAssetRegister(getDBFormatDate($Date),$data4, 0, 0, TRANSACTION_DEBIT, 0, 1);
								//Inserting In Unit Table
								$insert_unit="insert into unit(unit_id,society_id,wing_id,unit_no,floor_no,unit_type,area,sort_order,flat_configuration,virtual_acc_no) values('".$data4."','$society_id','$wing_id','$unit_no','$floor_no','$unit_type','$area',".$SortOrderID.",'$flat_configuration','$virtualac')";
								$data=$this->m_dbConn->insert($insert_unit);
								$isImportSuccessUnit = true;
								
								if($mapping_exists[0]['cnt'] == 0)
								{
									
									$insert_mapping = "INSERT INTO `mapping`(`society_id`, `unit_id`, `desc`, `code`, `role`, `created_by`, `view`) VALUES ('" . $society_id . "', '" . $data4 . "', '" . $unit_no . "', '" . getRandomUniqueCode() . "', '" . ROLE_MEMBER . "', '" . $_SESSION['login_id'] . "', 'MEMBER')";
									$result_mapping = $this->m_dbConnRoot->insert($insert_mapping);
								}
							}
							else
							{
								$errormsg="Unit &lt;".$unit_no."&gt; not imported check if society code &lt;".$society_code."&gt; match with BCode in BuildingID file or wing Code &lt;".$wing_code." &gt; match with WCode in WingID file";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");	
							}
							
							//For MEMBER IMPORT CHECK WHETHER UNIT EXISTS OR NOT
							$get_unit_id="select `unit_id` from `unit` where `unit_no` = '".$unit_no."'  and `society_id` = '" . $society_id . "' and `wing_id` = '" . $wing_id . "'";
							$data4=$this->m_dbConn->select($get_unit_id);
							$unit=$data4[0]['unit_id'];
								
							if($data4=='')
							{
								$errormsg=" Unit &lt;".$unit_no."&gt;  not found in unit table for member: &lt;".$owner_name."&gt; and wing: &lt;".$wing_code."&gt;  ";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"W");
							}
								
							$get_desg_id="select desg_id from desg where desg='".$desg."'";
							$data5=$this->m_dbConn->select($get_desg_id);
							$desg_id=$data5[0]['desg_id	'];
								
							if($desg_id=="" && $desg <> '' && $desg <> 'No' && $desg <> '0')
							{
								$insert_desg="insert into desg(desg) values('".$desg."')";
								$desg_id=$this->m_dbConn->insert($insert_desg);
							}
							else if($desg=='No' || $desg=='')
							{
								$desg_id=1;
							}
								
								
							$get_bg_id="select bg_id from bg where bg='".$blood_group."'";
							$data6=$this->m_dbConn->select($get_bg_id);
							$bg_id=$data6[0]['bg_id'];
						
							if($bg_id=="")
							{
								$bg_id=9;
							}
						
							if($unit <> "" && ($society_id <> "" || $society_id <> 0) && ($wing_id <> "" || $wing_id <> 0 ))
							{
							
								$select_member_main="select Count(*) as cnt from `member_main` where `society_id`='".$society_id."' and `wing_id`='".$wing_id."' and `unit`='".$unit."' and `owner_name`='".$owner_name."'";
								$checkexistmember=$this->m_dbConn->select($select_member_main);
								
								if(trim($gstin_no) == "-")
								{
									$gstin_no_to_insert = "";
								}
								else
								{
									$gstin_no_to_insert = trim($gstin_no);
								}
								
								if($checkexistmember[0]['cnt']==0)
								{
								 	$insert_member_main = "insert into member_main(society_id,wing_id,unit,owner_name,extn_ledger_name,email,mob,desg,dob,wed_any,gender,blood_group,parking_no,owner_gstin_no,resd_no,off_no,alt_email,eme_rel_name,eme_contact_1,eme_contact_2) values('$society_id','$wing_id','$unit','$owner_name','$extn_ledger_name','$email','$mob','".$desg_id."','$dob','$wed_any','$gender','".$bg_id."','$parking_no','$gstin_no_to_insert','$resd_no','$off_no','$alt_email','$eme_rel_name','$eme_contact_1','$eme_contact_2')";
									$data=$this->m_dbConn->insert($insert_member_main);
									
									$this->UpdateCoOwners($data, $owner_name, $mob, $email);
									$get_member_id = "select `member_id` FROM `member_main` where unit='".$unit."'";
									$res2 = $this->m_dbConn->select($get_member_id);
									//var_dump($res2);
									for($i=0;$i<=$res2[$i];$i++)
									{	
									   $member_id=$res2[$i]['member_id'];
									}
									if(trim($associatemembername) != "")
									{
										$insert_member_other_family = "insert into mem_other_family (member_id, other_name,relation,coowner) VALUES ('" . $member_id . "', '".$associatemembername."','Self','3')";
										$data2=$this->m_dbConn->insert($insert_member_other_family);
									}
									
									//car parking
									if(trim($car_parking_no) != "")
									{
										$sql01 = "select member_id from member_main where unit = '".$unit."'";
										$sql11 = $this->m_dbConn->select($sql01);
										$mem_id = $sql11[0]['member_id'];
										
										$for_amp_car = array();
										for($z=0;$z<strlen($car_parking_no);$z++)
										{
											$for_amp_car[$z] = $car_parking_no[$z];
										}
										
										if(in_array("&",$for_amp_car))
										{
											$indivi_car_parking_no = str_replace('&',',',$car_parking_no);
											$car_parking_coll = explode(',',$indivi_car_parking_no);
											for($i=0;$i<sizeof($car_parking_coll);$i++)
											{
												$sql02 = "insert into mem_car_parking(member_id,parking_slot) values('".$mem_id."','".trim($car_parking_coll[$i])."')";
												$sql22 = $this->m_dbConn->insert($sql02);
											}
										}
										else
										{
											$sql03 = "insert into mem_car_parking(member_id,parking_slot) values('".$mem_id."','".trim($car_parking_no)."')";
											$sql33 = $this->m_dbConn->insert($sql03);
										}
									}
									
									//bike parking
									if(trim($bike_parking_no) != "")
									{
										$sql01 = "select member_id from member_main where unit = '".$unit."'";
										$sql11 = $this->m_dbConn->select($sql01);
										$mem_id = $sql11[0]['member_id'];
										
										$for_amp_bike = array();
										for($z=0;$z<strlen($bike_parking_no);$z++)
										{
											$for_amp_bike[$z] = $bike_parking_no[$z];
										}
										
										if(in_array("&",$for_amp_bike))
										{
											$indivi_bike_parking_no = str_replace('&',',',$bike_parking_no);
											$bike_parking_coll = explode(',',$indivi_bike_parking_no);
											for($i=0;$i<sizeof($bike_parking_coll);$i++)
											{
												$sql02 = "insert into mem_bike_parking(member_id,parking_slot) values('".$mem_id."','".trim($bike_parking_coll[$i])."')";
												$sql22 = $this->m_dbConn->insert($sql02);
											}
										}
										else
										{
											$sql03 = "insert into mem_bike_parking(member_id,parking_slot) values('".$mem_id."','".trim($bike_parking_no)."')";
											$sql33 = $this->m_dbConn->insert($sql03);
										}
									}

									$isImportSuccessMember = true;
									
									
								}
								else
								{
									$errormsg="Member &lt;".$owner_name."&gt; already exists  in this society  Hence meber not added again.";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
										
								}
							}
							else
							{
								$errormsg="Member &lt;".$owner_name."&gt; not imported check if unit no is blank &lt;".$unit_no ."&gt; or society code &lt;".$society_code."&gt; is not found for WCode :".$wing_code." and FCode : ".$unit_no;
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");		
							}
					
							//Display in Error Log File According to Conditions					
							if($isImportSuccessUnit &&  $isImportSuccessWing )
							{
								$Success++;
								if($isImportSuccessMember)
								{
									$errormsgfile='';
								
								if($errorData != '')
								{
									$errormsgfile .= $successData;
									$errormsgfile .= $errorData;
									$errormsgfile .= "<br> Row Imported Successfully";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsgfile,"W");
								}
								
								else
								{
									$errormsgfile .= $successData;
									$errormsgfile .= "<br> Row Imported Successfully";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsgfile,"I");	
								}
					
							}
							else
							{
								$errormsgfile.=$successData;
								$errormsgfile.=$ErrorMsg;
								$errormsgfile.="Row Not Imported";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsgfile,'E');
							}
					
						}
					}
					else
					{
						$Failed++;	
						$errormsgfile =$successData;
						$errormsgfile.=$ErrorMsg;
						//$errormsgfile.=$errorData;
						$errormsgfile.="<br>Row not imported";
						$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsgfile,'E');
					}
				}
				else
				{
					if($ErrorMsg!='')
					{
						$Failed++;
						$validatemsgfile=$successData;
						$validatemsgfile.=$ErrorMsg;
						$validatemsgfile.="<br>Row will not be imported";
						$this->obj_utility->logGenerator($validatefile,$rowCount,$validatemsgfile,'E');
					}
					else
					{
					
						$Success++;
						if($errorData!='')
						{
							$validatemsgfile=$successData;
							$validatemsgfile.=$errorData;
							$validatemsgfile.="<br>Row will be imported successfully";
							$this->obj_utility->logGenerator($validatefile,$rowCount,$validatemsgfile,'W');
						}
						else
						{
							$validatemsgfile=$successData;
							$validatemsgfile.="<br>Row will be imported successfully";
							$this->obj_utility->logGenerator($validatefile,$rowCount,$validatemsgfile,'I');	
						}
					}

				}
			}
		}
		
	}
		//For Full File Import, Check if all conditions are true
		if($validate=='')
		{
		if($isImportSuccessWing && $isImportSuccessUnit && $isImportSuccessMember)
		{
			$errormsgfile= "<br><File Data Imported><br>";
			$update_import_history="update `import_history` set wing_flag=1,unit_flag=1,member_flag=1 where society_id='".$_SESSION['society_id']."'";							
			$res123=$this->m_dbConn->update($update_import_history);
			$this->obj_utility->logGenerator($errorfile,'',$errormsgfile,"W");	
		}
		else
		{
			$errormsg="<br>Details not imported";
			$this->obj_utility->logGenerator($errorfile,'Error',$errormsg,"E");	
		}
		
		$errormsg="<br>[End of File Import]";
		$this->obj_utility->logGenerator($errorfile,'',$errormsg);
		}
		$validatemsgfile="<br> [End Of File Validation]";
		$this->obj_utility->logGenerator($validatefile,'',$validatemsgfile);
	}//Mostly for member flag
	
	$Total=$Success + $Failed;
	if($validate!='')
	{
		if($Success == '')
		{
			$Success=0;
			$validatemsgfile="<br>Number of rows which will be imported:".$Success." out of ".$Total;
			$this->obj_utility->logGenerator($validatefile,'',$validatemsgfile,"I");	
		}
		else
		{
			$validatemsgfile="<br>Number of rows which will be imported:".$Success." out of ".$Total;
			$this->obj_utility->logGenerator($validatefile,'',$validatemsgfile,"I");	
		}
		if($Failed=='')
		{
			$Failed=0;
			$validatemsgfile="<br>Number of rows which will not be imported:".$Failed." out of ".$Total;
			$this->obj_utility->logGenerator($validatefile,'',$validatemsgfile,"E");	
		}
		else
		{
			$validatemsgfile="<br>number of rows which will not be imported:".$Failed." out of ".$Total;
			$this->obj_utility->logGenerator($validatefile,'',$validatemsgfile,"E");
		}
		$this->actionPage="../import_society.php";
		
	}
	else
	{
	if($Success == 0 || $Success == '')
		{
			$Success=0;
			$errormsgfile="<br>Number of rows imported:".$Success." out of ".$Total;
			$this->obj_utility->logGenerator($errorfile,$rowount,$errormsgfile,"E");	
		}
		else
		{
			$errormsgfile="<br>Number of rows imported:".$Success." out of ".$Total;
			$this->obj_utility->logGenerator($errorfile,$rowount,$errormsgfile,"E");	
		}
		if($Failed==0 || $Failed=='')
		{
			$Failed=0;
			$errormsgfile="<br>Number of rows not imported:".$Failed." out of ".$Total;
			$this->obj_utility->logGenerator($errorfile,$rowount,$errormsgfile,"E");	
		}
		else
		{
			$errormsgfile="<br>Number of rows not imported:".$Failed." out of ".$Total;
			$this->obj_utility->logGenerator($errorfile,$rowount,$errormsgfile,"E");
		}
				
		}
	}
	
	public function get_date($id)
	{
		$sql = "select `BeginingDate`- INTERVAL 1 DAY  as BeginingDate from `period` where  id=".$id." ";
		$data = $this->m_dbConn->select($sql);
		return $data[0]['BeginingDate'];
	}
	
	public function getDBFormatDate($ddmmyyyy)
	{
		if($ddmmyyyy <> '' && $ddmmyyyy <> '00-00-0000')
		{
			return date('Y-m-d', strtotime($ddmmyyyy));
		}
		else
		{
			return '00-00-0000';
		}
	}
	
	public function InsertOrUpdateCoOwners($member_id, $owner_names, $mobile, $email)
	{
		$owner = str_replace('&', ',', $owner_names);
		//$owner = str_replace('/', ',', $owner);
		$owner = str_replace(' AND ', ',', $owner);
		$owner_coll = explode(',', $owner);

		$updatePrimaryOwner = "UPDATE `member_main` SET `primary_owner_name` = '" . trim($owner_coll[0]) . "'
		 WHERE `member_id` = '" . $member_id . "'";
		$resPrimaryOwner = $this->m_dbConn->update($updatePrimaryOwner);

		$sql = "SELECT mem_other_family_id FROM mem_other_family WHERE member_id =".$member_id;
		$memberExists = $this->m_dbConn->select($sql);

		for($i = 0; $i < sizeof($owner_coll); $i++)
		{
			if (!empty($memberExists))
			{
				if ($i == 0)
				{
					// var_dump($owner_coll);
					// die();
					$updateCoOwner = "UPDATE mem_other_family SET other_name = '".trim($owner_coll[$i])."'  WHERE mem_other_family_id =".$memberExists[$i]['mem_other_family_id'];
				}
				else 
				{
					$updateCoOwner = "UPDATE mem_other_family SET other_name = '".trim($owner_coll[$i])."'  WHERE mem_other_family_id =".$memberExists[$i]['mem_other_family_id'];
					// var_dump($updateCoOwner);
					// die();
				}
				$this->m_dbConn->update($updateCoOwner);
			}
			else 
			{
				$insertCoOwner = "INSERT INTO `mem_other_family` (`member_id`, `other_name`, `coowner`, `relation`, `other_mobile`, `other_email`, `send_commu_emails`) VALUES ('" . $member_id . "', '" . trim($owner_coll[$i]) . "', '1', 'Self', '" . $mobile . "', '" . $email . "', '1')";
				$resCoOwner = $this->m_dbConn->insert($insertCoOwner);
			}		
		}
	}

	private function UpdateCoOwners($member_id, $owner_names, $mobile, $email)
	{
		$owner = str_replace('&', ',', $owner_names);
		$owner = str_replace('/', ',', $owner);
		$owner = str_replace(' AND ', ',', $owner);
		$owner_coll = explode(',', $owner);
		
		$updatePrimaryOwner = "Update `member_main` SET `primary_owner_name` = '" . trim($owner_coll[0]) . "' WHERE `member_id` = '" . $member_id . "'";

		$resPrimaryOwner = $this->m_dbConn->update($updatePrimaryOwner);

		for($i = 0; $i < sizeof($owner_coll); $i++)
		{
			if($i == 0)
			{
				$insertCoOwner = "INSERT INTO `mem_other_family` (`member_id`, `other_name`, `coowner`, `relation`, `other_mobile`, `other_email`, `send_commu_emails`) VALUES ('" . $member_id . "', '" . trim($owner_coll[$i]) . "', '1', 'Self', '" . $mobile . "', '" . $email . "', '1')";
			}
			else
			{
				$insertCoOwner = "INSERT INTO `mem_other_family` (`member_id`, `other_name`, `coowner`) VALUES ('" . $member_id . "', '" . trim($owner_coll[$i]) . "', '2')";
			}

			$resCoOwner = $this->m_dbConn->insert($insertCoOwner);
		}
	}

	public function isValidEmailID($email)
	{
		$bResult = true;
		if(strpos($email, '@') && strpos($email, '.') == false)
		{
			$bResult = false;
		}
		return $bResult;
	}

	public function updateEmail($member_id, $email)
	{
		$sql = "UPDATE mem_other_family SET other_email = '" . $email . "' WHERE member_id = '" . $member_id ."'";
		$this->m_dbConn->update($sql);
	}

	public function updateMobNum($member_id, $mob)
	{
		$sql = "UPDATE mem_other_family SET other_mobile = '" . $mob . "' WHERE member_id = '" . $member_id ."'";
		$this->m_dbConn->update($sql);
	}

	
}


?>
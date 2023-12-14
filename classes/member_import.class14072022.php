<?php
include_once("include/dbop.class.php");
include_once("utility.class.php");
include_once("CsvOperations.class.php");
include_once("dbconst.class.php");
include_once("include/fetch_data.php");

$dbConn = new dbop();

//$obj_fetch = new FetchData($dbConn);

//echo "ash";
		//$a = $obj_fetch->GetSocietyDetails($_SESSION['society_id']);
		//echo $obj_fetch->objSocietyDetails->sSocietyCode;
		
class member_import 
{
	public $allColumns = array('BCode', 'WCode', 'FCode','FloorNo', 'Owner','ExtnLedgerName','Occupation', 
			'DateOfBirth', 'AnnivarsaryDate', 'Gender', 'BloodGroup', 'OwnerAddress','FlatArea', 'GSTINNO', 'ResPhone', 'MobileNo','OffPhone', 'EMail', 'Email1', 'DisposeDate', 'Inactive', 'EmergencyPersonName','EmergencyMobileNo', 'EmergencyTelephoneNo','PARKING_NO');

	public $ci = array();
	const BCODE = 0;
	const WCODE = 1;
	const FCODE = 2;
	const FLOOR_NO = 3;
	const OWNER= 4;
	const ExtnLedgerName = 5;
	const OCCUPATION = 6;
	const DOB = 7;
	const WED_DATE = 8;
	const GENDER = 9;
	const BLOOD_GROUP = 10;
	const ADDRESS = 11;
	const AREA=12;
	const GSTIN = 13;
	const RES_PHONE = 14;
	const MOBILE = 15;
	const OFFICE_PHONE = 16;
	const EMAIL = 17;
	const ALT_EMAIL = 18;
	const DISPOSE_DATE = 19;
	const INACTIVE = 20;
	const EMER_NAME = 21;
	const EMER_MOBILE = 22;
	const EMER_TEL = 23;
	const PARKING_NO = 24;
	
	
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_utility;
	public $errorfile_name;
	public $errorLog;
	public $actionPage = '../import_member.php';
	public $csv;
	public $obj_fetch;

	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->obj_fetch = new FetchData($this->m_dbConn);

		$this->obj_utility = new utility($this->m_dbConn);
		$this->csv = new CsvOperations();

		//echo "ashwo";
		$a = $this->obj_fetch->GetSocietyDetails($_SESSION['society_id']);
		//echo $this->obj_fetch->objSocietyDetails->sSocietyCode;
		//die();
	}
	
	public function CSVMemberImport()
	{
		if(isset($_POST["Import"]))
		{
			//echo 'Inside CSVUnitImport';
			if(isset($_FILES['file']) && $_FILES['file']['error'] == 0)
			{
				$result = "0";
				 $ext = pathinfo($_FILES['file'] ['name'], PATHINFO_EXTENSION);
				//$fileName = "files/" . $dateTimeNow. ".csv";
				 $tempName = $_FILES['file'] ['tmp_name'];
				/*
				$original_file_name='OwnerID.csv';
				//echo $_FILES['file'] ['name'];
				if(($_FILES['file'] ['name']) != "$original_file_name") {
					  //exit("Does not match");
					  $result = '<p>File Name Does Not Match(only OwnerID.csv file accepted)...</p>';
					  
					}
				else 
				*/
				if($ext <> '' && $ext <> 'csv')
				{	
					$result = '<p>Invalid file format selected. Expected csv file format</p>';
				}
				else
				{
					//if ( move_uploaded_file ($_FILES['file'] ['tmp_name'], $fileName)  )
					if (isset($_FILES['file']['error']) || is_array($_FILES['file']['error']))
					{  
					//echo "inside uploading print <br/>";
						$result = '<p> Member Data Uploading Process Started <' . $this->getDateTime() . '> </p>';
						//echo "1";
						$result .= $this->UploadData($tempName);
						//echo "2";
						$result .= '<p> Member Data Uploading Process Complete <' . $this->getDateTime() . '> </p>';
						//echo "printed";
					}
					else
					{ 
						echo $_FILES['file'] ['error'];
						switch ($_FILES['file'] ['error'])
						{
							case 1:
								   echo '<p> The file is bigger than this PHP installation allows</p>';
								   $result = '<p> The file is bigger than this PHP installation allows</p>';
								   break;
							case 2:
								   echo '<p> The file is bigger than this form allows</p>';
								   $result = '<p> The file is bigger than this form allows</p>';
								   break;
							case 3:
								   echo '<p> Only part of the file was uploaded</p>';
								   $result = '<p> Only part of the file was uploaded</p>';
								   break;
							case 4:
								   echo '<p> No file was uploaded</p>';
								   $result = '<p> No file was uploaded</p>';
								   break;
						}
					} 
				}
			}
			else if(isset($_FILES['file']) && $_FILES['file']['error'] <> 0)
			{
				$errorCode = $_FILES['file']['error']; 
				switch ($errorCode)
				{
					case 1:
						   //echo '<p> The file is bigger than this PHP installation allows</p>';
						   $result = '<p> The file is bigger than this PHP installation allows</p>';
						   break;
					case 2:
						   //echo '<p> The file is bigger than this form allows</p>';
						   $result = '<p> The file is bigger than this form allows</p>';
						   break;
					case 3:
						   //echo '<p> Only part of the file was uploaded</p>';
						   $result = '<p> Only part of the file was uploaded</p>';
						   break;
					case 4:
						   //echo '<p> No file was uploaded</p>';
						   $result = '<p> No file was uploaded</p>';
						   break;
				}
			}
			//echo '<body onload="parent.doneloading(\''.$result.'\')"></body>'; 
			return $result;
			
		}
	}
	
	public function UploadData($fileName,$errorfile)
	{
		$Foldername = $this->obj_fetch->objSocietyDetails->sSocietyCode;

		if (!file_exists('../logs/import_log/'.$Foldername)) 
		{
			mkdir('../logs/import_log/'.$Foldername, 0777, true);
		}


		$this->errorfile_name = '../logs/import_log/'.$Foldername.'/member_import_errorlog_'.date("d.m.Y").'_'.rand().'.html';
		$this->errorLog = $this->errorfile_name;		
		$errorfile = fopen($this->errorfile_name, "a");

		$file = fopen($fileName,"r");
		$errormsg="[Importing OwnerID]";
		$isImportSuccess = false;
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		$sql00="select member_flag from `import_history` where society_id='".$_SESSION['society_id']."'";
		$res01=$this->m_dbConn->select($sql00);
		if($res01[0]['member_flag']==0)
		{
				while (($row = fgetcsv($file)) !== FALSE)
				{
					//echo '<br/>';
					if($row[0] <> '')
						{
							$rowCount++;
							if($rowCount == 1)
							{
								$WCode=array_search(WCode,$row,true);
								$BCode=array_search(BCode,$row,true);
								$FCode=array_search(FCode,$row,true);
								$Owner=array_search(Owner,$row,true);
								$DateOfBirth=array_search(DateOfBirth,$row,true);
								$AnnivarsaryDate=array_search(AnnivarsaryDate,$row,true);
								$BloodGroup=array_search(BloodGroup,$row,true);
								$MobileNo=array_search(MobileNo,$row,true);
								$EMail=array_search(EMail,$row,true);
								$EMail1=array_search(Email1,$row,true);
								$EmergencyPersonName=array_search(EmergencyPersonName,$row,true);
								$EmergencyMobileNo=array_search(EmergencyMobileNo,$row,true);
								$EmergencyTelephoneNo=array_search(EmergencyTelephoneNo,$row,true);
								$Gender=array_search(Gender,$row,true);
								$Occupation=array_search(Occupation,$row,true);
								$OffPhone=array_search(OffPhone,$row,true);
								$Inactive=array_search(Inactive,$row,true);
								$DisposeDate=array_search(DisposeDate,$row,true);
								$OwnerAddress=array_search(OwnerAddress,$row,true);
								$CarParkingNo=array_search(CarParkingNo,$row,true);
								$GSTINNO=array_search(GSTINNO,$row,true);
								$BikeParkingNo=array_search(BikeParkingNo,$row,true);
								
								if(!isset($BCode) || !isset($WCode)  || !isset($FCode) || !isset($Owner) || !isset($DateOfBirth) || !isset($AnnivarsaryDate) || !isset($BloodGroup) || !isset($MobileNo) ||  !isset($EMail)  || !isset($EMail1) || !isset($EmergencyPersonName) || !isset($EmergencyMobileNo) || !isset($EmergencyTelephoneNo) || !isset($Gender) || !isset($Occupation) || !isset($OffPhone))// !isset($FloorNo)
									{
										$result = '<p>Column Names Not Found Cant Proceed Further......</p>'.'Go Back';
										$errormsg=" Column names   in file OwnerId not match";
										$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
										return $result;
										exit(0);
									}
								
								
							}
						 else
						   {
							   	$society_code=$row[$BCode];
								$wing_code=$row[$WCode];
								$unit_no=$row[$FCode];
								$owner_name= $this->m_dbConn->escapeString($row[$Owner]);
								$gender=$row[$Gender];
								$mob=$row[$MobileNo];
								$off_no=$row[$OffPhone];
								$desg=$row[$Occupation];
								$email=$row[$EMail];
								$alt_email=$row[$EMail1];
								$dob=$row[$DateOfBirth];
								$wed_any=$row[$AnnivarsaryDate];
								$blood_group=$row[$BloodGroup];
								$eme_rel_name=$row[$EmergencyPersonName];
								$eme_contact_1=$row[$EmergencyMobileNo];
								$eme_contact_2=$row[$EmergencyTelephoneNo];
								$owner_inactive=$row[$Inactive];
								$despdate=$row[$DisposeDate];
								$parking_no=$row[$OwnerAddress];
								$car_parking_no=$row[$CarParkingNo];
								$gstin_no=$row[$GSTINNO];				
								$bike_parking_no=$row[$BikeParkingNo];	
								
								$get_society_id="select society_id from society where society_code='".$society_code."'";
								$data2=$this->m_dbConn->select($get_society_id);
								if($data2=='')
								{
									$errormsg=" Society Code &lt;".$society_code."&gt;  not found in society table for unit: &lt;".$unit_no."&gt; and wing: &lt;".$wing_code."&gt;";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"W");
								}
								$society_id=$data2[0]['society_id'];
								$get_wing_id="select wing_id from wing where wing='".$wing_code."' and society_id = '" . $society_id . "'";
								$data3=$this->m_dbConn->select($get_wing_id);
								$wing_id=$data3[0]['wing_id'];
								if($data3=='')
								{
									$errormsg=" Wing &lt;".$wing_code."&gt; not found in wing table for unit: &lt;".$unit_no."&gt; and society &lt;".$society_code."&gt;";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"W");
								}
								
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
								
								if($owner_inactive=='')
								{
									
									$errormsg="Inactive flag  in ownerid is &lt;".$owner_inactive."&gt; Hence meber not added ";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
								}
								
								if($owner_inactive=='NO' && $despdate=="" && $unit <> "" && ($society_id <> "" || $society_id <> 0) && ($wing_id <> "" || $wing_id <> 0 ))
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
										$insert_member_main="insert into member_main(society_id,wing_id,unit,owner_name,gender,parking_no,resd_no,mob,alt_mob,off_no,off_add,desg,email,alt_email,dob,wed_any,blood_group,eme_rel_name,eme_contact_1,eme_contact_2,status,owner_gstin_no) values('$society_id','$wing_id','$unit','$owner_name','$gender','$parking_no','$resd_no','$mob','$alt_mob','$off_no','$off_add','".$desg_id."','$email','$alt_email','$dob','$wed_any','".$bg_id."','$eme_rel_name','$eme_contact_1','$eme_contact_2','Y','$gstin_no_to_insert')";
										$data=$this->m_dbConn->insert($insert_member_main);

										$this->UpdateCoOwners($data, $owner_name, $mob, $email);
										
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

										$isImportSuccess = true;
									}
									else
									{
										$errormsg="Member &lt;".$owner_name."&gt; already exists  in this society  Hence meber not added again.";
										$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
											
									}
								}
								else
								{
									$errormsg="Member &lt;".$owner_name."&gt; not imported check if owner is inactive &lt;" .$owner_inactive."&gt; or unit no is blank &lt;".$unit_no ."&gt; or society code &lt;".$society_code."&gt; match with BCode in BuildingID file or wing code &lt;".$wing_code." &gt;match with WCode in WingID file or dispose date not empty   &lt;".$despdate."&gt;"."in OwnerID file";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");		
								}
								
								
						   }
					
				}
				
			}
	
		}
		if($isImportSuccess)
		{
			$update_import_history="update `import_history` set member_flag=1 where society_id='".$_SESSION['society_id']."'";	
			$res123=$this->m_dbConn->update($update_import_history);
		}
		else
		{
			$errormsg="member details not imported";
			$this->obj_utility->logGenerator($errorfile,'Error',$errormsg,"E");	
		}	
						
		$errormsg="[End of  OwnerID]";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
		
	}
	
	public function UploadDataManually($file, $indexes, $array)
	{
		$this->errorfile_name = '../logs/import_log/update_members_errorlog_'.date("d.m.Y").'_'.rand().'.html';
		$this->errorLog = $this->errorfile_name;
		$errorfile = fopen($this->errorfile_name, "a");
		$errormsg="[Updating Members Data]";
		$isImportSuccess = false;
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);

		//Getting the columns selected by the user
		for ($i = 0; $i < count($this->allColumns); $i++)
		{
			if (in_array($i, $indexes))
			{
				switch($this->allColumns[$i])
				{
					case 'BCode':
						$this->ci[member_import::BCODE][0] = $i;
						$this->ci[member_import::BCODE][1] = 1;
						break;
					case 'WCode':
						$this->ci[member_import::WCODE][0] = $i;
						$this->ci[member_import::WCODE][1] = 1;
						break;
					case 'FCode':
						$this->ci[member_import::FCODE][0] = $i;
						$this->ci[member_import::FCODE][1] = 1;
						break;
					case 'FloorNo':
						$this->ci[member_import::FLOOR_NO][0] = $i;
						$this->ci[member_import::FLOOR_NO][1] = 1;
						break;	
					case 'Owner':
						$this->ci[member_import::OWNER][0] = $i;
						$this->ci[member_import::OWNER][1] = 1;
						break;
					case 'ExtnLedgerName':
						$this->ci[member_import::ExtnLedgerName][0] = $i;
						$this->ci[member_import::ExtnLedgerName][1] = 1;
						break;	
					case 'Occupation':
						$this->ci[member_import::OCCUPATION][0] = $i;
						$this->ci[member_import::OCCUPATION][1] = 1;
						break;
					case 'DateOfBirth':
						$this->ci[member_import::DOB][0] = $i;
						$this->ci[member_import::DOB][1] = 1;
						break;
					case 'AnnivarsaryDate':
						$this->ci[member_import::WED_DATE][0] = $i;
						$this->ci[member_import::WED_DATE][1] = 1;
						break;
					case 'Gender':
						$this->ci[member_import::GENDER][0] = $i;
						$this->ci[member_import::GENDER][1] = 1;
						break;
					case 'BloodGroup':
						$this->ci[member_import::BLOOD_GROUP][0] = $i;
						$this->ci[member_import::BLOOD_GROUP][1] = 1;
						break;
					case 'OwnerAddress':
						$this->ci[member_import::ADDRESS][0] = $i;
						$this->ci[member_import::ADDRESS][1] = 1;
						break;
					case 'FlatArea':
						$this->ci[member_import::AREA][0]=$i;
						$his->ci[member_import::AREA][1]=$i;
						break;
					case 'GSTINNO':
						$this->ci[member_import::GSTIN][0] = $i;
						$this->ci[member_import::GSTIN][1] = 1;
						break;
					case 'ResPhone':
						$this->ci[member_import::RES_PHONE][0] = $i;
						$this->ci[member_import::RES_PHONE][1] = 1;
						break;
					case 'MobileNo':
						$this->ci[member_import::MOBILE][0] = $i;
						$this->ci[member_import::MOBILE][1] = 1;
						break;
					case 'OffPhone':
						$this->ci[member_import::OFFICE_PHONE][0] = $i;
						$this->ci[member_import::OFFICE_PHONE][1] = 1;
						break;
					case 'EMail':
						$this->ci[member_import::EMAIL][0] = $i;
						$this->ci[member_import::EMAIL][1] = 1;
						break;
					case 'Email1':
						$this->ci[member_import::ALT_EMAIL][0] = $i;
						$this->ci[member_import::ALT_EMAIL][1] = 1;
						break;
					case 'DisposeDate':
						$this->ci[member_import::DISPOSE_DATE][0] = $i;
						$this->ci[member_import::DISPOSE_DATE][1] = 1;
						break;					
					case 'Inactive':
						$this->ci[member_import::INACTIVE][0] = $i;
						$this->ci[member_import::INACTIVE][1] = 1;
						break;					
					case 'EmergencyPersonName':
						$this->ci[member_import::EMER_NAME][0] = $i;
						$this->ci[member_import::EMER_NAME][1] = 1;
						break;					
					case 'EmergencyMobileNo':
						$this->ci[member_import::EMER_MOBILE][0] = $i;
						$this->ci[member_import::EMER_MOBILE][1] = 1;
						break;					
					case 'EmergencyTelephoneNo':
						$this->ci[member_import::EMER_TEL][0] = $i;
						$this->ci[member_import::EMER_TEL][1] = 1;
						break;
					case 'PARKING_NO':
						$this->ci[member_import::PARKING_NO][0] = $i;
						$this->ci[member_import::PARKING_NO][1] = 1;
						break;	
				}
			}
		}

		//Getting the columns by comparing with $allColumns Array
		for ($i = 0; $i < count($this->allColumns); $i++)
		{
			switch($i)
			{
				case $this->ci[member_import::BCODE][0]:
						$BCode=$this->allColumns[member_import::BCODE];
						break;
				case $this->ci[member_import::WCODE][0]:
						$WCode=$this->allColumns[member_import::WCODE];
						break;
				case $this->ci[member_import::FCODE][0]:
						$FCode=$this->allColumns[member_import::FCODE];
						break;
				case $this->ci[member_import::FLOOR_NO][0]:
						$FloorNo=$this->allColumns[member_import::FLOOR_NO];
						break;	
				case $this->ci[member_import::MOBILE][0]:
						$MobileNo=$this->allColumns[member_import::MOBILE];
						break;
				case $this->ci[member_import::OWNER][0]:
						$Owner = $this->allColumns[member_import::OWNER];	
						break;
				case $this->ci[member_import::ExtnLedgerName][0]:
						$Owner = $this->allColumns[member_import::ExtnLedgerName];	
						break;
				case $this->ci[member_import::DOB][0]:
						$DateOfBirth=$this->allColumns[member_import::DOB];
						break;
				case $this->ci[member_import::WED_DATE][0]:
						$AnnivarsaryDate=$this->allColumns[member_import::WED_DATE];
						break;
				case $this->ci[member_import::OCCUPATION][0]:
						$Occupation=$this->allColumns[member_import::OCCUPATION];
						break;
				case $this->ci[member_import::GENDER][0]:
						$Gender=$this->allColumns[member_import::GENDER];
						break;
				case $this->ci[member_import::BLOOD_GROUP][0]:
						$BloodGroup=$this->allColumns[member_import::BLOOD_GROUP];
						break;
				case $this->ci[member_import::ADDRESS][0]:
						$OwnerAddress=$this->allColumns[member_import::ADDRESS];
						break;
				case $this->ci[member_import::AREA][0]:
						$FlatArea=$this->allColumns[member_import::AREA];
						break;
				case $this->ci[member_import::GSTIN][0]:
						$GSTINNO=$this->allColumns[member_import::GSTIN];
						break;
				case $this->ci[member_import::RES_PHONE][0]:
						$ResPhone=$this->allColumns[member_import::RES_PHONE];
						break;
				case $this->ci[member_import::OFFICE_PHONE][0]:
						$OffPhone=$this->allColumns[member_import::OFFICE_PHONE];
						break;
				case $this->ci[member_import::EMAIL][0]:
						$EMail=$this->allColumns[member_import::EMAIL];
						break;
				case $this->ci[member_import::ALT_EMAIL][0]:
						$Email1=$this->allColumns[member_import::ALT_EMAIL];
						break;
				case $this->ci[member_import::DISPOSE_DATE][0]:
						$DisposeDate=$this->allColumns[member_import::DISPOSE_DATE];
						break;
				case $this->ci[member_import::INACTIVE][0]:
						$Inactive=$this->allColumns[member_import::INACTIVE];
						break;
				case $this->ci[member_import::EMER_NAME][0]:
						$EmergencyPersonName=$this->allColumns[member_import::EMER_NAME];
						break;
				case $this->ci[member_import::EMER_MOBILE][0]:
						$EmergencyMobileNo=$this->allColumns[member_import::EMER_MOBILE];
						break;
				case $this->ci[member_import::EMER_TEL][0]:
						$EmergencyTelephoneNo=$this->allColumns[member_import::EMER_TEL];
						break;
				case $this->ci[member_import::PARKING_NO][0]:
						$ParkingNo = $this->allColumns[member_import::PARKING_NO];
						break;		
			}
		}
		$m_TraceDebugInfo = "<br><br>Got the columns to be updated!... Column Names are: ". $BCode. " ". $WCode. " " .$FCode. " " . $Owner. " ". $Occupation. " " . $DateOfBirth . " " . $AnnivarsaryDate. " ". $Gender . " " .$BloodGroup. " " . $OwnerAddress. " " . $BikeParkingNo. " " . $CarParkingNo . " " . $GSTINNO. " " . $ResPhone. " " .$MobileNo . " " . $OffPhone. " " . $EMail. " " . $Email1. " " . $DisposeDate. " " . $Inactive. " " .$EmergencyMobileNo. " " . $EmergencyPersonName. " " . $EmergencyTelephoneNo." ".$FlatArea." ".$ParkingNo. " ".$FloorNo;

		// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
		
		$rowCount = 0;

		//Looping through each and every row and saving the values in the database
		for ($i = 1; $i < count($array) - 1; $i++)
		{
			$rowCount++;
			$m_TraceDebugInfo = "";

			$society_code=$array[$i][member_import::BCODE];
			$wing_code=$array[$i][member_import::WCODE];
			$unit_no=$array[$i][member_import::FCODE];

			$owner_name= $this->m_dbConn->escapeString($array[$i][member_import::OWNER]);
			if($owner_name != '')
			{
				$m_TraceDebugInfo .= "Owner: &lt;" . $owner_name . "&gt;";	
			}
			
			// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");

			$ExtnLedgerName = $array[$i][member_import::ExtnLedgerName];
			if ($ExtnLedgerName != '') 
			{
				$m_TraceDebugInfo .= "ExtnLedgerName : &lt;" . $ExtnLedgerName . "&gt;";
				//$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}
			
			$desg=$array[$i][member_import::OCCUPATION];
			if ($desg != '') 
			{
				$m_TraceDebugInfo .= "Occupation: &lt;" . $desg . "&gt;";
				$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}
			
			$area=$array[$i][member_import::AREA];
			if ($area != '') 
			{
				$m_TraceDebugInfo .= "Flat Area: &lt;" . $area . "&gt;";
				//$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			$dob=$array[$i][member_import::DOB];
			if ($dob != '')
			{
				$m_TraceDebugInfo .= "DOB: &lt;" . $dob . "&gt;";
				$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			$wed_any=$array[$i][member_import::WED_DATE];
			if ($wed_any != '') 
			{
				$m_TraceDebugInfo .= "Wedding Date: &lt;" . $wed_any . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}
-
			$gender=$array[$i][member_import::GENDER];
			if ($gender != '') 
			{
				$m_TraceDebugInfo .= "Gender: &lt;" . $gender . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			$blood_group=$array[$i][member_import::BLOOD_GROUP];
			if ($blood_group != '') 
			{
				$m_TraceDebugInfo .= "Blood Group: &lt;" . $blood_group."&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			$owner_address=$array[$i][member_import::ADDRESS];

			$gstin_no=$array[$i][member_import::GSTIN];
			if ($gstin_no != '')
			{
				$m_TraceDebugInfo .= "GSTIN: &lt;" . $gstin_no . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			$res_phone=$array[$i][member_import::RES_PHONE];
			if($res_phone != '')
			{
				$m_TraceDebugInfo .= "Residence Phone: &lt;" . $res_phone . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			$mob=$array[$i][member_import::MOBILE];
			if ($mob != '')
			{
				$m_TraceDebugInfo .= "Mobile: &lt;" . $mob . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			$off_no=$array[$i][member_import::OFFICE_PHONE];
			if ($off_no != '') 
			{
				$m_TraceDebugInfo .= "Office Number: &lt;" . $off_no . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			$email=$array[$i][member_import::EMAIL];
			// echo $email;
			if ($email != '')
			{
				$m_TraceDebugInfo .= "Email: &lt;" . $email . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			$alt_email=$array[$i][member_import::ALT_EMAIL];
			if ($alt_email != '')
			{
				$m_TraceDebugInfo .= "Alternate Email: &lt;" . $alt_email . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			$despdate=$array[$i][member_import::DISPOSE_DATE];
			if ($despdate != '')
			{
				$m_TraceDebugInfo .= "Dispose Date: &lt;" . $despdate . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			$owner_inactive=$array[$i][member_import::INACTIVE];
			if ($owner_inactive != '')
			{
				$m_TraceDebugInfo .= "Inactive: &lt;" . $owner_inactive . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			$eme_rel_name=$array[$i][member_import::EMER_NAME];
			if ($eme_rel_name != '')
			{
				$m_TraceDebugInfo .= "Emergency Person Name: &lt;" . $eme_rel_name . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			$eme_contact_1=$array[$i][member_import::EMER_MOBILE];
			if ($eme_contact_1 != '')
			{
				$m_TraceDebugInfo .= "Emergency Contact 1: &lt;" . $eme_contact_1 . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			$eme_contact_2=$array[$i][member_import::EMER_TEL];
			if ($eme_contact_2 != '')
			{
				$m_TraceDebugInfo .= "Emergency Contact 2: &lt;" . $eme_contact_2 . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}
			
			$parking_no = $array[$i][member_import::PARKING_NO];
			
			if ($parking_no != '')
			{
				$m_TraceDebugInfo .= "Parking No : &lt;" . $parking_no . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}
			// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");

			$floor = $array[$i][member_import::FLOOR_NO];
			if ($floor != '') 
			{
				$m_TraceDebugInfo .= "Floor Number: &lt;" . $floor . "&gt;";
				//$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			$memberExists = true;

			$getSocietyAndWingSQL = "SELECT wing.wing_id, society.society_id FROM wing JOIN society 
					ON wing.society_id = society.society_id 
					WHERE society.society_code = '$society_code' AND trim(wing.wing)= '$wing_code'";
			$getSocietyAndWing = $this->m_dbConn->select($getSocietyAndWingSQL);

			if($getSocietyAndWing=='')
			{
				$memberExists = false;
				$errormsg=" Society Code &lt;".$society_code."&gt;  not found in society table for unit: &lt;".$unit_no."&gt; and wing: &lt;".$wing_code."&gt;";
				$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"W");
			}
			$society_id=$getSocietyAndWing[0]['society_id'];
			$wing_id=$getSocietyAndWing[0]['wing_id'];
			
			if($getSocietyAndWing=='')
			{
				$memberExists = false;				
				$errormsg=" Wing &lt;".$wing_code."&gt; not found in wing table for unit: &lt;".$unit_no."&gt; and society &lt;".$society_code."&gt;";
				$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"W");
			}
			
			$get_unit_id="select `unit_id` from `unit` where `unit_no` = '".$unit_no."'  and `society_id` = '" . $society_id . "' and `wing_id` = '" . $wing_id . "'";
			$data4=$this->m_dbConn->select($get_unit_id);
			$unit=$data4[0]['unit_id'];
			
			if($data4=='')
			{
				$memberExists = false;
				$errormsg=" Unit &lt;".$unit_no."&gt;  not found in unit table for member: &lt;".$owner_name."&gt; and wing: &lt;".$wing_code."&gt;  ";
				$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"W");
			}
			
			if ($memberExists == true)
			{
				//Member found... Updating the details of the Member as per the FCode
				$sql = "";
				$MemberOtherSql = "";
				
				if (isset($FlatArea))
				{
					//Flat Area to be Updated
					if ($area != '')
					{
						if($this->obj_utility->isNumeric($area))
						{
							$sql1 = "UPDATE unit SET area='".$area."' WHERE unit_id='".$unit."'";
							$this->m_dbConn->update($sql1);
							$m_TraceDebugInfo .= "Updated Area where Flat No: <" .$unit_no. "><br>";
						}// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
						else
						{
							$m_TraceDebugInfo.="Invalid Flat Area Format &lt;".$area."&gt;";
						}
					}
				}

				if (isset($FloorNo))
				{
					//Floor Number to be Updated
					if ($floor != '')
					{
						if($this->obj_utility->isNumeric($floor))
						{
							$sql1 = "UPDATE unit SET floor_no ='".$floor."' WHERE unit_id='".$unit."'";
							$this->m_dbConn->update($sql1);
							$m_TraceDebugInfo .= "Updated Floor No where Flat No: <" .$unit_no. "><br>";
						}// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
						else
						{
							$m_TraceDebugInfo.="Invalid Floor Number Format &lt;".$floor."&gt;";
						}
					}
				}
			
			
				if (isset($Owner))
				{
					//Owner Name to be Updated
					if ($owner_name != '')
					{
						
						$sql1 = "UPDATE member_main SET owner_name='".$owner_name."' WHERE unit='".$unit."'";
						$this->m_dbConn->update($sql1);
						$m_TraceDebugInfo .= "Updated Owner Name where Flat No: <" . $unit . ">";
						// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
																		
						$sql1 = "SELECT member_id FROM member_main WHERE unit = '".$unit."'";
						$member_id = $this->m_dbConn->select($sql1);

						if($this->InsertOrUpdateCoOwners($member_id[0]['member_id'], $owner_name, $mob, $email) == true)
						{
							$m_TraceDebugInfo .= "Updated Owners Where Flat No: <" . $unit . ">";
						}
						else
						{
							$m_TraceDebugInfo .= "Error Updating Owners Where Flat No: <" . $unit . ">";
						}
						// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
					}
				}
				
				if (isset($ExtnLedgerName))
				{
					//extn_ledger_name to be Updated
					if ($ExtnLedgerName != '')
					{
						$sql .= "extn_ledger_name='".$ExtnLedgerName."',";
					}
				}

				if (isset($Occupation))
				{
					//Occupation is to be updated
					if ($desg != '')
					{
						$desgIdSQL = "SELECT desg_id FROM desg WHERE desg = '$desg'";
						$desgID = $this->m_dbConn->select($desgIdSQL);

						$sql .= "desg ='".$desgID[0]['desg_id']."',";
					}
					else
					{
						$sql .= "desg='1',";
					}
				}
				
				if (isset($DateOfBirth))
				{
					//Date of birth is to updated
					if ($dob != '')											
					{
						if ($this->validateDate($dob) != '') 
						{
							$sql .= "dob ='".$this->validateDate($dob)."',";
						}
						else
						{
							// var_dump('in else');
							$m_TraceDebugInfo.="INVALID: Please check the date format of ".$dob;
							// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"E");
						}
					}
				}
				
				if (isset($AnnivarsaryDate))
				{
					//Wedding Anniversary date is to updated
					if ($wed_any != '')
					{
						if ($this->validateDate($wed_any) != '') 
						{
							$sql .= "wed_any ='".$this->validateDate($wed_any)."',";
						}
						else
						{	//var_dump('in else');
							$m_TraceDebugInfo.="INVALID: Please check the date format of ".$wed_any;
							// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"E");
						}
					}
				}
				
				if (isset($ParkingNo))
				{
					//Parking is to updated	
					
					if ($parking_no != '')
					{
						$sql .= "parking_no ='".$parking_no."',";
					}
				}
				
				if (isset($Gender))
				{
					//Gender is to updated		
					if ($gender != '')
					{
						$sql .= "gender ='".$gender."',";
					}
				}
				if (isset($BloodGroup))
				{
					//Blood Group is to updated
					if ($blood_group != '')
					{
						$sql .= "blood_group ='".$blood_group."',";
					}
				}

				if (isset($OwnerAddress))
				{
					//Owner Address is to updated
					if ($owner_address != '')
					{
						$sql .= "alt_address='$owner_address',";
					}
				}

				if (isset($GSTINNO)) 
				{
					// GSTIN Number to be updated
					if ($gstin_no != '')
					{
						$sql .= "owner_gstin_no ='".$gstin_no."',";
					}
				}

				if (isset($ResPhone)) 
				{
					// Residence Number to be updated
					if ($res_phone != '')
					{
						$sql .= "resd_no ='".$res_phone."',";
					}
				}						

				if (isset($MobileNo)) 
				{
					// Mobile Number to be updated
					if ($mob != '')
					{
						$sql .= "mob =".$mob.",";
						$MemberOtherSql .= "other_mobile =".$mob.",";
					}
				}										

				if (isset($OffPhone)) 
				{
					// Office Number to be updated
					if ($off_no != '')
					{
						$sql .= "off_no =".$off_no.",";
					}
				}
				// $regexForEmail = "^[a-zA-Z0-9_+&*-]+(?:\\.[a-zA-Z0-9_+&*-]+)*@" + "(?:[a-zA-Z0-9-]+\\.)+[a-zA-Z]{2,7}$";
				if (isset($EMail))
				{
					// Email to be updated	
					if ($email != '')
					{
						if ($this->isValidEmailID($email)) 
						{		
							$sql .= "email ='".$email."',";
							$MemberOtherSql .= "other_email ='".$email."',";
						}
						else
						{
							$m_TraceDebugInfo.="INVALID: Please check the email format of ".$email;
							// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"E");
						}
					}
				}
				if (isset($Email1))
				{
					//Alternate Email to be updated
					if ($alt_email != '')
					{
						if ($this->isValidEmailID($alt_email))
						{
							$sql .= "alt_email ='".$alt_email."',";
						}
						else
						{
							$m_TraceDebugInfo.="INVALID: Please check the email format of ".$alt_email.".";
							// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"E");
						}
					}
				}
				if (isset($DisposeDate))
				{
					//Dispose Date is to be updated
					if ($despdate != '')
					{
						$sql .= "";
					}
				}
				if (isset($Inactive))
				{
					//Inactive Flag is to be updated
					if ($owner_inactive != '')
					{
						$sql .= "";
					}
				}
				if (isset($EmergencyPersonName))
				{
					//Emergency Person Name is to be updated
					if ($eme_rel_name != '')
					{ 
						$sql .= "eme_rel_name='".$eme_rel_name."',";
					}
				}	
				if (isset($EmergencyMobileNo))
				{
					//Emergency Person Mobile Number is to be updated
					if ($eme_contact_1 != '')
					{
						$sql .= "eme_contact_1='".$eme_contact_1."',";
					}
				}
				if (isset($EmergencyTelephoneNo))
				{
					//Emergency Person Telephone is to be updated
					if ($eme_contact_2 != '')
					{
						$sql .="eme_contact_2='".$eme_contact_2."',";
					}
				}

				$sql = rtrim($sql, ",");
				$MemberOtherSql = rtrim($MemberOtherSql, ",");
				
				
				if ($sql != "" || $sql1 != "")
				{
					if($sql != "")
					{
						$query = "UPDATE member_main SET ". $sql ." WHERE unit = '".$unit."' and `ownership_status` = 1";
						
						$this->m_dbConn->update($query);
						
						if($MemberOtherSql != "")
						{
							$Select_Member_OtherSql = "SELECT mof.mem_other_family_id from member_main as m JOIN mem_other_family as mof ON m.member_id = mof.member_id JOIN unit as u ON m.unit = u.unit_id where u.unit_id = '".$unit."' and mof.coowner =1 and m.ownership_status = 1";
							
							$Member_other_details = $this->m_dbConn->select($Select_Member_OtherSql);
							
							$query1 = "UPDATE mem_other_family SET ". $MemberOtherSql ." WHERE mem_other_family_id = '".$Member_other_details[0]['mem_other_family_id']."'";
							
							$this->m_dbConn->update($query1);
							
						}
						
	
						$m_TraceDebugInfo .= "Updated Member Details Where Flat No: &lt;" . $unit . "&gt;";	
					}
					$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I"); 

					$isImportSuccess = true;
				}
				else
				{
					$isImportSuccess = true;
					$m_TraceDebugInfo .= "No records to be updated for unit : <" . $unit . ">";
					$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"E");
				}
			}
		}

		if(!$isImportSuccess)
		{
			$m_TraceDebugInfo = "Member Details not updated";
			$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
		}	
						
		$errormsg="[End of Members Data]";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg);

		header("Location: " . $actionPage);		
	}

	function validateDate($date)
	{
		return date_format(new DateTime($date), 'Y-m-d');
	}

	function getDateTime()
	{
		$dateTime = new DateTime();
		$dateTimeNow = $dateTime->format('Y-m-d H:i:s');
		return $dateTimeNow;
	}

	public function InsertOrUpdateCoOwners($member_id, $owner_names, $mobile, $email)
	{
		$ShowDebugTrace = 0;
		echo "<BR>owner_names : " . $owner_names;
		if($ShowDebugTrace ==1)
		{
			echo "<BR><BR><BR>Inside InsertOrUpdateCoOwners<BR>";
			//echo "<BR>member_id : " . $member_id;
			echo "<BR>owner_names : " . $owner_names;
		}
		$owner = str_replace('&', ',', $owner_names);
		//$owner = str_replace('/', ',', $owner);
		$owner = str_replace(' AND ', ',', $owner);
		$owner_coll = explode(',', $owner);
		
		$New_count = sizeof($owner_coll);
		if($ShowDebugTrace == 1)
		{
			echo "<BR>New_count :" . $New_count;
			echo "<BR>owner_col " ;
			print_r($owner_coll);
		}
		$updatePrimaryOwner = "Update `member_main` SET `primary_owner_name` = '" . trim($owner_coll[0]) . "'
		 WHERE `member_id` = '" . $member_id . "'";
		$resPrimaryOwner = $this->m_dbConn->update($updatePrimaryOwner);

		if($ShowDebugTrace == 1)
		{
			echo "<BR>updatePrimaryOwner " . $updatePrimaryOwner;
		}
///Update member_other_family
		$sql = "SELECT * FROM mem_other_family WHERE member_id =".$member_id;
		$memberExists = $this->m_dbConn->select($sql);
		
		
		$existing_count = sizeof($memberExists);
		if($ShowDebugTrace == 1)
		{
			echo "<BR>existing_count :" . $existing_count;
		}
		//var_dump($memberExists);

		if($New_count == $existing_count)
		{
			//if($ShowDebugTrace ==1)
			{
				echo "<BR>Existing count = New count";
			}
			for($i = 0; $i < sizeof($owner_coll); $i++)
			{
				if($ShowDebugTrace == 1)
				{
					echo "<BR>Owner " . $owner_coll[$i] . " and exist member " . $memberExists[$i]['mem_other_family_id'];
				}
				$updateCoOwner = "UPDATE mem_other_family SET other_name = '".trim($owner_coll[$i])."'  WHERE mem_other_family_id =".$memberExists[$i]['mem_other_family_id'];
				if($ShowDebugTrace == 1)
				{	
					echo "<BR>Owner " . $owner_coll[$i] . " and exist member " . $memberExists[$i]['mem_other_family_id'];	
					echo "<BR>Update query " . $updateCoOwner;
				}
				$this->m_dbConn->update($updateCoOwner);					
			}		
		}
		else if($New_count > $existing_count)
		{
			//if($ShowDebugTrace ==1)
			if($existing_count ==1)
			{
				for($i = 0; $i < sizeof($owner_coll); $i++)
				{
					if ($i == 0)
					{
						if($ShowDebugTrace == 1)
						{
							echo "<BR>First member";
							echo "<BR>Owner " . $owner_coll[$i] . " and exist member " . $memberExists[$i]['mem_other_family_id'];
						}
						$updateCoOwner = "UPDATE mem_other_family SET other_name = '".trim($owner_coll[$i])."'  WHERE mem_other_family_id =".$memberExists[$i]['mem_other_family_id'];
						echo "<BR>Update query " . $updateCoOwner;
						$this->m_dbConn->update($updateCoOwner);
					}
					else 
					{
						if($ShowDebugTrace == 1)
						{
							echo "<BR>More members" . $i;
						}
				
						$insertCoOwner = "INSERT INTO `mem_other_family` (`member_id`, `other_name`, `coowner`) VALUES ('" . $member_id . "', '" . trim($owner_coll[$i]) . "', '2')";
						echo "<BR>insertCoOwner query " . $insertCoOwner;
						$resCoOwner = $this->m_dbConn->insert($insertCoOwner);
					}
					if($ShowDebugTrace == 1)
					{	
						echo "<BR>Owner " . $owner_coll[$i] . " and exist member " . $memberExists[$i]['mem_other_family_id'];					
					}		
				}
			}	
			else
			{
				echo "<BR><BR>New_count $New_count > existing_count $existing_count Not supported";
				//Send update back to user
				return false;
			}				
		}
		else //($New_count < $existing_count)
		{
			
			//if($ShowDebugTrace ==1)
			{
			echo "<BR><BR>New_count $New_count < existing_count $existing_count ";
			}
		
			//Iterate over existing member line records and update new names and delete remaining
			for($i = 0; $i < sizeof($memberExists); $i++)
			{
				if ($i == 0)
				{
					if($ShowDebugTrace == 1)
					{
					echo "<BR>First member";
					echo "<BR>Owner " . $owner_coll[$i] . " and exist member " . $memberExists[$i]['mem_other_family_id'];
					}
					$updateCoOwner = "UPDATE mem_other_family SET other_name = '".trim($owner_coll[$i])."'  WHERE mem_other_family_id =".$memberExists[$i]['mem_other_family_id'];
					echo "<BR>Update query " . $updateCoOwner;
					$this->m_dbConn->update($updateCoOwner);

					// var_dump($owner_coll);
					// die();
					//$updateCoOwner = "UPDATE mem_other_family SET other_name = '".trim($owner_coll[$i])."'  WHERE mem_other_family_id =".$memberExists[$i]['mem_other_family_id'];
				}
				else 
				{
					if($ShowDebugTrace == 1)
					{
					echo "<BR>More members" . $i;
					
					}
			
						
$DeleteQuery = "DELETE FROM `mem_other_family` WHERE mem_other_family_id =".$memberExists[$i]['mem_other_family_id'];
	 
					echo "<BR>Delete query " . $DeleteQuery;
					$this->m_dbConn->delete($updateCoOwner);
					
					// var_dump($updateCoOwner);
					// die();
				}
				if($ShowDebugTrace == 1)
				{	
				echo "<BR>Owner " . $owner_coll[$i] . " and exist member " . $memberExists[$i]['mem_other_family_id'];
				
				}		
			}			
		}
	}

	private function UpdateCoOwners($member_id, $owner_names, $mobile, $email)
	{
		$owner = str_replace('&', ',', $owner_names);
		//$owner = str_replace('/', ',', $owner);
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
}

?>
<?php
include_once("include/dbop.class.php");
include_once("utility.class.php");
include_once("CsvOperations.class.php");
include_once("dbconst.class.php");
include_once("include/fetch_data.php");

$dbConn = new dbop();

$obj_fetch = new FetchData($dbConn);

//echo "ash";
		//$a = $obj_fetch->GetSocietyDetails($_SESSION['society_id']);
		//echo $obj_fetch->objSocietyDetails->sSocietyCode;
		
class member_import 
{
	public $allColumns = array('BCode', 'WCode', 'FCode','FloorNo', 'Owner','Occupation', 
			'DateOfBirth', 'AnnivarsaryDate', 'Gender', 'BloodGroup', 'OwnerAddress','FlatArea', 'GSTINNO', 'ResPhone', 'MobileNo','OffPhone', 'EMail', 'Email1', 'DisposeDate', 'Inactive', 'EmergencyPersonName','EmergencyMobileNo', 'EmergencyTelephoneNo','ParkingNo','FlatConfiguration','ExtnLedgerName');

	public $ci = array();
	const BCODE = 0;
	const WCODE = 1;
	const FCODE = 2;
	const FLOOR_NO = 3;
	const OWNER= 4;
	const OCCUPATION = 5;
	const DOB = 6;
	const WED_DATE = 7;
	const GENDER = 8;
	const BLOOD_GROUP = 9;
	const ADDRESS = 10;
	const AREA=11;
	const GSTIN = 12;
	const RES_PHONE = 13;
	const MOBILE = 14;
	const OFFICE_PHONE = 15;
	const EMAIL = 16;
	const ALT_EMAIL = 17;
	const DISPOSE_DATE = 18;
	const INACTIVE = 19;
	const EMER_NAME = 20;
	const EMER_MOBILE = 21;
	const EMER_TEL = 22;
	const PARKING_NO = 23;
	const FLAT_CONFIGURATION = 24;
	const ExtnLedgerName = 25;
	
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
								$FloorNo=array_search(FloorNo,$row,true);
								$FlatArea=array_search(FlatArea,$row,true);
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
								$Parking_No=array_search(ParkingNo,$row,true);
								$GSTINNO=array_search(GSTINNO,$row,true);
								$BikeParkingNo=array_search(BikeParkingNo,$row,true);
								$FlatConfiguration=array_search(FlatConfiguration,$row,true);
							    $ExtnLedgerName=array_search(ExtnLedgerName,$row,true);
							
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
								$parking_no=$row[$Parking_No];
								$car_parking_no=$row[$CarParkingNo];
								$gstin_no=$row[$GSTINNO];				
								$bike_parking_no=$row[$BikeParkingNo];	
								$flat_configuration=$row[$FlatConfiguration];
								$extnledgername=$row=[$ExtnLedgerName];

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
		$file = fopen($file,"r");
		
				foreach ($array as $key => $row) {
					# code...
				// }
				// while (($row = $array) !== FALSE)
				// {
			
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
								$FloorNo=array_search(FloorNo,$row,true);
								$FlatArea=array_search(FlatArea,$row,true);
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
								$Parking_No=array_search(ParkingNo,$row,true);
								$GSTINNO=array_search(GSTINNO,$row,true);
								$BikeParkingNo=array_search(BikeParkingNo,$row,true);
								$FlatConfiguration=array_search(FlatConfiguration,$row,true);
							    $ExtnLedgerName=array_search(ExtnLedgerName,$row,true);
							    

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
								if($BCode !== FALSE){
									//echo "hello";
									$society_code=$row[$BCode];
								}
								if($wing_code !== FALSE){
									//echo "hello";
									$wing_code=$row[$WCode];
								}
								if($FCode !== FALSE){
									//echo "hello";
									$unit_no=$row[$FCode];
								}if($FloorNo !== FALSE){
									//echo "hello";
									$floor_no=$row[$FloorNo];
								}
								
								if($Owner !== FALSE){
									//echo "hello";
									$owner_name= $this->m_dbConn->escapeString($row[$Owner]);
								}
								if($Gender !== FALSE){
									//echo "hello";
									$gender=$row[$Gender];
								}
								if($FlatArea !== FALSE){
									//echo "hello";
									$area=$row[$FlatArea];
								}
								if($MobileNo !== FALSE){
									//echo "hello";
									$mob=$row[$MobileNo];
								}
								if($OffPhone !== FALSE){
									//echo "hello";
									$off_no=$row[$OffPhone];
								}
								if($Occupation !== FALSE){
									//echo "hello";
									$desg=$row[$Occupation];
								}
								if($EMail !== FALSE){
									//echo "hello";
									$email=$row[$EMail];
								}
								if($DateOfBirth !== FALSE){
									//echo "hello";
									$dob=$row[$DateOfBirth];
								}
								if($AnnivarsaryDate !== FALSE){
									//echo "hello";
									$wed_any=$row[$AnnivarsaryDate];
								}
								if($EmergencyPersonName !== FALSE){
									//echo "hello";
									$eme_rel_name=$row[$EmergencyPersonName];
								}
								if($EmergencyMobileNo !== FALSE){
									//echo "hello";
									$eme_contact_1=$row[$EmergencyMobileNo];
								}
								if($EmergencyTelephoneNo !== FALSE){
									//echo "hello";
									$eme_contact_2=$row[$EmergencyTelephoneNo];
								}
								if($EmergencyPersonName !== FALSE){
									//echo "hello";
									$eme_rel_name=$row[$EmergencyPersonName];
								}
								if($Inactive !== FALSE){
									//echo "hello";
									$owner_inactive=$row[$Inactive];
								}
								if($DisposeDate !== FALSE){
									//echo "hello";
									$despdate=$row[$DisposeDate];
								}
								if($Parking_No !== FALSE){
									//echo "hello";
									$parking_no=$row[$Parking_No];
								}
								if($CarParkingNo !== FALSE){
									//echo "hello";
									$car_parking_no=$row[$CarParkingNo];
								}
								if($GSTINNO !== FALSE){
									//echo "hello";
									$gstin_no=$row[$GSTINNO];
								}
								if($BikeParkingNo !== FALSE){
									//echo "hello";
									$bike_parking_no=$row[$BikeParkingNo];
								}
								
								
								if($FlatConfiguration !== FALSE){
								
									$flat_configuration=$row[$FlatConfiguration];
								}
								
								if($ExtnLedgerName !== FALSE){
									//echo "hello";
									$extnledgername=$row[$ExtnLedgerName];	
								}
								
								
//$m_TraceDebugInfo = "<br><br>Got the columns to be updated!... Column Names are: ". $BCode. " ". $WCode. " " .$FCode. " ".$FloorNo. " " . $Owner. " ". $Occupation. " " . $DateOfBirth . " " . $AnnivarsaryDate. " ". $Gender . " " .$BloodGroup. " " . $OwnerAddress. " " . $BikeParkingNo. " " . $CarParkingNo . " ".$FlatArea." " . $GSTINNO. " " . $ResPhone. " " .$MobileNo . " " . $OffPhone. " " . $EMail. " " . $Email1. " " . $DisposeDate. " " . $Inactive. " " .$EmergencyMobileNo. " " . $EmergencyPersonName. " " . $EmergencyTelephoneNo."   ".$Parking_No. "  ".$FlatConfiguration." ".$ExtnLedgerNameIndex."";


	// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
		
		// $rowCount = 0;
     
		if($owner_name != '')
			{
				$m_TraceDebugInfo .= "Owner: &lt;" . $owner_name . "&gt;";	
			}
			
			// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			
			
			if ($desg != '') 
			{
				$m_TraceDebugInfo .= "Occupation: &lt;" . $desg . "&gt;";
				$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}
			
		
			if ($area != '') 
			{
				$m_TraceDebugInfo .= "Flat Area: &lt;" . $area . "&gt;";
				//$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}
			if ($floor_no != '') 
			{
				$m_TraceDebugInfo .= "Floor No: &lt;" .$floor_no. "&gt;";
				//$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}
		
			if ($dob != '')
			{
				$m_TraceDebugInfo .= "DOB: &lt;" . $dob . "&gt;";
				$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			
			if ($wed_any != '') 
			{
				$m_TraceDebugInfo .= "Wedding Date: &lt;" . $wed_any . "&gt;";
				 $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

		
			if ($gender != '') 
			{
				$m_TraceDebugInfo .= "Gender: &lt;" . $gender . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			
			if ($blood_group != '') 
			{
				$m_TraceDebugInfo .= "Blood Group: &lt;" . $blood_group."&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			
			if ($gstin_no != '')
			{
				$m_TraceDebugInfo .= "GSTIN: &lt;" . $gstin_no . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			
			if($res_phone != '')
			{
				$m_TraceDebugInfo .= "Residence Phone: &lt;" . $res_phone . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

		
			if ($mob != '')
			{
				$m_TraceDebugInfo .= "Mobile: &lt;" . $mob . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			
			if ($off_no != '') 
			{
				$m_TraceDebugInfo .= "Office Number: &lt;" . $off_no . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

		
			// echo $email;
			if ($email != '')
			{
				$m_TraceDebugInfo .= "Email: &lt;" . $email . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			
			if ($alt_email != '')
			{
				$m_TraceDebugInfo .= "Alternate Email: &lt;" . $alt_email . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			
			if ($despdate != '')
			{
				$m_TraceDebugInfo .= "Dispose Date: &lt;" . $despdate . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

		
			if ($owner_inactive != '')
			{
				$m_TraceDebugInfo .= "Inactive: &lt;" . $owner_inactive . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

		
			if ($eme_rel_name != '')
			{
				$m_TraceDebugInfo .= "Emergency Person Name: &lt;" . $eme_rel_name . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

		
			if ($eme_contact_1 != '')
			{
				$m_TraceDebugInfo .= "Emergency Contact 1: &lt;" . $eme_contact_1 . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}

			
			if ($eme_contact_2 != '')
			{
				$m_TraceDebugInfo .= "Emergency Contact 2: &lt;" . $eme_contact_2 . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}
			
			
			
			if ($parking_no != '')
			{
				$m_TraceDebugInfo .= "Parking No : &lt;" . $parking_no . "&gt;";
				// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}
			// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");

			
			if ($floor != '') 
			{
				$m_TraceDebugInfo .= "Floor Number: &lt;" . $floor . "&gt;";
				//$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}
			
				
			if ($flat_configuration != '') 
			{
					$m_TraceDebugInfo .= "Flat Configuration : &lt;" . $flat_configuration . "&gt;";
					//$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			}
	
			 	
			if ($extnledgername != '') 
			{
				$m_TraceDebugInfo .= "ExtnLedgerName : &lt;" .$extnledgername . "&gt;";
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
				
				if(array_search($FlatArea,$indexes))
				{
		
				if (isset($FlatArea))
				{
					
					if ($area != '')
					{
						if($this->obj_utility->isNumeric($area))
						{
							$sql1 = "Update `unit` set `area`='".$area."' where `unit_id`='".$unit."'";
							$this->m_dbConn->update($sql1);
                              
							$m_TraceDebugInfo .= "Updated Area where Flat No: <" .$unit_no. "><br>";
						}// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
						else
						{
							$m_TraceDebugInfo.="Invalid Flat Area Format &lt;".$area."&gt;";
						}
					}

				}
			}

				
				
			if(array_search($FlatConfiguration,$indexes))
            {
	
				if (isset($FlatConfiguration))
				{
					if ($flat_configuration != '')
					{
					   // FlatConfiguration to be Updated";
				
						   echo $sql2 = "Update `unit` set `flat_configuration`='".$flat_configuration."' Where `unit_id`='".$unit."'";
							$this->m_dbConn->update($sql2);
						
						    $m_TraceDebugInfo.= "Updated flat_configuration where Flat No: <" .$unit_no. "><br>";
					
					}
					else{
						  $m_TraceDebugInfo.="Invalid Floor Number Format &lt;".$flat_configuration."&gt;";

					}
					
				}
			}	
			
				if(array_search($FloorNo,$indexes))
				{
		
				if (isset($FloorNo))
				{
					//Floor Number to be Updated
					if ($floor_no != '')
					{
						if($this->obj_utility->isNumeric($floor_no))
						{
							$sql1 = "UPDATE unit SET floor_no ='".$floor_no."' WHERE unit_id='".$unit."'";
							$this->m_dbConn->update($sql1);
							
							$m_TraceDebugInfo .= "Updated Floor No where Flat No: <" .$unit_no. "><br>";
						}// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
						else
						{
							$m_TraceDebugInfo.="Invalid Floor Number Format &lt;".$floor_no."&gt;";
						}
					}
				}
				}

				                                                                                           
				if(array_search($Owner,$indexes))
				{
		
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

						// if($this->InsertOrUpdateCoOwners($member_id[0]['member_id'], $owner_name, $mob, $email) == true)
						// {
						// 	$m_TraceDebugInfo .= "Updated Owners Where Flat No: <" . $unit . ">";
						// }
						// else
						// {
						// 	$m_TraceDebugInfo .= "Error Updating Owners Where Flat No: <" . $unit . ">";
						// }
						// $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
					}
				}
			}
				
			if(array_search($ExtnLedgerName,$indexes))
            {
	
				if (isset($ExtnLedgerName))
				{
				
					//extn_ledger_name to be Updated
					if ($extnledgername != '')
					{
						 //$sql2 = "Update `unit` set `extn_ledger_name`='".$extnledgername."' Where `unit_id`='".$unit."'";
						//$this->m_dbConn->update($sql2);
						$sql1 = "UPDATE member_main set `extn_ledger_name`='".$extnledgername."' WHERE unit='".$unit."'";
						$this->m_dbConn->update($sql1);
					
						$m_TraceDebugInfo.= "Updated external ledger where Flat No: <" .$unit_no. "><br>";
				
				}
				else{
					  $m_TraceDebugInfo.="Invalid external ledger &lt;".$extnledgername."&gt;";

				}
				}
		
			}

		
				if(array_search($Parking_No,$indexes))
            {
	
				if (isset($Parking_No))
				{
					//Parking is to updated	
					
					if ($parking_no != '')
					{
						
						$sql1 = "UPDATE member_main set `parking_no`='".$parking_no."' WHERE unit='".$unit."'";
						$this->m_dbConn->update($sql1);
					
						$m_TraceDebugInfo.= "Updated parking no where Flat No: <" .$unit_no. "><br>";
				
				}
				
				}
             }  
			
			 
			 if(array_search($Occupation,$indexes))
			 {
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
			}		

			
			if(array_search($DateOfBirth,$indexes))
            {
	
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
			}
				
				if(array_search($AnnivarsaryDate,$indexes))
            {
		
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
			}


			if(array_search($Gender,$indexes))
            {
	
				if (isset($Gender))
				{
					//Gender is to updated		
					if ($gender != '')
					{
						
						$sql1 = "UPDATE member_main set `gender`='".$gender."' WHERE unit='".$unit."'";
						$this->m_dbConn->update($sql1);
					
						$m_TraceDebugInfo.= "Updated gender where Flat No: <" .$unit_no. "><br>";
					}
				}
			}

			if(array_search($BloodGroup,$indexes))
            {
				if (isset($BloodGroup))
				{
					//Blood Group is to updated
					if ($blood_group != '')
					{
						$sql1 = "UPDATE member_main set `blood_group`='".$blood_group."' WHERE unit='".$unit."'";
						$this->m_dbConn->update($sql1);
					
						$m_TraceDebugInfo.= "Updated gstn no where Flat No: <" .$unit_no. "><br>";
					}
				}
			}

			if(array_search($OwnerAddress,$indexes))
            {
				if (isset($OwnerAddress))
				{
					//Owner Address is to updated
					if ($owner_address != '')
					{
						
						$sql1 = "UPDATE member_main set `alt_address`='".$owner_address."' WHERE unit='".$unit."'";
						$this->m_dbConn->update($sql1);
					
						$m_TraceDebugInfo.= "Updated address where Flat No: <" .$unit_no. "><br>";
					}
				}
			}	

			if(array_search($GSTINNO,$indexes))
            {
				if (isset($GSTINNO)) 
				{
					// GSTIN Number to be updated
					if ($gstin_no != '')
					{
						
						$sql1 = "UPDATE member_main set `owner_gstin_no`='".$gstin_no."' WHERE unit='".$unit."'";
						$this->m_dbConn->update($sql1);
					
						$m_TraceDebugInfo.= "Updated gstn no where Flat No: <" .$unit_no. "><br>";
				
				}
			
				}
			}	

			if(array_search($ResPhone,$indexes))
            {
				if (isset($ResPhone)) 
				{
					// Residence Number to be updated
					if ($res_phone != '')
					{
						$sql .= "resd_no ='".$res_phone."',";
					}
				}						
			}

			if(array_search($MobileNo,$indexes))
            {
				if (isset($MobileNo)) 
				{
					// Mobile Number to be updated
					if ($mob != '')
					{
						$sql .= "mob =".$mob.",";
						
					
					}
				}	
			}										

			if(array_search($OffPhone,$indexes))
            {
				if (isset($OffPhone)) 
				{
					// Office Number to be updated
					if ($off_no != '')
					{
						$sql .= "off_no =".$off_no.",";
					}
				}
			}	
				// $regexForEmail = "^[a-zA-Z0-9_+&*-]+(?:\\.[a-zA-Z0-9_+&*-]+)*@" + "(?:[a-zA-Z0-9-]+\\.)+[a-zA-Z]{2,7}$";
				
				if(array_search($EMail,$indexes))
				{
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

			}		
				if(array_search($Email1,$indexes))
				{

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

			}
			
			if(array_search($DisposeDate,$indexes))
            {	

				if (isset($DisposeDate))
				{
					//Dispose Date is to be updated
					if ($despdate != '')
					{
						$sql .= "";
					}
				}
			}
			
			if(array_search($Inactive,$indexes))
            {	
				if (isset($Inactive))
				{
					//Inactive Flag is to be updated
					if ($owner_inactive != '')
					{
						$sql .= "";
					}
				}
			}

			if(array_search($EmergencyPersonName,$indexes))
            {
				if (isset($EmergencyPersonName))
				{
					//Emergency Person Name is to be updated
					if ($eme_rel_name != '')
					{ 
						$sql .= "eme_rel_name='".$eme_rel_name."',";
					}
				}	
			}	

			if(array_search($EmergencyMobileNo,$indexes))
            {
				if (isset($EmergencyMobileNo))
				{
					//Emergency Person Mobile Number is to be updated
					if ($eme_contact_1 != '')
					{
						$sql1 = "UPDATE member_main set `eme_contact_1`='".$eme_contact_1."' WHERE unit='".$unit."'";
						$this->m_dbConn->update($sql1);
					
						$m_TraceDebugInfo.= "Updated emergency contact 1 where Flat No: <" .$unit_no. "><br>";
					}
				}

			}	

			if(array_search($EmergencyTelephoneNo,$indexes))
            {
				if (isset($EmergencyTelephoneNo))
				{
					//Emergency Person Telephone is to be updated
					if ($eme_contact_2 != '')
					{
						
						$sql1 = "UPDATE member_main set `eme_contact_2`='".$eme_contact_2."' WHERE unit='".$unit."'";
						$this->m_dbConn->update($sql1);
					
						$m_TraceDebugInfo.= "Updated emergency contact 2 where Flat No: <" .$unit_no. "><br>";
				
				}
					}
				}
			}	

				
			}
		

	
}
}

$sql = rtrim($sql, ",");
				$MemberOtherSql = rtrim($MemberOtherSql, ",");
				
				
				if ($sql != "" || $sql1 != "")
				{
					if($sql != "")
					{
						echo $query = "UPDATE member_main SET ". $sql ." WHERE unit = '".$unit."' and `ownership_status` = 1";
						
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
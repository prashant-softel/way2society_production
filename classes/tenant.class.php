<?php
include_once ("dbconst.class.php");
include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
//echo "dm";
include_once("activate_user_email.class.php");
//echo "dm2";
include_once("../ImageManipulator.php");
include_once("utility.class.php");
include_once("document_maker.class.php");
include_once("latestcount.class.php");

//error_reporting(7);
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
class tenant 
{
	public $actionPage = "../tenant.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_utility;
	public $obj_activation ;
	public $m_bShowTrace;
	public $obj_DocumentMaker;
	
	function __construct($dbConn, $dbConnRoot)
	{
		//echo 'Inside const tenant';
		$this->display_pg=new display_table();
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_bShowTrace = 0;
		$this->obj_utility=new utility($this->m_dbConn, $this->m_dbConnRoot);
		$this->obj_DocumentMaker = new doc_templates($this->m_dbConn, $this->m_dbConnRoot);
		$this->obj_activation = new activation_email($dbConn, $dbConnRoot);
	}
	public function addServiceRequest($srTitle,$srPriority,$srCategory,$unitId,$loginId,$societyId)
	{
		$obj_LatestCount = new latestCount($this->m_dbConn);
		$request_no = $obj_LatestCount->getLatestRequestNo($societyId);
		//echo "request_no : ".$request_no;
		$sqlName = "Select `member_id`,`name` from login where login_id = '".$loginId."';";
		$sqlName_res = $this->m_dbConnRoot->select($sqlName);
		//var_dump($sqlName_res);
		$sql4 = "SELECT u.`unit_no`, mm.`primary_owner_name`, w.`wing`,mm.`mob` FROM `unit` u, `member_main` mm, `wing` w WHERE u.`unit_id` = mm.`unit` AND w.`wing_id` = mm.`wing_id` AND mm.`ownership_status` = '1' AND u.`unit_id` = '".$unitId."'";
		$sql4_res = $this->m_dbConn->select($sql4);
		$summery = "This is Leave & License NOC request.";
		 $sqlsr = "INSERT INTO `service_request` (`request_no`, `society_id`, `reportedby`, `dateofrequest`, `email`, `phone`, `priority`, `category`, `summery`,`img`, `details`, `status`, `unit_id`) VALUES ('".$request_no."', '".$_SESSION['society_id']."', '".$sqlName_res[0]['name']."', '".getDBFormatDate(date('d-m-Y'))."', '".$sqlName_res[0]['name']."', '".$sql4_res[0]['mob']."', '".$srPriority."', '".$srCategory."', '".$srTitle."','','".$summery."', 'Raised', '".$unitId."')";
		$sqlsr_res = $this->m_dbConn->insert($sqlsr);
		return($sqlsr_res);
	}
	public function startProcess()
	{
		$errorExists = 0;
		$unitId = $_POST['unit_id']; 
		$resUnit = $this->obj_utility->GetUnitDesc($unitId);
		$unitNo = $resUnit[0]["unit_no"];
		if($this->m_bShowTrace)
		{
			echo "unitid:".$unitId."no".$unitNo;
			print_r($resUnit);
		}
		$ResDocTypes = $this->m_dbConn->select("select ID from document_type where doc_type='Lease'");
		//print_r($ResDocTypes);
		$doc_type = "";
		if(isset($ResDocTypes[0]["ID"]))
		{
			$doc_type = $ResDocTypes[0]["ID"];
		}
		else
		{
			echo "<br>Lease Document type not found";
		}		
				
		if($_REQUEST['insert']=='Submit' && $errorExists==0)
		{
			$srResult = 0;
			//echo "sizeof(serviceRequestDetails) : ".sizeof($_SESSION['serviceRequestDetails']);
			if($_POST['actionPage2'] == "serviceRequest")
			{
				$srResult = $this->addServiceRequest($_SESSION['serviceRequestDetails']['srTitle'],$_SESSION['serviceRequestDetails']['priority'],$_SESSION['serviceRequestDetails']['category'],$_SESSION['TENANT_REQUEST_ID'],$_SESSION['serviceRequestDetails']['unit_no'],$_SESSION['login_id'],$_SESSION['society_id']);
			}
			$start=getDBFormatDate($_POST['start_date']);
			$end=getDBFormatDate($_POST['end_date']);
			$today = date("Y-m-d");    // 2018-01-20
			$total_month = $_POST["Lease_Period"];
			$p_verification = $_POST['pVerified'];
			$leaveAndLicenseAgreement = $_POST['leaveAndLicenseAgreement'];
			$members=$_POST['count'];
			$insert_query="insert into `tenant_module` (`serviceRequestId`,`doc_id`,`unit_id`,`tenant_name`,tenant_MName,tenant_LName,`mobile_no`,`email`,`dob`,`agent_name`,`agent_no`,`members`,`create_date`,`start_date`,`end_date`, `total_month`, `note`,`ApprovalLevel`) values ('".$srResult."','0','".$unitId."','".$_POST['t_name']."','".$_POST['t_mname']."','".$_POST['t_lname']."','".$_POST['contact_1']."','".$_POST['email_1']."','".getDBFormatDate($_POST['mem_dob_1'])."','".$_POST['agent']."','".$_POST['agent_no']."','1','".date('Y-m-d')."','".$start."','".$end."', '".$total_month."', '".$_POST['note']."','".$this->getApprovalLevel()."')";
			//$insert_query;
			$result=$this->m_dbConn->insert($insert_query);
			//Inserting renovation details in approval_details table..
			$sql7 = "insert into `approval_details` (`referenceId`, module_id) values ('".$result."','".TENANT_SOURCE_TABLE_ID."');";
			$sql7_res = $this->m_dbConn->insert($sql7);
			if($_POST['verified'] == 1) // If Verified checkbox is check then only update
			{
				$this->updateTenantVerificationStatus($result, $p_verification, $leaveAndLicenseAgreement, $total_month);
			}
			//-------------------------------------------------Code to upload profile image of the tenant--------------------------------------------------//
			$photoFile = $_FILES['profilePhoto'];
			
			//var_dump($photoFile);
			$str = $unitNo ."//Lease//".$start;
			if($this->m_bShowTrace)
			{
				echo "path:".$str;
			}
			$parts = explode("//", $str);
			$fileName = "";
			$doc_id = array();				
			$doc=$_POST['doc_count'];
			
			$finalMemberCount = $members;
			$todaysDate = date('Y-m-d');         		                    
			//----------------------------------------------------G_Drive Code----------------------------------------------------------------------
			//----------------------------------------------------Uncomment below code for gDrive---------------------------------------------------//
			
			/*$resResponse = $this->obj_utility->UploadAttachment($photoFile,  $doc_type, $todaysDate, "Uploaded_Documents", $i, true, $UnitNo);
			echo "<br>Profile Image Result:";
			var_dump($resResponse);
			$sStatus = $resResponse["status"];
			$sMode = $resResponse["mode"];
			$sFileName = $resResponse["response"];
			$sUploadFileName = $resResponse["FileName"];
			if($sMode == "1")
			{
				$random_name = $sFileName;
			} 
			else if($sMode == "2")
			{
				$docGDriveID = $sFileName;
			}
			else
			{
			}
			$sDocVersion = '2';
			if($GdriveDocID != "")
			{
				$sDocVersion = '2';
			}
			$sDocVersion = '2';
			if($GdriveDocID != "")
			{
				$sDocVersion = '2';
			}*/
			$fileName = "tenantProfile_".$unitNo."_".$result."_".basename($photoFile['name']);
			//echo "HTTP POST : ".$_SERVER['HTTP_HOST'];
			if($_SERVER['HTTP_HOST'] == "localhost" )
			{		
				$uploaddir = $_SERVER['DOCUMENT_ROOT']."/beta_aws_master/Uploaded_Documents";			   
			}
			else
			{
				$uploaddir = $_SERVER['DOCUMENT_ROOT']."/Uploaded_Documents";			   
			}
			$uploadfile = $uploaddir ."/". $fileName;	
			//echo "filename : ".$uploadfile."<br/>";
			$fileResult = move_uploaded_file($photoFile['tmp_name'], $uploadfile);
			//echo "fileResult : ".$fileResult;
			if($fileResult)
			{
				$updateProfileSql = "update tenant_module set `img` = '".$fileName."' where tenant_id = '".$result."';";
				$updateResult = $this->m_dbConn->update($updateProfileSql);
			}
			//echo 
			
			//------------------------------------------------Storing Vehicle details in Database----------------------------------------------------------//
		
			//$RefId=$result;
			$tenantId = $result;
			$vehicleCount = (int)$_POST['vehiclecount'];
			for($i = 1;$i <= $vehicleCount;$i++)
			{
				$regNo = $_POST['carRegNo_'.$i];
				$owner = $_POST['carOwner_'.$i];
				$make = $_POST['carMake_'.$i];
				$model = $_POST['carModel_'.$i];
				$vehicleType = $_POST['vehicleType_'.$i];
				$color = $_POST['carColor_'.$i];
				$parkingType = $_POST['parkingType_'.$i];
				$parking_slot = $_POST['parkingSlot_'.$i];
				$parking_sticker = $_POST['parkingSticker_'.$i];
				
				if($vehicleType == 2)//BIKE
				{
					$sqlBike = "select count(*)as cnt from mem_bike_parking where member_id='".$tenantId."' and bike_reg_no='".addslashes(trim(strtoupper($regNo)))."' and status='Y' and bike_type='1'";
					$resBike = $this->m_dbConn->select($sqlBike);
					if($resBike[0]['cnt']==0)
					{
						$insert_queryBike = "insert into mem_bike_parking (`member_id`,`ParkingType`,`parking_slot`,`bike_reg_no`,`bike_owner`,`bike_model`,`bike_make`,`bike_color`,`parking_sticker`,`bike_type`) values ('".$tenantId."','".$parkingType."','".addslashes(trim(ucwords($parking_slot)))."','".addslashes(trim(strtoupper($regNo)))."','".addslashes(trim(ucwords($owner)))."','".addslashes(trim(ucwords($model)))."','".addslashes(trim(ucwords($make)))."','".addslashes(trim(ucwords($color)))."','".addslashes(trim(ucwords($parking_sticker)))."','1')";
						$dataBike = $this->m_dbConn->insert($insert_queryBike);
					}
					else
					{
					}
				}
				if($vehicleType == '4')//CAR
				{
					$sqlCar = "select count(*)as cnt from mem_car_parking where member_id='".$tenantId."' and car_reg_no='".$regNo."' and status='Y' and car_type='1'";
					$resCar = $this->m_dbConn->select($sqlCar);
					if($resCar[0]['cnt']==0)
					{
						$insert_query="insert into mem_car_parking (`member_id`,`ParkingType`,`parking_slot`,`car_reg_no`,`car_owner`,`car_model`,`car_make`,`car_color`,`parking_sticker`,`car_type`) values ('".$tenantId."','".$parkingType."','".addslashes(trim(ucwords($parking_slot)))."','".addslashes(trim(strtoupper($regNo)))."','".addslashes(trim(ucwords($owner)))."','".addslashes(trim(ucwords($model)))."','".addslashes(trim(ucwords($make)))."','".addslashes(trim(ucwords($color)))."','".addslashes(trim(ucwords($parking_sticker)))."','1')";
						$data = $this->m_dbConn->insert($insert_query);
					}
					else
					{
					}
				}
			}
			//---------------------------------------------------Uploading tenant documents-------------------------------------------------------------//
			for($i=1; $i<=$doc; $i++)
			{
				$PostDate = $_POST['start_date'];         		                    
				$docGDriveID = "";
				$random_name = "";
				$doc_name = "";
				$doc_name = $_POST["doc_name_".$i];
				//echo "Doc Name : ".$doc_name;
				
				//---------------------------------------------Saving file on server code-------------------------------------------------------//
				//--------------------------------------------If G Drive code doesn't Work Uncommented Below code-----------------------------------//
				
				$fileName = "tenant_".$unitNo."_".$result."_".basename($_FILES['userfile'.$i]['name']);
				//echo " fileName: ".$fileName;
				if($_SERVER['HTTP_HOST'] == "localhost" )
				{		
					$uploaddir = $_SERVER['DOCUMENT_ROOT']."/beta_aws_master/Uploaded_Documents";			   
				}
				else
				{
					$uploaddir = $_SERVER['DOCUMENT_ROOT']."/Uploaded_Documents";			   
				}
				$uploadfile = $uploaddir ."/". $fileName;	
				//echo "<br>filename : ".$uploadfile."<br/>";
				$fileResult = move_uploaded_file($_FILES['userfile'.$i]['tmp_name'], $uploadfile);
				//echo "<br>fileResult : ".$fileResult;
				if($fileResult)
				{
					$insert_query="insert into `documents` (`Name`, `Unit_Id`,`refID`,`Category`, `Note`,`Document`,`source_table`,`doc_type_id`,`doc_version`,`attachment_gdrive_id`) values ('".$doc_name."', '".$unitId."','".$result."','0','','".$fileName."','1','".$doc_type."','1','')";
					$data=$this->m_dbConn->insert($insert_query);
				}
				
				
				//------------------------------------G Drive Code-------------------------------------------------------------------------//
				
				/*$resResponse = $this->obj_utility->UploadAttachment($_FILES,  $doc_type, $PostDate, "Uploaded_Documents", $i, true, $UnitNo);
				$sStatus = $resResponse["status"];
				$sMode = $resResponse["mode"];
				$sFileName = $resResponse["response"];
				$sUploadFileName = $resResponse["FileName"];
				if($sMode == "1")
				{
					$random_name = $sFileName;
				} 
				else if($sMode == "2")
				{
					$docGDriveID = $sFileName;
				}
				else
				{
				}
				$sDocVersion = '1';
				if($GdriveDocID != "")
				{
					$sDocVersion = '2';
				}
				$insert_query="insert into `documents` (`Name`, `Unit_Id`,`refID`,`Category`, `Note`,`Document`,`source_table`,`doc_type_id`,`doc_version`,`attachment_gdrive_id`) values ('".$doc_name."', '".$unitId."','".$tenantId."','0', '','".$sUploadFileName."','1','2','".$sDocVersion."','".$docGDriveID."')";
				$data=$this->m_dbConn->insert($insert_query);*/
			}
			$chkCreateLogin = 0;
			$CommuEmails = 0;
		   	for($i=1;$i <= $members;$i++)
			{
				$addmembers= $_POST['members_'.($i)];
				$addrelation=$_POST['relation_'.($i)];
				$memDOB=getDBFormatDate($_POST['mem_dob_'.($i)]);
				$number=$_POST['contact_'.($i)];
				$email=$_POST['email_'.($i)];
				if($_POST['chkCreateLogin'] == "1")
				{
					$chkCreateLogin = 1;
					//$this->SendActivationEmail();
				}
				if($_POST['other_send_commu_emails'] == "1")
				{
					$CommuEmails = 1;
				}
				if($i > 1)
				{
					$_POST['other_send_commu_emails'] = 0;
				}
				if($addmembers <> '')
				{
				 	$sqldata="insert into `tenant_member`(`tenant_id`,`mem_name`,`relation`,`mem_dob`,`contact_no`,`email`,`send_act_email`,`send_commu_emails`) values('".$result."','".$addmembers."','".$addrelation."','".$memDOB."','".$number."','".$email."','".$chkCreateLogin."','".$CommuEmails."')";						
				$data=$this->m_dbConn->insert($sqldata);
				}
				else
				{
					$finalMemberCount--;
				}
				//$up_query="update `tenant_module` set  `members`='".$finalMemberCount."' where tenant_id='".$result."'";
				//$data = $this->m_dbConn->update($up_query);
			}
			if($_POST['actionPage2'] == "serviceRequest")
			{
				$tenantName = $_POST['t_name']." ".$_POST['t_mname']." ".$_POST['t_lname'];
				$this->obj_DocumentMaker->getTenantNOC($unitId,$tenantName,$_SESSION['society_id'],$result);
				$this->actionPage = "../document_maker.php?temp=".$_SESSION['TENANT_REQUEST_ID']."&tId=".$result;
			}
			else
			{
				$this->actionPage = "../view_member_profile.php?id=" . $_POST['mem_id'];
			}
			return "Insert";
		}
	
	/*-------------------------------------------------Update---------------------------------------------------------------------------------------------------*/

		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			//echo "<pre>";
			//print_r($_POST);
			//echo "</pre>";
			$start = getDBFormatDate($_POST['start_date']);
			$end = getDBFormatDate($_POST['end_date']);
			$total_month = $_POST["Lease_Period"];
			$Tenant_id = $_POST['tenant_id'];
			$p_verification = $_POST['pVerified'];
			$leaveAndLicenseAgreement = $_POST['leaveAndLicenseAgreement'];
			$members=$_POST['count'];
			//echo "members : ".$members;
			$finalMemberCount = $members;
			$activeStatus = 0;
			if($_POST['tenantAction'] == "ter")
			{
				$activeStatus = 1;
			}
			if(isset($_POST['verified']))
			{
				$activeStatus = 1;
				$this->updateTenantVerificationStatus($Tenant_id, $p_verification, $leaveAndLicenseAgreement, $total_month);
			}
			if(isset($_POST['approved']))
			{
				//$activeStatus = $_POST['approved'];
				$sql1 = "select * from approval_details where referenceId = '".$Tenant_id."' and module_id = '".TENANT_SOURCE_TABLE_ID."';";
				$sql1_res = $this->m_dbConn->select($sql1);
				//var_dump($sql1_res);
				if($sql1_res[0]['firstLevelApprovalStatus'] == 'Y')
				{
					//echo "in if";
					$sql3 = "Select m.`role` from login as l,mapping as m where l.login_id = '".$_SESSION['login_id']."' and l.`current_mapping` = m.`id`";
					$approvedByDesignation = $this->m_dbConnRoot->select($sql3);
					$sql2 = "update approval_details set `secondApprovalById` = '".$_SESSION['login_id']."',`secondApprovalByDesignation`='".$approvedByDesignation[0]['role']."', `secondLevelApprovalStatus` = 'Y' where referenceId = '".$Tenant_id."' and module_id='".TENANT_SOURCE_TABLE_ID."';";
					$sql2_res = $this->m_dbConn->update($sql2);
					$sql7 = "select `serviceRequestId` from tenant_module where tenant_id = '".$Tenant_id."';";
					$sql7_res = $this->m_dbConn->select($sql7);
					$sql8 = "update service_request set `status` = 'Approved' where request_id = '".$sql7_res[0]['serviceRequestId']."';";
					$sql8_res = $this->m_dbConn->update($sql8);
				}
				else
				{
					///echo "in else";
					$sql3 = "Select m.`role` from login as l,mapping as m where l.login_id = '".$_SESSION['login_id']."' and l.`current_mapping` = m.`id`";
					$approvedByDesignation = $this->m_dbConnRoot->select($sql3);
					$sql2 = "update `approval_details` set `firstApprovalById` = '".$_SESSION['login_id']."',`firstApprovalByDesignation`='".$approvedByDesignation[0]['role']."', `firstLevelApprovalStatus` = 'Y' where referenceId = '".$Tenant_id."' and module_id='".TENANT_SOURCE_TABLE_ID."';";
					$sql2_res = $this->m_dbConn->update($sql2);
					$sql7 = "select `serviceRequestId` from tenant_module where tenant_id = '".$Tenant_id."';";
					$sql7_res = $this->m_dbConn->select($sql7);
					$sql8 = "update service_request set `status` = 'Waiting for Approval' where request_id = '".$sql7_res[0]['serviceRequestId']."';";
					$sql8_res = $this->m_dbConn->update($sql8);
				}
			}
			$photoFile = $_FILES['profilePhoto'];
			$resUnit = $this->obj_utility->GetUnitDesc($_POST['unit_id']);
			$unitNo = $resUnit[0]['unit_no'];
			//$resSociety = $this->obj_DocumentMaker->GetGDriveDetails();
			//var_dump($resSociety);
			$memberId = $this->getMemberId($_POST['unit_id']);
			//$sGDrive_W2S_ID = $resSociety["0"]["GDrive_W2S_ID"];
			//echo "Photo Size".sizeof($photoFile);
			if(($photoFile['name']) != "")
			{
				//echo "in if";
				//----------------------------------------------------G_Drive Code----------------------------------------------------------------------
				//----------------------------------------------------Uncomment below code for gDrive---------------------------------------------------//
				
				/*$resResponse = $this->obj_utility->UploadAttachment($photoFile,  $doc_type, $todaysDate, "Uploaded_Documents", $i, true, $UnitNo);
				echo "<br>Profile Image Result:";
				var_dump($resResponse);
				$sStatus = $resResponse["status"];
				$sMode = $resResponse["mode"];
				$sFileName = $resResponse["response"];
				$sUploadFileName = $resResponse["FileName"];
				if($sMode == "1")
				{
					$random_name = $sFileName;
				} 
				else if($sMode == "2")
				{
					$docGDriveID = $sFileName;
				}
				else
				{
				}
				$sDocVersion = '2';
				if($GdriveDocID != "")
				{
					$sDocVersion = '2';
				}
				$sDocVersion = '2';
				if($GdriveDocID != "")
				{
					$sDocVersion = '2';
				}*/
				$fileName = "tenantProfile_".$unitNo."_".$Tenant_id."_".basename($photoFile['name']);
				if($_SERVER['HTTP_HOST'] == "localhost" )
				{		
					$uploaddir = $_SERVER['DOCUMENT_ROOT']."/beta_aws_master/Uploaded_Documents";			   
				}
				else
				{
					$uploaddir = $_SERVER['DOCUMENT_ROOT']."/Uploaded_Documents";			   
				}
				$uploadfile = $uploaddir ."/". $fileName;	
				//echo "filename : ".$uploadfile."<br/>";
				$fileResult = move_uploaded_file($photoFile['tmp_name'], $uploadfile);
				//echo "fileResult : ".$fileResult;
				if($fileResult)
				{
					//echo "in if2";
					$up_query = "update `tenant_module` set `tenant_name` = '".$_POST['t_name']."',`tenant_MName` = '".$_POST['t_mname']."',`tenant_LName` = '".$_POST['t_lname']."' ,`mobile_no` = '".$_POST['contact_1']."' ,`email` = '".$_POST['email_1']."',`dob` = '".getDBFormatDate($_POST['mem_dob_1'])."',`agent_name` = '".$_POST['agent']."',`agent_no` = '".$_POST['agent_no']."',`members` = '".$_POST['count']."',`create_date` = '".date('Y-m-d')."',`start_date` = '".$start."',`end_date`= '".$end."',`note`='".$_POST['note']."',`img` = '".$fileName."' where tenant_id='".$Tenant_id."'";
					//echo "up_query : ".$up_query;
					$data = $this->m_dbConn->update($up_query);
				}
			}
			else
			{
				//echo "in else";
				$up_query="update `tenant_module` set `tenant_name`='".$_POST['t_name']."',`tenant_MName`= '".$_POST['t_mname']."',`tenant_LName` = '".$_POST['t_lname']."',`mobile_no`= '".$_POST['contact_1']."' ,`email`= '".$_POST['email_1']."',`dob`= '".getDBFormatDate($_POST['mem_dob_1'])."',`agent_name` = '".$_POST['agent']."',`agent_no` = '".$_POST['agent_no']."',`members`= '".$_POST['count']."',`create_date` = '".date('Y-m-d')."',`start_date` = '".$start."',`end_date`= '".$end."',`note` = '".$_POST['note']."' where tenant_id='".$Tenant_id."'";
				//echo "up_query : ".$up_query;
				$data = $this->m_dbConn->update($up_query);
			}
			
			$doc_Count = $_POST['doc_count'];				
			$fileName = "";
			for($i = 1; $i <= $doc_Count; $i++)
			{
				$doc_name = $_POST["doc_name_".$i];
				$fileName = "tenant_".$unitNo."_".$result."_".basename($_FILES['userfile'.$i]['name']);
				if($_SERVER['HTTP_HOST'] == "localhost" )
				{		
					$uploaddir = $_SERVER['DOCUMENT_ROOT']."/beta_aws_master/Uploaded_Documents";			   
				}
				else
				{
					$uploaddir = $_SERVER['DOCUMENT_ROOT']."/Uploaded_Documents";			   
				}
				$uploadfile = $uploaddir ."/". $fileName;	
				//echo "filename : ".$uploadfile."<br/>";
				$fileResult = move_uploaded_file($_FILES['userfile'.$i]['tmp_name'], $uploadfile);
				//echo "fileResult : ".$fileResult;
				if($fileResult)
				{
					$insert_query="insert into `documents` (`Name`, `Unit_Id`,`refID`,`Category`, `Note`,`Document`,`source_table`,`doc_type_id`,`doc_version`,`attachment_gdrive_id`) values ('".$doc_name."', '".$unitId."','".$Tenant_id."','0','','".$fileName."','1','".$doc_type."','1','')";
					$data=$this->m_dbConn->insert($insert_query);
				}
				/*if($_FILES['userfile'.$i]['name'] <> "")
				{
					$PostDate = $_POST['start_date'];         		                    
					$docGDriveID = "";
					$random_name = "";
					$doc_name = $_POST["doc_name_".$i];
					$resResponse = $this->obj_utility->UploadAttachment($_FILES,  $doc_type, $PostDate, "Uploaded_Documents", $i, true, $UnitNo);
					$sStatus = $resResponse["status"];
					$sMode = $resResponse["mode"];
					$sFileName = $resResponse["response"];
					$sUploadFileName = $resResponse["FileName"];
					if($sMode == "1")
					{
						$random_name = $sFileName;
					} 
					else if($sMode == "2")
					{
						$docGDriveID = $sFileName;
					}
					else
					{
					}
					$sDocVersion = '1';
					if($GdriveDocID != "")
					{
						$sDocVersion = '2';
					}
				 	$insert_query="insert into `documents` (`Name`, `Unit_Id`,`refID`, `Category`, `Note`,`Document`,`source_table`,`doc_type_id`,`doc_version`,`attachment_gdrive_id`) values ('".$doc_name."', '".$unit_id."','".$Tenant_id."','0', '','".$sUploadFileName."','1','2','".$sDocVersion."','".$docGDriveID."')";
					$doc_id=$this->m_dbConn->insert($insert_query);
				}*/
			}
			$chkCreateLogin = 0;
			$CommuEmails = 0;  
			if($members <>'')
			{
				$del_member = "delete from `tenant_member` where tenant_id='".$Tenant_id."'";
				$del_list = $this->m_dbConn->delete($del_member);
				for($i=1;$i <= $members;$i++)
				{
					$addmembers= $_POST['members_'.($i)];
					$addrelation=$_POST['relation_'.($i)];
					$Memdob=getDBFormatDate($_POST['mem_dob_'.($i)]);
					$number=$_POST['contact_'.($i)];
					$email=$_POST['email_'.($i)];
					if($_POST['chkCreateLogin'] == "1")
					{
						$chkCreateLogin = 1;
					}
					if($_POST['other_send_commu_emails'] == "1")
					{
						$CommuEmails = 1;
					}
				
					if($i > 1)
					{
						$_POST['other_send_commu_emails'] = 0;
					}
				
					if($addmembers <> '')
					{
						$sqldata="insert into `tenant_member`(`tenant_id`,`mem_name`,`relation`,`mem_dob`,`contact_no`,`email`,`send_act_email`,`send_commu_emails`) values('".$Tenant_id."','".$addmembers."','".$addrelation."','".$Memdob."','".$number."','".$email."','".$chkCreateLogin."','".$CommuEmails."')";						
						$data=$this->m_dbConn->insert($sqldata);
					}
					else
					{
						$finalMemberCount--;
					}
				
				}
				$up_query="update `tenant_module` set  `members`='".$finalMemberCount."' where tenant_id='".$Tenant_id."'";
				$data = $this->m_dbConn->update($up_query);
			}
			$vehicleCount = $_POST['vehiclecount'];
			if($vehicleCount <>'')
			{
				$sql1 = "delete from `mem_car_parking` where `member_id`='".$Tenant_id."' and `car_type` = '1'";
				$sql1_res = $this->m_dbConn->delete($sql1);
				$sql2 = "delete from `mem_bike_parking` where `member_id`='".$Tenant_id."' and `bike_type` = '1'";
				$sql2_res = $this->m_dbConn->delete($sql2);
				for($i=1;$i <= $vehicleCount;$i++)
				{
					$regNo = $_POST['carRegNo_'.$i];
					$owner = $_POST['carOwner_'.$i];
					$make = $_POST['carMake_'.$i];
					$model = $_POST['carModel_'.$i];
					$vehicleType = $_POST['vehicleType_'.$i];
					$color = $_POST['carColor_'.$i];
					$parkingType = $_POST['parkingType_'.$i];
					$parking_slot = $_POST['parkingSlot_'.$i];
					$parking_sticker = $_POST['parkingSticker_'.$i];
					if($vehicleType == 2)//BIKE
					{
						$insert_queryBike = "insert into mem_bike_parking (`member_id`,`ParkingType`,`parking_slot`,`bike_reg_no`,`bike_owner`,`bike_model`,`bike_make`,`bike_color`,`parking_sticker`,`bike_type`) values ('".$Tenant_id."','".$parkingType."','".addslashes(trim(ucwords($parking_slot)))."','".addslashes(trim(strtoupper($regNo)))."','".addslashes(trim(ucwords($owner)))."','".addslashes(trim(ucwords($model)))."','".addslashes(trim(ucwords($make)))."','".addslashes(trim(ucwords($color)))."','".addslashes(trim(ucwords($parking_sticker)))."','1')";
						$dataBike = $this->m_dbConn->insert($insert_queryBike);
					}
					if($vehicleType == '4')//CAR
					{
						$insert_query="insert into mem_car_parking (`member_id`,`ParkingType`,`parking_slot`,`car_reg_no`,`car_owner`,`car_model`,`car_make`,`car_color`,`parking_sticker`,`car_type`) values ('".$Tenant_id."','".$parkingType."','".addslashes(trim(ucwords($parking_slot)))."','".addslashes(trim(strtoupper($regNo)))."','".addslashes(trim(ucwords($owner)))."','".addslashes(trim(ucwords($model)))."','".addslashes(trim(ucwords($make)))."','".addslashes(trim(ucwords($color)))."','".addslashes(trim(ucwords($parking_sticker)))."','1')";
						$data = $this->m_dbConn->insert($insert_query);
					}				
				}
				$up_query="update `tenant_module` set  `members`='".$finalMemberCount."' where tenant_id='".$Tenant_id."'";
				$data = $this->m_dbConn->update($up_query);
			}
			if($_POST['tenantAction'] == "ter")
			{
				if($_POST['actionPage'] == "serviceRequest")
				{
					$this->actionPage = "../tenant.php?prf&mem_id=".$memberId."&sr";
				}
				else
				{
					$this->actionPage = "../view_member_profile.php?id=" . $_POST['mem_id'];
				}
			}
			else if(isset($_POST['verified']) || isset($_POST['approved']))
			{
				$this->actionPage = "../show_tenant.php?TenantList=4";
			}
			else
			{
				$this->actionPage = "../view_member_profile.php?id=" . $_POST['mem_id'];
			}
			return "Update";
		}
		else
		{
			return $errString;
		}
		$_SESSION['serviceRequestDetails'] = "";
	}
	
	public function updateTenantVerificationStatus($Tenant_id, $p_verification, $leaveAndLicenseAgreement, $total_month)
	{
		$sql1 = "Select m.`role` from login as l,mapping as m where l.login_id = '".$_SESSION['login_id']."' and l.`current_mapping` = m.`id` ";
		$verifiedByDesignation = $this->m_dbConnRoot->select($sql1);
		$sqlApprovalUpdate = "Update approval_details Set `verifiedStatus` = 'Y', verifiedById = '".$_SESSION['login_id']."' and verifiedByDesignation = '".$verifiedByDesignation[0]['role']."' where referenceId = '".$Tenant_id."' and module_id = '".TENANT_SOURCE_TABLE_ID."'";
		$sqlApprovalUpdate_res = $this->m_dbConn->update($sqlApprovalUpdate);
		$updateTenantModule = "Update `tenant_module` set `active` = '1', `p_varification` = '".$p_verification."', `leaveAndLicenseAgreement` = '".$leaveAndLicenseAgreement."', `total_month` = '".$total_month."' where `tenant_id` = '".$Tenant_id."';";
		$update_Res = $this->m_dbConn->update($updateTenantModule);
		$sql1 = "select `serviceRequestId` from tenant_module where tenant_id = '".$Tenant_id."';";
		$sql1_res = $this->m_dbConn->select($sql1);
		$sql3 = "update service_request set `status` = 'Verified' where request_id = '".$sql1_res[0]['serviceRequestId']."';";
		$sql3_res = $this->m_dbConn->update($sql3);
	}
	
	
	public function SendActivationEmail()
	{
		if($_POST['chkCreateLogin'] == "1")
			{
				//echo "chk".$_POST['chkCreateLogin'];
				//die();
				$role = ROLE_MEMBER;
				$unit_id  = $_POST["unit_id"];
				$code  = $_POST["Code"];
				$society_id = $_SESSION['society_id'];
				$NewUserEmailID = $_REQUEST['email_1'];
				$DisplayName = $_POST['members_1'];
				//echo "unit:".$unit_id  ." code:".	$code ." email:".$NewUserEmailID ." name:".$DisplayName ;
				
				$ActivationStatus = $this->obj_activation->AddMappingAndSendActivationEmail($role, $unit_id, $society_id, $code, $NewUserEmailID, $DisplayName);
				//echo "status:".$ActivationStatus;
				
				if($ActivationStatus != "Success")
				{				
					return "Unable to Send Activation Email.";
				}
				//die();
			}
	}
	public function display1($rsas)
	{
		$thheader = array('t_name','mob','alt_mob','email','members','start_date','end_date','p_varification','upload','note');
		$this->display_pg->edit		= "gettenant";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "tenant.php";

		$res = $this->display_pg->display_new($rsas);
		return $res;
	}
	public function pgnation()
	{
		$sql1 = "select id,`t_name`,`mob`,`alt_mob`,`email`,`members`,`start_date`,`end_date`,`p_varification`,`upload`,`note` from  where status='Y'";
		$cntr = "select count(status) as cnt from  where status='Y'";

		$this->display_pg->sql1		= $sql1;
		$this->display_pg->cntr1	= $cntr;
		$this->display_pg->mainpg	= "tenant.php";

		$limit	= "50";
		$page	= $_REQUEST['page'];
		$extra	= "";

		$res	= $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}
	public function selecting($Tenant_Id)
	{
		//$sql = "select t.tenant_name,t.mobile_no,t.alter_no,t.email,t.start_date,t.end_date,t.p_varification,t.note, from `tenant_module` as t LEFT JOIN `Documents` as d on t.doc_id=d.doc_id where t.tenant_id='".$Tenant_Id."'"; 
		//$sql = "select  tenant_name,dob, mobile_no, alter_no, email, start_date, end_date, note  from `tenant_module` where tenant_id='".$Tenant_Id."'"; 
		//$sql = "select  tenant_id,unit_id, tenant_name,start_date, end_date,  agent_name, agent_no, note  from `tenant_module` where tenant_id='".$Tenant_Id."'"; 
		$sql = "select t.tenant_id,t.unit_id, u.unit_no, t.tenant_name,t.tenant_MName,t.tenant_LName,t.start_date, t.end_date, t.total_month, t.`agent_name`, t.agent_no, t.note,t.`dob`,t.`mobile_no`,t.`email`, t.`img`, t.`active`, t.`p_varification`, t.`leaveAndLicenseAgreement` from `tenant_module` t, `unit` u where tenant_id='".$Tenant_Id."' and t.unit_id = u.unit_id";
		$result=$this->m_dbConn->select($sql);
		/*if($_SERVER['HTTP_HOST'] == "localhost" )
		{		
			$uploaddir = $_SERVER['DOCUMENT_ROOT']."beta_aws_master/Uploaded_Documents";			   
		}
		else
		{
			$uploaddir = $_SERVER['DOCUMENT_ROOT']."Uploaded_Documents";			   
		}*/
		$uploaddir = "Uploaded_Documents";
		$result[0]['img'] = $uploaddir."/".$result[0]['img'];
		$arrayTenant = array();
		if($result<>'')
		{
				//echo "hi";
			//$result[0]['dob'] = getDisplayFormatDate($result[0]['dob']);
			$result[0]['start_date'] = getDisplayFormatDate($result[0]['start_date']);
			$result[0]['end_date'] = getDisplayFormatDate($result[0]['end_date']);
			$result[0]['dob'] = getDisplayFormatDate($result[0]['dob']);
			$sqldata="select `tenant_id`,`mem_name`,`relation`,`mem_dob`,`contact_no`,`email`,`send_act_email`,`send_commu_emails` from `tenant_member` where `tenant_id`='".$Tenant_Id."'";						
			$res1=$this->m_dbConn->select($sqldata);
			
			
			$result[0]['members'] = array();
			for($i=0;$i<sizeof($res1);$i++)
			{
				$res1[$i]['mem_dob'] = getDisplayFormatDate($res1[$i]['mem_dob']);
				array_push($result[0]['members'], $res1[$i]);
			}
			$result[0]['documents']=array();
			//$doc_id=$result[0]['doc_id'];
			$unit=$result[0]['unit_id'];
			$sqlDoc="select * from `documents` where `refId`='".$Tenant_Id."' and (status='Y' or status='') and Unit_Id='".$unit."'";
			$res2=$this->m_dbConn->select($sqlDoc);
			for($j=0;$j<sizeof($res2);$j++)
			{
				$doc_version=$res2[$j]['doc_version'];
				$URL = "";
	            $gdrive_id = $res2[$j]['attachment_gdrive_id'];
	            if($doc_version == "1")
	            {
	            	$URL = "Uploaded_Documents/". $res2[$j]["Document"];
	            }
	            else if($doc_version == "2")
	            {
	                if($gdrive_id == "" || $gdrive_id == "-")
	                {
	                 	$URL = "Uploaded_Documents/". $res2[$j]["Document"];
	               	}
	                else
	                {
	                 	$URL = "https://drive.google.com/file/d/". $gdrive_id."/view";
	                }
	        	}
				$res2[$j]['documentLink'] = $URL;
			}
			//var_dump($res2);
			for($i=0;$i<sizeof($res2);$i++)
			{
				array_push($result[0]['documents'], $res2[$i]);
			}
			$sqlCar="select * from `mem_car_parking` where `car_type`='1' and member_id = '".$Tenant_Id."' and status='Y'";
			$sqlCar_res = $this->m_dbConn->select($sqlCar);
			//var_dump($sqlCar_res);
			$result[0]['carDetails'] = $sqlCar_res;
			$sqlBike="select * from `mem_bike_parking` where `bike_type`='1' and member_id = '".$Tenant_Id."' and status='Y'";
			$sqlBike_res = $this->m_dbConn->select($sqlBike);
			$result[0]['bikeDetails'] = $sqlBike_res;
			if(sizeof($sqlBike_res) == 0 && sizeof($sqlCar_res) == 0)
			{
				$result[0]['vehicleCount'] = 0;
			}
			else
			{
				$result[0]['vehicleCount'] = sizeof($sqlBike_res)+sizeof($sqlCar_res);
			}
		}
		return $result;
	}
	public function deleting($Tenant_id)
	{
		$sql = "update `tenant_module` set status='N' where tenant_id='".$Tenant_id."'";
		$res = $this->m_dbConn->update($sql);
	}
	
	
														/*--------------------------------------------  Show tenant list------------------------------------------------------*/
														
	public function getRecords()
	{
		 $sql =" select t.*,d.* from `tenant_module` as t LEFT JOIN `documents` as d on t.doc_id=d.doc_id where t.status='Y'";
		$result=$this->m_dbConn->select($sql);
		//echo $sql;
		return $result;
	}
	public function getTenantDocuments($UnitID = 0)
	{
		
		//$UnitID = $_SESSION["unit_id"];
		 $sql =" select t.*,d.* from `tenant_module` as t LEFT JOIN `documents` as d on t.tenant_id=d.refID where t.status='Y' and d.source_table=1";
		 //echo "unitid:".$UnitID ;
		 if($SESSION['role']!=ROLE_ADMIN && $_SESSION['role']!=ROLE_SUPER_ADMIN && $_SESSION['role']!=ROLE_ADMIN_MEMBER)
		 {
		 	$sql .= " and t.unit_id='".$UnitID."'";
		 }
		$result=$this->m_dbConn->select($sql);
		//echo $sql;
		return $result;
	}
	public function getTenantDocumentsNew($UnitID = 0)
	{
		
		//$UnitID = $_SESSION["unit_id"];
		 $sql =" select t.* from `tenant_module` as t where t.status='Y'";
		 //echo "unitid:".$UnitID ;
		// if($SESSION['role']!=ROLE_ADMIN && $_SESSION['role']!=ROLE_SUPER_ADMIN && $_SESSION['role']!=ROLE_ADMIN_MEMBER)
		 if($UnitID != 0)
		 {
		 	$sql .= " and t.unit_id='".$UnitID."'";
		 }
		$result=$this->m_dbConn->select($sql);
		//echo $sql;
		return $result;
	}
	
													/*-----------------------------------------------show image and document from edit ---------------------------------*/
	public function getViewDetails($TenantId)
	{
		 $sql="select t.tenant_id,t.doc_id,t.img,t.active,d.Document from `tenant_module` as t LEFT JOIN `documents` as d on d.refID=t.tenant_id where t.tenant_id='".$TenantId."' and d.status='Y'";
		$result=$this->m_dbConn->select($sql);
		return $result;
	}
	
												/*-------------------------------------------------------------- Menber profile function page--------------------------------------------------------*/
	public function getTenantRecords($unit_id)
	{	
	$sql ="select * from `tenant_module` where status='Y' and `unit_id`='".$unit_id."' and end_date >= CURDATE()";
		//
		// $sql ="select *, Count(t.unit_id) as counttotal from `tenant_module` as t LEFT JOIN `Documents` as d on t.doc_id=d.doc_id where t.status='Y' and t.`unit_id`='".$unit_id."' and t.end_date >= now()";
		//$sql =" select* from `tenant_module` as t LEFT JOIN `Documents` as d on t.doc_id=d.doc_id where t.status='Y' and t.`unit_id`='".$unit_id."'";
		 $result=$this->m_dbConn->select($sql);
		 $Tenant_Id=$result[0]['tenant_id'];
		if($Tenant_Id<>'')
			{
				$sqldata="select `tenant_id`,`mem_name`,`relation`,`mem_dob` ,`contact_no`,`email`,`send_act_email`,`send_commu_emails` from `tenant_member` where `tenant_id`='".$Tenant_Id."'";						
				$res1=$this->m_dbConn->select($sqldata);
				$result[0]['Allmembers']=$res1;
			}
			if($Tenant_Id<>'')
			{
				$sqldoc="select * from documents where refID='".$Tenant_Id."' and status in('Y','') and unit_id='".$unit_id."'";
			//echo "sql:".$sqldoc;	
				$res2=$this->m_dbConn->select($sqldoc);
				for($j=0;$j<sizeof($res2);$j++)
				{
					$doc_version=$res2[$j]['doc_version'];
					$URL = "";
	            	$gdrive_id = $res2[$j]['attachment_gdrive_id'];
	            	if($doc_version == "1")
	            	{
	            		$URL = "Uploaded_Documents/". $res2[$j]["Document"];
	            	}
	            	else if($doc_version == "2")
	            	{
	                	if($gdrive_id == "" || $gdrive_id == "-")
	                	{
	                 		$URL = "Uploaded_Documents/". $res2[$j]["Document"];
	               		}
	                	else
	                	{
	                 		$URL = "https://drive.google.com/file/d/". $gdrive_id."/view";
	                	}
	        		}
					$res2[$j]['documentLink'] = $URL;
				}
				if(sizeof($res2) > 0)
				{
					$result[0]['Alldocuments']=$res2;
				}
				else
				{
					$result[0]['Alldocuments'] = "No documents attached";
				}
			}
			
			$sqlCount ="select  Count(unit_id) as counttotal from `tenant_module`  where status='Y' and `unit_id`='".$unit_id."'" ;
			$res3=$this->m_dbConn->select($sqlCount);
			$result[0]['Count']=$res3[0]['counttotal'];
			if($Tenant_Id<>'')
			{
				$result[0]['Count'] = $result[0]['Count'] - 1;
			}
			if($this->m_bShowTrace)
			{
				echo "<pre>";
				print_r($result);
				echo "</pre>";
			}
		return $result;
	}
													
													
													/*-------------------------------------------------------------- Member List  form Unit --------------------------------------------------------*/
			public function MemberList($unit)
			{
			//	$sql =" select* from `tenant_module` as t LEFT JOIN `Documents` as d on t.doc_id=d.doc_id where t.status='Y' and t.unit_id='".$unit."'";
				$sql =" select *, u.unit_no from `tenant_module` as t LEFT JOIN `documents` as d on t.doc_id=d.doc_id join unit as u on t.unit_id=u.unit_id where t.status='Y' and t.unit_id='".$unit."'";
				$result=$this->m_dbConn->select($sql);
				return $result;
				
			}
			
			public function getViewDetailsUser($TenantId)
			{
			$data=$this->selecting($TenantId);
			
				return $data;	
		}
		
		/*-------------------------------------------------------------Notificatino alert-------------------------------------------------*/
		
	public function TenantAlert()
	{
		if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER))
		{
		//$sql="select * ,u.unit_no from `tenant_module` as t join unit as u on u.unit_id=t.unit_id where t.end_date >= TIMESTAMPADD( DAY , -30, NOW( )+ INTERVAL 5 HOUR + INTERVAL 30 MINUTE) t.status='Y' and t.active='1' order by t.tenant_id desc";
		//$sql="select *,u.unit_no from `tenant_module` as t join unit as u on u.unit_id=t.unit_id where t.end_date >= DATE(now()) and t.end_date <= DATE_ADD(DATE(now()), INTERVAL 1 Month) and t.status='Y' and t.active='1' order by t.tenant_id desc";
		$sql="select *,u.unit_no, w.wing from `tenant_module` as t join unit as u on u.unit_id=t.unit_id join wing as w on u.wing_id=w.wing_id where t.end_date >= DATE(now()) and t.end_date <= DATE_ADD(DATE(now()), INTERVAL 1 Month) and t.status='Y' and t.active='1' order by t.tenant_id desc";
		
		}
		else
		{
			$sql="select *,u.unit_no, w.wing from `tenant_module` as t join unit as u on u.unit_id=t.unit_id join wing as w on u.wing_id=w.wing_id where t.unit_id='".$_SESSION['unit_id']."' and  t.end_date >= DATE(now()) and t.end_date <= DATE_ADD(DATE(now()), INTERVAL 1 Month) and t.status='Y' and t.active='1' order by t.tenant_id desc";
		}
		$result=$this->m_dbConn->select($sql);
		return $result;
	}
	public function combobox07($query,$id)
	{
			$str.="<option value='1'>Please Select</option>";
			$data = $this->m_dbConn->select($query);
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
	public function checkTenantStatus($unitId)
	{
		$result = array();
		$sql1 = "select * from `tenant_module` where status='Y' and `unit_id`='".$unitId."' and end_date >= CURDATE()";
		$sql1_res = $this->m_dbConn->select($sql1);
		//var_dump($sql1_res);
		$memberId = $this->getMemberId($unitId);
		if(sizeof($sql1_res) > 0)
		{
			$result['tenantStatus'] = 1;//Active Tenant Exits
			$result['memberId'] = $memberId;
			$result['tenantId'] = $sql1_res[0]['tenant_id'];
		}
		else
		{
			$result['tenantStatus'] = 0;//No Active Tenant Exits
			$result['memberId'] = $memberId;
			$result['tenantId'] = 0;
		}
		//var_dump($result);
		return ($result);
	}
	public function getMemberId($unitId)
	{
		$sql1 = "SELECT `member_id` FROM `member_main` where unit = '".$unitId."' and ownership_status = '1' and status = 'Y'";
		$sql1_res = $this->m_dbConn->select($sql1);
		return ($sql1_res[0]['member_id']);
	}
	public function getApprovalLevel()
	{
		$sql1 = "Select `Value` from appdefault_new where `Property` = 'LevelOfApprovalForTenantRequest' and module_id = '3';";
		$sql1_res = $this->m_dbConn->select($sql1);
		return ($sql1_res[0]['Value']);
	}
}
?> 
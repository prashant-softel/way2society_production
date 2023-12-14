<?php

include_once ("dbconst.class.php"); 
include_once( "include/fetch_data.php");
include_once( "utility.class.php");
include_once('../swift/swift_required.php');
include_once("android.class.php");
//echo "include_ gdrive";
include_once("../GDrive.php");
include_once("email.class.php");


class task
{
	public $actionPage = "../tasks.php?type=raised_to";
	public $m_dbConn;
	public $m_dbConnRoot;
	public $objFetchData;
	public $m_obj_utility;
	public $m_bShowTrace;
	
	function __construct($dbConn, $dbConnRoot, $socID = "")
	{  
		$dbConnRoot=new dbop(true);
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->objFetchData = new FetchData($dbConn);
		$this->m_obj_utility = new utility($dbConn, $dbConnRoot);
		$this->m_bShowTrace = true;
		//echo "soc check".$socID;
		if($socID <> "")
		{
			$this->objFetchData->GetSocietyDetails($socID);	
		}
		else
		{
			$this->objFetchData->GetSocietyDetails($_SESSION['society_id']);
		}
		//echo "checked";
	}
	public function UpdateTask($TaskID, $status, $Percentage)
	{
			$sqlUpdate = "update tasklist set `Status`='".$status."',`PercentCompleted`='".$Percentage."' where id='".$TaskID."'";
		
			$sqlquery = "select * from tasklist where id = '".$TaskID."'";
			$result=$this->m_dbConn->select($sqlquery);
			$sTaskOwnerEmail = $this->getLoginEmailId($result[0]['RaisedBy']);
			$sTaskOwnerName = $this->getLoginName($result[0]['RaisedBy']);
			$sAssigneeName = $this->getTaskassignedName($result[0]['Task_Owner']);
			$sAssigneeEmail = $this->getTaskassignedEmailID($result[0]['Task_Owner']);
			if($result[0]['Priority'] == PRIORITY_LOW)
			{
				$sPriority = "Low";
			}
			else if($result[0]['Priority'] == PRIORITY_MEDIUM)
			{
				$sPriority = "Medium";
			}
			else if($result[0]['Priority'] == PRIORITY_HIGH)
			{
				$sPriority = "High";
			}
			else if($result[0]['Priority'] == PRIORITY_CRITICAL)
			{
				$sPriority = "Critical";
			}

			$mailsubject = "Task Updated with Title : " .$result[0]['Title'];
			$mailbody = "<table  style='border-collapse:collapse'><tr>"
        		. "<tr><td >Dear Member,</td></tr>"
        		."<tr><td>This is to inform you that task has been updated with title <b>".$result[0]['Title']."</b>, created by <b>".$sTaskOwnerName."</b> and assigned to <b>".$sAssigneeName."</b> </td></tr>"
        		. "<tr><td><br></td></tr>"
        		. "<tr><td>Description of the task : ".$result[0]['Description']."</td></tr>"
        		. "<tr><td>Priority of the task : ".$sPriority."</td></tr>"
        		. "<tr><td>Completion Percentage : ".$Percentage."% </td></tr>"
        		. "<tr><td>Due date of the task : ".$result[0]['DueDate']."</td></tr>"
        		. "<tr><td><br><br></td></tr>"
 //       		+ "<tr><td> If you have any questions, please contact society Manager or Secretary.</td></tr>"
        		. "<tr><td><br></td></tr>"
         		. "</tr></table>";
			//echo "Mail Subject : " .$mailsubject . "<br>Mail Body : <br>" .$mailbody. "<br>Assigned To id : " .$sAssigneeEmail . "<br> Creator of Task : " .$sTaskOwnerEmail ;
			$this->sendEmail($mailsubject,$mailbody, $sAssigneeEmail,$sTaskOwnerEmail);
			
		//echo $sqlUpdate;
		echo $this->m_dbConn->update($sqlUpdate);
	}	
	
	public function UpdateTask_due($TaskID, $due_date)
	{
		$sqlUpdate = "update tasklist set `DueDate`='".$due_date."' where id='".$TaskID."'";
		
		//echo $sqlUpdate;
		$sqlquery = "select * from tasklist where id = '".$TaskID."'";
			$result=$this->m_dbConn->select($sqlquery);
			$sTaskOwnerEmail = $this->getLoginEmailId($result[0]['RaisedBy']);
			$sTaskOwnerName = $this->getLoginName($result[0]['RaisedBy']);
			$sAssigneeName = $this->getTaskassignedName($result[0]['Task_Owner']);
			$sAssigneeEmail = $this->getTaskassignedEmailID($result[0]['Task_Owner']);
			if($result[0]['Priority'] == PRIORITY_LOW)
			{
				$sPriority = "Low";
			}
			else if($result[0]['Priority'] == PRIORITY_MEDIUM)
			{
				$sPriority = "Medium";
			}
			else if($result[0]['Priority'] == PRIORITY_HIGH)
			{
				$sPriority = "High";
			}
			else if($result[0]['Priority'] == PRIORITY_CRITICAL)
			{
				$sPriority = "Critical";
			}
			$mailsubject = "Task Updated with Title: " .$result[0]['Title'];
			$mailbody = "<table  style='border-collapse:collapse'><tr>"
        		. "<tr><td >Dear Member,</td></tr>"
        		."<tr><td>This is to inform you that task has been updated with title <b>".$result[0]['Title']."</b>, created by <b>".$sTaskOwnerName."</b> and assigned to <b>".$sAssigneeName."</b> </td></tr>"
        		. "<tr><td><br></td></tr>"
        		. "<tr><td>Description of the task : ".$result[0]['Description']."</td></tr>"
        		. "<tr><td>Priority of the task : ".$sPriority."</td></tr>"
        		. "<tr><td>Completion Percentage : ".$result[0]['PercentCompleted']."% </td></tr>"
        		. "<tr><td>Due date of the task : ".$due_date."</td></tr>"
        		. "<tr><td><br><br></td></tr>"
 //       		+ "<tr><td> If you have any questions, please contact society Manager or Secretary.</td></tr>"
        		. "<tr><td><br></td></tr>"
         		. "</tr></table>";
		//	echo "Mail Subject : " .$mailsubject . "<br>Mail Body : <br>" .$mailbody. "<br>Assigned To id : " .$sAssigneeEmail . "<br> Creator of Task : " .$sTaskOwnerEmail ;
			$this->sendEmail($mailsubject,$mailbody, $sAssigneeEmail,$sTaskOwnerEmail);
		
		echo $this->m_dbConn->update($sqlUpdate);
		
	}	
	public function startProcess()
	{
		if($this->m_bShowTrace)
		{
			//echo $_POST['insert'];			
		}				
		$errorExists=0;
		if($_POST['insert']=='Submit' && $errorExists==0)
		{
			$society_id = $_SESSION['society_id'];
			$login_id = $_SESSION['login_id'];
			
			$IssuedBy = $login_id;
			$Title = urlencode($_POST['title']);
			$description = urlencode($_POST['task_desc']);

			$Title = $_POST['title'];
			$description = $_POST['task_desc'];
			$AssignedTo = $_POST['AssignedTo'][0];
			//echo "to:".$AssignedTo;
			//die();
			$DueDate = getDBFormatDate($_POST['due_date']);
			//$TaskType = $_POST['task_type'];
			$TaskType = "1";
			$request_no=$_POST['request_no'];
			$priority = $_POST['priority'];
			$TaskStatus = $_POST['status'];
			$userFile = $_POST['userfile'];
			$PercentCompleted = $_POST['PercentCompleted'];
			$notify = $_POST['notify'];
			$mobilenotify = $_POST['mobile_notify'];
			$file = $_FILES['userfile']['name'];
			$insertQuery = "insert into tasklist (`request_no`,`Task_Owner`,`RaisedBy`,`Title`,`Description`,`Attachment`,`Priority`,`DueDate`,`Status`,`PercentCompleted`,`TaskType`,`TypeID`,`Role`,`Reminder`) values ('".$request_no."','".$AssignedTo . "','" .$IssuedBy . "','" .$Title . "','" . $description . "','" . $file . "','" . $priority . "','" . $DueDate . "','" . $TaskStatus . "','" . $PercentCompleted . "','1','1','1','1')";
			$sTaskOwnerEmail = $this->getLoginEmailId($IssuedBy);
			$sTaskOwnerName = $this->getLoginName($IssuedBy);
			$sAssigneeName = $this->getTaskassignedName($AssignedTo);
			$sAssigneeEmail = $this->getTaskassignedEmailID($AssignedTo);
			if($priority == PRIORITY_LOW)
			{
				$sPriority = "Low";
			}
			else if($priority == PRIORITY_MEDIUM)
			{
				$sPriority = "Medium";
			}
			else if($priority == PRIORITY_HIGH)
			{
				$sPriority = "High";
			}
			else if($priority == PRIORITY_CRITICAL)
			{
				$sPriority = "Critical";
			}
			$mailsubject = "New Task : " .$Title;
			$mailbody = "<table  style='border-collapse:collapse'><tr>"
        		. "<tr><td >Dear Member,</td></tr>"
        		."<tr><td>This is to inform you that new task with title <b>".$Title."</b> has been created by <b>".$sTaskOwnerName."</b> assigned to <b>".$sAssigneeName."</b> </td></tr>"
        		. "<tr><td><br></td></tr>"
        		. "<tr><td>Description of the task : ".$description."</td></tr>"
        		. "<tr><td>Priority of the task : ".$sPriority."</td></tr>"
        		. "<tr><td>Completion Percentage : ".$PercentCompleted."% </td></tr>"
        		. "<tr><td>Due date of the task : ".$_POST['due_date']."</td></tr>"
        		. "<tr><td><br><br></td></tr>"
 //       		+ "<tr><td> If you have any questions, please contact society Manager or Secretary.</td></tr>"
        		. "<tr><td><br></td></tr>"
         		. "</tr></table>";
			//echo "Mail Subject : " .$mailsubject . "<br>Mail Body : <br>" .$mailbody. "<br>Assigned To id : " .$sAssigneeEmail . "<br> Creator of Task : " .$sTaskOwnerEmail ;
			$this->sendEmail($mailsubject,$mailbody, $sAssigneeEmail,$sTaskOwnerEmail);
			$insertRes = $this->m_dbConn->insert($insertQuery);
			if(!empty($_FILES['userfile']['name'])){
				
				 $target_path = "../Uploaded_Documents/Tasks/".$_SESSION['society_id']."/Task_id_".$insertRes."/"; 
				 $file_path = $target_path.basename($_FILES['userfile']['name']);   
				//echo $_FILES['userfile']['tmp_name']; 
				if(!file_exists($target_path))
				{
					mkdir($target_path, 0777, true);
				}
				else{
					chmod($target_path, 0777,true);
				}
				if(move_uploaded_file($_FILES['userfile']['tmp_name'], $file_path)) {  
					echo "File uploaded successfully!";
					$update_query = "UPDATE tasklist SET Attachment = '".$file_path."' where id = '".$insertRes."'";
					$Update_result = $this->m_dbConn->update($update_query);
				} else{  
					echo "Sorry, file not uploaded, please try again!";  
				} 
			header("Location: ../tasks.php?type=raised_to");
			}
			}
		
		else if($_POST['insert']=='Update' && $errorExists==0)
		{
			$docGDriveID = "";
			$sMode = "";	
			//die();
			if($_POST['notice_creation_type'] == 2)
			{
				$notice_type = "0000-00-00";
					$PostDate = "0000-00-00";
					
				if($_FILES['userfile']['name'] == "")
				{
					//echo "Please select file to upload.";
					//return;
					$_POST["note"] = "";
				}
				else
				{
					$notice_type = $_POST["notice_type"];
					$PostDate = $_POST['post_date'];
					
						//echo "trace:".$notice_type.$PostDate;
					//$docGDriveID = $this->UploadAttachment($_FILES, $notice_type,$PostDate);

					//$notice_type = $_POST["doc_type"];
					//$PostDate = $_POST['post_date'];
					//echo "trace:".$notice_type.$PostDate;
					//$docGDriveID = $this->UploadAttachment($_FILES, $notice_type,$PostDate);
					//die();
					$resResponse = $this->m_obj_utility->UploadAttachment($_FILES, $notice_type, $PostDate, "Notices");
					$sStatus = $resResponse["status"];
					$sMode = $resResponse["mode"];
					$sFileName = $resResponse["response"];
					
					$sUploadFileName = $resResponse["FileName"];
					$_POST['note'] = $sUploadFileName;

					if($sMode == "1")
					{
						$uploaded_filename = $sFileName;
						$_POST['note'] = $resResponse["note"];
					} 
					else if($sMode == "2")
					{
						$docGDriveID = $sFileName;
					}
					else
					{
						//failure or no file uploaded
					}
					//die();
					
					//echo "gdif:".$docGDriveID;
					//die();
				}
			}	
			//echo "exp:".$_POST['exp_date'];
			//die();
			$sNoticeVersion = '2';
			$doc_type = $_POST['notice_type'];
			if($docGDriveID != "" && $docGDriveID != "Error")
			{
				$sNoticeVersion = '2';
			}

			//echo "<pre>";
			//print_r($_POST);
			//echo "</pre>";
			//die();
			if($sMode != "")
			{
				$sqlUpdate = "UPDATE `notices` SET `society_id`='" .$_SESSION['society_id']. "',`notice_type_id`='".$_POST['notice_type_id']."',`issuedby`='" .$_POST['issueby']. "',`subject`='" .$_POST['subject']. "',`description`='" .$_POST['description']. "',
						`note`='" .$_POST['note']. "',`post_date`='" .getDBFormatDate($_POST['post_date']). "',`exp_date`='" .getDBFormatDate($_POST['exp_date']). "',`isNotify`='".$_POST['notify']."',`doc_id`='".$doc_type."',`doc_template_id`='".$_REQUEST["notice_template"]."', `notice_version`='".$sNoticeVersion."',`attachment_gdrive_id`='".$docGDriveID."' WHERE `id`='".$_POST['updaterowid']."'";
			}
			else
			{
				$sqlUpdate = "UPDATE `notices` SET `society_id`='" .$_SESSION['society_id']. "',`notice_type_id`='".$_POST['notice_type_id']."',`issuedby`='" .$_POST['issueby']. "',`subject`='" .$_POST['subject']. "',`description`='" .$_POST['description']. "',
						`post_date`='" .getDBFormatDate($_POST['post_date']). "',`exp_date`='" .getDBFormatDate($_POST['exp_date']). "',`isNotify`='".$_POST['notify']."',`doc_id`='".$doc_type."',`doc_template_id`='".$_REQUEST["notice_template"]."',`notice_version`='".$sNoticeVersion."' WHERE `id`='".$_POST['updaterowid']."'";
			}					
			//echo "sql:".$sqlUpdate;	
			$result = $this->m_dbConn->update($sqlUpdate);
			
			$sqlDelete = "DELETE FROM `display_notices` WHERE `notice_id` = '".$_POST['updaterowid']."'"; 
			$this->m_dbConn->delete($sqlDelete);
			
			$noticeToArray = array();
			$notice=$_POST['post_noticeto'];
			
			if ($notice)
			{
				foreach ($notice as $value)
				{
					array_push($noticeToArray,$value);
				}
			}
			
			for($i=0;$i<sizeof($noticeToArray);$i++)
			{
				if($noticeToArray[$i]==0)
				{
					$sqldata="insert into `display_notices`(`notice_id`,`unit_id`) values(".$_POST['updaterowid'].",".$noticeToArray[$i].")";					
					$data=$this->m_dbConn->insert($sqldata);
				}
				else
				{
					$sqldata="insert into `display_notices`(`notice_id`,`unit_id`) values(".$_POST['updaterowid'].",".$noticeToArray[$i].")";					
					$data=$this->m_dbConn->insert($sqldata);
				}					
			}
			
			$logMsg = "Added New Notice". " | " . " Sent to :". implode(",",$noticeToArray) ." | Notify Flag :". $_POST['notify'] . " | Mobile_Notify Flag :" .$_POST['mobile_notify']. " | Version : ". $sNoticeVersion. " | UploadedDocID :". $docGDriveID;
			$insertQuery = "INSERT INTO `change_log`(`ChangedLogDec`, `ChangedBy`, `ChangedTable`, `ChangedKey`) VALUES ('" . $this->m_dbConn->escapeString($logMsg) . "','".$_SESSION['login_id']."','Notice','".$result."')";										
				$this->m_dbConn->insert($insertQuery);			
			return "Update";			
		}
		
	}
	public function combobox($query,$id)
	{
	$str.="<option value=''>Please Select</option>";
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
						if($v==$id)
						{
							$sel = "selected";
						}
						else
						{
							$sel = "";	
						}
						$str.="<OPTION VALUE=".$v.">";
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
	
	 public function selecting($taskID)
	{
		
		 $sqlQuery = "select * from tasklist where id='".$taskID."'";	
		//echo $sqlQuery;			
		$res = $this->m_dbConn->select($sqlQuery);	
		//print_r($res);
		return $res;
	
	}

	public function comboboxRoot($query,$id)
	{
	$str.="<option value=''>Please Select</option>";
	$data = $this->m_dbConnRoot->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($v==$id)
						{
							$sel = "selected";
						}
						else
						{
							$sel = "";	
						}
						$str.="<OPTION VALUE=".$v.">";
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
	
	
	public function combobox2($query,$id)
	{
	$str.="<option selected='selected' value='0'>All</option>";
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
						if($v==$id)
						{
							$sel = "selected";
						}
						else
						{
							$sel = "";	
						}
						$str.="<OPTION VALUE=".$v.">";
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
	public function combobox2Root($query,$id)
	{
	//$str.="<option selected='selected' value='0'>All</option>";
	$data = $this->m_dbConnRoot->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($v==$id)
						{
							$sel = "selected";
						}
						else
						{
							$sel = "";	
						}
						$str.="<OPTION VALUE=".$v.">";
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
	
	
	public function FetchAlltasks($type,$raised)
	{
		//echo "fetch all";
		//$callingURL = "http://localhost:8080/Unichem_web/Tasks?SocID=".$society_id."&TaskOwner=".$AssignedTo."&RaisedBy=".$login_id."&Title=".$Title."&DueDate=".$DueDate."&Task_Desc=".$description."&Attachment=asdfdfdf.jpg&Status=".$TaskStatus."&Completed=".$PercentCompleted."&TaskType=".$TaskType."&Role=1&Reminder=".$notify."&Priority=".$priority."&TypeID=1";
			//echo $EncodedURL = $callingURL;
			//$resTask = file_get_contents($EncodedURL);
			// echo $type;
			if($type == "all")
			{
				if($raised == 'Raised')
				{
					 $sql = "select * from tasklist where Status = 1";
				}
				else
				{
					 $sql = "select * from tasklist ";
				}
			}
			else
			{
				 $sql = "select * from tasklist ";
				
				// echo $_SESSION["current_mapping"];
				if($type=="raised_by")
				{
				$sql .="where Task_Owner='".$_SESSION["current_mapping"]."' order by TimeStamp DESC";
				}
				else if($type=="raised_to")
				{
				$sql .="where RaisedBy='".$_SESSION["login_id"]."' order by TimeStamp DESC";
				}
			}
			// or '".$_SESSION["login_id"]."'";
			//file_get_contents(filename);
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			
		
	return $result;	
	}
	
	
	
	public function GetAttachmentFileLink($NoticeID)
	{
		$arAttachment = array();
		$sql = "select * from `notices` where id='".$NoticeID."'";
		$result = $this->m_dbConn->select($sql);
		$sGDriveID = "";
		if(isset($result["0"]["attachment_gdrive_id"]) && $result["0"]["attachment_gdrive_id"] != "" || $result["0"]["attachment_gdrive_id"] != "-")
		{
			$sGDriveID = $result["0"]["attachment_gdrive_id"];
		}
		$sW2S_Uploaded_file = "";
		if(isset($result["0"]["note"]) && $result["0"]["note"] != "")
		{
			$sW2S_Uploaded_file = $result["0"]["note"];
		}
		$sNoticeVersion = $result["0"]["notice_version"];
		
		$arAttachment["notice_version"] = $sNoticeVersion;
		if($sNoticeVersion == "1")
		{
			$arAttachment["attachment_file"] = $sW2S_Uploaded_file;
			$arAttachment["Source"] = "1";//w2s
		}
		else if($sNoticeVersion == "2")
		{
			if($sGDriveID != "")
			{
				$arAttachment["attachment_file"] = "https://drive.google.com/file/d/". $sGDriveID ."/view";
				$arAttachment["Source"] = "2";//gdrive
			}
			else
			{
				$arAttachment["attachment_file"] = $sW2S_Uploaded_file;	
				//$arAttachment["attachment_file"] = "1518789971_anurag.xlsx";
				$arAttachment["Source"] = "1";//w2s
			}
		}
		else
		{

		}
		return $arAttachment;
	}
	
	
	
	///----------------------------------------------Mobile Notification ----------------------------///
	
	public function SendMobileNotification($subject, $noticeToArray, $NoticeID, $UnitID, $SocID, $DBName)
	{
		$NoticeTitle="Society Notice";
		$NoticeMassage = $subject ;
		$display = array();
		$EmailIDtoUnitIDs = array();					
													
		for($i=0;$i<sizeof($noticeToArray);$i++)
		{		
		//echo "INside for loop";	
		//print_r($noticeToArray[$i]);							
			//if($noticeToArray[$i]==0)
			//{	
			//echo "if condition";	
				$UnitNo = $noticeToArray[$i];
				$emailIDList = $this->objFetchData->GetEmailIDToSendNotification(0);

				for($i = 0; $i < sizeof($emailIDList); $i++)
				{	
					if(($emailIDList[$i]['to_email'] <> "") )
					{
						
						$UnitID = $emailIDList[$i]['unit'];
						//echo "<br>email:".$emailIDList[$i]['to_email'];	
						//echo "<br>unit:".$emailIDList[$i]['unit'];	
						//echo "<br>unit:".$noticeToArray[$i];	
						
						if($UnitNo == $UnitID)
						{
							//echo "<br>matched";
						
							$objAndroid = new android($emailIDList[$i]['to_email'], $SocID, $UnitID);
							$sendMobile=$objAndroid->sendNoticeNotification($NoticeTitle,$NoticeMassage,$NoticeID);
						}
					}
				//}
			}
			
			}
		
	}
	public function fetch_template_details($id)
	{	
		$sqlQuery = "select * from document_templates where id='".$id."'";
		$res = $this->m_dbConnRoot->select($sqlQuery);
		return $res[0];
	}
	public function UploadAttachment($arFILES, $notice_type,$PostDate)
	{
		$docGDriveID = "";
		try
		{
			//echo "start";
			//die();
			$fileTempName = $arFILES['userfile']['tmp_name'];  
			$fileSize = $arFILES['userfile']['size'];
			$fileName = time().'_'.basename($arFILES['userfile']['name']);
			if($_SERVER['HTTP_HOST']=="localhost")
			{		
				$uploaddir = $_SERVER['DOCUMENT_ROOT']."/beta_aws_9/Notices";			   
			}
			else
			{
				$uploaddir = $_SERVER['DOCUMENT_ROOT']."/Notices";			   
			}
			$uploadfile = $uploaddir ."/". $fileName;	
			if($this->m_bShowTrace)
			{
				echo $uploadfile;	
			}
			
			//die();
			$resSociety = $this->m_obj_utility->GetGDriveDetails();
			$sGDrive_W2S_ID = $resSociety["0"]["GDrive_W2S_ID"];
			$ObjGDrive = new GDrive($this->m_dbConn, "0", $sGDrive_W2S_ID, 0);
			if($this->m_bShowTrace)
			{
				echo "uploading to gdrive from tenant:".$documentName. ".".$fileExt ."|".$random_name ."|".$uploadedfile;	
			}
			$mimeType = $arFILES['userfile']['type'];
			$documentName = time() . "_" . $arFILES['userfile']['name'] ;
			$noticeFileName = $documentName;
			$sqlDocName = "select doc_type from `document_type` where ID='".$notice_type."'";
			$resDocName = $this->m_dbConn->select($sqlDocName);
			if($this->m_bShowTrace)
			{
				echo "doctype:".$NoticeAlias = $resDocName[0]["doc_type"];
				echo "notice_type:".$notice_type;
			}
			//die();
			//$str = "Lease//".$start;
			$folderName = $NoticeAlias . "//".$PostDate; 
			if($this->m_bShowTrace)
			{
				echo "path:".$folderName;
			
			echo "filename:".$noticeFileName ." mime:". $mimeType ." tmpname:". $arFILES['userfile']['tmp_name'] ." folderName:". $folderName;
			echo "W2SGD:".$sGDrive_W2S_ID;
			}
			if($sGDrive_W2S_ID != "")
			{
			//$mimeType = 'application/vnd.google-apps.file';
				$UploadedFiles = $ObjGDrive->UploadFiles($noticeFileName , $noticeFileName, $mimeType, $arFILES['userfile']['tmp_name'], $folderName, $folderName, "", "", $sGDrive_W2S_ID, "0");
			}
			else
			{
				if(move_uploaded_file($arFILES['userfile']['tmp_name'], $uploadfile))
				{
					$_POST['note'] = $fileName;
				}
				else
				{
					echo "Error uploading file - check destination is writeable.";
					return "";
				}
			}
			if($this->m_bShowTrace)
			{
				echo "<br>uploadfile:";
				echo "<pre>";
				print_r($UploadedFiles);

				echo "</pre>";
			}
			$_POST["note"] = $noticeFileName;
			if($UploadedFiles["status"] == 1)
			{
				$docGDriveID = $UploadedFiles["response"]["id"];
				echo "file uploaded successfully to gdrive.";
			}
			else
			{
				//$docGDriveID = $UploadedFiles["status"][0][""];
				$docGDriveID = "Error";
			}
		}
		catch(Exception $exp)
		 {
			echo "Error occured in uploading document. Details are:".$exp->getMessage();
			die();
		 }
		return $docGDriveID;
	}
	
	public function getEditDetails($id)
	{
		$sql="Select  * from tasklist where id='".$id."'";
		$result=$this->m_dbConn->select($sql);
		return $result;
	}
	public function getLoginName($loginId)
	{
		$sql = "SELECT name FROM `login` where login_id =".$loginId.";";
		$name = $this->m_dbConnRoot->select($sql);
		return $name[0]['name'];	
	}
	public function getTaskassignedName($ownerid)
	{
		$sql = "SELECT login.name FROM `mapping` join login on mapping.login_id=login.login_id where mapping.id = '".$ownerid."'and mapping.society_id='" . $_SESSION['society_id'] . "'";
		$name = $this->m_dbConnRoot->select($sql);
		return $name[0]['name'];
		
	}
	public function getLoginEmailId($loginId)
	{
		$sql = "SELECT member_id FROM `login` where login_id =".$loginId.";";
		$emailid = $this->m_dbConnRoot->select($sql);
		return $emailid[0]['member_id'];	
	}
	public function getTaskassignedEmailID($ownerid)
	{
		$sql = "SELECT login.member_id FROM `mapping` join login on mapping.login_id=login.login_id where mapping.id = '".$ownerid."'and mapping.society_id='" . $_SESSION['society_id'] . "'";
		$emailid = $this->m_dbConnRoot->select($sql);
		return $emailid[0]['member_id'];
		
	}
	public function sendEmail($mailsubject,$mailbody, $sAssigneeEmail,$sTaskOwnerEmail)
	{
	  try
	  {
		  $bccArray = array();
		  $bccArray[0]=$sAssigneeEmail;
		  $bccArray[1]=$sTaskOwnerEmail;
		  $dbname = $_SESSION["dbname"];
		  $society_id = $_SESSION['society_id'];
		  $EMailIDToUse = $this->m_obj_utility->GetEmailIDToUse(true, 1, "", "", 0, $dbname, $society_id);
		  $EMailID = $EMailIDToUse['email'];
		  $Password = $EMailIDToUse['password'];
		 //var_dump($EMailIDToUse);
		  $societyEmail = "";	  
		  if($this->objFetchData->objSocietyDetails->sSocietyEmail <> "")
		  {
			 $societyEmail = $this->objFetchData->objSocietyDetails->sSocietyEmail;
		  }
		  else
		  {
			 $societyEmail = "societyaccounts@pgsl.in";
		  }	 
		  //echo "societyEmail : " .$societyEmail;
		  
		  $bccArray[2]=$societyEmail;
		  $SocietyName =$this->objFetchData->objSocietyDetails->sSocietyName;
		//echo "Statis" .$EMailIDToUse['status'];
		//  var_dump($bccArray);
		  if($EMailIDToUse['status'] == 0)				
		 {
			 //AWS Config 
			 $AWS_Config = CommanEmailConfig();
				 $transport = Swift_SmtpTransport::newInstance($AWS_Config[0]['Endpoint'],$AWS_Config[0]['Port'] , $AWS_Config[0]['Security'])
				  ->setUsername($AWS_Config[0]['Username'])
				  ->setPassword($AWS_Config[0]['Password']);	 
			//$transport = Swift_SmtpTransport::newInstance('103.50.162.146')
			//->setUsername($EMailID)
			//->setSourceIp('0.0.0.0')
			//->setPassword($Password) ; 
			// Create the message
			$message = Swift_Message::newInstance();
			if($this->objFetchData->objSocietyDetails->sSocietyEmail <> "")
			{
				$message->setBcc(array(
							   $societyEmail => $societyName
							));
			}
			$message->setTo($bccArray);															
			$message->setReplyTo(array(
						   $societyEmail => $societyName
						));
			$message->setSubject($mailsubject);
			$message->setBody($mailbody);
			//$message->setFrom($EMailID, $this->objFetchData->objSocietyDetails->sSocietyName);
			$message->setFrom('no-reply@way2society.com',$SocietyName);
			$message->setContentType("text/html");
			// Send the email
			$mailer = Swift_Mailer::newInstance($transport);
			
			$result = $mailer->send($message);
			
			if($result <> 0)
			{
				echo "<br/>Success";
			}
			else
			{
				echo "<br/>Failed";
			}
		}
		
	  }
	  catch(Exception $exp)
	  {
		echo "Error occured in email sending.";
	  }

	}
}

?>
<?php

include_once ("dbconst.class.php"); 
include_once( "include/fetch_data.php");
include_once( "utility.class.php");
include_once('../swift/swift_required.php');
include_once("android.class.php");
include_once("email.class.php");
//echo "include_ gdrive";
include_once("../GDrive.php");
ini_set('memory_limit', '1024M');  // exceed memory limit

class notice
{
	public $actionPage = "../notices.php";
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
		$this->m_bShowTrace = false;
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
	
	public function AddNotice($society_id, $login_id, $dbname, $IssuedBy, $subject, $description, $note, $NoticeDate, $expDate, $noticeType, $noticeCreationType, $postToNotice, $noticeTypeID, $smsNotify, $notify, $mobilenotify, $file, $saveAsTemplate, $showToMember, $showInNotice,$msgBody)
	{
		//echo "NOtice Date : ".$postToNotice;
	
		
		if($IssuedBy <> '' && $subject <> '' && $NoticeDate <> ''  && $noticeType <> '')
		{
			
			//die();
			$docGDriveID = "";
			//echo  "Type :".$NoticeCreationType;
			if($noticeCreationType == 2)
			{
				//echo "insert:".$NoticeDate;
				$notice_type = "0000-00-00";
				$PostDate = "0000-00-00";
				if($expDate == '')
				{
					$expDate = "00-00-0000";
				}	
				if($file == "")
				{
					//echo "Please select file to upload.";
					//return;
				}
				else
				{
					$notice_type = $noticeType;
					$PostDate = $NoticeDate;
					//echo "Post Date :".$PostDate."Exp Date : ".$expDate;	
					$resResponse = $this->m_obj_utility->UploadAttachment($_FILES, $notice_type, $PostDate, "Notices");
					$sStatus = $resResponse["status"];
					$sMode = $resResponse["mode"];
					$sFileName = $resResponse["response"];
					$sUploadFileName = $resResponse["FileName"];
					$note = $sUploadFileName;

					if($sMode == "1")
					{
						$uploaded_filename = $sFileName;
						//$_POST['note'] = $resResponse["note"];
					} 
					else if($sMode == "2")
					{
						$docGDriveID = $sFileName;
					}
					else
					{
						//failure or no file uploaded
					}
					//echo "gdif:".$docGDriveID;
					//die();
				}
					
			}
				
				$creation_date=date('d-m-Y');
				$noticeToArray = array();
				$notice = array();
				$notice=$postToNotice;
				$doc_type = $noticeType;
				
				if($notice)
				{
					for($i=0;$i<sizeof($notice);$i++)
					{
						//echo "$value :".$notice[$i]['MemberId'];
						array_push($noticeToArray,$notice[$i]['MemberId']);
					}
				}
				
				$sNoticeVersion = '2';
				if($docGDriveID != "" && $docGDriveID != "Error")
				{
					$sNoticeVersion = '2';
				}
				
				  $insert_notice="insert into `notices`(`notice_type_id`,`issuedby`,`subject`,`description`,`creation_date`,`post_date`,`exp_date`,`note`,`society_id`,`isNotify`, `sms_notify`,`mobile_notify`,`doc_id`,`notice_version`,`attachment_gdrive_id`) values('".$noticeTypeID."','" .$IssuedBy. "','" .$subject. "','" .$description. "','" .getDBFormatDate($creation_date). "','" .getDBFormatDate($NoticeDate). "','" .getDBFormatDate($expDate). "','" .$note. "','" .$society_id. "','".$notify."','".$smsNotify."','".$mobilenotify."','".$doc_type."','".$sNoticeVersion."','".$docGDriveID."')";
				 //$insert_notice;
				//die();
				$res=$this->m_dbConn->insert($insert_notice);
				//print_r($res);
				//$bEnableSaveTemplate = 1;
				if($saveAsTemplate == 1)
				{
					 $sqlQry = "insert into `document_templates`(`template_subject`,`template_name`,`template_data`,`visible_to_member`,`show_in_notice`) values ('".$subject."','".$subject."','".$description."','".$showToMember."','".$showInNotice."') ";
					$resDoc=$this->m_dbConnRoot->insert($sqlQry);
				}
				
				for($i=0;$i<sizeof($noticeToArray);$i++)
				{
					
					if($noticeToArray[$i]==0)
					{
						$sqldata="insert into `display_notices`(`notice_id`,`unit_id`) values('".$res."','".$noticeToArray[$i]."')";						
						$data=$this->m_dbConn->insert($sqldata);
					}
					else
					{
						$sqldata="insert into `display_notices`(`notice_id`,`unit_id`) values('".$res."','".$noticeToArray[$i]."')";						
						$data=$this->m_dbConn->insert($sqldata);
					}	
					
				}
				$this->objFetchData->objSocietyDetails->sSocietyEmail;
				if($notify)
				{	
				//echo "Call Email Function<br>";		
				//print_r($noticeToArray[$i]);	
				//if($_SERVER['HTTP_HOST']<>"localhost")
				//{
					 $this->sendEmail($subject,$description, $noticeToArray, $fileName, $res, $noticeToArray[$i], $society_id, $dbname, 0, 0);
					
				//}		
				}
				
				if($mobilenotify)
				{															
					$this->SendMobileNotification($subject, $noticeToArray, $res, $society_id, $dbname);
					
				}
				 
				if($_POST['sms_notify'])
				{
					$this->SendNoticeSMS($msgBody, $noticeToArray, $res, $society_id, $dbname);
				} 
		//}
				$logMsg = "Added New Notice". " | " . " Sent to :". implode(",",$noticeToArray) ." | Notify Flag :". $notify . " | Mobile_Notify Flag :" .$_POST['mobile_notify']. " | Version : ". $sNoticeVersion. " | UploadedDocID :". $docGDriveID;
				$insertQuery = "INSERT INTO `change_log`(`ChangedLogDec`, `ChangedBy`, `ChangedTable`, `ChangedKey`) VALUES ('" . $this->m_dbConn->escapeString($logMsg) . "','".$login_id."','Notice','".$res."')";										
					$this->m_dbConn->insert($insertQuery);			
				return "Insert";
			}
	
			else
			{
				return "Record Not Inserted. Please make sure all mandatory values are enterted.";
				
			}
	
	}
	public function startProcess()
	{
		if($this->m_bShowTrace)
		{
			echo $_POST['insert'];			
		}				
		$errorExists=0;
		if($_POST['insert']=='Submit' && $errorExists==0)
		{
			$society_id = $_SESSION['society_id'];
			$login_id = $_SESSION['login_id'];
			$dbname = $_SESSION["dbname"];
			$IssuedBy = $_POST['issueby'];
			$subject = $_POST['subject'];
			$description = $_POST['description'];
			$note = $_POST['note'];
			$NoticeDate = $_POST['post_date'];
			$expDate = $_POST['exp_date'];
			$noticeType = $_POST['notice_type'];
			$noticeCreationType = $_POST['notice_creation_type'];
			$postToNotice = $_POST['post_noticeto'];
			$noticeTypeID = $_POST['notice_type_id'];
			$smsNotify = $_POST['sms_notify'];
			$notify = $_POST['notify'];
			$mobilenotify = $_POST['mobile_notify'];
			$msgBody = $_POST['SMSTemplate'];
			$file = $_FILES['userfile']['name'];
			if($_POST['groupId'] == "AO")
			{
				$postToNotice = $_POST['post_noticeto'];
				if($postToNotice[0] == "0")
				{
					$postToNotice = $this->getGroupMemberForAll($_POST['groupId']);
				}
				else
				{
					$postunit_id = "";
					for($i = 0;$i<count($postToNotice);$i++)
					{
						$postunit_id .= $postToNotice[$i] . ',';
					}
					$postunit_id = rtrim($postunit_id,',');
					//echo "Post Unit Id : " . $postunit_id;
					$postToNotice = $this->getGroupMemberForUnit($postunit_id);
				 }
			}
			elseif( $_POST['groupId'] == "ACO")
			{
				$postToNotice = $_POST['post_noticeto'];
				if($postToNotice[0] == "0")
				{
					$postToNotice = $this->getGroupMemberForAll($_POST['groupId']);
				}
				else
				{
					$postunit_id = "";
					for($i = 0;$i<count($postToNotice);$i++)
					{
						$postunit_id .= $postToNotice[$i] . ',';
					}
					$postunit_id = rtrim($postunit_id,',');
					//echo "Post Unit Id : " . $postunit_id;
					$postToNotice = $this->getGroupMemberForUnit($postunit_id);
				 }
			}
			elseif($_POST['groupId'] == "AOCO")
			{
				$postToNotice = $_POST['post_noticeto'];
				if($postToNotice[0] == "0")
				{
					$postToNotice = $this->getGroupMemberForAll($_POST['groupId']);
				}
				else
				{
					$postunit_id = "";
					for($i = 0;$i<count($postToNotice);$i++)
					{
						$postunit_id .= $postToNotice[$i] . ',';
					}
					$postunit_id = rtrim($postunit_id,',');
					//echo "Post Unit Id : " . $postunit_id;
					$postToNotice = $this->getGroupMemberForUnit($postunit_id);
				 }
			}
			elseif($_POST['groupId'] == "ACM")
			{
				$postToNotice = $_POST['post_noticeto'];
				if($postToNotice[0] == "0")
				{
					$postToNotice = $this->getGroupMemberForAll($_POST['groupId']);
				}
				else
				{
					$postunit_id = "";
					for($i = 0;$i<count($postToNotice);$i++)
					{
						$postunit_id .= $postToNotice[$i] . ',';
					}
					$postunit_id = rtrim($postunit_id,',');
					//echo "Post Unit Id : " . $postunit_id;
					$postToNotice = $this->getGroupMemberForUnit($postunit_id);
				 }
			}
			elseif($_POST['groupId'] == "AVO")
			{
				$postToNotice = $_POST['post_noticeto'];
				if($postToNotice[0] == "0")
				{
					$postToNotice = $this->getGroupMemberForAll($_POST['groupId']);
				}
				else
				{
					$postunit_id = "";
					for($i = 0;$i<count($postToNotice);$i++)
					{
						$postunit_id .= $postToNotice[$i] . ',';
					}
					$postunit_id = rtrim($postunit_id,',');
					//echo "Post Unit Id : " . $postunit_id;
					$postToNotice = $this->getGroupMemberForUnit($postunit_id);
				 }
			}
			elseif($_POST['groupId'] == "AR")
			{
				$postToNotice = array();
				$postToNotice = $_POST['post_noticeto'];
				if($postToNotice[0] == "0")
				{
					$postToNotice = $this->getGroupMemberForAll($_POST['groupId']);
				}
				else
				{
					$postunit_id = "";
					for($i = 0;$i<count($postToNotice);$i++)
					{
						$postunit_id .= $postToNotice[$i] . ',';
					}
					$postunit_id = rtrim($postunit_id,',');
					//echo "Post Unit Id : " . $postunit_id;
					$postToNotice = $this->getGroupMemberForUnit($postunit_id);
				 }
			}
			elseif($_POST['groupId'] == "AT")
			{
				$postToNotice = $_POST['post_noticeto'];
				if($postToNotice[0] == "0")
				{
					$postToNotice = $this->getGroupMemberForAll($_POST['groupId']);
				}
				else
				{
					$postunit_id = "";
					for($i = 0;$i<count($postToNotice);$i++)
					{
						$postunit_id .= $postToNotice[$i] . ',';
					}
					$postunit_id = rtrim($postunit_id,',');
					//echo "Post Unit Id : " . $postunit_id;
					$postToNotice = $this->getGroupMemberForUnit($postunit_id);
				 }
			}
			//If No group is selected
			elseif($_POST['groupId'] == "0")
			{
				$postToNotice = $_POST['post_noticeto'];
				//For All Group
				if($postToNotice[0] == "0")
				{
					$postToNotice = $this->getGroupMemberForAll($_POST['groupId']);
				}
				//If selected members are selected
				else
				{
					$postunit_id = "";
					for($i = 0;$i<count($postToNotice);$i++)
					{
						$postunit_id .= $postToNotice[$i] . ',';
					}
					$postunit_id = rtrim($postunit_id,',');
					//echo "Post Unit Id : " . $postunit_id;
					$postToNotice = $this->getGroupMemberForUnit($postunit_id);
				 }
			}
			else
			{
				$postToNotice = array();
				//$memArray = array();
				$postToNotice = $_POST['post_noticeto'];
				if($postToNotice[0] == "0")
				{
					$postToNotice = $this->getGroupMemberForAll($_POST['groupId']);
				}
				else
				{
					$postunit_id = "";
					for($i = 0;$i<count($postToNotice);$i++)
					{
						$postunit_id .= $postToNotice[$i] . ',';
					}
					$postunit_id = rtrim($postunit_id,',');
					//echo "Post Unit Id : " . $postunit_id;
					$postToNotice = $this->getGroupMemberForUnit($postunit_id);
				 }
			}
			if(isset($_POST['notify']))
			{
				$notify = $_POST['notify'];
			}
			else
			{
				$notify = "0";
			}
			if(isset($_POST['sms_notify']))
			{
				$smsNotify = $_POST['sms_notify'];
			}
			else
			{
				$smsNotify = "0";
			}
			if(isset($_POST['mobile_notify']))
			{
				$mobilenotify = $_POST['mobile_notify'];
			}
			else
			{
				$mobilenotify = "0";
			}
			
			$file = $_FILES['userfile']['name'];
			//var_dump($postToNotice);
			$this->AddNotice($society_id, $login_id, $dbname, $IssuedBy, $subject, $description, $note, $NoticeDate, $expDate, $noticeType, $noticeCreationType, $postToNotice, $noticeTypeID, $smsNotify, $notify, $mobilenotify, $file, $saveAsTemplate, $visible_to_member, $show_in_notice,$msgBody);	
		
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
						`note`='" .$_POST['note']. "',`post_date`='" .getDBFormatDate($_POST['post_date']). "',`exp_date`='" .getDBFormatDate($_POST['exp_date']). "',`isNotify`='".$_POST['notify']."', `sms_notify` = '".$_POST['sms_notify']."', `mobile_notify` = '".$_POST['sms_notify']."', `doc_id`='".$doc_type."',`doc_template_id`='".$_REQUEST["notice_template"]."', `notice_version`='".$sNoticeVersion."',`attachment_gdrive_id`='".$docGDriveID."' WHERE `id`='".$_POST['updaterowid']."'";
			}
			else
			{
				$sqlUpdate = "UPDATE `notices` SET `society_id`='" .$_SESSION['society_id']. "',`notice_type_id`='".$_POST['notice_type_id']."',`issuedby`='" .$_POST['issueby']. "',`subject`='" .$_POST['subject']. "',`description`='" .$_POST['description']. "',
						`post_date`='" .getDBFormatDate($_POST['post_date']). "',`exp_date`='" .getDBFormatDate($_POST['exp_date']). "',`isNotify`='".$_POST['notify']."',`sms_notify` = '".$_POST['sms_notify']."', `mobile_notify` = '".$_POST['sms_notify']."',`doc_id`='".$doc_type."',`doc_template_id`='".$_REQUEST["notice_template"]."',`notice_version`='".$sNoticeVersion."' WHERE `id`='".$_POST['updaterowid']."'";
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
					//$data=$this->m_dbConn->insert($sqldata);
				}
				else
				{
					$sqldata="insert into `display_notices`(`notice_id`,`unit_id`) values(".$_POST['updaterowid'].",".$noticeToArray[$i].")";					
					//$data=$this->m_dbConn->insert($sqldata);
				}					
			}
			
			if($_POST['notify'])
			{																
				$this->sendEmail($_POST['subject'],$_POST['description'], $noticeToArray, "",$_POST['updaterowid'],$noticeToArray[0], $_SESSION['society_id'], $_SESSION["dbname"],0,0);
			}
			
			if($_POST['mobile_notify'])
				{	
				
				 	//echo '<BR>Send Notificaion inside the add notice<BR>';														
					$this->SendMobileNotification($subject, $noticeToArray, $_POST['updaterowid'], $_SESSION['society_id'], $_SESSION['dbname']);
					
				}	
				
			if($_POST['sms_notify'])
				{

					$this->SendNoticeSMS($_POST['SMSTemplate'], $noticeToArray, $result,$_SESSION['society_id'], $_SESSION['dbname']);
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
	
	public function comboboxGroup($query,$id)
	{
		$str = "";
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
	public function FetchNotices($nid=0, $UnitID = 0)
	{
		//echo "FetchNotices";
		$todayDate=date('Y-m-d');
		if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER))
		{
			
			if($nid <> 0)
			{
			//$sql="select * from `notices` where id=".$nid." and society_id=".$_SESSION['society_id']." ";
			//$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and noticetbl.id=".$nid." and noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.exp_date < '".$todayDate."' and displaynoticetbl.unit_id IN (0) ";
			$sql = "select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and noticetbl.id=".$nid." and noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y'  and noticetbl.exp_date >='".$todayDate."' ORDER BY noticetbl.exp_date DESC"; //and noticetbl.exp_date > '".$todayDate."'";			
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
			else{
			//$sql="select * from `notices` where society_id=".$_SESSION['society_id']." ";
			//$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id  and noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.exp_date < '".$todayDate."' and displaynoticetbl.unit_id IN (0)";


			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y'  and noticetbl.exp_date >='".$todayDate."' ORDER BY noticetbl.exp_date DESC"; //and noticetbl.exp_date > '".$todayDate."'";			
			//echo "nid".$nid.$sql;
			$result=$this->m_dbConn->select($sql);
			}
		}
		else
		{
			$ReqUnitID = 0;
			if($UnitID == 0)
			{
				$ReqUnitID = $_SESSION['unit_id'];
			}
			else
			{
				$ReqUnitID = $UnitID;
			}
			if($nid <> 0)
			{
			//$sql="select * from `notices` where id=".$nid." and society_id=".$_SESSION['society_id']."";
			//$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and noticetbl.id=".$nid." and noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.exp_date < '".$todayDate."' and  displaynoticetbl.unit_id IN (".$_SESSION['unit_id'].",0) ";
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices as noticetbl,display_notices as displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and noticetbl.id=".$nid." and noticetbl.society_id=".$_SESSION['society_id']." and  displaynoticetbl.unit_id IN (".$ReqUnitID.",0) and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y' and noticetbl.exp_date >='".$todayDate."' ORDER BY noticetbl.exp_date DESC";
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
			else{
			//$sql="select * from `notices` where society_id=".$_SESSION['society_id']." ";
			//$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.exp_date < '".$todayDate."' and  displaynoticetbl.unit_id IN (".$_SESSION['unit_id'].",0) ";			
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices as noticetbl,display_notices as displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and  displaynoticetbl.unit_id IN (".$ReqUnitID.",0) and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y' and noticetbl.exp_date >='".$todayDate."' ORDER BY noticetbl.exp_date DESC";			
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
		}
		//echo sizeof($result);
	return $result;	
	}
	public function FetchAllNotices($nid=0, $UnitID = 0)
	{
		//echo "fetch all";
		$todayDate=date('Y-m-d');
		if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['role']==ROLE_MANAGER))
		{
			
			if($nid <> 0)
			{
				
			 $sql ="select noticetbl.* FROM notices noticetbl WHERE noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.status = 'Y' and noticetbl.id=".$nid."  ORDER BY noticetbl.id DESC";//noticetbl.exp_date DESC";	
			//$sql = "select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and noticetbl.id=".$nid." and noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y'  ORDER BY noticetbl.exp_date DESC"; //and noticetbl.exp_date > '".$todayDate."'";			
			// echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
			else{
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y' ORDER BY noticetbl.id DESC";//noticetbl.exp_date DESC"; //and noticetbl.exp_date > '".$todayDate."'";			
			///echo "nid".$nid.$sql;
			$result=$this->m_dbConn->select($sql);
			}
		}
		else
		{
			$ReqUnitID = 0;
			if($UnitID == 0)
			{
				$ReqUnitID = $_SESSION['unit_id'];
			}
			else
			{
				$ReqUnitID = $UnitID;
			}
			if($nid <> 0)
			{
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices as noticetbl,display_notices as displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and noticetbl.id=".$nid." and noticetbl.society_id=".$_SESSION['society_id']." and  displaynoticetbl.unit_id IN (".$ReqUnitID.",0) and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y' ORDER BY noticetbl.id DESC";//noticetbl.exp_date DESC";
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
			else{
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices as noticetbl,display_notices as displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and  displaynoticetbl.unit_id IN (".$ReqUnitID.",0) and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y'  ORDER BY noticetbl.id DESC";//noticetbl.exp_date DESC";			
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
		}
	return $result;	
	}
	public function FetchAllNoticesEx($nid=0, $UnitID = 0, $bDocsMode = false)
	{
		//echo "fetch all";
		$todayDate=date('Y-m-d');
		if($bDocsMode == false && $_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER))
		{
			
			if($nid <> 0)
			{
			$sql = "select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and noticetbl.id=".$nid." and noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y'  ORDER BY noticetbl.exp_date DESC"; //and noticetbl.exp_date > '".$todayDate."'";			
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
			else{
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y' ORDER BY noticetbl.exp_date DESC"; //and noticetbl.exp_date > '".$todayDate."'";			
			//echo "nid".$sql;
			$result=$this->m_dbConn->select($sql);
			}
		}
		else
		{
			$ReqUnitID = 0;
			if($UnitID == 0)
			{
				$ReqUnitID = 0;
			}
			else
			{
				$ReqUnitID = $UnitID;
			}
			if($nid <> 0)
			{
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices as noticetbl,display_notices as displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and noticetbl.id=".$nid." and noticetbl.society_id=".$_SESSION['society_id']." and  displaynoticetbl.unit_id IN (".$ReqUnitID.",0) and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y' ORDER BY noticetbl.exp_date DESC";
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
			else{
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices as noticetbl,display_notices as displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and  displaynoticetbl.unit_id IN (".$ReqUnitID.",0) and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y'  ORDER BY noticetbl.exp_date DESC";			
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
		}
	return $result;	
	}
	
	public function getcount()
	{
		$todayDate=date('Y-m-d');
		if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER))
		{
		//$sql="select count(*) as cnt from `notices` where  society_id=".$_SESSION['society_id']." ";
		//$sql="select noticetbl.id FROM notices as noticetbl JOIN display_notices as displaynoticetbl on displaynoticetbl.notice_id=noticetbl.id where noticetbl.exp_date > '".$todayDate." and displaynoticetbl.unit_id IN (0)";
		 $sql = "select noticetbl.id FROM notices as noticetbl JOIN display_notices as displaynoticetbl on displaynoticetbl.notice_id=noticetbl.id where noticetbl.exp_date >= '".$todayDate."'";
		//echo "countQuery :".$sql;
		$result=$this->m_dbConn->select($sql);
		}
		else
		{
			//$sql="select count(*) as cnt from `notices` where  society_id=".$_SESSION['society_id']." ";
			//$sql="select noticetbl.id FROM notices as noticetbl JOIN display_notices as displaynoticetbl on displaynoticetbl.notice_id=noticetbl.id where noticetbl.exp_date < '".$todayDate."' and displaynoticetbl.unit_id IN (0,".$_SESSION['unit_id'].")";
			$sql="select noticetbl.id FROM notices as noticetbl JOIN display_notices as displaynoticetbl on displaynoticetbl.notice_id=noticetbl.id where noticetbl.exp_date >= '".$todayDate."' and displaynoticetbl.unit_id IN (0,".$_SESSION['unit_id'].")";
			$result=$this->m_dbConn->select($sql);
		}
		return $result;
		
	}
	
	public function selecting()
	{
		$sql = "SELECT * FROM `notices` WHERE `id` = '".$_REQUEST['noticeId']."'";		
		//$sql = "select noticetbl.*,displaynoticetbl.unit_id FROM notices as noticetbl,display_notices as displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.id = '".$_REQUEST['noticeId']."' ";			
		$res = $this->m_dbConn->select($sql);
		
		$sql1 = "select displaynoticetbl.unit_id FROM notices as noticetbl,display_notices as displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.id = '".$_REQUEST['noticeId']."' ";			
		$result = $this->m_dbConn->select($sql1);
		//print_r($res);
		for($i = 0; $i < sizeof($result); $i++)
		{		
			//$res[0]['unit'.$i] = $result[$i]['unit_id'];		
			$res[0]['unit'] .= $result[$i]['unit_id'].",";
			$res[0]['post_date'] = getDisplayFormatDate($res[0]['post_date']);
			$res[0]['exp_date'] = getDisplayFormatDate($res[0]['exp_date']);
		}
		
		//$res[0]['unitCount'] = $i;
		return $res;
	}
	
	public function deleting()
	{
		$sql0 = "select count(*)as cnt from `notices` WHERE `id`='".$_REQUEST['noticeId']."' and status='Y'";
		$res0 = $this->m_dbConn->select($sql0);
		
		if($res0[0]['cnt']>0)
		{	
			$sql1="update `notices` set status='N' where `id`='".$_REQUEST['noticeId']."'";
			//echo $sql1;
			$this->m_dbConn->update($sql1);
			
			$sql2 = "update `display_notices` set status='N' WHERE `notice_id` = '".$_REQUEST['noticeId']."'";
			$this->m_dbConn->update($sql2);
			
			echo "msg1###".$_SESSION['ssid'].'###'.$_SESSION['wwid'];
		}
		else
		{
			echo "msg";	
		}
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
	
	public function sendEmail($subject, $desc, $noticeToArray, $fileName, $NoticeID, $UnitID, $SocID, $DBName, $bCronjobProcess, $QueueID)
	{
		$emailCount = 0;
		$NoticeBatchSize = 40; //was 10
		$mailSubject = $subject ;
		$mailBody = $desc;
		echo "<br/>Notice to display:". sizeof($noticeToArray) . "  batch size " . $NoticeBatchSize;														
		$display = array();
		$EmailIDtoUnitIDs = array();

		$resAttachment = $this->GetAttachmentFileLink($NoticeID);
		//print_r($resAttachment);
		//die();
		if($resAttachment["notice_version"] == "2" && $resAttachment["Source"] == "2" && $resAttachment["attachment_file"] != "")
		{
			$mailBody .= "<br>Please find attachment :". $resAttachment["attachment_file"];
		}
		//print_r($noticeToArray);														
		//die();
		//echo "<br/>display:".sizeof($noticeToArray);														
		for($i=0;$i<sizeof($noticeToArray);$i++)
		{	
			//echo "noticeToArray:[".$noticeToArray[$i]."]";										
			if($noticeToArray[$i]==0)
			{			
			//echo"[test]";				
				/*$sql = 'SELECT  mem_other_family.other_email, mem_other_family.other_name, member_main.email, member_main.owner_name FROM `mem_other_family` JOIN  `member_main` on mem_other_family.member_id = member_main.member_id JOIN `unit` on unit.unit_id = member_main.unit where unit.society_id = '.$SocID.' AND mem_other_family.send_commu_emails = 1 and member_main.member_id IN (SELECT  member_main.`member_id` FROM (select  `member_id` from `member_main` where `ownership_date` <= "NOW()"  ORDER BY `ownership_date` desc) as member_id Group BY unit)';// Group BY member_main.unit ';
				$result = $this->m_dbConn->select($sql);
				//echo "<br/>result:".$result ."";
				//print_r($result);*/

				$emailIDList = $this->objFetchData->GetEmailIDToSendNotification(0);

				//print_r($emailIDList);

				for($i = 0; $i < sizeof($emailIDList); $i++)
				{	
					if(($emailIDList[$i]['to_email'] <> "") && (isValidEmailID($emailIDList[$i]['to_email']) == true))
					{
						$display[$emailIDList[$i]['to_email']] = $emailIDList[$i]['to_name'];
						$EmailIDtoUnitIDs[$noticeToArray[$i]] = $emailIDList[$i]['to_email'];
					}
				}							
				break;
			}
			else
			{	
			//echo"[test]";						
				/*$sql = "SELECT mem_other_family.other_email, mem_other_family.other_name, member_main.email, member_main.owner_name FROM `mem_other_family` JOIN  `member_main` on mem_other_family.member_id = member_main.member_id JOIN `unit` on unit.unit_id = member_main.unit where unit.society_id = '".$SocID."' AND mem_other_family.send_commu_emails = 1 AND unit.unit_id = '".$noticeToArray[$i]."'  and member_main.member_id IN (SELECT member_main.`member_id` FROM (select  `member_id` from `member_main` where ownership_date <='NOW()' ORDER BY ownership_date desc) as member_id Group BY unit)";// Group BY member_main.unit";  																					
				//echo $sql;
				$result = $this->m_dbConn->select($sql);	*/
				//echo"[test2]<br/>";
				//print_r($result);

				$emailIDList = $this->objFetchData->GetEmailIDToSendNotification($noticeToArray[$i]);
				//print_r($emailIDList);
				for($iResultCnt = 0; $iResultCnt < sizeof($emailIDList); $iResultCnt++)
				{
					if(($emailIDList[$iResultCnt]['to_email'] <> "") && (isValidEmailID($emailIDList[$iResultCnt]['to_email']) == true))
					{
						$display[$emailIDList[$iResultCnt]['to_email']] = $emailIDList[$iResultCnt]['to_name'];
						$EmailIDtoUnitIDs[$i] = $noticeToArray[$i];	
					}
				}
			}							
		}
		//echo "size:".sizeof($display);														
		//print_r($display);
		//die();
		if(sizeof($display) == 0)
		{
			echo '<br>Error 003: Email ID Missing.<br>';
			return;
			//exit();
		}							
												
		// Create the mail transport configuration					
	  $societyEmail = "";	  
	  if($this->objFetchData->objSocietyDetails->sSocietyEmail <> "")
	  {
		 $societyEmail = $this->objFetchData->objSocietyDetails->sSocietyEmail;
	  }
	  else
	  {
		 $societyEmail = "societyaccounts@pgsl.in";
	  }	  
	  try
	  {
		  $bccArray = array();
		  $bccUnitsArray = array();
		  $iLimit = 0;
		  $iCounter = 0;
		  //for($iCnt = 0; $iCnt < sizeof($display); $iCnt++)
		  
		  //echo '<br/>Main Array:' . sizeof($display) . '<br/>';
		  //print_r($display);
		  
		  $obj_utility = new utility($this->m_dbConn, $this->m_dbConnRoot);
		  //echo "<br/>units <".  print_r($EmailIDtoUnitIDs) .">";
		  foreach($display as $key => $value)
		  {
			  
				  $bccEmailForQueueArray[$iLimit] = $value;
				  //echo "counter:<".$iCounter.">";
				//  echo "iLimit:<".$iLimit.">";
			  //echo "EmailIDtoUnitIDs:<".$EmailIDtoUnitIDs[$iCounter] .">";
			  $bccUnitsArray[$iLimit]= $EmailIDtoUnitIDs[$iCounter];
			  //die();
			  $iLimit = $iLimit + 1;
			  $iCounter = $iCounter + 1;
			  $bSendMail = false;
			  if($iLimit == $NoticeBatchSize || $iCounter == sizeof($display))
			  {
				  echo "<BR>sending $iLimit of $NoticeBatchSize notices";
				  $bccArray[$key] = $value;
				  $bccArray['emailtracker@way2society.com'] = $this->objFetchData->objSocietyDetails->sSocietyName;
				  $bSendMail = true;
				  $iLimit = 0;
			  }
			  else
			  {
				  $bccArray[$key] = $value;
			  }
			  
		  		if($bSendMail == true)
				{
					//echo "unitstobcc:".print_r($bccUnitsArray);

					$EMailIDToUse = $this->m_obj_utility->GetEmailIDToUse(true, 1, "", $UnitID, 0, $DBName, $SocID, $NoticeID, $bccUnitsArray);
					
					
					//print_r($EMailIDToUse);
					
					$EMailID = $EMailIDToUse['email'];
					$Password = $EMailIDToUse['password'];
					echo '<br/>Email ID To Use : [' . $EMailID . '][' . $Password . ']';
					//die();
					if($EMailIDToUse['status'] == 0)				
					{
						//if(($emailCount >= 6) && ($emailCount <11))
						//if(($emailCount < 6))
						{
														
							//These settings are for AWS TLS. 		
							/*$SMTP_Username = "AKIAWORPNMPGX76CCAPQ";
							$SMTP_Password = "BOwueG82ahzTYrSgK5igS9qChzA6KKF35obJEEvXTrGe";
							$SMTP_endpoint = "email-smtp.ap-south-1.amazonaws.com";
							$SMTP_Port = 587;
							$SMTP_Security = "tls";
					
							$transport = Swift_SmtpTransport::newInstance($SMTP_endpoint, $SMTP_Port, $SMTP_Security)
							  ->setUsername($SMTP_Username)
							  ->setPassword($SMTP_Password);*/	 
							//AWS Config
				
						$AWS_Config = CommanEmailConfig();
						 $transport = Swift_SmtpTransport::newInstance($AWS_Config[0]['Endpoint'],$AWS_Config[0]['Port'] , $AWS_Config[0]['Security'])
						  			->setUsername($AWS_Config[0]['Username'])
				 			 		->setPassword($AWS_Config[0]['Password']);	 																
							// Create the message
							$message = Swift_Message::newInstance();
							
							if($this->objFetchData->objSocietyDetails->sSocietyEmail <> "")
							{
								$message->setTo(array(
								   $societyEmail => $societyName
								));
							}
							
							$message->setBcc($bccArray);															
							 
							 $message->setReplyTo(array(
							   $societyEmail => $societyName
							));
							
							$message->setSubject($mailSubject);
							$message->setBody($mailBody);
							$message->setFrom($EMailID, $this->objFetchData->objSocietyDetails->sSocietyName);
							$message->setContentType("text/html");
							//echo "src:".$resAttachment["Source"] ;
							//echo "attch:".$resAttachment["attachment_file"];	
							//die();									 
							//$sPath = "https://drive.google.com/uc?authuser=0&id=1k2xk6IQwyhzBZ58bJS6tpmlYESeyzbW2&export=download";
							if($resAttachment["Source"] == "1"  && $resAttachment["attachment_file"] != "")
							{
								echo "<BR>attaching file...";
								//$message->attach(Swift_Attachment::fromPath($resAttachment["attachment_file"]));
								//$message->attach(Swift_Attachment::fromPath("https://drive.google.com/file/d/1k2xk6IQwyhzBZ58bJS6tpmlYESeyzbW2/view"));
								//$message->attach(Swift_Attachment::fromPath("https://drive.google.com/file/d/1k2xk6IQwyhzBZ58bJS6tpmlYESeyzbW2"));
								//$message->attach(Swift_Attachment::fromPath($sPath));
								$message->attach(Swift_Attachment::fromPath('../Notices/' . $resAttachment["attachment_file"]));
							}
							// Send the email
							$mailer = Swift_Mailer::newInstance($transport);
							
							echo "<BR>sending batch $emailCount ... ";
							$result = $mailer->send($message);
							if($result <> 0)
							{							
								$sendind ='Success';
							}
							else
							{
								echo "<br/>Failed";
								$sendind ='Failed';
							}
						}
						/*
						else
						{
							echo "<BR>skipped batch ". $emailCount;
						}
						*/
						$emailCount ++;
						
						echo "<BR>Batch $emailCount Result ". $result;
						foreach($bccUnitsArray as $key => $value)
		  				{
								//echo "<br/>Success";
							$sqlUpdate1 = "Update `display_notices` set `SendNoticeEmailDate`=now(),`SendStatus`='".$sendind."'  WHERE `notice_id` = '".$NoticeID."' and `unit_id`='".$value."'"; 
							$this->m_dbConn->update($sqlUpdate1);
						}
						//echo "<br/>cron:".$bCronjobProcess ."<br/>";
						if($bCronjobProcess)
						{
							//$sqlDelete = "DELETE FROM `emailqueue` WHERE `id` = '".$QueueID."'"; 
							//echo $sqlDelete;
							//$dbConnRoot->delete($sqlDelete);
							$sqlUpdate = "Update `emailqueue` set `Status`=1 WHERE `SourceTableID` = '".$QueueID."' and `Status`=0"; 
							//echo $sqlUpdate;
							$this->m_dbConnRoot->update($sqlUpdate);
						}
						//die();	
					}
					else
					{
						if($EMailIDToUse['status'] == 2)
						{
							
						}
						echo $EMailIDToUse['msg'];
					}
					$bccArray = array();
				}
		  }
	  }
		catch(Exception $exp)
		{
			var_dump($exp);
			//$ResultAry[$unitID] = $exp->getMessage();
			
	//$DBName, $societyID, $unitID, $periodID, $subject, $desc, $noticeToArray, $fileName, $NoticeID, $UnitID, $SocID, $DBName, $bCronjobProcess, $QueueID
/*
			$EmailSourceModule = 3; // 3- for notice
			$NoticeID;
			//set period and society variables
			$periodID=0;
			$societyID=$_SESSION['society_id'] ;
			
		   echo $SQL_query_existCheck = "select * from `emailqueue` where `dbName`='".$DBName."' and `PeriodID`='".$periodID."' and `SocietyID`='".$societyID."' and `UnitID`='".$UnitID."' and `Status`=0 and `ModuleTypeID`='".$EmailSourceModule."'";
echo "<BR>Before select";
		$SQL_query_existCheckRes = $this->m_dbConnRoot->select($SQL_query_existCheck);
		//echo "Already exist count:".sizeof($SQL_query_existCheckRes);
echo "<BR>after select";
var_dump($SQL_query_existCheckRes);
echo "<BR>Before if";
			if(sizeof($SQL_query_existCheckRes) == 0)
			{
				echo "<BR>inserting into queue";
				$queue_query = "insert into `emailqueue`(`dbName`,`PeriodID`, `SocietyID`, `UnitID`, `ModuleTypeID`, `ModuleRefID`) values ('".$DBName."','".$periodID."','".$societyID."','".$UnitID."','".$EmailSourceModule."','".$NoticeID."')";
				//echo "<br/>".$queue_query;
				$dbConnRoot->insert($queue_query);
				//var_dump($exp);
		$ResultAry[$unitID] = "Message added into email queue. ECode : " . $exp->getMessage();
				//return;
			}
			else
			{
				echo "<BR>Already exists into queue";
				$ResultAry[$unitID] = "Already in email queue. ECode : " . $exp->getMessage();  //$exp->getMessage() ;
			}
			*/
		}

	}
	
	//**===============================SMS Template to Send ==============================================//
	
	public function getSMSTemplate($Subject, $IsUpdate, $IsSubChange, $OriginalSub)
	{
		$smsDetails = $this->m_dbConn->select("SELECT `society_name`, `sms_start_text`,`sms_end_text` FROM `society` WHERE `society_id` = '".$_SESSION['society_id']."'");
		
		if($IsUpdate <> 0 && $IsUpdate <> '')
		{	
			if($IsSubChange == 1)
			{
				$NoticeMsg = "".$smsDetails[0]['sms_start_text'].", Notice for ".$OriginalSub." is updated to ".$Subject." . Please login to www.way2society.com to know more details. ".$smsDetails[0]['sms_end_text']."";
				return	$NoticeMsg;
			}
			else if($IsSubChange == 0)
			{
				$NoticeMsg = "".$smsDetails[0]['sms_start_text'].", Notice for ".$Subject." is updated . Please login to www.way2society.com to know more details. ".$smsDetails[0]['sms_end_text']."";
				return	$NoticeMsg;
			}
		}
		else
		{
			$NoticeMsg = "".$smsDetails[0]['sms_start_text'].", Notice for ".$Subject." is generated . Please login to www.way2society.com to know more details. ".$smsDetails[0]['sms_end_text']."";
			return	$NoticeMsg;
		}	
	}	
	
 	///----------------------------------------------Mobile SMS--------------------------------------////
	
	public function SendNoticeSMS($msgBody, $noticeToArray, $NoticeID, $SocID, $DBName)
	{
		//**----Making log file name as SendNoticeSMS.html to track notice sms logs ----**
		$Logfile=fopen("SendNoticeSMS.html", "a");	
		$msg = "<center><b><font color='#003399' >  DATE : </b>".date('Y-m-d')."</font></center> <br /> ";
		fwrite($Logfile,$msg);		
		date_default_timezone_set('Asia/Kolkata');
		
		//***------Fetching details from society to append in msg-----//
		$smsDetails = $this->m_dbConn->select("SELECT `society_name`, `sms_start_text`,`sms_end_text` FROM `society` WHERE `society_id` = '".$SocID."'");																									
					
		$msg = "<b>DBNAME : </b>". $DBName ."<br /><b> SOCIETY : </b>".$smsDetails[0]['society_name']."<br /><b> START TIME : </b>".date('Y-m-d h:i:s ')."<br /><br />";

		fwrite($Logfile,$msg);
				
		$unitDetails = array();
		if($noticeToArray[0] == 0)
			{
				//**----------When all unit are selected then this condition run----//
				
				//echo '<BR> All unit is selected ';
				$units = $this->m_dbConn->select("SELECT u.id, u.unit_no, mm.mob,mm.alt_mob,u.unit_id FROM `unit` AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit WHERE u.society_id = '".$SocID."'" );
				//$unitDetails = $units;
				for($j = 0; $j < sizeof($units); $j++)
					{
						array_push($unitDetails, $units[$j]);
					}	

			}
			
		else if($noticeToArray[0] <> 0)
			{
				//** -----------When multiple selection of unit or single -----**
				
				//echo '<BR>Multiple selection';
				for($i = 0 ; $i < sizeof($noticeToArray) ; $i++)
				{
					//echo '<BR>Multiple Selection for loop';
					$units = $this->m_dbConn->select("SELECT u.id, u.unit_no, mm.mob, mm.alt_mob, u.unit_id FROM `unit` AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit WHERE u.society_id = '".$SocID."' AND u.unit_id = '".$noticeToArray[$i]."'");
					for($j = 0; $j < sizeof($units); $j++)
					{
						array_push($unitDetails, $units[$j]);
					}
				}
			}
		//echo '<BR>Size of push array'.sizeof($unitDetails);
		
		//** --------- Now further code execute for requested unit---**
		for($i = 0 ; $i < sizeof($unitDetails) ; $i++)
		{
			//echo '<BR>After getting array values';
			
			//**-----Check mobile number exits---**
				if($unitDetails[$i]['mob'] <> '' && $unitDetails[$i]['mob'] <> 0)
				{	
					//echo '<BR> We got some mobile number '.$unitDetails[$i]['mob'];
					$smsText = $msgBody;
					
					//**Check for client id 	
					$clientDetails = $this->m_dbConnRoot->select("SELECT `client_id` FROM  `society` WHERE  `dbname` ='".$DBName."' ");
				
					if(sizeof($clientDetails) > 0)
					{
						$clientID = $clientDetails[0]['client_id'];
						//echo '<BR> Client ID is '.$clientID;
					}
			
					
					//**---Calling SMS function for utility---***
					$response =  $this->m_obj_utility->SendSMS($unitDetails[$i]['mob'], $smsText, $clientID);
					
					//echo '<BR>Response of Send SMS '.$response;
					//echo '<BR>Response'.$response ;
					//echo '<BR>ResultAry'.$ResultAry[$unitDetails[$i]['unit_id']];
					$ResultAry[$unitDetails[$i]['unit_id']] =  $response;
					$status = explode(',',$response);	
					//echo '<BR>Status'.$status[1];	
					$res= "Update `display_notices` set `SendNoticeSMSDate`=now(), `sms_Status`='".$status[1]."' where `notice_id` = '".$NoticeID."' and `unit_id`='".$unitDetails[$i]['unit_id']."'"; 
					$this->m_dbConn->update($res);
					$msg = "<b>** INFORMATION ** </b>Unit - '".$unitDetails[$i]['unit_no']."' : Message Sent['".$smsText."']. <br /><br />";
					fwrite($Logfile,$msg);
					
					$current_dateTime = date('Y-m-d h:i:s ');
					
					//***----Inserting the response ------------**
					//$res = $this->m_dbConn->select("INSERT INTO `generalsms_log`(`UnitID`, `SentGeneralSMSDate`, `MessageText`, `SentBy`, `SentReport`, `status`) VALUES ('".$unitDetails[$i]['unit_id']."','".$current_dateTime."','". $smsText ."','".$_SESSION['login_id']."', '".$ResultAry[$unitDetails[$i]['unit_id']]."', '".$status[0]."')");	
				}
				else
				{
					$msg = "<b>** ERROR ** </b>Unit - '".$units[$i]['unit_no']."' : Invalid Mobile Number. <br /><br />";
					fwrite($Logfile,$msg);
				}
			
			
				
		}
		$msg = "<b> END TIME : </b>".date('Y-m-d h:i:s ')."<br /><hr />";
		fwrite($Logfile,$msg);
		
		return true;
	}
	
	
	
	
	
	
	///----------------------------------------------Mobile Notification ----------------------------///
	
	public function SendMobileNotification($subject, $noticeToArray, $NoticeID, $SocID, $DBName)
	{
		$NoticeTitle="Society Notice";
		$NoticeMassage = $subject ;
		$display = array();
		$EmailIDtoUnitIDs = array();					
		
		$emailIDList = $this->objFetchData->GetEmailIDToSendNotification(0);														
		for($i=0;$i<sizeof($noticeToArray);$i++)
		{			
				for($i = 0; $i < sizeof($emailIDList); $i++)
				{	
					if(($emailIDList[$i]['to_email'] <> "") )
					{
						
						$UnitID = $emailIDList[$i]['unit'];
						
						$objAndroid = new android($emailIDList[$i]['to_email'], $SocID, $UnitID);
						//NoticeToArray if it is 0 then notification goes to all units
						if($noticeToArray[0] == 0 && $emailIDList[$i]['to_email'] <> "")
						{
							$sendMobile=$objAndroid->sendNoticeNotification($NoticeTitle,$NoticeMassage,$NoticeID);
						}
						else if($noticeToArray[0] <> 0 && $emailIDList[$i]['to_email'] <> "")
						{
						//NoticeToArray if it is not 0 then notification goes to selected units	
							if(in_array($UnitID, $noticeToArray))
							{
								$sendMobile=$objAndroid->sendNoticeNotification($NoticeTitle,$NoticeMassage,$NoticeID);
							}
						}

						$Notification_response = json_decode($sendMobile,true);
						if($Notification_response['status'] == 1)
						{
							//echo "Notification" ;
							
							$status="Success";
						}
						else
						{
							//echo "Notify";
							$status="Failed";
						}
						
						  $res="Update `display_notices` set `SendNotificationDate`=now(), `Mobile_Notification`='".$status."' where `notice_id` = '".$NoticeID."' and `unit_id`='".$UnitID."'"; 
						$this->m_dbConn->update($res);
						
					}	
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
	public function GetUnitDescriptionsFromNotices()
	{
		$sqlQuery = "select distinct(unit_id) from display_notices";
		$res = $this->m_dbConn->select($sqlQuery);
		return $res;
	}
	public function getComment($ID)
	{
		$comment="select * from reversal_credits where ID='".$ID."' ";
		//echo $comment;
		$com=$this->m_dbConn->select($comment);
		//print_r($com);
		return $com;
	}
	public function getMemberCount()
	{
		$sql = "Select count(*) as cnt from mem_other_family where status = 'Y'";
		$count = $this->m_dbConn->select($sql);
		return $count;
	}
	
	function resendNotification($Notice_id, $sms_text, $notification)
	{
		try
		{
			// Notice _id and Notifications
		
			$noticeToArray = array();
			
			$notice_query = "SELECT subject, description  FROM `notices` WHERE id = '".$Notice_id."'";
			$notice_result = $this->m_dbConn->select($notice_query);
			
			$notice_display_query = "SELECT unit_id FROM `display_notices` WHERE notice_id = '".$Notice_id."' and `status` = 'Y'";
			$notice_display_result = $this->m_dbConn->select($notice_display_query);
			
			if(!empty($notice_display_result))
			{
				foreach($notice_display_result as $notice_display)
				{
					array_push($noticeToArray, $notice_display['unit_id']);	
				}
			}
			
			if(!empty($notice_result) && !empty($noticeToArray))
			{
				//Send Email Notification to selected people in notice
				if($notification['email'])
				{																
					$this->sendEmail($notice_result[0]['subject'],$notice_result[0]['description'], $noticeToArray,"",$Notice_id, $noticeToArray[0], $_SESSION['society_id'], $_SESSION["dbname"],0,0);
				}
				
				//Send Mobile Notification to selected people in notice	
				if($notification['mobile'])
				{	
					$this->SendMobileNotification($notice_result[0]['subject'], $noticeToArray, $Notice_id, $_SESSION['society_id'], $_SESSION['dbname']);
				}	
				
				//Send SMS Notification to selected people in notice		
				if($notification['sms'])
				{
					$this->SendNoticeSMS($sms_text, $noticeToArray, $Notice_id, $_SESSION['society_id'], $_SESSION['dbname']);
				}	
			}
			
			return 1;	
		}
		catch(EXCEPTION $e)
		{
			return $e->getMessage();
		}
		
	}
	
	
	
	function getGroupMembers($gId)
    {
		$sql = "";
		$memId =  array();
		$memDetails = "";
		if($gId == "AO")
		{
			$sql = "Select u.`unit_id` as MemberId, CONCAT(u.`unit_no`,'-',m.`primary_owner_name`,' (Owner)') as other_name from member_main as m, unit as u where m.ownership_status = '1' and m.`unit` = u.`unit_id`" ;
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "ACO")
		{
			$sql = "Select u.`unit_id` as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Co-Owner)') as other_name from mem_other_family as mof,unit as u,member_main as m where mof.coowner = '2' and mof.`member_id` = m.`member_id` and u.`unit_id` = m.`unit`" ;
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "AOCO")
		{
			$sql = "Select u.`unit_id` as MemberId, CONCAT(u.`unit_no`,'-',m.`primary_owner_name`,' (Owner)') as other_name from member_main as m, unit as u where m.ownership_status = '1' and m.`unit` = u.`unit_id` union 
			Select mof.`mem_other_family_id` as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Co-Owner)') as other_name from mem_other_family as mof,unit as u,member_main as m where mof.coowner = '2' and mof.`member_id` = m.`member_id` and u.`unit_id` = m.`unit`" ;
			//echo $sql;
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "AR")
		{
			$sql = "Select CONCAT('M-',u.`unit_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '1' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',u.`unit_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Co-Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '2' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',u.`unit_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Resident)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner` = '0' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('T-',u.`unit_id`) as MemberId,CONCAT(u.`unit_no`,'-',t.`mem_name`,' (Tenant)') as other_name from tenant_module as tm,tenant_member as t,unit as u where tm.Status = 'Y' and tm.`unit_id` = u.`unit_id` and t.`tenant_id` = tm.`tenant_id`";
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "ART")
		{
			$sql = "Select DISTINCT(mm.`unit`) as MemberId, mm.`owner_name` as other_name from member_main as mm where mm.`status` = 'Y' union Select DISTINCT(t.`unit_id`) as MemberId, t.`tenant_name` as other_name from  tenant_module as t where t.`active` = '1' and t.`status` = 'Y'" ;
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "ACM")
		{
			$sql = "Select DISTINCT(u.`unit_id`) as MemberId, CONCAT(u.`unit_no`,'-',M.`other_name`) as other_name from mem_other_family as M, commitee as C,unit as u,member_main as mm where M.status = 'Y' and M.mem_other_family_id = C.member_id and mm.`member_id` = M.`member_id` and mm.`unit` = u.`unit_id`" ;
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "AT")
		{
			$sql = "Select unit_id as MemberId, tenant_name as other_name from tenant_module where status = 'Y'";
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "AVO")
		{
			$sql = "SELECT u.`unit_id` as MemberId, CONCAT(u.`unit_no`,'-',mof.other_name) as other_name FROM `mem_other_family` mof, `mem_car_parking` mcp,member_main m,unit u where mcp.member_id = mof.member_id and mcp.status ='Y' and m.`member_id` = mof.`member_id` and m.`unit` = u.`unit_id`" ;
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "ALH")
		{
			$sql = "Select u.`unit_id` as MemberId, CONCAT(u.`unit_no`,'-',M.owner_name) as other_name from mortgage_details as L, member_main as M,unit as u where L.Status = 'Y' and L.LienStatus = 'Open' and M.member_id = L.member_id and u.`unit_id` = m.`unit`";
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "0")
		{
			
			$sql = "select u.`unit_id` as MemberId, CONCAT(CONCAT(u.unit_no,' '), mm.owner_name) AS 'other_name' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.society_id = '" . $_SESSION['society_id'] . "' and ownership_status = 1 ORDER BY u.sort_order";
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "MHT")
		{
			$sql = "Select mm.unit as MemberId, mof.other_name as other_name from tenant_module as tm join member_main as mm on mm.unit=tm.unit_id join mem_other_family as mof on mof.member_id = mm.member_id group by mm.unit";
			$memId = $this->m_dbConn->select($sql);
		} 
		else
		{
			$WGroup = substr($gId, 0,1); 
			if($WGroup == "W")
			{
				$sql = "select u.`unit_id` as MemberId, CONCAT(CONCAT(u.unit_no,' '), mm.owner_name) AS 'other_name' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.society_id = '" . $_SESSION['society_id'] . "' and u.wing_id ='".substr($gId,1)."' and ownership_status = 1 ORDER BY u.sort_order";
				$memId = $this->m_dbConn->select($sql);
			}
			else
			{
			
				$sql = "Select CONCAT('M-',u.`unit_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '1' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',u.`unit_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Co-Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '2' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',u.`unit_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Resident)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner` = '0' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('T-',u.`unit_id`) as MemberId,CONCAT(u.`unit_no`,'-',t.`mem_name`,' (Tenant)') as other_name from tenant_module as tm,tenant_member as t,unit as u where tm.Status = 'Y' and tm.`unit_id` = u.`unit_id` and t.`tenant_id` = tm.`tenant_id`";
			$resultMem = $this->m_dbConn->select($sql);
			$sql2 = "Select MemberId from membergroup_members where GroupId = '".$gId."' and Status = 'Y'";
			$resultGMem =  $this->m_dbConn->select($sql2);
			$k=0;
			for($i=0;$i<sizeof($resultGMem);$i++)
			{
				for($j=0;$j<sizeof($resultMem);$j++)
				{
					if($resultGMem[$i]['MemberId'] == $resultMem[$j]['MemberId'])
					{
						$memId[$k]['MemberId'] = $resultMem[$j]['MemberId'];
						$memId[$k]['other_name'] = $resultMem[$j]['other_name'];
						$k=$k+1;
					}
				}
			}
		}
		}
		//echo "sql = ".$sql;
		//echo "<pre>";
		//print_r($memId);
		//echo "</pre>";
        if(sizeof($memId)>0)
        { 
           	$memRes = "<option value ='0' selected='selected'>&nbsp;All</option>";
    		for($i = 0; $i < sizeof($memId); $i++)
          	{ 
            	$memRes .= "<option value=".$memId[$i]['MemberId'].">&nbsp;".$memId[$i]['other_name']."</option>";  
          	}
		}
		else
		{
			$memRes = "<option>No Member found under selected category.</option>";
		}
		return($memRes);
	}
	function getGroupMemberForUnit($unitid)
	{
			 $sql = "Select u.`unit_id` as MemberId, CONCAT(u.`unit_no`,'-',m.`primary_owner_name`,' (Owner)') as other_name from member_main as m, unit as u where m.ownership_status = '1' and m.`unit` = u.`unit_id` and u.unit_id in (".$unitid.")" ;
			$memId = $this->m_dbConn->select($sql);
			return($memId);
	}
	function getGroupMemberForAll($gId)
    {
		$sql = "";
		$memId = array();
		$memDetails = "";
		if($gId == "AO")
		{
			$sql = "Select u.`unit_id` as MemberId, CONCAT(u.`unit_no`,'-',m.`primary_owner_name`,' (Owner)') as other_name from member_main as m, unit as u where m.ownership_status = '1' and m.`unit` = u.`unit_id`" ;
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "ACO")
		{
			$sql = "Select u.`unit_id` as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Co-Owner)') as other_name from mem_other_family as mof,unit as u,member_main as m where mof.coowner = '2' and mof.`member_id` = m.`member_id` and u.`unit_id` = m.`unit`" ;
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "AOCO")
		{
			$sql = "Select u.`unit_id` as MemberId, CONCAT(u.`unit_no`,'-',m.`primary_owner_name`,' (Owner)') as other_name from member_main as m, unit as u where m.ownership_status = '1' and m.`unit` = u.`unit_id` union 
			Select mof.`mem_other_family_id` as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Co-Owner)') as other_name from mem_other_family as mof,unit as u,member_main as m where mof.coowner = '2' and mof.`member_id` = m.`member_id` and u.`unit_id` = m.`unit`" ;
			//echo $sql;
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "AR")
		{
			$sql = "Select CONCAT('M-',u.`unit_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '1' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',u.`unit_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Co-Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '2' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',u.`unit_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Resident)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner` = '0' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('T-',u.`unit_id`) as MemberId,CONCAT(u.`unit_no`,'-',t.`mem_name`,' (Tenant)') as other_name from tenant_module as tm,tenant_member as t,unit as u where tm.Status = 'Y' and tm.`unit_id` = u.`unit_id` and t.`tenant_id` = tm.`tenant_id`";
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "ART")
		{
			$sql = "Select DISTINCT(mm.`unit`) as MemberId, mm.`owner_name` as other_name from member_main as mm where mm.`status` = 'Y' union Select DISTINCT(t.`unit_id`) as MemberId, t.`tenant_name` as other_name from  tenant_module as t where t.`active` = '1' and t.`status` = 'Y'" ;
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "ACM")
		{
			$sql = "Select DISTINCT(u.`unit_id`) as MemberId, CONCAT(u.`unit_no`,'-',M.`other_name`) as other_name from mem_other_family as M, commitee as C,unit as u,member_main as mm where M.status = 'Y' and M.mem_other_family_id = C.member_id and mm.`member_id` = M.`member_id` and mm.`unit` = u.`unit_id`" ;
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "AT")
		{
			$sql = "Select unit_id as MemberId, tenant_name as other_name from tenant_module where status = 'Y'";
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "AVO")
		{
			$sql = "SELECT u.`unit_id` as MemberId, CONCAT(u.`unit_no`,'-',mof.other_name) as other_name FROM `mem_other_family` mof, `mem_car_parking` mcp,member_main m,unit u where mcp.member_id = mof.member_id and mcp.status ='Y' and m.`member_id` = mof.`member_id` and m.`unit` = u.`unit_id`" ;
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "ALH")
		{
			$sql = "Select u.`unit_id` as MemberId, CONCAT(u.`unit_no`,'-',M.owner_name) as other_name from mortgage_details as L, member_main as M,unit as u where L.Status = 'Y' and L.LienStatus = 'Open' and M.member_id = L.member_id and u.`unit_id` = m.`unit`";
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "0")
		{
			
			$sql = "select u.`unit_id` as MemberId, CONCAT(CONCAT(u.unit_no,' '), mm.owner_name) AS 'other_name' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.society_id = '" . $_SESSION['society_id'] . "' and ownership_status = 1 ORDER BY u.sort_order";
			$memId = $this->m_dbConn->select($sql);
		}
		else
		{
			$sql = "Select CONCAT('M-',u.`unit_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '1' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',u.`unit_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Co-Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '2' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',u.`unit_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Resident)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner` = '0' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('T-',u.`unit_id`) as MemberId,CONCAT(u.`unit_no`,'-',t.`mem_name`,' (Tenant)') as other_name from tenant_module as tm,tenant_member as t,unit as u where tm.Status = 'Y' and tm.`unit_id` = u.`unit_id` and t.`tenant_id` = tm.`tenant_id`";
			$resultMem = $this->m_dbConn->select($sql);
			$sql2 = "Select MemberId from membergroup_members where GroupId = '".$gId."' and Status = 'Y'";
			$resultGMem =  $this->m_dbConn->select($sql2);
			$k=0;
			for($i=0;$i<sizeof($resultGMem);$i++)
			{
				for($j=0;$j<sizeof($resultMem);$j++)
				{
					if($resultGMem[$i]['MemberId'] == $resultMem[$j]['MemberId'])
					{
						//echo "in if";
						$memId[$k]['MemberId'] = $resultMem[$j]['MemberId'];
						$memId[$k]['other_name'] = $resultMem[$j]['other_name'];
						$k=$k+1;
					}
				}
			}
		}
		return($memId);
	}
		
	function FetchActivities($noticeId)
	{
		//$select ="select* from `display_notices` where notice_id='".$noticeId."'";
		 $select ="select d.id,u.unit_no,m.owner_name,d.SendStatus,d.SendNoticeEmailDate,d.sms_status,d.Mobile_Notification,d.SendNotificationDate,d.SendNoticeSMSDate from display_notices as d join member_main as m on m.unit=d.unit_id join unit as u on d.unit_id=u.unit_id where d.notice_id='".$noticeId."' AND m.ownership_status = 1";		
		
		$result =  $this->m_dbConn->select($select);
		return $result;
	}
}
?>
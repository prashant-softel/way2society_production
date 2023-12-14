<?php
include_once("dbconst.class.php");
include_once("include/dbop.class.php");
include_once( "include/fetch_data.php");
include_once("include/display_table.class.php");
include_once("utility.class.php");
include_once("email.class.php");
include_once('../swift/swift_required.php');
//include_once('email_format.class.php');
//require_once('swift/swift_required.php');
//error_reporting(0);
class approval_module
{
	public $actionPage = "../approvals.php?type=active";
	public $m_dbConn;
	public $objFetchData;
	public $m_dbConnRoot;
	public $m_obj_utility;
	
	function __construct($dbConn, $dbConnRoot,$socID = "")
	{
		$dbConnRoot=new dbop(true);
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->objFetchData = new FetchData($dbConn,$dbConnRoot);
		$this->m_obj_utility = new utility($dbConn, $dbConnRoot);
		
		
		//echo "soc check".$socID;
		if($socID <> "")
		{
			$this->objFetchData->GetSocietyDetails($socID);	
		}
		else
		{
			$this->objFetchData->GetSocietyDetails($_SESSION['society_id']);
		}
		//$this->display_pg=new display_table();
	
		//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
//		$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);

		///dbop::__construct();
	}

	public function startProcess()
	{
		$errorExists = 0;
		if($_REQUEST['insert']=='Submit' && $errorExists==0)
		{
			
			$SendEmail= false;
			$SendSMS = false;
			if(isset($_POST['notify']))
			{
				$email_notify = $_POST['notify'];
				$SendEmail=true;
			}
			else
			{
				$email_notify = "0";
				$SendEmail=false;
			}
			
			if(isset($_POST['sms_notify']))
			{
				$sms_notify = $_POST['sms_notify'];
				$SendSMS=true;
			}
			else
			{
				$sms_notify = "0";
				$SendSMS=false;
			}
			 $cur_date = date('Y-m-d');
			
			 $insert_query="insert into approval(`issued_by`,`subject`,`description`,`Approvals_selected_count`,`Approval_required_count`,`post_date`,`created_date`,`end_date`,`notify_email`,`sms_notify`) values('".$_SESSION['name']."','".$_POST['subject']."','".$_POST['description']."','".$_POST['total']."','".$_POST['min']."','".getDBFormatDate($_POST['post_date'])."','".$cur_date."','".getDBFormatDate($_POST['exp_date'])."','".$email_notify."','".$sms_notify."')";
			$data = $this->m_dbConn->insert($insert_query);
			//$data =1;
			if($data <> '')
			{
				
				 $Get_approval=$_POST['approval'];
				// var_dump($Get_approval[0]);
				 if($Get_approval[0] > '0')
				 {
						for($i=0;$i<sizeof($Get_approval);$i++)
						{
							$sql="insert into approval_status(approval_id,member_id) values('".$data."','".$Get_approval[$i]."')";
							$res=$this->m_dbConn->insert($sql);
						}	
				 }
				 else
				 {
					 //echo $_POST['total'];
					 $select="select member_id from commitee";
					 $members=$this->m_dbConn->select($select);
					 for($i=0;$i<sizeof($members);$i++)
					 {
						$sql="insert into approval_status(approval_id,member_id) values('".$data."','".$members[$i]['member_id']."')";
						$res=$this->m_dbConn->insert($sql); 
					 }
				 }
				// die();
				
				if($SendEmail== true)
				{
					//echo "call Send EMails".
					$this->sendApprovalEmail($_POST['subject'],$_POST['description'], $data, $_SESSION['society_id'],$_POST['post_date'],$_POST['exp_date']);
				}
			}
			//echo "Upload Documents";
			$select = "select society_code from `society` where `society_id` = '".$_SESSION['society_id']."' ";
			$query = $this->m_dbConn->select($select);
			if(!file_exists("../Approval_documents"))
			{
				mkdir("../Approval_documents",0777, true);
			}
			else
			{
				mkdir("../Approval_documents/".$query[0]['society_code']."",0777, true);
			}
			$fileCount = $_POST['doc_count'];
			//$cnt=1;
			for($i=1; $i<=$fileCount; $i++)
			{
				$fileName=$_POST['doc_name_'.$i]; 
				$file= $_FILES['userfile_'.$i]['name'];
				$file_type=$_FILES['userfile_'.$i]['type'];
				$file_size=$_FILES['userfile_'.$i]['size'];
				$file_tmp=$_FILES['userfile_'.$i]['tmp_name'];
				
				$temp = explode(".", $file);
				$extension = end($temp);
				$fileName1 = $fileName.'_'.$_SESSION['society_id'].'_'.date("Ymdhis");
				$newfilename = $fileName1.'.'.$extension;
				$destination = "../Approval_documents/".$query[0]['society_code']."/".$newfilename;
				move_uploaded_file($file_tmp, $destination);
				$sql_doc="insert into approval_documents(approval_id, doc_name, attachment) values('".$data."','".$fileName."','".$newfilename."')";
				$results=$this->m_dbConn->insert($sql_doc);
				//$cnt++;
			}
			?>
                    <script>window.location.href = '../approvals.php?type=active';</script>
                    <?php
			//echo "INsert";			
			return "Insert";
			
		}
		
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			//$up_query="update approval set `issued_by`='".$_POST['issueby']."',`post_date`='".$_POST['post_date']."',`end_date`='".$_POST['exp_date']."',`subject`='".$_POST['subject']."',`notify_email`='".$_POST['notify']."',`attachment`='".$_POST['userfile']."',`description`='".$_POST['description']."' where id='".$_POST['id']."'";
			//$data = $this->update($up_query);
			
			$up_query="update approval set `post_date`='".getDBFormatDate($_POST['post_date'])."',`end_date`='".getDBFormatDate($_POST['exp_date'])."',`subject`='".$_POST['subject']."',`notify_email`='".$_POST['notify']."',`description`='".$_POST['description']."',`Approvals_selected_count` ='".$_POST['total']."',`Approval_required_count` ='".$_POST['min']."' where id='".$_POST['id']."'";
			$data = $this->m_dbConn->update($up_query);
			//echo "<br><br>";
			$deletequery = "delete from approval_status where approval_id ='".$_POST['approval_Id']."'";
			$deletestatus = $this->m_dbConn->delete($deletequery);
			$Get_approval=$_POST['approval'];
			
			for($i=0;$i<sizeof($Get_approval);$i++)
			{ 
				//echo "<br><br>";
				$sql="insert into approval_status(approval_id,member_id) values('".$_POST['approval_Id']."','".$Get_approval[$i]."')";
				$res=$this->m_dbConn->insert($sql);
			}
			?>
                    <script>window.location.href = '../approvals.php?type=active';</script>
                    <?php
			return "Update";
		}
		else
		{
			return $errString;
		}
				
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
	
	public function CMemeberCount()
	{
		$query ="select Count(C.member_id) as MemberIdCount from mem_other_family as M, commitee as C where M.status = 'Y' and M.mem_other_family_id = C.member_id";
		$resCount = $this->m_dbConn->select($query);
		$count= $resCount[0]['MemberIdCount'];
		return $count;
	}
	
	function approval_view($id)
	{
		$sql = "select * from approval where status='Y' and id='".$id."'";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	function View($id)
	{
		$FinalArray=array();
		$Approval_status = "";
		$Vote_result = "Not Voted";
		$sqlquety="select * from approval_status where approval_id='".$id."'";
		$res=$this->m_dbConn->select($sqlquety);
		for($i=0;$i<sizeof($res);$i++)
		{
			$comments =$res[$i]['comments']; 
			///$sql1="select other_name from mem_other_family where mem_other_family_id='".$res[$i]['member_id']."'";
			$sql1 = "SELECT c.member_id as MemberId, CONCAT(CONCAT(u.`unit_no`,'-',mof.`other_name`), CONCAT(u.unit_no, IF(mof.coowner = 1, ' (Owner)', ' (Co-Owner)'))) AS 'other_name' FROM `member_main` as mm join unit as u on mm.unit=u.unit_id join mem_other_family as mof on mof.member_id=mm.member_id join commitee as c on mof.mem_other_family_id = c.member_id where mof.mem_other_family_id='".$res[$i]['member_id']."' ";
			$res1=$this->m_dbConn->select($sql1);
			$mem_name=$res1[0]['other_name'];
			if($res[$i]['approval_status'] == 1)
			{
				$Approval_status ='Yes';
				$Vote_result = 'Voted';
			}
			else if($res[$i]['approval_status'] == 2)
			{
				$Approval_status = 'No';
				$Vote_result='Voted';
			}
			else
			{
				$Approval_status ='';
				$Vote_result = 'Not Voted';
			}
			array_push($FinalArray,array('mem_name'=>$mem_name,'Approval_Status' =>$Approval_status, 'Vote_Result'=>$Vote_result,'comments'=>$comments));				 
		}	
		return $FinalArray;
	}
	function View_attachments($id)
	{
		$finalArray=array();
		$sqlquery="select * from approval_documents where approval_id='".$id."'";
		$result = $this->m_dbConn->select($sqlquery);
		$select = "select society_code from `society` where `society_id` = '".$_SESSION['society_id']."' ";
		$res = $this->m_dbConn->select($select);
		$society_code = $res[0]['society_code'];
		for($i=0;$i<sizeof($result);$i++)
		{
			$Url = '../Approval_documents/'.$society_code.'/'.$result[$i]['attachment']; // server
			//$Url = '../master5/Approval_documents/'.$society_code.'/'.$result[$i]['attachment']; // localhost
			$doc_name = $result[$i]['doc_name'];
			array_push($finalArray,array('Url'=>$Url,'Doc_name' =>$doc_name));		
		}
		
		return $finalArray;
	}
	
	
	function FetchAllApprovals($type)
	{
		 $date = date('Y-m-d');
		if($type == "active")
		{
			$query ="Select * from approval where status='Y' and end_date >='".$date."' order by id DESC";
		}
		else
		{
			$query ="Select * from approval where status='Y' and end_date <'".$date."' order by id DESC";
		}
		//echo $query;
			$result=$this->m_dbConn->select($query);
			for($i=0;$i<sizeof($result);$i++)
			{
				$checked = "SELECT count(approval_status) as count FROM `approval_status` where approval_id = '".$result[$i]['id']."' and approval_status not in(0)";
				$result1=$this->m_dbConn->select($checked);
				if($result1[0]['count'] > 0)
				{
					$result[$i]['votedcount'] =$result1[0]['count']; 
				}
				else
				{
					$result[$i]['votedcount'] ='0';
				}
			}
		return($result);
	}
	function ApprovalVote($id)
	{
		$date = date('Y-m-d');
		$query ="Select * from approval where id='".$id."' and status='Y'";		
		$result=$this->m_dbConn->select($query);
		return($result);
	}
	function TakeFeedback($approvalId,$menberId)
	{
		//$date = date('Y-m-d');
		$query ="Select * from approval_status where approval_id='".$approvalId."' and 	member_id = '".$menberId."'";		
		$result=$this->m_dbConn->select($query);
		return($result);
	}
	function getGroupMemberForUnit($unitid)
	{
			 $sql = "Select u.`unit_id` as MemberId, CONCAT(u.`unit_no`,'-',m.`primary_owner_name`,' (Owner)') as other_name from member_main as m, unit as u where m.ownership_status = '1' and m.`unit` = u.`unit_id` and u.unit_id in (".$unitid.")" ;
			$memId = $this->m_dbConn->select($sql);
			return($memId);
	}
	
	public function combobox($query)
	{
	}
	public function display1($rsas)
	{
		$thheader = array('group','type','issue','sub','email','mob','attach','desc');
		$this->display_pg->edit		= "getapproval_module";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "approval_module.php";

		$res = $this->display_pg->display_new($rsas);
		return $res;
	}
	public function pgnation()
	{
		$sql1 = "select id,`group`,`type`,`issue`,`sub`,`email`,`mob`,`attach`,`desc` from approval where status='Y'";
		$cntr = "select count(status) as cnt from approval where status='Y'";

		$this->display_pg->sql1		= $sql1;
		$this->display_pg->cntr1	= $cntr;
		$this->display_pg->mainpg	= "approval_module.php";

		$limit	= "50";
		$page	= $_REQUEST['page'];
		$extra	= "";

		$res	= $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}
	public function selecting()
	{
		//$sql = "select id,`group`,`type`,`issue`,`sub`,`email`,`mob`,`attach`,`desc` from approval where id='".$_REQUEST['approval_moduleId']."'";
		$sql = "select `id`,`issued_by`,`post_date`,`end_date`,`subject`,`notify_email`,`sms_notify`,`Approvals_selected_count`,`Approval_required_count`,`description` from approval where id = '".$_REQUEST['approval_moduleId']."'";
		$res = $this->m_dbConn->select($sql);
		if($res <> '')
		{
			$res[0]['post_date'] = getDisplayFormatDate($res[0]['post_date']);
			$res[0]['end_date'] = getDisplayFormatDate($res[0]['end_date']);
			$sql1 = "select `id`,`doc_name`,`attachment` from approval_documents where approval_id = '".$_REQUEST['approval_moduleId']."'";
			$res1 = $this->m_dbConn->select($sql1);
			if($res1 <> '')
			{
				for($i=0;$i<sizeof($res1);$i++)
				{
					//$res[0]['documents'] .= $res1[$i]['doc_name'].",";	
					//$res[0]['attacment'] .= $res1[$i]['attachment'].",";
					$res[0]['documents'] .= $res1[$i]['id']."#".$res1[$i]['doc_name']."#".$res1[$i]['attachment'].",";		
				}
			}
			
			$sql2 = "select `id`,`member_id` from approval_status where approval_id = '".$_REQUEST['approval_moduleId']."'";
			$res2 = $this->m_dbConn->select($sql2);
			if($res2 <> '')
			{
				for($j=0;$j<sizeof($res2);$j++)
				{
					$res[0]['mem_id'] .=$res2[$j]['member_id'] .",";
				}
			}
		}
		return $res;
	}
	public function getDocuments($approvalId)
	{
		$sql="SELECT * from approval_documents where `approval_id`=".$approvalId." AND status='Y'";
		$res=$this->m_dbConn->select($sql);
		return $res;
	}
	public function deleting()
	{
		$sql = "update approval set status='N' where id='".$_REQUEST['approval_moduleId']."'";
		$res = $this->m_dbConn->update($sql);
		//return $res;
	}
	public function getAccessForApproval($Approval_id)
	{
		//$select="SELECT mof.mem_other_family_id, mm.member_id FROM `member_main` as mm join mem_other_family as mof on mm.member_id=mof.member_id where mm.unit='".$_SESSION['unit_id']."'";
		$select = "SELECT mof.mem_other_family_id, mm.member_id FROM `member_main` as mm join mem_other_family as mof on mm.member_id=mof.member_id join approval_status as ap on ap.member_id=mof.mem_other_family_id where mm.unit='".$_SESSION['unit_id']."' and ap.approval_id='".$Approval_id."'";
		$res = $this->m_dbConn->select($select);
		
		return $res;
	} 
	
	public function SubmitFeedback($approvalID,$comments,$login_id,$selectOption,$OtherMemId)
	{
		 $updatefeedback ="update approval_status set `login_id`='".$login_id."',`approval_status`='".$selectOption."',comments='".$comments."',`timestamp`=NOW() where approval_id='".$approvalID."' and member_id='".$OtherMemId."'  ";
		$res = $this->m_dbConn->update($updatefeedback);
		return $res;
	}
	
	public function sendApprovalEmail($subject, $description,$ApproveId,$SocietyId,$postDate,$expDate)
	{
		include_once('email_format.class.php');
		date_default_timezone_set('Asia/Kolkata');
		$societyName =  $this->objFetchData->objSocietyDetails->sSocietyName;
		$sql= "select * from approval_status where approval_id = '".$ApproveId."'";
		$Result=$this->m_dbConn->select($sql);
		$display = array();	
		if($Result <> '')
		{
			//echo "Call Email 1<br>";
			for($i=0;$i<sizeof($Result);$i++)
			{
				 $selectQry="select other_email,other_name from `mem_other_family` where `mem_other_family_id`='".$Result[$i]['member_id']."'";
				 $memEmail=$this->m_dbConn->select($selectQry);
				 if($memEmail[0]['other_email'] <> "")
				 {
					$display[$memEmail[0]['other_email']] = $memEmail[0]['other_name'];
				}
			}
		} 
		//var_dump($display); 
		
		$mailSubject = "Approval Required : ".$subject;
		
		//$url="<a href='http://way2society.com/approval_vote.php?moduleId=".$ApproveId. "'>Click here...</a>";
		//$url="http://way2society.com/approval_vote.php?moduleId=".$ApproveId."";
		$onclickURL = 'https://way2society.com/Dashboard.php?View=ADMIN';
		$url = 'https://way2society.com/approval_vote.php?moduleId='.$ApproveId;
		//$onclickURL = 'localhost/master5/Dashboard.php?View=ADMIN';
		//$url = 'localhost/master5/approval_vote.php?moduleId='.$ApproveId;
		//$url="localhost/master5/approval_vote.php?moduleId=".$ApproveId."";
		
		
					$mailBody .= '<br /><table width="100%" cellspacing="0" cellpadding="0" border="0">
										<tbody><tr>
										<td colspan="3">Dear Committee Member,</td></tr>
										<tr><td colspan="3"><br></td></tr>';
					$mailBody .='<tr><td colspan="3"><b>Subject  :</b> Approval Required '.$subject.' from Date '.$postDate.' to '.$expDate.'.</td>			</tr>
										<tr><td colspan="3">'.$description.'</td></tr>  
										<tr><td colspan="3"><br></td></tr>
										<tr><td>&nbsp;</td><td valign="middle" bgcolor="#337AB7" height="40" align="center" style="text-align:center; width:200px;"><center><a  id="act_btn" target="_blank" style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif" href="'.$url.'">Approve</a></center></td><td>&nbsp;</td></tr>
										<tr><td colspan="3"><br></td></tr>
										<tr><td colspan="3"><br></td></tr>
										</tbody></table>';	
		
		$societyEmail = "";	
		
	  	if($this->objFetchData->objSocietyDetails->sSocietyEmail <> "")
	  	{
		 	$societyEmail = $this->objFetchData->objSocietyDetails->sSocietyEmail;
	  	}
		else
	 	{
		 	$societyEmail = "techsupport@way2society.com";
	  	}
		try
	  	{	
			$EMailIDToUse = $this->m_obj_utility->GetEmailIDToUse(false, 0, 0, 0, 0, 0, $_SESSION['society_id'],0,0);
			 $EMailID = $EMailIDToUse['email'];
			 $Password = $EMailIDToUse['password'];			
			//$EMailID = "sujitkumar0304@gmail.com";
			//$Password = "9869752739";  
			//$host = "103.50.162.146";
			//$host = "smtp.gmail.com";
			//echo "Call Email 6<br>";
			$emailContent = GetEmailHeader() . $mailBody . GetEmailFooter() ;
			//$transport = Swift_SmtpTransport::newInstance($host,587)
				//->setUsername($EMailID)
				//->setSourceIp('0.0.0.0')
				//->setPassword($Password); 

			//AWS Config 
			$AWS_Config = CommanEmailConfig();
				 			$transport = Swift_SmtpTransport::newInstance($AWS_Config[0]['Endpoint'],$AWS_Config[0]['Port'] , $AWS_Config[0]['Security'])
				 					 ->setUsername($AWS_Config[0]['Username'])
				  					 ->setPassword($AWS_Config[0]['Password']);	
				// Create the message
				$message = Swift_Message::newInstance();
				
				$message->setTo(array(
		   		$societyEmail => $this->objFetchData->objSocietyDetails->sSocietyName
				)); 
				/*$message->setCc(array(
					
					$email => $name
					
				));*/
				$message->setBcc($display);					
				$message->setReplyTo(array(
				   $societyEmail => $this->objFetchData->objSocietyDetails->sSocietyName				   
				));
				//print_r( $societyEmail );
				$message->setSubject($mailSubject);
				$message->setBody($emailContent);
				$message->setFrom("no-reply@way2society.com", $this->objFetchData->objSocietyDetails->sSocietyName);					
				$message->setContentType("text/html");		
				$mailer = Swift_Mailer::newInstance($transport);
				$result = $mailer->send($message);
				//echo "Result".$result;
				$result = 1;
				if($result == 1)
				{
					echo 'Success';
				}
				else
				{
					echo 'Failed';
				}
	  
	  		}
	  		catch(Exception $exp)
			{
				echo "Error occure in email sending.";
			}
		//}
//}
		/*$societyEmail = "";	 
		$DBName = $_SESSION["dbname"]; 
	  if($this->objFetchData->objSocietyDetails->sSocietyEmail <> "")
	  {
		 $societyEmail = $this->objFetchData->objSocietyDetails->sSocietyEmail;
	  }
	  else
	  {
		 $societyEmail = "techsupport@way2society.com";
	  }	 
	  
		$sql= "select * from approval_status where approval_id = '".$ApproveId."'";
		$Result=$this->m_dbConn->select($sql);
		if($Result <> '')
		{
			for($i=0;$i<sizeof($Result);$i++)
			{
				 $selectQry="select other_email from `mem_other_family` where `mem_other_family_id`='".$Result[$i]['member_id']."'";
				 $memEmail=$this->m_dbConn->select($selectQry);
				 $email = $memEmail[0]['other_email'];
				 
				
			}
			 $mailBody = 'Dear Committee Members, <br /> <br /> ';
			 $mailBody .= $_POST['description'];
		}
	  try
	  	{ 
	 		  $EMailIDToUse = $this->m_obj_utility->GetEmailIDToUse(true, 1, "", 0, 0, $DBName, $SocietyId, 0, 0);
			  $EMailID = $EMailIDToUse['email'];
			  $Password = $EMailIDToUse['password'];
			  $transport = Swift_SmtpTransport::newInstance('103.50.162.146',587)
						->setUsername($EMailID)
						->setSourceIp('0.0.0.0')
						->setPassword($Password) ; 
																			
				// Create the message
				$message = Swift_Message::newInstance();
				if($this->objFetchData->objSocietyDetails->sSocietyEmail <> "")
				{
					$message->setTo(array(
					   $societyEmail => $societyName
					));
				}
				$message->setBcc($display);
				 $message->setReplyTo(array(
				   $societyEmail => $societyName
				)); 
				$message->setSubject($subject);
				$message->setBody($mailBody);
				$message->setFrom("no-reply@way2society.com", $this->objFetchData->objSocietyDetails->sSocietyName);					
				$message->setContentType("text/html");		
				$mailer = Swift_Mailer::newInstance($transport);
				$result = $mailer->send($message);
				//die();
				$result = 1;
				if($result == 1)
				{
					echo 'Success';
				}
				else
				{
					echo 'Failed';
				}
	  
	  	}
	  	catch(Exception $exp)
		{
			echo "Error occure in email sending.";
		}*/
	  
	  
	  
	  
	  
	  
	 
				  
	}
	  
		
	/*function SendNoticeSMS($msgBody,$ApproveId,$SocID, $DBName)
	{
		
		$msgBody = $_POST['subject'];
		$smsText=$msgBody;
		
		
		//**----Making log file name as SendNoticeSMS.html to track notice sms logs ----**
		echo "inside function";
		$Logfile=fopen("SendNoticeSMS.html", "a");	
		$msg = "<center><b><font color='#003399' >  DATE : </b>".date('Y-m-d')."</font></center> <br /> ";
		fwrite($Logfile,$msg);		
		date_default_timezone_set('Asia/Kolkata');
		
		//***------Fetching details from society to append in msg-----//
		
		$smsDetails = $this->m_dbConn->select("SELECT `society_name`, `sms_start_text`,`sms_end_text` FROM `society` WHERE `society_id` = '".$SocID."'");																									
		var_dump($smsDetails);			
		$msg = "<b>DBNAME : </b>". $DBName ."<br /><b> SOCIETY : </b>".$smsDetails[0]['society_name']."<br /><b> START TIME : </b>".date('Y-m-d h:i:s ')."<br /><br />";

		fwrite($Logfile,$msg);
		$sql="select other_mobile from `mem_other_family` where `mem_other_family_id`='".$ApproveId."'";
		$ressel=$this->m_dbConn->select($sql);
		print_r($ressel);
		
		for($i=0;$i<sizeof($ressel);$i++)
		{
			$mobile=$ressel[$i]['other_mobile'];
			//echo $mobile;
		}
		$smsText=$_POST['subject'];
		$clientDetails = $this->m_dbConnRoot->select("SELECT `client_id` FROM  `society` WHERE  `dbname` ='".$DBName."' ");
				
					if(sizeof($clientDetails) > 0)
					{
						$clientID = $clientDetails[0]['client_id'];
						//echo '<BR> Client ID is '.$clientID;
					}
					//print_r($mobile);
					//print_r($smsText);
					//print_r($clientID);
					//die();
					
					$response =  $this->m_obj_utility->SendSMS($mobile, $smsText, $clientID);	
					echo $response;				
					$ResultAry[$ressel[$i]['other_mobile']] = $response;
					
					
	}*/
}
?>

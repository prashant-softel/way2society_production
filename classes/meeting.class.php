<?php
include_once("dbconst.class.php");
include_once("include/display_table.class.php");
include_once( "include/fetch_data.php");
include_once( "utility.class.php");
include_once('../swift/swift_required.php');
class meeting //extends dbop
{
	public $actionPage = "../meeting.php";
	public $objFetchData;
	public $m_obj_utility;
	function __construct($dbConn,$dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->objFetchData = new FetchData($dbConn);
		$this->m_obj_utility = new utility($dbConn, $dbConnRoot);
		$this->display_pg=new display_table($this->m_dbConn);

		//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);

		//dbop::__construct();
	}

	public function startProcess()
	{
		$errorExists = 0;
		if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			$cId=$_SESSION['login_id'];
			$mId=$_POST['id'];
			$mDate=$_POST['mdate'];
			$hr=$_POST['hr'];
			$mn=$_POST['mn'];
			$ampm=$_POST['ampm'];
			$mTime=$hr.":".$mn.$ampm;
			$venue=$_POST['venue'];
			$res=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=2&id=$mId&mDate=$mDate&cId=$cId&mTime=$mTime&venue=$venue&dbName=".$_SESSION['dbname']);
			//$up_query="update `group` set `srno`='".$_POST['srno']."',`groupname`='".$_POST['groupname']."' where id='".$_POST['id']."'";
			//$data = $this->m_dbConn->update($up_query);
			return "Update";
		}
		else
		{
			return $errString;
		}
	}
	public function combobox($query)
	{
	}
	public function display1($rsas, $method)
	{
		//echo "Method:".$method;
		$viewMethod = "viewMeeting";
		$thheader = array('Creation Date','Meeting Title','Meeting Date', 'Meeting Time', 'Venue','');
		$this->display_pg->edit		= $method;
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "meeting.php";
		$this->display_pg->view     = $viewMethod;
		if($_SESSION['role']=="Super Admin")
		{
			if ($method=="cancel")
			{
				$res = $this->display_pg->display_datatable($rsas, false, false);
				return $res;
			}
			else if ($method=="closed")
			{
				$viewMethod = "viewMinutes";
				$this->display_pg->view     = $viewMethod;
				$res = $this->display_pg->display_datatable($rsas, false, false, true);
				return $res;
			}
			else if($method=="invited")
			{
				$res = $this->display_pg->display_datatable($rsas, false, false, true);
				return $res;
			}
			else
			{
				$res = $this->display_pg->display_datatable($rsas, true, true);
				return $res;
			}
		}
		else
		{
			$res = $this->display_pg->display_datatable($rsas, true, true);
			return $res;
		}
	}
	public function pgnation($type)
	{
		if($type=="open")
		{
			$res= file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=7&dbName=".$_SESSION['dbname']);
			$jRes = json_decode($res,true);
			$cRes=array();
			for($i=0;$i<sizeof($jRes);$i++)
			{
				$cRes[$i]['Id'] = $jRes[$i]['Id'];
				$cRes[$i]['CreatedDate'] = $jRes[$i]['CreatedDate'];
				$cRes[$i]['Title'] = "<a onclick = 'goTOViewPage(".$jRes[$i]['Id'].")'>".$jRes[$i]['Title']."</a>";
				$cRes[$i]['MeetingDate'] = $jRes[$i]['MeetingDate'];
				$cRes[$i]['MeetingTime'] = $jRes[$i]['MeetingTime'];
				$cRes[$i]['Venue'] = $jRes[$i]['Venue'];
				$cRes[$i]['cancel'] = "<input type='button' name='cancel' id='cancel' value='Cancel' class='btn btn-primary btn-xs' onClick='cancelMeeting(".$jRes[$i]['Id'].")'/>";
				//$cRes[$i]['send'] = "<input type='button' name='send' id='send' value='send' class='btn btn-primary btn-xs' onClick='SendEmail(".$jRes[$i]['Id'].")'/>";	
			}
			$method="getmeeting";
			if(sizeof($jRes) > 0)
			{
				//echo "in if";
				$data=$this->display1($cRes,$method);
			}
			else
			{
				$data=$this->display1($jRes,$method);	
			}
			return $data;
		}
		if($type=="invited")
		{
			$res= file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=10&dbName=".$_SESSION['dbname']);
			$jRes = json_decode($res,true);
			$cRes=$jRes;
			/*echo "<pre>";
			print_r($cRes);
			echo "</pre>";*/
			foreach($cRes as $key => $value)
			{
				foreach($value as $k => $v)
				{
					unset($cRes[$key]['CreatedBy']);
					unset($cRes[$key]['LastMeetingDate']);
					unset($cRes[$key]['MeetingType']);
					unset($cRes[$key]['MeetingStatus']);
					unset($cRes[$key]['Notes']);
					unset($cRes[$key]['EndText']);
					unset($cRes[$key]['Status']);
					//unset($cRes[$key]['GroupId']);
					$cRes[$key]['GroupId'] = "";
				//unset($cRes[$key]['Title']);
				}
			}
			//echo "Id:".$jRes['id'];
			$method="invited";
			$data=$this->display1($cRes,$method);
			return $data;
		}
		if($type=="inProcess")
		{
			$res = file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=9&dbName=".$_SESSION['dbname']);
			$jRes = json_decode($res,true);
			$cRes=array();
			for($i=0;$i<sizeof($jRes);$i++)
			{
				$cRes[$i]['Id'] = $jRes[$i]['Id'];
				$cRes[$i]['CreatedDate'] = $jRes[$i]['CreatedDate'];
				$cRes[$i]['Title'] = "<a onclick = 'goTOViewPage(".$jRes[$i]['Id'].")'>".$jRes[$i]['Title']."</a>";
				$cRes[$i]['MeetingDate'] = $jRes[$i]['MeetingDate'];
				$cRes[$i]['MeetingTime'] = $jRes[$i]['MeetingTime'];
				$cRes[$i]['Venue'] = $jRes[$i]['Venue'];
				//$cRes[$i]['send'] = "<input type='button' name='send' id='send' value='Send Email' class='btn btn-primary btn-xs' onClick='goto_notice()'/>";
				$cRes[$i]['cancel'] = "";
			}
			$method="getminutes";
			if(sizeof($jRes) > 0)
			{
				//echo "in if";
				$data=$this->display1($cRes,$method);
			}
			else
			{
				$data=$this->display1($jRes,$method);	
			}
			return $data;
		}
		if($type=="closed")
		{
			$res= file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=8&dbName=".$_SESSION['dbname']);
			$jRes = json_decode($res,true);
			$cRes=$jRes;
			//echo "<pre>";
			//print_r($cRes);
			//echo "</pre>";
			foreach($cRes as $key => $value)
			{
				foreach($value as $k => $v)
				{
					unset($cRes[$key]['CreatedBy']);
					unset($cRes[$key]['LastMeetingDate']);
					unset($cRes[$key]['MeetingType']);
					unset($cRes[$key]['MeetingStatus']);
					unset($cRes[$key]['Notes']);
					unset($cRes[$key]['EndText']);
					unset($cRes[$key]['Status']);
					
					//$cRes[$key]['send'] = "<button type='button' class='btn btn-primary' onClick='SendEamils(".$jRes[$key]['Id'].")'>
 //Send Email
//</button>";
					//$cRes[$key]['send'] = "<button type='button' class='btn btn-primary' data-toggle='modal' data-target='#exampleModal' onClick='SendEamils(".$jRes[$key]['Id'].")'>
 //Send Email
//</button>";
$cRes[$key]['send'] = "<button type='button' class='btn btn-primary'  onClick='SendEamils(".$jRes[$key]['Id'].")'>
 Send Email
</button>";
					
				}
			}
			//echo "<pre>";
			//print_r($cRes);
			//echo "</pre>";
			//echo "Id:".$jRes['id'];
			//echo "Name:".$jRes['name'];
			$data=$this->display1($cRes,$type);
			return $data;	
		}
		if($type=="cancel")
		{
			$res= file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=11&dbName=".$_SESSION['dbname']);
			$jRes = json_decode($res,true);
			$cRes=$jRes;
			/*echo "<pre>";
			print_r($cRes);
			echo "</pre>";*/
			foreach($cRes as $key => $value)
			{
				foreach($value as $k => $v)
				{
					unset($cRes[$key]['CreatedBy']);
					unset($cRes[$key]['LastMeetingDate']);
					unset($cRes[$key]['MeetingType']);
					unset($cRes[$key]['MeetingStatus']);
					unset($cRes[$key]['Notes']);
					unset($cRes[$key]['EndText']);
					unset($cRes[$key]['Status']);
					$cRes[$key]['GroupId'] = "";
					//unset($cRes[$key]['GroupId']);
				//unset($cRes[$key]['Title']);
				}
			}
			//echo "<pre>";
			//print_r($cRes);
			//echo "</pre>";
			//echo "Id:".$jRes['id'];
			//echo "Name:".$jRes['name'];
			$data=$this->display1($cRes,$type);
			return $data;	
		}
	}
	public function selecting()
	{
		//echo "In selecting:";
		$mId=$_REQUEST['mId'];
		//echo "In selecting:".$mId;
		$res=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=6&mId=$mId&dbName=".$_SESSION['dbname']);
		//echo "Res:";
		//print_r($res);
		$jRes = json_decode($res,true);
		//echo "Res:";
		//print_r($jRes);
		//$sql = "select id,`srno`,`groupname` from `group` where id='".$_REQUEST['groupId']."'";
		//$res = $this->m_dbConn->select($sql);
		//echo "Result:".$res;
		//$res1=substr($res, 1);
		//echo strlen($res1);
		//echo (strlen($res1) - 1);
		//$res2=substr($res1, 0,(strlen($res1) - 3) );
		//foreach ($res2 as $k => $v) 
		//{
			//$arr[$k]=$v;
		//}
		//echo $jRes;
		//echo $res;
		return $jRes;
		
	}
	public function deleting($mId)
	{
		$result="";
		$AttRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingAttendance?mode=3&mId=$mId&dbName=".$_SESSION['dbname']);
		$result.="Meeting att res:".$AttRes."<br>";
		$agendaRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingQue?mode=3&mId=$mId&dbName=".$_SESSION['dbname']);
		$result.="Meeting Agenda res:".$agendaRes."<br>";
		$meetingRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=3&id=$mId&dbName=".$_SESSION['dbname']);
		$result.="Meeting Result:".$meetingRes."<br>";
		return $result;
	}
	public function getCountOfMeeting()
	{
		$openRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=7&dbName=".$_SESSION['dbname']);
		$jOpenRes = json_decode($openRes,true);
		$invitedRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=10&dbName=".$_SESSION['dbname']);
		$jInvitedRes = json_decode($invitedRes,true);
		$pendingRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=9&dbName=".$_SESSION['dbname']);
		$jPendingRes = json_decode($pendingRes,true);
		$closedRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=8&dbName=".$_SESSION['dbname']);
		$jClosedRes = json_decode($closedRes,true);
		$cancelRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=11&dbName=".$_SESSION['dbname']);
		$jCancelRes = json_decode($cancelRes,true);
		$meetingCount = array();
		$meetingCount[0] = sizeof($jOpenRes);
		$meetingCount[1] = sizeof($jInvitedRes);
		$meetingCount[2] = sizeof($jPendingRes);
		$meetingCount[3] = sizeof($jClosedRes);
		$meetingCount[4] = sizeof($jCancelRes);
		return $meetingCount;
	}
	public function GetMemberEmails($gId)
	{
		
		if($gId == "AO")
		{
			$sql = "Select member_id as MemberId, primary_owner_name as other_name,email as other_email from member_main where ownership_status = '1'" ;
			//$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "ACO")
		{
			$sql = "Select mem_other_family_id as MemberId, other_name as other_name,other_email from mem_other_family where coowner = '2'" ;
			//$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "AOCO")
		{
			 $sql = "Select member_id as MemberId, primary_owner_name as other_name,email as other_email from member_main where ownership_status = '1' union Select mem_other_family_id as MemberId, other_name as other_name,other_email as other_email from mem_other_family where coowner = '2'" ;
			//$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "AR")
		{
			$sql = "Select mof.`mem_other_family_id` as MemberId, CONCAT(mof.`other_name`,' (Owner)') as other_name,mof.other_email as other_email from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '1' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id` union Select mof.`mem_other_family_id` as MemberId, CONCAT(mof.`other_name`,' (Co-Owner)') as other_name,mof.other_email as other_email from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '2' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id` union Select mof.`mem_other_family_id` as MemberId, CONCAT(mof.`other_name`,' (Resident)') as other_name,mof.other_email as other_email from mem_other_family as mof,member_main as mm, unit as u where mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id` union Select t.`tmember_id` as MemberId,CONCAT(t.`mem_name`,' (Tenant)') as other_name, t.email as other_email from tenant_module as tm,tenant_member as t,unit as u where tm.Status = 'Y' and tm.`unit_id` = u.`unit_id` and t.`tenant_id` = tm.`tenant_id`";
						//echo $sql;
			//$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "ART")
		{
		 $sql = "Select mem_other_family_id as MemberId, other_name as other_name, other_email as other_email from mem_other_family where status = 'Y' union Select tmember_id as MemberId, mem_name as other_name,email as other_email from tenant_member where status = 'Y'" ;
			//$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "ACM")
		{
			 $sql = "Select C.member_id as MemberId, M.other_name,M.other_email from mem_other_family as M, commitee as C where M.status = 'Y' and M.mem_other_family_id = C.member_id" ;
			//$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "AT")
		{
			 $sql = "Select tenant_id as MemberId, tenant_name as other_name,email as other_email from tenant_module where status = 'Y'";
			//$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "AVO")
		{
			$sql = "SELECT mof.mem_other_family_id as MemberId, mof.other_name,mof.other_email FROM `mem_other_family` mof, `mem_car_parking` mcp where mcp.member_id = mof.member_id and mcp.status ='Y'" ;
			//$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "ALH")
		{
			 $sql = "Select L.member_id as MemberId, M.owner_name as other_name,M.email as other_email from mortgage_details as L, member_main as M where L.Status = 'Y' and L.LienStatus = 'Open' and M.member_id = L.member_id";
			//$memId = $this->m_dbConn->select($sql);
		}
		
		$memId = $this->m_dbConn->select($sql);
		$mem_array = array();
		if($memId <> '')
		{
			for($i=0; $i< sizeof($memId); $i++)
			{
				$member_name = $memId[$i]['other_name'];
				$mem_email = $memId[$i]['other_email'];
				array_push($mem_array,array("member_name"=>$member_name, "member_email"=>$mem_email));
			}
		}
		return $mem_array;
 }
 
 public function SendMinutedEmail($mTitle, $mDesc, $mem_array)
 {
	$mailSubject = "Minuted Meeting :" .$mTitle;
	$mailBody = '<div style="width:100%"><center><div style="width:70%">'.$mDesc.'</div><center></div>';
	//print_r($mem_array);
	$societyEmail = "";	  
	if($this->objFetchData->objSocietyDetails->sSocietyEmail <> "")
	  {
		 $societyEmail = $this->objFetchData->objSocietyDetails->sSocietyEmail;
	  }
	  else
	  {
		 $societyEmail = "societyaccounts@pgsl.in";
	  }	 
	  //echo $societyEmail; 
	 // $EMailIDToUse = $this->m_obj_utility->GetEmailIDToUse(true, 1, "", $UnitID, 0, $DBName, $SocID, $NoticeID, $bccUnitsArray);
		//$EMailIDToUse = $this->GetEmailIDToUse(false,0,0,0,0,$_SESSION['dbname'],$_SESSION['society_id'],0,0);			
	//$EMailID = $EMailIDToUse['email'];
					//$Password = $EMailIDToUse['password']; 
	for($i=0;$i<sizeof($mem_array);$i++)
	{
		//echo "inside far loop ";
		$mem_email = $mem_array[$i]['member_email'];
		$mem_name  = $mem_array[$i]['member_name'];
		try
	   {
		 //  echo "society_id ".$_SESSION['society_id'];
		 // echo "inside far try "; 
		   //$EMailIDToUse = $m_obj_utility->GetEmailIDToUse(false,0,0,0,0,$_SESSION['dbname'],$_SESSION['society_id'],0,0);
			$EMailIDToUse = $this->m_obj_utility->GetEmailIDToUse(false, 0, 0, 0, 0, 0, $_SESSION['society_id'], 0, 0);
			//$EMailID = "no-reply@way2society.com";
			//$Password = "Society@1234!";
					
			if(isset($EMailIDToUse) && $EMailIDToUse['status'] == 0)
			{
					$EMailID = $EMailIDToUse['email'];
					$Password = $EMailIDToUse['password'];
			}	
			//echo "Test";
			$transport = Swift_SmtpTransport::newInstance('103.50.162.146',587)
									->setUsername($EMailID)
									->setSourceIp('0.0.0.0')
									->setPassword($Password); 	
									
									$message = Swift_Message::newInstance();
							
							
							
							$message->setTo(array( $mem_email => $mem_name));														
							// echo "Test1";
							 $message->setReplyTo(array(
							   $societyEmail => $societyName
							));
							
							$message->setSubject($mailSubject);
							$message->setBody($mailBody);
							$message->setFrom($EMailID, $this->objFetchData->objSocietyDetails->sSocietyName);
							$message->setContentType("text/html");
							
							// Send the email
							$mailer = Swift_Mailer::newInstance($transport);
							
							//echo "<BR>sending batch $emailCount ... ";
							$result = $mailer->send($message);
							if($result <> 0)
							{							
								$sendind ='Success';
								$result =$result;
							}
							else
							{
								echo "<br/>Failed";
								$sendind ='Failed';
								$result =0;
							}
						
						
	   }
	   catch(Exception $exp)
		{
		}
	   
	}
 return $result;	
 }
 
 
}
?>	
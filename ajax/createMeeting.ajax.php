<?php 
	include_once("../classes/dbconst.class.php");
	include_once("../classes/createMeeting.class.php");
	include_once("../classes/include/dbop.class.php");
	include_once("../classes/createGrp.class.php");
	$dbConn = new dbop();
	$obj_Cmeeting=new createMeeting($dbConn);
	//echo "In Ajax";
	//To get all member of society
	if($_REQUEST["method"]=="getmember") 
	{
		//echo "in edit:";
		
        $comboUnit=$obj_Cmeeting->comboboxForMemberSelection("Select G.MemberId, M.other_name from membergroup_members G Inner join mem_other_family M on G.MemberId=M.mem_other_family_id where G.GroupId=$gId",0,'All','0');
		return $comboUnit;
	}
	//to Delete meeting
	if($_REQUEST["method"]=="delete")
	{
		//echo "In delete of ajax";
		$res=$obj_meeting->deleting();
		//print_r($res);
		return "Data Deleted Successfully";
	}
	//To Fetch all member id
	if($_REQUEST['method']=="Fetch")
	{
		$gId = $_REQUEST['gId'];
		$memId = $obj_Cmeeting->getGroupMembers($gId);
		echo $memId;
	}
	//To Fetch seleted member id
	if($_REQUEST['method']=="FetchSelectedMembers")
	{
		$selectedMembers = "";
		$gId = $_REQUEST['gId'];
		$mId = $_REQUEST['mId'];
		$memId = $obj_Cmeeting->getMeetingAttendees($gId,$mId);
		if(sizeof($memId) > 0)
		{
			$selectedMembers = "<ul>";
			for($i = 0;$i < sizeof( $memId );$i++)
			{	
				$selectedMembers .= "<li class='selectMemId'> &nbsp".$memId[$i]['other_name']."</li>";
			}
			$selectedMembers .= "</ul>";
		}
		echo $selectedMembers;
		/*echo "<pre>";
		print_r($memId);
		echo "</pre>";*/
	}
	//To update meeting
	if($_REQUEST['method']=="Edit")
	{
		//echo "In Ajax";
		$mId=$_REQUEST['mId'];
		//echo "mId:".$mId;
		$meetingRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=6&mId=$mId&dbName=".$_SESSION['dbname']);
		echo $meetingRes;
		echo "#";
		$agendaRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingQue?mode=5&mId=$mId&dbName=".$_SESSION['dbname']);
		echo $agendaRes;
		echo "#";
		$memRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingAttendance?mode=6&mId=$mId&dbName=".$_SESSION['dbname']);
		echo $memRes;
	}
	//For creating a new meeting
	if($_REQUEST['method']=="Create")
	{
		$cId=urlencode($_SESSION['login_id']);
		$title=$_REQUEST['title'];
		$mDate=$_REQUEST['mDate'];
		$mTime=$_REQUEST['mTime'];
		$venue=$_REQUEST['venue'];
		$lastDate=$_REQUEST['mLastDate'];
		$notes=$_REQUEST['notes'];
		$eText=$_REQUEST['eText'];
		$gId=$_REQUEST['gId'];
		$strURL = HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=1&title=$title&type=1&mDate=$mDate&cId=$cId&mTime=$mTime&venue=$venue&lastmdate=$lastDate&mStatus=1&notes=$notes&eText=$eText&gId=$gId&dbName=".$_SESSION['dbname'];
		//echo $strURL;
		$mRes=file_get_contents($strURL);
		echo "Meeting:".$mRes;
		$mId=$obj_Cmeeting->getMeetingId();
		//echo "mId".$mId;
		$srNo=json_decode($_REQUEST['SrNo']);
		$agenda=json_decode($_REQUEST['agendaArr']);
		//echo $agenda[2];
		//echo "Size:".sizeof($srNo);
		for($i=0;$i<sizeof($srNo);$i++)
		{
			$agendaValue = $agenda[$i];
			$agendaValue = str_replace("&", "and", $agendaValue);
			$mARes=file_get_contents(HOST_NAME.	"8080/MinutesOfMeetingS/ServletMeetingQue?mode=1&mId=$mId&srNo=$srNo[$i]&q=$agendaValue&r=%20&pId=0&sId=0&dbName=".$_SESSION['dbname']);
			echo "Agenda Res: i".$i." ".$mARes;
		}
		$memArr=json_decode($_REQUEST['memArr']);
		$len=sizeof($memArr);
		for($i=0;$i<$len;$i++)
		{
			$memId=urlencode($memArr[$i]);
			$memRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingAttendance?mode=1&mId=$mId&memId=$memId&s=%20&a=%20&dbName=".$_SESSION['dbname']);
			echo "Att Result: i".$i." ".$memRes;
		}
		//return "success";
	}
	//For updating meeting
	if($_REQUEST['method']=="Update")
	{
		$id=$_REQUEST['id'];
		$cId=urlencode($_SESSION['login_id']);
		$title=$_REQUEST['title'];
		$mDate=$_REQUEST['mDate'];
		$mTime=$_REQUEST['mTime'];
		$venue=$_REQUEST['venue'];
		$lastDate=$_REQUEST['mLastDate'];
		$notes=$_REQUEST['notes'];
		$eText=$_REQUEST['eText'];
		$gId=$_REQUEST['gId'];
		//For adding agenda for meeting
		$mId=$obj_Cmeeting->getMeetingId();
		echo "mId:".$mId;
		$strURL = HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=2&id=$mId&title=$title&type=1&mDate=$mDate&cId=$cId&mTime=$mTime&venue=$venue&lastmdate=$lastDate&mStatus=1&notes=$notes&eText=$eText&gId=$gId&dbName=".$_SESSION['dbname'];
		echo $strURL;
		$mRes=file_get_contents($strURL);
		echo "Meeting:".$mRes;
		$aId=json_decode($_REQUEST['aId']);
		$srNo=json_decode($_REQUEST['SrNo']);
		$agenda=json_decode($_REQUEST['agendaArr']);
		//echo $agenda[2];
		//echo "Size:".sizeof($srNo);
		for($i=0;$i<sizeof($srNo);$i++)
		{
			echo "Agenda : "+$agenda[$i];
			$agendaValue = $agenda[$i];
			$agendaValue = str_replace("&", "and", $agendaValue);
			$mARes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingQue?mode=6&aId=$aId[$i]&srNo=$srNo[$i]&q=$agendaValue&dbName=".$_SESSION['dbname']);
			echo "Agenda Update Res: i".$i." ".$mARes;
		}
		$res=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingAttendance?mode=6&mId=$mId&dbName=".$_SESSION['dbname']);
		ob_clean();
	  	$jRes=json_decode($res,true);
		$memExId=array();
		for($i=0;$i<sizeof($jRes);$i++)
		{
			$memExId[$i]=$jRes[$i]['MemberId'];
		}
		//echo "<pre>";
		//print_r ($memExId);
		//echo "</pre>";
		$memId=json_decode($_REQUEST['memArr']);
		//echo "<pre>";
		//print_r ($memId);
		//echo "</pre>";
		$memNew=array();
		$memNew = array_diff($memId, $memExId);
		//$memNew=json_encode($memNew,true);
		//echo "<pre>";
		//print_r ($memNew);
		//echo "</pre>";
		$i=0;
		foreach($memNew as $key => $value)
		{
			$insert[$i]=$value;
			$i=$i+1;
		}
		//echo "<pre>";
		//print_r ($insert);
		//echo "</pre>";
		$len=sizeof($insert);
		for($i=0;$i<$len;$i++)
		{
			$memId=urlencode($insert[$i]);
			$memRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingAttendance?mode=1&mId=$mId&memId=$memId&s=%20&a=%20&dbName=".$_SESSION['dbname']);
			echo "Att Result: i".$i." ".$memRes;
		}
		//return "success";
	}
	if($_REQUEST['method']=="Preview")
	{
		$mId=$_REQUEST['mId'];
		$sName=$_REQUEST['socName'];
		//echo "sName:"+$sName;
		if($mId!=0)
		{
			$jmeetingRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=6&mId=$mId&dbName=".$_SESSION['dbname']);
			$meetingRes=json_decode($jmeetingRes,true);
			$jmeetingAgenda=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingQue?mode=5&mId=$mId&dbName=".$_SESSION['dbname']);
			$meetingAgenda=json_decode($jmeetingAgenda,true);
		}
		else if($mId==0)
		{
			$mId=$obj_Cmeeting->getMeetingId();
			$jmeetingRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=6&mId=$mId&dbName=".$_SESSION['dbname']);
			$meetingRes=json_decode($jmeetingRes,true);
			$jmeetingAgenda=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingQue?mode=5&mId=$mId&dbName=".$_SESSION['dbname']);
			$meetingAgenda=json_decode($jmeetingAgenda,true);
		}
		//$today = date("F j, Y"); 
		$heading="<table width='100%'><tr><td style='text-align:left;width:50%'>Ref:_____________</td><td style='text-align:right;width:50%'>Date:__/__/____</td></tr></table><table style='text-align:center;width:100%'><tr><td><b>Notice of ".$meetingRes['Title']."</b></td></tr></table><br>";
		$note=$meetingRes['Notes'];
		$agendaContent="<table style='width:95%;margin-left:3%'>";
		for($i=0;$i<sizeof($meetingAgenda);$i++)
		{
			$agendaContent.="<tr><td>".($i+1).". ".$meetingAgenda[$i]['Question']."</td></tr>";
		}
		$agendaContent.="</table>";
		//echo "ET:".$EndText;
		$footer="<br><b>Please Note:</b><table style='margin-left:3%;width:95%'><tr><td>". $meetingRes['EndText']."</td></tr></table><br><table style='width:100%;'><tr><td style='text-align:center'><b>All members are requested to attend the ".$meetingRes['Title'].".</b></td></tr><tr><td><br><br></td></tr><tr><td><b>For ".$sName."</b></td></tr><tr><td><br><br></td></tr><tr><td><b>Secretary &nbsp;&nbsp;&nbsp;&nbsp;Treasurer &nbsp;&nbsp;&nbsp;&nbsp;Chairman</b></td></tr></table>";
		$finalData=$heading.$note.$agendaContent.$footer;
		echo $finalData;
	}
	if($_REQUEST['method']=="viewMeeting")
	{
		$mId=$_REQUEST['mId'];
		$sName=$_REQUEST['socName'];
		//echo "sName:"+$sName;
		if($mId!=0)
		{
			$jmeetingRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=6&mId=$mId&dbName=".$_SESSION['dbname']);
			$meetingRes=json_decode($jmeetingRes,true);
			$jmeetingAgenda=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingQue?mode=5&mId=$mId&dbName=".$_SESSION['dbname']);
			$meetingAgenda=json_decode($jmeetingAgenda,true);
			$jMeetingAtt=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingAttendance?mode=6&mId=$mId&dbName=".$_SESSION['dbname']);
			$MeetingAtt=json_decode($jMeetingAtt,true);
		}
		else if($mId==0)
		{
			$mId=$obj_Cmeeting->getMeetingId();
			$jmeetingRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=6&mId=$mId&dbName=".$_SESSION['dbname']);
			$meetingRes=json_decode($jmeetingRes,true);
			$jmeetingAgenda=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingQue?mode=5&mId=$mId&dbName=".$_SESSION['dbname']);
			$meetingAgenda=json_decode($jmeetingAgenda,true);
			
		}
		//$today = date("F j, Y"); 
		$heading="<table width='100%'><tr><td style='text-align:left;width:50%'>Ref:_____________</td><td style='text-align:right;width:50%'>Date:__/__/____</td></tr></table><table style='text-align:center;width:100%'><tr><td><b>".$meetingRes['Title']."</b></td></tr></table><br>";
		$note=$meetingRes['Notes'];
		$agendaContent="<table style='width:95%;margin-left:3%'>";
		for($i=0;$i<sizeof($meetingAgenda);$i++)
		{
			$agendaContent.="<tr><td>".($i+1).". ".$meetingAgenda[$i]['Question']."</td></tr>";
		}
		$agendaContent.="</table>";
		//echo "ET:".$EndText;
		$footer="<br><b>Please Note:</b><table style='margin-left:3%;width:95%'><tr><td>". $meetingRes['EndText']."</td></tr></table><br><table style='width:100%;'><tr><td style='text-align:center'><b>All members are requested to attend the ".$meetingRes['Title'].".</b></td></tr><tr><td><br><br></td></tr><tr><td><b>For ".$sName."</b></td></tr><tr><td><br><br></td></tr><tr><td><b>Secretary &nbsp;&nbsp;&nbsp;&nbsp;Treasurer &nbsp;&nbsp;&nbsp;&nbsp;Chairman</b></td></tr></table>";
		$finalData=$heading.$note.$agendaContent.$footer;
		echo $finalData;
	}
	if($_REQUEST['method']=="getHeader")
	{
		$data=$obj_Cmeeting->getHead();
		echo $data;
	}
	if($_REQUEST['method']=="getTemplate")
	{
		$tempId=$_REQUEST['id'];
		$res1=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=13&tempId=$tempId&dbName=".$_SESSION['dbname']);
		$res2=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingQue?mode=7&tempId=$tempId&dbName=".$_SESSION['dbname']);
		echo $res1;
		echo "#";
		echo $res2;
	}
	if($_REQUEST['method']=="deleteAgenda")
	{
		$aId=$_REQUEST['aId'];
		$res=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingQue?mode=8&aId=$aId&dbName=".$_SESSION['dbname']);
		echo $res;
	}
	if($_REQUEST['method']=="getMeetingId")
	{
		$mId=$obj_Cmeeting->getMeetingId();
		echo $mId;
	}
	if($_REQUEST['method']=="changeStatus")
	{
		$mId=$_REQUEST['mId'];
		$status=$_REQUEST['status'];
		$res=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=12&mId=$mId&s=$status&dbName=".$_SESSION['dbname']);
		echo $res;
	}
	if($_REQUEST['method'] == "getInviteesDetails")
	{
		$gId=$_REQUEST['gId'];
		$mId=$_REQUEST['mId'];
		$groupRes = file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/Servlet1?mode=6?id=$gId&dbName=".$_SESSION['dbname']);
		echo $groupRes;
		echo "#";
		$meetingAtt = file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingAttendance?mode=5&mId=$mId&dbName=".$_SESSION['dbname']);
		echo $meetingAtt;
	}
?>
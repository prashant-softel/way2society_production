
<?php 
	include_once("../classes/dbconst.class.php");
	include_once("../classes/meeting.class.php");	
	include_once("../classes/include/dbop.class.php");
	$dbConn = new dbop();
	$dbConnRoot = new dbop(true);
	//$dbConn = new dbop();
	$obj_meeting=new meeting($dbConn, $dbConnRoot);
	
	echo $_REQUEST["method"]."@@@";
	//echo $_REQUEST["method"];
	if($_REQUEST["method"]=="edit") 
	{
		//echo "in edit:";
		$select_type = $obj_meeting->selecting();
		$res=$select_type;
		foreach($res as $key => $value)
		{
			unset($res['CreatedBy']);
			unset($res['CreatedDate']);
		}
		//echo "New:";
		//print_r($res);
		//print_r ("in momgroup.ajax:".$select_type);
		foreach($res as $k => $v)
		{
				echo $v."#";
		}
	}
	if($_REQUEST["method"]=="delete")
	{
		$mId=urlencode($_REQUEST['mId']);
		echo "mId:".$mId;
		$res=$obj_meeting->deleting($mId);
		echo $res;
	}
	if($_REQUEST["method"]=="cancelMeeting")
	{
		$mId=urlencode($_REQUEST['mId']);
		$status = urlencode($_REQUEST['status']);
		$meetingRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=12&mId=".$mId."&s=".$status."&dbName=".$_SESSION['dbname']);
		echo $meetingRes;
	}
	if($_REQUEST["method"]=="GetData")
	{
		
		$mId=urlencode($_REQUEST['mId']);
		//$res =getMeetingByMeetingId($mId);
		$res=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=6&mId=$mId&dbName=".$_SESSION['dbname']);
		echo $res;
	}
	if($_REQUEST["method"]=="fetchMemberEmail")
	{
		//echo "Test";
		$grpId= urlencode($_REQUEST['gId']);
		$res =$obj_meeting->GetMemberEmails($grpId);
		echo json_encode($res);
	}
	if($_REQUEST["method"]=="SendingEmails")
	{
		$Meeting_Title = $_REQUEST['title'];
		$Meeting_Desc = $_REQUEST['minutesDetails'];
		$MemberDetails = $_REQUEST['mdata'];
		$res = $obj_meeting->SendMinutedEmail($Meeting_Title,$Meeting_Desc,$MemberDetails);
		echo $res;
	}
?>
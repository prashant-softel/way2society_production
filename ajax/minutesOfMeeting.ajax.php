<?php
	include_once("../classes/dbconst.class.php");
	include_once("../classes/document_maker.class.php");
	include_once("../classes/createMinutes.class.php");
	include_once("../classes/include/dbop.class.php");
	include_once("../classes/createGrp.class.php");
	$dbConn = new dbop();
	$obj_Cminutes=new createMinutes($dbConn);
	$dbConnRoot = new dbop(true);
	$obj_templates = new doc_templates($dbConn,$dbConnRoot); 
	//echo "In Ajax";
	//To get all member of society
	if($_REQUEST["method"]=="getAgenda") 
	{
		//echo "in edit:";
		$mId=$_REQUEST['mId'];
		$meetingRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=6&mId=$mId&dbName=".$_SESSION['dbname']);
		echo $meetingRes;
		echo "#";
        $meetingAgenda=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingQue?mode=5&mId=$mId&dbName=".$_SESSION['dbname']);
		echo $meetingAgenda;
		echo "#";
		$meetingTemplateDetails=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingTemplateDetails?mode=4&maId=".$mId."&dbName=".$_SESSION['dbname']);
		echo $meetingTemplateDetails;
		echo "#";
		$meetingAtt=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingAttendance?mode=6&mId=$mId&dbName=".$_SESSION['dbname']);
		echo $meetingAtt;
		$chairmanName  =  $obj_Cminutes->getChairmanName();
		echo "#";
		echo $chairmanName[0]['other_name'];
	}
	if($_REQUEST["method"]=="Update")
	{
		
		$mId=urlencode($_REQUEST['mId']);
		$objid=json_decode($_REQUEST['Id'],true);
		$objminutes=json_decode($_REQUEST['minutes'],true);
		$objres=json_decode($_REQUEST['res'],true);
		//echo "Res:".$res[0];
		$objpId=json_decode($_REQUEST['pId'],true);
		$objsId=json_decode($_REQUEST['sId'],true);
		$objpassBy=json_decode($_REQUEST['passBy'],true);
		$note=$_REQUEST['note'];
		$eText=$_REQUEST['endNote'];
		$len=sizeof($objminutes);
		//echo "0 of id".urlencode($objid[0]);
		//echo "Len:".$len;
		$i=0;
		$flag=0;
		for($i=0;$i<$len;$i++)
		{
			//echo "in for loop";
			$id=urlencode($objid[$i]);
			$minutes=urlencode($objminutes[$i]);
			$res=urlencode($objres[$i]);
			$pId=urlencode($objpId[$i]);
			$sId=urlencode($objsId[$i]);
			$passBy=urlencode($objpassBy[$i]);
			$minutesRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingQue?mode=2&id=$id&m=$minutes&r=$res&pId=$pId&sId=$sId&passBy=$passBy&dbName=".$_SESSION['dbname']);
			//echo "minutes res:".$minutesRes."<br>";
		}
		$type = $_REQUEST['type'];
		if($type == "Update")
		{
			$tRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingTemplateDetails?mode=2&mId=$mId&note=$note&endNote=$eText&dbName=".$_SESSION['dbname']);
		}
		else
		{
			$tRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingTemplateDetails?mode=1&maId=$mId&note=$note&endNote=$eText&dbName=".$_SESSION['dbname']);	
		}
		echo $tRes;
		
		$memAttRes=json_decode($_REQUEST['memIdArray']);
		echo $obj_Cminutes->deleteAttendance($mId);
		for($i=0;$i<sizeof($memAttRes);$i++)
		{
			
			$attRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingAttendance?mode=7&mId=$mId&memId=$memAttRes[$i]&dbName=".$_SESSION['dbname']);
		}
	}
	if($_REQUEST['method']=="FetchPreview")
	{
		$mId=urlencode($_REQUEST['mId']);
		//echo "Mid:".$mId;
		$meetingRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=6&mId=$mId&dbName=".$_SESSION['dbname']);
		//echo $meetingRes;
		//echo "#";
		$meetingRes=json_decode($meetingRes,true);
		
		$agendaRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingQue?mode=5&mId=$mId&dbName=".$_SESSION['dbname']);
		//echo $agendaRes;
		//echo "#";
		$agendaRes=json_decode($agendaRes,true);
		
		$tempRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingTemplateDetails?mode=4&maId=$mId&dbName=".$_SESSION['dbname']);
		//echo $TempRes;
		$tempRes=json_decode($tempRes,true);
		
		$socId=$_SESSION['society_id'];
		//echo "id:".$socId;
		$socName=$obj_Cminutes->getSocietyName($socId);
		//echo "soc Name:".$socName;
		//echo "title:".$meetingRes['Title'];
		$date=explode("-",$meetingRes['MeetingDate']);
		$groupId = $meetingRes['GroupId'];
		//echo $groupId;
		$sqlDate=$date[2].$date[1].$date[0];
		$dayName=date("l", mktime(0,0,0,$date[1],$date[0],$date[2]));
		$monName=date('F', strtotime($sqlDate));
		$heading="<table width='100%'><tr><td style='text-align:left;width:50%'> </td><td style='text-align:right;width:50%'>Date:__/__/____</td></tr></table><table style='text-align:center;width:100%'><tr><td><b>".$socName." Minutes of ".$meetingRes['Title']." held on ".$dayName.",".$date[0]." ".$monName." ".$date[2]."</b></td></tr></table>";
		$note=$tempRes['Note'];
		$len=sizeof($agendaRes);
		$agendaContent="<table style='width:100%;'><tr><td><b>Follwing are the agenda of meeting and resolution:</b></td></tr>";
		for($i=0;$i<$len;$i++)
		{
			$resP = $obj_Cminutes->getProposedByName($agendaRes[$i]['ProposedBy'],$groupId);
			$resS = $obj_Cminutes->getSecondedByName($agendaRes[$i]['SecondedBy'],$groupId);
			//echo "GroupId:".$groupId."<br>PBy:".$agendaRes[$i]['ProposedBy']."<br>SBy:".$agendaRes[$i]['SecondedBy']."<br>";
			$agendaContent.="<tr><td style='margin-left:3%'><b>".($i+1).".".$agendaRes[$i]['Question'].".</b></td></tr><tr><td style='margin-left:3%'>".$agendaRes[$i]['Minutes']."</td></tr><tr><td style='margin-left:3%'>".$agendaRes[$i]['Resolution']."</td></tr><tr><td style='margin-left:3%;text-align:right'> Proposed by ".$resP[0]['other_name']."<br>Seconded by ".$resS[0]['other_name']."<br>Passed ".$agendaRes[$i]['PassedBy']."</td></tr><tr><td><br></td></tr>";
		}
		$agendaContent.="</table>";
		$footer="<br>"."<table style='width:100%;'><tr><td style='text-align:center'>".$tempRes['EndNote']."</td></tr><tr><td><b>For ".$socName."</b></td></tr><tr><td><br><br></td></tr><tr><td><b>Secretary &nbsp;&nbsp;&nbsp;&nbsp;Treasurer &nbsp;&nbsp;&nbsp;&nbsp;Chairman</b></td></tr></table>";
		$finalData=$heading.$note.$agendaContent.$footer;
		echo $finalData;
	}
	if($_REQUEST['method']=="viewMinutes")
	{
		$mId=urlencode($_REQUEST['mId']);
		//echo "Mid:".$mId;
		$meetingRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=6&mId=$mId&dbName=".$_SESSION['dbname']);
		//echo $meetingRes;
		//echo "#";
		$meetingRes=json_decode($meetingRes,true);
		
		$agendaRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingQue?mode=5&mId=$mId&dbName=".$_SESSION['dbname']);
		//echo $agendaRes;
		//echo "#";
		$agendaRes=json_decode($agendaRes,true);
		
		$tempRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeetingTemplateDetails?mode=4&maId=$mId&dbName=".$_SESSION['dbname']);
		//echo $TempRes;
		//echo $tempRes;
		//echo "#";
		$tempRes=json_decode($tempRes,true);
		
		$socId=$_SESSION['society_id'];
		//echo "id:".$socId;
		$socName=$obj_Cminutes->getSocietyName($socId);
		//echo "soc Name:".$socName;
		//echo "title:".$meetingRes['Title'];
		$groupId = $meetingRes['GroupId'];
		$date=explode("-",$meetingRes['MeetingDate']);
		$sqlDate=$date[2].$date[1].$date[0];
		$dayName=date("l", mktime(0,0,0,$date[1],$date[0],$date[2]));
		$monName=date('F', strtotime($sqlDate));
		$heading="<table width='100%'><tr><td style='text-align:left;width:50%'> </td><td style='text-align:right;width:50%'>Date:__/__/____<br></td></tr></table><table style='text-align:center;width:100%'><tr><td><b>".$socName." Minutes of ".$meetingRes['Title']." held on ".$dayName.",".$date[0]." ".$monName." ".$date[2]."</b></td></tr></table><br>";
		$note=$tempRes['Note'];
		$len=sizeof($agendaRes);
		$agendaContent="<table style='width:100%;'><tr><td><b>Follwing are the agenda of meeting and resolution:</b><br></td></tr>";
		for($i=0;$i<$len;$i++)
		{
			$resP = $obj_Cminutes->getProposedByName($agendaRes[$i]['ProposedBy'],$groupId);
			$resS = $obj_Cminutes->getSecondedByName($agendaRes[$i]['SecondedBy'],$groupId);
			$agendaContent.="<tr><td style='margin-left:3%'><b>".($i+1).".".$agendaRes[$i]['Question'].".</b></td></tr><tr><td style='margin-left:3%'>".$agendaRes[$i]['Minutes']."</td></tr><tr><td style='margin-left:3%'>".$agendaRes[$i]['Resolution']."</td></tr><tr><td style='margin-left:3%;text-align:right'> Proposed by ".$resP[0]['other_name']."<br>Seconded by ".$resS[0]['other_name']."<br>Passed ".$agendaRes[$i]['PassedBy']."</td></tr><tr><td><br></td></tr>";
		}
		$agendaContent.="</table>";
		$footer="<br>"."<table style='width:100%;'><tr><td style='text-align:center'>".$tempRes['EndNote']."<br><br></td></tr><tr><td><b>For ".$socName."</b></td></tr><tr><td><br><br></td></tr><tr><td><b>Secretary &nbsp;&nbsp;&nbsp;&nbsp;Treasurer &nbsp;&nbsp;&nbsp;&nbsp;Chairman</b></td></tr></table>";
		$finalData=$heading.$note."<br><br>".$agendaContent.$footer;
		echo $finalData;
	}
	if($_REQUEST['method']=="final")
	{
		$mId=urlencode($_REQUEST['mId']);
		$status="4";
		$res=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=12&mId=$mId&s=$status&dbName=".$_SESSION['dbname']);
		echo $res;
	}
	if($_REQUEST['method']=="getHeader")
	{
		$data=$obj_Cminutes->getHead();
		echo $data;
	}
?>
<?php include_once("../classes/createGrp.class.php");
	include_once("../classes/momGroup.class.php");	
	include_once("../classes/include/dbop.class.php");
	$dbConn = new dbop();
	$obj_cGrp=new createGrp($dbConn);
	$obj_momGrp=new momGroup($dbConn);
	//echo "dad";
	//echo $_REQUEST["method"]."@@@";
	//echo "req:".$_REQUEST["method"];
	if($_REQUEST['method']=="Fetch")
	{
		$MemId=json_decode($_REQUEST['memIdArray']);
		$gName=$_REQUEST['gName'];
		$gDes=$_REQUEST['gDes'];
		//echo "in Ajax:<br>Name:".$gName."<br>Des:".$gDes."MemId: ".$MemId;
		//echo "<pre>";
		//print_r($MemId);
		//echo "</pre>";
		$res=$obj_cGrp->addGroupDetails($gName, $gDes);
		//echo "Group Res:".$res;
		$memRes = $obj_cGrp->addGrpMemberDetails($MemId, $gName);
		//echo "Mem Res:".$memRes;
			/*if($memRes=="success")
			{
				echo "Group created successfully..";
			}
			else
			{
				echo "Server failure...";
			}*/
		/*else if($res=="problem")
		{
			echo "Group name already exits..";
		}
		else
		{
			echo "Server failure...";
		}*/
		//e cho "Result mem: ".$select_type;
	}
	
		//echo $select_type;
		//print_r ("in momgroup.ajax:".$select_type);
		/*foreach($select_type as $k => $v)
		{
				echo $v."#";
		}*/
	
	/*if($_REQUEST["method"]=="delete")
	{
		//echo "In delete of ajax";
		$res=$obj_momGrp->deleting();
		//print_r($res);
		return "Data Deleted Successfully";
	}
	*/
	if($_REQUEST['method']=="Edit")
	{
		$groupId=$_REQUEST['groupId'];
		$grpRes=$obj_cGrp->getGroupDetails($groupId);
		$memberId=$obj_cGrp->getMemDetails($groupId);
		ob_clean();
		echo json_encode($memberId);
		echo "#";
		echo json_encode($grpRes);
	}
	if($_REQUEST['method']=="Update")
	{
		$gId=$_REQUEST['gId'];
		$gName=urlencode($_REQUEST['gName']);
		$gDes=urlencode($_REQUEST['gDes']);
		$res=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/servletGrpMember?mode=6&gId=$gId&dbName=".$_SESSION['dbname']);
		ob_clean();
	  	$jRes=json_decode($res,true);
		$memExId=array();
		for($i=0;$i<sizeof($jRes);$i++)
		{
			$memExId[$i]=$jRes[$i]['MemberId'];
		}
		/*echo "<pre>";
		print_r ($memExId);
		echo "</pre>";*/
		$MemId=json_decode($_REQUEST['memIdArray']);
		/*echo "<pre>";
		print_r ($MemId);
		echo "</pre>";*/
		$memNew=array();
		$memNew = array_diff($MemId, $memExId);
		//$memNew=json_encode($memNew,true);
		/*echo "<pre>";
		print_r ($memNew);
		echo "</pre>";*/
		$i=0;
		foreach($memNew as $key => $value)
		{
			$insert[$i]=$value;
			$i=$i+1;
		}
		/*echo "<pre>";
		print_r ($insert);
		echo "</pre>";*/
		$res = file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/Servlet1?mode=2&id=$gId&name=$gName&des=$gDes&dbName=".$_SESSION['dbname']); 
		//echo "Group:".$res;
		for($i=0;$i<sizeof($insert);$i++)
		{
			$mId=$insert[$i];
			//echo "mId".$insert[$i];
			$memRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/servletGrpMember?mode=1&gId=$gId&memId=$mId&dbName=".$_SESSION['dbname']);
			//echo "<br>Member:".$memRes;
		}
		
	}
?>
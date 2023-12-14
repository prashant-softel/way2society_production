<?php
include_once("../classes/include/dbop.class.php");
 include_once("../classes/create_poll.class.php");
 include_once("../classes/utility.class.php");
$dbConnRoot = new dbop(true); 
$dbConn=new dbop();
$obj_create_poll = new create_poll($dbConnRoot,$dbConn);
$obj_Utility = new utility($dbConn, $dbConnRoot);

echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_create_poll->selecting($_REQUEST['pollId']);

	foreach($select_type as $k => $v)
	{
		foreach($v as $kk => $vv)
		{
			echo $vv."#";
		}
	}
}
if($_REQUEST["method"]=="delete")
{

	$obj_create_poll->deleting($_REQUEST['pollId']);
	return "Data Deleted Successfully";
}
if($_REQUEST["method"]  == "ShowSMSTemplate")
{
	echo $obj_create_poll->getSMSTemplate($_REQUEST['PollQuestion'], $_REQUEST['IsUpdateRequest'], $_REQUEST['IsSubChange'], $_REQUEST['OriginalSub']);
}

if($_REQUEST["method"]  == "SMSTest")
{
	echo $obj_Utility->SendDemoSMS($_REQUEST['TestMobileNo'], $_REQUEST['SMSTemplate']);
}

if($_REQUEST["method"]=="answer")
{
	//print_r ($_SESSION);
	$poll_id=$_REQUEST["pollID"];
	$option_id=$_REQUEST["optionID"];
	$revote=$_REQUEST['Re-Vote'];
	$comment_rev=$_REQUEST['comment_rev'];
	echo $$revote;
	//echo $option_id;
	//print_r($_REQUEST);
	if($revote==1)
	{
		 $check="SELECT a.poll_id ,a.option_id ,b.option_id ,b.unit_id FROM poll_vote as b JOIN poll_option as a ON b.option_id = a.option_id WHERE a.poll_id = '".$poll_id."' and b.unit_id='".$_SESSION['unit_id']."'and b.society_id='".$_SESSION['society_id']."' and b.Isvalid='1'";
		$select = $obj_create_poll->m_dbConnRoot->select($check);
		//print_r($select);
		for($i=0;$i<sizeof($select);$i++)
		{
			$optin=$select[$i]['option_id'];
		$upquery="Update `poll_vote` set `Isvalid`='0' where option_id='".$optin."' and unit_id='".$_SESSION['unit_id']."' and society_id='".$_SESSION['society_id']."' ";	
		$result = $obj_create_poll->m_dbConnRoot->update($upquery);
		 $vote = "update `poll_option` set `counter`=`counter` -1 where `option_id`='".$optin."' and `poll_id`='".$poll_id."'";
	$result = $obj_create_poll->m_dbConnRoot->update($vote);
		//print_r($result);
		}
	}
	$vote="insert into `poll_vote`(`option_id`,`login_id`,`society_id`,`unit_id`,`member_id`,`mem_comment`) VALUE ('".$option_id."','".$_SESSION['login_id']."','".$_SESSION['society_id']."','".$_SESSION['unit_id']."','".$_SESSION['member_id']."','".$comment_rev."')";	
	$result = $obj_create_poll->m_dbConnRoot->insert($vote);
	
	 $voteCount = "update `poll_option` set `counter`=`counter` +1 where `option_id`='".$option_id."' and `poll_id`='".$poll_id."'";
	$result = $obj_create_poll->m_dbConnRoot->update($voteCount);
}

?>
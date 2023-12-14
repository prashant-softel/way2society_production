
<?php 
include_once("../classes/include/dbop.class.php");
include_once("../classes/notice.class.php");
include_once("../classes/utility.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$obj_notice = new notice($dbConn,$dbConnRoot);
$obj_Utility = new utility($dbConn, $dbConnRoot);

//echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_notice->selecting();

	//foreach($select_type as $k => $v)
	//{
		//foreach($v as $kk => $vv)
		//{
		//	echo $vv."#";
		//}
	//}
	echo $_REQUEST["method"]."@@@".json_encode($select_type[0]); 
}

if($_REQUEST["method"]=="delete")
{
	$obj_notice->deleting();
	echo $_REQUEST["method"]."@@@Data Deleted Successfully";
}
if($_REQUEST["method"]=="fetch_templates")
{
	$template_id = $_REQUEST["template_id"];
	$templates = $obj_notice->fetch_template_details($template_id);
	echo json_encode($templates);
	 
	
}
if($_REQUEST['method']=="Fetch")
	{
		$gId = $_REQUEST['gId'];
		//echo "gId:".$gId;
		$memId = $obj_notice->getGroupMembers($gId);
		echo $memId;
	}

if($_REQUEST['method']=="resend_notifiction")
	{
		$notice_id = $_REQUEST['notice_id'];
		$sms_text = $_REQUEST['sms_text'];
		$notification = $_REQUEST['notification'];
		$result = $obj_notice->resendNotification($notice_id, $sms_text, $notification);
		echo '@@@'.json_encode($result);
	}


	
if($_REQUEST["method"]  == "ShowSMSTemplate")
{
	$Subject = $_REQUEST['subject'];
	$IsUpdate = $_REQUEST['IsUpdateRequest'];
	$IsSubChange = $_REQUEST['IsSubChange'];
	$OriginalSub = $_REQUEST['OriginalSub'];
	echo $obj_notice->getSMSTemplate($Subject, $IsUpdate ,$IsSubChange, $OriginalSub);
}

if($_REQUEST["method"]  == "SMSTest")
{
	$TestMobileNo = $_REQUEST['TestMobileNo'];
	$MsgBody = $_REQUEST['SMSTemplate'];
	echo $obj_Utility->SendDemoSMS($TestMobileNo, $MsgBody);
}
?>
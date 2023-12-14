<?php include_once("../classes/approval_module.class.php");
include_once("../classes/include/dbop.class.php");
include_once("../classes/approval_module.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$obj_approval_module = new approval_module($dbConn,$dbConnRoot);


//echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
{
	//echo "Edit";
	$select_type = $obj_approval_module->selecting();

	/*foreach($select_type as $k => $v)
	{
		foreach($v as $kk => $vv)
		{
			echo $vv."#";
		}
	}*/
	echo $_REQUEST["method"]."@@@".json_encode($select_type[0]); 
}
if($_REQUEST["method"]=="editDocumentDetails")
{
	echo $_REQUEST["method"]."@@@";
	//echo "Edit Dociments";
	$select_type = $obj_approval_module->getDocuments($_REQUEST['approvalId']);
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
	
	//echo "Deleted";
	//die();
	$obj_approval_module->deleting();
	echo $_REQUEST["method"]."@@@Data Deleted Successfully";
	//return "Data Deleted Successfully";
}
if($_REQUEST["method"] == "Feedback")
{
	//echo "Submit Feedback";
	//$msg = '@@@';
	$success =0;
	$approvalID=$_REQUEST['approvalID'];
	$comments=$_REQUEST['comments'];
	$login_id=$_REQUEST['login_id'];
	$selectOption=$_REQUEST['selectOption']; 
	$OtherMemId=$_REQUEST['CommetteeId']; 
	$result = $obj_approval_module->SubmitFeedback($approvalID,$comments,$login_id,$selectOption,$OtherMemId);
	if($result > 0)
	{
		$success =1;
	}
	else
	{
		$success =0;
	}
	echo '@@@'.$success;
}
/*if($_REQUEST["method"]  == "ShowSMSTemplate")
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
}*/
?>
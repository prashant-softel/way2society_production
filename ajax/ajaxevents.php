<?php
include_once("../classes/events.class.php");
include_once("../classes/include/dbop.class.php");
include_once("../classes/utility.class.php");

$dbConnRoot = new dbop(true);
$dbConn = new dbop();

$obj_events=new events($dbConn,$dbConnRoot);
$obj_Utility = new utility($dbConn, $dbConnRoot);
echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
	{
	$select_type=$obj_events->selecting($_REQUEST['eventId']);
	foreach($select_type as $k => $v)
		{
		foreach($v as $kk => $vv)
			{
			echo $vv."^";
			}
		}
	}
if($_REQUEST["method"]=="delete")
	{
	$obj_events->deleting($_REQUEST['eventId']);
	return "Data Deleted Successfully";
	}
	if($_REQUEST["method"]=="delete1")
	{
	$result = $obj_events->deleting1($_REQUEST['eventsId']);
	echo $result;
	return $result;
	}
	
if($_REQUEST["method"]  == "ShowSMSTemplate")
{

	echo $obj_events->getSMSTemplate($_REQUEST['Eventsubject'], $_REQUEST['IsUpdateRequest'] ,$_REQUEST['IsSubChange'], $_REQUEST['OriginalSub']);
}

if($_REQUEST["method"]  == "SMSTest")
{
	echo $obj_Utility->SendDemoSMS($_REQUEST['TestMobileNo'], $_REQUEST['SMSTemplate']);
}
	
?>

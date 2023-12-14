<?php 
include_once("../classes/addSMSQuota.class.php");
include_once("../classes/include/dbop.class.php");
include_once("../classes/dbconst.class.php");
$dbConn = new dbop(); 
$dbConnRoot = new dbop(true); 
$obj_addSMSQuota = new addSMSQuota($dbConn,$dbConnRoot);
if(isset($_REQUEST['method']))	
{
	if($_REQUEST["method"]=="fetchSMSQuota")
	{
		$smsQuotaId = $_REQUEST['smsQuotaId'];
		$res = $obj_addSMSQuota->getSMSQuotaDetails($smsQuotaId);
		foreach($res as $k => $v)
		{
			foreach($v as $kk => $vv)
			{
				echo $vv."#";
			}
		}
	}
}
?>
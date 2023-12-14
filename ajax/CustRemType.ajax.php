<?php 
include_once("../classes/include/dbop.class.php");
include_once("../classes/dbconst.class.php");
include_once("../classes/viewcustomerreminder.class.php");
$dbConn=new dbop;
$dbConnRoot=new dbop(true);
$objrem=new SMSReminder($m_dbConn,$m_dbConnRoot);

if(isset($_REQUEST['method']))
{
	if($_REQUEST['method']=="getCustdetails")
	{
		$Id=$_REQUEST['Id'];
		$res=$objrem->getAllRemdata($Id);
		foreach($res as $k=>$v)
		{
			foreach($v as $kk => $vv)
			{
				echo $vv."#";
			}
		}
		
	}
	
	if($_REQUEST['method']=="deleteCustremdetails")
	{
		$Id=$_REQUEST['Id'];
		$res1=$objrem->deleteRemdata($Id);
		echo $res1;
		
	}
	if($_REQUEST['method']=="deleteProcessCustremdetails")
	{
		$Id=$_REQUEST['Id'];
		$res2=$objrem->deleteProcessRemdata($Id);
		echo $res2;
	}
}









?>
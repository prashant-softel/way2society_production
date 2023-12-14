<?php
try
{
	error_reporting(0);
	$errorfile_name = 'classes/logfile.txt';
	//$this->errorLog = $this->errorfile_name;
	$errorfile = fopen($errorfile_name, "a");
	$errormsg = "starting new image upload";
	$msgFormat=$errormsg."\r\n";
	fwrite($errorfile,$msgFormat);
	//include_once("classes/include/dbop.class.php");

$errorfile_name = "image_upload_errorlog.txt";
//echo file_put_contents($errorfile_name,"Hello World. Testing!");
$errorfile = fopen($errorfile_name, "a");
$errormsg = "inside GDriveTest";
$msgFormat=$errormsg."\r\n";

fwrite($errorfile,$msgFormat);

if(include_once('google-api-php-client/src/Google/Client.php'))
{
$errormsg = "done client loading..";
$msgFormat=$errormsg."\r\n";
fwrite($errorfile,$msgFormat);

}
if(include_once('google-api-php-client/src/Google/Service/Oauth2.php'))
{
$errormsg = "done oath2 loading..";
$msgFormat=$errormsg."\r\n";
fwrite($errorfile,$msgFormat);

}
if(include_once('google-api-php-client/src/Google/Service/Drive.php'))
{
	$errormsg = "done Drive loading..";
	$msgFormat=$errormsg."\r\n";
	fwrite($errorfile,$msgFormat);
}
//error_reporting(7);
//session_start();
$errormsg = "include gdrive complete";
$msgFormat=$errormsg."\r\n";
fwrite($errorfile,$msgFormat);

//include_once("classes/include/dbop.class.php");
//$objConn = new dbop($m_dbConn);
//$res= $objConn->select("select * from society");
//print_r($res);
//header('Content-Type: text/html; charset=utf-8');
// Init the variables
//include_once("classes/notice.class.php");
//include_once("classes/utility.class.php");
//include_once("classes/CDocumentsUserView.class.php");
$errormsg = "include all complete";
$msgFormat=$errormsg."\r\n";
fwrite($errorfile,$msgFormat);

}
catch(Exception $exp)
 {
 	$errormsg = "Exception:".$exp;
	$msgFormat=$errormsg."\r\n";
	fwrite($errorfile,$msgFormat);
	
}
//$display_notices=$obj_notice->FetchAllNotices($_REQUEST['in']);
//echo "<pre>";
//print_r($display_notices);
//echo "</pre>";


class TestFile
{
	public $m_dbConn;
	public $m_service;
	public $m_UnitNo;
	public $m_sRootFolderID;
	public $m_bShowTrace;
	public $m_notice;
	public $m_objUtility;
	public $m_bSetupInProgress;
	
	function __construct($dbConn, $UnitNo = 0, $rootid = "", $ShowTrace = 0, $bSetupProcess = 0)
	{
		//print_r($dbConn);
		$errorfile_name = 'image_upload_errorlog_'.date("d.m.Y").'.html';
			
		if($ShowTrace)
		{
			//$this->errorLog = $this->errorfile_name;
			//$errorfile = fopen($errorfile_name, "a");
		}
		$this->m_dbConn = $dbConn;
		$this->m_UnitNo = $UnitNo;
		$this->m_sRootFolderID = $rootid;
		//echo "before Initialize".$UnitNo ;
		if($ShowTrace)
		{
			echo "inside Initialize";
			$errormsg = "inside gdrive ctor";
			$msgFormat=$errormsg."\r\n";
			fwrite($errorfile,$msgFormat);
		}
		$this->m_bShowTrace = $ShowTrace;
		//$this->m_notice = new notice($dbConn);
		//$this->m_objUtility = new utility($dbConn);
		if($ShowTrace)
		{
			echo "before Initialize";

				$errormsg = "inside gdrive ctor";
				$msgFormat=$errormsg."\r\n";
				fwrite($errorfile,$msgFormat);
		}
		$this->m_bSetupInProgress = $bSetupProcess;
		//$this->Initialize($bSetupProcess); 
	}
	
}

?>

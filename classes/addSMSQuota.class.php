 <?php
include_once("dbconst.class.php");
include_once("include/display_table.class.php");
include_once("utility.class.php");
class addSMSQuota extends dbop
{
	public $m_dbConn;
	public $m_dbConnRoot;
	public $actionPage = "../viewSMSQuota.php";
	public $obj_utility;
	function __construct($dbConn, $dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->m_dbConnRoot = $dbConnRoot;
		$this->obj_utility=new utility($this->m_dbConn, $this->m_dbConnRoot);
		//dbop::__construct();
	}
	public function startProcess()
	{	
		if($_REQUEST['btnSubmit'] == "Submit")
		{
			//insert
			//echo "<pre>";
			//print_r($_POST);
			//echo "</pre>";
			$clientId = $_POST['clientId'];
			$societyId = $_POST['societyId'];
			$sellDate = getDBFormatDate($_POST['sellDate']);
			$soldBy = $_POST['soldBy'];
			$smsAllotted = $_POST['smsAllotted'];
			$amount = $_POST['amount'];
			$paymentStatus = $_POST['paymentStatus'];
			if($paymentStatus == "")
			{
				$paymentStatus = "2";
			}
			$note = $_POST['note'];
			$recordedBy = $_SESSION['login_id'];
			$timeStamp=date('d/m/Y h:i:s', time());
			$status="Y";
			$sql="INSERT INTO `sms_allotment`(`ClientId`,`SocietyId`,`SellDate`,`SoldBy`,`SMSAllotted`,`Amount`,`Payment_Received`,`Note`,`RecordedBy`,`Status`,`TimeStamp`) VALUES (".$clientId.",'".$societyId."','".$sellDate."','".$soldBy."','".$smsAllotted."','".$amount."','".$paymentStatus."','".$note."','".$recordedBy."','".$status."','".$timeStamp."')";
			//echo "query:".$sql;
			if($this->m_bShowTrace == 1)
			{
				echo $sql."<br>";
			}
			$res = $this->m_dbConnRoot->insert($sql);
			if($this->m_bShowTrace == 1)
			{
				echo $res;
			}
			//$this->actionPage .="?societyId=".$societyId;
			return "Insert";
		}
		if($_REQUEST['btnSubmit'] == "Update")
		{
			//insert
			echo "<pre>";
			print_r($_POST);
			echo "</pre>";
			$Id = $_POST['smsQuotaId'];
			$clientId = $_POST['clientId'];
			$societyId = $_POST['societyId'];
			$sellDate = getDBFormatDate($_POST['sellDate']);
			$soldBy = $_POST['soldBy'];
			$smsAllotted = $_POST['smsAllotted'];
			$amount = $_POST['amount'];
			$paymentStatus = $_POST['paymentStatus'];
			if($paymentStatus == "")
			{
				$paymentStatus = "2";
			}
			$note = $_POST['note'];
			$recordedBy = $_SESSION['login_id'];
			$timeStamp=date('d/m/Y h:i:s', time());
			$status="Y";
			$sql="Update `sms_allotment` set `ClientId` = '".$clientId."',`SocietyId` = '".$societyId."',`SellDate` = '".$sellDate."',`SoldBy` = '".$soldBy."',`SMSAllotted` = '".$smsAllotted."',`Amount` = '".$amount."',`Payment_Received` = '".$paymentStatus."',`Note` = '".$note."',`RecordedBy` = '".$recordedBy."',`Status` = '".$status."',`TimeStamp` = '".$timeStamp."' where `Id` = ".$Id;
			echo "query:".$sql;
			if($this->m_bShowTrace == 1)
			{
				echo $sql."<br>";
			}
			$res = $this->m_dbConnRoot->update($sql);
			if($this->m_bShowTrace == 1)
			{
				echo $res;
			}
			//echo $res;
			//$this->actionPage .="?societyId=".$societyId;
			return "Update";
		}
	}
	function getSMSQuotaDetails($Id)
	{
		$sql = "SELECT * FROM `sms_allotment` where `Id` = ".$Id;
		$res = $this->m_dbConnRoot->select($sql);
		return $res;
	}
}
?>	
 <?php
include_once("dbconst.class.php");
include_once("include/display_table.class.php");
include_once("utility.class.php");
class addParkingType extends dbop
{
	public $m_dbConn;
	public $m_dbConnRoot;
	public $actionPage = "../viewParkingType.php";
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
			$parkingType = $_POST['parkingType'];
			$description = $_POST['description'];
			$rate = $_POST['rate'];
			$visible = $_POST['visible'];	
			$ledgerId = $_POST['ledgerId'];
			if($visible == "")
			{
				$visible = "0";
			}
			$timeStamp = date('d/m/Y h:i:s', time());
			$status = "Y";
			$sql = "INSERT INTO `parking_type`(`ParkingType`,`Description`,`Rate`,`IsVisible`,`LinkedToLedgerId`,`Status`,`TimeStamp`) VALUES ('".$parkingType."','".$description."','".$rate."','".$visible."','".$ledgerId."','".$status."','".$timeStamp."')";
			//echo "query:".$sql;
			if($this->m_bShowTrace == 1)
			{
				echo $sql."<br>";
			}
			$res = $this->m_dbConn->insert($sql);
			if($this->m_bShowTrace == 1)
			{
				echo $res;
			}
			//$this->actionPage .="?societyId=".$societyId;
			return "Insert";
		}
		if($_REQUEST['btnSubmit'] == "Update")
		{
			//update
			$parkingTypeId = $_POST['parkingTypeId'];
			$parkingType = $_POST['parkingType'];
			$description = $_POST['description'];
			$rate = $_POST['rate'];
			$visible = $_POST['visible'];	
			$ledgerId = $_POST['ledgerId'];
			if($visible == "")
			{
				$visible = "0";
			}
			$timeStamp=date('d/m/Y h:i:s', time());
			$status="Y";
			$sql="Update `parking_type` set `ParkingType` = '".$parkingType."',`Description` = '".$description."',`Rate` = '".$rate."',`IsVisible` = '".$visible."',`LinkedToLedgerId` = '".$ledgerId."',`Status` = '".$status."',`TimeStamp` = '".$timeStamp."' where `Id` = ".$parkingTypeId;
			//echo "query:".$sql;
			if($this->m_bShowTrace == 1)
			{
				echo $sql."<br>";
			}
			$res = $this->m_dbConn->update($sql);
			if($this->m_bShowTrace == 1)
			{
				echo $res;
			}
			return "Update";
		}
	}
}
?>	 
 <?php
include_once("dbconst.class.php");
include_once("include/display_table.class.php");
include_once("utility.class.php");
class rentingRegistration extends dbop
{
	public $m_dbConn;
	public $m_dbConnRoot;
	public $actionPage = "../lien.php";
	public $obj_utility;
	function __construct($dbConn, $dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->m_dbConnRoot = $dbConnRoot;
		$this->obj_utility=new utility($this->m_dbConn, $this->m_dbConnRoot);
		//dbop::__construct();
	}
	//Used to get UnitNo and owner name from unit and member_main
	public function getProfession()
	{
		$sqlForProfession = "Select Id, Profession from profession";
		$professionDeatils = $this->m_dbConn->select($sqlForProfession);
		return $professionDeatils;
	}
	public function getAllTenantByUnitId($UnitId)
	{
		$sqlForTenant = "";
		if($UnitId != 0)
		{
			$sqlForTenant = "SELECT `tenant_id`, CONCAT(`tenant_name`,' ',`tenant_MName`,' ',`tenant_LName`) as tenantName, `mobile_no`,`email`,`start_date`,`end_date`,`tenantStatus`,`active` FROM `tenant_module` where `unit_id` = '".$UnitId."' and `status` = 'Y' ";
		}
		else
		{
			$sqlForTenant = "SELECT t.`tenant_id`, CONCAT(t.`tenant_name`,' ',t.`tenant_MName`,' ',t.`tenant_LName`) as tenantName, t.`mobile_no`,t.`email`,t.`start_date`,t.`end_date`,t.`tenantStatus`,t.`active`, CONCAT('[',u.`unit_no`,']',' ',m.`owner_name`) as memberName FROM `tenant_module` as t,unit as u,member_main as m where m.`unit` = u.`unit_id` and t.`status` = 'Y' and m.`ownership_status` = '1' and t.`unit_id` = u.`unit_id`";
		}
		$tenantDetails = $this->m_dbConn->select($sqlForTenant);
		return $tenantDetails;
	}
	public function getMemberId($UnitId)
	{
		$sqlForTenant = "SELECT `member_id` FROM `member_main` where `unit` = '".$UnitId."' and `ownership_status` = '1' and `status` = 'Y' ";
		$tenantDetails = $this->m_dbConn->select($sqlForTenant);
		return $tenantDetails['0']['member_id'];
	}
}
?>	
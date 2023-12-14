<?php
include_once("dbconst.class.php");
include_once("utility.class.php");

class CustomerReminder
{
	
	public $m_dbConn;
	public $obj_utility;
	public $m_dbConnRoot;
	
	function __construct($dbConn,$dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->obj_utility = new utility($this->m_dbConn);
		
	}
	public function getTargetName()
	{
		$narray=array();
		$sqlexec="select id,Name from membergroup group by Name order by Name";
		$result=$this->m_dbConn->select($sqlexec);
		for($i=0;$i<sizeof($result);$i++)
		{
			$grpname=$result[$i]['Name'];
			$grpid=$result[$i]['id'];
			array_push($narray,array("gnme"=>$grpname,"gid"=>$grpid));
		}
		return $narray;
		
	}
	
	
	
	
	
	
}



?>
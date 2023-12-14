<?php

include_once ("dbconst.class.php"); 
include_once( "include/fetch_data.php");
include_once( "utility.class.php");
include_once('../swift/swift_required.php');
include_once("android.class.php");
//echo "include_ gdrive";
include_once("../GDrive.php");


class backup
{
	public $m_dbConn;
	public $m_dbConnRoot;
	public $objFetchData;
	public $m_obj_utility;
	public $m_bShowTrace;
	
	function __construct($dbConn, $dbConnRoot, $socID = "")
	{  
		$dbConnRoot=new dbop(true);
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->objFetchData = new FetchData($dbConn);
		$this->m_obj_utility = new utility($dbConn, $dbConnRoot);
		$this->m_bShowTrace = true;
		
		//echo "checked";
	} 
	public function backup($society)
	{
		
		$sqlQuery = "SELECT * FROM `societydb_maintain` where society_id='".$society."' order by id desc";	
		$res = $this->m_dbConnRoot->select($sqlQuery);	
		return $res;
	
	}
}
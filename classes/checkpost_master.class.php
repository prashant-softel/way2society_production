<?php
include_once("utility.class.php");

class SM_Report 
{	
  public $m_dbConn;	
  public $m_dbConnRoot;		
  public $smConn;
  public $smConnRoot;
  public $m_objUtility;
  public $m_fetch;
  public $actionPage;
  public $obj_fetch;

  function __construct($dbConn,$dbConnRoot,$smConn,$smConnRoot)
  {
	  //** assing the connection to the variable
	  $this->m_dbConn = $dbConn;
	  $this->m_dbConnRoot = $dbConnRoot;
	  $this->smConn = $smConn;
	  $this->smConnRoot = $smConnRoot;
	  $this->m_objUtility =  new utility($this->m_dbConn);
	  $this->obj_fetch = new FetchData($this->m_dbConn);
	  $a = $this->obj_fetch->GetSocietyDetails($_SESSION['society_id']);

  }
  public function startProcess()
{	
	$errorExists=0;
	if($_POST['insert']=='Insert')
	{
		$society_name = $this->obj_fetch->objSocietyDetails->sSocietyCode;
		$code = "";
		$refNo = 0;
        //$sqlquery="select max(qrcode) as qrcode from `checkpost_master`";
		$sqlquery="select max(code_ref) as qrcode from `checkpost_master`";
		$result = $this->smConn->select($sqlquery);
		if($result[0]['qrcode'] <> '')
		{
			$num=$result[0]['qrcode']+1; 
			$num_length = strlen((string)$num);
			if($num_length == 1)
			{
				$code=$society_name."00".$num;
				
			}
			elseif($num_length == 2)
			{
				$code=$society_name."0".$num;
			}
			else
			{
				$code=$society_name.$num;
			}
			$refNo = $num;
		}
		else
		{
			$code=$society_name."001";
			$refNo = 1;
		}
		
		 $insertQuery6 = "insert into `checkpost_master` (`checkpost_name`,`desc`,`qrcode`,`code_ref`) value('".$_POST['name1']."','".$_POST['desc']."','".$code."','".$refNo."')";
	 	$Insert = $this->smConn->insert($insertQuery6);
		$this->actionPage='../checkpost_master.php';
	 	return "Insert";	   
 	}
 	else if($_POST['insert']=='Update')
 	{ 	
 		$IsSuccess = true;
 		try
 		{  
 			 $this->smConn->begin_transaction();
	 	  	 $updateQUery="Update `checkpost_master` set `checkpost_name`='".$_POST['name1']."',`desc`='".$_POST['desc']."' where `id`='".$_REQUEST['id']."'";
			 $Update = $this->smConn->update($updateQUery);		
			 $this->smConn->commit();
 		}
 		catch(Exception $exp)
		{			
			$this->smConn->rollback();
			$IsSuccess = false;				
		}
 		
		$this->actionPage='../checkpost_master.php';
 	 	return "Update";	
 	}

}

public function getcheckpost_report()
{
	$sql = "SELECT * FROM `checkpost_master` order by id DESC";
	$res = $this->smConn->select($sql);
	return $res;
}

public function selecting($Id)
{
	$sql = "SELECT `checkpost_name`, `desc`, `qrcode`, `qrcodepath` FROM `checkpost_master` WHERE `id` = '".$Id."'";
	$res =  $this->smConn->select($sql);
	return $res;	
}
}
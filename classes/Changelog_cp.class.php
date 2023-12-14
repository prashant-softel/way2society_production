<?php
include_once("dbconst.class.php");
include_once("utility.class.php");

class ChangeLogAdmin
{
	public $actionPage = "../ViewChangeLog.php";
	
	public $m_dbConn;
	public $obj_utility;
	public $m_dbConnRoot;
	
	function __construct($dbConn,$dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->obj_utility = new utility($this->m_dbConn);
		
	}
	public function getloginName($LID)
	{
			$sqllogin="Select name from login where login_id='".$LID."'";
		    $res_display=$this->m_dbConnRoot->select($sqllogin);
			$changenme=$res_display[0]['name'];
			return $changenme;
			

	}
	
	public function ChangeLog()
	{
		
		$narray=array();
		
		$sqlexec="Select ChangedBy from change_log Group By ChangedBy";
		$result = $this->m_dbConn->select($sqlexec);
		for($i=0;$i<sizeof($result);$i++)
		{
			
				
				$changedky=$result[$i]['ChangedKey'];
			
			    $cnme=$this->getloginName($result[$i]['ChangedBy']);
				
			
			array_push($narray,array("cby"=>$cnme));
			
		}
		
		return $narray;
		
		

		
		
		
	}
	public function ChangeTable()
	{
		$ntb=array();
		$sqldis="Select ChangedTable from change_log Group By ChangedTable";
		$restb=$this->m_dbConn->select($sqldis);
		for($i=0;$i<sizeof($restb);$i++)
		{
			$changedtb=$restb[$i]['ChangedTable'];
			
			array_push($ntb,array("ctb"=>$changedtb));
			
		}
	
		return $ntb;
	}
	public function getLoginID($nme)
	{
		$sqllogin="Select login_id from login where name='".$nme."'";
		    $res_display=$this->m_dbConnRoot->select($sqllogin);
			$changeID=$res_display[0]['login_id'];
			return $changeID;
	}
	public function ChangeDis()
	{
		if($_REQUEST['insert']=='Submit' && $errorExists==0)
		{
			
			 $Narray=array();
			 $clby=$this->getLoginID($_POST['changelogby']);
			 $cltb=$_POST['changelogTN'];
			  $fromdate=$_POST['from_date'];
			  $todate=$_POST['to_date'];
			 
			if($clby<>'' && $cltb=='')
			{
				 $sqlDis="select ChangeTS,ChangedBy,ChangedTable,ChangedLogDec from change_log where ChangedBy='".$clby."'";
				 if($fromdate <> 0  && $todate <> 0)
				{
					  $sqlDis .= "  and ChangeTS BETWEEN '".getDBFormatDate($fromdate)."' AND '".getDBFormatDate($todate)."'";					
				}
				$result=$this->m_dbConn->select($sqlDis);
				for($i=0;$i<sizeof($result);$i++)
				{
					 $changetb=$result[$i]['ChangedTable'];
					 $changednme=$this->getloginName($result[$i]['ChangedBy']);
					 $changedDesc=$result[$i]['ChangedLogDec'];
					 $changedtime=$result[$i]['ChangeTS'];
					array_push($Narray,array("ctb"=>$changetb,"cnme"=>$changednme,"cdesc"=>$changedDesc,"cts"=>$changedtime));
				}
				
			}
			else if($cltb<>'' && $clby=='')
			{
				  $sqlDis="select ChangeTS,ChangedBy,ChangedTable,ChangedLogDec from change_log where ChangedTable='".$cltb."'";
				 if($fromdate <> 0  && $todate <> 0)
				{
					 $sqlDis .= "  and ChangeTS BETWEEN '".getDBFormatDate($fromdate)."' AND '".getDBFormatDate($todate)."'";					
				}
				$result=$this->m_dbConn->select($sqlDis);
				for($i=0;$i<sizeof($result);$i++)
				{
					 $changetb=$result[$i]['ChangedTable'];
					 $changednme=$this->getloginName($result[$i]['ChangedBy']);
					 $changedDesc=$result[$i]['ChangedLogDec'];
					 $changedtime=$result[$i]['ChangeTS'];
					array_push($Narray,array("ctb"=>$changetb,"cnme"=>$changednme,"cdesc"=>$changedDesc,"cts"=>$changedtime));
				}
				
			}
			else if($cltb=='' && $clby=='')
			{
				  $sqlDis="select ChangeTS,ChangedBy,ChangedTable,ChangedLogDec from change_log";
				 if($fromdate <> 0  && $todate <> 0)
				{
				  $sqlDis .= "  where ChangeTS BETWEEN '".getDBFormatDate($fromdate)."' AND '".getDBFormatDate($todate)."'";					
				}
				$result=$this->m_dbConn->select($sqlDis);
				for($i=0;$i<sizeof($result);$i++)
				{
					 $changetb=$result[$i]['ChangedTable'];
					 $changednme=$this->getloginName($result[$i]['ChangedBy']);
					 $changedDesc=$result[$i]['ChangedLogDec'];
					 $changedtime=$result[$i]['ChangeTS'];
					array_push($Narray,array("ctb"=>$changetb,"cnme"=>$changednme,"cdesc"=>$changedDesc,"cts"=>$changedtime));
				}
				
			}
			else if($cltb<>'' && $clby<>'')
			{
				 $sqlDis="select ChangeTS,ChangedBy,ChangedTable,ChangedLogDec from change_log where ChangedBy='".$clby."' and ChangedTable='".$cltb."'";
				 if($fromdate <> 0  && $todate <> 0)
				{
				   $sqlDis .= "  and ChangeTS BETWEEN '".getDBFormatDate($fromdate)."' AND '".getDBFormatDate($todate)."'";					
				}
				$result=$this->m_dbConn->select($sqlDis);
				for($i=0;$i<sizeof($result);$i++)
				{
					 $changetb=$result[$i]['ChangedTable'];
					 $changednme=$this->getloginName($result[$i]['ChangedBy']);
					 $changedDesc=$result[$i]['ChangedLogDec'];
					 $changedtime=$result[$i]['ChangeTS'];
					array_push($Narray,array("ctb"=>$changetb,"cnme"=>$changednme,"cdesc"=>$changedDesc,"cts"=>$changedtime));
				}
				
			}
			
			
			
		}
		
		return $Narray;
	}

}

?>
<?php
include_once ("dbconst.class.php"); 
//include_once("include/dbop.class.php");
include_once("latestcount.class.php");

include_once( "include/fetch_data.php");

include_once('../swift/swift_required.php');
include_once("../ImageManipulator.php");
include_once("utility.class.php");

class document_manager extends dbop
{
	public $m_dbConn;
	public $m_dbConnRoot;
	public $objFetchData;
	public $obj_Utility;
	public $obj_android;	
	

	function __construct($dbConn, $dbConnRoot, $SocietyID)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->m_dbConnRoot = $dbConnRoot;
		//dbop::__construct();
		
		$this->objFetchData = new FetchData($dbConn);
		if(isset($SocietyID) && $SocietyID <> "")
		{
			$this->objFetchData->GetSocietyDetails($SocietyID);
		}
		else
		{
			$this->objFetchData->GetSocietyDetails($_SESSION['society_id']);
		}
		
		$this->obj_Utility = new utility($dbConn, $dbConnRoot);
	
	}
	
	
	
	
	
	public function combobox($query,$id)
	{
	$str.="<option value=''>Please Select</option>";
	$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($v==$id)
						{
							$sel = "selected";
						}
						else
						{
							$sel = "";	
						}
						$str.="<OPTION VALUE=".$v.">";
					}
					else
					{
						$str.=$v."</OPTION>";
					}
					$i++;
				}
			}
		}
			return $str;
	}
	
	
	public function checkExitingMember($nomination_status,$member_id)
	{		
			
			//echo "member id".$member_id;
			if($nomination_status=='1')
			{
				 $sql="SELECT member_id FROM `nomination_form`where nomination_status='3' or nomination_status='2' and member_id='".$member_id."'";
				$result = $this->m_dbConn->select($sql);
				
					if($result[0]['member_id']!="")
					{
						if($result[0]['member_id']=$member_id)
							{
								$data='1';
							}}
						else
							{
								$data='0';
							}
					
			}
	
			else if($nomination_status=='2')
			{
			 $sql="select member_id from nomination_form where  nomination_status='3'  and member_id='".$member_id."' ";
			$result = $this->m_dbConn->select($sql);
			//print_r($result);
			
					if($result[0]['member_id']!="")
					{
						if($result[0]['member_id']=$member_id)
							{
								$data='2';
							}}
						else
							{
								$data='0';
							}
					
			}
	
			return $data;
	}

	
	
	public function getRecords($type)
	{	//$finalArray=array();
		 $sql1="select mm.unit,un.unit_no, mm.owner_name,nf.nomination_id,nf.member_id,nf.nomination_status,nf.meeting,nf.timestamp,me.Title from nomination_form nf,member_main mm,unit un,meeting me where mm.member_id=nf.member_id and un.unit_id=mm.unit  ";
		//$sql1="select mm.owner_name,nf.nomination_id,nf.member_id,nf.nomination_status from nomination_form nf,member_main mm  where mm.member_id=nf.member_id";
		//select mm.owner_name,nf.nomination_id,nf.member_id,nf.nomination_status from nomination_form nf,member_main mm where mm.member_id=nf.member_id AND nomination_status='1'

		if($type=="submitting")
		{
			$sql1 .=" AND nomination_status='1' group by nf.nomination_id";
		}
		else if($type=="submitted")
		{
			$sql1 .=" AND nomination_status='2'  group by nf.nomination_id";
		}
		else if($type=="approved")
		{
			$sql1 .=" AND nomination_status='3' group by nf.nomination_id ";
		}
			else if($type=="cancel")
		{
			$sql1 .=" AND nomination_status='4' group by nf.nomination_id ";
		}
		//echo $sql1;
		$result = $this->m_dbConn->select($sql1);

	

		$tempArray= array();
		
		//echo  sizeof($result);
		for($i=0;$i <sizeof($result);$i++)
		{
			$result[$i]['Nomineedetails'] = array();
			
			 $sql2="Select nominee_name,relation,percentage_share,is_minor from nomination_details where nomination_id ='".$result[$i]['nomination_id']."' ";
			 $result1 = $this->m_dbConn->select($sql2);
			 
			 for($j=0;$j <sizeof($result1);$j++)
			 {
	 			 array_push($result[$i]['Nomineedetails'],$result1[$j]);
			 }
				
		}
		return $result;
	}
	
	public function updateNomination($nomination_id,$check_id)
	{	
	
	
			
		$timestamp=getCurrentTimeStamp();
		$sql1="Select nomination_status from nomination_form WHERE `nomination_id`='".$nomination_id."'";
		$result1 = $this->m_dbConn->select($sql1);
		if($result1[0]["nomination_status"]=="1")
		{
				$sql="UPDATE `nomination_form` SET `nomination_status`='2', timestamp='".$timestamp['DateTime']."' WHERE `nomination_id`='".$nomination_id."'";
		}

		else if($result1[0]["nomination_status"]=="2")
		{
			 $sql="select nomination_id,member_id from nomination_form where  nomination_status='3'  and member_id='".$check_id."' ";
			$result = $this->m_dbConn->select($sql);
			if($result[0]['member_id']!="")
					{
						if($result[0]['member_id']=$check_id)
							{
								$cancel_nomination_id=$result[0]['nomination_id'];
								//echo $cancel_nomination_id;
									
					
					$sql=" UPDATE `nomination_form` SET `nomination_status`='4', timestamp='".$timestamp['DateTime']."', meeting='0' WHERE `nomination_id`='".$cancel_nomination_id."'";
					$result = $this->m_dbConn->update($sql);
					$sql="UPDATE `nomination_form` SET `nomination_status`='3', timestamp='".$timestamp['DateTime']."', meeting='0' WHERE `nomination_id`='".$nomination_id."' ";						
					$result = $this->m_dbConn->update($sql);
					
							}}
						else
							{
								$sql="UPDATE `nomination_form` SET `nomination_status`='3', timestamp='".$timestamp['DateTime']."', meeting='0' WHERE `nomination_id`='".$nomination_id."'";
							}
					

					
	       
	}
		$result = $this->m_dbConn->update($sql);
	
		if($result = $nomination_id)
		{
			$data1='success';
		}
		else
		{
			$data1='failed';
		}
		
		return $data1;
	}
	
	
}?>























<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
</body>
</html>
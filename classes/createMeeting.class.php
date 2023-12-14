 <?php
include_once("dbconst.class.php");
include_once("include/display_table.class.php");

class createMeeting extends dbop
{
	public $m_dbConn;
	public $actionPage = "../meeting.php";
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		//$this->display_pg=new display_table($this->m_dbConn);

		//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);

		//dbop::__construct();
	}
	public function getMeetingByMeetingId($mId)
	{
		$res=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=6&mId=$mId&dbName=".$_SESSION['dbname']);
		return $res;
	}
	public function getMeetingId()
	{
		$mId=$this->m_dbConn->select("select `Id` from `meeting` order by `Id` desc LIMIT 1");
		//echo "Id:"+$mId[0]['Id'];
		return $mId[0]['Id'];
	}
	public function getMeetingGroupId($mId)
	{
		$sql = "select GroupId from meeting where Id = '".$mId."'";
		$GroupId =  $this->m_dbConn->select($sql);
		return $GroupId[0]['GroupId'];
	}
	public function addMeeting()
	{
		//echo "In class..1";
		$cId=urldecode($_SESSION['login_id']);
		$title=urlencode($_POST['title']);
		$mDate=urlencode($_POST['mdate']);
		$hr=$_POST['hr'];
		$mn=$_POST['mn'];
		$ampm=$_POST['ampm'];
		$mTime=urlencode($hr.":".$mn.$ampm);
		$venue=urlencode($_POST['venue']);
		$notes=urlencode($_POST['notes']);
		$noOfAgenda=$_POST['maxrows'];
		$endText=urlencode($_POST['endText']);
		//echo "Date: "+$_POST['mdate'];
		//print_r($mDate);
		/*$agenda=array();
		for($i=0;$i<$noOfAgenda;$i++)
		{
			$agenda[$i]=$_POST['agenda'.$i];

		}
		
		*/
		$res=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=1&title=$title&type=1&mDate=$mDate&cId=$cId&mTime=$mTime&venue=$venue&mStatus=1&notes=$notes&eText=$endText&dbName=".$_SESSION['dbname']);
		//echo $res;
		return "Insert";
	}
	function getGroupMembers($gId)
    {
		$sql = "";
		$memId =  array();
		$memDetails = "";
		if($gId == "AO")
		{
			$sql = "Select m.member_id as MemberId, CONCAT(u.`unit_no`,'-',m.`primary_owner_name`,' (Owner)') as other_name from member_main as m, unit as u where m.ownership_status = '1' and m.`unit` = u.`unit_id`" ;
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "ACO")
		{
			$sql = "Select mof.`mem_other_family_id` as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Co-Owner)') as other_name from mem_other_family as mof,unit as u,member_main as m where mof.coowner = '2' and mof.`member_id` = m.`member_id` and u.`unit_id` = m.`unit`" ;
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "AOCO")
		{
			$sql = "Select m.member_id as MemberId, CONCAT(u.`unit_no`,'-',m.`primary_owner_name`,' (Owner)') as other_name from member_main as m, unit as u where m.ownership_status = '1' and m.`unit` = u.`unit_id` union 
			Select mof.`mem_other_family_id` as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Co-Owner)') as other_name from mem_other_family as mof,unit as u,member_main as m where mof.coowner = '2' and mof.`member_id` = m.`member_id` and u.`unit_id` = m.`unit`" ;
			//echo $sql;
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "AR")
		{
			$sql = "Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '1' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Co-Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '2' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Resident)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner` = '0' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('T-',t.`tmember_id`) as MemberId,CONCAT(u.`unit_no`,'-',t.`mem_name`,' (Tenant)') as other_name from tenant_module as tm,tenant_member as t,unit as u where tm.Status = 'Y' and tm.`unit_id` = u.`unit_id` and t.`tenant_id` = tm.`tenant_id`";
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "ART")
		{
			$sql = "Select mem_other_family_id as MemberId, other_name as other_name from mem_other_family where status = 'Y' union Select tmember_id as MemberId, mem_name as other_name from tenant_member where status = 'Y'" ;
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "ACM")
		{
			$sql = "Select C.member_id as MemberId, CONCAT(u.`unit_no`,'-',M.`other_name`) as other_name from mem_other_family as M, commitee as C,unit as u,member_main as mm where M.status = 'Y' and M.mem_other_family_id = C.member_id and mm.`member_id` = M.`member_id` and mm.`unit` = u.`unit_id`" ;
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "AT")
		{
			$sql = "Select tenant_id as MemberId, tenant_name as other_name from tenant_module where status = 'Y'";
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "AVO")
		{
			$sql = "SELECT mof.mem_other_family_id as MemberId, CONCAT(u.`unit_no`,'-',mof.other_name) as other_name FROM `mem_other_family` mof, `mem_car_parking` mcp,member_main m,unit u where mcp.member_id = mof.member_id and mcp.status ='Y' and m.`member_id` = mof.`member_id` and m.`unit` = u.`unit_id`" ;
			$memId = $this->m_dbConn->select($sql);
		}
		else if($gId == "ALH")
		{
			$sql = "Select L.member_id as MemberId, CONCAT(u.`unit_no`,'-',M.owner_name) as other_name from mortgage_details as L, member_main as M,unit as u where L.Status = 'Y' and L.LienStatus = 'Open' and M.member_id = L.member_id and u.`unit_id` = m.`unit`";
			$memId = $this->m_dbConn->select($sql);
		}
		else
		{
			$sql = "Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '1' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Co-Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '2' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Resident)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner` = '0' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('T-',t.`tmember_id`) as MemberId,CONCAT(u.`unit_no`,'-',t.`mem_name`,' (Tenant)') as other_name from tenant_module as tm,tenant_member as t,unit as u where tm.Status = 'Y' and tm.`unit_id` = u.`unit_id` and t.`tenant_id` = tm.`tenant_id`";
			$resultMem = $this->m_dbConn->select($sql);
			$sql2 = "Select MemberId from membergroup_members where GroupId = '".$gId."' and Status = 'Y'";
			$resultGMem =  $this->m_dbConn->select($sql2);
			$k=0;
			for($i=0;$i<sizeof($resultGMem);$i++)
			{
				for($j=0;$j<sizeof($resultMem);$j++)
				{
					if($resultGMem[$i]['MemberId'] == $resultMem[$j]['MemberId'])
					{
						$memId[$k]['MemberId'] = $resultMem[$j]['MemberId'];
						$memId[$k]['other_name'] = $resultMem[$j]['other_name'];
						$k=$k+1;
					}
				}
			}
		}
        if(sizeof($memId)>0)
        { 
            $memDetails="<ul><li>&nbsp;<input type='checkbox' id ='0' class='checkBox' name='mem_id[]' checked/>&nbsp;All</li>";
			for($i = 0; $i < sizeof($memId); $i++)
          	{ 
            	$memDetails .= "<li class='allMem'>&nbsp;<input type='checkbox' id='".$memId[$i]['MemberId']."' class='checkBox' onChange='uncheckDefaultCheckBox(this.id);' name='".$memId[$i]['MemberId']."'/> &nbsp; ".$memId[$i]['other_name']."</li>";
          	}
        	$memDetails .= "</ul>"; 
        }
		else
		{
			$memDetails="<ul><li>No Members found under selected category.</li></ul>";
		}
        return($memDetails);
      }
	  public function getMeetingAttendees($GroupId,$mId)
	  {
		  //echo "In CreateMeeting";
		  $sql = "";
		  $memId =  array();
		  $data = "";
		  //echo "GId:".$GroupId;
		  //echo "MId:".$mId;
		  if($GroupId == "AO")
		  {
				$sql = "Select ma.`MemberId`, CONCAT(u.`unit_no`,'-',mm.`primary_owner_name`) as other_name from meetingattendance ma,member_main mm,unit u where ma.`MeetingId` = '".$mId."' and mm.`member_id` = ma.`MemberId` and u.`unit_id` = mm.`unit`";
				$memId = $this->m_dbConn->select($sql);
		  }
		  else if($GroupId == "ACO")
		  {
				$sql = "Select ma.MemberId, CONCAT(u.`unit_no`,'-',mof.other_name) as other_name from meetingattendance ma, mem_other_family mof,member_main mm,unit u where ma.MeetingId = '".$mId."' and mof.mem_other_family_id = ma.MemberId and mm.`member_id` = mof.`member_id` and mm.`unit` = u.`unit_id`";
				//echo $sql;
				$memId = $this->m_dbConn->select($sql);
		  }
		  else if($GroupId == "AOCO")
		  {
				$sql = "Select ma.MemberId, CONCAT(u.`unit_no`,'-',mm.primary_owner_name) as other_name from meetingattendance ma,member_main mm,unit u where ma.MeetingId = '".$mId."' and mm.member_id = ma.MemberId and mm.`unit` = u.`unit_id`
				union 
				Select ma.MemberId, mof.other_name from meetingattendance ma, mem_other_family mof,member_main mm, unit u where ma.MeetingId = '".$mId."' and mof.mem_other_family_id = ma.MemberId and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`";
				$memId = $this->m_dbConn->select($sql);
		  }
		  else if($GroupId == "AR")
		  {
			  	//echo "In AR".$GroupId;
				$sql = "Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '1' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Co-Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '2' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Resident)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner` = '0' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('T-',t.`tmember_id`) as MemberId,CONCAT(u.`unit_no`,'-',t.`mem_name`,' (Tenant)') as other_name from tenant_module as tm,tenant_member as t,unit as u where tm.Status = 'Y' and tm.`unit_id` = u.`unit_id` and t.`tenant_id` = tm.`tenant_id`";
				//echo $sql;
				$resultMem = $this->m_dbConn->select($sql);
				//echo "<pre>";
				//print_r($resultMem);
				//echo "</pre>";
				$sql2 = "Select MemberId from meetingattendance where MeetingId = '".$mId."' and Status = 'Y'";
				//echo $sql2;
				$resultGMem =  $this->m_dbConn->select($sql2);
				//echo "<pre>";
				//print_r($resultMem);
				//echo "</pre>";
				$k=0;
				for($i=0;$i<sizeof($resultGMem);$i++)
				{
					for($j=0;$j<sizeof($resultMem);$j++)
					{
						if($resultGMem[$i]['MemberId'] == $resultMem[$j]['MemberId'])
						{
							$memId[$k]['MemberId'] = $resultMem[$j]['MemberId'];
							$memId[$k]['other_name'] = $resultMem[$j]['other_name'];
							$k=$k+1;
						}
					}
				}
				//echo "<pre>";
				//print_r($memId);
				//echo "</pre>";
		  }
		  else if($GroupId == "ART")
		  {
				$sql = "Select ma.MemberId, CONCAT(u.`unit_no`,'-',mof.other_name) as other_name from meetingattendance ma, mem_other_family mof,member_main mm,unit u where ma.MeetingId = '".$mId."' and mof.mem_other_family_id = ma.MemberId and mm.`member_id` = mof.`member_id` and mm.`unit` = u.`unit_id`
				union 
				Select ma.MemberId, CONCAT(u.`unit_no`,'-',mt.mem_name) as other_name from meetingattendance ma, tenant_member mt,tenant_module tm,unit u where ma.MeetingId = '".$mId."' and mt.tmember_id = ma.MemberId and mt.`tenant_id` = tm.`tenant_id` and tm.`unit_id` = u.`unit_id`";
				$memId = $this->m_dbConn->select($sql);
		  }
		  else if($GroupId == "ACM")
		  {
				$sql = "Select ma.MemberId, CONCAT(u.`unit_no`,'-',mof.other_name) as other_name from meetingattendance ma, mem_other_family mof,member_main mm,unit u where ma.MeetingId = '".$mId."' and mof.mem_other_family_id = ma.MemberId and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`";
				$memId = $this->m_dbConn->select($sql);
		  }
		  else if($GroupId == "AT")
		  {
				$sql = "Select ma.MemberId, CONCAT(u.`unit_no`,'-',tm.tenant_name) as other_name from meetingattendance ma, tenant_module tm,unit u where ma.MeetingId = '".$mId."' and ma.MemberId = tm.tenant_id and tm.status = 'Y' and tm.`unit_id` = u.`unit_id`";
				$memId = $this->m_dbConn->select($sql);
   		  }
		  else if($GroupId == "AVO")
		  {
				$sql = "Select ma.MemberId, CONCAT(u.`unit_no`,'-',mof.other_name) as other_name from meetingattendance ma, mem_other_family mof,member_main mm,unit u where ma.MeetingId = '".$mId."' and mof.mem_other_family_id = ma.MemberId and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`";
				//echo $sql;
				$memId = $this->m_dbConn->select($sql);
		  }
		  else if($GroupId == "ALH")
		  {
				$sql = "Select ma.MemberId,CONCAT(u.`unit_no`,'-',mm.owner_name) as other_name from meetingattendance ma, mortgage_details as L, member_main as mm,unit as u where ma.MeetingId = '".$mId."' and and mm.member_id = L.member_id and mm.member_id = ma.MemberId and mm.`unit`=u.`unit_id`";
				$memId = $this->m_dbConn->select($sql);
		  }
		  else
		  {
		  		$sql = "Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '1' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Co-Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '2' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Resident)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner` = '0' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('T-',t.`tmember_id`) as MemberId,CONCAT(u.`unit_no`,'-',t.`mem_name`,' (Tenant)') as other_name from tenant_module as tm,tenant_member as t,unit as u where tm.Status = 'Y' and tm.`unit_id` = u.`unit_id` and t.`tenant_id` = tm.`tenant_id`";
				$resultMem = $this->m_dbConn->select($sql);
				$sql2 = "Select MemberId from meetingattendance where MeetingId = '".$mId."' and Status = 'Y'";
				$resultGMem =  $this->m_dbConn->select($sql2);
				$k=0;
				for($i=0;$i<sizeof($resultGMem);$i++)
				{
					for($j=0;$j<sizeof($resultMem);$j++)
					{
						if($resultGMem[$i]['MemberId'] == $resultMem[$j]['MemberId'])
						{
							$memId[$k]['MemberId'] = $resultMem[$j]['MemberId'];
							$memId[$k]['other_name'] = $resultMem[$j]['other_name'];
							$k=$k+1;
						}
					}
				}
			}
			
			return $memId;
	  }
	  public function getMembersMarkPresent($GroupId,$mId)
	  {
		  //echo "In CreateMeeting";
		  $sql = "";
		  $memId =  array();
		  $data = "";
		  //echo "GId:".$GroupId;
		  //echo "MId:".$mId;
		  if($GroupId == "AO")
		  {
				$sql = "Select ma.MemberId, CONCAT(u.`unit_no`,'-',mm.primary_owner_name) as other_name from meetingattendance ma,member_main mm, unit u where ma.MeetingId = '".$mId."' and mm.member_id = ma.MemberId and ma.Attendance = 'P' and ma.Status = 'Y' and mm.`unit` = u.`unit_id`";
				$memId = $this->m_dbConn->select($sql);
		  }
		  else if($GroupId == "ACO")
		  {
				$sql = "Select ma.MemberId, CONCAT(u.`unit_no`,'-',mof.other_name) as other_name from meetingattendance ma, mem_other_family mof,member_main mm,unit u where ma.MeetingId = '".$mId."' and mof.mem_other_family_id = ma.MemberId and ma.Attendance = 'P' and ma.Status = 'Y' and mm.`member_id` = mof.`member_id` and mm.`unit` = u.`unit_id`";
				$memId = $this->m_dbConn->select($sql);
		  }
		  else if($GroupId == "AOCO")
		  {
				$sql = "Select ma.MemberId, CONCAT(u.`unit_no`,'-',mm.primary_owner_name) as other_name from meetingattendance ma,member_main mm,unit u where ma.MeetingId = '".$mId."' and mm.member_id = ma.MemberId and mm.`unit` = u.`unit_id` union Select ma.MemberId, CONCAT(u.`unit_no`,'-',mof.other_name) as other_name from meetingattendance ma, mem_other_family mof,member_main mm, unit u where ma.MeetingId = '".$mId."' and mof.mem_other_family_id = ma.MemberId and ma.Attendance = 'P' and ma.Status = 'Y' and mof.`member_id`=mm.`member_id` and mm.`unit`=u.`unit_id`";
				$memId = $this->m_dbConn->select($sql);
		  }
		  else if($GroupId == "AR")
		  {
			  	//echo "In AR".$GroupId;
				$sql = "Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '1' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Co-Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '2' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Resident)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner` = '0' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('T-',t.`tmember_id`) as MemberId,CONCAT(u.`unit_no`,'-',t.`mem_name`,' (Tenant)') as other_name from tenant_module as tm,tenant_member as t,unit as u where tm.Status = 'Y' and tm.`unit_id` = u.`unit_id` and t.`tenant_id` = tm.`tenant_id`";
				//echo $sql;
				$resultMem = $this->m_dbConn->select($sql);
				//echo "<pre>";
				//print_r($resultMem);
				//echo "</pre>";
				$sql2 = "Select MemberId from meetingattendance where MeetingId = '".$mId."' and Status = 'Y' and Attendance = 'P' and Status = 'Y'";
				//echo $sql2;
				$resultGMem =  $this->m_dbConn->select($sql2);
				//echo "<pre>";
				//print_r($resultMem);
				//echo "</pre>";
				$k=0;
				for($i=0;$i<sizeof($resultGMem);$i++)
				{
					for($j=0;$j<sizeof($resultMem);$j++)
					{
						if($resultGMem[$i]['MemberId'] == $resultMem[$j]['MemberId'])
						{
							$memId[$k]['MemberId'] = $resultMem[$j]['MemberId'];
							$memId[$k]['other_name'] = $resultMem[$j]['other_name'];
							$k=$k+1;
						}
					}
				}
				//echo "<pre>";
				//print_r($memId);
				//echo "</pre>";
		  }
		  else if($GroupId == "ART")
		  {
				$sql = "Select ma.MemberId, CONCAT(u.`unit_no`,'-',mof.other_name) as other_name from meetingattendance ma, mem_other_family mof, member_main mm, unit u where ma.MeetingId = '".$mId."' and mof.mem_other_family_id = ma.MemberId and mm.`member_id` = mof.`member_id` and mm.`unit` = u.`unit_id` union Select ma.MemberId, CONCAT(u.`unit_no`,'-',mt.mem_name) as other_name from meetingattendance ma, tenant_member mt,tenant_module tm, unit u where ma.MeetingId = '".$mId."' and mt.tmember_id = ma.MemberId and ma.Attendance = 'P' and ma.Status = 'Y' and tm.`tenant_id`=t=mt.`tenant_id` and tm.`unit_id` = u.`unit_id`";
				$memId = $this->m_dbConn->select($sql);
		  }
		  else if($GroupId == "ACM")
		  {
				$sql = "Select ma.MemberId, CONCAT(u.`unit_no`,'-',mof.other_name) as other_name from meetingattendance ma, mem_other_family mof, member_main mm, unit u where ma.MeetingId = '".$mId."' and mof.mem_other_family_id = ma.MemberId and ma.Attendance = 'P' and ma.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`";
				$memId = $this->m_dbConn->select($sql);
		  }
		  else if($GroupId == "AT")
		  {
				$sql = "Select ma.MemberId, CONCAT(u.`unit_no`,'-',tm.tenant_name) as other_name from meetingattendance ma, tenant_module tm ,unit uwhere ma.MeetingId = '".$mId."' and ma.MemberId = tm.tenant_id and tm.status = 'Y' and ma.Attendance = 'P' and ma.Status = 'Y' and tm.`unit_id` = u.`unit_id`";
				$memId = $this->m_dbConn->select($sql);
   		  }
		  else if($GroupId == "AVO")
		  {
				$sql = "Select ma.MemberId, CONCAT(u.`unit_no`,'-',mof.other_name) as other_name from meetingattendance ma, mem_other_family mof,member_main mm,unit u where ma.MeetingId = '".$mId."' and mof.mem_other_family_id = ma.MemberId and ma.Attendance = 'P' and ma.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit`=u.`unit_id`";
				//echo $sql;
				$memId = $this->m_dbConn->select($sql);
		  }
		  else if($GroupId == "ALH")
		  {
				$sql = "Select ma.MemberId, CONCAT(u.`unit_no`,'-',mm.owner_name) as other_name from meetingattendance ma, mortgage_details as L, member_main as mm,unit as u where ma.MeetingId = '".$mId."' and mm.`unit` = u.`unit_id` and mm.member_id = L.member_id and mm.member_id = ma.MemberId and ma.Attendance = 'P' and ma.Status = 'Y'";
				$memId = $this->m_dbConn->select($sql);
		  }
		  else
		  {
		  		$sql = "Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '1' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Co-Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '2' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Resident)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner` = '0' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('T-',t.`tmember_id`) as MemberId,CONCAT(u.`unit_no`,'-',t.`mem_name`,' (Tenant)') as other_name from tenant_module as tm,tenant_member as t,unit as u where tm.Status = 'Y' and tm.`unit_id` = u.`unit_id` and t.`tenant_id` = tm.`tenant_id`";
				$resultMem = $this->m_dbConn->select($sql);
				$sql2 = "Select MemberId from meetingattendance where MeetingId = '".$mId."' and Status = 'Y' and Attendance = 'P' and Status = 'Y'";
				$resultGMem =  $this->m_dbConn->select($sql2);
				$k=0;
				for($i=0;$i<sizeof($resultGMem);$i++)
				{
					for($j=0;$j<sizeof($resultMem);$j++)
					{
						if($resultGMem[$i]['MemberId'] == $resultMem[$j]['MemberId'])
						{
							$memId[$k]['MemberId'] = $resultMem[$j]['MemberId'];
							$memId[$k]['other_name'] = $resultMem[$j]['other_name'];
							$k=$k+1;
						}
					}
				}
			}
			
			return $memId;
	  }
	  function comboboxForMemberSelection($query,$id, $defaultText = 'Please Select', $defaultValue = '')
          {
          	if($defaultText != '')
          	{ 
            	?>
            	<ul>
            		<li>&nbsp;<input type="checkbox" id ='0' class="checkBox" name="mem_id[]" checked/>&nbsp;<?php echo $defaultText ; ?></li>
	  	<?php 
          	}
          	$data = $this->m_dbConn->select($query);
          	//echo $data;
          	for($i = 0; $i < sizeof($data); $i++)
          	{ 
            	?>
            		<li>&nbsp;<input type="checkbox" id="<?php echo $data[$i]['MemberId']; ?>" class="checkBox" onChange="uncheckDefaultCheckBox(this.id);" name="<?php echo $data[$i]['MemberId']; ?>"/> &nbsp; <?php echo $data[$i]['other_name'];?></li>
            	<?php   
          	}
            ?>
          </ul> 
          <?php
	  }
	  public function SelectgrpName()
	 {
		$res= file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/Servlet1?mode=4&dbName=".$_SESSION['dbname']);
		//print_r ($res);
		
		//$grpName=$_POST['grpName'];
		//$res= file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/Servlet1?mode=5&name=$grpName");
		//echo "<br>data".$res;
		$jRes = json_decode($res,true);
		$cRes=$jRes;
		foreach($cRes as $key => $value)
		{
			foreach($value as $k => $v)
			{
				unset($cRes[$key]['Description']);
				unset($cRes[$key]['Status']);
				unset($cRes[$key]['TimeStamp']);
			}
		}
		return $cRes;
	}
	public function getGroupId($gname)
	{
		$name=urlencode($gname);
		$res=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/Servlet1?mode=5&name=$name&dbName=".$_SESSION['dbname']);
		$jRes = json_decode($res,true);
		$gId=$jRes['Id'];
		return $gId;	
	}
	public function getHead()
	{
		$socRes=$this->m_dbConn->select("SELECT * from `society` where society_id='".$_SESSION['society_id']."';");
		$content="<table style='text-align:center;width:100%'>
					<tr>
						<td>
							<b>".$socRes[0]['society_name']."
						</td>
					</tr>
					<tr>
						<td>
							<b>".$socRes[0]['society_add']."
						</td>
					</tr>
					<tr>
						<td>
							<b>".$socRes[0]['registration_no']."</b>
						</td>
					</tr>
				</table>";
		return $content;
	}
	public function comboboxForSelect($query,$id)
	{
		$str.="<option value='0'>Please Select</option>";
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
	public function selectComboboxForTemplate()
	{
		$res=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/ServletMeeting?mode=14&dbName=".$_SESSION['dbname']);
		/*echo "<pre>";
		print_r($res);
		echo "</pre>";*/
		$jRes=json_decode($res,true);
		return $jRes;
	}
}
?>	
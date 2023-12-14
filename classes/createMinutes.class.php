<?php
include_once("defaults.class.php");
include_once("utility.class.php");
include_once("/includes/dbop.class.php");
set_time_limit(0);
class createMinutes extends dbop
{
	private $m_dbConn;
	function __construct($dbConn)
	{
			//echo "ctor";
		$this->m_dbConn = $dbConn;
	}
 	function getSocietyName($sId)
 	{
		$socName=$this->m_dbConn->select("SELECT `society_name` FROM `society` WHERE `society_id`='".$sId."'");
		return $socName[0]['society_name'];
  	}
  	function textDecore($meetingRes, $agendaRes, $tempRes)
	{
		$socId=$_SESSION['society_id'];
		//echo "id:".$socId;
		$socName=$this->getSocietyName($socId);
		//echo "soc Name:".$socName;
		//echo "title:".$meetingRes['Title'];
		$heading="<table width='100%'><tr><td><b>".$socName." Minutes of ".$meetingRes['Title']." held on ".$meetingRes['MeetingDate']."";
		$note="<br>".$TempRes['Note'];
		$len=sizeof($agendaRes);
		$agendaContent="<br>Follwing are the agenda of meeting and resolution:<br>";
		for($i=0;$i<$len;$i++)
		{
			$agendaContent.=($i+1).")"." ".$agendaRes[$i]['Question'].".<br>".$agendaRes[$i]['Minutes']."<br>".$agendaRes[$i]['Resolution']."<br><br> Proposed by ".$agendaRes[$i]['ProposedBy']."<br>Seconded by ".$agendaRes[$i]['SecondedBy']."<br>Passed ".$agendaRes[$i]['PassedBy']."<br><br>";
		}
		$footer="<br>".$tempRes['EndNote']."<br><br>"." For ".$socName."";
		$finalData=$heading.$note.$agendaContent.$footer;
		return $finalData;
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
	public function getProposedByName($id,$gId)
	{
		$sqlP = "";
		$resP = array();
		if($gId == "AO")
		{	
			$sqlP = "select primary_owner_name as other_name from member_main where member_id = '".$id."'";
			$resP = $this->m_dbConn->select($sqlP);
		}
		else if($gId == "ACO")
		{
			$sqlP = "select `other_name` from mem_other_family where mem_other_family_id = ".$id;
			$resP = $this->m_dbConn->select($sqlP);
		}
		else if($gId == "AOCO")
		{
			$sqlP = "select member_id as mem_other_family_id,primary_owner_name as other_name from member_main union select mem_other_family_id,other_name from mem_other_family";
			$res = $this->m_dbConn->select($sqlP);
			for($i=0;$i<sizeof($res);$i++)
			{
				if($res[$i]['mem_other_family_id'] === $id)
				{
					$resP[0]['other_name'] = $res[$i]['other_name'];				
				}
			}
		}
		else if($gId == "AT")
		{
			$sqlP = "select tenant_name as other_name from tenant_module where tenant_id = ".$id;
			$resP = $this->m_dbConn->select($sqlP);
		}
		else if ($gId == "AR")
		{
			//echo "In AR";;
			$sqlP = "Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(mof.`other_name`,' (Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('T-',t.`tmember_id`) MemberId,CONCAT(t.`mem_name`,' (Tenant)') as other_name from tenant_module as tm,tenant_member as t,unit as u where tm.Status = 'Y' and tm.`unit_id` = u.`unit_id` and t.`tenant_id` = tm.`tenant_id`";
			//echo $sqlP;
			$resultMem = $this->m_dbConn->select($sqlP);
			for($j=0;$j<sizeof($resultMem);$j++)
			{
				if($id == $resultMem[$j]['MemberId'])
				{
					$resP[0]['other_name'] = $resultMem[$j]['other_name'];
				}
			}
		}
		else
		{
			$sqlP = "Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(mof.`other_name`,' (Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('T-',t.`tmember_id`) MemberId,CONCAT(t.`mem_name`,' (Tenant)') as other_name from tenant_module as tm,tenant_member as t,unit as u where tm.Status = 'Y' and tm.`unit_id` = u.`unit_id` and t.`tenant_id` = tm.`tenant_id`";
			//echo $sql;
			$resultMem = $this->m_dbConn->select($sqlP);
			for($j=0;$j<sizeof($resultMem);$j++)
			{
				if($id == $resultMem[$j]['MemberId'])
				{
					$resP[0]['other_name'] = $resultMem[$j]['other_name'];
				}
			}
		}
		return $resP;
	}
	public function getSecondedByName($id,$gId)
	{
		$sqlP = "";
		$resP = array();
		if($gId == "AO")
		{	
			$sqlP = "select primary_owner_name as other_name from member_main where member_id = '".$id."'";
			$resP = $this->m_dbConn->select($sqlP);
		}
		else if($gId == "ACO")
		{
			$sqlP = "select `other_name` from mem_other_family where mem_other_family_id = ".$id;
			$resP = $this->m_dbConn->select($sqlP);
		}
		else if($gId == "AOCO")
		{
			$sqlP = "select member_id as mem_other_family_id,primary_owner_name as other_name from member_main union select mem_other_family_id,other_name from mem_other_family";
			$res = $this->m_dbConn->select($sqlP);
			for($i=0;$i<sizeof($res);$i++)
			{
				if($res[$i]['mem_other_family_id'] === $id)
				{
					$resP[0]['other_name'] = $res[$i]['other_name'];				
				}
			}
		}
		else if($gId == "AT")
		{
			$sqlP = "select tenant_name as other_name from tenant_module where tenant_id = ".$id;
			$resP = $this->m_dbConn->select($sqlP);
		}
		else if($gId == "ART")
		{
			$sqlP = "Select mem_other_family_id as MemberId, other_name as other_name from mem_other_family where status = 'Y' union Select tenant_id as MemberId, tenant_name as other_name from tenant_module where status = 'Y'";
			$res = $this->m_dbConn->select($sqlP);
			for($i=0;$i<sizeof($res);$i++)
			{
				if($res[$i]['MemberId'] === $id)
				{
					$resP[0]['other_name'] = $res[$i]['other_name'];				
				}
			}
		}
		else if ($gId == "AR")
		{
			$sqlP = "Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(mof.`other_name`,' (Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('T-',t.`tmember_id`) MemberId,CONCAT(t.`mem_name`,' (Tenant)') as other_name from tenant_module as tm,tenant_member as t,unit as u where tm.Status = 'Y' and tm.`unit_id` = u.`unit_id` and t.`tenant_id` = tm.`tenant_id`";
			//echo $sql;
			$resultMem = $this->m_dbConn->select($sqlP);
			for($j=0;$j<sizeof($resultMem);$j++)
			{
				if($id == $resultMem[$j]['MemberId'])
				{
					$resP[0]['other_name'] = $resultMem[$j]['other_name'];
				}
			}
		}
		else
		{
			$sqlP = "Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(mof.`other_name`,' (Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('T-',t.`tmember_id`) MemberId,CONCAT(t.`mem_name`,' (Tenant)') as other_name from tenant_module as tm,tenant_member as t,unit as u where tm.Status = 'Y' and tm.`unit_id` = u.`unit_id` and t.`tenant_id` = tm.`tenant_id`";
			//echo $sql;
			$resultMem = $this->m_dbConn->select($sqlP);
			for($j=0;$j<sizeof($resultMem);$j++)
			{
				if($id == $resultMem[$j]['MemberId'])
				{
					$resP[0]['other_name'] = $resultMem[$j]['other_name'];
				}
			}
		}
		return $resP;
	}
	public function getChairmanName()
	{
		$sql = "SELECT M.other_name FROM `commitee` as C, mem_other_family as M where C.member_id  =  M.mem_other_family_id and C.position = 'Chairman'";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	public function deleteAttendance($mId)
	{
		$sql = "Update meetingattendance set Attendance = 'A' where MeetingId = ".$mId;
		$res = $this->m_dbConn->update($sql);
		return $res;
	}
}
?>

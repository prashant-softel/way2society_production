<?php if(!isset($_SESSION)){ session_start(); }
	include_once("include/dbop.class.php");
	include_once("document_maker.class.php");
	include_once("servicerequest.class.php");
	include_once("dbconst.class.php");
class api_utility 
{
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_documentMaker;
	public $obj_serviceRequest;
	function __construct($societyId,$unitId,$role)
	{
		$this->m_dbConnRoot = new dbop(true);
		$societyDetails = $this->getSocietyDetails($societyId);
		//var_dump($societyDetails);
		$this->m_dbConn = new dbop(false, $societyDetails[0]['dbname']);
		$this->setSessionValue($societyDetails,$unitId,$role);
		$this->obj_documentMaker = new doc_templates($this->m_dbConn,$this->m_dbConnRoot,$societyId);
		$this->obj_serviceRequest = new servicerequest($this->m_dbConn);
	}
	public function getSocietyDetails($societyId)
	{
		$sql = "Select * from society where society_id = '".$societyId."'";
		$sql_res = $this->m_dbConnRoot->select($sql);
		return ($sql_res);
		//var_dump($sql_res);
		//$this->m_dbConn = new dbop(false,$sql_res[])
	}
	public function setSessionValue($details,$unitId,$role)
	{
		$_SESSION['society_id'] = $details[0]['society_id'];
		$_SESSION['society_name'] = $details[0]['society_name'];
		$_SESSION['client_id'] = $details[0]['client_id'];
		$_SESSION['unit_id'] = $unitId;
		$_SESSION['role'] = $role;
	}
	public function getDBConnectionObject()
	{
		$data[0] = $this->m_dbConn;
		$data[1] = $this->m_dbConnRoot;
		return($data);
	}
	public function getRenovationRequest($type, $loginId, $societyId)
	{
		//echo "in getRenovationRequest";
		$approvalLevel = $this->getApprovalLevel("2");
		if($type == "pending")
		{
			$verificationAccess = $this->checkVerificationAccess($loginId,$societyId,$_SESSION['role'],"2");
			$sql1 = "select rr.*,rr.`Id` as RenovationId,sr.*,u.`unit_no`,mm.`primary_owner_name`,mm.`member_id`,u.`unit_id`, ad.* from renovation_details as rr, service_request as sr, unit as u, member_main as mm, approval_details as ad where ad.`verifiedStatus` = 'N' and rr.`status` = 'Y' and u.`unit_id` = rr.`unit_id` and mm.`unit`= u.`unit_id` and rr.`status` = 'Y' and sr.`request_id` = rr.`request_id` and ad.`referenceId` = rr.`Id` and ad.`module_id` ='".RENOVATION_SOURCE_TABLE_ID."' and mm.`ownership_status`= '1' ORDER BY rr.`Id` asc";
			$sql1_res = $this->m_dbConn->select($sql1);
			//var_dump($sql1_res);
			for($i = 0 ; $i < sizeof($sql1_res);$i++)
			{
				$sql1_res[$i]['verificationAccess'] = $verificationAccess;
			}
		}
		if($type == "verified")
		{
			if($approvalLevel == "1")
			{
				$sql1 = "select rr.*,rr.`Id` as RenovationId,sr.*,u.`unit_no`,mm.`primary_owner_name`,mm.`member_id`,u.`unit_id`,ad.* from renovation_details as rr, service_request as sr, unit as u, member_main as mm, approval_details as ad where ad.`verifiedStatus` = 'Y' and ad.`firstLevelApprovalStatus` = 'N' and rr.`status` = 'Y' and u.`unit_id` = rr.`unit_id` and mm.`unit`= u.`unit_id` and rr.`status` = 'Y' and sr.`request_id` = rr.`request_id` and ad.`referenceId` = rr.`Id` and ad.`module_id` = '".RENOVATION_SOURCE_TABLE_ID."' and mm.`ownership_status`= '1' ORDER BY rr.`Id` asc";
			
			}
			else
			{
				$sql1 = "select rr.*,rr.`Id` as RenovationId,sr.*,u.`unit_no`,mm.`primary_owner_name`,mm.`member_id`,u.`unit_id`,ad.* from renovation_details as rr, service_request as sr, unit as u, member_main as mm, approval_details as ad where ad.`verifiedStatus` = 'Y' and ad.`secondLevelApprovalStatus` = 'N' and rr.`status` = 'Y' and u.`unit_id` = rr.`unit_id` and mm.`unit`= u.`unit_id` and rr.`status` = 'Y' and sr.`request_id` = rr.`request_id` and ad.`referenceId` = rr.`Id` and ad.`module_id` = '".RENOVATION_SOURCE_TABLE_ID."' and mm.`ownership_status`= '1' ORDER BY rr.`Id` asc";
			}
			$sql1_res = $this->m_dbConn->select($sql1);
			$result = $this->checkApprovalAccess($loginId,$societyId,"2");
			for($i = 0 ; $i < sizeof($sql1_res);$i++)
			{
				//echo "in for";
				$sql2 = "Select `name` from login where login_id = '".$sql1_res[$i]['verifiedById']."';";
				$sql2_res = $this->m_dbConnRoot->select($sql2);
				$sql1_res[$i]['verifiedByName'] = $sql2_res[0]['name'];
				if($sql1_res[$i]['firstLevelApprovalStatus'] == 'Y')
				{	
					//echo "in if1";
					$sql3 = "Select `name` from login where login_id = '".$sql1_res[$i]['firstApprovalById']."';";
					$sql3_res = $this->m_dbConnRoot->select($sql3);
					$sql1_res[$i]['firstApprovedByName'] = $sql2_res[0]['name'];
					//var_dump();
					if($approvalLevel == "2")
					{
						//echo "in if2";
						if($sql1_res[$i]['firstApprovalById'] == $loginId)
						{
							$sql1_res[$i]['approvalAccess'] = false;
						}
						else
						{
							$sql1_res[$i]['approvalAccess'] = $result;
						}
					}
					else
					{
						$sql1_res[$i]['approvalAccess'] = $result;
					}
				}
				else
				{
					$sql1_res[$i]['approvalAccess'] = $result;
				}
			}
		}
		if( $type == "approved")
		{
			if($approvalLevel == "1")
			{
				$sql1 = "select rr.*,rr.`Id` as RenovationId,sr.*,u.`unit_no`,mm.`primary_owner_name`,mm.`member_id`,u.`unit_id`,ad.* from renovation_details as rr, service_request as sr, unit as u, member_main as mm, approval_details as ad where ad.`verifiedStatus` = 'Y' and ad.`firstLevelApprovalStatus` = 'Y' and rr.`status` = 'Y' and u.`unit_id` = rr.`unit_id` and mm.`unit`= u.`unit_id` and rr.`status` = 'Y' and sr.`request_id` = rr.`request_id` and ad.`referenceId` = rr.`Id` and ad.`module_id` = '".RENOVATION_SOURCE_TABLE_ID."' and mm.`ownership_status`= '1' ORDER BY rr.`Id` asc";
			}
			else
			{
				$sql1 = "select rr.*,rr.`Id` as RenovationId,sr.*,u.`unit_no`,mm.`primary_owner_name`,mm.`member_id`,u.`unit_id`,ad.* from renovation_details as rr, service_request as sr, unit as u, member_main as mm, approval_details as ad where ad.`verifiedStatus` = 'Y' and ad.`firstLevelApprovalStatus` = 'Y' and ad.`secondLevelApprovalStatus` = 'Y' and rr.`status` = 'Y' and u.`unit_id` = rr.`unit_id` and mm.`unit`= u.`unit_id` and rr.`status` = 'Y' and sr.`request_id` = rr.`request_id` and ad.`referenceId` = rr.`Id` and ad.`module_id` = '".RENOVATION_SOURCE_TABLE_ID."' and mm.`ownership_status`= '1' ORDER BY rr.`Id` asc";
			}
			$sql1_res = $this->m_dbConn->select($sql1);
			for($i = 0 ; $i < sizeof($sql1_res);$i++)
			{
				$sql2 = "Select `name` from login where login_id = '".$sql1_res[$i]['verifiedById']."';";
				$sql2_res = $this->m_dbConnRoot->select($sql2);
				$sql3 = "Select `name` from login where login_id = '".$sql1_res[$i]['firstApprovalById']."';";
				$sql3_res = $this->m_dbConnRoot->select($sql3);
				$sql4 = "Select `name` from login where login_id = '".$sql1_res[$i]['secondApprovalById']."';";
				$sql4_res = $this->m_dbConnRoot->select($sql4);
				$sql1_res[$i]['verifiedByName'] = $sql2_res[0]['name'];
				$sql1_res[$i]['firstApprovedByName'] = $sql3_res[0]['name'];
				$sql1_res[$i]['secondApprovedByName'] = $sql4_res[0]['name'];
				//$sql1_res[$i]['start_date'] = getDisplayFormatDate($sql1_res[$i]['start_date']);
				//$sql1_res[$i]['end_date'] = getDisplayFormatDate($sql1_res[$i]['end_date']);
			}
		}
		for($i = 0 ; $i < sizeof($sql1_res);$i++)
		{
			$sql1_res[$i]['application_date'] = getDisplayFormatDate($sql1_res[$i]['application_date']);
			$sql1_res[$i]['start_date'] = getDisplayFormatDate($sql1_res[$i]['start_date']);
			$sql1_res[$i]['end_date'] = getDisplayFormatDate($sql1_res[$i]['end_date']);
			$sql1_res[$i]['work_details'] = strip_tags(html_entity_decode($sql1_res[$i]['work_details'])).trim(); 
			if($sql1_res[$i]['location'] == "1")
			{
				$sql1_res[$i]['location'] = "Inside";
			}
			else
			{
				$sql1_res[$i]['location'] = "Outside";
			}
			//echo "Labourer Name :".$sql1_res[$i]['labourer_name'];
			$labourerNameRes = $sql1_res[$i]['labourer_name'];
			//echo "labourerNameRes : ".$labourerNameRes;
			$labourerNameFinal = array();
			if($labourerNameRes != "")
			{
				$labourerName = explode(",",$labourerNameRes);
				//var_dump($labourerName);
				for($j = 0;$j < sizeof($labourerName); $j++)
				{
					$labourerNameFinal[$j]['index'] = $j + 1;
					$labourerNameFinal[$j]['labourerName'] = $labourerName[$j];
				}
			}
			else
			{
				$labourerNameFinal[0]['index'] = 0;
				$labourerNameFinal[0]['labourerName'] = "Not mentioned";
			}
			$sql1_res[$i]['labourer_name'] = $labourerNameFinal;
		}
		//var_dump($sql1_res);
		return $sql1_res;
	}
	public function getAddressProofRequests($type,$loginId,$societyId)
	{
		$approvalLevel = $this->getApprovalLevel("4");
		//echo $approvalLevel;
		if($type == "pending")
		{
			$verificationAccess = $this->checkVerificationAccess($loginId,$societyId,$_SESSION['role'],"4");
			$sql1 = "select ap.*,ap.`id` as addressProofId,sr.*,u.`unit_no`,mm.`primary_owner_name`,mm.`member_id`,mof.`other_name`,mof.`relation`,u.`unit_id`, ad.* from addressproof_noc as ap, service_request as sr, unit as u, member_main as mm, approval_details as ad, mem_other_family as mof where ad.`verifiedStatus` = 'N' and mm.`unit`= u.`unit_id` and ap.`status` = 'Y' and sr.`request_id` = ap.`service_request_id` and ad.`referenceId` = ap.`id` and ad.`module_id` = '".ADDRESSPROOF_SOURCE_TABLE_ID."' and mof.`mem_other_family_id` = ap.`mem_other_family_id` and u.`unit_id` = ap.`unit_id` and mm.`ownership_status` = '1'";
			$sql1_res = $this->m_dbConn->select($sql1);
			for($i = 0 ; $i < sizeof($sql1_res);$i++)
			{
				$sql1_res[$i]['verificationAccess'] = $verificationAccess;
			}
		}
		if($type == "verified")
		{
			if($approvalLevel == "1")
			{
				$sql1 = "select  ap.*,ap.`id` as addressProofId,sr.*,u.`unit_no`,mm.`primary_owner_name`,mm.`member_id`,mof.`other_name`,mof.`relation`,u.`unit_id`, ad.* from addressproof_noc as ap, service_request as sr, unit as u, member_main as mm, approval_details as ad, mem_other_family as mof where ad.`verifiedStatus` = 'Y' and ad.`firstLevelApprovalStatus` = 'N' and mm.`unit`= u.`unit_id` and ap.`status` = 'Y' and sr.`request_id` = ap.`service_request_id` and ad.`referenceId` = ap.`id` and ad.`module_id` = '".ADDRESSPROOF_SOURCE_TABLE_ID."' and mof.`mem_other_family_id` = ap.`mem_other_family_id` and u.`unit_id` = ap.`unit_id` and mm.`ownership_status` = '1'";
			}
			else
			{
				$sql1 = "select ap.*,ap.`id` as addressProofId,sr.*,u.`unit_no`,mm.`primary_owner_name`,mm.`member_id`,mof.`other_name`,mof.`relation`,u.`unit_id`, ad.* from addressproof_noc as ap, service_request as sr, unit as u, member_main as mm, approval_details as ad, mem_other_family as mof where ad.`verifiedStatus` = 'Y' and ad.`secondLevelApprovalStatus` = 'N' and mm.`unit`= u.`unit_id` and ap.`status` = 'Y' and sr.`request_id` = ap.`service_request_id` and ad.`referenceId` = ap.`id` and ad.`module_id` = '".ADDRESSPROOF_SOURCE_TABLE_ID."' and mof.`mem_other_family_id` = ap.`mem_other_family_id` and u.`unit_id` = ap.`unit_id` and mm.`ownership_status` = '1'";
			}
			$sql1_res = $this->m_dbConn->select($sql1);
			$result = $this->checkApprovalAccess($loginId,$societyId,"4");
			//echo "Approval Status: ".$result;
			//var_dump($sql1_res);
			for($i = 0 ; $i < sizeof($sql1_res);$i++)
			{
				//echo ""
				$sql2 = "Select `name` from login where login_id = '".$sql1_res[$i]['verifiedById']."';";
				$sql2_res = $this->m_dbConnRoot->select($sql2);
				$sql1_res[$i]['verifiedById'] = "Yes<br/>By: ".
				$sql1_res[$i]['verifiedByName'] = $sql2_res[0]['name'];
				if($sql1_res[$i]['firstLevelApprovalStatus'] == 'Y')
				{
					$sql3 = "Select `name` from login where login_id = '".$sql1_res[$i]['firstApprovalById']."';";
					$sql3_res = $this->m_dbConnRoot->select($sql3);
					$sql1_res[$i]['firstApprovedByName'] = $sql3_res[0]['name'];
					if($approvalLevel == "2")
					{
						if($sql1_res[$i]['firstApprovalById'] == $loginId)
						{
							$sql1_res[$i]['approvalAccess'] = false;
						}
						else
						{
							$sql1_res[$i]['approvalAccess'] = $result;
						}
					}
					else
					{
						$sql1_res[$i]['approvalAccess'] = $result;
					}
				}
				else
				{	
					$sql1_res[$i]['approvalAccess'] = $result;
				}
			}
		}
		if( $type == "approved")
		{
			if($approvalLevel == "1")
			{
				$sql1 = "select ap.*,ap.`id` as addressProofId,sr.*,u.`unit_no`,mm.`primary_owner_name`,mm.`member_id`,mof.`other_name`,mof.`relation`,u.`unit_id`, ad.* from addressproof_noc as ap, service_request as sr, unit as u, member_main as mm, approval_details as ad, mem_other_family as mof where ad.`verifiedStatus` = 'Y' and ad.`firstLevelApprovalStatus` = 'Y' and  mm.`unit`= u.`unit_id` and ap.`status` = 'Y' and sr.`request_id` = ap.`service_request_id` and ad.`referenceId` = ap.`id` and ad.`module_id` = '".ADDRESSPROOF_SOURCE_TABLE_ID."' and mof.`mem_other_family_id` = ap.`mem_other_family_id` and u.`unit_id` = ap.`unit_id` and mm.`ownership_status` = '1'";
			}
			else
			{
				$sql1 = "select ap.*,sr.*,ap.`id` as addressProofId,u.`unit_no`,mm.`primary_owner_name`,mm.`member_id`,mof.`other_name`,mof.`relation`,u.`unit_id`, ad.* from addressproof_noc as ap, service_request as sr, unit as u, member_main as mm, approval_details as ad, mem_other_family as mof where ad.`verifiedStatus` = 'Y' and ad.`firstLevelApprovalStatus` = 'Y' and ad.`secondLevelApprovalStatus` = 'Y' and mm.`unit`= u.`unit_id` and ap.`status` = 'Y' and sr.`request_id` = ap.`service_request_id` and ad.`referenceId` = ap.`id` and ad.`module_id` = '".ADDRESSPROOF_SOURCE_TABLE_ID."' and mof.`mem_other_family_id` = ap.`mem_other_family_id` and u.`unit_id` = ap.`unit_id` and mm.`ownership_status` = '1'";
			}
			$sql1_res = $this->m_dbConn->select($sql1);
			for($i = 0 ; $i < sizeof($sql1_res);$i++)
			{
				$sql2 = "Select `name` from login where login_id = '".$sql1_res[$i]['verifiedById']."';";
				$sql2_res = $this->m_dbConnRoot->select($sql2);
				$sql3 = "Select `name` from login where login_id = '".$sql1_res[$i]['firstApprovalById']."';";
				$sql3_res = $this->m_dbConnRoot->select($sql3);
				$sql4 = "Select `name` from login where login_id = '".$sql1_res[$i]['secondApprovalById']."';";
				$sql4_res = $this->m_dbConnRoot->select($sql4);
				$sql1_res[$i]['verifiedByName'] = $sql2_res[0]['name'];
				$sql1_res[$i]['firstApprovedByName'] = $sql3_res[0]['name'];
				$sql1_res[$i]['secondApprovedByName'] = $sql4_res[0]['name'];
			}
		}
		//var_dump($sql1_res);
		for($i = 0 ; $i < sizeof($sql1_res);$i++)
		{
			$sql1_res[$i]['since_staying_date'] = getDisplayFormatDate($sql1_res[$i]['since_staying_date']);
			$sql1_res[$i]['dateofrequest'] = getDisplayFormatDate($sql1_res[$i]['dateofrequest']);
			if($sql1_res[$i]['purpose_code'] == "1")
			{
				$sql1_res[$i]['purpose_code'] = "Domicile Certificate NOC";
			}
			else if($sql1_res[$i]['purpose_code'] == "2")
			{
				$sql1_res[$i]['purpose_code'] = "Passport NOC";
			}
			else
			{
				$sql1_res[$i]['purpose_code'] = "Address Proof NOC";
			}
		}
		
		return $sql1_res;
	}
	public function checkApprovalAccess($loginId,$societyId,$moduleId)
	{
		$result = false;
		//echo "moduleId : ".$moduleId;
		if($moduleId == "2")//Renovation Request
		{
			$sql1 = "Select p.`PROFILE_APPROVAL_OF_RENOVATION_REQUEST` from mapping as m, profile as p, login as l where l.`login_id` = '".$loginId."' and p.`id` = m.`profile` and m.`society_id` = '".$societyId."' and m.`login_id` = l.`login_id` and m.`role` = '".$_SESSION['role']."' and m.`status` = '2'";
			$sql1_res = $this->m_dbConnRoot->select($sql1);
			if($sql1_res[0]['PROFILE_APPROVAL_OF_RENOVATION_REQUEST'] == 1)
			{
				$result = true;
			}
		}
		else if($moduleId == "3")//Tenant
		{
			$sql1 = "Select p.`PROFILE_APPROVALS_LEASE` from mapping as m, profile as p, login as l where l.`login_id` = '".$loginId."' and p.`id` = m.`profile` and m.`society_id` = '".$societyId."' and m.`login_id` = l.`login_id` and m.`role` = '".$_SESSION['role']."' and m.`status` = '2'";
			$sql1_res = $this->m_dbConnRoot->select($sql1);
			if($sql1_res[0]['PROFILE_APPROVALS_LEASE'] == 1)
			{
				$result = true;
			}
		}
		else//AddresProof Request
		{
			$sql1 = "Select p.`PROFILE_APPROVAL_OF_NOC` from mapping as m, profile as p, login as l where l.`login_id` = '".$loginId."' and p.`id` = m.`profile` and m.`society_id` = '".$societyId."' and m.`login_id` = l.`login_id` and m.`role` = '".$_SESSION['role']."' and m.`status` = '2'";
			$sql1_res = $this->m_dbConnRoot->select($sql1);
			//var_dump($sql1_res);
			if($sql1_res[0]['PROFILE_APPROVAL_OF_NOC'] == 1)
			{
				$result = true;
			}
		}
		return($result);
	}
	public function checkVerificationAccess($loginId,$societyId,$role,$moduleId)
	{
		$result = false;
		if($moduleId == "2")//Renovation Request
		{
			$sql1 = "Select p.`PROFILE_VERIFICATION_OF_RENOVATION_REQUEST` from mapping as m, profile as p, login as l where l.`login_id` = '".$loginId."' and p.`id` = m.`profile` and m.`society_id` = '".$societyId."' and m.`login_id` = l.`login_id` and m.`role` = '".$role."' and m.`status` = '2'";
			$sql1_res = $this->m_dbConnRoot->select($sql1);
			if($sql1_res[0]['PROFILE_VERIFICATION_OF_RENOVATION_REQUEST'] == 1)
			{
				$result = true;
			}
		}
		else
		{
			if(($_SESSION['role'] == ROLE_SUPER_ADMIN)||($_SESSION['role'] == ROLE_ADMIN_MEMBER )|| ($_SESSION['role'] == ROLE_ADMIN))
			{
				$result = true;
			}
		}
		return($result);
	}
	public function getApprovalLevel($moduleId)
	{
		if($moduleId == "2")//Renovation Request
		{
			$sql1 = "Select `Value` from appdefault_new where `Property` = 'LevelOfApprovalForRenovationRequest' and module_id = '2';";
			$sql1_res = $this->m_dbConn->select($sql1);
		}
		else if($moduleId == "3")//Tenant
		{
			$sql1 = "Select `Value` from appdefault_new where `Property` = 'LevelOfApprovalForTenantRequest' and module_id = '3';";
			$sql1_res = $this->m_dbConn->select($sql1);
		}
		else //AddressProof
		{
			$sql1 = "Select `Value` from appdefault_new where `Property` = 'LevelOfApprovalForAddressProofRequest' and module_id = '4';";
			$sql1_res = $this->m_dbConn->select($sql1);
		}
		return ($sql1_res[0]['Value']);
	}
	public function approveRequest($requestType,$requestId,$action,$loginId,$serviceRequestId)
	{
		$_SESSION['login_id'] = $loginId;
		//echo "Session value :".$_SESSION['login_id'];
		//echo "<br>requestType : ".$requestType;
		$this->obj_serviceRequest->getRenovationId();
		//var_dump($_SESSION);
		if($requestType == $_SESSION['RENOVATION_DOC_ID'])
		{
			//echo "in if";
			$result = $this->obj_documentMaker->updateRenovationStatus($requestId,$action);
		}
		else if($requestType == $_SESSION['ADDRESS_PROOF_ID'])
		{
			//echo "in else if";
			$result = $this->obj_documentMaker->updateAddressProofStatus($serviceRequestId,$requestId,$action);
		}
		else
		{
			
		}
		return ($result);
	}
}
?>
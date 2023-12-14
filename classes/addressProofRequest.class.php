<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once ("dbconst.class.php");
include_once('../swift/swift_required.php');
include_once( "include/fetch_data.php");
include_once("utility.class.php");
include_once("include/dbop.class.php");
include_once ("servicerequest.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
class addressProofApproval
{
	//public $actionPage = "../events_view.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	public $actionPage;
	public $obj_servicerequest;
	
	function __construct($dbConn, $dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->m_dbConnRoot = $dbConnRoot;
		$this->actionPage = "";
		$this->obj_servicerequest = new servicerequest($this->m_dbConn);
		//dbop::__construct();
	}
	//Vaishali's Code
	//Function to get pending, approved and completed renovation request.
	public function getAddressProofRequests($type)
	{
		$approvalLevel = $this->getApprovalLevel();
		//echo $approvalLevel;
		if($type == "pending")
		{
			$verificationAccess = $this->checkVerificationAccess($_SESSION['role']);
			$sql1 = "select ap.`id`,sr.`request_id`,sr.`request_no`,sr.`priority`,u.`unit_no`,mm.`owner_name`,mm.`member_id`,ap.`purpose_code`,mof.`other_name`,ap.`since_staying_date`,u.`unit_id`, ad.`verifiedById`, ad.`firstApprovalById`, ad.`secondApprovalById`, ad.`verifiedByDesignation`, ad.`firstApprovalByDesignation`, ad.`SecondApprovalByDesignation`,ad.`verifiedStatus`,ad.`firstLevelApprovalStatus`, ad.`secondLevelApprovalStatus`,ap.`id` from addressproof_noc as ap, service_request as sr, unit as u, member_main as mm, approval_details as ad, mem_other_family as mof where ad.`verifiedStatus` = 'N' and mm.`unit`= u.`unit_id` and ap.`status` = 'Y' and sr.`request_id` = ap.`service_request_id` and ad.`referenceId` = ap.`id` and ad.`module_id` = '".ADDRESSPROOF_SOURCE_TABLE_ID."' and mof.`mem_other_family_id` = ap.`mem_other_family_id` and u.`unit_id` = ap.`unit_id` and mm.`ownership_status` = '1'";
			$sql1_res = $this->m_dbConn->select($sql1);
			for($i = 0 ; $i < sizeof($sql1_res);$i++)
			{
				if($verificationAccess)
				{
						$sql1_res[$i]['verifiedById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sql1_res[$i]['request_id']."&apId=".$sql1_res[$i]['id']."&action=verify' target='_self'><font color='#FF0000'>Click here to verify!</font></a>";
				}
				else
				{
					$sql1_res[$i]['verifiedById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sql1_res[$i]['request_id']."&apId=".$sql1_res[$i]['id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
				}
				$sql1_res[$i]['firstApprovalById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sql1_res[$i]['request_id']."&apId=".$sql1_res[$i]['id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
				$sql1_res[$i]['secondApprovalById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sql1_res[$i]['request_id']."&apId=".$sql1_res[$i]['id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
			}
		}
		if($type == "verified")
		{
			if($approvalLevel == "1")
			{
				$sql1 = "select  ap.`id`,sr.`request_id`,sr.`request_no`,sr.`priority`,u.`unit_no`,mm.`owner_name`,mm.`member_id`,ap.`purpose_code`,mof.`other_name`,ap.`since_staying_date`,u.`unit_id`, ad.`verifiedById`, ad.`firstApprovalById`, ad.`secondApprovalById`, ad.`verifiedByDesignation`, ad.`firstApprovalByDesignation`, ad.`SecondApprovalByDesignation`,ad.`verifiedStatus`,ad.`firstLevelApprovalStatus`, ad.`secondLevelApprovalStatus` from addressproof_noc as ap, service_request as sr, unit as u, member_main as mm, approval_details as ad, mem_other_family as mof where ad.`verifiedStatus` = 'Y' and ad.`firstLevelApprovalStatus` = 'N' and mm.`unit`= u.`unit_id` and ap.`status` = 'Y' and sr.`request_id` = ap.`service_request_id` and ad.`referenceId` = ap.`id` and ad.`module_id` = '".ADDRESSPROOF_SOURCE_TABLE_ID."' and mof.`mem_other_family_id` = ap.`mem_other_family_id` and u.`unit_id` = ap.`unit_id` and mm.`ownership_status` = '1'";
			}
			else
			{
				$sql1 = "select ap.`id`,sr.`request_id`,sr.`request_no`,sr.`priority`,u.`unit_no`,mm.`owner_name`,mm.`member_id`,ap.`purpose_code`,mof.`other_name`,ap.`since_staying_date`,u.`unit_id`, ad.`verifiedById`, ad.`firstApprovalById`, ad.`secondApprovalById`, ad.`verifiedByDesignation`, ad.`firstApprovalByDesignation`, ad.`SecondApprovalByDesignation`,ad.`verifiedStatus`,ad.`firstLevelApprovalStatus`, ad.`secondLevelApprovalStatus` from addressproof_noc as ap, service_request as sr, unit as u, member_main as mm, approval_details as ad, mem_other_family as mof where ad.`verifiedStatus` = 'Y' and ad.`secondLevelApprovalStatus` = 'N' and mm.`unit`= u.`unit_id` and ap.`status` = 'Y' and sr.`request_id` = ap.`service_request_id` and ad.`referenceId` = ap.`id` and ad.`module_id` = '".ADDRESSPROOF_SOURCE_TABLE_ID."' and mof.`mem_other_family_id` = ap.`mem_other_family_id` and u.`unit_id` = ap.`unit_id` and mm.`ownership_status` = '1'";
			}
			$sql1_res = $this->m_dbConn->select($sql1);
			$result = $this->checkApprovalAccess();
			//echo $result;
			//var_dump($sql1_res);
			for($i = 0 ; $i < sizeof($sql1_res);$i++)
			{
				$sql2 = "Select `name` from login where login_id = '".$sql1_res[$i]['verifiedById']."';";
				$sql2_res = $this->m_dbConnRoot->select($sql2);
				$sql1_res[$i]['verifiedById'] = "Yes<br/>By: ".$sql2_res[0]['name']."<br/>Post: ".$sql1_res[$i]['verifiedByDesignation']."<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sql1_res[$i]['request_id']."&apId=".$sql1_res[$i]['id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
				if($sql1_res[$i]['firstLevelApprovalStatus'] == 'Y')
				{
					$sql3 = "Select `name` from login where login_id = '".$sql1_res[$i]['firstApprovalById']."';";
					$sql3_res = $this->m_dbConnRoot->select($sql3);
					//echo $sql3_res[0]['name'];
					if($result)
					{
						if($approvalLevel == "1")
						{
							$sql1_res[$i]['firstApprovalById'] = "Yes<br>By: ".$sql3_res[0]['name']."<br/>Post: ".$sql1_res[$i]['firstApprovalByDesignation']."<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sql1_res[$i]['request_id']."&apId=".$sql1_res[$i]['id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
							$sql1_res[$i]['secondApprovalById'] = "Yes<br>By: ".$sql3_res[0]['name']."<br/>Post: ".$sql1_res[$i]['firstApprovalByDesignation']."<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sql1_res[$i]['request_id']."&apId=".$sql1_res[$i]['id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";	
						}
						else
						{
							if($sql1_res[$i]['firstApprovalById'] == $_SESSION['login_id'])
							{
								$sql1_res[$i]['firstApprovalById'] = "Yes<br>By: ".$sql3_res[0]['name']."<br/>Post: ".$sql1_res[$i]['firstApprovalByDesignation']."<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sql1_res[$i]['request_id']."&apId=".$sql1_res[$i]['id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
								$sql1_res[$i]['secondApprovalById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sql1_res[$i]['request_id']."&apId=".$sql1_res[$i]['id']."&action=view' target='_self'><font color='#FF0000'>Click here to View!</font></a>";
								
							}
							else
							{
								$sql1_res[$i]['firstApprovalById'] = "Yes<br>By: ".$sql3_res[0]['name']."<br/>Post: ".$sql1_res[$i]['SecondApprovalByDesignation']."<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sql1_res[$i]['request_id']."&apId=".$sql1_res[$i]['id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
								$sql1_res[$i]['secondApprovalById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sql1_res[$i]['request_id']."&apId=".$sql1_res[$i]['id']."&action=approve' target='_self'><font color='#FF0000'>Click here to Approve!</font></a>";
															
							}
						}
					}
					else
					{
						$sql1_res[$i]['secondApprovalById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sql1_res[$i]['request_id']."&apId=".$sql1_res[$i]['id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
						$sql1_res[$i]['firstApprovalById'] = "Yes<br>By: ".$sql3_res[0]['name']."<br/>Post: ".$sql1_res[$i]['SecondApprovalByDesignation']."<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sql1_res[$i]['request_id']."&apId=".$sql1_res[$i]['id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";			
					}
				}
				else
				{
					if($result)
					{
						
						$sql1_res[$i]['firstApprovalById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sql1_res[$i]['request_id']."&apId=".$sql1_res[$i]['id']."&action=approve' target='_self'><font color='#FF0000'>Click here to Approve!</font></a>";
						$sql1_res[$i]['secondApprovalById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sql1_res[$i]['request_id']."&apId=".$sql1_res[$i]['id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
					}
					else
					{
						$sql1_res[$i]['firstApprovalById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sql1_res[$i]['request_id']."&apId=".$sql1_res[$i]['id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
						$sql1_res[$i]['secondApprovalById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sql1_res[$i]['request_id']."&apId=".$sql1_res[$i]['id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";	
					}
				}
			}
			
		}
		if( $type == "approved")
		{
			if($approvalLevel == "1")
			{
				$sql1 = "select ap.`id`,sr.`request_id`,sr.`request_no`,sr.`priority`,u.`unit_no`,mm.`owner_name`,mm.`member_id`,ap.`purpose_code`,mof.`other_name`,ap.`since_staying_date`,u.`unit_id`, ad.`verifiedById`, ad.`firstApprovalById`, ad.`secondApprovalById`, ad.`verifiedByDesignation`, ad.`firstApprovalByDesignation`, ad.`SecondApprovalByDesignation`,ad.`verifiedStatus`,ad.`firstLevelApprovalStatus`, ad.`secondLevelApprovalStatus` from addressproof_noc as ap, service_request as sr, unit as u, member_main as mm, approval_details as ad, mem_other_family as mof where ad.`verifiedStatus` = 'Y' and ad.`firstLevelApprovalStatus` = 'Y' and  mm.`unit`= u.`unit_id` and ap.`status` = 'Y' and sr.`request_id` = ap.`service_request_id` and ad.`referenceId` = ap.`id` and ad.`module_id` = '".ADDRESSPROOF_SOURCE_TABLE_ID."' and mof.`mem_other_family_id` = ap.`mem_other_family_id` and u.`unit_id` = ap.`unit_id` and mm.`ownership_status` = '1'";
			}
			else
			{
				$sql1 = "select ap.`id`,sr.`request_id`,sr.`request_no`,sr.`priority`,u.`unit_no`,mm.`owner_name`,mm.`member_id`,ap.`purpose_code`,mof.`other_name`,ap.`since_staying_date`,u.`unit_id`, ad.`verifiedById`, ad.`firstApprovalById`, ad.`secondApprovalById`, ad.`verifiedByDesignation`, ad.`firstApprovalByDesignation`, ad.`SecondApprovalByDesignation`,ad.`verifiedStatus`,ad.`firstLevelApprovalStatus`, ad.`secondLevelApprovalStatus` from addressproof_noc as ap, service_request as sr, unit as u, member_main as mm, approval_details as ad, mem_other_family as mof where ad.`verifiedStatus` = 'Y' and ad.`firstLevelApprovalStatus` = 'Y' and ad.`secondLevelApprovalStatus` = 'Y' and mm.`unit`= u.`unit_id` and ap.`status` = 'Y' and sr.`request_id` = ap.`service_request_id` and ad.`referenceId` = ap.`id` and ad.`module_id` = '".ADDRESSPROOF_SOURCE_TABLE_ID."' and mof.`mem_other_family_id` = ap.`mem_other_family_id` and u.`unit_id` = ap.`unit_id` and mm.`ownership_status` = '1'";
			}
			$sql1_res = $this->m_dbConn->select($sql1);
			$result = $this->checkApprovalAccess();
			for($i = 0 ; $i < sizeof($sql1_res);$i++)
			{
				$sql2 = "Select `name` from login where login_id = '".$sql1_res[$i]['verifiedById']."';";
				$sql2_res = $this->m_dbConnRoot->select($sql2);
				$sql3 = "Select `name` from login where login_id = '".$sql1_res[$i]['firstApprovalById']."';";
				$sql3_res = $this->m_dbConnRoot->select($sql3);
				$sql4 = "Select `name` from login where login_id = '".$sql1_res[$i]['secondApprovalById']."';";
				$sql4_res = $this->m_dbConnRoot->select($sql4);
				$sql1_res[$i]['verifiedById'] = "Yes<br/>By: ".$sql2_res[0]['name']."<br/>Post: ".$sql1_res[$i]['verifiedByDesignation']."<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sql1_res[$i]['request_id']."&apId=".$sql1_res[$i]['id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
				$sql1_res[$i]['firstApprovalById'] = "Yes<br/>By: ".$sql3_res[0]['name']."<br/>Post: ".$sql1_res[$i]['firstApprovalByDesignation']."<br><a href = 'document_maker.php?temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sql1_res[$i]['request_id']."&apId=".$sql1_res[$i]['id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
				$sql1_res[$i]['secondApprovalById'] = "Yes<br/>By: ".$sql4_res[0]['name']."<br/>Post: ".$sql1_res[$i]['SecondApprovalByDesignation']."<br><a href = 'document_maker.php?temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sql1_res[$i]['request_id']."&apId=".$sql1_res[$i]['id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
			}
		}
		//var_dump($sql1_res);
		for($i = 0 ; $i < sizeof($sql1_res);$i++)
		{
			//var_dump($typeOfWorkList);
			//$sql1_res[$i]['type_of_work'] = $typeOfWorkList; 
			$sql1_res[$i]['owner_name'] = "<a href = 'view_member_profile.php?prf&id=".$sql1_res[$i]['member_id']."' target='_self'>".$sql1_res[$i]['owner_name']."</a>";
			$sql1_res[$i]['request_no'] = "<a href = 'viewrequest.php?rq=".$sql1_res[$i]['request_no']."' target='_self'>".$sql1_res[$i]['request_no']."</a>";
		}
		return $sql1_res;
	}
	public function checkApprovalAccess()
	{
		$sql1 = "Select p.`PROFILE_APPROVAL_OF_NOC` from mapping as m, profile as p, login as l where l.`login_id` = '".$_SESSION['login_id']."' and p.`id` = m.`profile` and m.`society_id` = '".$_SESSION['society_id']."' and m.`login_id` = l.`login_id` and m.`role` = '".$_SESSION['role']."' and m.`status` = '2'";
		$sql1_res = $this->m_dbConnRoot->select($sql1);
		$result = false;
		if($sql1_res[0]['PROFILE_APPROVAL_OF_NOC'] == 1)
		{
			$result = true;
		}
		return($result);
	}
	public function getApprovalLevel()
	{
		$sql1 = "Select `Value` from appdefault_new where `Property` = 'LevelOfApprovalForAddressProofRequest' and module_id = '4';";
		$sql1_res = $this->m_dbConn->select($sql1);
		return ($sql1_res[0]['Value']);
	}
	public function checkVerificationAccess($role)
	{
		if($role == ROLE_ADMIN || $role == ROLE_SUPER_ADMIN)
		{
			$result = true;
		}
		else
		{
			$result = false;
		}
		return($result);
	}
		//End
}
?>
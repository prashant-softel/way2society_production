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
class renovationRequest
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
	public function getRenovationRequest($type)
	{
		$approvalLevel = $this->getApprovalLevel();
		if($type == "pending")
		{
			$verificationAccess = $this->checkVerificationAccess();
			$sql1 = "select rr.`Id`,sr.`request_no`,sr.`priority`,u.`unit_no`,mm.`primary_owner_name`,mm.`member_id`,rr.`Id`,rr.`application_date`, rr.`type_of_work`, u.`unit_id`, ad.`verifiedById`, ad.`firstApprovalById`, ad.`secondApprovalById`, ad.`verifiedByDesignation`, ad.`firstApprovalByDesignation`, ad.`SecondApprovalByDesignation`,ad.`verifiedStatus`,ad.`firstLevelApprovalStatus`, ad.`secondLevelApprovalStatus` from renovation_details as rr, service_request as sr, unit as u, member_main as mm, approval_details as ad where ad.`verifiedStatus` = 'N' and rr.`status` = 'Y' and u.`unit_id` = rr.`unit_id` and mm.`unit`= u.`unit_id` and rr.`status` = 'Y' and sr.`request_id` = rr.`request_id` and ad.`referenceId` = rr.`Id` and ad.`module_id` ='".RENOVATION_SOURCE_TABLE_ID."' and mm.`ownership_status`= '1' ORDER BY rr.`Id` asc";
			
			$sql1_res = $this->m_dbConn->select($sql1);
			for($i = 0 ; $i < sizeof($sql1_res);$i++)
			{
				if($verificationAccess)
				{
					$sql1_res[$i]['verifiedById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['RENOVATION_DOC_ID']."&rId=".$sql1_res[$i]['Id']."&action=verify' target='_self'><font color='#FF0000'>Click here to verify!</font></a>";
				}
				else
				{
					$sql1_res[$i]['verifiedById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['RENOVATION_DOC_ID']."&rId=".$sql1_res[$i]['Id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
				}
				$sql1_res[$i]['firstApprovalById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['RENOVATION_DOC_ID']."&rId=".$sql1_res[$i]['Id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
				$sql1_res[$i]['secondApprovalById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['RENOVATION_DOC_ID']."&rId=".$sql1_res[$i]['Id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
			}
		}
		if($type == "verified")
		{
			if($approvalLevel == "1")
			{
				$sql1 = "select rr.`Id`,sr.`request_no`,sr.`priority`,u.`unit_no`,mm.`primary_owner_name`,mm.`member_id`,rr.`Id`,rr.`application_date`, rr.`type_of_work`, u.`unit_id`, ad.`verifiedById`, ad.`firstApprovalById`, ad.`secondApprovalById`, ad.`verifiedByDesignation`, ad.`firstApprovalByDesignation`, ad.`SecondApprovalByDesignation`,ad.`verifiedStatus`,ad.`firstLevelApprovalStatus`, ad.`secondLevelApprovalStatus` from renovation_details as rr, service_request as sr, unit as u, member_main as mm, approval_details as ad where ad.`verifiedStatus` = 'Y' and ad.`firstLevelApprovalStatus` = 'N' and rr.`status` = 'Y' and u.`unit_id` = rr.`unit_id` and mm.`unit`= u.`unit_id` and rr.`status` = 'Y' and sr.`request_id` = rr.`request_id` and ad.`referenceId` = rr.`Id` and ad.`module_id` = '".RENOVATION_SOURCE_TABLE_ID."' and mm.`ownership_status`= '1' ORDER BY rr.`Id` asc";
			
			}
			else
			{
				$sql1 = "select rr.`Id`,sr.`request_no`,sr.`priority`,u.`unit_no`,mm.`primary_owner_name`,mm.`member_id`,rr.`Id`,rr.`application_date`, rr.`type_of_work`, u.`unit_id`, ad.`verifiedById`, ad.`firstApprovalById`, ad.`secondApprovalById`, ad.`verifiedByDesignation`, ad.`firstApprovalByDesignation`, ad.`SecondApprovalByDesignation`,ad.`verifiedStatus`,ad.`firstLevelApprovalStatus`, ad.`secondLevelApprovalStatus` from renovation_details as rr, service_request as sr, unit as u, member_main as mm, approval_details as ad where ad.`verifiedStatus` = 'Y' and ad.`secondLevelApprovalStatus` = 'N' and rr.`status` = 'Y' and u.`unit_id` = rr.`unit_id` and mm.`unit`= u.`unit_id` and rr.`status` = 'Y' and sr.`request_id` = rr.`request_id` and ad.`referenceId` = rr.`Id` and ad.`module_id` = '".RENOVATION_SOURCE_TABLE_ID."' and mm.`ownership_status`= '1' ORDER BY rr.`Id` asc";
			}
			$sql1_res = $this->m_dbConn->select($sql1);
			$result = $this->checkApprovalAccess();
			for($i = 0 ; $i < sizeof($sql1_res);$i++)
			{
				$sql2 = "Select `name` from login where login_id = '".$sql1_res[$i]['verifiedById']."';";
				$sql2_res = $this->m_dbConnRoot->select($sql2);
				$sql1_res[$i]['verifiedById'] = "Yes<br/>By: ".$sql2_res[0]['name']."<br/>Post: ".$sql1_res[$i]['verifiedByDesignation']."<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['RENOVATION_DOC_ID']."&rId=".$sql1_res[$i]['Id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
				if($sql1_res[$i]['firstLevelApprovalStatus'] == 'Y')
				{
					$sql3 = "Select `name` from login where login_id = '".$sql1_res[$i]['firstApprovalById']."';";
					$sql3_res = $this->m_dbConnRoot->select($sql3);
					//echo $sql3_res[0]['name'];
					//echo "result: ".$result;
					if($result)
					{
						//echo "Approval Level :".$approvalLevel;
						if($approvalLevel == "1")
						{
							$sql1_res[$i]['firstApprovalById'] = "Yes<br>By: ".$sql3_res[0]['name']."<br/>Post: ".$sql1_res[$i]['firstApprovalByDesignation']."<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['RENOVATION_DOC_ID']."&aId=".$sql1_res[$i]['request_id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
							$sql1_res[$i]['secondApprovalById'] = "Yes<br>By: ".$sql3_res[0]['name']."<br/>Post: ".$sql1_res[$i]['firstApprovalByDesignation']."<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['RENOVATION_DOC_ID']."&aId=".$sql1_res[$i]['request_id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";	
						}
						else
						{
							//echo "in else";
							if($sql1_res[$i]['firstApprovalById'] == $_SESSION['login_id'])
							{
								$sql1_res[$i]['firstApprovalById'] = "Yes<br>By: ".$sql3_res[0]['name']."<br/>Post: ".$sql1_res[$i]['firstApprovalByDesignation']."<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['RENOVATION_DOC_ID']."&rId=".$sql1_res[$i]['Id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
								$sql1_res[$i]['secondApprovalById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['RENOVATION_DOC_ID']."&rId=".$sql1_res[$i]['Id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";		
							}
							else
							{
								//echo "in else2";
								$sql1_res[$i]['secondApprovalById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['RENOVATION_DOC_ID']."&rId=".$sql1_res[$i]['Id']."&action=approve' target='_self'><font color='#FF0000'>Click here to Approve!</font></a>";
								$sql1_res[$i]['firstApprovalById'] = "Yes<br>By: ".$sql3_res[0]['name']."<br/>Post: ".$sql1_res[$i]['firstApprovalByDesignation']."<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['RENOVATION_DOC_ID']."&rId=".$sql1_res[$i]['Id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";							
							}
						}
					}
					else
					{
						$sql1_res[$i]['secondApprovalById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['RENOVATION_DOC_ID']."&rId=".$sql1_res[$i]['Id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
						$sql1_res[$i]['firstApprovalById'] = "Yes<br>By: ".$sql3_res[0]['name']."<br/>Post: ".$sql1_res[$i]['firstApprovalByDesignation']."<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['RENOVATION_DOC_ID']."&rId=".$sql1_res[$i]['Id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";			
					}
				}
				else
				{
					if($result)
					{
						
						$sql1_res[$i]['firstApprovalById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['RENOVATION_DOC_ID']."&rId=".$sql1_res[$i]['Id']."&action=approve' target='_self'><font color='#FF0000'>Click here to Approve!</font></a>";
						$sql1_res[$i]['secondApprovalById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['RENOVATION_DOC_ID']."&rId=".$sql1_res[$i]['Id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
					}
					else
					{
						$sql1_res[$i]['firstApprovalById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['RENOVATION_DOC_ID']."&rId=".$sql1_res[$i]['Id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
						$sql1_res[$i]['secondApprovalById'] = "No<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['RENOVATION_DOC_ID']."&rId=".$sql1_res[$i]['Id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";	
					}
				}
			}
			
		}
		if( $type == "approved")
		{
			if($approvalLevel == "1")
			{
				$sql1 = "select rr.`Id`,sr.`request_no`,sr.`priority`,u.`unit_no`,mm.`primary_owner_name`,mm.`member_id`,rr.`Id`,rr.`application_date`, rr.`type_of_work`, u.`unit_id`, ad.`verifiedById`, ad.`firstApprovalById`, ad.`secondApprovalById`, ad.`verifiedByDesignation`, ad.`firstApprovalByDesignation`, ad.`SecondApprovalByDesignation`,ad.`verifiedStatus`,ad.`firstLevelApprovalStatus`, ad.`secondLevelApprovalStatus` from renovation_details as rr, service_request as sr, unit as u, member_main as mm, approval_details as ad where ad.`verifiedStatus` = 'Y' and ad.`firstLevelApprovalStatus` = 'Y' and rr.`status` = 'Y' and u.`unit_id` = rr.`unit_id` and mm.`unit`= u.`unit_id` and rr.`status` = 'Y' and sr.`request_id` = rr.`request_id` and ad.`referenceId` = rr.`Id` and ad.`module_id` = '".RENOVATION_SOURCE_TABLE_ID."' and mm.`ownership_status`= '1' ORDER BY rr.`Id` asc";
			}
			else
			{
				$sql1 = "select rr.`Id`,sr.`request_no`,sr.`priority`,u.`unit_no`,mm.`primary_owner_name`,mm.`member_id`,rr.`Id`,rr.`application_date`, rr.`type_of_work`, u.`unit_id`, ad.`verifiedById`, ad.`firstApprovalById`, ad.`secondApprovalById`, ad.`verifiedByDesignation`, ad.`firstApprovalByDesignation`, ad.`SecondApprovalByDesignation`,ad.`verifiedStatus`,ad.`firstLevelApprovalStatus`, ad.`secondLevelApprovalStatus` from renovation_details as rr, service_request as sr, unit as u, member_main as mm, approval_details as ad where ad.`verifiedStatus` = 'Y' and ad.`firstLevelApprovalStatus` = 'Y' and ad.`secondLevelApprovalStatus` = 'Y' and rr.`status` = 'Y' and u.`unit_id` = rr.`unit_id` and mm.`unit`= u.`unit_id` and rr.`status` = 'Y' and sr.`request_id` = rr.`request_id` and ad.`referenceId` = rr.`Id` and ad.`module_id` = '".RENOVATION_SOURCE_TABLE_ID."' and mm.`ownership_status`= '1' ORDER BY rr.`Id` asc";
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
				$sql1_res[$i]['verifiedById'] = "Yes<br/>By: ".$sql2_res[0]['name']."<br/>Post: ".$sql1_res[$i]['verifiedByDesignation']."<br><a id='link' href = 'document_maker.php?temp=".$_SESSION['RENOVATION_DOC_ID']."&rId=".$sql1_res[$i]['Id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
				$sql1_res[$i]['firstApprovalById'] = "Yes<br/>By: ".$sql3_res[0]['name']."<br/>Post: ".$sql1_res[$i]['firstApprovalByDesignation']."<br><a href = 'document_maker.php?temp=".$_SESSION['RENOVATION_DOC_ID']."&rId=".$sql1_res[$i]['Id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
				$sql1_res[$i]['secondApprovalById'] = "Yes<br/>By: ".$sql4_res[0]['name']."<br/>Post: ".$sql1_res[$i]['SecondApprovalByDesignation']."<br><a href = 'document_maker.php?temp=".$_SESSION['RENOVATION_DOC_ID']."&rId=".$sql1_res[$i]['Id']."&action=view' target='_self'><font color='#FF0000'>Click here to view!</font></a>";
			}
		}
		//var_dump($sql1_res);
		for($i = 0 ; $i < sizeof($sql1_res);$i++)
		{
			$sql5 = "select * from documents where refId = '".$sql1_res[$i]['Id']."' and source_table = '".RENOVATION_SOURCE_TABLE_ID."'";
			$sql5_res = $this->m_dbConn->select($sql5);
			for($j = 0;$j < sizeof($sql5_res); $j++)
			{
				$doc_version=$sql5_res[$j]['doc_version'];
				$URL = "";
				$gdrive_id = $sql5_res[$j]['attachment_gdrive_id'];
				if($doc_version == "1")
				{
					$URL = "Uploaded_Documents/". $sql5_res[$j]["Document"];
				}
				else if($doc_version == "2")
				{
					if($gdrive_id == "" || $gdrive_id == "-")
					{
						$URL = "Uploaded_Documents/". $sql5_res[$j]["Document"];
					}
					else
					{
						$URL = "https://drive.google.com/file/d/". $gdrive_id."/view";
					}
				}
				$sql5_res[$j]['documentLink'] = $URL;
			}
			//var_dump($sql5_res);
			$typeOfWorkList = "";
			$workList = $sql1_res[$i]['type_of_work'];
			$workListArr =  explode(",",$workList);
			//var_dump($workListArr);
			$check = false;
			for($m = 0; $m < sizeof($workListArr);$m++)
			{
				for($d = 0; $d < sizeof($sql5_res) ; $d++)
				{
					if($workListArr[$m] == $sql5_res[$d]['Name'])
					{
						$typeOfWorkList .= "<a href = '".$sql5_res[$d]['documentLink']."' target='_self'>".$sql5_res[$d]['Name']."</a><br>";//Start coding
						$check = true;
					}
				}
				if($check)
				{
				}
				else
				{
					$typeOfWorkList .= $workListArr[$m]."<br/>";
				}
				$check = false;
			}
			//var_dump($typeOfWorkList);
			$sql1_res[$i]['type_of_work'] = $typeOfWorkList; 
			$sql1_res[$i]['owner_name'] = "<a href = 'view_member_profile.php?prf&id=".$sql1_res[$i]['member_id']."' target='_self'>".$sql1_res[$i]['owner_name']."</a>";
			$sql1_res[$i]['request_no'] = "<a href = 'viewrequest.php?rq=".$sql1_res[$i]['request_no']."' target='_self'>".$sql1_res[$i]['request_no']."</a>";
		}
		return $sql1_res;
	}
	public function checkApprovalAccess()
	{
		$sql1 = "Select p.`PROFILE_APPROVAL_OF_RENOVATION_REQUEST` from mapping as m, profile as p, login as l where l.`login_id` = '".$_SESSION['login_id']."' and p.`id` = m.`profile` and m.`society_id` = '".$_SESSION['society_id']."' and m.`login_id` = l.`login_id` and m.`role` = '".$_SESSION['role']."' and m.`status` = '2'";
		$sql1_res = $this->m_dbConnRoot->select($sql1);
		$result = false;
		if($sql1_res[0]['PROFILE_APPROVAL_OF_RENOVATION_REQUEST'] == 1)
		{
			$result = true;
		}
		return($result);
	}
	public function checkVerificationAccess()
	{
		$sql1 = "Select p.`PROFILE_VERIFICATION_OF_RENOVATION_REQUEST` from mapping as m, profile as p, login as l where l.`login_id` = '".$_SESSION['login_id']."' and p.`id` = m.`profile` and m.`society_id` = '".$_SESSION['society_id']."' and m.`login_id` = l.`login_id` and m.`role` = '".$_SESSION['role']."' and m.`status` = '2'";
		$sql1_res = $this->m_dbConnRoot->select($sql1);
		$result = false;
		if($sql1_res[0]['PROFILE_VERIFICATION_OF_RENOVATION_REQUEST'] == 1)
		{
			$result = true;
		}
		return($result);
	}
	public function getApprovalLevel()
	{
		$sql1 = "Select `Value` from appdefault_new where `Property` = 'LevelOfApprovalForRenovationRequest' and module_id = '2';";
		$sql1_res = $this->m_dbConn->select($sql1);
		return ($sql1_res[0]['Value']);
	}
	//End
	
}
?>
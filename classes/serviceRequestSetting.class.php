<?php
if(!isset($_SESSION)){ session_start(); }
include_once ("dbconst.class.php"); 
include_once("include/dbop.class.php");

class serviceRequestSetting
{
	//public $actionPage = "../addnotice.php";
	public $m_dbConn;
	public $m_dbConnRoot;	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;		
		$this->m_dbConnRoot = new dbop(true);	
	}	
	public function getRenovationRequestDefaultValues()
	{
		$sql1 = "select `property`,`value` from appdefault_new where module_id = '2';";
		$sql1_res = $this->m_dbConn->select($sql1);
		$result = array();
		$result['Garbage Area'] = $sql1_res[0]['value'];
		//var_dump($sql1_res);
		if($sql1_res[1]['value'] <> "")
		{
			//echo "in if";
			$workListJson = $sql1_res[1]['value'];
		}
		else
		{
			$workListJson = "";
		}
		//var_dump($workListJson);
		$workList = json_decode($workListJson,true);
		//var_dump($workList);
		$result['List_Of_Work'] = $workList['WorkList'];
		$result['Renovation_template_header'] = $sql1_res[2]['value'];
		$result['Renovation_template_footer'] = $sql1_res[3]['value'];
		$result['Renovation_terms_conditions'] = $sql1_res[4]['value'];
		$result['LevelOfApprovalForRenovationRequest'] = $sql1_res[5]['value'];
		if($sql1_res[6]['value'] <> "")
		{
			$result['RenovationRequestCategoryId'] = $sql1_res[6]['value'];
		}
		else
		{
			$result['RenovationRequestCategoryId'] = 0;
		}
		$result['RenovationRequestThankYouNote'] = $sql1_res[7]['value'];
		return ($result);
	}
	public function getAddressProofDefaults()
	{
		$sql1 = "select `property`,`value` from appdefault_new where module_id = '4';";
		$sql1_res = $this->m_dbConn->select($sql1);
		$result = array();
		$result['AddressRequestCategoryId'] = $sql1_res[0]['value'];
		$result['LevelOfApprovalForAddressProofRequest'] = $sql1_res[1]['value'];
		$result['AddressProofThankyouNote'] = $sql1_res[2]['value'];
		$result['AddressProof_NOC_header'] = $sql1_res[3]['value'];
		$result['AddressProof_NOC_footer'] = $sql1_res[4]['value'];
		return ($result);
	}
	public function getTenantRequestDefaults()
	{
		$sql1 = "select `property`,`value` from appdefault_new where module_id = '3';";
		$sql1_res = $this->m_dbConn->select($sql1);
		$result = array();
		$result['TenantRequestCategoryId'] = $sql1_res[0]['value'];
		$result['LevelOfApprovalForTenantRequest'] = $sql1_res[1]['value'];
		$result['TenantRequestThankyouNote'] = $sql1_res[2]['value'];
		$result['Tenant_NOC_header'] = $sql1_res[3]['value'];
		$result['Tenant_NOC_footer'] = $sql1_res[4]['value'];
		return ($result);
	}
	public function getServiceRequestDefaults()
	{
		$renovationDefaults = $this->getRenovationRequestDefaultValues();
		$tenantRequestDefaults = $this->getTenantRequestDefaults();
		$addressproofRequestDefaults = $this->getAddressProofDefaults();
		$result = array();
		$result[0] = $renovationDefaults;
		$result[1] = $tenantRequestDefaults;
		$result[2] = $addressproofRequestDefaults;
		return($result);
	}
	public function updateRenovationDefaults($categoryId, $approvalLevel,$header,$footer, $termsCondition, $thankyouNote, $workList)
	{
		$header = htmlspecialchars_decode($header);
		$footer = htmlspecialchars_decode($footer);
		$termsCondition = htmlspecialchars_decode($termsCondition);
		$thankyouNote = htmlspecialchars_decode($thankyouNote);
		$oldRenovationDefaults = $this->getRenovationRequestDefaultValues();
		//var_dump($oldRenovationDefaults);
		if(array_key_exists("RenovationRequestCategoryId", $oldRenovationDefaults))
		{
			$sql1 = "Update appdefault_new set `value` = '".$categoryId."' where `property` = 'RenovationRequestCategoryId'";
			$result = $this->m_dbConn->update($sql1);
		}
		else
		{
			$sql1 = "INSERT INTO `appdefault_new` (`Property`, `Value`, `LoginID`, `module_id`) VALUES ('RenovationRequestCategoryId', '".$categoryId."', '".$_SESSION['login_id']."','2')";
			$result = $this->m_dbConn->insert($sql1);	
		}
		if(array_key_exists("LevelOfApprovalForRenovationRequest", $oldRenovationDefaults))
		{
			$sql1 = "Update appdefault_new set `value` = '".$approvalLevel."' where `property` = 'LevelOfApprovalForRenovationRequest'";
			$result = $this->m_dbConn->update($sql1);
		}
		else
		{
			$sql1 = "INSERT INTO `appdefault_new` (`Property`, `Value`, `LoginID`, `module_id`) VALUES ('LevelOfApprovalForRenovationRequest', '".$approvalLevel."', '".$_SESSION['login_id']."','2')";
			$result = $this->m_dbConn->insert($sql1);	
		}
		if(array_key_exists("Renovation_template_header", $oldRenovationDefaults))
		{
			$sql1 = "Update appdefault_new set `value` = '".$header."' where `property` = 'Renovation_template_header'";
			$result = $this->m_dbConn->update($sql1);
		}
		else
		{
			$sql1 = "INSERT INTO `appdefault_new` (`Property`, `Value`, `LoginID`, `module_id`) VALUES ('Renovation_template_header', '".$header."', '".$_SESSION['login_id']."','2')";
			$result = $this->m_dbConn->insert($sql1);	
		}
		if(array_key_exists("Renovation_template_footer", $oldRenovationDefaults))
		{
			$sql1 = "Update appdefault_new set `value` = '".$footer."' where `property` = 'Renovation_template_footer'";
			$result = $this->m_dbConn->update($sql1);
		}
		else
		{
			$sql1 = "INSERT INTO `appdefault_new` (`Property`, `Value`, `LoginID`, `module_id`) VALUES ('Renovation_template_footer', '".$footer."', '".$_SESSION['login_id']."','2')";
			$result = $this->m_dbConn->insert($sql1);	
		}
		if(array_key_exists("Renovation_terms_conditions", $oldRenovationDefaults))
		{
			$sql1 = "Update appdefault_new set `value` = '".$termsCondition."' where `property` = 'Renovation_terms_conditions'";
			$result = $this->m_dbConn->update($sql1);
		}
		else
		{
			$sql1 = "INSERT INTO `appdefault_new` (`Property`, `Value`, `LoginID`, `module_id`) VALUES ('Renovation_terms_conditions', '".$termsCondition."', '".$_SESSION['login_id']."','2')";
			$result = $this->m_dbConn->insert($sql1);	
		}
		if(array_key_exists("RenovationRequestThankYouNote", $oldRenovationDefaults))
		{
			$sql1 = "Update appdefault_new set `value` = '".$thankyouNote."' where `property` = 'RenovationRequestThankYouNote'";
			$result = $this->m_dbConn->update($sql1);
		}
		else
		{
			$sql1 = "INSERT INTO `appdefault_new` (`Property`, `Value`, `LoginID`, `module_id`) VALUES ('RenovationRequestThankYouNote', '".$thankyouNote."', '".$_SESSION['login_id']."','2')";
			$result = $this->m_dbConn->insert($sql1);	
		}
		$workListFinal = array();
		$workList = json_decode($workList);
		var_dump($workList);
		for($i = 0;$i < sizeof($workList);$i++)
		{
			$workListFinal['WorkList'][$i]['work'] = $workList[$i][0];
			$workListFinal['WorkList'][$i]['drawingReq'] = $workList[$i][1];
		}
		//var_dump($workListFinal);
		$workListFinal = json_encode($workListFinal);
		//var_dump($workListFinal);
		
		if(array_key_exists("List_Of_Work", $oldRenovationDefaults))
		{
			$sql1 = "Update appdefault_new set `value` = '".$workListFinal."' where `property` = 'List_Of_Work'";
			$result = $this->m_dbConn->update($sql1);
		}
		else
		{
			$sql1 = "INSERT INTO `appdefault_new` (`Property`, `Value`, `LoginID`, `module_id`) VALUES ('List_Of_Work', '".$workListFinal."', '".$_SESSION['login_id']."','2')";
			$result = $this->m_dbConn->insert($sql1);	
		}
		//if($oldRenovationDefaults[])
	}
	public function updateTenantDefaults($categoryId, $approvalLevel,$header,$footer,$thankyouNote)
	{
		$header = htmlspecialchars_decode($header);
		$footer = htmlspecialchars_decode($footer);
		$thankyouNote = htmlspecialchars_decode($thankyouNote);
		$oldTenantDefaults = $this->getTenantRequestDefaults();
		var_dump($oldTenantDefaults);
		if(array_key_exists("TenantRequestCategoryId", $oldTenantDefaults))
		{
			$sql1 = "Update appdefault_new set `value` = '".$categoryId."' where `property` = 'TenantRequestCategoryId'";
			$result = $this->m_dbConn->update($sql1);
		}
		else
		{
			$sql1 = "INSERT INTO `appdefault_new` (`Property`, `Value`, `LoginID`, `module_id`) VALUES ('TenantRequestCategoryId', '".$categoryId."', '".$_SESSION['login_id']."','3')";
			$result = $this->m_dbConn->insert($sql1);	
		}
		if(array_key_exists("LevelOfApprovalForTenantRequest", $oldTenantDefaults))
		{
			$sql1 = "Update appdefault_new set `value` = '".$approvalLevel."' where `property` = 'LevelOfApprovalForTenantRequest'";
			$result = $this->m_dbConn->update($sql1);
		}
		else
		{
			$sql1 = "INSERT INTO `appdefault_new` (`Property`, `Value`, `LoginID`, `module_id`) VALUES ('LevelOfApprovalForTenantRequest', '".$approvalLevel."', '".$_SESSION['login_id']."','3')";
			$result = $this->m_dbConn->insert($sql1);	
		}
		if(array_key_exists("Tenant_NOC_header", $oldTenantDefaults))
		{
			$sql1 = "Update appdefault_new set `value` = '".$header."' where `property` = 'Tenant_NOC_header'";
			$result = $this->m_dbConn->update($sql1);
		}
		else
		{
			$sql1 = "INSERT INTO `appdefault_new` (`Property`, `Value`, `LoginID`, `module_id`) VALUES ('Tenant_NOC_header', '".$header."', '".$_SESSION['login_id']."','3')";
			$result = $this->m_dbConn->insert($sql1);	
		}
		if(array_key_exists("Tenant_NOC_footer", $oldTenantDefaults))
		{
			$sql1 = "Update appdefault_new set `value` = '".$footer."' where `property` = 'Tenant_NOC_footer'";
			$result = $this->m_dbConn->update($sql1);
		}
		else
		{
			$sql1 = "INSERT INTO `appdefault_new` (`Property`, `Value`, `LoginID`, `module_id`) VALUES ('Tenant_NOC_footer', '".$footer."', '".$_SESSION['login_id']."','3')";
			$result = $this->m_dbConn->insert($sql1);	
		}
		if(array_key_exists("TenantRequestThankyouNote", $oldTenantDefaults))
		{
			$sql1 = "Update appdefault_new set `value` = '".$thankyouNote."' where `property` = 'TenantRequestThankyouNote'";
			$result = $this->m_dbConn->update($sql1);
		}
		else
		{
			$sql1 = "INSERT INTO `appdefault_new` (`Property`, `Value`, `LoginID`, `module_id`) VALUES ('TenantRequestThankyouNote', '".$thankyouNote."', '".$_SESSION['login_id']."','3')";
			$result = $this->m_dbConn->insert($sql1);	
		}
		
	}
	public function updateAddressProofDefaults($categoryId, $approvalLevel,$header,$footer,$thankyouNote)
	{
		$header = htmlspecialchars_decode($header);
		$footer = htmlspecialchars_decode($footer);
		$thankyouNote = htmlspecialchars_decode($thankyouNote);
		$oldAddressProofDefaults = $this->getAddressProofDefaults();
		//var_dump($oldAddressProofDefaults);
		if(array_key_exists("AddressRequestCategoryId", $oldAddressProofDefaults))
		{
			$sql1 = "Update appdefault_new set `value` = '".$categoryId."' where `property` = 'AddressRequestCategoryId'";
			$result = $this->m_dbConn->update($sql1);
		}
		else
		{
			$sql1 = "INSERT INTO `appdefault_new` (`Property`, `Value`, `LoginID`, `module_id`) VALUES ('AddressRequestCategoryId', '".$categoryId."', '".$_SESSION['login_id']."','4')";
			$result = $this->m_dbConn->insert($sql1);	
		}
		if(array_key_exists("LevelOfApprovalForAddressProofRequest", $oldAddressProofDefaults))
		{
			$sql1 = "Update appdefault_new set `value` = '".$approvalLevel."' where `property` = 'LevelOfApprovalForAddressProofRequest'";
			$result = $this->m_dbConn->update($sql1);
		}
		else
		{
			$sql1 = "INSERT INTO `appdefault_new` (`Property`, `Value`, `LoginID`, `module_id`) VALUES ('LevelOfApprovalForAddressProofRequest', '".$approvalLevel."', '".$_SESSION['login_id']."','4')";
			$result = $this->m_dbConn->insert($sql1);	
		}
		if(array_key_exists("AddressProof_NOC_header", $oldAddressProofDefaults))
		{
			$sql1 = "Update appdefault_new set `value` = '".$header."' where `property` = 'AddressProof_NOC_header'";
			$result = $this->m_dbConn->update($sql1);
		}
		else
		{
			$sql1 = "INSERT INTO `appdefault_new` (`Property`, `Value`, `LoginID`, `module_id`) VALUES ('AddressProof_NOC_header', '".$header."', '".$_SESSION['login_id']."','4')";
			$result = $this->m_dbConn->insert($sql1);	
		}
		if(array_key_exists("AddressProof_NOC_footer", $oldAddressProofDefaults))
		{
			$sql1 = "Update appdefault_new set `value` = '".$footer."' where `property` = 'AddressProof_NOC_footer'";
			$result = $this->m_dbConn->update($sql1);
		}
		else
		{
			$sql1 = "INSERT INTO `appdefault_new` (`Property`, `Value`, `LoginID`, `module_id`) VALUES ('AddressProof_NOC_footer', '".$footer."', '".$_SESSION['login_id']."','4')";
			$result = $this->m_dbConn->insert($sql1);	
		}
		if(array_key_exists("AddressProofThankyouNote", $oldAddressProofDefaults))
		{
			$sql1 = "Update appdefault_new set `value` = '".$thankyouNote."' where `property` = 'AddressProofThankyouNote'";
			$result = $this->m_dbConn->update($sql1);
		}
		else
		{
			$sql1 = "INSERT INTO `appdefault_new` (`Property`, `Value`, `LoginID`, `module_id`) VALUES ('AddressProofThankyouNote', '".$thankyouNote."', '".$_SESSION['login_id']."','4')";
			$result = $this->m_dbConn->insert($sql1);	
		}
		
	}
}
?>
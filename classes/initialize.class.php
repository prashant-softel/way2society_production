<?php

include_once('dbconst.class.php');
include_once('include/config.php');
include_once('utility.class.php');
include_once("include/dbop.class.php");

class initialize
{
	public $m_dbConn;
	public $dbConn;
	public $m_objUtility;
	public $bIsNewUserConnectedViaStoredProcedure;
	public $m_objDBRoot;
	public $NewUserConnectedViaStoredProcedureDetails;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->m_objDBRoot = new dbop(true);
		$this->dbConn = new dbop();
		$this->m_objUtility = new utility($this->dbConn, $this->m_objDBRoot);
		$this->bIsNewUserConnectedViaStoredProcedure = false;
		$this->NewUserConnectedViaStoredProcedureDetails = array();
		
	}

	public function combobox($query, $id, $defaultText = 'Please Select', $defaultValue = '0')
	{
		$str = '';
		
		if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "'>" . $defaultText . "</option>";
		}
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
						if($id==$v)
						{
							$sel = 'selected';	
						}
						else
						{
							$sel = '';
						}
						
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
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
	
	public function filtercombobox($query, $id, $defaultText = 'Please Select', $defaultValue = '0')
	{
		$str = '';
		
		if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "'>" . $defaultText . "</option>";
		}
		
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
						if($id==$v)
						{
							$sel = 'selected';	
						}
						else
						{
							$sel = '';
						}
						
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
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
	public function getMapCount($login_id)
	{
		$sql = "select Count(login_id) as Cnt from mapping where login_id='" . $login_id . "'";
		$result = $this->m_dbConn->select($sql);
		
		return $result[0]['Cnt'];
	}
	
	public function getDBName($mapid)
	{
		$sql = "Select maptbl.society_id, dbtbl.dbname from dbname as dbtbl JOIN mapping as maptbl ON maptbl.society_id = dbtbl.society_id where maptbl.id = '" . $mapid . "'";
		$result = $this->m_dbConn->select($sql);
		return $result[0]['dbname'];
	}
	
	public function getSocietyID($dbName)
	{
		$sql = "Select society_id from dbname where dbname = '" . $dbName . "'";
		$result = $this->m_dbConn->select($sql);
		return $result[0]['society_id'];
	}
	
	public function getMapDetails($mapid)
	{
		$sql = "Select maptbl.society_id, maptbl.unit_id, maptbl.role, maptbl.view, maptbl.profile, maptbl.code, maptbl.desc, maptbl.login_id, maptbl.status, dbtbl.dbname,societytbl.client_id,societytbl.security_dbname from dbname as dbtbl JOIN mapping as maptbl ON maptbl.society_id = dbtbl.society_id  JOIN `society` as societytbl ON dbtbl.society_id  = societytbl.society_id where maptbl.id = '" . $mapid . "'";
		//echo $sql;
		$result = $this->m_dbConn->select($sql);
		
		return $result;
	}
	
	
	public function setProfile($profileID)
	{
		//$profileSuperAdmin = array(PROFILE_GENERATE_BILL => 1, PROFILE_CHEQUE_ENTRY => 1, PROFILE_PAYMENTS => 1, PROFILE_MANAGE_MASTER => 1, PROFILE_UPDATE_INTEREST => 1, PROFILE_REVERSE_CHARGE => 1, PROFILE_BANK_RECO => 1);
		
		//$profileAdmin = array(PROFILE_GENERATE_BILL => 1, PROFILE_CHEQUE_ENTRY => 1, PROFILE_PAYMENTS => 1, PROFILE_MANAGE_MASTER => 1, PROFILE_UPDATE_INTEREST => 1, PROFILE_REVERSE_CHARGE => 1, PROFILE_BANK_RECO => 1);
		
		//$profileAdminMember = array(PROFILE_GENERATE_BILL => 0, PROFILE_CHEQUE_ENTRY => 1, PROFILE_PAYMENTS => 1, PROFILE_MANAGE_MASTER => 0, PROFILE_UPDATE_INTEREST => 0, PROFILE_REVERSE_CHARGE => 0);
		
		/*if($_SESSION['role'] == ROLE_SUPER_ADMIN)
		{
			$_SESSION['profile'] = $profileSuperAdmin;
		}
		else if($_SESSION['role'] == ROLE_ADMIN)
		{
			$_SESSION['profile'] = $profileAdmin;
		}
		else if($_SESSION['role'] == ROLE_ADMIN_MEMBER)
		{
			$_SESSION['profile'] = $profileAdminMember;
		}*/
		
		$this->resetProfile();
		
		$sql = "Select * from profile where id = '" . $profileID . "'";
		$result = $this->m_dbConn->select($sql);
		
		if($result <> '')
		{
			$_SESSION['profile'][PROFILE_GENERATE_BILL] = $result[0]['PROFILE_GENERATE_BILL'];
			$_SESSION['profile'][PROFILE_CREATE_INVOICE] = $result[0]['PROFILE_CREATE_INVOICE'];
			$_SESSION['profile'][PROFILE_EDIT_BILL] = $result[0]['PROFILE_EDIT_BILL'];
			$_SESSION['profile'][PROFILE_CHEQUE_ENTRY] = $result[0]['PROFILE_CHEQUE_ENTRY'];
			$_SESSION['profile'][PROFILE_PAYMENTS] = $result[0]['PROFILE_PAYMENTS'];
			$_SESSION['profile'][PROFILE_UPDATE_INTEREST] = $result[0]['PROFILE_UPDATE_INTEREST'];
			$_SESSION['profile'][PROFILE_MANAGE_MASTER] = $result[0]['PROFILE_MANAGE_MASTER'];
			$_SESSION['profile'][PROFILE_BANK_RECO] = $result[0]['PROFILE_BANK_RECO'];
			$_SESSION['profile'][PROFILE_REVERSE_CHARGE] = $result[0]['PROFILE_REVERSE_CHARGE'];
			$_SESSION['profile'][PROFILE_SEND_NOTIFICATION] = $result[0]['PROFILE_SEND_NOTIFICATION'];
			$_SESSION['profile'][PROFILE_NEFT_DETAILS] = $result[0]['PROFILE_CHEQUE_ENTRY'];
			$_SESSION['profile'][PROFILE_VOUCHER_EDIT] = $result[0]['PROFILE_PAYMENTS'];
			$_SESSION['profile'][PROFILE_CREATE_VOUCHER] = $result[0]['PROFILE_PAYMENTS'];
			$_SESSION['profile'][PROFILE_DEPOSITGROUP] = $result[0]['PROFILE_CHEQUE_ENTRY'];
			$_SESSION['profile'][PROFILE_CHEQUELEAFBOOK] = $result[0]['PROFILE_PAYMENTS'];
			$_SESSION['profile'][PROFILE_EDIT_MEMBER] = $result[0]['PROFILE_EDIT_MEMBER'];
			
			$_SESSION['profile'][PROFILE_SEND_NOTICE] = $result[0]['PROFILE_SEND_NOTICE'];
			$_SESSION['profile'][PROFILE_SEND_EVENT] = $result[0]['PROFILE_SEND_EVENT'];
			$_SESSION['profile'][PROFILE_CREATE_ALBUM] = $result[0]['PROFILE_CREATE_ALBUM'];
			$_SESSION['profile'][PROFILE_CREATE_POLL] = $result[0]['PROFILE_CREATE_POLL'];
			$_SESSION['profile'][PROFILE_APPROVALS_LEASE] = $result[0]['PROFILE_APPROVALS_LEASE'];
			$_SESSION['profile'][PROFILE_SERVICE_PROVIDER] = $result[0]['PROFILE_SERVICE_PROVIDER'];
			$_SESSION['profile'][PROFILE_PHOTO] = $result[0]['PROFILE_PHOTO'];
			$_SESSION['profile'][PROFILE_MESSAGE] = $result[0]['PROFILE_MESSAGE'];
			$_SESSION['profile'][PROFILE_MANAGE_LIEN] = $result[0]['PROFILE_MANAGE_LIEN'];
			$_SESSION['profile'][PROFILE_USER_MANAGEMENT] = $result[0]['PROFILE_USER_MANAGEMENT'];
			$_SESSION['profile'][PROFILE_CLASSIFIED] = $result[0]['PROFILE_CLASSIFIED'];
			$_SESSION['profile'][PROFILE_VENDOR_MANAGEMENT] = $result[0]['PROFILE_VENDOR_MANAGEMENT'];
		}
	}
	
	public function resetProfile()
	{
		$_SESSION['profile'][PROFILE_GENERATE_BILL] = 0;
		$_SESSION['profile'][PROFILE_CREATE_INVOICE] = 0;
		$_SESSION['profile'][PROFILE_EDIT_BILL] = 0;
		$_SESSION['profile'][PROFILE_CHEQUE_ENTRY] = 0;
		$_SESSION['profile'][PROFILE_PAYMENTS] = 0;
		$_SESSION['profile'][PROFILE_UPDATE_INTEREST] = 0;
		$_SESSION['profile'][PROFILE_MANAGE_MASTER] = 0;
		$_SESSION['profile'][PROFILE_BANK_RECO] = 0;
		$_SESSION['profile'][PROFILE_REVERSE_CHARGE] = 0;
		$_SESSION['profile'][PROFILE_SEND_NOTIFICATION] = 0;
		$_SESSION['profile'][PROFILE_NEFT_DETAILS] = 0;
		$_SESSION['profile'][PROFILE_VOUCHER_EDIT] = 0;
		$_SESSION['profile'][PROFILE_CREATE_VOUCHER] = 0;
		$_SESSION['profile'][PROFILE_DEPOSITGROUP] = 0;
		$_SESSION['profile'][PROFILE_CHEQUELEAFBOOK] = 0;
		$_SESSION['profile'][PROFILE_EDIT_MEMBER] = 0;
		
		$_SESSION['profile'][PROFILE_SEND_NOTICE] = 0 ;
		$_SESSION['profile'][PROFILE_SEND_EVENT] = 0 ;
		$_SESSION['profile'][PROFILE_CREATE_ALBUM] = 0 ;
		$_SESSION['profile'][PROFILE_CREATE_POLL] = 0 ;
		$_SESSION['profile'][PROFILE_APPROVALS_LEASE] = 0 ;
		$_SESSION['profile'][PROFILE_SERVICE_PROVIDER] = 0;
		$_SESSION['profile'][PROFILE_PHOTO] = 0;
		$_SESSION['profile'][PROFILE_MESSAGE] = 0;
		$_SESSION['profile'][PROFILE_MANAGE_LIEN] = 0;
		$_SESSION['profile'][PROFILE_USER_MANAGEMENT] = 0;
		$_SESSION['profile'][PROFILE_CLASSIFIED] = 0;
		$_SESSION['profile'][PROFILE_VENDOR_MANAGEMENT] = 0;
	}

	public function getProfile($profileID)
	{
		$sql = "Select * from profile where id = '" . $profileID . "'";
		$result = $this->m_dbConn->select($sql);
		
		$profile = array();
		
		if($result <> '')
		{
			$profile[PROFILE_GENERATE_BILL] = $result[0]['PROFILE_GENERATE_BILL'];
			$profile[PROFILE_CREATE_INVOICE] = $result[0]['PROFILE_CREATE_INVOICE'];
			$profile[PROFILE_EDIT_BILL] = $result[0]['PROFILE_EDIT_BILL'];
			$profile[PROFILE_CHEQUE_ENTRY] = $result[0]['PROFILE_CHEQUE_ENTRY'];
			$profile[PROFILE_PAYMENTS] = $result[0]['PROFILE_PAYMENTS'];
			$profile[PROFILE_UPDATE_INTEREST] = $result[0]['PROFILE_UPDATE_INTEREST'];
			$profile[PROFILE_MANAGE_MASTER] = $result[0]['PROFILE_MANAGE_MASTER'];
			$profile[PROFILE_BANK_RECO] = $result[0]['PROFILE_BANK_RECO'];
			$profile[PROFILE_REVERSE_CHARGE] = $result[0]['PROFILE_REVERSE_CHARGE'];
			$profile[PROFILE_SEND_NOTIFICATION] = $result[0]['PROFILE_SEND_NOTIFICATION'];
			$profile[PROFILE_EDIT_MEMBER] = $result[0]['PROFILE_EDIT_MEMBER'];	
			$profile[PROFILE_SEND_NOTICE] = $result[0]['PROFILE_SEND_NOTICE'];
			$profile[PROFILE_SEND_EVENT] = $result[0]['PROFILE_SEND_EVENT'];
			$profile[PROFILE_CREATE_ALBUM] = $result[0]['PROFILE_CREATE_ALBUM'];
			$profile[PROFILE_CREATE_POLL] = $result[0]['PROFILE_CREATE_POLL'];
			$profile[PROFILE_APPROVALS_LEASE] = $result[0]['PROFILE_APPROVALS_LEASE'];
			$profile[PROFILE_SERVICE_PROVIDER] = $result[0]['PROFILE_SERVICE_PROVIDER'];
			$profile[PROFILE_PHOTO] = $result[0]['PROFILE_PHOTO'];
			$profile[PROFILE_MESSAGE] = $result[0]['PROFILE_MESSAGE'];
			$profile[PROFILE_MANAGE_LIEN] = $result[0]['PROFILE_MANAGE_LIEN'];
			$profile[PROFILE_USER_MANAGEMENT] = $result[0]['PROFILE_USER_MANAGEMENT'];
			
			$profile[PROFILE_CLASSIFIED] = $result[0]['PROFILE_CLASSIFIED'];
			$profile[PROFILE_APPROVAL_RENOVATION_REQUEST] = $result[0]['PROFILE_APPROVAL_OF_RENOVATION_REQUEST'];
			$profile[PROFILE_VERIFICATION_RENOVATION_REQUEST] = $result[0]['PROFILE_VERIFICATION_OF_RENOVATION_REQUEST'];
			$profile[PROFILE_APPROVAL_NOC] = $result[0]['PROFILE_APPROVAL_OF_NOC'];
			$profile[PROFILE_VENDOR_MANAGEMENT] = $result[0]['PROFILE_VENDOR_MANAGEMENT'];
			
		}
		
		return $profile;
	}
	
	public function setCurrentMapping($mapID)
	{
		$sql = "UPDATE login SET current_mapping = '" . $mapID . "' WHERE login_id = '" . $_SESSION['login_id'] . "'";	
		$result = $this->m_dbConn->update($sql);
	}
	
	public function verifyCode($code)
	{
		$sql = "Select id, society_id, unit_id, `desc`, role, status from mapping where code = '"	. $code . "'";
		$result = $this->m_dbConn->select($sql);
		
		return $result;
	}
	
	public function setLoginIDToMap($mapID, $status = 0)
	{
		$sql = "Update mapping SET login_id = '" . $_SESSION['login_id'] . "' where id = '" . $mapID . "'";
		if($status <> 0)
		{
			$sql = "Update mapping SET login_id = '" . $_SESSION['login_id'] . "', status = '" . $status . "' where id = '" . $mapID . "'";
		}
		$result = $this->m_dbConn->update($sql);
		
		$this->setCurrentMapping($mapID);
	}
	
	public function addUser($name, $id, $pass, $fbid = '', $bSetSession = false, $sCode = '', $mob)
	{
		$this->NewUserConnectedViaStoredProcedureDetails = array();
		$result = -1;
		if($fbid == '' || $fbid == 0)
		{
			$sqlCheck = "select count(member_id) as cnt from login where member_id = '" . $id . "'";
		}
		else
		{
			$sqlCheck = "select count(fbcode) as cnt from login where fbcode = '" . $fbid . "'";
		}
		
		$resultCheck = $this->m_dbConn->select($sqlCheck);
		
		if($resultCheck[0]['cnt'] <= 0)
		{
			$sql = "INSERT INTO `login`(`member_id`, `password`, `name`, `fbcode`,`mobile_number`) VALUES ('" . $id . "', '" . $pass . "', '" .$this->m_dbConn->escapeString($name). "', '" .  $fbid . "', '" .  $mob . "')";
			$result = $this->m_dbConn->insert($sql);
			
			if($bSetSession == true && $result > 0)
			{
				$_SESSION['login_id'] = $result;
				$_SESSION['current_mapping'] = 0;
				$_SESSION['society_id'] = 0;
				$_SESSION['authority'] = '';
				$_SESSION['name'] = $name;
			}
			
			if($result > 0 && $sCode <> '')
			{
				$societyiD = 0;
				$unitiD = 0;
				$societyName ="";
				$unitNumber = '';
				
				$sqlMap = "Select maptbl.id,maptbl.society_id,maptbl.`desc`,unit_id,societytbl.society_name from mapping as maptbl JOIN `society` as societytbl on maptbl.society_id = societytbl.society_id where code = '" . $sCode . "' and maptbl.status = '1' ";
				$resultMap2 = $this->m_dbConn->select($sqlMap);
				
				$societyiD = $resultMap2[0]['society_id'];
				$societyName = $resultMap2[0]['society_name'];
				$unitiD = $resultMap2[0]['unit_id'];
				$unitNumberByCode = $resultMap2[0]['desc'];
				
				if($resultMap2 <> '')
				{
					$sqlUpdateMap = "Update mapping SET login_id = '" . $result . "', status = 2 where id = '" . $resultMap2[0]['id'] ."'";
					$updateMap = $this->m_dbConn->update($sqlUpdateMap);
					if(sizeof($updateMap) > 0)
					{
						$this->bIsNewUserConnectedViaStoredProcedure = true;
					}
					array_push($this->NewUserConnectedViaStoredProcedureDetails,array("society_name" => $societyName , "unit_no" => $unitNumberByCode));
				}
				
				$memberArray = $this->checkIfEmailAlreadyExists($id);
				if(sizeof($memberArray) > 0 )
				{
					$this->bIsNewUserConnectedViaStoredProcedure = true;
											
					for($i = 0;$i < sizeof($memberArray) ; $i++)
					{
						if($memberArray[$i]['unit'] == $unitiD && $memberArray[$i]['society_id'] == $societyiD)
						{
							$unitNumber = $memberArray[$i]['unit_no'];
						}
						//$sqlMap = "Select id from mapping where society_id = '" . $memberArray[$i]['society_id'] . "' and  unit_id = '" . $memberArray[$i]['unit'] . "' and status = '1'";
						$sqlMap = "Select maptbl.id,societytbl.society_name from mapping as maptbl JOIN `society` as societytbl on maptbl.society_id = societytbl.society_id where maptbl.society_id = '" . $memberArray[$i]['society_id'] . "' and maptbl.society_id <> '" .$societyiD. "' and  unit_id = '" . $memberArray[$i]['unit'] . "' and  unit_id <> '" . $unitiD . "' and maptbl.status = '1' ";
						$resultMap = $this->m_dbConn->select($sqlMap);
						
						if($resultMap <> '')
						{
							//add connected society details to common array
							array_push($this->NewUserConnectedViaStoredProcedureDetails,array("society_name" => $resultMap[0]['society_name'] , "unit_no" => $memberArray[$i]['unit_no']));
							$sqlUpdateMap = "Update mapping SET login_id = '" . $result . "', status = 2 where id = '" . $resultMap[0]['id'] ."'";
							$updateMap = $this->m_dbConn->update($sqlUpdateMap);
						}
						else if($unitNumber == "")
						{
							$insert_mapping = "INSERT INTO `mapping`(`login_id`,`society_id`, `unit_id`, `desc`, `code`, `role`, `created_by`, `view`,`status`) VALUES ('" . $result . "','" . $memberArray[$i]['society_id'] . "', '" . $memberArray[$i]['unit'] . "', '" . $memberArray[$i]['unit_no'] . "', '" . getRandomUniqueCode() . "', '" . ROLE_MEMBER . "', '" . $result . "', 'MEMBER','2')";
							$result_mapping = $this->m_dbConn->insert($insert_mapping);
							
							if($result_mapping  <> "" && $result_mapping  > 0)
							{
								$sqlMapNew = "Select maptbl.id,societytbl.society_name from mapping as maptbl JOIN `society` as societytbl on maptbl.society_id = societytbl.society_id where  maptbl.id = '" . $result_mapping. "' ";
								$resultMapNew = $this->m_dbConn->select($sqlMapNew);
								if($resultMapNew <> '')
								{
									//add connected society details to common array
									array_push($this->NewUserConnectedViaStoredProcedureDetails,array("society_name" => $resultMapNew[0]['society_name'] , "unit_no" => $memberArray[$i]['unit_no']));
								}
							}
						}		
					}
						
				}
			}
			else if($result > 0 && $id <> "" &&   isValidEmailID($this->m_dbConn->escapeString($id)) == true)
			{
				$memberArray = $this->checkIfEmailAlreadyExists($id);
				if(sizeof($memberArray) > 0 )
				{
						$this->bIsNewUserConnectedViaStoredProcedure = true;
												
						for($i = 0;$i < sizeof($memberArray) ; $i++)
						{
							//$sqlMap = "Select id from mapping where society_id = '" . $memberArray[$i]['society_id'] . "' and  unit_id = '" . $memberArray[$i]['unit'] . "' and status = '1'";
							$sqlMap = "Select maptbl.id,societytbl.society_name from mapping as maptbl JOIN `society` as societytbl on maptbl.society_id = societytbl.society_id where maptbl.society_id = '" . $memberArray[$i]['society_id'] . "' and  unit_id = '" . $memberArray[$i]['unit'] . "' and maptbl.status = '1' ";
							$resultMap = $this->m_dbConn->select($sqlMap);
							
							if($resultMap <> '')
							{
								//add connected society details to common array
								array_push($this->NewUserConnectedViaStoredProcedureDetails,array("society_name" => $resultMap[0]['society_name'] , "unit_no" => $memberArray[$i]['unit_no']));
								$sqlUpdateMap = "Update mapping SET login_id = '" . $result . "', status = 2 where id = '" . $resultMap[0]['id'] ."'";
								$updateMap = $this->m_dbConn->update($sqlUpdateMap);
							}
							else
							{
									$insert_mapping = "INSERT INTO `mapping`(`login_id`,`society_id`, `unit_id`, `desc`, `code`, `role`, `created_by`, `view`,`status`) VALUES ('" . $result . "','" . $memberArray[$i]['society_id'] . "', '" . $memberArray[$i]['unit'] . "', '" . $memberArray[$i]['unit_no'] . "', '" . getRandomUniqueCode() . "', '" . ROLE_MEMBER . "', '" . $result . "', 'MEMBER','2')";
									$result_mapping = $this->m_dbConn->insert($insert_mapping);
									
									if($result_mapping  <> "" && $result_mapping  > 0)
									{
										$sqlMapNew = "Select maptbl.id,societytbl.society_name from mapping as maptbl JOIN `society` as societytbl on maptbl.society_id = societytbl.society_id where  maptbl.id = '" . $result_mapping. "' ";
										$resultMapNew = $this->m_dbConn->select($sqlMapNew);
										if($resultMapNew <> '')
										{
											//add connected society details to common array
											array_push($this->NewUserConnectedViaStoredProcedureDetails,array("society_name" => $resultMapNew[0]['society_name'] , "unit_no" => $memberArray[$i]['unit_no']));
										}
									}
							}		
						}
											
						if(isset($_REQUEST['url']) || isset($_REQUEST['URL']))
						{
							$Url = $_REQUEST['url'];
							if(!empty($_REQUEST['URL']))
							{
								$Url = $_REQUEST['URL'];
							}
						?>
                        		<script>window.location.href = "initialize.php?url=" + "<?php echo $Url?>";</script>
						<?php
						}
						else
						{
							?>
                        	<script>window.location.href = "initialize.php";</script>	
						<?php
						}
				}
			}
			
			/*if($result > 0 && $sCode == '')
			{
				//$sqlII = "SELECT `member_id`,`mobile_number`,`name`,`fbcode` FROM `login` where `login_id` = ".$result;
				//$loginData = $this->m_dbConn->select($sqlII);
				
				if(sizeof($loginData) > 0)
				{
					//send email to support team for assigning society to registered member
					//$res = $this->sendEmail($loginData,false);
				}	
			}*/
		}
		return $result;
	}
	
	public function getModuleAccess()
	{
		$query = "Select * from module where society_id = '" . $_SESSION['society_id'] . "'";
		$result = $this->m_dbConn->select($query);
		
		if(!$result)
		{
			$result = $this->setModuleAccess();
		}
		
		$_SESSION['module']['service_request'] = $result[0]['service_request'];
		$_SESSION['module']['notice'] = $result[0]['notice'];
		$_SESSION['module']['event'] = $result[0]['event'];
		$_SESSION['module']['document'] = $result[0]['document'];						
		$_SESSION['module']['service'] = $result[0]['service'];						
		$_SESSION['module']['classified'] = $result[0]['classified'];						
		$_SESSION['module']['forum'] = $result[0]['forum'];						
		$_SESSION['module']['directory'] = $result[0]['directory'];						
	}
	
	public function setModuleAccess()
	{
		$query = "Insert into module (`society_id`) values ('" . $_SESSION['society_id'] . "') ";
		$result = $this->m_dbConn->insert($query);
		
		$response = array();
		$response[0]['service_request'] = 1;
		$response[0]['notice'] = 1;
		$response[0]['event'] = 1;
		$response[0]['document'] = 1;
		$response[0]['service'] = 1;
		$response[0]['classified'] = 1;
		$response[0]['forum'] = 1;
		$response[0]['directory'] = 1;
		
		return $response;
	}
	
	public function updateModuleAccess($sr_req, $notice, $event, $document, $service, $classified, $forum, $directory)
	{
		$query = "UPDATE `module` SET `service_request`= '" . $sr_req . "', `notice`= '" . $notice . "', `event`= '" . $event . "', `document`= '" . $document . "', `service`= '" . $service . "', `classified`= '" . $classified . "', `forum`= '" . $forum . "', `directory`= '" . $directory . "' WHERE `society_id` = '" . $_SESSION['society_id'] . "'";
		$result = $this->m_dbConn->update($query);
	}
	
	private function getUserIP() 
	{		
		if( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) 
		{
			if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')>0) {
				$addr = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
				return trim($addr[0]);
			} 
			else 
			{
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
		}
		else 
		{
			return $_SERVER['REMOTE_ADDR'];
		}
	}
	
	public function set_DB_Version()
	{
		//First We Check version of current Database
		$_SESSION['DB_Schema_Ver'] = $this->m_objUtility->GetDB_Schema_Ver();
		
		if($_SESSION['DB_Schema_Ver'] < 201)
		{
			$this->m_objUtility->UpdateTableForCounter();
			$this->dbConn->update("UPDATE society SET DB_Schema_Ver = 201 WHERE society_id = '".$_SESSION['society_id']."'");
			$_SESSION['DB_Schema_Ver'] = 201;
		}
		
		/*if($_SESSION['DB_Schema_Ver'] < 202)
		{
			
			$this->m_objUtility->SwapVoucherTableByAndToEntry();
			$this->m_dbConn->update("UPDATE society SET DB_Schema_Ver = 202 WHERE society_id = '".$_SESSION['society_id']."'");
			$_SESSION['DB_Schema_Ver'] = 202;
		}*/
		
		//Add if block for next db version change
	}
	
	
	public function saveLoginDetails()
	{
		$locdetails = json_decode(file_get_contents("https://ipinfo.io/" . $this->getUserIP() . "/json"), true);	
		$mMysqli = mysqli_connect(DB_HOST_ROOT, DB_USER_ROOT, DB_PASSWORD_ROOT, DB_DATABASE_ROOT);				
		
		$aryTimeStamp = getCurrentTimeStamp();
		
		$sqlInsert = 'INSERT INTO `LoginTrackingLog`(`UserId`, `MappingID`, `IP`, `Hostname`, `City`, `Region`, `Country`, `Location`, `ISP`, `Postal_Code`, `Timestamp`) VALUES ("'.$_SESSION['login_id'].'","'.$_SESSION['current_mapping'].'","'.$locdetails['ip'].'","'.$locdetails['hostname'].'","'.$locdetails['city'].'","'.$locdetails['region'].'","'.$locdetails['country'].'","'.$locdetails['loc'].'","'.$locdetails['org'].'","'.$locdetails['postal'].'", "' . $aryTimeStamp['DateTime'] . '")';				
		if($mMysqli)
		{
			$result = $mMysqli->query($sqlInsert);
			mysqli_close($mMysqli);	
		}									
		//$data = $this->m_dbConn->insert($sqlInsert);		
	}		
	public function sendEmail($loginData,$bIsSendSocistyDetails = false,$data = "")
	{
		include_once('email_format.class.php');
		require_once('swift/swift_required.php');
		
		try
	  {
		  /* $transport = Swift_SmtpTransport::newInstance('103.50.162.146', 465, "ssl")
								->setUsername('no-reply14@way2society.com')
								->setSourceIp('0.0.0.0')
								->setPassword('society123') ; */
								
			$EMailIDToUse = $this->m_objUtility->GetEmailIDToUse(false, 0, 0, 0, 0, 0, 0, 0, 0);
			
			$EMailID = "";
			$Password = "";
					
			if(isset($EMailIDToUse) && $EMailIDToUse['status'] == 0)
			{
					$EMailID = $EMailIDToUse['email'];
					$Password = $EMailIDToUse['password'];
			}	
			
			//$transport = Swift_SmtpTransport::newInstance('103.50.162.146', 465, "ssl")
			$transport = Swift_SmtpTransport::newInstance('103.50.162.146',587)
									->setUsername($EMailID)
									->setSourceIp('0.0.0.0')
									->setPassword($Password); 					
								
																						
			//Create Email Body
			$mailBody = "Hi ,Techsupport Team<br><br>";
			$mailBody .= "A new user has registered on the www.way2society.com.<br>";
			$mailBody .= "Account details are as follows:<br><br>";
			
			$EmailSubject = "New user ";
			
			if($loginData[0]['fbcode']  == "")
			{
				$mailBody .= "<table align='center' border='1'  style='border-collapse: collapse;'><tr><th style='padding: 5px 5px 5px 5px;'>Name</th><th style='padding: 5px 5px 5px 5px;'>Email ID</th><th  style='padding: 5px 5px 5px 5px;'>Mobile Number</th></tr>
        							<tr><td style='padding: 5px 5px 5px 5px;'>".$loginData[0]['name']."</td><td  style='padding: 5px 5px 5px 5px;'>".$loginData[0]['member_id']."</td><td  style='padding: 5px 5px 5px 5px;'>".$loginData[0]['mobile_number']."</td></tr></table>";
			}
			else
			{
				$mailBody .= "User has registered via fconnect:";
				$mailBody .= "<table  align='center' border='1'  style='border-collapse: collapse;'><tr><th style='padding: 5px 5px 5px 5px;'>Name</th><th style='padding: 5px 5px 5px 5px;'>Email ID / Mobile Number</th></tr>
        							<tr><td  style='padding: 5px 5px 5px 5px;'>".$loginData[0]['name']."</td><td  style='padding: 5px 5px 5px 5px;'>".$loginData[0]['member_id']."</td></tr></table>";
			}
			
			if($bIsSendSocistyDetails == true && $data <> "" && count($data) > 0)
			{
				$mailBody .= "<br><br>Society details are as follows:<br><br>";
				$mailBody .= "<table  align='center' border='1'  style='border-collapse: collapse;'><tr><th style='padding: 5px 5px 5px 5px;'>Society Name</th><th style='padding: 5px 5px 5px 5px;'>Unit/Flat/Shop No</th><th style='padding: 5px 5px 5px 5px;'>Contact No</th></tr>
        							<tr><td  style='padding: 5px 5px 5px 5px;'>".$data['society_name']."</td><td  style='padding: 5px 5px 5px 5px;'>".$data['unit']."</td><td  style='padding: 5px 5px 5px 5px;'>".$data['wing']."</td></tr></table>";	
				$EmailSubject = "New user : " .$data['society_name'] . "-".$data['name']." (".$data['wing'] ."-".$data['unit'] .")" ;
			}
			
			$array = json_decode(file_get_contents("http://ipinfo.io/" . $this->getUserIP() . "/json"), true);	
			
			//send ip details of user
			$mailBody .= '<br><br>Additional Information :<br><table border="1" cellpadding="10" style="border-collapse: collapse;" align="center">';
			 foreach($array as $key => $value)
		   {
				$mailBody .='<tr><td style="padding: 5px 5px 5px 5px;">'.$key.'</td><td style="padding: 5px 5px 5px 5px;">'.$value.'</td></tr>';
		   }
			$mailBody .='</table><br><br>';
			
			if($this->bIsNewUserConnectedViaStoredProcedure == true)
			{
				  //sending connected society details
					if(sizeof($this->NewUserConnectedViaStoredProcedureDetails) > 0)
					{
						$mailBody .= '<br><br><font  color="#00CC00"  size="12px;">Successfully connected to :</font><br><table border="1" cellpadding="10" style="border-collapse: collapse;" align="center">';
						$mailBody .='<tr><td style="padding: 5px 5px 5px 5px;">Society Name</td><td style="padding: 5px 5px 5px 5px;">Unit No</td></tr>';
						
						for($i = 0; $i < sizeof($this->NewUserConnectedViaStoredProcedureDetails); $i++)
					   {
							$mailBody .='<tr><td style="padding: 5px 5px 5px 5px;">'.$this->NewUserConnectedViaStoredProcedureDetails[$i]["society_name"].'</td><td style="padding: 5px 5px 5px 5px;">'.$this->NewUserConnectedViaStoredProcedureDetails[$i]["unit_no"].'</td></tr>';
					   }
					  $mailBody .='</table><br><br>';
					}
					if(sizeof($this->NewUserConnectedViaStoredProcedureDetails) > 1)
					{
						$size = sizeof($this->NewUserConnectedViaStoredProcedureDetails) - 1;
						$EmailSubject .= "Activated : ".$this->NewUserConnectedViaStoredProcedureDetails[0]["society_name"] ." (" .$this->NewUserConnectedViaStoredProcedureDetails[0]["unit_no"] .") + ".$size . " ";
					}
					else
					{
						$EmailSubject .= "Activated : ".$this->NewUserConnectedViaStoredProcedureDetails[0]["society_name"] ." - ".$this->NewUserConnectedViaStoredProcedureDetails[0]["unit_no"];
					}
					//$mailBody .='<br><br><font  color="#00CC00" size="14px;">User has been successfully connected.</font>';
			}
					
			if($bIsSendSocistyDetails == false)
			{
				//sending welcome email to new member
				$memberMailSubject = "Welcome,".$loginData[0]['name']."!"; 																			
				
				//$memberMailBody = GetEmailHeader();
				$memberMailBody = "Hi ".$loginData[0]['name']."<br>Welcome to way2society.<br><br>";
				$memberMailBody .=" 	Here's your account information:<br>
													Name: ".$loginData[0]['name']."<br>
													Username: ".$loginData[0]['member_id']."<br>";
				
				// Send the email
				$result1 = false;
				if($loginData[0]['member_id'] <> ""  && isValidEmailID($this->m_dbConn->escapeString($loginData[0]['member_id'])) == true)
				{
					//$result1 = $this->sendSwiftMessage($transport,$memberMailSubject,$memberMailBody,$loginData[0]['member_id'] , $loginData[0]['name']);
					$result1 = $this->sendSwiftMessage($transport,$memberMailSubject,$memberMailBody,$loginData[0]['member_id'] , $loginData[0]['name']);
				}
			}
			
			// Send the email
			$result2 =  $this->sendSwiftMessage($transport, $EmailSubject ,$mailBody, 'techsupport@way2society.com','Techsupport');
			//$result2 =  $this->sendSwiftMessage($transport,'New user registered  on way2society',$mailBody, 'dalvishreya106@gmail.com','Techsupport');
			
			if($bIsSendSocistyDetails == false)
			{
				if($result1 && $result2)
				{
					return true;
				}
				else
				{
					return false; 			
				}
			}
			else if($bIsSendSocistyDetails == true && $result2)
			{
				return true;
			}
			else
			{
				return false; 	
			}
		
			return $result;
	  }
	  catch(Exception $exp)
	  {
		echo "Error occured in email sending.";
	 }
	  
	}
	
	public function fetchLoginDetails($id = 0)
	{
		if($id > 0)
		{
			$sql ="select `member_id`,`name`,`mobile_number`,`fbcode` from `login` where `login_id` = '".$id."' ";
		}
		else
		{
			$sql ="select `member_id`,`name`,`mobile_number`,`fbcode` from `login` where `login_id` = '".$_SESSION['login_id']."' ";
		}
		$result = $this->m_dbConn->select($sql);	
		
		return $result;
	}
	
	public function sendSwiftMessage($transport ,$MailSubject,$MailBody,$toId,$toName)
	{
		try
	  {
			$emailContent = GetEmailHeader() . $MailBody . GetEmailFooter() ;
			$mailer = Swift_Mailer::newInstance($transport);
			$message = Swift_Message::newInstance();
			$message->setTo(array( $toId => $toName));
		 
			$message->setSubject($MailSubject);
			$message->setBody($emailContent);
			//$message->setFrom(array('no-reply14@way2society.com' => 'no-reply'));
			$message->setFrom(array('no-reply@way2society.com' => 'way2society'));
			$message->setCc(array('activation@way2society.com' => 'Way2Society Activation'));
			$message->setCc(array('cs@way2society.com' => 'way2society'));
			$message->setContentType("text/html");	
		 
			// Send the email
			$result = $mailer->send($message);	
			
			if($result <> 0)
			{
				return true;
			}
			else
			{
				return false;
			}
	  }
	  catch(exception $e)
	  {
		  echo "error occured in send function";
	 }
	}
	
	
	public function sendNewUserActivationEmail($email, $name, $sAccountActivationCode ="", $GetReturnStatus ="", $SocietyID = "")
	{
		//echo "sending email";
		include_once('email_format.class.php');
		include_once('email.class.php');
		require_once('../swift/swift_required.php');
			//	echo "sending email2";
		//include_once('utility.class.php');
		$EMailIDToUse = $this->m_objUtility->GetEmailIDToUse(false, 0, 0, 0, 0, 0, $SocietyID, 0, 0);
		//var_dump($EMailIDToUse);
		
		//$EMailID = "";
		//$Password = "";
				
		//if(isset($EMailIDToUse) && $EMailIDToUse['status'] == 0)
		//{
		//		$EMailID = $EMailIDToUse['email'];
				//$Password = $EMailIDToUse['password'];
		//}
		//AWS EMail
		
		$AWS_Config = CommanEmailConfig();
		
		$transport = Swift_SmtpTransport::newInstance($AWS_Config[0]['Endpoint'],$AWS_Config[0]['Port'] , $AWS_Config[0]['Security'])
				  ->setUsername($AWS_Config[0]['Username'])
				  ->setPassword($AWS_Config[0]['Password']);	 
		
			  
		//$transport = Swift_SmtpTransport::newInstance('103.50.162.146',587)
		//							->setUsername($EMailID)
		//							->setSourceIp('0.0.0.0')
			//						->setPassword($Password) ; 
		  try
		  {
			  	$MailBody = $this->generateActivationEmailTemplate(true,$email,$name,$sAccountActivationCode);							
				$emailContent = GetEmailHeader() . $MailBody . GetEmailFooter();
				$mailer = Swift_Mailer::newInstance($transport);
				$message = Swift_Message::newInstance();
			 	//$message->setTo(array( 'dalvishreya106@gmail.com' => 'name'));
				$message->setTo(array($email => $name));
			 	$message->setSubject('Way2society Account Activation');
				$message->setBody($emailContent);
				$message->setFrom(array('cs@way2society.com' => 'way2society'));
				$message->setBcc(array( 'activation@way2society.com' => 'Way2Society Activaton'));
				$message->setContentType("text/html");	
				// Send the email
				$result = $mailer->send($message);	
				if($result >= 1)
				{		
					//echo 'result : ' .$result;
					
					if($GetReturnStatus == "1")
					{ 
						return "Success";
					}
					else
					{
						echo $email.':Success';
					}
				}
				else
				{
					
					if($GetReturnStatus == "1")
					{ 
						return "Success";
					}
					else
					{
						echo $email.':Failed';
					}
				}
					
				//return $result;
		  }
		  catch(Exception $exp)
		  {
			echo "Error occured in email sending.";
			//print_r($exp->getMessage());
		}	
	}

	public function sendNewUserActivationSMS($mobile, $name, $sAccountActivationCode ="", $GetReturnStatus ="", $SocietyID = "")
	{
			 // $MobileNo='8850007210';
			 $MsgBody='Your activation code is '.$sAccountActivationCode;
             
             if($mobile !='' && $mobile !=0)
             {

			 $response = $this->m_objUtility->SendDemoSMS($mobile, $MsgBody);
             
             $result=explode(",", $response);
             echo $result[1];
			 }
			 else
			 {
			 	echo "Error";
			 }

	}
	
	
	public function getSwiftEmailObject()
	{
		
		require_once('swift/swift_required.php');
		
		//$transport = Swift_SmtpTransport::newInstance('md-in-1.webhostbox.net', 465, "ssl")
		//$transport = Swift_SmtpTransport::newInstance('103.50.162.146',587)
									//->setUsername('no-reply14@way2society.com')
									//->setSourceIp('0.0.0.0')
									//->setPassword('society123') ; 
		$AWS_Config = CommanEmailConfig();
		$transport = Swift_SmtpTransport::newInstance($AWS_Config[0]['Endpoint'],$AWS_Config[0]['Port'] , $AWS_Config[0]['Security'])
				 			->setUsername($AWS_Config[0]['Username'])
				  			->setPassword($AWS_Config[0]['Password']);	
		return $transport;	
		
	}
	
	public function checkIfEmailAlreadyExists($sEmailID)
	{
		$result = $this->m_dbConn->ExecStoredProcWithoutWithParameters('checkUserEmailIdAlreadyExists("'.$sEmailID.'")');
		//var_dump($result);	
		return $result;
	}
	
	public function generateActivationEmailTemplate($bShow = false,$email , $name,$sAccountActivationCode = "", $bHideRegistrationVerbaige = 0)
	{
		    $encryptedEmail = $this->m_objUtility->encryptData($email);	
			$loginExist = $this->m_objDBRoot->select("SELECT * FROM `login` WHERE `member_id` ='".$email."'");	
			$sCode=substr($sAccountActivationCode, -4);
			$sActivCode=$sCode;
			
			/*	if($email <> '')
			{	
			//	echo "Code: ".$sAccountActivationCode;
				$sCode=substr(($sAccountActivationCode),0, 4);
				$sActivCode=$sCode;
				$sAccountActivationCode=$email . $sActivCode;									
				
				}
				else
				{
					$sAccountActivationCode=$sAccountActivationCode;
				}*/
							
		  if(sizeof($loginExist) > 0)
		  {
			  //$Url = 'http://way2society.com/login.php?mCode='.$sAccountActivationCode.'&u='.$encryptedEmail;
				$Url = 'https://way2society.com/login.php?mCode='.$sAccountActivationCode.'&u='.$encryptedEmail;
		  }
		  else
		  {
			$newUserUrl = "https://way2society.com/newuser.php?reg&u=".$email."&c=".$sAccountActivationCode."&n=".$name."&tkn=".$encryptedEmail;
			   $Url = $newUserUrl.'&URL=https://way2society.com/Dashboard.php?View=MEMBER';
		  }
		   
		   $mailBody = "Hi ".$name.",";
		   $activationbtnText = "";
		 if($bHideRegistrationVerbaige == 1)
		 {
			  $mailBody .="<br /><br />Your society accounting is done on way2society.com platform. You can sign up and check your bills and record payments and communicate with managing committeethrough your way2society.com account. Please sign-up for free by clicking below button :";
			  $activationbtnText = "Sign Up";
		 }  
		 else
		 {
			  $mailBody .="<br /><br />Thank you for registering with way2society.com";
			  
			  if($_SESSION['society_client_id'] == 13) //Society Manager Inc
			  {
				  $mailBody .=" for digital services for your flat/ garage/ shop & Society Managers Inc. for On-Ground Full Service Society Management services";
			  }
			  //$mailBody .="<br />We have received request for registeration for ".$email;
			  $mailBody .= ".<br />Please click on the following link to activate your account. After login, please update your profile to serve you better.";
			  
			   $mailBody .= "<br />Your username is your email id : ".$email." and you can set password of your choice.";
			  //$mailBody .="<br />If you have any query or concern, please contact us at cs@way2society.com.";
			  // $mailBody .='<tr><td><br></td></tr>';
			   $activationbtnText = "Activate";
		 }
		 
		 if($_SESSION['society_client_id'] == 13) //Society Manager Inc
		 {
			 $Contact_String = 'In case of any issues you can email: teamsocman@gmail.com or Call/SMS/WhatsApp: +91 7977468288';
		 }
		 else
		 {
			$Contact_String = 'If you have any query or concern, please contact us at <a href="mailto:cs@way2society.com" target="_blank">cs@way2society.com</a>';
	     }
		 
		  if($bShow == true)
		  {
			$mailBody .= '<br /><br /><table width="100%" cellspacing="0" cellpadding="0" border="0">
										<tbody><tr ><td style="width:200px"></td><td valign="middle" bgcolor="#337AB7" height="40" align="center" style="text-align:center; width:200px; ">
										<a  id="act_btn" target="_blank" style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif" href="'.$Url.'">'.$activationbtnText.'</a></center></td><td style="width:200px"></td>
										</tr>
										<tr><td></td></tr>';
										$mailBody .='<tr><td><br></td></tr>
										<tr><td colspan="3">If you already have a Way2Society.com user login id and want to link this unit with it, then please login to your Way2Society account and click on the Link "Have A New Code To Link Another Society/Flat ?" and enter this activation code. </td></tr>  
										<tr><td><br></td></tr>
										<tr  align="center"><td colspan="3">
                                       <table style="height:50px;"><tr style="background-color:#f8f8f8; height:50px;">
                                       <td align="center" style="width:160px;height: 45px;"><span style="font-size:16px">Web Access Code :</span></td>
                                       <td align="center" style="background-color:rgb(217, 237, 247);width: 340px;height: 45px;"><span style="font-size:16px">'.$sAccountActivationCode.'</span></td>
                                       </tr></table></td></tr><tr><td><br></td></tr>                 
										<tr><td colspan="3">Alternativaly, You can install mobile App "Way2Society" from Google Play Store or Apple App Store. Click on "New Account Activation" link at bottom of login screen and use following Activation Code when prompted during installation. </td></tr>
                                        <tr><td colspan="3">Your username is your email id : '.$email.' and you can set password of your choice. </td></tr><tr><td><br><br></td></tr><tr  align="center"><td colspan="3">
                                       <table style="height:50px;"><tr style="background-color:#f8f8f8; height:50px;">
                                       <td align="center" style="width:160px;height: 45px;"><span style="font-size:16px">Mobile Access Code :</span></td>
                                       <td align="center" style="background-color:rgb(217, 237, 247);width: 200px;height: 45px;"><span style="font-size:16px">'.$sActivCode.'</span></td><td>&nbsp;&nbsp;&nbsp;</td><td><a rel="nofollow" target="_blank" href="https://play.google.com/store/apps/details?id=com.ionicframework.way2society869487&amp;rdid=com.ionicframework.way2society869487">
										<img src="https://way2society.com/images/app.png" width="120" height="50" style="" class="yiv1843970569ycb7204091656"></a></td>
										<td><a rel="nofollow" href="https://itunes.apple.com/in/app/way2society/id1389751648?mt=8" target="_blank">
										<img src="https://way2society.com/images/ios.png" width="120" height="50" style="" class="yiv1843970569ycb7204091656"></a></td>
                                       </tr></table></td></tr><tr><td><br><br></td></tr>
										<tr style="background-color:#f8f8f8"><td colspan="3" align="center"><p style="font-size:18px;">Please watch below video for detail instructions for Activation</p></td></tr>
										<tr style="background-color:#f8f8f8"><td colspan="3" align="center">
								<video poster="../images/images.png" width="100%" height="50%" controls="controls">
							<a href="https://www.youtube.com/watch?v=j5-iMVxSTDg" >
							<img src="http://way2society.com/images/active1.jpg" width="300px" height="160px" alt="image instead of video" />
							</a>
							</video>
							</td></tr> 
                                        <tr style="background-color:#f8f8f8"><td colspan="3"><br></td></tr>
										<tr><td colspan="3"><br></td></tr>
										<tr><td colspan="3">'.$Contact_String.'</td></tr>
										</center></tbody></table>';	
		  }
		  else
		  {
			  	 $mailBody .= '<br /><br /><table width="100%" cellspacing="0" cellpadding="0" border="0">
										<tbody><tr><td style="width:200px"></td><td valign="middle" bgcolor="#337AB7" height="40" align="center" style="text-align:center; width:200px;">
										<a  id="act_btn" target="_blank" style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif;" >Activate</a></center></td><td style="width:200px"></td>
										</tr></center></tbody></table>';	
		  }
		  	return $mailBody;
	}
	
	public function bEmailIdAlreadyExists($email)
	{
		$sqlCheck = "select count(member_id) as cnt from login where member_id = '" . $email . "'";
		$resultCheck = $this->m_dbConn->select($sqlCheck);	
		if($resultCheck[0]['cnt'] > 0)
		{
			return true;	
		}
		return false;
	}
}
?>

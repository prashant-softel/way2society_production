<?php
include_once("include/display_table.class.php");
include_once("defaults.class.php");
include_once("dbconst.class.php");
include_once("bill_period.class.php");
include_once("genbill.class.php");


class society
{
	public $actionPage = "../society.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_billperiod;
	
	function __construct($dbConn, $dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		
		$this->display_pg=new display_table($this->m_dbConn);
		$this->obj_billperiod = new bill_period($this->m_dbConn);
		$this->obj_genbill = new genbill($this->m_dbConn,$this->m_dbConnRoot);
		
	}
	public function startProcess()
	{
		$timestamp = DateTime::createFromFormat('U.u', microtime(true));
		$uniqueTime = $timestamp->format("m-d-Y H:i:s.u");
			
		$errorExists=0;
		if($_REQUEST['insert']=='Submit' && $errorExists==0)
		{
			if($_FILES['photo']['name'] <> "")
			
				{ 
				
					if (($_FILES["photo"]["type"] == "image/png") ) 
							{
								 $exe_photo_main = "";
								
								
								if($_FILES["photo"]["type"] == "image/png")
								{
									$exe_photo_main =".png" ;
								}
								
								
					//$photo_new_path = $this->up_photo($_FILES['photo']['name'],$_FILES["photo"]["tmp_name"],'../SocietyLogo/main');
					
					////////////////////////////////////////
					$thumbWidth_index  = 240;
					$thumbHeight_index = 80;
					$pathToThumbs_index = '../SocietyLogo/thumb/';
					$image_name = time().'_thumb_'.str_replace(' ','-',$_FILES['photo']['name']);
					
					//$thumb_path = $this->thumb_photo($thumbWidth_index,$thumbHeight_index,$pathToThumbs_index,$photo_new_path,$exe_photo_main,$image_name); 
					////////////////////////////////////////
					//$thumb_path='';
					
					}
					else 
					{					
						return 'Invalid File Type For Photo';
					}
				}
			
			date_default_timezone_set('Asia/Kolkata');
			$societyCreatedDate = date("Y-m-d");
			
			$sqlFetchYearID = "SELECT  *  FROM `year` where '".$societyCreatedDate."' BETWEEN `BeginingDate` AND `EndingDate`";
			$resYearID = $this->m_dbConn->select($sqlFetchYearID);
			$YearID = $resYearID[0]['YearID'];
			if($YearID <> "")
			{
				$bIsSuccess = $this->AddPeriods($_POST['bill_cycle'] , $YearID);
				if($bIsSuccess == false)
				{
					return "unable to generate periods for year  ".$resYearID[0]['YearDescription'];		
				}
			}
			else
			{
				return "year not created please  add year first and then add society";			
			}
			
			if($_POST['society_code']<>"" && $_POST['society_name']<>"" && $_POST['email'])
			{				
				$regExist = false;
				if($_POST['registration_no']<>"")
				{								
					$sql00 = "select count(*)as cnt from society where registration_no = '".$this->m_dbConn->escapeString($_POST['registration_no'])."' and status='Y'";					
					$res00 = $this->m_dbConn->select($sql00);
					if($res00[0]['cnt'] > 0)
					{
						 $regExist = true;
					}				
				}
				
				if($regExist == false)
				{					
					$sql = "select count(*)as cnt from society where society_name = '".$this->m_dbConnRoot->escapeString($_POST['society_name'])."' and landmark='".$this->m_dbConn->escapeString($_POST['landmark'])."' and status='Y'";
					$res = $this->m_dbConn->select($sql);
					
					if($res[0]['cnt']==0)
					{
						$insert_society_root = "INSERT INTO `society`(`society_code`, `society_name`, `dbname`, `send_reminder_sms`,`client_id`) VALUES ('" . $_POST['society_code'] . "','" . $this->m_dbConn->escapeString(trim(ucwords($_POST['society_name']))) . "','" . $_SESSION['dbname'] . "', '".$_POST['send_reminder']."','".$_SESSION['client_id']."')";
						
						$result_society_id = $this->m_dbConnRoot->insert($insert_society_root);
						
						$update_dbname = "UPDATE dbname SET society_id = '" . $result_society_id . "' ,locked = '" . $uniqueTime . "' WHERE dbname = '" . $_SESSION['dbname'] . "'";
						$result_dbname = $this->m_dbConnRoot->update($update_dbname);
						
						$insert_mapping = "INSERT INTO `mapping`(`login_id`, `society_id`, `desc`, `role`, `profile`, `created_by`, `status`, `view`) VALUES ('" . $_SESSION['login_id'] . "', '" . $result_society_id . "', '" . ROLE_SUPER_ADMIN . "', '" . ROLE_SUPER_ADMIN . "', '" . PROFILE_SUPER_ADMIN_ID . "', '" . $_SESSION['login_id'] . "', 2, 'ADMIN')";
						
						$result_mapping = $this->m_dbConnRoot->insert($insert_mapping);
						
						$sqlUpdate = "UPDATE `login` SET `current_mapping`='" . $result_mapping . "' WHERE login_id = '" . $_SESSION['login_id'] . "'";
						$resultUpdate = $this->m_dbConnRoot->update($sqlUpdate);
						
						if($_SESSION['client_id'] > 0)
						{
							$sqlSelectSadmin = "select login_id from login where client_id = '" . $_SESSION['client_id'] . "' and authority = 'self'";
							$resultSelectSadmin = $this->m_dbConnRoot->select($sqlSelectSadmin);
							
							print_r($resultSelectSadmin);
							
							echo 'SAdmin Count : ' . sizeof($resultSelectSadmin);
							for($sadminCnt = 0 ; $sadminCnt < sizeof($resultSelectSadmin) ; $sadminCnt++)
							{
								if($resultSelectSadmin[$sadminCnt]['login_id'] <> $_SESSION['login_id'])
								{
									$insert_mapping_sadmin = "INSERT INTO `mapping`(`login_id`, `society_id`, `desc`, `role`, `profile`, `created_by`, `status`, `view`) VALUES ('" . $resultSelectSadmin[$sadminCnt]['login_id'] . "', '" . $result_society_id . "', '" . ROLE_SUPER_ADMIN . "', '" . ROLE_SUPER_ADMIN . "', '" . PROFILE_SUPER_ADMIN_ID . "', '" . $_SESSION['login_id'] . "', 2, 'ADMIN')";
						
									$result_mapping_sadmin = $this->m_dbConnRoot->insert($insert_mapping_sadmin);
								}
							}
						}						
						
						$insert_mapping = "INSERT INTO `mapping`(`society_id`, `desc`, `code`, `role`, `profile`, `created_by`, `view`) VALUES ('" . $result_society_id . "', '" . ROLE_ADMIN . "', '" . getRandomUniqueCode() . "', '" . ROLE_ADMIN. "', '" . PROFILE_ADMIN_ID . "', '" . $_SESSION['login_id'] . "', 'ADMIN')";
						
						$result_mapping = $this->m_dbConnRoot->insert($insert_mapping);
						
						 $insert_query = "insert into society (`society_id`, `society_code`, `society_name`, `circle`, `registration_date`, `registration_no`, `society_add`,`show_address_in_email`, `city`, `landmark`,
									 `region`, `postal_code`, `country`, `phone`, `phone2`, `fax_number`, `pan_no`, `tan_no`, `service_tax_no`, `email`, `cc_email`, `url`, `member_since`, `bill_cycle`, `int_rate`, 
									 `int_method`, `int_tri_amt`, `rebate_method`, `rebate`, `chq_bounce_charge`, `show_wing`, `show_parking`, `show_area`, `bill_method`, `property_tax_no`, `water_tax_no`, 
									 `calc_int`, `show_share`, `bill_footer`, `sms_start_text`, `sms_end_text`, `send_reminder_sms`,`bill_due_date`,`show_floor`,`society_creation_yearid`,`unit_presentation`,`bill_as_link`,`email_contactno`,`neft_notify_by_email`,`notify_payment_voucher_daily`, `notify_sms_payment_voucher_daily` ,`gstin_no`,`apply_service_tax`,`apply_GST_on_Interest`,`apply_GST_above_Threshold`,`service_tax_threshold`,`igst_tax_rate`,`cgst_tax_rate`,`sgst_tax_rate`,`cess_tax_rate`,`gst_start_date`,`bank_penalty_amt`,`balancesheet_template`,`SocietyName_of_TDS`,`Show_Email_Postal_in_billheader`,`IsRoundOffLedgerAmt`,`reco_date_same_as_voucher`,`show_vertual_ac`,`show_intercom`,`bill_template`,`apply_Outstanding_amount`, `Auth_Share_Capital_Text`,`Auth_Share_Capital_Amount`,`show_logo`,`show_QR_code`,`show_reciept_on_supp`, `print_voucher_portrait`) values ('" . $result_society_id . "', '".$_POST['society_code']."','".$this->m_dbConn->escapeString(trim(ucwords($_POST['society_name'])))."',
									 '".$this->m_dbConn->escapeString(trim(ucwords($_POST['circle'])))."','" . getDBFormatDate($_POST['registration_date']) . "' , '".$this->m_dbConn->escapeString(trim($_POST['registration_no']))."',
									 '".$this->m_dbConn->escapeString(trim(ucwords(     $_POST['society_add'])))."', '".$_POST['show_address_in_email']."', '".$this->m_dbConn->escapeString(trim(ucwords($_POST['city'])))."',
									 '".$this->m_dbConn->escapeString(trim(ucwords($_POST['landmark'])))."', '".$this->m_dbConn->escapeString(trim(ucwords($_POST['region'])))."', '".$_POST['postal_code']."', 
									 '".(trim(ucwords($_POST['country'])))."', '".$_POST['phone']."', '".$_POST['phone2']."', '".$_POST['fax_number']."', '".$_POST['pan_no']."', '".$_POST['tan_no']."', '".$_POST['service_tax_no']."', 
									 '".$this->m_dbConn->escapeString(trim($_POST['email']))."', '".$this->m_dbConn->escapeString(trim($_POST['cc_email']))."', '".$this->m_dbConn->escapeString(trim($_POST['url']))."', '".$_POST['member_since']."', '".$_POST['bill_cycle']."', '".$_POST['int_rate']."', '".$_POST['int_method']."',
									 '".$_POST['int_tri_amt']."', '".$_POST['rebate_method']."', '".$_POST['rebate']."', '".$_POST['chq_bounce_charge']."', '".$_POST['show_wing']."', '".$_POST['show_parking']."', '".$_POST['show_area']."', '".$_POST['bill_method']."',
									 '".$_POST['property_tax_no']."', '".$_POST['water_tax_no']."', '".$_POST['calc_int']."', '".$_POST['show_share']."', 
									 '".$_POST['bill_footer']."', '".$_POST['sms_start_text']."', '".$_POST['sms_end_text']."', '".$_POST['send_reminder']."', '".$_POST['bill_due_date']."', '".$_POST['show_floor']."','".$YearID."','".$_POST['unit_presentation']."','".$_POST['bill_as_link']."','".$_POST['email_contactno']."','".$_POST['neft_notify_by_email']."', '".$_POST['notify_payment_voucher_daily_by_email']."', '".$_POST['notify_payment_voucher_daily_by_sms']."','".$_POST['gstin_no']."','".$_POST['apply_service_tax']."','".$_POST['apply_GST_On_Interest']."','".$_POST['apply_GST_above_Threshold']."','".$_POST['service_tax_threshold']."','".$_POST['igst_tax_rate']."','".$_POST['cgst_tax_rate']."','".$_POST['sgst_tax_rate']."','".$_POST['cess_tax_rate']."','".getDBFormatDate($_POST['gst_start_date'])."','".$_POST['bank_penalty_amt']."', '".$_POST['balancesheet_temp']."','".$_POST['tds_society_name']."','".$_POST['show_in_email_bill_header']."','".$_POST['apply_rounded_amt']."','".$_POST['reco_date_same_as_voucher']."','".$_POST['show_virtual']."','".$_POST['show_intercom']."','".$_POST['bill_temp']."','".$_POST['apply_Outstanding_amount']."', '".$_POST['Authorised_Share_Capital_Text']."','".$_POST['Authorised_Share_Capital_Amount']."','".$_POST['show_logo']."','".$_POST['show_QR_code']."','".$_POST['show_supp_reciept']."', '".$_POST['print_voucher_portrait']."')";
					
						//echo $insert_query;
						
						$data=$this->m_dbConn->insert($insert_query);
						$_SESSION['gst_start_date'] = getDBFormatDate($_POST['gst_start_date']);
						//echo '<br>';
						
						/*$sql = "insert into login(`society_id`,`security_no`,`member_id`,`password`,`authority`,`name`, `current_society`)values('".$data."','".$_POST['key']."','".$this->m_dbConn->escapeString($_POST['admin_user'])."','".$this->m_dbConn->escapeString($_POST['admin_pass'])."','Super Admin','Admin', '" . $data . "')";
						$res = $this->m_dbConn->insert($sql);
						
						$sqlUpdate = "UPDATE `login` SET `current_society`='" . $data . "' WHERE login_id = '" . $_SESSION['login_id'] . "'";
						$resultUpdate = $this->m_dbConn->update($sqlUpdate);*/
						
						/*$sql1 = "insert into del_control_admin(`society_id`,`login_id`,`del_control_admin`)values('".$data."','".$res."','1')";
						$res1 = $this->m_dbConn->insert($sql1);*/
						
						$sqlDefault = "INSERT INTO `appdefault`(`APP_DEFAULT_SOCIETY`, `changed_by`) VALUES ('" . $result_society_id . "', '" . $_SESSION['login_id'] . "')";
						$resultDefault = $this->m_dbConn->insert($sqlDefault);
						
						$sqlDefault = "INSERT INTO `counter`(`society_id`) VALUES ('" . $result_society_id . "')";
						$resultDefault = $this->m_dbConn->insert($sqlDefault);
						
						$obj_default = new defaults($this->m_dbConn);						
						$obj_default->getDefaults($data, true);
						
						if($_POST['unit_presentation'] <> $_POST['unit_presentation_previous_value'])
						{
							$up_query = "update unit set  `unit_presentation` = '".$_POST['unit_presentation']."' where  `unit_presentation` = '".$_POST['unit_presentation_previous_value']."' ";
							$data = $this->m_dbConn->update($up_query);
						}
						
												
						?>
						<script>window.location.href = '../society.php?id=<?php echo $_SESSION['society_id'];?>&show&imp'</script>
						<?php
						//return "Insert";
					}
					else
					{
						return ucwords($_POST['society_name']).' society is exist under this landmark - ' . $_POST['landmark'];
					}
				}
				else
				{
					return "Already exist this registration no. - " . $_POST['registration_no'];	
				}
			}
			else
			{
				return "All * Field Required";
			}
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			if($_POST['society_code']<>"")
			{	
			
				$sql = "select `bank_penalty_amt` from `society` where `society_id` = ".$_SESSION['society_id'];
				$previousAmt=$this->m_dbConn->select($sql);
				$changedLogDes = "Update bank_penalty_amt:<br>bank_penalty_amt : ".$previousAmt[0]['bank_penalty_amt']."|";
				$sqlForChangeLog = "INSERT INTO change_log (`ChangedLogDec`,`ChangedBy`,`ChangedTable`,`ChangedKey`) values ('".$changedLogDes."','".$_SESSION['login_id']."','society','".$_SESSION['society_id']."')";
				$res = $this->m_dbConn->insert($sqlForChangeLog);
			if($_FILES['photo']['name'] <> "")
			
				{ 
				
					if (($_FILES["photo"]["type"] == "image/png")) 
							{
								 $exe_photo_main = "";
								
								/*if ($_FILES["photo"]["type"] == "image/jpeg")
								{
									$exe_photo_main =".jpeg" ;
								}*/
								if($_FILES["photo"]["type"] == "image/png")
								{
									$exe_photo_main =".png" ;
								}
								/*else if ($_FILES["photo"]["type"] == "image/gif")
								{
									 $exe_photo_main =".gif" ;
								}
								else if ($_FILES["photo"]["type"] == "image/jpg")
								{
									 $exe_photo_main =".jpg" ;
								}*/	
								
					//$photo_new_path = $this->up_photo($_FILES['photo']['name'],$_FILES["photo"]["tmp_name"],'../SocietyLogo/main');
					
					////////////////////////////////////////
					$thumbWidth_index  = 240;
					$thumbHeight_index = 80;
					
					//$pathToThumbs_index = '../upload/thumb/';
					//$pathToThumbs_index = $_SERVER['DOCUMENT_ROOT'].'/upload/thumb/';
					$pathToThumbs_index = '../SocietyLogo/thumb/';
					$image_name = time().'_thumb_'.str_replace(' ','-',$_FILES['photo']['name']);
					
					//$thumb_path = $this->thumb_photo($thumbWidth_index,$thumbHeight_index,$pathToThumbs_index,$photo_new_path,$exe_photo_main,$image_name); 
					////////////////////////////////////////
					//$thumb_path='';
					
					}
					else 
					{					
						return 'Invalid File Type For Photo';
					}
				}
			
	//	die();
			
			 	$up_query="update society set `society_name`='".$this->m_dbConn->escapeString(trim(ucwords($_POST['society_name'])))."',`society_add`='".$this->m_dbConn->escapeString(trim(($_POST['society_add'])))."',`show_address_in_email`='".$_POST['show_address_in_email']."',
						`registration_date`='" . getDBFormatDate($_POST['registration_date']) . "',`landmark`='".$this->m_dbConn->escapeString(trim(ucwords($_POST['landmark'])))."',`state`='".$_POST['state_id']."',`city`='".$this->m_dbConn->escapeString(trim(ucwords($_POST['city'])))."',`region`='".$this->m_dbConn->escapeString(trim(ucwords($_POST['region'])))."',
						`postal_code`='".$_POST['postal_code']."',`country`='".$this->m_dbConn->escapeString(trim(ucwords($_POST['country'])))."',`phone`='".$_POST['phone']."',`phone2`='".$_POST['phone2']."',
						`fax_number`='".$_POST['fax_number']."',`email`='".$this->m_dbConn->escapeString(trim($_POST['email']))."',`cc_email`='".$this->m_dbConn->escapeString(trim($_POST['cc_email']))."',`member_since`='".$_POST['member_since']."',`bill_cycle`='".$_POST['bill_cycle']."',
						`int_rate`='".$_POST['int_rate']."',`int_tri_amt`='".$_POST['int_tri_amt']."',`int_method`='".$_POST['int_method']."',`rebate_method`='".$_POST['rebate_method']."',
						`chq_bounce_charge`='".$_POST['chq_bounce_charge']."',`show_wing`='".$_POST['show_wing']."',`calc_int`='".$_POST['calc_int']."',`show_parking`='".$_POST['show_parking']."',
						`show_area`='".$_POST['show_area']."',`bill_method`='".$_POST['bill_method']."',`rebate`='".$_POST['rebate']."',`property_tax_no`='".$_POST['property_tax_no']."',`water_tax_no`='".$_POST['water_tax_no']."',
						`show_share`='".$_POST['show_share']."', `registration_no`='".$this->m_dbConn->escapeString(trim($_POST['registration_no']))."',`circle` ='".$this->m_dbConn->escapeString(trim(ucwords($_POST['circle'])))."',`pan_no` = '".$_POST['pan_no']."' ,
						`tan_no` ='".$_POST['tan_no']."', `service_tax_no` ='".$_POST['service_tax_no']."',`url`='".$this->m_dbConn->escapeString(trim($_POST['url']))."', `bill_footer`='".$_POST['bill_footer']."',
						`sms_start_text` = '".$_POST['sms_start_text']."', `sms_end_text` = '".$_POST['sms_end_text']."', `send_reminder_sms` = '".$_POST['send_reminder']."', `bill_due_date` = '".$_POST['bill_due_date']."', `show_floor` = '".$_POST['show_floor']."',`unit_presentation` = '".$_POST['unit_presentation']."' ,`bill_as_link` = '".$_POST['bill_as_link']."',`email_contactno` = '".$_POST['email_contactno']."' , `neft_notify_by_email` = '".$_POST['neft_notify_by_email']."' ,`notify_payment_voucher_daily` = '".$_POST['notify_payment_voucher_daily_by_email']."' , `notify_sms_payment_voucher_daily` = '".$_POST['notify_payment_voucher_daily_by_sms']."',`gstin_no` = '".$_POST['gstin_no']."', `apply_service_tax` = '".$_POST['apply_service_tax']."',`apply_GST_On_Interest` = '".$_POST['apply_GST_On_Interest']."',`apply_GST_above_Threshold` = '".$_POST['apply_GST_above_Threshold']."', `service_tax_threshold` = '".$_POST['service_tax_threshold']."', `igst_tax_rate` = '".$_POST['igst_tax_rate']."' , `cgst_tax_rate` = '".$_POST['cgst_tax_rate']."' , `sgst_tax_rate` = '".$_POST['sgst_tax_rate']."' , `cess_tax_rate` = '".$_POST['cess_tax_rate']."', `gst_start_date` = '".getDBFormatDate($_POST['gst_start_date'])."' , `bank_penalty_amt` = '".$_POST['bank_penalty_amt']."', SMS_Reminder_Days='".$_POST['reminder_days']."',balancesheet_template='".$_POST['balancesheet_temp']."',`Record_NEFT` = '".$_POST['apply_NEFT_member']."',`send_reminder_email` = '".$_POST['Send_reminder_email']."',`Email_Reminder_Days` = '".$_POST['reminder_days_email']."',`PaymentGateway`='".$_POST['enable_paytm']."',`Paytm_Link`='".$_POST['pg_link']."', `PGName`='".$_POST['pg_name']."',`PGBeneficiaryBank`='".$_POST['payment_bank']."',`SocietyName_of_TDS`= '".$_POST['tds_society_name']."' ,`Show_Email_Postal_in_billheader`='".$_POST['show_in_email_bill_header']."',`IsRoundOffLedgerAmt`='".$_POST['apply_rounded_amt']."',`reco_date_same_as_voucher` = '".$_POST['reco_date_same_as_voucher']."',`show_vertual_ac` = '".$_POST['show_virtual']."',`show_intercom` = '".$_POST['show_intercom']."',`bill_template` = '".$_POST['bill_temp']."',`apply_Outstanding_amount` = '".$_POST['apply_Outstanding_amount']."', `Auth_Share_Capital_Text` = '".$_POST['Authorised_Share_Capital_Text']."',`Auth_Share_Capital_Amount` = '".$_POST['Authorised_Share_Capital_Amount']."',`show_logo` = '".$_POST['show_logo']."',`show_QR_code` = '".$_POST['show_QR_code']."', `show_reciept_on_supp` = '".$_POST['show_supp_reciept']."',`print_voucher_portrait` = '".$_POST['print_voucher_portrait']."' where society_code='".$_POST['society_code']."' and society_id ='".$_SESSION['society_id']."'";
				
				$data=$this->m_dbConn->update($up_query);
				
				$_SESSION['apply_gst'] = $_POST['apply_service_tax'];
				$_SESSION['apply_NEFT'] = $_POST['apply_NEFT_member'];
			
				$_SESSION['gst_start_date'] = getDBFormatDate($_POST['gst_start_date']);
				$up_query_soc_name = "UPDATE `society` SET `society_name`='" .$this->m_dbConn->escapeString(trim(ucwords($_POST['society_name']))). "', `send_reminder_sms` = '".$_POST['send_reminder']."' WHERE society_code='".$_POST['society_code']."' and society_id ='".$_SESSION['society_id']."'";
				
				$update_soc_name = $this->m_dbConnRoot->update($up_query_soc_name);
					
					
					//This code will update the day of SMSReminder
					if($_POST['IsSetSMSChange'] == 1)
					{
						$CheckBillRegisterExits = "SELECT * FROM billregister ORDER BY ID DESC LIMIT 1";
						$ResultCheckBillRegisterExits = $this->m_dbConn->select($CheckBillRegisterExits);
						$SMSReminderPeriod =$ResultCheckBillRegisterExits[0]['PeriodID'];
						if($SMSReminderPeriod <> 0 || $SMSReminderPeriod <> '')
						{
							$this->obj_genbill->SetReminderSMSDetails($_SESSION['society_id'], $SMSReminderPeriod);
						}
					}
					if($_POST['Send_reminder_email'] == 1)
					{
						$CheckBillRegisterExits = "SELECT * FROM billregister ORDER BY ID DESC LIMIT 1";
						$ResultCheckBillRegisterExits = $this->m_dbConn->select($CheckBillRegisterExits);
						$EmailReminderPeriod =$ResultCheckBillRegisterExits[0]['PeriodID'];
						if($EmailReminderPeriod <> 0 || $EmailReminderPeriod <> '')
						{
							$this->obj_genbill->SetReminderEmailDetails($_SESSION['society_id'], $EmailReminderPeriod);
						}
					}
				//	die();
				//$updateAppDefault = "UPDATE `appdefault` SET `APP_DEFAULT_EMAILID`= '".$this->m_dbConn->escapeString(trim($_POST['email']))."' WHERE APP_DEFAULT_SOCIETY = '" . $_SESSION['society_id'] . "' and `changed_by`= '" . $_SESSION['login_id'] . "'";
		
				//$resultUpdate = $this->m_dbConn->update($updateAppDefault);
				
				if($_POST['unit_presentation'] <> $_POST['unit_presentation_previous_value'])
				{
					$up_query = "update unit set  `unit_presentation` = '".$_POST['unit_presentation']."' where  `unit_presentation` = '".$_POST['unit_presentation_previous_value']."' ";
					$data = $this->m_dbConn->update($up_query);
				}
				
				?>
               <script>window.location.href = '../society.php?id=<?php echo $_SESSION['society_id'];?>&show&imp'</script>
                <?php
				//echo $up_query;
				//echo $sql;
				return "Update";
			}
		}
		else
		{
			return $errString;
		}
	}
	public function combobox($query,$id,$showDefaultText = false)
	{
		if($showDefaultText == true)
		{
			$str ="<option value='0'>Please Select</option>";
		}
		else
		{
			$str;	
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
						if($v==$id)
						{
							$sel = "selected";
						}
						else
						{
							$sel = "";	
						}
						$str.="<OPTION VALUE=".$v." ".$sel.">";
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
	public function display1($rsas)
	{
			$thheader=array('Wing','Society Name','Address','City','Region','Postal Code','Country','Phone No.1','Phone No.2','Fax No.','Email id','Member Since');
			$this->display_pg->edit="getsociety";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="society.php";
			$res=$this->society_list_show($rsas);
			return $res;
	}
	public function pgnation()
	{
		if(isset($_SESSION['sadmin']))
		{
			//$sql1 = "select * from society where status='Y' order by society_id desc";
			
			//$cntr = "select count(*) as cnt from society where status='Y' ";
			$sql1 = "select * from society where status='Y' and society_id='".$_SESSION['society_id']."' order by society_id desc";
			//echo $sql1;
			$cntr = "select count(*) as cnt from society where status='Y' and society_id='".$_SESSION['society_id']."'";
		}
		else
		{
			$sql1 = "select * from society where status='Y' and society_id='".$_SESSION['society_id']."' order by society_id desc";
			//echo $sql1;
			$cntr = "select count(*) as cnt from society where status='Y' and society_id='".$_SESSION['society_id']."'";
			
		}
		
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$this->display_pg->mainpg="society.php";
		$limit="20";
		$page = $_REQUEST['page'];
		$extra="";
		$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}
	
	public function society_list_show($res)
	{
		if($res<>"")
		{
		?>
		<table align="center" border="0">
		<tr height="30" bgcolor="#CCCCCC">
        	<th width="220">Society Name</th>
            <th width="200">Address</th>
            <th width="100">Landmark</th>
            <th width="90">Phone No.</th>
            <th width="170">Email</th>
            <?php if(isset($_SESSION['sadmin'])){?>
            <th width="100">View</th>
            <th width="100">Add</th>
            <?php }?>
            
            <?php if(isset($_SESSION['admin'])){?>
            <th width="100">Action</th>
            <?php }?>
            <th width="90">View Report</th>
            <!--<th width="90">Import Wing</th>-->
        	<th width="50">Edit</th>
            <?php if(isset($_SESSION['sadmin'])){?>
            <th width="70">Delete</th>
            <?php }?>
        </tr>
        <?php foreach($res as $k => $v){?>
        <tr height="25" bgcolor="#BDD8F4" align="center">
        	<td align="center"><?php echo $res[$k]['society_name'];?></td>
            <td align="center">
			<div style="overflow-y:scroll;overflow-x:hidden;width:190px; height:80px; border:solid #CCCCCC 1px;" align="center">
			<?php echo $res[$k]['society_add'];?>
            </div>
            </td>
            <td align="center"><?php echo $res[$k]['landmark'];?></td>
            <td align="center"><?php echo $res[$k]['phone'];?></td>
            <td align="center"><a href="mailto:<?php echo $res[$k]['email'];?>" style="color:#0000FF" target="_blank"><?php echo $res[$k]['email'];?></a></td>
            
            <?php if(isset($_SESSION['sadmin'])){?>
            <td align="center"><a href="wing.php?imp&idd=<?php echo time();?>&sa&sid=<?php echo $res[$k]['society_id'];?>&id=<?php echo rand('0000000','9999999');?>" style="color:#0000FF;"><b>View Wing</b></a></td>
            <td align="center"><a href="wing.php?imp&ssid=<?php echo $res[$k]['society_id'];?>&s&idd=<?php echo time();?>" style="color:#0000FF;"><b>Add Wing</b></a></td>
            <?php }else{?>
            <td align="center"><a href="wing.php?imp&ssid=<?php echo $res[$k]['society_id'];?>&s&idd=<?php echo time();?>" style="color:#0000FF;"><b>Add Wing</b></a></td>
            <?php }?>
            
            
            <td align="center">
            <a href="reports.php?&sid=<?php echo $res[$k]['society_id'];?>" style="color:#0000FF;"><b>Member's Due</b></a>
            </td>
            
           <!-- <td align="center">
            <a href="wing_import.php?&sid=<?php //echo $res[$k]['society_id'];?>" style="color:#0000FF;"><b>Import Wing</b></a>
            </td>-->
            
            <td align="center">
            <a href="javascript:void(0);" onclick="society_edit(<?php echo $res[$k]['society_id']?>)"><img src="images/edit.gif" /></a>
            </td>
            
            <?php if(isset($_SESSION['sadmin'])){?>
            <td align="center">
            <?php if($this->chk_delete_perm_sadmin()==1){?>
            <a href="javascript:void(0);" onclick="del_society(<?php echo $res[$k]['society_id']?>);"><img src="images/del.gif" /></a>
            <?php }else{?>
            <a href="del_control_sadmin.php?prm" target="_blank" style="text-decoration:none;"><font color=#FF0000 style='font-size:10px;'><b>Not Allowed</b></font></a>
            <?php }?>
            </td>
            <?php }?>
        </tr>
        <?php }?>
        </table>
		<?php
		}
		else
		{
			?>
            <table align="center" border="0">
            <tr>
            	<td><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
            </tr>
            </table>
            <?php	
		}
	}
	public function chk_delete_perm_sadmin()
	{
		$sql = "select * from del_control_sadmin where status='Y'";
		$res = $this->m_dbConn->select($sql);
		return $res[0]['del_control_sadmin'];
	}
	public function selecting()
	{
		$sql1 = "select s.society_id,s.society_code,s.society_name,s.circle,DATE_FORMAT(registration_date, '%d-%m-%Y') as registration_date,s.registration_no,s.society_add,s.city,s.landmark,s.state,s.region,s.postal_code,s.country,s.phone,s.phone2,s.fax_number, s.pan_no, s.tan_no, s.service_tax_no, s.email,s.url, s.member_since, s.bill_cycle, s.int_rate, s.int_tri_amt, s.int_method, s.rebate_method, s.rebate, s.chq_bounce_charge, s.bill_method, s.show_wing, s.show_parking, s.show_area,s.calc_int,s.property_tax_no,s.water_tax_no,s.show_share,s.bill_footer, s.sms_start_text, s.sms_end_text, s.send_reminder_sms, s.bill_due_date,s.show_floor,s.unit_presentation, s.cc_email ,s.bill_as_link,s.email_contactno,s.neft_notify_by_email,s.show_address_in_email,s.apply_service_tax,s.service_tax_threshold,s.igst_tax_rate,s.cgst_tax_rate,s.sgst_tax_rate,s.cess_tax_rate ,s.gstin_no,s.apply_GST_On_Interest,s.apply_GST_above_Threshold,DATE_FORMAT(s.gst_start_date, '%d-%m-%Y')  as gst_start_date,s.bank_penalty_amt,s.society_logo_thumb ,SMS_Reminder_Days
		,notify_payment_voucher_daily, notify_sms_payment_voucher_daily,s.balancesheet_template,s.Record_NEFT,s.send_reminder_email, s.Email_Reminder_Days,s.	PaymentGateway,s.Paytm_Link,s.PGName,s.PGBeneficiaryBank,s.SocietyName_of_TDS,s.Show_Email_Postal_in_billheader,s.IsRoundOffLedgerAmt,s.reco_date_same_as_voucher,s.show_vertual_ac,s.show_intercom,s.bill_template,s.apply_Outstanding_amount, s.Auth_Share_Capital_Text, s.Auth_Share_Capital_Amount,society_QR_Code,s.show_logo,s.show_QR_code,s.show_reciept_on_supp,s.print_voucher_portrait from society as s where s.society_id='".$_REQUEST['societyId']."'";
		$var=$this->m_dbConn->select($sql1);
		return $var;
	}
	public function deleting()
	{
		$sql0 = "select count(*)as cnt from unit where society_id='".$_REQUEST['societyId']."' and status='Y'";
		$res0 = $this->m_dbConn->select($sql0);
		
		if($res0[0]['cnt']==0)
		{
			$sql1="update society set status='N' where society_id='".$_REQUEST['societyId']."'";
			$this->m_dbConn->update($sql1);
			
			echo "msg1";
		}
		else
		{
			echo "msg";	
		}
	}
	
	public function del_society()
	{
		$sql1 = "update society set status='N' where society_id='".$_REQUEST['sss_id']."'";
		$this->m_dbConn->update($sql1);
		
		$sql2 = "update login set status='N' where society_id='".$_REQUEST['sss_id']."'";
		$this->m_dbConn->update($sql2);
		
	}
	
	public function check_socty_exist()
	{
		$sql = "select count(*)as cnt from society where society_name = '".$this->m_dbConn->escapeString($_REQUEST['soc_name'])."' and landmark='".$this->m_dbConn->escapeString($_REQUEST['landmark'])."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		
		if($res[0]['cnt']==0)
		{
			echo 0;
			echo '####';
		}
		else
		{
			echo 1;
			echo '####'.$_REQUEST['landmark'];
		}
	}
///////////////////////////////////////////////////////
/*	
	public function getInterestMethod($id)
	{
		
	$str.="<option value=''>Please Select</option>";
	$data =array('delay after due days','Full month');
	
		foreach($data as $value)
			{
				
				
						$str.="<OPTION VALUE=".$value.">";
					
					
				}
			
		
			return $str;
	}
	
*/

public function AddPeriods($cycleID , $yearID)
{
		$PeriodName = '';		
		$IsSuccess = false;	
				
		$FetchPeriod = $this->m_dbConn->select("select count(YearID) as count from `period` where `Billing_cycle`='".$cycleID."' and `YearID`= '".$yearID."'");
									
		if($FetchPeriod[0]['count'] == 0)
		{ 
		
			$months = getMonths($cycleID);
			
			$PrevYear =  $yearID - 1;
			$sqlFetchData = $this->m_dbConn->select("SELECT * FROM `year`  where  `YearID`= '".$PrevYear."'");
			
			$begin_date = $this->obj_billperiod->getBeginDate(end($months),$sqlFetchData[0]['YearDescription']);
			$end_date = $this->obj_billperiod->getEndDate(end($months),$sqlFetchData[0]['YearDescription']); 
									
			$insert_query="insert into period(`Billing_cycle`,`Type`,`YearID`,`PrevPeriodID`,`IsYearEnd`,`BeginingDate`,`EndingDate` )
									 values(".$cycleID.",'".end($months)."',".$PrevYear.",'0', '1','".$begin_date ."','".$end_date."')";
			$prevPeriod = $this->m_dbConn->insert($insert_query);	
			
			$this->obj_billperiod->setPeriod($months ,$cycleID,$yearID);
			$IsSuccess = true;	
		}
		else
		{
				$IsSuccess = false;	
		}
		
		return $IsSuccess;
				
}
	public function up_photo($name,$tmp_path,$location)
	{
		$photo_name = $name;
		$photo_name1 = str_replace(' ','-',$name);
		$old_path = $tmp_path;
		$new_path = $location.'/'.time().'_'.$photo_name1;
		$image = move_uploaded_file($old_path,$new_path);
		
		return $new_path;
	}
	public function thumb_photo($thumbWidth,$thumbHeight,$pathToThumbs,$newpath,$exe,$image_name)
	{
		$kk = 0;
					
	 /* if($exe=='.jpg' || $exe=='.jpeg')
	  {
		$img = imagecreatefromjpeg($newpath);				  //die();
		if(!$img)
		{
			$kk = 1;
		?>
		<!--	<script> window.location.href = '../service_prd_reg.php?nul=nul'; </script> -->
		<?php	
		}
	  }*/
	  /*else if($exe=='.gif')
	  {
		$img = imagecreatefromgif($newpath);				  //die();				  
		if(!$img)
		{
			$kk = 1;
		
		}
	  }*/
	  if($exe=='.png')
	  {
		$img = imagecreatefrompng($newpath);				  //die();
		if(!$img)
		{
			$kk = 1;
		?>
		
		<?php	
		}
	  }
	 /* else if($exe=='.bmp')
	  {
		$img = imagecreatefromwbmp($newpath);				  //die();
		if(!$img)
		{
			$kk = 1;
		}
	  }*/
	  else {} 
		  
	  if($kk<>1)
	  {
		  $width  = imagesx($img);
		  $height = imagesy($img);

		  $new_width  = $thumbWidth;
		  $new_height = $thumbHeight;
	
		  $tmp_img = imagecreatetruecolor($new_width,$new_height);
		  $bg_color = imagecolorat($tmp_img,1,1);
		  imagecolortransparent($tmp_img, $bg_color);
		  imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
		  imagepng($tmp_img,"{$pathToThumbs}{$image_name}");
		  
		  $thum_path = $pathToThumbs.$image_name;
		  
		  return $thum_path;
	  }
	}

}
?>
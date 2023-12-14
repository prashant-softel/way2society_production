<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once ("dbconst.class.php");
include_once('../swift/swift_required.php');
include_once( "include/fetch_data.php");
include_once("utility.class.php");
include_once("android.class.php");
include_once("latestcount.class.php");
include_once("../GDrive.php");
include_once("servicerequest.class.php");

class doc_templates extends dbop
{
	//public $actionPage = "../events_view.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	public $objFetchData;
	public $obj_Utility;
	public $obj_android;
	public $actionPage;
	public $obj_serviceRequest;
	
	function __construct($dbConn, $dbConnRoot, $SocietyID)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->m_dbConnRoot = $dbConnRoot;
		$this->actionPage = "";
		$this->obj_serviceRequest = new servicerequest($this->m_dbConn);
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

	public function combobox07($query,$id)
	{
		//$str.="<option value=''>All</option>";
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
	
	public function combobox_for_ptc($query,$id)
	{
		//$str.="<option value=''>All</option>";
		$sql01 = "SELECT `APP_DEFAULT_PROPERTY_TAX_LEDGER` FROM `appdefault` WHERE `APP_DEFAULT_SOCIETY` = '".$_SESSION['society_id']."'";
		$sql11 = $this->m_dbConn->select($sql01);
		if($sql11[0]['APP_DEFAULT_PROPERTY_TAX_LEDGER'] != 0)
		{
			$id = $sql11[0]['APP_DEFAULT_PROPERTY_TAX_LEDGER'];
		}
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
	
	public function combobox_for_overdue_payment($query,$id)
	{
		//$str.="<option value=''>All</option>";
		$str.="<option value=''>Please Select</option>";
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i = 0;
				$due_amt = $this->obj_Utility->getDueAmount($value['unit_id']);
				if($due_amt > 0)
				{
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
							$str.=$v." - ".$due_amt."</OPTION>";
						}
						$i++;
					}
				}	
			}
		}
		return $str;
	}
	
	public function combobox11($query,$name,$id)
	{
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			$pp = 0;
			foreach($data as $key => $value)
			{
				$i=0;
				
				foreach($value as $k => $v)
				{
					if($i==0)
					{
					?>
					&nbsp;<input type="checkbox" value="<?php echo $v;?>" name="<?php echo $name;?>" id="<?php echo $id;?><?php echo $pp;?>"/>					
					<?php
					}
					else if($i==1)
					{
						$society_grp_id = $this->society_grp_id($v);
					echo "<a href='../list_society_group_details.php?grp&id=".$society_grp_id."&view' target='_blank' style='color:blue;text-decoration:none;' title='Click to view society under this group'>".$v."</a>";
					?>
						<br />
					<?php
					}
					$i++;
				}
			$pp++;
			}
			?>
			<input type="hidden" size="2" id="count_<?php echo $id;?>" value="<?php echo $pp;?>" />
			<?php
		}
	}
	
	public function comboboxRoot($query,$id)
	{
		$str.="<option value=''>Please Select</option>";
		$data = $this->m_dbConnRoot->select($query);
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
	
	public function fetch_template_details($id)
	{	
		$sqlQuery = "select * from document_templates where id='".$id."'";
		$res = $this->m_dbConnRoot->select($sqlQuery);
		return $res[0];
	}
	
	public function fetch_data($to_pass, $nominees_info = "")
	{
		$final = "";
		if($to_pass['template_id'] == 27 && $to_pass['unit_id'] <> 0) //overdue payment
		{
			$get_primary_name = $this->obj_Utility->GetUnitNo($to_pass['unit_id']);
			
			$get_due_amt = $this->obj_Utility->getDueAmount($to_pass['unit_id']);
			$get_due_amt_comma = number_format($get_due_amt,2);
		
			$templates = $this->fetch_template_details($to_pass['template_id']);
		
			$sql01 = "select society_name from society where society_id = '".$_SESSION['society_id']."'";
			$sql11 = $this->m_dbConn->select($sql01);
			$society_name = $sql11[0]['society_name'];

			$today_date = date("d-m-Y");
			
			$old = array("{Date_Today}","{Primary_Name}","{Due_Amount}","{Society_Name}");
			$new = array($today_date,$get_primary_name[0]['primary_owner_name'],$get_due_amt_comma,$society_name);
			
			$final = str_replace($old,$new,$templates['template_data']);
		}
		else if($to_pass['template_id'] == 25) //agm notice
		{
			//check society_name flag here
			if($to_pass['society_name'] == 1)
			{
				$sql07 = "SELECT `society_name`,`registration_no`,`society_add` FROM `society` WHERE `society_id` = '".$_SESSION['society_id']."'";
				$sql77 = $this->m_dbConn->select($sql07);
				
				$society_table = "<table align='center'><tr><th style='text-align:center'>".$sql77[0]['society_name']."</th></tr><tr><th style='text-align:center'>".$sql77[0]['registration_no']."</th></tr><tr><th style='text-align:center'>".$sql77[0]['society_add']."</th></tr></table>";
			}
			
			$templates = $this->fetch_template_details($to_pass['template_id']);

			$sql01 = "select society_name from society where society_id = '".$_SESSION['society_id']."'";
			$sql11 = $this->m_dbConn->select($sql01);
			$society_name = $sql11[0]['society_name'];

			$correct_date=date_create($to_pass['date_of_meeting']);

			$day = date_format($correct_date,"l");
			$date = date_format($correct_date,"d");
			$month = date_format($correct_date,"M");
			$year = date_format($correct_date,"Y");
			$toput_date = $date." of ".$month;
			
			$toput_time_and_venue = $to_pass['time_of_meeting'].", ".$to_pass['venue'];
			
			$old = array("{day}","{date of month}","{year}","{time and venue}","{last AGM date}","{start time}","{Association Name}");
			$new = array($day,$toput_date,$year,$toput_time_and_venue,$to_pass['last_agm_date'],$to_pass['time_of_meeting'],$society_name);
			
			//$final = str_replace($old,$new,$templates['template_data']);
			
			if($to_pass['society_name'] == 1)
			{
				$final = $society_table;
				$final .= str_replace($old,$new,$templates['template_data']);
			}
			else
			{
				$final = str_replace($old,$new,$templates['template_data']);
			}
			
		}
		else if($to_pass['template_id'] == 1) //associate member form 
		{
			$templates = $this->fetch_template_details($to_pass['template_id']);
			$final = $templates['template_data'];
			//echo $final;
		}
		else if(($to_pass['template_id'] == 29 || $to_pass['template_id'] == 28) && $to_pass['fine_rs_id'] <> 0) //credit(refund) and debit(fine) resp
		{
			$templates = $this->fetch_template_details($to_pass['template_id']);
			
			$sql01 = "select society_name from society where society_id = '".$_SESSION['society_id']."'";
			$sql11 = $this->m_dbConn->select($sql01);
			$society_name = $sql11[0]['society_name'];
			
			$sql01 = "SELECT rs.ID, rs.UnitID, rs.Amount, rs.Comments, mm.unit, mm.primary_owner_name FROM `reversal_credits` rs, `member_main` mm where rs.UnitID = mm.unit and rs.ID = '".$to_pass['fine_rs_id']."' and ownership_status = 1";
			$sql11 = $this->m_dbConn->select($sql01);
			
			$old = array("{Member_Name}","{dc_amount}","{dc_reason}","{SOCIETY_NAME}");
			$new = array($sql11[0]['primary_owner_name'],abs($sql11[0]['Amount']),$sql11[0]['Comments'],$society_name);
			
			$final = str_replace($old,$new,$templates['template_data']);
		}
		else if($to_pass['template_id'] == 31 || $to_pass['template_id'] == 33) //passport and domicile resp.
		{
			$templates = $this->fetch_template_details($to_pass['template_id']);
			
			$sql01 = "select society_name, registration_no, society_add,"./*_line_1, society_add_line_2,*/ "city from society where society_id = '".$_SESSION['society_id']."'";
			$sql11 = $this->m_dbConn->select($sql01);
			
			$today_date = date("d-m-Y");
			$old = array("{SOCIETY_NAME}","{SOCIETY_REGISTRATION_NUMBER}","{ADDRESS LINE 1}","{LETTER_DATE}","{APPLIER_NAME}","{RELATION_WITH_OWNER}","{OWNER_NAME}","{OWNER_ADDRESS}","{SINCE_STAYING_DATE}","{SOCIETY_CITY}","{ADDRESS LINE 2}");
			
			$new = array($sql11[0]['society_name'],$sql11[0]['registration_no'],$sql11[0]['society_add'],$today_date,$to_pass['applier_name'],$to_pass['relation'],$to_pass['owner_name'],$to_pass['address'],$to_pass['start_date'],$sql11[0]['city']," ");
			
			$final = str_replace($old,$new,$templates['template_data']);
		}
		else if($to_pass['template_id'] == 34) //bank noc
		{
			$templates = $this->fetch_template_details($to_pass['template_id']);
			
			$sql01 = "select society_name, registration_no, society_add,"./*_line_1, society_add_line_2*/" cc_no, cc_date from society where society_id = '".$_SESSION['society_id']."'";
			$sql11 = $this->m_dbConn->select($sql01);
			
			$today_date = date("d-m-Y");
			$old = array("{SOCIETY_NAME}","{SOCIETY_REGISTRATION_NUMBER}","{ADDRESS LINE 1}","{ADDRESS LINE 2}","{LETTER_DATE}","{AUTHORISED_NAME}","{BANK_NAME}","{BANK_ADDRESS}","{FLAT_NUMBER}","{OWNER_NAME}","{CC_NUMBER}","{CC_DATE}","{FLAT_COST}");
			
			$new = array($sql11[0]['society_name'],$sql11[0]['registration_no'],$sql11[0]['society_add']," ",$today_date,$to_pass['manager_name'],$to_pass['bank_name'],$to_pass['bank_address'],$to_pass['flat_no'],$to_pass['flat_owner_name'],$to_pass['cc_no'],$to_pass['cc_date'],$to_pass['flat_cost']);
			
			$final = str_replace($old,$new,$templates['template_data']);
		}
		else if($to_pass['template_id'] == 35) //lnl noc
		{
			$templates = $this->fetch_template_details($to_pass['template_id']);
			
			$sql01 = "select society_name, registration_no, society_add"./*_line_1, society_add_line_2*/" from society where society_id = '".$_SESSION['society_id']."'";
			$sql11 = $this->m_dbConn->select($sql01);
			
			$sql02 = "select unit_presentation from unit where unit_no = '".$to_pass['flat_no']."'";
			$sql22 = $this->m_dbConn->select($sql02);
			$unit_presentation = $sql22[0]['unit_presentation'];
			
			$today_date = date("d-m-Y");
			if($unit_presentation != 3)
			{
				$old = array("{SOCIETY_NAME}","{SOCIETY_REGISTRATION_NUMBER}","{ADDRESS LINE 1}","{ADDRESS LINE 2}","{LETTER_DATE}","{OWNER_NAME}","{OWNER_HIS_HER}","{FLAT_NO}","{TENANT}");
				$new = array($sql11[0]['society_name'],$sql11[0]['registration_no'],$sql11[0]['society_add']," ",$today_date,$to_pass['flat_owner_name'],$to_pass['gender'],$to_pass['flat_no'],$to_pass['tenant_name']);
			}
			else
			{
				$old = array("{SOCIETY_NAME}","{SOCIETY_REGISTRATION_NUMBER}","{ADDRESS LINE 1}","{ADDRESS LINE 2}","{LETTER_DATE}","{OWNER_NAME}","{OWNER_HIS_HER}","{FLAT_NO}","{TENANT}","only");
				$new = array($sql11[0]['society_name'],$sql11[0]['registration_no'],$sql11[0]['society_add']," ",$today_date,$to_pass['flat_owner_name'],$to_pass['gender'],$to_pass['flat_no'],$to_pass['tenant_name']," ");
			}
			
			$final = str_replace($old,$new,$templates['template_data']);
		}
		else if($to_pass['template_id'] == 36) //em noc
		{
			$templates = $this->fetch_template_details($to_pass['template_id']);
			
			$sql01 = "select society_name, registration_no, society_add"./*_line_1, society_add_line_2*/" from society where society_id = '".$_SESSION['society_id']."'";
			$sql11 = $this->m_dbConn->select($sql01);
			
			$today_date = date("d-m-Y");
			
			$old = array("{SOCIETY_NAME}","{SOCIETY_REGISTRATION_NUMBER}","{ADDRESS LINE 1}","{ADDRESS LINE 2}","{LETTER_DATE}","{OWNER_NAME}","{HIS_HER}","{FLAT_NO}","{CURRENT_ELECTRICITY_NAME}","{OWNER_ADDRESS}");
			$new = array($sql11[0]['society_name'],$sql11[0]['registration_no'],$sql11[0]['society_add']," ",$today_date,$to_pass['flat_owner_name'],$to_pass['gender'],$to_pass['flat_no'],$to_pass['current_em'],$to_pass['flat_owner_address']);
			
			$final = str_replace($old,$new,$templates['template_data']);
		}
		else if($to_pass['template_id'] == 37 || $to_pass['template_id'] == 38) //web access blocked and web access restored resp.
		{
			$templates = $this->fetch_template_details($to_pass['template_id']);
			
			$sql01 = "select society_name, registration_no, society_add"./*_line_1, society_add_line_2*/" from society where society_id = '".$_SESSION['society_id']."'";
			$sql11 = $this->m_dbConn->select($sql01);
			
			$today_date = date("d-m-Y");
			
			$sql02 = "select u.unit_id, u.block_unit, u.block_desc, mm.primary_owner_name from `unit` u, `member_main` mm where u.unit_id = mm.unit and u.unit_id = '".$to_pass['unit_id']."' and mm.ownership_status = 1";
			$sql22 = $this->m_dbConn->select($sql02);
			
			$old = array("{SOCIETY_NAME}","{SOCIETY_REGISTRATION_NUMBER}","{ADDRESS LINE 1}","{ADDRESS LINE 2}","{LETTER_DATE}","{member_name}","{block_reason}");
			$new = array($sql11[0]['society_name'],$sql11[0]['registration_no'],$sql11[0]['society_add']," ",$today_date,$sql22[0]['primary_owner_name'],$sql22[0]['block_desc']);
			
			$final = str_replace($old,$new,$templates['template_data']);
		}
		else if($to_pass['template_id'] == 49) //nomination form
		{
			$templates = $this->fetch_template_details($to_pass['template_id']);
			
			$sql01 = "select society_name, registration_no, society_add"./*_line_1, society_add_line_2*/" from society where society_id = '".$_SESSION['society_id']."'";
			$sql11 = $this->m_dbConn->select($sql01);
			
			$old = array("{OWNER_NAME}","{SHARE_SERIAL_NUMBER}","{SHARE_ISSUE_DATE}","{FIVE_OR_TEN_SHARES}","{SHARE_PER_AMOUNT}","{SHARE_START_NUMBER}","{SHARE_END_NUMBER}","{FLAT_NAME}","{TOTAL_AREA}","{WITNESS1_NAME_1}","{WITNESS1_ADDRESS_1}","{WITNESS2_NAME_2}","{WITNESS2_ADDRESS_1}","{SOCIETY_NAME}","{ADDRESS LINE 1}","{ADDRESS_LINE 2}","{NOMINATION_ID}");
			$new = array($to_pass['flat_owner_name'],$to_pass['sc_no'],$to_pass['sc_date'],$to_pass['no_of_shares'],$to_pass['amt_per_share'],$to_pass['start_no'],$to_pass['end_no'],$to_pass['flat_no'],$to_pass['area'],$to_pass['witness_name_1'],$to_pass['witness_address_1'],$to_pass['witness_name_2'],$to_pass['witness_address_2'],$sql11[0]['society_name'],$sql11[0]['society_add']," "," ");
			
			$before_final_replace = str_replace($old,$new,$templates['template_data']);
			
			$converted = array();
			for($iCnt = 0; $iCnt < 5; $iCnt++)
			{
				if($iCnt < sizeof($nominees_info))
				{
					$converted[$iCnt] = (array)$nominees_info[$iCnt];
				}
				else
				{
					$new_empty = array("name" => '', "address" => '', "relation" => '', "share" => '', "dob" => '', "guardian_name" => '');
					array_push($converted,$new_empty);
				}
			}
			
			$guardian_string = "6.{No} As the nominee/s at Sr.No {Sr.No} is the minor, I hereby appoint Shri/Shrimati {Guardian_Name} as the guardian/legal representative of the minor to represent the minor nominee in matters connected with this nomination.";
			
			$old_1 = array("{NOMINEE_1A}","{NOMINEE_1B}","{NOMINEE_1C}","{NOMINEE_1D}","{NOMINEE_1E}","{NOMINEE_2A}","{NOMINEE_2B}","{NOMINEE_2C}","{NOMINEE_2D}","{NOMINEE_2E}","{NOMINEE_3A}","{NOMINEE_3B}","{NOMINEE_3C}","{NOMINEE_3D}","{NOMINEE_3E}","{NOMINEE_4A}","{NOMINEE_4B}","{NOMINEE_4C}","{NOMINEE_4D}","{NOMINEE_4E}","{NOMINEE_5A}","{NOMINEE_5B}","{NOMINEE_5C}","{NOMINEE_5D}","{NOMINEE_5E}","{FIRST_NOMINEE_NAME}");
			$new_1 = array($converted[0]['name'],$converted[0]['address'],$converted[0]['relation'],$converted[0]['share'],$converted[0]['dob'],$converted[1]['name'],$converted[1]['address'],$converted[1]['relation'],$converted[1]['share'],$converted[1]['dob'],$converted[2]['name'],$converted[2]['address'],$converted[2]['relation'],$converted[2]['share'],$converted[2]['dob'],$converted[3]['name'],$converted[3]['address'],$converted[3]['relation'],$converted[3]['share'],$converted[3]['dob'],$converted[4]['name'],$converted[4]['address'],$converted[4]['relation'],$converted[4]['share'],$converted[4]['dob'],$converted[0]['name']);
			
			$before_guardian_string = str_replace($old_1,$new_1,$before_final_replace);
			
			$replace_guardian_string = array();
			$guardian_counter = 1;
			for($iCounter = 0; $iCounter < 5; $iCounter++)
			{
				if($converted[$iCounter]["guardian_name"] == "")
				{
					$replace_guardian_string[$iCounter] = "";
				}
				else
				{
					$make_guardian_string_1 = str_replace("{Sr.No}",$iCounter + 1,$guardian_string);
					$make_guardian_string_2 = str_replace("{Guardian_Name}",$converted[$iCounter]["guardian_name"],$make_guardian_string_1);
					$make_guardian_string_3 = str_replace("{No}",$guardian_counter,$make_guardian_string_2);
					$replace_guardian_string[$iCounter] = $make_guardian_string_3;
					$guardian_counter++;
				}
			}
			
			$old_2 = array("{Guardian_String_1}","{Guardian_String_2}","{Guardian_String_3}","{Guardian_String_4}","{Guardian_String_5}");
			$new_2 = array($replace_guardian_string[0],$replace_guardian_string[1],$replace_guardian_string[2],$replace_guardian_string[3],$replace_guardian_string[4]);
			$final1 = str_replace($old_2,$new_2,$before_guardian_string);
			
			if($to_pass['triplicate'] == 1)
			{
				$make_duplicate = str_replace("ORIGINAL","DUPLICATE",$final);
				$make_triplicate = str_replace("ORIGINAL","TRIPLICATE",$final);
				$final1 = $final1."<br>".$make_duplicate."<br>".$make_triplicate;
			}
									
			//get member_id from unit_id
			$sql09 = "SELECT `member_id` FROM `member_main` WHERE `unit` = '".$to_pass['unit_id']."' AND `ownership_status` = '1'";
			$sql99 = $this->m_dbConn->select($sql09);
			
			//change status of previous nomination forms to 'N'
			//$sql_update_01 = "UPDATE `nomination_form` SET `status` = 'N' WHERE `member_id` = '".$sql99[0]['member_id']."'";
			//$sql_update_01_res = $this->m_dbConn->update($sql_update_01);
			
			//insert nomination info into tables
			$sql08 = "INSERT INTO `nomination_form`(`member_id`,`witness_name_one`,`witness_add_one`,`witness_name_two`,`witness_add_two`,`status`) VALUES('".$sql99[0]['member_id']."','".$to_pass['witness_name_1']."','".$to_pass['witness_address_1']."','".$to_pass['witness_name_2']."','".$to_pass['witness_address_2']."','Y')";
			$sql88 = $this->m_dbConn->insert($sql08);
			
			for($iNomineeCnt = 0; $iNomineeCnt < sizeof($converted); $iNomineeCnt++)
			{
				if(strlen($converted[$iNomineeCnt]['name']) > 0)
				{
					if(strlen($converted[$iNomineeCnt]['guardian_name']) > 0)
					{
						$is_minor = '1';
					}
					else
					{
						$is_minor = '0';
					}
					
					$sql10 = "INSERT INTO `nomination_details`(`nomination_id`,`nominee_name`,`nominee_address`,`relation`,`percentage_share`,`DOB`,`is_minor`,`guardian_name`) VALUES('".$sql88."','".$converted[$iNomineeCnt]['name']."','".$converted[$iNomineeCnt]['address']."','".$converted[$iNomineeCnt]['relation']."','".$converted[$iNomineeCnt]['share']."','".getDBFormatDate($converted[$iNomineeCnt]['dob'])."','".$is_minor."','".$converted[$iNomineeCnt]['guardian_name']."')";
					$sql010 = $this->m_dbConn->insert($sql10);
				}
			}
			//tables created in Acme Amay database
			
			/*echo "<pre>";
			print_r($converted);
			echo "</pre>";
			
			for($i = 0; $i < sizeof($converted); $i++)
			{
				echo "name: ".$i." : ".strlen($converted[$i]['name']);
				echo "address: ".$i." : ".strlen($converted[$i]['address']);
				echo "relation: ".$i." : ".strlen($converted[$i]['relation']);
				echo "share: ".$i." : ".strlen($converted[$i]['share']);
				echo "dob: ".$i." : ".strlen($converted[$i]['dob']);
				echo "guardian_name: ".$i." : ".strlen($converted[$i]['guardian_name']);
			}*/
			$string = 'No. '.$sql88;
			$final2 = preg_replace('/No/',$string,$final1,1);
			//echo $final2;
			
			$final = array();
			array_push($final,$final2,$sql88);
			
		}
		else if($to_pass['template_id'] == 51) //property tax certificate
		{
			$templates = $this->fetch_template_details($to_pass['template_id']);
			
			$sql01 = "SELECT `society_name` FROM `society` WHERE `society_id` = '".$_SESSION['society_id']."'";
			$sql11 = $this->m_dbConn->select($sql01);
			
			$sql02 = "SELECT mm.`unit`, mm.`owner_name`,u.`unit_id`,u.`unit_no` FROM `member_main` mm, `unit` u WHERE u.`unit_id` = mm.`unit` AND u.`unit_id` = '".$to_pass['flat_id']."' AND `ownership_status` = 1";
			$sql22 = $this->m_dbConn->select($sql02);
			
			$sql04 = "SELECT `APP_DEFAULT_PROPERTY_TAX_LEDGER` FROM `appdefault` WHERE `APP_DEFAULT_SOCIETY` = '".$_SESSION['society_id']."'";
			$sql44 = $this->m_dbConn->select($sql04);
			
			if($sql44[0]['APP_DEFAULT_PROPERTY_TAX_LEDGER'] != $to_pass['ledger_id'])
			{
				$update = "UPDATE `appdefault` SET `APP_DEFAULT_PROPERTY_TAX_LEDGER` = '".$to_pass['ledger_id']."' WHERE `APP_DEFAULT_SOCIETY` = '".$_SESSION['society_id']."'";
				$update01 = $this->m_dbConn->select($update);
				
				$sql03 = "SELECT * FROM `ledger` WHERE `id` = '".$to_pass['ledger_id']."'";
				$sql33 = $this->m_dbConn->select($sql03);
			}
			else if($sql44[0]['APP_DEFAULT_PROPERTY_TAX_LEDGER'] == $to_pass['ledger_id'])
			{
				$sql03 = "SELECT * FROM `ledger` WHERE `id` = '".$to_pass['ledger_id']."'";
				$sql33 = $this->m_dbConn->select($sql03);
			}		
			
			$sql05 = "SELECT `ID` FROM `period` WHERE `YearID` = '".$_SESSION['default_year']."' AND '".getDBFormatDate(date("d-m-Y"))."' > `BeginingDate`";
			$sql55 = $this->m_dbConn->select($sql05);
			/*echo "<pre>";
			print_r($sql55);
			echo "</pre>";*/
			$periods = "";
			foreach($sql55 as $key)
			{
				foreach($key as $v)
				{
					$periods .= $v.","; 
				}
			}
			$periods = rtrim(trim($periods),',');
			//echo $periods;
			if($to_pass['society_name'] == 1)
			{
				$sql07 = "SELECT `society_name`,`registration_no`,`society_add` FROM `society` WHERE `society_id` = '".$_SESSION['society_id']."'";
				$sql77 = $this->m_dbConn->select($sql07);
				
				$society_table = "<table align='center'><tr><th style='text-align:center'>".$sql77[0]['society_name']."</th></tr><tr><th style='text-align:center'>".$sql77[0]['registration_no']."</th></tr><tr><th style='text-align:center'>".$sql77[0]['society_add']."</th></tr></table>";
			}
			//SELECT v.`RefNo`, v.`RefTableID`, v.`To`, v.`Credit`, bd.`UnitID`, bd.`PeriodID`,p.`Type`,p.`BeginingDate`,p.`EndingDate` FROM `voucher` v, `billdetails` bd, `period` p WHERE v.`To` = '95' AND v.`RefTableID` = 1 AND bd.`UnitID` = '16' AND bd.`PeriodID` IN (14,15,16,17,18,19,20,21,22,23,24,25) AND v.`RefNo` = bd.`ID` AND bd.`PeriodID` = p.`ID`
			$sql06 = "SELECT v.`RefNo`, v.`RefTableID`, v.`To`, v.`Credit`, bd.`UnitID`, bd.`PeriodID`,p.`Type`,p.`BeginingDate`,p.`EndingDate` FROM `voucher` v, `billdetails` bd, `period` p WHERE v.`To` = '".$to_pass['ledger_id']."' AND v.`RefTableID` = 1 AND bd.`UnitID` = '".$to_pass['flat_id']."' AND bd.`PeriodID` IN (".$periods.") AND v.`RefNo` = bd.`ID` AND bd.`PeriodID` = p.`ID` ORDER BY bd.`PeriodID`";
			$sql66 = $this->m_dbConn->select($sql06);

			$table = "<table border='1' align='center'><tr><th style='text-align:center;'>Month</th><th style='text-align:center;'>Amount</th></tr>";

			if(sizeof($sql66) > 0)
			{
				for($z = 0; $z < sizeof($sql66); $z++)
				{
					$total_amount = $total_amount + $sql66[$z]['Credit'];
				}
				//echo "Total: ".$total_amount;
				$amt_in_words = $this->obj_Utility->convert_number_to_words($total_amount);
				//use function convert_number_to_words() from utility.class.php to convert Amount to words
				$period_string = date('F Y',strtotime(getDisplayFormatDate($sql66[0]['BeginingDate']))) ." to ".date('F Y',strtotime(getDisplayFormatDate($sql66[(sizeof($sql66) - 1)]['EndingDate'])));				
			
				for($i = 0; $i < sizeof($sql66); $i++)
				{
					$yearOnly=substr($sql66[$i]['BeginingDate'],0,4);
					$table .= "<tr><td style='text-align:center;'>".$sql66[$i]['Type']." - ".$yearOnly."</td><td style='text-align:center;'>".$sql66[$i]['Credit']."</td></tr>";
				}
				
			}
			else
			{
				$total_amount = 0;
				$amt_in_words = "Zero";
				$period_string = "";
				$table .= "<tr><td style='text-align:center;' colspan='2'>No Bills found for the default year.</td></tr>";
			}
			$table .= "<td style='text-align:center;'><b>Total</b></td><td style='text-align:center;'>".$total_amount."</td></table>";
			
			$old = array("{Member_Name}","{Flat_No}","{Ledger_Name}","{Society_Name}","{Date}","{Place}","{Amount}","{Amount_in_words}","{Period_Dates}","{Table}");
			$new = array($sql22[0]['owner_name'],$sql22[0]['unit_no'],$sql33[0]['ledger_name'],$sql11[0]['society_name'],date("d-m-Y"),$to_pass['place'],$total_amount,$amt_in_words,$period_string,$table);
			
			if($to_pass['society_name'] == 1)
			{
				$final = $society_table;
				$final .= str_replace($old,$new,$templates['template_data']);
			}
			else
			{
				$final = str_replace($old,$new,$templates['template_data']);
			}
		}
		else if($to_pass['template_id'] == 53) //renovation letter
		{
			$templates = $this->fetch_template_details($to_pass['template_id']);
			
			$sql1 = "SELECT * FROM `society` WHERE `society_id` = '".$_SESSION['society_id']."'";
			$sql1_res = $this->m_dbConn->select($sql1);
			
			$sql2 = "SELECT u.`unit_no`, mm.`primary_owner_name`, w.`wing` FROM `unit` u, `member_main` mm, `wing` w WHERE u.`unit_id` = mm.`unit` AND w.`wing_id` = mm.`wing_id` AND mm.`ownership_status` = '1' AND u.`unit_id` = '".$to_pass['flat_id']."'";
			$sql2_res = $this->m_dbConn->select($sql2);
			
			//$sql3 = "SELECT * FROM `appdefault_new` WHERE `Property` = 'Garbage Area'";
			//$sql3_res = $this->m_dbConn->select($sql3);
			
			$checkIfPropertyExists = $this->obj_Utility->getAppDefaultProperty('Garbage Area');
			
			if($checkIfPropertyExists == "")
			{
				//$sql4 = "INSERT INTO `appdefault_new`(`Property`,`Value`) VALUES('Garbage Area','".$to_pass['garbage_area']."')";
				//$sql4_res = $this->m_dbConn->insert($sql4);
				
				$insert = $this->obj_Utility->setAppDefaultProperty('Garbage Area',$to_pass['garbage_area']);
			}
			else
			{
				//$sql5 = "UPDATE `appdefault_new` SET `Value` = '".$to_pass['garbage_area']."' WHERE `Property` = 'Garbage Area'";
				//$sql5_res = $this->m_dbConn->update($sql5);
				
				$update02 = $this->obj_Utility->updateAppDefaultProperty('Garbage Area',$to_pass['garbage_area']);
			}
			
			$old = array("{Date}","{Society_Name}","{Flat_No}","{Flat_Owner_Name}","{Wing_No}","{Renovation Text}","{designated area}");
			$new = array(getDisplayFormatDate(date("Y-m-d")),$sql1_res[0]['society_name'],$sql2_res[0]['unit_no'],$sql2_res[0]['primary_owner_name'],$sql2_res[0]['wing'],$to_pass['renovation_text'],$to_pass['garbage_area']);
			
			$final = str_replace($old,$new,$templates['template_data']);
		}
		
		return $final;
		
	}
	
	function fetch_unit_id($rc_id)
	{
		$sql01 = "select UnitID from reversal_credits where ID = '".$rc_id."'";
		$sql11 = $this->m_dbConn->select($sql01);
		
		return $sql11[0]['UnitID'];
	}
	
	function fetch_subject($temp_id)
	{
		$sql01 = "select template_subject from document_templates where id = '".$temp_id."'";
		$sql11 = $this->m_dbConnRoot->select($sql01);
		
		return $sql11[0]['template_subject'];
	}
	
	function fetch_unit_details_and_cc_dets($unit_id)
	{
		$sql01 = "SELECT mm.unit, u.unit_no, u.unit_id, mm.primary_owner_name, u.society_id, s.society_id, s.cc_no, s.cc_date FROM `member_main` mm, `unit` u, `society` s where mm.unit = u.unit_id and u.unit_id = '".$unit_id."' and s.society_id = u.society_id and mm.ownership_status = 1";
		$sql11 = $this->m_dbConn->select($sql01);
		$sql11[0]['cc_date'] = getDisplayFormatDate($sql11[0]['cc_date']);
		
		return $sql11;
	}
	
	function fetch_data_for_nomination($unit_id, $nomination_id)
	{
		if($nomination_id == 0)
		{
			$sql01 = "SELECT u.unit_id, u.unit_no, u.area, u.share_certificate, u.share_certificate_from, u.share_certificate_to, mm.primary_owner_name, mm.owner_name, s.amt_per_share FROM `unit` u, `member_main` mm, `society` s where u.unit_id = '".$unit_id."' and mm.ownership_status = '1' and u.unit_id = mm.unit and u.society_id = s.society_id";
			$sql11 = $this->m_dbConn->select($sql01);
		}
		else
		{
			$sql01 = "SELECT u.unit_id, u.unit_no, u.area, u.share_certificate, u.share_certificate_from, u.share_certificate_to, mm.primary_owner_name, mm.owner_name, s.amt_per_share FROM `unit` u, `member_main` mm, `society` s where u.unit_id = '".$unit_id."' and mm.ownership_status = '1' and u.unit_id = mm.unit and u.society_id = s.society_id";
			$sql11 = $this->m_dbConn->select($sql01);
			
			$sql02 = "SELECT `member_id` FROM `member_main` WHERE `unit` = '".$unit_id."' AND `ownership_status` = '1'";
			$sql22 = $this->m_dbConn->select($sql02);
		
			//$sql03 = "SELECT * FROM `nomination_form` WHERE `member_id` = '".$sql22[0]['member_id']."' AND `status` = 'Y'";
			$sql03 = "SELECT * FROM `nomination_form` WHERE `nomination_id` = '".$nomination_id."'";
			$sql33 = $this->m_dbConn->select($sql03);
		
			$sql04 = "SELECT * FROM `nomination_details` WHERE `nomination_id` = '".$nomination_id."'";
			$sql44 = $this->m_dbConn->select($sql04);
		
			array_push($sql11,$sql33,$sql44);
		}	
		
		return $sql11;
	}
	
	function get_nominations($unit_id)
	{
		$sql1 = "SELECT `member_id` FROM `member_main` WHERE `unit` = '".$unit_id."'";
		$sql1_res = $this->m_dbConn->select($sql1);
		
		$sql2 = "SELECT * FROM `nomination_form` WHERE `member_id` = '".$sql1_res[0]['member_id']."' AND `status` = 'Y'";
		$sql2_res = $this->m_dbConn->select($sql2);
		
		if($sql2_res <> "")
		{				
			$table = "<table id='prev_nominations'><tr style='height:30px'><th style='width:5%'>Edit</th><th style='width:5%'>Delete</th><th style='width:5%'>Nomination ID</th><th style='width:45%'>Nominees Name and Share</th><th style='width:25%'>Witnesses</th><th style='width:15%'>Status</th></tr>";
			for($i = 0; $i < sizeof($sql2_res); $i++)
			{
				$nominees_string = "";
				$sql3 = "SELECT * FROM `nomination_details` WHERE `nomination_id` = '".$sql2_res[$i]['nomination_id']."'";
				$sql3_res = $this->m_dbConn->select($sql3);
			
				$status = "";
				if($sql2_res[$i]['nomination_status'] == 0)
				{
					$status = "Draft";
				}
				else if($sql2_res[$i]['nomination_status'] == 1)
				{
					$status = "Submitting triplicate printed on paper to Managing Committee.";
				}
				else if($sql2_res[$i]['nomination_status'] == 2)
				{
					$status = "Submitted";
				}
				else if($sql2_res[$i]['nomination_status'] == 3)
				{
					$status = "Approved";
				}
				//table tr start here
				//Edit and Status here

				for($j = 0; $j < sizeof($sql3_res); $j++)
				{
					$nominees_string .= $sql3_res[$j]['nominee_name']."(".$sql3_res[$j]['percentage_share']."), ";
				}
				$nominees_string = rtrim($nominees_string,", ");
				
				if($sql2_res[$i]['nomination_status'] == 0)
				{
					$table .= "<tr style='height:30px'><td><a class='hover' id='Edit_".$sql2_res[$i]['nomination_id']."' onclick='display_nominations(".$sql2_res[$i]['nomination_id'].",true)'><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a></td><td><a class='hover' id='Delete_".$sql2_res[$i]['nomination_id']."' onclick='delete_nomination(".$sql2_res[$i]['nomination_id'].");'><img src='images/del.gif' border='0' alt='Delete' style='cursor:pointer;'/></a></td><td>".$sql2_res[$i]['nomination_id']."</td><td>".$nominees_string."</td><td>".$sql2_res[$i]['witness_name_one'].", ".$sql2_res[$i]['witness_name_two']."</td><td>".$status."</td></tr>";
				}
				else
				{
					$table .= "<tr style='height:30px'><td></td><td></td><td>".$sql2_res[$i]['nomination_id']."</td><td>".$nominees_string."</td><td>".$sql2_res[$i]['witness_name_one'].", ".$sql2_res[$i]['witness_name_two']."</td><td>".$status."</td></tr>";
				}
			}
		
			$table .= "</table>";
		}
		else
		{
			$table = "";
		}
		
		return $table;
	}
	
	function submit_form($nomination_id, $status_flag)
	{
		if($status_flag == 1)
		{
			$sql2 = "SELECT `member_id` FROM `nomination_form` WHERE `nomination_id` = '".$nomination_id."'";
			$sql2_res = $this->m_dbConn->select($sql2);
				
			$sql3 = "UPDATE `nomination_form` SET `nomination_status` = '0' WHERE `member_id` = '".$sql2_res[0]['member_id']."' AND `nomination_id` != '".$nomination_id."'";
			$sql3_res = $this->m_dbConn->update($sql3);
		}
		
		$sql1 = "UPDATE `nomination_form` SET `nomination_status` = '".$status_flag."' WHERE `nomination_id` = '".$nomination_id."'";
		$sql1_res = $this->m_dbConn->update($sql1);
		
		return $status_flag;
	}
	
	function delete_nomination($nomination_id)
	{
		$sql1 = "UPDATE `nomination_form` SET `status` = 'N' WHERE `nomination_id` = '".$nomination_id."'";
		$sql1_res = $this->m_dbConn->update($sql1);
		
		return $nomination_id;
	}
	
	function add_head()
	{
		$sql1 = "SELECT * FROM `society` WHERE `society_id` = '".$_SESSION['society_id']."'";
		$sql1_res = $this->m_dbConn->select($sql1);
		
		$table = "<table id='society_table'>
					<tr>
						<td>".$sql1_res[0]['society_name']."</td>
					</tr>
					<tr>
						<td>".$sql1_res[0]['society_add']."</td>
					</tr>
					<tr>
						<td>".$sql1_res[0]['registration_no']."</td>
					</tr>
				  </table>";
				  
		return $table;
	}
	
	function getGarbageArea()
	{
		$sql1 = "SELECT * FROM `appdefault_new` WHERE `Property` = 'Garbage Area' and module_id = '2'";
		$sql1_res = $this->m_dbConn->select($sql1);
		
		if($sql1_res[0]['Value'] <> "")
		{
			$garbage_area = $sql1_res[0]['Value'];
		}
		else
		{
			$garbage_area = "";
		}
		
		return $garbage_area;
	}
	//Vaishali's Code
	//Function to get list of works from appdefaultNew table
	public function getListOfWork()
	{
		$sql1 = "SELECT * FROM `appdefault_new` WHERE `Property` = 'List_Of_Work' and module_id = '2'";
		$sql1_res = $this->m_dbConn->select($sql1);
		
		if($sql1_res[0]['Value'] <> "")
		{
			$listOfWork = $sql1_res[0]['Value'];
		}
		else
		{
			$listOfWork = "";
		}
		$workList = json_decode($listOfWork,true);
		//var_dump($listOfWork);
		return $workList['WorkList'];
	}
	public function getTemplateDetails()
	{
		$sql1 = "SELECT * FROM `appdefault_new` WHERE module_id = '2'";
		$sql1_res = $this->m_dbConn->select($sql1);
		for($i = 1;$i < sizeof($sql1_res);$i++)
		{
			if($sql1_res[$i]['Value'] <> "")
			{
				$template[$i-1] = $sql1_res[$i]['Value'];
			}
			else
			{
				$template[$i-1] = "";
			}
		}
		
		//$workList = json_decode($listOfWork,true);
		return ($template);
	}
	//------------------------------------------------------------NOT IN USE---------------------------------------------------------------------------------
	//Function to add renovation details in database
	/*public function addRenovationDetails()
	{
		/*echo "<pre>";
		print_r($_POST);
		echo "</pre>";*/
		/*echo "<pre>";
		print_r($_FILES);
		echo "</pre>";*/
		/*$listOfWorkArr = $this->getListOfWork();
		$Name = array();
		$docName = array();
		$files = array();
		$sizeOfDoc = 0;
		$workTypeArr = $_POST['workType'];
		//var_dump($workTypeArr);
		//echo $_POST['sizeOfDoc'];
		$j = 0;
		for($i = 0; $i < $_POST['sizeOfDoc'];$i++)
		{
			if($_FILES['userfile']['error'][$i] == "0")
			{
				$files['userfile']['name'][$i] = $_FILES['userfile']['name'][$i];
				$files['userfile']['type'][$i] = $_FILES['userfile']['type'][$i];
				$files['userfile']['tmp_name'][$i] = $_FILES['userfile']['tmp_name'][$i];
				$files['userfile']['error'][$i] = $_FILES['userfile']['error'][$i];
				$docName[$i] = $_FILES['userfile']['name'][$i];
				$sizeOfDoc = $sizeOfDoc + 1;
				$Name[$j] = $workTypeArr[$i]; 
				$j++;
			}
		}
		
		$workType = $workTypeArr[0];
		for($i = 0;$i < sizeof($workTypeArr)-1;$i++)
		{
			$workType .= ",";
			$workType .= $workTypeArr[$i+1];
		}
		$labourerNameArr = $_POST['labourerName'];
		$labourer = $labourerNameArr[0];
		for($i = 0;$i < sizeof($labourerNameArr)-1;$i++)
		{
			$labourer .= ",";
			$labourer .= $labourerNameArr[$i+1]; 
		}
		$temp_labourer = "<ol>";
		if($labourerNameArr[0] == "")
		{
			$temp_labourer .= "<li>____________________</li>";
		}
		else
		{
			$temp_labourer .= "<li>".$labourerNameArr[0]."</li>";
		}
		for($i = 1;$i < sizeof($labourerNameArr);$i++)
		{
			$temp_labourer .= "<li>".$labourerNameArr[$i]."</li>";
		}
		$maxLabourer = (int)$_POST['MaxNoOfLabourer'] - sizeof($labourerNameArr);
		for($k = 0; $k < $maxLabourer; $k++)
		{
			//echo "k :".$k;
			$temp_labourer .= "<li>____________________</li>";
		}
		$temp_labourer .= "</ol>";
		//echo $labourer;
		if($_POST['location'] == "Inside")
		{
			$location = "1";
		}
		else
		{
			$location = "2";
		}
		$todayDate = getCurrentTimeStamp();
		
		$sql2 = "INSERT INTO `renovation_details` (`request_id`, `unit_id`, `application_date`, `start_date`, `end_date`, `work_details`, `type_of_work`, `location`, `contractor_name`,`ContractorContactNo`, `contractor_address`, `max_labourer`, `labourer_name`) VALUES ('".$_SESSION['renovation_service_request_id']."', '".$_POST['renovationUnitId']."', '".$todayDate['Date']."', '".getDBFormatDate($_POST['startDate'])."', '".getDBFormatDate($_POST['endDate'])."', '".$_POST['renovation_text']."', '".$workType."','".$location."', '".$_POST['contractorName']."','".$_POST['contractorContact']."','".$_POST['contractorAddress']."', '".$_POST['MaxNoOfLabourer']."', '".$labourer."');";
		$sql2_res = $this->m_dbConn->insert($sql2);
		//var_dump($Name);
		//Inserting renovation details in approval_details table..
		$sql7 = "insert into `approval_details` (`referenceId`, module_id) values ('".$sql2_res."','".RENOVATION_SOURCE_TABLE_ID."');";
		$sql7_res = $this->m_dbConn->insert($sql7);
		$resUnit = $this->obj_Utility->GetUnitDesc($_POST['renovationUnitId']);
		$unitNo = $resUnit[0]['unit_no'];
		
		//GDRive code-------------------------------------------------------------------------------------------------------------------
		/*$resSociety = $this->GetGDriveDetails();
		$sGDrive_W2S_ID = $resSociety["0"]["GDrive_W2S_ID"];
		if($sGDrive_W2S_ID != "")
		{
			$ObjGDrive = new GDrive($this->m_dbConn, "0", $sGDrive_W2S_ID, 0);
			
			for($i = 0;$i < $sizeOfDoc;$i++)
			{
				$UploadedFiles = $ObjGDrive->UploadFiles($files['userfile']['name'][$i],$files['userfile']['name'][$i], $files['userfile']['type'][$i], $files['userfile']['tmp_name'][$i], "Renovation_Request","",$unitNo,"", $sGDrive_W2S_ID,$unitNo, "0");
				$sStatus = "1";
				$sMode = "2";
				$sFileName = $UploadedFiles["response"]["id"];
				if($sMode == "1")
				{
					$random_name = $sFileName;
				} 
				else if($sMode == "2")
				{
					$docGDriveID = $sFileName;
				}
				else
				{
				}
				$sDocVersion = '2';
				if($GdriveDocID != "")
				{
					$sDocVersion = '2';
				}
				$insert_query="insert into `documents` (`Name`, `Unit_Id`,`refID`,`Category`, `Note`,`Document`,`source_table`,`doc_type_id`,`doc_version`,`attachment_gdrive_id`) values ('".$Name[$i]."', '".$_POST['renovationUnitId']."','".$sql2_res."','0', '','".$docName[$i]."','".RENOVATION_SOURCE_TABLE_ID."','".$_SESSION['RENOVATION_DOC_ID']."','".$sDocVersion."','".$docGDriveID."')";
				$data=$this->m_dbConn->insert($insert_query);
			}
		}
		---------------------------------------------------------------------------------------------------------------------
		
		
		$target_path = "Uploaded_Documents/"; 
		$target_path = $target_path . basename($_FILES['file']['name']);
		 for($i = 0;$i < $sizeOfDoc;$i++)
		{
			if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) 
			{
				
				$selectDocuments = "update `documents` set status = 'N' where refId = '".$renovationId."' and source_table = '".RENOVATION_SOURCE_TABLE_ID."'";
				$selectResult = $db->select($selectDocuments);

				if($selectResult <> '')
				{
					$attach_doc = $selectResult[0]['attached_doc'];

					if($img <> '')
					{
						//$imgArray = explode(',', $img);

						//array_push($imgArray, basename($_FILES['file']['name']));

						$updateDocument = "Update `spr_document` SET `attached_doc` = '" . basename($_FILES['file']['name']) . "' where service_prd_reg_id = '" . $Service_prd_id . "' and `document_id`='" .$Document_id. "'";
					}
					else
					{
						$updateDocument = "Update `spr_document` SET `attached_doc` = '" . basename($_FILES['file']['name']) . "' where service_prd_reg_id = '" . $Service_prd_id . "' and `document_id`='" .$Document_id. "'";
					}

					$updateResult = $dbConnRoot->update($updateDocument);
				}

		    	echo "Upload and move success";
			}
			 
			else 
			{
				echo $target_path;
		    	echo "There was an error uploading the file, please try again!";
			}
		}
		$templates = $this->fetch_template_details("53");
		//var_dump($templates);
		$sql3 = "SELECT * FROM `society` WHERE `society_id` = '".$_SESSION['society_id']."'";
		$sql3_res = $this->m_dbConn->select($sql3);
			
		$sql4 = "SELECT u.`unit_no`, mm.`primary_owner_name`, w.`wing`,mm.`mob` FROM `unit` u, `member_main` mm, `wing` w WHERE u.`unit_id` = mm.`unit` AND w.`wing_id` = mm.`wing_id` AND mm.`ownership_status` = '1' AND u.`unit_id` = '".$_POST['renovationUnitId']."'";
		$sql4_res = $this->m_dbConn->select($sql4);
		$checkIfPropertyExists = $this->obj_Utility->getAppDefaultProperty('Garbage Area');
		if($checkIfPropertyExists == "")
		{
			$insert = $this->obj_Utility->setAppDefaultProperty('Garbage Area',$_POST['garbage_area']);
		}
		else
		{
			$update02 = $this->obj_Utility->updateAppDefaultProperty('Garbage Area',$_POST['garbage_area']);
		}
		$societyAddress = explode(",",$sql3_res[0]['society_add']);
		/*echo "<pre>";
		print_r($societyAddress);
		echo "</pre>";*/
		
		/*$templateDetails = $this->getTemplateDetails();
		//echo "<pre>";
		//print_r($templateDetails);
		//echo "</pre>";
		$sql5 = "select * from documents where refId = '".$sql2_res."' and source_table = '".RENOVATION_SOURCE_TABLE_ID."'";
		$sql5_res = $this->m_dbConn->select($sql5);
		//var_dump($sql5_res);
		for($j=0;$j<sizeof($sql5_res);$j++)
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
		$typeOfWorkList = "<ol>";
		$check = false;
		for($m = 0; $m < sizeof($workTypeArr);$m++)
		{
			$typeOfWorkList .= "<li>".$workTypeArr[$m]."</li>";
		}
		$typeOfWorkList .= "</ol>";
		//var_dump($typeOfWorkList);
		//-------------------------------Old Template coding--------------------------------------------------------------------------//
		
		/*$old = array("{header}","{Date}","{Society_Name}","{Address1}","{Address2}","{Flat_Owner_Name}","{Wing_No}","{Flat_No}","{Work_Type}","{Work_Details}","{Start_Date}","{End_Date}","{contractor_name}","{contractor_address}","{max_labourer}","{labourer_name}","{terms_condition}","{Flat_Owner_Name}","{footer}","{Society_Name}");
		$new = array($templateDetails[1],getDisplayFormatDate(date("Y-m-d")),$sql3_res[0]['society_name'],$societyAddress[0].",".$societyAddress[1],$societyAddress[2].",".$societyAddress[3],$sql4_res[0]['primary_owner_name'],$sql4_res[0]['wing'],$sql4_res[0]['unit_no'],$typeOfWorkList,$_POST['renovation_text'],getDisplayFormatDate($_POST['startDate']),getDisplayFormatDate($_POST['endDate']),$_POST['contractorName'],$_POST['contractorAddress'],$_POST['MaxNoOfLabourer'],$temp_labourer,$templateDetails[3],$sql4_res[0]['primary_owner_name'],$templateDetails[2],$sql3_res[0]['society_name']);
		$final = str_replace($old,$new,$templates['template_data']);*/
		
		//-------------------------------New Template coding--------------------------------------------------------------------------//
		/*$old = array("{header}","{Date}","{Owner_name}","{Society_Name}","{Society_Name}","{Flat_No}","{Address1}","{Wing}","{Address2}","{Owner_No}","{Flat_No}","{Work_Type}","{Work_Details}","{Start_Date}","{End_Date}","{contractor_name}","{Contact_No}","{contractor_address}","{max_labourer}","{labourer_name}","{terms_condition}","{Flat_Owner_Name}","{footer}");
		$new = array($templateDetails[1],getDisplayFormatDate(date("Y-m-d")),$sql4_res[0]['primary_owner_name'],$sql3_res[0]['society_name'],$sql3_res[0]['society_name'],$sql4_res[0]['unit_no'],$societyAddress[0].",".$societyAddress[1],$sql4_res[0]['wing'],$societyAddress[2].",".$societyAddress[3],$sql4_res[0]['mob'],$sql4_res[0]['unit_no'],$typeOfWorkList,$_POST['renovation_text'],getDisplayFormatDate($_POST['startDate']),getDisplayFormatDate($_POST['endDate']),$_POST['contractorName'],$_POST['contractorContact'],$_POST['contractorAddress'],$_POST['MaxNoOfLabourer'],$temp_labourer,$templateDetails[3],$sql4_res[0]['primary_owner_name'],$templateDetails[2]);
		$final = str_replace($old,$new,$templates['template_data']);
		
		//echo $final;
		$sql6 = "update `renovation_details` SET `final_template`= '".$final."' where Id = '".$sql2_res."';";
		$sql6_res = $this->m_dbConn->update($sql6);
		$this->actionPage = "../document_maker.php?View=MEMBER&temp=9&rId=".$sql2_res;
	}*/
	//--------------------------------------------------------------------------------------------------------------------------------------------------------//
	
	//Query to add columns to society table
	//ALTER TABLE `society` ADD `cc_no` VARCHAR(100) NOT NULL , ADD `cc_date` DATE NOT NULL , ADD `SMS_Reminder_Days` INT(11) NOT NULL DEFAULT '1' ;
	//ALTER TABLE `society` ADD `amt_per_share` INT(11) NOT NULL ;
	//ALTER TABLE `society` CHANGE `society_add` `society_add_line_1` VARCHAR(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
	//ALTER TABLE `society` ADD `society_add_line_2` VARCHAR(250) NOT NULL AFTER `society_add_line_1`;
	public function getFinalTemplate($renovationId,$role)
	{
		$sql1 = "select `final_template` from renovation_details where Id = '".$renovationId."';";
		$sql1_res = $this->m_dbConn->select($sql1);
		$finalTemplate = "";
		$finalTemplate2 = $sql1_res[0]['final_template'];
		//echo $finalTemplate;
		//var_dump($sql1_res[0]['final_template']);
		if($role != ROLE_MEMBER)
		{
			$sql2 = "SELECT `Value` from appdefault_new where `Property` = 'Renovation_template_footer' and `module_id` = '2';";
			$sql2_res = $this->m_dbConn->select($sql2);
			$finalTemplate = $finalTemplate2."<br>".$sql2_res[0]['Value'];
		}
		else
		{
			//echo "in else";
			/*$sql2 = "Select `Value` from appdefault_new where `Property` = 'RenovationRequestThankYouNote';";
			$sql2_res = $this->m_dbConn->select($sql2);
			$template = $sql2_res[0]['Value'];
			$sql3 = "Select u.`unit_no`,s.`request_no` from renovation_details as rr, service_request as s, unit as u where rr.`request_id` = s.`request_id` and rr.`Id` = '".$renovationId."' and u.`unit_id` = rr.`unit_id`";
			$sql3_res = $this->m_dbConn->select($sql3);
			$old = array("{FLAT_NO}","{REQUEST_NO}");
			$new = array($sql3_res[0]['unit_no'],$sql3_res[0]['request_no']);
			$finalTemplate3 = str_replace($old,$new,$template);*/
			//echo $finalTemplate;
			$societyHead = $this->getSocietyHeader();
			///echo $societyHead;
			//echo $finalTemplate2;
			$finalTemplate = $societyHead."".$finalTemplate2;
		}
		//echo $finalTemplate;
		$finalTemplate = str_replace('"',"'",$finalTemplate);
		//echo $finalTemplate;
		return ($finalTemplate);
	}
	public function GetGDriveDetails()
	{
		$sqlSelect = "select GDrive_W2S_ID,GDrive_Credentials,GDrive_UserID from `society`";
		//echo $sqlSelect;
		//print_r($m_dbConn);
		$res = $this->m_dbConn->select($sqlSelect);
		return $res;
	}
	public function getRenovationDocument($renovationId)
	{
		$sql1 = "select * from documents where refId = '".$renovationId."' and source_table = '2' and `doc_type_id` = '9' and status = 'Y'";
		$sql1_res = $this->m_dbConn->select($sql1);
		for($j=0;$j<sizeof($sql1_res);$j++)
		{
			$doc_version=$sql1_res[$j]['doc_version'];
			$URL = "";
	    	$gdrive_id = $sql1_res[$j]['attachment_gdrive_id'];
	        if($doc_version == "1")
	        {
	        	$URL = "Uploaded_Documents/". $sql1_res[$j]["Document"];
	        }
	        else if($doc_version == "2")
	        {
	        	if($gdrive_id == "" || $gdrive_id == "-")
	            {
	            	$URL = "Uploaded_Documents/". $sql1_res[$j]["Document"];
	            }
	            else
	            {
	            	$URL = "https://drive.google.com/file/d/". $gdrive_id."/view";
	            }
	       	}
		   	$sql1_res[$j]['documentLink'] = $URL;
		}
		//var_dump($sql1_res);
		if(sizeof($sql1_res) > 0)
		{
			$data = "<strong>Documents Attached:<ul>";
			for($i = 0;$i < sizeof($sql1_res); $i++)
			{
				$data .= "<ol>".($i+1).".&nbsp;<a href = '".$sql1_res[$i]['documentLink']."' target='_blank'>".$sql1_res[$i]['Name']."</a></ol>";
			}
		}
		else
		{
			$data = "<ul><strong><ol>No document attached.</ol>";
		}
		$data .= "</ul></strong>";
		
		return $data;
	}
	public function updateAddressProofStatus($serviceRequestId,$addressProofId,$type)
	{
		//$sqlRR = "Select rr.`request_id`, src.`email`, src.`email_cc` from renovation_details as rr, service_request as sr, servicerequest_category as src where rr.`Id` = '".$renovationId."' and rr.`status` ='Y' and sr.`request_id` = rr.`request_id` and sr.`category` = '".$_SESSION['RENOVATION_DOC_ID']."' and sr.`category` = src.`ID`";
		$sqlRR = "Select src.`email`, src.`email_cc` from service_request as sr, servicerequest_category as src where sr.`category` = '".$_SESSION['ADDRESS_PROOF_ID']."' and sr.`category` = src.`ID`";
		$sqlRR_res = $this->m_dbConn->select($sqlRR);
		$SqlmemberEmail = "select mof.`other_email` from mem_other_family as mof, member_main as mm, service_request as sr where sr.`unit_id` = mm.`unit` and mm.`member_id` = mof.`member_id` and mm.`ownership_status` = '1' and sr.`request_id` = '".$serviceRequestId."'";
		$memberEmail = $this->m_dbConn->select($SqlmemberEmail);
		$approvalLevel = $this->getApprovalLevel("4");
		//var_dump($sqlRR_res);
		if($type == "verify")
		{
			$sql1 = "Select m.`role` from login as l,mapping as m where l.login_id = '".$_SESSION['login_id']."' and l.`current_mapping` = m.`id` ";
			$verifiedByDesignation = $this->m_dbConnRoot->select($sql1);
			//var_dump($verifiedByDesignation);
			$sql2 = "update approval_details set `verifiedById` = '".$_SESSION['login_id']."',`verifiedByDesignation`='".$verifiedByDesignation[0]['role']."',verifiedStatus = 'Y' where referenceId = '".$addressProofId."' and module_id='".ADDRESSPROOF_SOURCE_TABLE_ID."';";
			$sql2_res = $this->m_dbConn->update($sql2);
			//var_dump($sql2_res);
			$sql3 = "update service_request set `status` = 'Verified' where request_id = '".$serviceRequestId."';";
			$sql3_res = $this->m_dbConn->update($sql3);
			//var_dump($sql3_res);
		}
		else if($type == "approve")
		{
			//echo "in approve";
		       $sql1 = "select * from approval_details where referenceId = '".$addressProofId."' and module_id = '".ADDRESSPROOF_SOURCE_TABLE_ID."';";
			$sql1_res = $this->m_dbConn->select($sql1);
			var_dump($sql1_res);
			if($sql1_res[0]['firstLevelApprovalStatus'] == 'Y')
			{
				//echo "in if";
				$sql3 = "Select m.`role` from login as l,mapping as m where l.login_id = '".$_SESSION['login_id']."' and l.`current_mapping` = m.`id`";
				$approvedByDesignation = $this->m_dbConnRoot->select($sql3);
				$sql2 = "update approval_details set `secondApprovalById` = '".$_SESSION['login_id']."',`secondApprovalByDesignation`='".$approvedByDesignation[0]['role']."', `secondLevelApprovalStatus` = 'Y' where referenceId = '".$addressProofId."' and module_id='".ADDRESSPROOF_SOURCE_TABLE_ID."';";
				$sql2_res = $this->m_dbConn->update($sql2);
				$sql3 = "update service_request set `status` = 'Approved' where request_id = '".$serviceRequestId."';";
				$sql3_res = $this->m_dbConn->update($sql3);
			}
			else
			{
			//	echo "in else";
				$sql3 = "Select m.`role` from login as l,mapping as m where l.login_id = '".$_SESSION['login_id']."' and l.`current_mapping` = m.`id`";
				$approvedByDesignation = $this->m_dbConnRoot->select($sql3);
				echo $sql2 = "update `approval_details` set `firstApprovalById` = '".$_SESSION['login_id']."',`firstApprovalByDesignation`='".$approvedByDesignation[0]['role']."', `firstLevelApprovalStatus` = 'Y' where referenceId = '".$addressProofId."' and module_id='".ADDRESSPROOF_SOURCE_TABLE_ID."';";
				$sql2_res = $this->m_dbConn->update($sql2);
				if($approvalLevel == "2")
				{
					$sql3 = "update service_request set `status` = 'Waiting for Approval' where request_id = '".$serviceRequestId."';";
				}
				else
				{
					$sql3 = "update service_request set `status` = 'Approved' where request_id = '".$serviceRequestId."';";
				}
				$sql3_res = $this->m_dbConn->update($sql3);
			}
		}
		$this->sendEmail($serviceRequestId,$memberEmail[0]['other_email'],$sqlRR_res[0]['email'],$sqlRR_res[0]['email_cc'],$type);
		return($sql2_res);
	}
	public function updateRenovationStatus($renovationId,$type)
	{
		$sqlRR = "Select rr.`request_id`, src.`email`, src.`email_cc` from renovation_details as rr, service_request as sr, servicerequest_category as src where rr.`Id` = '".$renovationId."' and rr.`status` ='Y' and sr.`request_id` = rr.`request_id` and sr.`category` = '".$_SESSION['RENOVATION_DOC_ID']."' and sr.`category` = src.`ID`";
		$sqlRR_res = $this->m_dbConn->select($sqlRR);
		$SqlmemberEmail = "select mof.`other_email` from mem_other_family as mof, member_main as mm, service_request as sr where sr.`unit_id` = mm.`unit` and mm.`member_id` = mof.`member_id` and mm.`ownership_status` = '1' and sr.`request_id` = '".$sqlRR_res[0]['request_id']."'";
		$memberEmail = $this->m_dbConn->select($SqlmemberEmail);
		$approvalLevel = $this->getApprovalLevel("2");
		//var_dump($sqlRR_res);
		if($type == "verify")
		{
			$sql1 = "Select m.`role` from login as l,mapping as m where l.login_id = '".$_SESSION['login_id']."' and l.`current_mapping` = m.`id` ";
			$verifiedByDesignation = $this->m_dbConnRoot->select($sql1);
			$sql2 = "update approval_details set `verifiedById` = '".$_SESSION['login_id']."',`verifiedByDesignation`='".$verifiedByDesignation[0]['role']."',verifiedStatus = 'Y' where referenceId = '".$renovationId."' and module_id='".RENOVATION_SOURCE_TABLE_ID."';";
			$sql2_res = $this->m_dbConn->update($sql2);
			
			$sql3 = "update service_request set `status` = 'Verified' where request_id = '".$sqlRR_res[0]['request_id']."';";
			$sql3_res = $this->m_dbConn->update($sql3);
		}
		else if($type == "approve")
		{
			//echo "in approve";
			$sql1 = "select * from approval_details where referenceId = '".$renovationId."' and module_id = '".RENOVATION_SOURCE_TABLE_ID."';";
			$sql1_res = $this->m_dbConn->select($sql1);
			//var_dump($sql1_res);
			if($sql1_res[0]['firstLevelApprovalStatus'] == 'Y')
			{
				//echo "in if";
				$sql3 = "Select m.`role` from login as l,mapping as m where l.login_id = '".$_SESSION['login_id']."' and l.`current_mapping` = m.`id`";
				$approvedByDesignation = $this->m_dbConnRoot->select($sql3);
				$sql2 = "update approval_details set `secondApprovalById` = '".$_SESSION['login_id']."',`secondApprovalByDesignation`='".$approvedByDesignation[0]['role']."', `secondLevelApprovalStatus` = 'Y' where referenceId = '".$renovationId."' and module_id='".RENOVATION_SOURCE_TABLE_ID."';";
				$sql2_res = $this->m_dbConn->update($sql2);
				$sql3 = "update service_request set `status` = 'Approved' where request_id = '".$sqlRR_res[0]['request_id']."';";
				$sql3_res = $this->m_dbConn->update($sql3);
			}
			else
			{
				///echo "in else";
				$sql3 = "Select m.`role` from login as l,mapping as m where l.login_id = '".$_SESSION['login_id']."' and l.`current_mapping` = m.`id`";
				$approvedByDesignation = $this->m_dbConnRoot->select($sql3);
				$sql2 = "update `approval_details` set `firstApprovalById` = '".$_SESSION['login_id']."',`firstApprovalByDesignation`='".$approvedByDesignation[0]['role']."', `firstLevelApprovalStatus` = 'Y' where referenceId = '".$renovationId."' and module_id='".RENOVATION_SOURCE_TABLE_ID."';";
				$sql2_res = $this->m_dbConn->update($sql2);
				if($approvalLevel == "2")
				{
					$sql3 = "update service_request set `status` = 'Waiting for Approval' where request_id = '".$sqlRR_res[0]['request_id']."';";
				}
				else
				{
					$sql3 = "update service_request set `status` = 'Approved' where request_id = '".$sqlRR_res[0]['request_id']."';";
				}
				$sql3_res = $this->m_dbConn->update($sql3);
			}
		}
		$this->sendEmail($sqlRR_res[0]['request_id'],$memberEmail[0]['other_email'],$sqlRR_res[0]['email'],$sqlRR_res[0]['email_cc'],$type);
		return($sql2_res);
	}
	public function sendEmail($requestId,$email,$catEmail = '',$catEmailCC = '', $status)
	{	
		$details = $this->obj_serviceRequest->getViewDetails($requestNo,true);
		$CategoryDetails = $this->obj_serviceRequest->GetCategoryDetails( $details[0]['category']);
		
		date_default_timezone_set('Asia/Kolkata');
		try
	  	{
		  $mailSubject = "[SR#".$requestNo."] - ".substr(strip_tags($details[0]['summery']),0,50)." - ".$status;
		  $Raisename=$details[0]['reportedby'];
		  $raisedtimestamp = strtotime($details[0]['timestamp']);
		  $updatedtimestamp = strtotime($details[sizeof($details)-1]['timestamp']);
		  $url="<a href='http://way2society.com/viewrequest.php?rq=".$requestNo. "'>http://way2society.com/viewrequest.php?rq=".$requestNo. "</a>";
		
		$mailBody = '<table border="black" style="border-collapse:collapse;" cellpadding="10px">
							<tr> <td colspan="3"> <b>Service Request [SR#'.$requestNo.'] Status: '.$type.' </b> </td></tr>   							
							<tr> <td style="width:30%;border-right:none;"><b>Raised By</b></td><td style="width:10%;border-left:none;"> : </td><td style="width:60%;">'.$Raisename.'<br/>'.date("d-m-Y (g:i:s a)", $raisedtimestamp).'</td></tr>
							<tr><td style="border-right:none;"><b>Category</b></td><td style="border-left:none;"> : </td><td>'.$CategoryDetails[0]['category'].'</td></tr>
							<tr><td style="border-right:none;"><b>Priority</b></td><td style="border-left:none;"> : </td><td>'.$details[0]['priority'].'</td></tr>
    						<tr><td style="border-right:none;"><b>Status</b></td><td style="border-left:none;"> : </td><td>'.$status.'</td></tr>
    						
							<tr><td style="border-right:none;"><b>Subject</b></td><td style="border-left:none;"> : </td><td>'.nl2br(htmlentities($details[0]['summery'], ENT_QUOTES, 'UTF-8')).'</td></tr>
							<tr><td style="border-right:none;"><b>Description</b></td><td style="border-left:none;"> : </td><td>'.$desc.'</td></tr>
							     
						</table><br />'	;	
		
		$mailBody .="You may view or update this service request by copying below link to browser or by clicking here<br />".$url;
	
		  $bccArray = array();
		  $bccArray[0]=$email;
		  $bccArray[1]=$catEmail;
		  $bccArray[2]=$catEmailCC;
		  $dbname = $_SESSION["dbname"];
		  $society_id = $_SESSION['society_id'];
		  $EMailIDToUse = $this->obj_Utility->GetEmailIDToUse(true, 1, "", "", 0, $dbname, $society_id);
		  $EMailID = $EMailIDToUse['email'];
		  $Password = $EMailIDToUse['password'];
		 // var_dump($EMailIDToUse);
		  $societyEmail = "";	  
		  if($this->objFetchData->objSocietyDetails->sSocietyEmail <> "")
		  {
			 $societyEmail = $this->objFetchData->objSocietyDetails->sSocietyEmail;
		  }
		  else
		  {
			 $societyEmail = "techsupport@way2society.com";
		  }	 
		  echo "societyEmail : " .$societyEmail;
		  
		  $bccArray[3]=$societyEmail;
		  //var_dump($bccArray);
		  if($EMailIDToUse['status'] == 0)				
		 {
			$transport = Swift_SmtpTransport::newInstance('103.50.162.146')
			->setUsername($EMailID)
			->setSourceIp('0.0.0.0')
			->setPassword($Password) ; 
			// Create the message
			$message = Swift_Message::newInstance();
			if($this->objFetchData->objSocietyDetails->sSocietyEmail <> "")
			{
				$message->setBcc(array(
							   $societyEmail => $societyNam
							));
			}
			$message->setTo($bccArray);															
			$message->setReplyTo(array(
						   $societyEmail => $societyName
						));
			$message->setSubject($mailSubject);
			$message->setBody($mailBody);
			$message->setFrom($EMailID, $this->objFetchData->objSocietyDetails->sSocietyName);
			$message->setContentType("text/html");
			//var_dump($message);
			// Send the email
			$mailer = Swift_Mailer::newInstance($transport);
			
			$result = $mailer->send($message);
			
			if($result <> 0)
			{
				echo "<br/>Success";
			}
			else
			{
				echo "<br/>Failed";
			}
		}
		
	  }
	  catch(Exception $exp)
	  {
		echo "Error occured in email sending.";
	  }
	
	}
	public function addRenovationDetails2($societyId,$unitId,$startDate,$endDate,$workDetails,$workTypeArr,$location,$contractorName,$contractorNO,$contractorAdd,$maxLabourer,$labourerNameArr,$srTitle,$srPriority,$srCategory,$loginId,$drawingFiles,$sizeOfDoc)//Function to be called from IONIC
	{
		$sqlName = "Select `member_id`,`name` from login where login_id = '".$loginId."';";
		$sqlName_res = $this->m_dbConnRoot->select($sqlName);
		$obj_LatestCount = new latestCount($this->m_dbConn);
		$request_no = $obj_LatestCount->getLatestRequestNo($societyId);
		$sql4 = "SELECT u.`unit_no`, mm.`primary_owner_name`, w.`wing`,mm.`mob` FROM `unit` u, `member_main` mm, `wing` w WHERE u.`unit_id` = mm.`unit` AND w.`wing_id` = mm.`wing_id` AND mm.`ownership_status` = '1' AND u.`unit_id` = '".$unitId."'";
		$sql4_res = $this->m_dbConn->select($sql4);
		$summery = "This is Renovation request.";
		$sqlsr = "INSERT INTO `service_request` (`request_no`, `society_id`, `reportedby`, `dateofrequest`, `email`, `phone`, `priority`, `category`, `summery`,`img`, `details`, `status`, `unit_id`) VALUES ('".$request_no."', '".$societyId."', '".$sqlName_res[0]['name']."', '".getDBFormatDate(date('d-m-Y'))."', '".$sqlName_res[0]['name']."', '".$sql4_res[0]['mob']."', '".$srPriority."', '".$srCategory."', '".$srTitle."','','".$summery."', 'Raised', '".$unitId."')";					
		$sqlsr_res = $this->m_dbConn->insert($sqlsr);
		$workType = $workTypeArr[0];
		for($i = 0;$i < sizeof($workTypeArr)-1;$i++)
		{
			$workType .= ",";
			$workType .= $workTypeArr[$i+1];
		}
		$temp_labourer = "<ol>";
		if(sizeof($labourerNameArr) > 0)
		{
			$labourer = $labourerNameArr[0];
			for($i = 0;$i < sizeof($labourerNameArr)-1;$i++)
			{
				$labourer .= ",";
				$labourer .= $labourerNameArr[$i+1]; 
			}
		}
		if($labourerNameArr[0] == "")
		{
			$temp_labourer .= "<li>____________________</li>";
		}
		else
		{
			$temp_labourer .= "<li>".$labourerNameArr[0]."</li>";
		}
		for($i = 1;$i < sizeof($labourerNameArr);$i++)
		{
			$temp_labourer .= "<li>".$labourerNameArr[$i]."</li>";
		}
		$reducedMaxLabourer = $maxLabourer - sizeof($labourerNameArr);
		for($k = 0; $k < $reducedMaxLabourer; $k++)
		{
			//echo "k :".$k;
			$temp_labourer .= "<li>____________________</li>";
		}
		$temp_labourer .= "</ol>";
		// $workDetails = "<br>".$workDetails."</br>";
		$workDetails=trim(preg_replace('/\s\s+/', ' ', $workDetails));
		$workDetails = str_replace('""','',$workDetails);


		$workDetails = trim($workDetails); 
		$todayDate = getCurrentTimeStamp();
		$sql2 = "INSERT INTO `renovation_details` (`request_id`, `unit_id`, `application_date`, `start_date`, `end_date`, `work_details`, `type_of_work`, `location`, `contractor_name`,`ContractorContactNo`, `contractor_address`, `max_labourer`, `labourer_name`) VALUES ('".$sqlsr_res."','".$unitId."', '".$todayDate['Date']."', '".getDBFormatDate($startDate)."', '".getDBFormatDate($endDate)."', '".$workDetails."', '".$workType."','".$location."', '".$contractorName."','".$contractorNO."','".$contractorAdd."', '".$maxLabourer."', '".$labourer."');";
		$sql2_res = $this->m_dbConn->insert($sql2);
		//var_dump($Name);
		//Inserting renovation details in approval_details table..
		$sql7 = "insert into `approval_details` (`referenceId`, module_id) values ('".$sql2_res."','".RENOVATION_SOURCE_TABLE_ID."');";
		$sql7_res = $this->m_dbConn->insert($sql7);
		
		//-----------------------------------------------Adding Drawing documents-----------------------------------------------------------------------------
		$workList = $this->getListOfWork();
		//var_dump($workList);
		for($i = 0; $i < $sizeOfDoc;$i++)
		{
			$doc_name = $workList[$i]['work'];
			//echo "<br>docName : ".$doc_name;
			//echo "<br> drawing : ".$drawingFiles['userfile']['name'][$i];
			if($drawingFiles['userfile']['name'][$i] != "")
			{
				//echo "in if";
				$fileName = "renovationDrawingFile_".$unitId."_".$sql2_res."_".basename($drawingFiles['userfile']['name'][$i]);
				//echo " fileName: ".$fileName;
				if($_SERVER['HTTP_HOST'] == "localhost" )
				{		
					$uploaddir = $_SERVER['DOCUMENT_ROOT']."/beta_aws_master/Uploaded_Documents";			   
				}
				else
				{
					$uploaddir = $_SERVER['DOCUMENT_ROOT']."/Uploaded_Documents";			   
				}
				$uploadfile = $uploaddir ."/". $fileName;	
				//echo "<br>filename : ".$uploadfile."<br/>";
				$fileResult = move_uploaded_file($drawingFiles['userfile']['tmp_name'][$i], $uploadfile);
				//echo "<br>fileResult : ".$fileResult;
				if($fileResult)
				{
					$insert_query="insert into `documents` (`Name`, `Unit_Id`,`refID`,`Category`, `Note`,`Document`,`source_table`,`doc_type_id`,`doc_version`,`attachment_gdrive_id`) values ('".$doc_name."', '".$unitId."','".$sql2_res."','0','','".$fileName."','2','9','1','')";
					$data=$this->m_dbConn->insert($insert_query);
				}
			}
			
		}
		$templates = $this->fetch_template_details("53");
		//var_dump($templates);
		$sql3 = "SELECT * FROM `society` WHERE `society_id` = '".$societyId."'";
		$sql3_res = $this->m_dbConn->select($sql3);
			
		
		/*$checkIfPropertyExists = $this->obj_Utility->getAppDefaultProperty('Garbage Area');
		if($checkIfPropertyExists == "")
		{
			$insert = $this->obj_Utility->setAppDefaultProperty('Garbage Area',$_POST['garbage_area']);
		}
		else
		{
			$update02 = $this->obj_Utility->updateAppDefaultProperty('Garbage Area',$_POST['garbage_area']);
		}*/
		$societyAddress = explode(",",$sql3_res[0]['society_add']);
		$templateDetails = $this->getTemplateDetails();
		$typeOfWorkList = "<ol>";
		$check = false;
		for($m = 0; $m < sizeof($workTypeArr);$m++)
		{
			$typeOfWorkList .= "<li>".$workTypeArr[$m]."</li>";
		}
		$typeOfWorkList .= "</ol>";
		if($workDetails == "")
		{
			$workDetails = "No work details mentioned as such.";
		}
		$workDetails = trim($workDetails);
		//-------------------------------New Template coding--------------------------------------------------------------------------//
		//var_dump("work Details : ".$workDetails);
		$thankYouNote = "<p style='margin-left:0in; margin-right:0in'>I hereby request you to kindly grant me permission to carry out the above mentioned renovation.</p><br/><p style='margin-left:0in; margin-right:0in'>Thanking you,</p>";
		//echo "template : ".$templates['template_data'];
		$old = array("{header}","{Date}","{Owner_name}","{Society_Name}","{Society_Name}","{Flat_No}","{Address1}","{Wing}","{Address2}","{Owner_No}","{Flat_No}","{Work_Type}","{Work_Details}","{Start_Date}","{End_Date}","{contractor_name}","{Contact_No}","{contractor_address}","{max_labourer}","{labourer_name}","{terms_condition}","{Flat_Owner_Name}","{footer}");
		$new = array($templateDetails[1],getDisplayFormatDate(date("Y-m-d")),$sql4_res[0]['primary_owner_name'],$sql3_res[0]['society_name'],$sql3_res[0]['society_name'],$sql4_res[0]['unit_no'],$societyAddress[0].",".$societyAddress[1],$sql4_res[0]['wing'],$societyAddress[2].",".$societyAddress[3],$sql4_res[0]['mob'],$sql4_res[0]['unit_no'],$typeOfWorkList,$workDetails,getDisplayFormatDate($startDate),getDisplayFormatDate($endDate),$contractorName,$contractorNO,$contractorAdd,$maxLabourer,$temp_labourer,$templateDetails[3],$sql4_res[0]['primary_owner_name'],"");
		$final = str_replace($old,$new,$templates['template_data']);
		
		//echo $final;
		$sql6 = "update `renovation_details` SET `final_template`= '".$final."' where Id = '".$sql2_res."';";
		$sql6_res = $this->m_dbConn->update($sql6);
		$_SESSION['serviceRequestDetails'] = "";
		$this->actionPage = "../document_maker.php?View=MEMBER&temp=".$_SESSION['RENOVATION_DOC_ID']."&rId=".$sql2_res;
		return ($sql2_res);
	}
	//end
	public function getpassDetail2($unitId)
	{
		$sqlSelect1 = "select mm.primary_owner_name, mof.mem_other_family_id, mof.other_name, mof.relation, mm.member_id from `mem_other_family` as mof, member_main as mm where mof.`member_id` = mm.`member_id` and mm.unit='".$unitId."' and mm.`ownership_status` = '1' and mof.`status` = 'Y'";
		//echo $sqlSelect;
		//print_r($m_dbConn);
		$res1 = $this->m_dbConn->select($sqlSelect1);
		
		//var_dump($res1);
		return $res1;
	}
	public function sendAddressProofDetails($serviceRequestId,$memberDetails,$stayingSinceDate, $purpose,$note,$ownerAddress,$unitId)
	{
		
		
	}
	
	public function addAddressProofDetails($srTitle,$srPriority,$srCategory,$loginId,$purpose,$unitId,$memberName,$stayingSince,$note,$societyId)
	{
		$applierName = "";
		$keyWord = "";
		$relation = "";
		$keyWord2 = "";
		$applierName2 = "";	
		$sqlName = "Select `member_id`,`name` from login where login_id = '".$loginId."';";
		$sqlName_res = $this->m_dbConnRoot->select($sqlName);
		$obj_LatestCount = new latestCount($this->m_dbConn);
		$request_no = $obj_LatestCount->getLatestRequestNo($societyId);
		$sql4 = "SELECT u.`unit_no`, mm.`primary_owner_name`, w.`wing`,mm.`mob` FROM `unit` u, `member_main` mm, `wing` w WHERE u.`unit_id` = mm.`unit` AND w.`wing_id` = mm.`wing_id` AND mm.`ownership_status` = '1' AND u.`unit_id` = '".$unitId."'";
		$sql4_res = $this->m_dbConn->select($sql4);
		$summery = "This is Address Proof request.";
		$sqlsr = "INSERT INTO `service_request` (`request_no`, `society_id`, `reportedby`, `dateofrequest`, `email`, `phone`, `priority`, `category`, `summery`,`img`, `details`, `status`, `unit_id`) VALUES ('".$request_no."', '".$societyId."', '".$sqlName_res[0]['name']."', '".getDBFormatDate(date('d-m-Y'))."', '".$sqlName_res[0]['name']."', '".$sql4_res[0]['mob']."', '".$srPriority."', '".$srCategory."', '".$srTitle."','','".$summery."', 'Raised', '".$unitId."')";	
		$sqlOwnerId = "select mof.`mem_other_family_id` from mem_other_family as mof, member_main as mm where mm.`ownership_status` = '1' and mm.`member_id` = mof.`member_id` and mm.`unit`= '".$unitId."';";
		$sqlOwnerId_res = $this->m_dbConn->select($sqlOwnerId);
		$ownerId = $sqlOwnerId_res[0]['mem_other_family_id'];				
		//echo "query:".$sql;  	
		$sqlsr_res = $this->m_dbConn->insert($sqlsr);
		$tempId = 0;
		if($purpose == '2')
		{
			$tempId = 31;	
		}
		else 
		{
			$tempId = 33;
		}
		$sql1 = "SELECT u.`unit_no`, mm.`primary_owner_name`, w.`wing` FROM `unit` u, `member_main` mm, `wing` w WHERE u.`unit_id` = mm.`unit` AND w.`wing_id` = mm.`wing_id` AND mm.`ownership_status` = '1' AND u.`unit_id` = '".$unitId."'";
		$sql1_res = $this->m_dbConn->select($sql1);
		//var_dump($sql1_res);
		
		$sql2 = "SELECT `society_name`,`registration_no`,`city`,`society_add` FROM `society` WHERE `society_id` = '".$societyId."'";
		$sql2_res = $this->m_dbConn->select($sql2);
		//var_dump($sql1_res);
		$templates = $this->fetch_template_details($tempId);
		//var_dump($templates);
		$memberAddress = $this->obj_serviceRequest->getMemberAddress($unitId,$societyId);
		//var_dump($memberAddress);
		//echo $templates['template_data'];
		
		//------------------------------------ request required same approval-------------------------------------------------//
			
		/*$sql6 = "insert into `approval_details` (`referenceId`, module_id) values ('".$sqlsr_res."','".ADDRESSPROOF_SOURCE_TABLE_ID."');";
		$sql6_res = $this->m_dbConn->insert($sql6);*/
		
		//-----------------------------------------------------------------------------------------------------------------------
		for($i = 0; $i < sizeof($memberName);$i++)
		{
			$sql3 = "SELECT other_name,relation,mem_other_family_id FROM `mem_other_family` where  mem_other_family_id='".$memberName[$i]."'";
			$sql3_res = $this->m_dbConn->select($sql3);
			$sql4 = "INSERT INTO `addressproof_noc` (`service_request_id`,`purpose_code`, `unit_id`, `mem_other_family_id`, `since_staying_date`,`note`) VALUES ('".$sqlsr_res."', '".$purpose."','".$unitId."','".$memberName[$i]."','".getDBFormatDate($stayingSince)."','".$note."');";
			$sql4_res = $this->m_dbConn->insert($sql4);
			//var_dump($sql3_res);
			//------------------------------------Each request required different approval-------------------------------------------------//
			
			$sql6 = "insert into `approval_details` (`referenceId`, module_id) values ('".$sql4_res."','".ADDRESSPROOF_SOURCE_TABLE_ID."');";
			$sql6_res = $this->m_dbConn->insert($sql6);
			
			//------------------------------------------------------------------------------------------------------------------------------//
			//var_dump($sql6_res);
			if($memberName[$i] == $ownerId)
			{
				$applierName = "";
				$keyWord = "";
				$relation = "";
				$keyWord2 = "";
				$applierName2 = "him/her";	
			}
			else
			{
				$applierName = $sql3_res[0]['other_name'];
				$keyWord = "of";
				$relation = $sql3_res[0]['relation'].",";
				$keyWord2 = "to";
				$applierName2 = $sql3_res[0]['other_name'];	
			}
			//echo $applierName2;
			if($tempId == "31")//Passport NOC
			{
				///echo $applierName2;
				$old = array("{SOCIETY_NAME}","{SOCIETY_REGISTRATION_NUMBER}","{ADDRESS LINE 1}","{ADDRESS LINE 2}","{LETTER_DATE}","{APPLIER_NAME}","{Keyword}","{RELATION_WITH_OWNER}","{OWNER_NAME}","{OWNER_ADDRESS}","{SINCE_STAYING_DATE}","{APPLIER_NAME}","{Keyword2}","{OWNER_NAME}","{APPLIER_NAME2}","{SOCIETY_CITY}","{SOCIETY_NAME}");
				
				$new = array($sql2_res[0]['society_name'],$sql2_res[0]['registration_no'],$sql2_res[0]['society_add'],"",getDisplayFormatDate(date("Y-m-d")),$applierName,$keyWord,$relation,$sql1_res[0]['primary_owner_name'],$memberAddress,$stayingSince,"",$keyWord2,$sql1_res[0]['primary_owner_name'],$applierName2,$sql2_res[0]['city'],$sql2_res[0]['society_name']);
				//echo $applierName2;
			}
			else//Domicile & addressproof
			{
				$old = array("{SOCIETY_NAME}","{SOCIETY_REGISTRATION_NUMBER}","{ADDRESS LINE 1}","{ADDRESS LINE 2}","{LETTER_DATE}","{APPLIER_NAME}","{Keyword}","{RELATION_WITH_OWNER}","{OWNER_NAME}","{OWNER_ADDRESS}","{SINCE_STAYING_DATE}","{APPLIER_NAME}","{Keyword2}","{OWNER_NAME}","{APPLIER_NAME2}","{SOCIETY_NAME}");
				
				$new = array($sql2_res[0]['society_name'],$sql2_res[0]['registration_no'],$sql2_res[0]['society_add'],"",getDisplayFormatDate(date("Y-m-d")),$applierName,$keyWord,$relation,$sql1_res[0]['primary_owner_name'],$memberAddress,$stayingSince,$sql3_res[0]['other_name'],$keyWord2,$sql1_res[0]['primary_owner_name'],$applierName2,$sql2_res[0]['society_name']);
			}
			//echo $applierName2;
			//var_dump($old);
			//var_dump($new);
			//echo $templates['template_data'];
			$final = str_replace($old,$new,$templates['template_data']);
			//echo $final;
			
			
			//$finalTemplate .= "<div style='page-break-after:always'><span style='display:none'>&nbsp;</span></div>";
			$sql5 = "update `addressproof_noc` SET `final_template` = '".$final."' where id = '".$sql4_res."'";
			$sql5_res = $this->m_dbConn->update($sql5);
		}
		$_SESSION['serviceRequestDetails'] = "";
		$this->actionPage = "../document_maker.php?View=MEMBER&temp=".$_SESSION['ADDRESS_PROOF_ID']."&aId=".$sqlsr_res; 
		return($sqlsr_res);
	}
	public function getAddressProofFinalTemplate($serviceRequestId,$addressProofId = 0,$role)
	{
		if($role != ROLE_MEMBER)
		{
			$sql1 = "Select `final_template` from `addressproof_noc` where service_request_id='".$serviceRequestId."';";
			$sql1_res = $this->m_dbConn->select($sql1);
			//var_dump($sql1_res);
			$finalTemplate = "";
			if($addressProofId == 0)
			{
				for($i = 0;$i < sizeof($sql1_res);$i++)
				{
					$finalTemplate .= $sql1_res[$i]['final_template'];
					$finalTemplate .= "<div style='page-break-after:always'><span style='display:none'>&nbsp;</span></div>";
				}
			}
			else
			{
				$sql5 = "Select `final_template` from `addressproof_noc` where id = '".$addressProofId."';";
				$sql5_res = $this->m_dbConn->select($sql5);
				$finalTemplate = $sql5_res[0]['final_template'];
			}
		}
		else
		{
			$sql2 = "Select `Value` from appdefault_new where `Property` = 'AddressProofThankyouNote';";
			$sql2_res = $this->m_dbConn->select($sql2);
			$template = $sql2_res[0]['Value'];
			//var_dump($template);
			$sql3 = "Select mof.`other_name`,mof.`relation`, sr.`request_no` from `addressproof_noc` as ap, `mem_other_family` as mof, `service_request` as sr where ap.`service_request_id` = '".$serviceRequestId."' and ap.`service_request_id` = sr.`request_id` and ap.`mem_other_family_id` = mof.`mem_other_family_id`;";
			$sql3_res = $this->m_dbConn->select($sql3);
			//var_dump($sql3_res);
			$addressProofDetails = "<ol>";
			for($i = 0;$i < sizeof($sql3_res);$i++)
			{
				$addressProofDetails .= "<li>".$sql3_res[$i]['other_name']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$sql3_res[$i]['relation']."</li>";
			}
			$addressProofDetails .= "</ol>";
			$old = array("{Address_PROOF_DETAILS}","{REQUEST_NO}");
			$new = array($addressProofDetails,$sql3_res[0]['request_no']);
			$finalTemplate = str_replace($old,$new,$template);
			//$finalTemplate = $template;
			$societyHead = $this->getSocietyHeader();
			$finalTemplate = $societyHead."<div style = 'text-align: right;'><br/>Date:".date("d-m-Y")."</div><br/>".$finalTemplate;
		}
		$finalTemplate = str_replace('"',"'",$finalTemplate);
		return($finalTemplate);
	}
	public function uploadFile($renovationId,$files,$unitId)
	{
		$target_path = "Uploaded_Documents/";
		$target_path = $target_path . basename($files['file']['name']);
		for($i = 0; $i < sizeof($files);$i++)
		{
			if (move_uploaded_file($files[$i]['file']['tmp_name'], $target_path)) 
			{
				$renovationId = $_REQUEST['renovationId'];
				$sql1 = "update `documents` set status = 'N' where refId = '".$renovationId."' and source_table = '".RENOVATION_SOURCE_TABLE_ID."'";
				$sql1_res = $this->m_dbConn->select($sql1);
				$insert_query="insert into `documents` (`Name`, `Unit_Id`,`refID`,`Category`, `Note`,`Document`,`source_table`,`doc_type_id`,`doc_version`,`attachment_gdrive_id`) values ('DrawingFile".($i+1)."', '".$unitId."','".$renovationId."','0', '','".$$files[$i]['file']['name']."','".RENOVATION_SOURCE_TABLE_ID."','".$_SESSION['RENOVATION_DOC_ID']."','1','".$$target_path."')";
				$data=$this->m_dbConn->insert($insert_query);
				echo "Upload and move success";
				$result = "1";
			}
			else 
			{
				echo $target_path;
		    	echo "There was an error uploading the file, please try again!";
				$result = 0;
			}
		}
		return($result);
	}
	public function getTenantNOC($unitId,$tenantName,$societyId,$tenantId)
	{
		$templates = $this->fetch_template_details("35");
		$sql1 = "SELECT u.`unit_no`, mm.`primary_owner_name`, w.`wing`,mm.`mob` FROM `unit` u, `member_main` mm, `wing` w WHERE u.`unit_id` = mm.`unit` AND w.`wing_id` = mm.`wing_id` AND mm.`ownership_status` = '1' AND u.`unit_id` = '".$unitId."'";
		$sql1_res = $this->m_dbConn->select($sql1);
		
		$sql2 = "SELECT `society_name`,`registration_no`,`city`,`society_add` FROM `society` WHERE `society_id` = '".$societyId."'";
		$sql2_res = $this->m_dbConn->select($sql2);
		
		
		$old = array("{SOCIETY_NAME}","{SOCIETY_REGISTRATION_NUMBER}","{ADDRESS LINE 1}","{ADDRESS LINE 2}","{LETTER_DATE}","{OWNER_NAME}","{OWNER_HIS_HER}","{FLAT_NO}","{TENANT}","{OWNER_NAME}","{OWNER_HIS_HER}","{TENANT}","{SOCIETY_NAME}");
		
		$new = array($sql2_res[0]['society_name'],$sql2_res[0]['registration_no'],$sql2_res[0]['society_add'],"",getDisplayFormatDate(date("Y-m-d")),$sql1_res[0]['primary_owner_name'],"his/her",$sql1_res[0]['unit_no'],$tenantName,$sql1_res[0]['primary_owner_name'],"his/her",$tenantName,$sql2_res[0]['society_name']);
		$final = str_replace($old,$new,$templates['template_data']);
		$sql3 = "update `tenant_module` set `noc_document` = '".$final."' where `tenant_id` = '".$tenantId."';";
		$sql3_res = $this->m_dbConn->update($sql3);
	}
	public function getTenantFinalNOC($tenantId,$role)
	{
		$finalTemplate = "";
		if($role != ROLE_MEMBER)
		{
			$sql1 = "Select `noc_document` from `tenant_module` where tenant_id = '".$tenantId."';";
			$sql1_res = $this->m_dbConn->select($sql1);
			//var_dump($sql1_res);
			$finalTemplate = $sql1_res[0]['noc_document'];
		}
		else
		{
			$sql2 = "Select `Value` from appdefault_new where `Property` = 'TenantRequestThankyouNote';";
			$sql2_res = $this->m_dbConn->select($sql2);
			$template = $sql2_res[0]['Value'];
			$sql3 = "Select u.`unit_no`, t.`tenant_name`,t.`tenant_MName`,t.`tenant_LName`,t.`create_date`,t.`start_date`,t.`end_date`,s.`request_no` from tenant_module as t, service_request as s, unit as u where t.`serviceRequestId` = s.`request_id` and t.`tenant_id` = '".$tenantId."' and u.`unit_id` = t.`unit_id`";
			$sql3_res = $this->m_dbConn->select($sql3);
			$old = array("{FLAT_NO}","{TENANT_NAME}","{CREATE_DATE}","{LEASE_START_DATE}","{LEASE_END_DATE}","{REQUEST_NO}");
			$new = array($sql3_res[0]['unit_no'],$sql3_res[0]['tenant_name']." ".$sql3_res[0]['tenant_MName']." ".$sql3_res[0]['tenant_LName'],getDisplayFormatDate($sql3_res[0]['create_date']),getDisplayFormatDate($sql3_res[0]['start_date']),getDisplayFormatDate($sql3_res[0]['end_date']),$sql3_res[0]['request_no']);
			$finalTemplate = str_replace($old,$new,$template);
			$societyHead = $this->getSocietyHeader();
			$finalTemplate = $societyHead."<div style = 'text-align: right;'><br/>Date:".date("d-m-Y")."</div><br/>".$finalTemplate;
		}
		$finalTemplate = str_replace('"',"'",$finalTemplate);
		return($finalTemplate);
	}
	public function getSocietyHeader()
	{
		$sql1 = "select * from society where society_id = '".$_SESSION['society_id']."'";
		$sql1_res = $this->m_dbConn->select($sql1);
		$societyHead = "<div style = 'text-align: center;'><strong>".$sql1_res[0]['society_name']."</strong></div><div style = 'text-align: center;'>REGN. NO: ".$sql1_res[0]['registration_no']."<br />".$sql1_res[0]['society_add']."<br /></div>";
		return($societyHead);
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
}
?>
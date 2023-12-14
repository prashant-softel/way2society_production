<?php if(!isset($_SESSION)){ session_start(); }
include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once("utility.class.php");
include_once('../swift/swift_required.php');
include_once("activate_user_email.class.php");
include_once("email.class.php");

class view_member_profile extends dbop
{
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_activation;
	public $m_obj_utility;
	public $objFetchData;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = new dbop(true);
		$this->display_pg=new display_table($this->m_dbConn);
		$this->objFetchData = new FetchData($dbConn);
		$this->m_obj_utility = new utility($this->m_dbConn, $this->m_dbConnRoot);
		
		$this->obj_activation = new activation_email($this->m_dbConn, $this->m_dbConnRoot);
		if(!empty($_SESSION['society_id']))
		{
			$this->objFetchData->GetSocietyDetails($_SESSION['society_id']);
		}
		//dbop::__construct();
	}
	
	public function show_member_main()
	{
		$sql = "SELECT wing,unit_no,mm.unit,u.flat_configuration,mm.owner_name,mm.primary_owner_name,mob,resd_no,off_no,dsg.desg_id,dsg.desg,email,alt_email,dob,wed_any,bgg.bg_id,bgg.bg,eme_rel_name,eme_contact_1,eme_contact_2,off_add,alt_mob,mm.parking_no,u.area,mm.profile,mm.publish_contact,mm.publish_profile,intercom_no,mm.alt_address,mm.owner_gstin_no, u.virtual_acc_no FROM member_main as mm,bg as bgg,unit as u,wing as w,desg as dsg where mm.blood_group=bgg.bg_id and mm.unit=u.unit_id and u.wing_id=w.wing_id and mm.desg=dsg.desg_id and mm.status='Y' and bgg.status='Y' and u.status='Y' and w.status='Y' and dsg.status='Y' and mm.member_id='".$_GET['id']."' ";
		
		
		$res = $this->m_dbConn->select($sql);
	
		return $res;
	}
	public function show_member_main_by_OwnerID()
	{
		$sql = "SELECT `wing`,unit_no,mm.unit,mm.owner_name,mm.primary_owner_name,mob,resd_no,off_no,dsg.desg_id,dsg.desg,email,alt_email,dob,wed_any,bgg.bg_id,bgg.bg,eme_rel_name,eme_contact_1,eme_contact_2,off_add,alt_mob,mm.parking_no,u.area,mm.profile,mm.publish_contact,mm.publish_profile,intercom_no,mm.alt_address,mm.owner_gstin_no FROM member_main as mm,bg as bgg,unit as u,wing as w,desg as dsg where mm.blood_group=bgg.bg_id and mm.unit=u.unit_id and u.wing_id=w.wing_id and mm.desg=dsg.desg_id and mm.status='Y' and bgg.status='Y' and u.status='Y' and w.status='Y' and dsg.status='Y' and mm.member_id='".$_SESSION['owner_id']."' ";
		//echo "string".$sql;
		
		$res = $this->m_dbConn->select($sql);
	
		return $res;
	}
	public function show_mem_other_family()
	{
		
		
		//$sql = "select * from mem_other_family as msd,bg as bgg,desg as dsg where msd.member_id='".$_GET['id']."' and msd.child_bg=bgg.bg_id and msd.other_desg=dsg.desg_id and msd.status='Y' and bgg.status='Y' and dsg.status='Y'";
		$sql = "select mem_other_family_id,other_name,relation,other_dob,dsg.desg_id,dsg.desg,ssc,bgg.bg_id,bg,msd.coowner, msd.other_profile, msd.other_mobile, msd.other_email,msd.other_publish_profile,msd.coowner,msd.other_wed,msd.other_publish_contact,msd.send_commu_emails from mem_other_family as msd,bg as bgg,desg as dsg,member_main as membertbl where membertbl.member_id='".$_GET['id']."' and membertbl.member_id=msd.member_id and msd.child_bg=bgg.bg_id and msd.other_desg=dsg.desg_id and msd.status='Y' and bgg.status='Y' and dsg.status='Y'";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	public function show_mem_car_parking()
	{
		
		$sql = "select * from mem_car_parking as mcp,member_main as membertbl where membertbl.member_id='".$_GET['id']."' and membertbl.member_id=mcp.member_id and mcp.status='Y' ";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	public function show_mem_bike_parking()
	{
		$sql = "select * from mem_bike_parking as mbp,member_main as membertbl where membertbl.member_id='".$_GET['id']."' and membertbl.member_id=mbp.member_id and mbp.status='Y' ";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	public function submit_renew_registration($Id,$vehicle_type)
	{
		if($vehicle_type == VEHICLE_BIKE)
		{
			$table = 'mem_bike_parking';
			$id_colunm = 'mem_bike_parking_id';
		}
		else if($vehicle_type == VEHICLE_CAR)
		{
			$table = 'mem_car_parking';
			$id_colunm = 'mem_car_parking_id';
		}
		
		if(!empty($Id))
		{
			$Select_query = "SELECT Renew_Registration FROM $table where $id_colunm = '".$Id."' and Renew_Registration = 1 and Renew_Registration_Date <= '2019-12-31'";
			$Data = $this->m_dbConn->select($Select_query);
		}
		
		if(empty($Data))
		{
			$current_date = date('Y-m-d');
			$Update_query = "UPDATE $table SET Renew_Registration = '1' , Renew_Registration_Date = '".$current_date."' WHERE $id_colunm = '".$Id."'";
			$this->m_dbConn->update($Update_query);
			$this->sendRenewalSubmitEmail($Id, $table, $id_colunm);
			$status = 'success';
			$msg = "Your Application Successfully Submitted";
		}
		else
		{
			$status = 'failed';
			$msg = "Your Application Already Submitted";
		}
		
		$result = array('status'=>$status,'msg'=>$msg);
		echo json_encode($result);
	}
	
	public function exportMemberVehicleReport()
	{
		$header = array('Sr No.', 'Full Name', 'Wing', 'Flat', 'Telephone No.', 'Email' , 'Model' ,  'Vehicle Reg. No.', 'Registered Owner', 'Relation', 'Parking Space', 'Vehicle Type', 'Vehicle Space', 'Date');
	
		$member_main_query = "SELECT mm.primary_owner_name, mm.member_id, mm.mob, mm.email, u.unit_no, w.wing   From `member_main` as mm JOIN `unit` as u ON mm.unit = u.unit_id LEFT JOIN wing as w ON u.wing_id = w.wing_id where mm.ownership_status = 1";
		$member_main_data = $this->m_dbConn->select($member_main_query);		
		
		$car_parking_query = "SELECT * FROM mem_car_parking WHERE  Renew_Registration = 1 AND status != 'N'";
		$car_parking_data = $this->m_dbConn->select($car_parking_query);
		
		$bike_parking_query = "SELECT * FROM mem_bike_parking WHERE Renew_Registration = 1 AND status != 'N'";
		$bike_parking_data = $this->m_dbConn->select($bike_parking_query);
		

		/*echo "<pre>";
		print_r($member_main_data);
		echo "</pre>";*/
		
		$all_member_IDs = array_column($member_main_data,'member_id');
		$car_member_IDs = array_column($car_parking_data,'member_id');
		$bike_member_IDs = array_column($bike_parking_data,'member_id');
		
		$export_to_excel_data = array();
		
		$SrNo = 1;
		$count = 0;
		foreach($all_member_IDs as $member_id)
		{
			$member_index = array_search($member_id,$all_member_IDs);
			if($member_index !== '0')
			{
				if(in_array($member_id,$car_member_IDs))
				{
					$member_car_parking_keys = array_keys($car_member_IDs,$member_id);
					//var_dump($member_car_parking_keys);
					
					foreach($member_car_parking_keys as $key)
					{
						$export_to_excel_data[$count]['SrNo'] = $SrNo;
						$export_to_excel_data[$count]['FullName'] = $member_main_data[$member_index]['primary_owner_name'];
						$export_to_excel_data[$count]['Wing'] = $member_main_data[$member_index]['wing'];
						$export_to_excel_data[$count]['Flat'] = $member_main_data[$member_index]['unit_no'];
						$export_to_excel_data[$count]['Wing'] = $member_main_data[$member_index]['wing'];
						$export_to_excel_data[$count]['Mobile'] = $member_main_data[$member_index]['mob'];
						$export_to_excel_data[$count]['Email'] = $member_main_data[$member_index]['email'];
						$export_to_excel_data[$count]['Model'] = $car_parking_data[$key]['car_model'];
						$export_to_excel_data[$count]['RegisteredNo'] = $car_parking_data[$key]['car_reg_no'];
						$export_to_excel_data[$count]['RegisteredOwner'] = $car_parking_data[$key]['car_owner'];
						$export_to_excel_data[$count]['Relation'] = '';
						$export_to_excel_data[$count]['ParkingSpace'] = $car_parking_data[$key]['parking_slot'];
						$export_to_excel_data[$count]['VehicleType'] = 'Four Wheeler';
						$export_to_excel_data[$count]['VehicleSpace'] = 'Open Allocation';
						$export_to_excel_data[$count]['Date'] = getDisplayFormatDate($car_parking_data[$key]['Renew_Registration_Date']);	
						$count++;
						$SrNo++;
					}
				}
				
				if(in_array($member_id,$bike_member_IDs))
				{
					$member_bike_parking_keys = array_keys($bike_member_IDs,$member_id);
					//var_dump($member_bike_parking_keys);
					
					foreach($member_bike_parking_keys as $key)
					{
						$export_to_excel_data[$count]['SrNo'] = $SrNo;
						$export_to_excel_data[$count]['FullName'] = $member_main_data[$member_index]['primary_owner_name'];
						$export_to_excel_data[$count]['Wing'] = $member_main_data[$member_index]['wing'];
						$export_to_excel_data[$count]['Flat'] = $member_main_data[$member_index]['unit_no'];
						$export_to_excel_data[$count]['Wing'] = $member_main_data[$member_index]['wing'];
						$export_to_excel_data[$count]['Mobile'] = $member_main_data[$member_index]['mob'];
						$export_to_excel_data[$count]['Email'] = $member_main_data[$member_index]['email'];
						$export_to_excel_data[$count]['Model'] = $bike_parking_data[$key]['bike_model'];
						$export_to_excel_data[$count]['RegisteredNo'] = $bike_parking_data[$key]['bike_reg_no'];
						$export_to_excel_data[$count]['RegisteredOwner'] = $bike_parking_data[$key]['bike_owner'];
						$export_to_excel_data[$count]['Relation'] = '';
						$export_to_excel_data[$count]['ParkingSpace'] = $bike_parking_data[$key]['parking_slot'];
						$export_to_excel_data[$count]['VehicleType'] = 'Two Wheeler';
						$export_to_excel_data[$count]['VehicleSpace'] = '';
						$export_to_excel_data[$count]['Date'] = getDisplayFormatDate($car_parking_data[$key]['Renew_Registration_Date']);
						$count++;
						$SrNo++;
					}
				}	
			}
		}
		
		
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=Renew_Registration_Report.csv');	
		
		ob_end_clean();
		$output = fopen("php://output","w");		
    	fputcsv($output, $header); 
		
		foreach($export_to_excel_data as $value)
		{
			fputcsv($output, $value);
		}
		
		fclose($output);
		exit();
	
	}
	
	
	// Fetch all Parking Type  
	public function get_parking_type()
	{
		$query = "SELECT id, ParkingType FROM `parking_type`";
		$ParkingTypeTableData = $this->m_dbConn->select($query);
		$ParkingIds = array_column($ParkingTypeTableData,'id');
		$ParkingName = array_column($ParkingTypeTableData,'ParkingType');
		$FinalParking  = array_combine($ParkingIds,$ParkingName);
		return $FinalParking;
	}

	public function show_share_certificate_details()
	{
		$sql = "SELECT `unit` FROM `member_main` WHERE `member_id` = '".$_GET['id']."'";
		$unit = $this->m_dbConn->select($sql);
		$sql = "SELECT `share_certificate`, `share_certificate_from`, `share_certificate_to`, `nomination`,nominee_name FROM `unit` WHERE `unit_id` = '".$unit[0]['unit']."'";
		$result = $this->m_dbConn->select($sql);
		return $result;
	}
	
	public function show_share_certificate()
	{
		$sql = 'SELECT `show_share` FROM `society` WHERE `society_id` = "'.$_SESSION['society_id'].'"';
		$result = $this->m_dbConn->select($sql);	
		return $result[0]['show_share'];
	}

	public function combobox11($query,$id)
	{
	//$str.="<option value=''>Please Select</option>";
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
	public function ComboboxWithDefaultSelect($query,$id)
		{
		//$str.="<option value=''>All</option>";
		$str.="<option value='0'>Undefine</option>";
		$data = $this->m_dbConn->select($query);
		//print_r($data);
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

	public function sendRenewalSubmitEmail($Id, $table, $id_colunm)
	{
	
		if($table == 'mem_bike_parking')
		{
			
			$select = "select mm.primary_owner_name, mm.email, u.unit_no, v.bike_reg_no, v.bike_owner, v.bike_model from $table as v LEFT JOIN `member_main` as mm ON v.member_id = mm.member_id LEFT JOIN `unit` as u ON u.unit_id = mm.unit where $id_colunm = '".$Id."' AND mm.ownership_status = 1 ";
			$vehicle_details = $this->m_dbConn->select($select);

			$select = "select";
			$vehicle = "Two Wheeler";
			$vehicle_owner = $vehicle_details[0]['bike_owner'];
			$vehicle_unit_no = $vehicle_details[0]['unit_no'];
			$vehicle_reg_no = $vehicle_details[0]['bike_reg_no'];
			$vehicle_model = $vehicle_details[0]['bike_model'];
		}
		elseif($table == 'mem_car_parking')
		{
			$select = "select mm.primary_owner_name, mm.email, u.unit_no, v.car_reg_no, v.car_owner, v.car_model from $table as v LEFT JOIN `member_main` as mm ON v.member_id = mm.member_id LEFT JOIN `unit` as u ON u.unit_id = mm.unit where $id_colunm = '".$Id."' AND mm.ownership_status = 1";
			$vehicle_details = $this->m_dbConn->select($select);
			$vehicle = "Four Wheeler";
			$vehicle_owner = $vehicle_details[0]['car_owner'];
			$vehicle_unit_no = $vehicle_details[0]['unit_no'];
			$vehicle_reg_no = $vehicle_details[0]['car_reg_no'];
			$vehicle_model = $vehicle_details[0]['car_model'];

		}

		$member_email = $vehicle_details[0]['email'];
		$member_primary_owner_name = $vehicle_details[0]['primary_owner_name'];


		$body = "Dear ".$member_primary_owner_name.", <br><br> Your Open Parking Allocation details has been successfully submitted as per details below. <br><br>Date of application :".date('d-m-Y H:i:s')."<br>Name :".$vehicle_owner."<br>Flat :".$vehicle_unit_no."<br>Vehicle Type :".$vehicle."<br>vehicle registration number:".$vehicle_reg_no."<br>Model :".$vehicle_model."<br><br>You will be required to submit a copy of the 'RC book' and Company Letter (in case of company owned vehicle) on the day of the allocation which will be notified shortly by the managing committee.<br><br>
			Should you have any queries or need clarifications in the meanwhile, you can contact the Marigold Society office on 022-4979-4642 or raise Service Request(SR) on Way2Society App<br><br>Managing Committee, <br><br>Shri Marigold CHS Ltd.";

			$societyEmail = "";	  
			$societyName = "";
			if($this->objFetchData->objSocietyDetails->sSocietyEmail <> "")
			{
			   $societyEmail = $this->objFetchData->objSocietyDetails->sSocietyEmail;
			   $societyName = $this->objFetchData->objSocietyDetails->sSocietyName;
			}

			$EMailIDToUse = $this->m_obj_utility->GetEmailIDToUse(true, 1, "", 0, 0, $_SESSION['dbname'], $_SESSION['society_id'], 0, 0);

			$EMailID = $EMailIDToUse['email'];
			$Password = $EMailIDToUse['password'];

			//$transport = Swift_SmtpTransport::newInstance('103.50.162.146',587)
								//->setUsername('no-reply@way2society.com')
								//->setUsername($EMailID)
								//->setSourceIp('0.0.0.0')
								//->setPassword('society123') ; 
								//->setPassword($Password) ; 
			$AWS_Config = CommanEmailConfig();
			$transport = Swift_SmtpTransport::newInstance($AWS_Config[0]['Endpoint'],$AWS_Config[0]['Port'] , $AWS_Config[0]['Security'])
				 	->setUsername($AWS_Config[0]['Username'])
				  	->setPassword($AWS_Config[0]['Password']);																				
						// Create the message
						$message = Swift_Message::newInstance();
						
						if($this->objFetchData->objSocietyDetails->sSocietyEmail <> "")
						{
							$message->setTo(array(
							   $societyEmail => $societyName
							));
						}
						
						$message->setBcc(array($member_email => $member_primary_owner_name));															
						 
						 $message->setReplyTo(array(
						   $societyEmail => $societyName
						));
						
						$message->setSubject($member_primary_owner_name." Your Open Parking Allocation details has been successfully submitted at ".date('d-m-Y H:i:s'));
						$message->setBody($body);
						$message->setFrom($EMailID, $this->objFetchData->objSocietyDetails->sSocietyName);
						$message->setContentType("text/html");
						
						// Send the email
						$mailer = Swift_Mailer::newInstance($transport);
						
						$result = $mailer->send($message);
						

	/*		$transport = Swift_SmtpTransport::newInstance('103.50.162.146',587)
								//->setUsername('no-reply@way2society.com')
								->setUsername($EMailID)
								->setSourceIp('0.0.0.0')
								//->setPassword('society123') ; 
								->setPassword($Password) ; 

		//$mailer = new Swift_Mailer($transport);
		$message = Swift_Message::newInstance();
 		
		// Create a message
	    //$message = new Swift_Message();
	 
	    // Set a "subject"
	    $message->setSubject($member_primary_owner_name." Your Open Parking Allocation details has been successfully submitted at ".date('d-m-Y H:i:s'));
	 
	    // Set the "From address"
	    $message->addFrom($EMailID,$societyName);
	 
	    // Set the "To address" [Use setTo method for multiple recipients, argument should be array]
	    $message->addTo($societyEmail, $societyName);
	 
	    // Add "CC" address [Use setCc method for multiple recipients, argument should be array]
	    $message->addTo($member_email, $member_primary_owner_name);
	 
	    // Set the plain-text "Body"
	    $message->setBody($body);

	    $message->setContentType("text/html");
		
	    $mailer = Swift_Mailer::newInstance($transport);
		
	    // Send the message
	    $result = $mailer->send($message);
		*/
		//var_dump($result);
	}

	


}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'submit_renew_registration')
{
	$Db_Conn = new dbop();
	$Obj_view_mem = new view_member_profile($Db_Conn);
	echo $Obj_view_mem->submit_renew_registration($_REQUEST['id'],$_REQUEST['vehicle_type']);
	
}



?>

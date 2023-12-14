<?php
include_once("include/dbop.class.php");
include_once("utility.class.php");
include_once("dbconst.class.php");
include_once("register.class.php");
include_once("changelog.class.php");//Pending - Verify

include_once("include/fetch_data.php");

// error_reporting(1);
class import_tenantdata 
{
	public $m_dbConn;
	public $obj_utility;
	public $errorfile_name;
	public $errorLog;
	public $actionPage = '../import_tenant_data.php';
	public $bvalidate;
	public $changeLog;
	public $obj_fetch;
	

	private $FDCatArray;

	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->dbConnRoot = $dbConnRoot;
		$this->obj_utility = new utility($this->m_dbConn);
		$this->changeLog = new changelog($this->m_dbConn);
		$this->register = new regiser($this->m_dbConn);


		$this->obj_fetch = new FetchData($this->m_dbConn);

		$a = $this->obj_fetch->GetSocietyDetails($_SESSION['society_id']);
			
	}
	

	public function UploadData($fileName,$fileData, $bvalidate)
	{
		$Foldername = $this->obj_fetch->objSocietyDetails->sSocietyCode;

		if (!file_exists('../logs/import_log/'.$Foldername)) 
		{
			mkdir('../logs/import_log/'.$Foldername, 0777, true);
		}

		$a = 'import_tenant_data_errorlog_'.date("d.m.Y").'_'.rand().'.html';
		$b = '../logs/import_log/'.$Foldername;

		$c = 'logs/import_log/'.$Foldername;
		
		$this->errorfile_name = $b.'/'.$a;
		$errorfile = fopen($this->errorfile_name, "a");

		if($bvalidate == true)
		{
			$this->errorfile_name = $c.'/'.$a;
		}else
		{
			$this->errorfile_name = $b.'/'.$a;
		}

		$this->errorLog = $this->errorfile_name;
   


		$errormsg="[Importing Tenant Data]";
		$isImportSuccess = true;
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		// $bvalidate = true;

		$array = array();
		$Success = 0;
		$rowCount = 0;
		$m_TraceDebugInfo = "";
		$noErrorInFIle = array();

		foreach($fileData as $row)
		{
			$isImportSuccess = true;

			if($row[0] || $row[1] <> '')
			{
				$rowCount++;
				if($rowCount == 1)//Header
				{
					
						$UnitNoCol = array_search(Unitno,$row, true);
						$TenantFname = array_search(TenantFname,$row, true);
						$TenantMname = array_search(TenantMname,$row, true);
						$TenantLname = array_search(TenantLname,$row, true);
						$DOB = array_search(DOB,$row, true);
						$Email = array_search(Email,$row, true);
						$ContactNumber = array_search(ContactNumber,$row, true);
						$Agentname = array_search(Agentname,$row, true);	
						$AgentContactNo = array_search(AgentContactNo,$row, true);
						$StartDate = array_search(StartDate,$row, true);
						$EndDate = array_search(EndDate,$row, true);
						$TenantType = array_search(TenantType,$row, true);	
						$Address = array_search(Address,$row, true);
						$Pincode = array_search(Pincode,$row, true);
						$City = array_search(City,$row, true);
						$wing = array_search(Wing,$row, true);
						$ErrorPrintHead = false;
						
						

						if($ErrorPrintHead == true)
						{
							array_push($noErrorInFIle,$ErrorPrintHead);
							$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"E");	
						}
						//die();
						
						/*if(!isset($UnitNoCol) || !isset($TenantFname) || !isset($TenantMname) || !isset($DOB)||!isset($Email) || !isset($ContactNumber) || !isset($Agentname) || !isset($AgentContactNo) || !isset($StartDate) || !isset($EndDate) || !isset($TenantType) || !isset($Address) || !isset($Pincode) || !isset($City) || !isset($NoOfMember) )
						{
								$result = '<p>Required Column Names Not Found. Cant Proceed Further......</p>';
								$errormsg=" Column names does not match";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								return $result;
								
						}*/

					
				}
				else
				{

					/*if($rowCount == 2)
					{
						continue;	
					}*/
					
					//print_r($row[$Address]);
					//echo $row[$TenantFname];
					/*if($row[$UnitNoCol] == "")
					{
						echo "ashwini";
					}
					else
					{
						echo "rokade";
					}
					die();*/
					
						$errormsg = '';

						//getting wing id
						$getwing = "select `wing_id` from `wing` where `wing` = '".$row[$wing]."' ";
						$wingid = $this->m_dbConn->select($getwing);

						//$wingid[0]['wing_id'];

						$unitidquery = "select `unit_id` from unit where `unit_no` = '".$row[$UnitNoCol]."' and `wing_id`='".$wingid[0]['wing_id']."' ";
						$unitid = $this->m_dbConn->select($unitidquery);

						// print_r($unitid);
						//die();
						if($unitid[0]['unit_id'] == '')
						{
							$errormsg="Unit No Missing Or Please mention correct Unit No.  :<br/>";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;
						}else
						{
							$UnitnoCol = $unitid[0]['unit_id'];
						}

						// echo $UnitnoCol;
						// die();

						if($row[$TenantFname] == '')
						{
							$errormsg="Tenant First Name Missing   :<br/>";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;
						}else
						{
							$Tenantfname  = $row[$TenantFname];
						}

						if($row[$TenantMname] == '')
						{
							$errormsg="Tenant Middle Name Missing    :<br/>";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;
						}else
						{
							$Tenantmname = $row[$TenantMname];
						}

						if($row[$TenantLname] == '')
						{
							$errormsg="Tenant Last Name Missing   :<br/>";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;
						}else
						{
							$Tenantlname = $row[$TenantLname];
						}

						if($row[$DOB] == '')
						{
							$errormsg="DOB Missing :<br/>";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;

						}else
						{
							$date = explode('-', $row[$DOB]);
							if(strlen($date[0]) < 3 && strlen($date[2]) < 3)
							{
								$errormsg = "The Date format should be 'dd-mm-yyyy' ";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								$isImportSuccess = false;

							}else
							{
								$dob = getDBFormatDate($row[$DOB]);
							}

							// $dateofdeposite = $row[$DateofDeposite];
						}


						/*$datefomr = $row[$DOB];
						$datecheck = $this->obj_utility->dateFormat($datefomr)
						if($datecheck == '')
						{
							$errormsg="DOB Missing   :<br/>";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;
						}
						else
						{
							$dob = $datecheck;
						}
						*/



						if($row[$Email] == '')
						{
							$errormsg="Email ID Missing   :<br/>";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;
						}else
						{
							$email = $row[$Email];
						}

						if($row[$ContactNumber] == '')
						{
							$errormsg="Contact Number Missing   :<br/>";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;
						}else
						{
							$Contactnumber = $row[$ContactNumber];
						}

						if($row[$Agentname] == '')
						{
							$errormsg="Agent name Missing  :<br/>";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;
						}else
						{
							$agentname = $row[$Agentname];
						}

						if($row[$AgentContactNo] == '')
						{
							$errormsg="Agent Contact Number Missing   :<br/>";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;
						}else
						{
							$agentContactNo = $row[$AgentContactNo];
						}

						if($row[$StartDate] == '')
						{
							$errormsg="Start Date Missing   :<br/>";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;
						}else
						{
							$Startdate = $row[$StartDate];
						}

						if($row[$EndDate] == '')
						{
							$errormsg="End Date Missing    :<br/>";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;
						}else
						{
							$Enddate = $row[$EndDate];
						}

						if($row[$TenantType] == '')
						{
							$errormsg="Tenant Type Missing  :<br/>";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;
						}else
						{
							$Tenanttype = $row[$TenantType];
						}

						$address = $row[$Address];
						$pincode = $row[$Pincode];
						$city = $row[$City];
						$NoOfmember= $row[$NoOfMember];

						/*echo $UnitnoCol;
						echo $Tenantfname;
						echo $Tenantmname;
						echo $Tenantlname;
						echo $dob;
						echo $email;
						echo $Contactnumber;
						echo $agentname;
						echo $agentContactNo;
						echo $Startdate;
						echo $Enddate;
						echo $Tenanttype;
						print_r($Address);
						print_r($Pincode);
						print_r($City);
						print_r($NoOfMember);*/

						//die();
						//echo $isImportSuccess;
						

						if($isImportSuccess == false)
						{
							$errormsg = "Data not Inserted";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
							
						}
						//elseif($UnitnoCol <> '' && $Tenantfname <> '' && $Tenantmname <> '' && $Tenantlname <> '' && $dob <> '' && $email <> '' && $Contactnumber <> '' && $agentname <> '' && $agentContactNo <> '' && $Startdate <> '' && $Enddate <> '' && $Tenanttype <> '')
						else
						{
							$sql_insert = "insert into `tenant_module`(`unit_id`,`tenant_name`,`tenant_MName`,`Tenant_LName`,`dob`,`email`,`mobile_no`,`agent_name`,`agent_no`,`start_date`,`end_date`,`Address`,`Pincode`,`City`,`members`,`tenantType`) values ('".$UnitnoCol."','".$Tenantfname."','".$Tenantmname."','".$Tenantlname."','". getDBFormatDate($dob)."','".$Email."','".$Contactnumber."','".$agentname."','".$agentContactNo."','". getDBFormatDate($Startdate)."','". getDBFormatDate($Enddate)."',
							'".$address."','".$pincode."','".$city."','1','".$Tenanttype."')";
							$sql_insert_done = $this->m_dbConn->insert($sql_insert);

							// echo $sql_insert_done;
							// die();
							// echo $tenantidModule = "select tenant_id from tenant_module where `unit_id`='".$UnitnoCol."'";
							// $id = $this->m_dbConn->select($tenantidModule);
							// //$ashwini = $id['tenant_id'];

							// print_r($id);
							//die();
							
								$sql_insert1 = "insert into `tenant_member`(`tenant_id`,`mem_name`,`relation`,`mem_dob`,`email`,`contact_no`) values ('".$sql_insert_done."','".$Tenantfname." " .$Tenantmname." ".$Tenantlname."','".self."','". getDBFormatDate($dob)."','".$Email."','".$Contactnumber."')";

								$sql_insert_member = $this->m_dbConn->insert($sql_insert1);
							//die();

							if($sql_insert_member <> "" && $sql_insert_done <> "")
							{
								$errormsg = "Tenant Data inserted successfully.";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
							}
						}
						
					 
				}
				
			}
		}
		
	}
}

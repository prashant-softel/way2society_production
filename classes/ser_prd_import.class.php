<?php
include_once("include/dbop.class.php");
include_once("utility.class.php");
include_once("CsvOperations.class.php");
include_once("dbconst.class.php");
include_once("service_prd_reg.class.php");
include_once("include/fetch_data.php");

class service_prov_import 
{	
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_utility;
	public $errorfile_name;
	public $errorLog;
	public $actionPage = '../import_ser_prd.php';
	public $csv;
	public $obj_service_prd_reg;
	private $MaxStaffID;
	private $errorCheck;
	public $obj_fetch;	
	public $allColumns = array('Staff_Id','Name', 'Phone', 'Gender', 'Service Type', 'Created Date','Approved Date', 'Address','Working Apartment');

	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->obj_utility = new utility($this->m_dbConn);
		$this->obj_service_prd_reg = new service_prd_reg($this->m_dbConn, $this->m_dbConnRoot);
		$this->csv = new CsvOperations();
		$this->MaxStaffID = 0;
		$this->errorCheck = false;

		$this->obj_fetch = new FetchData($this->m_dbConn);

		$a = $this->obj_fetch->GetSocietyDetails($_SESSION['society_id']);
	}
	
	public function UploadDataManually($file,$indexes,$FileData)
	{
		$Foldername = $this->obj_fetch->objSocietyDetails->sSocietyCode;

		if (!file_exists('../logs/import_log/'.$Foldername)) 
		{
			mkdir('../logs/import_log/'.$Foldername, 0777, true);
		}
		
		$this->errorfile_name = '../logs/import_log/'.$Foldername.'/import_service_provider_errorlog_'.date("d.m.Y").'_'.rand().'.html';
		$this->errorLog = $this->errorfile_name;
		$errorfile = fopen($this->errorfile_name, "a");
		$errormsg="[Importing Service Provider Data Start]<BR>";
		$isImportSuccess = false;
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		$enableinsert = true;
		$contimax = 0;
		
		//var_dump($indexes);
		//The below code will check the position of columns so we identify the correct data 
		//var_dump($FileData);
		$SerPrdArray = array();
		$SprCatArray = array();
		$SerUnitArray = array();
		$SerSocArray = array();
		try
		{
			$ColumntoInsert = '';
			$StaffCol=array_search('Staff_Id',$FileData[0]);
			$NameCol = array_search('Name',$FileData[0]);
			$PhoneCol = array_search('Phone',$FileData[0]);
			$GenderCol = array_search('Gender',$FileData[0]);
			$ServiceTypeCol = array_search('Service Type',$FileData[0]);
			$CreatedDateCol = array_search('Created Date',$FileData[0]);
			$ApprovedDateCol = array_search('Approved Date',$FileData[0]);
			$AddressCol = array_search('Address',$FileData[0]);
			$WorkingApartmentCol = array_search('Working Apartment',$FileData[0]);	
			$successmsg = array();
			//Validation Start Here
			$m_TraceDebugInfo = "";
			$rowCount = 1;
			$ErrorPrintHead = false;
			$noErrorInFIle = array();
			if($StaffCol == '' && $StaffCol <> 0)
			{
				$ErrorPrintHead = true;
				$m_TraceDebugInfo .= "Society Staff ID Head Missing   :: ";
			}
			if($NameCol == '' && $NameCol <> 0)
			{
				$ErrorPrintHead = true;
				$m_TraceDebugInfo .= "Name Head Missing   :: ";
			}
			if($PhoneCol == '')
			{
				$ErrorPrintHead = true;
				$m_TraceDebugInfo .= "Phone Head Missing   :: ";
			}
			if(array_search($GenderCol,$indexes))
			{
				if($GenderCol == '')
				{
					$ErrorPrintHead = true;
					$m_TraceDebugInfo .= "Gender Head Missing   :: ";
				}	
			}
			if($ServiceTypeCol == '')
			{
				$ErrorPrintHead = true;
				$m_TraceDebugInfo .= "Service Type Head Missing   :: ";
			}
			if(array_search($AddressCol,$indexes))
			{
				if($AddressCol == '')
				{
					$ErrorPrintHead = true;
					$m_TraceDebugInfo .= "Address Head Missing  :: ";
				}	
			}
			
			if($WorkingApartmentCol == '')
			{
				//$ErrorPrintHead = true;
				$m_TraceDebugInfo .= "Working Apartment Head Missing   :: ";
			}
			
			if($ErrorPrintHead == true)
			{
				if($this->errorCheck) echo "m_TraceDebugInfo : " .$m_TraceDebugInfo;
				array_push($noErrorInFIle,$ErrorPrintHead);
				$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"E");	
			}
								
			//It is necessary always keep the TableColName, ColPosition And Tablevalue array present below with same sequence
			
			$TableColName = array('full_name','gender','cur_con_1','cur_resd_add');
			$ColPosition = array($NameCol,$GenderCol,$PhoneCol,$AddressCol);
			$CategoryArray = array();
			$catID = array();
			
			//ColumntoInsert is a string which collect the table column name according to checked in datadisplay page
			$ColumntoInsert = 'society_id';
			//$c = 0;
			for($j = 0; $j < sizeof($ColPosition); $j++)
			{
				//if($this->errorCheck) echo "<BR>$j : " . $ColPosition[$j];
				//making string of columns which going to insert. Here it is making the string of only those columns which is checked; 
				if(in_array($ColPosition[$j],$indexes))
				{
					if($ColPosition[$j] == '' && $ColPosition[$j] !== 0)
					{
						continue;
					}
					else
					{
						$ColumntoInsert .= ', '.$TableColName[$j];
					}
				}
			}
			
			$GetCategory = $this->m_dbConnRoot->select("SELECT cat_id, cat from cat where status = 'Y'");
			
			//Pushing the Fetch category from databse to new array so it convert in index array which hepl us to perform array_search function
			for($i = 0 ; $i< sizeof($GetCategory); $i++)
			{ 
				array_push($CategoryArray,strtolower($GetCategory[$i]['cat']));
				array_push($catID,$GetCategory[$i]['cat_id']);	
			}	
			
			$queryStaffIDs = "select society_staff_id as staff_id from service_prd_society where society_id = ".$_SESSION['society_id']."";
			$result =  $this->m_dbConnRoot->select($queryStaffIDs);
			$StaffID_array = array_column($result, 'staff_id');
			//array_walk($StaffID_array, function(&$v){ $v = intval($v); });
			if($this->errorCheck) var_dump($StaffID_array);
			
			//if($this->errorCheck) echo "<BR>".max($StaffID_array);
			//$MaxStaffID = max($StaffID_array);
			
			
			$sqlMaxNumber = "SELECT CAST(society_staff_id AS UNSIGNED) as maxStaffID FROM `service_prd_society` where society_id = ".$_SESSION['society_id']." order by maxStaffID desc LIMIT 1";
//			$LastStaffID = $this->m_dbConnRoot->select($sqlMaxNumber);			
//			$MaxStaffID = $LastStaffID[0]['maxStaffID'];
//			if($this->errorCheck) echo "<BR>MaxStaffID ".$MaxStaffID;
//			
			for($i = 2 ; $i < sizeof($FileData)-1 ; $i++)
			{
				
				if($this->errorCheck) echo "<BR>Data row: ". $i;	

				$rowCount++;
				$errormsg = "";
				$errorPrintData = false;
				//Here storing the value in New Varialbe according to there coloum name
				$timestamp = getCurrentTimeStamp();	
				$StaffID=$FileData[$i][$StaffCol];
				$Name = $FileData[$i][$NameCol];
				$Phone = $FileData[$i][$PhoneCol];
				$Gender = $FileData[$i][$GenderCol];
				$CategoryType = $FileData[$i][$ServiceTypeCol];
				$CreatedDate = $FileData[$i][$CreatedDateCol];
				$ApprovedDate = $FileData[$i][$ApprovedDateCol];
				$Address = $FileData[$i][$AddressCol];
				$WorkingUnit = $FileData[$i][$WorkingApartmentCol];
				$finalCatID = '';
				
				//Check whether Requested Category exits or not of CategoryID		
				if($Name == '')
				{
					$errorPrintData = true;
					if($this->errorCheck) echo $errormsg .= "<BR>Name Missing  :: ";
				}
				if($Phone == '')
				{
					//$errorPrintData = true;
					if($this->errorCheck) echo "<BR>Phone Number Default Taken";
					$Phone = '0';
					if($this->errorCheck) echo "<BR>Phone number default: ".$Phone;
				}
				if($Gender == '')
				{
					//$errorPrintData = true;
					if($this->errorCheck) echo "<BR>Gender Default Taken";
					$Gender = '-';
					if($this->errorCheck) echo "<BR>Gender number default: ".$Gender;
				}
				if($CategoryType == '')
				{
					$errorPrintData = true;
					if($this->errorCheck) echo $errormsg .= "<BR>Service Type Missing  :: ";
				}
				if($Address == '')
				{
					$errorPrintData = true;
					if($this->errorCheck) echo $errormsg .= "<BR>Address Missing  :: ";
				}
				if($WorkingUnit == '')
				{
					//$errorPrintData = true;
					if($this->errorCheck) echo "<BR>Working unit Missing ::";
				}
				
				
				//echo "yo1";
				if(in_array(strtolower($CategoryType),$CategoryArray))
				{
					
					//If Category exits then we now get the position of category and with position we get the id of category
					
					 $CategoryPresent = array_search(strtolower($CategoryType),$CategoryArray);
					 $finalCatID = $catID[$CategoryPresent];
					 
				}
				else
				{
					//If Category not found the we save the category as Other Services 	
					$errorPrintData = true;	
					$errormsg .= "Service Type Not Found : &lt;" . $CategoryType . "&gt;";
				}
				
				//Making array of columns that present in service_prd_reg table And Putting Quoate Because it's a string 
				$TableValue = array("'".$Name."'","'".$Gender."'",$Phone,"'".$Address."'");
				//echo "<BR>table:"+$TableValue;
				//Here is another array to store the columns values 
				$ValuestoInsert = $_SESSION['society_id'];
				for($j = 0; $j < sizeof($ColPosition); $j++)
				{
					if(in_array($ColPosition[$j],$indexes))
					{
						if($ColPosition[$j] == '' && $ColPosition[$j] !== 0)
						{
							continue;
						}
						else
						{
						$ValuestoInsert .= ', '.$TableValue[$j];		
						}
					}
				}
				if($this->errorCheck) var_dump($ValuestoInsert);
				// It's Check There is No More Error in file
				
				//$result =  $this->m_dbConnRoot->select($queryStaffIDs);
//				$StaffID_array = array_column($result, 'staff_id');
//				if($this->errorCheck) var_dump($StaffID_array);
				
				if($ErrorPrintHead == false)
				{
					$InsertSerPrd = "Insert into service_prd_reg(".$ColumntoInsert.") values(".$ValuestoInsert.")";
					//array_push($SerPrdArray,$InsertSerPrd);
					if($this->errorCheck) echo "<BR>".$InsertSerPrd;
					if($enableinsert) $SelectedSerPrd = $this->m_dbConnRoot->insert($InsertSerPrd);
					if($this->errorCheck) echo "<BR><BR><BR>Service Provider: ".$TableValue[0];
					if($this->errorCheck) echo "<BR>Registered Service Provider ID: ".$SelectedSerPrd;
					//var_dump($SelectedSerPrd);
					
					$SelectSerPrd = "Select service_prd_reg_id from service_prd_reg order by service_prd_reg_id desc limit 1";
					if($this->errorCheck) echo "<BR>".$SelectSerPrd;
					$SelectedSerPrd = $this->m_dbConnRoot->select($SelectSerPrd);
					//var_dump($SelectedSerPrd);
					if($this->errorCheck) var_dump($SelectedSerPrd[0]['service_prd_reg_id']);
				//	var_dump($SerPrdArray);
				
					$values = explode(',',$ValuestoInsert); 
					$serprd_data = 'Name &lt;' . $values[1] . '&gt; :: Gender &lt; '.$values[2] .'&gt; :: Phone &lt; '.$values[3].'&gt';
					if($$values[4] <> '')
					{
						$serprd_data .= '  :: Address &lt'.$values[4].'&gt';
					}
					if($CategoryType <> '')
					{
						$serprd_data .= ' :: Service Type &lt;'.$CategoryType.'&gt';
					}
					
					$Insert_Spr_Cat = "Insert into spr_cat(`service_prd_reg_id`,`cat_id`,`status`,`TimeStamp`) values('".$SelectedSerPrd[0]['service_prd_reg_id']."','".$finalCatID."','Y','".$timestamp['DateTime']."')";
					if($this->errorCheck) echo "<BR>".$Insert_Spr_Cat;
					//dbcon->Insert(
					//array_push($SprCatArray,$Insert_Spr_Cat);
					if($enableinsert) $ResultSerCat = $this->m_dbConnRoot->insert($Insert_Spr_Cat);
					//if($this->errorCheck) echo "<BR>Insert cat statement: " . $Insert_Spr_Cat;
					//If user checked the Working Apartment Column only this code executes
				//when process starts there you make query to get all existing staffid in array. Also get $MaxStaffID
				//Check here is $StaffID is in that array
				//if its in array 
				//return "StaffID $StaffID is not unique."
				//If staff id is correct, then return 0 else return error string
					
				$StaffID_ErrorString=0;
				if($this->errorCheck) echo "<BR>Before validate Staff ID".$StaffID;	
				if($this->errorCheck) echo "<BR> isnumber : " . is_numeric($StaffID);
				$IsStaffIDValid = $this->ValidateStaffID($StaffID,$StaffID_array);
				//print_r($StaffID_ErrorString);
				if($this->errorCheck) echo "<BR>staff valid check: ".$IsStaffIDValid;
			
				if($this->errorCheck) echo "<Br> error string for staffid: '".$StaffID_ErrorString."'";
				if($IsStaffIDValid > 0)
				{
					for($t = 1 ; $t <= sizeof($StaffID_array); $t++)
					{
						if(in_array((string)$t,$StaffID_array))
						{
							$contimax = $t;
							//if($this->errorCheck) echo "<BR>".$contimax;
						}
						else
						{
							//if($this->errorCheck) echo "<BR> Break";
							break;
						}
					}
					//echo $IsStaffIDValid;
					if($this->errorCheck) echo "<BR>Generating .. ";
					//$MaxStaffID = GenerateMaxStaffID($sqlMaxNumber);
					//$LastStaffID = $this->m_dbConnRoot->select($sqlMaxNumber);			
					//$MaxStaffID = $LastStaffID[0]['maxStaffID'];
					$MaxStaffID = $contimax;
					if($this->errorCheck) echo "<BR>ContinuousMaxStaffID ".$MaxStaffID;
					$StaffID = $MaxStaffID + 1;
					if($this->errorCheck) echo "<BR>Generated Least Possible Staff ID: ".$StaffID;
				}
				$InsertSerSocLink = "Insert into service_prd_society(`provider_id`,society_id,society_staff_id,status) values('".$SelectedSerPrd[0]['service_prd_reg_id']."','".$_SESSION['society_id']."','".$StaffID."','Y')";
				if($this->errorCheck) echo "<BR>".$InsertSerSocLink;
				//array_push($SerSocArray,$InsertSerSocLink);
				if($enableinsert) $ResultSerSoc = $this->m_dbConnRoot->insert($InsertSerSocLink);
					
					if($this->errorCheck) echo "<BR>Registered Staff ID: ".$StaffID;
					array_push($StaffID_array,$StaffID);
					$serprd_data .= ' :: Working Apartment &lt;'.$WUnit[$k].'&gt';
					
					if($this->errorCheck) echo "<BR>working apts: ". $WorkingUnit;
					
					//if(in_array($ServiceTypeCol,$indexes))
//					{
//							$Service = array();
//							$Service = explode(';',$CategoryType);
//							for($k = 0 ; $k < sizeof($CategoryType); $k++)
//							{
//									if($WUnit[$k] <> '')
//									{
//									
//									
//									}
//								
//							}
//						
//					}
					if(in_array($WorkingApartmentCol,$indexes))
					{
						//According to number of unit assing it will explode in to an array
						$WUnit = array();
						if($WorkingUnit <> "Society Office" and $WorkingUnit <> "society office")
						{$WUnit = explode(' ',$WorkingUnit);}
						else{$WUnit = array($WorkingUnit);}
						for($k = 0 ; $k < sizeof($WUnit); $k++)
						{
							//echo "unuit:".$WUnit[$k];
							$UnitNumber = $WUnit[$k];
							if($WUnit[$k] <> '')
							{
//								$IsSlashPresent = substr_count($WUnit[$k],"/");
//								$IsDashPresent = substr_count($WUnit[$k],"-");
//								echo "slash check: ".$IsSlashPresent;
//								if($IsSlashPresent > 0)
//								{
//									$UnitDetails = explode('/',$WUnit[$k]);	
//									$UnitWing = $UnitDetails[0];
//									$UnitNumber = $WUnit[$k];
//								}
//								else if($IsDashPresent > 0)
//								{
//									if(sizeof($WUnit[$k]) > 1)
//									{
//										$UnitDetails = explode('-',$WUnit[$k]);	
//										$UnitWing = $UnitDetails[0];
//										$UnitNumber = $UnitDetails[1];
//									}
//									else
//									{
//										$UnitWing = $WUnit[$k];
//										$UnitNumber = 'Society-Office';
//									}
//									
//								}
//								else
//								{
//									$fullunit = $WUnit[$k];
//									//$a = is_numeric($fullunit);
//									//if(is_numeric($fullunit))
//									//{
//										echo "<BR> inside: ";
//										//$UnitNumber = $fullunit;
//										
//									//}
//									//else
//								//	{
//										$UnitWing = substr($fullunit,0,1);
//										echo "<BR> inside: ".$UnitWing;
//										$UnitNumber = substr($fullunit,1);
//										echo "<BR> inside: ".$UnitNumber;
//									//}
//									
//								}
								
								if($this->errorCheck) echo '<BR>This is Unit Number '.$WUnit[$k];
								//Now When We get the Unit Wing And unitNumber so we will fetch information and inser the data
								//$WingID = $this->m_dbConn->select("Select wing_id from wing where status = 'Y' AND wing = '".$UnitWing."' AND society_id = '".$_SESSION['society_id']."'");
								
								//$ServiceUnitsDetails = $this->m_dbConn->select("Select u.unit_id, mm.owner_name from unit as u JOIN member_main as mm ON mm.unit = u.unit_id WHERE u.wing_id = '".$WingID[0]['wing_id']."' AND u.unit_no = '".$UnitNumber."' AND ownership_status = 1");
								
								$ServiceUnitsDetailsQ = "Select u.unit_id, mm.owner_name from unit as u JOIN member_main as mm ON mm.unit = u.unit_id WHERE u.unit_no = '".$WUnit[$k]."' AND ownership_status = 1 and u.society_id = '".$_SESSION['society_id']."'";
								
								$ServiceUnitsDetails = $this->m_dbConn->select($ServiceUnitsDetailsQ);
								if($this->errorCheck) var_dump($ServiceUnitsDetails);
								if(sizeof($ServiceUnitsDetails) == 0)
								{
									//$errorPrintData = true;
									$errormsg = "Working Unit  &lt;" . $WUnit[$k] . "&gt; of &lt;$Name&gt; Not Found. Pl set manually";
									if($this->errorCheck) echo $errormsg;
									$this->obj_utility->logGenerator($errorfile,$count,$errormsg,"E");
									$count++;
									
								}
								else
								{
									if($this->errorCheck) echo "<BR>Unit found";
									$UnitNoDetails = $UnitNumber.' ['.$ServiceUnitsDetails[0]['owner_name'].']';
									if($this->errorCheck) echo "<BR>Registered Unit: " . $UnitNoDetails;
									$InsertSerUnit = "Insert into service_prd_units(`service_prd_id`,unit_id,unit_no,society_id) values('".$SelectedSerPrd[0]['service_prd_reg_id']."','".$ServiceUnitsDetails[0]['unit_id']."','".$UnitNoDetails."','".$_SESSION['society_id']."')";
									//array_push($SerUnitArray,$InsertSerUnit);
									if($enableinsert) $ResultSerPrd = $this->m_dbConnRoot->insert($InsertSerUnit);
									//echo "<BR> Result: : :".$ResultSerPrd;
									if($this->errorCheck) var_dump($InsertSerUnit);
									$serprd_data .= ' :: Working Apartment &lt;'.$WUnit[$k].'&gt';
									if($this->errorCheck) echo "<BR>serprd_data: ".$serprd_data;
									
								}
							}
						}
					}
										array_push($successmsg,$serprd_data);
	
				}
				if($errorPrintData == true)
				{
					array_push($noErrorInFIle,$FileData[$i]);
					$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");			
				}	
			}
			//INSERT INTO `hostmjbt_societydb`.`service_prd_society` (`sp_id`, `provider_id`, `society_id`, `society_staff_id`, `status`) VALUES (NULL, '344', '59', 'V2', 'Y');
			if($this->errorCheck) echo "<BR>Error file";
			if($this->errorCheck) var_dump($noErrorInFIle);
			if($this->errorCheck) echo "<BR>end Error file";
			//if(sizeof($noErrorInFIle) == 0)
			if(1)
			{
				//If there is no more error in file so it will send for import
				$this->m_dbConnRoot->begin_transaction();

				$this->InsertServiceProviderRecord($SerPrdArray, $SprCatArray, $SerUnitArray, $SerSocArray);
				
				$this->m_dbConnRoot->commit();	
				$isImportSuccess = true;
				//var_dump($successmsg);
				$count=1;
				for($test = 0 ; $test < sizeof($successmsg); $test++)
				{
					$successmsg1 = $successmsg[$test];
					$successmsg1 .= '<BR><b>Imported Successfully</b>';
					$this->obj_utility->logGenerator($errorfile,$count,$successmsg1,"I");
					$count++;
				}
				$errormsg = "<BR>[Importing Service Provider Data Imported Successfully]";
				$this->obj_utility->logGenerator($errorfile,'',$errormsg);
				$errormsg = "<BR>[Importing Service Provider Data End]";
				$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
			}
			else
			{
				$errormsg = "<BR>[Importing Service Provider Data End]";
				$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
			}
			
		
		}
		catch(Exception $exp)
		{
			$this->m_dbConnRoot->rollback();
			return $exp;
		}
		//var_dump($SerUnitArray);
	}
	
	

	private function ValidateStaffID($StaffID,$StaffID_array)
	{		
		if(!is_numeric($StaffID))
		{
			if($this->errorCheck) echo "<BR>StaffID $StaffID not numeric.";
			return 1;
		}
		else
		{
			//echo "<BR>id: ".$StaffID;
			if(in_array($StaffID, $StaffID_array))
			{
				if($this->errorCheck) echo "<BR>STAFF-ID: $StaffID already exists...";
				return 2;
			}
			else
			{
				return 0;
			}
			
		}
	}

	private function GenerateMaxStaffID($sqlMaxNumber)
	{		
			$LastStaffID = $this->m_dbConnRoot->select($sqlMaxNumber);			
			$MaxStaffID = $LastStaffID[0]['maxStaffID'];
			if($this->errorCheck) echo "<BR>MaxStaffID ".$MaxStaffID;
		return $MaxStaffID;
	}				
	
	private function InsertServiceProviderRecord($SerPrdArray, $SprCatArray, $SerUnitArray, $SerSocArray)
	{	
		//if($this->errorCheck) var_dump($SerPrdArray);
		//if($this->errorCheck) var_dump($SerSocArray);
		//if($this->errorCheck) var_dump($SerUnitArray);
	
		for($i = 0 ; $i < sizeof($SerPrdArray); $i++)
		{
			//Inserting the query for service_prd_reg and spr_cat in one loop, because always both array contain same size and same sequence
			
			//if($this->errorCheck) echo "<BR><BR>". $SerPrdArray[$i];
			//$ResultSerPrd = $this->m_dbConnRoot->insert($SerPrdArray[$i]);
			//if($this->errorCheck) echo "<BR>". $SprCatArray[$i];
			//$ResultSerCat = $this->m_dbConnRoot->insert($SprCatArray[$i]);
			//if($this->errorCheck) echo "<BR>". $SerSocArray[$i];
			//$ResultSerSoc = $this->m_dbConnRoot->insert($SerSocArray[$i]);
			
		}
		
		for($j = 0; $j < sizeof($SerUnitArray); $j++)
		{
			//Inserting the SerUnit Array 
			//if($this->errorCheck) echo "<BR>". $SerUnitArray[$j];
			//$ResultSerPrd = $this->m_dbConnRoot->insert($SerUnitArray[$j]);
		}
	}
	
}
?>
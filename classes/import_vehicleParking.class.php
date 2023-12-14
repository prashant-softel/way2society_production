<?php
include_once("include/dbop.class.php");
include_once("utility.class.php");
include_once("CsvOperations.class.php");
include_once("dbconst.class.php");
include_once("include/fetch_data.php");

class import_vehicleParking 
{	
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_utility;
	public $errorfile_name;
	public $errorLog;
	public $obj_fetch;
	public $actionPage = '../import_vehicleParking.php';
	public $csv;
	
	public $allColumns = array('WingNo', 'UnitNo', 'VehicleType', 'ParkingType','ParkingType2','ParkingSlotNo','ParkingSlotOwned','ParkingStickerNo', 'RegistrationNo','OwnerName', 'VehicleMake','VehicleModel','Colour','RegDocSubmitted','Note');

	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->obj_utility = new utility($this->m_dbConn);
		$this->csv = new CsvOperations();
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

		$this->errorfile_name = '../logs/import_log/'.$Foldername.'/import_vehicle_parking_'.date("d.m.Y").'_'.rand().'.html';
		$this->errorLog = $this->errorfile_name;
		$errorfile = fopen($this->errorfile_name, "a");
		$errormsg="[Importing Vehicle Parking Data Start]<BR>";
		$isImportSuccess = false;
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		
		//echo "Indexes : ";
		//var_dump($indexes);
		//The below code will check the position of columns so we identify the correct data 
		//echo "FileData : ";
		//var_dump($FileData);
		$vehicleParkingArray = array();
		$parkingType2Array = array();
		$SerUnitArray = array();

		try
		{
			$this->m_dbConn->begin_transaction();
			$WingNoCol = array_search('WingNo',$FileData[0]);
			$UnitNoCol = array_search('UnitNo',$FileData[0]);
			$VehicleTypeCol = array_search('VehicleType',$FileData[0]);
			$ParkingTypeCol = array_search('ParkingType',$FileData[0]);
			$ParkingType2Col = array_search('ParkingType2',$FileData[0]);
			$ParkingSlotNoCol = array_search('ParkingSlotNo',$FileData[0]);
			$ParkingStickerNoCol = array_search('ParkingStickerNo',$FileData[0]);
			$RegistrationNoCol = array_search('RegistrationNo',$FileData[0]);	
			$OwnerNameCol = array_search('OwnerName',$FileData[0]);
			$VehicleMakeCol = array_search('VehicleMake',$FileData[0]);
			$VehicleModelCol = array_search('VehicleModel',$FileData[0]);
			$ColourCol = array_search('Colour',$FileData[0]);	
			$RegDocSubmittedCol = array_search('RegDocSubmitted',$FileData[0]);
			
			//echo "RegDocSubmitted : ";
			//var_dump($RegDocSubmittedCol);	
			$successmsg = array();
			//Validation Start Here
			$m_TraceDebugInfo = "";
			$rowCount = 1;
			$ErrorPrintHead = false;
			$noErrorInFIle = array();
			if($WingNoCol == '' && $WingNoCol <> 0)
			{
				$ErrorPrintHead = true;
				$m_TraceDebugInfo .= "Wing No Head Missing   :: ";
			}
			if($UnitNoCol == '')
			{
				$ErrorPrintHead = true;
				$m_TraceDebugInfo .= "Unit No Head Missing   :: ";
			}
			if($VehicleTypeCol == '')
			{
				$ErrorPrintHead = true;
				$m_TraceDebugInfo .= "Vehicle Type Head Missing   :: ";
			}
			if($RegistrationNoCol == '')
			{
				$ErrorPrintHead = true;
				$m_TraceDebugInfo .= "Registration No Head Missing   :: ";
			}
			if(array_search($ParkingTypeCol,$indexes))
			{
				if($ParkingTypeCol == '')
				{
					$ErrorPrintHead = true;
					$m_TraceDebugInfo .= "Parking Type Head Missing   :: ";
				}	
			}
			if(array_search($ParkingType2Col,$indexes))
			{
				if($ParkingType2Col == '')
				{
					$ErrorPrintHead = true;
					$m_TraceDebugInfo .= "Parking Type2 Head Missing  :: ";
				}	
			}
			if(array_search($ParkingSlotNoCol,$indexes))
			{
				if($ParkingSlotNoCol == '')
				{
					$ErrorPrintHead = true;
					$m_TraceDebugInfo .= "Parking Slot No Head Missing  :: ";
				}	
			}
			if(array_search($ParkingStickerNoCol,$indexes))
			{
				if($ParkingStickerNoCol == '')
				{
					$ErrorPrintHead = true;
					$m_TraceDebugInfo .= "Parking Sticker No Head Missing  :: ";
				}	
			}
			if(array_search($RegistrationNoCol,$indexes))
			{
				if($RegistrationNoCol == '')
				{
					$ErrorPrintHead = true;
					$m_TraceDebugInfo .= "Registration No Head Missing  :: ";
				}	
			}
			if(array_search($OwnerNameCol,$indexes))
			{
				if($OwnerNameCol == '')
				{
					$ErrorPrintHead = true;
					$m_TraceDebugInfo .= "Owner Name Head Missing  :: ";
				}	
			}
			if(array_search($VehicleMakeCol,$indexes))
			{
				if($VehicleMakeCol == '')
				{
					$ErrorPrintHead = true;
					$m_TraceDebugInfo .= "Vehicle Make Head Missing  :: ";
				}	
			}
			if(array_search($VehicleModelCol,$indexes))
			{
				if($VehicleModelCol == '')
				{
					$ErrorPrintHead = true;
					$m_TraceDebugInfo .= "Vehicle Model Head Missing  :: ";
				}	
			}
			if(array_search($ColourCol,$indexes))
			{
				if($ColourCol == '')
				{
					$ErrorPrintHead = true;
					$m_TraceDebugInfo .= "Colour Head Missing  :: ";
				}	
			}
			if(array_search($RegDocSubmittedCol,$indexes))
			{
				if($RegDocSubmittedCol == '')
				{
					$ErrorPrintHead = true;
					$m_TraceDebugInfo .= "Reg Doc Submitted Head Missing  :: ";
				}	
			}		
			if($ErrorPrintHead == true)
			{
				array_push($noErrorInFIle,$ErrorPrintHead);
				$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"E");	
			}
			//It is necessary always keep the TableColName, ColPosition And Tablevalue array present below with same sequence
			$TableBikeColName = array('ParkingType','ParkingType2','parking_slot','parking_sticker','bike_reg_no','bike_owner','bike_model','bike_make','bike_color','RegDocSubmitted');
			
				$TableCarColName = array('ParkingType','ParkingType2','parking_slot','parking_sticker','car_reg_no','car_owner','car_model','car_make','car_color','RegDocSubmitted');
				$ColPosition = array($ParkingSlotNoCol,$ParkingStickerNoCol,$RegistrationNoCol,$OwnerNameCol,$VehicleMakeCol,$VehicleModelCol,$ColourCol,$RegDocSubmittedCol);
				$ColPosition2 = array($ParkingTypeCol,$ParkingType2Col,$ParkingSlotNoCol,$ParkingStickerNoCol,$RegistrationNoCol,$OwnerNameCol,$VehicleMakeCol,$VehicleModelCol,$ColourCol,$RegDocSubmittedCol);
				$ParkingTypeArray = array();
				$ParkingTypeId = array();
				
				/*echo "TableBikeColName : ";
				var_dump($TableBikeColName);
				
				echo "TableCarColName : ";
				var_dump($TableCarColName);
				
				echo "ColPosition : ";
				var_dump($ColPosition);*/

			//ColumntoInsert is a string which collect the table column name according to checked in datadisplay page
					
			//$c = 0;
			$PakringTypeData = $this->m_dbConn->select("SELECT Id, ParkingType from parking_type where status = 'Y'");
			
			//Pushing the Fetch category from databse to new array so it convert in index array which hepl us to perform array_search function
			for($i = 0 ; $i< sizeof($PakringTypeData); $i++)
			{ 
				array_push($ParkingTypeArray,strtolower($GetCategory[$i]['ParkingType']));
				array_push($ParkingTypeId,$PakringTypeData[$i]['Id']);	
			}
	
			for($i = 1 ; $i < sizeof($FileData)-1 ; $i++)
			{
				$rowCount++;
				$errormsg = "";
				$errorPrintData = false;
				//Here storing the value in New Varialbe according to there coloum name
				$timestamp = getCurrentTimeStamp();	
				
				$wingNo = $FileData[$i][$WingNoCol];
				$unitNo = $FileData[$i][$UnitNoCol];
				$vehicleType = $FileData[$i][$VehicleTypeCol];
				 $parkingType = $FileData[$i][$ParkingTypeCol];
				$parkingType2 = $FileData[$i][$ParkingType2Col];
				$parkingSlot = $FileData[$i][$ParkingSlotNoCol];
				$parkingSticker = $FileData[$i][$ParkingStickerNoCol];
				$regNo = $FileData[$i][$RegistrationNoCol];
				$ownerName = $FileData[$i][$OwnerNameCol];
				$make = $FileData[$i][$VehicleMakeCol];
				$model = $FileData[$i][$VehicleModelCol];
				$color = $FileData[$i][$ColourCol];
				$regDocSubmitted = $FileData[$i][$RegDocSubmittedCol];

				$getMemberId = "Select mm.member_id,mm.owner_name from member_main as mm, unit as u, wing as w where u.unit_id = mm.unit and u.wing_id = w.wing_id and w.wing = '".$wingNo."' and u.unit_no = '".$unitNo."' and u.society_id = '".$_SESSION['society_id']."' and mm.ownership_status = '1';";
				//Check whether Requested Category exits or not of CategoryID		
				$memData = $this->m_dbConn->select($getMemberId);
				$finalMemberId = $memData[0]['member_id'];
				$finalMemberName = $memData[0]['owner_name'];
				//echo $parkingSlot;
				//die();
				if($wingNo == '')
				{
					$errorPrintData = true;
					$errormsg .= "Wing No Missing  :: <br>";
				}
				else
				{
					 $wingNo1  = $wingNo;
				}

				if($unitNo == '')
				{
					$errorPrintData = true;
					$errormsg .= "Unit Number Missing  ::<br>";
				}
				else
				{
					 $unitNo1  = $unitNo;
				}
				

				if($vehicleType == '')
				{
					$errorPrintData = true;
					$errormsg .= "Vehicle Type Missing  :: <br>";
				}
				elseif(($vehicleType <> "Car" ) && ($vehicleType <> "Bike" )) 
				{
					$errorPrintData = true;
					$errormsg .= "Please Correct Vehicle Type :<br> ";
				}

				if($regNo == '')
				{
					$errorPrintData = true;
					$errormsg .= "Registration Number Missing  :: <br>";
				}
				else
				{
					 $regNo1  = $regNo;
				}
				
				
				if($ownerName == '')
				{
					$errorPrintData = true;
					$errormsg .= "Owner Name Missing  :: <br>";
				}
				else
				{
					 $ownerName1  = $ownerName;
				}

				$finalParkingTypeId = '';
				$finalParkingTypeId2 = '';
				if(in_array(strtolower($parkingType),$PakringTypeData))
				{
					
					//If Category exits then we now get the position of category and with position we get the id of category

					 $parkingTypePresent = array_search(strtolower($parkingType),$PakringTypeData);
					 $finalParkingTypeId = $ParkingTypeId[$parkingTypePresent];
					 
				}
				if(in_array(strtolower($parkingType2),$PakringTypeData))
				{
					
					//If Category exits then we now get the position of category and with position we get the id of category

					 $parkingTypePresent = array_search(strtolower($parkingType2),$PakringTypeData);
					 $finalParkingTypeId2 = $ParkingTypeId[$parkingTypePresent];
					 
				}
				
				//Making array of columns that present in service_prd_reg table And Putting Quoate Because it's a string 
				$ColumntoInsert = 'member_id';

				/*for($j = 0; $j < sizeof($ColPosition2); $j++)
				{
					//making string of columns which going to insert. Here it is making the string of only those columns which is checked; 
					//var_dump($indexes);
					if(in_array($ColPosition2[$j],$indexes))
					{
						if($ColPosition2[$j] == '' && $ColPosition2[$j] !== 0)
						{
							continue;
						}
						else
						{
							if($vehicleType == "Car")
							{
								$ColumntoInsert .= ', '.$TableCarColName[$j];
							}
							else if($vehicleType == "Bike")
							{
								$ColumntoInsert .= ', '.$TableBikeColName[$j];
							}
						}
					}
				}*/
				//echo "Column Names final: ";
				//var_dump($ColumntoInsert);
				
			
				$TableValue = array("'".$finalMemberId."'","'".$parkingSlot."'","'".$parkingSticker."'","'".$regNo."'","'".$ownerName."'","'".$model."'","'".$make."'","'".$color."'","'".$regDocSubmitted."'");
				
				//echo "Table Values : ";
				//var_dump($TableValue);
				//Here is another array to store the columns values 
				$ValuestoInsert = "";
				/*if($finalMemberId != '')
				{
					$ValuestoInsert = $finalMemberId;
				}*/
				/*else
				{
					$errorPrintData = true;	
					$errormsg .= "Member Not Found with unit no: &lt;" . $unitNo . "&gt; & wing : &lt; ".$wingNo." &gt;";
				}*/
				
				/*if($finalParkingTypeId != "")
				{
					$ValuestoInsert .= ",".$finalParkingTypeId;
				}
				else
				{
					$ValuestoInsert .= ", 0";
				}*/
				/*if($finalParkingTypeId2 != "")
				{
					$ValuestoInsert .= ','.$finalParkingTypeId2;
				}
				else
				{
					$ValuestoInsert .= ", 0";
				}*/
				/*for($j = 0; $j < sizeof($ColPosition); $j++)
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
				}*/
				//echo "ValuestoInsert final : ";
			//	var_dump($ValuestoInsert);
				// It's Check There is No More Error in file
				
				if($ErrorPrintHead == false)
				{
					if($vehicleType == "Car")
					{
						echo $InsertVehicleParking = "Insert into mem_car_parking (`member_id`,`ParkingType`,`parking_slot`,`parking_sticker`,`car_reg_no`,`car_owner`,`car_model`,`car_make`,`car_color`) values('".$finalMemberId."','".$parkingType."','".$parkingSlot."','".$parkingSticker."','".$regNo1."','".$ownerName1."','".$model."','".$make."','".$color."')";
						
					//die();
					}
					else if($vehicleType == "Bike")
					{
						$InsertVehicleParking = "Insert into mem_bike_parking (`member_id`,`ParkingType`,`parking_slot`,`parking_sticker`,`bike_reg_no`,`bike_owner`,`bike_model`,`bike_make`,`bike_color`) values('".$finalMemberId."','".$parkingType."','".$parkingSlot."','".$parkingSticker."','".$regNo1."','".$ownerName1."','".$model."','".$make."','".$color."')";
					}
					//echo "InsertVehicleParking : ".$InsertVehicleParking;
					array_push($vehicleParkingArray,$InsertVehicleParking);
					//var_dump($vehicleParkingArray);
					//$values = explode(',',$ValuestoInsert); 
					//$vehicleParkingData = 'MemberId &lt;' . $values[1] . '&gt; :: ParkingType &lt; '.$values[2] .'&gt; :: ParkingType2 &lt; '.$values[3].'&gt';
				}
				
				if($errorPrintData == true)
				{
					array_push($noErrorInFIle,$FileData[$i]);
					$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");			
				}
				$ColumntoInsert = "member_id";
				$ValuestoInsert = "";	
			}
			
			
			if(sizeof($noErrorInFIle) == 0)
			{
				//If there is no more error in file so it will send for import
				$this->InsertVehicleParking($vehicleParkingArray);
				
				$this->m_dbConn->commit();	
				$isImportSuccess = true;
				//echo "Commit";
				$count=1;
				for($test = 0 ; $test < sizeof($successmsg); $test++)
				{
					$successmsg1 = $successmsg[$test];
					$successmsg1 .= '<BR><b>Imported Successfully</b>';
					$this->obj_utility->logGenerator($errorfile,$count,$successmsg1,"I");
					$count++;
				}
				$errormsg = "<BR>[Importing Vehicle Data Imported Successfully]";
				$this->obj_utility->logGenerator($errorfile,'',$errormsg);
				$errormsg = "<BR>[Importing Vehicle Data End]";
				$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
			}
			else
			{
				$errormsg = "<BR>[Importing Vehicle Data End]";
				$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
			}
		
		}
		catch(Exception $exp)
		{
			$this->m_dbConn->rollback();
			//echo "Roll BAck";
			return $exp;
		}
			
	}
	
	private function InsertVehicleParking($VehcileParkingArray)
	{	
		//var_dump($VehcileParkingArray);
		for($i = 0 ; $i < sizeof($VehcileParkingArray); $i++)
		{
			///echo $VehcileParkingArray[$i]."<br>";
			//Inserting the query for service_prd_reg and spr_cat in one loop, because always both array contain same size and same sequence
			$result = $this->m_dbConn->insert($VehcileParkingArray[$i]);
			//echo "result : ".$result;
		}
	}
	
}
?>
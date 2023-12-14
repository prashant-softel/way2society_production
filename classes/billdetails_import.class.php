<?php
//include_once("include/dbop.class.php");
include_once("defaults.class.php");
include_once("changelog.class.php");
include_once("utility.class.php");
include_once("register.class.php");
include_once("include/fetch_data.php");
class billdetails_import 
{
	
	public $m_dbConn;
	public $m_dbConnRoot;
	private $obj_default;
	public $changeLog;
	private $obj_utility;
	public $errofile_name;
	public $m_objLog;
	public $obj_fetch;
	public $actionPage = "../import_opening_balance.php";
	
	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->obj_default = new defaults($this->m_dbConn);
		$this->changeLog = new changeLog($this->m_dbConn);
		$this->obj_utility = new utility($this->m_dbConn);

		$this->obj_fetch = new FetchData($this->m_dbConn);
		$a = $this->obj_fetch->GetSocietyDetails($_SESSION['society_id']);


		date_default_timezone_set('Asia/Kolkata');	

		$Foldername = $this->obj_fetch->objSocietyDetails->sSocietyCode;

		if (!file_exists('../logs/import_log/'.$Foldername)) 
		{
			mkdir('../logs/import_log/'.$Foldername, 0777, true);
		}

		$this->errofile_name = '../logs/import_log/'.$Foldername.'/fd_import_errorlog_'.date("d.m.Y").'_'.rand().'.html';
		$this->errorLog = $this->errofile_name;

	}
	
	public function CSVBillDetailsImport()
	{
		//echo '11';
		
		if(isset($_POST["Import"]))
		{
			//echo '1';
			if(isset($_FILES['file']) && $_FILES['file']['error'] == 0)
			{
				
				$result = "0";
				  $ext = pathinfo($_FILES['file'] ['name'], PATHINFO_EXTENSION);
				//$fileName = "files/" . $dateTimeNow. ".csv";
				 $tempName = $_FILES['file'] ['tmp_name'];
				/*
				$original_file_name='BuildingID.csv';
				//echo $_FILES['file'] ['name'];
				if(($_FILES['file'] ['name']) != "$original_file_name") {
					  //exit("Does not match");
					  $result = '<p>File Name Does Not Match(only BuildingID.csv file accepted)...</p>';
					  
					}
				else 
				*/
				if($ext <> '' && $ext <> 'csv')
				{	
					$result = '<p>Invalid file format selected. Expected csv file format</p>';
				}
				else
				{
					//if ( move_uploaded_file ($_FILES['file'] ['tmp_name'], $fileName)  )
					if (isset($_FILES['file']['error']) || is_array($_FILES['file']['error']))
					{  
						$result = '<p> Bill Details Data Uploading Process Started <' . $this->getDateTime() . '> </p>';
						
						$result .= $this->UploadData($tempName);
						
						$result .= '<p> Bill Details Data Uploading Process Complete <' . $this->getDateTime() . '> </p>';
					}
					else
					{ 
						echo $_FILES['file'] ['error'];
						switch ($_FILES['file'] ['error'])
						{
							case 1:
								   echo '<p> The file is bigger than this PHP installation allows</p>';
								   $result = '<p> The file is bigger than this PHP installation allows</p>';
								   break;
							case 2:
								   echo '<p> The file is bigger than this form allows</p>';
								   $result = '<p> The file is bigger than this form allows</p>';
								   break;
							case 3:
								   echo '<p> Only part of the file was uploaded</p>';
								   $result = '<p> Only part of the file was uploaded</p>';
								   break;
							case 4:
								   echo '<p> No file was uploaded</p>';
								   $result = '<p> No file was uploaded</p>';
								   break;
						}
					} 
				}
			}
			else if(isset($_FILES['file']) && $_FILES['file']['error'] <> 0)
			{
				
				//echo '2';
				$errorCode = $_FILES['file']['error']; 
				switch ($errorCode)
				{
					case 1:
						   //echo '<p> The file is bigger than this PHP installation allows</p>';
						   $result = '<p> The file is bigger than this PHP installation allows</p>';
						   break;
					case 2:
						   //echo '<p> The file is bigger than this form allows</p>';
						   $result = '<p> The file is bigger than this form allows</p>';
						   break;
					case 3:
						   //echo '<p> Only part of the file was uploaded</p>';
						   $result = '<p> Only part of the file was uploaded</p>';
						   break;
					case 4:
						   //echo '<p> No file was uploaded</p>';
						   $result = '<p> No file was uploaded</p>';
						   break;
				}
			}
			//echo '<body onload="parent.doneloading(\''.$result.'\')"></body>'; 
			//return $result;
			
		}
		
		else
		{
			//echo '3';
			}
	}
	public function ImportData($SocietyID)
	{ 
		$errorfile = fopen($this->errofile_name, "a");
		$result = $this->UploadData($_FILES['upload_files']['tmp_name'][0],$errorfile);
		
		$message = '';
		if($result == true)
		{
			$message = 'File uploaded successfully';
		}
		else
		{
			$message = 'Error in uploading file';
		}
		
		return $message;
	}
	
	public function UploadData($fileName,$errorfile)
	{
		//$filename="C:\\wamp\\www\\OneDrive\\sujit\\beta_aws\\BillDetail.csv";		
		$UpdateFlag = false;
		if (file_exists($fileName))
		{
			$file = fopen($fileName,"r");
			$errormsg="[Importing Member Opening Balance]	";
			$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
			$isImportSuccess = false;
			$linecount=0;
			$periodname=$_POST["eperiod"];
			$totalCount = 0;
			$successcount = 0;
			$prevPeriod = $this->getPrevPeriod($_POST['Period']);
			//die();
			if(isset($_POST['Year']))
			{
				$_SESSION['default_year'] = $_POST['Year'];
			}			
		}
		while (($row = fgetcsv($file)) !== FALSE)
		{	
		$errormsg = "";
			if($row[0] <> '')
			{
				$rowCount++;
				$totalCount = $rowCount;
				if($rowCount == 1)
				{  
					$BCode=array_search(BCode,$row,true);
					$FCode=array_search(FCode,$row,true);
					$BillDate=array_search(BillDate,$row,true);
					$DueDate=array_search(DueDate,$row,true);
					$BillNo=array_search(BillNo,$row,true);
						
					$TotalAmount=array_search(BalancePrincipal_PrevYear,$row,true);
					$Interest=array_search(BalanceInterest_PrevYear,$row,true);
					$AmountPayable=array_search(BalanceAmount_PrevYear,$row,true);
					$PrincipalArrears=array_search(BalancePrincipal_PrevYear,$row,true);
					$InterestArrears=array_search(BalanceInterest_PrevYear,$row,true);
					$BillAmount = array_search(BalanceAmount_PrevYear,$row,true);
					$BalanceSuppPrincipal_PrevYear_Index = array_search(BalanceSuppPrincipal_PrevYear,$row,true);
					$BalanceSuppInterest_PrevYear_Index = array_search(BalanceSuppInterest_PrevYear,$row,true);
					$csvColUpdateAssetTableFalg = array_search(UpdateAssetTable,$row,true);
					//echo '<br> csvColUpdateAssetTableFalg : '.$csvColUpdateAssetTableFalg;
						
					$QtrMonth=array_search(QtrMonth,$row,true);
					if($csvColUpdateAssetTableFalg === "")
					{
						if(!isset($BCode) || !isset($FCode) || !isset($BillDate) ||  !isset($DueDate) ||  !isset($BillNo) ||  !isset($TotalAmount) || !isset($Interest) ||  !isset($AmountPayable) || !isset($PrincipalArrears)  || !isset($InterestArrears) ||  !isset($BillAmount))
						{
							$result = '<p>Column Names Not Found Cant Proceed Further......</p>'.'Go Back';
							$errormsg=" Column names in file ".$fileName." line " .$rowCount. "not match\n";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								
							return $result;
							exit(0);
						}	
					}
					else
					{
						//update asset flag set means file called individualy after society setup
						$UpdateFlag = true;
						if(!isset($FCode) || !isset($BillDate) ||  !isset($DueDate)  ||  !isset($TotalAmount) || !isset($Interest) ||  !isset($AmountPayable) || !isset($PrincipalArrears)  || !isset($InterestArrears) ||  !isset($BillAmount))
						{  
							$result = '<p>Column Names Not Found Cant Proceed Further......</p>'.'Go Back';
							$errormsg=" Column names in file ".$fileName." line " .$rowCount. "not match\n";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							return $result;
							exit(0);
						}	
					}					
				}
				else
				{
					$society_code=$row[$BCode];
					$UnitID=$row[$FCode];
					$BillSubTotal=$row[$TotalAmount];
					$BillInterest=$row[$Interest];
					$BillNumber=$row[$BillNo];
					$CurrentBillAmount=$row[$BillAmount];
					$BalanceSuppPrincipal_PrevYear = $row[$BalanceSuppPrincipal_PrevYear_Index];
					$BalanceSuppInterest_PrevYear = $row[$BalanceSuppInterest_PrevYear_Index];
					$datam09 = '';
					/*
					echo '<br> BillSubTotal'.$BillSubTotal;
					echo '<br> BillInterest'.$BillInterest;
					echo '<br> BalanceSuppPrincipal_PrevYear'.$BalanceSuppPrincipal_PrevYear;
					echo '<br> BalanceSuppInterest_PrevYear'.$BalanceSuppInterest_PrevYear;
					echo '<br>Index BalanceSuppPrincipal_PrevYear : '.$BalanceSuppPrincipal_PrevYear;;*/
					$MaintainceAmount = $BillSubTotal + $BillInterest;
					$SupplementryAmount = $BalanceSuppPrincipal_PrevYear + $BalanceSuppInterest_PrevYear;
					
				
					$PrincipalArrears2 = 0;
					$InterestArrears2 = 0;

					$TotalBillPayable=$row[$AmountPayable];
					$QtrMonth2=$row[$QtrMonth];
					$QtrMonthNewValue=str_replace(",","-",$QtrMonth2);
							
					if(strcmp($QtrMonthNewValue,$periodname)==0 && !$UpdateFlag)
					{
						$linecount++;
								
						if($linecount == 1)
						{
							$BillDate2=$row[$BillDate];
							$DueDate2=$row[$DueDate];
							$date_range=$this->get_date($prevPeriod);
							$diff1=$this->obj_utility->getDateDiff($date_range,$this->getDBFormatDate($BillDate2));
							$diff2=$this->obj_utility->getDateDiff($date_range,$this->getDBFormatDate($DueDate2));
									   
									   
							if($diff1 > 0)
							{
								$errormsg="BillDate &lt; ".$this->getDBFormatDate($BillDate2)."&gt; "."is &gt; than ".$date_range;
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");   
							}
									   
							if($diff2 > 0)
							{
								$errormsg="DueDate &lt;".$this->getDBFormatDate($DueDate2)."&gt;"."is &lt; than ".$date_range;
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");   
								
							}
							$desc = 'data imported..';
							$iLatestChangeID = $this->changeLog->setLog($desc, $_SESSION['login_id'], 'billregister', '--');
							$insert_into_billregister="insert into `billregister`(SocietyID,PeriodID,CreatedBy,BillDate,DueDate,Notes,LatestChangeID,BillType) values(".$_SESSION['society_id'].",'".$prevPeriod."','" . $this->m_dbConn->escapeString($_SESSION['login_id']). "','".$this->getDBFormatDate($BillDate2)."','".$this->getDBFormatDate($DueDate2)."','Import Data',".$iLatestChangeID.",'".$BillType."') ";
							//echo "insert_into_billregister2 <".$insert_into_billregister.">";
							$datam09=$this->m_dbConn->insert($insert_into_billregister);	
						}
						$search_society="select society_id from `society` where society_code='".$society_code."'";
						$result=$this->m_dbConn->select($search_society);
						$society_id=$result[0]['society_id'];
								
						$search_unit="select unit_id from `unit` where unit_no='".$UnitID."' and society_id='".$society_id."'";
						$result2=$this->m_dbConn->select($search_unit);
						$unit_id=$result2[0]['unit_id'];
						$billDetails_openingBalance = $BillSubTotal + $BillInterest;
						$assetQuery = "SELECT * FROM `assetregister` WHERE `LedgerID` = '".$unit_id."' AND `Is_Opening_Balance` = 1";
						$openingBalance = $this->m_dbConn->select($assetQuery);
						if($openingBalance)
						{
							if($billDetails_openingBalance > 0)
							{
								if(abs($billDetails_openingBalance) <> $openingBalance[0]['Debit'])
								{
									$errormsg = "Ledger &lt;".$UnitID."&gt; Opening Balance &lt;". $openingBalance[0]['Debit'] . " Dr &gt; does not match with Opening Balance of Bill &lt;" . number_format(abs($billDetails_openingBalance),2) . " Dr &gt;";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");  
								}							
							}
							else if($billDetails_openingBalance < 0)
							{
								if(abs($billDetails_openingBalance) <> $openingBalance[0]['Credit'])
								{
									$errormsg =  "Ledger &lt;".$UnitID."&gt; Opening Balance &lt;". $openingBalance[0]['Credit'] . " Cr &gt; does not match with Opening Balance of Bill &lt;" . number_format(abs($billDetails_openingBalance),2) . " Cr &gt;";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");  
								}	
							}
							else
							{
								if((abs($billDetails_openingBalance) <> $openingBalance[0]['Debit']) || (abs($billDetails_openingBalance) <> $openingBalance[0]['Credit']))
								{
									$errormsg =  "Ledger &lt;".$UnitID."&gt; Opening Balance &lt;". $openingBalance[0]['Debit'] . " Dr &gt; does not match with Opening Balance of Bill &lt;" . number_format(abs($billDetails_openingBalance),2) . " Dr &gt;";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");  
								}
							}
						}		
								$insert_into_billdetails="insert into `billdetails`(UnitID,PeriodID,BillRegisterID,BillNumber,BillSubTotal,BillInterest,CurrentBillAmount,PrincipalArrears,InterestArrears,TotalBillPayable,BillType) 
								values('$unit_id','".$prevPeriod."',".$datam09.",'$BillNumber','$BillSubTotal','$BillInterest','$CurrentBillAmount','$PrincipalArrears2','$InterestArrears2','$TotalBillPayable','".$BillType."') ";
						//echo "insert_into_billdetails <".$insert_into_billdetails.">";
						$data=$this->m_dbConn->insert($insert_into_billdetails);
						$isImportSuccess = true;
						$successcount++;
						$errormsg="Bill for unit: &lt;".$UnitID." &gt;:: period: &lt;".$prevPeriod." &gt;imported successfully.";
						$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");  	
					}
					else if($UpdateFlag)
					{ 
						$linecount++;
						if($linecount == 1)
						{
							$BillDate2=$row[$BillDate];
							$DueDate2=$row[$DueDate];
									//echo "test3";
									/*//$date_range=$this->get_date($prevPeriod);
									echo "test4";
									 $diff1=$this->obj_utility->getDateDiff($date_range,$this->getDBFormatDate($BillDate2));
									 echo "test5";
								   $diff2=$this->obj_utility->getDateDiff($date_range,$this->getDBFormatDate($DueDate2));
								   echo "test5";
								   
								   if($diff1 > 0)
								   {
									   echo "test3";
									$errormsg="BillDate &lt; ".$this->getDBFormatDate($BillDate2)."&gt; "."is &gt; than ".$date_range;
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");   
									   echo "test4";
								   }
								   
								   if($diff2 > 0)
								   {
									   echo "test5";
									$errormsg="DueDate &lt;".$this->getDBFormatDate($DueDate2)."&gt;"."is &lt; than ".$date_range;
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");   
									   echo "test6";
								   }*/
						}
					    $search_society="select society_id from `society` where society_code='".$society_code."'";
						$result=$this->m_dbConn->select($search_society);
						$society_id=$result[0]['society_id'];
								
						if($UnitID<>'')
						{
							if($BillSubTotal=='')
							{  
								$BillSubTotal=0;
							}
							if($BillInterest=='')
							{
								$BillInterest=0;
							}
							if($CurrentBillAmount =='')
							{
								$CurrentBillAmount =0;
							}
							
							if(!empty($UnitID))
							{
								$search_unit="select unit_id from `unit` where unit_no='".$UnitID."' and society_id='".$_SESSION['society_id']."'";
								$result2=$this->m_dbConn->select($search_unit);
								$unit_id=$result2[0]['unit_id'];	
								
								if(empty($unit_id))
								{
									$errormsg .="Unit No &lt;".$UnitID."&gt;Not Found\n";
								}
							}
							else
							{
								$errormsg .= " FCode is empty\n";
							}
							
							if(!empty($society_code))
							{
								$sql01 = "select `society_creation_yearid` from `society` where society_code = '".$society_code."'";
								$sql11 = $this->m_dbConn->select($sql01);
								$currentYear = $sql11[0]['society_creation_yearid'];	
								
								if(empty($currentYear))
								{
									$errormsg .="BCode &lt;".$society_code."&gt;Not Found\n";
								}
							}
							else
							{
								$errormsg .="BCode is empty\n";
							}						
							
							//fetching society start period id 
							$sql = "Select ID,Type from period  where YearID = '" . ($currentYear - 1) . "' and IsYearEnd = 1 ORDER BY ID ASC";
							$result = $this->m_dbConn->select($sql);
							$PeriodID = $result[0]['ID']; 
							$prevPeriod = $result[0]['Type'];
						
							echo '<br>Period '.var_dump($PeriodID);
							echo '<br>dbName : '.$_SESSION['dbname'];
							if(empty($PeriodID))
							{
								$errormsg .="Previous Year Period ID Not Found\n";
							}
							
							if(!empty($errormsg))
							{
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								continue;	
							}
							
							
							//print_r($unit_id);
							$billDetails_openingBalance = $BillSubTotal + $BillInterest;
							$assetQuery = "SELECT * FROM `assetregister` WHERE `LedgerID` = '".$unit_id."' AND `Is_Opening_Balance` = 1";
							$openingBalance = $this->m_dbConn->select($assetQuery);
							//Your code
							$sql02 = "select `society_creation_yearid` from `society` where society_code = '".$society_code."'";
							$sql22 = $this->m_dbConn->select($sql02);
							$society_creation_yearid = $sql22[0]['society_creation_yearid'];
							//echo "yearid: ".$society_creation_yearid;
							//die();
							if($society_creation_yearid <> "")
							{
								//$OpeningBalanceDate = $this->obj_utility->GetDateByOffset($_SESSION['default_year_start_date'] , -1);
								$OpeningBalanceDate = $this->obj_utility->GetDateByOffset($this->obj_utility->getCurrentYearBeginingDate($society_creation_yearid) , -1);
								if($OpeningBalanceDate <> "")
								{
									$Date = $OpeningBalanceDate;		
								}		
							}
							$obj_register = new regiser($this->m_dbConn);
							if($openingBalance)
							{
								if(((float)$CurrentBillAmount) < 0)
								{
									$sqlUpdate = "Update ledger SET opening_balance = '" . abs($CurrentBillAmount) .  "', `opening_type` = '1'  Where id = '" .$unit_id . "'" ;
									$result = $this->m_dbConn->update($sqlUpdate);
						
									$sqlUpdate = "Update assetregister SET Credit = '" . abs($CurrentBillAmount) . "', Debit = 0 where LedgerID = '" .$unit_id . "' and Is_Opening_Balance = 1";
									$result = $this->m_dbConn->update($sqlUpdate);
											
									//$insertAsset = $obj_register->SetAssetRegister(getDBFormatDate($Date), $_POST['id'], 0, 0, TRANSACTION_CREDIT, abs($opening_balance), 1);
								}
								else
								{
									$sqlUpdate = "Update ledger SET opening_balance = '" . abs($CurrentBillAmount) .  "', `opening_type` = '2'  Where id = '" .$unit_id . "'" ;
									$result = $this->m_dbConn->update($sqlUpdate);
						
									$sqlUpdate = "Update assetregister SET Debit = '" . abs($CurrentBillAmount) . "', Credit = 0 where LedgerID = '" . $unit_id . "' and Is_Opening_Balance = 1";
									$result = $this->m_dbConn->update($sqlUpdate);
										
									//echo getDBFormatDate($Date);
									//$insertAsset = $obj_register->SetAssetRegister(getDBFormatDate($Date), $_POST['id'], 0, 0, TRANSACTION_DEBIT, abs($opening_balance), 1);	
										
								}										
							}
							else
							{
								if(((float)$CurrentBillAmount) < 0)
								{
									$sqlUpdate = "Update ledger SET opening_balance = '" . abs($CurrentBillAmount) .  "', `opening_type` = '1'  Where id = '" .$unit_id . "'" ;
									$result = $this->m_dbConn->update($sqlUpdate);
						
									$sqlUpdate = "Update assetregister SET Credit = '" . abs($CurrentBillAmount) . "', Debit = 0 where LedgerID = '" .$unit_id . "' and Is_Opening_Balance = 1";
									$result = $this->m_dbConn->update($sqlUpdate);
											
									$insertAsset = $obj_register->SetAssetRegister(getDBFormatDate($Date), $unit_id, 0, 0, TRANSACTION_CREDIT, abs($CurrentBillAmount), 1);
								}
								else
								{
									$sqlUpdate = "Update ledger SET opening_balance = '" . abs($CurrentBillAmount) .  "', `opening_type` = '2'  Where id = '" .$unit_id . "'" ;
									$result = $this->m_dbConn->update($sqlUpdate);
						
											
									//echo getDBFormatDate($Date);
									$insertAsset = $obj_register->SetAssetRegister(getDBFormatDate($Date), $unit_id, 0, 0, TRANSACTION_DEBIT, abs($CurrentBillAmount), 1);										
								}
							}
							//$currentYear = $_SESSION['society_creation_yearid'];
							//echo "society_creation_yearid: ".$currentYear."<br>";
							//Your code
						
							
							//if($MaintainceAmount <> 0)
							{
								$MaintainceResult = $this->insertBillDetaildAndRegister($unit_id,$PeriodID,$BillNumber,$BillSubTotal,$BillInterest,$MaintainceAmount,$PrincipalArrears2,$InterestArrears2,$BillDate2,$MaintainceAmount,$DueDate2,$society_id,0);	
								if($MaintainceResult == true)
								{
									$isImportSuccess = true;
								}
							}
							
							//if($SupplementryAmount <> 0)
							{
								$Supplementryresult = $this->insertBillDetaildAndRegister($unit_id,$PeriodID,$BillNumber,$BalanceSuppPrincipal_PrevYear,$BalanceSuppInterest_PrevYear,$SupplementryAmount,$PrincipalArrears2,$InterestArrears2,$BillDate2,$SupplementryAmount,$DueDate2,$society_id,1);									
								if($SupplementryAmount == true)
								{
									$isImportSuccess = true;
								}
							}
							$successcount++;
							$errormsg="Bill for unit: &lt;".$UnitID." &gt;:: period: &lt;".$prevPeriod." &gt;imported successfully.";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");  	
						}
						else
						{
							if($UnitID=='')
							{
								$errormsg= "FCode does not exist in data\n";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							}
							else	
							{
								$errormsg= $UnitID ." not Matched\n";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							}
							//$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							//return $result;
					
							//$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");  
						}
					}
				}
			}
			else
			{
				$result = '<p>Column Names Not Found Cant Proceed Further......</p>'.'Go Back';
				//$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
			}				
		}
		if($isImportSuccess)
		{
			$update_import_history="update `import_history` set billdetails_flag=1 where society_id='".$_SESSION['society_id']."'";							
			$res123=$this->m_dbConn->update($update_import_history);
		}
		else
		{
			//$errormsg="bill details not imported";
			//$this->obj_utility->logGenerator($errorfile,'Error',$errormsg,"E");	
		}
		$totalCount = $totalCount - 1;
		//var_dump($ledgerblank);
		
		 $errormsg = "<br><br><b>Number of Rows : ".$totalCount."</b>";
	 
		 $errormsg .= "<br><b>Number of Rows Imported : ".$successcount."</b>";
		 $errormsg .= "<br><b>Number of Rows Not Imported : ".($totalCount - $successcount)."</b>";
		 $this->obj_utility->logGenerator($errorfile,'',$errormsg);

		$errormsg="<br><br>[End of Member Opening Balance]";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg);			
		
		return $isImportSuccess;
						
	}
	
	public function insertBillDetaildAndRegister($unit_id,$PeriodID,$BillNumber,$Principal_PrevYear,$Interest_PrevYear,$CurrentBillAmount,$PrincipalArrears2,$InterestArrears2,$BillDate2,$TotalBillPayable,$DueDate2,$society_id,$BillType)
	{
		$datam09 = '';
		$sqlbillregister = "Select  * from `billregister`  where SocietyID = '" . $society_id . "' and PeriodID = '" . $PeriodID . "' and BillType = '".$BillType."' ";
		$resultbillregister = $this->m_dbConn->select($sqlbillregister);
		
		$sqlbilldetails = "Select  * from `billdetails`  where UnitID = '" . $unit_id . "' and PeriodID = '" . $PeriodID . "' and BillType = '".$BillType."'";
		$resultbilldetails = $this->m_dbConn->select($sqlbilldetails);
		
		if(sizeof($resultbillregister) == 0)
		{
			$insert_into_billregister="insert into `billregister`(SocietyID,PeriodID,CreatedBy,BillDate,DueDate,Notes,LatestChangeID,BillType) values(".$_SESSION['society_id'].",'".$PeriodID."','" . $this->m_dbConn->escapeString($_SESSION['login_id']). "','".$this->getDBFormatDate($BillDate2)."','".$this->getDBFormatDate($DueDate2)."','Import Data','0','".$BillType."') ";
			//echo "Billregister &lt;".$insert_into_billregister."&gt;";
			$datam09 = $this->m_dbConn->insert($insert_into_billregister);
			//print_r ($datam09);
			
		}
		else
		{
			$datam09 = $resultbillregister[0]['ID'];
		}
		if(sizeof($resultbilldetails) == 0)
		{
			$insert_into_billdetails="insert into `billdetails`(UnitID,PeriodID,BillRegisterID,BillNumber,BillSubTotal,BillInterest,CurrentBillAmount,PrincipalArrears,InterestArrears,TotalBillPayable,BillType) 
			values('".$unit_id."','".$PeriodID."','".$datam09."','$BillNumber','$Principal_PrevYear','$Interest_PrevYear','$CurrentBillAmount','$PrincipalArrears2','$InterestArrears2','$TotalBillPayable','".$BillType."') ";
			
			//echo "BillDetail <".$insert_into_billdetails.">";
			
			$data=$this->m_dbConn->insert($insert_into_billdetails);
			$isImportSuccess = true;
			//print_r ($data);
		}
		else
		{ 
			$bill_Detail= implode(' | ',$resultbilldetails[0]);
			$changeLog="insert into `change_log` (ChangedLogDec,ChangedBy,ChangedTable)  values('<br />Original Record :  ".$bill_Detail."','".$_SESSION['login_id']."','billdetails')";
			$changeLogDetail=$this->m_dbConn->insert($changeLog);
		
			$updatebilldetails = "Update `billdetails` set ";
			$updatebilldetails .= "BillSubTotal = '".$Principal_PrevYear."', ";
			$updatebilldetails .= "BillInterest = '".$Interest_PrevYear."', ";
			$updatebilldetails .= "CurrentBillAmount = '".$CurrentBillAmount."', ";
			$updatebilldetails .= "PrincipalArrears = '".$PrincipalArrears2."', ";
			$updatebilldetails .= "InterestArrears = '".$InterestArrears2."', ";
			$updatebilldetails .= "LatestChangeID = '".$changeLogDetail."', ";
			$updatebilldetails .= "TotalBillPayable = '".$TotalBillPayable."' ";
			$updatebilldetails .= "where  `UnitID` = '".$unit_id."' and `PeriodID` = '".$PeriodID."' and BillType = '".$BillType."'";
			$this->m_dbConn->update($updatebilldetails);	
			  
			$isImportSuccess = true;
		}
		
		return true;
	}
		
	
	
	function getDateTime()
	{
		$dateTime = new DateTime();
		$dateTimeNow = $dateTime->format('Y-m-d H:i:s');
		return $dateTimeNow;
	}
	
	function getDBFormatDate($ddmmyyyy)
	{
		if($ddmmyyyy <> '')
		{
			$ddmmyyyy = str_replace('/', '-', $ddmmyyyy);
			return date('Y-m-d', strtotime($ddmmyyyy));
		}
		else
		{
			return '';
		}
	}
	
	
	public function get_date($id)
	{
		
	$sql="select BeginingDate from `period` where id=".$id." ";
	$data=$this->m_dbConn->select($sql);
	
	return $data[0]['BeginingDate'];
		
	}
	
	function getPrevPeriod($curPeriod)
	{
		$sql = "Select PrevPeriodID from period where ID = '" . $curPeriod . "'";
		$result = $this->m_dbConn->select($sql);
		
		return $result[0]['PrevPeriodID'];
	}
	
	
}

?>
<?php
include_once("include/dbop.class.php");
include_once("utility.class.php");
include_once("dbconst.class.php");
include_once("register.class.php");
include_once("changelog.class.php");//Pending - Verify
// include_once("genbill.class.php");
include_once("FixedDeposit.class.php");//Pending - Verify
include_once("include/fetch_data.php");

// error_reporting(1);
class fd_import 
{
	public $m_dbConn;
	public $obj_utility;
	public $errorfile_name;
	public $errorLog;
	public $actionPage = '../import_fixed_deposit.php';
	public $bvalidate;
	public $changeLog;
	public $obj_fetch;
	//public $obj_genbill;
	//private $InvoiceNumberArray = array();
	private $obj_FixedDeposit;
	private $FDCatArray;

	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->dbConnRoot = $dbConnRoot;
		$this->obj_utility = new utility($this->m_dbConn);
		$this->obj_FixedDeposit = new FixedDeposit($this->m_dbConn, $this->dbConnRoot);
		$this->changeLog = new changelog($this->m_dbConn);
		$this->register = new regiser($this->m_dbConn);


		$this->obj_fetch = new FetchData($this->m_dbConn);

		$a = $this->obj_fetch->GetSocietyDetails($_SESSION['society_id']);
		//Array of FD categories
		// $this->$FDCatArray = $this->obj_FixedDeposit->FetchFdCategories();
		//Array of Ledgers
		//implement this function to give you all existing ledgers
		//$this->$LedgerArray = $this->obj_utility->GetLedgers();		
	}
	

	//Move this to utility so others can use it
	public function IsLedgerExist($LedgerName)
	{
		$return_value = false;
		//check if ledger exist in $LedgerArray
		if(m_bAreLedgersLoaded == false)
		{		
			//$this->$LedgerArray = $this->obj_utility->GetLedgers();
		}
		$select = "Select id, ledger_name From `ledger` Where `ledger_name`= '".$LedgerName."' ";
		$ledgerid =$this->m_dbConn->select($select);

		foreach ($ledgerid as $key) {
			$id = $key["id"];
		}

		if ($id > 0){
			$return_value = true;
		}
		return $return_value;
	}

	public function AddLedgerToArray($LedgerName)
	{
		//check if ledger exist in $LedgerArray, if not add to array for later checks
	}

	public function GetBankID($BankName)
	{
		$select = "Select BankID From `bank_master` Where `BankName`= '".$BankName."' ";
		$bankid =$this->m_dbConn->select($select);
		foreach ($bankid as $key)
		{
			$id = $key["BankID"];
		}
		// print_r($bankid);
		// die();
		return $id;
	}
	
	private function GetFDCategory($Category)
	{
		//Pending : Get categoryid from $this->$FDCatArray
		$CategoryID = 0;
		$sql = "SELECT  category_id, is_fd_category FROM `account_category` where `category_name`= '".$Category."' ";
		//  echo $sql;
		$res = $this->m_dbConn->select($sql);
		// print_r($res);
		// die();	
		
		for($i= 0 ;$i < sizeof($res); $i++)
		{
			$CategoryID = $res[$i]['category_id'];
			$is_fd_category = $res[$i]['is_fd_category'];
			if($is_fd_category < 1)
			{
				$CategoryID = 0;
			}		
		}
		// `

		return $CategoryID;
		// print_r($)
	}

	//Give Vaidate button and Pass validate flag. 
	public function UploadData($fileName,$fileData, $bvalidate)
	{
		$Foldername = $this->obj_fetch->objSocietyDetails->sSocietyCode;

		if (!file_exists('../logs/import_log/'.$Foldername)) 
		{
			mkdir('../logs/import_log/'.$Foldername, 0777, true);
		}

		$a = 'import_fd_errorlog_'.date("d.m.Y").'_'.rand().'.html';
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



		$errormsg="[Importing Fixed Deposit Data]";
		$isImportSuccess = true;
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		// $bvalidate = true;

		$array = array();
		$Success = 0;
		$rowCount = 0;
		// print_r($_SESSION);
		// die();
		// echo "test1";
		foreach($fileData as $row)
		{
			$isImportSuccess = true;

			if($row[0] || $row[1] <> '')
			{
				$rowCount++;
				if($rowCount == 1)//Header
				{
					$FDBankName = array_search(FDBankName, $row, true);
					$Category = array_search(Category,$row,true);
					$FDName=array_search(FDName,$row,true);
					$FDRNo = array_search(FDRNo, $row, true);
					$DateofDeposite = array_search(DateofDeposite, $row, true);
					$PrincipleAmount = array_search(PrincipleAmount, $row, true);
					$InterestRate = array_search(InterestRate, $row, true);
					$InterestPayoutFrequency = array_search(InterestPayoutFrequency, $row, true);
					$MaturityAmount = array_search(MaturityAmount, $row, true);
					$DateOfMaturity = array_search(DateOfMaturity, $row, true);
					$FDInterestType = array_search(FDInterestType, $row, true);
					$FDNote = array_search(FDNote, $row, true);
					// print_r($UnitNo);
					// die();

					if(!isset($FDBankName) || !isset($Category) || !isset($FDName) || !isset($FDRNo) || !isset($DateofDeposite) || !isset($PrincipleAmount) || !isset($InterestRate) || !isset($InterestPayoutFrequency) || !isset($DateOfMaturity) || !isset($FDInterestType) )
					{
						$result = '<p>Required Column Names Not Found. Cant Proceed Further......</p>';
						$errormsg=" Column names does not match";
						$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
						return $result;
					}
					
				}
				else
				{

					if($rowCount==2)
					{
						continue;	
					}
					

						$errormsg = '';

						if($row[$FDBankName] == '')
						{
							$errormsg=" FDBankName is mandatory field and it cannot be blank";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;
						}else
						{
							$BankName = $row[$FDBankName];
							// print_r($BankName);
							// die();
							$BankID = $this->GetBankID($BankName);
							//Pending : Error handling if bank not found
							if($BankID == '')
							{
								$errormsg=" Bank Name &lt;".$BankName."&gt; does not exists";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								$isImportSuccess = false;
							}
							// print_r($BankID);
							// die();
						}

						if($row[$Category] == '')
						{
							$errormsg=" Category is mandatory field and it cannot be blank";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;

						}else
						{
							$category = $row[$Category];
							// die();
							
							$CategoryID = $this->GetFDCategory($category);
							// print_r($CategoryID);
							// die();
							if($CategoryID == 0)
							{
								$errormsg=" Category &lt;".$category."&gt; is missing or not defined as FD category";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								$isImportSuccess = false;
							}
							
							//Pending : Error handling if FD Category not found. Log error FD Category does not exist												   		
						}

						if($row[$FDName] == '')
						{

							$errormsg="FDName is mandatory field and it cannot be blank";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;

						}else
						{
							$Fdname = $row[$FDName];
						}
						
						//pending: add optional field for certificate number 
						if($row[$FDRNo] == '')
						{
							$errormsg="FDRNo is mandatory field and it cannot be blank";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;

						}else
						{
							$Fdrno = $row[$FDRNo];
						}
						
						if($row[$DateofDeposite] == '')
						{
							$errormsg=" DateofDeposite date does not exist";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;

						}else
						{
							$date = explode('-', $row[$DateofDeposite]);
							if(strlen($date[0]) < 3 && strlen($date[2]) < 3)
							{
								$errormsg = "The Date format should be 'dd-mm-yyyy' ";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								$isImportSuccess = false;

							}else
							{
								$dateofdeposite = getDBFormatDate($row[$DateofDeposite]);
							}

							// $dateofdeposite = $row[$DateofDeposite];
						}
						
						if($row[$PrincipleAmount] == '')
						{
							$errormsg="Principle Amount does not exist";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;

						}else
						{
							$principleamount = $row[$PrincipleAmount];
						}
						
						if($row[$InterestRate] == '')
						{
							$errormsg="Interest Rate does not exist";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;

						}else
						{
							$interestrate = $row[$InterestRate];
						}

						if($row[$InterestPayoutFrequency] == '')
						{
							$errormsg="Interest Payout Frequency does not exist";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;

						}else
						{
							$interestpayoutfrequency = $row[$InterestPayoutFrequency];
						}
						// print_r($row[$InterestPayoutFrequency]);
							// die();

						if($row[$MaturityAmount] == '')
						{
							$errormsg="Maturity Amount does not exist";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;

						}else
						{
							$maturityamount = $row[$MaturityAmount];
						}

						if($row[$DateOfMaturity] == '')
						{
							$errormsg=" Date Of Maturity date does not exist";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;

						}else
						{
							$dateofmaturity = getDBFormatDate($row[$DateOfMaturity]);
							// $dateofmaturity = $row[$DateOfMaturity]
						}

						if($row[$FDInterestType] == '')
						{
							$errormsg=" FD Interest Type date does not exist";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;

						}else
						{
							$fdinteresttype = $row[$FDInterestType];
						}

						if($row[$FDNote] == '')
						{
							$errormsg="FDNote does not exist";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							$isImportSuccess = false;

						}else
						{
							$Fdnote = $row[$FDNote];
						}


						if($isImportSuccess == false)
						{
							$errormsg = "Data not Inserted";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							
						}else
						{
							$OpeningBalanceDate = $dateofdeposite;
							
							//Check if ledger already exist

							if($OpeningBalanceDate < $_SESSION['default_year_start_date'])
							{
								$date=date_create($_SESSION['default_year_start_date']);
								date_sub($date,date_interval_create_from_date_string("1 day"));
								$LedgerOpeningBalanceDate = date_format($date,"Y-m-d");
								$ledgeramount = $principleamount;
							}else
							{
								$date=date_create($OpeningBalanceDate);
								date_sub($date,date_interval_create_from_date_string("1 day"));
								$LedgerOpeningBalanceDate = date_format($date,"Y-m-d");
								// $LedgerOpeningBalanceDate = $dateofdeposite;
								$ledgeramount = 0;
							} 

							if($this->IsLedgerExist($Fdname))
							{
								$errormsg="<b>FD Name: ".$Fdname." already exist </b>";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								$errormsg = "FDBankName :" .$BankName.",<br> Category :".$category. ",<br> FDRNo :".$Fdrno.",<br> DateofDeposite:".$dateofdeposite.",<br> PrincipleAmount:".$principleamount.",<br> InterestRate :".$interestrate.",<br> Interest Payout Frequency :".$interestpayoutfrequency.",<br> MaturityAmount :".$maturityamount.",<br> DateOfMaturity :".$dateofmaturity.",<br> FD Interest Type :".$fdinteresttype.",<br> FDNote :".$Fdnote."<br> <b>Data not Inserted</b> ";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");

								// $isImportSuccess = false;
								//if ledger exist
								//Log error
							}
							else
							{	
								// var_dump($bvalidate == false);
								if($bvalidate == false)
								{
									// echo "<br>test1.2 <br>";
									$NewFDLedgerID = $this->obj_FixedDeposit->Create_New_Ledger($Fdname, $CategoryID, $LedgerOpeningBalanceDate, $ledgeramount );
								}

								$date1 = date_create($dateofdeposite);
								$date2 = date_create($dateofmaturity);
								$diff = date_diff($date1, $date2);
								$FD_Period = $diff->format("%R%a days");

								if($bvalidate == false)
								{
									// echo "test1.3";
									$Old_FDR_ID = 0;
									$FD_Master_ID = $this->obj_FixedDeposit->GetOrUpdateFD_Master($Old_FDR_ID, $NewFDLedgerID, $Fdrno, $principleamount, $dateofdeposite, $maturityamount, $dateofmaturity, $interestrate, $fdinteresttype, $interestpayoutfrequency,  $FD_Period, $BankID , $Fdnote);
									$register = $this->register->SetRegister($LedgerOpeningBalanceDate, $NewFDLedgerID, 0, 0, TRANSACTION_DEBIT, $ledgeramount, 1);
								}


								if($bvalidate == false)
								{
									$errormsg = "FDBankName :" .$BankName.",<br> Category :".$category. ",<br> FDName :".$Fdname.",<br> FDRNo :".$Fdrno.",<br> DateofDeposite:".$dateofdeposite.",<br> PrincipleAmount:".$principleamount.",<br> InterestRate :".$interestrate.",<br> MaturityAmount :".$maturityamount.",<br> DateOfMaturity :".$dateofmaturity.",<br> FDNote :".$Fdnote."<br> <b>Added successfully</b>";
									$dec = "Added New FD";
									$changedby = $_SESSION['login_id'];
									$changedtable = "fd_master";
									$changedkey = $FD_Master_ID;
									$this->changeLog->setLog($dec, $changedby, $changedtable, $changedkey);

								}else
								{
									$errormsg = "FDBankName :" .$BankName.",<br> Category :".$category. ",<br> FDName :".$Fdname.",<br> FDRNo :".$Fdrno.",<br> DateofDeposite:".$dateofdeposite.",<br> PrincipleAmount:".$principleamount.",<br> InterestRate :".$interestrate.",<br> MaturityAmount :".$maturityamount.",<br> DateOfMaturity :".$dateofmaturity.",<br> FDNote :".$Fdnote." ";
								}


								if($bValidate == false)
								{
									if($FD_Master_ID > 0)
									{
										$errormsg = "FDR created with ID :" .$FD_Master_ID. "  " . $errormsg;
										//Update Change log
									}
									else
									{
										$errormsg = "Error creating FDR :" .$FD_Master_ID. "  " . $errormsg;
										
									}
									// $this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
								}
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");


							}
							
						}	
					//} 
				}
			}
		}
	}
}

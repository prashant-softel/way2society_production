

<?php
include_once("utility.class.php");
//include_once("PaymentDetails.class.php");
include_once("ChequeDetails.class.php");
//echo "import";
include_once("include/fetch_data.php");

$dbConn = new dbop();
class receiptImport
{
	public $errorLog;
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_utility;
	//public $obj_PaymentDetails;
	public $obj_ChequeDetails;
	public $obj_fetch;
	public $actionPage = "../import_payments_receipts.php";
	
	function __construct($dbConnRoot, $dbConn)
	{
		
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->obj_utility= new utility($this->m_dbConn);
		$this->obj_ChequeDetails=new ChequeDetails($this->m_dbConn);

		$this->obj_fetch = new FetchData($this->m_dbConn);
		$a = $this->obj_fetch->GetSocietyDetails($_SESSION['society_id']);
		
	}
	public function ImportData($SocietyID)
	{
		date_default_timezone_set('Asia/Kolkata');
		$Foldername = $this->obj_fetch->objSocietyDetails->sSocietyCode;

		if (!file_exists('../logs/import_log/'.$Foldername)) 
		{
			mkdir('../logs/import_log/'.$Foldername, 0777, true);
		}

		$errofile_name = '../logs/import_log/'.$Foldername.'/import_errorlog_'.date("d.m.Y").'_'.rand().'.html';	
		$this->errorLog=$errofile_name;
		$errorfile=fopen($errofile_name, "a");
		$tmp_array=array();	
		if(isset($_POST["Import"]))
		{
			$valid_files=array('Receipt');
			$limit=count($_FILES['upload_files']['name']);
			$success=0;
			 
			 for($m=0;$m<$limit;$m++)
			 {
				 $filename=$_FILES['upload_files']['name'][$m];
				$tmp_filename=$_FILES['upload_files']['tmp_name'][$m];
				
				for($i=0;$i<sizeof($valid_files);$i++)
				{
					$pos=strpos($filename,$valid_files[$i]);
					/*if($pos === FALSE)
					{
						$message = $filename." is not a valid file";
						return $message;
					}
					else*/
					{
						$ext = pathinfo($filename, PATHINFO_EXTENSION);
						if($ext <> '' && $ext <> 'txt' && $ext <> 'csv')
						{	
							return $filename.'  Invalid file format selected. Expected *.txt or *.csv file format';
						}
						else
						{
							$success++;
							$tmp_array[$i]=$_FILES['upload_files']['tmp_name'][$m];
						}
					}
				}
				
			 } 	
			 		$logfile="";
				 
				 	$result=$this->startprocess($tmp_array[0],0,$errorfile,$errofile_name);
				 	if($result <> '')
				 	{
						$this->actionPage="../import_payments_receipts.php";
					 	return $result;
					}
		
			}

	}
	
	
	function startprocess($filename,$pos,$errorfile,$errofile_name)
	{
		
		if($pos==0)
			 {
				 $import_result=$this->UploadData($filename,$errorfile,$errofile_name);
			 	 return $import_result;
			 }
			 
			 else
			 {
				 
				return 'All Data Imported Successfully...'; 
				 
			}
	}
	
	public function UploadData($fileName,$errorfile,$errofile_name)
	{
		$ChequeLeafBook=array();
		$file = fopen($fileName,"r");
		$data=0;
		$DepositeID=0;
		$errormsg ="[Importing Receipts Details]"." Created At [".date('d-m-Y h:i:s')."]";
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		$getYearSql = "SELECT year.BeginingDate AS begin, year.EndingDate AS end FROM year where YearID = " . $_SESSION['default_year']; 
		$getYearRange = $this->m_dbConn->select($getYearSql);

		$beginDate = $getYearRange[0]['begin'];
		$endDate = $getYearRange[0]['end'];

		$banksSQL = "SELECT ledger.id AS BankID, ledger.ledger_name as BankName FROM ledger JOIN bank_master ON ledger.id = bank_master.BankID";
		$banks = $this->m_dbConn->select($banksSQL);
		$Success='';
		$Failed='';
		$bankID= array();
		$bankNames = array();

		for ($i = 0; $i < count($banks); $i++)
		{
			$bankID[$i] = $banks[$i]['BankID'];
			$bankNames[$i] = $banks[$i]['BankName'];
		}
		$cnt=0;
		while (($row = fgetcsv($file)) !== FALSE)
		{
			$row =array_map('trim', $row);
			if($row <> '')
			{
					
					$rowCount++;
					
					if($rowCount == 1 || $rowCount == 2)
					{
						if($rowCount == 2)
						{
							//Because Row 2 is not contains any receipt details 
							continue;
						}
						$VNo=array_search(VoucherNo,$row,true);
						$VDateIndex=array_search(RctDate,$row,true);
						$To=array_search(To_,$row,true);
						$BankName=array_search(BankName,$row,true);
						$BranchName=array_search(BranchName,$row,true);	
						$UnitNo=array_search(UnitNo,$row,true);	
						//$AccountName=array_search(AccountName,$row,true);
						$ChequeNo=array_search(ChequeNo,$row,true);	
						$ChequeDateIndex=array_search(ChequeDate,$row,true);
						$Amount=array_search(Amount,$row,true);	
						$BillTypeIndex=array_search(BillType,$row,true);			
						$Remark=array_search(Remark,$row,true);	
						
						if(!isset($VDateIndex) || !isset($To)|| !isset($ChequeNo) || !isset($ChequeDateIndex) || !isset($Remark) || !isset($Amount)|| !isset($VNo) || !isset($BillTypeIndex))
						{
							$result = 'Column Names Not Found Cant Proceed Further......'.'Go Back';
							$errormsg=" Column names in file BankBook not match";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg);
							return $result;
							exit(0);
						}
					}
				   else
				   {	
				   		try 
						{
							$cnt++;
					   		$BankID='';
					   		$vno='';
							$voucherDate='';
							$to='';
							$bankIndex='';
							$PayerBank='';
							$PayerChequeBranch='';
							$unitNo='';
							$chequeDate='';
							//$accountName='';
							$chequeNo='';
							$comments='';
							$bankIndex='';
							$ChequeDate='';
							$to='';
							$errormsgfile='';
							$BillType = 0;
							$errormsg='';
							$amount='';
							
							$vNo=$row[$VNo];
							
							$VoucherDate=trim($row[$VDateIndex]);
							$to = trim($row[$To]);
							if($to!='')
							{
								$bankIndex = array_search($to, $bankNames, true);
							}
							$PayerBank=trim($row[$BankName]);
							$PayerChequeBranch=trim($row[$BranchName]);
							$unitNo = trim($row[$UnitNo]);
							$chequeNo=trim($row[$ChequeNo]);	
							$ChequeDate=trim($row[$ChequeDateIndex]);	
							$comments=trim($row[$Remark]);	
							$amount=trim($row[$Amount]);
							
							if($bankIndex === false)
							{
								$errormsg.="<br>Bank &lt;" . $to . "&gt; doesn't exists. Please mention correct bank name.";
								$BankID = '';
							}
							else
							{
								$BankID = $bankID[$bankIndex];
							}
							
							$BillType = trim($row[$BillTypeIndex]);
							
							$successData='';
							
							//echo '<br>Comments '.$comments;
							
							if($vNo == "")
							{
								
								$IsSameCntApply = $this->obj_utility->IsSameCounterApply();
								
								//** Here we check the  whether we have to use same counter or different for all banks
								if($IsSameCntApply == 1)
								{
									$Counter = $this->obj_utility->GetCounter(VOUCHER_RECEIPT, 0,false);	
								}
								else
								{
									$Counter = $this->obj_utility->GetCounter(VOUCHER_RECEIPT, $BankID,false);
									//var_dump($Counter);
								}
								$vNo = $Counter[0]['CurrentCounter']; 

							}
							$systemVoucherNo = $vNo;
							//echo "ashwini";
							//echo $BillType;
							//die();

							if($BillType == '0' ||  $BillType == '1' || $BillType == '2')
							{
								//$errormsg.="<br>Bill Type &lt;" . $BillType . "&gt; doesn't exists";
								//$errormsg.="<br>Bill No accepted...";
								
							}
							else
							{
								$errormsg.="<br><b>Bill Type not accepted &lt;" . $BillType . "&gt;. please mention correct Bill type.</b>";
							}

							if($chequeNo=='')
							{
								$errormsg.="<br><b>Please provide Cheque No.</b>";
							}

							if($ChequeDate == '' || $ChequeDate== '0' || $ChequeDate=='-' || $ChequeDate=='--')
							{
								$errormsg.="<br><b>Please provide Cheque Date.</b>";
							}
							else
							{
								if($this->obj_utility->dateFormat($ChequeDate)==true)// yyyy-mm-dd
								{
									if($this->check_in_range($beginDate, $endDate, $ChequeDate));
									{
										
										$chequeDate=$ChequeDate;
									}
								}
								elseif($this->obj_utility->validateDate($ChequeDate)==true)//dd-mm-yyyy
								{
									$ChequeDate=getDBFormatDate($ChequeDate);
									if($ChequeDate!='00-00-0000')
									{
										if($this->check_in_range($beginDate, $endDate, $ChequeDate)==true)
										{
											$chequeDate=$ChequeDate;
										}
										else
										{
											$errormsg.="<br>Cheque Date &lt;".$ChequeDate."&gt;Not In Range. It Should Be Between ".$beginDate." and ".$endDate;	
										}
									}
								}
								else
								{
									 $errormsg .="<br>Invalid Cheque Date Format &lt;".$ChequeDate."&gt;Date Should be in either DD-MM-YYYY or YYYY-MM-DD Format ";
								}
							}
							if($VoucherDate=='' || $VoucherDate=='0' || $VoucherDate=='-' || $VoucherDate=='--')
							{
								$errormsg.="<br>Receipt Date Not Provided";
							}
							else
							{
								if($this->obj_utility->dateFormat($VoucherDate))// yyyy-mm-dd
								{
									if($this->check_in_range($beginDate, $endDate, $VoucherDate))
									{
										$voucherDate=$VoucherDate;	
									}
									else
									{
										$errormsg.="<br>Receipt Date &lt;".$VoucherDate."&gt;Not In Range. It Should Be Between ".$beginDate." and ".$endDate;		
									}
								}
								
								elseif($this->obj_utility->validateDate($VoucherDate))//dd-mm-yyyy
								{
									$VoucherDate=getDBFormatDate($VoucherDate);
									if($VoucherDate!='00-00-0000')
									{
										if($this->check_in_range($beginDate, $endDate, $VoucherDate))
										{
											$voucherDate=$VoucherDate;
										}
										else
										{
											$errormsg.="<br>Receipt Date &lt;".$VoucherDate."&gt;Not In Range. It Should Be Between ".$beginDate." and ".$endDate;		
										}
									}
										
								}
								else
								{
									 $errormsg .="<br>Invalid Receipt Date Format &lt;".$voucherDate."&gt;Date Should be in either DD-MM-YYYY or YYYY-MM-DD Format ";
								}
							}
								
								
								
							if($vNo == '' || !is_numeric($vNo))
							{
								$errormsg.="<br>Invalid Voucher Number Format OR Voucher Number Not Provided.";
							}
						  /*	else
							{
								if()
								{
									$errormsg.="<br>Invalid Voucher Number Format &lt;".$vNo."&gt;";
									$vNo='';	
								}
							}*/

							if($unitNo == '')
							{
								
									$errormsg.="<br>Please mention <b> Unit Number</b>.";
							}
							else
							{
								$unitNo = trim($unitNo);
								
								$PaidBy=$this->getLedgerID($unitNo);
								if($PaidBy=='')
								{
									//$unitNo='';
									$errormsg.="<br>please mention correct Unit Number Not Found &lt;".$unitNo."&gt;";
								}	
							}

							if($amount == '' || (!is_numeric($amount)))
							{
								$errormsg.="<br>Invalid Amount Format &lt;".$amount."&gt;";
							}
							
								/*if(!is_numeric($amount))
								{
									//$amount='';
									$errormsg.="<br>Ashwini Invalid Amount Format &lt;".$amount."&gt;";
									
								}*/
							
							/*else
							{
								$errormsg.="<br>ashiwniroakde";
							}*/
							
							/*else
							{
								
							}*/
							$successData.="Unit No &lt;".$row[$UnitNo]."&gt; Voucher Number &lt;".$row[$VNo]."&gt; Receipt Date : &lt;".$row[$VDateIndex]."&gt; To_ &lt;".$row[$To]."&gt; Cheque No &lt;".$row[$ChequeNo]."&gt; Cheque Date &lt;".$row[$ChequeDateIndex]."&gt; Payer Bank &lt;".$row[$BankName]."&gt; Payer Branch Name &lt;".$row[$BranchName]."&gt; Amount &lt;".$row[$Amount]."&gt;  Bill Type &lt;".$row[$BillTypeIndex]."&gt; Remark &lt;".$row[$Remark]."&gt;";//Account Name &lt;".$row[$AccountName]."&gt;
								
							if($unitNo!='' && ($vNo!='' && is_numeric($vNo)) && $voucherDate!='' && $chequeNo!='' && $chequeDate!='' && $BankID!='' && ($amount!='' &&  is_numeric($amount)) && $PaidBy!='' && (($BillType == 0 ||  $BillType == 1 || $BillType == 2 )))
							{
								if($cnt == 1) // First Entry, So create Batch ID
								{
									$batch_name = 'receipt_'.date("Y-m-d H:i:s");
									$created_by = $_SESSION['login_id'];
									$insertBatchQuery = "INSERT INTO `import_batch`(`BatchName`, `VoucherType`, `Imported_By`, `importfilename`) VALUES ('$batch_name','".VOUCHER_RECEIPT."','$created_by','$errofile_name')";

									$BatchID = $this->m_dbConn->insert($insertBatchQuery);
								}
								
								if(empty($BatchID)){

									$errormsgfile .="<br>Batch ID not found. Please contact to tech support team for further detail";
									break;
								}
								
								if(array_key_exists($to,$ChequeLeafBook) && strtolower($to) <> 'cash')
								{
									$data=$ChequeLeafBook[$to];
								}
								else if(strtolower($to) <> 'cash')
								{
									//date_default_timezone_set('Asia/Kolkata');
									$desc = 'DATA IMPORTED'.date('Y-m-d H:i:sa');
									$queryII = "select `society_creation_yearid` FROM `society` where `society_id` = '".$_SESSION['society_id']."'";
								   	$resII = $this->m_dbConn->select($queryII);
								  	
								  	$insert_query1="insert into depositgroup (`bankid`,`createby`,`depositedby`,`status`,`desc`,`DepositSlipCreatedYearID`) values ('".$BankID."','".$_SESSION['login_id']."','Import Data','0','".$desc."','".$resII[0]['society_creation_yearid']."')";
								   	$data = $this->m_dbConn->insert($insert_query1);
									$ChequeLeafBook[$to]=$data;
								}
							   if(is_numeric($chequeNo)== FALSE && strtolower($chequeNo)=='cash'  &&  strtolower($to) == 'cash')
							   {
									$DepositeID= -3;  
							   }
							   else if(is_numeric($chequeNo)== FALSE && strtolower($chequeNo)=='neft')
							   {
									$DepositeID= -2;     
							   }
							    else if(is_numeric($chequeNo)== TRUE && strlen($chequeNo) > 6)
							   {
								   //neft or online transaction
									$DepositeID= -2;     
							   }
							    else if(is_numeric($chequeNo)== FALSE && strtolower($chequeNo) <> 'neft' && strtolower($chequeNo) <> 'cash')
							   {
								   //neft or online transaction
									$DepositeID= -2;     
							   }
							   else
							   {
								  $DepositeID=$data; 
							   }
							 echo '<br>Paid BY In receipt_import '.$PaidBy;
							 echo '<br>Voucher Number'.$vNo;
							  $this->obj_ChequeDetails->AddNewValues($voucherDate,$chequeDate,$chequeNo,$vNo,$systemVoucherNo,1,$amount, $PaidBy, $BankID, $PayerBank, $PayerChequeBranch, $DepositeID, $comments,$BillType,0,0,0,0,0,true,'',0,0,$BatchID); 
							 // echo  $errormsg="To_ : ".$to . " : PaidBy : ".$PaidBy.":  UNIt No:".$unitNo;
							 $Success++;
							  $errormsgfile.=$successData;
							  $errormsgfile.="<br>Row Inserted";
							  $this->obj_utility->logGenerator($errorfile,$cnt,$errormsgfile,'I');
	 
							}
							else
							{
								$Failed++;
								$errormsgfile .=$successData;
								$errormsgfile .=$errormsg;
								$errormsgfile .="<br>Row Not Inserted";
								$this->obj_utility->logGenerator($errorfile,$cnt,$errormsgfile,'E');
							}
					   }
					   	catch ( Exception $e )
						{
						}
				 }
			
		}
	
	}
	$errormsg="[End of  ReceiptDetails]";
	$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
	$Total=$Success+$Failed;
	if($Success)
	{
		if($Success=='')
		{
			$Success=0;	
		}
		$result="<br><br>Number Of Row Successfully Imported : ".$Success." Out Of ".$Total;
		$this->obj_utility->logGenerator($errorfile,'',$result,'I');
	}
	if($Failed)
	{
		if($Failed=='')
		{
			$Failed=0;
		}
		$result="<br>Number Of Rows Not Imported : ".$Failed." Out Of ".$Total;
		$this->obj_utility->logGenerator($errorfile,'',$result,'I');
	}
	
	
	return  "file imported successfully..";
	
	}//function
	
	
	function getDateTime()
	{
		$dateTime = new DateTime();
		$dateTimeNow = $dateTime->format('Y-m-d H:i:s');
		return $dateTimeNow;
	}
	
	public function getLedgerID($LedgerName)
	{
		
		$sql="SELECT id FROM ledger WHERE ledger_name = '".$LedgerName."'";
		$result = $this->m_dbConn->select($sql);
		
		if ($result == '')
		{
			return 0;
		}
		else
		{
			return $result[0]['id'];	
		}	
	}
	
	function getDBFormatDate($ddmmyyyy)
	{
		//echo $ddmmyyyy;
		if($ddmmyyyy <> '' && $ddmmyyyy <> '00-00-0000')
		{
			$ddmmyyyy = str_replace('/', '-', $ddmmyyyy);
			return date('d-m-Y', strtotime($ddmmyyyy));
		}
		else
		{
			return '00-00-0000';
		}
	}

	//Check date is in between two dates
	function check_in_range($start_date, $end_date, $date_from_user)
	{
		// Convert to timestamp
		
		$start_ts = strtotime($start_date);
		$end_ts = strtotime($end_date);
		$user_ts = strtotime($date_from_user);

		// Check that user date is between start & end
		return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
	}
}

?>
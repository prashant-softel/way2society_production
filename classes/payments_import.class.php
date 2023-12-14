<?php
include_once("utility.class.php");
include_once("PaymentDetails.class.php");
include_once("ChequeDetails.class.php");
include_once("latestcount.class.php");
include_once("changelog.class.php");
//echo "import";
include_once("include/fetch_data.php");

$dbConn = new dbop();

class paymentImport 
{
	
	public $m_dbConn;
	public $errorLog;
	public $m_dbConnRoot;
	public $obj_utility;
	public $obj_PaymentDetails;
	public $obj_ChequeDetails;
	public $actionPage = "../import_payments_receipts.php";
	public $errofile_name;
	public $obj_latestcount;
	public $changeLog;
	public $obj_fetch;
	
	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->obj_utility = new utility($this->m_dbConn);
		$this->obj_PaymentDetails = new PaymentDetails($this->m_dbConn);
		$this->obj_PaymentDetails->actionType = "IMPORT";
		$this->obj_ChequeDetails = new ChequeDetails($this->m_dbConn);
		$this->obj_latestcount = new latestCount($this->m_dbConn);
		$this->changeLog = new changeLog($dbConn);

		$this->obj_fetch = new FetchData($this->m_dbConn);
		$a = $this->obj_fetch->GetSocietyDetails($_SESSION['society_id']);
		
	}
	public function ImportData($SocietyID)
	{
		
		date_default_timezone_set('Asia/Kolkata');		
		$this->errofile_name = 'import_payment_'.$SocietyID.'_'.date('Y-m-d').'.txt';
		$this->errorLog=$this->errofile_name;
		$errorfile=fopen($this->errofile_name, "a");
		
		$tmp_array=array();	
		if(isset($_POST["Import"]))
		{			
			$valid_files=array('Payment');
			$limit=count($_FILES['upload_files']['name']);
			$success=0;
			 
			for($m=0;$m<$limit;$m++)
			{
				$filename=$_FILES['upload_files']['name'][$m];
				$tmp_filename=$_FILES['upload_files']['tmp_name'][$m];
				for($i=0;$i<sizeof($valid_files);$i++)
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
			$logMsg ="Payment Imported | Imported By : ".$_SESSION['login_id']." | File Name : ".$filename."";	
			$this->changeLog->setLog($logMsg, $_SESSION['login_id'], "Payment",$tmp_filename);	

			$logfile="";
			$result=$this->startprocess($tmp_array[0],0,$errorfile);
			if($result <> '')
			{
				$this->actionPage="../import_payments_receipts.php";
				return $result;
			}
		}
	}
		
	function startprocess($filename,$pos,$errorfile)
	{
		if($pos==0)
		{
			$import_result=$this->UploadData($filename,$errorfile);
			return $import_result;
		}
		else
		{
			return 'All Data Imported Successfully...';
		}
	}
	
	public function UploadData($fileName,$errorfile)
	{
		$ChequeLeakBook=array();
		$file = fopen($fileName,"r");
		$data=0;
		$ChequeExistance='';
		$SuccessCount = 0;
		$TotalCount = 0;
		
		date_default_timezone_set('Asia/Kolkata');	
		$Foldername = $this->obj_fetch->objSocietyDetails->sSocietyCode;

		if (!file_exists('../logs/import_log/'.$Foldername)) 
		{
			mkdir('../logs/import_log/'.$Foldername, 0777, true);
		}
		
		$this->errorfile_name = '../logs/import_log/'.$Foldername.'/payment_import_errorlog_'.date("d.m.Y").'_'.rand().'.html';
		$this->errorLog = $this->errorfile_name;
		$errorfile = fopen($this->errorfile_name, "a");
		$errfile_name=$this->errorfile_name;
		$errormsg="[Importing Payment Details.....]";
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg,"I");
		
		$opening_date = "";

		if($_SESSION['society_creation_yearid'] <> "")
		{			
			$OpeningBalanceDate = $this->obj_utility->GetDateByOffset($this->obj_utility->getCurrentYearBeginingDate($_SESSION['society_creation_yearid']) , -1);
			if($OpeningBalanceDate <> "")
			{
				$opening_date = $OpeningBalanceDate;		
			}
		}
 		$IsRoundOffAmt= $this->obj_utility->GetSocietyInformation($_SESSION['society_id']);
		$IsRoundOff =false;
		if($IsRoundOffAmt['IsRoundOffLedgerAmt'] <> 0 && $_SESSION['default_ledger_round_off'] <> 0)
		{
			$IsRoundOff = true;
		}
		while (($row = fgetcsv($file)) !== FALSE)
		{
			//if($row[0] <> '')
			{
				$rowCount++;
				$errormsg = "";
				$SuccessMsg = "";
				$WarningMsg = "";
				
				if($rowCount == 1 || $rowCount == 2)
				{
					if($rowCount == 1 )
					{
						$VNoIndex = array_search(VNo,$row,true);
						$By = array_search(By_,$row,true);	
						if($By == false)
						{
							$By = array_search(BankLedger,$row,true);						
						}				
						
						$ChequeNo = array_search(ChequeNo,$row,true);	
						$ChequeDate = array_search(ChequeDate,$row,true);		
						$AccountName = array_search(AccountName,$row,true);
						$Amount = array_search(Amount,$row,true);
						$InvoiceNumber_Index = array_search(InvoiceNumber,$row,true);
						$InvoiceDate_Index = array_search(InvoiceDate,$row,true);
						$ExpenseHead = array_search(ExpenseHead, $row, true);
						$ExpenseAmt_Index = array_search(ExpenseAmt,$row,true);
						$CGST_Index = array_search(CGST, $row, true);
						$SGST_Index = array_search(SGST, $row, true);
						$TDS_Index = array_search(TDS, $row, true);
						$RoundOffAmount_Index = array_search(RoundOffAmount, $row, true);
						$Remark = array_search(Remark,$row,true);	
						
						
						//To Check Upload file is not in the previous Sample File format
						
						$SrNo = array_search(Sr,$row,true);
						$VSrNo = array_search(VSr,$row,true);
						$VDate = array_search(VDate,$row,true);
						$Particular = array_search(Particular,$row,true);
						 	
						if($SrNo == true || $VSrNo == true || $VDate == true || $Particular == true)
						{
							$result = 'Payment sample file format is changed now. Please download latest sample file then upload payment details';
							$errormsg .= $result;
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							return $result;
							exit(0);
						}
						else if($By == false || $ChequeNo == false || $ChequeDate == false || $AccountName == false || $Amount == false)
						{
							$result = 'Column Names Not Found Can\'t Proceed Further......'.'Go Back';
							$errormsg .=" Column names in file BankBook not match";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							return $result;
							exit(0);
						}
					}
				}
				else
				{
					try
					{
						$VNo = "";
						
						if(!empty($VNoIndex) || $VNoIndex === 0)
						{
							$VNo = $row[$VNoIndex];	
						}
						$TotalCount++;
						$by = $row[$By];
						$accountName = $row[$AccountName];
						$chequeNo = $row[$ChequeNo];
						$chequeDate = $row[$ChequeDate];
						$InvoiceNumber = $row[$InvoiceNumber_Index];
						$InvoiceDate = $row[$InvoiceDate_Index];
						$comments = $row[$Remark];	
						$amount = $row[$Amount];
						$CGST_Amount = $row[$CGST_Index];
						$SGST_Amount = $row[$SGST_Index];
						$TDS_Amount = $row[$TDS_Index];
						$RoundOff_Amount = $row[$RoundOffAmount_Index];
						$ExpenseAmt = $row[$ExpenseAmt_Index];
						
						$PayerBank = $this->getLedgerID($by);
						$sr = $row[$Sr];
						$expenseLeder = $row[$ExpenseHead];
						
						if(empty($VNo))
						{
							$IsSameCntApply = $this->obj_utility->IsSameCounterApply();
	
							//** Here we check the  whether we have to use same counter or different for all banks
							if($IsSameCntApply == 1)
							{
								$Counter = $this->obj_utility->GetCounter(VOUCHER_PAYMENT, 0,false);	
							}
							else
							{
								$Counter = $this->obj_utility->GetCounter(VOUCHER_PAYMENT, $PayerBank,false);
							}
						
							$VNo = $Counter[0]['CurrentCounter']; 
						}
						
						$SystemVoucherNo = $VNo;
						
						if($ExpenseHead == 0)
						{
							$expenseLeder = "";
						}

						if($PayerBank=="")
						{
							$errormsg .='Bank or Cash Account:'.$by.'Does Not Exists In Current Society.';
						}
						
						if($TotalCount == 1) // First Entry, So create Batch ID
						{
							$batch_name = 'payment_'.date("Y-m-d H:i:s");
							$created_by = $_SESSION['login_id'];
							$insertBatchQuery = "INSERT INTO `import_batch`(`BatchName`, `VoucherType`, `Imported_By`,`importfilename`) VALUES ('$batch_name','".VOUCHER_PAYMENT."','$created_by','$errfile_name')";

							$BatchID = $this->m_dbConn->insert($insertBatchQuery);
						}
						
						if(empty($BatchID)){

							$errormsg .="<br>Batch ID not found. Please contact to tech support team for further detail";
							break;
						}
						
						if(array_key_exists($by,$ChequeLeakBook) && strtolower($by) <> 'cash')
						{
							$data=$ChequeLeakBook[$by];
						}
						else if(strtolower($by) <> 'cash')
						{
						    $LeafName = 'DATA IMPORTED'.date('Y-m-d H:i:sa');
	
						    $insert_query1="insert into chequeleafbook (`LeafName`,`StartCheque`,`EndCheque`,`BankID`,`Comment`,`CustomLeaf`,`LeafCreatedYearID`) values ('".$LeafName."','0','0','".$PayerBank."','DATA IMPORTED','1','".$_SESSION['default_year']."')";
							$data = $this->m_dbConn->insert($insert_query1);
  	
						    $ChequeLeakBook[$by]=$data;
						}
					    
						$SuccessMsg = "Cheque Number &lt;".$chequeNo."&gt; Cheque Date &lt;".$chequeDate."&gt; Bank Name &lt;".$by."&gt; Acoount Name &lt;".$accountName."&gt; Amount &lt; ".$amount." &gt;";
						
						$PaidTo = $this->getLedgerID(trim($accountName));
						$ExpenceBy = $this->getLedgerID($expenseLeder);
						$AddJVEntry = 0;
						$AddTDS = 0;
						$AddRoundOff=0;
						$ExpenseSideTotal = 0;
						//$InvoiceDate = '0000-00-00';
						$IsInvoice = 0;
						$isSuspense=0;
						 if(!empty($InvoiceDate))
						   {
							   $InvoiceDate = getDBFormatDate($InvoiceDate);
						   }
						   else
						   {
							    $InvoiceDate = getDBFormatDate($chequeDate);
						   }
						if($InvoiceNumber == 0 || !empty($InvoiceNumber))
						{
							$InvoiceNumber = 1234;	
						}
						
						if(!empty($ExpenceBy))
						{
						   $AddJVEntry = 1;
						   
						   $SuccessMsg .= "Expense Head &lt;".$expenseLeder."&gt; Expense Amount &lt;".$ExpenseAmt."&gt;";
						   
						   if($CGST_Amount <> 0)
						   {
							   $SuccessMsg .= "CGST Amount &lt;".$CGST_Amount."&gt;";
						   }
						   
						   if($SGST_Amount <> 0)
						   {
							   $SuccessMsg .= "SGST Amount &lt;".$SGST_Amount."&gt;";
						   }
						  
						   $ExpenseSideTotal = $ExpenseAmt + $SGST_Amount + $CGST_Amount + $RoundOff_Amount - $TDS_Amount; //2 jv
						   $InvoiceAmount = $ExpenseAmt + $SGST_Amount + $CGST_Amount + $RoundOff_Amount;
						   if($IsRoundOff = true)
							{
						   		$InvoiceAmount = $this->obj_utility->getRoundValue2($InvoiceAmount);
							}
							
						   
						   $IsInvoice = 1;
						   if($amount <> $ExpenseSideTotal)
						   {
								$errormsg .='Expense total is not matching with Main Total';
						   }
						}
						if(!empty($RoundOff_Amount) && $RoundOff_Amount <> '0')
						{
							if($IsRoundOff = true)
							{
								$SuccessMsg .= "Round Off Amount &lt;".$RoundOff_Amount."&gt;";
							}
							else
							{
								$errormsg .='Round Off not enable on your society ';
							}
						}
						
						if(!empty($TDS_Amount) && $TDS_Amount <> '0')
						{
							$AddTDS = 1;
							
							$SuccessMsg .= "TDS Amount &lt;".$TDS_Amount."&gt;";
						}
						   
						if(is_numeric($chequeNo)== FALSE && strtolower($chequeNo)=='ecs')
						{
							$ModeOfPayment=1;
							$LeafID= $data;   
						}
						else if(is_numeric($chequeNo)== FALSE && strtolower($chequeNo)=='cash')
						{
							$ModeOfPayment= '-1'; 
							$LeafID= -1;  
						}
						else if(is_numeric($chequeNo)== FALSE && strtolower($chequeNo)=='other')
						{
							$ModeOfPayment=2;
							$LeafID= $data;   
						}
						else 
						{
							$ModeOfPayment= 0;
							$LeafID= $data;     
						}
						
						if($_SESSION['default_year_start_date'] <> "" && $_SESSION['default_year_end_date'] <> "")
						{
							$correct_cheque_date = $this->obj_utility->getIsDateInRange($chequeDate,$_SESSION['default_year_start_date'],$_SESSION['default_year_end_date']);
						}
						else
						{
							$sql07 = "select * from year where YearID = '".$_SESSION['default_year']."'";
							$sql77 = $this->m_dbConn->select($sql07);
							if($sql77 <> "")
							{
								$correct_cheque_date = $this->obj_utility->getIsDateInRange($chequeDate,$sql77[0]['BeginingDate'],$sql77[0]['EndingDate']);
							}
						}
						
						
						if(empty($PaidTo) || $PaidTo == 0)
						{
							$PaidTo = $_SESSION['default_suspense_account'];	
							$WarningMsg = "Account Name &lt;".trim($accountName)." &gt; not exits in system, so we add this entry under the suspense ledger";
							$isSuspense = 1;
						}
						
						
						
						if($PaidTo <> '')
						{
							$ChequeExistance = '';
							$ECSExistance = '';
							$CashExistance = '';
							$otherExistance = '';
							
							if($ModeOfPayment == 0) //cheque
							{
								$sql03="select id from paymentdetails where ChequeNumber='".$chequeNo."' and PayerBank='".$PayerBank."' ";								
								$resExistance = $this->m_dbConn->select($sql03);								
								$ChequeExistance = $resExistance[0]['id'];
							}
							else if($ModeOfPayment == 1) //ECS
							{
								$sql04 = 'select id from paymentdetails where ModeOfPayment="'.$ModeOfPayment.'" and ChequeDate="'.getDBFormatDate($chequeDate).'" and PaidTo="'.$PaidTo.'" and ChequeNumber="'.$chequeNo.'" and Amount="'.$amount.'"';
								$resExistance = $this->m_dbConn->select($sql04);
								$ECSExistance = $resExistance[0]['id'];
							}
							else if($ModeOfPayment == '-1') //cash entry
							{
								$sql05 = 'select id from paymentdetails where ModeOfPayment="'.$ModeOfPayment.'" and ChequeDate="'.getDBFormatDate($chequeDate).'" and PaidTo="'.$PaidTo.'" and ChequeNumber="'.$chequeNo.'" and Amount="'.$amount.'" and PayerBank="'.$PayerBank.'"';								
								$resExistance = $this->m_dbConn->select($sql05);
								$CashExistance = $resExistance[0]['id'];
							}
							else if($ModeOfPayment == 2) //other entry
							{
								$sql06 = 'select id from paymentdetails where ModeOfPayment="'.$ModeOfPayment.'" and ChequeDate="'.getDBFormatDate($chequeDate).'" and PaidTo="'.$PaidTo.'" and ChequeNumber="'.$chequeNo.'" and Amount="'.$amount.'" and PayerBank="'.$PayerBank.'"';
								$resExistance = $this->m_dbConn->select($sql06);
								$otherExistance = $resExistance[0]['id'];
							}
							
							if($PayerBank <> '' && $amount <> '')
							{
								if($ModeOfPayment == 0 && $ChequeExistance <> '')
								{									
									$errormsg .= "Cheque ".$chequeNo." already issued.";
								}
								else if($ModeOfPayment == 1 && $ECSExistance <> '')
								{
									$errormsg .= "ECS entry for Line No. <".$rowCount."> already done.";
								}
								else if($ModeOfPayment == '-1' && $CashExistance <> '')
								{
									$errormsg .= "Cash entry for Line No. <".$rowCount."> already done.";
								}
								else if($ModeOfPayment == 2 && $otherExistance <> '')
								{
									$errormsg .= "Other entry for Line No. <".$rowCount."> already done.";
								}
								else if($correct_cheque_date != 1)
								{
									$errormsg .= "Date not in range for Line No.: <".$rowCount."> .";
								}
							}
								
							if(empty($errormsg))
							{
								$success = '';
								$chequeDate = $this->getDBFormatDate($chequeDate);
								
								$IsEntryAdded = false;
								$success = $this->obj_PaymentDetails->AddNewValues($LeafID, $_SESSION['society_id'], $PaidTo, $chequeNo, $chequeDate, $VNo, $SystemVoucherNo, 1, $amount, $PayerBank, $comments, $this->getDBFormatDate($chequeDate), $ExpenceBy, $isDoubleEntry, $InvoiceDate, $TDS_Amount, $ModeOfPayment, 0, 0, 0, 0, 0, 0, 0,$InvoiceAmount,$PaymentVoucherNo, 0, ADD, false, $BatchID);
								
								if($AddJVEntry == 1)
								{
									$LatestVoucherNo = $this->obj_latestcount->getLatestVoucherNo($_SESSION['society_id']);								
									$VoucherNo = $LatestVoucherNo;
									
									
									$this->obj_PaymentDetails->CreatePaymentToJVDetailsEx($ExpenceBy, $PaidTo, $InvoiceDate, $InvoiceAmount, $CGST_Amount,$SGST_Amount,VOUCHER_JOURNAL, $comments, $VoucherNo,true,0,0,$RoundOff_Amount,0);	
									
								
									$DocumentStatus="Insert into `invoicestatus`(`NewInvoiceNo`,`InvoiceChequeAmount`,`InvoiceRaisedVoucherNo`,`AmountReceivable`,`TDSAmount`,`IGST_Amount`,`CGST_Amount`,`SGST_Amount`,`CESS_Amount`,`is_invoice`) values('".$InvoiceNumber."','".$InvoiceAmount."','".$VoucherNo."','".$InvoiceAmount."','".$TDS_Amount."','".$IGSTAmount."','".$CGST_Amount."','".$SGST_Amount."','".$CESSAmount."','".$IsInvoice."')";
									$DocStatusID = $this->m_dbConn->insert($DocumentStatus);
								}
								
								if($AddTDS == 1)
								{
									//$this->obj_PaymentDetails->AddTDSDetailsEx($PaidTo, $_SESSION['default_tds_payable'], $chequeDate, $TDS_Amount, VOUCHER_JOURNAL, $comments, $VoucherNo);
									$this->obj_PaymentDetails->AddTDSDetailsEx($PaidTo, $_SESSION['default_tds_payable'], $InvoiceDate, $TDS_Amount, VOUCHER_JOURNAL, $comments, $VoucherNo);
								}
								
								if($AddJVEntry == 1)
								{
									$this->obj_PaymentDetails->UpdateInvoiceStatus_with_invoiceVoucher($PaymentVoucherNo,$InvoiceNumber,$VoucherNo,$InvoiceAmount, $TDS_Amount,$DocStatusID,$IGSTAmount,$CGST_Amount,$SGST_Amount,$CESSAmount,$InvoiceRaisedVoucherNo,$PaidTo,$ExpenceBy,$InvoiceDate,$comments,0,false,$isSuspense);
								}
								
								
								if($success == 'Import Successful')
								{
									$SuccessMsg .= "<br>successfully imported.";
									$SuccessCount++; 
									$this->obj_utility->logGenerator($errorfile,$rowCount,$SuccessMsg,"I");
									if(!empty($WarningMsg))
									{
										$this->obj_utility->logGenerator($errorfile,$rowCount,$WarningMsg,"W");	
									}
								}
								else if($success != 'Import Successful')
								{
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								}
							}
							else 
							{
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							}
						}
						else
						{
							$errormsg .=implode(' | ',$row)."not inserted because account name ledger not found in ledger";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");   
					    }
				 	}
					catch ( Exception $e )
					{
						$errormsg=implode(' | ',$row);
						$errormsg .="not inserted";
						$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
						$this->obj_utility->logGenerator($errorfile,$rowCount,$e);
					}
				}
			}
		}
		$errormsg="[End of  Payment Details]";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg,"I");
		
		$errormsg = "<br><br><b>Number of Rows : ".$TotalCount."</b>";
		$errormsg .= "<br><b>Number of Rows Imported : ".$SuccessCount."</b>";
		$errormsg .= "<br><b>Number of Rows Not Imported : ".($TotalCount - $SuccessCount)."</b>";
		$this->obj_utility->logGenerator($errorfile,'',$errormsg);
		
		return  "file imported successfully..";
	}

	function getDateTime()
	{
		$dateTime = new DateTime();
		$dateTimeNow = $dateTime->format('Y-m-d H:i:s');
		return $dateTimeNow;
	}
	
	public function getLedgerID($LedgerName)
	{
		$LedgerID = "";

		if($LedgerName <> "")
		{
		 	$sql="select `id` from `ledger` where trim(`ledger_name`)=trim('".$LedgerName."') ";
			$result = $this->m_dbConn->select($sql);
			$LedgerID = $result[0]['id'];
		}	 
		
		return $LedgerID;
	}
	
	function getDBFormatDate($ddmmyyyy)
	{
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
}

?>

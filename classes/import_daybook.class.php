<?php
include_once("utility.class.php");
include_once("dbconst.class.php");
include_once("ChequeDetails.class.php");
include_once("PaymentDetails.class.php");
include_once("createvoucher.class.php");
include_once("genbill.class.php");
include_once("register.class.php");
include_once("bill_period.class.php");
include_once("include/fetch_data.php");

class dayBook 
{
  public $m_dbConn;	
  public $m_dbConnRoot;		
  public $smConn;
  public $m_objUtility;
  public $obj_ChequeDetails;
  public $obj_PaymentDetails;
  public $obj_createvoucher;
  public $m_fetch;
  public $m_bShowTrace;
  public $errorLog;
  public $errorfile_name;
  public $obj_register;
  public $obj_genbill;
  public $obj_billperiod;
  public $obj_fetch;

  public $CategoryGroupMap = array('Branch / Divisions'=>LIABILITY,'Profit & Loss A/c'=>LIABILITY,'Capital Account'=>LIABILITY,'Current Assets'=>ASSET,'Current Liabilities'=>LIABILITY,'Direct Expenses'=>EXPENSE,'Direct Incomes'=>INCOME,
  									'Fixed Assets'=>ASSET,'Indirect Expenses'=>EXPENSE,'Indirect Incomes'=>INCOME,'Investments'=>ASSET,'Loans (Liability)'=>LIABILITY,'Misc. Expenses (ASSET)'=>ASSET,'Purchase Accounts'=>EXPENSE,'Sales Accounts'=>INCOME,'Suspense A/c'=>LIABILITY);	
  private $BanksDepositIDs = array();
  private $ChequeLeakBook=array();
  private $LedgerDetailsArray = array();
  private $CategoryDetailsArray = array();
  private $LedgerNameArray = array();
  private $CategoryNameArray = array();
  private $UnitMappingKeys = array();
  private $mappingLedgerIDs = array();
  private $GUIDArray = array();
  private $VoucherArray = array();
  function __construct($dbConn)
  {
	  //** assing the connection to the variable
	  $this->m_dbConn = $dbConn;
	  $this->m_objUtility =  new utility($this->m_dbConn);
	  $this->obj_ChequeDetails=new ChequeDetails($this->m_dbConn);
	  $this->obj_PaymentDetails=new PaymentDetails($this->m_dbConn);
	  $this->obj_createvoucher = new createVoucher($this->m_dbConn);
	  $this->obj_register = new regiser($this->m_dbConn);
	  $this->obj_genbill = new genbill($dbConn);
	  $this->obj_billperiod = new bill_period($this->m_dbConn);
	  $this->m_bShowTrace = 0;
	  $this->obj_fetch = new FetchData($this->m_dbConn);

	  $a = $this->obj_fetch->GetSocietyDetails($_SESSION['society_id']);
	}
   			
  public function dayBookProcess($daybookData)
  {
	 echo '<font style="color:red; font-size:20px;"><b>Please Wait...</b></font>' ;
	 $rowCnt = 1;
	 date_default_timezone_set('Asia/Kolkata');		

	$Foldername = $this->obj_fetch->objSocietyDetails->sSocietyCode;

	if (!file_exists('../logs/import_log/'.$Foldername)) 
	{
		mkdir('../logs/import_log/'.$Foldername, 0777, true);
	}
	 $this->errorfile_name = '../logs/import_log/'.$Foldername.'/daybook_import_errorlog_'.date("d.m.Y").'_'.rand().'.html';

	 $errorfile = fopen($this->errorfile_name, "a");

//*********************************Period Add Start Here ****************************//

	 
	$data = $this->m_dbConn->select("SELECT `bill_cycle` FROM `society` WHERE `status` = 'Y' " );
	if($data[0]['bill_cycle'] <> "" && $data[0]['bill_cycle'] <> 0)
	{	
		$billing_cycle = $data[0]['bill_cycle'];
		
		$getYear = $this->m_dbConn->select("SELECT `YearID`, `YearDescription` FROM `year` where `YearID`='".$_SESSION['default_year']."' " );
		
		if($getYear[0]['YearID'] <> "")
		{
			$YearID = $getYear[0]['YearID']; 
		
			if($billing_cycle <>'' && $YearID<>'')
			{									
				$res = $this->m_dbConn->select("select count(YearID) as count from `period` where `Billing_cycle`='".$billing_cycle."' and `YearID`= '".$YearID."'");
									
				if($res[0]['count'] == 0)
				{ 
					$months = getMonths($billing_cycle);
					$this->obj_billperiod->setPeriod($months ,$billing_cycle,$YearID);																																				
					$errormsg = "<br>Periods Inserted Successfully for Year ".$getYear[0]['YearDescription']."<br><br>";
				}
				else
				{
					$errormsg = "<br>Periods Already Exists For Year ".$getYear[0]['YearDescription']."<br><br>";		
				}												
			}
			else
			{
				$errormsg = "<br>Billing Cycle Or YearID Is Empty For Society <br><br>";			
			}
					
		}
		
		$this->m_objUtility->logGenerator($errorfile,'',$errormsg);
	}
//******************************Period Add End Here ********************************************************//

	 
	 $errormsg="[Importing DayBook Details] Created At [".date("d.m.Y")."]";
	 $this->m_objUtility->logGenerator($errorfile,'start',$errormsg);

	 $rowCnt = 1;
	 $NumberofRowImported = 0;
	 
	 $this->FetchUnitMappingKey();
	 $this->FetchGUID();
	 foreach($daybookData as $rowdata)
	 {
		 $VoucherType = $rowdata['VoucherType'];
		 if($this->m_bShowTrace)
		 {
			 echo '<br>==============================================LINE : '.$rowCnt.' IS '.strtoupper($VoucherType).'========================================================================<br>';			 
		 }
		 
		 switch ($VoucherType) 
		 {
			case "Receipt":
				$result = $this->callReceiptMethod2($rowdata,$errorfile,$rowCnt);
				break;
			case "Payment":
				$result = $this->callPaymentMethod($rowdata,$errorfile,$rowCnt);
				break;
			case "Contra":
				//echo '<br><b>Contra : </b>'.$rowCnt;
				$result = $this->callPaymentMethod($rowdata,$errorfile,$rowCnt);
				break;
			case "Journal":
				$result = $this->callJournalMethod($rowdata,$errorfile,$rowCnt);
				break;
			case "Debit Note":
				$result = $this->callDebitCreditMethod($rowdata,DEBIT_NOTE,$errorfile,$rowCnt);
				break;
			case "Credit Note":
				$result = $this->callDebitCreditMethod($rowdata,CREDIT_NOTE,$errorfile,$rowCnt);
				break;		
			default:
				echo "Voucher Type Not Found!!";
		}
		$rowCnt++;
		if($result == true)
		{
			$NumberofRowImported++;	
		}
		
	 }
	 
	 $errormsg="[End Importing DayBook Details]"; 
	 $this->m_objUtility->logGenerator($errorfile,'End',$errormsg);
	 $TotalRow = $rowCnt-1;
	 
	 $errormsg = "<br><br><br><b>Total Number of Row is  ".$TotalRow."</b>";
	 $errormsg .= "<br><b>Total Number of Row is Imported  ".$NumberofRowImported."</b>";
	 $errormsg .= "<br><b>Total Number of Row is not Imported  ".($TotalRow - $NumberofRowImported)."</b>";
	 $this->m_objUtility->logGenerator($errorfile,'',$errormsg);
	    
  }
  
  public function callReceiptMethod($Data,$errorfile,$rowCnt)
  {
	  $rowerrormsg = '';
	  if($this->m_bShowTrace)
		 {
			 echo '<br>*********INSIDE callReceiptMethod FUNCTION ***************<br>';
			 var_dump($Data);	
			 //return;		 
		 }
		
		//***By and To side entry must be same in receipt and NumberofEntries storing number of record
		if($NumberofEntries = count($Data['ByLedgers']) == count($Data['ToLedgers']))
		{
			//** Date is not in proper format so need to convert it
			$ChequeDate = $Data['Date'];
			//**Require Parameter to call addNewDetails in cheque.class.php
			$VoucherDate = $ChequeDate;
		 	$ChequeNumber = '';
			$IsCallUpdtCnt = 0;
			$ByLedger = array_keys($Data['ByLedgers']);
			$ToLedger = array_keys($Data['ToLedgers']);
			$ChequeNumber = '-';
			$BillType = 0;
			$Comments = $Data['Description'];
			$PayerBank = '-';
			$PayerChequeBranch = '-';
			$beginDate = $_SESSION['default_year_start_date'];
			$endDate = $_SESSION['default_year_end_date'];
			
			// ** Now we check number of receipt present in single row
			for($i = 0 ; $i < $NumberofEntries; $i++)
			{
				if(in_array($ToLedger[$i],$this->UnitMappingKeys))
				{
					$ledgerIndex = array_search($ToLedger[$i],$this->UnitMappingKeys);
					$PaidBy = $this->mappingLedgerIDs[$ledgerIndex];
				}
				else
				{
					$PaidBy = $this->getLedgerID($ToLedger[$i]);
				}
				$BankID = $this->getLedgerID($ByLedger[$i]);
				
				if($VoucherDate == '' || $VoucherDate == '0' || $VoucherDate == '0000-00-00' || $VoucherDate == '0000-00-00') 
				{
					$rowerrormsg .= "<br>Voucher Date Can Not be 0 or empty";
				}
				else if($this->m_objUtility->check_in_range($beginDate, $endDate, $VoucherDate) == false)
				{
					$rowerrormsg .= "<br>Voucher Date &lt;".$VoucherDate."&gt;Not In Range. It Should Be Between ".$beginDate." and ".$endDate;
					return false;
				}
				//***Check whether Bank or PaidBy Ledger is not empty or 0
				if($PaidBy == 0 || $PaidBy == '' || $BankID == 0 || $BankID == '')
				{
					//if($this->m_bShowTrace)
					//{
						if($PaidBy == 0 || $PaidBy == '')
						{
							$rowerrormsg .='<br> "<b>'.$ToLedger[$i].'</b>"  Not Found Please Create Ledger';	
						}
						if($BankID == 0 || $BankID == '')
						{
							$rowerrormsg .= '<br>"<b>'.$ByLedger[$i].'</b>"  Not Found Please Insert Valid Bank Name';	
						}
						
					//}
				}
					
				//*** Debit and Credit Amount must be same
				if($Data['ByLedgers'][$ByLedger[$i]] - $Data['ToLedgers'][$ToLedger[$i]] == 0)
				{
					$Amount = $Data['ToLedgers'][$ToLedger[$i]];
					$VoucherCounter = $Data['VoucherNumber'];
					$SystemVoucherNo = $VoucherCounter;
				}
				else
				{
					 //if($this->m_bShowTrace)
					 //{
						 $rowerrormsg .= '<br>Amout is not valid/matched';
					 //}
				}
				
				if($rowerrormsg <> ''  || $rowerrormsg <> 0 )
				{
					$rowerrormsg .= "<br>Row Not Inserted";
					$this->m_objUtility->logGenerator($errorfile,$rowCnt,$rowerrormsg,'E');
					
					return false;
				}
				
				//**Check Deposit Id is already exits or not for data bank
				if(array_key_exists($BankID,$this->BanksDepositIDs))
				{
					$data=$this->BanksDepositIDs[$BankID];
				}
				else
				{
					//*** Deposit Part
					$desc = 'DATA IMPORTED'.date('Y-m-d H:i:sa');
					
					$insert_query1="insert into depositgroup (`bankid`,`createby`,`depositedby`,`status`,`desc`,`DepositSlipCreatedYearID`) values ('".$BankID."','".$_SESSION['login_id']."','Import Data','0','".$desc."','".$_SESSION['default_year']."')";
					$data = $this->m_dbConn->insert($insert_query1);
					$this->BanksDepositIDs[$BankID]=$data;	
				}
				$DepositID = $data;
				
				if($rowerrormsg == '' || $rowerrormsg == 0)
				{
					//***Calling Chequedetails.class.php
					$Result = $this->obj_ChequeDetails->AddNewValues($VoucherDate, $ChequeDate, $ChequeNumber, $VoucherCounter,$SystemVoucherNo,$IsCallUpdtCnt, $Amount, $PaidBy, $BankID, $PayerBank, $PayerChequeBranch, $DepositID, $Comments,$BillType); 
					
					//if($this->m_bShowTrace)
					//{
						 $rowerrormsg = 'VoucherDate : <'.$VoucherDate.'> Cheque Date : <'.$ChequeDate.'> Cheque Number : <'.$ChequeNumber.'>VoucherNumber : <'.$VoucherCounter.'
						 > Amount : <'.$Amount.'> PaidBy :<'.$ToLedger[$i].'> BankID : <'.$ByLedger[$i].'> PayerBank : <'.$PayerBank.'> PayerChequeBranch : <'.$PayerChequeBranch.'>
						  BillType : <Maintaince> Comments : &lt;'.$Comments.'&gt; DepositID : <'.$DepositID.'>';
						 
					//}
					if($Result == 'Insert')
					{
						$rowerrormsg .='<br>Row Inserted Successfully';
						$this->m_objUtility->logGenerator($errorfile,$rowCnt,$rowerrormsg,'I');
						return true;
					}
				}
			}
		}
	}

  public function callReceiptMethod2($Data,$errorfile,$rowCnt)
  {
	  $rowerrormsg = '';
	  if($this->m_bShowTrace)
		 {
			 echo '<br>*********INSIDE callReceiptMethod FUNCTION ***************<br>';
			 var_dump($Data);
			 //return;			 
		 }
		
		//***By and To side entry must be same in receipt and NumberofEntries storing number of record
		
			//** Date is not in proper format so need to convert it
			$ChequeDate = $Data['Date'];
			//**Require Parameter to call addNewDetails in cheque.class.php
			$VoucherDate = $ChequeDate;
		 	$ChequeNumber = $this->getCheckNumber($Data['Description']);
			$IsCallUpdtCnt = 1;
			$ByLedgers=$Data['ToLedgers'];
			$ByLedger = array_keys($ByLedgers);
			$ToLedgers=$Data['ByLedgers'];
			$ToLedger = array_keys($ToLedgers);
			$ListOfAccounts=array();
			$ListOfBankLedger=array();
			$ListOfCashLedger=array();
			$BillType = 0;
			$Comments = $Data['Description'];
			$PayerBank = '--';
			$PayerChequeBranch = '--';
			$beginDate = $_SESSION['default_year_start_date'];
			$endDate = $_SESSION['default_year_end_date'];
			$ByDetail = array();
			$ToDetail = array();
			$PayerBankID='';
			$PayerBankName='';
			$ExternalCounter = $Data['VoucherNumber'];
		 	$Vouchertype = $Data['VoucherType'];
			$Vouchertype='Receipt';
			$SystemVoucherNo=$ExternalCounter;
			$GUID = $Data['GUID'];
			
			$rowerrormsg = '';
			
			
			 if(in_array($GUID,$this->GUIDArray))
			 {
				 $GUIDIndex = array_search($GUID,$this->GUIDArray);
				 $VoucherNo = $this->VoucherArray[$GUIDIndex];
				 $rowerrormsg .= "Transaction already exits in ".$Vouchertype." with voucher Number ".$VoucherNo;
			 }
			
			
			if($this->m_objUtility->check_in_range($beginDate, $endDate, $ChequeDate ) == false)
		 	{
				 $rowerrormsg.='Date Not In Range &lt;'.$ChequeDate.'&gt;';
			 
			// return false;
			 //$rowerrormsg .= "<br>Voucher Date &lt;".$VoucherDate."&gt;Not In Range. It Should Be Between ".$beginDate." and ".$endDate;
		 	}
			/*echo '<br>By Ledgers : ';
			var_dump($ByLedgers);
			echo '<br>To Ledgers : ';
			var_dump($ToLedgers);*/
			// ** Now we check number of receipt present in single row
		//Making Array of ledgers
		 for($i = 0 ; $i < sizeof($ByLedgers); $i++)
		 {
			if(in_array($ByLedger[$i],$this->UnitMappingKeys))
		 	{ 
				$byLedgerIndex[$i] = array_search($ByLedger[$i],$this->UnitMappingKeys);
				$ByDetail[$i]['Head'] = $this->mappingLedgerIDs[$byLedgerIndex[$i]];
			}
			else
			{
				$ByDetail[$i]['Head'] = $this->getLedgerID($ByLedger[$i]);	
			}
			if($ByDetail[$i]['Head']=='')
			{
				$rowerrormsg .="Ledger Name &lt;".$ByDetail[$i]['Head']."&gt; not found";
			}
			$ByDetail[$i]['Amt'] = $ByLedgers[$ByLedger[$i]] ;
			$ByTotal=$ByTotal+$ByDetail[$i]['Amt'];
			 //$ByDetail[$i]['invoicetaxable'] = 0;
		}
		//var_dump($ByDetail);
		for($i=0 ; $i<sizeof($ToLedgers) ; $i++)
		{
			if(in_array($ToLedger[$i],$this->UnitMappingKeys))
		 	{ 
				$toLedgerIndex[$i] = array_search($ToLedger[$i],$this->UnitMappingKeys);
				$ToDetail[$i]['Head'] = $this->mappingLedgerIDs[$toLedgerIndex[$i]];
			}
			else
			{
				$ToDetail[$i]['Head']=$this->getLedgerID($ToLedger[$i]);
			}
			if($ToDetail[$i]['Head']=='')
			{
				$rowerrormsg .="Ledger Name &lt;".$ToDetail[$i]['Head']."&gt; not found";
			}
			$ToDetail[$i]['Amt'] = $ToLedgers[$ToLedger[$i]] ;
			$ToTotal=$ToTotal+$ToDetail[$i]['Amt'];
		}
		
		
			$ListOfCashLedger = $this->m_objUtility->GetBankLedger($_SESSION['default_cash_account']);
			$ListOfBankLedger = $this->m_objUtility->GetBankLedger($_SESSION['default_bank_account']);
			
			$CashLedgers = array();
			$PayerBank='';
			
			$ListOfAccounts = array_merge($ListOfBankLedger,$ListOfCashLedger);
			
			//var_dump($ListOfAccounts);
			for($j=0;$j<sizeof($ListOfAccounts);$j++)
			{
				for($i=0;$i<sizeof($ToDetail);$i++)
				{
					if($ToDetail[$i]['Head']==$ListOfAccounts[$j]['id'] && $ToLedger[$i]==$ListOfAccounts[$j]['ledger_name'])
					{
						$PayerBankID = $ToDetail[$i]['Head'];
						$PayerBankName = $ToLedger[$i];
						$PayerAmount = $ToDetail[$i]['Amt'];
					}
						
				}
			}
		//public function SetVoucherDetails($BillDate, $RefNo, $RefTableID, $VoucherNo, $SrNo, $VoucherTypeID, $LedgerID, $TransactionType, $Amount, $note = "")
		
		/*echo '<br> Payer Bank ID : '.$PayerBankID;
		echo '<br>Payer Bank Name : '.$PayerBankName;
		
		echo '<br>By Total : '.$ByTotal;
		echo '<br>To Total : '.$ToTotal;*/
		//ToCheckSameFloatValues
		$PaidBy=$ByDetail[0]['Head'];
		$Amount=$ByTotal+$ToTotal;
		
		if (abs(($ByTotal-$ToTotal)/$ByTotal) < 0.00001) 
		{
			if($PayerBankID=="")
			{
				$rowerrormsg .='<br>Bank or Cash Account:Does Not Exists In Current Society.';
				//$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg);
			}
			
			
				//$AccountsArray = array_column($ListOfAccounts, 'id');
						//if(in_array($ToArray[$i]['Head'],$AccountsArray))
			for($i=0;$i<sizeof($ListOfAccounts);$i++)
			{
				if($PayerBankName==$ListOfCashLedger[$i]['ledger_name'])
				{
					$cash_account=$PayerBankName;
				}
				if($PayerBankName==$ListOfBankLedger[$i]['ledger_name'])
				{
					$bank_account=$PayerBankName;
				}
			}
			/*
			echo '<br>Bank Account : '.$bank_account;
			echo '<br>Cash Account : '.$cash_account;*/
				if($rowerrormsg <> ''  || $rowerrormsg <> 0 )
				{
					$rowerrormsg .= "<br>Row Not Inserted";
					$this->m_objUtility->logGenerator($errorfile,$rowCnt,$rowerrormsg,'E');
					
					return false;
				}
				
			
				//**Check Deposit Id is already exits or not for data bank
				if(array_key_exists($PayerBankID,$this->BanksDepositIDs))
				{
					$data=$this->BanksDepositIDs[$PayerBankID];
				}
				else
				{
					//*** Deposit Part
					$desc = 'DATA IMPORTED'.date('Y-m-d H:i:sa');
					//echo '<br>Where it should go';
					$insert_query1="insert into depositgroup (`bankid`,`createby`,`depositedby`,`status`,`desc`,`DepositSlipCreatedYearID`) values ('".$PayerBankID."','".$_SESSION['login_id']."','Import Data','0','".$desc."','".$_SESSION['default_year']."')";
					$data = $this->m_dbConn->insert($insert_query1);
					$this->BanksDepositIDs[$PayerBankID]=$data;	
				}
				/*echo '<br>Bank Data : '.$data;
				var_dump($data);*/
				$DepositID = $data;
				
			/*	echo '<br>Deposit ID : '.$DepositID;
				echo '<br>Row Message : '.$rowerrormsg;
				echo '<br>Voucher Counter : '.$ExternalCounter;
				*/
				if($rowerrormsg == '' || $rowerrormsg == 0)
				{
					//***Calling Chequedetails.class.php
					/*echo '<br>Before Result';
					echo '<br>Bank ID '.$BankID;
					echo '<br> Amount '.$Amount;*/
					//$data, $_SESSION['society_id'], $PaidTo, $ChequeNumber, $ChequeDate, $amount, $PayerBankID, $PaymentNote, $VoucherDate, $ModeOfPayment,$ByDetail,$ToDetail,$isDoubleEntry,$ListOfAccounts,$ExternalCounter,$SystemVoucherNo
					$Result = $this->obj_ChequeDetails->AddNewValues2( $DepositID,$PaidBy,$ChequeNumber,$VoucherDate, $ChequeDate,$ByDetail,$ToDetail,$ListOfAccounts, $ExternalCounter,$SystemVoucherNo,$IsCallUpdtCnt,$PayerAmount, $PayerBankID,$PayerBankName,$PayerChequeBranch, $Comments,$Vouchertype,0,false,$GUID); 
					/*echo '<br>After Result';*/
					
					//if($this->m_bShowTrace)
					//{
						 $rowerrormsg = 'VoucherDate : &lt;'.$VoucherDate.'&gt; Cheque Date : &lt;'.$ChequeDate.'&gt; Cheque Number : &lt;'.$ChequeNumber.'&gt; VoucherNumber : &lt;'.$VoucherCounter.'
						 &gt; Amount : &lt;'.$ToTotal.'&gt; PaidBy :&lt;'.$ToLedger[0].'&gt; BankID : &lt;'.$PayerBankID.'&gt; PayerBank : &lt;'.$PayerBank.'&gt; PayerChequeBranch : &lt;'.$PayerChequeBranch.'&gt;
						  BillType : &lt;Maintaince&gt; Comments : &lt;'.$Comments.'&gt; DepositID : &lt;'.$DepositID.'&gt;';
						 
					//}
					if($Result == 'Import Successful')
					{
						$rowerrormsg .='<br>Row Inserted Successfully';
						$this->m_objUtility->logGenerator($errorfile,$rowCnt,$rowerrormsg,'I');
						return true;
					}
				}
			}
		
	}

	
  public function callPaymentMethod($Data,$errorfile,$rowCnt)
  {
	   if($this->m_bShowTrace)
		 {
			 echo '<br>*********INSIDE callPaymentMethod FUNCTION ***************<br>';
			 var_dump($Data);
			 //return;
			 				 
		 }
		 
		 $Vouchertype = $Data['VoucherType'];
		 
		 $ByDetail = array();
		 $ToDetail = array();
		 $byLedgerIndex=array();
		 $toLedgerIndex=array();
		 $ByUnitID=array();
		 $ToUnitID=array();
		 $ByTotal=0;
		 $ToTotal=0;
		 $PayerBankID='';
		 $PayerBankName='';
		 $ChequeDate = $Data['Date'];
		 $expenseLeder = "";
		 $SubCategoryLedger = "";
		 $PaymentNote = $Data['Description'];
		 $CreditDebitEditable_ID = 0;
		 $IsCallUpdtCnt = 1;
		 $ExternalCounter = $Data['VoucherNumber'];
		 $Vouchertype = $Data['VoucherType'];
		 $beginDate = $_SESSION['default_year_start_date'];
		 $endDate = $_SESSION['default_year_end_date'];
		 $errormsg = '';
		 $SystemVoucherNo = $VoucherCounter;
		 $GUID = $Data['GUID'];
		 $ChequeNumber = $this->getCheckNumber($Data['Description']);	
		
		 $ByLedgersHeadAndAmount = $Data['ToLedgers'];
		 $ToLedgersHeadAndAmount=$Data['ByLedgers'];
		 
		  if(in_array($GUID,$this->GUIDArray))
		 {
			 $GUIDIndex = array_search($GUID,$this->GUIDArray);
			 $VoucherNo = $this->VoucherArray[$GUIDIndex];
			 $rowerrormsg .= "Transaction already exits in ".$Vouchertype." with voucher Number ".$VoucherNo;
		 }
		 
		 
		  if($this->m_objUtility->check_in_range($beginDate, $endDate, $ChequeDate ) == false)
		 {
			 echo $rowerrormsg.='Date Not In Range &lt;'.$ChequeDate.'&gt;';
			 
			// return false;
			 //$rowerrormsg .= "<br>Voucher Date &lt;".$VoucherDate."&gt;Not In Range. It Should Be Between ".$beginDate." and ".$endDate;
		 }
		 $ByLedgerName = array_keys($ByLedgersHeadAndAmount);
		 $ToLedgerName=array_keys($ToLedgersHeadAndAmount);
		 //Making Array of ledgers
		 for($i = 0 ; $i < sizeof($ByLedgersHeadAndAmount); $i++)
		 {
			if(in_array($ByLedgerName[$i],$this->UnitMappingKeys))
		 	{ 
				$byLedgerIndex[$i] = array_search($ByLedgerName[$i],$this->UnitMappingKeys);
				$ByDetail[$i]['Head'] = $this->mappingLedgerIDs[$byLedgerIndex[$i]];
			}
			else
			{
				$ByDetail[$i]['Head'] = $this->getLedgerID($ByLedgerName[$i]);	
			}
			if($ByDetail[$i]['Head']=='')
			{
				$rowerrormsg .='Invalid By Ledger Name &lt;'.$ByDetail[$i]['Head'].'&gt;';
				//continue;
			}
			$ByDetail[$i]['Amt'] = $ByLedgersHeadAndAmount[$ByLedgerName[$i]] ;
			$ByTotal=$ByTotal+$ByDetail[$i]['Amt'];
			 //$ByDetail[$i]['invoicetaxable'] = 0;
		}
		for($i=0 ; $i<sizeof($ToLedgersHeadAndAmount) ; $i++)
		{
			if(in_array($ToLedgerName[$i],$this->UnitMappingKeys))
		 	{ 
				$toLedgerIndex[$i] = array_search($ToLedgerName[$i],$this->UnitMappingKeys);
				$ToDetail[$i]['Head'] = $this->mappingLedgerIDs[$toLedgerIndex[$i]];
			}
			else
			{
				$ToDetail[$i]['Head']=$this->getLedgerID($ToLedgerName[$i]);
			}
			if($ToDetail[$i]['Head']=='')
			{
				$rowerrormsg .='Invalid To Ledger Name &lt;'.$ToDetail[$i]['Head'].'&gt;';
				//continue;
			}
			$ToDetail[$i]['Amt'] = $ToLedgersHeadAndAmount[$ToLedgerName[$i]] ;
			$ToTotal=$ToTotal+$ToDetail[$i]['Amt'];
		}
		
			$ListOfCashLedger = $this->m_objUtility->GetBankLedger($_SESSION['default_cash_account']);
			$ListOfBankLedger = $this->m_objUtility->GetBankLedger($_SESSION['default_bank_account']);
			$CashLedgers = array();
			$PayerBank='';
			
			$ListOfAccounts = array_merge($ListOfBankLedger,$ListOfCashLedger);
			
			//var_dump($ListOfAccounts);
			for($j=0;$j<sizeof($ListOfAccounts);$j++)
			{
				for($i=0;$i<sizeof($ByDetail);$i++)
				{
					if($ByDetail[$i]['Head']==$ListOfAccounts[$j]['id'] && $ByLedgerName[$i]==$ListOfAccounts[$j]['ledger_name'])
					{
						//echo '<br>Payer Bank Present ';
						$PayerBankID=$ByDetail[$i]['Head'];
						$PayerBankName=$ByLedgerName[$i];
						$PayerAmount=$ByDetail[$i]['Amt'];
					}
						
				}
				for($i=0; $i < sizeof($ToDetail); $i++)
				{
					if($ToDetail[$i]['Head']==$ListOfAccounts[$j]['id'] && $ToLedgerName[$i]==$ListOfAccounts[$j]['ledger_name'])
					{
						$Vouchertype='Contra';
					}
				}
			}
		//public function SetVoucherDetails($BillDate, $RefNo, $RefTableID, $VoucherNo, $SrNo, $VoucherTypeID, $LedgerID, $TransactionType, $Amount, $note = "")
		
		//ToCheckSameFloatValues
		if (abs(($ByTotal-$ToTotal)/$ByTotal) < 0.00001) 
		{
			if($PayerBankID=="")
			{
				$rowerrormsg .='<br>Bank or Cash Account:Does Not Exists In Current Society.';
				//$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg);
			}
			
			
				//$AccountsArray = array_column($ListOfAccounts, 'id');
						//if(in_array($ToArray[$i]['Head'],$AccountsArray))
			for($i=0;$i<sizeof($ListOfAccounts);$i++)
			{
				if($PayerBankName==$ListOfCashLedger[$i]['ledger_name'])
				{
					$cash_account=$PayerBankName;
				}
				if($PayerBankName==$ListOfBankLedger[$i]['ledger_name'])
				{
					$bank_account=$PayerBankName;
				}
			}
			
			
			//echo '<br>Payer Bank : '.$PayerBankName;
			if(array_key_exists($PayerBankID,$this->ChequeLeakBook) && $PayerBankName <> $cash_account)
			{
				$data=$this->ChequeLeakBook[$PayerBankID];
			}
			else if($PayerBankName <> $cash_account)
			{
				 $LeafName = 'DATA IMPORTED'.date('Y-m-d H:i:sa');
			    $insert_query1="insert into chequeleafbook (`LeafName`,`StartCheque`,`EndCheque`,`BankID`,`Comment`,`CustomLeaf`,`LeafCreatedYearID`) values ('".$LeafName."','0','0','".$PayerBankID."','DATA IMPORTED','1','".$_SESSION['default_year']."')";
				$data = $this->m_dbConn->insert($insert_query1);
  						    
				$this->ChequeLeakBook[$PayerBankID]=$data;
			}	
			if($PayerBankName==$bank_account)
			{
				$ModeOfPayment=0;
			}
			if($PayerBankName==$cash_account)
			{
				$ModeOfPayment=2;
			}
						
				$ExpenseTo = 0;
				$isDoubleEntry = 0;
			   
			$PaidTo=$ToDetail[0]['Head'];
			
			$amount=$ToTotal=$ByTotal;
			
			
			
			if($PaidTo <> '')
			{
				$ChequeExistance = '';
				$ECSExistance = '';
				$CashExistance = '';
				$otherExistance = '';
				
				if($PayerBankName <> '' && $amount <> '')
				{
					if($ModeOfPayment == 0 && $ChequeExistance <> '')
					{									
						echo $errormsg="Cheque ".$chequeNo." already issued.";
						//$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);   
					}
					else if($ModeOfPayment == 1 && $ECSExistance <> '')
					{
						$errormsg = "ECS entry for Voucher No. <".$sr."> already done.";
						//$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
					}
					else if($ModeOfPayment == '-1' && $CashExistance <> '')
					{
						//echo "Found cash<br>";
						$errormsg = "Cash entry for Voucher No. <".$sr."> already done.";
						//$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
					}
					else if($ModeOfPayment == 2 && $otherExistance <> '')
					{
						$errormsg = "Other entry for Voucher No. <".$sr."> already done.";
						//$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
					}
					else if($ChequeExistance == '' && $ECSExistance == '' && $CashExistance == '' && $otherExistance == '')
					{
						if($isDoubleEntry == 1)
						{
							$success = '';
							//echo "called 1";
							
							//$success = $this->obj_PaymentDetails->AddNewPaymentEntry($LeafID, $_SESSION['society_id'], $PaidTo, $chequeNo, $this->getDBFormatDate($chequeDate), $amount, $PayerBank, $comments, $this->getDBFormatDate($voucherDate), $ExpenseTo, $isDoubleEntry, $this->getDBFormatDate($chequeDate), 0, $ModeOfPayment, 0, 0, 0, 0, 0, 0, 0, $amount);
							if($success == 'Import Successful')
							{
								$errormsg = "Voucher No. <".$sr."> successfully imported.";
								//$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
							}
							else if($success != 'Import Successful')
							{
								$errormsg = "Voucher No. <".$sr."> not imported successfully.";
								//$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
							}
						}
						else
						{
							
							if($rowerrormsg <> '' || $rowerrormsg <> 0)
							{
								$rowerrormsg .= "<br>Row not imported.";
								$this->m_objUtility->logGenerator($errorfile,$rowCnt,$rowerrormsg,'E');
								return false;
								//$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
							}
							///echo '<br>Where It Should Go';
							$VoucherDate=$ChequeDate;
							$success = '';
							$rowerrormsg = 'Leaf ID : &lt;'.$data.'&gt; Paid To : &lt;'.$PaidTo.'&gt; Cheque Number : &lt;'.$ChequeNumber.'&gt; Cheque Date : &lt;'.$ChequeDate.'&gt; Amount : &lt;'.$amount.'&gt; Payer Bank Name : &lt;'.$PayerBankName.'&gt; Voucher Date : &lt;'.$VoucherDate.'&gt; Mode Of Payment : &lt;'.$ModeOfPayment.'&gt;';
							/*echo '<br>By Detail : '.var_dump($ByDetail);
							
							echo '<br>To Detail : '.var_dump($ToDetail);
							echo '<br> Is Double Entry : '.$isDoubleEntry.'<br>List Of Accounts : '.var_dump($ListOfAccounts).' External Counter : '.$ExternalCounter.' System Voucher Number : '.$SystemVoucherNo;
						//	echo '<br>Payer Amount : '.$PayerAmount;*/
							
							$success = $this->obj_PaymentDetails->AddNewPaymentEntry($data, $_SESSION['society_id'], $PaidTo, $ChequeNumber, $ChequeDate, $PayerAmount, $PayerBankID, $PaymentNote, $Vouchertype, $VoucherDate, $ModeOfPayment,$ByDetail,$ToDetail,$isDoubleEntry,$ListOfAccounts,$ExternalCounter,$SystemVoucherNo,$IsCallUpdtCnt,false,0,0,0,0,$GUID);
							
							
							if($success == true)
							{
								$rowerrormsg .= "<br>Row successfully imported.";
								$this->m_objUtility->logGenerator($errorfile,$rowCnt,$rowerrormsg,'I');
								return true;
								//$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
							}
						}
					}
				}
				
			}
			
						/*}
					
					catch ( Exception $e )
					{
						
					    $errormsg=implode(' | ',$row);
						$errormsg .="not inserted";
						$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
						$this->obj_utility->logGenerator($errorfile,$sr,$e);
					}
				}
			}
		}
		$errormsg="[End of  Payment Details]";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
		return  "file imported successfully..";
	}

*/			
			
			
			 /*echo '<br><b>Balanced</b>';
			 for($i=0 ; $i<sizeof($ToLedgersHeadAndAmount) ; $i++)
			 {
				 $this->obj_PaymentDetails->AddNewPaymentEntry($ChequeDate,$ChequeNumber,$Amount,$PaidTo,$_SESSION['login_id'],$PayerBank,$Comments,$VoucherDate);
			 }
			 for($i = 0 ; $i < sizeof($ByLedgersHeadAndAmount); $i++)
		   	 {
				 //AddNewPaymentEntry($InvoiceDate,$ExVoucherNo,$ByArray,$ToArray,$FinalValue,$Note)
			 }*/
		}
		else
		{
			$rowerrormsg.='<br>Balance Does Not Match';
			$this->m_objUtility->logGenerator($errorfile,$rowCnt,$rowerrormsg,'E');
			return false;
		}
		 
  }
  
  public function callJournalMethod($Data,$errorfile,$rowCnt)
  {
	   $rowerrormsg = '';
	   if($this->m_bShowTrace)
	     {
		  	 echo '<br>*********INSIDE callJournalMethod FUNCTION ***************<br>';
			 var_dump($Data);				 
	     }
		 
		$msg='';
		$PreviousMsg="";
		$PreviousCounter = 0;
		$TDSVNo=0;
		$IsCallUpdtCnt  = 1;
		$TDSVNo=0;
		$IsSubmit = 1;
		$IsSubmit = 1;
		$arData = array();
		$UpdateInvoice=false;
		$VoucherNumber = $Data['VoucherNumber'];
		$Note = $Data['Description'];
		$VoucherDate = $Data['Date'];
		$byallLedger  = $Data['ByLedgers'];
		$toallLedger = $Data['ToLedgers'];
		$Vouchertype = $Data['VoucherType'];
		$byLedgersKey = array_keys($byallLedger);
		$toLedgersKey = array_keys($toallLedger);
		$allledger=array_merge($byallLedger,$toallLedger);
		/*echo '<br>By All Ledgers : ';
		var_dump($byallLedger);
		echo '<br>To All Ledgers : ';
		
		var_dump($toallLedger);*/
		
		//echo '<br>Size of All Ledgers : '.sizeof($allledger);
		$beginDate = $_SESSION['default_year_start_date'];
		$endDate = $_SESSION['default_year_end_date'];
		
		 $GUID = $Data['GUID'];
		 $ChequeNumber = $this->getCheckNumber($Data['Description']);	
		
		 $ByLedgersHeadAndAmount = $Data['ToLedgers'];
		 $ToLedgersHeadAndAmount=$Data['ByLedgers'];
		 
		 if(in_array($GUID,$this->GUIDArray))
		 {
			 $GUIDIndex = array_search($GUID,$this->GUIDArray);
			 $VoucherNo = $this->VoucherArray[$GUIDIndex];
			 $rowerrormsg .= "Transaction already exits in ".$Vouchertype." with voucher Number ".$VoucherNo;
		 }
		
		//Date Vallidation 
		if($VoucherDate == '' || $VoucherDate == '0' || $VoucherDate == '0000-00-00' || $VoucherDate == '0000-00-00') 
		{
			$rowerrormsg .= "<br>Voucher Date Can Not be 0 or empty";
		}
		else if($this->m_objUtility->check_in_range($beginDate, $endDate, $VoucherDate) == false)
		{
			$rowerrormsg .= "<br>Voucher Date &lt;".$VoucherDate."&gt; Not In Range. It Should Be Between ".$beginDate." and ".$endDate;
		}
		$LedgerErrorLogString = '';
		$arSubData = array();
		$cnt=0;	
		//var_dump($byallLedger);
		
		
		$ListOfCashLedger = $this->m_objUtility->GetBankLedger($_SESSION['default_cash_account']);
		$ListOfBankLedger = $this->m_objUtility->GetBankLedger($_SESSION['default_bank_account']);
		$CashLedgers = array();
		$PayerBank='';
			
			$ListOfAccounts = array_merge($ListOfBankLedger,$ListOfCashLedger);
			$byLedgers=array();
			$toLedger=array();
			$flagPayment;
			$flagReceipt;
			
			
		$AccountsArray = array_column($ListOfAccounts, 'id');
		for($i = 0 ; $i < count($byallLedger) ; $i++)
		{	$cnt++;
			$arSubData['byto'] = 'BY';
				//echo '<br>By Ledger Key : '.$byLedgersKey[$i];
				if(in_array($byLedgersKey[$i],$this->UnitMappingKeys))
				{
					$ledgerIndex = array_search($byLedgersKey[$i],$this->UnitMappingKeys);
					//echo '<br>Ledger Index : '.$ledgerIndex;
					$arSubData['To'] = $this->mappingLedgerIDs[$ledgerIndex];
				}
				else
				{
					$arSubData['To'] = $this->getLedgerID($byLedgersKey[$i]);	
					//echo '<br>'.$arSubData['To'];
				}
				
				if(in_array($arSubData['To'],$AccountsArray))
				{
					//echo '<br>Journal Voucher : By Ledgers';
					$flagReceipt='Receipt';
				}
				$arSubData['Debit'] = $byallLedger[$byLedgersKey[$i]];
				$arSubData['Credit'] = '';
				
				
				if($arSubData['To'] == '' || $arSubData['To'] == 0)
				{
					$rowerrormsg .= ' Ledger <b>&lt;'.$byLedgersKey[$i].'&gt;</b> not found';
				}
				else
				{
					$LedgerErrorLogString .= " Ledger &lt;".$byLedgersKey[$i]."&gt; Amount &lt;".$arSubData['Debit']."&gt;";
					$arData[$i] = $arSubData;	
				}
			}
			$j=0;
			//echo '<br>Count Of All Ledgers : '.count($allledger);
  			for($i = 0 ;  $i < count($toallLedger) ; $i++)
  			{
				
				$arSubData['byto'] = 'TO';
				
				if(in_array($toLedgersKey[$i],$this->UnitMappingKeys))
				{
					$ledgerIndex = array_search($toLedgersKey[$i],$this->UnitMappingKeys);
					
					$arSubData['To'] = $this->mappingLedgerIDs[$ledgerIndex];
				}
				else
				{
					$arSubData['To'] = $this->getLedgerID($toLedgersKey[$i]);	
				}
				
				$arSubData['Debit'] = '';
				$arSubData['Credit'] = $toallLedger[$toLedgersKey[$i]];
				
				/*if(in_array($arSubData['To'],$AccountsArray))
				{
					//echo '<br>Journal Voucher : To Ledgers';
					
					$flagPayment='Payment';
				}*/
				
				if($arSubData['To'] == '' || $arSubData['To'] == 0)
				{
					//echo '<br> Ledger Key '.$toLedgersKey[$i];
					$rowerrormsg .= 'Ledger <b>&lt;'.$toLedgersKey[$i].'&gt;</b> not found';
				}
				else
				{
					$LedgerErrorLogString .= " Ledger &lt;".$toLedgersKey[$i]." &gt; Amount  &lt;".$arSubData['Credit']."&gt;";
					$arData[$cnt] = $arSubData;	
				}
				
			
			$cnt++;
		}
		
		if($rowerrormsg <> ''  || $rowerrormsg <> 0 )
		{
			$rowerrormsg .= "<br>Row Not Inserted";
			$this->m_objUtility->logGenerator($errorfile,$rowCnt,$rowerrormsg,'E');
			return false;
		}
		
		if($this->m_bShowTrace)
		 {
			 echo '<br>Final Data Sending to createNewVoucher function<br>';
			 echo '<pre>';
			 print_r($arData);
			 echo '</pre>';	
			 echo '<br>PreviousMsg : '.$PreviousMsg.'<br>PreviousCounter : '.$PreviousCounter.'<br>UpdateInvoice : '.$UpdateInvoice.'<br>TDSVNo : '.$TDSVNo.'<br> IsSubmit : '.$IsSubmit.'
			 <br>VoucherDate : '.$VoucherDate.'<br>is_invoice : '.$is_invoice.'<br>IGST_Amount : '.$IGST_Amount.'<br>CGST_Amount : '.$CGST_Amount.'<br>CGST_Amount : '.$SGST_Amount.'
			 <br>Cess_Amount : '.$Cess_Amount.'<br>NewInvoiceNo : '.$NewInvoiceNo.'<br>InvoiceStatusID : '.$InvoiceStatusID.'<br>Note : '.$Note.'
			 <br>RefNo : '.$RefNo.'<br>RefTableID : '.$RefTableID.'<br>VoucherNumber : '.$VoucherNumber.'<br>IsCallUpdtCnt : '.$IsCallUpdtCnt.'<br>ExistVoucher : '.$ExistVoucher;			 
		 }
		 
		 	 $rowerrormsg .= 'VoucherDate : <'.$VoucherDate.'> VoucherNumber : <'.$VoucherNumber.'> '.$LedgerErrorLogString.' Note : &lt;'.$Note.'&gt;';
		 	
			/*if($flagPayment=='Payment' && $flagReceipt=='Receipt')
			{
				//echo '<br>Journal Voucher Type : Contra';
				$result=$this->callPaymentMethod($Data,$errorfile,$rowCnt);
				
				if($result==1)
				{
					echo '<br>Success Contra';
					return true;
				}
				else
				{
					echo '<br>Failed Contra';
					return false;
				}
			}
			elseif($flagPayment=='Payment')
			{
				$result=$this->callPaymentMethod($Data,$errorfile,$rowCnt);
				
				if($result==1)
				{
					echo '<br>Success Payment';
					return true;
				}
				else
				{
					echo '<br>Failed Payment';
					return false;
				}
				
			}
			elseif($flagReceipt=='Receipt')
			{
				$result=$this->callReceiptMethod2($Data,$errorfile,$rowCnt);
				
				if($result==1)
				{
					echo '<br>Success Receipt';
					return true;
				}
				else
				{
					echo '<br>Failed Receipt';
					return false;
				}
			}
			else*/
			{
				//echo '<br>GUID : '.$GUID;
		 		$Result = $this->obj_createvoucher->createNewVoucher_WithGUID($PreviousMsg,$PreviousCounter,$UpdateInvoice,$TDSVNo,$IsSubmit,$VoucherDate,$arData,$is_invoice,$IGST_Amount,$CGST_Amount,$SGST_Amount,$Cess_Amount,$NewInvoiceNo,$InvoiceStatusID,$Note,$ExistVoucher,$RefNo,$RefTableID,$VoucherNumber,$IsCallUpdtCnt,$GUID);
			}
			
			if($Result == 'Update')
			{
				$rowerrormsg .='<br>Row Inserted Successfully';
				$this->m_objUtility->logGenerator($errorfile,$rowCnt,$rowerrormsg,'I');
				return true;
			}
	}
	
	
  public function callDebitCreditMethod($Data,$NoteType,$errorfile,$rowCnt)
  {
	  	$rowerrormsg = '';
	    if($this->m_bShowTrace)
	     {
			 //$Detail,$UnitID,$bill_date,$BillType,$NoteType,$BillNote,$IseditModeSet,$CreditDebitEditable_ID,$IsCallUpdtCnt = 1,$ExternalCounter;
		  	 echo '<br>*********INSIDE callDebitCreditMethod FUNCTION ***************<br>';
			 var_dump($Data);			
			 //return;	 
	     }
		 //Assing all the required value for Create Credit or Debit Note
		 $Detail = array();
		 $bill_date = $Data['Date'];
		 $BillNote = $Data['Description'];
		 $BillType = 0;
		 $IseditModeSet = false;
		 $CreditDebitEditable_ID = 0;
		 $IsCallUpdtCnt = 1;
		 $ExternalCounter = $Data['VoucherNumber'];
		 $Vouchertype = $Data['VoucherType'];
		 $beginDate = $_SESSION['default_year_start_date'];
		 $endDate = $_SESSION['default_year_end_date'];
		 $GUID = $Data['GUID'];
		 
		 if($NoteType == DEBIT_NOTE)
		 {
			$UnitName = array_keys($Data['ByLedgers']);
			$LedgerHeadandAmount =  $Data['ToLedgers'];
			$UnitID =$this->getLedgerID($UnitName[0]);
		 }
		 else
		 {
			$UnitName = array_keys($Data['ToLedgers']);
			$LedgerHeadandAmount = $Data['ByLedgers'];
			$UnitID =$this->getLedgerID($UnitName[0]);
		 }
		 
		 //Check Whether same transaction present or not
		 
		 if(in_array($GUID,$this->GUIDArray))
		 {
			 $GUIDIndex = array_search($GUID,$this->GUIDArray);
			 $VoucherNo = $this->VoucherArray[$GUIDIndex];
			 $rowerrormsg .= "Transaction already exits in ".$Vouchertype." with voucher Number ".$VoucherNo;
		 }
		
		 //Validation for date
		 if($this->m_objUtility->check_in_range($beginDate, $endDate, $bill_date) == false)
		 {
			 $rowerrormsg .= "<br>Voucher Date &lt;".$VoucherDate."&gt;Not In Range. It Should Be Between ".$beginDate." and ".$endDate;
		 }
		 
		 // First It Check Unit Id in UnitMappingKeys whether it's map key exits or not
		 if(in_array($UnitName[0],$this->UnitMappingKeys))
		 {
			$ledgerIndex = array_search($UnitName[0],$this->UnitMappingKeys);
			$UnitID = $this->mappingLedgerIDs[$ledgerIndex];
		 }
		 else
		 {
			 // If key DoesNot Exits then it will take value from Ledger
			 $UnitID = $this->getLedgerID($UnitName[0]);	 
		 }
		 
		 if($UnitID == 0 || $UnitID == '')
		 {
			 $rowerrormsg .= " Unit ".$UnitName[0]." not found";
		 }
		 $Amount = (int)$Data['ByLedgers'][$UnitName[0]];
		 $LedgerName = array_keys($LedgerHeadandAmount);
		 
		 //Making Array of ledgers
		 for($i = 0 ; $i < sizeof($LedgerHeadandAmount); $i++)
		 {
			 $Detail[$i]['Head'] = $this->getLedgerID($LedgerName[$i]);
			 $Detail[$i]['Amt'] = $LedgerHeadandAmount[$LedgerName[$i]] ;
			 $Detail[$i]['invoicetaxable'] = 0;
			
			 if(empty($Detail[$i]['Head']))
			 {
			 	 $ledgerIndex = array_search($LedgerName[$i],$this->UnitMappingKeys);
			 	 $Detail[$i]['Head'] = $this->mappingLedgerIDs[$ledgerIndex];
				 //$rowerrormsg .= "Invalid Debit Type Entry found for voucher no. ".$ExternalCounter;	 
			 }
			 
		 }
		 
		 //it will check data does not contain any errot all. If yes then It does not execute the code the print error
		 if($rowerrormsg <> '' || $rowerrormsg <> 0)
		 {
			$this->m_objUtility->logGenerator($errorfile,$rowCnt,$rowerrormsg,'E');
			return false;	 
		 }
		  
		 if($this->m_bShowTrace)
	     {
			 echo '<br>Bill Details ';
			 echo '<pre>';
			 print_r($Detail);
			 echo '</pre>';	
		}
	 	$flag= 1;
	 	//echo 'Counter : '.$ExternalCounter;
	 	//If no Error is data then it will call AddCreditDebitNote funtion 
		 if($rowerrormsg == '')
		 {
			
			$Result = $this->obj_genbill->AddCreditDebitNote($Detail,$UnitID,$bill_date,$BillType,$NoteType,$BillNote,$IseditModeSet,$CreditDebitEditable_ID,$IsCallUpdtCnt,$ExternalCounter,$flag,$GUID);
			//echo '<br>After AddCreditDebitNote';
			//var_dump($Result);
			 if($Result == true)
			 {
				 
				 $rowerrormsg .=  ' UnitID :  &lt;'.$UnitName[0].'&gt; Bill Date : &lt; '.$bill_date.' &gt;  Amount : &lt; '.abs($Amount).' &gt; Voucher Type :&lt;'.$Vouchertype.'&gt; BillNote : &lt;'.$BillNote.'&gt';
				 $rowerrormsg.='<br>Row Inserted Successfully';
				 //echo '<br>Row Msg : '.$rowerrormsg;   			  
				 $this->m_objUtility->logGenerator($errorfile,$rowCnt,$rowerrormsg,'I');
				 return true;
			 }	 
		 }
  }	

  public function getCheckNumber($transaction_comment)
  {
	  $NumberString =  preg_replace('/[^0-9]/',' ',$transaction_comment);
	  $ChequeNumber = explode(' ',trim($NumberString,' '));
	  return $ChequeNumber[0];
  }
	
  public function getLedgerID($LedgerName)
  {
	  //When first time this function call then it make query other wise it will return value from it's pre store array (else part)
	  if(sizeof($this->LedgerDetailsArray) == 0)
	  {
		 $result = $this->m_dbConn->select("Select id,ledger_name from ledger");	 
		 
		 $this->LedgerDetailsArray = $result;
		 $this->LedgerNameArray = array_column($this->LedgerDetailsArray, 'ledger_name');
		
		 if(in_array($LedgerName,$this->LedgerNameArray))
		 {
			$index = array_search($LedgerName,$this->LedgerNameArray);
			return $this->LedgerDetailsArray[$index]['id'];
		} 
	  }
	  else
	  {
		 if(in_array($LedgerName,$this->LedgerNameArray))
		 {
			$index = array_search($LedgerName,$this->LedgerNameArray);
			return $this->LedgerDetailsArray[$index]['id'];
		 } 
	  }
  }
  
  public function getCategoryID($categoryName,$makeQuery = false)
  {
	  //When first time this function call then it make query or $maleQuery Value is true other wise it will return value from it's pre store array (else part)
	  if(sizeof($this->CategoryDetailsArray) == 0 || $makeQuery == true)
	  {
		 $result = $this->m_dbConn->select("SELECT category_id,group_id,parentcategory_id,category_name from account_category");
		 $this->CategoryDetailsArray = $result;
		 $this->CategoryNameArray = array_column($this->CategoryDetailsArray, 'category_name');
		 $this->CategoryNameArray = array_map('strtolower', $this->CategoryNameArray);
		 
		 if(in_array($categoryName,$this->CategoryNameArray))
		 {
			$index = array_search($categoryName,$this->CategoryNameArray);
			return $this->CategoryDetailsArray[$index];
		 } 
	  }
	  else
	  {
		 if(in_array($categoryName,$this->CategoryNameArray))
		 {
			$index = array_search($categoryName,$this->CategoryNameArray);
			return $this->CategoryDetailsArray[$index];
		 } 
	  }
  }
  
  public function insertCategory($group_id,$parentcategory_id = 1,$category_name)
  {
	  $timestamp = getCurrentTimeStamp();
	  $insertQuery = "insert into account_category (`group_id`,`parentcategory_id`,`category_name`,`enteredby`,`timestamp`,`status`) values ('".$group_id."','".$parentcategory_id."','".$category_name."','".$_SESSION['login_id']."','".$timestamp['DateTime']."','Y')";	
	  $Result = $this->m_dbConn->insert($insertQuery);
	  return $Result;
  }
  
  public function FetchUnitMappingKey()
  {
	 $Result = $this->m_dbConn->select("Select unit,extn_ledger_name from member_main");  
	 $this->UnitMappingKeys = array_column($Result, 'extn_ledger_name');
	 $this->mappingLedgerIDs = array_column($Result, 'unit');
  }
  
  public function FetchGUID()
  {
	 $Result = $this->m_dbConn->select("Select GUID,ExternalCounter from voucher where GUID <> ''");  
	 $this->GUIDArray = array_column($Result, 'GUID');
	 $this->VoucherArray = array_column($Result, 'ExternalCounter');
  }
  
  public function returnGruopName($groupID)
  {
	  if($groupID == LIABILITY)
	  {
		  return 'Liability';
	  }
	  else if($groupID == ASSET)
	  {
		  return 'Asset';
	  }
	  else if($groupID == INCOME)
	  {
		  return 'Income';
	  }
	  else if($groupID == EXPENSE)
	  {
		  return 'Expense';
	  }
  }
  
  
  //*** This function import Category
  public function importDayBookCategoryData($CategoryData)
  {
	 //Making error file here 
	 $Foldername = $this->obj_fetch->objSocietyDetails->sSocietyCode;

	 if (!file_exists('../logs/import_log/'.$Foldername)) 
	 {
		 mkdir('../logs/import_log/'.$Foldername, 0777, true);
	 }

	$this->errorfile_name = '../logs/import_log/'.$Foldername.'/import_category_errorlog_'.date("d.m.Y").'_'.rand().'.html';
	 
	$errorfile = fopen($this->errorfile_name, "a");
	$errormsg="[Importing  Category Details] Created At [".date("d.m.Y")."]";
	$this->m_objUtility->logGenerator($errorfile,'start',$errormsg);  
	// assing initail value for insert category
	$rowCnt = 1;
	$InsertedRow = 0;
	
	$SuspenceAccount_Name =  strtolower(SUSPENSE_AC);
	$getSuspense_id = $this->getCategoryID($SuspenceAccount_Name,false);
	//var_dump($getSuspense_id);
	if(empty($getSuspense_id))
	{
	$SuspenceAccount = $this->insertCategory(LIABILITY,1,SUSPENSE_AC);
	$errormsg = "group &lt;Liability&gt; Parent &lt;-&gt;  Category Name &lt;Suspense a/c&gt; ";
	if($SuspenceAccount <> 0)
	{
		$InsertedRow++;
		$errormsg .= "<br>Row Inserted Successfully";
		$this->m_objUtility->logGenerator($errorfile,$rowCnt,$errormsg,'I');
	}
	else
	{
		$errormsg .= "<br>Row Not Inserted";
		$this->m_objUtility->logGenerator($errorfile,$rowCnt,$errormsg,'E');
	}
	}
	else
	{
		$errormsg = "category &lt;".SUSPENSE_AC."&gt already present";
		$errormsg .= "<br>Row Not Inserted";
		$this->m_objUtility->logGenerator($errorfile,$rowCnt,$errormsg,'E');
	}
	
	
	foreach($CategoryData as $data)
	 {
		$rowCnt++;
		if($this->m_bShowTrace)
		{
			 echo '<br>==============================================LINE : '.$rowCnt.' IS '.strtoupper($data['Category']).'====================================<br>';
			 var_dump($data);
		}
		
		$Category_Name = strtolower($data['Category']);
		$Parent_Name = strtolower($data['Parent']);
		
		//In Xml There are several category which does not contains parent so this all the primary category
		if($data['Parent'] == '' || $data['Parent'] == '0')
		{	
			
			$CategoryAlreadyPresent = $this->getCategoryID($Category_Name);
			
			if(sizeof($CategoryAlreadyPresent) == 0)
			{
				$NameofCategories = array_keys($this->CategoryGroupMap);
				
				$NameofCategories = array_map('strtolower', $NameofCategories);
			
				if(in_array($Category_Name,$NameofCategories))
				{
					//In CategoryGroupMap we assinged category group
					$group_id = $this->CategoryGroupMap[$data['Category']];
				}
				else
				{
					$group_id = $this->CategoryGroupMap[SUSPENSE_AC];
				}
					$Result = $this->insertCategory($group_id,1,$data['Category']);
					$errormsg = "group &lt;".$this->returnGruopName($group_id)."&gt; Parent &lt;-&gt;  Category Name &lt;".$data['Category']."&gt; ";
					if($Result <> 0)
					{
						$InsertedRow++;
						$errormsg .= "<br>Row Inserted Successfully";
						$this->m_objUtility->logGenerator($errorfile,$rowCnt,$errormsg,'I');
					}
					else
					{
						$errormsg .= "<br>Row Not Inserted";
						$this->m_objUtility->logGenerator($errorfile,$rowCnt,$errormsg,'E');
					}
				
			}
			else if(sizeof($CategoryAlreadyPresent) <> 0)
			{
				$errormsg = "category &lt;".$data['Category']."&gt already present";
				$errormsg .= "<br>Row Not Inserted";
				$this->m_objUtility->logGenerator($errorfile,$rowCnt,$errormsg,'E');
			}
		}
		//Here those have parent category will execute below code
		else if($data['Parent'] <> '' && $data['Parent'] <> '0')
		{
			$CategoryAlreadyPresent = $this->getCategoryID($Category_Name,true);	
			
			if(sizeof($CategoryAlreadyPresent) == 0)
			{
				$CategoryAlreadyPresent = $this->getCategoryID($Parent_Name);
				
				if(sizeof($CategoryAlreadyPresent) == 0)
				{
					//Parent Category not Found so we put it on suspense account
					$CategoryAlreadyPresent = $this->getCategoryID($SuspenceAccount_Name);
					//var_dump($CategoryAlreadyPresent);
				}
				
				if(sizeof($CategoryAlreadyPresent) > 0)
				{
					$group_id = $CategoryAlreadyPresent['group_id'];
					$parentcategory_id = $CategoryAlreadyPresent['category_id'];
					$Result = $this->insertCategory($group_id,$parentcategory_id,$data['Category']);
					$errormsg = "group &lt;".$this->returnGruopName($group_id)."&gt; Parent &lt;-&gt;  Category Name &lt;".$data['Category']."&gt; ";
					
					if($Result <> 0)
					{
						$InsertedRow++;
						$errormsg .= "<br>Row Inserted Successfully";
						$this->m_objUtility->logGenerator($errorfile,$rowCnt,$errormsg,'I');
					}
					else
					{
						$errormsg .= "<br>Row Not Inserted";
						$this->m_objUtility->logGenerator($errorfile,$rowCnt,$errormsg,'E');
					}
				}
				else
				{
					$errormsg = "Parent Category not present for category &lt;".$data['Category']."&gt";
					$errormsg .= "<br>Row Not Inserted";
					$this->m_objUtility->logGenerator($errorfile,$rowCnt,$errormsg,'W');
				}	
			}
			else if(sizeof($CategoryAlreadyPresent) > 0)
			{
				$errormsg = "category &lt;".$data['Category']."&gt already present";
				$errormsg .= "<br>Row Not Inserted";
				$this->m_objUtility->logGenerator($errorfile,$rowCnt,$errormsg,'E');
			}
		}	 
	 }
	   
	$errormsg="[Importing Category Details] End At [".date("d.m.Y")."]";
	$this->m_objUtility->logGenerator($errorfile,'End',$errormsg);  
	
	$rowCnt = $rowCnt;
	$NoRowFailed = $rowCnt - $InsertedRow;
	
	$errormsg ="<br><br><br> Total Number of row for category : <b> ".$rowCnt."</b></br>";
	$errormsg .="<br> Total Number of row inserted :<b> ".$InsertedRow."</b><br>";
	$errormsg .="<br> Total Number of row failed to inserted : <b>".$NoRowFailed."</b><br><br>"; 
	$this->m_objUtility->logGenerator($errorfile,'',$errormsg);
	 
  }
  
  //***** This function Import Ledgers********
  public function importDayBookLedgerData($LedgerData)
  {
	$Foldername = $this->obj_fetch->objSocietyDetails->sSocietyCode;

	if (!file_exists('../logs/import_log/'.$Foldername)) 
	{
		mkdir('../logs/import_log/'.$Foldername, 0777, true);
	}
	  //Creating Opening Error Log File 
	  $this->errorfile_name = '../logs/import_log/'.$Foldername.'/daybook_master_import_errorlog_'.date("d.m.Y").'_'.rand().'.html';
  	  
	  $errorfile = fopen($this->errorfile_name, "a");
	  
	  /*$errormsg = "**********************************************************************************************************************************************************<br>";
	  $this->m_objUtility->logGenerator($errorfile,'',$errormsg);
	  */
	  $errormsg="[Importing DayBook Master Ledger Details] Created At [".date("d.m.Y")."]";
	  $this->m_objUtility->logGenerator($errorfile,'start',$errormsg);
	  
	  //Fetching Opening date for setting opening balance
	  $society_id = $_SESSION['society_id'];
	  $YearforOpeningDate =  - 1;
	  $EndingDateResult = $this->m_dbConn->select("Select EndingDate from year where YearID = '".$_SESSION['society_creation_yearid']."'");
	  $Date = $EndingDateResult[0]['EndingDate'];
	  $Date = date('Y-m-d', strtotime($Date.' -1 year'));
	  // I=Setting Initail value for import 
	  $isImportSuccess = false;
	  $rowCnt = 0;
	  $InsertedRow = 0;
	  
	  //This function fetch all unit Mapping Key from Unit Table
	  $this->FetchUnitMappingKey();
	
	  foreach ($LedgerData as $data)
	  {
		  
		 $rowCnt++;
		 if($this->m_bShowTrace)
		 {
			 echo '<br>==============================================LINE : '.$rowCnt.' IS '.strtoupper($data['LedgerName']).'====================================<br>';
			 var_dump($data);
		 }
		
		$Category_Name = strtolower($data['Category']);
		$Parent_Name = strtolower($data['Parent']);
		//Here we check whether Particular LedgerName is Unit Number 
		if(in_array($data['LedgerName'],$this->UnitMappingKeys))
		{
			// here We set opening balance for unit is exits and skip insert query because unit already in created
			$ledgerIndex = array_search($data['LedgerName'],$this->UnitMappingKeys);
			$LedgerID = $this->mappingLedgerIDs[$ledgerIndex];
			
			$opening_balance = $data['OpeningBalance'];
			//openingtype issue
			if($opening_balance>0)
			{
			
			$this->obj_register->UpdateRegister($LedgerID,0, TRANSACTION_CREDIT,abs($opening_balance));	
			
			}
			else
			{
				
			$this->obj_register->UpdateRegister($LedgerID,0, TRANSACTION_DEBIT, abs($opening_balance));	
			
			}
			$update_query="Update ledger set opening_balance = '".abs($opening_balance)."' Where id = '".$LedgerID."'";
			$updateOpeningBalance = $this->m_dbConn->update("Update ledger set opening_balance = '".abs($opening_balance)."' Where id = '".$LedgerID."'");
			
			$errormsg = "Ledger Name: &lt;".$data['LedgerName']."&gt; already present OpeningBalance set : &lt;".abs($opening_balance)."&gt; ";
			$this->m_objUtility->logGenerator($errorfile,$rowCnt,$errormsg,"W");
			continue;
		}
		 
		//If ledger Id is not Present then it will insert and below code execute 
		$ledgerID = $this->getLedgerID($data['LedgerName']);
		
		if($ledgerID == '' || $ledgerID == 0)
		{ 
			//Check Category is present or not
			$CategoryDetails = $this->getCategoryID($Parent_Name);
			
			if(sizeof($CategoryDetails) == 0)
			{
				$CategoryDetails = $this->getCategoryID(strtolower(SUSPENSE_AC));	
			}
			
			
			if(sizeof($CategoryDetails) <> 0)
			{
				//asssinging all the value for ledger inser query
				$category_id = $CategoryDetails['category_id'];
				$show_in_bill_flag = 0;	
				$ledger_name = $data['LedgerName'];
				$taxable_flag = 0;
				$note = '';
				$opening_balance = 0;
				
				if($data['OpeningBalance'] <> 0)
				{
					$opening_balance = $data['OpeningBalance'];
				}
				
				if($CategoryDetails['group_id'] == LIABILITY)
				{
					if($opening_balance > 0)
					{
						$account_type=1;
					}
					else
					{
						$account_type=2;
					}
					$insert_ledger="insert into `ledger`(society_id,categoryid,show_in_bill,ledger_name,taxable,sale,purchase,income,expense,payment,receipt,opening_type,opening_balance,note,`opening_date`) values('".$society_id."','".$category_id."','".$show_in_bill_flag."','".$this->m_dbConn->escapeString($ledger_name)."','".$taxable_flag."',0,0,0,1,1,1,'".$account_type."','".abs($opening_balance)."','".$note."','".getDBFormatDate($Date)."')";
					$errormsg= "Ledger Name: &lt;".$ledger_name."&gt; : Type: &lt; Liability &gt; openingBalance :  &lt;".abs($opening_balance)."&gt;  openingDate :  &lt;".$Date."&gt;";
					$isImportSuccess = true;
					
				}
				else if($CategoryDetails['group_id'] == ASSET)
				{
					$payment_flag = 0;
					if($opening_balance > 0)
					{
						$account_type=1;
					}
					else
					{
						$account_type=2;
					}
					if($category_id == $_SESSION['default_cash_account'])
					{
						$payment_flag = 1;
					}
					$insert_ledger="insert into `ledger`(society_id,categoryid,show_in_bill,ledger_name,taxable,sale,purchase,income,expense,payment,receipt,opening_type,opening_balance,note,`opening_date`) values('".$society_id."','".$category_id."','".$show_in_bill_flag."','".$this->m_dbConn->escapeString($ledger_name)."','".$taxable_flag."',1,1,1,0,$payment_flag,1,'$account_type','".abs($opening_balance)."','$note','".getDBFormatDate($Date)."')";
					$errormsg= "Ledger Name: &lt;".$ledger_name."&gt; :: Type: &lt; Asset &gt; openingBalance :  &lt;".abs($opening_balance)."&gt;  openingDate :  &lt;".$Date."&gt;";
					$isImportSuccess = true;
				}
				else if($CategoryDetails['group_id'] == INCOME)
				{
					$insert_ledger="insert into `ledger`(society_id,categoryid,show_in_bill,ledger_name,taxable,sale,purchase,income,expense,payment,receipt,opening_type,opening_balance,note,`opening_date`) values('".$society_id."','".$category_id."','".$show_in_bill_flag."','".$this->m_dbConn->escapeString($ledger_name)."',0,1,0,1,0,0,1,'$opening_type','".abs($opening_balance)."','$note','".getDBFormatDate($Date)."')";
					$errormsg= "Ledger Name: &lt;".$ledger_name."&gt; :: Type: &lt; Income &gt; openingBalance :  &lt;".abs($opening_balance)."&gt;  openingDate :  &lt;".$Date."&gt;";
					$isImportSuccess = true;
				}
				else if($CategoryDetails['group_id'] == EXPENSE)
				{
					$insert_ledger="insert into `ledger`(society_id,categoryid,show_in_bill,ledger_name,taxable,sale,purchase,income,expense,payment,receipt,opening_type,opening_balance,note,`opening_date`) values('".$society_id."','".$category_id."','".$show_in_bill_flag."','".$this->m_dbConn->escapeString($ledger_name)."',0,0,0,0,1,1,0,'$opening_type','".abs($opening_balance)."','$note','".getDBFormatDate($Date)."')";
					$errormsg= "Ledger Name: &lt;".$ledger_name."&gt; :: Type: &lt; Expense &gt; openingBalance :  &lt;".abs($opening_balance)."&gt;  openingDate :  &lt;".$Date."&gt;";
					$isImportSuccess = true;
					
				}
				$NewLedgerID=$this->m_dbConn->insert($insert_ledger);
				$errormsg .="<br>Row Inserted Successfully";
				$this->m_objUtility->logGenerator($errorfile,$rowCnt,$errormsg,"I");
				
				if($isImportSuccess == true)
				{
					$InsertedRow++;
				}
				
										
				if($CategoryDetails['group_id'] == LIABILITY)
				{
					//opening balance not equal to 0 because no need to insert with 0 amount in table
					if($opening_balance <> 0)
					{
						if($opening_balance > 0)
						{
							$insertLiability = $this->obj_register->SetLiabilityRegister(getDBFormatDate($Date), $NewLedgerID, 0, 0, TRANSACTION_CREDIT, abs($opening_balance), 1);
						}
						else
						{
							$insertLiability = $this->obj_register->SetLiabilityRegister(getDBFormatDate($Date), $NewLedgerID, 0, 0, TRANSACTION_DEBIT, abs($opening_balance), 1);
						}
					}
				}
				else if($CategoryDetails['category_id'] == $_SESSION['default_bank_account'] || $CategoryDetails['category_id'] == $_SESSION['default_cash_account'])
				{
					//If the Bank category is set on default page then only this code will execute
					$insertBank = $this->obj_register->SetBankRegister(getDBFormatDate($Date), $NewLedgerID, 0, 0, TRANSACTION_RECEIVED_AMOUNT, abs($opening_balance), 0, 0, 1);
					$insertBankMaster = $insert_query="insert into bank_master (`BankID`, `BankName`) values ('" . $NewLedgerID . "', '".$this->m_dbConn->escapeString($ledger_name)."')";
					$sqlInsertResult = $this->m_dbConn->insert($insertBankMaster);
				}
				else if($CategoryDetails['group_id'] == ASSET)
				{
					//Setting Opening balance value in Asset Table
					if($opening_balance > 0)
					{
						$insertAsset = $this->obj_register->SetAssetRegister(getDBFormatDate($Date), $NewLedgerID, 0, 0, TRANSACTION_CREDIT, abs($opening_balance), 1);
					}
					else
					{
						$insertAsset = $this->obj_register->SetAssetRegister(getDBFormatDate($Date), $NewLedgerID, 0, 0, TRANSACTION_DEBIT, abs($opening_balance), 1);					//echo $insertAsset;	
					}
				}	
			}
			else
			{
				$errormsg = "Category not present for ledger &lt;".$data['LedgerName']."&gt";
				$errormsg .= "<br>Row Not Inserted";
				$this->m_objUtility->logGenerator($errorfile,$rowCnt,$errormsg,'E');
			}
		}
		else if($ledgerID <> 0 && $ledgerID <> '')
		{
			$errormsg = "Ledger &lt;".$data['LedgerName']."&gt already present";
			$errormsg .= "<br>Row Not Inserted";
			$this->m_objUtility->logGenerator($errorfile,$rowCnt,$errormsg,'E');
		}
		
	  }
		$errormsg="[Importing DayBook Master Ledger Details] Created At [".date("d.m.Y")."]";
		$this->m_objUtility->logGenerator($errorfile,'End',$errormsg);
		
		$NoRowFailed = $rowCnt - $InsertedRow;
		$errormsg ="<br><br><br> Total Number of row for Ledger : <b> ".$rowCnt."</b>";
		$errormsg .="<br> Total Number of row inserted : <b> ".$InsertedRow."</b>";
		$errormsg .="<br> Total Number of row failed to inserted : <b> ".$NoRowFailed."</b></br>"; 
		$this->m_objUtility->logGenerator($errorfile,'',$errormsg);
	}
  
} 
?> 
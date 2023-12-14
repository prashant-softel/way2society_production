<?php
//include_once("include/dbop.class.php");

include_once("register.class.php");
include_once("dbconst.class.php");
include_once("latestcount.class.php");
include_once("utility.class.php");
include_once("voucher.class.php");
include_once("changelog.class.php");
include_once("PaymentDetails.class.php");
include_once("ChequeDetails.class.php");


class createVoucher 
{
	public $actionPage = "../createvoucher.php";
	public $m_dbConn;
	public $m_voucher;
	public $m_register;
	public $m_latestcount;
	public $m_objUtility;
	public $changeLog;
	public $obj_PaymentDetails;
	public $obj_ChequeDetails;
	public $EDITEntryTracker;
	public $request_mode;

	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->m_voucher = new voucher($dbConn);
		$this->obj_PaymentDetails = new PaymentDetails($dbConn);
		$this->obj_ChequeDetails = new ChequeDetails($dbConn);
		$this->m_latestcount = new latestCount($dbConn);
		$this->m_register = new regiser($dbConn);
		$this->m_objUtility = new utility($dbConn);
		$this->changeLog = new changeLog($dbConn);
	}			
	
	public function startProcess()
	{
		
		$msg='';
		$PreviousMsg="";
		$PreviousCounter = 0;
		$UpdateInvoice=false;
		$this->request_mode = $_POST['mode'];

		
		if(isset($_POST['submit']) && $_POST['submit']== 'Update')
	  	{
			if($_POST['Updatetable'] == TABLE_PAYMENT_DETAILS && $_POST['mode'] == EDIT) // If transaction is payment then it will call edit payment
			{
				return $this->update_payment_details(EDIT); // Passing Edit flag
			}
			else if($_POST['Updatetable'] == TABLE_CHEQUE_DETAILS)
			{
				$ChequeDate = $_POST['cheque_date'];
				$VoucherDate = $_POST['voucher_date'];
				$ChequeNumber = $_POST['ChequeNumber'];
				$maxrows = $_POST['maxrows'];
				$ExistVoucher=$_POST['Vno'];
				$BillType = $_POST['BillType'];
				$ByArray = array();
				$ToArray = array();
				$BankLedgers = array();
				$PayerAmount = 0;
				$ToTotal = 0;
				$PayerBankID = 0;
				$GUID = "";
				$ListOfCashLedger = $this->m_objUtility->GetBankLedger($_SESSION['default_cash_account']);
				$ListOfBankLedger = $this->m_objUtility->GetBankLedger($_SESSION['default_bank_account']);
				
				
				$ListOfAccounts = array_merge($ListOfBankLedger,$ListOfCashLedger);
				$BankLedgers = array_column($ListOfAccounts, 'id');
				$cnt = 0;	
				$IsbankPresent = false;	
				for($i=1;$i<=$_POST['maxrows'];$i++)
				{
					$arSubData = array();
					if($_POST['byto'.$i] == 'BY')
					{
						if($_POST['To'.$i] == 0 || empty($_POST['To'.$i]))
						{
							continue;
						}
						if(in_array($_POST['To'.$i],$BankLedgers))
						{
							$IsbankPresent = true;
							$PayerBankID = $_POST['To'.$i];
							$PayerAmount = $_POST['Debit'.$i];
						}
						$arSubData['Head'] = $_POST['To'.$i];
						$arSubData['Amt'] = $_POST['Debit'.$i];
						
						$ByArray[$cnt] = $arSubData;
						$cnt++;
					}
				}
				
				
				$cnt = 0;
				for($i=1;$i<=$_POST['maxrows'];$i++)
				{
					$arSubData = array();
					if($_POST['byto'.$i] == 'TO')
					{
						if($_POST['To'.$i] == 0 || empty($_POST['To'.$i]))
						{
							continue;
						}
						if(in_array($_POST['To'.$i],$BankLedgers))
						{
							$PayerBankID = $_POST['To'.$i];
							$PayerAmount = $_POST['Credit'.$i];
						}
						$arSubData['Head'] = $_POST['To'.$i];
						$arSubData['Amt'] = $_POST['Credit'.$i];
						$ToArray[$cnt] = $arSubData;
						$cnt++;
					}
				}
				
				$PaidBy = $ByArray[0]['Head'];	
				if($IsbankPresent == true)
				{
					$PaidBy = $ToArray[0]['Head'];	
				}
				
				$GetPriviousEntriesDetails = $this->m_dbConn->select("Select * from  chequeentrydetails where id = '".$_POST['RowID']."'"); 
				$PaidByPrev = $GetPriviousEntriesDetails[0]['PaidBy'];
				$PayerBankPre = $GetPriviousEntriesDetails[0]['PayerBank'];
				$PayerChequeBranchPrev = $GetPriviousEntriesDetails[0]['Comments'];
				$ChequeDetailsId = $_POST['RowID'];
				$DepositID = $GetPriviousEntriesDetails[0]['DepositID'];
				
				$Status = $this->obj_ChequeDetails->DeletePreviousRecord($PaidByPrev, $PayerBankPrev, $PayerChequeBranchPrev,$ChequeDetailsId);
				
				if($Status == "Update")
				{
					$result = $this->obj_ChequeDetails->AddNewValues2($DepositID,$PaidBy,$ChequeNumber,$VoucherDate,$ChequeDate,$ByArray,$ToArray,$ListOfAccounts, $_POST['VoucherNumber'],$_POST['OnPageLoadTimeVoucherNumber'],$_POST['IsCallUpdtCnt'], $PayerAmount, $PayerBankID,$PayerBankPre,$PayerChequeBranchPrev,$_POST['Note'],VOUCHER_RECEIPT,$BillType,true,$GUID);
					
					if($result <> "")
					{
						$this->EDITEntryTracker .= "<br> VoucherDate | ChequeDate | ChequeNumber | Amount | PaidBy | BankID | PayerBank | PayerChequeBranch | DepositID | EnteredBy | Comments | BillType<br>";
						$this->EDITEntryTracker .= $VoucherDate ."|". $ChequeDate ."|". $ChequeNumber ."|". $PayerAmount ."|". $PaidBy."|". $PayerBankID . "|" . $PayerBankPre."|".$PayerChequeBranchPrev."|".$DepositID . "|".$_SESSION['login_id']. "|" .$_POST['Note']. "|" .$BillType;
						
						
						$this->actionPage = "../VoucherEdit.php?Vno=".$result."&bankid=".$PayerBankID."&receipt=1";
					}
					$this->changeLog->setLog($this->EDITEntryTracker, $_SESSION['login_id'], "chequeentrydetails", $ChequeDetailsId);
					
				}
				return "Update";
			}
			
			$UpdateInvoice=true;
			
			if($_POST['page']=='Liability')
			{
			$this->actionPage="../LiabilitySummary.php";
			}
			
			if($_POST['page']=='Income')
			{
			$this->actionPage="../IncomeDetails.php";
			}
			
			if($_POST['page']=='Expense')
			{
			$this->actionPage="../ExpenseDetails.php";
			}
			
			
			if(isset($_POST['Vno']) && $_POST['Vno'] <> '')
		  	{
				$PreviousData=$this->FetchData($_POST['Vno']);
			
			   if($PreviousData <> '')
			   {
				//print_r($PreviousData);
				foreach($PreviousData as $k=>$v)
				{
					//echo '1';
					if($PreviousData[$k]['By'] <> '')
					{
						//echo '2';
						$sql="select  changelogtbl.ChangedLogDec, vouchertbl.ExternalCounter from `voucher` as vouchertbl JOIN `change_log` as changelogtbl on changelogtbl.ChangeLogID=vouchertbl.LatestChangeID where VoucherNo='".$_POST['Vno']."' and `By`=".$PreviousData[$k]['By']." ";
						//echo $sql;
						$data=$this->m_dbConn->select($sql);
						 
						$PreviousMsg=$data[0]['ChangedLogDec'];
						$PreviousCounter = $data[0]['ExternalCounter'];
						$msg=$this->DeletePreviousRecords($PreviousData[$k]['By'],$_POST['Vno'],$PreviousData[$k]['Debit'],$PreviousData[$k]['Date'],$PreviousData[$k]['id'],'By');
						
						
					}
					else
					{
						//echo '3';
						$msg.=$this->DeletePreviousRecords($PreviousData[$k]['To'],$_POST['Vno'],$PreviousData[$k]['Credit'],$PreviousData[$k]['Date'],$PreviousData[$k]['id'],'To');  
					}
				}
				
		    }	
		  	//$iLatestChangeID = $this->changeLog->setLog($msg, $_SESSION['login_id'], 'VOUCHER', '--');
	   }
	   
	 }
	 
	 
	 if($_POST['Updatetable'] == TABLE_PAYMENT_DETAILS && $_POST['mode'] == ADD) //If it is new Multiple Ledger Payement
	 {
		 return $this->update_payment_details(ADD); // Passing flag to add new entry
	}
	 
	$TDSVNo=0;
	if(!empty($_POST['Vno']) && $_POST['Vno'] != 0){

		$sqlVoucher="Select InvoiceStatusID from `invoicestatus` where TDSVoucherNo='".$_POST['Vno']."'"; 	
	 	$data1=$this->m_dbConn->select($sqlVoucher);
	
		if( $data1<> '')
		{
				$TDSVNo= $data1[0]['InvoiceStatusID'];
		}
	}
	
		
		$IsSubmit = $_POST['submit'];
		$VoucherDate = $_POST['voucher_date'];
		$maxrows = $_POST['maxrows'];
		$ExistVoucher=$_POST['Vno'];
		$arData = array();
		/*$byto =	array();
		$To = array();
		$Debit = array();
		$Credit = array();*/
		for($i=1;$i<=$_POST['maxrows'];$i++)
		{
			$arSubData = array();
			$arSubData['byto'] = $_POST['byto'.$i];
			$arSubData['To'] = $_POST['To'.$i];
			$arSubData['Debit'] = $_POST['Debit'.$i];
			$arSubData['Credit'] = $_POST['Credit'.$i];
			$arData[$i-1] = $arSubData;  
		}
			/*echo "<pre>";
			print_r($arData);
			echo "</pre>";*/
			//die();
		$is_invoice = $_POST['is_invoice'];
		$IGST_Amount = $_POST['igst_amount'];
		$CGST_Amount = $_POST['cgst_amount'];
		$SGST_Amount = $_POST['sgst_amount'];
		$Cess_Amount = $_POST['cess_amount'];
		$NewInvoiceNo = $_POST['invoice_no'];
		$InvoiceStatusID = $_POST['InvoiceStatusID'];
		$Note = $_POST['Note'];
		
		
		$RefNo = $_POST['RowID'];
		$RefTableID = $_POST['Updatetable'];
		$ExistVoucher = $_POST['Vno'];
		
		if($_POST['Updatetable'] == TABLE_FIXEDASSETLIST)
		{
			$this->Update_Fixed_Asset($RefNo,$arData);
		}
		
		
		$VoucherNumber = $_POST['VoucherNumber'];
		$IsCallUpdtCnt = $_POST['IsCallUpdtCnt'];
	  	$Result = $this->createNewVoucher($PreviousMsg,$PreviousCounter,$UpdateInvoice,$TDSVNo,$IsSubmit,$VoucherDate,$arData,$is_invoice,$IGST_Amount,$CGST_Amount,$SGST_Amount,$Cess_Amount,$NewInvoiceNo,$InvoiceStatusID,$Note,$ExistVoucher,$RefNo,$RefTableID,$VoucherNumber,$IsCallUpdtCnt);
		//print_r($Result);
		//die();
		
		//if($_POST['is_invoice']> 0)
	  // {
		   //$selectID="select *from voucher" vhere
		  // $updateQuery="update `invoicestatus` set InvoiceChequeAmount='".$_POST['Debit']."', AmountReceivable='".$_POST['Debit']."',IGST_Amount='".$_POST['igst_amount']."',CGST_Amount='".$_POST['cgst_amount']."',SGST_Amount='".$_POST['sgst_amount']."',CESS_Amount='".$_POST['cess_amount']."' where InvoiceStatusID='".$_POST['InvoiceStatusID']."' and NewInvoiceNo='".$_POST['invoice_no']."'";
	    //$updateQuery="update `invoicestatus` set InvoiceChequeAmount='".$_POST['Debit']."', AmountReceivable='".$_POST['Debit']."',IGST_Amount='".$_POST['igst_amount']."',CGST_Amount='".$_POST['cgst_amount']."',SGST_Amount='".$_POST['sgst_amount']."',CESS_Amount='".$_POST['cess_amount']."' where InvoiceStatusID='".$_POST['InvoiceStatusID']."' and NewInvoiceNo='".$_POST['invoice_no']."'";
		//$res=$this->m_dbConn->update($updateQuery);
	  // }
	  // else
	  // {
	  // $deleteData="Delete * from `invoicestatus` where InvoiceStatusID='".$_POST['InvoiceStatusID']."' and NewInvoiceNo='".$_POST['invoice_no']."' ";		$res1=$this->m_dbConn->delete($deleteData);
	   //}
		return $Result;
 }		
 
 
 	public function Update_Fixed_Asset($RefNo,$arData)
	{
		$Fixed_Asset_Details_Query = "SELECT OpeningValue From fixedassetlist WHERE FixedAssetID = '".$RefNo."'";
		$Fixed_Asset_Details = $this->m_dbConn->select($Fixed_Asset_Details_Query);
		$DepreciationAmount = 0;
		
		for($i = 0; $i < count($arData); $i++)
		{
			if($DepreciationAmount == 0)
			{
				if(isset($arData[$i]['byto']) && strtoupper($arData[$i]['byto'])=="BY")
				{
					if($arData[$i]['Debit'] <> 0)
					{
						$DepreciationAmount = $arData[$i]['Debit'];
					}
				}
			}
		}
		$EndingValue = $Fixed_Asset_Details[0]['OpeningValue'] - $DepreciationAmount;
		
		$Update_Fixed_Asset = "UPDATE fixedassetlist SET EndingValue = '".$EndingValue."', Depreciation = '".$DepreciationAmount."' WHERE FixedAssetID = '".$RefNo."'";
		$result = $this->m_dbConn->update($Update_Fixed_Asset);
		return $result;
	}
 
 
 	public function update_payment_details($Mode) //update_payment_details start $maode is request whether you want to ADD new Entry Or Edit the Exiting Entry
	{
		// getting all value from UI
		$ChequeDate = $_POST['voucher_date'];
		$VoucherDate = $_POST['voucher_date'];
		$ChequeNumber = $_POST['ChequeNumber'];
		$ModeOfPayment = $_POST['ModeOfPayment'];
		$maxrows = $_POST['maxrows'];
		$ExistVoucher=$_POST['Vno'];
		$ByArray = array();
		$ToArray = array();
		$BankLedgers = array();
		$Amount = 0;
		$ToTotal = 0;
		$PayerBank = 0;
		$ListOfCashLedger = $this->m_objUtility->GetBankLedger($_SESSION['default_cash_account']); // return list of bank ledgers
		$ListOfBankLedger = $this->m_objUtility->GetBankLedger($_SESSION['default_bank_account']); // return list of cash ledgers
		$GUID = "";
		
		$ListOfAccounts = array_merge($ListOfBankLedger,$ListOfCashLedger); // combination of bank and cash ledger
		$BankLedgers = array_column($ListOfAccounts, 'id');
		$CashLedgers = array_column($ListOfCashLedger,'id');
		
		$cnt = 0;	
		
		for($i=1;$i<=$_POST['maxrows'];$i++) // Start reading BY side entry
		{
			$arSubData = array();
			if($_POST['byto'.$i] == 'BY')
			{
				if($_POST['To'.$i] == 0 || empty($_POST['To'.$i])) // Checking whether user selected any ledger in drop down or not. I not then skip that row
				{
					continue;
				}
				if(in_array($_POST['To'.$i],$BankLedgers)) // Checking if in drop down they selected any bank or not
				{
					$PayerBank = $_POST['To'.$i];
					$Amount  =  $_POST['Debit'.$i]; // Adding final total
				}
				$arSubData['Head'] = $_POST['To'.$i]; //Set Ledger ID
				$arSubData['Amt'] = $_POST['Debit'.$i]; // Set Ledger Amount
				
				$ToArray[$cnt] = $arSubData; // Push particular row data into ToArray because By and To is reverse in Voucher Table
				$cnt++;
			}
		}
		
		$IsbankPresent = false; // set Flag to check whether Bank present in TO side ledger drop down	
		$cnt = 0;
		for($i=1;$i<=$_POST['maxrows'];$i++)
		{
			$arSubData = array();
			if($_POST['byto'.$i] == 'TO')
			{
				if($_POST['To'.$i] == 0 || empty($_POST['To'.$i]))// Checking whether user selected any ledger in drop down or not. I not then skip that row
				{
					continue;
				}
				if(in_array($_POST['To'.$i],$BankLedgers))// Checking if in drop down they selected any bank or not
				{
					$IsbankPresent = true; // set flag true if bank present
					$PayerBank = $_POST['To'.$i];
					$Amount  =  $_POST['Credit'.$i];
				}
				$arSubData['Head'] = $_POST['To'.$i]; //Set Ledger ID
				$arSubData['Amt'] = $_POST['Credit'.$i]; // Set Ledger Amount
				$ByArray[$cnt] = $arSubData;// Push particular row data into ByArray because By and To is reverse in Voucher Table
				$cnt++;
			}
		}
		$IsTallyImport = false; //Need to implement for tally entry also. Till that IsTallyImport flag is hardcoded. 
		$PaidTo = $ToArray[0]['Head']; // setting PaidTo 
		
		if($IsTallyImport == true) //If it is Tally import
		{
			$PaidTo = $ByArray[0]['Head']; // Tally import provide right by and to side entry but in our system is reverse
			if($IsbankPresent == true)
			{
				$PaidTo = $ToArray[0]['Head'];	
			}
		}
		
		if(in_array($PayerBank,$CashLedgers))
		{
			$ModeOfPayment = '-1';
			$ChequeNumber = '-1';
		}
		
		
		if($Mode == EDIT) // IF it is EDIT Mode 
		{
			
			$GetPriviousEntriesDetails = $this->m_dbConn->select("Select * from  paymentdetails where id = '".$_POST['RowID']."'"); //Fetching Exiting data
			$PaidToPre = $GetPriviousEntriesDetails[0]['PaidTo'];
			$ChequeNumberPre = $GetPriviousEntriesDetails[0]['ChequeNumber'];
			$ChequeDatePre = $GetPriviousEntriesDetails[0]['ChequeDate'];
			$AmountPre = $GetPriviousEntriesDetails[0]['Amount'];
			$PayerBankPre = $GetPriviousEntriesDetails[0]['PayerBank'];
			$CommentsPre = $GetPriviousEntriesDetails[0]['Comments'];
			$VoucherDatePre = $GetPriviousEntriesDetails[0]['VoucherDate'];
			$InvoiceDatePre = $GetPriviousEntriesDetails[0]['InvoiceDate'];
			$TDSAmountPre = $GetPriviousEntriesDetails[0]['TDSAmount'];
			$LeafID = $GetPriviousEntriesDetails[0]['ChqLeafID'];
			$DoubleEntry = $GetPriviousEntriesDetails[0]['IsMultipleEntry'];
			$ExpenseByPre = $GetPriviousEntriesDetails[0]['ExpenseBy'];
			$RowID = $_POST['RowID'];
			$ModeOfPaymentPre = $GetPriviousEntriesDetails[0]['ModeOfPayment'];
			$InvoiceAmountPre = $GetPriviousEntriesDetails[0]['InvoiceAmount'];
			
			$bankDetails_Result = $this->obj_PaymentDetails->getReconcileStatus($ExistVoucher,$PayerBankPre);
			
			$reconcileDate = $bankDetails_Result[0]['Reconcile Date'];
			$reconcileStatus = $bankDetails_Result[0]['ReconcileStatus'];
			$reconcile = $bankDetails_Result[0]['Reconcile'];
			$return = $bankDetails_Result[0]['Return'];
			
			//Storing all current data to store in log 
			$str = "\r\nPaidTo | ChequeNumber | ChequeDate | Amount | PayerBank | Comments | VoucherDate | InvoiceDate | TDSAmount | LeafID | DoubleEntry | ExpenseBy | RowID | ModeOfPaymentPre";
			$str1 = "\r\n".$PaidToPre."|".$ChequeNumberPre."|".$ChequeDatePre."|".$AmountPre."|".$PayerBankPre."|".$CommentsPre."|".$VoucherDatePre."|".$InvoiceDatePre."|".$TDSAmountPre."|".$LeafID."|".$DoubleEntry."|".$ExpenseByPre."|".$RowID."|".$ModeOfPaymentPre."|".$InvoiceAmountPre;
			$this->EDITEntryTracker = "\r\nPrev Record:".$str."<br>";
			$this->EDITEntryTracker .= $str1."<br>";
			
			// Calling deletePaymentDetails to delete the exiting entry
			$Status = $this->obj_PaymentDetails->deletePaymentDetails($ChequeDatePre,$ChequeNumberPre,$VoucherDatePre,$AmountPre,$PaidToPre,$ExpenseByPre,$PayerBankPre,$ChqLeafIDPre,$CommentsPre,$InvoiceDatePre,$TDSAmountPre,$RowID, false, 0, 0);
		
			if($Status) // If Exiting data deleted then only this condition will true
			{
				$this->EDITEntryTracker .="\r\npayment record deleted successfully.";
				// Now calling AddNewPaymentEntry 
				$result = $this->obj_PaymentDetails->AddNewPaymentEntry($LeafID,$_SESSION['society_id'],$PaidTo,$ChequeNumber,$ChequeDate,$Amount,$PayerBank,$_POST['Note'],VOUCHER_PAYMENT,$VoucherDate,$ModeOfPayment,$ByArray,$ToArray,$MultipleEntry = 0,$ListOfAccounts,$_POST['VoucherNumber'],$_POST['OnPageLoadTimeVoucherNumber'],$_POST['IsCallUpdtCnt'],true, $reconcileDate, $reconcileStatus, $reconcile, $return, $GUID);	
			
				if($result <> "") 
				{
					// storing new record added log
					$str2 = "\r\n".$PaidTo."|".$ChequeNumber."|".$ChequeDate."|".$Amount."|".$PayerBank."|".$_POST['Note']."|".$VoucherDate."|".$InvoiceDate."|".$TDSAmount."|".$LeafID."|".$DoubleEntry."|".$ExpenseBy."|".$result."|".$ModeOfPayment;
					$this->EDITEntryTracker .="\r\nnew record added at($result):".$str2;
					$this->actionPage = "../VoucherEdit.php?Vno=".$result."&bankid=".$PayerBank."&LeafID=".$_POST['LeafID']."&CustomLeaf=".$_POST['CustomLeaf']."&payment=1"; // Redirect to VoucherEdit File
				}
				
				//setLog to set log into table
				$this->changeLog->setLog($this->EDITEntryTracker, $_SESSION['login_id'], "paymentdetails", $RowID);
				return "Update"; // return update means entry updated successfully
			}
		}
		else
		{
			$result = $this->obj_PaymentDetails->AddNewPaymentEntry($_POST['LeafID'],$_SESSION['society_id'],$PaidTo,$ChequeNumber,$ChequeDate,$Amount,$PayerBank,$_POST['Note'],VOUCHER_PAYMENT,$VoucherDate,$ModeOfPayment,$ByArray,$ToArray,$MultipleEntry = 0,$ListOfAccounts,$_POST['VoucherNumber'],$_POST['OnPageLoadTimeVoucherNumber'],$_POST['IsCallUpdtCnt'],false,0,0,0,0,$GUID);					
			$this->actionPage = "../PaymentDetails.php?bankid=".$PayerBank."&LeafID=".$_POST['LeafID']."&CustomLeaf=".$_POST['CustomLeaf']; 
			return "Insert";
		}
	}
	
	public function combobox($query, $id, $bShowAll = false)
	{
		if($bShowAll == true)
		{
			$str.="<option value=''>All</option>";
		}
		else
		{
			$str.="<option value='0'>Please Select</option>";
		}
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			$vowels = array('/', '-', '.', '*', '%', '&', ',', '"');
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
						$str.= str_replace($vowels, ' ', $v)."</OPTION>";
					}
					$i++;
				}
			}
		}
			return $str;
	}
	
	public function createNewVoucher($PreviousMsg,$PreviousCounter,$UpdateInvoice,$invoicestatusID,$IsSubmit,$VoucherDate,$arData,$is_invoice,$IGST_Amount,$CGST_Amount,$SGST_Amount,$Cess_Amount,$NewInvoiceNo,$InvoiceStatusID,$Note,$ExistVoucherNo=0,$RefNo=0,$RefTableID=0,$EXVoucherNumber,$IsCallUpdtCnt)
	{
	//	echo '<br>Create New Voucher';
		$GUID = "";
		return $this->createNewVoucher_WithGUID($PreviousMsg,$PreviousCounter,$UpdateInvoice,$invoicestatusID,$IsSubmit,$VoucherDate,$arData,$is_invoice,$IGST_Amount,$CGST_Amount,$SGST_Amount,$Cess_Amount,$NewInvoiceNo,$InvoiceStatusID,$Note,$ExistVoucherNo,$RefNo,$RefTableID,$EXVoucherNumber,$IsCallUpdtCnt,$GUID);
	}
	
	public function createNewVoucher_WithGUID($PreviousMsg,$PreviousCounter,$UpdateInvoice,$invoicestatusID,$IsSubmit,$VoucherDate,$arData,$is_invoice,$IGST_Amount,$CGST_Amount,$SGST_Amount,$Cess_Amount,$NewInvoiceNo,$InvoiceStatusID,$Note,$ExistVoucherNo=0,$RefNo=0,$RefTableID=0,$EXVoucherNumber,$IsCallUpdtCnt,$GUID)
	{
	//	echo '<br>main Function ; '.$GUID;
		$dataVoucher1=0;
		$Createmsg="";
		$dataArr = array();
		$dataArr['Date'] = $VoucherDate;
		$dataArr['Voucher No'] = $EXVoucherNumber;
		//echo"msg:".$PreviousMsg ." update1 ".$UpdateInvoice." update2 ".$invoicestatusID." update3 ".$IsSubmit." update4 ".$VoucherDate." update5 ".$is_invoice;
		
		/*echo "in createnewvoucher<br>";
		echo "<pre>";
		print_r($arData);
		echo "</pre>";*/
		
							
		if(isset($IsSubmit) && isset($VoucherDate) && $VoucherDate <> "")
		{ 				
			$SrNo=0;
			$total=0;
			$TDSAMount=0;
			if($ExistVoucherNo != 0)
			{
				$LatestVoucherNo = $ExistVoucherNo;
			}
			else
			{	
				$LatestVoucherNo = $this->m_latestcount->getLatestVoucherNo($_SESSION['society_id']);
			}
					//	$PaymentVoucherNo = $LatestVoucherNo;
		//	$LatestVoucherNo = $this->m_latestcount->getLatestVoucherNo($_SESSION['society_id']);	
			try
			{
				$this->m_dbConn->begin_transaction(); 
				
				$FetchYearID = "Select APP_DEFAULT_YEAR from appdefault";
				$ResultFetchYearID = $this->m_dbConn->select($FetchYearID);
				
				$ExpectedCounter = $this->m_objUtility->GetCounter(VOUCHER_JOURNAL,0,false);
				if($IsCallUpdtCnt == 1)
				{
					$this->m_objUtility->UpdateExVCounter(VOUCHER_JOURNAL,$EXVoucherNumber,0);
				}
				
				for($i=0;$i<sizeof($arData);$i++)
				{
					$SrNo++;
					//echo "byto1: ".$arData[$i]['byto']."<br>";
					//echo "To1: ".$arData[$i]['To']."<br>";
										
					if(isset($arData[$i]['byto']) && strtoupper($arData[$i]['byto'])=="BY")
					{
						//echo "byto2: ".$arData[$i]['byto']."<br>";
						//echo "To2: ".$arData[$i]['To']."<br>";

						if(isset($arData[$i]['To']) && $arData[$i]['To'] <> '0' && $arData[$i]['Debit'] <> 0 && $arData[$i]['Debit'] <> '')
						{	
							$LedgerName=$this->m_objUtility->getLedgerName($arData[$i]['To']);
							
							$dataArr['By Ledger'][$LedgerName] = number_format($arData[$i]['Debit'], 2);
							
							//$Createmsg="Amount from ".$LedgerName." ".$arData[$i]['Debit']." debit voucher created.";
							//$iLatestChangeID = $this->changeLog->setLog($Createmsg, $_SESSION['login_id'], 'VOUCHER', '--');	
							
							if(!empty($GUID))
							{
								$dataVoucher1 = $this->m_voucher->SetVoucherDetails_WithGUID(getDBFormatDate($VoucherDate),$RefNo,$RefTableID,
$LatestVoucherNo,$SrNo,VOUCHER_JOURNAL,$arData[$i]['To'],TRANSACTION_DEBIT,$arData[$i]['Debit'],$this->m_dbConn->escapeString($Note),$EXVoucherNumber,$GUID);	
							}
							else
							{
								$dataVoucher1 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),$RefNo,$RefTableID,
$LatestVoucherNo,$SrNo,VOUCHER_JOURNAL,$arData[$i]['To'],TRANSACTION_DEBIT,$arData[$i]['Debit'],$this->m_dbConn->escapeString($Note),$EXVoucherNumber);
							}
							
							
							$arByParentDetails = $this->m_objUtility->getParentOfLedger($arData[$i]['To']);
					
							if(!(empty($arByParentDetails)))
							{
								$ByGroupID = $arByParentDetails['group'];
								$ByCategoryID = $arByParentDetails['category'];		
						
								if($ByGroupID==LIABILITY)
								{	
									//echo 'SetLiabilityRegister';
									$regResult1 = $this->m_register->SetLiabilityRegister(getDBFormatDate($VoucherDate),$arData[$i]['To'],$dataVoucher1,VOUCHER_JOURNAL, TRANSACTION_DEBIT, $arData[$i]['Debit'],0,$iLatestChangeID);	
						
								}
					
								if($ByGroupID==ASSET)
								{
									if($ByCategoryID == BANK_ACCOUNT || $ByCategoryID == CASH_ACCOUNT)
									{
										$regResult2 = $this->m_register->SetBankRegister(getDBFormatDate($VoucherDate),$arData[$i]['To'], $dataVoucher1, VOUCHER_JOURNAL,
										TRANSACTION_RECEIVED_AMOUNT, $arData[$i]['Debit'], -1, 0, 0, getDBFormatDate($VoucherDate), 0, 0,0, 0, 0);
									
									}
									else
									{
										//echo 'SetAssetRegister';
										$regResult2 = $this->m_register->SetAssetRegister(getDBFormatDate($VoucherDate),$arData[$i]['To'], $dataVoucher1, VOUCHER_JOURNAL, TRANSACTION_DEBIT, $arData[$i]['Debit'],0,$iLatestChangeID);		
									}
									
								}
					
								if($ByGroupID==INCOME)
								{
									//echo 'SetIncomeRegister';
									$regResult3 = $this->m_register->SetIncomeRegister($arData[$i]['To'], getDBFormatDate($VoucherDate), $dataVoucher1, VOUCHER_JOURNAL, TRANSACTION_DEBIT, $arData[$i]['Debit'],$iLatestChangeID);
								}
					
								if($ByGroupID==EXPENSE)
								{
									//echo 'SetExpenseRegister';
									$regResult4 = $this->m_register->SetExpenseRegister($arData[$i]['To'],getDBFormatDate($VoucherDate), $dataVoucher1, VOUCHER_JOURNAL, TRANSACTION_DEBIT,$arData[$i]['Debit'],0,$iLatestChangeID);
								}
							}			
						}			
					}
					else if( isset($arData[$i]['byto']) && strtoupper($arData[$i]['byto'])=="TO" )
					{					
						if(isset($arData[$i]['To']) && $arData[$i]['To'] <> '0' && $arData[$i]['Credit'] <> 0 && $arData[$i]['Credit'] <> '')	
						{
							$TDSAMount=$arData[$i]['Credit'];
							$LedgerName=$this->m_objUtility->getLedgerName($arData[$i]['To']);
							
							$dataArr['To Ledger'][$LedgerName] = number_format($arData[$i]['Credit'], 2);
							
							//$Createmsg.=$LedgerName." amount ".$arData[$i]['Credit']."credit voucher created.";
							//$iLatestChangeID = $this->changeLog->setLog($Createmsg, $_SESSION['login_id'], 'VOUCHER', '--');
							
							if(!empty($GUID))
							{
								$dataVoucher2 = $this->m_voucher->SetVoucherDetails_WithGUID(getDBFormatDate($VoucherDate),$RefNo,$RefTableID,
								$LatestVoucherNo,$SrNo,VOUCHER_JOURNAL,$arData[$i]['To'],TRANSACTION_CREDIT,$arData[$i]['Credit'],$this->m_dbConn->escapeString($Note),$EXVoucherNumber,$GUID);
							}
							else
							{
								$dataVoucher2 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),$RefNo,$RefTableID,
								$LatestVoucherNo,$SrNo,VOUCHER_JOURNAL,$arData[$i]['To'],TRANSACTION_CREDIT,$arData[$i]['Credit'],$this->m_dbConn->escapeString($Note),$EXVoucherNumber);
							}	
							//echo 'settovoucher';
							$arToParentDetails = $this->m_objUtility->getParentOfLedger($arData[$i]['To']);
		
							if(!(empty($arToParentDetails)))
							{
								$ToGroupID = $arToParentDetails['group'];
								$ToCategoryID = $arToParentDetails['category'];		
							
								if($ToGroupID==LIABILITY)
								{
									//echo 'SetLiabilityRegister';
									$regResult1 = $this->m_register->SetLiabilityRegister(getDBFormatDate($VoucherDate),$arData[$i]['To'],$dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $arData[$i]['Credit'], 0,$iLatestChangeID);	
						
								}
					
								if($ToGroupID==ASSET)
								{
									if($ToCategoryID == BANK_ACCOUNT || $ToCategoryID == CASH_ACCOUNT)
									{
										$regResult2 = $this->m_register->SetBankRegister(getDBFormatDate($VoucherDate),$arData[$i]['To'], $dataVoucher2, VOUCHER_JOURNAL,
										TRANSACTION_PAID_AMOUNT, $arData[$i]['Credit'], -1, 0, 0, getDBFormatDate($VoucherDate), 0, 0,0, 0, 0);
									
									}
									else
									{
										//echo 'SetAssetRegister';
										$regResult2 = $this->m_register->SetAssetRegister(getDBFormatDate($VoucherDate), $arData[$i]['To'], $dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $arData[$i]['Credit'], 0,$iLatestChangeID);	
									}
								}
					
								if($ToGroupID==INCOME)
								{
									//echo 'SetIncomeRegister';
									$regResult3 = $this->m_register->SetIncomeRegister($arData[$i]['To'], getDBFormatDate($VoucherDate), $dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $arData[$i]['Credit'],$iLatestChangeID);
								}
			
								if($ToGroupID==EXPENSE)
								{
						
									//echo 'SetExpenseRegister';
									$regResult4 = $this->m_register->SetExpenseRegister($arData[$i]['To'],getDBFormatDate($VoucherDate), $dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $arData[$i]['Credit'],0,$iLatestChangeID);
						
								}					
							}						
						}
					}
					$total+=$arData[$i]['Credit'];
					
					if($_SESSION['sgst_input']==$arData[$i]['To'])
					{
						$SGST_Amount=$arData[$i]['Debit'];
					}
					if($_SESSION['cgst_input']==$arData[$i]['To'])
					{
						$CGST_Amount=$arData[$i]['Debit'];
					}
					
					//print_r($total);
				}
				if($UpdateInvoice==true)
				{ 
				//echo 'test' . $is_invoice;
					
					if($is_invoice > 0)
					{
						//echo 'SGST : ' . $sgst_input;
					//	echo 'Session : ' . $_SESSION['sgst_input'];
						//echo 'Data : ' . $arData[$i]['To'];
						/*if($_SESSION['sgst_input']==$arData[$i]['To'])
						{	
							echo $updateQuery="update `invoicestatus` set InvoiceChequeAmount='".$total."',InvoiceRaisedVoucherNo='".$LatestVoucherNo."', AmountReceivable='".$total."',IGST_Amount='".$IGST_Amount."',SGST_Amount='".$arData[$i]['Debit']."',CESS_Amount='".$Cess_Amount."',NewInvoiceNo='".$NewInvoiceNo."' where InvoiceStatusID='".$InvoiceStatusID."'";
						}
						else if( $_SESSION['cgst_input']== $arData[$i]['To'])
						{	
							echo $updateQuery="update `invoicestatus` set InvoiceChequeAmount='".$total."',InvoiceRaisedVoucherNo='".$LatestVoucherNo."', AmountReceivable='".$total."',IGST_Amount='".$IGST_Amount."',CGST_Amount='".$arData[$i]['Debit']."',CESS_Amount='".$Cess_Amount."',NewInvoiceNo='".$NewInvoiceNo."' where InvoiceStatusID='".$InvoiceStatusID."'";
						}
						
						else*/
						{
							 $updateQuery="update `invoicestatus` set InvoiceChequeAmount='".$total."',InvoiceRaisedVoucherNo='".$LatestVoucherNo."', AmountReceivable='".$total."',IGST_Amount='".$IGST_Amount."',CGST_Amount='".$CGST_Amount."',SGST_Amount='".$SGST_Amount."',CESS_Amount='".$Cess_Amount."',NewInvoiceNo='".$NewInvoiceNo."' where InvoiceStatusID='".$InvoiceStatusID."'";
						}
							
						//}
					$res1=$this->m_dbConn->update($updateQuery);
					}
					else
					{
						if($InvoiceStatusID <> '' && $NewInvoiceNo <> '')
						{
							$deleteData="Delete  from `invoicestatus` where InvoiceStatusID='".$InvoiceStatusID."' and NewInvoiceNo='".$NewInvoiceNo."'";
							$res1=$this->m_dbConn->delete($deleteData);
						}
					}
				}
				else
				{
					if(isset($is_invoice) && $is_invoice==1)
		 			{
						$DocumentStatus="Insert into `invoicestatus`(`NewInvoiceNo`,`InvoiceChequeAmount`,`InvoiceRaisedVoucherNo`,`AmountReceivable`,IGST_Amount,CGST_Amount,SGST_Amount,CESS_Amount,is_invoice) values('".$NewInvoiceNo."','".$total."','".$LatestVoucherNo."','".$total."','".$IGST_Amount."','".$CGST_Amount."','".$SGST_Amount."','".$Cess_Amount."','".$is_invoice."')";
						$res=$this->m_dbConn->insert($DocumentStatus);
					}
				}
				if($invoicestatusID > 0)
				{
					$updateQuery="update `invoicestatus` set TDSVoucherNo='".$LatestVoucherNo."', TDSAmount='".$TDSAMount."' where InvoiceStatusID='".$invoicestatusID."'";
					$res1=$this->m_dbConn->update($updateQuery);	
				}
		//if($TDSAMount<400)
			//{
				//throw new Exception("value entered should greater than 400");
			//}
			}
			catch( Exception $exp)
			{
				echo "message:".$exp->getMessage();
				$this->m_dbConn->rollback();
			}
			
			if($EXVoucherNumber <> $ExpectedCounter[0]['CurrentCounter'])
			{
				//This code is run when voucher create
				$Createmsg .= ' :: Voucher Number changed to '.$EXVoucherNumber . ' Expected '.$ExpectedCounter[0]['CurrentCounter'].' ';
			}
			if($PreviousMsg <> '')
			{
				// This is on edit mode run 
				if($PreviousCounter <> 0 && $PreviousCounter <> '')
				{
					if($EXVoucherNumber <> $PreviousCounter)
					{
						$ChangeMsg .= ' :: Counter  '.$PreviousCounter.' Changed to '.$EXVoucherNumber; 
					}	
				}
			}
			else
			{
				$ChangeMsg=$Createmsg;		
			}
			
			
			$dataArr['Amount'] = $total;
			
			if($is_invoice){

				$dataArr['Invoice'] = 'YES';
				$dataArr['Invoice No.'] = $NewInvoiceNo;
				$dataArr['CGST Amount'] = $CGST_Amount;
				$dataArr['SGST Amount'] = $SGST_Amount;
			}
			else{

				$dataArr['Invoice'] = 'No';
				$dataArr['Invoice No.'] = '-';
				$dataArr['CGST Amount'] = 0;
				$dataArr['SGST Amount'] = 0;
			}

			$dataArr['Note'] = $this->m_dbConn->escapeString($Note);

			$logArr = json_encode($dataArr);

			if($this->request_mode == EDIT){

				if(!empty($ExistVoucherNo) && $ExistVoucherNo != 0){

					$checkPreviousLogQry = "SELECT ChangeLogID FROM change_log WHERE ChangedKey = '$ExistVoucherNo' AND ChangedTable = '".TABLE_JOURNAL_VOUCHER."' ORDER BY ChangeLogID DESC LIMIT 1";
				
					$previousLogDetails = $this->m_dbConn->select($checkPreviousLogQry);

					$previousLogID = $previousLogDetails[0]['ChangeLogID'];
				}
			}
		
			$iLatestChangeID = $this->changeLog->setLog($logArr, $_SESSION['login_id'], TABLE_JOURNAL_VOUCHER, $LatestVoucherNo, $this->request_mode, $previousLogID);
			
			$sql09="Update `voucher` set LatestChangeID='".$iLatestChangeID."' where id='".$dataVoucher1."'";
			$res=$this->m_dbConn->update($sql09);
			
			$this->m_dbConn->commit();
			
			return 'Update';		
		}
		else
		{
			return 'Record Not Updated';	
		}
	}
	
	public function getChequeDate($ChequeEntryDetailsTableID)
	{
		$SelectQuery = "SELECT ChequeDate,ChequeNumber,BillType FROM chequeentrydetails WHERE ID = '".$ChequeEntryDetailsTableID."'";
		$res = $this->m_dbConn->select($SelectQuery);
		return $res;
	}
	
public function FetchData($VoucherNo)
{
	 $sqlfetch="select `id`,DATE_FORMAT(Date, '%d-%m-%Y') as Date,`RefNo`,`RefTableID`,`VoucherNo`,`ExternalCounter`,`SrNo`,`VoucherTypeID`,`By`,`To`,`Debit`,`Credit`,`Note`,`LatestChangeID`,`Timestamp` from `voucher`  where VoucherNo='".$VoucherNo."'  ";
	//echo $sqlfetch;
	$result=$this->m_dbConn->select($sqlfetch);
	
		if($result['0']['RefTableID'] == TABLE_SALESINVOICE && $result['0']['VoucherTypeID'] == VOUCHER_JOURNAL)
		{
			//Fetching Notes for sale invoice because invoice note save in sale invoice table first time when invoice created
		
			$InvoiceNote = "Select Note from sale_invoice where ID = '".$result['0']['RefNo']."'";
			$InvoiceNoteResult = $this->m_dbConn->select($InvoiceNote);
			$result['0']['Note'] = $InvoiceNoteResult['0']['Note'];
		}
		else if($result['0']['RefTableID'] == TABLE_PAYMENT_DETAILS)
		{
			$PaymentDetails = $this->m_dbConn->select("SELECT ModeOfPayment, ChequeNumber FROM paymentdetails where id = '".$result['0']['RefNo']."'");
			$result['0']['ModeOfPayment'] = $PaymentDetails[0]['ModeOfPayment'];
			$result['0']['ChequeNumber'] = $PaymentDetails[0]['ChequeNumber'];
		}
		else if($result['0']['RefTableID'] == TABLE_CHEQUE_DETAILS)
		{
			//Pending for Cheque 
		}
	
if($result <> '')
	{
		$sqlselect="select * from `invoicestatus` where InvoiceRaisedVoucherNo='".$result[0]['VoucherNo']."'";
		$result1=$this->m_dbConn->select($sqlselect);
		
	 	$result[0]['InvoiceStatusID']=$result1[0]['InvoiceStatusID'];
		$result[0]['NewInvoiceNo']=$result1[0]['NewInvoiceNo'];
	  	$result[0]['IGST_Amount']=$result1[0]['IGST_Amount'];
	  	$result[0]['CGST_Amount']=$result1[0]['CGST_Amount'];
	    $result[0]['SGST_Amount']=$result1[0]['SGST_Amount'];
		$result[0]['CESS_Amount']=$result1[0]['CESS_Amount'];
		$result[0]['is_invoice']=$result1[0]['is_invoice'];
	}
	
	
	return $result;
	
	
	
}	

public function Totalrows($VoucherNo)
{
	$sqlfetch2="select count(*) as cnt from `voucher` where VoucherNo='".$VoucherNo."' ";
	//echo $sqlfetch2;
	$result2=$this->m_dbConn->select($sqlfetch2);
	
	return $result2[0]['cnt'];
	
}


public function DeletePreviousRecords($LedgerID,$VoucherNo,$Amount,$Date,$VoucherID,$Type)
{
	$dataVoucher1=0;
	$Createmsg2="";
	
	if($Type=='By')
	{
		$arByParentDetails = $this->m_objUtility->getParentOfLedger($LedgerID);	
		
		$LedgerName=$this->m_objUtility->getLedgerName($LedgerID);
		$Createmsg2="Amount from ".$LedgerName." ".$Amount."debit voucher deleted";
		$sql001="delete from `voucher` where VoucherNo=".$VoucherNo." and `By`=".$LedgerID." ";
		//echo $sql001;
		$sqlDelete = $this->m_dbConn->delete($sql001);
		
		//print_r($arByParentDetails);	
	   if(!(empty($arByParentDetails)))
	   {
			//echo 'arToParentDetails';
			$ByGroupID = $arByParentDetails['group'];
			$ByCategoryID = $arByParentDetails['category'];		
													
			if($ByGroupID==LIABILITY)
			{
				//echo '1';												
				//$regResult1 = $this->m_register->SetLiabilityRegister(getDBFormatDate($_POST['voucher_date']),$_POST['To'.$i],$dataVoucher1,VOUCHER_JOURNAL, TRANSACTION_DEBIT, $_POST['Debit'.$i], 0);	
				$regDelete1="delete from `liabilityregister` where `Is_Opening_Balance` = 0 AND VoucherID=".$VoucherID." and `LedgerID`=".$LedgerID." and Debit=".$Amount." ";
				//echo $regDelete1;
				$regResult1 = $this->m_dbConn->delete($regDelete1);
						//echo 'end';									
			}
															
			if($ByGroupID==ASSET)
			{
				if($ByCategoryID == BANK_ACCOUNT || $ByCategoryID == CASH_ACCOUNT)
				{
					$regDelete2="delete from `bankregister` where `Is_Opening_Balance` = 0 AND VoucherID=".$VoucherID." and `LedgerID`=".$LedgerID." and ReceivedAmount =".$Amount." ";
					//echo $regDelete2;
					$regResult2 = $this->m_dbConn->delete($regDelete2);			
					
				}
				else
				{
					//echo '2';										
					//$regResult2 = $this->m_register->SetAssetRegister(getDBFormatDate($_POST['voucher_date']), $_POST['To'.$i], $dataVoucher1, VOUCHER_JOURNAL, TRANSACTION_DEBIT, $_POST['Debit'.$i], 0);	
					$regDelete2="delete from `assetregister` where `Is_Opening_Balance` = 0 AND VoucherID=".$VoucherID." and `LedgerID`=".$LedgerID." and Debit=".$Amount." ";
					//echo $regDelete2;
					$regResult2 = $this->m_dbConn->delete($regDelete2);	
					//echo 'end';
							
				}
						
			}
															
			if($ByGroupID==INCOME)
			{
				//echo '3';													
				//$regResult3 = $this->m_register->SetIncomeRegister($_POST['To'.$i], getDBFormatDate($_POST['voucher_date']), $dataVoucher1, VOUCHER_JOURNAL, TRANSACTION_DEBIT, $_POST['Debit'.$i]);
				$regDelete3="delete from `incomeregister` where VoucherID=".$VoucherID." and `LedgerID`=".$LedgerID." and Debit=".$Amount."";
				//echo $regDelete3;
				$regResult3 = $this->m_dbConn->delete($regDelete3);
				//echo 'end';		
			}
															
			if($ByGroupID==EXPENSE)
			{
						
				//echo '4';													
				//$regResult4 = $this->m_register->SetExpenseRegister($_POST['To'.$i],getDBFormatDate($_POST['voucher_date']), $dataVoucher1, VOUCHER_JOURNAL, TRANSACTION_DEBIT,$_POST['Debit'.$i],0);
				$regDelete4="delete from `expenseregister` where VoucherID=".$VoucherID." and `LedgerID`=".$LedgerID." and Debit=".$Amount."";
				//echo $regDelete4;
				$regResult4 = $this->m_dbConn->delete($regDelete4);	
				//echo 'end';												
			}
													
		}
		
		
	}
	else
	{
		 $arToParentDetails = $this->m_objUtility->getParentOfLedger($LedgerID);
		 $LedgerName=$this->m_objUtility->getLedgerName($LedgerID);
		 $Createmsg2="Amount from ".$LedgerName." ".$Amount."credit voucher deleted.";
		 $sql002="delete from `voucher` where VoucherNo=".$VoucherNo." and `To`=".$LedgerID." ";
		// echo $sql002;
		 $sqlDelete = $this->m_dbConn->delete($sql002);
	
		 if(!(empty($arToParentDetails)))
		   {
			
					$ToGroupID = $arToParentDetails['group'];
					$ToCategoryID = $arToParentDetails['category'];		
																			
					if($ToGroupID==LIABILITY)
					{
								
						//$regResult1 = $this->m_register->SetLiabilityRegister(getDBFormatDate($_POST['voucher_date']),$_POST['To'.$i],$dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $_POST['Credit'.$i], 0);	
						$regDelete1="delete from `liabilityregister` where `Is_Opening_Balance` = 0 AND VoucherID=".$VoucherID." and `LedgerID`=".$LedgerID." and Credit=".$Amount." ";
						//echo $regDelete1;
						$regResult1 = $this->m_dbConn->delete($regDelete1);																
					}
																			
					if($ToGroupID==ASSET)
					{
						if($ToCategoryID == BANK_ACCOUNT || $ToCategoryID == CASH_ACCOUNT)
						{
							$regDelete2="delete from `bankregister` where `Is_Opening_Balance` = 0 AND VoucherID=".$VoucherID." and `LedgerID`=".$LedgerID." and PaidAmount =".$Amount." ";
							//echo $regDelete2;
							$regResult2 = $this->m_dbConn->delete($regDelete2);			
						}
						else
						{
							//$regResult2 = $this->m_register->SetAssetRegister(getDBFormatDate($_POST['voucher_date']), $_POST['To'.$i], $dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $_POST['Credit'.$i], 0);	
							$regDelete2="delete from `assetregister` where `Is_Opening_Balance` = 0 AND VoucherID=".$VoucherID." and `LedgerID`=".$LedgerID." and Credit=".$Amount." ";
							//echo $regDelete2;
							$regResult2 = $this->m_dbConn->delete($regDelete2);
						}
								
					}
																			
					if($ToGroupID==INCOME)
					{
																				
						//$regResult3 = $this->m_register->SetIncomeRegister($_POST['To'.$i], getDBFormatDate($_POST['voucher_date']), $dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $_POST['Credit'.$i]);
						$regDelete3="delete from `incomeregister` where VoucherID=".$VoucherID." and `LedgerID`=".$LedgerID." and Credit=".$Amount."";
						//echo $regDelete3;
						$regResult3 = $this->m_dbConn->delete($regDelete3);
					
					}
					
					if($ToGroupID==EXPENSE)
					{
																			
						//$regResult4 = $this->m_register->SetExpenseRegister($_POST['To'.$i],getDBFormatDate($_POST['voucher_date']), $dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $_POST['Credit'.$i],0);
						$regDelete4="delete from `expenseregister` where VoucherID=".$VoucherID." and `LedgerID`=".$LedgerID." and Credit=".$Amount."";
						//echo $regDelete4;
						$regResult4 = $this->m_dbConn->delete($regDelete4);														
					}
														
			}
	
	}
	
	
	return $Createmsg2;	 
	
}	


public function DeletedRecord()
{
	if(isset($_REQUEST['Vno']) && $_REQUEST['Vno'] <> '')
		  	{
				$PreviousData=$this->FetchData($_REQUEST['Vno']);
			
				   if($PreviousData <> '')
				   {
					//print_r($PreviousData);
						foreach($PreviousData as $k=>$v)
						{
							//echo '1';
							if($PreviousData[$k]['By'] <> '')
							{
								//echo '2';
								$sqlUpdate="Update `paymentdetails` set `ExpenseBy`='0',`TDSAmount`='0',`InvoiceDate`='0000-00-00'  where `VoucherID`='".$PreviousData[$k]['id']."' ";
								//echo $sqlUpdate;
								$data23=$this->m_dbConn->update($sqlUpdate);

								
								$sql="select  changelogtbl.ChangedLogDec, changelogtbl.ChangeLogID from `voucher` as vouchertbl JOIN `change_log` as changelogtbl on changelogtbl.ChangeLogID=vouchertbl.LatestChangeID where VoucherNo='".$_REQUEST['Vno']."' and `By`=".$PreviousData[$k]['By']." ";
								//echo $sql;
								$data=$this->m_dbConn->select($sql);
								 
								$PreviousMsg=$data[0]['ChangedLogDec'];
								$previousLogID = $data[0]['ChangeLogID'];
								$msg=$this->DeletePreviousRecords($PreviousData[$k]['By'],$_REQUEST['Vno'],$PreviousData[$k]['Debit'],$PreviousData[$k]['Date'],$PreviousData[$k]['id'],'By');
								
								
							}
							else
							{
								//echo '3';
								$msg.=$this->DeletePreviousRecords($PreviousData[$k]['To'],$_REQUEST['Vno'],$PreviousData[$k]['Credit'],$PreviousData[$k]['Date'],$PreviousData[$k]['id'],'To');  
							}
						}
					
			}
			
		  	$iLatestChangeID = $this->changeLog->setLog($PreviousMsg, $_SESSION['login_id'], TABLE_JOURNAL_VOUCHER, $_REQUEST['Vno'], DELETE, $previousLogID);	
			}
			$this->actionPage="../view_ledger_details.php?&lid='".$_REQUEST['lid']."'&gid='".$_REQUEST['gid']."'";
			return "Record Deleted Succesfully..";
}

public function StartEndDate($yearID)
{
	$sql="SELECT DATE_FORMAT(BeginingDate, '%d-%m-%Y') as BeginingDate,DATE_FORMAT(EndingDate, '%d-%m-%Y') as EndingDate FROM `year` where `YearID`='".$yearID."'  ";
	$data=$this->m_dbConn->select($sql);
	
	/*$BeginingDateArray=explode('-',$datepicker[0]['BeginingDate']);
	$EndDateArray=explode('-',$datepicker[0]['EndingDate']);
	$StartDate=$BeginingDateArray[0].'-'.$BeginingDateArray[0].'-'.$BeginingDateArray[0];
	$EndDate=$BeginingDateArray[0].'-'.$BeginingDateArray[0].'-'.$BeginingDateArray[0];
	*/
	return $data;
	
}

	public function getRefNoRefTableID($Vno)
	{
		$sql1 = "SELECT `RefNo`, `RefTableID` FROM `voucher` WHERE `VoucherNo` = '".$Vno."'";
		$sql1_res = $this->m_dbConn->select($sql1);
		
		return $sql1_res;
	}

}
?>
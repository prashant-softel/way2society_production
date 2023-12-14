<?php
include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once("utility.class.php");
include_once("../classes/dbconst.class.php");

class voucherCounter extends dbop
{
	public $actionPage = "../voucherCounter.php";
	public $utility;
	public $m_dbConn;
	public $m_dbConnRoot;
	function __construct($m_dbConn,$m_dbConnRoot)
	{
		$this->utility = new utility($m_dbConn,$m_dbConnRoot);
		$this->m_dbConn = $m_dbConn;
		$this->m_dbConnRoot = $m_dbConnRoot; 
	}

	public function startProcess()
	{
		$StartCounter = 0;
		$VoucherResult = 0;
		$FromDate = $_POST['from_date'];
		$EndDate = $_POST['end_date'];
		
		if($_REQUEST['btn_submit']=='Submit')
		{
			if($_POST['Journal'] == true)
			{
				$CounterDetails = $this->utility->GetCounter(VOUCHER_JOURNAL,0,false);
				$VoucherResult = $this->utility->CheckVoucherCounterExit(VOUCHER_JOURNAL,0,true, true, $FromDate, $EndDate);
				
				if(sizeof($VoucherResult) > 0)
				{
					$CurrentCounter = $this->updateCounter($_POST['JVstart'],$VoucherResult,0);
					$this->utility->UpdateExVCounter(VOUCHER_JOURNAL,$CurrentCounter,0);	
				}		
			}
			
			if($_POST['Invoice'] == true)
			{
				$CounterDetails = $this->utility->GetCounter(VOUCHER_INVOICE,0,false);
				$VoucherResult = $this->utility->CheckVoucherCounterExit(VOUCHER_INVOICE,0,true, true, $FromDate, $EndDate);
				//var_dump($VoucherResult);
				if(sizeof($VoucherResult) > 0)
				{
					$CurrentCounter = $this->updateCounter($_POST['InvoiceStart'],$VoucherResult,VOUCHER_INVOICE);
					$this->utility->UpdateExVCounter(VOUCHER_INVOICE,$CurrentCounter,0);	
				}		
			}
			
			if($_POST['Chk_DebitNote'] == true)
			{
				$CounterDetails = $this->utility->GetCounter(VOUCHER_DEBIT_NOTE,0,false);
				$VoucherResult = $this->utility->CheckVoucherCounterExit(VOUCHER_DEBIT_NOTE,0,true, true, $FromDate, $EndDate);
				//var_dump($VoucherResult);
				if(sizeof($VoucherResult) > 0)
				{
					$CurrentCounter = $this->updateCounter($_POST['DebitNotestart'],$VoucherResult,VOUCHER_DEBIT_NOTE);
					$this->utility->UpdateExVCounter(VOUCHER_DEBIT_NOTE,$CurrentCounter,0);	
				}		
			}
			
			
			if($_POST['Chk_CreditNote'] == true)
			{
				$CounterDetails = $this->utility->GetCounter(VOUCHER_CREDIT_NOTE,0,false);
				$VoucherResult = $this->utility->CheckVoucherCounterExit(VOUCHER_CREDIT_NOTE,0,true, true, $FromDate, $EndDate);
				//var_dump($VoucherResult);
				if(sizeof($VoucherResult) > 0)
				{
					$CurrentCounter = $this->updateCounter($_POST['CreditNote'],$VoucherResult,VOUCHER_CREDIT_NOTE);
					$this->utility->UpdateExVCounter(VOUCHER_CREDIT_NOTE,$CurrentCounter,0);	
				}		
			}
			
			$CashLedgerDetails = $this->utility->GetBankLedger($_SESSION['default_cash_account']);
			
			for($i = 0 ; $i < sizeof($CashLedgerDetails); $i++)
			{
				if($_POST['CashPay'.$i] == true)
				{
					$CounterDetails = $this->utility->GetCounter(VOUCHER_PAYMENT,$CashLedgerDetails[$i]['id'],false);
					$VoucherResult = $this->utility->CheckVoucherCounterExit(VOUCHER_PAYMENT,$CashLedgerDetails[0]['id'],true, true, $FromDate, $EndDate);
					
					if(sizeof($VoucherResult) > 0)
					{
						$CurrentCounter = $this->updateCounter($_POST['CashPaystart'.$i],$VoucherResult,0);
						$this->utility->UpdateExVCounter(VOUCHER_PAYMENT,$CurrentCounter,$CashLedgerDetails[$i]['id']);
					}
				}
				
				if($_POST['CashReceive'.$i] == true)
				{
					$CounterDetails = $this->utility->GetCounter(VOUCHER_RECEIPT,$CashLedgerDetails[$i]['id'],false);
					$VoucherResult = $this->utility->CheckVoucherCounterExit(VOUCHER_RECEIPT,$CashLedgerDetails[0]['id'],true, true, $FromDate, $EndDate);
					
					if(sizeof($VoucherResult) > 0)
					{
						$CurrentCounter = $this->updateCounter($_POST['CashReceivestart'.$i],$VoucherResult,0);
						$this->utility->UpdateExVCounter(VOUCHER_RECEIPT,$CurrentCounter,$CashLedgerDetails[$i]['id']);
					}	
				}
	
			}
						
			
			if($_POST['IsSameCntApply'] == 1)
			{
				if($_POST['SingleRcptCnt'] == true)
				{
					$CounterDetails = $this->utility->GetCounter(VOUCHER_RECEIPT,0,false);
					$VoucherResult = $this->utility->CheckVoucherCounterExit(VOUCHER_RECEIPT,0,true, true, $FromDate, $EndDate);
					
					if(sizeof($VoucherResult) > 0)
					{
						$CurrentCounter = $this->updateCounter($_POST['SingleVcrCntRcptSrt'],$VoucherResult,0);
						$this->utility->UpdateExVCounter(VOUCHER_RECEIPT,$CurrentCounter,0);
					}
				}
				if($_POST['SinglePayCnt'] == true)
				{
					$CounterDetails = $this->utility->GetCounter(VOUCHER_PAYMENT,0,false);
					$VoucherResult = $this->utility->CheckVoucherCounterExit(VOUCHER_PAYMENT,0,true, true,$FromDate, $EndDate);
					
					if(sizeof($VoucherResult) > 0)
					{
						$CurrentCounter = $this->updateCounter($_POST['SingleVcrCntPaySrt'],$VoucherResult,0);
						$this->utility->UpdateExVCounter(VOUCHER_PAYMENT,$CurrentCounter,0);
					}
				}
							
			}
			else
			{
				$NumberOfBank = $_POST['NumberOfBank'] ;

				 for($i = 0; $i < $NumberOfBank; $i++ )
                    {
						 $BnkLedgerID = $_POST['LedgerID'.$i];
						
                         if($_POST['BankReceipt'.$i] == true)
						 {
							 $CounterDetails = $this->utility->GetCounter(VOUCHER_RECEIPT, $BnkLedgerID, false);
							 $VoucherResult = $this->utility->CheckVoucherCounterExit(VOUCHER_RECEIPT, $BnkLedgerID, true, true, $FromDate, $EndDate);
							 
							 if(sizeof($VoucherResult) > 0)
							 {
								$CurrentCounter = $this->updateCounter($_POST['BnkLedgerStartRcpValue'.$i],$VoucherResult,0);
								$this->utility->UpdateExVCounter(VOUCHER_RECEIPT,$CurrentCounter,$BnkLedgerID);	 
							 }		 
						 }
						 
						 if($_POST['BankPayment'.$i] == true)
						 {
							 $CounterDetails = $this->utility->GetCounter(VOUCHER_PAYMENT, $BnkLedgerID, false);
							 $VoucherResult = $this->utility->CheckVoucherCounterExit(VOUCHER_PAYMENT, $BnkLedgerID, true, true, $FromDate, $EndDate);
	 
						     if(sizeof($VoucherResult) > 0)
							 {
								$CurrentCounter = $this->updateCounter($_POST['BnkLedgerStartPayValue'.$i],$VoucherResult,0);
							 	$this->utility->UpdateExVCounter(VOUCHER_PAYMENT,$CurrentCounter,$BnkLedgerID);
							 }
						 }        
                    }	
			}		
		}
		return "Update";
	}
	
	public function updateCounter($Counter,$Voucher,$VoucherType)
	{
		for($i = 0 ; $i < sizeof($Voucher); $i++)
		{
			$this->m_dbConn->update("update voucher set ExternalCounter = '".$Counter."' WHERE VoucherNo = '".$Voucher[$i]['VoucherNo']."' ");	
			
			if($VoucherType == VOUCHER_INVOICE)
			{
				$setInvoiceNumbr = $this->m_dbConn->update("Update sale_invoice set Inv_Number = '".$Counter."' WHERE ID = '".$Voucher[$i]['RefNo']."'");
			}
			else if($VoucherType == VOUCHER_CREDIT_NOTE || $VoucherType == VOUCHER_DEBIT_NOTE)
			{
				$setDebitOrCreditNumbr = $this->m_dbConn->update("Update credit_debit_note set Note_No = '".$Counter."' WHERE ID = '".$Voucher[$i]['RefNo']."'");
			}
			$Counter++;
		}
		return $Counter-1;	
	}
	
	public function CounterValidationReport($VoucherType,$LedgerID)
	{
		//****Declaring the array to manage Counter Report 
		//***DuplicateCounterDetails array store duplicate Counter And it's Voucher Number
		$DuplicateCounterDetails = array();
		//*** ValidCounter array store Unique and not duplicate Counter
		$ValidCounter = array();
		//**index map of counters
		$CounterIndexMap = array();
		//***Start Counter use in for loop to set start value
		$StartCounter = 0;
		//***End Counter use in for loop to set end value
		$EndCounter = 0;
		
		//***Fetching result from voucher Table by passing voucher Type and LedgerID if it exit else 0 
		$result = $this->utility->CheckVoucherCounterExit($VoucherType, $LedgerID, true, false, $_SESSION['from_date'], $_SESSION['to_date']);
		//var_dump($result);

		//***Pushing  data in $CounterIndexMap so we know index of counters
		for($x = 0 ; $x < sizeof($result); $x++)
		{
			array_push($CounterIndexMap,$result[$x]['ExternalCounter']);
		}

		$Counter = 0;
		
		for($i = 0; $i < sizeof($result); $i++)
		{
			//***If first check in ValidCounter Array whether this entry already came in loop if yes then second time for same value it consider as duplicate value and push into duplicateCounter array
			if(in_array($result[$i]['ExternalCounter'],$ValidCounter))
			{
				//***We are Checking Duplicate Counter how many time in Result and collect the data till all duplicate counter remove from CounterIndex
				//when second time duplicate value come then we no need to go with while loop because data already store in DuplicateCounterDetails
				while(array_search($result[$i]['ExternalCounter'],$CounterIndexMap) <> false)
				{
					$index = array_search($result[$i]['ExternalCounter'],$CounterIndexMap);
					
					$DuplicateCounterDetails['duplicate'][$Counter]['ExitingCounter'] = $result[$index]['ExternalCounter'];
					$DuplicateCounterDetails['duplicate'][$Counter]['ExitingVoucher'] = $result[$index]['VoucherNo'];
					$DuplicateCounterDetails['duplicate'][$Counter]['Date'] = $result[$index]['Date'];
					$DuplicateCounterDetails['duplicate'][$Counter]['Amount'] = $result[$index]['Amount'];
					$DuplicateCounterDetails['duplicate'][$Counter]['RefNo'] = $result[$index]['RefNo']; 
					$Counter++;
					unset($CounterIndexMap[$index]);
				}
			}
			else
			{
				//**Non Duplicate values pushing into valid Counter Array 
				array_push($ValidCounter,$result[$i]['ExternalCounter']);	
			}
		}
	
		///** Very Fisrt value of valid counter
		$DefaultCounterDetails = $this->utility->GetCounter($VoucherType, $LedgerID, false);
		
	 
		
		$StartCounter = $DefaultCounterDetails[0]['StartCounter'];
		//** Last Value of Valid Counter
		$EndCounter = $DefaultCounterDetails[0]['CurrentCounter'];		
		
		if($EndCounter < max($ValidCounter))
		{
			$EndCounter = max($ValidCounter);
		}
	
		//**Temp value to check Number is missing or not
		$temp = $StartCounter;
		//Total Number of missing Counter
		$MissingCounter = 0;
		for($j = $StartCounter ; $j < $EndCounter; $j++)
		{
			//***Here it start checking whether entry present or not in Valid Counter
			if(!in_array($temp,$ValidCounter))
			{
				//**If it was not in array then number is missing
				if($temp !== 0 && $temp <> '')
				{
					//***So we Push that number in Missing Counter array	
					$DuplicateCounterDetails['Missing'][$MissingCounter] = $temp;
					$MissingCounter++;
				}
			}
			//***If Number was not missing then we increament the counter to next one and so on till last counter
			$temp = $temp+1;
		}
		return 	$DuplicateCounterDetails;
	}
	
	public function displayErrorReport($errorDetails, $voucherType, $LedgerName, $LedgerID,$IsSameCntApply = false,$IsCashMode = false)
	{
		$Name = '';
		$urlPage = '';
		// It is write to access only show once missing counter of all bank when single counter is set for al bank
		$AccessSingleCounterMissingNumber = false;
		
		//var_dump($errorDetails);
		if(sizeof($errorDetails) > 0)
		{
			for($i = 0 ; $i < sizeof($errorDetails['duplicate']);$i++)
			{
				if($voucherType == VOUCHER_JOURNAL)
				{
					$Name = $LedgerName;
					$urlPage = 'VoucherEdit.php?Vno='.$errorDetails['duplicate'][$i]['ExitingVoucher'];		
				}
				else if($voucherType == VOUCHER_INVOICE)
				{
					$Name = $LedgerName;
					$InvoiceDetails = $this->getDetailsForUrl($voucherType, $errorDetails['duplicate'][$i]['RefNo']);
					$urlPage = "Invoice.php?UnitID=".$InvoiceDetails[0]['UnitID']."&inv_number=".$InvoiceDetails[0]['Inv_Number'];		
				}
				else if($voucherType == VOUCHER_CREDIT_NOTE || $voucherType == VOUCHER_DEBIT_NOTE)
				{
					$Name = $LedgerName;
					$CreditORDebitDetails = $this->getDetailsForUrl($voucherType, $errorDetails['duplicate'][$i]['RefNo']);
					$urlPage = "Invoice.php?debitcredit_id=".$errorDetails['duplicate'][$i]['RefNo']."&UnitID=".$CreditORDebitDetails[0]['UnitID']."&NoteType=".$CreditORDebitDetails[0]['Note_Type'];		
				}
				else if($voucherType == VOUCHER_PAYMENT)
				{
					$Name = $LedgerName.' payment';
					if($IsCashMode == true)
					{
						$urlPage = "PaymentDetails.php?bankid=".$LedgerID."&LeafID=-1&report=".$errorDetails['duplicate'][$i]['RefNo'];						
					}
					else if($voucherType == VOUCHER_PAYMENT)
					{
						$PaymentDetails = $this->getDetailsForUrl($voucherType,$errorDetails['duplicate'][$i]['RefNo']);
						$urlPage = "PaymentDetails.php?bankid=".$LedgerID."&LeafID=".$PaymentDetails[0]['ChqLeafID']."&CustomLeaf=".$PaymentDetails[0]['CustomLeaf'];						
					}
				}
				else if($voucherType == VOUCHER_RECEIPT)
				{
					$Name = $LedgerName.' RECEIPT';
					
					if($IsCashMode == true)
					{
						$urlPage = "ChequeDetails.php?depositid=-3&bankid=".$LedgerID;
					}
					
					if($voucherType == VOUCHER_RECEIPT)
					{
						if($PaymentDetails[0]['DepositGrp'] == -2)
						{
							$urlPage = "NeftDetails.php?bankid=".$LedgerID;
						}
						else
						{
							$PaymentDetails = $this->getDetailsForUrl($voucherType,$errorDetails['duplicate'][$i]['RefNo']);
							$urlPage = "ChequeDetails.php?depositid=".$PaymentDetails[0]['DepositGrp']."&bankid=".$LedgerID;	
						}
					}
				}
				
				echo "<br>".$Name." Voucher Number : ".$errorDetails['duplicate'][$i]['ExitingCounter'];
				echo "<br>Internal Ref No .: ". $errorDetails['duplicate'][$i]['ExitingVoucher'];
				echo "<br>Voucher Date : ". getDisplayFormatDate($errorDetails['duplicate'][$i]['Date']);
				echo "<br>Amount : ". $errorDetails['duplicate'][$i]['Amount'];
				echo "<br><font color='#FF0000' >**Error**Voucher Number is duplicate.</font>";
				echo $Url =	"&nbsp;&nbsp;<a href='' onClick=\"window.open('". $urlPage ."','popup','type=fullWindow,fullscreen,scrollbars=yes');\"><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a><hr />";	
			}
			
			if($IsSameCntApply == 1)
			{
				if($voucherType <> VOUCHER_RECEIPT && $voucherType <> VOUCHER_PAYMENT)
				{
					$this->showMissingCounter($errorDetails['Missing'],$Name);
				}
			}
			else
			{
				$this->showMissingCounter($errorDetails['Missing'],$Name);
			}
		}
		else
		{
			echo "<br><font color='#339933' >No duplicate entries .</font>";
		}		
	}	
	
	public function showMissingCounter($MissingCounterData,$Name,$IsSameBankCounter = 0)
	{
		if(sizeof($MissingCounterData) > 0)
		{ 
			echo "<br><left><span style = 'font-size:23px;padding-bottom:10px;'>".$Name." VOUCHER NUMBER MISSING"; 
			if($IsSameBankCounter == 1)
			{
				echo " FOR ALL BANKS";
			}
			
			echo "</span></left><br />";	
			
			for($i = 0 ; $i < sizeof($MissingCounterData);$i++)
			{
				echo "<br><label>Voucher Number : ".$MissingCounterData[$i]."</label>";
			}
		}
		
	}
	
	public function getDetailsForUrl($voucherType,$refID)
	{
		if($voucherType == VOUCHER_RECEIPT)
		{
			return $Result = $this->m_dbConn->select("SELECT  DepositGrp, ChkDetailID FROM bankregister as bnk JOIN chequeentrydetails chq ON bnk.ChkDetailID = chq.id WHERE chq.ID = '".$refID."'");
		}
		else if($voucherType == VOUCHER_PAYMENT)
		{
			return $Result = $this->m_dbConn->select("SELECT  ChqLeafID, CustomLeaf FROM paymentdetails as pd JOIN chequeleafbook chqlb ON pd.ChqLeafID = chqlb.id WHERE pd.id = '".$refID."'");
		}
		else if($voucherType == VOUCHER_INVOICE)
		{
			return $Result = $this->m_dbConn->select("SELECT  Inv_Number, UnitID FROM sale_invoice  WHERE ID = '".$refID."'");
		}
		else if($voucherType == VOUCHER_CREDIT_NOTE || VOUCHER_DEBIT_NOTE)
		{
			return $Result = $this->m_dbConn->select("SELECT  UnitID,Note_Type FROM credit_debit_note  WHERE ID = '".$refID."'");
		}
	}
}
?>
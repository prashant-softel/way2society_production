<?php if(!isset($_SESSION)){ session_start(); } ?>
<?php
	include_once("utility.class.php");
	include_once("dbconst.class.php");

	error_reporting(0);	
	
	class RegistersValidation
	{
		public $m_dbConn;
		public $m_objUtility;
		public $isDelete;
		private $GroupArray = array();
		private $LedgerIDArray = array();
		function __construct($dbConn)
		{
			$this->m_dbConn = $dbConn;
			$this->m_objUtility = new utility($dbConn);
			$this->isDelete = false;
		}
		
		public function getSocietyName()
		{
			$sql = "SELECT `society_name` FROM `society` WHERE `society_id` = '".$_SESSION['society_id']."'";			
			$result = $this->m_dbConn->select($sql);
			return $result[0]['society_name'];
		}
		
		public function getLedgerName($lid)
		{
			$sql = "SELECT `ledger_name` FROM `ledger` WHERE `id` = '".$lid."'";			
			$result = $this->m_dbConn->select($sql);
			return $result[0]['ledger_name'];
		}	
		
		public function processVoucherTable($VoucherDetails,$IsDateWrong = false, $IsByorToEmpty = false,$IsZeroDate = false)
		{
			$Total = 0;
			for($i = 0; $i < sizeof($VoucherDetails); $i++)
			{
				$head = '';
				$AdditionalHead = '';
				
				$GroupID = '';
				$LedgerID = '';
				if($VoucherDetails[$i]['RefTableID'] == TABLE_BILLREGISTER)
				{
					$BillDetailsQuery = "SELECT UnitID, PeriodID,BillType FROM billdetails where ID ='".$VoucherDetails[$i]['RefNo']."'";
					$BillDetails =  $this->m_dbConn->select($BillDetailsQuery);	
				
					if(empty($BillDetails))
					{
						$AdditionalHead = '<br>Data not exits in billdetails table';
						$url = "";
						$head = "bill";
					}
					else
					{
						$LedgerID = $BillDetails[0]['UnitID'];
						$Ledger_index = array_search($LedgerID,$this->LedgerIDArray);
						$GroupID = $this->GroupArray[$Ledger_index];
						$url = "Maintenance_bill.php?UnitID=".$BillDetails[0]['UnitID']."&PeriodID=".$BillDetails[0]['PeriodID']."&BT=".$BillDetails[0]['BillType'];
						if($BillDetails[0]['BillType'] == 0)
						{
							$head = "Maintenance Bill";
						}
						else
						{
							$head = "Supplementry Bill";
						}	
					}
				}
				else if($VoucherDetails[$i]['RefTableID'] == TABLE_CHEQUE_DETAILS)
				{
					$chequeentrydetailsQuery = "SELECT ID, BankID,DepositID,PaidBy FROM chequeentrydetails where ID ='".$VoucherDetails[$i]['RefNo']."'";
					$chequeentrydetails =  $this->m_dbConn->select($chequeentrydetailsQuery);
					
					if(empty($chequeentrydetails))
					{
						$head = "Receipt";
						$url = "";
						$AdditionalHead = "<br>Data not exits in chequeentrydetails table";
					}
					else
					{
						$LedgerID = $chequeentrydetails[0]['PaidBy'];
						$Ledger_index = array_search($LedgerID,$this->LedgerIDArray);
						$GroupID = $this->GroupArray[$Ledger_index];
						if($DepositID == DEPOSIT_NEFT)
						{
							$head = "NEFT Receipt";
							$url = "NeftDetails.php?bankid=".$chequeentrydetails[0]['BankID']."&edt=".$chequeentrydetails[0]['ID'];	
						}
						else if($DepositID == DEPOSIT_CASH)
						{
							$head = "Cash Receipt";
							$url = "ChequeDetails.php?depositid=".DEPOSIT_CASH."&bankid=".$chequeentrydetails[0]['BankID']."&edt=".$chequeentrydetails[0]['ID'];
						}
						else
						{
							$head = "Deposit Cheque (Reciept)";
							$url = "ChequeDetails.php?depositid=".$chequeentrydetails[0]['DepositID']."&bankid=".$chequeentrydetails[0]['BankID']."&edt=".$chequeentrydetails[0]['ID'];	
						}
					}
				}
				else if($VoucherDetails[$i]['RefTableID'] == TABLE_PAYMENT_DETAILS)
				{
					$head = "Payment";
					$paymentdetailsQuery = "SELECT p.id,p.PayerBank,p.ChqLeafID,p.PaidTo,c.CustomLeaf FROM paymentdetails as p JOIN chequeleafbook as c ON p.ChqLeafID = c.id where p.id = '".$VoucherDetails[$i]['RefNo']."'";
					$paymentdetails =  $this->m_dbConn->select($paymentdetailsQuery);
					if(empty($paymentdetails))
					{
						$AdditionalHead = '<br>Data not exits in payment table';
						$url = "";
					}
					else
					{
						$LedgerID = $paymentdetails[0]['PaidTo'];
						$Ledger_index = array_search($LedgerID,$this->LedgerIDArray);
						$GroupID = $this->GroupArray[$Ledger_index];
						$url = "PaymentDetails.php?bankid=".$paymentdetails[0]['PayerBank']."&LeafID=".$paymentdetails[0]['ChkLeafID']."&CustomLeaf=".$paymentdetails[0]['CustomLeaf']."&edt=".$paymentdetails[0]['id']; 	
					}
				}
				else if($VoucherDetails[$i]['RefTableID'] == TABLE_REVERSAL_CREDITS)
				{
					$head = "Reverse Credit";
					$url = "";	
				}
				else if($VoucherDetails[$i]['RefTableID'] == TABLE_NEFT)
				{
					$head = "Neft";
					$url = "";		
				}
				else if($VoucherDetails[$i]['RefTableID'] == TABLE_FD_MASTER)
				{
					$head = "FD_Master";
					$url = "";
				}
				else if($VoucherDetails[$i]['RefTableID'] == TABLE_FIXEDASSETLIST)
				{
					$head = "FixedAssetList";
					$url = "";
				}
				else if($VoucherDetails[$i]['RefTableID'] == TABLE_SALESINVOICE)
				{
					$head = "Invoice";
					$saleInvoiceQuery = "SELECT UnitID,Inv_Number FROM sale_invoice where ID = '".$VoucherDetails[$i]['RefNo']."'";
					$saleInvoiceDetails =  $this->m_dbConn->select($saleInvoiceQuery);
					if(empty($saleInvoiceDetails))
					{
						$url = "";
						$AdditionalHead = "<br>Data not exits sale_invoice table";
					}
					else
					{
						$LedgerID = $saleInvoiceDetails[0]['UnitID'];
						$Ledger_index = array_search($LedgerID,$this->LedgerIDArray);
						$GroupID = $this->GroupArray[$Ledger_index];
						$url = "Invoice.php?UnitID=".$saleInvoiceDetails[0]['UnitID']."&inv_number=".$saleInvoiceDetails[0]['Inv_Number'];						
					}
				}
				else if($VoucherDetails[$i]['RefTableID'] == TABLE_CREDIT_DEBIT_NOTE)
				{
					$credit_debit_notesQuery = "SELECT ID, UnitID,Note_Type FROM credit_debit_note where ID = '".$VoucherDetails[$i]['RefNo']."'";
					$credit_debit_notedetails =  $this->m_dbConn->select($credit_debit_notesQuery);
					
					if(empty($credit_debit_notedetails))
					{
						$url = "";
						$AdditionalHead = "<br>Data not exits in credit_debit_note table";
					}
					else
					{
						$LedgerID = $credit_debit_notedetails[0]['UnitID'];
						$Ledger_index = array_search($LedgerID,$this->LedgerIDArray);
						$GroupID = $this->GroupArray[$Ledger_index];
						$url = "Invoice.php?debitcredit_id=".$credit_debit_notedetails[0]['ID']."&UnitID=".$credit_debit_notedetails[0]['UnitID']."&NoteType=".$credit_debit_notedetails[0]['Note_Type'];
						if($credit_debit_notedetails[0]['Note_Type'] == CREDIT_NOTE)
						{
							$head = "Credit Note";	
						}
						else
						{
							$head = "Debit Note";
						}	
					}
				}
				
				$middelbodypart = '';
				$middelbodypart .= '<br>Voucher No. :'.$VoucherDetails[$i]['VoucherNo'];
				$middelbodypart .= '<br>Voucher Date : '.$date = getDisplayFormatDate($VoucherDetails[$i]['Date']);
				$middelbodypart .= '<br>Credit total : '.$VoucherDetails[$i]['VoucherCredit'];
				$middelbodypart .= '<br>Debit total : '.$VoucherDetails[$i]['VoucherDebit'];
				
				$ViewImage = '';
				if(!empty($LedgerID) && !empty($GroupID) && !empty($VoucherDetails[$i]['VoucherTypeID']) && !empty($VoucherDetails[$i]['id']))
				{
					$ViewImage .= "<a onClick='ViewVoucherDetail(".$LedgerID.",".$GroupID.",".$VoucherDetails[$i]['VoucherTypeID'].",".$VoucherDetails[$i]['id'].")' style='color:#0000FF;cursor: pointer;'>";
					$ViewImage .= "<img src='images/view.jpg' border='0' alt='View' style='cursor:pointer;' width='18' height='15' />";
					$ViewImage .= "</a>";	
				}
				
				if($IsDateWrong == true)
				{
					echo "<br><font color='#FF0000' >**Error**Wrong Date for ".$head."</font>";
					if(!empty($ViewImage))
					{
						echo $ViewImage;
					}
					echo '<br>Voucher Date : '.$date = $VoucherDetails[$i]['Date'];
					echo $middelbodypart;	
				}
				else if($IsByorToEmpty == true)
				{
					echo "<br><font color='#FF0000' >**Error**Leger  is  Missing By Or To Side for ".$head." in voucher table</font>";
					if(!empty($ViewImage))
					{
						echo $ViewImage;
					}
					echo '<br>Voucher No. :'.$VoucherDetails[$i]['VoucherNo'];
					echo '<br>Voucher Date : '.getDisplayFormatDate($VoucherDetails[$i]['Date']);
					echo '<br>Difference total: '.$VoucherDetails[$i]['due'];
				}
				else if($IsZeroDate == true)
				{
					echo "<br><font color='#FF0000' >**Error**Zero Date voucher entry for ".$head." in voucher table</font>";
					if(!empty($ViewImage))
					{
						echo $ViewImage;
					}
					echo '<br>Voucher No. :'.$VoucherDetails[$i]['VoucherNo'];
					echo '<br>Voucher Date : '.getDisplayFormatDate($VoucherDetails[$i]['Date']);	
									
				}
				else
				{
					echo "<br><font color='#FF0000' >**Error**Difference in Debit and Credit side entry for ".$head."</font>&nbsp;&nbsp;";
					if(!empty($ViewImage))
					{
						echo $ViewImage;
					}
					echo $middelbodypart;				
					echo '<br>Difference total (Credit - Debit): '.$VoucherDetails[$i]['due'];
				}
				
				$diff = $VoucherDetails[$i]['due'];
				$Total += $diff ;
				
				if($AdditionalHead <> '')
				{
					echo '<b>'.$AdditionalHead.'</b>';
				}
				if($url <> '')
				{
					echo $Url =	"&nbsp;&nbsp;<a href='' onClick=\"window.open('". $url ."','ViewLedgerPopup','type=fullWindow,fullscreen,scrollbars=yes');\"><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a>";		
				}
				echo '<hr>';
			}
			echo "<br><font color='black'  style='font-size:20px;' >*******Total Difference Found <span color = 'red'>".$Total."</span>**************</font>" ;
			if($IsDateWrong == true || $IsZeroDate)
			{
				echo "<br><font color = 'blue'> Note &nbsp;&nbsp;: </font><font color= 'red'>Please correct the date to reflect this entry in balance sheet</font>";
			}
			echo '<hr>';
		}
		
		public function SetGroupID()
		{
			$query = "SELECT l.id, ac.group_id FROM ledger as l JOIN account_category as ac ON l.categoryid = ac.category_id";
			$LedgerAndGroupDetails = $this->m_dbConn->select($query);
			$this->GroupArray = array_column($LedgerAndGroupDetails,'group_id');
			$this->LedgerIDArray = array_column($LedgerAndGroupDetails,'id');
		}
		
		public function ValidateVoucherTable()
		{
			//This is to get all Ledger group at the begin to validation
 			$this->SetGroupID();
			$Voucherquery = "SELECT id,VoucherNo,`Date`,`By`,`To`,VoucherTypeID,RefNo,RefTableID,sum(Credit) as VoucherCredit, sum(Debit) as VoucherDebit,sum(Credit) - sum(Debit) as due FROM `voucher` group by voucherNo having sum(Credit) != SUM(Debit)";
			$VoucherDetails = $this->m_dbConn->select($Voucherquery);
			
			if(!empty($VoucherDetails))
			{
				if($VoucherDetails[0]['VoucherNo'] !== NULL)
				{
					echo "<br><font color='#0000FF'  style='font-size:20px;'>Validating Voucher Table Entries For Wrong Entry</font><br><br>";
					$this->processVoucherTable($VoucherDetails,false,false,false);	
				}
			}
			
			$WrongDatedEntry = "SELECT v.id,v.VoucherNo,v.Date,v.By,v.To,v.RefNo,v.VoucherTypeID,v.RefTableID,sum(v.Credit) as VoucherCredit, sum(v.Debit) as VoucherDebit,sum(v.Credit) - sum(v.Debit) as due from voucher as v JOIN year as y WHERE v.Date < y.BeginingDate and y.YearID = '".$_SESSION['society_creation_yearid']."'";
			$WrondDatedEntry = $this->m_dbConn->select($WrongDatedEntry);
			
			if(!empty($WrondDatedEntry))
			{
				if($WrondDatedEntry[0]['VoucherNo'] !== NULL)
				{
					echo "<br><font color='#0000FF'  style='font-size:20px;'>Below entries is before society creation date </font><br><br>";
					$this->processVoucherTable($WrondDatedEntry,true,false,true);
				}
			}
			
			$ByOrToquery = "SELECT id,VoucherNo,`Date`,`By`,`To`,RefNo,RefTableID,VoucherTypeID,sum(Credit) as VoucherCredit, sum(Debit) as VoucherDebit,sum(Credit) - sum(Debit) as due FROM voucher where (`By` = '' and `Debit` != '') OR (`To` = '' and `Credit` != '')";
			$ByOrToDetails =  $this->m_dbConn->select($ByOrToquery);
			
			if(!empty($ByOrToDetails))
			{
				if($ByOrToDetails[0]['VoucherNo'] !== NULL)
				{
						echo "<br><font color='#0000FF'  style='font-size:20px;'>Ledger Name is Missing in Voucher Table  </font><br><br>";
						$this->processVoucherTable($ByOrToDetails,false,true,false);
				}
			}
			
			$ZeroDateWithAmountQuery = "SELECT id,VoucherNo,`Date`,`By`,`To`,RefNo,RefTableID,VoucherTypeID,sum(Credit) as VoucherCredit, sum(Debit) as VoucherDebit,sum(Credit) - sum(Debit) as due FROM voucher where `Date` = '0000-00-00' AND (Credit != 0 OR Debit != 0)";
			$ZeroDateWithAmountDetails =  $this->m_dbConn->select($ZeroDateWithAmountQuery);
			
			if(!empty($ZeroDateWithAmountDetails))
			{
				if($ZeroDateWithAmountDetails[0]['VoucherNo'] !== NULL)
				{
					echo "<br><font color='#0000FF'  style='font-size:20px;'>Zero Date entries in Voucher Table  </font><br><br>";
					$this->processVoucherTable($ZeroDateWithAmountDetails,false,false,true);	
				}
			}
		}
		
		
		public function ValidateRegisterEntries($tableName)
		{
			$groupName = '';
			echo "<br><font color='#0000FF'  style='font-size:20px;'>"."Validating  ".$tableName." Entries</font><br><br>";
			$sqlRegister = '';
			$GroupID = 0;
			if($tableName == 'liabilityregister')
			{
				$GroupID = 1;
			}
			else if($tableName == 'assetregister')
			{
				$GroupID = 2;
			}
			else if($tableName == 'incomeregister')
			{
				$GroupID = 3;
			}
			else if($tableName == 'expenseregister')
			{
				$GroupID = 4;
			}
			
			if(isset($_POST["method"]) && $_POST["method"] == "run" && $_POST["cleanInvalidEntries"] == "YES")
			{
				$this->isDelete = true;		
			}
			$sqlFetch = '';
			$sqlFetch =  " select * from `" . $tableName. "`  where `Is_Opening_Balance` = 0 ";
			
			if($this->isDelete == false && isset($_REQUEST['developer']) )
			{
				echo '<br>'.$sqlFetch;
			}
			$result = $this->m_dbConn->select($sqlFetch);
			$isError = false;
			for($i = 0; $i < sizeof($result); $i++)
			{
				$isError = false;
				$amount = 0;
				
				if($result[$i]['Credit'] <> 0)
				{
					$amount = $result[$i]['Credit'];
				}
				else if($result[$i]['Debit'] <> 0)
				{
					$amount = $result[$i]['Debit'];
				}
				
				$arPaidToParentDetails = $this->m_objUtility->getParentOfLedger($result[$i]['LedgerID']);
				$PaidToGroupID = "";
				
				//get group of individual ledger
				if(!(empty($arPaidToParentDetails)))
				{
					$PaidToGroupID = $arPaidToParentDetails['group'];
					$PaidToCategoryID = $arPaidToParentDetails['category'];
					$PaidToCategoryName = $arPaidToParentDetails['category_name'];
					$PaidToLedgerName = $arPaidToParentDetails['ledger_name'];
					
					if($PaidToGroupID == LIABILITY)
					{
						$groupName = 'Liability';
					}
					else if($PaidToGroupID == ASSET)
					{
						$groupName = 'Asset';	
					}
					else if($PaidToGroupID == INCOME)
					{
						$groupName = 'Income';	
					}
					else if($PaidToGroupID == EXPENSE)
					{
						$groupName = 'Expense';	
					}
					
					if($PaidToGroupID == ASSET  && ($PaidToCategoryID == BANK_ACCOUNT || $PaidToCategoryID == CASH_ACCOUNT))
					{
						$isError = true;
						//bank or cash account entries are in assettable
						
						$sqlAssetRegisterI = " delete *  from `" . $tableName. "` where `LedgerID` = '".$result[$i]['LedgerID']."'	";
						echo "<br><font color='#FF0000' >**Error**Invalid Bank Entry In Register</font>";
						$url ="view_ledger_details.php?lid=".$result[$i]['LedgerID']."&gid=".$GroupID;
						echo $Url =	"&nbsp;&nbsp;<a href='' onClick=\"window.open('". $url ."','ViewLedgerPopup','type=fullWindow,fullscreen,scrollbars=yes');\"><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a>";	
					}
				}
				
				if($GroupID <> 0 && $PaidToGroupID <> "" && $GroupID <> $PaidToGroupID)
				{
					$isError = true;
					//entry in register in not of register type entry
					$sqlRegisterI = " select *  from `" . $tableName. "` where `VoucherID` = '".$result[$i]['VoucherID']."'	";
					
					if($this->isDelete == false)
					{
						echo "<br><font color='#FF0000' >**Error**Invalid Ledger Entry In ".$tableName."</font>";
						$url ="view_ledger_details.php?lid=".$result[$i]['LedgerID']."&gid=".$GroupID;
						echo $Url =	"&nbsp;&nbsp;<a href='' onClick=\"window.open('". $url ."','ViewLedgerPopup','type=fullWindow,fullscreen,scrollbars=yes');\"><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a>";	
						 if(isset($_REQUEST['developer']))
						 {
							echo "<br><font color='#FF0000' >query".$sqlRegisterI.'</font>';	
						 }
					}
					
						
				}
				if(($PaidToGroupID == LIABILITY || $PaidToGroupID == ASSET) && $PaidToCategoryID <>  $result[$i]['SubCategoryID'])
				{ 
					$isError = true;
					if($tableName == 'liabilityregister' || $tableName == 'assetregister' )
					{
						echo "<br><font color='#FF0000' >**Error** Invalid Category for ledger found in register.Expected Category Name: [".$PaidToCategoryName."]"."</font>";	
						$link = "ledger.php?edt=".$result[$i]['LedgerID'];	
						echo "&nbsp;<a href='' onClick=\"window.open('". $link ."','popup','type=fullWindow,fullscreen,scrollbars=yes');\"><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a> <br />";						
					}
				}
				
				$sqlYear ='SELECT * FROM `year`';
				$resultYear = $this->m_dbConn->select($sqlYear);
				
				if($result[$i]['Date'] < $resultYear[0]['BeginingDate'])
				{
					$isError = true;
					if($this->isDelete == false)
					{
						echo "<br><font color='#FF0000' >**Error**Entry Date is wrong or invalid</font>";
						echo "<hr>";
					}		
					
				}
				
				//else if($GroupID == $PaidToGroupID)
				{
				
					if($result[$i]['VoucherID'] <> '' && $result[$i]['VoucherID'] > 0)
					{
						
						$sqlVoucher = "select * from `voucher` where `id` = '".$result[$i]['VoucherID']."' ";
						$resultVoucher = $this->m_dbConn->select($sqlVoucher);
						
						if(sizeof($resultVoucher) == 0)
						{
							$isError = true;
							//voucher id not found for register entry
							if($this->isDelete == false)
							{
								echo "<br><font color='#FF0000' >**Error** Voucher Does Not Exists For This Entry</font>";	
							}
							$sqlRegister = " delete from `" . $tableName. "` where Is_Opening_Balance = 0 AND VoucherID != 0 AND `VoucherID` = '".$result[$i]['VoucherID']."'	";
							if($this->isDelete == true)
							{
								$this->m_dbConn->delete($sqlRegister);
							}
							else if(isset($_REQUEST['developer']))
							{
								echo "<br><font color='#FF0000' >query".$sqlRegister.'</font>';	
							}
						}	
					}
				}
				if($isError == true)
				{
					echo '<br>Voucher ID: '.$result[$i]['VoucherID'];
					echo "<br>LedgerName:".$this->getLedgerName($result[$i]['LedgerID']);
					echo "<br>Group:"." [ ".$groupName." ]";
					//echo "<br>Category ID:" . $PaidToCategoryName;
					if(isset($_REQUEST['developer']))
					{
						echo "<br>Entry Details:" . implode(" :: ",$result[$i]);
					}
					else
					{
						echo "<br>Date:" .getDisplayFormatDate($result[$i]['Date']);	
						echo "<br>Amount:" .number_format($amount,2);		
					}	
					if($this->isDelete == false)
					{
						echo "<hr>";
					}
				}
				
			}
			
	
		}
		
		
	public function getdbBackup()
	{

		try
		{
			if($_SERVER['HTTP_HOST']=="localhost")
			{
				$dbhost = 'localhost';
				$dbuser = 'root';
				$dbpass = '';
			}
			else
			{
				$dbhost = 'localhost';
				$dbuser = 'hostmjbt_society';
				$dbpass = 'society123';
			}	
			
			$backup_dir_db = '../ValidationBackup/db/' . date("Ymd");
			if (!file_exists($backup_dir_db)) {
				mkdir($backup_dir_db, 0777, true);
			}
			
			$dbname = $_SESSION['dbname'];
			$backup_file = $backup_dir_db . '/' . $dbname . '_' . date("Y-m-d_H-i-s") . '.sql.gz';
			$command = "mysqldump --opt -h $dbhost -u $dbuser -p$dbpass $dbname | gzip  > " . $backup_file;
			
			//$backup_file = $backup_dir_db . '/' . $dbname . '_' . date("Y-m-d_H-i-s") . '.sql';
			//echo $command = "mysqldump --opt -h $dbhost -u $dbuser -p $dbpass $dbname> " . $backup_file;
			
			system($command, $retval);
			if($retval == 0)
			{
				return "success";
			}
			
			
		}
		catch(Exception $e)
		{
			echo $e;
			return "fail";	
		}
	}
		
	function getLedgerNameArray()
	{
		$arr = array();
		$ledQuery = 'SELECT `id`, `ledger_name` FROM `ledger`';
		$res = $this->m_dbConn->select($ledQuery);						
		for($i = 0; $i < sizeof($res); $i++)
		{			
			$arr[$res[$i]['id']]= $res[$i]['ledger_name'];			
		}
		return $arr;
	}
	
	
	public function CheckVoucherType($VoucherID)
{
	$Url = '';
	
	$sql03 = "select `VoucherNo` from `voucher` where `id` = '".$VoucherID."' ";
	$data03 = $this->m_dbConn->select($sql03);
	
	$sql = "select * from `voucher` where `VoucherNo` = '".$data03[0]['VoucherNo']."' ";
	$data = $this->m_dbConn->select($sql);
	if($data <> "")
	{
		//check if jv exists in payment by voucher id
		$checkPaymentEntry = "select * from `paymentdetails` where `VoucherID` = '".$data[0]['id']."' ";
		$res2 = $this->m_dbConn->select($checkPaymentEntry);
		
		if(sizeof($res2) > 0)
		{
			//jv exists in payment means jv is of payment type
			$sql01 = "select * from `voucher` where `RefNo` = '".$res2[0]['id']."' and `RefTableID` = '3'  and `By` > 0 ";
			$res01 = $this->m_dbConn->select($sql01);
			
			$customLeafQuery = "SELECT `CustomLeaf` FROM `chequeleafbook` WHERE `id` = ".$res2[0]['ChqLeafID'];				
			$result = $this->m_dbConn->select($customLeafQuery);
			
			if($result[0]['CustomLeaf'] == -1)
			{
				$Url = "PaymentDetails.php?bankid=".$res01[0]['By']."&LeafID=".$res2[0]['ChqLeafID']."&edt=".$res2[0]['id'];																	
			}
			else
			{
				$Url = "PaymentDetails.php?bankid=".$res01[0]['By']."&LeafID=".$res2[0]['ChqLeafID']."&CustomLeaf= ". $result[0]['CustomLeaf']. "&edt=".$res2[0]['id'];																	
			}
			//echo $Url;
			return $Url;	
		}
		else
		{
			$checkPaymentEntry = "select * from `paymentdetails` where `Amount` = '".$data[1]['Credit']."' and `PaidTo` = '".$data[1]['To']."' and `InvoiceDate` = '".$data[1]['Date']."' ";
			$res3 = $this->m_dbConn->select($checkPaymentEntry);
			if(sizeof($res3) > 1)
			{
				//multiple entries fetched
				for($i=0; $i < sizeof($res3); $i++)
				{
					//search payment id in voucher
					$sql2 = "select * from `voucher` where `RefNo` = '".$res3[$i]['id']."' and `RefTableID` = '3' ";
					$res4 = $this->m_dbConn->select($sql2); 
					if(sizeof($res4) > 1)
					{
						if($res4[0]['VoucherNo'] == $data[0]['VoucherNo'] + 1)
						{
							//jv voucherno and payment voucher number match
							$customLeafQuery = "SELECT `CustomLeaf` FROM `chequeleafbook` WHERE `id` = ".$res3[$i]['ChqLeafID'];				
							$result = $this->m_dbConn->select($customLeafQuery);
							
							if($result[0]['CustomLeaf'] == -1)
							{
								$Url = "PaymentDetails.php?bankid=".$res4[0]['By']."&LeafID=".$res3[$i]['ChqLeafID']."&edt=".$res3[$i]['id'];																	
							}
							else
							{
								$Url = "PaymentDetails.php?bankid=".$res4[0]['By']."&LeafID=".$res3[$i]['ChqLeafID']."&CustomLeaf= ". $result[0]['CustomLeaf']. "&edt=".$res3[$i]['id'];																	
							}
							//echo $Url;
							return $Url;	
						}
						else
						{
							//no record found	
							//echo "test";
						}	
					}	
				}	
			}
			else if(sizeof($res3) == 1)
			{
				//one entry fetched	
				$sql2 = "select * from `voucher` where `RefNo` = '".$res3[0]['id']."' and `RefTableID` = '3' ";
				$res04 = $this->m_dbConn->select($sql2); 
				
				$customLeafQuery = "SELECT `CustomLeaf` FROM `chequeleafbook` WHERE `id` = ".$res3[0]['ChqLeafID'];				
				$result = $this->m_dbConn->select($customLeafQuery);
				
				if($result[0]['CustomLeaf'] == -1)
				{
					$Url = "PaymentDetails.php?bankid=".$res04[0]['By']."&LeafID=".$res3[0]['ChqLeafID']."&edt=".$res3[0]['id'];																	
				}
				else
				{
					$Url = "PaymentDetails.php?bankid=".$res04[0]['By']."&LeafID=".$res3[0]['ChqLeafID']."&CustomLeaf= ". $result[0]['CustomLeaf']. "&edt=".$res3[0]['id'];																	
				}
				//echo $Url;
				return $Url;	
			}
			else
			{
				//no record found		
			}
				
		}
	}
	
	return '';
}


// Delete Corrupted entry from bank register and voucher type table
public function DeleteCorruptedBankRegister($tbleID,$tbleName)
{
	$select = "SELECT * FROM `" . $tbleName. "` WHERE id='".$tbleID."'";
	$result = $this->m_dbConn->select($select);
	
	$chkid = $result[0]['ChkDetailID'];
	$VoucherType =$result[0]['VoucherTypeID'];
	$ChequeDate = $result[0]['Cheque Date'];
	$Amount = 0;
	if($result[0]['PaidAmount'] == 0)
	{
		$Amount =$result[0]['ReceivedAmount'];
	}
	else
	{
		$Amount =$result[0]['PaidAmount'];
	}
	//voucher type = 2  payment 3- chequ  6 = both 
	// $deleteEntry = "DELETE FROM `paymentdetails` WHERE `id`='".$chkid."' and `ChequeDate` = '".$ChequeDate."' AND `Amount` ='".$Amount."' ";
	 //$deleteCheques = "DELETE FROM `chequeentrydetails` WHERE `id`='".$chkid."' and `ChequeDate` = '".$ChequeDate."' AND `Amount` ='".$Amount."'  ";

	
	if($VoucherType == VOUCHER_PAYMENT)
	{
	 	$deletePaymeny = "DELETE FROM `paymentdetails` WHERE `id`='".$chkid."'  AND `Amount` ='".$Amount."' ";
	 	$result = $this->m_dbConn->delete($deletePaymeny);
	}
	elseif($VoucherType == VOUCHER_RECEIPT)
	{
	 	$deleteCheques = "DELETE FROM `chequeentrydetails` WHERE `id`='".$chkid."'  AND `Amount` ='".$Amount."'  ";
	 	$result = $this->m_dbConn->delete($deleteCheques);
	}
	elseif($VoucherType == VOUCHER_CONTRA)
	{
	 	$deletePaymeny = "DELETE FROM `paymentdetails` WHERE `id`='".$chkid."'  AND `Amount` ='".$Amount."' ";
		$result = $this->m_dbConn->delete($deletePaymeny);
	    $deleteCheques = "DELETE FROM `chequeentrydetails` WHERE `id`='".$chkid."'  AND `Amount` ='".$Amount."'  ";
		$result1 = $this->m_dbConn->delete($deleteCheques);
	}
	$deleteAsset = "DELETE FROM `" . $tbleName. "` WHERE id='".$tbleID."'";
	$result2 = $this->m_dbConn->delete($deleteAsset);
	return $result2;
}

// Delete Corrupted entry from  register type table
public function DeleteCorruptedRegister($tbleID,$tbleName)
{
	
	$deleteAsset = "DELETE FROM `" . $tbleName. "` WHERE id='".$tbleID."'";
	$result = $this->m_dbConn->delete($deleteAsset);
	return $result;
	
}

}

	
?>


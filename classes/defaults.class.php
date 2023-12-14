<?php if(!isset($_SESSION)){ session_start(); } ?>

<?php
	
	include_once('dbconst.class.php');
	include_once('changelog.class.php');
	include_once('utility.class.php');
	//Defaults : ID's in appdefault table in DB
	define ('APP_DEFAULT_YEAR', 'APP_DEFAULT_YEAR');
	define ('APP_DEFAULT_PERIOD', 'APP_DEFAULT_PERIOD');
	
	define ('APP_DEFAULT_INTEREST_ON_PRINCIPLE_DUE', 'APP_DEFAULT_INTEREST_ON_PRINCIPLE_DUE');
	define ('APP_DEFAULT_PENALTY_TO_MEMBER', 'APP_DEFAULT_PENALTY_TO_MEMBER');
	define	('APP_DEFAULT_BANK_CHARGES', 'APP_DEFAULT_BANK_CHARGES');
	define	('APP_DEFAULT_TDS_PAYABLE', 'APP_DEFAULT_TDS_PAYABLE');
	define	('APP_DEFAULT_TDS_RECEIVABLE', 'APP_DEFAULT_TDS_RECEIVABLE');
	define	('APP_DEFAULT_IMPOSE_FINE', 'APP_DEFAULT_IMPOSE_FINE');       /// impose fine
	define ('APP_DEFAULT_INCOME_EXPENDITURE_ACCOUNT', 'APP_DEFAULT_INCOME_EXPENDITURE_ACCOUNT');
	define ('APP_DEFAULT_ADJUSTMENT_CREDIT', 'APP_DEFAULT_ADJUSTMENT_CREDIT');
	define ('APP_DEFAULT_SUSPENSE_ACCOUNT', 'APP_DEFAULT_SUSPENSE_ACCOUNT');
	define ('APP_DEFAULT_LEDGER_ROUND_OFF', 'APP_DEFAULT_LEDGER_ROUND_OFF');	
	define ('APP_DEFAULT_IGST', 'APP_DEFAULT_IGST'); 	
	define ('APP_DEFAULT_CGST', 'APP_DEFAULT_CGST'); 	
	define ('APP_DEFAULT_SGST', 'APP_DEFAULT_SGST'); 	
	define ('APP_DEFAULT_CESS', 'APP_DEFAULT_CESS'); 	
	define ('APP_DEFAULT_CESS', 'APP_DEFAULT_CESS'); 	
	define ('APP_DEFAULT_INPUT_CGST', 'APP_DEFAULT_INPUT_CGST'); 
	define ('APP_DEFAULT_INPUT_SGST', 'APP_DEFAULT_INPUT_SGST');	
	define ('APP_DEFAULT_INPUT_IGST', 'APP_DEFAULT_INPUT_IGST'); 
	
	define ('APP_DEFAULT_CURRENT_ASSET', 'APP_DEFAULT_CURRENT_ASSET');
	define ('APP_DEFAULT_DUE_FROM_MEMBERS', 'APP_DEFAULT_DUE_FROM_MEMBERS');
	define ('APP_DEFAULT_CONTRIBUTION_FROM_MEMBERS', 'APP_DEFAULT_CONTRIBUTION_FROM_MEMBERS');
	define ('APP_DEFAULT_BANK_ACCOUNT', 'APP_DEFAULT_BANK_ACCOUNT');
	define ('APP_DEFAULT_CASH_ACCOUNT', 'APP_DEFAULT_CASH_ACCOUNT');	
	define ('APP_DEFAULT_SOCIETY', 'APP_DEFAULT_SOCIETY');
	define ('APP_DEFAULT_SINKING_FUND', 'APP_DEFAULT_SINKING_FUND');
	define ('APP_DEFAULT_INVESTMENT_REGISTER', 'APP_DEFAULT_INVESTMENT_REGISTER');
	//define ('APP_DEFAULT_EMAILID', 'APP_DEFAULT_EMAILID');
	
class defaults
{
	public $actionPage = "../defaults.php";
	public $m_dbConn;
	public $m_objLog;
	public $m_dbConnRoot;
	
	function __construct($dbConn, $dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->m_objLog = new changeLog($dbConn);
		$this->obj_utility = new utility($dbConn);
		if($dbConnRoot == NULL)
		{
			//echo "in if not found";
			$Root = new dbop(true);
			$this->m_dbConnRoot = $Root;
		}
		else
		{
			//echo "in if found";
			$this->m_dbConnRoot = $dbConnRoot;
		}
	}
	
	public function CheckCounterData($YearID)
	{
		$SQL = "Select * from vouchercounter where YearID = '".$YearID."'";
		$RESULTSQL = $this->m_dbConn->select($SQL);
		$TableEntry = sizeof($RESULTSQL);
		if($TableEntry == 0 || $TableEntry == '')
		{
			$RESULTSQL = 0;
		}
		else if($TableEntry <> 0 && $TableEntry <> '')
		{
			$RESULTSQL = 1;
		}
		return  $RESULTSQL;	
	}
	
	public function getDefaultCategory($ledgerId,$category)
	{
	
		if($category <> 1)
		{
			$FetchCategoryIDFromLedger = "select  c.group_id from ledger l join account_category as c  ON c.category_id = l.categoryid where id='".$ledgerId."'	";
			$result= $this->m_dbConn->select($FetchCategoryIDFromLedger);
			return $result;
		}
		else if($category == 1)
		{
			$FetchCategoryIDFromCategory = "select group_id from account_category where category_id='".$ledgerId."' "; 
			$result= $this->m_dbConn->select($FetchCategoryIDFromCategory);
			return $result;
		}
		
	}

	public function getDefaults($society_id, $bSetToSession = true)
	{
		$bSuccess = false;
		
		$sqlDefault = "select * from `appdefault` where " . APP_DEFAULT_SOCIETY . "=" . $society_id;
		
		//echo $sqlDefault;
		$resDefault = $this->m_dbConn->select($sqlDefault);
		
		if($bSetToSession)
		{
			$_SESSION['default_year'] = 0;
			$_SESSION['default_period'] = 0;
			$_SESSION['default_interest_on_principle'] = 0;
			$_SESSION['default_penalty_to_member'] = 0;
			$_SESSION['default_bank_charges'] = 0;
			$_SESSION['default_tds_payable'] = 0;
			$_SESSION['default_tds_receivable'] = 0;
			$_SESSION['default_impose_fine'] = 0;   // impose fine
			$_SESSION['default_current_asset'] = 0;
			$_SESSION['default_fixed_asset'] = 0;
			$_SESSION['default_bank_account'] = 0;
			$_SESSION['default_cash_account'] = 0;
			$_SESSION['default_due_from_member'] = 0;
			$_SESSION['default_contribution_from_member'] = 0;
			$_SESSION['APP_DEFAULT_SUNDRY_DEBETOR'] = 0;
			$_SESSION['default_income_expenditure_account'] = 0;
			$_SESSION['default_adjustment_credit'] = 0;
			$_SESSION['default_suspense_account'] = 0;
			$_SESSION['default_ledger_round_off'] = 0;
			$_SESSION['igst_service_tax'] = 0;
			$_SESSION['cgst_service_tax'] = 0;
			$_SESSION['sgst_service_tax'] = 0;
			$_SESSION['cess_service_tax'] = 0;
			$_SESSION['sgst_input'] = 0;
			$_SESSION['cgst_input'] = 0;
			$_SESSION['igst_input'] = 0;
			$_SESSION['society_id'] = 0;
			$_SESSION['default_year_start_date'] = 0;
			$_SESSION['default_year_end_date'] = 0;
			$_SESSION['is_year_freeze'] = 0;
			$_SESSION['mem_other_id'] = 0;
			$_SESSION['apply_gst'] = 0;
			$_SESSION['apply_NEFT'] =0;
			$_SESSION['apply_paytm'] =0;
			$_SESSION['paytm_link'] =0;
			$_SESSION['APP_DEFAULT_SUNDRY_CREDITOR'] = 0;
			$_SESSION['default_sinking_fund'] = 0;
			$_SESSION['default_investment_register'] = 0;
			//$_SESSION['defaultEmailID'] = "";
		
			if($resDefault<>"")
			{
				$_SESSION['default_year'] = $resDefault[0][APP_DEFAULT_YEAR];;
				$_SESSION['default_period'] = $resDefault[0][APP_DEFAULT_PERIOD];
				$_SESSION['default_interest_on_principle'] = $resDefault[0][APP_DEFAULT_INTEREST_ON_PRINCIPLE_DUE];
				$_SESSION['default_penalty_to_member'] = $resDefault[0][APP_DEFAULT_PENALTY_TO_MEMBER];
				$_SESSION['default_bank_charges'] = $resDefault[0][APP_DEFAULT_BANK_CHARGES];
				$_SESSION['default_tds_payable'] = $resDefault[0][APP_DEFAULT_TDS_PAYABLE];
				$_SESSION['default_tds_receivable'] = $resDefault[0][APP_DEFAULT_TDS_RECEIVABLE];
				$_SESSION['default_impose_fine'] = $resDefault[0][APP_DEFAULT_IMPOSE_FINE];  // impose fine
				$_SESSION['default_current_asset'] = $resDefault[0][APP_DEFAULT_CURRENT_ASSET];
				$_SESSION['default_fixed_asset'] = $resDefault[0][APP_DEFAULT_FIXED_ASSET];
				$_SESSION['default_bank_account'] = $resDefault[0][APP_DEFAULT_BANK_ACCOUNT];
				$_SESSION['default_cash_account'] = $resDefault[0][APP_DEFAULT_CASH_ACCOUNT];
				$_SESSION['default_due_from_member'] = $resDefault[0][APP_DEFAULT_DUE_FROM_MEMBERS];
				$_SESSION['default_contribution_from_member'] = $resDefault[0][APP_DEFAULT_CONTRIBUTION_FROM_MEMBERS];
				$_SESSION['default_Sundry_debetor'] = $resDefault[0][APP_DEFAULT_SUNDRY_DEBETOR];
				$_SESSION['default_income_expenditure_account'] = $resDefault[0][APP_DEFAULT_INCOME_EXPENDITURE_ACCOUNT];
				$_SESSION['default_adjustment_credit'] = $resDefault[0][APP_DEFAULT_ADJUSTMENT_CREDIT];
				$_SESSION['default_suspense_account'] = $resDefault[0][APP_DEFAULT_SUSPENSE_ACCOUNT];
				$_SESSION['default_ledger_round_off'] = $resDefault[0][APP_DEFAULT_LEDGER_ROUND_OFF];
				$_SESSION['igst_service_tax'] = $resDefault[0][APP_DEFAULT_IGST];
				$_SESSION['cgst_service_tax'] = $resDefault[0][APP_DEFAULT_CGST];
				$_SESSION['sgst_service_tax'] = $resDefault[0][APP_DEFAULT_SGST];
				$_SESSION['cess_service_tax'] = $resDefault[0][APP_DEFAULT_CESS];
				$_SESSION['sgst_input'] = $resDefault[0][APP_DEFAULT_INPUT_SGST];
				$_SESSION['cgst_input'] = $resDefault[0][APP_DEFAULT_INPUT_CGST];
				$_SESSION['igst_input'] = $resDefault[0][APP_DEFAULT_INPUT_IGST];
				$_SESSION['society_id'] = $resDefault[0][APP_DEFAULT_SOCIETY];
                $_SESSION['default_Sundry_creditor'] = $resDefault[0][APP_DEFAULT_SUNDRY_CREDITOR];
				$_SESSION['default_sinking_fund']  = $resDefault[0][APP_DEFAULT_SINKING_FUND];
				$_SESSION['default_investment_register'] =  $resDefault[0][APP_DEFAULT_INVESTMENT_REGISTER];

				$result = $this -> getGSTapply();
				$_SESSION['apply_gst'] = $result[0]['apply_service_tax'];
				$_SESSION['apply_NEFT'] = $result[0]['Record_NEFT'];
				$_SESSION['apply_paytm'] =$result[0]['PaymentGateway'];
				$_SESSION['paytm_link'] =$result[0]['Paytm_Link'];
				$bSuccess = true;
				$_SESSION['show_counter'] = false;
				if($_SESSION['default_year'] >= 6)
				{
					$_SESSION['show_counter'] = true;
				}
			}
		}
		
		$sqlIV = "SELECT `current_map_year` FROM  `mapping` where  login_id = '" . $_SESSION['login_id'] . "'  and `society_id` = '" . $_SESSION['society_id'] . "'  and  `unit_id` = '".$_SESSION['unit_id']."' ";
		$resIV = $this->m_dbConnRoot->select($sqlIV);
		
		if($resIV[0]['current_map_year'] == 0)
		{
			$_SESSION['default_year'] = $resDefault[0][APP_DEFAULT_YEAR];
		}
		else
		{
			$_SESSION['default_year'] = $resIV[0]['current_map_year'];
			$resDefault[0][APP_DEFAULT_YEAR] = $resIV[0]['current_map_year'];
		}
			
		$sqlSociety = "SELECT `society_creation_yearid`,`gst_start_date` FROM `society`  where `society_id`  = ".$society_id;
		$resSociety = $this->m_dbConn->select($sqlSociety);
		$_SESSION['society_creation_yearid'] = $resSociety[0]['society_creation_yearid'];
		
		$sqlUnitblock = "SELECT `block_unit` FROM `unit`  where `unit_id`  = ".$_SESSION['unit_id'];
		$resUnit = $this->m_dbConn->select($sqlUnitblock);
		$_SESSION['unit_blocked'] = $resUnit[0]['block_unit'];
		
		$_SESSION['gst_start_date'] = $resSociety[0]['gst_start_date'];
		
		$sqlYear = "SELECT * FROM `year`  where `YearID`  = ".$_SESSION['default_year'];
		$resYear = $this->m_dbConn->select($sqlYear);
		$_SESSION['is_year_freeze'] = $resYear[0]['is_year_freeze'];

		$sql_email = "Select member_id from login where login_id = '" . $_SESSION['login_id'] . "'";
		$result_email = $this->m_dbConnRoot->select($sql_email);

		if($result_email <> '')
		{
			$sql_mem_other = "Select mo.`mem_other_family_id` from `mem_other_family` as mo JOIN member_main as mm on mo.member_id = mm.member_id JOIN unit as u ON mm.unit = u.unit_id WHERE u.unit_id = '" . $_SESSION['unit_id'] . "' and mo.other_email = '" . $result_email[0]['member_id'] . "'";
			$result_mem_other = $this->m_dbConn->select($sql_mem_other);

			if($result_mem_other <> '')
			{
				$_SESSION['mem_other_id'] = $result_mem_other[0]['mem_other_family_id'];
			}
		}
		
		$this->setSessionDates();
		return $resDefault;
	}
	
	public function SetDefaultVoucherCounter()
	{
		$Result = false;
		$CheckCounterTablePresent = $this->m_dbConn->select("Select * from vouchercounter where YearID = '".$_SESSION['default_year']."'");
		
		$CounterLedgerDetails = array(); 
		if(sizeof($CheckCounterTablePresent) == 0)
		{
			$LedgerDetails = array("VoucherType"=>VOUCHER_JOURNAL,"LedgerID"=>0,"StartCnt"=>1,"CurrentCnt"=>1);
			array_push($CounterLedgerDetails,$LedgerDetails);
			$LedgerDetails = array("VoucherType"=>VOUCHER_INVOICE,"LedgerID"=>0,"StartCnt"=>1,"CurrentCnt"=>1);
			array_push($CounterLedgerDetails,$LedgerDetails);
			$LedgerDetails = array("VoucherType"=>VOUCHER_CREDIT_NOTE,"LedgerID"=>0,"StartCnt"=>1,"CurrentCnt"=>1);
			array_push($CounterLedgerDetails,$LedgerDetails);
			$LedgerDetails = array("VoucherType"=>VOUCHER_DEBIT_NOTE,"LedgerID"=>0,"StartCnt"=>1,"CurrentCnt"=>1);
			array_push($CounterLedgerDetails,$LedgerDetails);
			
			$CashLedgerDetails = $this->obj_utility->GetBankLedger($_SESSION['default_cash_account']);
			for($i = 0 ; $i < sizeof($CashLedgerDetails); $i++)
			{
				$LedgerDetails = array("VoucherType"=>VOUCHER_RECEIPT,"LedgerID"=>$CashLedgerDetails[$i]['id'],"StartCnt"=>1,"CurrentCnt"=>1);
				array_push($CounterLedgerDetails,$LedgerDetails);
				$LedgerDetails = array("VoucherType"=>VOUCHER_PAYMENT,"LedgerID"=>$CashLedgerDetails[$i]['id'],"StartCnt"=>1,"CurrentCnt"=>1);
				array_push($CounterLedgerDetails,$LedgerDetails);
				
			}
			$BnkLedgerDetails = $this->obj_utility->GetBankLedger($_SESSION['default_bank_account']);
			
			for($i = 0; $i < sizeof($BnkLedgerDetails); $i++ )
			{
				$LedgerDetails = array("VoucherType"=>VOUCHER_RECEIPT,"LedgerID"=>$BnkLedgerDetails[$i]['id'],"StartCnt"=>1,"CurrentCnt"=>1);
				array_push($CounterLedgerDetails,$LedgerDetails);
				$LedgerDetails = array("VoucherType"=>VOUCHER_PAYMENT,"LedgerID"=>$BnkLedgerDetails[$i]['id'],"StartCnt"=>1,"CurrentCnt"=>1);
				array_push($CounterLedgerDetails,$LedgerDetails);
			}
			
			$this->UpdateCounter($_SESSION['default_year'],0,$CounterLedgerDetails);
			
			$Result = true;
		}
		return $Result;
	}
	
	
	
			
	public function resetDefaults()
	{
		$_SESSION['default_year'] = 0;
		$_SESSION['default_period'] = 0;
		$_SESSION['default_interest_on_principle'] = 0;
		$_SESSION['default_penalty_to_member'] = 0;
		$_SESSION['default_bank_charges'] = 0;
		$_SESSION['default_tds_payable'] = 0;
		$_SESSION['default_tds_receivable'] = 0;
		$_SESSION['default_impose_fine'] = 0;        //impose fine
		$_SESSION['default_current_asset'] = 0;
		$_SESSION['default_fixed_asset'] = 0;
		$_SESSION['default_bank_account'] = 0;
		$_SESSION['default_cash_account'] = 0;
		$_SESSION['default_due_from_member'] = 0;
		$_SESSION['default_Sundry_debetor'] = 0;
		$_SESSION['default_income_expenditure_account'] = 0;
		$_SESSION['default_adjustment_credit'] = 0;
		$_SESSION['default_suspense_account'] = 0;
		$_SESSION['default_ledger_round_off'] = 0;
		$_SESSION['igst_service_tax'] = 0;
		$_SESSION['cgst_service_tax'] = 0;
		$_SESSION['sgst_service_tax'] = 0;
		$_SESSION['cess_service_tax'] = 0;
		$_SESSION['sgst_input'] = 0;
		$_SESSION['cgst_input'] = 0;
		$_SESSION['igst_input'] = 0;
		$_SESSION['society_id'] = 0;
		$_SESSION['profile'] = '';
		$_SESSION['mem_other_id'] = 0;
		$_SESSION['default_Sundry_creditor'] = 0;
		$_SESSION['default_sinking_fund'] = 0;
		$_SESSION['default_investment_register'] = 0;
		//$_SESSION['defaultEmailID'] = '';
	}
	public function UpdateCounter($Year,$IsbankCounterChk,$CounterDetails)
	{
		for($i = 0 ; $i < sizeof($CounterDetails) ; $i++)
		{
			//First we check whether data exits or not in the counter table
			$CheckTableISEmpty = "Select * from vouchercounter where YearID = '".$Year."' AND `VoucherType` = '".$CounterDetails[$i]['VoucherType']."' AND `LedgerID` = '".$CounterDetails[$i]['LedgerID']."' ";
			$ResultCheckTableISEmpty = $this->m_dbConn->select($CheckTableISEmpty);
			
			//If data not exits then we insert in the table
			if(sizeof($ResultCheckTableISEmpty) == 0 || sizeof($ResultCheckTableISEmpty) == '')
			{
				$InsertCounter = "Insert into vouchercounter (`YearID`,`VoucherType`, `LedgerID`,`StartCounter`,`CurrentCounter`) values ('".$Year."','".$CounterDetails[$i]['VoucherType']."','".$CounterDetails[$i]['LedgerID']."','".$CounterDetails[$i]['StartCnt']."','".$CounterDetails[$i]['CurrentCnt']."')";
				$ResultCounter = $this->m_dbConn->insert($InsertCounter);	
			}
			//IF data exits we simply update 
			else if(sizeof($ResultCheckTableISEmpty) <> 0 || sizeof($ResultCheckTableISEmpty) <> '')
			{
				$UpdateCounter = "update vouchercounter set `StartCounter` = '".$CounterDetails[$i]['StartCnt']."', `CurrentCounter` = '".$CounterDetails[$i]['CurrentCnt']."' where VoucherType = '".$CounterDetails[$i]['VoucherType']."' AND LedgerID = '".$CounterDetails[$i]['LedgerID']."' AND YearID = '".$Year."'";
				$ResultCounter = $this->m_dbConn->update($UpdateCounter);
			}
		}	
				$sqlUpdate = $this->m_dbConn->update("UPDATE `appdefault` SET `APP_DEFAULT_SINGLE_COUNTER` = '".$IsbankCounterChk."' WHERE APP_DEFAULT_SOCIETY = '" . $_SESSION['society_id'] . "' ");
				$_SESSION['APP_DEFAULT_SINGLE_COUNTER'] = $IsbankCounterChk;
	}        
		
	public function setDefault($Society, $Year, $Period, $Interest, $penalty, $bankCharges,$tdsPayable, $CurrentAsset, $MemberDue, $SundryDebetor,  $BankAcc, $CashAcc,$defaultIncomeExpenditureAccount, $defaultAdjustmentCredit,$igstServiceTax,$cgstSertviceTax,$sgstServiceTax,$cessServiceTax,$ImposeFine,$InputCgst,$InputSgst, $fixedAsset ,$contributionfrommember,$tdsReceivable,$defaultSuspenseAccount, $defaultLedgerRoundOff, $SundaryCreditor, $InputIgst,$defaultSinkingFund, $defaultInvestmentRegister /*$defaultServiceTax ,$defaultEmailID*/)
	{
		//echo "bank : ". $bankCharges;
		//$sqlUpdate = "UPDATE `appdefault` SET `Value`= '" . $value . "' WHERE ID = '" . $id . "'";
		$sqlFetch = "SELECT * from `society` where `society_id` = '" . $Society . "' ";
		$res00 = $this->m_dbConn->select($sqlFetch);
		//$EmailChnage = "Prev EmailID: <". $res00[0]['email']."> Current EmailID: <". $defaultEmailID.">";
		 $sqlUpdate = "UPDATE `appdefault` SET `APP_DEFAULT_YEAR`='" . $Year . "', `APP_DEFAULT_PERIOD`='" . $Period . "', `APP_DEFAULT_INTEREST_ON_PRINCIPLE_DUE`= '" . $Interest . "', `APP_DEFAULT_PENALTY_TO_MEMBER`= '" . $penalty . "', `APP_DEFAULT_BANK_CHARGES`= '" . $bankCharges . "',`APP_DEFAULT_TDS_PAYABLE`= '" . $tdsPayable . "', `APP_DEFAULT_TDS_RECEIVABLE`= '" . $tdsReceivable . "',`APP_DEFAULT_CURRENT_ASSET`= '" . $CurrentAsset . "', `APP_DEFAULT_DUE_FROM_MEMBERS`= '" . $MemberDue . "', `APP_DEFAULT_CONTRIBUTION_FROM_MEMBERS`= '" . $contributionfrommember . "',`APP_DEFAULT_SUNDRY_DEBETOR`= '" . $SundryDebetor . "', `APP_DEFAULT_INCOME_EXPENDITURE_ACCOUNT`= '" . $defaultIncomeExpenditureAccount . "', `APP_DEFAULT_BANK_ACCOUNT`= '" . $BankAcc . "', `APP_DEFAULT_CASH_ACCOUNT`= '" . $CashAcc . "', `APP_DEFAULT_ADJUSTMENT_CREDIT`= '" . $defaultAdjustmentCredit .  "', `APP_DEFAULT_SUSPENSE_ACCOUNT`= '" . $defaultSuspenseAccount .  "', `APP_DEFAULT_LEDGER_ROUND_OFF`= '" . $defaultLedgerRoundOff .  "', `APP_DEFAULT_IGST`= '" . $igstServiceTax .  "', `APP_DEFAULT_CGST`= '" . $cgstSertviceTax .  "', `APP_DEFAULT_SGST`= '" . $sgstServiceTax .  "', `APP_DEFAULT_CESS`= '" . $cessServiceTax .  "',`APP_DEFAULT_IMPOSE_FINE`= '" . $ImposeFine .  "', `APP_DEFAULT_INPUT_CGST`= '".$InputCgst."', `APP_DEFAULT_INPUT_SGST`='".$InputSgst."', `APP_DEFAULT_FIXED_ASSET` = '".$fixedAsset."', `changed_by`= '" . $_SESSION['login_id'] . "', `APP_DEFAULT_SINGLE_COUNTER` = '".$IsbankCounterChk."',
		 `APP_DEFAULT_SUNDRY_CREDITOR` = '". $SundaryCreditor ."',`APP_DEFAULT_INPUT_IGST`='".$InputIgst."',`APP_DEFAULT_SINKING_FUND`= '" . $defaultSinkingFund .  "',`APP_DEFAULT_INVESTMENT_REGISTER`= '" . $defaultInvestmentRegister .  "'  WHERE APP_DEFAULT_SOCIETY = '" . $Society . "'";
			
		//$sqlUpdate = "UPDATE `appdefault` SET `APP_DEFAULT_PERIOD`='" . $Period . "', `APP_DEFAULT_INTEREST_ON_PRINCIPLE_DUE`= '" . $Interest . "', `APP_DEFAULT_PENALTY_TO_MEMBER`= '" . $penalty . "', `APP_DEFAULT_BANK_CHARGES`= '" . $bankCharges . "',`APP_DEFAULT_TDS_PAYABLE`= '" . $tdsPayable . "', `APP_DEFAULT_CURRENT_ASSET`= '" . $CurrentAsset . "', `APP_DEFAULT_DUE_FROM_MEMBERS`= '" . $MemberDue . "', `APP_DEFAULT_INCOME_EXPENDITURE_ACCOUNT`= '" . $defaultIncomeExpenditureAccount . "', `APP_DEFAULT_BANK_ACCOUNT`= '" . $BankAcc . "', `APP_DEFAULT_CASH_ACCOUNT`= '" . $CashAcc . "', `APP_DEFAULT_ADJUSTMENT_CREDIT`= '" . $defaultAdjustmentCredit .  "', `APP_DEFAULT_SERVICE_TAX`= '" . $defaultServiceTax .  "', `changed_by`= '" . $_SESSION['login_id'] . "' WHERE APP_DEFAULT_SOCIETY = '" . $Society . "'";
		
		$result = $this->m_dbConn->update($sqlUpdate);
		
		$sqlYear = " UPDATE  `mapping` SET `current_map_year` = '" . $Year . "'  WHERE login_id = '" . $_SESSION['login_id'] . "'  and `society_id` = '" . $_SESSION['society_id'] . "' and  `unit_id` = '".$_SESSION['unit_id']."' ";	
		$resultYear = $this->m_dbConnRoot->update($sqlYear);
		
		//$sqlSocietyUpdate = "UPDATE `society` SET `email` = '" . $defaultEmailID .  "' where `society_id` = '" . $Society . "' ";
		
		//$res = $this->m_dbConn->update($sqlSocietyUpdate);
		
		//$this->m_objLog->setLog($EmailChnage, $_SESSION['login_id'], 'society', '-');
		
		$_SESSION['society_id'] = $Society;
		
		//$sqlUpdate = "UPDATE `login` SET `current_society`='" . $Society . "' WHERE login_id = '" . $_SESSION['login_id'] . "'";
		//$resultUpdate = $this->m_dbConn->update($sqlUpdate);
		
		return $result;
	}
	
	public function combobox($query, $id, $defaultText = '')
	{
		if($defaultText <> '')
		{
			$str = '<option value="0">' . $defaultText . '</option>';
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
					//else
					//{
					//	$str.=$v."</OPTION>";
					//}
					$i++;
				}
			}
		}
		return $str;
	}
	
	public function getSocietyName($SocietyID)
	{
		$sql = "Select society_name from society where society_id = '" . $SocietyID . "'";
		$result = $this->m_dbConn->select($sql);
		return $result[0]['society_name'];
	}
	
	public function getMemberID($SocietyID,$UnitID)
	{
		$sql="select member_id from `member_main` where unit='".$UnitID."' and  society_id = '" . $SocietyID . "' AND `ownership_status`=1";
		$res=$this->m_dbConn->select($sql);
		 return $res[0]['member_id'];		
	}
	
	public function getGSTapply()
	{
	 $sql="select apply_service_tax,Record_NEFT,PaymentGateway,Paytm_Link from society";
  	 $result=$this->m_dbConn->select($sql);
  	// echo $result;
  	 return $result;
	}
	
	public function setSessionDates()
	{
		if($_SESSION['default_year'] <> "" && $_SESSION['default_year'] > 0)
		{
			$sqlFetchDate = "select `YearID`,`YearDescription`,`BeginingDate`,`EndingDate`,`status` from `year` where `YearID` = '".$_SESSION['default_year']."' and `status` = 'Y' ";
			$resFetchDate = $this->m_dbConn->select($sqlFetchDate);
			
			if($resFetchDate <> "")
			{
				$_SESSION['default_year_start_date'] = $resFetchDate[0]['BeginingDate'];
				$_SESSION['default_year_end_date'] = $resFetchDate[0]['EndingDate'];
				
				$_SESSION['year_description'] = $resFetchDate[0]['YearDescription'];
				$_SESSION['from_date'] = getDisplayFormatDate($resFetchDate[0]['BeginingDate']);
				$_SESSION['to_date'] = getDisplayFormatDate($resFetchDate[0]['EndingDate']);
				?>
				<script>
					localStorage.setItem("minGlobalCurrentYearStartDate", "<?php  echo getDisplayFormatDate($_SESSION['default_year_start_date']);?>");
					localStorage.setItem("maxGlobalCurrentYearEndDate", "<?php  echo getDisplayFormatDate($_SESSION['default_year_end_date']);?>");
				</script>
	<?php }
		}	
		
	}
	function GetYear($yearID)
	{
		$sql= "select * from year where status = 'Y' AND YearID ='".$yearID."'  ORDER BY YearID DESC";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
}
?>
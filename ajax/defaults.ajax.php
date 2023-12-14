<?php 
	if(!isset($_SESSION['sadmin']))
	{
		//header('Location: ../login.php?alog');
	}
?>

<?php
 include_once("../classes/dbconst.class.php");
 include_once("../classes/defaults.class.php");
include_once("../classes/include/dbop.class.php");
	 $dbConn = new dbop();
	 $dbConnRoot = new dbop(true);
	$obj_default = new defaults($dbConn,$dbConnRoot);


	if($_REQUEST['method'] == 'FetchCounterDetails')
	{
		$YearID = $_REQUEST['ChangeYearID'];
		$SQL = "Select VoucherType, StartCounter, CurrentCounter, LedgerID from vouchercounter where YearID = '".$YearID."'";
		$RESULTSQL = $obj_default->m_dbConn->select($SQL);
		echo json_encode($RESULTSQL);
	}
	
	if($_REQUEST['method'] == 'CheckExitingData')
	{
		$YID = $_REQUEST['YearID'];
		$Flag = $obj_default->CheckCounterData($YID);
		echo $Flag;
	}
	
	if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'updateCounter')
	{
		$CounterDetails = json_decode(str_replace('\\', '', $_REQUEST['Counterdata']), true);
		$defaultYear = $_REQUEST['defaultYear'];
		$IsbankCounterChk = $_REQUEST['IsbankCounterChk'];
		$result = $obj_default->UpdateCounter($defaultYear,$IsbankCounterChk,$CounterDetails);
		echo "Voucher Updated Successfully";
		
	}
	if($_REQUEST['method'] == 'GetYearData')
	{
		$YID = $_REQUEST['YearID'];
		$Flag = $obj_default->GetYear($YID);
		//print_r($Flag);
		$YearEndDate =$Flag[0]['EndingDate']; 
		$effectiveDate = date('Y-m-d', strtotime("+6 months", strtotime($YearEndDate)));
		$today= date("Y-m-d");
		$value=0;
		if($Flag[0]['is_year_freeze'] == 0)
		{
			if($effectiveDate<=$today)
			{
				$value = 1;
			}
			else
			{
				$value = 0;
			}
		}
		else
		{
			$value=2;
		}
		$Flag[0]['value']=$value;
		echo json_encode($Flag);
	}
	
	
	
	
	
	
	if(isset($_REQUEST['update']))
	{
		echo 'update';
		$CounterDetails = json_decode(str_replace('\\', '', $_REQUEST['Counterdata']), true);
		$defaultYear = $_REQUEST['defaultYear'];
		$defaultPeriod = $_REQUEST['defaultPeriod'];
		$interestOnPrinciple = $_REQUEST['interestOnPrinciple'];
		$penaltyToMember = $_REQUEST['penaltyToMember'];
		$bankCharges = $_REQUEST['bankCharges'];
		$tdsPayable = $_REQUEST['tdsPayable'];
		$tdsReceivable = $_REQUEST['tdsReceivable'];
		$imposeFine = $_REQUEST['imposeFine'];         // Impose Fine
		$currentAsset = $_REQUEST['currentAsset'];
		$fixedAsset = $_REQUEST['fixedAsset'];
		$dueFromMember = $_REQUEST['dueFromMember'];
		$contributionfrommember = $_REQUEST['contributionfrommember'];
		$sundrydebetor = $_REQUEST['Sundrydebtor'];
		$bankAccount = $_REQUEST['bankAccount'];
		$cashAccount = $_REQUEST['cashAccount'];
		$defaultIncomeExpenditureAccount = $_REQUEST['defaultIncomeExpenditureAccount'];
		$defaultAdjustmentCredit = $_REQUEST['defaultAdjustmentCredit'];
		$defaultSuspenseAccount = $_REQUEST['defaultSuspenseAccount'];
		$defaultLedgerRoundOff = $_REQUEST['defaultLedgerRoundOff'];
		$igstServiceTax = $_REQUEST['igstServiceTax'];
		$cgstServiceTax = $_REQUEST['cgstServiceTax'];
		$sgstServiceTax = $_REQUEST['sgstServiceTax'];
		$cessServiceTax = $_REQUEST['cessServiceTax'];
		$InputCgst = $_REQUEST['cgstInput'];
		$InputSgst = $_REQUEST['sgstInput'];
		 $InputIgst = $_REQUEST['igstInput'];
		$Sundrycreditor = $_REQUEST['Sundrycreditor'];
		$societyID = $_REQUEST['societyid'];
		$defaultSinkingFund    = $_REQUEST['defaultSinkingFund'];
		$defaultInvestmentRegister = $_REQUEST['defaultInvestmentRegister'];
		//$defaultEmailID = $_REQUEST['defaultEmailID'];
		
		$updateDefault = $obj_default->setDefault($societyID, $defaultYear, $defaultPeriod, $interestOnPrinciple, $penaltyToMember, $bankCharges,$tdsPayable, $currentAsset, $dueFromMember,$sundrydebetor, $bankAccount, $cashAccount,$defaultIncomeExpenditureAccount, $defaultAdjustmentCredit, $igstServiceTax, $cgstServiceTax, $sgstServiceTax, $cessServiceTax, $imposeFine, $InputCgst, $InputSgst, $fixedAsset, $contributionfrommember,$tdsReceivable,$defaultSuspenseAccount, $defaultLedgerRoundOff ,$Sundrycreditor,$InputIgst,$defaultSinkingFund,$defaultInvestmentRegister/*,$defaultEmailID*/);
		
		/*$updateDefault = $updateDefault . $obj_default->setDefault(APP_DEFAULT_PERIOD, $defaultPeriod);
		$updateDefault = $updateDefault . $obj_default->setDefault(APP_DEFAULT_INTEREST_ON_PRINCIPLE_DUE, $interestOnPrinciple);
		$updateDefault = $updateDefault . $obj_default->setDefault(APP_DEFAULT_CURRENT_ASSET, $currentAsset);
		$updateDefault = $updateDefault . $obj_default->setDefault(APP_DEFAULT_DUE_FROM_MEMBERS, $dueFromMember);
		$updateDefault = $updateDefault . $obj_default->setDefault(APP_DEFAULT_BANK_ACCOUNT, $bankAccount);
		$updateDefault = $updateDefault . $obj_default->setDefault(APP_DEFAULT_SOCIETY, $societyID);*/
		
		$obj_default->getDefaults($societyID, true);
				
		echo "Defaults Updated Successfully";// . $updateDefault;
	}
	if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'ChangeYear')
	{
		 $defaultYear = "UPDATE `appdefault` SET `APP_DEFAULT_YEAR`='" . $Year . "'  WHERE APP_DEFAULT_SOCIETY = '" . $_SESSION['society_id'] . "'";
		 $res=$obj_default->m_dbConn->update($defaultYear);
		 $_SESSION['default_year'] = $Year;
	}
?>
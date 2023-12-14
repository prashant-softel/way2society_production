<?php include_once "ses_set_as.php"; 
	include_once("classes/include/dbop.class.php");
	include_once("classes/dbconst.class.php");
	include_once("classes/neft.class.php");
	include_once("classes/utility.class.php");
	//include_once("header.php");
	include_once("dbconst.class.php");
	//include_once("includes/head_s.php");
	include_once("classes/PaymentGateway.class.php");
	include_once("classes/utility.class.php");
	include_once("classes/ChequeDetails.class.php");
	include_once("classes/view_member_profile.class.php");

	//echo "test";
//include_once("RightPanel.php");
	$bHasError = false;
	$txnid = "";
	$UnitID = "";
	$bMobileNoProvided = false;
	$bUnitNoProvided = false;
	$bTokenProvided = false;
	$bTokenValid = false;	
	$dbConn = "";
	$obj_utility = null;

	if($_REQUEST["Site_id"] && $_REQUEST["Site_id"] != "")
	{
		if( $_REQUEST["Site_id"] == "59")
		{
			$SocID = $_REQUEST["Site_id"];
			$dbConnRoot = new dbop(true);
			$resDBName = $dbConnRoot->select("select dbname from dbname where society_id='".$SocID."'");
			//print_r($resDBName);
			$dbConn = new dbop(false, $resDBName[0]["dbname"]);
			$obj_utility = new utility($dbConn);
		
			if(isset($_REQUEST["Token"]) && $_REQUEST["Token"] != "")
			{
				$sToken = $_REQUEST["Token"];//its UnitID
				$bTokenProvided = true;
				//echo "tk:".$sToken;
				$arToken = $obj_utility->IsAPITokenValid(2, 0 /*$UnitID*/, $sToken);
				$status = $arToken["status"];
				if($bTrace)
				{
					echo "status is:".$status;
				}
				if($status == "1")
				{
					$bTokenValid = true;
				}
				else
				{
					$sError = "Invalid Token &lt;".$sToken."&gt;";
					$bHasError = true;
				
				}
				if($bTrace)
				{
					echo $sError; 
				}
			}
			else
			{
				$bHasError = true;
				if(!$bTokenProvided)
				{
					$sError = "Token &lt;Tokend&gt; not provided";				
				}
			}
			
		}
		else
		{
			$sError = "Society ID &lt;Site_id&gt; is invalid";
			$bHasError = true;		
		}
	}
	else
	{
		$sError = "Society ID &lt;Site_id&gt; not provided";
		$bHasError = true;
	}
	//echo "soc:".$SocID;
	if($SocID != "" && isset($_REQUEST["Unique_id"]) && $_REQUEST["Unique_id"] != "")
	{
		/*$UniqueID = $_REQUEST["Unique_id"];
		if($UniqueID == "")
		{
			$sError = "Unique_id &lt;Unique_id&gt; not provided4";		
		}
		else if($UniqueID == "0")
		{
			$sError = "Invalid Unique_id &lt;Unique_id&gt; provided";		
		}
		else
		{
			$UniqueID = str_replace(' ', '', $UniqueID);
			if(strlen($UniqueID) == 10)
			{
				$_REQUEST["mob_no"] = $UniqueID;
				
				if($bTrace)
				{
					echo "mobile:".$_REQUEST["mob_no"];
				}

			}
			else
			{
				$sqlUnit = "select unit_id from unit where `unit_no` = '".$UniqueID."'";
				$resUnits = $dbConn->select($sqlUnit);
				if($bTrace)
				{
					print_r($resUnits);
				}
				if(sizeof($resUnits) > 0);
				{
					$_REQUEST["unit_id"] = $resUnits[0]["unit_id"];
				}
			}
		}*/
	}
	else
	{
		if(!$bTokenProvided || !$bTokenValid)
		{

		}
		else if($SocID != "")
		{
			$sError = "Unique_id &lt;Unique_id&gt; not provided";
		}
	}
	$sStart_date = "";
	$sEnd_date = "";
	if(!$bHasError)
	{
		if(isset($_REQUEST["start_date"]) && $_REQUEST["start_date"] != "")
		{
			$sStart_date = $_REQUEST["start_date"];
		}
		else
		{
			$sError = "Start Date &lt;start_date&gt; not provided";		
			$bHasError = true;
		}
		if(isset($_REQUEST["end_date"]) && $_REQUEST["end_date"] != "")
		{
			$sEnd_date = $_REQUEST["end_date"];
		}
		else
		{
			$sError = "End Date &lt;end_date&gt; not provided";
			$bMobileNoProvided = true;
		}
		if(isset($_REQUEST["unit_id"]) && $_REQUEST["unit_id"] != "")
		{
			$UnitID = $_REQUEST["unit_id"];//its UnitID
		}
		else
		{
			$sError = "UniqueID &lt;Unique_ID&gt; not provided";	
			$bUnitNoProvided = true;
			$UnitID = $arToken["unit_id"];//its UnitID
		}
		$UnitID = "0";
		if(!$bMobileNoProvided && !$bUnitNoProvided)
		{
			$bHasError = false;
		}
		$obj_neft = new neft($dbConn);
		$TransactionID = $txnid;
		//echo $TransactionID;
		//$obj_Details = new ChequeDetails($dbConn);
		//$bank_details=$obj_Details->getPayerBankDetails($_SESSION['unit_id']);
		$obj_Payment_Gateway = new PaymentGateway($dbConn);
		//$obj_view_member_profile = new view_member_profile($dbConn);
		//$TotalDueAmount=$obj_utility->getDueAmount($_SESSION['unit_id']);

		//$show_member_main  = $obj_view_member_profile->show_member_main_by_OwnerID();
		//$sql = "select led.id, led.ledger_name from `ledger` AS led JOIN `bank_master` AS bm ON led.id = bm.BankID  where led.categoryid='" . BANK_ACCOUNT . "' AND bm.AllowNEFT=1";
		//echo $sql;
		//$result= $dbConn->select($sql);
		//echo $sql;
		$Timestamp = $Response[0]["TimeStamp"];
		$date = new DateTime($Timestamp);
		$TrnxTimestamp = $date->format('d-m-Y H:i:s');
		//print_r($new_date_format);
		/*if($_SESSION["role"] == ROLE_SUPER_ADMIN && isset($_REQUEST["uid"]))
		{
			$UnitID = $_REQUEST["uid"];
		}
		else
		{
			$LoginID = $_SESSION["unit_id"];
		}*/
		$LoginID = $_SESSION["login_id"]; 
		$arResponse = array();
		$sStatusCode = "";
		$sTrnxID= "";
		$w2sPaymentID = "";
		$sTrnxAmount = "";
		//echo "test".$TransactionID;
		//if($obj_Payment_Gateway->IsTranxIDAlreadyUsed($TransactionID))
		//{
			//echo "unitwise;";
			$arResponse = $obj_Payment_Gateway->GetTransactionDetailsJSON($TransactionID, $UnitID, true, $sStart_date, $sEnd_date);
			//print_r($arResponse);
			//if($arResponse["transact_id"] == "")
			{	
			//	$sError = "TransactionID  &lt;".$TransactionID."&gt; is not found for  &lt;".$UniqueID."&gt; ";
			//	$bHasError = true;
			}
		//}
		//else
		//{
			//$sError = "TransactionID  &lt;".$TransactionID."&gt; is not valid";
			//$bHasError = true;
		//}
	}
	if($bHasError)
	{

		$arResponse["tran_stat"] = $sStatusCode;
		$arResponse["unique_id"] = $UniqueID;
		$arResponse["transact_id"] = "";
		$arResponse["amount"] = $sTrnxAmount;
		$arResponse["status"] = "Failure";
		$arResponse["responseCode"] = $sError;
	}
	echo json_encode($arResponse);
?>
	

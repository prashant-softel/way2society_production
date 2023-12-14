<?php include_once "ses_set_as.php"; 
	include_once("classes/include/dbop.class.php");
	include_once("classes/dbconst.class.php");
	include_once("classes/neft.class.php");
	include_once("classes/utility.class.php");
	//include_once("header.php");
	include_once("dbconst.class.php");
	//include_once("includes/head_s.php");
	include_once("classes/PaymentGateway.class.php");
	include_once("classes/ChequeDetails.class.php");
	include_once("classes/view_member_profile.class.php");

	//echo "test";
//include_once("RightPanel.php");
	$bHasError = 0;
	$txnid = "";
	$bTrace = 0;
	$UnitID = "";
	$bMobileNoProvided = false;
	$bUnitNoProvided = false;
	$bSocietyIDProvided = 0;
	$bUnsupportedSocietyIDProvided = 0;
	$dbConn = "";
	$obj_utility = null;
	if($_REQUEST["Site_id"] && $_REQUEST["Site_id"] != "")
	{
		$sSiteID = $_REQUEST["Site_id"];
		$sql = "select dbname from dbname where society_id='".$sSiteID."'";
			//echo "sql:".$sql;
	 	try
	 	{
		 	$dbConnRoot = new dbop(true);
		 	$resDBName = $dbConnRoot->select($sql);
		 	//echo "soc:";
		 	//print_r($resDBName);
		 	if(isset($resDBName))
		 	{
				$dbConn = new dbop(false, $resDBName[0]["dbname"]);
				$sqlPG = "select PaymentGateway from society";
				$resPGEnabled = $dbConn->select($sqlPG);
			 	$bIsPGEnabled = $resPGEnabled[0]["PaymentGateway"];
			 	//echo "sql:".$sql;
			 	
				$bSocietyIDProvided = 1;
				$SocID = $sSiteID;

			 	if($bIsPGEnabled != "1")
			 	{
			 		$SocID = "";
			 		$bSocietyIDProvided = 0;
				
			 		$sError = "Payment Gateway Not Subscribed for SocietyID &lt;".$sSiteID."&gt;";
				 	$bUnsupportedSocietyIDProvided = 1;
					$bError = true;
				}
				else
				{
					$obj_utility = new utility($dbConn);
				}
			}
			else
			{
				$sError = "Invalid SocietyID &lt;".$sSiteID."&gt; provided";
				$bUnsupportedSocietyIDProvided = 1;
				$bHasError = 1;	
			}
		}
	 	catch(Exception $ex)
	 	{
	 		$bIsPGEnabled = "0";
	 		//echo "Exception occured";
	 	}
	}
	else
	{
		$sError = "Society ID &lt;Site_id&gt; not provided";
		$bHasError = 1;
	}
	//echo "soc:".$SocID;
	//echo "err".$sError;
	if($SocID != "" && isset($_REQUEST["Unique_id"]) && $_REQUEST["Unique_id"] != "" && !$bHasError)
	{
		$UniqueID = $_REQUEST["Unique_id"];
		if($UniqueID == "")
		{
			$sError = "Unique_id &lt;Unique_id&gt; not provided";		
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
			if(isset($_REQUEST["Token"]) && $_REQUEST["Token"] != "")
			{
				$sToken = $_REQUEST["Token"];//its UnitID
				$bTokenProvided = true;
				//echo "tk:".$sToken;
				$arToken = $obj_utility->IsAPITokenValid(2, $UniqueID /*$UnitID*/, $sToken);
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
				}
				if($bTrace)
				{
					echo $sError; 
				}
			}
			else
			{
				if(!$bTokenProvided)
				{
					$sError = "Token &lt;Token&gt; not provided";				
				}
			}
			if(!$bTokenProvided || !$bTokenValid)
			{
				$bHasError = 1;
			}
			if($bTrace)
			{
				
				echo "1 sError".$sError . " error: ".$bHasError;
			}
		}
	}
	else
	{
		//echo "err:".$sError;
		if($SocID != "")
		{
			$sError = "Unique_id &lt;Unique_id&gt; not provided";
		}
		else
		{
			if($bIsPGEnabled != "1")
			{
				$sError = "Payment Gateway Not Subscribed for SocietyID &lt;".$sSiteID."&gt;";
			}
			else if(!$bUnsupportedSocietyIDProvided)
			{
				if($SocID != "")
				{ 
					$sError = "Unique_id ID &lt;Unique_id&gt; not provided";
				}
				else
				{
					$sError = "Society ID &lt;site_id&gt; not provided";
				}
			}
			else
			{
				$sError = "Invalid SocietyID &lt;".$sSiteID."&gt; provided";
			}
			echo $sError;
		}
	}
	if($bTrace)
	{	
		echo "2 sError".$sError . " error: ".$bHasError;
	}
	if(!$bHasError)
	{	
		if(isset($_REQUEST["TrnxID"]) && $_REQUEST["TrnxID"] != "")
		{
			$txnid = $_REQUEST["TrnxID"];
		}
		else
		{
			$sError = "Transaction ID &lt;TrnxID&gt; not provided";		
			$bHasError = 1;
		}
		if(isset($_REQUEST["mob_no"]) && $_REQUEST["mob_no"] != "")
		{
			$mob_no = $_REQUEST["mob_no"];
		}
		else
		{
			//$sError = "Mobile Number &lt;mob_no&gt; not provided";
			$bMobileNoProvided = true;
		}
		if($txnid == "")
		{
			$status = "Failure";
		}
		else
		{
			$status = "Success";
		}
		if(isset($_REQUEST["unit_id"]) && $_REQUEST["unit_id"] != "")
		{
			$UnitID = $_REQUEST["unit_id"];//its UnitID
		}
		else
		{
			//$sError = "UnitID &lt;UnitID&gt; not provided";	
			$bUnitNoProvided = true;
		}
		//$UnitID = "0";
		if((!$bMobileNoProvided && !$bUnitNoProvided ) || $txnid == "")
		{
			$bHasError = 1;
		}
		if($bTrace)
		{
			echo "unit_id:".$UnitID;
		}
		if($bTrace)
		{
			echo "sError".$sError . " has error:".$bHasError;
		}
		if(!$bHasError)
		{
			$obj_neft = new neft($dbConn);
			$TransactionID = $txnid;
			//echo $TransactionID;
			$obj_utility = new utility($dbConn);
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
			if($obj_Payment_Gateway->IsTranxIDAlreadyUsed($TransactionID))
			{
				//echo "unitwise;";
				$arResponse = $obj_Payment_Gateway->GetTransactionDetailsJSON($TransactionID, $UnitID);
				//print_r($arResponse);
				if($arResponse["paytm_tran_id"] == "")
				{	
					$sError = "TransactionID  &lt;".$TransactionID."&gt; is not found for  &lt;".$UniqueID."&gt; ";
					$bHasError = 1;
				}
			}
			else
			{
				$sError = "TransactionID  &lt;".$TransactionID."&gt; is not valid";
				$bHasError = 1;
			}
		}
	}
	if($bHasError)
	{
		$arResponse["tran_stat"] = $sStatusCode;
		$arResponse["unique_id"] = $UniqueID;
		$arResponse["paytm_tran_id"] = $TransactionID;
		$arResponse["w2s_transact_id"] = "";
		$arResponse["amount"] = $sTrnxAmount;
		$arResponse["status"] = "Failure";
		$arResponse["responseCode"] = $sError;
	}
	echo json_encode($arResponse);
?>
	

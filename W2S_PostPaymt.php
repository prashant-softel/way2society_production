<?php

	try
	{
		//error_reporting(7);
		$bTrace = 0;
		if(isset($_REQUEST["Trace"]))
		{
			$bTrace = 1;	
		}
		if($bTrace)
		{
			echo " 0";
		}
		include_once("classes/include/dbop.class.php");
		if($bTrace)
		{
			echo " 1";
		}
		include_once("classes/dbconst.class.php");
		if($bTrace)
		{
			echo "2";
		}
		include_once("classes/ChequeDetails.class.php");
		if($bTrace)
		{
			echo " 3";
		}
		include_once("classes/include/fetch_data.php");
		if($bTrace)
		{
			echo "obj1";
		}
		include_once("classes/PaymentGateway.class.php");
		if($bTrace)
		{
			echo "obj2";
		}
		include_once("classes/utility.class.php");
		//echo "incl";
		if($bTrace)
		{
			echo "obj3";
		}
		$bHasError = false;
		$PGClientID = "2";
		$bTokenProvided = false;
		$bTokenValid = false;
		$obj_utility = null;
		if($PGClientID == "1")  // PayUMoney
		{
			$status=$_POST["status"];
			$unmapstatus=$_POST["unmappedstatus"];
			$firstname=$_POST["firstname"];
			$amount=$_POST["amount"];
			$txnid=$_POST["txnid"];
			$posted_hash=$_POST["hash"];
			$key=$_POST["key"];
			$productinfo=$_POST["productinfo"];
			$Comments = $productinfo;
			$email=$_POST["email"];
			$payuMoneyId=$_POST["payuMoneyId"];
			$LoginID = $_REQUEST["LID"];
			$PaidBy = $_REQUEST["UID"];//its UnitID
			$PaidTo = $_REQUEST["PT"];
			$BillType = $_REQUEST["BT"];
			$Mode = $_REQUEST["mode"];
			$BankCode = $_REQUEST["bankcode"];
			$bank_ref_num = $_REQUEST["bank_ref_num"];
			$amount_split = $_REQUEST["amount_split"];	
			$Date = Date('Y-m-d');
			
			$salt = $_SESSION["PGSalt"];
			//echo "LID:".$LoginID." date:".$Date ." txnid: ". $txnid ." amt:". $amount ." paidby:". $PaidBy ." paidto:". $PaidTo." bankcode:". $BankCode ." none:". "-" ."type:". "-2" ."Comments:". $Comments ."BT:". $_REQUEST['BT'];
			If (isset($_POST["additionalCharges"])) 
			{
				$additionalCharges=$_POST["additionalCharges"];
				$retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;	
			}
			else 
			{	  
				$retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
			}
			//echo "posted_hash:".$posted_hash;
			//echo "hash:".$hash;
			$hash = hash("sha512", $retHashSeq);
				 
		   	if ($hash != $posted_hash) 
		   	{
		   		echo "Invalid Transaction. Please try again";
			   	$bHasError = true;
		   	}
			//$salt="GQs7yium";
			//$salt="e5iIg1jwi8";
			//echo "<h3>Thank You. Your order status is ". $status .".</h3>";
			//echo "<h4>Your Transaction ID for this transaction is ".$txnid.".</h4>";
			//echo "<h4>We have received a payment of Rs. " . $amount . ". Your order will soon be shipped.</h4>";

			// echo "<b>Transaction Completed successfully.<b> Note: This is demo transaction.";
			
			// echo $LoginID."|".$PaidBy."|".$PaidTo."|".$Date."|".$amount."|".$BillType."|".$txnid."|".$status."|".$payuMoneyId."|".$Mode."|".$Comments;

			$BankCode= "PayU-".$BankCode;
			//       echo "<br>".$Date."|". $Date."|". $txnid."|". $amount."|". $PaidBy."|". $PaidTo."|". $BankCode;
			//         echo "<br>".$Comments."|". $BillType;	
			$dbConn = new dbop();
			//$obj_neft = new neft($m_dbConn);
			$obj_ChequeDetails = new ChequeDetails($dbConn);

			$obj_Payment_Gateway = new PaymentGateway($dbConn);
					
		}
		else if($PGClientID == "2") // PayTM
		{
			$strRequest = "";
			foreach($_REQUEST as $paramName => $paramValue) 
			{
				if($strRequest == "")
				{
					$strRequest = $paramName . "=>" . $paramValue;
				}
				else
				{
					$strRequest .= "," . $paramName . "=>" . $paramValue;	
				}
				//echo "<br/>" . $paramName . " = " . $paramValue;
			}
			$amount = "0";
			$txnid = "";
			$PaidBy = "";
			$Date = "";
			$SocID = "";
			$sError = "";
			$dbConn = "";

			$bHasError = false;
			$dbConnRoot = new dbop(true);
			$logInsertID = $dbConnRoot->insert("insert into w2s_payment_posting_log (`fields`) values ('".$strRequest."')");
				
			if($_REQUEST["Site_id"] && $_REQUEST["Site_id"] != "")
			{
				//$arValidSiteIDs = array("59","156","202","230");
				//if(in_array($sSiteID,$arValidSiteIDs))
				//{
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
					}
					else
					{
						$sError = "Invalid SocietyID &lt;".$sSiteID."&gt; provided";
						$bUnsupportedSocietyIDProvided = 1;	
					}
				}
			 	catch(Exception $ex)
			 	{
			 		$bIsPGEnabled = "0";
			 		//echo "Exception occured";
			 	}
			 	//echo "error:".$sError;
			 	
			 	if($bTrace)
			 	{
			 		echo "bIsPGEnabled:".$bIsPGEnabled;
			 	}
			}
			else
			{
				$sError = "Society ID &lt;Site_id&gt; not provided";
				$bHasError = true;
			}
			if($SocID != "")
			{
				$resDBName = $dbConnRoot->select("select dbname from dbname where society_id='".$SocID."'");
				//print_r($resDBName);
				$dbConn = new dbop(false, $resDBName[0]["dbname"]);
				$obj_utility = new utility($dbConn);
			}
			if($_REQUEST["mob_no"] <> '')
			{
				$_REQUEST["Unique_id"] = $_REQUEST["mob_no"];
			}
			//$_REQUEST["Unique_id"] = $_REQUEST["mob_no"];
			if($SocID != "" && isset($_REQUEST["Unique_id"]) && $_REQUEST["Unique_id"] != "")
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
							$sTokenID = $arToken["id"];
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
			}
			else
			{
				$sError = "Unique_id ID &lt;Unique_id&gt; not provided";
			}
			if(isset($_REQUEST["amount"]) && $_REQUEST["amount"] != "")
					{
				$amount=$_REQUEST["amount"];
			}
			else
			{
				$sError = "Transaction Amount &lt;amount&gt; not provided";
				
				$bHasError = true;
			}
			
			if(isset($_REQUEST["txnid"]) && $_REQUEST["txnid"] != "")
			{
				$txnid=$_REQUEST["txnid"];
			}
			else
			{
				$sError = "Transaction ID &lt;txnid&gt; not provided";
				
				$bHasError = true;
			}
			
			if($txnid == "")
			{
				$status = "Failure";
			}
			else
			{
				$status = "Success";
			}
		}
		
		$bMobileNoProvided = false;
		$bUnitNoProvided = false;
		$bW2s_trnx_Provided = false;
		$sMobileNo = "";

		if($bTokenProvided && $bTokenValid)
		{	
			if(isset($_REQUEST["mob_no"]) && $_REQUEST["mob_no"] != "")
			{
				$sMobileNo = $_REQUEST["mob_no"];
				$bMobileNoProvided = true;
			}
			else
			{
				$sError = "Mobile Number &lt;mob_no&gt; not provided";
				
			}
			if($bTrace)
			{
				echo "flag:".$bMobileNoProvided;
			}
			if(isset($_REQUEST["unit_id"]) && $_REQUEST["unit_id"] != "")
			{
				$PaidBy = $_REQUEST["unit_id"];//its UnitID
				$bUnitNoProvided = true;
			}
			else
			{
				if(!$bMobileNoProvided)
				{
					$sError = "Unique_id &lt;Unique_id&gt; not provided";	
						
				}
			}
			if(isset($_REQUEST["w2s_transact_id"]) && $_REQUEST["w2s_transact_id"] != "")
			{
				$w2s_transact_id = $_REQUEST["w2s_transact_id"];//its UnitID
				$bW2s_trnx_Provided = true;
			}
			else
			{
				$bHasError = true;
				if(!$bW2s_trnx_Provided)
				{
					$sError = "way2society Transaction ID &lt;w2s_transact_id&gt; not provided";	
				}
			}
			//echo "error:".$sError;
			if(($bMobileNoProvided || $bUnitNoProvided) && $bW2s_trnx_Provided)
			{
				$sError = "";
			}
			else
			{
				$bHasError = true;
			}
			//echo "error:".$sError;
			//$BillType = $_REQUEST["BT"];
			$BillType = 0;
			$LoginID = $_SESSION["login_id"];
			if(isset($_REQUEST["Payment_Date"]) && $_REQUEST["Payment_Date"] != "")
			{
				$Date = $_REQUEST["Payment_Date"];
			}
			else
			{
				$sError = "Payment Date &lt;Payment_Date&gt; not provided";
				$bHasError = true;
			}
			
			
			//echo "soc:".$SocID;
			//echo "error:".$bHasError;
			if($SocID != "")
			{
				$resBankID = $dbConn->select("select `PGBeneficiaryBank` from society");
				//print_r($resBankID);
				$PaidTo = $resBankID[0]["PGBeneficiaryBank"];
				//$obj_neft = new neft($m_dbConn);
				
				$obj_Payment_Gateway = new PaymentGateway($dbConn);
				
				if($bTrace)
				{
					echo "mob:".$sMobileNo;
				}
				if($bUnitNoProvided)
				{
					$PaidBy = $_REQUEST["unit_id"];
				}
				else if($bMobileNoProvided)
				{
					$UnitDetails = $obj_utility->GetUnitDetailsByMobileNo($sMobileNo);
					if($bTrace)
					{
						print_r($UnitDetails[0]);
					}
					//echo "unit:".$UnitDetails[0]["unit_id"];
					$PaidBy = $UnitDetails[0]["unit_id"];
				}
				$UnitExistDetails = $obj_utility->IsUnitExist($PaidBy);
				$strUnitID = $UnitExistDetails[0]["unit_id"];
				if($strUnitID == "")
				{
					$sError = "Unique id &lt;".$PaidBy."&gt; not provided";
					$bHasError = true;
				}
				else
				{
				}
			}
		}

		$unmapstatus = "";
		$payuMoneyId = "";
		$bank_ref_num = "";
		$amount_split = "";
		$Mode = "0";
		$Comments = "Being Payment of Rs ". $amount . " through PayTM recorded Successfully";
		$BankCode= "PayTM";
		//echo "error:".$sError;
	
		 
		if(!$bHasError && $bW2s_trnx_Provided)
		{
			if($bTrace)
			{
				echo "completing payment";
			}
			$arResult = $obj_Payment_Gateway->CompletePayment($w2s_transact_id,$PGClientID, $LoginID,$PaidBy,$PaidTo,$Date,$amount,$BillType,$txnid,$status,$unmapstatus,$payuMoneyId, $bank_ref_num, $amount_split, $strRequest,$Mode,$Comments, $sTokenID, "PayTM");
			if($arResult["status"] == "1")
			{
				//$logUpdateID = $dbConnRoot->update("update w2s_payment_posting_log set `w2s_transact_id`='".$w2s_transact_id."',`society_id` = '".$SocID."' where id='".$logInsertID."'");
			
				//echo "date:".$Date . " date " . $Date . " txnid " . $txnid . " amount " .  $amount . " paidby " . $PaidBy . " paidto " . $PaidTo . " bankcode " .  $BankCode;
				//	echo "completed payment. Updating registers...";
				
				//$obj_ChequeDetails->AddNewValuesNew($Date, $Date, $txnid, $amount, $PaidBy, $PaidTo, $BankCode, "-", DEPOSIT_ONLINE, $Comments, $BillType,0,0,0,0,1,false, $SocID, "4"/*,1Payment gateway flag*/);
				//	echo "done1";		 
				if($PGClientID == "1") // PayUMoney
				{
					//$w2surl = "http://localhost//w2s_aws_1/";
					$w2surl = "https://way2society.com/";
					$URL = "Location: ".$w2surl."/Response.php?TrnxID=".$txnid;
					//echo "url:".$URL;
					header($URL);
				}
				//	echo "done2";   
				$arResponse = $obj_Payment_Gateway->GetTransactionDetailsJSON($txnid, $PaidBy);
				$arResponse["unique_id"] = $UniqueID;
						
				//$arResponse = $obj_Payment_Gateway->GetTransactionDetailsJSON($txnid, $PaidBy);
				//$arResponse["unique_id"] = $UniqueID;
			
			}
			else
			{
				$sError = $arResult["Error"];
				$bHasError = true;
			}
		}
		else
		{
				
			$amount = "0";
			$txnid = "";
			$PaidBy = "";
			$Date = "";
			$SocID = "";
		}
		
		if($bHasError)
		{
			
			$arResponse["tran_stat"] = "";
			$arResponse["paytm_tran_id"] = "";
			$arResponse["unique_id"] = "";
			$arResponse["w2s_transact_id"] = "";
			$arResponse["amount"] = "";
			$arResponse["status"] = "Failure";
			$arResponse["responseCode"] = "";
			$arResponse["responseCode"] .= $sError;
		}
		echo json_encode($arResponse);
	}
	catch(Exception $ex)
	{
		echo "exception:".$ex;
	}         
?>	
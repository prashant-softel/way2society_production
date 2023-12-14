<?php
	//include_once "classes/doc.class.php" ;
	//include_once("includes/head_s.php");
	include_once("classes/include/dbop.class.php");	
	include_once("classes/include/fetch_data.php");
	//echo "1";
	include_once("classes/utility.class.php");
	//echo "1";
	include_once("classes/PaymentGateway.class.php");

 	$Site_ID = "";
 	//$_REQUEST["Site_id"];
 	//$UnitID = $_REQUEST["unit_id"];
 	//echo "cid:".$_REQUEST["CID"];	
 	$UnitID = "";
 	$sMobileNo = "";
 	$bMobileNoProvided = false;
	$bUnitNoProvided = false;
	$bTrace = 0;
	if(isset($_REQUEST["Trace"]))
	{
		$bTrace = 1;	
	}
 	//$arData = explode("_", $Site_ID);
 	//$SocID = $arData[0];
 	//$UnitID = $arData[1];
 	$BT = "0";
 	$arBillType = array("0" => "Maintenance", "1" => "Supplementary");

 	if(isset($arData[2]))   
 	{
 		$BT = $arData[2];
 		if($BT == "0")
 		{
 			$arBillType = array("0" => "Maintenance");
 		}
 		elseif ($BT == "1") {
 			$arBillType = array("1" => "Supplementary");
 		}
 		else
 		{
 			$arBillType = array("0" => "Maintenance", "1" => "Supplementary");
 		}
		//echo "BT:".$BT;
		//echo "<br>";
 	}
 	$bMobileNoProvided = false;
	$bUnitNoProvided = false;
	$bSocietyIDProvided = 0;
	$bUnsupportedSocietyIDProvided = 0;
	$SocID = "";
	$UniqueID = "";
	$dbConn = "";
	//echo "d:".$_REQUEST["Site_id"];
	if(isset($_REQUEST["Site_id"]) && $_REQUEST["Site_id"] != "")
	{
		$sSiteID = $_REQUEST["Site_id"];
		//$arValidSiteIDs = array("59","156","202","230");
		//if(in_array($sSiteID,$arValidSiteIDs))
		//{
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
		$sError = "Society ID &lt;site_id&gt; not provided";		
	}
	if($_REQUEST["mob_no"] <> '')
	{
		$_REQUEST["Unique_id"] = $_REQUEST["mob_no"];
	}
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

			}
			else
			{
				$sqlUnit = "select unit_id from unit where `unit_no` = '".$UniqueID."'";
				$resUnits = $dbConn->select($sqlUnit);
				//print_r($resUnits);
				if($bTrace)
				{
					print_r($resUnits);
				}
				if(sizeof($resUnits) > 0);
				{
					$_REQUEST["unit_id"] = $resUnits[0]["unit_id"];
				}
			}
		}
	}
	else
	{
		$sError = "Unique_id ID &lt;Unique_id&gt; not provided";
	}

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
		echo "mob:".$sMobileNo;
	}
	if(isset($_REQUEST["unit_id"]) && $_REQUEST["unit_id"] != "")
	{
		$UnitID = $_REQUEST["unit_id"];//its UnitID
		$bUnitNoProvided = true;
	}
	else
	{
		if(!$bMobileNoProvided)
		{
			$sError = "UnitID &lt;unit_id&gt; not provided";	
			
		}
	}
	
	if($bMobileNoProvided || $bUnitNoProvided)
	{
		$sError = "";
	}
	if($bTrace)
	{
		echo "soc:".$SocID;
	 	echo "flag:".$bSocietyIDProvided;
		echo "unit:".$UnitID;
	}
	$bTokenProvided = false;
	$bTokenValid = false;
	$sTokenID = "";
	//print_r($arBillType);
	$arResponse = array();
	//print_r($arBillType);
	if($SocID != "" && $UniqueID != "")
	{
		$objFetchData = new FetchData($dbConn);
		$objUtility = new utility($dbConn);
		$obj_Payment_Gateway = new PaymentGateway($dbConn);

		//echo "tk:";
		if(isset($_REQUEST["Token"]) && $_REQUEST["Token"] != "")
		{
			/*$sToken = $_REQUEST["Token"];//its UnitID
			$bTokenProvided = true;
			$arToken = $objUtility->IsAPITokenValid(2, $UniqueID, $sToken);
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
			}
			if($bTrace)
			{						
				echo "status:".$status ." token:".$sTokenID;
			}
			if($bTrace)
			{
				echo $sError; 
			}*/
		}
		else
		{
			/*if(!$bTokenProvided)
			{
				$sError = "Token &lt;Tokend&gt; not provided";				
			}*/
		}
		if($bTrace)
		{
				
			echo "bmob:".$bMobileNoProvided;
		}
		//if($bTokenProvided && $bTokenValid)
		{
			if($bMobileNoProvided)
			{
				$UnitDetails = $objUtility->GetUnitDetailsByMobileNo($sMobileNo);
			}
			else if($bUnitNoProvided)
			{
				$UnitDetails = $objUtility->GetUnitNo($UnitID);
			}
			if($bTrace)
			{
				echo "unit:".print_r($UnitDetails);
			}
			$SocietyDetails = $objUtility->getSocietyAddress();
			//echo "bank".$PGBeneficiaryBank;
			$PGBeneficiaryBank = $objUtility->GetPaymentGatewayBankID();
			if($bTrace)
			{
				echo "bank:".$PGBeneficiaryBank;
			}
			//print_r($SocietyDetails);
			$socAddress = $SocietyDetails[0]["society_add"];
			if($bTrace)
			{
				echo "size:".sizeof($UnitDetails);
			}
			$sOwnerName = "";
			$sFlatNo = "";
			$sOwner_address ="";
			$arUniqueUnitNoAndMobileNo = array();
			//$sSiteID = "";
			$bMobileNumberFound = false;
			$iNumberOfBills = 0;
			for($iCnt = 0; $iCnt < sizeof($UnitDetails);  $iCnt++)
			{
				//echo "<br>mob:". $UnitDetails[$iCnt]["mobile_no"];
				$bCheckMobileNo = false;
				if($bMobileNoProvided)
				{
					$strCombine = $UnitDetails[$iCnt]["unit_no"] ."|" . $UnitDetails[$iCnt]["mobile_no"];
					//echo "<br>str:".$strCombine;
					if(!in_array($strCombine, $arUniqueUnitNoAndMobileNo))
					{
						//echo "<br>not found";
						if($sMobileNo == $UnitDetails[$iCnt]["mobile_no"])
						{
							$bCheckMobileNo = true;
							$bMobileNumberFound = true;
			
							array_push($arUniqueUnitNoAndMobileNo, $strCombine);
						}
					}
					else
					{
						//echo "<br>found. No need to show dues again";
					}
				}
				if($bTrace)
				{
					echo "flag mob:".$bMobileNumberFound;
				}
				if($bCheckMobileNo || $bUnitNoProvided)
				{
					if($bMobileNumberFound == false)
					{
						$bMobileNumberFound = true;
					}
					//echo "<br>found:". $UnitDetails[$iCnt]["mobile_no"];
					//print_r($UnitDetails[$iCnt]);
					//echo "<br>";
					//echo "size:".sizeof($UnitDetails);
					if($bUnitNoProvided)
					{	
						if($bTrace)
						{
							echo "unit test:";
						}
						$sOwnerName = $UnitDetails[$iCnt]["primary_owner_name"];
						$UnitNo = $UnitDetails[$iCnt]["unit_no"];
					}
					else if($bMobileNoProvided)
					{
						if($bTrace)
						{
							echo "mobile test:";
						}
						//$sOwnerName = $UnitDetails[$iCnt]["to_name"];
						$sOwnerName = $UnitDetails[$iCnt]["primary_owner_name"];
						//echo "name :".$sOwnerName ;
						//var_dump($UnitDetails);
						//print_r($iCnt);
						
						//$sFlatNo = $UnitDetails[$iCnt]["unit_no"];
					}
					if($bTrace)
					{
						echo "unitno:".$UnitNo;
						echo "unit id:".$UnitID;
						print_r($UnitDetails[$iCnt]);
					}
					if(isset($UnitDetails[$iCnt]["unit_id"]))
					{
						$UnitID = $UnitDetails[$iCnt]["unit_id"];
						$UnitNoDetails =$objUtility->IsUnitExist($UnitID);
						//print_r($UnitNoDetails);
						$UnitNo = $UnitNoDetails[0]["unit_no"];
					}
					if($bTrace)
					{
						echo "unitno:".$UnitNo;
						echo "unit id:".$UnitID;
					}
					//$UnitNo = $UnitDetails[$iCnt]["unit_no"];
					$sOwner_address =  $UnitNo ."," . $socAddress;
				
					$arReport = array();
					//$Site_ID = $Site_ID;
					$arReport["owner_name"] = $sOwnerName;
					$arReport["apartment_no"] = $UnitNo;
					$arReport["owner_address"] = $sOwner_address;
					$arReport["site_id"] = $sSiteID;
					
					$TotalBillAmount = 0;
					$arDues = array();
							
					foreach ($arBillType as $sBillType => $sBillTypeDesc) 
				 	{
				 		if($bTrace)
						{
				
				 			echo " sBillType: ".$sBillType;
						}

			 		 	$latestPeriod = $objFetchData->getLatestPeriodID($UnitID);
						
						$BillFor_Msg = "";
						if($bTrace)
						{
							echo "bill:".$latestPeriod;
						}
						if(isset($latestPeriod))
						{
							$BillFor = $objFetchData->GetBillFor($latestPeriod);
							$BillFor_Bill = $objUtility->displayFormatBillFor($BillFor);
							
							$BillRegisterData = $objFetchData->GetValuesFromBillRegister($UnitID, $latestPeriod, $sBillType);
							if(empty($BillRegisterData)) { 
								continue;	
							}
							if($bTrace)
							{
								echo "<br>bill size:".sizeof($BillRegisterData);
							}
							//print_r($BillRegisterData[0]);
							$sDueDate = "";
							$sBillDate = "";
							$BillNumber = "";
							$BillAmount = "";
							if(isset($BillRegisterData) && sizeof($BillRegisterData) > 0)
							{
								if($bTrace)
								{
									echo "<br>bill sz:".sizeof($BillRegisterData);
								}
								
								for($iVal = 0; $iVal < sizeof($BillRegisterData) ; $iVal++) 
								{
									$BillDetails = $BillRegisterData[$iVal]["value"];
									//print_r($BillDetails);
									$sBillDate = getDisplayFormatDate($BillDetails->sBillDate);
									$sDueDate = getDisplayFormatDate($BillDetails->sDueDate);
									$BillAmount = $BillDetails->BillAmount;
									$BillNumber = $BillDetails->BillNumber;
									break;
								}
								if($bTrace)
								{
									echo "<br>billsize:".print_r($BillRegisterData);
								}
								
								if($BillAmount <= 0)
								{
									continue;
								}
								if($bTrace)
								{
									echo "<br>bill dt:".sizeof($BillRegisterData);
								}
								
								if($bTrace)
								{
									echo "<br>bill: ".$BillAmount . " Total: ". $TotalBillAmount;
								}
								if($TotalBillAmount == "")
								{
									$TotalBillAmount = $BillAmount;
								}
								else
								{
									$TotalBillAmount += $BillAmount;
								}
								if($bTrace)
								{
									echo "<br>bill: ".$BillAmount . " Total: ". $TotalBillAmount;
								}
								//$soc_id = $_SESSION["society_id"];
								//$sqlSelect1 = "select * from `dbname` where `society_id`='".$soc_id."'";
								//$resDBName = $dbopRoot->select($sqlSelect1);
								//echo "c";
								if($bTrace)
								{
									echo "BT: ".$BT ." BillType: ".$sBillType;
								}
								if($BT != "")
								{
									//$dues = $objUtility->getDueAmountByBillType($UnitID, $sBillType);	
									$dues = $objUtility->getDueAmountTillDate($UnitID, $sBillType);//new
								}
								else
								{
									//$dues = $objUtility->getDueAmountTillDate($UnitID);
									//$dues = $objUtility->getDueAmountByBillType($UnitID, $sBillType);	
									
									$dues = $objUtility->getDueAmountTillDate($UnitID, $sBillType);//new
								}
								if($bTrace)
								{
									echo " due:".$dues;
								}
								if($dues == "0.00")
								{
									$sDueDate = "";
									$sBillDate = "";
									$BillNumber = "";
									$sBillTypeDesc = "";
									$BillFor_Bill = "";
									
									//echo "<br>Total: ". $TotalBillAmount;
									$TotalBillAmount = $TotalBillAmount - $BillAmount;

									$BillAmount = "";
								}
								else
								{
									$TotalBillAmount = $dues;
								}
								if($bTrace)
								{
									echo "<br>Total: ". $TotalBillAmount;
								}
								if($BillAmount != "")
								{
									
									$LoginID = "0";
									$UnitID = $UnitID;
									$Date = Date('Y-m-d');
									$Amount = $BillAmount;
									$BillType = $sBillType;	
									$PaidTo = $PGBeneficiaryBank;
									$TranxID = "0";
									$Status = "0";
									$payuMoneyId = "0";
									$Mode = "0";
									$Comments = "Being Due Amount Fetched";
									if($bTrace)
									{
										echo "token:".$sTokenID;
									}
									$w2s_transact_id = $obj_Payment_Gateway->InitiatePayment(2,$LoginID,$UnitID,$PaidTo,$Date,$Amount,$BillType,$TranxID,$Status,$payuMoneyId,$Mode,$Comments, $sTokenID);

									$arDuesList = array();
									$arDuesList["due_date"] = $sDueDate;
									$arDuesList["charge_date"] = $sBillDate;
									$arDuesList["pending_amt"] = $BillAmount;
									//$arDuesList["pending_amt"] = $dues;       // comment on Total Due Amaunt in supll and mentenamce bill
									$arDuesList["item_id"] = $BillNumber;
									$arDuesList["type"] = $sBillTypeDesc;
									$arDuesList["item_description"] = $BillFor_Bill;
									$arDuesList["w2s_transact_id"] = $w2s_transact_id;
									//$arReport["dues_list"] = array($arDuesList);
									
									array_push($arDues, $arDuesList);
									$iNumberOfBills++;
								}
							}
						}
					}
					//if($TotalBillAmount > 0)
					{
						$arReport["dues_list"] = $arDues;
						$arReport["total_due"] = $TotalBillAmount;
						$arReport["responseCode"] = $sError;
						if($iNumberOfBills > 1)
						{
							$arReport["allow_partial_payment"] = "false";
						}
						else
						{
							$arReport["allow_partial_payment"] = "true";	
						}
						$arReport["status"] = "Success";
						array_push($arResponse, $arReport);
					}
					//print_r($dues);
					//echo "<br><ptr>";
				}
			}
		}
		//else
		//{
		//	echo "";
		//}
	}
	if($arReport["status"] != "Success")
	{
		if(!$bMobileNumberFound || $bSocietyIDProvided == "0" || $UniqueID == "" || $bUnsupportedSocietyIDProvided == 0)
		{
			$arDuesList = array();
			$arReport = array();

			$arDuesList["due_date"] = "";
			$arDuesList["charge_date"] = "";
			$arDuesList["pending_amt"] = "";
			$arDuesList["item_id"] = "";
			$arDuesList["type"] = "";
			$arDuesList["w2s_transact_id"] = "";
			$arDuesList["item_description"] = "";
			//$arReport["dues_list"] = array($arDuesList);
			$arReport["owner_name"] = "";
			$arReport["apartment_no"] = "";
			$arReport["owner_address"] = "";
			$arReport["site_id"] = $sSiteID;
			
			$arReport["dues_list"] = $arDuesList;
			$arReport["total_due"] = $TotalBillAmount;
			//echo "soc1:".$bSocietyIDProvided;
			//echo "bIsPGEnabled:".$bIsPGEnabled;
			//echo "bsupport:".$bUnsupportedSocietyIDProvided;
			if($bIsPGEnabled != "1")
			{
				if(!isset($bIsPGEnabled))
				{
					$sMsg = "Invalid SocietyID &lt;".$sSiteID."&gt; provided";
				}
				else
				{
					$sMsg = "Payment Gateway Not Subscribed for SocietyID &lt;".$sSiteID."&gt;";
				}
			}
			else if(!$bSocietyIDProvided)
			{
				$sMsg = "";
				if($bUnsupportedSocietyIDProvided == 0)
				{
					 $sMsg  = "Society ID &lt; Site_id &gt; not provided";
				}
				else
				{
					$sMsg = "Invalid SocietyID &lt;".$sSiteID."&gt; provided";
				}
			}
			else
			{
				if($UniqueID == "")
				{
					$sMsg = "Unique id &lt;Unique_id&gt; not Provided";
				}
				else
				{
					if($sMobileNo == "")
					{
						$sMsg = "Unique_id &lt;".$UniqueID."&gt; not linked to any Unit or Flat";
					}
					else
					{
						$sMsg = "Mobile Number &lt;" . $sMobileNo ."&gt; not linked to any Unit or Flat";
					}
				}
			}
			$arReport["status"] = "Failure";
			$arReport["responseCode"] = $sMsg;
			$arReport["allow_partial_payment"] = "false";
			array_push($arResponse, $arReport);
		}
	}
	//echo "<pre>";
	print_r(json_encode($arResponse));
	//echo "</pre>";

?>
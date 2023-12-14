<?php
	//echo "1";
	include_once("classes/include/dbop.class.php");
	//echo "1";
	include_once("classes/dbconst.class.php");
	//echo "1";
	include_once("classes/utility.class.php");
	//echo "1";
	include_once("classes/neft.class.php");
	//echo "1";
	$random1 = rand('000000','999999'); //random 6 digit code1
	$ClientKeyID = "188374677837";  // existing given client ID
	$random2 = rand('000000','999999'); //random 6 digit code2
	$ClientKey = $random1 . $ClientKeyID . $random2; // concatenate above 3

	$ClientID = base64_encode($ClientKey); //generated new ClientID
	echo $ClientID;
	die();
	
	$ClientID = $_REQUEST["ClientID"];
	//echo "<br>".$ClientID;
	//$ClientID = strtr(rtrim(base64_encode($ClientKey), '='), '+/', '-_');
	if($bTrace)
	{
		echo "<br>".$ClientID;
	}
	//echo "<br>".base64_decode(strtr($ClientID, '-_', '+/'));
	//echo "<br>".
	$UserClientKey = base64_decode($ClientID);
	$UserClientKey = substr($UserClientKey, 6,12);
		
	$bError = false;
	//$bTrace = true;
	if($ClientKeyID != $UserClientKey)
	{
		$sError = "Client ID &lt;ClientID&gt; is invalid";
		$bError = true;
	}
	$bMobileNoProvided = false;
	$bUnitNoProvided = false;
	$bSocietyIDProvided = 0;
	$bIsPGEnabled = 0;
	$SocID = "";
	$UniqueID = "";
	$dbConn = "";
	$bMobileNoProvided;
	$sMobileNo = "";
	$UnitID = "";
	if($bTrace)
	{
		echo "error:".$bError;
	}
	$bUnsupportedSocietyIDProvided = 0;
	if(!$bError)
	{
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
		
		if($bTrace)
		{
			echo "error2:".$bError;
		}
		//echo "site:".$SocID;
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
				
				$objUtility = new utility($dbConn,$dbConnRoot);
				if(strlen($UniqueID) == 10)
				{
					$UnitDetails = $objUtility->GetUnitDetailsByMobileNo($UniqueID);
					//echo "size:".sizeof($UnitDetails);
					//print_r($UnitDetails);
					if(isset($UnitDetails) && sizeof($UnitDetails) > 0)
					{
						$_REQUEST["mob_no"] = $UniqueID;
					}
					else
					{
						//echo "zero";
					}
					//$_REQUEST["mob_no"] = $UniqueID;
					

				}
				else
				{
					$sqlUnit = "select unit_id from unit where `unit_no` = '".$UniqueID."'";
					//echo "sql:".$sqlUnit;
					$resUnits = $dbConn->select($sqlUnit);
					//print_r($resUnits);
					if($bTrace)
					{
						print_r($resUnits);
					}
					if(sizeof($resUnits) > 0)
					{
						$_REQUEST["unit_id"] = $resUnits[0]["unit_id"];
					}
					else
					{
						//echo "error:";
						$sError = "UniqueID &lt;".$UniqueID."&gt; not linked to any Unit or Flat";	
						$bError = true;		
					}
				}
			}
			if($bTrace)
			{
				echo "error3:".$bError;
			}
			if(isset($_REQUEST["mob_no"]) && $_REQUEST["mob_no"] != "")
			{
				$sMobileNo = $_REQUEST["mob_no"];
				$bMobileNoProvided = true;
			}
			else
			{
				$sError = "Mobile Number &lt;mob_no&gt; not provided";
				$bError = true;
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
					$sError = "UniqueID &lt;".$UniqueID."&gt; not linked to any Unit or Flat";	
					//$bError = true;
				}
			}
			if($bTrace)
			{
				echo "bError4:".$bError;
			}
			if($bMobileNoProvided || $bUnitNoProvided)
			{
				$sError = "";
				$bError = false;
			}
			if(!$bError)
			{
				$bSuccess = false;
				$sStatus = "Failure";
				$sResponse = "";
				try
				{
					if($bTrace)
					{
						echo "uniq:".$UniqueID;
					}
					//$objUtility = new utility($dbConn, $dbConnRoot);
					
					if($bTrace)
					{
						echo "uniq:".$UniqueID;
					}
					$randomNumber = $objUtility->generateRandomString(30);
					
					if($bTrace)
					{
						echo "uniq:".$UniqueID;
					}
					$arResponse = array();
					
					$arResponse["unique_id"] = $UniqueID;
					$arResponse["Site_id"] = $SocID;
					$arResponse["Token"] = $randomNumber;

					if($bTrace)
					{
						echo "uniq:".$UniqueID;
					}
					$sqlUpdate = "update api_tokens set status=0 where `ClientID`='2' and `UniqueID`='".$UniqueID."'";
					$dbConn->insert($sqlUpdate);
					//echo "insert:";
					$iStatus = 0;
					$sqlInsert = "insert into api_tokens (`ClientID`,`UniqueID`,`Token`,`status`) values('2','".$UniqueID."','".$randomNumber."','1')";
					$iStatus = $dbConn->insert($sqlInsert);
					if($bTrace)
					{
						echo "status:".$iStatus;
					}
					if($iStatus > 0)
					{
						$bSuccess = true;
						$sStatus = "Success";
					}
				}
				catch(Exception $ex)
				{
					if($bTrace)
					{
						echo "msg".$ex;
					}
					$bSuccess = false;
					$sResponse = "Unepxected Error Occurred. Please try again Later.";
				}

				$arResponse["status"] = $sStatus;
				//echo "Success:".$bSuccess;
				if($bSuccess)
				{
					$sError = "";
				}
				$arResponse["responseCode"] = $sError;

			}
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
			//echo $sError;
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
	//print_r($arResponse);
	if($bError)
	{
		$arResponse = array();
		$arResponse["unique_id"] = $UniqueID;
		$arResponse["Site_id"] = "";
		$arResponse["status"] = "Failure";
		$arResponse["responseCode"] = $sError;
	}
	echo json_encode($arResponse);
?>
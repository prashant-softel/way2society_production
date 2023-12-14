<?php

	include_once('include/dbop.class.php');
	include_once("dbconst.class.php");
	include_once("utility.class.php");
	include_once("PaymentDetails.class.php");
	include_once("ChequeDetails.class.php");
	include_once("changelog.class.php");
	
	class Import_Bank_Statement{
		
	public $m_dbConn;
	public $show_debug_trace;	
	public $obj_utility;
	public $obj_PaymentDetails;
	public $obj_ChequeDetails;
	public $changeLog;
	public $errorLog;
	public $errorfile_name;
	private $BankStatementMatchIDs = array();
		
	function __construct(){
	
		$this->m_dbConn = new dbop();
		$this->show_debug_trace = 0;
		$this->errorLog;
		$this->errorfile_name;
		$this->BankStatementMatchIDs;
		$this->obj_utility = new utility($this->m_dbConn);
		$this->obj_PaymentDetails = new PaymentDetails($this->m_dbConn);
		$this->obj_ChequeDetails = new ChequeDetails($this->m_dbConn);
		$this->changeLog = new changeLog($this->m_dbConn);
	}
	
	public function Compare_Bank_Statement_Data($BankID,$BeginingDate = 0, $EndingDate = 0){
		
		if($this->show_debug_trace == 1){
		
			echo "<br>Compare_Bank_Statement_Data Function Start";
			echo "<br>Bank ID : ".$BankID;
			echo "<br>Data as Below";
			echo "<pre>";
			print_r($BankStatementData);
			echo "<pre>";
		}
		
		//array to store Compare Result
		$Result = array();
		$MatchEntry = array();
		$PresentInBank = array();
		$PresentW2s = array();
		
		
		if($BeginingDate <> 0 && $EndingDate <> 0)
		{
			
			$BankStatementResult = $this->MatchBankStatementWithW2s($BankID,$BeginingDate,$EndingDate); // In This method we process the data and divide into three part match, present in bank and present in w2s
			$MatchEntry = $BankStatementResult[MATCH_ENTRY];
			$PresentInBank = $BankStatementResult[PRESENT_IN_BANK];
			$PresentW2s = $BankStatementResult[PRESENT_IN_W2S];

		}
		else
		{
			//As per society Bill Method Reconcilation Data will send to MatchBankStatementWithW2s method
			$PeriodDetails_Query = "SELECT `BeginingDate`, `EndingDate` FROM `period` WHERE YearID = '".$_SESSION['default_year']."' and `status` = 'Y'";
			$PeriodDetails = $this->m_dbConn->select($PeriodDetails_Query);
			
			for($j = 0 ; $j < count($PeriodDetails); $j++)
			{
				$BeginingDate = $PeriodDetails[$j]['BeginingDate'];
				$EndingDate = $PeriodDetails[$j]['EndingDate'];	
				
				$BankStatementResult = $this->MatchBankStatementWithW2s($BankID,$BeginingDate,$EndingDate); // In This method we process the data and divide into three part match, present in bank and present in w2s
				
				if(!empty($BankStatementResult[MATCH_ENTRY]))
				{
					$MatchEntry = array_merge($MatchEntry,$BankStatementResult[MATCH_ENTRY]);					
				}
				
				if(!empty($BankStatementResult[PRESENT_IN_BANK]))
				{
					$PresentInBank = array_merge($PresentInBank,$BankStatementResult[PRESENT_IN_BANK]);					
				}
				
				if(!empty($BankStatementResult[PRESENT_IN_W2S]))
				{
					$PresentW2s = array_merge($PresentW2s,$BankStatementResult[PRESENT_IN_W2S]);					
				}
			}	
		}
		
		$AllMatchStatementID = array_column($MatchEntry,'BankStatementID');
		$AllPresentInBankStatementID = array_column($PresentInBank,'BankStatementID');
		$duplicates = array_intersect($AllPresentInBankStatementID,$AllMatchStatementID);
		$duplicates = array_keys($duplicates);
		
		foreach($duplicates as $index)
		{
			unset($AllPresentInBankStatementID[$index]);
		}
		
		$UniqueEntryPresentInBank = array_unique($AllPresentInBankStatementID);
		$UniqueEntryPresentInBank = array_keys($UniqueEntryPresentInBank);
		
		
		$PresentInBankFinal = array();
		$AmountMatchFinal = array();
		foreach($UniqueEntryPresentInBank as $index)
		{
			if(!empty($PresentInBank[$index][AMOUNT_MATCH]))
			{
				array_push($AmountMatchFinal,$PresentInBank[$index]);	
			}
			else
			{
				array_push($PresentInBankFinal,$PresentInBank[$index]);				
			}

		}
		
		/*echo "<br>Count : ".count($AmountMatchFinal);
		echo "<pre>";
		print_r($AmountMatchFinal);
		echo "</pre>";
		
		echo "<pre>";
		print_r($PresentInBankFinal);
		echo "</pre>";
		
		*/
		
		$Result[MATCH_ENTRY] = array_values($MatchEntry);
		$Result[AMOUNT_MATCH] = array_values($AmountMatchFinal);
		$Result[PRESENT_IN_BANK] = array_values($PresentInBankFinal);
		$Result[PRESENT_IN_W2S] = array_values($PresentW2s);
		return $Result;
	}
	
	
	private function MatchBankStatementWithW2s($BankID,$Start_Date,$End_Date)
	{
		
		//get all Bank UnReco Statement
		$EndDateplus15days = date('Y-m-d',strtotime($End_Date. '+ 15 days'));
		$Bank_Statement_query = "SELECT `Id`, `Date`, `ChequeNo`, `Bank_Description`, `Debit`, `Credit`, `Notes` FROM `actualbankstmt` where Reco_Status = 0 and BankID = '".$BankID."' and `Date` BETWEEN '".$Start_Date."' AND '".$EndDateplus15days."' ";
		$BankStatementData = $this->m_dbConn->select($Bank_Statement_query);		
		
		//var_dump($BankStatementData);
		//Get All Unreconcile Bank entries
		$Bank_Register_Details_query = "SELECT * FROM `bankregister` WHERE LedgerID = '".$BankID."' AND `Cheque Date` BETWEEN '".$Start_Date."' AND '".$End_Date."' AND ReconcileStatus = 0 AND Is_Opening_Balance = 0 AND `Return` = 0";
		$Bank_Register_Details = $this->m_dbConn->select($Bank_Register_Details_query);
		/*echo "<br>Bank Statement";
		var_dump($Bank_Register_Details);
		echo "<br>Bank Registe";*/
		//get All required data from cheque entry table for receipt voucher
		$W2s_ChequeEntry_Query = "SELECT `ID`, `ChequeNumber`,`Amount`, `ChequeDate`, `PaidBy`, `DepositID`, `Comments` FROM `chequeentrydetails` WHERE `ChequeDate` BETWEEN '".$Start_Date."' AND '".$End_Date."'";
		$W2s_ChequeEntry_Details = $this->m_dbConn->select($W2s_ChequeEntry_Query);
		//var_dump($W2s_ChequeEntry_Details);
		
		//Get All NEFT Record Present in ChequeEntry Table for Specific Date Range 
		
		$W2s_NEFT_Query = "SELECT `ChequeDate` FROM `chequeentrydetails` where `ChequeDate` BETWEEN '".$Start_Date."' AND '".$End_Date."' AND `BankID` = '".$BankID."' AND DepositID = '".DEPOSIT_NEFT."' Group BY `ChequeDate`";
		$W2s_NEFT_Details = $this->m_dbConn->select($W2s_NEFT_Query);
		
		$NeftData = array(); // Here we Manupulate NEFT Data in a well Structure Format
			
		for($k = 0 ; $k < count($W2s_NEFT_Details); $k++)
		{
			// Get All Neft Details For That Particular Date
			$SelectNeftChequeNumber = "SELECT chk.ID, chk.ChequeNumber, chk.Amount FROM `chequeentrydetails` as chk JOIN bankregister as bk ON chk.ID = bk.ChkDetailID   where chk.ChequeDate = '".$W2s_NEFT_Details[$k]['ChequeDate']."' AND chk.BankID = '".$BankID."' AND chk.DepositID = '".DEPOSIT_NEFT."' AND bk.ReconcileStatus = 0";
			$NeftChequeNumberData = $this->m_dbConn->select($SelectNeftChequeNumber);
			
			$Amount = 0;
			$tempChequeNumber = $NeftChequeNumberData[0]['ChequeNumber']; // First Cheque Number 
			$NeftIDs = array();
			$NeftSplitAmount = array();
			
			for($j = 0 ; $j < count($NeftChequeNumberData); $j++)
			{
				if($tempChequeNumber == $NeftChequeNumberData[$j]['ChequeNumber']) // If Cheque Number Same then it' will Add the Amount. Considering as split entry
				{
					$Amount += $NeftChequeNumberData[$j]['Amount'];
					array_push($NeftIDs,$NeftChequeNumberData[$j]['ID']);
					array_push($NeftSplitAmount,$NeftChequeNumberData[$j]['Amount']);
					$tempDate = $W2s_NEFT_Details[$k]['ChequeDate'];
				}
				else // If Cheque Number is not same as previous then it will push the data into NeftData Array. Storing Date as Key and Inside the Date Key again Storing Cheque Number as sub Key and for this amount and ID as Value 
				{
					$NeftData[$tempDate][$tempChequeNumber] = array('Amount' => $Amount, 'IDs' => implode(',',$NeftIDs), 'SplitAmount' => implode(',',$NeftSplitAmount));
					$NeftSplitAmount = array();
					$Amount = $NeftChequeNumberData[$j]['Amount'];
					array_push($NeftSplitAmount,$NeftChequeNumberData[$j]['Amount']);
					$tempDate = $W2s_NEFT_Details[$k]['ChequeDate'];
					$tempChequeNumber = $NeftChequeNumberData[$j]['ChequeNumber'];
					$NeftIDs = array($NeftChequeNumberData[$j]['ID']);
				}
				
				if($j == count($NeftChequeNumberData)-1) // If it is last entry store it
				{
					$NeftData[$tempDate][$tempChequeNumber] = array('Amount' => $Amount, 'IDs' => implode(',',$NeftIDs), 'SplitAmount' => implode(',',$NeftSplitAmount));
				}
			}
		}
		
		$NeftDates = array_keys($NeftData); // Array to get All different Date Present for NEFT
		
		//get All required data from paymentdetails table for payment voucher
		$W2s_PaymentDetails_Query = "SELECT `id`, `ChequeNumber`, `PaidTo`, `Comments` FROM `paymentdetails` WHERE `ChequeDate` BETWEEN '".$Start_Date."' AND '".$End_Date."'";
		$W2s_PaymentDetails_Details = $this->m_dbConn->select($W2s_PaymentDetails_Query);
		
		
		//Bank Statement ChequeNumber
		
		$BankStatement_ChequeNo = array_column($BankStatementData,'ChequeNo');
		
		//Bank Register Related Data store in array
		$W2s_Bank_Register_CheckDetailID = array_column($Bank_Register_Details,'ChkDetailID');

		
		//Cheque Entry Related Data store in array
		$W2s_ChequeEntry_ChequeNo = array_column($W2s_ChequeEntry_Details,'ChequeNumber');
		$W2s_ChequeEntry_ID = array_column($W2s_ChequeEntry_Details,'ID');
		$W2s_ChequeEntry_Amounts = array_column($W2s_ChequeEntry_Details,'Amount');
		$W2s_ChequeEntry_ChequeDates = array_column($W2s_ChequeEntry_Details,'ChequeDate');
		$W2s_ChequeEntry_DepositIDs = array_column($W2s_ChequeEntry_Details,'DepositID');
		$W2s_ChequeEntry_Comments = array_column($W2s_ChequeEntry_Details,'Comments');
		$W2s_ChequeEntry_PaidBy = array_column($W2s_ChequeEntry_Details,'PaidBy');		
		
		// Payment Entry Related Data store in array
		$W2s_PaymentDetails_ChequeNo = array_column($W2s_PaymentDetails_Details,'ChequeNumber');
		$W2s_PaymentDetails_ID = array_column($W2s_PaymentDetails_Details,'id');
		$W2s_PaymentDetails_Comments = array_column($W2s_PaymentDetails_Details,'Comments');
		$W2s_PaymentDetails_PaidTo = array_column($W2s_PaymentDetails_Details,'PaidTo');
		
		/*echo "<br>============================";
		echo "<br>W2s_PaymentDetails_ID : ";
		echo "<pre>";
		print_r($W2s_PaymentDetails_ID);
		echo "</pre>";*/
		
		for($i = 0 ; $i < count($BankStatementData); $i++) // Bank Statement Process Start
		{
			$Bank_Statement_id = $BankStatementData[$i]['Id'];
			$Bank_Date = trim($BankStatementData[$i]['Date']);
			$Bank_ChequeNo = trim($BankStatementData[$i]['ChequeNo']);
			$Bank_Description = $BankStatementData[$i]['Bank_Description'];
			$Bank_PaidAmt = trim($BankStatementData[$i]['Debit']);
			$Bank_ReceivedAmt = trim($BankStatementData[$i]['Credit']);
			$Bank_Notes = trim($BankStatementData[$i]['Notes']);
			
			if(!empty($Bank_Notes))
			{
				$Bank_Description .= "<br> Note: ".$Bank_Notes; 
			}
			
			$Amount = 0;
			$TransactionVoucherType = 0;
			
			/*if($Bank_ChequeNo == 751893)
			{
				echo "<pre>";
				print_r($BankStatementData[$i]);
				echo "</pre>";
			}
			
*/
			
			// Basic Bank Statement Details
			$BankDetails = array('Date' => $Bank_Date, 'ChequeNumber' => $Bank_ChequeNo, 'Debit' => $Bank_PaidAmt, 'Credit' => $Bank_ReceivedAmt, 'BankStatementID' => $Bank_Statement_id, 'Bank_Description' => $Bank_Description);
			
			if($Bank_PaidAmt > 0 && is_numeric($Bank_PaidAmt)) // If Paid Amount is greater than 0 means it's Payment 
			{
				/*if($i == 207)
				{
					echo "<br>Inside Paid Amount";
					echo "<pre>";
					print_r($BankStatementData[$i]);
					echo "</pre>";
				}*/
					
				if(in_array($Bank_ChequeNo,$W2s_PaymentDetails_ChequeNo)) // Now we know it's payment so we need to check chequeNumber in W2s_PaymentDetails_ChequeNo array
				{	
					/*if($i == 207)
					{
					echo "<br>Cheque Number";						
					}*/

					$PaymentData = array_keys($W2s_PaymentDetails_ChequeNo,$Bank_ChequeNo); // get All Data who has same cheque Number
					$Bank_Register_Ids = array();
					$Bank_Register_ChkDetailsIds = array();
					$IsSplitEntry = false;
					$tableindexArray = array();
					$registerindexArray = array();
					$SplitEntryValues = array();
					$IsAmountMatch = false;
					
					/*if($i == 207)
					{
						echo "<pre>";
						print_r($PaymentData);
						echo "</pre>";						
					}*/
					
					if(count($PaymentData) > 1) // if we have multiple entry with Same Cheque Number 
					{
						foreach($PaymentData as $index)
						{
							if($IsAmountMatch == true) // If Amount Matched . Jump out of loop
							{
								break;
							}
							$ID = $W2s_PaymentDetails_ID[$index]; // Table ID of payment
							
							$filtered = array_keys($W2s_Bank_Register_CheckDetailID,$ID); // Check Whether Payment Table ID present in Bankregister Table or not
							
							if(count($filtered) > 1) // If it present more than 1 time means there is one receipt entry and one is payment
							{
								foreach($filtered as $w2s_bank_register_index)
								{
									$BankRegisterVoucherType = $Bank_Register_Details[$w2s_bank_register_index]['VoucherTypeID'];
									if($BankRegisterVoucherType == VOUCHER_PAYMENT || $BankRegisterVoucherType == VOUCHER_CONTRA) // When it is payment then only go inside
									{
										
										if($Bank_Register_Details[$w2s_bank_register_index]['PaidAmount'] == $Bank_PaidAmt) // If single Amount Match then reset array and variable and Store information about it
										{
											$Bank_Register_Ids = array();
											$Bank_Register_ChkDetailsIds = array();
											$tableindexArray = array();
											$registerindexArray = array();
											$SplitEntryValues = array();
											$IsAmountMatch = true;
											$IsSplitEntry = false;
											$Amount = $Bank_Register_Details[$w2s_bank_register_index]['PaidAmount'];
											$TransactionVoucherType = $BankRegisterVoucherType;
											array_push($Bank_Register_Ids,$Bank_Register_Details[$w2s_bank_register_index]['id']);
											array_push($SplitEntryValues,$Bank_Register_Details[$w2s_bank_register_index]['PaidAmount']);
											array_push($Bank_Register_ChkDetailsIds,$Bank_Register_Details[$w2s_bank_register_index]['ChkDetailID']);
											array_push($tableindexArray,$index);
											array_push($registerindexArray,$w2s_bank_register_index);
											break;
										}
										else
										{
											// If Amount not matched add Amount to Previous one
											$IsSplitEntry = true;
											$TransactionVoucherType = $BankRegisterVoucherType;
											array_push($Bank_Register_Ids,$Bank_Register_Details[$w2s_bank_register_index]['id']);
											array_push($SplitEntryValues,$Bank_Register_Details[$w2s_bank_register_index]['PaidAmount']);
											array_push($Bank_Register_ChkDetailsIds,$Bank_Register_Details[$w2s_bank_register_index]['ChkDetailID']);
											array_push($tableindexArray,$index);
											array_push($registerindexArray,$w2s_bank_register_index);
											$Amount += $Bank_Register_Details[$w2s_bank_register_index]['PaidAmount'];	
										}
									}
									else
									{
										continue;
									}
								}
							}
							else
							{
								$w2s_bank_register_index = $filtered[0];
								$BankRegisterVoucherType = $Bank_Register_Details[$w2s_bank_register_index]['VoucherTypeID'];
								if($BankRegisterVoucherType == VOUCHER_PAYMENT || $BankRegisterVoucherType == VOUCHER_CONTRA) 
								{
									if($Bank_Register_Details[$w2s_bank_register_index]['PaidAmount'] == $Bank_PaidAmt) // If single Amount Match then reset array and variable and Store information about it
									{
										$Bank_Register_Ids = array();
										$Bank_Register_ChkDetailsIds = array();
										$tableindexArray = array();
										$registerindexArray = array();
										$SplitEntryValues = array();
										$IsAmountMatch = true;
										$IsSplitEntry = false;
										
										$Amount = $Bank_Register_Details[$w2s_bank_register_index]['PaidAmount'];
										$TransactionVoucherType = $BankRegisterVoucherType;
										array_push($Bank_Register_Ids,$Bank_Register_Details[$w2s_bank_register_index]['id']);
										array_push($SplitEntryValues,$Bank_Register_Details[$w2s_bank_register_index]['PaidAmount']);
										array_push($Bank_Register_ChkDetailsIds,$Bank_Register_Details[$w2s_bank_register_index]['ChkDetailID']);
										array_push($tableindexArray,$index);
										array_push($registerindexArray,$w2s_bank_register_index);
										break;
									}
									else
									{
										// If Amount not matched add Amount to Previous one
										$IsSplitEntry = true;
										$TransactionVoucherType = $BankRegisterVoucherType;
										array_push($Bank_Register_Ids,$Bank_Register_Details[$w2s_bank_register_index]['id']);
										array_push($SplitEntryValues,$Bank_Register_Details[$w2s_bank_register_index]['PaidAmount']);
										array_push($Bank_Register_ChkDetailsIds,$Bank_Register_Details[$w2s_bank_register_index]['ChkDetailID']);
										array_push($tableindexArray,$index);
										array_push($registerindexArray,$w2s_bank_register_index);
										$Amount += $Bank_Register_Details[$w2s_bank_register_index]['PaidAmount'];	
									}
								}
							}
						}
						
						/*if($i == 207)
						{
							echo "<br>Amount : ".$Bank_Register_Details[$w2s_bank_register_index]['PaidAmount'];
							echo "<pre>";
							print_r($Amount);
							echo "</pre>";						
						}*/
					}
					else // If it is a single Entry then
					{
						foreach($PaymentData as $index)
						{
							$ID = $W2s_PaymentDetails_ID[$index];
							$filtered = array_keys($W2s_Bank_Register_CheckDetailID,$ID);
							/*
							if($Bank_ChequeNo == '1350')
							{
								echo "<pre>";
								print_r($W2s_Bank_Register_CheckDetailID);
								echo "</pre>";
								echo "<br>Paymeent ID : ".$ID;
								echo "<pre>";
								print_r($filtered);
								echo "</pre>";						
							}
							*/
							
							
							if(count($filtered) > 1)
							{
								foreach($filtered as $w2s_bank_register_index)
								{
									
									$BankRegisterVoucherType = $Bank_Register_Details[$w2s_bank_register_index]['VoucherTypeID'];
									
									if($BankRegisterVoucherType == VOUCHER_PAYMENT  || $BankRegisterVoucherType == VOUCHER_CONTRA)
									{
										$TransactionVoucherType = $BankRegisterVoucherType;
										$Amount += $Bank_Register_Details[$w2s_bank_register_index]['PaidAmount'];
										array_push($Bank_Register_Ids,$Bank_Register_Details[$w2s_bank_register_index]['id']);
										array_push($SplitEntryValues,$Bank_Register_Details[$w2s_bank_register_index]['PaidAmount']);
										array_push($Bank_Register_ChkDetailsIds,$Bank_Register_Details[$w2s_bank_register_index]['ChkDetailID']);										
										array_push($tableindexArray,$index);
										array_push($registerindexArray,$w2s_bank_register_index);
									}
									else
									{
										continue;
									}
								}
							}
							else
							{
								$w2s_bank_register_index = $filtered[0];
								$BankRegisterVoucherType = $Bank_Register_Details[$w2s_bank_register_index]['VoucherTypeID'];
								
								/*if($Bank_ChequeNo == '1350')
								{
									echo "<pre>";
									print_r($Bank_Register_Details[$w2s_bank_register_index]);
									echo "</pre>";						
								}*/
								
								if($BankRegisterVoucherType == VOUCHER_PAYMENT  || $BankRegisterVoucherType == VOUCHER_CONTRA)
								{
									$TransactionVoucherType = $BankRegisterVoucherType;
									$Amount = $Bank_Register_Details[$w2s_bank_register_index]['PaidAmount'];
									array_push($Bank_Register_Ids,$Bank_Register_Details[$w2s_bank_register_index]['id']);
									array_push($SplitEntryValues,$Bank_Register_Details[$w2s_bank_register_index]['PaidAmount']);
									array_push($Bank_Register_ChkDetailsIds,$Bank_Register_Details[$w2s_bank_register_index]['ChkDetailID']);
									array_push($tableindexArray,$index);
									array_push($registerindexArray,$w2s_bank_register_index);
								}
							}
						}
					}
					
					$Bank_Register_Ids = implode(',',$Bank_Register_Ids);
						
					if($Bank_PaidAmt == $Amount)//  If Amount Match the store data under Match key
					{
						/*if($i == 207)
						{
							echo "<br>Amount Matched";
						}
						echo "<br>Count : ".$i;*/
						$LedgerID = array();
						$LedgerName = array();
						$Result[MATCH_ENTRY][$i] = $BankDetails;
						$Result[MATCH_ENTRY][$i]['ID'] = $Bank_Register_Ids ;
						$Result[MATCH_ENTRY][$i]['ChkDetailID'] = $Bank_Register_ChkDetailsIds;
						$Result[MATCH_ENTRY][$i]['W2s_Comments'] = $W2s_PaymentDetails_Comments[$index];
						$Result[MATCH_ENTRY][$i]['VoucherType'] = $TransactionVoucherType;
						if($IsSplitEntry == true)
						{
							
							$Amount_commnet = implode(',',$SplitEntryValues);
							$Result[MATCH_ENTRY][$i]['error_msg'] = "Cheque Number '".$Bank_ChequeNo."' is split entry. Amount's '".$Amount_commnet."' total '".$Bank_PaidAmt."' Matched";
						}
						else
						{
							
							$Result[MATCH_ENTRY][$i]['error_msg'] = "Cheque Number '".$Bank_ChequeNo."' and Amount '".$Bank_PaidAmt."' Matched";							
						}
						
						$tableindexArray = array_unique($tableindexArray);
						foreach($tableindexArray as $tableindex) // Remove used data from array
						{
							array_push($LedgerID,$W2s_PaymentDetails_PaidTo[$tableindex]);
							if(!empty($W2s_PaymentDetails_PaidTo[$tableindex]))
							{
								array_push($LedgerName,$this->obj_utility->getLedgerName($W2s_PaymentDetails_PaidTo[$tableindex]));	
							}
							
							unset($W2s_PaymentDetails_ChequeNo[$tableindex]);
							unset($W2s_PaymentDetails_ID[$tableindex]);
							unset($W2s_PaymentDetails_PaidTo[$tableindex]);
							unset($W2s_PaymentDetails_Comments[$tableindex]);
						}
						
						$registerindexArray = array_unique($registerindexArray);
						foreach($registerindexArray as $registerindex) // Remove used data from array
						{
							unset($W2s_Bank_Register_CheckDetailID[$registerindex]);
							unset($Bank_Register_Details[$registerindex]);
						}
						
						$LedgerID = implode(',',$LedgerID);
						$LedgerName = implode(', ',$LedgerName);
						$Result[MATCH_ENTRY][$i]['LedgerID'] = $LedgerID;
						$Result[MATCH_ENTRY][$i]['LedgerName'] = $LedgerName;
						
						
						
					}
					else // If Amount Not Matched Store under Present in Bank Key
					{
						/*if($i == 207)
						{
							echo "<br>Amount Not Matched";
						}
						echo "<br>Count : ".$i;*/
						$Result[PRESENT_IN_BANK][$i] = $BankDetails;
						$Result[PRESENT_IN_BANK][$i]['VoucherType'] = $TransactionVoucherType;
						if($IsSplitEntry == true)
						{
							$Amount_commnet = implode(',',$SplitEntryValues);
							$Result[PRESENT_IN_BANK][$i]['error_msg'] = "Cheque Number '".$Bank_ChequeNo."' is split entry. Amount's ".$Amount_commnet."' total '".$Amount."' not matching";
						}
						else
						{
							$Result[PRESENT_IN_BANK][$i]['error_msg'] = "Cheque Number '".$Bank_ChequeNo."' and Amount '".$Bank_PaidAmt."'not matching with '".$Amount."'";							
						}
					}
				}
				else // Cheque Number not found in Payment Details Table 
				{
					//echo "<br>Count : ".$i;
					$Result[PRESENT_IN_BANK][$i] = $BankDetails;
					$Result[PRESENT_IN_BANK][$i]['VoucherType'] = $TransactionVoucherType;
					$Result[PRESENT_IN_BANK][$i]['error_msg'] = "ChequeNumber : '".$Bank_ChequeNo."' Not Found";	
				}
			}
			else if($Bank_ReceivedAmt > 0 && is_numeric($Bank_ReceivedAmt)) // If Received Amount is greater than 0 means it's Deposit 
			{
				/*if($Bank_ChequeNo == 751893)
				{
					echo "<br>Inside the Cheque";
					echo "<pre>";
					print_r($BankStatementData[$i]);
					echo "</pre>";
				}*/
				
				
				if(!empty($Bank_ChequeNo) && $Bank_ChequeNo <> 0)
				{
					if(in_array($Bank_ChequeNo,$W2s_ChequeEntry_ChequeNo)) // Now we know it's Deposit so we need to check chequeNumber in W2s_ChequeEntry_ChequeNo array
					{
						$ChequeData = array_keys($W2s_ChequeEntry_ChequeNo,$Bank_ChequeNo); // if we have multiple entry with Same Cheque Number
						
						$Bank_Register_Ids = array();
						$Bank_Register_ChkDetailsIds = array();
						$tableindexArray = array();
						$registerindexArray = array();
						$IsSplitEntry = false;
						$SplitEntryValues = array();
						$IsAmountMatch = false;
						
						if(count($ChequeData) > 1)
						{
							foreach($ChequeData as $index)
							{
								if($IsAmountMatch == true)// If Amount Matched . Jump out of loop
								{
									break;
								}
								
								$ID = $W2s_ChequeEntry_ID[$index]; // Table ID of chequeEntry Table
								
								$filtered = array_keys($W2s_Bank_Register_CheckDetailID,$ID); // Check Whether ChequeEntry Table ID present in Bankregister Table or not
								
								if(count($filtered) > 1) // If it present more than 1 time means there is one receipt entry and one is payment
								{
									foreach($filtered as $w2s_bank_register_index)
									{
										$BankRegisterVoucherType = $Bank_Register_Details[$w2s_bank_register_index]['VoucherTypeID'];
										if($BankRegisterVoucherType == VOUCHER_RECEIPT || $BankRegisterVoucherType == VOUCHER_CONTRA) //When it is Receipt then only go inside
										{
											
											if($Bank_Register_Details[$w2s_bank_register_index]['ReceivedAmount'] == $Bank_ReceivedAmt) // If single Amount Match then reset array and variable and Store information about it
											{
												$Bank_Register_Ids = array();
												$Bank_Register_ChkDetailsIds = array();
												$tableindexArray = array();
												$registerindexArray = array();
												$SplitEntryValues = array();
												
												$Amount = $Bank_Register_Details[$w2s_bank_register_index]['ReceivedAmount'];
												$IsAmountMatch = true;
												
												$TransactionVoucherType = $BankRegisterVoucherType;
												array_push($Bank_Register_Ids,$Bank_Register_Details[$w2s_bank_register_index]['id']);
												array_push($SplitEntryValues,$Bank_Register_Details[$w2s_bank_register_index]['ReceivedAmount']);
												array_push($Bank_Register_ChkDetailsIds,$Bank_Register_Details[$w2s_bank_register_index]['ChkDetailID']);
												array_push($tableindexArray,$index);
												array_push($registerindexArray,$w2s_bank_register_index);
												break;
											}
											else // If Amount not matched add Amount to Previous one
											{
												$IsSplitEntry = true;
												$TransactionVoucherType = $BankRegisterVoucherType;
												array_push($Bank_Register_Ids,$Bank_Register_Details[$w2s_bank_register_index]['id']);
												array_push($SplitEntryValues,$Bank_Register_Details[$w2s_bank_register_index]['ReceivedAmount']);
												array_push($Bank_Register_ChkDetailsIds,$Bank_Register_Details[$w2s_bank_register_index]['ChkDetailID']);
												array_push($tableindexArray,$index);
												array_push($registerindexArray,$w2s_bank_register_index);
												$Amount += $Bank_Register_Details[$w2s_bank_register_index]['ReceivedAmount'];	
											}
										}
										else
										{
											continue;
										}
									}
								}
								else
								{
									$w2s_bank_register_index = $filtered[0];
									$BankRegisterVoucherType = $Bank_Register_Details[$w2s_bank_register_index]['VoucherTypeID'];
									if($BankRegisterVoucherType == VOUCHER_RECEIPT || $BankRegisterVoucherType == VOUCHER_CONTRA)
									{
										$BankRegisterVoucherType = $Bank_Register_Details[$w2s_bank_register_index]['VoucherTypeID'];
										if($BankRegisterVoucherType == VOUCHER_RECEIPT || $BankRegisterVoucherType == VOUCHER_CONTRA)
										{
											if($Bank_Register_Details[$w2s_bank_register_index]['ReceivedAmount'] == $Bank_ReceivedAmt)// If single Amount Match then reset array and variable and Store information about it
											{
												$Bank_Register_Ids = array();
												$Bank_Register_ChkDetailsIds = array();
												$tableindexArray = array();
												$registerindexArray = array();
												$SplitEntryValues = array();
												$IsSplitEntry = false;
												$Amount = $Bank_Register_Details[$w2s_bank_register_index]['ReceivedAmount'];
												$IsAmountMatch = true;
												
												$TransactionVoucherType = $BankRegisterVoucherType;
												array_push($Bank_Register_Ids,$Bank_Register_Details[$w2s_bank_register_index]['id']);
												array_push($SplitEntryValues,$Bank_Register_Details[$w2s_bank_register_index]['ReceivedAmount']);
												array_push($Bank_Register_ChkDetailsIds,$Bank_Register_Details[$w2s_bank_register_index]['ChkDetailID']);
												array_push($tableindexArray,$index);
												array_push($registerindexArray,$w2s_bank_register_index);
												break;
											}
											else
											{
												// If Amount not matched add Amount to Previous one
												$IsSplitEntry = true;
												$TransactionVoucherType = $BankRegisterVoucherType;
												array_push($Bank_Register_Ids,$Bank_Register_Details[$w2s_bank_register_index]['id']);
												array_push($SplitEntryValues,$Bank_Register_Details[$w2s_bank_register_index]['ReceivedAmount']);
												array_push($Bank_Register_ChkDetailsIds,$Bank_Register_Details[$w2s_bank_register_index]['ChkDetailID']);
												array_push($tableindexArray,$index);
												array_push($registerindexArray,$w2s_bank_register_index);
												$Amount += $Bank_Register_Details[$w2s_bank_register_index]['ReceivedAmount'];	
											}
										}
									}
								}
							}
						
						}
						else
						{
							/*if($Bank_ChequeNo == 751893)
							{
								echo "<br>Else Condition";
							}*/
							foreach($ChequeData as $index)
							{
								$ID = $W2s_ChequeEntry_ID[$index];
								
								$filtered = array_keys($W2s_Bank_Register_CheckDetailID,$ID);
								
								/*if($Bank_ChequeNo == 751893)
								{
									echo "<pre>";
									print_r($W2s_Bank_Register_CheckDetailID);
									echo "</pre>";
									echo "<br>ID is ".$ID;
									echo "<br>Else Condition";
								}*/
								
								
								if(count($filtered) > 1)
								{
									foreach($filtered as $w2s_bank_register_index)
									{
										$BankRegisterVoucherType = $Bank_Register_Details[$w2s_bank_register_index]['VoucherTypeID'];
										if($BankRegisterVoucherType == VOUCHER_RECEIPT || $BankRegisterVoucherType == VOUCHER_CONTRA)
										{
											$TransactionVoucherType = $BankRegisterVoucherType;
											$Amount += $Bank_Register_Details[$w2s_bank_register_index]['ReceivedAmount'];
											array_push($Bank_Register_Ids,$Bank_Register_Details[$w2s_bank_register_index]['id']);
											array_push($SplitEntryValues,$Bank_Register_Details[$w2s_bank_register_index]['ReceivedAmount']);
											array_push($Bank_Register_ChkDetailsIds,$Bank_Register_Details[$w2s_bank_register_index]['ChkDetailID']);											
											array_push($tableindexArray,$index);
											array_push($registerindexArray,$w2s_bank_register_index);
										}
										else
										{
											continue;
										}
									}
								}
								else
								{
									/*if($Bank_ChequeNo == 751893)
									{
										var_dump($filtered);
										echo "<br>Again Else Condition";
									}*/
									$w2s_bank_register_index = $filtered[0];
									$BankRegisterVoucherType = $Bank_Register_Details[$w2s_bank_register_index]['VoucherTypeID'];
									if($BankRegisterVoucherType == VOUCHER_RECEIPT || $BankRegisterVoucherType == VOUCHER_CONTRA)
									{
										/*if($Bank_ChequeNo == 751893)
										{
											echo "<br>Voucher Type Receipt Condition";
										}*/
										$TransactionVoucherType = $BankRegisterVoucherType;
										$Amount = $Bank_Register_Details[$w2s_bank_register_index]['ReceivedAmount'];
										array_push($Bank_Register_Ids,$Bank_Register_Details[$w2s_bank_register_index]['id']);
										array_push($SplitEntryValues,$Bank_Register_Details[$w2s_bank_register_index]['ReceivedAmount']);
										array_push($Bank_Register_ChkDetailsIds,$Bank_Register_Details[$w2s_bank_register_index]['ChkDetailID']);										
										array_push($tableindexArray,$index);
										array_push($registerindexArray,$w2s_bank_register_index);				
									}
								}
							}
						}
						
						
						$Bank_Register_Ids = implode(',',$Bank_Register_Ids);
						
						if($Bank_ReceivedAmt == $Amount) //  If Amount Match the store data under Match key
						{
							$LedgerID = array();
							$LedgerName = array();
							$Result[MATCH_ENTRY][$i] = $BankDetails;
							$Result[MATCH_ENTRY][$i]['ID'] = $Bank_Register_Ids ;
							$Result[MATCH_ENTRY][$i]['ChkDetailID'] = $Bank_Register_ChkDetailsIds;
							$Result[MATCH_ENTRY][$i]['W2s_Comments'] = $W2s_ChequeEntry_Comments[$index];
							$Result[MATCH_ENTRY][$i]['VoucherType'] = $TransactionVoucherType;
							if($IsSplitEntry == true)
							{
								$Amount_commnet = implode(',',$SplitEntryValues);
								$Result[MATCH_ENTRY][$i]['error_msg'] = "Cheque Number '".$Bank_ChequeNo."' is split entry. Amount's '".$Amount_commnet."' total '".$Bank_ReceivedAmt."' Matched";
							}
							else
							{
								$Result[MATCH_ENTRY][$i]['error_msg'] = "Cheque Number '".$Bank_ChequeNo."' and Amount '".$Bank_ReceivedAmt."' Matched";							
							}
							
							
							$tableindexArray = array_unique($tableindexArray);
							foreach($tableindexArray as $tableindex)  // Remove used data from array
							{
								array_push($LedgerID,$W2s_ChequeEntry_PaidBy[$tableindex]);
								
								if(!empty($W2s_ChequeEntry_PaidBy[$tableindex]))
								{
									array_push($LedgerName,$this->obj_utility->getLedgerName($W2s_ChequeEntry_PaidBy[$tableindex]));
								}

								unset($W2s_ChequeEntry_ChequeNo[$tableindex]);
								unset($W2s_ChequeEntry_ID[$tableindex]);
								unset($W2s_ChequeEntry_PaidBy[$tableindex]);
								unset($W2s_ChequeEntry_Comments[$tableindex]);
							}
							
							$registerindexArray = array_unique($registerindexArray);
							foreach($registerindexArray as $registerindex) // Remove used data from array
							{
								unset($W2s_Bank_Register_CheckDetailID[$registerindex]);
								unset($Bank_Register_Details[$registerindex]);
							}
						
							$LedgerID = implode(',',$LedgerID);
							$LedgerName = implode(', ',$LedgerName);
							$Result[MATCH_ENTRY][$i]['LedgerID'] = $LedgerID;
							$Result[MATCH_ENTRY][$i]['LedgerName'] = $LedgerName;
							
						}
						else // If Amount Not Matched Store under Present in Bank Key 
						{
								//echo "<br>Count : ".$i;
								$Result[PRESENT_IN_BANK][$i] = $BankDetails;
								$Result[PRESENT_IN_BANK][$i]['VoucherType'] = $TransactionVoucherType;
								if($IsSplitEntry == true)
								{
									$Amount_commnet = implode(',',$SplitEntryValues);
									$Result[PRESENT_IN_BANK][$i]['error_msg'] = "Cheque Number '".$Bank_ChequeNo."' is split entry. Amount's ".$Amount_commnet."' total '".$Amount."' not matching with '".$Bank_ReceivedAmt."'";
								}
								else
								{
									$Result[PRESENT_IN_BANK][$i]['error_msg'] = "Cheque Number '".$Bank_ChequeNo."' and Amount '".$Bank_ReceivedAmt."' not matching with '".$Amount."'";							
								}
	
						}
					}
					else
					{
						//echo "<br>Count : ".$i;
						$Result[PRESENT_IN_BANK][$i] = $BankDetails;
						$Result[PRESENT_IN_BANK][$i]['VoucherType'] = $TransactionVoucherType;
						$Result[PRESENT_IN_BANK][$i]['error_msg'] = "ChequeNumber : '".$Bank_ChequeNo."' not found in system";
					}
				}
				else // If We Don't have Cheque Number in Bank Statement
				{
					if(strpos(strtolower($Bank_Description),'neft') !== false) // We check in Description for NFT word
					{
						if(in_array(getDBFormatDate($Bank_Date),$NeftDates)) // If Date is present in NEFT data 
						{
							$NeftChequeNumber = array_keys($NeftData[getDBFormatDate($Bank_Date)]); // All the Cheque Number keys present in Same Date
							$IsAmountMatch = false;
							foreach($NeftChequeNumber as $NeftKey)
							{
								$NeftChequeData = $NeftData[getDBFormatDate($Bank_Date)][$NeftKey]; // Keys of Neft Data With  Cheque Number
								//var_dump($NeftChequeData['Amount']);
								if($IsAmountMatch == true)
								{
									//break;
								}
								
								if((int)$NeftChequeData['Amount'] == $Bank_ReceivedAmt) // If Amount Matched then go inside
								{
										// Now Amount is Matched so we need to store details about this entry
									
										$Amount = $Bank_ReceivedAmt;
										$NeftIDs = explode(',',$NeftChequeData['IDs']);
										$Bank_Register_Ids = array();
										$tableindexArray = array();
										$registerindexArray = array();
										$index = -1;
										foreach($NeftIDs as $NeftID) // Neft may be single or multiple entries
										{
											
											$ChequeEntryKey = array_keys($W2s_ChequeEntry_ID,$NeftID); // Cheque Entry Table ID  key
											
											$index = $ChequeEntryKey[0];
											array_push($tableindexArray,$ChequeEntryKey[0]);	
											
											$filtered = array_keys($W2s_Bank_Register_CheckDetailID,$NeftID); 
											
											if($filtered > 1)
											{
												foreach($filtered as $w2s_bank_register_index) // If It is multiple entry in Bank Register Table
												{
													$BankRegisterVoucherType = $Bank_Register_Details[$w2s_bank_register_index]['VoucherTypeID'];
													if($BankRegisterVoucherType == VOUCHER_RECEIPT) // If is Receipt Then only go inside
													{
														array_push($Bank_Register_Ids,$Bank_Register_Details[$w2s_bank_register_index]['id']);
														array_push($registerindexArray,$w2s_bank_register_index);
													}
													else
													{
														continue;
													}	
												}
											}
											else
											{
												$w2s_bank_register_index = $filtered[0];
												$BankRegisterVoucherType = $Bank_Register_Details[$w2s_bank_register_index]['VoucherTypeID'];
												
												if($BankRegisterVoucherType == VOUCHER_RECEIPT) // If is Receipt Then only go inside
												{
													
													array_push($Bank_Register_Ids,$Bank_Register_Details[$w2s_bank_register_index]['id']);													
													array_push($registerindexArray,$w2s_bank_register_index);
												}
											}
											
										}
										$IsSplitEntry= false;
										if(count($Bank_Register_Ids) > 1)
										{
											$IsSplitEntry = true;
										}
										
										
										$LedgerID = array();
										$LedgerName = array();
										
										$Bank_Register_Ids = implode(',',$Bank_Register_Ids);
										$IsAmountMatch = true;

										$Result[MATCH_ENTRY][$i] = $BankDetails;
										$Result[MATCH_ENTRY][$i]['ID'] = $Bank_Register_Ids;
										$Result[MATCH_ENTRY][$i]['ChkDetailID'] = $NeftChequeData['IDs'];
										$Result[MATCH_ENTRY][$i]['ChequeNumber'] = 'NEFT';
										$Result[MATCH_ENTRY][$i]['W2s_Comments'] = $W2s_ChequeEntry_Comments[$index];
										$Result[MATCH_ENTRY][$i]['VoucherType'] = VOUCHER_RECEIPT;
										
										if($IsSplitEntry == true)
										{
											$Result[MATCH_ENTRY][$i]['error_msg'] = "This is split neft entry Date '".$Bank_Date."' and Amounts are '".$NeftChequeData['SplitAmount']."' total '".$Bank_ReceivedAmt."' Matched";											
										}
										else
										{
											$Result[MATCH_ENTRY][$i]['error_msg'] = "Neft Date '".$Bank_Date."' and Amount '".$Bank_ReceivedAmt."' Matched";
										}
										//echo "<br>Count T3 : ".$i;	
										
										unset($Result[PRESENT_IN_BANK][$i]);
										unset($NeftData[getDBFormatDate($Bank_Date)][$NeftKey]);
										
										$tableindexArray = array_unique($tableindexArray);
										foreach($tableindexArray as $tableindex)
										{
											array_push($LedgerID,$W2s_ChequeEntry_PaidBy[$tableindex]);
											
											if(!empty($W2s_ChequeEntry_PaidBy[$tableindex]))
											{
												array_push($LedgerName,$this->obj_utility->getLedgerName($W2s_ChequeEntry_PaidBy[$tableindex]));
											}
											unset($W2s_ChequeEntry_ChequeDates[$tableindex]);
											unset($W2s_ChequeEntry_DepositIDs[$tableindex]);
											unset($W2s_ChequeEntry_Comments[$tableindex]);
											unset($W2s_ChequeEntry_ID[$tableindex]);
											unset($W2s_ChequeEntry_PaidBy[$tableindex]);
										}
										
										$registerindexArray = array_unique($registerindexArray);
										foreach($registerindexArray as $registerindex)
										{
											unset($W2s_Bank_Register_CheckDetailID[$registerindex]);
											unset($Bank_Register_Details[$registerindex]);
										}
										
										
										$LedgerID = implode(',',$LedgerID);
										$LedgerName = implode(', ',$LedgerName);
										$Result[MATCH_ENTRY][$i]['LedgerID'] = $LedgerID;
										$Result[MATCH_ENTRY][$i]['LedgerName'] = $LedgerName;
										break;		
								}
								else
								{
									//echo "<br>Count T1 : ".$i;
									$Result[PRESENT_IN_BANK][$i] = $BankDetails;
									$Result[PRESENT_IN_BANK][$i]['error_msg'] = "Neft Date '".$Bank_Date."' matched but bank amount '".$Bank_ReceivedAmt."' not matching";
								} 
							}
						}
						else
						{
							//echo "<br>Count T2: ".$i;
							$Result[PRESENT_IN_BANK][$i] = $BankDetails;
							$Result[PRESENT_IN_BANK][$i]['error_msg'] = "Neft Date '".$Bank_Date."' not Matched";
						}
					}
					else
					{
						//echo "<br>Count : ".$i;
						$Result[PRESENT_IN_BANK][$i] = $BankDetails;
						$Result[PRESENT_IN_BANK][$i]['error_msg'] = "ChequeNumber is empty and it's not neft";
					}
				}
			}
			else
			{
				//echo "<br>Count : ".$i;
				array_push($Result[PRESENT_IN_BANK][$i],$BankDetails);
				$Result[PRESENT_IN_BANK][$i]['error_msg'] = "Amount can not be 0 or empty";
			}
		}
		
		$Bank_Register_Details = array_values($Bank_Register_Details);
		$W2s_Bank_Register_CheckDetailID = array_values($W2s_Bank_Register_CheckDetailID);		
		

		$W2s_ChequeEntry_ID = array_values($W2s_ChequeEntry_ID);
		$W2s_ChequeEntry_PaidBy = array_values($W2s_ChequeEntry_PaidBy);
		$W2s_ChequeEntry_ChequeNo = array_values($W2s_ChequeEntry_ChequeNo);
		$W2s_ChequeEntry_Comments = array_values($W2s_ChequeEntry_Comments);		
		
		/*echo "<pre>";
		print_r($W2s_ChequeEntry_Comments);
		echo "</pre>";
		*/
		$W2s_PaymentDetails_ID = array_values($W2s_PaymentDetails_ID);
		$W2s_PaymentDetails_PaidTo = array_values($W2s_PaymentDetails_PaidTo);
		$W2s_PaymentDetails_ChequeNo = array_values($W2s_PaymentDetails_ChequeNo);	
		$W2s_PaymentDetails_Comments = array_values($W2s_PaymentDetails_Comments);		
		
		$PaidAmountArray = array_column($Bank_Register_Details,'PaidAmount');
		$ReceivedAmountArray = array_column($Bank_Register_Details,'ReceivedAmount');
		
		//var_dump($ReceivedAmountArray);
		
		/*echo "<br>Bank Count : ".count($Bank_Register_Details);
		echo "<br>W2s_Bank_Register_CheckDetailID Count : ".count($W2s_Bank_Register_CheckDetailID);		
		echo "<br>Paid Count : ".count($PaidAmountArray);*/
		/*echo "<br>Received Count : ".count($ReceivedAmountArray);
		echo "<br>Count : ".count($W2s_ChequeEntry_ID);
		echo "<br>Comment Count : ".count($W2s_ChequeEntry_Comments);
		
		var_dump($W2s_PaymentDetails_ID);
		*/
		
		foreach($Result[PRESENT_IN_BANK] as $keys => $values)
		{
			$temp_present_in_bank = array();
			$AmountMatchCounter = 0;
			
			if($values['Debit'] > 0 && !empty($values['Debit']))
			{
				$filter = array_keys($PaidAmountArray,$values['Debit']);
				
				if(!empty($filter))
				{
					foreach($filter as $index)
					{
						if($Bank_Register_Details[$index]['VoucherTypeID'] == VOUCHER_PAYMENT || $Bank_Register_Details[$index]['VoucherTypeID'] == VOUCHER_CONTRA)
						{
							$tableindex = array_keys($W2s_PaymentDetails_ID,$Bank_Register_Details[$index]['ChkDetailID']);						
							
							if(!empty($tableindex))
							{
								$tableindex = $tableindex[0];
								$temp_present_in_bank[$AmountMatchCounter]['ID'] = $Bank_Register_Details[$index]['id'];
								$temp_present_in_bank[$AmountMatchCounter]['Date'] = $Bank_Register_Details[$index]['Cheque Date'];
								$temp_present_in_bank[$AmountMatchCounter]['ChkDetailID'] = $Bank_Register_Details[$index]['ChkDetailID'];							
								$temp_present_in_bank[$AmountMatchCounter]['ChequeNumber'] = $W2s_PaymentDetails_ChequeNo[$tableindex];
								$temp_present_in_bank[$AmountMatchCounter]['LedgerID'] = $W2s_PaymentDetails_PaidTo[$tableindex];	
								if(!empty($W2s_PaymentDetails_PaidTo[$tableindex]))
								{
									$temp_present_in_bank[$AmountMatchCounter]['LedgerName'] = $this->obj_utility->getLedgerName($W2s_PaymentDetails_PaidTo[$tableindex]);															
								}							
								$temp_present_in_bank[$AmountMatchCounter]['Amount'] = $Bank_Register_Details[$index]['PaidAmount'];						
								$temp_present_in_bank[$AmountMatchCounter]['comments'] = $W2s_PaymentDetails_Comments[$tableindex];
								$Result[PRESENT_IN_BANK][$keys]['VoucherType'] = $Bank_Register_Details[$index]['VoucherTypeID'];
								$Result[PRESENT_IN_BANK][$keys][AMOUNT_MATCH] = $temp_present_in_bank;
								$AmountMatchCounter++;
								
								unset($Bank_Register_Details[$index]);
								unset($W2s_Bank_Register_CheckDetailID[$index]);
								unset($PaidAmountArray[$index]);
								
								unset($W2s_PaymentDetails_ChequeNo[$tableindex]);
								unset($W2s_PaymentDetails_ID[$tableindex]);
								unset($W2s_PaymentDetails_PaidTo[$tableindex]);
							}
						}
					}
				}
			}
			else if($values['Credit'] > 0 && !empty($values['Credit']))
			{
				$filter = array_keys($ReceivedAmountArray,$values['Credit']);
				
				if(!empty($filter))
				{
					foreach($filter as $index)
					{
						if($Bank_Register_Details[$index]['VoucherTypeID'] == VOUCHER_RECEIPT || $Bank_Register_Details[$index]['VoucherTypeID'] == VOUCHER_CONTRA)
						{
							
							$tableindex = array_keys($W2s_ChequeEntry_ID,$Bank_Register_Details[$index]['ChkDetailID']);						
							
							if(!empty($tableindex))
							{
								$tableindex = $tableindex[0];							
								$temp_present_in_bank[$AmountMatchCounter]['ID'] = $Bank_Register_Details[$index]['id'];
								$temp_present_in_bank[$AmountMatchCounter]['Date'] = $Bank_Register_Details[$index]['Cheque Date'];
								$temp_present_in_bank[$AmountMatchCounter]['ChkDetailID'] = $Bank_Register_Details[$index]['ChkDetailID'];
								$temp_present_in_bank[$AmountMatchCounter]['ChequeNumber'] = $W2s_ChequeEntry_ChequeNo[$tableindex];								
								$temp_present_in_bank[$AmountMatchCounter]['LedgerID'] = $W2s_ChequeEntry_PaidBy[$tableindex];
								$temp_present_in_bank[$AmountMatchCounter]['LedgerName'] = $this->obj_utility->getLedgerName($W2s_ChequeEntry_PaidBy[$tableindex]);							
								$temp_present_in_bank[$AmountMatchCounter]['Amount'] = $Bank_Register_Details[$index]['ReceivedAmount'];
								$temp_present_in_bank[$AmountMatchCounter]['comments'] = $W2s_ChequeEntry_Comments[$tableindex];
								$Result[PRESENT_IN_BANK][$keys]['VoucherType'] = $Bank_Register_Details[$index]['VoucherTypeID'];
								$Result[PRESENT_IN_BANK][$keys][AMOUNT_MATCH] = $temp_present_in_bank;
								
								$AmountMatchCounter++;
								
								unset($Bank_Register_Details[$index]);
								unset($W2s_Bank_Register_CheckDetailID[$index]);
								unset($ReceivedAmountArray[$index]);
								unset($W2s_ChequeEntry_ChequeNo[$tableindex]);
								unset($W2s_ChequeEntry_ChequeDates[$tableindex]);
								unset($W2s_ChequeEntry_DepositIDs[$tableindex]);
								unset($W2s_ChequeEntry_ID[$tableindex]);
								unset($W2s_ChequeEntry_PaidBy[$tableindex]);
							}
						}
					}
				}
			}
		}
		
		if(!empty($Bank_Register_Details)) // All Remaining Entries In BankRegister After Match with bank Statement
		{
			
			$cnt = 0;
			foreach($Bank_Register_Details as $k => $v)
			{
				$Comment = '';
				$ChequeNumber = '';
				$Result[PRESENT_IN_W2S][$cnt]['ID'] = $v['id'];
				$Result[PRESENT_IN_W2S][$cnt]['Date'] = $v['Date'];
				$Result[PRESENT_IN_W2S][$cnt]['Debit'] = $v['PaidAmount'];
				$Result[PRESENT_IN_W2S][$cnt]['Credit'] = $v['ReceivedAmount'];
				$Result[PRESENT_IN_W2S][$cnt]['VoucherType'] = $v['VoucherTypeID'];
				$Result[PRESENT_IN_W2S][$cnt]['ChkDetailID'] = $v['ChkDetailID'];
				
				if($v['VoucherTypeID'] == VOUCHER_PAYMENT || $v['VoucherTypeID'] == VOUCHER_CONTRA)
				{
					$index = array_search($v['ChkDetailID'],$W2s_PaymentDetails_ID);
					
					if($index !== false)
					{
						$Result[PRESENT_IN_W2S][$cnt]['LedgerID'] = $W2s_PaymentDetails_PaidTo[$index];
						if(!empty($W2s_PaymentDetails_PaidTo[$index]))
						{
							$Result[PRESENT_IN_W2S][$cnt]['LedgerName'] = $this->obj_utility->getLedgerName($W2s_PaymentDetails_PaidTo[$index]);	
						}
						$ChequeNumber = $W2s_PaymentDetails_ChequeNo[$index];
						$Comment = $W2s_PaymentDetails_Comments[$index];
					}
					
				}
				else if($v['VoucherTypeID'] == VOUCHER_RECEIPT)
				{
					$index = array_search($v['ChkDetailID'],$W2s_ChequeEntry_ID);
					
					if($index !== false)
					{
						$Result[PRESENT_IN_W2S][$cnt]['LedgerID'] = $W2s_ChequeEntry_PaidBy[$index];
						
						if(!empty($W2s_ChequeEntry_PaidBy[$index]))
						{
							$Result[PRESENT_IN_W2S][$cnt]['LedgerName'] = $this->obj_utility->getLedgerName($W2s_ChequeEntry_PaidBy[$index]);							
						}

						$ChequeNumber = $W2s_ChequeEntry_ChequeNo[$index];
						$Comment = $W2s_ChequeEntry_Comments[$index];
					}
				}
				
				if(!empty($ChequeNumber))
				{
					$Result[PRESENT_IN_W2S][$cnt]['ChequeNumber'] = $ChequeNumber;
				}
				else
				{
					$Result[PRESENT_IN_W2S][$cnt]['ChequeNumber'] = '-';							
				}
				
				if(!empty($Comment))
				{
					$Result[PRESENT_IN_W2S][$cnt]['W2s_Comments'] = $Comment;	
				}
				else
				{
					$Result[PRESENT_IN_W2S][$cnt]['W2s_Comments'] = '-';
				}
				
				$cnt++;
			}
		}
		
		return $Result;	
	}
	
	
	
	
	
	public function ReconcilationProcess($Flag, $BankID, $Data, $RadioSelection)
	{
	
		if($Flag == MATCH_ENTRY || $Flag == AMOUNT_MATCH)
		{
			return $Result = $this->ProcessMatchEntries($Data,$RadioSelection,$Flag);		
		}
		else if($Flag == PRESENT_IN_BANK)
		{
			return $Result = $this->ProcessPresentInBankEntries($Data, $BankID);			
		}
		else if($Flag == PRESENT_IN_W2S)
		{
			return $Result = $this->ProcessPresentInW2sEntries($Data);			
		}
		
	}
	
	
	
	private function ProcessMatchEntries($Data,$RadioSelection,$flag)
	{
		if(!empty($Data))
		{
		
			$TotalEntryReconcile = 0 ;
			for($i = 0 ; $i < count($Data); $i++)
			{
				$ReconcileDate = getDBFormatDate($Data[$i]['Date']);
				
				if($flag == AMOUNT_MATCH)
				{
					$BankRegisterIDs = $Data[$i][AMOUNT_MATCH][$RadioSelection[$i]]['ID'];	
				}
				else
				{
					$BankRegisterIDs = $Data[$i]['ID'];					
				}
				$BankStatementID = $Data[$i]['BankStatementID'];
				if(!empty($BankRegisterIDs))
				{
					
					$UpdateBankStatement = "UPDATE `actualbankstmt` SET `Reco_Status` = 1, `Reco_By` = '".$_SESSION['login_id']."' WHERE Id = '".$BankStatementID."'";
					$StatementResult = $this->m_dbConn->update($UpdateBankStatement);
					
					$UpdateBankRegister = "UPDATE `bankregister` SET `Reconcile Date` = '".$ReconcileDate."', `statement_id` = '".$BankStatementID."', `ReconcileStatus` = 1, `Reconcile` = 1 WHERE id in (".$BankRegisterIDs.")";
					$Result = $this->m_dbConn->update($UpdateBankRegister);
					$TotalEntryReconcile += $Result;	
				}
			}
			return "###".$TotalEntryReconcile;
		}
	}
	
	private function ProcessPresentInBankEntries($Data, $BankID)
	{
		$TotalEntryAddedToW2S = 0;
		$AlreadyExitsChequeNos = array();
		$TotalEntryNotAdded = 0;
		$SelectYearDescQuery = "SELECT YearDescription FROM `year` WHERE YearID = '".$_SESSION['default_year']."'"; 
		$YearDescResult = $this->m_dbConn->select($SelectYearDescQuery);
		//echo "<br>Test1";
		$LeafAndDepositName = RECO_TRANSACTION.' FY '.$YearDescResult[0]['YearDescription'];
		
		$DepositID = $this->getDepositID($LeafAndDepositName,$BankID);
		//echo "<br>Test2";
		$LeafID = $this->getLeafID($LeafAndDepositName,$BankID);
		//echo "<br>Test3";
		$IsSameCntApply = $this->obj_utility->IsSameCounterApply();
		
		if($_SESSION['default_suspense_account'] <> 0 && !empty($_SESSION['default_suspense_account']) && ($DepositID <> 0 || $LeafID <> 0))
		{
			
			for($i = 0 ; $i < count($Data); $i++)
			{
				$this->m_dbConn->begin_transaction();
				$Amount = 0; 
				$VoucherDate = $Data[$i]['Date'];
				$ChequeDate = $VoucherDate;
				$ChequeNumber = $Data[$i]['ChequeNumber'];
				$Comments = $Data[$i]['Bank_Description'];
				$IsCallUpdtCnt = true;
				$PayerBank = '-';
				$PayerBranch = '-';
				$BillType = Maintenance;
				
				
				if($Data[$i]['Credit'] > 0 && !empty($Data[$i]['Credit']))
				{
					$Amount = $Data[$i]['Credit'];	
					$PaidBy = $_SESSION['default_suspense_account'];
					
					if($IsSameCntApply == 1)
					{
						$Counter = $this->obj_utility->GetCounter(VOUCHER_RECEIPT,0);	
					}
					else
					{
						$Counter = $this->obj_utility->GetCounter(VOUCHER_RECEIPT, $BankID);		
					}

					$ExVoucherCounter = $Counter[0]['CurrentCounter'];
					$systemVoucherNo = $ExVoucherCounter;
					
					$Result = $this->obj_ChequeDetails->AddNewValues($VoucherDate, $ChequeDate, $ChequeNumber, $ExVoucherCounter,$systemVoucherNo,$IsCallUpdtCnt, $Amount, $PaidBy, $BankID, $PayerBank, $PayerBranch, $DepositID, $Comments,$BillType, 0, 0, 0, 0, 0, false,$GatewayID = "",0);
					
					if($Result == 'Insert')
					{
						$TotalEntryAddedToW2S++;
						$this->m_dbConn->commit();
					}
					else
					{
						$this->m_dbConn->rollback();
					}
				}
				else if($Data[$i]['Debit'] > 0 && !empty($Data[$i]['Debit']))
				{
					$Amount = $Data[$i]['Debit'];
					$PaidTo = $_SESSION['default_suspense_account'];
					
					if($IsSameCntApply == 1)
					{
						$Counter = $this->obj_utility->GetCounter(VOUCHER_PAYMENT,0);	
					}
					else
					{
						$Counter = $this->obj_utility->GetCounter(VOUCHER_PAYMENT, $BankID);		
					}
					
					$ExVoucherCounter = $Counter[0]['CurrentCounter'];
					$systemVoucherNo = $ExVoucherCounter;
					
					$Status = $this->obj_PaymentDetails->AddNewValues($LeafID, $_SESSION['society_id'], $PaidTo, $ChequeNumber, $ChequeDate, $ExVoucherCounter, $systemVoucherNo, $IsCallUpdtCnt, $Amount, $BankID, $Comments, $VoucherDate, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
	 				
					if($Status == '-2')
					{
						array_push($AlreadyExitsChequeNos,$ChequeNumber);
						$TotalEntryNotAdded++;
						$this->m_dbConn->rollback();
					}
					else
					{
						$TotalEntryAddedToW2S++;
						$this->m_dbConn->commit();	
					}
				}
			}	
		}
		
		$AlreadyExitsChequeNos = implode(',',$AlreadyExitsChequeNos);
		return '###'.$TotalEntryAddedToW2S.'###'.$AlreadyExitsChequeNos.'###'.$TotalEntryNotAdded;
	}
	
	private function ProcessPresentInW2sEntries($Data)
	{
		$TotalEntryDeleted = 0;
		
		for($i = 0 ; $i < count($Data); $i++)
		{
			$this->m_dbConn->begin_transaction();
			
			if($Data[$i]['VoucherType'] == VOUCHER_RECEIPT)
			{
				$GetEntriesDetails = $this->m_dbConn->select("Select * from  chequeentrydetails where id = '".$Data[$i]['ChkDetailID']."'"); 
				$PaidByPrev = $GetEntriesDetails[0]['PaidBy'];
				$PayerBankPre = $GetEntriesDetails[0]['PayerBank'];
				$PayerChequeBranchPrev = $GetEntriesDetails[0]['Comments'];
				$ChequeDetailsId = $Data[$i]['ChkDetailID'];
				$DepositID = $GetEntriesDetails[0]['DepositID'];	
				$Status = $this->obj_ChequeDetails->DeletePreviousRecord($PaidBy, $PayerBank, $PayerChequeBranch,$ChequeDetailsId);
				if($Status == "Update")	
				{
					$TotalEntryDeleted++;
					$this->m_dbConn->commit();
				}
				else
				{
					$this->m_dbConn->rollback();
				}			
			
			}
			else if($Data[$i]['VoucherType'] == VOUCHER_PAYMENT || $Data[$i]['VoucherType'] == VOUCHER_CONTRA)
			{
				$GetEntriesDetails = $this->m_dbConn->select("Select * from  paymentdetails where id = '".$Data[$i]['ChkDetailID']."'"); //Fetching Exiting data
				$PaidToPre = $GetEntriesDetails[0]['PaidTo'];
				$ChequeNumberPre = $GetEntriesDetails[0]['ChequeNumber'];
				$ChequeDatePre = $GetEntriesDetails[0]['ChequeDate'];
				$AmountPre = $GetEntriesDetails[0]['Amount'];
				$PayerBankPre = $GetEntriesDetails[0]['PayerBank'];
				$CommentsPre = $GetEntriesDetails[0]['Comments'];
				$VoucherDatePre = $GetEntriesDetails[0]['VoucherDate'];
				$InvoiceDatePre = $GetEntriesDetails[0]['InvoiceDate'];
				$TDSAmountPre = $GetEntriesDetails[0]['TDSAmount'];
				$LeafID = $GetEntriesDetails[0]['ChqLeafID'];
				$DoubleEntry = $GetEntriesDetails[0]['IsMultipleEntry'];
				$ExpenseByPre = $GetEntriesDetails[0]['ExpenseBy'];
				$RowID = $Data[$i]['ChkDetailID'];
				$ModeOfPaymentPre = $GetEntriesDetails[0]['ModeOfPayment'];
				$InvoiceAmountPre = $GetEntriesDetails[0]['InvoiceAmount'];
				
				$reconcileDate = '';
				$reconcileStatus = 0;
				$reconcile = 0;
				$return = 0;
				
				//Storing all current data to store in log 
				$Msg = "\r\nPaidTo | ChequeNumber | ChequeDate | Amount | PayerBank | Comments | VoucherDate | InvoiceDate | TDSAmount | LeafID | DoubleEntry | ExpenseBy | RowID | ModeOfPaymentPre";
				$Msg .= "\r\n".$PaidToPre."|".$ChequeNumberPre."|".$ChequeDatePre."|".$AmountPre."|".$PayerBankPre."|".$CommentsPre."|".$VoucherDatePre."|".$InvoiceDatePre."|".$TDSAmountPre."|".$LeafID."|".$DoubleEntry."|".$ExpenseByPre."|".$RowID."|".$ModeOfPaymentPre."|".$InvoiceAmountPre;
				$Msg .="<br>";
				
				// Calling deletePaymentDetails to delete the exiting entry
				$Status = $this->obj_PaymentDetails->deletePaymentDetails($ChequeDatePre,$ChequeNumberPre,$VoucherDatePre,$AmountPre,$PaidToPre,$ExpenseByPre,$PayerBankPre,$ChqLeafIDPre,$CommentsPre,$InvoiceDatePre,$TDSAmountPre,$RowID, false, 0, 0);
			
				if($Status) // If Exiting data deleted then only this condition will true
				{
					$Msg.="\r\npayment record deleted successfully.";
					$TotalEntryDeleted++;
					
					$this->changeLog->setLog($Msg, $_SESSION['login_id'], "paymentdetails", $RowID);
					$this->m_dbConn->commit();
				}
				else
				{
					$this->m_dbConn->rollback();
				}
			}
		}
		
		return "###".$TotalEntryDeleted;
	}
	
	
	public function getDepositID($LeafAndDepositName,$BankID)
	{
		$CheckWhetherSpilAlreadyCreatedQuery = "SELECT id FROM `depositgroup` where bankid = '".$BankID."' and depositedby = '".$LeafAndDepositName."' and DepositSlipCreatedYearID = '".$_SESSION['default_year']."'";
		$CheckWhetherSpilAlreadyCreatedResult = $this->m_dbConn->select($CheckWhetherSpilAlreadyCreatedQuery);
		$DepositID = 0;
		if(count($CheckWhetherSpilAlreadyCreatedResult) == 0)
		{
			$InsertRecoTransactionDepositID = "INSERT INTO `depositgroup` (`bankid`, `createby`, `depositedby`, `desc`, `DepositSlipCreatedYearID`) VALUES ('".$BankID."', '".$_SESSION['login_id']."', '".$LeafAndDepositName."', '".$LeafAndDepositName."', '".$_SESSION['default_year']."')";
			$DepositID = $this->m_dbConn->insert($InsertRecoTransactionDepositID);
		}
		else
		{
			$DepositID = $CheckWhetherSpilAlreadyCreatedResult[0]['id'];
		}
		return $DepositID;
	}
	
	public function getLeafID($LeafAndDepositName,$BankID)
	{
		$CheckWhetherLeafAlreadyCreatedQuery = "SELECT id FROM `chequeleafbook` where LeafName = '".$LeafAndDepositName."' and BankID = '".$BankID."' and LeafCreatedYearID = '".$_SESSION['default_year']."' and `status` = 'Y'";
		$CheckWhetherLeafAlreadyCreatedResult = $this->m_dbConn->select($CheckWhetherLeafAlreadyCreatedQuery);
		$LeafID = 0;
		if(count($CheckWhetherLeafAlreadyCreatedResult) == 0)
		{
			echo $InsertRecoTransactionLeafID = "INSERT INTO `chequeleafbook` (`LeafName`, `StartCheque`, `EndCheque`, `CustomLeaf`, `BankID`, `Comment`, `LeafCreatedYearID`, `IsReturnChequeLeaf`) VALUES ('".$LeafAndDepositName."', '0', '0', '1', '".$BankID."', '".$LeafAndDepositName."', '".$_SESSION['default_year']."', '0')";
			$LeafID = $this->m_dbConn->insert($InsertRecoTransactionLeafID);
		}
		else
		{
			$LeafID = $CheckWhetherLeafAlreadyCreatedResult[0]['id'];
		}
		return $LeafID;
	}
	
	public function getVoucherName($VoucherType)
	{
		
		if($VoucherType == VOUCHER_RECEIPT)
		{
			$VoucherName = "Receipt Voucher";
		}
		else if($VoucherType == VOUCHER_PAYMENT)
		{
			$VoucherName = "Payment Voucher";
		}
		else if($VoucherType == VOUCHER_CONTRA)
		{
			$VoucherName = "Contra Voucher";
		}
		else
		{
			$VoucherName = '-';
		}
		
		return $VoucherName;
		
	}
	
	function getReconcileDetails($Statement_id)
	{
		$Bank_Regsiter_Details_Query = "SELECT * FROM bankregister WHERE statement_id = '".$Statement_id."'";
		$Bank_Regsiter_Details_Result = $this->m_dbConn->select($Bank_Regsiter_Details_Query);
		$Result = array();
		for($i = 0; $i < count($Bank_Regsiter_Details_Result); $i++)
		{
			$Amount = 0;
			$bankID = $Bank_Regsiter_Details_Result[$i]['LedgerID'];
			$chkDetailID = $Bank_Regsiter_Details_Result[$i]['ChkDetailID'];
			if($Bank_Regsiter_Details_Result[$i]['PaidAmount'] > 0)
			{
				$LeafID = $Bank_Regsiter_Details_Result[$i]['DepositGrp'];
				$Amount = $Bank_Regsiter_Details_Result[$i]['PaidAmount'];
				//$Check_Entry_Details = $this->m_dbConn->select($Check_Entry_Details_Query);

				$Leaf_Details_Query = "SELECT payment.PaidTo, chk_leaf.CustomLeaf FROM `paymentdetails` as payment JOIN `chequeleafbook` as chk_leaf ON payment.ChqLeafID = chk_leaf.id WHERE payment.id = '".$chkDetailID."'";
				$Payment_Details = $this->m_dbConn->select($Leaf_Details_Query);
				
				$LedgerName = '-';
				$LedgerName = $this->obj_utility->getLedgerName($Payment_Details[0]['PaidTo']);
				
				if($chequeDetails[0]['ChqLeafID'] == -1)
				{
					$Url = "PaymentDetails.php?bankid=".$bankID."&LeafID=".$LeafID."&edt=".$chkDetailID;																	
				}
				else
				{
					$Url = "PaymentDetails.php?bankid=".$bankID."&LeafID=".$LeafID."&CustomLeaf= ". $Payment_Details[0]['CustomLeaf']. "&edt=".$chkDetailID;																	
				}
			}
			else if($Bank_Regsiter_Details_Result[$i]['ReceivedAmount'] > 0)
			{
				$Amount = $Bank_Regsiter_Details_Result[$i]['ReceivedAmount'];
				$depositID = $Bank_Regsiter_Details_Result[$i]['DepositGrp'];
				
				$Check_Entry_Details_Query = "SELECT PaidBy FROM `chequeentrydetails` WHERE ID = '".$chkDetailID."'";
				$Check_Entry_Details = $this->m_dbConn->select($Check_Entry_Details_Query);
				
				$LedgerName = '-';
				$LedgerName = $this->obj_utility->getLedgerName($Check_Entry_Details[0]['PaidBy']);
				
				if($depositID > 0)
				{
					$Url = "ChequeDetails.php?depositid=".$depositID."&bankid=".$bankID."&edt=".$chkDetailID;	
				}
				else if($depositID == DEPOSIT_NEFT || $depositID == DEPOSIT_ONLINE)
				{
					$Url = "NeftDetails.php?bankid=".$bankID."&edt=".$chkDetailID;	
				}
				else if($depositID == DEPOSIT_CASH)
				{
					$Url = "ChequeDetails.php?depositid=".$depositID."&bankid=".$bankID."&edt=".$chkDetailID;	
				}
			}
			
			
			$Result[$i]['LedgerName'] = $LedgerName;
			$Result[$i]['Amount'] = $Amount;
			$Result[$i]['Url'] = $Url;
		}
		return $Result;
	}
	
	function ImportActualBankStmt($PresentInBank)
	{
		
		date_default_timezone_set('Asia/Kolkata');		
		$this->errorfile_name = 'BankStatement_import_errorlog_'.date("d.m.Y").'_'.rand().'.html';
		$this->errorLog = $this->errorfile_name;
	
		$errorfile = fopen($this->errorfile_name, "a");
		$errormsg="[Importing Bank Statement]";
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		$errormsg = "";	
		$msg = "";	
		
		$checkExist = "select `ChequeNo`,DATE_FORMAT(Date,'%d-%m-%Y') AS Date,`Debit`,`Credit` from `actualbankstmt` where `BankID` = '".$PresentInBank[0]['BankID']."'"; 
		$ResultMatch=$this->m_dbConn->select($checkExist);
	
		$ChequeNo_Array = array_column($ResultMatch,'ChequeNo');
		$Date_Array = array_column($ResultMatch,'Date');
		//$DebitAmount_Array = array_column($ResultMatch,'Debit');
		//$CreditAmount_Array = array_column($ResultMatch,'Credit');
		$rowCount = 0;
		$SuccessCount = 0;
	 	for($i = 0;$i< sizeof($PresentInBank) ; $i++)
	 	{
			$errormsg = "";
			$successmsg = "";
			$rowCount ++;
//			if(!in_array($PresentInBank[$i]['ChequeNumber'],$ChequeNo_Array) && !empty(trim($PresentInBank[$i]['ChequeNumber'])))
			{
		 		$InsertActualStament ="insert into `actualbankstmt`(`BankID`,`Date`,`ChequeNo`,`Bank_Description`,`Debit`,`Credit`, `Bank_Balance`, `Notes`, `Added_By`) VALUES ('".$PresentInBank[$i]['BankID']."', '".getDBFormatDate($PresentInBank[$i]['Date'])."','".$PresentInBank[$i]['ChequeNumber']."','".$PresentInBank[$i]['Bank_Description']."','".$PresentInBank[$i]['Debit']."','".$PresentInBank[$i]['Credit']."', '".$PresentInBank[$i]['Bank_Balance']."', '".$PresentInBank[$i]['Notes']."', '".$_SESSION['login_id']."')";
				$Result1=$this->m_dbConn->Insert($InsertActualStament);	
				$errormsg = "";
				$successmsg = "";
				$SuccessCount++;
				$successmsg .= "Date : &lt;".$PresentInBank[$i]['Date']."&gt; Cheque Number &lt;".$PresentInBank[$i]['ChequeNumber']."&gt; Debit Amount &lt; ".$PresentInBank[$i]['Debit']."&gt; Credit Amount &lt;".$PresentInBank[$i]['Credit']. "&gt; Balance Amount &lt;".$PresentInBank[$i]['Bank_Balance']. "&gt;  Bank Description &lt;".$PresentInBank[$i]['Bank_Description']." &gt; Notes &lt;".$PresentInBank[$i]['Notes']. "&gt; Data Imported successfully <br>";
				$this->obj_utility->logGenerator($errorfile,$rowCount,$successmsg,"I");	
			}
/*	
			else if(!in_array($PresentInBank[$i]['Date'],$Date_Array) && empty(trim($PresentInBank[$i]['ChequeNumber'])))
			{
			 	$InsertActualStament ="insert into `actualbankstmt`(`BankID`,`Date`,`ChequeNo`,`Bank_Description`,`Debit`,`Credit`, `Bank_Balance`, `Notes`, `Added_By`) VALUES ('".$PresentInBank[$i]['BankID']."','".getDBFormatDate($PresentInBank[$i]['Date'])."','".$PresentInBank[$i]['ChequeNumber']."','".$PresentInBank[$i]['Bank_Description']."','".$PresentInBank[$i]['Debit']."','".$PresentInBank[$i]['Credit']."', '".$PresentInBank[$i]['Bank_Balance']."', '".$PresentInBank[$i]['Notes']."', '".$_SESSION['login_id']."')";
				$Result1=$this->m_dbConn->Insert($InsertActualStament);	
				$errormsg = "";
				$successmsg = "";
				$SuccessCount++;
				$successmsg .= "Date : &lt;".$PresentInBank[$i]['Date']."&gt;  Cheque Number &lt;".$PresentInBank[$i]['ChequeNumber']."&gt; Debit Amount &lt; ".$PresentInBank[$i]['Debit']."&gt;  Credit Amount &lt;".$PresentInBank[$i]['Credit']. "&gt; Balance Amount &lt;".$PresentInBank[$i]['Bank_Balance']. "&gt;  Bank Description &lt;".$PresentInBank[$i]['Bank_Description']." &gt; Notes &lt;".$PresentInBank[$i]['Notes']. "&gt;  Data Imported successfully <br>";
				$this->obj_utility->logGenerator($errorfile,$rowCount,$successmsg,"I");	
			}
			else
			{
				$errormsg .= "Date : &lt;".$PresentInBank[$i]['Date']."&gt; or Cheque Number &lt;".$PresentInBank[$i]['ChequeNumber']."&gt; Debit Amaunt &lt; ".$PresentInBank[$i]['Debit']."&gt; or Credit &lt;".$PresentInBank[$i]['Credit']."&gt;  Balance Amount &lt;".$PresentInBank[$i]['Bank_Balance']. "&gt;  Bank Description &lt;".$PresentInBank[$i]['Bank_Description']." &gt; Notes &lt;".$PresentInBank[$i]['Notes']. "&gt; Data Already Exist Not Imported <br>";
			$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");	
			}
*/	
	
	 	}
		
		
		$errormsg="[End Importing Bank Statement]";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
		
		$errormsg = "<br><br><br><b>Total number of row is  ".$rowCount."</b>";
	    $errormsg .= "<br><b>Total number of row is imported  ".$SuccessCount."</b>";
	    $errormsg .= "<br><b>Total Number of row is not imported  ".($rowCount - $SuccessCount)."</b>";
		$this->obj_utility->logGenerator($errorfile,'',$errormsg);
		
		
	 	$insertLog ="Insert into `change_log` (`ChangedLogDec`,`ChangedBy`,`ChangedTable`,`ChangedKey`) VALUES ('Import ActualBankStmt','".$_SESSION['login_id']."','actualbankstmt','--')";
	 	$Result2=$this->m_dbConn->Insert($insertLog);
	 }
}



?>
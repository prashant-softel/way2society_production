<?php
include_once("include/dbop.class.php");
include_once("utility.class.php");
include_once("dbconst.class.php");
include_once("changelog.class.php");//Pending - Verify
include_once("genbill.class.php");
include_once("include/fetch_data.php");

class creditnote_import 
{
	public $m_dbConn;
	public $obj_utility;
	public $errorfile_name;
	public $errorLog;
	public $actionPage = '../import_invoice.php?Note';
	public $changeLog;
	public $obj_genbill;
	public $obj_fetch;
	private $billNoArray = array();

	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->obj_utility = new utility($this->m_dbConn);
		$this->m_objLog = new changeLog($this->m_dbConn);
		$this->obj_genbill = new genbill($this->m_dbConn);

		$this->obj_fetch = new FetchData($this->m_dbConn);

		$a = $this->obj_fetch->GetSocietyDetails($_SESSION['society_id']);

	}
	

	public function UploadData($fileName,$fileData, $NoteType)
	{
		$Foldername = $this->obj_fetch->objSocietyDetails->sSocietyCode;

		if (!file_exists('../logs/import_log/'.$Foldername)) 
		{
			mkdir('../logs/import_log/'.$Foldername, 0777, true);
		}
		
		if($NoteType == CREDIT_NOTE)
		{
			$this->errorfile_name = '../logs/import_log/'.$Foldername.'/import_creditnote_errorlog_'.date("d.m.Y").'_'.rand().'.html';	
			$errormsg="[Importing Credit Note Data]";
			$this->actionPage = '../import_invoice.php?Note='.CREDIT_NOTE;
		}
		else if($NoteType == DEBIT_NOTE)
		{
			$this->errorfile_name = '../logs/import_log/'.$Foldername.'/import_debitnote_errorlog_'.date("d.m.Y").'_'.rand().'.html';
			$errormsg="[Importing Debit Note Data]";
			$this->actionPage = '../import_invoice.php?Note='.DEBIT_NOTE;
		}
		
		$this->errorLog = $this->errorfile_name;
		$errorfile = fopen($this->errorfile_name, "a");
		
		$isImportSuccess = false;
		$Success = 0;
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		
		$CreditBillNoExitsSql="Select Note_No from credit_debit_note";
		$CreditBillNoExitsResult = $this->m_dbConn->select($CreditBillNoExitsSql);
		$this->billNoArray = array_column($CreditBillNoExitsResult, 'Note_No');
		
		foreach($fileData as $row)
			{
				if($row[0] || $row[1] <> '')
				{
						$rowCount++;
						if($rowCount == 1)//Header
						{
							$ledger_id = array();
							$UnitNoIndex = array_search(FCode,$row,true);
							$CreditDateIndex = array_search(Date,$row,true);
							$CreditBillNoIndex=array_search(Bill_No,$row,true);
							$CreditBillTypeIndex=array_search(Bill_Type,$row,true);
							$IsTaxableIndex=array_search(Is_Taxable,$row,true);
							$j = 0;
							$ledger_no=0;
							$cnt = 0;
							//LedgerNames - Dynamic
							for($i=5;$i<sizeof($row)-3;$i++)
							{
								$ledger[$j] = $row[$i];
								$ledger_no[$j]=array_search($ledger[$j],$row,true);
								$query_id="Select id,ledger_name from ledger where ledger_name='".$ledger[$j]."'";
								//pending optimization
								$LedgerResult=$this->m_dbConn->select($query_id);
								$ledger_id[$cnt] = $LedgerResult[0]['id'];
								//echo "Ledger ID".$ledger_id[$cnt];
								$cnt++;
								$j++;
							}
							
							$CGSTIndex=array_search(CGST,$row,true);
							$SGSTIndex=array_search(SGST,$row,true);
							$NoteIndex = array_search(Note,$row,true);
							
							if(!isset($UnitNoIndex) || !isset($CreditDateIndex) || !isset($CreditBillNoIndex) || !isset($NoteIndex)  ||!isset($ledger) || !isset($CreditBillTypeIndex))
							{
								$result = '<p>Required Column Names Not Found. Cant Proceed Further......</p>';
								$errormsg=" Column names does not match";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								return $result;
								
							}
						}
					 	else
					   	{   
							$errormsg = '';
							//Checking if Unit No Exists
							$Unit_No = $row[$UnitNoIndex];
							if($Unit_No == '')
							{
								$errormsg .= "<br>Unit No Not Provided "."For Row".$rowCount;
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");	
							}
							//Set Unit ID according to Unit No
							else
							{
								$UnitID = $this->obj_utility->GetUnitID($Unit_No);
								//var_dump($UnitID);
								if($UnitID == 0 || $UnitID == '')
								{
									$errormsg .= "<br>Unit No. &lt; " . $Unit_No . " &gt; Not Exits";
								}
							}
							
							if(!empty($UnitID))
							{
							//Setting and Validating Invoice No
							$CreditBillNo=$row[$CreditBillNoIndex];
							if($CreditBillNo == 0 || $CreditBillNo == '')
							{
								$errormsg .= "<br>Bill Number Not provided";
							}
							else
							{	
								if(in_array($CreditBillNo,$this->billNoArray))
								{
									
									$warningmsg = $CreditBillNo." Bill Number Exists";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$warningmsg,"W");
								}
							
							
							$CreditDate = $row[$CreditDateIndex];
							
							if($CreditDate=='')
							{
								$errormsg .= "<br> Date Not Provided";
							}
							else
							{
								$Valid = $this->obj_utility->check_in_range($_SESSION['default_year_start_date'], $_SESSION['default_year_end_date'],getDBFormatDate($CreditDate));
								
								if($Valid)
								{
									$CreditDate = $CreditDate;
									
								}
								else
								{
									$errormsg .= "<br>INVALID: Date Not In Range ".$CreditDate."Start Date :".getDisplayFormatDate($_SESSION['default_year_start_date'])." End Date:".getDisplayFormatDate($_SESSION['default_year_end_date']);
								}	
							}
							
							$CreditBillType = $row[$CreditBillTypeIndex];
							if($CreditBillType <> Maintenance && $CreditBillType <> Supplementry)
							{
								$errormsg .= " <br>Bill Type Does Not Exits";
							}
							
							$Note=$row[$NoteIndex];
							$CreditNote_CGST=$row[$CGSTIndex];
							$CreditNote_SGST=$row[$SGSTIndex];
							
							
							
							if(isset($CreditNote_CGST) && isset($CreditNote_SGST))
							{
								//$ValidSGST=$this->obj_utility->isNumeric($CreditNote_SGST);
								//$ValidCGST=$this->obj_utility->isNumeric($CreditNote_CGST);
								//if($ValidCGST)
								{
									$CGST = $CreditNote_CGST;
								}
								//if($ValidSGST)
								{
									$SGST = $CreditNote_CGST;
								}
							}
							
							if((empty($CreditNote_CGST) && !empty($CreditNote_SGST)) || (!empty($CreditNote_CGST) && empty($CreditNote_SGST)))
							{
								$errormsg .= "<br>CGST and SGST both must have to be set OR NONE";
							}
						}
					}
							$ledger_amt = array();
							$j = 0;
							//Storing Ledger Amount  
							for($i=5;$i<sizeof($row)-3;$i++)
							{
								$ledger_amt[$j] = $row[$i];
								$j++;
							}
							
							$ledgerTotal=0;			
							$associativeArray = array();
							$Counter = 0;
							//Saving in associative array 
							
							for($i = 0 ; $i < count($ledger_id); $i++)
							{
								if(!empty($ledger_id[$i]))
								{
									$associativeArray[$Counter]['Head'] = $ledger_id[$i];
									$associativeArray[$Counter]['Amt'] = $ledger_amt[$i];
									$ledgerTotal = $ledgerTotal + $ledger_amt[$i];
									$Counter++;
								}
								else
								{
									$errormsg .= "<br>Ledger Name ".$ledger[$i]." Not Exits For Row".$rowCount;
								}
									
							}
							
							if($ledgerTotal == 0)
							{
								$errormsg .= "<br>Amount Not Found For Unit ".$Unit_No;
							}
							$FinalValue = array();
							// Associative Array of Ledger Total, CGST , SGST and Total
							$FinalValue['SubTotal'] = $ledgerTotal;
							$FinalValue['CGST'] = $CGST;
							$FinalValue['SGST'] = $SGST;
							$finalTotal=$ledgerTotal+$CGST+$SGST;
							$RoundOffAmtForfinalTotal = $this->obj_utility->getRoundValue2($finalTotal);
							$RoundOffAmt = $RoundOffAmtForfinalTotal - $finalTotal;
							$FinalValue['RoundOffAmt'] = $RoundOffAmt;
							$FinalValue['Total']=$RoundOffAmtForfinalTotal;
							$Taxable=$row[$IsTaxableIndex];
							$isTaxable= false;
							if($Taxable == "Yes")
							{
								$isTaxable= true;
							}
							else
							{
								$isTaxable= false;
							}
							
							if(!empty($errormsg))
							{
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								continue;
							}
							
							if((isset($UnitID) && isset($CreditDate) && isset($CreditBillNo) && isset($FinalValue)) || (isset($CGST) && isset($SGST)))
							{	
								
								$GUID = "";
								$Result = $this->obj_genbill->AddCreditDebitNoteWithImport($associativeArray,$UnitID,$CreditDate,$CreditBillType,$NoteType,$Note,false,0,1,$CreditBillNo,false,$GUID,true,$FinalValue,$isTaxable);
								
								if($Result == 1)
								{   
									$Success++;
									if($NoteType == CREDIT_NOTE)
									{
										$errormsg ="CreditNote Data Imported : Unit No : &lt; ".$Unit_No." &gt; Date : &lt; ".$CreditDate." &gt; Credit No : &lt; ".$CreditBillNo." &gt;<br>";	
									}
									else if($NoteType == DEBIT_NOTE)
									{
										$errormsg ="DebitNote Data Imported : Unit No : &lt; ".$Unit_No." &gt; Date : &lt; ".$CreditDate." &gt; Debit No : &lt; ".$CreditBillNo." &gt; <br>";										
									}
									
									$erormsg=0;
									for($i = 0 ; $i < count($ledger_id); $i++)
									{
										if($ledger_amt[$i] <> '' && $ledger_amt[$i] <> 0)
										{
											$errormsg .= $ledger[$i];
											$errormsg .= " : ";
											$errormsg .= $ledger_amt[$i];
											$errormsg .= " &nbsp;&nbsp;&nbsp; ";
										}
									}
									$errormsg =  $errormsg."<br>CGST : ".$CGST." <br> SGST : ".$SGST." <br> Note : ".$Note."";
									
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
								}
								
								
							}
							
						}
					}
				}
				$TotalRow = $rowCount - 1;
				$Failed = $TotalRow -  $Success;
				$errormsg = "<br>Total Number of Rows :".$TotalRow;
				$errormsg .= "<br>Number of Rows Successfully Imported :".$Success;
				$errormsg .= "<br>Number of Rows Not Imported : ".$Failed;
				$this->obj_utility->logGenerator($errorfile,'',$errormsg,"I");
							
			}//function upload data

//Validation Pending-
//Move to utility
	
	public function isNumeric($Numeric)
	{
		$bResult = true;
		 if (!preg_match('/^[0-9]*$/', $Numeric))
		
		{
			$bResult = false;
		}
		return $bResult;
	}
	
	public function validateDate($InvoiceDate)
	{
	if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$InvoiceDate))
	{
    return true;
	}
	else 
	{
    return false;
	}
	}
}//class
						?>
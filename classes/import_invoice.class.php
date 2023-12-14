<?php
include_once("include/dbop.class.php");
include_once("utility.class.php");
include_once("dbconst.class.php");
include_once("changelog.class.php");//Pending - Verify
include_once("genbill.class.php");
include_once("include/fetch_data.php");

class invoice_import 
{
	public $m_dbConn;
	public $obj_utility;
	public $errorfile_name;
	public $errorLog;
	public $actionPage = '../import_invoice.php';
	public $changeLog;
	public $obj_genbill;
	public $obj_fetch;
	private $InvoiceNumberArray = array();

	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->obj_utility = new utility($this->m_dbConn);
		$this->m_objLog = new changeLog($this->m_dbConn);
		$this->obj_genbill = new genbill($this->m_dbConn);

		$this->obj_fetch = new FetchData($this->m_dbConn);

		$a = $this->obj_fetch->GetSocietyDetails($_SESSION['society_id']);

	}
	

	public function UploadData($fileName,$fileData)
	{
		$Foldername = $this->obj_fetch->objSocietyDetails->sSocietyCode;

		if (!file_exists('../logs/import_log/'.$Foldername)) 
		{
			mkdir('../logs/import_log/'.$Foldername, 0777, true);
		}

		$this->errorfile_name = '../logs/import_log/'.$Foldername.'/import_invoice_errorlog_'.date("d.m.Y").'_'.rand().'.html';
		$this->errorLog = $this->errorfile_name;
		$errorfile = fopen($this->errorfile_name, "a");
		$errormsg="[Importing Invoice Data]";
		$isImportSuccess = false;
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		
		$invoice_no="Select Inv_Number from sale_invoice";
		$InvoiceResult=$this->m_dbConn->select($invoice_no);
		$this->InvoiceNumberArray = array_column($InvoiceResult, 'Inv_Number');
		$Success = 0; 
		foreach($fileData as $row)
			{
				if($row[0] || $row[1] <> '')
				{
						$rowCount++;
						if($rowCount == 1)//Header
						{
							$ledger_id = array();
							$UnitNoIndex = array_search(FCode,$row,true);
							$InvoiceDateIndex = array_search(InvoiceDate,$row,true);
							$InvoiceNoIndex=array_search(InvoiceNo,$row,true);
							$j = 0;
							$ledger_no=0;
							$cnt = 0;
							//LedgerNames - Dynamic
							for($i=3;$i<sizeof($row)-3;$i++)
							{
								$ledger[$j] = $row[$i];
								$ledger_no[$j]=array_search($ledger[$j],$row,true);
								$query_id="Select id from ledger where ledger_name='".$ledger[$j]."'";
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
							
							if(!isset($UnitNoIndex) || !isset($InvoiceDateIndex) || !isset($InvoiceNoIndex) || !isset($NoteIndex)  ||!isset($ledger) )
							{
								$result = '<p>Required Column Names Not Found. Cant Proceed Further......</p>';
								$errormsg=" Column names does not match";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								return $result;
								
							}
						}
					 	else
					   	{   
							
							$Unit_No = $row[$UnitNoIndex];
							$errormsg = '';
							if($Unit_No == '')
							{
								$errormsg .= "<br>Unit No Not Provided";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");	
							}
							//Set Unit ID according to Unit No
							else
							{
								$UnitID = $this->obj_utility->GetUnitID($Unit_No);
								
								if($UnitID == 0 || $UnitID == '')
								{
									$errormsg .= "<br>Unit No Not Found";
								}
							}
							
							if(!empty($UnitID))
							{
							//Setting and Validating Invoice No
							$Invoice_No = $row[$InvoiceNoIndex];
							echo '<br>Invoice Number'.$Invoice_No;
							if($Invoice_No == 0 || $Invoice_No == '')
							{
								$errormsg .= "<br>Invoice Number Not provided";
							}
							else
							{	
								if(in_array($Invoice_No,$this->InvoiceNumberArray))
								{
									$Warningmsg = "<br>Invoice Number Exists";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$Warningmsg,"W");
								}
							
							
							$Invoice_Date = $row[$InvoiceDateIndex];
							
							if($Invoice_Date== '')
							{
								$errormsg .= "<br>Invoice Date Not Provided";
							}
							else
							{
								$Valid=$this->obj_utility->check_in_range($_SESSION['default_year_start_date'], $_SESSION['default_year_end_date'],getDBFormatDate($Invoice_Date));
								
								if($Valid)
								{
									$InvoiceDate=$Invoice_Date;
									
								}
								else
								{
									$errormsg .= "<br>INVALID: Date Not In Range ".$Invoice_Date."Start Date :".$_SESSION['default_year_start_date']."End Date:".$_SESSION['default_year_end_date'];
								}	
							}
							
							
							$Note=$row[$NoteIndex];
							$Invoice_CGST=$row[$CGSTIndex];
							$Invoice_SGST=$row[$SGSTIndex];
							
							
							
							if(isset($Invoice_CGST) && isset($Invoice_SGST))
							{
								// $ValidSGST=$this->obj_utility->isNumeric($Invoice_SGST);
								// $ValidCGST=$this->obj_utility->isNumeric($Invoice_CGST);
								// if($ValidCGST)
								 {
									$CGST=$Invoice_CGST;
								}
								//if($ValidSGST)
								{
									$SGST=$Invoice_SGST;
									$SGST;
								}
							}
							
							if((empty($Invoice_CGST) && !empty($Invoice_SGST)) || (!empty($Invoice_CGST) && empty($Invoice_SGST)))
							{
								$errormsg .= "<br>Either CGST and SGST have to be imported OR NONE";
							}
						}
					}
							$ledger_amt = array();
							$j = 0;
							//Storing Ledger Amount  
							for($i=3;$i<sizeof($row)-3;$i++)
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
								if($ledger_amt[$i] <> '' && $ledger_amt[$i] <> 0)
								{
									$ledgerTotal = $ledgerTotal + $ledger_amt[$i];
									if(!empty($ledger_id[$i]))
									{
										$associativeArray[$Counter]['Head'] = $ledger_id[$i];
										$associativeArray[$Counter]['Amt'] = $ledger_amt[$i];
										$Counter++;
									}
									else
									{
										$errormsg .= "<br>Ledger Name ".$ledger[$i]." Not Exits For Row".$rowCount;
									}
								}
									
							}
							
							if($ledgerTotal == 0)
							{
								$errormsg .= "<br>Amount is not set for any ledger";		
							}
							
							
							$FinalValue = array();
							// Associative Array of Ledger Total, CGST , SGST and Total
							$FinalValue['Subtotal'] = $ledgerTotal;
							$FinalValue['CGST'] = $CGST;
							$FinalValue['SGST'] = $SGST;
							$finalTotal=$ledgerTotal+$CGST+$SGST;
							$RoundOffAmtForfinalTotal = $this->obj_utility->getRoundValue2($finalTotal);
							$RoundOffAmt = $RoundOffAmtForfinalTotal - $finalTotal;
							$FinalValue['RoundOffAmt'] = $RoundOffAmt;
							$FinalValue['Total'] = $RoundOffAmtForfinalTotal;
							
							if(!empty($errormsg))
							{
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								continue;
							}
							
							
							if((isset($UnitID) && isset($InvoiceDate) && isset($Invoice_No) && isset($FinalValue)) || (isset($CGST) && isset($SGST)))
							{	
								$Result = $this->obj_genbill->SetSalesInvoiceVoucher_WithImport($UnitID,$InvoiceDate,$associativeArray,$Note,$Invoice_No,0,true,$FinalValue,false);
								
								if($Result == 1)//Successful
								{   //Value In Errormsg
								
									$Success++;
									
									$errormsg ="Invoice Data Imported : Unit ID : &lt;".$UnitID." &gt; Date : &lt;".$InvoiceDate." &gt; Invoice No : &lt;".$Invoice_No." &gt; <br>";
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
									$errormsg =  $errormsg."<br>CGST : '".$CGST."' <br> SGST : '".$SGST."' <br> Note : '".$Note."'";
									
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
								}
							}
						}
					}
				}
				$totalCount = $rowCount - 1;
				$Failed = $totalCount - $Success;
				$errormsg = "<br>Total Number of Rows : ".$totalCount;
				$errormsg .= "<br>Number of Rows Successfully Imported :".$Success;
				$errormsg .= "<br>Number of Rows Not Imported : ".$Failed;
				$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
			}
	
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
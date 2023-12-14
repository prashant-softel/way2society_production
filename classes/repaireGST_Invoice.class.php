<?php if(!isset($_SESSION)){session_start(); }
include_once("include/display_table.class.php");
include_once("dbconst.class.php");
include_once("voucher.class.php");
include_once("register.class.php");
include_once("utility.class.php");
include_once("genbill.class.php");
include_once("createvoucher.class.php");
include_once("PaymentDetails.class.php");

class updateInterest extends dbop
{
	public $actionPage = "../updateInterest.php";
	public $m_dbConn;
	public $m_objUtility;
	public $m_objGenbill;
	public $m_objCreateVoucher;
	public $m_objPaymentDetails;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->m_objUtility = new utility($this->m_dbConn);
		$this->m_objGenbill = new genbill($this->m_dbConn);
		$this->m_objCreateVoucher = new createVoucher($this->m_dbConn);
		$this->m_objPaymentDetails = new PaymentDetails($this->m_dbConn);
	}
	

public function RepairVouchers($Validate = 1)
{
 if($_SESSION['cgst_input'] > 0 && $_SESSION['sgst_input'] > 0)
 {
	$bUpdatedb = 1;
	if($Validate == 1)
	{
		$bUpdatedb = 0;
		echo "<BR><BR>Validating the GST voucher repair...<BR>";
	}
	else
	{
		echo "<BR><BR>Repairing GST vouchers...<BR>";
	}
	$NewVoucherElements = 0;
	try
	{
		$this->m_dbConn->begin_transaction();	

		 $SelectInvoice = "select InvoiceRaisedVoucherNo,AmountReceivable,CGST_Amount,SGST_Amount from `invoicestatus` where CGST_Amount NOT IN(0) and SGST_Amount NOT IN (0)";
		$data = $this->m_dbConn->select($SelectInvoice);
		//print_r($data);
		$ExpenceBy = 0;
		$PaidTo = 0;
		$invoiceDate = 0;
		$VoucherType =  5;
		$RefID = 0;
		$RefTableID = 0;
		$VoucherCount = 0;
		$ProcessedVouchers = 0;
		for($i=0; $i<sizeof($data);$i++)
		{
			$VoucherCount++;
			 $VoucherNo = $data[$i]['InvoiceRaisedVoucherNo']; 
			 echo "<BR><BR>VOUCHER NO :".$VoucherNo;
			 $Amount =$data[$i]['AmountReceivable']; 
			 echo "<BR>AMOUNT :".$Amount;
			 $CGSTAmount = $data[$i]['CGST_Amount'];
			 echo "<BR>CGST AMOUNT :".$CGSTAmount;
			 $SGSTAmount = $data[$i]['SGST_Amount'];
			 echo "<BR>SGST AMOUNT :".$SGSTAmount;
			
			$SelectVoucher="SELECT `id`,`Date`,`By`,`To`,`Note`, debit, credit, `VoucherNo`, VoucherTypeID, RefNo, RefTableID FROM `voucher` where VoucherNO='".$VoucherNo."'";
			$data1 = $this->m_dbConn->select($SelectVoucher);
			 echo "<pre>";
			 print_r($data1);
			 echo "</pre>";

			$VoucherRecordSize = sizeof($data1);
			echo "<br>Count : " . $VoucherRecordSize;
			if($VoucherRecordSize == 2)		//if voucher has two records, means its not converted yet
			{
				$UpdateQuery="delete  from `voucher` where VoucherNo='".$VoucherNo."'";
				echo "<BR>Deleting VoucherNo: " . $VoucherNo;
				if($bUpdatedb == 1)
				{
					$results2=$this->m_dbConn->delete($UpdateQuery);
				}
				for($icount = 0; $icount <= $VoucherRecordSize; $icount++)
				{
					if($icount == 0)
					{
						//delete entries from registers
						if($data1[$icount]['By'] > 0)
						{
							$ExpenceBy	=  $data1[$icount]['By'];
						}
						$invoiceDate = getDisplayFormatDate($data1[$icount]['Date']);
						$Comments = $data1[$icount]['Note'];
						$VoucherType =  $data1[$icount]['VoucherTypeID'];
						$RefID = $data1[$icount]['RefNo'];
						$RefTableID = $data1[$icount]['RefTableID'];						
					}
					else
					{  
						if($data1[$icount]['To'] > 0)
						{
							$PaidTo	=	$data1[$icount]['To'];
						}
					}
					$VoucherID= $data1[$icount]['id'];	
					
				//$deleteVoucher = $this->m_objCreateVoucher->DeletedRecordEX($VoucherNo);
				
				
					if($VoucherID > 0)
					{
						echo "<BR>Deleting Registers for '".$VoucherID."'";
						if($bUpdatedb == 1)
						{
							$UpdateQuery1="delete  from `liabilityregister` where VoucherID='".$VoucherID."'";
							$results3=$this->m_dbConn->delete($UpdateQuery1);
							//echo "<BR>";
							$UpdateQuery2="delete  from `expenseregister` where VoucherID='".$VoucherID."'";
							//echo "<BR>";
							$results3=$this->m_dbConn->delete($UpdateQuery2);
							$UpdateQuery3="delete  from `incomeregister` where VoucherID='".$VoucherID."'";
							//echo "<BR>";
							$results3=$this->m_dbConn->delete($UpdateQuery3);
							$UpdateQuery4="delete from `assetregister` where VoucherID='".$VoucherID."'";
							//echo "<BR>";
							$results3=$this->m_dbConn->delete($UpdateQuery4);
						}
					}
				}
				
				echo "<BR>Creating VoucherNo='".$VoucherNo."'   ExpenseBy='".$ExpenceBy."'   PaidTo='".$PaidTo."'  invoiceDate='".$invoiceDate."'  Amount='".$Amount."'   CGSTAmount='".$CGSTAmount."'   SGSTAmount='".$SGSTAmount."'  Comments='".$Comments."'";
				echo " VoucherType :".$VoucherType . " RefID :". $RefID . " RefTableID :".$RefTableID;
			
				if($bUpdatedb == 1)
				{
					echo $NewVoucherElements = $this->m_objPaymentDetails->CreatePaymentToJVDetailsEx($ExpenceBy, $PaidTo, $invoiceDate, $Amount, $CGSTAmount,$SGSTAmount,$VoucherType, $Comments, $VoucherNo, $RefID, $RefTableID);
					if ($NewVoucherElements > 2)
					{

						$ProcessedVouchers++;
					}
				}
				echo "<BR>Voucher updated. : " . $NewVoucherElements . " records";
			}
			else
			{
			
				echo "<BR>Voucher not updated. Has " . $VoucherRecordSize . " reccords.<BR>";	
			}
			$data1 = $this->m_dbConn->select($SelectVoucher);
			echo "<pre>";
			print_r($data1);
			echo "</pre>";
			echo "<BR>---------------------------------------------------------------------------<BR>";
		}
//		$this->m_objLog->setLog($LogMsg, $_SESSION['login_id'], TABLE_FD_MASTER,$data[0]['id']);
		$this->m_dbConn->commit();
		echo "<BR>Conversion completed. Processed " . $ProcessedVouchers . " out of " . $VoucherCount;
		return 'Success';
	}
	catch(Exception $e)
	{
		$this->m_dbConn->rollback();
		echo "<BR>Exception:".$e->getMessage()."Line No:".$e->getLine();
		return 'Failed';
	}	
 }
 else
 {
	echo "<center><table style='width:80%; color:red;'><tr><td><br><br></td></tr><tr><td align='center' style='font-size: 20px;
    font-weight: bold;'>Could not start repair GST ! </td></tr>
	<tr><td align='center'><a href='defaults.php'>Click here</a> to set ledger for Input (CGST and SGST).</td></tr></table></center>";	 
 }
}
	
	/*public function startProcess()
	{	
		try
		{
			//echo "Before RepairVouchers";
			//$this->RepairVouchers(0);
			die;
			
			
			$this->m_dbConn->begin_transaction();
				
			for($i = 0; $i < $_POST['Count']; $i++)
			{		
				$sqlQuery = 'SELECT `UnitID`, `BillSubTotal`, `AdjustmentCredit`,`BillTax`, `IGST`,`CGST`,`SGST`,`CESS`,`BillInterest`, `CurrentBillAmount`, `PrincipalArrears`, `InterestArrears`, `TotalBillPayable`,`PeriodID`,`BillType` FROM `billdetails` WHERE `ID` = '.$_POST['billDetailsID'.$i];

				
				$billDetails = $this->m_dbConn->select($sqlQuery);
				if($_POST['Interest'.$i] <> $billDetails[0]['BillInterest'])
				{	
					//echo "inside";
					$CurrentBillAmount = $billDetails[0]['BillSubTotal'] + $billDetails[0]['AdjustmentCredit'] + $billDetails[0]['BillTax'] + $_POST['Interest'.$i];
					$UnitID = $billDetails[0]['UnitID'];
					$PeriodID = $billDetails[0]['PeriodID'];
					$InterestArrears =  $billDetails[0]['InterestArrears'];
					$PrincipalArrears = $billDetails[0]['PrincipalArrears'];
					$AdjustmentCredit = $billDetails[0]['AdjustmentCredit'];
					$BillType = $_POST["bill_type"];
					//echo "BillType.".$BillType;
					$CurrInterestAmount = $_POST['Interest'.$i];
					//echo "UnitiD:".$UnitID . " PeriodID:". $PeriodID ." BillType:" .$BillType;
					
					$BillRegisterData = $this->m_objGenbill->objFetchData->GetValuesFromBillRegister($UnitID, $PeriodID, $BillType);
					//print_r($BillRegisterData);
					$data = array();
					for($iVal = 0; $iVal < sizeof($BillRegisterData) ; $iVal++) 
					{
						$BillDetails = $BillRegisterData[$iVal]["value"];
						$LedgerID = $BillDetails->sHeader;
						$LedgerAmount = $BillDetails->sHeaderAmount;
						$LedgerVoucherID = $BillDetails->sVoucherID;
						$TaxableFlag = $BillDetails->Taxable;
						if(($LedgerID <> INTEREST_ON_PRINCIPLE_DUE) && ($LedgerID <>  SERVICE_TAX) && ($LedgerID <> IGST_SERVICE_TAX) &&  ($LedgerID <> CGST_SERVICE_TAX) && ($LedgerID <> SGST_SERVICE_TAX) &&  ($LedgerID <> CESS_SERVICE_TAX))
						{
							$HeaderAndAmount = array("Head"=>$LedgerID, "Amt"=> $LedgerAmount, "HeadOldValue"=> $LedgerAmount, "VoucherID"=>$LedgerVoucherID, "Taxable" => $TaxableFlag);

						array_push($data, $HeaderAndAmount);
						}
					}
					if(sizeof($data) > 0)
					{
					$this->m_objGenbill->BillDetailsUpdate($data, $UnitID, $PeriodID, $CurrInterestAmount, $InterestArrears, $PrincipalArrears, $AdjustmentCredit, $BillType);
					echo "<br>interest update complete.";
					}
					else
					{
						echo "<br>nothing to update interest.";
					}
				}

			}
			
			$this->m_dbConn->commit();			
			return "Update"; 
		}
		catch(Exception $exp)
		{
			$this->m_dbConn->rollback();
			echo "Exception:". $exp;
			return $exp;
		}
	}*/

	/*function IsGST()
	{		
		return $this->m_objUtility->IsGST();
	}
	
	function getDetails($periodID, $billType = 0)
	{		
		if($periodID == 0)
		{
			$periodQuery = 'SELECT `M_PeriodID` FROM `society` WHERE `society_id` = '.$_SESSION['society_id'];
			$res = $this->m_dbConn->select($periodQuery);
			$periodID = $res[0]['M_PeriodID'];
		}
				
		$sql = "SELECT billdetails.ID, ledger.ledger_name as 'Unit', billdetails.BillSubTotal, billdetails.AdjustmentCredit, billdetails.BillTax, billdetails.BillInterest, billdetails.CurrentBillAmount, billdetails.PrincipalArrears, billdetails.InterestArrears, billdetails.TotalBillPayable, billdetails.Note, billdetails.BillType, billdetails.CGST, billdetails.SGST FROM `billdetails` JOIN `ledger` ON ledger.id = billdetails.UnitID WHERE ledger.society_id = '".$_SESSION['society_id']."' AND billdetails.PeriodID = '".$periodID."' AND billdetails.BillType = '" . $billType . "'";
		//echo $sql;		
		$result = $this->m_dbConn->select($sql);
		//var_dump($result );
		//if(sizeof($result) > 0)
		//{
			$result[0]['M_Period'] = $periodID;
		//}
		return $result;
	}*/
	
	function getPeriod($period, $billtype = 0)
	{
		$sqlPeriod = "Select periodtbl.type, yeartbl.YearDescription from period as periodtbl JOIN year as yeartbl ON periodtbl.YearID = yeartbl.YearID where periodtbl.ID = '" . $period . "'";
		
		$billTypeText = 'Maintenance';
		if($billtype == 1)
		{
			$billTypeText = 'Supplementary';
		}

		$sqlResult = $this->m_dbConn->select($sqlPeriod);
		return "<b><font color='#0000FF'>" . $billTypeText . " Bill's For : " . $sqlResult[0]['type'] . " "  . $sqlResult[0]['YearDescription'] . "</font></b><br><br>";
	}
	
	public function combobox($query,$id)
	{
	$str.="<option value=''>Please Select</option>";
	$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
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
						$str.=$v."</OPTION>";
					}
					$i++;
				}
			}
		}
			return $str;
	}
	
}
?>
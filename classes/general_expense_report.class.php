<?php

include_once('utility.class.php');
include_once("bill_period.class.php");
include_once("dbconst.class.php");

class GeneralExpenseReport
{
	public $m_dbConn;
	public $m_dbConnRoot;
	public $m_bill_period;
	public $m_utility;	
	
	public function __construct($dbConn,$dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_utility = new utility($dbConn,$dbConnRoot);
		$this->m_bill_period = new bill_period($dbConn);
	}
	
	public function fetchExpenseReport($periodType)
	{
		
		$Result = array();
		$PeriodArray = array();
		$Data = array();
		
		$getExpenseLedgerQuery = "SELECT id, ledger_name, category_name, group_id  FROM `ledger` as l JOIN `account_category` as ac ON l.categoryid = ac.category_id where ac.group_id = '".EXPENSE."'";
		$ExpenseLedgerList = $this->m_dbConn->select($getExpenseLedgerQuery);
		
		$year_desc_query = 'SELECT `YearDescription` FROM `year` where `YearID` =' .$_SESSION['default_year'];
		$desc = $this->m_dbConn->select($year_desc_query); 
		
		$months = getMonths($periodType);
		$societyDetails = $this->m_utility->GetSocietyInformation($_SESSION['society_id']);
		
		for( $i = 0; $i < sizeof($months); $i++)
		{
			$isLast = 0;
			if($i == sizeof($months)-1)
			{
				$isLast = 1;	
			}
			$begin_date = $this->m_bill_period->getBeginDate($months[$i],$desc[0]['YearDescription']);
			$end_date = $this->m_bill_period->getEndDate($months[$i],$desc[0]['YearDescription']);
			$PeriodArray[$i]['PeriodName'] = $months[$i];
			$PeriodArray[$i]['startDate'] = $begin_date;
			$PeriodArray[$i]['endDate'] = $end_date;
		}
		
		$PeriodHeadList = array();
		for($i = 0; $i < count($PeriodArray); $i++)
		{
			$startDate = $PeriodArray[$i]['startDate'];
			$endDate = $PeriodArray[$i]['endDate'];	
			$period_Name = $PeriodArray[$i]['PeriodName'];
					
			$registerDetailsQuery = "SELECT sum(Debit) - sum(Credit) as amount, ltable.id, ltable.ledger_name FROM `expenseregister` as ex 
									 LEFT JOIN ledger as ltable ON ltable.id = ex.LedgerID 
									 where LedgerID in(SELECT id FROM `ledger` as l JOIN `account_category` as ac ON l.categoryid = ac.category_id where ac.group_id = '".EXPENSE."') and `Date` between '".$startDate."' and '".$endDate."' group by LedgerID ";
			
			$registerDetails = $this->m_dbConn->select($registerDetailsQuery);
			
			$PeriodHead = $period_Name. '('.$startDate.' - '.$endDate.')';
			array_push($PeriodHeadList,$PeriodHead);
			
			
			for($j =0 ; $j < count($registerDetails); $j++)
			{
				$ledger_name = $registerDetails[$j]['ledger_name'];
				$Result[$PeriodHead][$ledger_name] = $registerDetails[$j]['amount'];	
			}
		}
		
		$Data['ExpenseLedgerList'] = $ExpenseLedgerList;
		$Data['PeriodHead'] = $PeriodHeadList;
		$Data['data'] = $Result;
		$Data['society_name'] = $societyDetails['society_name'];
		$Data['Year'] = $desc[0]['YearDescription'];
		return $Data;
	}
	
	
	public function fetchExpenseReportDetails($ReportType,$startDate,$endDate)
	{
		
		
		$final_arr = array();
		 $sql ="SELECT v.*,p.ChequeDate,p. ChequeNumber,l.ledger_name FROM `voucher` as v left join paymentdetails as p on p.id= v.RefNo Left Join ledger as l on l.id=v.`By` OR l.id=v.`To` Where v.VoucherTypeID=2 and v.`Date` between '".getDBFormatDate($startDate)."' AND '".getDBFormatDate($endDate)."'";
			 //$sql ="SELECT v.*,p.ChequeDate,p. ChequeNumber,l.ledger_name FROM `voucher` as v left join paymentdetails as p on p.id= v.RefNo Left Join ledger as l on l.id=v.`By` OR l.id=v.`To` Where  v.`Date` between '".getDBFormatDate($startDate)."' AND '".getDBFormatDate($endDate)."'";
			$VoucherList = $this->m_dbConn->select($sql);
			for($i= 0 ;$i<sizeof($VoucherList);$i++)
			{
				$ChequeDate	=	$VoucherList[$i]['ChequeDate'];
				$ChequeNo  	=	$VoucherList[$i]['ChequeNumber'];
				$VoucherNo  =	$VoucherList[$i]['VoucherNo'];
				if($VoucherList[$i]['By'] <> '' &&  $VoucherList[$i]['To'] == '')
				{
					$AccuntNo =  $VoucherList[$i]['By'];
				}
				if($VoucherList[$i]['To'] <> '' && $VoucherList[$i]['By'] == '')
				{
					$AccuntNo =  $VoucherList[$i]['To'];
				}
				$Debit = $VoucherList[$i]['Debit'];
				$Credit = $VoucherList[$i]['Credit'];
				$LedgerName = $VoucherList[$i]['ledger_name'];
				$Notes = $VoucherList[$i]['Note'];
				//if($Debit <> 0 && $Credit <> 0)
				//{
					array_push($final_arr, array("VoucherNo"=>$VoucherNo,"ChequeDate"=>$ChequeDate,"ChequeNumber"=>$ChequeNo,"AccountNumber"=>$AccuntNo, "CreditAmount"=>$Credit, "DebitAmount"=>$Debit,"LedgerName"=>$LedgerName, "Note"=>$Notes));
				//}
			}
		
		//return 	$final_arr;
		$res = $this->DisplayReport($final_arr,$startDate,$endDate);
		return $res;
		
	}	
	public function DisplayReport($data,$startDate,$endDate)
	{
		//var_dump($data);
		$societyName = '<div style="text-align: center; display:none;" id="societname"> W2S Play Graund </div>'; 
		$head = '<div style="text-align: center;"><span style="font-size: 16px; font-weight: bold;">Payment Voucher </span><br><span style="font-size: 12px;color: blue; line-height: 25px;">From: '.$startDate.'  To : '.$endDate.' </span></div>';
		$tble = $societyName.''.$head.'<table class="table table-bordered table-hover table-striped">';
		$tble .='<tr><th style="text-align: center;">Date</th><th style="text-align: center;">Cheque No. </th><th style="text-align: center;">Ledger ID </th><th style="text-align: center;">Reference Description</th><th style="text-align: center;">Debit</th><th style="text-align: center;">Credit</th><th style="text-align: center;">Note</th></tr>';
		$preChequeNo =0;
		$preDate= '';
		$notes = '';
		for($i=0; $i<sizeof($data);$i++)
		{
			if($data[$i]['CreditAmount'] <> 0 || $data[$i]['DebitAmount'] <> 0)
			{
				$tble .='<tr>';
				if($data[$i]['ChequeNumber'] <> $preChequeNo )
				{
					$preChequeNo=$data[$i]['ChequeNumber']; 
					$tble .='<td style="text-align: center;">'.getDisplayFormatDate($data[$i]['ChequeDate']).'</td>';
					$tble .='<td style="text-align: center;">'.$data[$i]['ChequeNumber'].'</td>';
					$notes = $data[$i]['Note'];
				}
				else
				{
					$tble .='<td>&nbsp</td>';
					$tble .='<td>&nbsp</td>';
					$notes = '';
				}
				$tble .='<td style="text-align: center;">'.$data[$i]['AccountNumber'].'</td>';
				$tble .='<td>'.$data[$i]['LedgerName'].'</td>';
				if($data[$i]['CreditAmount'] <> 0)
				{
					$tble .='<td style="text-align: right;">'.$data[$i]['CreditAmount'].'</td>';
				}
				else
				{
					$tble .='<td>&nbsp</td>';
				}
				if($data[$i]['DebitAmount'] <> 0)
				{
					$tble .='<td style="text-align: right;">'.$data[$i]['DebitAmount'].'</td>';
				}
				else
				{
					$tble .='<td>&nbsp</td>';
				}
				$tble .='<td>'.$notes.'</td>';
				$tble .='</tr>';
			}
		}
		$tble .='</table>';
		return $tble;
	}
	
	
	public function fetchExpenseReportDetails1($ReportType,$startDate,$endDate)
	{
	
		$final_arr = array();
			// $sql ="SELECT v.*,p.ChequeDate,p. ChequeNumber,l.ledger_name FROM `voucher` as v  join paymentdetails as p on p.id= v.RefNo Left Join ledger as l on l.id=v.`By` OR l.id=v.`To` left join invoicestatus as ins on ins.InvoiceClearedVoucherNo=v.VoucherNo AND ins.InvoiceRaisedVoucherNo=v.VoucherNo  Where v.VoucherTypeID IN(2)  and v.`Date` between '".getDBFormatDate($startDate)."' AND '".getDBFormatDate($endDate)."' ";
			//echo  $sql ="SELECT v.*,p.ChequeDate,p. ChequeNumber,l.ledger_name,ins.InvoiceClearedVoucherNo,ins.InvoiceRaisedVoucherNo,ins.TDSVoucherNo FROM `voucher` as v join paymentdetails as p on p.id= v.RefNo Left Join ledger as l on l.id=v.`By` OR l.id=v.`To` left join invoicestatus as ins on ins.InvoiceClearedVoucherNo=v.VoucherNo Where v.VoucherTypeID IN(2) and v.`Date` between '".getDBFormatDate($startDate)."' AND '".getDBFormatDate($endDate)."' group by v.id";
			$sql ="SELECT v.*,p.ChequeDate,p. ChequeNumber,l.ledger_name,ins.InvoiceClearedVoucherNo,group_concat(ins.InvoiceRaisedVoucherNo) as RaisedVoucherNo,group_concat(ins.TDSVoucherNo) as TDSVoucherNo FROM `voucher` as v join paymentdetails as p on p.id= v.RefNo Left Join ledger as l on l.id=v.`By` OR l.id=v.`To` left join invoicestatus as ins on ins.InvoiceClearedVoucherNo=v.VoucherNo Where v.VoucherTypeID IN(2) and v.`Date` between '".getDBFormatDate($startDate)."' AND '".getDBFormatDate($endDate)."' group by v.ID";
			$VoucherList = $this->m_dbConn->select($sql);
			
			$final_Data = array();
			$tempVoucherNo = $VoucherList[0]['VoucherNo'];
			
			for($i= 0 ;$i<sizeof($VoucherList);$i++)
			{
				
				$VoucherNo =$VoucherList[$i]['VoucherNo'];
				$ChequeNo =$VoucherList[$i]['ChequeNumber'];
				$ChequeDate =$VoucherList[$i]['ChequeDate'];
				//$ClearVoucher =$VoucherList[$i]['InvoiceClearedVoucherNo'];
				$RaisedVoucher =$VoucherList[$i]['RaisedVoucherNo'];
				$TDSVoucher =$VoucherList[$i]['TDSVoucherNo'];
				$LedgerName = $VoucherList[$i]['ledger_name'];
				//$LedgerID = $VoucherList[$i]['To'];
				$DebitAmount = $VoucherList[$i]['Debit'];
				$CreditAmount = $VoucherList[$i]['Credit'];
				
				if($VoucherList[$i]['By'] <> '' &&  $VoucherList[$i]['To'] == '')
				{
					$AccuntNo =  $VoucherList[$i]['By'];
				}
				if($VoucherList[$i]['To'] <> '' && $VoucherList[$i]['By'] == '')
				{
					$AccuntNo =  $VoucherList[$i]['To'];
				}
				$Notes = $VoucherList[$i]['Note'];
			
				if($RaisedVoucher <> '' || $TDSVoucher <> '')
				{
						
					
					if($VoucherNo <> $VoucherList[$i+1]['VoucherNo']){
						
					
						array_push($final_Data, array("voucherNo"=>$VoucherNo, "Date"=> $ChequeDate,"ChequeNo"=>$ChequeNo, "LedgerName"=>$LedgerName, "DebitAmount"=>$RisedDebitAmount,"CreditAmount"=>$CreditAmount,"AccountNumber"=>$AccuntNo,"Notes"=>''));
					
					//if($VoucherList[$i]['To'] <> '' || $VoucherList[$i]['To'] <> 0 )
					//{
						$invoiceData = array();
						$LedgerID = $VoucherList[$i]['To'];
						
						
						//echo $sql01 = "Select * from voucher where VoucherNo IN(".$RaisedVoucher.")";
						$sql01 = "Select v.*,l.ledger_name from voucher as v Left Join ledger as l on l.id=v.`By` OR l.id=v.`To` where v.VoucherNo IN(".$RaisedVoucher.")";
						$raisedVoucherData = $this->m_dbConn->select($sql01);
						$cgst_amt = 0;
						$sgst_amt = 0;
						$SGST_LedgerName = '';
						$CGST_LedgerName = '';
						$RisedDebitAmount=0;
						$SGSTAccountNo = 0;
						$CGSTAccountNo = 0;
						
						for($j=0; $j <sizeof($raisedVoucherData);$j++ )
						{
							
							if($_SESSION['sgst_input'] == $raisedVoucherData[$j]['By'] )
							{
								
								$sgst_amt = $sgst_amt+$raisedVoucherData[$j]['Debit'];
								$SGST_LedgerName=$raisedVoucherData[$j]['ledger_name'];
								$SGSTAccountNo = $raisedVoucherData[$j]['By'];
							}
							if($_SESSION['cgst_input'] ==  $raisedVoucherData[$j]['By'] )
							{
								$cgst_amt = $cgst_amt+$raisedVoucherData[$j]['Debit'];
								$CGST_LedgerName=$raisedVoucherData[$j]['ledger_name'];
								$CGSTAccountNo = $raisedVoucherData[$j]['By'];
							}
							if(!empty($raisedVoucherData[$j]['By']) && $_SESSION['sgst_input'] <>  $raisedVoucherData[$j]['By'] && $_SESSION['cgst_input'] <>  $raisedVoucherData[$j]['By'] )
							{

								array_push($final_Data, array("voucherNo"=>$VoucherNo, "Date"=> $ChequeDate,"ChequeNo"=>$ChequeNo, "LedgerName"=>$raisedVoucherData[$j]['ledger_name'], "DebitAmount"=>$raisedVoucherData[$j]['Debit'],"CreditAmount"=>$raisedVoucherData[$j]['Credit'],"AccountNumber"=>$raisedVoucherData[$j]['By'],"Notes"=>''));
							}
							
						}
						array_push($final_Data, array("voucherNo"=>$VoucherNo,"Date"=> $ChequeDate,"ChequeNo"=>$ChequeNo, "LedgerName"=>$CGST_LedgerName, "DebitAmount"=>number_format((float)$cgst_amt, 2, '.', ''),"CreditAmount"=>0,"AccountNumber"=>$CGSTAccountNo,"Notes"=>$Notes));
						array_push($final_Data, array("voucherNo"=>$VoucherNo,"Date"=> $ChequeDate,"ChequeNo"=>$ChequeNo, "LedgerName"=>$SGST_LedgerName, "DebitAmount"=>number_format((float)$sgst_amt, 2, '.', ''),"CreditAmount"=>0,"AccountNumber"=>$SGSTAccountNo,"Notes"=>''));
						
						
						if(!empty($TDSVoucher))
						{
							
							$sql02 = "Select v.*,l.ledger_name from voucher as v Left Join ledger as l on l.id=v.`By` OR l.id=v.`To` where v.VoucherNo IN(".$TDSVoucher.")";
							$TDSVoucherData = $this->m_dbConn->select($sql02);
							$TDS_amt = 0;
							$TDS_Ledger = '';
							$TDSAccountNo= 0;
							for($k=0; $k <sizeof($TDSVoucherData);$k++ )
							{
								$TDS_amt = $TDS_amt+$TDSVoucherData[$k]['Credit'];
								$TDS_Ledger = $TDSVoucherData[$k]['ledger_name'];
								$TDSAccountNo = $TDSVoucherData[$k]['To'];
							}
						array_push($final_Data, array("voucherNo"=>$VoucherNo,"Date"=> $ChequeDate,"ChequeNo"=>$ChequeNo, "LedgerName"=>$TDS_Ledger, "DebitAmount"=>0,"CreditAmount"=>number_format((float)$TDS_amt, 2, '.', ''),"AccountNumber"=>$TDSAccountNo,"Notes"=>''));
						//}
					
					}
					
				}
				array_push($final_Data, array("voucherNo"=>$VoucherNo, "Date"=> $ChequeDate,"ChequeNo"=>$ChequeNo, "LedgerName"=>$LedgerName, "DebitAmount"=>$DebitAmount,"CreditAmount"=>$CreditAmount,"AccountNumber"=>$AccuntNo,"Notes"=>$Notes));

			}
			else if(empty($RaisedVoucher) && empty($TDSVoucher))
			{
				array_push($final_Data, array("voucherNo"=>$VoucherNo, "Date"=> $ChequeDate,"ChequeNo"=>$ChequeNo, "LedgerName"=>$LedgerName, "DebitAmount"=>$DebitAmount,"CreditAmount"=>$CreditAmount,"AccountNumber"=>$AccuntNo,"Notes"=>$Notes));
				
			}
						$tempVoucherNo = $VoucherNo;
				
		}
		
		$Result = $this->DisplayReport1($final_Data,$startDate,$endDate);
		return $Result;
		
	}	
	
	public function DisplayReport1($data,$startDate,$endDate)
	{
		//var_dump($data);
		$societyName = '<div style="text-align: center; display:none;" id="societname"> W2S Play Graund </div>'; 
		$head = '<div style="text-align: center;"><span style="font-size: 16px; font-weight: bold;">Payment Voucher </span><br><span style="font-size: 12px;color: blue; line-height: 25px;">From: '.$startDate.'  To : '.$endDate.' </span></div>';
		$tble = $societyName.''.$head.'<table class="table table-bordered table-hover table-striped">';
		$tble .='<tr><th style="text-align: center;">Date</th><th style="text-align: center;">Cheque No. </th><th style="text-align: center;">Ledger ID </th><th style="text-align: center;">Reference Description</th><th style="text-align: center;">Debit</th><th style="text-align: center;">Credit</th><th style="text-align: center;">Note</th></tr>';
		$preChequeNo =0;
		$preDate= '';
		$notes = '';
		for($i=0; $i<sizeof($data);$i++)
		{
			if($data[$i]['CreditAmount'] <> 0 || $data[$i]['DebitAmount'] <> 0)
			{
				$tble .='<tr>';
				if($data[$i]['ChequeNo'] <> $preChequeNo )
				{
					$preChequeNo=$data[$i]['ChequeNo']; 
					$tble .='<td style="text-align: center;">'.getDisplayFormatDate($data[$i]['Date']).'</td>';
					$tble .='<td style="text-align: center;">'.$data[$i]['ChequeNo'].'</td>';
					$notes = $data[$i]['Notes'];
				}
				else
				{
					$tble .='<td>&nbsp</td>';
					$tble .='<td>&nbsp</td>';
					$notes = '';
				}
				$tble .='<td style="text-align: center;">'.$data[$i]['AccountNumber'].'</td>';
				$tble .='<td>'.$data[$i]['LedgerName'].'</td>';
				if($data[$i]['CreditAmount'] <> 0)
				{
					$tble .='<td style="text-align: right;">'.$data[$i]['CreditAmount'].'</td>';
				}
				else
				{
					$tble .='<td>&nbsp</td>';
				}
				if($data[$i]['DebitAmount'] <> 0)
				{
					$tble .='<td style="text-align: right;">'.$data[$i]['DebitAmount'].'</td>';
				}
				else
				{
					$tble .='<td>&nbsp</td>';
				}
				$tble .='<td style="width:20%">'.$data[$i]['Notes'].'</td>';
				$tble .='</tr>';
			}
		}
	$tble .='</table>';
		return $tble;	
	} 
}

?>
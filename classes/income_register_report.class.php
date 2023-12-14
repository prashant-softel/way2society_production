<?php
include_once("dbconst.class.php");
class income_report extends dbop
{
	public $m_dbConn;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;

	}


public function getIncomeDetailsNew($from, $to)
{
	$ledgername_array=array();
	
//$sql="SELECT incometbl.Credit,incometbl.Debit,incometbl.Date,incometbl.LedgerID,vouchertbl.VoucherNo,vouchertbl.Note,incometbl.VoucherID from `incomeregister` as incometbl  JOIN `voucher` as vouchertbl on vouchertbl.id=incometbl.VoucherID where incometbl.Date between '".$from_date."' and '".$to_date."' GROUP BY  incometbl.LedgerID,incometbl.Date";
//$sql="SELECT DATE_FORMAT(v.`Date`,'%M %Y') AS MonthYear, Sum(i.TDSAmount) as TotalTDS, Sum(i.IGST_Amount) as TotalIGST, Sum(i.CGST_Amount) as TotalCGST,Sum(i.SGST_Amount) as TotalSGST, Sum(i.CESS_Amount) as TotalCESS FROM `invoicestatus` as i JOIN `voucher`as v ON i.InvoiceRaisedVoucherNo =v.VoucherNo where v.id in (Select min(id) from voucher group by VoucherNo) and (v.Date between '".getDBFormatDate($from)."' and '".getDBFormatDate($to)."') group by YEAR(Date),MONTH(Date) Order by Date";
$sql="SELECT SUM(incometbl.Credit) as Credit,SUM(incometbl.Debit) as Debit,incometbl.Date,incometbl.LedgerID from `incomeregister` as incometbl WHERE incometbl.Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."' GROUP BY incometbl.Date,incometbl.LedgerID  ORDER BY incometbl.LedgerID, incometbl.Date";
$result=$this->m_dbConn->select($sql);

$get_ledger_name="select id,ledger_name from `ledger`";
$result02=$this->m_dbConn->select($get_ledger_name);

//print_r($result02);
for($i = 0; $i < sizeof($result02); $i++)
{
$ledgername_array[$result02[$i]['id']]=$result02[$i]['ledger_name'];

}

for($i = 0; $i < sizeof($result); $i++)
{
	//$result[$i]['BY'] = $ledgername_array[$result[$i]['LedgerID']];
	$result[$i]['TO'] = $ledgername_array[$result[$i]['LedgerID']];
}
//print_r($result);
return $result;	
}

/*-----------------GST DETAILS------------------*/


public function get_InvoiceGSTDetails($from, $to)
{
	//echo "Begining of function";
	//echo "IGST_SERVICE_TAX 2<" .SGST_SERVICE_TAX . ">"; 
	$ledgername_array=array();
	
	$sql="SELECT DATE_FORMAT(v.`Date`,'%M %Y') AS MonthYear, Sum(i.TDSAmount) as TotalTDS, Sum(i.IGST_Amount) as TotalIGST, Sum(i.CGST_Amount) as TotalCGST,Sum(i.SGST_Amount) as TotalSGST, Sum(i.CESS_Amount) as TotalCESS FROM `invoicestatus` as i JOIN `voucher`as v ON i.InvoiceRaisedVoucherNo =v.VoucherNo where v.id in (Select min(id) from voucher group by VoucherNo) and (v.Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."') group by YEAR(Date),MONTH(Date) Order by Date";
	
$result=$this->m_dbConn->select($sql);
//print_r($result);
$get_ledger_name="select id,ledger_name from `ledger`";
$result02=$this->m_dbConn->select($get_ledger_name);

//print_r($result02);
for($i = 0; $i < sizeof($result02); $i++)
{
$ledgername_array[$result02[$i]['id']]=$result02[$i]['ledger_name'];

}

for($i = 0; $i < sizeof($result); $i++)
{
	//$result[$i]['BY'] = $ledgername_array[$result[$i]['LedgerID']];
	$result[$i]['TO'] = $ledgername_array[$result[$i]['LedgerID']];
}
//print_r($result);
return $result;	
}


public function getGSTDetails($from, $to)
{
	$ledgername_array=array();
	$sql="SELECT DATE_FORMAT(`Date`,'%M %Y') AS MonthYear,`LedgerID` , SUM(CASE WHEN (`LedgerID`='".IGST_SERVICE_TAX."') THEN Credit ELSE 0 END) AS 'IGST' , SUM(CASE WHEN (`LedgerID`='".CGST_SERVICE_TAX."') THEN Credit ELSE 0 END) AS 'CGST' , SUM(CASE WHEN (`LedgerID`='".SGST_SERVICE_TAX."') THEN Credit ELSE 0 END) AS 'SGST' , SUM(CASE WHEN (`LedgerID`='".CESS_SERVICE_TAX."') THEN Credit ELSE 0 END) AS 'CESS' FROM `incomeregister` WHERE Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."' and LedgerID IN ('".IGST_SERVICE_TAX."','".CGST_SERVICE_TAX."','".SGST_SERVICE_TAX."','".CESS_SERVICE_TAX."') group by YEAR(Date), MONTH(Date) Order by LedgerID , Date";
	
//$sql="SELECT incometbl.Credit,incometbl.Debit,incometbl.Date,incometbl.LedgerID,vouchertbl.VoucherNo,vouchertbl.Note,incometbl.VoucherID from `incomeregister` as incometbl  JOIN `voucher` as vouchertbl on vouchertbl.id=incometbl.VoucherID where incometbl.Date between '".$from_date."' and '".$to_date."' GROUP BY  incometbl.LedgerID,incometbl.Date";
//$sql="SELECT SUM(incometbl.Credit) as Credit,SUM(incometbl.Debit) as Debit,incometbl.Date,incometbl.LedgerID from `incomeregister` as incometbl WHERE incometbl.Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."' and incometbl.LedgerID IN ('".IGST_SERVICE_TAX."','".CGST_SERVICE_TAX."','".SGST_SERVICE_TAX."','".CESS_SERVICE_TAX."') GROUP BY incometbl.Date,incometbl.LedgerID  ORDER BY incometbl.LedgerID, incometbl.Date";
$result=$this->m_dbConn->select($sql);

$get_ledger_name="select id,ledger_name from `ledger`";
$result02=$this->m_dbConn->select($get_ledger_name);

//print_r($result02);
for($i = 0; $i < sizeof($result02); $i++)
{
$ledgername_array[$result02[$i]['id']]=$result02[$i]['ledger_name'];

}

for($i = 0; $i < sizeof($result); $i++)
{
	//$result[$i]['BY'] = $ledgername_array[$result[$i]['LedgerID']];
	$result[$i]['TO'] = $ledgername_array[$result[$i]['LedgerID']];
}
//print_r($result);
return $result;	
}


public function getIncomeDetails($from_date,$to_date, $ledger_id = "")
{
	$ledgername_array=array();
	
$sql="SELECT incometbl.Credit,incometbl.Debit,incometbl.Date,incometbl.LedgerID,vouchertbl.VoucherNo,vouchertbl.Note,incometbl.VoucherID from `incomeregister` as incometbl  JOIN `voucher` as vouchertbl on vouchertbl.id=incometbl.VoucherID where incometbl.Date between '".getDBFormatDate($from_date)."' and '".getDBFormatDate($to_date)."' ";

if(!empty($ledger_id)){
	$sql .= " AND incometbl.LedgerID ='".$ledger_id."'";
}
$result=$this->m_dbConn->select($sql);

//echo $sql;
$get_ledger_name="select id,ledger_name from `ledger`";
$result02=$this->m_dbConn->select($get_ledger_name);

//print_r($result02);
for($i = 0; $i < sizeof($result02); $i++)
{
$ledgername_array[$result02[$i]['id']]=$result02[$i]['ledger_name'];

}

for($i = 0; $i < sizeof($result); $i++)
{
	//$result[$i]['BY'] = $ledgername_array[$result[$i]['LedgerID']];
	$result[$i]['TO'] = $ledgername_array[$result[$i]['LedgerID']];
}
//print_r($result);
return $result;	
}

public function show_particulars($lid,$vid, $requiredMemberDetails = false)
	{
		
	$sql2="select RefNo,RefTableID,VoucherNo from `voucher` where id=".$vid." ";
	//echo $sql2;
		$data2=$this->m_dbConn->select($sql2);
		$RefNo=$data2[0]['RefNo'];
		$RefTableID=$data2[0]['RefTableID'];
		$VoucherNo=$data2[0]['VoucherNo'];
		
		
		$sql3="select `ledger_name`, mm.owner_name from `voucher` as vouchertbl JOIN `ledger` as ledgertbl on vouchertbl.By=ledgertbl.id JOIN member_main as mm ON mm.unit=ledgertbl.id where vouchertbl.RefNo='".$RefNo."' and vouchertbl.RefTableID='".$RefTableID."' and vouchertbl.VoucherNo='".$VoucherNo."'";

		if($requiredMemberDetails){
			$sql3 .= " AND mm.ownership_status = 1 ";
		}
		//echo $sql3;
		$data3=$this->m_dbConn->select($sql3);	
		if($requiredMemberDetails){
			return $data3;
		}
		return $data3[0]['ledger_name'];
	}

public function paid_InvoiceGSTDetails($from, $to)
{
	//echo "Begining of function";
	//echo "IGST_SERVICE_TAX 2<" .SGST_SERVICE_TAX . ">"; 
	$ledgername_array=array();
	
	//$sqlData="select v.`Date` as MonthYear, ast.VoucherID, ast.LedgerID from `voucher` as v join `assetregister` as ast on v.id=ast.VoucherID where (v.Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."')";
	
	//$resultData=$this->m_dbConn->select($sqlData);

// NEW 
 $sql = "SELECT v.`Date` AS MonthYear, i.TDSAmount as TotalTDS,i.CGST_Amount as TotalCGST,i.SGST_Amount as TotalSGST,i.IGST_Amount as TotalIGST, i.AmountReceived as TotalInvoiceAmount, i.TDSAmount as TDSAmounts, i.InvoiceRaisedVoucherNo,v.To,l.ledger_name as LegerName,ld.GSTIN_No,ld.PAN_No, i.NewInvoiceNo,ld.TDS_NatureOfPayment as NatureName,i.RoundOffAmount as TotalRoundOff FROM `invoicestatus` as i JOIN `voucher`as v ON i.InvoiceRaisedVoucherNo =v.VoucherNo join ledger as l on v.To=l.id left join ledger_details as ld on l.id=ld.LedgerID  where v.To!='' and (v.Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."') and v.VoucherNo IN (select v.VoucherNo from `voucher` as v join `assetregister` as ast on v.id=ast.VoucherID where (v.Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."') and ast.LedgerID IN ('".$_SESSION['sgst_input']."' ,'".$_SESSION['cgst_input']."' ,'".$_SESSION['igst_input']."'))  order by l.ledger_name ,v .`Date` ASC";
 
 
 //Existing 
/// echo $sql = "SELECT v.`Date` AS MonthYear, i.TDSAmount as TotalTDS,i.CGST_Amount as TotalCGST,i.SGST_Amount as TotalSGST, i.AmountReceived as TotalInvoiceAmount, i.TDSAmount as TDSAmounts, i.InvoiceRaisedVoucherNo,v.To,l.ledger_name as LegerName,ld.GSTIN_No,ld.PAN_No, i.NewInvoiceNo,ld.TDS_NatureOfPayment as NatureName FROM `invoicestatus` as i JOIN `voucher`as v ON i.InvoiceRaisedVoucherNo =v.VoucherNo join ledger as l on v.To=l.id left join ledger_details as ld on l.id=ld.LedgerID  where v.To!='' and (v.Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."') and v.VoucherNo IN (select v.VoucherNo from `voucher` as v join `assetregister` as ast on v.id=ast.VoucherID where (v.Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."') and ast.LedgerID IN ('".$_SESSION['sgst_input']."' ,'".$_SESSION['cgst_input']."' )) and CGST_Amount NOT IN (0) AND SGST_Amount NOT IN (0) order by l.ledger_name ,v .`Date` ASC";
	
	 // $sql="SELECT v.`Date` AS MonthYear, i.TDSAmount as TotalTDS, i.IGST_Amount as TotalIGST,i.CGST_Amount as TotalCGST,i.SGST_Amount as TotalSGST, i.CESS_Amount as TotalCESS,i.AmountReceived as TotalInvoiceAmount, i.TDSAmount as TDSAmounts, i.InvoiceRaisedVoucherNo,v.To,l.ledger_name as LegerName,ld.GSTIN_No,ld.PAN_No, i.NewInvoiceNo  FROM `invoicestatus` as i JOIN `voucher`as v ON i.InvoiceRaisedVoucherNo =v.VoucherNo join ledger as l on v.To=l.id left join ledger_details as ld on l.id=ld.LedgerID where v.To!='' and (v.Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."') and v.VoucherNo IN (select v.`Date` as MonthYear,v.VoucherNo, ast.VoucherID, ast.LedgerID from `voucher` as v join `assetregister` as ast on v.id=ast.VoucherID where (v.Date between '2017-04-01' AND '2018-03-31') and ast.LedgerID=449) order by l.ledger_name ,v .`Date` ASC";
	
	$result=$this->m_dbConn->select($sql);

	// Written by Amit 
	// Few GST entry were made from JV but it was not an invoice so it was not displaying in report To fix that issue I have added below code
	// First Its take all the voucher related to GST from Asset Register and check with invoice status table then pending voucher will be treated separately

	$qry = "select v.VoucherNo from `voucher` as v join `assetregister` as ast on v.id=ast.VoucherID where (v.Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."') and ast.LedgerID IN ('".$_SESSION['sgst_input']."' ,'".$_SESSION['cgst_input']."')";
	$GSTVoucherResult = $this->m_dbConn->select($qry);

	if(!empty($GSTVoucherResult)){ // if it not empty

		$GSTVoucherNoArr = array_column($GSTVoucherResult, 'VoucherNo');

		$invoiceVoucherNoArr = array_column($result, 'InvoiceRaisedVoucherNo');

		$pendingVoucherNoArr = array_unique(array_diff($GSTVoucherNoArr, $invoiceVoucherNoArr));

		$voucherString = implode($pendingVoucherNoArr, ',');

		if(!empty($voucherString)){

			$pendingGstQry = "SELECT `Date`, `By`, `To`, `Debit`, `Credit`, VoucherNo,  l.ledger_name as LegerName,ld.GSTIN_No,ld.PAN_No, ld.TDS_NatureOfPayment as NatureName FROM voucher as v LEFT JOIN ledger as l on v.To=l.id left JOIN ledger_details as ld on l.id=ld.LedgerID  WHERE VoucherNo IN($voucherString)";

			$pendingGSTResult = $this->m_dbConn->select($pendingGstQry);

			$count = count($result);
			$tempVoucher = $pendingGSTResult[0]['VoucherNo'];

			foreach ($pendingGSTResult as $value) {

				extract($value);

				if($tempVoucher != $VoucherNo){

					$count++;
				}

				if($tempVoucher == $VoucherNo){

					if($To == "" && ($By == $_SESSION['sgst_input'] || $By == $_SESSION['cgst_input'])){

						if($By == $_SESSION['cgst_input']){

							$result[$count]['TotalCGST'] = $Debit;
						}

						if($By == $_SESSION['sgst_input']){

							$result[$count]['TotalSGST'] = $Debit;
						}
					}		
					else if($To == "" && $By != $_SESSION['sgst_input'] && $By != $_SESSION['cgst_input']) {

						$result[$count]['InvoiceAmount'] += $Debit;
					}
					else if($To != ""){

						$result[$count]['TotalInvoiceAmount'] = $Credit;
						$result[$count]['LegerName'] = $LegerName;
						$result[$count]['GSTIN_No'] = $GSTIN_No;
						$result[$count]['PAN_No'] = $PAN_No;
						$result[$count]['NatureName'] = $NatureName;
						$result[$count]['MonthYear'] = $Date;
					}
				}
				$tempVoucher = $VoucherNo;
			}
		}
	}
	

	return $result;	
}
///------------------------------------------New GST Incoming report-------------------------------///
public function getGSTIncomingDetails($from, $to, $LedgerList)
{
	
	$ledgername_array=array();
	
	$result = array();
	
//	$sql="select `CGST` , `SGST` ,UnitID ,billregister.BillDate ,billdetails.BillNumber, billdetails.BillType from billdetails join billregister on billdetails.BillRegisterID=billregister.ID where (billregister.BillDate between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."') order by UnitID,billregister.BillDate";
//    $sql .= " UNION select `CGST` , `SGST`, UnitID ,Inv_Date as BillDate, Inv_Number as BillNumber   from sale_invoice where (Inv_Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."') order by UnitID, Inv_Date";
	
	$sql = "select `CGST` , `SGST` ,UnitID ,billregister.BillDate ,billdetails.BillNumber,if(billdetails.BillType = 0, 'Maintenance','Supplementry') as BillType,CurrentBillAmount as BillAmount from billdetails join billregister on billdetails.BillRegisterID=billregister.ID 
			where CGST != 0 and SGST != 0 and (billregister.BillDate between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."') 
			UNION 
			select `CGST` , `SGST`, UnitID ,Inv_Date as BillDate, Inv_Number as BillNumber, if(BillType = 2, 'Invoice','') as BillType,TotalPayable as BillAmount from sale_invoice 
			where  CGST != 0 and SGST != 0 and (Inv_Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."') order by UnitID,BillDate";
		
	$data= $this->m_dbConn->select($sql);
	return $data;
	

	
	
	
	
/*	

	if(!empty($Invoice_Result)){

		if($Invoice_Result[0]['TotalCGST'] <> 0 || $Invoice_Result[0]['TotalSGST'] <> 0){
			
			$result[0]['Invoice_CGST'] = $Invoice_Result[0]['TotalCGST'];
			$result[0]['Invoice_SGST'] = $Invoice_Result[0]['TotalSGST'];
			$result[0]['Invoice_Number'] = $Invoice_Result[0]['Inv_Number'];
			$result[0]['Invoice_Date'] = getDisplayFormatDate($Invoice_Result[0]['Inv_Date']);
		}
		else
		{
			$result[0]['Invoice_Number'] = '-';
			$result[0]['Invoice_Date'] = '-';  
			$result[0]['Invoice_CGST'] = 0;
			$result[0]['Invoice_SGST'] = 0;
		}
	}
	
	$result[0]['BillDate'] = getDisplayFormatDate($result[0]['BillDate']);
	if(empty($result[0]['BillDate']))
	{
		$result[0]['BillDate'] = '-';
		$result[0]['BillNumber'] = '-';
	}
	
	
	

$get_ledger_name="select id,ledger_name from `ledger`";
$result02=$this->m_dbConn->select($get_ledger_name);

//print_r($result02);
for($i = 0; $i < sizeof($result02); $i++)
{
$ledgername_array[$result02[$i]['id']]=$result02[$i]['ledger_name'];

}

for($i = 0; $i < sizeof($result); $i++)
{
	//$result[$i]['BY'] = $ledgername_array[$result[$i]['LedgerID']];
	$result[$i]['TO'] = $ledgername_array[$result[$i]['LedgerID']];
}
//print_r($result);*/
return $result;	
}
public function paid_TDSDetails($from, $to)
{
	$ledgername_array=array();
	$sqlData="select ledgertbl.id,lr.Date,ledgertbl.ledger_name as Particular,lr.Debit,lr.Credit,lr.VoucherID,lr.VoucherTypeID,lr.Is_Opening_Balance, v.VoucherNo from voucher as v,`liabilityregister` as lr JOIN `ledger` as ledgertbl on lr.LedgerID=ledgertbl.id where (lr.Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."') AND lr.LedgerID = '".TDS_PAYABLE."'  and Is_Opening_Balance = 0 AND v.`id`= lr.`VoucherId` ORDER BY Date ASC";
	$resultData=$this->m_dbConn->select($sqlData);
	
	//Amit (For Loop  ->Added for show Payment date in Tds Report if it's link to Payment)
	for ($i=0; $i < count($resultData); $i++) { 
		
		$InvoiceStatusQuery = "SELECT v.`Date`, invoice.`InvoiceClearedVoucherNo` FROM invoicestatus as invoice JOIN `voucher` as v ON v.VoucherNo = invoice.InvoiceClearedVoucherNo WHERE invoice.`TDSVoucherNo` = '".$resultData[$i]['VoucherNo']."'";
		$InvoiceStatusDetails = $this->m_dbConn->select($InvoiceStatusQuery);
		
		if(!empty($InvoiceStatusDetails[0]['InvoiceClearedVoucherNo']) && $InvoiceStatusDetails[0]['InvoiceClearedVoucherNo'] <> 0){
			$InvoiceStatusDetails[0]['Date'];
			$resultData[$i]['Date'] = $InvoiceStatusDetails[0]['Date'];
		}
	}

	// Amit code end



	return $resultData;	
}
public function getLedgerDetails()
{
	$sql = "SELECT * , ac.group_id,ac.category_name FROM `ledger` join `account_category` as ac on ledger.categoryid=ac.category_id where id = ".TDS_PAYABLE;
	$res = $this->m_dbConn->select($sql);
	return $res;
}
public function getSocietyTanNo()
{
	$sql = "SELECT tan_no FROM society where society_id = ".$_SESSION['society_id'];
	$res = $this->m_dbConn->select($sql);
	return $res;
}
public function getNatureOfPayment($lId)
{
	$sql = "SELECT TDS_NatureOfPayment FROM `ledger_details` where LedgerID = ".$lId;
	$natureOfPayment = $this->m_dbConn->select($sql);
	return $natureOfPayment;
}
}

?>
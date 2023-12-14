<?php include_once("../classes/PaymentDetails.class.php");
include_once("../classes/include/dbop.class.php");
include_once("../classes/dbconst.class.php");
include_once("../classes/utility.class.php");
include_once("../classes/latestcount.class.php");
include_once("../classes/changelog.class.php");
$dbConn = new dbop();
$obj_PaymentDetails = new PaymentDetails($dbConn);
$objUtility = new utility($dbConn);
$m_latestcount = new latestCount($dbConn);
$changeLog = new changeLog($dbConn);

echo $_REQUEST["method"]."@@@";
if($_REQUEST['method']=="Fetch")
	{
		$lId = $_REQUEST['lId'];
		
		
		
		$memId = $obj_PaymentDetails->getNarration($lId);
			
		echo $memId;
	}
	
if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_PaymentDetails->selecting();

	foreach($select_type as $k => $v)
	{
		echo json_encode($v);
		/*foreach($v as $kk => $vv)
		{
			echo $vv."#";
		}*/
	}
}

if(isset($_REQUEST["method"]) &&  $_REQUEST["method"]=="DeleteInvoice")
{
	echo $obj_PaymentDetails->DeletePaymentInvoice($_REQUEST['VoucherNo'],$_REQUEST['ClearVoucherNo'],$_REQUEST['BankID'],$_REQUEST['LeafID']);	
}


if($_REQUEST["method"]=="UpdateCashPaymentDetails")
{
	$obj_PaymentDetails->actionType = 3;
	$Detail = json_decode(str_replace('\\', '', $_REQUEST['data']), true);
	//print_r($Detail);
	$CashDetailsId=$_REQUEST["CashDetailsId"];
	$PaidTo=$Detail[0]['PaidTo'];
	$ChequeNumber=$Detail[0]['ChequeNumber'];
	$VoucherNumber = $Detail[0]['VoucherNumber'];
	$IsCallUpdtCnt=$Detail[0]['IsCallUpdtCnt'];
	$ChequeDate=$Detail[0]['ChequeDate'];
	$Amount=$Detail[0]['Amount'];
	$PayerBank=$Detail[0]['PayerBank'];
	$Comments=$Detail[0]['Comments'];
	$VoucherDate=$Detail[0]['VoucherDate'];
	$InvoiceDate=$Detail[0]['InvoiceDate'];
	$TDSAmount=$Detail[0]['TDSAmount'];
	$LeafID=$Detail[0]['LeafID'];
	$DoubleEntry = $Detail[0]["DoubleEntry"];
	$ExpenseBy=$Detail[0]['ExpenseBy'];
	$OnPageLoadTimeVoucherNumber = $Detail[0]['OnPageLoadTimeVoucherNumber'];
	//$obj_PaymentDetails->UpdateCashPayment($VoucherDate, $ChequeDate, $ChequeNumber, $Amount, $PaidBy, $BankID, $PayerBank, $Comments,$CashDetailsId);
	$PaymentVoucherNo = 0;
	echo $obj_PaymentDetails->UpdatePaymentDetails($PaidTo,$ChequeNumber, $VoucherNumber,$OnPageLoadTimeVoucherNumber, $IsCallUpdtCnt, $ChequeDate,$Amount,$PayerBank,$Comments,$VoucherDate,$InvoiceDate,$TDSAmount,$LeafID,$DoubleEntry,$ExpenseBy,$CashDetailsId,$PaymentVoucherNo);
}

if($_REQUEST["method"]=="EditPaymentDetails")
{
	//echo "inside ajax file EditPaymentDetails";
	$obj_PaymentDetails->actionType = 3;
	$Detail1 = json_decode(str_replace('\\', '', $_REQUEST['data']), true);	
	
	$Detail = sortArray($Detail1);
	
	for($iCnt = 0 ; $iCnt < sizeof($Detail); $iCnt++)
	{
		$PaidTo=$Detail[$iCnt]['PaidTo'];
		$ChequeNumber=$Detail[$iCnt]['ChequeNumber'];
		$ChequeDate=$Detail[$iCnt]['ChequeDate'];
		$Amount=$Detail[$iCnt]['Amount'];
		$PayerBank=$Detail[$iCnt]['PayerBank'];
		$Comments=$Detail[$iCnt]['Comments'];
		$VoucherDate=$Detail[$iCnt]['VoucherDate'];
		$VoucherNumber = $Detail[$iCnt]['VoucherNumber'];
		$OnPageLoadTimeVoucherNumber = $Detail[$iCnt]['OnPageLoadTimeVoucherNumber'];
		$IsCallUpdtCnt = $Detail[$iCnt]['IsCallUpdtCnt'];
		$InvoiceDate=$Detail[$iCnt]['InvoiceDate'];
		$TDSAmount=$Detail[$iCnt]['TDSAmount'];
		$LeafID=$Detail[$iCnt]['LeafID'];
		$DoubleEntry = $Detail[$iCnt]["DoubleEntry"];
		$ExpenseBy=$Detail[$iCnt]['ExpenseBy'];
		$ExpenseBy=$Detail[$iCnt]['ExpenseBy'];
		$ModeOfPayment=$Detail[$iCnt]['ModeOfPayment'];
		$rowID =  $Detail[$iCnt]['RowID'];
		$reconcleDate = $Detail[$iCnt]['ReconcileDate'];
		$rStatus = $Detail[$iCnt]['ReconcileStatus'];
		$reconcile = $Detail[$iCnt]['Reconcile'];
		$returnFlag = $Detail[$iCnt]['ReturnFlag'];
		$MultipleEntry = $Detail[$iCnt]["MultipleEntry"];
		$Ref = $Detail[$iCnt]['Ref'];		
		$InvoiceAmount = $Detail[$iCnt]['InvoiceAmount'];
		$BillType = $Detail[$iCnt]['BillType'];
		echo $res= $obj_PaymentDetails->UpdatePaymentDetails($PaidTo,$ChequeNumber,$VoucherNumber, $OnPageLoadTimeVoucherNumber, $IsCallUpdtCnt,$ChequeDate,$Amount,$PayerBank,$Comments,$VoucherDate,$InvoiceDate,$TDSAmount,$LeafID,$DoubleEntry,$ExpenseBy, $rowID, $ModeOfPayment, $reconcleDate, $rStatus, $reconcile, $returnFlag, $MultipleEntry, $Ref,$InvoiceAmount,$PaymentVoucherNo,$ExistPaymemtVoucher,$bSkipBeginTrnx,true, $bcash,$BillType);	
		
	}
}
if($_REQUEST["method"]=="delete")
{
	$obj_PaymentDetails->actionType = 2;
	$Data=$obj_PaymentDetails->m_dbConn->select("select * from `paymentdetails` where id=".$_REQUEST["PaymentDetailsId"]." ");
	$MultipleEntryData = array();
	
	if($Data[0]['Reference'] <> 0)
	{
		$MultipleEntryData = $obj_PaymentDetails->m_dbConn->select("SELECT * FROM `paymentdetails` WHERE `Reference` = '".$Data[0]['Reference']."'");		
	}
	if(sizeof($MultipleEntryData) > 0)
	{
		$prevRef = 0;
		for($i = 0; $i < sizeof($MultipleEntryData); $i++)
		{
			$obj_PaymentDetails->deletePaymentDetails($MultipleEntryData[$i]['ChequeDate'],$MultipleEntryData[$i]['ChequeNumber'],$MultipleEntryData[$i]['VoucherDate'],
				$MultipleEntryData[$i]['Amount'],$MultipleEntryData[$i]['PaidTo'],$MultipleEntryData[$i]['ExpenseBy'],$MultipleEntryData[$i]['PayerBank'],$MultipleEntryData[$i]['ChqLeafID'],
				$MultipleEntryData[$i]['Comments'],$MultipleEntryData[$i]['InvoiceDate'],$MultipleEntryData[$i]['TDSAmount'],$MultipleEntryData[$i]["id"],false,$MultipleEntryData[$i]['Reference'],$prevRef);
			$prevRef = $MultipleEntryData[$i]['Reference'];				
		}
	}
	else
	{
		$obj_PaymentDetails->deletePaymentDetails($Data[0]['ChequeDate'],$Data[0]['ChequeNumber'],$Data[0]['VoucherDate'],$Data[0]['Amount'],$Data[0]['PaidTo'],$Data[0]['ExpenseBy'],$Data[0]['PayerBank'],$Data[0]['ChqLeafID'],$Data[0]['Comments'],$Data[0]['InvoiceDate'],$Data[0]['TDSAmount'],$_REQUEST["PaymentDetailsId"]);	
	}		
}
if($_REQUEST['method'] == 'FetchVoucher')
{
	$sql = "SELECT `Reference` FROM `paymentdetails` WHERE `id` = '".$_REQUEST['pId']."'";
	$result = $obj_PaymentDetails->m_dbConn->select($sql);
	
	if($result[0]['Reference'] <> 0)
	{
	 	$selectQuery = 'SELECT bank.VoucherTypeID, vchr.VoucherNo FROM `bankregister` AS bank JOIN `voucher` AS vchr ON bank.VoucherID = vchr.id WHERE bank.ChkDetailID = "'.$result[0]['Reference'].'" AND vchr.RefTableID = 3';		
	}
	else
	{		
		 $selectQuery = 'SELECT bank.VoucherTypeID, vchr.VoucherNo FROM `bankregister` AS bank JOIN `voucher` AS vchr ON bank.VoucherID = vchr.id WHERE bank.ChkDetailID = "'.$_REQUEST['pId'].'" AND vchr.RefTableID = 3';
	}
	$voucherDetails = $obj_PaymentDetails->m_dbConn->select($selectQuery);	
		
	echo base64_encode($voucherDetails[0]['VoucherNo']) . '#'. base64_encode($voucherDetails[0]['VoucherTypeID']);	
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'fetchLedgers')
{	
	$result = $obj_PaymentDetails->comboboxEx("select id,concat_ws(' - ', ledgertable.ledger_name,categorytbl.category_name)  from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where ledgertable.payment='1' and ledgertable.society_id=".$_SESSION['society_id']. " ORDER BY ledgertable.ledger_name ASC");
	echo $result;	
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'fetchExpenseBy')
{
	$PaidTo = $obj_PaymentDetails->comboboxEx("select id,concat_ws(' - ', ledgertable.ledger_name,categorytbl.category_name)  from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where ledgertable.expense='1' and ledgertable.society_id=".$_SESSION['society_id']." ORDER BY ledgertable.ledger_name ASC");	
	echo $PaidTo;
}


if($_REQUEST["mode"] == "Fill")
{
	$MultipleEntryArr = array();	
	
	$chqDetail = json_decode(str_replace('\\', '', $_REQUEST['Cheque']), true);
	for($iCnt = 0 ; $iCnt < sizeof($chqDetail); $iCnt++)
	{
		$show_in_jvformat = 0;	
	//$Data = $obj_PaymentDetails->m_dbConn->select("select PaidTo,ExpenseBy, DATE_FORMAT(ChequeDate, '%d-%m-%Y'),Amount,Comments,ChequeNumber from paymentdetails where ChequeNumber=".$chqDetail[$iCnt]['cheque']." and ChqLeafID=".$_REQUEST["LeafID"]);
		if($_REQUEST["CustomLeaf"] == "0")
		{			
      		$sql1 = "select PaidTo,ExpenseBy, DATE_FORMAT(ChequeDate, '%d-%m-%Y') as ChequeDate,Amount,TDSAmount, DATE_FORMAT(InvoiceDate, '%d-%m-%Y') as InvoiceDate,ChequeNumber,Bill_Type,Comments,ModeOfPayment,paymenttbl.id, DATE_FORMAT(VoucherDate, '%d-%m-%Y') as VoucherDate,`IsMultipleEntry`,`InvoiceAmount`,accounttbl.group_id from `paymentdetails` as paymenttbl join ledger as ledgertbl on ledgertbl.id = paymenttbl.PaidTo join `account_category` as accounttbl on accounttbl.category_id = ledgertbl.categoryid  where ChequeNumber='".$chqDetail[$iCnt]['cheque']."' and ChqLeafID='".$_REQUEST["LeafID"]."' ";			
		} 
		else
		{			
	  		$sql1 = "select PaidTo,ExpenseBy, DATE_FORMAT(ChequeDate, '%d-%m-%Y') as ChequeDate,Amount,TDSAmount, DATE_FORMAT(InvoiceDate, '%d-%m-%Y') as InvoiceDate,ChequeNumber,Bill_Type,Comments,ModeOfPayment,paymenttbl.id, DATE_FORMAT(VoucherDate, '%d-%m-%Y') as VoucherDate,`IsMultipleEntry`,`InvoiceAmount`, accounttbl.group_id from `paymentdetails` as paymenttbl join ledger as ledgertbl on ledgertbl.id = paymenttbl.PaidTo join `account_category` as accounttbl on accounttbl.category_id = ledgertbl.categoryid where paymenttbl.id='".$chqDetail[$iCnt]['cheque']."' and ChqLeafID='".$_REQUEST["LeafID"]."' ";
		}
		
		if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
		{
			$sql1 .= "  and VoucherDate BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
		}
		//echo $sql1;
		$Data = $obj_PaymentDetails->m_dbConn->select($sql1);
		
		if($_REQUEST["CustomLeaf"] != "0")
		{
			$key = array_search($Data[0]['ChequeNumber'], $MultipleEntryArr);		
			if($key !== false)
			{			
				continue;				
			}
			else if($Data[0]['IsMultipleEntry'] == 1)
			{		
				array_push($MultipleEntryArr, $Data[0]['ChequeNumber']);
			}
			
			$refQuery = "select `Reference` from paymentdetails where ChqLeafID='".$_REQUEST["LeafID"]."' And `id` = '".$Data[0]['id']."'";
			$iReference = $obj_PaymentDetails->m_dbConn->select($refQuery);
			if($iReference[0]['Reference'] <> 0)
			{
				$sql2 = "select PaidTo,ExpenseBy, DATE_FORMAT(ChequeDate, '%d-%m-%Y') as ChequeDate,Amount,TDSAmount, DATE_FORMAT(InvoiceDate, '%d-%m-%Y') as InvoiceDate,ChequeNumber,Bill_Type,Comments,ModeOfPayment,id,DATE_FORMAT(VoucherDate, '%d-%m-%Y') as VoucherDate,`IsMultipleEntry`,`InvoiceAmount` from `paymentdetails` as paymenttbl  where ChequeNumber='".$Data[0]['ChequeNumber']."' and ChqLeafID='".$_REQUEST["LeafID"]."' ";			
				$Data = $obj_PaymentDetails->m_dbConn->select($sql2);	
			}
		}		
		
		$reconcileStatus = $obj_PaymentDetails->m_dbConn->select("SELECT bank.ReconcileStatus, bank.Reconcile, bank.Return, DATE_FORMAT(bank.`Reconcile Date`, '%d-%m-%Y') as ReconcileDate FROM `bankregister` AS bank JOIN `voucher` AS voucher ON bank.VoucherID = voucher.id WHERE bank.ChkDetailID = '".$Data[0]['id']."' AND voucher.RefTableID = ". TABLE_PAYMENT_DETAILS);		
		
		$sqlVoucher = "select v.Note,v.Date, ins.NewInvoiceNo as InvoiceNo,ins.InvoiceStatusID, ins.AmountReceived as InvoceAmount,ins.TDSAmount,ins.CGST_Amount,ins.SGST_Amount,ins.IGST_Amount,ins.RoundOffAmount from `voucher` as v join `invoicestatus` as ins on v.VoucherNo=ins.InvoiceClearedVoucherNo where v.RefNo ='".$Data[0]['id']."' and `RefTableID` = '".TABLE_PAYMENT_DETAILS."' group by ins.InvoiceStatusID";
		 
		$data2 = $obj_PaymentDetails->m_dbConn->select($sqlVoucher);
		
		 $sqlVoucherNo= "select * from voucher where RefNo='".$Data[0]['id']."' and RefTableID='".TABLE_PAYMENT_DETAILS."'";
		 $Paymentvoucher = $obj_PaymentDetails->m_dbConn->select($sqlVoucherNo);
		 //var_dump($Paymentvoucher);
		 
		 if(sizeof($Paymentvoucher) > 2)
		 {
			 $show_in_jvformat = 1;
		 }
		 	
		for($i = 0; $i < sizeof($Data); $i++)
		{	if(sizeof($Paymentvoucher) > 0)
			{
				$Data[$i]['PaymentVoucherNo'] = $Paymentvoucher[0]['VoucherNo'];	
				$Data[$i]['ExternalVoucherNo'] = $Paymentvoucher[0]['ExternalCounter'];
				$Data[$i]['show_in_jvformat'] = $show_in_jvformat;	
					
			}
			if(sizeof($reconcileStatus) > 0)
			{			
				$Data[$i]['ReconcileStatus'] = $reconcileStatus[0]['ReconcileStatus'];
				$Data[$i]['Reconcile'] = $reconcileStatus[0]['Reconcile'];		
				$Data[$i]['Return'] = $reconcileStatus[0]['Return'];
				$Data[$i]['ReconcileDate'] = getDisplayFormatDate($reconcileStatus[0]['ReconcileDate']);
				
			}
				
										
			if(sizeof($data2) > 0)
			{
				$inviceDetails = array();
				for($iCntRow=0; $iCntRow<sizeof($data2);$iCntRow++)
				{
					$InvoiceNo=$data2[$iCntRow]['InvoiceNo'];
					$InvoiceID=$data2[$iCntRow]['InvoiceStatusID'];
			
				$sqlLegerName="SELECT ds.*, v.date,v.Note,l.ledger_name,l.id FROM `invoicestatus` as ds join `voucher` as v on ds.InvoiceRaisedVoucherNo=v.VoucherNo join ledger as l on v.By=l.id where InvoiceStatusID='".$InvoiceID."'";
				$data3 = $obj_PaymentDetails->m_dbConn->select($sqlLegerName);
				$data3[0]['date']=getDisplayFormatDate($data3[0]['date']);
				$data2[$iCntRow]['ExpenseDetails'] = $data3[0];
				}
				
				$Data[$i]['InvoiceData'] = json_encode($data2);
			}
			
			if($show_in_jvformat == 1)
			{
				$MultiLedgerData = array();
				$MultiLedgerCount = 0;
				$Paymentvoucher = array_reverse($Paymentvoucher);
				for($j = 0 ; $j < sizeof($Paymentvoucher);  $j++)
				{
					if(!empty($Paymentvoucher[$j]['By']))
					{
						$LedgerDetails = $objUtility->getParentOfLedger($Paymentvoucher[$j]['By']);
						$MultiLedgerData[$j]['By'] = $LedgerDetails['ledger_name'];	
						$MultiLedgerData[$j]['Debit'] = $Paymentvoucher[$j]['Debit'];
					}
					
					if(!empty($Paymentvoucher[$j]['To']))
					{
						$LedgerDetails = $objUtility->getParentOfLedger($Paymentvoucher[$j]['To']);
						$MultiLedgerData[$j]['To']  = $LedgerDetails['ledger_name'];
						$MultiLedgerData[$j]['Credit'] = $Paymentvoucher[$j]['Credit'];
					}
				}
				$Data[$i]['Multi_Ledger'] = json_encode($MultiLedgerData);
			}
			
			
		}
		
	  //print_r($Data);
		
		echo '^^' . $chqDetail[$iCnt]['no'] . '@@';
		foreach($Data as $k => $v)
		{
			echo '_//_';
			echo json_encode($v);								
			/*foreach($v as $kk => $vv)
			{
				echo $vv."#";
			}*/
		}	
	}
}

/*if($_REQUEST["mode"] == "test")
{
	$chqDetail = json_decode($_REQUEST['Cheque'], true);
	for($iCnt = 0 ; $iCnt < sizeof($chqDetail); $iCnt++)
	{
		$Data = $obj_PaymentDetails->m_dbConn->select("select PaidTo,ExpenseBy,ChequeDate,Amount,Comments,ChequeNumber from paymentdetails where ChequeNumber=".$chqDetail[$iCnt]['cheque']." and ChqLeafID=".$_REQUEST["LeafID"]);
		
		echo '^^' . $chqDetail[$iCnt]['no'] . '##';
		foreach($Data as $k => $v)
		{	
			foreach($v as $kk => $vv)
			{
				echo $vv."#";
			}
		}	
	}
	$leafID = $_REQUEST['LeafID'];
	//echo $chqDetail;
}*/
if(isset($_REQUEST["update"]))
{
	$obj_PaymentDetails->actionType= 1;
	$PaidchqDetail1 = json_decode(str_replace('\\', '', $_REQUEST['data']), true);
	//print_r($PaidchqDetail);
	$PaidchqDetail = sortArray($PaidchqDetail1);

	for($iCnt = 0 ; $iCnt < sizeof($PaidchqDetail); $iCnt++)
	{
		//$Data = $obj_PaymentDetails->m_dbConn->select("select PaidTo,ExpenseBy,ChequeDate,Amount,Comments,ChequeNumber from paymentdetails where ChequeNumber=".$chqDetail[$iCnt]['cheque']." and ChqLeafID=".$_REQUEST["LeafID"]);
		
		//print_r($PaidchqDetail[$iCnt]);
		//print_r($PaidchqDetail[$iCnt]["PaidTo"]);
		//echo '^^' . $PaidchqDetail[$iCnt]['no'] . '##';
		/*foreach($Data as $k => $v)
		{	
			foreach($v as $kk => $vv)
			{
				echo $vv."#";
			}
		}	*/
		$LeafID = $PaidchqDetail[$iCnt]["LeafID"];
		$SocietyID = $PaidchqDetail[$iCnt]["SocietyID"];
		$PaidTo = $PaidchqDetail[$iCnt]["PaidTo"];
		$CheqNumber = $PaidchqDetail[$iCnt]["ChequeNumber"];
		$BillType = $PaidchqDetail[$iCnt]["BillType"];
		$ChequeDate = $PaidchqDetail[$iCnt]["ChequeDate"];
		$Amount = $PaidchqDetail[$iCnt]["Amount"];
		$PayerBank = $PaidchqDetail[$iCnt]["PayerBank"];
		$Comments = $PaidchqDetail[$iCnt]["Comments"];
		$VoucherDate = $PaidchqDetail[$iCnt]["VoucherDate"];
		$ExpenseBy = $PaidchqDetail[$iCnt]["ExpenseBy"];
		$DoubleEntry = $PaidchqDetail[$iCnt]["DoubleEntry"];
		$InvoiceDate=$PaidchqDetail[$iCnt]["InvoiceDate"];
		$TDSAmount=$PaidchqDetail[$iCnt]["TDSAmount"];
		$ModeOfPayment=$PaidchqDetail[$iCnt]["ModeOfPayment"];
		$MultipleEntry = $PaidchqDetail[$iCnt]["MultipleEntry"];
		$Ref = $PaidchqDetail[$iCnt]['Ref'];
		$InvoiceAmount = $PaidchqDetail[$iCnt]['InvoiceAmount'];
		$voucherNumber = $PaidchqDetail[$iCnt]['VoucherNumber'];
		$systemDefineVNo = $PaidchqDetail[$iCnt]['systemDefineVNo'];
		$IsCallUpdtCnt = $PaidchqDetail[$iCnt]['IsCallUpdtCnt'];
			
		$strValues = $LeafID ."|". $SocietyID ."|". $PaidTo ."|". $CheqNumber ."|". $ChequeDate ."|".  $Amount ."|". $PayerBank ."|". $Comments ."|". $VoucherDate ."|". $ExpenseBy ."|".  $DoubleEntry ."|". $InvoiceDate ."|". $TDSAmount ."|". $ModeOfPayment ."|" . $MultipleEntry."|" .$InvoiceAmount. "|" .$BillType. '<br />';		
	        $result= $obj_PaymentDetails->AddNewValues($LeafID, $SocietyID, $PaidTo, $CheqNumber, $ChequeDate, $voucherNumber,$systemDefineVNo, $IsCallUpdtCnt, $Amount, $PayerBank, $Comments, $VoucherDate, $ExpenseBy, $DoubleEntry,$InvoiceDate,$TDSAmount,$ModeOfPayment,0,0,0,0,$MultipleEntry,$Ref,0,$InvoiceAmount,$PaymentVoucherNo,0,ADD,false,0,$BillType);	

		if($result == "-2")  // cheque number already exist in another leaf
		{ 
			echo "Cheque number ".$CheqNumber." already issued!";
		}
		else
		{
			//echo "1";
			//$obj_PaymentDetails->CommitTransaction();
		}	
	}
}
/*if($_REQUEST["method"]=="delete")
{
	$obj_PaymentDetails->deleting();
	return "Data Deleted Successfully";
}
*/



if($_REQUEST["method"]=="AddPaymentDetails")
{
	$trace_debug = 0;
	$obj_PaymentDetails->actionType = 4;
	$Detail = json_decode($_REQUEST['data'], true);
	
	$obj_PaymentDetails->BeginTransaction();
	$rowID = $_REQUEST['RowID'];
	
	//mysqli_autocommit($dbConn,false);
	
	if($trace_debug == 1)
	{
		echo '<pre>';
		print_r($Detail);
		echo '</pre>';
	}
	

	for($iCnt = 0 ; $iCnt < sizeof($Detail); $iCnt++)
	{   
		$PopupPayment=$Detail[$iCnt]['popupPayment'];
		$PaidTo=$Detail[$iCnt]['PaidTo'];
		$ChequeNumber=$Detail[$iCnt]['ChequeNumber'];
		$ChequeDate=$Detail[$iCnt]['ChequeDate'];
		$Amount=$Detail[$iCnt]['Amount'];
		$PayerBank=$Detail[$iCnt]['PayerBank'];
		$Comments=$Detail[$iCnt]['Comments'];
		$VoucherDate=$Detail[$iCnt]['VoucherDate'];
		$InvoiceDate=$Detail[$iCnt]['InvoiceDate'];
		$ExVoucherNumber=$Detail[$iCnt]['VoucherCounter'];
		$SystemDefineVNo=$Detail[$iCnt]['systemDefaultExternalNo'];
		$IsCallUpdtCnt=$Detail[$iCnt]['IsCallUpdtPaymentCnt'];
		$TDSAmount=0;
		$CGSTAmount =0;
		$SGSTAmount = 0;
		$IGSTAmount = 0;
		$RoundOffAmount =0;
		$LeafID=$Detail[$iCnt]['LeafID'];
		$DoubleEntry = 0;
		//ExpenseBy=$Detail[$iCnt]['ExpenseBy'];
		$ExpenseBy=0;
		$ModeOfPayment=$Detail[$iCnt]['ModeOfPayment'];
		//echo "modeof payment".$ModeOfPayment;
		
		$reconcleDate =$Detail[$iCnt]['reconcileDate'];
		$rStatus =$Detail[$iCnt]['recStatus'];
		$reconcile =$Detail[$iCnt]['reconcile'];
		$ExistPaymemtVoucher =$Detail[$iCnt]['ExistPaymentVoucher'];
		$returnFlag = 0;
		$MultipleEntry =0;
		$Ref = 0;		
		$InvoiceAmount = $Detail[$iCnt]['InvoiceAmount'];
		$PaymentVoucherNo = 0;
		 //$obj_PaymentDetails->UpdatePaymentDetails($PaidTo,$ChequeNumber,$ChequeDate,$Amount,$PayerBank,$Comments,$VoucherDate,$InvoiceDate,$TDSAmount,$LeafID,$DoubleEntry,$ExpenseBy, $rowID, $ModeOfPayment, $reconcleDate, $rStatus, $reconcile, $returnFlag, $MultipleEntry, $Ref,$InvoiceAmount,$PaymentVoucherNo );
		
		$allLedgerDetails = $objUtility->GetLedgerDetails();
		
		if($PopupPayment==1)
	 	{
			$InvoicesData=$Detail[$iCnt]['Invoices'];
			$ClearVoucherNo=$Detail[$iCnt]['ClearVoucherNo'];
		
			$selectTDS="select ds.`TDSVoucherNo`,v.id,v.ExternalCounter from `invoicestatus` as ds join `voucher` as v on ds.TDSVoucherNo=v.VoucherNo where `InvoiceClearedVoucherNo`='".$ClearVoucherNo."'";
			
			if($trace_debug == 1)
			{
				echo '<br>TDS : query '.$selectTDS;
			}
			
			$results1=$dbConn->select($selectTDS);
			
			if($trace_debug == 1)
			{
				echo "<br>Result1 ";
				echo '<pre>';
				print_r($results1);
				echo '</pre>';	
			}
			
			if($results1 <> '')
			 	{ 
				
				if($trace_debug == 1)
				{
					echo "<br>TDS Entry Found. Deleting TDS Entry from Voucher Table";
				}
				 for($iVCount=0; $iVCount < sizeof($results1);$iVCount++)
				 {
					$UpdateQuery="delete  from `voucher` where VoucherNo='".$results1[$iVCount]['TDSVoucherNo']."'";
					if($trace_debug == 1)
					{
						
						echo "<br>TDS Entry Delete From Voucher";
						echo "<br>DeleteQuery : ".$UpdateQuery;
					}
					
					$results2=$dbConn->delete($UpdateQuery);
			 		if($results1[$iVCount]['id'] > 0)
					{
				   		$UpdateQuery1="delete  from `liabilityregister` where VoucherID='".$results1[$iVCount]['id']."'";
						$results3=$dbConn->delete($UpdateQuery1);
					
						$UpdateQuery2="delete  from `expenseregister` where VoucherID='".$results1[$iVCount]['id']."'";
					 	$results3=$dbConn->delete($UpdateQuery2);
					 	$UpdateQuery3="delete  from `incomeregister` where VoucherID='".$results1[$iVCount]['id']."'";
					 	$results3=$dbConn->delete($UpdateQuery3);
					 	$UpdateQuery4="delete from `assetregister` where VoucherID='".$results1[$iVCount]['id']."'";
						$results3=$dbConn->delete($UpdateQuery4);
						
						if($trace_debug == 1)
						{
							echo "<br>Delete TDS Entry Delete From Registers";
							echo "<br> Liabilty : ".$UpdateQuery1;
							echo "<br> Expe : ".$UpdateQuery2;
							echo "<br> Income : ".$UpdateQuery3;
							echo "<br> Assest : ".$UpdateQuery4;
						}
						
					}
				}
				
				}
			$DeleteInvoiceDataString = $Detail[$iCnt]['deleteInvoicesVoucher'];
			
			if($trace_debug == 1)
			{
				echo "<br>Delete Invoice DataString ".$DeleteInvoiceDataString;
			}
			
			$DeteteInvoiceData = json_decode($DeleteInvoiceDataString,true);
			
			for($j = 0; $j < sizeof($DeteteInvoiceData); $j++)
			{
				$obj_PaymentDetails->DeletePaymentInvoice($DeteteInvoiceData[$j]['voucherNumber'],$ClearVoucherNo,$PayerBank,$LeafID);
			}
			
			 $UpdateQuery2="update `invoicestatus` set `InvoiceClearedVoucherNo`='',TDSVoucherNo='',AmountReceived='',TDSAmount='',TDSAmount='',CESS_Amount='' where `InvoiceClearedVoucherNo`='".$ClearVoucherNo."'";
			$results3=$dbConn->update($UpdateQuery2);
			$InvoicesData=json_decode($InvoicesData, true);
			
			if($trace_debug == 1)
			{
				echo '<br>Set Empty invoice Entry'.$UpdateQuery2	;
				
				echo "<br>Invoive Data";
				echo '<pre>';
				print_r($InvoicesData);
				echo '</pre>';
				
			}
			
		
			
			for($iRow=0; $iRow < sizeof($InvoicesData);$iRow++) 
			{ 			
			$InvoiceDate = $InvoicesData[$iRow]["InvoiceDate"];
			$InvoiceNumber = $InvoicesData[$iRow]["InvoiceNumber"];
			$ExpenceBy = $InvoicesData[$iRow]["ExpenceBy"];
			$InvoiceAmount = $InvoicesData[$iRow]["InvoiceAmount"];
			$TDSAmount = $InvoicesData[$iRow]["TDSAmount"];
			$CGSTAmount = $InvoicesData[$iRow]["CGSTAmount"];
			$SGSTAmount = $InvoicesData[$iRow]["SGSTAmount"];
				$IGSTAmount = $InvoicesData[$iRow]["IGSTAmount"];
			$CESSAmount = $InvoicesData[$iRow]["CESSAmount"];
			$TDSPayable = $InvoicesData[$iRow]["TDSPayable"];
			$IsInvoice = $InvoicesData[$iRow]["IsInvoice"];
			$DocStatusID = $InvoicesData[$iRow]["InvoiceStatusID"];
			$NewInvoice = $InvoicesData[$iRow]["NewInvoice"];
				$RoundOffAmount = $InvoicesData[$iRow]["RoundOffAmt"];
			$InvoiceExternalVoucherNo = $InvoicesData[$iRow]["InvoiceExternalVoucherNo"];
			$IsCallUpdtInvoiceCnt = $InvoicesData[$iRow]["IsCallUpdtInvoiceCnt"];
			
			if($NewInvoice==1)
			{
				
				$LatestVoucherNo = $m_latestcount->getLatestVoucherNo($_SESSION['society_id']);								
				$VoucherNo = $LatestVoucherNo;
				
				
					$obj_PaymentDetails->CreatePaymentToJVDetailsEx($ExpenceBy, $PaidTo, $InvoiceDate, $InvoiceAmount, $CGSTAmount,$SGSTAmount,VOUCHER_JOURNAL, $Comments, $VoucherNo,true,0,$InvoiceExternalVoucherNo,$RoundOffAmount,$IGSTAmount);
			//	$obj_PaymentDetails->CreatePaymentToJVDetailsEx($ExpenceBy, $PaidTo, $InvoiceDate, $InvoiceAmount, $CGSTAmount,$SGSTAmount,VOUCHER_JOURNAL, $Comments, $VoucherNo);
				
				//$obj_PaymentDetails->AddTDSDetailsEx($ExpenceBy, $PaidTo, $InvoiceDate, $InvoiceAmount, VOUCHER_JOURNAL, $Comments, $VoucherNo);
			 	//echo "Add TDSDetailsEX Details ";
				  $DocumentStatus="Insert into `invoicestatus`(`NewInvoiceNo`,`InvoiceChequeAmount`,`InvoiceRaisedVoucherNo`,`AmountReceivable`,`TDSAmount`,`IGST_Amount`,`CGST_Amount`,`SGST_Amount`,`CESS_Amount`,`is_invoice`,`RoundOffAmount`) values('".$InvoiceNumber."','".$InvoiceAmount."','".$VoucherNo."','".$InvoiceAmount."','".$TDSAmount."','".$IGSTAmount."','".$CGSTAmount."','".$SGSTAmount."','".$CESSAmount."','".$IsInvoice."','".$RoundOffAmount."')";
	$DocStatusID=$dbConn->insert($DocumentStatus);
				if($trace_debug == 1)
				{
					echo '<br>Adding New Invoice JV'.$UpdateQuery2	;
					echo '<br>Adding invoice Status '.$DocumentStatus;
					
				}

				if($IsCallUpdtInvoiceCnt == 1)
				{
					$objUtility->UpdateExVCounter(VOUCHER_JOURNAL,$InvoiceExternalVoucherNo,0);
				}

				$ExpenseLedgerName = $allLedgerDetails[$ExpenceBy]['General']['ledger_name'];

				$PaidToLedgerName = $allLedgerDetails[$PaidTo]['General']['ledger_name'];

				$BankName = $allLedgerDetails[$PayerBank]['General']['ledger_name'];

				$chequeLeaf = $objUtility->getLeftName($LeafID);


				$totalAmt = $InvoiceAmount + $CGSTAmount + $SGSTAmount;

				$dataArr = array('Date'=>$InvoiceDate, 'Voucher No'=>$VoucherNo, 'By Ledger'=>array($ExpenseLedgerName=>number_format($InvoiceAmount, 2)),'To Ledger'=>array($PaidToLedgerName=>number_format($InvoiceAmount, 2)), 'Amount'=>$totalAmt, 
							'Invoice'=>'Yes', 'Invoice No.'=>$InvoiceNumber, 'CGST Amount'=>$CGSTAmount, 'SGST Amount'=>$SGSTAmount, 'Note'=>$dbConn->escapeString($Comments), 'Bank'=> $BankName,'Cheque Leaf'=>$chequeLeaf,'Cheque Number'=> $ChequeNumber, 'External Voucher'=>$InvoiceExternalVoucherNo);
											
				$logArr = json_encode($dataArr);

				$changeLog->setLog($logArr, $_SESSION['login_id'], TABLE_JOURNAL_VOUCHER, $VoucherNo, ADD, 0);

				$InvoicesData[$iRow]["InvoiceStatusID"] = $DocStatusID;
			}
			
			if($TDSAmount <> '' && $TDSAmount <> 0)
			{
				
			$obj_PaymentDetails->AddTDSDetailsEx($PaidTo, $TDSPayable, $InvoiceDate, $TDSAmount, VOUCHER_JOURNAL, $Comments, $VoucherNo);
			$InvoicesData[$iRow]['TDSVoucherNo'] =  $VoucherNo;
				$TDSPayableLedgerName = $allLedgerDetails[$TDSPayable]['General']['ledger_name'];

				$dataArr = array('Date'=>$InvoiceDate, 'Voucher No'=>$VoucherNo, 'By Ledger'=>array($PaidToLedgerName=>number_format($TDSAmount, 2)),'To Ledger'=>array($TDSPayableLedgerName=>number_format($TDSAmount, 2)), 'Amount'=>$TDSAmount, 
							'Note'=>$dbConn->escapeString($Comments), 'Bank'=> $BankName,'Cheque Leaf'=>$chequeLeaf,'Cheque Number'=> $ChequeNumber);
				
				$logArr = json_encode($dataArr);

				$changeLog->setLog($logArr, $_SESSION['login_id'], TABLE_JOURNAL_VOUCHER, $VoucherNo, ADD, 0);
			}
			else
			{
				$InvoicesData[$iRow]['TDSVoucherNo'] =  0;
			}
			
		}
		
		$AllowCheckDelete = true;
		$bcash =false;
		if($LeafID == -1 && $ClearVoucherNo == -1)
		{
			//$AllowCheckDelete = false;
			$bcash= true;
		}
		
		 $res = $obj_PaymentDetails->UpdatePaymentDetails($PaidTo,$ChequeNumber,$ExVoucherNumber,$SystemDefineVNo,$IsCallUpdtCnt,$ChequeDate,$Amount,$PayerBank,$Comments,$VoucherDate,$InvoiceDate,$TDSAmount,$LeafID,$DoubleEntry,$ExpenseBy, $rowID, $ModeOfPayment, $reconcleDate, $rStatus, $reconcile, $returnFlag, $MultipleEntry, $Ref,$InvoiceAmount,$PaymentVoucherNo,$ExistPaymemtVoucher, true,$AllowCheckDelete,$bcash);
	
		for($iRow=0; $iRow < sizeof($InvoicesData);$iRow++) 
		{ 		
			if($trace_debug == 1)
			{
				echo "<br>Updatig Payment Entry";
			}
			$InvoiceNumber = $InvoicesData[$iRow]["InvoiceNumber"];
			$InvoiceDate = $InvoicesData[$iRow]["InvoiceDate"];
			$InvoiceAmount = $InvoicesData[$iRow]["InvoiceAmount"];
			$TDSAmount = $InvoicesData[$iRow]["TDSAmount"];
			$IGSTAmount = $InvoicesData[$iRow]["IGSTAmount"];
			$CGSTAmount = $InvoicesData[$iRow]["CGSTAmount"];
			$SGSTAmount = $InvoicesData[$iRow]["SGSTAmount"];
			$CESSAmount = $InvoicesData[$iRow]["CESSAmount"];
			$DocStatusID = $InvoicesData[$iRow]["InvoiceStatusID"];
			$VoucherNo = $InvoicesData[$iRow]["TDSVoucherNo"];
			$InvoiceRaisedVoucherNo = $InvoicesData[$iRow]["InvoiceRaisedVoucherNo"];
			$InvoiceExpenceBy = $InvoicesData[$iRow]["ExpenceBy"];
			$InvoiceExternalVoucherNo = $InvoicesData[$iRow]["InvoiceExternalVoucherNo"];
			$IsCallUpdtInvoiceCnt = $InvoicesData[$iRow]["IsCallUpdtInvoiceCnt"];

			$obj_PaymentDetails->UpdateInvoiceStatus_with_invoiceVoucher($PaymentVoucherNo,$InvoiceNumber,$VoucherNo,$InvoiceAmount, $TDSAmount,$DocStatusID,$IGSTAmount,$CGSTAmount,$SGSTAmount,$CESSAmount,$InvoiceRaisedVoucherNo,$PaidTo,$InvoiceExpenceBy,$InvoiceDate,$Comments, $InvoiceExternalVoucherNo, $IsCallUpdtInvoiceCnt);
		
			
		}
		
		if($trace_debug == 1)
		{
			echo "<br>Result ".$res;
		}
		
		if($res == "-2")  // cheque number already exist in another leaf
		{ 
			echo "0";
		}
		else
		{
			echo "1";
			$obj_PaymentDetails->CommitTransaction();
		}
		
	 }
	}
	
}	

function sortArray($array)
{
	foreach($array as $key=>$value){
        $arr_Ref[$key] = $array[$key]['Ref'];
        $arr_ME[$key] = $array[$key]['MultipleEntry'];
        }
	array_multisort($arr_Ref, SORT_ASC, $arr_ME, SORT_DESC,$array);	
	return $array;	
}
if($_REQUEST["method"]=="FetchGroupID")
{
	
	 $result=$objUtility->getParentOfLedgerGroup($_REQUEST['PaidTo']);
	if($result <> '')
	{
		
	 	$groupid = $result['group'] ;	
		
	}
	echo $groupid;
	
}
if($_REQUEST["method"]=="FetchTDSLedgerDetails")
{
	 $qry = "SELECT ledger_name FROM `ledger` where id='".$_REQUEST['ledgerId']."'";
	//$result=$dbConn->select($qry);
	$result = $obj_PaymentDetails->m_dbConn->select($qry);
	//print_r($result);
	if($result <> '')
	{
		
	 	$ledgername = $result[0]['ledger_name'] ;	
		
	}
	echo $ledgername;
	
}
if($_REQUEST["method"]=="AddTDSPaymentDetails")
{
	$trace_debug = 1;
	
	$PaidTo=$_REQUEST['PaidTo'];
	$ChallanDate=$_REQUEST['ChallanDate'];
	$TotalAmount=$_REQUEST['TotalAmount'];
	$AssesmentYear=$_REQUEST['AssesmentYear'];
	$YearID=$_REQUEST['YearID'];
	$Com_Deduct=$_REQUEST['Comp_deductees'];
	
	$Non_com_deduct=$_REQUEST['Comp_non_deductees'];
	$NatureOfTDS=$_REQUEST['Nature_of_TDS'];
	$TDS_TaxPayer=$_REQUEST['TDS_taxPayer'];
	$TDS_reg_Assess=$_REQUEST['TDS_reg_assess'];
	$from = $_REQUEST['from'];
	$to	  = $_REQUEST['to'];
	$BankID= $_REQUEST['BankID'];
	//echo "<br>PayerBank ".$PayerBank=$_REQUEST['PaybleData'];
	///var_dump($_REQUEST['PaybleData']);
	//$obj_PaymentDetails->actionType = 4;
	$Detail = json_decode($_REQUEST['PaybleData'], true);
	//var_dump($Detail);
	// $BankID= $Detail[0]['BankID'];
	
	//die();
	$updateStatus = 0;
	$ChallanInsert="Insert into `tds_challanregister`(`AssessmentYear`,`TaxYear`,`Company_Deductees`,`NonCompany_Deducteed`,`Challan_date`,`LedgerId`,`NatureOfTDS`,`BankId`,`Payable_Taxpayer`,`Reguler_Assessment`,`TotalAmount`,`from_date`,`to_date`) values('".$AssesmentYear."','".$YearID."','".$Com_Deduct."','".$Non_com_deduct."','".getDBFormatDate($ChallanDate)."','".$PaidTo."','".$NatureOfTDS."','".$BankID."','".$TDS_TaxPayer."','".$TDS_reg_Assess."','".str_replace(',', '' ,$TotalAmount)."','".getDBFormatDate($from)."','".getDBFormatDate($to)."')";
	//echo $ChallanInsert;
	//die();
	$ChallanInsertID=$dbConn->insert($ChallanInsert);
	$LogData = "";
	//echo "CHallan ID ".$ChallanInsertID;
	for($i= 0; $i<sizeof($Detail); $i++)
	{
		
		$VoucheID = $Detail[$i]["VoucherID"];
		$VoucheLedgerID = $Detail[$i]['VoucherLedgerID'];
		//$BankID= $Detail[$i]['BankId'];
		$UpdateStatus = "UPDATE `liabilityregister` SET `ChallanID`='".$ChallanInsertID."' where VoucherID ='".$VoucheID ."'";
		$result=$dbConn->update($UpdateStatus);
		
		if($result <> '' || $result <> 0)
		{
			$updateStatus=1;
		}
		else
		{
			$updateStatus = 0;
		}
		$LogData .= "Voucher ID :".$Detail[$i]["VoucherID"]." LadgerName :".$Detail[$i]["LedgerName"]. " Date :".$Detail[$i]["VDate"]. " Amount :".$Detail[$i]["Amount"].",";
	}
	$sql ="SELECT * FROM `bank_master` where `BankID`='".$BankID."'  "; 
	$bankDetails=$dbConn->select($sql);
	$dataArr = array('Challan Date'=> getDBFormatDate($ChallanDate), 'Challan No.'=>'', 'Amount'=> $TotalAmount,'Bank'=> $bankDetails[0]['BankName'], 'Date Range'=>getDBFormatDate($from).'to'.getDBFormatDate($to),'Details Data'=>$LogData,'Comments'=>'');
	

	 $logArr = json_encode($dataArr);
	 $changeLog->setLog($logArr, $_SESSION['login_id'], TABLE_TDSCHALLAN, $ChallanInsertID, ADD, 0);
	echo $updateStatus;
	//
	
}
if($_REQUEST["method"]=="Check")
{
	//echo "Test";
	$VenderGSTIN = 0;
	$firstTwoCharacters = '';
	$SocietyGSTINTwoCharacters = '';
	
	$SocietyGSTIN = $objUtility->GetSocietyInformation($_SESSION['society_id']);
	$SocietyGSTINTwoCharacters = substr($SocietyGSTIN['gstin_no'], 0, 2);
	
	$qry ="SELECT * FROM `ledger_details` where LedgerID = '".$_REQUEST['lId']."'";
	$res=$dbConn->select($qry);
	
	$firstTwoCharacters = substr($res[0]['GSTIN_No'], 0, 2);
	if($firstTwoCharacters == $SocietyGSTINTwoCharacters)
	{
		$VenderGSTIN = 1;// $res[0]['GSTIN_No'];  in State vendor
	}
	else if($res[0]['GSTIN_No'] == '')
	{
		$VenderGSTIN = 0; // Vendor GSTIN not updated 
	}
	else if($SocietyGSTIN['gstin_no'] =='')
	{
		$VenderGSTIN =3;  // Society GSTIN Not Updated
	}
	else
	{
		$VenderGSTIN = 2; // out state  state Vendor
	}
	echo $VenderGSTIN;
}
if($_REQUEST["method"]=="UpdateChallan")
{
	//echo "Test";
	$VenderGSTIN = 0;
	$firstTwoCharacters = '';
	$qry ="UPDATE `tds_challanregister` SET `ChallanNo`='".$_REQUEST['ChallanNo']."',`BSR_Code`='".$_REQUEST['BSR_code']."', `Comment`= '".$_REQUEST['Comment']."'  where id = '".$_REQUEST['ChallanId']."'";
	$res=$dbConn->update($qry);
	
	
	 
	if($res <> '' || $res <> 0)
		{
			$updateStatus=1;
		}
		else
		{
			$updateStatus = 0;
		}
	$sql = "SELECT * FROM  `tds_challanregister` where id = '".$_REQUEST['ChallanId']."'";
	$challanRes=$dbConn->select($sql);
	$ChallanDate = $challanRes[0]['Challan_date'];
	$TotalAmount = $challanRes[0]['TotalAmount'];
	$from = $challanRes[0]['from_date'];
	$to = $challanRes[0]['to_date'];
	
	$sql1 ="SELECT * FROM `bank_master` where `BankID`='".$challanRes[0]['BankId']."'  "; 
	$bankDetails=$dbConn->select($sql1);
	
	
	$dataArr = array('Challan Date'=> $ChallanDate, 'Challan No.'=>$_REQUEST['ChallanNo'], 'Amount'=> $TotalAmount,'Bank'=> $bankDetails[0]['BankName'], 'Date Range'=>$from.'to'.$to,'Details Data'=> '','Comments'=>$_REQUEST['Comment']);
	

	 $logArr = json_encode($dataArr);
	 $changeLog->setLog($logArr, $_SESSION['login_id'], TABLE_TDSCHALLAN, $_REQUEST['ChallanId'], EDIT, $_REQUEST['ChallanId']);	
	echo $updateStatus;
	
}
if($_REQUEST["method"]=="LoadChallan")
{
	$sql = "SELECT `ChallanNo`,`BSR_Code`,`Comment` FROM  `tds_challanregister` where id = '".$_REQUEST['ChallanId']."'";
	$challanRes=$dbConn->select($sql);
	echo json_encode($challanRes);
}
?>
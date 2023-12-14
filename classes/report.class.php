<?php
include_once("dbconst.class.php");
include_once("utility.class.php");

class report extends dbop
{
	public $m_dbConn;
	public $obj_utility;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->obj_utility = new utility($this->m_dbConn);
	}
	
	
	public function show_society_name($name)
	{
	  
	  $sql = "select society_name from society where society_id='".$name."'";
	  $data = $this->m_dbConn->select($sql);
	  return $data[0]['society_name'];
	
	}
	
	public function show_mem_due_details($from, $to, $wing, $BillType)
	{
		$memberIDS = $this->obj_utility->getMemberIDs(getDBFormatDate($to));	
		if($from == '')
		{
			$getPerod = "select yeartbl.BeginingDate from `period` as periodtbl JOIN `society` as societytbl on periodtbl.Billing_cycle = societytbl.bill_cycle  JOIN `year` as yeartbl on yeartbl.YearID=periodtbl.YearID where societytbl.society_id =".$_REQUEST["sid"]." and  yeartbl.YearID= ".$_SESSION['default_year']." ";
			//$period = $this->m_dbConn->select($getPerod);
			//$from =	$period[0]['BeginingDate'];
			
		}
		//$sql ="select wingtbl.wing as wing,wingtbl.wing_id as wing_id,membertbl.owner_name as member,membertbl.member_id as member_id, unittbl.unit_no as unit,sum(Debit)-sum(Credit) as amount, unittbl.unit_id as unit_id from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id JOIN society as societytbl on unittbl.society_id=societytbl.society_id where societytbl.society_id=".$_REQUEST["sid"]." and assettbl.Date Between '".getDBFormatDate($from)."' and '".getDBFormatDate($to)."'";
		
		$strBillType = "";
		$strChequeType = "";
		$strReturnBillType = "";
		$strPaymentBillType = "";
		//echo "BT:".$BillType;
		if($BillType ==  Maintenance || $BillType ==  Supplementry || $BillType == Invoice)  // Maintenance bill OR Supplementary bill
		{ 
			$strBillType = " and billdet.BillType = ". $BillType;
			$strBillTypeCrDrNote = " and CrDrNote.BillType = ". $BillType;
			$strChequeType = " and chqdet.BillType=". $BillType ."";
			$strPaymentBillType = " and payment.Bill_Type = ".$BillType."";

		}
		
		if($BillType == Maintenance || $BillType == Supplementry || $BillType == Combine_Bill){

			// BillDetail
			$sql = " SELECT * FROM(select unittbl.unit_id, membertbl.owner_name as member,membertbl.member_id as member_id, unittbl.unit_no as unit,(assettbl.Debit),(assettbl.Credit), assettbl.LedgerID as LedgerID, societytbl.society_id, assettbl.Date, assettbl.VoucherTypeID, unittbl.sort_order, wingtbl.wing, billdet.BillType, billdet.ID as base_id from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN `billdetails` as billdet on vchrtbl.RefNo=billdet.ID JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id JOIN `society` as societytbl on unittbl.society_id=societytbl.society_id where vchrtbl.RefTableID='".TABLE_BILLREGISTER."'" . $strBillType;

			if($BillType == Combine_Bill){

				// Invoice table
				$sql .=  " UNION ALL select unittbl.unit_id, membertbl.owner_name as member,membertbl.member_id as member_id, unittbl.unit_no as unit,(assettbl.Debit),(assettbl.Credit), assettbl.LedgerID as LedgerID, societytbl.society_id, assettbl.Date, assettbl.VoucherTypeID, unittbl.sort_order, wingtbl.wing, inv.BillType, inv.ID as base_id from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN `sale_invoice` as inv on vchrtbl.RefNo=inv.ID JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id JOIN `society` as societytbl on unittbl.society_id=societytbl.society_id where vchrtbl.RefTableID='".TABLE_SALESINVOICE."'";
				
				// Journal
				$sql .=  " UNION ALL select unittbl.unit_id, membertbl.owner_name as member,membertbl.member_id as member_id, unittbl.unit_no as unit,(assettbl.Debit),(assettbl.Credit), assettbl.LedgerID as LedgerID, societytbl.society_id, assettbl.Date, assettbl.VoucherTypeID, unittbl.sort_order, wingtbl.wing, 2 as BillType, vchrtbl.ID as base_id from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id JOIN `society` as societytbl on unittbl.society_id=societytbl.society_id where assettbl.VoucherTypeID ='".VOUCHER_JOURNAL."' AND RefTableID = 0";
			}
		}
		else if($BillType == Invoice){

			// Invoice table
			$sql = "SELECT * FROM(select unittbl.unit_id, membertbl.owner_name as member,membertbl.member_id as member_id, unittbl.unit_no as unit,(assettbl.Debit),(assettbl.Credit), assettbl.LedgerID as LedgerID, societytbl.society_id, assettbl.Date, assettbl.VoucherTypeID, unittbl.sort_order, wingtbl.wing, inv.BillType, inv.ID as base_id from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN `sale_invoice` as inv on vchrtbl.RefNo=inv.ID JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id JOIN `society` as societytbl on unittbl.society_id=societytbl.society_id where vchrtbl.RefTableID='".TABLE_SALESINVOICE."'"; 
		}	

		// CreditDebit
		$sql .= " UNION ALL select unittbl.unit_id, membertbl.owner_name as member,membertbl.member_id as member_id, unittbl.unit_no as unit,(assettbl.Debit),(assettbl.Credit), assettbl.LedgerID as LedgerID, societytbl.society_id, assettbl.Date, assettbl.VoucherTypeID, unittbl.sort_order, wingtbl.wing, CrDrNote.BillType, CrDrNote.ID as base_id from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN `credit_debit_note` as CrDrNote ON vchrtbl.RefNo = CrDrNote.ID JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id JOIN `society` as societytbl on unittbl.society_id=societytbl.society_id where vchrtbl.RefTableID='".TABLE_CREDIT_DEBIT_NOTE."'" . $strBillTypeCrDrNote; 
		
		// ChequeEntry 
		$sql .= " UNION ALL select unittbl.unit_id, membertbl.owner_name as member,membertbl.member_id as member_id, unittbl.unit_no as unit,(assettbl.Debit),(assettbl.Credit), assettbl.LedgerID as LedgerID, societytbl.society_id, assettbl.Date, assettbl.VoucherTypeID, unittbl.sort_order, wingtbl.wing, chqdet.BillType, chqdet.ID as base_id  from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN `chequeentrydetails` as chqdet on (vchrtbl.RefNo=chqdet.ID) JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id JOIN `society` as societytbl on unittbl.society_id=societytbl.society_id where vchrtbl.RefTableID='".TABLE_CHEQUE_DETAILS."'" . $strChequeType ;
		
		// Payment
		// $sql .= " UNION ALL select unittbl.unit_id, membertbl.owner_name as member,membertbl.member_id as member_id, unittbl.unit_no as unit,(assettbl.Debit),(assettbl.Credit), assettbl.LedgerID as LedgerID, societytbl.society_id, assettbl.Date, assettbl.VoucherTypeID, unittbl.sort_order, wingtbl.wing from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN (SELECT b2.ChkDetailID FROM `chequeentrydetails` as cheque JOIN bankregister as b1 ON cheque.ID = b1.ChkDetailID JOIN bankregister as b2 ON (b1.ID = b2.Ref AND b1.ReceivedAmount = b2.PaidAmount)  WHERE b1.`Return` = 1 and b1.VoucherTypeID = '".VOUCHER_RECEIPT."' and cheque.PaidBy in(SELECT id FROM ledger WHERE categoryid = '".DUE_FROM_MEMBERS."') ".$strReturnBillType." group by b1.ChkDetailID order by b2.ChkDetailID) as p ON p.ChkDetailID = vchrtbl.refNo JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id JOIN `society` as societytbl on unittbl.society_id=societytbl.society_id where vchrtbl.RefTableID='".TABLE_PAYMENT_DETAILS."'"; 
		
		$sql .= " UNION ALL select unittbl.unit_id, membertbl.owner_name as member,membertbl.member_id as member_id, unittbl.unit_no as unit,(assettbl.Debit),(assettbl.Credit), assettbl.LedgerID as LedgerID, societytbl.society_id, assettbl.Date, assettbl.VoucherTypeID, unittbl.sort_order, wingtbl.wing, p.BillType, p.ChkDetailID as base_id  from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN (SELECT b2.ChkDetailID, cheque.BillType FROM `chequeentrydetails` as cheque JOIN bankregister as b1 ON cheque.ID = b1.ChkDetailID JOIN bankregister as b2 ON (b1.ID = b2.Ref AND b1.ReceivedAmount = b2.PaidAmount)  WHERE b1.`Return` = 1 and b1.VoucherTypeID = '".VOUCHER_RECEIPT."' and cheque.PaidBy in(SELECT id FROM ledger WHERE categoryid = '".DUE_FROM_MEMBERS."') group by b1.ChkDetailID order by b2.ChkDetailID) as p ON p.ChkDetailID = vchrtbl.refNo JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id JOIN `society` as societytbl on unittbl.society_id=societytbl.society_id where vchrtbl.RefTableID='".TABLE_PAYMENT_DETAILS."'"; 
		
		$sql .= " UNION ALL select unittbl.unit_id, membertbl.owner_name as member,membertbl.member_id as member_id, unittbl.unit_no as unit,(assettbl.Debit),(assettbl.Credit), assettbl.LedgerID as LedgerID, societytbl.society_id, assettbl.Date, assettbl.VoucherTypeID, unittbl.sort_order, wingtbl.wing, payment.Bill_Type as BillType, payment.id as base_id from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN paymentdetails as payment ON (vchrtbl.RefNo = payment.id AND payment.id NOT IN(SELECT b2.ChkDetailID FROM `chequeentrydetails` as cheque JOIN bankregister as b1 ON cheque.ID = b1.ChkDetailID JOIN bankregister as b2 ON (b1.ID = b2.Ref AND b1.ReceivedAmount = b2.PaidAmount)  WHERE b1.`Return` = 1 and b1.VoucherTypeID = '".VOUCHER_RECEIPT."' and cheque.PaidBy in(SELECT id FROM ledger WHERE categoryid = '".DUE_FROM_MEMBERS."') group by b1.ChkDetailID order by b2.ChkDetailID)) JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id JOIN `society` as societytbl on unittbl.society_id=societytbl.society_id where vchrtbl.RefTableID='".TABLE_PAYMENT_DETAILS."' ".$strPaymentBillType." "; 
		
		$sql .= ") A where A.member_id IN (" . $memberIDS . ") and A.society_id='".$_REQUEST["sid"]."' and A.Date <= '".getDBFormatDate($to)."'";

		if($wing <> "")
		{
			$sql .= " and A.wing = '".$wing."'";
		}
		$sql .= " ORDER BY A.sort_order, A.Date";
		
		//echo $sql;
		$res = $this->m_dbConn->select($sql);
		
		//print_r($res);
		$finalArray = array();
		$PaymentIDs = array();

		for($iCount = 0; $iCount < sizeof($res); $iCount++)
		{
			if($res[$iCount]['BillType'] === NULL || $res[$iCount]['BillType'] === ""){
			
				$res[$iCount]['BillType'] = 0;
			}
			if($BillType != Combine_Bill && $res[$iCount]['VoucherTypeID'] == VOUCHER_PAYMENT && (in_array($res[$iCount]['base_id'], $PaymentIDs) || $BillType != $res[$iCount]['BillType'])){

				continue;
			}
			$amount = 0;
			$amount = $finalArray[$res[$iCount]['LedgerID']]['amount'] + $res[$iCount]['Debit'] - $res[$iCount]['Credit'];
			
			$finalArray[$res[$iCount]['LedgerID']] = $res[$iCount];
			$finalArray[$res[$iCount]['LedgerID']]['amount'] = $amount;

			//echo '<br>' . ($iCount + 1) . ' ' . $res[$iCount]['LedgerID'] . ' VoucherType : ' . $res[$iCount]['VoucherTypeID'] . ' : Debit : ' . $res[$iCount]['Debit'] . ' Credit : ' . $res[$iCount]['Credit'] . ' Total : ' . $finalArray[$res[$iCount]['LedgerID']]['amount'];
			if($BillType != Combine_Bill && $res[$iCount]['VoucherTypeID'] == VOUCHER_PAYMENT && !in_array($res[$iCount]['base_id'], $PaymentIDs)){

				array_push($PaymentIDs, $res[$iCount]['base_id']);
			}
		}

		$res = array();
		foreach($finalArray as $k => $v)
		{
			array_push($res, $v);
		}

		// getting all unit detail present in society
		$getUnitDetailsQry = "select membertbl.owner_name as member,membertbl.member_id as member_id, unittbl.unit_no as unit, societytbl.society_id, unittbl.sort_order,unittbl.unit_id, wingtbl.wing from `unit` as unittbl JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id JOIN `society` as societytbl on unittbl.society_id=societytbl.society_id where member_id IN(".$memberIDS.");";
		$unitListDetail = $this->m_dbConn->select($getUnitDetailsQry);
		$resultUnitList = array_column($res, 'unit_id');
		$resultCount = count($resultUnitList);
		
		if(!empty($unitListDetail))
		{
			$YearDetails =$this->obj_utility->getBeginningAndEndingDate($_SESSION["society_creation_yearid"]);
		
			for($i = 0;$i <= sizeof($unitListDetail)-1;$i++) // loop through each member and see does he/she have any opening balance
			{
				
				$Temp_Total = 0;
				
				if($BillType == Combine_Bill)
				{
					 $temp = $this->obj_utility->getOpeningBalance($unitListDetail[$i]['unit_id'],getDisplayFormatDate($YearDetails['BeginingDate']));	
					 $Temp_Total = $temp['Total'];
					 				
				}
				else if($BillType == Maintenance || $BillType == Supplementry || $BillType == Invoice)
				{
					 $temp = $this->obj_utility->getInceptionOpeningBalanceSplit($unitListDetail[$i]['unit_id']);
					 $Temp_Total = $temp[0]['TotalBillPayable'];
					 if($BillType == Supplementry)
					 {
						 $Temp_Total = $temp[0]['supp_TotalBillPayable'];
					 }
					 else if($BillType == Invoice)
					 {
						 $Temp_Total = $temp[0]['InvTotalBillPayable'];
					 }
				}

				 if(sizeof($temp) > 0)
				 {	
					 $index = -1;
					
					 if(in_array($unitListDetail[$i]['unit_id'], $resultUnitList)){ // if member has any opening balance and it also has some transaction the add final amount after calculation

						$index = array_search($unitListDetail[$i]['unit_id'], $resultUnitList);
						if($index !== -1 && $index !== false){

							if($temp['OpeningType'] == TRANSACTION_CREDIT)
							{
								$res[$index]['amount'] = $res[$index]['amount'] - $Temp_Total ;
							}
							else
							{
								$res[$index]['amount'] = $res[$index]['amount'] + $Temp_Total ;
							}
						}
					 }
					 else{ // if use does not have any transaction but has opening balance then add amount and others details in array to prepare report
 
						$res[$resultCount]['unit_id'] = $unitListDetail[$i]['unit_id'];
						$res[$resultCount]['member'] = $unitListDetail[$i]['member'];
						$res[$resultCount]['member_id'] = $unitListDetail[$i]['member_id'];
						$res[$resultCount]['unit'] = $unitListDetail[$i]['unit'];;
						$res[$resultCount]['Debit'] = ($temp['OpeningType'] == TRANSACTION_CREDIT)?0:$Temp_Total;
						$res[$resultCount]['Credit'] = ($temp['OpeningType'] == TRANSACTION_CREDIT)?$Temp_Total:0;
						$res[$resultCount]['wing'] = $unitListDetail[$i]['wing'];;
						$res[$resultCount]['Date'] = $YearDetails['BeginingDate']; 
						$res[$resultCount]['society_id'] = $_SESSION['society_id'];
						$res[$resultCount]['amount'] = ($temp['OpeningType'] == TRANSACTION_CREDIT) ? -$Temp_Total : $Temp_Total;
						$resultCount++;
					}
					 
				 }
			}
		}
		usort($res, function ($item1, $item2) { // sort unit with unit_id to show unit no sequentially 
			return $item1['unit_id'] > $item2['unit_id'];
		});
	
		return $res;
	}
	
	public function get_login_name($login_id)
	{
	 	$sql = "select member_id from `login` where login_id=".$login_id."";
	 	$res = $this->m_dbConn->select($sql);
		return $res[0]['member_id'];
		
	}
	
	
	
}
?>
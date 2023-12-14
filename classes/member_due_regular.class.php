<?php
include_once("dbconst.class.php");
include_once("utility.class.php");

class memberDuesRegular extends dbop
{
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_utility;
	public $ShowDebugTrace;
	private $testUnitID;
	
	function __construct($dbConn,$dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot= $dbConnRoot;
		$this->obj_utility = new utility($this->m_dbConn);
		$this->ShowDebugTrace = 0;
		$this->testUnitID = 52; //set to unit id you want to debug

	}
	
	public function getMemberDuesRegular($dues, $wing,$to,$BillType)
	{
		$memberIDS = $this->obj_utility->getMemberIDs(getDBFormatDate($to));
		
		$wingsCollection = array();
		$result = array();
		//$max_period = "select M_PeriodID from `society` where society_id=".$_SESSION['society_id']." ";
		//$max_period = "select `ID` as 'M_PeriodID' from `period` WHERE ('" . getDBFormatDate($to) . "' BETWEEN `BeginingDate` and `EndingDate`)";
		//$data=$this->m_dbConn->select($max_period);	
		if($BillType == 0 || $BillType== 1 )
		{
			$max_period = "SELECT MAX(PeriodID) as M_PeriodID FROM `billregister` WHERE `BillDate` <='".getDBFormatDate($to)."' and BillType=".$BillType;
			
		}
		else
		{
			$max_period = "SELECT MAX(PeriodID) as M_PeriodID FROM `billregister` WHERE `BillDate` <='".getDBFormatDate($to)."'";
		}
		$data=$this->m_dbConn->select($max_period);	
		
		$getPeriod ="select yeartbl.BeginingDate from `period` as periodtbl JOIN `society` as societytbl on periodtbl.Billing_cycle = societytbl.bill_cycle  JOIN `year` as yeartbl on yeartbl.YearID=periodtbl.YearID where societytbl.society_id =".$_REQUEST["sid"]." and  yeartbl.YearID= ".$_SESSION['default_year']." ";
		$period = $this->m_dbConn->select($getPeriod);
		$from = $period[0]['BeginingDate'];
		
		//$sql = "SELECT billregistertbl.BillDate,billtbl.UnitID,unittbl.unit_no,( billtbl.BillSubTotal + billtbl.PrincipalArrears ) as Principal,( billtbl.BillInterest + billtbl.InterestArrears ) as Interest,membertbl.owner_name FROM `billdetails` as billtbl JOIN `billregister` as billregistertbl on billregistertbl.id=billtbl.BillRegisterID JOIN `unit` as unittbl on billtbl.UnitID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id  where unittbl.society_id=".$_SESSION['society_id']." and billtbl.PeriodID=".$data[0]['M_PeriodID']."  and billregistertbl.BillDate BETWEEN '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."'   and membertbl.member_id IN (SELECT MAX(member_id) FROM `member_main` where ownership_date <='".getDBFormatDate($from)."' GROUP BY unit)";		
		 $sql = "SELECT billregistertbl.BillDate,billtbl.UnitID,unittbl.unit_no,( billtbl.BillSubTotal + billtbl.PrincipalArrears ) as Principal,( billtbl.BillInterest + billtbl.InterestArrears ) as Interest,( billtbl.CGST + billtbl.SGST + billtbl.IGST + billtbl.CESS) as GSTTax,membertbl.owner_name,membertbl.member_id FROM `billdetails` as billtbl JOIN `billregister` as billregistertbl on billregistertbl.id=billtbl.BillRegisterID JOIN `unit` as unittbl on billtbl.UnitID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id  where unittbl.society_id=".$_SESSION['society_id']." and billtbl.PeriodID=".$data[0]['M_PeriodID']." and billregistertbl.BillDate BETWEEN '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."'   and membertbl.member_id IN (".$memberIDS.")";
		 if($BillType ==0 || $BillType==1)
		 {
		  	$sql .= "and billtbl.BillType=".$BillType;	
		 }
		if($dues <> "")
		{
			$sql .= " HAVING Principal > ".$dues." ORDER BY unittbl.sort_order";	
		}								
		$res = $this->m_dbConn->select($sql); 
		
		$sqlWing = 'SELECT unittbl.unit_id, wingtbl.wing from `unit` as unittbl JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id';
		if($wing <> "")
		{
			$sqlWing .= ' WHERE wingtbl.wing = "'.$wing.'"';	
		}		
		$wing_details = $this->m_dbConn->select($sqlWing);
		for($i = 0; $i < sizeof($wing_details); $i++)
		{			
			$wingsCollection[$wing_details[$i]['unit_id']] = $wing_details[$i]['wing'];
		}
		
		for($i = 0; $i < sizeof($res); $i++)
		{
			if($wingsCollection[$res[$i]['UnitID']] <> "")
			{
				$final = array();
				$final['BillDate'] = $res[$i]['BillDate'];
				$final['UnitID'] = $res[$i]['UnitID'];
				$final['UnitNo'] = $res[$i]['unit_no'];
				$final['Principal'] = $res[$i]['Principal'];
				$final['Interest'] = $res[$i]['Interest'];
				$final['GSTTax'] = $res[$i]['GSTTax'];
				$final['owner_name'] = $res[$i]['owner_name'];
				$final['member_id'] = $res[$i]['member_id'];
				$final['Wing'] = $wingsCollection[$res[$i]['UnitID']];
				array_push($result, $final);				
			}
		}		 
		return $result;				
	}
				
	public function getAllPaymentDetails($uid,$billdate,$principal,$interest,$to,$BillType)
	{
		if($uid == $testUnitID && $this->ShowDebugTrace = 1)
		{		
			echo "<BR>0 principal : $principal ::  interest: $interest  - ::  Amount: $Amount<BR>";
			 
		}
		$todate = DateTime::createFromFormat('d-m-Y', $to);
		$dateTo =$todate->format('Y-m-d');
		$today = date("Y-m-d");
		$calulatedAmount = array();
		$calulatedTotalAmount = array();
		$sql = "SELECT VoucherDate,ChequeDate,ChequeNumber,sum(Amount) as 'Amount',PaidBy,IsReturn from `chequeentrydetails` where  PaidBy=".$uid." and IsReturn=0 and VoucherDate  between '".$billdate."' and '".$dateTo."' and BillTYpe='".$BillType."'";
		$res = $this->m_dbConn->select($sql);
/*
		if($uid == $testUnitID && $this->ShowDebugTrace = 1)
		{		
			echo "<BR> All payments<BR>". $sql;
			var_dump($res); 
		}
		*/
		$Amount = $res[0]['Amount'];

		// get credit Note details

		$creditNoteQry = "SELECT SUM(TotalPayable) as Amount FROM `credit_debit_note` WHERE UNITID = '$uid' AND Note_Type = '". CREDIT_NOTE."' AND Date BETWEEN '$billdate' AND '$dateTo'";
		$creditNoteDetails = $this->m_dbConn->select($creditNoteQry);
		$creditAmount = $creditNoteDetails[0]['Amount'];

		// get debit Note details
		$debitNoteQry = "SELECT SUM(TotalPayable) as Amount FROM `credit_debit_note` WHERE UNITID = '$uid' AND Note_Type = '". DEBIT_NOTE."' AND Date BETWEEN '$billdate' AND '$dateTo'";
		$debitNoteDetails = $this->m_dbConn->select($debitNoteQry);
		$debitAmount = $debitNoteDetails[0]['Amount'];
		
		
	 	$sql2="Select MAX(ChequeDate) as ChequeDate, MAX(VoucherDate) as VoucherDate from chequeentrydetails where PaidBy='".$uid."' and VoucherDate <= '".$dateTo."' and BillType='".$BillType."'";
		$res2 = $this->m_dbConn->select($sql2);
	 
		 $date=$res2[0]['ChequeDate'];
		 $Vdate=$res2[0]['VoucherDate'];
		//echo $Vdate;
		//echo $date;
		if($Vdate <> '')
		{
		 $days = $this->obj_utility->getDateDiff($dateTo, $Vdate);
		}
		else
		{
			$sql3="Select left(`locked`,10) as dt from dbname  where society_id='".$_SESSION['society_id']."'";
			$res3 = $this->m_dbConnRoot->select($sql3); 
			//echo $res3;
			$lockeDate=	$res3[0]['dt'];
			$date = DateTime::createFromFormat('m-d-Y', $lockeDate);
			$date = $date->format('Y-m-d');
			//echo $date;
			if($billdate <> '')
			{
				$days = $this->obj_utility->getDateDiff($dateTo, $billdate);

			}
			else
			{
				$days = $this->obj_utility->getDateDiff($dateTo, $date);

			}
						//echo 
			if($days > 0)
			{
				$days;
			}
			else
			{
				$days= 0;
			}
			
		}

		if($Amount > 0)
		{
			if($interest > 0)
			{
				//echo "Intrest ".$interest;
				if($Amount > $interest)
				{	
				//echo "<BR>If";					
					$Amount = $Amount - $interest;
					$interest = 0;					
				}
				else
				{	
				//echo "<BR>Else";				
					$interest = $interest - $Amount;
					$Amount = 0;					
				}
				
				//echo "<BR>principal" . $principal;
				//echo "<BR>interest" . $interest;
				//echo "<BR>Amount" . $Amount;
				//echo "<BR>";
				
				//$Amount = $interest-$Amount;
				if($Amount > 0 )
				{
					//$interest = 0;	
					$principal = $principal-$Amount;	
					
				}
				
			}
			else
			{
				
				$principal = $principal-$Amount;
				/*
				$calulatedAmount["interest"] = $interest;
				$calulatedAmount["principal"] = $principal;
				$calulatedAmount["ChequeDate"] = $date;
				$calulatedAmount["DiffDate"] = $days;
				array_push($calulatedTotalAmount,$calulatedAmount);
				*/
			}				
		}
		else
		{
			/*$calulatedAmount["interest"] = $interest;
			$calulatedAmount["principal"] = $principal;
			$calulatedAmount["ChequeDate"] = $date;
			$calulatedAmount["DiffDate"] = $days;
			array_push($calulatedTotalAmount,$calulatedAmount);*/
		}
		
		if($_SESSION['society_id'] <> 381 && $BillType <> 1)
		{
			
			if($creditAmount <> 0 && !empty($creditAmount)){
			
				$principal = $principal-$creditAmount;
			}

			if($debitAmount <> 0 && !empty($debitAmount)){
			
				$principal = $principal+$debitAmount;
			}
		}
		$total =$principal+$interest;
		//}
		if( $total<> 0)
		{
			$calulatedAmount["interest"] = $interest;
			$calulatedAmount["principal"] = $principal;
			$calulatedAmount["ChequeDate"] = $date;
			$calulatedAmount["DiffDate"] = $days;				
			array_push($calulatedTotalAmount,$calulatedAmount);
		}
	
	return $calulatedTotalAmount;
		
		
	}


	public function getWing($uid)
	{
		
		$sql = "select wingtbl.wing from `unit` as unittbl JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id where unittbl.unit_id=".$uid."  ";
		$data = $this->m_dbConn->select($sql);
		return $data[0]['wing'];	
		
	}
	
	function getNoticeTemplates($temp_id)
	{
		$selectTemplate= "SELECT * FROM `document_templates` where id ='".$temp_id."'";	
		$result = $this->m_dbConnRoot->select($selectTemplate); 
		return $result;
	}
	
}



?>
<?php
include_once("include/display_table.class.php");
include_once("utility.class.php");
include_once("dbconst.class.php");
include_once("ChequeDetails.class.php");

class view_ledger_details
{
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_utility;
	public $obj_chequeDetails;
	public $m_MemberArray = array();
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = new dbop(true);
		$this->display_pg = new display_table($this->m_dbConn);
		$this->obj_utility = new utility($this->m_dbConn, $this->m_dbConnRoot);
		$this->obj_chequeDetails = new ChequeDetails($this->m_dbConn);
		
		if(!isset($_REQUEST['to_date']))
		{
			$this->m_MemberArray = $this->obj_utility->getUnitData(0,getDBFormatDate($_SESSION['default_year_end_date']));
			
		}
		else
		{
			$this->m_MemberArray = $this->obj_utility->getUnitData(0,getDBFormatDate($_REQUEST['to_date']));	
			//var_dump($this->m_MemberArray);	
		}
	}

	public function details($gid,$lid, $from, $to,$bIsMultiple = false,$bIsMergeReport = false)
	{
		$categoryid=$this->obj_utility->getParentOfLedger($lid);
	 if($gid == 1)
	  {
		  if($from <> "" && $to <> "")
		  {
		  	$sql = "select ledgertbl.id, vouchertbl.Date, vouchertbl.RefNo, vouchertbl.RefTableID ,ledgertbl.ledger_name as Particular, liabilitytbl.Debit, liabilitytbl.Credit,VoucherID, ExternalCounter, liabilitytbl.VoucherTypeID,Is_Opening_Balance, acc.category_name as particular_category_name, liabilitytbl.ChallanID from `liabilityregister` as liabilitytbl JOIN `ledger` as ledgertbl on liabilitytbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid JOIN voucher as vouchertbl ON liabilitytbl.VoucherID = vouchertbl.id where liabilitytbl.LedgerID='".$lid."' AND liabilitytbl.Date BETWEEN '" .$from."' AND '".$to."' and Is_Opening_Balance = 0 ORDER BY Date ASC";	  
		  }
		  else
		  {
			 $sql = "select ledgertbl.id, vouchertbl.Date,vouchertbl.RefNo , vouchertbl.RefTableID,ledgertbl.ledger_name as Particular,liabilitytbl.Debit, liabilitytbl.Credit,VoucherID, ExternalCounter, liabilitytbl.VoucherTypeID,Is_Opening_Balance, acc.category_name as particular_category_name, liabilitytbl.ChallanID from `liabilityregister` as liabilitytbl JOIN `ledger` as ledgertbl on liabilitytbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid JOIN voucher as vouchertbl ON liabilitytbl.VoucherID = vouchertbl.id where liabilitytbl.LedgerID='".$lid."' and Is_Opening_Balance = 0";	   
		  	
			if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
		    {
				$sql .= "  and liabilitytbl.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
		    }
			
			$sql .= " ORDER BY Date ASC";
		  }
		  $data=$this->m_dbConn->select($sql);		  
	  }
	 else if($gid == 2)
	  {
		
		if($categoryid['category'] == BANK_ACCOUNT || $categoryid['category'] == CASH_ACCOUNT)
		{ 
			if($from <> "" && $to <> "")
		  	{
				$sql = "select ledgertbl.id, vouchertbl.Date, vouchertbl.RefNo , vouchertbl.RefTableID,ledgertbl.ledger_name as Particular,PaidAmount as Debit,ReceivedAmount as  Credit,VoucherID, ExternalCounter, banktbl.VoucherTypeID,Is_Opening_Balance, acc.category_name as particular_category_name from `bankregister` as banktbl JOIN `ledger` as ledgertbl on banktbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid  JOIN voucher as vouchertbl ON banktbl.VoucherID = vouchertbl.id where banktbl.LedgerID='".$lid."' AND banktbl.Date BETWEEN '" .$from."' AND '".$to."' and Is_Opening_Balance = 0 ORDER BY Date ASC";		  
			}
			else
			{
				$sql = "select ledgertbl.id, vouchertbl.Date, vouchertbl.RefNo , vouchertbl.RefTableID, ledgertbl.ledger_name as Particular,PaidAmount as Debit,ReceivedAmount as  Credit,VoucherID, ExternalCounter,banktbl.VoucherTypeID,Is_Opening_Balance, acc.category_name as particular_category_name from `bankregister` as banktbl JOIN `ledger` as ledgertbl on banktbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid  JOIN voucher as vouchertbl ON banktbl.VoucherID = vouchertbl.id where banktbl.LedgerID='".$lid."' and Is_Opening_Balance = 0";		  
				if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
				{
					$sql .= "  and banktbl.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
				}
				$sql .= " ORDER BY Date ASC";
			}
		}
		else
		{
			if($from <> "" && $to <> "")
		  	{				   
				$sql = "select ledgertbl.id, vouchertbl.Date, vouchertbl.RefNo , vouchertbl.RefTableID, ledgertbl.ledger_name as Particular, assettbl.Debit, assettbl.Credit,VoucherID, ExternalCounter, assettbl.VoucherTypeID,Is_Opening_Balance, acc.category_name as particular_category_name from `assetregister` as assettbl JOIN `ledger` as ledgertbl on assettbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid  JOIN voucher as vouchertbl ON assettbl.VoucherID = vouchertbl.id where assettbl.LedgerID='".$lid."' AND assettbl.Date BETWEEN '" .$from."' AND '".$to."' and Is_Opening_Balance = 0 ORDER BY Date ASC";				
			}
			else
			{
				$sql = "select ledgertbl.id,vouchertbl.Date, vouchertbl.RefNo , vouchertbl.RefTableID, ledgertbl.ledger_name as Particular, assettbl.Debit, assettbl.Credit,VoucherID, ExternalCounter, assettbl.VoucherTypeID,Is_Opening_Balance, acc.category_name as particular_category_name from `assetregister` as assettbl JOIN `ledger` as ledgertbl on assettbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid  JOIN voucher as vouchertbl ON assettbl.VoucherID = vouchertbl.id where assettbl.LedgerID='".$lid."' and Is_Opening_Balance = 0 ";					
				if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
				{
					$sql .= "  and assettbl.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
				}
				$sql .= " ORDER BY Date ASC";
			}
		}		
	  $data = $this->m_dbConn->select($sql);	
		
		if($categoryid['category'] == DUE_FROM_MEMBERS)
		{
			 /*  if($from <> "" && $to <> "")
			  {
					$memberIDS = $this->obj_utility->getMemberIDs($to);	
					$sql2="SELECT owner_name FROM `member_main` where `unit` = '".$lid."' and member_id IN (".$memberIDS.") Group BY unit ";
			  }
			  else
			  {
					$memberIDS = $this->obj_utility->getMemberIDs($_SESSION['default_year_end_date']);	
					$sql2="SELECT owner_name FROM `member_main` where `unit` = '".$lid."' and member_id IN (".$memberIDS.") Group BY unit ";  
			   }
				$res =  $this->m_dbConn->select($sql2);	
				$data[0]['owner_name'] =$res[0]['owner_name'];*/
				$data[0]['owner_name'] =$this->m_MemberArray[$data[0]['id']]['owner_name'];
		}
		  	 
	  }
	 else if($gid == 3)
	  {
		  if($bIsMultiple == true && $bIsMergeReport == true)
		  {
			   $sql="SELECT   ledgertbl.id,ledgertbl.ledger_name as Particular,VoucherID, ExternalCounter , incometbl.VoucherTypeID, SUM(incometbl.Credit) as Credit,SUM(incometbl.Debit) as Debit,monthname(incometbl.Date) as Date,incometbl.LedgerID, vouchertbl.RefNo , vouchertbl.RefTableID,acc.category_name as particular_category_name from `incomeregister` as incometbl JOIN `ledger` as ledgertbl on incometbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid JOIN voucher as vouchertbl ON incometbl.VoucherID = vouchertbl.id where incometbl.LedgerID='".$lid."' ";
			   if($from <> "" && $to <> "")
			   {
					$sql .="  and incometbl.Date BETWEEN '" .$from."' AND '".$to."' ";  
			   }
			   else if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
			   {
					$sql .= "  and incometbl.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
			   }
			   
			   $sql .="  GROUP BY incometbl.Date ASC";
		  }
		  else
		  {
			  	if($from <> "" && $to <> "")
				{
					$sql = "select ledgertbl.id, vouchertbl.Date,vouchertbl.RefNo , vouchertbl.RefTableID, ledgertbl.ledger_name as Particular, incometbl.Debit, incometbl.Credit,VoucherID, ExternalCounter , incometbl.VoucherTypeID, acc.category_name as particular_category_name from `incomeregister` as incometbl JOIN `ledger` as ledgertbl on incometbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid JOIN voucher as vouchertbl ON incometbl.VoucherID = vouchertbl.id where incometbl.LedgerID='".$lid."' AND incometbl.Date BETWEEN '" .$from."' AND '".$to."' ORDER BY Date ASC";	  
				}
				else
				{
					$sql = "select ledgertbl.id, vouchertbl.Date,vouchertbl.RefNo , vouchertbl.RefTableID, ledgertbl.ledger_name as Particular, incometbl.Debit, incometbl.Credit,VoucherID, ExternalCounter , incometbl.VoucherTypeID, acc.category_name as particular_category_name from `incomeregister` as incometbl JOIN `ledger` as ledgertbl on incometbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid JOIN voucher as vouchertbl ON incometbl.VoucherID = vouchertbl.id where incometbl.LedgerID='".$lid."' ";	  
					if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
					{
						$sql .= "  and incometbl.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
					}
					$sql .= " ORDER BY Date ASC";
				}
		  }
		  $data = $this->m_dbConn->select($sql);
	  }
	  else if($gid == 4)
	  {
		  if($from <> "" && $to <> "")
			{
	   			$sql = "select ledgertbl.id, vouchertbl.Date, vouchertbl.RefNo, vouchertbl.RefTableID ,ledgertbl.ledger_name as Particular, expensetbl.Debit, expensetbl.Credit,VoucherID, ExternalCounter, expensetbl.VoucherTypeID, acc.category_name as particular_category_name from `expenseregister` as expensetbl JOIN `ledger` as ledgertbl on expensetbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid JOIN voucher as vouchertbl ON expensetbl.VoucherID = vouchertbl.id where expensetbl.LedgerID='".$lid."'  AND expensetbl.Date BETWEEN '" .$from."' AND '".$to."' ORDER BY Date ASC";	 
			}
			else
			{
				$sql = "select ledgertbl.id, vouchertbl.Date, vouchertbl.RefNo, vouchertbl.RefTableID ,ledgertbl.ledger_name as Particular, expensetbl.Debit, expensetbl.Credit, VoucherID, ExternalCounter,expensetbl.VoucherTypeID, acc.category_name as particular_category_name from `expenseregister` as expensetbl JOIN `ledger` as ledgertbl on expensetbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid JOIN voucher as vouchertbl ON expensetbl.VoucherID = vouchertbl.id where expensetbl.LedgerID='".$lid."' ";	  	
				if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
				{
					$sql .= "  and expensetbl.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
				}
				$sql .= " ORDER BY Date ASC";
			}
	  	$data = $this->m_dbConn->select($sql);
	 }
	 
	
	
	if($bIsMultiple == true)
	{
		 for($k = 0 ; $k< sizeof($data) ; $k++)
		 {
			 	$DebitAmt = $data[$k]['Debit'];
				$CreditAmt = $data[$k]['Credit'];
				
				$ByORTo = '';
				if($data[$k]['VoucherTypeID'] ==VOUCHER_PAYMENT || $data[$k]['VoucherTypeID']==VOUCHER_RECEIPT){

					if($DebitAmt <> 0 || ($DebitAmt == 0 && $CreditAmt == 0))
					{
						//$data[$k]['ParticularLedgerName'] = $this->get_voucher_details($data[$k]['VoucherTypeID'],$data[$k]['VoucherID'],$lid,$gid, "To");
						$ByORTo = 'By';
					}
					else
					{
						//$data[$k]['ParticularLedgerName'] = $this->get_voucher_details($data[$k]['VoucherTypeID'],$data[$k]['VoucherID'],$lid,$gid, "By");
						$ByORTo = 'To';
					}
				}
				else
				{ 
					if($DebitAmt <> 0)
					{
						//$data[$k]['ParticularLedgerName'] = $this->get_voucher_details($data[$k]['VoucherTypeID'],$data[$k]['VoucherID'],$lid,$gid, "To");
						$ByORTo = 'To';
					}
					else
					{
						//$data[$k]['ParticularLedgerName'] = $this->get_voucher_details($data[$k]['VoucherTypeID'],$data[$k]['VoucherID'],$lid,$gid, "By");
						$ByORTo = 'By';
					}
				}
				
				$resdata = $this->get_voucher_details($data[$k]['VoucherTypeID'],$data[$k]['VoucherID'],$lid,$gid, $ByORTo);
				
				if(!empty($resdata[0]['ledger_id']))
				{
					$LedgerDetails = $this->obj_utility->getParentOfLedger($resdata[0]['ledger_id']);	
				}
				
				//var_dump($LedgerDetails);
				
				$data[$k]['category_name'] = $LedgerDetails['category_name'];
				$data[$k]['group_name'] = $LedgerDetails['group_name'];
				$data[$k]['ParticularLedgerName'] = $resdata[0]['ledger_name'];
				$data[$k]['ChequeNumber'] = $resdata[0]['ChequeNumber'];
				$data[$k]['Note'] = $resdata[0]['Note'];
				$data[$k]['VoucherNo'] = $resdata[0]['VoucherNo'];
				$data[$k]['VoucherType'] = $resdata[0]['VoucherType'];
				$data[$k]['RefTableID'] = $resdata[0]['RefTableID'];
				$data[$k]['LoginID'] = $resdata[0]['LoginID'];
				$data[$k]['Timestamp'] = $resdata[0]['Timestamp'];

				if($data[$k]['ChequeNumber'] == -1)
				{
					$data[$k]['ChequeNumber'] = 'Cash';	
				}
				else if($data[$k]['ChequeNumber'] == "")
				{
					$data[$k]['ChequeNumber'] = '-';	
				}
					
				/*if($data[$k]['VoucherTypeID']  <> 0)
				{
					if($data[$k]['VoucherTypeID'] == VOUCHER_JOURNAL)
					{
							$data[$k]['VoucherType']	 = 'Journal Voucher';
					}
					else if($data[$k]['VoucherTypeID'] == VOUCHER_PAYMENT)
					{
							$data[$k]['VoucherType']	 = 'Payment Voucher';
					}
					else if($data[$k]['VoucherTypeID'] == VOUCHER_RECEIPT)
					{
							$data[$k]['VoucherType']	 = 'Receipt Voucher';	
					}
					else if($data[$k]['VoucherTypeID'] == VOUCHER_CONTRA)
					{
							$data[$k]['VoucherType']	 = 'Contra Voucher';	
					}
					else if($data[$k]['VoucherTypeID'] == VOUCHER_SALES)
					{
							$data[$k]['VoucherType']	 = 'Sales Voucher';	
					}
				}
				else
				{
					$data[$k]['VoucherType']	 = '';		
				}*/
		}	
	}
	//echo "<pre>";
		//print_r($data);
		//echo "</pre>";
	   return $data;
	}	
	
	public function details1($gid,$lid, $from, $to,$bIsMultiple = false,$bIsMergeReport = false)
	{
		//echo "inside function";
		$categoryid=$this->obj_utility->getParentOfLedger($lid);
	 if($gid == 1)
	  {
		  if($from <> "" && $to <> "")
		  {
		  	$sql = "select ledgertbl.id, vouchertbl.Date, vouchertbl.RefNo, vouchertbl.RefTableID ,ledgertbl.ledger_name as Particular, liabilitytbl.Debit, liabilitytbl.Credit,VoucherID, ExternalCounter, liabilitytbl.VoucherTypeID,Is_Opening_Balance, acc.category_name as particular_category_name, liabilitytbl.ChallanID from `liabilityregister` as liabilitytbl JOIN `ledger` as ledgertbl on liabilitytbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid JOIN voucher as vouchertbl ON liabilitytbl.VoucherID = vouchertbl.id where liabilitytbl.LedgerID='".$lid."' AND liabilitytbl.Date BETWEEN '" .$from."' AND '".$to."' and Is_Opening_Balance = 0 ORDER BY Date ASC";	  
		  }
		  else
		  {
			 $sql = "select ledgertbl.id, vouchertbl.Date,vouchertbl.RefNo , vouchertbl.RefTableID,ledgertbl.ledger_name as Particular,liabilitytbl.Debit, liabilitytbl.Credit,VoucherID, ExternalCounter, liabilitytbl.VoucherTypeID,Is_Opening_Balance, acc.category_name as particular_category_name, liabilitytbl.ChallanID from `liabilityregister` as liabilitytbl JOIN `ledger` as ledgertbl on liabilitytbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid JOIN voucher as vouchertbl ON liabilitytbl.VoucherID = vouchertbl.id where liabilitytbl.LedgerID='".$lid."' and Is_Opening_Balance = 0";	   
		  	
			if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
		    {
				$sql .= "  and liabilitytbl.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
		    }
			
			$sql .= " ORDER BY Date ASC";
		  }
		  $data=$this->m_dbConn->select($sql);		  
	  }
	 else if($gid == 2)
	  {
		
		if($categoryid['category'] == BANK_ACCOUNT || $categoryid['category'] == CASH_ACCOUNT)
		{ 
			if($from <> "" && $to <> "")
		  	{
				$sql = "select ledgertbl.id, vouchertbl.Date, vouchertbl.RefNo , vouchertbl.RefTableID,ledgertbl.ledger_name as Particular,PaidAmount as Debit,ReceivedAmount as  Credit,VoucherID, ExternalCounter, banktbl.VoucherTypeID,Is_Opening_Balance, acc.category_name as particular_category_name from `bankregister` as banktbl JOIN `ledger` as ledgertbl on banktbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid  JOIN voucher as vouchertbl ON banktbl.VoucherID = vouchertbl.id where banktbl.LedgerID='".$lid."' AND banktbl.Date BETWEEN '" .$from."' AND '".$to."' and Is_Opening_Balance = 0 ORDER BY Date ASC";		  
			}
			else
			{
				$sql = "select ledgertbl.id, vouchertbl.Date, vouchertbl.RefNo , vouchertbl.RefTableID, ledgertbl.ledger_name as Particular,PaidAmount as Debit,ReceivedAmount as  Credit,VoucherID, ExternalCounter,banktbl.VoucherTypeID,Is_Opening_Balance, acc.category_name as particular_category_name from `bankregister` as banktbl JOIN `ledger` as ledgertbl on banktbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid  JOIN voucher as vouchertbl ON banktbl.VoucherID = vouchertbl.id where banktbl.LedgerID='".$lid."' and Is_Opening_Balance = 0";		  
				if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
				{
					$sql .= "  and banktbl.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
				}
				$sql .= " ORDER BY Date ASC";
			}
		}
		else
		{
			if($from <> "" && $to <> "")
		  	{				   
				$sql = "select ledgertbl.id, vouchertbl.Date, vouchertbl.RefNo , vouchertbl.RefTableID, ledgertbl.ledger_name as Particular, assettbl.Debit, assettbl.Credit,VoucherID, ExternalCounter, assettbl.VoucherTypeID,Is_Opening_Balance, acc.category_name as particular_category_name from `assetregister` as assettbl JOIN `ledger` as ledgertbl on assettbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid  JOIN voucher as vouchertbl ON assettbl.VoucherID = vouchertbl.id where assettbl.LedgerID='".$lid."' AND assettbl.Date BETWEEN '" .$from."' AND '".$to."' and Is_Opening_Balance = 0 ORDER BY Date ASC";				
			}
			else
			{
				$sql = "select ledgertbl.id,vouchertbl.Date, vouchertbl.RefNo , vouchertbl.RefTableID, ledgertbl.ledger_name as Particular, assettbl.Debit, assettbl.Credit,VoucherID, ExternalCounter, assettbl.VoucherTypeID,Is_Opening_Balance, acc.category_name as particular_category_name from `assetregister` as assettbl JOIN `ledger` as ledgertbl on assettbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid  JOIN voucher as vouchertbl ON assettbl.VoucherID = vouchertbl.id where assettbl.LedgerID='".$lid."' and Is_Opening_Balance = 0 ";					
				if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
				{
					$sql .= "  and assettbl.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
				}
				$sql .= " ORDER BY Date ASC";
			}
		}		
	  $data = $this->m_dbConn->select($sql);	
		
		if($categoryid['category'] == DUE_FROM_MEMBERS)
		{
				$data[0]['owner_name'] =$this->m_MemberArray[$data[0]['id']]['owner_name'];
		}
		  	 
	  }
	 else if($gid == 3)
	  {
		  if($bIsMultiple == true && $bIsMergeReport == true)
		  {
			   $sql="SELECT   ledgertbl.id,ledgertbl.ledger_name as Particular,VoucherID, ExternalCounter , incometbl.VoucherTypeID, SUM(incometbl.Credit) as Credit,SUM(incometbl.Debit) as Debit,monthname(incometbl.Date) as Date,incometbl.LedgerID, vouchertbl.RefNo , vouchertbl.RefTableID,acc.category_name as particular_category_name from `incomeregister` as incometbl JOIN `ledger` as ledgertbl on incometbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid JOIN voucher as vouchertbl ON incometbl.VoucherID = vouchertbl.id where incometbl.LedgerID='".$lid."' ";
			   if($from <> "" && $to <> "")
			   {
					$sql .="  and incometbl.Date BETWEEN '" .$from."' AND '".$to."' ";  
			   }
			   else if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
			   {
					$sql .= "  and incometbl.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
			   }
			   
			   $sql .="  GROUP BY incometbl.Date ASC";
		  }
		  else
		  {
			  	if($from <> "" && $to <> "")
				{
					$sql = "select ledgertbl.id, vouchertbl.Date,vouchertbl.RefNo , vouchertbl.RefTableID, ledgertbl.ledger_name as Particular, incometbl.Debit, incometbl.Credit,VoucherID, ExternalCounter , incometbl.VoucherTypeID, acc.category_name as particular_category_name from `incomeregister` as incometbl JOIN `ledger` as ledgertbl on incometbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid JOIN voucher as vouchertbl ON incometbl.VoucherID = vouchertbl.id where incometbl.LedgerID='".$lid."' AND incometbl.Date BETWEEN '" .$from."' AND '".$to."' ORDER BY Date ASC";	  
				}
				else
				{
					$sql = "select ledgertbl.id, vouchertbl.Date,vouchertbl.RefNo , vouchertbl.RefTableID, ledgertbl.ledger_name as Particular, incometbl.Debit, incometbl.Credit,VoucherID, ExternalCounter , incometbl.VoucherTypeID, acc.category_name as particular_category_name from `incomeregister` as incometbl JOIN `ledger` as ledgertbl on incometbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid JOIN voucher as vouchertbl ON incometbl.VoucherID = vouchertbl.id where incometbl.LedgerID='".$lid."' ";	  
					if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
					{
						$sql .= "  and incometbl.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
					}
					$sql .= " ORDER BY Date ASC";
				}
		  }
		  $data = $this->m_dbConn->select($sql);
	  }
	  else if($gid == 4)
	  {
		  if($from <> "" && $to <> "")
			{
	   			$sql = "select ledgertbl.id, vouchertbl.Date, vouchertbl.RefNo, vouchertbl.RefTableID ,ledgertbl.ledger_name as Particular, expensetbl.Debit, expensetbl.Credit,VoucherID, ExternalCounter, expensetbl.VoucherTypeID, acc.category_name as particular_category_name from `expenseregister` as expensetbl JOIN `ledger` as ledgertbl on expensetbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid JOIN voucher as vouchertbl ON expensetbl.VoucherID = vouchertbl.id where expensetbl.LedgerID='".$lid."'  AND expensetbl.Date BETWEEN '" .$from."' AND '".$to."' ORDER BY Date ASC";	 
			}
			else
			{
				$sql = "select ledgertbl.id, vouchertbl.Date, vouchertbl.RefNo, vouchertbl.RefTableID ,ledgertbl.ledger_name as Particular, expensetbl.Debit, expensetbl.Credit, VoucherID, ExternalCounter,expensetbl.VoucherTypeID, acc.category_name as particular_category_name from `expenseregister` as expensetbl JOIN `ledger` as ledgertbl on expensetbl.LedgerID=ledgertbl.id JOIN `account_category` as acc ON acc.category_id = ledgertbl.categoryid JOIN voucher as vouchertbl ON expensetbl.VoucherID = vouchertbl.id where expensetbl.LedgerID='".$lid."' ";	  	
				if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
				{
					$sql .= "  and expensetbl.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
				}
				$sql .= " ORDER BY Date ASC";
				
			}
			//echo "<br><br>".$sql;
	  	$data = $this->m_dbConn->select($sql);
	 }
	 
	
	
	if($bIsMultiple == true)
	{
		 for($k = 0 ; $k< sizeof($data) ; $k++)
		 {
			 	
			 	$DebitAmt = $data[$k]['Debit'];
				$CreditAmt = $data[$k]['Credit'];
				
				$ByORTo = '';
				if($data[$k]['VoucherTypeID'] ==VOUCHER_PAYMENT || $data[$k]['VoucherTypeID']==VOUCHER_RECEIPT){

					if($DebitAmt <> 0 || ($DebitAmt == 0 && $CreditAmt == 0))
					{
						//$data[$k]['ParticularLedgerName'] = $this->get_voucher_details($data[$k]['VoucherTypeID'],$data[$k]['VoucherID'],$lid,$gid, "To");
						$ByORTo = 'By';
					}
					else
					{
						//$data[$k]['ParticularLedgerName'] = $this->get_voucher_details($data[$k]['VoucherTypeID'],$data[$k]['VoucherID'],$lid,$gid, "By");
						$ByORTo = 'To';
					}
				}
				else
				{ 
					if($DebitAmt <> 0)
					{
						//$data[$k]['ParticularLedgerName'] = $this->get_voucher_details($data[$k]['VoucherTypeID'],$data[$k]['VoucherID'],$lid,$gid, "To");
						$ByORTo = 'To';
					}
					else
					{
						//$data[$k]['ParticularLedgerName'] = $this->get_voucher_details($data[$k]['VoucherTypeID'],$data[$k]['VoucherID'],$lid,$gid, "By");
						$ByORTo = 'By';
					}
				}
				
				$resdata = $this->get_voucher_details($data[$k]['VoucherTypeID'],$data[$k]['VoucherID'],$lid,$gid, $ByORTo);
				
				if(!empty($resdata[0]['ledger_id']))
				{
					$LedgerDetails = $this->obj_utility->getParentOfLedger($resdata[0]['ledger_id']);	
				}
				
					
					$data[$k]['ParticularLedgerID'] = $resdata[0]['ledger_id'];
					$data[$k]['category_name'] = $LedgerDetails['category_name'];
					$data[$k]['group_name'] = $LedgerDetails['group_name'];
					$data[$k]['ParticularLedgerName'] = $resdata[0]['ledger_name'];
					$data[$k]['ChequeNumber'] = $resdata[0]['ChequeNumber'];
					$data[$k]['Note'] = $resdata[0]['Note'];
					$data[$k]['VoucherNo'] = $resdata[0]['VoucherNo'];
					$data[$k]['VoucherType'] = $resdata[0]['VoucherType'];
					$data[$k]['RefTableID'] = $resdata[0]['RefTableID'];
					$data[$k]['LoginID'] = $resdata[0]['LoginID'];
					$data[$k]['Timestamp'] = $resdata[0]['Timestamp'];
					//$data[$k]['Date'] ='Various';
	
					if($data[$k]['ChequeNumber'] == -1)
					{
						$data[$k]['ChequeNumber'] = 'Cash';	
					}
					else if($data[$k]['ChequeNumber'] == "")
					{
						$data[$k]['ChequeNumber'] = '-';	
					}
					$ledger_id[$k] = $resdata[0]['ledger_id'];
			
		}	
	}
	
	$ledger_ids = array_unique($ledger_id);
	foreach($ledger_ids as $kid => $id)
	{
		$cont= 0;
		foreach($data as $key => $val)
		{
			if($val['ParticularLedgerID'] === $id)
			{
				$cont++;
				$updata[$id]['Debit'] += $val['Debit'];
				$updata[$id]['Credit'] += $val['Credit'];
				if($cont > 1)
				{
					$updata[$id]['Date'] ='Various';
				}
				else
				{
					$updata[$id]['Date'] =$val['Date'];
				}
				foreach($val as $k => $v)
				{
					if($k != 'Debit' && $k != 'Credit' && $k != 'Date')
					{
						$updata[$id][$k] = $v;
					}
				}
			}
		}
		//echo "Count".$cont; 
		$cont=0;
	}
	$data = array_values($updata);
	return $data;
	}	
	
public function details2($lid,$vid,$vtype)
{
		$Type = '';
		$sql2 = "select `id`,`Date`, VoucherNo, ExternalCounter, `VoucherTypeID`, `RefNo`, `RefTableID` from `voucher` where id='".$vid."' ";
		$data2 = $this->m_dbConn->select($sql2);

		//set variable for logView

		$logTable = $data2[0]['RefTableID'];
		$logRef = $data2[0]['RefNo'];
		
		if($data2[0]['VoucherTypeID'] == VOUCHER_JOURNAL)
		{
			$Type ='Journal Voucher';	
			
			if($data2[0]['RefTableID'] == 0){ // for JV we don't have any table but in change log table we are storing 10 as TableRef 

				$logTable = TABLE_JOURNAL_VOUCHER;
				$logRef = $data2[0]['VoucherNo'];
			}
			else
			{
				$logTable = TABLE_JOURNAL_VOUCHER;
				$logRef = $data2[0]['VoucherNo'];
			}
				
		}
		else if($data2[0]['VoucherTypeID'] == VOUCHER_PAYMENT)
		{
				$Type ='Payment Voucher';		
		}
		else if($data2[0]['VoucherTypeID'] == VOUCHER_RECEIPT)
		{
				$Type ='Receipt Voucher';		
		}
		else if($data2[0]['VoucherTypeID'] == VOUCHER_SALES)
		{
				$Type ='Sales Voucher';		
		}
		else if($data2[0]['VoucherTypeID'] == VOUCHER_CONTRA)
		{
				$Type ='Contra Voucher';		
		}
		else if($data2[0]['VoucherTypeID'] == VOUCHER_CREDIT_NOTE)
		{
				$Type ='Credit Note Voucher';		
		}
		else if($data2[0]['VoucherTypeID'] == VOUCHER_DEBIT_NOTE)
		{
				$Type ='Debit Note Voucher';		
		}
		
		$BankID = $this->obj_utility->getBankID($data2[0]['VoucherTypeID'],$data2[0]['RefNo']);
		$prefix = $this->obj_utility->GetPreFix($data2[0]['VoucherTypeID'],$BankID);
		
		if(!empty($prefix))
		{
			$prefix = $prefix.'-';
		}
		if($data2[0]['VoucherTypeID'] == VOUCHER_SALES)
		{
			$prefix .= $this->getChequeNumber($data2[0]['id'],false);
		}
		else
		{
			$prefix .= $data2[0]['ExternalCounter'];
		} 
		
		
		
		$table1 =  "<table style='text-align:center; margin: 0 auto;'  class='table1'>
					<input type='hidden' id='voucherDetail' value='".$logTable."-".$logRef."'>		
						<tr height='10' >
								<th width='200' style='text-align:center;'>Voucher No</th>
								<th width='200' style='text-align:center;'>Voucher Date</th>
								<th width='200' style='text-align:center;'>Voucher Type</th>
							 </tr>";
		$table1 .=  "<tr height='10'>
							<td align='center'>".$prefix."</td>
							<td align='center'>".getDisplayFormatDate($data2[0]['Date'])."</td>
							<td align='center'>".$Type."</td>
						 </tr></table><br>";			
		
		echo $table1;				 	 
		
		$table2 = "<center><table style='text-align:center;width: 70%;' class='table2' >
						<tr height='10'>
							<th width='200' style='text-align:center;'>By</th>
							<th width='200' style='text-align:center;'>To</th>
							<th width='110' style='text-align:center;'>Debit</th>
							<th width='180' style='text-align:center;'>Credit</th>
						</tr>";	
		
		echo $table2;	
		
		$ledgername_array = array();
		$MemberIDs  = $this->obj_utility->getMemberIDs($data2[0]['Date']);
		//$get_ledger_name = "select id,ledger_name from `ledger`";
		$get_ledger_name ="select led.id,led.ledger_name,IF(unittbl.unit_id IS NULL,'',unittbl.unit_id) as unit_id, IF(mem.owner_name IS NULL,'',mem.owner_name) as owner_name from `ledger` as led LEFT JOIN `unit` as unittbl on unittbl.unit_id = led.id LEFT JOIN `member_main` as mem on mem.unit = unittbl.unit_id  and mem.member_id IN(".$MemberIDs.")";
		$result02 = $this->m_dbConn->select($get_ledger_name);
		
		for($i = 0; $i < sizeof($result02); $i++)
		{
			if($result02[$i]['unit_id']  <> "" && $result02[$i]['owner_name']  <> "" )
			{
				//ledger is member ledger
				$ledgername_array[$result02[$i]['id']] = $result02[$i]['ledger_name']. "-" .$result02[$i]['owner_name'];	
			}
			else
			{
				$ledgername_array[$result02[$i]['id']] = $result02[$i]['ledger_name'];	
			}
		
		}
		
		$sql1 = "select `desc` from `vouchertype` where id='".$vtype."'";
		$data1 = $this->m_dbConn->select($sql1);
		$voucher = $data1[0]['desc'];
		
		
		
		$VoucherNo = $data2[0]['VoucherNo'];
		$VoucherTypeID = $data2[0]['VoucherTypeID'];
		
		$sql002 = "select * from `voucher` where VoucherNo='".$VoucherNo."' and VoucherTypeID='".$VoucherTypeID."' ";	
		$data3 = $this->m_dbConn->select($sql002);
		
		for($i = 0; $i < sizeof($data3); $i++)
		{
			$data3[$i]['ledger_name'] = $ledgername_array[$data3[$i]['ledger_name']];
			
		}
		
		if($data3 <> "")
		{
			if($data2[0]['VoucherTypeID'] == VOUCHER_PAYMENT)
			{
				$data4 = array_reverse($data3);
				//print_r($data4);
				foreach($data4 as $k => $v)
				{
					if($data4[$k]['To'] <> "" )
					{
						echo "<tr height='10'>";
						echo "<td align='center'>".$ledgername_array[$data4[$k]['To']]."</td>";
						echo "<td align='center'></td>";
						if($data4[$k]['Credit'] <> 0)
						{
						echo "<td align='center'>".number_format($data4[$k]['Credit'],2)."</td>";
						}
						else
						{
							echo "<td align='center'> </td>";
							
						}
						if($data4[$k]['Debit'] <> 0)
						{
						 echo "<td align='center'>".number_format($data4[$k]['Debit'],2)."</td>";
						}
						else
						{
							echo "<td align='center'></td>";
							
						}
						echo "</tr>";
					}
					else
					{
						echo "<tr >";
						echo "<td align='center'></td>";
						echo "<td align='center'>".$ledgername_array[$data4[$k]['By']]."</td>";
						if($data4[$k]['Credit'] <> 0)
						{
						echo "<td align='center'>".number_format($data4[$k]['Credit'],2)."</td>";
						}
						else
						{
							echo "<td align='center'></td>";
							
						}
						if($data4[$k]['Debit'] <> 0)
						{
						echo "<td align='center'>".number_format($data4[$k]['Debit'],2)."</td>";
						}
						else
						{
							echo "<td align='center'>0.00</td>";
							
						}	
						echo "</tr>";
					}
				}
			}
			else
			{
				foreach($data3 as $k => $v)
				{
					if($data3[$k]['By'] <> "")
					{
					echo "<tr height='10'>";
							echo "<td align='center'>".$ledgername_array[$data3[$k]['By']]."</td>";
							echo "<td align='center'></td>";
							if($data3[$k]['Debit'] <> 0)
							{
							echo "<td align='center'>".number_format($data3[$k]['Debit'],2)."</td>";
							}
							else
							{
								echo "<td align='center'> </td>";
								
							}
							if($data3[$k]['Credit'] <> 0)
							{
							 echo "<td align='center'>".number_format($data3[$k]['Credit'],2)."</td>";
							}
							else
							{
								echo "<td align='center'></td>";
								
							}
					echo "</tr>";
					
					
					}
					else
					{
					echo "<tr >";
							echo "<td align='center'></td>";
							echo "<td align='center'>".$ledgername_array[$data3[$k]['To']]."</td>";
							if($data3[$k]['Debit'] <> 0)
							{
							echo "<td align='center'>".number_format($data3[$k]['Debit'],2)."</td>";
							}
							else
							{
								echo "<td align='center'></td>";
								
							}
							if($data3[$k]['Credit'] <> 0)
							{
							echo "<td align='center'>".number_format($data3[$k]['Credit'],2)."</td>";
							}
							else
							{
								echo "<td align='center'>0.00</td>";
								
							}	
					echo "</tr>";
					}
				}
			}
		
		}
		else
		{
			echo "<tr height='25'><td colspan='6' align='center'><font color='#FF0000'><b>Records Not Found....<!--  by admin --></b></font></td></tr>";
		}
		echo "</table></center><br>";			
		
		if($data3[0]['Note'] <> "" && ($data3[0]['RefTableID'] == 0 || $data3[0]['RefTableID'] == TABLE_FD_MASTER)) // FD may have many voucher so we nned to take note from voucher
		{
			echo "<center><table border=0 class='table2' style='width: 70%'><tr>";
			echo "<th align='center'>Note</th>";
			echo "</tr>";
			echo "<tr>";
				echo "<td align='center'><div style='100px'>".$data3[0]['Note']."</div></td>";
			echo "</tr></table></center>";
		}
		else
		{
			$table_name = "";
			$column_name = "";
			$ID="ID";
			if($data3[0]['RefTableID'] == TABLE_BILLREGISTER)
			{
				$table_name = "billdetails";
				$column_name = "Note";
			}
			else if($data3[0]['RefTableID'] == TABLE_CHEQUE_DETAILS)
			{
				$table_name = "chequeentrydetails";
				$column_name = "Comments";
			}
			else if($data3[0]['RefTableID'] == TABLE_PAYMENT_DETAILS)
			{
				$table_name = "paymentdetails";
				$column_name = "Comments";
			}
			else if($data3[0]['RefTableID'] == TABLE_REVERSAL_CREDITS)
			{
				$table_name = "reversal_credits";
				$column_name = "Comments";
			}
			else if($data3[0]['RefTableID'] == TABLE_NEFT)
			{
				$table_name = "neft";
				$column_name = "comments";
			}
			else if($data3[0]['RefTableID'] == TABLE_FD_MASTER)
			{
				$table_name = "fd_master";
				$column_name = "note";
			}
			else if($data3[0]['RefTableID'] == TABLE_SALESINVOICE)
			{
				$table_name = "sale_invoice";
				$column_name = "Note";
			}
			else if($data3[0]['RefTableID'] == TABLE_CREDIT_DEBIT_NOTE)
			{
				$table_name = "credit_debit_note";
				$column_name = "Note";
			}
			else if($data3[0]['RefTableID'] == TABLE_FIXEDASSETLIST)
			{
				$table_name = "fixedassetlist";
				$column_name = "FixedAssetDescription";
				$ID="FixedAssetID";
			}
			if($data3[0]['RefTableID'] != 0){
			$build_query = "select ".$column_name." from ".$table_name." where $ID = '".$data3[0]['RefNo']."'";
			$result = $this->m_dbConn->select($build_query);
			
			/*echo "<pre>";
			print_r($result);
			echo "</pre>";*/
			if($result[0][$column_name] <> "")
			{
				echo "<center><table border=0 class='table2' style='width: 70%'><tr>";
					echo "<th align='center'>Note</th>";
				echo "</tr>";
				echo "<tr>";
					echo "<td align='center'><div style='100px'>".$result[0][$column_name]."</div></td>";
				echo "</tr></table></center>";
			}
			}
		}
	}
	
	
public function getDebitCreditDetails($RefID,$RefTableID)
{
	if($RefTableID == TABLE_SALESINVOICE)
	{
		$sql = "SELECT UnitID, Inv_Number, Inv_Date, IF(LatestChangeID = 0, CreatedBy_LoginID, LatestChangeID) AS LoginID, IF(LatestChangeID = 0, CreatedTimestamp, LastModified) AS Timestamp FROM sale_invoice where ID = '".$RefID."'";
		$data = $this->m_dbConn->select($sql);
	}
	else
	{
		$sql = "SELECT UnitID, Note_Type, IF(LatestChangeID = 0, CreatedBy_LoginID, LatestChangeID) AS LoginID, IF(LatestChangeID = 0, CreatedTimestamp, LastModified) AS Timestamp FROM credit_debit_note where ID = '".$RefID."'";
		$data = $this->m_dbConn->select($sql);	
	}
	
	return $data;
}	

public function get_voucher_details($vtype = 0,$vid = 0,$lid = 0,$gid = 0, $byORto = "")
{	
	if($vtype <> '' && $lid == '')
	{ 
		$sql = "select `desc` from `vouchertype` where id='".$vtype."'";	
		$data = $this->m_dbConn->select($sql);
		return $data[0]['desc'];
	}
	else if($vtype == '')
	{
		//$sqlFetchVoucher = "select VoucherNo,VoucherTypeID from `voucher` where id='".$vid."' ";
		//$dataVoucher = $this->m_dbConn->select($sqlFetchVoucher);
		
		//$sqlFetchNote = "select `Note` from `voucher` where VoucherNo ='".$dataVoucher[0]['VoucherNo']."' and VoucherTypeID ='".$dataVoucher[0]['VoucherTypeID']."' ";
		//$resNote = $this->m_dbConn->select($sqlFetchNote);
		
		$sql2 = "select * from `voucher` where id='".$vid."' ";
		$data2 = $this->m_dbConn->select($sql2);
		
		//if note in voucher table blanck fetch note from chequeentrydetails or paymentdetails
		/*if($data2[0]['Note'] == ""  && $resNote[0]['Note'] <> "" )
		{
			$data2[0]['Note'] = 	$resNote[0]['Note'];
		}
		else */
		if((empty($data2[0]['Note']) || $data2[0]['Note'] == "0") && $data2[0]['VoucherTypeID'] <> VOUCHER_SALES && $data2[0]['RefTableID'] <> "" &&  $data2[0]['RefNo'] <> "")
		{
			$sqlfetch  = "";
			if($data2[0]['RefTableID'] == TABLE_CHEQUE_DETAILS )
			{
			 	$sqlfetch = "select `Comments`,`PayerBank`,`ChequeNumber`, EnteredBy, Timestamp from `chequeentrydetails` where `ID` = '".$data2[0]['RefNo']."'";		
			}
			else if($data2[0]['RefTableID'] == TABLE_PAYMENT_DETAILS)
			{
				$sqlfetch = "select `Comments`,`ChequeNumber`, EnteredBy, Timestamp from `paymentdetails` where `id` = '".$data2[0]['RefNo']."'";						
			}
			else if($data2[0]['RefTableID'] == TABLE_CREDIT_DEBIT_NOTE)
			{
				$sqlfetch = "select `Note` as Comments from `credit_debit_note` where `id` = '".$data2[0]['RefNo']."'";						
			}
			if($sqlfetch <> "")
			{
				$datafetch = $this->m_dbConn->select($sqlfetch);
				if($datafetch <> "")
				{
					$data2[0]['ChequeNumber'] = $datafetch[0]['ChequeNumber'];
					if($datafetch[0]['Comments'] <> "")
					{
						$data2[0]['Note'] = strip_tags($datafetch[0]['Comments']);
					}
					if($data2[0]['RefTableID'] == TABLE_CHEQUE_DETAILS )
					{
						$data2[0]['PayerBank'] = $datafetch[0]['PayerBank'];
					}
					$data2[0]['LoginID'] = $datafetch[0]['EnteredBy'];
					$data2[0]['Timestamp'] = $datafetch[0]['Timestamp'];
				}
			}
		}
		else if($data2[0]['VoucherTypeID'] == VOUCHER_SALES && $data2[0]['RefTableID'] <> "" &&  $data2[0]['RefNo'] <> "")
		{
			$sqlBill = "SELECT `BillNumber`,`PeriodID` FROM `billdetails` where `ID`= ".$data2[0]['RefNo'] ." ";
			$billresult = $this->m_dbConn->select($sqlBill);
			$chequeNumber = $billresult[0]['BillNumber']; 
			if($billresult <> "")
			{
				$sqlPeriod = "Select periodtbl.type, yeartbl.YearDescription from period as periodtbl JOIN year as yeartbl ON periodtbl.YearID = yeartbl.YearID where periodtbl.ID = '" . $billresult[0]['PeriodID'] . "'";
			
				$sqlResult = $this->m_dbConn->select($sqlPeriod);
				
				if($sqlResult <> "")
				{
					$chequeNumber =  '[BillNo:' . $billresult[0]['BillNumber'].'] <br>[ Maintenance Bill For '.$sqlResult[0]['type'] . " "  . $sqlResult[0]['YearDescription'].']';
					$data2[0]['Note'] = $chequeNumber;
				}
			}		
		}
				
		return $data2;
	}
	else if($vtype <> '' && $vid <> '' && $lid <> '')
	{ 	
		$finalRes = array();
		$aryParent = $this->obj_utility->getParentOfLedger($lid);
		$sql1 = "select `desc` from `vouchertype` where id='".$vtype."'";
		$data1 = $this->m_dbConn->select($sql1);
		$voucher = $data1[0]['desc'];
		

		$sql2 = "select `RefTableID`,`VoucherNo`, `RefNo`,`VoucherTypeID`,`Note` from `voucher` where id='".$vid."' ";
		$data2 = $this->m_dbConn->select($sql2);
		
		$RefNo = $data2[0]['RefNo'];
		$RefTableID = $data2[0]['RefTableID'];
		$VoucherNo = $data2[0]['VoucherNo'];
		$finalRes[0]['Note'] = $data2[0]['Note'];
		$finalRes[0]['VoucherType'] = $voucher;
		$finalRes[0]['VoucherNo'] = $VoucherNo;
		//print_r($data2);
		
		if($data2[0]['VoucherTypeID'] <> VOUCHER_SALES && $data2[0]['RefTableID'] <> "" &&  $data2[0]['RefNo'] <> "")
		{
			$sqlfetch  = "";
			if($data2[0]['RefTableID'] == TABLE_CHEQUE_DETAILS )
			{
			 	$sqlfetch = "select `Comments`,`PayerBank`,`ChequeNumber`, EnteredBy, Timestamp from `chequeentrydetails` where `ID` = '".$data2[0]['RefNo']."'";		
			}
			else if($data2[0]['RefTableID'] == TABLE_PAYMENT_DETAILS)
			{
				$sqlfetch = "select `Comments`,`ChequeNumber`, EnteredBy, Timestamp from `paymentdetails` where `id` = '".$data2[0]['RefNo']."'";						
			}
			else if($data2[0]['RefTableID'] == TABLE_CREDIT_DEBIT_NOTE)
			{
				$sqlfetch = "select `Note` as Comments from `credit_debit_note` where `id` = '".$data2[0]['RefNo']."'";						
			}
			if($sqlfetch <> "")
			{
				$datafetch = $this->m_dbConn->select($sqlfetch);
				if($datafetch <> "")
				{
					$finalRes[0]['LoginID'] = $datafetch[0]['EnteredBy'];
					$finalRes[0]['Timestamp'] = $datafetch[0]['Timestamp'];
					$finalRes[0]['ChequeNumber'] = $datafetch[0]['ChequeNumber'];
					if($datafetch[0]['Comments'] <> "")
					{
						$finalRes[0]['Note'] = $datafetch[0]['Comments'];
					}
					if($data2[0]['RefTableID'] == TABLE_CHEQUE_DETAILS )
					{
						$finalRes[0]['PayerBank'] = $datafetch[0]['PayerBank'];
					}
				}
			}
		}
		else if($data2[0]['VoucherTypeID'] == VOUCHER_SALES && $data2[0]['RefTableID'] <> "" &&  $data2[0]['RefNo'] <> "")
		{
			$sqlBill = "SELECT `BillNumber`,`PeriodID` FROM `billdetails` where `ID`= ".$data2[0]['RefNo'] ." ";
			$billresult = $this->m_dbConn->select($sqlBill);
			$chequeNumber = $billresult[0]['BillNumber']; 
			if($billresult <> "")
			{
				$sqlPeriod = "Select periodtbl.type, yeartbl.YearDescription from period as periodtbl JOIN year as yeartbl ON periodtbl.YearID = yeartbl.YearID where periodtbl.ID = '" . $billresult[0]['PeriodID'] . "'";
			
				$sqlResult = $this->m_dbConn->select($sqlPeriod);
				
				if($sqlResult <> "")
				{
					$chequeNumber =  '[BillNo:' . $billresult[0]['BillNumber'].'] <br>[ Maintenance Bill For '.$sqlResult[0]['type'] . " "  . $sqlResult[0]['YearDescription'].']';
					$finalRes[0]['Note'] = $chequeNumber;
					$finalRes[0]['ChequeNumber'] = $billresult[0]['BillNumber'];
				}
			}		
		}
		
		if($aryParent['category'] == BANK_ACCOUNT && $vtype == VOUCHER_RECEIPT)
		{
			$sql3 = "select `ledger_name`,ledgertbl.id from `voucher` as vouchertbl JOIN `ledger` as ledgertbl on vouchertbl.By=ledgertbl.id where vouchertbl.RefNo='".$RefNo."' and vouchertbl.RefTableID='".$RefTableID."' and vouchertbl.VoucherNo='".$VoucherNo."'";
			$data3 = $this->m_dbConn->select($sql3);
			$finalRes[0]['ledger_name'] = $data3[0]['ledger_name'];	
			//return $data3[0]['ledger_name'];	
		}
		else if($aryParent['category'] == BANK_ACCOUNT && $vtype == VOUCHER_PAYMENT)
		{
			$sql4 = "select `ledger_name`,ledgertbl.id from `voucher` as vouchertbl JOIN `ledger` as ledgertbl on vouchertbl.To=ledgertbl.id where vouchertbl.RefNo='".$RefNo."' and vouchertbl.RefTableID='".$RefTableID."' and vouchertbl.VoucherNo='".$VoucherNo."'";
			$data4 = $this->m_dbConn->select($sql4);	
			$finalRes[0]['ledger_name'] = $data4[0]['ledger_name'];			
			//return $data4[0]['ledger_name'];	
		}
		else
		{			
			if($gid == 1)
			{	
				if($byORto <> "")
				{
					$sql4 = "select `ledger_name`,ledgertbl.id from `voucher` as vouchertbl JOIN `ledger` as ledgertbl on vouchertbl.".$byORto."=ledgertbl.id where vouchertbl.RefNo='".$RefNo."' and vouchertbl.RefTableID='".$RefTableID."' and vouchertbl.VoucherNo='".$VoucherNo."'";	
				}
				else
				{
					$sql4 = "select `ledger_name`,ledgertbl.id from `voucher` as vouchertbl JOIN `ledger` as ledgertbl on vouchertbl.By=ledgertbl.id where vouchertbl.RefNo='".$RefNo."' and vouchertbl.RefTableID='".$RefTableID."' and vouchertbl.VoucherNo='".$VoucherNo."'";	
				}
				$data4 = $this->m_dbConn->select($sql4);				
			}	
			else
			{
				
				if($byORto <> "")
				{
					$sql4 = "select `ledger_name`,ledgertbl.id  from `voucher` as vouchertbl JOIN `ledger` as ledgertbl on vouchertbl.".$byORto."=ledgertbl.id where vouchertbl.RefNo='".$RefNo."' and vouchertbl.RefTableID='".$RefTableID."' and vouchertbl.VoucherNo='".$VoucherNo."'";
				}
				else
				{
					$sql4 = "select `ledger_name`,ledgertbl.id from `voucher` as vouchertbl JOIN `ledger` as ledgertbl on vouchertbl.To=ledgertbl.id where vouchertbl.RefNo='".$RefNo."' and vouchertbl.RefTableID='".$RefTableID."' and vouchertbl.VoucherNo='".$VoucherNo."'";
				}
				$data4 = $this->m_dbConn->select($sql4);				
			}
			$finalRes[0]['ledger_name'] = $data4[0]['ledger_name'];	
			$finalRes[0]['ledger_id'] = $data4[0]['id'];
			/* Added Get Ldger Pan No. */
			  $sql5= "SELECT * FROM `ledger_details` where LedgerID = '".$finalRes[0]['ledger_id']."'"; 
			$data5 = $this->m_dbConn->select($sql5);
			$finalRes[0]['ledger_pan'] = $data5[0]['PAN_No'];
			$finalRes[0]['Tds_Head'] = $data5[0]['TDS_NatureOfPayment'];
			$finalRes[0]['TDS_Ded_rate'] = $data5[0]['TDS_Ded_rate'];
			
		}
		
		//print_r($finalRes);
		return $finalRes;
	}
}

public function IsCreditor($LedgerId)
{
	$sql = "select count(*) as cnt from ledger As ledgertbl JOIN account_category As categorytbl ON ledgertbl.categoryid = categorytbl.category_id where ledgertbl.payment=1 and categorytbl.group_id=1 and ledgertbl.society_id='".$_SESSION['society_id']."' and ledgertbl.id='".$LedgerId."' ";
	$data = $this->m_dbConn->select($sql);
	if($data[0]['cnt'] == 1)
	return true;
	return false;
}

public function generatUrl($VoucherID,$VoucherTypeID,$isDelete = false)
{
	$Url = ""; 
	$sqlvoucher = 'select * from `voucher` where `id` = "'.$VoucherID.'" and 	`VoucherTypeID` = "'.$VoucherTypeID.'" ';
	$resvoucher = $this->m_dbConn->select($sqlvoucher);
	
	if(($VoucherTypeID == '2' || $VoucherTypeID == '6')  && $resvoucher[0]['RefTableID'] == '3')
	{
		//read payment table
		$sql01 = 'select `RefNo`,`RefTableID`,`By` from `voucher` where `VoucherNo` = "'.$resvoucher[0]['VoucherNo'].'"';
		$res01 = $this->m_dbConn->select($sql01);
		
		$VoucherSize = count($res01);
		if(isset($res01))
		{
			$sqlPayment = 'select * from `paymentdetails` where `id` = "'.$res01[0]['RefNo'].'"';
			$resPayment = $this->m_dbConn->select($sqlPayment);
			$customLeafQuery = "SELECT `CustomLeaf` FROM `chequeleafbook` WHERE `id` = '".$resPayment[0]['ChqLeafID']."'";				
			$result = $this->m_dbConn->select($customLeafQuery);
			if($resPayment[0]['ChqLeafID'] == -1)
			{
				$result[0]['CustomLeaf'] = -1;
			}
			$resPayment[0]['CustomLeaf'] = $result[0]['CustomLeaf'];	
			
			if($VoucherSize > 2) // If size is more than 2 means it's multiple ledger report
			{
				$Url = "VoucherEdit.php?Vno=".$resvoucher[0]['VoucherNo']."&bankid=".$resPayment[0]['PayerBank']."&chequeNumber=".$resPayment[0]['ChequeNumber'].'&LeafID='.$resPayment[0]['ChqLeafID'].'&payment=1';				
			}
			else
			{
				if($result[0]['CustomLeaf'] == -1)
				{
					$Url = "PaymentDetails.php?bankid=".$resPayment[0]['PayerBank']."&LeafID=".$resPayment[0]['ChqLeafID']."&edt=".$res01[0]['RefNo'];																	
				}
				else
				{
					$Url = "PaymentDetails.php?bankid=".$resPayment[0]['PayerBank']."&LeafID=".$resPayment[0]['ChqLeafID']."&CustomLeaf= ". $resPayment[0]['CustomLeaf']. "&edt=".$res01[0]['RefNo'];																	
				}	
			}
		}
	}
	else if(($VoucherTypeID == '3' || $VoucherTypeID == '6') && ($resvoucher[0]['RefTableID'] == '2' || $resvoucher[0]['RefTableID'] == TABLE_FD_MASTER))
	{
		//read chequeentrydetails table
		
		if($VoucherTypeID == '3')
		{
			$Column = 'To';
		}
		else
		{
			$Column = 'By';
		}
		
		$sql01 = "select `RefNo`,`RefTableID`,`".$Column."`, `SrNo` from `voucher` where `VoucherNo` = '".$resvoucher[0]['VoucherNo']."' and 	`VoucherTypeID` = '".$VoucherTypeID."' and `".$Column."` > 0 ";
		$res01 = $this->m_dbConn->select($sql01);
		
		$IsMultiplekeyExitsForSameVoucher = array_key_exists(1,$res01);
		if($IsMultiplekeyExitsForSameVoucher == true)// means there is 2 entries with same voucherno Which can cause problem if we delete or edit 
		{
			//return false;
		}
		
		$sqlReceipt = 'select * from `chequeentrydetails` where `ID` = "'.$res01[0]['RefNo'].'"';
		$resRecipt = $this->m_dbConn->select($sqlReceipt);	
		$depositID = $resRecipt[0]['DepositID'];
		
		if($depositID > 0)
		{
			
			$Url = "ChequeDetails.php?depositid=".$depositID."&bankid=".$res01[0][$Column]."&edt=".$res01[0]['RefNo'];	
		}
		else if($depositID == -2)
		{
			$Url = "NeftDetails.php?bankid=".$res01[0][$Column]."&edt=".$res01[0]['RefNo'];	
		}
		else if($depositID == -3)
		{
			$Url = "ChequeDetails.php?depositid=".$depositID."&bankid=".$res01[0][$Column]."&edt=".$res01[0]['RefNo'];	
		}

		
		if($resvoucher[0]['RefTableID'] == TABLE_FD_MASTER){
			$Url = 'VoucherEdit.php?Vno='.$resvoucher[0]['VoucherNo'];
		}
			
	}
	if($isDelete)
	{
		$Url .= "&delete"; 	
	}
	return $Url;
}

public function CheckVoucherType($VoucherID)
{
	$Url = '';
	
	$sql03 = "select `VoucherNo` from `voucher` where `id` = '".$VoucherID."' ";
	$data03 = $this->m_dbConn->select($sql03);
	
	$sql = "select * from `voucher` where `VoucherNo` = '".$data03[0]['VoucherNo']."' ";
	$data = $this->m_dbConn->select($sql);
	
	if($data[0]['VoucherTypeID'] == VOUCHER_JOURNAL)
	{
		$Url = "VoucherEdit.php?Vno=".$data03[0]['VoucherNo'];
		return $Url;
	}
	
	if($data <> "")
	{
		//check if jv exists in payment by voucher id
		$checkPaymentEntry = "select * from `paymentdetails` where `VoucherID` = '".$data[0]['id']."' ";
		$res2 = $this->m_dbConn->select($checkPaymentEntry);
		
		if(sizeof($res2) > 0)
		{
			//jv exists in payment means jv is of payment type
			$sql01 = "select * from `voucher` where `RefNo` = '".$res2[0]['id']."' and `RefTableID` = '3'  and `By` > 0 ";
			$res01 = $this->m_dbConn->select($sql01);
			
			$customLeafQuery = "SELECT `CustomLeaf` FROM `chequeleafbook` WHERE `id` = ".$res2[0]['ChqLeafID'];				
			$result = $this->m_dbConn->select($customLeafQuery);
			
			if($result[0]['CustomLeaf'] == -1)
			{
				$Url = "PaymentDetails.php?bankid=".$res01[0]['By']."&LeafID=".$res2[0]['ChqLeafID']."&edt=".$res2[0]['id'];																	
			}
			else
			{
				$Url = "PaymentDetails.php?bankid=".$res01[0]['By']."&LeafID=".$res2[0]['ChqLeafID']."&CustomLeaf= ". $result[0]['CustomLeaf']. "&edt=".$res2[0]['id'];																	
			}
			//echo $Url;
			return $Url;	
		}
		else
		{
			$checkPaymentEntry = "select * from `paymentdetails` where `Amount` = '".$data[1]['Credit']."' and `PaidTo` = '".$data[1]['To']."' and `InvoiceDate` = '".$data[1]['Date']."' ";
			$res3 = $this->m_dbConn->select($checkPaymentEntry);
			if(sizeof($res3) > 1)
			{
				//multiple entries fetched
				for($i=0; $i < sizeof($res3); $i++)
				{
					//search payment id in voucher
					$sql2 = "select * from `voucher` where `RefNo` = '".$res3[$i]['id']."' and `RefTableID` = '3' ";
					$res4 = $this->m_dbConn->select($sql2); 
					if(sizeof($res4) > 1)
					{
						if($res4[0]['VoucherNo'] == $data[0]['VoucherNo'] + 1)
						{
							//jv voucherno and payment voucher number match
							$customLeafQuery = "SELECT `CustomLeaf` FROM `chequeleafbook` WHERE `id` = ".$res3[$i]['ChqLeafID'];				
							$result = $this->m_dbConn->select($customLeafQuery);
							
							if($result[0]['CustomLeaf'] == -1)
							{
								$Url = "PaymentDetails.php?bankid=".$res4[0]['By']."&LeafID=".$res3[$i]['ChqLeafID']."&edt=".$res3[$i]['id'];																	
							}
							else
							{
								$Url = "PaymentDetails.php?bankid=".$res4[0]['By']."&LeafID=".$res3[$i]['ChqLeafID']."&CustomLeaf= ". $result[0]['CustomLeaf']. "&edt=".$res3[$i]['id'];																	
							}
							//echo $Url;
							return $Url;	
						}
						else
						{
							//no record found	
							//echo "test";
						}	
					}	
				}	
			}
			else if(sizeof($res3) == 1)
			{
				//one entry fetched	
				$sql2 = "select * from `voucher` where `RefNo` = '".$res3[0]['id']."' and `RefTableID` = '3' ";
				$res04 = $this->m_dbConn->select($sql2); 
				
				$customLeafQuery = "SELECT `CustomLeaf` FROM `chequeleafbook` WHERE `id` = ".$res3[0]['ChqLeafID'];				
				$result = $this->m_dbConn->select($customLeafQuery);
				
				if($result[0]['CustomLeaf'] == -1)
				{
					$Url = "PaymentDetails.php?bankid=".$res04[0]['By']."&LeafID=".$res3[0]['ChqLeafID']."&edt=".$res3[0]['id'];																	
				}
				else
				{
					$Url = "PaymentDetails.php?bankid=".$res04[0]['By']."&LeafID=".$res3[0]['ChqLeafID']."&CustomLeaf= ". $result[0]['CustomLeaf']. "&edt=".$res3[0]['id'];																	
				}
				//echo $Url;
				return $Url;	
			}
			else
			{
				//no record found		
			}
				
		}
	}
	
	return '';
}

public function getChequeNumber($vid,$showBillText = true)
{
	$chequeNumber = '-';
	$sql2 = "select * from `voucher` where `id`= '".$vid."' ";
	$data2 = $this->m_dbConn->select($sql2);
	
	if(sizeof($data2) > 0)
	{
		$RefNo = $data2[0]['RefNo'];
		$RefTableID = $data2[0]['RefTableID'];
		$type = $data2[0]['VoucherTypeID'];
		if($RefTableID <> "")
		{									
				
			if($RefTableID == TABLE_CHEQUE_DETAILS)
			{	
				
				$sql = "select ChequeNumber FROM  `chequeentrydetails`  where ID ='".$RefNo."'";									
				$result = $this->m_dbConn->select($sql);					
				$chequeNumber = $result[0]['ChequeNumber'];
					
			}
			else if($RefTableID == TABLE_PAYMENT_DETAILS)
			{
				$sql = "select ChequeNumber FROM  `paymentdetails`  where id ='".$RefNo."'";									
				$result = $this->m_dbConn->select($sql);					
				$chequeNumber = $result[0]['ChequeNumber']; 
			}
			else if($type == 1)
			{
				$sqlBill = "SELECT `BillNumber`,`PeriodID` FROM `billdetails` where `ID`= ".$RefNo." ";
				$billresult = $this->m_dbConn->select($sqlBill);
				$chequeNumber = $billresult[0]['BillNumber']; 
				if($billresult <> "")
				{
					$sqlPeriod = "Select periodtbl.type, yeartbl.YearDescription from period as periodtbl JOIN year as yeartbl ON periodtbl.YearID = yeartbl.YearID where periodtbl.ID = '" . $billresult[0]['PeriodID'] . "'";
				
					$sqlResult = $this->m_dbConn->select($sqlPeriod);
					if($showBillText == true)
					{
						$chequeNumber =  $billresult[0]['BillNumber'].' <br>[ Maintenance Bill For '.$sqlResult[0]['type'] . " "  . $sqlResult[0]['YearDescription'].']';
					}
					else
					{
							$chequeNumber =  $billresult[0]['BillNumber'];	
					}
				}
			}	
			
			if($chequeNumber == -1){
				$chequeNumber = 'CASH';
			}
		}
	}
	return $chequeNumber;	
}

public function fetchLedgerIDArray($type)
{
//	echo "<BR> Type : " . $type;
		if($type == 0)
		{
			//$sql = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id  ORDER BY acctbl.group_id ASC ";
			$sql = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id WHERE acctbl.group_id = '".LIABILITY."'  ORDER BY acctbl.group_id,led.ledger_name ASC ";
			
			$sql1 = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led , `account_category` as acctbl, `unit` AS u where led.categoryid =acctbl.category_id AND led.id = u.unit_id AND led.categoryid = '".DUE_FROM_MEMBERS."'  ORDER BY acctbl.group_id,u.sort_order ASC ";
			
			$sql2 = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id WHERE acctbl.group_id = '".ASSET."' AND led.categoryid <> '".DUE_FROM_MEMBERS."' ORDER BY acctbl.group_id,led.ledger_name ASC";
			
			$sql3 = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id WHERE acctbl.group_id = '".EXPENSE."' OR acctbl.group_id = '".INCOME."' ORDER BY acctbl.group_id,led.ledger_name ASC";
			
		}
		else if($type == 1)
		{	
			//$sql = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id where led.categoryid = '".DUE_FROM_MEMBERS."'   ORDER BY acctbl.group_id,led.id ASC";		 
			$sql = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led, `account_category` as acctbl, `unit` AS u where led.categoryid =acctbl.category_id AND led.id = u.unit_id AND led.categoryid = '".DUE_FROM_MEMBERS."'   ORDER BY acctbl.group_id,u.sort_order ASC";
		}
		else if($type == 2)
		{
			$sql = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id  where led.categoryid NOT IN (".DUE_FROM_MEMBERS.",".BANK_ACCOUNT.",".CASH_ACCOUNT.")   ORDER BY acctbl.group_id,led.ledger_name ASC ";
		}	
		else if($type == 3)
		{
			$sql = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id  where led.categoryid IN (".BANK_ACCOUNT.")  ORDER BY acctbl.group_id,led.ledger_name ASC";
		}	
		else if($type == 4)
		{
			$sql = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id  where led.categoryid  IN (".CASH_ACCOUNT.")  ORDER BY acctbl.group_id,led.ledger_name ASC";
		}	
		else if($type == 5)		//Liability
		{
			//$sql = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id  ORDER BY acctbl.group_id ASC ";
			$sql = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id WHERE acctbl.group_id = '".LIABILITY."'  ORDER BY acctbl.group_id,led.ledger_name ASC ";
		}	
		else if($type == 6)		//Assets
		{
			
			//$sql1 = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led , `account_category` as acctbl, `unit` AS u where led.categoryid =acctbl.category_id AND led.id = u.unit_id AND led.categoryid = '".DUE_FROM_MEMBERS."'  ORDER BY acctbl.group_id,u.sort_order ASC ";
			
			$sql = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id WHERE acctbl.group_id = '".ASSET."' AND led.categoryid <> '".DUE_FROM_MEMBERS."' ORDER BY acctbl.group_id,led.ledger_name ASC";
		}	
		else if($type == 7)		//Income
		{
			
			$sql = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id WHERE acctbl.group_id = '".INCOME."' ORDER BY acctbl.group_id,led.ledger_name ASC";
			
		}
		else if($type == 8)		//Expense
		{
			
			$sql3 = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id WHERE acctbl.group_id = '".EXPENSE."' ORDER BY acctbl.group_id,led.ledger_name ASC";
			
		}
		
		$sqlResult = $this->m_dbConn->select($sql);
		if($sql1 <> '')
		{
			$sqlResult1 = $this->m_dbConn->select($sql1);
			$sqlResult2 = $this->m_dbConn->select($sql2);
			$sqlResult3 = $this->m_dbConn->select($sql3);		
			
			 if(is_array($sqlResult1) & !empty($sqlResult1))
            {	
				$sqlResult = array_merge($sqlResult,$sqlResult1);
			}
			 if(is_array($sqlResult2) & !empty($sqlResult2))
			 {
				$sqlResult = array_merge($sqlResult,$sqlResult2);				
			 }
			  if(is_array($sqlResult3) & !empty($sqlResult3))
			 {
				$sqlResult = array_merge($sqlResult,$sqlResult3);				
			 }
		}
		return $sqlResult;
		
	}
	/*------------------------------------------All leger detail-----------------------------------*/
	
	public function AllLegerDetail($groupid,$from,$to)
	{
		
		if(isset($_POST['from_date']) <> '')
		{
			$from=getDBFormatDate($_POST['from_date']); 
			$to=getDBFormatDate($_POST['to_date']); 
			$groupid=$_POST['groupid'];
		}
			if($groupid<>'')
			{
				//$sql="SELECT v.id, v.Date,v.VoucherNo,v.By,v.Debit,v.Credit,v.Note,v.VoucherTypeID, l.ledger_name,vt.desc FROM `voucher` as v join vouchertype as vt on v.VouchertypeID=vt.id join ledger as l on v.`By`=l.id group by v.`voucherNo` ";	
				$sql="SELECT v.id, v.Date,v.VoucherNo, v.ExternalCounter, v.By,v.Debit,v.Credit,v.Note,v.VoucherTypeID, l.ledger_name, l.id as ledger_id, vt.desc,ac.group_id,p.ID as 'PeriodID' FROM `voucher` as v join vouchertype as vt on v.VouchertypeID=vt.id join ledger as l on v.`By`=l.id join `account_category` as ac on l.categoryid=ac.category_id join period as p on (v.Date BETWEEN p.BeginingDate and p.EndingDate) where (v.`Date` BETWEEN '".$from."' AND '".$to."') and ac.group_id='".$groupid."' group by v.`voucherNo`";
				//  $sql="SELECT v.id, v.Date,v.VoucherNo,v.By,v.Debit,v.Credit,v.Note,v.VoucherTypeID, l.ledger_name,vt.desc,ac.group_id FROM `voucher` as v join vouchertype as vt on v.VouchertypeID=vt.id join ledger as l on v.`By`=l.id join `account_category` as ac on l.categoryid=ac.category_id  where (v.`Date` BETWEEN '".$from."' AND '".$to."') and ac.group_id='".$groupid."' group by v.`voucherNo`";
			}
			else
			{
				 $sql="SELECT v.id, v.Date,v.VoucherNo, v.ExternalCounter, v.RefNo,v.By,v.Debit,v.Credit,v.Note,v.VoucherTypeID, l.ledger_name, l.id as ledger_id, vt.desc,p.ID as 'PeriodID' FROM `voucher` as v join vouchertype as vt on v.VouchertypeID=vt.id join ledger as l on v.`By`=l.id join period as p on (v.Date BETWEEN p.BeginingDate and p.EndingDate)  where (v.`Date` BETWEEN '".$from."' AND '".$to."') group by v.`voucherNo`";
				//echo $sql="SELECT v.id, v.Date,v.VoucherNo,v.By,v.Debit,v.Credit,v.Note,v.VoucherTypeID, l.ledger_name,vt.desc FROM `voucher` as v join vouchertype as vt on v.VouchertypeID=vt.id join ledger as l on v.`By`=l.id  where (v.`Date` BETWEEN '".$_SESSION['default_year_start_date']."' AND '".$_SESSION['default_year_end_date']."') group by v.`voucherNo`";
			}

				$result = $this->m_dbConn->select($sql);
		
				for($i = 0 ; $i < count($result); $i++)
				{
					$result[$i]['BankID']= $this->obj_utility->getBankID($result[$i]['VoucherTypeID'], $result[$i]['RefNo']);
				}
		
		return $result;	
	
	}
	
	public function BillType($refNo)
	{
		$sql="SELECT BillType FROM `billdetails` where ID='".$refNo."'";
		$result = $this->m_dbConn->select($sql);
		return $result;	
	
	}
	

		public function combobox($query,$id)
	{		
	//echo "test group";
		$str='';
		$str.="<option value=''>All</option>";
		echo $data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($v==$id)
						{
							$sel = "selected";
						}
						else
						{
							$sel = "";	
						}
						$str.="<OPTION VALUE=".$v." ".$sel.">";
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
	public function combobox1($query,$id)
	{		
	
		$str='';
		//$str.="<option value=''>All</option>";
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
						if($v==$id)
						{
							$sel = "selected";
						}
						else
						{
							$sel = "";	
						}
						$str.="<OPTION VALUE=".$v." ".$sel.">";
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
	function delete_receipts($VoucherNos)
	{
		//echo "Coming here 2?";
		$deleted_chequeentrydetailsID = array();
		$k = 0;
		for($i = 0; $i < sizeof($VoucherNos); $i++)
		{
			//echo "size of: ".sizeof($VoucherNos)."<br>";
			$sql01 = "SELECT v.id, v.Date, v.VoucherNo, v.RefNo, v.RefTableID, v.By, v.Debit, v.Credit, v.Note, v.VoucherTypeID, l.ledger_name, vt.desc FROM `voucher` AS v JOIN vouchertype AS vt ON v.VouchertypeID = vt.id JOIN ledger AS l ON v.`By` = l.id WHERE v.VoucherTypeID = '3' AND v.VoucherNo = '".$VoucherNos[$i]."' GROUP BY v.`voucherNo`";
			//echo "<br>";
			$sql11 = $this->m_dbConn->select($sql01);
			
			$chequeentrydetailsID = $sql11[0]['RefNo'];
			$table_id = $sql11[0]['RefTableID'];
			if($table_id == 2)
			{
				$sql02 = "SELECT * FROM `chequeentrydetails` WHERE `ID` = '".$chequeentrydetailsID."'";
				$sql22 = $this->m_dbConn->select($sql02);
				
				/*$PaidBy=$select_type[0]['PaidBy'];
				$PayerBank=$select_type[0]['PayerBank'];
				$PayerChequeBranch=$select_type[0]['PayerChequeBranch'];
				$ChequeDetailsId=$select_type[0]['ID']; //chequeentrydetails's ID
	
				$paymentIDs = $obj_ChequeDetails->deleteReturnChequeEntry($PaidBy);	
				foreach($paymentIDs as $k => $v)
				{
					foreach($v as $kk => $vv)
					{
						echo $vv.",";
					}
				}
		
				$obj_ChequeDetails->DeletePreviousRecord($PaidBy, $PayerBank, $PayerChequeBranch,$ChequeDetailsId);*/
				
				$PaidBy = $sql22[0]['PaidBy'];
				$PayerBank = $sql22[0]['PayerBank'];
				$PayerChequeBranch = $sql22[0]['PayerChequeBranch'];
				
				//$returnedIDs = $this->obj_chequeDetails->deleteReturnChequeEntry($PaidBy);
				$is_update = $this->obj_chequeDetails->DeletePreviousRecord($PaidBy,$PayerBank,$PayerChequeBranch,$chequeentrydetailsID);
				
				if($is_update == "Update")
				{
					$deleted_chequeentrydetailsID[$k] = $chequeentrydetailsID;
					$k++;
				}
			}
		}
		return $deleted_chequeentrydetailsID;
	}
	
	function link_vouchers($VoucherNos,$fd_id)
	{
		$all_Vouchers = array();
		$updated_voucher = array();
		$not_updated_voucher = array();
		$i = 0;
		$j = 0;
		for($iVCount = 0; $iVCount < sizeof($VoucherNos); $iVCount++)
		{
			$sql01 = "SELECT * FROM `voucher` WHERE `VoucherNo` = '".$VoucherNos[$iVCount]."'";
			$sql11 = $this->m_dbConn->select($sql01);
			
			if($sql11[0]['RefTableID'] == 2)
			{
				$chequeentrydetailsID = $sql11[0]['RefNo'];
				$sql03 = "DELETE FROM `chequeentrydetails` WHERE `ID` = '".$chequeentrydetailsID."'";
				$sql33 = $this->m_dbConn->delete($sql03);
				
				$sql04 = "UPDATE `voucher` SET `RefNo` = '".$fd_id."', `RefTableID` = '6' WHERE `VoucherNo` = '".$VoucherNos[$iVCount]."'";
				$sql44 = $this->m_dbConn->update($sql04);
				
				$updated_voucher[$i] = $VoucherNos[$iVCount];
				$i++;
			}
			else if($sql11[0]['RefTableID'] == 0 && $sql11[0]['RefNo'] == 0)
			{
				$sql02 = "UPDATE `voucher` SET `RefNo` = '".$fd_id."', `RefTableID` = '6' WHERE `VoucherNo` = '".$VoucherNos[$iVCount]."'";
				$sql22 = $this->m_dbConn->update($sql02);
				
				$updated_voucher[$i] = $VoucherNos[$iVCount];
				$i++;
			}
			else
			{
				$not_updated_voucher[$j] = $VoucherNos[$iVCount];
				$j++;
			}
		}
		array_push($all_Vouchers,$updated_voucher,$not_updated_voucher);
		
		return $all_Vouchers;
	}

function InvoiceStatus($VoucherNo)
{
	$selectQuery="SELECT InvoiceClearedVoucherNo,TDSVoucherNo FROM `invoicestatus` where InvoiceRaisedVoucherNo='".$VoucherNo."'";
	
	$result= $this->m_dbConn->select($selectQuery);
	return $result;
}

public function getGroupMembers($type)
    {
		$sql = "";
		$memId =  array();
		$ledDetails = "";
		if($type == 1)
		{	
		
		
			$sql = "select led.id as LEDID,concat_ws('-',led.ledger_name,m.owner_name) as LEDNAME,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led, `account_category` as acctbl, `unit` AS u,member_main as m where led.categoryid =acctbl.category_id AND led.id = u.unit_id AND u.unit_id = m.unit AND led.categoryid = '".DUE_FROM_MEMBERS."'   and m.ownership_status = 1 ORDER BY acctbl.group_id,u.sort_order ASC";
			$memId = $this->m_dbConn->select($sql);
		}
		else if($type == 2)
		{
			//old one.
			//$sql = "select led.id as LEDID,led.ledger_name as LEDNAME,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id  where led.categoryid NOT IN (".DUE_FROM_MEMBERS.",".BANK_ACCOUNT.",".CASH_ACCOUNT.")   ORDER BY acctbl.group_id,led.ledger_name ASC ";
		  	//new one
			$sql="select led.id as LEDID ,concat_ws(' ', led.ledger_name,'(',acctbl.category_name, ')') as LEDNAME,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id  where led.categoryid NOT IN (".DUE_FROM_MEMBERS.",".BANK_ACCOUNT.",".CASH_ACCOUNT.")   ORDER BY acctbl.group_id,led.ledger_name ASC"; 
			$memId = $this->m_dbConn->select($sql);
		}	
		else if($type == 3)
		{
			// old one.
			//$sql = "select led.id as LEDID,led.ledger_name as LEDNAME,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id  where led.categoryid IN (".BANK_ACCOUNT.")  ORDER BY acctbl.group_id,led.ledger_name ASC";
		 	// new one.
			$sql = "select led.id as LEDID,concat_ws(' ', led.ledger_name,'(',acctbl.category_name, ')') as LEDNAME,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id  where led.categoryid IN (".BANK_ACCOUNT.")  ORDER BY acctbl.group_id,led.ledger_name ASC";
			$memId = $this->m_dbConn->select($sql);
		}	
		else if($type == 4)
		{ 
		// old one
	 	//$sql = "select led.id as LEDID,led.ledger_name as LEDNAME,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id  where led.categoryid  IN (".CASH_ACCOUNT.")  ORDER BY acctbl.group_id,led.ledger_name ASC"; //neww one
		$sql = "select led.id as LEDID,concat_ws(' ', led.ledger_name,'(',acctbl.category_name, ')') As LEDNAME,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl on led.categoryid =acctbl.category_id where led.categoryid IN (".CASH_ACCOUNT.") ORDER BY acctbl.group_id,led.ledger_name ASC
";  
			$memId = $this->m_dbConn->select($sql);
		}	
	
		else if($type == 5)		//Liability
		{ 
		//old one
		//$sql = "select led.id as LEDID,led.ledger_name as LEDNAME,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id  WHERE acctbl.group_id = '".LIABILITY."'   ORDER BY acctbl.group_id,led.ledger_name ASC";
		 // new one.
		$sql = "select led.id as LEDID,concat_ws('  ', led.ledger_name,'(',acctbl.category_name, ')') As LEDNAME,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id  WHERE acctbl.group_id = '".LIABILITY."'   ORDER BY acctbl.group_id,led.ledger_name ASC";

			$memId = $this->m_dbConn->select($sql);
			
		}	
		else if($type == 6)		//Assets
		{
			
			//$sql1 = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led , `account_category` as acctbl, `unit` AS u where led.categoryid =acctbl.category_id AND led.id = u.unit_id AND led.categoryid = '".DUE_FROM_MEMBERS."'  ORDER BY acctbl.group_id,u.sort_order ASC ";
			
			//$sql = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id WHERE acctbl.group_id = '".ASSET."' AND led.categoryid <> '".DUE_FROM_MEMBERS."' ORDER BY acctbl.group_id,led.ledger_name ASC";
			$sql = "select led.id as LEDID,concat_ws(' ', led.ledger_name,'(',acctbl.category_name, ')') As LEDNAME,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id  WHERE acctbl.group_id = '".ASSET."' AND led.categoryid <> '".DUE_FROM_MEMBERS."'  ORDER BY acctbl.group_id,led.ledger_name ASC";
			$memId = $this->m_dbConn->select($sql);
		}	
		else if($type == 7)		//Income
		{
			
			//$sql = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id WHERE acctbl.group_id = '".INCOME."' ORDER BY acctbl.group_id,led.ledger_name ASC";
			
			$sql = "select led.id as LEDID,concat_ws('  ', led.ledger_name,'(',acctbl.category_name, ')') As LEDNAME,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id  WHERE acctbl.group_id = '".INCOME."'   ORDER BY acctbl.group_id,led.ledger_name ASC";
			$memId = $this->m_dbConn->select($sql);
		}
		else if($type == 8)		//Expense
		{
			
			//$sql = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id WHERE acctbl.group_id = '".EXPENSE."' ORDER BY acctbl.group_id,led.ledger_name ASC";
			
			$sql = "select led.id as LEDID,concat_ws('  ', led.ledger_name,'(',acctbl.category_name, ')') As LEDNAME,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id  WHERE acctbl.group_id = '".EXPENSE."'   ORDER BY acctbl.group_id,led.ledger_name ASC";
			$memId = $this->m_dbConn->select($sql);
		}
		//echo "Query sql : " . $sql;
				
		
		$ledDetails = array();
		
        if(sizeof($memId)>0)
        { 
            
			for($i = 0; $i < sizeof($memId); $i++)
          	{ 
			$ledDetails[$i]['LedID'] = $memId[$i]["LEDID"] ;
			$ledDetails[$i]['LedName'] = $memId[$i]["LEDNAME"];
          	}
        	
        }
		else
		{
			$ledDetails[0]['LedID'] = "No Data";
		}
		
        return $ledDetails;
      }
public function getGroupMembers1($type)
    {
		$sql = "";
		$memId =  array();
		$ledDetails = "";
		if($type == 1)
		{	
			$sql = "select led.id as LEDID,led.ledger_name as LEDNAME,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led, `account_category` as acctbl, `unit` AS u where led.categoryid =acctbl.category_id AND led.id = u.unit_id AND led.categoryid = '".DUE_FROM_MEMBERS."'   ORDER BY acctbl.group_id,u.sort_order ASC";
			$memId = $this->m_dbConn->select($sql);
		}
		else if($type == 2)
		{
			$sql = "select led.id as LEDID,led.ledger_name as LEDNAME,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id  where led.categoryid NOT IN (".DUE_FROM_MEMBERS.",".BANK_ACCOUNT.",".CASH_ACCOUNT.")   ORDER BY acctbl.group_id,led.ledger_name ASC ";
			$memId = $this->m_dbConn->select($sql);
		}	
		else if($type == 3)
		{
			$sql = "select led.id as LEDID,led.ledger_name as LEDNAME,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id  where led.categoryid IN (".BANK_ACCOUNT.")  ORDER BY acctbl.group_id,led.ledger_name ASC";
			$memId = $this->m_dbConn->select($sql);
		}	
		else if($type == 4)
		{
			$sql = "select led.id as LEDID,led.ledger_name as LEDNAME,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id  where led.categoryid  IN (".CASH_ACCOUNT.")  ORDER BY acctbl.group_id,led.ledger_name ASC";
			$memId = $this->m_dbConn->select($sql);
		}	
		
		
        if(sizeof($memId)>0)
        { 
            $ledDetails="<input type='checkbox' id ='0' class='checkBox' name='mem_id[]' checked/>";
			for($i = 0; $i < sizeof($memId); $i++)
          	{ 
			
            	$ledDetails .= "<input type='checkbox' id='".$memId[$i]['LEDID']."' class='checkBox' name='".$memId[$i]['LEDID']."'/>";
          	}
        
        }
		else
		{
			$ledDetails="No Members found under selected category.";
		}
		
        return($ledDetails);
      }
	  
	  
	  public function fetchLedgerIDArray1($type)
{
	 $sql = "select led.id,led.ledger_name,led.categoryid ,acctbl.group_id,led.payment from `ledger` as led join `account_category` as acctbl  on led.categoryid =acctbl.category_id WHERE led.id in(".$type.") ORDER BY acctbl.group_id,led.ledger_name ASC ";
			
		
		$sqlResult = $this->m_dbConn->select($sql);
		return $sqlResult;
		
}
function getChallanList($st_date= '',$end_date='')
{
		// $sql = "SELECT * FROM `challanregister`";
	if($st_date != '' && $end_date != '')
	{
		 $sql ="SELECT cr.*,l.ledger_name FROM `tds_challanregister` as cr join ledger as l on l.id = cr.LedgerId where Challan_date between '".$st_date."' and '".$end_date."'";

	}
	else
	{
		 $sql ="SELECT cr.*,l.ledger_name FROM `tds_challanregister` as cr join ledger as l on l.id = cr.LedgerId";
	}
	//echo $sql;
	$sqlResult = $this->m_dbConn->select($sql);
		 return $sqlResult;
	}
	function getChallanPrint($id)
	{
		// $sql = "SELECT * FROM `challanregister`";
		$sql ="SELECT cr.*,l.ledger_name FROM `tds_challanregister` as cr join ledger as l on l.id = cr.LedgerId where cr.id='".$id."' ";
		 $sqlResult = $this->m_dbConn->select($sql);
		 
		 $sql1 ="SELECT * FROM `bank_master` where BankID= '".$sqlResult[0]['BankId']."'";
		 $sqlResult1 = $this->m_dbConn->select($sql1);
		 $sqlResult[0]['BankName']   =  $sqlResult1[0]['BankName'];
		 $sqlResult[0]['BranchName'] =  $sqlResult1[0]['BranchName'];
		 return $sqlResult;
	}
   function GetInvoiceLedger($VoucheNo)
   {
	  //echo "VOucher No." .$VoucheNo;
	  $sql="SELECT * FROM `invoicestatus` where TDSVoucherNo ='".$VoucheNo."'";
	  $sqlResult = $this->m_dbConn->select($sql);
	  
	  if($sqlResult <> '')
	  {
		 
			   $ClearVoucherNo = $sqlResult[0]['InvoiceClearedVoucherNo'];
			   $GrossAmount = $sqlResult[0]['InvoiceChequeAmount'];
		 	    $sql1="SELECT `By` FROM `voucher` where VoucherNo = '".$ClearVoucherNo."'";
		       $sqlResult1 = $this->m_dbConn->select($sql1);
			   
		      if($sqlResult1[0]['By'] <> '')
		      {
			    $sqlResult[0]['BankID'] = $sqlResult1[0]['By']; 
		      }
			  else
			  {
				  $sqlResult[0]['BankID'] =0;
			  }
		
		  
	  }
	  
	  return $sqlResult;
   }
   // GetGroup ID from TDS Deductee Report
   function GetGroupID($LedgerID)
   {
	   $sql= "SELECT ac.group_id FROM `ledger` as l join `account_category` as ac on l.categoryid=ac.category_id where l.id ='".$_SESSION['default_tds_payable']."'";
	    $sqlResult = $this->m_dbConn->select($sql);
		return $sqlResult[0]['group_id'];
   }
   
   function GetBankName($BankId)
   {
	   $sql= "SELECT BankName FROM `bank_master` where BankID = '".$BankId."'";
	   $sqlResult = $this->m_dbConn->select($sql);
	   return $sqlResult[0]['BankName'];
   }
   
   public function Reportdetails($voucherType,$fromDate,$toDate)
   {
	   $ledgername_array = array();
	   $Voucher_no_array = array();
	   $categoryid['group'] =0;
	   
	   /*$sql="SELECT InvoiceClearedVoucherNo,TDSVoucherNo FROM `invoicestatus`";
	   $result01 = $this->m_dbConn->select($sql);
		for($i = 0; $i < sizeof($result01); $i++)
		{
			array_push( $Voucher_no_array, array("ClearVoucher"=>$result01[$i]['InvoiceClearedVoucherNo'],"TDSVoucherNo"=>$result01[$i]['TDSVoucherNo'] ));
		}*/
		$get_ledger_name = "select id,ledger_name from `ledger`";
		$result02 = $this->m_dbConn->select($get_ledger_name);
		for($i = 0; $i < sizeof($result02); $i++)
		{
			$ledgername_array[$result02[$i]['id']] = $result02[$i]['ledger_name'];	
		}
		
		$sql002 = "select * from `voucher` where VoucherTypeID='".$voucherType."' and `Date` Between '".getDBFormatDate($fromDate)."' and '".getDBFormatDate($toDate)."' ";
		 $data3 = $this->m_dbConn->select($sql002);
		
		for($i = 0; $i < sizeof($data3); $i++)
		{
			 $data3[$i]['ledger_name'] = $ledgername_array[$data3[$i]['ledger_name']];
		}
		
		if($data3 <> "")
		{
			echo $table1 =  "<table id='reports' style='text-align:left; width:100%;' class='table table-bordered table-hover table-striped'>	
						<tr style='border:1px solid #ddd;' >
							<th style='text-align:center;border:1px solid #ddd;width: 100px;'>Date</th>
							<th style='text-align:center;border:1px solid #ddd;width: 220px;'>Perticulars</th>
							<th style='text-align:center;border:1px solid #ddd;width: 100px;'>Debit Amount</th>
							<th style='text-align:center;border:1px solid #ddd;width: 100px;'>Credit Amount</th>
							<th style='text-align:center;border:1px solid #ddd;width: 100px;'>Type</th>
							<th style='text-align:center;border:1px solid #ddd;width: 80px;'>Voucher No</th>
							<th style='text-align:center;border:1px solid #ddd;width: 300px;'>Narration</th>
						</tr>";
		
			foreach($data3 as $k => $v)
			{
				$voucher_type =$this->get_voucher_details($data3[$k]['VoucherTypeID']);
				if($data3[$k]['By'] <> "")
				{
				 	echo "<tr style='border:1px solid #ddd;'>";
					echo "<td style='text-align:left;border:1px solid #ddd;'>".$data3[$k]['Date']."</td>";
					echo "<td style='text-align:left;border:1px solid #ddd;'>".$ledgername_array[$data3[$k]['By']]."</td>";
					if($data3[$k]['Debit'] <> 0)						
					{
						echo "<td style='text-align:right;border:1px solid #ddd;'>".$data3[$k]['Debit']."</td>";
					}
					else
					{
						echo "<td style='text-align:right;border:1px solid #ddd;'> </td>";
					}
					if($data3[$k]['Credit'] <> 0)
					{
					 	echo "<td style='text-align:right;border:1px solid #ddd;'>".$data3[$k]['Credit']."</td>";
					}
					else
					{
						echo "<td style='text-align:right;border:1px solid #ddd;'></td>";
					}
					echo "<td style='text-align:center;border:1px solid #ddd;'>".$voucher_type."</td>";
					echo "<td style='text-align:center;border:1px solid #ddd;'><a href='#' onClick='ViewVoucherDetail(".$data3[$k]['By'].", ".$categoryid['group'].", ".$data3[$k]['VoucherTypeID'].", ".$data3[$k]['id'].")'>".$data3[$k]['VoucherNo']."</a></td>";
					echo "<td style='text-align:left;border:1px solid #ddd;'>".$data3[$k]['Note']."</td>";
					echo "</tr>";
				}
				else
				{
					echo "<tr style='border:1px solid #ddd;' >";
					echo "<td style='text-align:left;border:1px solid #ddd;'>".$data3[$k]['Date']."</td>";
					echo "<td style='text-align:left;border:1px solid #ddd;'>".$ledgername_array[$data3[$k]['To']]."</td>";
					if($data3[$k]['Debit'] <> 0)
					{
						echo "<td style='text-align:right;border:1px solid #ddd;'>".$data3[$k]['Debit']."</td>";
					}
					else
					{
						echo "<td style='text-align:right;border:1px solid #ddd;'></td>";
					}
					if($data3[$k]['Credit'] <> 0)
					{
						echo "<td style='text-align:right;border:1px solid #ddd;'>".$data3[$k]['Credit']."</td>";
					}
					else
					{
						echo "<td style='text-align:right;border:1px solid #ddd;'>0.00</td>";
					}
					echo "<td style='text-align:center;border:1px solid #ddd;'>".$voucher_type."</td>";
					echo "<td style='text-align:center;border:1px solid #ddd;'><a href='#' onClick='ViewVoucherDetail(".$data3[$k]['To'].", ".$categoryid['group'].", ".$data3[$k]['VoucherTypeID'].", ".$data3[$k]['id'].")'>".$data3[$k]['VoucherNo']."</a></td>";
					echo "<td style='text-align:left;border:1px solid #ddd;'>".$data3[$k]['Note']."</td>";	
					echo "</tr>";
				}
			}
			$table1 .=  "</table>";
		}
	}
}
?>





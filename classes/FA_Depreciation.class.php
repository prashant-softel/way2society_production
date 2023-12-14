<?php
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once("utility.class.php");
include_once("dbconst.class.php");
include_once("register.class.php");
include_once("voucher.class.php");
include_once("latestcount.class.php");

class fa_dep extends dbop
{
	public $actionPage = "../ledger.php";
	public $m_dbConn;
	private $obj_utility;
	private $obj_register;
	public $isCatError;
	public $obj_voucher;
	public $obj_latestcount;
	public $ShowDebugTrace;
	
	function __construct($dbConn)
	{
		$this->ShowDebugTrace = 0;
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);

		/*//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);*/

		$this->obj_utility = new utility($this->m_dbConn);
		$this->obj_register = new regiser($this->m_dbConn);
		$this->obj_voucher = new voucher($this->m_dbConn);
		$this->obj_latestcount = new latestCount($this->m_dbConn);
		$this->isCatError = false;
		////dbop::__construct();
	}

	public function startProcess()
	{
		$errorExists = 0;

		/*//$curdate 		=  $this->curdate;
		//$curdate_show	=  $this->curdate_show;
		//$curdate_time	=  $this->curdate_time;
		//$ip_location	=  $this->ip_location;*/
		

	}
	
	public function combobox($query, $id, $defaultString = '', $defaultValue = '')
	{
		if($defaultString <> '')
		{		
			$str.="<option value='" . $defaultValue . "'>" . $defaultString . "</option>";
		}
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
	public function display1($rsas)
	{
		/*$thheader = array('Category','Ledger','Opening Balance','Type','Show_In_Bill','Taxable','Sale','Purchase','Income','Expense','Payment','Receipt','Note','Opening Date');*/
		$thheader = array('Category','Ledger','Depreciation Rate','Opening Balance','Purchased Before 30 Sept','Purchased After 30 Sept','Sold Before 30 Sept','Sold After 30 Sept','Type','Depreciation','Closing Balance','Note');
		$this->display_pg->edit		= "getaccount_subcategory";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "ledger.php";
		$this->display_pg->view		= "getaccount_subcategory";
		$this->display_pg->print		= "getaccount_subcategory";
		//$res = $this->display_pg->display_new($rsas);
		$res = $this->display_pg->display_datatable($rsas, false /*Show Edit Option*/, false /*Hide Delete Option*/,true /*Show View Option*/,false);
		return $res;
	}
	
	public function pgnation()
	{
		//$sql1 = "select ledger_table.id,ledger_table.society_id,Account.category_name,ledger_table.ledger_name,ledger_table.show_in_bill,ledger_table.taxable,ledger_table.sale,ledger_table.purchase,ledger_table.income,ledger_table.expense,ledger_table.payment,ledger_table.receipt,ledger_table.opening_type,ledger_table.opening_balance,ledger_table.note from `account_category` as `Account` Join `ledger` as `ledger_table` where Account.category_id = ledger_table.categoryid";
		
		/*$sql1 = "select ledger_table.id,CONCAT(Account.category_name,CONCAT('(',Account.category_id,')')) as 'Category Name',CONCAT(ledger_table.ledger_name, CONCAT('(',ledger_table.id,')')) as 'ledger',ledger_table.opening_balance,IF(ledger_table.opening_type = '0', 'None',IF(ledger_table.opening_type = '1', 'Credit',IF(ledger_table.opening_type = '2', 'Debit',''))) as opening_type, ledger_table.show_in_bill,ledger_table.taxable,ledger_table.sale,ledger_table.purchase,ledger_table.income,ledger_table.expense,ledger_table.payment,ledger_table.receipt,ledger_table.note,DATE(DATE_ADD(ledger_table.opening_date, INTERVAL 1 DAY)) as opening_date from `account_category` as `Account`,`ledger` as `ledger_table`, `society` as `society_table` where Account.category_id = ledger_table.categoryid and society_table.society_id = ledger_table.society_id and society_table.society_id =".$_SESSION['society_id']; */
		//echo $sql1 = "select ledger_table.id,CONCAT(Account.category_name,CONCAT('(',Account.category_id,')')) as 'Category Name',CONCAT(ledger_table.ledger_name, CONCAT('(',ledger_table.id,')')) as 'ledger',FORMAT(ledger_table.opening_balance,2) as opening_balance,IF(ledger_table.opening_type = '0', 'None',IF(ledger_table.opening_type = '1', 'Credit',IF(ledger_table.opening_type = '2', 'Debit',''))) as opening_type, ledger_table.show_in_bill,ledger_table.note,DATE_FORMAT(DATE(DATE_ADD(ledger_table.opening_date, INTERVAL 1 DAY)),'%d-%m-%Y') as opening_date from `account_category` as `Account`,`ledger` as `ledger_table`, `society` as `society_table` where Account.category_id = ledger_table.categoryid and society_table.society_id = ledger_table.society_id and society_table.society_id =".$_SESSION['society_id']; 
	 //use this query $sql1 ="select ledger_table.id,CONCAT(Account.category_name,CONCAT('(',Account.category_id,')')) as 'Category Name',CONCAT(ledger_table.ledger_name) as 'ledger',ld.GSTIN_No,FORMAT(ledger_table.opening_balance,2) as opening_balance,IF(ledger_table.opening_type = '0', 'None',IF(ledger_table.opening_type = '1', 'Credit',IF(ledger_table.opening_type = '2', 'Debit',''))) as opening_type, ledger_table.show_in_bill,ledger_table.note,DATE_FORMAT(DATE(DATE_ADD(ledger_table.opening_date, INTERVAL 1 DAY)),'%d-%m-%Y') as opening_date  from `account_category` as `Account` join `ledger` as `ledger_table` on Account.category_id = ledger_table.categoryid join `society` as `society_table` on society_table.society_id = ledger_table.society_id left join `ledger_details` as ld on ledger_table.id=ld.LedgerID where society_table.society_id ='".$_SESSION['society_id']."' AND `Account`.`category_id` = '16' order by ledger_table.id";
	 // $sql1 ="select ledger_table.id,CONCAT(Account.category_name,CONCAT('(',Account.category_id,')')) as 'Category Name',CONCAT(ledger_table.ledger_name, CONCAT('(',ledger_table.id,')')) as 'ledger',ld.GSTIN_No,FORMAT(ledger_table.opening_balance,2) as opening_balance,IF(ledger_table.opening_type = '0', 'None',IF(ledger_table.opening_type = '1', 'Credit',IF(ledger_table.opening_type = '2', 'Debit',''))) as opening_type, ledger_table.show_in_bill,ledger_table.note,DATE_FORMAT(DATE(DATE_ADD(ledger_table.opening_date, INTERVAL 1 DAY)),'%d-%m-%Y') as opening_date  from `account_category` as `Account` join `ledger` as `ledger_table` on Account.category_id = ledger_table.categoryid join `society` as `society_table` on society_table.society_id = ledger_table.society_id left join `ledger_details` as ld on ledger_table.id=ld.LedgerID where society_table.society_id ='".$_SESSION['society_id']."' order by ledger_table.id";
	 
	 $sql4 = "SELECT `category_id` FROM `account_category` WHERE `parentcategory_id` = '".FIXED_ASSET."'";
	 $sql4_res = $this->m_dbConn->select($sql4);
	 
	 $category_ids = FIXED_ASSET.",";
	 for($i = 0; $i < sizeof($sql4_res); $i++)
	 {
		 $category_ids .= $sql4_res[$i]['category_id'].",";
	 }
	 
	 //echo $category_ids;
	 $category_ids = rtrim($category_ids,",");
	 //echo $category_ids;
	 	 
	 $sql1 ="select ledger_table.id,CONCAT(Account.category_name,CONCAT('(',Account.category_id,')')) as 'Category Name',CONCAT(ledger_table.ledger_name) as 'ledger',FORMAT(ledger_table.opening_balance,2) as opening_balance,IF(ledger_table.opening_type = '0', 'None',IF(ledger_table.opening_type = '1', 'Credit',IF(ledger_table.opening_type = '2', 'Debit',''))) as opening_type, ledger_table.note from `account_category` as `Account` join `ledger` as `ledger_table` on Account.category_id = ledger_table.categoryid join `society` as `society_table` on society_table.society_id = ledger_table.society_id left join `ledger_details` as ld on ledger_table.id=ld.LedgerID where society_table.society_id ='".$_SESSION['society_id']."' AND `Account`.`category_id` IN (".$category_ids.") order by ledger_table.id";
		$result = $this->m_dbConn->select($sql1);

		//print_r($_SESSION);
		for($i = 0; $i < sizeof($result); $i++)
		{			
			$sql3 = "SELECT `FixedAssetID`,`DepreciationPercent` as `Depreciation Rate`,`EndingValue` as `Closing Balance`,`Depreciation`, `PurchaseDate` FROM `fixedassetlist` WHERE `LedgerID` = '".$result[$i]['id']."' AND `YearID` = '".$_SESSION['default_year']."'";
			$sql3_res = $this->m_dbConn->select($sql3);
			
			if($sql3_res <> "")
			{
				$result[$i]['Purchase Date'] = $sql3_res[0]['PurchaseDate'];
				$result[$i]['Depreciation'] = $sql3_res[0]['Depreciation'];
				$result[$i]['Depreciation Rate'] = $sql3_res[0]['Depreciation Rate'];
				$result[$i]['Closing Balance'] = $sql3_res[0]['Closing Balance'];								
			}
			else
			{
				$result[$i]['Depreciation'] = "";
				$result[$i]['Depreciation Rate'] = "";
				$result[$i]['Closing Balance'] = "";	
			}
			
			$DepreciationNoteQuery = "SELECT Note FROM voucher WHERE RefNo = '".$sql3_res[0]['FixedAssetID']."' AND RefTableID = '".TABLE_FIXEDASSETLIST."'";
			$DepreciationNote = $this->m_dbConn->select($DepreciationNoteQuery);
			$result[$i]['Note'] = $DepreciationNote[0]['Note'];
			
/*			$sql2 = "SELECT `PurchaseDate` FROM `fixedassetlist` WHERE `LedgerID` = '".$result[$i]['id']."' LIMIT 1";
			$sql2_res = $this->m_dbConn->select($sql2);
			
			$display_date = getDisplayFormatDate($sql2_res[0]['PurchaseDate']);			
			$result[$i]['Purchase Date'] = $display_date;
*/
			$LedgerID = $result[$i]['id'];

			$ClosingDate = $this->obj_utility->GetDateByOffset2($_SESSION['default_year_end_date'], -1);
		//Using 30-03 date to avoid pick up of depreciation value

//******************


//			echo "<BR><BR>ONe";
			$this->getFixAssetValues($LedgerID, $sql_op_bal, $halfyear_bal, $halfyear_bal_sale, $second_halfyear_bal, $second_halfyear_bal_sale);

			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>LedgerID " . $LedgerID;
				print_r($sql_op_bal_res );
				echo "<BR>sql_op_bal<BR>";
				echo "<BR>sql_op_bal =  $sql_op_bal";
						
				echo "<BR>sql_halfyear<BR>";
				print_r($sql_halfyear_res );
				echo "<BR>halfyear_bal = ". $halfyear_bal;
				echo "<BR>halfyear_bal_sale = ". $halfyear_bal_sale;

				echo "<BR>sql_second_halfyear<BR>";
				print_r($sql_second_halfyear_res );
				echo "<BR>second_halfyear_bal =  $second_halfyear_bal";
				echo "<BR>second_halfyear_bal_sale =  $second_halfyear_bal_sale";
				echo "<BR><BR>";
			}

			$url = "window.open('FixedAssetDep.php?id=".$result[$i]['id']."','_blank')";
//			$result[$i]['opening_balance'] = '<a onClick="'.$url.'">'.$result[$i]['opening_balance'].'</a>';
			$result[$i]['opening_balance'] = $sql_op_bal;
			$result[$i]['sql_op_bal'] = $sql_op_bal;
			$result[$i]['opening_balance'] = '<a onClick="'.$url.'">'.$sql_op_bal .'</a>';
			//$result[$i]['ledger'] = '<a onClick="'.$url.'">'.$result[$i]['ledger'] .'</a>';
			$result[$i]['half_year_bal'] = $halfyear_bal;
			$result[$i]['half_year_bal_sale'] = $halfyear_bal_sale;
			$result[$i]['second_half_year_bal'] = $second_halfyear_bal ; 
			$result[$i]['second_half_year_bal_sale'] = $second_halfyear_bal_sale; 
		}

		//print_r($result[1]);
		
		// Re arrange the data in a particular sequence
		
		$FormatedData = array();
		$i=0;
		for($i = 0 ; $i < count($result); $i++)
		{
			//echo "<BR>Value " . 
			$value = $result[$i]['sql_op_bal'] + $result[$i]['half_year_bal'] - $result[$i]['half_year_bal_sale']+ $result[$i]['second_half_year_bal'] - $result[$i]['second_half_year_bal_sale'] ;	
			//if($result[$i]['opening_balance'] > 0 || $result[$i]['half_year_bal'] > 0 || $result[$i]['half_year_bal_sale'] > 0 ||$result[$i]['second_half_year_bal'] > 0||$result[$i]['second_half_year_bal_sale'] > 0) 
			if($value>0)
			{
			$FormatedData[$i]['id'] = $result[$i]['id'];
			$FormatedData[$i]['Category Name'] = $result[$i]['Category Name'];
			//$FormatedData[$i]['Purchase Date'] = $result[$i]['Purchase Date'];
			$FormatedData[$i]['ledger'] = $result[$i]['ledger'];
			$FormatedData[$i]['Depreciation Rate'] = $result[$i]['Depreciation Rate'];
			$FormatedData[$i]['opening_balance'] = $result[$i]['opening_balance'];
			$FormatedData[$i]['less_than_183'] = $result[$i]['half_year_bal'] ;
			$FormatedData[$i]['greater_than_183'] = $result[$i]['second_half_year_bal'] ;
			
			
//			$FormatedData[$i]['Sold During Year'] = '0';
			$FormatedData[$i]['Sold Before 30 Sept'] = $result[$i]['half_year_bal_sale'] ;;
			$FormatedData[$i]['Sold After 30 Sept'] =  $result[$i]['second_half_year_bal_sale'];
			
			$FormatedData[$i]['opening_type'] = $result[$i]['opening_type'];
			$FormatedData[$i]['Depreciation'] = $result[$i]['Depreciation'];
			$FormatedData[$i]['Closing Balance'] = $result[$i]['Closing Balance'];
			$FormatedData[$i]['Note'] = $result[$i]['Note'];
			}
			//$i++;
		}
		$this->display1($FormatedData);
	}
	
	public function selecting()
	{
		alert("This code required");
		return;
		
		$sql = "select id,`society_id`,`categoryid`,`ledger_name`,`show_in_bill`,`taxable`,`sale`,`purchase`,`income`,`expense`,`payment`,`receipt`,`opening_type`,`opening_balance`,`note`,`opening_type`,DATE_FORMAT(opening_date, '%d-%m-%Y') as opening_date,`supplementary_bill` from ledger where id='".$_REQUEST['account_subcategoryId']."'";
		$res = $this->m_dbConn->select($sql);
		
		
		//$sqlDate = "select Date from assetregister where LedgerID = '" . $res[0]['id'] . "' and Is_Opening_Balance = 1" ;
		//$resDate = $this->m_dbConn->select($sqlDate);
		
		$group_id = $this->obj_utility->getParentOfLedger($res[0]['id']);
		
		/*$sDate = '';
		if($resDate <> '')
		{
			$sDate = $resDate[0]['Date'];
		}
		*/
		//$res[0]['Date'] = getDisplayFormatDate($sDate);
		$res[0]['Group'] = $group_id['group'];
		if($res <> '')
		{
		for($i=0;$i<sizeof($res);$i++)
		{
		$select="select * from `ledger_details` where LedgerID='".$res[$i]['id']."' and status='Y'";
		$res1 = $this->m_dbConn->select($select);
		
		$res[0]['GSTIN_No']=$res1[$i]['GSTIN_No'];
		$res[0]['PAN_No']=$res1[$i]['PAN_No'];
		}
		}
		return $res;
	}
	public function deleting()
	{
		alert("This code required");
		return;
		$sql = "update ledger set status='N' where id='".$_REQUEST['account_subcategoryId']."'";
		$res = $this->m_dbConn->update($sql);
		$sql1="update ledger_details set status='N' where LedgerID='".$_REQUEST['account_subcategoryId']."'";
		$res1 = $this->m_dbConn->update($sql1);
	}
	
	
	public function FetchDate($default_year)
	{
		$sql = "select DATE_FORMAT(BeginingDate, '%d-%m-%Y') as BeginingDate from `year` where `YearID`='".$default_year."' ";
		$res = $this->m_dbConn->select($sql);
		return  $this->obj_utility->GetDateByOffset($res[0]['BeginingDate'],-1);
	}
	
	/*
	function moveRegEntriesAccordingToGroupChange($ledgerID, $prevGroup, $currentGroup, $prevCatID, $currentCatID)
	{
		$entries = array();	
		$oldIDs = "";
		$newIDs = "";	
		
		if($currentCatID == BANK_ACCOUNT || $currentCatID == CASH_ACCOUNT)
		{
			if($prevGroup == LIABILITY)
			{
				$countQuery = "SELECT Count(*) AS cnt FROM `liabilityregister` WHERE `LedgerID` = '".$ledgerID."' AND `Is_Opening_Balance` != 1";
			}
			else if($prevGroup == EXPENSE)
			{
				$countQuery = "SELECT count(*) AS cnt FROM `expenseregister` WHERE `LedgerID` = '".$ledgerID."'";  
			}
			else if($prevGroup == INCOME)
			{
				$countQuery = "SELECT count(*) AS cnt FROM `incomeregister` WHERE `LedgerID` = '".$ledgerID."'"; 
			}
			$count = $this->m_dbConn->select($countQuery);
			if($count[0]['cnt'] > 0)
			{
				return "Not Allowed";
			}
		}
		if($prevCatID == BANK_ACCOUNT || $prevCatID == CASH_ACCOUNT)
		{
			$selectQuery = "SELECT count(*) AS cnt FROM `bankregister` WHERE `LedgerID` = '".$ledgerID."' AND `Is_Opening_Balance` != 1";
			$count = $this->m_dbConn->select($selectQuery); 
			if($count[0]['cnt'] > 0)
			{
				return "Changing of GroupID is Not Allowed";
			}
		}
		
		if($prevGroup == LIABILITY)
		{
			$selectQuery = "SELECT * FROM `liabilityregister` WHERE `LedgerID` = '".$ledgerID."' AND `Is_Opening_Balance` != 1";
			$entries = $this->m_dbConn->select($selectQuery); 	
			$deleteQuery = "DELETE FROM `liabilityregister` WHERE `LedgerID` = '".$ledgerID."'";
			$this->m_dbConn->delete($deleteQuery);
		}
		else if($prevCatID == BANK_ACCOUNT || $prevCatID == CASH_ACCOUNT)
		{
			
		}
		else if($prevGroup == ASSET)
		{
			$selectQuery = "SELECT * FROM `assetregister` WHERE `LedgerID` = '".$ledgerID."' AND `Is_Opening_Balance` != 1";	
			$entries = $this->m_dbConn->select($selectQuery);
			$deleteQuery = "DELETE FROM `assetregister` WHERE `LedgerID` = '".$ledgerID."'";	
			$this->m_dbConn->delete($deleteQuery); 	
		}
		else if($prevGroup == EXPENSE)
		{
			$selectQuery = "SELECT * FROM `expenseregister` WHERE `LedgerID` = '".$ledgerID."'";
			$entries = $this->m_dbConn->select($selectQuery); 	
			$deleteQuery = "DELETE FROM `expenseregister` WHERE `LedgerID` = '".$ledgerID."'";	
			$this->m_dbConn->delete($deleteQuery);	
		}
		else if($prevGroup == INCOME)
		{
			$selectQuery = "SELECT * FROM `incomeregister` WHERE `LedgerID` = '".$ledgerID."'";	
			$entries = $this->m_dbConn->select($selectQuery); 	
			$deleteQuery = "DELETE FROM `incomeregister` WHERE `LedgerID` = '".$ledgerID."'";	
			$this->m_dbConn->delete($deleteQuery);
		}
		
		if($currentGroup == LIABILITY)
		{
			for($i = 0; $i < sizeof($entries); $i++)
			{
				$oldIDs .= $entries[$i]['id'] . "|";
				$transactionDetails = $this->getTransactionDetails($entries, $i);
				//$insertLiability = $this->obj_register->SetLiabilityRegister(getDBFormatDate($entries[$i]['Date']), $ledgerID, $entries[$i]['VoucherID'], $entries[$i]['VoucherTypeID'], $transactionDetails[0], $transactionDetails[1], 0);	
				$sqlInsert = "INSERT INTO `liabilityregister`(`Date`, `CategoryID`, `SubCategoryID`, `LedgerID`, `VoucherID`, `VoucherTypeID`, `" . $transactionDetails[0] . "`, `Is_Opening_Balance`) VALUES ('" . getDBFormatDate($entries[$i]['Date']) . "', '" . $currentGroup . "', '" . $currentCatID . "', '" . $ledgerID . "', '" . $entries[$i]['VoucherID'] . "',  '" . $entries[$i]['VoucherTypeID'] . "', '" . $transactionDetails[1] . "', '0')";
				$insertLiability = $this->m_dbConn->insert($sqlInsert);
				$newIDs .= $insertLiability . "|";
			}
		}
		else if($currentCatID == BANK_ACCOUNT || $currentCatID == CASH_ACCOUNT)
		{
			//
		}
		else if($currentGroup == ASSET)
		{			
			for($i = 0; $i < sizeof($entries); $i++)
			{
				$oldIDs .= $entries[$i]['id'] . "|";
				$transactionDetails = $this->getTransactionDetails($entries, $i);
				//$insertAsset = $this->obj_register->SetAssetRegister(getDBFormatDate($entries[$i]['Date']), $ledgerID, $entries[$i]['VoucherID'], $entries[$i]['VoucherTypeID'], $transactionDetails[0], $transactionDetails[1], 0);	
				$sqlInsert = "INSERT INTO `assetregister`(`Date`, `CategoryID`, `SubCategoryID`, `LedgerID`, `VoucherID`, `VoucherTypeID`, `" . $transactionDetails[0] . "`, `Is_Opening_Balance`) VALUES ('" . getDBFormatDate($entries[$i]['Date']) . "', '" . $currentGroup . "', '" . $currentCatID . "', '" . $ledgerID . "', '" . $entries[$i]['VoucherID'] . "',  '" . $entries[$i]['VoucherTypeID'] . "', '" . $transactionDetails[1] . "', '0')";
				$insertAsset = $this->m_dbConn->insert($sqlInsert);
				$newIDs .= $insertAsset . "|";				
			}
		}
		else if($currentGroup == INCOME)
		{
			for($i = 0; $i < sizeof($entries); $i++)
			{
				$oldIDs .= $entries[$i]['id'] . "|";
				$transactionDetails = $this->getTransactionDetails($entries, $i);
				$insertIncome = $this->obj_register->SetIncomeRegister($ledgerID, getDBFormatDate($entries[$i]['Date']), $entries[$i]['VoucherID'], $entries[$i]['VoucherTypeID'], $transactionDetails[0], $transactionDetails[1]);	
				$newIDs .= $insertIncome . "|";
			}
		}
		else if($currentGroup == EXPENSE)
		{
			for($i = 0; $i < sizeof($entries); $i++)
			{
				$oldIDs .= $entries[$i]['id'] . "|";
				$transactionDetails = $this->getTransactionDetails($entries, $i);				
				$insertExpense = $this->obj_register->SetExpenseRegister($ledgerID, getDBFormatDate($entries[$i]['Date']), $entries[$i]['VoucherID'], $entries[$i]['VoucherTypeID'], $transactionDetails[0], $transactionDetails[1]);					
				$newIDs .= $insertExpense . "|";
			}
		}
		$logMsg .= "<br />Records moved - ";
		$logMsg .= " oldGroup<".$prevGroup."> :".$oldIDs . "<br /> newGroup<".$currentGroup."> :".$newIDs;
		
		$insertQuery = "INSERT INTO `change_log`(`ChangedLogDec`, `ChangedBy`, `ChangedTable`) VALUES ('" . $this->m_dbConn->escapeString($logMsg) . "','".$_SESSION['login_id']."','registers')";										
		$this->m_dbConn->insert($insertQuery);
	}
	
	function getTransactionDetails($entries, $cnt)
	{
		$transactionType = TRANSACTION_CREDIT;
		if($entries[$cnt]['Debit'] > 0)
		{
			$transactionType = TRANSACTION_DEBIT;
			$amount = $entries[$cnt]['Debit'];
		}
		else
		{
			$amount = $entries[$cnt]['Credit'];
		}
		return array($transactionType, $amount);
	}	
	*/

	function getDepreciation($depreciation_method, $depreciation_percent, $op_bal, $halfyear_bal, $halfyear_bal_sale, $second_halfyear_bal, $second_halfyear_bal_sale, $purchase_date)
	{
		if($depreciation_method == 1)	//Straight line
		{
			echo "<BR>Inside Straigh line";
			$purchase_amount = $op_bal;
			$dep_amt = ($depreciation_percent/100) * $purchase_amount;
						
			if($purchase_date <> "")
			{
				echo "<BR>Inside if block";
				$year = date('Y', strtotime($sql1_res[0]['PurchaseDate']));
				$oct_date = $year."-10-01";
				
				if($purchase_date >= $oct_date) //chi condition
				{
					$actual_depreciation_percent = ($sql1_res[0]['DepreciationPercent']/2);
				}
				else
				{
					$actual_depreciation_percent = $sql1_res[0]['DepreciationPercent'];
				}
				
			}
			else
			{
				//Pending
			}
	
			
//			$sql1_res[0]['openingbalance'] .= $op_bal; .........................................................................................//$sql1_res[0]['PurchaseValue'];
			$dep_amt = ($actual_depreciation_percent/100)*$sql1_res[0]['openingbalance'];
			
			
			
		}
		else if($depreciation_method == 2) //Reducing balance
		{
			$dep_amt = $this->obj_utility->getRoundValue2(($op_bal + $halfyear_bal + $halfyear_bal_sale + ($second_halfyear_bal_sale/2) + ($second_halfyear_bal/2)) * ($depreciation_percent/100));
		}
		
		if($this->ShowDebugTrace == 1)
		{
			echo "<BR>getDepreciation()*************";
	
			echo "<BR>depreciation_method . $depreciation_method";
			echo "<BR>depreciation_percent . $depreciation_percent";
			echo "<BR>op_bal =  $op_bal";
					
			echo "<BR>halfyear_bal = ". $halfyear_bal;
			echo "<BR>halfyear_bal_sale = ". $halfyear_bal_sale;

			echo "<BR>second_halfyear_bal = ". $second_halfyear_bal;
			echo "<BR>second_halfyear_bal_sale =  $second_halfyear_bal_sale";
			echo "<BR>dep_amt =  $dep_amt";
			echo "<BR><BR>";

		}
		return $dep_amt;
	}

	function getFixAssetValues($LedgerID, &$op_bal, &$halfyear_bal, &$halfyear_bal_sale, &$second_halfyear_bal, &$second_halfyear_bal_sale)
	{
		$year_start_date = $_SESSION[default_year_start_date];
		$year = date('Y', strtotime($_SESSION[default_year_start_date]));
		$year_mid_date = $year."-09-30";
		$year_end_date = $_SESSION[default_year_end_date];
		//Using 30-03 date to avoid pick up of depreciation value
		$year_end_date = $this->obj_utility->GetDateByOffset2($_SESSION['default_year_end_date'], -1);


/*
		$sql1 = "SELECT l.`ledger_name`, fal.`FixedAssetID`, fal.`PurchaseDate`, fal.`PurchaseValue`, fal.`DepreciationMethod`, fal.`DepreciationPercent`, v.`By` FROM `ledger` l, `fixedassetlist` fal, `voucher` v WHERE l.`id` = '".$LedgerID."' AND l.`id` = fal.`LedgerID` AND fal.`FixedAssetID` = v.`RefNo` AND v.`RefTableID` = '7' LIMIT 1";
		$sql1_res = $this->m_dbConn->select($sql1);
*/
		$sql_op_bal = "SELECT SUM(`Debit`) as Purchase, SUM(`Credit`) Sale FROM `assetregister` WHERE  `LedgerID` = '". $LedgerID . "' and `Date` < '$year_start_date'";

		$sql_halfyear = "SELECT SUM(`Debit`) as Purchase, SUM(`Credit`) Sale FROM `assetregister` WHERE `LedgerID` = '".$LedgerID . "' and `Date`  >= '$year_start_date' and `Date` <= '$year_mid_date'";
		
		$sql_second_halfyear = "SELECT SUM(`Debit`) as Purchase, SUM(`Credit`) Sale FROM `assetregister` WHERE  `LedgerID` = '".$LedgerID . "' and `Date` > '$year_mid_date' and `Date` <= '$year_end_date'";

		$sql_op_bal_res = $this->m_dbConn->select($sql_op_bal);
		$op_bal = $this->obj_utility->getRoundValue2($sql_op_bal_res[0]['Purchase'] - $sql_op_bal_res[0]['Sale']);
		$result[$i]['opening_balance'] = $halfyear_bal;
		
		$halfyear_bal = 0;
		$sql_halfyear_res = $this->m_dbConn->select($sql_halfyear);
		$halfyear_bal = $sql_halfyear_res[0]['Purchase'];
		$halfyear_bal_sale = $sql_halfyear_res[0]['Sale'];

		if(!isset($halfyear_bal))
		{
			$halfyear_bal = 0;
		}
		if(!isset($halfyear_bal_sale))
		{
			$halfyear_bal_sale = 0;
		}

		$result[$i]['half_year_bal'] = $halfyear_bal;
		$result[$i]['half_year_bal_sale'] = $halfyear_bal_sale;
		
		$sql_second_halfyear_res = $this->m_dbConn->select($sql_second_halfyear);		
		$second_halfyear_bal = $sql_second_halfyear_res[0]['Purchase'];// - $sql_second_halfyear_res[0]['Sale'];
		$second_halfyear_bal_sale =  $sql_second_halfyear_res[0]['Sale'];

		if(!isset($second_halfyear_bal))
		{
			$second_halfyear_bal = 0;
		}
		if(!isset($second_halfyear_bal_sale))
		{
			$second_halfyear_bal_sale = 0;
		}

		$result[$i]['second_half_year_bal'] = $second_halfyear_bal ; 
		$result[$i]['second_half_year_bal_sale'] = $second_halfyear_bal_sale; 

/*
		if($sql1_res[0]['DepreciationMethod'] == '' || $sql1_res[0]['DepreciationMethod'] = "0")
		{
			$sql1_res[0]['DepreciationMethod'] = 2;
		}
	*/	
		if($this->ShowDebugTrace == 1)
		{
			echo "<BR>getFixAssetValues()";
			echo "<BR>year_start_date " . $year_start_date ;
			echo "<BR>year_mid_date " . $year_mid_date ;
			echo "<BR>year_end_date " . $year_end_date ;
	
			echo "<BR>LedgerID " . $LedgerID;
			print_r($sql_op_bal_res );
			echo "<BR>op_bal =  $op_bal";
					
			echo "<BR><BR>sql_halfyear<BR>";
			print_r($sql_halfyear_res );
			echo "<BR>halfyear_bal1 = ". $halfyear_bal;
			echo "<BR>halfyear_bal_sale = ". $halfyear_bal_sale;

			echo "<BR><BR>sql_second_halfyear<BR>";
			print_r($sql_second_halfyear_res );
			echo "<BR>second_halfyear_bal = ". $second_halfyear_bal;
			echo "<BR>second_halfyear_bal_sale =  $second_halfyear_bal_sale";
			echo "<BR><BR>";

//			echo "<BR>DepreciationMethod : " . $sql1_res[0]['DepreciationMethod'];
//			echo "<BR>OpeningDate : " . $OpeningDate;	
		}		
	}
	
	//This function is called from FixedAssetDep.php
	function getLedgerDetails($LedgerID)
	{
		//$sql1 = "SELECT l.`ledger_name`, fal.`PurchaseDate`, fal.`PurchaseValue`, fal.`DepreciationMethod`, fal.`DepreciationPercent` FROM `ledger` l, `fixedassetlist` fal WHERE l.`id` = '".$LedgerID."' AND l.`id` = fal.`LedgerID`";
		$sql1 = "SELECT l.`ledger_name`, fal.`FixedAssetID`, fal.`PurchaseDate`, fal.`PurchaseValue`, fal.`DepreciationMethod`, fal.`DepreciationPercent`, v.`By` FROM `ledger` l, `fixedassetlist` fal, `voucher` v WHERE l.`id` = '".$LedgerID."' AND l.`id` = fal.`LedgerID` AND fal.`FixedAssetID` = v.`RefNo` AND v.`RefTableID` = '7' LIMIT 1";
		$sql1_res = $this->m_dbConn->select($sql1);
		
		//echo "<BR>getLedgerDetails";
		//var_dump($sql1_res);
		
		if($sql1_res == "")
		{
			//If record not found in fixedassetlist
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Record not found in fixedassetlist<BR>";
			}
			$sql1_res = $this->obj_utility->getOpeningBalance($LedgerID, $_SESSION['default_year_start_date']);
			$DepreciationMethod = $sql1_res[0]['DepreciationMethod'];
			if($DepreciationMethod == '' || $DepreciationMethod = "0")
			{
				$DepreciationMethod =  Reducing_Balance;
				$sql1_res[0]['DepreciationMethod'] = $DepreciationMethod ;
			}
			$sql1_res[0]['DepreciationMethod'] = $DepreciationMethod ;

			$actual_depreciation_percent = 15;	
			$sql1_res[0]['DepreciationPercent']=$actual_depreciation_percent; 
		}
		else
		{
			$DepreciationMethod = $sql1_res[0]['DepreciationMethod'];			
			$actual_depreciation_percent = $sql1_res[0]['DepreciationPercent'];	
		}

		$halfyear_bal = 0;
		$halfyear_bal_sale = 0;
		$second_halfyear_bal = 0;
		$second_halfyear_bal_sale = 0;
		//echo "<BR><BR>Two";
			
		$this->getFixAssetValues($LedgerID, $op_bal, $halfyear_bal, $halfyear_bal_sale, $second_halfyear_bal, $second_halfyear_bal_sale);


		$sql1_res[0]['ledger_name'] .= $sql1_res['LedgerName'];	
		$sql1_res[0]['openingbalance'] .= $op_bal;//$opening_balance;
		$sql1_res[0]['half_year_bal'] = $halfyear_bal;
		$sql1_res[0]['second_half_year_bal'] = $second_halfyear_bal;
		$sql1_res[0]['half_year_bal_sale'] = $halfyear_bal_sale;
		$sql1_res[0]['second_half_year_bal_sale'] = $second_halfyear_bal_sale;
		
		if($DepreciationMethod == STRAIGHT_LINE)	//Straight line
		{
			$purchase_date = $sql1_res[0]['PurchaseDate'];

			$dep_amt = $this->getDepreciation($DepreciationMethod, $actual_depreciation_percent, $op_bal, $halfyear_bal, $halfyear_bal_sale, $second_halfyear_bal, $second_halfyear_bal_sale, $purchase_date);

			$closing_bal = $op_bal + $halfyear_bal + $second_halfyear_bal - $halfyear_bal - $second_halfyear_bal - $dep_amt;		
			$dep_amt = ($actual_depreciation_percent/100)*$sql1_res[0]['openingbalance'];
			$closing_bal = $sql1_res[0]['openingbalance'] - $dep_amt;
		}
		else if($DepreciationMethod == Reducing_Balance) //Reducing balance
		{
			$dep_amt = $this->getDepreciation($DepreciationMethod, $actual_depreciation_percent, $op_bal, $halfyear_bal, $halfyear_bal_sale, $second_halfyear_bal, $second_halfyear_bal_sale);

			$closing_bal = $op_bal + $halfyear_bal + $second_halfyear_bal - $halfyear_bal_sale - $second_halfyear_bal_sale - $dep_amt;		
		}
	
		$sql1_res[0]['dep_amt'] = $dep_amt;
		$sql1_res[0]['closing_bal'] = $closing_bal;
		
		if($this->ShowDebugTrace == 1)
		{
			echo "<pre>";
			print_r($sql1_res);
			echo "</pre>";
		}
		return $sql1_res;
	}
	
	function getOpeningBalance($LedgerID, $dep_type)
	{
		if($dep_type == STRAIGHT_LINE)
		{
			$sql1 = "SELECT `opening_balance` FROM `ledger` WHERE Is_Opening_Balance =1 and `id` = '".$LedgerID."'";
			$sql1_res = $this->m_dbConn->select($sql1);
			
			$opening_balance = $sql1_res[0]['opening_balance'];
		}
		else if($dep_type == Reducing_Balance)
		{
			$opening_balance_array = $this->obj_utility->getOpeningBalance($LedgerID,$_SESSION['default_year_start_date']);			
			$opening_balance = $opening_balance_array['Total'];
		}
		
		/*echo "<pre>";
		print_r($opening_balance);
		echo "</pre>";*/
		
		return $opening_balance;
	}
	
	function createDepreciationVoucher($to_ledger_id, $by_ledger_id, $dep_amt, $opening_bal, $closing_bal, $dep_per, $purchase_date, $purchase_amt,$depreciation_type, $first_half_bal, $second_half_bal)
	{
		$IsCallUpdtCnt = 1;
		//echo "<BR>To Ledger " . $to_ledger_id . "<BR>By Ledger " . $by_ledger_id;

		$date = date("Y-m-d");
		$latestVoucherNo = $this->obj_latestcount->getLatestVoucherNo($_SESSION['society_id']);
		
		$sql1 = "SELECT `ledger_name` FROM `ledger` WHERE `id` = '".$to_ledger_id."'";
		$sql1_res = $this->m_dbConn->select($sql1);
		$ledger_name = $sql1_res[0]['ledger_name'];
		
		//check if already exists here
		$sql4 = "SELECT * FROM `fixedassetlist` WHERE `LedgerID` = '".$to_ledger_id."' AND `YearID` = '".$_SESSION['default_year']."'";
		$sql4_res = $this->m_dbConn->select($sql4);
		if($this->ShowDebugTrace == 1)
		{
			echo "<br>test " . $sql4;
			var_dump(sql4_res);
			print_r(sql4_res);
		}
		if(sizeof($sql4_res) > 0)
		{
			//echo "<br>test4";
			//pending : Write code to update
			$return = "@@@Exists";
		}
		else if(sizeof($sql4_res) == 0)
		{
			//echo "<BR>Inside else";			
		// $first_half_bal, $second_half_bal
			$sql2 = "INSERT INTO `fixedassetlist`(`FixedAssetDescription`,`LedgerID`,`YearID`,`PurchaseDate`,`PurchaseValue`,`DepreciationMethod`,`DepreciationPercent`,`OpeningValue`,`Depreciation`,`EndingValue`) VALUES('".$sql1_res[0]['ledger_name']."','".$to_ledger_id."','".$_SESSION['default_year']."','".getDBFormatDate($purchase_date)."','".$purchase_amt."','".$depreciation_type."','".$dep_per."','".$opening_bal."','".$dep_amt."','".$closing_bal."')";
			$sql2_res = $this->m_dbConn->insert($sql2);
			
			$sql3 = "SELECT `YearDescription` FROM `year` WHERE `YearID` = '".$_SESSION['default_year']."'";
			$sql3_res = $this->m_dbConn->select($sql3);
			
			
			$note = "Depreciation applied to ".$ledger_name." for year ".$sql3_res[0]['YearDescription'].".";
			if($sql2_res <> "")
			{
				$SrNo = 1;
				$transaction_date = $_SESSION['default_year_end_date'];	//31st March
				$voucherTypeID = VOUCHER_JOURNAL;
				$isOpeningBalance = 0;
				$ExVoucherNo = 0;
			
				//echo "<BR>Before by_ledger_id " . $by_ledger_id;			
				if(isset($by_ledger_id) && $by_ledger_id <> 0)
				{

					$ExpectedCounter = $this->obj_utility->GetCounter(VOUCHER_JOURNAL,0,false);
					//var_dump($ExpectedCounter);
					$ExVoucherNo = $ExpectedCounter[0]['CurrentCounter'];
					if($IsCallUpdtCnt == 1)
					{
						$this->obj_utility->UpdateExVCounter(VOUCHER_JOURNAL,$ExVoucherNo,0);
					}
					$TransactionType = TRANSACTION_DEBIT;

					$dep_voucher_by = $this->obj_voucher->SetVoucherDetails($transaction_date,$sql2_res,TABLE_FIXEDASSETLIST,$latestVoucherNo,$SrNo++,$voucherTypeID,$by_ledger_id,$TransactionType,$dep_amt,$note,$ExVoucherNo);

					$insertDepDebit = $this->obj_register->SetRegister($transaction_date, $by_ledger_id, $dep_voucher_by, $voucherTypeID, $TransactionType, $dep_amt, $isOpeningBalance);
				
					if(isset($to_ledger_id) && $to_ledger_id <> 0 && isset($dep_voucher_by) && $dep_voucher_by <> 0)
					{
						$TransactionType = TRANSACTION_CREDIT;
						$ExVoucherNo=0;
						$dep_voucher_to = $this->obj_voucher->SetVoucherDetails($transaction_date,$sql2_res,TABLE_FIXEDASSETLIST,$latestVoucherNo,$SrNo++,$voucherTypeID,$to_ledger_id,TRANSACTION_CREDIT,$dep_amt,$note);
						
						$insertDepCredit = $this->obj_register->SetRegister($transaction_date, $to_ledger_id, $dep_voucher_to, $voucherTypeID, $TransactionType, $dep_amt, $isOpeningBalance);	
					}
				}
				$return = "@@@Success";
			}
			else
			{
				$return = "@@@Error";			
			}
		}
		return $return;
	}
		
	function create_table($led_id)
	{
		 $sql1 = "SELECT fal.`FixedAssetID`, fal.`YearID`, y.`YearDescription` FROM `fixedassetlist` fal, `year` y WHERE fal.`LedgerID` = '".$led_id."' AND fal.`YearID` = y.`YearID`";
		$sql1_res = $this->m_dbConn->select($sql1);
		
		if($sql1_res <> "")
		{
			$table = "<table style='width:100%' id='voucher_table' ><tr><th>Sr No</th><th>Date</th><th>Voucher No</th><th>Ledger Name</th><th>Amount</th><th>Year</th><th>Note</th><th>Edit</th>";
			
			if($_SESSION['role'] == ROLE_SUPER_ADMIN)
			{
				$table .= "<th>Delete</th>";	
			}
			$table .= "</tr>";
			
			
			
			for($i = 0; $i < sizeof($sql1_res); $i++)
			{
				 $sql2 = "SELECT *, l.`ledger_name` FROM `voucher` v, `ledger` l WHERE v.`RefNo` = '".$sql1_res[$i]['FixedAssetID']."' AND v.`RefTableID` = '".TABLE_FIXEDASSETLIST."' AND v.`By` = l.`id` LIMIT 1";
				$sql2_res = $this->m_dbConn->select($sql2);
				
				if($sql1_res[$i]['YearID'] == $_SESSION['default_year'])
				{
					$table .= "<tr style='background-color:#4CAF50;' id='current_year'><td>".($i + 1)."</td><td>".getDisplayFormatDate($sql2_res[0]['Date'])."</td><td>".$sql2_res[0]['VoucherNo']."</td><td>".$sql2_res[0]['ledger_name']."</td><td>".$sql2_res[0]['Debit']."</td><td>".$sql1_res[$i]['YearDescription']."</td><td>".$sql2_res[0]['Note']."</td><td><a href='VoucherEdit.php?Vno=".$sql2_res[0]['VoucherNo']."&pg=' target='_blank'><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a></td>";
					if($_SESSION['role'] == ROLE_SUPER_ADMIN)
					{
						$table .= "<td><a onclick='detele_Asset_Voucher(".$sql2_res[0]['VoucherNo'].",".$sql1_res[$i]['FixedAssetID'].")'><img src='images/del.gif' border='0' alt='Edit' style='cursor:pointer;'/></a></td>";	
					}
					$table .= "</tr>";
					
					//href='VoucherEdit.php?Vno="+ my_data[i]['VoucherNo'] + "&pg=' target='_blank'><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/>
				}
				else
				{
					$table .= "<tr><td>".($i + 1)."</td><td>".getDisplayFormatDate($sql2_res[0]['Date'])."</td><td>".$sql2_res[0]['VoucherNo']."</td><td>".$sql2_res[0]['ledger_name']."</td><td>".$sql2_res[0]['Debit']."</td><td>".$sql1_res[$i]['YearDescription']."</td><td>".$sql2_res[0]['Note']."</td><td></td>";
					//show Edit button for current year vouchers only
					//<td><a href='VoucherEdit.php?Vno=".$sql2_res[0]['VoucherNo']."&pg=' target='_blank'><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a></td>";
					if($_SESSION['role'] == ROLE_SUPER_ADMIN)
					{
						//$table .= "<td><a onclick='detele_Asset_Voucher(".$sql2_res[0]['VoucherNo'].",".$sql1_res[$i]['FixedAssetID'].")'><img src='images/del.gif' border='0' alt='Edit' style='cursor:pointer;'/></a></td>";	
						//show delete button for current year vouchers only
						$table .= "<td></td>";
					}
					$table .= "</tr>";
				}
				
			}
			
			$table .= "</table>";
		}
		else
		{
			$table = "";
		}
		return $table;
	}
	
	function deleteAssetVoucher($VoucherNo, $RefNo)
	{
		try
		{
			
			$this->m_dbConn->begin_transaction();
			
			$AssetDelete_query = "DELETE FROM fixedassetlist WHERE FixedAssetID = '".$RefNo."'";
			$AssetDelete = $this->m_dbConn->delete($AssetDelete_query);
			if($AssetDelete > 0)
			{
				$VoucherDelete = $this->obj_utility->Delete_VoucherandRegister_table($VoucherNo, VOUCHER_JOURNAL);
				if($VoucherDelete > 0)
				{
					$this->m_dbConn->commit();
					$result = "success";
				}
				else
				{
					$this->m_dbConn->rollback();
					$result = "failed";
				}
			}
			else
			{
				$this->m_dbConn->rollback();
				$result = "failed";
			}
			
			return "@@@".$result;
			
			
		}
		catch(Exception $e)
		{
			$this->m_dbConn->rollback();
			return "@@@".$e->getMessage();
		}
	}
	
	
	
	function update_all()
	{
		//echo "<BR>Inside update_all()";
		$sql1 = "SELECT DISTINCT(`LedgerID`) FROM `fixedassetlist` WHERE `YearID` != '".$_SESSION['default_year']."'";
		
		$sql1_res = $this->m_dbConn->select($sql1);
		
		if($this->ShowDebugTrace == 1)
		{
			var_dump($sql1_res);
		}
		$result = array();
		
		for($i = 0; $i < sizeof($sql1_res); $i++)
		{

			$sql2_res = $this->getLedgerDetails($sql1_res[$i]['LedgerID']);
			if($this->ShowDebugTrace == 1)
			{
				var_dump($sql2_res);
			}
			$DepreVoucher = $this->createDepreciationVoucher($sql1_res[$i]['LedgerID'],$sql2_res[0]['By'],$sql2_res[0]['dep_amt'],$sql2_res[0]['openingbalance'],$sql2_res[0]['closing_bal'],$sql2_res[0]['DepreciationPercent'],$sql2_res[0]['PurchaseDate'],$sql2_res[0]['PurchaseValue'],$sql2_res[0]['DepreciationMethod']);
			
			//var_dump($sql2_res);
			array_push($result,$DepreVoucher);
		}	
		
		return $result;
	}
	
	public function checkIfDefaultSet()
	{
		$fixed_asset = 0;
		if($_SESSION['society_id'])
		{
			$sql1 = "SELECT `APP_DEFAULT_FIXED_ASSET` FROM `appdefault` WHERE `APP_DEFAULT_SOCIETY` = '".$_SESSION['society_id']."'";
			$sql1_res = $this->m_dbConn->select($sql1);
			$fixed_asset = $sql1_res[0]['APP_DEFAULT_FIXED_ASSET'];			
		}
		
		return $fixed_asset;
	}
}
?>
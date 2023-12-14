<?php
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once("utility.class.php");
include_once("dbconst.class.php");
include_once("register.class.php");
include_once("changelog.class.php");
class account_subcategory extends dbop
{
	public $actionPage = "../vendor.php";
	public $m_dbConn;
	private $obj_utility;
	private $obj_register;
	public $isCatError;
	public $isLedgerExits;
	public $m_objLog;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		/*//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);*/

		$this->obj_utility = new utility($this->m_dbConn);
		$this->obj_register = new regiser($this->m_dbConn);
		$this->isCatError = false;
		$this->isLedgerExits = false;
		////dbop::__construct();
		$this->m_objLog = new changeLog($this->m_dbConn);
	}
	
	function getcategoryId($CateGoryID)
	{
		$sqlid = "select category_id, category_name from account_category where category_id='".$CateGoryID."'";
		$catid = $this->m_dbConn->select($sqlid);
		
		return $catid;
	}

	function validate()
	{
		//$a = $this->getcategoryId();
		if($_SESSION['default_Sundry_creditor'] == 0)
		{
			$errorMsg1 = '<font style="font-size:15px;text-align:center; padding-top:30px; 
			margin-top: 100px; font-weight:bold; color:blue">NOTE : Sundry Creditor not selected on defaults page. Please select sundry creditor value. For that click on below link..<br /></font><br />
			<font style="font-size:17px;text-align:center; padding-top:50px; 
			margin-top: 200px; font-weight:bold; color:blue"><a href="defaults.php"> Click Here..</a>  </font> 
			<br><br>';
			
		}
		return $errorMsg1;
	}
	public function startProcess()
	{

		$errorExists = 0;
		//$s = "select name from login where member_id='".$this->m_dbConn->escapeString(strtolower($_POST['user']))."' and status='Y'";
		//echo $s;
		/*//$curdate 		=  $this->curdate;
		//$curdate_show	=  $this->curdate_show;
		//$curdate_time	=  $this->curdate_time;
		//$ip_location	=  $this->ip_location;*/
		
		

		if($_REQUEST['insert']=='Insert' && $errorExists==0)
		{	
			if($_POST['society_id']<>"" && $_POST['categoryid']<>"" && $_POST['ledger_name']<>"" &&  $_POST['opening_type'] > 0 ) 
			{
				if($_SESSION['society_creation_yearid'] <> "")
				{
					//$OpeningBalanceDate = $this->obj_utility->GetDateByOffset($_SESSION['default_year_start_date'] , -1);
					$OpeningBalanceDate = $this->obj_utility->GetDateByOffset($this->obj_utility->getCurrentYearBeginingDate($_SESSION['society_creation_yearid']) , -1);
					if($OpeningBalanceDate <> "")
					{
						$_POST['balance_date'] = $OpeningBalanceDate;		
					}		
			 	}
				
				$aryParent = $this->obj_utility->getParentOfCategory($_POST['categoryid']);
				//print_r($aryParent);
				$b = $this->getcategoryId();
				$count_query = "select count(ledger_name) as count from ledger where ledger_name='".$_POST['ledger_name']."' and society_id='" . $_SESSION['society_id'] . "' and 	categoryid='".$b[0]['category_id']."'  ";	
				$res = $this->m_dbConn->select($count_query);

				//echo $res[0]['count'];

				if($res[0]['count'] <= 0)
				{ //,`created_by`  ,'".$_SESSION['name']."'
					
					//`expense`,`payment`,  '".$_POST['expense']."','".$_POST['payment']."',
					$openingBalance = abs($_POST['opening_balance']);

					$insert_query="insert into ledger (`society_id`,`categoryid`,`ledger_name`,`opening_type`,`opening_balance`,`note`,`opening_date`,`show_in_bill`,`supplementary_bill`,`taxable`,`sale`,`purchase`,`income`,`expense`,`payment`,`receipt`,`taxable_no_threshold`) 
					values 
					('".$_POST['society_id']."','".$_POST['categoryid']."','".$_POST['ledger_name']."','".$_POST['opening_type']."','".$openingBalance."','".$_POST['note']."','".getDBFormatDate($_POST['balance_date'])."','".$_POST['show_in_bill']."','".$_POST['supplementary_bill']."','".$_POST['taxable']."','".$_POST['sale']."','".$_POST['purchase']."','".$_POST['income']."','".$_POST['expense']."','".$_POST['payment']."','".$_POST['receipt']."','".$_POST['nothreshold']."')";
					
					//('".$_POST['society_id']."','".$_POST['categoryid']."','".$_POST['ledger_name']."','".$_POST['opening_type']."','".$openingBalance."','".$_POST['note']."','".getDBFormatDate($_POST['balance_date'])."','".$_POST['show_in_bill']."','".$_POST['income']."','".$_POST['sale']."','".$_POST['supplementary_bill']."','".$_POST['expense']."','".$_POST['purchase']."','".$_POST['taxable']."','".$_POST['payment']."','".$_POST['receipt']."','".$_POST['nothreshold']."')";
					//echo $insert_query;			
					//die();
					$ledgerID = $this->m_dbConn->insert($insert_query);
					//echo '<br>ParentID : ' . $aryParent['group'] . ' Type : ' . ASSET;
					$Transaction = ($_POST['opening_type'] == 1)?TRANSACTION_CREDIT:TRANSACTION_DEBIT;
					
					if($aryParent['group'] == LIABILITY)
					{
						$insertLiability = $this->obj_register->SetLiabilityRegister(getDBFormatDate($_POST['balance_date']), $ledgerID, 0, 0, $Transaction, $openingBalance , 1);
					}
								
					
					if($aryParent['group'] == LIABILITY)
					{
					
						$insertQuery="Insert into `ledger_details` (`LedgerID`,`GSTIN_No`,`PAN_No`,`TDS_NatureOfPayment`,`TDS_Ded_rate`,`vendor_address1`,`vendor_address2`,`vendor_city`,`vendor_pincode`,`vendor_state`,`vendor_contact`,`vendor_office_no`,`vendor_email`,`website`) values('".$ledgerID."','".strtoupper($_POST['GSTIN_No'])."','".strtoupper($_POST['Pan_no'])."','".$_POST['natureOfPayment']."','".$_POST['nature_rate']."','".$_POST['Address1']."','".$_POST['Address2']."','".$_POST['City']."','".$_POST['Pincode']."','".$_POST['State']."','".$_POST['contact_no']."','".$_POST['off_contact']."','".$_POST['email_add']."','".$_POST['website']."')";

						$ledgerDetails = $this->m_dbConn->insert($insertQuery);
						
					}
					//change log history 
					$dataArr=array('SocietyId'=>$_POST['society_id'],'CategoryId'=>$_POST['categoryid'],'Ledger Name'=>$_POST['ledger_name'],
					'Opening Type'=>$_POST['opening_type'],'Opening Balance'=>$openingBalance,'Note'=>$_POST['note'],'balance_date'=>$_POST['balance_date'],
					'Show In Maintence Bill'=>isset($_POST['show_in_bill'])?Yes:No,'Supplementary Bill'=>isset($_POST['supplementary_bill'])?Yes:No,'Taxable'=>isset($_POST['taxable'])?Yes:No,
					'Sale'=>isset($_POST['sale'])?Yes:No,'Purchase'=>isset($_POST['purchase'])?Yes:No,'	Income'=>isset($_POST['income'])?Yes:No,'Expense'=>isset($_POST['expense'])?Yes:No,
					'Payment'=>isset($_POST['payment'])?Yes:No,'Receipt'=>isset($_POST['receipt'])?Yes:No,'Taxable Without GST Threshold'=>isset($_POST['nothreshold'])?Yes:No,'Security Deposit'=>isset($_POST['sec_dep'])?Yes:No,
					'GSTIN No'=>strtoupper($_POST['GSTIN_No']),'Pan No'=>strtoupper($_POST['Pan_no']),'Nature of Payment'=>$_POST['natureOfPayment'],
					'TDS Rate (%)'=>$_POST['nature_rate'],'Address1'=>$_POST['Address1'],'Address2'=>$_POST['Address2'],'City'=>$_POST['City'],
					'Pincode'=>$_POST['Pincode'],'State'=>$_POST['State'],'Contact No'=>$_POST['contact_no'],'Office No'=>$_POST['off_contact'],
					'Email'=>$_POST['email_add'],'Website Name'=>$_POST['website']
					);
					
					$logArr = json_encode($dataArr);
					//var_dump($logArr);
					$this->m_objLog->setLog($logArr, $_SESSION['login_id'], TABLE_VENDOR_MANAGEMENT, $ledgerID, ADD, 0);
					//die();				
					return "Insert";
				}
				else
				{
					$this->isLedgerExits = true;
					return "Ledger already exist";		
				}
			}
			else
			{
									
				return "All * Field Required";											
			}
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			if($_POST['society_id']<> "" && $_POST['categoryid']<>"" && $_POST['ledger_name']<>"" && $_POST['opening_type'] > 0 ) 
			{

					$aryParent = $this->obj_utility->getParentOfCategory($_POST['categoryid']);
					//if($aryParent['group'] == LIABILITY || $aryParent['group'] == ASSET) // Now all group have opening balance 
					{
						if($_POST['balance_date'] == '')
						{
							return "All * Field Required";											
						}
					}
					$openingBalance = abs($_POST['opening_balance']);
					//echo "ashwini";
					$selectQuery = 'SELECT cat.category_name,led.`categoryid`,led.`ledger_name`,led.`payment`,led.`expense`,led.`opening_type`,led.`opening_balance`,led.`status` FROM `ledger` led JOIN `account_category` AS cat  ON led.categoryid = cat.category_id  where led.`id` = "'.$_POST['id'].'"';
					$prevCategoryID = $this->m_dbConn->select($selectQuery);
					//`, lg.vendor_address1, lg.vendor_address2,lg.`vendor_city`,lg.`vendor_pincode`, lg.`vendor_state`    JOIN `ledger_details` AS lg ON led.id = lg.LedgerID
					$prevAryParent = $this->obj_utility->getParentOfCategory($prevCategoryID[0]['categoryid']);					
					
					$logMsg .= "<br />Original Record : ";
					$logMsg .= implode(' || ', array_map(function ($entry) 
					{
						return $_POST['id'] . " | " . $entry['categoryid'] . " | " . $entry['ledger_name'] . " | " . $entry['expense'] . " | " . $entry['payment']
							. " | " . $entry['receipt'] . " | " . $entry['opening_type'] . " | " . $entry['opening_balance'] . " | " . $entry['status'];
					}, $prevCategoryID));
					
					if($prevAryParent['group'] == $aryParent['group'])
					{
						try
						{
							$this->m_dbConn->begin_transaction();
							if($aryParent['group'] == ASSET)
							{						
								$isCatIDBank = false;
								$isPrevCatIDBank = false;
								if($_POST['categoryid'] == BANK_ACCOUNT || $_POST['categoryid'] == CASH_ACCOUNT)
								{
									$isCatIDBank = true;
								}
								if($prevCategoryID[0]['categoryid'] == BANK_ACCOUNT || $prevCategoryID[0]['categoryid'] == CASH_ACCOUNT)
								{
									$isPrevCatIDBank = true;
								}
								if( $isCatIDBank == true && $isPrevCatIDBank == false )
								{
									$assetregCountQuery = "SELECT Count(*) as cnt from `assetregister` WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance != 1";							
									$assetregCount = $this->m_dbConn->select($assetregCountQuery);
									if($assetregCount[0]['cnt'] > 0)
									{
										$this->isCatError = true;
										return "Ledger<".$prevCategoryID[0]['ledger_name']."> is associated with category<".$prevCategoryID[0]['category_name'].">. Changing it to Bank/Cash account is restricted.";
									}
								}
								else if( $isCatIDBank == false && $isPrevCatIDBank == true )
								{							
									$bankregCountQuery = "SELECT Count(*) as cnt from `bankregister` WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance != 1";							
									$bankregCnt = $this->m_dbConn->select($bankregCountQuery);							
									if($bankregCnt[0]['cnt'] > 0)
									{
										$this->isCatError = true;
										return "Ledger<".$prevCategoryID[0]['ledger_name']."> is associated with Bank/Cash account. Changing of Category ID is restricted.";								
									}
								}
								else if(($_POST['categoryid'] == BANK_ACCOUNT && $prevCategoryID[0]['categoryid'] == CASH_ACCOUNT) || ($_POST['categoryid'] == CASH_ACCOUNT && $prevCategoryID[0]['categoryid'] == BANK_ACCOUNT))
								{
									$bankregCountQuery = "SELECT Count(*) as cnt from `bankregister` WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance != 1";							
									$bankregCnt = $this->m_dbConn->select($bankregCountQuery);							
									if($bankregCnt[0]['cnt'] > 0)
									{
										$this->isCatError = true;
										return "Ledger<".$prevCategoryID[0]['ledger_name']."> is associated with category<".$prevCategoryID[0]['category_name'].">. Changing of Category ID is restricted.";
									}
								}
							}
							$this->m_dbConn->commit();
						}
						catch(Exception $exp)
						{
							$this->m_dbConn->rollback();
							return $exp;
						}

					}
					else
					{
						try
						{
							$this->m_dbConn->begin_transaction();
							$errorMsg = $this->moveRegEntriesAccordingToGroupChange($_POST['id'],$prevAryParent['group'],$aryParent['group'],$prevCategoryID[0]['categoryid'],$_POST['categoryid']);
							$this->m_dbConn->commit();
							if($errorMsg == "Changing of GroupID is Not Allowed")
							{
								$this->isCatError = true;
								return "Ledger<".$prevCategoryID[0]['ledger_name']."> is associated with Bank/Cash account. Changing of Group ID is restricted.";
							}
							else if($errorMsg == "Not Allowed")
							{
								$this->isCatError = true;
								return "Ledger<".$prevCategoryID[0]['ledger_name']."> is associated with group<".$prevAryParent['groupname'].">. Changing it to Bank/Cash account is restricted.";	
							}							
						}
						catch(Exception $exp)
						{
							$this->m_dbConn->rollback();
							return $exp;
						}
					}
					
					$up_query="update ledger set `society_id`='".$_POST['society_id']."',`categoryid`='".$_POST['categoryid']."',`ledger_name`='".$_POST['ledger_name']."',`opening_type`='".$_POST['opening_type']."',`opening_balance`='".$openingBalance."',`note`='".$_POST['note']."',`opening_date` ='".getDBFormatDate($_POST['balance_date'])."',`supplementary_bill`='".$_POST['supplementary_bill']."',`show_in_bill`='".$_POST['show_in_bill']."',`taxable`='".$_POST['taxable']."',`sale`='".$_POST['sale']."',`purchase`='".$_POST['purchase']."',`income`='".$_POST['income']."',`expense`='".$_POST['expense']."',`payment`='".$_POST['payment']."',`receipt`='".$_POST['receipt']."',`taxable_no_threshold` = '".$_POST['nothreshold']."' where id='".$_POST['id']."'";

					//`taxable_no_threshold` = '".$_POST['nothreshold']."',
					//,`vendor_address1` ='".$_POST['Address1']."',`vendor_address2` ='".$_POST['Address2']."',`vendor_city` ='".$_POST['City']."',`vendor_pincode` ='".$_POST['Pincode']."',`vendor_state` ='".$_POST['State']."'
	//echo $up_query;
	//die();
					$data = $this->m_dbConn->update($up_query);
					
					$aryParentLedger = $this->obj_utility->getParentOfLedger($_POST['id']);
					
					// Delete Openning balance From other register if exits
					
					$this->deleteOpeningBalance($aryParent['group'],$_POST['categoryid'],$_POST['id']);
					
					$Transaction   = ($_POST['opening_type'] == 1)?TRANSACTION_CREDIT:TRANSACTION_DEBIT;
					$TransactionOpp = ($Transaction == TRANSACTION_CREDIT)?TRANSACTION_DEBIT:TRANSACTION_CREDIT;
					
					if($aryParent['group'] == LIABILITY)
					{
						$selectLiability = "SELECT Count(*) as cnt from `liabilityregister` WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
						$dataSelectLiability = $this->m_dbConn->select($selectLiability);
					
						if($dataSelectLiability[0]['cnt'] > 0)
						{
							$updateLiability = "UPDATE `liabilityregister` SET `Date`='" . getDBFormatDate($_POST['balance_date']) . "',`CategoryID`='".$aryParentLedger['group']."',`SubCategoryID`='" . $aryParentLedger['category'] . "' , ".$Transaction." ='" . $openingBalance . "', ".$TransactionOpp."=0 WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
							$dataLiability = $this->m_dbConn->update($updateLiability);
							
							$updateLiability2 = "UPDATE `liabilityregister` SET  `CategoryID`='".$aryParentLedger['group']."',`SubCategoryID`='" . $aryParentLedger['category'] . "'   WHERE LedgerID = '" . $_POST['id'] . "' ";
							$dataLiability2 = $this->m_dbConn->update($updateLiability2);
						}
						else
						{
							$insertLiability = $this->obj_register->SetLiabilityRegister(getDBFormatDate($_POST['balance_date']), $_POST['id'], 0, 0, $Transaction, $openingBalance, 1);
						}
					}
					
					
					
					$selectQuery = 'SELECT * FROM `ledger` WHERE `id` = "'.$_POST['id'].'"';
					$res = $this->m_dbConn->select($selectQuery);
					$logMsg .= "<br />Updated Record : ";
					$logMsg .= implode(' || ', array_map(function ($entry) 
					{
						return $_POST['id'] . " | " . $entry['categoryid'] . " | " . $entry['ledger_name'] . " | " . $entry['expense'] . " | " . $entry['payment']
							. " | " . $entry['receipt'] . " | " . $entry['opening_type'] . " | " . $entry['opening_balance'] . " | " . $entry['status'];
					}, $res));
					
					$insertQuery = "INSERT INTO `change_log`(`ChangedLogDec`, `ChangedBy`, `ChangedTable`, `ChangedKey`) VALUES ('" . $this->m_dbConn->escapeString($logMsg) . "','".$_SESSION['login_id']."','ledger','".$_POST['id']."')";										
					$this->m_dbConn->insert($insertQuery);											
					
					if($aryParent['group'] == LIABILITY || $aryParent['group'] == ASSET)
					{
						$selquery="Select * from ledger_details where LedgerID='".$_POST['id']."'";
						$dataGSTIN=$this->m_dbConn->select($selquery);	
						if($dataGSTIN <> '')
						{
							$UpdateQuery="UPDATE `ledger_details` SET `GSTIN_No`='".strtoupper($_POST['GSTIN_No'])."',`PAN_No`='".strtoupper($_POST['Pan_no'])."',`TDS_NatureOfPayment`='".$_POST['natureOfPayment']."',TDS_Ded_rate='".$_POST['nature_rate']."', `vendor_address1`='".$_POST['Address1']."',`vendor_address2`='".$_POST['Address2']."',`vendor_city`='".$_POST['City']."',`vendor_pincode`='".$_POST['Pincode']."',`vendor_state`='".$_POST['State']."',`vendor_contact`='".$_POST['contact_no']."',`vendor_office_no`='".$_POST['off_contact']."',`vendor_email`='".$_POST['email_add']."',`website`='".$_POST['website']."' where LedgerID='".$_POST['id']."'";
						$ledgerDetails = $this->m_dbConn->update($UpdateQuery);
						}
						else
						{
							$insertQuery="Insert into `ledger_details` (`LedgerID`,`GSTIN_No`,`PAN_No`,`TDS_NatureOfPayment`,`TDS_Ded_rate`,`vendor_address1`,`vendor_address2`,`vendor_city`,`vendor_pincode`,`vendor_state`,`vendor_contact`,`vendor_office_no`,`vendor_email`,`website`) values('".$_POST['id']."','".strtoupper($_POST['GSTIN_No'])."','".strtoupper($_POST['Pan_no'])."','".$_POST['natureOfPayment']."','".$_POST['nature_rate']."','".$_POST['Address1']."','".$_POST['Address2']."','".$_POST['City']."','".$_POST['Pincode']."','".$_POST['State']."','".$_POST['contact_no']."','".$_POST['off_contact']."','".$_POST['email_add']."','".$_POST['website']."')";
						$ledgerDetails = $this->m_dbConn->insert($insertQuery);
						}
					}
					//change log history
					$sqlEdit="SELECT `ChangeLogID` FROM `change_log` where `ChangedKey`='".$_POST['id']."' and ChangedTable='".TABLE_VENDOR_MANAGEMENT."'";
					$prevChangeLogID = $this->m_dbConn->select($sqlEdit);
					
					$dataArr=array('SocietyId'=>$_POST['society_id'],'CategoryId'=>$_POST['categoryid'],'Ledger Name'=>$_POST['ledger_name'],
					'Opening Type'=>$_POST['opening_type'],'Opening Balance'=>$openingBalance,'Note'=>$_POST['note'],'balance_date'=>$_POST['balance_date'],
					'Show In Maintence Bill'=>isset($_POST['show_in_bill'])?Yes:No,'Supplementary Bill'=>isset($_POST['supplementary_bill'])?Yes:No,'Taxable'=>isset($_POST['taxable'])?Yes:No,
					'Sale'=>isset($_POST['sale'])?Yes:No,'Purchase'=>isset($_POST['purchase'])?Yes:No,'	Income'=>isset($_POST['income'])?Yes:No,'Expense'=>isset($_POST['expense'])?Yes:No,
					'Payment'=>isset($_POST['payment'])?Yes:No,'Receipt'=>isset($_POST['receipt'])?Yes:No,'Taxable Without GST Threshold'=>isset($_POST['nothreshold'])?Yes:No,'Security Deposit'=>isset($_POST['sec_dep'])?Yes:No,
					'GSTIN No'=>strtoupper($_POST['GSTIN_No']),'Pan No'=>strtoupper($_POST['Pan_no']),'Nature of Payment'=>$_POST['natureOfPayment'],
					'TDS Rate (%)'=>$_POST['nature_rate'],'Address1'=>$_POST['Address1'],'Address2'=>$_POST['Address2'],'City'=>$_POST['City'],
					'Pincode'=>$_POST['Pincode'],'State'=>$_POST['State'],'Contact No'=>$_POST['contact_no'],'Office No'=>$_POST['off_contact'],
					'Email'=>$_POST['email_add'],'Website Name'=>$_POST['website']
					);				
					
					$logArr = json_encode($dataArr);
					//var_dump($logArr);
					$this->m_objLog->setLog($logArr, $_SESSION['login_id'], TABLE_VENDOR_MANAGEMENT,$_POST['id'], EDIT, $prevChangeLogID[0]['ChangeLogID']);
					

					if(isset($_POST['edit']) && $_POST['edit'] <> "")
					{		
						return "LedgerUpdate";
						
					}
					else
					{
						return "Update";	
					}
			}
			else
			{
									
				return "All * Field Required";											
			}
		}
		else
		{
			return $errString;
		}
	}
	
	// Delete the Opening balance Opening Balance
	
	public function deleteOpeningBalance($groupID, $categoryID, $ledgerID)
	{
		$deleteAsset	 = "DELETE FROM `assetregister` WHERE LedgerID = '" . $ledgerID . "' and Is_Opening_Balance = 1";
		$deleteBank 	 = "DELETE FROM `bankregister` WHERE LedgerID = '" . $ledgerID . "' and Is_Opening_Balance = 1";
		$deleteLiability = "DELETE FROM `liabilityregister` WHERE LedgerID = '" . $ledgerID . "' and Is_Opening_Balance = 1";
		$deleteIncome    = "DELETE FROM `incomeregister` WHERE LedgerID = '" . $ledgerID . "' and Is_Opening_Balance = 1";
		$deleteExpense   = "DELETE FROM `expenseregister` WHERE LedgerID = '" . $ledgerID . "' and Is_Opening_Balance = 1";
		
		if($groupID == ASSET)
		{
			if($categoryID == BANK_ACCOUNT || $categoryID == CASH_ACCOUNT)
			{
				$dataDeleteAsset = $this->m_dbConn->delete($deleteAsset);		
				$dataDeleteLiability = $this->m_dbConn->delete($deleteLiability);	
				$dataDeleteIncome = $this->m_dbConn->delete($deleteIncome);
				$dataDeleteExpense = $this->m_dbConn->delete($deleteExpense);
			}
			else
			{	
				$dataDeleteBank = $this->m_dbConn->delete($deleteBank);	
				$dataDeleteLiability = $this->m_dbConn->delete($deleteLiability);	
				$dataDeleteIncome = $this->m_dbConn->delete($deleteIncome);
				$dataDeleteExpense = $this->m_dbConn->delete($deleteExpense);
			}
		}
		else if($groupID == LIABILITY)
		{
				$dataDeleteAsset = $this->m_dbConn->delete($deleteAsset);	
				$dataDeleteBank = $this->m_dbConn->delete($deleteBank);	
				$dataDeleteIncome = $this->m_dbConn->delete($deleteIncome);
				$dataDeleteExpense = $this->m_dbConn->delete($deleteExpense);
		}
		else if($groupID == INCOME)
		{
				$dataDeleteAsset = $this->m_dbConn->delete($deleteAsset);	
				$dataDeleteBank = $this->m_dbConn->delete($deleteBank);	
				$dataDeleteLiability = $this->m_dbConn->delete($deleteLiability);	
				$dataDeleteExpense= $this->m_dbConn->delete($deleteExpense);
		}
		else if($groupID == EXPENSE)
		{
				$dataDeleteAsset = $this->m_dbConn->delete($deleteAsset);	
				$dataDeleteBank = $this->m_dbConn->delete($deleteBank);	
				$dataDeleteLiability = $this->m_dbConn->delete($deleteLiability);	
				$dataDeleteIncome = $this->m_dbConn->delete($deleteIncome);
		}
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
	public function combobox1($query, $id )
	{
		
		
			$str.="<option value='0'>Please Select</option>";
		
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			$vowels = array('/',  '.', '*', '%', '&', ',', '"');
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
						$str.= str_replace($vowels, ' ', $v)."</OPTION>";
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
		$thheader = array('Group Name','Category','Ledger','OpeningBalance','OpeningType','GSTIN No','Pan No','Contact No','Office No','Email','Address1','Address2','City','Pincode','State','Opening Date','Creation Date','Website','Note');
		//,'Created By','created On'

		//print_r($thheader);
		$this->display_pg->edit		= "getaccount_subcategory";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "vendor.php";
		$this->display_pg->view		= "getaccount_subcategory";
		$this->display_pg->print	= "getaccount_subcategory";
		$ShowEdit = true;
		$res = $this->display_pg->display_new($rsas);

		if($_SESSION['role']==ROLE_SUPER_ADMIN  || $_SESSION['profile'][PROFILE_VENDOR_MANAGEMENT] == 1  &&  $_SESSION['is_year_freeze'] == 0)
		{
			
			$ShowEdit = true;
			
		}
		else
		{
			$ShowEdit = false;
		}
		$res = $this->display_pg->display_datatable($rsas, $ShowEdit /*Show Edit Option*/, false /*Hide Delete Option*/,true /*Show View Option*/,true);
		
	
		return $res;
	}
	public function display_new($rsas)
	{
		$thheader = array('Group Name','Category','Ledger','OpeningBalance','OpeningType','GSTIN No','Opening Date','Note');
		
		$this->display_pg->edit		= "getaccount_subcategory";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "vendor.php";
		$this->display_pg->view		= "getaccount_subcategory";
		$this->display_pg->print	= "getaccount_subcategory";
		$ShowEdit = true;
		$res = $this->display_pg->display_datatable($rsas, $ShowEdit /*Show Edit Option*/, false /*Hide Delete Option*/,true /*Show View Option*/,true);

	}
	public function pgnation()
	{
	
		//$sql11= "SELECT `APP_DEFAULT_SUNDRY_CREDITOR` FROM `appdefault` WHERE `APP_DEFAULT_SOCIETY`='".$_SESSION['society_id']."'";
		
		//$result1 = $this->m_dbConn->select($sql11);
		
		$sql1 ="select ledger_table.id,g.groupname 'Group Name', Account.category_name as 'Category Name',ledger_table.ledger_name as 'ledger',FORMAT(ledger_table.opening_balance,2) as opening_balance,IF(ledger_table.opening_type = '0', 'None',IF(ledger_table.opening_type = '1', 'Credit',IF(ledger_table.opening_type = '2', 'Debit',''))) as opening_type, ld.GSTIN_No,ld.PAN_No, ld.vendor_contact,ld.vendor_office_no ,ld.vendor_email,ld.vendor_address1,ld.vendor_address2, ld.vendor_city, ld.vendor_pincode, ld.vendor_state, DATE_FORMAT(DATE(DATE_ADD(ledger_table.opening_date, INTERVAL 1 DAY)),'%d-%m-%Y') as opening_date, DATE_FORMAT(ledger_table.created_at,'%Y-%m-%d') as Creation_date,ld.website, ledger_table.note from `account_category` as `Account` join `ledger` as `ledger_table` on Account.category_id = ledger_table.categoryid join `society` as `society_table` on society_table.society_id = ledger_table.society_id left join `ledger_details` as ld on ledger_table.id=ld.LedgerID join `group` as g on Account.group_id = g.id where society_table.society_id ='".$_SESSION['society_id']."' and Account.category_id='".$_SESSION['default_Sundry_creditor']."' order by ledger_table.id"; 
				
		$result = $this->m_dbConn->select($sql1);
		for($i = 0 ; $i< sizeof($result);$i++)
		{
			
			if($result[$i]['Creation_date'] == '0000-00-00')
			{
				$result[$i]['Creation_date']=  $this->obj_utility->GetDateByOffset($this->obj_utility->getCurrentYearBeginingDate($_SESSION['society_creation_yearid']) , 0);  	
			}
			else
			{
				$result[$i]['Creation_date'] = $result[$i]['Creation_date'];
			}
		}
		
		//echo "<pre>";
		//print_r($result);
		//echo "</pre>";
		$this->display1($result);
	}
	public function selecting()
	{
		//,`Created_by`,`created_on`
		$sql = "select l.id,l.`society_id`,l.`categoryid`,l.`ledger_name`,l.`expense`,l.`payment`,l.`opening_type`,l.`opening_balance`,l.`note`,l.`opening_type`,DATE_FORMAT(l.opening_date, '%d-%m-%Y') as opening_date,l.`show_in_bill`,l.`taxable`,l.`sale`,l.`purchase`,l.`income`,l.`expense`,l.`payment`,l.`receipt`,l.`taxable_no_threshold`,l.`supplementary_bill` from ledger as l where id='".$_REQUEST['account_subcategoryId']."'";
		$res = $this->m_dbConn->select($sql);
		//print_r($res);
		
		$group_id = $this->obj_utility->getParentOfLedger($res[0]['id']);
		
		
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
			$res[0]['nature_of_payId']=$res1[$i]['TDS_NatureOfPayment'];
			$res[0]['nature_deduction_rate']=$res1[$i]['TDS_Ded_rate'];

			$res[0]['vendor_address1']=$res1[$i]['vendor_address1'];
			$res[0]['vendor_address2']=$res1[$i]['vendor_address2'];
			$res[0]['vendor_city']=$res1[$i]['vendor_city'];
			$res[0]['vendor_state']=$res1[$i]['vendor_state'];
			$res[0]['vendor_pincode']=$res1[$i]['vendor_pincode'];
			
			$res[0]['vendor_contact']=$res1[$i]['vendor_contact'];
			$res[0]['vendor_office_no']=$res1[$i]['vendor_office_no'];
			$res[0]['vendor_email']=$res1[$i]['vendor_email'];
			$res[0]['website']=$res1[$i]['website'];
		}
		//print_r($res);
		}
		return $res;
	}
	public function deleting()
	{
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
			
		}
	
		
		if($prevGroup == LIABILITY)
		{
			$selectQuery = "SELECT * FROM `liabilityregister` WHERE `LedgerID` = '".$ledgerID."' AND `Is_Opening_Balance` != 1";
			$entries = $this->m_dbConn->select($selectQuery); 	
			$deleteQuery = "DELETE FROM `liabilityregister` WHERE `LedgerID` = '".$ledgerID."'";
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
}
?>
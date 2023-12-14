<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once("dbconst.class.php");
include_once("register.class.php");
include_once("utility.class.php");
include_once("changelog.class.php");
include_once ("include/fetch_data.php");
include_once("genbill.class.php");

class unit
{

	public $actionPage= "../unit.php";//"../unit_search.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_utility;
	public $m_objLog;
	public $obj_fetch;
	public $obj_genbill;
	public $debug_trace;
	
	function __construct($dbConn, $dbConnRoot = '')
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->obj_utility = new utility($this->m_dbConn);
		$this->display_pg=new display_table($this->m_dbConn);
		$this->m_objLog = new changeLog($this->m_dbConn);
		$this->obj_fetch = new FetchData($this->m_dbConn);
		$this->obj_genbill = new genbill($this->m_dbConn);
		$this->debug_trace = 0;
		//dbop::__construct();
	}

	public function startProcess()
	{

		$errorExists=0;
		try
		{
			$this->m_dbConn->begin_transaction();
		if($_REQUEST['mode']=='Insert' && $errorExists==0)
		{
			if($_POST['society_id']<>"" && $_POST['wing_id']<>"" && $_POST['unit_no']<>"" && $_POST['owner_name']<>""  && $_POST['Period']<>"" && $_POST['Period'] > 0 && $_POST['unit_presentation']<>"" && $_POST['unit_presentation'] > 0  && $_POST['ownership_date'] <> "")
			{
				$unitLedgerName = addslashes(trim($_POST['unit_no']));//$this->get_wing_name($_POST['wing_id']) . '-' . addslashes(trim($_POST['unit_no']));
				$sql = "select count(*)as cnt from unit where society_id='".$_SESSION['society_id']."' and wing_id='".$_POST['wing_id']."' and unit_no='".$unitLedgerName."' and status='Y'";
				$res = $this->m_dbConn->select($sql);
				
				$mownership = "SELECT * FROM `year`  where `YearID` = '".$_SESSION['society_creation_yearid']."' ";
				$mres2 = $this->m_dbConn->select($mownership);
				
				if($res[0]['cnt']==0)
				{
					foreach(explode(',',$_POST['unit_no']) as $k)
					{
						
						if($k<>"")
						{
							$sql0 = "select count(*)as cnt from unit where society_id='".$_SESSION['society_id']."' and wing_id='".$_POST['wing_id']."' and unit_no='".$unitLedgerName."'";
							$res0 = $this->m_dbConn->select($sql0);
							if($res0[0]['cnt']==0)
							{
								$rand_no = rand('00000000','99999999');
								$sql00 = "select count(*)as cnt from unit where rand_no='".$rand_no."' and status='Y'";
								$res00 = $this->m_dbConn->select($sql00);
								if($res00[0]['cnt']==1)
								{
									$rand_no = rand('00000000','99999999');
								}
								
								//Maintenance Bill
								$iBillSubTotal = ($_POST['bill_subtotal'] == '') ? 0 : $_POST['bill_subtotal'];
								$iBillInterest = ($_POST['bill_interest'] == '') ? 0 : $_POST['bill_interest'];
								$iPrevPrinciple = ($_POST['principle_balance'] == '') ? 0 : $_POST['principle_balance'];
								$iPrevInterest = ($_POST['interest_balance'] == '') ? 0 : $_POST['interest_balance'];
								
								$opening_balance = $iBillSubTotal + $iBillInterest;
								$total_bill_payable = $opening_balance + $iPrevPrinciple + $iPrevInterest; 

								//Supplimentary Bill
								$iSuppBillSubTotal = ($_POST['supp_bill_subtotal'] == '') ? 0 : $_POST['supp_bill_subtotal'];
								$iSuppBillInterest = ($_POST['supp_bill_interest'] == '') ? 0 : $_POST['supp_bill_interest'];
								$iSuppPrevPrinciple = ($_POST['supp_principle_balance'] == '') ? 0 : $_POST['supp_principle_balance'];
								$iSuppPrevInterest = ($_POST['supp_interest_balance'] == '') ? 0 : $_POST['supp_interest_balance'];
								
								$supp_opening_balance = $iSuppBillSubTotal + $iSuppBillInterest;
								$supp_total_bill_payable = $supp_opening_balance + $iSuppPrevPrinciple + $iSuppPrevInterest; 
								
								$total_opening_balance = $total_bill_payable + $supp_total_bill_payable;
																			
								$obj_register = new regiser($this->m_dbConn);
								
								//$Date = $this->display_pg->curdate();
								$Date = $this->getOpeningBalanceDate();
								
								if(((float)$total_opening_balance) < 0)
								{
									$sqlInsert = "INSERT INTO `ledger`(`society_id`, `categoryid`, `ledger_name`, `sale`, `receipt`, `opening_balance`,`opening_type`) VALUES ('".$_SESSION['society_id']."', '" . DUE_FROM_MEMBERS . "', '" .$this->m_dbConn->escapeString($unitLedgerName). "', 1, 1, '" . abs($total_opening_balance) . "','1')";	
									$sqlLegerID = $this->m_dbConn->insert($sqlInsert);
								
									$insertAsset = $obj_register->SetAssetRegister(getDBFormatDate($Date), $sqlLegerID, 0, 0, TRANSACTION_CREDIT, abs($total_opening_balance), 1);
								}
								else
								{
									$sqlInsert = "INSERT INTO `ledger`(`society_id`, `categoryid`, `ledger_name`, `sale`, `receipt`, `opening_balance`,`opening_type`) VALUES ('".$_SESSION['society_id']."', '" . DUE_FROM_MEMBERS . "', '" .$this->m_dbConn->escapeString($unitLedgerName). "', 1, 1, '" . abs($total_opening_balance) . "','2')";	
									$sqlLegerID = $this->m_dbConn->insert($sqlInsert);
									
									$insertAsset = $obj_register->SetAssetRegister(getDBFormatDate($Date), $sqlLegerID, 0, 0, TRANSACTION_DEBIT, $total_opening_balance, 1);
									//pending : Add log for above queries
								}
								
								$getmax="select max(sort_order) as cnt from `unit` where society_id='".$_SESSION['society_id']."'";
								$getmaxID = $this->m_dbConn->select($getmax);
								$SortOrderID=$getmaxID[0]['cnt'] + 100;
								
								$isBlocked = 0;
								if(isset($_POST['Blocked']))
								{
								$isBlocked = 1; 
								}			
											
								$sql1 = "insert into unit(unit_id,society_id,wing_id,unit_no,floor_no,unit_type,composition,area,carpet,flat_configuration,commercial,residential,terrace,rand_no,intercom_no,sort_order,unit_presentation,resident_no,block_unit, taxable_no_threshold) values('" . $sqlLegerID . "', '".$_SESSION['society_id']."','".$_POST['wing_id']."','".$this->m_dbConn->escapeString($unitLedgerName)."','".$_POST['floor_no']."','".$_POST['unit_type']."','".$_POST['composition']."','".$_POST['area']."','".$_POST['carpet']."','".$_POST['flat_configuration']."','".$_POST['commercial']."','".$_POST['residential']."','".$_POST['terrace']."','".$rand_no."', '".$_POST['intercom_no']."',".$SortOrderID." , '".$_POST['unit_presentation']."', '".$_POST['resident_no']."','" . $isBlocked . "', '".$_POST['GSTNoExp']."')";
										
								$res1 = $this->m_dbConn->insert($sql1);
								
								$sql2="insert into `member_main`(society_id,wing_id,owner_name,unit,ownership_date,alt_address) values('".$_SESSION['society_id']."','".$_POST['wing_id']."','".$this->m_dbConn->escapeString($_POST['owner_name'])."','" . $sqlLegerID . "','".$mres2[0]['BeginingDate']."', '".$this->m_dbConn->escapeString($_POST['permanant_add'])."') ";
								$res2 = $this->m_dbConn->insert($sql2);

								$this->UpdateCoOwners($res2, $this->m_dbConn->escapeString($_POST['owner_name']),"","");

								$sql3="insert into `billdetails`(UnitID, PeriodID, BillSubTotal, BillInterest, PrincipalArrears, InterestArrears,TotalBillPayable, BillRegisterID) values(".$sqlLegerID.",'".$_POST['Period']."','".$iBillSubTotal."','".$iBillInterest."','" . $iPrevPrinciple . "', '" . $iPrevInterest . "', '"  . $total_bill_payable . "', '" . $this->getBillRegisterID($_POST['Period'], true) . "')";
								$res3 = $this->m_dbConn->insert($sql3);

								$sql3="insert into `billdetails`(UnitID, PeriodID, BillSubTotal, BillInterest, PrincipalArrears, InterestArrears,TotalBillPayable, BillRegisterID, BillType) values(".$sqlLegerID.",'".$_POST['Period']."','".$iBillSubTotal."','".$iBillInterest."','" . $iPrevPrinciple . "', '" . $iPrevInterest . "', '"  . $total_bill_payable . "', '" . $this->getBillRegisterID($_POST['Period'], true) . "',0)";
								$res3 = $this->m_dbConn->insert($sql3);

								if($supp_total_bill_payable !=0)
								{
								
									$sql4="insert into `billdetails`(UnitID, PeriodID, BillSubTotal, BillInterest, PrincipalArrears, InterestArrears,TotalBillPayable, BillRegisterID, BillType) values(".$sqlLegerID.",'".$_POST['Period']."','".$iSuppBillSubTotal."','".$iSuppBillInterest."','" . $iSuppPrevPrinciple . "', '" . $iSuppPrevInterest . "', '"  . $supp_total_bill_payable . "', '" . $this->getBillRegisterID($_POST['Period'], true) . "',0)";							
									
									$res3 = $this->m_dbConn->insert($sql4);
								}
								$insert_mapping = "INSERT INTO `mapping`(`society_id`, `unit_id`, `desc`, `code`, `role`, `created_by`, `view`) VALUES ('" . $_SESSION['society_id'] . "', '" . $sqlLegerID . "', '" . $unitLedgerName . "', '" . getRandomUniqueCode() . "', '" . ROLE_MEMBER . "', '" . $_SESSION['login_id'] . "', 'MEMBER')";
								$result_mapping = $this->m_dbConnRoot->insert($insert_mapping);
								
								$this->actionPage="../unit.php";
							}
						}
					}
					$this->m_dbConn->commit();
					return "Insert";
				}
				else
				{
					return "Unit Already exist";
				}
			}
			else
			{
				return "All * Field Required";
				//return "Some * field is missing";
			}
		}
		else if($_REQUEST['mode']=='Update' && $errorExists==0)
		{
			if(isset($_POST['id']) && $_POST['id'] <> "")
			{
				$this->actionPage="../unit.php?uid=".$_POST['id'];
			}
			$unitLedgerName = addslashes(trim($_POST['unit_no']));
			
			if($_POST['new_owner_name'] <> "" && $_POST['ownership_date'] <> "")
			{
				//transfer member ownership 
				$bIsOwnershipExists = false;
				$bIsOwnershipDateSmaller = false;	
				
				$sqlSelectCheck = "SELECT * FROM `member_main`  where `unit`='".$_POST['id']."' and `society_id`='".$_SESSION['society_id']."'  order by `ownership_date` desc ";
				$dataSelectCheck = $this->m_dbConn->select($sqlSelectCheck);
				
				if(sizeof($dataSelectCheck) > 0)
				{
					for($i = 0; $i < sizeof($dataSelectCheck);$i++)
					{
						$datediff = $this->obj_utility->getDateDiff(getDBFormatDate($_POST['new_ownership_date']),$dataSelectCheck[$i]['ownership_date']);
						
						if($datediff == 0)
						{
								$bIsOwnershipExists = true;	
								break;	
						}
						else if($datediff < 0)
						{
								$bIsOwnershipDateSmaller = true;
								break;	
						}	
					}
						
				}
				
				if($bIsOwnershipExists == false)
				{
					$sqlSelect = "SELECT `member_id` FROM `member_main`  where `owner_name`='".$this->m_dbConn->escapeString($_POST['owner_name'])."' and  `ownership_date` ='".getDBFormatDate($_POST['ownership_date'])."'  and  `unit`='".$_POST['id']."' and `society_id`='".$_SESSION['society_id']."' and `member_id` = '".$_POST['member_id']."' ";
					$dataSelect = $this->m_dbConn->select($sqlSelect);
					
					if($bIsOwnershipDateSmaller == false)
					{	
						$sqlUpdate = "update `member_main` set  `ownership_status` = '0', `transfer_reason` = '".$_POST['transfer_reason']."' where  `unit`='".$_POST['id']."' and `society_id`='".$_SESSION['society_id']."'  ";
						$dataUpdate = $this->m_dbConn->update($sqlUpdate);
						
							$tsql2="insert into `member_main`(society_id,wing_id,owner_name,unit,ownership_date,ownership_status,email,mob,iid) values('".$_SESSION['society_id']."','".$_POST['wing_id']."','".$this->m_dbConn->escapeString($_POST['new_owner_name'])."',".$_POST['id'].",'".getDBFormatDate($_POST['new_ownership_date'])."',1, '".$this->m_dbConn->escapeString($_POST['email'])."', '".$_POST['mob']."','".$_POST['iid']."') ";
							$tres2 = $this->m_dbConn->insert($tsql2);

						$this->UpdateCoOwners($tres2, $this->m_dbConn->escapeString($_POST['new_owner_name']),$this->m_dbConn->escapeString($_POST['mob']),$this->m_dbConn->escapeString($_POST['email']));

						$msg = "Ownership Transfer From <".$_POST['owner_name']." > To  <".$_POST['new_owner_name']." >";
						$msg .=" On Date <". getDBFormatDate($_POST['new_ownership_date']).">";
						$msg .=" Status <Active>";
						$this->m_objLog->setLog($msg, $_SESSION['login_id'], 'member_main', $tres2);
					}
					else
					{
							$tsql3="insert into `member_main`(society_id,wing_id,owner_name,unit,ownership_date,ownership_status,email,mob,iid) values('".$_SESSION['society_id']."','".$_POST['wing_id']."','".$this->m_dbConn->escapeString($_POST['new_owner_name'])."',".$_POST['id'].",'".getDBFormatDate($_POST['new_ownership_date'])."',0, '".$this->m_dbConn->escapeString($_POST['email'])."', '".$_POST['mob']."','".$_POST['iid']."') ";
						$tres3 = $this->m_dbConn->insert($tsql3);	
						$msg = "Record Added To Ownership Transfer History <".$_POST['new_owner_name']." >";
						$msg .=" On Date <". getDBFormatDate($_POST['new_ownership_date']).">";
						$msg .=" Status <InActive>";
						$this->m_objLog->setLog($msg, $_SESSION['login_id'], 'member_main', $tres3);	
					}
					$this->m_dbConn->commit();
				}
				else
				{
					
					return "Ownership Already Exists For  Unit On  '".$_POST['new_ownership_date']."' ";	
				}
				return "Update";
			}
			else if($_POST['society_id']<>"" && $_POST['wing_id']<>"" && $_POST['unit_no']<>"" && $_POST['owner_name']<>""  && $_POST['Period']<>"" && $_POST['Period'] > 0 && $_POST['unit_presentation']<>"" && $_POST['unit_presentation'] > 0 && $_POST['ownership_date'] <> "")
			{
				$isBlocked = 0;
				$block_desc = "";

				if(isset($_POST['Blocked']))
				{
					$isBlocked = 1; 
					if(isset($_POST['reason']))
					{
						$block_desc = $_POST['reason'];
					}
				}
				if($_POST['unit_presentation']==3 ||$_POST['unit_presentation']==4||$_POST['unit_presentation']==5){
                    $flate_config= "";
				}
				else{
				   $flate_config=$_POST['flattype'].'-'.$_POST['flat_configuration'];
				}
				 $up_query="update unit set `wing_id`='".$_POST['wing_id']."',`unit_no`='".$_POST['unit_no']."', `floor_no`='".$_POST['floor_no']."',`unit_type`='".$_POST['unit_type']."', `composition`='".$_POST['composition']."', `area`='".$_POST['area']."', `carpet`='".$_POST['carpet']."',`flat_configuration`='".$flate_config."', `commercial`='".$_POST['commercial']."', `residential`='".$_POST['residential']."',`terrace`='".$_POST['terrace']."', `intercom_no`='" . $_POST['intercom_no'] . "' , `unit_presentation` = '".$_POST['unit_presentation']."' , `resident_no` = '".$_POST['resident_no']."',`block_unit`='".$isBlocked."',`taxable_no_threshold` = '".$_POST['GSTNoExp']."',`block_desc`='".$block_desc."' where unit_id='".$_POST['id']."'";
				$data=$this->m_dbConn->update($up_query);
				
				
					$sql2="update `member_main` set `owner_name`='".$_POST['owner_name']."',`alt_address`='".$this->m_dbConn->escapeString($_POST['permanant_add'])."' where `unit`='".$_POST['id']."' and `society_id`='".$_SESSION['society_id']."' and  `member_id` = '".$_POST['member_id']."'";
					$data=$this->m_dbConn->update($sql2);

				//Maintenance Bill				
				$iBillSubTotal = ($_POST['bill_subtotal'] == '') ? 0 : $_POST['bill_subtotal'];
				$iBillInterest = ($_POST['bill_interest'] == '') ? 0 : $_POST['bill_interest'];
				$iPrevPrinciple = ($_POST['principle_balance'] == '') ? 0 : $_POST['principle_balance'];
				$iPrevInterest = ($_POST['interest_balance'] == '') ? 0 : $_POST['interest_balance'];
								
				$opening_balance = $iBillSubTotal + $iBillInterest;
				$total_bill_payable = $opening_balance + $iPrevPrinciple + $iPrevInterest; 
				
				//Supplimentary Bill
				$iSuppBillSubTotal = ($_POST['supp_bill_subtotal'] == '') ? 0 : $_POST['supp_bill_subtotal'];
				$iSuppBillInterest = ($_POST['supp_bill_interest'] == '') ? 0 : $_POST['supp_bill_interest'];
				$iSuppPrevPrinciple = ($_POST['supp_principle_balance'] == '') ? 0 : $_POST['supp_principle_balance'];
				$iSuppPrevInterest = ($_POST['supp_interest_balance'] == '') ? 0 : $_POST['supp_interest_balance'];
				
				$supp_opening_balance = $iSuppBillSubTotal + $iSuppBillInterest;
				$supp_total_bill_payable = $supp_opening_balance + $iSuppPrevPrinciple + $iSuppPrevInterest; 
				
				$total_opening_balance = $total_bill_payable + $supp_total_bill_payable;
															
				//if($_POST['principle_balance'] <> '')
				{
								
					$Date = $this->getOpeningBalanceDate();
					
					$checkAsset = "Select count(*) as rcdCnt from assetregister where LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
					$checkResult = $this->m_dbConn->select($checkAsset);
					
					$obj_register = new regiser($this->m_dbConn);
					
					if(((float)$total_opening_balance) < 0)
					{
						$sqlUpdate = "Update ledger SET opening_balance = '" . abs($total_opening_balance) .  "', ledger_name = '" . $_POST['unit_no'] . "',`opening_type` = '1'  Where id = '" . $_POST['id'] . "'" ;
						$result = $this->m_dbConn->update($sqlUpdate);
						
						if($checkResult[0]['rcdCnt'] > 0)
						{
							$sqlUpdate = "Update assetregister SET Credit = '" . abs($total_opening_balance) . "', Debit = 0 where LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
							$result = $this->m_dbConn->update($sqlUpdate);
						}
						else
						{
							$insertAsset = $obj_register->SetAssetRegister(getDBFormatDate($Date), $_POST['id'], 0, 0, TRANSACTION_CREDIT, abs($total_opening_balance), 1);
						}
					}
					else
					{
						$sqlUpdate = "Update ledger SET opening_balance = '" . abs($total_opening_balance) .  "', ledger_name = '" . $_POST['unit_no'] . "',`opening_type` = '2'  Where id = '" . $_POST['id'] . "'" ;
						$result = $this->m_dbConn->update($sqlUpdate);
						
						if($checkResult[0]['rcdCnt'] > 0)
						{
							$sqlUpdate = "Update assetregister SET Debit = '" . abs($total_opening_balance) . "', Credit = 0 where LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
							$result = $this->m_dbConn->update($sqlUpdate);
						}
						else
						{
							//echo getDBFormatDate($Date);
							$insertAsset = $obj_register->SetAssetRegister(getDBFormatDate($Date), $_POST['id'], 0, 0, TRANSACTION_DEBIT, abs($total_opening_balance), 1);	
						}
					}
					
					$checkBill = "Select *,count(*) as rcdCnt from billdetails where UnitID = '" . $_POST['id'] . "' and BillType = 0 and PeriodID = '".$_POST['Period']."'";
					$checkResult = $this->m_dbConn->select($checkBill);
										
					//Opening Maintenance bill
					if($checkResult[0]['rcdCnt'] > 0)
					{
						$msg = "Unit [".$_POST['id']."] Maintenance bill Updated  [PA:".$checkResult[0]['PrincipalArrears']." :: ".$_POST['principle_balance']."]";
						$msg .= " [IA:".$checkResult[0]['InterestArrears']." :: ".$_POST['interest_balance']."]";
						$msg .= " [BT:".$checkResult[0]['BillSubTotal']." :: ".$_POST['bill_subtotal']."]";
						$msg .= " [BI:".$checkResult[0]['BillInterest']." :: ".$_POST['bill_interest']."]";
						$this->m_objLog->setLog($msg, $_SESSION['login_id'], 'unit', $checkResult[0]['ID']);	
						
						$sqlUpdate="Update `billdetails` SET PrincipalArrears = '" . $_POST['principle_balance'] . "' ,InterestArrears = '" . $_POST['interest_balance'] . "', BillSubTotal = '" . $_POST['bill_subtotal'] . "', BillInterest = '" . $_POST['bill_interest'] . "', CurrentBillAmount = '" . $total_bill_payable . "', TotalBillPayable = '" . $total_bill_payable . "', BillRegisterID = '" . $this->getBillRegisterID($_POST['Period'], true) . "' Where PeriodID = '".$_POST['Period']."' and UnitID = '" . $_POST['id'] .  "' and BillType = 0";
						$result = $this->m_dbConn->update($sqlUpdate);
					}
					else
					{
						$sql3="insert into `billdetails`(UnitID, PeriodID, BillSubTotal, BillInterest, PrincipalArrears, InterestArrears, CurrentBillAmount, TotalBillPayable, BillRegisterID, BillType) values(".$_POST['id'].",'".$_POST['Period']."','".$iBillSubTotal."','".$iBillInterest."','" . $iPrevPrinciple . "', '" . $iPrevInterest . "', '" . $total_bill_payable . "', '" . $total_bill_payable . "', '" . $this->getBillRegisterID($_POST['Period'], true) . "',0)";
						$res3 = $this->m_dbConn->insert($sql3);
						
						$msg = "Unit [".$_POST['id']."] Maintenance bill inserted [PA:".$_POST['principle_balance']."]";
						$msg .= " [IA:".$_POST['interest_balance']."]";
						$msg .= " [BT:".$_POST['bill_subtotal']."]";
						$msg .= " [BI:".$_POST['bill_interest']."]";
						$this->m_objLog->setLog($msg, $_SESSION['login_id'], 'unit', $res3);	
					}
					
					//Opening Period Supplimentary Bill
					$checkBill = "Select *,count(*) as rcdCnt from billdetails where UnitID = '" . $_POST['id'] . "' and BillType = 1 and PeriodID = '".$_POST['Period']."'";
					$checkResult = $this->m_dbConn->select($checkBill);
					
					//Supplimentary bill update					
					if($checkResult[0]['rcdCnt'] > 0)
					{
						$msg = "Unit [".$_POST['id']."] Supplimentary Bill Updated  [PA:".$checkResult[0]['PrincipalArrears']." :: ".$_POST['supp_principle_balance']."]";
						$msg .= " [IA:".$checkResult[0]['InterestArrears']." :: ".$_POST['supp_interest_balance']."]";
						$msg .= " [BT:".$checkResult[0]['BillSubTotal']." :: ".$_POST['supp_bill_subtotal']."]";
						$msg .= " [BI:".$checkResult[0]['BillInterest']." :: ".$_POST['supp_bill_interest']."]";
						$this->m_objLog->setLog($msg, $_SESSION['login_id'], 'unit', $checkResult[0]['ID']);	
						
						//Update Supplimentary Bill
						$sqlUpdate="Update `billdetails` SET PrincipalArrears = '" . $_POST['supp_principle_balance'] . "' ,InterestArrears = '" . $_POST['supp_interest_balance'] . "', BillSubTotal = '" . $_POST['supp_bill_subtotal'] . "', BillInterest = '" . $_POST['supp_bill_interest'] . "', CurrentBillAmount = '" . $supp_total_bill_payable . "', TotalBillPayable = '" . $supp_total_bill_payable . "', BillRegisterID = '" . $this->getBillRegisterID($_POST['Period'], true) . "' Where PeriodID = '".$_POST['Period']."' and BillType = 1 and UnitID = '" . $_POST['id'] .  "'";
						$result = $this->m_dbConn->update($sqlUpdate);
					}
					else
					{
						//Insert Supplimentary Bill, if it exist (if balance is more than 0)
						if($supp_total_bill_payable != 0)
						{
							$sql3="insert into `billdetails`(UnitID, PeriodID, BillSubTotal, BillInterest, PrincipalArrears, InterestArrears, CurrentBillAmount, TotalBillPayable, BillRegisterID, BillType) values(".$_POST['id'].",'".$_POST['Period']."','".$iSuppBillSubTotal."','".$iSuppBillInterest."','" . $iSuppPrevPrinciple . "', '" . $iSuppPrevInterest . "', '" . $supp_total_bill_payable . "', '" . $supp_total_bill_payable . "', '" . $this->getBillRegisterID($_POST['Period'], true) . "',1)";
							$res3 = $this->m_dbConn->insert($sql3);
							
							$msg = "Unit [".$_POST['id']."] Opening Supplimentaty Bill Inserted [PA:".$_POST['principle_balance']."]";
							$msg .= " [IA:".$_POST['interest_balance']."]";
							$msg .= " [BT:".$_POST['bill_subtotal']."]";
							$msg .= " [BI:".$_POST['bill_interest']."]";
							$this->m_objLog->setLog($msg, $_SESSION['login_id'], 'unit', $res3);	
						}
						else
						{
							$msg = "Supplimentary bill is 0. So Not inserted.";
							$this->m_objLog->setLog($msg, $_SESSION['login_id'], 'unit', $checkResult[0]['ID']);
						}

					}

					
					$sql2="update `member_main` set `owner_name`='".$this->m_dbConn->escapeString($_POST['owner_name'])."' , `ownership_date` ='".getDBFormatDate($_POST['ownership_date'])."'  where `unit`='".$_POST['id']."' and `society_id`='".$_SESSION['society_id']."' and `member_id` = '".$_POST['member_id']."' ";
					$data=$this->m_dbConn->update($sql2);	
			}
			$this->m_dbConn->commit();
				return "Update";
			}
			else
			{
				return "Some * field is missing";
			}
		}
		else
		{
			$this->m_dbConn->commit();
			return $errString;
		}
		}
		catch(Exception $exp)
		{
			$this->m_dbConn->rollback();
			return $exp;
		}
	}
	
	private function UpdateCoOwners($member_id, $owner_names, $mobile, $email)
	{
		$owner = str_replace('&', ',', $owner_names);
		$owner = str_replace('/', ',', $owner);
		$owner = str_replace(' AND ', ',', $owner);
		$owner_coll = explode(',', $owner);
		
		$updatePrimaryOwner = "Update `member_main` SET `primary_owner_name` = '" . trim($owner_coll[0]) . "' WHERE `member_id` = '" . $member_id . "'";

		$resPrimaryOwner = $this->m_dbConn->update($updatePrimaryOwner);

		for($i = 0; $i < sizeof($owner_coll); $i++)
		{
			if($i == 0)
			{
				$insertCoOwner = "INSERT INTO `mem_other_family` (`member_id`, `other_name`, `coowner`, `relation`, `other_mobile`, `other_email`, `send_commu_emails`) VALUES ('" . $member_id . "', '" . trim($owner_coll[$i]) . "', '1', 'Self', '" . $mobile . "', '" . $email . "', '1')";
			}
			else
			{
				$insertCoOwner = "INSERT INTO `mem_other_family` (`member_id`, `other_name`, `coowner`) VALUES ('" . $member_id . "', '" . trim($owner_coll[$i]) . "', '2')";
			}

			$resCoOwner = $this->m_dbConn->insert($insertCoOwner);
		}
	}
	
	public function getUnitDetails()
	{
		//declare GSTNoThresholdFlag_Units to hold all the unit on  GSTNothreshold will apply
		$GSTNoThresholdFlag_Units = array();
		//Fetching Member Data to show in Freezing part
		$sql = "Select unit.unit_id,unit.unit_no,unit.taxable_no_threshold,wing.wing,member_main.primary_owner_name,member_main.member_id from unit JOIN wing ON unit.wing_id = wing.wing_id JOIN member_main ON member_main.unit = unit.unit_id where member_main.ownership_status = 1";
		$Result = $this->m_dbConn->select($sql);
		$societyInfo = $this->obj_utility->GetSocietyInformation($_SESSION['society_id']);	
		$ResultPreviousPeriodData = $this->obj_fetch->getPreviousPeriodData($_REQUEST['period'],true);

		$GSTNoThresholdFlag = $this->obj_genbill->GetGSTNoThresholdFlag_perMember($societyInfo, $_REQUEST['period'], $ResultPreviousPeriodData[0]['PrevPeriodID'], $ResultPreviousPeriodData[0]['BeginingDate'], $ResultPreviousPeriodData[0]['EndingDate'],0);
		//GSTNoThresholdFlag return all the Calculated GST NO Threshold Units
		//Below function will push the unit in Result so we can use it to show comparision
		$cnt = 0;
		for($i = 0 ; $i < sizeof($GSTNoThresholdFlag); $i++)
		{
			for($j= 0 ; $j < sizeof($GSTNoThresholdFlag[$i]['nt_unit']);$j++)
			{
				$GSTNoThresholdFlag_Units[$cnt] = $GSTNoThresholdFlag[$i]['nt_unit'][$j]['unit_id'];
				$cnt++;
			}
		}
		
		//Here setting the values
		for($i = 0 ; $i < sizeof($Result);$i++)
		{
			if(in_array($Result[$i]['unit_id'],$GSTNoThresholdFlag_Units))
			{
				$Result[$i]['ThresholdCalculatedUnit'] = 1;
			}
			else
			{
				$Result[$i]['ThresholdCalculatedUnit'] = 0;
			}
		}
		return $Result;
	}
	
	
	public function ShowGSTNoThreshold()
	{
		$UnitDetails = $this->getUnitDetails();
		if(true)
		{ ?>
        			
		 <table width="100%" style="border: 1px solid;">
			
				<tr height="25" align="center" style="width:80%">
					<th align="center" style="width:5%;text-align: center;border-left:1px solid #e8e8e8;;">Sr No</th>
					<th align="center" style="width:5%;text-align: center;border-left:1px solid #e8e8e8;;">Wing</th>
					<th align="center" style="width:10%;text-align: center;border-left:1px solid #e8e8e8;">Unit No.</th>
                    <th align="center" style="width:25%;text-align: center;border-left:1px solid #e8e8e8;">Member Name</th>
					<th align="center" style="width:10%;text-align: center;border-left:1px solid #e8e8e8;">Current Threshold flag</th>
					<th align="center" style="width:10%;text-align: center;border-left:1px solid #e8e8e8;">Threshold Flag Should Be </th>					
					<th align="center" style="width:10%;text-align: center;border-left:1px solid #e8e8e8;">Status</th>
				</tr>
            </table>
            <div class="scrollableContainer">
 			<div class="scrollingArea">
			<table width="100%" id="example" class="display">
          
        <?php
		$iCounter = 1;
		foreach($UnitDetails as $k => $v)
		{
		?>
        	<tr height="25" align="center" <?php if($UnitDetails[$k]['taxable_no_threshold'] <>$UnitDetails[$k]['ThresholdCalculatedUnit']){?>style="background-color:#f44336" <?php }?>>
        	<td style="width:5.4%;text-align: center;border-left:1px solid #e8e8e8;"><?php echo $iCounter++;?></td>
            <td style="width:5.3%;text-align: center;border-left:1px solid #e8e8e8;"><?php echo $UnitDetails[$k]['wing'];?></td>
            <td align="center" style="width:10.7%;text-align: center;border-left:1px solid #e8e8e8;"><?php echo $UnitDetails[$k]['unit_no'];?></td>
            <td align="center" style="width:26.1%;text-align: center;border-left:1px solid #e8e8e8;"><a href="view_member_profile.php?scm&id=<?php echo $UnitDetails[$k]['member_id'];?>&tik_id=<?php echo time();?>&m&view" target="_blank"><?php echo $UnitDetails[$k]['primary_owner_name'];?></a></td>
            <td align="center" style="width:10%;border-left:1px solid #e8e8e8;"><input type="checkbox" <?php if($UnitDetails[$k]['taxable_no_threshold'] == 1){ ?> checked<?php }?>></td>
            <td align="left" style="width:10%;text-align: center;border-left:1px solid #e8e8e8;"><input type="checkbox" <?php if($UnitDetails[$k]['ThresholdCalculatedUnit'] == 1){ ?> checked<?php }?>></td>						
            <?php if($UnitDetails[$k]['taxable_no_threshold'] == $UnitDetails[$k]['ThresholdCalculatedUnit'])
			{ ?>
				<td align="center" style="width:10%;text-align: center;border-left:1px solid #e8e8e8;"><img src="images/clear.png" alt="Smiley face" height="42" width="42"></td>
			<?php }
            else
            { ?>
            	<td align="center" style="width:10%;text-align: center;border-left:1px solid #e8e8e8;"><img src="images/can.png" alt="Smiley face" height="42" width="42"></td>
           	<?php }?>
            </tr>
        <?php 
			}
		?>
        </table>
        </div>
        </div>
        
    
		<?php
		}
		else
		{
			?>
            <table align="center" border="0">
            <tr>
            	<td><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
            </tr>
            </table>
        <?php }
		return $body;
	}
	
	public function getOpeningBalanceDate()
	{
		$currentYear = $_SESSION['default_year'];
		
		//$sql = "Select periodtbl.BeginingDate from period as periodtbl JOIN society as societytbl ON societytbl.bill_cycle = periodtbl.Billing_cycle where YearID = '" . $currentYear . "' ORDER BY periodtbl.ID ASC";
		
		//$result = $this->m_dbConn->select($sql);
		$OpeningBalanceDate = $this->obj_utility->GetDateByOffset($_SESSION['default_year_start_date'] , -1);
		return $OpeningBalanceDate;
		//return $result[0]['BeginingDate'];
	}
	
	public function combobox($query, $id, $bShowAll = false)
	{
		if($bShowAll == true)
		{
			$str.="<option value='0'>All</option>";
		}
		else
		{
			$str.="<option value=''>Please Select</option>";
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
	
	public function combobox00($query,$id)
	{
	$str.="<option value=''>All Wing</option>";
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
	public function display1($rsas, $bShowViewLink = false)
	{
		//echo "inside display1";
		$thheader=array('Society Name','Wing','Unit No.');
		$this->display_pg->edit="getunit";
		$this->display_pg->th=$thheader;
		$this->display_pg->mainpg="unit.php";
			//	echo "calling showunit";
		//$res=$this->display_pg->display_new($rsas);
		$res=$this->show_unit($rsas, $bShowViewLink);
		//echo "exiting display1";
		return $res;
	}
	
	public function pgnation_bill($society_id, $wing_id, $unit_id, $period_id,$IsReadonlyPage, $BillType)
	{
		$unit_array = array();
		//$memberIDS = $this->obj_utility->getMemberIDs($_SESSION['default_year_end_date']);	
		$sqlPeriod = "SELECT max(id),PeriodID,BillDate,DueDate FROM `billregister` where `PeriodID` = '".$period_id."' ";
		$resultPeriod = $this->m_dbConn->select($sqlPeriod);
		$sql1 = 'select bill.UnitID, bill.PeriodID, unittbl.unit_id, unittbl.unit_no, wingtbl.wing, societytbl.society_name, membertbl.owner_name,membertbl.member_id, societytbl.society_code,unittbl.unit_presentation  from billdetails as bill JOIN unit as unittbl on bill.UnitID = unittbl.unit_id JOIN wing as wingtbl on unittbl.wing_id = wingtbl.wing_id JOIN society as societytbl on unittbl.society_id = societytbl.society_id JOIN member_main as membertbl ON unittbl.unit_id = membertbl.unit where unittbl.status = "Y" and bill.PeriodID = "' . $period_id . '" and societytbl.society_id = "' . $society_id . '"  and bill.BillType="'. $BillType.'" and membertbl.member_id IN (SELECT `member_id` FROM (select  DISTINCT(unit),`member_id` from `member_main` where ownership_date <="'.$resultPeriod[0]['BillDate'].'"  ORDER BY ownership_date desc) as member_id Group BY unit) '; 
		
		if($wing_id <> 0)
		{
			$sql1 .= ' and unittbl.wing_id = "' . $wing_id . '"';
		}		
		if($unit_id <> 0)
		{
			$sql1 .= ' and unittbl.unit_id = "' . $unit_id . '"';
		}
		
		$sql1 .= ' Group BY unittbl.unit_id  ORDER BY unittbl.`sort_order` ASC';
		//echo $sql1;
		
		$get_unit_desc= "select id,description from `unit_type`";
		$result02 = $this->m_dbConn->select($get_unit_desc);
		
		for($i = 0; $i < sizeof($result02); $i++)
		{
			$unit_array[$result02[$i]['id']] = $result02[$i]['description'];
		
		}
		
		$result = $this->m_dbConn->select($sql1);
		//$isBill=true;
		
		for($i = 0; $i < sizeof($result); $i++)
		{
			$result[$i]['unit_presentation'] = str_replace(' No.', '', $unit_array[$result[$i]['unit_presentation']]);
			
		}
		
		$this->show_unit($result, true,true,true,$IsReadonlyPage, $BillType);
	}
	
	public function pgnation($bShowViewLink = false,$IsReadonlyPage =false)
	{
		$unit_array = array();
		
		$get_unit_desc= "select id,description from `unit_type`";
		$result02 = $this->m_dbConn->select($get_unit_desc);
		
		for($i = 0; $i < sizeof($result02); $i++)
		{
			$unit_array[$result02[$i]['id']] = $result02[$i]['description'];
		
		}
		
		if($bShowViewLink)
		{
			$_REQUEST['insert']='Search';
		}
		
		if($_REQUEST['insert']=='Search')
		{
			 $sql1 = "select u.unit_id,s.society_id,u.unit_presentation,s.society_name,w.wing_id,w.wing,u.unit_no,u.rand_no,m.owner_name,m.member_id,u.resident_no,u.block_unit,u.block_desc from 
					 unit as u,society as s,wing as w,member_main as m
					 where u.status='Y' and w.status='Y' and s.status='Y' and m.status='Y' and 
					 u.wing_id=w.wing_id and u.unit_id=m.unit and u.society_id='".$_SESSION['society_id']."' and s.society_id='".$_SESSION['society_id']."' and 	m.society_id='".$_SESSION['society_id']."' and  m.member_id IN (SELECT  `member_id` FROM (select  DISTINCT(unit),`member_id` from `member_main` where ownership_date <= '" .$_SESSION['default_year_end_date']. "'  ORDER BY ownership_date desc) as member_id Group BY unit)  ";
					 
			if($_REQUEST['society_id']<>"")
			{		 
				$sql1 .= " and s.society_id='".$_REQUEST['society_id']."'";
			}
			else
			{
				if(isset($_SESSION['admin']))
				{
					$sql1 .= " and s.society_id='".$_SESSION['society_id']."'";
				}
			}
			
			if($_REQUEST['wing_id']<>"")
			{		 
				$sql1 .= " and w.wing_id='".$_REQUEST['wing_id']."'";
			}
			
			if($_REQUEST['unit_no']<>"")
			{		 
				$sql1 .= " and u.unit_no='".$_REQUEST['unit_no']."'";
			}
			
			if($_REQUEST['owner_name']<>"")
			{		 
				$sql1 .= " and m.owner_name LIKE '%".$_REQUEST['owner_name']."%'";
			}
			$sql1 .= " Group BY u.unit_id order by u.sort_order";	
			
			
		}
		else
		{
		 $sql1 = "select u.unit_id,s.society_id,u.unit_presentation,s.society_name,w.wing_id,w.wing,u.unit_no,u.rand_no,m.owner_name,m.member_id,u.resident_no,u.block_unit from 
					 unit as u,society as s,wing as w,member_main as m
					 where u.status='Y' and w.status='Y' and s.status='Y' and m.status='Y' and 
					 u.wing_id=w.wing_id and u.unit_id=m.unit and u.society_id='".$_SESSION['society_id']."' and s.society_id='".$_SESSION['society_id']."' and m.society_id='".$_SESSION['society_id']."' and m.member_id IN (SELECT  `member_id` FROM (select  DISTINCT(unit),`member_id` from `member_main` where ownership_date <= '" .$_SESSION['default_year_end_date']. "'  ORDER BY ownership_date desc) as member_id Group BY unit)  ";
					 
			if(isset($_REQUEST['wing_id']) && $_REQUEST['wing_id'] <> "")
			{
				
				$sql1 .= " and u.wing_id=".$_REQUEST['wing_id']."";
			}		 
					 
					 
			if(isset($_SESSION['sadmin']))
			{
				if(isset($_REQUEST['sa']))
				{
					$sql1 .= " and s.society_id='".$_REQUEST['sid']."' and w.wing_id='".$_REQUEST['wid']."'";
				
				}
			}
			
			if(isset($_REQUEST['ssid']) && isset($_REQUEST['wwid']))
			{		 
				$sql1 .= " and s.society_id='".$_REQUEST['ssid']."' and w.wing_id='".$_REQUEST['wwid']."' ";
			}
			else
			{
				if(isset($_SESSION['sadmin']))
				{
					//$sql1 .= " group by  u.unit_id  order by u.sort_order";	
				}
				else
				{
					$sql1 .= " and s.society_id='".$_SESSION['society_id']."' ";	
				}
			}
			$sql1 .= "  Group BY u.unit_id order by u.sort_order";	
			//echo $sql1;
			
		}
		
		
		if($_REQUEST['insert']=='Search')
		{
			$cntr = "select count(*) as cnt from 
					 unit as u,society as s,wing as w
					 where u.status='Y' and w.status='Y' and s.status='Y' and 
					 u.wing_id=w.wing_id and u.society_id='".$_SESSION['society_id']."' and s.society_id='".$_SESSION['society_id']."' and m.member_id IN (SELECT  `member_id` FROM (select  DISTINCT(unit),`member_id` from `member_main` where ownership_date <= '" .$_SESSION['default_year_end_date']. "'  ORDER BY ownership_date desc) as member_id Group BY unit) ";
			if($_REQUEST['society_id']<>"")
			{		 
				$cntr .= " and s.society_id='".$_REQUEST['society_id']."'";
			}
			else
			{
				$cntr .= " and s.society_id='".$_SESSION['society_id']."'";
			}
			if($_REQUEST['wing_id']<>"")
			{		 
				$cntr .= " and w.wing_id='".$_REQUEST['wing_id']."'";
			}
			if($_REQUEST['unit_no']<>"")
			{		 
				$cntr .= " and u.unit_no='".$_REQUEST['unit_no']."' ";
			}	
			$cntr .= "  Group BY u.unit_id order by u.sort_order";
		}
		else
		{
			$cntr = "select count(*) as cnt from 
					 unit as u,society as s,wing as w
					 where u.status='Y' and w.status='Y' and s.status='Y' and 
					 u.wing_id=w.wing_id and u.society_id='".$_SESSION['society_id']."' and s.society_id='".$_SESSION['society_id']."' and m.member_id IN (SELECT  `member_id` FROM (select  DISTINCT(unit),`member_id` from `member_main` where ownership_date <= '" .$_SESSION['default_year_end_date']. "'  ORDER BY ownership_date desc) as member_id Group BY unit) ";
			
			if(isset($_REQUEST['wing_id']) && $_REQUEST['wing_id'] <> "")
			{
				
				$cntr .= " and u.wing_id=".$_REQUEST['wing_id']."";
			}	
			if(isset($_SESSION['sadmin']))
			{
				if(isset($_REQUEST['sa']))
				{
					$cntr .= " and s.society_id='".$_REQUEST['sid']."' and w.wing_id='".$_REQUEST['wid']."'";
				}
			}

			if(isset($_REQUEST['ssid']) && isset($_REQUEST['wwid']))
			{		 
				$cntr .= " and s.society_id='".$_REQUEST['ssid']."' and w.wing_id='".$_REQUEST['wwid']."' ";
			}
			else
			{
				if(isset($_SESSION['sadmin']))
				{
					//$cntr .= " order by u.sort_order";	
				}
				else
				{
					$cntr .= " and s.society_id='".$_SESSION['society_id']."' ";	
				}
				
			}
			$cntr .= "  Group BY u.unit_id order by u.sort_order";
		}
		
		
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$this->display_pg->mainpg="unit.php";
		$limit = "50";
		$page=$_REQUEST['page'];
		
		if(isset($_SESSION['sadmin']))
		{
			if(isset($_REQUEST['sa']))
			{
				$extra = "&imp&id=".time()."&sa&sid=".$_REQUEST['sid']."&wid=".$_REQUEST['wid']."&id=".$_REQUEST['id'].'&wing_id='.$_REQUEST['wing_id'];
			}
			else
			{
				$extra = "&imp&id=".time();
			}
			
		}
		else
		{
			$extra = "&imp&ws&ssid=".$_REQUEST['ssid']."&wwid=".$_REQUEST['wwid']."&idd=".time().'&unit_no='.$_REQUEST['unit_no'].'&wing_id='.$_REQUEST['wing_id'];
		}
		
		//$res = $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		//return $res;
		$result = $this->m_dbConn->select($sql1);
		
		for($i = 0; $i < sizeof($result); $i++)
		{
			$result[$i]['unit_presentation'] = str_replace(' No', '', $unit_array[$result[$i]['unit_presentation']]);
			
		}
		
		$this->show_unit($result, false, true,false,$IsReadonlyPage);
	}
	
	public function show_unit($res, $bShowViewLink = false, $bShowEdit = false,$isBill = false,$IsReadonlyPage = false, $BillType = 0)
	{
		$EncodeUnitArray;
		$EncodeUrl;
		if($BillType != 1)
		{
			$BillType = 0;
		}
		$UnitArray = $this->getAllUnits();
		if(sizeof($UnitArray) > 0)
		{
			$EncodeUnitArray = json_encode($UnitArray);
			$EncodeUrl = urlencode($EncodeUnitArray);
		}
		if($bShowViewLink == true)
		{
			$sqlPeriod = "Select periodtbl.type, periodtbl.BeginingDate, yeartbl.YearDescription from period as periodtbl JOIN year as yeartbl ON periodtbl.YearID = yeartbl.YearID where periodtbl.ID = '" . $_REQUEST['period_id'] . "'";
		
			$sqlResult = $this->m_dbConn->select($sqlPeriod);
			$startyear = array();
			$BeginingDate = array();
			$startyear = explode('-',$sqlResult[0]["YearDescription"]);
			$BeginingDate = explode('-',$sqlResult[0]["BeginingDate"]);
			if($startyear[0] >= $BeginingDate[0])
			{
				$RetrunVal .= " " . $startyear[0];
			}
			else if($startyear[0] <= $BeginingDate[0])
			{
				$RetrunVal .= " " . $BeginingDate[0];
			}
			$RetrunVal;
			//var_dump($BeginingDate);
			//echo "<b><font color='#0000FF'> Bill's For : " . $sqlResult[0]['type'] . " "  . $sqlResult[0]['YearDescription'] . "</font></b><br><br>";
		}
		if($res<>"")
		{
					
			//print_r($res);
			if(!isset($_REQUEST['page']))
			{
				$_REQUEST['page'] = 1;
			}
			$iCounter = 1 + (($_REQUEST['page'] - 1) * 50);
	
		?>
		<table id="example" class="display" cellspacing="0" width="100%">
        <thead>
		<tr height="30">
        	<th width="10">Sr No.</th>
        	<!--<th width="300">Society Name</th>-->
            <th width="10">Wing</th>
             <th width="10">Unit Type</th>
            <th width="10">Unit No.</th>
            <th width="50">Member Name</th>
            <th width="20">Due</th>
            <th width="50">Reverse charge/Credit</th>
			<?php if($bShowEdit == true && $isBill == true && $_SESSION['role']==ROLE_SUPER_ADMIN) {
				//echo "test"; ?> 
           		<!--<th width="50">Edit</th>-->
			<?php }
			else if($bShowEdit == true && $isBill == false){
				 if($_SESSION['role'] != ROLE_ADMIN_MEMBER && $IsReadonlyPage == false && $_SESSION['is_year_freeze'] == 0){?>
            	<th width="50">Edit</th>
                <?php } ?>
                    <th width="20">Transfer History</th>
            <?php }if($bShowViewLink == true) { ?> 
             	<th width="20">Bill</th>
			 	
               <?php }
                if($_SESSION['role'] == ROLE_SUPER_ADMIN && $isBill == true && $_SESSION['is_year_freeze'] == 0)
                {?>
                 <th width="70">Delete</th>
                <?php } 
                if($isBill == true)
                {?>
                 <th width="500">PDF</th>
                <?php }?>
            
            
            <?php if(isset($_SESSION['admin'])){?>
                <th width="100">Code</th>
				<?php }?>
            
            <?php if(isset($_GET['ws'])){?>
        	<th width="70">Edit</th>
            <th width="70">Delete</th>
            <?php }?>
            
        </tr>
        </thead>
        <tbody>
		<script>var unitArray = []; </script> 
        <?php foreach($res as $k => $v){
			$specialChars = array('/', '.', '*', '%', '&', ',', '(', ')', '"');
        		$UnitNo = str_replace($specialChars, '', $res[$k]['unit_no']);?>
			<script>unitArray.push(<?php echo $res[$k]['unit_id']; ?>)</script>
			
        	<tr height="25" bgcolor="#BDD8F4" align="center">
        	<td align="center"><?php echo $iCounter++;?></td>
        	<!--<td align="center"><?php //echo $res[$k]['society_name'];?></td>-->
            <td align="center"><?php echo $res[$k]['wing'];?></td>
            <td align="center"><?php echo $res[$k]['unit_presentation'];?></td>
            <td align="center" id="<?php echo 'unit_no_'.$res[$k]['unit_id']?>"> <a href="view_member_profile.php?scm&id=<?php echo $res[$k]['member_id'];?>&tik_id=<?php echo time();?>&m&view" target="_blank"><?php echo $res[$k]['unit_no'];?></a></td>
            
            <td align="center" id="<?php echo 'owner_name_'.$res[$k]['unit_id']?>"><a href="view_member_profile.php?scm&id=<?php echo $res[$k]['member_id'];?>&tik_id=<?php echo time();?>&m&view" target="_blank"><?php echo $res[$k]['owner_name'];?></a></td>
            <?php 
			if(sizeof($UnitArray) > 0)
			{
				$Url = "member_ledger_report.php?&uid=".$res[$k]['unit_id']."&Cluster=".$EncodeUrl;
			}
			else
			{
				$Url = "member_ledger_report.php?&uid=".$res[$k]['unit_id'];
			}
			?>
            <td align="center"><a href="#" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes');" style="color:#0000FF;"><?php echo $this->obj_utility->getDueAmount($res[$k]['unit_id']);;?></a></td>
            <td align="center">
            <a href="reverse_charges.php?&uid=<?php echo $res[$k]['unit_id'];?>" style="color:#0000FF;"><b>Reverse charge/Credit</b></a>
            </td>
            <?php 				
			if($bShowEdit == true && $isBill == true && $_SESSION['role']==ROLE_SUPER_ADMIN && $_SESSION['is_year_freeze'] == 0) { ?> 
           		<!-- <td align="center"><?php echo "<a href='Maintenance_bill.php?UnitID=".$res[$k]['unit_id']."&PeriodID=".$_REQUEST['period_id']."&BT=".$BillType."&edt' target='_blank'><img src='images/edit.gif' /></a>" ?> </td>-->
			<?php }
			else if($bShowEdit == true && $isBill == false && $_SESSION['is_year_freeze'] == 0){
             if($_SESSION['role'] != ROLE_ADMIN_MEMBER && $IsReadonlyPage == false){?>
            	<td align="center"><a href="unit.php?uid=<?php echo $res[$k]['unit_id']?>"><img src="images/edit.gif" /></a></td>
                <?php }
			
				?>
                <td align="center">
            <div onClick="viewMemberStatus('<?php echo $res[$k]['unit_id'];?>');" style="color:#0000FF;cursor: pointer;"><img src='images/view.jpg' border='0' alt='View' style='cursor:pointer;' width="18" height="15" /></div>
            </td>
            <?php }
			if($bShowViewLink == true) 
			{ ?> 
            	<script>//unitArray.push(<?php //echo $res[$k]['unit_id']; ?>)</script>
             <td align="center"><?php echo "<A href='Maintenance_bill.php?UnitID=".$res[$k]['unit_id']."&PeriodID=".$_REQUEST['period_id']."&BT=".$BillType."' target='_blank'>View</A>" ?> </td>
            <?php 
			
			if($_SESSION['role'] == ROLE_SUPER_ADMIN && $isBill == true && $_SESSION['is_year_freeze'] == 0)
            {
				//echo $_SESSION['login_id'];?>
             <td align="center" style="width:1%;"><a onClick='billDelete("<?php echo $res[$k]['unit_id']?>","<?php echo $_REQUEST['period_id']?>","<?php echo $_SESSION['login_id']?>");'><img src='images/del.gif' border='0' alt='Delete' style='cursor:pointer;'/></a></td>
            <?php } ?>
             <td align="center" width="1%"><iframe src="<?php echo 'pdfbill.php?pdffile=maintenance_bills/' . $res[$k]['society_code'] . '/' . $sqlResult[0]['type'].''.$RetrunVal.'/bill-' . $res[$k]['society_code'] . '-' . $UnitNo . '-' . $sqlResult[0]['type'].''.$RetrunVal. '-'.$BillType.'.pdf'; ?>" name="pdfexport_<?php echo $res[$k]['unit_id']; ?>" id="pdfexport_<?php //echo $res[$k]['unit_id']; ?>" style="border:0px solid #0F0;width:130px;height:50px;"></iframe></td>
            <!--<td align="center"><div id="status_<?php //echo $res[$k]['unit_id']; ?>" style="color:#0033FF; font-weight:bold;"></div></td>-->
             
             <!--<td align="center"><?php //echo "<a href='Maintenance_bill_edit.php?UnitID=".$res[$k]['unit_id']."&PeriodID=".$_REQUEST['period_id']."' target='_blank'>Edit</a>" ?> </td>--><?php }?>
			
            <?php if(isset($_SESSION['admin'])){?><td align="center"><?php echo $res[$k]['rand_no'];?></td><?php } ?>
            
            <?php if(isset($_GET['ws'])){?>
            <td align="center">
            <a href="javascript:void(0);" onclick="getunit('edit-<?php echo $res[$k]['unit_id']?>')"><img src="images/edit.gif" /></a>
            </td>
            
            <td align="center">
            <?php if($this->chk_delete_perm_admin()==1){?>
            <a href="javascript:void(0);" onclick="getunit('delete-<?php echo $res[$k]['unit_id']?>');"><img src="images/del.gif" /></a>
            <?php }else{?>
            <a href="del_control_admin.php?prm" target="_blank" style="text-decoration:none;"><font color=#FF0000 style='font-size:10px;'><b>Not Allowed</b></font></a>
            <?php }?>
            </td>
            <?php }?>
             
            </tr>
        <?php 
		}?>
        </tbody>
        </table>

		<?php
		}
		else
		{
			?>
            <table align="center" border="0">
            <tr>
            	<td><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
            </tr>
            </table>
            <?php		
		}
	}
	
	public function selecting()
	{
			//$sql1="select `unit_no` from unit where unit_id='".$_REQUEST['unitId']."'";
			
			//$sql1="select unittbl.`unit_id`, unittbl.`society_id`, unittbl.`wing_id`, unittbl.`unit_no`, membertbl.`owner_name`,DATE_FORMAT(membertbl.ownership_date, '%d-%m-%Y') as ownership_date, unittbl.`floor_no`, unittbl.`unit_type`, unittbl.`composition`, unittbl.`area`, unittbl.`carpet`, unittbl.`commercial`, unittbl.`residential`, unittbl.`terrace`, unittbl.`intercom_no`, unittbl.unit_presentation FROM unit as unittbl JOIN member_main as membertbl ON unittbl.unit_id = membertbl.unit where unittbl.unit_id='".$_REQUEST['unitId']."' and unittbl.society_id='".$_SESSION['society_id']."' and DATE(NOW()) >= membertbl.`ownership_date`  ";
			$sql1="select unittbl.`unit_id`,membertbl.`member_id`, unittbl.`wing_id`, unittbl.`unit_no`, membertbl.`owner_name`,DATE_FORMAT(membertbl.ownership_date, '%d-%m-%Y') as ownership_date, unittbl.`floor_no`, unittbl.`unit_type`, unittbl.`composition`, unittbl.`area`, unittbl.`carpet`,unittbl.`flat_configuration`, unittbl.`commercial`, unittbl.`residential`, unittbl.`terrace`, unittbl.`intercom_no`, unittbl.unit_presentation, membertbl.alt_address, unittbl.resident_no,unittbl.block_unit,unittbl.block_desc, unittbl.taxable_no_threshold FROM unit as unittbl JOIN member_main as membertbl ON unittbl.unit_id = membertbl.unit where unittbl.unit_id='".$_REQUEST['unitId']."' and unittbl.society_id='".$_SESSION['society_id']."' and membertbl.ownership_date <='".$_SESSION['default_year_end_date']."' order by membertbl.ownership_date desc";
			//echo $sql1;
			$var=$this->m_dbConn->select($sql1);
			
			
			$sqlFetch="SELECT `society_creation_yearid` FROM `society` where society_id = '".$_SESSION['society_id']."'";
			$res = $this->m_dbConn->select($sqlFetch);
			
			$currentYear = $res[0]['society_creation_yearid'];
		
			//$sql = "Select periodtbl.ID from period as periodtbl JOIN society as societytbl ON societytbl.bill_cycle = periodtbl.Billing_cycle where YearID = '" . ($currentYear - 1) . "' and IsYearEnd = 1 ORDER BY periodtbl.ID ASC";
			$sql = "Select  ID from period  where YearID = '" . ($currentYear - 1) . "' and IsYearEnd = 1 ORDER BY  ID ASC";
		
			$result = $this->m_dbConn->select($sql);
		
			$sqlbill = "select bill.PeriodID, bill.PrincipalArrears, bill.InterestArrears, bill.BillSubTotal, bill.BillInterest, prd.YearID from billdetails as bill JOIN period as prd ON bill.PeriodID = prd.ID where BillType = 0 and UnitID = '" . $_REQUEST['unitId'] . "' and PeriodID = '" . $result[0]['ID'] . "'";
			
			$resultbill = $this->m_dbConn->select($sqlbill);
			
			//$var[0]['year'] = $result[0]['YearID'];
			$var[0]['year'] = $currentYear - 1;
			$var[0]['period'] = $result[0]['ID'];
			$var[0]['principle'] = $resultbill[0]['PrincipalArrears'];
			$var[0]['interest'] = $resultbill[0]['InterestArrears'];
			$var[0]['billsubtotal'] = $resultbill[0]['BillSubTotal'];
			$var[0]['billinterest'] = $resultbill[0]['BillInterest'];

			$sqlbill = "select bill.PeriodID, bill.PrincipalArrears, bill.InterestArrears, bill.BillSubTotal, bill.BillInterest, prd.YearID from billdetails as bill JOIN period as prd ON bill.PeriodID = prd.ID where BillType = 1 and UnitID = '" . $_REQUEST['unitId'] . "' and PeriodID = '" . $result[0]['ID'] . "'";
			
			$resultbill = $this->m_dbConn->select($sqlbill);
			
			if($resultbill <> '')
			{
				//$var[0]['year'] = $result[0]['YearID'];
				$var[0]['supp_principle'] = $resultbill[0]['PrincipalArrears'];
				$var[0]['supp_interest'] = $resultbill[0]['InterestArrears'];
				$var[0]['supp_billsubtotal'] = $resultbill[0]['BillSubTotal'];
				$var[0]['supp_billinterest'] = $resultbill[0]['BillInterest'];
			}
			else
			{
				//$var[0]['year'] = $result[0]['YearID'];
				$var[0]['supp_principle'] = 0;
				$var[0]['supp_interest'] = 0;
				$var[0]['supp_billsubtotal'] = 0;
				$var[0]['supp_billinterest'] = 0;
			}
			
			//echo $sql1;
			return $var;
	}
	public function deleting()
	{
		$sql0 = "select count(*)as cnt from member_main where unit='".$_REQUEST['unitId']."' and status='Y'";
		$res0 = $this->m_dbConn->select($sql0);
		
		if($res0[0]['cnt']>0)
		{	
			$sql1="update unit set status='N' where unit_id='".$_REQUEST['unitId']."'";
			$this->m_dbConn->update($sql1);
			
			echo "msg1###".$_SESSION['ssid'].'###'.$_SESSION['wwid'];
		}
		else
		{
			echo "msg";	
		}
	}
	
	public function soc_name($s_id)
	{
		$sql = "select * from society where society_id='".$s_id."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		echo $res[0]['society_name'];
	}
	public function wing_name($ww_id)
	{
		$sql = "select * from wing where wing_id='".$ww_id."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		echo $res[0]['wing'];
	}
	
	public function get_wing_name($ww_id)
	{
		$sql = "select * from wing where wing_id='".$ww_id."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		return $res[0]['wing'];
	}
	
	public function chk_delete_perm_admin()
	{
		$sql = "select * from del_control_admin where status='Y' and login_id='".$_SESSION['login_id']."'";
		$res = $this->m_dbConn->select($sql);
		return $res[0]['del_control_admin'];
	}
	
	public function getallwing()
	{
		
	$sql="select wing,wing_id from `wing` where society_id=".$_SESSION['society_id']." ";
	$res=$this->m_dbConn->select($sql);
	return $res;
	}
	
	public function getLastIID()
	{
		$sql1 = "SELECT MAX(`iid`) AS `max_iid` FROM `member_main` WHERE `society_id` = '".$_SESSION['society_id']."'";
		$sql1_res = $this->m_dbConn->select($sql1);
		
		$last_iid = $sql1_res[0]['max_iid'] + 1;
		
		return $last_iid;
	}
	
	public function getAllUnits()
	{
		$sql="select `unit_id` from `unit` where `society_id` = ".$_SESSION['society_id']." and `status` = 'Y' order by sort_order asc";
		$res=$this->m_dbConn->select($sql);
		$flatten = array();
    	foreach($res as $key)
		{
			$flatten[] = $key['unit_id'];
		}

    	return $flatten;
	}
	
	public function getBillRegisterID($PeriodID, $bCreateIfNotExist = false)
	{
		$iBillRegisterID = 0;
		
		$sqlSelect = "SELECT `ID` from billregister WHERE SocietyID = '" . $_SESSION['society_id'] . "' and PeriodID = '" . $PeriodID . "' ORDER BY ID DESC LIMIT 1";
		$sqlSelectResult = $this->m_dbConn->select($sqlSelect);
		
		if($sqlSelectResult <> '')
		{
			$iBillRegisterID = $sqlSelectResult[0]['ID'];
		}
		else
		{
			if($bCreateIfNotExist == true)
			{
				$aryDate = array();
				$aryDate = $this->obj_utility->getPeriodBeginAndEndDate($PeriodID);
				$sqlInsert = "INSERT INTO `billregister`(`SocietyID`, `PeriodID`, `CreatedBy`, `BillDate`, `DueDate`, `LatestChangeID`, `Notes`) VALUES ('" . $this->m_dbConn->escapeString($_SESSION['society_id']). "', '" . $this->m_dbConn->escapeString( $PeriodID). "', '" . $this->m_dbConn->escapeString($_SESSION['login_id']). "', '" . $this->m_dbConn->escapeString($aryDate['BeginDate']) . "', '" . $this->m_dbConn->escapeString($aryDate['EndDate']) . "', '0', 'Initial Bill')";
				$sqlInsertResult = $this->m_dbConn->insert($sqlInsert);
				$iBillRegisterID = $sqlInsertResult;
			}
		}
		
		return $iBillRegisterID;
	}
	
	
	public function getMemberStatus($unitID)
	{
		$sqlUnit = "select  `unit_no` from `unit` where `unit_id` = '".$unitID."'  "; 
		$resUnit = $this->m_dbConn->select($sqlUnit);
		
		$sql = "select  `owner_name`,`ownership_date`,IF(`ownership_status` = 1,'Active','Ex - Member')  as `ownership_status` from `member_main` where `unit` = '".$unitID."' order by `ownership_date` desc "; 
		$sqlResult = $this->m_dbConn->select($sql);
		if(sizeof($sqlResult) > 0)
		{
				//echo '<div  id="outerDiv" style="border:none;width:95%;">';
				//fetch society details for header
				$societyDetails = $this->obj_fetch->GetSocietyDetails($this->obj_fetch->GetSocietyID($unitID));
				echo '<div id="society_header" style="text-align:center;display:none;">';
				echo '<div id="society_name" style="font-weight:bold; font-size:16px;">'.$this->obj_fetch->objSocietyDetails->sSocietyName.'</div>';
				echo '<div id="society_reg" style="font-size:14px;">';
				if($this->obj_fetch->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$this->obj_fetch->objSocietyDetails->sSocietyRegNo; 
				}
				echo '</div>';
				echo '<div id="society_address"; style="font-size:12px;">'.$this->obj_fetch->objSocietyDetails->sSocietyAddress.'</div></div>';
				
				if(sizeof($resUnit) > 0)
				{
					echo '<br/><center><font style="font-size:18px;"><b>Ownership Details [Unit No:' .$resUnit[0]['unit_no']. ']</b></font></center>';
				
				}
				else
				{
					echo '<center><font style="font-size:18px;"><b>Ownership Details</b></center>';	
				}
				echo '<center><table style="text-align:center; margin: 0 auto; width:95%;"  class="table1"   cellpadding="30">
						<thead>
							<tr>
							<th  style="text-align:center; width:10%;">Sr. No.</th>
							<th  style="text-align:center; width:50%;">Member Name</th>
							<th  style="text-align:center; width:20%;">Ownership Date</th>
							<th  style="text-align:center; width:20%;">Status</th>
							</tr>
						</thead>';
			echo '<tbody>';			
			for($i = 0;$i <sizeof($sqlResult);$i++ )
			{			
				echo '<tr>
				<td  style="text-align:center; width:10%;">'.($i+1).'</td>
				<td style="text-align:left; width:50%;">'.$sqlResult[$i]['owner_name'].'</td>
				<td style="text-align:center; width=20%">'.getDisplayFormatDate($sqlResult[$i]['ownership_date']).'</td>';
				if($sqlResult[$i]['ownership_status'] == "Ex - Member")
				{
					echo '<td  style="text-align:center;color:#FF0000; width=20%" ><b>'.$sqlResult[$i]['ownership_status'].'</b></td>';
				}
				else
				{
					echo '<td  style="text-align:center;color:#00CC00; width=20%" ><b>'.$sqlResult[$i]['ownership_status'].'</b></td>';
				}
				echo '</tr>';
			}
			echo '</tbody></table></center>';
		}
	}
	
	
	
	
	
	public function fetchOwnershipDetails($yearid)
	{
		$finalArray =array();
		
		if($yearid <> "" && $yearid > 0)
		{
			$SqlYear = "select `BeginingDate`,`EndingDate` from `year` where YearID='".$yearid."'";
			$YearStartAndEndDates = $this->m_dbConn->select($SqlYear);
		}
		
		$sql = "select unittbl.`unit_id`,wing.`wing` as Wing, unittbl.`unit_no` as Unit, membertbl.`owner_name` 'Previous Member',membertbl.`owner_name` as Member,membertbl.ownership_date as tempdate,DATE_FORMAT(membertbl.ownership_date, '%d-%m-%Y') 'Ownership Date' FROM unit as unittbl JOIN member_main as membertbl ON unittbl.unit_id = membertbl.unit JOIN wing  ON unittbl.wing_id = wing.wing_id  where unittbl.society_id='".$_SESSION['society_id']."' and membertbl.ownership_date > '2015-04-01' ";
		/*and membertbl.ownership_date <='".$_SESSION['default_year_end_date']."'*/
		
		if($yearid <> "" && $yearid > 0 && sizeof($YearStartAndEndDates) > 0)
		{
			$sql .=" and membertbl.ownership_date between '".$YearStartAndEndDates[0]['BeginingDate']."' and '".$YearStartAndEndDates[0]['EndingDate']."' ";
		}
		
		$sql .= " order by  membertbl.ownership_date  desc";
		
		$data = $this->m_dbConn->select($sql);
		
		for($i = 0; $i < sizeof($data);$i++)
		{	
			$sqlPrev = "select `owner_name` FROM  member_main where ownership_date < '".$data[$i]['tempdate']."'  and unit = '".$data[$i]['unit_id']."'  order by ownership_date desc";
			$dataPrev = $this->m_dbConn->select($sqlPrev);
			
			unset($data[$i]['unit_id']);
			unset($data[$i]['tempdate']);
			$data[$i]['Previous Member'] = "<div style='text-align:left;width: 40%;'>".$dataPrev[0]['owner_name']."</div>";
			$data[$i]['Member'] = "<div style='text-align:left;width: 60%;'>".$data[$i]['Member']."</div>";
			array_push($finalArray,$data[$i]);
		}
		
		return $finalArray;
	}
	
	
	public function displayResults($details,$resBillcycle)
	{
		$flag = false;
		if(sizeof($resBillcycle) > 0 && sizeof($details) > 0)
		{
		  echo "<div style='display:none;' id='societyname'><center><h1><font>"  .$resBillcycle[0]['society_name']. "</font></h1></center></div>";	
		  echo "<div><center><font>Ownership Transfer History</font></center></div>";	
		}
		if(sizeof($details) > 0)
		{
			echo '<br><table style="text-align:center; width:100%;" class="table table-bordered table-hover table-striped" cellpadding="50">';
		}
		foreach($details as $row)
		{
			if(!$flag) 
			{
				echo '<tr style="border:1px solid #ddd;">';
				echo '<td style="border:1px solid #ddd;">' . implode('<td style="border:1px solid #ddd;">', array_keys($row)) . "\n";
				$flag = true;
				echo '</tr>';
				echo '<tr style="border:1px solid #ddd;">';
				echo '<td style="border:1px solid #ddd;">' . implode('<td style="border:1px solid #ddd;">', array_values($row)) . "\n";
				echo '</tr>';
				
			}
			else
			{
				echo '<tr style="border:1px solid #ddd;">';
				echo '<td style="border:1px solid #ddd;">' . implode('<td style="border:1px solid #ddd;">', array_values($row)) . "\n";
				echo '</tr>';
			}
		}
		if(sizeof($details) > 0)
		{
			echo '</table></div>';
		}
	}
	
	
	/*------------------------------------------------Fetch Tenant Details-------------------------------------*/
	public function fetchTenantDetails($TenantList)
	{
		$todayDate= date("Y-m-d");
		//echo $todayDate;
		if($TenantList== 0)
		{
			$sql="select  t.`tenant_id`, w.`wing` as Wing, u.`unit_no` as Unit,CONCAT(t.`tenant_name`,'',t.`tenant_MName`,'',t.`tenant_LName`) as `Lessee Name` ,t.members as 'Lessee Occupying in the Flat', t.`start_date` as 'Start Date' ,t.`end_date` as 'End Date',d.`Document` as 'Lease Document' ,t.`active` as Verified, mm.`member_id`, t.`ApprovalLevel` from `member_main` as mm,`tenant_module` as t JOIN `unit` as u on t.unit_id=u.unit_id join wing as w on u.wing_id= w.wing_id left join documents as d on t.doc_id = d.doc_id and d.`source_table` = '1'  where t.status='Y' and mm.`unit` = u.`unit_id` and mm.`ownership_status` = 1 order by u.sort_order asc, t.end_date desc";
			/*$sql="select  t.`tenant_id`, w.`wing` as Wing, u.`unit_no` as Unit,CONCAT(t.`tenant_name`,'',t.`tenant_MName`,'',t.`tenant_LName`) as `Lessee Name` ,t.members as 'Lessee Occupying in the Flat', t.`start_date` as 'Start Date' ,t.`end_date` as 'End Date',d.`Document` as 'Lease Document' ,t.`active` as Verified, mm.`member_id`, ad.`verifiedById`, ad.`firstApprovalById`, ad.`secondApprovalById`, ad.`verifiedByDesignation`, ad.`firstApprovalByDesignation`, ad.`SecondApprovalByDesignation`,ad.`verifiedStatus`,ad.`firstLevelApprovalStatus`, ad.`secondLevelApprovalStatus` from `approval_details` as ad,`member_main` as mm,`tenant_module` as t JOIN `unit` as u on t.unit_id=u.unit_id join wing as w on u.wing_id= w.wing_id left join documents as d on t.doc_id=d.doc_id  where t.status='Y' and mm.`unit` = u.`unit_id` and ad.`referenceId` = t.`tenant_id` and ad.`module_id` = '".TENANT_SOURCE_TABLE_ID."' order by u.sort_order asc, t.end_date desc";*/

		}
		else if($TenantList==1)
		{
			$sql="select  t.`tenant_id`, w.`wing` as Wing, u.`unit_no` as Unit, CONCAT(t.`tenant_name`,'',t.`tenant_MName`,'',t.`tenant_LName`) as `Lessee Name`,t.members as 'Lessee Occupying in the Flat', t.`start_date` as 'Start Date' ,t.`end_date` as 'End Date',d.`Document` as 'Lease Document' ,t.`active` as Verified, mm.`member_id`, t.`ApprovalLevel` from `member_main` as mm,`tenant_module` as t JOIN `unit` as u on t.unit_id=u.unit_id join wing as w on u.wing_id= w.wing_id left join documents as d on t.doc_id=d.doc_id and d.`source_table` = '1' where t.status='Y' and  t.end_date >= CURDATE() and mm.`unit` = u.`unit_id` and mm.`ownership_status` = 1 order by u.sort_order asc,  t.end_date desc ";
				
		}
		else if($TenantList==2)
		{
			$sql="select  t.`tenant_id`, w.`wing` as Wing, u.`unit_no` as Unit, CONCAT(t.`tenant_name`,' ',t.`tenant_MName`,' ',t.`tenant_LName`) as `Lessee Name` ,t.members as 'Lessee Occupying in the Flat', t.`start_date` as 'Start Date' ,t.`end_date` as 'End Date',d.`Document` as 'Lease Document' ,t.`active` as Verified , mm.`member_id`,  t.`ApprovalLevel` from `member_main` as mm,`tenant_module` as t JOIN `unit` as u on t.unit_id=u.unit_id join wing as w on u.wing_id= w.wing_id left join documents as d on t.doc_id=d.doc_id and d.`source_table` = '1' where t.status='Y' and  t.end_date < CURDATE() and mm.`unit` = u.`unit_id` and mm.`ownership_status` = 1 order by u.sort_order asc,  t.end_date desc ";
		}
		else if($TenantList==3)
		{
			$sql="select t.`tenant_id`, w.`wing` as Wing, u.`unit_no` as Unit, CONCAT(t.`tenant_name`,' ',t.`tenant_MName`,' ',t.`tenant_LName`) as `Lessee Name` ,t.members as 'Lessee Occupying in the Flat', t.`start_date` as 'Start Date' ,t.`end_date` as 'End Date',d.`Document` as 'Lease Document' ,t.`active` as Verified, mm.`member_id`, t.`ApprovalLevel` from `member_main` as mm,`tenant_module` as t JOIN `unit` as u on t.unit_id=u.unit_id join wing as w on u.wing_id= w.wing_id left join documents as d on t.doc_id=d.doc_id and d.`source_table` = '1' where t.status='Y' and t.end_date >= DATE(now()) and t.end_date <= DATE_ADD(DATE(now()), INTERVAL 1 Month) and mm.`unit` = u.`unit_id` and mm.`ownership_status` = 1 order by u.sort_order asc, t.end_date desc";
		}
		else if($TenantList==4)
		{
			$sql="select t.`tenant_id`, w.`wing` as Wing, u.`unit_no` as Unit, CONCAT(t.`tenant_name`,' ',t.`tenant_MName`,' ',t.`tenant_LName`) as `Lessee Name` ,t.members as 'Lessee Occupying in the Flat', t.`start_date` as 'Start Date' ,t.`end_date` as 'End Date',d.`Document` as 'Lease Document' ,t.`active` as Verified2,mm.member_id, mm.`member_id`, t.`ApprovalLevel` from `tenant_module` as t JOIN `unit` as u on t.unit_id=u.unit_id join wing as w on u.wing_id= w.wing_id left join documents as d on t.doc_id=d.doc_id join `member_main` as mm on mm.unit=u.unit_id where t.status='Y' and mm.`ownership_status` = 1 and t.end_date >= '".$todayDate."' order by u.sort_order asc, t.end_date desc";
		}
		else if($TenantList==5)
		{
			$sql="select  t.`tenant_id`, w.`wing` as Wing, u.`unit_no` as Unit, CONCAT(t.`tenant_name`,' ',t.`tenant_MName`,' ',t.`tenant_LName`) as `Lessee Name` ,t.members as 'Lessee Occupying in the Flat', t.`start_date` as 'Start Date' ,t.`end_date` as 'End Date',d.`Document` as 'Lease Document' ,t.`active` as Verified , mm.`member_id`,  t.`ApprovalLevel` from `member_main` as mm,`tenant_module` as t JOIN `unit` as u on t.unit_id=u.unit_id join wing as w on u.wing_id= w.wing_id left join documents as d on t.doc_id=d.doc_id and d.`source_table` = '1' where t.status='Y' and  t.end_date < CURDATE() and mm.unit=u.unit_id and t.`unit_id` NOT IN (select  t.`unit_id` from `member_main` as mm,`tenant_module` as t JOIN `unit` as u on t.unit_id=u.unit_id join wing as w on u.wing_id= w.wing_id left join documents as d on t.doc_id=d.doc_id and d.`source_table` = '1' where t.status='Y' and  t.end_date >= CURDATE() and mm.`unit` = u.`unit_id` and mm.`ownership_status` = 1 order by u.sort_order asc,  t.end_date desc ) and mm.`ownership_status` = 1 order by u.sort_order asc,  t.end_date desc ";
			//$sql="select  t.`tenant_id`, w.`wing` as Wing, u.`unit_no` as Unit, CONCAT(t.`tenant_name`,' ',t.`tenant_MName`,' ',t.`tenant_LName`) as `Lessee Name` ,t.members as 'Lessee Occupying in the Flat', t.`start_date` as 'Start Date' ,t.`end_date` as 'End Date',d.`Document` as 'Lease Document' ,t.`active` as Verified , mm.`member_id`,  t.`ApprovalLevel` from `member_main` as mm,`tenant_module` as t JOIN `unit` as u on t.unit_id=u.unit_id join wing as w on u.wing_id= w.wing_id left join documents as d on t.doc_id=d.doc_id and d.`source_table` = '1' where t.status='Y' and  t.end_date < CURDATE() and mm.`unit` = u.`unit_id` and mm.`ownership_status` = 1  order by u.sort_order asc,  t.end_date desc ";
		}
		//echo $sql;
		$verificationStatus = $this->checkVerificationAccess();
		//var_dump($verificationStatus);
		$approvalStatus = $this->checkApprovalAccess();
		//var_dump($approvalStatus);
		$data = $this->m_dbConn->select($sql);
		
		/*if($_SESSION['society_id'] == 59)
		{
			$this->debug_trace = 1;
		}
		
		if($this->debug_trace)
		{
			echo "<pre>";
			print_r($data);
			echo "</pre>";
		}
		*/
		
		
		for($iTenant = 0; $iTenant < sizeof($data); $iTenant++)
		{
			$data[$iTenant]['Lessee Name'] = "<a href='tenant.php?mem_id=".$data[$iTenant]['member_id']."&tik_id=".time()."&view=".$data[$iTenant]['tenant_id']."'><span>".$data[$iTenant]['Lessee Name']."</span></a>";
			$data[$iTenant]['Active']= ($this->obj_utility->getDateDiff($data[$iTenant]["End Date"], date("Y-m-d")	) >= 0) ? 'Yes' : "No";	
			//$data[$iTenant]['Date Of  Birth']= getDisplayFormatDate($data[$iTenant]['Date Of  Birth']);
			$data[$iTenant]['Start Date']= getDisplayFormatDate($data[$iTenant]['Start Date']);
			$data[$iTenant]['End Date']= getDisplayFormatDate($data[$iTenant]['End Date']);
			$data[$iTenant]['Lease Document']= "<a href='https://way2society.com/Uploaded_Documents/".$data[$iTenant]['Lease Document']. "'download><img src='images/download1.ico'  width='20'></a>";
			if($this->debug_trace)
			{
				echo "<pre>";
				print_r($data[$iTenant]['Verified2']);
				echo "</pre>";
			}
			
			
			if($data[$iTenant]['Verified2'] == "0")//Not Verified
	   		{
		   		if(getDBFormatDate($data[$iTenant]['End Date']) >= date("Y-m-d") && $_REQUEST['TenantList'] == 4 && $verificationStatus)
		 		{
		   			$data[$iTenant]['Verified']="NO <br><a href='tenant.php?mem_id=".$data[$iTenant]['member_id']."&tik_id=".time()."&edit=".$data[$iTenant]['tenant_id']."&action=verify'><span style='color:red;font-weight: bold;'>Click Here To Verify</span></a>";
		   		}
		   		else
		   		{
			 			$data[$iTenant]['Verified']="No"; 
		   		}
			} 
			else //	$data[$iTenant]['Verified'] == "1" //Verified
			{
				$data[$iTenant]['Verified']='Yes';
			}
			$Tenant_Id=$data[$iTenant]['tenant_id'];
			if($Tenant_Id<>'')
			{
				$sqldata="select `tenant_id`,`mem_name` as 'Additional Member',`relation` as Relation,`mem_dob`,`contact_no`,`email`  from `tenant_member` where `tenant_id`='".$Tenant_Id."'";						
				$res1=$this->m_dbConn->select($sqldata);
				
				$data[$iTenant]['Lessee Occupying in the Flat'] = '<table table-bordered table-hover table-striped">';
				for($i=0;$i<sizeof($res1);$i++)
				{
					$res1[$i]['mem_dob']=getDisplayFormatDate($res1[$i]['mem_dob']);		
			 		$data[$iTenant]['Lessee Occupying in the Flat'] .= '<tr align="left"><td>' . $res1[$i]['Additional Member'] . '</td><td>(' . $res1[$i]['Relation'] . ')</td><td>' .  $res1[$i]['mem_dob']. '</td><td>&nbsp;&nbsp;&nbsp;' .  $res1[$i]['contact_no']. '</td></tr>';
				}
				$data[$iTenant]['Lessee Occupying in the Flat'] .= '</table>';
				
				$sqldata1="select * from `documents` where `refID`='".$Tenant_Id."' and `source_table` = '1' and status = 'N'";						
				$res2=$this->m_dbConn->select($sqldata1);
				$data[$iTenant]['Lease Document'] = '<table table-bordered table-hover table-striped">';
				for($j=0;$j<sizeof($res2);$j++)
				{
					$doc_version=$res2[$j]['doc_version'];
					$URL = "";
	                $gdrive_id = $res2[$j]['attachment_gdrive_id'];
	                if($doc_version == "1")
	                {
	                	$URL = "Uploaded_Documents/". $res2[$j]["Document"];
	                }
	                else if($doc_version == "2")
	                {
	                    if($gdrive_id == "" || $gdrive_id == "-")
	                    {
	                         $URL = "Uploaded_Documents/". $res2[$j]["Document"];
	                    }
	                    else
	                    {
	                        $URL = "https://drive.google.com/file/d/". $gdrive_id."/view";
	                    }
	                }
					$data[$iTenant]['Lease Document'] .= '<tr align="left"><td ><a href="'.$URL.'" target="_blank">' . $res2[$j]['Name'] . '</td></tr>';
				}
				$data[$iTenant]['Lease Document'] .= '</table>';
				
			}
			//var_dump($data);
			$ApprovalLevel = $data[$iTenant]['ApprovalLevel'];
			//echo "ApprovalLevel : ".$ApprovalLevel;
			if($ApprovalLevel == "0")
			{
				
				if($data[$iTenant]['Verified2'] == '1')//Verified
				{
					$data[$iTenant]['Approval (1st Level)'] = "Yes";
					$data[$iTenant]['Approval (2nd Level)'] = "Yes";
				}
				else//Not Verified
				{
					$data[$iTenant]['Approval (1st Level)'] = "No";
					$data[$iTenant]['Approval (2nd Level)'] = "No";
				}
			}
			if($ApprovalLevel == "1")
			{//
				//echo "in if";
				$sqlApprovalDetails = "Select * from approval_details where referenceId = '".$Tenant_Id."' and module_id = '".TENANT_SOURCE_TABLE_ID."';";
				$approvalDetails_res = $this->m_dbConn->select($sqlApprovalDetails);	
				//var_dump ($approvalDetails_res);
				if($data[$iTenant]['Verified2'] == "1" )//Verified
				{
					//echo "in if";
					if($approvalDetails_res[0]['firstLevelApprovalStatus'] == "Y")
					{
						$data[$iTenant]['Approval (1st Level)'] = "Yes";
						$data[$iTenant]['Approval (2nd Level)'] = "Yes";
					}
					else//Not Verified
					{
						//echo "in else";
						if($approvalStatus)
						{
							//echo "in if";
							$data[$iTenant]['Approval (1st Level)'] = "No<br><a href='tenant.php?mem_id=".$data[$iTenant]['member_id']."&tik_id=".time()."&edit=".$data[$iTenant]['tenant_id']."&action=approve'><span style='color:red;font-weight: bold;'>Click Here To Approve</span></a>";
							$data[$iTenant]['Approval (2nd Level)'] = "No";
						}
						else
						{
							$data[$iTenant]['Approval (1st Level)'] = "No";
							$data[$iTenant]['Approval (2nd Level)'] = "No";
						}
					}
				}
				else
				{
					$data[$iTenant]['Approval (1st Level)'] = "No";
					$data[$iTenant]['Approval (2nd Level)'] = "No";
				}
			}
			if($ApprovalLevel == "2")
			{
				//echo "in if1";
				$sqlApprovalDetails2 = "Select * from approval_details where referenceId = '".$Tenant_Id."' and module_id = '".TENANT_SOURCE_TABLE_ID."';";
				$approvalDetails_res = $this->m_dbConn->select($sqlApprovalDetails2);
				if($approvalDetails_res[0]['verifiedStatus'] == 'Y')
				{
					//echo "in if2";
					
					//var_dump($approvalDetails_res);
					
					if($approvalDetails_res[0]['firstLevelApprovalStatus'] == "Y")
					{
						$data[$iTenant]['Approval (1st Level)'] = "Yes";
						if($approvalDetails_res[0]['secondLevelApprovalStatus'] == "Y")
						{
							$data[$iTenant]['Approval (2nd Level)'] = "Yes";
						}
						else
						{
							if($approvalStatus)
							{
								$data[$iTenant]['Approval (2nd Level)'] = "No<br><a href='tenant.php?mem_id=".$data[$iTenant]['member_id']."&tik_id=".time()."&edit=".$data[$iTenant]['tenant_id']."&action=approve'><span style='color:red;font-weight: bold;'>Click Here To Approve</span></a>";
							}
							else
							{	
								$data[$iTenant]['Approval (2nd Level)'] = "No";
							}
						}
					}
					else
					{
						if($approvalStatus)
						{
							$data[$iTenant]['Approval (1st Level)'] = "No<br><a href='tenant.php?mem_id=".$data[$iTenant]['member_id']."&tik_id=".time()."&edit=".$data[$iTenant]['tenant_id']."&action=approve'><span style='color:red;font-weight: bold;'>Click Here To Approve</span></a>";
							$data[$iTenant]['Approval (2nd Level)'] = "No";
						}
						else
						{
							$data[$iTenant]['Approval (1st Level)'] = "No<br><a href='tenant.php?mem_id=".$data[$iTenant]['member_id']."&tik_id=".time()."&edit=".$data[$iTenant]['tenant_id']."&action=approve'><span style='color:red;font-weight: bold;'>Click Here To Approve</span></a>";
							$data[$iTenant]['Approval (2nd Level)'] = "No";
						}
					}
				}
				else
				{
					$data[$iTenant]['Approval (1st Level)'] = "No";
					$data[$iTenant]['Approval (2nd Level)'] = "No";
				}
			}
			unset($data[$iTenant]['tenant_id']);
			unset($data[$iTenant]['member_id']);
			unset($data[$iTenant]['ApprovalLevel']);
			unset($data[$iTenant]['Verified2']);
			
			if($_REQUEST['TenantList'] == 4)
			{
				unset($data[$iTenant]['member_id']);
				
			}
		}
		
		return $data;
	}
	public function displayTenantResults($details,$resTenant)
	{
		$flag = false;
		if(sizeof($resTenant) > 0 && sizeof($details) > 0)
		{
		  echo "<div style='display:none; ' id='societyname'><center><h1><font>"  .$resTenant[0]['society_name']. "</font></h1></center></div>";	
		  echo "<div><center><font>Leave & License History</font></center></div>";	
		}
		if(sizeof($details) > 0)
		{
			echo '<br><table style="text-align:left; width:100%;" class="table table-bordered table-hover table-striped" cellpadding="50">';
		}
		foreach($details as $row)
		{
			if(!$flag) 
			{
				echo '<tr style="border:1px solid #ddd;">';
				echo '<td style="border:1px solid #ddd; text-align:center;">' . implode('<td style="border:1px solid #ddd; text-align:center;">', array_keys($row)) . "\n";
				$flag = true;
				echo '</tr>';
				echo '<tr style="border:1px solid #ddd;">';
				echo '<td style="border:1px solid #ddd;">' . implode('<td style="border:1px solid #ddd;">', array_values($row)) . "\n";
				echo '</tr>';
				
			}
			else
			{
				echo '<tr style="border:1px solid #ddd;">';
				echo '<td style="border:1px solid #ddd;">' . implode('<td style="border:1px solid #ddd;">', array_values($row)) . "\n";
				echo '</tr>';
			}
		}
		if(sizeof($details) > 0)
		{
			echo '</table></div>';
		}
	}
	
	
		
	/*------------------------------------------------Fetch Tenant Details for user-------------------------------------*/
	public function fetchTenantDetailsForUser($TenantList,$unitID)
	{
		//$unit=$_REQUEST['unit_id'];
		
		if($TenantList== 0)
		{
			  $sql="select  t.`tenant_id`, w.`wing` as Wing,concat_ws(' ',t.`tenant_name`,t.tenant_MName,t.tenant_LName) as 'Lessee Name' ,t.members as 'Lessee Occupying in the Flat', t.`start_date` as 'Start Date' ,t.`end_date` as 'End Date',d.`Document` as 'Lease Document' ,t.`active` as Verified,mm.`member_id` from `member_main` as mm,`tenant_module` as t JOIN `unit` as u on t.unit_id=u.unit_id join wing as w on u.wing_id= w.wing_id left join documents as d on t.doc_id=d.doc_id where t.status='Y' and t.unit_id='".$unitID."' and mm.`unit` = u.`unit_id` and mm.`ownership_status` = 1  order by u.sort_order asc,  t.end_date desc";

		}
		else if($TenantList==1)
		{
			$sql="select  t.`tenant_id`, w.`wing` as Wing, concat_ws(' ',t.`tenant_name`,t.tenant_MName,t.tenant_LName) as 'Lessee Name' ,t.members as 'Lessee Occupying in the Flat', t.`start_date` as 'Start Date' ,t.`end_date` as 'End Date',d.`Document` as 'Lease Document' ,t.`active` as Verified, mm.`member_id` from `member_main` as mm, `tenant_module` as t JOIN `unit` as u on t.unit_id=u.unit_id join wing as w on u.wing_id= w.wing_id left join documents as d on t.doc_id=d.doc_id where mm.`unit` = u.`unit_id` and t.unit_id='".$unitID."' and t.status='Y' and  t.end_date >= CURDATE() and mm.`ownership_status` = 1  order by u.sort_order asc,  t.end_date desc ";
				
		}
		else{
			$sql="select  t.`tenant_id`, w.`wing` as Wing, concat_ws(' ',t.`tenant_name`,t.tenant_MName,t.tenant_LName) as 'Lessee Name' ,t.members as 'Lessee Occupying in the Flat', t.`start_date` as 'Start Date' ,t.`end_date` as 'End Date',d.`Document` as 'Lease Document' ,t.`active` as Verified, mm.`member_id` from `member_main` as mm,`tenant_module` as t JOIN `unit` as u on t.unit_id=u.unit_id join wing as w on u.wing_id= w.wing_id left join documents as d on t.doc_id=d.doc_id where t.unit_id='".$unitID."' and t.status='Y' and mm.`unit` = u.`unit_id` and  t.end_date < CURDATE() and mm.`ownership_status` = 1 order by u.sort_order asc,  t.end_date desc ";
			
			}
		$data = $this->m_dbConn->select($sql);
		
	for($iTenant = 0; $iTenant < sizeof($data); $iTenant++)
		{
			$data[$iTenant]['Lessee Name'] = "<a href='tenant.php?mem_id=".$data[$iTenant]['member_id']."&tik_id=".time()."&view=".$data[$iTenant]['tenant_id']."'><span>".$data[$iTenant]['Lessee Name']."</span></a>";
			$data[$iTenant]['Active']= ($this->obj_utility->getDateDiff($data[$iTenant]["End Date"], date("Y-m-d")	) >= 0) ? 'Yes' : "No";	
			//$data[$iTenant]['Date Of  Birth']= getDisplayFormatDate($data[$iTenant]['Date Of  Birth']);
			$data[$iTenant]['Start Date']= getDisplayFormatDate($data[$iTenant]['Start Date']);
			$data[$iTenant]['End Date']= getDisplayFormatDate($data[$iTenant]['End Date']);
			$data[$iTenant]['Lease Document']= "<a href='https://way2society.com/Uploaded_Documents/".$data[$iTenant]['Lease Document']. "'download><img src='images/download1.ico'  width='20'></a>";
			
	   
	   if($data[$iTenant]['Verified'] == 0)
	   {
		   $data[$iTenant]['Verified']='No';
		}
		else	
		{
			$data[$iTenant]['Verified']='Yes';
		}
			$Tenant_Id=$data[$iTenant]['tenant_id'];
			if($Tenant_Id<>'')
			{
				$sqldata="select `tenant_id`,`mem_name` as 'Additional Member',`relation` as Relation,`mem_dob`,`contact_no`,`email`  from `tenant_member` where `tenant_id`='".$Tenant_Id."'";						
				$res1=$this->m_dbConn->select($sqldata);
				
				$data[$iTenant]['Lessee Occupying in the Flat'] = '<table table-bordered table-hover table-striped">';
				for($i=0;$i<sizeof($res1);$i++)
				{
					$res1[$i]['mem_dob']=getDisplayFormatDate($res1[$i]['mem_dob']);		
			 		$data[$iTenant]['Lessee Occupying in the Flat'] .= '<tr align="left"><td>' . $res1[$i]['Additional Member'] . '</td><td>(' . $res1[$i]['Relation'] . ')</td><td>' .  $res1[$i]['mem_dob']. '</td><td>&nbsp;&nbsp;&nbsp;' .  $res1[$i]['contact_no']. '</td></tr>';
				}
				$data[$iTenant]['Lessee Occupying in the Flat'] .= '</table>';
				
				$sqldata1="select * from `documents` where `refID`='".$Tenant_Id."' and status = 'N'";						
				$res2=$this->m_dbConn->select($sqldata1);
				
				$data[$iTenant]['Lease Document'] = '<table table-bordered table-hover table-striped">';
				
				for($j=0;$j<sizeof($res2);$j++)
				{
					$doc_version=$res2[$i]['doc_version'];
					$URL = "";
	                $gdrive_id = $res2[$i]['attachment_gdrive_id'];
	                if($doc_version == "1")
	                {
	                	$URL = "Uploaded_Documents/". $res2[$j]["Document"];
	                }
	                else if($doc_version == "2")
	                {
	                    if($gdrive_id == "" || $gdrive_id == "-")
	                    {
	                         $URL = "Uploaded_Documents/". $res2[$j]["Document"];
	                    }
	                    else
	                    {
	                        $URL = "https://drive.google.com/file/d/". $gdrive_id."/view";
	                    }
	                }
					$data[$iTenant]['Lease Document'] .= '<tr align="left"><td ><a href="'.$URL.'" target="_blank">' . $res2[$j]['Name'] . '</td></tr>';
				}
				$data[$iTenant]['Lease Document'] .= '</table>';
			}
			unset($data[$iTenant]['tenant_id']);
		}
		
		return $data;
	}
	public function displayTenantResultsHistory($details,$resTenant)
	{
		$flag = false;
		if(sizeof($resTenant) > 0 && sizeof($details) > 0)
		{
		  echo "<div style='display:none; ' id='societyname'><center><h1><font>"  .$resTenant[0]['society_name']. "</font></h1></center></div>";	
		  echo "<div><center><font>Lessee History</font></center></div>";	
		}
		if(sizeof($details) > 0)
		{
			echo '<br><table style="text-align:left; width:100%;" class="table table-bordered table-hover table-striped" cellpadding="50">';
		}
		foreach($details as $row)
		{
			if(!$flag) 
			{
				echo '<tr style="border:1px solid #ddd;">';
				echo '<td style="border:1px solid #ddd; text-align:center;">' . implode('<td style="border:1px solid #ddd; text-align:center;">', array_keys($row)) . "\n";
				$flag = true;
				echo '</tr>';
				echo '<tr style="border:1px solid #ddd;">';
				echo '<td style="border:1px solid #ddd;">' . implode('<td style="border:1px solid #ddd;">', array_values($row)) . "\n";
				echo '</tr>';
				
			}
			else
			{
				echo '<tr style="border:1px solid #ddd;">';
				echo '<td style="border:1px solid #ddd;">' . implode('<td style="border:1px solid #ddd;">', array_values($row)) . "\n";
				echo '</tr>';
			}
		}
		if(sizeof($details) > 0)
		{
			echo '</table></div>';
		}
	}
	
	public function checkApprovalAccess()
	{
		$sql1 = "Select p.`PROFILE_APPROVALS_LEASE` from mapping as m, profile as p, login as l where l.`login_id` = '".$_SESSION['login_id']."' and p.`id` = m.`profile` and m.`society_id` = '".$_SESSION['society_id']."' and m.`login_id` = l.`login_id` and m.`role` = '".$_SESSION['role']."' and m.`status` = '2'";
		$sql1_res = $this->m_dbConnRoot->select($sql1);
		$result = false;
		if($sql1_res[0]['PROFILE_APPROVALS_LEASE'] == 1)
		{
			$result = true;
		}
		return($result);
	}
	public function checkVerificationAccess()
	{
		$result = false;
		if(($_SESSION['role'] == ROLE_SUPER_ADMIN)||($_SESSION['role'] == ROLE_ADMIN_MEMBER )|| ($_SESSION['role'] == ROLE_ADMIN))
		{
			$result = true;
		}
		return($result);
	}
	//
	public function GetPreviousPDFGen($period,$billType)
	{
		//echo "billType ".$billType;
		$a = $this->obj_fetch->GetSocietyDetails($_SESSION['society_id']);
	     $qry ="SELECT p.Type,p.EndingDate,y.YearDescription FROM `period` as p join year as y on y.YearID=p.YearID where ID='".$period."' ";
		$res = $this->m_dbConn->select($qry);
		$endingdate=substr($res[0]['EndingDate'], 0, 4);
		
	    $url = 'maintenance_bills/'.$this->obj_fetch->objSocietyDetails->sSocietyCode.'/'.$res[0]['Type'].' '.$endingdate.'/';
		$files = scandir($url);
		//print_r($files);
		$maintCnt=0;
		$SuppCnt=0;
		
		$FinalBillCount =0;
		$mantFlage=false;
		$suppFlage= false;
		for($i=0;$i<sizeof($files);$i++)
		{
			if($files[$i] <> '.' && $files[$i] <> '..' && $files[$i] <> '')
			{
				$matchTye = substr($files[$i], -5);
				$matchTye1 = substr($matchTye, 0, 1);
				$matchTye2 = substr($files[$i], 0, 4);
				//echo "matchTye1 =>" .$matchTye1. "billType =>  ".$billType."<br>";
				if($matchTye1 == $billType && $billType == 0 && $matchTye2 == 'bill')
				{
					
					$maintCnt++;
					
				}
				else if($matchTye1 == $billType &&  $billType == 1 && $matchTye2 == 'bill')
				{
					$SuppCnt++;
				
				}
			}
		}
		
		$qry1 ="select count(u.unit_id) as unitcnt from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.status = 'Y' and u.society_id = '" . $_SESSION['society_id'] . "' and mm.ownership_status=1 ORDER BY u.sort_order";
		$res1 = $this->m_dbConn->select($qry1);
		
		$res1['unitcnt'] = $res1[0]['unitcnt'];
		if($billType == 0)
		{
			$res1['pdfcnt'] = $maintCnt;
		}
		else
		{
			$res1['pdfcnt'] = $SuppCnt;
		}
		
		//echo "Main:".$maintCnt;
		//echo "Supp:".$SuppCnt;
		//echo "FInal:".$FinalBillCount;
		//print_r($res1);
		return $res1;
	}
}
?>
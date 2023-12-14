<?php if(!isset($_SESSION)){ session_start(); }

set_time_limit(0);
ignore_user_abort(1);
ini_set('default_socket_timeout', -1);
ini_set('max_execution_time', -1);
ini_set('memory_limit', -1);
ini_set('mysql.connect_timeout', -1);

include_once("include/display_table.class.php");
include_once("dbconst.class.php");
include_once ("include/fetch_data.php");
include_once("changelog.class.php");
include_once("latestcount.class.php");
include_once("voucher.class.php");
include_once("register.class.php");
include_once("utility.class.php");
include_once("billmaster.class.php");

class CUpdatedBillDetails
{
	public $sBillNo;
	public $sBillDate;
	public $sDueDate;
	public $sPeriodID;
	public $sSocietyID;
	public $sWingID;
	public $sUnitID;
	public $arJournals;
	public $sAdjustmentCredit;	
	public $arrData;
	public $m_dbConn;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->sBillNo = "";
		$this->sBillDate = "";
		$this->sDueDate = "";
		$this->sPeriodID = "";
		$this->sSocietyID = "";
		$this->sWingID = "";
		$this->sUnitID = "";
		$this->sAdjustmentCredit = "";		
		$this->arJournals = "";
		$this->arrData = array();
	}
}

class genbill
{
	public $actionPage="../genbill.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	
	public 	$oUpdateBillData;
	private $obj_LatestCount;
	private $errofile_name;
	private $m_bill_file;
	public $objFetchData;
	public $strTrace;
	public $m_objUtility;
    public $delTrace;
	public $m_objLog;
   	private $obj_billmaster;			
	public $obj_voucher;
	public $obj_register;
	public $ShowDebugTrace;
	
    function __construct($dbConn, $dbConnRoot = "")
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->display_pg=new display_table($this->m_dbConn);
		
		$oUpdateBillData = new CUpdatedBillDetails($this->m_dbConn);
		$actionPage="../Maintenance_bill_edit.php";
		
		$this->obj_LatestCount = new latestCount($this->m_dbConn);
		$this->objFetchData = new FetchData($this->m_dbConn);
		$this->m_objUtility =  new utility($this->m_dbConn, $this->m_dbConnRoot);
        $this->m_objLog = new changeLog($this->m_dbConn);
		//$obj_utility = new utility($this->m_dbConn, $this->m_dbConnRoot);
				
		$this->obj_voucher = new voucher($this->m_dbConn);
		$this->obj_register = new regiser($this->m_dbConn);
		$this->obj_billmaster = new billmaster($this->m_dbConn);
		$this->ShowDebugTrace = 0;
	}
	
	//Edit bill (Called from maintenancebill_edit_process.php)
	public function startProcess()
	{
		$errorExists=0;
		$actionPage="../Maintenance_bill_edit.php";
		//echo '<script type="text/javascript">alert("'.$_REQUEST['Bill'].'");<//script>';
		
		if($_REQUEST['insert']=='Insert' && $errorExists==0)
		{
			if($_POST['society_id']<>"" && $_POST['wing_id']<>"" && $_POST['unit_no']<>"")
			{
				$sql = "select count(*)as cnt from unit where society_id='".$_SESSION['society_id']."' and wing_id='".$_POST['wing_id']."' and unit_no='".addslashes(trim($_POST['unit_no']))."' and status='Y'";
				$res = $this->m_dbConn->select($sql);
				
				if($res[0]['cnt']==0)
				{
					foreach(explode(',',$_POST['unit_no']) as $k)
					{
						if($k<>"")
						{
							$sql0 = "select count(*)as cnt from unit where society_id='".$_SESSION['society_id']."' and wing_id='".$_POST['wing_id']."' and unit_no='".addslashes(trim($k))."'";
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
							
							$sql1 = "insert into unit(`society_id`,`wing_id`,`unit_no`,`rand_no`)values
									('".$_SESSION['society_id']."','".$_POST['wing_id']."','".addslashes(trim($k))."','".$rand_no."')";
							$res1 = $this->m_dbConn->insert($sql1);
							}
						}
					}
					
					return "Insert";
				}
				else
				{
					return "Already exist";
				}
			}
			else
			{
				return "Some * field is missing";
			}
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			if($_POST['society_id']<>"" && $_POST['wing_id']<>"" && $_POST['unit_no']<>"")
			{
				$up_query="update unit set `society_id`='".$_POST['society_id']."',`wing_id`='".$_POST['wing_id']."',`unit_no`='".$_POST['unit_no']."' where unit_id='".$_POST['id']."'";
				$data=$this->m_dbConn->update($up_query);
				return "Update";
			}
			else
			{
				return "Some * field is missing";
			}
		}
		else if($_REQUEST['mode']=='Generate' && $errorExists==0)
		{
			$_SESSION['default_year'] = $_REQUEST['year_id'];
			$_SESSION['default_period'] = $_REQUEST['period_id'];
			$this->SetDefaultYearPeriod($_REQUEST['year_id'], $_REQUEST['period_id']);
			
			return $this->generateBill();
		}
		else if($_REQUEST['mode']== 'View' && $errorExists==0)
		{
			$_SESSION['default_year'] = $_REQUEST['year_id'];
			$_SESSION['default_period'] = $_REQUEST['period_id'];
			$this->SetDefaultYearPeriod($_REQUEST['year_id'], $_REQUEST['period_id']);
			
			return "";
		}
		else if($_REQUEST['Bill']== 'Update' && $errorExists==0)
		{
			return $this->UpdateBill();
		}
		else if($_REQUEST['mode']== 'Export To Excel' && $errorExists==0)
		{			
			return "Export";			
		}
		else
		{
			echo "<script>alert('error');</script>";
			return $errString;
		}
	}
	public function IsSupplementaryBill()
	{
		//echo "gen_supplementary_bill:".$_REQUEST["gen_supplementary_bill"];
		
		/*if(!isset($_REQUEST["gen_supplementary_bill"]))
		{
			$_REQUEST["gen_supplementary_bill"] = 0;
		}*/
		
		if(!isset($_REQUEST['bill_method']))
		{
			$_REQUEST['bill_method'] = 0;	
		}
		
		//return $_REQUEST["gen_supplementary_bill"];
		return $_REQUEST['bill_method'];
	}
	
	public function SetDefaultYearPeriod($year, $period)
	{
		$sqlUpdate = "UPDATE `appdefault` SET `APP_DEFAULT_YEAR`='" . $year . "',`APP_DEFAULT_PERIOD`='" . $period . "' WHERE `APP_DEFAULT_SOCIETY` = '" . $_SESSION['society_id'] . "'";
		$sqlResult = $this->m_dbConn->update($sqlUpdate);
	}
	
	public function combobox($query,$id, $defaultText = 'Please Select', $defaultValue = '')
	{
		$str = '';
		
		if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "' selected = 'selected'>" . $defaultText . "</option>";
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
	
	public function comboboxForLedgerReport($query,$id, $defaultText = 'Please Select', $defaultValue = '')
	{
		if($defaultText != '')
		{
			?>
        <ul>
			<li>&nbsp;<input type="checkbox"      id = '0' class="checkBox chekAll" checked/>&nbsp;<?php echo $defaultText ; ?></li>
			<?php 
		}
		
		$data = $this->m_dbConn->select($query);
		
		for($i = 0; $i < sizeof($data); $i++)
		{?>
        	<li>&nbsp;<input type="checkbox"  id="<?php echo $data[$i]['unit_id'];?>" class="checkBox" onChange="uncheckDefaultCheckBox(this.id);"/>&nbsp; <?php echo $data[$i]['name'];?></li>
        <?php		
		}
		?>
		</ul>
        <?php 
	}
	
	public function generateBill()
	{
		$info = '';
		
		try
		{
			$this->m_dbConn->begin_transaction();
			{
				if($_REQUEST['period_id'] <> '' && $_REQUEST['period_id'] > 0 && $_REQUEST['bill_date'] <> '' && $_REQUEST['due_date'] <> '')
				{
					$sqlGen = '';
					$sqlGen_BillCalc = '';
					
					if($_REQUEST['society_id'] == '0')
					{
						//generate for all units in all societies
//						$sqlGen = "select master.UnitID, master.AccountHeadID, master.AccountHeadAmount,master.BillType from unitbillmaster as master where master.BillType='".$this->IsSupplementaryBill() ."'";
						$sqlGen_BillCalc = "select distinct(master.UnitID), unitinfo.society_id as SocietyID, master.BillType from unitbillmaster as master JOIN unit as unitinfo ON master.UnitID = unitinfo.unit_id and  master.BillType='".$this->IsSupplementaryBill(); "' ORDER BY unitinfo.sort_order ASC";
					}
					else if($_REQUEST['wing_id'] == '0' && $_REQUEST['unit_id'] == '0')
					{
						//generate for all units in single society
//						$sqlGen = "select master.UnitID, master.AccountHeadID, master.AccountHeadAmount,master.BillType from unitbillmaster as master JOIN unit as unitinfo on master.UnitID = unitinfo.unit_id where unitinfo.society_id = '" . $_REQUEST['society_id'] .  "' where master.BillType='".$this->IsSupplementaryBill()."'";
						$sqlGen_BillCalc = "select distinct(master.UnitID), unitinfo.society_id as SocietyID,master.BillType from unitbillmaster as master JOIN unit as unitinfo on master.UnitID = unitinfo.unit_id where unitinfo.society_id = '" . $_REQUEST['society_id'] .  "' and  master.BillType='".$this->IsSupplementaryBill()."' ORDER BY unitinfo.sort_order ASC";
					}
					else if($_REQUEST['unit_id'] == '0')
					{
						//generate for all units in single wing
//						$sqlGen = "select master.UnitID, master.AccountHeadID, master.AccountHeadAmount,master.BillType from unitbillmaster as master JOIN unit as unitinfo on master.UnitID = unitinfo.unit_id where unitinfo.society_id = '" . $_REQUEST['society_id'] .  "' and unitinfo.wing_id = '" . $_REQUEST['wing_id'] . "' and master.BillType=".$this->IsSupplementaryBill();
						$sqlGen_BillCalc = "select distinct(master.UnitID), unitinfo.society_id as SocietyID, master.BillType from unitbillmaster as master JOIN unit as unitinfo on master.UnitID = unitinfo.unit_id where unitinfo.society_id = '" . $_REQUEST['society_id'] .  "' and unitinfo.wing_id = '" . $_REQUEST['wing_id'] . "' and master.BillType='".$this->IsSupplementaryBill() ."' ORDER BY unitinfo.sort_order ASC";
					}
					else
					{
						//generate for single unit
						$sqlGen = "select master.UnitID, master.AccountHeadID, master.AccountHeadAmount from unitbillmaster as master where master.UnitID = '" . $_REQUEST['unit_id'] . "' and  master.BillType='".$this->IsSupplementaryBill() ."'";
						$sqlGen_BillCalc = "select distinct(master.UnitID), unitinfo.society_id as SocietyID from unitbillmaster as master JOIN unit as unitinfo ON master.UnitID = unitinfo.unit_id where master.UnitID = '" . $_REQUEST['unit_id'] . "' and master.BillType='".$this->IsSupplementaryBill() ."' ORDER BY unitinfo.sort_order ASC";
					}
				
					//echo '<br/>SqlGen : ' . $sqlGen_BillCalc;
					
					//$result = $this->m_dbConn->select($sqlGen);
					$result = $this->m_dbConn->select($sqlGen_BillCalc);
					$UnitIDCol = array();
					$aryGenBillInsertID = array();
					
					if($result <> "")
					{
						$this->obj_LatestCount->updateLatestBillNo($_SESSION['society_id'], $_REQUEST['bill_no']);
										
						foreach($result as $k => $v)
						{
							//Delete Bill Details if already generated
							$this->DeleteBillDetails($result[$k]['UnitID'], $_REQUEST['period_id'],false,false,$this->IsSupplementaryBill());
						}
						
						$changeLog = new changeLog($this->m_dbConn);
						// $desc = $this->IsSupplementaryBill();
						if($this->IsSupplementaryBill())
						{
							$desc = 'Generated Supplementary Bill for Unit <' . $result[$k]['UnitID'] . '> Period <' . $_REQUEST['period_id'] . '>';
						}
						else
						{
							$desc = 'Generated '.$this->IsSupplementaryBill().' Bill for Unit <' . $result[$k]['UnitID'] . '> Period <' . $_REQUEST['period_id'] . '>';
						}
						if($_REQUEST['unit_id'] == '0')
						{
							if($this->IsSupplementaryBill())
							{
							$desc = 'Generated Supplementary Bill for Unit <All> Period <' . $_REQUEST['period_id'] . '>';
							}
							else
							{
								$desc = 'Generated '.$this->IsSupplementaryBill().' Bill for Unit <All> Period <' . $_REQUEST['period_id'] . '>';
							}
						}
						
						$iLatestChangeID = $changeLog->setLog($desc, $_SESSION['login_id'], 'billregister', '--');
						
						
						$FontSize =$_POST['font_size'];
						
						//echo "Font Size:".$FontSize;
						
						$Notes = ($_REQUEST['bill_notes']);	
						$sBillDate = getDBFormatDate($this->m_dbConn->escapeString($_REQUEST['bill_date']));
						//echo '<br/>Bill date after getDBFormatDate <' . $sBillDate . ">"; 
						$this->strTrace .= '<tr><td>Bill date after getDBFormatDate <' . $sBillDate . "></td></tr>"; 

//						$sqlSelect = "SELECT * from `billregister` where `SocietyID` = '" . $this->m_dbConn->escapeString($result[$k]['SocietyID']). "' and `PeriodID` = '" . $this->m_dbConn->escapeString( $_REQUEST['period_id']). "' and `BillDate` = '" . $sBillDate . "' and `DueDate` = '" . getDBFormatDate($this->m_dbConn->escapeString($_REQUEST['due_date'])) . "' and `BillType` = '".$this->IsSupplementaryBill()."' and `font_size` = '".$FontSize."'";
//							echo "<BR>$sqlSelect<BR>";
					
					"<BR>" . $sqlSelect = "SELECT * from `billregister` where `SocietyID` = '" . $this->m_dbConn->escapeString($result[$k]['SocietyID']). "' and `PeriodID` = '" . $this->m_dbConn->escapeString( $_REQUEST['period_id']). "' and `BillDate` = '" . $sBillDate . "' and `DueDate` = '" . getDBFormatDate($this->m_dbConn->escapeString($_REQUEST['due_date'])) . "' and  `Notes` = '" . $Notes . "' and `BillType` = '".$this->IsSupplementaryBill()."' and `font_size` = '".$FontSize."'";
					$ExistingBillRegister = $this->m_dbConn->select($sqlSelect);
					if($ExistingBillRegister <> '')
					{
						
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>$sqlSelect<BR>";
							echo "<BR>Bill Register exists : ". $ExistingBillRegister[0][ID] . "<BR> ";
						}
						$BillRegisterID = $ExistingBillRegister[0][ID];
						//print_r($ExistingBillRegister);
					}
						
					if($BillRegisterID <= 0)
					{
						$sqlInsert = "INSERT INTO `billregister`(`SocietyID`, `PeriodID`, `CreatedBy`, `BillDate`, `DueDate`, `LatestChangeID`, `Notes`,`BillType`,`font_size`) VALUES ('" . $this->m_dbConn->escapeString($result[$k]['SocietyID']). "', '" . $this->m_dbConn->escapeString( $_REQUEST['period_id']). "', '" . $this->m_dbConn->escapeString($_SESSION['login_id']). "', '" . $sBillDate . "', '" . getDBFormatDate($this->m_dbConn->escapeString($_REQUEST['due_date'])) . "', '" . $this->m_dbConn->escapeString($iLatestChangeID). "', '" . $Notes . "','".$this->IsSupplementaryBill()."','".$FontSize."')";
							
						$BillRegisterID = $this->m_dbConn->insert($sqlInsert);
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>Creating new BillRegisterID:". $BillRegisterID . "<BR>";
						}
						//echo "<BR><BR>In if part " . $sqlInsert ;
					}

						//echo '<br/>BillRegisterID <' . $BillRegisterID . ">"; 
						$this->strTrace .= '<tr><td>BillRegisterID <' . $BillRegisterID . "></td></tr>"; 																												
						//die;
							
						//$aryGenBillInsertID[$result[$k]['UnitID']] = $resultInsert;
							
						$PeriodID = $_REQUEST['period_id'];
						$PrevPeriodID = $this->getPreviousPeriodData($PeriodID);
						//var_dump($PrevPeriodID);
						if($PrevPeriodID < 1)
						{
							$desc = 'No prev period for period <' . $PeriodID . "> : assuming this is first period.";
							//return;
						}
						else
						{
							$desc = 'Prev period id <'.$PrevPeriodID.'> for period <' . $PeriodID . "> : assuming this is first period.";
						}
		
						$PrevPeriodBeginingDate = 0;
						$PrevPeriodEndingDate = 0;
					    //echo "check";
						/*if(!$this->IsSupplementaryBill())
						{*/
							//echo 'Getting data for PrevPeriodID : ' . $PrevPeriodID;
							//$sqlPrevQuery = "Select Type, YearID, PrevPeriodID, Status, BeginingDate, EndingDate from period where ID=" . $PeriodID;
							$sqlPymtQuery = "Select Type, YearID, PrevPeriodID, Status, BeginingDate, EndingDate from period where ID=" . $PrevPeriodID;
					//		echo '<br/>sqlPymtQuery : ' . $sqlPymtQuery;
							$Prevresult = $this->m_dbConn->select($sqlPymtQuery);
							//$PrevPeriodID = -1;
							if(!is_null($Prevresult))
							{
								$Type = $Prevresult[0]['Type'];
								$YearID = $Prevresult[0]['YearID'];
								//ToDo : Comment this out Bug
								//$PrevPeriodID = $Prevresult[0]['PrevPeriodID'];
								$PrevPeriodBeginingDate = $Prevresult[0]['BeginingDate'];
								$PrevPeriodEndingDate = $Prevresult[0]['EndingDate'];
											
								//echo '<br/>PrevPeriodID : ' . $PrevPeriodID;
								//echo '<br/>Prev Period : ' . $Type;
								//echo '<br/>Prev YearID : ' . $YearID;
								//echo '<br/>PrevPeriodBeginingDate : ' . $PrevPeriodBeginingDate;
								//echo '<br/>PrevPeriodEndingDate : ' . $PrevPeriodEndingDate;
								
							}
						/*}*/
		
		//-----		
		
				//echo "Get interest calc parameters of Society";
				$SocietyID = $_SESSION['society_id'];
				$sqlSocietyQuery = "Select int_rate, int_method, int_tri_amt, rebate_method, rebate from society where society_id =" . $SocietyID;
		//		echo '<br/>sqlSocietyQuery : ' . $sqlSocietyQuery;
				$SQLResult = $this->m_dbConn->select($sqlSocietyQuery);
						
		//Todo : Remove hard coding
				$BillCalcType = 1;	//LIFO
				$InterestRate = 18;		//default to 18%
				$InterestMethod = 2;	//default to monthly			
				$InterestTrigger = 0;			
				$RebateMethod = 0;			
				$RebateAmount = 0;			
		
				if(!is_null($SQLResult))
				{
					
					//$BillCalcType = $SQLResult[0]['int_calc_type'];
					$InterestRate = $SQLResult[0]['int_rate'];			
					$InterestMethod = $SQLResult[0]['int_method'];			
					$InterestTrigger = $SQLResult[0]['int_tri_amt'];			
					$RebateMethod = $SQLResult[0]['rebate_method'];			
					$RebateAmount = $SQLResult[0]['rebate'];			
				}
				if (!(($RebateMethod == 2) || ($RebateMethod == 1)))
				{
					$strMsg = 'Unsupported RebateMethod ' . $RebateMethod . '. <BR>Only RebateMethod None(1) and Flat(2) is supported. Rebate would not be processing';
					echo "<script>alert('".$strMsg."');</script>";		
				}
				//echo '<br/>$InterestTrigger : ' . $InterestTrigger . ' not implemented';
				$this->strTrace .=  '<tr><td>$InterestTrigger : ' . $InterestTrigger . ' not implemented</td></tr>';
		
		/*		echo '<br/>InterestRate : ' . $InterestRate;
				echo '<br/>InterestMethod : ' . $InterestMethod;
				echo '<br/>InterestTrigger : ' . $InterestTrigger;
				echo '<br/>RebateMethod : ' . $RebateMethod;
				echo '<br/>RebateAmount : ' . $RebateAmount;*/
		//		$BillSubTotal = -1;
		//=============='
						//echo $sqlGen_BillCalc;
						//echo "<BR>sqlgen_BillCalc<BR>" . $sqlGen_BillCalc . "<BR>";		
						$result_BillCalc = $this->m_dbConn->select($sqlGen_BillCalc);
				
						if($result_BillCalc <> "")
						{
							$this->objFetchData->GetSocietyDetails($SocietyID);
							$SocietyDetails = $this->objFetchData->objSocietyDetails;
							
							//var_dump($this->objFetchData->objSocietyDetails->sSocietyCode);
							$bill_dir = '../m_bills_log/' . $this->objFetchData->objSocietyDetails->sSocietyCode;
							//echo $bill_dir;
							if (!file_exists($bill_dir)) 
							{
								mkdir($bill_dir, 0777, true);
							}
							//echo $SocietyDetails->sSocietyName;
							foreach($result_BillCalc as $k => $v)
							{
								if($result_BillCalc[$k]['UnitID'] <> 0)
								{
									$UnitID = $result_BillCalc[$k]['UnitID'];
									$iBillCounter = $this->obj_LatestCount->getLatestBillNo($_SESSION['society_id']);
									$BillFor = $this->objFetchData->GetBillFor($PeriodID);
									//var_dump($BillFor);
									$this->objFetchData->GetMemberDetails($UnitID);
									$CurUnitID = $this->objFetchData->objMemeberDetails->sUnitNumber;
									
									$this->errofile_name = $bill_dir.'/M_Bill_'.$_SESSION['society_id'].'_'. $CurUnitID .'_'.$BillFor.'_'.$iBillCounter .'.html';
									$this->errofile_name = str_replace(' ', '-',$this->errofile_name);

									$this->m_bill_file = fopen($this->errofile_name,"a");
									$this->strTrace = "<html><head><title>m-Bill log</title></head><body><table>";
									$this->strTrace .= "<tr><td>-----------------------------------------------------------------------</td></tr>";
									if($this->IsSupplementaryBill())
									{ 
										$this->strTrace .= "<tr><td>New Supplementary Bill Generated for SocietyID <".$_SESSION['society_id']."> : <b>".$this->objFetchData->objSocietyDetails->sSocietyName." </b></td></tr>";
									}
									else
									{
										$this->strTrace .= "<tr><td>New Bill Generated for SocietyID <".$_SESSION['society_id']."> : <b>".$this->objFetchData->objSocietyDetails->sSocietyName." </b></td></tr>";
									}
									$this->strTrace .= "<tr><td>UnitID <".$UnitID."> : <b>".$CurUnitID."</b></td></tr>";
									$this->strTrace .= "<tr><td>PeriodID <".$PeriodID.">  : <b>".$BillFor."</b></td></tr>";
									$this->strTrace .= "<tr><td>Bill Number <".$iBillCounter."></b></td></tr>";
									//echo "generating bill per unit";
									
									//$iCheckForUnitTarrifAmountForCurrentPeriod = $this->GetBillSubTotal($UnitID,$PeriodID);
									

									//if($iCheckForUnitTarrifAmountForCurrentPeriod > 0) 
									//{
											$BillDetailID = $this->generateBill_PerUnit($UnitID, $PeriodID, $PrevPeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate, $BillRegisterID, $iBillCounter, $BillCalcType,$InterestRate,$InterestMethod,$InterestTrigger, $RebateMethod, $RebateAmount, $sBillDate);
	
											$this->strTrace .= "<tr><td>-----------------------------------------------------------------------</td></tr>";
									//}
									//else
									//{
											//$this->strTrace .= "<tr><td>Bill Not Generated for SocietyID <".$_SESSION['society_id']."> : :UnitID <".$UnitID.">  : :  PeriodID <".$PeriodID.">  : :  Bill Type <".$this->IsSupplementaryBill()."> Tarrif not available. </b></td></tr>";
									//}
									
									$this->strTrace .= "</table></body></html>";
									//echo "file name " . $this->m_bill_file;
									fwrite($this->m_bill_file,$this->strTrace);
									fclose($this->m_bill_file);
									
								}
							}
						}
						$info = 'Bills generated successully.';
						echo $info;
						if($_POST["unit_id"] == 0)
						{
							$updateQuery = "UPDATE `society` SET `M_PeriodID` =" .$PeriodID. " WHERE society_id = ".$_SESSION['society_id'] ;
							//echo "<br>UPDATE ".$updateQuery;
							$this->m_dbConn->update($updateQuery);
						}
						//Insert or Uodate Reminder date in `remindersms` table						
						/*$countQuery = "SELECT count(ID) AS `cnt` FROM `remindersms` WHERE `society_id` = '" . $this->m_dbConn->escapeString($result[$k]['SocietyID']). "' AND `PeriodId` = '" . $this->m_dbConn->escapeString( $_REQUEST['period_id']). "'";
						
						$count = $this->m_dbConnRoot->select($countQuery);
						
						$reminderDate = $this->GetDateByOffset($_REQUEST['due_date'], -5);
						if($count[0]['cnt'] == 0)
						{																					
							$sqlReminderQuery = "INSERT INTO `remindersms`(`society_id`, `PeriodID`, `ReminderType`, `EventDate`, `EventReminderDate`, `LoginID`) VALUES ('" . $this->m_dbConn->escapeString($result[$k]['SocietyID']). "',
										 '" . $this->m_dbConn->escapeString( $_REQUEST['period_id']). "','".SENDBILLREMINDER."','" . getDBFormatDate($this->m_dbConn->escapeString($_REQUEST['due_date'])) . "','" . getDBFormatDate($this->m_dbConn->escapeString($reminderDate)) . "',
										 '".$_SESSION['login_id']."')";												
							$this->m_dbConnRoot->insert($sqlReminderQuery);
						}
						else
						{
							$updateTable = "UPDATE `remindersms` SET `EventDate`='" . getDBFormatDate($this->m_dbConn->escapeString($_REQUEST['due_date'])) . "',
								`EventReminderDate`='" . getDBFormatDate($this->m_dbConn->escapeString($reminderDate)) . "', `LoginID` = '".$_SESSION['login_id']."', `ReminderType` = '".SENDBILLREMINDER."'  WHERE `society_id`='" . $this->m_dbConn->escapeString($result[$k]['SocietyID']). "' AND
								`PeriodID`='" . $this->m_dbConn->escapeString( $_REQUEST['period_id']). "'";								
							$this->m_dbConnRoot->update($updateTable);
						}*/
						
						//$sqlReminderQuery = "INSERT INTO `remindersms`(`society_id`, `PeriodID`, `ReminderType`, `EventDate`, `EventReminderDate`, `LoginID`) VALUES ('" . $this->m_dbConn->escapeString($result[$k]['SocietyID']). "', '" . $this->m_dbConn->escapeString( $_REQUEST['period_id']). "','".SENDBILLREMINDER."','" . getDBFormatDate($this->m_dbConn->escapeString($_REQUEST['due_date'])) . "','" . getDBFormatDate($this->m_dbConn->escapeString($reminderDate)) . "', '".$_SESSION['login_id']."')";
						//$this->m_dbConnRoot->insert($sqlReminderQuery);
					}
					else
					{
						$info = "No Units found in Bill Master to Generate Bills for selected criteria.";
					}
				}
				else
				{
					$info = 'Please enter valid data for all the fields.';
				}
				//if($this->ShowDebugTrace == 1)
				{
					echo "doing commit";
				}
				$this->m_dbConn->commit();
				return $info;
			}
		}
		catch(Exception $exp)
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Exception occured. Doing rollback.";
				print_r($exp);
			}
			$this->m_dbConn->rollback();
			return $exp;
		}
	}

	//If Bill has been already generated for a Unit/Period, delete it first and then regenerate.
	public function DeleteBillDetails($UnitID, $PeriodID,$bIsManualDelete = false,$bIsDeleteAll = true,$IsSupplementaryBill = false)
	{
//		$this->ShowDebugTrace = 1;
		//Check if data exist in billdetails
		$sqlCheck = "Select ID,BillRegisterID from billdetails where UnitID ='" . $UnitID . "' and PeriodID ='" . $PeriodID . "'  and BillType='".$IsSupplementaryBill."'";
		$sqlResult = $this->m_dbConn->select($sqlCheck);
		if($sqlResult <> '')
		{
			$RefNo = $sqlResult[0]['ID'];
			$BillRegisterID = $sqlResult[0]['BillRegisterID'];

			$sqlCount = "Select Count(*) as cnt from billregister where ID = '" . $BillRegisterID . "'";
			$resCount = $this->m_dbConn->select($sqlCount);

			//Delete from billregister
			if(($bIsManualDelete == false && $bIsDeleteAll == true) || ($bIsManualDelete == true && $bIsDeleteAll == true) || ($bIsManualDelete == true && $bIsDeleteAll == false && $resCount[0]['cnt'] > 0))
			{
			   $sqlDelete = "Delete from billregister where ID = '" . $BillRegisterID . "'and BillType='".$IsSupplementaryBill."'";
				$sqlDelete = $this->m_dbConn->delete($sqlDelete);
			}
                        
             //Delete from billdetails
			$sqlDelete = "Delete from billdetails where UnitID ='" . $UnitID . "' and PeriodID ='" . $PeriodID . "' and BillType='".$IsSupplementaryBill."'";
			$sqlDelete = $this->m_dbConn->delete($sqlDelete);
		
			//Delete from VoucherSales
			$sqlSelect = "Select id, SrNo, VoucherNo from voucher where RefNo = '" . $RefNo . "' and RefTableID = '" . TABLE_BILLREGISTER . "'";
			$sqlResultVoucher = $this->m_dbConn->select($sqlSelect);
			if($sqlResultVoucher <> '')
			{
				if($this->ShowDebugTrace == 1)
				{
					echo "Found Voucher No :" . $sqlResultVoucher[0]['VoucherNo'];
					print_r($sqlResultVoucher);
				}
				foreach($sqlResultVoucher as $key=>$value)
				{
					if($value['SrNo'] == '1')
					{
						//Delete from AssetRegister
						$sqlDelete = "Delete from assetregister where VoucherID = '" . $value['id'] . "'";
						$sqlResultDelete = $this->m_dbConn->delete($sqlDelete);
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>Asset register deleted  for VoucherID = '" . $value['id'] . " result: " . $sqlResultDelete . "<BR>";
						}
					}
					if($sqlResultDelete ==0)
					{
						//Delete from IncomeRegister
						$sqlDelete = "Delete from incomeregister where VoucherID = '" . $value['id'] . "'";
						$sqlResultDelete = $this->m_dbConn->delete($sqlDelete);
						if($this->ShowDebugTrace == 1)
						{
							echo "Income register deleted for VoucherID = " . $value['id'] . " result: " . $sqlResultDelete . "<BR>";
						}
					}
					if($sqlResultDelete == 0)
					{
						//Delete from liabilityRegister
						$sqlDelete = "Delete from liabilityregister where VoucherID = '" . $value['id'] . "'";
						$sqlResultDelete = $this->m_dbConn->delete($sqlDelete);
						if($this->ShowDebugTrace == 1)
						{
							echo "Liability register deleted  for VoucherID = '" . $value['id'] . " result: " . $sqlResultDelete . "<BR>";
						}
					}
					$sqlResultDelete = 0;

				}				
				
				$sqlDelete = "Delete from voucher where RefNo = '" . $RefNo . "' and RefTableID = '" . TABLE_BILLREGISTER . "'";
				$sqlResultDelete = $this->m_dbConn->delete($sqlDelete);
			}
                        
			//trace msg for single deleted record
			$this->delTrace = "Deleted Bill for Unit <".$UnitID."> PeriodID <".$PeriodID.">";
			//trace msg for deleted entries
			if($this->ShowDebugTrace == 1)
			{
				echo "Deleted Bill for Unit <".$UnitID."> PeriodID <".$PeriodID."><BR>";
			}
			if($bIsManualDelete == true && $bIsDeleteAll == false)
			{
				$this->m_objLog->setLog($this->delTrace, $_SESSION['login_id'], 'billdetails', $RefNo);
			}
      }
	}
	
	private function SetVoucher($UnitID, $PeriodID, $InsertID, $BillDate, $resultFetch, $AdditionalBillingHeads)
	{
	//	$this->ShowDebugTrace = 1;
		if ($this->ShowDebugTrace == 1)
		{
			echo '<BR><br>Inside SetVoucher: ' . $UnitID . "<BR>";
			//var_dump($resultFetch);
		}

		$sqlFetch = "Select `BillSubTotal`, `BillInterest`, `CurrentBillAmount`, `AdjustmentCredit`, `BillTax`,`IGST`,`CGST`,`SGST`,`CESS` from `billdetails` where UnitID = '" . $UnitID . "' and PeriodID = '" . $PeriodID . "' and BillType = '" . $this->IsSupplementaryBill() .  "'";
		$sqlResult = $this->m_dbConn->select($sqlFetch);
		
		$iBillInterest = 0;
		$iCurrentBillAmount = 0;
		
		if($sqlResult <> '')
		{
			$iVoucherCouter = $this->obj_LatestCount->getLatestVoucherNo($_SESSION['society_id']);
			$iSrNo = 1;
			$BillSubTotal = $sqlResult[0]['BillSubTotal'];
			$iBillInterest = $sqlResult[0]['BillInterest'];
//			$iCurrentBillAmount = $sqlResult[0]['CurrentBillAmount'];
			$iAdjustmentCredit = $sqlResult[0]['AdjustmentCredit'];
			$iBillTax = $sqlResult[0]['BillTax'];
			$iIGST = $sqlResult[0]['IGST'];
			$iCGST = $sqlResult[0]['CGST'];
			$iSGST = $sqlResult[0]['SGST'];
			$iCESS = $sqlResult[0]['CESS'];

			$iTotalAmount = $BillSubTotal + $iBillInterest + $iAdjustmentCredit + $iBillTax + $iIGST + $iCGST + $iSGST + $iCESS ;
			
			if ($this->ShowDebugTrace == 1)
			{
				echo '<br/>iTotalAmount: ' . $iTotalAmount . "<BR>";
			}
//			$obj_voucher = new voucher($this->m_dbConn);
			$sqlVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($BillDate), $InsertID, TABLE_BILLREGISTER, $iVoucherCouter, $iSrNo, VOUCHER_SALES, $UnitID, TRANSACTION_DEBIT, $iTotalAmount);
			if ($this->ShowDebugTrace == 1)
			{
				echo '<br/>After SetVoucherDetails: ' . $regResult . "<BR>";
			}
			
//			$obj_register = new regiser($this->m_dbConn);
		 	$regResult = $this->obj_register->SetRegister(getDBFormatDate($BillDate), $UnitID, $sqlVoucherID, VOUCHER_SALES, TRANSACTION_DEBIT, $iTotalAmount, 0);
			if ($this->ShowDebugTrace == 1)
			{
				echo '<br/>After SetRegister: ' . $regResult . "<BR>";
			}
			
			$sqlPeriod = "Select BeginingDate, EndingDate from period where ID = '" . $PeriodID . "'";
			$resultPeriod = $this->m_dbConn->select($sqlPeriod);
			
			$beginDate = $resultPeriod[0]['BeginingDate'];
			$endDate = $resultPeriod[0]['EndingDate'];
			
			
			/****************************************************************************
			//Get the Account Heads as per the period selected
			*****************************************************************************/

			$this->strTrace .=  '<tr><td>Updated AccountHead : ' . $sqlFetch . '</td></tr>';
			
			$logMsg =  "<br/><b>Records fetched for Period [" . $PeriodID . "] - Begin Date <= " . $beginDate . "</b>";
			
			$logMsg .= '<br/>' . implode('<br/>', array_map(function ($entry) 
			{
				return "Head : " . $entry['AccountHeadID'] . " Amount : " . $entry['AccountHeadAmount'] . " BeginPeriod " . $entry['BeginPeriod'] . " EndPeriod " . $entry['EndPeriod'];
			}, $resultFetch));
			
			if ($this->ShowDebugTrace == 1)
			{
				echo $logMsg;
			}
			$this->strTrace .= "<br/>" . $logMsg;
			
			$sqlResult = array();
			
			if($resultFetch <> "")
			{
				$unit_inprocess = 0;
				$ledger_inprocess = 0;
				$add = false;
				
				for($iCnt = 0; $iCnt < sizeof($resultFetch); $iCnt++)
				{
					if ($this->ShowDebugTrace == 1)
					{
						echo "<BR>" . $resultFetch[$iCnt]['BeginPeriod'] . " " . $resultFetch[$iCnt]['EndPeriod'] . " Acct Head:". $resultFetch[$iCnt]['AccountHeadID'] . "  Amount : " . $resultFetch[$iCnt]['AccountHeadAmount'];
					}
					$dateDiff = $this->m_objUtility->getDateDiff($resultFetch[$iCnt]['EndPeriod'], $endDate);

					if($unit_inprocess <> $resultFetch[$iCnt]['UnitID'])
					{
						$ledger_inprocess = 0;
					}
					
					if($dateDiff >= 0 && $ledger_inprocess <> $resultFetch[$iCnt]['AccountHeadID'])
					{
						//echo "<BR>Passed";
						$unit_inprocess = $resultFetch[$iCnt]['UnitID'];
						$ledger_inprocess = $resultFetch[$iCnt]['AccountHeadID'];
						array_push($sqlResult, $resultFetch[$iCnt]);
					}
				}
			}
			
			//var_dump($sqlResult);
			
			$logMsg = "<br/><br/><b>Filtered Records for Period [" . $PeriodID . "] with Date Between " . $beginDate . " AND " . $endDate . "</b>";
			$logMsg .= "<br/>" . implode('<br/>', array_map(function ($entry) 
			{
				return "Head : " . $entry['AccountHeadID'] . " Amount : " . $entry['AccountHeadAmount'] . " BeginPeriod " . $entry['BeginPeriod'] . " EndPeriod " . $entry['EndPeriod'];
			}, $sqlResult));
			
			//echo $logMsg;
			$this->strTrace .= "<br/>" . $logMsg;
						
			$ExtraHeadCount = count($AdditionalBillingHeads) ;
			//echo '<br>ExtraHeadCount : ' . $ExtraHeadCount . '<br>';
			$this->strTrace .=  '<tr><td>ExtraHeadCount : ' . $ExtraHeadCount . '</td></tr>';
			
			//add AdditionalBillingHeads elements to member unitbillmaster records
			$Index = 0;
			$InterestOnPrincipalLegderID = INTEREST_ON_PRINCIPLE_DUE; //pending
			$InterestOnArrearsReversalCharge = 0;
			while ($Index < $ExtraHeadCount)
			{
					//echo "<BR>Account head : " . $AdditionalBillingHeads[$Index]['AccountHeadID'];
					if( $AdditionalBillingHeads[$Index]['AccountHeadID'] == $InterestOnPrincipalLegderID)
					{
						//Process Interest credit
						$InterestOnArrearsReversalCharge = $InterestOnArrearsReversalCharge + $AdditionalBillingHeads[$Index]['AccountHeadAmount'];
						if ($this->ShowDebugTrace == 1)
						{
									echo "<BR>SetVoucher:Interest reversal credit : AccountHeadID :". $AdditionalBillingHeads[$Index]['AccountHeadID'] . "  Amount :". $AdditionalBillingHeads[$Index]['AccountHeadAmount'] . "  taxable? :". $AdditionalBillingHeads[$Index]['Taxable'] . "<BR>";
						}
					}
					else
					{
						array_push($sqlResult, $AdditionalBillingHeads[$Index]);
					}
				$Index = $Index +1;
			}
			
			if ($this->ShowDebugTrace == 1)
			{
				var_dump($sqlResult);
			}
			if($sqlResult <> '')
			{
				foreach($sqlResult as $k=>$v)
				{
					//echo '<br>ID : ' . $sqlResult[$k]['AccountHeadID'] . ' Amount : ' . $sqlResult[$k]['AccountHeadAmount'];
					$this->strTrace .=  '<tr><td>ID : ' . $sqlResult[$k]['AccountHeadID'] . ' Amount : ' . $sqlResult[$k]['AccountHeadAmount'].'</td></tr>';
					
					if($sqlResult[$k]['AccountHeadAmount'] <> '' || $sqlResult[$k]['AccountHeadAmount'] <> 0.00)
					{
						$iSrNo = $iSrNo + 1;
						
						$sqlVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($BillDate), $InsertID, TABLE_BILLREGISTER, $iVoucherCouter, $iSrNo, VOUCHER_SALES, $sqlResult[$k]['AccountHeadID'], TRANSACTION_CREDIT, $sqlResult[$k]['AccountHeadAmount']);
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>Head " . $sqlResult[$k]['AccountHeadID'] . "  Amount : " . $sqlResult[$k]['AccountHeadAmount'] . "<BR>";
						}
						$regResult = $this->obj_register->SetRegister(getDBFormatDate($BillDate), $sqlResult[$k]['AccountHeadID'], $sqlVoucherID, VOUCHER_SALES, TRANSACTION_CREDIT, $sqlResult[$k]['AccountHeadAmount']);
					}
				}
				//if($iBillInterest > 0)
				{
					$iSrNo = $iSrNo + 1;
					//echo "<BR>iBillInterest : ". $iBillInterest ; 
					$sqlVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($BillDate), $InsertID, TABLE_BILLREGISTER, $iVoucherCouter, $iSrNo, VOUCHER_SALES, INTEREST_ON_PRINCIPLE_DUE, TRANSACTION_CREDIT, $iBillInterest);

					$regResult = $this->obj_register->SetRegister(getDBFormatDate($BillDate), INTEREST_ON_PRINCIPLE_DUE, $sqlVoucherID, VOUCHER_SALES, TRANSACTION_CREDIT, $iBillInterest);
				}
				$SocDetails=$this->m_objUtility->GetSocietyInformation($_SESSION['society_id']);
				$bApplyServiceTax = $SocDetails['apply_service_tax'];

				$iDateDiff = $this->m_objUtility->getDateDiff(getDBFormatDate($BillDate), GST_START_DATE);

				if($iDateDiff < 0)
				{
					$bApplyServiceTax = 0;
				}
				if($this->ShowDebugTrace == 1)
				{
					echo "bApplyServiceTax : " . $bApplyServiceTax  . "<BR>";
				}
				if($bApplyServiceTax == 1) 
				{
					if($iCGST <> 0)
					{
						$iSrNo = $iSrNo + 1;					
						$sqlVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($BillDate), $InsertID, TABLE_BILLREGISTER, $iVoucherCouter, $iSrNo, VOUCHER_SALES, CGST_SERVICE_TAX, TRANSACTION_CREDIT, $iCGST);
	
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>Head " . CGST_SERVICE_TAX . "  Amount : " . $iCGST . "<BR>";
						}
						$regResult = $this->obj_register->SetRegister(getDBFormatDate($BillDate), CGST_SERVICE_TAX, $sqlVoucherID, VOUCHER_SALES, TRANSACTION_CREDIT, $iCGST, 0);
					}
					else
					{
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>CGST Amount is zero";	
							//pending : remove from Register
						}
					}
					
					if($iSGST <> 0)
					{					
						$iSrNo = $iSrNo + 1;					
						$sqlVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($BillDate), $InsertID, TABLE_BILLREGISTER, $iVoucherCouter, $iSrNo, VOUCHER_SALES, SGST_SERVICE_TAX, TRANSACTION_CREDIT, $iSGST);
	
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>Head " . SGST_SERVICE_TAX . "  Amount : " . $iSGST . "<BR>";
						}
						$regResult = $this->obj_register->SetRegister(getDBFormatDate($BillDate), SGST_SERVICE_TAX, $sqlVoucherID, VOUCHER_SALES, TRANSACTION_CREDIT, $iSGST, 0);
					}
					else
					{
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>SGST Amount is zero<BR>";	
						}
					}

/*
					if($iIGST <> 0)
					{
						$iSrNo = $iSrNo + 1;					
						$sqlVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($BillDate), $InsertID, TABLE_BILLREGISTER, $iVoucherCouter, $iSrNo, VOUCHER_SALES,IGST_SERVICE_TAX, TRANSACTION_CREDIT, c);
	
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>Head " . IGST_SERVICE_TAX . "  Amount : " . $iIGST . "<BR>";
						}
						$regResult = $this->obj_register->SetRegister(getDBFormatDate($BillDate), IGST_SERVICE_TAX, $sqlVoucherID, VOUCHER_SALES, TRANSACTION_CREDIT, $iIGST, 0);
					}
					else
					{
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>IGST Amount is zero";	
						}
					}
					
					
					if($iCESS <> 0)
					{
						$iSrNo = $iSrNo + 1;					
						$sqlVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($BillDate), $InsertID, TABLE_BILLREGISTER, $iVoucherCouter, $iSrNo, VOUCHER_SALES,CESS_SERVICE_TAX, TRANSACTION_CREDIT, $iCESS);
	
						$regResult = $this->obj_register->SetRegister(getDBFormatDate($BillDate), CESS_SERVICE_TAX, $sqlVoucherID, VOUCHER_SALES, TRANSACTION_CREDIT, $iCESS, 0);	
					}
					else
					{
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>CESS Amount is zero";	
						}
					}
					*/
				}
			}			
		}
	}

	public function SetBillCalcValues($UnitID, $PeriodID, $BillSubTotal, $AmountPaid = 0)
	{
		//Current Period
		$sqlQuery = "Select ID,BalanceAmount,PreviousArrears,BillSubTotal,BalPrincipal,BalInterest,BalanceAmount from billcalc where UnitID=" . $UnitID. " and PeriodID=" . $PeriodID;
		$this->strTrace .=  '<tr><td>'.$sqlQuery.'</td></tr>';
		$result = $this->m_dbConn->select($sqlQuery);
		// end of current preriod 
		echo "<script>alert('".$sqlQuery."');</script>";		
		$ID = $result[0]['ID'];
		echo "ID: <" .$ID.">";
		$this->strTrace .=  '<tr><td>ID: <" .$ID."></td></tr>';
		$oldBillSubTotal = $result[0]['BillSubTotal'];		//PS: Why do we need prev subtotal?

		if($result == "")
		{
			if($AmountPaid > 0)
			{
				echo "<script>alert('No bill generated for unit id <'".$UnitID."'>');</script>";
				return;
			}
			//Previous entry
			
			$PrevPeriodID = $PeriodID - 1;		//Pending: Not necessarily true
				
			$sqlPrevQuery = "Select ID,BalPrincipal,BalInterest,BalanceAmount,BillSubTotal from billcalc where UnitID=" . $UnitID.
			 " and PeriodID=" . $PrevPeriodID; 
			//echo $sqlPrevQuery;
			//echo "<script>alert('".$sqlPrevQuery."');<///script>";
			$Prevresult = $this->m_dbConn->select($sqlPrevQuery);
			
			$BalPrincipal = $Prevresult[0]['BalPrincipal'];
			$BalInterest = $Prevresult[0]['BalInterest'];
			$BalanceAmount = $Prevresult[0]['BalanceAmount'];
			$PrevBillSubTotal = $Prevresult[0]['BillSubTotal'];
			$AccumBillPrincipal = $BillSubTotal;
			$AccumBillInterest = 0;
			$AccumBillSubTotal = $BillSubTotal;
			if($BalanceAmount == 0)
			{
				$BalPrincipal = '0';
				$BalInterest = '0';
				$BalanceAmount = '0';
			}
			else
			{
				$AccumBillPrincipal = $BalPrincipal + $BillSubTotal;
				$AccumBillInterest = $BalInterest + '0';
				$AccumBillSubTotal = $BalanceAmount + $BillSubTotal;
			}
					$InsertQuery = "INSERT INTO `billcalc`(`ID`, `UnitID`, `PeriodID`, `ModifiedFlag`, `BillSubTotal`, `AdjustmentCredit`,
					 `Interest`, `Tax`, `PreviousArrears`, `ArrearInterest`, `TotalDue`, `LateDays`, `PaidAmt`, `PaidPrincipal`, `PaidInterest`,
					  `BalPrincipal`, `BalInterest`, `BalanceAmount`, `Remark`) VALUES (".$ID."," .$UnitID.",".$PeriodID.",'0',".$BillSubTotal.
					  ",'0','0','0',". $BalPrincipal .",". $BalInterest .",". $BalanceAmount .",'0','0','0','0',".$AccumBillPrincipal.
					  ",".$AccumBillInterest.",".$AccumBillSubTotal.",'test insert 1')";
						echo $InsertQuery;
						$this->strTrace .=  '<tr><td>'. $InsertQuery . '</td></tr>';
						echo "<script>alert('".$InsertQuery."');</script>";
						$this->m_dbConn->insert($InsertQuery);
		}
		else
		{
			$CurBalPrincipal = $result[0]['BalPrincipal'];
			$CurBalInterest = $result[0]['BalInterest'];
			$CurBalanceAmount = $result[0]['BalanceAmount'];
			$CurBillSubTotal = $result[0]['BillSubTotal'];
			if($AmountPaid == 0)
			{
				
				$SubTotalDiff = 0;
				if($BillSubTotal >= $CurBillSubTotal)
				{
				   $SubTotalDiff = $BillSubTotal - $CurBillSubTotal;
				}
				else
				{
					$SubTotalDiff = $CurBillSubTotal - $BillSubTotal;
				}
				$CurBalanceAmount = $BillSubTotal;
				$CurBalPrincipal += $SubTotalDiff;
				
				$UpdateQuery = "UPDATE `billcalc` SET `ID`=".$ID.",`UnitID`=".$UnitID.",`PeriodID`=".$PeriodID.",`ModifiedFlag`='1',`BillSubTotal`="
				.$BillSubTotal.",`AdjustmentCredit`='0',`Interest`=0,`Tax`='0',`PreviousArrears`='0',`ArrearInterest`='0',`TotalDue`='0'
				,`LateDays`='0',`PaidAmt`='0',`PaidPrincipal`='0',`PaidInterest`='0',`BalPrincipal`=".$CurBalPrincipal.",`BalInterest`='0',`BalanceAmount`="
				.$CurBalanceAmount.",`Remark`='test update 1' WHERE UnitID=" . $UnitID. " and PeriodID=" . $PeriodID;
				echo $UpdateQuery;
				$this->strTrace .=  '<tr><td>'. $UpdateQuery. '</td></tr>';
				echo "<script>alert('".$UpdateQuery."');</script>";
				$this->m_dbConn->update($UpdateQuery);
			}
			else
			{
				$PreviousArrears = $result[0]['PreviousArrears'];
				$ArrearInterest = $result[0]['ArrearInterest'];
				$TotalDue = $result[0]['TotalDue'];
				
				$iBalPrincipal = $result[0]['BalPrincipal'];
				$iBalInterest = $result[0]['BalInterest'];
				$iBalanceAmount = $result[0]['BalanceAmount'];
				
				$InterestAmount = CalculateInterst($AmountPaid, $InterestPercetage);
				$PaidPrincipal = $AmountPaid - $InterestAmount;
				
				$iBalPrincipal = $PaidPrincipal - $iBalPrincipal;
				$iBalInterest = $InterestAmount - $iBalInterest;
				$iBalAmount = $AmountPaid - $iBalAmount;
				
				$UpdateQuery = "UPDATE `billcalc` SET `ID`=".$ID.",`UnitID`=".$UnitID.",`PeriodID`=".$PeriodID.",`ModifiedFlag`='1',`BillSubTotal`="
				.$BillSubTotal.",`AdjustmentCredit`='0',`Interest`=0,`Tax`='0',`PreviousArrears`=". $PreviousArrears .
				",`ArrearInterest`=".$ArrearInterest.",`TotalDue`=".$TotalDue.",`LateDays`
				='0',`PaidAmt`=". $AmountPaid .",`PaidPrincipal`=".$PaidPrincipal.",`PaidInterest`=".$InterestAmount.",`BalPrincipal`=".$iBalPrincipal.
				",`BalInterest`=".$iBalInterest.",`BalanceAmount`="
				.$iBalAmount.",`Remark`='test update 2' WHERE UnitID=" . $UnitID. " and PeriodID=" . $PeriodID;
				echo $UpdateQuery;
				$this->strTrace .=  '<tr><td>'.$UpdateQuery . '</td></tr>';
				echo "<script>alert('".$UpdateQuery."');</script>";
				$this->m_dbConn->update($UpdateQuery);
			}
		}
	}
	
	
	public function CalculateInterst($AmountPaid, $InterestPercentage)
	{
		$InterestAmount = 0;
		$InterestPercentage = 10;
		$InterestAmount = $AmountPaid / $InterestPercentage;
		return $InterestAmount;
	}
	
	public function SetUpdatedBillParameters($objectUpdatedBill)
	{
		$this->oUpdateBillData = $objectUpdatedBill;
	}
	
	public function UpdateBill()
	{
		echo "GST code not implemented";
		//die;
		
		$info = '';
		$arvalues = explode(',',$this->oUpdateBillData->arJournals);
		$_REQUEST['due_date'] = $this->oUpdateBillData->sDueDate;
		$_REQUEST['bill_date'] = $this->oUpdateBillData->sBillDate;
		$_REQUEST['period_id'] = $this->oUpdateBillData->sPeriodID;
		$_REQUEST['society_id'] = $this->oUpdateBillData->sSocietyID;
		$_REQUEST['wing_id'] = $this->oUpdateBillData->sWingID;
		$_REQUEST['unit_id'] = $this->oUpdateBillData->sUnitID;
		$_REQUEST['adjustment_credit'] = $this->oUpdateBillData->sAdjustmentCredit;
		$this->actionPage ="../Maintenance_bill_edit.php?UnitID=" . $this->oUpdateBillData->sUnitID . "&PeriodID=" .$this->oUpdateBillData->sPeriodID ."&BT=".$this->IsSupplementaryBill();
		if($_REQUEST['period_id'] <> '' && $_REQUEST['bill_date'] <> '' && $_REQUEST['due_date'] <> '')
		{
			$sqlGen = '';
			if($_REQUEST['society_id'] == '0')
			{
				//generate for all units in all societies
				$sqlGen = "select master.UnitID, master.AccountHeadID, master.AccountHeadAmount from unitbillmaster as master where BillType=".$this->IsSupplementaryBill();
			}	
			else if($_REQUEST['wing_id'] == '0')
			{
				//generate for all units in single society
				$sqlGen = "select master.UnitID, master.AccountHeadID, master.AccountHeadAmount, master.BillType from unitbillmaster as master JOIN unit as unitinfo on master.UnitID = unitinfo.unit_id where unitinfo.society_id = '" . $_REQUEST['society_id'] .  "' AND master.billType =".$this->IsSupplementaryBill();
			}
			else if($_REQUEST['unit_id'] == '0')
			{
				//generate for all units in single wing
				$sqlGen = "select master.UnitID, master.AccountHeadID, master.AccountHeadAmount, master.BillType from unitbillmaster as master JOIN unit as unitinfo on master.UnitID = unitinfo.unit_id where unitinfo.society_id = '" . $_REQUEST['society_id'] .  "' and unitinfo.wing_id = '" . $_REQUEST['wing_id'] . "'  AND master.billType =".$this->IsSupplementaryBill();
			}
			else
			{
				//generate for single unit
				$sqlGen = "select master.UnitID, master.AccountHeadID, master.AccountHeadAmount, master.BillType from unitbillmaster as master where master.UnitID = '" . $_REQUEST['unit_id'] . "' AND master.billType =".$this->IsSupplementaryBill();
			}
		echo $sqlGen;
		$this->strTrace .=  '<tr><td>'.$sqlGen.'</td></tr>';
			$result = $this->m_dbConn->select($sqlGen);
			
			if($result == "")
			{
				echo "<script>alert('record doesnt exist');</script>";
			}
			$BillSubTotal = 0;
			if($result <> "")
			{
				//echo "before fetch data";
				$this->strTrace .=  '<tr><td>before fetch data</td></tr>';
				$objFetchDaa = new FetchData($this->m_dbConn);
				
				for($iCount = 0; $iCount < sizeof($this->oUpdateBillData->arrData); $iCount++)
				{
					$Header = $objFetchDaa->GetHeadingFromAccountHead($this->oUpdateBillData->arrData[$iCount]["key"]);
					//echo "after fetch data";
					$this->strTrace .=  '<tr><td>after fetch data</td></tr>';
					$HeaderAmount = $this->oUpdateBillData->arrData[$iCount]["value"];
					$HeaderName = $this->oUpdateBillData->arrData[$iCount]["key"];
					$BillSubTotal += $HeaderAmount;
					//echo "subbill <" . $BillSubTotal . ">.";
					if($HeaderAmount <> 0)
					{
						//$sqlCheck = "select ID from billregister where UnitID = '" . $this->oUpdateBillData->sUnitID . "' and PeriodID = '" . $_REQUEST['period_id'] . "' and AccountHeadID = '" . $this->oUpdateBillData->arrData[$iCount]["key"] . "'" ;
						
						$sqlCheck = "select ID from billdetails where UnitID = '" . $this->oUpdateBillData->sUnitID . "' and PeriodID = '" . $_REQUEST['period_id'] . "'" ;
						
						//echo '<br/>SqlCheck : ' . $sqlCheck;
						//echo "<script>alert('che". $sqlCheck ."');<//script>";
						$resultCheck = $this->m_dbConn->select($sqlCheck);
						if($resultCheck <> "")
						{
							$iLatestChangeID = '1';
							$iBillNo = $this->oUpdateBillData->sBillNo;
							
							//$sqlUpdate = "UPDATE `billregister` SET `CreatedBy`='" . $this->m_dbConn->escapeString($_SESSION['login_id']). "', `BillDate`='" . $this->oUpdateBillData->sBillDate . "', `DueDate`='" . $this->oUpdateBillData->sDueDate . "', `BillNumber`='" . $this->oUpdateBillData->sBillNo . "', `LatestChangeID`='" . $this->m_dbConn->escapeString($iLatestChangeID). "', `AccountHeadAmount`='". $HeaderAmount ."' WHERE ID ='" . $resultCheck[0]['ID'] . "' and UnitID ='" . $this->oUpdateBillData->sUnitID . "' and PeriodID='" . $_REQUEST['period_id'] . "'";
							
							
							$sqlUpdate1 = "UPDATE `billdetails` SET `BillNumber`='" . $this->oUpdateBillData->sBillNo . "', `AdjustmentCredit`='" .$this->oUpdateBillData->sAdjustmentCredit ."' WHERE ID ='" . $resultCheck[0]['ID'] . "' and UnitID ='" . $this->oUpdateBillData->sUnitID . "' and PeriodID='" . $_REQUEST['period_id'] . "'";
							$resultUpdate1 = $this->m_dbConn->update($sqlUpdate1);
							
							$sqlUpdate2 = "UPDATE `voucher` SET `Credit`= '" . $HeaderAmount ."' where `To`= '" . $HeaderName ."' and `RefNo` ='" . $resultCheck[0]['ID'] . "' ";
							$resultUpdate2 = $this->m_dbConn->update($sqlUpdate2);
							
							$sqlUpdate3 = "UPDATE `billregister` SET `CreatedBy`='" . $this->m_dbConn->escapeString($_SESSION['login_id']). "', `BillDate`='" . $this->oUpdateBillData->sBillDate . "', `DueDate`='" . $this->oUpdateBillData->sDueDate . "', `LatestChangeID`='" . $this->m_dbConn->escapeString($iLatestChangeID). "' WHERE ID ='" . $resultCheck[0]['ID'] . "'";
							$resultUpdate3 = $this->m_dbConn->update($sqlUpdate3);
						}
					}
				}
				//echo "subbilltotal <" . $BillSubTotal . ">.";
				$this->strTrace .=  '<tr><td>subbilltotal <" . $BillSubTotal . "></td></tr>';

				$info = "Bills generated successfully.";
			}
			else
			{
				$info = "No Units found in Bill Master to Generate Bills for selected criteria.";
			}
			echo "calling calcbill" . $_REQUEST['unit_id'] . '"'. $_REQUEST['period_id'] .'"' .$BillSubTotal;
			$this->strTrace .=  "<tr><td>calling calcbill" . $_REQUEST['unit_id'] . "'". $_REQUEST['period_id'] ."'" .$BillSubTotal . "</td></tr>";
			//$this->SetBillCalcValues($_REQUEST['unit_id'], $_REQUEST['period_id'], $BillSubTotal);	
			echo "bill calc done";
			$this->strTrace .=  '<tr><td>bill calc done</td></tr>';
		}
		else
		{
			$info = 'Please enter valid data for all the fields.';		
		}
		//echo "<script>alert('exit')<//script>";
		return $info;
	}
	
	public function GetValueFromBillCalc($UnitID, $PeriodID, $ColumnName)
	{
		$sqlCheck = "select ".$ColumnName." from billcalc where UnitID =" . $UnitID . " and PeriodID = " . $PeriodID ;
						
			//echo '<br/>SqlCheck : ' . $sqlCheck;
			//echo "<script>alert('che". $sqlCheck ."');<//script>";
			$resultCheck = $this->m_dbConn->select($sqlCheck);
			if($resultCheck <> "")
			{
				return $resultCheck[0][$ColumnName];
				
			}
	}
	public function GetValueFromChequeEntryDetails($UnitID, $PeriodID, $ColumnName)
	{
		//$sqlCheck = "select ".$ColumnName." from chequeentrydetails where UnitID =" . $UnitID . " and PeriodID = " . $PeriodID ;
		$sqlCheck = "select ".$ColumnName." from chequeentrydetails where ID='1'" ;
					
		//echo '<br/>SqlCheck : ' . $sqlCheck;
		//echo "<script>alert('che". $sqlCheck ."');<//script>";
		$resultCheck = $this->m_dbConn->select($sqlCheck);
		if($resultCheck <> "")
		{
			//echo "<script>alert('".$resultCheck[0][$ColumnName]."')<//script>";
			return $resultCheck[0][$ColumnName];
			
		}
	}

	public function IsLedgerTaxable($LedgerID)
	{
			//Code Review: subcategory column should change to ledger name
			$sqlCheck = "select * from ledger where id = " . $LedgerID ;
						
			echo '<br/>SqlCheck : ' . $sqlCheck;
			$this->strTrace .=  '<tr><td>SqlCheck : ' . $sqlCheck . '</td></tr>';
			//echo "<script>alert('che". $sqlCheck ."');<//script>";
			$resultCheck = $this->m_dbConn->select($sqlCheck);
			if($resultCheck <> "")
			{
				//echo "<script>alert('".$resultCheck[0]['subcategory']."')<//script>";
				return $resultCheck[0]['taxable'];
			}	
			
			return 0;
	}

	public function GetListofSundryDebtors()
	{
			//Code Review: subcategory column should change to ledger name
			$sqlCheck = "select subcategory from ledger where receipt='1'" ;
						
			echo '<br/>SqlCheck : ' . $sqlCheck;
			$this->strTrace .=  '<tr><td>SqlCheck : ' . $sqlCheck . '</td></tr>';
			//echo "<script>alert('che". $sqlCheck ."');<//script>";
			$resultCheck = $this->m_dbConn->select($sqlCheck);
			/*if($resultCheck <> "")
			{
				//echo "<script>alert('".$resultCheck[0]['subcategory']."')<//script>";
				return $resultCheck[0][$ColumnName];
			}	
			*/
			return $resultCheck;
	}

	public function getPreviousPeriodData($PeriodID)
	{
//			echo '<br/>PeriodID : ' . $PeriodID;
			$sqlPrevQuery = "Select Type, YearID, PrevPeriodID, Status from period where ID=" . $PeriodID;
			//echo '<br/>sqlPrevQuery : ' . $sqlPrevQuery;
			$Prevresult = $this->m_dbConn->select($sqlPrevQuery);
			$PrevPeriodID = -1;
			if(!is_null($Prevresult))
			{
				$Type = $Prevresult[0]['Type'];			
				$YearID = $Prevresult[0]['YearID'];			
				$PrevPeriodID = $Prevresult[0]['PrevPeriodID'];			
/*				echo '<br/>Prev Period : ' . $Type;
				echo '<br/>Prev YearID : ' . $YearID;
				echo '<br/>PrevPeriodID : ' . $PrevPeriodID;*/
			}
			return $PrevPeriodID;	
	}

//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
	public function PrevBillDueDate($PrevBillRegisterID)
	{
		$sqlBillPrevQuery = "Select DueDate from billregister where ID=" . $PrevBillRegisterID ; 
		//echo '<br/>sqlPrevQuery : ' . $sqlBillPrevQuery;
		$SQLResult = $this->m_dbConn->select($sqlBillPrevQuery);

		$PrevBillDueDate = 0;

		if(!is_null($SQLResult))
		{
			$PrevBillDueDate = $SQLResult[0]['DueDate'];;
		}
		return $PrevBillDueDate;
	}
	
	public function PrevBillBillDate($PrevBillRegisterID)
	{
		$PrevBillBillDate = 0;
		if($PrevBillRegisterID <> '' && $PrevBillRegisterID <> 0)
		{
			$sqlBillPrevQuery = "Select BillDate from billregister where ID=" . $PrevBillRegisterID ; 
			//echo '<br/>sqlPrevQuery : ' . $sqlBillPrevQuery;
			$SQLResult = $this->m_dbConn->select($sqlBillPrevQuery);
			if(!is_null($SQLResult))
			{
				$PrevBillBillDate = $SQLResult[0]['BillDate'];
			}
		}
		return $PrevBillBillDate;
	}
	
	
/*	public function generateBill()
	{
		$UnitID = 30;
		$PeriodID = 7;
		$PrevPeriodID = $this->getPreviousPeriodData($UnitID, $PeriodID);
		if($PrevPeriodID < 1	)
		{
			echo '<br/>No prev period for period <' . $PeriodID . "> : assuming this is first period.";
			//return;
		}
				
		$this->generateBill_PerUnit($UnitID, $PeriodID, $PrevPeriodID);
		return;
	}
*/

	public function generateBill_PerUnit($UnitID, $PeriodID, $PrevPeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate, $BillRegisterID, $BillNo, $BillCalcType, $InterestRate,$InterestMethod,$InterestTrigger, $RebateMethod, $RebateAmount, $sBillDate)
	{
		//$this->ShowDebugTrace = 1;
		$BillNote = "";
		//=====================================
		// if(!$this->IsSupplementaryBill())
		 //{
			$sqlPrevQuery = "Select ID, BillRegisterID, PrincipalArrears, InterestArrears, `BillSubTotal`, `AdjustmentCredit`, `BillTax`,`IGST`,`CGST`, `SGST`, `CESS`, `BillInterest` from billdetails where UnitID=" . $UnitID.	 " and PeriodID=" . $PrevPeriodID." AND BillType ='" . $this->IsSupplementaryBill()."' " ; 
			//echo '<br/>sqlPrevQuery : ' . $sqlPrevQuery;
			$this->strTrace .=  '<tr><td>sqlPrevQuery : ' . $sqlPrevQuery . '</td></tr>';
			$SQLResult = $this->m_dbConn->select($sqlPrevQuery);
		 //}
		 //echo "<br/>skipped prev</br>";
		$PrevPrincipalDue = 0;
		$PrevInterestDue = 0;
		$PrevBillSubTotal = 0;
		$PrevAdjustmentCredit= 0;
		$PrevBillTax = 0;
		$PrevIGST = 0;
		$PrevCGST = 0;
		$PrevCGST = 0;
		$PrevCESS = 0;
		$PrevBillPrincipalAmount = 0;
		$PrevBillInterestAmount = 0;
		$CurrentBillInterestAmount = 0;
		$dateDiff  =0;		
		if(!is_null($SQLResult))
		{
			for($iCounter = 0; $iCounter < sizeof($SQLResult); $iCounter++)
			{
				$PrevBillRegisterID = $SQLResult[$iCounter]['BillRegisterID'];
				
				//ToDo Get PrevBill Due date using JOIN
				$PrevBillDueDate = $this->PrevBillDueDate($PrevBillRegisterID);
				$this->strTrace .=  '<tr><td>PrevBillDueDate : ' . $PrevBillDueDate . '</td></tr>';
				$this->strTrace .=  '<tr><td>CurrentBillDate : ' . $sBillDate . '</td></tr>';
				
				$dateDiff = $this->m_objUtility->getDateDiff($PrevBillDueDate,$sBillDate);
				$this->strTrace .=  '<tr><td>dateDiff : ' . $dateDiff . '</td></tr>';	
				
				$PrevPrincipalDue = $PrevPrincipalDue + $SQLResult[$iCounter]['PrincipalArrears'];
				$PrevInterestDue =  $PrevInterestDue + $SQLResult[$iCounter]['InterestArrears'];;
		
				$PrevBillSubTotal = $PrevBillSubTotal  +  $SQLResult[$iCounter]['BillSubTotal'];;
				$PrevAdjustmentCredit = $PrevAdjustmentCredit + $SQLResult[$iCounter]['AdjustmentCredit'];;
				
				$PrevIGST = $PrevIGST + $SQLResult[$iCounter]['IGST'];
				$PrevCGST = $PrevCGST + $SQLResult[$iCounter]['CGST'];
				$PrevSGST = $PrevSGST + $SQLResult[$iCounter]['SGST'];
				$PrevCESS = $PrevCESS + $SQLResult[$iCounter]['CESS'];

				$PrevBillTax = $PrevBillTax + $SQLResult[$iCounter]['BillTax']+ $SQLResult[$iCounter]['IGST']+ $SQLResult[$iCounter]['CGST'] +$SQLResult[$iCounter]['SGST'] + $SQLResult[$iCounter]['CESS'];
				
				$PrevBillPrincipalAmount = $PrevBillSubTotal + $PrevAdjustmentCredit + $PrevIGST + $PrevCGST + $PrevSGST + $PrevCESS;// + $PrevBillTax;
				
				$PrevBillPrincipal_ = $PrevBillPrincipalAmount;
				$PrevBillInterestAmount =  $PrevBillInterestAmount + $SQLResult[$iCounter]['BillInterest'];
				
				if($dateDiff  > 0)
				{
					$this->strTrace .=  '<tr><td> PrevBillDueDate greater than CurrentBillDate Hence interest not calculated.</td></tr>';	
				}
			}
		}
		else
		{
//			echo '<br/>No Query Data. May be a first period: <br><br>';
		}
		//=====================================
		
		$PrevPrincipalDue_ = $PrevPrincipalDue ;
		$PrevInterestDue_ = $PrevInterestDue;
		$PrevBillPrincipal_ = $PrevBillPrincipalAmount;
		$PrevBillInterest_ = $PrevBillInterestAmount;

		//We need to use LIFO : So payment is applied in this order
		//1. Interest 
		//2. Prev bill amount
		//3. Principal arrears
		
//		$PrevPrincipalDue = $PrevPrincipalDue + $PrevBillPrincipalAmount;
		$PrevInterestDue = $PrevInterestDue + $PrevBillInterestAmount;

		//Get payments made from Receipt Table	= $PaymentReceived

		$PaidPrincipal = 0;
		$PaidInterest = 0;
		
//		$resultCheck = $this->GetPaymentsReceived ($UnitID, $PeriodID, $PrevPeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate);


//===========

		if ($this->ShowDebugTrace == 1)
		{
			echo '<br/>InterestRate : ' . $InterestRate;
			echo '<br/>InterestMethod : ' . $InterestMethod;
			echo '<br/>InterestTrigger : ' . $InterestTrigger;
			echo '<br/>RebateMethod : ' . $RebateMethod;
			echo '<br/>RebateAmount : ' . $RebateAmount;
			echo '<br/>';
		}
//==============

		//echo '<BR> Before GetReversalCredits :<BR>';
		$AdditionalBillingHeads = $this->GetReversalCredits($UnitID, $PeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate);
		
		$this->strTrace .=  '<tr><td>AdditionalBillingHeads :</td></tr>';

		if ($this->ShowDebugTrace == 1)
		{
				$ExtraHeadCount = count($AdditionalBillingHeads) ;
				echo "<BR> AdditionalBillingHeads : " . $ExtraHeadCount . " <BR>";
				var_dump($AdditionalBillingHeads);
		}
//		$this->strTrace .=  '<tr><td>PrevPrincipalDue : ' . $PrevPrincipalDue . '</td></tr>';

		$BillMaster = $this->GetBillMaster($UnitID, $PeriodID);

		if ($this->ShowDebugTrace == 1)
		{
			echo '<br>GetBillMaster return: <br>';
			//var_dump($BillMaster);
		}
		$InterestOnArrearsReversalCharge = 0;
		$TaxableLedgerTotal = 0;
		$BillSubTotal = $this->GetBillSubTotal_Plain($UnitID, $PeriodID, $BillMaster, $AdditionalBillingHeads, $TaxableLedgerTotal, $InterestOnArrearsReversalCharge, $BillNote);
		
		if ($this->ShowDebugTrace == 1)
		{
			//echo '<br/>BillSubTotal : ' . $BillSubTotal;
			echo '<br/>BillSubTotal including additional heads: ' . $BillSubTotal;
			echo '<br/>InterestOnArrearsReversalCharge : ' . $InterestOnArrearsReversalCharge;
			echo '<br/>BillNote : ' . $BillNote;
		}
		$this->strTrace .=  '<tr><td>BillSubTotal including additional heads: ' . $BillSubTotal . '</td></tr>';
		//Caiculate Tax, if any
		//$BillTax = $this->CalculateTax($UnitID, $BillSubTotal);
	
//=================
			$CreditAmount = 0;
			//$PrevPrincipalDue = -100000; //pending cleanup
			if($PrevPrincipalDue < 0)
			{
				$CreditAmount = $PrevPrincipalDue * -1;
				$PrevPrincipalDue = 0;
				
				$this->strTrace .=  '<tr><td>There is a credit of : ' . $CreditAmount . '</td></tr>';
				$this->strTrace .=  '<tr><td>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount . '</td></tr>';
	
				if ($this->ShowDebugTrace == 1)
				{
					echo '<br/><br/>There is a credit of : ' . $CreditAmount;
					echo '<br/>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount;
				}
				if($CreditAmount  > $PrevBillPrincipalAmount)
				{			
					$CreditAmount  = $CreditAmount  - $PrevBillPrincipalAmount;
					$PaidPrincipal = $PrevBillPrincipalAmount;
					$PrevBillPrincipalAmount = 0;
					if ($this->ShowDebugTrace == 1)
					{
						echo '<br/><br/>Test 1 There is a credit of : ' . $CreditAmount;
						echo '<br/>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount;
					}
					
				}
				else
				{
					$PrevBillPrincipalAmount = $PrevBillPrincipalAmount - $CreditAmount ;
					$PaidPrincipal = $CreditAmount ;
					$CreditAmount  = 0;	
				}						
				//echo '<br/>There is a credit of : ' . $CreditAmount;
				$this->strTrace .=  '<tr><td>There is a credit of : ' . $CreditAmount . '</td></tr>';
				//echo '<br/>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount;
				$this->strTrace .=  '<tr><td>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount . '</td></tr>';
				//echo '<br/>PrevPrincipalDue : ' . $PrevPrincipalDue;
				$this->strTrace .=  '<tr><td>PrevPrincipalDue : ' . $PrevPrincipalDue . '</td></tr>';
				//echo '<br/>';
			}
			if ($this->ShowDebugTrace == 1)
			{
				echo '<br/>Test0 There is a credit of : ' . $CreditAmount;
				echo '<br/>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount;
				echo '<br/>PrevPrincipalDue : ' . $PrevPrincipalDue;
			}


			
			$PrevBillBillDate = $this->PrevBillBillDate($PrevBillRegisterID);
			//echo '<br/>PrevBillBillDate : ' . $PrevBillBillDate;
			$this->strTrace .=  '<tr><td>PrevBillBillDate : ' . $PrevBillBillDate . '</td></tr>';
				
			$sBillDate = $this->GetDateByOffset($sBillDate, -1);
			//echo '<br/>sCurrentBillDate : ' . $sBillDate;
			$this->strTrace .=  '<tr><td>sCurrentBillDate : ' . $sBillDate . '</td></tr>';
	
	//		echo '<br/>PrevPeriodBeginingDate : ' . $PrevPeriodBeginingDate;
	//		$sqlCheck = "select * from chequeentrydetails where voucherdate >= '". $PrevPeriodBeginingDate . "' AND voucherdate <= '" . $PrevPeriodEndingDate . "' AND PaidBy = " . $UnitID;
	
		    $sqlCheck = "select * from chequeentrydetails where voucherdate >= '". $PrevBillBillDate . "' AND voucherdate <= '" . $sBillDate . "' AND PaidBy = " . $UnitID." AND BillType  = ". $this->IsSupplementaryBill();
			//echo '<br/>SqlCheck inline : ' . $sqlCheck;
			$this->strTrace .=  '<tr><td>SqlCheck inline : ' . $sqlCheck . '</td></tr>';
			$resultCheck = $this->m_dbConn->select($sqlCheck);
			if ($this->ShowDebugTrace == 1)
			{
					$sqlCheck = "";
					echo "<BR>SqlCheckEntryDetails : " . $sqlCheck . "<BR>";
					print_r($resultCheck );
			}
			$PaymentDate = 0;
			$PaymentReceived = 0;
			$PaymentReceived_ = 0;
			$iTotalPayments = 0;
			
			if($resultCheck <> '')
			{
				foreach($resultCheck as $k => $v)
				{
					$PaymentDate = 0;
					$PaymentReceived = 0;
					$iTotalPayments = $iTotalPayments + 1;
					//echo '<br/><br/>///============ Payment : ' . $iTotalPayments;
					$this->strTrace .=  '<tr><td>///============ Payment : ' . $iTotalPayments . '</td></tr>';
					if($resultCheck[$k]['Amount'] <> 0)
					{
						$PaymentDate  = $resultCheck[$k]['VoucherDate'];					
						$ChequeDate = $resultCheck[$k]['ChequeDate'];
						$ChequeNumeber = $resultCheck[$k]['ChequeNumeber'];
						$PaymentReceived = $resultCheck[$k]['Amount'];
						$PayerBank = $resultCheck[$k]['PayerBank'];
						$IsReturn = $resultCheck[$k]['IsReturn'];
	//					echo '<br/><br/>IsReturn : ' . $IsReturn;
						if ($this->ShowDebugTrace == 1)
						{
							echo '<br/>Processing Cheque No: <' . $ChequeNumeber . '> dated <' . $ChequeDate . '> for amount <' . $PaymentReceived . '>';
						}
						if ($IsReturn == 1)
						{
							if ($this->ShowDebugTrace == 1)
							{
								echo '<br/>Cheque No: <' . $ChequeNumeber . '> dated <' . $ChequeDate . '> for amount <' . $PaymentReceived . '> would not be processed as it was returned';
							}
							$this->strTrace .=  '<tr><td>Cheque No: <' . $ChequeNumeber . '> dated <' . $ChequeDate . '> for amount <' . $PaymentReceived . '> would not be processed as it was returned</td></tr>';
						}
						else
						{
							$PaymentReceived_ = $PaymentReceived_ + $PaymentReceived;
		
							//echo '<br/><br/>ChequeDate : ' . $ChequeDate;
							$this->strTrace .=  '<tr><td>ChequeDate : ' . $ChequeDate . '</td></tr>';
							//echo '<br/><br/>ChequeNumeber : ' . $ChequeNumeber;
							$this->strTrace .=  '<tr><td>ChequeNumeber : ' . $ChequeNumeber . '</td></tr>';
							//echo '<br/><br/>PayerBank : ' . $PayerBank;
							$this->strTrace .=  '<tr><td>PayerBank : ' . $PayerBank . '</td></tr>';
							//echo '<br/><br/>Amount : ' . $PaymentReceived;
							$this->strTrace .=  '<tr><td>Amount : ' . $PaymentReceived . '</td></tr>';
		
							//echo '<br/><br/>PrevPrincipalDue : ' . $PrevPrincipalDue;
							$this->strTrace .=  '<tr><td>PrevPrincipalDue : ' . $PrevPrincipalDue . '</td></tr>';
							//echo '<br/>TotalPrevInterestDue : ' . $PrevInterestDue;
							$this->strTrace .=  '<tr><td>TotalPrevInterestDue : ' . $PrevInterestDue . '</td></tr>';
							//echo '<br/>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount;
							$this->strTrace .=  '<tr><td>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount . '</td></tr>';
							//echo '<br/>PrevBillInterestAmount : ' . $PrevBillInterestAmount;
							//echo '<br/><br/>PaymentReceived : ' . $PaymentReceived;
							$this->strTrace .=  '<tr><td>PaymentReceived : ' . $PaymentReceived . '</td></tr>';
							//echo '<br/>PaymentDate : ' . $PaymentDate;
							$this->strTrace .=  '<tr><td>PaymentDate : ' . $PaymentDate . '</td></tr>';
					
							//Apply received amount to calculate
					
							//Update prev Principal amount and Prev Interest (this will be updated in current bill record
							if($PaymentReceived > 0)
							{
								//Process Interest first
								//echo '<br/><br/><br/>PrevInterestDue Processing...';
								$this->strTrace .=  '<tr><td>PrevInterestDue Processing...</td></tr>';
								//echo '<br/>PrevInterestDue  : ' . $PrevInterestDue;
								$this->strTrace .=  '<tr><td>PrevInterestDue  : ' . $PrevInterestDue . '</td></tr>';
								//echo '<br/>PaidInterest  : ' . $PaidInterest;
								$this->strTrace .=  '<tr><td>PaidInterest  : ' . $PaidInterest . '</td></tr>';
								//echo '<br/>PaymentReceived : ' . $PaymentReceived;
								$this->strTrace .=  '<tr><td>PaymentReceived : ' . $PaymentReceived . '</td></tr>';
								if($PrevInterestDue > 0)
								{
									//echo '<br/>Processing...';
									$this->strTrace .=  '<tr><td>Processing...</td></tr>';
									if($PaymentReceived > $PrevInterestDue )
									{
										$PaidInterest = $PaidInterest + $PrevInterestDue;
										$PaymentReceived = $PaymentReceived - $PrevInterestDue;
										$PrevInterestDue = 0;
									}
									else
									{
										$PaidInterest = $PaidInterest + $PaymentReceived ;
										$PrevInterestDue = $PrevInterestDue - $PaymentReceived ;
										$PaymentReceived = 0;
									}
									
									//echo '<br/>PaidInterest  : ' . $PaidInterest;
									$this->strTrace .=  '<tr><td>PaidInterest  : ' . $PaidInterest . '</td></tr>';
									//echo '<br/>Remaining TotalPrevInterestDue : ' . $PrevInterestDue;
									$this->strTrace .=  '<tr><td>Remaining TotalPrevInterestDue : ' . $PrevInterestDue . '</td></tr>';
									//echo '<br/>PaymentReceived : ' . $PaymentReceived;
									$this->strTrace .=  '<tr><td>PaymentReceived : ' . $PaymentReceived . '</td></tr>';
								}
								else
								{
									//echo '<br/>No PrevInterestDue to process...';							
									$this->strTrace .=  '<tr><td>No PrevInterestDue to process...</td></tr>';
								}
								
								//echo '<br/>End of PrevInterestDue Processing...';
								$this->strTrace .=  '<tr><td>End of PrevInterestDue Processing...</td></tr>';
								//echo '<br/>';
		
								//Process PrevBill Principal first as we follow LIFO						
								$SubPaidPrincipal = 0;			
								//echo '<br/><br/>PrevBillPrincipalAmount Processing...';
								$this->strTrace .=  '<tr><td>PrevBillPrincipalAmount Processing...</td></tr>';
								if($PaymentReceived > 0) 
								{
									//echo '<br/>PrevBillPrincipalAmount  : ' . $PrevBillPrincipalAmount;
									$this->strTrace .=  '<tr><td>PrevBillPrincipalAmount  : ' . $PrevBillPrincipalAmount . '</td></tr>';
									//echo '<br/>SubPaidPrincipal  : ' . $SubPaidPrincipal;
									$this->strTrace .=  '<tr><td>SubPaidPrincipal  : ' . $SubPaidPrincipal . '</td></tr>';
									//echo '<br/>PaymentReceived : ' . $PaymentReceived;
									$this->strTrace .=  '<tr><td>PaymentReceived : ' . $PaymentReceived . '</td></tr>';
									if($PrevBillPrincipalAmount  > 0)
									{
										//echo '<br/>Processing...';
										$this->strTrace .=  '<tr><td>Processing...</td></tr>';
										
										if($PaymentReceived > $PrevBillPrincipalAmount )
										{
											$SubPaidPrincipal = $PrevBillPrincipalAmount;
											$PaymentReceived = $PaymentReceived - $PrevBillPrincipalAmount;
											$PrevBillPrincipalAmount = 0;
										}
										else
										{
											$SubPaidPrincipal = $PaymentReceived ;
											$PrevBillPrincipalAmount = $PrevBillPrincipalAmount - $PaymentReceived ;
											$PaymentReceived = 0;
										}
									}
								
									//echo '<br/>SubPaidPrincipal  : ' . $SubPaidPrincipal;
									$this->strTrace .=  '<tr><td>SubPaidPrincipal  : ' . $SubPaidPrincipal . '</td></tr>';
									//echo '<br/>Remaining PrevBillPrincipalAmount  : ' . $PrevBillPrincipalAmount;
									$this->strTrace .=  '<tr><td>Remaining PrevBillPrincipalAmount  : ' . $PrevBillPrincipalAmount . '</td></tr>';
									//echo '<br/>PaymentReceived : ' . $PaymentReceived;
									$this->strTrace .=  '<tr><td>PaymentReceived : ' . $PaymentReceived . '</td></tr>';
									
									if ($SubPaidPrincipal > 0 && ($PaymentDate > $PrevBillDueDate))
									{
										
										//echo '<br/>Calc interest on : ' . $SubPaidPrincipal;
										$this->strTrace .=  '<tr><td>Calc interest on : ' . $SubPaidPrincipal . '</td></tr>';
		
										//If payment is late calculate interest on part of principal that is paid by this cheque
										$InterestAmount = $this->GetInterest($UnitID, $SubPaidPrincipal, $PrevBillDueDate, $PaymentDate, $BillCalcType, $InterestRate,$InterestMethod,$InterestTrigger, $RebateMethod, $RebateAmount);
										$CurrentBillInterestAmount = $CurrentBillInterestAmount + $InterestAmount;
										//echo '<br/>CurrentBillInterestAmount : ' . $CurrentBillInterestAmount;
										$this->strTrace .=  '<tr><td>CurrentBillInterestAmount : ' . $CurrentBillInterestAmount . '</td></tr>';
									}
									else
									{
										//echo '<br/>No interest: ';
										$this->strTrace .=  '<tr><td>No interest:</td></tr>';
									}
									
									$PaidPrincipal = $PaidPrincipal + $SubPaidPrincipal;
									//echo '<br/>Paid Principal: ' . $PaidPrincipal;
									$this->strTrace .=  '<tr><td>Paid Principal: ' . $PaidPrincipal . '</td></tr>';
									//echo '<br/>';
									
								}
								else
								{
									//echo '<br/>No payment for PrevBillPrincipalAmount Processing...';
									$this->strTrace .=  '<tr><td>No payment for PrevBillPrincipalAmount Processing...</td></tr>';
								}
								//echo '<br/>End of PrevBillPrincipalAmount Processing...';
								$this->strTrace .=  '<tr><td>End of PrevBillPrincipalAmount Processing...</td></tr>';
								//Process PrevPrincipal Arrears as we follow LIFO
								$SubPaidPrincipal = 0;			
								//echo '<br/><br/>PrevPrincipalDue Processing...';
								$this->strTrace .=  '<tr><td>PrevPrincipalDue Processing...</td></tr>';
								if($PaymentReceived > 0) 
								{
									//echo '<br/>Remaining payment ' . $PaymentReceived . ' is applied to Prev Principal Arrars' . $PrevPrincipalDue;
									$this->strTrace .=  '<tr><td>Remaining payment ' . $PaymentReceived . ' is applied to Prev Principal Arrars' . $PrevPrincipalDue . '</td></tr>';
									//echo '<br/>PrevPrincipalDue  : ' . $PrevPrincipalDue;
									$this->strTrace .=  '<tr><td>PrevPrincipalDue  : ' . $PrevPrincipalDue . '</td></tr>';
									//echo '<br/>SubPaidPrincipal  : ' . $SubPaidPrincipal;
									$this->strTrace .=  '<tr><td>SubPaidPrincipal  : ' . $SubPaidPrincipal . '</td></tr>';
									//echo '<br/>PaymentReceived : ' . $PaymentReceived;
									$this->strTrace .=  '<tr><td>PaymentReceived : ' . $PaymentReceived . '</td></tr>';
									if($PrevPrincipalDue > 0)
									{
										//echo '<br/>PrevPrincipalDue Processing...';
										$this->strTrace .=  '<tr><td>PrevPrincipalDue Processing...</td></tr>';
										if($PaymentReceived > $PrevPrincipalDue )
										{
											$SubPaidPrincipal = $PrevPrincipalDue;
											$PaymentReceived = $PaymentReceived - $PrevPrincipalDue;
											$PrevPrincipalDue = 0;
										}
										else
										{
											$SubPaidPrincipal = $PaymentReceived ;
											$PrevPrincipalDue = $PrevPrincipalDue - $PaymentReceived ;
											$PaymentReceived = 0;
										}	
										//echo '<br/>SubPaidPrincipal  : ' . $SubPaidPrincipal;
										$this->strTrace .=  '<tr><td>SubPaidPrincipal  : ' . $SubPaidPrincipal . '</td></tr>';
										//echo '<br/>PaymentReceived : ' . $PaymentReceived;
										$this->strTrace .=  '<tr><td>PaymentReceived : ' . $PaymentReceived . '</td></tr>';
										//echo '<br/>';
									}									
				
									if ($SubPaidPrincipal>0 && ($PaymentDate > $PrevPeriodEndingDate))
									{
										//echo '<br/>Calc interest on : ' . $SubPaidPrincipal;
							
										$this->strTrace .=  '<tr><td>Calc interest on : ' . $SubPaidPrincipal . '</td></tr>';
										//If payment is late calculate interest on part of principal that is paid by this cheque
										$InterestAmount = $this->GetInterest($UnitID, $SubPaidPrincipal, $PrevPeriodEndingDate, $PaymentDate, $BillCalcType, $InterestRate, $InterestMethod, $InterestTrigger, $RebateMethod, $RebateAmount);
										$CurrentBillInterestAmount = $CurrentBillInterestAmount + $InterestAmount;
										//echo '<br/>CurrentBillInterestAmount : ' . $CurrentBillInterestAmount;
										$this->strTrace .=  '<tr><td>CurrentBillInterestAmount : ' . $CurrentBillInterestAmount . '</td></tr>';
									}
									else
									{
										//echo '<br/>No interest: ';
										$this->strTrace .=  '<tr><td>No interest: </td></tr>';
									}
									
									$PaidPrincipal = $PaidPrincipal + $SubPaidPrincipal;
									//echo '<br/>Paid Principal: ' . $PaidPrincipal;
									$this->strTrace .=  '<tr><td>Paid Principal: ' . $PaidPrincipal . '</td></tr>';
									
								}
								else
								{
									//echo '<br/>No payment for PrevPrincipalDue Processing...';
									$this->strTrace .=  '<tr><td>No payment for PrevPrincipalDue Processing...</td></tr>';
									
								}
								//echo '<br/>End of PrevPrincipalDue Processing...';
								$this->strTrace .=  '<tr><td>End of PrevPrincipalDue Processing... </td></tr>';
								//Now process PrevPrincipal Arrears																		
							}
				
							//if payment is more than all obligations. Apply it as credit
							//echo '<br/><br/>PaymentReceived: ' . $PaymentReceived;
							$this->strTrace .=  '<tr><td>PaymentReceived: ' . $PaymentReceived . '</td></tr>';
							//echo '<br/>Credit Amount: ' . $CreditAmount;
							$this->strTrace .=  '<tr><td>Credit Amount: ' . $CreditAmount . '</td></tr>';
							if($PaymentReceived > 0)
							{
								//echo '<br/>Processing credit... ';
								$this->strTrace .=  '<tr><td>Processing credit... </td></tr>';
								$CreditAmount = $CreditAmount + $PaymentReceived;
								//echo '<br/>Credit Amount: ' . $CreditAmount;
								$this->strTrace .=  '<tr><td>Credit Amount: ' . $CreditAmount . '</td></tr>';
							}										
							//echo '<br/>';
						}
					}
					else
					{
						//echo '<br/>No amount to process' ;				
						$this->strTrace .=  '<tr><td>No amount to process . </td></tr>';
					}				
				}//for each
			}//if($resultCheck <> '')
			
			if ($this->ShowDebugTrace == 1)
			{
					echo '<br/>Processed cheque payments: ' . $iTotalPayments;
			}
			$this->strTrace .=  '<tr><td>Processed cheque payments: ' . $iTotalPayments . '</td></tr><tr></tr></br>---------------------------<tr></br></tr>';
			//echo '<br/><br/>';
	
			//If Principal is due after processing all payments made in last period
			$CurrentPeriod_CreditAmount  = 0;
		
			if($BillSubTotal < 0)
			{
				$CurrentPeriod_CreditAmount = $BillSubTotal * -1;
			}
			
			if ($this->ShowDebugTrace == 1)
			{				
				echo "<BR>PrevBillPrincipalAmount" . $PrevBillPrincipalAmount;
			}
			if ($PrevBillPrincipalAmount > 0 && $dateDiff < 0)
			{
				if($CurrentPeriod_CreditAmount > 0 && $CurrentPeriod_CreditAmount > $PrevBillPrincipalAmount)
				{
					if ($this->ShowDebugTrace == 1)
					{				
						echo '<br/>CurrentPerid Credit Amount ' . $CurrentPeriod_CreditAmount . ' is more than ' . $PrevBillPrincipalAmount . ' so interest not calculated on PrevBillPrincipalAmount' ;	
					}
					$this->strTrace .=  '<tr><td>CurrentPerid Credit Amount ' . $CurrentPeriod_CreditAmount . ' is more than ' . $PrevBillPrincipalAmount . ' so interest not calculated on PrevBillPrincipalAmount </td></tr>';
					$CurrentPeriod_CreditAmount = $CurrentPeriod_CreditAmount - $PrevBillPrincipalAmount;
				}
				else
				{
					$InterestableAmount = $PrevBillPrincipalAmount;
					if($CurrentPeriod_CreditAmount > 0 && $CurrentPeriod_CreditAmount < $PrevBillPrincipalAmount)
					{
						$InterestableAmount  =  $PrevBillPrincipalAmount - $CurrentPeriod_CreditAmount;
						$this->strTrace .=  '<tr><td>PrevBillPrincipalAmount reduced by Subtotal credit :' . $CurrentPeriod_CreditAmount . '</td></tr>';
						
					}
					//echo '<br/>Calculate interest on unpaid PrevBillPrincipalAmount :' . $PrevBillPrincipalAmount ;				
					$this->strTrace .=  '<tr><td>Calculate interest on unpaid PrevBillPrincipalAmount :' . $PrevBillPrincipalAmount . '</td></tr>';
					//Calculate interest on remaining Bill Principal Arrears from Dues Date to end of period after all payments are applied
					$InterestAmount = $this->GetInterest($UnitID, $InterestableAmount, $PrevPeriodBeginingDate, $PrevPeriodEndingDate, $BillCalcType, $InterestRate, $InterestMethod, $InterestTrigger, $RebateMethod, $RebateAmount);
					//$InterestAmount = $this->GetInterest($UnitID, $PrevBillPrincipalAmount, $PrevBillDueDate, $sBillDate, $BillCalcType, $InterestRate, $InterestMethod, $InterestTrigger, $RebateMethod, $RebateAmount);
					$CurrentBillInterestAmount = $CurrentBillInterestAmount + $InterestAmount;
					//echo '<br/>CurrentBillInterestAmount : ' . $CurrentBillInterestAmount;
					$this->strTrace .=  '<tr><td>CurrentBillInterestAmount : ' . $CurrentBillInterestAmount . '</td></tr>';
				}
			}
			if($PrevBillPrincipalAmount < 0)
			{
				$PrevPrincipalDue = $PrevPrincipalDue + $PrevBillPrincipalAmount;				
				if ($this->ShowDebugTrace == 1)
				{				
					echo "<BR>Credit of PrevBillPrincipalAmount " . $PrevBillPrincipalAmount . " added to PrevPrincipalDue" . $PrevPrincipalDue;
				}
				$PrevBillPrincipalAmount = 0;
			}
			
			if ($PrevPrincipalDue > 0 && $dateDiff < 0)
			{
				if ($this->ShowDebugTrace == 1)
				{				
					echo "<BR>PrevPrincipalDue" . $PrevPrincipalDue;
				}
				if($CurrentPeriod_CreditAmount > 0 && $CurrentPeriod_CreditAmount > $PrevPrincipalDue)
				{
					if ($this->ShowDebugTrace == 1)
					{				
						echo '<br/>22CurrentPerid Credit Amount ' . $CurrentPeriod_CreditAmount . ' is more than PrevPrincipalDue ' . $PrevPrincipalDue . ' so interest not calculated on PrevPrincipalDue' ;
					}
					$this->strTrace .=  '<tr><td>CurrentPerid Credit Amount ' . $CurrentPeriod_CreditAmount . ' is more than PrevPrincipalDue ' . $PrevPrincipalDue . ' so interest not calculated on PrevPrincipalDue </td></tr>';
					$CurrentPeriod_CreditAmount = $CurrentPeriod_CreditAmount - $PrevPrincipalDue;
				}
				else
				{			
					$InterestableAmount = $PrevPrincipalDue;
					if($CurrentPeriod_CreditAmount > 0 && $CurrentPeriod_CreditAmount < $PrevPrincipalDue)
					{
						$InterestableAmount  =  $PrevPrincipalDue - $CurrentPeriod_CreditAmount;
						$this->strTrace .=  '<tr><td>PrevPrincipalDue reduced by Subtotal credit :' . $CurrentPeriod_CreditAmount . '</td></tr>';
						if ($this->ShowDebugTrace == 1)
						{				
							echo '<BR>PrevPrincipalDue reduced by Subtotal credit :' . $CurrentPeriod_CreditAmount . '</BR>';
						}
					}
					if ($this->ShowDebugTrace == 1)
					{				
						echo '<br/><br/>Calculate interest on unpaid PrevPrincipalDue :' . $InterestableAmount ;				
					}
					$this->strTrace .=  '<tr><td>Calculate interest on unpaid PrevPrincipalDue :' . $InterestableAmount . '</td></tr>';
					//Calculate interest on remaining PrevPrincipalArrears for whole billing period, after all payments are applied
					
					//$intCalcBeginingDate = $this->GetDateByOffset($PrevPeriodBeginingDate, -1);
					
					$InterestAmount = $this->GetInterest($UnitID, $InterestableAmount, $PrevPeriodBeginingDate, $PrevPeriodEndingDate, $BillCalcType, $InterestRate, $InterestMethod, $InterestTrigger, $RebateMethod, $RebateAmount);
					$CurrentBillInterestAmount = $CurrentBillInterestAmount + $InterestAmount;
					//echo '<br/><br/>CurrentBillInterestAmount : ' . $CurrentBillInterestAmount;
					$this->strTrace .=  '<tr><td>CurrentBillInterestAmount : ' . $CurrentBillInterestAmount . '</td></tr>';
				}
			}
			
			if ($this->ShowDebugTrace == 1)
			{
				echo '<br/><br/>Total CurrentBillInterestAmount : ' . $CurrentBillInterestAmount;
			}
			$this->strTrace .=  '<tr><td>Total CurrentBillInterestAmount : ' . $CurrentBillInterestAmount . '</td></tr>';
			if($RebateMethod == 2)	//Flat amount
			{
				//echo '<br/>Rebate Method : Flat';
				$this->strTrace .=  '<tr><td>Rebate Method : Flat</td></tr>';
				//echo '<br/>Rebate Amount : '. $RebateAmount;
				$this->strTrace .=  '<tr><td>Rebate Amount : '. $RebateAmount . '</td></tr>';
				if($CurrentBillInterestAmount > $RebateAmount)
				{
				//echo '<br/>InterestAmount Before rebate : ' . $CurrentBillInterestAmount;
				$this->strTrace .=  '<tr><td>InterestAmount Before rebate : ' . $CurrentBillInterestAmount . '</td></tr>';
				//echo '<br/>RebateAmount : ' . $RebateAmount;
				$this->strTrace .=  '<tr><td>RebateAmount : ' . $RebateAmount . '</td></tr>';
					$CurrentBillInterestAmount = $CurrentBillInterestAmount - $RebateAmount;
				//echo '<br/>InterestAmount after rebate: ' . $CurrentBillInterestAmount;
				$this->strTrace .=  '<tr><td>InterestAmount after rebate: ' . $CurrentBillInterestAmount . '</td></tr>';
				}
				else
				{
					$CurrentBillInterestAmount = 0;
				}
			}
		
		
		$BillTax = 0;
		$IGST = 0;
		$CGST = 0;
		$SGST = 0;
		$CESS = 0;
		//If INTEREST_ON_PRINCIPLE_DUE is taxable, then reversal credit of Interest would be considered for tax refund
		$this->CalculateGST($UnitID, $BillDate, $TaxableLedgerTotal, $CurrentBillInterestAmount, $InterestOnArrearsReversalCharge, $IGST, $CGST, $SGST,$CESS);
		

			if($InterestOnArrearsReversalCharge <> 0)
			{
				if ($this->ShowDebugTrace == 1)
				{
					echo '<br>CurrentBillInterestAmount  : ' . $CurrentBillInterestAmount ;
					echo '<br>InterestOnArrearsReversalCharge : ' . $InterestOnArrearsReversalCharge;
				}
				$this->strTrace .=  '<tr><td>InterestOnArrearsReversalCharge : ' . $InterestOnArrearsReversalCharge . '</td></tr>';
				$CurrentBillInterestAmount = $CurrentBillInterestAmount + $InterestOnArrearsReversalCharge;
				if ($this->ShowDebugTrace == 1)
				{
					echo '<br>Updated CurrentBillInterestAmount  : ' . $CurrentBillInterestAmount ;
				}
			}
	
			//echo '<br/><br/>Final CurrentBillInterestAmount : ' . $CurrentBillInterestAmount;
			$this->strTrace .=  '<tr><td>Final CurrentBillInterestAmount : ' . $CurrentBillInterestAmount . '</td></tr>';
			//echo '<br/>PaidPrincipal : ' . $PaidPrincipal;
			$this->strTrace .=  '<tr><td>PaidPrincipal : ' . $PaidPrincipal . '</td></tr>';
			//echo '<br/>PaidInterest : ' . $PaidInterest;
			$this->strTrace .=  '<tr><td>PaidInterest : ' . $PaidInterest . '</td></tr>';
			//echo '<br/>Payment Credit after processing Dues : ' . $CreditAmount;
			$this->strTrace .=  '<tr><td>Payment Credit after processing Dues : ' . $CreditAmount . '</td></tr>';
			//echo '<br/>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount;
			$this->strTrace .=  '<tr><td>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount . '</td></tr>';
			//echo '<br/>PrevPrincipalDue : ' . $PrevPrincipalDue;
			$this->strTrace .=  '<tr><td>PrevPrincipalDue : ' . $PrevPrincipalDue . '</td></tr>';
			//echo '<br/>PrevInterestDue : ' . $PrevInterestDue;
			$this->strTrace .=  '<tr><td>PrevInterestDue : ' . $PrevInterestDue . '</td></tr>';
			

		$CurrentBillAmount = $BillSubTotal + $CurrentBillInterestAmount + $BillTax + $IGST + $CGST + $SGST + $CESS ;
		//echo "issuppl:".$this->IsSupplementaryBill();
		/*if(!$this->IsSupplementaryBill())
		{*/
		//Total = Current Bill Amount + Principal Due + InterestDue
		//echo '<br/>PrevPrincipalDue : ' . $PrevPrincipalDue;
		$this->strTrace .=  '<tr><td>PrevPrincipalDue : ' . $PrevPrincipalDue . '</td></tr>';
		$PrevPrincipalDue = $PrevPrincipalDue + $PrevBillPrincipalAmount;
		//echo '<br/>PrevPrincipalDue : ' . $PrevPrincipalDue;
		$this->strTrace .=  '<tr><td>PrevPrincipalDue : ' . $PrevPrincipalDue . '</td></tr>';
		$PrevPrincipalDue = $PrevPrincipalDue - $CreditAmount;
		//echo '<br/>PrevPrincipalDue : ' . $PrevPrincipalDue;
		$this->strTrace .=  '<tr><td>PrevPrincipalDue : ' . $PrevPrincipalDue . '</td></tr>';

		$TotalBillPayable = $CurrentBillAmount + $PrevPrincipalDue + $PrevInterestDue;
		$PrevDues = $PrevPrincipalDue + $PrevInterestDue;

		if ($this->ShowDebugTrace == 1)
		{
			echo '<br/>New month BillSubTotal : ' . $BillSubTotal;
			//echo '<br/>ReversalCredits : ' . $ReversalCredits;
			//echo '<br/>BillTax : ' . $BillTax;
			echo '<br/>CurrentBillInterestAmount : ' . $CurrentBillInterestAmount;
			echo '<br/>CurrentBillAmount : ' . $CurrentBillAmount;
			echo '<br/>PrevPrincipalDue : ' . $PrevPrincipalDue;
			echo '<br/>PrevInterestDue : ' . $PrevInterestDue;
			echo '<br/>PrevDues : ' . $PrevDues;
			echo '<br/>TotalBillPayable : ' . $TotalBillPayable;
		}

		$LateDays = 0;
		$InsertQuery = "INSERT INTO `billdetails`(`UnitID`, `PeriodID`, `BillRegisterID`, `BillNumber`,
		`ModifiedFlag`, `PrevPrincipalArrears`, `PrevInterestArrears`, `PrevBillPrincipal`, 
		`PrevBillInterest`, `PaymentReceived`, `PaidPrincipal`, `PaidInterest`,
		`BillSubTotal`, `AdjustmentCredit`, `BillTax`,`IGST`,`CGST`,`SGST`,`CESS`,`BillInterest`, `LateDays`, 
		`CurrentBillAmount`,`PrincipalArrears`, `InterestArrears`, `TotalBillPayable`,`Note`,`BillType`) VALUES ("
			. $UnitID.",".$PeriodID."," . $BillRegisterID . ", " . $BillNo . ", '0'," 
			. $this->getRoundValue($PrevPrincipalDue_) .","
			. $this->getRoundValue($PrevInterestDue_).","
			. $this->getRoundValue($PrevBillPrincipal_) . ","
			. $this->getRoundValue($PrevBillInterest_) .","
			. $PaymentReceived_.','.$PaidPrincipal.','.$PaidInterest.','
			. $this->getRoundValue($BillSubTotal).",0,". $BillTax.",".$IGST.",".$CGST.",".$SGST.",".$CESS.","
			. $this->getRoundValue($CurrentBillInterestAmount).",".$LateDays.','
			. $this->getRoundValue($CurrentBillAmount).","
			. $this->getRoundValue($PrevPrincipalDue).","
			. $this->getRoundValue($PrevInterestDue).","
			. $this->getRoundValue($TotalBillPayable).","
			. "'" . $BillNote. "',"
			. $this->IsSupplementaryBill().")";

			if ($this->ShowDebugTrace == 1)
			{
				echo '<br/>InsertQuery : ' . $InsertQuery;
			}
			$this->strTrace .=  '<tr><td>InsertQuery : ' . $InsertQuery . '</td></tr>';
			$BillDetailID = $this->m_dbConn->insert($InsertQuery);
			if ($this->ShowDebugTrace == 1)
			{
				echo '<br/>BillDetailID : ' . $BillDetailID;
			}
			$this->strTrace .=  '<tr><td>BillDetailID : ' . $BillDetailID . '</td></tr>';
			$this->SetVoucher($UnitID, $PeriodID, $BillDetailID, $_REQUEST['bill_date'], $BillMaster, $AdditionalBillingHeads);			
			
			$this->UpdateAdjustmentWithBillDetails($UnitID, $PeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate, $BillDetailID);
			return $BillDetailID;
	}

	private function getRoundValue($amount)
	{
		$roundAmount = 0;
		//$roundAmount = round($amount * 2)/2; //(Eg. 1 to 1.24 = 1, 1.25 to 1.74 = 1.5, 1.75 to 1.99 = 2)
		$roundAmount = round($amount); //(Eg. 1 to 1.49 = 1, 1.50 to 1.99 = 2)
		return $roundAmount;
	}
	
	
	public function GetNumberOfMonthsDuesDate($DueDate, $PaymentDate)
	{
		//echo '<br/>DueDate : ' . $DueDate;
		$this->strTrace .=  '<tr><td>DueDate : ' . $DueDate . '</td></tr>';
		//echo '<br/>PaymentDate : ' . $PaymentDate;
		$this->strTrace .=  '<tr><td>PaymentDate : ' . $PaymentDate . '</td></tr>';
		
		$gmtTimezone = new DateTimeZone('GMT');
		$datetime1 = new DateTime($DueDate, $gmtTimezone);
		//$datetime1 = $datetime1->modify('-1 day');
		
		$datetime2 = new DateTime($PaymentDate, $gmtTimezone);
		$datetime2 = $datetime2->modify('+1 day');
		
		$interval = $datetime1->diff($datetime2);
		$diff_d = $interval->format('%d');
		//echo '<br/>diff in days: ' . $diff_d;
		$this->strTrace .=  '<tr><td>diff in days: ' . $diff_d . '</td></tr>';
		
		$diff_m = $interval->format('%m');
		//echo '<br/>diff in months: ' . $diff_m;
		$this->strTrace .=  '<tr><td>diff in months: ' . $diff_m . '</td></tr>';
		
		$diff_y = $interval->format('%y');
		//echo '<br/>diff in years: ' . $diff_y;
		$this->strTrace .=  '<tr><td>diff in years: ' . $diff_y . '</td></tr>';
		
		if($diff_y > 0)
		{
			$diff_m = ($diff_y * 12) + $diff_m;			
		}
		
		//if payment is made few days late but less than a month, charge one month interest
		if($diff_m <= 0)
		{
			if($diff_d > 0)
			{
				$diff_m = 1;
			}			
		}
		else if($diff_d > 0)
		{
			$diff_m =  $diff_m + 1;
		}
		
		return $diff_m;	
	}
	
	public function GetNumberOfDaysAfterDuesDate($DueDate, $PaymentDate)
	{
		//echo '<br/>DueDate : ' . $DueDate;
		$this->strTrace .=  '<tr><td>DueDate : ' . $DueDate . '</td></tr>';
		
		//echo '<br/>PaymentDate : ' . $PaymentDate;
		$this->strTrace .=  '<tr><td>PaymentDate : ' . $PaymentDate. '</td></tr>';
		
		$datetime1 = new DateTime($DueDate);
		$datetime1 = $datetime1->modify('-1 day');
		$datetime2 = new DateTime($PaymentDate);
		$interval = $datetime1->diff($datetime2);
		$diff = $interval->format('%R%a days');
		//echo '<br/>diff : ' . $diff;
		$this->strTrace .=  '<tr><td>diff : ' . $diff . '</td></tr>';
		
		return $diff;	
	}
	public function GetDateByOffset($myDate, $Offset)
	{
		//echo '<br/>myDate : ' . $myDate;
		$this->strTrace .=  '<tr><td>myDate : ' . $myDate. '</td></tr>';
		
		//echo '<br/>Offset : ' . $Offset;
		$this->strTrace .=  '<tr><td>Offset : ' . $Offset. '</td></tr>';
		$datetime1 = new DateTime($myDate);
		$newDate = $datetime1->modify($Offset . ' day');
//		echo '<br/>Offetdate : ' . $newDate->format('y-m-d');

		return $newDate->format('Y-m-d');	
	}

	public function GetInterest($UnitID, $PrevPrincipalDue, $DueDate, $PaymentDate, $BillCalcType, $InterestRate, $InterestMethod, $InterestTrigger, $RebateMethod, $RebateAmount)
	{
		//echo '<br/>Inside GetInterest : ' . $PrevPrincipalDue;
		$this->strTrace .=  '<tr><td>Inside GetInterest : ' . $PrevPrincipalDue. '</td></tr>';
		
		//echo '<br/>PrevPrincipalDue : ' . $PrevPrincipalDue;
		$this->strTrace .=  '<tr><td>PrevPrincipalDue : ' . $PrevPrincipalDue. '</td></tr>';
		
	//	echo '<br/>DueDate : ' . $DueDate;
		$this->strTrace .=  '<tr><td>DueDate : ' . $DueDate. '</td></tr>';
		
		//echo '<br/>PaymentDate : ' . $PaymentDate;
		$this->strTrace .=  '<tr><td>PaymentDate : ' . $PaymentDate. '</td></tr>';
		
		//echo '<br/>BillCalcType : ' . $BillCalcType;
		$this->strTrace .=  '<tr><td>BillCalcType : ' . $BillCalcType . '</td></tr>';
		
		$InterestAmount = 0;
				
//		$BillSubTotal = -1;
			
		//echo '<br/>InterestRate : ' . $InterestRate;
		$this->strTrace .=  '<tr><td>InterestRate : ' . $InterestRate. '</td></tr>';
		
		//echo '<br/>InterestMethod : ' . $InterestMethod;
		$this->strTrace .=  '<tr><td>InterestMethod : ' . $InterestMethod . '</td></tr>';
		
		//echo '<br/>InterestTrigger : ' . $InterestTrigger;
		$this->strTrace .=  '<tr><td>InterestTrigger : ' . $InterestTrigger. '</td></tr>';
		
		//echo '<br/>RebateMethod : ' . $RebateMethod;
		$this->strTrace .=  '<tr><td>RebateMethod : ' . $RebateMethod. '</td></tr>';
		
		//echo '<br/>RebateAmount : ' . $RebateAmount;
		$this->strTrace .=  '<tr><td>RebateAmount : ' . $RebateAmount. '</td></tr>';

		if($PrevPrincipalDue > $InterestTrigger)
		{
/*			echo '<br/>PrevPrincipalDue : ' . $PrevPrincipalDue;
			if($RebateMethod == 3)	//DueAmountWaiver
			{
				echo '<br/>RebateMethod DueAmountWaiver for Amt : ' . $RebateAmount;					
				$PrevPrincipalDue = $PrevPrincipalDue - $RebateAmount;					
			}
			echo '<br/>PrevPrincipalDue after DueAmountWaiver : ' . $PrevPrincipalDue;
			*/
			if ($InterestMethod == 2)	//Full Month
			{					
				//echo '<br/>InterestMethod : Monthly';
				$this->strTrace .=  '<tr><td>InterestMethod : Monthly</td></tr>';
				
				$InterestAmount =  ($PrevPrincipalDue * $InterestRate/100 )/12;
				//echo '<br/>Monthly Interest Amt : ' . $InterestAmount;					
				$this->strTrace .=  '<tr><td>Monthly Interest Amt : ' . $InterestAmount. '</td></tr>';
				
				$NumberOfMonths = $this->GetNumberOfMonthsDuesDate($DueDate, $PaymentDate);
				//echo '<br/>Interest due for months: ' . $NumberOfMonths;					
				$this->strTrace .=  '<tr><td>Due Date Given: ' . $DueDate.'</td></tr>';
				$this->strTrace .=  '<tr><td>PaymentDate: ' . $PaymentDate.'</td></tr>';
				$this->strTrace .=  '<tr><td>Interest due for months: ' . $NumberOfMonths.'</td></tr>';
				
				$InterestAmount = $InterestAmount * $NumberOfMonths;
				//echo '<br/>Total Interest Amt : ' . $InterestAmount;					
				$this->strTrace .=  '<tr><td>Total Interest Amt : ' . $InterestAmount.'</td></tr>';
			}
			else if ($InterestMethod == 1)	//DelayAfterDueDate
			{
				//echo '<br/>DelayAfterDueDate';
				$this->strTrace .=  '<tr><td>DelayAfterDueDate</td></tr>';
				
				$DelayDays = $this->GetNumberOfDaysAfterDuesDate($DueDate, $PaymentDate);
				//echo '<br/>DelayDays : ' . $DelayDays;
				$this->strTrace .=  '<tr><td>DelayDays : ' . $DelayDays . '</td></tr>';
				
				$InterestAmount =  ($PrevPrincipalDue * $InterestRate/100 ) * $DelayDays/365;					
				//echo '<br/>InterestAmount : ' . $InterestAmount;
				$this->strTrace .=  '<tr><td>InterestAmount : ' . $InterestAmount.'</td></tr>';
			}
			else
			{
				//Error handling	
			}			
		}

		//echo '<br/>InterestAmount : ' . $InterestAmount;		
		$this->strTrace .=  '<tr><td>InterestAmount : ' . $InterestAmount.'</td></tr>';
		return $InterestAmount;			
	}

	public function GetAdjustment ($UnitID, $PeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate)
	{
		//echo '<br/>PrevPeriodBeginingDate : ' . $PrevPeriodBeginingDate;
		//echo '<br/>PrevPeriodEndingDate : ' . $PrevPeriodEndingDate;
		$sqlAdj = "select * from reversal_credits where date >= '". $PrevPeriodBeginingDate . "' AND date <= '" . $PrevPeriodEndingDate . "' AND UnitID = " . $UnitID . " AND Status = 1 AND BillType ='" . $this->IsSupplementaryBill()."' " ;
		//echo '<br/>GetPaymentsReceived:sqlAdj : ' . $sqlAdj;
		$resultAdj = $this->m_dbConn->select($sqlAdj);
//		return $sqlAdj ;
		
		$AdjAmount = 0;
		if($resultAdj <> '')
		{
			foreach($resultAdj as $k => $v)
			{
				$PaymentDate = 0;
				$PaymentReceived = 0;
				$iTotalPayments = $iTotalPayments + 1;
				if($resultAdj[$k]['Amount'] <> 0)
				{
					$PaymentDate  = $resultAdj[$k]['Date'];					
					$Amount = $resultAdj[$k]['Amount'];
					$AdjAmount = $AdjAmount + $Amount;
					echo "<BR>Ledger " . $resultAdj[$k]['LedgerID'] . " Amount " . $Amount ;
				}
			}
		}
		return $AdjAmount;
	}
	

	public function GetReversalCredits($UnitID, $PeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate)
	{
		//$this->ShowDebugTrace = 1;
		$AdditionalBillingHeads = array();
				
//		$sqlAdj = "select rc.ID, rc.Date, rc.VoucherID, rc.UnitId, rc.Amount, rc.Comments, ledgertbl.Taxable, rc.LedgerID  from reversal_credits as rc JOIN ledger as ledgertbl ON rc.ledgerid = ledgertbl.id where rc.date >= '". $PrevPeriodBeginingDate . "' AND rc.date <= '" . $PrevPeriodEndingDate . "' AND rc.UnitID = " . $UnitID . " AND rc.BillType ='" . $this->IsSupplementaryBill()."' AND rc.STATUS = 1 " ;		
		$sqlAdj = "select rc.Amount as AccountHeadAmount, rc.Comments, ledgertbl.Taxable, rc.LedgerID as AccountHeadID  from reversal_credits as rc JOIN ledger as ledgertbl ON rc.ledgerid = ledgertbl.id where rc.date >= '". $PrevPeriodBeginingDate . "' AND rc.date <= '" . $PrevPeriodEndingDate . "' AND rc.UnitID = " . $UnitID . " AND rc.BillType ='" . $this->IsSupplementaryBill()."' AND rc.STATUS = 1 " ;		
		
		$resultAdj = $this->m_dbConn->select($sqlAdj);
		return $resultAdj;
/*		
		$ReversalCredits = 0;
		if($resultAdj <> '')
		{
			if ($this->ShowDebugTrace == 1)
			{
				echo '<br/>Reversal_charge :CHECK : ' . $sqlAdj;
				print_r($resultAdj);
			}
			foreach($resultAdj as $k => $v)
			{
				$PaymentDate = 0;
				$PaymentReceived = 0;
				$iTotalPayments = $iTotalPayments + 1;
				if($resultAdj[$k]['Amount'] <> 0)
				{
					$LedgerID = $resultAdj[$k]['LedgerID'];
//					$PaymentDate  = $resultAdj[$k]['Date'];					
					$Amount = $resultAdj[$k]['Amount'];
					$Taxable = $resultAdj[$k]['Taxable'];
					$note =  $Amount . ":" . $resultAdj[$k]['Comments'];
					if ($this->ShowDebugTrace == 1)
					{
						echo "<BR>Comments " . $note . "<BR>";
					}
					if($BillNote == "")
					{
						$BillNote = $note;
						
					}
					else
					{
						$BillNote = $BillNote . ":: " . $note;
					}
					//echo "<BR>BillNote " . $BillNote . "<BR>";
					$ReversalCredits = $ReversalCredits + $Amount;
					$this->strTrace .=  '<tr><td>LedgerID: <' . $LedgerID . '> Amount <' . $Amount . '></td></tr>';

					$tempArray = array("AccountHeadID" => $LedgerID, "AccountHeadAmount" => $Amount, "Taxable" => $Taxable);							
												
					array_push($AdditionalBillingHeads, $tempArray);
				}
			}
		}		
		else
		{
			if ($this->ShowDebugTrace == 1)
			{
				echo "<BR>No AdditionalBillingHeads<BR>";
			}
		}
		if ($this->ShowDebugTrace == 1)
		{
			echo "<BR>AdditionalBillingHeads<BR>";
			print_r($AdditionalBillingHeads);
		}
		return $AdditionalBillingHeads;*/
	}
	
	
	public function UpdateAdjustmentWithBillDetails ($UnitID, $PeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate, $BillDetailID)
	{
		//echo '<br/>PrevPeriodBeginingDate : ' . $PrevPeriodBeginingDate;
		//echo '<br/>PrevPeriodEndingDate : ' . $PrevPeriodEndingDate;
		$sqlAdj = "UPDATE reversal_credits SET PeriodID = " . $PeriodID . " where date >= '". $PrevPeriodBeginingDate . "' AND date <= '" . $PrevPeriodEndingDate . "' AND UnitID = " . $UnitID . " AND Status = 1 AND BillType ='" . $this->IsSupplementaryBill()."' " ;
		if($this->ShowDebugTrace == 1)
		{
			echo '<br/>Reversal_credit update :sqlAdj : ' . $sqlAdj;
		}
		$resultAdj = $this->m_dbConn->update($sqlAdj);
		return $resultAdj;
	}
		
	public function GetPaymentsReceived ($UnitID, $PeriodID, $PrevPeriodID, $BeginingDate, $EndingDate)
	{
	
		//echo '<br/>PrevPeriodBeginingDate : ' . $PrevPeriodBeginingDate;
		$sqlCheck = "select * from chequeentrydetails where voucherdate >= '". $BeginingDate . "' AND voucherdate <= '" . $EndingDate . "' AND PaidBy = " . $UnitID;
		//echo '<br/>GetPaymentsReceived:SqlCheck : ' . $sqlCheck;
		$resultCheck = $this->m_dbConn->select($sqlCheck);
		return $resultCheck ;
	}



	public function GetBillMaster($UnitID, $PeriodID)
	{
		if($this->ShowDebugTrace == 1)
		{
			echo "<BR>Inside GetBillMaster <BR>";		
		}//print_r($AdditionalBillingHeads );
		
		$sqlPeriod = "Select BeginingDate, EndingDate from period where ID = '" . $PeriodID . "'";
		$resultPeriod = $this->m_dbConn->select($sqlPeriod);
		
		$beginDate = $resultPeriod[0]['BeginingDate'];
		$endDate = $resultPeriod[0]['EndingDate'];		
		
		/****************************************************************************
		//Get the Account Heads as per the period selected
		*****************************************************************************/
		
		if($this->IsSupplementaryBill() == 1)
		{
			$sqlFetch = "select master.UnitID, master.AccountHeadID, master.AccountHeadAmount, master.BeginPeriod, master.EndPeriod, master.BillType, ledgertbl.taxable from unitbillmaster as master JOIN ledger as ledgertbl ON master.AccountHeadID = ledgertbl.id where master.UnitID = '" . $UnitID . "' and ledgertbl.supplementary_bill = '1' and master.AccountHeadID != '" . INTEREST_ON_PRINCIPLE_DUE . "' and master.BeginPeriod <= '" . $beginDate . "' and master.BillType='".$this->IsSupplementaryBill()."' ORDER BY UnitID, AccountHeadID, EndPeriod ASC";
		}
		else
		{
			$sqlFetch = "select master.UnitID, master.AccountHeadID, master.AccountHeadAmount, master.BeginPeriod, master.EndPeriod,master.BillType, ledgertbl.taxable from unitbillmaster as master JOIN ledger as ledgertbl ON master.AccountHeadID = ledgertbl.id where master.UnitID = '" . $UnitID . "' and ledgertbl.show_in_bill = '1' and master.AccountHeadID != '" . INTEREST_ON_PRINCIPLE_DUE . "' and master.BeginPeriod <= '" . $beginDate . "' and master.BillType='".$this->IsSupplementaryBill()."' ORDER BY UnitID, AccountHeadID, EndPeriod ASC";
		}
		

		if($this->ShowDebugTrace == 1)
		{
			echo "<BR>sqlFetch : " . $sqlFetch;
		}
		$resultFetch = $this->m_dbConn->select($sqlFetch);
		if($this->ShowDebugTrace == 1)
		{
			//var_dump($resultFetch);
		}
			
		if($resultFetch <> "")
		{
			//echo "<BR>BillMaster";	
			//print_r($resultFetch);
		}
		return $resultFetch;
	}
	
	public function GetBillSubTotal_Plain($UnitID, $PeriodID, $resultFetch, $AdditionalBillingHeads, &$TaxableLedgerTotal, &$InterestOnArrearsReversalCharge, &$BillNote)
	{
		//$this->ShowDebugTrace = 1;
		$BillSubTotal = 0;//-1;
		$BillTax = 0;
		$IGST = 0;
		$CGST = 0;
		$SGST = 0;
		$CESS = 0;
		
		$sqlPeriod = "Select BeginingDate, EndingDate from period where ID = '" . $PeriodID . "'";
		$resultPeriod = $this->m_dbConn->select($sqlPeriod);
		
		$beginDate = $resultPeriod[0]['BeginingDate'];
		$endDate = $resultPeriod[0]['EndingDate'];
		
		
		/****************************************************************************
		//Get the Account Heads as per the period selected
		*****************************************************************************/
		if($this->ShowDebugTrace == 1)
		{
//				echo "<BR>GetBillSubtotal Matser :<BR>". var_dump($resultFetch) . "<BR>";
		}
			
		if($resultFetch <> "")
		{
			//var_dump(resultFetch);
			$BillSubTotal = 0;
			
			$unit_inprocess = 0;
			$ledger_inprocess = 0;
			$add = false;
			for($iCnt = 0; $iCnt < sizeof($resultFetch); $iCnt++)
			{
				$dateDiff = $this->m_objUtility->getDateDiff($resultFetch[$iCnt]['EndPeriod'], $endDate);
				
				if($unit_inprocess <> $resultFetch[$iCnt]['UnitID'])
				{
					$ledger_inprocess = 0;
				}
				
				if($dateDiff >= 0 && $ledger_inprocess <> $resultFetch[$iCnt]['AccountHeadID'])
				{
					$unit_inprocess = $resultFetch[$iCnt]['UnitID'];
					$ledger_inprocess = $resultFetch[$iCnt]['AccountHeadID'];
					$BillSubTotal += $resultFetch[$iCnt]['AccountHeadAmount'];

					if($this->ShowDebugTrace == 1)
					{
						echo "<BR>unit_inprocess :". $unit_inprocess . "  ledger_inprocess :". $ledger_inprocess . "  Amount :". $resultFetch[$iCnt]['AccountHeadAmount'] . "  taxable? :". $resultFetch[$iCnt]['taxable'] . "<BR>";
					}					
					if($resultFetch[$iCnt]['taxable'] == 1)
					{
						$TaxableLedgerTotal += $resultFetch[$iCnt]['AccountHeadAmount'];
					}
					if($this->ShowDebugTrace == 1)
					{
						echo "<BR>TaxableLedgerTotal :". $TaxableLedgerTotal . "<BR>";
					}
					//echo "head:".$resultFetch[$iCnt]['AccountHeadAmount'];
				}
			}
		}

		if($this->ShowDebugTrace == 1)
		{
			echo "<BR>BillSubTotal :" . $BillSubTotal ;
			echo "<BR>TaxableLedgerTotal :" . $TaxableLedgerTotal ;
		}
		if($AdditionalBillingHeads <> '')
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Processing AdditionalBillingHeads <BR>";		
//				print_r($AdditionalBillingHeads );
			}
			$InterestOnPrincipalLegderID = INTEREST_ON_PRINCIPLE_DUE; //pending take this id from default table
			foreach($AdditionalBillingHeads as $k=>$v)
			{
				$this->strTrace .=  '<tr><td>ID : ' . $AdditionalBillingHeads[$k]['AccountHeadID'] . ' Amount : ' . $AdditionalBillingHeads[$k]['AccountHeadAmount']. ' Taxable : ' . $AdditionalBillingHeads[$k]['Taxable'] . '</td></tr>';

				if($this->ShowDebugTrace == 1)
				{
					echo '<br>ID : ' . $AdditionalBillingHeads[$k]['AccountHeadID'] . ' Amount : ' . $AdditionalBillingHeads[$k]['AccountHeadAmount']. ' Taxable : ' . $AdditionalBillingHeads[$k]['Taxable'];
				}

				if($AdditionalBillingHeads[$k]['AccountHeadAmount'] <> '' || $AdditionalBillingHeads[$k]['AccountHeadAmount'] <> 0.00)
				{
					if( $AdditionalBillingHeads[$k]['AccountHeadID'] == $InterestOnPrincipalLegderID)
					{
						//Process Interest credit
						$InterestOnArrearsReversalCharge = $InterestOnArrearsReversalCharge + $AdditionalBillingHeads[$k]['AccountHeadAmount'];
						if ($this->ShowDebugTrace == 1)
						{
							echo "<BR>Interest reversal credit : AccountHeadID :". $AdditionalBillingHeads[$k]['AccountHeadID'] . "  Amount :". $AdditionalBillingHeads[$k]['AccountHeadAmount'] . "  taxable? :". $AdditionalBillingHeads[$k]['Taxable'];
						}
						
					}
					else
					{
						$BillSubTotal += $AdditionalBillingHeads[$k]['AccountHeadAmount'];
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>reversal credit : AccountHeadID :". $AdditionalBillingHeads[$k]['AccountHeadID'] . "  Amount :". $AdditionalBillingHeads[$k]['AccountHeadAmount'] . "  taxable? :". $AdditionalBillingHeads[$k]['Taxable'] ;
						}
						if($AdditionalBillingHeads[$k]['Taxable'] == 1)
						{
							$TaxableLedgerTotal += $AdditionalBillingHeads[$k]['AccountHeadAmount'];
							if($this->ShowDebugTrace == 1)
							{
								echo "<BR>TaxableLedgerTotal :" . $TaxableLedgerTotal;
							}
						}
					}
					$note =  $AdditionalBillingHeads[$k]['AccountHeadAmount'] . ":" . $AdditionalBillingHeads[$k]['Comments'];
					if ($this->ShowDebugTrace == 1)
					{
						echo "<BR>Comments " . $note . "<BR>";
					}
					if($BillNote == "")
					{
						$BillNote = $note;
						
					}
					else
					{
						$BillNote = $BillNote . ":: " . $note;
					}
					//echo "<BR>BillNote " . $BillNote . "<BR>";										
				}
			} //for each
		}
		return $BillSubTotal;
	}
	
	public function CalculateGST($UnitID, $BillDate, $TaxableLedgerTotal, $CurrentBillInterestAmount, $InterestOnArrearsReversalCharge, &$IGST,&$CGST,&$SGST,&$CESS)
	{
		$this->ShowDebugTrace = 1;
		//echo "<BR>Inside CalculateGST TaxableLedgerTotal : " . $TaxableLedgerTotal;
		$IGST = 0;
		$CGST = 0;
		$SGST = 0;
		$CESS = 0;

		//echo "<BR>BillSubTotal with RC :" . $BillSubTotal ;
		$societyInfo = $this->m_objUtility->GetSocietyInformation($_SESSION['society_id']);
		//$ServiceTaxRate = $societyInfo['service_tax_rate'];
		$IgstServiceTaxRate = $societyInfo['igst_tax_rate'];
		$CgstServiceTaxRate = $societyInfo['cgst_tax_rate'];
		$SgstServiceTaxRate = $societyInfo['sgst_tax_rate'];
		$CessServiceTaxRate = $societyInfo['cess_tax_rate'];
		$ApplyServiceTax = $societyInfo['apply_service_tax'];
		$ApplyServiceTaxOnInterest = $societyInfo['apply_GST_on_Interest'];
		$ApplyGSTAboveThreshold = $societyInfo['apply_GST_above_Threshold'];
		$ServiceTaxLimit = $societyInfo['service_tax_threshold'];
		
		$BillDate = getDBFormatDate($this->m_dbConn->escapeString($_REQUEST['bill_date']));
		$iDateDiff = $this->m_objUtility->getDateDiff($BillDate, GST_START_DATE);
		
		if($iDateDiff < 0)
		{
			$ApplyServiceTax = 0;
			if($this->ShowDebugTrace == 1)
			{
				echo "Bill date " . $BillDate . " isi before GST start date ". GST_START_DATE . " So reseting ApplyServiceTax :". $ApplyServiceTax . "<BR>";
			}
		}
		if($this->ShowDebugTrace == 1)
		{
			echo "<BR>ApplyServiceTax ". $ApplyServiceTax . "<BR>";
		}

			
		$IsUnitCommercial = 0; //Pending : Then threshold is not applicable

		if($ApplyServiceTax == 1)
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Service tax limit : " . $ServiceTaxLimit . "<BR>";
			}
			if($IsUnitCommercial == 1)
			{
				 $ServiceTaxLimit = 0;
				
				if($this->ShowDebugTrace == 1)
				{
					echo "<BR>Since commercial Unit, resetting Service tax limit : " . $ServiceTaxLimit . "<BR>";
				}
			}
				
			if($ApplyServiceTaxOnInterest == 1)
			{
				$TaxableLedgerTotal += $CurrentBillInterestAmount;
			}
	
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>ApplyServiceTaxOnInterest is set, CurrentBillInterestAmount: " . $CurrentBillInterestAmount . " added to TaxableLedgerTotal<BR>";
				echo "<BR>TaxableLedgerTotal: " . $TaxableLedgerTotal ;
				echo "<BR>Service tax limit : " . $ServiceTaxLimit . "<BR>";
			}
/*			if($TaxableLedgerTotal < 0)//Pending : -ve tax needs to be calculated?
			{
				if($this->ShowDebugTrace == 1)
				{
					echo "<BR>TaxableLedgerTotal is : " . $TaxableLedgerTotal . " so setting it to 0<BR>"; 	
				}
				$TaxableLedgerTotal = 0;
			}
*/
			if($TaxableLedgerTotal >= $ServiceTaxLimit)
			{
				$TaxableAmount = $TaxableLedgerTotal;
				if($this->ShowDebugTrace == 1)
				{
					echo "<BR>TaxableAmount: " . $TaxableAmount . "<BR>";
				}
	
				if($ApplyGSTAboveThreshold == 1)
				{
					$TaxableAmount -= $ServiceTaxLimit ;
					
					if($this->ShowDebugTrace == 1)
					{
						echo "<BR>ApplyGSTAboveThreshold is set, TaxableAmount - ServiceTaxLimit : " . $TaxableAmount . " <BR>";
					}
				}
	
				//$this->strTrace .=  '<tr><td>TaxableAmount : ' . $TaxableAmount . '</td></tr>';
				$IGST = $this->getRoundValue($TaxableAmount * $IgstServiceTaxRate / 100);
				$CGST = $this->getRoundValue($TaxableAmount * $CgstServiceTaxRate / 100);
				$SGST = $this->getRoundValue($TaxableAmount * $SgstServiceTaxRate / 100);
				$CESS = $this->getRoundValue($TaxableAmount * $CessServiceTaxRate / 100);
				
				if($this->ShowDebugTrace == 1)
				{
					echo "<BR>CGST " . $CGST ;
					echo "<BR>SGST " . $SGST ;
				}
				echo "<BR>";
			}
			
			echo "<BR>InterestOnArrearsReversalCharge " . $InterestOnArrearsReversalCharge ;
			if($InterestOnArrearsReversalCharge <> 0)
			{
				echo "<BR>IsLedgerTaxable: " . $this->IsLedgerTaxable(INTEREST_ON_PRINCIPLE_DUE);
				if($this->IsLedgerTaxable(INTEREST_ON_PRINCIPLE_DUE))
				{
					if($this->ShowDebugTrace == 1)
					{
						echo "<BR>Ledger is Taxable" . INTEREST_ON_PRINCIPLE_DUE ;
					}
					$CGST = $CGST + $this->getRoundValue($InterestOnArrearsReversalCharge * $CgstServiceTaxRate / 100);
					$SGST = $SGST + $this->getRoundValue($InterestOnArrearsReversalCharge * $SgstServiceTaxRate / 100);
					if($this->ShowDebugTrace == 1)
					{
						echo "<BR>CGST " . $CGST ;
						echo "<BR>SGST " . $SGST ;
					}
				}
			}
		}
	}

	public function CalculateTax($UnitID, $Amount)
	{
		return 0;	
	}
	
	public function getNotes($SocietyID, $PeriodID, $supplementary_bill)
	{
		//Check if Notes for current period are set for society
		$sqlSelect = "Select Notes from billregister where SocietyID = '" . $SocietyID . "' and PeriodID = '" . $PeriodID . "' and BillType='"
		.$supplementary_bill."' Order By ID DESC";
		$result = $this->m_dbConn->select($sqlSelect);
		$Notes = '';
		if($result <> '')
		{
			$Notes = $result[0]['Notes'];
			$Notes = str_replace('<br />', "", $Notes); 
		}
		else
		{
			//Get notes for Previous Period
			$PrevPeriodID = $this->getPreviousPeriodData($PeriodID);
			$sqlSelect = "Select Notes from billregister where SocietyID = '" . $SocietyID . "' and PeriodID = '" . $PrevPeriodID . "' and BillType='".$supplementary_bill."' Order By ID DESC";
			$result = $this->m_dbConn->select($sqlSelect);
			if($result <> '')
			{
				$Notes = $result[0]['Notes'];
				$Notes = str_replace('<br />', "", $Notes);
			}
		}
		echo $Notes;
		$this->strTrace .=  '<tr><td>'.$Notes.'</td></tr>';
	}

	public function setNotes($Note, $PeriodID, $supplementary_bill)
	{
		//Check if Notes for current period are set for society
		
		$sqlCheck = "Select count(PeriodID) as Cnt from billregister where PeriodID = '" . $PeriodID . "'  and BillType='".$supplementary_bill."'";
		$sqlCount = $this->m_dbConn->select($sqlCheck);
		
		$msg = '';
		
		if($sqlCount[0]['Cnt'] == 0)
		{
			$msg = "No bills have been generated for the selected Period. Kindly generate bills and then retry to Update Notes.";
		}
		else
		{
			$sqlUpdate = "Update billregister SET `Notes` = '" . $this->m_dbConn->escapeString($Note) . "' WHERE PeriodID = '" . $PeriodID . "'  and BillType='".$supplementary_bill."'";
			$resultUpdate = $this->m_dbConn->update($sqlUpdate);
			
			$msg = "Notes updated successfully";
		}
		
		echo $msg;
	}
	
	public function getFontSize($SocietyID, $PeriodID, $supplementary_bill)
	{
		//Check if Notes for current period are set for society
		 $sqlSelect = "Select font_size from billregister where SocietyID = '" . $SocietyID . "' and PeriodID = '" . $PeriodID . "' and BillType='"
		.$supplementary_bill."' Order By ID DESC";
		$result = $this->m_dbConn->select($sqlSelect);
		$FontSize = '';
		if($result <> '')
		{
			$FontSize = $result[0]['font_size'];
			//$Notes = str_replace('<br />', "", $Notes); 
		}
		else
		{
			//Get notes for Previous Period
			$PrevPeriodID = $this->getPreviousPeriodData($PeriodID);
			$sqlSelect = "Select font_size from billregister where SocietyID = '" . $SocietyID . "' and PeriodID = '" . $PrevPeriodID . "' and BillType='".$supplementary_bill."' Order By ID DESC";
			$result = $this->m_dbConn->select($sqlSelect);
			if($result <> '')
			{
				$FontSize = $result[0]['font_size'];
				//$Notes = str_replace('<br />', "", $Notes);
			}
		}
		echo $FontSize;
		//$this->strTrace .=  '<tr><td>'.$Notes.'</td></tr>';
	}
	public function setFont($Font, $PeriodID, $supplementary_bill)
	{
		//Check if Notes for current period are set for society
		
		$sqlCheck = "Select count(PeriodID) as Cnt from billregister where PeriodID = '" . $PeriodID . "'  and BillType='".$supplementary_bill."'";
		$sqlCount = $this->m_dbConn->select($sqlCheck);
		
		$msg = '';
		
		if($sqlCount[0]['Cnt'] == 0)
		{
			$msg = "No bills have been generated for the selected Period. Kindly generate bills and then retry to Update Font SIze.";
		}
		else
		{
			$sqlUpdate = "Update billregister SET `font_size` = '" . $Font . "' WHERE PeriodID = '" . $PeriodID . "'  and BillType='".$supplementary_bill."'";
			$resultUpdate = $this->m_dbConn->update($sqlUpdate);
			
			$msg = "Font Size updated successfully";
		}
		
		echo $msg;
	}
	
	public function getBillAndDueDate($periodID, $societyID, $BillType,$bShowDueDate = true)
	{
		$sql = "Select `BillDate`, `DueDate` from `billregister` where `PeriodID` = '" . $periodID . "' and `SocietyID` = '" . $societyID . "' and `BillType` = '" . $BillType . "' ORDER BY ID DESC";
		$res = $this->m_dbConn->select($sql);
		$aryDate = array();
		if($res <> '')
		{ 
			$aryDate['BillDate'] = getDisplayFormatDate($res[0]['BillDate']);
			$aryDate['DueDate'] = getDisplayFormatDate($res[0]['DueDate']);
		}
		else
		{
			$sql = "select BeginingDate, EndingDate	from period where ID = '" . $periodID . "'";
			$res = $this->m_dbConn->select($sql);
			$aryDate['BillDate'] = getDisplayFormatDate($res[0]['BeginingDate']);
			$aryDate['DueDate'] = getDisplayFormatDate($res[0]['EndingDate']);
			
			
		}
		
		if($BillType == "1" && $bShowDueDate == false)
		{
			$aryDate['DueDate'] = getDisplayFormatDate(PHP_MAX_DATE);
		}
		
				return $aryDate;
	}

	function getCollectionOfDataToDisplay($society_id, $wing_id, $unit_id, $period_id,$isBillSummary = false, $SupplementaryBill)
	{		
		$sqlPeriod = "SELECT max(id),PeriodID,BillDate,DueDate FROM `billregister` where `PeriodID` = '".$period_id."' and BillType='".$SupplementaryBill."' ";
		$resultPeriod = $this->m_dbConn->select($sqlPeriod);
		
		$prevPeriod = $this->getPreviousPeriodData($period_id);
		
		//$sqlPymtQuery = "Select Type, YearID, PrevPeriodID, Status, BeginingDate, EndingDate from period where ID=" . $prevPeriod;
		//		echo '<br/>sqlPymtQuery : ' . $sqlPymtQuery;
		//$Prevresult = $this->m_dbConn->select($sqlPymtQuery);
		
		$sqlPymtQuery2 = "Select Type, YearID, PrevPeriodID, Status, BeginingDate, EndingDate from period where ID=" . $period_id;
		$Prevresult2 = $this->m_dbConn->select($sqlPymtQuery2);
				
		$detailsArray = array();		
		
		if($unit_id == 0)
		{
			$memberIDS = $this->m_objUtility->getMemberIDs($resultPeriod[0]['BillDate']);
		//$sql1 = 'select unittbl.unit_id, unittbl.unit_no, member.owner_name, bill.ID as refNo, bill.PrincipalArrears,bill.AdjustmentCredit, bill.BillNumber, bill.InterestArrears, bill.BillInterest, bill.TotalBillPayable,bill.PrevInterestArrears,bill.BillSubTotal,bill.PeriodID,bill.BillRegisterID from billdetails as bill JOIN unit as unittbl on bill.UnitID = unittbl.unit_id JOIN wing as wingtbl on unittbl.wing_id = wingtbl.wing_id JOIN society as societytbl on unittbl.society_id = societytbl.society_id JOIN member_main AS member ON member.unit = unittbl.unit_id where bill.PeriodID = "' . $period_id . '" and societytbl.society_id = "' . $society_id . '" and member.society_id = "' . $society_id . '" and member.member_id IN (SELECT  member.`member_id` FROM (select  DISTINCT(unit),`member_id` from `member_main` where ownership_date <="'.$resultPeriod[0]['BillDate'].'"  ORDER BY ownership_date desc) as member_id ) '; 
			$sql1 = 'select unittbl.unit_id, unittbl.unit_no, member.owner_name, bill.ID as refNo, bill.PrincipalArrears,bill.AdjustmentCredit, bill.BillNumber, bill.InterestArrears, bill.BillInterest, bill.TotalBillPayable,bill.PrevInterestArrears,bill.BillSubTotal,bill.PeriodID,bill.BillRegisterID from billdetails as bill JOIN unit as unittbl on bill.UnitID = unittbl.unit_id JOIN wing as wingtbl on unittbl.wing_id = wingtbl.wing_id JOIN society as societytbl on unittbl.society_id = societytbl.society_id JOIN member_main AS member ON member.unit = unittbl.unit_id where bill.PeriodID = "' . $period_id . '" and societytbl.society_id = "' . $society_id . '" and member.society_id = "' . $society_id . '" and bill.BillType="'.$SupplementaryBill.'" and member.member_id IN ('.$memberIDS.') '; 
		}
		else
		{
		$sql1 = 'select unittbl.unit_id, unittbl.unit_no, member.owner_name, bill.ID as refNo, bill.PrincipalArrears,bill.AdjustmentCredit, bill.BillNumber, bill.InterestArrears, bill.BillInterest, bill.TotalBillPayable,bill.PrevInterestArrears,bill.BillSubTotal,bill.PeriodID,bill.BillRegisterID from billdetails as bill JOIN unit as unittbl on bill.UnitID = unittbl.unit_id JOIN wing as wingtbl on unittbl.wing_id = wingtbl.wing_id JOIN society as societytbl on unittbl.society_id = societytbl.society_id JOIN member_main AS member ON member.unit = unittbl.unit_id where bill.PeriodID = "' . $period_id . '" and societytbl.society_id = "' . $society_id . '" and member.society_id = "' . $society_id . '" and bill.BillType="'.$SupplementaryBill.'" '; 
		}
		
		if($wing_id <> 0)
		{
			$sql1 .= ' and unittbl.wing_id = "' . $wing_id . '"';
		}		
		if($unit_id <> 0)
		{
			$sql1 .= ' and unittbl.unit_id = "' . $unit_id . '"';
		}
		$sql1 .= '   Group BY unittbl.unit_id  ORDER BY unittbl.sort_order ASC';
				
		$res = $this->m_dbConn->select($sql1);
		
		$sql = 'SELECT `id`, `ledger_name` FROM `ledger` where `society_id` = '.$society_id . ' OR `id` = '.INTEREST_ON_PRINCIPLE_DUE;
		if($SupplementaryBill == 1)
		{
			$sql .= ' and supplementary_bill = "1"';
		}
		else
		{
			$sql .= ' and show_in_bill = "1"';	
		}
		
		$result = $this->m_dbConn->select($sql);
		
		//$LedgerShowInBill = array("id"=> INTEREST_ON_PRINCIPLE_DUE, "ledger_name"=> $result23[0]['ledger_name']);
		//array_push($result, $LedgerShowInBill);
		/*foreach($result as $key)
		{
			
			if($key['id'] <> INTEREST_ON_PRINCIPLE_DUE)
			{
				$sql23 = 'SELECT `ledger_name` FROM `ledger` where `id` = '.INTEREST_ON_PRINCIPLE_DUE .' and `society_id` = '.$society_id;
				$result23= $this->m_dbConn->select($sql23);
				$LedgerShowInBill = array("id"=> INTEREST_ON_PRINCIPLE_DUE, "ledger_name"=> $result23[0]['ledger_name']);
				array_push($result, $LedgerShowInBill);
				
			}
		}*/
			
		//print_r($result);	
		for($i = 0; $i <  sizeof($res);$i++)
		{	
			$details = array();
			$details[''] = '';
			$details['UNIT NO'] = $res[$i]['unit_no'];
			$details['MemberName'] = $res[$i]['owner_name'];
			if($isBillSummary == true)
			{
				$sqlFinyr = "SELECT periodtable.YearID,periodtable.Type,yeartable.YearDescription FROM `period` as periodtable join `year` as yeartable on periodtable.YearID = yeartable. YearID where periodtable.ID = '".$res[$i]['PeriodID']."' ";
				$resFinyr = $this->m_dbConn->select($sqlFinyr);
				
				if($resFinyr <> "")
				{
					$details['Fin. Year'] = $resFinyr[0]['YearDescription'];
					$details['BILL FOR'] = $resFinyr[0]['Type'];		
				}
							
				$sqlBillDate = "SELECT DATE_FORMAT(BillDate, '%d-%m-%Y') as BillDate FROM `billregister` where `ID`= '".$res[$i]['BillRegisterID']."' ";
				$resBillDate = $this->m_dbConn->select($sqlBillDate);
				
				if($resBillDate <> "")
				{
					$details['BillDate'] = $resBillDate[0]['BillDate'];		
				}
			}
			$details['BILL NO'] = $res[$i]['BillNumber'];
			for($j = 0; $j < sizeof($result); ++$j)
			{
				if($result[$j]['id'] == INTEREST_ON_PRINCIPLE_DUE)
				{
					 $sql = 'SELECT `Credit` from `voucher` where `To` = '.$result[$j]['id']. ' and    `RefNo` = ' . $res[$i]['refNo'] . ' and `RefTableID` = ' . TABLE_BILLREGISTER .' and Credit < 0 ';
				}
				else
				{
					$sql = 'SELECT `Credit` from `voucher` where `To` = '.$result[$j]['id']. ' and    `RefNo` = ' . $res[$i]['refNo'] . ' and `RefTableID` = ' . TABLE_BILLREGISTER ;	
				}
			//$result = $this->m_dbConn->select($sql);	
				//$sql = 'SELECT `AccountHeadAmount` FROM `unitbillmaster` where `AccountHeadID` = '.$result[$j]['id']. ' and `UnitID` = '.$res[$i]['unit_id'];
				$res1 = $this->m_dbConn->select($sql);
				//echo "<br>Credit:".$res1[0]['Credit'] ;
					if(sizeof($res1) > 0)
					{
						$Credit=0;
					//$details[$result[$j]['ledger_name']] = $res1[0]['AccountHeadAmount'];
						if($result[$j]['id'] <> INTEREST_ON_PRINCIPLE_DUE)
						{
							
							for($m = 0; $m < sizeof($res1); $m++)
							{
								$Credit += $res1[$m]['Credit'];
							
							}
						}
						else
						{
							$Credit = $res1[0]['Credit'];
						}
						$details[$result[$j]['ledger_name']]= $Credit;
					}
					else
					{
							$details[$result[$j]['ledger_name']] = '0.00';
					}
			}
			
			$details['BillSubTotal'] = $res[$i]['BillSubTotal'];
			if($SupplementaryBill == 0)
			{
				$details['AdjustmentCredit'] = $res[$i]['AdjustmentCredit'];
				$details['InterestOnArrears'] = $res[$i]['BillInterest'];
				$details['PreviousPrincipalArrears'] = $res[$i]['PrincipalArrears'];
				$details['PreviousInterestArrears'] = $res[$i]['InterestArrears'];
			}
			$details['Payable'] = $details['BillSubTotal'] + $details['AdjustmentCredit']  + $details['InterestOnArrears'] + $details['PreviousPrincipalArrears'] +$details['PreviousInterestArrears'];   
			
			$paymentReceived = $this->GetPaymentsReceived($res[$i]['unit_id'], $period_id, $prevPeriod, $Prevresult2[0]['BeginingDate'], $Prevresult2[0]['EndingDate']);
			//echo "size";
			$iPaidDetails = '';
			$iPaidTotal = 0;
			for($iPaidCnt = 0 ; $iPaidCnt < sizeof($paymentReceived) ; $iPaidCnt++)
			{
				$iPaidDetails .= ($iPaidCnt + 1) . ' - ' . $paymentReceived[$iPaidCnt] . '<br />';
				$iPaidTotal +=  $paymentReceived[$iPaidCnt]['Amount'];
			}
			
			//$details['Paid'] = $paymentReceived;
			if($isBillSummary == true)
			{
				$details['Paid'] = $iPaidTotal;
				$details['Balance'] = $details['Payable'] - $iPaidTotal;
				if($details['Balance'] < 0 )
				{
					$details['Balance'] = number_format($details['Balance'],2).' Cr'; 		
				}
				else
				{
					$details['Balance'] = number_format($details['Balance'],2).' Dr'; 		
				}
				$details['Payable'] = $details['Payable'];
			}
			
			array_push($detailsArray, $details);
			//$details['Payable'] = number_format($details['Payable'],2);
		}
	
		if($isBillSummary == false)
		{
			$DisplayTotal = array();
			$checkZero = array();
			for($i = 0; $i <  sizeof($detailsArray); $i++)
			{
				foreach($detailsArray[$i] as $key => $value)
				{
					if($key <> '' && $key <> 'UNIT NO' && $key <> 'MemberName' &&  $key <> 'Fin. Year' &&  $key <> 'BILL FOR' && $key <> 'BILL NO' && $key <> 'BillDate' && $key <> 'Balance')
					{
					   if(!isset($DisplayTotal[$key]))
						{
							$DisplayTotal[$key] = 0;
						}
						$DisplayTotal[$key] += $value;
						 if(!isset($checkZero[$key]))
						{
							$checkZero[$key] = false;
						}
						
						if($value == '0.00' && ($checkZero[$key] <> true || $checkZero[$key] == '')) 
						{
							$checkZero[$key] = false;
						}
						else
						{
							$checkZero[$key] = true;
						}
					
					}
					else
					{
						$DisplayTotal[$key] = '';	
						$checkZero[$key] = true;
					}
				  
				}
			}
			array_push($detailsArray,$DisplayTotal);
			$detailsArray = $this->unsetZeroKeysFromArray($detailsArray , $checkZero);
		}
		return $detailsArray;	
	}	
	
	public function getCurrentAndPreviousPeriod()
	{
		$response = '<option value="0">No Period To Display</option>';
		
		$curDate = $this->display_pg->curdate();
		
		$sql = "Select periodtbl.ID, periodtbl.Type, yeartbl.YearDescription, periodtbl.PrevPeriodID from period as periodtbl JOIN year as yeartbl on periodtbl.YearID = yeartbl.YearID JOIN society as societytbl on periodtbl.Billing_cycle = societytbl.bill_cycle where '" . $curDate . "' >= periodtbl.BeginingDate and '" . $curDate . "' <= periodtbl.EndingDate and societytbl.society_id = '" . $_SESSION['society_id'] . "'";
		//echo $sql;
		$result = $this->m_dbConn->select($sql);
		
		if($result <> '')
		{
			$response = '<option value="' . $result[0]['ID'] . '">' . $result[0]['Type'] . ' : ' . $result[0]['YearDescription'] .  '</option>';		
			
			if($result[0]['PrevPeriodID'] > 0)
			{
				$sql = "Select periodtbl.ID, periodtbl.Type, yeartbl.YearDescription from period as periodtbl JOIN year as yeartbl on periodtbl.YearID = yeartbl.YearID where periodtbl.ID = '" . $result[0]['PrevPeriodID'] . "'";
				//echo $sql;
				$result = $this->m_dbConn->select($sql);
				
				if($result <> '')
				{
					$response .= '<option value="' . $result[0]['ID'] . '">' . $result[0]['Type'] . ' : ' . $result[0]['YearDescription'] .  '</option>';			
				}
			}
		}
		return ($response);
	}
	
	//when single bill is updated this code is called  
	//Bill edit
	public function BillDetailsUpdate($Detail,$UnitID,$PeriodID,$CurrentBillInterestAmount,$InterestArrears,$PrincipalArrears,$AdjustmentCredit, $SupplementaryBill)
	{
		//$this->ShowDebugTrace = 1;
		$BillSubTotal = 0;
		$TaxableLedgerTotal = 0;
		$ChangeMsg = "";
		$CreditTransactionType = "`Credit`";

		$sqlCheck = "select `ID`,`BillInterest`,`PrincipalArrears`,`InterestArrears`,`BillRegisterID`,`LatestChangeID`, `AdjustmentCredit` from billdetails where UnitID = '" . $UnitID . "' and PeriodID = '" . $PeriodID . "' and BillType='".$SupplementaryBill ."'" ;
		$resultCheck = $this->m_dbConn->select($sqlCheck);
		if($resultCheck == "")
		{
			echo "<BR>Bill Not found for UnitID = '" . $UnitID . "' and PeriodID = '" . $PeriodID . "' and BillType='".$SupplementaryBill ."'";
			return -1;
		}
		$BillDetailRefNo =  $resultCheck[0]['ID'];
		//Get Asset Entry from voucher table
		$sqlCheck3= "select `id`, `Date` as 'BillDate', VoucherNo from `voucher` where `By`= '" . $UnitID ."' and `RefNo` ='" . $resultCheck[0]['ID'] . "' and`RefTableID` = '" . TABLE_BILLREGISTER . "' ";

		$resultCheck3 = $this->m_dbConn->select($sqlCheck3);
		echo "<BR>Voucher No : ". $resultCheck3[0]['VoucherNo'] . "<BR>";
//		if($this->ShowDebugTrace == 1)
		{
			echo "<BR>resultCheck3 BillDetailID : " . $resultCheck[0]['ID'] . "<BR>";
			print_r($resultCheck3);
			echo "<BR>Detail<BR>";
			print_r($Detail);
		}

		foreach($Detail as $key => $val)
		{
			$HeaderAmount = $Detail[$key]['Amt'];
			$HeaderName = $Detail[$key]['Head'];
			$VoucherID = $Detail[$key]['VoucherID'];
			$HeadOldValue = $Detail[$key]['HeadOldValue'];
			$Taxable = $Detail[$key]['Taxable'];
			if($this->ShowDebugTrace == 1)
			{
				echo "<br>Ledger:".$Detail[$key]['Head']."::Amt:".$Detail[$key]['Amt'] . " ::OldAmt:".$Detail[$key]['HeadOldValue'] . "<br>";
			}
//			if($resultCheck <> "")
			{
				//New ledger added into the bill
				if($VoucherID == 0 && $HeadOldValue == 0)
				{
					//First see if record exist
					$this->FindVoucherIdAndUpdateOrInsertInVoucherAndRegisterTable_Credit($HeaderName, $resultCheck[0]['ID'], TABLE_BILLREGISTER, $HeaderAmount);

					$LedgerDetails=$this->m_objUtility->GetLedgerDetails($HeaderName);
					echo "<BR>Header :";
					print_r($HeaderName);
					echo "<BR>Ledger Details";
					print_r($LedgerDetails);
					$Taxable = $LedgerDetails[$HeaderName]['General']['taxable'];
					//if($this->ShowDebugTrace == 1)
					{
						echo "<BR>Header" . $HeaderName . " is taxable : " . $Taxable . "<BR>";
					}
				}
				else
				{
					if($HeaderName == INTEREST_ON_PRINCIPLE_DUE && $HeaderAmount >= 0)	//Pending: Review this condition. When does it meet?
					{
						$sqlUpdate2 = "Delete from `voucher` where `To`= '" . $HeaderName ."' and `RefNo` ='" . $resultCheck[0]['ID'] . "' and `RefTableID` = '" . TABLE_BILLREGISTER . "' and `id`= '" . $VoucherID ."'";
						
						
						$resultUpdate2 = $this->m_dbConn->delete($sqlUpdate2);
						$sqlUpdate3 = "Delete from `incomeregister` where `VoucherID`= '" . $VoucherID ."' and `LedgerID` ='" . $HeaderName . "' ";
						$resultUpdate3 = $this->m_dbConn->delete($sqlUpdate3);
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>Deleted Interest ledger " . $HeaderName . " Amount : " . $HeaderAmount . "<BR>";
							echo $sqlUpdate2 . '<br>';
							echo $sqlUpdate3 . '<br>';
						}
					}
					else
					{
						
						if($HeadOldValue <> $HeaderAmount)
						{
							if($this->ShowDebugTrace == 1)
							{
								echo "<BR>Updating " . $HeaderName . " Amount : " . $HeaderAmount . "  :: BillSubTotal " . $BillSubTotal . " and TaxableLedgerTotal : " . $TaxableLedgerTotal . "<BR>";
							}
							$this->UpdateElseInsertVoucherAndRegister_Credit($VoucherID, $HeaderName, $resultCheck[0]['ID'], TABLE_BILLREGISTER, $HeaderAmount);
						}
						else
						{
							if($this->ShowDebugTrace == 1)
							{
								echo "<BR>Amount not changed. DB not updated <BR>";
							}							
						}
							
					}
				}
				//Update BillSubTotal
				$BillSubTotal += $HeaderAmount;

				if($Taxable == 1)
				{
					$TaxableLedgerTotal += $HeaderAmount;
				}
				if($this->ShowDebugTrace == 1)
				{
					echo "<BR>BillSubTotal " . $BillSubTotal . " and TaxableLedgerTotal : " . $TaxableLedgerTotal . "<BR>";
				}						
			}
		}
		
		//Update Interest On Arrears
		if($this->ShowDebugTrace == 1)
		{
			echo "<BR>Updating INTEREST_ON_PRINCIPLE_DUE Amount :". $CurrentBillInterestAmount ."<BR>";
		}
	$this->FindVoucherIdAndUpdateOrInsertInVoucherAndRegisterTable_Credit(INTEREST_ON_PRINCIPLE_DUE, $resultCheck[0]['ID'], TABLE_BILLREGISTER, $CurrentBillInterestAmount);
		
		//Update Adjustment Credit/Rebate
		if($this->ShowDebugTrace == 1)
		{
			echo "<BR>Updating ADJUSTMENT_CREDIT Amount :". $AdjustmentCredit ."<BR>";
			echo  "Adjustment Credit New : " . $AdjustmentCredit . "  Old :" .  $resultCheck[0]['AdjustmentCredit'] ;
		}
		
		if($AdjustmentCredit <>  $resultCheck[0]['AdjustmentCredit'])
		{
			//Update only if changed
			$this->FindVoucherIdAndUpdateOrInsertInVoucherAndRegisterTable_Credit(ADJUSTMENT_CREDIT, $resultCheck[0]['ID'], TABLE_BILLREGISTER, $AdjustmentCredit);
		}

		//Update Service Tax		
		//if(SERVICE_TAX > 0)
		$ServiceTax = 0;
		$IGSTRateTax = 0;
		$CGSTRateTax = 0;
		$SGSTRateTax = 0;
		$CESSRateTax = 0;
		
		$societyInfo = $this->m_objUtility->GetSocietyInformation($_SESSION['society_id']);
		$ApplyServiceTax = $societyInfo['apply_service_tax'];
		$ServiceTaxLimit = $societyInfo['service_tax_threshold'];
		$IGSTRate = $societyInfo['igst_tax_rate'];
		$CGSTRate = $societyInfo['cgst_tax_rate'];
		$SGSTRate = $societyInfo['sgst_tax_rate'];
		$CESSRate = $societyInfo['cess_tax_rate'];
		//$ServiceTaxRate = $societyInfo['gstin_no'];

		$iDateDiff = $this->m_objUtility->getDateDiff($resultCheck3[0]['BillDate'], GST_START_DATE);

		if($iDateDiff < 0)
		{
			$ApplyServiceTax = 0;			
		}

		$IGSTAmount = 0;
		$CGSTAmount = 0;
		$SGSTAmount = 0;
		$CESSAmount = 0;

		if( $ApplyServiceTax == 1)
		{
			$ApplyServiceTaxOnInterest = $societyInfo['apply_GST_on_Interest'];
			if($ApplyServiceTaxOnInterest == 1)
			{
				$TaxableLedgerTotal += $CurrentBillInterestAmount;
			}	
	
			$ApplyGSTAboveThreshold = $societyInfo['apply_GST_above_Threshold'];
	
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>ApplyGSTAboveThreshold :" . $ApplyGSTAboveThreshold  . "<BR>";
				echo "<BR>Service tax limit : " . $ServiceTaxLimit . "<BR>";
			}
	
			if($TaxableLedgerTotal < 0)
			{
				$TaxableAmount = $TaxableLedgerTotal;
				
			}
			else if($TaxableLedgerTotal >= $ServiceTaxLimit)
			{
				$TaxableAmount = $TaxableLedgerTotal;
	
				if($ApplyGSTAboveThreshold == 1)
				{
					$TaxableAmount -= $ServiceTaxLimit ;
				}
				//$this->strTrace .=  '<tr><td>TaxableAmount : ' . $TaxableAmount . '</td></tr>';
			}
				
			$IGSTAmount = $this->getRoundValue($TaxableAmount * $IGSTRate / 100);
			$CGSTAmount = $this->getRoundValue($TaxableAmount * $CGSTRate / 100);
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Taxable amount = " . $TaxableAmount . ", so Service tax limit changed to : " . $CGSTAmount . "<BR>";
			}
			//echo $CGSTRateTax;
			$SGSTAmount = $this->getRoundValue($TaxableAmount * $SGSTRate / 100);
			//echo $SGSTRateTax;
			$CESSAmount = $this->getRoundValue($TaxableAmount * $CESSRate / 100);
		}


/*		if(IGST_SERVICE_TAX > 0)
		{
			echo "<BR> test 1<BR>";
			$this->ServiceTaxImplement(IGST_SERVICE_TAX,$IGSTAmount,$resultCheck[0]['ID'],$ApplyServiceTax);
		}
		if(CESS_SERVICE_TAX > 0)
		{
						echo "<BR> test 4<BR>";

			$this->ServiceTaxImplement(CESS_SERVICE_TAX,$CESSAmount,$resultCheck[0]['ID'],$ApplyServiceTax);
		}*/
		if(CGST_SERVICE_TAX > 0)
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>CGST Ledger: " . CGST_SERVICE_TAX . " CGST Amt : " . $CGSTAmount . "<BR>";
			}
			$this->ServiceTaxImplement(CGST_SERVICE_TAX,$CGSTAmount,$resultCheck[0]['ID'],$ApplyServiceTax);
		}
		if(SGST_SERVICE_TAX > 0)
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR> SGST Ledger: " . SGST_SERVICE_TAX . " SGST Amt : " . $SGSTAmount . "<BR>";
			}
			$this->ServiceTaxImplement(SGST_SERVICE_TAX,$SGSTAmount,$resultCheck[0]['ID'],$ApplyServiceTax);
		}


		$AssetTotal = $BillSubTotal + $CurrentBillInterestAmount + $AdjustmentCredit + $IGSTAmount + $CGSTAmount + $SGSTAmount + $CESSAmount;
		$CurrentBillAmount = $AssetTotal;
		$TotalBillPayable = $CurrentBillAmount + $InterestArrears + $PrincipalArrears;

		if($this->ShowDebugTrace == 1)
		{
			echo "<BR>AssetTotal : ". $AssetTotal ;
			echo "<BR>InterestArrears : ". $InterestArrears ;
			echo "<BR>PrincipalArrears : ". $PrincipalArrears ;
			echo "<BR>TotalBillPayable : ". $TotalBillPayable ;
		}
		$this->obj_register->UpdateRegister($UnitID, $resultCheck3[0]['id'], TRANSACTION_DEBIT, $AssetTotal); 
			
		echo "Verify this Asset update query: <BR>" . $sqlUpdate4 = "UPDATE `assetregister` SET `Debit`= '" . $AssetTotal ."' where `VoucherID`= '" . $resultCheck3[0]['id'] ."' and `LedgerID` ='" . $UnitID . "' ";
//		$resultUpdate4 = $this->m_dbConn->update($sqlUpdate4);*/


		$sqlUpdate5 = "UPDATE `voucher` SET `Debit`= '" . $AssetTotal ."' where `By`= '" . $UnitID ."' and `RefNo` ='" . $resultCheck[0]['ID'] . "' and `RefTableID` = '" . TABLE_BILLREGISTER . "' ";
		$resultUpdate5 = $this->m_dbConn->update($sqlUpdate5);
		//echo $sqlbillregister="SELECT `LatestChangeID`  FROM  `billregister` WHERE ID ='" . $resultCheck[0]['BillRegisterID'] . "' ";
		//$PrevChangeID =$this->m_dbConn->select($sqlbillregister);
		$changeLog = new changeLog($this->m_dbConn);
		$ChangeMsg .= "Amt changed for[";
		foreach($Detail as $key => $val)
		{
			if($Detail[$key]['Amt'] <> $Detail[$key]['HeadOldValue'])
			{
				$ChangeMsg .= $Detail[$key]['Head'].":".$Detail[$key]['HeadOldValue']."::".$Detail[$key]['Amt']."||";	
			}
		}
		
		if($CurrentBillInterestAmount <> $resultCheck[0]['BillInterest'])
		{
			$ChangeMsg .= INTEREST_ON_PRINCIPLE_DUE . ":".$resultCheck[0]['BillInterest']."::".$CurrentBillInterestAmount."||";	
		}
		
		if($PrincipalArrears <> $resultCheck[0]['PrincipalArrears'])
		{
			$ChangeMsg .= "PrinArrs:".$resultCheck[0]['PrincipalArrears']."::".$PrincipalArrears."||";	
		}
		
		if($InterestArrears <> $resultCheck[0]['InterestArrears'])
		{
			$ChangeMsg .= "IntArrs:".$resultCheck[0]['InterestArrears']."::".$InterestArrears."||";	
		}
		
		if($AdjustmentCredit <> $resultCheck[0]['AdjustmentCredit'])
		{
			$ChangeMsg .= "AdjCr:".$resultCheck[0]['AdjustmentCredit']."::".$AdjustmentCredit."||";	
		}
		$ChangeMsg .= "]";
		//echo "<br>ChangeMsg:".$ChangeMsg;
		$desc = 'Updated Bill for Unit <' . $UnitID . '> Period <' . $PeriodID . '>  PrevChangeID:'.$resultCheck[0]['LatestChangeID']."<br>".$ChangeMsg;
		echo "<BR>" . $desc . "<BR>";
		$iLatestChangeID = $changeLog->setLog($desc, $_SESSION['login_id'], 'billregister', $resultCheck[0]['ID']);
		//echo $BillRegisterUpdate = "UPDATE `billregister` SET `LatestChangeID`='" . $this->m_dbConn->escapeString($iLatestChangeID). "' WHERE ID ='" . $resultCheck[0]['BillRegisterID'] . "'";
		//$resultBillRegister = $this->m_dbConn->update($BillRegisterUpdate);
		echo $sqlUpdate1 = "UPDATE `billdetails` SET `LatestChangeID`='" . $this->m_dbConn->escapeString($iLatestChangeID) . "',`BillSubTotal`='" . $BillSubTotal . "',`BillInterest`='" . $CurrentBillInterestAmount . "',`CurrentBillAmount`='" . $CurrentBillAmount . "',`PrincipalArrears`='" . $PrincipalArrears . "',`InterestArrears`='" . $InterestArrears . "', `AdjustmentCredit` = '" . $AdjustmentCredit . "', `TotalBillPayable`='" . $TotalBillPayable . "', `BillTax` = '" . $ServiceTax . "', `IGST` = '" . $IGSTAmount . "', `CGST` = '" . $CGSTAmount. "', `SGST` = '" . $SGSTAmount . "', `CESS` = '" . $CESSAmount . "' WHERE ID ='" . $resultCheck[0]['ID'] . "' and UnitID ='" . $UnitID . "' and PeriodID='" . $PeriodID . "' and BillType='".$SupplementaryBill ."'";
		$resultUpdate1 = $this->m_dbConn->update($sqlUpdate1);

		foreach($Detail as $z => $v)
		{
			$HeaderAmount1 = $OldValue1 = 0;
			$HeaderName1 = "";
			$HeaderAmount1 = $Detail[$z]['Amt'];
			$HeaderName1 = $Detail[$z]['Head'];
			$OldValue1 = $Detail[$z]['HeadOldValue'];
			
			//$this->obj_billmaster->update_billmaster($_REQUEST['UnitID'],$HeaderName1,$HeaderAmount1,$_REQUEST['PeriodID'],$_REQUEST['PeriodID'],$_REQUEST['PeriodID'],0);		
		}
/*
		//Apurva's code
		//print_r($_SESSION);
		//print_r($Detail);
		
		$select0 = "select `BeginingDate`, `EndingDate` from `period` where `ID`='".$_REQUEST['PeriodID']."' and `YearID`='".$_SESSION['default_year']."'";
		$select01 = $this->m_dbConn->select($select0);
		
		foreach($Detail as $z => $v)
		{
			$HeaderAmount1 = $Detail[$z]['Amt'];
			$HeaderName1 = $Detail[$z]['Head'];
			
			$select1 = "select * from `unitbillmaster` where `UnitID`='".$_REQUEST['UnitID']."' and `AccountHeadID`='".$HeaderName1."' and `BeginPeriod`='".$select01[0]['BeginingDate']."' and `EndPeriod`='".$select01[0]['EndingDate']."'";
			$select11 = $this->m_dbConn->select($select1);
			
			if($select11 <> '')
			{
				$update1 = "update `unitbillmaster` set `AccountHeadAmount`='".$HeaderAmount1."' where `UnitID`='".$_REQUEST['UnitID']."' and `AccountHeadID`='".$HeaderName1."' and `BeginPeriod`='".$select01[0]['BeginingDate']."' and `EndPeriod`='".$select01[0]['EndingDate']."'";
				$update11 = $this->m_dbConn->update($update1);				
			}
			else
			{
				$insert1 = "insert into `unitbillmaster`(UnitID,CreatedBy,LatestChangeID,AccountHeadID,AccountHeadAmount,BeginPeriod,EndPeriod) values('".$_REQUEST['UnitID']."','".$_SESSION['login_id']."',0,'".$HeaderName1."','".$HeaderAmount1."','".$select01[0]['BeginingDate']."','".$select01[0]['EndingDate']."')";
				$insert11 = $this->m_dbConn->insert($insert1);
			}
		}	
*/
	}
	
	public function getPeriod($PeriodId)
	{
		$sqlPeriod = "Select periodtbl.type, yeartbl.YearDescription from period as periodtbl JOIN year as yeartbl ON periodtbl.YearID = yeartbl.YearID where periodtbl.ID = '" . $PeriodId . "'";
		$sqlResult = $this->m_dbConn->select($sqlPeriod);
		$msg = $sqlResult[0]['type'] . " " . $sqlResult[0]['YearDescription'];
		return $msg;
	}

	//Return values
	//1: Updated
	//2: New record inserted		
	//UpdateVoucherAndRegister_Credit(ADJUSTMENT_CREDIT, $resultCheck[0]['ID'], TABLE_BILLREGISTER);

	public function FindVoucherIdAndUpdateOrInsertInVoucherAndRegisterTable_Credit($LedgerID, $RefNo, $RefTableID, $Amount)
	{	
		//$this->ShowDebugTrace = 1;
		
		if($this->ShowDebugTrace == 1)
		{
			echo "<BR>Inside FindVoucherIdAndUpdateOrInsertInVoucherAndRegisterTable_Credit LedgerID:" .  $LedgerID . "  Amount : " . $Amount . "<BR>" ;
		}
			//For one VoucherNo, there are mutiple records in voucher table with VoucherID for each line of the bill
		//Check if record exist for given Ledger in the Voucher
		$sqlVoucher= "select `id`, `Debit`, `Credit` from `voucher` where `To`= '" . $LedgerID ."' and `RefNo` ='" . $RefNo . "'  and `RefTableID` = '" . $RefTableID . "' ";
		$resultVoucher = $this->m_dbConn->select($sqlVoucher);
		$voucherID = 0;
		if($this->ShowDebugTrace == 1)
		{
			echo "Voucher object <BR>";
			print_r($resultVoucher);
		}
		if($resultVoucher <> '')
		{
			//Record exist in the database
			if($resultVoucher[0]['id'] <> "")
			{
				$voucherID = $resultVoucher[0]['id'];
				echo "<BR>Record found in voucher for  LedgerID: " . $LedgerID . "  at VoucherID: " . $voucherID . "<BR>";
				if($resultVoucher[0]['Credit'] <>  $Amount)
				{
					//Voucher exist and amount is different.. should update
					echo "<BR>Test 1<BR>"; //Tested
					echo "<BR>LedgerID: " . $LedgerID . " Old value " . $resultVoucher[0]['Credit'] . " and new value :".  $Amount . " are diff . existing voucher : " . $voucherID . " record updated<BR>";
					return $this->UpdateElseInsertVoucherAndRegister_Credit($voucherID, $LedgerID, $RefNo, $RefTableID, $Amount);
				}
				else
				{
					echo "<BR>Test 2<BR>"; //Tested
					echo "<BR>LedgerID: " . $LedgerID . " Old and new values are same :".  $Amount . " record not updated<BR>";
					return 0;
				}
			}
			else
			{
					echo "<BR>Test 3<BR>";
			}
			
		}
		else
		{
			echo "<BR>LedgerID: " . $LedgerID . " no prev record exist<BR>";
		}
		if($voucherID == 0 and $Amount == 0)
		{
			//No Record exist in the database and amount is 0
			echo "<BR>Test 4<BR>";
			echo "<BR>voucherID == 0 and Amount == 0<BR>";
			echo "<BR>LedgerID: " . $LedgerID . " new value is :".  $Amount . " and no prev record exist, so not updated<BR>";
			return;
		}
		echo "<BR>Test 5<BR>";
		echo "<BR>LedgerID: " . $LedgerID . " VoucherID : " . $voucherID . " new value is :".  $Amount . "<BR>";
		$this->UpdateElseInsertVoucherAndRegister_Credit($voucherID, $LedgerID, $RefNo, $RefTableID, $Amount);

		return;
	}
	//If voucherID == 0 then new record would be inserted, so if you are not sure if ledger exist in Voucher then call FindVoucherIdAndUpdateOrInsertInVoucherAndRegisterTable_Credit
	public function UpdateElseInsertVoucherAndRegister_Credit($voucherID, $LedgerID, $RefNo, $RefTableID, $Amount)
	{	
		//$this->ShowDebugTrace = 1;
		
		if($this->ShowDebugTrace == 1)
		{
			echo "Inside UpdateElseInsertVoucherAndRegister_Credit voucherID:" .  $voucherID . "  LedgerID:" .  $LedgerID . "  Amount : " . $Amount . "<BR>" ;
		}
		//For one VoucherNo, there are mutiple records in voucher table with VoucherID for each line of the bill
		//Check if record exist for given Ledger in the Voucher
		if($voucherID <> 0)
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Updating VoucherID " . $voucherID . "  LedgerID:" . $LedgerID . " Amount : " . $Amount . "<BR>";
			}
			$sqlUpdate6 = "UPDATE `voucher` SET `Credit`= '" . $Amount ."' where `To`= '" . $LedgerID ."' and `RefNo` ='" . $RefNo . "' and `RefTableID` = '" . $RefTableID . "' and `id`= '" . $voucherID ."'";
			//$sqlUpdate6 = "UPDATE `voucher` SET `Credit`= '" . $Amount ."' where `To`= '" . $LedgerID ."' and `RefNo` ='" . $RefNo . "' and `RefTableID` = '" . $RefTableID . "' ";
			if($this->ShowDebugTrace == 1)
			{
				//echo $sqlUpdate6 ;
			}
			$resultUpdate6 = $this->m_dbConn->update($sqlUpdate6);
			$this->obj_register->UpdateRegister($LedgerID, $voucherID, TRANSACTION_CREDIT, $Amount); 
			return 1;
		}
		else
		{
			
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Inserting LedgerID:" . $LedgerID . " Amount : " . $Amount . "<BR>";
			}
			$sql = 'SELECT max(SrNo) as "M_SrNo", `Date`, `RefNo`,`RefTableID`,`VoucherNo`, `VoucherTypeID` FROM `voucher` WHERE `RefNo` = '. $RefNo .' AND `RefTableID` = "' . $RefTableID . '" ';
			$voucherDetails = $this->m_dbConn->select($sql);
//			$obj_voucher = new voucher($this->m_dbConn);
//			$obj_register = new regiser($this->m_dbConn);
			$voucherID = $this->obj_voucher->SetVoucherDetails($voucherDetails[0]['Date'],$voucherDetails[0]['RefNo'],$voucherDetails[0]['RefTableID'],$voucherDetails[0]['VoucherNo'],$voucherDetails[0]['M_SrNo'] + 1,$voucherDetails[0]['VoucherTypeID'],$LedgerID, TRANSACTION_CREDIT,$Amount,"-");
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Inserting new voucher row " . $voucherID . "  LedgerID:" . $LedgerID . " Amount : " . $Amount . "<BR>";
			}
			$this->obj_register->SetRegister($voucherDetails[0]['Date'], $LedgerID, $voucherID, $voucherDetails[0]['VoucherTypeID'], TRANSACTION_CREDIT, $Amount); 
			return 2;
		}
		return 0;
	}	
	
	public function comboboxEx($query)
	{
		$id=0;
		//echo "<script>alert('test')<//script>";
		$str.="<option value=''>Please Select</option>";
	$data = $this->m_dbConn->select($query);
	//echo "<script>alert('test2')<//script>";
		if(!is_null($data))
		{
			$vowels = array('/', '*', '%', '&', ',', '(', ')', '"');
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
						//$str.=$v."</OPTION>";
					}
					//echo "<script>alert('".$str."')<//script>";
					$i++;
				}
			}
		}
		//return $str;
		//print_r( $str);
		//echo "<script>alert('test')<//script>";
		return $str;
	}
	
	function SetReminderSMSDetails($society_id, $period_id)
	{
		//Get bill due date
		$sqlDueDate = "SELECT `DueDate` from billregister WHERE SocietyID = '" . $society_id . "' and PeriodID = '" . $period_id . "' ORDER BY ID DESC LIMIT 1";
		$sqlDueDateResult = $this->m_dbConn->select($sqlDueDate);
		$dueDate = $sqlDueDateResult[0]['DueDate'];
		
		if($dueDate <> '')
		{
			//Insert or Update Reminder date in `remindersms` table						
			$countQuery = "SELECT count(ID) AS `cnt` FROM `remindersms` WHERE `society_id` = '" . $this->m_dbConn->escapeString($society_id). "' AND `PeriodId` = '" . $this->m_dbConn->escapeString( $period_id). "'";
			$count = $this->m_dbConnRoot->select($countQuery);
			
			$ReminderDays = -5;
			//Get Reminder_Day from Society Level
/*			$societyInfo = $this->m_objUtility->GetSocietyInformation($_SESSION['society_id']);
			//Add sms_reminder_days in the database with default to 5
			$ReminderDays = $societyInfo['sms_reminder_days'] * 1;
			if($dueDate <> '')
			{
				$ReminderDays = -5;
			}
*/

			$reminderDate = $this->GetDateByOffset($dueDate, $ReminderDays);
			if($count[0]['cnt'] == 0)
			{																					
				$sqlReminderQuery = "INSERT INTO `remindersms`(`society_id`, `PeriodID`, `ReminderType`, `EventDate`, `EventReminderDate`, `LoginID`) VALUES ('" . $this->m_dbConn->escapeString($society_id). "',
							 '" . $this->m_dbConn->escapeString($period_id). "','".SENDBILLREMINDER."','" . getDBFormatDate($this->m_dbConn->escapeString($dueDate)) . "','" . getDBFormatDate($this->m_dbConn->escapeString($reminderDate)) . "',
							 '".$_SESSION['login_id']."')";												
				$this->m_dbConnRoot->insert($sqlReminderQuery);
			}
			else
			{
				$updateTable = "UPDATE `remindersms` SET `EventDate`='" . getDBFormatDate($this->m_dbConn->escapeString($dueDate)) . "',
					`EventReminderDate`='" . getDBFormatDate($this->m_dbConn->escapeString($reminderDate)) . "', `LoginID` = '".$_SESSION['login_id']."', `ReminderType` = '".SENDBILLREMINDER."'  WHERE `society_id`='" . $this->m_dbConn->escapeString($society_id). "' AND
					`PeriodID`='" . $this->m_dbConn->escapeString($period_id). "'";								
				$this->m_dbConnRoot->update($updateTable);
			}	
		}
	}
	
	
	public function unsetZeroKeysFromArray($data , $checkZero)
	{
		foreach($checkZero as $key => $value)
		{
			if($checkZero[$key] == false)
			{
				$this->recursiveRemoval($data, $key);
			}
		}
		return $data;
		
	}
	
	function recursiveRemoval(&$array, $keyToRemove)
	{
		if(is_array($array))
		{
			foreach($array as $key => &$arrayElement)
			{
				if(is_array($arrayElement))
				{
					$this->recursiveRemoval($arrayElement, $keyToRemove);
				}
				else
				{
					if($key == $keyToRemove)
					{
						unset($array[$key]);
					}
				}
			}
		}
	}
	
	
function getCollectionOfDataToDisplay_optimize($society_id, $wing_id, $unit_id, $period_id,$isBillSummary = false, $SupplementaryBill)
	{
		$sqlPeriod = "SELECT max(id),PeriodID,BillDate,DueDate FROM `billregister` where `PeriodID` = '".$period_id."' and BillType='".$SupplementaryBill."' ";
		$resultPeriod = $this->m_dbConn->select($sqlPeriod);
		$prevPeriod = $this->getPreviousPeriodData($period_id);
		
		$sqlPymtQuery2 = "Select Type, YearID, PrevPeriodID, Status, BeginingDate, EndingDate from period where ID=" . $period_id;
		$Prevresult2 = $this->m_dbConn->select($sqlPymtQuery2);
				
		$sql = "SELECT `id`,`ledger_name` FROM `ledger`  WHERE `id` IN(SELECT vch.`To` from `voucher` as vch JOIN `billdetails` as bill on bill.ID = vch.`RefNo` where `To` <> '' and `RefTableID`=1 and BillType = '". $SupplementaryBill . "' and bill.`PeriodID` = '".$period_id."' ) and `id` NOT IN(".INTEREST_ON_PRINCIPLE_DUE.",".ADJUSTMENT_CREDIT.",".IGST_SERVICE_TAX.",".CGST_SERVICE_TAX.",".SGST_SERVICE_TAX.",".CESS_SERVICE_TAX.") ";
		$result = $this->m_dbConn->select($sql);
		
		//array_push($result,array('id' => INTEREST_ON_PRINCIPLE_DUE ,'ledger_name'  => "iNTEREST ON ARREARS"));
		//array_push($result,array('id' => ADJUSTMENT_CREDIT ,'ledger_name'  => "ADJUSTMENT CREDIT"));
			$societyInfo = $this->m_objUtility->GetSocietyInformation($_SESSION['society_id']);
			$ApplyServiceTax = $societyInfo['apply_service_tax'];
			if($ApplyServiceTax == 1)
			{
		$sqlII = "SELECT  IF(vch.`RefNo` > 0,' ',' ')  as '',vch.`RefNo`, uni.unit_no as UNIT_NO, mem.owner_name as OWNER_NAME,mem.owner_gstin_no as OWNER_GSTIN,uni.area as AREA, bill.BillNumber as BILL_NUMBER, ";
			}
			else
			{
				$sqlII = "SELECT  IF(vch.`RefNo` > 0,' ',' ')  as '',vch.`RefNo`, uni.unit_no as UNIT_NO, mem.owner_name as OWNER_NAME,uni.area as AREA, bill.BillNumber as BILL_NUMBER, ";
			}
		for($m = 0; $m < sizeof($result); $m++)
		{
			$sqlII	.= " SUM( IF( vch.`To` =  '".$result[$m]['id']."', vch.`Credit` , 0.00 ) ) AS '".$result[$m]['ledger_name']."' ";
			
			if($m < sizeof($result))
			{
				$sqlII	.= " , ";
			}
		}
		
		/*$sqlII	.="	sum(vch.credit), sum(chq.Amount), (sum(vch.credit) - sum(chq.Amount)),bill.`BillSubTotal` ,bill.`BillInterest` as InterestOnArrears,
							bill.`AdjustmentCredit` , bill.`PrincipalArrears` as PreviousPrincipalArrears , bill.`InterestArrears` as PreviousInterestArrears,
							(bill.`BillSubTotal` + bill.`AdjustmentCredit`  + bill.`BillInterest` + bill.`PrincipalArrears` + bill.`InterestArrears`) as Payable
							FROM voucher AS vch JOIN billdetails AS bill ON vch.RefNo = bill.ID JOIN member_main AS mem ON bill.UnitID = mem.unit
							JOIN unit AS uni ON uni.unit_id = bill.UnitID LEFT JOIN chequeentrydetails as chq on bill.UnitID = chq.PaidBy WHERE bill.PeriodID ='".$period_id."' ";*/
		/*$sqlII	.="	bill.`BillSubTotal` ,bill.`BillInterest` as InterestOnArrears,
							bill.`AdjustmentCredit` , bill.`PrincipalArrears` as PreviousPrincipalArrears , bill.`InterestArrears` as PreviousInterestArrears,
							(bill.`BillSubTotal` + bill.`AdjustmentCredit`  + bill.`BillInterest` + bill.`PrincipalArrears` + bill.`InterestArrears`) as Payable
							FROM voucher AS vch JOIN billdetails AS bill ON vch.RefNo = bill.ID JOIN member_main AS mem ON bill.UnitID = mem.unit
							JOIN unit AS uni ON uni.unit_id = bill.UnitID LEFT JOIN chequeentrydetails as chq on bill.UnitID = chq.PaidBy WHERE bill.PeriodID ='".$period_id."' ";	*/
							//$societyInfo = $this->m_objUtility->GetSocietyInformation($_SESSION['society_id']);
		//$ApplyServiceTax = $societyInfo['apply_service_tax'];
		if($ApplyServiceTax == 1)
		{
			$sqlII	.="	bill.`BillSubTotal` ,bill.`BillInterest` as InterestOnArrears,
								bill.`AdjustmentCredit`, bill.`CGST` as CGST , bill.`SGST` as SGCT , bill.`PrincipalArrears` as PreviousPrincipalArrears , bill.`InterestArrears` as PreviousInterestArrears ,
								(bill.`BillSubTotal` + bill.`AdjustmentCredit`  + bill.`BillInterest` + bill.`PrincipalArrears` + bill.`InterestArrears` + bill.`CGST` + bill.`SGST` ) as Payable
								FROM voucher AS vch JOIN billdetails AS bill ON vch.RefNo = bill.ID JOIN member_main AS mem ON bill.UnitID = mem.unit
								JOIN unit AS uni ON uni.unit_id = bill.UnitID WHERE bill.BillType = '". $SupplementaryBill . "' and bill.PeriodID ='".$period_id."' ";										
		}
		else
		{
			$sqlII	.="	bill.`BillSubTotal` ,bill.`BillInterest` as InterestOnArrears,
								bill.`AdjustmentCredit` , bill.`PrincipalArrears` as PreviousPrincipalArrears , bill.`InterestArrears` as PreviousInterestArrears,
								(bill.`BillSubTotal` + bill.`AdjustmentCredit`  + bill.`BillInterest` + bill.`PrincipalArrears` + bill.`InterestArrears`) as Payable
								FROM voucher AS vch JOIN billdetails AS bill ON vch.RefNo = bill.ID JOIN member_main AS mem ON bill.UnitID = mem.unit
								JOIN unit AS uni ON uni.unit_id = bill.UnitID WHERE bill.BillType = '". $SupplementaryBill . "' and bill.PeriodID ='".$period_id."' ";										
		}
		if($unit_id == 0)
		{
			$memberIDS = $this->m_objUtility->getMemberIDs($resultPeriod[0]['BillDate']);
			$sqlII	.="	and mem.member_id IN (".$memberIDS.") ";
		}
		else
		{
			$sqlII   .= "   and uni.unit_id = '" . $unit_id . "'";
		}
		
		$sqlII   .= "    and bill.BillType='".$SupplementaryBill."' ";
		$sqlII	.="	GROUP BY vch.`RefNo`  ORDER BY uni.sort_order ASC";
		
		$resData = $this->m_dbConn->select($sqlII);
		
		/********** CALCULATE SUM OF LEDGERS *************/
		$sumArray = array();

		foreach ($resData as $k=>$subArray) 
		{
  			foreach ($subArray as $id=>$value) 
			{
    			$sumArray[$id]+=$value;
  			}
		}
		
		$sumArray[''] = "";
		$sumArray['UNIT_NO'] = " ";
		$sumArray['OWNER_NAME'] = "Total";
		$sumArray['BillNumber'] = " ";
		array_push($resData,$sumArray);
		
		/********* REMOVE REFNO FROM ARRAY ******/
		$this->recursiveRemoval($resData, 'RefNo');
		
		return $resData;
	}		

	public function getCollectionOfDataForContributionLedger_optimize($society_id, $wing_id, $unit_id, $period_id,$isBillSummary = false, $SupplementaryBill)
	{
		$sqlPeriod  = "Select ID from `period` where YearID = '" . $_SESSION['default_year'] . "'";
		$resPeriod = $this->m_dbConn->select($sqlPeriod);

		$iFirst = true;
		
		for($iCntr = 0 ; $iCntr < sizeof($resPeriod); $iCntr++)
		{
			if($iFirst == false)
			{
				$periodList .= ',';
			}
			$iFirst = false;
			$periodList .= $resPeriod[$iCntr]['ID'];
		}

		//echo $periodList;

		//$sql = "SELECT `id`,`ledger_name` FROM `ledger`  WHERE `id` IN(SELECT vch.`To` from `voucher` as vch JOIN `billdetails` as bill on bill.ID = vch.`RefNo` where `To` <> '' and `RefTableID`=1 and bill.`PeriodID` IN (" . $periodList . ") ) and `id` NOT IN(".INTEREST_ON_PRINCIPLE_DUE.",".ADJUSTMENT_CREDIT.") ";
		
		$sql = "SELECT `id`,`ledger_name` FROM `ledger`  WHERE `id` IN(SELECT vch.`To` from `voucher` as vch JOIN `billdetails` as bill on bill.ID = vch.`RefNo` where `To` <> '' and `RefTableID`=1 and BillType = '" . $SupplementaryBill . "' and bill.`PeriodID` IN (" . $periodList . ") ) and `id` NOT IN(".INTEREST_ON_PRINCIPLE_DUE.",".ADJUSTMENT_CREDIT.",".IGST_SERVICE_TAX.",".CGST_SERVICE_TAX.",".SGST_SERVICE_TAX.",".CESS_SERVICE_TAX.") ";

		$result = $this->m_dbConn->select($sql);
		
		$sqlII = "SELECT  vch.`RefNo`, uni.`unit_id`, uni.`unit_no` as 'Unit No', mem.`owner_name` as 'Member Name', bill.`BillNumber`, yr.`YearDescription` as 'Fin. Year', prd.`Type` as 'Bill For', prd.`ID` as 'PeriodID', ";
		
		for($m = 0; $m < sizeof($result); $m++)
		{
			$sqlII	.= " SUM( IF( vch.`To` =  '".$result[$m]['id']."', vch.`Credit` , 0.00 ) ) AS '".$result[$m]['ledger_name']."' ";
			
			if($m < sizeof($result))
			{
				$sqlII	.= " , ";
			}
		}

		$societyInfo = $this->m_objUtility->GetSocietyInformation($_SESSION['society_id']);
		$ApplyServiceTax = $societyInfo['apply_service_tax'];
		if($ApplyServiceTax == 1)
		{
			$sqlII	.="	bill.`BillSubTotal` ,bill.`BillInterest` as InterestOnArrears,
							bill.`AdjustmentCredit`, bill.`CGST` as CGST , bill.`SGST` as SGCT , bill.`PrincipalArrears` as PreviousPrincipalArrears , bill.`InterestArrears` as PreviousInterestArrears,
							(bill.`BillSubTotal` + bill.`AdjustmentCredit`  + bill.`BillInterest` + bill.`PrincipalArrears` + bill.`InterestArrears` + bill.`CGST` + bill.`SGST` ) as Payable
							FROM voucher AS vch JOIN billdetails AS bill ON vch.RefNo = bill.ID JOIN member_main AS mem ON bill.UnitID = mem.unit
							JOIN unit AS uni ON uni.unit_id = bill.UnitID JOIN year as yr ON yr.YearID = '".$_SESSION['default_year']."'  JOIN period as prd on bill.PeriodID = prd.ID WHERE bill.BillType = '". $SupplementaryBill . "' and bill.PeriodID IN (" . $periodList . ") ";								
		}
		else
		{
			$sqlII	.="	bill.`BillSubTotal` ,bill.`BillInterest` as InterestOnArrears,
							bill.`AdjustmentCredit`, bill.`PrincipalArrears` as PreviousPrincipalArrears , bill.`InterestArrears` as PreviousInterestArrears,
							(bill.`BillSubTotal` + bill.`AdjustmentCredit`  + bill.`BillInterest` + bill.`PrincipalArrears` + bill.`InterestArrears`) as Payable
							FROM voucher AS vch JOIN billdetails AS bill ON vch.RefNo = bill.ID JOIN member_main AS mem ON bill.UnitID = mem.unit
							JOIN unit AS uni ON uni.unit_id = bill.UnitID JOIN year as yr ON yr.YearID = '".$_SESSION['default_year']."'  JOIN period as prd on bill.PeriodID = prd.ID WHERE bill.BillType = '". $SupplementaryBill . "' and  bill.PeriodID IN (" . $periodList . ") ";										
		}										
							
		if($unit_id == 0)
		{
			$memberIDS = $this->m_objUtility->getMemberIDs($resultPeriod[0]['BillDate']);
			//$sqlII	.="	and mem.member_id IN (".$memberIDS.") ";
		}
		else
		{
			$sqlII   .= " and uni.unit_id = '" . $unit_id . "'";
		}
		
		$sqlII   .= "    and bill.BillType='".$SupplementaryBill."' ";
		$sqlII	.="	GROUP BY vch.`RefNo`  ORDER BY uni.sort_order,bill.PeriodID ASC";
		
		$resData = $this->m_dbConn->select($sqlII);
		
		/********** CALCULATE SUM OF LEDGERS *************/
		$sumArray = array();


		for($iCnt = 0; $iCnt < sizeof($resData); $iCnt++)
		{
			$sqlPeriod = "SELECT max(id),PeriodID,BillDate,DueDate FROM `billregister` where `PeriodID` = '".$resData[$iCnt]['PeriodID']."' and BillType='".$SupplementaryBill."' ";
			$resultPeriod = $this->m_dbConn->select($sqlPeriod);
			$prevPeriod = $this->getPreviousPeriodData($resData[$iCnt]['PeriodID']);
			
			$sqlPymtQuery2 = "Select Type, YearID, PrevPeriodID, Status, BeginingDate, EndingDate from period where ID='" . $resData[$iCnt]['PeriodID'] . "'";

			$Prevresult2 = $this->m_dbConn->select($sqlPymtQuery2);

			$paymentReceived = $this->GetPaymentsReceived($resData[$iCnt]['unit_id'], $resData[$iCnt]['PeriodID'], $prevPeriod, $Prevresult2[0]['BeginingDate'], $Prevresult2[0]['EndingDate']);
			

			$iPaidDetails = '';
			$iPaidTotal = 0;
			for($iPaidCnt = 0 ; $iPaidCnt < sizeof($paymentReceived) ; $iPaidCnt++)
			{
				$iPaidDetails .= ($iPaidCnt + 1) . ' - ' . $paymentReceived[$iPaidCnt] . '<br />';
				$iPaidTotal +=  $paymentReceived[$iPaidCnt]['Amount'];
			}
			
			if($isBillSummary == true)
			{
				$resData[$iCnt]['Paid'] = $iPaidTotal;
				$resData[$iCnt]['Balance'] = $resData[$iCnt]['Payable'] - $iPaidTotal;
				if($resData[$iCnt]['Balance'] < 0 )
				{
					$resData[$iCnt]['Balance'] = number_format($resData[$iCnt]['Balance'],2).' Cr'; 		
				}
				else
				{
					$resData[$iCnt]['Balance'] = number_format($resData[$iCnt]['Balance'],2).' Dr'; 		
				}
				$resData[$iCnt]['Payable'] = $resData[$iCnt]['Payable'];
			}
		}
		
		foreach ($resData as $k=>$subArray) 
		{
			foreach ($subArray as $id=>$value) 
			{
    			$sumArray[$id]+=$value;
  			}
		}
		
		//$sumArray[''] = "";
		$sumArray['Unit No'] = $resData[0]['Unit No'];
		$sumArray['Member Name'] = "Total";
		$sumArray['BillNumber'] = " ";
		$sumArray['Fin. Year'] = " ";
		$sumArray['Bill For'] = " ";
		$sumArray['Payable'] = " ";
		$sumArray['Paid'] = " ";
		$sumArray['Balance'] = " ";

		array_push($resData,$sumArray);
		
		/********* REMOVE REFNO FROM ARRAY ******/
		$this->recursiveRemoval($resData, 'RefNo');
		$this->recursiveRemoval($resData, 'unit_id');
		$this->recursiveRemoval($resData, 'PeriodID');
		
		return $resData;
	}		
	
	public function setMaintenanceBillReadUnreadFlag($UnitID,$PeriodID,$BillType)
	{
		//Maintenance Bill email read-unread flag
		if($_SESSION['unit_id'] == $UnitID && ($_SESSION['role'] == ROLE_MEMBER || $_SESSION['role'] == ROLE_ADMIN_MEMBER))
		{
			date_default_timezone_set('Asia/Kolkata');
			$readDate = date('Y-m-d h:i:s');

			$member_other_id = $_SESSION['mem_other_id'];

			$updateTable = "UPDATE `billdetails` SET `Bill_Read_Timestamp`= '" . $readDate .  "', `Mem_Other_ID` = '" . $member_other_id . "', `Login_ID` = '" . $_SESSION['login_id'] . "' where `UnitID` = '".$UnitID."' and `PeriodID` = '".$PeriodID."' and `BillType` ='".$BillType."' and `Bill_Read_Timestamp` = 0 ";								
			$this->m_dbConn->update($updateTable);
		}
		else
		{
			//do nothing
		}
	}
	
	

	function ServiceTaxImplement($GSTLedger,$ServiceTaxAmount,$refNo,$ApplyServiceTax)
	{
		//$this->ShowDebugTrace = 1;
		
		$sqlCheckST= "select * from `voucher` where `To`= '" . $GSTLedger ."' and `RefNo` ='" . $refNo . "'  and `RefTableID` = '" . TABLE_BILLREGISTER . "'";
		$resultST = $this->m_dbConn->select($sqlCheckST);

		if($this->ShowDebugTrace == 1)
		{
			echo "<BR>Inside ServiceTaxImplement for :" . $GSTLedger . "<BR>";
			echo $sqlCheckST;
			echo "<BR>";
			print_r($resultST);
			echo "<BR>VoucherID :" . $resultST[0]['id'] . "<BR>";
		}
//		echo "<BR>GroupID :". $GroupID . "  LedgerID : " . $ledgerID . " VoucherID :" . $resultST[0]['id'] . "   Amount :" . $ServiceTaxAmount . "<BR>";
		$voucherID = $resultST[0]['id'];
		if($resultST[0]['id']  <> "")
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Exist in voucher with id:" .  $resultST[0]['id'] . "<BR>";
			}
			if($ApplyServiceTax == 0)
			{
				if($this->ShowDebugTrace == 1)
				{
					echo "Deleting voucherID:" . $voucherID . " from voucher and incomeregister and liabilityregister<BR>";
				}
				//No Tax.. so delete the one existing earlier
				$sqlDeleteST = "DELETE from `voucher` WHERE `id` = '" . $voucherID  . "'";
				$resultDeleteST = $this->m_dbConn->delete($sqlDeleteST);

				$sqlDeleteST = "DELETE from `incomeregister` WHERE `LedgerID` = '" . $voucherID  . "'";
				$resultDeleteST = $this->m_dbConn->delete($sqlDeleteST);
				//if($resultDeleteST==0)
				{
					$sqlDeleteST = "DELETE from `liabilityregister` WHERE `LedgerID` = '" . $voucherID  . "'";
					$resultDeleteST = $this->m_dbConn->delete($sqlDeleteST);
				}
				return;
			}
		}			
		$this->UpdateElseInsertVoucherAndRegister_Credit($voucherID, $GSTLedger, $refNo, TABLE_BILLREGISTER, $ServiceTaxAmount);
						
	}
}
?>
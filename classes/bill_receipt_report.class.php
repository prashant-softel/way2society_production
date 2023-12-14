<?php if(!isset($_SESSION)){session_start(); }
include_once("include/display_table.class.php");
include_once("dbconst.class.php");
include_once("utility.class.php");
include_once("changelog.class.php");

class bill_receipt_report extends dbop
{
	public $actionPage = "../bill_receipt_report.php";
	public $m_dbConn;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg = new display_table($this->m_dbConn);
	}
	public function startProcess()
	{
		$errorExists=0;
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
		else
		{
			return $errString;
		}
	}
	public function combobox($query,$id, $defaultText, $defaultValue = '0')
	{
		$str = '';
		
		if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "'>" . $defaultText . "</option>";
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
	public function display1($rsas)
	{
		$thheader=array('Society Name','Wing','Unit No.');
		$this->display_pg->edit="getunit";
		$this->display_pg->th=$thheader;
		$this->display_pg->mainpg="unit.php";
		
		//$res=$this->display_pg->display_new($rsas);
		$res=$this->show_unit($rsas);
		
		return $res;
	}
	public function pgnation()
	{
		if($_REQUEST['insert']=='Search')
		{
			$sql1 = "select u.unit_id,s.society_id,s.society_name,w.wing_id,w.wing,u.unit_no,u.rand_no from 
					 unit as u,society as s,wing as w
					 where u.status='Y' and w.status='Y' and s.status='Y' and 
					 u.wing_id=w.wing_id and u.society_id=s.society_id
					 ";
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
			$sql1 .= " order by s.society_id,w.wing,u.unit_id";		 
		}
		else
		{
			$sql1 = "select u.unit_id,s.society_id,s.society_name,w.wing_id,w.wing,u.unit_no,u.rand_no from 
					 unit as u,society as s,wing as w
					 where u.status='Y' and w.status='Y' and s.status='Y' and 
					 u.wing_id=w.wing_id and u.society_id=s.society_id";
					 
			if(isset($_SESSION['sadmin']))
			{
				if(isset($_REQUEST['sa']))
				{
					$sql1 .= " and s.society_id='".$_REQUEST['sid']."' and w.wing_id='".$_REQUEST['wid']."'";
				}
			}
			
			if(isset($_REQUEST['ssid']) && isset($_REQUEST['wwid']))
			{		 
				$sql1 .= " and s.society_id='".$_REQUEST['ssid']."' and w.wing_id='".$_REQUEST['wwid']."' order by s.society_id,w.wing,u.unit_id";
			}
			else
			{
				if(isset($_SESSION['sadmin']))
				{
					$sql1 .= " order by s.society_id,w.wing,u.unit_id";	
				}
				else
				{
					$sql1 .= " and s.society_id='".$_SESSION['society_id']."' order by s.society_id,w.wing,u.unit_id";	
				}
			}
		}
		
		//echo $sql1;
		
		if($_REQUEST['insert']=='Search')
		{
			$cntr = "select count(*) as cnt from 
					 unit as u,society as s,wing as w
					 where u.status='Y' and w.status='Y' and s.status='Y' and 
					 u.wing_id=w.wing_id and u.society_id=s.society_id
					 ";
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
				$cntr .= " and u.unit_no='".$_REQUEST['unit_no']."'";
			}	
		}
		else
		{
			$cntr = "select count(*) as cnt from 
					 unit as u,society as s,wing as w
					 where u.status='Y' and w.status='Y' and s.status='Y' and 
					 u.wing_id=w.wing_id and u.society_id=s.society_id
					 ";
			if(isset($_SESSION['sadmin']))
			{
				if(isset($_REQUEST['sa']))
				{
					$cntr .= " and s.society_id='".$_REQUEST['sid']."' and w.wing_id='".$_REQUEST['wid']."'";
				}
			}

			if(isset($_REQUEST['ssid']) && isset($_REQUEST['wwid']))
			{		 
				$cntr .= " and s.society_id='".$_REQUEST['ssid']."' and w.wing_id='".$_REQUEST['wwid']."' order by s.society_id,w.wing,u.unit_id";
			}
			else
			{
				if(isset($_SESSION['sadmin']))
				{
					$cntr .= " order by s.society_id,w.wing,u.unit_id";	
				}
				else
				{
					$cntr .= " and s.society_id='".$_SESSION['society_id']."' order by s.society_id,w.wing,u.unit_id";	
				}
			}
		}
		
		
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$this->display_pg->mainpg="unit.php";
		$limit = "500";
		$page=$_REQUEST['page'];
		
		if(isset($_SESSION['sadmin']))
		{
			if(isset($_REQUEST['sa']))
			{
				$extra = "&imp&id=".time()."&sa&sid=".$_REQUEST['sid']."&wid=".$_REQUEST['wid']."&id=".$_REQUEST['id'];
			}
			else
			{
				$extra = "&imp&id=".time();
			}
			
		}
		else
		{
			$extra = "&imp&ws&ssid=".$_REQUEST['ssid']."&wwid=".$_REQUEST['wwid']."&idd=".time().'&unit_no='.$_REQUEST['unit_no'];
		}
		
		$res = $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}
	
	public function show_unit($res)
	{
		if($res<>"")
		{
		?>
		<table align="center" border="0">
		<tr height="30" bgcolor="#CCCCCC">
        	<th width="300">Society Name</th>
            <th width="100">Wing</th>
            <th width="100">Unit No.</th>
            
            <?php if(isset($_SESSION['admin'])){?><th width="100">Code</th><?php }?>
            
            <?php if(isset($_GET['ws'])){?>
        	<th width="70">Edit</th>
            <th width="70">Delete</th>
            <?php }?>
            
        </tr>
        <?php foreach($res as $k => $v){?>
        <tr height="25" bgcolor="#BDD8F4" align="center">
        	<td align="center"><?php echo $res[$k]['society_name'];?></td>
            <td align="center"><?php echo $res[$k]['wing'];?></td>
            <td align="center"><?php echo $res[$k]['unit_no'];?></td>
            <?php if(isset($_SESSION['admin'])){?><td align="center"><?php echo $res[$k]['rand_no'];?></td><?php } ?>
            
            <?php if(isset($_GET['ws'])){?>
            <td align="center">
            <a href="javascript:void(0);" onclick="getunit('edit-<?php echo $res[$k]['unit_id']?>')"><img src="../images/edit.gif" /></a>
            </td>
            
            <td align="center">
            <?php if($this->chk_delete_perm_admin()==1){?>
            <a href="javascript:void(0);" onclick="getunit('delete-<?php echo $res[$k]['unit_id']?>');"><img src="../images/del.gif" /></a>
            <?php }else{?>
            <a href="del_control_admin.php?prm" target="_blank" style="text-decoration:none;"><font color=#FF0000 style='font-size:10px;'><b>Not Allowed</b></font></a>
            <?php }?>
            </td>
            <?php }?>
            
            </tr>
        <?php }?>
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
			$sql1="select unit_id,`society_id`,`wing_id`,`unit_no` from unit where unit_id='".$_REQUEST['unitId']."'";
			$var=$this->m_dbConn->select($sql1);
			return $var;
	}
	public function deleting()
	{
		$sql0 = "select count(*)as cnt from member_main where unit='".$_REQUEST['unitId']."' and status='Y'";
		$res0 = $this->m_dbConn->select($sql0);
		
		if($res0[0]['cnt']==0)
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
	
	public function chk_delete_perm_admin()
	{
		$sql = "select * from del_control_admin where status='Y' and login_id='".$_SESSION['login_id']."'";
		$res = $this->m_dbConn->select($sql);
		return $res[0]['del_control_admin'];
	}
	
	public function fetch_data($aryUnit, $period, $bill_type)
	{
		$sqlPeriod = "Select BeginingDate, EndingDate from period where ID = '" . $period . "'";
		$resultPeriod = $this->m_dbConn->select($sqlPeriod);
		
		$beginDate = $resultPeriod[0]['BeginingDate'];
		$endDate = $resultPeriod[0]['EndingDate'];
		
		$sql = "select UnitID, AccountHeadID, AccountHeadAmount, BeginPeriod, EndPeriod from unitbillmaster where UnitID IN (" . implode(',',  (array)$aryUnit) . ") and BeginPeriod <= '" . $beginDate . "' and  BillType = '" . $bill_type . "' ORDER BY UnitID, AccountHeadID, EndPeriod ASC";
		//$sql = "select UnitID, AccountHeadID, AccountHeadAmount, BeginPeriod, EndPeriod from unitbillmaster where UnitID IN (1) and BeginPeriod <= '" . $beginDate . "' and AccountHeadID = 105 ORDER BY UnitID, AccountHeadID, EndPeriod ASC";
		
		$res = $this->m_dbConn->select($sql);	
			
		if($res<>"")
		{
			$obj_utility = new utility($this->m_dbConn);
			
			$aryResult = array();
			array_push($aryResult,array('success'=>'0'));
			
			$unit_inprocess = 0;
			$ledger_inprocess = 0;
			$add = false;
			
			foreach($res as $k => $v)
			{
				$dateDiff = $obj_utility->getDateDiff($res[$k]['EndPeriod'], $endDate);
				//echo '<br/>Date Diff : ' . $res[$k]['EndPeriod'] . ":" . $endDate . " = " . $dateDiff;
				
				if($unit_inprocess <> $res[$k]['UnitID'])
				{
					$ledger_inprocess = 0;
				}
				
				if($dateDiff >= 0 && $ledger_inprocess <> $res[$k]['AccountHeadID'])
				{
					$unit_inprocess = $res[$k]['UnitID'];
					$ledger_inprocess = $res[$k]['AccountHeadID'];
					
			 		$show_dtl = array("id"=>$res[$k]['UnitID'], "head"=>$res[$k]['AccountHeadID'], "amt"=>$res[$k]['AccountHeadAmount']);
					array_push($aryResult,$show_dtl);
				}
			}
			echo json_encode($aryResult);
		}
		else
		{
			echo json_encode(array(array("success"=>1), array("message"=>'No Data To Display')));
		}
	}
	
	public function fetch_acc_head($billtype)
	{
/*
		 $sql = "select * from ledger where `id` NOT IN ('".IGST_SERVICE_TAX."','".CGST_SERVICE_TAX."','".SGST_SERVICE_TAX."','".CESS_SERVICE_TAX."') and show_in_bill='1' and society_id='" . $_SESSION['society_id'] . "'";
		if($billtype == 1)
		{
			$sql = "select * from ledger where `id` NOT IN ('".IGST_SERVICE_TAX."','".CGST_SERVICE_TAX."','".SGST_SERVICE_TAX."','".CESS_SERVICE_TAX."') and supplementary_bill='1' and society_id='" . $_SESSION['society_id'] . "'";
		}
*/
		
		$sql = "SELECT ll.id, ll.categoryid, ll.ledger_name, ac.group_id FROM `ledger` as ll join account_category as ac on ll.categoryid= ac.category_id where ac.group_id = 3 and ll.id NOT IN ('".IGST_SERVICE_TAX."','".CGST_SERVICE_TAX."','".SGST_SERVICE_TAX."','".CESS_SERVICE_TAX."') ";

		if($billtype == 1)
		{
			$sql = $sql . " and ll.supplementary_bill='1'";
		}
		else
		{
			$sql = $sql . " and ll.show_in_bill='1'";
		}

		//echo $sql;

		$res = $this->m_dbConn->select($sql);	
		$aryResult = array();
		if($res<>"")
		{
			array_push($aryResult,array('success'=>'0'));
			foreach($res as $k => $v)
			{
			 	$show_dtl = array("id"=>$res[$k]['id'], "head"=>($res[$k]['ledger_name'] . '[' . $res[$k]['id'] . ']'));
				array_push($aryResult,$show_dtl);
			}
			return json_encode($aryResult);
		}
		else
		{
			array_push($aryResult,array('success'=>'1'));
			//return json_encode(array(array("success"=>1), array("message"=>'No Data To Display')));
			return json_encode($aryResult);
		}
	}
	
	public function update_billmaster($unit, $head, $amount, $period, $start_period, $end_period, $bill_type)
	{
		$sqlPeriod = "Select BeginingDate, EndingDate from period where ID = '" . $start_period . "'";
		$resultPeriod = $this->m_dbConn->select($sqlPeriod);
		
		$beginDate_start = $resultPeriod[0]['BeginingDate'];
		$endDate_start = $resultPeriod[0]['EndingDate'];
		
		//echo "********Start Period : <" . $start_period . "> Begin <" . $beginDate_start . "> End <" . $endDate_start . ">.";
		
		$sqlPeriod = "Select BeginingDate, EndingDate from period where ID = '" . $end_period . "'";
		$resultPeriod = $this->m_dbConn->select($sqlPeriod);
		
		$beginDate_end = $resultPeriod[0]['BeginingDate'];
		$endDate_end = $resultPeriod[0]['EndingDate'];
		
		if($end_period == 0)
		{
			$endDate_end = PHP_MAX_DATE;
		}
		
		//echo "********End Period : <" . $start_period . "> Begin <" . $beginDate_end . "> End <" . $endDate_end . ">.";		
		
		$sql = "select ID, AccountHeadID, AccountHeadAmount As HeadAmt, BeginPeriod, EndPeriod from unitbillmaster where UnitID = '" . $unit .  "' and AccountHeadID='" . $head . "' and BillType = '" . $bill_type . "' ORDER BY EndPeriod ASC";
		
		$res = $this->m_dbConn->select($sql);	
		
		$logMsg = "Set/Update Tariff : Unit <" . $unit . "> Head <" . $head . "> Amount <" . $amount . "> Period <" . $beginDate_start . "> To <" . $endDate_end .  ">";
		
		$obj_log = new changeLog($this->m_dbConn);
		$logID = $obj_log->setLog($logMsg, $_SESSION['login_id'], 'unitbillmaster', 0);
				
		if($res <> "")
		{
			$logMsg .= "<br />Original Record : ";
			$logMsg .= implode(' || ', array_map(function ($entry) 
			{
				return $entry['HeadAmt'] . " | " . $entry['BeginPeriod'] . " To " . $entry['EndPeriod'];
			}, $res));
	
			$updateLogSql = "update `change_log` SET ChangedLogDec = '" . $this->m_dbConn->escapeString($logMsg) . "' WHERE ChangeLogID = '" . $logID . "'";
			$resultUpdateLog = $this->m_dbConn->update($updateLogSql);
			
			$obj_utility = new utility($this->m_dbConn);
			
			$iMatchID = 0;
			$bIsRangeSinglePeriod = false;
			$bProcessComplete = false;
			$bCheckRemainingEntries = false;
			for($iCnt = 0; $iCnt < sizeof($res); $iCnt++)
			{
				if($bProcessComplete == false)
				{
					if($iMatchID == 0)
					{
						//Check if date is in range of already existing entries.
						$bIsDateInRange = $obj_utility->getIsDateInRange($endDate_start, $res[$iCnt]['BeginPeriod'], $res[$iCnt]['EndPeriod']);
						
						//echo "Check Range : Start : " . $endDate_start . ":" . $res[$iCnt]['BeginPeriod'] . ":" . $res[$iCnt]['EndPeriod'] . ":" . $bIsDateInRange;
						if($bIsDateInRange == true)
						{
							//echo '********Match found at ID <' . $res[$iCnt]['ID'] . '> between <' . $res[$iCnt]['BeginPeriod'] . '> and <' . $res[$iCnt]['EndPeriod'] .  '>.';
							$iMatchID = $res[$iCnt]['ID'];
							
							//Check if the tariff is set for period range that already exist.
							if($res[$iCnt]['BeginPeriod'] == $beginDate_start && $res[$iCnt]['EndPeriod'] == $endDate_end)
							{
								//Update the record and set process as complete
								$sqlUpdate = "Update unitbillmaster SET AccountHeadAmount = '" . $amount . "', CreatedBy = '" . $_SESSION['login_id'] . "', LatestChangeID = '" . $logID . "' WHERE UnitID = '" . $unit . "' and AccountHeadID = '" . $head . "' and ID = '" . $res[$iCnt]['ID'] .  "' and BillType = '" . $bill_type . "' ";
								$resultUpdate = $this->m_dbConn->update($sqlUpdate);
								
								$bProcessComplete = true;
							}
							else if($res[$iCnt]['BeginPeriod'] == $beginDate_start)
							{
								$currentEndDate = $res[$iCnt]['EndPeriod'];
								$currentAmount = $res[$iCnt]['HeadAmt'];
								
								$sqlUpdate = "Update unitbillmaster SET AccountHeadAmount = '" . $amount .  "', EndPeriod = '" . $endDate_end . "', CreatedBy = '" . $_SESSION['login_id'] . "', LatestChangeID = '" . $logID . "' WHERE ID= '" . $res[$iCnt]['ID'] .  "' and BillType = '" . $bill_type . "' ";
								$resultUpdate = $this->m_dbConn->update($sqlUpdate);
								
								$currentDateDiff = $obj_utility->getDateDiff($currentEndDate, $endDate_end);
								
								if($currentDateDiff > 0)
								{
									$sqlInsert = "INSERT INTO `unitbillmaster`(`UnitID`, `CreatedBy`, `LatestChangeID`, `AccountHeadID`, `AccountHeadAmount`, `BeginPeriod`, `EndPeriod`, `BillType`) VALUES ('" . $this->m_dbConn->escapeString($unit) . "','" . $_SESSION['login_id'] . "', '" . $logID .  "','" . $this->m_dbConn->escapeString($head) . "','" . $this->m_dbConn->escapeString($currentAmount) . "', '" . $this->GetDateByOffset($endDate_end, 1) . "', '" . $currentEndDate . "', '" . $bill_type . "')";
									$resultInsert = $this->m_dbConn->insert($sqlInsert);
								}
								
								$bProcessComplete = true;
								$bCheckRemainingEntries = true;
							}
							else
							{
								//Update the End date of the entry at which the range is found.
								$sqlUpdate = "Update unitbillmaster SET EndPeriod = '" . $this->GetDateByOffset($beginDate_start, -1) . "', CreatedBy = '" . $_SESSION['login_id'] . "', LatestChangeID = '" . $logID . "' WHERE ID= '" . $res[$iCnt]['ID'] .  "' and BillType = '" . $bill_type . "' ";
								$resultUpdate = $this->m_dbConn->update($sqlUpdate);
								
								//Insert the new entry 
								$sqlInsert = "INSERT INTO `unitbillmaster`(`UnitID`, `CreatedBy`, `LatestChangeID`, `AccountHeadID`, `AccountHeadAmount`, `BeginPeriod`, `EndPeriod`, `BillType`) VALUES ('" . $this->m_dbConn->escapeString($unit) . "','" . $_SESSION['login_id'] . "', '" . $logID . "','" . $this->m_dbConn->escapeString($head) . "','" . $this->m_dbConn->escapeString($amount) . "', '" . $beginDate_start . "', '" . $endDate_end . "', '" . $bill_type . "')";
								$resultInsert = $this->m_dbConn->insert($sqlInsert);
								
								//Check if the End Date of the new entry is less than the End Date range found.
								$dateDiffEnd = $obj_utility->getDateDiff($res[$iCnt]['EndPeriod'], $endDate_end);
								//echo "Date Diff End : " . $res[$iCnt]['EndPeriod'] . ":" . $endDate_end . " = " . $dateDiffEnd;
								if($dateDiffEnd > 0)
								{
									$sqlInsert = "INSERT INTO `unitbillmaster`(`UnitID`, `CreatedBy`, `LatestChangeID`, `AccountHeadID`, `AccountHeadAmount`, `BeginPeriod`, `EndPeriod`, `BillType`) VALUES ('" . $this->m_dbConn->escapeString($unit) . "','" . $_SESSION['login_id'] . "', '" . $logID . "','" . $this->m_dbConn->escapeString($head) . "','" . $this->m_dbConn->escapeString($res[$iCnt]['HeadAmt']) . "', '" . $this->GetDateByOffset($endDate_end, 1) . "', '" . $res[$iCnt]['EndPeriod'] . "', '" . $bill_type . "')";
									$resultInsert = $this->m_dbConn->insert($sqlInsert);
								}
								
								$bCheckRemainingEntries = true;
							}
								//Check the Begin and End Dates for the entries after the Range entry
							if($bCheckRemainingEntries)
							{	
								for($jCnt = ($iCnt + 1); $jCnt < sizeof($res); $jCnt++)
								{
									if($end_period == 0)
									{
										//Currently set tarif should be set for lifetime. Delete other tariffs if any.
										$sqlDelete = "Delete from unitbillmaster where ID = '" . $res[$jCnt]['ID'] . "' and BillType = '" . $bill_type . "'";
										$resultDelete = $this->m_dbConn->delete($sqlDelete);
									}
									else
									{
										$newStartDate = $this->GetDateByOffset($endDate_end, 1);
										$dateDiff = $obj_utility->getDateDiff($newStartDate, $res[$jCnt]['EndDate']);
										if($dateDiff > 0)
										{
											$sqlDelete = "Delete from unitbillmaster where ID = '" . $res[$jCnt]['ID'] . "' and BillType = '" . $bill_type . "'";
											$resultDelete = $this->m_dbConn->delete($sqlDelete);
										}
										else
										{
											$dateDiffCheck = $obj_utility->getDateDiff($res[$jCnt]['BeginPeriod'], $newStartDate);
											if($dateDiffCheck < 0)
											{
												$sqlUpdate = "Update unitbillmaster SET BeginPeriod = '" . $newStartDate . "', CreatedBy = '" . $_SESSION['login_id'] . "', LatestChangeID = '" . $logID . "' where ID = '" . $res[$jCnt]['ID'] . "' and BillType = '" . $bill_type . "'";
												$resultUpdate = $this->m_dbConn->update($sqlUpdate);
											}
											
											$bProcessComplete = true;
											
											break;
										}
									}
								}
							}
						}
					}
				}
			}
			
			if($iMatchID == 0)
			{
				$sqlInsert = "INSERT INTO `unitbillmaster`(`UnitID`, `CreatedBy`, `LatestChangeID`, `AccountHeadID`, `AccountHeadAmount`, `BeginPeriod`, `EndPeriod`, `BillType`) VALUES ('" . $this->m_dbConn->escapeString($unit) . "','" . $_SESSION['login_id'] . "', '" . $logID .  "','" . $this->m_dbConn->escapeString($head) . "','" . $this->m_dbConn->escapeString($amount) . "', '" . $beginDate_start . "', '" . $endDate_end . "', '" . $bill_type . "')";
				$resultInsert = $this->m_dbConn->insert($sqlInsert);
			}
			
		}
		else
		{
			//Insert
			$sqlInsert = "INSERT INTO `unitbillmaster`(`UnitID`, `CreatedBy`, `LatestChangeID`, `AccountHeadID`, `AccountHeadAmount`, `BeginPeriod`, `EndPeriod`, `BillType`) VALUES ('" . $this->m_dbConn->escapeString($unit) . "','" . $_SESSION['login_id'] . "', '" . $logID .  "','" . $this->m_dbConn->escapeString($head) . "','" . $this->m_dbConn->escapeString($amount) . "', '" . $beginDate_start . "', '" . $endDate_end . "', '" . $bill_type . "')";
			$resultInsert = $this->m_dbConn->insert($sqlInsert);
		}
		
		//Delete the entries with 0 amount
		$sqlDelete = "Delete from unitbillmaster where UnitID = '" . $unit . "' and AccountHeadID = '" . $head . "' and AccountHeadAmount = '0' and BillType = '" . $bill_type . "'";
		$resultDelete = $this->m_dbConn->delete($sqlDelete); 
		
		//Check and Update/Delete the entries with same amount in continuation but divided into different rows.
		$sqlCheck = "select ID, AccountHeadID, AccountHeadAmount, BeginPeriod, EndPeriod from unitbillmaster where UnitID = '" . $unit .  "' and AccountHeadID='" . $head . "' and BillType = '" . $bill_type . "' ORDER BY BeginPeriod ASC";
		$resCheck = $this->m_dbConn->select($sqlCheck);	
		
		if($resCheck)
		{
			$prevEndDate = '';
			$prevStartDate = '';
			$prevAmount = 0;
			$prevID = '';

			for($iCnt = 0 ; $iCnt < sizeof($resCheck) ; $iCnt++)
			{
				$bSetPrevStartDate = true;
				$curBeginDate = $resCheck[$iCnt]['BeginPeriod'];
				if($prevEndDate != '')
				{
					$tempDate = $this->GetDateByOffset($resCheck[$iCnt]['BeginPeriod'], -1);
					//echo "********Counter : " . $iCnt . " prevAmount : " .  $prevAmount . " AccountHeadAmount : " . $resCheck[$iCnt]['AccountHeadAmount'] . " tempDate : " . $tempDate . "  prevEndDate : " . $prevEndDate . "*********";
					if(($prevAmount == $resCheck[$iCnt]['AccountHeadAmount']) && ($tempDate == $prevEndDate))
					{
						//Update
						$sqlUpdateDate = "Update unitbillmaster SET BeginPeriod = '" . $prevStartDate . "' where ID = '" . $resCheck[$iCnt]['ID'] . "' and BillType = '" . $bill_type . "'";
						$resultUpdateDate = $this->m_dbConn->update($sqlUpdateDate);
						$bSetPrevStartDate = false;
												
						$sqlDeleteDate = "Delete from unitbillmaster where ID = '" . $prevID . "' and BillType = '" . $bill_type . "'";
						$resultDeleteDate = $this->m_dbConn->delete($sqlDeleteDate);
 					}
				}
				
				if($bSetPrevStartDate == true)
				{
					$prevStartDate = $resCheck[$iCnt]['BeginPeriod'];
				}
				$prevEndDate = $resCheck[$iCnt]['EndPeriod'];
				$prevID = $resCheck[$iCnt]['ID'];
				$prevAmount = $resCheck[$iCnt]['AccountHeadAmount']; 
			}
		}
				
		$sql = "select ID, AccountHeadID, AccountHeadAmount As HeadAmt, BeginPeriod, EndPeriod from unitbillmaster where UnitID = '" . $unit .  "' and AccountHeadID='" . $head . "' and BillType = '" . $bill_type . "' ORDER BY EndPeriod ASC";
		
		$res = $this->m_dbConn->select($sql);	
		
		$logMsg .= "<br />Updated Record : ";
		$logMsg .= implode(' || ', array_map(function ($entry) 
		{
			return $entry['HeadAmt'] . " | " . $entry['BeginPeriod'] . " To " . $entry['EndPeriod'];
		}, $res));
	
		$updateLogSql = "update `change_log` SET ChangedLogDec = '" . $this->m_dbConn->escapeString($logMsg) . "' WHERE ChangeLogID = '" . $logID . "'";
		$resultUpdateLog = $this->m_dbConn->update($updateLogSql);
	}
	
	public function GetDateByOffset($myDate, $Offset)
	{
		$datetime1 = new DateTime($myDate);
		$newDate = $datetime1->modify($Offset . ' day');
		return $newDate->format('Y-m-d');	
	}
	
	public function getCurrentPeriod()
	{
		$todayDate = $this->display_pg->curdate();
		
		$sql = "Select periodtbl.ID from period as periodtbl JOIN society as societytbl on periodtbl.Billing_cycle = societytbl.bill_cycle where '" . $todayDate . "' >= periodtbl.BeginingDate and '" . $todayDate . "' <= periodtbl.EndingDate";
		
		$result = $this->m_dbConn->select($sql);
		return $result[0]['ID'];
	}
	
	function fetch_details($unit, $head, $bill_type)
	{
		$sqlMember = "Select unittbl.unit_no, membertbl.owner_name from unit as unittbl JOIN member_main as membertbl on membertbl.unit = unittbl.unit_id where unittbl.unit_id = '" . $unit . "'";
		$resultMember = $this->m_dbConn->select($sqlMember);
		
		$sql = "Select mastertbl.AccountHeadAmount as amt, mastertbl.BeginPeriod, mastertbl.EndPeriod, ledgertbl.ledger_name from unitbillmaster as mastertbl JOIN ledger as ledgertbl on mastertbl.AccountHeadID = ledgertbl.id where mastertbl.UnitID = '" . $unit . "' and mastertbl.AccountHeadID = '" . $head . "' and mastertbl.BillType = '" . $bill_type . "' order by mastertbl.BeginPeriod ASC";
		$result = $this->m_dbConn->select($sql);
		
		$msg = '';
		$msg .= "Unit : " . $resultMember[0]['unit_no'];
		$msg .= "<br/>Owner : " . $resultMember[0]['owner_name'];
		
		if($result)
		{
			$msg .= "<br/><br/>Ledger : " . $result[0]['ledger_name'];
			
			$msg .= "<br/><br/>Details :";
			
			for($iCnt = 0 ; $iCnt < sizeof($result) ; $iCnt++)
			{
				$fromDate = date('F Y', strtotime($result[$iCnt]['BeginPeriod']));
				$toDate = date('F Y', strtotime($result[$iCnt]['EndPeriod']));
				
				if($result[$iCnt]['EndPeriod'] == PHP_MAX_DATE)
				{
					$toDate = "Lifetime";
				}
				
				$msg .= "<br/><b>From : </b>[" . $fromDate . "] <b>To : </b>[" . $toDate . "] <b>Amount : </b>[" . $result[$iCnt]['amt'] . "]";
			}
		}
		else
		{
			$msg .= "<br/><br/>No details to display";
		}
		
		echo $msg;
	}
	
	function get_report($wing_id,$year_id,$period_id,$bill_type)
	{
		if($wing_id == 0)
		{
			$sql01 = "SELECT `unit_id` FROM `unit` WHERE `status` = 'Y' ORDER BY `sort_order`";
			$sql11 = $this->m_dbConn->select($sql01);
		}
		else
		{
			$sql01 = "SELECT `unit_id` FROM `unit` WHERE `status` = 'Y' AND `wing_id` = '".$wing_id."' ORDER BY `sort_order`";
			$sql11 = $this->m_dbConn->select($sql01);
		}
		
		$sql07 = "SELECT `society_name` FROM `society` WHERE `society_id` = '".$_SESSION['society_id']."'";
		$sql77 = $this->m_dbConn->select($sql07);
		
		$sql08 = "SELECT `BeginingDate`, `EndingDate` FROM `period` WHERE `ID` = '".$period_id."'";
		$sql88 = $this->m_dbConn->select($sql08);
		
		if($wing_id == 0)
		{
			$wing = "All";			
		}
		else
		{
			$sql09 = "SELECT `wing` FROM `wing` WHERE `wing_id` = '".$wing_id."' AND `society_id` = '".$_SESSION['society_id']."'";
			$sql99 = $this->m_dbConn->select($sql09);
			$wing = $sql99[0]['wing'];
		}
		
		$sql03 = "SELECT `BillDate`, `DueDate` FROM `billregister` WHERE `PeriodID` = '".$period_id."' AND `BillType` = '".$bill_type."'";
		$sql33 = $this->m_dbConn->select($sql03);
		//echo "<br>";
		$current_bill_date = $sql33[0]['BillDate'];
		//echo "<br>";
		
		$sql12 = "SELECT `ID` FROM `period` WHERE `PrevPeriodID` = '".$period_id."'";
		$sql12_res = $this->m_dbConn->select($sql12);
		
		$sql13 = "SELECT `BillDate`, `DueDate` FROM `billregister` WHERE `PeriodID` = '".$sql12_res[0]['ID']."' AND `BillType` = '".$bill_type."'";
		$sql13_res = $this->m_dbConn->select($sql13);
		//echo "<br>";
		if($sql13_res <> "")
		{
			$next_period_bill_date = $sql13_res[0]['BillDate'];
		}
		else
		{
			$next_period_bill_date = date("Y-m-d");
		}
		$billtype = "";
		if($bill_type == 0)
		{
			$billtype = "Regular Bill";
		}
		if($bill_type == 1)
		{
			$billtype = "Suplementary Bill";
		}
		$period_string = "Member - " .$billtype." Receipt Report for period ".getDisplayFormatDate($current_bill_date)." to ".getDisplayFormatDate($next_period_bill_date)." for ".$wing." wing(s)." ;
		
		$table = "<table width='100%' style='text-align:center;' id='report_table'>
					<tr height='50px'>
						<span id='fileName' style='display:none'>".$sql77[0]['society_name']." (".$period_string.")</span>
						<th colspan='14'>".$sql77[0]['society_name']."</th>
					</tr>
					<tr height='30px'>
						<th colspan='14'>".$period_string."</th>
					</tr>
					<tr height='50px'>
						<th width='5%'>Sr. No.</th>
						<th width='7%'>Member's Code/<br>Flat No.</th>
						<th width='10%'>Member's Name</th>
						<th width='7%'>Bill Amount</th>
						<th width='7%'>Arrears</th>
						<th width='7%'>Amount Due</th>
						<th width='8%'>Voucher Date</th>
						<th width='8%'>Cheque Date</th>
						<th width='7%'>Cheque No./<br>Trans No.</th>
						<th width='7%'>Bank Name</th>
						<th width='7%'>Receipt Amount</th>
						<th width='7%'>TDS Amount</th>
						<th width='7%'>Returned Cheques</th>
						<th width='7%'>Dues C/F</th>
					</tr>";	
		
		$total_BillAmount = 0;
		$total_BillArrears = 0;
		$total_AmountDue = 0;
		$total_Amount = 0;
		$total_ReturnedCheques_Amount = 0;
		$total_TDS_Amount = 0;
		
		for($i = 0; $i < sizeof($sql11); $i++)
		{
			$total_arrears = 0;
			$dues = 0;
			$sql05 = "SELECT * FROM `billdetails` WHERE `UnitID` = '".$sql11[$i]['unit_id']."' AND `PeriodID` = '".$period_id."' AND `BillType` = '".$bill_type."'";
			$sql55 = $this->m_dbConn->select($sql05);
			
			$sql04 = "SELECT  cd.ChequeDate as Date, cd.*, v.VoucherNo,v.VoucherTypeID FROM `chequeentrydetails` as cd,voucher as v WHERE cd.id = v.RefNo and cd.`PaidBy` = '".$sql11[$i]['unit_id']."' AND cd.`BillType` = '".$bill_type."' AND cd.`VoucherDate` BETWEEN '".$current_bill_date."' AND '".$next_period_bill_date."' and v.RefTableID = '".TABLE_CHEQUE_DETAILS."' AND v.VoucherTypeID ='".VOUCHER_RECEIPT."' group by v.VoucherNo";
			
			$sql44 = $this->m_dbConn->select($sql04);
			
			$sqlcreditresult = array();
			$sqlcredit = "SELECT  cdn.`Date` as Date, cdn.ID, cdn.`Date` as VoucherDate, cdn.`TotalPayable` as Amount, cdn.Note_Type  FROM `credit_debit_note` as cdn, `voucher` as v Where cdn.ID = v.RefNo and cdn.UnitID = '".$sql11[$i]['unit_id']."' AND cdn.`BillType` = '".$bill_type."' AND cdn.`Date` BETWEEN '".$current_bill_date."' AND '".$next_period_bill_date."' and v.RefTableID = '".TABLE_CREDIT_DEBIT_NOTE."' AND v.VoucherTypeID ='".VOUCHER_CREDIT_NOTE."' group by v.VoucherNo";
			$sqlcreditresult = $this->m_dbConn->select($sqlcredit);
			
			for($k = 0 ; $k < count($sqlcreditresult); $k++)
			{
				$sqlcreditresult[$k]['ChequeNumber'] =  'Credit Note';
				$sqlcreditresult[$k]['PayerBank'] = '-';
				$sqlcreditresult[$k]['PayerChequeBranch'] =  '-';
				$sqlcreditresult[$k]['IsReturn'] = 0; 
			}
			
			if(!empty($sqlcreditresult))
			{
				if(!empty($sql44))
				{
					//Merging CreditDebit and Receipt array
					$sql44 = array_merge($sql44, $sqlcreditresult);
				}
				else
				{
					$sql44 = $sqlcreditresult;
				}
			}
		
			$total_arrears = $sql55[0]['PrincipalArrears'] + $sql55[0]['InterestArrears'];
		
			$sql06 = "SELECT u.`unit_no`, mm.`owner_name`,mm.member_id FROM `unit` u, `member_main` mm WHERE u.`unit_id`=mm.`unit` AND u.`unit_id` = '".$sql11[$i]['unit_id']."' AND mm.ownership_status = 1 ";
			$sql66 = $this->m_dbConn->select($sql06);		
		
			$table .= "<tr height='50px' valign='middle'>
						<td>".($i + 1)."</td>
						<td>".$sql66[0]['unit_no']."</td>
						<td><a href='view_member_profile.php?scm&id=".$sql66[0]['member_id']."&tik_id=". time() ."&m&view' target='_blank'>".$sql66[0]['owner_name'] ."</a></td>
						<td><a href='Maintenance_bill.php?UnitID=".$sql11[$i]['unit_id'] ."&PeriodID=".$period_id. "&BT=".$bill_type. "' target='_blank'>".number_format($sql55[0]['CurrentBillAmount'],2)."</a></td>
						<td>".number_format($total_arrears,2)."</td>
						<td><a href='member_ledger_report.php?uid=".$sql11[$i]['unit_id']."' target='_blank'>".number_format($sql55[0]['TotalBillPayable'],2)."</a></td>
						<td>".getDisplayFormatDate($sql44[0]['VoucherDate'])."</td>
						<td>".getDisplayFormatDate($sql44[0]['ChequeDate'])."</td>
						<td>".$sql44[0]['ChequeNumber']."</td>
						<td>".$sql44[0]['PayerBank']."</td>";
			
			$total_BillAmount = $total_BillAmount + $sql55[0]['CurrentBillAmount'];
			$total_BillArrears = $total_BillArrears + $total_arrears;
			$total_AmountDue = $total_AmountDue + $sql55[0]['TotalBillPayable'];
			
			if($sql44[0]['IsReturn'] == 1)
			{
				$table .= "<td>-</td><td>-</td>	
				<td><a href='print_voucher.php?vno=".base64_encode($sql44[0]['VoucherNo']) ."&type=".base64_encode($sql44[0]['VoucherTypeID']). "' target='_blank'>".number_format($sql44[0]['Amount'],2)."</a></td>";	
				$table .= "<td></td>";
				$total_ReturnedCheques_Amount = $total_ReturnedCheques_Amount + $sql44[0]['Amount'];
			}
			else if($sql44[0]['IsReturn'] == 0)
			{
				if($sql44[0]['Note_Type'] == CREDIT_NOTE)
				{
					$table .= "<td><a href='Invoice.php?debitcredit_id=".$sql44[0]['ID']."&UnitID=".$sql11[$i]['unit_id']."&NoteType=".CREDIT_NOTE."' target='_blank'>".number_format($sql44[0]['Amount'],2)."</a></td>";							
				}
				else
				{
					$table .= "<td><a href='print_voucher.php?vno=".base64_encode($sql44[0]['VoucherNo']) ."&type=".base64_encode($sql44[0]['VoucherTypeID']). "' target='_blank'>".number_format($sql44[0]['Amount'],2)."</a></td>";
				}
				$table .= "<td>".number_format($sql44[0]['TDS_Amount'],2)."</td>	
						   <td></td>";
				
				$sql55[0]['TotalBillPayable'] = $sql55[0]['TotalBillPayable'] - $sql44[0]['Amount'] - $sql44[0]['TDS_Amount'];
				
				$table .= "<td><a href='member_ledger_report.php?uid=".$sql11[$i]['unit_id']."' target='_blank'>".number_format($sql55[0]['TotalBillPayable'],2)."</a></td>";
				
				$total_Amount = $total_Amount + $sql44[0]['Amount'];
				$total_TDS_Amount += $sql44[0]['TDS_Amount'];
			}
			
			$table .= "</tr>";
			
			if(sizeof($sql44) > 1)
			{
				for($j = 1; $j < sizeof($sql44); $j++)
				{
					$table .= "<tr height='50px' valign='middle'>
							   	<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td>".getDisplayFormatDate($sql44[$j]['VoucherDate'])."</td>
								<td>".getDisplayFormatDate($sql44[$j]['ChequeDate'])."</td>
								<td>".$sql44[$j]['ChequeNumber']."</td>
								<td>".$sql44[$j]['PayerBank']."</td>";
					
					if($sql44[$j]['IsReturn'] == 1)
					{
						$table .= "<td>-</td><td>-</td>
						<td><a href='print_voucher.php?vno=".base64_encode($sql44[$j]['VoucherNo']) ."&type=".base64_encode($sql44[$j]['VoucherTypeID']). "' target='_blank'>".number_format($sql44[$j]['Amount'],2)."</a></td>";
						$table .= "<td></td>";
						$total_ReturnedCheques_Amount = $total_ReturnedCheques_Amount + $sql44[$j]['Amount'];
					}
					else if($sql44[$j]['IsReturn'] == 0)
					{
						if($sql44[$j]['Note_Type'] == CREDIT_NOTE)
						{
							$table .= "<td><a href='Invoice.php?debitcredit_id=".$sql44[$j]['ID']."&UnitID=".$sql11[$i]['unit_id']."&NoteType=".CREDIT_NOTE."' target='_blank'>".number_format($sql44[$j]['Amount'],2)."</a></td>";							
						}
						else
						{
							$table .= "<td><a href='print_voucher.php?vno=".base64_encode($sql44[$j]['VoucherNo']) ."&type=".base64_encode($sql44[$j]['VoucherTypeID']). "' target='_blank'>".number_format($sql44[$j]['Amount'],2)."</a></td>";	
						}
						
				  		$table .= "<td>".number_format($sql44[$j]['TDS_Amount'],2)."</td>
						<td></td>";
								   
						$sql55[0]['TotalBillPayable'] = $sql55[0]['TotalBillPayable'] - $sql44[$j]['Amount'] - $sql44[$j]['TDS_Amount'];
						$table .= "<td style='border-style:solid; border-width: 0px 1px 1px 1px;'>".number_format($sql55[0]['TotalBillPayable'],2)."</td>";
						
						$total_Amount = $total_Amount + $sql44[$j]['Amount'];
						$total_TDS_Amount += $sql44[$j]['TDS_Amount'];
					}
					
					$table .= "</tr>";
				}
			}
		}
		
		$table .= "<tr height='50px' valign='middle'>
						<td colspan='3'>Total</td>
						<td>".number_format($total_BillAmount,2)."</td>
						<td>".number_format($total_BillArrears,2)."</td>
						<td>".number_format($total_AmountDue,2)."</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td>".number_format($total_Amount,2)."</td>
						<td>".number_format($total_TDS_Amount,2)."</td>
						<td>".number_format($total_ReturnedCheques_Amount,2)."</td>
						<td></td>
				  </tr>";

		$table .= "</table>";
		
		return $table;		
	}
}
?>
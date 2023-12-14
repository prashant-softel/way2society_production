<?php
	include_once("../classes/billmaster.class.php");
	include_once("../classes/include/dbop.class.php");
	include_once("../classes/changelog.class.php");


	$dbConn = new dbop();
	$dbConnRoot = new dbop(true);
	$obj_billmaster = new billmaster($dbConn);
	$m_objLog = new changeLog($dbConn, $dbConnRoot);
	if(isset($_REQUEST['getunit']))
	{
		$get_unit = $obj_billmaster->fetch_unit_data();
	}
	else if(isset($_REQUEST['acchead']))
	{
		echo $get_acchead = $obj_billmaster->fetch_acc_head($_REQUEST['billtype']);
	}
	else if(isset($_REQUEST['update']))
	{
        $wing = $_REQUEST['wwid'];
		$aryHead = json_decode($_REQUEST['head']);
		$aryAmt = json_decode($_REQUEST['amt']);
		
		$unit = $_REQUEST['unit'];
		
		$period = $_REQUEST['period'];
		$start_period = $_REQUEST['start_period'];
		$end_period = $_REQUEST['end_period'];
		$bill_type = $_REQUEST['bill_type'];
		$sql1 ="SELECT u.unit_no,u.area,w.wing,CONCAT(w.wing,'-', u.unit_no) as unit_no FROM `unit` as u join wing as w on u.wing_id =w.wing_id where u.unit_id='".$unit."'";
		$res1 = $dbConn->select($sql1);
		$aryResult = array('unit no'=>$res1[0]['unit_no'],'area'=>$res1[0]['area'],'period'=>$period,'start period'=>$start_period,'end_period'=>$end_period,'bill type'=>$bill_type);
		for($iCnt = 0; $iCnt < sizeof($aryHead); $iCnt++)
		{
			$update_master = $obj_billmaster->update_billmaster($unit, $aryHead[$iCnt], $aryAmt[$iCnt], $period, $start_period, $end_period, $bill_type);
        
		 $sql = "SELECT ledger_name FROM `ledger` where id='".$aryHead[$iCnt]."'";
         $res = $dbConn->select($sql);	
		 $aryResult[$res[0]['ledger_name']] = $aryAmt[$iCnt];
		}
		  //print_r($aryResult);
		  $sql2 = "SELECT ID from `unitbillmaster` where UnitID ='".$unit."'";
		  $res2 = $dbConn->select($sql2);	
          $logArr = json_encode($aryResult);
		  $checkPreviousLogQry = "SELECT ChangeLogID FROM change_log WHERE ChangedKey = '".$res2[0]['ID']."' AND ChangedTable = '".TABLE_BILL_MASTER."'";
		  $previousLogDetails = $dbConn->select($checkPreviousLogQry);
		  $previousLogID = $previousLogDetails[0]['ChangeLogID'];
		  $m_objLog->setLog($logArr, $_SESSION['login_id'], TABLE_BILL_MASTER, $res2[0]['ID'], EDIT, $previousLogID);
                       
		echo 'Records Updated';
	}
	else if(isset($_REQUEST['getdata']))
	{
		$aryUnit = json_decode($_REQUEST['unit']);
		$period = $_REQUEST['period'];
		$fetch_data = $obj_billmaster->fetch_data($aryUnit, $period, $_REQUEST['bill_type']);
	}
	else if(isset($_REQUEST['details']))
	{
		$get_details = $obj_billmaster->fetch_details($_REQUEST['unit'], $_REQUEST['head'], $_REQUEST['bill_type']);
	}
?>
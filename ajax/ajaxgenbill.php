<?php
	include_once("../classes/genbill.class.php");
	include_once("../classes/include/dbop.class.php");
	include_once ("../classes/include/exportToExcel.php");
	include_once("../classes/parking.class.php");
	$dbConn = new dbop();
	$obj_genbill = new genbill($dbConn);
   	$objParking = new Parking($dbConn);
	
	    //remove all empty spaces after php closing brackets
        ob_clean();
	
	if(isset($_REQUEST['getnote']))
	{
		$get_notes = $obj_genbill->getNotes($_REQUEST['society'], $_REQUEST['period'], $_REQUEST['supplementary_bill']);
	}
	else if(isset($_REQUEST['setnote']))
	{
		$set_notes = $obj_genbill->setNotes($_REQUEST['note'], $_REQUEST['period'], $_REQUEST['supplementary_bill']);
	}
	else if(isset($_REQUEST['getsize']))
	{
		$get_font = $obj_genbill->getFontSize($_REQUEST['society'], $_REQUEST['period'], $_REQUEST['supplementary_bill']);
	}
	else if(isset($_REQUEST['setfont']))
	{
		$set_font = $obj_genbill->setFont($_REQUEST['font'], $_REQUEST['period'], $_REQUEST['supplementary_bill']);
	}
	else if(isset($_REQUEST['getdate']))
	{
		$bShowDueDate = true;
		if(isset($_REQUEST['hide_duedate']) && $_REQUEST['hide_duedate'] == 1 && $_REQUEST['supplementary_bill'] ==1)
		{
			$bShowDueDate = false;
		}
		
		$get_dates = $obj_genbill->getBillAndDueDate($_REQUEST['period'], $_REQUEST['society'], $_REQUEST['supplementary_bill'],$bShowDueDate);
		echo $get_dates['BillDate'] . "@@@" . $get_dates['DueDate'] . "@@@" . $get_dates['DueDateToDisplay'];
	}
	else if(isset($_REQUEST['Export']))
	{		
		$details = $obj_genbill->getCollectionOfDataToDisplay($_REQUEST['society_id'], $_REQUEST['wing_id'], $_REQUEST['unit_id'], $_REQUEST['period_id']);			
		exportExcel($details);
	}
	else if(isset($_REQUEST['method']) && $_REQUEST["method"]=="BillEdit")
	{
		echo $_REQUEST["method"]."@@@";		
		$Detail = json_decode(str_replace('\\', '', $_REQUEST['data']), true);
		//print_r($Detail);
		$CurrentBillInterestAmount=$_REQUEST["InterestOnPrincipleDue"];
		//echo "<br>CurrentBillInterestAmount:".$CurrentBillInterestAmount;
		$InterestArrears =$_REQUEST["IntrestOnPreviousarrears"];
		$PrincipalArrears=$_REQUEST["PrinciplePreviousArrears"];
		$AdjustmentCredit=$_REQUEST["AdjustmentCredit"];
		$UnitID=$_REQUEST["UnitID"];
		$PeriodID=$_REQUEST["PeriodID"];
		$bill_date=$_REQUEST["bill_date"];
		$SupplementaryBill = $_REQUEST["SupplementaryBill"];
		$Note = $_REQUEST['Note'];
		$EditableInvoiceNo = $_REQUEST['EditableInvoiceNo'];
		$ExitingInvoiceUnitID = $_REQUEST['ExitingInvoiceUnitID'];
		$IsCallUpdtCnt = $_REQUEST['IsCallUpdtCnt'];
		$VoucherCounter = $_REQUEST['VoucherCounter'];

		if($_REQUEST['RequestType']==edt)
		{	
		echo "Edit method";
	
		
		$obj_genbill->BillDetailsUpdate($Detail,$UnitID,$PeriodID,$CurrentBillInterestAmount,$InterestArrears,$PrincipalArrears,$AdjustmentCredit, $SupplementaryBill);
		}
		//** Is invoice Edit is identify which method to call if no then it call addnewsaleInvoice or if yes the editsale invoice
		else if($_REQUEST['RequestType'] == 'Invoice' && $_REQUEST['IsInvoiceEdit'] == 0)
		{	
		//***Call gen class file method for create new invoice bill
			
			$obj_genbill->AddNewSalesInvoice($Detail,$UnitID,$bill_date,$Note,0,0,$IsCallUpdtCnt);
		}
		else if($_REQUEST['RequestType'] == 'Invoice' && $_REQUEST['IsInvoiceEdit'] == 1)
		{
			echo '<BR>VoucherCounter '.$VoucherCounter;
			$obj_genbill->EditSalesInvoice($Detail,$UnitID,$bill_date,$Note,$EditableInvoiceNo,$ExitingInvoiceUnitID,$IsCallUpdtCnt,$VoucherCounter);
		}
	}
	else if(isset($_REQUEST['method']) && $_REQUEST["method"]=="regenerate_bill")
	{
		// This function regenerate the bill
		
		echo $obj_genbill->ReGenerateBill($_REQUEST['UnitID'],$_REQUEST['PeriodID'],$_REQUEST['BillType']);
	}
	else if(isset($_REQUEST['method']) && $_REQUEST['method']=='deleteInvoice')
	{
		//***** Here calling delete method from class to delete the invoice bill
		
		$InvoiceNumber=$_REQUEST['InvoiceNumber'];
		$UnitID=$_REQUEST['UnitID'];
		$billdate=$_REQUEST['billdate'];
		 // 1 set for delete the invoice whole entry because from another function we pass 0 to  when invoice edit
		$obj_genbill->deleteInvoice($InvoiceNumber,$UnitID,$billdate,1);
	}
	else if($_REQUEST['method'] == 'AddCreditDebitNote')
	{
		$Detail = json_decode(str_replace('\\', '', $_REQUEST['data']), true);
		$GUID = "";
		$result = $obj_genbill->AddCreditDebitNote($Detail,$_REQUEST['UnitID'],$_REQUEST['bill_date'],$_REQUEST['BillType'],$_REQUEST['NoteType'],$_REQUEST['Note'],$_REQUEST['IsEditModeSet'],$_REQUEST['editableCreditDebitId'],$_REQUEST['IsCallUpdtCnt'],$_REQUEST['VoucherNumber'],0,$GUID);
	}
	else if($_REQUEST['method'] == 'deleteDebitorCredit')
	{
		$result = $obj_genbill->deleteDebitorCredit($_REQUEST['DebitorCreditID'],$_REQUEST['NoteType']);
	}
	else if(isset($_REQUEST['method']) && $_REQUEST['method']=='Checktaxable')
	{
		$checktaxableLedger="select taxable as tax from ledger where id = '".$_REQUEST['showtax']."' ";
		$res = $dbConn->select($checktaxableLedger);
		echo  $res[0]['tax'];
	}
	else if(isset($_REQUEST['method']) && $_REQUEST['method']=='UpdateGSTNoThresholdFlag')
	{
		$result = $obj_genbill->UpdateGSTNoThresholdFlag($_REQUEST['periodID']);
	}
	else if(isset($_REQUEST['method']) && $_REQUEST['method']=='ShowGSTNoThresholdFlag')
	{
		$sql = "Update appdefault set APP_DEFAULT_PERIOD = '".$_REQUEST['periodID']."'";
		$res = $dbConn->update($sql);
		$_SESSION['default_period'] = $_REQUEST['periodID'];
	}
	else if(isset($_REQUEST['method']) && $_REQUEST["method"]=="bcheckLatestPeriod")
	{
		$bLatestPeriod = 'failed';		
		$sql = "SELECT max(PeriodID) as PeriodID FROM `billdetails` WHERE BillType = '" . $_REQUEST['BT'] . "'";
		$res = $dbConn->select($sql);
                //echo $res[0]['PeriodID'].":".$_REQUEST["iPeriodID"];
		//if(sizeof($res) > 0 && $res[0]['PeriodID'] ==$_REQUEST["iPeriodID"])
		{
		    $bLatestPeriod = 'success';
		}
		echo $bLatestPeriod;
	}
     else if(isset($_REQUEST['method']) && $_REQUEST["method"]=="performDelete")
	{
				$IsSupplementaryBill = false;	
				if($_REQUEST["IsSupplemenataryBill"] == '1')
				{
					$IsSupplementaryBill = true;	
				}
				
                if($_REQUEST["iUnitID"] == '0')
                {
                    //fetch all unit
                    $sql = "SELECT `unit_id` FROM `unit`";
                    $res = $dbConn->select($sql);
                    for($i = 0;$i < sizeof($res);$i++)
                    {
                       $obj_genbill->DeleteBillDetails($res[$i]["unit_id"], $_REQUEST["iPeriodID"],true,true,$IsSupplementaryBill);  
                    }
                    $obj_genbill->delTrace = "Deleted Bill for Unit <All> PeriodID <".$_REQUEST["iPeriodID"].">";
                    $obj_genbill->m_objLog->setLog($obj_genbill->delTrace, $_SESSION['login_id'], 'billdetails', 0);
                }
                else
                {
                  $obj_genbill->DeleteBillDetails($_REQUEST["iUnitID"], $_REQUEST["iPeriodID"],true,false,$IsSupplementaryBill); 
                }
	}
	else if(isset($_REQUEST['method']) && $_REQUEST['method'] == "UpdateGenBillFromParking")
	{
		$UnitID = json_decode($_REQUEST['unitId']);
		$amount = json_decode($_REQUEST['amount']);
		$ledgerName = json_decode($_REQUEST['ledgerName']);
		$yearId = urldecode($_REQUEST['yearId']);
		$periodId = urldecode($_REQUEST['periodId']);
		$ledgerId = array();
		for($i=0;$i<sizeof($ledgerName);$i++)
		{
			$sql = "select id from ledger where ledger_name = '".$ledgerName[$i]."' and society_id = '".$_SESSION['society_id']."'";
			$result = $dbConn->select($sql);
			$ledgerId[$i] = $result[0]['id'];
		}
		$details = array();
		for($i = 0;$i<sizeof($amount);$i++)
		{
			$details[$i]['UnitId'] = $UnitID[$i];
			$details[$i]['Amt'] = $amount[$i];
			$details[$i]['Head'] = $ledgerId[$i];
		}
		$res = $obj_genbill->updateBillMasterDetailsFromParkingPage($details,$yearId,$periodId); 
		echo $res;
	}
	else if(isset($_REQUEST['method']) && $_REQUEST['method'] == "getPeriodByYear")
	{
		$year = urldecode($_REQUEST['YearId']);
			$sqlForYear = "Select ID, Type from period where YearID = '".$year."' and status = 'Y'";
			//echo $sqlForYear;
			$res = $dbConn->select($sqlForYear);
			//var_dump($res);
			$periodDetails = "";
			$periodDetails = "<select name='period' id='period'><option value='0'>Select Period</option>";
			for($i=0;$i<sizeof($res);$i++)
			{
				$periodDetails .= "<option value = '".$res[$i]['ID']."'>".$res[$i]['Type']."</option>";
			}
			$periodDetails .= "</select>";
			echo $periodDetails;
	}
	else if(isset($_REQUEST['method']) && $_REQUEST['method'] == "getMemberListing")
	{
		$yearId = $_REQUEST['year'];
		$periodId = $_REQUEST['period'];
		echo $objParking->MemberParkingListings(true,$yearId,$periodId);
	}
?>
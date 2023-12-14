<?php
include('config_script.php');
include_once("../../../classes/defaults.class.php");
$m_dbConn = new dbop();
$bSuccess = true;

if(isset($bSuccess))
{
	try
	{
		$startNo = (int)$_REQUEST['start'];;
		$endNo = (int)$_REQUEST['end'];
		$default_year = (int)$_REQUEST['default_year'] - 1;
		
		$NumberOfSociety = 0;
		$body = '';
		$dbPrefix = "hostmjbt_society";
		$isImportSuccess = false;
		
		echo '<br/><br/>Executing code ... ' . $dbPrefix . $startNo . ' to ' . $dbPrefix . $endNo;
		
		for($iCount = $startNo; $iCount <= $endNo; $iCount++)
		{
			$dbName = $dbPrefix . $iCount;
			echo '<br/><br/>Connecting DB : ' . $dbName;
		
			if(!$m_dbConn->isConnected)
			{
				echo ' .....Connection Failed';	
			}
			else
			{
				
				echo ' ....Connected';	
				
				$Periodsql = "SELECT ID,BeginingDate,EndingDate FROM `period` where IsYearEnd = 1 and YearID = '".$default_year."'";
				$PeriodDetails = $m_dbConn->select($Periodsql);
				
				if(empty($PeriodDetails))
				{
					echo '<br/><br/>Period Not Found For Previous Year';
					continue;
				}
				
				$Select = "SELECT LedgerID , sum(debit)- sum(credit) as dues FROM `assetregister` where SubCategoryID = '".$_SESSION['default_due_from_member']."' and `Date` < '".$PeriodDetails[0]['BeginingDate']."' group by ledgerID";
				$MemberDuesDetails = $m_dbConn->select($Select);
				
				if(empty($MemberDuesDetails))
				{
					echo '<br/><br/> No Records Exits In Asset Table Of Previuos Year';
					continue;
				}
				
				$BillNumber = 0;
				$PeriodID = $PeriodDetails[0]['ID'];
				$DueDate2 =  $PeriodDetails[0]['EndingDate'];
				$society_id = $_SESSION['society_id'];
				$BillType = 0;
				$datam09 = '';
				
				for($i = 0 ; $i < sizeof($MemberDuesDetails); $i++)
				{
					$Principal_PrevYear = $MemberDuesDetails[$i]['dues'];
					$Interest_PrevYear = 0;
					$CurrentBillAmount = $MemberDuesDetails[$i]['dues'];
					$PrincipalArrears2 = 0;
					$InterestArrears2 = 0;
					$BillDate2 = $PeriodDetails[0]['BeginingDate'];
					$TotalBillPayable =  $MemberDuesDetails[$i]['dues'];
					$BillNumber++;
					
					$unit_id = $MemberDuesDetails[$i]['LedgerID'];
					$sqlbillregister = "Select  * from `billregister`  where SocietyID = '" . $society_id . "' and PeriodID = '" . $PeriodID . "' and BillType = '".$BillType."' ";
					$resultbillregister = $m_dbConn->select($sqlbillregister);
					$sqlbilldetails = "Select  * from `billdetails`  where UnitID = '" . $unit_id . "' and PeriodID = '" . $PeriodID . "' and BillType = '".$BillType."'";
					$resultbilldetails = $m_dbConn->select($sqlbilldetails);
					
					if($datam09 == '')
					{
						if(sizeof($resultbillregister) == 0)
						{
							$insert_into_billregister = "insert into `billregister`(SocietyID,PeriodID,CreatedBy,BillDate,DueDate,Notes,LatestChangeID,BillType) values(".$_SESSION['society_id'].",'".$PeriodID."','" .$_SESSION['login_id']. "','".$BillDate2."','".$DueDate2."','Import Data','0','".$BillType."') ";
							$datam09 = $m_dbConn->insert($insert_into_billregister);
						}
						else
						{
							$datam09 = $resultbillregister[0]['ID'];
						}
					}
					
					if(!empty($resultbilldetails))
					{
						$sqlresultbilldetailsDelete = "DELETE FROM `billdetails` WHERE UnitID = '" . $unit_id . "' and PeriodID = '" . $PeriodID . "' and BillType = '".$BillType."'";
						$resultbilldetailsDelete = $m_dbConn->delete($sqlresultbilldetailsDelete);
						
						if($resultbilldetailsDelete == true)
						{
							$insert_into_billdetails="insert into `billdetails`(UnitID,PeriodID,BillRegisterID,BillNumber,BillSubTotal,BillInterest,CurrentBillAmount,PrincipalArrears,InterestArrears,TotalBillPayable,BillType) 
							values('".$unit_id."','".$PeriodID."','".$datam09."','$BillNumber','$Principal_PrevYear','$Interest_PrevYear','$CurrentBillAmount','$PrincipalArrears2','$InterestArrears2','$TotalBillPayable','".$BillType."') ";
							$data=$m_dbConn->insert($insert_into_billdetails);
							$isImportSuccess = true;
						}
					}
					else
					{
						if(sizeof($resultbilldetails) == 0)
						{
							$insert_into_billdetails="insert into `billdetails`(UnitID,PeriodID,BillRegisterID,BillNumber,BillSubTotal,BillInterest,CurrentBillAmount,PrincipalArrears,InterestArrears,TotalBillPayable,BillType) 
							values('".$unit_id."','".$PeriodID."','".$datam09."','$BillNumber','$Principal_PrevYear','$Interest_PrevYear','$CurrentBillAmount','$PrincipalArrears2','$InterestArrears2','$TotalBillPayable','".$BillType."') ";
							$data=$m_dbConn->insert($insert_into_billdetails);
							$isImportSuccess = true;
						}		
					}
				}
			
			if($isImportSuccess == true)
			{
				echo '<br>Opening Balance Updated Successfully in BillDetails Table';
			}
			else
			{
				echo '<br>Opening Balance Not Updated';
			}
			
				echo "<br/>Connection Closed";
			}
		}
	}
	catch(Exception $exp)
	{
		echo $exp;
	}
}
?>
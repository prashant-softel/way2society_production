<?php
include('config_script.php');
include_once("../../../classes/defaults.class.php");
include_once ("../../../classes/include/dbop.class.php");
include_once("../../../classes/dbconst.class.php");
include_once("../../../classes/BalanceSheet.class.php");
include_once("../../../classes/utility.class.php");
session_start(); 
$parent=0;
$bSuccess = true;

if(isset($bSuccess))
{
	try
	{
		$startNo = (int)$_REQUEST['start'];;
		$endNo = (int)$_REQUEST['end'];
		//$default_year = (int)$_REQUEST['default_year'];
		$InitialSocietyID = $_SESSION['society_id'];
		$InitialDBName = $_SESSION['dbname'];
		$NumberOfSociety = 0;
		$body = '';
		$dbPrefix = "hostmjbt_society";
		$isImportSuccess = false;
		
		echo '<br/><br/>Executing code ... ' . $dbPrefix . $startNo . ' to ' . $dbPrefix . $endNo;
		
		$body .= "<html><head><style>body { font: normal medium/1.4 sans-serif;}table {border-collapse: collapse;width: 100%;}th, td {padding: 0.25rem;text-align: left;border: 1px solid #ccc;}tbody tr:nth-child(odd) {background: #eee;} label{color:black}</style></head>";
		$body .= "<div align='center' style='width:100%;font-weight: bold;'><div><label>BalanceSheet Mismatch Society List</label></div>";
		$body .= "<table  align='center' style='border-collapse: collapse;border:1px solid black;bgcolor:gray;color:black'>";
		$body .= "<thead><tr>";
		$body .= "<th>Sr No.</th>";
		$body .= "<th>Society ID</th>";
		$body .= "<th>Database Name</th>";
		$body .= "<th>Society Name</th>";
		$body .= "<th>Begin Date</th>";
		$body .= "<th>End Date</th>";
		$body .= "<th>Liabilty Total</th>";
		$body .= "<th>Asset Total</th>";
		$body .= "<th>Amount Difference</th>";
		$body .= "</tr></thead><tbody>";
		$counter = 0;
		
		$IsDateSet = false;
		$BeginDate = getDBFormatDate($_REQUEST['beginDate']);
		$EndDate = getDBFormatDate($_REQUEST['endDate']);
				
		for($iCount = $startNo; $iCount <= $endNo; $iCount++)
		{
			$dbName = $dbPrefix . $iCount;
			//echo '<br/><br/>Connecting DB : ' . $dbName;
			
			$m_dbConn = new dbop(false,$dbName);
			$m_dbConnRoot = new dbop(true);
			
			if($m_dbConn->isConnected == false)
			{
				//echo ' .....Connection Failed';	
			}
			else
			{
				/*
				if($IsDateSet == false)
				{
					$GetDateDetailsQuery = "SELECT BeginingDate, EndingDate FROM `year` where YearID = '".$default_year."'";
					$DateDetails = $m_dbConn->select($GetDateDetailsQuery);	
					$BeginDate = getDBFormatDate($DateDetails[0]['BeginingDate']);
					$EndDate = getDBFormatDate($DateDetails[0]['EndingDate']);
					$IsDateSet = true;			
				}*/

				$AssetDetails = $m_dbConn->select("SELECT sum(Debit) - sum(Credit) as AssetTotal FROM `assetregister` where `Date` between '".$BeginDate."' and '".$EndDate."'");
				
				$BanktDetails = $m_dbConn->select("SELECT sum(ReceivedAmount) - sum(PaidAmount) as BankTotal FROM `bankregister` where `Date` between '".$BeginDate."' and '".$EndDate."'");
				
				$ExpensetDetails = $m_dbConn->select("SELECT  sum(Debit) -sum(Credit) as ExpenseTotal FROM `expenseregister` where `Date` between '".$BeginDate."' and '".$EndDate."'");
				
				$IncomeDetails = $m_dbConn->select("SELECT sum(Credit) - sum(Debit) as IncomeTotal FROM `incomeregister` where `Date` between '".$BeginDate."' and '".$EndDate."'");
				
				$LiabilityDetails = $m_dbConn->select("SELECT sum(Credit) - sum(Debit) as LiabiltyTotal FROM `liabilityregister` where `Date` between '".$BeginDate."' and '".$EndDate."'");						
				
				
				$ASSETTotal = $AssetDetails[0]['AssetTotal']+$BanktDetails[0]['BankTotal'];
				$ProfitNLoss = $IncomeDetails[0]['IncomeTotal'] - ($ExpensetDetails[0]['ExpenseTotal']);
				$LIABILITYTotal = $LiabilityDetails[0]['LiabiltyTotal']+$ProfitNLoss;
				
				//echo '<br>Asset Total '.$ASSETTotal;
				//echo '<br>ProfitNLoss Total '.$ProfitNLoss;
				//echo '<br>LIABILITYTotal Total '.$LIABILITYTotal;
				
				$getSocietyDetailsSql = "Select society_id, society_name from society";
				$SocietyDetails = $m_dbConn->select($getSocietyDetailsSql);
				
				if(empty($SocietyDetails))
				{
					//echo 'DataBase Is Empty';
					continue;
				}
			
			if ((abs(($LIABILITYTotal-$ASSETTotal)/$ASSETTotal) < 0.00001) == false) 
			{
				$DifferenceTotal = $LIABILITYTotal - $ASSETTotal;
				$counter++;
				$body .="<tr>";
				$body .= "<td>".$counter."</td>";
				$body .= "<td>".$SocietyDetails[0]['society_id']."</td>";
				$body .= "<td>".$dbName."</td>";
				$body .= "<td>".$SocietyDetails[0]['society_name']."</td>";
				$body .= "<td>".$BeginDate."</td>";
				$body .= "<td>".$EndDate."</td>";
				$body .= "<td>".$LIABILITYTotal."</td>";
				$body .= "<td>".$ASSETTotal."</td>";
				$body .= "<td>".$DifferenceTotal."</td>";
				$body .="</tr>";
			}
				//echo "<br/>Connection Closed";
			}
		}
		$body .="</tbody></table></html>";
		
		echo "<center><button type='button' name='ExportToExcel' id='ExportToExcel' onClick='ExportToExcel();' style='font-size: 25px;'>Export To Excel</button><center><br>";
		echo  $body;
		
		
	}
	catch(Exception $exp)
	{
		echo $exp;
	}
}

	
<?php
//include('config_script.php');
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
		
		//echo '<br/><br/>Executing code ... ' . $dbPrefix . $startNo . ' to ' . $dbPrefix . $endNo;
		
		$body .= "<html><head><style>body { font: normal medium/1.4 sans-serif;}table {border-collapse: collapse;width: 100%;}th, td {padding: 0.25rem;text-align: left;border: 1px solid #ccc;}tbody tr:nth-child(odd) {background: #eee;} label{color:black}</style></head>";
		$body .= "<div align='center' style='width:100%;font-weight: bold;'><div><label>Society List</label></div>";
		$body .= "<table  align='center' style='border-collapse: collapse;border:1px solid black;bgcolor:gray;color:black'>";
		$body .= "<thead><tr>";
		$body .= "<th>Sr No.</th>";
		$body .= "<th>Society Name</th>";
		$body .= "<th>Begin Date</th>";
		$body .= "<th>End Date</th>";
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
				$getSocietyNameQuery = "SELECT society_name FROM `society`";
				$society_details = $m_dbConn->select($getSocietyNameQuery);
				$getDate_Query = "SELECT BeginingDate, EndingDate FROM `period` where ID in (SELECT max(PeriodID) FROM `billregister`)";
				$getRegisterDate = $m_dbConn->select($getDate_Query);
				
			if (!empty($society_details) <> 0 && !empty($getRegisterDate)) 
			{
				$counter++;
				$body .="<tr>";
				$body .= "<td>".$counter."</td>";
				$body .= "<td>".$society_details[0]['society_name']."</td>";
				$body .= "<td>".$getRegisterDate[0]['BeginingDate']."</td>";
				$body .= "<td>".$getRegisterDate[0]['EndingDate']."</td>";
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

	
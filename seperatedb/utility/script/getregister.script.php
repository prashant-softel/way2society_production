<?php
	//$AppPath = preg_replace("!${_SERVER['SCRIPT_NAME']}$!", '', $_SERVER['SCRIPT_FILENAME']);
	include('config_script.php');
	error_reporting(0);
	function getResult($mMysqli, $sqlQuery)
	{
		$result = $mMysqli->query($sqlQuery);						
		if($result)
		{
			$count = 0;
			while($row = $result->fetch_array(MYSQL_ASSOC))
			{
				$data[$count] = $row;
				$count++;
			}											
		}	
		return $data;	
	}
	
	function indexByFirstDayOfMonth($data, $key, $transactionType, $previousArr) {
		foreach($data as $value) {
			//$dateArr = explode('-',$value[$key]);
			//$firstDateOfMonth = $dateArr[0].'-'.$dateArr[1].'-01';
			//$firstDateOfMonth = $dateArr[0].'-'.$dateArr[1].'-01';
			$firstDateOfMonth = date("M-Y", strtotime($value[$key]));
			$previousArr[$firstDateOfMonth][$transactionType] = $value[$transactionType];
		}
		return $previousArr;
	}
			
	//if(isset($_REQUEST['fromDate']) && isset($_REQUEST['toDate']))
	if(isset($_REQUEST['startDB']) && isset($_REQUEST['endDB']) && isset($_REQUEST['fromDate']) &&isset($_REQUEST['toDate'])
		&& $_REQUEST['startDB'] <> '' && $_REQUEST['endDB'] <> '' && $_REQUEST['fromDate'] <> ''  && $_REQUEST['toDate'] <> '')
		{
			///echo "Inside if";
		try
			{
			$hostname = DB_HOST;
			$username =DB_USER;
			$password = DB_PASSWORD;
			$dbPrefix = 'hostmjbt_society';
	
			
			$startNo = (int)$_REQUEST['startDB'];
			$endNo = (int)$_REQUEST['endDB'];
			
			$fromdate = DateTime::createFromFormat('d-m-Y', $_REQUEST['fromDate'])->format('Y-m-d');
			$todate = DateTime::createFromFormat('d-m-Y', $_REQUEST['toDate'])->format('Y-m-d');
		
 			//$d1=new DateTime($todate); 
 			//$d2=new DateTime($fromdate);                                  
 			//$Months = $d2->diff($d1); 
			//$howeverManyMonths = (($Months->y) * 12) + ($Months->m);
 
			$finalarray = [];
			$newArray = [];
			for($iCount = $startNo; $iCount <= $endNo; $iCount++)
			{
				$dbName = $dbPrefix . $iCount;
				
				$mMysqli = mysqli_connect($hostname, $username, $password, $dbName);
				if(!$mMysqli)
				{
					//echo '<br/>Connection Failed';	
				}
				else
				{
					$SocietyName = getResult($mMysqli,"SELECT society_name FROM `society`");
					$societyname = $SocietyName[0]['society_name'];
					//echo  "SELECT * FROM `chequeentrydetails` where DepositID = -2 and `VoucherDate` between '".$fromdate."' and '".$todate."'";
					$finalarray['NEFT'] = getResult($mMysqli, "SELECT COUNT(ID) as NEFT , VoucherDate FROM `chequeentrydetails` where DepositID = -2 and `VoucherDate` between '".$fromdate."' and '".$todate."' GROUP BY YEAR(VoucherDate), MONTH(VoucherDate) order by VoucherDate");
					
					
					$finalarray['CASH'] = getResult($mMysqli, "SELECT COUNT(ID) as CASH , VoucherDate FROM `chequeentrydetails` where DepositID = -3 and `VoucherDate` between '".$fromdate."' and '".$todate."' GROUP BY YEAR(VoucherDate), MONTH(VoucherDate) order by VoucherDate");
					
					$finalarray['PayTM'] = getResult($mMysqli, "SELECT COUNT(ID) as PayTM , VoucherDate FROM `chequeentrydetails` where DepositID = -4 and `VoucherDate` between '".$fromdate."' and '".$todate."' GROUP BY YEAR(VoucherDate), MONTH(VoucherDate) order by VoucherDate");
					
					$finalarray['CHEQUE'] = getResult($mMysqli, "SELECT COUNT(ID) as CHEQUE , VoucherDate FROM `chequeentrydetails` where  DepositID NOT IN (-2,-3,-4) and `VoucherDate` between '".$fromdate."' and '".$todate."' GROUP BY YEAR(VoucherDate), MONTH(VoucherDate) order by VoucherDate");
					
					$newArray[$societyname] = indexByFirstDayOfMonth($finalarray['NEFT'], 'VoucherDate', 'NEFT', []);
					$newArray[$societyname] = indexByFirstDayOfMonth($finalarray['CASH'], 'VoucherDate', 'CASH', $newArray[$societyname]);
					$newArray[$societyname] = indexByFirstDayOfMonth($finalarray['PayTM'], 'VoucherDate', 'PayTM', $newArray[$societyname]);
					$newArray[$societyname] = indexByFirstDayOfMonth($finalarray['CHEQUE'], 'VoucherDate', 'CHEQUE', $newArray[$societyname]);
					
					
					
					// fromdate - $to_date = total month
					// loop over month
					// e.g. jan - 20 data present neft/cash/paytm
					// 
					// [jan-20]['neft'] = final['neft'][][]
					// 
//					var_dump($finalarray);
	//				$newArray[$societyname] = $finalarray;
					//for($i = 0;$i<$howeverManyMonths;$i++)
					//{
						
					//}
					
				}
			}
			//echo"<pre>";
			//print_r($newArray);
			//echo"</pre>";
			//mysqli_close($mMysqli);
			//die();		
			echo json_encode($newArray);	
			
			
		
			
			
		}
		catch(Exception $exp)
		{
			echo $exp;
		}
	}
	else
	{
		echo 'Missing Parameters';
	}
	
	
?>
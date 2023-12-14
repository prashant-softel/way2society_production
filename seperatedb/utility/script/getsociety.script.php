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
	if(isset($_REQUEST['clientId']))
	{
		//echo "call";
		//echo "date 1".$date=date_create($_REQUEST['postDate']);
		/*if($_REQUEST['postDate'] <> '')
		{
			$show_date = DateTime::createFromFormat('d-m-Y', $_REQUEST['postDate'])->format('Y-m-d');
		}*/
		//echo "call2";
		//echo"Date ". $pdate= date_format($_REQUES['postDate'],"Y-m-d");
		try
		{
			$hostname = DB_HOST;
			$username =DB_USER;
			$password = DB_PASSWORD;
			$dbroot = 'hostmjbt_societydb';
			$mMysqli = mysqli_connect($hostname, $username, $password, $dbroot);
			//$query = "SELECT society_name,dbname,Last_use_society_timestamp FROM `society` where client_id ='".$_REQUEST['clientId']."'";
			$data = getResult($mMysqli, "SELECT s.society_name,s.dbname,s.Last_use_society_timestamp,c.client_name FROM `society` as s join client as c on c.id=s.client_id where s.client_id ='".$_REQUEST['clientId']."' and s.status = 'Y'");		
			$final_Array = array();
			for($iCount = 0; $iCount < sizeof($data); $iCount++)
			{
				$billData = GetSocietyDetails($data[$iCount]['dbname'], $data[$iCount]['society_id'], $data[$iCount]['society_name'], $data[$iCount]['Last_use_society_timestamp']);
				//print_r($billData);
				//array_push($final_Array, array("society_name"=>$data[$iCount]['society_name'],"BillDate"=>$billData[0]['BillDate'], "LastBillGenerate"=>$billData[0]['LastBillDate']));
				array_push($final_Array, array("society_name"=>$data[$iCount]['society_name'],"clientName"=>$data[$iCount]['client_name'],"BillDate"=>$billData[0]['BillDate'], "ForPeriod"=>$billData[0]['Type'],"Total_mamber"=>$billData[0]['totalUnit']));
				
			}
			//echo "<pre>";
			//print_r($final_Array);
			//echo "</pre>";
			
			
			mysqli_close($mMysqli);		
			echo json_encode($final_Array);	
			
			
		
			
			
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
	function GetSocietyDetails($dbName,$societyId,$societyName,$LastTime)
	{
		$mMysqli1 = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, $dbName);
		if(!$mMysqli1)
		{
			//echo '<br/>Connection Failed';
		}
		else
		{
			//echo '<br/>Connected';
			/*if($show_date <> '')
			{
				//echo "in if";
				$data = getResult($mMysqli1, "SELECT billDate, MAX(Timestamp) as LastBillDate FROM `billregister` where Timestamp > '".$show_date."'");		
			}
			else
			{*/
				//echo "SELECT billDate, MAX(Timestamp) as LastBillDate FROM `billregister";
				//$data = getResult($mMysqli1, "SELECT billDate, MAX(Timestamp) as LastBillDate FROM `billregister`");
				//$data = getResult($mMysqli1, "SELECT br.PeriodID,p.Type, BillDate FROM `billregister` as br join period as p on p.ID = br.PeriodID ORDER BY br.PeriodID DESC LIMIT 1");	
				$data = getResult($mMysqli1, "SELECT br.PeriodID,p.Type, BillDate FROM `billregister` as br join period as p on p.ID = br.PeriodID ORDER BY br.PeriodID ASC LIMIT 1");	
				$data1 = getResult($mMysqli1, "SELECT COUNT(id) as totalUnit FROM `unit`");	
				$data[0]['totalUnit'] = $data1[0]['totalUnit'];
			//}
		}
		
		mysqli_close($mMysqli1);
		//echo '<br/>Connection Closed';
		return $data;
	}
	
?>
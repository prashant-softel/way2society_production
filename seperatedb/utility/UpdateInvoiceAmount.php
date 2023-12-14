<?php
	include('script/config_script.php');
	error_reporting(0);	
	if($_SERVER['HTTP_HOST']=="localhost")
	{		
		define("HOSTNAME", DB_HOST);
		define("USERNAME", DB_USER);
		define("PASSWORD", DB_PASSWORD); 			
	}
	else
	{		
		define("HOSTNAME", DB_HOST);
		define("USERNAME", DB_USER);
		define("PASSWORD", DB_PASSWORD); 			
	}
	
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
	
	try
	{				
		$mMysqli = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, "hostmjbt_societydb");
		if(!$mMysqli)
		{
			echo '<br/>Connection Failed';
		}
		else
		{
			echo '<br/>Connected';
			
			date_default_timezone_set('Asia/Kolkata');
			$data = getResult($mMysqli, "SELECT * FROM `society` WHERE `status` = 'Y' ");				
			
			for($iCount = 0; $iCount < sizeof($data); $iCount++)
			{
				echo '<br/><br/>Connecting DB : ' . $data[$iCount]['dbname'];				
				$mMysqli1 = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, $data[$iCount]['dbname']);
				
				if(!$mMysqli1)
				{
					echo '<br/>Connection Failed';
				}
				else
				{
					echo '<br/>Connected';	
					$data2 = getResult($mMysqli1, "SELECT * FROM `paymentdetails`");
					for($j = 0; $j < sizeof($data2); $j++)
					{											
						if($data2[$j]['ExpenseBy'] > 0 && $data2[$j]['InvoiceAmount'] == 0)
						{							
							$invoiceAmount = $data2[$j]['Amount'] + $data2[$j]['TDSAmount'];							
							$mMysqli1->query("UPDATE `paymentdetails` SET `InvoiceAmount`='".$invoiceAmount."' WHERE `id` = '".$data2[$j]['id']."'");							
						}						
					}					
				}				
			}
						
			mysqli_close($mMysqli);
			echo '<br/>Connection Closed';
		}
		
	}
	catch(Exception $exp)
	{
		echo $exp;
	}			
	
?>
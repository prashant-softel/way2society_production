<?php
	include('config_script.php');
	error_reporting(0);
	if(isset($_REQUEST['dbName']) && isset($_REQUEST['sVoucherID']) && isset($_REQUEST['eVoucherID']) && isset($_REQUEST['sVoucherNo']) &&
		$_REQUEST['dbName'] <> '' && $_REQUEST['sVoucherID'] <> '' && $_REQUEST['eVoucherID'] <> '' && $_REQUEST['sVoucherNo'] <> '')
	{
		try
		{
			$hostname = DB_HOST;
			$username =DB_USER;
			$password = DB_PASSWORD;
			$dbPrefix = 'hostmjbt_society';
	
			
			$startNo = (int)$_REQUEST['sVoucherID'];
			$endNo = (int)$_REQUEST['eVoucherID'];
			$sVoucherNo = (int)$_REQUEST['sVoucherNo']; 
			
			$dbName = $dbPrefix . $_REQUEST['dbName'];
			
			$body = '';
			
			echo '<br/><br/>Updating DB ' .$dbName. ' for voucher Number(s)';	
			
			echo '<br/><br/>Connecting DB : ' . $dbName;
			
				
				$mMysqli = mysqli_connect($hostname, $username, $password, $dbName);
				
			//	mysqli_autocommit($mMysqli,false);
				
				if(!$mMysqli)
				{
						echo '<br/>Connection Failed';	
				}
				else
				{
					
					echo '<br/>Connected';	
					
					$SelectVoucherDetailsQuery = "SELECT SrNo,id FROM voucher WHERE id between '".$startNo."' AND '".$endNo."'";
					
					$VoucherDetails = mysqli_query($mMysqli, $SelectVoucherDetailsQuery);
					$VoucherDetails = GetResult($VoucherDetails);	
					
					$IsFirstVoucher = true;
					for($i = 0 ; $i <  count($VoucherDetails); $i++)
					{
						if($VoucherDetails[$i]['SrNo'] == 1)
						{
							if($IsFirstVoucher == false)
							{
								$sVoucherNo++;	
								
							}
							else
							{
								$IsFirstVoucher = false;
							}
						}
						
						$UpdateVoucher_query = "UPDATE voucher set VoucherNo = '".$sVoucherNo."' WHERE id = '".$VoucherDetails[$i]['id']."'";
						
						$VoucherUpdate = mysqli_query($mMysqli, $UpdateVoucher_query);
						
					}
					
					
					if($VoucherUpdate)
					{
						echo "<br>Voucher Updated Successfully";
					}
				
					mysqli_close($mMysqli);
					$body .="<br/>Connection Closed";
				}
		
			
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
	
	function GetResult($result)
	{
		$count = 0;
		while($row = $result->fetch_array(MYSQL_ASSOC))
		{
			$data[$count] = $row;
			$count++;
		}
		return $data;
	}
?>
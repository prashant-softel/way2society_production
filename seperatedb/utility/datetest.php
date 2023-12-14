<?php	
	
	//date_default_timezone_set('Asia/Calcutta');
	
	$gmtTimezone = new DateTimeZone('GMT');

	$DueDate = "2016-10-01";
	$PaymentDate = "2016-10-31";

	$datetime1 = new DateTime($DueDate, $gmtTimezone);
	//$datetime1 = $datetime1->modify('-1 day');
	
	$datetime2 = new DateTime($PaymentDate, $gmtTimezone);
	$datetime2 = $datetime2->modify('+1 day');
	
	$interval = $datetime1->diff($datetime2);

	var_dump($interval);

	$diff_d = $interval->format('%d');
	
	echo '<br/>diff in days: ' . $diff_d;
	
	$diff_m = $interval->format('%m');
	echo '<br/>diff in months: ' . $diff_m;
	
	$diff_y = $interval->format('%y');
	echo '<br/>diff in years: ' . $diff_y;
	
	if($diff_y > 0)
	{
		$diff_m = ($diff_y * 12) + $diff_m;			
	}
	
	//if payment is made few days late but less than a month, charge one month interest
	if($diff_m <= 0)
	{
		if($diff_d > 0)
		{
			$diff_m = 1;
		}			
	}
	else if($diff_d > 0)
	{
		$diff_m =  $diff_m + 1;
	}

	echo '<br/><br/>Diff in Months <' . $diff_m . '>';
?>
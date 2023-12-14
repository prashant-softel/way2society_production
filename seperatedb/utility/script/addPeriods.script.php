<?php
include('../../../classes/dbconst.class.php');
include('../../../classes/include/dbop.class.php');
include('../../../classes/bill_period.class.php');
include('config_script.php');

	
	//error_reporting(0);	
	if(isset($_REQUEST['start']) && isset($_REQUEST['end']) && isset($_REQUEST['year']) && $_REQUEST['start'] <> '' && $_REQUEST['end'] <> '' && $_REQUEST['year'] <> '' )
	{
		try
		{
			$hostname = DB_HOST;
			$username =DB_USER;
			$password = DB_PASSWORD;
			//$dbPrefix = DB_DATABASE;
			$dbPrefix = "hostmjbt_society";
		
			
			$startNo = (int)$_REQUEST['start'];
			$endNo = (int)$_REQUEST['end'];
			$year = $_REQUEST['year'];
			$YearID = 0; 
			
			echo '<br/><br/>Updating DB ' . $dbPrefix . $startNo . ' to ' . $dbPrefix . $endNo;
			
			for($iCount = $startNo; $iCount <= $endNo; $iCount++)
			{
				//echo "<br>dbname1:".$dbPrefix;
				//echo "<br>cnt:".$iCount;
				
				$dbName = $dbPrefix . $iCount;
				//echo "<br>dbname2:".$dbName;
				$mMysqli = new dbop(false,$dbName);
				
				echo '<br/>Connecting DB : ' . $dbName;
				
				if($mMysqli->isConnected == false)
				{
					echo '<br/>Connection Failed';
					echo '<br/>Connection Error : '.$mMysqli->connErrorMsg;
					echo '<hr>';
				}
				else
				{
					echo '<br/>Connected';
					
					//die();
					$obj_billperiod = new bill_period($mMysqli);
					
					
					$data = $mMysqli->select("SELECT `bill_cycle` FROM `society` WHERE `status` = 'Y' " );
					if($data[0]['bill_cycle'] <> "" && $data[0]['bill_cycle'] <> 0)
					{	
						$billing_cycle = $data[0]['bill_cycle'];
						echo "<br>billing cycle : ".$billing_cycle;
						
						$getYear = $mMysqli->select("SELECT `YearID` FROM `year` where `YearDescription`='".$year."' " );
						
						if($getYear[0]['YearID'] <> "")
						{
							$YearID = $getYear[0]['YearID']; 
							echo "<br>YearID : ".$YearID;
							
							if($billing_cycle <>'' && $YearID<>'')
							{									
								$res = $mMysqli->select("select count(YearID) as count from `period` where `Billing_cycle`='".$billing_cycle."' and `YearID`= '".$YearID."'");
													
								if($res[0]['count'] == 0)
								{ 
									$months = getMonths($billing_cycle);
									print_r($months);
									$obj_billperiod->setPeriod($months ,$billing_cycle,$YearID);																																				
									echo  "<br>Periods Inserted Successfully";
								}
								else
								{
									echo  "<br>Periods Already Exists For Given Year ";		
								}												
							}
							else
							{
								echo  "<br>Billing Cycle Or YearID Is Empty For Society";			
							}		
						}
						else
						{
							echo  "<br>Year Not Exist In Year Table For Society";
							//selecting last yearID entry in year table
							$getPrevYear = $mMysqli->select("SELECT max(YearID) As PrevYearID FROM `year`");
							//By default Year is not freeze if you want then you have to updte it from UI
							$IsfreezeYear = 0;
							//month representation according to billing cycle whether it's monthly or quaterly
							$months = getMonths($billing_cycle);
							//Month name 
							$MonthName = 'April-May-June-July-August-September-October-November-December-January-February-March';
							//Getting Begin Data and End Date
							$begin_date = $obj_billperiod->getBeginDate($MonthName,$year);
							$end_date = $obj_billperiod->getEndDate($MonthName,$year);
						        // Finally inserting Year in Year Table;
						        $sql="insert into year (`YearDescription`,`PrevYearID`,`BeginingDate`,`EndingDate`,`is_year_freeze`) values ('".$year."','".$getPrevYear[0]['PrevYearID']."','".$begin_date."','".$end_date."','".$IsfreezeYear."' )";
							$YearID = $mMysqli->insert($sql);
							//Inserting Period
							$obj_billperiod->setPeriod($months ,$billing_cycle,$YearID);
				
							if($YearID <> 0)
							{
								echo  "<br>Adding Year And Period";
								echo  "<br>Year And Periods Inserted Successfully";	
							}
						}
					}
					else
					{
						echo  "<br>Billing Cycle Not Set For Society";			
					}
					echo '<br/>Connection Closed';
					echo '<hr>';
					//$mMysqli->close();
				}
			}
		}
		catch(Exception $exp)
		{
			echo $exp;
		}
	}
	else
	{
		echo '<br>Missing Parameters';
	}
	
	
	
?>
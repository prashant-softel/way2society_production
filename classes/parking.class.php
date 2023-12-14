<?php
include_once("include/display_table.class.php");
/*echo "<pre>";
print_r($_SESSION);
echo "</pre>";
*/
?>
<html>
<head>
	
	<script type="text/javascript">
    	function UpdateBillMaster(param)
		{
			document.getElementById("unitNo").value = param['Unit_No'];
			document.getElementById("unitId").value = param["Unit_Id"];
			document.getElementById("ledgerName").value = param["ledger_name"];
			document.getElementById("ledgerId").value = param["ledger_id"];
			document.getElementById("ParkingAmt").value = param["ParkingRateAmt"];
			document.getElementById("cPeriod").checked = "true";
		}
		function UpdateBillAmount()
		{
			var unitId = document.getElementById("unitId").value;
			var unitNo = document.getElementById("unitNo").value;
			var ledgerName = document.getElementById("ledgerName").value;
			var ledgerId = document.getElementById("ledgerId").value;
			var ParkingAmt = document.getElementById("ParkingAmt").value;
			var Period = document.getElementByName("BillPeriod").value;
			var BillFor = "0";
			alert (Period);
			if(Period == "cPeriod")
			{
				displayPeriods();
				BillFor = document.getElementById("BillFor").value;
			}
			alert(BillFor);
			var details = Array();
			details['UnitId'] = unitId;
			details['Head'] = ledgerId;
			details['Amt'] = ParkingAmt;
			$.ajax({
				url: 'ajax/ajaxTask.php',
        		type: 'POST',
        		data: {"details":JSON.stringify(details)},
        		success: function(data)
        		{
        			//alert(data);
        			data = data.trim();
        			if(data > 0)
        			{	
        				alert("Task updated successfully");
        				window.location.reload();
        			}
            	}
			})
		}	
		function displayPeriods(str)
		{
			if(str == "C")
			{
				document.getElementById("BillForTr").style.display = "table-row";
			}
			else
			{
				alert ("in else");
				document.getElementById("BillForTr").style.display = "none";
			}
		}
		function checkAll(str)
		{
			//alert ("checkAll");
			var i = 0;
			var checkBox = document.getElementById("chkAll");
			if(checkBox.checked)
			{
				//alert ("in if");
				for(i = 0;i < str;i++)
				{
					if(document.getElementById("chk"+i))
					{
						document.getElementById("chk"+i).checked = true;
					}
				}
			}
			else
			{
				for(i = 0;i< str;i++)
				{
					if(document.getElementById("chk"+i))
					{
						document.getElementById("chk"+i).checked = false;
					}
				}
			}
		}
    </script>

<?php
class Parking
{
	public $m_dbConn;
	public $display_pg;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg = new display_table($this->m_dbConn);
	
	}
	

	
	public function MemberParkingListings($bBillWise = false)
	{
		$finalArray = array();
		$flag = 0;

			//print_r($new_parking);
			//die();
		
		if(!$bBillWise)
		{
			//$sqlCars = "SELECT mem.member_id, unit.unit_no, car.car_owner as vehicle_owner,car.parking_slot as parking_slot, car.parking_sticker as sticker, car.car_reg_no as reg_no,car.car_make as make,car.car_model as model,car.car_color as color,mem.owner_name as type,CONCAT(unit.unit_no, '-',mem.owner_name) as owner_name FROM `mem_car_parking` as car Join `member_main`as mem on mem.member_id = car.member_id Join `unit` on mem.unit = unit.unit_id where mem.ownership_status=1 order by unit.sort_order asc";
			$sqlCars ="SELECT mem.member_id, unit.unit_no, car.car_owner as vehicle_owner,car.parking_slot as parking_slot, car.parking_sticker as sticker, car.car_reg_no as reg_no,car.car_make as make,car.car_model as model,car.car_color as color,mem.owner_name as type FROM `mem_car_parking` as car Join `member_main`as mem on mem.member_id = car.member_id Join `unit` on mem.unit = unit.unit_id where mem.ownership_status=1 and car.status='Y' order by unit.sort_order asc";
			$resultCars = $this->m_dbConn->select($sqlCars);
			
			
		//	$sqlBike =  "SELECT mem.member_id, unit.unit_no, bk.bike_owner as vehicle_owner,bk.parking_slot as parking_slot, bk.parking_sticker as sticker, bk.bike_reg_no as reg_no,bk.bike_make as make,bk.bike_model as model,bk.bike_color as color,mem.owner_name as type,CONCAT(unit.unit_no, '-',mem.owner_name) as owner_name FROM `mem_bike_parking` as bk Join `member_main`as mem on mem.member_id = bk.member_id Join `unit` on mem.unit = unit.unit_id where mem.ownership_status=1 order by unit.sort_order asc";
				$sqlBike =  "SELECT mem.member_id, unit.unit_no, bk.bike_owner as vehicle_owner,bk.parking_slot as parking_slot, bk.parking_sticker as sticker, bk.bike_reg_no as reg_no,bk.bike_make as make,bk.bike_model as model,bk.bike_color as color,mem.owner_name as type FROM `mem_bike_parking` as bk Join `member_main`as mem on mem.member_id = bk.member_id Join `unit` on mem.unit = unit.unit_id where mem.ownership_status=1 and bk.status='Y' order by unit.sort_order asc";
			$resultBike = $this->m_dbConn->select($sqlBike);
			
			if(sizeof($resultCars) > 0)
			{
				for($cars = 0;$cars <= sizeof($resultCars)-1;$cars ++ )
				{
					$resultCars[$cars]["unit_no"] ="<a href='view_member_profile.php?id=".$resultCars[$cars]['member_id']."'>".$resultCars[$cars]['unit_no']."</a>";
					$resultCars[$cars]["type"] = 'Car';//'<i class="fa fa-car fa-lg" aria-hidden="true"></i>';
					array_push($finalArray,$resultCars[$cars]);
				}
			}
			if(sizeof($resultBike) > 0)
			{
				for($bikes = 0;$bikes <= sizeof($resultBike)-1;$bikes ++ )
				{
					$resultBike[$bikes]["unit_no"]="<a href='view_member_profile.php?id=".$resultBike[$bikes]['member_id']."'>".$resultBike[$bikes]['unit_no']."</a>";
					$resultBike[$bikes]["type"] = 'Bike';//<i class="fa fa-bicycle fa-lg" aria-hidden="true"></i>';
					array_push($finalArray,$resultBike[$bikes]);
				}
			}
			$thheader = array("Unit No", "Vehicle Owner Name", "Slot Number", "Sticker Number", "Registration Number", "Make", "Model", "Color", "Vehicle Type");
		}
		else
		{
			$sqlBike = "SELECT count(*) as bikes,bike.member_id,unit.unit_no,unit.unit_id,mm.primary_owner_name FROM `mem_bike_parking` as bike join `member_main` as mm on mm.member_id = bike.member_id join unit on unit.unit_id = mm.unit where bike.status = 'Y' group by bike.member_id limit 0,1000";

			$sqlCars = "SELECT count(*) as cars, car.member_id,unit.unit_no,unit.unit_id,mm.primary_owner_name FROM `mem_car_parking` as car join `member_main` as mm on mm.member_id = car.member_id join unit on unit.unit_id = mm.unit where car.status = 'Y' group by car.member_id limit 0,1000";
			//echo $sqlBike; 
			$resultBike = $this->m_dbConn->select($sqlBike);
			$resultCars = $this->m_dbConn->select($sqlCars);
			$new_parking = array_replace_recursive($resultBike, $resultCars);
			for($i = 0; $i < sizeof($resultBike); $i++)
			{
				$mem_id = $resultBike[$i]["member_id"];
				$b_Found = 0;
				for($j = 0; $j < sizeof($resultCars); $j++)
				{
					if($resultCars[$j]["member_id"] == $mem_id)
					{
						$resultCars[$j]["bikes"] = $resultBike[$i]["bikes"];
						$b_Found = 1;
					}
				}
				if($b_Found == 0)
				{
					array_push($resultCars,$resultBike[$i]);
				}
			}
			
			for($i = 0; $i < sizeof($resultCars); $i++)
			{
				if(!isset($resultCars[$i]["cars"]))
				{
					$resultCars[$i]["cars"] = "0";
					//array_push($resultCars[$i],$resultCars[$i]["cars"]);
				}
			}
			$new_vehicles = array();
			for($new = 0; $new < sizeof($resultCars); $new++) 
			{
				//echo "<br>cnt:". $new . " unit: " .$new_parking[$new]["unit_no"];
				$UnitNo = "-";
				$CarOwner = "-";
				$Bikes = "0";
				$Cars = "0";
				$pType = "0";
				$bike_owner = "-";
				$owner_name = "-";
				if(isset($resultCars[$new]["unit_no"]))
				{
					$UnitNo = $resultCars[$new]["unit_no"];
				}
				if(isset($resultCars[$new]["car_owner"]))
				{
					$CarOwner = $resultCars[$new]["car_owner"];
				}
				if(isset($resultCars[$new]["bikes"]))
				{
					$Bikes = $resultCars[$new]["bikes"];
				}
				if(isset($resultCars[$new]["cars"]))
				{
					$Cars = $resultCars[$new]["cars"];
				}
				if(isset($resultCars[$new]["primary_owner_name"]))
				{
					$owner_name = $resultCars[$new]["primary_owner_name"];
				}
				if(isset($resultCars[$new]["bike_owner"]))
				{
					$bike_owner = $resultCars[$new]["bike_owner"];
				}
				if(isset($resultCars[$new]['ParkingType']))
				{
					$pType = $resultCars[$new]["ParkingType"];
				}
				if(isset($resultCars[$new]["unit_id"]))
				{
					$UnitId = $resultCars[$new]["unit_id"];
				}
				$new_vehicles[$new]["member_id"] = $resultCars[$new]["member_id"];
				$new_vehicles[$new]["unit_no"] = $UnitNo; 
				$new_vehicles[$new]["primary_owner_name"] = '<a href="view_member_profile.php?scm&id='.$resultCars[$new]["member_id"].'&tik_id='.time().'&m&view" >'.$owner_name.'</a>'; 
				$new_vehicles[$new]["bikes"] = $Bikes; 
				$new_vehicles[$new]["cars"] = $Cars; 
				$new_vehicles[$new]['ParkingType'] = $pType;
				$new_vehicles[$new]["unit_id"] = $UnitId; 
				array_push($finalArray, $new_vehicles[$new]);
			}
			$sqlForLedgerName = "select DISTINCT l.`ledger_name` from `parking_type` as pt , `ledger` as l where pt.`LinkedToLedgerId` = l.`id` and pt.`Status` = 'Y'";
			$LedgerName = $this->m_dbConn->select($sqlForLedgerName);
			$sqlForParkingType = "select DISTINCT `ParkingType`, `Rate` from `parking_type` where Status = 'Y'";
			$parkingType = $this->m_dbConn->select($sqlForParkingType);
			$pTypeHeader = array();
			$pRate = array();
			$thheader = array("<input type='checkbox' name='chkAll' id='chkAll' value='0' onChange='checkAll(".sizeof($finalArray).")'><br>Select All","Unit","Owner Name","Total Bikes","Total Cars");
			for($i=0;$i<sizeof($parkingType);$i++)
			{
				$addCol = $parkingType[$i]['ParkingType']."<br>(Rs.".$parkingType[$i]['Rate']. ")";
				array_push($thheader, $addCol);
				$addCol = "";
				array_push($pTypeHeader, $parkingType[$i]['ParkingType']);
				array_push($pRate, $parkingType[$i]['Rate']);
			}
			for($i=0;$i<sizeof($LedgerName);$i++)
			{
				array_push($thheader, $LedgerName[$i]['ledger_name']);
			}
			$flag = 1;
			//echo sizeof($thheader);
		}
		if($flag == "1")
		{
			$data = $this->display($finalArray,$thheader,$pTypeHeader);
		}
		else
		{
			$data = $this->displayDatatable($finalArray,$thheader);
		}	
		return $data;
	}
	public function display($res,$thheader,$pTypeHeader)
	{
		$delParkingType = $this->getDeletedParkingType();
		//print_r($delParkingType);
		if(sizeof($delParkingType) == 0)
		{
		}
		else
		{
			$display = "<font color='red'><b>Disabled Parking Type : ";
			for($i = 0;$i < sizeof($delParkingType);$i++)
			{
				$display .= ($i+1)."] ".$delParkingType[$i];
			}
			$display .= "</b></font>"; 
		}
		$colorArray = array();
		$pType = "";
		$lName = "";
		$color = "";
		$q=0;
		$details = array();
		$rate = 0;
		$j = 0;
		$k = 0;
		$cArray = array('Red','Fuchsia','Green','Yellow','Aqua');
		$i = 0;
		$p = 0;
		$result = array();
		for($j = 4 + sizeof ( $pTypeHeader ); $j < sizeof ( $thheader ); $j++)
		{
			$sqlQuery = "SELECT pt.`ParkingType`,l.`ledger_name` FROM `parking_type` as pt, `ledger` as l where l.`id` = pt.`LinkedToLedgerId` and pt.`status` = 'Y' and l.`ledger_name` = '".$thheader[$j]."'";
			$result = $this->m_dbConn->select($sqlQuery);
			for($k = 0; $k < sizeof($result); $k++)
			{
				$colorArray[$i]['ParkingType'] = $result[$k]['ParkingType'];
				$colorArray[$i]['ledger_name'] = $result[$k]['ledger_name'];
				$colorArray[$i]['color'] = $cArray[$p];
				$i = $i + 1;
			}
			$p = $p + 1;
		}
		?>
		<table class="display datatable" cellspacing="0" style="width:100%">
        	<?php echo $display;?>
        	<thead>
            	<tr  height="30" bgcolor="#FFFFFF">
                	<?php
					$j = 0;
					$len = 4 + sizeof($pTypeHeader);
					for($i=0;$i<sizeof($thheader);$i++)
					{
						
						if($i >= 5 && $i <= $len)
						{
					?>
                   			<th style="text-align:center" class="tooltip2"><?php echo $thheader[$i]?>
                        		<span class="tooltiptext">
                                	<?php echo $this->getParkingDetails($pTypeHeader[$j])?>
                            	</span>
                        	</th>
               		<?php
							$j = $j + 1;
						}
						else
						{
					?>
                    		<th style="text-align:center"><?php echo $thheader[$i]?></th>
                    <?php
						}
					}
					?>
            	</tr>
            </thead>
            <tbody>
   		<?php
		if($res<>"")
		{
			for($i = 0; $i < sizeof($res); $i++)
			{
				?>
            	<tr height="25" bgcolor="#BDD8F4" align="center">
					<td id ="td<?php echo $i?>"><input type="checkbox" name="checkbox" id = "chk<?php echo $i ?>" value = "<?php echo $res[$i]['unit_id'];?>" style = "display:none"/></td>
                	<td align="center"><?php echo $res[$i]['unit_no'] ?></td>
                    <td align="center"><?php echo $res[$i]['primary_owner_name'] ?></td>
                    <td align="center"><?php echo $res[$i]['bikes'] ?></td>
                    <td align="center"><?php echo $res[$i]['cars'] ?></td>
                    <?php
					for($j = 0; $j < sizeof($pTypeHeader); $j++)
					{
						$sql = "select mbp.`mem_bike_parking_id`,pt.Rate,l.`ledger_name` from `ledger` as l,`mem_bike_parking` as mbp,`parking_type` as pt where mbp.`member_id` = '".$res[$i]['member_id']."' and mbp.`ParkingType` = pt.`Id` and l.`id` = pt.`LinkedToLedgerId` and pt.`ParkingType` = '".$pTypeHeader[$j]."' union select mbp.`mem_car_parking_id`,pt.Rate,l.`ledger_name` from `ledger` as l,`mem_car_parking` as mbp,`parking_type` as pt where mbp.`member_id` = '".$res[$i]['member_id']."' and mbp.`ParkingType` = pt.`Id` and l.`id` = pt.`LinkedToLedgerId` and pt.`ParkingType` = '".$pTypeHeader[$j]."'";
						$details = $this->m_dbConn->select($sql);
						//echo "<pre>";
						//print_r($colorArray);
						//echo "</pre>";
						//echo "<pre>";
						//print_r($details);
						//echo "</pre>";
						for($p = 0; $p < sizeof($colorArray); $p++)
						{
							if($pTypeHeader[$j] == $colorArray[$p]['ParkingType'])
							{
							?>
                    			<td align="center"><b><font color="<?php echo $colorArray[$p]['color'];?>" ><?php echo sizeof($details);?></font></b></td>
							<?php
							}
						}
					}
					for($j = 4 + sizeof ( $pTypeHeader ); $j < sizeof ( $thheader ); $j++ )
					{ 
						$carRate = 0;
						$bikeRate = 0;
						$sqlCar = "select pt.`Rate` from `mem_car_parking` mcp,`ledger` l,`parking_type` pt where mcp.`member_id` = '".$res[$i]['member_id']."' and pt.`LinkedToLedgerId` = l.`id` and l.`ledger_name` = '".$thheader[$j]."' and mcp.`ParkingType` = pt.`Id`";
						$resultCar = $this->m_dbConn->select($sqlCar);
						for($c = 0; $c < sizeof ( $resultCar ) ; $c++)
						{
							$carRate +=$resultCar[$c]['Rate'];
						}
						$sqlBike = "select pt.`Rate` from `mem_bike_parking` mbp,`ledger` l,`parking_type` pt where mbp.`member_id` = '".$res[$i]['member_id']."' and pt.`LinkedToLedgerId` = l.`id` and l.`ledger_name` = '".$thheader[$j]."' and mbp.`ParkingType` = pt.`Id`";
						$resultBike = $this->m_dbConn->select($sqlBike);
						for($b = 0; $b < sizeof ( $resultBike ) ; $b++)
						{
							$bikeRate +=$resultBike[$b]['Rate'];
						}
						for($p = 0; $p < sizeof($colorArray); $p++)
						{
							$sqlLedgerCharges = "";
							if($thheader[$j] == $colorArray[$p]['ledger_name'])
							{
							?>
                    			<td align="center"><b><font color = "<?php echo $colorArray[$p]['color'];?>"><?php echo ($carRate + $bikeRate);?></font></b>
                                <?php 
									
									$BillMasterDetails = $this->getBillMasterDetails($colorArray[$p]['ledger_name'],$res[$i]['unit_id']);
									$totalRate = (int)$carRate+$bikeRate;
									$arrayParkingDetails = $this->getBillMasterDetailsForDialog($colorArray[$p]['ledger_name'],$res[$i]['unit_id'],$totalRate);
									$BillMasterDetails = (int) $BillMasterDetails;
									if($BillMasterDetails == $totalRate)
									{
									?>
                                    	</td>
                                    <?php
									}
									else
									{
									//Model Code............
									?>
 										 <input type="hidden" id="<?php echo $res[$i]['unit_id']?>" value="<?php echo $res[$i]['unit_id'].",".$totalRate.",".$colorArray[$p]['ledger_name'];?>"/>                                   
                                    	 <script>
										 	document.getElementById("chk<?php echo $i;?>").style.display = "block";
										 </script>
                                    <b>(<?php echo $BillMasterDetails;?>)</b></td>
                                    <?php
									}
									?>
                    			<?php
								break;
							}
						}
					}					
					?>
                </tr>    
            <?php
			}
		}
		else
		{
			?>
            <td align = "center" colspan="<?php echo sizeof($thheader)?>">No records found</td>
            <?php	
		}
		?>
        </tbody>
       </table>
      <?php
	}
	public function displayDatatable($rsas,$thheader,$map)
	{
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= $map;
		
		$res = $this->display_pg->display_datatable($rsas, false, false);
		return $res;
	}
	public function getParkingDetails($pType)
	{
		//echo "pType:".$pType;
		$sql = "Select * from `ledger` as l,`parking_type` as pt where pt.`ParkingType` = '".$pType."' and l.`id` = pt.`LinkedToLedgerId`";
		$result = $this->m_dbConn->select($sql);
		//echo "<pre>";
		//print_r($result);
		//echo "</pre>";
		if($result[0]['Description'] == "")
		{
			$content = "Parking Type : ".$pType."<br>Rate : Rs. ".$result[0]['Rate']."<br>Ledger Name : ".$result[0]['ledger_name']." ";
		}
		else
		{
			$content = "Parking Type : ".$pType."<br>Rate : Rs. ".$result[0]['Rate']."<br>Ledger Name : ".$result[0]['ledger_name']."<br>Description : ".$result[0]['Description']." ";
		}
		return $content;
	}
	public function getDeletedParkingType()
	{
		$finalRes = array();
		$j = 0;
		$sql = "Select `Id`,`ParkingType` from `parking_type` where Status = 'N'";
		$result = $this->m_dbConn->select($sql);
		//print_r($result);
		for($i = 0;$i < sizeof($result); $i++)
		{
			$sqlM = "Select `member_id` from `mem_car_parking` where `ParkingType` = ".$result[$i]['Id']." union Select `member_id` from `mem_bike_parking` where `ParkingType` = ".$result[$i]['Id'];
			$res = $this->m_dbConn->select($sqlM);
			if($res == "")
			{
			}
			else
			{
				array_push($finalRes,$result[$i]['ParkingType']);
				$j = $j + 1;
			}
		}
		return $finalRes;
	}
	public function getBillMasterDetails($ledgerName, $UnitId)
	{	
		$sqlForBillMaster = "select sum(b.`AccountHeadAmount`) as TotalBill from unitbillmaster as b, ledger as l,unit as u where l.`ledger_name` = '".$ledgerName."' and b.`AccountHeadID` = l.`id` and b.`UnitID` = u.`unit_id` and u.`unit_id` = '".$UnitId."'";
		$BillMasterResult = $this->m_dbConn->select($sqlForBillMaster);
		return ($BillMasterResult[0]['TotalBill']);
	}
	public function getBillMasterDetailsForDialog($ledgerName, $UnitId, $ParkingRate)
	{	
		$sqlForBillMaster = "select u.`unit_id`,l.`id` as LedgerId,b.`AccountHeadAmount` as BillMasterAmt from unitbillmaster as b, ledger as l,unit as u where l.`ledger_name` = '".$ledgerName."' and b.`AccountHeadID` = l.`id` and b.`UnitID` = u.`unit_id` and u.`unit_id` = '".$UnitId."'";
		$BillMasterResult = $this->m_dbConn->select($sqlForBillMaster);
		$arrayResult = array("ledger_id" => $BillMasterResult[0]['LedgerId'],"ParkingRateAmt"=> $ParkingRate, "Unit_No"=> $UnitNo, "Unit_Id" => $BillMasterResult[0]['unit_id'],"ledger_name" => $ledgerName, "BillMasterAmt" => $BillMasterResult[0]['BillMasterAmt']);
		/*echo "<pre>";
		print_r($arrayResult);
		echo "</pre>";*/
		return $arrayResult;
	}
	public function getYears()
	{
		$sqlForYear = "Select YearID, YearDescription from year where status = 'Y'";
		$res = $this->m_dbConn->select($sqlForYear);
		return($res);
	}
	
}
?>
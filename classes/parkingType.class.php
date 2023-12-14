 <?php
include_once("dbconst.class.php");
include_once("include/display_table.class.php");
include_once("utility.class.php");
class parkingType extends dbop
{
	public $m_dbConn;
	public $m_dbConnRoot;
	public $actionPage = "../viewParkingType.php";
	public $obj_utility;
	function __construct($dbConn, $dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->obj_utility = new utility($dbConn,$dbConnRoot);
		//dbop::__construct();
	}
	//Used to get society selection box
	public function ComboboxForLedger($query,$id)
	{
		//$str.="<option value=''>All</option>";
		$str.="<option value='0'>Please Select</option>";
		$data = $this->m_dbConn->select($query);
		//print_r($data);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($id==$v)
						{
							$sel = 'selected';	
						}
						else
						{
							$sel = '';
						}
				
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
					}
					else
					{
						$str.=$v."</OPTION>";
					}
					$i++;
				}
			}	
		}
		return $str;
	}
	public function display($res)
	{
		?>
        <table id="example" class="display" cellspacing="0">
   		<?php
		if($res<>"")
		{
			?>
            <thead>
            	<tr  height="30" bgcolor="#FFFFFF">
                	<?php if($this->obj_utility->checkAccess()=="0")
					{
					?>
            			<th style="text-align:center">Edit</th>
            			<th style="text-align:center">Delete</th>
                    <?php
					}
					?>
                	<th style="text-align:center">Parking Type</th>
            		<th style="text-align:center">Description</th>
            		<th style="text-align:center">Rate</th>
                    <th style="text-align:center">Visibility</th>
                    <th style="text-align:center">Ledger Name</th>
                    
            	</tr>
            </thead>
            <tbody>
            <?php
				for($i=0;$i<sizeof($res);$i++)
				{
			 ?>		
             		<tr height="25" bgcolor="#BDD8F4" align="center"> 
                    	<?php
						if($this->obj_utility->checkAccess()=="0")
						{
						?>
                    		<td><a href="addParkingType.php?method=edit&Id=<?php echo $res[$i]['Id'];?>"><img src="images/edit.gif"  /></a></td>
                    		<td><a onclick="deleteParkingType(<?php echo $res[$i]['Id']?>)"><img src="images/del.gif" /></a></td>
                       	<?php
						}
						?>
                		<td align="center"><?php echo $res[$i]['ParkingType'];?></td>
                		<td align="center"><?php echo $res[$i]['Description'];?> </td>
                		<td align="center"><?php echo $res[$i]['Rate'];?> </td>
                        <?php
						if($res[$i]['IsVisible'] == "1")
						{
						?>
                        	<td align="center">Yes</td>
                        <?php
						}
						else
						{
						?>
                        	<td align="center">No</td>
                         <?php
						}
						?>
                        <td align="center"><?php echo $res[$i]['ledger_name'];?></td>
                    </tr>
      				<?php
					}      
					?>
               	</tbody>
         <?php
		}
		else
		{
		?>
        	<thead>
            	<tr  height="30" bgcolor="#FFFFFF">
                	<?php if($this->obj_utility->checkAccess()=="0")
					{
					?>
            			<th style="text-align:center">Edit</th>
            			<th style="text-align:center">Delete</th>
                    <?php
					}
					?>
                	<th style="text-align:center">Parking Type</th>
            		<th style="text-align:center">Description</th>
            		<th style="text-align:center">Rate</th>
                    <th style="text-align:center">Visibility</th>
                    <th style="text-align:center">Ledger Name</th>
            	</tr>
            </thead>
        	<tbody>
            	<tr>
                <?php if($this->obj_utility->checkAccess()=="0")
				{
				?>	
                    <td></td>
                    <td></td>
                    <td></td>
            		<td style="text-align:center"><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
                    <td></td>
                    <td></td>
                    <td></td>
                 <?php
				}
				else
				{
				?>
                    <td></td>
                    <td></td>
            		<td style="text-align:center"><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
                    <td></td>
                    <td></td>
                 <?php
				}
				?>
            	</tr>
            </tbody>
       	<?php	
		}
		?>
        </table>
       	<?php
	}
	public function pgnation()
	{
		$query = "SELECT pt.`Id`, pt.`ParkingType`, pt.`Description`, pt.`Rate`, pt.`IsVisible`, l.`ledger_name` FROM `parking_type` pt, `ledger` l where l.`id` = pt.`LinkedToLedgerID` AND pt. `Status` = 'Y'";
		$res = $this->m_dbConn->select($query);
		$data = $this->display($res);
		return $data;
	}
	public function getParkingDetails($Id)
	{
		$res = "";
		$sql =  "Select * from `parking_type` where `Id` = ".$Id;
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	public function deleteParkingDetails($Id)
	{
		$sql = "Update `parking_type` set Status = 'N' where `Id` = ".$Id;
		$res = $this->m_dbConn->update($sql);
		return $res;
	}
	public function getAllParkingDetails()
	{
		$finalCar = array();
		$finalBike = array();
		
		$sqlCar = "SELECT car.`mem_car_parking_id`as ParkingId, mem.member_id, unit.unit_no, car.car_owner as vehicle_owner,car.car_reg_no as reg_no,car.car_make as make,car.car_model as model,car.car_color as color, car.`ParkingType` FROM `mem_car_parking` as car Join `member_main`as mem on mem.member_id = car.member_id Join `unit` on mem.unit = unit.unit_id where mem.ownership_status=1 and car.status='Y'";
		$resCar = $this->m_dbConn->select($sqlCar);
		$sqlBike = "SELECT bk.mem_bike_parking_id as ParkingId,mem.member_id, unit.unit_no, bk.bike_owner as vehicle_owner,bk.bike_reg_no as reg_no,bk.bike_make as make,bk.bike_model as model,bk.bike_color as color, bk.`ParkingType` FROM `mem_bike_parking` as bk Join `member_main`as mem on mem.member_id = bk.member_id Join `unit` on mem.unit = unit.unit_id where mem.ownership_status=1 and bk.status='Y'";
		$resBike = $this->m_dbConn->select($sqlBike);
		for($i=0;$i<sizeof($resCar);$i++)
		{
			$finalCar[$i]['ParkingId'] = $resCar[$i]['ParkingId'];
			$finalCar[$i]['member_id'] = $resCar[$i]['member_id'];
			$finalCar[$i]['Unit'] = $resCar[$i]['unit_no'];
			$finalCar[$i]['vehicle_owner'] = $resCar[$i]['vehicle_owner'];
			$finalCar[$i]['RegNo'] = $resCar[$i]['reg_no'];
			$finalCar[$i]['Make'] = $resCar[$i]['make'];
			$finalCar[$i]['Model'] = $resCar[$i]['model'];
			$finalCar[$i]['ParkingType'] = $resCar[$i]['ParkingType'];
			$finalCar[$i]['Type'] = "Car";
		}
		for($i=0;$i<sizeof($resBike);$i++)
		{
			$finalBike[$i]['ParkingId'] = $resBike[$i]['ParkingId'];
			$finalBike[$i]['member_id'] = $resBike[$i]['member_id'];
			$finalBike[$i]['Unit'] = $resBike[$i]['unit_no'];
			$finalBike[$i]['vehicle_owner'] = $resBike[$i]['vehical_owner'];
			$finalBike[$i]['RegNo'] = $resBike[$i]['reg_no'];
			$finalBike[$i]['Make'] = $resBike[$i]['make'];
			$finalBike[$i]['Model'] = $resBike[$i]['model'];
			$finalBike[$i]['ParkingType'] = $resBike[$i]['ParkingType'];
			$finalBike[$i]['Type'] = "Bike";
		}
		/*echo "Car<br>";
		echo "<pre>";
		print_r($finalCar);
		echo "</pre>";
		echo "Bike<br>";
		echo "<pre>";
		print_r($finalBike);
		echo "</pre>";*/
		$final = array();
		$final = array_merge($finalCar,$finalBike);
		return $final;
	}
	public function startProcess()
	{
		$this->actionPage = "../updateParkingType.php";
		/*echo "<pre>";
		print_r($_POST);
		echo "</pre>";*/
		$count =  $_POST['Count'];
		$res = $this->getAllParkingDetails();
		for($i = 0;$i<$count;$i++)
		{
			if($_POST['parkingType'.$i] != $res[$i]['ParkingType'])
			{
				$id = $_POST['parkingId'.$i];
				$type = $_POST['type'.$i];
				if($type == "Car")
				{
					$table = "mem_car_parking";
				}
				else
				{
					$table = "mem_bike_parking";
				}
				$parkingType = $_POST['parkingType'.$i];
				$sql = "Update `".$table."` set ParkingType = '".$parkingType."' where `".$table."_id` = '".$id."'";
				//echo $sql."<br>";
				$this->m_dbConn->update($sql);			
			}
			else
			{
			}
		}
		return "Update";
	}
}
?>	
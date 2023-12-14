<?php

$AppPath = preg_replace("!${_SERVER['SCRIPT_NAME']}$!", '', $_SERVER['SCRIPT_FILENAME']);
include_once($AppPath.'/classes/include/dbop.class.php');
define('VALIDATE',0);
define('DELETE',1);

try
{
	$mMysqli = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, "hostmjbt_societydb");
	
	if(!$mMysqli)
	{
		echo "<br>Database Connction Failed";
	}
	else
	{
		$Society_id  = $_REQUEST['soc_id'];
		$RequestType = $_REQUEST['request_type'];
		echo "<br>Database Connected";
		
		$Units_Belongs_To_Society_Query = "SELECT * FROM `mapping` WHERE society_id = '".$Society_id."' AND role in ('Admin Member','Member') order by unit_id";
		$Units_Belongs_To_Society_Result = mysqli_query($mMysqli, $Units_Belongs_To_Society_Query);
		$Units_Belongs_To_Society = GetResult($Units_Belongs_To_Society_Result);
		
		$Data = filter_unit_data($Units_Belongs_To_Society);
		if($RequestType == DELETE)
		{
			
			$Delete_Mapping_Ids = trim(implode(',',array_column($Data, 'mapping_id')));
			
			if(!empty($Delete_Mapping_Ids))
			{
				$Delete_Extra_Mapping_Code_Query = "DELETE FROM `mapping` WHERE id in (".$Delete_Mapping_Ids.")";
				$result = mysqli_query($mMysqli,$Delete_Extra_Mapping_Code_Query);
				if($result)
				{
					echo "<br>Deleted Duplicate Successfully!!";
				}
			}
			else
			{
				echo "<br>Duplicate Code not found!!";
			}	
		}
		
		
		if($RequestType == VALIDATE)
		{
			$body .= "<html><head><style>body { font: normal medium/1.4 sans-serif;}table {border-collapse: collapse;width: 100%;}th, td {padding: 0.25rem;text-align: center;border: 1px solid #ccc;}tbody tr:nth-child(odd) {background: #eee;} label{color:black}</style></head>";
			$body .= "<div align='center' style='width:70%;font-weight: bold;'><div><label>Duplicate Mapping Codes</label></div>";
			$body .= "<table  align='center' style='border-collapse: collapse;border:1px solid black;bgcolor:gray;color:black'>";
			$body .= "<thead><tr>";
			$body .= "<th>Sr No.</th>";
			$body .= "<th>Unit No.</th>";
			$body .= "<th>Mapping Code</th>";
			$body .= "<th>Status</th>";
			$body .= "</tr></thead><tbody>";
			$counter = 1;	
			
			foreach($Data as $details)
			{
				$body .= "<tr>";
				$body .= "<td>".$counter."</td>";
				$body .= "<td>".$details['unit']."</td>";
				$body .= "<td>".$details['code']."</td>";
				$body .= "<td>".$details['status']."</td>";
				$body .= "</tr>";
				$counter++;
			}
			$body .= "</tbody>";
			echo "<center><button type='button' name='ExportToExcel' id='ExportToExcel' onClick='ExportToExcel();' style='font-size: 25px;'>Export To Excel</button><center><br>";
			echo $body;
			
		}
	}
}
catch(EXCEPTION $e)
{
	$e->getMessage();	
}


function filter_unit_data($Data)
{
	$Result           	= array();
	$unit_without_Ids 	= array();
	$mapping_Ids      	= array();
	$unit_with_login  	= array();
	$mapping_ids_array  = array_column($Data,'id'); 
	
	foreach($Data as $unit_details)
	{
		$cnt++;
		$unit_id = $unit_details['unit_id'];
		$login_id = $unit_details['login_id'];
		$mapping_id = $unit_details['id'];
		
		if($login_id == 0)
		{
			array_push($mapping_Ids,$mapping_id);
			array_push($unit_without_Ids,$unit_id);
		}
		else
		{
			array_push($unit_with_login,$unit_id);
		}
	}
	
	foreach($unit_without_Ids as $unit)
	{
		$index = -1;
		if(in_array($unit,$unit_with_login)) // check login exits for unit or not
		{
			$index = array_search($unit,$unit_without_Ids); // if exits store other code generated for this unit
			array_push($Result,$mapping_Ids[$index]);
			unset($mapping_Ids[$index]);
			unset($unit_without_Ids[$index]);
		}
		else
		{
			$all_unit_index = array_keys($unit_without_Ids,$unit); // if login not exits then check if it has more than 1 code 
			if(count($all_unit_index) > 1) // if yes 
			{
				for($i = 1; $i < count($all_unit_index); $i++) // then store the mapping ids of those codes to delete from table
				{
					$index = $all_unit_index[$i];
					array_push($Result,$mapping_Ids[$index]);
					unset($mapping_Ids[$index]);
					unset($unit_without_Ids[$index]);
				}
			}
		}
	}
	
	$final_result = array();
	$cnt = 0;

	foreach($Result as $mapping_id) // get each mapping id from loop
	{
		$index = -1;
		if(in_array($mapping_id, $mapping_ids_array))
		{
			$index = array_search($mapping_id, $mapping_ids_array);	
			if($index !== -1)
			{
				$final_result[$cnt]['mapping_id'] = $mapping_id;
				$final_result[$cnt]['code'] = $Data[$index]['code'];
				$final_result[$cnt]['unit'] = $Data[$index]['desc'];
				$final_result[$cnt]['status'] = ($Data[$index]['login_id'] == 0)? 'Inactive':'Active';				
				$cnt++;
			}
		}
	}
	
	return $final_result;	
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